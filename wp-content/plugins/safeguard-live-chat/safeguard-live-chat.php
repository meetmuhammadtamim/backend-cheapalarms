<?php
/**
 * Plugin Name: Safeguard Live Chat
 * Description: Website chat button, conversation store, and staff replies in Telegram. No AI and no portal.
 * Version: 1.1.4
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Safeguard
 * Text Domain: safeguard-live-chat
 * License: Proprietary
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SLC_PLUGIN_FILE', __FILE__);
define('SLC_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SLC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SLC_PLUGIN_VERSION', '1.1.4');

require_once SLC_PLUGIN_PATH . 'includes/class-slc-plugin.php';

register_activation_hook(__FILE__, ['Slc_Plugin', 'activate']);
register_deactivation_hook(__FILE__, ['Slc_Plugin', 'deactivate']);

add_action('plugins_loaded', static function () {
    Slc_Plugin::instance()->boot();
});
