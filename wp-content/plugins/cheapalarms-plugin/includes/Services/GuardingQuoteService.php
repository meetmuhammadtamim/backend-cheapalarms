<?php

namespace CheapAlarms\Plugin\Services;

use CheapAlarms\Plugin\Config\Config;
use CheapAlarms\Plugin\Support\AustralianPhone;
use CheapAlarms\Plugin\Support\EmailTemplateHtmlHelper;
use WP_Error;

use function __;
use function add_query_arg;
use function clean_user_cache;
use function current_time;
use function delete_transient;
use function delete_user_meta;
use function email_exists;
use function esc_attr;
use function esc_html;
use function esc_url;
use function get_option;
use function get_password_reset_key;
use function get_transient;
use function get_user_by;
use function gmdate;
use function is_email;
use function is_wp_error;
use function rawurlencode;
use function sanitize_email;
use function sanitize_text_field;
use function set_transient;
use function trailingslashit;
use function update_option;
use function wp_create_user;
use function wp_generate_password;
use function wp_update_user;
use function wp_strip_all_tags;

/**
 * ManPower / guarding quotes: GHL contact + estimate + customer portal (alarm/CCTV parity).
 * New customers receive a portal invite; admin "Send quote" notifies them to view it in the portal.
 */
class GuardingQuoteService
{
    private const OPTION_KEY = 'ca_guarding_quotes';
    private const OPTION_RATES = 'ca_guarding_rate_card';
    private const MAX_STORED = 500;
    private const LOCK_TTL = 90;
    private const MIN_HOURS = 4.0;
    private const MIN_PERM_MONTHS = 3;

    /** Ad hoc client charge-out bands (ex GST $/hr). */
    private const DEFAULT_ADHOC_RATES = [
        'weekdayDay'         => 65.0,
        'weekdayNight'       => 75.0,
        'fridayNight'        => 80.0,
        'saturday'           => 85.0,
        'saturdayIntoSunday' => 95.0,
        'sunday'             => 100.0,
        'publicHoliday'      => 130.0,
    ];

    /** Permanent ongoing bands (ex GST $/hr) — term discounts apply on top. */
    private const DEFAULT_PERMANENT_RATES = [
        'weekdayDay'         => 60.0,
        'weekdayNight'       => 70.0,
        'fridayNight'        => 75.0,
        'saturday'           => 80.0,
        'saturdayIntoSunday' => 90.0,
        'sunday'             => 95.0,
        'publicHoliday'      => 120.0,
    ];

    /** Legacy alias — same as ad hoc defaults for old single-card snapshots. */
    private const DEFAULT_RATES = self::DEFAULT_ADHOC_RATES;

    /** Percent off ex-GST subtotal by permanent contract length (months). */
    private const DEFAULT_TERM_DISCOUNTS = [
        3  => 0.0,
        6  => 5.0,
        12 => 10.0,
        24 => 15.0,
    ];

    private const DEFAULT_SHORT_NOTICE_PCT = 12.5;
    private const DEFAULT_SUPERVISOR_UPLIFT = 12.5;

    private const PH_DAYS_BY_STATE = [
        'NSW' => 11,
        'VIC' => 11,
        'QLD' => 11,
        'SA'  => 11,
        'WA'  => 11,
        'TAS' => 11,
        'ACT' => 11,
        'NT'  => 8,
    ];

    public function __construct(
        private GhlClient $ghlClient,
        private GhlSignalService $signalService,
        private PortalService $portalService,
        private EmailTemplateService $emailTemplateService,
        private Config $config,
        private Logger $logger
    ) {
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, mixed>|WP_Error
     */
    public function submit(array $body): array|WP_Error
    {
        if ($this->config->getGhlToken() === '' || $this->config->getLocationId() === '') {
            return new WP_Error(
                'ghl_not_configured',
                __('Quote submission is temporarily unavailable. Please call 1300 225 276.', 'cheapalarms'),
                ['status' => 503]
            );
        }

        $contactName = sanitize_text_field((string) ($body['contactName'] ?? $body['name'] ?? ''));
        $email       = sanitize_email((string) ($body['email'] ?? ''));
        $phoneRaw    = sanitize_text_field((string) ($body['phone'] ?? ''));

        if ($contactName === '' || $email === '' || !is_email($email) || $phoneRaw === '') {
            return new WP_Error(
                'missing_params',
                __('Please provide name, a valid email, and phone number.', 'cheapalarms'),
                ['status' => 400]
            );
        }

        $phone = AustralianPhone::toE164($phoneRaw);
        if ($phone === null) {
            return new WP_Error(
                'invalid_phone',
                __('Please enter a valid Australian phone number (e.g. 04XX XXX XXX).', 'cheapalarms'),
                ['status' => 400]
            );
        }

        if (empty($body['verified'])) {
            return new WP_Error(
                'not_verified',
                __('Please confirm your details before submitting.', 'cheapalarms'),
                ['status' => 400]
            );
        }

        $sites = $this->normalizeSites($body['sites'] ?? []);
        if ($sites === []) {
            return new WP_Error(
                'missing_sites',
                __('Add at least one site address.', 'cheapalarms'),
                ['status' => 400]
            );
        }

        $lockKey = 'ca_guarding_quote_lock_' . md5(strtolower($email) . '|' . $phone);
        if (get_transient($lockKey) !== false) {
            return new WP_Error(
                'duplicate_request',
                __('We already received your quote request. Our team will email you shortly.', 'cheapalarms'),
                ['status' => 429]
            );
        }
        set_transient($lockKey, time(), self::LOCK_TTL);

        $locationId = $this->config->getLocationId();
        [$firstName, $lastName] = $this->splitName($contactName);

        $contactId = $this->resolveOrCreateContact($firstName, $lastName, $email, $phone, $locationId);
        if (is_wp_error($contactId)) {
            delete_transient($lockKey);

            return $contactId;
        }

        $jobType = sanitize_text_field((string) ($body['jobType'] ?? 'adhoc'));
        if (!in_array($jobType, ['adhoc', 'permanent'], true)) {
            $jobType = 'adhoc';
        }

        $durationMonths = max(0, (int) ($body['durationMonths'] ?? 0));
        if ($jobType === 'permanent' && $durationMonths < self::MIN_PERM_MONTHS) {
            delete_transient($lockKey);

            return new WP_Error(
                'invalid_duration',
                sprintf(
                    /* translators: %d: minimum months */
                    __('Permanent contracts need at least %d months.', 'cheapalarms'),
                    self::MIN_PERM_MONTHS
                ),
                ['status' => 400]
            );
        }

        $schedMode = sanitize_text_field((string) ($body['schedMode'] ?? 'dates'));
        if (!in_array($schedMode, ['dates', 'weekly'], true)) {
            $schedMode = 'dates';
        }

        $defaultGuards = max(1, (int) ($body['defaultGuards'] ?? 1));
        $staffingMode  = sanitize_text_field((string) ($body['staffingMode'] ?? 'sites'));
        if (!in_array($staffingMode, ['sites', 'guards'], true)) {
            $staffingMode = 'sites';
        }
        $totalGuards = max(1, (int) ($body['totalGuards'] ?? $defaultGuards));
        if ($staffingMode === 'guards') {
            $sites = $this->applyTotalGuardsToSites($sites, $totalGuards);
            $defaultGuards = $totalGuards;
        }

        $start         = sanitize_text_field((string) ($body['start'] ?? ''));
        $end           = sanitize_text_field((string) ($body['end'] ?? ''));
        $shiftDate     = sanitize_text_field((string) ($body['shiftDate'] ?? ''));
        $days          = $this->normalizeDays($body['days'] ?? []);
        $phCover       = !empty($body['phCover']);
        $auState       = strtoupper(sanitize_text_field((string) ($body['auState'] ?? 'NSW')));
        if (!isset(self::PH_DAYS_BY_STATE[$auState])) {
            $auState = 'NSW';
        }

        if ($start === '' || $end === '') {
            delete_transient($lockKey);

            return new WP_Error(
                'missing_times',
                __('Start and end times are required.', 'cheapalarms'),
                ['status' => 400]
            );
        }
        if ($schedMode === 'dates' && $shiftDate === '') {
            delete_transient($lockKey);

            return new WP_Error(
                'missing_date',
                __('Choose a shift date.', 'cheapalarms'),
                ['status' => 400]
            );
        }
        if ($schedMode === 'weekly' && $days === []) {
            delete_transient($lockKey);

            return new WP_Error(
                'missing_days',
                __('Select at least one day.', 'cheapalarms'),
                ['status' => 400]
            );
        }

        $card = $this->getGlobalRateCard();
        $rates = $jobType === 'permanent' ? $card['permanent'] : $card['adhoc'];
        $shortNotice = !empty($body['shortNotice']);
        $includeSupervisor = !empty($body['includeSupervisor']);
        $termDiscountPct = $jobType === 'permanent'
            ? $this->termDiscountPercent($durationMonths, $card['termDiscounts'])
            : 0.0;

        $pricing = $this->calculatePricing([
            'jobType'            => $jobType,
            'durationMonths'     => $durationMonths,
            'schedMode'          => $schedMode,
            'defaultGuards'      => $defaultGuards,
            'start'              => $start,
            'end'                => $end,
            'shiftDate'          => $shiftDate,
            'days'               => $days,
            'sites'              => $sites,
            'phCover'            => $phCover,
            'auState'            => $auState,
            'rates'              => $rates,
            'termDiscountPct'    => $termDiscountPct,
            'shortNotice'        => $shortNotice,
            'shortNoticePct'     => $card['shortNoticePct'],
            'includeSupervisor'  => $includeSupervisor,
            'supervisorUplift'   => $card['supervisorUplift'],
        ]);
        if (is_wp_error($pricing)) {
            delete_transient($lockKey);

            return $pricing;
        }

        $quoteId = 'GQ-' . strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 8));
        $now     = current_time('c');

        $quote = [
            'id'           => $quoteId,
            'status'       => 'submitted',
            'createdAt'    => $now,
            'updatedAt'    => $now,
            'ghlContactId' => $contactId,
            'locationId'   => $locationId,
            'portalInvite' => false,
            'contact'      => [
                'name'      => $contactName,
                'firstName' => $firstName,
                'lastName'  => $lastName,
                'email'     => $email,
                'phone'     => $phone,
            ],
            'jobType'            => $jobType,
            'durationMonths'     => $durationMonths,
            'serviceType'        => sanitize_text_field((string) ($body['serviceType'] ?? '')),
            'schedMode'          => $schedMode,
            'staffingMode'       => $staffingMode,
            'totalGuards'        => $totalGuards,
            'defaultGuards'      => $defaultGuards,
            'start'              => $start,
            'end'                => $end,
            'shiftDate'          => $shiftDate,
            'days'               => $days,
            'sites'              => $sites,
            'phCover'            => $phCover,
            'auState'            => $auState,
            'shortNotice'        => $shortNotice,
            'includeSupervisor'  => $includeSupervisor,
            'termDiscountPct'    => $termDiscountPct,
            'termDiscounts'      => $card['termDiscounts'],
            'shortNoticePct'     => $card['shortNoticePct'],
            'supervisorUplift'   => $card['supervisorUplift'],
            'rates'              => $rates,
            'pricing'            => $pricing,
            'adjusted'           => null,
            'adminNotes'         => '',
            'emailSent'          => false,
            'needsResend'        => false,
            'revision'           => 0,
            'ghlEstimateId'      => null,
        ];

