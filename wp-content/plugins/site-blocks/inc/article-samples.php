<?php
/**
 * One published post and one published case study so the reading layout can be reviewed.
 *
 * Skips a slug that already exists. Does not overwrite later edits.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create the two review samples once hubs exist.
 */
function site_blocks_seed_article_samples(): void {
	if ( get_option( 'site_blocks_article_samples' ) ) {
		return;
	}

	if ( ! post_type_exists( 'case_study' ) || ! taxonomy_exists( 'case_study_hub' ) ) {
		return;
	}

	$camera = site_blocks_sample_attachment( 'images/cctv/spotlight-camera.webp' );
	$cloud  = site_blocks_sample_attachment( 'images/enterprise/ss-cloud-video.webp' );

	$study_id = site_blocks_sample_upsert(
		'case_study',
		'warehouse-alarm-and-cctv',
		__( 'Warehouse alarm and CCTV, fitted for an integrator', 'site-blocks' ),
		__( 'Safeguard was the installation partner. The integrator specified the system. We fitted the alarm, the cameras, and the cabling, then handed the site back ready to monitor.', 'site-blocks' ),
		site_blocks_sample_case_study_content( $camera )
	);

	$post_id = site_blocks_sample_upsert(
		'post',
		'what-monitoring-does-after-the-alarm',
		__( 'What happens after a monitored alarm goes off', 'site-blocks' ),
		__( 'Monitoring is the room that answers the panel. Here is what that room does, what stays with you on site, and when the monthly fee is worth paying.', 'site-blocks' ),
		site_blocks_sample_post_content( $cloud )
	);

	if ( $study_id > 0 ) {
		wp_set_object_terms( $study_id, array( 'enterprise' ), 'case_study_hub', false );
		if ( $camera > 0 ) {
			set_post_thumbnail( $study_id, $camera );
		}
	}

	if ( $post_id > 0 ) {
		$term    = term_exists( 'advice', 'category' );
		$term_id = 0;
		if ( is_array( $term ) && isset( $term['term_id'] ) ) {
			$term_id = (int) $term['term_id'];
		} elseif ( is_numeric( $term ) ) {
			$term_id = (int) $term;
		} else {
			$created = wp_insert_term( __( 'Advice', 'site-blocks' ), 'category', array( 'slug' => 'advice' ) );
			if ( is_array( $created ) && isset( $created['term_id'] ) ) {
				$term_id = (int) $created['term_id'];
			}
		}
		if ( $term_id > 0 ) {
			wp_set_post_categories( $post_id, array( $term_id ) );
		}
		if ( $cloud > 0 ) {
			set_post_thumbnail( $post_id, $cloud );
		}
	}

	if ( $study_id > 0 && $post_id > 0 ) {
		update_option( 'site_blocks_article_samples', '1' );
	}
}
add_action( 'init', 'site_blocks_seed_article_samples', 25 );

/**
 * Import a plugin image and return its attachment ID.
 */
function site_blocks_sample_attachment( string $relative ): int {
	if ( ! function_exists( 'site_blocks_media_import_one' ) ) {
		return 0;
	}

	$map    = site_blocks_media_map();
	$result = site_blocks_media_import_one( $relative, $map );
	if ( empty( $result['ok'] ) || empty( $result['id'] ) ) {
		return site_blocks_media_attachment_id( $relative );
	}

	site_blocks_media_map_save( $map );

	return (int) $result['id'];
}

/**
 * Insert the sample when that slug is still free. Existing copies are left alone.
 */
function site_blocks_sample_upsert( string $type, string $slug, string $title, string $excerpt, string $content ): int {
	$existing = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => $type,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $existing ) ) {
		return (int) $existing[0];
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => $type,
			'post_status'  => 'publish',
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_excerpt' => $excerpt,
			'post_content' => $content,
			'post_author'  => 1,
		),
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return 0;
	}

	$post_id = (int) $post_id;
	update_post_meta( $post_id, '_kad_post_layout', 'fullwidth' );
	update_post_meta( $post_id, '_kad_post_content_style', 'unboxed' );
	update_post_meta( $post_id, '_kad_post_title', 'hide' );
	update_post_meta( $post_id, '_kad_post_header', 'disable' );
	update_post_meta( $post_id, '_kad_post_footer', 'disable' );

	return $post_id;
}

/**
 * Figure markup for an imported photo.
 */
function site_blocks_sample_figure( int $attachment_id, string $alt ): string {
	if ( $attachment_id <= 0 ) {
		return '';
	}

	$html = wp_get_attachment_image(
		$attachment_id,
		'large',
		false,
		array(
			'alt' => $alt,
		)
	);

	if ( ! is_string( $html ) || '' === $html ) {
		return '';
	}

	return '<figure>' . $html . '</figure>';
}

/**
 * Blog sample. Several h2 headings so the contents list and the scroll bar have something to track.
 */
