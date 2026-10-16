<?php
/**
 * Front-end performance for the homepage.
 *
 * - One stylesheet request instead of fifteen (assets/css/safeguard-home.bundle.css,
 *   built by tools/build-home-css.py). The source stylesheets are untouched and
 *   still load on every other page.
 * - Self-hosted fonts (assets/fonts) instead of the Google Fonts stylesheets,
 *   which also removes the unused child-theme font families from the homepage.
 * - A preload hint for the hero image, the largest contentful paint element.
 * - Long cache lifetimes for static files under uploads/safeguard.
 * - WordPress emoji detection script and styles off.
 *
 * Every piece falls back to the previous behaviour when its file is missing.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stylesheet handles the bundle replaces, in print order.
 *
 * @return string[]
 */
function site_blocks_home_bundle_handles(): array {
	return array(
		'site-blocks-main',
		'safeguard-home',
		'safeguard-process-flow',
		'safeguard-why',
		'safeguard-details',
		'safeguard-coverage',
		'safeguard-quality',
		'safeguard-connected',
		'safeguard-blog',
		'safeguard-process',
		'safeguard-brands',
		'safeguard-cases',
		'safeguard-motion',
		'safeguard-signature-icons',
		'safeguard-logo-marquee',
	);
}

/**
 * Whether the homepage should use the bundle: front page and the built file exists.
 */
function site_blocks_home_bundle_active(): bool {
	return is_front_page()
		&& is_readable( SITE_BLOCKS_DIR . 'assets/css/safeguard-home.bundle.css' )
		&& is_readable( SITE_BLOCKS_DIR . 'assets/fonts/safeguard-fonts.css' );
}

/**
 * Swap the individual stylesheet tags for the one bundle tag.
 *
 * The bundle takes the place of the first replaced tag, so the cascade order
 * is unchanged. The Google Fonts stylesheets are dropped on the homepage.
 *
 * @param string $tag    Link tag HTML.
 * @param string $handle Style handle.
 */
function site_blocks_home_bundle_tag( string $tag, string $handle ): string {
	static $printed = false;

	if ( ! site_blocks_home_bundle_active() ) {
		return $tag;
	}

	if ( in_array( $handle, array( 'safeguard-home-fonts', 'kadence-child-fonts' ), true ) ) {
		return '';
	}

	if ( ! in_array( $handle, site_blocks_home_bundle_handles(), true ) ) {
		return $tag;
	}

	if ( $printed ) {
		return '';
	}

	$printed = true;
	$href    = add_query_arg( 'ver', SITE_BLOCKS_VERSION, SITE_BLOCKS_URL . 'assets/css/safeguard-home.bundle.css' );

	return '<link rel="stylesheet" id="safeguard-home-bundle-css" href="' . esc_url( $href ) . '" media="all" />' . "\n";
}
add_filter( 'style_loader_tag', 'site_blocks_home_bundle_tag', 10, 2 );

/**
 * Preload the hero image (the largest contentful paint element) with its
 * responsive candidates, so the browser fetches the right size straight away.
 */
function site_blocks_preload_hero_image(): void {
	if ( ! is_front_page() || ! function_exists( 'site_blocks_hero_image_set' ) ) {
		return;
	}

	$set = site_blocks_hero_image_set();
	printf(
		'<link rel="preload" as="image" href="%1$s" imagesrcset="%2$s" imagesizes="%3$s" fetchpriority="high" />' . "\n",
		esc_url( $set['src'] ),
		esc_attr( $set['srcset'] ),
		esc_attr( $set['sizes'] )
	);
}
add_action( 'wp_head', 'site_blocks_preload_hero_image', 1 );

/**
 * Hero image candidates. Widths match files made by tools/make-image-variants.py;
 * a missing file is left out and the full image is always the last candidate.
 *
 * @return array{src: string, srcset: string, sizes: string, width: int, height: int}
 */
function site_blocks_hero_image_set(): array {
	$candidates = array(
		480  => 'images/hero/home-rack-480.webp',
		800  => 'images/hero/home-rack-800.webp',
		1100 => 'images/hero/home-rack-1100.webp',
		1500 => 'images/hero/home-rack.webp',
	);

	$parts = array();
	foreach ( $candidates as $width => $path ) {
		if ( 1500 !== $width && ! site_blocks_asset_file_readable( $path ) ) {
			continue;
		}
		$parts[] = site_blocks_asset_url( $path ) . ' ' . $width . 'w';
	}

	return array(
		'src'    => site_blocks_asset_url( 'images/hero/home-rack.webp' ),
		'srcset' => implode( ', ', $parts ),
		// Container-wide on desktop (1200px minus its padding), 92vw on phones.
		'sizes'  => '(min-width: 1240px) 1165px, 92vw',
		'width'  => 1500,
		'height' => 750,
	);
}

/**
 * Write a cache-lifetime rule next to the shared photos so browsers keep them.
 *
 * URLs built by site_blocks_asset_url() carry the file's modified time, so a
 * changed photo gets a new URL and a long lifetime is safe.
 */
function site_blocks_ensure_asset_cache_rules(): void {
	if ( SITE_BLOCKS_VERSION === get_option( 'site_blocks_cache_rules_version' ) ) {
		return;
	}

	$dir = site_blocks_safeguard_upload_disk( '' );
	if ( '' !== $dir && is_dir( $dir ) && is_writable( $dir ) ) {
		$rules = "# Written by the site-blocks plugin: long browser cache for static files.\n"
			. "<IfModule mod_expires.c>\n"
			. "ExpiresActive On\n"
			. "ExpiresByType image/webp \"access plus 1 year\"\n"
			. "ExpiresByType image/png \"access plus 1 year\"\n"
			. "ExpiresByType image/jpeg \"access plus 1 year\"\n"
			. "ExpiresByType image/svg+xml \"access plus 1 year\"\n"
			. "</IfModule>\n"
			. "<IfModule mod_headers.c>\n"
			. "<FilesMatch \"\\.(webp|png|jpe?g|svg|avif)$\">\n"
			. "Header set Cache-Control \"public, max-age=31536000, immutable\"\n"
			. "</FilesMatch>\n"
			. "</IfModule>\n";

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		file_put_contents( trailingslashit( $dir ) . '.htaccess', $rules );
	}

	update_option( 'site_blocks_cache_rules_version', SITE_BLOCKS_VERSION, false );
}
add_action( 'init', 'site_blocks_ensure_asset_cache_rules', 30 );

/**
 * Turn the WordPress emoji detection script and styles off on the front end.
 */
function site_blocks_disable_emoji_assets(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'site_blocks_disable_emoji_assets', 1 );
