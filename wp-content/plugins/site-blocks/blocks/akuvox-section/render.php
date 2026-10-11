<?php
/**
 * Akuvox section block (config-driven).
 *
 * @package Site_Blocks
 *
 * @var array $attributes Block attributes.
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once SITE_BLOCKS_DIR . 'inc/akuvox-render.php';

$section = isset( $attributes['section'] ) ? sanitize_key( (string) $attributes['section'] ) : '';

if ( '' === $section ) {
	return;
}

site_blocks_render_akuvox_section( $section );
