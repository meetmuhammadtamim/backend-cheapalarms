<?php
/**
 * Shared Safeguard site chrome (utility bar + header).
 *
 * @package Site_Blocks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Flatten dropdown / mega links from a nav group item.
 *
 * @param array<string, mixed> $item Nav item.
 * @return array<int, array<string, mixed>>
 */
function site_blocks_safeguard_nav_item_links( array $item ): array {
	if ( ! empty( $item['columns'] ) && is_array( $item['columns'] ) ) {
		$links = array();
		foreach ( $item['columns'] as $column ) {
			if ( empty( $column['links'] ) || ! is_array( $column['links'] ) ) {
				continue;
			}
			foreach ( $column['links'] as $link ) {
				if ( is_array( $link ) ) {
					$links[] = $link;
				}
			}
		}

		return $links;
	}

	$children = $item['children'] ?? array();

	return is_array( $children ) ? $children : array();
}

/**
 * Primary service nav, flat map (legacy / footer).
 *
 * @return array<string, string> Path => label.
 */
function site_blocks_get_safeguard_nav_services(): array {
	$flat = array();
	foreach ( site_blocks_get_safeguard_nav_items() as $item ) {
		if ( ( $item['type'] ?? '' ) === 'group' ) {
			$flat[ $item['path'] ] = $item['label'];
			foreach ( site_blocks_safeguard_nav_item_links( $item ) as $child ) {
				$flat[ $child['path'] ] = $child['label'];
			}
		} else {
			$flat[ $item['path'] ] = $item['label'];
		}
	}

	return $flat;
}

/**
 * Structured primary nav — silo hubs with spoke dropdowns.
 *
 * IA (hub → spokes). Product pages under Electronic are top-level URLs
 * but belong to that silo in nav. Monitoring is its own silo.
 *
 * @return array<int, array<string, mixed>>
 */
