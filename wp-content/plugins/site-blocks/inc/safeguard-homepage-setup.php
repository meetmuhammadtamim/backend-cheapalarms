<?php
/**
 * Safeguard homepage, hide theme chrome, enqueue assets.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the Safeguard full-page homepage is active.
 */
function site_blocks_is_safeguard_homepage(): bool {
	return is_front_page();
}

/**
 * Enqueue Safeguard homepage CSS and JS.
 */
function site_blocks_enqueue_safeguard_home(): void {
	if ( ! site_blocks_is_safeguard_homepage() ) {
		return;
	}

	wp_enqueue_style(
		'safeguard-home-fonts',
		'https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@400;500;600;700&family=Montserrat:wght@500;600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'safeguard-home',
		SITE_BLOCKS_URL . 'assets/css/safeguard-home.css',
		array( 'safeguard-home-fonts' ),
		SITE_BLOCKS_VERSION
	);

	if ( function_exists( 'site_blocks_enqueue_process_flow_styles' ) ) {
		site_blocks_enqueue_process_flow_styles( 'safeguard-home' );
	}

	wp_enqueue_script(
		'safeguard-home',
		SITE_BLOCKS_URL . 'assets/js/safeguard-home.js',
		array(),
		SITE_BLOCKS_VERSION,
		true
	);

	wp_enqueue_style(
		'safeguard-why',
		SITE_BLOCKS_URL . 'assets/css/safeguard-why.css',
		array( 'safeguard-home' ),
		SITE_BLOCKS_VERSION
	);

	wp_enqueue_style(
		'safeguard-details',
		SITE_BLOCKS_URL . 'assets/css/safeguard-details.css',
		array( 'safeguard-home' ),
		SITE_BLOCKS_VERSION
	);

	wp_enqueue_style(
		'safeguard-coverage',
		SITE_BLOCKS_URL . 'assets/css/safeguard-coverage.css',
		array( 'safeguard-home' ),
		SITE_BLOCKS_VERSION
	);

	wp_enqueue_style(
		'safeguard-quality',
		SITE_BLOCKS_URL . 'assets/css/safeguard-quality.css',
		array( 'safeguard-home' ),
		SITE_BLOCKS_VERSION
	);

	wp_enqueue_style(
		'safeguard-connected',
		SITE_BLOCKS_URL . 'assets/css/safeguard-connected.css',
		array( 'safeguard-home' ),
		SITE_BLOCKS_VERSION
	);

	wp_enqueue_script(
		'safeguard-connected',
		SITE_BLOCKS_URL . 'assets/js/safeguard-connected.js',
		array(),
		SITE_BLOCKS_VERSION,
		true
	);

	wp_enqueue_style(
		'safeguard-blog',
		SITE_BLOCKS_URL . 'assets/css/safeguard-blog.css',
		array( 'safeguard-home' ),
		SITE_BLOCKS_VERSION
	);

	wp_enqueue_style(
		'safeguard-process',
		SITE_BLOCKS_URL . 'assets/css/safeguard-process.css',
		array( 'safeguard-home' ),
		SITE_BLOCKS_VERSION
	);

	wp_enqueue_style(
		'safeguard-brands',
		SITE_BLOCKS_URL . 'assets/css/safeguard-brands.css',
		array( 'safeguard-home' ),
		SITE_BLOCKS_VERSION
	);

	wp_enqueue_script(
		'safeguard-brands',
		SITE_BLOCKS_URL . 'assets/js/safeguard-brands.js',
		array(),
		SITE_BLOCKS_VERSION,
		true
	);

	wp_enqueue_style(
		'safeguard-cases',
		SITE_BLOCKS_URL . 'assets/css/safeguard-cases.css',
		array( 'safeguard-home' ),
		SITE_BLOCKS_VERSION
	);

	wp_enqueue_style(
		'safeguard-motion',
		SITE_BLOCKS_URL . 'assets/css/safeguard-motion.css',
		array( 'safeguard-details', 'safeguard-coverage', 'safeguard-quality', 'safeguard-connected' ),
		SITE_BLOCKS_VERSION
	);

	wp_enqueue_script(
		'safeguard-motion',
		SITE_BLOCKS_URL . 'assets/js/safeguard-motion.js',
		array(),
		SITE_BLOCKS_VERSION,
		true
	);

	// Rough Notation (about 4 KB gzipped, self-hosted, deferred) underlines the hero
	// focus phrase.
	wp_enqueue_script(
		'safeguard-rough-notation',
		SITE_BLOCKS_URL . 'assets/js/vendor/rough-notation.iife.js',
		array(),
		'0.5.1',
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	// GSAP core and ScrollTrigger (about 40 KB gzipped together), self-hosted and
	// deferred. Used for the "details" section reveal and parallax.
	wp_enqueue_script(
		'safeguard-gsap',
		SITE_BLOCKS_URL . 'assets/js/vendor/gsap.min.js',
		array(),
		'3.13.0',
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_enqueue_script(
		'safeguard-scrolltrigger',
		SITE_BLOCKS_URL . 'assets/js/vendor/ScrollTrigger.min.js',
		array( 'safeguard-gsap' ),
		'3.13.0',
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_enqueue_script(
		'safeguard-details-motion',
		SITE_BLOCKS_URL . 'assets/js/safeguard-details-motion.js',
		array( 'safeguard-gsap', 'safeguard-scrolltrigger', 'safeguard-rough-notation' ),
		SITE_BLOCKS_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	// GSAP SplitText (about 3.5 KB gzipped): line-by-line headline reveal.
	wp_enqueue_script(
		'safeguard-split-text',
		SITE_BLOCKS_URL . 'assets/js/vendor/SplitText.min.js',
		array( 'safeguard-gsap' ),
		'3.13.0',
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_enqueue_script(
		'safeguard-coverage-motion',
		SITE_BLOCKS_URL . 'assets/js/safeguard-coverage-motion.js',
		array( 'safeguard-gsap', 'safeguard-scrolltrigger', 'safeguard-split-text' ),
		SITE_BLOCKS_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_enqueue_script(
		'safeguard-hero-depth',
		SITE_BLOCKS_URL . 'assets/js/safeguard-hero-depth.js',
		array( 'safeguard-gsap', 'safeguard-scrolltrigger' ),
		SITE_BLOCKS_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_enqueue_script(
		'safeguard-hero-focus',
		SITE_BLOCKS_URL . 'assets/js/safeguard-hero-focus.js',
		array( 'safeguard-rough-notation' ),
		SITE_BLOCKS_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'site_blocks_enqueue_safeguard_home', 30 );

/**
 * Body classes for Safeguard homepage.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function site_blocks_safeguard_body_class( array $classes ): array {
	if ( site_blocks_is_safeguard_homepage() ) {
		$classes[] = 'safeguard-homepage';
		$classes[] = 'is-safeguard-front';
	}
	return $classes;
}
add_filter( 'body_class', 'site_blocks_safeguard_body_class' );
