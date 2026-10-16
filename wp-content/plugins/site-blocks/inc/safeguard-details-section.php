<?php
/**
 * "The details behind a reliable system" section.
 *
 * Centred headline and intro, then three scenes: positioned properly, tested
 * to communicate, installed to be maintained. Static; no labels or controls.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Scenes.
 *
 * `top` positions each illustration so the bottom of its artwork lands on the
 * same baseline across the row (the three source canvases are identical in
 * size but the artwork sits at different heights inside them).
 *
 * @return array<int, array<string, string>>
 */
function site_blocks_get_details_scenes(): array {
	return array(
		array(
			'file'  => 'positioned-properly',
			'top'   => '4.9%',
			'alt'   => __( 'A security camera mounted on a wall covers the entrance of a building as a person walks toward the door.', 'site-blocks' ),
			'title' => __( 'Positioned properly', 'site-blocks' ),
			'desc'  => __( 'The right camera angle helps capture clear, usable footage when it matters most.', 'site-blocks' ),
		),
		array(
			'file'  => 'tested-to-communicate',
			'top'   => '11%',
			'alt'   => __( 'An alarm panel sends a signal through a tower to a phone and to an operator at a monitoring desk.', 'site-blocks' ),
			'title' => __( 'Tested to communicate', 'site-blocks' ),
			'desc'  => __( 'Alarm signals need to reach the right people reliably, not just sound locally.', 'site-blocks' ),
		),
		array(
			'file'  => 'installed-to-be-maintained',
			'top'   => '-6.8%',
			'alt'   => __( 'A technician works on a neatly cabled network rack.', 'site-blocks' ),
			'title' => __( 'Installed to be maintained', 'site-blocks' ),
			'desc'  => __( 'Clean installation makes future servicing, changes, and fault-finding faster and easier.', 'site-blocks' ),
		),
	);
}

/**
 * Render the section.
 */
function site_blocks_render_details_section(): void {
	$scenes = site_blocks_get_details_scenes();
	?>
		<section class="sg-details" aria-labelledby="sg-details-title" data-sg-details>
			<div class="sg-container">
				<header class="sg-details__intro">
					<h2 id="sg-details-title" class="sg-details__title" data-sg-reveal><?php esc_html_e( 'A security system is only as good as the details behind it', 'site-blocks' ); ?></h2>
					<p class="sg-details__lead" data-sg-reveal>
							<?php
							printf(
								/* translators: %s: the emphasised phrase, wrapped in markup */
								esc_html__( 'A camera in the wrong position, an alarm that does not communicate properly, or a poorly organised network can create a false sense of security. Safeguard looks at the complete system, %s, to make sure the technology, installation, and operation work together.', 'site-blocks' ),
								'<span class="sg-details__mark" data-sg-mark>' . esc_html__( 'not just the equipment', 'site-blocks' ) . '</span>'
							);
							?>
						</p>
				</header>

				<ul class="sg-details__grid" role="list">
					<?php foreach ( $scenes as $scene ) : ?>
						<li class="sg-details__item">
							<div class="sg-details__art">
								<img
									class="sg-details__img"
									src="<?php echo esc_url( site_blocks_asset_url( 'images/details/' . $scene['file'] . '.webp' ) ); ?>"
									srcset="<?php echo esc_url( site_blocks_asset_url( 'images/details/' . $scene['file'] . '-724.webp' ) ); ?> 724w, <?php echo esc_url( site_blocks_asset_url( 'images/details/' . $scene['file'] . '.webp' ) ); ?> 1448w"
									sizes="(min-width: 900px) 380px, 90vw"
									alt="<?php echo esc_attr( $scene['alt'] ); ?>"
									width="1448"
									height="1086"
									loading="lazy"
									decoding="async"
									style="top: <?php echo esc_attr( $scene['top'] ); ?>"
								/>
							</div>
							<h3 class="sg-details__item-title"><?php echo esc_html( $scene['title'] ); ?></h3>
							<p class="sg-details__item-desc"><?php echo esc_html( $scene['desc'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
	<?php
}
