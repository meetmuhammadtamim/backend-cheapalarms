<?php
/**
 * Blog index (/blogs/).
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

global $wp_query;
$blog_posts = is_array( $wp_query->posts ) ? $wp_query->posts : array();
?>
<div class="sg-page alignfull sg-blog-page">
	<?php
	if ( function_exists( 'site_blocks_render_safeguard_header' ) ) {
		site_blocks_render_safeguard_header();
	}
	?>
	<main id="main" class="sg-main">
		<section class="sg-blog sg-blog--index" aria-labelledby="sg-blog-title">
			<div class="sg-container">
				<header class="sg-blog__head">
					<h1 id="sg-blog-title" class="sg-blog__title"><?php esc_html_e( 'Explore insights in our blog', 'site-blocks' ); ?></h1>
					<p class="sg-blog__lead"><?php esc_html_e( 'Practical advice on CCTV, alarms, access control and monitoring from the Safeguard team.', 'site-blocks' ); ?></p>
				</header>

				<?php if ( ! empty( $blog_posts ) ) : ?>
					<?php site_blocks_render_blog_cards( $blog_posts ); ?>

					<?php
					$links = paginate_links(
						array(
							'base'      => trailingslashit( site_blocks_blog_url() ) . 'page/%#%/',
							'format'    => '',
							'current'   => max( 1, (int) get_query_var( 'paged' ) ),
							'total'     => (int) $wp_query->max_num_pages,
							'prev_text' => __( 'Previous', 'site-blocks' ),
							'next_text' => __( 'Next', 'site-blocks' ),
							'type'      => 'list',
						)
					);
					if ( is_string( $links ) && '' !== $links ) :
						?>
						<nav class="sg-blog__pagination" aria-label="<?php esc_attr_e( 'Blog pages', 'site-blocks' ); ?>"><?php echo $links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></nav>
					<?php endif; ?>
				<?php else : ?>
					<p class="sg-blog__empty"><?php esc_html_e( 'New articles are on their way.', 'site-blocks' ); ?></p>
				<?php endif; ?>
			</div>
		</section>
	</main>
	<?php
	if ( function_exists( 'site_blocks_render_safeguard_footer' ) ) {
		site_blocks_render_safeguard_footer();
	}
	?>
</div>
<?php
get_footer();
