<?php
/**
 * Monitoring silo, copy, SEO, and section data per page key.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared CTA URLs for Monitoring pages.
 *
 * @return array{primary_label: string, primary_url: string, secondary_label: string, secondary_url: string}
 */
function site_blocks_monitoring_ctas(): array {
	return array(
		'primary_label'   => __( 'Request a quote', 'site-blocks' ),
		'primary_url'     => home_url( '/contact/' ),
		'secondary_label' => __( 'Call 1300 225 276', 'site-blocks' ),
		'secondary_url'   => 'tel:1300225276',
	);
}

/**
 * Page keys in this silo.
 *
 * @return array<string, string> key => hierarchical slug path.
 */
function site_blocks_monitoring_page_slugs(): array {
	return array(
		'hub'                      => 'monitoring',
		'back-to-base'             => 'monitoring/back-to-base',
		'virtual-patrol'           => 'monitoring/virtual-patrol',
		'solar-cameras-monitoring' => 'monitoring/solar-cameras-monitoring',
	);
}

/**
 * Whether the current request is a Monitoring silo page.
 */
function site_blocks_is_monitoring_page(): bool {
	return null !== site_blocks_get_monitoring_page_key();
}

/**
 * Detect the active Monitoring page key.
 */
function site_blocks_get_monitoring_page_key(): ?string {
	foreach ( site_blocks_monitoring_page_slugs() as $key => $path ) {
		if ( is_page( $path ) ) {
			return $key;
		}
	}

	return null;
}

/**
 * Sections available per page key.
 *
 * @return array<string, array<int, string>>
 */
function site_blocks_monitoring_page_sections(): array {
	return array(
		'hub'                      => array( 'services', 'paths', 'integration', 'industries', 'portal', 'trust', 'faq', 'cta' ),
		'back-to-base'             => array( 'intro', 'communicators', 'response-plans', 'quote', 'portal', 'trust', 'faq', 'cta' ),
		'virtual-patrol'           => array( 'intro', 'compare', 'features', 'industries', 'requirements', 'portal', 'trust', 'faq', 'cta' ),
		'solar-cameras-monitoring' => array( 'intro', 'use-cases', 'technical', 'industries', 'portal', 'monitoring-integration', 'faq', 'cta' ),
	);
}

/**
 * Whether a section applies to the given page key.
 */
function site_blocks_monitoring_section_applies( string $page_key, string $section ): bool {
	$sections = site_blocks_monitoring_page_sections();

	return isset( $sections[ $page_key ] ) && in_array( $section, $sections[ $page_key ], true );
}

/**
 * Document title per page key.
 */
function site_blocks_monitoring_document_title( string $page_key ): string {
	$titles = array(
		'hub'                      => __( 'Alarm Monitoring Sydney | Back-to-Base | Safeguard', 'site-blocks' ),
		'back-to-base'             => __( 'Back-to-Base Alarm Monitoring Sydney | Safeguard', 'site-blocks' ),
		'virtual-patrol'           => __( 'Virtual Patrol Sydney | Remote CCTV Guarding | Safeguard', 'site-blocks' ),
		'solar-cameras-monitoring' => __( 'Solar CCTV Monitoring Sydney | 4G Site Cameras | Safeguard', 'site-blocks' ),
	);

	return $titles[ $page_key ] ?? '';
}

/**
 * Meta description per page key.
 */
function site_blocks_monitoring_get_meta_description( string $page_key ): string {
	$descriptions = array(
		'hub'                      => __( 'Alarm monitoring, back-to-base, virtual patrol and solar CCTV monitoring. One team for installation and response planning. Request a quote today.', 'site-blocks' ),
		'back-to-base'             => __( 'Back-to-base alarm monitoring, IP and 4G paths, clear response plans. Request a quote for your property.', 'site-blocks' ),
		'virtual-patrol'           => __( 'Virtual patrol and remote guarding, live CCTV tours, alarm verification and professional monitoring. Cut false callouts. Request an assessment.', 'site-blocks' ),
		'solar-cameras-monitoring' => __( 'Solar-powered security cameras with professional monitoring for construction sites, farms and remote properties. 4G back-to-base. Request a site quote.', 'site-blocks' ),
	);

	return $descriptions[ $page_key ] ?? '';
}

/**
 * Hero config per page key.
 *
 * @return array<string, mixed>|null
 */
