<?php
/**
 * Two placeholder blog posts, so the homepage blog section shows a full row.
 *
 * Marked with the `_sg_dummy_post` meta so they are easy to find and delete
 * once real articles are published. Created once; later edits are kept.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Category id by slug, creating it when missing.
 */
function site_blocks_dummy_category( string $slug, string $name ): int {
	$term = term_exists( $slug, 'category' );
	if ( is_array( $term ) && isset( $term['term_id'] ) ) {
		return (int) $term['term_id'];
	}
	if ( is_numeric( $term ) ) {
		return (int) $term;
	}

	$created = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );

	return ( is_array( $created ) && isset( $created['term_id'] ) ) ? (int) $created['term_id'] : 0;
}

/**
 * Create the two placeholder posts.
 */
function site_blocks_seed_dummy_blog_posts(): void {
	if ( get_option( 'site_blocks_dummy_blog_posts' ) ) {
		return;
	}

	if ( ! function_exists( 'site_blocks_sample_upsert' ) || ! function_exists( 'site_blocks_sample_attachment' ) ) {
		return;
	}

	$contact = esc_url( home_url( '/contact/' ) );

	$posts = array(
		array(
			'slug'     => 'choosing-cctv-cameras-for-your-property',
			'date'     => '2026-09-22 09:00:00',
			'title'    => __( 'How to choose CCTV cameras for your property', 'site-blocks' ),
			'excerpt'  => __( 'Camera count matters less than camera position. Here is how to think about coverage, lighting and recording before you buy.', 'site-blocks' ),
			'image'    => 'images/cctv/spotlight.webp',
			'alt'      => __( 'A modern home at dusk with exterior lighting.', 'site-blocks' ),
			'category' => array( 'cctv', __( 'CCTV', 'site-blocks' ) ),
			'content'  => '<p>Most people start with a number: how many cameras do I need? A better starting point is a question: what do I need to be able to see, and when? The answer changes the position, the lens and the recording setup far more than the count does.</p>
<h2>Start with what you need to see</h2>
<p>Walk the property and list the points that matter: entry doors, the driveway, the gate, a loading area, a cash desk. For each one, decide whether you need to recognise a face, read a number plate, or just know that someone was there. Each of those asks for a different camera and a different position.</p>
<h2>Light and angle decide the picture</h2>
<p>A good camera in the wrong place gives a poor image. Backlit doors, glare from glass and dark corners are the usual problems. Mounting height and angle matter as much as resolution, and it is worth checking the view at night, not only in the day.</p>
<h2>Recording and who looks at it</h2>
<p>Footage only helps if it is recorded, kept for long enough and easy to find. Decide how many days you want to keep, who can view it, and whether you want alerts or only playback. A simple, well-placed system that someone actually checks beats a large one that nobody opens.</p>
<p>If you would like a second opinion on coverage for your site, <a href="' . $contact . '">talk to the team</a> and we will walk through it with you.</p>',
		),
		array(
			'slug'     => 'access-control-or-keys',
			'date'     => '2026-09-08 09:00:00',
			'title'    => __( 'Access control or keys: which suits your business?', 'site-blocks' ),
			'excerpt'  => __( 'Keys are cheap until someone leaves with one. Here is what access control changes day to day, and when it is worth it.', 'site-blocks' ),
			'image'    => 'images/access-control/gallery-03.webp',
			'alt'      => __( 'A building entrance with a video intercom and access reader.', 'site-blocks' ),
			'category' => array( 'access-control', __( 'Access control', 'site-blocks' ) ),
			'content'  => '<p>A key opens a door for whoever holds it. That is its strength and its weakness. Access control keeps the door but changes the question from who has a key to who is allowed in, and when.</p>
<h2>What keys do well</h2>
<p>For a small site with a few staff and little turnover, keys are simple and cheap. Nothing to configure, nothing to power. The cost shows up later: re-keying after a lost key, and not knowing who was in the building at a given time.</p>
<h2>What access control changes</h2>
<p>A card, fob or code can be switched off without touching the lock. You can set hours for each person, see who entered and when, and give a contractor a pass that expires. For a site with shifts, shared spaces or more than one door, that control tends to pay for itself in time saved.</p>
<h2>Choosing between them</h2>
<p>Think about how often people change, how many doors matter, and whether you need a record of entry. Many sites use both: access control on the doors that matter and keys elsewhere. The right mix depends on the building and the people in it.</p>
<p>If you are weighing it up for your premises, <a href="' . $contact . '">get in touch</a> and we can look at the doors with you.</p>',
		),
	);

	$done = 0;
	foreach ( $posts as $item ) {
		// The photo is the featured image; the article template shows it above the text.
		$image   = site_blocks_sample_attachment( $item['image'] );
		$post_id = site_blocks_sample_upsert( 'post', $item['slug'], $item['title'], $item['excerpt'], $item['content'] );
		if ( $post_id <= 0 ) {
			continue;
		}

		wp_update_post(
			array(
				'ID'            => $post_id,
				'post_date'     => $item['date'],
				'post_date_gmt' => get_gmt_from_date( $item['date'] ),
			)
		);
		update_post_meta( $post_id, '_sg_dummy_post', '1' );

		$categories = array( site_blocks_dummy_category( 'advice', __( 'Advice', 'site-blocks' ) ), site_blocks_dummy_category( $item['category'][0], $item['category'][1] ) );
		wp_set_post_categories( $post_id, array_values( array_filter( $categories ) ) );

		if ( $image > 0 ) {
			set_post_thumbnail( $post_id, $image );
		}
		++$done;
	}

	if ( count( $posts ) === $done ) {
		update_option( 'site_blocks_dummy_blog_posts', '1' );
	}
}
add_action( 'init', 'site_blocks_seed_dummy_blog_posts', 27 );
