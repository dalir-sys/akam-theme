<?php
/**
 * Theme helper functions.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default floating contact items used by the theme options panel.
 *
 * @return array<int,array<string,mixed>>
 */
function webmz_get_default_floating_contact_items() {
	return array(
		array(
			'enabled' => 'yes',
			'title'   => __( 'تماس با فروشگاه', 'tadris' ),
			'url'     => 'tel:',
			'color'   => '#189735',
			'icon'    => 'phone',
			'icon_id' => 0,
			'order'   => 1,
		),
		array(
			'enabled' => 'yes',
			'title'   => __( 'ارتباط از طریق واتس‌اپ', 'tadris' ),
			'url'     => 'https://wa.me/',
			'color'   => '#25d366',
			'icon'    => 'whatsapp',
			'icon_id' => 0,
			'order'   => 2,
		),
		array(
			'enabled' => 'yes',
			'title'   => __( 'ارتباط از طریق تلگرام', 'tadris' ),
			'url'     => 'https://t.me/',
			'color'   => '#2aabee',
			'icon'    => 'telegram',
			'icon_id' => 0,
			'order'   => 3,
		),
		array(
			'enabled' => 'yes',
			'title'   => __( 'پیج اینستاگرام', 'tadris' ),
			'url'     => 'https://instagram.com/',
			'color'   => '#d62976',
			'icon'    => 'instagram',
			'icon_id' => 0,
			'order'   => 4,
		),
		array(
			'enabled' => 'yes',
			'title'   => __( 'ارتباط در ایتا', 'tadris' ),
			'url'     => 'https://eitaa.com/',
			'color'   => '#f28c28',
			'icon'    => 'eitaa',
			'icon_id' => 0,
			'order'   => 5,
		),
		array(
			'enabled' => 'yes',
			'title'   => __( 'ارتباط در بله', 'tadris' ),
			'url'     => 'https://ble.ir/',
			'color'   => '#00a693',
			'icon'    => 'bale',
			'icon_id' => 0,
			'order'   => 6,
		),
		array(
			'enabled' => 'yes',
			'title'   => __( 'ارتباط در روبیکا', 'tadris' ),
			'url'     => 'https://rubika.ir/',
			'color'   => '#f6b000',
			'icon'    => 'rubika',
			'icon_id' => 0,
			'order'   => 7,
		),
		array(
			'enabled' => 'yes',
			'title'   => __( 'ارتباط در سروش', 'tadris' ),
			'url'     => 'https://splus.ir/',
			'color'   => '#1f8cff',
			'icon'    => 'soroush',
			'icon_id' => 0,
			'order'   => 8,
		),
		array(
			'enabled' => 'yes',
			'title'   => __( 'تماس با پشتیبان فروشگاه', 'tadris' ),
			'url'     => '#',
			'color'   => '#ff9f0a',
			'icon'    => 'support',
			'icon_id' => 0,
			'order'   => 9,
		),
	);
}

/**
 * Default theme option values.
 *
 * @return array<string,mixed>
 */
function webmz_get_default_options() {
	return array(
		'color_primary'       => '#0878f9',
		'color_secondary'     => '#092c4c',
		'color_primary_hover' => '#0662cc',
		'color_primary_light' => '#ffffff',
		'color_text_dark'     => '#111827',
		'color_text_gray'     => '#64748b',
		'color_text_navy'     => '#102d50',
		'color_background'    => '#f3f8fc',
		'container_width'     => 1240,
		'popular_searches'    => '',
		'font_family'         => 'system',
		'admin_font_family'   => 'system',
		'local_gravatar_enable' => 'no',
		'local_gravatar_url'    => '',
		'admin_logo_id'       => 0,
		'favicon_id'          => 0,
		'account_ajax_enabled' => 'yes',
		'account_accent_light' => '#fff4e7',
		'account_endpoint_icons' => array(),
		'account_endpoint_titles' => array(),
		'account_endpoint_order' => array(),
		'account_visible_endpoints' => array(),
		'account_custom_endpoints' => array(),
		'checkout_visible_fields' => array(),
		'layout_header'       => 0,
		'layout_footer'       => 0,
		'layout_single_post'  => 0,
		'layout_single_product' => 0,
		'layout_archive_post' => 0,
		'layout_archive_teacher' => 0,
		'layout_single_teacher' => 0,
		'layout_archive_product' => 0,
		'layout_404'          => 0,
		'layout_search'       => 0,
		'layout_page'         => 0,
		'related_use_theme_colors' => 'yes',
		'related_box_bg'           => '#eef3f8',
		'related_box_accent'       => '#f97316',
		'related_box_btn_bg'       => '',
		'related_box_btn_hover'    => '',
		'related_box_title_color'  => '',
		'related_box_desc_color'   => '',
		'floating_contact_enabled' => 'yes',
		'floating_contact_position' => 'left',
		'floating_contact_button_color' => '#b65a78',
		'floating_contact_button_hover_color' => '#a54c6b',
		'floating_contact_offset_x' => 24,
		'floating_contact_offset_bottom' => 24,
		'floating_contact_button_icon_id' => 0,
		'floating_contact_panel_title' => __( 'پاسخگوی شما هستیم', 'tadris' ),
		'floating_contact_panel_desc' => __( 'یکی از راه‌های زیر را برای ارتباط انتخاب کنید', 'tadris' ),
		'floating_contact_items'   => webmz_get_default_floating_contact_items(),
		'sticky_add_to_cart_enabled' => 'yes',
	);
}

/**
 * Get all stored settings merged with safe defaults.
 *
 * @return array<string,mixed>
 */
function webmz_get_options() {
	$options = get_option( 'webmz_options', array() );
	$options = is_array( $options ) ? $options : array();

	return wp_parse_args( $options, webmz_get_default_options() );
}

/**
 * Get one stored option.
 *
 * @param string $key Option key.
 * @return mixed
 */
function webmz_get_option( $key ) {
	$options = webmz_get_options();
	return array_key_exists( $key, $options ) ? $options[ $key ] : null;
}

/**
 * Get sanitized popular search phrases configured in the theme panel.
 *
 * Each term may be entered on a separate line or separated by Persian/English
 * comma. A maximum of twenty unique phrases is returned for widget output.
 *
 * @return array<int,string>
 */
function webmz_get_popular_search_terms() {
	$raw   = (string) webmz_get_option( 'popular_searches' );
	$terms = preg_split( '/[\r\n,،]+/u', $raw );
	$clean = array();

	foreach ( (array) $terms as $term ) {
		$term = sanitize_text_field( trim( $term ) );

		if ( '' !== $term ) {
			$clean[] = $term;
		}
	}

	return array_slice( array_values( array_unique( $clean ) ), 0, 20 );
}

/**
 * Available local fonts. To add a font, place a font.css file in
 * assets/fonts/{font-slug}/font.css. Folder names become selectable labels.
 *
 * @return array<string,array<string,string>>
 */
