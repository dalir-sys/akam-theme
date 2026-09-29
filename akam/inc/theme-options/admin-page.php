<?php
/**
 * WebMZ AJAX theme options screen.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WEBMZ_RTL_LICENSED' ) || true !== WEBMZ_RTL_LICENSED ) {
	return;
}

/**
 * Add theme settings page.
 *
 * @return void
 */
function webmz_register_options_page() {
	add_menu_page(
		esc_html__( 'تنظیمات قالب آکام', 'webmz' ),
		esc_html__( 'آکام', 'webmz' ),
		'manage_options',
		'webmz-options',
		'webmz_render_options_page',
		'dashicons-admin-customizer',
		59
	);

	/*
	 * Keep the original theme options panel visible as the first submenu.
	 * Some nested post types, such as support tickets, add their own submenu under
	 * the WebMZ parent. Registering this submenu explicitly prevents the theme
	 * settings screen from disappearing from the WordPress admin menu.
	 */
	add_submenu_page(
		'webmz-options',
		esc_html__( 'تنظیمات پوسته', 'webmz' ),
		esc_html__( 'تنظیمات پوسته', 'webmz' ),
		'manage_options',
		'webmz-options',
		'webmz_render_options_page',
		0
	);
}
add_action( 'admin_menu', 'webmz_register_options_page', 5 );

/**
 * Add Tadris settings link to the WordPress admin bar.
 *
 * @param \WP_Admin_Bar $wp_admin_bar Admin bar instance.
 * @return void
 */
function webmz_admin_bar_theme_settings( $wp_admin_bar ) {
	if ( ! $wp_admin_bar instanceof WP_Admin_Bar || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$wp_admin_bar->add_node(
		array(
			'id'    => 'webmz-tadris-settings',
			'title' => esc_html__( 'تنظیمات آکام', 'webmz' ),
			'href'  => admin_url( 'admin.php?page=webmz-options' ),
			'meta'  => array(
				'title' => esc_html__( 'تنظیمات قالب آکام', 'webmz' ),
			),
		)
	);
}
add_action( 'admin_bar_menu', 'webmz_admin_bar_theme_settings', 80 );

/**
 * Add body class on theme options screen.
 *
 * @param string $classes Admin body classes.
 * @return string
 */
function webmz_admin_options_body_class( $classes ) {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( $screen && 'toplevel_page_webmz-options' === $screen->id ) {
		$classes .= ' webmz-options-screen';
	}

	return $classes;
}
add_filter( 'admin_body_class', 'webmz_admin_options_body_class' );

/**
 * Load assets only on the theme options screen.
 *
 * @param string $hook Admin screen hook.
 * @return void
 */
function webmz_admin_options_assets( $hook ) {
	if ( 'toplevel_page_webmz-options' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	$admin_css_path = WEBMZ_DIR . 'assets/css/admin-options.css';
	$admin_js_path  = WEBMZ_DIR . 'assets/js/admin-options.js';
	$admin_css_ver  = file_exists( $admin_css_path ) ? (string) filemtime( $admin_css_path ) : WEBMZ_VERSION;
	$admin_js_ver   = file_exists( $admin_js_path ) ? (string) filemtime( $admin_js_path ) : WEBMZ_VERSION;

	$fonts      = webmz_get_local_fonts();
	$options    = webmz_get_options();
	$font_slug  = isset( $options['admin_font_family'] ) ? sanitize_key( $options['admin_font_family'] ) : 'system';
	$admin_font = isset( $fonts[ $font_slug ] ) ? $fonts[ $font_slug ] : $fonts['system'];
	$font_deps  = array();

	if ( ! empty( $admin_font['css'] ) && file_exists( WEBMZ_DIR . $admin_font['css'] ) ) {
		wp_enqueue_style( 'webmz-admin-options-font', WEBMZ_URI . $admin_font['css'], array(), WEBMZ_VERSION );
		$font_deps[] = 'webmz-admin-options-font';
	}

	wp_enqueue_style( 'webmz-admin-options', WEBMZ_URI . 'assets/css/admin-options.css', $font_deps, $admin_css_ver );

	$font_stack = isset( $admin_font['stack'] ) ? $admin_font['stack'] : '-apple-system, BlinkMacSystemFont, "Segoe UI", Tahoma, Arial, sans-serif';
	$font_css   = '.webmz-admin, .webmz-admin p, .webmz-admin label, .webmz-admin .description, .webmz-admin .webmz-tab__label, .webmz-admin .webmz-field, .webmz-admin .webmz-status, .webmz-admin h1, .webmz-admin h2, .webmz-admin strong{font-family:' . $font_stack . ' !important;}';
	$font_css  .= '.webmz-admin svg, .webmz-admin .webmz-tab__icon, .webmz-admin .webmz-tab__icon svg, .webmz-admin .webmz-tab__lucide, .webmz-admin [data-lucide], .webmz-admin .dashicons, .webmz-admin .dashicons:before{font-family:initial !important;}';
	wp_add_inline_style( 'webmz-admin-options', $font_css );
	wp_enqueue_script( 'webmz-lucide', WEBMZ_URI . 'assets/vendor/lucide/lucide.min.js', array(), '0.469.0', true );
	wp_enqueue_script( 'webmz-admin-options', WEBMZ_URI . 'assets/js/admin-options.js', array( 'jquery', 'jquery-ui-sortable', 'webmz-lucide' ), $admin_js_ver, true );
	wp_localize_script( 'webmz-admin-options', 'webmzAdmin', array(
		'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
		'nonce'         => wp_create_nonce( 'webmz_save_options' ),
		'childNonce'    => wp_create_nonce( 'webmz_child_theme' ),
		'defaultTab'    => 'appearance',
		'mediaTitle'    => esc_html__( 'انتخاب تصویر', 'webmz' ),
		'mediaButton'   => esc_html__( 'استفاده از تصویر', 'webmz' ),
		'saving'        => esc_html__( 'در حال ذخیره...', 'webmz' ),
		'saved'         => esc_html__( 'تنظیمات با موفقیت ذخیره شد.', 'webmz' ),
		'error'         => esc_html__( 'ذخیره تنظیمات با خطا روبه‌رو شد.', 'webmz' ),
		'installingChild' => esc_html__( 'در حال نصب پوسته فرزند...', 'webmz' ),
		'childInstallError' => esc_html__( 'نصب پوسته فرزند با خطا روبه‌رو شد.', 'webmz' ),
	) );
}
add_action( 'admin_enqueue_scripts', 'webmz_admin_options_assets' );

/**
 * Render a fixed SVG icon for the theme options tabs.
 *
 * @param string $icon Icon slug.
 * @return string
 */
function webmz_admin_tab_icon( $icon ) {
	$icons = array(
		'appearance'       => 'palette',
		'floating-contact' => 'phone-call',
		'builder'          => 'layout-grid',
		'account'          => 'user-round',
		'checkout'         => 'credit-card',
		'otp'              => 'shield-check',
		'tickets'          => 'ticket',
		'ai-chat'          => 'bot',
	);

	$name = isset( $icons[ sanitize_key( $icon ) ] ) ? $icons[ sanitize_key( $icon ) ] : 'settings';

	return '<i data-lucide="' . esc_attr( $name ) . '" class="webmz-tab__lucide" aria-hidden="true"></i>';
}

/**
 * Render a layout select input.
 *
 * @param string $key Option key suffix.
 * @param string $label User facing label.
 * @param array  $options Existing stored options.
 * @return void
 */
function webmz_render_layout_select( $key, $label, $options ) {
	$choices = webmz_get_layout_choices( $key );
	$current = isset( $options[ 'layout_' . $key ] ) ? absint( $options[ 'layout_' . $key ] ) : 0;
	$classes = 'webmz-field webmz-field--select webmz-layout-field';
	if ( $current > 0 ) {
		$classes .= ' is-layout-assigned';
	}
	?>
	<label class="<?php echo esc_attr( $classes ); ?>">
		<span><?php echo esc_html( $label ); ?></span>
		<select name="options[layout_<?php echo esc_attr( $key ); ?>]">
			<option value="0"><?php esc_html_e( 'استفاده از طرح پیش‌فرض قالب', 'webmz' ); ?></option>
			<?php foreach ( $choices as $id => $title ) : ?>
				<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $current, $id ); ?>><?php echo esc_html( $title ); ?></option>
			<?php endforeach; ?>
		</select>
	</label>
	<?php
}


/**
 * Render reusable media picker field for theme options.
 *
 * @param string $input_name Input name.
 * @param int    $attachment_id Selected attachment ID.
 * @param string $label Field label.
 * @return void
 */
function webmz_render_media_picker_field( $input_name, $attachment_id, $label ) {
	$attachment_id = absint( $attachment_id );
	$image_url     = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'thumbnail' ) : '';
	$field_id      = 'webmz-media-' . md5( $input_name );
	?>
	<div class="webmz-media-field">
		<div class="webmz-media-field__head">
			<span><?php echo esc_html( $label ); ?></span>
		</div>
		<div class="webmz-media-field__body">
			<div class="webmz-media-preview <?php echo $image_url ? '' : 'is-empty'; ?>" data-webmz-media-preview="<?php echo esc_attr( $field_id ); ?>">
				<?php if ( $image_url ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>" alt="">
				<?php else : ?>
					<span><?php esc_html_e( 'بدون آیکون', 'webmz' ); ?></span>
				<?php endif; ?>
			</div>
			<input type="hidden" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $attachment_id ); ?>">
			<div class="webmz-media-actions">
				<button type="button" class="button webmz-media-upload" data-target="#<?php echo esc_attr( $field_id ); ?>" data-preview="[data-webmz-media-preview='<?php echo esc_attr( $field_id ); ?>']" data-title="<?php esc_attr_e( 'انتخاب آیکون', 'webmz' ); ?>" data-button="<?php esc_attr_e( 'استفاده از آیکون', 'webmz' ); ?>"><?php esc_html_e( 'آپلود/انتخاب', 'webmz' ); ?></button>
				<button type="button" class="button webmz-media-remove" data-target="#<?php echo esc_attr( $field_id ); ?>" data-preview="[data-webmz-media-preview='<?php echo esc_attr( $field_id ); ?>']"><?php esc_html_e( 'حذف', 'webmz' ); ?></button>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render reusable color picker field for theme options.
 *
 * @param string $input_name Input name attribute.
 * @param string $value      Current hex color value.
 * @param string $label      Field label.
 * @return void
 */
function webmz_render_color_field( $input_name, $value, $label ) {
	$field_id = 'webmz-color-' . md5( $input_name );
	$hex      = sanitize_hex_color( $value );
	if ( ! $hex ) {
		$hex = '#000000';
	}
	?>
	<div class="webmz-field webmz-field--color">
		<span class="webmz-field__label"><?php echo esc_html( $label ); ?></span>
		<div class="webmz-color-field">
			<input type="color" class="webmz-color-field__native" id="<?php echo esc_attr( $field_id ); ?>-native" value="<?php echo esc_attr( $hex ); ?>" aria-label="<?php echo esc_attr( $label ); ?>">
			<input type="text" class="webmz-color-field__hex" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $hex ); ?>" maxlength="7" dir="ltr" placeholder="#000000" spellcheck="false">
		</div>
	</div>
	<?php
}

