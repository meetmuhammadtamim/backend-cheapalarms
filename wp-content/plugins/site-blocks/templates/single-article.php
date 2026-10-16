<?php
/**
 * Shared reading template for posts and case studies.
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
	$is_study   = ( 'case_study' === get_post_type() );
	$hubs       = $is_study ? get_the_terms( get_the_ID(), 'case_study_hub' ) : array();
	$hub        = ( is_array( $hubs ) && isset( $hubs[0] ) && $hubs[0] instanceof WP_Term ) ? $hubs[0] : null;
	$categories = $is_study ? array() : get_the_category();
	$kicker     = $is_study
		? __( 'Case study', 'site-blocks' )
		: ( ( is_array( $categories ) && isset( $categories[0] ) ) ? $categories[0]->name : __( 'Insights', 'site-blocks' ) );
	$words      = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', get_the_ID() ) ) );
	$minutes    = max( 1, (int) round( $words / 220 ) );
	$content    = apply_filters( 'the_content', get_post_field( 'post_content', get_the_ID() ) );
	$headings   = site_blocks_article_collect_headings( $content );
	$archive    = $is_study ? site_blocks_case_studies_url() : site_blocks_blog_url();
	if ( ! is_string( $archive ) || '' === $archive ) {
		$archive = $is_study ? home_url( '/case-studies/' ) : home_url( '/' );
	}
	$related = site_blocks_article_related( get_the_ID(), get_post_type() );
	?>
	<div class="sg-read-progress" aria-hidden="true"><span id="sg-read-progress"></span></div>
	<div class="sg-page alignfull sg-read-page">
		<?php
		if ( function_exists( 'site_blocks_render_safeguard_header' ) ) {
			site_blocks_render_safeguard_header();
		}
		?>
		<main id="main" class="sg-main">
			<article class="sg-read" id="sg-read" aria-labelledby="sg-read-title">
				<header class="sg-read__hero">
					<div class="sg-container sg-read__measure">
						<nav class="sg-read__crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'site-blocks' ); ?>">
							<ol role="list">
								<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'site-blocks' ); ?></a></li>
								<li><a href="<?php echo esc_url( $archive ); ?>"><?php echo esc_html( $is_study ? __( 'Case studies', 'site-blocks' ) : __( 'Insights', 'site-blocks' ) ); ?></a></li>
								<?php if ( $hub instanceof WP_Term ) : ?>
									<li><a href="<?php echo esc_url( get_term_link( $hub ) ); ?>"><?php echo esc_html( $hub->name ); ?></a></li>
								<?php endif; ?>
								<li aria-current="page"><?php the_title(); ?></li>
							</ol>
						</nav>
						<p class="sg-read__kicker"><?php echo esc_html( $kicker ); ?></p>
						<h1 id="sg-read-title" class="sg-read__title"><?php the_title(); ?></h1>
						<?php if ( has_excerpt() ) : ?>
							<p class="sg-read__dek"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
						<div class="sg-read__meta">
							<span><?php echo esc_html( get_the_author() ? get_the_author() : __( 'Safeguard Security', 'site-blocks' ) ); ?></span>
							<span aria-hidden="true">&middot;</span>
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
							<span aria-hidden="true">&middot;</span>
							<span><?php echo esc_html( sprintf( /* translators: %d: minutes */ __( '%d min read', 'site-blocks' ), $minutes ) ); ?></span>
						</div>
					</div>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="sg-read__figure">
						<?php the_post_thumbnail( 'large', array( 'class' => 'sg-read__image', 'loading' => 'eager' ) ); ?>
					</figure>
				<?php endif; ?>

				<div class="sg-container sg-read__layout">
					<div class="sg-read__main">
						<?php if ( count( $headings ) > 1 ) : ?>
							<details class="sg-read__toc sg-read__toc--mobile">
								<summary><?php esc_html_e( 'On this page', 'site-blocks' ); ?></summary>
								<?php site_blocks_article_toc_list( $headings ); ?>
							</details>
						<?php endif; ?>

						<div class="sg-read__content entry-content" id="sg-read-content">
							<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered post content. ?>
						</div>

						<footer class="sg-read__end">
							<p class="sg-read__end-kicker"><?php esc_html_e( 'Talk to Safeguard', 'site-blocks' ); ?></p>
							<h2><?php esc_html_e( 'Want This Looked At On Your Site?', 'site-blocks' ); ?></h2>
							<p><?php esc_html_e( 'Call 1300 225 276 or send a message. A person on the team replies.', 'site-blocks' ); ?></p>
							<a class="sg-read__button" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact the team', 'site-blocks' ); ?></a>
						</footer>

						<?php if ( $related ) : ?>
							<section class="sg-read__related" aria-label="<?php esc_attr_e( 'More to read', 'site-blocks' ); ?>">
								<h2><?php esc_html_e( 'More To Read', 'site-blocks' ); ?></h2>
								<ul role="list">
									<?php foreach ( $related as $item ) : ?>
										<li><a href="<?php echo esc_url( get_permalink( $item ) ); ?>"><?php echo esc_html( get_the_title( $item ) ); ?></a></li>
									<?php endforeach; ?>
								</ul>
							</section>
						<?php endif; ?>
					</div>

					<aside class="sg-read__rail">
						<?php if ( count( $headings ) > 1 ) : ?>
							<nav class="sg-read__toc sg-read__toc--desk" aria-label="<?php esc_attr_e( 'On this page', 'site-blocks' ); ?>">
								<p><?php esc_html_e( 'On this page', 'site-blocks' ); ?></p>
								<?php site_blocks_article_toc_list( $headings ); ?>
							</nav>
						<?php endif; ?>
						<div class="sg-read__card">
							<p><?php esc_html_e( 'Master Licence #000103619', 'site-blocks' ); ?></p>
							<a href="tel:1300225276"><?php esc_html_e( '1300 225 276', 'site-blocks' ); ?></a>
						</div>
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