function webmz_get_local_fonts() {
	$fonts = array(
		'system' => array(
		'label' => esc_html__( 'فونت سیستم', 'tadris' ),
		'css'   => '',
		'stack' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Tahoma, Arial, sans-serif',
		),
	);
	$base = WEBMZ_DIR . 'assets/fonts/';
	if ( is_dir( $base ) ) {
		$font_files = glob( $base . '*/font.css' );
		if ( is_array( $font_files ) ) {
			foreach ( $font_files as $file ) {
				$slug = sanitize_key( basename( dirname( $file ) ) );
				if ( '' === $slug ) {
					continue;
				}
				$fonts[ $slug ] = array(
					'label' => ucfirst( str_replace( array( '-', '_' ), ' ', $slug ) ),
					'css'   => 'assets/fonts/' . $slug . '/font.css',
					'stack' => '"' . $slug . '", Tahoma, Arial, sans-serif',
				);
			}
		}
	}

	return apply_filters( 'webmz_local_fonts', $fonts );
}

/**
 * Post types that represent templates/layouts, not front-end content.
 *
 * @return string[]
 */
function webmz_get_non_content_post_types() {
	return array(
		'webmz_layout',
		'elementor_library',
		'e-landing-page',
		'revision',
		'nav_menu_item',
		'custom_css',
		'customize_changeset',
		'oembed_cache',
		'user_request',
		'wp_block',
		'wp_template',
		'wp_template_part',
		'wp_global_styles',
		'wp_navigation',
	);
}

/**
 * Whether a post is real front-end content (not a layout/template).
 *
 * @param mixed $post Post object or ID.
 * @return bool
 */
function webmz_is_content_context_post( $post ) {
	$post = get_post( $post );

	if ( ! $post instanceof WP_Post ) {
		return false;
	}

	return ! in_array( $post->post_type, webmz_get_non_content_post_types(), true );
}

/**
 * Get the current post ID being represented by an Elementor layout.
 *
 * @return int
 */
