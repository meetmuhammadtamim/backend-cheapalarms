<?php
/**
 * Alarm Systems hero — Electronic Security product stage hero.
 *
 * @package Site_Blocks
 *
 * @var array $attributes Block attributes.
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once SITE_BLOCKS_DIR . 'inc/safeguard-stage-hero.php';

$eyebrow   = isset( $attributes['eyebrow'] ) ? (string) $attributes['eyebrow'] : __( 'Alarm Systems', 'site-blocks' );
$cta_label = isset( $attributes['primaryCtaLabel'] ) ? (string) $attributes['primaryCtaLabel'] : __( 'Request a quote', 'site-blocks' );
$cta_url   = isset( $attributes['primaryCtaUrl'] ) ? (string) $attributes['primaryCtaUrl'] : '/contact/';
$sec_label = isset( $attributes['secondaryCtaLabel'] ) ? (string) $attributes['secondaryCtaLabel'] : __( 'Call 1300 225 276', 'site-blocks' );
$sec_url   = isset( $attributes['secondaryCtaUrl'] ) ? (string) $attributes['secondaryCtaUrl'] : 'tel:1300225276';

site_blocks_render_stage_hero(
	array(
		'id'              => 'sg-alarm-hero-heading',
		'class'           => 'sg-alarm-systems-hero',
		'breadcrumb'      => array(
			array(
				'label' => __( 'Home', 'site-blocks' ),
				'url'   => home_url( '/' ),
			),
			array(
				'label' => __( 'Electronic Security', 'site-blocks' ),
				'url'   => home_url( '/electronic-security/' ),
			),
			array(
				'label'   => __( 'Alarm Systems', 'site-blocks' ),
				'current' => true,
			),
		),
		'eyebrow'         => $eyebrow,
		'title'           => __( 'Alarm systems that earn their keep.', 'site-blocks' ),
		'lead'            => __( 'Wireless and smart alarms designed around your property, not an off-the-shelf kit. A technician reviews the scope.', 'site-blocks' ),
		'primary_label'   => $cta_label,
		'primary_url'     => ( 0 === strpos( $cta_url, 'tel:' ) || 0 === strpos( $cta_url, 'http' ) ) ? $cta_url : home_url( $cta_url ),
		'secondary_label' => $sec_label,
		'secondary_url'   => ( 0 === strpos( $sec_url, 'tel:' ) || 0 === strpos( $sec_url, 'http' ) ) ? $sec_url : home_url( $sec_url ),
		'frame_image'     => 'images/alarm/hero.webp',
		'frame_alt'       => __( 'Professionally installed alarm system for a Sydney property', 'site-blocks' ),
	)
);
