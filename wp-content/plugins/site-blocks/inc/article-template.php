<?php
/**
 * Reading layout for blog posts and case studies.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param string $template Template path.
 */
function site_blocks_article_template( string $template ): string {
	if ( is_singular( 'post' ) ) {
		$file = SITE_BLOCKS_DIR . 'templates/single-post.php';
		if ( file_exists( $file ) ) {
			return $file;
		}
	}

	if ( is_singular( 'case_study' ) ) {
		$file = SITE_BLOCKS_DIR . 'templates/single-case-study.php';
		if ( file_exists( $file ) ) {
			return $file;
		}
	}

	if ( is_post_type_archive( 'case_study' ) || is_tax( 'case_study_hub' ) ) {
		$file = SITE_BLOCKS_DIR . 'templates/archive-case_study.php';
		if ( file_exists( $file ) ) {
			return $file;
		}
	}

	return $template;
}
add_filter( 'template_include', 'site_blocks_article_template', 99 );

/**
 * @param array<string, string> $layout Kadence layout.
 * @return array<string, string>
 */
function site_blocks_article_kadence_layout( array $layout ): array {
	if ( is_singular( array( 'post', 'case_study' ) ) || is_post_type_archive( 'case_study' ) || is_tax( 'case_study_hub' ) ) {
		$layout['layout']  = 'fullwidth';
		$layout['boxed']   = 'unboxed';
		$layout['title']   = 'hide';
		$layout['sidebar'] = 'disable';
		$layout['header']  = 'disable';
		$layout['footer']  = 'disable';
	}

	return $layout;
}
add_filter( 'kadence_post_layout', 'site_blocks_article_kadence_layout' );

/**
 * Add stable ids to h2 headings and return the list for the contents nav.
 *
 * @param string $html Post HTML.
 * @return array<int, array{id:string,text:string}>
 */
function site_blocks_article_collect_headings( string &$html ): array {
	$items   = array();
	$updated = preg_replace_callback(
		'/<h2([^>]*)>(.*?)<\/h2>/is',
		static function ( array $match ) use ( &$items ): string {
			$text = trim( wp_strip_all_tags( $match[2] ) );
			$id   = sanitize_title( $text );
			if ( '' === $id ) {
				$id = 'section-' . ( count( $items ) + 1 );
			}
			$base = $id;
			$n    = 2;
			$used = array_column( $items, 'id' );
			while ( in_array( $id, $used, true ) ) {
				$id = $base . '-' . $n;
				++$n;
			}
			$items[] = array(
				'id'   => $id,
				'text' => $text,
			);
			$attrs = preg_replace( '/\s+id=(["\']).*?\1/i', '', $match[1] );
			$attrs = is_string( $attrs ) ? $attrs : $match[1];

			return '<h2 id="' . esc_attr( $id ) . '"' . $attrs . '>' . $match[2] . '</h2>';
		},
		$html
	);

	if ( is_string( $updated ) ) {
		$html = $updated;
	}

	return $items;
}

/**
 * @param array<int, array{id:string,text:string}> $headings Headings.
 */
function site_blocks_article_toc_list( array $headings ): void {
	echo '<ol role="list">';
	foreach ( $headings as $heading ) {
		echo '<li><a href="#' . esc_attr( $heading['id'] ) . '">' . esc_html( $heading['text'] ) . '</a></li>';
	}
	echo '</ol>';
}

/**
 * @return array<int, WP_Post>
 */
function site_blocks_article_related( int $id, string $type ): array {
	$posts = get_posts(
		array(
			'post_type'      => $type,
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'post__not_in'   => array( $id ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	return is_array( $posts ) ? $posts : array();
}

/**
 * Drop a mid-article banner in front of the second heading.
 */
function site_blocks_blog_insert_banner( string $html, string $banner ): string {
	$count  = 0;
	$placed = false;
	$updated = preg_replace_callback(
		'/<h2\b/i',
		static function ( array $match ) use ( &$count, &$placed, $banner ): string {
			++$count;
			if ( 2 === $count ) {
				$placed = true;
				return $banner . $match[0];
			}

			return $match[0];
		},
		$html
	);

	if ( ! is_string( $updated ) ) {
		return $html;
	}

	if ( ! $placed ) {
		return $updated . $banner;
	}

	return $updated;
}

/**
 * Reading time in minutes.
 */
function site_blocks_article_minutes( int $post_id ): int {
	$words = str_word_count( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ) );

	return max( 1, (int) round( $words / 220 ) );
}

/**
 * Styles and the scroll progress script.
 */
function site_blocks_article_assets(): void {
	if ( ! is_singular( array( 'post', 'case_study' ) ) && ! is_post_type_archive( 'case_study' ) && ! is_tax( 'case_study_hub' ) ) {
		return;
	}

	site_blocks_enqueue_safeguard_fonts( 'safeguard-article-fonts' );
	site_blocks_enqueue_safeguard_style( 'safeguard-home', 'safeguard-home.css', 'safeguard-article-fonts' );
	site_blocks_enqueue_safeguard_home_script();

	$sheet = is_singular( 'post' ) ? 'blog-post.css' : 'case-study.css';
	$ver   = SITE_BLOCKS_VERSION;
	$path  = SITE_BLOCKS_DIR . 'assets/css/' . $sheet;
	if ( file_exists( $path ) ) {
		$ver = (string) filemtime( $path );
	}
	wp_enqueue_style(
		'safeguard-article',
		SITE_BLOCKS_URL . 'assets/css/' . $sheet,
		array( 'safeguard-home' ),
		$ver
	);

	if ( is_singular( array( 'post', 'case_study' ) ) ) {
		wp_enqueue_script(
			'safeguard-article',
			SITE_BLOCKS_URL . 'assets/js/article-progress.js',
			array(),
			SITE_BLOCKS_VERSION,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'site_blocks_article_assets', 30 );

/**
 * Flush editorial URLs once per plugin version, after the rules are registered.
 */
function site_blocks_flush_editorial_rewrites(): void {
	if ( get_option( 'site_blocks_editorial_rewrite' ) === SITE_BLOCKS_VERSION ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'site_blocks_editorial_rewrite', SITE_BLOCKS_VERSION );
}
add_action( 'init', 'site_blocks_flush_editorial_rewrites', 99 );