function site_blocks_monitoring_hero_config( string $page_key ): ?array {
	$ctas = site_blocks_monitoring_ctas();
	$hub  = home_url( '/monitoring/' );

	$configs = array(
		'hub' => array_merge(
			$ctas,
			array(
				'id'            => 'sg-monitoring-hero-heading',
				'class'         => 'sg-monitoring-hero',
				'breadcrumb'    => array(
					array( 'label' => __( 'Home', 'site-blocks' ), 'url' => home_url( '/' ) ),
					array( 'label' => __( 'Monitoring & Response', 'site-blocks' ), 'current' => true ),
				),
				'badge'         => __( 'Monitoring & Response', 'site-blocks' ),
				'title_before'  => __( 'When Your Alarm Trips, Someone Should Be ', 'site-blocks' ),
				'title_accent'  => __( 'Watching', 'site-blocks' ),
				'title_after'   => __( '.', 'site-blocks' ),
				'lead'            => __( 'Safeguard provides professional alarm monitoring, back-to-base alarm response, virtual patrol over your CCTV, and monitored solar cameras for remote sites. Because we also install your alarms, cameras and access control, monitoring connects to real response plans, not three vendors pointing at each other.', 'site-blocks' ),
				'trust_aria_label'=> __( 'Why Safeguard monitoring', 'site-blocks' ),
				'trust_chips'     => array(
					array(
						'icon'  => 'id-card',
						'line1' => __( 'Master Licence', 'site-blocks' ),
						'line2' => '#000103619',
					),
					array(
						'icon'  => 'award',
						'line1' => __( 'ASIAL', 'site-blocks' ),
						'line2' => __( 'member', 'site-blocks' ),
					),
					array(
						'icon'  => 'clock',
						'line1' => __( '20+', 'site-blocks' ),
						'line2' => __( 'years', 'site-blocks' ),
					),
					array(
						'icon'  => 'wrench',
						'line1' => __( 'Install +', 'site-blocks' ),
						'line2' => __( 'monitor', 'site-blocks' ),
					),
				),
				'caption_title'   => __( 'One coordinated response, not three vendors', 'site-blocks' ),
				'caption_items'   => array(
					array(
						'icon'  => 'radio-tower',
						'title' => __( 'Back-To-Base', 'site-blocks' ),
						'desc'  => __( 'Alarm signals', 'site-blocks' ),
					),
					array(
						'icon'  => 'cctv',
						'title' => __( 'Virtual Patrol', 'site-blocks' ),
						'desc'  => __( 'CCTV tours', 'site-blocks' ),
					),
					array(
						'icon'  => 'sun',
						'title' => __( 'Solar Sites', 'site-blocks' ),
						'desc'  => __( 'Remote cameras', 'site-blocks' ),
					),
				),
				'hero_image'      => 'hub-hero.webp',
				'hero_alt'      => __( 'Monitoring operator reviewing alarm alerts at a professional security desk in Sydney', 'site-blocks' ),
			)
		),
		'back-to-base' => array_merge(
			$ctas,
			array(
				'id'            => 'sg-back-to-base-hero-heading',
				'class'         => 'sg-monitoring-hero sg-back-to-base-hero',
				'breadcrumb'    => array(
					array( 'label' => __( 'Home', 'site-blocks' ), 'url' => home_url( '/' ) ),
					array( 'label' => __( 'Monitoring & Response', 'site-blocks' ), 'url' => $hub ),
					array( 'label' => __( 'Back-to-Base Monitoring', 'site-blocks' ), 'current' => true ),
				),
				'badge'         => __( 'Back-to-Base Monitoring', 'site-blocks' ),
				'title_before'  => __( 'Someone Watching When You ', 'site-blocks' ),
				'title_accent'  => __( 'Cannot', 'site-blocks' ),
				'title_after'   => __( '.', 'site-blocks' ),
				'lead'            => __( 'Safeguard connects your alarm to professional back-to-base monitoring. When sensors trigger, an agreed response plan can set out keyholder contact, patrol attendance or escalation. Because we install and maintain your system too, monitoring is not an afterthought from a third party.', 'site-blocks' ),
				'trust_aria_label'=> __( 'Back-to-base monitoring credentials', 'site-blocks' ),
				'trust_chips'     => array(
					array(
						'icon'  => 'id-card',
						'line1' => __( 'Master Licence', 'site-blocks' ),
						'line2' => '#000103619',
					),
					array(
						'icon'  => 'award',
						'line1' => __( 'ASIAL', 'site-blocks' ),
						'line2' => __( 'member', 'site-blocks' ),
					),
					array(
						'icon'  => 'radio-tower',
						'line1' => __( 'IP & 4G', 'site-blocks' ),
						'line2' => __( 'monitoring paths', 'site-blocks' ),
					),
					array(
						'icon'  => 'phone',
						'line1' => __( 'Call us', 'site-blocks' ),
						'line2' => '1300 225 276',
					),
				),
				'caption_title'   => __( 'Dual paths keep your alarm online', 'site-blocks' ),
				'caption_items'   => array(
					array(
						'icon'  => 'router',
						'title' => __( 'IP', 'site-blocks' ),
						'desc'  => __( 'Fixed internet', 'site-blocks' ),
					),
					array(
						'icon'  => 'smartphone',
						'title' => __( '4G', 'site-blocks' ),
						'desc'  => __( 'Cellular backup', 'site-blocks' ),
					),
					array(
						'icon'  => 'shield-check',
						'title' => __( 'Response', 'site-blocks' ),
						'desc'  => __( 'Your plan', 'site-blocks' ),
					),
				),
				'hero_image'      => 'back-to-base-hero-centre.webp',
				'hero_alt'        => __( 'Monitoring centre operator supervising back-to-base alarm events', 'site-blocks' ),
			)
		),
		'virtual-patrol' => array_merge(
			$ctas,
			array(
				'id'            => 'sg-virtual-patrol-hero-heading',
				'class'         => 'sg-monitoring-hero sg-virtual-patrol-hero',
				'breadcrumb'    => array(
					array( 'label' => __( 'Home', 'site-blocks' ), 'url' => home_url( '/' ) ),
					array( 'label' => __( 'Monitoring & Response', 'site-blocks' ), 'url' => $hub ),
					array( 'label' => __( 'Virtual Patrol', 'site-blocks' ), 'current' => true ),
				),
				'badge'         => __( 'Virtual Patrol', 'site-blocks' ),
				'title_before'  => __( 'Professional Eyes On Your Cameras, ', 'site-blocks' ),
				'title_accent'  => __( 'Remotely', 'site-blocks' ),
				'title_after'   => __( '.', 'site-blocks' ),
				'lead'            => __( 'Safeguard\'s virtual patrol service provides scheduled tours, alarm-triggered checks and documented reporting over your CCTV, without keeping a static guard on site every hour. It can be integrated with monitoring and physical response options when attendance is required.', 'site-blocks' ),
				'trust_aria_label'=> __( 'Virtual patrol credentials', 'site-blocks' ),
				'trust_chips'     => array(
					array(
						'icon'  => 'id-card',
						'line1' => __( 'Master Licence', 'site-blocks' ),
						'line2' => '#000103619',
					),
					array(
						'icon'  => 'award',
						'line1' => __( 'ASIAL', 'site-blocks' ),
						'line2' => __( 'member', 'site-blocks' ),
					),
					array(
						'icon'  => 'cctv',
						'line1' => __( 'Scheduled', 'site-blocks' ),
						'line2' => __( 'CCTV tours', 'site-blocks' ),
					),
					array(
						'icon'  => 'phone',
						'line1' => __( 'Call us', 'site-blocks' ),
						'line2' => '1300 225 276',
					),
				),
				'caption_title'   => __( 'Remote guarding over your existing cameras', 'site-blocks' ),
				'caption_items'   => array(
					array(
						'icon'  => 'calendar-clock',
						'title' => __( 'Scheduled', 'site-blocks' ),
						'desc'  => __( 'Patrol tours', 'site-blocks' ),
					),
					array(
						'icon'  => 'bell-ring',
						'title' => __( 'Alarm', 'site-blocks' ),
						'desc'  => __( 'Triggered checks', 'site-blocks' ),
					),
					array(
						'icon'  => 'clipboard-list',
						'title' => __( 'Reports', 'site-blocks' ),
						'desc'  => __( 'Documented activity', 'site-blocks' ),
					),
				),
				'hero_image'      => 'virtual-patrol-hero.webp',
				'hero_alt'        => __( 'Virtual patrol operator reviewing CCTV feeds in a bright monitoring room', 'site-blocks' ),
			)
		),
		'solar-cameras-monitoring' => array_merge(
			$ctas,
			array(
				'id'              => 'sg-solar-monitoring-hero-heading',
				'class'           => 'sg-monitoring-hero sg-solar-monitoring-hero',
				'breadcrumb'      => array(
					array( 'label' => __( 'Home', 'site-blocks' ), 'url' => home_url( '/' ) ),
					array( 'label' => __( 'Monitoring & Response', 'site-blocks' ), 'url' => $hub ),
					array( 'label' => __( 'Solar Cameras with Monitoring', 'site-blocks' ), 'current' => true ),
				),
				'badge'           => __( 'Solar Monitoring', 'site-blocks' ),
				'title_before'    => __( 'Security Where There Is No Power, ', 'site-blocks' ),
				'title_accent'    => __( 'Monitored', 'site-blocks' ),
				'title_after'     => __( '.', 'site-blocks' ),
				'lead'            => __( 'Safeguard supplies solar-powered security cameras with 4G connectivity and professional monitoring for construction sites, farms, vacant land and remote properties. Deploy without mains power or fixed internet, monitor remotely, and escalate to patrol response when your plan requires it.', 'site-blocks' ),
				'trust_aria_label'=> __( 'Solar monitoring credentials', 'site-blocks' ),
				'trust_chips'     => array(
					array(
						'icon'  => 'id-card',
						'line1' => __( 'Master Licence', 'site-blocks' ),
						'line2' => '#000103619',
					),
					array(
						'icon'  => 'award',
						'line1' => __( 'ASIAL', 'site-blocks' ),
						'line2' => __( 'member', 'site-blocks' ),
					),
					array(
						'icon'  => 'sun',
						'line1' => __( 'Solar +', 'site-blocks' ),
						'line2' => __( '4G', 'site-blocks' ),
					),
					array(
						'icon'  => 'phone',
						'line1' => __( 'Call us', 'site-blocks' ),
						'line2' => '1300 225 276',
					),
				),
				'caption_title'   => __( 'Security where there is no power or fixed internet', 'site-blocks' ),
				'caption_items'   => array(
					array(
						'icon'  => 'sun',
						'title' => __( 'Solar', 'site-blocks' ),
						'desc'  => __( 'Off-grid power', 'site-blocks' ),
					),
					array(
						'icon'  => 'signal',
						'title' => __( '4G', 'site-blocks' ),
						'desc'  => __( 'Remote connectivity', 'site-blocks' ),
					),
					array(
						'icon'  => 'shield-check',
						'title' => __( 'Monitor', 'site-blocks' ),
						'desc'  => __( 'Professional response', 'site-blocks' ),
					),
				),
				'secondary_label' => __( 'Speak to Us', 'site-blocks' ),
				'secondary_url'   => home_url( '/contact/' ),
				'hero_image'      => 'solar-monitoring-hero.webp',
				'hero_alt'        => __( 'Solar-powered security camera on a construction site mast with Sydney skyline in distance', 'site-blocks' ),
			)
		),
	);

	return $configs[ $page_key ] ?? null;
}

/**
 * FAQ items per page key.
 *
 * @return array<int, array{q: string, a: string}>
 */
