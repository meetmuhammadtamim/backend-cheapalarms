<?php
/**
 * Akuvox landing page config (section order + copy).
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Canonical section order for the Akuvox page.
 *
 * @return array<int, string>
 */
function site_blocks_akuvox_page_sections(): array {
	return array(
		'difference',
		'portal',
		'products',
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
function site_blocks_akuvox_section_applies( string $section ): bool {
	return in_array( $section, site_blocks_akuvox_page_sections(), true );
}

/**
 * Shared CTAs for the Akuvox page.
 *
 * @return array{primary_label: string, primary_url: string, secondary_label: string, secondary_url: string}
 */
function site_blocks_akuvox_ctas(): array {
	return array(
		'primary_label'   => __( 'Request a quote', 'site-blocks' ),
		'primary_url'     => home_url( '/contact/' ),
		'secondary_label' => __( 'Call 1300 225 276', 'site-blocks' ),
		'secondary_url'   => 'tel:1300225276',
	);
}

/**
 * Hero config (conversion stage hero).
 *
 * @return array<string, mixed>
 */
function site_blocks_akuvox_hero_config(): array {
	$ctas = site_blocks_akuvox_ctas();

	return array(
		'id'              => 'sg-akuvox-hero-heading',
		'class'           => 'sg-akuvox-hero',
		'eyebrow'         => __( 'Akuvox · installed by Safeguard', 'site-blocks' ),
		'title'           => __( 'See Who’s At The Door. Unlock From Your Phone. Keep The Wiring You Already Have.', 'site-blocks' ),
		'lead'            => __( 'Safeguard designs and installs Akuvox video intercoms for homes, apartments and commercial entries: new IP systems or 2-wire upgrades on cabling that is already in the wall.', 'site-blocks' ),
		'primary_label'   => $ctas['primary_label'],
		'primary_url'     => $ctas['primary_url'],
		'secondary_label' => $ctas['secondary_label'],
		'secondary_url'   => $ctas['secondary_url'],
		'trust'           => array(
			array(
				'icon'  => 'award',
				'label' => __( 'Master Licence #000103619', 'site-blocks' ),
			),
			array(
				'icon'  => 'shield',
				'label' => __( 'ASIAL member', 'site-blocks' ),
			),
			array(
				'icon'  => 'smartphone',
				'label' => __( 'SmartPlus app handover', 'site-blocks' ),
			),
			array(
				'icon'  => 'phone',
				'label' => __( '1300 225 276', 'site-blocks' ),
			),
		),
		'frame_image'     => 'images/intercom/hero.webp',
		'frame_alt'       => __( 'Akuvox outdoor door station and indoor video monitor', 'site-blocks' ),
	);
}

/**
 * Difference section config.
 *
 * @return array<string, mixed>
 */
function site_blocks_akuvox_difference_config(): array {
	return array(
		'eyebrow'   => __( 'The Akuvox difference', 'site-blocks' ),
		'title'     => __( 'A SIP Video Intercom, Not A Doorbell Kit.', 'site-blocks' ),
		'intro'     => __( 'Akuvox is specified for the door, the wiring and the people who use it. Safeguard installs it under a Master Licence so entry, CCTV and access can sit in one design.', 'site-blocks' ),
		'points'    => array(
			array(
				'title'       => __( 'SIP Video At The Door', 'site-blocks' ),
				'description' => __( 'See and speak to visitors before you unlock. This is a proper video intercom, not a consumer doorbell stuck on the facade.', 'site-blocks' ),
			),
			array(
				'title'       => __( 'Upgrade On 2-Wire You Already Have', 'site-blocks' ),
				'description' => __( 'Many Sydney apartments and older homes can keep existing 2-wire runs. We confirm that on site before anyone chases new cable.', 'site-blocks' ),
			),
			array(
				'title'       => __( 'Open Stack, One Installer', 'site-blocks' ),
				'description' => __( 'ONVIF cameras, access readers and the intercom can be planned together, so you are not juggling three contractors at the front door.', 'site-blocks' ),
			),
		),
		'image'     => 'images/intercom/hero.webp',
		'image_alt' => __( 'Akuvox door station and indoor monitor specified for a Sydney entry', 'site-blocks' ),
	);
}

/**
 * SmartPlus app section (not the Safeguard quote portal).
 *
 * @return array<string, mixed>
 */
function site_blocks_akuvox_portal_config(): array {
	return array(
		'heading_id'    => 'sg-akuvox-smartplus-heading',
		'section_class' => 'sg-paradox-blueeye sg-akuvox-smartplus',
		'title_before'  => __( 'Answer And Unlock From ', 'site-blocks' ),
		'title_accent'  => __( 'SmartPlus', 'site-blocks' ),
		'intro'         => __( 'SmartPlus is the Akuvox app for video calls, remote unlock and visitor codes.', 'site-blocks' ),
		'bullets'       => array(
			__( 'See and speak to visitors on your phone', 'site-blocks' ),
			__( 'Unlock the door or gate when you choose', 'site-blocks' ),
			__( 'Issue and revoke temporary access codes', 'site-blocks' ),
			__( 'Owner, resident and staff roles after we set it up', 'site-blocks' ),
		),
		'icon_path'     => 'images/brands/akuvox.webp',
		'icon_alt'      => __( 'Akuvox', 'site-blocks' ),
		'image_path'    => 'images/brands/akuvox.webp',
		'image_alt'     => __( 'Akuvox brand mark', 'site-blocks' ),
	);
}

/**
 * Featured products config.
 *
 * @return array<string, mixed>
 */
function site_blocks_akuvox_products_config(): array {
	$ctas = site_blocks_akuvox_ctas();

	return array(
		'eyebrow'  => __( 'The Products', 'site-blocks' ),
		'title'    => __( 'Common Akuvox Components We May Include In Your Design.', 'site-blocks' ),
		'lead'     => __( 'Every door is different. Exact devices depend on the entry, existing cable and how many people need to answer or unlock. We spec the station, monitor and credentials after the site check — not from a catalogue page.', 'site-blocks' ),
		'cta'      => array(
			'label' => $ctas['primary_label'],
			'url'   => $ctas['primary_url'],
		),
		'trust'    => array(
			array(
				'icon'  => 'video',
				'label' => __( 'Video at the door', 'site-blocks' ),
			),
			array(
				'icon'  => 'cable',
				'label' => __( '2-wire or IP', 'site-blocks' ),
			),
			array(
				'icon'  => 'lock',
				'label' => __( 'Access ready', 'site-blocks' ),
			),
			array(
				'icon'  => 'headset',
				'label' => __( 'Local support', 'site-blocks' ),
			),
		),
		'featured' => array(
			'title'       => __( 'Akuvox Door Station', 'site-blocks' ),
			'tag'         => __( 'Outdoor video phone', 'site-blocks' ),
			'description' => __( 'The face of the system. Visitors call, you see them, then you unlock. Weather-rated stations for houses, apartments and commercial entries.', 'site-blocks' ),
			'image'       => 'images/intercom/hero.webp',
			'alt'         => __( 'Akuvox outdoor door station', 'site-blocks' ),
		),
		'items'    => array(
			array(
				'title'       => __( 'Indoor Monitor', 'site-blocks' ),
				'tag'         => __( 'Answer at home', 'site-blocks' ),
				'description' => __( 'Wall monitor so someone can take the call without picking up a phone. Still works locally if the internet drops.', 'site-blocks' ),
				'image'       => 'images/ajax/property/home.webp',
				'alt'         => __( 'Indoor video intercom monitor in a Sydney home', 'site-blocks' ),
			),
			array(
				'title'       => __( 'Access Reader', 'site-blocks' ),
				'tag'         => __( 'Cards, fobs, codes', 'site-blocks' ),
				'description' => __( 'Residents and staff can unlock without a call. We match credentials to how the building already works.', 'site-blocks' ),
				'image'       => 'images/access-control/hero.webp',
				'alt'         => __( 'Access reader at a Sydney building entrance', 'site-blocks' ),
			),
			array(
				'title'       => __( '2-Wire Upgrade Kit', 'site-blocks' ),
				'tag'         => __( 'Keep existing cable', 'site-blocks' ),
				'description' => __( 'Where the run is sound, we convert old 2-wire intercoms to IP video instead of recabling the whole building.', 'site-blocks' ),
				'image'       => 'images/ajax/property/apartments.webp',
				'alt'         => __( 'Apartment building entry suited to a 2-wire intercom upgrade', 'site-blocks' ),
			),
		),
	);
}

/**
 * Property fit config.
 *
 * @return array<string, mixed>
 */
function site_blocks_akuvox_property_fit_config(): array {
	return array(
		'eyebrow' => __( 'Property Fit', 'site-blocks' ),
		'title'   => __( 'Specified For The Door You Actually Have.', 'site-blocks' ),
		'lead'    => __( 'House, strata or commercial — the station, wiring path and who can unlock all change. We design around that, then install it.', 'site-blocks' ),
		'panels'  => array(
			array(
				'title' => __( 'House Or Townhouse', 'site-blocks' ),
				'desc'  => __( 'Front door or gate station, indoor monitor and phone answering. Add a second entry if the property needs it.', 'site-blocks' ),
				'image' => 'images/ajax/property/home.webp',
				'alt'   => __( 'Akuvox intercom for a home', 'site-blocks' ),
			),
			array(
				'title' => __( 'Strata And Apartments', 'site-blocks' ),
				'desc'  => __( 'Directory panels, per-unit call routing and building-manager access, planned around strata and fire-egress rules. 2-wire reuse where it is sound.', 'site-blocks' ),
				'image' => 'images/ajax/property/apartments.webp',
				'alt'   => __( 'Akuvox intercom for Sydney apartments and strata', 'site-blocks' ),
			),
			array(
				'title' => __( 'Commercial Entries', 'site-blocks' ),
				'desc'  => __( 'Verified visitors, remote release for deliveries, and readers for staff — integrated with CCTV and access when the site needs both.', 'site-blocks' ),
				'image' => 'images/ajax/property/small-business.webp',
				'alt'   => __( 'Akuvox intercom for a Sydney commercial entry', 'site-blocks' ),
			),
		),
	);
}

/**
 * Install steps (visible strip — shared process-flow renderer is disabled site-wide).
 *
 * @return array<string, mixed>
 */
function site_blocks_akuvox_install_config(): array {
	return array(
		'eyebrow' => __( 'How installation works', 'site-blocks' ),
		'title'   => __( 'Four Steps From Site Check To SmartPlus Handover.', 'site-blocks' ),
		'steps'   => array(
			array(
				'title'       => __( 'Site Check', 'site-blocks' ),
				'description' => __( 'We look at the door, gate and existing cable before anyone specs hardware.', 'site-blocks' ),
				'icon'        => 'clipboard-check',
			),
			array(
				'title'       => __( 'Spec IP Or 2-Wire', 'site-blocks' ),
				'description' => __( 'Reuse sound 2-wire, or plan a new IP run when the pair will not carry video.', 'site-blocks' ),
				'icon'        => 'network',
			),
			array(
				'title'       => __( 'Install', 'site-blocks' ),
				'description' => __( 'Door station, indoor monitor and readers fitted and tested on the entry.', 'site-blocks' ),
				'icon'        => 'wrench',
			),
			array(
				'title'       => __( 'SmartPlus Handover', 'site-blocks' ),
				'description' => __( 'App users, codes and roles set up.', 'site-blocks' ),
				'icon'        => 'smartphone',
			),
		),
	);
}

/**
 * Compare table config (doorbell vs recable vs Akuvox with Safeguard).
 *
 * @return array<string, mixed>
 */
function site_blocks_akuvox_compare_config(): array {
	return array(
		'eyebrow'      => __( 'Compare', 'site-blocks' ),
		'title'        => __( 'Doorbell Kit, Recable, Or Akuvox With Safeguard.', 'site-blocks' ),
		'left_heading' => __( 'Video doorbell', 'site-blocks' ),
		'mid_heading'  => __( 'Recable everything', 'site-blocks' ),
		'right_heading'=> __( 'Akuvox with Safeguard', 'site-blocks' ),
		'rows'         => array(
			array(
				'label' => __( 'What it is', 'site-blocks' ),
				'left'  => __( 'Consumer doorbell on Wi-Fi', 'site-blocks' ),
				'mid'   => __( 'New Cat6 to every station', 'site-blocks' ),
				'right' => __( 'SIP video intercom, specified and installed', 'site-blocks' ),
			),
			array(
				'label' => __( 'Existing 2-wire', 'site-blocks' ),
				'left'  => __( 'Usually ignored', 'site-blocks' ),
				'mid'   => __( 'Pulled out and replaced', 'site-blocks' ),
				'right' => __( 'Reused when the run is sound', 'site-blocks' ),
			),
			array(
				'label' => __( 'Unlock and visitors', 'site-blocks' ),
				'left'  => __( 'App-only, often one household', 'site-blocks' ),
				'mid'   => __( 'Depends on whatever you buy next', 'site-blocks' ),
				'right' => __( 'Phone, indoor monitor, codes and roles', 'site-blocks' ),
			),
			array(
				'label' => __( 'Strata / multi-tenant', 'site-blocks' ),
				'left'  => __( 'Poor fit', 'site-blocks' ),
				'mid'   => __( 'Disruptive and slow', 'site-blocks' ),
				'right' => __( 'Directory, unit routing, manager access', 'site-blocks' ),
			),
			array(
				'label' => __( 'CCTV and access', 'site-blocks' ),
				'left'  => __( 'Separate DIY add-ons', 'site-blocks' ),
				'mid'   => __( 'Still needs a second specialist', 'site-blocks' ),
				'right' => __( 'One licensed installer, open stack', 'site-blocks' ),
			),
			array(
				'label' => __( 'Best next step', 'site-blocks' ),
				'left'  => __( 'Fine for a rental doorbell', 'site-blocks' ),
				'mid'   => __( 'Only when cable genuinely fails', 'site-blocks' ),
				'right' => __( 'Start a quote for your door', 'site-blocks' ),
			),
		),
	);
}

/**
 * FAQ items.
 *
 * @return array<int, array{q: string, a: string}>
 */
function site_blocks_akuvox_faq_items(): array {
	return array(
		array(
			'q' => __( 'Can I answer the door from my phone when I am out?', 'site-blocks' ),
			'a' => __( 'Yes. After we set up SmartPlus, a call at the door or gate reaches your smartphone so you can see the visitor, speak to them and unlock from anywhere with an internet connection.', 'site-blocks' ),
		),
		array(
			'q' => __( 'What happens if the internet goes down?', 'site-blocks' ),
			'a' => __( 'The door station and indoor monitor keep talking to each other on the local network. Remote answering on your phone needs internet; the wall monitor still takes the call.', 'site-blocks' ),
		),
		array(
			'q' => __( 'Can you reuse our existing intercom wiring?', 'site-blocks' ),
			'a' => __( 'Often, especially in apartments and older homes with 2-wire cabling. We assess the run on site and only recable when the existing pair will not carry a reliable video upgrade.', 'site-blocks' ),
		),
		array(
			'q' => __( 'Do you install Akuvox for strata and apartment buildings?', 'site-blocks' ),
			'a' => __( 'Yes. We design directory panels, per-unit call routing and building-manager access around fire-egress and strata requirements, then install and hand over SmartPlus roles.', 'site-blocks' ),
		),
		array(
			'q' => __( 'Can a tradesperson unlock without me being home?', 'site-blocks' ),
			'a' => __( 'Yes. You can release the door from the app, and many designs include temporary codes you issue and revoke for cleaners, couriers and contractors.', 'site-blocks' ),
		),
		array(
			'q' => __( 'How is this page different from Intercom Systems?', 'site-blocks' ),
			'a' => __( 'Intercom Systems is the category hub for video and audio entry. This page is the Akuvox brand landing: SIP video, 2-wire upgrades and SmartPlus, installed by Safeguard.', 'site-blocks' ),
		),
		array(
			'q' => __( 'How much does an Akuvox intercom cost?', 'site-blocks' ),
			'a' => __( 'It depends on the door, existing cable, how many stations and who needs to unlock. Request a quote and a technician reviews the design. We do not publish kit prices on this page.', 'site-blocks' ),
		),
		array(
			'q' => __( 'Is Safeguard the manufacturer of Akuvox?', 'site-blocks' ),
			'a' => __( 'No. Akuvox manufactures the hardware. Safeguard is an independent Australian installer. We specify, supply, install and support the system for your property under Master Licence #000103619.', 'site-blocks' ),
		),
	);
}

/**
 * Quote CTA overrides.
 *
 * @return array<string, mixed>
 */
function site_blocks_akuvox_quote_cta_config(): array {
	$ctas        = site_blocks_akuvox_ctas();
	$phone_label = '1300 225 276';

	return array(
		'heading_id'      => 'sg-akuvox-quote-cta-heading',
		'eyebrow'         => __( 'Request a quote', 'site-blocks' ),
		'title'           => __( 'Design Your Akuvox Intercom With Safeguard.', 'site-blocks' ),
		'lead'            => __( 'Tell us about the door, the wiring and who needs to answer. We will spec IP or a 2-wire upgrade and hand over SmartPlus after install.', 'site-blocks' ),
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
		'bg_image'        => site_blocks_asset_url( 'images/intercom/hero.webp' ),
		'features'        => array(
			array(
				'title'       => __( 'Property-Led Design', 'site-blocks' ),
				'description' => __( 'Station and cable path chosen for your entry.', 'site-blocks' ),
				'icon'        => 'technician.png',
				'accent'      => 'blue',
			),
			array(
				'title'       => __( 'Clear Install Path', 'site-blocks' ),
				'description' => __( 'IP or 2-wire, then SmartPlus handover.', 'site-blocks' ),
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
				'label' => __( 'Independent Australian Akuvox installer', 'site-blocks' ),
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
