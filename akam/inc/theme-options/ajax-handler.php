<?php
/**
 * Secure AJAX option storage for WebMZ.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WEBMZ_RTL_LICENSED' ) || true !== WEBMZ_RTL_LICENSED ) {
	return;
}

/**
 * Get the bundled child theme slug.
 *
 * @return string
 */
function webmz_get_child_theme_slug() {
	return 'akam-child';
}

/**
 * Get the bundled child theme source directory.
 *
 * @return string
 */
function webmz_get_child_theme_source_dir() {
	return trailingslashit( WEBMZ_DIR . 'child-theme/' . webmz_get_child_theme_slug() );
}

/**
 * Build the current child theme install status for the options panel.
 *
 * @return array<string,mixed>
 */
function webmz_get_child_theme_status() {
	$slug       = webmz_get_child_theme_slug();
	$theme      = wp_get_theme( $slug );
	$installed  = $theme->exists();
	$is_active  = $installed && get_stylesheet() === $slug;
	$button_text = esc_html__( 'نصب و فعال‌سازی پوسته فرزند', 'tadris' );
	$label       = esc_html__( 'پوسته فرزند هنوز نصب نشده است.', 'tadris' );
	$status      = 'not-installed';

	if ( $is_active ) {
		$status      = 'active';
		$label       = esc_html__( 'پوسته فرزند فعال است.', 'tadris' );
		$button_text = esc_html__( 'پوسته فرزند فعال است', 'tadris' );
	} elseif ( $installed ) {
		$status      = 'installed';
		$label       = esc_html__( 'پوسته فرزند نصب شده ولی فعال نیست.', 'tadris' );
		$button_text = esc_html__( 'فعال‌سازی پوسته فرزند', 'tadris' );
	}

	return array(
		'slug'        => $slug,
		'status'      => $status,
		'label'       => $label,
		'button_text' => $button_text,
		'is_active'   => $is_active,
		'installed'   => $installed,
	);
}

/**
 * Copy bundled child theme files recursively without overwriting existing themes.
 *
 * @param string $source Source directory.
 * @param string $destination Destination directory.
 * @param string $error Error message passed by reference.
 * @return bool
 */
function webmz_copy_child_theme_files( $source, $destination, &$error ) {
	if ( ! is_dir( $source ) ) {
		$error = esc_html__( 'فایل‌های پوسته فرزند داخل قالب پیدا نشد.', 'tadris' );
		return false;
	}

	if ( ! wp_mkdir_p( $destination ) ) {
		$error = esc_html__( 'امکان ساخت پوشه پوسته فرزند وجود ندارد.', 'tadris' );
		return false;
	}

	$items = scandir( $source );
	if ( false === $items ) {
		$error = esc_html__( 'امکان خواندن فایل‌های پوسته فرزند وجود ندارد.', 'tadris' );
		return false;
	}

	foreach ( $items as $item ) {
		if ( '.' === $item || '..' === $item ) {
			continue;
		}

		$source_path      = trailingslashit( $source ) . $item;
		$destination_path = trailingslashit( $destination ) . $item;

		if ( is_link( $source_path ) ) {
			continue;
		}

		if ( is_dir( $source_path ) ) {
			if ( ! webmz_copy_child_theme_files( $source_path, $destination_path, $error ) ) {
				return false;
			}
			continue;
		}

		if ( file_exists( $destination_path ) ) {
			continue;
		}

		if ( ! copy( $source_path, $destination_path ) ) {
			/* translators: %s: File name. */
			$error = sprintf( esc_html__( 'کپی فایل %s انجام نشد.', 'tadris' ), esc_html( $item ) );
			return false;
		}
	}

	return true;
}

/**
 * Install and activate the bundled child theme.
 *
 * @return void
 */
