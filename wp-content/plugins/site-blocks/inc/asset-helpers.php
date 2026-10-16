<?php
/**
 * Plugin asset URL helpers.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disk path for a photo stored under wp-content/uploads/safeguard/.
 */
function site_blocks_safeguard_upload_disk( string $relative_path ): string {
	if ( ! function_exists( 'wp_upload_dir' ) ) {
		return '';
	}

	$uploads = wp_upload_dir();
	if ( ! empty( $uploads['error'] ) ) {
		return '';
	}

	$relative_path = ltrim( str_replace( '\\', '/', $relative_path ), '/' );

	return trailingslashit( (string) $uploads['basedir'] ) . 'safeguard/' . $relative_path;
}

/**
 * Public WordPress URL for a photo under wp-content/uploads/safeguard/.
 */
function site_blocks_safeguard_upload_url( string $relative_path ): string {
	if ( ! function_exists( 'wp_upload_dir' ) ) {
		return '';
	}

	$uploads = wp_upload_dir();
	if ( ! empty( $uploads['error'] ) ) {
		return '';
	}

	$relative_path = ltrim( str_replace( '\\', '/', $relative_path ), '/' );
	$segments      = explode( '/', 'safeguard/' . $relative_path );
	$encoded       = implode( '/', array_map( 'rawurlencode', $segments ) );

	return trailingslashit( (string) $uploads['baseurl'] ) . $encoded;
}

/**
 * Whether the photo is already in uploads/safeguard or still in the plugin folder.
 */
function site_blocks_asset_file_readable( string $relative_path ): bool {
	$relative_path = ltrim( str_replace( '\\', '/', $relative_path ), '/' );
	$upload        = site_blocks_safeguard_upload_disk( $relative_path );

	if ( '' !== $upload && is_readable( $upload ) ) {
		return true;
	}

	return is_readable( SITE_BLOCKS_DIR . 'assets/' . $relative_path );
}

/**
 * Prefer a sibling WebP file when one exists in uploads or in the plugin folder.
 *
 * @param string $relative_path Path relative to assets/, e.g. images/hero/house.png.
 */
function site_blocks_resolve_asset_path( string $relative_path ): string {
	$relative_path = ltrim( str_replace( '\\', '/', $relative_path ), '/' );

	if ( ! preg_match( '/\.(png|jpe?g)$/i', $relative_path ) ) {
		return $relative_path;
	}

	$webp_path = preg_replace( '/\.(png|jpe?g)$/i', '.webp', $relative_path );
	if ( is_string( $webp_path ) && site_blocks_asset_file_readable( $webp_path ) ) {
		return $webp_path;
	}

	return $relative_path;
}

/**
 * Build a public URL for a file under site-blocks/assets/.
 *
 * Raster paths (.png, .jpg, .jpeg) automatically use a sibling .webp file when present.
 * Image paths under images/ use wp-content/uploads/safeguard/ when that file is on disk.
 *
 * @param string $relative_path Path relative to assets/, e.g. images/hero/house.png.
 */
function site_blocks_asset_url( string $relative_path ): string {
	$relative_path = site_blocks_resolve_asset_path( $relative_path );

	if ( 0 === strpos( $relative_path, 'images/' ) ) {
		$upload_disk = site_blocks_safeguard_upload_disk( $relative_path );
		$upload_url  = site_blocks_safeguard_upload_url( $relative_path );
		if ( '' !== $upload_disk && is_readable( $upload_disk ) && '' !== $upload_url ) {
			return esc_url( add_query_arg( 'v', (string) (int) filemtime( $upload_disk ), $upload_url ) );
		}
	}

	$segments = explode( '/', $relative_path );
	$encoded  = implode( '/', array_map( 'rawurlencode', $segments ) );
	$url      = SITE_BLOCKS_URL . 'assets/' . $encoded;

	// A changed file gets a new URL, so browsers can keep it for a long time.
	if ( 0 === strpos( $relative_path, 'images/' ) ) {
		$plugin_disk = SITE_BLOCKS_DIR . 'assets/' . $relative_path;
		if ( is_readable( $plugin_disk ) ) {
			$url = add_query_arg( 'v', (string) (int) filemtime( $plugin_disk ), $url );
		}
	}

	return esc_url( $url );
}
