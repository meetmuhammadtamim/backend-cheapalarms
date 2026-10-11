<?php
/**
 * Ajax Alarm Systems — featured products (Hub / StreetSiren / DoorProtect).
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

$calc_url = home_url( '/contact/' );

$products = array(
	array(
		'title'       => __( 'Ajax Hub', 'site-blocks' ),
		'tag'         => __( 'Hub Control', 'site-blocks' ),
		'description' => __( 'The brain of your system. Manages devices, automations and encrypted communications.', 'site-blocks' ),
		'image'       => 'images/ajax/products/hub.webp',
		'icon'        => 'cpu',
		'modifier'    => 'hub',
		'alt'         => __( 'Ajax Hub installed in a neat equipment cabinet', 'site-blocks' ),
	),
	array(
		'title'       => __( 'Ajax StreetSiren', 'site-blocks' ),
		'tag'         => __( 'Indoor / Outdoor Alerting', 'site-blocks' ),
		'description' => __( 'Powerful siren with optional strobe to deter and alert instantly.', 'site-blocks' ),
		'image'       => 'images/ajax/products/streetsiren.webp',
		'icon'        => 'bell-ring',
		'modifier'    => 'siren',
		'alt'         => __( 'Ajax siren mounted on a wall in a modern home', 'site-blocks' ),
	),
	array(
		'title'       => __( 'Ajax DoorProtect', 'site-blocks' ),
		'tag'         => __( 'Perimeter Protection', 'site-blocks' ),
		'description' => __( 'Detects door and window opening in real time.', 'site-blocks' ),
		'image'       => 'images/ajax/products/doorprotect.webp',
		'icon'        => 'door-closed',
		'modifier'    => 'door',
		'alt'         => __( 'Ajax DoorProtect contact sensor on a window frame', 'site-blocks' ),
	),
);
?>
<section class="sg-ajax-section sg-ajax-products alignfull" aria-labelledby="sg-ajax-products-heading">
	<div class="sg-container">
		<div class="sg-ajax-products__layout">
			<div class="sg-ajax-products__intro">
				<p class="sg-ajax-section__eyebrow"><?php esc_html_e( 'The Products', 'site-blocks' ); ?></p>
				<h2 id="sg-ajax-products-heading" class="sg-ajax-section__title">
					<?php esc_html_e( 'Common Ajax components we may include in your design.', 'site-blocks' ); ?>
				</h2>
				<span class="sg-ajax-products__rule" aria-hidden="true"></span>
				<p class="sg-ajax-products__lead">
					<?php esc_html_e( 'Every property is different. The exact Ajax components we include depend on your layout, risks and lifestyle, ensuring you get intelligent protection that is reliable, discreet and easy to use.', 'site-blocks' ); ?>
				</p>
				<a class="sg-btn sg-btn--orange sg-ajax-products__cta" href="<?php echo esc_url( $calc_url ); ?>">
					<?php esc_html_e( 'Request a quote', 'site-blocks' ); ?>
					<span class="sg-btn__arrow" aria-hidden="true"><?php site_blocks_lucide_icon( 'arrow-right', 16 ); ?></span>
				</a>
			</div>

			<div class="sg-ajax-products__cards">
				<?php foreach ( $products as $product ) : ?>
					<?php
					$img_url = site_blocks_asset_url( $product['image'] );
					$mod     = sanitize_html_class( (string) $product['modifier'] );
					?>
					<article class="sg-ajax-product-card sg-ajax-product-card--<?php echo esc_attr( $mod ); ?>">
						<div class="sg-ajax-product-card__media">
							<img
								class="sg-ajax-product-card__img"
								src="<?php echo esc_url( $img_url ); ?>"
								alt="<?php echo esc_attr( $product['alt'] ); ?>"
								loading="lazy"
								decoding="async"
							/>
						</div>
						<div class="sg-ajax-product-card__body">
							<span class="sg-ajax-product-card__icon" aria-hidden="true">
								<?php site_blocks_lucide_icon( $product['icon'] ); ?>
							</span>
							<div class="sg-ajax-product-card__text">
								<h3 class="sg-ajax-product-card__title"><?php echo esc_html( $product['title'] ); ?></h3>
								<p class="sg-ajax-product-card__tag"><?php echo esc_html( $product['tag'] ); ?></p>
								<p class="sg-ajax-product-card__desc"><?php echo esc_html( $product['description'] ); ?></p>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