function webmz_get_context_post_id() {
	if ( ! empty( $GLOBALS['webmz_render_context_post_id'] ) ) {
		$context_id = absint( $GLOBALS['webmz_render_context_post_id'] );

		if ( $context_id && webmz_is_content_context_post( $context_id ) ) {
			return $context_id;
		}
	}

	$queried = get_queried_object();

	if ( $queried instanceof WP_Post && webmz_is_content_context_post( $queried ) ) {
		return (int) $queried->ID;
	}

	$post_id = absint( get_the_ID() );

	if ( $post_id && webmz_is_content_context_post( $post_id ) ) {
		return $post_id;
	}

	// Elementor editor / Theme Builder preview of a specific singular post.
	if ( ! empty( $_GET['preview_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$preview_id = absint( wp_unslash( $_GET['preview_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $preview_id && webmz_is_content_context_post( $preview_id ) ) {
			return $preview_id;
		}
	}

	return 0;
}

/**
 * Check if editor or preview is showing a layout itself.
 *
 * @return bool
 */
function webmz_is_layout_editing_context() {
	return is_singular( 'webmz_layout' ) || ( isset( $_GET['elementor-preview'] ) && 'webmz_layout' === get_post_type( absint( $_GET['elementor-preview'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

/**
 * Sanitize a user selected HTML heading tag.
 *
 * @param string $tag Heading tag.
 * @return string
 */
function webmz_sanitize_heading_tag( $tag ) {
	$tag = sanitize_key( (string) $tag );

	if ( ! in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ) {
		$tag = 'h3';
	}

	return $tag;
}

/**
 * Get the chronologically adjacent post for a given post ID.
 *
 * Uses a direct query so it works reliably inside Elementor layouts
 * where the main loop/global post may not be set. Matches WordPress 6.9+
 * adjacent logic with an ID tie-breaker for identical post_date values.
 *
 * @param int    $post_id   Current post ID.
 * @param string $direction Direction: prev|next.
 * @return \WP_Post|null
 */
function webmz_get_adjacent_post( $post_id, $direction = 'prev' ) {
	global $wpdb;

	$post_id = absint( $post_id );
	$post    = get_post( $post_id );

	if ( ! $post || 'publish' !== $post->post_status || ! webmz_is_content_context_post( $post ) ) {
		return null;
	}

	$is_prev = 'prev' === $direction;
	$op      = $is_prev ? '<' : '>';
	$order   = $is_prev ? 'DESC' : 'ASC';

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$adjacent_id = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			WHERE post_type = %s
			AND post_status = 'publish'
			AND (
				post_date {$op} %s
				OR ( post_date = %s AND ID {$op} %d )
			)
			ORDER BY post_date {$order}, ID {$order}
			LIMIT 1",
			$post->post_type,
			$post->post_date,
			$post->post_date,
			$post_id
		)
	);

	return $adjacent_id ? get_post( $adjacent_id ) : null;
}

/**
 * Checkout fields that can be controlled from the theme panel.
 *
 * @return array<string,array<string,string>>
 */
function webmz_checkout_get_field_choices() {
	$choices = array(
		'billing' => array(
			'billing_first_name' => esc_html__( 'نام', 'tadris' ),
			'billing_last_name'  => esc_html__( 'نام خانوادگی', 'tadris' ),
			'billing_company'    => esc_html__( 'نام شرکت', 'tadris' ),
			'billing_country'    => esc_html__( 'کشور', 'tadris' ),
			'billing_state'      => esc_html__( 'استان', 'tadris' ),
			'billing_city'       => esc_html__( 'شهر', 'tadris' ),
			'billing_address_1'  => esc_html__( 'آدرس اصلی', 'tadris' ),
			'billing_address_2'  => esc_html__( 'آدرس دوم', 'tadris' ),
			'billing_postcode'   => esc_html__( 'کد پستی', 'tadris' ),
			'billing_phone'      => esc_html__( 'تلفن', 'tadris' ),
			'billing_email'      => esc_html__( 'ایمیل', 'tadris' ),
		),
		'shipping' => array(
			'shipping_first_name' => esc_html__( 'نام گیرنده', 'tadris' ),
			'shipping_last_name'  => esc_html__( 'نام خانوادگی گیرنده', 'tadris' ),
			'shipping_company'    => esc_html__( 'شرکت گیرنده', 'tadris' ),
			'shipping_country'    => esc_html__( 'کشور ارسال', 'tadris' ),
			'shipping_state'      => esc_html__( 'استان ارسال', 'tadris' ),
			'shipping_city'       => esc_html__( 'شهر ارسال', 'tadris' ),
			'shipping_address_1'  => esc_html__( 'آدرس ارسال', 'tadris' ),
			'shipping_address_2'  => esc_html__( 'آدرس دوم ارسال', 'tadris' ),
			'shipping_postcode'   => esc_html__( 'کد پستی ارسال', 'tadris' ),
		),
		'order' => array(
			'order_comments' => esc_html__( 'یادداشت سفارش', 'tadris' ),
		),
	);

	return apply_filters( 'webmz_checkout_field_choices', $choices );
}

/**
 * Get default visible checkout field keys.
 *
 * @return array<int,string>
 */
function webmz_checkout_get_default_visible_fields() {
	$visible = array();

	foreach ( webmz_checkout_get_field_choices() as $section ) {
		foreach ( array_keys( (array) $section ) as $field_key ) {
			$visible[] = sanitize_key( $field_key );
		}
	}

	return array_values( array_unique( $visible ) );
}

/**
 * Get checkout fields enabled in the theme panel.
 *
 * @return array<int,string>
 */
function webmz_checkout_get_visible_fields() {
	$stored = webmz_get_option( 'checkout_visible_fields' );

	if ( ! is_array( $stored ) || empty( $stored ) ) {
		return webmz_checkout_get_default_visible_fields();
	}

	return array_values( array_unique( array_map( 'sanitize_key', $stored ) ) );
}

/**
 * Hide disabled checkout fields from WooCommerce checkout.
 *
 * @param array<string,array<string,array<string,mixed>>> $fields Checkout fields.
 * @return array<string,array<string,array<string,mixed>>>
 */
function webmz_filter_checkout_fields_by_theme_options( $fields ) {
	$visible = webmz_checkout_get_visible_fields();

	foreach ( array( 'billing', 'shipping', 'order' ) as $section ) {
		if ( empty( $fields[ $section ] ) || ! is_array( $fields[ $section ] ) ) {
			continue;
		}

		foreach ( array_keys( $fields[ $section ] ) as $field_key ) {
			if ( ! in_array( sanitize_key( $field_key ), $visible, true ) ) {
				unset( $fields[ $section ][ $field_key ] );
			}
		}
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'webmz_filter_checkout_fields_by_theme_options', 30 );

/**
 * Check if a checkout field is enabled in theme panel.
 *
 * @param string $field_key Checkout field key.
 * @return bool
 */
function webmz_checkout_is_field_visible( $field_key ) {
	$field_key = sanitize_key( $field_key );
	$visible   = webmz_checkout_get_visible_fields();

	return in_array( $field_key, $visible, true );
}

/**
 * Disable WooCommerce order notes block when order_comments is disabled.
 *
 * @param bool $enabled Whether order notes are enabled.
 * @return bool
 */
function webmz_checkout_enable_order_notes_field( $enabled ) {
	if ( ! webmz_checkout_is_field_visible( 'order_comments' ) ) {
		return false;
	}

	return $enabled;
}
add_filter( 'woocommerce_enable_order_notes_field', 'webmz_checkout_enable_order_notes_field', 30 );

/**
 * Add a body class so the empty additional fields wrapper can be hidden safely.
 *
 * @param array<int,string> $classes Body classes.
 * @return array<int,string>
 */
function webmz_checkout_order_notes_body_class( $classes ) {
	if ( function_exists( 'is_checkout' ) && is_checkout() && ! webmz_checkout_is_field_visible( 'order_comments' ) ) {
		$classes[] = 'webmz-checkout-order-notes-disabled';
	}

	return $classes;
}
add_filter( 'body_class', 'webmz_checkout_order_notes_body_class' );

/**
 * Convert English digits to Persian numerals.
 *
 * @param string|int|float $value Input value.
 * @return string
 */
function webmz_to_persian_digits( $value ) {
	$english = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
	$persian = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );

	return str_replace( $english, $persian, (string) $value );
}

/**
 * Convert a Gregorian date to Jalali components.
 *
 * @param int $gy Gregorian year.
 * @param int $gm Gregorian month.
 * @param int $gd Gregorian day.
 * @return array{0:int,1:int,2:int} Jalali year, month, day.
 */
function webmz_gregorian_to_jalali( $gy, $gm, $gd ) {
	$gy = (int) $gy;
	$gm = (int) $gm;
	$gd = (int) $gd;

	$g_d_m = array( 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 );
	$gy2   = ( $gm > 2 ) ? ( $gy + 1 ) : $gy;
	$days  = 355666 + ( 365 * $gy ) + (int) floor( ( $gy2 + 3 ) / 4 ) - (int) floor( ( $gy2 + 99 ) / 100 ) + (int) floor( ( $gy2 + 399 ) / 400 ) + $gd + $g_d_m[ $gm - 1 ];
	$jy    = -1595 + ( 33 * (int) floor( $days / 12053 ) );
	$days %= 12053;
	$jy   += 4 * (int) floor( $days / 1461 );
	$days %= 1461;

	if ( $days > 365 ) {
		$jy   += (int) floor( ( $days - 1 ) / 365 );
		$days  = ( $days - 1 ) % 365;
	}

	if ( $days < 186 ) {
		$jm = 1 + (int) floor( $days / 31 );
		$jd = 1 + ( $days % 31 );
	} else {
		$jm = 7 + (int) floor( ( $days - 186 ) / 30 );
		$jd = 1 + ( ( $days - 186 ) % 30 );
	}

	return array( $jy, $jm, $jd );
}

/**
 * Jalali month labels.
 *
 * @return array<int,string>
 */
function webmz_get_jalali_month_names() {
	return array(
		1  => 'فروردین',
		2  => 'اردیبهشت',
		3  => 'خرداد',
		4  => 'تیر',
		5  => 'مرداد',
		6  => 'شهریور',
		7  => 'مهر',
		8  => 'آبان',
		9  => 'آذر',
		10 => 'دی',
		11 => 'بهمن',
		12 => 'اسفند',
	);
}

/**
 * Persian weekday labels aligned with WordPress wp_date( 'w' ).
 *
 * @return array<int,string>
 */
function webmz_get_persian_weekday_names() {
	return array(
		0 => 'یکشنبه',
		1 => 'دوشنبه',
		2 => 'سه‌شنبه',
		3 => 'چهارشنبه',
		4 => 'پنجشنبه',
		5 => 'جمعه',
		6 => 'شنبه',
	);
}

/**
 * Format a Unix timestamp as a Jalali date string for Persian UI.
 *
 * @param int  $timestamp Unix timestamp in site timezone.
 * @param bool $include_weekday Include weekday name.
 * @param bool $include_month_word Include the word «ماه» before year.
 * @return string
 */
function webmz_format_jalali_date( $timestamp, $include_weekday = true, $include_month_word = true ) {
	$timestamp = absint( $timestamp );

	if ( ! $timestamp ) {
		return '';
	}

	list( $jy, $jm, $jd ) = webmz_gregorian_to_jalali(
		(int) wp_date( 'Y', $timestamp ),
		(int) wp_date( 'n', $timestamp ),
		(int) wp_date( 'j', $timestamp )
	);

	$months   = webmz_get_jalali_month_names();
	$month    = isset( $months[ $jm ] ) ? $months[ $jm ] : '';
	$parts    = array();
	$weekdays = webmz_get_persian_weekday_names();
	$weekday  = (int) wp_date( 'w', $timestamp );

	if ( $include_weekday && isset( $weekdays[ $weekday ] ) ) {
		$parts[] = $weekdays[ $weekday ];
	}

	$parts[] = webmz_to_persian_digits( $jd );
	$parts[] = $month;

	if ( $include_month_word ) {
		$parts[] = __( 'ماه', 'tadris' );
	}

	$parts[] = webmz_to_persian_digits( $jy );

	return implode( ' ', array_filter( $parts ) );
}

/**
 * Get the deepest assigned term from a list of terms.
 *
 * @param array<int,\WP_Term> $terms Terms list.
 * @return \WP_Term|null
 */
function webmz_get_deepest_term( $terms ) {
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return null;
	}

	$deepest   = $terms[0];
	$max_depth = -1;

	foreach ( $terms as $term ) {
		if ( ! $term instanceof \WP_Term ) {
			continue;
		}

		$depth = count( get_ancestors( $term->term_id, $term->taxonomy ) );

		if ( $depth > $max_depth ) {
			$max_depth = $depth;
			$deepest   = $term;
		}
	}

	return $deepest instanceof \WP_Term ? $deepest : null;
}

/**
 * Get the deepest assigned category for a post breadcrumb trail.
 *
 * @param int $post_id Post ID.
 * @return \WP_Term|null
 */
function webmz_get_deepest_post_category( $post_id ) {
	$post_id    = absint( $post_id );
	$categories = get_the_category( $post_id );

	return webmz_get_deepest_term( $categories );
}

/**
 * Get the deepest assigned product category for a product breadcrumb trail.
 *
 * @param int $product_id Product ID.
 * @return \WP_Term|null
 */
function webmz_get_deepest_product_category( $product_id ) {
	$product_id = absint( $product_id );
	$terms      = get_the_terms( $product_id, 'product_cat' );

	return webmz_get_deepest_term( $terms );
}

/**
 * Append hierarchical term items to a breadcrumb trail.
 *
 * @param array<int,array{label:string,url:string,current:bool}> $items      Breadcrumb items.
 * @param \WP_Term                                                $term       Target term.
 * @param bool                                                    $as_current Whether the term is the current page.
 * @return void
 */
function webmz_append_breadcrumb_term_items( array &$items, $term, $as_current = false ) {
	if ( ! $term instanceof \WP_Term || is_wp_error( $term ) ) {
		return;
	}

	$ancestors = array_reverse( get_ancestors( $term->term_id, $term->taxonomy ) );

	foreach ( $ancestors as $ancestor_id ) {
		$ancestor = get_term( $ancestor_id, $term->taxonomy );

		if ( ! $ancestor instanceof \WP_Term || is_wp_error( $ancestor ) ) {
			continue;
		}

		$ancestor_link = get_term_link( $ancestor );

		if ( is_wp_error( $ancestor_link ) ) {
			continue;
		}

		$items[] = array(
			'label'   => $ancestor->name,
			'url'     => $ancestor_link,
			'current' => false,
		);
	}

	$term_link = get_term_link( $term );

	if ( is_wp_error( $term_link ) ) {
		return;
	}

	$items[] = array(
		'label'   => $term->name,
		'url'     => $as_current ? '' : $term_link,
		'current' => $as_current,
	);
}

/**
 * Default breadcrumb display args.
 *
 * @return array<string,mixed>
 */
function webmz_get_breadcrumb_default_args() {
	return array(
		'home_label'      => get_bloginfo( 'name' ),
		'show_home'       => true,
		'show_blog_page'  => true,
		'show_shop_page'  => true,
		'show_categories' => true,
	);
}

/**
 * Append the WooCommerce shop page to a breadcrumb trail.
 *
 * @param array<int,array{label:string,url:string,current:bool}> $items Breadcrumb items.
 * @param bool                                                   $as_current Whether the shop page is the current page.
 * @return void
 */
function webmz_append_breadcrumb_shop_page( array &$items, $as_current = false ) {
	if ( ! function_exists( 'wc_get_page_id' ) ) {
		return;
	}

	$shop_page_id = absint( wc_get_page_id( 'shop' ) );

	if ( $shop_page_id <= 0 ) {
		return;
	}

	$items[] = array(
		'label'   => get_the_title( $shop_page_id ),
		'url'     => $as_current ? '' : get_permalink( $shop_page_id ),
		'current' => $as_current,
	);
}

/**
 * Build breadcrumb items for the WooCommerce shop archive.
 *
 * @param array $args Optional display flags.
 * @return array<int,array{label:string,url:string,current:bool}>
 */
function webmz_get_shop_breadcrumb_items( $args = array() ) {
	$args  = wp_parse_args( $args, webmz_get_breadcrumb_default_args() );
	$items = array();

	if ( $args['show_home'] ) {
		$items[] = array(
			'label'   => (string) $args['home_label'],
			'url'     => home_url( '/' ),
			'current' => false,
		);
	}

	if ( $args['show_shop_page'] ) {
		webmz_append_breadcrumb_shop_page( $items, true );
	} else {
		$shop_page_id = function_exists( 'wc_get_page_id' ) ? absint( wc_get_page_id( 'shop' ) ) : 0;
		$label        = $shop_page_id > 0 ? get_the_title( $shop_page_id ) : esc_html__( 'فروشگاه', 'tadris' );

		$items[] = array(
			'label'   => $label,
			'url'     => '',
			'current' => true,
		);
	}

	return $items;
}

/**
 * Build breadcrumb items for a WooCommerce product taxonomy archive.
 *
 * @param \WP_Term $term Taxonomy term.
 * @param array    $args Optional display flags.
 * @return array<int,array{label:string,url:string,current:bool}>
 */
function webmz_get_product_taxonomy_breadcrumb_items( $term, $args = array() ) {
	if ( ! $term instanceof \WP_Term || is_wp_error( $term ) ) {
		return array();
	}

	$args  = wp_parse_args( $args, webmz_get_breadcrumb_default_args() );
	$items = array();

	if ( $args['show_home'] ) {
		$items[] = array(
			'label'   => (string) $args['home_label'],
			'url'     => home_url( '/' ),
			'current' => false,
		);
	}

	if ( $args['show_shop_page'] ) {
		webmz_append_breadcrumb_shop_page( $items, false );
	}

	if ( $args['show_categories'] && is_taxonomy_hierarchical( $term->taxonomy ) ) {
		webmz_append_breadcrumb_term_items( $items, $term, true );
	} else {
		$items[] = array(
			'label'   => $term->name,
			'url'     => '',
			'current' => true,
		);
	}

	return $items;
}

/**
 * Build breadcrumb items for teachers archive.
 *
 * @param array $args Optional display flags.
 * @return array<int,array{label:string,url:string,current:bool}>
 */
function webmz_get_teacher_archive_breadcrumb_items( $args = array() ) {
	$args  = wp_parse_args( $args, webmz_get_breadcrumb_default_args() );
	$items = array();

	if ( $args['show_home'] ) {
		$items[] = array(
			'label'   => (string) $args['home_label'],
			'url'     => home_url( '/' ),
			'current' => false,
		);
	}

	$items[] = array(
		'label'   => esc_html__( 'آرشیو اساتید', 'tadris' ),
		'url'     => '',
		'current' => true,
	);

	return $items;
}

/**
 * Render breadcrumb navigation for the current context.
 *
 * @param string $wrapper_class Wrapper class.
 * @param array  $args          Optional breadcrumb args.
 * @return void
 */
function webmz_render_breadcrumb_nav( $wrapper_class = 'webmz-wc-breadcrumb', $args = array() ) {
	$items = webmz_get_context_breadcrumb_items( $args );

	if ( empty( $items ) ) {
		return;
	}

	echo '<nav class="' . esc_attr( $wrapper_class ) . ' webmz-wc-breadcrumb" aria-label="' . esc_attr__( 'مسیر صفحه', 'tadris' ) . '">';
	echo '<ol class="webmz-wc-breadcrumb__list">';

	$total = count( $items );
	foreach ( $items as $index => $item ) {
		$is_current = ! empty( $item['current'] ) || ( $index === $total - 1 && empty( $item['url'] ) );
		echo '<li class="webmz-wc-breadcrumb__item' . ( $is_current ? ' is-current' : '' ) . '">';

		if ( $index > 0 ) {
			echo '<span class="webmz-wc-breadcrumb__sep" aria-hidden="true">/</span>';
		}

		if ( $is_current || empty( $item['url'] ) ) {
			echo '<span class="webmz-wc-breadcrumb__current"' . ( $is_current ? ' aria-current="page"' : '' ) . '>' . esc_html( $item['label'] ) . '</span>';
		} else {
			echo '<a class="webmz-wc-breadcrumb__link" href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
		}

		echo '</li>';
	}

	echo '</ol></nav>';
}

/**
 * Build breadcrumb items for the current front-end context.
 *
 * @param array $args Optional display flags.
 * @return array<int,array{label:string,url:string,current:bool}>
 */
function webmz_get_context_breadcrumb_items( $args = array() ) {
	if ( class_exists( 'WooCommerce' ) ) {
		if ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
			$term = get_queried_object();

			if ( $term instanceof \WP_Term ) {
				return webmz_get_product_taxonomy_breadcrumb_items( $term, $args );
			}
		}

		if ( function_exists( 'is_shop' ) && is_shop() && ! is_search() ) {
			return webmz_get_shop_breadcrumb_items( $args );
		}
	}

	if ( defined( 'WEBMZ_TEACHER_POST_TYPE' ) && is_post_type_archive( WEBMZ_TEACHER_POST_TYPE ) ) {
		return webmz_get_teacher_archive_breadcrumb_items( $args );
	}

	return webmz_get_post_breadcrumb_items( webmz_get_context_post_id(), $args );
}

/**
 * Build breadcrumb items for the current post/page context.
 *
 * Each item contains label, url and current keys.
 *
 * @param int   $post_id Post ID.
 * @param array $args    Optional display flags.
 * @return array<int,array{label:string,url:string,current:bool}>
 */
function webmz_get_post_breadcrumb_items( $post_id, $args = array() ) {
	$post_id = absint( $post_id );
	$post    = get_post( $post_id );

	if ( ! $post ) {
		return array();
	}

	$args = wp_parse_args( $args, webmz_get_breadcrumb_default_args() );

	$items = array();

	if ( $args['show_home'] ) {
		$items[] = array(
			'label'   => (string) $args['home_label'],
			'url'     => home_url( '/' ),
			'current' => false,
		);
	}

	if ( 'post' === $post->post_type ) {
		$posts_page = absint( get_option( 'page_for_posts' ) );

		if ( $args['show_blog_page'] && $posts_page ) {
			$items[] = array(
				'label'   => get_the_title( $posts_page ),
				'url'     => get_permalink( $posts_page ),
				'current' => false,
			);
		}

		if ( $args['show_categories'] ) {
			$category = webmz_get_deepest_post_category( $post_id );

			if ( $category instanceof \WP_Term ) {
				webmz_append_breadcrumb_term_items( $items, $category, false );
			}
		}
	} elseif ( 'product' === $post->post_type && class_exists( 'WooCommerce' ) ) {
		if ( $args['show_shop_page'] ) {
			webmz_append_breadcrumb_shop_page( $items, false );
		}

		if ( $args['show_categories'] ) {
			$category = webmz_get_deepest_product_category( $post_id );

			if ( $category instanceof \WP_Term ) {
				webmz_append_breadcrumb_term_items( $items, $category, false );
			}
		}
	} elseif ( defined( 'WEBMZ_TEACHER_POST_TYPE' ) && WEBMZ_TEACHER_POST_TYPE === $post->post_type ) {
		$archive_link = get_post_type_archive_link( WEBMZ_TEACHER_POST_TYPE );

		if ( $archive_link ) {
			$items[] = array(
				'label'   => esc_html__( 'آرشیو اساتید', 'tadris' ),
				'url'     => $archive_link,
				'current' => false,
			);
		}
	} elseif ( 'page' === $post->post_type ) {
		$ancestors = array_reverse( get_post_ancestors( $post_id ) );

		foreach ( $ancestors as $ancestor_id ) {
			$items[] = array(
				'label'   => get_the_title( $ancestor_id ),
				'url'     => get_permalink( $ancestor_id ),
				'current' => false,
			);
		}
	}

	$items[] = array(
		'label'   => get_the_title( $post_id ),
		'url'     => '',
		'current' => true,
	);

	return $items;
}

/**
 * Supported heading tags for table of contents widgets.
 *
 * @return array<int,string>
 */
function webmz_get_toc_tag_choices() {
	return array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
}

/**
 * Normalize a list of heading tags.
 *
 * @param array<int,string> $tags Raw tags.
 * @return array<int,string>
 */
function webmz_normalize_toc_allowed_tags( $tags ) {
	$choices = webmz_get_toc_tag_choices();
	$clean   = array();

	foreach ( (array) $tags as $tag ) {
		$tag = strtolower( sanitize_key( (string) $tag ) );
		if ( in_array( $tag, $choices, true ) ) {
			$clean[] = $tag;
		}
	}

	return array_values( array_unique( $clean ) );
}

/**
 * Convert Elementor TOC widget settings to allowed heading tags.
 *
 * @param array<string,mixed> $settings Widget settings.
 * @return array<int,string>
 */
function webmz_toc_settings_to_tags( $settings ) {
	$tags = array();

	foreach ( webmz_get_toc_tag_choices() as $tag ) {
		$key = 'include_' . $tag;
		if ( ! empty( $settings[ $key ] ) && 'yes' === $settings[ $key ] ) {
			$tags[] = $tag;
		}
	}

	return webmz_normalize_toc_allowed_tags( $tags );
}

/**
 * Find Elementor widget settings inside saved layout data.
 *
 * @param array<int,array<string,mixed>> $elements Elementor elements.
 * @param string                         $widget_name Widget type slug.
 * @return array<int,array<string,mixed>>
 */
function webmz_elementor_elements_find_widget_settings( $elements, $widget_name ) {
	$found = array();

	foreach ( (array) $elements as $element ) {
		if ( ! is_array( $element ) ) {
			continue;
		}

		if (
			isset( $element['elType'], $element['widgetType'], $element['settings'] )
			&& 'widget' === $element['elType']
			&& $widget_name === $element['widgetType']
			&& is_array( $element['settings'] )
		) {
			$found[] = $element['settings'];
		}

		if ( ! empty( $element['elements'] ) ) {
			$found = array_merge(
				$found,
				webmz_elementor_elements_find_widget_settings( $element['elements'], $widget_name )
			);
		}
	}

	return $found;
}

/**
 * Collect all TOC heading tags configured in a saved layout.
 *
 * @param int $layout_id Layout post ID.
 * @return array<int,string>
 */
function webmz_collect_toc_tags_from_layout( $layout_id ) {
	$layout_id = function_exists( 'webmz_validate_layout_id' ) ? webmz_validate_layout_id( $layout_id ) : absint( $layout_id );

	if ( ! $layout_id ) {
		return array();
	}

	$raw_data = get_post_meta( $layout_id, '_elementor_data', true );
	$elements = is_string( $raw_data ) ? json_decode( $raw_data, true ) : $raw_data;

	if ( ! is_array( $elements ) ) {
		return array();
	}

	$widgets = webmz_elementor_elements_find_widget_settings( $elements, 'webmz-post-toc' );
	$tags    = array();

	foreach ( $widgets as $settings ) {
		$tags = array_merge( $tags, webmz_toc_settings_to_tags( $settings ) );
	}

	return webmz_normalize_toc_allowed_tags( $tags );
}

/**
 * Prime runtime TOC tags for the current singular post request.
 *
 * @return void
 */
function webmz_prime_runtime_toc_tags() {
	if ( is_admin() || ! is_singular( 'post' ) || ( function_exists( 'webmz_is_layout_editing_context' ) && webmz_is_layout_editing_context() ) ) {
		return;
	}

	if ( isset( $GLOBALS['webmz_runtime_toc_tags'] ) ) {
		return;
	}

	$layout_id = function_exists( 'webmz_get_assigned_layout_id' ) ? webmz_get_assigned_layout_id( 'single_post' ) : 0;
	$tags      = $layout_id ? webmz_collect_toc_tags_from_layout( $layout_id ) : array();

	$GLOBALS['webmz_runtime_toc_tags'] = $tags;
}
add_action( 'wp', 'webmz_prime_runtime_toc_tags', 20 );

/**
 * Prepare post content HTML for TOC parsing.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function webmz_prepare_toc_content_source( $post_id ) {
	$post = get_post( absint( $post_id ) );

	if ( ! $post ) {
		return '';
	}

	$content = (string) $post->post_content;

	if ( function_exists( 'do_blocks' ) ) {
		$content = do_blocks( $content );
	}

	$content = do_shortcode( $content );
	$content = wptexturize( $content );
	$content = convert_smilies( $content );
	$content = wpautop( $content );
	$content = shortcode_unautop( $content );

	return $content;
}

/**
 * Generate a unique heading anchor ID.
 *
 * @param string              $text     Heading text.
 * @param array<int,string>   $used_ids Already used IDs.
 * @return string
 */
function webmz_generate_toc_heading_id( $text, &$used_ids ) {
	$base = sanitize_title( wp_strip_all_tags( (string) $text ) );

	if ( '' === $base ) {
		$base = 'section';
	}

	$id = $base;
	$i  = 2;

	while ( in_array( $id, $used_ids, true ) ) {
		$id = $base . '-' . $i;
		++$i;
	}

	$used_ids[] = $id;

	return $id;
}

/**
 * Extract TOC items and inject anchor IDs into heading tags.
 *
 * @param string              $html         Source HTML.
 * @param array<int,string>   $allowed_tags Allowed heading tags.
 * @return array{html:string,items:array<int,array<string,mixed>>}
 */
function webmz_process_toc_headings( $html, $allowed_tags ) {
	$allowed_tags = webmz_normalize_toc_allowed_tags( $allowed_tags );
	$items        = array();
	$used_ids     = array();

	if ( '' === trim( (string) $html ) || empty( $allowed_tags ) ) {
		return array(
			'html'  => (string) $html,
			'items' => array(),
		);
	}

	$tag_pattern = implode( '|', array_map( 'preg_quote', $allowed_tags ) );
	$pattern     = '/<(' . $tag_pattern . ')(\s[^>]*)?>(.*?)<\/\1>/is';
	$index       = 0;

	$new_html = preg_replace_callback(
		$pattern,
		static function ( $matches ) use ( &$items, &$used_ids, &$index ) {
			$tag   = strtolower( $matches[1] );
			$attrs = isset( $matches[2] ) ? $matches[2] : '';
			$inner = $matches[3];
			$text  = wp_strip_all_tags( $inner );

			if ( '' === trim( $text ) ) {
				return $matches[0];
			}

			if ( preg_match( '/\sid=(["\'])([^"\']+)\1/i', $attrs, $id_match ) ) {
				$id = sanitize_title( $id_match[2] );
			} else {
				$id    = webmz_generate_toc_heading_id( $text, $used_ids );
				$attrs = trim( (string) $attrs );
				$attrs = $attrs ? $attrs . ' id="' . esc_attr( $id ) . '"' : ' id="' . esc_attr( $id ) . '"';
			}

			$items[] = array(
				'id'    => $id,
				'text'  => $text,
				'tag'   => $tag,
				'level' => (int) substr( $tag, 1 ),
				'index' => $index,
			);
			++$index;

			return '<' . $tag . $attrs . '>' . $inner . '</' . $tag . '>';
		},
		$html
	);

	return array(
		'html'  => is_string( $new_html ) ? $new_html : (string) $html,
		'items' => $items,
	);
}

/**
 * Get TOC items for a post.
 *
 * @param int                 $post_id      Post ID.
 * @param array<int,string>   $allowed_tags Allowed heading tags.
 * @return array<int,array<string,mixed>>
 */
function webmz_get_post_toc_items( $post_id, $allowed_tags ) {
	$processed = webmz_process_toc_headings(
		webmz_prepare_toc_content_source( $post_id ),
		$allowed_tags
	);

	return $processed['items'];
}

/**
 * Inject TOC anchor IDs into rendered post content when needed.
 *
 * @param string $content Rendered post content.
 * @return string
 */
function webmz_inject_toc_heading_ids_into_content( $content ) {
	if (
		is_admin()
		|| ( function_exists( 'webmz_is_layout_editing_context' ) && webmz_is_layout_editing_context() )
		|| empty( $GLOBALS['webmz_runtime_toc_tags'] )
		|| ! is_array( $GLOBALS['webmz_runtime_toc_tags'] )
	) {
		return $content;
	}

	$processed = webmz_process_toc_headings( $content, $GLOBALS['webmz_runtime_toc_tags'] );

	return $processed['html'];
}
add_filter( 'webmz_dynamic_post_content', 'webmz_inject_toc_heading_ids_into_content', 20 );

/**
 * Get related posts for inline content boxes.
 *
 * @param int $post_id Current post ID.
 * @param int $limit   Maximum number of related posts.
 * @return array<int,\WP_Post>
 */
function webmz_get_inline_related_posts( $post_id, $limit = 3 ) {
	$post_id = absint( $post_id );
	$limit   = max( 1, absint( $limit ) );

	if ( ! $post_id ) {
		return array();
	}

	$categories = wp_get_post_categories( $post_id );

	$args = array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => $limit,
		'post__not_in'           => array( $post_id ),
		'orderby'                => 'rand',
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'update_post_meta_cache' => true,
	);

	if ( ! empty( $categories ) ) {
		$args['category__in'] = $categories;
	}

	$query = new WP_Query( $args );

	return $query->posts;
}

/**
 * Get resolved colors for the related content box.
 *
 * @return array<string,string>
 */
function webmz_get_related_box_colors() {
	$options   = webmz_get_options();
	$use_theme = ! isset( $options['related_use_theme_colors'] ) || 'yes' === $options['related_use_theme_colors'];

	if ( $use_theme ) {
		return array(
			'bg'        => $options['color_background'],
			'accent'    => $options['color_secondary'],
			'btn_bg'    => $options['color_primary'],
			'btn_hover' => $options['color_primary_hover'],
			'title'     => $options['color_text_dark'],
			'desc'      => $options['color_text_gray'],
		);
	}

	return array(
		'bg'        => ! empty( $options['related_box_bg'] ) ? $options['related_box_bg'] : '#eef3f8',
		'accent'    => ! empty( $options['related_box_accent'] ) ? $options['related_box_accent'] : $options['color_secondary'],
		'btn_bg'    => ! empty( $options['related_box_btn_bg'] ) ? $options['related_box_btn_bg'] : $options['color_primary'],
		'btn_hover' => ! empty( $options['related_box_btn_hover'] ) ? $options['related_box_btn_hover'] : $options['color_primary_hover'],
		'title'     => ! empty( $options['related_box_title_color'] ) ? $options['related_box_title_color'] : $options['color_text_dark'],
		'desc'      => ! empty( $options['related_box_desc_color'] ) ? $options['related_box_desc_color'] : $options['color_text_gray'],
	);
}

/**
 * Build CSS custom properties for the related content box.
 *
 * @return string
 */
function webmz_get_related_box_css_vars_declaration() {
	$colors = webmz_get_related_box_colors();

	return '--webmz-related-bg:' . esc_attr( $colors['bg'] ) . ';' .
		'--webmz-related-accent:' . esc_attr( $colors['accent'] ) . ';' .
		'--webmz-related-btn-bg:' . esc_attr( $colors['btn_bg'] ) . ';' .
		'--webmz-related-btn-hover:' . esc_attr( $colors['btn_hover'] ) . ';' .
		'--webmz-related-title-color:' . esc_attr( $colors['title'] ) . ';' .
		'--webmz-related-desc-color:' . esc_attr( $colors['desc'] ) . ';';
}

/**
 * Render a related content box from manual or block attributes.
 *
 * @param array<string,mixed> $args Box content arguments.
 * @return string
 */
function webmz_render_related_content_box( $args ) {
	$args = wp_parse_args(
		is_array( $args ) ? $args : array(),
		array(
			'titleBefore'    => __( 'در مورد ', 'tadris' ),
			'titleHighlight' => '',
			'titleAfter'     => __( ' بیشتر بدانید', 'tadris' ),
			'description'    => '',
			'buttonText'     => __( 'مشاهده مقاله', 'tadris' ),
			'buttonUrl'      => '',
			'linkTarget'     => '',
		)
	);

	$title_before    = (string) $args['titleBefore'];
	$title_highlight = (string) $args['titleHighlight'];
	$title_after     = (string) $args['titleAfter'];
	$description     = (string) $args['description'];
	$button_text     = (string) $args['buttonText'];
	$button_url      = (string) $args['buttonUrl'];
	$link_target     = (string) $args['linkTarget'];

	if ( '' === trim( $button_text ) ) {
		$button_text = __( 'مشاهده مقاله', 'tadris' );
	}

	if ( '' === trim( $button_url ) ) {
		$button_url = '#';
	}

	$heading_html = esc_html( $title_before );

	if ( '' !== trim( $title_highlight ) ) {
		$heading_html .= '<strong>' . esc_html( $title_highlight ) . '</strong>';
	}

	$heading_html .= esc_html( $title_after );

	$link_attrs = '';
	if ( '_blank' === $link_target ) {
		$link_attrs = ' target="_blank" rel="noopener noreferrer"';
	}

	ob_start();
	?>
	<aside class="webmz-inline-related" aria-label="<?php esc_attr_e( 'مطلب مرتبط', 'tadris' ); ?>">
		<div class="webmz-inline-related__inner">
			<div class="webmz-inline-related__content">
				<p class="webmz-inline-related__title"><?php echo $heading_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				<?php if ( '' !== trim( $description ) ) : ?>
					<p class="webmz-inline-related__desc"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
			</div>
			<a class="webmz-inline-related__btn" href="<?php echo esc_url( $button_url ); ?>"<?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $button_text ); ?></a>
		</div>
	</aside>
	<?php
	return (string) ob_get_clean();
}