        $this->persistQuote($quote);

        $estimateId = $this->createGhlEstimate($quote, $contactId, $locationId);
        $portalBootstrap = null;
        if (is_string($estimateId) && $estimateId !== '') {
            $quote['ghlEstimateId'] = $estimateId;
            $portalBootstrap = $this->bootstrapPortalAccess(
                $quote,
                $estimateId,
                $contactId,
                $locationId,
                $firstName,
                $lastName,
                $email,
            );
            if (is_wp_error($portalBootstrap)) {
                $this->logger->warning('Guarding portal bootstrap failed (non-blocking)', [
                    'quoteId'    => $quoteId,
                    'estimateId' => $estimateId,
                    'error'      => $portalBootstrap->get_error_message(),
                ]);
                $quote['portalInvite'] = false;
                $quote['portalBootstrapFailed'] = true;
            } else {
                $quote['portalInvite']  = !empty($portalBootstrap['portalInvite']);
                $quote['portalUserId']  = $portalBootstrap['userId'] ?? null;
                $quote['portalUrl']     = $portalBootstrap['portalUrl'] ?? '';
                if (!empty($portalBootstrap['inviteEmailFailed'])) {
                    $quote['portalBootstrapFailed'] = true;
                }
            }
            $quote['updatedAt'] = current_time('c');
            $this->persistQuote($quote);
        }

        foreach (['guarding_quote', 'manpower_quote', 'website_quote'] as $tag) {
            $tagResult = $this->signalService->mergePortalTag($contactId, $locationId, $tag);
            if (is_wp_error($tagResult)) {
                $this->logger->warning('Guarding quote tag failed (non-blocking)', [
                    'quoteId'   => $quoteId,
                    'contactId' => $contactId,
                    'tag'       => $tag,
                    'error'     => $tagResult->get_error_message(),
                ]);
            }
        }

        $noteResult = $this->signalService->postContactTimelineNote(
            $contactId,
            $locationId,
            $this->buildTimelineNote($quote)
        );
        if (is_wp_error($noteResult)) {
            $this->logger->warning('Guarding quote timeline note failed (non-blocking)', [
                'quoteId'   => $quoteId,
                'contactId' => $contactId,
                'error'     => $noteResult->get_error_message(),
            ]);
        }

        $this->logger->info('Guarding quote submitted (awaiting admin send)', [
            'quoteId'       => $quoteId,
            'contactId'     => $contactId,
            'ghlEstimateId' => $quote['ghlEstimateId'] ?? null,
            'emailSent'     => false,
            'portalInvite'  => !empty($quote['portalInvite']),
        ]);

        $message = $this->customerSubmitMessage($quote, $email, $portalBootstrap);