function webmz_ajax_install_child_theme() {
	ob_start();

	check_ajax_referer( 'webmz_child_theme', 'nonce' );

	if ( ! current_user_can( 'install_themes' ) || ! current_user_can( 'switch_themes' ) ) {
		while ( ob_get_level() ) {
			ob_end_clean();
		}
		wp_send_json_error( array( 'message' => esc_html__( 'شما اجازه نصب یا فعال‌سازی پوسته را ندارید.', 'tadris' ) ), 403 );
	}

	$slug   = webmz_get_child_theme_slug();
	$status = webmz_get_child_theme_status();

	if ( ! empty( $status['is_active'] ) ) {
		while ( ob_get_level() ) {
			ob_end_clean();
		}
		wp_send_json_success( array(
			'message' => esc_html__( 'پوسته فرزند از قبل فعال است.', 'tadris' ),
			'status'  => $status,
		) );
	}

	if ( empty( $status['installed'] ) ) {
		$source      = webmz_get_child_theme_source_dir();
		$destination = trailingslashit( get_theme_root() ) . $slug;
		$error       = '';

		if ( file_exists( $destination ) ) {
			while ( ob_get_level() ) {
				ob_end_clean();
			}
			wp_send_json_error( array( 'message' => esc_html__( 'پوشه akam-child از قبل وجود دارد اما به‌عنوان پوسته معتبر شناسایی نمی‌شود. لطفاً آن را بررسی یا تغییرنام دهید.', 'tadris' ) ), 409 );
		}

		if ( ! wp_is_writable( get_theme_root() ) ) {
			while ( ob_get_level() ) {
				ob_end_clean();
			}
			wp_send_json_error( array( 'message' => esc_html__( 'مسیر پوسته‌های وردپرس قابل نوشتن نیست.', 'tadris' ) ) );
		}

		if ( ! webmz_copy_child_theme_files( $source, $destination, $error ) ) {
			while ( ob_get_level() ) {
				ob_end_clean();
			}
			wp_send_json_error( array( 'message' => $error ? $error : esc_html__( 'کپی فایل‌های پوسته فرزند انجام نشد.', 'tadris' ) ) );
		}

		wp_clean_themes_cache( true );
		$status = webmz_get_child_theme_status();

		if ( empty( $status['installed'] ) ) {
			while ( ob_get_level() ) {
				ob_end_clean();
			}
			wp_send_json_error( array( 'message' => esc_html__( 'پوسته فرزند نصب شد اما توسط وردپرس معتبر شناسایی نشد.', 'tadris' ) ) );
		}
	}

	switch_theme( $slug );
	wp_clean_themes_cache( true );

	$status = webmz_get_child_theme_status();

	if ( empty( $status['is_active'] ) ) {
		while ( ob_get_level() ) {
			ob_end_clean();
		}
		wp_send_json_error( array( 'message' => esc_html__( 'پوسته فرزند نصب شد اما فعال‌سازی آن انجام نشد.', 'tadris' ) ) );
	}

	while ( ob_get_level() ) {
		ob_end_clean();
	}

	wp_send_json_success( array(
		'message' => esc_html__( 'پوسته فرزند با موفقیت نصب و فعال شد.', 'tadris' ),
		'status'  => $status,
	) );
}
add_action( 'wp_ajax_webmz_install_child_theme', 'webmz_ajax_install_child_theme' );

/**
 * Sanitize settings posted by an administrator.
 *
 * @param array<string,mixed> $raw Raw input.
 * @return array<string,mixed>
 */
