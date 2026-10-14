<?php
/**
 * Brands index block.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once SITE_BLOCKS_DIR . 'inc/tech-brand.php';

site_blocks_render_brands_index();