function site_blocks_monitoring_faq_items( string $page_key ): array {
	$faqs = array(
		'hub' => array(
			array(
				'q' => __( 'What is back-to-base alarm monitoring?', 'site-blocks' ),
				'a' => __( 'Your alarm system can send signals to a professional monitoring centre. An agreed response plan can set out contact with you, keyholders or attendance arrangements.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Do I need monitoring if I have the Ajax app?', 'site-blocks' ),
				'a' => __( 'Self-monitoring via the app suits many homes. You receive alerts on your phone. Professional monitoring links the alarm to an agreed response plan for when you are asleep, overseas or unreachable.', 'site-blocks' ),
			),
			array(
				'q' => __( 'What happens when my alarm activates at 2am?', 'site-blocks' ),
				'a' => __( 'Alarm monitoring can be linked to an agreed response plan, typically with keyholder notification first, followed by patrol or alarm response attendance if required.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Does monitoring work if my internet drops?', 'site-blocks' ),
				'a' => __( 'Dual-path communication (IP plus cellular backup) keeps many systems online. We recommend the right path for your property during assessment, residential and commercial requirements differ.', 'site-blocks' ),
			),
			array(
				'q' => __( 'What is virtual patrol?', 'site-blocks' ),
				'a' => __( 'Scheduled and alarm-triggered CCTV tours, remote guarding without a physical guard on site every hour. See our Virtual Patrol page for detail.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Can Safeguard monitor a site with no mains power?', 'site-blocks' ),
				'a' => __( 'Yes, solar-powered cameras with 4G connectivity and professional monitoring suit construction sites, farms and remote properties. See Solar Cameras with Monitoring.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Do you publish monitoring prices on the website?', 'site-blocks' ),
				'a' => __( 'No. Monitoring is quoted per property based on equipment, communication path and response plan. Request a quote or call 1300 225 276 to speak to our team.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Are you licensed to provide monitoring in NSW?', 'site-blocks' ),
				'a' => __( 'Safeguard operates under Master Licence #000103619 and is an ASIAL member.', 'site-blocks' ),
			),
		),
		'back-to-base' => array(
			array(
				'q' => __( 'What is back-to-base monitoring?', 'site-blocks' ),
				'a' => __( 'Your alarm can send signals to a professional monitoring centre. An agreed response plan sets out how alerts are handled, rather than relying on you to see a phone notification.', 'site-blocks' ),
			),
			array(
				'q' => __( 'What\'s the difference between IP and 4G monitoring?', 'site-blocks' ),
				'a' => __( 'IP uses your internet connection; 4G uses the mobile network. 4G suits commercial sites and anywhere you need backup if broadband fails.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Can you monitor my existing alarm?', 'site-blocks' ),
				'a' => __( 'Often yes, we assess your panel and communicator during a site review.', 'site-blocks' ),
			),
			array(
				'q' => __( 'What is a daily test or poll signal?', 'site-blocks' ),
				'a' => __( 'Regular check-ins confirm your system is still communicating. If communication fails, you are notified to arrange service.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Who gets called first when my alarm trips?', 'site-blocks' ),
				'a' => __( 'Your documented keyholder list, in the order you specify. Patrol or alarm response attendance follows your plan if required.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Do you publish monitoring prices online?', 'site-blocks' ),
				'a' => __( 'No. We quote monitoring per property. Request a quote and the team will include monitoring options where they belong in the job.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Can I switch from self-monitoring to back-to-base later?', 'site-blocks' ),
				'a' => __( 'Yes, many customers start with the app and add professional monitoring when their risk or lifestyle changes.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Is monitoring prepaid?', 'site-blocks' ),
				'a' => __( 'Professional monitoring is typically billed in advance on a quarterly or annual cycle. Your quote confirms billing terms.', 'site-blocks' ),
			),
		),
		'virtual-patrol' => array(
			array(
				'q' => __( 'What is virtual patrol?', 'site-blocks' ),
				'a' => __( 'Scheduled and alarm-triggered CCTV tours, remote guarding without a physical guard on site every hour.', 'site-blocks' ),
			),
			array(
				'q' => __( 'How is virtual patrol different from mobile patrol?', 'site-blocks' ),
				'a' => __( 'Virtual patrol is remote via cameras. Mobile patrol sends a licensed officer to your site in person. Many sites use both.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Do I need speakers for virtual patrol?', 'site-blocks' ),
				'a' => __( 'Not always. Tours and checks work without audio. Speakers allow a voice challenge where they are installed.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Can virtual patrol replace a static guard?', 'site-blocks' ),
				'a' => __( 'It can reduce cost for some after-hours sites, but high-risk locations may still need physical presence. We assess at quote stage.', 'site-blocks' ),
			),
			array(
				'q' => __( 'How often are tours scheduled?', 'site-blocks' ),
				'a' => __( 'Agreed in your service plan based on risk, from several checks per night to broader schedules for lower-risk sites.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Do you publish virtual patrol prices online?', 'site-blocks' ),
				'a' => __( 'No. Virtual patrol is quoted per site based on camera count, tour frequency and response plan.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Can virtual patrol work with my existing cameras?', 'site-blocks' ),
				'a' => __( 'Often yes, we confirm compatibility and coverage during assessment.', 'site-blocks' ),
			),
		),
		'solar-cameras-monitoring' => array(
			array(
				'q' => __( 'Do solar cameras work in winter or overcast weather?', 'site-blocks' ),
				'a' => __( 'Systems are sized for local conditions with battery backup, we specify equipment for your site during quoting.', 'site-blocks' ),
			),
			array(
				'q' => __( 'What happens if the battery runs low?', 'site-blocks' ),
				'a' => __( 'Monitoring paths can alert you to power or communication issues so service is arranged before coverage gaps.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Is 4G monitoring included?', 'site-blocks' ),
				'a' => __( '4G connectivity and monitoring are part of the quoted package, details confirmed in your proposal, not published as standard rates online.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Can you monitor a site with no mains power at all?', 'site-blocks' ),
				'a' => __( 'Yes, that is the primary use case for solar monitoring deployments.', 'site-blocks' ),
			),
			array(
				'q' => __( 'How fast can a unit be deployed?', 'site-blocks' ),
				'a' => __( 'Many sites can be live within days of approval, subject to site access and equipment availability.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Do you publish solar monitoring prices online?', 'site-blocks' ),
				'a' => __( 'No. Every site differs, request a quote for hardware, install and monitoring combined.', 'site-blocks' ),
			),
			array(
				'q' => __( 'Can cameras move as my construction site progresses?', 'site-blocks' ),
				'a' => __( 'Often yes, relocation can be arranged as part of an ongoing construction security plan.', 'site-blocks' ),
			),
		),
	);

	return $faqs[ $page_key ] ?? array();
}

/**
 * Hub service cards (3 spokes) for the simple text grid.
 *
 * @return array<int, array{title: string, desc: string, url: string}>
 */
function site_blocks_monitoring_hub_services(): array {
	$items = site_blocks_monitoring_hub_photo_services();
	$out   = array();

	foreach ( $items as $item ) {
		$out[] = array(
			'title' => (string) $item['title'],
			'desc'  => (string) $item['desc'],
			'url'   => (string) $item['url'],
		);
	}

	return $out;
}

/**
 * Hub service cards for the photo-options template.
 *
 * @return array<int, array{title: string, desc: string, url: string, image: string, alt: string, icon: string}>
 */
function site_blocks_monitoring_hub_photo_services(): array {
	return array(
		array(
			'title' => __( 'Back-To-Base Alarm Monitoring', 'site-blocks' ),
			'desc'  => __( 'Alarm monitoring linked to the response plan for the site.', 'site-blocks' ),
			'url'   => home_url( '/monitoring/back-to-base/' ),
			'image' => 'images/monitoring/back-to-base-monitoring-centre.webp',
			'alt'   => __( 'Operators in a professional back-to-base monitoring centre', 'site-blocks' ),
			'icon'  => 'headset',
		),
		array(
			'title' => __( 'Virtual Patrol & Remote Guarding', 'site-blocks' ),
			'desc'  => __( 'Scheduled and alarm-triggered CCTV tours, professional cover without a guard on site every hour.', 'site-blocks' ),
			'url'   => home_url( '/monitoring/virtual-patrol/' ),
			'image' => 'images/monitoring/virtual-patrol-hero.webp',
			'alt'   => __( 'Virtual patrol operators reviewing live CCTV feeds', 'site-blocks' ),
			'icon'  => 'eye',
		),
		array(
			'title' => __( 'Solar Cameras With Monitoring', 'site-blocks' ),
			'desc'  => __( 'Solar-powered cameras with 4G connectivity and professional monitoring for construction, rural and off-grid sites.', 'site-blocks' ),
			'url'   => home_url( '/monitoring/solar-cameras-monitoring/' ),
			'image' => 'images/monitoring/solar-monitoring-hero.webp',
			'alt'   => __( 'Solar-powered security camera with professional monitoring', 'site-blocks' ),
			'icon'  => 'sun',
		),
	);
}

/**
 * Hub services section heading.
 *
 * @return array{title: string, intro: string}
 */
function site_blocks_monitoring_services_heading(): array {
	return array(
		'title' => __( 'Choose The Monitoring That Fits Your Site', 'site-blocks' ),
		'intro' => __( 'Three ways to cover a site, whether your alarm trips, your cameras need scheduled checks, or the site has no fixed power.', 'site-blocks' ),
	);
}

/**
 * Cross-link integration services.
 *
 * @return array<int, array{title: string, url: string}>
 */
function site_blocks_monitoring_integration_links(): array {
	return array(
		array(
			'title' => __( 'Alarm Systems', 'site-blocks' ),
			'url'   => home_url( '/alarm-systems/' ),
		),
		array(
			'title' => __( 'Ajax Alarm Systems', 'site-blocks' ),
			'url'   => home_url( '/brands/ajax-alarm-systems/' ),
		),
		array(
			'title' => __( 'CCTV & Security Cameras', 'site-blocks' ),
			'url'   => home_url( '/cctv-security-cameras/' ),
		),
		array(
			'title' => __( 'Access Control', 'site-blocks' ),
			'url'   => home_url( '/access-control/' ),
		),
		array(
			'title' => __( 'Physical Security', 'site-blocks' ),
			'url'   => home_url( '/physical-security/' ),
		),
		array(
			'title' => __( 'Mobile Patrols', 'site-blocks' ),
			'url'   => home_url( '/physical-security/mobile-patrols/' ),
		),
	);
}

/**
 * Related services card strip config per cross-link variant.
 *
 * @param bool|string $variant Config key.
 * @return array<string, mixed>|null
 */
