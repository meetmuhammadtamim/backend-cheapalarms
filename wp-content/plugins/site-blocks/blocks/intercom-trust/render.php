<?php
/**
 * Intercom, why Safeguard trust panel.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once SITE_BLOCKS_DIR . 'inc/intercom-icons.php';

site_blocks_render_trust_panel(
	array(
		'heading_id'    => 'sg-intercom-trust-heading',
		'section_class' => 'sg-intercom-trust',
		'panel_class'   => 'sg-alarm-why__panel sg-cctv-trust__panel',
		'title_before'  => __( 'Designed And Supported By Experienced ', 'site-blocks' ),
		'title_accent'  => __( 'Technicians', 'site-blocks' ),
		'items'         => array(
			array(
				'title' => __( 'Licensed And Accredited', 'site-blocks' ),
				'desc'  => __( 'Master Licence · ASIAL member.', 'site-blocks' ),
				'icon'  => 'weatherproof.png',
			),
			array(
				'title' => __( 'Homes, Strata And Commercial', 'site-blocks' ),
				'desc'  => __( 'From a single front door to multi-unit buildings.', 'site-blocks' ),
				'icon'  => 'property-coverage.png',
			),
			array(
				'title' => __( 'Built For Complex Sites', 'site-blocks' ),
				'desc'  => __( 'Heritage terraces, long gate runs, existing-cabling upgrades.', 'site-blocks' ),
				'icon'  => 'home-camera.png',
			),
			array(
				'title' => __( 'Support After Install', 'site-blocks' ),
				'desc'  => __( 'App setup, user handover, maintenance and monitoring.', 'site-blocks' ),
				'icon'  => 'support.png',
			),
		),
		'icon_renderer' => 'site_blocks_intercom_icon',
	)
);