function webmz_sanitize_options( $raw ) {
	$defaults = webmz_get_default_options();
	$clean    = $defaults;
	$raw      = is_array( $raw ) ? $raw : array();
	$colors   = array( 'color_primary', 'color_secondary', 'color_primary_hover', 'color_primary_light', 'color_text_dark', 'color_text_gray', 'color_text_navy', 'color_background', 'account_accent_light', 'related_box_bg', 'related_box_accent', 'related_box_btn_bg', 'related_box_btn_hover', 'related_box_title_color', 'related_box_desc_color' );
	foreach ( $colors as $key ) {
		if ( isset( $raw[ $key ] ) ) {
			$value = sanitize_hex_color( $raw[ $key ] );
			if ( in_array( $key, array( 'related_box_btn_bg', 'related_box_btn_hover', 'related_box_title_color', 'related_box_desc_color' ), true ) ) {
				$clean[ $key ] = $value ? $value : '';
			} else {
				$clean[ $key ] = $value ? $value : $defaults[ $key ];
			}
		}
	}

	$width = isset( $raw['container_width'] ) ? absint( $raw['container_width'] ) : absint( $defaults['container_width'] );
	$clean['container_width'] = min( 1920, max( 720, $width ) );

	$fonts = webmz_get_local_fonts();
	$font  = isset( $raw['font_family'] ) ? sanitize_key( $raw['font_family'] ) : 'system';
	$clean['font_family']   = isset( $fonts[ $font ] ) ? $font : 'system';

	$admin_font = isset( $raw['admin_font_family'] ) ? sanitize_key( $raw['admin_font_family'] ) : 'system';
	$clean['admin_font_family'] = isset( $fonts[ $admin_font ] ) ? $admin_font : 'system';

	$clean['local_gravatar_enable'] = isset( $raw['local_gravatar_enable'] ) && 'yes' === $raw['local_gravatar_enable'] ? 'yes' : 'no';
	$clean['local_gravatar_url']    = isset( $raw['local_gravatar_url'] ) ? esc_url_raw( $raw['local_gravatar_url'] ) : '';
	$clean['related_use_theme_colors'] = isset( $raw['related_use_theme_colors'] ) && 'yes' === $raw['related_use_theme_colors'] ? 'yes' : 'no';
	$clean['hover_effects']            = isset( $raw['hover_effects'] ) && in_array( $raw['hover_effects'], array( 'off', 'subtle', 'vivid' ), true ) ? $raw['hover_effects'] : 'subtle';
	$clean['floating_contact_enabled'] = isset( $raw['floating_contact_enabled'] ) && 'yes' === $raw['floating_contact_enabled'] ? 'yes' : 'no';
	$clean['sticky_add_to_cart_enabled'] = isset( $raw['sticky_add_to_cart_enabled'] ) && 'yes' === $raw['sticky_add_to_cart_enabled'] ? 'yes' : 'no';
	$clean['floating_contact_position'] = isset( $raw['floating_contact_position'] ) && 'right' === sanitize_key( $raw['floating_contact_position'] ) ? 'right' : 'left';

	if ( isset( $raw['floating_contact_button_color'] ) ) {
		$button_color = sanitize_hex_color( $raw['floating_contact_button_color'] );
		$clean['floating_contact_button_color'] = $button_color ? $button_color : $defaults['floating_contact_button_color'];
	}

	if ( isset( $raw['floating_contact_button_hover_color'] ) ) {
		$button_hover_color = sanitize_hex_color( $raw['floating_contact_button_hover_color'] );
		$clean['floating_contact_button_hover_color'] = $button_hover_color ? $button_hover_color : $defaults['floating_contact_button_hover_color'];
	}

	$offset_x = isset( $raw['floating_contact_offset_x'] ) ? absint( $raw['floating_contact_offset_x'] ) : absint( $defaults['floating_contact_offset_x'] );
	$clean['floating_contact_offset_x'] = min( 200, max( 0, $offset_x ) );

	$offset_bottom = isset( $raw['floating_contact_offset_bottom'] ) ? absint( $raw['floating_contact_offset_bottom'] ) : absint( $defaults['floating_contact_offset_bottom'] );
	$clean['floating_contact_offset_bottom'] = min( 240, max( 0, $offset_bottom ) );

	$clean['floating_contact_button_icon_id'] = isset( $raw['floating_contact_button_icon_id'] ) ? absint( $raw['floating_contact_button_icon_id'] ) : 0;
	if ( $clean['floating_contact_button_icon_id'] && 'attachment' !== get_post_type( $clean['floating_contact_button_icon_id'] ) ) {
		$clean['floating_contact_button_icon_id'] = 0;
	}

	$clean['floating_contact_panel_title'] = isset( $raw['floating_contact_panel_title'] ) ? sanitize_text_field( $raw['floating_contact_panel_title'] ) : $defaults['floating_contact_panel_title'];
	$clean['floating_contact_panel_desc']  = isset( $raw['floating_contact_panel_desc'] ) ? sanitize_text_field( $raw['floating_contact_panel_desc'] ) : $defaults['floating_contact_panel_desc'];

	$clean['floating_contact_items'] = array();
	if ( isset( $raw['floating_contact_items'] ) && is_array( $raw['floating_contact_items'] ) ) {
		foreach ( $raw['floating_contact_items'] as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$title = isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : '';
			$url   = isset( $item['url'] ) ? esc_url_raw( $item['url'] ) : '';

			if ( '' === $title ) {
				continue;
			}

			$color = isset( $item['color'] ) ? sanitize_hex_color( $item['color'] ) : '';
			$icon_id = isset( $item['icon_id'] ) ? absint( $item['icon_id'] ) : 0;
			if ( $icon_id && 'attachment' !== get_post_type( $icon_id ) ) {
				$icon_id = 0;
			}

			$clean['floating_contact_items'][] = array(
				'enabled' => isset( $item['enabled'] ) && 'yes' === $item['enabled'] ? 'yes' : 'no',
				'title'   => function_exists( 'mb_substr' ) ? mb_substr( $title, 0, 80 ) : substr( $title, 0, 160 ),
				'url'     => $url,
				'color'   => $color ? $color : '#2563eb',
				'icon'    => isset( $item['icon'] ) ? sanitize_key( $item['icon'] ) : 'chat',
				'icon_id' => $icon_id,
				'order'   => isset( $item['order'] ) ? max( 0, min( 9999, absint( $item['order'] ) ) ) : count( $clean['floating_contact_items'] ) + 1,
			);
		}
	}

	if ( empty( $clean['floating_contact_items'] ) ) {
		$clean['floating_contact_items'] = webmz_get_default_floating_contact_items();
	}

	$clean['admin_logo_id'] = isset( $raw['admin_logo_id'] ) ? absint( $raw['admin_logo_id'] ) : 0;
	if ( $clean['admin_logo_id'] && 'attachment' !== get_post_type( $clean['admin_logo_id'] ) ) {
		$clean['admin_logo_id'] = 0;
	}

	$clean['favicon_id'] = isset( $raw['favicon_id'] ) ? absint( $raw['favicon_id'] ) : 0;
	if ( $clean['favicon_id'] && 'attachment' !== get_post_type( $clean['favicon_id'] ) ) {
		$clean['favicon_id'] = 0;
	}

	$raw_terms = isset( $raw['popular_searches'] )
		? sanitize_textarea_field( $raw['popular_searches'] )
		: '';

	$terms = preg_split( '/[\r\n,،]+/u', $raw_terms );
	$terms = array_filter(
		array_map(
			'sanitize_text_field',
			array_map( 'trim', (array) $terms )
		)
	);

	$terms = array_slice( array_values( array_unique( $terms ) ), 0, 20 );
	$clean['popular_searches'] = implode( "\n", $terms );

	$clean['account_ajax_enabled'] = isset( $raw['account_ajax_enabled'] ) && 'yes' === $raw['account_ajax_enabled'] ? 'yes' : 'no';

	$allowed_checkout_fields = function_exists( 'webmz_checkout_get_default_visible_fields' ) ? webmz_checkout_get_default_visible_fields() : array();
	$clean['checkout_visible_fields'] = array();
	if ( isset( $raw['checkout_visible_fields'] ) && is_array( $raw['checkout_visible_fields'] ) ) {
		foreach ( $raw['checkout_visible_fields'] as $field_key ) {
			$field_key = sanitize_key( $field_key );
			if ( in_array( $field_key, $allowed_checkout_fields, true ) ) {
				$clean['checkout_visible_fields'][] = $field_key;
			}
		}
	}
	$clean['checkout_visible_fields'] = array_values( array_unique( $clean['checkout_visible_fields'] ) );

	$clean['account_visible_endpoints'] = array();
	if ( isset( $raw['account_visible_endpoints'] ) && is_array( $raw['account_visible_endpoints'] ) ) {
		foreach ( $raw['account_visible_endpoints'] as $endpoint ) {
			$endpoint = sanitize_key( $endpoint );
			if ( '' !== $endpoint ) {
				$clean['account_visible_endpoints'][] = $endpoint;
			}
		}
		$clean['account_visible_endpoints'] = array_values( array_unique( $clean['account_visible_endpoints'] ) );
	}

	$clean['account_endpoint_titles'] = array();
	if ( isset( $raw['account_endpoint_titles'] ) && is_array( $raw['account_endpoint_titles'] ) ) {
		foreach ( $raw['account_endpoint_titles'] as $endpoint => $title ) {
			$endpoint = sanitize_key( $endpoint );
			$title    = sanitize_text_field( $title );

			if ( '' === $endpoint || '' === $title ) {
				continue;
			}

			$clean['account_endpoint_titles'][ $endpoint ] = function_exists( 'mb_substr' ) ? mb_substr( $title, 0, 80 ) : substr( $title, 0, 160 );
		}
	}

	$clean['account_endpoint_order'] = array();
	if ( isset( $raw['account_endpoint_order'] ) && is_array( $raw['account_endpoint_order'] ) ) {
		foreach ( $raw['account_endpoint_order'] as $endpoint => $position ) {
			$endpoint = sanitize_key( $endpoint );

			if ( '' === $endpoint ) {
				continue;
			}

			$clean['account_endpoint_order'][ $endpoint ] = max( 0, min( 9999, absint( $position ) ) );
		}
	}

	$clean['account_endpoint_icons'] = array();
	if ( isset( $raw['account_endpoint_icons'] ) && is_array( $raw['account_endpoint_icons'] ) ) {
		foreach ( $raw['account_endpoint_icons'] as $endpoint => $attachment_id ) {
			$endpoint = sanitize_key( $endpoint );
			$attachment_id = absint( $attachment_id );

			if ( '' === $endpoint || ! $attachment_id || 'attachment' !== get_post_type( $attachment_id ) ) {
				continue;
			}

			$clean['account_endpoint_icons'][ $endpoint ] = $attachment_id;
		}
	}

	$clean['account_custom_endpoints'] = array();
	if ( function_exists( 'webmz_account_sanitize_custom_endpoints' ) && isset( $raw['account_custom_endpoints'] ) ) {
		$clean['account_custom_endpoints'] = webmz_account_sanitize_custom_endpoints( $raw['account_custom_endpoints'] );
	}

	foreach ( webmz_get_layout_types() as $type => $label ) {
		$key = 'layout_' . $type;
		$id  = isset( $raw[ $key ] ) ? absint( $raw[ $key ] ) : 0;
		if ( $id && ( 'webmz_layout' !== get_post_type( $id ) || 'publish' !== get_post_status( $id ) ) ) {
			$id = 0;
		}
		$clean[ $key ] = $id;
	}
	return $clean;
}

