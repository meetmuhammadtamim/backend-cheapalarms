<?php

if (!defined('ABSPATH')) {
    exit;
}

class Slc_Rest
{
    /** @var Slc_Store */
    private $store;

    /** @var Slc_Telegram */
    private $telegram;

    /** @var Slc_Ghl */
    private $ghl;

    public function __construct(Slc_Store $store, Slc_Telegram $telegram, Slc_Ghl $ghl)
    {
        $this->store = $store;
        $this->telegram = $telegram;
        $this->ghl = $ghl;
    }

    public function register(): void
    {
        register_rest_route('slc/v1', '/chat/start', [
            'methods'             => 'POST',
            'permission_callback' => '__return_true',
            'callback'            => [$this, 'start'],
        ]);
        register_rest_route('slc/v1', '/chat/message', [
            'methods'             => 'POST',
            'permission_callback' => '__return_true',
            'callback'            => [$this, 'message'],
        ]);
        register_rest_route('slc/v1', '/chat/poll', [
            'methods'             => 'POST',
            'permission_callback' => '__return_true',
            'callback'            => [$this, 'poll'],
        ]);
        register_rest_route('slc/v1', '/telegram/webhook', [
            'methods'             => 'POST',
            'permission_callback' => '__return_true',
            'callback'            => [$this, 'webhook'],
        ]);
        register_rest_route('slc/v1', '/telegram/setup-webhook', [
            'methods'             => 'POST',
            'permission_callback' => '__return_true',
            'callback'            => [$this, 'setupWebhook'],
        ]);
    }

    public function start(WP_REST_Request $request): WP_REST_Response
    {
        if ($this->limited('start', 8)) {
            return $this->error('Slow down and try again in a few minutes.', 429);
        }
        if (!$this->telegram->isConfigured()) {
            return $this->error('Live chat is not available right now. Please call 1300 225 276.', 503);
        }

        $name = $this->text($request, 'name', 120);
        $email = sanitize_email((string) $request->get_param('email'));
        $phone = $this->text($request, 'phone', 40);
        $message = $this->text($request, 'message', 2000);
        $page = $this->text($request, 'page', 255);

        if ($name === '' || $email === '' || !is_email($email) || $phone === '' || $message === '') {
            return $this->error('Name, email, phone, and a message are required.', 400);
        }

        $linked = $this->ghl->linkContact($name, $email, $phone);
        if (is_wp_error($linked)) {
            return $this->fromError($linked);
        }

        $row = $this->store->start([
            'name'  => $name,
            'email' => $email,
            'phone' => $phone,
            'page'  => $page,
        ], $message);
        if (is_wp_error($row)) {
            return $this->fromError($row);
        }

        $this->store->attachGhlContact((int) $row['id'], (string) $linked['id'], !empty($linked['existing']));
        $row = $this->store->findById((int) $row['id']) ?? $row;

        $alertId = $this->telegram->notifyStaff($row);
        if ($alertId === null) {
            return $this->error('Could not reach the team. Please call 1300 225 276.', 502);
        }
        $this->store->rememberTelegramMessage((int) $row['id'], $alertId);

        return new WP_REST_Response([
            'ok'         => true,
            'sessionKey' => (string) $row['session_key'],
            'status'     => (string) $row['status'],
            'reply'      => 'Thanks. Stay on this chat and someone from the team will reply.',
        ]);
    }

    public function message(WP_REST_Request $request): WP_REST_Response
    {
        if ($this->limited('message', 30)) {
            return $this->error('Slow down and try again in a few minutes.', 429);
        }
        $session = (string) $request->get_param('sessionKey');
        $text = $this->text($request, 'message', 2000);
        $row = $this->store->addVisitorMessage($session, $text);
        if (is_wp_error($row)) {
            return $this->fromError($row);
        }
        $this->telegram->forwardVisitor($row, $text);

        return new WP_REST_Response([
            'ok'     => true,
            'status' => (string) $row['status'],
        ]);
    }

    public function poll(WP_REST_Request $request): WP_REST_Response
    {
        $row = $this->store->findBySession((string) $request->get_param('sessionKey'));
        if ($row === null) {
            return $this->error('Chat not found.', 404);
        }
        $after = (int) $request->get_param('afterId');
        $messages = [];
        foreach ($this->store->messagesAfter((int) $row['id'], $after) as $msg) {
            $role = (string) ($msg['role'] ?? '');
            if (!in_array($role, ['agent', 'system'], true)) {
                continue;
            }
            $messages[] = [
                'id'      => (int) $msg['id'],
                'role'    => $role,
                'content' => (string) $msg['content'],
            ];
        }

        return new WP_REST_Response([
            'ok'       => true,
            'status'   => (string) $row['status'],
            'messages' => $messages,
        ]);
    }

