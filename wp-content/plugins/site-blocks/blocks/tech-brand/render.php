<?php
/**
 * Technology brand page block.
 *
 * @package Site_Blocks
 *
 * @var array $attributes Block attributes.
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once SITE_BLOCKS_DIR . 'inc/tech-brand.php';

$brand = isset( $attributes['brand'] ) ? sanitize_title( (string) $attributes['brand'] ) : '';

if ( '' === $brand ) {
	return;
}

site_blocks_render_tech_brand( $brand );
