<?php
/**
 * Newsletter subscription storage and Ajax handler.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Newsletter subscriber post type slug.
 */
const WEBMZ_NEWSLETTER_POST_TYPE = 'webmz_newsletter_sub';

/**
 * Register newsletter subscribers post type.
 *
 * @return void
 */
function webmz_register_newsletter_post_type() {
	register_post_type(
		WEBMZ_NEWSLETTER_POST_TYPE,
		array(
			'labels'              => array(
				'name'               => __( 'خبرنامه', 'tadris' ),
				'singular_name'      => __( 'عضو خبرنامه', 'tadris' ),
				'menu_name'          => __( 'خبرنامه', 'tadris' ),
				'edit_item'          => __( 'مشاهده عضو', 'tadris' ),
				'view_item'          => __( 'مشاهده عضو', 'tadris' ),
				'search_items'       => __( 'جستجوی اعضا', 'tadris' ),
				'not_found'          => __( 'عضوی یافت نشد.', 'tadris' ),
				'not_found_in_trash' => __( 'عضوی در زباله‌دان یافت نشد.', 'tadris' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => 'webmz-options',
			'show_in_rest'        => false,
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'menu_icon'           => 'dashicons-email',
		)
	);
}
add_action( 'init', 'webmz_register_newsletter_post_type' );

/**
 * Find existing subscriber by email.
 *
 * @param string $email Email address.
 * @return int Post ID or 0.
 */
function webmz_newsletter_find_subscriber( $email ) {
	$email = sanitize_email( $email );
	if ( ! is_email( $email ) ) {
		return 0;
	}

	$query = new WP_Query(
		array(
			'post_type'      => WEBMZ_NEWSLETTER_POST_TYPE,
			'post_status'    => 'private',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => '_webmz_newsletter_email',
					'value' => $email,
				),
			),
		)
	);

	return ! empty( $query->posts[0] ) ? absint( $query->posts[0] ) : 0;
}

/**
 * Ajax newsletter subscribe callback.
 *
 * @return void
 */
function webmz_ajax_newsletter_subscribe() {
	check_ajax_referer( 'webmz_newsletter_subscribe', 'nonce' );

	if ( ! empty( $_POST['webmz_newsletter_website'] ) ) {
		wp_send_json_error( array( 'message' => __( 'ارسال فرم تأیید نشد.', 'tadris' ) ), 400 );
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'لطفاً یک ایمیل معتبر وارد کنید.', 'tadris' ) ), 400 );
	}

	$rate_limit = webmz_contact_form_check_rate_limit( 10 );
	if ( is_wp_error( $rate_limit ) ) {
		wp_send_json_error( array( 'message' => $rate_limit->get_error_message() ), 429 );
	}

	if ( webmz_newsletter_find_subscriber( $email ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'شما قبلاً ثبت‌نام کردید.', 'tadris' ),
			),
			409
		);
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => WEBMZ_NEWSLETTER_POST_TYPE,
			'post_status' => 'private',
			'post_title'  => $email,
			'meta_input'  => array(
				'_webmz_newsletter_email' => $email,
				'_webmz_newsletter_ip'    => webmz_contact_form_get_client_ip(),
				'_webmz_newsletter_date'  => current_time( 'mysql' ),
			),
		),
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		wp_send_json_error( array( 'message' => __( 'ثبت‌نام انجام نشد. لطفاً دوباره تلاش کنید.', 'tadris' ) ), 500 );
	}

	wp_send_json_success(
		array(
			'message' => __( 'عضویت شما با موفقیت ثبت شد.', 'tadris' ),
		)
	);
}
add_action( 'wp_ajax_webmz_newsletter_subscribe', 'webmz_ajax_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_webmz_newsletter_subscribe', 'webmz_ajax_newsletter_subscribe' );

/**
 * Show subscriber email in admin list.
 *
 * @param array<string,string> $columns Columns.
 * @return array<string,string>
 */
function webmz_newsletter_admin_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['webmz_newsletter_email'] = __( 'ایمیل', 'tadris' );
			$new['webmz_newsletter_date']  = __( 'تاریخ عضویت', 'tadris' );
		}
	}
	return $new;
}
add_filter( 'manage_' . WEBMZ_NEWSLETTER_POST_TYPE . '_posts_columns', 'webmz_newsletter_admin_columns' );

/**
 * Render custom admin columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 * @return void
 */
function webmz_newsletter_admin_column_content( $column, $post_id ) {
	if ( 'webmz_newsletter_email' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_webmz_newsletter_email', true ) );
		return;
	}

	if ( 'webmz_newsletter_date' === $column ) {
		$date = get_post_meta( $post_id, '_webmz_newsletter_date', true );
		echo esc_html( $date ? wp_date( 'Y/m/d H:i', strtotime( $date ) ) : '-' );
	}
}
add_action( 'manage_' . WEBMZ_NEWSLETTER_POST_TYPE . '_posts_custom_column', 'webmz_newsletter_admin_column_content', 10, 2 );
