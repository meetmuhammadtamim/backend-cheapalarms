<?php
/**
 * Plugin Name: Site Blocks
 * Description: Server-rendered Gutenberg blocks, contact page, and security packages.
 * Version: 1.55.2
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * Author: Site Developer
 * Text Domain: site-blocks
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SITE_BLOCKS_VERSION', '1.55.2' );
define( 'SITE_BLOCKS_FILE', __FILE__ );
define( 'SITE_BLOCKS_DIR', plugin_dir_path( __FILE__ ) );
define( 'SITE_BLOCKS_URL', plugin_dir_url( __FILE__ ) );

require_once SITE_BLOCKS_DIR . 'inc/asset-helpers.php';
require_once SITE_BLOCKS_DIR . 'inc/safeguard-media-library.php';
require_once SITE_BLOCKS_DIR . 'inc/mdi-icons.php';
require_once SITE_BLOCKS_DIR . 'inc/register-blocks.php';
require_once SITE_BLOCKS_DIR . 'inc/setup-pages.php';
require_once SITE_BLOCKS_DIR . 'inc/form-handler.php';
require_once SITE_BLOCKS_DIR . 'inc/cpt-security-package.php';
require_once SITE_BLOCKS_DIR . 'inc/package-meta.php';
require_once SITE_BLOCKS_DIR . 'inc/package-helpers.php';
require_once SITE_BLOCKS_DIR . 'inc/package-templates.php';
require_once SITE_BLOCKS_DIR . 'inc/safeguard-homepage-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/safeguard-chrome.php';
require_once SITE_BLOCKS_DIR . 'inc/safeguard-footer.php';
require_once SITE_BLOCKS_DIR . 'inc/alarm-systems-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/contact-page-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/about-page-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/ajax-alarm-systems-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/paradox-m25-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/akuvox-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/tech-brand.php';
require_once SITE_BLOCKS_DIR . 'inc/cctv-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/intercom-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/access-control-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/physical-security-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/electronic-security-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/manpower-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/monitoring-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/enterprise-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/cpt-enterprise-insight.php';
require_once SITE_BLOCKS_DIR . 'inc/cpt-case-study.php';
require_once SITE_BLOCKS_DIR . 'inc/article-template.php';
require_once SITE_BLOCKS_DIR . 'inc/article-samples.php';
require_once SITE_BLOCKS_DIR . 'inc/enterprise-insight-templates.php';
require_once SITE_BLOCKS_DIR . 'inc/safeguard-media.php';
require_once SITE_BLOCKS_DIR . 'inc/safeguard-silo-setup.php';
require_once SITE_BLOCKS_DIR . 'inc/pillar-hero.php';
require_once SITE_BLOCKS_DIR . 'inc/safeguard-ajax-hero.php';
require_once SITE_BLOCKS_DIR . 'inc/safeguard-sections.php';
require_once SITE_BLOCKS_DIR . 'inc/safeguard-process-flow.php';
require_once SITE_BLOCKS_DIR . 'inc/safeguard-process-flow-configs.php';
require_once SITE_BLOCKS_DIR . 'inc/ajax-calculator.php';
require_once SITE_BLOCKS_DIR . 'inc/manpower-calculator.php';
require_once SITE_BLOCKS_DIR . 'inc/safeguard-portfolio-brands.php';
require_once SITE_BLOCKS_DIR . 'inc/safeguard-logo-marquee.php';
$secrets_bootstrap = SITE_BLOCKS_DIR . 'inc/cheapalarms-secrets-bootstrap.php';
if ( is_readable( $secrets_bootstrap ) ) {
	require_once $secrets_bootstrap;
}

/**
 * Enqueue compiled block styles.
 */
