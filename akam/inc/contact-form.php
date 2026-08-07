<?php
/**
 * Secure Ajax contact form storage.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Contact message post type slug.
 */
const WEBMZ_CONTACT_MESSAGE_POST_TYPE = 'webmz_contact_msg';

/**
 * Register contact messages post type.
 *
 * @return void
 */
function webmz_register_contact_message_post_type() {
	register_post_type(
		WEBMZ_CONTACT_MESSAGE_POST_TYPE,
		array(
			'labels'              => array(
				'name'               => __( 'فرم تماس', 'tadris' ),
				'singular_name'      => __( 'پیام فرم تماس', 'tadris' ),
				'menu_name'          => __( 'فرم تماس', 'tadris' ),
				'add_new_item'       => __( 'افزودن پیام تماس', 'tadris' ),
				'edit_item'          => __( 'مشاهده پیام تماس', 'tadris' ),
				'view_item'          => __( 'مشاهده پیام', 'tadris' ),
				'search_items'       => __( 'جستجوی پیام‌ها', 'tadris' ),
				'not_found'          => __( 'پیامی یافت نشد.', 'tadris' ),
				'not_found_in_trash' => __( 'پیامی در زباله‌دان یافت نشد.', 'tadris' ),
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
			'menu_icon'           => 'dashicons-email-alt2',
		)
	);
}
add_action( 'init', 'webmz_register_contact_message_post_type' );

/**
 * Count unread contact messages.
 *
 * @return int
 */
function webmz_contact_message_get_unread_count() {
	$query = new WP_Query(
		array(
			'post_type'      => WEBMZ_CONTACT_MESSAGE_POST_TYPE,
			'post_status'    => 'private',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation' => 'OR',
				array(
					'key'     => '_webmz_contact_read',
					'value'   => 'yes',
					'compare' => '!=',
				),
				array(
					'key'     => '_webmz_contact_read',
					'compare' => 'NOT EXISTS',
				),
			),
		)
	);

	return absint( $query->found_posts );
}

/**
 * Add unread badge to the contact form submenu.
 *
 * @return void
 */
function webmz_contact_message_admin_menu_badge() {
	global $submenu;

	if ( empty( $submenu['webmz-options'] ) || ! is_array( $submenu['webmz-options'] ) ) {
		return;
	}

	$count = webmz_contact_message_get_unread_count();
	if ( ! $count ) {
		return;
	}

	$target_slug = 'edit.php?post_type=' . WEBMZ_CONTACT_MESSAGE_POST_TYPE;
	$badge       = ' <span class="awaiting-mod count-' . esc_attr( $count ) . '"><span class="pending-count">' . esc_html( number_format_i18n( $count ) ) . '</span></span>';

	foreach ( $submenu['webmz-options'] as $index => $item ) {
		if ( isset( $item[2] ) && $target_slug === $item[2] ) {
			$submenu['webmz-options'][ $index ][0] .= $badge;
			break;
		}
	}
}
add_action( 'admin_menu', 'webmz_contact_message_admin_menu_badge', 99 );

/**
 * Sign a rendered contact form config.
 *
 * @param string $encoded_config Base64 encoded JSON config.
 * @return string
 */
function webmz_contact_form_sign_config( $encoded_config ) {
	return hash_hmac( 'sha256', (string) $encoded_config, wp_salt( 'auth' ) );
}

/**
 * Decode and verify posted contact form config.
 *
 * @param string $encoded_config Encoded JSON.
 * @param string $signature      HMAC signature.
 * @return array<string,mixed>|\WP_Error
 */