function site_blocks_monitoring_related_services_config( $variant ): ?array {
	$monitoring_hub = home_url( '/monitoring/' );

	$base = array(
		'eyebrow'       => __( 'Related services', 'site-blocks' ),
		'title_before'  => __( 'Explore The ', 'site-blocks' ),
		'title_accent'  => __( 'Connected', 'site-blocks' ),
		'title_after'   => __( ' services', 'site-blocks' ),
		'intro'         => __( 'If you need another part of the system, these are the most common next steps.', 'site-blocks' ),
		'use_brand_icons' => false,
		'hub_link'      => array(
			'label' => __( 'View all related services', 'site-blocks' ),
			'url'   => $monitoring_hub,
		),
	);

	$solar_cards = array(
		array(
			'title' => __( 'Back-To-Base Monitoring', 'site-blocks' ),
			'desc'  => __( 'Alarm monitoring for connected systems.', 'site-blocks' ),
			'url'   => home_url( '/monitoring/back-to-base/' ),
			'icon'  => 'back-to-base',
		),
		array(
			'title' => __( 'Virtual Patrol', 'site-blocks' ),
			'desc'  => __( 'Scheduled and alarm-triggered CCTV checks.', 'site-blocks' ),
			'url'   => home_url( '/monitoring/virtual-patrol/' ),
			'icon'  => 'virtual-patrol',
		),
		array(
			'title' => __( 'CCTV & Security Cameras', 'site-blocks' ),
			'desc'  => __( 'Camera systems that support verification and review.', 'site-blocks' ),
			'url'   => home_url( '/cctv-security-cameras/' ),
			'icon'  => 'cctv',
		),
		array(
			'title' => __( 'Mobile Patrols', 'site-blocks' ),
			'desc'  => __( 'On-site attendance, checks and physical response.', 'site-blocks' ),
			'url'   => home_url( '/physical-security/mobile-patrols/' ),
			'icon'  => 'mobile-patrols',
		),
	);

	if ( 'solar' === $variant ) {
		// Solar integration, brand icons from Safeguard asset pack.
		return array_merge(
			$base,
			array(
				'use_brand_icons' => true,
				'cards'           => $solar_cards,
			)
		);
	}

	if ( 'hub' === $variant || true === $variant ) {
		return array_merge(
			$base,
			array(
				'cards' => $solar_cards,
			)
		);
	}

	if ( 'compatible' === $variant ) {
		return array_merge(
			$base,
			array(
				'cards' => array(
					array(
						'title' => __( 'Ajax Alarm Systems', 'site-blocks' ),
						'desc'  => __( 'Wireless systems with app and professional monitoring paths.', 'site-blocks' ),
						'url'   => home_url( '/brands/ajax-alarm-systems/' ),
						'icon'  => 'ajax',
					),
					array(
						'title' => __( 'Alarm Systems', 'site-blocks' ),
						'desc'  => __( 'Wireless, wired and smart alarm solutions for your property.', 'site-blocks' ),
						'url'   => home_url( '/alarm-systems/' ),
						'icon'  => 'alarm-systems',
					),
					array(
						'title' => __( 'Virtual Patrol', 'site-blocks' ),
						'desc'  => __( 'Scheduled and alarm-triggered CCTV checks.', 'site-blocks' ),
						'url'   => home_url( '/monitoring/virtual-patrol/' ),
						'icon'  => 'virtual-patrol',
					),
				),
			)
		);
	}

	if ( 'virtual-patrol-req' === $variant ) {
		return array_merge(
			$base,
			array(
				'hub_link' => null,
				'cards'    => array(
					array(
						'title' => __( 'CCTV & Security Cameras', 'site-blocks' ),
						'desc'  => __( 'Camera systems that support verification and review.', 'site-blocks' ),
						'url'   => home_url( '/cctv-security-cameras/' ),
						'icon'  => 'cctv',
					),
					array(
						'title' => __( 'Solar Cameras With Monitoring', 'site-blocks' ),
						'desc'  => __( 'Off-grid solar and 4G cameras with professional monitoring.', 'site-blocks' ),
						'url'   => home_url( '/monitoring/solar-cameras-monitoring/' ),
						'icon'  => 'solar',
					),
					array(
						'title' => __( 'Mobile Patrols', 'site-blocks' ),
						'desc'  => __( 'On-site attendance, checks and physical response.', 'site-blocks' ),
						'url'   => home_url( '/physical-security/mobile-patrols/' ),
						'icon'  => 'mobile-patrols',
					),
				),
			)
		);
	}

	return null;
}

/**
 * End-of-page related services grid config per monitoring page key.
 *
 * @return array<string, mixed>|null
 */
function site_blocks_monitoring_related_page_grid_config( string $page_key ): ?array {
	$grids = array(
		'hub' => array(
			'heading_id'    => 'sg-monitoring-related-heading',
			'section_class' => 'sg-monitoring-related',
			'title_before'  => __( 'Related ', 'site-blocks' ),
			'title_accent'  => __( 'Services', 'site-blocks' ),
			'cards'         => array(
				array( 'title' => __( 'Alarm Systems', 'site-blocks' ), 'desc' => __( 'Wireless and smart alarms we connect to monitoring.', 'site-blocks' ), 'url' => home_url( '/alarm-systems/' ), 'icon' => 'alarm-systems.png' ),
				array( 'title' => __( 'CCTV & Security Cameras', 'site-blocks' ), 'desc' => __( 'Cameras for verification and virtual patrol.', 'site-blocks' ), 'url' => home_url( '/cctv-security-cameras/' ), 'icon' => 'ip-camera.png' ),
				array( 'title' => __( 'Mobile Patrols', 'site-blocks' ), 'desc' => __( 'On-site attendance when response plans require it.', 'site-blocks' ), 'url' => home_url( '/physical-security/mobile-patrols/' ), 'icon' => 'support.png' ),
				array( 'title' => __( 'Access Control', 'site-blocks' ), 'desc' => __( 'Manage entry alongside alarm and camera cover.', 'site-blocks' ), 'url' => home_url( '/access-control/' ), 'icon' => 'access-control.png' ),
			),
		),
		'back-to-base' => array(
			'heading_id'    => 'sg-monitoring-b2b-related-heading',
			'section_class' => 'sg-monitoring-b2b-related',
			'title_before'  => __( 'Often Installed ', 'site-blocks' ),
			'title_accent'  => __( 'With', 'site-blocks' ),
			'cards'         => array(
				array( 'title' => __( 'Ajax Alarm Systems', 'site-blocks' ), 'desc' => __( 'Wireless systems with IP and 4G reporting paths.', 'site-blocks' ), 'url' => home_url( '/brands/ajax-alarm-systems/' ), 'icon' => 'alarm-systems.png' ),
				array( 'title' => __( 'Virtual Patrol', 'site-blocks' ), 'desc' => __( 'Add live CCTV verification to alarm events.', 'site-blocks' ), 'url' => home_url( '/monitoring/virtual-patrol/' ), 'icon' => 'support.png' ),
				array( 'title' => __( 'Solar Camera Monitoring', 'site-blocks' ), 'desc' => __( 'Remote sites without fixed internet.', 'site-blocks' ), 'url' => home_url( '/monitoring/solar-cameras-monitoring/' ), 'icon' => 'ip-camera.png' ),
				array( 'title' => __( 'Mobile Patrols', 'site-blocks' ), 'desc' => __( 'Physical attendance when your plan requires it.', 'site-blocks' ), 'url' => home_url( '/physical-security/mobile-patrols/' ), 'icon' => 'support.png' ),
			),
		),
		'virtual-patrol' => array(
			'heading_id'    => 'sg-monitoring-vp-related-heading',
			'section_class' => 'sg-monitoring-vp-related',
			'title_before'  => __( 'Works Best ', 'site-blocks' ),
			'title_accent'  => __( 'With', 'site-blocks' ),
			'cards'         => array(
				array( 'title' => __( 'CCTV & Security Cameras', 'site-blocks' ), 'desc' => __( 'Camera systems used for checks and verification.', 'site-blocks' ), 'url' => home_url( '/cctv-security-cameras/' ), 'icon' => 'ip-camera.png' ),
				array( 'title' => __( 'Back-To-Base Monitoring', 'site-blocks' ), 'desc' => __( 'Alarm signals with agreed response plans.', 'site-blocks' ), 'url' => home_url( '/monitoring/back-to-base/' ), 'icon' => 'support.png' ),
				array( 'title' => __( 'Solar Camera Monitoring', 'site-blocks' ), 'desc' => __( 'Temporary and remote sites on 4G.', 'site-blocks' ), 'url' => home_url( '/monitoring/solar-cameras-monitoring/' ), 'icon' => 'ip-camera.png' ),
				array( 'title' => __( 'Mobile Patrols', 'site-blocks' ), 'desc' => __( 'Escalate to on-site attendance when required.', 'site-blocks' ), 'url' => home_url( '/physical-security/mobile-patrols/' ), 'icon' => 'support.png' ),
			),
		),
		'solar-cameras-monitoring' => array(
			'heading_id'    => 'sg-monitoring-solar-related-heading',
			'section_class' => 'sg-monitoring-solar-related',
			'title_before'  => __( 'Related ', 'site-blocks' ),
			'title_accent'  => __( 'Services', 'site-blocks' ),
			'cards'         => array(
				array( 'title' => __( 'Back-To-Base Monitoring', 'site-blocks' ), 'desc' => __( 'Back-to-base cover for connected alarms.', 'site-blocks' ), 'url' => home_url( '/monitoring/back-to-base/' ), 'icon' => 'support.png' ),
				array( 'title' => __( 'Virtual Patrol', 'site-blocks' ), 'desc' => __( 'Operator tours over live camera feeds.', 'site-blocks' ), 'url' => home_url( '/monitoring/virtual-patrol/' ), 'icon' => 'ip-camera.png' ),
				array( 'title' => __( 'CCTV & Security Cameras', 'site-blocks' ), 'desc' => __( 'Permanent camera systems for powered sites.', 'site-blocks' ), 'url' => home_url( '/cctv-security-cameras/' ), 'icon' => 'ip-camera.png' ),
				array( 'title' => __( 'Mobile Patrols', 'site-blocks' ), 'desc' => __( 'Physical checks and alarm response.', 'site-blocks' ), 'url' => home_url( '/physical-security/mobile-patrols/' ), 'icon' => 'support.png' ),
			),
		),
	);

	return $grids[ $page_key ] ?? null;
}

