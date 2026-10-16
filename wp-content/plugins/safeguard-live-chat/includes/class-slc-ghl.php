<?php

if (!defined('ABSPATH')) {
    exit;
}

class Slc_Ghl
{
    private const BASE = 'https://services.leadconnectorhq.com';
    private const VERSION = '2021-07-28';

    /** @var array<string, string> */
    private $secrets;

    /**
     * @param array<string, string> $secrets
     */
    public function __construct(array $secrets)
    {
        $this->secrets = $secrets;
    }

    public function isConfigured(): bool
    {
        return $this->token() !== '' && $this->locationId() !== '';
    }

    /**
     * Find a contact by phone, then email. Create one only when neither matches.
     * An existing contact is not updated.
     *
     * @return array{id:string,existing:bool}|\WP_Error
     */
    public function linkContact(string $name, string $email, string $phone)
    {
        if (!$this->isConfigured()) {
            return new WP_Error('ghl_not_configured', 'GoHighLevel is not configured.', ['status' => 503]);
        }

        $email = strtolower(trim($email));
        $phoneE164 = $this->toE164($phone);
        $phoneQuery = $phoneE164 !== '' ? $phoneE164 : $phone;

        $existing = $this->find($phoneQuery, $email);
        if (is_string($existing) && $existing !== '') {
            return ['id' => $existing, 'existing' => true];
        }

        $parts = preg_split('/\s+/', trim($name), 2) ?: [];
        $payload = [
            'firstName'  => (string) ($parts[0] ?? $name),
            'email'      => $email,
            'phone'      => $phoneQuery,
            'locationId' => $this->locationId(),
        ];
        $last = trim((string) ($parts[1] ?? ''));
        if ($last !== '') {
            $payload['lastName'] = $last;
        }

        $created = $this->request('POST', '/contacts/', $payload);
        if (!is_wp_error($created)) {
            $id = $this->idFrom($created);
            if ($id === '') {
                return new WP_Error('ghl_contact_missing_id', 'GoHighLevel did not return a contact.', ['status' => 502]);
            }

            return ['id' => $id, 'existing' => false];
        }

        $data = $created->get_error_data();
        $code = is_array($data) ? (int) ($data['code'] ?? 0) : 0;
        if ($code === 400 && is_array($data)) {
            $body = $data['body'] ?? null;
            if (is_string($body)) {
                $decoded = json_decode($body, true);
                $body = is_array($decoded) ? $decoded : null;
            }
            if (is_array($body)) {
                $id = $body['meta']['contactId'] ?? $body['contactId'] ?? '';
                if (is_string($id) && $id !== '') {
                    return ['id' => $id, 'existing' => true];
                }
            }
        }

        return new WP_Error('ghl_contact_failed', 'Could not save the contact in GoHighLevel.', ['status' => 502]);
    }

    /**
     * Save a website enquiry on a GoHighLevel contact.
     * A matching email is reused. The message is added as a note either way.
     */
    public function saveEnquiry(string $name, string $email, string $subject, string $message): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $email = strtolower(trim($email));
        $name = trim($name);
        $subject = trim($subject);
        $message = trim($message);
        if ($name === '' || $email === '' || !is_email($email) || $message === '') {
            return false;
        }
        if (strlen($message) > 3500) {
            $message = substr($message, 0, 3497) . '...';
        }

        $id = $this->findByEmail($email);
        if ($id === '') {
            $id = $this->createEnquiryContact($name, $email);
        }
        if ($id === '') {
            return false;
        }

        $lines = [
            'Website contact form',
            '',
            'Name: ' . $name,
            'Email: ' . $email,
        ];
        if ($subject !== '') {
            $lines[] = 'Subject: ' . $subject;
        }
        $lines[] = '';
        $lines[] = $message;

        $note = [
            'body'      => implode("\n", $lines),
            'contactId' => $id,
        ];
        $noted = $this->request('POST', '/contacts/' . rawurlencode($id) . '/notes', $note);
        if (is_wp_error($noted)) {
            $this->request('POST', '/notes/', $note);
        }

