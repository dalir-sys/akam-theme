<?php
/**
 * WebMZ educational support ticket system.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WEBMZ_TICKET_POST_TYPE' ) ) {
	define( 'WEBMZ_TICKET_POST_TYPE', 'webmz_ticket' );
}

/**
 * Default ticket settings.
 *
 * @return array<string,mixed>
 */
function webmz_ticket_default_settings() {
	return array(
		'enabled'                => 'yes',
		'require_purchase'       => 'yes',
		'notify_user_email'      => 'yes',
		'notify_admin_email'     => 'yes',
		'departments'            => "پشتیبانی آموزشی\nپشتیبانی فنی\nمالی و سفارش‌ها",
		'allowed_order_statuses' => array( 'completed', 'processing' ),
		'attachments_enabled'    => 'yes',
		'attachments_max_files'  => 3,
		'attachments_max_size'   => 2048,
		'attachments_extensions' => 'jpg,jpeg,png,gif,webp,pdf',
	);
}

/**
 * Return stored ticket settings.
 *
 * @return array<string,mixed>
 */
function webmz_ticket_get_settings() {
	$settings = get_option( 'webmz_ticket_settings', array() );
	$settings = is_array( $settings ) ? $settings : array();

	return wp_parse_args( $settings, webmz_ticket_default_settings() );
}

/**
 * Check if ticket system is enabled.
 *
 * @return bool
 */
function webmz_ticket_is_enabled() {
	$settings = webmz_ticket_get_settings();

	return 'yes' === $settings['enabled'];
}

/**
 * Multibyte-safe string length fallback.
 *
 * @param string $value Input value.
 * @return int
 */
function webmz_ticket_strlen( $value ) {
	$value = (string) $value;

	return function_exists( 'mb_strlen' ) ? mb_strlen( $value ) : strlen( $value );
}

/**
 * Get clean department list.
 *
 * @return array<int,string>
 */
function webmz_ticket_get_departments() {
	$settings    = webmz_ticket_get_settings();
	$departments = preg_split( '/[\r\n,،]+/u', (string) $settings['departments'] );
	$departments = array_filter(
		array_map(
			'sanitize_text_field',
			array_map( 'trim', (array) $departments )
		)
	);

	if ( empty( $departments ) ) {
		$departments = array( 'پشتیبانی آموزشی' );
	}

	return array_values( array_unique( $departments ) );
}

/**
 * Register ticket post type.
 *
 * @return void
 */
function webmz_ticket_register_post_type() {
	$labels = array(
		'name'               => esc_html__( 'تیکت‌ها', 'tadris' ),
		'singular_name'      => esc_html__( 'تیکت', 'tadris' ),
		'menu_name'          => esc_html__( 'تیکت‌های پشتیبانی', 'tadris' ),
		'add_new'            => esc_html__( 'افزودن تیکت', 'tadris' ),
		'add_new_item'       => esc_html__( 'افزودن تیکت جدید', 'tadris' ),
		'edit_item'          => esc_html__( 'ویرایش تیکت', 'tadris' ),
		'new_item'           => esc_html__( 'تیکت جدید', 'tadris' ),
		'view_item'          => esc_html__( 'مشاهده تیکت', 'tadris' ),
		'search_items'       => esc_html__( 'جستجوی تیکت‌ها', 'tadris' ),
		'not_found'          => esc_html__( 'تیکتی پیدا نشد.', 'tadris' ),
		'not_found_in_trash' => esc_html__( 'تیکتی در زباله‌دان پیدا نشد.', 'tadris' ),
	);

	register_post_type(
		WEBMZ_TICKET_POST_TYPE,
		array(
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => 'webmz-options',
			'show_in_admin_bar'   => false,
			'supports'            => array( 'title' ),
			'menu_icon'           => 'dashicons-sos',
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'has_archive'         => false,
			'rewrite'             => false,
		)
	);
}
add_action( 'init', 'webmz_ticket_register_post_type' );

/**
 * Add WooCommerce account endpoint.
 *
 * @return void
 */
