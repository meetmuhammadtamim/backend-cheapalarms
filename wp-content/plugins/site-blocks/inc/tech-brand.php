<?php
/**
 * Milesight, NX Witness, and Hanwha Vision pages.
 *
 * Layout follows the brand-page concept. Colours are Safeguard's.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once SITE_BLOCKS_DIR . 'inc/tech-brand-visuals.php';

/**
 * Every brand page. Slugs stay stable; public URLs are /brands/{slug}/.
 *
 * @return array<string, array{label: string, summary: string, logo?: string, title?: string}>
 */
function site_blocks_brand_catalogue(): array {
	return array(
		'ajax-alarm-systems' => array(
			'label'   => 'Ajax',
			'title'   => __( 'Ajax Alarm Systems', 'site-blocks' ),
			'summary' => __( 'Wireless alarm systems.', 'site-blocks' ),
			'logo'    => 'images/brands/ajax.webp',
		),
		'paradox-m25'        => array(
			'label'   => 'Paradox',
			'title'   => __( 'Paradox M25', 'site-blocks' ),
			'summary' => __( 'Paradox M25 alarm panel.', 'site-blocks' ),
			'logo'    => 'images/brands/paradox.webp',
		),
		'milesight'          => array(
			'label'   => 'Milesight',
			'title'   => __( 'Milesight', 'site-blocks' ),
			'summary' => __( 'Network cameras and IoT sensing.', 'site-blocks' ),
			'logo'    => 'images/brands/milesight.png',
		),
		'nx-witness'         => array(
			'label'   => 'NX Witness',
			'title'   => __( 'NX Witness', 'site-blocks' ),
			'summary' => __( 'Video management software.', 'site-blocks' ),
			'logo'    => 'images/brands/nx-witness.png',
		),
		'hanwha-vision'      => array(
			'label'   => 'Hanwha Vision',
			'title'   => __( 'Hanwha Vision', 'site-blocks' ),
			'summary' => __( 'Professional surveillance cameras.', 'site-blocks' ),
			'logo'    => 'images/brands/hanwha-vision.png',
		),
		'akuvox'             => array(
			'label'   => 'Akuvox',
			'title'   => __( 'Akuvox', 'site-blocks' ),
			'summary' => __( 'IP intercoms.', 'site-blocks' ),
			'logo'    => 'images/brands/akuvox.webp',
		),
	);
}

/**
 * Public path for one brand, including trailing slash.
 */
function site_blocks_brand_path( string $slug ): string {
	return '/brands/' . trim( $slug, '/' ) . '/';
}

/**
 * Page titles for the three technology brand layouts, keyed by slug.
 *
 * @return array<string, array{title: string}>
 */
function site_blocks_tech_brand_pages(): array {
	$pages = array();
	foreach ( array( 'milesight', 'nx-witness', 'hanwha-vision' ) as $slug ) {
		$brand = site_blocks_brand_catalogue()[ $slug ];
		$pages[ $slug ] = array(
			'title' => (string) $brand['title'],
		);
	}

	return $pages;
}

/**
 * Find a brand page at /brands/{slug}/ or at the old root URL.
 */
function site_blocks_find_brand_page( string $slug ): ?WP_Post {
	$child = get_page_by_path( 'brands/' . $slug );
	if ( $child instanceof WP_Post ) {
		return $child;
	}

	$root = get_page_by_path( $slug );
	if ( $root instanceof WP_Post && 'page' === $root->post_type ) {
		return $root;
	}

	return null;
}

/**
 * Whether the current request is one of the three technology brand pages.
 */
function site_blocks_is_tech_brand_page(): bool {
	return is_page( array( 'milesight', 'nx-witness', 'hanwha-vision' ) );
}

/**
 * @return array{quote: string, phone: string}
 */
function site_blocks_tech_brand_links(): array {
	return array(
		'quote' => home_url( '/contact/' ),
		'phone' => 'tel:1300225276',
	);
}

/**
 * Quote button plus a second action.
 */
function site_blocks_tech_brand_actions( string $second_label, string $second_url, bool $on_dark ): void {
	$links       = site_blocks_tech_brand_links();
	$second_class = $on_dark ? 'sg-btn sg-btn--ghost' : 'sg-btn sg-btn--secondary';
	?>
	<div class="sg-tech__actions">
		<a class="sg-btn sg-btn--orange" href="<?php echo esc_url( $links['quote'] ); ?>"><?php esc_html_e( 'Request a quote', 'site-blocks' ); ?></a>
		<a class="<?php echo esc_attr( $second_class ); ?>" href="<?php echo esc_url( $second_url ); ?>"><?php echo esc_html( $second_label ); ?></a>
	</div>
	<?php
}

/**
 * Enquiry band.
 *
 * @param string $heading_id Heading id.
 */
function site_blocks_tech_brand_enquiry( string $heading_id ): void {
	$links = site_blocks_tech_brand_links();
	site_blocks_render_quote_cta(
		array(
			'heading_id'      => $heading_id,
			'before'          => __( 'Planning or reviewing a ', 'site-blocks' ),
			'accent'          => __( 'surveillance system?', 'site-blocks' ),
			'sub'             => __( 'Tell us about the site and what it needs to do.', 'site-blocks' ),
			'primary_url'     => $links['quote'],
			'secondary_url'   => $links['phone'],
			'section_class'   => 'sg-tech__enquiry',
			'alignfull'       => false,
		)
	);
}

