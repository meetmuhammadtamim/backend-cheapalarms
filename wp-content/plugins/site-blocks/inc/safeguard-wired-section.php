<?php
/**
 * "Your security systems should work together" section.
 *
 * A diagram that GSAP builds as the visitor scrolls
 * (pinned, scrubbed, reversible) and that can be explored by hovering a node.
 *
 * A three-column diagram: devices on site, the infrastructure behind them, and
 * the single view for your people. Pipelines are drawn in an SVG overlay that
 * shares one coordinate system with the cards (1410 x 480 units), so lines and
 * cards stay aligned at every width. Icons are MDI, via the site helper.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inline style carrying a box in diagram units (see CSS for the maths).
 *
 * @param int $x X position.
 * @param int $y Y position.
 * @param int $w Width.
 * @param int $h Height.
 */
function site_blocks_wired_box( int $x, int $y, int $w, int $h ): string {
	return sprintf( '--x:%d;--y:%d;--w:%d;--h:%d', $x, $y, $w, $h );
}

/**
 * Render the section.
 */
function site_blocks_render_wired_section(): void {
	$devices = array(
		array(
			'icon'  => 'cctv',
			'label' => __( 'CCTV', 'site-blocks' ),
			'y'     => 65,
			'pipes' => '1 5',
		),
		array(
			'icon'  => 'bell',
			'label' => __( 'Alarms', 'site-blocks' ),
			'y'     => 165,
			'pipes' => '2 5',
		),
		array(
			'icon'  => 'lock-smart',
			'label' => __( 'Access control', 'site-blocks' ),
			'y'     => 265,
			'pipes' => '3 6',
		),
		array(
			'icon'  => 'intercom',
			'label' => __( 'Intercoms', 'site-blocks' ),
			'y'     => 365,
			'pipes' => '4 6',
		),
	);

	$infrastructure = array(
		array(
			'icon'  => 'server',
			'label' => __( 'Network, servers and recording', 'site-blocks' ),
			'y'     => 100,
			'pipes' => '1 2 5',
		),
		array(
			'icon'  => 'settings',
			'label' => __( 'Video management and integrations', 'site-blocks' ),
			'y'     => 291,
			'pipes' => '3 4 6',
		),
	);
	?>
		<section class="sg-wired" aria-labelledby="sg-wired-title" data-sg-wired>
			<div class="sg-container">
				<div class="sg-wired__stage" data-sg-stage>
				<header class="sg-wired__intro">
					<h2 id="sg-wired-title" class="sg-wired__title">
						<?php esc_html_e( 'Your security systems should', 'site-blocks' ); ?>
						<span class="sg-wired__accent"><?php esc_html_e( 'work together.', 'site-blocks' ); ?></span>
					</h2>
					<p class="sg-wired__lead"><?php esc_html_e( 'Bring your people, systems and sites together with a unified approach. When your security systems work as one, you get greater visibility, faster response and a safer, more efficient operation.', 'site-blocks' ); ?></p>
				</header>

				<div class="sg-wired__diagram">
					<h3 class="sg-wired__col sg-wired__col--1" style="<?php echo esc_attr( site_blocks_wired_box( 14, 8, 300, 44 ) ); ?>"><?php esc_html_e( 'On site', 'site-blocks' ); ?></h3>
					<h3 class="sg-wired__col sg-wired__col--2" style="<?php echo esc_attr( site_blocks_wired_box( 556, 8, 330, 44 ) ); ?>"><?php esc_html_e( 'Behind the scenes', 'site-blocks' ); ?></h3>
					<h3 class="sg-wired__col sg-wired__col--3" style="<?php echo esc_attr( site_blocks_wired_box( 1090, 8, 300, 44 ) ); ?>"><?php esc_html_e( 'For your people', 'site-blocks' ); ?></h3>

					<?php foreach ( $devices as $i => $device ) : ?>
						<div class="sg-wired__node sg-wired__node--device" data-pipes="<?php echo esc_attr( (string) $device['pipes'] ); ?>" style="<?php echo esc_attr( site_blocks_wired_box( 12, (int) $device['y'], 311, 82 ) . ';--i:' . (int) $i ); ?>">
							<span class="sg-wired__icon" aria-hidden="true"><?php site_blocks_icon( (string) $device['icon'], 24 ); ?></span>
							<span class="sg-wired__label"><?php echo esc_html( (string) $device['label'] ); ?></span>
						</div>
					<?php endforeach; ?>

					<?php foreach ( $infrastructure as $i => $node ) : ?>
						<div class="sg-wired__node sg-wired__node--core" data-pipes="<?php echo esc_attr( (string) $node['pipes'] ); ?>" style="<?php echo esc_attr( site_blocks_wired_box( 550, (int) $node['y'], 328, 123 ) . ';--i:' . (int) $i ); ?>">
							<span class="sg-wired__icon" aria-hidden="true"><?php site_blocks_icon( (string) $node['icon'], 24 ); ?></span>
							<span class="sg-wired__label"><?php echo esc_html( (string) $node['label'] ); ?></span>
						</div>
					<?php endforeach; ?>

					<div class="sg-wired__node sg-wired__node--result" data-pipes="1 2 3 4 5 6" style="<?php echo esc_attr( site_blocks_wired_box( 1088, 130, 319, 246 ) ); ?>">
						<span class="sg-wired__icon" aria-hidden="true"><?php site_blocks_icon( 'users', 24 ); ?></span>
						<span class="sg-wired__label"><?php esc_html_e( 'A clearer view of what’s happening and what needs attention', 'site-blocks' ); ?></span>
						<span class="sg-wired__spark" aria-hidden="true"></span>
					</div>

					<svg class="sg-wired__pipes" viewBox="0 0 1410 480" fill="none" aria-hidden="true" focusable="false">
						<g stroke="#1769A1" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
							<?php
							$pipes = array(
								1 => 'M323 106H413Q428 106 428 121V139Q428 154 443 154H542',
								2 => 'M323 206H413Q428 206 428 191V180Q428 165 443 165H542',
								3 => 'M323 306H413Q428 306 428 321V332Q428 347 443 347H542',
								4 => 'M323 406H413Q428 406 428 391V373Q428 358 443 358H542',
								5 => 'M881 165H936Q951 165 951 180V232Q951 247 966 247H1078',
								6 => 'M881 356H936Q951 356 951 341V262Q951 247 966 247H1078',
							);
							foreach ( $pipes as $n => $d ) :
								?>
								<path class="sg-wired__pipe sg-wired__pipe--<?php echo (int) $n; ?>" d="<?php echo esc_attr( $d ); ?>"/>
							<?php endforeach; ?>
						</g>
						<g fill="#1769A1">
							<circle class="sg-wired__dot sg-wired__dot--src" style="--i:0" cx="323" cy="106" r="6"/>
							<circle class="sg-wired__dot sg-wired__dot--src" style="--i:1" cx="323" cy="206" r="6"/>
							<circle class="sg-wired__dot sg-wired__dot--src" style="--i:2" cx="323" cy="306" r="6"/>
							<circle class="sg-wired__dot sg-wired__dot--src" style="--i:3" cx="323" cy="406" r="6"/>
							<circle class="sg-wired__dot sg-wired__dot--merge" style="--i:0" cx="542" cy="154" r="5"/>
							<circle class="sg-wired__dot sg-wired__dot--merge" style="--i:1" cx="542" cy="165" r="5"/>
							<circle class="sg-wired__dot sg-wired__dot--merge" style="--i:2" cx="542" cy="347" r="5"/>
							<circle class="sg-wired__dot sg-wired__dot--merge" style="--i:3" cx="542" cy="358" r="5"/>
							<circle class="sg-wired__dot sg-wired__dot--out" style="--i:0" cx="881" cy="165" r="6"/>
							<circle class="sg-wired__dot sg-wired__dot--out" style="--i:1" cx="881" cy="356" r="6"/>
						</g>
						<circle class="sg-wired__dot sg-wired__dot--end" cx="1078" cy="247" r="8" fill="#1769A1" stroke="#fff" stroke-width="3"/>
						<g class="sg-wired__pulses" fill="#fff" stroke="#1769A1" stroke-width="2.5"></g>
					</svg>
				</div>

				</div>

				<div class="sg-wired__closing" data-sg-stagger>
					<h3 class="sg-wired__closing-title" data-sg-reveal><?php esc_html_e( 'Complex behind the scenes. Clear in operation.', 'site-blocks' ); ?></h3>
					<p class="sg-wired__closing-text" data-sg-reveal><?php esc_html_e( 'We design, integrate and support security systems that work together, so you get a simpler, more effective way to protect your people, property and operations.', 'site-blocks' ); ?></p>
				</div>
			</div>
		</section>
	<?php
}
