<?php
/**
 * "Why clients trust Safeguard" proof band, directly below the hero.
 *
 * Cards on the left (a blue-gradient "20+ years" card and a white ASIAL card,
 * each with small 3D art rising above it) and, on the right, a headline, a short
 * introduction and four icon features in a two-by-two grid. All of it sits
 * directly on the page, with no container, no photograph and no checklist.
 *
 * The headline, introduction and feature copy are draft wording taken from a
 * design mock-up; confirm before launch.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Image markup for a converted 3D illustration.
 *
 * @param string $name File stem under images/why/.
 */
function site_blocks_why_art( string $name ): void {
	$full  = site_blocks_asset_url( 'images/why/' . $name . '.webp' );
	$half  = site_blocks_asset_url( 'images/why/' . $name . '-627.webp' );
	$small = site_blocks_asset_url( 'images/why/' . $name . '-400.webp' );
	?>
	<img
		class="sg-why__art"
		src="<?php echo esc_url( $full ); ?>"
		srcset="<?php echo esc_url( $small ); ?> 400w, <?php echo esc_url( $half ); ?> 627w, <?php echo esc_url( $full ); ?> 1254w"
		sizes="(min-width: 900px) 130px, 30vw"
		alt=""
		width="1254"
		height="1254"
		loading="lazy"
		decoding="async"
		aria-hidden="true"
	/>
	<?php
}

/**
 * Render the section.
 */
function site_blocks_render_why_section(): void {
	$features = array(
		array(
			'icon'  => 'pen-tool',
			'title' => __( 'Practical system design', 'site-blocks' ),
			'text'  => __( 'Solutions built around your site, risks and day-to-day operations.', 'site-blocks' ),
		),
		array(
			'icon'  => 'cctv',
			'title' => __( 'Integrated technologies', 'site-blocks' ),
			'text'  => __( 'CCTV, alarms, access control and intercoms working together as a complete system.', 'site-blocks' ),
		),
		array(
			'icon'  => 'hard-hat',
			'title' => __( 'Clean, professional installs', 'site-blocks' ),
			'text'  => __( 'Experienced installers, minimal disruption and a focus on quality and safety.', 'site-blocks' ),
		),
		array(
			'icon'  => 'headset',
			'title' => __( 'Support that continues after handover', 'site-blocks' ),
			'text'  => __( 'Ongoing support, upgrades, takeovers and system improvements as your needs evolve.', 'site-blocks' ),
		),
	);
	?>
		<section class="sg-why" aria-labelledby="sg-why-title">
			<div class="sg-container">
				<div class="sg-why__bento">
					<ul class="sg-why__cards" role="list" data-sg-stagger>
						<li class="sg-why__cell" data-sg-reveal="left"><div class="sg-why__card sg-why__card--blue">
							<?php site_blocks_why_art( 'safeguard-3d' ); ?>
							<p class="sg-why__figure"><?php esc_html_e( '20+', 'site-blocks' ); ?></p>
							<p class="sg-why__caption"><?php esc_html_e( 'Years Of Experience', 'site-blocks' ); ?></p>
						</div></li>
						<li class="sg-why__cell" data-sg-reveal="left"><div class="sg-why__card sg-why__card--white">
							<?php site_blocks_why_art( 'seal' ); ?>
							<span class="sg-why__logo">
								<img
									src="<?php echo esc_url( site_blocks_asset_url( 'images/why/asial.webp' ) ); ?>"
									alt="<?php esc_attr_e( 'Australian Security Industry Association Limited (ASIAL)', 'site-blocks' ); ?>"
									width="256"
									height="188"
									loading="lazy"
									decoding="async"
								/>
							</span>
							<p class="sg-why__caption"><?php esc_html_e( 'ASIAL Member', 'site-blocks' ); ?></p>
						</div></li>
					</ul>

					<div class="sg-why__copy" data-sg-stagger>
						<h2 id="sg-why-title" class="sg-why__heading" data-sg-reveal>
							<?php esc_html_e( 'Proven security advice, installation and', 'site-blocks' ); ?>
							<span class="sg-why__accent"><?php esc_html_e( 'support', 'site-blocks' ); ?></span>
						</h2>
						<p class="sg-why__lead" data-sg-reveal><?php esc_html_e( 'We design, install and support integrated security systems for Australian businesses, with a practical, no-nonsense approach that keeps people, property and operations secure.', 'site-blocks' ); ?></p>

						<ul class="sg-why__features" role="list">
							<?php foreach ( $features as $feature ) : ?>
								<li class="sg-why__feature" data-sg-reveal>
									<span class="sg-why__icon" aria-hidden="true"><?php site_blocks_icon( $feature['icon'], 24 ); ?></span>
									<div class="sg-why__feature-body">
										<h3 class="sg-why__feature-title"><?php echo esc_html( $feature['title'] ); ?></h3>
										<p class="sg-why__feature-text"><?php echo esc_html( $feature['text'] ); ?></p>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</section>
	<?php
}