/**
 * Theme options page output.
 *
 * @return void
 */
function webmz_render_options_page() {
	$options = webmz_get_options();
	$fonts   = webmz_get_local_fonts();
	$types   = webmz_get_layout_types();
	$ticket_settings = function_exists( 'webmz_ticket_get_settings' ) ? webmz_ticket_get_settings() : array();
	$ticket_defaults = function_exists( 'webmz_ticket_default_settings' ) ? webmz_ticket_default_settings() : array(
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
	$ticket_settings = wp_parse_args( is_array( $ticket_settings ) ? $ticket_settings : array(), $ticket_defaults );
	$ticket_order_statuses = function_exists( 'wc_get_order_statuses' ) ? wc_get_order_statuses() : array(
		'wc-completed'  => esc_html__( 'تکمیل‌شده', 'webmz' ),
		'wc-processing' => esc_html__( 'در حال انجام', 'webmz' ),
		'wc-on-hold'    => esc_html__( 'در انتظار بررسی', 'webmz' ),
	);

	$account_endpoint_labels = function_exists( 'webmz_account_default_endpoint_labels' ) ? webmz_account_default_endpoint_labels() : array(
		'dashboard'       => esc_html__( 'پیشخوان', 'webmz' ),
		'orders'          => esc_html__( 'سفارش‌ها', 'webmz' ),
		'downloads'       => esc_html__( 'دانلودها', 'webmz' ),
		'edit-address'    => esc_html__( 'نشانی', 'webmz' ),
		'edit-account'    => esc_html__( 'جزئیات حساب', 'webmz' ),
		'saved-videos'    => esc_html__( 'ذخیره شده‌ها', 'webmz' ),
		'support-tickets' => esc_html__( 'تیکت‌های پشتیبانی', 'webmz' ),
		'customer-logout' => esc_html__( 'بیرون رفتن', 'webmz' ),
	);
	$account_endpoint_icons  = isset( $options['account_endpoint_icons'] ) && is_array( $options['account_endpoint_icons'] ) ? $options['account_endpoint_icons'] : array();
	$account_endpoint_titles = function_exists( 'webmz_account_get_endpoint_title_overrides' ) ? webmz_account_get_endpoint_title_overrides() : ( isset( $options['account_endpoint_titles'] ) && is_array( $options['account_endpoint_titles'] ) ? $options['account_endpoint_titles'] : array() );
	$account_endpoint_order  = function_exists( 'webmz_account_get_endpoint_order_values' ) ? webmz_account_get_endpoint_order_values() : ( isset( $options['account_endpoint_order'] ) && is_array( $options['account_endpoint_order'] ) ? $options['account_endpoint_order'] : array() );
	$account_visible_endpoints = isset( $options['account_visible_endpoints'] ) && is_array( $options['account_visible_endpoints'] ) ? array_map( 'sanitize_key', $options['account_visible_endpoints'] ) : array();
	$account_custom_endpoints = function_exists( 'webmz_account_sanitize_custom_endpoints' ) ? webmz_account_sanitize_custom_endpoints( isset( $options['account_custom_endpoints'] ) ? $options['account_custom_endpoints'] : array() ) : array();
	foreach ( $account_custom_endpoints as $custom_endpoint ) {
		if ( isset( $custom_endpoint['slug'], $custom_endpoint['title'] ) && '' !== $custom_endpoint['slug'] && '' !== $custom_endpoint['title'] ) {
			$account_endpoint_labels[ sanitize_key( $custom_endpoint['slug'] ) ] = sanitize_text_field( $custom_endpoint['title'] );
		}
	}
	if ( function_exists( 'webmz_account_sort_endpoint_items_by_order' ) ) {
		$account_endpoint_labels = webmz_account_sort_endpoint_items_by_order( $account_endpoint_labels, $account_endpoint_order );
	}
	$account_ajax_enabled = isset( $options['account_ajax_enabled'] ) ? $options['account_ajax_enabled'] : 'yes';
	$checkout_field_choices = function_exists( 'webmz_checkout_get_field_choices' ) ? webmz_checkout_get_field_choices() : array();
	$checkout_visible_fields = function_exists( 'webmz_checkout_get_visible_fields' ) ? webmz_checkout_get_visible_fields() : array();
	$child_theme_status = function_exists( 'webmz_get_child_theme_status' ) ? webmz_get_child_theme_status() : array(
		'status'      => 'unknown',
		'label'       => esc_html__( 'وضعیت پوسته فرزند نامشخص است.', 'webmz' ),
		'button_text' => esc_html__( 'نصب و فعال‌سازی پوسته فرزند', 'webmz' ),
		'is_active'   => false,
	);
	$floating_contact_items = function_exists( 'webmz_get_floating_contact_items' ) ? webmz_get_floating_contact_items() : webmz_get_default_floating_contact_items();
	$floating_contact_position = isset( $options['floating_contact_position'] ) && 'right' === $options['floating_contact_position'] ? 'right' : 'left';
	?>
	<div class="wrap webmz-admin" dir="rtl">
		<form id="webmz-options-form" class="webmz-admin__shell">

			<aside class="webmz-admin__sidebar">
				<div class="webmz-admin__sidebar-brand">
					<div class="webmz-admin__logo-preview is-empty">
						<span>WEBMZ</span>
					</div>
					<div class="webmz-admin__sidebar-meta">
						<strong><?php esc_html_e( 'آکام', 'webmz' ); ?></strong>
						<span class="webmz-admin__version">v<?php echo esc_html( WEBMZ_VERSION ); ?></span>
					</div>
				</div>

				<p class="webmz-admin__sidebar-label"><?php esc_html_e( 'بخش‌های تنظیمات', 'webmz' ); ?></p>

				<nav class="webmz-tabs" aria-label="<?php esc_attr_e( 'بخش‌های تنظیمات', 'webmz' ); ?>">
				<button type="button" class="webmz-tab is-active" data-panel="appearance"><span class="webmz-tab__icon"><?php echo webmz_admin_tab_icon( 'appearance' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span class="webmz-tab__label"><?php esc_html_e( 'ظاهر قالب', 'webmz' ); ?></span></button>
				<button type="button" class="webmz-tab" data-panel="floating-contact"><span class="webmz-tab__icon"><?php echo webmz_admin_tab_icon( 'floating-contact' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span class="webmz-tab__label"><?php esc_html_e( 'تماس شناور', 'webmz' ); ?></span></button>
				<button type="button" class="webmz-tab" data-panel="builder"><span class="webmz-tab__icon"><?php echo webmz_admin_tab_icon( 'builder' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span class="webmz-tab__label"><?php esc_html_e( 'ساختار المنتوری', 'webmz' ); ?></span></button>
				<button type="button" class="webmz-tab" data-panel="account"><span class="webmz-tab__icon"><?php echo webmz_admin_tab_icon( 'account' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span class="webmz-tab__label"><?php esc_html_e( 'پنل حساب کاربری', 'webmz' ); ?></span></button>
				<button type="button" class="webmz-tab" data-panel="checkout"><span class="webmz-tab__icon"><?php echo webmz_admin_tab_icon( 'checkout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span class="webmz-tab__label"><?php esc_html_e( 'تنظیمات پرداخت', 'webmz' ); ?></span></button>
				<button type="button" class="webmz-tab" data-panel="otp"><span class="webmz-tab__icon"><?php echo webmz_admin_tab_icon( 'otp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span class="webmz-tab__label"><?php esc_html_e( 'ورود پیامکی OTP', 'webmz' ); ?></span></button>
				<button type="button" class="webmz-tab" data-panel="tickets"><span class="webmz-tab__icon"><?php echo webmz_admin_tab_icon( 'tickets' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span class="webmz-tab__label"><?php esc_html_e( 'تیکت پشتیبانی', 'webmz' ); ?></span></button>
				<button type="button" class="webmz-tab" data-panel="ai-chat"><span class="webmz-tab__icon"><?php echo webmz_admin_tab_icon( 'ai-chat' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span class="webmz-tab__label"><?php esc_html_e( 'دستیار هوشمند', 'webmz' ); ?></span></button>
				</nav>
			</aside>

			<div class="webmz-admin__workspace">
				<header class="webmz-admin__header">
					<div class="webmz-admin__header-copy">
						<p class="webmz-admin__eyebrow"><?php esc_html_e( 'پنل مدیریت پوسته', 'webmz' ); ?></p>
						<h1><?php esc_html_e( 'تنظیمات قالب آکام', 'webmz' ); ?></h1>
						<p><?php esc_html_e( 'قالب پایه المنتوری و ووکامرسی — مدیریت ظاهر، لایه‌ها و یکپارچه‌سازی‌ها', 'webmz' ); ?></p>
					</div>
				</header>

				<div class="webmz-admin__content">

			<section class="webmz-panel is-active" data-panel="appearance">
				<div class="webmz-card">
					<h2><?php esc_html_e( 'فاویکون سایت', 'webmz' ); ?></h2>
					<p class="description"><?php esc_html_e( 'آیکون کوچک نمایش‌داده‌شده در تب مرورگر و بوکمارک‌ها. پیشنهاد می‌شود تصویر مربعی با حداقل ۵۱۲×۵۱۲ پیکسل آپلود کنید.', 'webmz' ); ?></p>
					<?php webmz_render_media_picker_field( 'options[favicon_id]', isset( $options['favicon_id'] ) ? absint( $options['favicon_id'] ) : 0, esc_html__( 'تصویر فاویکون', 'webmz' ) ); ?>
				</div>
				<div class="webmz-card webmz-child-theme-card">
					<div class="webmz-card__title-row">
						<div>
							<h2><?php esc_html_e( 'پوسته فرزند آکام', 'webmz' ); ?></h2>
							<p class="description"><?php esc_html_e( 'برای اعمال تغییرات اختصاصی بدون آسیب دیدن هنگام بروزرسانی قالب اصلی، پوسته فرزند آماده آکام را نصب و فعال کنید.', 'webmz' ); ?></p>
						</div>
						<span class="webmz-child-theme-status webmz-child-theme-status--<?php echo esc_attr( $child_theme_status['status'] ); ?>" id="webmz-child-theme-status"><?php echo esc_html( $child_theme_status['label'] ); ?></span>
					</div>
					<div class="webmz-child-theme-actions">
						<button type="button" class="button button-primary" id="webmz-install-child-theme" <?php disabled( ! empty( $child_theme_status['is_active'] ) ); ?>>
							<?php echo esc_html( $child_theme_status['button_text'] ); ?>
						</button>
						<p class="description"><?php esc_html_e( 'فایل‌های پوسته فرزند از پوشه آماده داخل همین قالب به مسیر پوسته‌های وردپرس کپی می‌شوند و سپس فعال‌سازی انجام می‌شود.', 'webmz' ); ?></p>
					</div>
				</div>
				<div class="webmz-card">
					<h2><?php esc_html_e( 'رنگ‌های قالب', 'webmz' ); ?></h2>
					<div class="webmz-grid">
						<?php
						$color_fields = array(
							'color_primary'       => esc_html__( 'رنگ اصلی', 'webmz' ),
							'color_secondary'     => esc_html__( 'رنگ ثانویه', 'webmz' ),
							'color_primary_hover' => esc_html__( 'هاور رنگ اصلی', 'webmz' ),
							'color_primary_light' => esc_html__( 'رنگ اصلی کمرنگ', 'webmz' ),
							'color_text_dark'     => esc_html__( 'رنگ متن مشکی', 'webmz' ),
							'color_text_gray'     => esc_html__( 'رنگ متن خاکستری', 'webmz' ),
							'color_text_navy'     => esc_html__( 'رنگ متن سورمه‌ای', 'webmz' ),
							'color_background'    => esc_html__( 'رنگ پس‌زمینه سایت', 'webmz' ),
						);
						foreach ( $color_fields as $key => $label ) :
							webmz_render_color_field(
								'options[' . $key . ']',
								$options[ $key ],
								$label
							);
						endforeach; ?>
					</div>
				</div>
				<div class="webmz-card">
					<h2><?php esc_html_e( 'عرض و چیدمان سایت', 'webmz' ); ?></h2>
					<label class="webmz-field webmz-field--wide">
						<span><?php esc_html_e( 'حداکثر عرض محتوای سایت (پیکسل)', 'webmz' ); ?></span>
						<input type="number" name="options[container_width]" value="<?php echo esc_attr( absint( $options['container_width'] ) ); ?>" min="720" max="1920" step="10">
					</label>
					<p class="description"><?php esc_html_e( 'این مقدار عرض کانتینرهای پیش‌فرض قالب را کنترل می‌کند. طرح‌های تمام‌عرضی که مستقیماً در Elementor ساخته‌اید مستقل از این مقدار هستند.', 'webmz' ); ?></p>
				</div>
				<div class="webmz-card">
					<h2><?php esc_html_e( 'فونت پوسته و پنل مدیریت', 'webmz' ); ?></h2>
					<div class="webmz-grid">
						<label class="webmz-field webmz-field--select">
							<span><?php esc_html_e( 'فونت فعال پوسته', 'webmz' ); ?></span>
							<select name="options[font_family]">
								<?php foreach ( $fonts as $slug => $font ) : ?>
									<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $options['font_family'], $slug ); ?>><?php echo esc_html( $font['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>

						<label class="webmz-field webmz-field--select">
							<span><?php esc_html_e( 'فونت پنل ادمین وردپرس', 'webmz' ); ?></span>
							<select name="options[admin_font_family]">
								<?php foreach ( $fonts as $slug => $font ) : ?>
									<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( isset( $options['admin_font_family'] ) ? $options['admin_font_family'] : 'system', $slug ); ?>><?php echo esc_html( $font['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
					</div>
					<p class="description"><?php esc_html_e( 'برای افزودن فونت محلی، فایل font.css را داخل مسیر assets/fonts/{font-slug}/ قرار دهید. گزینه فونت ادمین فقط روی پیشخوان وردپرس اعمال می‌شود.', 'webmz' ); ?></p>
				</div>
				<div class="webmz-card">
					<h2><?php esc_html_e( 'آواتار لوکال کاربران', 'webmz' ); ?></h2>
					<div class="webmz-grid webmz-grid--tickets">
						<label class="webmz-field webmz-field--checkbox">
							<input type="checkbox" name="options[local_gravatar_enable]" value="yes" <?php checked( isset( $options['local_gravatar_enable'] ) ? $options['local_gravatar_enable'] : 'no', 'yes' ); ?>>
							<span><?php esc_html_e( 'جایگزینی Gravatar با تصویر لوکال فعال شود', 'webmz' ); ?></span>
						</label>
					</div>
					<label class="webmz-field webmz-field--wide">
						<span><?php esc_html_e( 'آدرس تصویر آواتار لوکال', 'webmz' ); ?></span>
						<input
							type="url"
							name="options[local_gravatar_url]"
							value="<?php echo esc_url( isset( $options['local_gravatar_url'] ) ? $options['local_gravatar_url'] : '' ); ?>"
							placeholder="<?php echo esc_attr( WEBMZ_URI . 'assets/images/default-avatar.png' ); ?>"
							style="direction:ltr;text-align:left;"
						>
					</label>
					<p class="description"><?php esc_html_e( 'وقتی این گزینه فعال باشد، تمام آواتارهای سایت به جای Gravatar از این تصویر لود می‌شوند تا در شرایط اختلال یا نت ملی، سایت منتظر Gravatar نماند.', 'webmz' ); ?></p>
				</div>
				<div class="webmz-card">
					<h2><?php esc_html_e( 'جستجوهای پرطرفدار', 'webmz' ); ?></h2>
					<label class="webmz-field webmz-field--wide">
						<span><?php esc_html_e( 'عبارت‌های پیشنهادی ویجت جستجو', 'webmz' ); ?></span>
						<textarea
							name="options[popular_searches]"
							rows="6"
							placeholder="<?php esc_attr_e( "مثال:\nکرم آبرسان\nآموزش وردپرس\nطراحی سایت", 'webmz' ); ?>"
						><?php echo esc_textarea( $options['popular_searches'] ); ?></textarea>
					</label>
					<p class="description"><?php esc_html_e( 'هر عبارت را در یک خط وارد کنید. حداکثر ۲۰ عبارت در ویجت جستجوی ایجکسی نمایش داده می‌شود.', 'webmz' ); ?></p>
				</div>
				<div class="webmz-card">
					<h2><?php esc_html_e( 'صفحه سینگل محصول', 'webmz' ); ?></h2>
					<div class="webmz-grid webmz-grid--tickets">
						<label class="webmz-field webmz-field--checkbox">
							<input type="checkbox" name="options[sticky_add_to_cart_enabled]" value="yes" <?php checked( isset( $options['sticky_add_to_cart_enabled'] ) ? $options['sticky_add_to_cart_enabled'] : 'yes', 'yes' ); ?>>
							<span><?php esc_html_e( 'نوار چسبان افزودن به سبد خرید در موبایل فعال باشد', 'webmz' ); ?></span>
						</label>
					</div>
					<p class="description"><?php esc_html_e( 'در صفحه محصول، پس از اسکرول کاربر یک نوار پایین صفحه نمایش داده می‌شود. این نوار فقط در موبایل فعال است و برای محصولات متغیر کاربر را به بخش خرید هدایت می‌کند.', 'webmz' ); ?></p>
				</div>
			</section>

			<section class="webmz-panel" data-panel="floating-contact">
				<div class="webmz-card webmz-notice">
					<h2><?php esc_html_e( 'دکمه تماس شناور پایین سایت', 'webmz' ); ?></h2>
					<p><?php esc_html_e( 'نمایش دکمه شناور تماس، جایگاه آن و راه‌های ارتباطی داخل پنل بازشونده را از این بخش مدیریت کنید.', 'webmz' ); ?></p>
				</div>

				<div class="webmz-card">
					<h2><?php esc_html_e( 'تنظیمات کلی', 'webmz' ); ?></h2>
					<div class="webmz-grid webmz-grid--3">
						<label class="webmz-field webmz-field--checkbox">
							<input type="checkbox" name="options[floating_contact_enabled]" value="yes" <?php checked( isset( $options['floating_contact_enabled'] ) ? $options['floating_contact_enabled'] : 'yes', 'yes' ); ?>>
							<span><?php esc_html_e( 'دکمه تماس شناور نمایش داده شود', 'webmz' ); ?></span>
						</label>
						<label class="webmz-field webmz-field--select">
							<span><?php esc_html_e( 'جایگاه دکمه', 'webmz' ); ?></span>
							<select name="options[floating_contact_position]">
								<option value="left" <?php selected( $floating_contact_position, 'left' ); ?>><?php esc_html_e( 'پایین چپ', 'webmz' ); ?></option>
								<option value="right" <?php selected( $floating_contact_position, 'right' ); ?>><?php esc_html_e( 'پایین راست', 'webmz' ); ?></option>
							</select>
						</label>
						<?php
						webmz_render_color_field(
							'options[floating_contact_button_color]',
							isset( $options['floating_contact_button_color'] ) ? $options['floating_contact_button_color'] : '#b65a78',
							esc_html__( 'رنگ دکمه اصلی', 'webmz' )
						);
						?>
					</div>
					<div class="webmz-grid webmz-grid--3" style="margin-top:18px">
						<?php
						webmz_render_color_field(
							'options[floating_contact_button_hover_color]',
							isset( $options['floating_contact_button_hover_color'] ) ? $options['floating_contact_button_hover_color'] : '#a54c6b',
							esc_html__( 'رنگ هاور دکمه', 'webmz' )
						);
						?>
						<label class="webmz-field webmz-field--wide">
							<span><?php esc_html_e( 'فاصله از سمت چپ/راست (پیکسل)', 'webmz' ); ?></span>
							<input type="number" name="options[floating_contact_offset_x]" value="<?php echo esc_attr( isset( $options['floating_contact_offset_x'] ) ? absint( $options['floating_contact_offset_x'] ) : 24 ); ?>" min="0" max="200" step="1">
						</label>
						<label class="webmz-field webmz-field--wide">
							<span><?php esc_html_e( 'فاصله از پایین (پیکسل)', 'webmz' ); ?></span>
							<input type="number" name="options[floating_contact_offset_bottom]" value="<?php echo esc_attr( isset( $options['floating_contact_offset_bottom'] ) ? absint( $options['floating_contact_offset_bottom'] ) : 24 ); ?>" min="0" max="240" step="1">
						</label>
					</div>
					<div class="webmz-grid webmz-grid--2" style="margin-top:18px">
						<label class="webmz-field webmz-field--wide">
							<span><?php esc_html_e( 'عنوان پنل', 'webmz' ); ?></span>
							<input type="text" name="options[floating_contact_panel_title]" value="<?php echo esc_attr( isset( $options['floating_contact_panel_title'] ) ? $options['floating_contact_panel_title'] : esc_html__( 'پاسخگوی شما هستیم', 'webmz' ) ); ?>">
						</label>
						<label class="webmz-field webmz-field--wide">
							<span><?php esc_html_e( 'توضیح کوتاه پنل', 'webmz' ); ?></span>
							<input type="text" name="options[floating_contact_panel_desc]" value="<?php echo esc_attr( isset( $options['floating_contact_panel_desc'] ) ? $options['floating_contact_panel_desc'] : esc_html__( 'یکی از راه‌های زیر را برای ارتباط انتخاب کنید', 'webmz' ) ); ?>">
						</label>
					</div>
					<div class="webmz-floating-contact-button-icon">
						<?php webmz_render_media_picker_field( 'options[floating_contact_button_icon_id]', isset( $options['floating_contact_button_icon_id'] ) ? absint( $options['floating_contact_button_icon_id'] ) : 0, esc_html__( 'آیکون دکمه اصلی', 'webmz' ) ); ?>
					</div>
				</div>

				<div class="webmz-card">
					<div class="webmz-card__title-row">
						<div>
							<h2><?php esc_html_e( 'راه‌های ارتباطی', 'webmz' ); ?></h2>
							<p class="description"><?php esc_html_e( 'ردیف‌ها را با دستگیره جابه‌جا کنید. برای هر مورد می‌توانید عنوان، لینک، رنگ و آیکون دلخواه انتخاب کنید.', 'webmz' ); ?></p>
						</div>
						<button type="button" class="button button-primary" id="webmz-add-floating-contact-item"><?php esc_html_e( 'افزودن راه ارتباطی', 'webmz' ); ?></button>
					</div>

					<div class="webmz-floating-contact-items" id="webmz-floating-contact-items">
						<?php foreach ( $floating_contact_items as $index => $item ) : ?>
							<?php
							$item = wp_parse_args(
								is_array( $item ) ? $item : array(),
								array(
									'enabled' => 'yes',
									'title'   => '',
									'url'     => '',
									'color'   => '#2563eb',
									'icon'    => 'chat',
									'icon_id' => 0,
									'order'   => absint( $index ) + 1,
								)
							);
							?>
							<div class="webmz-floating-contact-row">
								<span class="webmz-floating-contact-drag" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'جابه‌جایی راه ارتباطی', 'webmz' ); ?>">☰</span>
								<input type="hidden" class="webmz-floating-contact-order-input" name="options[floating_contact_items][<?php echo esc_attr( $index ); ?>][order]" value="<?php echo esc_attr( absint( $item['order'] ) ); ?>">
								<input type="hidden" name="options[floating_contact_items][<?php echo esc_attr( $index ); ?>][icon]" value="<?php echo esc_attr( sanitize_key( $item['icon'] ) ); ?>">
								<label class="webmz-floating-contact-row__enabled">
									<input type="checkbox" name="options[floating_contact_items][<?php echo esc_attr( $index ); ?>][enabled]" value="yes" <?php checked( $item['enabled'], 'yes' ); ?>>
									<span><?php esc_html_e( 'نمایش', 'webmz' ); ?></span>
								</label>
								<div class="webmz-floating-contact-row__fields">
									<label>
										<span><?php esc_html_e( 'عنوان', 'webmz' ); ?></span>
										<input type="text" name="options[floating_contact_items][<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $item['title'] ); ?>">
									</label>
									<label>
										<span><?php esc_html_e( 'لینک', 'webmz' ); ?></span>
										<input type="url" name="options[floating_contact_items][<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_url( $item['url'] ); ?>" dir="ltr" placeholder="https://example.com یا tel:">
									</label>
									<label class="webmz-floating-contact-row__color">
										<span class="webmz-field__label"><?php esc_html_e( 'رنگ', 'webmz' ); ?></span>
										<div class="webmz-color-field">
											<input type="color" class="webmz-color-field__native" value="<?php echo esc_attr( sanitize_hex_color( $item['color'] ) ? $item['color'] : '#2563eb' ); ?>" aria-label="<?php esc_attr_e( 'رنگ', 'webmz' ); ?>">
											<input type="text" class="webmz-color-field__hex" name="options[floating_contact_items][<?php echo esc_attr( $index ); ?>][color]" value="<?php echo esc_attr( $item['color'] ); ?>" maxlength="7" dir="ltr" placeholder="#2563eb" spellcheck="false">
										</div>
									</label>
								</div>
								<?php webmz_render_media_picker_field( 'options[floating_contact_items][' . $index . '][icon_id]', absint( $item['icon_id'] ), esc_html__( 'آیکون اختصاصی', 'webmz' ) ); ?>
								<div class="webmz-floating-contact-row__actions">
									<button type="button" class="button webmz-floating-contact-up"><?php esc_html_e( 'بالا', 'webmz' ); ?></button>
									<button type="button" class="button webmz-floating-contact-down"><?php esc_html_e( 'پایین', 'webmz' ); ?></button>
									<button type="button" class="button webmz-remove-floating-contact-item"><?php esc_html_e( 'حذف', 'webmz' ); ?></button>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<script type="text/html" id="webmz-floating-contact-template">
						<div class="webmz-floating-contact-row">
							<span class="webmz-floating-contact-drag" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'جابه‌جایی راه ارتباطی', 'webmz' ); ?>">☰</span>
							<input type="hidden" class="webmz-floating-contact-order-input" name="options[floating_contact_items][__INDEX__][order]" value="">
							<input type="hidden" name="options[floating_contact_items][__INDEX__][icon]" value="chat">
							<label class="webmz-floating-contact-row__enabled">
								<input type="checkbox" name="options[floating_contact_items][__INDEX__][enabled]" value="yes" checked>
								<span><?php esc_html_e( 'نمایش', 'webmz' ); ?></span>
							</label>
							<div class="webmz-floating-contact-row__fields">
								<label>
									<span><?php esc_html_e( 'عنوان', 'webmz' ); ?></span>
									<input type="text" name="options[floating_contact_items][__INDEX__][title]" value="" placeholder="<?php esc_attr_e( 'مثلاً پشتیبانی آنلاین', 'webmz' ); ?>">
								</label>
								<label>
									<span><?php esc_html_e( 'لینک', 'webmz' ); ?></span>
									<input type="url" name="options[floating_contact_items][__INDEX__][url]" value="" dir="ltr" placeholder="https://example.com">
								</label>
								<label class="webmz-floating-contact-row__color">
									<span class="webmz-field__label"><?php esc_html_e( 'رنگ', 'webmz' ); ?></span>
									<div class="webmz-color-field">
										<input type="color" class="webmz-color-field__native" value="#2563eb" aria-label="<?php esc_attr_e( 'رنگ', 'webmz' ); ?>">
										<input type="text" class="webmz-color-field__hex" name="options[floating_contact_items][__INDEX__][color]" value="#2563eb" maxlength="7" dir="ltr" placeholder="#2563eb" spellcheck="false">
									</div>
								</label>
							</div>
							<div class="webmz-media-field">
								<div class="webmz-media-field__head"><span><?php esc_html_e( 'آیکون اختصاصی', 'webmz' ); ?></span></div>
								<div class="webmz-media-field__body">
									<div class="webmz-media-preview is-empty" data-webmz-media-preview="webmz-floating-contact-icon-__INDEX__"><span><?php esc_html_e( 'بدون آیکون', 'webmz' ); ?></span></div>
									<input type="hidden" id="webmz-floating-contact-icon-__INDEX__" name="options[floating_contact_items][__INDEX__][icon_id]" value="0">
									<div class="webmz-media-actions">
										<button type="button" class="button webmz-media-upload" data-target="#webmz-floating-contact-icon-__INDEX__" data-preview="[data-webmz-media-preview='webmz-floating-contact-icon-__INDEX__']" data-title="<?php esc_attr_e( 'انتخاب آیکون', 'webmz' ); ?>" data-button="<?php esc_attr_e( 'استفاده از آیکون', 'webmz' ); ?>"><?php esc_html_e( 'آپلود/انتخاب', 'webmz' ); ?></button>
										<button type="button" class="button webmz-media-remove" data-target="#webmz-floating-contact-icon-__INDEX__" data-preview="[data-webmz-media-preview='webmz-floating-contact-icon-__INDEX__']"><?php esc_html_e( 'حذف', 'webmz' ); ?></button>
									</div>
								</div>
							</div>
							<div class="webmz-floating-contact-row__actions">
								<button type="button" class="button webmz-floating-contact-up"><?php esc_html_e( 'بالا', 'webmz' ); ?></button>
								<button type="button" class="button webmz-floating-contact-down"><?php esc_html_e( 'پایین', 'webmz' ); ?></button>
								<button type="button" class="button webmz-remove-floating-contact-item"><?php esc_html_e( 'حذف', 'webmz' ); ?></button>
							</div>
						</div>
					</script>
				</div>
			</section>

			<section class="webmz-panel" data-panel="builder">
				<div class="webmz-card webmz-notice">
					<h2><?php esc_html_e( 'قالب‌ساز آکام با Elementor Free', 'webmz' ); ?></h2>
					<p><?php esc_html_e( 'لایه پیش‌فرض هر بخش را اینجا انتخاب کنید. برای استایل اختصاصی، یک لایه با همان نوع بسازید و در «شرایط نمایش» قوانین نمایش در / عدم نمایش در را تنظیم کنید.', 'webmz' ); ?></p>
				</div>
				<div class="webmz-card">
					<div class="webmz-grid webmz-grid--layouts">
						<?php foreach ( $types as $key => $label ) : ?>
							<?php webmz_render_layout_select( $key, $label, $options ); ?>
						<?php endforeach; ?>
					</div>
				</div>
			</section>


			<section class="webmz-panel" data-panel="account">
				<div class="webmz-card webmz-notice">
					<h2><?php esc_html_e( 'تنظیمات پنل حساب کاربری ووکامرس', 'webmz' ); ?></h2>
					<p><?php esc_html_e( 'از این بخش ظاهر پنل حساب کاربری، آیکون endpointها و endpointهای سفارشی ووکامرس را مدیریت کنید.', 'webmz' ); ?></p>
				</div>

				<div class="webmz-card">
					<h2><?php esc_html_e( 'رفتار پنل', 'webmz' ); ?></h2>
					<div class="webmz-grid webmz-grid--tickets">
						<label class="webmz-field webmz-field--checkbox">
							<input type="checkbox" name="options[account_ajax_enabled]" value="yes" <?php checked( $account_ajax_enabled, 'yes' ); ?>>
							<span><?php esc_html_e( 'ناوبری داخلی پنل حساب کاربری به صورت AJAX انجام شود', 'webmz' ); ?></span>
						</label>
					</div>
					<p class="description"><?php esc_html_e( 'با فعال بودن این گزینه، کلیک روی آیتم‌های پنل حساب کاربری بدون رفرش کامل صفحه محتوای همان بخش را بارگذاری می‌کند. خروج از حساب همیشه به صورت عادی انجام می‌شود.', 'webmz' ); ?></p>
				</div>


				<div class="webmz-card">
					<h2><?php esc_html_e( 'رنگ‌های پنل حساب کاربری', 'webmz' ); ?></h2>
					<div class="webmz-grid">
						<?php
						webmz_render_color_field(
							'options[account_accent_light]',
							isset( $options['account_accent_light'] ) ? $options['account_accent_light'] : '#fff4e7',
							esc_html__( 'رنگ نارنجی کمرنگ / پس‌زمینه آیکون‌ها', 'webmz' )
						);
						?>
					</div>
					<p class="description"><?php esc_html_e( 'این رنگ برای پس‌زمینه‌های کمرنگ داخل پنل حساب کاربری مثل آیکون‌های منو، لیبل‌ها و بخش‌های تزئینی استفاده می‌شود.', 'webmz' ); ?></p>
				</div>

				<div class="webmz-card">
					<h2><?php esc_html_e( 'نمایش، عنوان و چینش endpointهای حساب کاربری', 'webmz' ); ?></h2>
					<p class="description"><?php esc_html_e( 'هر ردیف را می‌توانید با دستگیره جابه‌جا کنید، عنوان نمایشی آن را تغییر دهید و با تیک نمایش، حضورش در منوی حساب کاربری ووکامرس را کنترل کنید.', 'webmz' ); ?></p>
					<div class="webmz-account-endpoint-list" id="webmz-account-endpoint-list">
						<?php $endpoint_position = 1; ?>
						<?php foreach ( $account_endpoint_labels as $endpoint_key => $endpoint_label ) : ?>
							<?php
							$endpoint_key      = sanitize_key( $endpoint_key );
							$is_visible        = empty( $account_visible_endpoints ) || in_array( $endpoint_key, $account_visible_endpoints, true );
							$custom_title      = isset( $account_endpoint_titles[ $endpoint_key ] ) && '' !== $account_endpoint_titles[ $endpoint_key ] ? $account_endpoint_titles[ $endpoint_key ] : $endpoint_label;
							$configured_order  = isset( $account_endpoint_order[ $endpoint_key ] ) ? absint( $account_endpoint_order[ $endpoint_key ] ) : $endpoint_position;
							?>
							<div class="webmz-account-endpoint-row" data-endpoint="<?php echo esc_attr( $endpoint_key ); ?>">
								<span class="webmz-account-endpoint-drag" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'جابه‌جایی endpoint', 'webmz' ); ?>">☰</span>
								<input type="hidden" class="webmz-account-endpoint-order-input" name="options[account_endpoint_order][<?php echo esc_attr( $endpoint_key ); ?>]" value="<?php echo esc_attr( $configured_order ); ?>">
								<label class="webmz-account-endpoint-row__visible">
									<input type="checkbox" name="options[account_visible_endpoints][]" value="<?php echo esc_attr( $endpoint_key ); ?>" <?php checked( $is_visible ); ?>>
									<span><?php esc_html_e( 'نمایش', 'webmz' ); ?></span>
								</label>
								<div class="webmz-account-endpoint-row__main">
									<label>
										<span><?php esc_html_e( 'عنوان نمایشی', 'webmz' ); ?></span>
										<input type="text" name="options[account_endpoint_titles][<?php echo esc_attr( $endpoint_key ); ?>]" value="<?php echo esc_attr( $custom_title ); ?>" placeholder="<?php echo esc_attr( $endpoint_label ); ?>">
									</label>
									<code><?php echo esc_html( $endpoint_key ); ?></code>
								</div>
								<div class="webmz-account-endpoint-row__actions">
									<button type="button" class="button webmz-account-endpoint-up"><?php esc_html_e( 'بالا', 'webmz' ); ?></button>
									<button type="button" class="button webmz-account-endpoint-down"><?php esc_html_e( 'پایین', 'webmz' ); ?></button>
								</div>
							</div>
							<?php $endpoint_position++; ?>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="webmz-card">
					<h2><?php esc_html_e( 'آیکون endpointهای ووکامرس', 'webmz' ); ?></h2>
					<p class="description"><?php esc_html_e( 'برای هر آیتم پنل حساب کاربری می‌توانید یک آیکون PNG/SVG از رسانه وردپرس انتخاب کنید. اگر خالی باشد، آیکون پیش‌فرض قالب نمایش داده می‌شود.', 'webmz' ); ?></p>
					<div class="webmz-endpoint-icons-grid">
						<?php foreach ( $account_endpoint_labels as $endpoint_key => $endpoint_label ) : ?>
							<?php
							$endpoint_key = sanitize_key( $endpoint_key );
							$icon_id      = isset( $account_endpoint_icons[ $endpoint_key ] ) ? absint( $account_endpoint_icons[ $endpoint_key ] ) : 0;
							webmz_render_media_picker_field( 'options[account_endpoint_icons][' . $endpoint_key . ']', $icon_id, $endpoint_label );
							?>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="webmz-card">
					<div class="webmz-card__title-row">
						<div>
							<h2><?php esc_html_e( 'endpointهای سفارشی حساب کاربری', 'webmz' ); ?></h2>
							<p class="description"><?php esc_html_e( 'endpoint سفارشی می‌سازد و آن را داخل منوی حساب کاربری ووکامرس نمایش می‌دهد. آدرس هر endpoint بعد از ذخیره تنظیمات و ذخیره پیوندهای یکتا فعال می‌شود.', 'webmz' ); ?></p>
						</div>
						<button type="button" class="button button-primary" id="webmz-add-custom-endpoint"><?php esc_html_e( 'افزودن endpoint', 'webmz' ); ?></button>
					</div>

					<div class="webmz-custom-endpoints" id="webmz-custom-endpoints">
						<?php foreach ( $account_custom_endpoints as $index => $endpoint ) : ?>
							<div class="webmz-custom-endpoint-row">
								<div class="webmz-custom-endpoint-row__grid">
									<label class="webmz-field webmz-field--select">
										<span><?php esc_html_e( 'عنوان endpoint', 'webmz' ); ?></span>
										<input type="text" name="options[account_custom_endpoints][<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $endpoint['title'] ); ?>" placeholder="<?php esc_attr_e( 'مثلاً مشاوره‌های من', 'webmz' ); ?>">
									</label>
									<label class="webmz-field webmz-field--select">
										<span><?php esc_html_e( 'اسلاگ انگلیسی', 'webmz' ); ?></span>
										<input type="text" name="options[account_custom_endpoints][<?php echo esc_attr( $index ); ?>][slug]" value="<?php echo esc_attr( $endpoint['slug'] ); ?>" placeholder="my-consultations" dir="ltr">
										<small><?php esc_html_e( 'فقط حروف انگلیسی، عدد و خط تیره. آدرس: ', 'webmz' ); ?><code dir="ltr"><?php echo esc_html( function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( $endpoint['slug'] ) : $endpoint['slug'] ); ?></code></small>
									</label>
									<label class="webmz-field webmz-field--checkbox">
										<input type="checkbox" name="options[account_custom_endpoints][<?php echo esc_attr( $index ); ?>][enabled]" value="yes" <?php checked( $endpoint['enabled'], 'yes' ); ?>>
										<span><?php esc_html_e( 'فعال باشد', 'webmz' ); ?></span>
									</label>
								</div>

								<?php webmz_render_media_picker_field( 'options[account_custom_endpoints][' . $index . '][icon_id]', isset( $endpoint['icon_id'] ) ? absint( $endpoint['icon_id'] ) : 0, esc_html__( 'آیکون endpoint', 'webmz' ) ); ?>

								<label class="webmz-field webmz-field--wide">
									<span><?php esc_html_e( 'محتوای دلخواه endpoint', 'webmz' ); ?></span>
									<textarea name="options[account_custom_endpoints][<?php echo esc_attr( $index ); ?>][content]" rows="7" placeholder="<?php esc_attr_e( 'متن، HTML ساده یا shortcode دلخواه را وارد کنید.', 'webmz' ); ?>"><?php echo esc_textarea( $endpoint['content'] ); ?></textarea>
								</label>

								<button type="button" class="button webmz-remove-custom-endpoint"><?php esc_html_e( 'حذف این endpoint', 'webmz' ); ?></button>
							</div>
						<?php endforeach; ?>
					</div>

					<script type="text/html" id="webmz-custom-endpoint-template">
						<div class="webmz-custom-endpoint-row">
							<div class="webmz-custom-endpoint-row__grid">
								<label class="webmz-field webmz-field--select">
									<span><?php esc_html_e( 'عنوان endpoint', 'webmz' ); ?></span>
									<input type="text" name="options[account_custom_endpoints][__INDEX__][title]" value="" placeholder="<?php esc_attr_e( 'مثلاً مشاوره‌های من', 'webmz' ); ?>">
								</label>
								<label class="webmz-field webmz-field--select">
									<span><?php esc_html_e( 'اسلاگ انگلیسی', 'webmz' ); ?></span>
									<input type="text" name="options[account_custom_endpoints][__INDEX__][slug]" value="" placeholder="my-consultations" dir="ltr">
								</label>
								<label class="webmz-field webmz-field--checkbox">
									<input type="checkbox" name="options[account_custom_endpoints][__INDEX__][enabled]" value="yes" checked>
									<span><?php esc_html_e( 'فعال باشد', 'webmz' ); ?></span>
								</label>
							</div>

							<?php webmz_render_media_picker_field( 'options[account_custom_endpoints][__INDEX__][icon_id]', 0, esc_html__( 'آیکون endpoint', 'webmz' ) ); ?>

							<label class="webmz-field webmz-field--wide">
								<span><?php esc_html_e( 'محتوای دلخواه endpoint', 'webmz' ); ?></span>
								<textarea name="options[account_custom_endpoints][__INDEX__][content]" rows="7" placeholder="<?php esc_attr_e( 'متن، HTML ساده یا shortcode دلخواه را وارد کنید.', 'webmz' ); ?>"></textarea>
							</label>

							<button type="button" class="button webmz-remove-custom-endpoint"><?php esc_html_e( 'حذف این endpoint', 'webmz' ); ?></button>
						</div>
					</script>
				</div>
			</section>

			<section class="webmz-panel" data-panel="checkout">
				<div class="webmz-card webmz-notice">
					<h2><?php esc_html_e( 'تنظیمات پرداخت و فیلدهای تسویه حساب', 'webmz' ); ?></h2>
					<p><?php esc_html_e( 'فیلدهایی که تیک داشته باشند در فرم تسویه حساب ووکامرس نمایش داده می‌شوند. با برداشتن تیک هر فیلد، آن فیلد از صفحه پرداخت حذف می‌شود.', 'webmz' ); ?></p>
				</div>

				<?php if ( ! empty( $checkout_field_choices ) ) : ?>
					<?php
					$checkout_section_titles = array(
						'billing'  => esc_html__( 'فیلدهای صورت‌حساب', 'webmz' ),
						'shipping' => esc_html__( 'فیلدهای ارسال', 'webmz' ),
						'order'    => esc_html__( 'فیلدهای سفارش', 'webmz' ),
					);
					?>
					<?php foreach ( $checkout_field_choices as $section_key => $section_fields ) : ?>
						<div class="webmz-card">
							<h2><?php echo esc_html( isset( $checkout_section_titles[ $section_key ] ) ? $checkout_section_titles[ $section_key ] : $section_key ); ?></h2>
							<div class="webmz-grid webmz-grid--tickets">
								<?php foreach ( (array) $section_fields as $field_key => $field_label ) : ?>
									<?php $field_key = sanitize_key( $field_key ); ?>
									<label class="webmz-field webmz-field--checkbox">
										<input type="checkbox" name="options[checkout_visible_fields][]" value="<?php echo esc_attr( $field_key ); ?>" <?php checked( in_array( $field_key, $checkout_visible_fields, true ) ); ?>>
										<span><?php echo esc_html( $field_label ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</section>

			<?php if ( function_exists( 'webmz_otp_render_settings_panel' ) ) { webmz_otp_render_settings_panel(); } ?>

			<section class="webmz-panel" data-panel="ai-chat">
				<?php
				$ai_chat           = function_exists( 'webmz_ai_chat_get_settings' ) ? webmz_ai_chat_get_settings() : array();
				$ai_chat_providers = function_exists( 'webmz_ai_chat_providers' ) ? webmz_ai_chat_providers() : array();
				$ai_chat_has_key   = ! empty( $ai_chat['api_key'] );
				?>
				<div class="webmz-card webmz-notice">
					<h2><?php esc_html_e( 'دستیار هوشمند پشتیبانی (چت با هوش مصنوعی)', 'webmz' ); ?></h2>
					<p><?php esc_html_e( 'یک پنجره چت شناور که به سوالات کاربران درباره سایت، دوره‌ها و خرید با کمک هوش مصنوعی AvalAI یا GapGPT پاسخ می‌دهد. کلید API فقط روی سرور استفاده می‌شود و در مرورگر کاربران نمایش داده نمی‌شود.', 'webmz' ); ?></p>
				</div>

				<div class="webmz-card">
					<h2><?php esc_html_e( 'اتصال به سرویس هوش مصنوعی', 'webmz' ); ?></h2>
					<div class="webmz-grid webmz-grid--3">
						<label class="webmz-field webmz-field--checkbox">
							<input type="checkbox" name="ai_chat[enabled]" value="yes" <?php checked( $ai_chat['enabled'] ?? 'no', 'yes' ); ?>>
							<span><?php esc_html_e( 'دستیار هوشمند فعال باشد', 'webmz' ); ?></span>
						</label>
						<label class="webmz-field webmz-field--select">
							<span><?php esc_html_e( 'سرویس', 'webmz' ); ?></span>
							<select name="ai_chat[provider]">
								<?php foreach ( $ai_chat_providers as $provider_key => $provider ) : ?>
									<option value="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $ai_chat['provider'] ?? 'avalai', $provider_key ); ?>><?php echo esc_html( $provider['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
						<label class="webmz-field">
							<span><?php esc_html_e( 'مدل', 'webmz' ); ?></span>
							<input type="text" name="ai_chat[model]" value="<?php echo esc_attr( $ai_chat['model'] ?? 'gpt-4o-mini' ); ?>" dir="ltr" placeholder="gpt-4o-mini">
						</label>
					</div>
					<div class="webmz-grid webmz-grid--2" style="margin-top:18px">
						<label class="webmz-field webmz-field--wide">
							<span><?php esc_html_e( 'کلید API', 'webmz' ); ?></span>
							<input type="password" name="ai_chat[api_key]" value="" dir="ltr" autocomplete="new-password" placeholder="<?php echo esc_attr( $ai_chat_has_key ? __( '•••••••• (ذخیره شده؛ برای تغییر، کلید جدید را وارد کنید)', 'webmz' ) : 'sk-...' ); ?>">
							<?php if ( $ai_chat_has_key ) : ?>
								<small><label><input type="checkbox" name="ai_chat[api_key_clear]" value="1"> <?php esc_html_e( 'حذف کلید ذخیره‌شده', 'webmz' ); ?></label></small>
							<?php endif; ?>
						</label>
						<label class="webmz-field webmz-field--wide">
							<span><?php esc_html_e( 'آدرس API (اختیاری)', 'webmz' ); ?></span>
							<input type="url" name="ai_chat[base_url]" value="<?php echo esc_attr( $ai_chat['base_url'] ?? '' ); ?>" dir="ltr" placeholder="https://api.avalai.ir/v1">
							<small><?php esc_html_e( 'خالی = آدرس پیش‌فرض سرویس انتخاب‌شده. اگر سرویس آدرس جدیدی اعلام کرد یا از سرویس سازگار دیگری استفاده می‌کنید، اینجا وارد کنید.', 'webmz' ); ?></small>
						</label>
					</div>
					<p style="margin-top:14px">
						<button type="button" class="button button-secondary" id="webmz-ai-chat-test"><?php esc_html_e( 'تست اتصال', 'webmz' ); ?></button>
						<span id="webmz-ai-chat-test-result" class="description" style="margin-inline-start:10px"></span>
					</p>
				</div>

				<div class="webmz-card">
					<h2><?php esc_html_e( 'دانش و رفتار دستیار', 'webmz' ); ?></h2>
					<label class="webmz-field webmz-field--wide">
						<span><?php esc_html_e( 'دستورالعمل دستیار', 'webmz' ); ?></span>
						<textarea name="ai_chat[system_prompt]" rows="4"><?php echo esc_textarea( $ai_chat['system_prompt'] ?? '' ); ?></textarea>
					</label>
					<label class="webmz-field webmz-field--wide" style="margin-top:14px">
						<span><?php esc_html_e( 'اطلاعات و پاسخ‌های پشتیبانی', 'webmz' ); ?></span>
						<textarea name="ai_chat[knowledge]" rows="10" placeholder="<?php esc_attr_e( "مثال:\nساعت پاسخ‌گویی پشتیبانی: شنبه تا چهارشنبه ۹ تا ۱۷\nبعد از خرید، دوره‌ها در حساب کاربری > دانلودها قرار می‌گیرند.\nامکان پرداخت اقساطی نداریم.", 'webmz' ); ?>"><?php echo esc_textarea( $ai_chat['knowledge'] ?? '' ); ?></textarea>
						<small><?php esc_html_e( 'هر چیزی که دستیار باید درباره سایت بداند: سوالات متداول، قوانین، روش خرید و دسترسی، راه‌های تماس. نام و آدرس سایت به‌صورت خودکار اضافه می‌شود.', 'webmz' ); ?></small>
					</label>
					<div class="webmz-grid webmz-grid--2" style="margin-top:14px">
						<label class="webmz-field webmz-field--checkbox">
							<input type="checkbox" name="ai_chat[include_products]" value="yes" <?php checked( $ai_chat['include_products'] ?? 'yes', 'yes' ); ?>>
							<span><?php esc_html_e( 'لیست محصولات/دوره‌ها (نام، قیمت، لینک) به دانش دستیار اضافه شود', 'webmz' ); ?></span>
						</label>
						<label class="webmz-field webmz-field--select">
							<span><?php esc_html_e( 'نمایش برای', 'webmz' ); ?></span>
							<select name="ai_chat[visibility]">
								<option value="all" <?php selected( $ai_chat['visibility'] ?? 'all', 'all' ); ?>><?php esc_html_e( 'همه بازدیدکنندگان', 'webmz' ); ?></option>
								<option value="logged_in" <?php selected( $ai_chat['visibility'] ?? 'all', 'logged_in' ); ?>><?php esc_html_e( 'فقط کاربران واردشده', 'webmz' ); ?></option>
							</select>
						</label>
					</div>
				</div>

				<div class="webmz-card">
					<h2><?php esc_html_e( 'ظاهر و متن‌ها', 'webmz' ); ?></h2>
					<div class="webmz-grid webmz-grid--3">
						<label class="webmz-field">
							<span><?php esc_html_e( 'نام دستیار', 'webmz' ); ?></span>
							<input type="text" name="ai_chat[bot_name]" value="<?php echo esc_attr( $ai_chat['bot_name'] ?? '' ); ?>">
						</label>
						<label class="webmz-field webmz-field--select">
							<span><?php esc_html_e( 'جایگاه دکمه', 'webmz' ); ?></span>
							<select name="ai_chat[position]">
								<option value="right" <?php selected( $ai_chat['position'] ?? 'right', 'right' ); ?>><?php esc_html_e( 'پایین راست', 'webmz' ); ?></option>
								<option value="left" <?php selected( $ai_chat['position'] ?? 'right', 'left' ); ?>><?php esc_html_e( 'پایین چپ', 'webmz' ); ?></option>
							</select>
						</label>
						<?php webmz_render_color_field( 'ai_chat[color]', $ai_chat['color'] ?? '#0878f9', esc_html__( 'رنگ اصلی', 'webmz' ) ); ?>
					</div>
					<p class="description"><?php esc_html_e( 'اگر دکمه تماس شناور هم فعال است، این دو را در دو سمت مختلف قرار دهید یا فاصله از پایین را تغییر دهید.', 'webmz' ); ?></p>
					<div class="webmz-grid webmz-grid--3" style="margin-top:14px">
						<label class="webmz-field">
							<span><?php esc_html_e( 'فاصله از کنار (پیکسل)', 'webmz' ); ?></span>
							<input type="number" name="ai_chat[offset_x]" value="<?php echo esc_attr( (string) ( $ai_chat['offset_x'] ?? 24 ) ); ?>" min="0" max="200">
						</label>
						<label class="webmz-field">
							<span><?php esc_html_e( 'فاصله از پایین (پیکسل)', 'webmz' ); ?></span>
							<input type="number" name="ai_chat[offset_bottom]" value="<?php echo esc_attr( (string) ( $ai_chat['offset_bottom'] ?? 24 ) ); ?>" min="0" max="300">
						</label>
						<label class="webmz-field">
							<span><?php esc_html_e( 'متن کادر پیام', 'webmz' ); ?></span>
							<input type="text" name="ai_chat[placeholder]" value="<?php echo esc_attr( $ai_chat['placeholder'] ?? '' ); ?>">
						</label>
					</div>
					<label class="webmz-field webmz-field--wide" style="margin-top:14px">
						<span><?php esc_html_e( 'پیام خوش‌آمد', 'webmz' ); ?></span>
						<textarea name="ai_chat[welcome]" rows="2"><?php echo esc_textarea( $ai_chat['welcome'] ?? '' ); ?></textarea>
					</label>
					<label class="webmz-field webmz-field--wide" style="margin-top:14px">
						<span><?php esc_html_e( 'سوالات پیشنهادی (هر خط یک سوال، حداکثر ۶)', 'webmz' ); ?></span>
						<textarea name="ai_chat[suggestions]" rows="4"><?php echo esc_textarea( $ai_chat['suggestions'] ?? '' ); ?></textarea>
					</label>
					<div class="webmz-grid webmz-grid--2" style="margin-top:14px">
						<label class="webmz-field">
							<span><?php esc_html_e( 'متن لینک پشتیبانی انسانی', 'webmz' ); ?></span>
							<input type="text" name="ai_chat[fallback_text]" value="<?php echo esc_attr( $ai_chat['fallback_text'] ?? '' ); ?>">
						</label>
						<label class="webmz-field">
							<span><?php esc_html_e( 'لینک پشتیبانی انسانی (تیکت، تماس، تلگرام...)', 'webmz' ); ?></span>
							<input type="url" name="ai_chat[fallback_url]" value="<?php echo esc_attr( $ai_chat['fallback_url'] ?? '' ); ?>" dir="ltr" placeholder="https://">
						</label>
					</div>
				</div>

				<div class="webmz-card">
					<h2><?php esc_html_e( 'محدودیت‌ها و هزینه', 'webmz' ); ?></h2>
					<div class="webmz-grid webmz-grid--3">
						<label class="webmz-field">
							<span><?php esc_html_e( 'حداکثر پیام هر کاربر در ساعت', 'webmz' ); ?></span>
							<input type="number" name="ai_chat[rate_limit_per_hour]" value="<?php echo esc_attr( (string) ( $ai_chat['rate_limit_per_hour'] ?? 20 ) ); ?>" min="1" max="500">
						</label>
						<label class="webmz-field">
							<span><?php esc_html_e( 'حداکثر طول پیام کاربر (کاراکتر)', 'webmz' ); ?></span>
							<input type="number" name="ai_chat[max_input_chars]" value="<?php echo esc_attr( (string) ( $ai_chat['max_input_chars'] ?? 600 ) ); ?>" min="100" max="4000">
						</label>
						<label class="webmz-field">
							<span><?php esc_html_e( 'تعداد پیام‌های قبلی ارسالی به هوش مصنوعی', 'webmz' ); ?></span>
							<input type="number" name="ai_chat[history_limit]" value="<?php echo esc_attr( (string) ( $ai_chat['history_limit'] ?? 10 ) ); ?>" min="2" max="30">
						</label>
					</div>
					<div class="webmz-grid webmz-grid--2" style="margin-top:14px">
						<label class="webmz-field">
							<span><?php esc_html_e( 'حداکثر طول پاسخ (توکن)', 'webmz' ); ?></span>
							<input type="number" name="ai_chat[max_tokens]" value="<?php echo esc_attr( (string) ( $ai_chat['max_tokens'] ?? 700 ) ); ?>" min="100" max="4000">
						</label>
						<label class="webmz-field">
							<span><?php esc_html_e( 'خلاقیت پاسخ (temperature، ۰ تا ۱.۵)', 'webmz' ); ?></span>
							<input type="number" name="ai_chat[temperature]" value="<?php echo esc_attr( (string) ( $ai_chat['temperature'] ?? 0.4 ) ); ?>" min="0" max="1.5" step="0.1">
						</label>
					</div>
					<p class="description"><?php esc_html_e( 'هر پیام کاربر یک درخواست به سرویس هوش مصنوعی است و از اعتبار حساب شما کم می‌کند. این محدودیت‌ها از سوءاستفاده و هزینه ناخواسته جلوگیری می‌کنند.', 'webmz' ); ?></p>
				</div>

				<script>
				(function ($) {
					$('#webmz-ai-chat-test').on('click', function () {
						var $btn = $(this);
						var $out = $('#webmz-ai-chat-test-result');
						var data = $('#webmz-options-form').serializeArray().filter(function (field) {
							return field.name.indexOf('ai_chat[') === 0;
						});
						data.push({ name: 'action', value: 'webmz_ai_chat_test' });
						data.push({ name: 'nonce', value: window.webmzAdmin ? window.webmzAdmin.nonce : '' });
						$btn.prop('disabled', true);
						$out.css('color', '').text('<?php echo esc_js( __( 'در حال ارسال پیام آزمایشی...', 'webmz' ) ); ?>');
						$.post(window.ajaxurl, data).done(function (res) {
							var msg = res && res.data && res.data.message ? res.data.message : '';
							$out.css('color', res && res.success ? '#15803d' : '#b91c1c').text(msg);
						}).fail(function () {
							$out.css('color', '#b91c1c').text('<?php echo esc_js( __( 'خطا در ارتباط با سرور سایت.', 'webmz' ) ); ?>');
						}).always(function () {
							$btn.prop('disabled', false);
						});
					});
				}(jQuery));
				</script>
			</section>

			<section class="webmz-panel" data-panel="tickets">
				<div class="webmz-card webmz-notice">
					<h2><?php esc_html_e( 'تنظیمات سیستم تیکت پشتیبانی', 'webmz' ); ?></h2>
					<p><?php esc_html_e( 'از این بخش می‌توانید تنظیمات تیکت‌های پشتیبانی ووکامرس، دپارتمان‌ها و اعلان‌ها را مدیریت کنید.', 'webmz' ); ?></p>
				</div>

				<div class="webmz-card">
					<h2><?php esc_html_e( 'وضعیت و دسترسی', 'webmz' ); ?></h2>
					<div class="webmz-grid webmz-grid--tickets">
						<label class="webmz-field webmz-field--checkbox">
							<input type="checkbox" name="ticket[enabled]" value="yes" <?php checked( $ticket_settings['enabled'], 'yes' ); ?>>
							<span><?php esc_html_e( 'سیستم تیکت فعال باشد', 'webmz' ); ?></span>
						</label>

						<label class="webmz-field webmz-field--checkbox">
							<input type="checkbox" name="ticket[require_purchase]" value="yes" <?php checked( $ticket_settings['require_purchase'], 'yes' ); ?>>
							<span><?php esc_html_e( 'فقط خریداران محصول/دوره بتوانند تیکت ثبت کنند', 'webmz' ); ?></span>
						</label>
					</div>
					<p class="description"><?php esc_html_e( 'اگر محدودیت خرید فعال باشد، کاربر هنگام ثبت تیکت باید یکی از محصولات خریداری‌شده خود را انتخاب کند.', 'webmz' ); ?></p>
				</div>

				<div class="webmz-card">
					<h2><?php esc_html_e( 'دپارتمان‌ها', 'webmz' ); ?></h2>
					<label class="webmz-field webmz-field--wide">
						<span><?php esc_html_e( 'لیست دپارتمان‌ها', 'webmz' ); ?></span>
						<textarea name="ticket[departments]" rows="8" placeholder="<?php esc_attr_e( 'هر دپارتمان را در یک خط وارد کنید.', 'webmz' ); ?>"><?php echo esc_textarea( $ticket_settings['departments'] ); ?></textarea>
					</label>
					<p class="description"><?php esc_html_e( 'مثال: پشتیبانی آموزشی، پشتیبانی فنی، مالی و سفارش‌ها', 'webmz' ); ?></p>
				</div>

				<div class="webmz-card">
					<h2><?php esc_html_e( 'اعلان‌ها', 'webmz' ); ?></h2>
					<div class="webmz-grid webmz-grid--tickets">
						<label class="webmz-field webmz-field--checkbox">
							<input type="checkbox" name="ticket[notify_user_email]" value="yes" <?php checked( $ticket_settings['notify_user_email'], 'yes' ); ?>>
							<span><?php esc_html_e( 'بعد از پاسخ مدیر، به کاربر ایمیل ارسال شود', 'webmz' ); ?></span>
						</label>

						<label class="webmz-field webmz-field--checkbox">
							<input type="checkbox" name="ticket[notify_admin_email]" value="yes" <?php checked( $ticket_settings['notify_admin_email'], 'yes' ); ?>>
							<span><?php esc_html_e( 'بعد از ثبت تیکت یا پاسخ کاربر، به مدیر ایمیل ارسال شود', 'webmz' ); ?></span>
						</label>
					</div>
				</div>

				<div class="webmz-card">
					<h2><?php esc_html_e( 'وضعیت سفارش‌های مجاز', 'webmz' ); ?></h2>
					<div class="webmz-grid webmz-grid--tickets">
						<?php foreach ( $ticket_order_statuses as $status_key => $status_label ) : ?>
							<?php $clean_status_key = str_replace( 'wc-', '', sanitize_key( $status_key ) ); ?>
							<label class="webmz-field webmz-field--checkbox">
								<input type="checkbox" name="ticket[allowed_order_statuses][]" value="<?php echo esc_attr( $clean_status_key ); ?>" <?php checked( in_array( $clean_status_key, (array) $ticket_settings['allowed_order_statuses'], true ) ); ?>>
								<span><?php echo esc_html( $status_label ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
					<p class="description"><?php esc_html_e( 'فقط سفارش‌هایی با این وضعیت‌ها برای اجازه ثبت تیکت بررسی می‌شوند.', 'webmz' ); ?></p>
				</div>

				<div class="webmz-card">
					<h2><?php esc_html_e( 'پیوست فایل تیکت', 'webmz' ); ?></h2>
					<div class="webmz-grid webmz-grid--tickets">
						<label class="webmz-field webmz-field--checkbox">
							<input type="checkbox" name="ticket[attachments_enabled]" value="yes" <?php checked( $ticket_settings['attachments_enabled'] ?? 'yes', 'yes' ); ?>>
							<span><?php esc_html_e( 'امکان پیوست فایل هنگام ثبت تیکت فعال باشد', 'webmz' ); ?></span>
						</label>

						<label class="webmz-field">
							<span><?php esc_html_e( 'حداکثر تعداد فایل', 'webmz' ); ?></span>
							<input type="number" name="ticket[attachments_max_files]" min="1" max="10" value="<?php echo esc_attr( $ticket_settings['attachments_max_files'] ?? 3 ); ?>">
						</label>

						<label class="webmz-field">
							<span><?php esc_html_e( 'حداکثر حجم هر فایل (کیلوبایت)', 'webmz' ); ?></span>
							<input type="number" name="ticket[attachments_max_size]" min="100" max="10240" value="<?php echo esc_attr( $ticket_settings['attachments_max_size'] ?? 2048 ); ?>">
						</label>

						<label class="webmz-field webmz-field--wide">
							<span><?php esc_html_e( 'پسوندهای مجاز', 'webmz' ); ?></span>
							<input type="text" name="ticket[attachments_extensions]" value="<?php echo esc_attr( $ticket_settings['attachments_extensions'] ?? 'jpg,jpeg,png,gif,webp,pdf' ); ?>" placeholder="jpg,jpeg,png,gif,webp,pdf">
						</label>
					</div>
					<p class="description"><?php esc_html_e( 'فایل‌ها با فایروال امنیتی بررسی می‌شوند. فقط تصویر و PDF پشتیبانی می‌شود. پسوندها را با ویرگول جدا کنید.', 'webmz' ); ?></p>
				</div>
			</section>

				</div>

				<footer class="webmz-savebar">
					<div class="webmz-savebar__info">
						<i data-lucide="info" class="webmz-savebar__icon" aria-hidden="true"></i>
						<span class="webmz-status" aria-live="polite"><?php esc_html_e( 'تغییرات را ذخیره کنید تا اعمال شوند.', 'webmz' ); ?></span>
					</div>
					<button type="submit" class="button button-primary button-hero webmz-savebar__submit">
						<i data-lucide="save" class="webmz-savebar__btn-icon" aria-hidden="true"></i>
						<?php esc_html_e( 'ذخیره تنظیمات', 'webmz' ); ?>
					</button>
				</footer>
			</div>
		</form>
	</div>
	<?php
}