        return [
            'ok'            => true,
            'quoteId'       => $quoteId,
            'contactId'     => $contactId,
            'ghlEstimateId' => $quote['ghlEstimateId'] ?? null,
            'emailSent'     => false,
            'portalInvite'  => !empty($quote['portalInvite']),
            'message'       => $message,
        ];
    }

    /**
     * Global rate cards used as default snapshots for new quotes.
     *
     * @return array{
     *   ok: bool,
     *   rates: array<string, float>,
     *   adhoc: array<string, float>,
     *   permanent: array<string, float>,
     *   termDiscounts: array<string, float>,
     *   shortNoticePct: float,
     *   supervisorUplift: float
     * }
     */
    public function getRateCard(): array
    {
        $card = $this->getGlobalRateCard();

        return [
            'ok'               => true,
            'rates'            => $card['adhoc'], // legacy alias
            'adhoc'            => $card['adhoc'],
            'permanent'        => $card['permanent'],
            'termDiscounts'    => $card['termDiscounts'],
            'shortNoticePct'   => $card['shortNoticePct'],
            'supervisorUplift' => $card['supervisorUplift'],
        ];
    }

    /**
     * @param array<string, mixed> $body
     * @return array{
     *   ok: bool,
     *   rates: array<string, float>,
     *   adhoc: array<string, float>,
     *   permanent: array<string, float>,
     *   termDiscounts: array<string, float>,
     *   shortNoticePct: float,
     *   supervisorUplift: float
     * }|WP_Error
     */
    public function updateRateCard(array $body): array|WP_Error
    {
        $existing = $this->getGlobalRateCard();

        if (isset($body['adhoc']) && is_array($body['adhoc'])) {
            $existing['adhoc'] = $this->normalizeRates($body['adhoc'], self::DEFAULT_ADHOC_RATES);
        } elseif (isset($body['rates']) && is_array($body['rates']) && !isset($body['permanent'])) {
            // Legacy single-card PUT → update ad hoc only.
            $existing['adhoc'] = $this->normalizeRates($body['rates'], self::DEFAULT_ADHOC_RATES);
        }

        if (isset($body['permanent']) && is_array($body['permanent'])) {
            $existing['permanent'] = $this->normalizeRates($body['permanent'], self::DEFAULT_PERMANENT_RATES);
        }

        if (isset($body['termDiscounts']) && is_array($body['termDiscounts'])) {
            $existing['termDiscounts'] = $this->normalizeTermDiscounts($body['termDiscounts']);
        }

        if (array_key_exists('shortNoticePct', $body)) {
            $existing['shortNoticePct'] = $this->clampPercent($body['shortNoticePct'], self::DEFAULT_SHORT_NOTICE_PCT);
        }
        if (array_key_exists('supervisorUplift', $body)) {
            $n = is_numeric($body['supervisorUplift']) ? (float) $body['supervisorUplift'] : self::DEFAULT_SUPERVISOR_UPLIFT;
            $existing['supervisorUplift'] = round(max(0, $n), 2);
        }

        update_option(self::OPTION_RATES, [
            'adhoc'            => $existing['adhoc'],
            'permanent'        => $existing['permanent'],
            'termDiscounts'    => $existing['termDiscounts'],
            'shortNoticePct'   => $existing['shortNoticePct'],
            'supervisorUplift' => $existing['supervisorUplift'],
        ], false);

        $this->logger->info('Guarding global rate card updated', [
            'adhoc'     => $existing['adhoc'],
            'permanent' => $existing['permanent'],
        ]);

        return [
            'ok'               => true,
            'rates'            => $existing['adhoc'],
            'adhoc'            => $existing['adhoc'],
            'permanent'        => $existing['permanent'],
            'termDiscounts'    => $existing['termDiscounts'],
            'shortNoticePct'   => $existing['shortNoticePct'],
            'supervisorUplift' => $existing['supervisorUplift'],
        ];
    }

    /**
     * @return array{ok: bool, quotes: list<array<string, mixed>>, total: int}
     */
    public function listQuotes(int $page = 1, int $pageSize = 25, string $search = '', string $status = ''): array
    {
        $all = $this->allQuotes();
        usort($all, static fn ($a, $b) => strcmp((string) ($b['createdAt'] ?? ''), (string) ($a['createdAt'] ?? '')));

        if ($status !== '') {
            $all = array_values(array_filter(
                $all,
                static fn ($q) => ($q['status'] ?? '') === $status
            ));
        }

        if ($search !== '') {
            $needle = strtolower($search);
            $all = array_values(array_filter($all, static function ($q) use ($needle) {
                $hay = strtolower(implode(' ', [
                    $q['id'] ?? '',
                    $q['contact']['name'] ?? '',
                    $q['contact']['email'] ?? '',
                    $q['contact']['phone'] ?? '',
                    $q['serviceType'] ?? '',
                ]));

                return str_contains($hay, $needle);
            }));
        }

        $total = count($all);
        $page  = max(1, $page);
        $pageSize = min(100, max(1, $pageSize));
        $offset = ($page - 1) * $pageSize;

        $slice = array_slice($all, $offset, $pageSize);
        foreach ($slice as &$row) {
            $row['revision'] = max(0, (int) ($row['revision'] ?? 0));
            $row['needsResend'] = !empty($row['needsResend']);
        }
        unset($row);

        return [
            'ok'     => true,
            'quotes' => $slice,
            'total'  => $total,
            'page'   => $page,
            'pageSize' => $pageSize,
        ];
    }

    /**
     * @return array<string, mixed>|WP_Error
     */
    public function getQuote(string $quoteId): array|WP_Error
    {
        $quote = $this->findQuote($quoteId);
        if ($quote === null) {
            return new WP_Error('not_found', __('Guarding quote not found.', 'cheapalarms'), ['status' => 404]);
        }

        $quote['rates'] = $this->normalizeRates($quote['rates'] ?? null);
        $quote['revision'] = max(0, (int) ($quote['revision'] ?? 0));
        $quote['needsResend'] = !empty($quote['needsResend']);

        return ['ok' => true, 'quote' => $quote];
    }

    /**
     * Staff rate / status adjustments.
     *
     * @param array<string, mixed> $body
     * @return array<string, mixed>|WP_Error
     */
    public function updateQuote(string $quoteId, array $body): array|WP_Error
    {
        $quote = $this->findQuote($quoteId);
        if ($quote === null) {
            return new WP_Error('not_found', __('Guarding quote not found.', 'cheapalarms'), ['status' => 404]);
        }

        if (isset($body['status'])) {
            $status = sanitize_text_field((string) $body['status']);
            // Only submitted | sent — "sent" is set by sendQuoteToCustomer().
            if (in_array($status, ['submitted', 'sent'], true)) {
                $quote['status'] = $status;
            }
        }

        if (array_key_exists('adminNotes', $body)) {
            $quote['adminNotes'] = sanitize_text_field((string) $body['adminNotes']);
        }

        $pricingChanged = false;
        $recalculated = false;
        $contactResult = $this->applyContactUpdate($quote, $body);
        if (is_wp_error($contactResult)) {
            return $contactResult;
        }

        if (array_key_exists('shortNotice', $body)) {
            $next = !empty($body['shortNotice']);
            if ($next !== !empty($quote['shortNotice'])) {
                $quote['shortNotice'] = $next;
                $pricingChanged = true;
            }
        }
        if (array_key_exists('includeSupervisor', $body)) {
            $next = !empty($body['includeSupervisor']);
            if ($next !== !empty($quote['includeSupervisor'])) {
                $quote['includeSupervisor'] = $next;
                $pricingChanged = true;
            }
        }

        // Rate grid edit → recalculate authoritative pricing for this quote.
        if (isset($body['rates']) && is_array($body['rates'])) {
            $defaults = (($quote['jobType'] ?? '') === 'permanent')
                ? self::DEFAULT_PERMANENT_RATES
                : self::DEFAULT_ADHOC_RATES;
            $quote['rates'] = $this->normalizeRates($body['rates'], $defaults);
            $pricingChanged = true;
        }

        if ($pricingChanged || isset($body['rates'])) {
            $pricing = $this->recalculateFromQuote($quote);
            if (is_wp_error($pricing)) {
                return $pricing;
            }
            $quote['pricing'] = $pricing;
            $recalculated = true;
            // Sync adjusted from rates unless client explicitly applies a manual override.
            if (empty($body['applyAdjusted'])) {
                $quote['adjusted'] = [
                    'subtotal'  => $pricing['subtotal'],
                    'gst'       => $pricing['gst'],
                    'total'     => $pricing['total'],
                    'notes'     => sanitize_text_field((string) ($body['adjustedNotes'] ?? ($quote['adjusted']['notes'] ?? 'Recalculated from rate grid'))),
                    'updatedAt' => current_time('c'),
                    'fromRates' => true,
                ];
            }
            $pricingChanged = true;
        }

        $wantAdjusted = !empty($body['applyAdjusted'])
            || ((!$recalculated) && (isset($body['adjustedTotal']) || isset($body['adjustedSubtotal']) || isset($body['adjustedNotes'])));

        if ($wantAdjusted && (isset($body['adjustedTotal']) || isset($body['adjustedSubtotal']) || isset($body['adjustedNotes']))) {
            $hasTotal = array_key_exists('adjustedTotal', $body) && $body['adjustedTotal'] !== '' && $body['adjustedTotal'] !== null;
            $hasSub   = array_key_exists('adjustedSubtotal', $body) && $body['adjustedSubtotal'] !== '' && $body['adjustedSubtotal'] !== null;

            if ($hasTotal && !$hasSub) {
                // GST-inclusive total → derive ex-GST + GST (AUD GST = total / 11).
                $adjTotal = $this->money($body['adjustedTotal']);
                $adjSub   = round($adjTotal * 10 / 11, 2);
                $adjGst   = round($adjTotal - $adjSub, 2);
            } elseif ($hasSub && !$hasTotal) {
                $adjSub   = $this->money($body['adjustedSubtotal']);
                $adjGst   = isset($body['adjustedGst']) ? $this->money($body['adjustedGst']) : round($adjSub * 0.1, 2);
                $adjTotal = round($adjSub + $adjGst, 2);
            } elseif ($hasTotal && $hasSub) {
                $adjTotal = $this->money($body['adjustedTotal']);
                $adjSub   = $this->money($body['adjustedSubtotal']);
                $adjGst   = isset($body['adjustedGst']) ? $this->money($body['adjustedGst']) : round($adjTotal - $adjSub, 2);
            } else {
                $adjTotal = (float) ($quote['adjusted']['total'] ?? $quote['pricing']['total'] ?? 0);
                $adjSub   = (float) ($quote['adjusted']['subtotal'] ?? $quote['pricing']['subtotal'] ?? 0);
                $adjGst   = (float) ($quote['adjusted']['gst'] ?? $quote['pricing']['gst'] ?? round($adjTotal - $adjSub, 2));
            }

            $quote['adjusted'] = [
                'subtotal'  => $adjSub,
                'gst'       => $adjGst,
                'total'     => $adjTotal,
                'notes'     => sanitize_text_field((string) ($body['adjustedNotes'] ?? ($quote['adjusted']['notes'] ?? ''))),
                'updatedAt' => current_time('c'),
            ];
            if ($hasTotal || $hasSub) {
                $pricingChanged = true;
            }
        }

        // Pricing changed after a prior send → allow "Send updated quote" again.
        if ($pricingChanged && ((int) ($quote['revision'] ?? 0) > 0 || !empty($quote['emailSent']))) {
            $quote['needsResend'] = true;
        }

        $quote['updatedAt'] = current_time('c');
        $this->persistQuote($quote);

        $this->logger->info('Guarding quote updated by admin', [
            'quoteId'     => $quoteId,
            'status'      => $quote['status'],
            'needsResend' => !empty($quote['needsResend']),
        ]);

        return ['ok' => true, 'quote' => $quote];
    }

    /**
     * Update contact fields; if email changes, resolve/create GHL contact by email only.
     *
     * @param array<string, mixed> $quote
     * @param array<string, mixed> $body
     * @return bool|WP_Error true if contact changed
     */
    private function applyContactUpdate(array &$quote, array $body): bool|WP_Error
    {
        $incoming = null;
        if (isset($body['contact']) && is_array($body['contact'])) {
            $incoming = $body['contact'];
        } elseif (isset($body['contactName']) || isset($body['email']) || isset($body['phone'])) {
            $incoming = [
                'name'  => $body['contactName'] ?? ($quote['contact']['name'] ?? ''),
                'email' => $body['email'] ?? ($quote['contact']['email'] ?? ''),
                'phone' => $body['phone'] ?? ($quote['contact']['phone'] ?? ''),
            ];
        }
        if ($incoming === null) {
            return false;
        }

        $name = sanitize_text_field((string) ($incoming['name'] ?? $quote['contact']['name'] ?? ''));
        $email = sanitize_email((string) ($incoming['email'] ?? $quote['contact']['email'] ?? ''));
        $phoneRaw = sanitize_text_field((string) ($incoming['phone'] ?? $quote['contact']['phone'] ?? ''));

        if ($name === '' || $email === '' || !is_email($email) || $phoneRaw === '') {
            return new WP_Error(
                'invalid_contact',
                __('Contact needs a name, valid email, and phone number.', 'cheapalarms'),
                ['status' => 400]
            );
        }

        $phone = AustralianPhone::toE164($phoneRaw);
        if ($phone === null) {
            return new WP_Error(
                'invalid_phone',
                __('Please enter a valid Australian phone number (e.g. 04XX XXX XXX).', 'cheapalarms'),
                ['status' => 400]
            );
        }

        $prevName  = (string) ($quote['contact']['name'] ?? '');
        $prevEmail = strtolower((string) ($quote['contact']['email'] ?? ''));
        $prevPhone = (string) ($quote['contact']['phone'] ?? '');
        $nextEmail = strtolower($email);

        if ($prevName === $name && $prevEmail === $nextEmail && $prevPhone === $phone) {
            return false;
        }

        [$firstName, $lastName] = $this->splitName($name);

        $quote['contact'] = [
            'name'      => $name,
            'firstName' => $firstName,
            'lastName'  => $lastName,
            'email'     => $email,
            'phone'     => $phone,
        ];

        if ($prevEmail !== $nextEmail) {
            $locationId = (string) ($quote['locationId'] ?? $this->config->getLocationId());
            // Email change: match by email only — never reuse a contact found only by phone.
            $contactId = $this->resolveOrCreateContactByEmail($firstName, $lastName, $email, $phone, $locationId);
            if (is_wp_error($contactId)) {
                return $contactId;
            }
            $quote['ghlContactId'] = $contactId;
            $this->logger->info('Guarding quote contact rebound to GHL', [
                'quoteId'   => $quote['id'] ?? '',
                'email'     => $email,
                'contactId' => $contactId,
            ]);
        }

        return true;
    }

    /**
     * @param array<string, mixed> $quote
     * @return array<string, mixed>|WP_Error
     */
    private function recalculateFromQuote(array &$quote): array|WP_Error
    {
        $card = $this->getGlobalRateCard();
        $jobType = (string) ($quote['jobType'] ?? 'adhoc');
        $durationMonths = (int) ($quote['durationMonths'] ?? 0);
        $defaults = $jobType === 'permanent' ? self::DEFAULT_PERMANENT_RATES : self::DEFAULT_ADHOC_RATES;
        $rates = $this->normalizeRates($quote['rates'] ?? null, $defaults);

        $termDiscountPct = $jobType === 'permanent'
            ? $this->termDiscountPercent(
                $durationMonths,
                is_array($quote['termDiscounts'] ?? null)
                    ? $this->normalizeTermDiscounts($quote['termDiscounts'])
                    : $card['termDiscounts']
            )
            : 0.0;
        $quote['termDiscountPct'] = $termDiscountPct;
        if (!isset($quote['termDiscounts']) || !is_array($quote['termDiscounts'])) {
            $quote['termDiscounts'] = $card['termDiscounts'];
        }
        $quote['shortNoticePct'] = (float) ($quote['shortNoticePct'] ?? $card['shortNoticePct']);
        $quote['supervisorUplift'] = (float) ($quote['supervisorUplift'] ?? $card['supervisorUplift']);

        return $this->calculatePricing([
            'jobType'           => $jobType,
            'durationMonths'    => $durationMonths,
            'schedMode'         => $quote['schedMode'] ?? 'dates',
            'defaultGuards'     => (int) ($quote['defaultGuards'] ?? 1),
            'start'             => $quote['start'] ?? '',
            'end'               => $quote['end'] ?? '',
            'shiftDate'         => $quote['shiftDate'] ?? '',
            'days'              => is_array($quote['days'] ?? null) ? $quote['days'] : [],
            'sites'             => is_array($quote['sites'] ?? null) ? $quote['sites'] : [],
            'phCover'           => !empty($quote['phCover']),
            'auState'           => $quote['auState'] ?? 'NSW',
            'rates'             => $rates,
            'termDiscountPct'   => $termDiscountPct,
            'shortNotice'       => !empty($quote['shortNotice']),
            'shortNoticePct'    => $quote['shortNoticePct'],
            'includeSupervisor' => !empty($quote['includeSupervisor']),
            'supervisorUplift'  => $quote['supervisorUplift'],
        ]);
    }

    /**
     * Admin "Send quote" — marks quote sent in portal and emails a portal link (not inline quote HTML).
     *
     * @return array<string, mixed>|WP_Error
     */
    public function sendQuoteToCustomer(string $quoteId): array|WP_Error
    {
        $quote = $this->findQuote($quoteId);
        if ($quote === null) {
            return new WP_Error('not_found', __('Guarding quote not found.', 'cheapalarms'), ['status' => 404]);
        }

        $alreadySent = ((int) ($quote['revision'] ?? 0) > 0)
            || !empty($quote['emailSent'])
            || (($quote['status'] ?? '') === 'sent');
        if ($alreadySent && empty($quote['needsResend'])) {
            return new WP_Error(
                'already_sent',
                __('This quote has already been sent. Change the rates or total first, then send the updated quote.', 'cheapalarms'),
                ['status' => 409]
            );
        }

        $estimateId = (string) ($quote['ghlEstimateId'] ?? '');
        if ($estimateId === '') {
            return new WP_Error(
                'no_estimate',
                __('Cannot send quote: GoHighLevel estimate is missing on this request.', 'cheapalarms'),
                ['status' => 400]
            );
        }

        $nextRevision = max(0, (int) ($quote['revision'] ?? 0)) + 1;
        $sent         = $this->sendPortalQuoteReadyEmail($quote, $estimateId, $nextRevision);
        if (is_wp_error($sent)) {
            return $sent;
        }

        $quote['revision']     = $nextRevision;
        $quote['needsResend']  = false;
        $quote['emailSent']    = true;
        $quote['status']       = 'sent';
        $quote['lastSentAt']   = current_time('c');
        $quote['updatedAt']    = current_time('c');
        $quote['portalInvite'] = true;
        $this->persistQuote($quote);

        $this->logger->info('Guarding quote sent via portal email', [
            'quoteId'    => $quoteId,
            'email'      => $quote['contact']['email'] ?? '',
            'contactId'  => $quote['ghlContactId'] ?? '',
            'revision'   => $nextRevision,
            'estimateId' => $estimateId,
        ]);

        return ['ok' => true, 'quote' => $quote, 'emailSent' => true, 'revision' => $nextRevision];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function allQuotes(): array
    {
        $raw = get_option(self::OPTION_KEY, []);
        if (!is_array($raw)) {
            return [];
        }

        return array_values(array_filter($raw, 'is_array'));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findQuote(string $quoteId): ?array
    {
        foreach ($this->allQuotes() as $quote) {
            if (($quote['id'] ?? '') === $quoteId) {
                return $quote;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $quote
     */
    private function persistQuote(array $quote): void
    {
        $all = $this->allQuotes();
        $found = false;
        foreach ($all as $i => $existing) {
            if (($existing['id'] ?? '') === ($quote['id'] ?? '')) {
                $all[$i] = $quote;
                $found = true;
                break;
            }
        }
        if (!$found) {
            array_unshift($all, $quote);
        }
        if (count($all) > self::MAX_STORED) {
            $all = array_slice($all, 0, self::MAX_STORED);
        }
        update_option(self::OPTION_KEY, $all, false);
    }

    /**
     * @return string|WP_Error
     */
    private function resolveOrCreateContact(
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $locationId
    ): string|WP_Error {
        $existingId = $this->findContactByPhoneOrEmail($phone, $email, $locationId);
        if ($existingId !== null) {
            return $existingId;
        }

        $result = $this->ghlClient->post('/contacts/', [
            'firstName'  => $firstName,
            'lastName'   => $lastName !== '' ? $lastName : '-',
            'email'      => $email,
            'phone'      => $phone,
            'locationId' => $locationId,
            'source'     => 'ManPower Calculator',
        ], 15, $locationId);

        if (!is_wp_error($result)) {
            $id = $result['contact']['id'] ?? $result['id'] ?? $result['contactId'] ?? null;

            return is_string($id) && $id !== '' ? $id : new WP_Error(
                'ghl_contact_missing_id',
                __('Could not save your details. Please call 1300 225 276.', 'cheapalarms'),
                ['status' => 502]
            );
        }

        $errorData = $result->get_error_data();
        if ($result->get_error_code() === 'ghl_http_error' && isset($errorData['code']) && (int) $errorData['code'] === 400) {
            $errorBody = $errorData['body'] ?? null;
            if (is_string($errorBody)) {
                $decoded = json_decode($errorBody, true);
                if (is_array($decoded)) {
                    $errorBody = $decoded;
                }
            }
            if (is_array($errorBody)) {
                $contactId = $errorBody['meta']['contactId'] ?? null;
                if (is_string($contactId) && $contactId !== '') {
                    return $contactId;
                }
            }
        }

        $this->logger->error('Guarding quote contact creation failed', [
            'error' => $result->get_error_message(),
        ]);

        return new WP_Error(
            'ghl_contact_failed',
            __('Could not save your details. Please call 1300 225 276.', 'cheapalarms'),
            ['status' => 502]
        );
    }

    /**
     * Resolve/create GHL contact by email only (used when admin changes quote email).
     *
     * @return string|WP_Error
     */
    private function resolveOrCreateContactByEmail(
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $locationId
    ): string|WP_Error {
        $existingId = $this->findContactByEmail($email, $locationId);
        if ($existingId !== null) {
            return $existingId;
        }

        $result = $this->ghlClient->post('/contacts/', [
            'firstName'  => $firstName,
            'lastName'   => $lastName !== '' ? $lastName : '-',
            'email'      => $email,
            'phone'      => $phone,
            'locationId' => $locationId,
            'source'     => 'ManPower Calculator',
        ], 15, $locationId);

        if (!is_wp_error($result)) {
            $id = $result['contact']['id'] ?? $result['id'] ?? $result['contactId'] ?? null;

            return is_string($id) && $id !== '' ? $id : new WP_Error(
                'ghl_contact_missing_id',
                __('Could not save contact details in GoHighLevel.', 'cheapalarms'),
                ['status' => 502]
            );
        }

        $errorData = $result->get_error_data();
        if ($result->get_error_code() === 'ghl_http_error' && isset($errorData['code']) && (int) $errorData['code'] === 400) {
            $errorBody = $errorData['body'] ?? null;
            if (is_string($errorBody)) {
                $decoded = json_decode($errorBody, true);
                if (is_array($decoded)) {
                    $errorBody = $decoded;
                }
            }
            if (is_array($errorBody)) {
                $contactId = $errorBody['meta']['contactId'] ?? null;
                if (is_string($contactId) && $contactId !== '') {
                    return $contactId;
                }
            }
        }

        $this->logger->error('Guarding quote email-only contact creation failed', [
            'error' => $result->get_error_message(),
            'email' => $email,
        ]);

        return new WP_Error(
            'ghl_contact_failed',
            __('Could not save contact details in GoHighLevel.', 'cheapalarms'),
            ['status' => 502]
        );
    }

    private function findContactByEmail(string $email, string $locationId): ?string
    {
        if ($email === '' || !$this->config->isGhlFetchAllowed()) {
            return null;
        }

        $search = $this->ghlClient->get('/contacts/search', ['query' => $email], 10, $locationId);
        if (is_wp_error($search)) {
            return null;
        }
        $contacts = $search['contacts'] ?? $search['items'] ?? [];
        if (!is_array($contacts)) {
            return null;
        }
        $needle = strtolower($email);
        foreach ($contacts as $contact) {
            if (!is_array($contact)) {
                continue;
            }
            $id = $contact['id'] ?? null;
            if (!is_string($id) || $id === '') {
                continue;
            }
            $contactEmail = strtolower((string) ($contact['email'] ?? ''));
            if ($contactEmail === $needle) {
                return $id;
            }
        }

        return null;
    }

    private function findContactByPhoneOrEmail(string $phone, string $email, string $locationId): ?string
    {
        if (!$this->config->isGhlFetchAllowed()) {
            return null;
        }

        foreach ([$email, $phone] as $query) {
            if ($query === '') {
                continue;
            }
            $search = $this->ghlClient->get('/contacts/search', ['query' => $query], 10, $locationId);
            if (is_wp_error($search)) {
                continue;
            }
            $contacts = $search['contacts'] ?? $search['items'] ?? [];
            if (!is_array($contacts)) {
                continue;
            }
            foreach ($contacts as $contact) {
                if (!is_array($contact)) {
                    continue;
                }
                $id = $contact['id'] ?? null;
                if (!is_string($id) || $id === '') {
                    continue;
                }
                $contactPhone = AustralianPhone::toE164((string) ($contact['phone'] ?? ''));
                $contactEmail = strtolower((string) ($contact['email'] ?? ''));
                if ($contactPhone === $phone || $contactEmail === strtolower($email)) {
                    return $id;
                }
            }
        }

        return null;
    }

    /**
     * Create a GHL estimate (invoices/estimate) for staff — no portal invite, no snapshot pollution via EstimateService.
     *
     * @param array<string, mixed> $quote
     */
    private function createGhlEstimate(array $quote, string $contactId, string $locationId): ?string
    {
        $contactName = (string) ($quote['contact']['name'] ?? 'Customer');
        $email       = (string) ($quote['contact']['email'] ?? '');
        $phone       = (string) ($quote['contact']['phone'] ?? '');
        $quoteId     = (string) ($quote['id'] ?? '');
        $sitesCount  = is_array($quote['sites'] ?? null) ? count($quote['sites']) : 0;
        $subtotal    = (float) ($quote['pricing']['subtotal'] ?? 0);
        $gst         = (float) ($quote['pricing']['gst'] ?? 0);
        $total       = (float) ($quote['pricing']['total'] ?? 0);
        $hours       = (float) ($quote['pricing']['manpowerHours'] ?? 0);

        if ($contactId === '' || $locationId === '' || $total <= 0) {
            $this->logger->warning('Guarding GHL estimate skipped (missing contact/location/total)', [
                'quoteId'   => $quoteId,
                'contactId' => $contactId,
                'total'     => $total,
            ]);

            return null;
        }

        $brand = $this->config->getBrandName() ?: 'Safeguard';
        $name  = mb_substr('Guarding - ' . $contactName, 0, 40);

        $itemName = $this->ghlLineItemName($quote);
        $itemDesc = sprintf(
            "Guide quote %s · %d site(s) · %.1f manpower hours\nSubtotal ex GST: AUD %s\nGST 10%%: AUD %s\nTotal incl. GST: AUD %s\nSubject to confirmation.",
            $quoteId,
            $sitesCount,
            $hours,
            number_format($subtotal, 2),
            number_format($gst, 2),
            number_format($total, 2)
        );

        // GHL line amount = ex-GST; total incl GST is documented in description/terms.
        $payload = [
            'altId'   => $locationId,
            'altType' => 'location',
            'name'    => $name,
            'title'   => 'ESTIMATE',
            'businessDetails' => [
                'name' => $brand,
                'address' => [
                    'addressLine1' => $brand,
                    'city'         => 'Brisbane',
                    'state'        => 'QLD',
                    'postalCode'   => '4000',
                    'countryCode'  => 'AU',
                ],
            ],
            'currency' => 'AUD',
            'discount' => [
                'type'  => 'percentage',
                'value' => 0,
            ],
            'contactDetails' => [
                'id'      => $contactId,
                'email'   => $email,
                'name'    => $contactName,
                'phoneNo' => $phone,
                'address' => [
                    'addressLine1' => '',
                    'city'         => '',
                    'state'        => '',
                    'postalCode'   => '',
                    'countryCode'  => 'AU',
                ],
            ],
            'issueDate'         => gmdate('Y-m-d'),
            'expiryDate'        => gmdate('Y-m-d', strtotime('+30 days')),
            'frequencySettings' => ['enabled' => false],
            'liveMode'          => true,
            'items'             => [
                [
                    'name'        => $itemName,
                    'description' => $itemDesc,
                    'currency'    => 'AUD',
                    'amount'      => round(max(0.01, $subtotal), 2),
                    'qty'         => 1,
                    'type'        => 'one_time',
                ],
            ],
            'termsNotes' => $this->buildTimelineNote($quote),
        ];

        $result = $this->ghlClient->post('/invoices/estimate', $payload, 12, $locationId, 0);
        if (is_wp_error($result)) {
            $this->logger->warning('Guarding GHL estimate create failed (non-blocking)', [
                'quoteId'   => $quoteId,
                'contactId' => $contactId,
                'error'     => $result->get_error_message(),
            ]);

            return null;
        }

        $estimateId = $result['estimate']['id'] ?? $result['id'] ?? $result['_id'] ?? null;
        if (!is_string($estimateId) || $estimateId === '') {
            $this->logger->warning('Guarding GHL estimate created but id missing', [
                'quoteId' => $quoteId,
                'keys'    => array_keys(is_array($result) ? $result : []),
            ]);

            return null;
        }

        $this->logger->info('Guarding GHL estimate created', [
            'quoteId'       => $quoteId,
            'ghlEstimateId' => $estimateId,
            'contactId'     => $contactId,
        ]);

        return $estimateId;
    }

    /**
     * Create/link portal access for a guarding estimate (matches alarm/CCTV quote-request flow).
     *
     * @param array<string, mixed> $quote
     * @return array{portalInvite: bool, userId: int|null, portalUrl: string, createdUser: bool, inviteEmailFailed?: bool}|WP_Error
     */
    private function bootstrapPortalAccess(
        array $quote,
        string $estimateId,
        string $contactId,
        string $locationId,
        string $firstName,
        string $lastName,
        string $email
    ): array|WP_Error {
        $frontendUrl = $this->config->getFrontendUrl();
        if ($frontendUrl === '') {
            return new WP_Error(
                'portal_misconfigured',
                __('Portal URL is not configured.', 'cheapalarms'),
                ['status' => 500]
            );
        }

        $wpUserId  = email_exists($email);
        $isNewUser = !$wpUserId;

        if ($isNewUser) {
            $createResult = wp_create_user($email, wp_generate_password(20), $email);
            if (is_wp_error($createResult)) {
                return $createResult;
            }
            $wpUserId = (int) $createResult;
            wp_update_user([
                'ID'           => $wpUserId,
                'first_name'   => $firstName,
                'last_name'    => $lastName,
                'display_name' => trim("{$firstName} {$lastName}"),
                'role'         => 'ca_customer',
            ]);
            delete_user_meta($wpUserId, 'ca_password_set_at');
            delete_user_meta($wpUserId, 'ca_last_login');
        } else {
            $user = get_user_by('id', (int) $wpUserId);
            if ($user && !in_array('ca_customer', $user->roles, true)) {
                wp_update_user(['ID' => (int) $wpUserId, 'role' => 'ca_customer']);
            }
            clean_user_cache((int) $wpUserId);
        }

        $linked = $this->portalService->linkEstimateToExistingAccount($estimateId, (int) $wpUserId, $locationId);
        if (is_wp_error($linked)) {
            return $linked;
        }

        $inviteToken     = PortalService::generateToken();
        $inviteTokenHash = PortalService::hashInviteToken($inviteToken);
        $portalUrl       = add_query_arg(
            [
                'estimateId'  => $estimateId,
                'inviteToken' => $inviteToken,
            ],
            trailingslashit($frontendUrl) . 'portal'
        );

        $total    = (float) ($quote['adjusted']['total'] ?? $quote['pricing']['total'] ?? 0);
        $subtotal = (float) ($quote['adjusted']['subtotal'] ?? $quote['pricing']['subtotal'] ?? 0);
        $resetUrl = null;
        $itemName = $this->ghlLineItemName($quote);

        if ($isNewUser) {
            $user = get_user_by('id', (int) $wpUserId);
            if ($user) {
                $key = get_password_reset_key($user);
                if (!is_wp_error($key)) {
                    $resetUrl = add_query_arg(
                        [
                            'key'        => $key,
                            'login'      => rawurlencode($user->user_login),
                            'estimateId' => $estimateId,
                        ],
                        trailingslashit($frontendUrl) . 'set-password'
                    );
                }
            }
        }

        $metaOk = $this->portalService->updateMeta($estimateId, [
            'guardingQuoteId' => (string) ($quote['id'] ?? ''),
            'source'          => 'manpower_calculator',
            'locationId'      => $locationId,
            'account'         => [
                'inviteToken'  => $inviteTokenHash,
                'portalUrl'    => $portalUrl,
                'status'       => $isNewUser ? 'pending' : 'active',
                'statusLabel'  => $isNewUser
                    ? __('Invite sent', 'cheapalarms')
                    : __('Account active', 'cheapalarms'),
                'userId'       => (int) $wpUserId,
                'lastInviteAt' => current_time('mysql'),
                'canResend'    => true,
                'expiresAt'    => gmdate('c', current_time('timestamp') + DAY_IN_SECONDS * 7),
                'email'        => $email,
                'locationId'   => $locationId,
                'resetUrl'     => $resetUrl,
            ],
            'itemsMeta' => $this->portalService->buildInitialItemsMeta([
                [
                    'name'          => $itemName,
                    'photoRequired' => false,
                    'isHeading'     => false,
                ],
            ]),
            'quote' => [
                'status'               => null,
                'statusLabel'          => __('Under review', 'cheapalarms'),
                'total'                => $total,
                'subtotal'             => $subtotal,
                'currency'             => 'AUD',
                'approval_requested'   => false,
                'photos_required'      => false,
                'acceptance_enabled'   => false,
            ],
            'workflow' => [
                'status'      => 'requested',
                'currentStep' => 1,
                'requestedAt' => current_time('mysql'),
            ],
        ]);

        if (!$metaOk) {
            $this->logger->warning('Guarding portal meta save failed', [
                'estimateId' => $estimateId,
                'quoteId'    => $quote['id'] ?? '',
            ]);
        }

        $inviteSent        = false;
        $inviteEmailFailed = false;
        if ($isNewUser) {
            $contactName = (string) ($quote['contact']['name'] ?? trim("{$firstName} {$lastName}"));
            $mailResult  = $this->sendNewUserPortalInviteEmail(
                $email,
                $contactName,
                $contactId,
                $locationId,
                $estimateId,
                $portalUrl,
                $resetUrl,
                (int) $wpUserId
            );
            if (is_wp_error($mailResult)) {
                $this->logger->warning('Guarding portal invite email failed after account create', [
                    'estimateId' => $estimateId,
                    'email'      => $email,
                    'error'      => $mailResult->get_error_message(),
                ]);
                $inviteEmailFailed = true;
            } else {
                $inviteSent = true;
            }
        }

        return [
            'portalInvite'      => $inviteSent,
            'userId'            => (int) $wpUserId,
            'portalUrl'         => $portalUrl,
            'createdUser'       => $isNewUser,
            'inviteEmailFailed' => $inviteEmailFailed,
        ];
    }

    /**
     * Portal invite for brand-new customers (not a quote-details email).
     *
     * @return true|WP_Error
     */
    private function sendNewUserPortalInviteEmail(
        string $email,
        string $contactName,
        string $contactId,
        string $locationId,
        string $estimateId,
        string $portalUrl,
        ?string $resetUrl,
        int $userId
    ): true|WP_Error {
        $brandName = $this->config->getBrandName() ?: 'Safeguard';
        $brandTeam = $brandName . ' Team';
        $loginUrl  = trailingslashit($this->config->getFrontendUrl()) . 'login';

        clean_user_cache($userId);
        $userContext = UserContextHelper::getUserContext($userId, $email, $estimateId);
        $userContext['isNewUser']             = true;
        $userContext['hasPasswordSet']        = false;
        $userContext['hasPreviousEstimates']  = false;
        $userContext['estimateCount']         = 0;

        $subject = sprintf(__('Your %s portal is ready', 'cheapalarms'), $brandName);
        $message = '';

        try {
            $emailTemplate = $this->emailTemplateService->renderQuoteRequestEmail($userContext, [
                'customerName'   => $contactName,
                'estimateNumber' => $estimateId,
                'portalUrl'      => $portalUrl,
                'resetUrl'       => $resetUrl,
                'loginUrl'       => $loginUrl,
            ]);
            $subject = $emailTemplate['subject'] ?? $subject;
            $message = $emailTemplate['body'] ?? '';
        } catch (\Throwable $e) {
            $this->logger->warning('Guarding portal invite template failed, using fallback', [
                'estimateId' => $estimateId,
                'error'      => $e->getMessage(),
            ]);
        }

        if ($message === '') {
            $message  = '<p>' . esc_html(sprintf(__('Hi %s,', 'cheapalarms'), $contactName)) . '</p>';
            $message .= '<p>' . esc_html(__('We received your security guard quote request. Set your password to access your portal — we will notify you when your quote is ready to view.', 'cheapalarms')) . '</p>';
            if ($resetUrl) {
                $message .= '<p>' . EmailTemplateHtmlHelper::inlineCtaButton($this->config, $resetUrl, __('Set Your Password', 'cheapalarms')) . '</p>';
            }
            if ($portalUrl) {
                $message .= '<p style="margin-top:16px;color:#64748b;font-size:14px;">'
                    . esc_html(__('or', 'cheapalarms'))
                    . ' <a href="' . esc_url($portalUrl) . '" style="color:#2fb6c9;text-decoration:underline;">'
                    . esc_html(__('open your portal as a guest', 'cheapalarms'))
                    . '</a></p>';
            }
            $message .= '<p>' . esc_html(__('Thanks,', 'cheapalarms')) . '<br />' . esc_html($brandTeam) . '</p>';
        }

        $result = $this->ghlClient->post('/conversations/messages', [
            'contactId'  => $contactId,
            'type'       => 'Email',
            'status'     => 'pending',
            'subject'    => $subject,
            'html'       => $message,
            'emailFrom'  => $this->config->getEmailFromHeader(),
            'locationId' => $locationId,
        ], 30, $locationId);

        if (is_wp_error($result)) {
            return new WP_Error(
                'invite_email_failed',
                sprintf(
                    /* translators: %s: GHL error */
                    __('Could not send the portal invite email: %s', 'cheapalarms'),
                    $result->get_error_message()
                ),
                ['status' => 502]
            );
        }

        return true;
    }

    /**
     * Admin send — portal notification (estimate ready), not inline quote HTML.
     *
     * @param array<string, mixed> $quote
     * @return true|WP_Error
     */
    private function sendPortalQuoteReadyEmail(array $quote, string $estimateId, int $revision): true|WP_Error
    {
        $email      = sanitize_email((string) ($quote['contact']['email'] ?? ''));
        $contactId  = (string) ($quote['ghlContactId'] ?? '');
        $locationId = (string) ($quote['locationId'] ?? $this->config->getLocationId());

        if ($email === '' || !is_email($email)) {
            return new WP_Error(
                'email_missing',
                __('Cannot send quote: contact email is missing.', 'cheapalarms'),
                ['status' => 400]
            );
        }
        if ($contactId === '') {
            return new WP_Error(
                'contact_missing',
                __('Cannot send quote: GHL contact ID is missing on this quote.', 'cheapalarms'),
                ['status' => 400]
            );
        }

        $total     = (float) ($quote['adjusted']['total'] ?? $quote['pricing']['total'] ?? 0);
        $subtotal  = (float) ($quote['adjusted']['subtotal'] ?? $quote['pricing']['subtotal'] ?? 0);
        $portalUrl = $this->resolvePortalUrlForEmail($quote, $estimateId);

        $contactName = (string) ($quote['contact']['name'] ?? 'Customer');
        $firstName   = (string) ($quote['contact']['firstName'] ?? '');
        $sent        = $this->portalService->sendEstimateEmail(
            $estimateId,
            [
                'email'     => $email,
                'id'        => $contactId,
                'name'      => $contactName,
                'firstName' => $firstName,
            ],
            $locationId,
            $portalUrl,
            null
        );

        if (is_wp_error($sent)) {
            return $sent;
        }

        $this->portalService->updateMeta($estimateId, [
            'itemsMeta' => $this->portalService->buildInitialItemsMeta([
                [
                    'name'          => $this->ghlLineItemName($quote),
                    'photoRequired' => false,
                    'isHeading'     => false,
                ],
            ]),
            'quote' => [
                'status'             => 'sent',
                'statusLabel'        => __('Sent', 'cheapalarms'),
                'sentAt'             => current_time('mysql'),
                'total'              => $total,
                'subtotal'           => $subtotal,
                'currency'           => 'AUD',
                'sendCount'          => $revision,
                'revision'           => $revision,
                'revisionNumber'     => $revision,
                'photos_required'    => false,
                'acceptance_enabled' => true,
                'approval_requested' => false,
            ],
            'workflow' => [
                'status'      => 'ready_to_accept',
                'currentStep' => 2,
            ],
        ]);

        return true;
    }

    /**
     * Prefer the stored invite URL (includes inviteToken). Never invent a token-less guest link.
     *
     * @param array<string, mixed> $quote
     */
    private function resolvePortalUrlForEmail(array $quote, string $estimateId): string
    {
        $stored = (string) ($quote['portalUrl'] ?? '');
        if ($stored !== '' && str_contains($stored, 'inviteToken=')) {
            return $stored;
        }

        $raw = get_option('ca_portal_meta_' . $estimateId, '{}');
        $meta = is_string($raw) ? json_decode($raw, true) : [];
        if (is_array($meta)) {
            $fromMeta = (string) ($meta['account']['portalUrl'] ?? '');
            if ($fromMeta !== '' && str_contains($fromMeta, 'inviteToken=')) {
                return $fromMeta;
            }
        }

        $frontendUrl = $this->config->getFrontendUrl();
        if ($frontendUrl === '') {
            return $stored;
        }

        // Logged-in customers can open the estimate without a guest token.
        return add_query_arg(
            ['estimateId' => $estimateId],
            trailingslashit($frontendUrl) . 'portal'
        );
    }

    /**
     * GHL / portal line name — must match createGhlEstimate().
     *
     * @param array<string, mixed> $quote
     */
    private function ghlLineItemName(array $quote): string
    {
        $jobType     = (($quote['jobType'] ?? '') === 'permanent') ? 'Permanent' : 'Ad hoc';
        $serviceType = sanitize_text_field((string) ($quote['serviceType'] ?? 'Security guard'));

        return mb_substr(sprintf('%s · %s', $jobType, $serviceType), 0, 80);
    }

    /**
     * @param array<string, mixed> $quote
     * @param array<string, mixed>|WP_Error|null $portalBootstrap
     */
    private function customerSubmitMessage(array $quote, string $email, mixed $portalBootstrap): string
    {
        if (!empty($quote['portalInvite'])) {
            return __('Thanks. Check your email for a portal invite — we will notify you when your quote is ready to view.', 'cheapalarms');
        }

        $createdUser = is_array($portalBootstrap) && !empty($portalBootstrap['createdUser']);
        $inviteFailed = is_array($portalBootstrap) && !empty($portalBootstrap['inviteEmailFailed']);
        $bootstrapFailed = is_wp_error($portalBootstrap) || !empty($quote['portalBootstrapFailed']);

        if ($createdUser || $inviteFailed || $bootstrapFailed) {
            return __('Thanks. We received your request and will notify you when your quote is ready in the portal.', 'cheapalarms');
        }

        if (email_exists($email)) {
            return __('Thanks. We received your request — log in to your portal to track it. We will notify you when your quote is ready.', 'cheapalarms');
        }

        return __('Thanks. We received your request and will notify you when your quote is ready in the portal.', 'cheapalarms');
    }

    /**
     * @deprecated Guarding quotes now use portal emails. Kept for reference only.
     * Send guide quote email via GHL Conversations (same channel as product estimates).
     * No portal invite / WP account.
     *
     * @param array<string, mixed> $quote
     * @return true|WP_Error
     */
    private function sendCustomerEmailViaGhl(array $quote, int $revision): true|WP_Error
    {
        $email = sanitize_email((string) ($quote['contact']['email'] ?? ''));
        if ($email === '' || !is_email($email)) {
            return new WP_Error(
                'email_missing',
                __('Cannot send quote: contact email is missing.', 'cheapalarms'),
                ['status' => 400]
            );
        }

        $contactId = (string) ($quote['ghlContactId'] ?? '');
        $locationId = (string) ($quote['locationId'] ?? $this->config->getLocationId());
        if ($contactId === '') {
            return new WP_Error(
                'contact_missing',
                __('Cannot send quote: GHL contact ID is missing on this quote.', 'cheapalarms'),
                ['status' => 400]
            );
        }
        if ($locationId === '') {
            return new WP_Error(
                'ghl_location_missing',
                __('Cannot send quote: GoHighLevel location is not configured.', 'cheapalarms'),
                ['status' => 500]
            );
        }

        $id      = (string) ($quote['id'] ?? '');
        $brand   = $this->config->getBrandName() ?: 'Safeguard';
        $subject = $revision > 1
            ? sprintf('%s: updated security guard quote (%s, update %d)', $brand, $id, $revision)
            : sprintf('%s: your security guard quote (%s)', $brand, $id);
        $html    = $this->buildCustomerQuoteEmailHtml($quote, $revision);

        $payload = [
            'contactId'  => $contactId,
            'type'       => 'Email',
            'status'     => 'pending',
            'subject'    => $subject,
            'html'       => $html,
            'emailFrom'  => $this->config->getEmailFromHeader(),
            'locationId' => $locationId,
        ];

        $result = $this->ghlClient->post('/conversations/messages', $payload, 30, $locationId);
        if (is_wp_error($result)) {
            $this->logger->warning('Guarding quote GHL email failed', [
                'quoteId'   => $id,
                'email'     => $email,
                'contactId' => $contactId,
                'error'     => $result->get_error_message(),
            ]);

            return new WP_Error(
                'email_failed',
                sprintf(
                    /* translators: %s: GHL error */
                    __('Could not send the quote email via GoHighLevel: %s', 'cheapalarms'),
                    $result->get_error_message()
                ),
                ['status' => 502]
            );
        }

        return true;
    }

    /**
     * Branded HTML email with a clear summary of what they requested.
     *
     * @param array<string, mixed> $quote
     */
    private function buildCustomerQuoteEmailHtml(array $quote, int $revision): string
    {
        $name      = (string) ($quote['contact']['name'] ?? 'there');
        $id        = (string) ($quote['id'] ?? '');
        $jobAdhoc  = ($quote['jobType'] ?? '') !== 'permanent';
        $jobLabel  = $jobAdhoc ? 'Ad hoc' : 'Permanent';
        $service   = (string) ($quote['serviceType'] ?? 'Security guard');
        $guards    = max(1, (int) ($quote['defaultGuards'] ?? 1));
        $start     = (string) ($quote['start'] ?? '');
        $end       = (string) ($quote['end'] ?? '');
        $shiftDate = (string) ($quote['shiftDate'] ?? '');
        $schedMode = (string) ($quote['schedMode'] ?? 'dates');
        $days      = is_array($quote['days'] ?? null) ? $quote['days'] : [];
        $phCover   = !empty($quote['phCover']);
        $auState   = (string) ($quote['auState'] ?? 'NSW');
        $months    = (int) ($quote['durationMonths'] ?? 0);
        $sites     = is_array($quote['sites'] ?? null) ? $quote['sites'] : [];
        $hours     = (float) ($quote['pricing']['manpowerHours'] ?? 0);
        $subtotal  = (float) ($quote['adjusted']['subtotal'] ?? $quote['pricing']['subtotal'] ?? 0);
        $gst       = (float) ($quote['adjusted']['gst'] ?? $quote['pricing']['gst'] ?? 0);
        $total     = (float) ($quote['adjusted']['total'] ?? $quote['pricing']['total'] ?? 0);
        $primary   = esc_attr(EmailTemplateHtmlHelper::getPrimaryColor($this->config));
        $support   = esc_html($this->config->getSupportName() ?: $this->config->getBrandName());
        $phoneLine = '1300 225 276';
        $isUpdate  = $revision > 1;

        $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $dayLabels = [];
        foreach ($days as $d) {
            $n = (int) $d;
            if ($n >= 0 && $n <= 6) {
                $dayLabels[] = $dayNames[$n];
            }
        }
        $scheduleLine = $schedMode === 'weekly'
            ? (implode(', ', $dayLabels !== [] ? $dayLabels : ['—']) . ' · ' . $start . '–' . $end)
            : (($shiftDate !== '' ? $shiftDate . ' · ' : '') . $start . '–' . $end);

        $row = static function (string $label, string $value) use ($primary): string {
            return '<tr>'
                . '<td style="padding:10px 0;border-bottom:1px solid #e8eef3;color:#5a6b77;font-size:13px;width:38%;vertical-align:top;">'
                . esc_html($label) . '</td>'
                . '<td style="padding:10px 0;border-bottom:1px solid #e8eef3;color:#1e2a33;font-size:14px;font-weight:600;vertical-align:top;">'
                . $value . '</td></tr>';
        };

        $siteRows = '';
        foreach ($sites as $i => $site) {
            if (!is_array($site)) {
                continue;
            }
            $addr = (string) ($site['address'] ?? '');
            if ($addr === '') {
                continue;
            }
            $g = max(1, (int) ($site['guards'] ?? $guards));
            $sStart = !empty($site['custom']) && ($site['start'] ?? '') !== '' ? (string) $site['start'] : $start;
            $sEnd   = !empty($site['custom']) && ($site['end'] ?? '') !== '' ? (string) $site['end'] : $end;
            $meta = $g . ' guard' . ($g === 1 ? '' : 's') . ' · ' . $sStart . '–' . $sEnd;
            if (!empty($site['custom'])) {
                $meta .= ' · custom times';
            }
            $siteRows .= '<tr><td colspan="2" style="padding:12px 0;border-bottom:1px solid #e8eef3;">'
                . '<div style="font-weight:700;color:#0c2d44;font-size:14px;">Site ' . ((int) $i + 1) . '</div>'
                . '<div style="color:#1e2a33;font-size:14px;margin-top:4px;">' . esc_html($addr) . '</div>'
                . '<div style="color:#5a6b77;font-size:12px;margin-top:4px;">' . esc_html($meta) . '</div>'
                . '</td></tr>';
        }

        $html  = EmailTemplateHtmlHelper::getEmailHeader($this->config);
        $html .= '<div style="background:#f4f8fb;padding:28px 20px;">';
        $html .= '<div style="background:#ffffff;border:1px solid #dce6ee;border-radius:12px;padding:28px 24px;max-width:560px;margin:0 auto;font-family:Arial,Helvetica,sans-serif;">';
        $html .= '<p style="margin:0 0 8px;font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:' . $primary . ';font-weight:700;">'
            . ($isUpdate ? 'Updated security guard quote' : 'Security guard quote') . '</p>';
        $html .= '<h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;color:#0c2d44;">Hi ' . esc_html($name) . ',</h1>';
        if ($isUpdate) {
            $html .= '<p style="margin:0 0 22px;font-size:15px;line-height:1.55;color:#5a6b77;">We\'ve updated your quote (update '
                . (int) $revision . '). Here is the latest summary and guide total including GST.</p>';
        } else {
            $html .= '<p style="margin:0 0 22px;font-size:15px;line-height:1.55;color:#5a6b77;">Thanks for your request. Here is a summary of what you asked for, plus your guide total including GST.</p>';
        }

        $html .= '<div style="background:#f4f8fb;border-radius:10px;padding:16px 18px;margin:0 0 22px;">';
        $html .= '<div style="font-size:12px;color:#5a6b77;margin-bottom:4px;">Total (incl. GST)</div>';
        $html .= '<div style="font-size:28px;font-weight:800;color:#0c2d44;letter-spacing:-0.02em;">AUD ' . esc_html(number_format($total, 2)) . '</div>';
        $html .= '<div style="font-size:12px;color:#5a6b77;margin-top:8px;">Ex GST AUD ' . esc_html(number_format($subtotal, 2))
            . ' · GST AUD ' . esc_html(number_format($gst, 2))
            . ' · Ref ' . esc_html($id)
            . ($isUpdate ? (' · Update ' . (int) $revision) : '')
            . '</div>';
        $html .= '</div>';

        $html .= '<h2 style="margin:0 0 10px;font-size:15px;color:#0c2d44;">Your request</h2>';
        $html .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:0 0 8px;">';
        $html .= $row('Engagement', esc_html($jobLabel));
        $html .= $row('Service', esc_html($service));
        if (!$jobAdhoc && $months > 0) {
            $html .= $row('Contract term', esc_html($months . ' months'));
        }
        $html .= $row('Guards (default)', esc_html((string) $guards));
        $html .= $row('Schedule', esc_html($scheduleLine));
        $html .= $row('Public holiday cover', esc_html($phCover ? ('Yes · ' . $auState) : 'No'));
        $html .= $row('Estimated hours', esc_html(number_format($hours, 1) . ' h'));
        $html .= '</table>';

        if ($siteRows !== '') {
            $html .= '<h2 style="margin:22px 0 6px;font-size:15px;color:#0c2d44;">Sites</h2>';
            $html .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">' . $siteRows . '</table>';
        }

        $html .= '<p style="margin:22px 0 0;font-size:14px;line-height:1.55;color:#5a6b77;">This is a guide quote. If anything needs adjusting, reply to this email or call '
            . esc_html($phoneLine) . ' and we will help.</p>';
        $html .= '<p style="margin:18px 0 0;font-size:14px;color:#1e2a33;">Thanks,<br /><strong>' . $support . '</strong></p>';
        $html .= '</div></div>';

        $brandName = esc_html($this->config->getBrandName());
        $html .= '<div style="background-color:#f5f5f5;padding:24px 20px;text-align:center;font-size:12px;color:#666666;font-family:Arial,sans-serif;">'
            . '<p style="margin:0 0 6px;"><strong style="color:#333;">' . $brandName . '</strong></p>'
            . '<p style="margin:0;">Questions? Call ' . esc_html($phoneLine) . '</p>'
            . '</div></div>';

        return $html;
    }

    /**
     * @param array<string, mixed> $quote
     */
    private function buildTimelineNote(array $quote): string
    {
        $lines = [
            'ManPower / guarding quote submitted (website calculator).',
            'Quote ID: ' . ($quote['id'] ?? ''),
            'GHL Estimate: ' . (($quote['ghlEstimateId'] ?? '') !== '' ? $quote['ghlEstimateId'] : '(pending/failed)'),
            'Type: ' . ($quote['jobType'] ?? ''),
            'Service: ' . ($quote['serviceType'] ?? ''),
            'Sites: ' . count($quote['sites'] ?? []),
            'Guide total (incl GST): AUD ' . number_format((float) ($quote['pricing']['total'] ?? 0), 2),
            'PH cover: ' . (!empty($quote['phCover']) ? 'yes (' . ($quote['auState'] ?? '') . ')' : 'no'),
        ];

        return implode("\n", $lines);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function splitName(string $name): array
    {
        $name = trim(wp_strip_all_tags($name));
        $parts = preg_split('/\s+/', $name, 2) ?: [];
        $first = $parts[0] ?? $name;
        $last  = $parts[1] ?? '';

        return [$first, $last];
    }

    /**
     * @param mixed $raw
     * @return list<array<string, mixed>>
     */
    private function normalizeSites($raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $site) {
            if (!is_array($site)) {
                continue;
            }
            $address = sanitize_text_field((string) ($site['address'] ?? ''));
            if ($address === '') {
                continue;
            }
            $out[] = [
                'id'      => sanitize_text_field((string) ($site['id'] ?? '')),
                'address' => $address,
                'custom'  => !empty($site['custom']),
                'guards'  => max(1, (int) ($site['guards'] ?? 1)),
                'start'   => sanitize_text_field((string) ($site['start'] ?? '')),
                'end'     => sanitize_text_field((string) ($site['end'] ?? '')),
                'date'    => sanitize_text_field((string) ($site['date'] ?? '')),
                'days'    => $this->normalizeDays($site['days'] ?? []),
            ];
        }

        return $out;
    }

    /**
     * Server-side NSW guide pricing (authoritative).
     *
     * @param array<string, mixed> $ctx
     * @return array{subtotal: float, gst: float, total: float, manpowerHours: float, weeks: int, currency: string}|WP_Error
     */
    private function calculatePricing(array $ctx): array|WP_Error
    {
        $rates = $this->normalizeRates($ctx['rates'] ?? null);
        $jobType        = (string) ($ctx['jobType'] ?? 'adhoc');
        $durationMonths = max(0, (int) ($ctx['durationMonths'] ?? 0));
        $schedMode      = (string) ($ctx['schedMode'] ?? 'dates');
        $defaultGuards  = max(1, (int) ($ctx['defaultGuards'] ?? 1));
        $start          = (string) ($ctx['start'] ?? '');
        $end            = (string) ($ctx['end'] ?? '');
        $shiftDate      = (string) ($ctx['shiftDate'] ?? '');
        /** @var list<int> $days */
        $days           = is_array($ctx['days'] ?? null) ? $ctx['days'] : [];
        /** @var list<array<string, mixed>> $sites */
        $sites          = is_array($ctx['sites'] ?? null) ? $ctx['sites'] : [];
        $phCover        = !empty($ctx['phCover']);
        $auState        = (string) ($ctx['auState'] ?? 'NSW');

        if ($sites === []) {
            return new WP_Error('missing_sites', __('Add at least one site address.', 'cheapalarms'), ['status' => 400]);
        }

        $weeks = $jobType === 'permanent'
            ? (int) max(1, (int) round(($durationMonths / 12) * 52))
            : 1;

        $manpowerHours = 0.0;
        $rosterHours   = 0.0;
        $subtotal      = 0.0;

        foreach ($sites as $site) {
            $guards = max(1, (int) ($site['guards'] ?? $defaultGuards));
            $sStart = !empty($site['custom']) && ($site['start'] ?? '') !== '' ? (string) $site['start'] : $start;
            $sEnd   = !empty($site['custom']) && ($site['end'] ?? '') !== '' ? (string) $site['end'] : $end;

            if ($schedMode === 'dates') {
                $date = !empty($site['custom']) && ($site['date'] ?? '') !== '' ? (string) $site['date'] : $shiftDate;
                $range = $this->buildShiftRange($date, $sStart, $sEnd);
                if ($range === null) {
                    return new WP_Error('invalid_schedule', __('Invalid shift date/time.', 'cheapalarms'), ['status' => 400]);
                }
                $cost = $this->costRange($range['start'], $range['end'], $guards, false, $rates);
                $manpowerHours += $cost['billHours'] * $guards * $weeks;
                $rosterHours += $cost['billHours'] * $weeks;
                $subtotal += $cost['amount'] * $weeks;
            } else {
                $siteDays = !empty($site['custom']) && !empty($site['days']) && is_array($site['days'])
                    ? $this->normalizeDays($site['days'])
                    : $days;
                if ($siteDays === []) {
                    return new WP_Error('missing_days', __('Select at least one day.', 'cheapalarms'), ['status' => 400]);
                }
                foreach ($siteDays as $day) {
                    $date = $this->nextDateForDay($shiftDate !== '' ? $shiftDate : gmdate('Y-m-d'), $day);
                    $range = $this->buildShiftRange($date, $sStart, $sEnd);
                    if ($range === null) {
                        return new WP_Error('invalid_schedule', __('Invalid shift date/time.', 'cheapalarms'), ['status' => 400]);
                    }
                    $cost = $this->costRange($range['start'], $range['end'], $guards, false, $rates);
                    $manpowerHours += $cost['billHours'] * $guards * $weeks;
                    $rosterHours += $cost['billHours'] * $weeks;
                    $subtotal += $cost['amount'] * $weeks;
                }
            }
        }

        if ($phCover && $jobType === 'permanent' && $schedMode === 'weekly') {
            $phDays = self::PH_DAYS_BY_STATE[$auState] ?? 11;
            $years  = $durationMonths / 12;
            $guardSum = 0;
            foreach ($sites as $site) {
                $guardSum += max(1, (int) ($site['guards'] ?? $defaultGuards));
            }
            $avgGuards   = $guardSum / max(1, count($sites));
            $sampleHours = max(self::MIN_HOURS, $this->spanHours($start, $end));
            $uplift      = $phDays * $years * $sampleHours * $avgGuards * $rates['publicHoliday'] * count($sites);
            $subtotal   += $uplift;
            $manpowerHours += $phDays * $years * $sampleHours * $avgGuards * count($sites);
        }

        // Permanent term discount (aggressive on longer contracts).
        $termDiscountPct = max(0.0, (float) ($ctx['termDiscountPct'] ?? 0));
        if ($jobType === 'permanent' && $termDiscountPct > 0) {
            $subtotal *= (1 - min(40.0, $termDiscountPct) / 100);
        }

        // Short-notice surcharge on client charge-out.
        if (!empty($ctx['shortNotice'])) {
            $pct = max(0.0, (float) ($ctx['shortNoticePct'] ?? self::DEFAULT_SHORT_NOTICE_PCT));
            $subtotal *= (1 + min(50.0, $pct) / 100);
        }

        // Event supervisor uplift ($/hr × roster hours for one supervisor).
        if (!empty($ctx['includeSupervisor'])) {
            $upliftHr = max(0.0, (float) ($ctx['supervisorUplift'] ?? self::DEFAULT_SUPERVISOR_UPLIFT));
            $subtotal += $rosterHours * $upliftHr;
        }

        $subtotal = $this->money($subtotal);
        $gst      = $this->money($subtotal * 0.1);

        return [
            'subtotal'         => $subtotal,
            'gst'              => $gst,
            'total'            => $this->money($subtotal + $gst),
            'manpowerHours'    => round($manpowerHours, 1),
            'weeks'            => $weeks,
            'currency'         => 'AUD',
            'termDiscountPct'  => $termDiscountPct,
            'shortNotice'      => !empty($ctx['shortNotice']),
            'includeSupervisor'=> !empty($ctx['includeSupervisor']),
        ];
    }

    /**
     * @return array{start: \DateTimeImmutable, end: \DateTimeImmutable}|null
     */
    private function buildShiftRange(string $baseDate, string $start, string $end): ?array
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $baseDate) || !preg_match('/^\d{2}:\d{2}$/', $start) || !preg_match('/^\d{2}:\d{2}$/', $end)) {
            return null;
        }

        try {
            $startDt = new \DateTimeImmutable($baseDate . 'T' . $start . ':00');
            $endDt   = new \DateTimeImmutable($baseDate . 'T' . $end . ':00');
        } catch (\Exception $e) {
            return null;
        }

        if ($endDt <= $startDt) {
            $endDt = $endDt->modify('+1 day');
        }

        return ['start' => $startDt, 'end' => $endDt];
    }

    /**
     * @param array<string, float> $rates
     * @return array{billHours: float, amount: float}
     */
    private function costRange(\DateTimeImmutable $startDt, \DateTimeImmutable $endDt, int $guards, bool $treatAsPH, array $rates): array
    {
        $rawHours   = max(0.0, ($endDt->getTimestamp() - $startDt->getTimestamp()) / 3600);
        $billHours  = max(self::MIN_HOURS, $rawHours);
        $priced     = 0.0;
        $cursorTs   = $startDt->getTimestamp();
        $priceEndTs = $cursorTs + (int) round($billHours * 3600);
        $tz         = $startDt->getTimezone();

        while ($cursorTs < $priceEndTs) {
            $nextTs = min($cursorTs + 1800, $priceEndTs);
            $h      = ($nextTs - $cursorTs) / 3600;
            $slice  = (new \DateTimeImmutable('@' . $cursorTs))->setTimezone($tz);
            $priced += $h * $this->rateForInstant($slice, $treatAsPH, $rates) * $guards;
            $cursorTs = $nextTs;
        }

        return [
            'billHours' => $billHours,
            'amount'    => $priced,
        ];
    }

    /**
     * @param array<string, float> $rates
     */
    private function rateForInstant(\DateTimeImmutable $d, bool $treatAsPH, array $rates): float
    {
        if ($treatAsPH) {
            return $rates['publicHoliday'];
        }

        $day  = (int) $d->format('w');
        $mins = ((int) $d->format('G')) * 60 + (int) $d->format('i');
        $night = $mins >= 18 * 60 || $mins < 6 * 60;

        if ($day === 0) {
            return $rates['sunday'];
        }
        if ($day === 6) {
            return $night ? $rates['saturdayIntoSunday'] : $rates['saturday'];
        }
        if ($day === 5 && $night) {
            return $rates['fridayNight'];
        }
        if ($night) {
            return $rates['weekdayNight'];
        }

        return $rates['weekdayDay'];
    }

    /**
     * @return array{
     *   adhoc: array<string, float>,
     *   permanent: array<string, float>,
     *   termDiscounts: array<string, float>,
     *   shortNoticePct: float,
     *   supervisorUplift: float
     * }
     */
    private function getGlobalRateCard(): array
    {
        $stored = get_option(self::OPTION_RATES, null);
        if (!is_array($stored)) {
            return [
                'adhoc'            => self::DEFAULT_ADHOC_RATES,
                'permanent'        => self::DEFAULT_PERMANENT_RATES,
                'termDiscounts'    => $this->normalizeTermDiscounts(self::DEFAULT_TERM_DISCOUNTS),
                'shortNoticePct'   => self::DEFAULT_SHORT_NOTICE_PCT,
                'supervisorUplift' => self::DEFAULT_SUPERVISOR_UPLIFT,
            ];
        }

        // Legacy flat rate card → treat as ad hoc.
        if (!isset($stored['adhoc']) && isset($stored['weekdayDay'])) {
            return [
                'adhoc'            => $this->normalizeRates($stored, self::DEFAULT_ADHOC_RATES),
                'permanent'        => self::DEFAULT_PERMANENT_RATES,
                'termDiscounts'    => $this->normalizeTermDiscounts(self::DEFAULT_TERM_DISCOUNTS),
                'shortNoticePct'   => self::DEFAULT_SHORT_NOTICE_PCT,
                'supervisorUplift' => self::DEFAULT_SUPERVISOR_UPLIFT,
            ];
        }

        return [
            'adhoc'            => $this->normalizeRates($stored['adhoc'] ?? null, self::DEFAULT_ADHOC_RATES),
            'permanent'        => $this->normalizeRates($stored['permanent'] ?? null, self::DEFAULT_PERMANENT_RATES),
            'termDiscounts'    => $this->normalizeTermDiscounts($stored['termDiscounts'] ?? self::DEFAULT_TERM_DISCOUNTS),
            'shortNoticePct'   => $this->clampPercent($stored['shortNoticePct'] ?? self::DEFAULT_SHORT_NOTICE_PCT, self::DEFAULT_SHORT_NOTICE_PCT),
            'supervisorUplift' => round(max(0, is_numeric($stored['supervisorUplift'] ?? null) ? (float) $stored['supervisorUplift'] : self::DEFAULT_SUPERVISOR_UPLIFT), 2),
        ];
    }

    /**
     * @return array<string, float>
     * @deprecated Prefer getGlobalRateCard()['adhoc']
     */
    private function getGlobalRates(): array
    {
        return $this->getGlobalRateCard()['adhoc'];
    }

    /**
     * @param mixed $raw
     * @param array<string, float>|null $defaults
     * @return array<string, float>
     */
    private function normalizeRates($raw, ?array $defaults = null): array
    {
        $defaults = $defaults ?? self::DEFAULT_ADHOC_RATES;
        $out = $defaults;
        if (!is_array($raw)) {
            return $out;
        }
        foreach ($defaults as $key => $fallback) {
            if (!array_key_exists($key, $raw)) {
                continue;
            }
            $n = is_numeric($raw[$key]) ? (float) $raw[$key] : $fallback;
            if ($n < 0 || !is_finite($n)) {
                $n = $fallback;
            }
            $out[$key] = round($n, 2);
        }

        return $out;
    }

    /**
     * @param mixed $raw
     * @return array<string, float>
     */
    private function normalizeTermDiscounts($raw): array
    {
        $out = [];
        foreach (self::DEFAULT_TERM_DISCOUNTS as $months => $fallback) {
            $key = (string) $months;
            $val = is_array($raw) && array_key_exists($key, $raw)
                ? $raw[$key]
                : (is_array($raw) && array_key_exists($months, $raw) ? $raw[$months] : $fallback);
            $out[$key] = $this->clampPercent($val, $fallback);
        }

        return $out;
    }

    private function clampPercent(mixed $val, float $fallback): float
    {
        $n = is_numeric($val) ? (float) $val : $fallback;
        if (!is_finite($n) || $n < 0) {
            $n = $fallback;
        }

        return round(min(40.0, $n), 2);
    }

    /**
     * @param array<string, float> $discounts
     */
    private function termDiscountPercent(int $months, array $discounts): float
    {
        $months = max(self::MIN_PERM_MONTHS, $months);
        $best = 0.0;
        foreach ($discounts as $tier => $pct) {
            $tierMonths = (int) $tier;
            if ($tierMonths > 0 && $months >= $tierMonths) {
                $best = max($best, (float) $pct);
            }
        }

        return $best;
    }

    /**
     * Spread total guards across sites (evenly; remainder on first sites).
     *
     * @param list<array<string, mixed>> $sites
     * @return list<array<string, mixed>>
     */
    private function applyTotalGuardsToSites(array $sites, int $totalGuards): array
    {
        $n = count($sites);
        if ($n === 0) {
            return $sites;
        }
        $totalGuards = max(1, $totalGuards);
        $base = intdiv($totalGuards, $n);
        $rem  = $totalGuards % $n;
        foreach ($sites as $i => &$site) {
            $site['guards'] = max(1, $base + ($i < $rem ? 1 : 0));
        }
        unset($site);

        return $sites;
    }

    private function nextDateForDay(string $fromDateStr, int $dayNum): string
    {
        try {
            $d = new \DateTimeImmutable($fromDateStr . 'T12:00:00');
        } catch (\Exception $e) {
            $d = new \DateTimeImmutable('today');
        }
        $delta = ($dayNum - (int) $d->format('w') + 7) % 7;

        return $d->modify('+' . $delta . ' days')->format('Y-m-d');
    }

    private function spanHours(string $start, string $end): float
    {
        if (!preg_match('/^\d{2}:\d{2}$/', $start) || !preg_match('/^\d{2}:\d{2}$/', $end)) {
            return self::MIN_HOURS;
        }
        [$sh, $sm] = array_map('intval', explode(':', $start));
        [$eh, $em] = array_map('intval', explode(':', $end));
        $mins = ($eh * 60 + $em) - ($sh * 60 + $sm);
        if ($mins <= 0) {
            $mins += 24 * 60;
        }

        return $mins / 60;
    }

    /**
     * @param mixed $raw
     * @return list<int>
     */
    private function normalizeDays($raw): array
    {
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $d) {
            $n = (int) $d;
            if ($n >= 0 && $n <= 6) {
                $out[] = $n;
            }
        }

        return array_values(array_unique($out));
    }

    private function money($value): float
    {
        $n = is_numeric($value) ? (float) $value : 0.0;
        if (is_nan($n) || $n < 0) {
            return 0.0;
        }

        return round($n, 2);
    }
}