function site_blocks_get_safeguard_nav_items(): array {
	return array(
		array(
			'type'        => 'group',
			'label'       => __( 'Services', 'site-blocks' ),
			'short'       => __( 'Services', 'site-blocks' ),
			'path'        => '/physical-security/',
			'mega'        => true,
			'mega_layout' => 'feature',
			'heading'     => __( 'Our services', 'site-blocks' ),
			'cta_label'   => __( 'See all services', 'site-blocks' ),
			'children'    => array(
				array(
					'path'  => '/physical-security/',
					'label' => __( 'Physical Security', 'site-blocks' ),
					'icon'  => 'shield',
					'image' => 'images/physical-security/hub-hero.webp',
					'blurb' => __( 'Licensed guards and patrols for Greater Sydney sites, under one Master Licence.', 'site-blocks' ),
				),
				array(
					'path'  => '/physical-security/static-guards/',
					'label' => __( 'Static Security Guards', 'site-blocks' ),
					'icon'  => 'user',
					'image' => 'images/physical-security/static-hero.webp',
					'blurb' => __( 'On-site officers for reception, gates and floor presence during open hours.', 'site-blocks' ),
				),
				array(
					'path'  => '/physical-security/mobile-patrols/',
					'label' => __( 'Mobile Patrols', 'site-blocks' ),
					'icon'  => 'car',
					'image' => 'images/physical-security/mobile-hero.webp',
					'blurb' => __( 'Marked-vehicle checks and alarm response when a static guard is not on site.', 'site-blocks' ),
				),
				array(
					'path'  => '/manpower/',
					'label' => __( 'ManPower', 'site-blocks' ),
					'icon'  => 'users',
					'image' => 'images/manpower/hub-hero.webp',
					'blurb' => __( 'Extra licensed officers for events, absences and short-notice cover.', 'site-blocks' ),
				),
			),
		),
		array(
			'type'        => 'group',
			'label'       => __( 'Products', 'site-blocks' ),
			'short'       => __( 'Products', 'site-blocks' ),
			'path'        => '/electronic-security/',
			'mega'        => true,
			'mega_layout' => 'split',
			'columns'     => array(
				array(
					'heading' => __( 'Categories', 'site-blocks' ),
					'type'    => 'categories',
					'links'   => array(
						array(
							'path'  => '/alarm-systems/',
							'label' => __( 'Alarm Systems', 'site-blocks' ),
							'icon'  => 'bell-ring',
							'image' => 'images/alarm/hero.webp',
						),
						array(
							'path'  => '/cctv-security-cameras/',
							'label' => __( 'CCTV & Security Cameras', 'site-blocks' ),
							'icon'  => 'cctv',
							'image' => 'images/cctv/hero.webp',
						),
						array(
							'path'  => '/access-control/',
							'label' => __( 'Access Control', 'site-blocks' ),
							'icon'  => 'id-card',
							'image' => 'images/access-control/hero.webp',
						),
						array(
							'path'  => '/intercom-systems/',
							'label' => __( 'Intercom Systems', 'site-blocks' ),
							'icon'  => 'smartphone',
							'image' => 'images/intercom/hero.webp',
						),
						array(
							'path'  => '/monitoring/back-to-base/',
							'label' => __( 'Back-to-Base Monitoring', 'site-blocks' ),
							'icon'  => 'radio-tower',
							'image' => 'images/monitoring/back-to-base-hero.webp',
						),
						array(
							'path'  => '/monitoring/virtual-patrol/',
							'label' => __( 'Virtual Patrol', 'site-blocks' ),
							'icon'  => 'eye',
							'image' => 'images/monitoring/virtual-patrol-hero.webp',
						),
					),
				),
				array(
					'heading' => __( 'Brands', 'site-blocks' ),
					'type'    => 'brands',
					'links'   => array(
						array(
							'path'  => '/ajax-alarm-systems/',
							'label' => 'Ajax',
							'logo'  => 'images/brands/ajax.webp',
						),
						array(
							'path'  => '/paradox-m25/',
							'label' => 'Paradox',
							'logo'  => 'images/brands/paradox.webp',
						),
						array(
							'path'  => '/milesight/',
							'label' => 'Milesight',
						),
						array(
							'path'  => '/nx-witness/',
							'label' => 'NX Witness',
						),
						array(
							'path'  => '/hanwha-vision/',
							'label' => 'Hanwha Vision',
						),
						array(
							'path'  => '/akuvox/',
							'label' => 'Akuvox',
							'logo'  => 'images/brands/akuvox.webp',
						),
					),
				),
			),
		),
		array(
			'type'        => 'group',
			'label'       => __( 'Monitoring', 'site-blocks' ),
			'short'       => __( 'Monitoring', 'site-blocks' ),
			'path'        => '/monitoring/',
			'mega'        => true,
			'mega_layout' => 'feature',
			'heading'     => __( 'Monitoring', 'site-blocks' ),
			'cta_label'   => __( 'See all monitoring', 'site-blocks' ),
			'children'    => array(
				array(
					'path'  => '/monitoring/back-to-base/',
					'label' => __( 'Back-to-Base Monitoring', 'site-blocks' ),
					'icon'  => 'radio-tower',
					'image' => 'images/monitoring/back-to-base-hero.webp',
					'blurb' => __( 'Alarm monitoring with response arrangements agreed for the site.', 'site-blocks' ),
				),
				array(
					'path'  => '/monitoring/virtual-patrol/',
					'label' => __( 'Virtual Patrol', 'site-blocks' ),
					'icon'  => 'eye',
					'image' => 'images/monitoring/virtual-patrol-hero.webp',
					'blurb' => __( 'Scheduled camera checks, so sites are checked, not only recorded.', 'site-blocks' ),
				),
				array(
					'path'  => '/monitoring/solar-cameras-monitoring/',
					'label' => __( 'Solar Cameras with Monitoring', 'site-blocks' ),
					'icon'  => 'sun',
					'image' => 'images/monitoring/solar-monitoring-hero.webp',
					'blurb' => __( 'Off-grid solar cameras with 4G monitoring for yards, farms and remote sites.', 'site-blocks' ),
				),
			),
		),
		array(
			'type'        => 'group',
			'label'       => __( 'Enterprise', 'site-blocks' ),
			'short'       => __( 'Enterprise', 'site-blocks' ),
			'path'        => '/enterprise-solutions/',
			'mega'        => true,
			'mega_layout' => 'feature',
			'heading'     => __( 'Enterprise', 'site-blocks' ),
			'cta_label'   => __( 'See all enterprise', 'site-blocks' ),
			'children'    => array(
				array(
					'path'  => '/enterprise-solutions/',
					'label' => __( 'Enterprise Solutions', 'site-blocks' ),
					'icon'  => 'building-2',
					'image' => 'images/enterprise/hub-hero.webp',
					'blurb' => __( 'Multi-site CCTV, access control and monitoring designed for commercial operations.', 'site-blocks' ),
				),
				array(
					'path'  => '/safeguard-solutions/',
					'label' => __( 'Safeguard Solutions', 'site-blocks' ),
					'icon'  => 'monitor',
					'image' => 'images/enterprise/safeguard-solutions-hero.webp',
					'blurb' => __( 'Cloud console for live video, access events, alerts and multi-site control.', 'site-blocks' ),
				),
			),
		),
		array(
			'path'  => '/contact/',
			'label' => __( 'Contact', 'site-blocks' ),
		),
	);
}

/**
 * Whether a nav item or any child matches the current page.
 *
 * @param array<string, mixed> $item Nav item.
 */
