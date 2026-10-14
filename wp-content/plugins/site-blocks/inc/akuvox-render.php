<?php
/**
 * Akuvox landing page render dispatch.
 *
 * Reuses Ajax / Paradox product-landing section CSS (sg-ajax-*, sg-paradox-*) for sibling visual parity.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once SITE_BLOCKS_DIR . 'inc/akuvox-config.php';
require_once SITE_BLOCKS_DIR . 'inc/safeguard-stage-hero.php';
require_once SITE_BLOCKS_DIR . 'inc/mdi-icons.php';

/**
 * Dispatch an Akuvox section render.
 */
function site_blocks_render_akuvox_section( string $section ): void {
	if ( ! site_blocks_akuvox_section_applies( $section ) ) {
		return;
	}

	switch ( $section ) {
		case 'difference':
			site_blocks_akuvox_render_difference();
			break;
		case 'portal':
			site_blocks_akuvox_render_portal();
			break;
		case 'products':
			site_blocks_akuvox_render_products();
			break;
		case 'property-fit':
			site_blocks_akuvox_render_property_fit();
			break;
		case 'install':
			site_blocks_akuvox_render_install();
			break;
		case 'compare':
			site_blocks_akuvox_render_compare();
			break;
		case 'faq':
			site_blocks_akuvox_render_faq();
			break;
		case 'cta':
			site_blocks_akuvox_render_cta();
			break;
	}
}

/**
 * Hero.
 */
function site_blocks_akuvox_render_hero(): void {
	$config = site_blocks_akuvox_hero_config();

	site_blocks_render_stage_hero(
		array(
			'id'              => (string) $config['id'],
			'class'           => (string) $config['class'],
			'breadcrumb'      => array(
				array(
					'label' => __( 'Home', 'site-blocks' ),
					'url'   => home_url( '/' ),
				),
				array(
					'label' => __( 'Intercom Systems', 'site-blocks' ),
					'url'   => home_url( '/intercom-systems/' ),
				),
				array(
					'label'   => __( 'Akuvox', 'site-blocks' ),
					'current' => true,
				),
			),
			'eyebrow'         => (string) $config['eyebrow'],
			'title'           => (string) $config['title'],
			'lead'            => (string) $config['lead'],
			'primary_label'   => (string) $config['primary_label'],
			'primary_url'     => (string) $config['primary_url'],
			'secondary_label' => (string) $config['secondary_label'],
			'secondary_url'   => (string) $config['secondary_url'],
			'trust'           => $config['trust'] ?? array(),
			'frame_image'     => (string) $config['frame_image'],
			'frame_alt'       => (string) $config['frame_alt'],
		)
	);
}

/**
 * Difference (copy + numbered pillars | product frame).
 */
function site_blocks_akuvox_render_difference(): void {
	$config = site_blocks_akuvox_difference_config();
	?>
	<section class="sg-paradox-difference alignfull" aria-labelledby="sg-akuvox-difference-heading">
		<div class="sg-container">
			<div class="sg-paradox-difference__layout">
				<div class="sg-paradox-difference__copy">
					<p class="sg-ajax-section__eyebrow"><?php echo esc_html( (string) $config['eyebrow'] ); ?></p>
					<h2 id="sg-akuvox-difference-heading" class="sg-paradox-difference__title">
						<?php echo esc_html( (string) $config['title'] ); ?>
					</h2>
					<p class="sg-paradox-difference__intro"><?php echo esc_html( (string) $config['intro'] ); ?></p>
					<ol class="sg-paradox-difference__points" role="list">
						<?php foreach ( $config['points'] as $index => $point ) : ?>
							<li class="sg-paradox-difference__point">
								<span class="sg-paradox-difference__num" aria-hidden="true"><?php echo esc_html( (string) ( $index + 1 ) ); ?></span>
								<span class="sg-paradox-difference__rule" aria-hidden="true"></span>
								<div class="sg-paradox-difference__point-body">
									<h3 class="sg-paradox-difference__point-title"><?php echo esc_html( (string) $point['title'] ); ?></h3>
									<p class="sg-paradox-difference__point-desc"><?php echo esc_html( (string) $point['description'] ); ?></p>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
				<figure class="sg-paradox-difference__media">
					<div class="sg-paradox-difference__frame">
						<?php site_blocks_stage_hero_media( (string) $config['image'], (string) $config['image_alt'], 'sg-paradox-difference__img', 'lazy' ); ?>
					</div>
				</figure>
			</div>
		</div>
	</section>
	<?php
}

/**
 * SmartPlus app band.
 */
