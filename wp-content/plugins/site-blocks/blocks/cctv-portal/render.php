<?php
/**
 * CCTV, customer portal.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

site_blocks_render_portal_band(
	array(
		'heading_id'    => 'sg-cctv-portal-heading',
		'section_class' => 'sg-cctv-portal',
		'title_before'  => __( 'Your Quote, Photos And Approvals In ', 'site-blocks' ),
		'title_accent'  => __( 'One Place', 'site-blocks' ),
		'intro'         => __( 'Start online, then manage your CCTV quote in our secure portal, no waiting days for a callback.', 'site-blocks' ),
		'bullets'       => array(
			__( 'Track your quote progress in real time', 'site-blocks' ),
			__( 'Upload site photos and documents', 'site-blocks' ),
			__( 'Message our team and approve your estimate', 'site-blocks' ),
		),
	)
);
