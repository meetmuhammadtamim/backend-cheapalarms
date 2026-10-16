<?php
/**
 * Blog cards and the homepage "Explore insights in our blog" section.
 *
 * The cards read real posts (latest first): featured image, date, title, a
 * three-line excerpt and the post's categories as tags. If there are no posts
 * the homepage section is left out rather than showing placeholders.
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render one grid of blog cards.
 *
 * @param array<int, WP_Post> $posts Posts to show.
 */
function site_blocks_render_blog_cards( array $posts ): void {
	if ( empty( $posts ) ) {
		return;
	}
	?>
	<ul class="sg-blog-cards" role="list" data-sg-stagger>
		<?php
		foreach ( $posts as $post ) :
			$thumb_id   = (int) get_post_thumbnail_id( $post );
			$categories = get_the_category( $post->ID );
			$excerpt    = wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post ) ), 24, '…' );
			?>
			<li class="sg-blog-card" data-sg-reveal>
				<a class="sg-blog-card__link" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
					<span class="sg-blog-card__media">
						<?php
						if ( $thumb_id ) {
							echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								$thumb_id,
								'large',
								false,
								array(
									'class'    => 'sg-blog-card__img',
									'alt'      => '',
									'loading'  => 'lazy',
									'decoding' => 'async',
									'sizes'    => '(min-width: 860px) 360px, (min-width: 560px) 46vw, 92vw',
								)
							);
						}
						?>
					</span>
					<time class="sg-blog-card__date" datetime="<?php echo esc_attr( get_the_date( 'c', $post ) ); ?>"><?php echo esc_html( get_the_date( 'j M Y', $post ) ); ?></time>
					<h3 class="sg-blog-card__title"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
					<?php if ( '' !== $excerpt ) : ?>
						<p class="sg-blog-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $categories ) ) : ?>
						<span class="sg-blog-card__tags">
							<?php foreach ( array_slice( $categories, 0, 3 ) as $category ) : ?>
								<span class="sg-blog-card__tag"><?php echo esc_html( $category->name ); ?></span>
							<?php endforeach; ?>
						</span>
					<?php endif; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

/**
 * Homepage section: heading, intro, the three latest posts, "View More".
 */
function site_blocks_render_blog_section(): void {
	$posts = get_posts(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
		)
	);

	if ( empty( $posts ) ) {
		return;
	}
	?>
		<section id="sg-blog" class="sg-blog" aria-labelledby="sg-blog-title">
			<div class="sg-container">
				<header class="sg-blog__head" data-sg-reveal>
					<h2 id="sg-blog-title" class="sg-blog__title"><?php esc_html_e( 'Explore insights in our blog', 'site-blocks' ); ?></h2>
					<p class="sg-blog__lead"><?php esc_html_e( 'Practical advice on CCTV, alarms, access control and monitoring from the Safeguard team.', 'site-blocks' ); ?></p>
				</header>

				<?php site_blocks_render_blog_cards( $posts ); ?>

				<p class="sg-blog__more" data-sg-reveal>
					<a class="sg-blog__more-link" href="<?php echo esc_url( site_blocks_blog_url() ); ?>">
						<span><?php esc_html_e( 'View More', 'site-blocks' ); ?></span>
						<?php site_blocks_icon( 'arrow-right', 20 ); ?>
					</a>
				</p>
			</div>
		</section>
	<?php
}
