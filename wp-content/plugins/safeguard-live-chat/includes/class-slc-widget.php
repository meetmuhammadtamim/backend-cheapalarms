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

    private static function icon(string $name): void
    {
        $paths = [
            'x'              => '<path d="M18 6 6 18"></path><path d="m6 6 12 12"></path>',
            'send'           => '<path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"></path><path d="m21.854 2.147-10.94 10.939"></path>',
            'message-circle' => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"></path>',
        ];

        if (!isset($paths[$name])) {
            return;
        }

        printf(
            '<svg class="sg-lucide-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
            $paths[$name] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG paths.
        );
    }
}