function webmz_contact_form_decode_config( $encoded_config, $signature ) {
	$encoded_config = (string) $encoded_config;
	$signature      = (string) $signature;
	$expected       = webmz_contact_form_sign_config( $encoded_config );

	if ( '' === $encoded_config || '' === $signature || ! hash_equals( $expected, $signature ) ) {
		return new WP_Error( 'invalid_signature', __( 'اعتبار فرم تأیید نشد. لطفاً صفحه را تازه‌سازی کنید.', 'tadris' ) );
	}

	$json = base64_decode( $encoded_config, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
	if ( false === $json || strlen( $json ) > 50000 ) {
		return new WP_Error( 'invalid_config', __( 'تنظیمات فرم نامعتبر است.', 'tadris' ) );
	}

	$config = json_decode( $json, true );
	if ( ! is_array( $config ) ) {
		return new WP_Error( 'invalid_config', __( 'تنظیمات فرم نامعتبر است.', 'tadris' ) );
	}

	return $config;
}

/**
 * Get client IP for rate limiting.
 *
 * @return string
 */
function webmz_contact_form_get_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

	if ( ! rest_is_ip_address( $ip ) ) {
		return 'unknown';
	}

	return $ip;
}

/**
 * Check and update simple hourly per-IP rate limit.
 *
 * @param int $limit Maximum submissions per hour.
 * @return true|\WP_Error
 */
function webmz_contact_form_check_rate_limit( $limit = 5 ) {
	$limit   = max( 1, min( 50, absint( $limit ) ) );
	$ip      = webmz_contact_form_get_client_ip();
	$key     = 'webmz_contact_form_rate_' . md5( $ip );
	$current = absint( get_transient( $key ) );

	if ( $current >= $limit ) {
		return new WP_Error( 'rate_limited', __( 'تعداد ارسال‌های شما در یک ساعت گذشته زیاد است. لطفاً بعداً دوباره تلاش کنید.', 'tadris' ) );
	}

	set_transient( $key, $current + 1, HOUR_IN_SECONDS );

	return true;
}

/**
 * Normalize configured fields and remove unsupported field types.
 *
 * @param array<int,array<string,mixed>> $fields Raw fields.
 * @return array<int,array<string,mixed>>
 */
function webmz_contact_form_normalize_fields( $fields ) {
	$clean = array();
	$used  = array();

	foreach ( (array) $fields as $field ) {
		if ( ! is_array( $field ) ) {
			continue;
		}

		$id = ! empty( $field['id'] ) ? sanitize_key( $field['id'] ) : '';
		if ( '' === $id || isset( $used[ $id ] ) ) {
			continue;
		}

		$type = ! empty( $field['type'] ) ? sanitize_key( $field['type'] ) : 'text';
		if ( ! in_array( $type, array( 'text', 'email', 'tel', 'textarea', 'select', 'checkbox' ), true ) ) {
			continue;
		}

		$used[ $id ] = true;
		$clean[]    = array(
			'id'       => $id,
			'label'    => ! empty( $field['label'] ) ? sanitize_text_field( $field['label'] ) : $id,
			'type'     => $type,
			'required' => ! empty( $field['required'] ),
			'options'  => ! empty( $field['options'] ) && is_array( $field['options'] ) ? array_map( 'sanitize_text_field', $field['options'] ) : array(),
		);
	}

	return $clean;
}

/**
 * Validate and sanitize submitted values.
 *
 * @param array<int,array<string,mixed>> $fields Field definitions.
 * @return array<string,array<string,string>>|\WP_Error
 */
