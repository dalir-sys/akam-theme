<?php
/**
 * WebMZ advanced WooCommerce account panel.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default account endpoint labels used for icon settings.
 *
 * @return array<string,string>
 */
function webmz_account_default_endpoint_labels() {
	$items = array(
		'dashboard'       => esc_html__( 'پیشخوان', 'tadris' ),
		'orders'          => esc_html__( 'سفارش‌ها', 'tadris' ),
		'downloads'       => esc_html__( 'دانلودها', 'tadris' ),
		'edit-address'    => esc_html__( 'نشانی', 'tadris' ),
		'edit-account'    => esc_html__( 'جزئیات حساب', 'tadris' ),
		'saved-videos'    => esc_html__( 'ذخیره شده‌ها', 'tadris' ),
		'support-tickets' => esc_html__( 'تیکت‌های پشتیبانی', 'tadris' ),
		'customer-logout' => esc_html__( 'بیرون رفتن', 'tadris' ),
	);

	if ( function_exists( 'wc_get_account_menu_items' ) ) {
		$woo_items = wc_get_account_menu_items();
		if ( is_array( $woo_items ) ) {
			foreach ( $woo_items as $endpoint => $label ) {
				$endpoint = sanitize_key( $endpoint );
				if ( $endpoint && ! isset( $items[ $endpoint ] ) ) {
					$items[ $endpoint ] = wp_strip_all_tags( (string) $label );
				}
			}
		}
	}

	return apply_filters( 'webmz_account_default_endpoint_labels', $items );
}

/**
 * Sanitize an endpoint slug.
 *
 * @param string $slug Raw slug.
 * @return string
 */
function webmz_account_sanitize_endpoint_slug( $slug ) {
	// Non-Latin letters would become percent-encoded junk in the URL; keep only the Latin part.
	$slug = preg_replace( '/[^\x20-\x7E]/u', '', (string) $slug );
	$slug = sanitize_title( (string) $slug );
	$slug = str_replace( '_', '-', $slug );
	$slug = preg_replace( '/[^a-z0-9\-]/', '', $slug );
	$slug = trim( (string) $slug, '-' );

	return $slug;
}

/**
 * Reserved WooCommerce/account endpoint slugs.
 *
 * @return array<int,string>
 */
function webmz_account_reserved_endpoint_slugs() {
	$reserved = array(
		'dashboard',
		'orders',
		'view-order',
		'downloads',
		'edit-address',
		'edit-account',
		'payment-methods',
		'add-payment-method',
		'delete-payment-method',
		'set-default-payment-method',
		'lost-password',
		'customer-logout',
		'saved-videos',
		'support-tickets',
	);

	return apply_filters( 'webmz_account_reserved_endpoint_slugs', $reserved );
}

/**
 * Sanitize stored custom account endpoints.
 *
 * @param mixed $raw Raw input.
 * @return array<int,array{title:string,slug:string,content:string,icon_id:int,enabled:string}>
 */
function webmz_account_sanitize_custom_endpoints( $raw ) {
	$raw      = is_array( $raw ) ? $raw : array();
	$clean    = array();
	$used     = array();
	$reserved = webmz_account_reserved_endpoint_slugs();

	foreach ( $raw as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$title   = isset( $row['title'] ) ? sanitize_text_field( wp_unslash( $row['title'] ) ) : '';
		$slug    = isset( $row['slug'] ) ? webmz_account_sanitize_endpoint_slug( wp_unslash( $row['slug'] ) ) : '';
		$content = isset( $row['content'] ) ? wp_kses_post( wp_unslash( $row['content'] ) ) : '';
		$icon_id = isset( $row['icon_id'] ) ? absint( $row['icon_id'] ) : 0;
		$enabled = isset( $row['enabled'] ) && 'yes' === $row['enabled'] ? 'yes' : 'no';

		if ( '' === $title ) {
			continue;
		}

		// A Persian-only slug sanitizes to nothing; give the page a working Latin slug instead of dropping it.
		if ( '' === $slug ) {
			$number = count( $clean ) + 1;
			do {
				$slug = 'account-page-' . $number++;
			} while ( in_array( $slug, $used, true ) );
		}

		if ( in_array( $slug, $reserved, true ) || in_array( $slug, $used, true ) ) {
			continue;
		}

		if ( $icon_id && 'attachment' !== get_post_type( $icon_id ) ) {
			$icon_id = 0;
		}

		$clean[] = array(
			'title'   => $title,
			'slug'    => $slug,
			'content' => $content,
			'icon_id' => $icon_id,
			'enabled' => $enabled,
		);

		$used[] = $slug;

		if ( count( $clean ) >= 20 ) {
			break;
		}
	}

	return $clean;
}

