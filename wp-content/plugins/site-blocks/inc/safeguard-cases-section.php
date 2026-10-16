<?php
/**
 * "From a single residence to complex commercial sites" case studies section.
 *
 * A two-tone headline with a "See More" link, then three photo cards. Each card
 * is one link: a category label, the case title, and an arrow button sit in a
 * frosted panel over the photo.
 *
 * Placeholder content: there are no published case studies yet, so the three
 * cards point at the case studies archive and reuse existing site photographs.
 * Replace `href`, `image`, `label` and `title` with real posts when they exist.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cards (placeholders).
 *
 * `position` is the object-position used when the landscape photo is cropped to
 * the portrait card, chosen so the subject stays clear of the lower panel.
 *
 * @return array<int, array<string, string>>
 */
function site_blocks_get_case_cards(): array {
	return array(
		array(
			'file'     => 'cctv/residential',
			'small'    => 'cases/residential-720',
			'width'    => '1200',
			'height'   => '900',
			'position' => '50% 35%',
			'alt'      => __( 'A suburban family home with a garage and a white picket fence.', 'site-blocks' ),
			'label'    => __( 'Residential', 'site-blocks' ),
			'title'    => __( 'CCTV and access for a family home', 'site-blocks' ),
		),
		array(
			'file'     => 'cctv/commercial',
			'small'    => 'cases/commercial-720',
			'width'    => '1448',
			'height'   => '1086',
			'position' => '38% 40%',
			'alt'      => __( 'A commercial building with a glass entrance and a car park.', 'site-blocks' ),
			'label'    => __( 'Commercial upgrade', 'site-blocks' ),
			'title'    => __( 'Replacing an ageing camera system', 'site-blocks' ),
		),
		array(
			'file'     => 'enterprise/hub-integration',
			'small'    => 'cases/integrated-720',
			'width'    => '1200',
			'height'   => '900',
			'position' => '30% 30%',
			'alt'      => __( 'A Safeguard technician at a monitoring desk with a wall of camera feeds.', 'site-blocks' ),
			'label'    => __( 'Integrated commercial', 'site-blocks' ),
			'title'    => __( 'One system for cameras, alarms and doors', 'site-blocks' ),
		),
	);
}

/**
 * Render the section.
 */
function site_blocks_render_cases_section(): void {
	$cards   = site_blocks_get_case_cards();
	$archive = site_blocks_case_studies_url();
	?>
		<section id="sg-cases" class="sg-cases" aria-labelledby="sg-cases-title">
			<div class="sg-container">
				<header class="sg-cases__head" data-sg-reveal>
					<h2 id="sg-cases-title" class="sg-cases__title">
						<span class="sg-cases__title-line"><?php esc_html_e( 'From a single residence', 'site-blocks' ); ?></span>
						<span class="sg-cases__title-line sg-cases__title-line--soft"><?php esc_html_e( 'to complex commercial sites', 'site-blocks' ); ?></span>
					</h2>
					<a class="sg-cases__more" href="<?php echo esc_url( $archive ); ?>">
						<span><?php esc_html_e( 'See More', 'site-blocks' ); ?></span>
						<?php site_blocks_icon( 'arrow-right', 20 ); ?>
					</a>
				</header>

				<ul class="sg-cases__grid" role="list" data-sg-stagger>
					<?php foreach ( $cards as $card ) : ?>
						<li class="sg-cases__item" data-sg-reveal>
							<a class="sg-cases__card" href="<?php echo esc_url( $archive ); ?>">
								<img
									class="sg-cases__img"
									src="<?php echo esc_url( site_blocks_asset_url( 'images/' . $card['file'] . '.webp' ) ); ?>"
									srcset="<?php echo esc_url( site_blocks_asset_url( 'images/' . $card['small'] . '.webp' ) ); ?> 720w, <?php echo esc_url( site_blocks_asset_url( 'images/' . $card['file'] . '.webp' ) ); ?> <?php echo esc_attr( $card['width'] ); ?>w"
									sizes="(min-width: 900px) 380px, 92vw"
									alt="<?php echo esc_attr( $card['alt'] ); ?>"
									width="<?php echo esc_attr( $card['width'] ); ?>"
									height="<?php echo esc_attr( $card['height'] ); ?>"
									loading="lazy"
									decoding="async"
									style="object-position: <?php echo esc_attr( $card['position'] ); ?>"
								/>
								<span class="sg-cases__panel">
									<span class="sg-cases__label"><?php echo esc_html( $card['label'] ); ?></span>
									<h3 class="sg-cases__name"><?php echo esc_html( $card['title'] ); ?></h3>
									<span class="sg-cases__go" aria-hidden="true"><?php site_blocks_icon( 'arrow-top-right', 22 ); ?></span>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
	<?php
}
