<?php
/**
 * Headings do not end in a full stop.
 *
 * House style: a heading (h1 to h6) never ends with a full stop. Templates and
 * content across the site were written with them, so this removes a single
 * trailing full stop from each heading as the page is sent, including one that
 * sits alone in a closing inline tag. Ellipses ("...") and headings ending in a
 * letter or other mark are left alone.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Strip one trailing full stop from every heading in an HTML document.
 *
 * @param string $html Buffered page output.
 */
function site_blocks_strip_heading_full_stops( string $html ): string {
	// Only full documents; leave fragments and non-HTML responses untouched.
	if ( false === stripos( $html, '</html>' ) || false === stripos( $html, '</h' ) ) {
		return $html;
	}

	$stripped = preg_replace_callback(
		'~(<h([1-6])\b[^>]*>)(.*?)(</h\2>)~is',
		static function ( array $m ): string {
			$inner = $m[3];
			// Optional space, one full stop that is not part of "..", then only
			// closing inline tags and whitespace until the end of the heading.
			$inner = preg_replace(
				'~[ \t]*(?<!\.)\.((?:\s*</(?:span|em|strong|b|i|mark|a)>)*\s*)$~i',
				'$1',
				$inner
			);
			return $m[1] . ( is_string( $inner ) ? $inner : $m[3] ) . $m[4];
		},
		$html
	);

	return is_string( $stripped ) ? $stripped : $html;
}

/**
 * Buffer front-end HTML pages so headings can be tidied on the way out.
 */
function site_blocks_start_heading_full_stop_filter(): void {
	if ( is_admin() || is_feed() || is_embed() || is_robots() || is_trackback() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}
	ob_start( 'site_blocks_strip_heading_full_stops' );
}
add_action( 'template_redirect', 'site_blocks_start_heading_full_stop_filter', 1 );
