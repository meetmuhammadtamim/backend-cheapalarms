<?php

namespace CheapAlarms\Plugin\REST\Controllers;

use CheapAlarms\Plugin\REST\Auth\Authenticator;
use CheapAlarms\Plugin\Services\Container;
use CheapAlarms\Plugin\Services\GuardingQuoteService;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

use function is_wp_error;
use function register_rest_route;
use function sanitize_text_field;

/**
 * Public guarding-quote submit + admin list/detail/update.
 * Creates portal access on submit (invite for new customers); admin send uses portal email.
 */
class GuardingQuoteController implements ControllerInterface
{
    public function __construct(private Container $container)
    {
    }

    public function register(): void
    {
        $auth = $this->container->get(Authenticator::class);

        register_rest_route('ca/v1', '/guarding-quotes', [
            'methods'             => 'POST',
            'permission_callback' => '__return_true',
            'callback'            => [$this, 'submit'],
        ]);

        register_rest_route('ca/v1', '/admin/guarding-rate-card', [
            [
                'methods'             => 'GET',
                'permission_callback' => fn () => $auth->requirePermission('estimates.view'),
                'callback'            => [$this, 'getRateCard'],
            ],
            [
                'methods'             => 'PUT',
                'permission_callback' => fn () => $auth->requirePermission('estimates.manage'),
                'callback'            => [$this, 'updateRateCard'],
            ],
        ]);

        register_rest_route('ca/v1', '/admin/guarding-quotes', [
            'methods'             => 'GET',
            'permission_callback' => fn () => $auth->requirePermission('estimates.view'),
            'callback'            => [$this, 'listQuotes'],
        ]);

        register_rest_route('ca/v1', '/admin/guarding-quotes/(?P<quoteId>[A-Za-z0-9_-]+)/send', [
            'methods'             => 'POST',
            'permission_callback' => fn () => $auth->requirePermission('estimates.manage'),
            'callback'            => [$this, 'sendQuote'],
        ]);

        register_rest_route('ca/v1', '/admin/guarding-quotes/(?P<quoteId>[A-Za-z0-9_-]+)', [
            [
                'methods'             => 'GET',
                'permission_callback' => fn () => $auth->requirePermission('estimates.view'),
                'callback'            => [$this, 'getQuote'],
            ],
            [
                'methods'             => 'PATCH',
                'permission_callback' => fn () => $auth->requirePermission('estimates.manage'),
                'callback'            => [$this, 'updateQuote'],
            ],
        ]);
    }

    public function getRateCard(WP_REST_Request $request): WP_REST_Response
    {
        $denied = $this->denyUnless('estimates.view');
        if ($denied) {
            return $denied;
        }

        $service = $this->container->get(GuardingQuoteService::class);

        return $this->respond($service->getRateCard());
    }

    public function updateRateCard(WP_REST_Request $request): WP_REST_Response
    {
        $denied = $this->denyUnless('estimates.manage');
        if ($denied) {
            return $denied;
        }

        $body = $request->get_json_params();
        if (!is_array($body)) {
            $body = [];
        }

        $service = $this->container->get(GuardingQuoteService::class);

        return $this->respond($service->updateRateCard($body));
    }

    public function sendQuote(WP_REST_Request $request): WP_REST_Response
    {
        $denied = $this->denyUnless('estimates.manage');
        if ($denied) {
            return $denied;
        }

        $quoteId = sanitize_text_field((string) $request->get_param('quoteId'));
        $service = $this->container->get(GuardingQuoteService::class);

        return $this->respond($service->sendQuoteToCustomer($quoteId));
    }

    public function submit(WP_REST_Request $request): WP_REST_Response
    {
        $auth = $this->container->get(Authenticator::class);
        $rate = $auth->enforceRateLimit('guarding_quote_public', 8, 600);
        if (is_wp_error($rate)) {
            return $this->respond($rate);
        }

        $body = $request->get_json_params();
        if (!is_array($body)) {
            $body = [];
        }

        $service = $this->container->get(GuardingQuoteService::class);

        return $this->respond($service->submit($body));
    }

    public function listQuotes(WP_REST_Request $request): WP_REST_Response
    {
        $denied = $this->denyUnless('estimates.view');
        if ($denied) {
            return $denied;
        }

        $page     = max(1, (int) $request->get_param('page'));
        $pageSize = max(1, (int) ($request->get_param('pageSize') ?: 25));
        $search   = sanitize_text_field((string) ($request->get_param('search') ?? ''));
        $status   = sanitize_text_field((string) ($request->get_param('status') ?? ''));

        $service = $this->container->get(GuardingQuoteService::class);

        return $this->respond($service->listQuotes($page, $pageSize, $search, $status));
    }

    public function getQuote(WP_REST_Request $request): WP_REST_Response
    {
        $denied = $this->denyUnless('estimates.view');
        if ($denied) {
            return $denied;
        }

        $quoteId = sanitize_text_field((string) $request->get_param('quoteId'));
        $service = $this->container->get(GuardingQuoteService::class);

        return $this->respond($service->getQuote($quoteId));
    }

    public function updateQuote(WP_REST_Request $request): WP_REST_Response
    {
        $denied = $this->denyUnless('estimates.manage');
        if ($denied) {
            return $denied;
        }

        $quoteId = sanitize_text_field((string) $request->get_param('quoteId'));
        $body    = $request->get_json_params();
        if (!is_array($body)) {
            $body = [];
        }

        $service = $this->container->get(GuardingQuoteService::class);

        return $this->respond($service->updateQuote($quoteId, $body));
    }

    private function denyUnless(string $permission): ?WP_REST_Response
    {
        $auth = $this->container->get(Authenticator::class);
        $check = $auth->requirePermission($permission);
        if (is_wp_error($check)) {
            return $this->respond($check);
        }

        return null;
    }

    /**
     * @param array<string, mixed>|WP_Error $result
     */
    private function respond($result): WP_REST_Response
    {
        if (is_wp_error($result)) {
            return $this->errorResponse($result);
        }

        if (!isset($result['ok'])) {
            $result['ok'] = true;
        }

        $response = new WP_REST_Response($result, 200);
        $this->addSecurityHeaders($response);

        return $response;
    }

    private function errorResponse(WP_Error $error): WP_REST_Response
    {
        $status  = (int) ($error->get_error_data()['status'] ?? 400);
        $code    = $error->get_error_code();
        $message = $error->get_error_message();

        $response = new WP_REST_Response([
            'ok'      => false,
            'error'   => $message,
            'err'     => $message,
            'message' => $message,
            'code'    => $code,
        ], $status > 0 ? $status : 400);

        if ($code === 'rate_limited') {
            $retry = $error->get_error_data()['retry_after'] ?? null;
            if ($retry !== null) {
                $response->data['retry_after'] = (int) $retry;
            }
        }

        $this->addSecurityHeaders($response);

        return $response;
    }

    private function addSecurityHeaders(WP_REST_Response $response): void
    {
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('X-Frame-Options', 'SAMEORIGIN');
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
