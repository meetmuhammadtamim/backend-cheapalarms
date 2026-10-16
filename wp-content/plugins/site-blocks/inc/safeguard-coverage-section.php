<?php
/**
 * "Good installations start before anyone drills a hole" section.
 *
 * Copy on the left, the lobby coverage consultation image on the right.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the section.
 */
function site_blocks_render_coverage_section(): void {
	$image    = site_blocks_asset_url( 'images/coverage/lobby-coverage-consultation.webp' );
	$image_sm = site_blocks_asset_url( 'images/coverage/lobby-coverage-consultation-836.webp' );
	?>
		<section class="sg-coverage" aria-labelledby="sg-coverage-title" data-sg-coverage>
			<div class="sg-container sg-coverage__grid">
				<div class="sg-coverage__copy">
					<h2 id="sg-coverage-title" class="sg-coverage__title"><?php esc_html_e( 'Good installations start before anyone drills a hole', 'site-blocks' ); ?></h2>
					<p class="sg-coverage__text"><?php esc_html_e( 'Before equipment is installed, the site, coverage requirements, infrastructure, recording needs, access points, and future changes all need to be considered. A better design reduces blind spots, unnecessary complexity, and expensive changes later.', 'site-blocks' ); ?></p>
				</div>

				<div class="sg-coverage__media">
					<img
						class="sg-coverage__img"
						src="<?php echo esc_url( $image ); ?>"
						srcset="<?php echo esc_url( $image_sm ); ?> 836w, <?php echo esc_url( $image ); ?> 1672w"
						sizes="(min-width: 960px) 640px, 92vw"
						alt="<?php esc_attr_e( 'A Safeguard technician points out camera coverage while showing a floor plan on a tablet to a client in a building lobby.', 'site-blocks' ); ?>"
						width="1672"
						height="941"
						loading="lazy"
						decoding="async"
					/>
				</div>
			</div>
		</section>
	<?php
}