/**
 * Hub process steps.
 *
 * @return array<int, array{title: string, description: string}>
 */
function site_blocks_monitoring_process_steps(): array {
	return array(
		array(
			'title'       => __( 'Site Assessment', 'site-blocks' ),
			'description' => __( 'We review your risk, hours, access and existing equipment.', 'site-blocks' ),
		),
		array(
			'title'       => __( 'Tailored Plan', 'site-blocks' ),
			'description' => __( 'The right mix of back-to-base, virtual patrol, solar monitoring and physical response.', 'site-blocks' ),
		),
		array(
			'title'       => __( 'Connection & Commissioning', 'site-blocks' ),
			'description' => __( 'Your alarm or cameras are connected, tested and documented.', 'site-blocks' ),
		),
		array(
			'title'       => __( 'Monitoring & Reporting', 'site-blocks' ),
			'description' => __( 'Clear incident logs and agreed notifications.', 'site-blocks' ),
		),
		array(
			'title'       => __( 'Review', 'site-blocks' ),
			'description' => __( 'We refine coverage as your site, staff or build phase changes.', 'site-blocks' ),
		),
	);
}

/**
 * Hub three levels of cover (paths section).
 *
 * @return array{title: string, intro: string, columns: array<int, array{title: string, body: string}>, note: string}
 */
function site_blocks_monitoring_paths_config(): array {
	return array(
		'title'   => __( 'Three Levels Of Cover', 'site-blocks' ),
		'intro'   => __( 'Not every property needs the same response. Safeguard helps you choose, and we can combine options as your risk changes.', 'site-blocks' ),
		'columns' => array(
			array(
				'title' => __( 'Self-Monitoring Via App', 'site-blocks' ),
				'body'  => __( 'Monitor compatible systems from your phone with instant alerts when sensors trigger. You stay in control, but you need to be available to act. Best for low-risk sites or as a starting point before professional monitoring.', 'site-blocks' ),
			),
			array(
				'title' => __( 'Professional Back-To-Base', 'site-blocks' ),
				'body'  => __( 'When an alarm activates, the agreed response plan sets out keyholder contact, patrol attendance or escalation. This suits a site where someone may not be reachable at 2am.', 'site-blocks' ),
			),
			array(
				'title' => __( 'Virtual Patrol', 'site-blocks' ),
				'body'  => __( 'CCTV feeds are checked on a schedule or when alarms trigger, a remote guarding layer for warehouses, yards and after-hours sites. Different from mobile patrols: no vehicle on site unless your plan requires it.', 'site-blocks' ),
			),
		),
		'note'    => __( 'Monitoring is quoted per property based on communication path, response plan and equipment. We do not publish standard rates on this site, request a quote for your site.', 'site-blocks' ),
	);
}

/**
 * Split-section data for a page key and section id.
 *
 * @return array<string, mixed>|null
 */
