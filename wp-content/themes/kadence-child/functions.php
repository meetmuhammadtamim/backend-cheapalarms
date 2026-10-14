<?php
/**
 * Kadence Child — Hallmark contact page support.
 *
 * @package Kadence_Child
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KADENCE_CHILD_VERSION', '1.0.0' );
define( 'KADENCE_CHILD_DIR', get_stylesheet_directory() );
define( 'KADENCE_CHILD_URI', get_stylesheet_directory_uri() );

/**
 * Enqueue child theme styles and fonts.
 */
function kadence_child_enqueue_assets(): void {
	wp_enqueue_style(
		'kadence-child-fonts',
		'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=IBM+Plex+Mono:ital,wght@0,400;0,500;1,400&family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;1,400&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'kadence-child-tokens',
		KADENCE_CHILD_URI . '/assets/css/tokens.css',
		array( 'kadence-child-fonts' ),
		KADENCE_CHILD_VERSION
	);

	wp_enqueue_style(
		'kadence-child-main',
		KADENCE_CHILD_URI . '/assets/css/main.css',
		array( 'kadence-child-tokens' ),
		KADENCE_CHILD_VERSION
	);

	// Title case for headings and buttons, site-wide.
	wp_enqueue_style(
		'kadence-child-typography',
		KADENCE_CHILD_URI . '/assets/css/typography.css',
		array( 'kadence-child-main' ),
		KADENCE_CHILD_VERSION . '.' . (string) filemtime( KADENCE_CHILD_DIR . '/assets/css/typography.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'kadence_child_enqueue_assets', 20 );

/**
 * Apply Hallmark Atelier theme token scope on contact page.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function kadence_child_body_class( array $classes ): array {
	if ( is_front_page() ) {
		$classes[] = 'safeguard-homepage';
	}

	if ( is_page( 'contact' ) ) {
		$classes[] = 'hallmark-contact';
		$classes[] = 'is-contact-page';
	}

	if ( is_page( 'ajax-calculator' ) ) {
		$classes[] = 'sg-ajax-calculator-page';
	}

	return $classes;
}
add_filter( 'body_class', 'kadence_child_body_class' );

/**
 * Set data-theme on html for contact page.
 */
function kadence_child_html_data_theme(): void {
	if ( is_page( 'contact' ) ) {
		echo '<script>document.documentElement.setAttribute("data-theme","atelier");</script>' . "\n";
	}
}
add_action( 'wp_head', 'kadence_child_html_data_theme', 1 );

/**
 * Configure Kadence layout for contact page.
 *
 * @param array<string, string> $layout Layout settings.
 * @return array<string, string>
 */
function kadence_child_contact_layout( array $layout ): array {
	if ( is_front_page() || is_page( 'contact' ) || is_page( 'ajax-calculator' ) ) {
		$layout['layout']  = 'fullwidth';
		$layout['boxed']   = 'unboxed';
		$layout['title']   = 'hide';
		$layout['sidebar'] = 'disable';
	}
	return $layout;
}
add_filter( 'kadence_post_layout', 'kadence_child_contact_layout' );

