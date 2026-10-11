<?php
/**
 * Akuvox landing page block pattern.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'title'       => __( 'Akuvox Page', 'site-blocks' ),
	'description' => __( 'SEO landing page for Akuvox video intercoms installed by Safeguard.', 'site-blocks' ),
	'categories'  => array( 'site-pages' ),
	'keywords'    => array( 'akuvox', 'intercom', 'video', 'seo', 'safeguard' ),
	'content'     => '<div class="sg-page alignfull"><!-- wp:site/safeguard-header /--><main id="main" class="sg-main"><!-- wp:site/akuvox-hero /--><!-- wp:site/akuvox-section {"section":"difference"} /--><!-- wp:site/akuvox-section {"section":"portal"} /--><!-- wp:site/akuvox-section {"section":"products"} /--><!-- wp:site/akuvox-section {"section":"property-fit"} /--><!-- wp:site/akuvox-section {"section":"install"} /--><!-- wp:site/akuvox-section {"section":"compare"} /--><!-- wp:site/akuvox-section {"section":"faq"} /--><!-- wp:site/akuvox-section {"section":"cta"} /--></main><!-- wp:site/safeguard-footer /--></div>',
);
