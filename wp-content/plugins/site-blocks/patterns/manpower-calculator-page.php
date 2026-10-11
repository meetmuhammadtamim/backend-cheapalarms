<?php
/**
 * ManPower calculator page pattern — calculator only (site chrome + embed).
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'title'       => __( 'ManPower Calculator Page', 'site-blocks' ),
	'description' => __( 'Calculator-only security guard quote page (header, embed, footer).', 'site-blocks' ),
	'categories'  => array( 'site-pages' ),
	'keywords'    => array( 'manpower', 'guard', 'calculator', 'quote' ),
	'content'     => '<div class="sg-page sg-page--manpower-calculator alignfull"><!-- wp:site/safeguard-header /--><main id="main" class="sg-main sg-main--manpower-calculator"><!-- wp:site/manpower-calculator-embed /--></main><!-- wp:site/safeguard-footer /--></div>',
);
