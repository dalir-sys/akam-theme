<?php
/**
 * Contact Us banner widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Horizontal contact banner with protruding image, content, and contact buttons.
 */
class Contact_Us_Banner_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-contact-us-banner';
	}

	public function get_title() {
		return esc_html__( 'باکس تماس با ما', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'contact', 'banner', 'phone', 'telegram', 'whatsapp', 'تماس', 'بنر' );
	}

	public function get_style_depends() {
		return array( 'webmz-contact-us-banner' );
	}

	protected function register_controls() {
		$this->register_image_controls();
		$this->register_content_controls();
		$this->register_contact_buttons_controls();
		$this->register_style_controls();
	}

	protected function register_image_controls() {
		$this->start_controls_section(
			'section_image',
			array(
				'label' => esc_html__( 'تصویر', 'tadris' ),
			)
		);

		$this->add_control(
			'banner_image',
			array(
				'label'   => esc_html__( 'تصویر باکس', 'tadris' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'banner_image_size',
			array(
				'label'   => esc_html__( 'سایز رندر تصویر', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'large',
				'options' => array(
					'medium'       => esc_html__( 'متوسط', 'tadris' ),
					'medium_large' => esc_html__( 'متوسط بزرگ', 'tadris' ),
					'large'        => esc_html__( 'بزرگ', 'tadris' ),
					'full'         => esc_html__( 'کامل', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'banner_image_alt',
			array(
				'label'       => esc_html__( 'متن جایگزین تصویر', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'به مشاوره نیاز دارید؟', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control();

		$this->add_control(
			'description',
			array(
				'label'       => esc_html__( 'توضیحات', 'tadris' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'تیم پشتیبانی ما آماده پاسخگویی به سوالات شماست. از طریق راه‌های ارتباطی زیر با ما در تماس باشید.', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'phone_heading',
			array(
				'label'     => esc_html__( 'تماس تلفنی', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'phone_number',
			array(
				'label'       => esc_html__( 'شماره تماس', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '۰۹۱۵ ۰۰۰ ۰۰۰۰',
				'label_block' => true,
			)
		);

		$this->add_control(
			'phone_link',
			array(
				'label'   => esc_html__( 'لینک شماره', 'tadris' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => 'tel:09150000000' ),
			)
		);

		$this->add_control(
			'call_button_text',
			array(
				'label'   => esc_html__( 'متن دکمه تماس', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'تماس بگیر', 'tadris' ),
			)
		);

		$this->add_control(
			'call_button_link',
			array(
				'label'   => esc_html__( 'لینک دکمه تماس', 'tadris' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => 'tel:09150000000' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_contact_buttons_controls() {
		$this->start_controls_section(
			'section_contacts',
			array(
				'label' => esc_html__( 'دکمه‌های تماس', 'tadris' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon_image',
			array(
				'label' => esc_html__( 'تصویر آیکون', 'tadris' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'label',
			array(
				'label'       => esc_html__( 'متن', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'پشتیبانی', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'   => esc_html__( 'لینک', 'tadris' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$repeater->add_control(
			'bg_color',
			array(
				'label'   => esc_html__( 'رنگ پس‌زمینه', 'tadris' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#2aabee',
			)
		);

		$repeater->add_control(
			'bg_color_end',
			array(
				'label'       => esc_html__( 'رنگ دوم گرادیان (اختیاری)', 'tadris' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'اگر پر شود، پس‌زمینه گرادیان می‌شود.', 'tadris' ),
			)
		);

		$repeater->add_control(
			'show_arrow',
			array(
				'label'        => esc_html__( 'نمایش فلش', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'contact_buttons',
			array(
				'label'       => esc_html__( 'لیست دکمه‌ها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array(
						'label'    => esc_html__( 'پشتیبانی تلگرام', 'tadris' ),
						'bg_color' => '#2aabee',
						'link'     => array( 'url' => '#' ),
					),
					array(
						'label'        => esc_html__( 'پشتیبانی واتس اپ', 'tadris' ),
						'bg_color'     => '#25d366',
						'bg_color_end' => '#128c7e',
						'link'         => array( 'url' => '#' ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->webmz_register_box_style_controls(
			'style_box',
			esc_html__( 'کادر بنر', 'tadris' ),
			'.webmz-cu-banner',
			array(
				'default_background' => '#7c3aed',
				'gradient'             => array(
					'color_b' => '#5b21b6',
					'angle'   => 135,
				),
			)
		);

		$this->start_controls_section(
			'style_layout',
			array(
				'label' => esc_html__( 'چیدمان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'layout_gap',
			array(
				'label'      => esc_html__( 'فاصله بین بخش‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 24 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cu-banner' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_padding_top',
			array(
				'label'      => esc_html__( 'فاصله بالای ویجت (برای بیرون‌زدگی تصویر)', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 40 ),
				'selectors'  => array(
					'{{WRAPPER}}' => 'padding-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_image',
			array(
				'label' => esc_html__( 'تصویر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'      => esc_html__( 'عرض تصویر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 80, 'max' => 400 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 200 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cu-banner__media-img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_overflow_top',
			array(
				'label'       => esc_html__( 'بیرون‌زدن از بالا', 'tadris' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 60 ),
				'description' => esc_html__( 'میزان بیرون‌زدگی تصویر از لبه بالای باکس.', 'tadris' ),
				'selectors'   => array(
					'{{WRAPPER}} .webmz-cu-banner__media' => 'margin-top: calc(-1 * {{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->add_responsive_control(
			'image_overflow_bottom',
			array(
				'label'       => esc_html__( 'بیرون‌زدن از پایین', 'tadris' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'range'       => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'     => array( 'unit' => 'px', 'size' => 28 ),
				'description' => esc_html__( 'با margin-bottom منفی، فاصله پایین تصویر ناشی از padding باکس جبران می‌شود.', 'tadris' ),
				'selectors'   => array(
					'{{WRAPPER}} .webmz-cu-banner__media' => 'margin-bottom: calc(-1 * {{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->add_responsive_control(
			'image_align',
			array(
				'label'     => esc_html__( 'تراز عمودی تصویر', 'tadris' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-end'   => array(
						'title' => esc_html__( 'پایین', 'tadris' ),
						'icon'  => 'eicon-v-align-bottom',
					),
					'center'     => array(
						'title' => esc_html__( 'وسط', 'tadris' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-start' => array(
						'title' => esc_html__( 'بالا', 'tadris' ),
						'icon'  => 'eicon-v-align-top',
					),
				),
				'default'   => 'flex-end',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cu-banner__media' => 'align-self: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} .webmz-cu-banner__media-img',
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls(
			'style_title',
			esc_html__( 'عنوان', 'tadris' ),
			'.webmz-cu-banner__title',
			'#ffffff'
		);

		$this->webmz_register_text_style_controls(
			'style_description',
			esc_html__( 'توضیحات', 'tadris' ),
			'.webmz-cu-banner__description',
			'rgba(255,255,255,0.88)'
		);

		$this->start_controls_section(
			'style_phone',
			array(
				'label' => esc_html__( 'شماره تماس', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'phone_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cu-banner__phone' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'phone_typography',
				'selector' => '{{WRAPPER}} .webmz-cu-banner__phone',
			)
		);

		$this->add_responsive_control(
			'phone_row_gap',
			array(
				'label'      => esc_html__( 'فاصله از دکمه تماس', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 16 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cu-banner__phone-row' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_call_button',
			array(
				'label' => esc_html__( 'دکمه تماس', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'call_btn_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7c3aed',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cu-banner__call-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'call_btn_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cu-banner__call-btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'call_btn_hover_color',
			array(
				'label'     => esc_html__( 'رنگ متن هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-cu-banner__call-btn:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'call_btn_hover_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-cu-banner__call-btn:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'call_btn_typography',
				'selector' => '{{WRAPPER}} .webmz-cu-banner__call-btn',
			)
		);

		$this->add_responsive_control(
			'call_btn_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'top'    => '999',
					'right'  => '999',
					'bottom' => '999',
					'left'   => '999',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cu-banner__call-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'call_btn_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => '10',
					'right'  => '24',
					'bottom' => '10',
					'left'   => '24',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cu-banner__call-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'call_btn_shadow',
				'selector' => '{{WRAPPER}} .webmz-cu-banner__call-btn',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_contact_buttons',
			array(
				'label' => esc_html__( 'دکمه‌های تماس', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'contact_btn_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cu-banner__contact-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'contact_btn_typography',
				'selector' => '{{WRAPPER}} .webmz-cu-banner__contact-btn',
			)
		);

		$this->add_responsive_control(
			'contact_btn_gap',
			array(
				'label'      => esc_html__( 'فاصله بین دکمه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 10 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cu-banner__contacts' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'contact_btn_min_width',
			array(
				'label'      => esc_html__( 'حداقل عرض', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 120, 'max' => 400 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 200 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cu-banner__contact-btn' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'contact_btn_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => '12',
					'right'  => '16',
					'bottom' => '12',
					'left'   => '16',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cu-banner__contact-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'contact_btn_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'top'    => '12',
					'right'  => '12',
					'bottom' => '12',
					'left'   => '12',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cu-banner__contact-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'contact_icon_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 16, 'max' => 64 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 28 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cu-banner__contact-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'contact_arrow_color',
			array(
				'label'     => esc_html__( 'رنگ فلش', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.85)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cu-banner__contact-arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'contact_btn_shadow',
				'selector' => '{{WRAPPER}} .webmz-cu-banner__contact-btn',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render banner image.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return string
	 */
	private function get_image_html( $settings ) {
		$image = ! empty( $settings['banner_image'] ) && is_array( $settings['banner_image'] ) ? $settings['banner_image'] : array();
		$size  = ! empty( $settings['banner_image_size'] ) ? sanitize_key( $settings['banner_image_size'] ) : 'large';
		$alt   = ! empty( $settings['banner_image_alt'] ) ? $settings['banner_image_alt'] : '';

		$allowed_sizes = array( 'medium', 'medium_large', 'large', 'full' );
		if ( ! in_array( $size, $allowed_sizes, true ) ) {
			$size = 'large';
		}

		if ( ! empty( $image['id'] ) ) {
			return wp_get_attachment_image(
				absint( $image['id'] ),
				$size,
				false,
				array(
					'class'    => 'webmz-cu-banner__media-img',
					'alt'      => $alt,
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);
		}

		$url = ! empty( $image['url'] ) ? $image['url'] : Utils::get_placeholder_image_src();

		return sprintf(
			'<img class="webmz-cu-banner__media-img" src="%s" alt="%s" loading="lazy" decoding="async">',
			esc_url( $url ),
			esc_attr( $alt )
		);
	}

	/**
	 * Build inline background style for a contact button.
	 *
	 * @param array<string,mixed> $item Repeater item.
	 * @return string
	 */
	private function get_contact_btn_bg_style( $item ) {
		$start = ! empty( $item['bg_color'] ) ? (string) $item['bg_color'] : '#2aabee';
		$end   = ! empty( $item['bg_color_end'] ) ? (string) $item['bg_color_end'] : '';

		if ( $end ) {
			return 'background: linear-gradient(135deg, ' . $start . ' 0%, ' . $end . ' 100%);';
		}

		return 'background-color: ' . $start . ';';
	}

	/**
	 * Render contact button icon.
	 *
	 * @param array<string,mixed> $item Repeater item.
	 * @return string
	 */
	private function get_contact_icon_html( $item ) {
		$icon = ! empty( $item['icon_image'] ) && is_array( $item['icon_image'] ) ? $item['icon_image'] : array();

		if ( ! empty( $icon['id'] ) ) {
			return wp_get_attachment_image(
				absint( $icon['id'] ),
				'thumbnail',
				false,
				array(
					'class'   => 'webmz-cu-banner__contact-icon',
					'alt'     => '',
					'loading' => 'lazy',
				)
			);
		}

		if ( ! empty( $icon['url'] ) ) {
			return sprintf(
				'<img class="webmz-cu-banner__contact-icon" src="%s" alt="" loading="lazy">',
				esc_url( $icon['url'] )
			);
		}

		return '';
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$title_tag  = $this->webmz_get_title_tag( $settings );
		$image_html = $this->get_image_html( $settings );
		$buttons    = ! empty( $settings['contact_buttons'] ) && is_array( $settings['contact_buttons'] ) ? $settings['contact_buttons'] : array();

		if ( ! empty( $settings['phone_link'] ) && is_array( $settings['phone_link'] ) ) {
			$this->add_link_attributes( 'phone_link', $settings['phone_link'] );
		}

		if ( ! empty( $settings['call_button_link'] ) && is_array( $settings['call_button_link'] ) ) {
			$this->add_link_attributes( 'call_button_link', $settings['call_button_link'] );
		}
		?>
		<div class="webmz-cu-banner">
			<?php if ( $image_html ) : ?>
				<div class="webmz-cu-banner__media">
					<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>

			<div class="webmz-cu-banner__body">
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<<?php echo esc_html( $title_tag ); ?> class="webmz-cu-banner__title"><?php echo esc_html( $settings['title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
				<?php endif; ?>

				<?php if ( ! empty( $settings['description'] ) ) : ?>
					<p class="webmz-cu-banner__description"><?php echo esc_html( $settings['description'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $settings['phone_number'] ) || ! empty( $settings['call_button_text'] ) ) : ?>
					<div class="webmz-cu-banner__phone-row">
						<?php if ( ! empty( $settings['phone_number'] ) ) : ?>
							<a class="webmz-cu-banner__phone" <?php echo $this->get_render_attribute_string( 'phone_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<?php echo esc_html( $settings['phone_number'] ); ?>
							</a>
						<?php endif; ?>

						<?php if ( ! empty( $settings['call_button_text'] ) ) : ?>
							<a class="webmz-cu-banner__call-btn" <?php echo $this->get_render_attribute_string( 'call_button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<?php echo esc_html( $settings['call_button_text'] ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $buttons ) ) : ?>
				<div class="webmz-cu-banner__contacts">
					<?php foreach ( $buttons as $index => $item ) : ?>
						<?php
						$link_key = 'contact_btn_' . $index;
						if ( ! empty( $item['link'] ) && is_array( $item['link'] ) ) {
							$this->add_link_attributes( $link_key, $item['link'] );
						}
						$icon_html = $this->get_contact_icon_html( $item );
						$label     = ! empty( $item['label'] ) ? $item['label'] : '';
						$show_arrow = ! isset( $item['show_arrow'] ) || 'yes' === $item['show_arrow'];
						?>
						<a
							class="webmz-cu-banner__contact-btn"
							style="<?php echo esc_attr( $this->get_contact_btn_bg_style( $item ) ); ?>"
							<?php echo $this->get_render_attribute_string( $link_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						>
							<?php if ( $show_arrow ) : ?>
								<span class="webmz-cu-banner__contact-arrow" aria-hidden="true">&lsaquo;</span>
							<?php endif; ?>
							<?php if ( $label ) : ?>
								<span class="webmz-cu-banner__contact-text"><?php echo esc_html( $label ); ?></span>
							<?php endif; ?>
							<?php if ( $icon_html ) : ?>
								<?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
