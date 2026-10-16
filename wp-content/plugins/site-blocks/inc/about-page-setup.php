<?php
/**
 * About page assets and body class.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the About page is being viewed.
 */
function site_blocks_is_about_page(): bool {
	return is_page( 'about' );
}

/**
 * Enqueue Safeguard styles for the About page.
 */
function site_blocks_enqueue_about_page_assets(): void {
	if ( ! site_blocks_is_about_page() ) {
		return;
	}

	site_blocks_enqueue_safeguard_fonts( 'safeguard-about-fonts' );
	$dep = site_blocks_enqueue_safeguard_style( 'safeguard-home', 'safeguard-home.css', 'safeguard-about-fonts' );
	site_blocks_enqueue_safeguard_style( 'safeguard-about', 'about.css', $dep );
	site_blocks_enqueue_safeguard_home_script();

	// Scroll reveals, the same system as the homepage.
	wp_enqueue_style(
		'safeguard-motion',
		SITE_BLOCKS_URL . 'assets/css/safeguard-motion.css',
		array( 'safeguard-about' ),
		SITE_BLOCKS_VERSION
	);
	wp_enqueue_script(
		'safeguard-motion',
		SITE_BLOCKS_URL . 'assets/js/safeguard-motion.js',
		array(),
		SITE_BLOCKS_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'site_blocks_enqueue_about_page_assets', 30 );

/**
 * Body classes for the About page.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function site_blocks_about_page_body_class( array $classes ): array {
	if ( site_blocks_is_about_page() ) {
		$classes[] = 'safeguard-homepage';
		$classes[] = 'sg-about-page';
	}

	return $classes;
}
add_filter( 'body_class', 'site_blocks_about_page_body_class' );