/**
 * Return custom endpoints stored in the theme options.
 *
 * @return array<int,array<string,mixed>>
 */
function webmz_account_get_custom_endpoints() {
	$options   = function_exists( 'webmz_get_options' ) ? webmz_get_options() : array();
	$endpoints = isset( $options['account_custom_endpoints'] ) && is_array( $options['account_custom_endpoints'] ) ? $options['account_custom_endpoints'] : array();

	return webmz_account_sanitize_custom_endpoints( $endpoints );
}

/**
 * Return custom endpoint by slug.
 *
 * @param string $slug Endpoint slug.
 * @return array<string,mixed>|null
 */
function webmz_account_get_custom_endpoint( $slug ) {
	$slug = webmz_account_sanitize_endpoint_slug( $slug );

	foreach ( webmz_account_get_custom_endpoints() as $endpoint ) {
		if ( $slug === $endpoint['slug'] && 'yes' === $endpoint['enabled'] ) {
			return $endpoint;
		}
	}

	return null;
}

/**
 * Register custom WooCommerce account endpoints.
 *
 * @return void
 */
function webmz_account_register_custom_endpoints() {
	foreach ( webmz_account_get_custom_endpoints() as $endpoint ) {
		if ( 'yes' !== $endpoint['enabled'] || empty( $endpoint['slug'] ) ) {
			continue;
		}

		add_rewrite_endpoint( $endpoint['slug'], EP_ROOT | EP_PAGES );
	}
}
add_action( 'init', 'webmz_account_register_custom_endpoints', 15 );

/**
 * Flush rewrite rules when custom endpoint list changes.
 *
 * @return void
 */
function webmz_account_maybe_flush_custom_endpoints() {
	$endpoints = webmz_account_get_custom_endpoints();
	$slugs     = array();

	foreach ( $endpoints as $endpoint ) {
		if ( 'yes' === $endpoint['enabled'] && ! empty( $endpoint['slug'] ) ) {
			$slugs[] = $endpoint['slug'];
		}
	}

	sort( $slugs );
	$hash = md5( wp_json_encode( $slugs ) );

	if ( get_option( 'webmz_account_endpoint_hash' ) !== $hash ) {
		flush_rewrite_rules( false );
		update_option( 'webmz_account_endpoint_hash', $hash );
	}
}
add_action( 'init', 'webmz_account_maybe_flush_custom_endpoints', 120 );

/**
 * Add custom endpoint menu items before logout.
 *
 * @param array<string,string> $items WooCommerce account menu items.
 * @return array<string,string>
 */
function webmz_account_add_custom_menu_items( $items ) {
	if ( ! is_array( $items ) ) {
		return $items;
	}

	$logout = isset( $items['customer-logout'] ) ? $items['customer-logout'] : null;
	if ( null !== $logout ) {
		unset( $items['customer-logout'] );
	}

	foreach ( webmz_account_get_custom_endpoints() as $endpoint ) {
		if ( 'yes' !== $endpoint['enabled'] || empty( $endpoint['slug'] ) || empty( $endpoint['title'] ) ) {
			continue;
		}

		$items[ $endpoint['slug'] ] = $endpoint['title'];
	}

	if ( null !== $logout ) {
		$items['customer-logout'] = $logout;
	}

	return $items;
}
add_filter( 'woocommerce_account_menu_items', 'webmz_account_add_custom_menu_items', 80 );
/**
 * Get the visible WooCommerce account endpoints configured in theme options.
 * Empty value means every endpoint is visible for backward compatibility.
 *
 * @return array<int,string>
 */
