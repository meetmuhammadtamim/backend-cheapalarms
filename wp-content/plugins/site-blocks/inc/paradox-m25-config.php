<?php
/**
 * Paradox M25 landing page config (section order + copy).
 *
 * Image paths under assets/images/paradox/ come from Paradox marketing materials.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Canonical section order for the Paradox M25 page.
 *
 * @return array<int, string>
 */
function site_blocks_paradox_m25_page_sections(): array {
	return array(
		'difference',
		'portal',
		'products',
		'monitoring',
		'property-fit',
		'install',
		'compare',
		'faq',
		'cta',
	);
}

/**
 * Whether a section applies on this page.
 */
function site_blocks_paradox_m25_section_applies( string $section ): bool {
	return in_array( $section, site_blocks_paradox_m25_page_sections(), true );
}

/**
 * Shared CTAs for the Paradox M25 page.
 *
 * @return array{primary_label: string, primary_url: string, secondary_label: string, secondary_url: string}
 */
function site_blocks_paradox_m25_ctas(): array {
	return array(
		'primary_label'   => __( 'Request a quote', 'site-blocks' ),
		'primary_url'     => home_url( '/contact/' ),
		'secondary_label' => __( 'Call 1300 225 276', 'site-blocks' ),
		'secondary_url'   => 'tel:1300225276',
	);
}

/**
 * Hero config (stage hero).
 *
 * @return array<string, mixed>
 */
function site_blocks_paradox_m25_hero_config(): array {
	$ctas = site_blocks_paradox_m25_ctas();

	return array(
		'id'              => 'sg-paradox-hero-heading',
		'class'           => 'sg-paradox-m25-hero',
		'eyebrow'         => '',
		'title'           => __( 'Paradox M25 Australia', 'site-blocks' ),
		'lead'            => '',
		'primary_label'   => $ctas['primary_label'],
		'primary_url'     => $ctas['primary_url'],
		'secondary_label' => $ctas['secondary_label'],
		'secondary_url'   => $ctas['secondary_url'],
		'trust'           => array(),
		'background_image' => site_blocks_asset_url( 'images/paradox/wb-m25.webp' ),
		'mascot_image'     => 'images/paradox/hummingbird.webp',
		'mascot_alt'      => __( 'Paradox hummingbird', 'site-blocks' ),
		'frame_image'     => '',
		'frame_alt'       => '',
	);
}

/**
 * Difference section config (split: numbered pillars + house diagram).
 *
 * @return array<string, mixed>
 */
function site_blocks_paradox_m25_difference_config(): array {
	return array(
		'eyebrow'    => __( 'The Paradox difference', 'site-blocks' ),
		'title'      => __( 'The Paradox Difference', 'site-blocks' ),
		'intro'      => __( 'Built for clean installs and reliable control: PoE power, multi-path communication, and encrypted M wireless.', 'site-blocks' ),
		'points'     => array(
			array(
				'title'       => __( 'One-Cable PoE Install', 'site-blocks' ),
				'description' => __( 'Power and network over a single Cat6. Less wall clutter, faster commissioning.', 'site-blocks' ),
			),
			array(
				'title'       => __( 'Direct-Connect Ready', 'site-blocks' ),
				'description' => __( 'Reporting paths that do not depend on a single cloud hop for every alarm event.', 'site-blocks' ),
			),
			array(
				'title'       => __( 'Encrypted M Wireless', 'site-blocks' ),
				'description' => __( 'Frequency and encryption hopping across the M System device range.', 'site-blocks' ),
			),
		),
		// TODO: house-framed M25 ecosystem diagram from marketing materials.
		'image'      => 'images/paradox/difference-house.webp',
		'image_alt'  => __( 'Paradox M25 console connected to keypad, detectors and sensors', 'site-blocks' ),
	);
}

/**
 * BlueEye app section config (wireframe §03). Not the Safeguard quote portal.
 *
 * @return array<string, mixed>
 */
