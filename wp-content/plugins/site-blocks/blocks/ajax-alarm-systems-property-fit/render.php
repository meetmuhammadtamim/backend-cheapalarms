<?php
/**
 * Ajax Alarm Systems — property fit (homes + house-frame visual).
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

$calc_url   = home_url( '/contact/' );
$home_img   = site_blocks_asset_url( 'images/ajax/property/home-porch.webp' );

$checks = array(
	__( 'Front doors and windows', 'site-blocks' ),
	__( 'Outdoor alerting devices', 'site-blocks' ),
	__( 'App control and handover', 'site-blocks' ),
	__( 'Tailored for family homes', 'site-blocks' ),
);
?>
<section class="sg-ajax-section sg-ajax-property alignfull" aria-labelledby="sg-ajax-property-heading">
	<div class="sg-container">
		<div class="sg-ajax-property__layout">
			<div class="sg-ajax-property__copy">
				<p class="sg-ajax-section__eyebrow"><?php esc_html_e( 'Property Fit', 'site-blocks' ); ?></p>
				<h2 id="sg-ajax-property-heading" class="sg-ajax-section__title">
					<?php esc_html_e( 'Ajax systems designed for homes', 'site-blocks' ); ?>
				</h2>
				<p class="sg-ajax-property__lead">
					<?php esc_html_e( 'Safeguard designs Ajax alarm systems for residential properties, with layouts tailored to how your home is used.', 'site-blocks' ); ?>
				</p>

				<ul class="sg-ajax-property__checks" role="list">
					<?php foreach ( $checks as $item ) : ?>
						<li class="sg-ajax-property__check">
							<span class="sg-ajax-property__check-icon" aria-hidden="true">
								<?php site_blocks_lucide_icon( 'check', 16 ); ?>
							</span>
							<span class="sg-ajax-property__check-text"><?php echo esc_html( $item ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>

				<a class="sg-btn sg-btn--orange sg-ajax-property__cta" href="<?php echo esc_url( $calc_url ); ?>">
					<?php esc_html_e( 'Request a quote', 'site-blocks' ); ?>
					<span class="sg-btn__arrow" aria-hidden="true"><?php site_blocks_lucide_icon( 'arrow-right', 16 ); ?></span>
				</a>

				<p class="sg-ajax-property__note">
					<span class="sg-ajax-property__note-icon" aria-hidden="true">
						<?php site_blocks_lucide_icon( 'shield-check', 16 ); ?>
					</span>
					<?php esc_html_e( 'Designed around the property, not a generic kit.', 'site-blocks' ); ?>
				</p>
			</div>

			<figure class="sg-ajax-property__media">
				<svg class="sg-ajax-property__clip-defs" width="0" height="0" aria-hidden="true" focusable="false">
					<defs>
						<clipPath id="sg-ajax-property-house-clip" clipPathUnits="objectBoundingBox">
							<!-- House silhouette with soft rounded corners -->
							<path
								d="
									M 0.46 0.035
									Q 0.5 0.005 0.54 0.035
									L 0.955 0.245
									Q 1 0.26 1 0.3
									L 1 0.955
									Q 1 1 0.955 1
									L 0.045 1
									Q 0 1 0 0.955
									L 0 0.3
									Q 0 0.26 0.045 0.245
									Z
								"
							/>
						</clipPath>
					</defs>
				</svg>
				<div class="sg-ajax-property__house">
					<img
						class="sg-ajax-property__img"
						src="<?php echo esc_url( $home_img ); ?>"
						alt="<?php esc_attr_e( 'Ajax outdoor alerting device on a residential porch', 'site-blocks' ); ?>"
						width="1600"
						height="866"
						loading="lazy"
						decoding="async"
					/>
				</div>
			</figure>
		</div>
	</div>
</section>
