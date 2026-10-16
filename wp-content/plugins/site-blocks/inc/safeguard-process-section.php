<?php
/**
 * Homepage "How We Secure Your Site" process band.
 *
 * Full-width orange band: a centred one-line heading, then four numbered steps
 * joined by a dotted line, with a dashed doodle in the corners. Each step has a
 * one-line title and a two-line description.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Steps, in order.
 *
 * @return array<int, array{title: string, text: string}>
 */
function site_blocks_get_process_steps(): array {
	return array(
		array(
			'title' => __( 'Understand The Site', 'site-blocks' ),
			'text'  => __( 'What needs protecting and what could go wrong.', 'site-blocks' ),
		),
		array(
			'title' => __( 'Design The System', 'site-blocks' ),
			'text'  => __( 'Cameras, alarms and access, planned together.', 'site-blocks' ),
		),
		array(
			'title' => __( 'Install And Commission', 'site-blocks' ),
			'text'  => __( 'Mounting, wiring, testing and a clear handover.', 'site-blocks' ),
		),
		array(
			'title' => __( 'Support What Was Built', 'site-blocks' ),
			'text'  => __( 'Service, upgrades and fixes when things change.', 'site-blocks' ),
		),
	);
}

/**
 * Render the section.
 */
function site_blocks_render_process_section(): void {
	$steps = site_blocks_get_process_steps();
	?>
		<section id="sg-process" class="sg-process" aria-labelledby="sg-process-title">
			<svg class="sg-process__doodle sg-process__doodle--top" viewBox="0 0 360 300" aria-hidden="true" focusable="false">
				<path d="M0 2 C120 2 190 40 188 110 C186 170 150 190 120 170 C90 150 110 90 170 105 C230 120 250 190 262 240 C270 275 320 292 360 298" />
			</svg>
			<svg class="sg-process__doodle sg-process__doodle--bottom" viewBox="0 0 220 160" aria-hidden="true" focusable="false">
				<path d="M0 150 C50 150 70 120 80 90 C92 52 140 40 160 70 C178 98 140 120 118 100 C98 82 120 56 150 58 C190 60 205 20 220 0" />
				<circle cx="46" cy="52" r="26" />
			</svg>

			<div class="sg-container">
				<h2 id="sg-process-title" class="sg-process__title" data-sg-reveal><?php esc_html_e( 'How We Secure Your Site', 'site-blocks' ); ?></h2>

				<ol class="sg-process__steps" data-sg-stagger>
					<?php foreach ( $steps as $i => $step ) : ?>
						<li class="sg-process__step" data-sg-reveal>
							<span class="sg-process__num" aria-hidden="true"><?php echo esc_html( (string) ( $i + 1 ) ); ?></span>
							<h3 class="sg-process__name"><?php echo esc_html( $step['title'] ); ?></h3>
							<p class="sg-process__text"><?php echo esc_html( $step['text'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</section>
	<?php
}