function site_blocks_paradox_m25_portal_config(): array {
	return array(
		'heading_id'    => 'sg-paradox-blueeye-heading',
		'section_class' => 'sg-paradox-blueeye',
		'title_before'  => __( 'Control The System From ', 'site-blocks' ),
		'title_accent'  => __( 'BlueEye', 'site-blocks' ),
		'intro'         => __( 'Arm, disarm and manage users from the Paradox BlueEye app.', 'site-blocks' ),
		'bullets'       => array(
			__( 'Arm, disarm and receive push alerts', 'site-blocks' ),
			__( 'Owner, master and user roles', 'site-blocks' ),
			__( 'Schedules, automation and temperature alerts', 'site-blocks' ),
			__( 'OTA updates after installer setup', 'site-blocks' ),
		),
		'icon_path'     => 'images/paradox/blueeye-icon.webp',
		'icon_alt'      => __( 'Paradox BlueEye', 'site-blocks' ),
		'image_path'    => 'images/paradox/blueeye-icon.webp',
		'image_alt'     => __( 'Paradox BlueEye app mark', 'site-blocks' ),
	);
}

/**
 * Featured products config (intro + featured console + 3 supporting cards).
 *
 * @return array<string, mixed>
 */
function site_blocks_paradox_m25_products_config(): array {
	$ctas = site_blocks_paradox_m25_ctas();

	return array(
		'eyebrow'  => __( 'The Products', 'site-blocks' ),
		'title'    => __( 'Common Paradox M System Components We May Include In Your Design.', 'site-blocks' ),
		'lead'     => __( 'Every property is different. Exact devices depend on layout, risks and how you use the site. M25 has no native CCTV yet; cameras are specified as a separate pathway when needed.', 'site-blocks' ),
		'cta'      => array(
			'label' => $ctas['primary_label'],
			'url'   => $ctas['primary_url'],
		),
		'trust'    => array(
			array(
				'icon'  => 'shield',
				'label' => __( 'Proven technology', 'site-blocks' ),
			),
			array(
				'icon'  => 'lock',
				'label' => __( 'Secure by design', 'site-blocks' ),
			),
			array(
				'icon'  => 'network',
				'label' => __( 'Flexible integration', 'site-blocks' ),
			),
			array(
				'icon'  => 'headset',
				'label' => __( 'Expert support', 'site-blocks' ),
			),
		),
		'featured' => array(
			'title'       => __( 'Paradox M25 / M25 LTE', 'site-blocks' ),
			'tag'         => __( 'Console', 'site-blocks' ),
			'description' => __( 'The system brain. PoE powered, Ethernet and Wi-Fi, with LTE on the LTE model for backup paths.', 'site-blocks' ),
			'image'       => 'images/paradox/m25-featured.webp',
			'alt'         => __( 'Paradox M25 console', 'site-blocks' ),
		),
		'items'    => array(
			array(
				'title'       => __( 'Paradox K70M', 'site-blocks' ),
				'tag'         => __( '7 inch touch keypad', 'site-blocks' ),
				'description' => __( 'Wall keypad for local control. Not a front-door intercom station.', 'site-blocks' ),
				'image'       => 'images/paradox/products/k70m-cutout.webp',
				'alt'         => __( 'Paradox K70M touchscreen keypad', 'site-blocks' ),
			),
			array(
				'title'       => __( 'Detectors', 'site-blocks' ),
				'tag'         => __( 'Perimeter protection', 'site-blocks' ),
				'description' => __( 'Door contacts and indoor or outdoor PIRs, including pet-friendly options where needed.', 'site-blocks' ),
				'image'       => 'images/paradox/products/detectors.webp',
				'alt'         => __( 'Paradox M System motion and door detectors', 'site-blocks' ),
			),
			array(
				'title'       => __( 'Paradox K38M', 'site-blocks' ),
				'tag'         => __( '48-zone LCD keypad', 'site-blocks' ),
				'description' => __( 'Fixed LCD keypad for arming, status and local control where a full touchscreen is not needed.', 'site-blocks' ),
				'image'       => 'images/paradox/products/k38m.webp',
				'alt'         => __( 'Paradox K38M LCD keypad', 'site-blocks' ),
			),
		),
	);
}

/**
 * Monitoring options config.
 *
 * @return array<string, mixed>
 */
