<?php
/**
 * Paradox M25 landing page block pattern.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'title'       => __( 'Paradox M25 Page', 'site-blocks' ),
	'description' => __( 'SEO landing page for Paradox M25 alarm systems installed by Safeguard.', 'site-blocks' ),
	'categories'  => array( 'site-pages' ),
	'keywords'    => array( 'paradox', 'm25', 'alarm', 'seo', 'safeguard' ),
	'content'     => '<div class="sg-page alignfull"><!-- wp:site/safeguard-header /--><main id="main" class="sg-main"><!-- wp:site/paradox-m25-hero /--><!-- wp:site/paradox-m25-section {"section":"difference"} /--><!-- wp:site/paradox-m25-section {"section":"portal"} /--><!-- wp:site/paradox-m25-section {"section":"products"} /--><!-- wp:site/paradox-m25-section {"section":"monitoring"} /--><!-- wp:site/paradox-m25-section {"section":"property-fit"} /--><!-- wp:site/paradox-m25-section {"section":"install"} /--><!-- wp:site/paradox-m25-section {"section":"compare"} /--><!-- wp:site/paradox-m25-section {"section":"faq"} /--><!-- wp:site/paradox-m25-section {"section":"cta"} /--></main><!-- wp:site/safeguard-footer /--></div>',
);