/**
 * Build heading markup for an inline related box.
 *
 * @param string $title    Related post title.
 * @param array  $settings Widget settings.
 * @return string
 */
function webmz_inline_related_heading_html( $title, $settings ) {
	$template = isset( $settings['related_title_template'] ) ? trim( (string) $settings['related_title_template'] ) : '';

	if ( '' === $template ) {
		$template = esc_html__( 'در مورد {title} بیشتر بدانید', 'tadris' );
	}

	$parts = explode( '{title}', $template, 2 );

	if ( 2 === count( $parts ) ) {
		return esc_html( $parts[0] ) . '<strong>' . esc_html( $title ) . '</strong>' . esc_html( $parts[1] );
	}

	return esc_html( $template );
}

/**
 * Resolve button label for an inline related box.
 *
 * @param int                  $post_id  Related post ID.
 * @param array<string,mixed>  $settings Widget settings.
 * @return string
 */
function webmz_inline_related_button_label( $post_id, $settings ) {
	$custom = isset( $settings['related_button_text'] ) ? trim( (string) $settings['related_button_text'] ) : '';

	if ( '' !== $custom ) {
		return $custom;
	}

	if ( function_exists( 'webmz_post_has_video_url' ) && webmz_post_has_video_url( $post_id ) ) {
		return esc_html__( 'مشاهده ویدیو', 'tadris' );
	}

	return esc_html__( 'مشاهده مقاله', 'tadris' );
}

