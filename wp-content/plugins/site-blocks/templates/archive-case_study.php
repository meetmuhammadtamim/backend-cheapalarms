<?php
/**
 * Case study archive.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="sg-page alignfull sg-study-page">
	<?php
	if ( function_exists( 'site_blocks_render_safeguard_header' ) ) {
		site_blocks_render_safeguard_header();
	}
	?>
	<main id="main" class="sg-main">
		<div class="sg-study__wrap sg-study-archive">
			<p class="sg-study-archive__kicker"><?php esc_html_e( 'Safeguard', 'site-blocks' ); ?></p>
			<h1><?php echo esc_html( is_tax( 'case_study_hub' ) ? single_term_title( '', false ) : __( 'Case studies', 'site-blocks' ) ); ?></h1>
			<p><?php esc_html_e( 'Enterprise and residential work, written so the building is not identified.', 'site-blocks' ); ?></p>
			<?php if ( have_posts() ) : ?>
				<ul class="sg-study-archive__list" role="list">
					<?php
					while ( have_posts() ) :
						the_post();
						$hubs = get_the_terms( get_the_ID(), 'case_study_hub' );
						$hub  = ( is_array( $hubs ) && isset( $hubs[0] ) && $hubs[0] instanceof WP_Term ) ? $hubs[0]->name : '';
						?>
						<li>
							<a href="<?php the_permalink(); ?>">
								<?php
								if ( has_post_thumbnail() ) {
									the_post_thumbnail( 'large', array( 'alt' => '' ) );
								}
								?>
								<div>
									<?php if ( '' !== $hub ) : ?>
										<span><?php echo esc_html( $hub ); ?></span>
									<?php endif; ?>
									<strong><?php the_title(); ?></strong>
								</div>
							</a>
						</li>
					<?php endwhile; ?>
				</ul>
			<?php else : ?>
				<p><?php esc_html_e( 'Case studies are being prepared.', 'site-blocks' ); ?></p>
			<?php endif; ?>
		</div>
	</main>
	<?php
	if ( function_exists( 'site_blocks_render_safeguard_footer' ) ) {
		site_blocks_render_safeguard_footer();
	}
	?>
</div>
<?php
get_footer();
