<?php
/**
 * About page markup.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the About page.
 */
function site_blocks_render_about_page(): void {
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-chrome.php';
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-footer.php';
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-sections.php';
	require_once SITE_BLOCKS_DIR . 'inc/safeguard-contact-details.php';
	require_once SITE_BLOCKS_DIR . 'inc/mdi-icons.php';

	$details = site_blocks_get_safeguard_contact_details();
	$phone   = (string) $details['phone'];
	$phone_h = 'tel:' . preg_replace( '/[^\d+]/', '', $phone );

	$sections = array(
		array(
			'id'    => 'who-we-are',
			'title' => __( 'Who We Are', 'site-blocks' ),
			'body'  => array(
				__( 'Safeguard Security Services is an installation partner to a security integrator. We design, install, integrate and maintain electronic security systems.', 'site-blocks' ),
			),
		),
		array(
			'id'    => 'what-we-do',
			'title' => __( 'What We Do', 'site-blocks' ),
			'body'  => array(
				__( 'We install and integrate CCTV, alarms, access control and intercom systems. We also provide guarding, patrols, manpower and monitoring where that work is part of the agreed scope.', 'site-blocks' ),
			),
		),
		array(
			'id'    => 'office',
			'title' => __( 'Office', 'site-blocks' ),
			'body'  => array(
				__( 'The office is at 2/2 Stennett Road, Ingleburn NSW 2565. The office is staffed Monday to Friday, 8 am to 6 pm, and closed on weekends.', 'site-blocks' ),
			),
		),
		array(
			'id'    => 'licence',
			'title' => __( 'Licence', 'site-blocks' ),
			'body'  => array(
				__( 'Safeguard Security Services holds NSW Master Licence #000103619 and is an ASIAL member. The company has over 20 years of experience.', 'site-blocks' ),
			),
		),
		array(
			'id'    => 'how-a-job-starts',
			'title' => __( 'How A Job Starts', 'site-blocks' ),
			'body'  => array(
				__( 'Request a quote for the site and what you need. A member of the team checks the details and confirms the scope before work begins.', 'site-blocks' ),
			),
		),
	);
	?>
<div class="sg-page alignfull">
	<?php site_blocks_render_safeguard_header(); ?>
	<main id="main" class="sg-main">
		<section class="sg-about-hero" aria-labelledby="sg-about-heading">
			<img
				class="sg-about-hero__photo"
				src="<?php echo esc_url( site_blocks_asset_url( 'images/about/cctv-glass-offices.webp' ) ); ?>"
				alt="<?php esc_attr_e( 'Dome security camera under a building canopy, overlooking a glass office.', 'site-blocks' ); ?>"
				width="1672"
				height="941"
				fetchpriority="high"
				decoding="async"
			>
			<div class="sg-container sg-about-hero__inner">
				<p class="sg-about-hero__eyebrow"><?php esc_html_e( 'Over 20 years of experience', 'site-blocks' ); ?></p>
				<h1 id="sg-about-heading">
					<span class="sg-about-hero__title"><?php esc_html_e( 'Safeguard', 'site-blocks' ); ?></span>
					<span class="sg-about-hero__accent"><?php esc_html_e( 'Security Services', 'site-blocks' ); ?></span>
				</h1>
				<p class="sg-about-hero__lead"><?php esc_html_e( 'Safeguard Security Services installs and integrates electronic security systems for commercial and residential properties.', 'site-blocks' ); ?></p>
				<div class="sg-about-hero__actions">
					<a class="sg-btn sg-btn--orange" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
						<?php esc_html_e( 'Contact the team', 'site-blocks' ); ?>
						<?php site_blocks_icon( 'arrow-right' ); ?>
					</a>
					<a class="sg-btn sg-btn--secondary" href="<?php echo esc_url( $phone_h ); ?>">
						<?php site_blocks_icon( 'phone' ); ?>
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: phone number */
								__( 'Call %s', 'site-blocks' ),
								$phone
							)
						);
						?>
					</a>
				</div>
				<ul class="sg-about-hero__proof">
					<li>
						<span class="sg-about-hero__proof-icon"><?php site_blocks_icon( 'file-text' ); ?></span>
						<span>
							<span class="sg-about-hero__proof-label"><?php esc_html_e( 'Licence', 'site-blocks' ); ?></span>
							<span class="sg-about-hero__proof-value"><?php esc_html_e( 'Master Licence #000103619', 'site-blocks' ); ?></span>
						</span>
					</li>
					<li>
						<span class="sg-about-hero__proof-icon"><?php site_blocks_icon( 'shield-check' ); ?></span>
						<span>
							<span class="sg-about-hero__proof-label"><?php esc_html_e( 'Member', 'site-blocks' ); ?></span>
							<span class="sg-about-hero__proof-value"><?php esc_html_e( 'ASIAL', 'site-blocks' ); ?></span>
						</span>
					</li>
					<li>
						<span class="sg-about-hero__proof-icon"><?php site_blocks_icon( 'users' ); ?></span>
						<span>
							<span class="sg-about-hero__proof-label"><?php esc_html_e( 'Experience', 'site-blocks' ); ?></span>
							<span class="sg-about-hero__proof-value"><?php esc_html_e( '20+ years', 'site-blocks' ); ?></span>
						</span>
					</li>
				</ul>
			</div>
		</section>
		<section class="sg-band sg-band--white" aria-label="<?php esc_attr_e( 'About Safeguard', 'site-blocks' ); ?>">
			<div class="sg-container sg-about">
				<?php foreach ( $sections as $section ) : ?>
					<article class="sg-about__section" id="<?php echo esc_attr( $section['id'] ); ?>">
						<h2><?php echo esc_html( $section['title'] ); ?></h2>
						<?php foreach ( $section['body'] as $paragraph ) : ?>
							<p><?php echo esc_html( $paragraph ); ?></p>
						<?php endforeach; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
		site_blocks_render_quote_cta(
			array(
				'heading_id'     => 'sg-about-cta-heading',
				'before'         => __( 'Call ', 'site-blocks' ),
				'accent'         => $phone,
				'after'          => '',
				'sub'            => __( 'Or send a message through the contact form.', 'site-blocks' ),
				'primary_label'  => __( 'Send a message', 'site-blocks' ),
				'primary_url'    => home_url( '/contact/' ),
				'secondary_label'=> __( 'Call ', 'site-blocks' ) . $phone,
				'secondary_url'  => $phone_h,
				'section_class'  => '',
				'alignfull'      => false,
			)
		);
		?>
	</main>
	<?php
	site_blocks_render_safeguard_footer();
	site_blocks_render_safeguard_mobile_bar();
	?>
</div>
	<?php
}