/**
 * Render one inline related post box.
 *
 * @param \WP_Post|object       $related_post Related post object.
 * @param array<string,mixed>   $settings     Widget settings.
 * @param bool                  $is_preview   Whether this is an editor preview.
 * @return string
 */
function webmz_render_inline_related_box( $related_post, $settings = array(), $is_preview = false ) {
	$post_id   = isset( $related_post->ID ) ? absint( $related_post->ID ) : 0;
	$title     = isset( $related_post->post_title ) ? (string) $related_post->post_title : '';
	$excerpt   = isset( $related_post->post_excerpt ) ? (string) $related_post->post_excerpt : '';
	$permalink = '#';

	if ( $post_id && ! $is_preview ) {
		$title     = get_the_title( $post_id );
		$excerpt   = get_the_excerpt( $post_id );
		$permalink = get_permalink( $post_id );
	} elseif ( isset( $related_post->permalink ) ) {
		$permalink = (string) $related_post->permalink;
	}

	if ( '' === trim( $excerpt ) && $post_id && ! $is_preview ) {
		$excerpt = wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ), 24, '…' );
	}

	$template = isset( $settings['related_title_template'] ) ? trim( (string) $settings['related_title_template'] ) : '';

	if ( '' === $template ) {
		$template = esc_html__( 'در مورد {title} بیشتر بدانید', 'tadris' );
	}

	$parts = explode( '{title}', $template, 2 );

	if ( 2 === count( $parts ) ) {
		$title_before    = $parts[0];
		$title_highlight = $title;
		$title_after     = $parts[1];
	} else {
		$title_before    = '';
		$title_highlight = $title;
		$title_after     = '';
	}

	$button_text = webmz_inline_related_button_label( $post_id, is_array( $settings ) ? $settings : array() );

	return webmz_render_related_content_box(
		array(
			'titleBefore'    => $title_before,
			'titleHighlight' => $title_highlight,
			'titleAfter'     => $title_after,
			'description'    => $excerpt,
			'buttonText'     => $button_text,
			'buttonUrl'      => $permalink,
		)
	);
}