function site_blocks_enqueue_assets(): void {
	$load_styles = is_front_page()
		|| is_post_type_archive( 'security_package' )
		|| is_singular( 'security_package' )
		|| is_tax( 'package_type' )
		|| has_block( 'site/package-grid' )
		|| has_block( 'site/home-hero' );

	if ( ! $load_styles ) {
		return;
	}

	wp_enqueue_style(
		'site-blocks-main',
		SITE_BLOCKS_URL . 'assets/css/main.css',
		array( 'kadence-child-main' ),
		SITE_BLOCKS_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'site_blocks_enqueue_assets', 25 );

/**
 * Plugin activation.
 */
function site_blocks_activate(): void {
	site_blocks_register_security_package_cpt();
	site_blocks_seed_package_types();
	site_blocks_create_contact_page();
	site_blocks_create_about_page();
	site_blocks_create_home_page();
	site_blocks_create_alarm_systems_page();
	site_blocks_create_ajax_alarm_systems_page();
	site_blocks_create_paradox_m25_page();
	site_blocks_create_akuvox_page();
	site_blocks_create_tech_brand_pages();
	site_blocks_create_cctv_page();
	site_blocks_create_intercom_systems_page();
	site_blocks_create_intercoms_redirect_page();
	site_blocks_create_access_control_page();
	site_blocks_create_physical_security_pages();
	site_blocks_create_electronic_security_pages();
	site_blocks_create_manpower_pages();
	site_blocks_create_monitoring_pages();
	site_blocks_create_enterprise_pages();
	site_blocks_register_enterprise_insight_cpt();
	site_blocks_seed_enterprise_insight_posts();
	site_blocks_create_ajax_calculator_page();
	site_blocks_create_manpower_calculator_page();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'site_blocks_activate' );

/**
 * Refresh Kadence disable flags on existing Safeguard pages.
 */
function site_blocks_refresh_safeguard_page_meta(): void {
	$slugs = array( 'home', 'contact', 'about', 'alarm-systems', 'brands', 'brands/ajax-alarm-systems', 'brands/paradox-m25', 'brands/akuvox', 'brands/milesight', 'brands/nx-witness', 'brands/hanwha-vision', 'ajax-calculator', 'manpower-calculator', 'cctv-security-cameras', 'intercom-systems', 'access-control', 'physical-security', 'physical-security/static-guards', 'physical-security/mobile-patrols', 'electronic-security', 'manpower', 'monitoring', 'monitoring/back-to-base', 'monitoring/virtual-patrol', 'monitoring/solar-cameras-monitoring', 'enterprise-solutions', 'safeguard-solutions' );

	foreach ( $slugs as $slug ) {
		$page = get_page_by_path( $slug );

		if ( $page instanceof WP_Post ) {
			site_blocks_apply_safeguard_page_meta( (int) $page->ID );
		}
	}
}

/**
 * Run upgrades when plugin version changes (e.g. homepage added).
 */
function site_blocks_maybe_upgrade(): void {
	$stored = get_option( 'site_blocks_db_version', '' );

	if ( $stored === SITE_BLOCKS_VERSION ) {
		return;
	}

	if ( function_exists( 'site_blocks_create_home_page' ) ) {
		site_blocks_create_home_page();
	}

	if ( function_exists( 'site_blocks_create_contact_page' ) ) {
		site_blocks_create_contact_page();
	}

	if ( function_exists( 'site_blocks_create_about_page' ) ) {
		site_blocks_create_about_page();
	}

	if ( function_exists( 'site_blocks_create_alarm_systems_page' ) ) {
		site_blocks_create_alarm_systems_page();
	}

	if ( function_exists( 'site_blocks_create_ajax_alarm_systems_page' ) ) {
		site_blocks_create_ajax_alarm_systems_page();
	}

	if ( function_exists( 'site_blocks_create_paradox_m25_page' ) ) {
		site_blocks_create_paradox_m25_page();
	}

	if ( function_exists( 'site_blocks_create_akuvox_page' ) ) {
		site_blocks_create_akuvox_page();
	}

	if ( function_exists( 'site_blocks_create_tech_brand_pages' ) ) {
		site_blocks_create_tech_brand_pages();
	}

	if ( function_exists( 'site_blocks_create_cctv_page' ) ) {
		site_blocks_create_cctv_page();
	}

	if ( function_exists( 'site_blocks_create_intercom_systems_page' ) ) {
		site_blocks_create_intercom_systems_page();
	}

	if ( function_exists( 'site_blocks_create_intercoms_redirect_page' ) ) {
		site_blocks_create_intercoms_redirect_page();
	}

	if ( function_exists( 'site_blocks_create_access_control_page' ) ) {
		site_blocks_create_access_control_page();
	}

	if ( function_exists( 'site_blocks_create_physical_security_pages' ) ) {
		site_blocks_create_physical_security_pages();
	}

	if ( function_exists( 'site_blocks_create_electronic_security_pages' ) ) {
		site_blocks_create_electronic_security_pages();
	}

	if ( function_exists( 'site_blocks_create_manpower_pages' ) ) {
		site_blocks_create_manpower_pages();
	}

	if ( function_exists( 'site_blocks_create_monitoring_pages' ) ) {
		site_blocks_create_monitoring_pages();
	}

	if ( function_exists( 'site_blocks_create_enterprise_pages' ) ) {
		site_blocks_create_enterprise_pages();
	}

	if ( function_exists( 'site_blocks_seed_enterprise_insight_posts' ) ) {
		add_action( 'init', 'site_blocks_seed_enterprise_insight_posts', 15 );
	}

	if ( function_exists( 'site_blocks_create_ajax_calculator_page' ) ) {
		site_blocks_create_ajax_calculator_page();
	}

	if ( function_exists( 'site_blocks_create_manpower_calculator_page' ) ) {
		site_blocks_create_manpower_calculator_page();
	}

	if ( function_exists( 'site_blocks_refresh_safeguard_page_meta' ) ) {
		site_blocks_refresh_safeguard_page_meta();
	}

	update_option( 'site_blocks_db_version', SITE_BLOCKS_VERSION );
	flush_rewrite_rules();
}
add_action( 'init', 'site_blocks_maybe_upgrade', 5 );

/**
 * Keep the quote tools off the public site until live quoting and the portal ship.
 */
function site_blocks_pause_public_quote_tools(): void {
	if ( is_admin() ) {
		return;
	}

	$paused = array( 'get-an-instant-quote', 'design-my-solution', 'ajax-calculator', 'manpower-calculator' );

	if ( is_page( $paused ) ) {
		wp_safe_redirect( home_url( '/contact/' ), 302 );
		exit;
	}
}
add_action( 'template_redirect', 'site_blocks_pause_public_quote_tools' );
