<?php
/**
 * Missing-page layout with the Safeguard header and footer.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="sg-page alignfull">
	<?php
	if ( function_exists( 'site_blocks_render_safeguard_header' ) ) {
		site_blocks_render_safeguard_header();
	}
	?>
	<main id="main" class="sg-main">
		<section class="sg-404" aria-labelledby="sg-404-title">
			<div class="sg-container">
				<p class="sg-404__kicker"><?php esc_html_e( '404', 'site-blocks' ); ?></p>
				<h1 id="sg-404-title"><?php esc_html_e( "This Page Isn't Here", 'site-blocks' ); ?></h1>
				<p><?php esc_html_e( 'The link may be out of date, or the page has moved.', 'site-blocks' ); ?></p>
				<div class="sg-404__actions">
					<a class="sg-btn sg-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the homepage', 'site-blocks' ); ?></a>
					<a class="sg-btn sg-btn--secondary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'site-blocks' ); ?></a>
				</div>
			</div>
		</section>
	</main>
	<?php
	if ( function_exists( 'site_blocks_render_safeguard_footer' ) ) {
		site_blocks_render_safeguard_footer();
	}
	if ( function_exists( 'site_blocks_render_safeguard_mobile_bar' ) ) {
		site_blocks_render_safeguard_mobile_bar();
	}
	?>
</div>
<?php
get_footer();
