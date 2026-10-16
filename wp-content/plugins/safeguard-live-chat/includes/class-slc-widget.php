<?php

if (!defined('ABSPATH')) {
    exit;
}

class Slc_Widget
{
    public static function register(): void
    {
        add_action('wp_enqueue_scripts', [self::class, 'assets'], 35);
        add_action('wp_footer', [self::class, 'render'], 100);
    }

    public static function assets(): void
    {
        if (!self::shouldShow()) {
            return;
        }

        wp_enqueue_style(
            'safeguard-live-chat',
            SLC_PLUGIN_URL . 'assets/css/chat.css',
            [],
            SLC_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'safeguard-live-chat',
            SLC_PLUGIN_URL . 'assets/js/chat.js',
            [],
            SLC_PLUGIN_VERSION,
            true
        );

        wp_localize_script('safeguard-live-chat', 'sgChatConfig', [
            'startUrl'   => esc_url_raw(rest_url('slc/v1/chat/start')),
            'messageUrl' => esc_url_raw(rest_url('slc/v1/chat/message')),
            'pollUrl'    => esc_url_raw(rest_url('slc/v1/chat/poll')),
            'page'       => self::pagePath(),
        ]);
    }

    public static function render(): void
    {
        if (!self::shouldShow()) {
            return;
        }
        ?>
        <div class="sg-chat" id="sg-chat">
            <div class="sg-chat__panel" id="sg-chat-panel" role="dialog" aria-modal="true" aria-labelledby="sg-chat-title" aria-hidden="true">
                <header class="sg-chat__head">
                    <span class="sg-chat__badge" aria-hidden="true"><?php self::icon('headset', 22); ?></span>
                    <div class="sg-chat__head-text">
                        <h2 class="sg-chat__title" id="sg-chat-title"><?php esc_html_e('Message Our Team', 'safeguard-live-chat'); ?></h2>
                    </div>
                    <div class="sg-chat__head-actions">
                        <button type="button" class="sg-chat__new" id="sg-chat-new" aria-label="<?php esc_attr_e('Start a new chat', 'safeguard-live-chat'); ?>">
                            <?php esc_html_e('New chat', 'safeguard-live-chat'); ?>
                        </button>
                        <button type="button" class="sg-chat__close" id="sg-chat-close" aria-label="<?php esc_attr_e('Close chat', 'safeguard-live-chat'); ?>">
                            <?php self::icon('x', 20); ?>
                        </button>
                    </div>
                </header>
                <div class="sg-chat__messages" id="sg-chat-messages" role="log" aria-live="polite" aria-relevant="additions"></div>
                <form class="sg-chat__form" id="sg-chat-form">
                    <label class="sg-chat__sr-only" for="sg-chat-input"><?php esc_html_e('Your message', 'safeguard-live-chat'); ?></label>
                    <textarea
                        class="sg-chat__input"
                        id="sg-chat-input"
                        name="message"
                        rows="1"
                        maxlength="2000"
                        required
                        autocomplete="off"
                    ></textarea>
                    <button type="submit" class="sg-chat__send" id="sg-chat-send" aria-label="<?php esc_attr_e('Send message', 'safeguard-live-chat'); ?>">
                        <?php self::icon('send', 20); ?>
                    </button>
                </form>
            </div>
            <button type="button" class="sg-chat__toggle" id="sg-chat-toggle" aria-expanded="false" aria-controls="sg-chat-panel">
                <span class="sg-chat__toggle-icon sg-chat__toggle-icon--open" aria-hidden="true">
                    <?php self::icon('message-circle', 26); ?>
                </span>
                <span class="sg-chat__toggle-icon sg-chat__toggle-icon--close" aria-hidden="true">
                    <?php self::icon('x', 24); ?>
                </span>
                <span class="sg-chat__toggle-label"><?php esc_html_e('Chat', 'safeguard-live-chat'); ?></span>
            </button>
        </div>
        <?php
    }

    private static function shouldShow(): bool
    {
        return !is_admin() && !wp_doing_ajax() && !wp_is_json_request();
    }

    private static function pagePath(): string
    {
        global $wp;

        if (is_front_page()) {
            return '/';
        }

        if (isset($wp->request) && is_string($wp->request) && $wp->request !== '') {
            return '/' . trim($wp->request, '/') . '/';
        }

        return '/';
    }

    /**
     * Material Design Icons (https://pictogrammers.com/library/mdi/), single filled paths.
     */
    private static function icon(string $name, int $size = 20): void
    {
        $paths = [
            'x'              => 'M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z', // mdi:close
            'send'           => 'm4 6.03l7.5 3.22l-7.5-1zm7.5 8.72L4 17.97v-2.22zM2 3v7l15 2l-15 2v7l21-9z', // mdi:send-outline
            'message-circle' => 'M12,3C6.5,3 2,6.58 2,11C2.05,13.15 3.06,15.17 4.75,16.5C4.75,17.1 4.33,18.67 2,21C4.37,20.89 6.64,20 8.47,18.5C9.61,18.83 10.81,19 12,19C17.5,19 22,15.42 22,11C22,6.58 17.5,3 12,3Z', // mdi:chat
            'headset'        => 'M12,1C7,1 3,5 3,10V17A3,3 0 0,0 6,20H9V12H5V10A7,7 0 0,1 12,3A7,7 0 0,1 19,10V12H15V20H19V21H12V23H18A3,3 0 0,0 21,20V10C21,5 16.97,1 12,1Z', // mdi:headset
        ];

        if (!isset($paths[$name])) {
            return;
        }

        printf(
            '<svg class="sg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="%2$d" height="%2$d" fill="currentColor" aria-hidden="true" focusable="false"><path d="%1$s"/></svg>',
            esc_attr($paths[$name]),
            $size
        );
    }
}