/**
 * Inject inline related post boxes after selected paragraphs.
 *
 * @param string $content Rendered post content.
 * @return string
 */
function webmz_inject_inline_related_posts_into_content( $content ) {
	$runtime = isset( $GLOBALS['webmz_runtime_inline_related'] ) ? $GLOBALS['webmz_runtime_inline_related'] : null;

	if (
		! is_array( $runtime )
		|| empty( $runtime['enabled'] )
		|| empty( $runtime['post_id'] )
	) {
		return $content;
	}

	$settings  = isset( $runtime['settings'] ) && is_array( $runtime['settings'] ) ? $runtime['settings'] : array();
	$post_id   = absint( $runtime['post_id'] );
	$interval  = max( 1, isset( $settings['related_interval'] ) ? absint( $settings['related_interval'] ) : 3 );
	$max_boxes = max( 1, isset( $settings['related_max_boxes'] ) ? absint( $settings['related_max_boxes'] ) : 3 );
	$related   = webmz_get_inline_related_posts( $post_id, $max_boxes );

	if ( empty( $related ) ) {
		return $content;
	}

	$paragraph_count = 0;
	$box_count       = 0;
	$related_index   = 0;

	$content = preg_replace_callback(
		'/<\/p>/i',
		function ( $matches ) use ( &$paragraph_count, &$box_count, &$related_index, $interval, $max_boxes, $related, $settings ) {
			++$paragraph_count;

			if ( $box_count >= $max_boxes || 0 !== ( $paragraph_count % $interval ) ) {
				return $matches[0];
			}

			$related_post = $related[ $related_index % count( $related ) ];
			++$related_index;
			++$box_count;

			return $matches[0] . webmz_render_inline_related_box( $related_post, $settings );
		},
		$content
	);

	return is_string( $content ) ? $content : '';
}
add_filter( 'webmz_dynamic_post_content', 'webmz_inject_inline_related_posts_into_content', 30 );

