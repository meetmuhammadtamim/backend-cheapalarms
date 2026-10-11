<?php
/**
 * Blog post. HubSpot-style reading layout. Case studies do not use this file.
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
	$post_id    = get_the_ID();
	$categories = get_the_category();
	$category   = ( is_array( $categories ) && isset( $categories[0] ) && $categories[0] instanceof WP_Term ) ? $categories[0] : null;
	$minutes    = site_blocks_article_minutes( $post_id );
	$content    = apply_filters( 'the_content', (string) get_post_field( 'post_content', $post_id ) );
	$headings   = site_blocks_article_collect_headings( $content );
	$insights   = get_permalink( (int) get_option( 'page_for_posts' ) );
	if ( ! is_string( $insights ) || '' === $insights ) {
		$insights = home_url( '/' );
	}
	$author  = get_the_author() ? get_the_author() : __( 'Safeguard Security', 'site-blocks' );
	$related = site_blocks_article_related( $post_id, 'post' );
	$share   = rawurlencode( get_permalink() );
	$title_q = rawurlencode( wp_strip_all_tags( get_the_title() ) );
	?>
	<div class="sg-blog-progress" aria-hidden="true"><span id="sg-read-progress"></span></div>
	<div class="sg-page alignfull sg-blog-page">
		<?php
		if ( function_exists( 'site_blocks_render_safeguard_header' ) ) {
			site_blocks_render_safeguard_header();
		}
		?>
		<main id="main" class="sg-main">
			<article class="sg-blog" id="sg-read" aria-labelledby="sg-blog-title">
				<div class="sg-blog__grid">
					<div class="sg-blog__main">
						<nav class="sg-blog__crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'site-blocks' ); ?>">
							<ol role="list">
								<li>
									<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
										<?php site_blocks_lucide_icon( 'house', 16 ); ?>
										<?php esc_html_e( 'Home', 'site-blocks' ); ?>
									</a>
								</li>
								<li><a href="<?php echo esc_url( $insights ); ?>"><?php esc_html_e( 'Insights', 'site-blocks' ); ?></a></li>
								<?php if ( $category instanceof WP_Term ) : ?>
									<li><a href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a></li>
								<?php endif; ?>
								<li aria-current="page"><?php the_title(); ?></li>
							</ol>
						</nav>

						<?php if ( $category instanceof WP_Term ) : ?>
							<a class="sg-eyebrow sg-eyebrow--orange sg-blog__eyebrow" href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
						<?php endif; ?>

						<h1 id="sg-blog-title" class="sg-blog__title"><?php the_title(); ?></h1>

						<div class="sg-blog__byline">
							<div class="sg-blog__author">
								<?php echo get_avatar( (int) get_the_author_meta( 'ID' ), 48, '', $author, array( 'class' => 'sg-blog__avatar' ) ); ?>
								<p class="sg-blog__name"><?php echo esc_html( $author ); ?></p>
								<p class="sg-blog__meta">
									<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
									<span aria-hidden="true">&middot;</span>
									<span><?php echo esc_html( sprintf( /* translators: %d: minutes */ __( '%d min read', 'site-blocks' ), $minutes ) ); ?></span>
								</p>
							</div>
							<div class="sg-blog__share sg-blog__share--inline">
								<span class="sg-blog__share-label"><?php esc_html_e( 'Share', 'site-blocks' ); ?></span>
								<a href="<?php echo esc_url( 'https://www.linkedin.com/sharing/share-offsite/?url=' . $share ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'site-blocks' ); ?>">
									<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
								</a>
								<a href="<?php echo esc_url( 'https://twitter.com/intent/tweet?url=' . $share . '&text=' . $title_q ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on X', 'site-blocks' ); ?>">
									<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4l6.5 8.2L4.4 20H6.6l4.7-5.8L15.6 20H20l-6.8-8.6L19.4 4h-2.2l-4.3 5.4L8.4 4H4z"/></svg>
								</a>
								<button type="button" data-sg-copy="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php esc_attr_e( 'Copy link', 'site-blocks' ); ?>">
									<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
								</button>
								<a href="<?php echo esc_url( 'mailto:?subject=' . rawurlencode( wp_strip_all_tags( get_the_title() ) ) . '&body=' . rawurlencode( (string) get_permalink() ) ); ?>" aria-label="<?php esc_attr_e( 'Share by email', 'site-blocks' ); ?>">
									<?php site_blocks_lucide_icon( 'mail', 16 ); ?>
								</a>
							</div>
						</div>

						<?php if ( has_post_thumbnail() ) : ?>
							<figure class="sg-blog__figure">
								<?php the_post_thumbnail( 'large', array( 'class' => 'sg-blog__image', 'loading' => 'eager' ) ); ?>
							</figure>
						<?php endif; ?>

						<?php if ( count( $headings ) > 1 ) : ?>
							<details class="sg-blog__toc-mobile">
								<summary><?php esc_html_e( 'On this page', 'site-blocks' ); ?></summary>
								<?php site_blocks_article_toc_list( $headings ); ?>
							</details>
						<?php endif; ?>

						<div class="sg-blog__content entry-content" id="sg-read-content">
							<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered post content. ?>
						</div>
					</div>

					<aside class="sg-blog__rail">
						<?php if ( count( $headings ) > 1 ) : ?>
							<nav class="sg-blog__toc" aria-label="<?php esc_attr_e( 'On this page', 'site-blocks' ); ?>">
								<p><?php esc_html_e( 'On this page', 'site-blocks' ); ?></p>
								<?php site_blocks_article_toc_list( $headings ); ?>
							</nav>
						<?php endif; ?>

						<aside class="sg-blog__card">
							<img class="sg-blog__card-logo" src="<?php echo esc_url( site_blocks_asset_url( 'images/brand/safeguard-logo.png' ) ); ?>" alt="<?php esc_attr_e( 'Safeguard Security Services', 'site-blocks' ); ?>" width="168" height="48" />
							<p class="sg-blog__card-licence"><?php esc_html_e( 'Master Licence #000103619', 'site-blocks' ); ?></p>
							<a class="sg-blog__card-phone" href="tel:1300225276">
								<?php site_blocks_lucide_icon( 'phone', 20 ); ?>
								<span>1300 225 276</span>
							</a>
							<p class="sg-blog__card-note"><?php esc_html_e( 'Speak to our team', 'site-blocks' ); ?></p>
							<a class="sg-btn sg-btn--orange sg-btn--sm sg-blog__card-btn" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
								<?php esc_html_e( 'Send a message', 'site-blocks' ); ?>
								<?php site_blocks_lucide_icon( 'arrow-right', 18 ); ?>
							</a>
							<p class="sg-blog__card-proof">
								<?php site_blocks_lucide_icon( 'shield-check', 18 ); ?>
								<span><?php esc_html_e( 'ASIAL member', 'site-blocks' ); ?></span>
							</p>
						</aside>

						<?php if ( $related ) : ?>
							<div class="sg-blog__related">
								<p><?php esc_html_e( 'More from the blog', 'site-blocks' ); ?></p>
								<ul role="list">
									<?php foreach ( $related as $item ) : ?>
										<li>
											<a href="<?php echo esc_url( get_permalink( $item ) ); ?>">
												<?php if ( has_post_thumbnail( $item ) ) : ?>
													<?php echo get_the_post_thumbnail( $item, 'medium', array( 'alt' => '' ) ); ?>
												<?php endif; ?>
												<span><?php echo esc_html( get_the_title( $item ) ); ?></span>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
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
