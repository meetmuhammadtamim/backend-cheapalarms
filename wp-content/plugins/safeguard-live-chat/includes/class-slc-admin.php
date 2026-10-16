<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Chats" screen in the WordPress dashboard: every conversation started from the
 * website chat, with the visitor's details and the full message history.
 *
 * - List: status views, search (name, email, phone, page, message text), sorting,
 *   paging, bulk delete and CSV export.
 * - Detail: visitor card and the transcript.
 */
class Slc_Admin
{
    public const SLUG = 'slc-chats';
    public const CAP  = 'manage_options';

    private static ?Slc_Store $store = null;

    public static function register(Slc_Store $store): void
    {
        self::$store = $store;

        add_action('admin_menu', [self::class, 'menu']);
        add_action('admin_enqueue_scripts', [self::class, 'assets']);
        add_action('admin_post_slc_delete_chat', [self::class, 'handleDelete']);
        add_action('admin_post_slc_export_chats', [self::class, 'handleExport']);
    }

    public static function store(): Slc_Store
    {
        return self::$store ?? new Slc_Store();
    }

    public static function menu(): void
    {
        $waiting = 0;
        try {
            $counts  = self::store()->statusCounts();
            $waiting = (int) ($counts[Slc_Store::WAITING] ?? 0);
        } catch (\Throwable $e) {
            $waiting = 0;
        }

        $bubble = $waiting > 0
            ? ' <span class="awaiting-mod count-' . $waiting . '"><span class="pending-count">' . number_format_i18n($waiting) . '</span></span>'
            : '';

        $hook = add_menu_page(
            __('Website Chats', 'safeguard-live-chat'),
            __('Chats', 'safeguard-live-chat') . $bubble,
            self::CAP,
            self::SLUG,
            [self::class, 'render'],
            'dashicons-format-chat',
            26
        );

        if (is_string($hook) && $hook !== '') {
            add_action('load-' . $hook, [self::class, 'earlyActions']);
        }
    }

