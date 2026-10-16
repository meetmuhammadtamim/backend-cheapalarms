<?php

if (!defined('ABSPATH')) {
    exit;
}

class Slc_Store
{
    public const WAITING = 'waiting_agent';
    public const ACTIVE  = 'agent_active';
    public const RESOLVED = 'resolved';
    public const TIMED_OUT = 'timed_out';

    public function install(): void
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset = $wpdb->get_charset_collate();
        $conv = $this->conversationsTable();
        $msg  = $this->messagesTable();

        dbDelta("CREATE TABLE {$conv} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            session_key VARCHAR(64) NOT NULL,
            status VARCHAR(32) NOT NULL DEFAULT 'waiting_agent',
            visitor_name VARCHAR(120) NOT NULL DEFAULT '',
            visitor_email VARCHAR(190) NOT NULL DEFAULT '',
            visitor_phone VARCHAR(40) NOT NULL DEFAULT '',
            page_path VARCHAR(255) NULL,
            message_count INT UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            last_user_message_at DATETIME NULL,
            meta_json LONGTEXT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY session_key (session_key),
            KEY idx_status_updated (status, updated_at)
        ) {$charset};");

        dbDelta("CREATE TABLE {$msg} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            conversation_id BIGINT(20) UNSIGNED NOT NULL,
            role VARCHAR(16) NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY idx_conversation_created (conversation_id, created_at)
        ) {$charset};");
    }

    /**
     * @param array{name:string,email:string,phone:string,page?:string} $visitor
     * @return array<string, mixed>|\WP_Error
     */
    public function start(array $visitor, string $message)
    {
        $this->install();
        $message = trim(wp_strip_all_tags($message));
        if ($message === '') {
            return new WP_Error('empty_message', 'Message cannot be empty.', ['status' => 400]);
        }

        global $wpdb;
        $now = current_time('mysql');
        $session = bin2hex(random_bytes(16));
        $inserted = $wpdb->insert($this->conversationsTable(), [
            'session_key'          => $session,
            'status'               => self::WAITING,
            'visitor_name'         => $visitor['name'],
            'visitor_email'        => $visitor['email'],
            'visitor_phone'        => $visitor['phone'],
            'page_path'            => isset($visitor['page']) ? substr($visitor['page'], 0, 255) : '',
            'message_count'        => 0,
            'created_at'           => $now,
            'updated_at'           => $now,
            'last_user_message_at' => $now,
            'meta_json'            => wp_json_encode(['telegram' => []]),
        ]);
        if ($inserted !== 1) {
            return new WP_Error('db_error', 'Could not start the chat.', ['status' => 500]);
        }

        $id = (int) $wpdb->insert_id;
        $messageId = $this->appendMessage($id, 'user', $message);
        if ($messageId < 1) {
            return new WP_Error('db_error', 'Could not store the message.', ['status' => 500]);
        }

        $row = $this->findById($id);
        if ($row === null) {
            return new WP_Error('db_error', 'Could not start the chat.', ['status' => 500]);
        }

        return $row;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findBySession(string $session): ?array
    {
        $session = $this->normalizeSession($session);
        if ($session === '') {
            return null;
        }
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare('SELECT * FROM ' . $this->conversationsTable() . ' WHERE session_key = %s', $session),
            ARRAY_A
        );

        return is_array($row) ? $row : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        if ($id < 1) {
            return null;
        }
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare('SELECT * FROM ' . $this->conversationsTable() . ' WHERE id = %d', $id),
            ARRAY_A
        );

        return is_array($row) ? $row : null;
    }

    /**
     * @return array<string, mixed>|\WP_Error
     */
    public function addVisitorMessage(string $session, string $message)
    {
        $row = $this->findBySession($session);
        if ($row === null) {
            return new WP_Error('not_found', 'Chat not found.', ['status' => 404]);
        }
        $this->expireOneIfNeeded($row);
        $row = $this->findById((int) $row['id']) ?? $row;
        $status = (string) $row['status'];
        if (!in_array($status, [self::WAITING, self::ACTIVE], true)) {
            return new WP_Error('closed', 'This chat is closed.', ['status' => 400]);
        }
        $message = trim(wp_strip_all_tags($message));
        $messageId = $this->appendMessage((int) $row['id'], 'user', $message);
        if ($messageId < 1) {
            return new WP_Error('empty_message', 'Message cannot be empty.', ['status' => 400]);
        }
        $this->touch((int) $row['id'], true);

        return $this->findById((int) $row['id']) ?? $row;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function messagesAfter(int $conversationId, int $afterId): array
    {
        global $wpdb;
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT id, role, content, created_at FROM ' . $this->messagesTable() . ' WHERE conversation_id = %d AND id > %d ORDER BY id ASC',
                $conversationId,
                $afterId
            ),
            ARRAY_A
        );

        return is_array($rows) ? $rows : [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function recentMessages(int $conversationId, int $limit = 8): array
    {
        global $wpdb;
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT id, role, content, created_at FROM ' . $this->messagesTable() . ' WHERE conversation_id = %d ORDER BY id DESC LIMIT %d',
                $conversationId,
                $limit
            ),
            ARRAY_A
        );
        if (!is_array($rows)) {
            return [];
        }

        return array_reverse($rows);
    }

    /**
     * @param array{id:int,name:string,username:string} $agent
     * @return array<string, mixed>|\WP_Error
     */
    public function claim(int $conversationId, array $agent)
    {
        global $wpdb;
        $lock = 'slc_claim_' . $conversationId;
        $got = (int) $wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s, 5)', $lock));
        if ($got !== 1) {
            return new WP_Error('claim_busy', 'Another agent is claiming this chat.');
        }

        try {
            $row = $this->findById($conversationId);
            if ($row === null) {
                return new WP_Error('not_found', 'Chat not found.');
            }
            $this->expireOneIfNeeded($row);
            $row = $this->findById($conversationId) ?? $row;
            $status = (string) $row['status'];
            $meta = $this->meta($row);
            $tg = is_array($meta['telegram'] ?? null) ? $meta['telegram'] : [];
            $existing = (int) ($tg['claimedById'] ?? 0);
            $agentId = (int) $agent['id'];

            if ($status === self::ACTIVE && $existing > 0 && $existing !== $agentId) {
                return new WP_Error('already_claimed', 'This chat is already claimed by another agent.');
            }
            if (!in_array($status, [self::WAITING, self::ACTIVE], true)) {
                return new WP_Error(
                    'not_waiting',
                    $status === self::TIMED_OUT ? 'This chat timed out before an agent claimed it.' : 'This chat is not waiting.'
                );
            }

            $tg['claimedById'] = $agentId;
            $tg['claimedByName'] = $agent['name'];
            $tg['claimedByUsername'] = $agent['username'];
            $tg['agentDmChatId'] = (string) $agentId;
            $meta['telegram'] = $tg;
            $this->update($conversationId, [
                'status'    => self::ACTIVE,
                'meta_json' => wp_json_encode($meta),
            ]);
            if ($existing !== $agentId) {
                $label = trim((string) ($agent['name'] ?? ''));
                $this->appendMessage($conversationId, 'system', $label !== '' ? $label . ' joined.' : 'Joined the chat.');
            }
            $this->bindAgent($agentId, $conversationId);

            return $this->findById($conversationId) ?? $row;
        } finally {
            $wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)', $lock));
        }
    }

    /**
     * @return true|\WP_Error
     */
    public function addAgentMessage(int $conversationId, int $agentId, string $text)
    {
        $row = $this->findById($conversationId);
        if ($row === null) {
            return new WP_Error('not_found', 'Chat not found.');
        }
        if ((string) $row['status'] !== self::ACTIVE) {
            return new WP_Error('not_active', 'Claim this chat before sending a reply.');
        }
        $tg = $this->telegramMeta($row);
        $claimed = (int) ($tg['claimedById'] ?? 0);
        if ($claimed > 0 && $claimed !== $agentId) {
            return new WP_Error('forbidden', 'Only the claiming agent can reply.');
        }
        $id = $this->appendMessage($conversationId, 'agent', $text);
        if ($id < 1) {
            return new WP_Error('empty_message', 'Message cannot be empty.');
        }
        $this->touch($conversationId, false);

        return true;
    }

    public function resolve(int $conversationId, int $agentId): bool
    {
        $row = $this->findById($conversationId);
        if ($row === null || (string) $row['status'] !== self::ACTIVE) {
            return false;
        }
        $tg = $this->telegramMeta($row);
        if ((int) ($tg['claimedById'] ?? 0) !== $agentId) {
            return false;
        }
        $this->update($conversationId, ['status' => self::RESOLVED]);
        $this->appendMessage($conversationId, 'system', 'Chat closed by the team.');
        $this->unbindAgent($agentId, $conversationId);

        return true;
    }

    public function rememberTelegramMessage(int $conversationId, int $messageId): void
    {
        if ($conversationId < 1 || $messageId < 1) {
            return;
        }
        $row = $this->findById($conversationId);
        if ($row === null) {
            return;
        }
        $meta = $this->meta($row);
        $tg = is_array($meta['telegram'] ?? null) ? $meta['telegram'] : [];
        $ids = is_array($tg['messageIds'] ?? null) ? $tg['messageIds'] : [];
        $ids[] = $messageId;
        $tg['messageIds'] = array_values(array_unique(array_map('intval', $ids)));
        if (empty($tg['alertMessageId'])) {
            $tg['alertMessageId'] = $messageId;
        }
        $meta['telegram'] = $tg;
        $this->update($conversationId, ['meta_json' => wp_json_encode($meta)]);
    }

    public function attachGhlContact(int $conversationId, string $contactId, bool $existing): void
    {
        if ($conversationId < 1 || $contactId === '') {
            return;
        }
        $row = $this->findById($conversationId);
        if ($row === null) {
            return;
        }
        $meta = $this->meta($row);
        $meta['ghlContactId'] = $contactId;
        $meta['ghlExisting'] = $existing;
        $this->update($conversationId, ['meta_json' => wp_json_encode($meta)]);
    }

    public function findByTelegramMessage(int $messageId): int
    {
        if ($messageId < 1) {
            return 0;
        }
        global $wpdb;
        $like = '%"messageIds":%' . $messageId . '%';
        $id = $wpdb->get_var($wpdb->prepare(
            'SELECT id FROM ' . $this->conversationsTable() . ' WHERE meta_json LIKE %s ORDER BY id DESC LIMIT 1',
            $like
        ));

        return (int) $id;
    }

    public function activeForAgent(int $agentId): int
    {
        $map = get_option('slc_agent_chats', []);
        if (!is_array($map)) {
            return 0;
        }

        return (int) ($map[(string) $agentId] ?? 0);
    }

    public function expireWaiting(int $minutes): void
    {
        global $wpdb;
        $cutoff = wp_date('Y-m-d H:i:s', time() - ($minutes * 60));
        $ids = $wpdb->get_col($wpdb->prepare(
            'SELECT id FROM ' . $this->conversationsTable() . ' WHERE status = %s AND last_user_message_at < %s',
            self::WAITING,
            $cutoff
        ));
        if (!is_array($ids)) {
            return;
        }
        foreach ($ids as $id) {
            $this->update((int) $id, ['status' => self::TIMED_OUT]);
            $this->appendMessage((int) $id, 'system', 'No one was free to take this chat. Please call 1300 225 276.');
        }
    }

    /**
     * Create the tables on first use (for the admin screens, before any chat exists).
     */
    public function ensureTables(): void
    {
        static $checked = false;
        if ($checked) {
            return;
        }
        $checked = true;

        global $wpdb;
        $found = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($this->conversationsTable())));
        if ($found === null || $found === '') {
            $this->install();
        }
    }

    /**
     * Conversations for the admin list, newest activity first by default.
     *
     * @param array{status?:string,search?:string,orderby?:string,order?:string,per_page?:int,paged?:int} $args
     * @return array{rows: array<int, array<string, mixed>>, total: int}
     */
    public function adminList(array $args): array
    {
        global $wpdb;
        $this->ensureTables();

        $conv = $this->conversationsTable();
        $msg  = $this->messagesTable();

        $where  = ['1=1'];
        $params = [];

        $status = (string) ($args['status'] ?? '');
        if (in_array($status, [self::WAITING, self::ACTIVE, self::RESOLVED, self::TIMED_OUT], true)) {
            $where[]  = 'c.status = %s';
            $params[] = $status;
        }

        $search = trim((string) ($args['search'] ?? ''));
        if ($search !== '') {
            $like     = '%' . $wpdb->esc_like($search) . '%';
            $where[]  = '(c.visitor_name LIKE %s OR c.visitor_email LIKE %s OR c.visitor_phone LIKE %s OR c.page_path LIKE %s'
                . ' OR EXISTS (SELECT 1 FROM ' . $msg . ' sm WHERE sm.conversation_id = c.id AND sm.content LIKE %s))';
            array_push($params, $like, $like, $like, $like, $like);
        }

        $whereSql = implode(' AND ', $where);

        $allowed = ['id', 'visitor_name', 'status', 'message_count', 'created_at', 'updated_at'];
        $orderby = in_array((string) ($args['orderby'] ?? ''), $allowed, true) ? (string) $args['orderby'] : 'updated_at';
        $order   = strtoupper((string) ($args['order'] ?? 'DESC')) === 'ASC' ? 'ASC' : 'DESC';

        $perPage = max(1, min(200, (int) ($args['per_page'] ?? 20)));
        $paged   = max(1, (int) ($args['paged'] ?? 1));
        $offset  = ($paged - 1) * $perPage;

        $countSql = "SELECT COUNT(*) FROM {$conv} c WHERE {$whereSql}";
        $total    = (int) ($params ? $wpdb->get_var($wpdb->prepare($countSql, $params)) : $wpdb->get_var($countSql));

        $listSql = "SELECT c.*, (SELECT m.content FROM {$msg} m WHERE m.conversation_id = c.id AND m.role = 'user' ORDER BY m.id ASC LIMIT 1) AS first_message"
            . " FROM {$conv} c WHERE {$whereSql} ORDER BY c.{$orderby} {$order}, c.id DESC LIMIT %d OFFSET %d";
        $listParams = array_merge($params, [$perPage, $offset]);
        $rows = $wpdb->get_results($wpdb->prepare($listSql, $listParams), ARRAY_A);

        return [
            'rows'  => is_array($rows) ? $rows : [],
            'total' => $total,
        ];
    }

    /**
     * Conversation counts per status, plus an "all" total.
     *
     * @return array<string, int>
     */
    public function statusCounts(): array
    {
        global $wpdb;
        $this->ensureTables();

        $counts = ['all' => 0, self::WAITING => 0, self::ACTIVE => 0, self::RESOLVED => 0, self::TIMED_OUT => 0];
        $rows = $wpdb->get_results('SELECT status, COUNT(*) AS n FROM ' . $this->conversationsTable() . ' GROUP BY status', ARRAY_A);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $n = (int) $row['n'];
                $counts['all'] += $n;
                if (isset($counts[(string) $row['status']])) {
                    $counts[(string) $row['status']] = $n;
                }
            }
        }

        return $counts;
    }

    /**
     * Every message in a conversation, oldest first.
     *
     * @return array<int, array<string, mixed>>
     */
    public function conversationMessages(int $conversationId): array
    {
        global $wpdb;
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT id, role, content, created_at FROM ' . $this->messagesTable() . ' WHERE conversation_id = %d ORDER BY id ASC',
                $conversationId
            ),
            ARRAY_A
        );

        return is_array($rows) ? $rows : [];
    }

    /**
     * Delete conversations and their messages.
     *
     * @param array<int, int> $ids
     */
    public function deleteConversations(array $ids): int
    {
        global $wpdb;
        $ids = array_values(array_filter(array_map('intval', $ids), static fn(int $id): bool => $id > 0));
        if ($ids === []) {
            return 0;
        }

        $deleted = 0;
        foreach ($ids as $id) {
            $wpdb->delete($this->messagesTable(), ['conversation_id' => $id]);
            $deleted += (int) $wpdb->delete($this->conversationsTable(), ['id' => $id]);
        }

        return $deleted;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function visitorPayload(array $row): array
    {
        return [
            'name'  => (string) ($row['visitor_name'] ?? ''),
            'email' => (string) ($row['visitor_email'] ?? ''),
            'phone' => (string) ($row['visitor_phone'] ?? ''),
            'page'  => (string) ($row['page_path'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    public function telegramMeta(array $row): array
    {
        $meta = $this->meta($row);
        $tg = $meta['telegram'] ?? [];

        return is_array($tg) ? $tg : [];
    }

    private function expireOneIfNeeded(array $row): void
    {
        if ((string) ($row['status'] ?? '') !== self::WAITING) {
            return;
        }
        $last = strtotime((string) ($row['last_user_message_at'] ?? ''));
        if ($last && $last < (time() - 600)) {
            $this->update((int) $row['id'], ['status' => self::TIMED_OUT]);
        }
    }

    private function bindAgent(int $agentId, int $conversationId): void
    {
        $map = get_option('slc_agent_chats', []);
        if (!is_array($map)) {
            $map = [];
        }
        $map[(string) $agentId] = $conversationId;
        update_option('slc_agent_chats', $map, false);
    }

    private function unbindAgent(int $agentId, int $conversationId): void
    {
        $map = get_option('slc_agent_chats', []);
        if (!is_array($map)) {
            return;
        }
        if ((int) ($map[(string) $agentId] ?? 0) === $conversationId) {
            unset($map[(string) $agentId]);
            update_option('slc_agent_chats', $map, false);
        }
    }

    private function appendMessage(int $conversationId, string $role, string $content): int
    {
        $content = trim(wp_strip_all_tags($content));
        if ($conversationId < 1 || $content === '') {
            return 0;
        }
        global $wpdb;
        $ok = $wpdb->insert($this->messagesTable(), [
            'conversation_id' => $conversationId,
            'role'            => $role,
            'content'         => $content,
            'created_at'      => current_time('mysql'),
        ]);
        if ($ok !== 1) {
            return 0;
        }
        $count = (int) $wpdb->get_var($wpdb->prepare(
            'SELECT COUNT(*) FROM ' . $this->messagesTable() . ' WHERE conversation_id = %d',
            $conversationId
        ));
        $this->update($conversationId, ['message_count' => $count]);

        return (int) $wpdb->insert_id;
    }

    /**
     * @param array<string, mixed> $fields
     */
    private function update(int $id, array $fields): void
    {
        global $wpdb;
        $fields['updated_at'] = current_time('mysql');
        $wpdb->update($this->conversationsTable(), $fields, ['id' => $id]);
    }

    private function touch(int $id, bool $user): void
    {
        $fields = [];
        if ($user) {
            $fields['last_user_message_at'] = current_time('mysql');
        }
        $this->update($id, $fields);
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function meta(array $row): array
    {
        $decoded = json_decode((string) ($row['meta_json'] ?? ''), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function normalizeSession(string $session): string
    {
        $session = strtolower(trim($session));

        return preg_match('/^[a-f0-9]{32}$/', $session) === 1 ? $session : '';
    }

    private function conversationsTable(): string
    {
        global $wpdb;

        return $wpdb->prefix . 'slc_conversations';
    }

    private function messagesTable(): string
    {
        global $wpdb;

        return $wpdb->prefix . 'slc_messages';
    }
}
