<?php
/**
 * Case studies. Hubs are taxonomy terms, not extra post types.
 *
 * URLs:
 *   /case-studies/
 *   /case-studies/enterprise/
 *   /case-studies/residential/
 *   /case-studies/{hub}/{study}/
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the case study post type and the enterprise / residential hubs.
 */
function site_blocks_register_case_study_cpt(): void {
	register_post_type(
		'case_study',
		array(
			'labels'              => array(
				'name'               => __( 'Case Studies', 'site-blocks' ),
				'singular_name'      => __( 'Case Study', 'site-blocks' ),
				'add_new_item'       => __( 'Add Case Study', 'site-blocks' ),
				'edit_item'          => __( 'Edit Case Study', 'site-blocks' ),
				'view_item'          => __( 'View Case Study', 'site-blocks' ),
				'search_items'       => __( 'Search Case Studies', 'site-blocks' ),
				'not_found'          => __( 'No case studies found.', 'site-blocks' ),
				'menu_name'          => __( 'Case Studies', 'site-blocks' ),
			),
			'public'              => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-portfolio',
			'menu_position'       => 26,
			'has_archive'         => 'case-studies',
			'rewrite'             => false,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author' ),
			'capability_type'     => 'post',
		)
	);

	register_taxonomy(
		'case_study_hub',
		'case_study',
		array(
			'labels'            => array(
				'name'          => __( 'Case Study Hubs', 'site-blocks' ),
				'singular_name' => __( 'Hub', 'site-blocks' ),
				'menu_name'     => __( 'Hubs', 'site-blocks' ),
			),
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'rewrite'           => array(
				'slug'       => 'case-studies',
				'with_front' => false,
			),
			'show_admin_column' => true,
		)
	);
}
add_action( 'init', 'site_blocks_register_case_study_cpt' );

/**
 * Seed the two hubs. Studies stay drafts until someone publishes them.
 */
function site_blocks_seed_case_study_hubs(): void {
	if ( ! taxonomy_exists( 'case_study_hub' ) ) {
		return;
	}

	$hubs = array(
		'enterprise'  => __( 'Enterprise', 'site-blocks' ),
		'residential' => __( 'Residential', 'site-blocks' ),
	);

	foreach ( $hubs as $slug => $label ) {
		if ( ! term_exists( $slug, 'case_study_hub' ) ) {
			wp_insert_term( $label, 'case_study_hub', array( 'slug' => $slug ) );
		}
	}
}
add_action( 'init', 'site_blocks_seed_case_study_hubs', 11 );

/**
 * Pretty URLs for a study nested under its hub.
 */
function site_blocks_case_study_rewrites(): void {
	add_rewrite_rule(
		'^case-studies/(enterprise|residential)/([^/]+)/?$',
		'index.php?post_type=case_study&name=$matches[2]',
		'top'
	);
	add_rewrite_rule(
		'^case-studies/?$',
		'index.php?post_type=case_study',
		'top'
	);
}
add_action( 'init', 'site_blocks_case_study_rewrites', 12 );

/**
 * @param string  $url  Permalink.
 * @param WP_Post $post Post object.
 */
function site_blocks_case_study_link( string $url, WP_Post $post ): string {
	if ( 'case_study' !== $post->post_type ) {
		return $url;
	}

	$terms = get_the_terms( $post, 'case_study_hub' );
	$hub   = ( is_array( $terms ) && isset( $terms[0]->slug ) ) ? $terms[0]->slug : 'enterprise';

	return home_url( user_trailingslashit( 'case-studies/' . $hub . '/' . $post->post_name ) );
}
add_filter( 'post_type_link', 'site_blocks_case_study_link', 10, 2 );

/**
 * Take Enterprise Insights off the public site and draft anything already published.
 */
function site_blocks_retire_enterprise_insights(): void {
	if ( get_option( 'site_blocks_enterprise_insights_retired' ) ) {
		return;
	}

	$ids = get_posts(
		array(
			'post_type'      => 'enterprise_insight',
			'post_status'    => array( 'publish', 'future', 'private' ),
			'posts_per_page' => 50,
			'fields'         => 'ids',
		)
	);

	foreach ( $ids as $id ) {
		wp_update_post(
			array(
				'ID'          => (int) $id,
				'post_status' => 'draft',
			)
		);
	}

	update_option( 'site_blocks_enterprise_insights_retired', '1' );
}
add_action( 'init', 'site_blocks_retire_enterprise_insights', 20 );
