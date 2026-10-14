<?php
/**
 * Per-page skeleton process flow configs.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return process flow config by key.
 *
 * @return array{
 *   heading_id?: string,
 *   section_class?: string,
 *   eyebrow?: string,
 *   title_before: string,
 *   title_accent: string,
 *   title_after?: string,
 *   steps: array<int, array{label: string, title: string, badge: string, skeleton: string}>
 * }|null
 */
function site_blocks_process_flow_config( string $key ): ?array {
	$configs = site_blocks_process_flow_configs();

	return $configs[ $key ] ?? null;
}

/**
 * All registered process flow configs.
 *
 * @return array<string, array<string, mixed>>
 */
function site_blocks_process_flow_configs(): array {
	return array(
		'monitoring-virtual-patrol' => array(
			'heading_id'    => 'sg-monitoring-vp-flow-heading',
			'section_class' => 'sg-monitoring-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'Remote Patrol Coverage In ', 'site-blocks' ),
			'title_accent'  => __( 'Four Steps', 'site-blocks' ),
			'title_after'   => __( ' from cameras to confirmed activity.', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Schedule Agreed', 'site-blocks' ),
					'badge'    => __( 'Your site profile', 'site-blocks' ),
					'skeleton' => 'schedule',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Live Operator Tours', 'site-blocks' ),
					'badge'    => __( 'Live camera review', 'site-blocks' ),
					'skeleton' => 'tour',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Incidents Flagged', 'site-blocks' ),
					'badge'    => __( 'Keyholders notified', 'site-blocks' ),
					'skeleton' => 'flag',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Report Delivered', 'site-blocks' ),
					'badge'    => __( 'Tour logs ready', 'site-blocks' ),
					'skeleton' => 'report',
				),
			),
		),
		'monitoring-back-to-base' => array(
			'heading_id'    => 'sg-monitoring-b2b-flow-heading',
			'section_class' => 'sg-monitoring-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'Back-To-Base Monitoring In ', 'site-blocks' ),
			'title_accent'  => __( 'Four Steps', 'site-blocks' ),
			'title_after'   => __( ' from alarm to agreed response.', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Alarm Activates', 'site-blocks' ),
					'badge'    => __( 'Sensor triggered', 'site-blocks' ),
					'skeleton' => 'signal',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Signal Sent', 'site-blocks' ),
					'badge'    => __( 'IP or 4G path', 'site-blocks' ),
					'skeleton' => 'tour',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Operator Assesses', 'site-blocks' ),
					'badge'    => __( 'Site profile checked', 'site-blocks' ),
					'skeleton' => 'flag',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Response Executed', 'site-blocks' ),
					'badge'    => __( 'Logged and reported', 'site-blocks' ),
					'skeleton' => 'report',
				),
			),
		),
		'monitoring-solar-cameras-monitoring' => array(
			'heading_id'    => 'sg-monitoring-solar-flow-heading',
			'section_class' => 'sg-monitoring-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'Solar Site Monitoring In ', 'site-blocks' ),
			'title_accent'  => __( 'Four Steps', 'site-blocks' ),
			'title_after'   => __( ' from install to live cover.', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Site Assessment', 'site-blocks' ),
					'badge'    => __( 'Coverage mapped', 'site-blocks' ),
					'skeleton' => 'quote',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Solar Install', 'site-blocks' ),
					'badge'    => __( '4G commissioned', 'site-blocks' ),
					'skeleton' => 'install',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Monitoring Linked', 'site-blocks' ),
					'badge'    => __( 'Alerts configured', 'site-blocks' ),
					'skeleton' => 'flag',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Response Ready', 'site-blocks' ),
					'badge'    => __( 'Plan agreed', 'site-blocks' ),
					'skeleton' => 'report',
				),
			),
		),
		'monitoring-hub-how-it-works' => array(
			'heading_id'    => 'sg-monitoring-how-flow-heading',
			'section_class' => 'sg-monitoring-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'From Alert To ', 'site-blocks' ),
			'title_accent'  => __( 'Action', 'site-blocks' ),
			'title_after'   => __( ' in four steps', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Signal', 'site-blocks' ),
					'badge'    => __( 'Event detected', 'site-blocks' ),
					'skeleton' => 'signal',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Receive', 'site-blocks' ),
					'badge'    => __( 'Centre alerted', 'site-blocks' ),
					'skeleton' => 'tour',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Verify', 'site-blocks' ),
					'badge'    => __( 'Cause confirmed', 'site-blocks' ),
					'skeleton' => 'flag',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Respond', 'site-blocks' ),
					'badge'    => __( 'Plan executed', 'site-blocks' ),
					'skeleton' => 'report',
				),
			),
		),
		'monitoring-hub-process' => array(
			'heading_id'    => 'sg-monitoring-process-flow-heading',
			'section_class' => 'sg-monitoring-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'From Risk To ', 'site-blocks' ),
			'title_accent'  => __( 'Response', 'site-blocks' ),
			'title_after'   => __( ' in four steps', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Site Assessment', 'site-blocks' ),
					'badge'    => __( 'Risk reviewed', 'site-blocks' ),
					'skeleton' => 'assess',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Tailored Plan', 'site-blocks' ),
					'badge'    => __( 'Cover matched', 'site-blocks' ),
					'skeleton' => 'schedule',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Connection', 'site-blocks' ),
					'badge'    => __( 'Tested and live', 'site-blocks' ),
					'skeleton' => 'install',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Monitoring', 'site-blocks' ),
					'badge'    => __( '24/7 coverage', 'site-blocks' ),
					'skeleton' => 'report',
				),
			),
		),
		'enterprise-hub-process' => array(
			'heading_id'    => 'sg-enterprise-process-flow-heading',
			'section_class' => 'sg-enterprise-process-flow',
			'eyebrow'       => __( 'How we deliver', 'site-blocks' ),
			'title_before'  => __( 'From Assessment To ', 'site-blocks' ),
			'title_accent'  => __( 'Support', 'site-blocks' ),
			'title_after'   => __( ' in four steps', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Assess', 'site-blocks' ),
					'badge'    => __( 'Risks mapped', 'site-blocks' ),
					'skeleton' => 'assess',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Design', 'site-blocks' ),
					'badge'    => __( 'Scope agreed', 'site-blocks' ),
					'skeleton' => 'schedule',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Deploy', 'site-blocks' ),
					'badge'    => __( 'Systems live', 'site-blocks' ),
					'skeleton' => 'install',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Support', 'site-blocks' ),
					'badge'    => __( 'One accountable team', 'site-blocks' ),
					'skeleton' => 'report',
				),
			),
		),
		'homepage' => array(
			'heading_id'    => 'sg-home-process-flow-heading',
			'section_class' => 'sg-home-process-flow sg-reveal',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'A ', 'site-blocks' ),
			'title_accent'  => __( 'Smarter', 'site-blocks' ),
			'title_after'   => __( ' way to plan your security system', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Tell Us What You Need', 'site-blocks' ),
					'badge'    => __( 'Property and goals', 'site-blocks' ),
					'skeleton' => 'quote',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Share Your Site Details And Photos', 'site-blocks' ),
					'badge'    => __( 'Photos and access', 'site-blocks' ),
					'skeleton' => 'photos',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Receive A Tailored Estimate', 'site-blocks' ),
					'badge'    => __( 'Clear package', 'site-blocks' ),
					'skeleton' => 'assess',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Expert Review Before You Approve', 'site-blocks' ),
					'badge'    => __( 'Technician checked', 'site-blocks' ),
					'skeleton' => 'report',
				),
			),
		),
		'alarm-systems' => array(
			'heading_id'    => 'sg-alarm-steps-heading',
			'section_class' => 'sg-alarm-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'Simple Steps, Done ', 'site-blocks' ),
			'title_accent'  => __( 'Properly', 'site-blocks' ),
			'title_after'   => __( ' from quote to install.', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Tell Us What You Need', 'site-blocks' ),
					'badge'    => __( 'Quick start', 'site-blocks' ),
					'skeleton' => 'quote',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Share A Few Photos', 'site-blocks' ),
					'badge'    => __( 'Site details', 'site-blocks' ),
					'skeleton' => 'photos',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Get A Tailored Price', 'site-blocks' ),
					'badge'    => __( 'Instant estimate', 'site-blocks' ),
					'skeleton' => 'assess',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Technician Review', 'site-blocks' ),
					'badge'    => __( 'Checked first', 'site-blocks' ),
					'skeleton' => 'report',
				),
			),
		),
		'cctv' => array(
			'heading_id'    => 'sg-cctv-difference-heading',
			'section_class' => 'sg-cctv-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'One Team From First Plan To Final ', 'site-blocks' ),
			'title_accent'  => __( 'Handover', 'site-blocks' ),
			'title_after'   => __( '.', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Tell Us What To Cover', 'site-blocks' ),
					'badge'    => __( 'Entries and blind spots', 'site-blocks' ),
					'skeleton' => 'quote',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Share Site Details', 'site-blocks' ),
					'badge'    => __( 'Photos and access', 'site-blocks' ),
					'skeleton' => 'photos',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Tailored Estimate', 'site-blocks' ),
					'badge'    => __( 'One clear price', 'site-blocks' ),
					'skeleton' => 'assess',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Technician Review', 'site-blocks' ),
					'badge'    => __( 'Installer checked', 'site-blocks' ),
					'skeleton' => 'install',
				),
			),
		),
		'intercom' => array(
			'heading_id'    => 'sg-intercom-difference-heading',
			'section_class' => 'sg-intercom-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'One Team From First Plan To Final ', 'site-blocks' ),
			'title_accent'  => __( 'Handover', 'site-blocks' ),
			'title_after'   => __( '.', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Tell Us Your Entries', 'site-blocks' ),
					'badge'    => __( 'Doors and gates', 'site-blocks' ),
					'skeleton' => 'quote',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Share Site Details', 'site-blocks' ),
					'badge'    => __( 'Cabling mapped', 'site-blocks' ),
					'skeleton' => 'photos',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Tailored Estimate', 'site-blocks' ),
					'badge'    => __( 'Complete system', 'site-blocks' ),
					'skeleton' => 'assess',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Technician Review', 'site-blocks' ),
					'badge'    => __( 'Approved first', 'site-blocks' ),
					'skeleton' => 'install',
				),
			),
		),
		'ajax-alarm-systems' => array(
			'heading_id'    => 'sg-ajax-process-heading',
			'section_class' => 'sg-ajax-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'Ajax Alarm Delivery In ', 'site-blocks' ),
			'title_accent'  => __( 'Four Steps', 'site-blocks' ),
			'title_after'   => __( ' from design to support.', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Design', 'site-blocks' ),
					'badge'    => __( 'Right Ajax system', 'site-blocks' ),
					'skeleton' => 'assess',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Install', 'site-blocks' ),
					'badge'    => __( 'Professional fit', 'site-blocks' ),
					'skeleton' => 'install',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Monitor', 'site-blocks' ),
					'badge'    => __( 'Flexible cover', 'site-blocks' ),
					'skeleton' => 'tour',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Support', 'site-blocks' ),
					'badge'    => __( 'Local help', 'site-blocks' ),
					'skeleton' => 'support',
				),
			),
		),
		'paradox-m25' => array(
			'heading_id'    => 'sg-paradox-process-heading',
			'section_class' => 'sg-ajax-process-flow',
			'eyebrow'       => __( 'How installation works', 'site-blocks' ),
			'title_before'  => __( 'Paradox M25 Install In ', 'site-blocks' ),
			'title_accent'  => __( 'Four Steps', 'site-blocks' ),
			'title_after'   => __( ' from design to handover.', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Design', 'site-blocks' ),
					'badge'    => __( 'Right M25 layout', 'site-blocks' ),
					'skeleton' => 'assess',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'PoE Install', 'site-blocks' ),
					'badge'    => __( 'Console and devices', 'site-blocks' ),
					'skeleton' => 'install',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'BlueEye Setup', 'site-blocks' ),
					'badge'    => __( 'Users and testing', 'site-blocks' ),
					'skeleton' => 'cloud',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Support', 'site-blocks' ),
					'badge'    => __( 'Local help', 'site-blocks' ),
					'skeleton' => 'support',
				),
			),
		),
		'akuvox' => array(
			'heading_id'    => 'sg-akuvox-process-heading',
			'section_class' => 'sg-ajax-process-flow',
			'eyebrow'       => __( 'How installation works', 'site-blocks' ),
			'title_before'  => __( 'Akuvox Install In ', 'site-blocks' ),
			'title_accent'  => __( 'Four Steps', 'site-blocks' ),
			'title_after'   => __( ' from site check to SmartPlus handover.', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Site Check', 'site-blocks' ),
					'badge'    => __( 'Door and cable', 'site-blocks' ),
					'skeleton' => 'assess',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Spec IP Or 2-Wire', 'site-blocks' ),
					'badge'    => __( 'Right upgrade path', 'site-blocks' ),
					'skeleton' => 'quote',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Install', 'site-blocks' ),
					'badge'    => __( 'Station and monitor', 'site-blocks' ),
					'skeleton' => 'install',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'SmartPlus Handover', 'site-blocks' ),
					'badge'    => __( 'Users and testing', 'site-blocks' ),
					'skeleton' => 'cloud',
				),
			),
		),
		'access-control' => array(
			'heading_id'    => 'sg-access-control-process-heading',
			'section_class' => 'sg-ac-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'Access Control In ', 'site-blocks' ),
			'title_accent'  => __( 'Four Steps', 'site-blocks' ),
			'title_after'   => __( ' from site visit to handover.', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Tell Us Your Doors', 'site-blocks' ),
					'badge'    => __( 'Users mapped', 'site-blocks' ),
					'skeleton' => 'quote',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Share Site Details', 'site-blocks' ),
					'badge'    => __( 'Layout understood', 'site-blocks' ),
					'skeleton' => 'photos',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Tailored Estimate', 'site-blocks' ),
					'badge'    => __( 'Scoped to site', 'site-blocks' ),
					'skeleton' => 'assess',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Install And Handover', 'site-blocks' ),
					'badge'    => __( 'Trained and supported', 'site-blocks' ),
					'skeleton' => 'install',
				),
			),
		),
		'physical-security' => array(
			'heading_id'    => 'sg-ps-process-heading',
			'section_class' => 'sg-ps-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'From Risk To ', 'site-blocks' ),
			'title_accent'  => __( 'Response', 'site-blocks' ),
			'title_after'   => __( ' in four steps', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Site Assessment', 'site-blocks' ),
					'badge'    => __( 'Risk reviewed', 'site-blocks' ),
					'skeleton' => 'assess',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Tailored Plan', 'site-blocks' ),
					'badge'    => __( 'Right mix', 'site-blocks' ),
					'skeleton' => 'schedule',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Officers Deployed', 'site-blocks' ),
					'badge'    => __( 'Licensed team', 'site-blocks' ),
					'skeleton' => 'install',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Supervision', 'site-blocks' ),
					'badge'    => __( 'GPS and reporting', 'site-blocks' ),
					'skeleton' => 'report',
				),
			),
		),
		'electronic-security' => array(
			'heading_id'    => 'sg-es-process-heading',
			'section_class' => 'sg-es-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'From Design To ', 'site-blocks' ),
			'title_accent'  => __( 'Live Cover', 'site-blocks' ),
			'title_after'   => __( ' in four steps', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Site Assessment', 'site-blocks' ),
					'badge'    => __( 'Risk mapped', 'site-blocks' ),
					'skeleton' => 'assess',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'System Design', 'site-blocks' ),
					'badge'    => __( 'Right stack', 'site-blocks' ),
					'skeleton' => 'schedule',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Install & Commission', 'site-blocks' ),
					'badge'    => __( 'Tested live', 'site-blocks' ),
					'skeleton' => 'install',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Monitor & Support', 'site-blocks' ),
					'badge'    => __( 'Ongoing cover', 'site-blocks' ),
					'skeleton' => 'report',
				),
			),
		),
		'manpower' => array(
			'heading_id'    => 'sg-mp-process-heading',
			'section_class' => 'sg-mp-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'From Brief To ', 'site-blocks' ),
			'title_accent'  => __( 'Roster', 'site-blocks' ),
			'title_after'   => __( ' in four steps', 'site-blocks' ),
			'steps'         => array(
				array(
					'label'    => __( 'Step one', 'site-blocks' ),
					'title'    => __( 'Site Brief', 'site-blocks' ),
					'badge'    => __( 'Needs captured', 'site-blocks' ),
					'skeleton' => 'assess',
				),
				array(
					'label'    => __( 'Step two', 'site-blocks' ),
					'title'    => __( 'Coverage Plan', 'site-blocks' ),
					'badge'    => __( 'Hours & posts', 'site-blocks' ),
					'skeleton' => 'schedule',
				),
				array(
					'label'    => __( 'Step three', 'site-blocks' ),
					'title'    => __( 'Licensed Officers', 'site-blocks' ),
					'badge'    => __( 'Screened team', 'site-blocks' ),
					'skeleton' => 'install',
				),
				array(
					'label'    => __( 'Step four', 'site-blocks' ),
					'title'    => __( 'Supervise & Report', 'site-blocks' ),
					'badge'    => __( 'Clear escalation', 'site-blocks' ),
					'skeleton' => 'report',
				),
			),
		),
		'physical-security-static-guards' => array(
			'heading_id'    => 'sg-ps-static-process-heading',
			'section_class' => 'sg-ps-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'Static Guarding In ', 'site-blocks' ),
			'title_accent'  => __( 'Four Steps', 'site-blocks' ),
			'title_after'   => __( ' from brief to on-site cover.', 'site-blocks' ),
			'steps'         => array(
				array( 'label' => __( 'Step one', 'site-blocks' ), 'title' => __( 'Site Brief', 'site-blocks' ), 'badge' => __( 'Risk reviewed', 'site-blocks' ), 'skeleton' => 'assess' ),
				array( 'label' => __( 'Step two', 'site-blocks' ), 'title' => __( 'Roster Plan', 'site-blocks' ), 'badge' => __( 'Hours mapped', 'site-blocks' ), 'skeleton' => 'schedule' ),
				array( 'label' => __( 'Step three', 'site-blocks' ), 'title' => __( 'Officers Deployed', 'site-blocks' ), 'badge' => __( 'Licensed team', 'site-blocks' ), 'skeleton' => 'install' ),
				array( 'label' => __( 'Step four', 'site-blocks' ), 'title' => __( 'Supervision', 'site-blocks' ), 'badge' => __( 'Reports delivered', 'site-blocks' ), 'skeleton' => 'report' ),
			),
		),
		'physical-security-mobile-patrols' => array(
			'heading_id'    => 'sg-ps-mobile-process-heading',
			'section_class' => 'sg-ps-process-flow',
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'Mobile Patrols In ', 'site-blocks' ),
			'title_accent'  => __( 'Four Steps', 'site-blocks' ),
			'title_after'   => __( ' from route plan to GPS-verified checks.', 'site-blocks' ),
			'steps'         => array(
				array( 'label' => __( 'Step one', 'site-blocks' ), 'title' => __( 'Sites Mapped', 'site-blocks' ), 'badge' => __( 'Routes planned', 'site-blocks' ), 'skeleton' => 'assess' ),
				array( 'label' => __( 'Step two', 'site-blocks' ), 'title' => __( 'Patrol Schedule', 'site-blocks' ), 'badge' => __( 'Checks agreed', 'site-blocks' ), 'skeleton' => 'schedule' ),
				array( 'label' => __( 'Step three', 'site-blocks' ), 'title' => __( 'Vehicle Deployed', 'site-blocks' ), 'badge' => __( 'GPS tracked', 'site-blocks' ), 'skeleton' => 'tour' ),
				array( 'label' => __( 'Step four', 'site-blocks' ), 'title' => __( 'Reporting', 'site-blocks' ), 'badge' => __( 'Incidents logged', 'site-blocks' ), 'skeleton' => 'report' ),
			),
		),
	);
}