function site_blocks_akuvox_render_portal(): void {
	$config = site_blocks_akuvox_portal_config();
	?>
	<section class="sg-band sg-portal-band sg-paradox-blueeye sg-akuvox-smartplus alignfull" aria-labelledby="<?php echo esc_attr( (string) $config['heading_id'] ); ?>">
		<div class="sg-container sg-portal-band__grid">
			<div class="sg-paradox-blueeye__phone">
				<?php site_blocks_stage_hero_media( (string) $config['image_path'], (string) $config['image_alt'], 'sg-paradox-blueeye__img', 'lazy' ); ?>
			</div>
			<div class="sg-portal-band__copy">
				<?php if ( ! empty( $config['icon_path'] ) ) : ?>
					<div class="sg-paradox-blueeye__mark">
						<?php site_blocks_stage_hero_media( (string) $config['icon_path'], (string) ( $config['icon_alt'] ?? '' ), 'sg-paradox-blueeye__mark-img', 'lazy' ); ?>
					</div>
				<?php endif; ?>
				<h2 id="<?php echo esc_attr( (string) $config['heading_id'] ); ?>" class="sg-portal-band__title">
					<?php echo esc_html( (string) $config['title_before'] ); ?>
					<span class="sg-accent"><?php echo esc_html( (string) $config['title_accent'] ); ?></span>
				</h2>
				<p class="sg-portal-band__intro"><?php echo esc_html( (string) $config['intro'] ); ?></p>
				<ul class="sg-portal-band__list" role="list">
					<?php foreach ( $config['bullets'] as $bullet ) : ?>
						<li>
							<span class="sg-portal-band__check" aria-hidden="true">
								<?php site_blocks_icon( 'check', 16 ); ?>
							</span>
							<?php echo esc_html( (string) $bullet ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Products (intro + featured station + supporting cards).
 */
function site_blocks_akuvox_render_products(): void {
	$config   = site_blocks_akuvox_products_config();
	$cta      = $config['cta'];
	$featured = $config['featured'];
	?>
	<section class="sg-paradox-products alignfull" aria-labelledby="sg-akuvox-products-heading">
		<div class="sg-container">
			<div class="sg-paradox-products__layout">
				<div class="sg-paradox-products__intro">
					<p class="sg-ajax-section__eyebrow"><?php echo esc_html( (string) $config['eyebrow'] ); ?></p>
					<h2 id="sg-akuvox-products-heading" class="sg-paradox-products__title">
						<?php echo esc_html( (string) $config['title'] ); ?>
					</h2>
					<p class="sg-paradox-products__lead"><?php echo esc_html( (string) $config['lead'] ); ?></p>
					<a class="sg-btn sg-btn--orange sg-paradox-products__cta" href="<?php echo esc_url( (string) $cta['url'] ); ?>">
						<?php echo esc_html( (string) $cta['label'] ); ?>
						<span class="sg-btn__arrow" aria-hidden="true"><?php site_blocks_icon( 'arrow-right', 16 ); ?></span>
					</a>
					<ul class="sg-paradox-products__trust" role="list">
						<?php foreach ( $config['trust'] as $trust ) : ?>
							<li class="sg-paradox-products__trust-item">
								<span class="sg-paradox-products__trust-icon" aria-hidden="true">
									<?php site_blocks_icon( (string) $trust['icon'], 18 ); ?>
								</span>
								<span class="sg-paradox-products__trust-label"><?php echo esc_html( (string) $trust['label'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div class="sg-paradox-products__grid">
					<article class="sg-paradox-product sg-paradox-product--featured">
						<div class="sg-paradox-product__media">
							<?php site_blocks_stage_hero_media( (string) $featured['image'], (string) $featured['alt'], 'sg-paradox-product__img', 'lazy' ); ?>
						</div>
						<div class="sg-paradox-product__body">
							<div class="sg-paradox-product__text">
								<h3 class="sg-paradox-product__title"><?php echo esc_html( (string) $featured['title'] ); ?></h3>
								<p class="sg-paradox-product__tag"><?php echo esc_html( (string) $featured['tag'] ); ?></p>
								<p class="sg-paradox-product__desc"><?php echo esc_html( (string) $featured['description'] ); ?></p>
							</div>
						</div>
					</article>

					<div class="sg-paradox-products__row">
						<?php foreach ( $config['items'] as $product ) : ?>
							<article class="sg-paradox-product">
								<div class="sg-paradox-product__media">
									<?php site_blocks_stage_hero_media( (string) $product['image'], (string) $product['alt'], 'sg-paradox-product__img', 'lazy' ); ?>
								</div>
								<div class="sg-paradox-product__body">
									<div class="sg-paradox-product__text">
										<h3 class="sg-paradox-product__title"><?php echo esc_html( (string) $product['title'] ); ?></h3>
										<p class="sg-paradox-product__tag"><?php echo esc_html( (string) $product['tag'] ); ?></p>
										<p class="sg-paradox-product__desc"><?php echo esc_html( (string) $product['description'] ); ?></p>
									</div>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Property fit (house / strata / commercial).
 */
function site_blocks_akuvox_render_property_fit(): void {
	$config = site_blocks_akuvox_property_fit_config();
	?>
	<section class="sg-band sg-band--blue sg-cctv-segments sg-intercom-segments alignfull" aria-labelledby="sg-akuvox-property-heading">
		<div class="sg-container">
			<header class="sg-ajax-section__header">
				<p class="sg-ajax-section__eyebrow"><?php echo esc_html( (string) $config['eyebrow'] ); ?></p>
				<h2 id="sg-akuvox-property-heading" class="sg-ajax-section__title">
					<?php echo esc_html( (string) $config['title'] ); ?>
				</h2>
				<p class="sg-ajax-monitoring__lead"><?php echo esc_html( (string) $config['lead'] ); ?></p>
			</header>
			<div class="sg-cctv-segments__grid sg-intercom-segments__grid">
				<?php foreach ( $config['panels'] as $panel ) : ?>
					<article class="sg-cctv-segment sg-intercom-segment">
						<div class="sg-cctv-segment__media">
							<?php site_blocks_stage_hero_media( (string) $panel['image'], (string) $panel['alt'], '', 'lazy' ); ?>
						</div>
						<div class="sg-cctv-segment__body">
							<h3 class="sg-cctv-segment__title"><?php echo esc_html( (string) $panel['title'] ); ?></h3>
							<p class="sg-cctv-segment__desc"><?php echo esc_html( (string) $panel['desc'] ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Install steps (visible 4-up; process-flow renderer is disabled site-wide).
 */
function site_blocks_akuvox_render_install(): void {
	$config = site_blocks_akuvox_install_config();
	?>
	<section class="sg-ajax-process alignfull" aria-labelledby="sg-akuvox-process-heading">
		<div class="sg-container">
			<header class="sg-ajax-section__header">
				<p class="sg-ajax-section__eyebrow"><?php echo esc_html( (string) $config['eyebrow'] ); ?></p>
				<h2 id="sg-akuvox-process-heading" class="sg-ajax-section__title">
					<?php echo esc_html( (string) $config['title'] ); ?>
				</h2>
			</header>
			<ul class="sg-ajax-process__list" role="list">
				<?php foreach ( $config['steps'] as $step ) : ?>
					<li class="sg-ajax-process__item">
						<span class="sg-ajax-process__icon" aria-hidden="true">
							<?php site_blocks_icon( (string) $step['icon'], 32 ); ?>
						</span>
						<div class="sg-ajax-process__copy">
							<h3 class="sg-ajax-process__title"><?php echo esc_html( (string) $step['title'] ); ?></h3>
							<p class="sg-ajax-process__desc"><?php echo esc_html( (string) $step['description'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php
}

/**
 * Compare table (doorbell | recable | Akuvox).
 */
function site_blocks_akuvox_render_compare(): void {
	$config = site_blocks_akuvox_compare_config();
	?>
	<section class="sg-ajax-section sg-ajax-compare sg-akuvox-compare alignfull" aria-labelledby="sg-akuvox-compare-heading">
		<div class="sg-container">
			<header class="sg-ajax-section__header">
				<p class="sg-ajax-section__eyebrow"><?php echo esc_html( (string) $config['eyebrow'] ); ?></p>
				<h2 id="sg-akuvox-compare-heading" class="sg-ajax-section__title">
					<?php echo esc_html( (string) $config['title'] ); ?>
				</h2>
			</header>
			<div class="sg-ajax-compare__scroll" tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Video doorbell versus recable versus Akuvox with Safeguard comparison table', 'site-blocks' ); ?>">
				<table class="sg-ajax-compare__table">
					<thead>
						<tr>
							<th class="sg-ajax-compare__corner" scope="col"><span class="sg-sr-only"><?php esc_html_e( 'Feature', 'site-blocks' ); ?></span></th>
							<th class="sg-ajax-compare__head sg-ajax-compare__head--traditional" scope="col"><?php echo esc_html( (string) $config['left_heading'] ); ?></th>
							<th class="sg-ajax-compare__head sg-ajax-compare__head--traditional" scope="col"><?php echo esc_html( (string) $config['mid_heading'] ); ?></th>
							<th class="sg-ajax-compare__head sg-ajax-compare__head--ajax" scope="col"><?php echo esc_html( (string) $config['right_heading'] ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $config['rows'] as $row ) : ?>
							<tr>
								<th class="sg-ajax-compare__label" scope="row"><?php echo esc_html( (string) $row['label'] ); ?></th>
								<td class="sg-ajax-compare__cell"><?php echo esc_html( (string) $row['left'] ); ?></td>
								<td class="sg-ajax-compare__cell"><?php echo esc_html( (string) $row['mid'] ); ?></td>
								<td class="sg-ajax-compare__cell sg-ajax-compare__cell--ajax"><?php echo esc_html( (string) $row['right'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>
	<?php
}

/**
 * FAQ.
 */
function site_blocks_akuvox_render_faq(): void {
	site_blocks_render_faq_section(
		site_blocks_akuvox_faq_items(),
		array(
			'heading_id'     => 'sg-akuvox-faq-heading',
			'heading_before' => __( 'Akuvox ', 'site-blocks' ),
			'heading_accent' => __( 'FAQs.', 'site-blocks' ),
			'id_prefix'      => 'sg-akuvox-faq-',
		)
	);
}

/**
 * Quote CTA.
 */
function site_blocks_akuvox_render_cta(): void {
	site_blocks_render_ajax_quote_cta( site_blocks_akuvox_quote_cta_config() );
}
