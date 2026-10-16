<?php
/**
 * Homepage "Brands We Install And Support" showcase.
 *
 * One navy stage shows the selected brand: name, a line about it, a button to
 * its page and the product image. A row of logo tabs underneath switches the
 * stage. Every panel is in the markup (not loaded on demand), so the content
 * is readable without JavaScript; the script only moves the selection.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Brands shown in the showcase, in tab order.
 *
 * `photo` marks a product photo with its own background (not a cutout): the
 * stylesheet feathers its edges into the stage instead of showing a rectangle.
 * `logo_name` shows the brand name beside a symbol-only logo.
 *
 * @return array<int, array<string, mixed>>
 */
function site_blocks_get_brand_showcase(): array {
	return array(
		array(
			'slug'    => 'ajax-alarm-systems',
			'name'    => 'Ajax',
			'summary' => __( 'Wireless alarm systems you can run from a phone, for homes and businesses.', 'site-blocks' ),
			'logo'    => 'images/brands/ajax.webp',
			'image'   => 'images/ajax/ajax-products.webp',
			'width'   => 1875,
			'height'  => 1000,
			'alt'     => __( 'Ajax motion detector with camera, hub, phone app and keypad.', 'site-blocks' ),
		),
		array(
			'slug'    => 'paradox-m25',
			'name'    => 'Paradox',
			'summary' => __( 'Dependable alarm panels and keypads for homes and business sites.', 'site-blocks' ),
			'logo'    => 'images/brands/paradox.webp',
			'image'   => 'images/paradox/m25-featured.webp',
			'width'   => 600,
			'height'  => 600,
			'alt'     => __( 'Paradox M25 alarm panel.', 'site-blocks' ),
		),
		array(
			'slug'    => 'milesight',
			'name'    => 'Milesight',
			'summary' => __( 'Network cameras with edge AI analytics, from domes to PTZ, built for real environments.', 'site-blocks' ),
			'logo'    => 'images/brands/milesight.png',
			'image'   => 'images/brands/milesight-dome-hero.webp',
			'width'   => 1122,
			'height'  => 1402,
			'photo'   => true,
			'alt'     => __( 'Milesight AI dome camera.', 'site-blocks' ),
		),
		array(
			'slug'      => 'nx-witness',
			'name'      => 'NX Witness',
			'summary'   => __( 'Video management software that brings cameras, recording and events into one view.', 'site-blocks' ),
			'logo'      => 'images/brands/nx-witness.png',
			'logo_name' => true,
			'image'     => 'images/brands/nx-witness-hero.webp',
			'width'     => 1586,
			'height'    => 992,
			'alt'       => __( 'NX Witness client showing a vehicle search across site cameras.', 'site-blocks' ),
		),
		array(
			'slug'    => 'hanwha-vision',
			'name'    => 'Hanwha Vision',
			'summary' => __( 'Professional surveillance cameras with advanced imaging and intelligent analytics.', 'site-blocks' ),
			'logo'    => 'images/brands/hanwha-vision.png',
			'image'   => 'images/brands/hanwha-qno-8010r.webp',
			'width'   => 1688,
			'height'  => 1274,
			'alt'     => __( 'Hanwha Vision QNO-8010R bullet camera.', 'site-blocks' ),
		),
		array(
			'slug'    => 'akuvox',
			'name'    => 'Akuvox',
			'summary' => __( 'IP video intercoms and door stations to see, talk and unlock from anywhere.', 'site-blocks' ),
			'logo'    => 'images/brands/akuvox.webp',
			'image'   => 'images/intercom/hero.webp',
			'width'   => 1230,
			'height'  => 1024,
			'alt'     => __( 'Akuvox door station and indoor monitor.', 'site-blocks' ),
		),
	);
}

/**
 * Render the section.
 */
