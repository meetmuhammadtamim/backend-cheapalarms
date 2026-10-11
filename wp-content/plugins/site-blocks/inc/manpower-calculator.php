<?php
/**
 * ManPower guard quote calculator (static HTML + WP page).
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public URL for the standalone calculator HTML.
 */
function site_blocks_manpower_calculator_asset_url(): string {
	return site_blocks_asset_url( 'calculators/manpower-calculator/index.html' );
}

/**
 * Shortcode: full-width iframe embed for the ManPower guard quote calculator.
 *
 * @return string
 */
function site_blocks_manpower_calculator_shortcode(): string {
	$url = site_blocks_manpower_calculator_asset_url();
	if ( function_exists( 'rest_url' ) ) {
		$url = add_query_arg( 'apiBase', rawurlencode( rest_url( 'ca/v1' ) ), $url );
	}

	return sprintf(
		'<div class="sg-manpower-calculator-embed"><iframe class="sg-manpower-calculator-frame" src="%s" title="%s" loading="eager"></iframe></div>',
		esc_url( $url ),
		esc_attr__( 'Security guard quote calculator', 'site-blocks' )
	);
}
add_shortcode( 'sg_manpower_calculator', 'site_blocks_manpower_calculator_shortcode' );

/**
 * Whether the ManPower calculator page is being viewed.
 */
function site_blocks_is_manpower_calculator_page(): bool {
	return is_page( 'manpower-calculator' );
}

/**
 * Enqueue embed layout styles on the calculator page.
 */
function site_blocks_manpower_calculator_enqueue(): void {
	if ( ! site_blocks_is_manpower_calculator_page() ) {
		return;
	}

	$deps = array();
	if ( wp_style_is( 'safeguard-home', 'registered' ) || wp_style_is( 'safeguard-home', 'enqueued' ) ) {
		$deps[] = 'safeguard-home';
	}

	wp_enqueue_style(
		'site-blocks-manpower-calculator',
		SITE_BLOCKS_URL . 'assets/css/manpower-calculator-embed.css',
		$deps,
		SITE_BLOCKS_VERSION
	);

	wp_enqueue_script(
		'site-blocks-manpower-calculator',
		SITE_BLOCKS_URL . 'assets/js/manpower-calculator-embed.js',
		array(),
		SITE_BLOCKS_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'site_blocks_manpower_calculator_enqueue', 35 );

/**
 * Return block markup for the ManPower calculator page.
 *
 * @return string
 */
function site_blocks_get_manpower_calculator_page_content(): string {
	$pattern_file = SITE_BLOCKS_DIR . 'patterns/manpower-calculator-page.php';

	if ( ! file_exists( $pattern_file ) ) {
		return '<!-- wp:shortcode -->[sg_manpower_calculator]<!-- /wp:shortcode -->';
	}

	$pattern = include $pattern_file;

	return is_array( $pattern ) && isset( $pattern['content'] ) ? (string) $pattern['content'] : '';
}

/**
 * Create or update the published ManPower calculator page.
 */
function site_blocks_create_manpower_calculator_page(): void {
	$content = site_blocks_get_manpower_calculator_page_content();

	if ( '' === $content ) {
		return;
	}

	$existing = get_page_by_path( 'manpower-calculator' );

	$page_data = array(
		'post_title'   => __( 'Security Guard Quote Calculator', 'site-blocks' ),
		'post_name'    => 'manpower-calculator',
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => 1,
	);

	if ( $existing instanceof WP_Post ) {
		$page_data['ID'] = $existing->ID;
		$page_id         = wp_update_post( $page_data, true );
	} else {
		$page_id = wp_insert_post( $page_data, true );
	}

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return;
	}

	site_blocks_apply_safeguard_page_meta( (int) $page_id );
}
