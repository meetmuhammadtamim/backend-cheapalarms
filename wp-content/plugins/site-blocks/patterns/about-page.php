<?php
/**
 * About page block pattern.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'title'       => __( 'About Page', 'site-blocks' ),
	'description' => __( 'About Safeguard Security Services.', 'site-blocks' ),
	'categories'  => array( 'site-pages' ),
	'keywords'    => array( 'about', 'safeguard' ),
	'content'     => '<!-- wp:site/about-page /-->',
);