/**
 * Image size used for post/product loop thumbnails (uncropped).
 *
 * @return string
 */
function webmz_get_loop_image_size() {
	return (string) apply_filters( 'webmz_loop_image_size', 'full' );
}

/**
 * Default HTML attributes for loop/featured images.
 *
 * @param array<string,mixed> $attrs Optional attribute overrides.
 * @return array<string,mixed>
 */
function webmz_get_loop_image_attrs( $attrs = array() ) {
	$defaults = array(
		'class'    => 'theme-loop-image',
		'loading'  => 'lazy',
		'decoding' => 'async',
	);

	$attrs = wp_parse_args( $attrs, $defaults );

	if ( ! empty( $attrs['class'] ) && false === strpos( (string) $attrs['class'], 'theme-loop-image' ) ) {
		$attrs['class'] = trim( 'theme-loop-image ' . (string) $attrs['class'] );
	}

	return $attrs;
}

/**
 * Render a post thumbnail for loops/archives without forced crop sizes.
 *
 * @param int|\WP_Post|null   $post  Post object or ID.
 * @param array<string,mixed> $attrs Optional image attributes.
 * @return string
 */
function webmz_get_post_loop_thumbnail( $post = null, $attrs = array() ) {
	$post = get_post( $post );

	if ( ! $post || ! has_post_thumbnail( $post ) ) {
		return '';
	}

	return (string) get_the_post_thumbnail( $post, webmz_get_loop_image_size(), webmz_get_loop_image_attrs( $attrs ) );
}

/**
 * Render a WooCommerce product image for loops without forced crop sizes.
 *
 * @param \WC_Product|null    $product Product object.
 * @param array<string,mixed> $attrs   Optional image attributes.
 * @return string
 */
function webmz_get_product_loop_image( $product = null, $attrs = array() ) {
	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
		return '';
	}

	$image_id = $product->get_image_id();

	if ( $image_id <= 0 ) {
		return $product->get_image();
	}

	return (string) wp_get_attachment_image(
		$image_id,
		webmz_get_loop_image_size(),
		false,
		webmz_get_loop_image_attrs( $attrs )
	);
}

/**
 * Render an attachment image for loops without forced crop sizes.
 *
 * @param int                 $attachment_id Attachment ID.
 * @param array<string,mixed> $attrs         Optional image attributes.
 * @return string
 */
function webmz_get_attachment_loop_image( $attachment_id, $attrs = array() ) {
	$attachment_id = absint( $attachment_id );

	if ( $attachment_id <= 0 ) {
		return '';
	}

	return (string) wp_get_attachment_image(
		$attachment_id,
		webmz_get_loop_image_size(),
		false,
		webmz_get_loop_image_attrs( $attrs )
	);
}