/**
 * Save settings using a nonce and administrator capability check.
 *
 * @return void
 */
function webmz_ajax_save_options() {
	check_ajax_referer( 'webmz_save_options', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'شما اجازه انجام این عملیات را ندارید.', 'tadris' ) ), 403 );
	}
	$input = isset( $_POST['options'] ) ? wp_unslash( $_POST['options'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$clean = webmz_sanitize_options( $input );
	$old_endpoint_hash = get_option( 'webmz_account_endpoint_hash' );
	update_option( 'webmz_options', $clean );
	if ( function_exists( 'webmz_account_get_custom_endpoints' ) ) {
		$custom_slugs = array();
		foreach ( $clean['account_custom_endpoints'] as $endpoint ) {
			if ( isset( $endpoint['enabled'], $endpoint['slug'] ) && 'yes' === $endpoint['enabled'] ) {
				$custom_slugs[] = $endpoint['slug'];
			}
		}
		sort( $custom_slugs );
		$new_endpoint_hash = md5( wp_json_encode( $custom_slugs ) );
		if ( $old_endpoint_hash !== $new_endpoint_hash ) {
			flush_rewrite_rules( false );
			update_option( 'webmz_account_endpoint_hash', $new_endpoint_hash );
		}
	}

	$otp_clean = array();
	if ( function_exists( 'webmz_otp_sanitize_settings' ) && isset( $_POST['otp'] ) ) {
		$otp_input = wp_unslash( $_POST['otp'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$otp_clean = webmz_otp_sanitize_settings( $otp_input );
		update_option( 'webmz_otp_settings', $otp_clean );
	}

	$ticket_clean = array();
	if ( function_exists( 'webmz_ticket_sanitize_settings' ) && isset( $_POST['ticket'] ) ) {
		$ticket_input = wp_unslash( $_POST['ticket'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$ticket_clean = webmz_ticket_sanitize_settings( $ticket_input );
		update_option( 'webmz_ticket_settings', $ticket_clean );
	}

	// Stored separately and never echoed back, so the API key does not travel to the browser.
	if ( function_exists( 'webmz_ai_chat_sanitize_settings' ) && isset( $_POST['ai_chat'] ) ) {
		$ai_chat_input = wp_unslash( $_POST['ai_chat'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		update_option( 'webmz_ai_chat_settings', webmz_ai_chat_sanitize_settings( $ai_chat_input ), false );
	}

	wp_send_json_success( array(
		'message' => esc_html__( 'تنظیمات با موفقیت ذخیره شد.', 'tadris' ),
		'options' => $clean,
		'ticket'  => $ticket_clean,
		'otp'     => $otp_clean,
	) );
}
add_action( 'wp_ajax_webmz_save_options', 'webmz_ajax_save_options' );
