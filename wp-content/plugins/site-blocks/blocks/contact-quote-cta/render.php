<?php
/**
 * Contact page quote CTA.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

site_blocks_render_quote_cta(
	array(
		'heading_id'    => 'sg-contact-cta-heading',
		'before'        => __( 'Ready for a quote? Send a ', 'site-blocks' ),
		'accent'        => __( 'message', 'site-blocks' ),
		'sub'           => __( 'Tell us the site and what you need. A person on the team checks the scope before work begins.', 'site-blocks' ),
		'section_class' => 'sg-contact-cta',
	)
);