    public function setupWebhook(WP_REST_Request $request): WP_REST_Response
    {
        $expected = $this->telegram->webhookSecret();
        $provided = sanitize_text_field((string) $request->get_header('X-SLC-Telegram-Setup'));
        if ($expected === '' || $provided === '' || !hash_equals($expected, $provided)) {
            return $this->error('Invalid setup secret.', 403);
        }
        $url = rest_url('slc/v1/telegram/webhook');
        $ok = $this->telegram->setWebhook($url);

        return new WP_REST_Response(['ok' => $ok, 'url' => $url], $ok ? 200 : 500);
    }

    public function webhook(WP_REST_Request $request): WP_REST_Response
    {
        $secret = $this->telegram->webhookSecret();
        if ($secret !== '') {
            $header = sanitize_text_field((string) $request->get_header('X-Telegram-Bot-Api-Secret-Token'));
            if ($header === '' || !hash_equals($secret, $header)) {
                return new WP_REST_Response(['ok' => false], 403);
            }
        }
        $update = $request->get_json_params();
        if (!is_array($update)) {
            return new WP_REST_Response(['ok' => true]);
        }
        if (isset($update['callback_query']) && is_array($update['callback_query'])) {
            $this->onCallback($update['callback_query']);
        } elseif (isset($update['message']) && is_array($update['message'])) {
            $this->onStaffMessage($update['message']);
        }

        return new WP_REST_Response(['ok' => true]);
    }

    /**
     * @param array<string, mixed> $callback
     */
    private function onCallback(array $callback): void
    {
        $data = sanitize_text_field((string) ($callback['data'] ?? ''));
        $queryId = (string) ($callback['id'] ?? '');
        if (strpos($data, 'claim:') !== 0) {
            $this->telegram->answerCallback($queryId);

            return;
        }
        $from = is_array($callback['from'] ?? null) ? $callback['from'] : [];
        $result = $this->claim((int) substr($data, 6), $from);
        if (is_wp_error($result)) {
            $this->telegram->answerCallback($queryId, $result->get_error_message(), true);

            return;
        }
        $this->telegram->answerCallback($queryId, 'Claimed. Check your private chat with the bot.');
    }

    /**
     * @param array<string, mixed> $message
     */
    private function onStaffMessage(array $message): void
    {
        if (!empty($message['from']['is_bot'])) {
            return;
        }
        $chat = is_array($message['chat'] ?? null) ? $message['chat'] : [];
        $type = (string) ($chat['type'] ?? '');
        $chatId = (string) ($chat['id'] ?? '');
        if ($type === 'private') {
            $this->onPrivate($message, $chatId);

            return;
        }
        if ($chatId === '' || $chatId !== $this->telegram->staffChatId()) {
            return;
        }
        $text = trim(wp_strip_all_tags((string) ($message['text'] ?? '')));
        if ($text === '' || strpos($text, '/') === 0) {
            return;
        }
        $reply = is_array($message['reply_to_message'] ?? null) ? $message['reply_to_message'] : null;
        if ($reply === null) {
            return;
        }
        $conversationId = $this->store->findByTelegramMessage((int) ($reply['message_id'] ?? 0));
        if ($conversationId < 1 && preg_match('/Chat #(\d+)/', (string) ($reply['text'] ?? ''), $m) === 1) {
            $conversationId = (int) $m[1];
        }
        if ($conversationId < 1) {
            return;
        }
        $from = is_array($message['from'] ?? null) ? $message['from'] : [];
        $stored = $this->store->addAgentMessage($conversationId, (int) ($from['id'] ?? 0), $text);
        if (is_wp_error($stored)) {
            $this->telegram->sendMessage($chatId, $this->h($stored->get_error_message()));
        }
    }

