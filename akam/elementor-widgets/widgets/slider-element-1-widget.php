<?php
/**
 * Slider Element 1 — hero slider with illustration, pattern, and dual CTAs.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Hero slider widget with repeater slides.
 */
class Slider_Element_1_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-slider-element-1';
	}

	public function get_title() {
		return esc_html__( 'اسلایدر المان ۱', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-slides';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-slider-element-1' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-slider-element-1' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_slider_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'اسلایدها', 'tadris' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'slide_image',
			array(
				'label'       => esc_html__( 'تصویر اصلی (با قاب)', 'tadris' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => array( 'url' => Utils::get_placeholder_image_src() ),
				'description' => esc_html__( 'تصویر کامل شامل قاب و آبجکت‌ها را آپلود کنید.', 'tadris' ),
			)
		);

		$repeater->add_control(
			'pattern_image',
			array(
				'label'       => esc_html__( 'پترن تزئینی', 'tadris' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => esc_html__( 'تصویر پترن که بالای سمت چپ عکس اصلی و پشت آن نمایش داده می‌شود.', 'tadris' ),
			)
		);

		$repeater->add_control(
			'eyebrow',
			array(
				'label'       => esc_html__( 'برچسب بالای عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'وب پرداز', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'title_tag',
			array(
				'label'   => esc_html__( 'تگ عنوان', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
				),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => wp_kses_post( __( 'همین حالا <strong>شروع</strong> کن', 'tadris' ) ),
				'label_block' => true,
				'description' => esc_html__( 'برای هایلایت نارنجی، کلمه را داخل تگ strong قرار دهید.', 'tadris' ),
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label'       => esc_html__( 'توضیحات', 'tadris' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است.', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'primary_button_text',
			array(
				'label'   => esc_html__( 'متن دکمه اصلی', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'دوره جامع', 'tadris' ),
			)
		);

		$repeater->add_control(
			'primary_button_link',
			array(
				'label'   => esc_html__( 'لینک دکمه اصلی', 'tadris' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$repeater->add_control(
			'secondary_button_text',
			array(
				'label'   => esc_html__( 'متن دکمه دوم', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'دوره‌های رایگان', 'tadris' ),
			)
		);

		$repeater->add_control(
			'secondary_button_link',
			array(
				'label'   => esc_html__( 'لینک دکمه دوم', 'tadris' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'slides',
			array(
				'label'       => esc_html__( 'لیست اسلایدها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ eyebrow }}} — {{{ title }}}',
				'default'     => array(
					array(
						'eyebrow' => esc_html__( 'وب پرداز', 'tadris' ),
						'title'   => wp_kses_post( __( 'همین حالا <strong>شروع</strong> کن', 'tadris' ) ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_slider_controls() {
		$this->start_controls_section(
			'slider_settings',
			array(
				'label' => esc_html__( 'تنظیمات اسلایدر', 'tadris' ),
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
				'default'   => 5000,
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
			'show_accent_card',
			array(
				'label'        => esc_html__( 'نمایش کارت بنفش', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'prev_icon',
			array(
				'label'   => esc_html__( 'آیکون قبلی', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'next_icon',
			array(
				'label'   => esc_html__( 'آیکون بعدی', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->webmz_register_box_style_controls(
			'style_container',
			esc_html__( 'کانتینر', 'tadris' ),
			'.webmz-slider-element-1',
			array( 'default_background' => '#ffffff' )
		);

		$this->start_controls_section(
			'style_accent_card',
			array(
				'label' => esc_html__( 'کارت رنگی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'accent_card_color',
			array(
				'label'     => esc_html__( 'رنگ کارت', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-slider-element-1__accent-card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'accent_card_width',
			array(
				'label'      => esc_html__( 'عرض', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 40, 'max' => 200 ),
					'%'  => array( 'min' => 10, 'max' => 50 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-slider-element-1__accent-card' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'accent_card_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-slider-element-1__accent-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_visual',
			array(
				'label' => esc_html__( 'تصویر اصلی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'visual_width',
			array(
				'label'      => esc_html__( 'حداکثر عرض', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 160, 'max' => 600 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-slider-element-1__visual' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'visual_shadow',
				'selector' => '{{WRAPPER}} .webmz-slider-element-1__image',
			)
		);

		$this->add_control(
			'pattern_opacity',
			array(
				'label'     => esc_html__( 'شفافیت پترن', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ),
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-slider-element-1__pattern' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pattern_size',
			array(
				'label'      => esc_html__( 'اندازه پترن', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 60, 'max' => 400 ),
					'%'  => array( 'min' => 20, 'max' => 100 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-slider-element-1__pattern' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_content',
			array(
				'label' => esc_html__( 'ناحیه محتوا', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-slider-element-1__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_eyebrow',
			array(
				'label' => esc_html__( 'برچسب', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-slider-element-1__eyebrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .webmz-slider-element-1__eyebrow',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_title',
			array(
				'label' => esc_html__( 'عنوان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-slider-element-1__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_accent_color',
			array(
				'label'     => esc_html__( 'رنگ بخش برجسته', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-secondary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-slider-element-1__title strong, {{WRAPPER}} .webmz-slider-element-1__title span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .webmz-slider-element-1__title',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_description',
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
				'selectors' => array(
					'{{WRAPPER}} .webmz-slider-element-1__description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .webmz-slider-element-1__description',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_buttons',
			array(
				'label' => esc_html__( 'دکمه‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'buttons_typography',
				'selector' => '{{WRAPPER}} .webmz-slider-element-1__button',
			)
		);

		$this->add_responsive_control(
			'buttons_gap',
			array(
				'label'      => esc_html__( 'فاصله دکمه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-slider-element-1__actions' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'solid_button_heading',
			array(
				'label'     => esc_html__( 'دکمه اصلی (پررنگ)', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'solid_button_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-slider-element-1__button--solid' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'solid_button_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-secondary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-slider-element-1__button--solid' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'outline_button_heading',
			array(
				'label'     => esc_html__( 'دکمه دوم (خطی)', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'outline_button_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-secondary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-slider-element-1__button--outline' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'outline_button_border',
			array(
				'label'     => esc_html__( 'رنگ خط دور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-secondary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-slider-element-1__button--outline' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'outline_button_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-slider-element-1__button--outline' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_nav',
			array(
				'label' => esc_html__( 'کنترل اسلایدر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'nav_offset_top',
			array(
				'label'      => esc_html__( 'فاصله از محتوا', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 120 ),
				),
				'default'    => array(
					'size' => 28,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-slider-element-1__content' => 'padding-bottom: calc({{SIZE}}{{UNIT}} + 40px);',
				),
			)
		);

		$this->add_control(
			'nav_arrow_color',
			array(
				'label'     => esc_html__( 'رنگ فلش‌ها', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-slider-element-1__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => esc_html__( 'رنگ نقطه‌ها', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-slider-element-1__pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_active_color',
			array(
				'label'     => esc_html__( 'رنگ نقطه فعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-secondary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-slider-element-1__pagination .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render slide image.
	 *
	 * @param array<string,mixed> $image Image settings.
	 * @return void
	 */
	protected function render_slide_image( $image ) {
		$image = is_array( $image ) ? $image : array();

		if ( ! empty( $image['id'] ) ) {
			echo wp_get_attachment_image(
				absint( $image['id'] ),
				'large',
				false,
				array( 'class' => 'webmz-slider-element-1__image' )
			);
			return;
		}

		$url = ! empty( $image['url'] ) ? $image['url'] : Utils::get_placeholder_image_src();
		printf(
			'<img class="webmz-slider-element-1__image" src="%s" alt="">',
			esc_url( $url )
		);
	}

	/**
	 * Render pattern image behind main illustration.
	 *
	 * @param array<string,mixed> $pattern Pattern image settings.
	 * @return void
	 */
	protected function render_pattern_image( $pattern ) {
		$pattern = is_array( $pattern ) ? $pattern : array();

		if ( ! empty( $pattern['id'] ) ) {
			echo '<div class="webmz-slider-element-1__pattern" aria-hidden="true">';
			echo wp_get_attachment_image(
				absint( $pattern['id'] ),
				'medium',
				false,
				array( 'class' => 'webmz-slider-element-1__pattern-img' )
			);
			echo '</div>';
			return;
		}

		$url = ! empty( $pattern['url'] ) ? $pattern['url'] : '';

		if ( '' === $url ) {
			return;
		}

		printf(
			'<div class="webmz-slider-element-1__pattern" aria-hidden="true"><img class="webmz-slider-element-1__pattern-img" src="%s" alt=""></div>',
			esc_url( $url )
		);
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$slides   = ! empty( $settings['slides'] ) && is_array( $settings['slides'] ) ? $settings['slides'] : array();

		if ( empty( $slides ) ) {
			return;
		}

		$config = array(
			'loop'     => 'yes' === $settings['loop'],
			'autoplay' => 'yes' === $settings['autoplay'],
			'delay'    => max( 1000, absint( $settings['autoplay_delay'] ?? 5000 ) ),
		);

		$show_card = 'yes' === ( $settings['show_accent_card'] ?? 'yes' );
		?>
		<div
			class="webmz-slider-element-1 swiper"
			dir="rtl"
			data-webmz-slider-element-1="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
		>
			<div class="swiper-wrapper">
				<?php foreach ( $slides as $index => $slide ) : ?>
					<?php
					$title_tag  = ! empty( $slide['title_tag'] ) ? strtolower( (string) $slide['title_tag'] ) : 'h2';
					$allowed    = array( 'h1', 'h2', 'h3', 'h4' );
					$title_tag  = in_array( $title_tag, $allowed, true ) ? $title_tag : 'h2';
					$title      = ! empty( $slide['title'] ) ? $slide['title'] : '';
					$title_html = wp_kses(
						$title,
						array(
							'br'     => array(),
							'strong' => array(),
							'span'   => array( 'class' => array() ),
						)
					);

					$primary_key   = 'primary_btn_' . $index;
					$secondary_key = 'secondary_btn_' . $index;

					if ( ! empty( $slide['primary_button_link'] ) && is_array( $slide['primary_button_link'] ) ) {
						$this->add_link_attributes( $primary_key, $slide['primary_button_link'] );
					}
					if ( ! empty( $slide['secondary_button_link'] ) && is_array( $slide['secondary_button_link'] ) ) {
						$this->add_link_attributes( $secondary_key, $slide['secondary_button_link'] );
					}
					?>
					<div class="swiper-slide">
						<div class="webmz-slider-element-1__slide">
							<?php if ( $show_card ) : ?>
								<div class="webmz-slider-element-1__accent-card" aria-hidden="true"></div>
							<?php endif; ?>

							<div class="webmz-slider-element-1__content">
								<div class="webmz-slider-element-1__body">
									<?php if ( ! empty( $slide['eyebrow'] ) ) : ?>
										<span class="webmz-slider-element-1__eyebrow"><?php echo esc_html( $slide['eyebrow'] ); ?></span>
									<?php endif; ?>

									<?php if ( '' !== $title_html ) : ?>
										<<?php echo esc_html( $title_tag ); ?> class="webmz-slider-element-1__title"><?php echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_html( $title_tag ); ?>>
									<?php endif; ?>

									<?php if ( ! empty( $slide['description'] ) ) : ?>
										<p class="webmz-slider-element-1__description"><?php echo esc_html( $slide['description'] ); ?></p>
									<?php endif; ?>

									<?php if ( ! empty( $slide['primary_button_text'] ) || ! empty( $slide['secondary_button_text'] ) ) : ?>
										<div class="webmz-slider-element-1__actions">
											<?php if ( ! empty( $slide['primary_button_text'] ) ) : ?>
												<a class="webmz-slider-element-1__button webmz-slider-element-1__button--solid" <?php echo $this->get_render_attribute_string( $primary_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
													<?php echo esc_html( $slide['primary_button_text'] ); ?>
												</a>
											<?php endif; ?>
											<?php if ( ! empty( $slide['secondary_button_text'] ) ) : ?>
												<a class="webmz-slider-element-1__button webmz-slider-element-1__button--outline" <?php echo $this->get_render_attribute_string( $secondary_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
													<?php echo esc_html( $slide['secondary_button_text'] ); ?>
												</a>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</div>
							</div>

							<div class="webmz-slider-element-1__visual-wrap">
								<div class="webmz-slider-element-1__visual">
									<?php $this->render_pattern_image( $slide['pattern_image'] ?? array() ); ?>
									<?php $this->render_slide_image( $slide['slide_image'] ?? array() ); ?>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( count( $slides ) > 1 ) : ?>
				<?php if ( 'yes' === $settings['autoplay'] ) : ?>
					<div class="webmz-slider-element-1__autoplay-progress" aria-hidden="true">
						<svg viewBox="0 0 36 36" focusable="false">
							<circle class="webmz-slider-element-1__autoplay-track" cx="18" cy="18" r="15"></circle>
							<circle class="webmz-slider-element-1__autoplay-ring" cx="18" cy="18" r="15"></circle>
						</svg>
					</div>
				<?php endif; ?>
				<div class="webmz-slider-element-1__controls">
					<button class="webmz-slider-element-1__arrow webmz-slider-element-1__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'اسلاید قبلی', 'tadris' ); ?>">
						<?php Icons_Manager::render_icon( $settings['prev_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</button>
					<div class="webmz-slider-element-1__pagination"></div>
					<button class="webmz-slider-element-1__arrow webmz-slider-element-1__arrow--next" type="button" aria-label="<?php esc_attr_e( 'اسلاید بعدی', 'tadris' ); ?>">
						<?php Icons_Manager::render_icon( $settings['next_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</button>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