function site_blocks_monitoring_split_config( string $page_key, string $section ): ?array {
	$hub = home_url( '/monitoring/' );

	$splits = array(
		'hub' => array(
			'how-it-works' => array(
				'id'           => 'sg-monitoring-how-heading',
				'class'        => 'sg-monitoring-how-it-works',
				'title_before' => __( 'From Alert To ', 'site-blocks' ),
				'title_accent' => __( 'Action', 'site-blocks' ),
				'title_after'  => __( ' in four steps', 'site-blocks' ),
				'list'         => array(
					array( 'title' => __( 'Signal', 'site-blocks' ), 'desc' => __( 'Your alarm, camera or sensor detects an event and sends a signal to Safeguard\'s monitoring path.', 'site-blocks' ) ),
					array( 'title' => __( 'Receive', 'site-blocks' ), 'desc' => __( 'The alert is checked against your site profile and response plan.', 'site-blocks' ) ),
					array( 'title' => __( 'Verify', 'site-blocks' ), 'desc' => __( 'Where cameras are integrated, the cause can be checked before escalating, reducing false callouts.', 'site-blocks' ) ),
					array( 'title' => __( 'Respond', 'site-blocks' ), 'desc' => __( 'Keyholders are notified, virtual patrol checks run, mobile patrols or alarm response officers are dispatched, per your agreed plan.', 'site-blocks' ) ),
				),
				'image'        => 'hub-how-it-works.webp',
				'alt'          => __( 'Back-to-base alarm signal received at monitoring centre', 'site-blocks' ),
				'reverse'      => false,
			),
			'integration' => array(
				'id'           => 'sg-monitoring-integration-heading',
				'class'        => 'sg-monitoring-integration',
				'band'         => 'white',
				'title_before' => __( 'Installed, Monitored And ', 'site-blocks' ),
				'title_accent' => __( 'Responded To', 'site-blocks' ),
				'title_after'  => __( ' by one team', 'site-blocks' ),
				'paragraphs'   => array(
					__( 'Safeguard installs the alarms, cameras, access control and guarding, and links monitoring to the response plan, so the parts work together when something happens.', 'site-blocks' ),
					__( 'A camera flags movement, the plan sets out who checks it, and a guard responds when attendance is required. It\'s one coordinated response, not three vendors pointing at each other.', 'site-blocks' ),
				),
				'image'        => 'hub-integration.webp',
				'alt'          => __( 'Safeguard technician linking alarm and CCTV systems', 'site-blocks' ),
				'reverse'      => false,
			),
		),
		'back-to-base' => array(
			'intro' => array(
				'id'           => 'sg-btb-intro-heading',
				'class'        => 'sg-back-to-base-intro',
				'title_before' => __( 'Self-Monitoring Fails When The Phone Is ', 'site-blocks' ),
				'title_accent' => __( 'Off', 'site-blocks' ),
				'title_after'  => __( '.', 'site-blocks' ),
				'paragraphs'   => array(
					__( 'App alerts work until you are asleep, in a meeting, on a flight or simply miss the notification. Back-to-base monitoring links the alarm to an agreed response plan.', 'site-blocks' ),
					sprintf(
						/* translators: %s: link to monitoring hub */
						__( 'Part of Safeguard\'s %s.', 'site-blocks' ),
						'<a href="' . esc_url( $hub ) . '">' . esc_html__( 'alarm monitoring services', 'site-blocks' ) . '</a>'
					),
				),
				'paragraphs_html' => true,
				'image'           => 'back-to-base-monitoring-centre.webp',
				'alt'             => __( 'Monitoring centre operator handling back-to-base alarm events', 'site-blocks' ),
				'reverse'         => false,
			),
			'response-plans' => array(
				'id'           => 'sg-btb-response-heading',
				'class'        => 'sg-back-to-base-response',
				'title_before' => __( 'Your Response Plan, Agreed Before Monitoring ', 'site-blocks' ),
				'title_accent' => __( 'Starts', 'site-blocks' ),
				'title_after'  => '',
				'intro'        => __( 'Monitoring only works with a clear plan. Safeguard documents who is called, in what order, and when physical attendance is required.', 'site-blocks' ),
				'list'         => array(
					array(
						'title' => __( 'Keyholder Notification', 'site-blocks' ),
						'desc'  => __( 'Primary and secondary contacts called when alarms activate.', 'site-blocks' ),
					),
					array(
						'title' => __( 'Alarm Response / Patrol Attendance', 'site-blocks' ),
						'desc'  => sprintf(
							/* translators: 1: mobile patrols link */
							__( 'Licensed officers attend to verify and secure the site when your plan requires it. Links to %1$s and future Alarm Response services.', 'site-blocks' ),
							'<a href="' . esc_url( home_url( '/physical-security/mobile-patrols/' ) ) . '">' . esc_html__( 'Mobile Patrols', 'site-blocks' ) . '</a>'
						),
						'html'  => true,
					),
					array(
						'title' => __( 'Video Verification', 'site-blocks' ),
						'desc'  => __( 'Where CCTV is integrated, footage can be checked before escalating, reducing false callouts.', 'site-blocks' ),
					),
					array(
						'title' => __( 'Emergency Services', 'site-blocks' ),
						'desc'  => __( 'Escalation per your documented plan and applicable regulations.', 'site-blocks' ),
					),
				),
				'image'        => 'back-to-base-response-plan.webp',
				'alt'          => __( 'Monitoring centre operator managing an alarm response workflow', 'site-blocks' ),
				'reverse'      => true,
			),
			'compatible-systems' => array(
				'id'           => 'sg-btb-compatible-heading',
				'class'        => 'sg-back-to-base-compatible',
				'band'         => 'blue',
				'title_before' => __( 'Systems We ', 'site-blocks' ),
				'title_accent' => __( 'Monitor', 'site-blocks' ),
				'title_after'  => '',
				'list'         => array(
					array(
						'title' => __( 'Ajax Wireless Systems', 'site-blocks' ),
						'desc'  => __( 'Installed and supported by Safeguard; self-monitoring via app or professional back-to-base.', 'site-blocks' ),
					),
					array(
						'title' => __( 'Upgraded Wired Systems', 'site-blocks' ),
						'desc'  => __( 'Legacy panels upgraded so they can report when monitoring is selected for the job.', 'site-blocks' ),
					),
					array(
						'title' => __( 'Existing Panels', 'site-blocks' ),
						'desc'  => __( 'Many systems can be connected, we assess compatibility during site review.', 'site-blocks' ),
					),
				),
				'cross_links'  => 'compatible',
			),
		),
		'virtual-patrol' => array(
			'intro' => array(
				'id'           => 'sg-vp-intro-heading',
				'class'        => 'sg-virtual-patrol-intro',
				'title_before' => __( 'Unmonitored CCTV Only Helps ', 'site-blocks' ),
				'title_accent' => __( 'After', 'site-blocks' ),
				'title_after'  => __( ' the fact.', 'site-blocks' ),
				'paragraphs'   => array(
					__( 'Cameras record incidents, but a recording is often seen too late. Virtual patrol adds scheduled checks of your feeds, and extra checks when alarms trigger.', 'site-blocks' ),
					sprintf(
						/* translators: 1: monitoring hub link, 2: mobile patrols link */
						__( 'Part of Safeguard\'s %1$s, distinct from %2$s, where licensed officers attend in person.', 'site-blocks' ),
						'<a href="' . esc_url( $hub ) . '">' . esc_html__( 'monitoring services', 'site-blocks' ) . '</a>',
						'<a href="' . esc_url( home_url( '/physical-security/mobile-patrols/' ) ) . '">' . esc_html__( 'mobile patrols', 'site-blocks' ) . '</a>'
					),
				),
				'paragraphs_html' => true,
				'image'           => 'virtual-patrol-hero.webp',
				'alt'             => __( 'Virtual patrol operator reviewing CCTV feeds in a bright monitoring room', 'site-blocks' ),
				'reverse'         => false,
			),
		),
		'solar-cameras-monitoring' => array(
			'intro' => array(
				'id'           => 'sg-solar-intro-heading',
				'class'        => 'sg-solar-monitoring-intro',
				'title_before' => __( 'Temporary Sites Still Need ', 'site-blocks' ),
				'title_accent' => __( 'Permanent', 'site-blocks' ),
				'title_after'  => __( ' cover.', 'site-blocks' ),
				'paragraphs'   => array(
					__( 'Construction phases, rural blocks and off-grid properties cannot always wait for cabling and NBN. Solar cameras with 4G reporting and professional monitoring close that gap, with incident verification and response linked to your plan.', 'site-blocks' ),
				),
				'image'        => 'solar-monitoring-hero.webp',
				'alt'          => __( 'Solar security camera on a Sydney construction site', 'site-blocks' ),
				'reverse'      => false,
			),
			'whats-included' => array(
				'id'           => 'sg-solar-included-heading',
				'class'        => 'sg-solar-monitoring-included',
				'title_before' => __( 'What A Solar Monitoring Package Typically ', 'site-blocks' ),
				'title_accent' => __( 'Includes', 'site-blocks' ),
				'title_after'  => '',
				'intro'        => __( 'Every site differs, your quote lists exact hardware, install scope and monitoring plan. We do not publish package prices on the website.', 'site-blocks' ),
				'list'         => array(
					array( 'title' => __( 'Solar Camera Unit(S) With Battery Storage', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( '4G Connectivity For Live View And Alarm Reporting', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( 'Professional Installation And Commissioning', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( 'Back-To-Base Monitoring Integration', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( 'Optional Virtual Patrol Tours Over Camera Feeds', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( 'Optional Mobile Patrol Or Alarm Response Escalation', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( 'Relocation Or Expansion Support For Construction Phases', 'site-blocks' ), 'desc' => '' ),
				),
				'image'        => 'solar-monitoring-install.webp',
				'alt'          => __( 'Safeguard installing solar monitoring camera', 'site-blocks' ),
				'reverse'      => true,
			),
			'technical' => array(
				'id'           => 'sg-solar-technical-heading',
				'class'        => 'sg-solar-monitoring-technical',
				'band'         => 'blue',
				'title_before' => __( 'Built For Australian ', 'site-blocks' ),
				'title_accent' => __( 'Conditions', 'site-blocks' ),
				'title_after'  => '',
				'list'         => array(
					array( 'title' => __( 'Solar Panel Sized For Local Sun Hours And Camera Load', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( 'Battery Backup For Overnight And Cloudy Periods', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( 'Human/Vehicle Detection To Reduce False Alerts', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( 'PTZ Or Fixed Cameras Depending On Coverage Needs', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( '4G SIM Data Plan Managed As Part Of Service', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( 'Weather-Rated Housings For Outdoor Deployment', 'site-blocks' ), 'desc' => '' ),
				),
			),
			'monitoring-integration' => array(
				'id'           => 'sg-solar-integration-heading',
				'class'        => 'sg-solar-monitoring-integration',
				'title_before' => __( 'Monitored, Not Just ', 'site-blocks' ),
				'title_accent' => __( 'Recorded', 'site-blocks' ),
				'title_after'  => '',
				'list'         => array(
					array( 'title' => __( 'Alarm Events Can Be Reported To A Monitoring Service When Monitoring Is Selected For The Job', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( 'Live View Can Be Checked Where The Cameras Allow', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( 'Keyholders Notified; Patrol Dispatched Per Response Plan', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( 'Tour Logs For Construction Managers And Asset Owners', 'site-blocks' ), 'desc' => '' ),
					array( 'title' => __( 'One Vendor For Install, Monitoring And Physical Response, Not Three Vendors Pointing At Each Other.', 'site-blocks' ), 'desc' => '' ),
				),
				'image'        => 'solar-monitoring-night.webp',
				'alt'          => __( 'Night-time monitored perimeter on construction site', 'site-blocks' ),
				'reverse'      => false,
			),
		),
	);

	return $splits[ $page_key ][ $section ] ?? null;
}

/**
 * Numbered steps config for spoke how-it-works sections.
 *
 * @return array{eyebrow?: string, title_before: string, title_accent: string, title_after?: string, intro?: string, steps: array<int, array{title: string, desc: string}>}|null
 */
function site_blocks_monitoring_steps_config( string $page_key ): ?array {
	$configs = array(
		'back-to-base' => array(
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'How Back-To-Base Monitoring ', 'site-blocks' ),
			'title_accent'  => __( 'Works', 'site-blocks' ),
			'steps'         => array(
				array( 'title' => __( 'Alarm Activates', 'site-blocks' ), 'desc' => __( 'A sensor, door contact or panic button triggers your system.', 'site-blocks' ) ),
				array( 'title' => __( 'Signal Sent', 'site-blocks' ), 'desc' => __( 'Your communicator sends the alert over IP, 4G or dual path.', 'site-blocks' ) ),
				array( 'title' => __( 'Operator Assesses', 'site-blocks' ), 'desc' => __( 'Trained staff review the signal against your site profile and event type.', 'site-blocks' ) ),
				array( 'title' => __( 'Response Executed', 'site-blocks' ), 'desc' => __( 'Keyholders are called in order; patrol or alarm response attendance is arranged per your plan.', 'site-blocks' ) ),
				array( 'title' => __( 'Logged And Reported', 'site-blocks' ), 'desc' => __( 'Incidents are recorded for your records and future review.', 'site-blocks' ) ),
			),
		),
		'virtual-patrol' => array(
			'eyebrow'       => __( 'How it works', 'site-blocks' ),
			'title_before'  => __( 'How Virtual Patrol ', 'site-blocks' ),
			'title_accent'  => __( 'Works', 'site-blocks' ),
			'steps'         => array(
				array( 'title' => __( 'Schedule Agreed', 'site-blocks' ), 'desc' => __( 'Random and scheduled tour times based on your risk profile.', 'site-blocks' ) ),
				array( 'title' => __( 'Operator Tours Feeds', 'site-blocks' ), 'desc' => __( 'Live review of nominated cameras, perimeters, gates, yards, loading docks.', 'site-blocks' ) ),
				array( 'title' => __( 'Incidents Flagged', 'site-blocks' ), 'desc' => __( 'Suspicious activity logged with timestamps; keyholders notified per plan.', 'site-blocks' ) ),
				array( 'title' => __( 'Escalation', 'site-blocks' ), 'desc' => __( 'Alarm response or mobile patrol dispatched when your plan requires physical attendance.', 'site-blocks' ) ),
				array( 'title' => __( 'Report Delivered', 'site-blocks' ), 'desc' => __( 'Tour logs and incident notes for your records.', 'site-blocks' ) ),
			),
		),
	);

	return $configs[ $page_key ] ?? null;
}

/**
 * Communicators table (back-to-base).
 *
 * @return array{title: string, rows: array<int, array{path: string, best_for: string, summary: string, card_summary: string, icon: string}>, note: string}
 */
function site_blocks_monitoring_communicators_config(): array {
	return array(
		'title' => __( 'IP And 4G Monitoring Paths', 'site-blocks' ),
		'rows' => array(
			array(
				'path'         => __( 'IP monitoring', 'site-blocks' ),
				'best_for'     => __( 'Residential with stable internet', 'site-blocks' ),
				'summary'      => __( 'Alarm reports over your broadband connection. Cost-effective where connectivity is reliable.', 'site-blocks' ),
				'card_summary' => __( 'Best for residential with stable internet.', 'site-blocks' ),
				'icon'         => 'router',
			),
			array(
				'path'         => __( '4G monitoring', 'site-blocks' ),
				'best_for'     => __( 'Commercial sites, or where backup is required', 'site-blocks' ),
				'summary'      => __( 'Reports over the mobile network, keeps monitoring online if internet drops. Often required for commercial premises.', 'site-blocks' ),
				'card_summary' => __( 'Ideal for commercial sites, or where backup is required.', 'site-blocks' ),
				'icon'         => 'mobile',
			),
			array(
				'path'         => __( 'Dual path', 'site-blocks' ),
				'best_for'     => __( 'Higher reliability needs', 'site-blocks' ),
				'summary'      => __( 'Ethernet or Wiâ€‘Fi plus cellular backup, common on modern smart alarm systems.', 'site-blocks' ),
				'card_summary' => __( 'For sites with higher reliability needs.', 'site-blocks' ),
				'icon'         => 'shield',
			),
		),
		'note' => __( 'Commercial and shop premises typically require a cellular monitoring path. We confirm requirements during your assessment.', 'site-blocks' ),
	);
}

/**
 * Virtual patrol vs mobile patrol comparison table.
 *
 * @return array{title: string, headers: array<int, string>, rows: array<int, array<string, string>>, clarifier_html: string}
 */
function site_blocks_monitoring_compare_config(): array {
	return array(
		'title'   => __( 'Virtual Patrol Vs Mobile Patrol: Which Do You Need?', 'site-blocks' ),
		'headers' => array(
			'',
			__( 'Virtual patrol', 'site-blocks' ),
			__( 'Mobile Patrols', 'site-blocks' ),
		),
		'rows'    => array(
			array(
				'label' => __( 'Cover type', 'site-blocks' ),
				'virtual' => __( 'Remote checks on CCTV', 'site-blocks' ),
				'mobile'  => __( 'Physical, licensed officer on site', 'site-blocks' ),
			),
			array(
				'label' => __( 'Best for', 'site-blocks' ),
				'virtual' => __( 'Warehouses, yards, after-hours commercial', 'site-blocks' ),
				'mobile'  => __( 'Alarm response and visible deterrence', 'site-blocks' ),
			),
			array(
				'label' => __( 'Cost profile', 'site-blocks' ),
				'virtual' => __( 'Lower than 24/7 static guard; quoted per site', 'site-blocks' ),
				'mobile'  => __( 'Scheduled visits; quoted per site', 'site-blocks' ),
			),
			array(
				'label' => __( 'Response', 'site-blocks' ),
				'virtual' => __( 'Remote verification first; physical dispatch per plan', 'site-blocks' ),
				'mobile'  => __( 'officer attends in person', 'site-blocks' ),
			),
			array(
				'label' => __( 'Requires', 'site-blocks' ),
				'virtual' => __( 'Compatible CCTV/NVR and bandwidth', 'site-blocks' ),
				'mobile'  => __( 'Site access and patrol route', 'site-blocks' ),
			),
			array(
				'label' => __( 'Safeguard page', 'site-blocks' ),
				'virtual' => home_url( '/monitoring/virtual-patrol/' ),
				'mobile'  => home_url( '/physical-security/mobile-patrols/' ),
				'links'   => true,
			),
		),
		'clarifier_html' => sprintf(
			/* translators: %s: mobile patrols link */
			__( 'Need a guard on site? See %s. Need scheduled checks on the cameras? Virtual patrol covers that.', 'site-blocks' ),
			'<a href="' . esc_url( home_url( '/physical-security/mobile-patrols/' ) ) . '">' . esc_html__( 'Mobile Patrols', 'site-blocks' ) . '</a>'
		),
	);
}

/**
 * Virtual patrol features list.
 *
 * @return array<int, string>
 */
function site_blocks_monitoring_features_list(): array {
	return array(
		__( 'Scheduled and random virtual tours', 'site-blocks' ),
		__( 'Alarm-triggered camera verification', 'site-blocks' ),
		__( 'Timestamped tour and incident logs', 'site-blocks' ),
		__( 'Integration with back-to-base monitoring', 'site-blocks' ),
		__( 'Optional audio challenge where speakers are installed', 'site-blocks' ),
		__( 'Escalation to physical patrol or alarm response per plan', 'site-blocks' ),
	);
}

/**
 * Industry grid items per page key.
 *
 * @return array<int, array{title: string, desc: string}>
 */
function site_blocks_monitoring_industry_items( string $page_key ): array {
	$items = array(
		'hub' => array(
			array( 'title' => __( 'Residential', 'site-blocks' ), 'desc' => __( 'Home alarms monitored when you travel, sleep or cannot reach your phone in time.', 'site-blocks' ), 'icon' => 'fence-perimeter' ),
			array( 'title' => __( 'Retail & Commercial', 'site-blocks' ), 'desc' => __( 'After-hours alarm and camera cover for shops, offices and small warehouses.', 'site-blocks' ), 'icon' => 'warehouse-yard' ),
			array( 'title' => __( 'Warehouses & Logistics', 'site-blocks' ), 'desc' => __( 'Virtual patrol and back-to-base for large perimeters and limited staff overnight.', 'site-blocks' ), 'icon' => 'warehouse-yard' ),
			array( 'title' => __( 'Construction', 'site-blocks' ), 'desc' => __( 'Solar-monitored cameras for sites without power or fixed internet.', 'site-blocks' ), 'icon' => 'construction' ),
			array( 'title' => __( 'Strata', 'site-blocks' ), 'desc' => __( 'Common-area alarms and cameras with clear reporting for committees.', 'site-blocks' ), 'icon' => 'fence-perimeter' ),
			array( 'title' => __( 'Rural & Remote', 'site-blocks' ), 'desc' => __( '4G solar monitoring where traditional cabling is not practical.', 'site-blocks' ), 'icon' => 'farm-rural' ),
		),
		'virtual-patrol' => array(
			array( 'title' => __( 'Warehouses & Logistics', 'site-blocks' ), 'desc' => __( 'Perimeter and loading bay checks overnight.', 'site-blocks' ), 'icon' => 'warehouse' ),
			array( 'title' => __( 'Construction', 'site-blocks' ), 'desc' => __( 'after-hours monitoring before permanent power and staff.', 'site-blocks' ), 'icon' => 'hard-hat' ),
			array( 'title' => __( 'Car Yards & Equipment Storage', 'site-blocks' ), 'desc' => __( 'Wide open sites with multiple entry points.', 'site-blocks' ), 'icon' => 'car' ),
			array( 'title' => __( 'Strata Common Areas', 'site-blocks' ), 'desc' => __( 'Car parks, lobbies and plant rooms.', 'site-blocks' ), 'icon' => 'building-2' ),
			array( 'title' => __( 'Retail Back-Of-House', 'site-blocks' ), 'desc' => __( 'Stock areas and delivery zones.', 'site-blocks' ), 'icon' => 'store' ),
		),
		'solar-cameras-monitoring' => array(
			array( 'title' => __( 'Construction Sites', 'site-blocks' ), 'desc' => __( 'Temporary cover before power and fixed internet are live.', 'site-blocks' ), 'icon' => 'construction' ),
			array( 'title' => __( 'Rural Properties', 'site-blocks' ), 'desc' => __( 'Remote blocks and sheds without trenching for cable.', 'site-blocks' ), 'icon' => 'farm-rural' ),
			array( 'title' => __( 'Warehouses & Yards', 'site-blocks' ), 'desc' => __( 'Perimeter cameras where mains power is limited.', 'site-blocks' ), 'icon' => 'warehouse-yard' ),
			array( 'title' => __( 'Vacant Land', 'site-blocks' ), 'desc' => __( 'Deterrence and verified alerts on undeveloped sites.', 'site-blocks' ), 'icon' => 'fence-perimeter' ),
			array( 'title' => __( 'Equipment Storage', 'site-blocks' ), 'desc' => __( 'Plant, tools and materials left on site overnight.', 'site-blocks' ), 'icon' => 'warehouse-yard' ),
		),
	);

	return $items[ $page_key ] ?? array();
}

/**
 * Industry grid heading per page key.
 *
 * @return array{before: string, accent: string}|null
 */
function site_blocks_monitoring_industry_heading( string $page_key ): ?array {
	$headings = array(
		'hub'                      => array(
			'before' => __( 'Monitoring for every kind of ', 'site-blocks' ),
			'accent' => __( 'site', 'site-blocks' ),
		),
		'virtual-patrol'           => array(
			'before' => __( 'Built for after-hours ', 'site-blocks' ),
			'accent' => __( 'sites', 'site-blocks' ),
		),
		'solar-cameras-monitoring' => array(
			'before' => __( 'Solar monitoring for remote ', 'site-blocks' ),
			'accent' => __( 'sites', 'site-blocks' ),
		),
	);

	return $headings[ $page_key ] ?? null;
}

/**
 * Build scenario grid config for monitoring industry sections.
 *
 * @return array<string, mixed>|null
 */
function site_blocks_monitoring_industry_scenario_config( string $page_key ): ?array {
	$heading = site_blocks_monitoring_industry_heading( $page_key );
	$items   = site_blocks_monitoring_industry_items( $page_key );

	if ( null === $heading || $items === array() ) {
		return null;
	}

	return array(
		'layout'          => 'default',
		'eyebrow'         => __( 'Industries', 'site-blocks' ),
		'title_before'    => $heading['before'],
		'title_accent'    => $heading['accent'],
		'use_brand_icons' => 'virtual-patrol' !== $page_key,
		'use_mdi_icons'=> 'virtual-patrol' === $page_key,
		'cards'           => $items,
	);
}

/**
 * Solar package inclusions section.
 *
 * @return array<string, mixed>|null
 */
function site_blocks_monitoring_package_inclusions_config( string $page_key ): ?array {
	if ( 'solar-cameras-monitoring' !== $page_key ) {
		return null;
	}

	return array(
		'eyebrow'      => __( 'Package inclusions', 'site-blocks' ),
		'title_before' => __( 'What Makes It Work In ', 'site-blocks' ),
		'title_accent' => __( 'Australian Conditions', 'site-blocks' ),
		'intro'        => __( 'Key package elements are selected to keep remote monitoring reliable across sun, weather and changing site conditions.', 'site-blocks' ),
		'image'        => 'solar-package-banner.png',
		'alt'          => __( 'Solar-powered CCTV camera installed in remote Australian conditions', 'site-blocks' ),
		'cards'        => array(
			array(
				'eyebrow' => __( 'Power', 'site-blocks' ),
				'icon'    => 'power',
				'title'   => __( 'Solar Panel Sized For Local Sun Hours And Camera Load', 'site-blocks' ),
				'desc'    => __( 'Right-sized solar for consistent power from daylight through to peak usage.', 'site-blocks' ),
			),
			array(
				'eyebrow' => __( 'Backup', 'site-blocks' ),
				'icon'    => 'backup',
				'title'   => __( 'Battery Backup For Overnight And Cloudy Periods', 'site-blocks' ),
				'desc'    => __( 'Integrated battery storage keeps your system online when the sun is not.', 'site-blocks' ),
			),
			array(
				'eyebrow' => __( 'Detection', 'site-blocks' ),
				'icon'    => 'detection',
				'title'   => __( 'Human/Vehicle Detection To Reduce False Alerts', 'site-blocks' ),
				'desc'    => __( 'Smart detection focuses on people and vehicles, not trees or shadows.', 'site-blocks' ),
			),
			array(
				'eyebrow' => __( 'Camera type', 'site-blocks' ),
				'icon'    => 'camera',
				'title'   => __( 'PTZ Or Fixed Cameras Depending On Coverage Needs', 'site-blocks' ),
				'desc'    => __( 'Choose the right camera type and placement for your site and risks.', 'site-blocks' ),
			),
			array(
				'eyebrow' => __( 'Connectivity', 'site-blocks' ),
				'icon'    => 'connectivity',
				'title'   => __( '4G SIM Data Plan Managed As Part Of Service', 'site-blocks' ),
				'desc'    => __( 'Secure 4G connectivity with data managed and monitored by us.', 'site-blocks' ),
			),
			array(
				'eyebrow' => __( 'Outdoor hardware', 'site-blocks' ),
				'icon'    => 'weather',
				'title'   => __( 'Weather-Rated Housings For Outdoor Deployment', 'site-blocks' ),
				'desc'    => __( 'Rugged enclosures and mounts built to withstand harsh Australian conditions.', 'site-blocks' ),
			),
		),
		'checks'       => array(
			__( 'Installed to site requirements', 'site-blocks' ),
			__( 'Commissioned and tested', 'site-blocks' ),
			__( 'monitored and supported', 'site-blocks' ),
		),
	);
}

/**
 * Solar use-cases scenario grid (40/60 split with photo).
 *
 * @return array<string, mixed>
 */
function site_blocks_monitoring_use_cases_config(): array {
	return array(
		'layout'          => 'split',
		'eyebrow'         => __( 'Use cases', 'site-blocks' ),
		'title_before'    => __( 'Where Solar Monitoring ', 'site-blocks' ),
		'title_accent'    => __( 'Fits', 'site-blocks' ),
		'use_brand_icons' => true,
		'photo'           => array(
			'file' => 'solar-monitoring-rural.webp',
			'alt'  => __( 'Solar CCTV monitoring on a rural NSW property', 'site-blocks' ),
			'dir'  => 'monitoring',
		),
		'cards'           => array(
			array(
				'title' => __( 'Construction', 'site-blocks' ),
				'desc'  => __( 'Monitor plant, materials and access points from slab to handover. Move or expand as the site evolves.', 'site-blocks' ),
				'icon'  => 'construction',
			),
			array(
				'title' => __( 'Farms & Rural', 'site-blocks' ),
				'desc'  => __( 'Perimeter and shed cover without trenching cable across paddocks.', 'site-blocks' ),
				'icon'  => 'farm-rural',
			),
			array(
				'title' => __( 'Vacant Land', 'site-blocks' ),
				'desc'  => __( 'Deter trespass and dumping on undeveloped blocks.', 'site-blocks' ),
				'icon'  => 'fence-perimeter',
			),
			array(
				'title' => __( 'Remote Commercial', 'site-blocks' ),
				'desc'  => __( 'Yards and depots where mains power exists but camera cabling does not.', 'site-blocks' ),
				'icon'  => 'warehouse-yard',
			),
		),
	);
}

/**
 * Virtual patrol requirements list.
 *
 * @return array{title: string, items: array<int, string>}
 */
function site_blocks_monitoring_requirements_config(): array {
	return array(
		'title' => __( 'What You Need For Virtual Patrol', 'site-blocks' ),
		'items' => array(
			__( 'Compatible CCTV system or NVR with remote access', 'site-blocks' ),
			__( 'Adequate internet or 4G connectivity at the site', 'site-blocks' ),
			__( 'Camera coverage of priority areas (perimeter, entries, high-value zones)', 'site-blocks' ),
			__( 'Documented response plan linked to monitoring', 'site-blocks' ),
			__( 'Optional: IP speakers for operator audio challenge', 'site-blocks' ),
		),
	);
}

/**
 * Back-to-base quote section (no prices).
 *
 * @return array{title: string, body: string, links: array<int, array{label: string, url: string}>}
 */
function site_blocks_monitoring_quote_config(): array {
	return array(
		'title' => __( 'Request A Quote For Your Property', 'site-blocks' ),
		'body'  => __( 'Monitoring cost depends on your communicator path, response plan and property type. Safeguard does not publish monitoring prices on this website, every site is different. Request a quote and the team will recommend the right back-to-base setup.', 'site-blocks' ),
		'links' => array(
			array(
				'label' => __( 'Request a quote', 'site-blocks' ),
				'url'   => home_url( '/contact/' ),
				'primary' => true,
			),
			array(
				'label' => __( 'Speak to Us', 'site-blocks' ),
				'url'   => 'tel:1300225276',
			),
		),
	);
}

/**
 * Final CTA band per page key.
 *
 * @return array{before: string, accent: string, after?: string, sub: string, secondary_label?: string, secondary_url?: string}|null
 */
function site_blocks_monitoring_cta_config( string $page_key ): ?array {
	$ctas = array(
		'hub' => array(
			'before'          => __( 'Get monitoring that connects to ', 'site-blocks' ),
			'accent'          => __( 'real response', 'site-blocks' ),
			'after'           => __( '.', 'site-blocks' ),
			'sub'             => __( 'Tell us about your property and we\'ll recommend back-to-base, virtual patrol or solar monitoring, quoted for your site, with no obligation.', 'site-blocks' ),
			'secondary_label' => __( 'Speak to Us', 'site-blocks' ),
			'secondary_url'   => home_url( '/contact/' ),
		),
		'back-to-base' => array(
			'before' => __( 'Connect your alarm to professional ', 'site-blocks' ),
			'accent' => __( 'monitoring', 'site-blocks' ),
			'after'  => __( '.', 'site-blocks' ),
			'sub'    => __( 'Request a quote for back-to-base monitoring, tailored to your property, communicator path and response plan.', 'site-blocks' ),
		),
		'virtual-patrol' => array(
			'before' => __( 'Add live eyes to your ', 'site-blocks' ),
			'accent' => __( 'cameras', 'site-blocks' ),
			'after'  => __( '.', 'site-blocks' ),
			'sub'    => __( 'Tell us about your site and camera coverage, we\'ll recommend virtual patrol with a tailored quote.', 'site-blocks' ),
		),
		'solar-cameras-monitoring' => array(
			'before'          => __( 'monitor your site before power and cabling are ', 'site-blocks' ),
			'accent'          => __( 'ready', 'site-blocks' ),
			'after'           => __( '.', 'site-blocks' ),
			'sub'             => __( 'Request a site quote for solar cameras with professional monitoring, hardware, install and response plan combined.', 'site-blocks' ),
			'secondary_label' => __( 'Speak to Us', 'site-blocks' ),
			'secondary_url'   => home_url( '/contact/' ),
		),
	);

	return $ctas[ $page_key ] ?? null;
}
