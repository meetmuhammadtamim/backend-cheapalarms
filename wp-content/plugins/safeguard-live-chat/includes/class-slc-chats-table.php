<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin list of website chats (loaded only on the Chats screen).
 */
class Slc_Chats_Table extends WP_List_Table
{
    private Slc_Store $store;

    public function __construct(Slc_Store $store)
    {
        parent::__construct([
            'singular' => 'chat',
            'plural'   => 'chats',
            'ajax'     => false,
        ]);
        $this->store = $store;
    }

    public function get_columns(): array
    {
        return [
            'cb'       => '<input type="checkbox" />',
            'visitor'  => __('Visitor', 'safeguard-live-chat'),
            'message'  => __('First message', 'safeguard-live-chat'),
            'status'   => __('Status', 'safeguard-live-chat'),
            'page'     => __('Page', 'safeguard-live-chat'),
            'messages' => __('Messages', 'safeguard-live-chat'),
            'updated'  => __('Last activity', 'safeguard-live-chat'),
        ];
    }

    protected function get_sortable_columns(): array
    {
        return [
            'visitor'  => ['visitor_name', false],
            'status'   => ['status', false],
            'messages' => ['message_count', false],
            'updated'  => ['updated_at', true],
        ];
    }

    protected function get_views(): array
    {
        $counts  = $this->store->statusCounts();
        $current = $this->currentStatus();
        $labels  = ['' => __('All', 'safeguard-live-chat')] + Slc_Admin::statusLabels();
        $views   = [];

        foreach ($labels as $status => $label) {
            $count = $status === '' ? $counts['all'] : ($counts[$status] ?? 0);
            if ($status !== '' && $count === 0) {
                continue;
            }
            $url   = Slc_Admin::pageUrl($status === '' ? [] : ['chat_status' => $status]);
            $class = $current === $status ? ' class="current"' : '';
            $views[$status === '' ? 'all' : $status] = '<a href="' . esc_url($url) . '"' . $class . '>'
                . esc_html($label) . ' <span class="count">(' . number_format_i18n($count) . ')</span></a>';
        }

        return $views;
    }

    protected function get_bulk_actions(): array
    {
        return ['delete' => __('Delete', 'safeguard-live-chat')];
    }

    public function process_bulk_action(): void
    {
        if ($this->current_action() !== 'delete') {
            return;
        }
        if (!current_user_can(Slc_Admin::CAP)) {
            return;
        }
        check_admin_referer('bulk-' . $this->_args['plural']);

        $ids = isset($_REQUEST['chat']) ? array_map('absint', (array) wp_unslash($_REQUEST['chat'])) : [];
        $deleted = $this->store->deleteConversations($ids);
        wp_safe_redirect(Slc_Admin::pageUrl(['deleted' => $deleted]));
        exit;
    }

    public function prepare_items(): void
    {
        $perPage = 20;
        $result  = $this->store->adminList([
            'status'   => $this->currentStatus(),
            'search'   => isset($_REQUEST['s']) ? sanitize_text_field(wp_unslash($_REQUEST['s'])) : '',
            'orderby'  => isset($_REQUEST['orderby']) ? sanitize_key(wp_unslash($_REQUEST['orderby'])) : 'updated_at',
            'order'    => isset($_REQUEST['order']) ? sanitize_key(wp_unslash($_REQUEST['order'])) : 'desc',
            'per_page' => $perPage,
            'paged'    => $this->get_pagenum(),
        ]);

        $this->_column_headers = [$this->get_columns(), [], $this->get_sortable_columns(), 'visitor'];
        $this->items = $result['rows'];
        $this->set_pagination_args([
            'total_items' => $result['total'],
            'per_page'    => $perPage,
            'total_pages' => (int) ceil($result['total'] / $perPage),
        ]);
    }

    public function no_items(): void
    {
        esc_html_e('No chats found.', 'safeguard-live-chat');
    }

    private function currentStatus(): string
    {
        $status = isset($_REQUEST['chat_status']) ? sanitize_key(wp_unslash($_REQUEST['chat_status'])) : '';

        return array_key_exists($status, Slc_Admin::statusLabels()) ? $status : '';
    }

    protected function column_cb($item): string
    {
        return '<input type="checkbox" name="chat[]" value="' . esc_attr((string) $item['id']) . '" />';
    }

    protected function column_visitor($item): string
    {
        $id    = (int) $item['id'];
        $name  = trim((string) $item['visitor_name']);
        $view  = Slc_Admin::pageUrl(['chat' => $id]);
        $title = $name !== '' ? $name : __('Visitor', 'safeguard-live-chat');

        $out = '<strong><a class="row-title" href="' . esc_url($view) . '">' . esc_html($title) . '</a></strong>';
        if ($item['visitor_email'] !== '') {
            $out .= '<br><a href="mailto:' . esc_attr((string) $item['visitor_email']) . '">' . esc_html((string) $item['visitor_email']) . '</a>';
        }
        if ($item['visitor_phone'] !== '') {
            $out .= '<br><span class="slc-muted">' . esc_html((string) $item['visitor_phone']) . '</span>';
        }

        $deleteUrl = wp_nonce_url(
            add_query_arg(['action' => 'slc_delete_chat', 'chat' => $id], admin_url('admin-post.php')),
            'slc_delete_chat_' . $id
        );
        $actions = [
            'view'   => '<a href="' . esc_url($view) . '">' . esc_html__('View', 'safeguard-live-chat') . '</a>',
            'delete' => '<a class="submitdelete" href="' . esc_url($deleteUrl) . '" onclick="return confirm(\''
                . esc_js(__('Delete this chat and all of its messages?', 'safeguard-live-chat')) . '\');">'
                . esc_html__('Delete', 'safeguard-live-chat') . '</a>',
        ];

        return $out . $this->row_actions($actions);
    }

    protected function column_message($item): string
    {
        $text = trim((string) ($item['first_message'] ?? ''));

        return '<span class="slc-snippet">' . esc_html(wp_trim_words($text, 22, '…')) . '</span>';
    }

    protected function column_status($item): string
    {
        return Slc_Admin::statusPill((string) $item['status']);
    }

    protected function column_page($item): string
    {
        $page = (string) ($item['page_path'] ?? '');

        return $page === '' ? '<span class="slc-muted">&mdash;</span>' : '<code>' . esc_html($page) . '</code>';
    }

    protected function column_messages($item): string
    {
        return esc_html((string) (int) $item['message_count']);
    }

    protected function column_updated($item): string
    {
        $time = Slc_Admin::formatTime((string) $item['updated_at']);
        $ago  = '';
        $ts   = strtotime((string) $item['updated_at']);
        if ($ts) {
            // Stored in site time; compare against the same clock.
            $now = strtotime(current_time('mysql'));
            $ago = '<br><span class="slc-muted">' . esc_html(sprintf(__('%s ago', 'safeguard-live-chat'), human_time_diff($ts, $now))) . '</span>';
        }

        return esc_html($time) . $ago;
    }

    protected function column_default($item, $column_name): string
    {
        return '';
    }
}