function webmz_account_get_visible_endpoint_keys() {
	$options = function_exists( 'webmz_get_options' ) ? webmz_get_options() : array();
	$stored  = isset( $options['account_visible_endpoints'] ) && is_array( $options['account_visible_endpoints'] ) ? $options['account_visible_endpoints'] : array();
	$stored  = array_values( array_unique( array_filter( array_map( 'sanitize_key', $stored ) ) ) );

	return apply_filters( 'webmz_account_visible_endpoint_keys', $stored );
}

/**
 * Hide unchecked WooCommerce account endpoints from the custom account menu.
 *
 * @param array<string,string> $items WooCommerce account menu items.
 * @return array<string,string>
 */
function webmz_account_filter_visible_menu_items( $items ) {
	if ( ! is_array( $items ) ) {
		return $items;
	}

	if ( is_admin() && ! wp_doing_ajax() ) {
		return $items;
	}

	$visible = webmz_account_get_visible_endpoint_keys();

	if ( empty( $visible ) ) {
		return $items;
	}

	// Custom endpoints have their own on/off switch and are not in the visibility checklist.
	$custom = wp_list_pluck( webmz_account_get_custom_endpoints(), 'slug' );

	foreach ( array_keys( $items ) as $endpoint ) {
		$endpoint_key = sanitize_key( $endpoint );
		if ( ! in_array( $endpoint_key, $visible, true ) && ! in_array( $endpoint_key, $custom, true ) ) {
			unset( $items[ $endpoint ] );
		}
	}

	return $items;
}
add_filter( 'woocommerce_account_menu_items', 'webmz_account_filter_visible_menu_items', 120 );


/**
 * Register content callbacks for custom endpoints.
 *
 * @return void
 */
