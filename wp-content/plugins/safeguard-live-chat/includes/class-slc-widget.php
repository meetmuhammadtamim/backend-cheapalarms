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
                    <div class="sg-chat__head-text">
                        <p class="sg-chat__eyebrow"><?php esc_html_e('Safeguard', 'safeguard-live-chat'); ?></p>
                        <h2 class="sg-chat__title" id="sg-chat-title"><?php esc_html_e('Message our team', 'safeguard-live-chat'); ?></h2>
                    </div>
                    <div class="sg-chat__head-actions">
                        <button type="button" class="sg-chat__new" id="sg-chat-new" aria-label="<?php esc_attr_e('Start a new chat', 'safeguard-live-chat'); ?>">
                            <?php esc_html_e('New chat', 'safeguard-live-chat'); ?>
                        </button>
                        <button type="button" class="sg-chat__close" id="sg-chat-close" aria-label="<?php esc_attr_e('Close chat', 'safeguard-live-chat'); ?>">
                            <?php self::icon('x'); ?>
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
                        <?php self::icon('send'); ?>
                    </button>
                </form>
            </div>
            <button type="button" class="sg-chat__toggle" id="sg-chat-toggle" aria-expanded="false" aria-controls="sg-chat-panel">
                <span class="sg-chat__toggle-icon sg-chat__toggle-icon--open" aria-hidden="true">
                    <?php self::icon('message-circle'); ?>
                </span>
                <span class="sg-chat__toggle-icon sg-chat__toggle-icon--close" aria-hidden="true">
                    <?php self::icon('x'); ?>
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
    private static function icon(string $name): void
    {
        $paths = [
            'x'              => 'M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z', // mdi:close
            'send'           => 'm4 6.03l7.5 3.22l-7.5-1zm7.5 8.72L4 17.97v-2.22zM2 3v7l15 2l-15 2v7l21-9z', // mdi:send-outline
            'message-circle' => 'M12 3C6.5 3 2 6.58 2 11a7.22 7.22 0 0 0 2.75 5.5c0 .6-.42 2.17-2.75 4.5c2.37-.11 4.64-1 6.47-2.5c1.14.33 2.34.5 3.53.5c5.5 0 10-3.58 10-8s-4.5-8-10-8m0 14c-4.42 0-8-2.69-8-6s3.58-6 8-6s8 2.69 8 6s-3.58 6-8 6', // mdi:chat-outline
        ];

        if (!isset($paths[$name])) {
            return;
        }

        printf(
            '<svg class="sg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="%s"/></svg>',
            esc_attr($paths[$name])
        );
    }
}
