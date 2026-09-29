<?php
/**
 * Elementor Free integration for WebMZ widgets and category.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return the unique category slug used only for WebMZ widgets.
 *
 * @return string
 */
function webmz_elementor_category_slug() {
	return 'webmz-widgets';
}

/**
 * Return category slug for WebMZ dynamic template widgets.
 *
 * @return string
 */
function webmz_elementor_dynamic_category_slug() {
	return 'webmz-dynamic-widgets';
}

/**
 * Return category slug for WebMZ WooCommerce template widgets.
 *
 * @return string
 */
function webmz_elementor_shop_category_slug() {
	return 'webmz-shop-widgets';
}

/**
 * Return category slug for WebMZ single product template widgets.
 *
 * @return string
 */
function webmz_elementor_single_product_category_slug() {
	return 'webmz-single-product-widgets';
}

/**
 * Register the separated WebMZ Elementor widget categories.
 *
 * Basic page-building widgets stay under «آکام», while dynamic template
 * and store widgets use clearly named categories to avoid apparent extras.
 * Categories that contain no widgets are hidden automatically by Elementor.
 *
 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
 * @return void
 */
function webmz_register_elementor_category( $elements_manager ) {
	$elements_manager->add_category( webmz_elementor_category_slug(), array(
		'title' => esc_html__( 'آکام', 'tadris' ),
		'icon'  => 'fa fa-plug',
	) );
	$elements_manager->add_category( webmz_elementor_dynamic_category_slug(), array(
		'title' => esc_html__( 'آکام - قالب‌ساز', 'tadris' ),
		'icon'  => 'fa fa-file-code',
	) );
	$elements_manager->add_category( webmz_elementor_shop_category_slug(), array(
		'title' => esc_html__( 'آکام - فروشگاه', 'tadris' ),
		'icon'  => 'fa fa-shopping-basket',
	) );
	$elements_manager->add_category( webmz_elementor_single_product_category_slug(), array(
		'title' => esc_html__( 'سینگل محصول - آکام', 'tadris' ),
		'icon'  => 'fa fa-shopping-cart',
	) );
}
add_action( 'elementor/elements/categories_registered', 'webmz_register_elementor_category' );

/**
 * Load and register WebMZ widgets for layouts.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */
function webmz_register_elementor_widgets( $widgets_manager ) {
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-widget-traits.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/basic-widgets.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/ajax-search-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/ajax-search-popup-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/site-logo-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/navigation-menu-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/icon-navigation-menu-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/mega-menu-content-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/header-commerce-widget-trait.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/account-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/mobile-header-widgets.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/content-widgets.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/blog-archive-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/teachers-widgets.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/professional-teachers-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/instructor-slider-widget.php';
	if ( class_exists( 'WooCommerce' ) ) {
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/store-archive-widget.php';
	}
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/related-posts-grid-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/post-download-box-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/coupon-code-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/three-background-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/header-contact-box-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-basic-widgets.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-heading-animation-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/header-contact-box-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-video-widgets.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-view-all-v2-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-advanced-button-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-podcast-load-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-blog-widgets.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-blog-loop-2-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-blog-tile-loop-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-footer-widgets.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-back-to-top-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/back-to-top-2-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-plyr-widgets.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-checkout-steps-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-comments-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-otp-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/contact-widgets.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/slider-element-1-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/service-box-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/course-category-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/customer-testimonials-slider-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/customer-testimonials-slider-2-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/podcast-player-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/youtube-playlist-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/contact-us-banner-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/footer-menu-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/footer-contact-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/footer-links-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/footer-ticket-box-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/icon-details-box-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/zhaket-icon-list-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/zhaket-why-buy-footer-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/zhaket-special-banners-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/zhaket-heading-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/zhaket-footer-newsletter-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/zhaket-blog-loop-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/stories-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/blog-cta-widgets.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/shape-svg-library.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/shape-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/light-source-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/learning-path-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/membership-pricing-widget.php';
	require_once WEBMZ_DIR . 'elementor-widgets/widgets/animated-hero-section-widget.php';

	// General design widgets: these are visible even before any template is assigned.
	$widgets_manager->register( new \WebMZ\Elementor\Hero_Banner_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Page_Hero_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Slider_Element_1_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Service_Box_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Course_Category_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Customer_Testimonials_Slider_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Customer_Testimonials_Slider_2_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Podcast_Player_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Youtube_Playlist_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Coupon_Code_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Three_Background_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Contact_Us_Banner_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Footer_Contact_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Footer_Links_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Footer_Ticket_Box_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Icon_Details_Box_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Zhaket_Icon_List_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Zhaket_Why_Buy_Footer_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Zhaket_Special_Banners_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Zhaket_Heading_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Zhaket_Footer_Newsletter_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Zhaket_Blog_Loop_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Stories_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Newsletter_Subscribe_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Article_Request_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Feature_Card_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Action_Button_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Shape_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Light_Source_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Learning_Path_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Membership_Pricing_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Animated_Hero_Section_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Ajax_Search_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Ajax_Search_Popup_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Site_Logo_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Navigation_Menu_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Icon_Navigation_Menu_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Mega_Menu_Content_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Account_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Mobile_Menu_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Mobile_Account_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Text_Box_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Contact_Box_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Header_Contact_Box_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Header_Contact_Box_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Icon_Box_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Heading_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Heading_2_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Heading_3_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Heading_4_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Heading_5_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Heading_Animation_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Large_Video_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_View_All_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Video_Loop_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Counter_Widget() );
	$widgets_manager->register( new \Tadris_View_All_V2_Widget() );
	$widgets_manager->register( new \Tadris_Advanced_Button_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Podcast_Load_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Featured_Blog_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Blog_Loop_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Blog_Loop_2_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Blog_Tile_Loop_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Instagram_Follow_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Footer_Menu_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Footer_Menu_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Back_To_Top_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Back_To_Top_2_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Video_Plyr_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Podcast_Plyr_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Checkout_Steps_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_Comments_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Tadris_OTP_Login_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\WebMZ_Jobs_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\WebMZ_Team_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\WebMZ_Image_Gallery_Swiper_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\WebMZ_Contact_Tabs_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\WebMZ_Ajax_Contact_Form_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\WebMZ_Offices_Address_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\WebMZ_Jobs_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\WebMZ_Team_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\WebMZ_Image_Gallery_Swiper_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\WebMZ_Contact_Tabs_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\WebMZ_Ajax_Contact_Form_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\WebMZ_Offices_Address_Widget() );

	// Dynamic template widgets for post/page/archive layouts: displayed in the dedicated template-builder category.
	$widgets_manager->register( new \WebMZ\Elementor\Post_Title_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Post_Content_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Featured_Image_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Archive_Title_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Archive_Loop_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Blog_Archive_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Teachers_Archive_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Teachers_Display_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Professional_Teachers_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Instructor_Slider_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Teacher_Single_Hero_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Teacher_Single_Content_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Teacher_Single_Sections_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Teacher_Single_Courses_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Teacher_Single_Comments_Widget() );
	if ( class_exists( 'WooCommerce' ) ) {
		$widgets_manager->register( new \WebMZ\Elementor\Store_Archive_Widget() );
	}
	$widgets_manager->register( new \WebMZ\Elementor\Post_Breadcrumb_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Post_Modified_Date_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Post_Primary_Category_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Post_Share_Save_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Post_TOC_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Post_Author_Box_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Post_Navigation_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Related_Posts_Grid_Widget() );
	$widgets_manager->register( new \WebMZ\Elementor\Post_Download_Box_Widget() );

	// Store widgets appear only when WooCommerce is active and are grouped separately.
	if ( class_exists( 'WooCommerce' ) ) {
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/mini-cart-widget.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/woocommerce-widgets.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-product-widget.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-product-loop-2-widget.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-product-loop-3-widget.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-tabbed-product-loop-widget.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/file-loop-widget.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/zhaket-product-tabs-widget.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/zhaaket-top-products-box-widget.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/zhaket-bestsellers-column-widget.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/zhaket-vertical-slider-widget.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/zhaket-top-developer-widget.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/course-intro-banner-widget.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/special-offer-slider-widget.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/zhaket-product-loop-widget.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/single-product-webmasters-widgets.php';
		require_once WEBMZ_DIR . 'elementor-widgets/widgets/tadris-woocommerce-reviews-widget.php';
		$widgets_manager->register( new \WebMZ\Elementor\Mini_Cart_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Mobile_Mini_Cart_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Product_Title_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Product_Price_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Product_Rating_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Product_Gallery_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Product_Short_Description_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Product_Add_To_Cart_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Product_Meta_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Product_Tabs_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Related_Products_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Products_Result_Count_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Products_Ordering_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Products_Loop_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Tadris_Product_Loop_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Tadris_Product_Loop_2_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Tadris_Product_Loop_3_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Tadris_Tabbed_Product_Loop_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\File_Loop_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Zhaket_Product_Tabs_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Zhaaket_Top_Products_Box_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Zhaket_Bestsellers_Column_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Zhaket_Vertical_Slider_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Zhaket_Top_Developer_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Course_Intro_Banner_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Special_Offer_Slider_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Zhaket_Product_Loop_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_Product_Media_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_Participants_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_Rating_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_Product_Title_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_Short_Description_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_Product_Price_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_Add_To_Cart_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_Advanced_Add_To_Cart_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_Course_Features_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_Purchase_Box_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_Instructor_Box_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_Product_Content_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_FAQ_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_Section_Heading_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\SPW_Course_Curriculum_Widget() );
		$widgets_manager->register( new \WebMZ\Elementor\Tadris_WooCommerce_Reviews_Widget() );
	}
}
add_action( 'elementor/widgets/register', 'webmz_register_elementor_widgets' );

/**
 * Load editor helpers for WebMZ Elementor widgets.
 *
 * @return void
 */
function webmz_enqueue_elementor_editor_widget_assets() {
	wp_enqueue_script(
		'webmz-mega-menu-editor',
		WEBMZ_URI . 'assets/js/mega-menu-editor.js',
		array(),
		WEBMZ_VERSION,
		true
	);
}
add_action( 'elementor/editor/after_enqueue_scripts', 'webmz_enqueue_elementor_editor_widget_assets' );

/**
 * Ensure Tadris animation assets load in Elementor preview iframe.
 *
 * @return void
 */
function webmz_enqueue_elementor_tadris_animation_assets() {
	if ( function_exists( 'webmz_enqueue_tadris_elementor_animation_assets' ) ) {
		webmz_enqueue_tadris_elementor_animation_assets();
	}
}
add_action( 'elementor/frontend/after_enqueue_scripts', 'webmz_enqueue_elementor_tadris_animation_assets', 20 );
add_action( 'elementor/frontend/after_enqueue_styles', 'webmz_enqueue_elementor_tadris_animation_assets', 20 );
add_action( 'elementor/preview/enqueue_styles', 'webmz_enqueue_elementor_tadris_animation_assets', 20 );
add_action( 'elementor/preview/enqueue_scripts', 'webmz_enqueue_elementor_tadris_animation_assets', 20 );

