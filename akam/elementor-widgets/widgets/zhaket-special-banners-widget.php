<?php
/**
 * Zhaket special banners — header + image banner swiper.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Repeater-based promotional banner slider (Zhaket style).
 */
class Zhaket_Special_Banners_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-zhaket-special-banners';
	}

	public function get_title() {
		return esc_html__( 'بنرهای ویژه ژاکت', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'zhaket', 'banner', 'slider', 'swiper', 'ژاکت', 'بنر', 'اسلایدر', 'ویژه' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-zhaket-special-banners' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-zhaket-special-banners' );
	}

	protected function register_controls() {
		$this->register_header_controls();
		$this->register_banner_controls();
		$this->register_slider_controls();
		$this->register_layout_controls();
		$this->register_style_controls();
	}

	protected function register_header_controls() {
		$this->start_controls_section(
			'section_header',
			array(
				'label' => esc_html__( 'سرتیتر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_header',
			array(
				'label'        => esc_html__( 'نمایش سرتیتر', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'header_icon',
			array(
				'label'     => esc_html__( 'آیکون سرتیتر', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-square',
					'library' => 'fa-solid',
				),
				'condition' => array( 'show_header' => 'yes' ),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'header_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون سرتیتر', 'tadris' ),
			array( 'show_header' => 'yes' )
		);

		$this->add_control(
			'header_title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'ژاکت، انتخاب‌های متنوع', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_header' => 'yes' ),
			)
		);

		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ HTML عنوان', 'tadris' ), array( 'show_header' => 'yes' ) );

		$this->add_control(
			'header_description',
			array(
				'label'       => esc_html__( 'توضیحات', 'tadris' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( "جستجو کنید، بهترین محصولات را بیابید و سایت خود را مجهز کنید.\nبیش از هزار محصول متنوع در دسترس شماست!", 'tadris' ),
				'rows'        => 3,
				'label_block' => true,
				'condition'   => array( 'show_header' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_banner_controls() {
		$this->start_controls_section(
			'section_banners',
			array(
				'label' => esc_html__( 'بنرها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'banner_image',
			array(
				'label'   => esc_html__( 'تصویر بنر', 'tadris' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$repeater->add_control(
			'alt_text',
			array(
				'label'       => esc_html__( 'متن جایگزین تصویر', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'enable_link',
			array(
				'label'        => esc_html__( 'فعال‌سازی لینک', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'     => esc_html__( 'لینک', 'tadris' ),
				'type'      => Controls_Manager::URL,
				'default'   => array( 'url' => '' ),
				'condition' => array( 'enable_link' => 'yes' ),
			)
		);

		$this->add_control(
			'banners',
			array(
				'label'       => esc_html__( 'لیست بنرها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '<# if ( alt_text ) { #>{{{ alt_text }}}<# } else { #>' . esc_html__( 'بنر', 'tadris' ) . '<# } #>',
				'default'     => array(
					array(
						'banner_image' => array( 'url' => Utils::get_placeholder_image_src() ),
					),
					array(
						'banner_image' => array( 'url' => Utils::get_placeholder_image_src() ),
					),
					array(
						'banner_image' => array( 'url' => Utils::get_placeholder_image_src() ),
					),
					array(
						'banner_image' => array( 'url' => Utils::get_placeholder_image_src() ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_slider_controls() {
		$this->start_controls_section(
			'section_slider',
			array(
				'label' => esc_html__( 'تنظیمات اسلایدر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'پخش خودکار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => esc_html__( 'تأخیر پخش خودکار (ms)', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4500,
				'min'       => 1000,
				'max'       => 15000,
				'step'      => 500,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => esc_html__( 'حلقه بی‌نهایت', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'free_mode',
			array(
				'label'        => esc_html__( 'اسکرول آزاد', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'grab_cursor',
			array(
				'label'        => esc_html__( 'نشانگر دست', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_responsive_control(
			'space_between',
			array(
				'label'      => esc_html__( 'فاصله بین اسلایدها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
			)
		);

		$this->add_control(
			'show_navigation',
			array(
				'label'        => esc_html__( 'نمایش فلش‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'prev_icon',
			array(
				'label'     => esc_html__( 'آیکون قبلی', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				),
				'condition' => array( 'show_navigation' => 'yes' ),
			)
		);

		$this->add_control(
			'next_icon',
			array(
				'label'     => esc_html__( 'آیکون بعدی', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
				'condition' => array( 'show_navigation' => 'yes' ),
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'        => esc_html__( 'نمایش نقطه‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function register_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'چیدمان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'header_gap',
			array(
				'label'      => esc_html__( 'فاصله سرتیتر و اسلایدر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zsb' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_align',
			array(
				'label'   => esc_html__( 'تراز سرتیتر', 'tadris' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'right'  => array(
						'title' => esc_html__( 'راست', 'tadris' ),
						'icon'  => 'eicon-text-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'وسط', 'tadris' ),
						'icon'  => 'eicon-text-align-center',
					),
					'left'   => array(
						'title' => esc_html__( 'چپ', 'tadris' ),
						'icon'  => 'eicon-text-align-left',
					),
				),
				'default'   => 'right',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zsb__header' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_inner_gap',
			array(
				'label'      => esc_html__( 'فاصله آیکون و متن سرتیتر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zsb__heading-row' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_desc_gap',
			array(
				'label'      => esc_html__( 'فاصله عنوان و توضیحات', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zsb__header' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->webmz_register_box_style_controls(
			'box_style',
			esc_html__( 'کانتینر', 'tadris' ),
			'.webmz-zsb',
			array(
				'default_background' => 'transparent',
				'flat'               => true,
			)
		);

		$this->webmz_register_heading_icon_box_style_controls(
			'.webmz-zsb__header-icon',
			array(
				'condition'      => array( 'show_header' => 'yes' ),
				'bg_default'     => 'var(--webmz-color-primary)',
				'icon_default'   => '#ffffff',
				'use_icon_mode'  => false,
				'radius_default' => 6,
			)
		);

		$this->webmz_register_text_style_controls(
			'title_style',
			esc_html__( 'عنوان', 'tadris' ),
			'.webmz-zsb__title'
		);

		$this->start_controls_section(
			'description_style',
			array(
				'label' => esc_html__( 'توضیحات', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-gray)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zsb__description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .webmz-zsb__description',
			)
		);

		$this->add_responsive_control(
			'description_max_width',
			array(
				'label'      => esc_html__( 'حداکثر عرض', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 200, 'max' => 900 ),
					'%'  => array( 'min' => 30, 'max' => 100 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zsb__description' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'banner_style',
			array(
				'label' => esc_html__( 'کارت بنر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'banner_width',
			array(
				'label'      => esc_html__( 'عرض کارت', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 120, 'max' => 360 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 200,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zsb__slide' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'banner_height',
			array(
				'label'      => esc_html__( 'ارتفاع کارت', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 160, 'max' => 520 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 320,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zsb__card' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'banner_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => '16',
					'right'    => '16',
					'bottom'   => '16',
					'left'     => '16',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zsb__card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'            => 'banner_shadow',
				'selector'        => '{{WRAPPER}} .webmz-zsb__card',
				'fields_options'  => array(
					'box_shadow_type' => array(
						'default' => '',
					),
				),
			)
		);

		$this->add_control(
			'banner_hover_scale',
			array(
				'label'   => esc_html__( 'بزرگ‌نمایی در هاور', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1.03',
				'options' => array(
					'1'    => esc_html__( 'بدون تغییر', 'tadris' ),
					'1.02' => '1.02',
					'1.03' => '1.03',
					'1.05' => '1.05',
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zsb__card:hover' => 'transform: scale({{VALUE}});',
				),
			)
		);

		$this->add_control(
			'banner_image_fit',
			array(
				'label'   => esc_html__( 'نحوه نمایش تصویر', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => array(
					'cover'   => esc_html__( 'پوشش کامل', 'tadris' ),
					'contain' => esc_html__( 'جاگیری در کارت', 'tadris' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zsb__card img' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'navigation_style',
			array(
				'label'     => esc_html__( 'فلش‌های ناوبری', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_navigation' => 'yes' ),
			)
		);

		$this->add_control(
			'nav_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zsb__arrow' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zsb__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_size',
			array(
				'label'      => esc_html__( 'اندازه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 28, 'max' => 56 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 40,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zsb__arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pagination_style',
			array(
				'label'     => esc_html__( 'نقطه‌های صفحه‌بندی', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_pagination' => 'yes' ),
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zsb__pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_top_spacing',
			array(
				'label'      => esc_html__( 'فاصله از بالا', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 48 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zsb__pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build responsive space-between config for Swiper.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return int
	 */
	protected function get_space_between( $settings ) {
		$value = $settings['space_between'] ?? array();

		if ( is_array( $value ) && isset( $value['size'] ) ) {
			return max( 0, (int) $value['size'] );
		}

		return 20;
	}

	/**
	 * Get full-size banner image URL for lightbox.
	 *
	 * @param array<string,mixed> $item Banner item.
	 * @return string
	 */
	protected function get_banner_full_image_url( $item ) {
		$image = isset( $item['banner_image'] ) && is_array( $item['banner_image'] ) ? $item['banner_image'] : array();

		if ( ! empty( $image['id'] ) ) {
			$url = wp_get_attachment_image_url( absint( $image['id'] ), 'full' );

			if ( $url ) {
				return $url;
			}
		}

		return ! empty( $image['url'] ) ? (string) $image['url'] : '';
	}

	/**
	 * Render banner image.
	 *
	 * @param array<string,mixed> $item Banner item.
	 * @return void
	 */
	protected function render_banner_image( $item ) {
		$image = isset( $item['banner_image'] ) && is_array( $item['banner_image'] ) ? $item['banner_image'] : array();
		$alt   = isset( $item['alt_text'] ) ? trim( (string) $item['alt_text'] ) : '';

		if ( ! empty( $image['id'] ) ) {
			echo wp_get_attachment_image(
				absint( $image['id'] ),
				'large',
				false,
				array(
					'class'   => 'webmz-zsb__image',
					'alt'     => $alt,
					'loading' => 'lazy',
				)
			);
			return;
		}

		$url = ! empty( $image['url'] ) ? $image['url'] : Utils::get_placeholder_image_src();

		printf(
			'<img class="webmz-zsb__image" src="%s" alt="%s" loading="lazy" />',
			esc_url( $url ),
			esc_attr( $alt )
		);
	}

	/**
	 * Render a single banner slide.
	 *
	 * @param array<string,mixed> $item  Repeater item.
	 * @param int                 $index Item index.
	 * @return void
	 */
	protected function render_banner_slide( $item, $index ) {
		$image = isset( $item['banner_image'] ) && is_array( $item['banner_image'] ) ? $item['banner_image'] : array();

		if ( empty( $image['id'] ) && empty( $image['url'] ) ) {
			return;
		}

		$has_link  = 'yes' === ( $item['enable_link'] ?? '' ) && ! empty( $item['link']['url'] );
		$tag       = $has_link ? 'a' : 'div';
		$key       = 'zsb_banner_' . absint( $index );
		$full_url  = $this->get_banner_full_image_url( $item );
		$alt_text  = isset( $item['alt_text'] ) ? trim( (string) $item['alt_text'] ) : '';

		$this->add_render_attribute( $key, 'class', 'webmz-zsb__card webmz-zsb__card--lightbox' );
		$this->add_render_attribute( $key, 'data-webmz-zsb-lightbox-src', esc_url( $full_url ) );

		if ( '' !== $alt_text ) {
			$this->add_render_attribute( $key, 'data-webmz-zsb-lightbox-alt', esc_attr( $alt_text ) );
		}

		if ( $has_link ) {
			$this->add_link_attributes( $key, $item['link'] );
		} else {
			$this->add_render_attribute( $key, 'role', 'button' );
			$this->add_render_attribute( $key, 'tabindex', '0' );
		}
		?>
		<div class="swiper-slide webmz-zsb__slide">
			<<?php echo tag_escape( $tag ); ?> <?php echo $this->get_render_attribute_string( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php $this->render_banner_image( $item ); ?>
			</<?php echo tag_escape( $tag ); ?>>
		</div>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$banners  = ! empty( $settings['banners'] ) && is_array( $settings['banners'] ) ? $settings['banners'] : array();

		if ( empty( $banners ) ) {
			return;
		}

		$config = array(
			'autoplay'      => 'yes' === ( $settings['autoplay'] ?? '' ),
			'autoplayDelay' => max( 1000, absint( $settings['autoplay_delay'] ?? 4500 ) ),
			'loop'          => 'yes' === ( $settings['loop'] ?? 'yes' ),
			'freeMode'      => 'yes' === ( $settings['free_mode'] ?? 'yes' ),
			'grabCursor'    => 'yes' === ( $settings['grab_cursor'] ?? 'yes' ),
			'spaceBetween'  => $this->get_space_between( $settings ),
			'pagination'    => 'yes' === ( $settings['show_pagination'] ?? '' ),
			'navigation'    => 'yes' === ( $settings['show_navigation'] ?? '' ),
		);

		$show_header  = 'yes' === ( $settings['show_header'] ?? 'yes' );
		$icon_class   = $this->webmz_get_icon_color_mode_class( $settings, 'header_icon_color_mode' );
		$title_tag    = $this->webmz_get_title_tag( $settings );
		$title        = isset( $settings['header_title'] ) ? trim( (string) $settings['header_title'] ) : ( isset( $settings['title'] ) ? trim( (string) $settings['title'] ) : '' );
		$description  = isset( $settings['header_description'] ) ? trim( (string) $settings['header_description'] ) : ( isset( $settings['description'] ) ? trim( (string) $settings['description'] ) : '' );
		$has_nav      = 'yes' === ( $settings['show_navigation'] ?? '' );
		$has_pag      = 'yes' === ( $settings['show_pagination'] ?? '' );
		$slide_count  = 0;

		foreach ( $banners as $banner ) {
			$image = isset( $banner['banner_image'] ) && is_array( $banner['banner_image'] ) ? $banner['banner_image'] : array();

			if ( ! empty( $image['id'] ) || ! empty( $image['url'] ) ) {
				++$slide_count;
			}
		}

		if ( $slide_count < 1 ) {
			return;
		}
		?>
		<div class="webmz-zsb" dir="rtl">
			<?php if ( $show_header && ( '' !== $title || '' !== $description || ! empty( $settings['header_icon']['value'] ) ) ) : ?>
				<div class="webmz-zsb__header">
					<div class="webmz-zsb__heading-row">
						<?php if ( ! empty( $settings['header_icon']['value'] ) ) : ?>
							<span class="webmz-zsb__header-icon <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true">
								<?php Icons_Manager::render_icon( $settings['header_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							</span>
						<?php endif; ?>
						<?php if ( '' !== $title ) : ?>
							<?php
							printf(
								'<%1$s class="webmz-zsb__title">%2$s</%1$s>',
								tag_escape( $title_tag ),
								esc_html( $title )
							);
							?>
						<?php endif; ?>
					</div>
					<?php if ( '' !== $description ) : ?>
						<p class="webmz-zsb__description"><?php echo nl2br( esc_html( $description ) ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="webmz-zsb__slider-wrap">
				<?php if ( $has_nav ) : ?>
					<button type="button" class="webmz-zsb__arrow webmz-zsb__arrow--prev" aria-label="<?php esc_attr_e( 'قبلی', 'tadris' ); ?>">
						<?php Icons_Manager::render_icon( $settings['prev_icon'] ?? array(), array( 'aria-hidden' => 'true' ) ); ?>
					</button>
				<?php endif; ?>

				<div
					class="webmz-zsb__slider swiper"
					data-webmz-zhaket-special-banners="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
				>
					<div class="swiper-wrapper">
						<?php foreach ( $banners as $index => $banner ) : ?>
							<?php $this->render_banner_slide( $banner, $index ); ?>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( $has_nav ) : ?>
					<button type="button" class="webmz-zsb__arrow webmz-zsb__arrow--next" aria-label="<?php esc_attr_e( 'بعدی', 'tadris' ); ?>">
						<?php Icons_Manager::render_icon( $settings['next_icon'] ?? array(), array( 'aria-hidden' => 'true' ) ); ?>
					</button>
				<?php endif; ?>
			</div>

			<?php if ( $has_pag ) : ?>
				<div class="webmz-zsb__pagination swiper-pagination"></div>
			<?php endif; ?>
		</div>
		<?php
	}
}
