<?php
/**
 * Hero artwork for the three technology brand pages.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Milesight hero product shot.
 */
function site_blocks_tech_brand_visual_milesight(): void {
	$src = site_blocks_asset_url( 'images/brands/milesight-dome-hero.webp' );
	?>
	<img
		src="<?php echo esc_url( $src ); ?>"
		width="1122"
		height="1402"
		alt="<?php esc_attr_e( 'Milesight AI Pro dome camera.', 'site-blocks' ); ?>"
		decoding="async"
	/>
	<?php
}

/**
 * Hanwha Vision hero product shot.
 */
function site_blocks_tech_brand_visual_hanwha(): void {
	$src = site_blocks_asset_url( 'images/brands/hanwha-qno-8010r.webp' );
	?>
	<img
		class="sg-tech__product-shot"
		src="<?php echo esc_url( $src ); ?>"
		width="1688"
		height="1274"
		alt="<?php esc_attr_e( 'Hanwha Vision QNO-8010R bullet camera.', 'site-blocks' ); ?>"
		decoding="async"
	/>
	<?php
}

/**
 * NX Witness hero shot. Transparent PNG on the page navy.
 */
function site_blocks_tech_brand_visual_nx(): void {
	$src = site_blocks_asset_url( 'images/brands/nx-witness-hero.png' );
	?>
	<img
		class="sg-tech__shot"
		src="<?php echo esc_url( $src ); ?>"
		width="1586"
		height="992"
		alt="<?php esc_attr_e( 'NX Witness client showing a vehicle search across site cameras.', 'site-blocks' ); ?>"
		decoding="async"
	/>
	<span class="sg-tech__glow" aria-hidden="true"></span>
	<?php
}
