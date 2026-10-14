<?php
/**
 * Case study. Project page, not the blog layout.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$post_id  = get_the_ID();
	$hubs     = get_the_terms( $post_id, 'case_study_hub' );
	$hub      = ( is_array( $hubs ) && isset( $hubs[0] ) && $hubs[0] instanceof WP_Term ) ? $hubs[0] : null;
	$minutes  = site_blocks_article_minutes( $post_id );
	$content  = apply_filters( 'the_content', (string) get_post_field( 'post_content', $post_id ) );
	$headings = site_blocks_article_collect_headings( $content );
	$archive  = get_post_type_archive_link( 'case_study' );
	if ( ! is_string( $archive ) || '' === $archive ) {
		$archive = home_url( '/case-studies/' );
	}
	?>
	<div class="sg-study-progress" aria-hidden="true"><span id="sg-read-progress"></span></div>
	<div class="sg-page alignfull sg-study-page">
		<?php
		if ( function_exists( 'site_blocks_render_safeguard_header' ) ) {
			site_blocks_render_safeguard_header();
		}
		?>
		<main id="main" class="sg-main">
			<article class="sg-study" id="sg-read" aria-labelledby="sg-study-title">
				<header class="sg-study__hero">
					<div class="sg-study__wrap sg-study__hero-grid">
						<div class="sg-study__hero-copy">
							<nav class="sg-study__crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'site-blocks' ); ?>">
								<ol role="list">
									<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'site-blocks' ); ?></a></li>
									<li><a href="<?php echo esc_url( $archive ); ?>"><?php esc_html_e( 'Case studies', 'site-blocks' ); ?></a></li>
									<?php if ( $hub instanceof WP_Term ) : ?>
										<li><a href="<?php echo esc_url( get_term_link( $hub ) ); ?>"><?php echo esc_html( $hub->name ); ?></a></li>
									<?php endif; ?>
									<li aria-current="page"><?php the_title(); ?></li>
								</ol>
							</nav>
							<p class="sg-study__eyebrow"><?php esc_html_e( 'Case study', 'site-blocks' ); ?></p>
							<h1 id="sg-study-title"><?php the_title(); ?></h1>
							<?php if ( has_excerpt() ) : ?>
								<p class="sg-study__dek"><?php echo esc_html( get_the_excerpt() ); ?></p>
							<?php endif; ?>
						</div>
						<?php if ( has_post_thumbnail() ) : ?>
							<figure class="sg-study__figure">
								<?php the_post_thumbnail( 'large', array( 'class' => 'sg-study__image', 'loading' => 'eager' ) ); ?>
							</figure>
						<?php endif; ?>
					</div>
				</header>

				<div class="sg-study__facts">
					<div class="sg-study__wrap sg-study__facts-row">
						<div>
							<?php site_blocks_icon( 'house', 20 ); ?>
							<div>
								<p><?php esc_html_e( 'Hub', 'site-blocks' ); ?></p>
								<strong><?php echo esc_html( $hub instanceof WP_Term ? $hub->name : __( 'Enterprise', 'site-blocks' ) ); ?></strong>
							</div>
						</div>
						<div>
							<?php site_blocks_icon( 'user', 20 ); ?>
							<div>
								<p><?php esc_html_e( 'Role', 'site-blocks' ); ?></p>
								<strong><?php esc_html_e( 'Installation partner', 'site-blocks' ); ?></strong>
							</div>
						</div>
						<div>
							<?php site_blocks_icon( 'calendar', 20 ); ?>
							<div>
								<p><?php esc_html_e( 'Published', 'site-blocks' ); ?></p>
								<strong><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></strong>
							</div>
						</div>
						<div>
							<?php site_blocks_icon( 'clock', 20 ); ?>
							<div>
								<p><?php esc_html_e( 'Read time', 'site-blocks' ); ?></p>
								<strong><?php echo esc_html( sprintf( /* translators: %d: minutes */ __( '%d min', 'site-blocks' ), $minutes ) ); ?></strong>
							</div>
						</div>
					</div>
				</div>

				<div class="sg-study__wrap sg-study__grid">
					<div class="sg-study__main">
						<?php if ( count( $headings ) > 1 ) : ?>
							<details class="sg-study__jump">
								<summary><?php esc_html_e( 'Jump to a section', 'site-blocks' ); ?></summary>
								<?php site_blocks_article_toc_list( $headings ); ?>
							</details>
						<?php endif; ?>
						<div class="sg-study__content entry-content">
							<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered post content. ?>
						</div>
					</div>
					<aside class="sg-study__brief">
						<img class="sg-study__brief-logo" src="<?php echo esc_url( site_blocks_asset_url( 'images/brand/safeguard-logo.png' ) ); ?>" alt="<?php esc_attr_e( 'Safeguard Security Services', 'site-blocks' ); ?>" width="168" height="48" />
						<p class="sg-study__brief-label"><?php esc_html_e( 'Project brief', 'site-blocks' ); ?></p>
						<h2><?php echo esc_html( $hub instanceof WP_Term ? $hub->name : __( 'Enterprise', 'site-blocks' ) ); ?></h2>
						<p><?php esc_html_e( 'The building is not named. Photos are hardware only.', 'site-blocks' ); ?></p>
						<a class="sg-study__brief-phone" href="tel:1300225276">
							<?php site_blocks_icon( 'phone', 20 ); ?>
							<span>1300 225 276</span>
						</a>
						<p class="sg-study__brief-note"><?php esc_html_e( 'Speak to our team', 'site-blocks' ); ?></p>
						<a class="sg-btn sg-btn--orange sg-btn--sm" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
							<?php esc_html_e( 'Start a similar job', 'site-blocks' ); ?>
							<?php site_blocks_icon( 'arrow-right', 18 ); ?>
						</a>
					</aside>
				</div>
			</article>
		</main>
		<?php
		if ( function_exists( 'site_blocks_render_safeguard_footer' ) ) {
			site_blocks_render_safeguard_footer();
		}
		?>
	</div>
	<?php
endwhile;

get_footer();
