<?php
/**
 * Zhaket product tabs widget — AJAX tabbed product carousel with hover cards.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Marketplace-style tabbed product showcase.
 */
class Zhaket_Product_Tabs_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-zhaket-product-tabs';
	}

	public function get_title() {
		return esc_html__( 'تب محصولات ژاکت', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_categories() {
		return array( 'webmz-shop-widgets' );
	}

	public function get_keywords() {
		return array( 'zhaket', 'product', 'tab', 'carousel', 'ژاکت', 'محصول', 'تب' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-tadris-widgets', 'webmz-header-commerce', 'webmz-zhaket-product-tabs' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-zhaket-product-tabs' );
	}

	/**
	 * @return array<string,string>
	 */
	private function category_options() {
		$options = array( '' => esc_html__( '— انتخاب دسته —', 'tadris' ) );
		$terms   = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}

		return $options;
	}

	/**
	 * @return array<string,string>
	 */
	private function tag_options() {
		$options = array( '' => esc_html__( '— انتخاب برچسب —', 'tadris' ) );
		$terms   = get_terms(
			array(
				'taxonomy'   => 'product_tag',
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}

		return $options;
	}

	/**
	 * @return string
	 */
	private function theme_primary_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_primary' ) : '#ffad00';
	}

	/**
	 * @return string
	 */
	private function theme_secondary_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_secondary' ) : '#092c4c';
	}

	/**
	 * @return string
	 */
	private function theme_text_dark_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_text_dark' ) : '#111827';
	}

	/**
	 * @return string
	 */
	private function theme_text_gray_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_text_gray' ) : '#64748b';
	}

	/**
	 * @return string
	 */
	private function theme_background_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_background' ) : '#1c1e29';
	}

	protected function register_controls() {
		$this->register_header_controls();
		$this->register_tabs_controls();
		$this->register_query_controls();
		$this->register_labels_controls();
		$this->register_style_controls();
	}

	protected function register_header_controls() {
		$this->start_controls_section(
			'header',
			array(
				'label' => esc_html__( 'سربرگ', 'tadris' ),
			)
		);

		$this->add_control(
			'color_scheme',
			array(
				'label'   => esc_html__( 'حالت رنگ', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'light',
				'options' => array(
					'light' => esc_html__( 'لایت', 'tadris' ),
					'dark'  => esc_html__( 'دارک', 'tadris' ),
					'auto'  => esc_html__( 'خودکار (سیستم)', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'show_section_title',
			array(
				'label'        => esc_html__( 'نمایش عنوان بخش', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_badge',
			array(
				'label'        => esc_html__( 'نمایش نشان آیکون', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'show_section_title' => 'yes' ),
			)
		);

		$this->add_control(
			'badge_icon',
			array(
				'label'     => esc_html__( 'آیکون نشان', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-desktop',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_section_title' => 'yes',
					'show_badge'         => 'yes',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'badge_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون نشان', 'tadris' ),
			array(
				'show_section_title' => 'yes',
				'show_badge'         => 'yes',
			)
		);

		$this->add_control(
			'section_title',
			array(
				'label'       => esc_html__( 'عنوان بخش', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'کسب و کار خود را آنلاین کنید', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_section_title' => 'yes' ),
			)
		);

		$this->webmz_register_title_tag_control(
			'section_title_tag',
			esc_html__( 'تگ HTML عنوان بخش', 'tadris' ),
			array( 'show_section_title' => 'yes' )
		);

		$this->add_control(
			'show_view_all',
			array(
				'label'        => esc_html__( 'نمایش «مشاهده همه»', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'view_all_text',
			array(
				'label'     => esc_html__( 'متن لینک', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'مشاهده همه', 'tadris' ),
				'condition' => array( 'show_view_all' => 'yes' ),
			)
		);

		$this->add_control(
			'view_all_link',
			array(
				'label'     => esc_html__( 'لینک', 'tadris' ),
				'type'      => Controls_Manager::URL,
				'default'   => array(
					'url' => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '#',
				),
				'condition' => array( 'show_view_all' => 'yes' ),
			)
		);

		$this->add_control(
			'view_all_icon',
			array(
				'label'     => esc_html__( 'آیکون لینک', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
				'condition' => array( 'show_view_all' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_tabs_controls() {
		$this->start_controls_section(
			'tabs',
			array(
				'label' => esc_html__( 'تب‌ها', 'tadris' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'tab_label',
			array(
				'label'       => esc_html__( 'عنوان تب', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'همه', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'filter_type',
			array(
				'label'   => esc_html__( 'منبع محصولات', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => array(
					'all'      => esc_html__( 'همه محصولات', 'tadris' ),
					'category' => esc_html__( 'دسته‌بندی', 'tadris' ),
					'tag'      => esc_html__( 'برچسب', 'tadris' ),
				),
			)
		);

		$repeater->add_control(
			'category',
			array(
				'label'     => esc_html__( 'دسته محصول', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->category_options(),
				'condition' => array( 'filter_type' => 'category' ),
			)
		);

		$repeater->add_control(
			'tag',
			array(
				'label'     => esc_html__( 'برچسب محصول', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->tag_options(),
				'condition' => array( 'filter_type' => 'tag' ),
			)
		);

		$this->add_control(
			'tab_items',
			array(
				'label'       => esc_html__( 'لیست تب‌ها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ tab_label || filter_type }}}',
				'default'     => array(
					array(
						'tab_label'   => esc_html__( 'همه', 'tadris' ),
						'filter_type' => 'all',
					),
					array(
						'tab_label'   => esc_html__( 'آموزشی', 'tadris' ),
						'filter_type' => 'category',
						'category'    => '',
					),
					array(
						'tab_label'   => esc_html__( 'فروشگاهی', 'tadris' ),
						'filter_type' => 'category',
						'category'    => '',
					),
					array(
						'tab_label'   => esc_html__( 'شرکتی', 'tadris' ),
						'filter_type' => 'category',
						'category'    => '',
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_query_controls() {
		$this->start_controls_section(
			'query',
			array(
				'label' => esc_html__( 'کوئری و چیدمان', 'tadris' ),
			)
		);

		$this->add_control(
			'display_type',
			array(
				'label'   => esc_html__( 'نحوه نمایش', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slider',
				'options' => array(
					'slider' => esc_html__( 'اسلایدی (Swiper)', 'tadris' ),
					'grid'   => esc_html__( 'گرید', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'count',
			array(
				'label'   => esc_html__( 'تعداد محصولات در هر تب', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8,
				'min'     => 1,
				'max'     => 24,
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'   => esc_html__( 'مرتب‌سازی', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'       => esc_html__( 'جدیدترین', 'tadris' ),
					'popularity' => esc_html__( 'پرفروش‌ترین', 'tadris' ),
					'rating'     => esc_html__( 'بالاترین امتیاز', 'tadris' ),
					'rand'       => esc_html__( 'تصادفی', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'grid_columns_desktop',
			array(
				'label'     => esc_html__( 'تعداد ستون دسکتاپ', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '4',
				'options'   => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' ),
				'condition' => array( 'display_type' => 'grid' ),
			)
		);

		$this->add_control(
			'grid_columns_tablet',
			array(
				'label'     => esc_html__( 'تعداد ستون تبلت', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '2',
				'options'   => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
				'condition' => array( 'display_type' => 'grid' ),
			)
		);

		$this->add_control(
			'grid_columns_mobile',
			array(
				'label'     => esc_html__( 'تعداد ستون موبایل', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '1',
				'options'   => array( '1' => '1', '2' => '2' ),
				'condition' => array( 'display_type' => 'grid' ),
			)
		);

		foreach ( array(
			'desktop' => esc_html__( 'دسکتاپ', 'tadris' ),
			'tablet'  => esc_html__( 'تبلت', 'tadris' ),
			'mobile'  => esc_html__( 'موبایل', 'tadris' ),
		) as $device => $label ) {
			$this->add_control(
				'slides_' . $device,
				array(
					'label'     => sprintf( esc_html__( 'تعداد اسلاید %s', 'tadris' ), $label ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 'mobile' === $device ? 1 : ( 'tablet' === $device ? 3 : 4 ),
					'min'       => 1,
					'max'       => 6,
					'condition' => array( 'display_type' => 'slider' ),
				)
			);
		}

		$this->add_responsive_control(
			'slide_gap',
			array(
				'label'      => esc_html__( 'فاصله بین کارت‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 20 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'slider_loop',
			array(
				'label'        => esc_html__( 'لوپ اسلایدر', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition'    => array( 'display_type' => 'slider' ),
			)
		);

		$this->add_control(
			'slider_autoplay',
			array(
				'label'        => esc_html__( 'پخش خودکار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition'    => array( 'display_type' => 'slider' ),
			)
		);

		$this->add_control(
			'slider_autoplay_delay',
			array(
				'label'     => esc_html__( 'زمان پخش خودکار (ms)', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4000,
				'condition' => array(
					'display_type'    => 'slider',
					'slider_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_slider_nav',
			array(
				'label'        => esc_html__( 'نمایش فلش‌های ناوبری', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'display_type' => 'slider' ),
			)
		);

		$this->add_control(
			'nav_icon_prev',
			array(
				'label'     => esc_html__( 'آیکون فلش قبلی', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'display_type'     => 'slider',
					'show_slider_nav'  => 'yes',
				),
			)
		);

		$this->add_control(
			'nav_icon_next',
			array(
				'label'     => esc_html__( 'آیکون فلش بعدی', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'display_type'     => 'slider',
					'show_slider_nav'  => 'yes',
				),
			)
		);

		$this->add_control(
			'show_slider_pagination',
			array(
				'label'        => esc_html__( 'نمایش صفحه‌بندی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'display_type' => 'slider' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_labels_controls() {
		$this->start_controls_section(
			'labels',
			array(
				'label' => esc_html__( 'متن‌ها', 'tadris' ),
			)
		);

		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ HTML عنوان محصول', 'tadris' ) );

		$this->add_control(
			'add_to_cart_text',
			array(
				'label'   => esc_html__( 'متن افزودن به سبد', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'افزودن به سبد خرید', 'tadris' ),
			)
		);

		$this->add_control(
			'preview_text',
			array(
				'label'   => esc_html__( 'متن پیش‌نمایش', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'پیشنمایش', 'tadris' ),
			)
		);

		$this->add_control(
			'variable_text',
			array(
				'label'   => esc_html__( 'متن محصول متغیر', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'انتخاب گزینه‌ها', 'tadris' ),
			)
		);

		$this->add_control(
			'unavailable_text',
			array(
				'label'   => esc_html__( 'متن ناموجود', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'ناموجود', 'tadris' ),
			)
		);

		$this->add_control(
			'show_vendor',
			array(
				'label'        => esc_html__( 'نمایش فروشنده', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'vendor_source',
			array(
				'label'     => esc_html__( 'منبع نام فروشنده', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'badge',
				'options'   => array(
					'badge'  => esc_html__( 'متن متا (متاباکس لوپ فایل)', 'tadris' ),
					'author' => esc_html__( 'نام نویسنده محصول', 'tadris' ),
				),
				'condition' => array( 'show_vendor' => 'yes' ),
			)
		);

		$this->add_control(
			'vendor_icon_source',
			array(
				'label'     => esc_html__( 'منبع آیکون فروشنده', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'loop_thumb',
				'options'   => array(
					'loop_thumb' => esc_html__( 'تامنیل لوپ فایل‌ها', 'tadris' ),
					'author'     => esc_html__( 'آواتار نویسنده', 'tadris' ),
				),
				'condition' => array( 'show_vendor' => 'yes' ),
			)
		);

		$this->add_control(
			'vendor_fallback',
			array(
				'label'       => esc_html__( 'متن پیش‌فرض فروشنده', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'condition'   => array( 'show_vendor' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'box_style',
			array(
				'label' => esc_html__( 'باکس اصلی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'section_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-surface: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'section_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zpt' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'section_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zpt' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'header_style',
			array(
				'label' => esc_html__( 'سربرگ', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'header_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-text: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'header_title_typography',
				'selector' => '{{WRAPPER}} .webmz-zpt__title',
			)
		);

		$this->add_control(
			'badge_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه نشان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-badge-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_border_color',
			array(
				'label'     => esc_html__( 'حاشیه / رنگ نشان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-badge-border: {{VALUE}}; --webmz-zpt-badge-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'view_all_color',
			array(
				'label'     => esc_html__( 'رنگ «مشاهده همه»', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-view-all-text: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'view_all_border_color',
			array(
				'label'     => esc_html__( 'حاشیه «مشاهده همه»', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-view-all-border: {{VALUE}};',
					'{{WRAPPER}} .webmz-zpt__view-all-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'view_all_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه «مشاهده همه»', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-view-all-bg: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_heading_icon_box_style_controls(
			'.webmz-zpt__badge',
			array(
				'condition' => array(
					'show_section_title' => 'yes',
					'show_badge'         => 'yes',
				),
			)
		);

		$this->start_controls_section(
			'tabs_style',
			array(
				'label' => esc_html__( 'تب‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'tab_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-tab-text: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-tab-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_active_color',
			array(
				'label'     => esc_html__( 'رنگ متن فعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-tab-active-text: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_active_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه فعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-tab-active-bg: {{VALUE}}; --webmz-zpt-primary: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tab_typography',
				'selector' => '{{WRAPPER}} .webmz-zpt__tab',
			)
		);

		$this->add_responsive_control(
			'tab_radius',
			array(
				'label'      => esc_html__( 'گردی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zpt__tab' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'card_style',
			array(
				'label' => esc_html__( 'کارت محصول', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'گردی کارت', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 16 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zpt__card' => 'border-radius: {{SIZE}}{{UNIT}}; --webmz-zpt-card-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-zpt__card-media' => 'border-radius: {{SIZE}}{{UNIT}}; --webmz-zpt-card-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'card_hover_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt__card:hover, {{WRAPPER}} .webmz-zpt__card.is-hovered' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_hover_shadow',
				'label'    => esc_html__( 'سایه هاور', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-zpt__card:hover, {{WRAPPER}} .webmz-zpt__card.is-hovered',
			)
		);

		$this->add_control(
			'card_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt__card-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .webmz-zpt__card:hover .webmz-zpt__card-title, {{WRAPPER}} .webmz-zpt__card.is-hovered .webmz-zpt__card-title' => 'color: ' . $this->theme_text_dark_color() . ';',
				),
			)
		);

		$this->add_control(
			'card_price_color',
			array(
				'label'     => esc_html__( 'رنگ قیمت', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt__price' => 'color: {{VALUE}};',
					'{{WRAPPER}} .webmz-zpt__card:hover .webmz-zpt__price, {{WRAPPER}} .webmz-zpt__card.is-hovered .webmz-zpt__price' => 'color: ' . $this->theme_text_dark_color() . ';',
				),
			)
		);

		$this->add_control(
			'card_vendor_color',
			array(
				'label'     => esc_html__( 'رنگ فروشنده', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.85)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt__card-vendor-name' => 'color: {{VALUE}};',
					'{{WRAPPER}} .webmz-zpt__card:hover .webmz-zpt__card-vendor-name, {{WRAPPER}} .webmz-zpt__card.is-hovered .webmz-zpt__card-vendor-name' => 'color: ' . $this->theme_text_gray_color() . ';',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'buttons_style',
			array(
				'label' => esc_html__( 'دکمه‌های هاور', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'cart_btn_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه افزودن به سبد', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt__btn--cart:not(.is-disabled):not(.is-variable)' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cart_btn_color',
			array(
				'label'     => esc_html__( 'رنگ متن افزودن به سبد', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt__btn--cart' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'preview_btn_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه پیش‌نمایش', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f1f5f9',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt__btn--preview' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'preview_btn_color',
			array(
				'label'     => esc_html__( 'رنگ متن پیش‌نمایش', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_text_gray_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt__btn--preview' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'btn_radius',
			array(
				'label'      => esc_html__( 'گردی دکمه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 12 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zpt__btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'nav_style',
			array(
				'label' => esc_html__( 'فلش‌های اسلایدر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'nav_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-nav-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-nav-text: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_border_color',
			array(
				'label'     => esc_html__( 'حاشیه فلش‌ها', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpt' => '--webmz-zpt-nav-border: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @param array<string,mixed> $tab Tab settings.
	 * @return string
	 */
	private function resolve_tab_label( $tab ) {
		if ( ! empty( $tab['tab_label'] ) ) {
			return (string) $tab['tab_label'];
		}

		$filter_type = isset( $tab['filter_type'] ) ? sanitize_key( $tab['filter_type'] ) : 'all';

		if ( 'category' === $filter_type && ! empty( $tab['category'] ) ) {
			$term = get_term_by( 'slug', sanitize_title( (string) $tab['category'] ), 'product_cat' );
			if ( $term && ! is_wp_error( $term ) ) {
				return $term->name;
			}
		}

		if ( 'tag' === $filter_type && ! empty( $tab['tag'] ) ) {
			$term = get_term_by( 'slug', sanitize_title( (string) $tab['tag'] ), 'product_tag' );
			if ( $term && ! is_wp_error( $term ) ) {
				return $term->name;
			}
		}

		return esc_html__( 'همه محصولات', 'tadris' );
	}

	/**
	 * @param array<string,mixed> $s Widget settings.
	 * @return array<string,mixed>
	 */
	private function build_card_args( $s ) {
		return array(
			'title_tag'          => $this->webmz_get_title_tag( $s, 'title_tag' ),
			'add_to_cart_text'   => $s['add_to_cart_text'] ?? '',
			'preview_text'       => $s['preview_text'] ?? '',
			'variable_text'      => $s['variable_text'] ?? '',
			'unavailable_text'   => $s['unavailable_text'] ?? '',
			'show_vendor'        => isset( $s['show_vendor'] ) && 'yes' === $s['show_vendor'],
			'vendor_source'      => $s['vendor_source'] ?? 'badge',
			'vendor_icon_source' => $s['vendor_icon_source'] ?? 'loop_thumb',
			'vendor_fallback'    => $s['vendor_fallback'] ?? '',
		);
	}

	/**
	 * @param array<string,mixed> $s    Widget settings.
	 * @param string              $key  Icon control key.
	 * @return string
	 */
	private function render_nav_icon_html( $s, $key ) {
		if ( empty( $s[ $key ]['value'] ) ) {
			return '';
		}

		ob_start();
		Icons_Manager::render_icon( $s[ $key ], array( 'aria-hidden' => 'true' ) );

		return (string) ob_get_clean();
	}

	/**
	 * @param array<string,mixed> $s Widget settings.
	 * @return array<string,mixed>
	 */
	private function build_frontend_config( $s ) {
		$tabs = array();

		if ( ! empty( $s['tab_items'] ) && is_array( $s['tab_items'] ) ) {
			foreach ( array_values( $s['tab_items'] ) as $tab ) {
				$tabs[] = array(
					'filter_type' => isset( $tab['filter_type'] ) ? sanitize_key( $tab['filter_type'] ) : 'all',
					'category'    => isset( $tab['category'] ) ? sanitize_title( (string) $tab['category'] ) : '',
					'tag'         => isset( $tab['tag'] ) ? sanitize_title( (string) $tab['tag'] ) : '',
					'label'       => $this->resolve_tab_label( $tab ),
				);
			}
		}

		return array(
			'displayType'         => 'grid' === ( $s['display_type'] ?? 'slider' ) ? 'grid' : 'slider',
			'count'               => min( 24, max( 1, absint( $s['count'] ?? 8 ) ) ),
			'order_by'            => sanitize_key( $s['order_by'] ?? 'date' ),
			'gridColumnsDesktop'  => absint( $s['grid_columns_desktop'] ?? 4 ),
			'gridColumnsTablet'   => absint( $s['grid_columns_tablet'] ?? 2 ),
			'gridColumnsMobile'   => absint( $s['grid_columns_mobile'] ?? 1 ),
			'slidesDesktop'       => absint( $s['slides_desktop'] ?? 4 ),
			'slidesTablet'        => absint( $s['slides_tablet'] ?? 3 ),
			'slidesMobile'        => absint( $s['slides_mobile'] ?? 1 ),
			'spaceBetween'        => isset( $s['slide_gap']['size'] ) ? absint( $s['slide_gap']['size'] ) : 20,
			'sliderLoop'          => isset( $s['slider_loop'] ) && 'yes' === $s['slider_loop'],
			'sliderAutoplay'      => isset( $s['slider_autoplay'] ) && 'yes' === $s['slider_autoplay'],
			'sliderAutoplayDelay' => absint( $s['slider_autoplay_delay'] ?? 4000 ),
			'sliderNavigation'    => ! isset( $s['show_slider_nav'] ) || 'yes' === $s['show_slider_nav'],
			'sliderPagination'    => ! isset( $s['show_slider_pagination'] ) || 'yes' === $s['show_slider_pagination'],
			'navIconPrevHtml'     => $this->render_nav_icon_html( $s, 'nav_icon_prev' ),
			'navIconNextHtml'     => $this->render_nav_icon_html( $s, 'nav_icon_next' ),
			'tabs'                => $tabs,
			'cardArgs'            => $this->build_card_args( $s ),
		);
	}

	protected function render() {
		if ( ! function_exists( 'wc_get_product' ) || ! function_exists( 'webmz_zhaket_product_tabs_render_panel_html' ) ) {
			return;
		}

		$s    = $this->get_settings_for_display();
		$tabs = ! empty( $s['tab_items'] ) && is_array( $s['tab_items'] ) ? array_values( $s['tab_items'] ) : array();

		if ( empty( $tabs ) ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'حداقل یک تب اضافه کنید.', 'tadris' ) . '</div>';
			return;
		}

		$config            = $this->build_frontend_config( $s );
		$show_title        = isset( $s['show_section_title'] ) && 'yes' === $s['show_section_title'];
		$show_badge        = $show_title && isset( $s['show_badge'] ) && 'yes' === $s['show_badge'];
		$title_tag         = $this->webmz_get_title_tag( $s, 'section_title_tag' );
		$view_all          = isset( $s['show_view_all'] ) && 'yes' === $s['show_view_all'];
		$view_link         = isset( $s['view_all_link'] ) && is_array( $s['view_all_link'] ) ? $s['view_all_link'] : array();
		$view_url          = ! empty( $view_link['url'] ) ? $view_link['url'] : '#';
		$view_target       = ! empty( $view_link['is_external'] ) ? ' target="_blank"' : '';
		$view_rel          = ! empty( $view_link['nofollow'] ) ? ' rel="nofollow"' : '';
		$has_header        = $show_title || $view_all;
		$scheme            = isset( $s['color_scheme'] ) ? sanitize_key( $s['color_scheme'] ) : 'light';
		$scheme_class      = in_array( $scheme, array( 'light', 'dark', 'auto' ), true ) ? 'webmz-zpt--' . $scheme : 'webmz-zpt--light';
		?>
		<section
			class="webmz-zpt <?php echo esc_attr( $scheme_class ); ?>"
			dir="rtl"
			data-webmz-zhaket-product-tabs="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
		>
			<?php if ( $has_header ) : ?>
				<div class="webmz-zpt__header">
					<?php if ( $show_title ) : ?>
						<div class="webmz-zpt__lead">
							<?php if ( $show_badge && ! empty( $s['badge_icon']['value'] ) ) : ?>
								<span class="webmz-zpt__badge <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s, 'badge_icon_color_mode' ) ); ?>" aria-hidden="true">
									<?php Icons_Manager::render_icon( $s['badge_icon'], array( 'aria-hidden' => 'true' ) ); ?>
								</span>
							<?php endif; ?>

							<?php if ( ! empty( $s['section_title'] ) ) : ?>
								<<?php echo esc_html( $title_tag ); ?> class="webmz-zpt__title"><?php echo esc_html( $s['section_title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $view_all ) : ?>
						<a class="webmz-zpt__view-all" href="<?php echo esc_url( $view_url ); ?>"<?php echo $view_target . $view_rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<span><?php echo esc_html( $s['view_all_text'] ?? esc_html__( 'مشاهده همه', 'tadris' ) ); ?></span>
							<?php if ( ! empty( $s['view_all_icon']['value'] ) ) : ?>
								<span class="webmz-zpt__view-all-icon" aria-hidden="true">
									<?php Icons_Manager::render_icon( $s['view_all_icon'], array( 'aria-hidden' => 'true' ) ); ?>
								</span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="webmz-zpt__tabs" role="tablist">
				<?php foreach ( $tabs as $index => $tab ) : ?>
					<button
						type="button"
						class="webmz-zpt__tab<?php echo 0 === $index ? ' is-active' : ''; ?>"
						role="tab"
						aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
						data-webmz-zpt-tab="<?php echo esc_attr( $index ); ?>"
					><?php echo esc_html( $this->resolve_tab_label( $tab ) ); ?></button>
				<?php endforeach; ?>
			</div>

			<div class="webmz-zpt__panels">
				<?php foreach ( $tabs as $index => $tab ) : ?>
					<div
						class="webmz-zpt__panel<?php echo 0 === $index ? ' is-active' : ''; ?>"
						data-webmz-zpt-panel="<?php echo esc_attr( $index ); ?>"
						role="tabpanel"
						<?php echo 0 !== $index ? ' hidden' : ''; ?>
					>
						<div class="webmz-zpt__panel-inner">
							<?php
							if ( 0 === $index ) {
								echo webmz_zhaket_product_tabs_render_panel_html( $config, $index ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div>
						<div class="webmz-zpt__loading" hidden aria-hidden="true">
							<span class="webmz-zpt__loading-spinner"></span>
							<span class="webmz-zpt__loading-text"><?php esc_html_e( 'در حال بارگذاری...', 'tadris' ); ?></span>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}
