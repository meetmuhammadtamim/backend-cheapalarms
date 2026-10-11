<?php

/**

 * Contact page — detection and Safeguard assets.

 *

 * @package Site_Blocks

 */



declare( strict_types=1 );



if ( ! defined( 'ABSPATH' ) ) {

	exit;

}



/**

 * Whether the Contact page is being viewed.

 */

function site_blocks_is_contact_page(): bool {

	return is_page( 'contact' );

}



/**

 * Enqueue Safeguard styles for the Contact page.

 */

function site_blocks_enqueue_contact_page_assets(): void {

	if ( ! site_blocks_is_contact_page() ) {

		return;

	}



	site_blocks_enqueue_safeguard_fonts( 'safeguard-contact-fonts' );

	$dep = site_blocks_enqueue_safeguard_style( 'safeguard-home', 'safeguard-home.css', 'safeguard-contact-fonts' );

	$dep = site_blocks_enqueue_safeguard_style( 'safeguard-hero-variants', 'safeguard-hero-variants.css', $dep );

	$contact_css = SITE_BLOCKS_DIR . 'assets/css/safeguard-contact.css';
	wp_enqueue_style(
		'safeguard-contact',
		SITE_BLOCKS_URL . 'assets/css/safeguard-contact.css',
		array( $dep ),
		file_exists( $contact_css ) ? (string) filemtime( $contact_css ) : SITE_BLOCKS_VERSION
	);

	site_blocks_enqueue_safeguard_home_script();

}

add_action( 'wp_enqueue_scripts', 'site_blocks_enqueue_contact_page_assets', 30 );



/**

 * Body classes for Contact page shared blocks.

 *

 * @param string[] $classes Body classes.

 * @return string[]

 */

function site_blocks_contact_page_body_class( array $classes ): array {

	if ( site_blocks_is_contact_page() ) {

		$classes[] = 'safeguard-homepage';

		$classes[] = 'sg-contact-page';

	}



	return $classes;

}

add_filter( 'body_class', 'site_blocks_contact_page_body_class' );