    /**
     * Runs before the screen prints anything, so bulk deletes can redirect.
     */
    public static function earlyActions(): void
    {
        if (!current_user_can(self::CAP)) {
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
        require_once SLC_PLUGIN_PATH . 'includes/class-slc-chats-table.php';

        $table = new Slc_Chats_Table(self::store());
        $table->process_bulk_action();
    }

    public static function assets(string $hook): void
    {
        if ($hook !== 'toplevel_page_' . self::SLUG) {
            return;
        }

        wp_enqueue_style(
            'slc-admin',
            SLC_PLUGIN_URL . 'assets/css/admin.css',
            [],
            SLC_PLUGIN_VERSION
        );
    }

    /**
     * @return array<string, string> status => label
     */
    public static function statusLabels(): array
    {
        return [
            Slc_Store::WAITING   => __('Waiting for the team', 'safeguard-live-chat'),
            Slc_Store::ACTIVE    => __('In progress', 'safeguard-live-chat'),
            Slc_Store::RESOLVED  => __('Closed', 'safeguard-live-chat'),
            Slc_Store::TIMED_OUT => __('No reply (timed out)', 'safeguard-live-chat'),
        ];
    }

    public static function statusPill(string $status): string
    {
        $labels = self::statusLabels();
        $label  = $labels[$status] ?? $status;

        return '<span class="slc-pill slc-pill--' . esc_attr($status) . '">' . esc_html($label) . '</span>';
    }

    public static function formatTime(string $mysql): string
    {
        if ($mysql === '' || $mysql === '0000-00-00 00:00:00') {
            return '';
        }

        return mysql2date('j M Y, g:i a', $mysql);
    }

    public static function pageUrl(array $args = []): string
    {
        return add_query_arg(array_merge(['page' => self::SLUG], $args), admin_url('admin.php'));
    }

    public static function render(): void
    {
        if (!current_user_can(self::CAP)) {
            wp_die(esc_html__('You do not have permission to view this page.', 'safeguard-live-chat'));
        }

        self::store()->ensureTables();

        echo '<div class="wrap slc-admin">';

        $chatId = isset($_GET['chat']) ? absint(wp_unslash($_GET['chat'])) : 0;
        if ($chatId > 0) {
            self::renderDetail($chatId);
        } else {
            self::renderList();
        }

        echo '</div>';
    }

    private static function renderList(): void
    {
        require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
        require_once SLC_PLUGIN_PATH . 'includes/class-slc-chats-table.php';

        $table = new Slc_Chats_Table(self::store());
        $table->prepare_items();

        $exportUrl = wp_nonce_url(
            add_query_arg(
                [
                    'action'        => 'slc_export_chats',
                    'chat_status'   => isset($_GET['chat_status']) ? sanitize_key(wp_unslash($_GET['chat_status'])) : '',
                    's'             => isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '',
                ],
                admin_url('admin-post.php')
            ),
            'slc_export_chats'
        );

        echo '<h1 class="wp-heading-inline">' . esc_html__('Website Chats', 'safeguard-live-chat') . '</h1>';
        echo ' <a class="page-title-action" href="' . esc_url($exportUrl) . '">' . esc_html__('Export CSV', 'safeguard-live-chat') . '</a>';
        echo '<hr class="wp-header-end">';

        if (isset($_GET['deleted'])) {
            $count = absint(wp_unslash($_GET['deleted']));
            printf(
                '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
                esc_html(sprintf(_n('%d chat deleted.', '%d chats deleted.', $count, 'safeguard-live-chat'), $count))
            );
        }

        $table->views();
        echo '<form method="get">';
        echo '<input type="hidden" name="page" value="' . esc_attr(self::SLUG) . '">';
        if (isset($_GET['chat_status'])) {
            echo '<input type="hidden" name="chat_status" value="' . esc_attr(sanitize_key(wp_unslash($_GET['chat_status']))) . '">';
        }
        $table->search_box(__('Search chats', 'safeguard-live-chat'), 'slc-chat');
        $table->display();
        echo '</form>';
    }

    private static function renderDetail(int $id): void
    {
        $store = self::store();
        $chat  = $store->findById($id);

        echo '<p><a class="slc-back" href="' . esc_url(self::pageUrl()) . '">&larr; ' . esc_html__('All chats', 'safeguard-live-chat') . '</a></p>';

        if ($chat === null) {
            echo '<div class="notice notice-error"><p>' . esc_html__('That chat could not be found. It may have been deleted.', 'safeguard-live-chat') . '</p></div>';

            return;
        }

        $messages = $store->conversationMessages($id);
        $meta     = json_decode((string) ($chat['meta_json'] ?? ''), true);
        $meta     = is_array($meta) ? $meta : [];
        $tg       = is_array($meta['telegram'] ?? null) ? $meta['telegram'] : [];
        $agent    = trim((string) ($tg['claimedByName'] ?? ''));
        $ghl      = trim((string) ($meta['ghlContactId'] ?? ''));

        $name  = (string) $chat['visitor_name'];
        $email = (string) $chat['visitor_email'];
        $phone = (string) $chat['visitor_phone'];
        $page  = (string) ($chat['page_path'] ?? '');

        $deleteUrl = wp_nonce_url(
            add_query_arg(['action' => 'slc_delete_chat', 'chat' => $id], admin_url('admin-post.php')),
            'slc_delete_chat_' . $id
        );

        echo '<h1 class="wp-heading-inline">' . esc_html(sprintf(__('Chat #%d', 'safeguard-live-chat'), $id)) . '</h1>';
        echo ' ' . self::statusPill((string) $chat['status']);
        echo '<hr class="wp-header-end">';

        echo '<div class="slc-detail">';

        // Visitor card.
        echo '<aside class="slc-card slc-visitor">';
        echo '<h2>' . esc_html__('Visitor', 'safeguard-live-chat') . '</h2>';
        echo '<dl>';
        self::dlRow(__('Name', 'safeguard-live-chat'), $name !== '' ? esc_html($name) : '');
        self::dlRow(__('Email', 'safeguard-live-chat'), $email !== '' ? '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>' : '');
        self::dlRow(__('Phone', 'safeguard-live-chat'), $phone !== '' ? '<a href="tel:' . esc_attr(preg_replace('/[^0-9+]/', '', $phone)) . '">' . esc_html($phone) . '</a>' : '');
        self::dlRow(
            __('Started on page', 'safeguard-live-chat'),
            $page !== '' ? '<a href="' . esc_url(home_url($page)) . '" target="_blank" rel="noopener">' . esc_html($page) . '</a>' : ''
        );
        self::dlRow(__('Started', 'safeguard-live-chat'), esc_html(self::formatTime((string) $chat['created_at'])));
        self::dlRow(__('Last activity', 'safeguard-live-chat'), esc_html(self::formatTime((string) $chat['updated_at'])));
        self::dlRow(__('Messages', 'safeguard-live-chat'), esc_html((string) count($messages)));
        self::dlRow(__('Handled by', 'safeguard-live-chat'), $agent !== '' ? esc_html($agent) : '');
        if ($ghl !== '') {
            self::dlRow(__('CRM contact ID', 'safeguard-live-chat'), '<code>' . esc_html($ghl) . '</code>');
        }
        echo '</dl>';
        echo '<p class="slc-visitor__actions">';
        if ($email !== '') {
            echo '<a class="button button-primary" href="mailto:' . esc_attr($email) . '">' . esc_html__('Email visitor', 'safeguard-live-chat') . '</a> ';
        }
        echo '<a class="button button-link-delete" href="' . esc_url($deleteUrl) . '" onclick="return confirm(\''
            . esc_js(__('Delete this chat and all of its messages? This cannot be undone.', 'safeguard-live-chat')) . '\');">'
            . esc_html__('Delete chat', 'safeguard-live-chat') . '</a>';
        echo '</p>';
        echo '</aside>';

        // Transcript.
        echo '<section class="slc-card slc-transcript">';
        echo '<h2>' . esc_html__('Conversation', 'safeguard-live-chat') . '</h2>';
        if ($messages === []) {
            echo '<p class="slc-empty">' . esc_html__('No messages were stored for this chat.', 'safeguard-live-chat') . '</p>';
        }
        foreach ($messages as $message) {
            $role  = (string) $message['role'];
            $class = $role === 'user' ? 'visitor' : ($role === 'agent' ? 'team' : 'system');
            $who   = $role === 'user'
                ? ($name !== '' ? $name : __('Visitor', 'safeguard-live-chat'))
                : ($role === 'agent' ? ($agent !== '' ? $agent : __('Team', 'safeguard-live-chat')) : '');
            echo '<div class="slc-msg slc-msg--' . esc_attr($class) . '">';
            if ($who !== '') {
                echo '<span class="slc-msg__who">' . esc_html($who) . '</span>';
            }
            echo '<div class="slc-msg__bubble">' . nl2br(esc_html((string) $message['content'])) . '</div>';
            echo '<time class="slc-msg__time">' . esc_html(self::formatTime((string) $message['created_at'])) . '</time>';
            echo '</div>';
        }
        echo '</section>';

        echo '</div>';
    }

    private static function dlRow(string $label, string $valueHtml): void
    {
        echo '<dt>' . esc_html($label) . '</dt><dd>' . ($valueHtml !== '' ? $valueHtml : '<span class="slc-muted">&mdash;</span>') . '</dd>';
    }

    public static function handleDelete(): void
    {
        $id = isset($_GET['chat']) ? absint(wp_unslash($_GET['chat'])) : 0;
        if (!current_user_can(self::CAP) || $id < 1) {
            wp_die(esc_html__('You do not have permission to do that.', 'safeguard-live-chat'), '', ['response' => 403]);
        }
        check_admin_referer('slc_delete_chat_' . $id);

        $deleted = self::store()->deleteConversations([$id]);
        wp_safe_redirect(self::pageUrl(['deleted' => $deleted]));
        exit;
    }

    public static function handleExport(): void
    {
        if (!current_user_can(self::CAP)) {
            wp_die(esc_html__('You do not have permission to do that.', 'safeguard-live-chat'), '', ['response' => 403]);
        }
        check_admin_referer('slc_export_chats');

        $status = isset($_GET['chat_status']) ? sanitize_key(wp_unslash($_GET['chat_status'])) : '';
        $search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
        $store  = self::store();
        $labels = self::statusLabels();

        nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="website-chats-' . gmdate('Y-m-d') . '.csv"');

        $out = fopen('php://output', 'w');
        // UTF-8 byte order mark so Excel reads accents correctly.
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['ID', 'Started', 'Last activity', 'Status', 'Name', 'Email', 'Phone', 'Page', 'Messages', 'Conversation']);

        $page = 1;
        do {
            $batch = $store->adminList([
                'status'   => $status,
                'search'   => $search,
                'orderby'  => 'created_at',
                'order'    => 'DESC',
                'per_page' => 100,
                'paged'    => $page,
            ]);
            foreach ($batch['rows'] as $row) {
                $lines = [];
                foreach ($store->conversationMessages((int) $row['id']) as $message) {
                    $who     = $message['role'] === 'user' ? 'Visitor' : ($message['role'] === 'agent' ? 'Team' : 'System');
                    $lines[] = '[' . $message['created_at'] . '] ' . $who . ': ' . $message['content'];
                }
                fputcsv($out, array_map([self::class, 'csvCell'], [
                    (string) $row['id'],
                    (string) $row['created_at'],
                    (string) $row['updated_at'],
                    $labels[(string) $row['status']] ?? (string) $row['status'],
                    (string) $row['visitor_name'],
                    (string) $row['visitor_email'],
                    (string) $row['visitor_phone'],
                    (string) ($row['page_path'] ?? ''),
                    (string) $row['message_count'],
                    implode("\n", $lines),
                ]));
            }
            $page++;
        } while (count($batch['rows']) === 100);

        fclose($out);
        exit;
    }

    /**
     * Stop spreadsheet apps from running a visitor's text as a formula.
     */
    public static function csvCell(string $value): string
    {
        // A plain phone number such as +61 400 000 000 cannot run as a formula.
        if ($value !== '' && preg_match('/^[+-]?[0-9][0-9 ()-]*$/', $value) === 1) {
            return $value;
        }
        if ($value !== '' && strpos("=+-@\t\r", $value[0]) !== false) {
            return "'" . $value;
        }

        return $value;
    }
}
