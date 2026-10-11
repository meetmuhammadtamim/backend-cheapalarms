<?php
/**
 * Akuvox page detection, SEO meta, and JSON-LD (assets via safeguard-silo-setup.php).
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the Akuvox landing page is being viewed.
 */
function site_blocks_is_akuvox_page(): bool {
	return is_page( 'akuvox' );
}

/**
 * Page title for Akuvox.
 */
function site_blocks_akuvox_document_title( string $title ): string {
	if ( ! site_blocks_is_akuvox_page() ) {
		return $title;
	}

	return __( 'Akuvox Intercom Sydney | Video Intercom & 2-Wire Upgrade | Safeguard', 'site-blocks' );
}
add_filter( 'pre_get_document_title', 'site_blocks_akuvox_document_title', 20 );

/**
 * Meta description in head.
 */
function site_blocks_akuvox_meta_description(): void {
	if ( ! site_blocks_is_akuvox_page() ) {
		return;
	}

	printf(
		'<meta name="description" content="%s" />' . "\n",
		esc_attr__(
			'Akuvox video intercoms. Answer the door from your phone, upgrade old 2-wire systems, or spec a new IP entry. Request a quote.',
			'site-blocks'
		)
	);
}
add_action( 'wp_head', 'site_blocks_akuvox_meta_description', 2 );

/**
 * JSON-LD structured data for the Akuvox landing page.
 */
function site_blocks_akuvox_schema(): void {
	if ( ! site_blocks_is_akuvox_page() ) {
		return;
	}

	require_once SITE_BLOCKS_DIR . 'inc/akuvox-config.php';

	$site_url = home_url( '/' );
	$page_url = home_url( '/akuvox/' );
	$phone    = '1300225276';

	$schema = array(
		array(
			'@context'   => 'https://schema.org',
			'@type'      => 'LocalBusiness',
			'@id'        => $site_url . '#business',
			'name'       => 'Safeguard Security Services',
			'url'        => $page_url,
			'telephone'  => $phone,
			'priceRange' => '$$',
			'address'    => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => '2/2 Stennett Road',
				'addressLocality' => 'Ingleburn',
				'addressRegion'   => 'NSW',
				'postalCode'      => '2565',
				'addressCountry'  => 'AU',
			),
		),
		array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Service',
			'serviceType' => 'Akuvox video intercom installation',
			'provider'    => array( '@id' => $site_url . '#business' ),
			'url'         => $page_url,
			'description' => 'Design and installation of Akuvox SIP video intercoms for homes, apartments and commercial entries, including 2-wire upgrades.',
		),
		array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array(
					'@type'    => 'ListItem',
					'position' => 1,
					'name'     => 'Home',
					'item'     => $site_url,
				),
				array(
					'@type'    => 'ListItem',
					'position' => 2,
					'name'     => 'Intercom Systems',
					'item'     => home_url( '/intercom-systems/' ),
				),
				array(
					'@type'    => 'ListItem',
					'position' => 3,
					'name'     => 'Akuvox',
					'item'     => $page_url,
				),
			),
		),
	);

	$faq_entities = array();
	foreach ( site_blocks_akuvox_faq_items() as $item ) {
		$faq_entities[] = array(
			'@type'          => 'Question',
			'name'           => $item['q'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $item['a'],
			),
		);
	}

	$schema[] = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $faq_entities,
	);

	echo '<script type="application/ld+json">';
	echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	echo '</script>' . "\n";
}
add_action( 'wp_head', 'site_blocks_akuvox_schema', 5 );
