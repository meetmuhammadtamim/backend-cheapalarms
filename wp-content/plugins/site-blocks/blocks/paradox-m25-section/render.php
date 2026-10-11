<?php
/**
 * Paradox M25 section block (config-driven).
 *
 * @package Site_Blocks
 *
 * @var array $attributes Block attributes.
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once SITE_BLOCKS_DIR . 'inc/paradox-m25-render.php';

$section = isset( $attributes['section'] ) ? sanitize_key( (string) $attributes['section'] ) : '';

if ( '' === $section ) {
	return;
}

site_blocks_render_paradox_m25_section( $section );
