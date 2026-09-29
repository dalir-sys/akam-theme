<?php
/**
 * Public asset loading.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Tadris Elementor animation assets early.
 *
 * @return void
 */
function webmz_register_tadris_elementor_animation_assets() {
	if ( wp_style_is( 'webmz-tadris-elementor-animations', 'registered' ) ) {
		return;
	}

	wp_register_style(
		'webmz-tadris-elementor-animations',
		WEBMZ_URI . 'assets/css/tadris-elementor-animations.css',
		array(),
		WEBMZ_VERSION
	);
	wp_register_script(
		'webmz-vanilla-tilt',
		WEBMZ_URI . 'assets/js/vanilla-tilt.min.js',
		array(),
		WEBMZ_VERSION,
		true
	);

	$script_deps = array( 'jquery', 'webmz-vanilla-tilt' );

	if ( wp_script_is( 'elementor-frontend', 'registered' ) ) {
		$script_deps[] = 'elementor-frontend';
	}

	wp_register_script(
		'webmz-tadris-elementor-animations',
		WEBMZ_URI . 'assets/js/tadris-elementor-animations.js',
		$script_deps,
		WEBMZ_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'webmz_register_tadris_elementor_animation_assets', 5 );
add_action( 'elementor/frontend/after_register_scripts', 'webmz_register_tadris_elementor_animation_assets', 5 );

/**
 * Enqueue Tadris Elementor animation assets.
 *
 * @return void
 */
function webmz_enqueue_tadris_elementor_animation_assets() {
	webmz_register_tadris_elementor_animation_assets();

	if ( wp_script_is( 'elementor-frontend', 'registered' ) ) {
		$wp_scripts = wp_scripts();

		if ( isset( $wp_scripts->registered['webmz-tadris-elementor-animations'] ) ) {
			$deps = &$wp_scripts->registered['webmz-tadris-elementor-animations']->deps;

			if ( ! in_array( 'elementor-frontend', $deps, true ) ) {
				$deps[] = 'elementor-frontend';
			}
		}
	}

	wp_enqueue_style( 'webmz-tadris-elementor-animations' );
	wp_enqueue_script( 'webmz-vanilla-tilt' );
	wp_enqueue_script( 'webmz-tadris-elementor-animations' );
}

/**
 * Enqueue theme public assets and register optional vendor assets.
 *
 * @return void
 */
function webmz_enqueue_assets() {
	$options = webmz_get_options();
	$fonts   = webmz_get_local_fonts();
	$font    = isset( $fonts[ $options['font_family'] ] ) ? $fonts[ $options['font_family'] ] : $fonts['system'];

	wp_enqueue_style( 'webmz-style', get_template_directory_uri() . '/style.css', array(), WEBMZ_VERSION );
	wp_enqueue_style( 'webmz-main', WEBMZ_URI . 'assets/css/main.css', array( 'webmz-style' ), WEBMZ_VERSION );
	wp_enqueue_style( 'webmz-theme-loop-images', WEBMZ_URI . 'assets/css/theme-loop-images.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_enqueue_style( 'webmz-sticky-elements', WEBMZ_URI . 'assets/css/sticky-elements.css', array( 'webmz-main' ), WEBMZ_VERSION );
	webmz_enqueue_tadris_elementor_animation_assets();
	if ( is_rtl() ) {
		wp_enqueue_style( 'webmz-rtl', WEBMZ_URI . 'rtl.css', array( 'webmz-main' ), WEBMZ_VERSION );
	}
	if ( ! empty( $font['css'] ) && file_exists( WEBMZ_DIR . $font['css'] ) ) {
		wp_enqueue_style( 'webmz-font', WEBMZ_URI . $font['css'], array( 'webmz-main' ), WEBMZ_VERSION );
	}
	if ( get_stylesheet_directory() !== get_template_directory() ) {
		wp_enqueue_style( 'webmz-child-style', get_stylesheet_uri(), array( 'webmz-main' ), WEBMZ_VERSION );
	}

	$css = ':root{' .
		'--webmz-color-primary:' . esc_attr( $options['color_primary'] ) . ';' .
		'--webmz-color-secondary:' . esc_attr( $options['color_secondary'] ) . ';' .
		'--webmz-color-primary-hover:' . esc_attr( $options['color_primary_hover'] ) . ';' .
		'--webmz-color-primary-light:' . esc_attr( $options['color_primary_light'] ) . ';' .
		'--webmz-wc-primary:' . esc_attr( $options['color_primary'] ) . ';' .
		'--webmz-wc-primary-hover:' . esc_attr( $options['color_primary_hover'] ) . ';' .
		'--webmz-wc-primary-light:' . esc_attr( $options['color_primary_light'] ) . ';' .
		'--webmz-account-accent-light:' . esc_attr( isset( $options['account_accent_light'] ) ? $options['account_accent_light'] : '#fff4e7' ) . ';' .
		'--webmz-color-text-dark:' . esc_attr( $options['color_text_dark'] ) . ';' .
		'--webmz-color-text-gray:' . esc_attr( $options['color_text_gray'] ) . ';' .
		'--webmz-color-text-navy:' . esc_attr( $options['color_text_navy'] ) . ';' .
		'--webmz-bg:' . esc_attr( $options['color_background'] ) . ';' .
		'--webmz-container:' . absint( $options['container_width'] ) . 'px;' .
		'--webmz-font-family:' . $font['stack'] . ';' .
		webmz_get_related_box_css_vars_declaration() .
	'}';
	wp_add_inline_style( 'webmz-main', $css );


	$otp_css_path = WEBMZ_DIR . 'assets/css/otp-auth.css';
	$otp_js_path  = WEBMZ_DIR . 'assets/js/otp-auth.js';
	$otp_css_ver  = file_exists( $otp_css_path ) ? (string) filemtime( $otp_css_path ) : WEBMZ_VERSION;
	$otp_js_ver   = file_exists( $otp_js_path ) ? (string) filemtime( $otp_js_path ) : WEBMZ_VERSION;

	wp_register_style( 'webmz-otp-auth', WEBMZ_URI . 'assets/css/otp-auth.css', array( 'webmz-main' ), $otp_css_ver );
	wp_register_script( 'webmz-otp-auth', WEBMZ_URI . 'assets/js/otp-auth.js', array(), $otp_js_ver, true );
	$otp_firewall = function_exists( 'webmz_otp_firewall_issue_token' ) ? webmz_otp_firewall_issue_token() : array( 'token' => '', 'issued' => 0 );
	wp_localize_script(
		'webmz-otp-auth',
		'webmzOtpAuth',
		array(
			'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
			'nonce'          => wp_create_nonce( 'webmz_otp_auth' ),
			'firewallToken'  => $otp_firewall['token'],
			'firewallIssued' => $otp_firewall['issued'],
			'resendSeconds'  => function_exists( 'webmz_otp_get_settings' ) ? absint( webmz_otp_get_settings()['resend_seconds'] ) : 60,
			'sending'        => esc_html__( 'در حال ارسال کد...', 'tadris' ),
			'sent'           => esc_html__( 'کد تأیید ارسال شد.', 'tadris' ),
			'verifying'      => esc_html__( 'در حال بررسی کد...', 'tadris' ),
			'loggedIn'       => esc_html__( 'ورود با موفقیت انجام شد.', 'tadris' ),
			'error'          => esc_html__( 'خطایی رخ داد. دوباره تلاش کنید.', 'tadris' ),
			'invalidMobile'  => esc_html__( 'شماره موبایل را به‌درستی وارد کنید.', 'tadris' ),
			'loginHint'      => esc_html__( 'کد ورود برای شماره شما ارسال شد.', 'tadris' ),
			'registerHint'   => esc_html__( 'برای این شماره حسابی وجود ندارد؛ بعد از تأیید، ثبت‌نام انجام می‌شود.', 'tadris' ),
			'resendText'     => esc_html__( 'ارسال مجدد کد', 'tadris' ),
			'resendWaitText' => esc_html__( 'ارسال مجدد تا {s} ثانیه', 'tadris' ),
		)
	);
	if ( function_exists( 'webmz_otp_is_enabled' ) && webmz_otp_is_enabled() ) {
		wp_enqueue_style( 'webmz-otp-auth' );
		wp_enqueue_script( 'webmz-otp-auth' );
	}

	wp_register_style( 'webmz-plyr', WEBMZ_URI . 'assets/css/plyr.css', array(), WEBMZ_VERSION );
	wp_register_style( 'webmz-plyr-widgets', WEBMZ_URI . 'assets/css/plyr-widgets.css', array( 'webmz-plyr' ), WEBMZ_VERSION );
	wp_add_inline_style(
	'webmz-plyr',
	':root{--plyr-color-main:var(--webmz-color-primary,#0878f9);}
	.plyr{--plyr-color-main:var(--webmz-color-primary,#0878f9);}
	.plyr__control--overlaid{background:var(--webmz-color-primary,#0878f9);}
	.plyr--full-ui input[type=range]{color:var(--webmz-color-primary,#0878f9);}'
);
	wp_register_style( 'webmz-swiper', WEBMZ_URI . 'assets/css/swiper-bundle.min.css', array(), WEBMZ_VERSION );
	wp_register_script( 'webmz-plyr', WEBMZ_URI . 'assets/js/plyr.js', array(), WEBMZ_VERSION, true );
	wp_register_script( 'webmz-view-history', WEBMZ_URI . 'assets/js/view-history.js', array(), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-comments-widget', WEBMZ_URI . 'assets/css/comments-widget.css', array(), WEBMZ_VERSION );
	wp_register_script( 'webmz-comments-widget', WEBMZ_URI . 'assets/js/comments-widget.js', array(), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-comments-widget',
		'webmzCommentsWidget',
		array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'webmz_comments_widget' ),
			'sending'    => esc_html__( 'در حال ارسال دیدگاه...', 'tadris' ),
			'sent'       => esc_html__( 'دیدگاه شما ثبت شد.', 'tadris' ),
			'error'      => esc_html__( 'خطایی رخ داد. دوباره تلاش کنید.', 'tadris' ),
			'replyingTo' => esc_html__( 'در حال پاسخ به', 'tadris' ),
		)
	);
	wp_register_style( 'webmz-wc-reviews-widget', WEBMZ_URI . 'assets/css/woocommerce-reviews-widget.css', array( 'webmz-comments-widget' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-wc-reviews-widget', WEBMZ_URI . 'assets/js/woocommerce-reviews-widget.js', array(), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-wc-reviews-widget',
		'webmzWcReviewsWidget',
		array(
			'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
			'nonce'           => wp_create_nonce( 'webmz_wc_reviews_widget' ),
			'sending'         => esc_html__( 'در حال ارسال نظر...', 'tadris' ),
			'sent'            => esc_html__( 'نظر شما ثبت شد.', 'tadris' ),
			'error'           => esc_html__( 'خطایی رخ داد. دوباره تلاش کنید.', 'tadris' ),
			'replyingTo'      => esc_html__( 'در حال پاسخ به', 'tadris' ),
			'ratingRequired'  => esc_html__( 'لطفاً امتیاز خود را انتخاب کنید.', 'tadris' ),
		)
	);
	wp_register_style(
		'webmz-sweetalert',
		WEBMZ_URI . 'assets/css/sweetalert-webmz.css',
		array(),
		WEBMZ_VERSION
	);
	wp_enqueue_style( 'webmz-sweetalert' );
	wp_register_script(
		'webmz-sweetalert',
		WEBMZ_URI . 'assets/js/sweetalert2.js',
		array(),
		WEBMZ_VERSION,
		true
	);
	wp_register_script( 'webmz-swiper', WEBMZ_URI . 'assets/js/swiper-bundle.min.js', array(), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-contact-widgets', WEBMZ_URI . 'assets/css/contact-widgets.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-contact-widgets', WEBMZ_URI . 'assets/js/contact-widgets.js', array( 'webmz-swiper' ), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-slider-element-1', WEBMZ_URI . 'assets/css/slider-element-1-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-slider-element-1', WEBMZ_URI . 'assets/js/slider-element-1-widget.js', array( 'webmz-swiper' ), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-customer-testimonials-slider', WEBMZ_URI . 'assets/css/customer-testimonials-slider-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-customer-testimonials-slider', WEBMZ_URI . 'assets/js/customer-testimonials-slider-widget.js', array( 'webmz-swiper' ), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-customer-testimonials-slider-2', WEBMZ_URI . 'assets/css/customer-testimonials-slider-2-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-customer-testimonials-slider-2', WEBMZ_URI . 'assets/js/customer-testimonials-slider-2-widget.js', array( 'webmz-swiper' ), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-podcast-player', WEBMZ_URI . 'assets/css/podcast-player-widget.css', array( 'webmz-main', 'webmz-plyr-widgets' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-podcast-player', WEBMZ_URI . 'assets/js/podcast-player-widget.js', array( 'jquery', 'webmz-plyr', 'webmz-tadris-widgets' ), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-podcast-player',
		'webmzPodcastPlayer',
		array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'webmz_podcast_player' ),
			'errorText' => esc_html__( 'خطایی رخ داد. دوباره تلاش کنید.', 'tadris' ),
			'loadingText' => esc_html__( 'در حال بارگذاری پادکست...', 'tadris' ),
		)
	);
	wp_register_style( 'webmz-coupon-code', WEBMZ_URI . 'assets/css/coupon-code-widget.css', array(), WEBMZ_VERSION );
	wp_register_script( 'webmz-coupon-code', WEBMZ_URI . 'assets/js/coupon-code-widget.js', array(), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-youtube-playlist', WEBMZ_URI . 'assets/css/youtube-playlist-widget.css', array( 'webmz-main', 'webmz-plyr-widgets' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-youtube-playlist', WEBMZ_URI . 'assets/js/youtube-playlist-widget.js', array( 'jquery', 'webmz-plyr', 'webmz-tadris-widgets' ), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-youtube-playlist',
		'webmzYoutubePlaylist',
		array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'webmz_youtube_playlist' ),
			'errorText' => esc_html__( 'خطایی رخ داد. دوباره تلاش کنید.', 'tadris' ),
		)
	);
	wp_register_style( 'webmz-tadris-heading-widgets', WEBMZ_URI . 'assets/css/tadris-heading-widgets.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-tadris-heading-animation', WEBMZ_URI . 'assets/css/tadris-heading-animation-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-tadris-heading-animation', WEBMZ_URI . 'assets/js/tadris-heading-animation-widget.js', array(), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-tadris-advanced-button', WEBMZ_URI . 'assets/css/tadris-advanced-button-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-service-box', WEBMZ_URI . 'assets/css/service-box-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-course-intro-banner', WEBMZ_URI . 'assets/css/course-intro-banner-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-special-offer-slider', WEBMZ_URI . 'assets/css/special-offer-slider-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-special-offer-slider', WEBMZ_URI . 'assets/js/special-offer-slider-widget.js', array( 'webmz-swiper', 'webmz-plyr' ), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-special-offer-slider',
		'webmzSpecialOfferSlider',
		array(
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'nonce'        => wp_create_nonce( 'webmz_sos_intro_video' ),
			'plyrIconUrl'  => WEBMZ_URI . 'assets/images/plyr.svg',
			'closeLabel'   => esc_html__( 'بستن', 'tadris' ),
			'videoLabel'   => esc_html__( 'ویدیو معرفی محصول', 'tadris' ),
			'loadingLabel' => esc_html__( 'در حال بارگذاری...', 'tadris' ),
			'errorLabel'   => esc_html__( 'خطا در بارگذاری ویدیو.', 'tadris' ),
		)
	);
	wp_register_style( 'webmz-course-category', WEBMZ_URI . 'assets/css/course-category-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-contact-us-banner', WEBMZ_URI . 'assets/css/contact-us-banner-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-footer-contact', WEBMZ_URI . 'assets/css/footer-contact-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-icon-details-box', WEBMZ_URI . 'assets/css/icon-details-box-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-zhaket-icon-list', WEBMZ_URI . 'assets/css/zhaket-icon-list-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-zhaket-why-buy-footer', WEBMZ_URI . 'assets/css/zhaket-why-buy-footer-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-zhaket-special-banners', WEBMZ_URI . 'assets/css/zhaket-special-banners-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-zhaket-special-banners', WEBMZ_URI . 'assets/js/zhaket-special-banners-widget.js', array( 'webmz-swiper' ), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-zhaket-heading', WEBMZ_URI . 'assets/css/zhaket-heading-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-zhaket-footer-newsletter', WEBMZ_URI . 'assets/css/zhaket-footer-newsletter-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-zhaket-blog-loop', WEBMZ_URI . 'assets/css/zhaket-blog-loop-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-zhaket-blog-loop', WEBMZ_URI . 'assets/js/zhaket-blog-loop-widget.js', array( 'webmz-swiper' ), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-back-to-top-2', WEBMZ_URI . 'assets/css/back-to-top-2-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-shape', WEBMZ_URI . 'assets/css/shape-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-light-source', WEBMZ_URI . 'assets/css/light-source-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-learning-path', WEBMZ_URI . 'assets/css/learning-path-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-membership-pricing', WEBMZ_URI . 'assets/css/membership-pricing-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-animated-hero-section', WEBMZ_URI . 'assets/css/animated-hero-section-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-animated-hero-section', WEBMZ_URI . 'assets/js/animated-hero-section-widget.js', array( 'jquery' ), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-footer-menu', WEBMZ_URI . 'assets/css/footer-menu-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-footer-links', WEBMZ_URI . 'assets/css/footer-links-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-footer-ticket-box', WEBMZ_URI . 'assets/css/footer-ticket-box-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-service-box', WEBMZ_URI . 'assets/js/service-box-widget.js', array(), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-tadris-product-loop-2', WEBMZ_URI . 'assets/css/tadris-product-loop-2-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-tadris-product-loop-3', WEBMZ_URI . 'assets/css/tadris-product-loop-3-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-tabbed-product-loop', WEBMZ_URI . 'assets/css/tadris-tabbed-product-loop-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-tabbed-product-loop', WEBMZ_URI . 'assets/js/tadris-tabbed-product-loop-widget.js', array( 'webmz-tadris-widgets' ), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-tabbed-product-loop',
		'webmzTabbedProductLoop',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'webmz_tabbed_product_loop' ),
			'loadingText' => esc_html__( 'در حال بارگذاری...', 'tadris' ),
			'emptyText'   => esc_html__( 'محصولی برای نمایش یافت نشد.', 'tadris' ),
			'errorText'   => esc_html__( 'بارگذاری محصولات ناموفق بود.', 'tadris' ),
		)
	);
	wp_register_style( 'webmz-zhaaket-top-products-box', WEBMZ_URI . 'assets/css/zhaaket-top-products-box.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-zhaket-bestsellers-column', WEBMZ_URI . 'assets/css/zhaket-bestsellers-column-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-zhaket-vertical-slider', WEBMZ_URI . 'assets/css/zhaket-vertical-slider-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-zhaket-vertical-slider', WEBMZ_URI . 'assets/js/zhaket-vertical-slider-widget.js', array( 'webmz-swiper' ), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-zhaket-top-developer', WEBMZ_URI . 'assets/css/zhaket-top-developer-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-zhaket-top-developer', WEBMZ_URI . 'assets/js/zhaket-top-developer-widget.js', array( 'webmz-swiper' ), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-zhaket-product-tabs', WEBMZ_URI . 'assets/css/zhaket-product-tabs-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-zhaket-product-tabs', WEBMZ_URI . 'assets/js/zhaket-product-tabs-widget.js', array( 'jquery', 'webmz-swiper', 'webmz-tadris-widgets', 'webmz-header-commerce' ), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-zhaket-product-tabs',
		'webmzZhaketProductTabs',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'webmz_zhaket_product_tabs' ),
			'loadingText' => esc_html__( 'در حال بارگذاری...', 'tadris' ),
			'emptyText'   => esc_html__( 'محصولی برای نمایش یافت نشد.', 'tadris' ),
			'errorText'   => esc_html__( 'بارگذاری محصولات ناموفق بود.', 'tadris' ),
		)
	);
	wp_register_style( 'webmz-file-loop', WEBMZ_URI . 'assets/css/file-loop-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-file-loop', WEBMZ_URI . 'assets/js/file-loop-widget.js', array( 'webmz-swiper' ), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-zhaket-product-loop', WEBMZ_URI . 'assets/css/zhaket-product-loop-widget.css', array( 'webmz-main', 'webmz-swiper', 'webmz-zhaket-heading' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-zhaket-product-loop', WEBMZ_URI . 'assets/js/zhaket-product-loop-widget.js', array( 'jquery', 'webmz-swiper' ), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-file-loop',
		'webmzFileLoop',
		array(
			'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
			'nonce'            => wp_create_nonce( 'webmz_file_loop' ),
			'errorText'        => esc_html__( 'بارگذاری اطلاعات محصول ناموفق بود.', 'tadris' ),
			'loadingText'      => esc_html__( 'در حال بارگذاری...', 'tadris' ),
			'closeLabel'       => esc_html__( 'بستن', 'tadris' ),
			'viewProductLabel' => esc_html__( 'مشاهده محصول', 'tadris' ),
			'popupTitle'       => esc_html__( 'جزئیات محصول', 'tadris' ),
		)
	);
	wp_register_style( 'webmz-tadris-blog-loop-2', WEBMZ_URI . 'assets/css/tadris-blog-loop-2-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-tadris-blog-tile-loop', WEBMZ_URI . 'assets/css/tadris-blog-tile-loop-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-stories', WEBMZ_URI . 'assets/css/stories-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-blog-cta-widgets', WEBMZ_URI . 'assets/css/blog-cta-widgets.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-blog-cta-widgets', WEBMZ_URI . 'assets/js/blog-cta-widgets.js', array( 'webmz-sweetalert' ), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-blog-cta-widgets',
		'webmzBlogCtaWidgets',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'sendingText' => esc_html__( 'در حال ارسال...', 'tadris' ),
			'errorText'   => esc_html__( 'خطایی رخ داد. دوباره تلاش کنید.', 'tadris' ),
			'confirmText' => esc_html__( 'متوجه شدم', 'tadris' ),
		)
	);
	wp_register_script( 'webmz-stories', WEBMZ_URI . 'assets/js/stories-widget.js', array( 'webmz-swiper' ), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-stories',
		'webmzStories',
		array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'webmz_stories' ),
			'errorText' => esc_html__( 'بارگذاری استوری‌ها ناموفق بود.', 'tadris' ),
		)
	);
	wp_localize_script(
		'webmz-contact-widgets',
		'webmzContactWidgets',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'sendingText' => esc_html__( 'در حال ارسال درخواست...', 'tadris' ),
			'successText' => esc_html__( 'درخواست شما با موفقیت ارسال شد.', 'tadris' ),
			'errorText'   => esc_html__( 'خطایی رخ داد. دوباره تلاش کنید.', 'tadris' ),
		)
	);
	wp_register_style( 'webmz-ajax-search', WEBMZ_URI . 'assets/css/ajax-search.css', array( 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-ajax-search-popup', WEBMZ_URI . 'assets/css/ajax-search-popup.css', array( 'webmz-ajax-search' ), WEBMZ_VERSION );
	wp_add_inline_style(
		'webmz-main',
		'.webmz-search-popup__trigger{display:flex;align-items:center;justify-content:space-between;gap:12px;width:100%;min-height:48px;padding:10px 16px 10px 20px;border:1px solid #e8e8e8;border-radius:999px;background:#fff;color:#6b7280;font:inherit;font-size:14px;line-height:1.5;text-align:start;cursor:pointer}'
		. '.webmz-search-popup--boot .webmz-search-popup__overlay:not(.is-open):not(.is-closing){display:none!important;position:fixed!important;inset:0!important;width:0!important;height:0!important;overflow:hidden!important;visibility:hidden!important;pointer-events:none!important;z-index:-1!important;opacity:0!important}'
	);
	wp_register_style( 'webmz-site-logo', WEBMZ_URI . 'assets/css/site-logo-widget.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-header-commerce', WEBMZ_URI . 'assets/css/header-commerce-widgets.css', array(), WEBMZ_VERSION );
	wp_add_inline_style(
		'webmz-header-commerce',
		'.webmz-hcart__empty{display:none!important}.webmz-offcanvas.is-open .webmz-hcart__empty,.webmz-hcart.is-open .webmz-hcart__empty{display:flex!important}'
	);
	wp_register_style( 'webmz-navigation-menu', WEBMZ_URI . 'assets/css/navigation-widget.css', array(), WEBMZ_VERSION );
	wp_add_inline_style(
		'webmz-navigation-menu',
		'.webmz-nav__mega-menu{display:none}.elementor-editor-active .webmz-nav__mega-menu,.elementor-editor-preview .webmz-nav__mega-menu,.elementor-edit-mode .webmz-nav__mega-menu{display:block}'
	);
	wp_register_style( 'webmz-icon-navigation-menu', WEBMZ_URI . 'assets/css/icon-navigation-widget.css', array( 'webmz-navigation-menu' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-mega-menu-content', WEBMZ_URI . 'assets/css/mega-menu-content.css', array( 'webmz-navigation-menu' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-single-product-webmasters', WEBMZ_URI . 'assets/css/single-product-webmasters.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-mobile-offcanvas', WEBMZ_URI . 'assets/css/mobile-offcanvas.css', array(), WEBMZ_VERSION );
	wp_add_inline_style(
		'webmz-mobile-offcanvas',
		'.webmz-offcanvas--boot:not(.is-open){position:fixed!important;inset:0!important;width:0!important;height:0!important;overflow:hidden!important;visibility:hidden!important;pointer-events:none!important;z-index:-1!important}'
	);
	wp_register_script( 'webmz-ajax-search', WEBMZ_URI . 'assets/js/ajax-search.js', array( 'webmz-swiper' ), WEBMZ_VERSION, true );
	wp_localize_script( 'webmz-ajax-search', 'webmzAjaxSearch', array(
		'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
		'nonce'     => wp_create_nonce( 'webmz_ajax_search' ),
		'searchUrl' => home_url( '/' ),
	) );
	wp_register_script( 'webmz-ajax-search-popup', WEBMZ_URI . 'assets/js/ajax-search-popup.js', array( 'webmz-ajax-search' ), WEBMZ_VERSION, true );
	wp_register_script( 'webmz-header-commerce', WEBMZ_URI . 'assets/js/header-commerce-widgets.js', array( 'jquery' ), WEBMZ_VERSION, true );
	wp_register_script( 'webmz-navigation-menu', WEBMZ_URI . 'assets/js/navigation-widget.js', array(), WEBMZ_VERSION, true );
	wp_register_script( 'webmz-mega-menu-content', WEBMZ_URI . 'assets/js/mega-menu-content.js', array(), WEBMZ_VERSION, true );
	wp_register_script( 'webmz-single-product-webmasters', WEBMZ_URI . 'assets/js/single-product-webmasters.js', array( 'jquery', 'webmz-swiper', 'webmz-tadris-widgets' ), WEBMZ_VERSION, true );
	wp_register_script( 'webmz-mobile-offcanvas', WEBMZ_URI . 'assets/js/mobile-offcanvas.js', array(), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-mobile-offcanvas',
		'webmzMobileSearch',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'webmz_ajax_search' ),
		)
	);
	wp_register_script(
		'webmz-tadris-widgets',
		WEBMZ_URI . 'assets/js/tadris-widgets.js',
		array( 'jquery', 'webmz-sweetalert' ),
		WEBMZ_VERSION,
		true
	);
	wp_register_script( 'webmz-post-toc', WEBMZ_URI . 'assets/js/post-toc.js', array(), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-blog-archive', WEBMZ_URI . 'assets/css/blog-archive.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-blog-archive', WEBMZ_URI . 'assets/js/blog-archive.js', array(), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-teachers', WEBMZ_URI . 'assets/css/teachers.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_style( 'webmz-professional-teachers', WEBMZ_URI . 'assets/css/professional-teachers-widget.css', array( 'webmz-main', 'webmz-swiper', 'webmz-plyr-widgets' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-professional-teachers', WEBMZ_URI . 'assets/js/professional-teachers-widget.js', array( 'webmz-swiper', 'webmz-plyr' ), WEBMZ_VERSION, true );
	wp_register_style( 'webmz-instructor-slider', WEBMZ_URI . 'assets/css/instructor-slider-widget.css', array( 'webmz-main', 'webmz-swiper' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-instructor-slider', WEBMZ_URI . 'assets/js/instructor-slider-widget.js', array( 'webmz-swiper' ), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-professional-teachers',
		'webmzProfessionalTeachers',
		array(
			'plyrIconUrl' => WEBMZ_URI . 'assets/images/plyr.svg',
			'closeLabel'  => esc_html__( 'بستن', 'tadris' ),
			'videoLabel'  => esc_html__( 'ویدیو معرفی مدرس', 'tadris' ),
		)
	);
	wp_register_script( 'webmz-teachers-archive', WEBMZ_URI . 'assets/js/teachers-archive.js', array(), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-teachers-archive',
		'webmzTeachersArchive',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'webmz_teachers_archive' ),
			'loadingText' => esc_html__( 'در حال بارگذاری مدرسین...', 'tadris' ),
			'emptyText'   => esc_html__( 'مدرسی یافت نشد.', 'tadris' ),
			'errorText'   => esc_html__( 'خطایی رخ داد. دوباره تلاش کنید.', 'tadris' ),
		)
	);
	wp_register_style( 'webmz-floating-contact', WEBMZ_URI . 'assets/css/floating-contact.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-floating-contact', WEBMZ_URI . 'assets/js/floating-contact.js', array(), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-blog-archive',
		'webmzBlogArchive',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'webmz_blog_archive' ),
			'loadingText' => esc_html__( 'در حال بارگذاری مطالب...', 'tadris' ),
			'emptyText'   => esc_html__( 'مطلبی یافت نشد.', 'tadris' ),
			'errorText'   => esc_html__( 'خطایی رخ داد. دوباره تلاش کنید.', 'tadris' ),
		)
	);
	wp_register_style( 'webmz-store-archive', WEBMZ_URI . 'assets/css/store-archive.css', array( 'webmz-main' ), WEBMZ_VERSION );
	wp_register_script( 'webmz-store-archive', WEBMZ_URI . 'assets/js/store-archive.js', array(), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-store-archive',
		'webmzStoreArchive',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'webmz_store_archive' ),
			'loadingText' => esc_html__( 'در حال بارگذاری محصولات...', 'tadris' ),
			'emptyText'   => esc_html__( 'محصولی یافت نشد.', 'tadris' ),
			'errorText'   => esc_html__( 'خطایی رخ داد. دوباره تلاش کنید.', 'tadris' ),
		)
	);
	
	wp_localize_script(
		'webmz-tadris-widgets',
		'webmzTadrisWidgets',
		array(
			'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
			'nonce'              => wp_create_nonce( 'webmz_tadris_widgets' ),
			'loginMessage'       => esc_html__( 'ابتدا وارد سایت شوید.', 'tadris' ),
			'copiedMessage'      => esc_html__( 'لینک کپی شد.', 'tadris' ),
			'cartAddedMessage'   => esc_html__( 'به سبد خرید اضافه شد.', 'tadris' ),
			'cartAddedTitle'     => esc_html__( 'محصول به سبد خرید اضافه شد', 'tadris' ),
			'savedTitle'         => esc_html__( 'ذخیره', 'tadris' ),
			'shareTitle'         => esc_html__( 'اشتراک‌گذاری', 'tadris' ),
			'copyText'           => esc_html__( 'کپی لینک', 'tadris' ),
			'closeText'          => esc_html__( 'بستن', 'tadris' ),
			'errorMessage'       => esc_html__( 'خطایی رخ داد. دوباره تلاش کنید.', 'tadris' ),
			'favoriteLoadingTitle' => esc_html__( 'در حال ذخیره...', 'tadris' ),
			'favoriteRemovingTitle' => esc_html__( 'در حال حذف از ذخیره‌ها...', 'tadris' ),
			'favoriteSavedTitle'   => esc_html__( 'ذخیره شد', 'tadris' ),
			'favoriteRemovedTitle' => esc_html__( 'حذف شد', 'tadris' ),
			'cartLoadingTitle'     => esc_html__( 'در حال افزودن به سبد خرید...', 'tadris' ),
			'loadingText'          => esc_html__( 'لطفاً چند لحظه صبر کنید.', 'tadris' ),
			'checkoutUrl'        => class_exists( 'WooCommerce' ) ? wc_get_checkout_url() : home_url( '/' ),
			'savedVideosUrl'     => function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'saved-videos' ) : home_url( '/' ),
			'checkoutButtonText' => esc_html__( 'تسویه حساب', 'tadris' ),
			'cartAlreadyInCartTitle'   => esc_html__( 'محصول در سبد خرید موجود است', 'tadris' ),
			'cartAlreadyInCartMessage' => esc_html__( 'شما قبلاً این محصول را به سبد خریدتان اضافه کرده‌اید.', 'tadris' ),
			'cartAlreadyInCartButton'  => esc_html__( 'ثبت سفارش', 'tadris' ),
			'viewSavedText'      => esc_html__( 'مشاهده ذخیره‌ها', 'tadris' ),
			'saveText'           => esc_html__( 'ذخیره', 'tadris' ),
			'removeSavedText'    => esc_html__( 'حذف از ذخیره', 'tadris' ),
			'plyrIconUrl'        => WEBMZ_URI . 'assets/images/plyr.svg',
			'requireLoginDownload' => function_exists( 'webmz_otp_get_settings' ) && function_exists( 'webmz_otp_is_enabled' ) && webmz_otp_is_enabled() && 'yes' === webmz_otp_get_settings()['require_login_download'],
			'isLoggedIn'         => is_user_logged_in(),
			'downloadLoginTitle'   => esc_html__( 'برای دانلود باید وارد شوید', 'tadris' ),
			'downloadLoginText'    => esc_html__( 'جهت دانلود باید لاگین کنید.', 'tadris' ),
			'downloadLoginButton'  => esc_html__( 'ورود / عضویت', 'tadris' ),
			'addToCartUrl'       => class_exists( 'WooCommerce' ) ? ( class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( 'add_to_cart' ) : add_query_arg( 'wc-ajax', 'add_to_cart', home_url( '/' ) ) ) : '',
		)
	);
	wp_localize_script(
		'webmz-header-commerce',
		'webmzHeaderCommerce',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'webmz_header_cart' ),
		)
	);
	webmz_enqueue_tadris_elementor_animation_assets();
	wp_enqueue_script( 'webmz-main', WEBMZ_URI . 'assets/js/main.js', array(), WEBMZ_VERSION, true );
	wp_enqueue_script( 'webmz-sticky-elements', WEBMZ_URI . 'assets/js/sticky-elements.js', array(), WEBMZ_VERSION, true );
	if ( function_exists( 'webmz_floating_contact_is_enabled' ) && webmz_floating_contact_is_enabled() ) {
		wp_enqueue_style( 'webmz-floating-contact' );
		wp_enqueue_script( 'webmz-floating-contact' );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if (
		defined( 'WEBMZ_TEACHER_POST_TYPE' )
		&& (
			is_post_type_archive( WEBMZ_TEACHER_POST_TYPE )
			|| is_singular( WEBMZ_TEACHER_POST_TYPE )
		)
	) {
		wp_enqueue_style( 'webmz-teachers' );

		if ( is_post_type_archive( WEBMZ_TEACHER_POST_TYPE ) ) {
			wp_enqueue_script( 'webmz-teachers-archive' );
		}

		if ( is_singular( WEBMZ_TEACHER_POST_TYPE ) && class_exists( 'WooCommerce' ) ) {
			wp_enqueue_style( 'webmz-store-archive' );

			if ( wp_script_is( 'webmz-tadris-widgets', 'registered' ) ) {
				wp_enqueue_script( 'webmz-tadris-widgets' );
			}

			if ( wp_script_is( 'webmz-header-commerce', 'registered' ) ) {
				wp_enqueue_style( 'webmz-header-commerce' );
				wp_enqueue_script( 'webmz-header-commerce' );
			}
		}

		if ( is_singular( WEBMZ_TEACHER_POST_TYPE ) ) {
			wp_enqueue_style( 'webmz-comments-widget' );
			wp_enqueue_script( 'webmz-comments-widget' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'webmz_enqueue_assets' );


/**
 * Apply selected local font to the WordPress admin dashboard.
 *
 * @return void
 */
function webmz_enqueue_admin_font_assets() {
	if ( ! is_admin() ) {
		return;
	}

	$options = webmz_get_options();
	$fonts   = webmz_get_local_fonts();
	$slug    = isset( $options['admin_font_family'] ) ? sanitize_key( $options['admin_font_family'] ) : 'system';
	$font    = isset( $fonts[ $slug ] ) ? $fonts[ $slug ] : $fonts['system'];

	if ( ! empty( $font['css'] ) && file_exists( WEBMZ_DIR . $font['css'] ) ) {
		wp_enqueue_style( 'webmz-admin-font', WEBMZ_URI . $font['css'], array(), WEBMZ_VERSION );
	}

	$stack = isset( $font['stack'] ) ? $font['stack'] : '-apple-system, BlinkMacSystemFont, "Segoe UI", Tahoma, Arial, sans-serif';
	$text_selectors = 'body.wp-admin, body.wp-admin #wpadminbar, body.wp-admin .wp-core-ui, body.wp-admin input, body.wp-admin select, body.wp-admin textarea, body.wp-admin button, body.wp-admin .button, body.wp-admin .button-primary, body.wp-admin p, body.wp-admin label, body.wp-admin .description, body.wp-admin .media-modal, body.wp-admin .components-modal__frame, body.wp-admin .notice, body.wp-admin .menu-top, body.wp-admin .wp-menu-name, body.wp-admin.rtl h1, body.wp-admin.rtl h2, body.wp-admin.rtl h3, body.wp-admin.rtl h4, body.wp-admin.rtl h5, body.wp-admin.rtl h6, body.wp-admin .rtl h1, body.wp-admin .rtl h2, body.wp-admin .rtl h3, body.wp-admin .rtl h4, body.wp-admin .rtl h5, body.wp-admin .rtl h6';
	$icon_reset     = 'body.wp-admin .dashicons, body.wp-admin .dashicons:before, body.wp-admin #wpadminbar .ab-icon:before, body.wp-admin #wpadminbar .ab-item:before, body.wp-admin #adminmenu div.wp-menu-image:before, body.wp-admin .media-modal-icon:before, body.wp-admin .mce-ico';
	$css            = $text_selectors . '{font-family:' . $stack . ' !important;}';
	$css           .= $icon_reset . '{font-family:dashicons !important;}';
	$css           .= '#adminmenu .awaiting-mod,#adminmenu li.menu-top:hover .awaiting-mod,#adminmenu li.opensub .awaiting-mod,#adminmenu li.current .awaiting-mod,#adminmenu .wp-submenu .awaiting-mod{background-color:var(--wp-admin-theme-color)!important;}';

	wp_register_style( 'webmz-admin-font-inline', false, array(), WEBMZ_VERSION );
	wp_enqueue_style( 'webmz-admin-font-inline' );
	wp_add_inline_style( 'webmz-admin-font-inline', $css );
}
add_action( 'admin_enqueue_scripts', 'webmz_enqueue_admin_font_assets', 20 );