function site_blocks_render_brands_section(): void {
	$brands = site_blocks_get_brand_showcase();
	if ( empty( $brands ) ) {
		return;
	}

	$index_url = home_url( '/brands/' );
	?>
		<section id="sg-brands" class="sg-brands-home" aria-labelledby="sg-brands-home-title">
			<div class="sg-container">
				<header class="sg-brands-home__head" data-sg-reveal>
					<div class="sg-brands-home__intro">
						<h2 id="sg-brands-home-title" class="sg-brands-home__title"><?php esc_html_e( 'Brands We Install And Support', 'site-blocks' ); ?></h2>
						<p class="sg-brands-home__lead"><?php esc_html_e( 'We specify equipment around the site, from alarms and cameras to video management and intercoms.', 'site-blocks' ); ?></p>
					</div>
					<a class="sg-brands-home__more" href="<?php echo esc_url( $index_url ); ?>">
						<span><?php esc_html_e( 'View All Brands', 'site-blocks' ); ?></span>
						<?php site_blocks_icon( 'arrow-right', 20 ); ?>
					</a>
				</header>

				<div class="sg-brands-home__showcase" data-sg-brands>
					<div class="sg-brands-home__stage" data-sg-reveal>
						<?php
						foreach ( $brands as $i => $brand ) :
							$is_first = 0 === $i;
							$url      = home_url( site_blocks_brand_path( (string) $brand['slug'] ) );
							$classes  = 'sg-brands-home__panel' . ( $is_first ? ' is-active' : '' ) . ( ! empty( $brand['photo'] ) ? ' sg-brands-home__panel--photo' : '' );
							// Longer names get a smaller watermark so it stays out from behind the copy.
							$mark_size = min( 11.0, round( 93 / max( 1, strlen( (string) $brand['name'] ) ), 1 ) );
							?>
							<div
								class="<?php echo esc_attr( $classes ); ?>"
								id="sg-brand-panel-<?php echo esc_attr( (string) $brand['slug'] ); ?>"
								role="tabpanel"
								aria-labelledby="sg-brand-tab-<?php echo esc_attr( (string) $brand['slug'] ); ?>"
							>
								<span class="sg-brands-home__mark" aria-hidden="true" style="font-size: <?php echo esc_attr( (string) $mark_size ); ?>cqw"><?php echo esc_html( (string) $brand['name'] ); ?></span>
								<div class="sg-brands-home__copy">
									<h3 class="sg-brands-home__name"><?php echo esc_html( (string) $brand['name'] ); ?></h3>
									<p class="sg-brands-home__summary"><?php echo esc_html( (string) $brand['summary'] ); ?></p>
									<a class="sg-btn sg-btn--orange sg-brands-home__go" href="<?php echo esc_url( $url ); ?>">
										<?php
										printf(
											/* translators: %s: brand name */
											esc_html__( 'Explore %s', 'site-blocks' ),
											esc_html( (string) $brand['name'] )
										);
										?>
									</a>
								</div>
								<div class="sg-brands-home__visual">
									<img
										class="sg-brands-home__img"
										src="<?php echo esc_url( site_blocks_asset_url( (string) $brand['image'] ) ); ?>"
										width="<?php echo esc_attr( (string) $brand['width'] ); ?>"
										height="<?php echo esc_attr( (string) $brand['height'] ); ?>"
										alt="<?php echo esc_attr( (string) $brand['alt'] ); ?>"
										loading="lazy"
										decoding="async"
									/>
								</div>
							</div>
						<?php endforeach; ?>

						<button type="button" class="sg-brands-home__nav sg-brands-home__nav--prev" data-sg-brand-prev aria-label="<?php esc_attr_e( 'Previous brand', 'site-blocks' ); ?>">
							<?php site_blocks_icon( 'chevron-left', 22 ); ?>
						</button>
						<button type="button" class="sg-brands-home__nav sg-brands-home__nav--next" data-sg-brand-next aria-label="<?php esc_attr_e( 'Next brand', 'site-blocks' ); ?>">
							<?php site_blocks_icon( 'chevron-right', 22 ); ?>
						</button>
					</div>

					<div class="sg-brands-home__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Brands', 'site-blocks' ); ?>" data-sg-stagger>
						<?php
						foreach ( $brands as $i => $brand ) :
							$is_first = 0 === $i;
							?>
							<div class="sg-brands-home__tabcell" role="presentation" data-sg-reveal>
								<button
									type="button"
									class="sg-brands-home__tab<?php echo $is_first ? ' is-active' : ''; ?>"
									role="tab"
									id="sg-brand-tab-<?php echo esc_attr( (string) $brand['slug'] ); ?>"
									aria-controls="sg-brand-panel-<?php echo esc_attr( (string) $brand['slug'] ); ?>"
									aria-selected="<?php echo $is_first ? 'true' : 'false'; ?>"
									aria-label="<?php echo esc_attr( (string) $brand['name'] ); ?>"
									tabindex="<?php echo $is_first ? '0' : '-1'; ?>"
								>
									<img
										class="sg-brands-home__logo"
										src="<?php echo esc_url( site_blocks_asset_url( (string) $brand['logo'] ) ); ?>"
										alt=""
										loading="lazy"
										decoding="async"
									/>
									<?php if ( ! empty( $brand['logo_name'] ) ) : ?>
										<span class="sg-brands-home__logo-name" aria-hidden="true"><?php echo esc_html( (string) $brand['name'] ); ?></span>
									<?php endif; ?>
								</button>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>
	<?php
}