        return true;
    }

    private function findByEmail(string $email): string
    {
        $result = $this->request('POST', '/contacts/search', [
            'locationId' => $this->locationId(),
            'page'       => 1,
            'pageLimit'  => 20,
            'query'      => $email,
        ]);
        if (is_wp_error($result)) {
            return '';
        }
        $contacts = $result['contacts'] ?? $result['items'] ?? [];
        if (!is_array($contacts)) {
            return '';
        }
        foreach ($contacts as $contact) {
            if (!is_array($contact)) {
                continue;
            }
            $contactEmail = strtolower(trim((string) ($contact['email'] ?? '')));
            $id = (string) ($contact['id'] ?? '');
            if ($id !== '' && $contactEmail === $email) {
                return $id;
            }
        }

        return '';
    }

    private function createEnquiryContact(string $name, string $email): string
    {
        $parts = preg_split('/\s+/', trim($name), 2) ?: [];
        $payload = [
            'firstName'  => (string) ($parts[0] ?? $name),
            'email'      => $email,
            'locationId' => $this->locationId(),
            'source'     => 'Website contact form',
            'tags'       => ['Website enquiry'],
        ];
        $last = trim((string) ($parts[1] ?? ''));
        if ($last !== '') {
            $payload['lastName'] = $last;
        }

        $created = $this->request('POST', '/contacts/', $payload);
        if (!is_wp_error($created)) {
            return $this->idFrom($created);
        }

        $data = $created->get_error_data();
        $code = is_array($data) ? (int) ($data['code'] ?? 0) : 0;
        if ($code !== 400 || !is_array($data)) {
            return '';
        }
        $body = $data['body'] ?? null;
        if (is_string($body)) {
            $decoded = json_decode($body, true);
            $body = is_array($decoded) ? $decoded : null;
        }
        if (!is_array($body)) {
            return '';
        }
        $id = $body['meta']['contactId'] ?? $body['contactId'] ?? '';

        return is_string($id) ? $id : '';
    }

    /**
     * @return string|\WP_Error|null Contact id, error, or null when nobody matches.
     */
    private function find(string $phone, string $email)
    {
        foreach ([$phone, $email] as $query) {
            if ($query === '') {
                continue;
            }
            $result = $this->request('GET', '/contacts/search', null, ['query' => $query]);
            if (is_wp_error($result)) {
                continue;
            }
            $contacts = $result['contacts'] ?? $result['items'] ?? [];
            if (!is_array($contacts)) {
                continue;
            }
            foreach ($contacts as $contact) {
                if (!is_array($contact)) {
                    continue;
                }
                $id = $contact['id'] ?? '';
                if (!is_string($id) || $id === '') {
                    continue;
                }
                $contactEmail = strtolower((string) ($contact['email'] ?? ''));
                $samePhone = $phone !== '' && $this->samePhone($phone, (string) ($contact['phone'] ?? ''));
                $sameEmail = $email !== '' && $contactEmail === $email;
                if ($samePhone || $sameEmail) {
                    return $id;
                }
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed>|null $body
     * @param array<string, string> $query
     * @return array<string, mixed>|\WP_Error
     */
    private function request(string $method, string $path, ?array $body = null, array $query = [])
    {
        $url = self::BASE . $path;
        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }
        $args = [
            'method'  => $method,
            'timeout' => 15,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token(),
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Version'       => self::VERSION,
                'LocationId'    => $this->locationId(),
            ],
        ];
        if ($body !== null) {
            $args['body'] = wp_json_encode($body);
        }
        $response = wp_remote_request($url, $args);
        if (is_wp_error($response)) {
            return new WP_Error('ghl_http_error', 'Could not reach GoHighLevel.', ['status' => 502]);
        }
        $code = (int) wp_remote_retrieve_response_code($response);
        $raw = (string) wp_remote_retrieve_body($response);
        $decoded = json_decode($raw, true);
        if ($code < 200 || $code >= 300) {
            return new WP_Error('ghl_http_error', 'GoHighLevel returned an error.', [
                'status' => 502,
                'code'   => $code,
                'body'   => is_array($decoded) ? $decoded : $raw,
            ]);
        }

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param array<string, mixed> $result
     */
    private function idFrom(array $result): string
    {
        $id = $result['contact']['id'] ?? $result['id'] ?? $result['contactId'] ?? '';

        return is_string($id) ? $id : '';
    }

    private function samePhone(string $left, string $right): bool
    {
        $a = preg_replace('/\D+/', '', $left) ?? '';
        $b = preg_replace('/\D+/', '', $right) ?? '';
        if ($a === '' || $b === '') {
            return false;
        }

        return substr($a, -9) === substr($b, -9);
    }

    private function toE164(string $raw): string
    {
        $clean = preg_replace('/[\s\-()]/', '', trim($raw)) ?? '';
        if (preg_match('/^\+61[234578]\d{8}$/', $clean) === 1) {
            return $clean;
        }
        if (preg_match('/^0[234578]\d{8}$/', $clean) === 1) {
            return '+61' . substr($clean, 1);
        }
        if (preg_match('/^[234578]\d{8}$/', $clean) === 1) {
            return '+61' . $clean;
        }

        return '';
    }

    private function token(): string
    {
        $token = trim((string) ($this->secrets['ghl_token'] ?? ''));
        if ($token === '') {
            $env = getenv('CA_GHL_TOKEN');
            $token = is_string($env) ? trim($env) : '';
        }

        return $token;
    }

    private function locationId(): string
    {
        $id = trim((string) ($this->secrets['ghl_location_id'] ?? ''));
        if ($id === '') {
            $env = getenv('CA_LOCATION_ID');
            $id = is_string($env) ? trim($env) : '';
        }
        if ($id === '') {
            $opt = get_option('ca_ghl_location_id', '');
            $id = is_string($opt) ? trim($opt) : '';
        }

        return $id;
    }
}
