<?php
/**
 * WebMZ theme bootstrap.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WEBMZ_VERSION' ) ) {
	define( 'WEBMZ_VERSION', '1.0.0' );
}
if ( ! defined( 'WEBMZ_DIR' ) ) {
	define( 'WEBMZ_DIR', trailingslashit( get_template_directory() ) );
}
if ( ! defined( 'WEBMZ_URI' ) ) {
	define( 'WEBMZ_URI', trailingslashit( get_template_directory_uri() ) );
}

/**
 * Hosts that may use the theme without an RTL license (local + demo).
 *
 * Exact hosts only. Use webmz_is_license_whitelisted_host() for *.webmz.ir.
 *
 * @return array<int,string>
 */
function webmz_get_license_whitelist_hosts() {
	return array(
		'localhost',
		'127.0.0.1',
		'::1',
	);
}

/**
 * Whether the current request host is license-whitelisted.
 * Allows localhost hosts and any webmz.ir / *.webmz.ir domain.
 *
 * @return bool
 */
function webmz_is_license_whitelisted_host() {
	$host = '';

	if ( ! empty( $_SERVER['HTTP_HOST'] ) ) {
		$host = strtolower( (string) wp_unslash( $_SERVER['HTTP_HOST'] ) );
	} elseif ( function_exists( 'home_url' ) ) {
		$parsed = wp_parse_url( home_url() );
		$host   = isset( $parsed['host'] ) ? strtolower( (string) $parsed['host'] ) : '';
	}

	$host = preg_replace( '/:\d+$/', '', $host );
	if ( '' === $host ) {
		return false;
	}

	if ( in_array( $host, webmz_get_license_whitelist_hosts(), true ) ) {
		return true;
	}

	// Wildcard: webmz.ir and any subdomain (e.g. tadris.webmz.ir).
	if ( 'webmz.ir' === $host || substr( $host, -9 ) === '.webmz.ir' ) {
		return true;
	}

	return false;
}

/**
 * Whether the RTL Theme license is active for this product.
 *
 * @return bool
 */
function webmz_is_rtl_licensed() {
	return defined( 'WEBMZ_RTL_LICENSED' ) && true === WEBMZ_RTL_LICENSED;
}

/**
 * Mark the product as licensed and load pro feature files.
 *
 * @param array<int,string> $files Relative theme PHP paths.
 * @return void
 */
function webmz_enable_pro_features( $files ) {
	if ( ! defined( 'WEBMZ_RTL_LICENSED' ) ) {
		define( 'WEBMZ_RTL_LICENSED', true );
	}

	webmz_require_theme_files( $files );
}

/**
 * Require a list of theme PHP files relative to the theme root.
 *
 * @param array<int,string> $files Relative file paths.
 * @return void
 */
function webmz_require_theme_files( $files ) {
	foreach ( $files as $webmz_file ) {
		$webmz_path = WEBMZ_DIR . $webmz_file;
		if ( file_exists( $webmz_path ) ) {
			require_once $webmz_path;
		}
	}
}

/**
 * Front-end prompt when the theme license is inactive.
 * Site keeps rendering; only a floating notice is shown.
 *
 * @return void
 */
function webmz_rtl_license_inactive_frontend() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}

	$panel_url = admin_url();
	?>
	<style id="webmz-license-prompt-style">
		.webmz-license-prompt {
			position: fixed;
			inset-inline: 16px;
			bottom: 16px;
			z-index: 999999;
			max-width: 420px;
			margin-inline-start: auto;
			padding: 16px 18px;
			border: 1px solid #f1c40f;
			border-radius: 12px;
			background: #fff8e1;
			box-shadow: 0 12px 32px rgba(15, 23, 42, 0.18);
			color: #1f2937;
			font-family: Tahoma, "Segoe UI", Arial, sans-serif;
			direction: rtl;
			text-align: right;
		}
		.webmz-license-prompt__title {
			margin: 0 0 6px;
			font-size: 15px;
			font-weight: 700;
			line-height: 1.6;
		}
		.webmz-license-prompt__text {
			margin: 0 0 12px;
			font-size: 13px;
			line-height: 1.8;
			color: #4b5563;
		}
		.webmz-license-prompt__actions {
			display: flex;
			flex-wrap: wrap;
			gap: 8px;
			align-items: center;
		}
		.webmz-license-prompt__btn {
			display: inline-block;
			padding: 8px 14px;
			border-radius: 8px;
			background: #0f766e;
			color: #fff !important;
			text-decoration: none !important;
			font-size: 13px;
		}
		.webmz-license-prompt__btn:hover { background: #0d9488; }
		.webmz-license-prompt__close {
			appearance: none;
			border: 0;
			background: transparent;
			color: #6b7280;
			cursor: pointer;
			font-size: 13px;
			padding: 8px 6px;
		}
	</style>
	<div class="webmz-license-prompt" role="status" aria-live="polite" id="webmz-license-prompt">
		<p class="webmz-license-prompt__title"><?php echo esc_html__( 'پوسته آکام باید فعال شود.', 'webmz' ); ?></p>
		<p class="webmz-license-prompt__text"><?php echo esc_html__( 'لطفا لایسنس پوسته آکام را از پیشخوان وردپرس وارد کنید.', 'webmz' ); ?></p>
		<div class="webmz-license-prompt__actions">
			<a class="webmz-license-prompt__btn" href="<?php echo esc_url( $panel_url ); ?>"><?php echo esc_html__( 'فعال‌سازی از پنل', 'webmz' ); ?></a>
			<button type="button" class="webmz-license-prompt__close" data-webmz-license-close><?php echo esc_html__( 'بستن', 'webmz' ); ?></button>
		</div>
	</div>
	<script>
	(function () {
		var root = document.getElementById('webmz-license-prompt');
		if (!root) return;
		var closeBtn = root.querySelector('[data-webmz-license-close]');
		if (closeBtn) {
			closeBtn.addEventListener('click', function () {
				root.remove();
			});
		}
	})();
	</script>
	<?php
}

