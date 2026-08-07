<?php
/**
 * Tabbed product loop widget with AJAX tab loading.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce products displayed in filterable tabs.
 */
class Tadris_Tabbed_Product_Loop_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-tadris-tabbed-product-loop';
	}

	public function get_title() {
		return esc_html__( 'لوپ محصولات تب‌دار', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_categories() {
		return array( 'webmz-shop-widgets' );
	}

	public function get_keywords() {
		return array( 'product', 'tab', 'course', 'woocommerce', 'محصول', 'تب', 'دوره' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-tadris-widgets', 'webmz-tabbed-product-loop' );
	}

	public function get_style_depends() {
		$depends = array( 'webmz-swiper', 'webmz-tabbed-product-loop' );

		if ( function_exists( 'webmz_get_product_loop_styles' ) ) {
			foreach ( webmz_get_product_loop_styles() as $style ) {
				if ( ! empty( $style['style_depends'] ) && is_array( $style['style_depends'] ) ) {
					$depends = array_merge( $depends, $style['style_depends'] );
				}
			}
		} else {
			$depends[] = 'webmz-tadris-product-loop-3';
		}

		return array_values( array_unique( $depends ) );
	}

	/**
	 * Product category options.
	 *
	 * @return array<string,string>
	 */
	private function category_options() {
		$options = array(
			'' => esc_html__( '— انتخاب دسته —', 'tadris' ),
		);

		$terms = get_terms(
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
	 * Product tag options.
	 *
	 * @return array<string,string>
	 */
	private function tag_options() {
		$options = array(
			'' => esc_html__( '— انتخاب برچسب —', 'tadris' ),
		);

		$terms = get_terms(
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
			'section_title',
			array(
				'label'       => esc_html__( 'عنوان بخش', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دوره‌های آموزشی', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control( 'section_title_tag', esc_html__( 'تگ HTML عنوان بخش', 'tadris' ) );

		$this->add_control(
			'show_view_all',
			array(
				'label'        => esc_html__( 'نمایش لینک «مشاهده همه»', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'view_all_text',
			array(
				'label'     => esc_html__( 'متن لینک', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'همه دوره‌ها', 'tadris' ),
				'condition' => array(
					'show_view_all' => 'yes',
				),
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
				'condition' => array(
					'show_view_all' => 'yes',
				),
			)
		);

		$this->add_control(
			'view_all_icon',
			array(
				'label'     => esc_html__( 'آیکون لینک', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-arrow-up-right-from-square',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_view_all' => 'yes',
				),
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
				'default'     => esc_html__( 'تمامی محصولات', 'tadris' ),
				'label_block' => true,
				'description' => esc_html__( 'در صورت خالی بودن، نام دسته یا برچسب انتخاب‌شده نمایش داده می‌شود.', 'tadris' ),
			)
		);

		$repeater->add_control(
			'filter_type',
			array(
				'label'   => esc_html__( 'نوع فیلتر', 'tadris' ),
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
				'condition' => array(
					'filter_type' => 'category',
				),
			)
		);

		$repeater->add_control(
			'tag',
			array(
				'label'     => esc_html__( 'برچسب محصول', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->tag_options(),
				'condition' => array(
					'filter_type' => 'tag',
				),
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
						'tab_label'   => esc_html__( 'تمامی محصولات', 'tadris' ),
						'filter_type' => 'all',
					),
					array(
						'tab_label'   => esc_html__( 'فرانت‌اند', 'tadris' ),
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
			'loop_style',
			array(
				'label'   => esc_html__( 'استایل نمایش محصولات', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => function_exists( 'webmz_get_product_loop_style_options' ) ? webmz_get_product_loop_style_options() : array(),
			)
		);

		$this->add_control(
			'count',
			array(
				'label'   => esc_html__( 'تعداد محصولات در هر تب', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4,
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
			'display_type',
			array(
				'label'   => esc_html__( 'نحوه نمایش', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid'   => esc_html__( 'گرید', 'tadris' ),
					'slider' => esc_html__( 'اسلایدی (Swiper)', 'tadris' ),
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

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'فاصله بین آیتم‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 20 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-tpl__panel-inner' => '--webmz-tpl-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		foreach ( array( 'desktop' => esc_html__( 'دسکتاپ', 'tadris' ), 'tablet' => esc_html__( 'تبلت', 'tadris' ), 'mobile' => esc_html__( 'موبایل', 'tadris' ) ) as $device => $label ) {
			$this->add_control(
				'slides_' . $device,
				array(
					'label'     => sprintf( esc_html__( 'تعداد اسلاید %s', 'tadris' ), $label ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 'mobile' === $device ? 1 : ( 'tablet' === $device ? 2 : 4 ),
					'min'       => 1,
					'max'       => 6,
					'condition' => array( 'display_type' => 'slider' ),
				)
			);
		}

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
				'label'     => esc_html__( 'زمان پخش خودکار (میلی‌ثانیه)', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3500,
				'min'       => 1000,
				'step'      => 100,
				'condition' => array(
					'display_type'    => 'slider',
					'slider_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_slider_nav',
			array(
				'label'        => esc_html__( 'نمایش فلش‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition'    => array( 'display_type' => 'slider' ),
			)
		);

		$this->add_control(
			'show_slider_pagination',
			array(
				'label'        => esc_html__( 'نمایش صفحه‌بندی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
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
				'label' => esc_html__( 'متن و آیکون‌ها', 'tadris' ),
			)
		);

		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ HTML عنوان محصول', 'tadris' ) );

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'متن دکمه', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مشاهده دوره', 'tadris' ),
			)
		);

		$this->add_control(
			'variable_button_text',
			array(
				'label'   => esc_html__( 'متن دکمه محصول متغیر', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'انتخاب گزینه‌ها', 'tadris' ),
			)
		);

		$this->add_control(
			'unavailable_button_text',
			array(
				'label'   => esc_html__( 'متن دکمه ناموجود', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'ناموجود', 'tadris' ),
			)
		);

		$this->add_control(
			'sessions_suffix',
			array(
				'label'   => esc_html__( 'پسوند تعداد درس‌ها', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'درس', 'tadris' ),
			)
		);

		$this->add_control(
			'students_suffix',
			array(
				'label'   => esc_html__( 'پسوند تعداد دانشجویان', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'نفر', 'tadris' ),
			)
		);

		$this->add_control(
			'excerpt_words',
			array(
				'label'   => esc_html__( 'تعداد کلمات خلاصه', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 10,
				'min'     => 4,
				'max'     => 40,
			)
		);

		$this->add_control(
			'price_label',
			array(
				'label'     => esc_html__( 'برچسب قیمت', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'قیمت دوره', 'tadris' ),
				'condition' => array( 'loop_style' => '2' ),
			)
		);

		$this->add_control(
			'rating_label',
			array(
				'label'     => esc_html__( 'برچسب امتیاز', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'امتیاز دانشجویان', 'tadris' ),
				'condition' => array( 'loop_style' => '1' ),
			)
		);

		$icon_controls = array(
			'rating_icon'     => array( 'fas fa-star', 'fa-solid', esc_html__( 'آیکون ستاره امتیاز', 'tadris' ), 'rating_icon_color_mode' ),
			'students_icon'   => array( 'fas fa-user-friends', 'fa-solid', esc_html__( 'آیکون دانشجویان', 'tadris' ), 'students_icon_color_mode' ),
			'reviews_icon'    => array( 'far fa-comment', 'fa-regular', esc_html__( 'آیکون دیدگاه‌ها', 'tadris' ), 'reviews_icon_color_mode' ),
			'duration_icon'   => array( 'far fa-clock', 'fa-regular', esc_html__( 'آیکون مدت دوره', 'tadris' ), 'duration_icon_color_mode' ),
			'sessions_icon'   => array( 'far fa-calendar-alt', 'fa-regular', esc_html__( 'آیکون درس‌ها', 'tadris' ), 'sessions_icon_color_mode' ),
			'instructor_icon' => array( 'fas fa-chalkboard-teacher', 'fa-solid', esc_html__( 'آیکون مدرس', 'tadris' ), 'instructor_icon_color_mode' ),
		);

		foreach ( $icon_controls as $key => $meta ) {
			$this->add_control(
				$key,
				array(
					'label'   => $meta[2],
					'type'    => Controls_Manager::ICONS,
					'default' => array(
						'value'   => $meta[0],
						'library' => $meta[1],
					),
				)
			);
			$this->webmz_register_icon_color_mode_control( $meta[3], sprintf( esc_html__( 'نوع رنگ‌دهی %s', 'tadris' ), $meta[2] ) );
		}

		$this->end_controls_section();
	}

	/**
	 * Theme primary color from options.
	 *
	 * @return string
	 */
	private function theme_primary_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_primary' ) : '#0878f9';
	}

	/**
	 * Theme secondary color from options.
	 *
	 * @return string
	 */
	private function theme_secondary_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_secondary' ) : '#092c4c';
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'accent_style',
			array(
				'label' => esc_html__( 'رنگ اصلی کارت', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'primary_color',
			array(
				'label'     => esc_html__( 'رنگ اصلی (دکمه / قیمت / ستاره)', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}}' => '--webmz-tpl-primary: {{VALUE}}; --tpl3-primary: {{VALUE}}; --tpl2-primary: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'discount_color',
			array(
				'label'     => esc_html__( 'رنگ برچسب تخفیف', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}}' => '--tpl3-discount: {{VALUE}};',
				),
				'condition' => array( 'loop_style' => '3' ),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'section_style', esc_html__( 'باکس اصلی', 'tadris' ), '.webmz-tpl' );

		$this->start_controls_section(
			'header_style',
			array(
				'label' => esc_html__( 'سربرگ', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'header_gap',
			array(
				'label'     => esc_html__( 'فاصله عنوان و لینک', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-tpl__header' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'header_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_secondary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-tpl__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'header_title_typography',
				'selector' => '{{WRAPPER}} .webmz-tpl__title',
			)
		);

		$this->add_control(
			'view_all_color',
			array(
				'label'     => esc_html__( 'رنگ لینک «مشاهده همه»', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .webmz-tpl__view-all' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'view_all_typography',
				'selector' => '{{WRAPPER}} .webmz-tpl__view-all',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'tabs_style',
			array(
				'label' => esc_html__( 'تب‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'tabs_gap',
			array(
				'label'     => esc_html__( 'فاصله بین تب‌ها', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-tpl__tabs' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tabs_margin',
			array(
				'label'      => esc_html__( 'فاصله تب‌ها از محتوا', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 24 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-tpl__tabs' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'tab_color',
			array(
				'label'     => esc_html__( 'رنگ متن تب', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .webmz-tpl__tab' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه تب', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-tpl__tab' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_active_color',
			array(
				'label'     => esc_html__( 'رنگ متن تب فعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-tpl__tab.is-active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_active_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه تب فعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-tpl__tab.is-active' => 'background: {{VALUE}}; border-color: {{VALUE}}; --webmz-tpl-primary: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tab_typography',
				'selector' => '{{WRAPPER}} .webmz-tpl__tab',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'tab_border',
				'selector' => '{{WRAPPER}} .webmz-tpl__tab',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'tab_shadow',
				'selector' => '{{WRAPPER}} .webmz-tpl__tab',
			)
		);

		$this->add_responsive_control(
			'tab_radius',
			array(
				'label'      => esc_html__( 'گردی تب‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-tpl__tab' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tab_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی تب', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-tpl__tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'panel_style', esc_html__( 'پنل محصولات', 'tadris' ), '.webmz-tpl__panel-inner' );
	}

	/**
	 * Render icon HTML from settings.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @param string              $key      Icon setting key.
	 * @return string
	 */
	private function render_icon_html( $settings, $key ) {
		if ( empty( $settings[ $key ]['value'] ) ) {
			return '';
		}

		ob_start();
		Icons_Manager::render_icon( $settings[ $key ], array( 'aria-hidden' => 'true' ) );

		return (string) ob_get_clean();
	}

	/**
	 * Resolve tab label from repeater item.
	 *
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
	 * Build card args for loop renderers.
	 *
	 * @param array<string,mixed> $s Widget settings.
	 * @return array<string,mixed>
	 */
	private function build_card_args( $s ) {
		$loop_style = webmz_sanitize_product_loop_style( $s['loop_style'] ?? '1' );
		$common     = array(
			'title_tag'               => $this->webmz_get_title_tag( $s, 'title_tag' ),
			'button_text'             => $s['button_text'] ?? '',
			'variable_button_text'    => $s['variable_button_text'] ?? '',
			'unavailable_button_text' => $s['unavailable_button_text'] ?? '',
			'sessions_suffix'         => $s['sessions_suffix'] ?? '',
			'students_suffix'         => $s['students_suffix'] ?? '',
			'excerpt_words'           => absint( $s['excerpt_words'] ?? 10 ),
			'rating_icon_html'        => $this->render_icon_html( $s, 'rating_icon' ),
			'rating_icon_color_class' => $this->webmz_get_icon_color_mode_class( $s, 'rating_icon_color_mode' ),
			'students_icon_html'      => $this->render_icon_html( $s, 'students_icon' ),
			'students_icon_color_class' => $this->webmz_get_icon_color_mode_class( $s, 'students_icon_color_mode' ),
			'reviews_icon_html'       => $this->render_icon_html( $s, 'reviews_icon' ),
			'reviews_icon_color_class' => $this->webmz_get_icon_color_mode_class( $s, 'reviews_icon_color_mode' ),
			'duration_icon_html'      => $this->render_icon_html( $s, 'duration_icon' ),
			'duration_icon_color_class' => $this->webmz_get_icon_color_mode_class( $s, 'duration_icon_color_mode' ),
			'sessions_icon_html'      => $this->render_icon_html( $s, 'sessions_icon' ),
			'sessions_icon_color_class' => $this->webmz_get_icon_color_mode_class( $s, 'sessions_icon_color_mode' ),
			'instructor_icon_html'    => $this->render_icon_html( $s, 'instructor_icon' ),
			'instructor_icon_color_class' => $this->webmz_get_icon_color_mode_class( $s, 'instructor_icon_color_mode' ),
		);

		if ( '1' === $loop_style ) {
			return array_merge(
				$common,
				array(
					'show_rating'    => true,
					'show_course_meta' => true,
					'rating_label'   => $s['rating_label'] ?? '',
				)
			);
		}

		if ( '2' === $loop_style ) {
			return array_merge(
				$common,
				array(
					'price_label' => $s['price_label'] ?? '',
				)
			);
		}

		return $common;
	}

	/**
	 * Build frontend/AJAX config payload.
	 *
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
			'loopStyle'           => webmz_sanitize_product_loop_style( $s['loop_style'] ?? '1' ),
			'count'               => min( 24, max( 1, absint( $s['count'] ?? 4 ) ) ),
			'order_by'            => sanitize_key( $s['order_by'] ?? 'date' ),
			'displayType'         => 'slider' === ( $s['display_type'] ?? 'grid' ) ? 'slider' : 'grid',
			'gridGap'             => isset( $s['grid_gap']['size'] ) ? absint( $s['grid_gap']['size'] ) : 20,
			'gridColumnsDesktop'  => $s['grid_columns_desktop'] ?? '4',
			'gridColumnsTablet'   => $s['grid_columns_tablet'] ?? '2',
			'gridColumnsMobile'   => $s['grid_columns_mobile'] ?? '1',
			'slidesDesktop'       => absint( $s['slides_desktop'] ?? 4 ),
			'slidesTablet'        => absint( $s['slides_tablet'] ?? 2 ),
			'slidesMobile'        => absint( $s['slides_mobile'] ?? 1 ),
			'sliderLoop'          => isset( $s['slider_loop'] ) && 'yes' === $s['slider_loop'],
			'sliderAutoplay'      => isset( $s['slider_autoplay'] ) && 'yes' === $s['slider_autoplay'],
			'sliderAutoplayDelay' => absint( $s['slider_autoplay_delay'] ?? 3500 ),
			'sliderNavigation'    => isset( $s['show_slider_nav'] ) && 'yes' === $s['show_slider_nav'],
			'sliderPagination'    => ! isset( $s['show_slider_pagination'] ) || 'yes' === $s['show_slider_pagination'],
			'tabs'                => $tabs,
			'cardArgs'            => $this->build_card_args( $s ),
		);
	}

	protected function render() {
		if ( ! function_exists( 'wc_get_product' ) || ! function_exists( 'webmz_tabbed_product_loop_render_panel_html' ) ) {
			return;
		}

		$s    = $this->get_settings_for_display();
		$tabs = ! empty( $s['tab_items'] ) && is_array( $s['tab_items'] ) ? array_values( $s['tab_items'] ) : array();

		if ( empty( $tabs ) ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'حداقل یک تب اضافه کنید.', 'tadris' ) . '</div>';
			return;
		}

		$config      = $this->build_frontend_config( $s );
		$title_tag   = $this->webmz_get_title_tag( $s, 'section_title_tag' );
		$view_all    = isset( $s['show_view_all'] ) && 'yes' === $s['show_view_all'];
		$view_link   = isset( $s['view_all_link'] ) && is_array( $s['view_all_link'] ) ? $s['view_all_link'] : array();
		$view_url    = ! empty( $view_link['url'] ) ? $view_link['url'] : '#';
		$view_target = ! empty( $view_link['is_external'] ) ? ' target="_blank"' : '';
		$view_rel    = ! empty( $view_link['nofollow'] ) ? ' rel="nofollow"' : '';
		?>
		<section
			class="webmz-tpl webmz-tpl--style-<?php echo esc_attr( $config['loopStyle'] ); ?>"
			data-webmz-tabbed-product-loop="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
		>
			<div class="webmz-tpl__header">
				<?php if ( ! empty( $s['section_title'] ) ) : ?>
					<<?php echo esc_html( $title_tag ); ?> class="webmz-tpl__title"><?php echo esc_html( $s['section_title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
				<?php endif; ?>

				<?php if ( $view_all ) : ?>
					<a class="webmz-tpl__view-all" href="<?php echo esc_url( $view_url ); ?>"<?php echo $view_target . $view_rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<span><?php echo esc_html( $s['view_all_text'] ?? esc_html__( 'همه دوره‌ها', 'tadris' ) ); ?></span>
						<?php if ( ! empty( $s['view_all_icon']['value'] ) ) : ?>
							<span class="webmz-tpl__view-all-icon" aria-hidden="true">
								<?php Icons_Manager::render_icon( $s['view_all_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							</span>
						<?php endif; ?>
					</a>
				<?php endif; ?>
			</div>

			<div class="webmz-tpl__tabs" role="tablist" aria-label="<?php echo esc_attr( $s['section_title'] ?? esc_html__( 'فیلتر محصولات', 'tadris' ) ); ?>">
				<?php foreach ( $tabs as $index => $tab ) : ?>
					<button
						type="button"
						class="webmz-tpl__tab<?php echo 0 === $index ? ' is-active' : ''; ?>"
						role="tab"
						aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
						data-webmz-tpl-tab="<?php echo esc_attr( $index ); ?>"
					><?php echo esc_html( $this->resolve_tab_label( $tab ) ); ?></button>
				<?php endforeach; ?>
			</div>

			<div class="webmz-tpl__panels">
				<?php foreach ( $tabs as $index => $tab ) : ?>
					<div
						class="webmz-tpl__panel<?php echo 0 === $index ? ' is-active' : ''; ?>"
						data-webmz-tpl-panel="<?php echo esc_attr( $index ); ?>"
						role="tabpanel"
						<?php echo 0 !== $index ? ' hidden' : ''; ?>
					>
						<div class="webmz-tpl__panel-inner">
							<?php
							if ( 0 === $index ) {
								echo webmz_tabbed_product_loop_render_panel_html( $config, $index ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div>
						<div class="webmz-tpl__loading" hidden aria-hidden="true">
							<span class="webmz-tpl__loading-spinner"></span>
							<span class="webmz-tpl__loading-text"><?php esc_html_e( 'در حال بارگذاری...', 'tadris' ); ?></span>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}
