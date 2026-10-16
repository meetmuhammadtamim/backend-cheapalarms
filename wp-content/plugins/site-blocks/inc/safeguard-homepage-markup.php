<?php
/**
 * Safeguard homepage HTML markup.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render complete Safeguard homepage.
 */
function site_blocks_render_safeguard_homepage(): void {
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-chrome.php';
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-section-icons.php';
	require_once SITE_BLOCKS_DIR . 'inc/mdi-icons.php';
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-process-flow.php';
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-process-flow-configs.php';
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-details-section.php';
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-why-section.php';
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-coverage-section.php';
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-quality-section.php';
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-connected-section.php';
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-cases-section.php';
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-brands-section.php';
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-process-section.php';

	$hero_house   = site_blocks_asset_url( 'images/hero/house.png' );
	$hero_set     = site_blocks_hero_image_set();
	$logo_mark   = site_blocks_asset_url( 'images/brand/safeguard-logo-mark.png' );
	$ajax_img    = site_blocks_asset_url( 'images/ajax/ajax-products.png' );
	$quote       = esc_url( home_url( '/contact/' ) );
	$design      = esc_url( home_url( '/contact/' ) );
	$ajax_page   = esc_url( home_url( '/brands/ajax-alarm-systems/' ) );

	$service_cards = array(
		array(
			'href'  => '/alarm-systems/',
			'title' => __( 'Alarm Systems', 'site-blocks' ),
			'desc'  => __( 'Advanced intrusion detection with smart alerts.', 'site-blocks' ),
			'icon'  => 'site_blocks_sg_icon_service_alarm',
		),
		array(
			'href'  => '/cctv-security-cameras/',
			'title' => __( 'CCTV & Cameras', 'site-blocks' ),
			'desc'  => __( 'High-definition surveillance you can trust.', 'site-blocks' ),
			'icon'  => 'site_blocks_sg_icon_service_cctv',
		),
		array(
			'href'  => '/intercom-systems/',
			'title' => __( 'Video Intercoms', 'site-blocks' ),
			'desc'  => __( 'See, speak and grant access remotely.', 'site-blocks' ),
			'icon'  => 'site_blocks_sg_icon_service_intercom',
		),
		array(
			'href'  => '/access-control/',
			'title' => __( 'Access Control', 'site-blocks' ),
			'desc'  => __( 'Secure entry for people, vehicles and assets.', 'site-blocks' ),
			'icon'  => 'site_blocks_sg_icon_service_access',
		),
		array(
			'href'  => '/monitoring/',
			'title' => __( 'Monitoring & Response', 'site-blocks' ),
			'desc'  => __( '24/7 monitoring with rapid response when needed.', 'site-blocks' ),
			'icon'  => 'site_blocks_sg_icon_service_monitoring',
		),
		array(
			'href'  => '/preventative-maintenance/',
			'title' => __( 'Preventative Maintenance', 'site-blocks' ),
			'desc'  => __( 'Keep your system reliable with scheduled servicing.', 'site-blocks' ),
			'icon'  => 'site_blocks_sg_icon_service_maintenance',
		),
		array(
			'href'  => '/commercial-security/',
			'title' => __( 'Commercial', 'site-blocks' ),
			'desc'  => __( 'Scalable solutions for offices, retail and facilities.', 'site-blocks' ),
			'icon'  => 'site_blocks_sg_icon_service_commercial',
		),
		array(
			'href'  => '/residential-security/',
			'title' => __( 'Residential', 'site-blocks' ),
			'desc'  => __( 'Smart protection for your property and family.', 'site-blocks' ),
			'icon'  => 'site_blocks_sg_icon_service_residential',
		),
	);

	require_once SITE_BLOCKS_DIR . 'inc/safeguard-faq.php';
	$faq_items = site_blocks_get_safeguard_faq_items();
	?>
<div class="sg-page alignfull">
	<?php site_blocks_render_safeguard_header(); ?>

	<main id="main" class="sg-main">
		<?php // Hero: sits inside the standard site container (.sg-rack-hero--inset). ?>
		<section class="sg-rack-hero sg-rack-hero--inset" aria-labelledby="sg-rack-hero-heading">
			<div class="sg-container">
				<div class="sg-rack-hero__frame">
					<picture class="sg-rack-hero__photo">
						<img
							class="sg-rack-hero__img"
							src="<?php echo esc_url( $hero_set['src'] ); ?>"
							srcset="<?php echo esc_attr( $hero_set['srcset'] ); ?>"
							sizes="<?php echo esc_attr( $hero_set['sizes'] ); ?>"
							alt="<?php esc_attr_e( 'Technician connecting network cables inside an open cabinet.', 'site-blocks' ); ?>"
							width="<?php echo esc_attr( (string) $hero_set['width'] ); ?>"
							height="<?php echo esc_attr( (string) $hero_set['height'] ); ?>"
							loading="eager"
							fetchpriority="high"
							decoding="async"
						/>
					</picture>
					<div class="sg-rack-hero__copy">
						<h1 id="sg-rack-hero-heading" class="sg-rack-hero__title">
							<?php
								printf(
									/* translators: %s: the emphasised phrase, wrapped in markup */
									esc_html__( 'Security systems that work properly from %s', 'site-blocks' ),
									'<span class="sg-rack-hero__focus" data-sg-focus>' . esc_html__( 'Day One', 'site-blocks' ) . '</span>'
								);
								?>
						</h1>
						<p class="sg-rack-hero__lead">
							<?php esc_html_e( 'Safeguard designs, installs, integrates, and supports professional security systems for homes, businesses, and complex sites.', 'site-blocks' ); ?>
						</p>
						<div class="sg-rack-hero__ctas">
							<a class="sg-call-pill" href="tel:1300225276">
								<span class="sg-call-pill__icon" aria-hidden="true"><?php site_blocks_icon( 'phone', 20 ); ?></span>
								<span class="sg-call-pill__text"><?php esc_html_e( 'Call Us Now', 'site-blocks' ); ?></span>
							</a>
							<a class="sg-btn sg-btn--secondary sg-hv__btn-on-dark" href="#sg-cases">
								<?php esc_html_e( 'See our work', 'site-blocks' ); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</section>

		<?php
		site_blocks_render_logo_marquee(
			array(
				'title'      => __( 'Project Portfolio', 'site-blocks' ),
				'source'     => 'portfolio',
				'variant'    => 'light',
				'heading_id' => 'sg-home-portfolio-heading',
				'class'      => 'sg-logo-marquee--home',
			)
		);
		?>

		<?php site_blocks_render_why_section(); ?>

		<?php site_blocks_render_details_section(); ?>

		<?php site_blocks_render_coverage_section(); ?>

		<?php site_blocks_render_quality_section(); ?>

			<?php site_blocks_render_connected_section(); ?>

			<?php site_blocks_render_brands_section(); ?>

			<?php site_blocks_render_process_section(); ?>

			<?php site_blocks_render_cases_section(); ?>

		<?php site_blocks_render_blog_section(); ?>
	</main>

	<?php
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-footer.php';
	site_blocks_render_safeguard_footer();
	site_blocks_render_safeguard_mobile_bar();
	?>
</div>
	<?php
}

function site_blocks_sg_icon_portal_check(): void {
	site_blocks_icon( 'circle-check', 22, 'sg-icon--filled-check' );
}

function site_blocks_sg_icon_feature_shield(): void {
	site_blocks_icon( 'shield-check', 18 );
}

function site_blocks_sg_icon_feature_target(): void {
	site_blocks_icon( 'target', 18 );
}

function site_blocks_sg_icon_feature_gear(): void {
	site_blocks_icon( 'settings', 18 );
}