/**
 * Shared Safeguard approach closer: statement, shield, and a short list.
 *
 * @param array{id: string, heading: string, copy: string, list_title: string, items: array<int, string>} $args Section copy.
 */
function site_blocks_tech_brand_approach( array $args ): void {
	$links = site_blocks_tech_brand_links();
	$id    = (string) ( $args['id'] ?? '' );
	$items = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : array();
	?>
	<section class="sg-tech__band sg-tech__light" aria-labelledby="<?php echo esc_attr( $id ); ?>">
		<div class="sg-tech__wrap sg-tech__approach">
			<div class="sg-tech__approach-copy">
				<p class="sg-tech__eyebrow"><?php esc_html_e( 'The Safeguard approach', 'site-blocks' ); ?></p>
				<h2 id="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( (string) ( $args['heading'] ?? '' ) ); ?></h2>
				<p class="sg-tech__copy"><?php echo esc_html( (string) ( $args['copy'] ?? '' ) ); ?></p>
				<div class="sg-tech__actions">
					<a class="sg-btn sg-btn--navy" href="<?php echo esc_url( $links['quote'] ); ?>"><?php esc_html_e( 'Get in touch', 'site-blocks' ); ?></a>
				</div>
			</div>
			<img
				class="sg-tech__approach-mark"
				src="<?php echo esc_url( site_blocks_asset_url( 'images/brand/safeguard-logo-mark.png' ) ); ?>"
				width="512"
				height="512"
				alt="<?php esc_attr_e( 'Safeguard', 'site-blocks' ); ?>"
				decoding="async"
			/>
			<div class="sg-tech__approach-list">
				<p class="sg-tech__approach-label"><?php echo esc_html( (string) ( $args['list_title'] ?? '' ) ); ?></p>
				<ul>
					<?php foreach ( $items as $item ) : ?>
						<li><?php echo esc_html( (string) $item ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Inline icon for a brand rail card. Names are limited to this set.
 *
 * @param string $name Icon name.
 */
function site_blocks_tech_brand_rail_icon( string $name ): void {
	// Rail card names => icon keys in inc/mdi-icons.php.
	$keys = array(
		'camera'   => 'cctv',
		'building' => 'building',
		'nodes'    => 'graph',
		'layers'   => 'layers',
		'grid'     => 'layout-grid',
		'swap'     => 'swap',
		'lens'     => 'lens',
		'pulse'    => 'pulse',
		'shield'   => 'shield',
		'remote'   => 'monitor',
	);

	site_blocks_icon( $keys[ $name ] ?? 'cctv' );
}

/**
 * Heading on the left, a short row of cards on the right.
 *
 * @param array $args id, heading, copy, cards.
 */
function site_blocks_tech_brand_rail( array $args ): void {
	$id    = (string) ( $args['id'] ?? '' );
	$cards = isset( $args['cards'] ) && is_array( $args['cards'] ) ? $args['cards'] : array();
	?>
	<section class="sg-tech__band sg-tech__light" aria-labelledby="<?php echo esc_attr( $id ); ?>">
		<div class="sg-tech__wrap sg-tech__rail">
			<div class="sg-tech__rail-copy">
				<h2 id="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( (string) ( $args['heading'] ?? '' ) ); ?></h2>
				<p class="sg-tech__copy"><?php echo esc_html( (string) ( $args['copy'] ?? '' ) ); ?></p>
			</div>
			<div class="sg-tech__rail-stage" data-sg-rail>
				<button type="button" class="sg-tech__rail-nav sg-tech__rail-nav--prev" aria-label="<?php esc_attr_e( 'Previous cards', 'site-blocks' ); ?>">
					<?php site_blocks_icon( 'chevron-left' ); ?>
				</button>
				<div class="sg-tech__rail-view">
					<ul class="sg-tech__rail-track">
						<?php foreach ( $cards as $card ) : ?>
							<li class="sg-tech__rail-card">
								<span class="sg-tech__rail-icon"><?php site_blocks_tech_brand_rail_icon( (string) ( $card['icon'] ?? 'camera' ) ); ?></span>
								<h3><?php echo esc_html( (string) ( $card['title'] ?? '' ) ); ?></h3>
								<p><?php echo esc_html( (string) ( $card['text'] ?? '' ) ); ?></p>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<button type="button" class="sg-tech__rail-nav sg-tech__rail-nav--next" aria-label="<?php esc_attr_e( 'Next cards', 'site-blocks' ); ?>">
					<?php site_blocks_icon( 'chevron-right' ); ?>
				</button>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Render one technology brand page.
 */
function site_blocks_render_tech_brand( string $slug ): void {
	if ( ! isset( site_blocks_tech_brand_pages()[ $slug ] ) ) {
		return;
	}

	echo '<div class="sg-tech">';
	if ( 'nx-witness' === $slug ) {
		site_blocks_render_tech_brand_nx();
	} elseif ( 'hanwha-vision' === $slug ) {
		site_blocks_render_tech_brand_hanwha();
	} else {
		site_blocks_render_tech_brand_milesight();
	}
	echo '</div>';
}

/**
 * Milesight page.
 */
function site_blocks_render_tech_brand_milesight(): void {
	$links = site_blocks_tech_brand_links();
	?>
	<section class="sg-tech__band sg-tech__hero sg-tech__hero--split" aria-labelledby="sg-ms-hero">
		<div class="sg-tech__wrap sg-ms-hero">
			<div class="sg-ms-hero__copy">
				<img
					class="sg-ms-hero__logo"
					src="<?php echo esc_url( site_blocks_asset_url( 'images/brands/milesight.png' ) ); ?>"
					width="87"
					height="30"
					alt="<?php esc_attr_e( 'Milesight', 'site-blocks' ); ?>"
				/>
				<h1 id="sg-ms-hero"><?php esc_html_e( 'Intelligent', 'site-blocks' ); ?><br><?php esc_html_e( 'Surveillance, Built', 'site-blocks' ); ?><br><?php esc_html_e( 'For Real Environments.', 'site-blocks' ); ?></h1>
				<p class="sg-tech__lede"><?php esc_html_e( 'Milesight professional camera systems combine high-quality imaging, edge-based AI analytics, and dependable performance for commercial, industrial, and residential security environments.', 'site-blocks' ); ?></p>
				<div class="sg-tech__actions">
					<a class="sg-btn sg-btn--navy" href="<?php echo esc_url( $links['quote'] ); ?>"><?php esc_html_e( 'Talk to Safeguard', 'site-blocks' ); ?></a>
				</div>
				<ul class="sg-ms-hero__cards">
					<li>
						<span class="sg-ms-hero__icon" aria-hidden="true"><?php site_blocks_icon( 'cpu' ); ?></span>
						<p class="sg-ms-hero__card-title"><?php esc_html_e( 'AI-Enabled', 'site-blocks' ); ?></p>
						<p><?php esc_html_e( 'Intelligent detection at the edge', 'site-blocks' ); ?></p>
					</li>
					<li>
						<span class="sg-ms-hero__icon" aria-hidden="true"><?php site_blocks_icon( 'camera' ); ?></span>
						<p class="sg-ms-hero__card-title"><?php esc_html_e( 'Professional Range', 'site-blocks' ); ?></p>
						<p><?php esc_html_e( 'Dome, turret, bullet, PTZ and specialist cameras', 'site-blocks' ); ?></p>
					</li>
					<li>
						<span class="sg-ms-hero__icon" aria-hidden="true"><?php site_blocks_icon( 'graph' ); ?></span>
						<p class="sg-ms-hero__card-title"><?php esc_html_e( 'Open Integration', 'site-blocks' ); ?></p>
						<p><?php esc_html_e( 'Designed to work within connected security systems', 'site-blocks' ); ?></p>
					</li>
				</ul>
			</div>
			<figure class="sg-ms-hero__visual">
				<?php site_blocks_tech_brand_visual_milesight(); ?>
			</figure>
		</div>
	</section>

	<?php
	site_blocks_tech_brand_rail(
		array(
			'id'      => 'sg-ms-intro',
			'heading' => __( 'Intelligent Visibility For The Modern Environment', 'site-blocks' ),
			'copy'    => __( 'Milesight develops professional security and IoT technologies that extend beyond passive video recording. Its camera platforms support high-quality imaging, low-light performance, intelligent detection, and practical edge analytics, while its broader sensing ecosystem can contribute to a more complete understanding of buildings, sites, and operations.', 'site-blocks' ),
			'cards'   => array(
				array(
					'icon'  => 'camera',
					'title' => __( 'Intelligent Video', 'site-blocks' ),
					'text'  => __( 'Edge-based analytics can help identify and classify activity, people, and vehicles closer to the point of capture.', 'site-blocks' ),
				),
				array(
					'icon'  => 'building',
					'title' => __( 'Designed For Demanding Environments', 'site-blocks' ),
					'text'  => __( 'Professional camera ranges are built for dependable operation across indoor, outdoor, commercial, and infrastructure settings.', 'site-blocks' ),
				),
				array(
					'icon'  => 'nodes',
					'title' => __( 'Connected Systems', 'site-blocks' ),
					'text'  => __( 'Open network standards and integration pathways allow Milesight hardware to form part of a broader security or building-technology environment.', 'site-blocks' ),
				),
				array(
					'icon'  => 'shield',
					'title' => __( 'Compliance-Ready Options', 'site-blocks' ),
					'text'  => __( 'Selected camera families include NDAA-compliant options for projects with stricter procurement requirements.', 'site-blocks' ),
				),
			),
		)
	);
	?>

	<section class="sg-tech__band sg-tech__dark" id="sg-ms-system" aria-labelledby="sg-ms-sys">
		<div class="sg-tech__wrap">
			<div class="sg-tech__sys-head">
				<div>
					<p class="sg-tech__eyebrow"><?php esc_html_e( 'System role', 'site-blocks' ); ?></p>
					<h2 id="sg-ms-sys"><?php esc_html_e( 'A Capable Layer Within A Complete Security System', 'site-blocks' ); ?></h2>
				</div>
				<p class="sg-tech__copy"><?php esc_html_e( 'Milesight can operate as the capture and analytics layer within a professionally designed surveillance system. Depending on the site, its hardware may be used with local recording equipment or integrated with a wider video-management platform. The right configuration depends on the environment, the required coverage, and how the client wants to manage and respond to events.', 'site-blocks' ); ?></p>
			</div>
			<ol class="sg-tech__flow">
				<li class="sg-tech__node sg-tech__node--focus">
					<span class="sg-tech__step">01</span>
					<h3><?php esc_html_e( 'Cameras And Sensors', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Imaging, edge analytics and connected sensing at the point of capture.', 'site-blocks' ); ?></p>
					<span class="sg-tech__tag"><?php esc_html_e( 'Milesight layer', 'site-blocks' ); ?></span>
				</li>
				<li class="sg-tech__node">
					<span class="sg-tech__step">02</span>
					<h3><?php esc_html_e( 'Network And Recording', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Network infrastructure and local recording equipment, sized to the site.', 'site-blocks' ); ?></p>
				</li>
				<li class="sg-tech__node">
					<span class="sg-tech__step">03</span>
					<h3><?php esc_html_e( 'Video Management And Response', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'How the client views, manages and responds to events.', 'site-blocks' ); ?></p>
				</li>
			</ol>
		</div>
	</section>

	<?php
	site_blocks_tech_brand_approach(
		array(
			'id'         => 'sg-ms-close',
			'heading'    => __( 'Specified Around The Site, Not Just The Product', 'site-blocks' ),
			'copy'       => __( 'Safeguard approaches surveillance as a complete system. We consider coverage, environmental conditions, network infrastructure, recording requirements, and future integration before recommending the technology that best fits the site.', 'site-blocks' ),
			'list_title' => __( 'Considered before recommending', 'site-blocks' ),
			'items'      => array(
				__( 'Coverage', 'site-blocks' ),
				__( 'Environmental conditions', 'site-blocks' ),
				__( 'Network infrastructure', 'site-blocks' ),
				__( 'Recording requirements', 'site-blocks' ),
				__( 'Future integration', 'site-blocks' ),
			),
		)
	);
	?>
	<?php
	site_blocks_tech_brand_enquiry( 'sg-ms-enq' );
}

/**
 * NX Witness page.
 */
function site_blocks_render_tech_brand_nx(): void {
	?>
	<section class="sg-tech__band sg-tech__hero sg-tech__hero--stack sg-tech__hero--fit" aria-labelledby="sg-nx-hero">
		<div class="sg-tech__wrap">
			<div class="sg-tech__head">
				<img
					class="sg-tech__mark"
					src="<?php echo esc_url( site_blocks_asset_url( 'images/brands/nx-witness.png' ) ); ?>"
					alt="<?php esc_attr_e( 'NX Witness', 'site-blocks' ); ?>"
					width="40"
					height="40"
				/>
				<h1 id="sg-nx-hero"><?php esc_html_e( 'The Intelligence Behind The Video System', 'site-blocks' ); ?></h1>
				<p class="sg-tech__lede"><?php esc_html_e( 'Cameras, recording, and events in one view.', 'site-blocks' ); ?></p>
				<?php site_blocks_tech_brand_actions( __( 'See the software', 'site-blocks' ), '#sg-nx-system', true ); ?>
			</div>
			<div class="sg-tech__visual"><?php site_blocks_tech_brand_visual_nx(); ?></div>
		</div>
	</section>

	<?php
	site_blocks_tech_brand_rail(
		array(
			'id'      => 'sg-nx-intro',
			'heading' => __( 'One Clear View Of A Connected Security Environment', 'site-blocks' ),
			'copy'    => __( 'Network Optix develops software that helps organisations discover, manage, view, record, and work with video from a wide range of networked devices. Rather than replacing the physical camera layer, NX Witness provides the software environment that helps the wider system operate as one.', 'site-blocks' ),
			'cards'   => array(
				array(
					'icon'  => 'layers',
					'title' => __( 'Flexible Architecture', 'site-blocks' ),
					'text'  => __( 'Designed to work across different hardware environments and support systems that need to evolve over time.', 'site-blocks' ),
				),
				array(
					'icon'  => 'grid',
					'title' => __( 'Clear Video Operations', 'site-blocks' ),
					'text'  => __( 'Give authorised users a practical way to view live footage, review recordings, investigate events, and manage connected sites.', 'site-blocks' ),
				),
				array(
					'icon'  => 'swap',
					'title' => __( 'Intelligent Integration', 'site-blocks' ),
					'text'  => __( 'Logic-based events and integrations can connect video activity with other security and building systems.', 'site-blocks' ),
				),
				array(
					'icon'  => 'remote',
					'title' => __( 'Remote Access', 'site-blocks' ),
					'text'  => __( 'Built-in remote-access capabilities support viewing and connecting to video systems without relying on complex network configuration.', 'site-blocks' ),
				),
			),
		)
	);
	?>

	<?php
	$nx_story = array(
		array(
			'title' => __( 'Universal Device Compatibility', 'site-blocks' ),
			'body'  => __( 'NX Witness VMS excels in device integration, auto-discovering and connecting to 99% of IP cameras on the market today and 790+ IoT device manufacturers, ensuring a versatile and scalable surveillance ecosystem that adapts to your security landscape.', 'site-blocks' ),
			'image' => 'images/brands/nx-feature-device.webp',
			'alt'   => __( 'Dome camera, the kind of device NX Witness connects to.', 'site-blocks' ),
		),
		array(
			'title' => __( 'Cloud Connectivity', 'site-blocks' ),
			'body'  => __( 'Use the power of NX Cloud to access, manage, and control your surveillance system from anywhere. With cloud-based flexibility, ensure secure, remote connectivity for real-time surveillance without geographical limitations.', 'site-blocks' ),
			'image' => 'images/brands/nx-feature-cloud.webp',
			'alt'   => __( 'Connected locks across a map, illustrating NX Cloud connectivity.', 'site-blocks' ),
		),
		array(
			'title' => __( 'Streamlined Video Operations', 'site-blocks' ),
			'body'  => __( 'At the core of NX Witness VMS is the powerful server application, designed for efficient stream and storage management, with high resilience through advanced failover and redundancy mechanisms, offering a range of storage solutions to fit all needs.', 'site-blocks' ),
			'image' => 'images/brands/nx-feature-video.webp',
			'alt'   => __( 'NX Witness client searching recorded video for vehicles.', 'site-blocks' ),
		),
	);
	?>
	<section class="sg-tech__band sg-tech__dark sg-nx-story" aria-label="<?php esc_attr_e( 'How NX Witness works', 'site-blocks' ); ?>">
		<div class="sg-tech__wrap sg-nx-story__layout">
			<div class="sg-nx-story__steps">
				<?php foreach ( $nx_story as $nx_index => $nx_step ) : ?>
					<article class="sg-nx-story__step" data-step="<?php echo esc_attr( (string) $nx_index ); ?>">
						<h2><?php echo esc_html( $nx_step['title'] ); ?></h2>
						<p class="sg-tech__copy"><?php echo esc_html( $nx_step['body'] ); ?></p>
						<img
							class="sg-nx-story__inline"
							src="<?php echo esc_url( site_blocks_asset_url( $nx_step['image'] ) ); ?>"
							width="1986"
							height="1534"
							alt="<?php echo esc_attr( $nx_step['alt'] ); ?>"
							decoding="async"
							<?php echo 0 === $nx_index ? '' : 'loading="lazy"'; ?>
						/>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="sg-nx-story__stage">
				<div class="sg-nx-story__frame">
					<?php foreach ( $nx_story as $nx_index => $nx_step ) : ?>
						<img
							<?php echo 0 === $nx_index ? 'class="is-active"' : 'aria-hidden="true"'; ?>
							src="<?php echo esc_url( site_blocks_asset_url( $nx_step['image'] ) ); ?>"
							width="1986"
							height="1534"
							alt="<?php echo esc_attr( $nx_step['alt'] ); ?>"
							decoding="async"
						/>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>

	<?php
	site_blocks_tech_brand_approach(
		array(
			'id'         => 'sg-nx-close',
			'heading'    => __( 'Built Around How Your Organisation Needs To Work', 'site-blocks' ),
			'copy'       => __( 'Safeguard helps translate video-management technology into a practical operating system for the site. We consider camera compatibility, server requirements, user access, recording needs, and future expansion so the platform supports the way the organisation actually operates.', 'site-blocks' ),
			'list_title' => __( 'Considered before recommending', 'site-blocks' ),
			'items'      => array(
				__( 'Camera compatibility', 'site-blocks' ),
				__( 'Server requirements', 'site-blocks' ),
				__( 'User access', 'site-blocks' ),
				__( 'Recording needs', 'site-blocks' ),
				__( 'Future expansion', 'site-blocks' ),
			),
		)
	);
	?>

	<section class="sg-tech__band sg-tech__dark" id="sg-nx-system" aria-labelledby="sg-nx-sys">
		<div class="sg-tech__wrap sg-tech__split">
			<div>
				<p class="sg-tech__eyebrow"><?php esc_html_e( 'System role', 'site-blocks' ); ?></p>
				<h2 id="sg-nx-sys"><?php esc_html_e( 'The Software Layer Connecting The System', 'site-blocks' ); ?></h2>
				<p class="sg-tech__copy"><?php esc_html_e( 'In a complete surveillance design, NX Witness sits above the physical camera and recording infrastructure. It can provide the central interface for systems using cameras from brands such as Milesight or Hanwha Vision, while allowing the overall solution to be configured around the site\'s operational and integration requirements.', 'site-blocks' ); ?></p>
			</div>
			<ol class="sg-tech__stack">
				<li class="sg-tech__layer"><h3><small><?php esc_html_e( 'Layer 1', 'site-blocks' ); ?></small><?php esc_html_e( 'Camera Hardware', 'site-blocks' ); ?></h3><ul class="sg-tech__chips"><li><?php esc_html_e( 'Network cameras', 'site-blocks' ); ?></li><li><?php esc_html_e( 'Edge devices', 'site-blocks' ); ?></li></ul></li>
				<li class="sg-tech__layer"><h3><small><?php esc_html_e( 'Layer 2', 'site-blocks' ); ?></small><?php esc_html_e( 'Network And Server', 'site-blocks' ); ?></h3><ul class="sg-tech__chips"><li><?php esc_html_e( 'Network', 'site-blocks' ); ?></li><li><?php esc_html_e( 'Recording servers', 'site-blocks' ); ?></li></ul></li>
				<li class="sg-tech__layer sg-tech__layer--focus"><h3><small><?php esc_html_e( 'Layer 3', 'site-blocks' ); ?></small><?php esc_html_e( 'NX Witness', 'site-blocks' ); ?></h3><ul class="sg-tech__chips"><li><?php esc_html_e( 'Discover', 'site-blocks' ); ?></li><li><?php esc_html_e( 'View', 'site-blocks' ); ?></li><li><?php esc_html_e( 'Record', 'site-blocks' ); ?></li></ul></li>
				<li class="sg-tech__layer"><h3><small><?php esc_html_e( 'Layer 4', 'site-blocks' ); ?></small><?php esc_html_e( 'Users, Events, And Connected Systems', 'site-blocks' ); ?></h3><ul class="sg-tech__chips"><li><?php esc_html_e( 'Users', 'site-blocks' ); ?></li><li><?php esc_html_e( 'Events', 'site-blocks' ); ?></li><li><?php esc_html_e( 'Integrations', 'site-blocks' ); ?></li></ul></li>
			</ol>
		</div>
	</section>
	<?php
	site_blocks_tech_brand_enquiry( 'sg-nx-enq' );
}

/**
 * Hanwha Vision page.
 */
function site_blocks_render_tech_brand_hanwha(): void {
	$links = site_blocks_tech_brand_links();
	?>
	<section class="sg-tech__band sg-tech__hero sg-tech__hero--product" aria-labelledby="sg-hw-hero">
		<div class="sg-tech__wrap sg-tech__product">
			<div class="sg-tech__product-copy">
				<h1 id="sg-hw-hero"><?php esc_html_e( 'Confidence', 'site-blocks' ); ?><br><?php esc_html_e( 'Built Into', 'site-blocks' ); ?><br><span class="sg-tech__hl"><?php esc_html_e( 'Every Frame', 'site-blocks' ); ?></span></h1>
				<p class="sg-tech__lede"><?php esc_html_e( 'Hanwha Vision develops professional video-surveillance technologies that combine advanced imaging, intelligent analytics, and security-conscious engineering for demanding environments.', 'site-blocks' ); ?></p>
				<div class="sg-tech__actions">
					<a class="sg-btn sg-btn--navy" href="<?php echo esc_url( $links['quote'] ); ?>"><?php esc_html_e( 'Request a quote', 'site-blocks' ); ?></a>
				</div>
			</div>
			<div class="sg-tech__product-stage">
				<?php site_blocks_tech_brand_visual_hanwha(); ?>
			</div>
			<div class="sg-tech__points">
				<details open>
					<summary><?php esc_html_e( 'Advanced imaging', 'site-blocks' ); ?></summary>
					<p><?php esc_html_e( 'Clear, useful video across changing light, distance, and conditions.', 'site-blocks' ); ?></p>
				</details>
				<details>
					<summary><?php esc_html_e( 'Intelligent analytics', 'site-blocks' ); ?></summary>
					<p><?php esc_html_e( 'Edge-based intelligence can help distinguish relevant activity and make recorded video more useful for investigation and operations.', 'site-blocks' ); ?></p>
				</details>
				<details>
					<summary><?php esc_html_e( 'Security-conscious engineering', 'site-blocks' ); ?></summary>
					<p><?php esc_html_e( 'Professional hardware and software features support organisations with higher expectations around system resilience and cyber-aware deployment.', 'site-blocks' ); ?></p>
				</details>
			</div>
		</div>
	</section>

	<?php
	site_blocks_tech_brand_rail(
		array(
			'id'      => 'sg-hw-intro',
			'heading' => __( 'Professional Imaging For Environments That Matter', 'site-blocks' ),
			'copy'    => __( 'Hanwha Vision, known through its Wisenet technology, offers a broad range of professional cameras, recording systems, and surveillance technologies. Its solutions are suited to organisations that require dependable image quality, intelligent event detection, and a considered approach to the security of their infrastructure.', 'site-blocks' ),
			'cards'   => array(
				array(
					'icon'  => 'lens',
					'title' => __( 'Advanced Imaging', 'site-blocks' ),
					'text'  => __( 'Designed to deliver clear, useful video across changing light, distance, and environmental conditions.', 'site-blocks' ),
				),
				array(
					'icon'  => 'pulse',
					'title' => __( 'Intelligent Analytics', 'site-blocks' ),
					'text'  => __( 'Edge-based intelligence can help distinguish relevant activity and make recorded video more useful for investigation and operations.', 'site-blocks' ),
				),
				array(
					'icon'  => 'shield',
					'title' => __( 'Security-Conscious Engineering', 'site-blocks' ),
					'text'  => __( 'Professional hardware and software features support organisations with higher expectations around system resilience and cyber-aware deployment.', 'site-blocks' ),
				),
				array(
					'icon'  => 'nodes',
					'title' => __( 'Open Platform Compatibility', 'site-blocks' ),
					'text'  => __( 'Support for open video-management platforms allows Hanwha camera data and analytics to form part of broader security environments.', 'site-blocks' ),
				),
			),
		)
	);
	?>

	<section class="sg-tech__band sg-tech__dark" id="sg-hw-system" aria-labelledby="sg-hw-sys">
		<div class="sg-tech__wrap">
			<div class="sg-tech__sys-head">
				<div>
					<p class="sg-tech__eyebrow"><?php esc_html_e( 'System role', 'site-blocks' ); ?></p>
					<h2 id="sg-hw-sys"><?php esc_html_e( 'Premium Hardware Within A Complete Architecture', 'site-blocks' ); ?></h2>
				</div>
				<p class="sg-tech__copy"><?php esc_html_e( 'Hanwha Vision hardware can form the physical capture layer of a professionally designed surveillance system, with options for local recording or integration into a broader video-management environment. Safeguard can consider the site, the required coverage, and the operational goals before determining the most appropriate architecture.', 'site-blocks' ); ?></p>
			</div>
			<ol class="sg-tech__flow">
				<li class="sg-tech__node sg-tech__node--focus">
					<span class="sg-tech__step">01</span>
					<h3><?php esc_html_e( 'Camera Hardware', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'The physical capture layer, specified for the site\'s coverage and conditions.', 'site-blocks' ); ?></p>
					<span class="sg-tech__tag"><?php esc_html_e( 'Hanwha Vision layer', 'site-blocks' ); ?></span>
				</li>
				<li class="sg-tech__node">
					<span class="sg-tech__step">02</span>
					<h3><?php esc_html_e( 'Recording And Network Layer', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Local recording options and the network infrastructure that connects them.', 'site-blocks' ); ?></p>
				</li>
				<li class="sg-tech__node">
					<span class="sg-tech__step">03</span>
					<h3><?php esc_html_e( 'Video-Management Environment', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'The broader platform used to operate and review the system.', 'site-blocks' ); ?></p>
				</li>
			</ol>
		</div>
	</section>

	<?php
	site_blocks_tech_brand_approach(
		array(
			'id'         => 'sg-hw-close',
			'heading'    => __( 'Technology Selected With Purpose', 'site-blocks' ),
			'copy'       => __( 'Safeguard focuses on the relationship between equipment, environment, and operation. We help ensure that surveillance technology is selected and configured for the conditions it must cover, the information it must provide, and the people who will rely on it.', 'site-blocks' ),
			'list_title' => __( 'Selected in relation to', 'site-blocks' ),
			'items'      => array(
				__( 'The equipment', 'site-blocks' ),
				__( 'The environment it must cover', 'site-blocks' ),
				__( 'The operation that relies on it', 'site-blocks' ),
			),
		)
	);
	?>
	<?php
	site_blocks_tech_brand_enquiry( 'sg-hw-enq' );
}

/**
 * Block markup stored on the WordPress page.
 */
function site_blocks_tech_brand_page_content( string $slug ): string {
	return '<div class="sg-page alignfull"><!-- wp:site/safeguard-header /--><main id="main" class="sg-main"><!-- wp:site/tech-brand {"brand":"' . $slug . '"} /--></main><!-- wp:site/safeguard-footer /--></div>';
}

/**
 * Create or update one technology brand page.
 */
function site_blocks_upsert_tech_brand_page( string $slug, string $title ): void {
	$existing  = site_blocks_find_brand_page( $slug );
	$parent_id = site_blocks_brands_parent_id();
	$page_data = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_content' => site_blocks_tech_brand_page_content( $slug ),
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => 1,
		'post_parent'  => $parent_id,
	);

	if ( $existing instanceof WP_Post ) {
		$page_data['ID'] = $existing->ID;
		$page_id         = wp_update_post( $page_data, true );
	} else {
		$page_id = wp_insert_post( $page_data, true );
	}

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return;
	}

	site_blocks_apply_safeguard_page_meta( (int) $page_id );
}

/**
 * Create or update all three pages. Used on activation and version upgrade.
 */
function site_blocks_create_tech_brand_pages(): void {
	foreach ( site_blocks_tech_brand_pages() as $slug => $page ) {
		site_blocks_upsert_tech_brand_page( (string) $slug, (string) $page['title'] );
	}
}

/**
 * Publish a missing technology page without rewriting pages that already exist.
 */
function site_blocks_ensure_tech_brand_pages(): void {
	foreach ( site_blocks_tech_brand_pages() as $slug => $page ) {
		$existing = site_blocks_find_brand_page( (string) $slug );
		if ( $existing instanceof WP_Post ) {
			continue;
		}
		site_blocks_upsert_tech_brand_page( (string) $slug, (string) $page['title'] );
	}
}

/**
 * Brands index markup.
 */
function site_blocks_brands_index_content(): string {
	return '<div class="sg-page alignfull"><!-- wp:site/safeguard-header /--><main id="main" class="sg-main"><!-- wp:site/brands-index /--></main><!-- wp:site/safeguard-footer /--></div>';
}

/**
 * Create the /brands/ page when it is missing. Returns its ID.
 */
function site_blocks_brands_parent_id(): int {
	$existing = get_page_by_path( 'brands' );
	if ( $existing instanceof WP_Post && 'page' === $existing->post_type ) {
		return (int) $existing->ID;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => __( 'Brands', 'site-blocks' ),
			'post_name'    => 'brands',
			'post_content' => site_blocks_brands_index_content(),
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_author'  => 1,
			'post_parent'  => 0,
		),
		true
	);

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return 0;
	}

	site_blocks_apply_safeguard_page_meta( (int) $page_id );

	return (int) $page_id;
}

/**
 * Put every brand page under /brands/{slug}/ without rewriting its content.
 */
function site_blocks_ensure_brand_urls(): void {
	$parent_id = site_blocks_brands_parent_id();
	if ( $parent_id <= 0 ) {
		return;
	}

	site_blocks_ensure_tech_brand_pages();

	$moved = false;
	foreach ( array_keys( site_blocks_brand_catalogue() ) as $slug ) {
		$page = site_blocks_find_brand_page( (string) $slug );
		if ( ! $page instanceof WP_Post ) {
			continue;
		}
		if ( (int) $page->post_parent === $parent_id ) {
			continue;
		}
		$result = wp_update_post(
			array(
				'ID'          => $page->ID,
				'post_parent' => $parent_id,
			),
			true
		);
		if ( ! is_wp_error( $result ) ) {
			$moved = true;
		}
	}

	if ( $moved || '1' !== get_option( 'site_blocks_brand_paths' ) ) {
		flush_rewrite_rules( false );
		update_option( 'site_blocks_brand_paths', '1', false );
	}
}
add_action( 'init', 'site_blocks_ensure_brand_urls', 20 );

/**
 * Send the old root brand URLs to /brands/{slug}/.
 */
function site_blocks_redirect_legacy_brand_urls(): void {
	if ( is_admin() ) {
		return;
	}

	$request = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( (string) $_SERVER['REQUEST_URI'] ) : '';
	$path    = trim( (string) wp_parse_url( $request, PHP_URL_PATH ), '/' );
	$home    = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( '' !== $home && 0 === strpos( $path, $home ) ) {
		$path = trim( substr( $path, strlen( $home ) ), '/' );
	}

	if ( ! isset( site_blocks_brand_catalogue()[ $path ] ) ) {
		return;
	}

	wp_safe_redirect( home_url( site_blocks_brand_path( $path ) ), 301 );
	exit;
}
add_action( 'template_redirect', 'site_blocks_redirect_legacy_brand_urls', 0 );

/**
 * Whether the current request is the brands index.
 */
function site_blocks_is_brands_index(): bool {
	return is_page( 'brands' );
}

/**
 * Render the brands index.
 */
function site_blocks_render_brands_index(): void {
	?>
	<div class="sg-brands">
		<section class="sg-brands__band" aria-labelledby="sg-brands-heading">
			<div class="sg-container">
				<p class="sg-tech__eyebrow"><?php esc_html_e( 'Brands', 'site-blocks' ); ?></p>
				<h1 id="sg-brands-heading"><?php esc_html_e( 'Brands We Install', 'site-blocks' ); ?></h1>
				<p class="sg-brands__lede"><?php esc_html_e( 'Alarm, camera, video, and intercom brands Safeguard specifies. The right one depends on the site.', 'site-blocks' ); ?></p>
				<div class="sg-brands__grid">
					<?php foreach ( site_blocks_brand_catalogue() as $slug => $brand ) : ?>
						<?php
						$logo = isset( $brand['logo'] ) ? (string) $brand['logo'] : '';
						?>
						<a class="sg-brands__card" href="<?php echo esc_url( home_url( site_blocks_brand_path( (string) $slug ) ) ); ?>">
							<?php if ( '' !== $logo ) : ?>
								<img src="<?php echo esc_url( site_blocks_asset_url( $logo ) ); ?>" alt="" width="140" height="48" />
								<span class="sg-brands__label"><?php echo esc_html( (string) $brand['label'] ); ?></span>
							<?php else : ?>
								<span class="sg-brands__label"><?php echo esc_html( (string) $brand['label'] ); ?></span>
							<?php endif; ?>
							<span class="sg-brands__summary"><?php echo esc_html( (string) $brand['summary'] ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
		site_blocks_render_quote_cta(
			array(
				'heading_id'  => 'sg-brands-cta',
				'before'      => __( 'Planning or reviewing a ', 'site-blocks' ),
				'accent'      => __( 'surveillance system?', 'site-blocks' ),
				'sub'         => __( 'Tell us about the site and what it needs to do.', 'site-blocks' ),
				'alignfull'   => false,
			)
		);
		?>
	</div>
	<?php
}