    /**
     * @param array<string, mixed> $message
     */
    private function onPrivate(array $message, string $chatId): void
    {
        $from = is_array($message['from'] ?? null) ? $message['from'] : [];
        $agentId = (int) ($from['id'] ?? 0);
        $text = trim(wp_strip_all_tags((string) ($message['text'] ?? '')));
        $parts = preg_split('/\s+/', $text, 2) ?: [];
        $command = strtolower((string) ($parts[0] ?? ''));
        $arg = sanitize_text_field((string) ($parts[1] ?? ''));

        if ($command === '/start' && strpos($arg, 'claim_') === 0) {
            $result = $this->claim((int) substr($arg, 6), $from);
            if (is_wp_error($result)) {
                $this->telegram->sendMessage($chatId, $this->h($result->get_error_message()));
            }

            return;
        }

        if ($command === '/start' || $command === '/chat') {
            $id = $this->store->activeForAgent($agentId);
            if ($id < 1) {
                $this->telegram->sendMessage($chatId, "You're connected.\n\nTap Claim chat in the staff group. Then reply here.\n\nCommands: /chat · /done");

                return;
            }
            $row = $this->store->findById($id);
            $this->telegram->sendMessage($chatId, $row ? $this->briefing($row) : 'Active chat not found.');

            return;
        }

        if ($command === '/done') {
            $id = $this->store->activeForAgent($agentId);
            if ($id < 1 || !$this->store->resolve($id, $agentId)) {
                $this->telegram->sendMessage($chatId, 'No active chat to close.');

                return;
            }
            $this->telegram->sendMessage($chatId, 'Closed chat #' . $id . '.');

            return;
        }

        if ($text === '' || strpos($text, '/') === 0) {
            return;
        }
        $id = $this->store->activeForAgent($agentId);
        if ($id < 1) {
            $this->telegram->sendMessage($chatId, 'No active customer chat. Claim one from the staff group.');

            return;
        }
        $stored = $this->store->addAgentMessage($id, $agentId, $text);
        if (is_wp_error($stored)) {
            $this->telegram->sendMessage($chatId, $this->h($stored->get_error_message()));
        }
    }

    /**
     * @param array<string, mixed> $from
     * @return true|\WP_Error
     */
    private function claim(int $conversationId, array $from)
    {
        $name = trim(sanitize_text_field((string) ($from['first_name'] ?? '')) . ' ' . sanitize_text_field((string) ($from['last_name'] ?? '')));
        $username = sanitize_text_field((string) ($from['username'] ?? ''));
        $agentId = (int) ($from['id'] ?? 0);
        $result = $this->store->claim($conversationId, [
            'id'       => $agentId,
            'name'     => $name !== '' ? $name : ($username !== '' ? $username : 'Agent'),
            'username' => $username,
        ]);
        if (is_wp_error($result)) {
            return $result;
        }
        $this->telegram->sendMessage((string) $agentId, $this->briefing($result));
        $this->markClaimed($result, $name !== '' ? $name : $username);

        return true;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function markClaimed(array $row, string $claimer): void
    {
        $tg = $this->store->telegramMeta($row);
        $messageId = (int) ($tg['alertMessageId'] ?? 0);
        $chatId = $this->telegram->staffChatId();
        if ($messageId < 1 || $chatId === '') {
            return;
        }
        $text = '<b>Safeguard live chat</b>' . "\nChat #" . (int) $row['id'] . "\n\nClaimed by <b>" . $this->h($claimer) . "</b>\nContinuing in their private chat with the bot.";
        $this->telegram->editMessageText($chatId, $messageId, $text);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function briefing(array $row): string
    {
        $id = (int) $row['id'];
        $lines = [
            '<b>You claimed chat #' . $id . '</b>',
            '',
            'Name: ' . $this->h((string) ($row['visitor_name'] ?? '')),
            'Email: ' . $this->h((string) ($row['visitor_email'] ?? '')),
            'Phone: ' . $this->h((string) ($row['visitor_phone'] ?? '')),
        ];
        $recent = $this->store->recentMessages($id, 8);
        if ($recent !== []) {
            $lines[] = '';
            $lines[] = '<b>Recent messages</b>';
            foreach ($recent as $msg) {
                $role = (string) ($msg['role'] ?? 'user');
                $label = $role === 'agent' ? 'You' : ($role === 'system' ? 'System' : 'Customer');
                $content = trim((string) ($msg['content'] ?? ''));
                if (strlen($content) > 280) {
                    $content = substr($content, 0, 277) . '...';
                }
                $lines[] = '<b>' . $label . ':</b> ' . $this->h($content);
            }
        }
        $lines[] = '';
        $lines[] = 'Reply here to message the customer. /done closes the chat.';

        return implode("\n", $lines);
    }

    private function text(WP_REST_Request $request, string $key, int $max): string
    {
        $value = sanitize_text_field((string) $request->get_param($key));
        if (strlen($value) > $max) {
            $value = substr($value, 0, $max);
        }

        return trim($value);
    }

    private function limited(string $bucket, int $max): bool
    {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '0';
        $key = 'slc_' . $bucket . '_' . md5($ip);
        $count = (int) get_transient($key);
        if ($count >= $max) {
            return true;
        }
        set_transient($key, $count + 1, HOUR_IN_SECONDS);

        return false;
    }

    private function error(string $message, int $status): WP_REST_Response
    {
        return new WP_REST_Response(['ok' => false, 'error' => $message], $status);
    }

    private function fromError(WP_Error $error): WP_REST_Response
    {
        $data = $error->get_error_data();
        $status = is_array($data) && isset($data['status']) ? (int) $data['status'] : 400;

        return $this->error($error->get_error_message(), $status);
    }

    private function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
