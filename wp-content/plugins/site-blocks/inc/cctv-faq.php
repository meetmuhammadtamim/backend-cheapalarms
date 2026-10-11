<?php
/**
 * CCTV category page FAQ content.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<int, array{q: string, a: string}>
 */
function site_blocks_get_cctv_faq_items(): array {
	return array(
		array(
			'q' => __( 'How many cameras do I need?', 'site-blocks' ),
			'a' => __( 'It depends on your layout and what you need to see, placement beats camera count. We work that out in your estimate.', 'site-blocks' ),
		),
		array(
			'q' => __( 'Do I need internet?', 'site-blocks' ),
			'a' => __( 'No. Recording works offline. Internet adds remote viewing and alerts on your phone.', 'site-blocks' ),
		),
		array(
			'q' => __( 'Is CCTV legal on my property?', 'site-blocks' ),
			'a' => __( 'Usually yes on your own property, avoid filming neighbours\' private spaces. Strata may need approval; we help you plan compliant placement.', 'site-blocks' ),
		),
		array(
			'q' => __( 'Wired or wireless?', 'site-blocks' ),
			'a' => __( 'We usually recommend wired PoE for reliability. Wireless suits spots where cabling isn\'t practical.', 'site-blocks' ),
		),
		array(
			'q' => __( 'Is the quote the final price?', 'site-blocks' ),
			'a' => __( 'A technician reviews the scope before you approve, so there are no surprises on the day.', 'site-blocks' ),
		),
		array(
			'q' => __( 'Can I talk to someone?', 'site-blocks' ),
			'a' => __( 'Yes. Call 1300 225 276 or request a quote and we will go through the design with you.', 'site-blocks' ),
		),
	);
}
