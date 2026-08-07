<?php
/**
 * Zhaket vertical product slider — card-stack Swiper inside a styled box.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * Vertical product card slider (Zhaket style).
 */
class Zhaket_Vertical_Slider_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-zhaket-vertical-slider';
	}

	public function get_title() {
		return esc_html__( 'اسلایدر عمودی ژاکت', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-slider-vertical';
	}

	public function get_categories() {
		return array( 'webmz-shop-widgets' );
	}

	public function get_keywords() {
		return array( 'zhaket', 'vertical', 'slider', 'swiper', 'product', 'ژاکت', 'اسلایدر', 'عمودی', 'محصول' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-zhaket-vertical-slider' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-zhaket-vertical-slider' );
	}

	/**
	 * @return array<string,string>
	 */
	private function category_options() {
		$options = array(
			'' => esc_html__( 'انتخاب دسته', 'tadris' ),
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
			'' => esc_html__( 'انتخاب برچسب', 'tadris' ),
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
		$this->register_content_controls();
		$this->register_slider_controls();
		$this->register_style_controls();
	}

	protected function register_query_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'منبع محصولات', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'نوع منبع', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => array(
					'category' => esc_html__( 'دسته‌بندی', 'tadris' ),
					'tag'      => esc_html__( 'برچسب', 'tadris' ),
					'manual'   => esc_html__( 'انتخاب دستی (۵ محصول)', 'tadris' ),
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
					'source' => 'category',
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
					'source' => 'tag',
				),
			)
		);

		$this->add_control(
			'product_ids',
			array(
				'label'       => esc_html__( 'انتخاب ۵ محصول', 'tadris' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => \webmz_tadris_get_product_options(),
				'multiple'    => true,
				'label_block' => true,
				'description' => esc_html__( 'حداکثر ۵ محصول انتخاب کنید.', 'tadris' ),
				'condition'   => array(
					'source' => 'manual',
				),
			)
		);

		$this->add_control(
			'count',
			array(
				'label'     => esc_html__( 'تعداد محصولات', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5,
				'min'       => 1,
				'max'       => 5,
				'condition' => array(
					'source!' => 'manual',
				),
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'     => esc_html__( 'مرتب‌سازی', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => array(
					'date'       => esc_html__( 'جدیدترین', 'tadris' ),
					'popularity' => esc_html__( 'پرفروش‌ترین', 'tadris' ),
					'rating'     => esc_html__( 'بالاترین امتیاز', 'tadris' ),
					'rand'       => esc_html__( 'تصادفی', 'tadris' ),
				),
				'condition' => array(
					'source!' => 'manual',
				),
			)
		);

		$this->add_control(
			'product_link',
			array(
				'label'        => esc_html__( 'لینک کارت‌ها به صفحه محصول', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'       => esc_html__( 'اندازه تصویر شاخص', 'tadris' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'large',
				'options'     => array(
					'medium' => esc_html__( 'متوسط', 'tadris' ),
					'large'  => esc_html__( 'بزرگ', 'tadris' ),
					'full'   => esc_html__( 'کامل', 'tadris' ),
				),
				'description' => esc_html__( 'از تصویر شاخص اصلی محصول (Featured Image) استفاده می‌شود.', 'tadris' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_text',
			array(
				'label' => esc_html__( 'متن‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'قالب‌های برتر فروشگاهی', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control();

		$this->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'زیرعنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'راه‌اندازی یک فروشگاه مدرن و خاص', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_badge_icon',
			array(
				'label'        => esc_html__( 'نمایش آیکون روی اسلایدر', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'badge_icon',
			array(
				'label'     => esc_html__( 'آیکون', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-smile',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_badge_icon' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button',
			array(
				'label' => esc_html__( 'دکمه', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'متن دکمه', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مشاهده همه', 'tadris' ),
			)
		);

		$this->add_control(
			'button_url',
			array(
				'label'   => esc_html__( 'لینک دکمه', 'tadris' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_slider_controls() {
		$this->start_controls_section(
			'section_slider',
			array(
				'label' => esc_html__( 'اسلایدر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'پخش خودکار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => esc_html__( 'تأخیر پخش خودکار (ms)', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3500,
				'min'       => 1000,
				'max'       => 15000,
				'condition' => array(
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => esc_html__( 'لوپ', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'slide_speed',
			array(
				'label'   => esc_html__( 'سرعت انیمیشن (ms)', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 550,
				'min'     => 200,
				'max'     => 2000,
			)
		);

		$this->add_control(
			'slides_per_view',
			array(
				'label'       => esc_html__( 'نمایش اسلایدهای کناری', 'tadris' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1.38,
				'min'         => 1.15,
				'max'         => 1.65,
				'step'        => 0.01,
				'description' => esc_html__( 'مقدار بیشتر = نمایش بیشتر اسلاید قبلی و بعدی در کنار اسلاید وسط.', 'tadris' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'section_box_style',
			array(
				'label' => esc_html__( 'باکس', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'box_background',
				'selector' => '{{WRAPPER}} .webmz-zvs',
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
					'color'      => array(
						'default' => 'var(--webmz-color-secondary)',
					),
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'      => '28',
					'right'    => '20',
					'bottom'   => '28',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zvs' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zvs' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .webmz-zvs',
			)
		);

		$this->add_control(
			'show_decor',
			array(
				'label'        => esc_html__( 'نمایش تزئینات پس‌زمینه', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'decor_line_color',
			array(
				'label'     => esc_html__( 'رنگ خطوط پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.08)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zvs' => '--webmz-zvs-decor-line: {{VALUE}};',
				),
				'condition' => array(
					'show_decor' => 'yes',
				),
			)
		);

		$this->add_control(
			'accent_color',
			array(
				'label'     => esc_html__( 'رنگ نوار گوشه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zvs' => '--webmz-zvs-accent: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_style',
			array(
				'label' => esc_html__( 'اسلایدر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'slider_height',
			array(
				'label'       => esc_html__( 'حداکثر ارتفاع تصویر', 'tadris' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array( 'min' => 100, 'max' => 400 ),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 168,
				),
				'selectors'   => array(
					'{{WRAPPER}} .webmz-zvs' => '--webmz-zvs-slider-height: {{SIZE}}{{UNIT}};',
				),
				'description' => esc_html__( 'ارتفاع هر اسلاید بر اساس نسبت خود تصویر تنظیم می‌شود. این مقدار فقط سقف ارتفاع است.', 'tadris' ),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'گردی کارت', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 30 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 14,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zvs__slide-link' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .webmz-zvs__slide-link',
			)
		);

		$this->add_control(
			'badge_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zvs__badge' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'show_badge_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'badge_icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zvs__badge' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_badge_icon' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls(
			'title_style',
			esc_html__( 'عنوان', 'tadris' ),
			'.webmz-zvs__title'
		);

		$this->update_control(
			'title_style_color',
			array(
				'default' => '#ffffff',
			)
		);

		$this->start_controls_section(
			'subtitle_style',
			array(
				'label' => esc_html__( 'زیرعنوان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .webmz-zvs__subtitle',
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.78)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zvs__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'subtitle_spacing',
			array(
				'label'      => esc_html__( 'فاصله از عنوان', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 8,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zvs__subtitle' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_style',
			array(
				'label' => esc_html__( 'دکمه', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .webmz-zvs__btn',
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => '14',
					'right'    => '24',
					'bottom'   => '14',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zvs__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => esc_html__( 'گردی دکمه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zvs__btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_spacing',
			array(
				'label'      => esc_html__( 'فاصله از بالا', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zvs__btn' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		$this->start_controls_tab(
			'button_normal_tab',
			array(
				'label' => esc_html__( 'عادی', 'tadris' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_background',
				'selector' => '{{WRAPPER}} .webmz-zvs__btn',
				'fields_options' => array(
					'background' => array(
						'default' => 'gradient',
					),
					'color'      => array(
						'default' => 'var(--webmz-color-primary)',
					),
					'color_b'    => array(
						'default' => 'var(--webmz-color-primary-hover)',
					),
					'gradient_angle' => array(
						'default' => array(
							'unit' => 'deg',
							'size' => 180,
						),
					),
				),
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zvs__btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_hover_tab',
			array(
				'label' => esc_html__( 'هاور', 'tadris' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_hover_background',
				'selector' => '{{WRAPPER}} .webmz-zvs__btn:hover, {{WRAPPER}} .webmz-zvs__btn:focus-visible',
			)
		);

		$this->add_control(
			'button_hover_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zvs__btn:hover, {{WRAPPER}} .webmz-zvs__btn:focus-visible' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Build WP_Query args for category/tag sources.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<string,mixed>
	 */
	private function query_args( $settings ) {
		$count = min( 5, max( 1, absint( $settings['count'] ?? 5 ) ) );

		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $count,
			'no_found_rows'  => true,
		);

		$source = $settings['source'] ?? 'category';

		if ( 'category' === $source && ! empty( $settings['category'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => sanitize_title( $settings['category'] ),
				),
			);
		} elseif ( 'tag' === $source && ! empty( $settings['tag'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_tag',
					'field'    => 'slug',
					'terms'    => sanitize_title( $settings['tag'] ),
				),
			);
		}

		$order_by = $settings['order_by'] ?? 'date';

		switch ( $order_by ) {
			case 'popularity':
				$args['meta_key'] = 'total_sales';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;

			case 'rating':
				$args['meta_key'] = '_wc_average_rating';
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
	 * Resolve product IDs from settings.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<int>
	 */
	private function get_product_ids( $settings ) {
		$source = $settings['source'] ?? 'category';

		if ( 'manual' === $source ) {
			$raw = isset( $settings['product_ids'] ) ? $settings['product_ids'] : array();

			if ( ! is_array( $raw ) ) {
				$raw = array( $raw );
			}

			return array_slice( array_values( array_unique( array_filter( array_map( 'absint', $raw ) ) ) ), 0, 5 );
		}

		$query = new WP_Query( $this->query_args( $settings ) );
		$ids   = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$ids[] = get_the_ID();
			}
			wp_reset_postdata();
		}

		return $ids;
	}

	/**
	 * Render one slide card.
	 *
	 * @param int                 $product_id Product ID.
	 * @param array<string,mixed> $settings   Widget settings.
	 * @return void
	 */
	private function render_slide( $product_id, $settings ) {
		$product = function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;

		if ( ! $product ) {
			return;
		}

		$image_id   = $product->get_image_id();
		$thumb_url  = $image_id
			? wp_get_attachment_image_url( $image_id, webmz_get_loop_image_size() )
			: get_the_post_thumbnail_url( $product_id, webmz_get_loop_image_size() );

		$title     = $product->get_name();
		$link      = 'yes' === ( $settings['product_link'] ?? 'yes' );
		$permalink = $product->get_permalink();
		$tag       = ( $link && $permalink ) ? 'a' : 'div';
		$key       = 'zvs_slide_link_' . $product_id;

		$this->add_render_attribute( $key, 'class', 'webmz-zvs__slide-link' );

		if ( $link && $permalink ) {
			$this->add_render_attribute( $key, 'href', esc_url( $permalink ) );
			$this->add_render_attribute( $key, 'aria-label', esc_attr( $title ) );
		}

		?>
		<div class="swiper-slide">
			<article class="webmz-zvs__slide">
				<<?php echo tag_escape( $tag ); ?> <?php echo $this->get_render_attribute_string( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span class="webmz-zvs__slide-media">
						<?php if ( $thumb_url ) : ?>
							<img
								class="webmz-zvs__slide-img"
								src="<?php echo esc_url( $thumb_url ); ?>"
								alt="<?php echo esc_attr( $title ); ?>"
								loading="lazy"
								decoding="async"
								draggable="false"
							>
						<?php else : ?>
							<span class="webmz-zvs__slide-placeholder" aria-hidden="true"></span>
						<?php endif; ?>
					</span>
				</<?php echo tag_escape( $tag ); ?>>
			</article>
		</div>
		<?php
	}

	protected function render() {
		if ( ! function_exists( 'wc_get_product' ) ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'برای استفاده از این ویجت، ووکامرس باید فعال باشد.', 'tadris' ) . '</div>';
			return;
		}

		$settings    = $this->get_settings_for_display();
		$product_ids = $this->get_product_ids( $settings );

		if ( empty( $product_ids ) ) {
			$message = 'manual' === ( $settings['source'] ?? '' )
				? esc_html__( '۵ محصول را از بخش محتوا انتخاب کنید.', 'tadris' )
				: esc_html__( 'محصولی برای نمایش یافت نشد.', 'tadris' );
			echo '<div class="webmz-editor-placeholder">' . esc_html( $message ) . '</div>';
			return;
		}

		$title_tag  = $this->webmz_get_title_tag( $settings );
		$show_decor = 'yes' === ( $settings['show_decor'] ?? 'yes' );
		$show_badge = 'yes' === ( $settings['show_badge_icon'] ?? '' );
		$classes    = 'webmz-zvs' . ( $show_decor ? ' webmz-zvs--decor' : '' );

		$slider_config = array(
			'autoplay'      => 'yes' === ( $settings['autoplay'] ?? 'yes' ),
			'delay'         => absint( $settings['autoplay_delay'] ?? 3500 ),
			'loop'          => 'yes' === ( $settings['loop'] ?? 'yes' ),
			'speed'         => absint( $settings['slide_speed'] ?? 550 ),
			'slidesPerView' => isset( $settings['slides_per_view'] ) ? (float) $settings['slides_per_view'] : 1.38,
			'spaceBetween'  => 18,
		);

		$button_text = isset( $settings['button_text'] ) ? trim( (string) $settings['button_text'] ) : '';
		?>
		<div class="<?php echo esc_attr( $classes ); ?>" dir="rtl">
			<span class="webmz-zvs__accent" aria-hidden="true"></span>

			<div class="webmz-zvs__slider">
				<div class="webmz-zvs__slider-viewport">
					<div
						class="webmz-zvs__swiper swiper"
						data-webmz-zhaket-vertical-slider="<?php echo esc_attr( wp_json_encode( $slider_config ) ); ?>"
					>
						<div class="swiper-wrapper">
							<?php foreach ( $product_ids as $product_id ) : ?>
								<?php $this->render_slide( $product_id, $settings ); ?>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<?php if ( $show_badge && ! empty( $settings['badge_icon']['value'] ) ) : ?>
					<span class="webmz-zvs__badge" aria-hidden="true">
						<?php Icons_Manager::render_icon( $settings['badge_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<<?php echo esc_html( $title_tag ); ?> class="webmz-zvs__title">
					<?php echo esc_html( $settings['title'] ); ?>
				</<?php echo esc_html( $title_tag ); ?>>
			<?php endif; ?>

			<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
				<p class="webmz-zvs__subtitle"><?php echo esc_html( $settings['subtitle'] ); ?></p>
			<?php endif; ?>

			<?php if ( '' !== $button_text ) : ?>
				<?php
				$url_settings = ! empty( $settings['button_url'] ) && is_array( $settings['button_url'] ) ? $settings['button_url'] : array();
				$this->add_render_attribute( 'zvs_button', 'class', 'webmz-zvs__btn' );

				if ( ! empty( $url_settings['url'] ) ) {
					$this->add_link_attributes( 'zvs_button', $url_settings );
				} else {
					$this->add_render_attribute( 'zvs_button', 'href', '#' );
				}
				?>
				<a <?php echo $this->get_render_attribute_string( 'zvs_button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php echo esc_html( $button_text ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}