function webmz_account_register_custom_endpoint_content() {
	foreach ( webmz_account_get_custom_endpoints() as $endpoint ) {
		if ( 'yes' !== $endpoint['enabled'] || empty( $endpoint['slug'] ) ) {
			continue;
		}

		$slug = $endpoint['slug'];
		add_action(
			'woocommerce_account_' . $slug . '_endpoint',
			static function () use ( $slug ) {
				$endpoint = webmz_account_get_custom_endpoint( $slug );

				if ( ! $endpoint ) {
					echo '<div class="webmz-account-empty">' . esc_html__( 'این بخش در حال حاضر در دسترس نیست.', 'tadris' ) . '</div>';
					return;
				}

				echo '<div class="webmz-account-custom-endpoint">';
				echo '<h2>' . esc_html( $endpoint['title'] ) . '</h2>';
				// Shortcodes run after kses/autop so their own markup is not filtered or wrapped in <p>.
				echo '<div class="webmz-account-custom-endpoint__content">' . do_shortcode( shortcode_unautop( wpautop( wp_kses_post( $endpoint['content'] ) ) ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '</div>';
			}
		);
	}
}
add_action( 'wp_loaded', 'webmz_account_register_custom_endpoint_content' );

/**
 * Get current account endpoint key.
 *
 * @return string
 */
function webmz_account_get_current_endpoint() {
	global $wp;

	if ( ! empty( $wp->query_vars ) && is_array( $wp->query_vars ) ) {
		$menu_items = function_exists( 'wc_get_account_menu_items' ) ? wc_get_account_menu_items() : array();

		foreach ( array_keys( $menu_items ) as $endpoint ) {
			if ( 'dashboard' === $endpoint ) {
				continue;
			}

			if ( isset( $wp->query_vars[ $endpoint ] ) ) {
				return sanitize_key( $endpoint );
			}
		}
	}

	return 'dashboard';
}

/**
 * Return configured icon attachment ID for endpoint.
 *
 * @param string $endpoint Endpoint slug.
 * @return int
 */
function webmz_account_get_endpoint_icon_id( $endpoint ) {
	$options = function_exists( 'webmz_get_options' ) ? webmz_get_options() : array();
	$icons   = isset( $options['account_endpoint_icons'] ) && is_array( $options['account_endpoint_icons'] ) ? $options['account_endpoint_icons'] : array();
	$key     = sanitize_key( $endpoint );

	if ( isset( $icons[ $key ] ) ) {
		return absint( $icons[ $key ] );
	}

	$custom = webmz_account_get_custom_endpoint( $key );
	if ( $custom && ! empty( $custom['icon_id'] ) ) {
		return absint( $custom['icon_id'] );
	}

	return 0;
}

/**
 * Return default icon SVG for account endpoint.
 *
 * @param string $endpoint Endpoint slug.
 * @return string
 */
function webmz_account_default_icon_svg( $endpoint ) {
	$endpoint = sanitize_key( $endpoint );
	$paths    = array(
		'dashboard'       => '<path d="M4 13h6v7h-6z"/><path d="M14 4h6v16h-6z"/><path d="M4 4h6v5h-6z"/>',
		'orders'          => '<path d="M6 3h12l2 5v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"/><path d="M6 8h12"/><path d="M9 12h6"/>',
		'downloads'       => '<path d="M12 3v12"/><path d="M7 10l5 5l5 -5"/><path d="M5 21h14"/>',
		'edit-address'    => '<path d="M12 21s7 -5.2 7 -11a7 7 0 1 0 -14 0c0 5.8 7 11 7 11z"/><path d="M12 10m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>',
		'edit-account'    => '<path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h3"/><path d="M17.5 17.5l2 2"/><path d="M20 16l-4 4"/>',
		'saved-videos'    => '<path d="M18 7v14l-6 -4l-6 4v-14a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4"/>',
		'support-tickets' => '<path d="M4 5h16v11a2 2 0 0 1 -2 2h-6l-4 3v-3h-2a2 2 0 0 1 -2 -2z"/><path d="M8 9h8"/><path d="M8 13h5"/>',
		'view-history'    => '<path d="M12 8v4l3 2"/><path d="M3.05 11a9 9 0 1 1 2.64 6.36"/><path d="M3 16v-5h5"/>',
		'payment-methods' => '<path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"/><path d="M3 10h18"/><path d="M7 15h.01"/><path d="M11 15h2"/>',
		'customer-logout' => '<path d="M14 8v-2a2 2 0 0 0 -2 -2h-5a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5a2 2 0 0 0 2 -2v-2"/><path d="M9 12h12"/><path d="M18 9l3 3l-3 3"/>',
	);

	$path = isset( $paths[ $endpoint ] ) ? $paths[ $endpoint ] : '<path d="M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2z"/><path d="M8 9h8"/><path d="M8 13h5"/>';

	return '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>' . $path . '</svg>';
}

/**
 * Render endpoint icon.
 *
 * @param string $endpoint Endpoint slug.
 * @return void
 */
function webmz_account_render_endpoint_icon( $endpoint ) {
	$icon_id = webmz_account_get_endpoint_icon_id( $endpoint );

	if ( $icon_id ) {
		$image = wp_get_attachment_image(
			$icon_id,
			'thumbnail',
			false,
			array(
				'class'    => 'webmz-account-nav__icon-image',
				'alt'      => '',
				'loading'  => 'lazy',
				'decoding' => 'async',
			)
		);

		if ( $image ) {
			echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}
	}

	echo webmz_account_default_icon_svg( $endpoint ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Get endpoint title overrides configured in theme options.
 *
 * @return array<string,string>
 */
function webmz_account_get_endpoint_title_overrides() {
	$options = function_exists( 'webmz_get_options' ) ? webmz_get_options() : array();
	$stored  = isset( $options['account_endpoint_titles'] ) && is_array( $options['account_endpoint_titles'] ) ? $options['account_endpoint_titles'] : array();
	$titles  = array();

	foreach ( $stored as $endpoint => $title ) {
		$key   = sanitize_key( $endpoint );
		$title = sanitize_text_field( (string) $title );

		if ( '' === $key || '' === $title ) {
			continue;
		}

		$titles[ $key ] = $title;
	}

	return $titles;
}

/**
 * Get endpoint order values configured in theme options.
 *
 * @return array<string,int>
 */
function webmz_account_get_endpoint_order_values() {
	$options = function_exists( 'webmz_get_options' ) ? webmz_get_options() : array();
	$stored  = isset( $options['account_endpoint_order'] ) && is_array( $options['account_endpoint_order'] ) ? $options['account_endpoint_order'] : array();
	$order   = array();

	foreach ( $stored as $endpoint => $position ) {
		$key = sanitize_key( $endpoint );

		if ( '' === $key ) {
			continue;
		}

		$order[ $key ] = max( 0, min( 9999, absint( $position ) ) );
	}

	return $order;
}

/**
 * Sort endpoint array by configured order while preserving unknown endpoints.
 *
 * @param array<string,string> $items Endpoint items.
 * @param array<string,int>    $order Configured order values.
 * @return array<string,string>
 */
function webmz_account_sort_endpoint_items_by_order( $items, $order ) {
	if ( empty( $order ) || ! is_array( $items ) ) {
		return $items;
	}

	$indexed = array();
	$index   = 0;

	foreach ( $items as $endpoint => $label ) {
		$key       = sanitize_key( $endpoint );
		$position  = isset( $order[ $key ] ) ? absint( $order[ $key ] ) : 10000 + $index;
		$indexed[] = array(
			'endpoint' => (string) $endpoint,
			'label'    => $label,
			'position' => $position,
			'index'    => $index,
		);
		$index++;
	}

	usort(
		$indexed,
		static function ( $a, $b ) {
			if ( $a['position'] === $b['position'] ) {
				return $a['index'] <=> $b['index'];
			}

			return $a['position'] <=> $b['position'];
		}
	);

	$sorted = array();
	foreach ( $indexed as $row ) {
		$sorted[ $row['endpoint'] ] = $row['label'];
	}

	return $sorted;
}

/**
 * Apply configured account endpoint titles and order.
 *
 * @param array<string,string> $items WooCommerce account menu items.
 * @return array<string,string>
 */
function webmz_account_apply_endpoint_title_and_order( $items ) {
	if ( ! is_array( $items ) ) {
		return $items;
	}

	if ( is_admin() && ! wp_doing_ajax() ) {
		return $items;
	}

	$titles = webmz_account_get_endpoint_title_overrides();
	foreach ( $items as $endpoint => $label ) {
		$key = sanitize_key( $endpoint );
		if ( isset( $titles[ $key ] ) && '' !== $titles[ $key ] ) {
			$items[ $endpoint ] = $titles[ $key ];
		}
	}

	return webmz_account_sort_endpoint_items_by_order( $items, webmz_account_get_endpoint_order_values() );
}
add_filter( 'woocommerce_account_menu_items', 'webmz_account_apply_endpoint_title_and_order', 160 );

/**
 * Get dashboard stats.
 *
 * @return array<int,array<string,string|int>>
 */
function webmz_account_get_stats() {
	$user_id = get_current_user_id();
	$orders  = 0;
	$tickets = 0;
	$saved   = 0;

	if ( function_exists( 'wc_get_orders' ) && $user_id ) {
		$orders = count(
			wc_get_orders(
				array(
					'customer_id' => $user_id,
					'limit'       => -1,
					'return'      => 'ids',
				)
			)
		);
	}

	if ( function_exists( 'webmz_ticket_get_user_unread_count' ) ) {
		$tickets = webmz_ticket_get_user_unread_count( $user_id );
	}

	if ( function_exists( 'webmz_tadris_get_saved_posts' ) ) {
		$saved = count( webmz_tadris_get_saved_posts( $user_id ) );
	}

	$stats = array(
		array(
			'label' => esc_html__( 'سفارش‌ها', 'tadris' ),
			'value' => $orders,
			'icon'  => 'orders',
		),
		array(
			'label' => esc_html__( 'اعلان‌های تیکت', 'tadris' ),
			'value' => $tickets,
			'icon'  => 'support-tickets',
		),
		array(
			'label' => esc_html__( 'ذخیره‌شده‌ها', 'tadris' ),
			'value' => $saved,
			'icon'  => 'saved-videos',
		),
	);

	return $stats;
}

/**
 * Render the account panel navigation.
 *
 * @return void
 */
function webmz_account_render_navigation() {
	if ( ! function_exists( 'wc_get_account_menu_items' ) ) {
		return;
	}

	$current = webmz_account_get_current_endpoint();
	$items   = wc_get_account_menu_items();
	?>
	<nav class="webmz-account-nav" aria-label="<?php esc_attr_e( 'منوی حساب کاربری', 'tadris' ); ?>">
		<ul class="webmz-account-nav__list">
			<?php foreach ( $items as $endpoint => $label ) : ?>
				<?php
				$endpoint = sanitize_key( $endpoint );
				$url      = wc_get_account_endpoint_url( $endpoint );
				$active   = $endpoint === $current || ( 'dashboard' === $endpoint && 'dashboard' === $current );
				$is_logout = 'customer-logout' === $endpoint;
				?>
				<li class="webmz-account-nav__item webmz-account-nav__item--<?php echo esc_attr( $endpoint ); ?> <?php echo $active ? 'is-active' : ''; ?> <?php echo $is_logout ? 'is-logout' : ''; ?>">
					<a class="webmz-account-nav__link" href="<?php echo esc_url( $url ); ?>" data-webmz-account-endpoint="<?php echo esc_attr( $endpoint ); ?>" <?php echo $is_logout ? 'data-webmz-account-no-ajax="1"' : ''; ?>>
						<span class="webmz-account-nav__icon"><?php webmz_account_render_endpoint_icon( $endpoint ); ?></span>
						<span class="webmz-account-nav__text"><?php echo esc_html( wp_strip_all_tags( (string) $label ) ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
}

/**
 * Render profile block in the account sidebar.
 *
 * @return void
 */
function webmz_account_render_profile_card() {
	$user = wp_get_current_user();
	if ( ! $user || ! $user->exists() ) {
		return;
	}

	$display_name = $user->display_name ? $user->display_name : $user->user_login;
	?>
	<div class="webmz-account-profile">
		<div class="webmz-account-profile__avatar">
			<?php echo get_avatar( $user->ID, 96, '', esc_attr( $display_name ), array( 'class' => 'webmz-account-profile__image' ) ); ?>
		</div>
		<div class="webmz-account-profile__body">
			<strong><?php echo esc_html( $display_name ); ?></strong>
			<span><?php echo esc_html( $user->user_email ); ?></span>
		</div>
	</div>
	<?php
}

/**
 * Render account stats cards.
 *
 * @return void
 */
function webmz_account_render_stats() {
	$stats = webmz_account_get_stats();
	?>
	<div class="webmz-account-stats">
		<?php foreach ( $stats as $stat ) : ?>
			<div class="webmz-account-stat">
				<span class="webmz-account-stat__icon"><?php webmz_account_render_endpoint_icon( (string) $stat['icon'] ); ?></span>
				<span class="webmz-account-stat__value"><?php echo esc_html( number_format_i18n( absint( $stat['value'] ) ) ); ?></span>
				<span class="webmz-account-stat__label"><?php echo esc_html( $stat['label'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Enqueue account panel assets only on WooCommerce account pages.
 *
 * @return void
 */
function webmz_account_enqueue_assets() {
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() || ! is_user_logged_in() ) {
		return;
	}

	$options = function_exists( 'webmz_get_options' ) ? webmz_get_options() : array();
	$ajax_enabled = ! isset( $options['account_ajax_enabled'] ) || 'yes' === $options['account_ajax_enabled'];

	wp_enqueue_style( 'webmz-account-panel', WEBMZ_URI . 'assets/css/account-panel.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_enqueue_script( 'webmz-account-panel', WEBMZ_URI . 'assets/js/account-panel.js', array( 'webmz-otp-auth' ), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-account-panel',
		'webmzAccountPanel',
		array(
			'ajaxEnabled'  => $ajax_enabled,
			'loadingText'  => esc_html__( 'در حال بارگذاری...', 'tadris' ),
			'errorMessage' => esc_html__( 'بارگذاری این بخش با خطا روبه‌رو شد.', 'tadris' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'webmz_account_enqueue_assets' );
