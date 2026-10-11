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
 * Page titles, keyed by slug.
 *
 * @return array<string, array{title: string}>
 */
function site_blocks_tech_brand_pages(): array {
	return array(
		'milesight'     => array(
			'title' => __( 'Milesight', 'site-blocks' ),
		),
		'nx-witness'    => array(
			'title' => __( 'NX Witness', 'site-blocks' ),
		),
		'hanwha-vision' => array(
			'title' => __( 'Hanwha Vision', 'site-blocks' ),
		),
	);
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
 * Numbered consider list.
 *
 * @param string               $title List heading.
 * @param array<int, string>   $items Items.
 */
function site_blocks_tech_brand_consider( string $title, array $items ): void {
	?>
	<div class="sg-tech__consider">
		<h2><?php echo esc_html( $title ); ?></h2>
		<ul>
			<?php foreach ( $items as $item ) : ?>
				<li><?php echo esc_html( $item ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
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
	<section class="sg-tech__band sg-tech__hero" aria-labelledby="sg-ms-hero">
		<div class="sg-tech__wrap sg-tech__grid">
			<div>
				<p class="sg-tech__eyebrow"><?php esc_html_e( 'Intelligent surveillance technology', 'site-blocks' ); ?></p>
				<h1 id="sg-ms-hero"><?php esc_html_e( 'See more.', 'site-blocks' ); ?><span class="sg-tech__soft"><?php esc_html_e( 'Understand more.', 'site-blocks' ); ?></span></h1>
				<p class="sg-tech__lede"><?php esc_html_e( 'Milesight brings together intelligent network cameras, edge-based video analytics, and connected sensing technologies to help businesses understand what is happening across their environments.', 'site-blocks' ); ?></p>
				<?php site_blocks_tech_brand_actions( __( 'See where it fits', 'site-blocks' ), '#sg-ms-system', true ); ?>
			</div>
			<div class="sg-tech__visual"><?php site_blocks_tech_brand_visual_milesight(); ?></div>
		</div>
	</section>

	<section class="sg-tech__band sg-tech__light" aria-labelledby="sg-ms-intro">
		<div class="sg-tech__wrap">
			<div class="sg-tech__intro">
				<div>
					<p class="sg-tech__eyebrow"><?php esc_html_e( 'Brand introduction', 'site-blocks' ); ?></p>
					<h2 id="sg-ms-intro"><?php esc_html_e( 'Intelligent visibility for the modern environment', 'site-blocks' ); ?></h2>
					<p class="sg-tech__copy"><?php esc_html_e( 'Milesight develops professional security and IoT technologies that extend beyond passive video recording. Its camera platforms support high-quality imaging, low-light performance, intelligent detection, and practical edge analytics, while its broader sensing ecosystem can contribute to a more complete understanding of buildings, sites, and operations.', 'site-blocks' ); ?></p>
				</div>
				<aside class="sg-tech__glance" aria-label="<?php esc_attr_e( 'Milesight at a glance', 'site-blocks' ); ?>">
					<h2><?php esc_html_e( 'At a glance', 'site-blocks' ); ?></h2>
					<dl>
						<div><dt><?php esc_html_e( 'Category', 'site-blocks' ); ?></dt><dd><?php esc_html_e( 'Network cameras and IoT sensing', 'site-blocks' ); ?></dd></div>
						<div><dt><?php esc_html_e( 'Role in a system', 'site-blocks' ); ?></dt><dd><?php esc_html_e( 'Capture and analytics layer', 'site-blocks' ); ?></dd></div>
						<div><dt><?php esc_html_e( 'Suited to', 'site-blocks' ); ?></dt><dd><?php esc_html_e( 'Modern commercial environments', 'site-blocks' ); ?></dd></div>
					</dl>
				</aside>
			</div>
			<ul class="sg-tech__cards">
				<li class="sg-tech__card">
					<div class="sg-tech__card-top"><span class="sg-tech__glyph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="12" cy="12" r="3"/></svg></span></div>
					<h3><?php esc_html_e( 'Intelligent video', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Edge-based analytics can help identify and classify activity, people, and vehicles closer to the point of capture.', 'site-blocks' ); ?></p>
				</li>
				<li class="sg-tech__card">
					<div class="sg-tech__card-top"><span class="sg-tech__glyph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 20V8l8-4 8 4v12M9 20v-6h6v6"/></svg></span></div>
					<h3><?php esc_html_e( 'Designed for demanding environments', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Professional camera ranges are built for dependable operation across indoor, outdoor, commercial, and infrastructure settings.', 'site-blocks' ); ?></p>
				</li>
				<li class="sg-tech__card">
					<div class="sg-tech__card-top"><span class="sg-tech__glyph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="6" cy="12" r="2"/><circle cx="18" cy="6" r="2"/><circle cx="18" cy="18" r="2"/><path d="M8 12l8-5M8 12l8 5"/></svg></span></div>
					<h3><?php esc_html_e( 'Connected systems', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Open network standards and integration pathways allow Milesight hardware to form part of a broader security or building-technology environment.', 'site-blocks' ); ?></p>
				</li>
			</ul>
		</div>
	</section>

	<section class="sg-tech__band sg-tech__dark" id="sg-ms-system" aria-labelledby="sg-ms-sys">
		<div class="sg-tech__wrap">
			<div class="sg-tech__sys-head">
				<div>
					<p class="sg-tech__eyebrow"><?php esc_html_e( 'System role', 'site-blocks' ); ?></p>
					<h2 id="sg-ms-sys"><?php esc_html_e( 'A capable layer within a complete security system', 'site-blocks' ); ?></h2>
				</div>
				<p class="sg-tech__copy"><?php esc_html_e( 'Milesight can operate as the capture and analytics layer within a professionally designed surveillance system. Depending on the site, its hardware may be used with local recording equipment or integrated with a wider video-management platform. The right configuration depends on the environment, the required coverage, and how the client wants to manage and respond to events.', 'site-blocks' ); ?></p>
			</div>
			<ol class="sg-tech__flow">
				<li class="sg-tech__node sg-tech__node--focus">
					<span class="sg-tech__step">01</span>
					<h3><?php esc_html_e( 'Cameras and sensors', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Imaging, edge analytics and connected sensing at the point of capture.', 'site-blocks' ); ?></p>
					<span class="sg-tech__tag"><?php esc_html_e( 'Milesight layer', 'site-blocks' ); ?></span>
				</li>
				<li class="sg-tech__node">
					<span class="sg-tech__step">02</span>
					<h3><?php esc_html_e( 'Network and recording', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Network infrastructure and local recording equipment, sized to the site.', 'site-blocks' ); ?></p>
				</li>
				<li class="sg-tech__node">
					<span class="sg-tech__step">03</span>
					<h3><?php esc_html_e( 'Video management and response', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'How the client views, manages and responds to events.', 'site-blocks' ); ?></p>
				</li>
			</ol>
		</div>
	</section>

	<section class="sg-tech__band sg-tech__light" aria-labelledby="sg-ms-close">
		<div class="sg-tech__wrap sg-tech__close">
			<div>
				<p class="sg-tech__eyebrow"><?php esc_html_e( 'The Safeguard approach', 'site-blocks' ); ?></p>
				<h2 id="sg-ms-close"><?php esc_html_e( 'Specified around the site, not just the product', 'site-blocks' ); ?></h2>
				<p class="sg-tech__copy"><?php esc_html_e( 'Safeguard approaches surveillance as a complete system. We consider coverage, environmental conditions, network infrastructure, recording requirements, and future integration before recommending the technology that best fits the site.', 'site-blocks' ); ?></p>
				<div class="sg-tech__actions"><a class="sg-btn sg-btn--navy" href="<?php echo esc_url( $links['quote'] ); ?>"><?php esc_html_e( 'Talk to Safeguard about your requirements', 'site-blocks' ); ?></a></div>
			</div>
			<?php
			site_blocks_tech_brand_consider(
				__( 'Considered before recommending', 'site-blocks' ),
				array(
					__( 'Coverage', 'site-blocks' ),
					__( 'Environmental conditions', 'site-blocks' ),
					__( 'Network infrastructure', 'site-blocks' ),
					__( 'Recording requirements', 'site-blocks' ),
					__( 'Future integration', 'site-blocks' ),
				)
			);
			?>
		</div>
	</section>
	<?php
	site_blocks_tech_brand_enquiry( 'sg-ms-enq' );
}

/**
 * NX Witness page.
 */
function site_blocks_render_tech_brand_nx(): void {
	$links = site_blocks_tech_brand_links();
	?>
	<section class="sg-tech__band sg-tech__hero sg-tech__hero--stack" aria-labelledby="sg-nx-hero">
		<div class="sg-tech__wrap">
			<div class="sg-tech__head">
				<div>
					<p class="sg-tech__eyebrow"><?php esc_html_e( 'Video management software', 'site-blocks' ); ?></p>
					<h1 id="sg-nx-hero"><?php esc_html_e( 'The intelligence behind the video system', 'site-blocks' ); ?></h1>
				</div>
				<div>
					<p class="sg-tech__lede"><?php esc_html_e( 'NX Witness is a flexible video-management platform designed to bring network cameras, recording, users, and events together in one clear operating environment.', 'site-blocks' ); ?></p>
					<?php site_blocks_tech_brand_actions( __( 'See the software layer', 'site-blocks' ), '#sg-nx-system', true ); ?>
				</div>
			</div>
			<div class="sg-tech__visual"><?php site_blocks_tech_brand_visual_nx(); ?></div>
		</div>
	</section>

	<section class="sg-tech__band sg-tech__light" aria-labelledby="sg-nx-intro">
		<div class="sg-tech__wrap">
			<div class="sg-tech__intro">
				<div>
					<p class="sg-tech__eyebrow"><?php esc_html_e( 'Brand introduction', 'site-blocks' ); ?></p>
					<h2 id="sg-nx-intro"><?php esc_html_e( 'One clear view of a connected security environment', 'site-blocks' ); ?></h2>
					<p class="sg-tech__copy"><?php esc_html_e( 'Network Optix develops software that helps organisations discover, manage, view, record, and work with video from a wide range of networked devices. Rather than replacing the physical camera layer, NX Witness provides the software environment that helps the wider system operate as one.', 'site-blocks' ); ?></p>
				</div>
				<aside class="sg-tech__glance" aria-label="<?php esc_attr_e( 'NX Witness at a glance', 'site-blocks' ); ?>">
					<h2><?php esc_html_e( 'At a glance', 'site-blocks' ); ?></h2>
					<dl>
						<div><dt><?php esc_html_e( 'Category', 'site-blocks' ); ?></dt><dd><?php esc_html_e( 'Video management software', 'site-blocks' ); ?></dd></div>
						<div><dt><?php esc_html_e( 'Role in a system', 'site-blocks' ); ?></dt><dd><?php esc_html_e( 'The operating layer above the cameras', 'site-blocks' ); ?></dd></div>
						<div><dt><?php esc_html_e( 'Brings together', 'site-blocks' ); ?></dt><dd><?php esc_html_e( 'Cameras, recording, users and events', 'site-blocks' ); ?></dd></div>
					</dl>
				</aside>
			</div>
			<ul class="sg-tech__cards">
				<li class="sg-tech__card">
					<div class="sg-tech__card-top"><span class="sg-tech__glyph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 3l9 5-9 5-9-5 9-5zM3 13l9 5 9-5"/></svg></span></div>
					<h3><?php esc_html_e( 'Flexible architecture', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Designed to work across different hardware environments and support systems that need to evolve over time.', 'site-blocks' ); ?></p>
				</li>
				<li class="sg-tech__card">
					<div class="sg-tech__card-top"><span class="sg-tech__glyph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="4" width="8" height="7" rx="1"/><rect x="13" y="4" width="8" height="7" rx="1"/><rect x="3" y="13" width="8" height="7" rx="1"/><rect x="13" y="13" width="8" height="7" rx="1"/></svg></span></div>
					<h3><?php esc_html_e( 'Clear video operations', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Give authorised users a practical way to view live footage, review recordings, investigate events, and manage connected sites.', 'site-blocks' ); ?></p>
				</li>
				<li class="sg-tech__card">
					<div class="sg-tech__card-top"><span class="sg-tech__glyph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 8h14l-3-3M20 16H6l3 3"/></svg></span></div>
					<h3><?php esc_html_e( 'Intelligent integration', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Logic-based events and integrations can connect video activity with other security and building systems.', 'site-blocks' ); ?></p>
				</li>
			</ul>
		</div>
	</section>

	<section class="sg-tech__band sg-tech__dark" id="sg-nx-system" aria-labelledby="sg-nx-sys">
		<div class="sg-tech__wrap sg-tech__split">
			<div>
				<p class="sg-tech__eyebrow"><?php esc_html_e( 'System role', 'site-blocks' ); ?></p>
				<h2 id="sg-nx-sys"><?php esc_html_e( 'The software layer connecting the system', 'site-blocks' ); ?></h2>
				<p class="sg-tech__copy"><?php esc_html_e( 'In a complete surveillance design, NX Witness sits above the physical camera and recording infrastructure. It can provide the central interface for systems using cameras from brands such as Milesight or Hanwha Vision, while allowing the overall solution to be configured around the site\'s operational and integration requirements.', 'site-blocks' ); ?></p>
			</div>
			<ol class="sg-tech__stack">
				<li class="sg-tech__layer"><h3><small><?php esc_html_e( 'Layer 1', 'site-blocks' ); ?></small><?php esc_html_e( 'Camera hardware', 'site-blocks' ); ?></h3><ul class="sg-tech__chips"><li><?php esc_html_e( 'Network cameras', 'site-blocks' ); ?></li><li><?php esc_html_e( 'Edge devices', 'site-blocks' ); ?></li></ul></li>
				<li class="sg-tech__layer"><h3><small><?php esc_html_e( 'Layer 2', 'site-blocks' ); ?></small><?php esc_html_e( 'Network and server', 'site-blocks' ); ?></h3><ul class="sg-tech__chips"><li><?php esc_html_e( 'Network', 'site-blocks' ); ?></li><li><?php esc_html_e( 'Recording servers', 'site-blocks' ); ?></li></ul></li>
				<li class="sg-tech__layer sg-tech__layer--focus"><h3><small><?php esc_html_e( 'Layer 3', 'site-blocks' ); ?></small><?php esc_html_e( 'NX Witness', 'site-blocks' ); ?></h3><ul class="sg-tech__chips"><li><?php esc_html_e( 'Discover', 'site-blocks' ); ?></li><li><?php esc_html_e( 'View', 'site-blocks' ); ?></li><li><?php esc_html_e( 'Record', 'site-blocks' ); ?></li></ul></li>
				<li class="sg-tech__layer"><h3><small><?php esc_html_e( 'Layer 4', 'site-blocks' ); ?></small><?php esc_html_e( 'Users, events, and connected systems', 'site-blocks' ); ?></h3><ul class="sg-tech__chips"><li><?php esc_html_e( 'Users', 'site-blocks' ); ?></li><li><?php esc_html_e( 'Events', 'site-blocks' ); ?></li><li><?php esc_html_e( 'Integrations', 'site-blocks' ); ?></li></ul></li>
			</ol>
		</div>
	</section>

	<section class="sg-tech__band sg-tech__light" aria-labelledby="sg-nx-close">
		<div class="sg-tech__wrap sg-tech__close">
			<div>
				<p class="sg-tech__eyebrow"><?php esc_html_e( 'The Safeguard approach', 'site-blocks' ); ?></p>
				<h2 id="sg-nx-close"><?php esc_html_e( 'Built around how your organisation needs to work', 'site-blocks' ); ?></h2>
				<p class="sg-tech__copy"><?php esc_html_e( 'Safeguard helps translate video-management technology into a practical operating system for the site. We consider camera compatibility, server requirements, user access, recording needs, and future expansion so the platform supports the way the organisation actually operates.', 'site-blocks' ); ?></p>
				<div class="sg-tech__actions"><a class="sg-btn sg-btn--navy" href="<?php echo esc_url( $links['quote'] ); ?>"><?php esc_html_e( 'Talk to Safeguard about your video environment', 'site-blocks' ); ?></a></div>
			</div>
			<?php
			site_blocks_tech_brand_consider(
				__( 'Considered before recommending', 'site-blocks' ),
				array(
					__( 'Camera compatibility', 'site-blocks' ),
					__( 'Server requirements', 'site-blocks' ),
					__( 'User access', 'site-blocks' ),
					__( 'Recording needs', 'site-blocks' ),
					__( 'Future expansion', 'site-blocks' ),
				)
			);
			?>
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
	<section class="sg-tech__band sg-tech__hero sg-tech__hero--reverse" aria-labelledby="sg-hw-hero">
		<div class="sg-tech__wrap sg-tech__grid">
			<div>
				<p class="sg-tech__eyebrow"><?php esc_html_e( 'Professional video surveillance', 'site-blocks' ); ?></p>
				<h1 id="sg-hw-hero"><?php esc_html_e( 'Confidence built into', 'site-blocks' ); ?> <span class="sg-tech__hl"><?php esc_html_e( 'every frame', 'site-blocks' ); ?></span></h1>
				<p class="sg-tech__lede"><?php esc_html_e( 'Hanwha Vision develops professional video-surveillance technologies that combine advanced imaging, intelligent analytics, and security-conscious engineering for demanding environments.', 'site-blocks' ); ?></p>
				<?php site_blocks_tech_brand_actions( __( 'See where it fits', 'site-blocks' ), '#sg-hw-system', true ); ?>
			</div>
			<div class="sg-tech__visual"><?php site_blocks_tech_brand_visual_hanwha(); ?></div>
		</div>
	</section>

	<section class="sg-tech__band sg-tech__light" aria-labelledby="sg-hw-intro">
		<div class="sg-tech__wrap">
			<div class="sg-tech__intro">
				<div>
					<p class="sg-tech__eyebrow"><?php esc_html_e( 'Brand introduction', 'site-blocks' ); ?></p>
					<h2 id="sg-hw-intro"><?php esc_html_e( 'Professional imaging for environments that matter', 'site-blocks' ); ?></h2>
					<p class="sg-tech__copy"><?php esc_html_e( 'Hanwha Vision, known through its Wisenet technology, offers a broad range of professional cameras, recording systems, and surveillance technologies. Its solutions are suited to organisations that require dependable image quality, intelligent event detection, and a considered approach to the security of their infrastructure.', 'site-blocks' ); ?></p>
				</div>
				<aside class="sg-tech__glance" aria-label="<?php esc_attr_e( 'Hanwha Vision at a glance', 'site-blocks' ); ?>">
					<h2><?php esc_html_e( 'At a glance', 'site-blocks' ); ?></h2>
					<dl>
						<div><dt><?php esc_html_e( 'Category', 'site-blocks' ); ?></dt><dd><?php esc_html_e( 'Professional surveillance hardware', 'site-blocks' ); ?></dd></div>
						<div><dt><?php esc_html_e( 'Known through', 'site-blocks' ); ?></dt><dd><?php esc_html_e( 'Wisenet technology', 'site-blocks' ); ?></dd></div>
						<div><dt><?php esc_html_e( 'Emphasis', 'site-blocks' ); ?></dt><dd><?php esc_html_e( 'Imaging, analytics and engineering', 'site-blocks' ); ?></dd></div>
					</dl>
				</aside>
			</div>
			<ul class="sg-tech__cards">
				<li class="sg-tech__card">
					<div class="sg-tech__card-top"><span class="sg-tech__glyph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3.5"/></svg></span></div>
					<h3><?php esc_html_e( 'Advanced imaging', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Designed to deliver clear, useful video across changing light, distance, and environmental conditions.', 'site-blocks' ); ?></p>
				</li>
				<li class="sg-tech__card">
					<div class="sg-tech__card-top"><span class="sg-tech__glyph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 12h4l3-7 4 14 3-7h4"/></svg></span></div>
					<h3><?php esc_html_e( 'Intelligent analytics', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Edge-based intelligence can help distinguish relevant activity and make recorded video more useful for investigation and operations.', 'site-blocks' ); ?></p>
				</li>
				<li class="sg-tech__card">
					<div class="sg-tech__card-top"><span class="sg-tech__glyph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/></svg></span></div>
					<h3><?php esc_html_e( 'Security-conscious engineering', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Professional hardware and software features support organisations with higher expectations around system resilience and cyber-aware deployment.', 'site-blocks' ); ?></p>
				</li>
			</ul>
		</div>
	</section>

	<section class="sg-tech__band sg-tech__dark" id="sg-hw-system" aria-labelledby="sg-hw-sys">
		<div class="sg-tech__wrap">
			<div class="sg-tech__sys-head">
				<div>
					<p class="sg-tech__eyebrow"><?php esc_html_e( 'System role', 'site-blocks' ); ?></p>
					<h2 id="sg-hw-sys"><?php esc_html_e( 'Premium hardware within a complete architecture', 'site-blocks' ); ?></h2>
				</div>
				<p class="sg-tech__copy"><?php esc_html_e( 'Hanwha Vision hardware can form the physical capture layer of a professionally designed surveillance system, with options for local recording or integration into a broader video-management environment. Safeguard can consider the site, the required coverage, and the operational goals before determining the most appropriate architecture.', 'site-blocks' ); ?></p>
			</div>
			<ol class="sg-tech__flow">
				<li class="sg-tech__node sg-tech__node--focus">
					<span class="sg-tech__step">01</span>
					<h3><?php esc_html_e( 'Camera hardware', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'The physical capture layer, specified for the site\'s coverage and conditions.', 'site-blocks' ); ?></p>
					<span class="sg-tech__tag"><?php esc_html_e( 'Hanwha Vision layer', 'site-blocks' ); ?></span>
				</li>
				<li class="sg-tech__node">
					<span class="sg-tech__step">02</span>
					<h3><?php esc_html_e( 'Recording and network layer', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'Local recording options and the network infrastructure that connects them.', 'site-blocks' ); ?></p>
				</li>
				<li class="sg-tech__node">
					<span class="sg-tech__step">03</span>
					<h3><?php esc_html_e( 'Video-management environment', 'site-blocks' ); ?></h3>
					<p><?php esc_html_e( 'The broader platform used to operate and review the system.', 'site-blocks' ); ?></p>
				</li>
			</ol>
		</div>
	</section>

	<section class="sg-tech__band sg-tech__light" aria-labelledby="sg-hw-close">
		<div class="sg-tech__wrap sg-tech__close">
			<div>
				<p class="sg-tech__eyebrow"><?php esc_html_e( 'The Safeguard approach', 'site-blocks' ); ?></p>
				<h2 id="sg-hw-close"><?php esc_html_e( 'Technology selected with purpose', 'site-blocks' ); ?></h2>
				<p class="sg-tech__copy"><?php esc_html_e( 'Safeguard focuses on the relationship between equipment, environment, and operation. We help ensure that surveillance technology is selected and configured for the conditions it must cover, the information it must provide, and the people who will rely on it.', 'site-blocks' ); ?></p>
				<div class="sg-tech__actions"><a class="sg-btn sg-btn--navy" href="<?php echo esc_url( $links['quote'] ); ?>"><?php esc_html_e( 'Talk to Safeguard about your surveillance requirements', 'site-blocks' ); ?></a></div>
			</div>
			<?php
			site_blocks_tech_brand_consider(
				__( 'Selected in relation to', 'site-blocks' ),
				array(
					__( 'The equipment', 'site-blocks' ),
					__( 'The environment it must cover', 'site-blocks' ),
					__( 'The operation that relies on it', 'site-blocks' ),
				)
			);
			?>
		</div>
	</section>
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
	$existing  = get_page_by_path( $slug );
	$page_data = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_content' => site_blocks_tech_brand_page_content( $slug ),
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => 1,
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
 * Publish a missing page without rewriting pages that already exist.
 */
function site_blocks_ensure_tech_brand_pages(): void {
	foreach ( site_blocks_tech_brand_pages() as $slug => $page ) {
		$existing = get_page_by_path( (string) $slug );
		if ( $existing instanceof WP_Post ) {
			continue;
		}
		site_blocks_upsert_tech_brand_page( (string) $slug, (string) $page['title'] );
	}
}
add_action( 'init', 'site_blocks_ensure_tech_brand_pages', 20 );