function site_blocks_paradox_m25_monitoring_config(): array {
	return array(
		'eyebrow' => __( 'Monitoring Options', 'site-blocks' ),
		'title'   => __( 'Choose How Your Paradox M25 System Is Monitored', 'site-blocks' ),
		'lead'    => __( 'Safeguard can configure BlueEye self-monitoring, discuss professional back-to-base options, and plan communication paths so alerts still get through when broadband is down.', 'site-blocks' ),
		'options' => array(
			array(
				'title'       => __( 'Self-Monitoring Via BlueEye', 'site-blocks' ),
				'description' => __( 'Arm, disarm and receive alerts on your phone with role-based users.', 'site-blocks' ),
				'icon'        => 'smartphone',
			),
			array(
				'title'       => __( 'Professional Monitoring', 'site-blocks' ),
				'description' => __( 'Connect the system to an alarm monitoring service when monitoring is selected for the job.', 'site-blocks' ),
				'icon'        => 'headset',
			),
			array(
				'title'       => __( 'Backup Communication', 'site-blocks' ),
				'description' => __( 'Ethernet, Wi-Fi and LTE (on M25 LTE) can be planned as failover paths.', 'site-blocks' ),
				'icon'        => 'shield-check',
			),
		),
		// TODO: BlueEye app / console lifestyle shot.
		'image'     => 'images/paradox/monitoring-blueeye.webp',
		'image_alt' => __( 'BlueEye app controlling a Paradox M25 system', 'site-blocks' ),
	);
}

/**
 * Property fit config (spec headroom strip, wireframe §06).
 *
 * @return array<string, mixed>
 */
function site_blocks_paradox_m25_property_fit_config(): array {
	return array(
		'eyebrow' => __( 'Property Fit', 'site-blocks' ),
		'title'   => __( 'Sized For Homes, Ready For Small Business Growth', 'site-blocks' ),
		'lead'    => __( 'M25 has enough headroom for apartments through larger sites. We only install what the property needs first.', 'site-blocks' ),
		'stats'   => array(
			array(
				'value' => '200',
				'label' => __( 'Zones', 'site-blocks' ),
			),
			array(
				'value' => '10',
				'label' => __( 'Partitions', 'site-blocks' ),
			),
			array(
				'value' => '128',
				'label' => __( 'Users', 'site-blocks' ),
			),
			array(
				'value' => '75',
				'label' => __( 'Wireless devices', 'site-blocks' ),
			),
		),
	);
}

/**
 * Compare table config (Paradox M25 vs Ajax).
 *
 * @return array<string, mixed>
 */
function site_blocks_paradox_m25_compare_config(): array {
	return array(
		'eyebrow'       => __( 'Compare', 'site-blocks' ),
		'title'         => __( 'Paradox M25 Vs Ajax With Safeguard.', 'site-blocks' ),
		'left_heading'  => __( 'Paradox M25', 'site-blocks' ),
		'right_heading' => __( 'Ajax', 'site-blocks' ),
		'rows'          => array(
			array(
				'label' => __( 'Console power', 'site-blocks' ),
				'left'  => __( 'PoE over a single Cat6 (plus Wi-Fi / LTE options)', 'site-blocks' ),
				'right' => __( 'Plug-pack powered hub with Ethernet / Wi-Fi / 4G options', 'site-blocks' ),
			),
			array(
				'label' => __( 'App', 'site-blocks' ),
				'left'  => __( 'Paradox BlueEye', 'site-blocks' ),
				'right' => __( 'Ajax mobile apps', 'site-blocks' ),
			),
			array(
				'label' => __( 'Native cameras', 'site-blocks' ),
				'left'  => __( 'Not yet. CCTV specified separately when needed', 'site-blocks' ),
				'right' => __( 'MotionCam and related Ajax video devices available', 'site-blocks' ),
			),
			array(
				'label' => __( 'Keypad', 'site-blocks' ),
				'left'  => __( 'K70M touch or K38M LCD (wall control, not door intercom)', 'site-blocks' ),
				'right' => __( 'Ajax keypads and related controls', 'site-blocks' ),
			),
			array(
				'label' => __( 'Best next step', 'site-blocks' ),
				'left'  => __( 'Quote a Paradox M25 design for your property', 'site-blocks' ),
				'right' => __( 'Request an Ajax quote for your property', 'site-blocks' ),
			),
		),
	);
}

/**
 * FAQ items.
 *
 * @return array<int, array{q: string, a: string}>
 */
