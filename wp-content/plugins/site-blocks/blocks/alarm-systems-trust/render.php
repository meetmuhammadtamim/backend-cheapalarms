<?php
/**
 * Alarm Systems, trust panel.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once SITE_BLOCKS_DIR . 'inc/mdi-icons.php';

/**
 * @param string $icon MDI icon key.
 */
function site_blocks_alarm_trust_mdi_icon( string $icon ): void {
	site_blocks_icon( $icon, 24 );
}

site_blocks_render_trust_panel(
	array(
		'heading_id'    => 'sg-alarm-trust-heading',
		'section_class' => 'sg-alarm-trust',
		'title_before'  => __( 'Designed And Supported By Experienced ', 'site-blocks' ),
		'title_accent'  => __( 'Technicians', 'site-blocks' ),
		'items'         => array(
			array(
				'title' => __( 'Licensed And Accredited', 'site-blocks' ),
				'desc'  => __( 'Licensed installers, ASIAL member.', 'site-blocks' ),
				'icon'  => 'badge-check',
			),
			array(
				'title' => __( 'Residential And Commercial', 'site-blocks' ),
				'desc'  => __( 'Homes to multi-site business systems.', 'site-blocks' ),
				'icon'  => 'building-2',
			),
			array(
				'title' => __( 'Wireless And Hybrid Options', 'site-blocks' ),
				'desc'  => __( 'Ajax, traditional and upgrade pathways.', 'site-blocks' ),
				'icon'  => 'wifi',
			),
			array(
				'title' => __( 'Support After Install', 'site-blocks' ),
				'desc'  => __( 'Servicing and monitoring.', 'site-blocks' ),
				'icon'  => 'headset',
			),
		),
		'icon_renderer' => 'site_blocks_alarm_trust_mdi_icon',
	)
);
