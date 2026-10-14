<?php
/**
 * Ajax Alarm Systems — monitoring options (copy + app collage).
 *
 * @package Site_Blocks
 *
 * @var array $attributes Block attributes.
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once SITE_BLOCKS_DIR . 'inc/ajax-alarm-systems-icons.php';

$monitor_img = site_blocks_asset_url( 'images/ajax/ajax-monitoring-app.webp' );

$options = array(
	array(
		'title'       => __( 'Self-Monitoring Via App', 'site-blocks' ),
		'description' => __( 'Stay in control with real-time alerts and system updates in the Ajax app.', 'site-blocks' ),
		'icon'        => 'smartphone',
	),
	array(
		'title'       => __( 'Professional Monitoring', 'site-blocks' ),
		'description' => __( 'Connect the system to an alarm monitoring service when monitoring is selected for the job.', 'site-blocks' ),
		'icon'        => 'headset',
	),
	array(
		'title'       => __( 'Backup Communication', 'site-blocks' ),
		'description' => __( 'We design reliable backup paths to keep you connected when it matters most.', 'site-blocks' ),
		'icon'        => 'shield-check',
	),
);
?>
<section class="sg-ajax-section sg-ajax-monitoring alignfull" aria-labelledby="sg-ajax-monitoring-heading">
	<div class="sg-container">
		<div class="sg-ajax-monitoring__layout">
			<div class="sg-ajax-monitoring__copy">
				<p class="sg-ajax-section__eyebrow"><?php esc_html_e( 'Monitoring Options', 'site-blocks' ); ?></p>
				<h2 id="sg-ajax-monitoring-heading" class="sg-ajax-section__title">
					<?php esc_html_e( 'Choose How Your Ajax System Is Monitored', 'site-blocks' ); ?>
				</h2>
				<p class="sg-ajax-monitoring__lead">
					<?php esc_html_e( 'Safeguard can configure your system for self-monitoring via the Ajax app, discuss professional monitoring options, and plan backup communication to keep your property protected at all times.', 'site-blocks' ); ?>
				</p>

				<ul class="sg-ajax-monitoring__features" role="list">
					<?php foreach ( $options as $option ) : ?>
						<li class="sg-ajax-monitoring__feature">
							<span class="sg-ajax-monitoring__feature-icon" aria-hidden="true">
								<?php site_blocks_icon( $option['icon'], 22 ); ?>
							</span>
							<div class="sg-ajax-monitoring__feature-body">
								<h3 class="sg-ajax-monitoring__feature-title"><?php echo esc_html( $option['title'] ); ?></h3>
								<p class="sg-ajax-monitoring__feature-desc"><?php echo esc_html( $option['description'] ); ?></p>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<figure class="sg-ajax-monitoring__media">
				<img
					class="sg-ajax-monitoring__img"
					src="<?php echo esc_url( $monitor_img ); ?>"
					alt="<?php esc_attr_e( 'Ajax app on a phone with Hub and sensors in a modern home', 'site-blocks' ); ?>"
					width="624"
					height="780"
					loading="lazy"
					decoding="async"
				/>
			</figure>
		</div>
	</div>
</section>