/**
 * Admin notice when Elementor is unavailable, without breaking the base theme.
 *
 * @return void
 */
function webmz_elementor_missing_notice() {
	if ( ! current_user_can( 'activate_plugins' ) || class_exists( '\\Elementor\\Plugin' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ( 'toplevel_page_webmz-options' !== $screen->id && 'webmz_layout' !== $screen->post_type ) ) {
		return;
	}
	?>
	<div class="notice notice-info"><p><?php esc_html_e( 'برای طراحی لایه‌های آکام، افزونه رایگان Elementor را نصب و فعال کنید. قالب بدون المنتور نیز با طرح پیش‌فرض کار می‌کند.', 'tadris' ); ?></p></div>
	<?php
}
add_action( 'admin_notices', 'webmz_elementor_missing_notice' );

/**
 * Register Elementor Pro Theme Builder locations without forcing theme templates.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager Locations manager.
 * @return void
 */
function webmz_register_elementor_theme_locations( $elementor_theme_manager ) {
	if ( ! is_object( $elementor_theme_manager ) || ! method_exists( $elementor_theme_manager, 'register_all_core_location' ) ) {
		return;
	}

	$elementor_theme_manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'webmz_register_elementor_theme_locations' );

/**
 * Add WebMZ sticky header controls to Elementor Advanced tab.
 *
 * @param \Elementor\Element_Base $element Elementor element instance.
 * @return void
 */
function webmz_elementor_register_sticky_controls( $element ) {
	if ( ! class_exists( '\Elementor\Controls_Manager' ) || ! is_object( $element ) || ! method_exists( $element, 'start_controls_section' ) ) {
		return;
	}

	$element->start_controls_section(
		'webmz_sticky_controls_section',
		array(
			'label' => esc_html__( 'چسبان', 'tadris' ),
			'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
		)
	);

	$element->add_control(
		'webmz_sticky_header_enabled',
		array(
			'label'        => esc_html__( 'هدر چسبان', 'tadris' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'فعال', 'tadris' ),
			'label_off'    => esc_html__( 'غیرفعال', 'tadris' ),
			'return_value' => 'yes',
			'default'      => '',
			'description'  => esc_html__( 'این المان هنگام اسکرول به بالای صفحه می‌چسبد و عرض اصلی خودش را حفظ می‌کند.', 'tadris' ),
		)
	);

	$element->add_control(
		'webmz_sticky_hide_enabled',
		array(
			'label'        => esc_html__( 'مخفی شونده', 'tadris' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'فعال', 'tadris' ),
			'label_off'    => esc_html__( 'غیرفعال', 'tadris' ),
			'return_value' => 'yes',
			'default'      => '',
			'condition'    => array(
				'webmz_sticky_header_enabled' => 'yes',
			),
		)
	);

	$element->add_control(
		'webmz_sticky_hide_direction',
		array(
			'label'     => esc_html__( 'جهت مخفی شدن', 'tadris' ),
			'type'      => \Elementor\Controls_Manager::SELECT,
			'default'   => 'down',
			'options'   => array(
				'down' => esc_html__( 'هنگام اسکرول رو به پایین مخفی شود', 'tadris' ),
				'up'   => esc_html__( 'هنگام اسکرول رو به بالا مخفی شود', 'tadris' ),
			),
			'condition' => array(
				'webmz_sticky_header_enabled' => 'yes',
				'webmz_sticky_hide_enabled'   => 'yes',
			),
		)
	);

	$element->add_control(
		'webmz_sticky_container_enabled',
		array(
			'label'        => esc_html__( 'کانتینر چسبان', 'tadris' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'فعال', 'tadris' ),
			'label_off'    => esc_html__( 'غیرفعال', 'tadris' ),
			'return_value' => 'yes',
			'default'      => '',
			'separator'    => 'before',
			'description'  => esc_html__( 'هنگام اسکرول به بالای صفحه می‌چسبد و در پایان محدوده والد، چسبندگی را رها می‌کند.', 'tadris' ),
		)
	);

	$element->add_control(
		'webmz_sticky_container_stay_in_column',
		array(
			'label'        => esc_html__( 'ماندن در ستون', 'tadris' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'فعال', 'tadris' ),
			'label_off'    => esc_html__( 'غیرفعال', 'tadris' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'condition'    => array(
				'webmz_sticky_container_enabled' => 'yes',
			),
			'description'  => esc_html__( 'چسبندگی را به ستون یا کانتینر والد محدود می‌کند تا از ستون کناری پایین‌تر نرود.', 'tadris' ),
		)
	);

	$element->add_control(
		'webmz_sticky_container_offset',
		array(
			'label'      => esc_html__( 'فاصله از بالا', 'tadris' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array(
				'px' => array(
					'min'  => 0,
					'max'  => 300,
					'step' => 1,
				),
			),
			'default'    => array(
				'unit' => 'px',
				'size' => 0,
			),
			'condition'  => array(
				'webmz_sticky_container_enabled' => 'yes',
			),
		)
	);

	$element->end_controls_section();
}

/**
 * Normalize mouse animation scope setting.
 *
 * @param array  $settings Elementor settings.
 * @param string $key      Setting key.
 * @return string
 */
function webmz_elementor_get_mouse_animation_scope( $settings, $key ) {
	$scope = ! empty( $settings[ $key ] ) ? sanitize_key( $settings[ $key ] ) : 'element';

	return in_array( $scope, array( 'element', 'page' ), true ) ? $scope : 'element';
}

/**
 * Add a Tadris animation control with Elementor editor preview support.
 *
 * @param \Elementor\Element_Base $element Elementor element instance.
 * @param string                $id      Control ID.
 * @param array                 $args    Control arguments.
 * @return void
 */
function webmz_elementor_add_tadris_animation_control( $element, $id, array $args ) {
	$args['frontend_available'] = true;

	if (
		isset( $args['type'] )
		&& \Elementor\Controls_Manager::SWITCHER === $args['type']
		&& ! empty( $args['webmz_prefix_class'] )
	) {
		$args['prefix_class']       = 'webmz-tadris-';
		$args['classes_dictionary'] = array( 'yes' => $args['webmz_prefix_class'] );
		unset( $args['webmz_prefix_class'] );
	}

	$element->add_control( $id, $args );
}

/**
 * Add mouse animation scope control.
 *
 * @param \Elementor\Element_Base $element     Elementor element instance.
 * @param string                  $setting_key Setting key.
 * @param string                  $enabled_key Enabled setting key.
 * @return void
 */
function webmz_elementor_add_mouse_scope_control( $element, $setting_key, $enabled_key ) {
	webmz_elementor_add_tadris_animation_control(
		$element,
		$setting_key,
		array(
			'label'     => esc_html__( 'محدوده اجرا', 'tadris' ),
			'type'      => \Elementor\Controls_Manager::SELECT,
			'default'   => 'element',
			'options'   => array(
				'element' => esc_html__( 'فقط وقتی موس روی المان است', 'tadris' ),
				'page'    => esc_html__( 'هر جای صفحه', 'tadris' ),
			),
			'condition' => array(
				$enabled_key => 'yes',
			),
		)
	);
}

/**
 * Add Tadris animation controls to Elementor Advanced tab.
 *
 * @param \Elementor\Element_Base $element Elementor element instance.
 * @return void
 */
function webmz_elementor_register_tadris_animation_controls( $element ) {
	if ( ! class_exists( '\Elementor\Controls_Manager' ) || ! is_object( $element ) || ! method_exists( $element, 'start_controls_section' ) ) {
		return;
	}

	$element->start_controls_section(
		'webmz_tadris_animations_section',
		array(
			'label' => esc_html__( 'انیمیشن های آکام', 'tadris' ),
			'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_float_cloud_enabled',
		array(
			'label'              => esc_html__( 'شناور مثل ابر', 'tadris' ),
			'type'               => \Elementor\Controls_Manager::SWITCHER,
			'label_on'           => esc_html__( 'فعال', 'tadris' ),
			'label_off'          => esc_html__( 'غیرفعال', 'tadris' ),
			'return_value'       => 'yes',
			'default'            => '',
			'webmz_prefix_class' => 'float-cloud',
			'description'        => esc_html__( 'محتوای این المان به‌صورت نرم و آرام بالا و پایین می‌رود.', 'tadris' ),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_glow_beneath_enabled',
		array(
			'label'              => esc_html__( 'نور زیر', 'tadris' ),
			'type'               => \Elementor\Controls_Manager::SWITCHER,
			'label_on'           => esc_html__( 'فعال', 'tadris' ),
			'label_off'          => esc_html__( 'غیرفعال', 'tadris' ),
			'return_value'       => 'yes',
			'default'            => '',
			'separator'          => 'before',
			'webmz_prefix_class' => 'glow-beneath',
			'description'        => esc_html__( 'یک هاله نوری رنگی زیر المان اضافه می‌شود که بسیار آرام بزرگ و کوچک می‌شود.', 'tadris' ),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_glow_beneath_color',
		array(
			'label'     => esc_html__( 'رنگ نور', 'tadris' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#F4A261',
			'condition' => array(
				'webmz_tadris_glow_beneath_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_tilt_enabled',
		array(
			'label'              => esc_html__( 'چرخش با موس', 'tadris' ),
			'type'               => \Elementor\Controls_Manager::SWITCHER,
			'label_on'           => esc_html__( 'فعال', 'tadris' ),
			'label_off'          => esc_html__( 'غیرفعال', 'tadris' ),
			'return_value'       => 'yes',
			'default'            => '',
			'separator'          => 'before',
			'webmz_prefix_class' => 'mouse-tilt',
			'description'        => esc_html__( 'با حرکت موس، المان به‌صورت سه‌بعدی کمی می‌چرخد.', 'tadris' ),
		)
	);

	webmz_elementor_add_mouse_scope_control( $element, 'webmz_tadris_mouse_tilt_scope', 'webmz_tadris_mouse_tilt_enabled' );

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_tilt_max',
		array(
			'label'      => esc_html__( 'میزان چرخش', 'tadris' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'deg' ),
			'range'      => array(
				'deg' => array(
					'min'  => 1,
					'max'  => 50,
					'step' => 1,
				),
			),
			'default'    => array(
				'unit' => 'deg',
				'size' => 15,
			),
			'condition'  => array(
				'webmz_tadris_mouse_tilt_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_tilt_speed',
		array(
			'label'      => esc_html__( 'سرعت بازگشت', 'tadris' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'ms' ),
			'range'      => array(
				'ms' => array(
					'min'  => 100,
					'max'  => 1000,
					'step' => 10,
				),
			),
			'default'    => array(
				'unit' => 'ms',
				'size' => 200,
			),
			'condition'  => array(
				'webmz_tadris_mouse_tilt_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_tilt_perspective',
		array(
			'label'      => esc_html__( 'عمق پرسپکتیو', 'tadris' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array(
				'px' => array(
					'min'  => 200,
					'max'  => 2000,
					'step' => 10,
				),
			),
			'default'    => array(
				'unit' => 'px',
				'size' => 1000,
			),
			'condition'  => array(
				'webmz_tadris_mouse_tilt_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_tilt_scale',
		array(
			'label'      => esc_html__( 'بزرگنمایی', 'tadris' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'x' ),
			'range'      => array(
				'x' => array(
					'min'  => 0.8,
					'max'  => 1.2,
					'step' => 0.01,
				),
			),
			'default'    => array(
				'unit' => 'x',
				'size' => 1,
			),
			'condition'  => array(
				'webmz_tadris_mouse_tilt_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_tilt_axis',
		array(
			'label'     => esc_html__( 'محور چرخش', 'tadris' ),
			'type'      => \Elementor\Controls_Manager::SELECT,
			'default'   => '',
			'options'   => array(
				''  => esc_html__( 'هر دو محور', 'tadris' ),
				'x' => esc_html__( 'فقط افقی', 'tadris' ),
				'y' => esc_html__( 'فقط عمودی', 'tadris' ),
			),
			'condition' => array(
				'webmz_tadris_mouse_tilt_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_tilt_reverse',
		array(
			'label'        => esc_html__( 'جهت معکوس', 'tadris' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'بله', 'tadris' ),
			'label_off'    => esc_html__( 'خیر', 'tadris' ),
			'return_value' => 'yes',
			'default'      => '',
			'condition'    => array(
				'webmz_tadris_mouse_tilt_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_tilt_glare',
		array(
			'label'        => esc_html__( 'بازتاب نور', 'tadris' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'فعال', 'tadris' ),
			'label_off'    => esc_html__( 'غیرفعال', 'tadris' ),
			'return_value' => 'yes',
			'default'      => '',
			'condition'    => array(
				'webmz_tadris_mouse_tilt_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_tilt_max_glare',
		array(
			'label'      => esc_html__( 'شدت بازتاب', 'tadris' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'x' ),
			'range'      => array(
				'x' => array(
					'min'  => 0,
					'max'  => 1,
					'step' => 0.05,
				),
			),
			'default'    => array(
				'unit' => 'x',
				'size' => 0.5,
			),
			'condition'  => array(
				'webmz_tadris_mouse_tilt_enabled' => 'yes',
				'webmz_tadris_mouse_tilt_glare'   => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_follow_enabled',
		array(
			'label'              => esc_html__( 'دنبال کردن موس', 'tadris' ),
			'type'               => \Elementor\Controls_Manager::SWITCHER,
			'label_on'           => esc_html__( 'فعال', 'tadris' ),
			'label_off'          => esc_html__( 'غیرفعال', 'tadris' ),
			'return_value'       => 'yes',
			'default'            => '',
			'separator'          => 'before',
			'webmz_prefix_class' => 'mouse-follow',
			'description'        => esc_html__( 'المان با حرکت موس جابه‌جا می‌شود. همزمان با چرخش با موس یا شناور مثل ابر استفاده نشود.', 'tadris' ),
		)
	);

	webmz_elementor_add_mouse_scope_control( $element, 'webmz_tadris_mouse_follow_scope', 'webmz_tadris_mouse_follow_enabled' );

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_follow_direction',
		array(
			'label'     => esc_html__( 'جهت دنبال‌کردن', 'tadris' ),
			'type'      => \Elementor\Controls_Manager::SELECT,
			'default'   => 'direct',
			'options'   => array(
				'direct'  => esc_html__( 'مستقیم (به سمت موس)', 'tadris' ),
				'reverse' => esc_html__( 'معکوس (خلاف موس)', 'tadris' ),
			),
			'condition' => array(
				'webmz_tadris_mouse_follow_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_follow_intensity',
		array(
			'label'      => esc_html__( 'میزان جابه‌جایی', 'tadris' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array(
				'px' => array(
					'min'  => 5,
					'max'  => 80,
					'step' => 1,
				),
			),
			'default'    => array(
				'unit' => 'px',
				'size' => 25,
			),
			'condition'  => array(
				'webmz_tadris_mouse_follow_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_follow_speed',
		array(
			'label'      => esc_html__( 'نرمی حرکت', 'tadris' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'x' ),
			'range'      => array(
				'x' => array(
					'min'  => 0.05,
					'max'  => 1,
					'step' => 0.01,
				),
			),
			'default'    => array(
				'unit' => 'x',
				'size' => 0.22,
			),
			'condition'  => array(
				'webmz_tadris_mouse_follow_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_follow_axis',
		array(
			'label'     => esc_html__( 'محور حرکت', 'tadris' ),
			'type'      => \Elementor\Controls_Manager::SELECT,
			'default'   => 'both',
			'options'   => array(
				'both' => esc_html__( 'هر دو محور', 'tadris' ),
				'x'    => esc_html__( 'فقط افقی', 'tadris' ),
				'y'    => esc_html__( 'فقط عمودی', 'tadris' ),
			),
			'condition' => array(
				'webmz_tadris_mouse_follow_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_magnetic_enabled',
		array(
			'label'              => esc_html__( 'مگنت موس', 'tadris' ),
			'type'               => \Elementor\Controls_Manager::SWITCHER,
			'label_on'           => esc_html__( 'فعال', 'tadris' ),
			'label_off'          => esc_html__( 'غیرفعال', 'tadris' ),
			'return_value'       => 'yes',
			'default'            => '',
			'separator'          => 'before',
			'webmz_prefix_class' => 'mouse-magnetic',
			'description'        => esc_html__( 'وقتی موس نزدیک المان می‌شود، المان به سمت آن کشیده می‌شود.', 'tadris' ),
		)
	);

	webmz_elementor_add_mouse_scope_control( $element, 'webmz_tadris_mouse_magnetic_scope', 'webmz_tadris_mouse_magnetic_enabled' );

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_magnetic_strength',
		array(
			'label'      => esc_html__( 'قدرت مگنت', 'tadris' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array(
				'px' => array(
					'min'  => 5,
					'max'  => 60,
					'step' => 1,
				),
			),
			'default'    => array(
				'unit' => 'px',
				'size' => 20,
			),
			'condition'  => array(
				'webmz_tadris_mouse_magnetic_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_magnetic_radius',
		array(
			'label'      => esc_html__( 'شعاع جذب', 'tadris' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array(
				'px' => array(
					'min'  => 50,
					'max'  => 400,
					'step' => 5,
				),
			),
			'default'    => array(
				'unit' => 'px',
				'size' => 150,
			),
			'condition'  => array(
				'webmz_tadris_mouse_magnetic_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_magnetic_speed',
		array(
			'label'      => esc_html__( 'نرمی مگنت', 'tadris' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'x' ),
			'range'      => array(
				'x' => array(
					'min'  => 0.05,
					'max'  => 1,
					'step' => 0.01,
				),
			),
			'default'    => array(
				'unit' => 'x',
				'size' => 0.25,
			),
			'condition'  => array(
				'webmz_tadris_mouse_magnetic_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_spotlight_enabled',
		array(
			'label'              => esc_html__( 'نور دنبال‌کننده موس', 'tadris' ),
			'type'               => \Elementor\Controls_Manager::SWITCHER,
			'label_on'           => esc_html__( 'فعال', 'tadris' ),
			'label_off'          => esc_html__( 'غیرفعال', 'tadris' ),
			'return_value'       => 'yes',
			'default'            => '',
			'separator'          => 'before',
			'webmz_prefix_class' => 'mouse-spotlight',
			'description'        => esc_html__( 'یک هاله نوری روی المان دنبال موقعیت موس می‌رود.', 'tadris' ),
		)
	);

	webmz_elementor_add_mouse_scope_control( $element, 'webmz_tadris_mouse_spotlight_scope', 'webmz_tadris_mouse_spotlight_enabled' );

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_spotlight_color',
		array(
			'label'     => esc_html__( 'رنگ نور', 'tadris' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.35)',
			'condition' => array(
				'webmz_tadris_mouse_spotlight_enabled' => 'yes',
			),
		)
	);

	webmz_elementor_add_tadris_animation_control(
		$element,
		'webmz_tadris_mouse_spotlight_size',
		array(
			'label'      => esc_html__( 'اندازه نور', 'tadris' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( '%' ),
			'range'      => array(
				'%' => array(
					'min'  => 20,
					'max'  => 120,
					'step' => 1,
				),
			),
			'default'    => array(
				'unit' => '%',
				'size' => 60,
			),
			'condition'  => array(
				'webmz_tadris_mouse_spotlight_enabled' => 'yes',
			),
		)
	);

	$element->end_controls_section();
}

/**
 * Register the sticky controls after Elementor advanced sections.
 *
 * @return void
 */
function webmz_elementor_add_sticky_controls_hooks() {
	$hooks = array(
		'elementor/element/common/_section_style/after_section_end',
		'elementor/element/section/section_advanced/after_section_end',
		'elementor/element/column/section_advanced/after_section_end',
		'elementor/element/container/section_layout/after_section_end',
	);

	foreach ( $hooks as $hook ) {
		add_action( $hook, 'webmz_elementor_register_sticky_controls', 10, 1 );
		add_action( $hook, 'webmz_elementor_register_tadris_animation_controls', 10, 1 );
	}
}
webmz_elementor_add_sticky_controls_hooks();

/**
 * Check whether Elementor editor preview is active.
 *
 * @return bool
 */
function webmz_elementor_is_edit_mode() {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}

	$plugin = \Elementor\Plugin::$instance;

	return isset( $plugin->editor )
		&& method_exists( $plugin->editor, 'is_edit_mode' )
		&& $plugin->editor->is_edit_mode();
}

/**
 * Add frontend attributes for sticky Elementor elements.
 *
 * @param \Elementor\Element_Base $element Elementor element instance.
 * @return void
 */
function webmz_elementor_add_sticky_render_attributes( $element ) {
	if ( ! is_object( $element ) || ! method_exists( $element, 'get_settings_for_display' ) || ! method_exists( $element, 'add_render_attribute' ) ) {
		return;
	}

	$settings = $element->get_settings_for_display();

	if ( ! webmz_elementor_is_edit_mode() && ! empty( $settings['webmz_sticky_header_enabled'] ) && 'yes' === $settings['webmz_sticky_header_enabled'] ) {
		$hide_enabled   = ! empty( $settings['webmz_sticky_hide_enabled'] ) && 'yes' === $settings['webmz_sticky_hide_enabled'];
		$hide_direction = ! empty( $settings['webmz_sticky_hide_direction'] ) && 'up' === $settings['webmz_sticky_hide_direction'] ? 'up' : 'down';

		$element->add_render_attribute( '_wrapper', 'class', 'webmz-sticky-element' );
		$element->add_render_attribute( '_wrapper', 'data-webmz-sticky-header', '1' );
		$element->add_render_attribute( '_wrapper', 'data-webmz-sticky-hide', $hide_enabled ? '1' : '0' );
		$element->add_render_attribute( '_wrapper', 'data-webmz-sticky-hide-direction', $hide_direction );
	}

	if ( ! webmz_elementor_is_edit_mode() && ! empty( $settings['webmz_sticky_container_enabled'] ) && 'yes' === $settings['webmz_sticky_container_enabled'] ) {
		$stay_in_column = ! isset( $settings['webmz_sticky_container_stay_in_column'] ) || 'yes' === $settings['webmz_sticky_container_stay_in_column'];
		$offset         = isset( $settings['webmz_sticky_container_offset']['size'] ) ? (float) $settings['webmz_sticky_container_offset']['size'] : 0.0;

		$element->add_render_attribute( '_wrapper', 'class', 'webmz-sticky-container-element' );
		$element->add_render_attribute( '_wrapper', 'data-webmz-sticky-container', '1' );
		$element->add_render_attribute( '_wrapper', 'data-webmz-sticky-container-stay', $stay_in_column ? '1' : '0' );
		$element->add_render_attribute( '_wrapper', 'data-webmz-sticky-container-offset', (string) $offset );

		if ( $stay_in_column ) {
			$element->add_render_attribute( '_wrapper', 'class', 'webmz-sticky-container--stay-in-column' );
			$element->add_render_attribute( '_wrapper', 'style', '--webmz-sticky-container-offset: ' . $offset . 'px;' );
		}
	}
}
add_action( 'elementor/frontend/before_render', 'webmz_elementor_add_sticky_render_attributes' );

/**
 * Add frontend attributes for Tadris Elementor animations.
 *
 * @param \Elementor\Element_Base $element Elementor element instance.
 * @return void
 */
function webmz_elementor_add_tadris_animation_render_attributes( $element ) {
	if ( ! is_object( $element ) || ! method_exists( $element, 'get_settings_for_display' ) || ! method_exists( $element, 'add_render_attribute' ) ) {
		return;
	}

	$settings = $element->get_settings_for_display();

	if ( ! empty( $settings['webmz_tadris_float_cloud_enabled'] ) && 'yes' === $settings['webmz_tadris_float_cloud_enabled'] ) {
		$element->add_render_attribute( '_wrapper', 'class', 'webmz-tadris-float-cloud' );
	}

	if ( ! empty( $settings['webmz_tadris_glow_beneath_enabled'] ) && 'yes' === $settings['webmz_tadris_glow_beneath_enabled'] ) {
		$glow_color = ! empty( $settings['webmz_tadris_glow_beneath_color'] ) ? $settings['webmz_tadris_glow_beneath_color'] : '#F4A261';

		$element->add_render_attribute( '_wrapper', 'class', 'webmz-tadris-glow-beneath' );
		$element->add_render_attribute( '_wrapper', 'style', '--webmz-tadris-glow-color:' . esc_attr( $glow_color ) . ';' );
	}

	if ( ! empty( $settings['webmz_tadris_mouse_tilt_enabled'] ) && 'yes' === $settings['webmz_tadris_mouse_tilt_enabled'] ) {
		$tilt_max         = isset( $settings['webmz_tadris_mouse_tilt_max']['size'] ) ? (float) $settings['webmz_tadris_mouse_tilt_max']['size'] : 15.0;
		$tilt_speed       = isset( $settings['webmz_tadris_mouse_tilt_speed']['size'] ) ? (float) $settings['webmz_tadris_mouse_tilt_speed']['size'] : 200.0;
		$tilt_perspective = isset( $settings['webmz_tadris_mouse_tilt_perspective']['size'] ) ? (float) $settings['webmz_tadris_mouse_tilt_perspective']['size'] : 1000.0;
		$tilt_scale       = isset( $settings['webmz_tadris_mouse_tilt_scale']['size'] ) ? (float) $settings['webmz_tadris_mouse_tilt_scale']['size'] : 1.0;
		$tilt_axis        = ! empty( $settings['webmz_tadris_mouse_tilt_axis'] ) ? sanitize_key( $settings['webmz_tadris_mouse_tilt_axis'] ) : '';
		$tilt_reverse     = ! empty( $settings['webmz_tadris_mouse_tilt_reverse'] ) && 'yes' === $settings['webmz_tadris_mouse_tilt_reverse'];
		$tilt_glare       = ! empty( $settings['webmz_tadris_mouse_tilt_glare'] ) && 'yes' === $settings['webmz_tadris_mouse_tilt_glare'];
		$tilt_max_glare   = isset( $settings['webmz_tadris_mouse_tilt_max_glare']['size'] ) ? (float) $settings['webmz_tadris_mouse_tilt_max_glare']['size'] : 0.5;
		$tilt_scope       = webmz_elementor_get_mouse_animation_scope( $settings, 'webmz_tadris_mouse_tilt_scope' );

		$element->add_render_attribute( '_wrapper', 'class', 'webmz-tadris-mouse-tilt' );
		$element->add_render_attribute( '_wrapper', 'data-tilt', '' );
		$element->add_render_attribute( '_wrapper', 'data-webmz-mouse-tilt-scope', $tilt_scope );

		if ( 'page' === $tilt_scope ) {
			$element->add_render_attribute( '_wrapper', 'data-tilt-full-page-listening', 'true' );
		}

		$element->add_render_attribute( '_wrapper', 'data-tilt-max', (string) $tilt_max );
		$element->add_render_attribute( '_wrapper', 'data-tilt-speed', (string) $tilt_speed );
		$element->add_render_attribute( '_wrapper', 'data-tilt-perspective', (string) $tilt_perspective );
		$element->add_render_attribute( '_wrapper', 'data-tilt-scale', (string) $tilt_scale );

		if ( in_array( $tilt_axis, array( 'x', 'y' ), true ) ) {
			$element->add_render_attribute( '_wrapper', 'data-tilt-axis', $tilt_axis );
		}

		if ( $tilt_reverse ) {
			$element->add_render_attribute( '_wrapper', 'data-tilt-reverse', 'true' );
		}

		if ( $tilt_glare ) {
			$element->add_render_attribute( '_wrapper', 'data-tilt-glare', 'true' );
			$element->add_render_attribute( '_wrapper', 'data-tilt-max-glare', (string) $tilt_max_glare );
		}
	}

	if ( ! empty( $settings['webmz_tadris_mouse_follow_enabled'] ) && 'yes' === $settings['webmz_tadris_mouse_follow_enabled'] ) {
		$follow_intensity = isset( $settings['webmz_tadris_mouse_follow_intensity']['size'] ) ? (float) $settings['webmz_tadris_mouse_follow_intensity']['size'] : 25.0;
		$follow_speed     = isset( $settings['webmz_tadris_mouse_follow_speed']['size'] ) ? (float) $settings['webmz_tadris_mouse_follow_speed']['size'] : 0.22;
		$follow_axis      = ! empty( $settings['webmz_tadris_mouse_follow_axis'] ) ? sanitize_key( $settings['webmz_tadris_mouse_follow_axis'] ) : 'both';
		$follow_reverse   = ! empty( $settings['webmz_tadris_mouse_follow_direction'] ) && 'reverse' === $settings['webmz_tadris_mouse_follow_direction'];
		$follow_scope     = webmz_elementor_get_mouse_animation_scope( $settings, 'webmz_tadris_mouse_follow_scope' );

		if ( ! in_array( $follow_axis, array( 'both', 'x', 'y' ), true ) ) {
			$follow_axis = 'both';
		}

		$element->add_render_attribute( '_wrapper', 'class', 'webmz-tadris-mouse-follow' );
		$element->add_render_attribute( '_wrapper', 'data-webmz-mouse-follow-intensity', (string) $follow_intensity );
		$element->add_render_attribute( '_wrapper', 'data-webmz-mouse-follow-speed', (string) $follow_speed );
		$element->add_render_attribute( '_wrapper', 'data-webmz-mouse-follow-axis', $follow_axis );
		$element->add_render_attribute( '_wrapper', 'data-webmz-mouse-follow-reverse', $follow_reverse ? '1' : '0' );
		$element->add_render_attribute( '_wrapper', 'data-webmz-mouse-follow-scope', $follow_scope );
	}

	if ( ! empty( $settings['webmz_tadris_mouse_magnetic_enabled'] ) && 'yes' === $settings['webmz_tadris_mouse_magnetic_enabled'] ) {
		$magnetic_strength = isset( $settings['webmz_tadris_mouse_magnetic_strength']['size'] ) ? (float) $settings['webmz_tadris_mouse_magnetic_strength']['size'] : 20.0;
		$magnetic_radius   = isset( $settings['webmz_tadris_mouse_magnetic_radius']['size'] ) ? (float) $settings['webmz_tadris_mouse_magnetic_radius']['size'] : 150.0;
		$magnetic_speed    = isset( $settings['webmz_tadris_mouse_magnetic_speed']['size'] ) ? (float) $settings['webmz_tadris_mouse_magnetic_speed']['size'] : 0.25;
		$magnetic_scope    = webmz_elementor_get_mouse_animation_scope( $settings, 'webmz_tadris_mouse_magnetic_scope' );

		$element->add_render_attribute( '_wrapper', 'class', 'webmz-tadris-mouse-magnetic' );
		$element->add_render_attribute( '_wrapper', 'data-webmz-mouse-magnetic-strength', (string) $magnetic_strength );
		$element->add_render_attribute( '_wrapper', 'data-webmz-mouse-magnetic-radius', (string) $magnetic_radius );
		$element->add_render_attribute( '_wrapper', 'data-webmz-mouse-magnetic-speed', (string) $magnetic_speed );
		$element->add_render_attribute( '_wrapper', 'data-webmz-mouse-magnetic-scope', $magnetic_scope );
	}

	if ( ! empty( $settings['webmz_tadris_mouse_spotlight_enabled'] ) && 'yes' === $settings['webmz_tadris_mouse_spotlight_enabled'] ) {
		$spotlight_color = ! empty( $settings['webmz_tadris_mouse_spotlight_color'] ) ? $settings['webmz_tadris_mouse_spotlight_color'] : 'rgba(255,255,255,0.35)';
		$spotlight_size  = isset( $settings['webmz_tadris_mouse_spotlight_size']['size'] ) ? (float) $settings['webmz_tadris_mouse_spotlight_size']['size'] : 60.0;
		$spotlight_scope = webmz_elementor_get_mouse_animation_scope( $settings, 'webmz_tadris_mouse_spotlight_scope' );

		$element->add_render_attribute( '_wrapper', 'class', 'webmz-tadris-mouse-spotlight' );
		$element->add_render_attribute( '_wrapper', 'data-webmz-mouse-spotlight-scope', $spotlight_scope );
		$element->add_render_attribute(
			'_wrapper',
			'style',
			'--webmz-mouse-spotlight-color:' . esc_attr( $spotlight_color ) . ';--webmz-mouse-spotlight-size:' . esc_attr( $spotlight_size ) . '%;'
		);
	}
}
add_action( 'elementor/element/after_add_attributes', 'webmz_elementor_add_tadris_animation_render_attributes' );
add_action( 'elementor/frontend/before_render', 'webmz_elementor_add_tadris_animation_render_attributes' );
