<?php
/**
 * File loop widget — compact product slider/grid with AJAX hover popup.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce product loop with small thumbnails and hover detail popup.
 */
class File_Loop_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-file-loop';
	}

	public function get_title() {
		return esc_html__( 'لوپ فایل‌ها', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-posts-carousel';
	}

	public function get_categories() {
		return array( 'webmz-shop-widgets' );
	}

	public function get_keywords() {
		return array( 'file', 'loop', 'product', 'swiper', 'فایل', 'لوپ', 'محصول' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-file-loop' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-file-loop' );
	}

	/**
	 * @return array<string,string>
	 */
	private function category_options() {
		$options = array(
			'' => esc_html__( 'همه دسته‌ها', 'tadris' ),
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
	 * @return array<string,string>
	 */
	private function tag_options() {
		$options = array(
			'' => esc_html__( 'همه برچسب‌ها', 'tadris' ),
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
		$this->register_query_controls();
		$this->register_header_controls();
		$this->register_display_controls();
		$this->register_zhaket_content_controls();
		$this->register_style_controls();
		$this->register_zhaket_style_controls();
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
				'condition' => array(
					'filter_by' => 'category',
				),
			)
		);

		$this->add_control(
			'tag',
			array(
				'label'     => esc_html__( 'برچسب محصول', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->tag_options(),
				'default'   => '',
				'condition' => array(
					'filter_by' => 'tag',
				),
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
				'default'     => esc_html__( 'به‌روز ترین محصولات ایرانی', 'tadris' ),
				'label_block' => true,
				'condition'   => array(
					'show_title' => 'yes',
				),
			)
		);

		$this->webmz_register_title_tag_control(
			'title_tag',
			esc_html__( 'تگ عنوان', 'tadris' ),
			array(
				'show_title' => 'yes',
			)
		);

		$this->add_control(
			'show_title_lines',
			array(
				'label'        => esc_html__( 'نمایش خطوط تزئینی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array(
					'show_title' => 'yes',
				),
			)
		);

		$this->add_control(
			'default_badge_text',
			array(
				'label'       => esc_html__( 'متن متا پیش‌فرض (پاپ‌آپ)', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'محصول ویژه', 'tadris' ),
				'label_block' => true,
				'description' => esc_html__( 'اگر در متاباکس محصول متن متا تعریف نشده باشد، این مقدار نمایش داده می‌شود.', 'tadris' ),
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

		$this->add_control(
			'jacket_style',
			array(
				'label'        => esc_html__( 'استایل ژاکتی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'description'  => esc_html__( 'کارت سفید با سایه، تصویر و متن وسط‌چین و برچسب تخفیف در گوشه.', 'tadris' ),
				'separator'    => 'before',
			)
		);

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => esc_html__( 'فاصله بین آیتم‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-fl' => '--webmz-fl-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'grid_columns_heading',
			array(
				'label'     => esc_html__( 'تنظیمات گرید', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'display_type' => 'grid',
				),
			)
		);

		foreach (
			array(
				'desktop' => esc_html__( 'دسکتاپ', 'tadris' ),
				'tablet'  => esc_html__( 'تبلت', 'tadris' ),
				'mobile'  => esc_html__( 'موبایل', 'tadris' ),
			) as $device => $label
		) {
			$key = 'grid_columns_' . $device;
			$this->add_control(
				$key,
				array(
					'label'     => sprintf( esc_html__( 'تعداد ستون %s', 'tadris' ), $label ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'mobile' === $device ? '2' : ( 'tablet' === $device ? '4' : '6' ),
					'options'   => array(
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
					),
					'condition' => array(
						'display_type' => 'grid',
					),
				)
			);
		}

		$this->add_control(
			'slider_heading',
			array(
				'label'     => esc_html__( 'تنظیمات اسلایدر', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'display_type' => 'slider',
				),
			)
		);

		foreach (
			array(
				'desktop' => esc_html__( 'دسکتاپ', 'tadris' ),
				'tablet'  => esc_html__( 'تبلت', 'tadris' ),
				'mobile'  => esc_html__( 'موبایل', 'tadris' ),
			) as $device => $label
		) {
			$this->add_control(
				'slider_columns_' . $device,
				array(
					'label'     => sprintf( esc_html__( 'تعداد ستون %s (پر کردن عرض)', 'tadris' ), $label ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 'mobile' === $device ? 2 : ( 'tablet' === $device ? 4 : 6 ),
					'min'       => 1,
					'max'       => 10,
					'condition' => array(
						'display_type' => 'slider',
					),
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
				'description'  => esc_html__( 'در حالت free/auto معمولاً غیرفعال می‌ماند.', 'tadris' ),
				'condition'    => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'slider_autoplay',
			array(
				'label'        => esc_html__( 'پخش خودکار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'display_type' => 'slider',
				),
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
			'show_pagination',
			array(
				'label'        => esc_html__( 'نقطه‌های ناوبری', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_zhaket_content_controls() {
		$this->start_controls_section(
			'section_zhaket_content',
			array(
				'label'     => esc_html__( 'استایل ژاکتی', 'tadris' ),
				'condition' => array(
					'jacket_style' => 'yes',
				),
			)
		);

		$this->add_control(
			'zhaket_show_subtitle',
			array(
				'label'        => esc_html__( 'نمایش زیرعنوان', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'zhaket_subtitle_source',
			array(
				'label'     => esc_html__( 'منبع زیرعنوان', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'meta',
				'options'   => array(
					'meta'     => esc_html__( 'متن متا (متاباکس محصول)', 'tadris' ),
					'category' => esc_html__( 'دسته محصول', 'tadris' ),
				),
				'condition' => array(
					'zhaket_show_subtitle' => 'yes',
				),
			)
		);

		$this->add_control(
			'zhaket_show_discount_badge',
			array(
				'label'        => esc_html__( 'نمایش برچسب تخفیف', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

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

	/**
	 * Theme dark text color from options.
	 *
	 * @return string
	 */
	private function theme_text_dark_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_text_dark' ) : '#111827';
	}

	/**
	 * Theme gray text color from options.
	 *
	 * @return string
	 */
	private function theme_text_gray_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_text_gray' ) : '#64748b';
	}

	/**
	 * Theme primary light color from options.
	 *
	 * @return string
	 */
	private function theme_primary_light_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_primary_light' ) : '#ffffff';
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'style_section_header',
			array(
				'label' => esc_html__( 'عنوان بخش', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'header_line_color',
			array(
				'label'     => esc_html__( 'رنگ خطوط عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#c8c4e8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-fl' => '--webmz-fl-header-line: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2d2e5f',
				'selectors' => array(
					'{{WRAPPER}} .webmz-fl' => '--webmz-fl-header-title-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'header_title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-fl__header-title',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'استایل', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'accent_color',
			array(
				'label'     => esc_html__( 'رنگ اصلی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-fl' => '--webmz-fl-accent: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_active_color',
			array(
				'label'     => esc_html__( 'رنگ نقطه فعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-fl__pagination .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => esc_html__( 'رنگ نقطه غیرفعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cbd5e1',
				'selectors' => array(
					'{{WRAPPER}} .webmz-fl__pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_heading',
			array(
				'label'     => esc_html__( 'کارت محصول', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'jacket_style' => '',
				),
			)
		);

		$this->add_control(
			'card_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه کارت', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-fl:not(.webmz-fl--zhaket) .webmz-fl__card' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'jacket_style' => '',
				),
			)
		);

		$this->add_control(
			'card_border_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه کارت', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 14,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-fl:not(.webmz-fl--zhaket) .webmz-fl__card' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-fl:not(.webmz-fl--zhaket) .webmz-fl__card-thumb' => 'border-radius: calc({{SIZE}}{{UNIT}} - 4px);',
				),
				'condition' => array(
					'jacket_style' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'card_title_typography',
				'label'     => esc_html__( 'تایپوگرافی عنوان کارت', 'tadris' ),
				'selector'  => '{{WRAPPER}} .webmz-fl:not(.webmz-fl--zhaket) .webmz-fl__card-title',
				'condition' => array(
					'jacket_style' => '',
				),
			)
		);

		$this->add_control(
			'card_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان کارت', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#334155',
				'selectors' => array(
					'{{WRAPPER}} .webmz-fl:not(.webmz-fl--zhaket) .webmz-fl__card-title' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'jacket_style' => '',
				),
			)
		);

		$this->add_control(
			'popup_heading',
			array(
				'label'     => esc_html__( 'پاپ‌آپ هاور', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'popup_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه پاپ‌آپ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-fl__popup' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_zhaket_style_controls() {
		$this->start_controls_section(
			'section_zhaket_style',
			array(
				'label'     => esc_html__( 'استایل ژاکتی', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'jacket_style' => 'yes',
				),
			)
		);

		$this->add_control(
			'zhaket_card_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه کارت', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-fl--zhaket .webmz-fl__card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'zhaket_card_border_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه کارت', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 14,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-fl--zhaket .webmz-fl__card' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'zhaket_card_border',
				'label'    => esc_html__( 'حاشیه کارت', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-fl--zhaket .webmz-fl__card',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'    => '1',
							'right'  => '1',
							'bottom' => '1',
							'left'   => '1',
							'unit'   => 'px',
						),
					),
					'color'  => array(
						'default' => 'rgba(15, 23, 42, 0.08)',
					),
				),
			)
		);

		$this->add_responsive_control(
			'zhaket_card_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی کارت', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array(
					'top'    => '20',
					'right'  => '16',
					'bottom' => '18',
					'left'   => '16',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-fl--zhaket .webmz-fl__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->add_responsive_control(
			'zhaket_thumb_size',
			array(
				'label'      => esc_html__( 'اندازه تصویر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 48,
						'max' => 120,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 72,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-fl--zhaket' => '--webmz-fl-zhaket-thumb: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'zhaket_thumb_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه تصویر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-fl--zhaket .webmz-fl__card-thumb' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'zhaket_title_heading',
			array(
				'label'     => esc_html__( 'عنوان', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'zhaket_title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-fl--zhaket .webmz-fl__card-title',
			)
		);

		$this->add_control(
			'zhaket_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_text_dark_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-fl--zhaket .webmz-fl__card-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'zhaket_subtitle_heading',
			array(
				'label'     => esc_html__( 'زیرعنوان', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'zhaket_subtitle_typography',
				'label'    => esc_html__( 'تایپوگرافی زیرعنوان', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-fl--zhaket .webmz-fl__card-subtitle',
			)
		);

		$this->add_control(
			'zhaket_subtitle_color',
			array(
				'label'     => esc_html__( 'رنگ زیرعنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_text_gray_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-fl--zhaket .webmz-fl__card-subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'zhaket_discount_heading',
			array(
				'label'     => esc_html__( 'برچسب تخفیف', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'zhaket_discount_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه برچسب', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-fl--zhaket' => '--webmz-fl-zhaket-discount-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'zhaket_discount_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن برچسب', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_light_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-fl--zhaket' => '--webmz-fl-zhaket-discount-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'zhaket_discount_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه برچسب', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-fl--zhaket .webmz-fl__zhaket-discount' => 'border-radius: 0 0 {{SIZE}}{{UNIT}} 0;',
				),
			)
		);

		$this->add_control(
			'zhaket_hover_heading',
			array(
				'label'     => esc_html__( 'هاور', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'zhaket_hover_border_color',
			array(
				'label'     => esc_html__( 'رنگ حاشیه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-fl--zhaket' => '--webmz-fl-zhaket-hover-border: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build WP_Query args.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<string,mixed>
	 */
	private function query_args( $settings ) {
		$args = array(
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'posts_per_page'         => min( 24, max( 1, absint( $settings['count'] ) ) ),
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => true,
		);

		$tax_query = array();

		if ( function_exists( 'wc_get_product_visibility_term_ids' ) ) {
			$term_ids = wc_get_product_visibility_term_ids();

			if ( isset( $term_ids['exclude-from-catalog'] ) ) {
				$tax_query[] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => array( $term_ids['exclude-from-catalog'] ),
					'operator' => 'NOT IN',
				);
			}
		}

		$filter_by = isset( $settings['filter_by'] ) ? $settings['filter_by'] : 'category';

		if ( 'tag' === $filter_by && ! empty( $settings['tag'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'product_tag',
				'field'    => 'slug',
				'terms'    => sanitize_title( $settings['tag'] ),
			);
		} elseif ( ! empty( $settings['category'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => sanitize_title( $settings['category'] ),
			);
		}

		if ( ! empty( $tax_query ) ) {
			if ( count( $tax_query ) > 1 ) {
				$tax_query['relation'] = 'AND';
			}
			$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}

		switch ( $settings['order_by'] ?? 'date' ) {
			case 'popularity':
				$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;

			case 'rating':
				$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;

			case 'rand':
				$args['orderby'] = 'rand';
				break;

			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
		}

		return $args;
	}

	/**
	 * Format discount percent for badge display.
	 *
	 * @param int $percent Discount percent.
	 * @return string
	 */
	private function format_discount_badge_text( $percent ) {
		$badge = absint( $percent ) . '%';

		if ( function_exists( 'webmz_to_persian_digits' ) ) {
			$badge = webmz_to_persian_digits( $badge );
		}

		return str_replace( '%', '٪', $badge );
	}

	/**
	 * Get jacket-style card subtitle for a product.
	 *
	 * @param \WC_Product          $product Product.
	 * @param array<string,mixed> $s       Settings.
	 * @return string
	 */
	private function get_zhaket_card_subtitle( $product, $s ) {
		if ( 'yes' !== ( $s['zhaket_show_subtitle'] ?? 'yes' ) ) {
			return '';
		}

		$source = $s['zhaket_subtitle_source'] ?? 'meta';

		if ( 'category' === $source && function_exists( 'webmz_get_deepest_product_category' ) ) {
			$term = webmz_get_deepest_product_category( $product->get_id() );

			return ( $term instanceof \WP_Term ) ? $term->name : '';
		}

		$fallback = isset( $s['default_badge_text'] ) ? (string) $s['default_badge_text'] : '';

		return function_exists( 'webmz_pll_get_product_badge_text' )
			? webmz_pll_get_product_badge_text( $product->get_id(), $fallback )
			: $fallback;
	}

	/**
	 * Render one product card.
	 *
	 * @param \WC_Product          $product Product.
	 * @param array<string,mixed> $s       Settings.
	 * @return void
	 */
	private function render_product_card( $product, $s ) {
		if ( 'yes' === ( $s['jacket_style'] ?? '' ) ) {
			$this->render_zhaket_product_card( $product, $s );
			return;
		}

		$product_id = $product->get_id();
		$thumb_url  = function_exists( 'webmz_pll_get_product_thumb_url' )
			? webmz_pll_get_product_thumb_url( $product_id, 'thumbnail' )
			: get_the_post_thumbnail_url( $product_id, webmz_get_loop_image_size() );
		$title      = $product->get_name();
		?>
		<div class="webmz-fl__card-wrap">
			<a
				class="webmz-fl__card"
				href="<?php echo esc_url( $product->get_permalink() ); ?>"
				data-product-id="<?php echo esc_attr( $product_id ); ?>"
				aria-label="<?php echo esc_attr( $title ); ?>"
			>
				<div class="webmz-fl__card-thumb">
					<?php if ( $thumb_url ) : ?>
						<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" decoding="async">
					<?php else : ?>
						<span class="webmz-fl__card-thumb-placeholder" aria-hidden="true"></span>
					<?php endif; ?>
				</div>
				<div class="webmz-fl__card-title"><?php echo esc_html( $title ); ?></div>
			</a>
		</div>
		<?php
	}

	/**
	 * Render one jacket-style product card.
	 *
	 * @param \WC_Product          $product Product.
	 * @param array<string,mixed> $s       Settings.
	 * @return void
	 */
	private function render_zhaket_product_card( $product, $s ) {
		$product_id   = $product->get_id();
		$thumb_url    = function_exists( 'webmz_pll_get_product_thumb_url' )
			? webmz_pll_get_product_thumb_url( $product_id, 'thumbnail' )
			: get_the_post_thumbnail_url( $product_id, webmz_get_loop_image_size() );
		$title        = $product->get_name();
		$subtitle     = $this->get_zhaket_card_subtitle( $product, $s );
		$discount_pct = 0;

		if ( 'yes' === ( $s['zhaket_show_discount_badge'] ?? 'yes' ) && function_exists( 'webmz_get_product_loop_discount_percent' ) ) {
			$discount_pct = webmz_get_product_loop_discount_percent( $product );
		}
		?>
		<div class="webmz-fl__card-wrap">
			<a
				class="webmz-fl__card webmz-fl__card--zhaket"
				href="<?php echo esc_url( $product->get_permalink() ); ?>"
				data-product-id="<?php echo esc_attr( $product_id ); ?>"
				aria-label="<?php echo esc_attr( $title ); ?>"
			>
				<?php if ( $discount_pct > 0 ) : ?>
					<span class="webmz-fl__zhaket-discount" aria-hidden="true">
						<?php echo esc_html( $this->format_discount_badge_text( $discount_pct ) ); ?>
					</span>
				<?php endif; ?>
				<div class="webmz-fl__card-thumb">
					<?php if ( $thumb_url ) : ?>
						<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" decoding="async">
					<?php else : ?>
						<span class="webmz-fl__card-thumb-placeholder" aria-hidden="true"></span>
					<?php endif; ?>
				</div>
				<div class="webmz-fl__card-body">
					<div class="webmz-fl__card-title"><?php echo esc_html( $title ); ?></div>
					<?php if ( '' !== $subtitle ) : ?>
						<div class="webmz-fl__card-subtitle"><?php echo esc_html( $subtitle ); ?></div>
					<?php endif; ?>
				</div>
			</a>
		</div>
		<?php
	}

	protected function render() {
		if ( ! function_exists( 'wc_get_product' ) ) {
			return;
		}

		$s              = $this->get_settings_for_display();
		$query          = new WP_Query( $this->query_args( $s ) );
		$is_slider      = 'slider' === ( $s['display_type'] ?? 'slider' );
		$show_pagination = $is_slider && ( ! isset( $s['show_pagination'] ) || 'yes' === $s['show_pagination'] );
		$show_title     = 'yes' === ( $s['show_title'] ?? 'yes' );
		$gap            = isset( $s['item_gap']['size'] ) ? absint( $s['item_gap']['size'] ) : 16;
		$badge_fb       = isset( $s['default_badge_text'] ) ? $s['default_badge_text'] : '';
		$is_zhaket      = 'yes' === ( $s['jacket_style'] ?? '' );

		if ( ! $query->have_posts() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'محصولی برای نمایش یافت نشد.', 'tadris' ) . '</div>';
			return;
		}

		$cols_mobile  = ! empty( $s['slider_columns_mobile'] ) ? absint( $s['slider_columns_mobile'] ) : 2;
		$cols_tablet  = ! empty( $s['slider_columns_tablet'] ) ? absint( $s['slider_columns_tablet'] ) : 4;
		$cols_desktop = ! empty( $s['slider_columns_desktop'] ) ? absint( $s['slider_columns_desktop'] ) : 6;

		$slider_conf = array(
			'spaceBetween'  => $gap,
			'freeMode'      => true,
			'autoplay'      => 'yes' === ( $s['slider_autoplay'] ?? '' ),
			'autoplayDelay' => ! empty( $s['slider_autoplay_delay'] ) ? absint( $s['slider_autoplay_delay'] ) : 4000,
			'pagination'    => $show_pagination,
		);
		?>
		<div
			class="webmz-fl<?php echo $is_slider ? ' webmz-fl--slider' : ' webmz-fl--grid'; ?><?php echo $is_zhaket ? ' webmz-fl--zhaket' : ''; ?>"
			dir="rtl"
			data-webmz-fl-badge-fallback="<?php echo esc_attr( $badge_fb ); ?>"
			style="
				--webmz-fl-gap: <?php echo esc_attr( $gap ); ?>px;
				--webmz-fl-cols-mobile: <?php echo esc_attr( $cols_mobile ); ?>;
				--webmz-fl-cols-tablet: <?php echo esc_attr( $cols_tablet ); ?>;
				--webmz-fl-cols-desktop: <?php echo esc_attr( $cols_desktop ); ?>;
			"
		>
			<?php if ( $show_title && ! empty( $s['section_title'] ) ) : ?>
				<?php $show_header_lines = 'yes' === ( $s['show_title_lines'] ?? 'yes' ); ?>
				<header class="webmz-fl__header">
					<?php if ( $show_header_lines ) : ?>
						<span class="webmz-fl__header-line" aria-hidden="true"></span>
					<?php endif; ?>
					<?php
					$title_tag = $this->webmz_get_title_tag( $s, 'title_tag' );
					printf(
						'<%1$s class="webmz-fl__header-title">%2$s</%1$s>',
						esc_html( $title_tag ),
						esc_html( $s['section_title'] )
					);
					?>
					<?php if ( $show_header_lines ) : ?>
						<span class="webmz-fl__header-line" aria-hidden="true"></span>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<div class="webmz-fl__body">
				<div class="webmz-fl__track-wrap">
					<?php if ( $is_slider ) : ?>
						<div class="webmz-fl__slider-viewport">
							<div
								class="webmz-fl__slider swiper"
								data-webmz-fl-slider="<?php echo esc_attr( wp_json_encode( $slider_conf ) ); ?>"
							>
								<div class="swiper-wrapper">
									<?php
									while ( $query->have_posts() ) :
										$query->the_post();
										$product = wc_get_product( get_the_ID() );
										if ( ! $product ) {
											continue;
										}
										?>
										<div class="swiper-slide">
											<?php $this->render_product_card( $product, $s ); ?>
										</div>
									<?php endwhile; ?>
								</div>
							</div>
						</div>
						<?php if ( $show_pagination ) : ?>
							<div class="webmz-fl__pagination swiper-pagination"></div>
						<?php endif; ?>
					<?php else : ?>
						<div
							class="webmz-fl__grid webmz-loop-grid"
							style="
								--webmz-grid-columns: <?php echo esc_attr( $s['grid_columns_desktop'] ?? 6 ); ?>;
								--webmz-grid-tablet-columns: <?php echo esc_attr( $s['grid_columns_tablet'] ?? 4 ); ?>;
								--webmz-grid-mobile-columns: <?php echo esc_attr( $s['grid_columns_mobile'] ?? 2 ); ?>;
							"
						>
							<?php
							while ( $query->have_posts() ) :
								$query->the_post();
								$product = wc_get_product( get_the_ID() );
								if ( ! $product ) {
									continue;
								}
								$this->render_product_card( $product, $s );
							endwhile;
							?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="webmz-fl__popup" hidden aria-hidden="true"></div>
		</div>
		<?php
		wp_reset_postdata();
	}
}