function webmz_contact_form_collect_values( $fields ) {
	$posted = isset( $_POST['fields'] ) && is_array( $_POST['fields'] ) ? wp_unslash( $_POST['fields'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$values = array();

	foreach ( $fields as $field ) {
		$field_id = $field['id'];
		$type     = $field['type'];
		$label    = $field['label'];
		$value    = isset( $posted[ $field_id ] ) ? $posted[ $field_id ] : '';

		if ( is_array( $value ) ) {
			$value = implode( ', ', array_map( 'sanitize_text_field', $value ) );
		}

		if ( 'checkbox' === $type ) {
			$value = $value ? __( 'بله', 'tadris' ) : '';
		} elseif ( 'email' === $type ) {
			$value = sanitize_email( $value );
		} elseif ( 'textarea' === $type ) {
			$value = sanitize_textarea_field( $value );
			$value = function_exists( 'mb_substr' ) ? mb_substr( $value, 0, 5000 ) : substr( $value, 0, 5000 );
		} else {
			$value = sanitize_text_field( $value );
			$value = function_exists( 'mb_substr' ) ? mb_substr( $value, 0, 500 ) : substr( $value, 0, 500 );
		}

		if ( ! empty( $field['required'] ) && '' === trim( (string) $value ) ) {
			return new WP_Error( 'required_field', sprintf( __( 'فیلد «%s» اجباری است.', 'tadris' ), $label ) );
		}

		if ( 'email' === $type && '' !== $value && ! is_email( $value ) ) {
			return new WP_Error( 'invalid_email', sprintf( __( 'ایمیل وارد شده در فیلد «%s» معتبر نیست.', 'tadris' ), $label ) );
		}

		if ( 'select' === $type && '' !== $value && ! empty( $field['options'] ) && ! in_array( $value, $field['options'], true ) ) {
			return new WP_Error( 'invalid_choice', sprintf( __( 'مقدار انتخاب‌شده برای «%s» معتبر نیست.', 'tadris' ), $label ) );
		}

		$values[ $field_id ] = array(
			'label' => $label,
			'type'  => $type,
			'value' => (string) $value,
		);
	}

	return $values;
}

/**
 * Render submitted values as admin-safe plain text.
 *
 * @param array<string,array<string,string>> $values Submitted values.
 * @return string
 */
function webmz_contact_form_values_to_text( $values ) {
	$lines = array();

	foreach ( $values as $item ) {
		$lines[] = $item['label'] . ': ' . ( '' !== trim( $item['value'] ) ? $item['value'] : '-' );
	}

	return implode( "\n\n", $lines );
}

/**
 * Ajax submit callback.
 *
 * @return void
 */
function webmz_ajax_contact_form_submit() {
	check_ajax_referer( 'webmz_ajax_contact_form', 'nonce' );

	if ( ! empty( $_POST['webmz_contact_website'] ) ) {
		wp_send_json_error( array( 'message' => __( 'ارسال فرم تأیید نشد.', 'tadris' ) ), 400 );
	}

	$config = webmz_contact_form_decode_config(
		isset( $_POST['config'] ) ? sanitize_text_field( wp_unslash( $_POST['config'] ) ) : '',
		isset( $_POST['signature'] ) ? sanitize_text_field( wp_unslash( $_POST['signature'] ) ) : ''
	);

	if ( is_wp_error( $config ) ) {
		wp_send_json_error( array( 'message' => $config->get_error_message() ), 400 );
	}

	$hourly_limit = isset( $config['hourly_limit'] ) ? absint( $config['hourly_limit'] ) : 5;
	$rate_limit   = webmz_contact_form_check_rate_limit( $hourly_limit );
	if ( is_wp_error( $rate_limit ) ) {
		wp_send_json_error( array( 'message' => $rate_limit->get_error_message() ), 429 );
	}

	$fields = webmz_contact_form_normalize_fields( isset( $config['fields'] ) && is_array( $config['fields'] ) ? $config['fields'] : array() );
	if ( empty( $fields ) || count( $fields ) > 20 ) {
		wp_send_json_error( array( 'message' => __( 'تنظیمات فیلدهای فرم معتبر نیست.', 'tadris' ) ), 400 );
	}

	$values = webmz_contact_form_collect_values( $fields );
	if ( is_wp_error( $values ) ) {
		wp_send_json_error( array( 'message' => $values->get_error_message() ), 400 );
	}

	$form_title = ! empty( $config['form_title'] ) ? sanitize_text_field( $config['form_title'] ) : __( 'فرم تماس', 'tadris' );
	$success_message = ! empty( $config['success_message'] ) ? sanitize_text_field( $config['success_message'] ) : __( 'درخواست شما با موفقیت ثبت شد.', 'tadris' );
	$post_title = sprintf(
		/* translators: 1: Form title, 2: Date. */
		__( '%1$s - %2$s', 'tadris' ),
		$form_title,
		wp_date( 'Y/m/d H:i' )
	);

	$post_id = wp_insert_post(
		array(
			'post_type'    => WEBMZ_CONTACT_MESSAGE_POST_TYPE,
			'post_status'  => 'private',
			'post_title'   => $post_title,
			'post_content' => webmz_contact_form_values_to_text( $values ),
			'meta_input'   => array(
				'_webmz_contact_read'       => 'no',
				'_webmz_contact_form_title' => $form_title,
				'_webmz_contact_values'     => $values,
				'_webmz_contact_ip'         => webmz_contact_form_get_client_ip(),
				'_webmz_contact_user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
			),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'ذخیره پیام انجام نشد. لطفاً دوباره تلاش کنید.', 'tadris' ) ), 500 );
	}

	wp_send_json_success( array( 'message' => $success_message ) );
}
add_action( 'wp_ajax_webmz_ajax_contact_form_submit', 'webmz_ajax_contact_form_submit' );
add_action( 'wp_ajax_nopriv_webmz_ajax_contact_form_submit', 'webmz_ajax_contact_form_submit' );