/**
 * Admin notice when the theme license is inactive.
 *
 * @return void
 */
function webmz_rtl_license_inactive_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="notice notice-error">
		<p><?php echo esc_html__( 'لطفا لایسنس پوسته آکام را از پنل وارد کنید.', 'webmz' ); ?></p>
	</div>
	<?php
}

$webmz_pro_includes = array(
	'inc/helpers.php',
	'inc/video-embeds.php',
	'inc/ajax-search.php',
	'inc/contact-form.php',
	'inc/newsletter.php',
	'inc/blog-archive-ajax.php',
	'inc/teachers-post-type.php',
	'inc/teachers-archive-ajax.php',
	'inc/stories-post-type.php',
	'inc/stories-ajax.php',
	'inc/store-archive-ajax.php',
	'inc/mobile-offcanvas.php',
	'inc/nav-menu-icons.php',
	'inc/mega-menu.php',
	'inc/floating-contact.php',
	'inc/setup.php',
	'inc/enqueue.php',
	'inc/layout-post-type.php',
	'inc/layout-renderer.php',
	'inc/layout-conditions.php',
	'inc/theme-options/admin-page.php',
	'inc/theme-options/ajax-handler.php',
	'inc/woocommerce-account-panel.php',
	'inc/compatibility/elementor.php',
	'inc/compatibility/woocommerce.php',
	'inc/compatibility/woocommerce-elementor-hooks.php',
	'inc/compatibility/woocommerce-product-loop.php',
	'inc/woocommerce-cart-checkout.php',
	'inc/compatibility/tadris-content.php',
	'inc/compatibility/single-product-meta.php',
	'inc/compatibility/single-product-curriculum.php',
	'inc/product-loop-thumb-meta.php',
	'inc/product-preview-meta.php',
	'inc/zhaket-product-loop.php',
	'inc/zhaket-product-tabs.php',
	'inc/zhaket-top-developer.php',
	'inc/file-loop-ajax.php',
	'inc/tabbed-product-loop-ajax.php',
	'inc/post-downloads.php',
	'inc/ticket-attachments.php',
	'inc/support-tickets.php',
	'inc/view-history.php',
	'inc/youtube-playlist-ajax.php',
	'inc/comments-widget.php',
	'inc/podcast-player-ajax.php',
	'inc/special-offer-slider-ajax.php',
	'inc/woocommerce-reviews-widget.php',
	'inc/local-gravatar.php',
	'inc/favicon.php',
	'inc/otp-auth.php',
	'inc/blocks/related-post.php',
);

// --------------------------------------------------------------------------------------------------- Start RTL License
if ( webmz_is_license_whitelisted_host() ) {
	// Product is Active Now, Enable Pro Features (demo / local whitelist)
	if ( ! defined( 'WEBMZ_RTL_LICENSED' ) ) {
		define( 'WEBMZ_RTL_LICENSED', true );
	}
} else {
	$rtlLicenseClassName = 'RTL_License_1caedf22ac6ba764';
	$rtlLicenseFilePath  = __DIR__ . DIRECTORY_SEPARATOR . $rtlLicenseClassName . '.php';
	$rtlLicenseFileHash  = @sha1_file( $rtlLicenseFilePath );

	if ( $rtlLicenseFileHash === 'f6835f248891b09ffe132044865d2f7d3b0970af' && file_exists( $rtlLicenseFilePath ) ) {
		require_once $rtlLicenseFilePath;

		if ( class_exists( $rtlLicenseClassName ) && method_exists( $rtlLicenseClassName, 'isActive' ) ) {
			$rtlLicenseClass = new $rtlLicenseClassName();

			if ( $rtlLicenseClass->{'isActive'}() === true ) {
				// Product is Active Now, Enable Pro Features
				if ( ! defined( 'WEBMZ_RTL_LICENSED' ) ) {
					define( 'WEBMZ_RTL_LICENSED', true );
				}
			}
		}
	}
}
// ----------------------------------------------------------------------------------------------------- End RTL License

// Front-end and core features always load. Theme options stay gated by WEBMZ_RTL_LICENSED.
webmz_require_theme_files( $webmz_pro_includes );

if ( ! webmz_is_rtl_licensed() ) {
	add_action( 'wp_footer', 'webmz_rtl_license_inactive_frontend', 99 );
	add_action( 'admin_notices', 'webmz_rtl_license_inactive_admin_notice' );
}
