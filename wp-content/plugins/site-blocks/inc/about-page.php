<?php
/**
 * About page markup.
 *
 * Hero with the licence, ASIAL and experience facts, then: a considered approach,
 * what we do, how we work, the visible and invisible parts of a system, and the
 * licence and office details.
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
	$email   = (string) $details['email'];

	$offer = array(
		array(
			'icon'  => 'pen-tool',
			'title' => __( 'Design', 'site-blocks' ),
			'text'  => __( 'Security systems planned around the site, its risks and the way it is used.', 'site-blocks' ),
			'tone'  => 'blue',
		),
		array(
			'icon'  => 'hard-hat',
			'title' => __( 'Install', 'site-blocks' ),
			'text'  => __( 'Professional installation of electronic security systems for commercial and residential clients.', 'site-blocks' ),
			'tone'  => 'blue',
		),
		array(
			'icon'  => 'graph',
			'title' => __( 'Integrate', 'site-blocks' ),
			'text'  => __( 'CCTV, alarms, access control, intercoms and related systems working together as one environment.', 'site-blocks' ),
			'tone'  => 'blue',
		),
		array(
			'icon'  => 'wrench',
			'title' => __( 'Maintain And Improve', 'site-blocks' ),
			'text'  => __( 'Support for upgrades, takeovers, repairs and improvements where an existing installation needs attention.', 'site-blocks' ),
			'tone'  => 'orange',
		),
	);

	$steps = array(
		__( 'Understand the environment and what needs protecting', 'site-blocks' ),
		__( 'Select technology that suits the site', 'site-blocks' ),
		__( 'Plan how the parts of the system work together', 'site-blocks' ),
		__( 'Install a system that is practical to run and maintain', 'site-blocks' ),
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
				<h1 id="sg-about-heading">
					<span class="sg-about-hero__title"><?php esc_html_e( 'Safeguard', 'site-blocks' ); ?></span>
					<span class="sg-about-hero__accent"><?php esc_html_e( 'Security Services', 'site-blocks' ); ?></span>
				</h1>
				<p class="sg-about-hero__lead"><?php esc_html_e( 'Electronic security design, installation, maintenance and integration for commercial and residential properties.', 'site-blocks' ); ?></p>
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

		<section class="sg-about-intro" id="approach" aria-labelledby="sg-about-approach">
			<div class="sg-container sg-about-intro__grid">
				<h2 id="sg-about-approach" class="sg-about-title" data-sg-reveal="left"><?php esc_html_e( 'A Considered Approach To Security', 'site-blocks' ); ?></h2>
				<div class="sg-about-prose" data-sg-stagger>
					<p data-sg-reveal><?php esc_html_e( 'We approach security as more than a collection of products. A camera, alarm or access control device only performs well when it is selected properly, installed carefully and integrated around the way a site actually operates.', 'site-blocks' ); ?></p>
					<p data-sg-reveal><?php esc_html_e( 'That is why our focus is on thoughtful system design, practical technology selection and professional installation that supports long-term performance and usability.', 'site-blocks' ); ?></p>
					<p data-sg-reveal><?php esc_html_e( 'Our work spans CCTV, access control, alarms, intercoms and the supporting infrastructure that lets these systems operate reliably as part of a complete site solution.', 'site-blocks' ); ?></p>
				</div>
			</div>
		</section>

		<section class="sg-about-offer" id="what-we-do" aria-labelledby="sg-about-offer-title">
			<div class="sg-container">
				<h2 id="sg-about-offer-title" class="sg-about-title" data-sg-reveal><?php esc_html_e( 'What We Do', 'site-blocks' ); ?></h2>
				<ul class="sg-about-offer__grid" role="list" data-sg-stagger>
					<?php foreach ( $offer as $item ) : ?>
						<li class="sg-about-offer__item" data-sg-reveal>
							<span class="sg-about-tile sg-about-tile--<?php echo esc_attr( $item['tone'] ); ?>" aria-hidden="true"><?php site_blocks_icon( $item['icon'], 24 ); ?></span>
							<h3><?php echo esc_html( $item['title'] ); ?></h3>
							<p><?php echo esc_html( $item['text'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
				<p class="sg-about-offer__note" data-sg-reveal><?php esc_html_e( 'We also provide guarding, patrols, manpower and monitoring where that work is part of the agreed scope.', 'site-blocks' ); ?></p>
			</div>
		</section>

		<section class="sg-about-work" id="how-we-work" aria-labelledby="sg-about-work-title">
			<div class="sg-container sg-about-work__grid">
				<div data-sg-stagger>
					<h2 id="sg-about-work-title" class="sg-about-title" data-sg-reveal><?php esc_html_e( 'How We Work', 'site-blocks' ); ?></h2>
					<p class="sg-about-work__lead" data-sg-reveal><?php esc_html_e( 'We believe a security system should be properly considered before it is installed. That means understanding the environment, selecting suitable technology, planning how the different parts relate to each other, and delivering an installation that is practical to operate and maintain.', 'site-blocks' ); ?></p>
					<p class="sg-about-work__start" data-sg-reveal>
						<strong><?php esc_html_e( 'How a job starts.', 'site-blocks' ); ?></strong>
						<?php esc_html_e( 'Request a quote for the site and what you need. A member of the team checks the details and confirms the scope before work begins.', 'site-blocks' ); ?>
					</p>
				</div>
				<ol class="sg-about-work__steps" data-sg-stagger>
					<?php foreach ( $steps as $index => $step ) : ?>
						<li data-sg-reveal>
							<span class="sg-about-work__num" aria-hidden="true"><?php echo esc_html( (string) ( $index + 1 ) ); ?></span>
							<span><?php echo esc_html( $step ); ?></span>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</section>

		<section class="sg-about-focus" id="our-focus" aria-labelledby="sg-about-focus-title">
			<div class="sg-container sg-about-focus__grid" data-sg-stagger>
				<h2 id="sg-about-focus-title" class="sg-about-title sg-about-title--light" data-sg-reveal><?php esc_html_e( 'The Visible And Invisible Parts Of Security', 'site-blocks' ); ?></h2>
				<div class="sg-about-prose sg-about-prose--light">
					<p data-sg-reveal><?php esc_html_e( 'Our role is to bring both together. That includes the devices people notice, such as cameras and entry hardware, and the infrastructure behind them: recording, networking, control equipment and system configuration. The result is a security environment that is easier to use, maintain and build on over time.', 'site-blocks' ); ?></p>
					<p data-sg-reveal><?php esc_html_e( 'We also take over, repair, upgrade and maintain existing systems, where improving what is there makes more sense than replacing it.', 'site-blocks' ); ?></p>
					<p data-sg-reveal><?php esc_html_e( 'Safeguard is an electronic security specialist rather than a product-led installer. We help clients put the right systems in place and improve what already exists, so that security suits the property, the people using it and the demands of the site.', 'site-blocks' ); ?></p>
				</div>
			</div>
		</section>

		<section class="sg-about-facts" id="licence-and-office" aria-labelledby="sg-about-facts-title">
			<div class="sg-container">
				<h2 id="sg-about-facts-title" class="sg-about-title" data-sg-reveal><?php esc_html_e( 'Licence And Office', 'site-blocks' ); ?></h2>
				<div class="sg-about-facts__grid" data-sg-stagger>
					<article class="sg-about-facts__card" data-sg-reveal>
						<span class="sg-about-tile sg-about-tile--blue" aria-hidden="true"><?php site_blocks_icon( 'badge-check', 24 ); ?></span>
						<h3><?php esc_html_e( 'Licence', 'site-blocks' ); ?></h3>
						<p><?php esc_html_e( 'Safeguard Security Services holds NSW Master Licence #000103619 and is an ASIAL member, with over 20 years of experience.', 'site-blocks' ); ?></p>
					</article>
					<article class="sg-about-facts__card" data-sg-reveal>
						<span class="sg-about-tile sg-about-tile--orange" aria-hidden="true"><?php site_blocks_icon( 'map-pin', 24 ); ?></span>
						<h3><?php esc_html_e( 'Office', 'site-blocks' ); ?></h3>
						<p><?php esc_html_e( '2/2 Stennett Road, Ingleburn NSW 2565. Staffed Monday to Friday, 8 am to 6 pm, and closed on weekends.', 'site-blocks' ); ?></p>
						<p class="sg-about-facts__links">
							<a href="<?php echo esc_url( $phone_h ); ?>"><?php echo esc_html( $phone ); ?></a>
							<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
						</p>
					</article>
				</div>
			</div>
		</section>

		<?php
		site_blocks_render_quote_cta(
			array(
				'heading_id'      => 'sg-about-cta-heading',
				'before'          => __( 'Call ', 'site-blocks' ),
				'accent'          => $phone,
				'after'           => '',
				'sub'             => __( 'Or send a message through the contact form.', 'site-blocks' ),
				'primary_label'   => __( 'Send a message', 'site-blocks' ),
				'primary_url'     => home_url( '/contact/' ),
				'secondary_label' => __( 'Call ', 'site-blocks' ) . $phone,
				'secondary_url'   => $phone_h,
				'section_class'   => '',
				'alignfull'       => false,
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
