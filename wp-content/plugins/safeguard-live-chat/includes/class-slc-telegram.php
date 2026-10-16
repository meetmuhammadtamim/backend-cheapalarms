<?php

if (!defined('ABSPATH')) {
    exit;
}

class Slc_Telegram
{
    private const API = 'https://api.telegram.org/bot';

    /** @var array<string, string> */
    private array $secrets;

    /**
     * @param array<string, string> $secrets
     */
    public function __construct(array $secrets)
    {
        $this->secrets = $secrets;
    }

    public function isConfigured(): bool
    {
        return $this->token() !== '' && $this->staffChatId() !== '';
    }

    public function token(): string
    {
        return trim((string) ($this->secrets['telegram_bot_token'] ?? ''));
    }

    public function staffChatId(): string
    {
        return trim((string) ($this->secrets['telegram_staff_chat_id'] ?? ''));
    }

    public function webhookSecret(): string
    {
        return trim((string) ($this->secrets['telegram_webhook_secret'] ?? ''));
    }

    /**
     * @param array<string, mixed> $markup
     * @return array<string, mixed>|null
     */
    public function sendMessage(string $chatId, string $text, array $markup = []): ?array
    {
        $payload = [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'HTML',
        ];
        if ($markup !== []) {
            $payload['reply_markup'] = wp_json_encode($markup);
        }

        return $this->request('sendMessage', $payload);
    }

    /**
     * @param array<string, mixed> $markup
     */
    public function editMessageText(string $chatId, int $messageId, string $text, array $markup = []): ?array
    {
        $payload = [
            'chat_id'    => $chatId,
            'message_id' => $messageId,
            'text'       => $text,
            'parse_mode' => 'HTML',
        ];
        if ($markup !== []) {
            $payload['reply_markup'] = wp_json_encode($markup);
        }

        return $this->request('editMessageText', $payload);
    }

    public function answerCallback(string $id, string $text = '', bool $alert = false): void
    {
        if ($id === '') {
            return;
        }
        $this->request('answerCallbackQuery', [
            'callback_query_id' => $id,
            'text'              => $text,
            'show_alert'        => $alert,
        ]);
    }

    public function setWebhook(string $url): bool
    {
        $payload = [
            'url'                  => $url,
            'allowed_updates'      => wp_json_encode(['message', 'callback_query']),
            'drop_pending_updates' => false,
        ];
        $secret = $this->webhookSecret();
        if ($secret !== '') {
            $payload['secret_token'] = $secret;
        }
        $result = $this->request('setWebhook', $payload);

        return is_array($result);
    }

    public function claimLink(int $conversationId): string
    {
        $username = $this->username();
        if ($username === '') {
            return '';
        }

        return 'https://t.me/' . rawurlencode($username) . '?start=claim_' . $conversationId;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function notifyStaff(array $row, string $opening = ''): ?int
    {
        if (!$this->isConfigured()) {
            return null;
        }
        $id = (int) $row['id'];
        $name = $this->h((string) ($row['visitor_name'] ?? 'Visitor'));
        $lines = [
            '<b>Safeguard live chat</b>',
            'Chat #' . $id,
            '',
            '<b>Name:</b> ' . $name,
        ];
        $email = trim((string) ($row['visitor_email'] ?? ''));
        $phone = trim((string) ($row['visitor_phone'] ?? ''));
        $page = trim((string) ($row['page_path'] ?? ''));
        if ($email !== '') {
            $lines[] = '<b>Email:</b> ' . $this->h($email);
        }
        if ($phone !== '') {
            $lines[] = '<b>Phone:</b> ' . $this->h($phone);
        }
        if ($page !== '') {
            $lines[] = '<b>Page:</b> ' . $this->h($page);
        }
        $meta = json_decode((string) ($row['meta_json'] ?? ''), true);
        if (is_array($meta) && !empty($meta['ghlContactId'])) {
            $lines[] = '<b>GoHighLevel:</b> ' . (!empty($meta['ghlExisting']) ? 'existing contact' : 'new contact');
        }
        $opening = trim($opening);
        if ($opening !== '') {
            if (strlen($opening) > 1000) {
                $opening = substr($opening, 0, 997) . '...';
            }
            $lines[] = '';
            $lines[] = '<b>Message:</b>';
            $lines[] = $this->h($opening);
        }
        $lines[] = '';
        $lines[] = 'Tap Claim chat. Then reply in this private chat with the bot.';

        $button = ['text' => 'Claim chat'];
        $link = $this->claimLink($id);
        if ($link !== '') {
            $button['url'] = $link;
        } else {
            $button['callback_data'] = 'claim:' . $id;
        }

        $result = $this->sendMessage($this->staffChatId(), implode("\n", $lines), [
            'inline_keyboard' => [[$button]],
        ]);
        if (!is_array($result) || empty($result['message_id'])) {
            return null;
        }

        return (int) $result['message_id'];
    }

    /**
     * @param array<string, mixed> $row
     * @param array<int, array<string, mixed>> $messages
     */
    public function forwardVisitor(array $row, string $text): void
    {
        $tg = $this->meta($row);
        $chatId = (string) ($tg['agentDmChatId'] ?? '');
        if ($chatId === '' && !empty($tg['claimedById'])) {
            $chatId = (string) (int) $tg['claimedById'];
        }
        if ($chatId === '') {
            $chatId = $this->staffChatId();
        }
        if ($chatId === '') {
            return;
        }
        $name = $this->h((string) ($row['visitor_name'] ?? 'Customer'));
        $this->sendMessage($chatId, '<b>' . $name . '</b>' . "\n" . $this->h($text));
    }

    public function username(): string
    {
        $cached = get_transient('slc_bot_username');
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }
        $result = $this->request('getMe', []);
        $username = sanitize_text_field((string) ($result['username'] ?? ''));
        if ($username !== '') {
            set_transient('slc_bot_username', $username, DAY_IN_SECONDS);
        }

        return $username;
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>|null
     */
    private function request(string $method, array $params): ?array
    {
        $token = $this->token();
        if ($token === '') {
            return null;
        }
        $response = wp_remote_post(self::API . $token . '/' . $method, [
            'timeout' => 15,
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => wp_json_encode($params === [] ? new stdClass() : $params),
        ]);
        if (is_wp_error($response)) {
            return null;
        }
        $code = (int) wp_remote_retrieve_response_code($response);
        $data = json_decode((string) wp_remote_retrieve_body($response), true);
        if ($code < 200 || $code >= 300 || !is_array($data) || empty($data['ok'])) {
            return null;
        }
        $result = $data['result'] ?? null;

        return is_array($result) ? $result : ['ok' => true];
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function meta(array $row): array
    {
        $decoded = json_decode((string) ($row['meta_json'] ?? ''), true);
        if (!is_array($decoded)) {
            return [];
        }
        $tg = $decoded['telegram'] ?? [];

        return is_array($tg) ? $tg : [];
    }

    private function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
