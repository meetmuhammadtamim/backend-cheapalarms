<?php
/**
 * "We care about the parts most people never see" section.
 *
 * Copy and a short checklist on the left, the cabling montage on the right.
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
function site_blocks_render_quality_section(): void {
	$image    = site_blocks_asset_url( 'images/quality/network-cabling-installation.webp' );
	$image_sm = site_blocks_asset_url( 'images/quality/network-cabling-installation-724.webp' );
	$points   = array(
		__( 'Labelled cables and ports', 'site-blocks' ),
		__( 'An organised, readable cabinet', 'site-blocks' ),
		__( 'A system the next technician can follow', 'site-blocks' ),
	);
	?>
		<section class="sg-quality" aria-labelledby="sg-quality-title">
			<div class="sg-container sg-quality__grid">
				<div class="sg-quality__copy" data-sg-stagger>
					<h2 id="sg-quality-title" class="sg-quality__title" data-sg-reveal><?php esc_html_e( 'We care about the parts most people never see.', 'site-blocks' ); ?></h2>
					<p class="sg-quality__text" data-sg-reveal><?php esc_html_e( 'The finished camera may be the visible part of an installation, but the quality is often decided behind the walls, inside the cabinet, and at the connection points. We aim for clean cable paths, organised equipment, labelled connections, and installations that are easier to understand, maintain, and expand.', 'site-blocks' ); ?></p>
					<ul class="sg-quality__list" role="list">
						<?php foreach ( $points as $point ) : ?>
							<li data-sg-reveal>
								<span class="sg-quality__check" aria-hidden="true"><?php site_blocks_icon( 'circle-check', 24, 'sg-icon--filled-check' ); ?></span>
								<span><?php echo esc_html( $point ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div class="sg-quality__media" data-sg-reveal="left">
					<img
						class="sg-quality__img"
						src="<?php echo esc_url( $image ); ?>"
						srcset="<?php echo esc_url( $image_sm ); ?> 724w, <?php echo esc_url( $image ); ?> 1448w"
						sizes="(min-width: 960px) 600px, 92vw"
						alt="<?php esc_attr_e( 'A technician labelling patch cables in a network cabinet, with a close-up of numbered cable labels and a tidy overhead cable tray.', 'site-blocks' ); ?>"
						width="1448"
						height="1086"
						loading="lazy"
						decoding="async"
					/>
				</div>
			</div>
		</section>
	<?php
}
