<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once SLC_PLUGIN_PATH . 'includes/class-slc-store.php';
require_once SLC_PLUGIN_PATH . 'includes/class-slc-telegram.php';
require_once SLC_PLUGIN_PATH . 'includes/class-slc-ghl.php';
require_once SLC_PLUGIN_PATH . 'includes/class-slc-rest.php';
require_once SLC_PLUGIN_PATH . 'includes/class-slc-widget.php';

class Slc_Plugin
{
    private static ?Slc_Plugin $instance = null;

    private Slc_Store $store;
    private Slc_Telegram $telegram;
    private Slc_Ghl $ghl;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function activate(): void
    {
        self::instance()->store()->install();
        if (!wp_next_scheduled('slc_expire_waiting')) {
            wp_schedule_event(time() + 120, 'slc_every_5_minutes', 'slc_expire_waiting');
        }
    }

    public static function deactivate(): void
    {
        wp_clear_scheduled_hook('slc_expire_waiting');
    }

    public function boot(): void
    {
        add_filter('cron_schedules', static function (array $schedules): array {
            $schedules['slc_every_5_minutes'] = [
                'interval' => 300,
                'display'  => 'Every 5 minutes',
            ];

            return $schedules;
        });

        add_action('rest_api_init', function () {
            (new Slc_Rest($this->store(), $this->telegram(), $this->ghl()))->register();
        });

        Slc_Widget::register();

        add_action('slc_expire_waiting', function () {
            $this->store()->expireWaiting(10);
        });

        if (!wp_next_scheduled('slc_expire_waiting')) {
            wp_schedule_event(time() + 120, 'slc_every_5_minutes', 'slc_expire_waiting');
        }
    }

    public function store(): Slc_Store
    {
        if (!isset($this->store)) {
            $this->store = new Slc_Store();
        }

        return $this->store;
    }

    public function telegram(): Slc_Telegram
    {
        if (!isset($this->telegram)) {
            $this->telegram = new Slc_Telegram($this->secrets());
        }

        return $this->telegram;
    }

    public function ghl(): Slc_Ghl
    {
        if (!isset($this->ghl)) {
            $this->ghl = new Slc_Ghl($this->secrets());
        }

        return $this->ghl;
    }

    /**
     * @return array<string, string>
     */
    private function secrets(): array
    {
        $path = SLC_PLUGIN_PATH . 'config/secrets.php';
        if (is_readable($path)) {
            $data = include $path;
            if (is_array($data)) {
                return $this->picked($data);
            }
        }

        if (defined('CA_PLUGIN_PATH')) {
            $legacy = CA_PLUGIN_PATH . 'config/secrets.php';
            if (is_readable($legacy)) {
                $data = include $legacy;
                if (is_array($data)) {
                    return $this->picked($data);
                }
            }
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    private function picked(array $data): array
    {
        $keys = [
            'telegram_bot_token',
            'telegram_staff_chat_id',
            'telegram_webhook_secret',
            'ghl_token',
            'ghl_location_id',
        ];
        $out = [];
        foreach ($keys as $key) {
            $out[$key] = isset($data[$key]) ? (string) $data[$key] : '';
        }

        return $out;
    }
}
