<?php
/**
 * Public URLs for the blog and case studies.
 *
 * The addresses visitors see are fixed here so links never fall back to the
 * query-string forms (?post_type=case_study, ?post_type=post):
 *
 *   /case-studies/   the case studies archive
 *   /blogs/          the blog index (/blog/ redirects here)
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public URL of the case studies archive.
 */
function site_blocks_case_studies_url(): string {
	return home_url( '/case-studies/' );
}

/**
 * Public URL of the blog index.
 */
function site_blocks_blog_url(): string {
	return home_url( '/blogs/' );
}

/**
 * Any code asking WordPress for the case studies archive gets the public URL.
 *
 * @param string $link      Archive link.
 * @param string $post_type Post type.
 */
function site_blocks_filter_case_study_archive_link( string $link, string $post_type ): string {
	if ( 'case_study' === $post_type ) {
		return site_blocks_case_studies_url();
	}

	return $link;
}
add_filter( 'post_type_archive_link', 'site_blocks_filter_case_study_archive_link', 10, 2 );

/**
 * Register the blog index route.
 */
function site_blocks_blog_index_rewrites(): void {
	add_rewrite_rule( '^blogs/page/([0-9]{1,})/?$', 'index.php?sg_blog=1&paged=$matches[1]', 'top' );
	add_rewrite_rule( '^blogs/?$', 'index.php?sg_blog=1', 'top' );
	add_rewrite_rule( '^blog/?$', 'index.php?sg_blog=redirect', 'top' );
}
add_action( 'init', 'site_blocks_blog_index_rewrites', 12 );

/**
 * @param array<int, string> $vars Public query vars.
 * @return array<int, string>
 */
function site_blocks_blog_query_vars( array $vars ): array {
	$vars[] = 'sg_blog';

	return $vars;
}
add_filter( 'query_vars', 'site_blocks_blog_query_vars' );

/**
 * Whether the current request is the blog index.
 */
function site_blocks_is_blog_index(): bool {
	return '1' === (string) get_query_var( 'sg_blog' );
}

/**
 * /blog/ goes to /blogs/.
 */
function site_blocks_blog_redirect(): void {
	if ( 'redirect' === (string) get_query_var( 'sg_blog' ) ) {
		wp_safe_redirect( site_blocks_blog_url(), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'site_blocks_blog_redirect', 1 );

/**
 * The blog index lists nine posts a page.
 *
 * @param WP_Query $query Query.
 */
function site_blocks_blog_index_query( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( '1' === (string) $query->get( 'sg_blog' ) ) {
		$query->set( 'post_type', 'post' );
		$query->set( 'posts_per_page', 9 );
		$query->set( 'ignore_sticky_posts', true );
	}
}
add_action( 'pre_get_posts', 'site_blocks_blog_index_query' );

/**
 * @param string $template Template path.
 */
function site_blocks_blog_index_template( string $template ): string {
	if ( site_blocks_is_blog_index() ) {
		$file = SITE_BLOCKS_DIR . 'templates/archive-blog.php';
		if ( file_exists( $file ) ) {
			return $file;
		}
	}

	return $template;
}
add_filter( 'template_include', 'site_blocks_blog_index_template', 99 );

/**
 * Full-width layout, no theme chrome, as for the other editorial pages.
 *
 * @param array<string, string> $layout Kadence layout.
 * @return array<string, string>
 */
function site_blocks_blog_index_layout( array $layout ): array {
	if ( site_blocks_is_blog_index() ) {
		$layout['layout']  = 'fullwidth';
		$layout['boxed']   = 'unboxed';
		$layout['title']   = 'hide';
		$layout['sidebar'] = 'disable';
		$layout['header']  = 'disable';
		$layout['footer']  = 'disable';
	}

	return $layout;
}
add_filter( 'kadence_post_layout', 'site_blocks_blog_index_layout' );

/**
 * Styles for the blog index page (the homepage enqueues its own).
 */
function site_blocks_blog_index_assets(): void {
	if ( ! site_blocks_is_blog_index() ) {
		return;
	}

	site_blocks_enqueue_safeguard_fonts( 'safeguard-blog-fonts' );
	site_blocks_enqueue_safeguard_style( 'safeguard-home', 'safeguard-home.css', 'safeguard-blog-fonts' );
	site_blocks_enqueue_safeguard_home_script();
	wp_enqueue_style(
		'safeguard-blog',
		SITE_BLOCKS_URL . 'assets/css/safeguard-blog.css',
		array( 'safeguard-home' ),
		SITE_BLOCKS_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'site_blocks_blog_index_assets', 30 );

/**
 * Pages of the blog index are not "home" for body-class purposes.
 *
 * @param array<int, string> $classes Body classes.
 * @return array<int, string>
 */
function site_blocks_blog_index_body_class( array $classes ): array {
	if ( site_blocks_is_blog_index() ) {
		$classes[] = 'sg-blog-index';
	}

	return $classes;
}
add_filter( 'body_class', 'site_blocks_blog_index_body_class' );