function site_blocks_safeguard_item_is_current( array $item ): bool {
	$path = trim( (string) ( $item['path'] ?? '' ), '/' );

	if ( '' !== $path && is_page( $path ) ) {
		return true;
	}

	foreach ( site_blocks_safeguard_nav_item_links( $item ) as $child ) {
		if ( is_array( $child ) && site_blocks_safeguard_item_is_current( $child ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Whether a nav path (or its silo children) is the current page.
 */
function site_blocks_safeguard_nav_is_current( string $path, bool $include_children = false ): bool {
	$slug = trim( $path, '/' );

	if ( is_page( $slug ) ) {
		return true;
	}

	if ( ! $include_children ) {
		return false;
	}

	foreach ( site_blocks_get_safeguard_nav_items() as $item ) {
		if ( (string) ( $item['path'] ?? '' ) === $path ) {
			return site_blocks_safeguard_item_is_current( $item );
		}
	}

	return false;
}

/**
 * @param array<string, mixed> $item Nav item.
 */
function site_blocks_render_safeguard_nav_link( array $item, string $class = '' ): void {
	$path       = (string) ( $item['path'] ?? '' );
	$label      = (string) ( $item['label'] ?? '' );
	$short      = isset( $item['short'] ) ? (string) $item['short'] : '';
	$is_current = site_blocks_safeguard_nav_is_current( $path, false );
	$attrs      = $is_current ? ' aria-current="page"' : '';
	$extra      = $class !== '' ? ' class="' . esc_attr( $class ) . '"' : '';
	$title      = $short !== '' && $short !== $label ? ' title="' . esc_attr( $label ) . '"' : '';

	printf(
		'<a href="%s"%s%s%s>',
		esc_url( home_url( $path ) ),
		$extra,
		$attrs,
		$title
	);
	if ( $short !== '' ) {
		printf(
			'<span class="sg-header__nav-label sg-header__nav-label--long">%s</span><span class="sg-header__nav-label sg-header__nav-label--short">%s</span>',
			esc_html( $label ),
			esc_html( $short )
		);
	} else {
		echo esc_html( $label );
	}
	echo '</a>';
}

/**
 * Render a Products-style category card for simple dropdowns.
 *
 * @param array<string, mixed> $child      Link config.
 * @param bool                 $has_nested Whether this row opens a nested flyout.
 */
function site_blocks_render_safeguard_simple_menu_link( array $child, bool $has_nested = false ): void {
	$is_current = $has_nested
		? site_blocks_safeguard_item_is_current( $child )
		: site_blocks_safeguard_nav_is_current( (string) ( $child['path'] ?? '' ), false );
	$attrs = $is_current ? ' aria-current="page"' : '';
	$class = 'sg-header__mega-category';
	if ( $has_nested ) {
		$class .= ' sg-header__mega-category--branch';
	}
	?>
	<a
		class="<?php echo esc_attr( $class ); ?>"
		href="<?php echo esc_url( home_url( (string) ( $child['path'] ?? '' ) ) ); ?>"
		<?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	>
		<span class="sg-header__mega-category-label"><?php echo esc_html( (string) ( $child['label'] ?? '' ) ); ?></span>
		<?php site_blocks_render_mega_category_thumb( $child ); ?>
	</a>
	<?php
}

/**
 * Render a simple dropdown, with nested flyouts for silo spokes.
 *
 * @param array<int, array<string, mixed>> $children Links.
 * @param string                           $menu_id  Optional id for the top menu.
 * @param bool                             $submenu  Whether this is a nested flyout.
 */
function site_blocks_render_safeguard_simple_menu( array $children, string $menu_id = '', bool $submenu = false ): void {
	$wrap_class = $submenu ? 'sg-header__nav-submenu' : 'sg-header__nav-menu sg-header__nav-menu--simple';
	$id         = '' !== $menu_id ? ' id="' . esc_attr( $menu_id ) . '"' : '';
	?>
	<div class="<?php echo esc_attr( $wrap_class ); ?>"<?php echo $id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $id is escaped. ?>>
		<div class="sg-header__mega-panel sg-header__mega-panel--simple">
			<ul class="sg-header__simple-list" role="list">
				<?php foreach ( $children as $child ) : ?>
					<?php
					if ( ! is_array( $child ) ) {
						continue;
					}

					$nested     = $child['children'] ?? array();
					$has_nested = is_array( $nested ) && array() !== $nested;
					?>
					<?php if ( $has_nested ) : ?>
						<li class="sg-header__nav-menu-item sg-header__nav-menu-item--nested">
							<?php site_blocks_render_safeguard_simple_menu_link( $child, true ); ?>
							<?php site_blocks_render_safeguard_simple_menu( $nested, '', true ); ?>
						</li>
					<?php else : ?>
						<li class="sg-header__nav-menu-item">
							<?php site_blocks_render_safeguard_simple_menu_link( $child, false ); ?>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<?php
}

/**
 * Render nested mobile nav links for a silo group.
 *
 * @param array<int, array<string, mixed>> $children Links.
 * @param int                              $depth    Nesting depth.
 */
function site_blocks_render_safeguard_mobile_children( array $children, int $depth = 0 ): void {
	foreach ( $children as $child ) {
		if ( ! is_array( $child ) ) {
			continue;
		}

		$class         = 0 === $depth ? 'sg-mobile-nav__child' : 'sg-mobile-nav__grandchild';
		$child_current = site_blocks_safeguard_item_is_current( $child );
		$child_attrs   = $child_current ? ' aria-current="page"' : '';
		?>
		<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( home_url( (string) ( $child['path'] ?? '' ) ) ); ?>"<?php echo $child_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php echo esc_html( (string) ( $child['label'] ?? '' ) ); ?>
		</a>
		<?php
		$nested = $child['children'] ?? array();
		if ( is_array( $nested ) && array() !== $nested ) {
			site_blocks_render_safeguard_mobile_children( $nested, $depth + 1 );
		}
	}
}

/**
 * Whether a managed mega-menu thumbnail exists on disk or in the media library.
 */
function site_blocks_mega_image_available( string $relative_path ): bool {
	$relative_path = ltrim( str_replace( '\\', '/', $relative_path ), '/' );

	if ( function_exists( 'site_blocks_media_source_exists' ) && site_blocks_media_source_exists( $relative_path ) ) {
		return true;
	}

	return is_readable( SITE_BLOCKS_DIR . 'assets/' . $relative_path );
}

/**
 * Render circular category thumbnail (photo or icon fallback).
 *
 * @param array<string, mixed> $link Category link config.
 */
function site_blocks_render_mega_category_thumb( array $link ): void {
	$image = isset( $link['image'] ) ? (string) $link['image'] : '';
	$icon  = isset( $link['icon'] ) ? (string) $link['icon'] : '';
	$label = (string) ( $link['label'] ?? '' );

	if ( '' !== $image && site_blocks_mega_image_available( $image ) ) {
		printf(
			'<img class="sg-header__mega-category-thumb" src="%s" alt="" width="50" height="50" loading="lazy" decoding="async" />',
			esc_url( site_blocks_asset_url( $image ) )
		);
		return;
	}

	echo '<span class="sg-header__mega-category-thumb sg-header__mega-category-thumb--icon" aria-hidden="true">';
	if ( '' !== $icon ) {
		site_blocks_lucide_icon( $icon, 18 );
	}
	echo '</span>';
}

/**
 * Render preview mega menu (links left, hover image right).
 *
 * @param array<string, mixed> $item     Nav group item.
 * @param string               $group_id Panel id.
 */
function site_blocks_render_safeguard_preview_mega( array $item, string $group_id ): void {
	$children = $item['children'] ?? array();
	if ( empty( $children ) || ! is_array( $children ) ) {
		return;
	}

	$first = null;
	foreach ( $children as $child ) {
		if ( is_array( $child ) ) {
			$first = $child;
			break;
		}
	}

	if ( ! is_array( $first ) ) {
		return;
	}

	$first_label = (string) ( $first['label'] ?? '' );
	$first_image = isset( $first['image'] ) ? (string) $first['image'] : '';
	$first_src   = ( '' !== $first_image && site_blocks_mega_image_available( $first_image ) )
		? site_blocks_asset_url( $first_image )
		: '';
	?>
	<div
		class="sg-header__nav-menu sg-header__nav-menu--mega sg-header__nav-menu--preview"
		id="<?php echo esc_attr( $group_id ); ?>"
		role="region"
		aria-label="<?php echo esc_attr( (string) ( $item['label'] ?? '' ) ); ?>"
	>
		<div class="sg-header__mega-panel">
			<div class="sg-header__mega-preview">
				<ul class="sg-header__mega-preview-links" role="list">
					<?php foreach ( $children as $index => $child ) : ?>
						<?php
						if ( ! is_array( $child ) ) {
							continue;
						}
						$is_current = site_blocks_safeguard_nav_is_current( (string) ( $child['path'] ?? '' ), false );
						$is_first   = 0 === (int) $index;
						$label      = (string) ( $child['label'] ?? '' );
						$image      = isset( $child['image'] ) ? (string) $child['image'] : '';
						$src        = ( '' !== $image && site_blocks_mega_image_available( $image ) )
							? site_blocks_asset_url( $image )
							: '';
						$classes    = 'sg-header__mega-preview-link';
						if ( $is_first ) {
							$classes .= ' is-active';
						}
						$attrs = $is_current ? ' aria-current="page"' : '';
						?>
						<li>
							<a
								class="<?php echo esc_attr( $classes ); ?>"
								href="<?php echo esc_url( home_url( (string) ( $child['path'] ?? '' ) ) ); ?>"
								data-preview-src="<?php echo esc_url( $src ); ?>"
								data-preview-alt="<?php echo esc_attr( $label ); ?>"
								<?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							>
								<?php echo esc_html( $label ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="sg-header__mega-preview-media">
					<img
						class="sg-header__mega-preview-img"
						src="<?php echo esc_url( $first_src ); ?>"
						alt="<?php echo esc_attr( $first_label ); ?>"
						width="640"
						height="400"
						decoding="async"
						<?php echo '' === $first_src ? ' hidden' : ''; ?>
					/>
					<span class="sg-header__mega-preview-placeholder"<?php echo '' !== $first_src ? ' hidden' : ''; ?> aria-hidden="true"></span>
					<p class="sg-header__mega-preview-caption"><?php echo esc_html( $first_label ); ?></p>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render Vimeo-style feature mega (icon links left, featured story right).
 *
 * @param array<string, mixed> $item     Nav group item.
 * @param string               $group_id Panel id.
 */
function site_blocks_render_safeguard_feature_mega( array $item, string $group_id ): void {
	$children = $item['children'] ?? array();
	if ( empty( $children ) || ! is_array( $children ) ) {
		return;
	}

	$active = null;
	foreach ( $children as $child ) {
		if ( ! is_array( $child ) ) {
			continue;
		}
		if ( null === $active ) {
			$active = $child;
		}
		if ( site_blocks_safeguard_nav_is_current( (string) ( $child['path'] ?? '' ), false ) ) {
			$active = $child;
			break;
		}
	}

	if ( ! is_array( $active ) ) {
		return;
	}

	$active_label = (string) ( $active['label'] ?? '' );
	$active_blurb = (string) ( $active['blurb'] ?? '' );
	$active_path  = (string) ( $active['path'] ?? '' );
	$active_image = isset( $active['image'] ) ? (string) $active['image'] : '';
	$active_src   = ( '' !== $active_image && site_blocks_mega_image_available( $active_image ) )
		? site_blocks_asset_url( $active_image )
		: '';
	$heading      = (string) ( $item['heading'] ?? $item['label'] ?? '' );
	$cta_label    = (string) ( $item['cta_label'] ?? '' );
	$cta_path     = (string) ( $item['path'] ?? '' );
	?>
	<div
		class="sg-header__nav-menu sg-header__nav-menu--mega sg-header__nav-menu--feature"
		id="<?php echo esc_attr( $group_id ); ?>"
		role="region"
		aria-label="<?php echo esc_attr( (string) ( $item['label'] ?? '' ) ); ?>"
	>
		<div class="sg-header__mega-panel">
			<div class="sg-header__mega-feature">
				<div class="sg-header__mega-feature-main">
					<?php if ( '' !== $heading ) : ?>
						<h3 class="sg-header__mega-heading"><?php echo esc_html( $heading ); ?></h3>
					<?php endif; ?>
					<ul class="sg-header__mega-feature-links" role="list">
						<?php foreach ( $children as $child ) : ?>
							<?php
							if ( ! is_array( $child ) ) {
								continue;
							}
							$label      = (string) ( $child['label'] ?? '' );
							$path       = (string) ( $child['path'] ?? '' );
							$blurb      = (string) ( $child['blurb'] ?? '' );
							$icon       = (string) ( $child['icon'] ?? '' );
							$image      = isset( $child['image'] ) ? (string) $child['image'] : '';
							$src        = ( '' !== $image && site_blocks_mega_image_available( $image ) )
								? site_blocks_asset_url( $image )
								: '';
							$is_current = site_blocks_safeguard_nav_is_current( $path, false );
							$is_active  = $path === $active_path;
							$classes    = 'sg-header__mega-feature-link';
							if ( $is_active ) {
								$classes .= ' is-active';
							}
							$attrs = $is_current ? ' aria-current="page"' : '';
							?>
							<li>
								<a
									class="<?php echo esc_attr( $classes ); ?>"
									href="<?php echo esc_url( home_url( $path ) ); ?>"
									data-preview-src="<?php echo esc_url( $src ); ?>"
									data-preview-alt="<?php echo esc_attr( $label ); ?>"
									data-preview-blurb="<?php echo esc_attr( $blurb ); ?>"
									<?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								>
									<span class="sg-header__mega-feature-icon" aria-hidden="true">
										<?php
										if ( '' !== $icon ) {
											site_blocks_lucide_icon( $icon, 18 );
										}
										?>
									</span>
									<span class="sg-header__mega-feature-label"><?php echo esc_html( $label ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="sg-header__mega-feature-aside">
					<div class="sg-header__mega-feature-media">
						<img
							class="sg-header__mega-feature-img"
							src="<?php echo esc_url( $active_src ); ?>"
							alt=""
							width="640"
							height="400"
							decoding="async"
							<?php echo '' === $active_src ? ' hidden' : ''; ?>
						/>
						<span class="sg-header__mega-feature-placeholder"<?php echo '' !== $active_src ? ' hidden' : ''; ?> aria-hidden="true"></span>
					</div>
					<a class="sg-header__mega-feature-title" href="<?php echo esc_url( home_url( $active_path ) ); ?>">
						<span class="sg-header__mega-feature-title-text"><?php echo esc_html( $active_label ); ?></span>
						<?php site_blocks_lucide_icon( 'arrow-right', 16 ); ?>
					</a>
					<p class="sg-header__mega-feature-blurb"><?php echo esc_html( $active_blurb ); ?></p>
					<?php if ( '' !== $cta_label && '' !== $cta_path ) : ?>
						<a class="sg-header__mega-feature-cta" href="<?php echo esc_url( home_url( $cta_path ) ); ?>">
							<span><?php echo esc_html( $cta_label ); ?></span>
							<?php site_blocks_lucide_icon( 'arrow-right', 16 ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render split mega menu (categories + brands).
 *
 * @param array<string, mixed> $item     Nav group item.
 * @param string               $group_id Panel id.
 */
function site_blocks_render_safeguard_mega_menu( array $item, string $group_id ): void {
	$columns = $item['columns'] ?? array();
	if ( empty( $columns ) || ! is_array( $columns ) ) {
		return;
	}
	?>
	<div
		class="sg-header__nav-menu sg-header__nav-menu--mega sg-header__nav-menu--split"
		id="<?php echo esc_attr( $group_id ); ?>"
		role="region"
		aria-label="<?php echo esc_attr( (string) ( $item['label'] ?? '' ) ); ?>"
	>
		<div class="sg-header__mega-panel">
			<div class="sg-header__mega-layout">
				<?php foreach ( $columns as $column ) : ?>
					<?php
					$column_type = (string) ( $column['type'] ?? 'links' );
					$links       = $column['links'] ?? array();
					if ( empty( $links ) || ! is_array( $links ) ) {
						continue;
					}
					?>
					<div class="sg-header__mega-col sg-header__mega-col--<?php echo esc_attr( $column_type ); ?>">
						<h3 class="sg-header__mega-heading"><?php echo esc_html( (string) ( $column['heading'] ?? '' ) ); ?></h3>
						<?php if ( 'brands' === $column_type ) : ?>
							<ul class="sg-header__mega-brands" role="list">
								<?php foreach ( $links as $link ) : ?>
									<?php
									if ( ! is_array( $link ) ) {
										continue;
									}
									$is_current = site_blocks_safeguard_nav_is_current( (string) ( $link['path'] ?? '' ), false );
									$attrs      = $is_current ? ' aria-current="page"' : '';
									$logo       = isset( $link['logo'] ) ? (string) $link['logo'] : '';
									?>
									<li class="sg-header__mega-brand-item">
										<a
											class="sg-header__mega-brand"
											href="<?php echo esc_url( home_url( (string) ( $link['path'] ?? '' ) ) ); ?>"
											<?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										>
											<?php if ( '' !== $logo ) : ?>
												<img
													class="sg-header__mega-brand-logo"
													src="<?php echo esc_url( site_blocks_asset_url( $logo ) ); ?>"
													alt="<?php echo esc_attr( (string) ( $link['label'] ?? '' ) ); ?>"
													width="120"
													height="48"
													loading="lazy"
													decoding="async"
												/>
											<?php else : ?>
												<span class="sg-header__mega-brand-text"><?php echo esc_html( (string) ( $link['label'] ?? '' ) ); ?></span>
											<?php endif; ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php else : ?>
							<ul class="sg-header__mega-categories" role="list">
								<?php foreach ( $links as $link ) : ?>
									<?php
									if ( ! is_array( $link ) ) {
										continue;
									}
									$is_current = site_blocks_safeguard_nav_is_current( (string) ( $link['path'] ?? '' ), false );
									$attrs      = $is_current ? ' aria-current="page"' : '';
									?>
									<li class="sg-header__mega-category-item">
										<a
											class="sg-header__mega-category"
											href="<?php echo esc_url( home_url( (string) ( $link['path'] ?? '' ) ) ); ?>"
											<?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										>
											<span class="sg-header__mega-category-label"><?php echo esc_html( (string) ( $link['label'] ?? '' ) ); ?></span>
											<?php site_blocks_render_mega_category_thumb( $link ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Shared contact and CTA URLs.
 *
 * @return array<string, string>
 */
function site_blocks_get_safeguard_contact(): array {
	return array(
		'phone'      => '1300 225 276',
		'phone_href' => 'tel:1300225276',
		'email'      => 'sales@safeguardsecurity.com.au',
		'email_href' => 'mailto:sales@safeguardsecurity.com.au',
		'quote'      => esc_url( home_url( '/contact/' ) ),
	);
}

/**
 * Render utility bar and site header.
 */
function site_blocks_render_safeguard_header(): void {
	$logo      = site_blocks_asset_url( 'images/brand/safeguard-logo.png' );
	$logo_mark = site_blocks_asset_url( 'images/brand/safeguard-logo-mark.png' );
	$nav_items = site_blocks_get_safeguard_nav_items();
	$contact   = site_blocks_get_safeguard_contact();
	$phone     = $contact['phone'];
	$phone_h   = $contact['phone_href'];
	$email     = $contact['email'];
	$email_h   = $contact['email_href'];
	$quote     = $contact['quote'];
	?>
	<div class="sg-utility" role="complementary" aria-label="<?php esc_attr_e( 'Service information', 'site-blocks' ); ?>">
		<div class="sg-container sg-utility__inner">
			<span class="sg-utility__location"><?php esc_html_e( 'NSW Master Licence #000103619', 'site-blocks' ); ?></span>
			<div class="sg-utility__contacts">
				<a class="sg-utility__link" href="<?php echo esc_url( $email_h ); ?>">
					<?php site_blocks_lucide_icon( 'mail', 14 ); ?>
					<?php echo esc_html( $email ); ?>
				</a>
				<a class="sg-utility__link" href="<?php echo esc_attr( $phone_h ); ?>">
					<?php site_blocks_lucide_icon( 'phone', 14 ); ?>
					<?php echo esc_html( $phone ); ?>
				</a>
			</div>
		</div>
	</div>

	<header class="sg-header" id="sg-header">
		<div class="sg-container sg-header__inner">
			<a class="sg-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img
					class="sg-header__logo-img sg-header__logo-img--full"
					src="<?php echo esc_url( $logo ); ?>"
					alt="<?php esc_attr_e( 'Safeguard Security Services', 'site-blocks' ); ?>"
					width="766"
					height="210"
					decoding="async"
				/>
				<img
					class="sg-header__logo-img sg-header__logo-img--mark"
					src="<?php echo esc_url( $logo_mark ); ?>"
					alt="<?php esc_attr_e( 'Safeguard Security Services', 'site-blocks' ); ?>"
					width="450"
					height="512"
					decoding="async"
				/>
			</a>

			<nav class="sg-header__nav" aria-label="<?php esc_attr_e( 'Primary', 'site-blocks' ); ?>">
				<ul class="sg-header__nav-list" role="list">
					<?php foreach ( $nav_items as $item ) : ?>
						<?php if ( ( $item['type'] ?? '' ) === 'group' ) : ?>
							<?php
							$group_current = site_blocks_safeguard_nav_is_current( (string) $item['path'], true );
							$group_id      = 'sg-nav-' . sanitize_title( (string) $item['label'] );
							$mega_layout   = (string) ( $item['mega_layout'] ?? '' );
							$is_mega       = ! empty( $item['mega'] );
							$item_classes  = 'sg-header__nav-item sg-header__nav-item--dropdown';
							if ( $is_mega ) {
								$item_classes .= ' sg-header__nav-item--mega';
							}
							if ( 'split' === $mega_layout ) {
								$item_classes .= ' sg-header__nav-item--mega-split';
							}
							if ( 'feature' === $mega_layout || 'preview' === $mega_layout ) {
								$item_classes .= ' sg-header__nav-item--mega-feature';
							}
							if ( $group_current ) {
								$item_classes .= ' is-active';
							}
							?>
							<li class="<?php echo esc_attr( $item_classes ); ?>">
								<div class="sg-header__nav-dropdown-wrap">
									<a
										class="sg-header__nav-link sg-header__nav-link--parent"
										href="<?php echo esc_url( home_url( (string) $item['path'] ) ); ?>"
										title="<?php echo esc_attr( (string) $item['label'] ); ?>"
										aria-haspopup="true"
										aria-expanded="false"
										<?php echo $group_current ? 'aria-current="page"' : ''; ?>
									>
										<span class="sg-header__nav-label sg-header__nav-label--long"><?php echo esc_html( (string) $item['label'] ); ?></span>
										<span class="sg-header__nav-label sg-header__nav-label--short"><?php echo esc_html( (string) ( $item['short'] ?? $item['label'] ) ); ?></span>
										<span class="sg-header__nav-caret" aria-hidden="true"></span>
									</a>
									<?php if ( $is_mega && 'split' === $mega_layout && ! empty( $item['columns'] ) ) : ?>
										<?php site_blocks_render_safeguard_mega_menu( $item, $group_id ); ?>
									<?php elseif ( $is_mega && 'feature' === $mega_layout && ! empty( $item['children'] ) ) : ?>
										<?php site_blocks_render_safeguard_feature_mega( $item, $group_id ); ?>
									<?php elseif ( ! empty( $item['children'] ) && is_array( $item['children'] ) ) : ?>
										<?php site_blocks_render_safeguard_simple_menu( $item['children'], $group_id, false ); ?>
									<?php elseif ( ! empty( $item['columns'] ) && is_array( $item['columns'] ) ) : ?>
										<?php
										$menu_class = 'sg-header__nav-menu sg-header__nav-menu--mega';
										$flat_links = site_blocks_safeguard_nav_item_links( $item );
										?>
										<ul class="<?php echo esc_attr( $menu_class ); ?>" id="<?php echo esc_attr( $group_id ); ?>" role="list">
											<?php foreach ( $flat_links as $child ) : ?>
												<li>
													<?php site_blocks_render_safeguard_nav_link( $child, 'sg-header__nav-menu-link' ); ?>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</div>
							</li>
						<?php else : ?>
							<li class="sg-header__nav-item">
								<?php site_blocks_render_safeguard_nav_link( $item, 'sg-header__nav-link' ); ?>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</nav>

			<div class="sg-header__actions">
				<a class="sg-header__phone" href="<?php echo esc_attr( $phone_h ); ?>">
					<?php site_blocks_lucide_icon( 'phone', 18 ); ?>
					<?php echo esc_html( $phone ); ?>
				</a>
				<button type="button" class="sg-header__menu-btn" aria-expanded="false" aria-controls="sg-mobile-panel" aria-label="<?php esc_attr_e( 'Open menu', 'site-blocks' ); ?>">
					<span></span><span></span><span></span>
				</button>
				<a class="sg-btn sg-btn--orange sg-btn--sm sg-header__quote-mobile" href="<?php echo esc_url( $quote ); ?>"><?php esc_html_e( 'Quote', 'site-blocks' ); ?></a>
			</div>
		</div>

		<div class="sg-mobile-panel" id="sg-mobile-panel" hidden>
			<div class="sg-container">
				<nav class="sg-mobile-nav" aria-label="<?php esc_attr_e( 'Mobile', 'site-blocks' ); ?>">
					<?php foreach ( $nav_items as $item ) : ?>
						<?php if ( ( $item['type'] ?? '' ) === 'group' ) : ?>
							<div class="sg-mobile-nav__group">
								<?php
								$group_current = site_blocks_safeguard_nav_is_current( (string) $item['path'], true );
								$attrs         = $group_current ? ' aria-current="page"' : '';
								?>
								<a class="sg-mobile-nav__parent" href="<?php echo esc_url( home_url( (string) $item['path'] ) ); ?>"<?php echo $attrs; ?>>
									<?php echo esc_html( (string) $item['label'] ); ?>
								</a>
								<?php if ( 'split' === ( $item['mega_layout'] ?? '' ) && ! empty( $item['columns'] ) && is_array( $item['columns'] ) ) : ?>
									<?php foreach ( $item['columns'] as $column ) : ?>
										<?php
										$links = $column['links'] ?? array();
										if ( empty( $links ) || ! is_array( $links ) ) {
											continue;
										}
										?>
										<p class="sg-mobile-nav__section"><?php echo esc_html( (string) ( $column['heading'] ?? '' ) ); ?></p>
										<?php foreach ( $links as $child ) : ?>
											<?php
											if ( ! is_array( $child ) ) {
												continue;
											}
											$child_current = site_blocks_safeguard_nav_is_current( (string) $child['path'], false );
											$child_attrs   = $child_current ? ' aria-current="page"' : '';
											?>
											<a class="sg-mobile-nav__child" href="<?php echo esc_url( home_url( (string) $child['path'] ) ); ?>"<?php echo $child_attrs; ?>>
												<?php echo esc_html( (string) $child['label'] ); ?>
											</a>
										<?php endforeach; ?>
									<?php endforeach; ?>
								<?php elseif ( ! empty( $item['children'] ) && is_array( $item['children'] ) ) : ?>
									<?php site_blocks_render_safeguard_mobile_children( $item['children'], 0 ); ?>
								<?php elseif ( ! empty( $item['columns'] ) ) : ?>
									<?php foreach ( site_blocks_safeguard_nav_item_links( $item ) as $child ) : ?>
										<?php
										$child_current = site_blocks_safeguard_nav_is_current( (string) $child['path'], false );
										$child_attrs   = $child_current ? ' aria-current="page"' : '';
										?>
										<a class="sg-mobile-nav__child" href="<?php echo esc_url( home_url( (string) $child['path'] ) ); ?>"<?php echo $child_attrs; ?>>
											<?php echo esc_html( (string) $child['label'] ); ?>
										</a>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
						<?php else : ?>
							<?php
							$is_current = site_blocks_safeguard_nav_is_current( (string) $item['path'], false );
							$attrs      = $is_current ? ' aria-current="page"' : '';
							?>
							<a class="sg-mobile-nav__link" href="<?php echo esc_url( home_url( (string) $item['path'] ) ); ?>"<?php echo $attrs; ?>>
								<?php echo esc_html( (string) $item['label'] ); ?>
							</a>
						<?php endif; ?>
					<?php endforeach; ?>
				</nav>
			</div>
		</div>
	</header>
	<?php
}

/**
 * Whether the current page uses Safeguard custom chrome.
 */
function site_blocks_uses_safeguard_chrome(): bool {
	return is_front_page()
		|| is_page( 'alarm-systems' )
		|| is_page( 'ajax-alarm-systems' )
		|| is_page( 'paradox-m25' )
		|| is_page( 'akuvox' )
		|| is_page( 'cctv-security-cameras' )
		|| is_page( 'access-control' )
		|| is_page( 'intercom-systems' )
		|| is_page( 'ajax-calculator' )
		|| is_page( 'manpower-calculator' )
		|| is_page( 'physical-security' )
		|| is_page( 'static-guards' )
		|| is_page( 'mobile-patrols' )
		|| is_page( 'electronic-security' )
		|| is_page( 'manpower' )
		|| is_page( 'monitoring' )
		|| is_page( 'back-to-base' )
		|| is_page( 'virtual-patrol' )
		|| is_page( 'solar-cameras-monitoring' )
		|| is_page( 'enterprise-solutions' )
		|| is_page( 'safeguard-solutions' )
		|| is_page( 'about' )
		|| is_singular( array( 'post', 'case_study' ) )
		|| is_post_type_archive( 'case_study' )
		|| is_tax( 'case_study_hub' );
}

/**
 * Suppress Kadence footer markup at render time (after query is ready).
 */
function site_blocks_suppress_kadence_footer(): void {
	if ( ! site_blocks_uses_safeguard_chrome() ) {
		return;
	}

	remove_action( 'kadence_footer', 'Kadence\footer_markup', 10 );
}
add_action( 'kadence_footer', 'site_blocks_suppress_kadence_footer', 1 );

/**
 * Suppress Kadence header markup at render time.
 */
function site_blocks_suppress_kadence_header(): void {
	if ( ! site_blocks_uses_safeguard_chrome() ) {
		return;
	}

	remove_action( 'kadence_header', 'Kadence\header_markup', 10 );
}
add_action( 'kadence_header', 'site_blocks_suppress_kadence_header', 1 );

/**
 * Replace Kadence header/footer with Safeguard chrome on service pages.
 */
function site_blocks_remove_kadence_theme_chrome(): void {
	if ( ! site_blocks_uses_safeguard_chrome() ) {
		return;
	}

	remove_action( 'kadence_header', 'Kadence\header_markup' );
	remove_action( 'kadence_footer', 'Kadence\footer_markup' );
}
add_action( 'wp', 'site_blocks_remove_kadence_theme_chrome' );

/**
 * Kadence layout: fullwidth shell, theme header/footer off (Safeguard chrome in content).
 *
 * @param array<string, string> $layout Layout settings.
 * @return array<string, string>
 */
function site_blocks_safeguard_kadence_layout( array $layout ): array {
	if ( ! site_blocks_uses_safeguard_chrome() ) {
		return $layout;
	}

	$layout['layout']  = 'fullwidth';
	$layout['boxed']   = 'unboxed';
	$layout['title']   = 'hide';
	$layout['sidebar'] = 'disable';
	$layout['header']  = 'disable';
	$layout['footer']  = 'disable';

	return $layout;
}
add_filter( 'kadence_post_layout', 'site_blocks_safeguard_kadence_layout' );