/**
 * Mark message read when opened in admin.
 *
 * @param \WP_Post $post Post object.
 * @return void
 */
function webmz_contact_message_mark_read_on_edit( $post ) {
	if ( $post instanceof WP_Post && WEBMZ_CONTACT_MESSAGE_POST_TYPE === $post->post_type && current_user_can( 'edit_post', $post->ID ) ) {
		update_post_meta( $post->ID, '_webmz_contact_read', 'yes' );
	}
}
add_action( 'edit_form_after_title', 'webmz_contact_message_mark_read_on_edit' );

/**
 * Add message details metabox.
 *
 * @return void
 */
function webmz_contact_message_add_metaboxes() {
	add_meta_box(
		'webmz-contact-message-details',
		__( 'جزئیات پیام', 'tadris' ),
		'webmz_contact_message_render_metabox',
		WEBMZ_CONTACT_MESSAGE_POST_TYPE,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_' . WEBMZ_CONTACT_MESSAGE_POST_TYPE, 'webmz_contact_message_add_metaboxes' );

/**
 * Render message details.
 *
 * @param \WP_Post $post Post object.
 * @return void
 */
function webmz_contact_message_render_metabox( $post ) {
	$values = get_post_meta( $post->ID, '_webmz_contact_values', true );
	$values = is_array( $values ) ? $values : array();
	?>
	<table class="widefat striped">
		<tbody>
			<?php foreach ( $values as $item ) : ?>
				<tr>
					<th style="width:180px"><?php echo esc_html( isset( $item['label'] ) ? $item['label'] : '' ); ?></th>
					<td><?php echo nl2br( esc_html( isset( $item['value'] ) ? $item['value'] : '' ) ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<p>
		<strong><?php esc_html_e( 'IP:', 'tadris' ); ?></strong>
		<?php echo esc_html( (string) get_post_meta( $post->ID, '_webmz_contact_ip', true ) ); ?>
	</p>
	<?php
}

/**
 * Admin columns.
 *
 * @param array<string,string> $columns Columns.
 * @return array<string,string>
 */
function webmz_contact_message_columns( $columns ) {
	return array(
		'cb'           => isset( $columns['cb'] ) ? $columns['cb'] : '',
		'title'        => __( 'عنوان', 'tadris' ),
		'read_status'  => __( 'وضعیت', 'tadris' ),
		'form_title'   => __( 'فرم', 'tadris' ),
		'message_data' => __( 'خلاصه پیام', 'tadris' ),
		'date'         => __( 'تاریخ', 'tadris' ),
	);
}
add_filter( 'manage_' . WEBMZ_CONTACT_MESSAGE_POST_TYPE . '_posts_columns', 'webmz_contact_message_columns' );

/**
 * Render admin columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 * @return void
 */
function webmz_contact_message_column_content( $column, $post_id ) {
	if ( 'read_status' === $column ) {
		$is_read = 'yes' === get_post_meta( $post_id, '_webmz_contact_read', true );
		echo $is_read ? esc_html__( 'خوانده‌شده', 'tadris' ) : '<strong style="color:#d97706">' . esc_html__( 'نخوانده', 'tadris' ) . '</strong>';
		return;
	}

	if ( 'form_title' === $column ) {
		echo esc_html( (string) get_post_meta( $post_id, '_webmz_contact_form_title', true ) );
		return;
	}

	if ( 'message_data' === $column ) {
		echo esc_html( wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ), 18, '...' ) );
	}
}
add_action( 'manage_' . WEBMZ_CONTACT_MESSAGE_POST_TYPE . '_posts_custom_column', 'webmz_contact_message_column_content', 10, 2 );

/**
 * Add read/unread row action links.
 *
 * @param array<string,string> $actions Row actions.
 * @param \WP_Post             $post    Post object.
 * @return array<string,string>
 */
function webmz_contact_message_row_actions( $actions, $post ) {
	if ( ! $post instanceof WP_Post || WEBMZ_CONTACT_MESSAGE_POST_TYPE !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
		return $actions;
	}

	$is_read = 'yes' === get_post_meta( $post->ID, '_webmz_contact_read', true );
	$action  = $is_read ? 'unread' : 'read';
	$url     = wp_nonce_url(
		add_query_arg(
			array(
				'action'  => 'webmz_contact_mark_' . $action,
				'post_id' => $post->ID,
			),
			admin_url( 'edit.php?post_type=' . WEBMZ_CONTACT_MESSAGE_POST_TYPE )
		),
		'webmz_contact_mark_' . $action . '_' . $post->ID
	);

	$actions[ 'webmz_mark_' . $action ] = '<a href="' . esc_url( $url ) . '">' . esc_html( $is_read ? __( 'علامت‌گذاری به‌عنوان نخوانده', 'tadris' ) : __( 'علامت‌گذاری به‌عنوان خوانده‌شده', 'tadris' ) ) . '</a>';

	return $actions;
}
add_filter( 'post_row_actions', 'webmz_contact_message_row_actions', 10, 2 );

/**
 * Handle read/unread row actions.
 *
 * @return void
 */
function webmz_contact_message_handle_mark_action() {
	if ( empty( $_GET['action'] ) || empty( $_GET['post_id'] ) ) {
		return;
	}

	$action = sanitize_key( wp_unslash( $_GET['action'] ) );
	if ( ! in_array( $action, array( 'webmz_contact_mark_read', 'webmz_contact_mark_unread' ), true ) ) {
		return;
	}

	$post_id = absint( $_GET['post_id'] );
	if ( ! $post_id || WEBMZ_CONTACT_MESSAGE_POST_TYPE !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_die( esc_html__( 'دسترسی کافی ندارید.', 'tadris' ) );
	}

	$state = 'webmz_contact_mark_read' === $action ? 'read' : 'unread';
	check_admin_referer( 'webmz_contact_mark_' . $state . '_' . $post_id );
	update_post_meta( $post_id, '_webmz_contact_read', 'read' === $state ? 'yes' : 'no' );

	wp_safe_redirect( admin_url( 'edit.php?post_type=' . WEBMZ_CONTACT_MESSAGE_POST_TYPE ) );
	exit;
}
add_action( 'admin_init', 'webmz_contact_message_handle_mark_action' );