function site_blocks_sample_post_content( int $image_id ): string {
	$figure = site_blocks_sample_figure( $image_id, __( 'Cloud video view used with a monitored site', 'site-blocks' ) );
	$study  = esc_url( home_url( '/case-studies/enterprise/warehouse-alarm-and-cctv/' ) );
	$phone  = esc_url( home_url( '/contact/' ) );

	return '<p>A siren on the wall tells the street something happened. Monitoring tells a person. The panel sends a signal, someone checks it, and the right call goes out. That is the whole job. The rest of this note is what that looks like in practice.</p>
<h2>The panel is only the start</h2>
<p>The alarm panel, the sensors, and the communicator live on site. When a zone trips, the communicator sends an event to the monitoring room. A door, a detector, a panic button, and a power fail are different events. The room sees which one it was, and when.</p>
<p>Nothing useful happens if that signal never leaves the building. A monitored system needs a path off site, usually the internet with a mobile backup. If both paths drop, the room should see a fail, not silence.</p>
<h2>Who gets the call</h2>
<p>The monitoring room works from a response plan you agree up front. A verified alarm is not the same call as a low battery. For a real event the room calls the names on the list, in the order you set. If nobody answers, the plan says whether the next step is a guard, the police, or both.</p>
<ul>
<li>You name the people who can be woken up.</li>
<li>You say which events are urgent and which can wait until morning.</li>
<li>You update the list when staff change. An old mobile number is how a real alarm gets missed.</li>
</ul>
<p>The room does not invent a new plan at 2am. It follows the one on the account.</p>
<h2>What you still handle on site</h2>
<p>Monitoring does not replace a lock, a camera, or someone who can walk the floor. The room can tell you a roller door opened. Someone still has to decide whether that door should have opened. Cameras help that decision when they are recorded and someone can look.</p>
' . $figure . '
<p>If you want to see how that sits on a working fit-out, the <a href="' . $study . '">warehouse alarm and CCTV case study</a> is the install side of the same idea.</p>
<h2>When monitoring earns its fee</h2>
<p>It earns it when the site is empty and an event still needs a person. Workshops after hours, stock rooms, and sites with more than one address are the usual fit. A panel that only makes a noise outside is cheap. It is also easy to ignore.</p>
<p>The fee is monthly, on top of the install. Ask what is included: the response plan, the test schedule, and what happens when a path fails. If those answers are vague, the siren is doing more work than the room.</p>
<p>To talk it through on a real site, <a href="' . $phone . '">contact the team</a> or call 1300 225 276.</p>';
}

/**
 * Enterprise case study. Hardware only. The building and the integrator stay unnamed.
 */
function site_blocks_sample_case_study_content( int $image_id ): string {
	$figure = site_blocks_sample_figure( $image_id, __( 'Close-up of a camera used on the install', 'site-blocks' ) );
	$post   = esc_url( home_url( '/what-monitoring-does-after-the-alarm/' ) );
	$phone  = esc_url( home_url( '/contact/' ) );

	return '<p>Safeguard was the installation partner to a security integrator. The integrator specified the system and kept the client relationship. We supplied the labour, the cabling, and the commissioning, then left them with a site they could hand to monitoring.</p>
<h2>The brief</h2>
<p>One warehouse. An alarm for the doors and the internal space, cameras on the entries and the aisle ends, and a recorder the integrator could reach without a visit. No request for a new control room. The existing rack stayed where it was.</p>
<p>The public version of this job stops there on purpose. The building is not named, and the photos are hardware only.</p>
<h2>What went on the walls</h2>
<p>The alarm used door contacts on the roller doors and detectors in the open floor. Cameras covered the pedestrian door, the dock, and the two aisle ends that the integrator had marked. The recorder went in the rack they already had, on the shelf they pointed at.</p>
' . $figure . '
<ul>
<li>Contacts and detectors for the alarm.</li>
<li>Cameras only where the integrator had marked a view.</li>
<li>A recorder in the existing rack, not a new cabinet.</li>
</ul>
<h2>Cabling and power</h2>
<p>Runs were planned on the ground before anything was fixed. Longer paths were called out before the lift went up, so the quote and the cable on the drum matched. Power for the recorder came from the same rack circuit the integrator had nominated. We did not add a second supply.</p>
<p>At the end of the fit-out every camera had a picture, every door reported open and closed, and the communicator sent a test. That test is the point a job stops being a pile of hardware.</p>
<h2>Handover</h2>
<p>The integrator took the passwords, the zone list, and the camera names. Monitoring, if they turn it on, uses that list. What the room does with an event is covered in <a href="' . $post . '">what happens after a monitored alarm goes off</a>.</p>
<p>A similar install starts with a marked plan and a person who can walk the site. <a href="' . $phone . '">Contact the team</a> or call 1300 225 276.</p>';
}
