<?php
/**
 * Paradox M25 page detection (assets via safeguard-silo-setup.php).
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the Paradox M25 landing page is being viewed.
 */
function site_blocks_is_paradox_m25_page(): bool {
	return is_page( 'paradox-m25' );
}