function webmz_ticket_add_account_endpoint() {
	add_rewrite_endpoint( 'support-tickets', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'webmz_ticket_add_account_endpoint' );

/**
 * Flush rewrite rules once for the endpoint.
 *
 * @return void
 */
function webmz_ticket_maybe_flush_rewrite() {
	if ( '1' !== get_option( 'webmz_support_tickets_endpoint_v1' ) ) {
		flush_rewrite_rules( false );
		update_option( 'webmz_support_tickets_endpoint_v1', '1' );
	}
}
add_action( 'init', 'webmz_ticket_maybe_flush_rewrite', 100 );

/**
 * Add settings submenu.
 *
 * @return void
 */
function webmz_ticket_register_settings_page() {
	add_submenu_page(
		'webmz-options',
		esc_html__( 'تنظیمات تیکت پشتیبانی', 'tadris' ),
		esc_html__( 'تنظیمات تیکت', 'tadris' ),
		'manage_options',
		'tadris-ticket-settings',
		'webmz_ticket_render_settings_page'
	);
}
// Ticket settings are rendered inside the main WebMZ theme options panel.
// add_action( 'admin_menu', 'webmz_ticket_register_settings_page' );

/**
 * Sanitize ticket settings.
 *
 * @param array<string,mixed> $raw Raw settings.
 * @return array<string,mixed>
 */
function webmz_ticket_sanitize_settings( $raw ) {
	$defaults = webmz_ticket_default_settings();
	$raw      = is_array( $raw ) ? $raw : array();

	$settings = array(
		'enabled'               => isset( $raw['enabled'] ) && 'yes' === $raw['enabled'] ? 'yes' : 'no',
		'require_purchase'      => isset( $raw['require_purchase'] ) && 'yes' === $raw['require_purchase'] ? 'yes' : 'no',
		'notify_user_email'     => isset( $raw['notify_user_email'] ) && 'yes' === $raw['notify_user_email'] ? 'yes' : 'no',
		'notify_admin_email'    => isset( $raw['notify_admin_email'] ) && 'yes' === $raw['notify_admin_email'] ? 'yes' : 'no',
		'departments'           => '',
		'allowed_order_statuses' => $defaults['allowed_order_statuses'],
	);

	$raw_statuses = isset( $raw['allowed_order_statuses'] ) && is_array( $raw['allowed_order_statuses'] )
		? array_map( 'sanitize_key', wp_unslash( $raw['allowed_order_statuses'] ) )
		: $defaults['allowed_order_statuses'];
	$valid_statuses = function_exists( 'wc_get_order_statuses' )
		? array_map( static function( $status ) { return str_replace( 'wc-', '', sanitize_key( $status ) ); }, array_keys( wc_get_order_statuses() ) )
		: array( 'completed', 'processing', 'on-hold' );
	$settings['allowed_order_statuses'] = array_values( array_intersect( $raw_statuses, $valid_statuses ) );
	if ( empty( $settings['allowed_order_statuses'] ) ) {
		$settings['allowed_order_statuses'] = $defaults['allowed_order_statuses'];
	}

	$raw_departments = isset( $raw['departments'] ) ? sanitize_textarea_field( wp_unslash( $raw['departments'] ) ) : $defaults['departments'];
	$departments     = preg_split( '/[\r\n,،]+/u', (string) $raw_departments );
	$departments     = array_filter(
		array_map(
			'sanitize_text_field',
			array_map( 'trim', (array) $departments )
		)
	);
	$departments     = array_slice( array_values( array_unique( $departments ) ), 0, 30 );

	if ( empty( $departments ) ) {
		$departments = webmz_ticket_get_departments();
	}

	$settings['departments'] = implode( "\n", $departments );

	$settings['attachments_enabled'] = isset( $raw['attachments_enabled'] ) && 'yes' === $raw['attachments_enabled'] ? 'yes' : 'no';
	$settings['attachments_max_files'] = isset( $raw['attachments_max_files'] ) ? max( 1, min( 10, absint( $raw['attachments_max_files'] ) ) ) : 3;
	$settings['attachments_max_size']  = isset( $raw['attachments_max_size'] ) ? max( 100, min( 10240, absint( $raw['attachments_max_size'] ) ) ) : 2048;

	$raw_extensions = isset( $raw['attachments_extensions'] ) ? sanitize_text_field( wp_unslash( $raw['attachments_extensions'] ) ) : $defaults['attachments_extensions'];
	$extensions     = preg_split( '/[\s,،;|]+/u', strtolower( $raw_extensions ) );
	$extensions     = array_values(
		array_unique(
			array_filter(
				array_map(
					static function( $ext ) {
						return strtolower( preg_replace( '/[^a-z0-9]/', '', sanitize_key( $ext ) ) );
					},
					(array) $extensions
				)
			)
		)
	);

	if ( function_exists( 'webmz_ticket_attachment_mime_map' ) ) {
		$mime_map = webmz_ticket_attachment_mime_map();
		$blocked  = webmz_ticket_attachment_blocked_extensions();
		$extensions = array_values(
			array_filter(
				$extensions,
				static function( $ext ) use ( $mime_map, $blocked ) {
					return '' !== $ext && isset( $mime_map[ $ext ] ) && ! in_array( $ext, $blocked, true );
				}
			)
		);
	}

	if ( empty( $extensions ) ) {
		$extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf' );
	}

	$settings['attachments_extensions'] = implode( ',', array_slice( $extensions, 0, 20 ) );

	return $settings;
}

/**
 * Save settings from admin form.
 *
 * @return void
 */
function webmz_ticket_save_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'شما اجازه انجام این عملیات را ندارید.', 'tadris' ) );
	}

	check_admin_referer( 'webmz_ticket_save_settings', 'webmz_ticket_settings_nonce' );

	$raw      = isset( $_POST['webmz_ticket_settings'] ) ? wp_unslash( $_POST['webmz_ticket_settings'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$settings = webmz_ticket_sanitize_settings( $raw );

	update_option( 'webmz_ticket_settings', $settings );

	wp_safe_redirect( add_query_arg( 'updated', '1', admin_url( 'admin.php?page=tadris-ticket-settings' ) ) );
	exit;
}
add_action( 'admin_post_webmz_ticket_save_settings', 'webmz_ticket_save_settings' );

/**
 * Render settings page.
 *
 * @return void
 */
function webmz_ticket_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = webmz_ticket_get_settings();
	?>
	<div class="wrap tadris-ticket-settings" dir="rtl">
		<h1><?php esc_html_e( 'تنظیمات سیستم تیکت پشتیبانی', 'tadris' ); ?></h1>

		<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'تنظیمات تیکت ذخیره شد.', 'tadris' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="tadris-ticket-settings-form">
			<input type="hidden" name="action" value="webmz_ticket_save_settings">
			<?php wp_nonce_field( 'webmz_ticket_save_settings', 'webmz_ticket_settings_nonce' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'فعال‌سازی سیستم تیکت', 'tadris' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="webmz_ticket_settings[enabled]" value="yes" <?php checked( $settings['enabled'], 'yes' ); ?>>
							<?php esc_html_e( 'سیستم تیکت فعال باشد', 'tadris' ); ?>
						</label>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'محدودیت خرید محصول', 'tadris' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="webmz_ticket_settings[require_purchase]" value="yes" <?php checked( $settings['require_purchase'], 'yes' ); ?>>
							<?php esc_html_e( 'فقط کاربرانی که حداقل یک محصول/دوره خریده‌اند بتوانند تیکت ثبت کنند', 'tadris' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'در این حالت کاربر هنگام ثبت تیکت باید یکی از محصولات خریداری‌شده خود را انتخاب کند.', 'tadris' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'دپارتمان‌ها', 'tadris' ); ?></th>
					<td>
						<textarea name="webmz_ticket_settings[departments]" rows="8" class="large-text" placeholder="<?php esc_attr_e( 'هر دپارتمان را در یک خط وارد کنید.', 'tadris' ); ?>"><?php echo esc_textarea( $settings['departments'] ); ?></textarea>
						<p class="description"><?php esc_html_e( 'مثال: پشتیبانی آموزشی، پشتیبانی فنی، مالی و سفارش‌ها', 'tadris' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'اعلان ایمیلی به کاربر', 'tadris' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="webmz_ticket_settings[notify_user_email]" value="yes" <?php checked( $settings['notify_user_email'], 'yes' ); ?>>
							<?php esc_html_e( 'بعد از پاسخ مدیر، ایمیل اطلاع‌رسانی برای کاربر ارسال شود', 'tadris' ); ?>
						</label>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'اعلان ایمیلی به مدیر', 'tadris' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="webmz_ticket_settings[notify_admin_email]" value="yes" <?php checked( $settings['notify_admin_email'], 'yes' ); ?>>
							<?php esc_html_e( 'بعد از ثبت تیکت/پاسخ کاربر، ایمیل اطلاع‌رسانی برای مدیر سایت ارسال شود', 'tadris' ); ?>
						</label>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'پیوست فایل', 'tadris' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="webmz_ticket_settings[attachments_enabled]" value="yes" <?php checked( $settings['attachments_enabled'] ?? 'yes', 'yes' ); ?>>
							<?php esc_html_e( 'امکان پیوست فایل هنگام ثبت تیکت فعال باشد', 'tadris' ); ?>
						</label>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="webmz_ticket_attachments_max_files"><?php esc_html_e( 'حداکثر تعداد فایل', 'tadris' ); ?></label></th>
					<td>
						<input type="number" id="webmz_ticket_attachments_max_files" name="webmz_ticket_settings[attachments_max_files]" min="1" max="10" value="<?php echo esc_attr( $settings['attachments_max_files'] ?? 3 ); ?>" class="small-text">
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="webmz_ticket_attachments_max_size"><?php esc_html_e( 'حداکثر حجم هر فایل (کیلوبایت)', 'tadris' ); ?></label></th>
					<td>
						<input type="number" id="webmz_ticket_attachments_max_size" name="webmz_ticket_settings[attachments_max_size]" min="100" max="10240" value="<?php echo esc_attr( $settings['attachments_max_size'] ?? 2048 ); ?>" class="small-text">
						<p class="description"><?php esc_html_e( 'مثال: 2048 یعنی حداکثر ۲ مگابایت برای هر فایل.', 'tadris' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="webmz_ticket_attachments_extensions"><?php esc_html_e( 'پسوندهای مجاز', 'tadris' ); ?></label></th>
					<td>
						<input type="text" id="webmz_ticket_attachments_extensions" name="webmz_ticket_settings[attachments_extensions]" value="<?php echo esc_attr( $settings['attachments_extensions'] ?? 'jpg,jpeg,png,gif,webp,pdf' ); ?>" class="regular-text">
						<p class="description"><?php esc_html_e( 'پسوندها را با ویرگول جدا کنید. فقط تصویر و PDF پشتیبانی می‌شود.', 'tadris' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button( esc_html__( 'ذخیره تنظیمات تیکت', 'tadris' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Ticket status labels.
 *
 * @return array<string,string>
 */
function webmz_ticket_status_labels() {
	return array(
		'open'     => esc_html__( 'باز', 'tadris' ),
		'answered' => esc_html__( 'پاسخ داده شده', 'tadris' ),
		'user'     => esc_html__( 'در انتظار پاسخ مدیر', 'tadris' ),
		'closed'   => esc_html__( 'بسته شده', 'tadris' ),
	);
}

/**
 * Sanitize ticket status.
 *
 * @param string $status Raw status.
 * @return string
 */
function webmz_ticket_sanitize_status( $status ) {
	$status = sanitize_key( $status );
	$valid  = array_keys( webmz_ticket_status_labels() );

	return in_array( $status, $valid, true ) ? $status : 'open';
}

/**
 * Return status label.
 *
 * @param string $status Status key.
 * @return string
 */
function webmz_ticket_get_status_label( $status ) {
	$labels = webmz_ticket_status_labels();
	$status = webmz_ticket_sanitize_status( $status );

	return isset( $labels[ $status ] ) ? $labels[ $status ] : $labels['open'];
}

/**
 * Get purchased product IDs for user.
 *
 * @param int $user_id User ID.
 * @return array<int>
 */
function webmz_ticket_get_user_purchased_product_ids( $user_id ) {
	$user_id = absint( $user_id );

	if ( ! $user_id || ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_orders' ) ) {
		return array();
	}

	$settings = webmz_ticket_get_settings();
	$statuses = isset( $settings['allowed_order_statuses'] ) && is_array( $settings['allowed_order_statuses'] )
		? array_map( 'sanitize_key', $settings['allowed_order_statuses'] )
		: array( 'completed', 'processing' );

	$orders = wc_get_orders(
		array(
			'customer_id' => $user_id,
			'status'      => $statuses,
			'limit'       => 200,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'return'      => 'objects',
		)
	);

	$product_ids = array();

	foreach ( $orders as $order ) {
		if ( ! $order || ! is_a( $order, 'WC_Order' ) ) {
			continue;
		}

		foreach ( $order->get_items() as $item ) {
			$product_id   = absint( $item->get_product_id() );
			$variation_id = absint( $item->get_variation_id() );

			if ( $product_id ) {
				$product_ids[] = $product_id;
			}
			if ( $variation_id ) {
				$product_ids[] = $variation_id;
			}
		}
	}

	return array_values( array_unique( array_filter( array_map( 'absint', $product_ids ) ) ) );
}

/**
 * Get product options bought by user.
 *
 * @param int $user_id User ID.
 * @return array<int,string>
 */
function webmz_ticket_get_user_purchased_products( $user_id ) {
	$product_ids = webmz_ticket_get_user_purchased_product_ids( $user_id );
	$options     = array();

	foreach ( $product_ids as $product_id ) {
		$product = function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
		if ( $product && $product->exists() ) {
			$options[ $product_id ] = $product->get_name();
		}
	}

	return $options;
}

/**
 * Check if user may create ticket for a product.
 *
 * @param int $user_id User ID.
 * @param int $product_id Product ID.
 * @return bool
 */
function webmz_ticket_user_can_create_for_product( $user_id, $product_id ) {
	$settings = webmz_ticket_get_settings();

	if ( 'yes' !== $settings['require_purchase'] ) {
		return true;
	}

	$product_id = absint( $product_id );
	if ( ! $product_id ) {
		return false;
	}

	return in_array( $product_id, webmz_ticket_get_user_purchased_product_ids( $user_id ), true );
}

/**
 * Get ticket messages.
 *
 * @param int $ticket_id Ticket ID.
 * @return array<int,array<string,mixed>>
 */
function webmz_ticket_get_messages( $ticket_id ) {
	$messages = get_post_meta( absint( $ticket_id ), '_webmz_ticket_messages', true );
	$messages = is_array( $messages ) ? $messages : array();

	return array_values( array_filter( $messages, 'is_array' ) );
}

/**
 * Save ticket messages.
 *
 * @param int                         $ticket_id Ticket ID.
 * @param array<int,array<string,mixed>> $messages Messages.
 * @return void
 */
function webmz_ticket_save_messages( $ticket_id, $messages ) {
	update_post_meta( absint( $ticket_id ), '_webmz_ticket_messages', array_values( $messages ) );
}

/**
 * Append a message to ticket.
 *
 * @param int    $ticket_id Ticket ID.
 * @param int    $user_id User ID.
 * @param string $author_type user|staff.
 * @param string                        $content Message content.
 * @param array<int,array<string,mixed>> $attachments Optional attachments.
 * @return array<string,mixed>|false
 */
function webmz_ticket_add_message( $ticket_id, $user_id, $author_type, $content, $attachments = array() ) {
	$ticket_id = absint( $ticket_id );
	$user_id   = absint( $user_id );
	$content   = trim( wp_kses_post( $content ) );

	if ( ! $ticket_id || '' === $content ) {
		return false;
	}

	$user = $user_id ? get_userdata( $user_id ) : false;

	$message = array(
		'id'          => wp_generate_uuid4(),
		'user_id'     => $user_id,
		'author_type' => 'staff' === $author_type ? 'staff' : 'user',
		'author_name' => $user ? $user->display_name : esc_html__( 'کاربر', 'tadris' ),
		'content'     => $content,
		'created_at'  => current_time( 'mysql' ),
	);

	if ( ! empty( $attachments ) && is_array( $attachments ) ) {
		$message['attachments'] = array_values( $attachments );
	}

	$messages   = webmz_ticket_get_messages( $ticket_id );
	$messages[] = $message;
	webmz_ticket_save_messages( $ticket_id, $messages );

	update_post_meta( $ticket_id, '_webmz_ticket_last_activity', current_time( 'mysql' ) );

	return $message;
}

/**
 * Get current user's ticket unread count.
 *
 * @param int $user_id User ID.
 * @return int
 */
function webmz_ticket_get_user_unread_count( $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();

	if ( ! $user_id ) {
		return 0;
	}

	$query = new WP_Query(
		array(
			'post_type'      => WEBMZ_TICKET_POST_TYPE,
			'post_status'    => 'publish',
			'author'         => $user_id,
			'fields'         => 'ids',
			'posts_per_page' => 100,
			'meta_query'     => array(
				array(
					'key'     => '_webmz_ticket_user_unread',
					'value'   => '1',
					'compare' => '=',
				),
			),
		)
	);

	return absint( $query->found_posts );
}

/**
 * Get admin unread ticket count.
 *
 * @return int
 */
function webmz_ticket_get_admin_unread_count() {
	$query = new WP_Query(
		array(
			'post_type'      => WEBMZ_TICKET_POST_TYPE,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => 1,
			'meta_query'     => array(
				array(
					'key'     => '_webmz_ticket_admin_unread',
					'value'   => '1',
					'compare' => '=',
				),
			),
		)
	);

	return absint( $query->found_posts );
}

/**
 * Build account endpoint URL.
 *
 * @param int $ticket_id Optional ticket ID.
 * @return string
 */
function webmz_ticket_get_account_url( $ticket_id = 0 ) {
	if ( function_exists( 'wc_get_account_endpoint_url' ) ) {
		$url = wc_get_account_endpoint_url( 'support-tickets' );
	} else {
		$url = home_url( '/my-account/support-tickets/' );
	}

	$ticket_id = absint( $ticket_id );
	if ( $ticket_id ) {
		$url = add_query_arg( 'ticket_id', $ticket_id, $url );
	}

	return $url;
}

/**
 * Check if current user may view a ticket.
 *
 * @param int $ticket_id Ticket ID.
 * @param int $user_id User ID.
 * @return bool
 */
function webmz_ticket_user_can_view( $ticket_id, $user_id = 0 ) {
	$ticket_id = absint( $ticket_id );
	$user_id   = $user_id ? absint( $user_id ) : get_current_user_id();

	if ( ! $ticket_id || WEBMZ_TICKET_POST_TYPE !== get_post_type( $ticket_id ) ) {
		return false;
	}

	if ( current_user_can( 'edit_post', $ticket_id ) ) {
		return true;
	}

	return $user_id && (int) get_post_field( 'post_author', $ticket_id ) === $user_id;
}

/**
 * Notify user by email when staff replies.
 *
 * @param int $ticket_id Ticket ID.
 * @return void
 */
function webmz_ticket_notify_user_answered( $ticket_id ) {
	$settings = webmz_ticket_get_settings();
	if ( 'yes' !== $settings['notify_user_email'] ) {
		return;
	}

	$ticket_id = absint( $ticket_id );
	$user_id   = (int) get_post_field( 'post_author', $ticket_id );
	$user      = $user_id ? get_userdata( $user_id ) : false;

	if ( ! $user || empty( $user->user_email ) ) {
		return;
	}

	$subject = sprintf( __( 'پاسخ تیکت شما: %s', 'tadris' ), get_the_title( $ticket_id ) );
	$message = sprintf(
		__( "سلام،\n\nبه تیکت شما پاسخ داده شد.\n\nعنوان: %1\$s\nمشاهده تیکت: %2\$s", 'tadris' ),
		get_the_title( $ticket_id ),
		webmz_ticket_get_account_url( $ticket_id )
	);

	wp_mail( $user->user_email, $subject, $message );
	do_action( 'webmz_ticket_user_answered_sms', $ticket_id );
}

/**
 * Notify admin when user creates/replies.
 *
 * @param int    $ticket_id Ticket ID.
 * @param string $event Event type.
 * @return void
 */
function webmz_ticket_notify_admin( $ticket_id, $event = 'new' ) {
	$settings = webmz_ticket_get_settings();
	if ( 'yes' !== $settings['notify_admin_email'] ) {
		return;
	}

	$admin_email = get_option( 'admin_email' );
	if ( ! is_email( $admin_email ) ) {
		return;
	}

	$subject = 'reply' === $event
		? sprintf( __( 'پاسخ جدید کاربر در تیکت: %s', 'tadris' ), get_the_title( $ticket_id ) )
		: sprintf( __( 'تیکت جدید: %s', 'tadris' ), get_the_title( $ticket_id ) );

	$message = sprintf(
		__( "یک تیکت پشتیبانی نیاز به بررسی دارد.\n\nعنوان: %1\$s\nلینک مدیریت: %2\$s", 'tadris' ),
		get_the_title( $ticket_id ),
		get_edit_post_link( $ticket_id, '' )
	);

	wp_mail( $admin_email, $subject, $message );
}

/**
 * Render one ticket message.
 *
 * @param array<string,mixed> $message Message data.
 * @return string
 */
function webmz_ticket_get_message_html( $message, $ticket_id = 0 ) {
	$type        = isset( $message['author_type'] ) && 'staff' === $message['author_type'] ? 'staff' : 'user';
	$name        = isset( $message['author_name'] ) ? sanitize_text_field( $message['author_name'] ) : '';
	$content     = isset( $message['content'] ) ? wp_kses_post( $message['content'] ) : '';
	$created_at  = isset( $message['created_at'] ) ? strtotime( (string) $message['created_at'] ) : 0;
	$date        = $created_at ? date_i18n( get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ), $created_at ) : '';
	$attachments = isset( $message['attachments'] ) && is_array( $message['attachments'] ) ? $message['attachments'] : array();
	$attachments_html = '';

	if ( ! empty( $attachments ) && function_exists( 'webmz_ticket_render_attachments_html' ) ) {
		$attachments_html = webmz_ticket_render_attachments_html( $attachments, $ticket_id );
	}

	ob_start();
	?>
	<div class="webmz-ticket-message webmz-ticket-message--<?php echo esc_attr( $type ); ?>">
		<div class="webmz-ticket-message__head">
			<strong><?php echo esc_html( $name ); ?></strong>
			<?php if ( 'staff' === $type ) : ?>
				<span><?php esc_html_e( 'پشتیبانی', 'tadris' ); ?></span>
			<?php endif; ?>
			<time><?php echo esc_html( $date ); ?></time>
		</div>
		<div class="webmz-ticket-message__body">
			<?php echo wp_kses_post( wpautop( $content ) ); ?>
			<?php
			if ( $attachments_html ) {
				echo $attachments_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
	</div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Render messages list.
 *
 * @param int $ticket_id Ticket ID.
 * @return void
 */
function webmz_ticket_render_messages( $ticket_id ) {
	$messages  = webmz_ticket_get_messages( $ticket_id );
	$ticket_id = absint( $ticket_id );

	echo '<div class="webmz-ticket-messages" data-webmz-ticket-messages>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	foreach ( $messages as $message ) {
		echo webmz_ticket_get_message_html( $message, $ticket_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Create ticket.
 *
 * @param array<string,mixed> $data Ticket data.
 * @param int                $user_id User ID.
 * @return int|WP_Error
 */
function webmz_ticket_create( $data, $user_id ) {
	$user_id     = absint( $user_id );
	$subject     = isset( $data['subject'] ) ? sanitize_text_field( $data['subject'] ) : '';
	$content     = isset( $data['message'] ) ? trim( wp_kses_post( $data['message'] ) ) : '';
	$department  = isset( $data['department'] ) ? sanitize_text_field( $data['department'] ) : '';
	$product_id  = isset( $data['product_id'] ) ? absint( $data['product_id'] ) : 0;
	$departments = webmz_ticket_get_departments();

	if ( ! webmz_ticket_is_enabled() ) {
		return new WP_Error( 'disabled', esc_html__( 'سیستم تیکت در حال حاضر غیرفعال است.', 'tadris' ) );
	}

	if ( ! $user_id ) {
		return new WP_Error( 'login_required', esc_html__( 'برای ثبت تیکت ابتدا وارد حساب کاربری شوید.', 'tadris' ) );
	}

	if ( webmz_ticket_strlen( $subject ) < 3 || webmz_ticket_strlen( $subject ) > 160 ) {
		return new WP_Error( 'invalid_subject', esc_html__( 'عنوان تیکت باید بین ۳ تا ۱۶۰ کاراکتر باشد.', 'tadris' ) );
	}

	if ( webmz_ticket_strlen( wp_strip_all_tags( $content ) ) < 5 ) {
		return new WP_Error( 'invalid_message', esc_html__( 'متن تیکت خیلی کوتاه است.', 'tadris' ) );
	}

	if ( ! in_array( $department, $departments, true ) ) {
		$department = $departments[0];
	}

	if ( ! webmz_ticket_user_can_create_for_product( $user_id, $product_id ) ) {
		return new WP_Error( 'product_required', esc_html__( 'برای ثبت تیکت باید یکی از محصولات خریداری‌شده خود را انتخاب کنید.', 'tadris' ) );
	}

	$ticket_id = wp_insert_post(
		array(
			'post_type'   => WEBMZ_TICKET_POST_TYPE,
			'post_status' => 'publish',
			'post_title'  => $subject,
			'post_author' => $user_id,
		),
		true
	);

	if ( is_wp_error( $ticket_id ) ) {
		return $ticket_id;
	}

	update_post_meta( $ticket_id, '_webmz_ticket_status', 'open' );
	update_post_meta( $ticket_id, '_webmz_ticket_department', $department );
	update_post_meta( $ticket_id, '_webmz_ticket_product_id', $product_id );
	update_post_meta( $ticket_id, '_webmz_ticket_user_unread', '0' );
	update_post_meta( $ticket_id, '_webmz_ticket_admin_unread', '1' );
	update_post_meta( $ticket_id, '_webmz_ticket_last_activity', current_time( 'mysql' ) );

	$attachments = array();
	if ( ! empty( $data['attachments'] ) && is_array( $data['attachments'] ) && function_exists( 'webmz_ticket_attachment_process_uploads' ) ) {
		$attachments = webmz_ticket_attachment_process_uploads( $ticket_id, $user_id, $data['attachments'] );
		if ( is_wp_error( $attachments ) ) {
			wp_delete_post( $ticket_id, true );
			return $attachments;
		}
	}

	webmz_ticket_add_message( $ticket_id, $user_id, 'user', $content, $attachments );
	webmz_ticket_notify_admin( $ticket_id, 'new' );

	return $ticket_id;
}

/**
 * Add WooCommerce account menu item.
 *
 * @param array<string,string> $items Menu items.
 * @return array<string,string>
 */
function webmz_ticket_account_menu_items( $items ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return $items;
	}

	$logout = isset( $items['customer-logout'] ) ? $items['customer-logout'] : null;
	if ( $logout ) {
		unset( $items['customer-logout'] );
	}

	$count = webmz_ticket_get_user_unread_count();
	$label = esc_html__( 'تیکت‌های پشتیبانی', 'tadris' );
	if ( $count > 0 ) {
		// WooCommerce escapes account menu labels with esc_html(), so HTML badges are shown as raw code.
		// Keep the label plain text and style the endpoint page/list itself separately.
		$label .= ' (' . number_format_i18n( $count ) . ')';
	}

	$items['support-tickets'] = $label;

	if ( $logout ) {
		$items['customer-logout'] = $logout;
	}

	return $items;
}
add_filter( 'woocommerce_account_menu_items', 'webmz_ticket_account_menu_items', 30 );

/**
 * Render ticket status badge.
 *
 * @param string $status Status.
 * @return string
 */
function webmz_ticket_status_badge( $status ) {
	$status = webmz_ticket_sanitize_status( $status );

	return '<span class="webmz-ticket-status webmz-ticket-status--' . esc_attr( $status ) . '">' . esc_html( webmz_ticket_get_status_label( $status ) ) . '</span>';
}

/**
 * Render account ticket list.
 *
 * @param int $user_id User ID.
 * @return void
 */
function webmz_ticket_render_account_list( $user_id ) {
	$query = new WP_Query(
		array(
			'post_type'      => WEBMZ_TICKET_POST_TYPE,
			'post_status'    => 'publish',
			'author'         => absint( $user_id ),
			'posts_per_page' => 50,
			'orderby'        => 'meta_value',
			'order'          => 'DESC',
			'meta_key'       => '_webmz_ticket_last_activity',
		)
	);

	?>
	<div class="webmz-ticket-list">
		<?php if ( $query->have_posts() ) : ?>
			<?php while ( $query->have_posts() ) : ?>
				<?php
				$query->the_post();
				$ticket_id  = get_the_ID();
				$status     = get_post_meta( $ticket_id, '_webmz_ticket_status', true );
				$department = get_post_meta( $ticket_id, '_webmz_ticket_department', true );
				$unread     = '1' === get_post_meta( $ticket_id, '_webmz_ticket_user_unread', true );
				?>
				<a class="webmz-ticket-list-item <?php echo $unread ? 'is-unread' : ''; ?>" href="<?php echo esc_url( webmz_ticket_get_account_url( $ticket_id ) ); ?>">
					<span class="webmz-ticket-list-item__title">
						<?php if ( $unread ) : ?><span class="webmz-ticket-dot" aria-hidden="true"></span><?php endif; ?>
						<?php the_title(); ?>
					</span>
					<span class="webmz-ticket-list-item__meta">
						<?php echo wp_kses_post( webmz_ticket_status_badge( $status ) ); ?>
						<span><?php echo esc_html( $department ); ?></span>
						<time><?php echo esc_html( get_the_modified_date() ); ?></time>
					</span>
				</a>
			<?php endwhile; ?>
		<?php else : ?>
			<p class="webmz-ticket-empty"><?php esc_html_e( 'هنوز تیکتی ثبت نکرده‌اید.', 'tadris' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
	wp_reset_postdata();
}

/**
 * Render new ticket form in account.
 *
 * @param int $user_id User ID.
 * @return void
 */
function webmz_ticket_render_account_form( $user_id ) {
	$settings   = webmz_ticket_get_settings();
	$products   = webmz_ticket_get_user_purchased_products( $user_id );
	$require    = 'yes' === $settings['require_purchase'];
	$departments = webmz_ticket_get_departments();

	if ( $require && empty( $products ) ) {
		?>
		<div class="webmz-ticket-notice webmz-ticket-notice--warning">
			<?php esc_html_e( 'برای ثبت تیکت پشتیبانی باید حداقل یک محصول یا دوره خریداری‌شده داشته باشید.', 'tadris' ); ?>
		</div>
		<?php
		return;
	}
	?>
	<form class="webmz-ticket-form" data-webmz-ticket-form="create">
		<h3><?php esc_html_e( 'ثبت تیکت جدید', 'tadris' ); ?></h3>
		<div class="webmz-ticket-form__message" data-webmz-ticket-form-message></div>
		<div class="webmz-ticket-form__grid">
			<label>
				<span><?php esc_html_e( 'موضوع تیکت', 'tadris' ); ?></span>
				<input type="text" name="subject" maxlength="160" required>
			</label>

			<label>
				<span><?php esc_html_e( 'دپارتمان', 'tadris' ); ?></span>
				<select name="department" required>
					<?php foreach ( $departments as $department ) : ?>
						<option value="<?php echo esc_attr( $department ); ?>"><?php echo esc_html( $department ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>

			<label>
				<span><?php esc_html_e( 'محصول/دوره مرتبط', 'tadris' ); ?></span>
				<select name="product_id" <?php echo $require ? 'required' : ''; ?>>
					<option value="0"><?php echo $require ? esc_html__( 'انتخاب محصول خریداری‌شده', 'tadris' ) : esc_html__( 'بدون محصول خاص', 'tadris' ); ?></option>
					<?php foreach ( $products as $product_id => $product_name ) : ?>
						<option value="<?php echo esc_attr( $product_id ); ?>"><?php echo esc_html( $product_name ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
		</div>

		<label>
			<span><?php esc_html_e( 'متن پیام', 'tadris' ); ?></span>
			<textarea name="message" rows="6" required></textarea>
		</label>

		<?php
		if ( function_exists( 'webmz_ticket_render_attachment_upload_field' ) ) {
			webmz_ticket_render_attachment_upload_field();
		}
		?>

		<button type="submit" class="button webmz-ticket-submit"><?php esc_html_e( 'ارسال تیکت', 'tadris' ); ?></button>
	</form>
	<?php
}

/**
 * Render single ticket in account.
 *
 * @param int $ticket_id Ticket ID.
 * @return void
 */
function webmz_ticket_render_account_single( $ticket_id ) {
	$ticket_id = absint( $ticket_id );

	if ( ! webmz_ticket_user_can_view( $ticket_id ) ) {
		echo '<div class="webmz-ticket-notice webmz-ticket-notice--error">' . esc_html__( 'تیکت موردنظر پیدا نشد یا شما به آن دسترسی ندارید.', 'tadris' ) . '</div>';
		return;
	}

	update_post_meta( $ticket_id, '_webmz_ticket_user_unread', '0' );

	$status     = get_post_meta( $ticket_id, '_webmz_ticket_status', true );
	$department = get_post_meta( $ticket_id, '_webmz_ticket_department', true );
	$product_id = absint( get_post_meta( $ticket_id, '_webmz_ticket_product_id', true ) );
	$product    = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
	?>
	<div class="webmz-ticket-single" data-webmz-ticket-single="<?php echo esc_attr( $ticket_id ); ?>">
		<a class="webmz-ticket-back" href="<?php echo esc_url( webmz_ticket_get_account_url() ); ?>">&larr; <?php esc_html_e( 'بازگشت به تیکت‌ها', 'tadris' ); ?></a>
		<header class="webmz-ticket-single__header">
			<div>
				<h3><?php echo esc_html( get_the_title( $ticket_id ) ); ?></h3>
				<div class="webmz-ticket-single__meta">
					<?php echo wp_kses_post( webmz_ticket_status_badge( $status ) ); ?>
					<span><?php echo esc_html( $department ); ?></span>
					<?php if ( $product ) : ?>
						<span><?php echo esc_html( $product->get_name() ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</header>

		<?php webmz_ticket_render_messages( $ticket_id ); ?>

		<?php if ( 'closed' === webmz_ticket_sanitize_status( $status ) ) : ?>
			<div class="webmz-ticket-notice webmz-ticket-notice--info"><?php esc_html_e( 'این تیکت بسته شده است.', 'tadris' ); ?></div>
		<?php else : ?>
			<form class="webmz-ticket-form webmz-ticket-reply-form" data-webmz-ticket-form="reply">
				<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $ticket_id ); ?>">
				<div class="webmz-ticket-form__message" data-webmz-ticket-form-message></div>
				<label>
					<span><?php esc_html_e( 'پاسخ شما', 'tadris' ); ?></span>
					<textarea name="message" rows="5" required></textarea>
				</label>
				<?php
				if ( function_exists( 'webmz_ticket_render_attachment_upload_field' ) ) {
					webmz_ticket_render_attachment_upload_field();
				}
				?>
				<button type="submit" class="button webmz-ticket-submit"><?php esc_html_e( 'ارسال پاسخ', 'tadris' ); ?></button>
			</form>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Account endpoint content.
 *
 * @return void
 */
function webmz_ticket_account_content() {
	if ( ! webmz_ticket_is_enabled() ) {
		echo '<p class="webmz-ticket-empty">' . esc_html__( 'سیستم تیکت در حال حاضر غیرفعال است.', 'tadris' ) . '</p>';
		return;
	}

	$user_id   = get_current_user_id();
	$ticket_id = isset( $_GET['ticket_id'] ) ? absint( $_GET['ticket_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<div class="webmz-ticket-account" data-webmz-ticket-account>
		<?php if ( $ticket_id ) : ?>
			<?php webmz_ticket_render_account_single( $ticket_id ); ?>
		<?php else : ?>
			<div class="webmz-ticket-account__head">
				<h2><?php esc_html_e( 'تیکت‌های پشتیبانی', 'tadris' ); ?></h2>
				<p><?php esc_html_e( 'از این بخش می‌توانید برای دوره‌ها و محصولات خریداری‌شده درخواست پشتیبانی ارسال کنید.', 'tadris' ); ?></p>
			</div>
			<?php webmz_ticket_render_account_form( $user_id ); ?>
			<?php webmz_ticket_render_account_list( $user_id ); ?>
		<?php endif; ?>
	</div>
	<?php
}
add_action( 'woocommerce_account_support-tickets_endpoint', 'webmz_ticket_account_content' );

/**
 * Shortcode fallback for non-WooCommerce pages.
 *
 * @return string
 */
function webmz_ticket_shortcode() {
	if ( ! is_user_logged_in() ) {
		return '<div class="webmz-ticket-notice webmz-ticket-notice--warning">' . esc_html__( 'برای مشاهده تیکت‌ها ابتدا وارد حساب کاربری شوید.', 'tadris' ) . '</div>';
	}

	ob_start();
	webmz_ticket_account_content();

	return (string) ob_get_clean();
}
add_shortcode( 'webmz_support_tickets', 'webmz_ticket_shortcode' );

/**
 * AJAX create ticket.
 *
 * @return void
 */
function webmz_ticket_ajax_create() {
	check_ajax_referer( 'webmz_support_tickets', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( 'ابتدا وارد حساب کاربری شوید.', 'tadris' ) ), 401 );
	}

	$user_id = get_current_user_id();
	$key     = 'webmz_ticket_create_' . $user_id;
	if ( get_transient( $key ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'لطفاً چند لحظه بعد دوباره تلاش کنید.', 'tadris' ) ), 429 );
	}
	set_transient( $key, '1', 30 );

	$data = array(
		'subject'    => isset( $_POST['subject'] ) ? wp_unslash( $_POST['subject'] ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		'department' => isset( $_POST['department'] ) ? wp_unslash( $_POST['department'] ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		'product_id' => isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0,
		'message'    => isset( $_POST['message'] ) ? wp_unslash( $_POST['message'] ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	);

	if ( isset( $_FILES['attachments'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$data['attachments'] = $_FILES['attachments']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	$ticket_id = webmz_ticket_create( $data, $user_id );

	if ( is_wp_error( $ticket_id ) ) {
		wp_send_json_error( array( 'message' => $ticket_id->get_error_message() ), 400 );
	}

	wp_send_json_success(
		array(
			'message'    => esc_html__( 'تیکت شما با موفقیت ثبت شد.', 'tadris' ),
			'ticket_id'  => $ticket_id,
			'ticket_url' => webmz_ticket_get_account_url( $ticket_id ),
		)
	);
}
add_action( 'wp_ajax_webmz_ticket_create', 'webmz_ticket_ajax_create' );

/**
 * AJAX user reply.
 *
 * @return void
 */
function webmz_ticket_ajax_reply() {
	check_ajax_referer( 'webmz_support_tickets', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( 'ابتدا وارد حساب کاربری شوید.', 'tadris' ) ), 401 );
	}

	$user_id   = get_current_user_id();
	$ticket_id = isset( $_POST['ticket_id'] ) ? absint( $_POST['ticket_id'] ) : 0;
	$message   = isset( $_POST['message'] ) ? wp_unslash( $_POST['message'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	if ( ! webmz_ticket_user_can_view( $ticket_id, $user_id ) || (int) get_post_field( 'post_author', $ticket_id ) !== $user_id ) {
		wp_send_json_error( array( 'message' => esc_html__( 'شما به این تیکت دسترسی ندارید.', 'tadris' ) ), 403 );
	}

	if ( 'closed' === webmz_ticket_sanitize_status( get_post_meta( $ticket_id, '_webmz_ticket_status', true ) ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'این تیکت بسته شده است.', 'tadris' ) ), 400 );
	}

	$key = 'webmz_ticket_reply_' . $user_id . '_' . $ticket_id;
	if ( get_transient( $key ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'لطفاً چند لحظه بعد دوباره تلاش کنید.', 'tadris' ) ), 429 );
	}
	set_transient( $key, '1', 10 );

	$attachments = array();
	if ( isset( $_FILES['attachments'] ) && function_exists( 'webmz_ticket_attachment_process_uploads' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$attachments = webmz_ticket_attachment_process_uploads( $ticket_id, $user_id, $_FILES['attachments'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( is_wp_error( $attachments ) ) {
			wp_send_json_error( array( 'message' => $attachments->get_error_message() ), 400 );
		}
	}

	$new_message = webmz_ticket_add_message( $ticket_id, $user_id, 'user', $message, $attachments );
	if ( ! $new_message ) {
		wp_send_json_error( array( 'message' => esc_html__( 'متن پاسخ معتبر نیست.', 'tadris' ) ), 400 );
	}

	update_post_meta( $ticket_id, '_webmz_ticket_status', 'user' );
	update_post_meta( $ticket_id, '_webmz_ticket_admin_unread', '1' );
	update_post_meta( $ticket_id, '_webmz_ticket_user_unread', '0' );
	wp_update_post( array( 'ID' => $ticket_id ) );
	webmz_ticket_notify_admin( $ticket_id, 'reply' );

	wp_send_json_success(
		array(
			'message' => esc_html__( 'پاسخ شما ارسال شد.', 'tadris' ),
			'html'    => webmz_ticket_get_message_html( $new_message, $ticket_id ),
		)
	);
}
add_action( 'wp_ajax_webmz_ticket_reply', 'webmz_ticket_ajax_reply' );

/**
 * Add metaboxes.
 *
 * @return void
 */
function webmz_ticket_add_metaboxes() {
	add_meta_box(
		'webmz_ticket_details',
		esc_html__( 'جزئیات تیکت', 'tadris' ),
		'webmz_ticket_render_admin_details_metabox',
		WEBMZ_TICKET_POST_TYPE,
		'side',
		'high'
	);

	add_meta_box(
		'webmz_ticket_conversation',
		esc_html__( 'گفتگوی تیکت', 'tadris' ),
		'webmz_ticket_render_admin_conversation_metabox',
		WEBMZ_TICKET_POST_TYPE,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'webmz_ticket_add_metaboxes' );

/**
 * Render admin details metabox.
 *
 * @param WP_Post $post Post object.
 * @return void
 */
function webmz_ticket_render_admin_details_metabox( $post ) {
	wp_nonce_field( 'webmz_ticket_save_admin_meta', 'webmz_ticket_admin_meta_nonce' );

	$status     = webmz_ticket_sanitize_status( get_post_meta( $post->ID, '_webmz_ticket_status', true ) );
	$department = get_post_meta( $post->ID, '_webmz_ticket_department', true );
	$product_id = absint( get_post_meta( $post->ID, '_webmz_ticket_product_id', true ) );
	$user       = get_userdata( (int) $post->post_author );
	$product    = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
	?>
	<p><strong><?php esc_html_e( 'کاربر:', 'tadris' ); ?></strong><br><?php echo $user ? esc_html( $user->display_name . ' - ' . $user->user_email ) : esc_html__( 'نامشخص', 'tadris' ); ?></p>
	<p>
		<label for="webmz_ticket_status"><strong><?php esc_html_e( 'وضعیت', 'tadris' ); ?></strong></label><br>
		<select id="webmz_ticket_status" name="webmz_ticket_status" class="widefat">
			<?php foreach ( webmz_ticket_status_labels() as $key => $label ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $status, $key ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="webmz_ticket_department"><strong><?php esc_html_e( 'دپارتمان', 'tadris' ); ?></strong></label><br>
		<select id="webmz_ticket_department" name="webmz_ticket_department" class="widefat">
			<?php foreach ( webmz_ticket_get_departments() as $dep ) : ?>
				<option value="<?php echo esc_attr( $dep ); ?>" <?php selected( $department, $dep ); ?>><?php echo esc_html( $dep ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p><strong><?php esc_html_e( 'محصول مرتبط:', 'tadris' ); ?></strong><br><?php echo $product ? esc_html( $product->get_name() ) : esc_html__( 'ندارد', 'tadris' ); ?></p>
	<?php
}

/**
 * Render admin conversation metabox.
 *
 * @param WP_Post $post Post object.
 * @return void
 */
function webmz_ticket_render_admin_conversation_metabox( $post ) {
	update_post_meta( $post->ID, '_webmz_ticket_admin_unread', '0' );
	?>
	<div class="webmz-ticket-admin-box" data-webmz-admin-ticket="<?php echo esc_attr( $post->ID ); ?>">
		<?php webmz_ticket_render_messages( $post->ID ); ?>
		<div class="webmz-ticket-admin-reply" data-webmz-ticket-admin-reply-wrap="">
			<?php wp_nonce_field( 'webmz_support_tickets_admin', 'webmz_support_tickets_admin_nonce' ); ?>
			<textarea rows="5" class="widefat" data-webmz-ticket-admin-message placeholder="<?php esc_attr_e( 'پاسخ مدیر را بنویسید...', 'tadris' ); ?>"></textarea>
			<?php
			if ( function_exists( 'webmz_ticket_render_attachment_upload_field' ) ) {
				webmz_ticket_render_attachment_upload_field();
			}
			?>
			<div class="webmz-ticket-admin-reply__actions">
				<button type="button" class="button button-primary" data-webmz-ticket-admin-reply><?php esc_html_e( 'ارسال پاسخ', 'tadris' ); ?></button>
				<label>
					<input type="checkbox" data-webmz-ticket-close-after-reply value="1">
					<?php esc_html_e( 'بعد از ارسال پاسخ، تیکت بسته شود', 'tadris' ); ?>
				</label>
				<span class="webmz-ticket-admin-reply__message" data-webmz-ticket-admin-status></span>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Save admin ticket meta.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post Post object.
 * @return void
 */
function webmz_ticket_save_admin_meta( $post_id, $post ) {
	if ( WEBMZ_TICKET_POST_TYPE !== $post->post_type ) {
		return;
	}

	if ( ! isset( $_POST['webmz_ticket_admin_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_ticket_admin_meta_nonce'] ) ), 'webmz_ticket_save_admin_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$status     = isset( $_POST['webmz_ticket_status'] ) ? webmz_ticket_sanitize_status( wp_unslash( $_POST['webmz_ticket_status'] ) ) : 'open'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$department = isset( $_POST['webmz_ticket_department'] ) ? sanitize_text_field( wp_unslash( $_POST['webmz_ticket_department'] ) ) : '';
	if ( ! in_array( $department, webmz_ticket_get_departments(), true ) ) {
		$department = webmz_ticket_get_departments()[0];
	}

	update_post_meta( $post_id, '_webmz_ticket_status', $status );
	update_post_meta( $post_id, '_webmz_ticket_department', $department );
}
add_action( 'save_post', 'webmz_ticket_save_admin_meta', 10, 2 );

/**
 * AJAX admin reply.
 *
 * @return void
 */
function webmz_ticket_ajax_admin_reply() {
	check_ajax_referer( 'webmz_support_tickets_admin', 'nonce' );

	$ticket_id = isset( $_POST['ticket_id'] ) ? absint( $_POST['ticket_id'] ) : 0;
	$message   = isset( $_POST['message'] ) ? wp_unslash( $_POST['message'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$close     = isset( $_POST['close_ticket'] ) && '1' === (string) $_POST['close_ticket'];

	if ( ! $ticket_id || WEBMZ_TICKET_POST_TYPE !== get_post_type( $ticket_id ) || ! current_user_can( 'edit_post', $ticket_id ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'دسترسی به تیکت مجاز نیست.', 'tadris' ) ), 403 );
	}

	$user_id     = get_current_user_id();
	$attachments = array();
	if ( isset( $_FILES['attachments'] ) && function_exists( 'webmz_ticket_attachment_process_uploads' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$attachments = webmz_ticket_attachment_process_uploads( $ticket_id, $user_id, $_FILES['attachments'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( is_wp_error( $attachments ) ) {
			wp_send_json_error( array( 'message' => $attachments->get_error_message() ), 400 );
		}
	}

	$new_message = webmz_ticket_add_message( $ticket_id, $user_id, 'staff', $message, $attachments );
	if ( ! $new_message ) {
		wp_send_json_error( array( 'message' => esc_html__( 'متن پاسخ معتبر نیست.', 'tadris' ) ), 400 );
	}

	update_post_meta( $ticket_id, '_webmz_ticket_status', $close ? 'closed' : 'answered' );
	update_post_meta( $ticket_id, '_webmz_ticket_user_unread', '1' );
	update_post_meta( $ticket_id, '_webmz_ticket_admin_unread', '0' );
	wp_update_post( array( 'ID' => $ticket_id ) );
	webmz_ticket_notify_user_answered( $ticket_id );

	wp_send_json_success(
		array(
			'message' => esc_html__( 'پاسخ ارسال شد.', 'tadris' ),
			'html'    => webmz_ticket_get_message_html( $new_message, $ticket_id ),
			'status'  => $close ? 'closed' : 'answered',
		)
	);
}
add_action( 'wp_ajax_webmz_ticket_admin_reply', 'webmz_ticket_ajax_admin_reply' );

/**
 * Add admin columns.
 *
 * @param array<string,string> $columns Columns.
 * @return array<string,string>
 */
function webmz_ticket_admin_columns( $columns ) {
	return array(
		'cb'         => isset( $columns['cb'] ) ? $columns['cb'] : '',
		'title'      => esc_html__( 'موضوع', 'tadris' ),
		'user'       => esc_html__( 'کاربر', 'tadris' ),
		'department' => esc_html__( 'دپارتمان', 'tadris' ),
		'product'    => esc_html__( 'محصول', 'tadris' ),
		'sstatus'    => esc_html__( 'وضعیت', 'tadris' ),
		'unread'     => esc_html__( 'اعلان', 'tadris' ),
		'date'       => esc_html__( 'تاریخ', 'tadris' ),
	);
}
add_filter( 'manage_' . WEBMZ_TICKET_POST_TYPE . '_posts_columns', 'webmz_ticket_admin_columns' );

/**
 * Render admin columns.
 *
 * @param string $column Column name.
 * @param int    $post_id Post ID.
 * @return void
 */
function webmz_ticket_admin_column_content( $column, $post_id ) {
	if ( 'user' === $column ) {
		$user = get_userdata( (int) get_post_field( 'post_author', $post_id ) );
		echo $user ? esc_html( $user->display_name ) : '—';
	} elseif ( 'department' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_webmz_ticket_department', true ) );
	} elseif ( 'product' === $column ) {
		$product_id = absint( get_post_meta( $post_id, '_webmz_ticket_product_id', true ) );
		$product    = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
		echo $product ? esc_html( $product->get_name() ) : '—';
	} elseif ( 'sstatus' === $column ) {
		echo wp_kses_post( webmz_ticket_status_badge( get_post_meta( $post_id, '_webmz_ticket_status', true ) ) );
	} elseif ( 'unread' === $column ) {
		$unread = '1' === get_post_meta( $post_id, '_webmz_ticket_admin_unread', true );
		echo $unread ? '<span class="webmz-admin-ticket-unread">' . esc_html__( 'نیاز به پاسخ', 'tadris' ) . '</span>' : '—'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'manage_' . WEBMZ_TICKET_POST_TYPE . '_posts_custom_column', 'webmz_ticket_admin_column_content', 10, 2 );

/**
 * Show unread count in admin menu.
 *
 * @return void
 */
function webmz_ticket_admin_menu_badge() {
	global $submenu;

	if ( empty( $submenu['webmz-options'] ) ) {
		return;
	}

	$count = webmz_ticket_get_admin_unread_count();
	if ( ! $count ) {
		return;
	}

	foreach ( $submenu['webmz-options'] as &$item ) {
		if ( isset( $item[2] ) && false !== strpos( $item[2], 'edit.php?post_type=' . WEBMZ_TICKET_POST_TYPE ) ) {
			$item[0] .= ' <span class="update-plugins count-' . absint( $count ) . '"><span class="plugin-count">' . number_format_i18n( $count ) . '</span></span>';
			break;
		}
	}
}
add_action( 'admin_menu', 'webmz_ticket_admin_menu_badge', 99 );

/**
 * Enqueue frontend ticket assets.
 *
 * @return void
 */
function webmz_ticket_enqueue_frontend_assets() {
	$should_enqueue = false;

	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		$should_enqueue = true;
	}

	if ( is_singular() ) {
		$post = get_post();
		if ( $post && has_shortcode( (string) $post->post_content, 'webmz_support_tickets' ) ) {
			$should_enqueue = true;
		}
	}

	if ( ! $should_enqueue ) {
		return;
	}

	wp_enqueue_style( 'webmz-support-tickets', WEBMZ_URI . 'assets/css/support-tickets.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_enqueue_script( 'webmz-support-tickets', WEBMZ_URI . 'assets/js/support-tickets.js', array( 'jquery' ), WEBMZ_VERSION, true );
	$localize = array(
		'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
		'nonce'        => wp_create_nonce( 'webmz_support_tickets' ),
		'sending'      => esc_html__( 'در حال ارسال...', 'tadris' ),
		'errorMessage' => esc_html__( 'خطایی رخ داد. دوباره تلاش کنید.', 'tadris' ),
	);

	if ( function_exists( 'webmz_ticket_attachment_get_frontend_config' ) ) {
		$localize['attachments'] = webmz_ticket_attachment_get_frontend_config();
	}

	wp_localize_script( 'webmz-support-tickets', 'webmzSupportTickets', $localize );
}
add_action( 'wp_enqueue_scripts', 'webmz_ticket_enqueue_frontend_assets' );

/**
 * Enqueue admin ticket assets.
 *
 * @param string $hook Current hook.
 * @return void
 */
function webmz_ticket_enqueue_admin_assets( $hook ) {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$is_ticket_screen = $screen && WEBMZ_TICKET_POST_TYPE === $screen->post_type;
	$is_settings      = in_array( $hook, array( 'tadris_page_tadris-ticket-settings', 'webmz_page_tadris-ticket-settings', 'toplevel_page_webmz-options' ), true );

	if ( ! $is_ticket_screen && ! $is_settings ) {
		return;
	}

	wp_enqueue_style( 'webmz-support-tickets-admin', WEBMZ_URI . 'assets/css/support-tickets.css', array(), WEBMZ_VERSION );
	wp_enqueue_script( 'webmz-support-tickets-admin', WEBMZ_URI . 'assets/js/support-tickets.js', array( 'jquery' ), WEBMZ_VERSION, true );
	$admin_localize = array(
		'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
		'nonce'        => wp_create_nonce( 'webmz_support_tickets_admin' ),
		'sending'      => esc_html__( 'در حال ارسال...', 'tadris' ),
		'errorMessage' => esc_html__( 'خطایی رخ داد. دوباره تلاش کنید.', 'tadris' ),
	);

	if ( function_exists( 'webmz_ticket_attachment_get_frontend_config' ) ) {
		$admin_localize['attachments'] = webmz_ticket_attachment_get_frontend_config();
	}

	wp_localize_script( 'webmz-support-tickets-admin', 'webmzSupportTicketsAdmin', $admin_localize );
}
add_action( 'admin_enqueue_scripts', 'webmz_ticket_enqueue_admin_assets' );