function site_blocks_paradox_m25_faq_items(): array {
	return array(
		array(
			'q' => __( 'What is the Paradox M25?', 'site-blocks' ),
			'a' => __( 'M25 is Paradox\'s modern hybrid wireless console for the M System. It manages sensors, keypads, sirens and app users, with PoE power and multi-path communication.', 'site-blocks' ),
		),
		array(
			'q' => __( 'Is M25 the same as older Paradox panels?', 'site-blocks' ),
			'a' => __( 'No. Spectra, Magellan and EVO are older lines. M25 only works with M System devices (model names ending in M).', 'site-blocks' ),
		),
		array(
			'q' => __( 'Does M25 include cameras or a door intercom?', 'site-blocks' ),
			'a' => __( 'Not as a native package today. Video verification and access modules are still rolling out. We specify Uniview CCTV or a separate intercom when the site needs them. K70M is a wall keypad, not a front-door station.', 'site-blocks' ),
		),
		array(
			'q' => __( 'How is M25 powered and connected?', 'site-blocks' ),
			'a' => __( 'Usually one Cat6 cable for PoE power and Ethernet. Built-in Wi-Fi can be primary or backup. M25 LTE adds cellular as another path.', 'site-blocks' ),
		),
		array(
			'q' => __( 'What app do customers use?', 'site-blocks' ),
			'a' => __( 'Paradox BlueEye on iOS and Android for arming, alerts and user management after we set the system up.', 'site-blocks' ),
		),
		array(
			'q' => __( 'Can M25 be professionally monitored?', 'site-blocks' ),
			'a' => __( 'Yes. We can discuss self-monitoring via BlueEye and professional back-to-base options suited to your property and communicator paths.', 'site-blocks' ),
		),
		array(
			'q' => __( 'How much does a Paradox M25 system cost?', 'site-blocks' ),
			'a' => __( 'Cost depends on property size, device count and monitoring choice. Request a quote and a technician reviews the design.', 'site-blocks' ),
		),
		array(
			'q' => __( 'Is Safeguard the manufacturer of Paradox?', 'site-blocks' ),
			'a' => __( 'No. Paradox manufactures the hardware. Safeguard is an independent Australian installer. We design, supply, install and support M25 systems for your property.', 'site-blocks' ),
		),
	);
}

/**
 * Quote CTA overrides (dark Ajax-style panel).
 *
 * @return array<string, mixed>
 */
function site_blocks_paradox_m25_quote_cta_config(): array {
	$ctas        = site_blocks_paradox_m25_ctas();
	$phone_label = '1300 225 276';

	return array(
		'heading_id'      => 'sg-paradox-quote-cta-heading',
		'eyebrow'         => __( 'Request a quote', 'site-blocks' ),
		'title'           => __( 'Design Your Paradox M25 System With Safeguard.', 'site-blocks' ),
		'lead'            => __( 'Tell us about the property, get pricing guidance quickly, and we will design the right M25 layout with BlueEye handover.', 'site-blocks' ),
		'primary_label'   => $ctas['primary_label'],
		'primary_url'     => $ctas['primary_url'],
		'secondary_label' => sprintf(
			/* translators: %s: phone number */
			__( 'Call %s', 'site-blocks' ),
			$phone_label
		),
		'secondary_url'   => 'tel:1300225276',
		'phone_href'      => 'tel:1300225276',
		'phone_label'     => $phone_label,
		'bg_image'        => site_blocks_asset_url( 'images/paradox/wb-m25.webp' ),
		'features'        => array(
			array(
				'title'       => __( 'Property-Led Design', 'site-blocks' ),
				'description' => __( 'Devices chosen for your layout and risks.', 'site-blocks' ),
				'icon'        => 'technician.png',
				'accent'      => 'blue',
			),
			array(
				'title'       => __( 'Clear Install Path', 'site-blocks' ),
				'description' => __( 'PoE planning, pairing and testing included.', 'site-blocks' ),
				'icon'        => 'dollar.png',
				'accent'      => 'orange',
			),
			array(
				'title'       => __( 'Track Everything', 'site-blocks' ),
				'description' => __( 'A person on the team confirms the scope before work begins.', 'site-blocks' ),
				'icon'        => 'secure.png',
				'accent'      => 'blue',
			),
		),
		'trust_items'     => array(
			array(
				'label' => __( 'Independent Australian Paradox installer', 'site-blocks' ),
				'type'  => 'safeguard-logo',
			),
			array(
				'label' => __( 'Australian owned and operated', 'site-blocks' ),
				'type'  => 'text',
			),
			array(
				'label' => __( 'Master Licence #000103619', 'site-blocks' ),
				'type'  => 'text',
			),
		),
	);
}
