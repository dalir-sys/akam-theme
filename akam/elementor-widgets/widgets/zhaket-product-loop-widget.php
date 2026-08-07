<?php
/**
 * Zhaket product loop widget — marketplace cards with hover actions.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce product loop styled like Zhaket marketplace.
 */
class Zhaket_Product_Loop_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-zhaket-product-loop';
	}

	public function get_title() {
		return esc_html__( 'لوپ محصولات ژاکت', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_categories() {
		return array( 'webmz-shop-widgets' );
	}

	public function get_keywords() {
		return array( 'zhaket', 'product', 'loop', 'swiper', 'grid', 'ژاکت', 'محصول', 'لوپ', 'اسلایدر' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-zhaket-product-loop', 'webmz-zhaket-heading' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-zhaket-product-loop', 'webmz-tadris-widgets', 'webmz-header-commerce' );
	}

	/**
	 * @return string
	 */
	private function theme_primary_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_primary' ) : '#0878f9';
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
	private function theme_primary_light_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_primary_light' ) : '#ffffff';
	}

	/**
	 * @return array<string,string>
	 */
	private function category_options() {
		$options = array( '' => esc_html__( 'همه دسته‌ها', 'tadris' ) );
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
		$options = array( '' => esc_html__( 'همه برچسب‌ها', 'tadris' ) );
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

	protected function register_controls() {
		$this->register_query_controls();
		$this->register_header_controls();
		$this->register_card_controls();
		$this->register_display_controls();
		$this->register_style_controls();
	}

	protected function register_query_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'کوئری محصولات', 'tadris' ),
			)
		);

		$this->add_control(
			'filter_by',
			array(
				'label'   => esc_html__( 'منبع محصولات', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => array(
					'category' => esc_html__( 'دسته‌بندی', 'tadris' ),
					'tag'      => esc_html__( 'برچسب', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'category',
			array(
				'label'     => esc_html__( 'دسته محصول', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->category_options(),
				'default'   => '',
				'condition' => array( 'filter_by' => 'category' ),
			)
		);

		$this->add_control(
			'tag',
			array(
				'label'     => esc_html__( 'برچسب محصول', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->tag_options(),
				'default'   => '',
				'condition' => array( 'filter_by' => 'tag' ),
			)
		);

		$this->add_control(
			'count',
			array(
				'label'   => esc_html__( 'تعداد محصولات', 'tadris' ),
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

		$this->end_controls_section();
	}

	protected function register_header_controls() {
		$this->start_controls_section(
			'section_header',
			array(
				'label' => esc_html__( 'عنوان بخش', 'tadris' ),
			)
		);

		$this->add_control(
			'show_title',
			array(
				'label'        => esc_html__( 'نمایش عنوان', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'section_title',
			array(
				'label'       => esc_html__( 'متن عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'جدیدترین محصولات', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_title' => 'yes' ),
			)
		);

		$this->webmz_register_title_tag_control(
			'title_tag',
			esc_html__( 'تگ عنوان', 'tadris' ),
			array( 'show_title' => 'yes' )
		);

		$this->add_control(
			'show_badge',
			array(
				'label'        => esc_html__( 'نمایش نشان آیکون', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'badge_icon',
			array(
				'label'     => esc_html__( 'آیکون نشان', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'far fa-calendar-alt',
					'library' => 'fa-regular',
				),
				'condition' => array(
					'show_title' => 'yes',
					'show_badge' => 'yes',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'badge_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون نشان', 'tadris' ),
			array(
				'show_title' => 'yes',
				'show_badge' => 'yes',
			)
		);

		$this->add_control(
			'show_button',
			array(
				'label'        => esc_html__( 'نمایش دکمه مشاهده همه', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
				'condition'    => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'متن دکمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'مشاهده همه', 'tadris' ),
				'label_block' => true,
				'condition'   => array(
					'show_title'  => 'yes',
					'show_button' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'       => esc_html__( 'لینک دکمه', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#' ),
				'label_block' => true,
				'condition'   => array(
					'show_title'  => 'yes',
					'show_button' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_icon',
			array(
				'label'     => esc_html__( 'آیکون دکمه', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_title'  => 'yes',
					'show_button' => 'yes',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'button_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون دکمه', 'tadris' ),
			array(
				'show_title'  => 'yes',
				'show_button' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function register_card_controls() {
		$this->start_controls_section(
			'section_card',
			array(
				'label' => esc_html__( 'محتوای کارت', 'tadris' ),
			)
		);

		$this->add_control(
			'card_title_tag',
			array(
				'label'   => esc_html__( 'تگ عنوان محصول', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => array(
					'h3' => 'H3',
					'h4' => 'H4',
					'div' => 'DIV',
				),
			)
		);

		$this->add_control(
			'show_sales',
			array(
				'label'        => esc_html__( 'نمایش تعداد فروش', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_rating',
			array(
				'label'        => esc_html__( 'نمایش امتیاز', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_discount_badge',
			array(
				'label'        => esc_html__( 'نمایش برچسب تخفیف', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_price',
			array(
				'label'        => esc_html__( 'نمایش قیمت', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_cart_button',
			array(
				'label'        => esc_html__( 'دکمه افزودن به سبد', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'add_to_cart_text',
			array(
				'label'     => esc_html__( 'متن افزودن به سبد', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'افزودن به سبد خرید', 'tadris' ),
				'condition' => array( 'show_cart_button' => 'yes' ),
			)
		);

		$this->add_control(
			'show_preview_button',
			array(
				'label'        => esc_html__( 'دکمه پیش‌نمایش', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'preview_text',
			array(
				'label'       => esc_html__( 'متن پیش‌نمایش', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'پیشنمایش', 'tadris' ),
				'condition'   => array( 'show_preview_button' => 'yes' ),
				'description' => esc_html__( 'لینک از متاباکس «پیش‌نمایش محصول» خوانده می‌شود.', 'tadris' ),
			)
		);

		$this->add_control(
			'hide_preview_no_url',
			array(
				'label'        => esc_html__( 'مخفی کردن پیش‌نمایش بدون لینک', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'condition'    => array( 'show_preview_button' => 'yes' ),
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
			'free_text',
			array(
				'label'   => esc_html__( 'متن رایگان', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'رایگان', 'tadris' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_display_controls() {
		$this->start_controls_section(
			'section_display',
			array(
				'label' => esc_html__( 'نمایش', 'tadris' ),
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

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => esc_html__( 'فاصله بین آیتم‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zpl' => '--webmz-zpl-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'grid_columns_heading',
			array(
				'label'     => esc_html__( 'تنظیمات گرید', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'display_type' => 'grid' ),
			)
		);

		foreach (
			array(
				'desktop' => esc_html__( 'دسکتاپ', 'tadris' ),
				'tablet'  => esc_html__( 'تبلت', 'tadris' ),
				'mobile'  => esc_html__( 'موبایل', 'tadris' ),
			) as $device => $label
		) {
			$default = 'mobile' === $device ? '1' : ( 'tablet' === $device ? '2' : '4' );
			$this->add_control(
				'grid_columns_' . $device,
				array(
					'label'     => sprintf( esc_html__( 'تعداد ستون %s', 'tadris' ), $label ),
					'type'      => Controls_Manager::SELECT,
					'default'   => $default,
					'options'   => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'condition' => array( 'display_type' => 'grid' ),
				)
			);
		}

		$this->add_control(
			'slider_heading',
			array(
				'label'     => esc_html__( 'تنظیمات اسلایدر', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'display_type' => 'slider' ),
			)
		);

		foreach (
			array(
				'desktop' => esc_html__( 'دسکتاپ', 'tadris' ),
				'tablet'  => esc_html__( 'تبلت', 'tadris' ),
				'mobile'  => esc_html__( 'موبایل', 'tadris' ),
			) as $device => $label
		) {
			$default = 'mobile' === $device ? 1 : ( 'tablet' === $device ? 2 : 4 );
			$this->add_control(
				'slider_columns_' . $device,
				array(
					'label'     => sprintf( esc_html__( 'تعداد ستون %s', 'tadris' ), $label ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => $default,
					'min'       => 1,
					'max'       => 8,
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
				'default'      => '',
				'condition'    => array( 'display_type' => 'slider' ),
			)
		);

		$this->add_control(
			'slider_autoplay',
			array(
				'label'        => esc_html__( 'پخش خودکار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'display_type' => 'slider' ),
			)
		);

		$this->add_control(
			'slider_autoplay_delay',
			array(
				'label'     => esc_html__( 'تأخیر پخش خودکار (ms)', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4000,
				'min'       => 1000,
				'max'       => 15000,
				'condition' => array(
					'display_type'    => 'slider',
					'slider_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_navigation',
			array(
				'label'        => esc_html__( 'فلش‌های ناوبری', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'display_type' => 'slider' ),
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'        => esc_html__( 'نقطه‌های ناوبری', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'condition'    => array( 'display_type' => 'slider' ),
			)
		);

		$this->add_control(
			'mobile_stack_header',
			array(
				'label'        => esc_html__( 'چیدمان عمودی هدر در موبایل', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->webmz_register_heading_icon_box_style_controls(
			'.webmz-zpl__header-badge',
			array(
				'condition'    => array(
					'show_title' => 'yes',
					'show_badge' => 'yes',
				),
				'bg_default'   => $this->theme_primary_color(),
				'icon_default' => $this->theme_primary_light_color(),
			)
		);

		$this->start_controls_section(
			'section_style_header',
			array(
				'label' => esc_html__( 'عنوان بخش', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'header_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_text_dark_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl__header-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'header_title_typography',
				'selector' => '{{WRAPPER}} .webmz-zpl__header-title',
			)
		);

		$this->add_control(
			'header_button_color',
			array(
				'label'     => esc_html__( 'رنگ دکمه مشاهده همه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl__header-button' => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_card',
			array(
				'label' => esc_html__( 'کارت محصول', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه کارت', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 30 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zpl__card' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-zpl__media' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'card_border',
				'selector'       => '{{WRAPPER}} .webmz-zpl__card',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'unit'     => 'px',
							'isLinked' => true,
						),
					),
					'color'  => array(
						'default' => 'rgba(15, 23, 42, 0.1)',
					),
				),
			)
		);

		$this->add_control(
			'card_body_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه باکس سفید', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl__body' => 'background-color: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'card_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان محصول', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_text_dark_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_title_typography',
				'selector' => '{{WRAPPER}} .webmz-zpl__title',
			)
		);

		$this->add_control(
			'card_meta_color',
			array(
				'label'     => esc_html__( 'رنگ متا (فروش/امتیاز)', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_text_gray_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl__meta' => 'color: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'card_price_color',
			array(
				'label'     => esc_html__( 'رنگ قیمت', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_text_dark_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl__price' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_price_regular_color',
			array(
				'label'     => esc_html__( 'رنگ قیمت خط‌خورده', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_text_gray_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl__price-regular' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_discount_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه برچسب تخفیف', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl' => '--webmz-zpl-discount-bg: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'card_discount_color',
			array(
				'label'     => esc_html__( 'رنگ متن تخفیف', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_light_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl' => '--webmz-zpl-discount-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_cart_btn_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه دکمه سبد', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl' => '--webmz-zpl-cart-bg: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'card_cart_btn_color',
			array(
				'label'     => esc_html__( 'رنگ متن دکمه سبد', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_light_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl' => '--webmz-zpl-cart-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_preview_btn_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه دکمه پیش‌نمایش', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f1f5f9',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl' => '--webmz-zpl-preview-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_preview_btn_color',
			array(
				'label'     => esc_html__( 'رنگ متن پیش‌نمایش', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_text_gray_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl' => '--webmz-zpl-preview-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_nav',
			array(
				'label'     => esc_html__( 'ناوبری اسلایدر', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'display_type' => 'slider' ),
			)
		);

		$this->add_control(
			'nav_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه فلش', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl__nav' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون فلش', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_text_gray_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zpl__nav' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<string,mixed>
	 */
	private function card_args( $settings ) {
		return array(
			'title_tag'           => $settings['card_title_tag'] ?? 'h3',
			'show_sales'          => 'yes' === ( $settings['show_sales'] ?? 'yes' ),
			'show_rating'         => 'yes' === ( $settings['show_rating'] ?? 'yes' ),
			'show_discount_badge' => 'yes' === ( $settings['show_discount_badge'] ?? 'yes' ),
			'show_price'          => 'yes' === ( $settings['show_price'] ?? 'yes' ),
			'show_cart_button'    => 'yes' === ( $settings['show_cart_button'] ?? 'yes' ),
			'show_preview_button' => 'yes' === ( $settings['show_preview_button'] ?? 'yes' ),
			'hide_preview_no_url' => 'yes' === ( $settings['hide_preview_no_url'] ?? '' ),
			'add_to_cart_text'    => $settings['add_to_cart_text'] ?? esc_html__( 'افزودن به سبد خرید', 'tadris' ),
			'preview_text'        => $settings['preview_text'] ?? esc_html__( 'پیشنمایش', 'tadris' ),
			'variable_text'       => $settings['variable_text'] ?? esc_html__( 'انتخاب گزینه‌ها', 'tadris' ),
			'unavailable_text'    => $settings['unavailable_text'] ?? esc_html__( 'ناموجود', 'tadris' ),
			'free_text'           => $settings['free_text'] ?? esc_html__( 'رایگان', 'tadris' ),
		);
	}

	/**
	 * @param array<string,mixed> $settings Widget settings.
	 */
	private function render_header( $settings ) {
		if ( 'yes' !== ( $settings['show_title'] ?? 'yes' ) ) {
			return;
		}

		$title       = isset( $settings['section_title'] ) ? trim( (string) $settings['section_title'] ) : '';
		$show_badge  = 'yes' === ( $settings['show_badge'] ?? 'yes' );
		$show_button = 'yes' === ( $settings['show_button'] ?? 'yes' );
		$title_tag   = $this->webmz_get_title_tag( $settings, 'title_tag' );
		$stack       = 'yes' === ( $settings['mobile_stack_header'] ?? 'yes' );

		if ( '' === $title && ! $show_badge && ! $show_button ) {
			return;
		}
		?>
		<header class="webmz-zpl__header webmz-zh-heading<?php echo $stack ? ' webmz-zh-heading--stack-mobile' : ''; ?>">
			<div class="webmz-zpl__header-lead webmz-zh-heading__lead">
				<?php if ( $show_badge && ! empty( $settings['badge_icon']['value'] ) ) : ?>
					<span class="webmz-zpl__header-badge webmz-zh-heading__badge <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'badge_icon_color_mode' ) ); ?>" aria-hidden="true">
						<?php Icons_Manager::render_icon( $settings['badge_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php endif; ?>

				<?php if ( '' !== $title ) : ?>
					<<?php echo esc_html( $title_tag ); ?> class="webmz-zpl__header-title webmz-zh-heading__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $title_tag ); ?>>
				<?php endif; ?>
			</div>

			<?php if ( $show_button && ! empty( $settings['button_text'] ) ) : ?>
				<?php
				$this->add_render_attribute( 'header_button', 'class', 'webmz-zpl__header-button webmz-zh-heading__button' );
				if ( ! empty( $settings['button_link']['url'] ) ) {
					$this->add_link_attributes( 'header_button', $settings['button_link'] );
				} else {
					$this->add_render_attribute( 'header_button', 'href', '#' );
				}
				?>
				<a <?php echo $this->get_render_attribute_string( 'header_button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php if ( ! empty( $settings['button_icon']['value'] ) ) : ?>
						<span class="webmz-zpl__header-button-icon webmz-zh-heading__button-icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'button_icon_color_mode' ) ); ?>" aria-hidden="true">
							<?php Icons_Manager::render_icon( $settings['button_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<span class="webmz-zh-heading__button-text"><?php echo esc_html( $settings['button_text'] ); ?></span>
				</a>
			<?php endif; ?>
		</header>
		<?php
	}

	protected function render() {
		if ( ! function_exists( 'wc_get_product' ) || ! function_exists( 'webmz_zhaket_product_loop_query_args' ) ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'ووکامرس فعال نیست.', 'tadris' ) . '</div>';
			return;
		}

		$settings       = $this->get_settings_for_display();
		$query          = new WP_Query( webmz_zhaket_product_loop_query_args( $settings ) );
		$is_slider      = 'slider' === ( $settings['display_type'] ?? 'slider' );
		$show_nav       = $is_slider && 'yes' === ( $settings['show_navigation'] ?? 'yes' );
		$show_pagination = $is_slider && 'yes' === ( $settings['show_pagination'] ?? '' );
		$gap            = isset( $settings['item_gap']['size'] ) ? absint( $settings['item_gap']['size'] ) : 16;
		$card_args      = $this->card_args( $settings );

		if ( ! $query->have_posts() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'محصولی برای نمایش یافت نشد.', 'tadris' ) . '</div>';
			return;
		}

		$cols_mobile  = ! empty( $settings['slider_columns_mobile'] ) ? absint( $settings['slider_columns_mobile'] ) : 1;
		$cols_tablet  = ! empty( $settings['slider_columns_tablet'] ) ? absint( $settings['slider_columns_tablet'] ) : 2;
		$cols_desktop = ! empty( $settings['slider_columns_desktop'] ) ? absint( $settings['slider_columns_desktop'] ) : 4;

		$grid_mobile  = ! empty( $settings['grid_columns_mobile'] ) ? absint( $settings['grid_columns_mobile'] ) : 1;
		$grid_tablet  = ! empty( $settings['grid_columns_tablet'] ) ? absint( $settings['grid_columns_tablet'] ) : 2;
		$grid_desktop = ! empty( $settings['grid_columns_desktop'] ) ? absint( $settings['grid_columns_desktop'] ) : 4;

		$slider_conf = array(
			'spaceBetween'  => $gap,
			'slidesDesktop' => $cols_desktop,
			'slidesTablet'  => $cols_tablet,
			'slidesMobile'  => $cols_mobile,
			'loop'          => 'yes' === ( $settings['slider_loop'] ?? '' ),
			'autoplay'      => 'yes' === ( $settings['slider_autoplay'] ?? '' ),
			'autoplayDelay' => ! empty( $settings['slider_autoplay_delay'] ) ? absint( $settings['slider_autoplay_delay'] ) : 4000,
			'navigation'    => $show_nav,
			'pagination'    => $show_pagination,
		);
		?>
		<div
			class="webmz-zpl<?php echo $is_slider ? ' webmz-zpl--slider' : ' webmz-zpl--grid'; ?>"
			dir="rtl"
			style="
				--webmz-zpl-gap: <?php echo esc_attr( $gap ); ?>px;
				--webmz-zpl-cols-mobile: <?php echo esc_attr( $is_slider ? $cols_mobile : $grid_mobile ); ?>;
				--webmz-zpl-cols-tablet: <?php echo esc_attr( $is_slider ? $cols_tablet : $grid_tablet ); ?>;
				--webmz-zpl-cols-desktop: <?php echo esc_attr( $is_slider ? $cols_desktop : $grid_desktop ); ?>;
			"
		>
			<?php $this->render_header( $settings ); ?>

			<div class="webmz-zpl__body-wrap">
				<?php if ( $is_slider ) : ?>
					<div class="webmz-zpl__track-wrap">
						<?php if ( $show_nav ) : ?>
							<button type="button" class="webmz-zpl__nav webmz-zpl__nav--prev" aria-label="<?php esc_attr_e( 'اسلاید قبلی', 'tadris' ); ?>">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</button>
							<button type="button" class="webmz-zpl__nav webmz-zpl__nav--next" aria-label="<?php esc_attr_e( 'اسلاید بعدی', 'tadris' ); ?>">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</button>
						<?php endif; ?>

						<div class="webmz-zpl__slider-viewport">
							<div class="webmz-zpl__slider swiper" data-webmz-zpl-slider="<?php echo esc_attr( wp_json_encode( $slider_conf ) ); ?>">
								<div class="swiper-wrapper">
									<?php
									while ( $query->have_posts() ) :
										$query->the_post();
										$product = wc_get_product( get_the_ID() );
										if ( ! $product ) {
											continue;
										}
										webmz_zhaket_product_loop_render_card( $product, $card_args, true );
									endwhile;
									?>
								</div>
							</div>
						</div>

						<?php if ( $show_pagination ) : ?>
							<div class="webmz-zpl__pagination swiper-pagination"></div>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<div class="webmz-zpl__grid">
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							$product = wc_get_product( get_the_ID() );
							if ( ! $product ) {
								continue;
							}
							webmz_zhaket_product_loop_render_card( $product, $card_args, false );
						endwhile;
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
		wp_reset_postdata();
	}
}
