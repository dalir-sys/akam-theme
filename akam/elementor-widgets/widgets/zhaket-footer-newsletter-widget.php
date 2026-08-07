<?php
/**
 * Zhaket footer newsletter subscription box.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * RTL newsletter card styled like Zhaket marketplace footer.
 */
class Zhaket_Footer_Newsletter_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-zhaket-footer-newsletter';
	}

	public function get_title() {
		return esc_html__( 'خبرنامه فوتر ژاکت', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-email-field';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'newsletter', 'footer', 'zhaket', 'email', 'خبرنامه', 'فوتر', 'ژاکت', 'ایمیل' );
	}

	public function get_style_depends() {
		return array( 'webmz-zhaket-footer-newsletter' );
	}

	public function get_script_depends() {
		return array( 'webmz-blog-cta-widgets' );
	}

	/**
	 * Read a theme color option with CSS-variable fallback.
	 *
	 * @param string $key     Option key.
	 * @param string $css_var CSS custom property fallback.
	 * @param string $hex     Hard fallback hex.
	 * @return string
	 */
	private function theme_color( $key, $css_var, $hex ) {
		if ( function_exists( 'webmz_get_option' ) ) {
			$value = (string) webmz_get_option( $key );
			if ( '' !== $value ) {
				return $value;
			}
		}

		return 'var(' . $css_var . ', ' . $hex . ')';
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_layout_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'خبرنامه ژاکت', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control();

		$this->add_control(
			'show_badge',
			array(
				'label'        => esc_html__( 'نمایش نشان / لوگو', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'badge_type',
			array(
				'label'     => esc_html__( 'نوع نشان', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'icon',
				'options'   => array(
					'icon'  => esc_html__( 'آیکون', 'tadris' ),
					'image' => esc_html__( 'تصویر', 'tadris' ),
				),
				'condition' => array( 'show_badge' => 'yes' ),
			)
		);

		$this->add_control(
			'badge_icon',
			array(
				'label'     => esc_html__( 'آیکون نشان', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-envelope-open-text',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_badge' => 'yes',
					'badge_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'badge_image',
			array(
				'label'     => esc_html__( 'تصویر نشان', 'tadris' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'show_badge' => 'yes',
					'badge_type' => 'image',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'badge_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون نشان', 'tadris' ),
			array(
				'show_badge' => 'yes',
				'badge_type' => 'icon',
			)
		);

		$this->add_control(
			'email_placeholder',
			array(
				'label'       => esc_html__( 'متن placeholder ایمیل', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'ایمیل خود را وارد کنید', 'tadris' ),
				'label_block' => true,
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'show_email_icon',
			array(
				'label'        => esc_html__( 'نمایش آیکون ایمیل', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'email_icon',
			array(
				'label'     => esc_html__( 'آیکون ایمیل', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'far fa-envelope',
					'library' => 'fa-regular',
				),
				'condition' => array( 'show_email_icon' => 'yes' ),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'email_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون ایمیل', 'tadris' ),
			array( 'show_email_icon' => 'yes' )
		);

		$this->add_control(
			'submit_display',
			array(
				'label'     => esc_html__( 'نمایش دکمه ارسال', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'icon',
				'options'   => array(
					'icon'      => esc_html__( 'فقط آیکون', 'tadris' ),
					'text'      => esc_html__( 'فقط متن', 'tadris' ),
					'icon_text' => esc_html__( 'آیکون و متن', 'tadris' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'     => esc_html__( 'متن دکمه', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'عضویت', 'tadris' ),
				'condition' => array(
					'submit_display' => array( 'text', 'icon_text' ),
				),
			)
		);

		$this->add_control(
			'submit_icon',
			array(
				'label'     => esc_html__( 'آیکون دکمه', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-arrow-left',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'submit_display' => array( 'icon', 'icon_text' ),
				),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'submit_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون دکمه', 'tadris' ),
			array(
				'submit_display' => array( 'icon', 'icon_text' ),
			)
		);

		$this->add_control(
			'success_message',
			array(
				'label'       => esc_html__( 'پیام موفقیت', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'عضویت شما با موفقیت ثبت شد.', 'tadris' ),
				'label_block' => true,
				'separator'   => 'before',
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
			'content_gap',
			array(
				'label'      => esc_html__( 'فاصله عنوان و فرم', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zfn' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_gap',
			array(
				'label'      => esc_html__( 'فاصله نشان و عنوان', 'tadris' ),
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
					'{{WRAPPER}} .webmz-zfn__lead' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'input_bar_gap',
			array(
				'label'      => esc_html__( 'فاصله داخلی نوار ایمیل', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 24 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 8,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zfn__input-bar' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'mobile_stack',
			array(
				'label'        => esc_html__( 'چیدمان عمودی در موبایل', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->webmz_register_box_style_controls(
			'box_style',
			esc_html__( 'کارت', 'tadris' ),
			'.webmz-zfn',
			array(
				'default_background' => $this->theme_color( 'account_accent_light', '--webmz-account-accent-light', '#fff4e7' ),
			)
		);

		$this->start_controls_section(
			'title_style',
			array(
				'label' => esc_html__( 'عنوان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_text_dark', '--webmz-color-text-dark', '#111827' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zfn__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'selector'       => '{{WRAPPER}} .webmz-zfn__title',
				'fields_options' => array(
					'font_weight' => array(
						'default' => '700',
					),
					'font_size'   => array(
						'default' => array(
							'unit' => 'px',
							'size' => 18,
						),
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'badge_style',
			array(
				'label'     => esc_html__( 'نشان / لوگو', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_badge' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'badge_size',
			array(
				'label'      => esc_html__( 'اندازه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 24, 'max' => 80 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 36,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zfn__badge' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'badge_background_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zfn__badge' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_text_dark', '--webmz-color-text-dark', '#111827' ),
				'selectors' => $this->webmz_get_icon_color_value_selectors( '{{WRAPPER}} .webmz-zfn__badge' ),
				'condition' => array( 'badge_type' => 'icon' ),
			)
		);

		$this->add_responsive_control(
			'badge_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => 10,
					'right'    => 10,
					'bottom'   => 10,
					'left'     => 10,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zfn__badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'badge_shadow',
				'selector' => '{{WRAPPER}} .webmz-zfn__badge',
			)
		);

		$this->add_responsive_control(
			'badge_icon_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 8, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 18,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zfn__badge svg, {{WRAPPER}} .webmz-zfn__badge i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'badge_type' => 'icon' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'input_bar_style',
			array(
				'label' => esc_html__( 'نوار ایمیل', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'input_bar_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_primary_light', '--webmz-color-primary-light', '#ffffff' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zfn__input-bar' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'input_bar_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'      => 8,
					'right'    => 8,
					'bottom'   => 8,
					'left'     => 8,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zfn__input-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'input_bar_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => 12,
					'right'    => 12,
					'bottom'   => 12,
					'left'     => 12,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zfn__input-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'input_bar_shadow',
				'selector' => '{{WRAPPER}} .webmz-zfn__input-bar',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'input_style',
			array(
				'label' => esc_html__( 'فیلد ایمیل', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'input_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_text_dark', '--webmz-color-text-dark', '#111827' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zfn__input' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'input_placeholder_color',
			array(
				'label'     => esc_html__( 'رنگ placeholder', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_text_gray', '--webmz-color-text-gray', '#64748b' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zfn__input::placeholder' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'input_typography',
				'selector' => '{{WRAPPER}} .webmz-zfn__input',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'email_icon_style',
			array(
				'label'     => esc_html__( 'آیکون ایمیل', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_email_icon' => 'yes' ),
			)
		);

		$this->add_control(
			'email_icon_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_text_gray', '--webmz-color-text-gray', '#64748b' ),
				'selectors' => $this->webmz_get_icon_color_value_selectors( '{{WRAPPER}} .webmz-zfn__input-icon' ),
			)
		);

		$this->add_responsive_control(
			'email_icon_size',
			array(
				'label'      => esc_html__( 'اندازه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 10, 'max' => 32 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 18,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zfn__input-icon svg, {{WRAPPER}} .webmz-zfn__input-icon i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'submit_style',
			array(
				'label' => esc_html__( 'دکمه ارسال', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'submit_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_primary', '--webmz-color-primary', '#0878f9' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zfn__submit' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_background_hover',
			array(
				'label'     => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_primary_hover', '--webmz-color-primary-hover', '#0662cc' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zfn__submit:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_primary_light', '--webmz-color-primary-light', '#ffffff' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zfn__submit' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'submit_display' => array( 'text', 'icon_text' ),
				),
			)
		);

		$this->add_control(
			'submit_icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_primary_light', '--webmz-color-primary-light', '#ffffff' ),
				'selectors' => $this->webmz_get_icon_color_value_selectors( '{{WRAPPER}} .webmz-zfn__submit' ),
				'condition' => array(
					'submit_display' => array( 'icon', 'icon_text' ),
				),
			)
		);

		$this->add_responsive_control(
			'submit_size',
			array(
				'label'      => esc_html__( 'اندازه دکمه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 32, 'max' => 72 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 44,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zfn__submit' => 'min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'submit_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => 8,
					'right'    => 8,
					'bottom'   => 8,
					'left'     => 8,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zfn__submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'submit_icon_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 10, 'max' => 32 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zfn__submit svg, {{WRAPPER}} .webmz-zfn__submit i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'submit_display' => array( 'icon', 'icon_text' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'submit_typography',
				'selector'  => '{{WRAPPER}} .webmz-zfn__submit-text',
				'condition' => array(
					'submit_display' => array( 'text', 'icon_text' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render badge image or icon.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_badge( $settings ) {
		$badge_type = ! empty( $settings['badge_type'] ) ? $settings['badge_type'] : 'icon';

		if ( 'image' === $badge_type ) {
			if ( ! empty( $settings['badge_image']['id'] ) ) {
				echo wp_get_attachment_image( absint( $settings['badge_image']['id'] ), 'thumbnail', false, array( 'class' => 'webmz-zfn__badge-img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				return;
			}

			if ( ! empty( $settings['badge_image']['url'] ) ) {
				?>
				<img class="webmz-zfn__badge-img" src="<?php echo esc_url( $settings['badge_image']['url'] ); ?>" alt="">
				<?php
			}
			return;
		}

		if ( ! empty( $settings['badge_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['badge_icon'], array( 'aria-hidden' => 'true' ) );
		}
	}

	protected function render() {
		$settings       = $this->get_settings_for_display();
		$title          = isset( $settings['title'] ) ? trim( (string) $settings['title'] ) : '';
		$show_badge     = 'yes' === ( $settings['show_badge'] ?? '' );
		$show_email_icn = 'yes' === ( $settings['show_email_icon'] ?? '' );
		$submit_display = ! empty( $settings['submit_display'] ) ? $settings['submit_display'] : 'icon';
		$stack_mobile   = ! empty( $settings['mobile_stack'] ) && 'yes' === $settings['mobile_stack'];
		$title_tag      = $this->webmz_get_title_tag( $settings );
		$form_id        = 'webmz-zfn-' . $this->get_id();
		$nonce          = wp_create_nonce( 'webmz_newsletter_subscribe' );
		$success        = ! empty( $settings['success_message'] ) ? $settings['success_message'] : __( 'عضویت شما با موفقیت ثبت شد.', 'tadris' );
		$classes        = 'webmz-zfn webmz-newsletter' . ( $stack_mobile ? ' webmz-zfn--stack-mobile' : '' );
		?>
		<div class="<?php echo esc_attr( $classes ); ?>" data-success-message="<?php echo esc_attr( $success ); ?>">
			<?php if ( $show_badge || '' !== $title ) : ?>
				<div class="webmz-zfn__header">
					<div class="webmz-zfn__lead">
						<?php if ( $show_badge ) : ?>
							<span class="webmz-zfn__badge <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'badge_icon_color_mode' ) ); ?>" aria-hidden="true">
								<?php $this->render_badge( $settings ); ?>
							</span>
						<?php endif; ?>

						<?php if ( '' !== $title ) : ?>
							<<?php echo esc_html( $title_tag ); ?> class="webmz-zfn__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $title_tag ); ?>>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<form class="webmz-newsletter__form webmz-zfn__form" id="<?php echo esc_attr( $form_id ); ?>" novalidate>
				<input type="hidden" name="action" value="webmz_newsletter_subscribe">
				<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">
				<input type="text" class="webmz-newsletter__honeypot" name="webmz_newsletter_website" value="" tabindex="-1" autocomplete="off" aria-hidden="true">

				<div class="webmz-zfn__input-bar">
					<?php if ( $show_email_icn && ! empty( $settings['email_icon']['value'] ) ) : ?>
						<span class="webmz-zfn__input-icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'email_icon_color_mode' ) ); ?>" aria-hidden="true">
							<?php Icons_Manager::render_icon( $settings['email_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>

					<label class="screen-reader-text" for="<?php echo esc_attr( $form_id ); ?>-email"><?php esc_html_e( 'ایمیل', 'tadris' ); ?></label>
					<input
						type="email"
						id="<?php echo esc_attr( $form_id ); ?>-email"
						class="webmz-newsletter__input webmz-zfn__input"
						name="email"
						placeholder="<?php echo esc_attr( $settings['email_placeholder'] ); ?>"
						required
						autocomplete="email"
					>

					<button type="submit" class="webmz-newsletter__submit webmz-zfn__submit <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'submit_icon_color_mode' ) ); ?>">
						<?php if ( in_array( $submit_display, array( 'icon', 'icon_text' ), true ) && ! empty( $settings['submit_icon']['value'] ) ) : ?>
							<?php Icons_Manager::render_icon( $settings['submit_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						<?php endif; ?>

						<?php if ( in_array( $submit_display, array( 'text', 'icon_text' ), true ) && ! empty( $settings['button_text'] ) ) : ?>
							<span class="webmz-zfn__submit-text webmz-newsletter__submit-text"><?php echo esc_html( $settings['button_text'] ); ?></span>
						<?php endif; ?>

						<span class="webmz-newsletter__submit-loading" aria-hidden="true"><?php esc_html_e( '...', 'tadris' ); ?></span>
					</button>
				</div>

				<p class="webmz-newsletter__message webmz-zfn__message" role="status" aria-live="polite" hidden></p>
			</form>
		</div>
		<?php
	}
}
