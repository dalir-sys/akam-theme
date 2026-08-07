<?php
/**
 * Shared trigger-style controls for header commerce widgets.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;

defined( 'ABSPATH' ) || exit;

/**
 * Trigger style presets for account and mini-cart header widgets.
 */
trait Header_Commerce_Widget_Trait {

	/**
	 * Available trigger style options.
	 *
	 * @return array<string,string>
	 */
	protected function get_header_commerce_trigger_style_options() {
		return array(
			'default' => esc_html__( 'پیش‌فرض', 'tadris' ),
			'style-1' => esc_html__( 'استایل ۱ — دایره‌ای خطی', 'tadris' ),
			'style-2' => esc_html__( 'استایل ۲ — دایره‌ای پررنگ', 'tadris' ),
			'style-3' => esc_html__( 'استایل ۳ — مربعی خطی', 'tadris' ),
		);
	}

	/**
	 * Register trigger style selector and related content controls.
	 *
	 * @param array<string,mixed> $args Optional args: show_label_control (bool).
	 * @return void
	 */
	protected function register_header_commerce_trigger_style_controls( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'show_label_control' => false,
			)
		);

		$this->add_control(
			'trigger_style',
			array(
				'label'   => esc_html__( 'استایل دکمه', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => $this->get_header_commerce_trigger_style_options(),
			)
		);

		if ( $args['show_label_control'] ) {
			$this->add_control(
				'show_label',
				array(
					'label'        => esc_html__( 'نمایش متن', 'tadris' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'بله', 'tadris' ),
					'label_off'    => esc_html__( 'خیر', 'tadris' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array(
						'trigger_style' => 'default',
					),
				)
			);
		}

		$this->add_responsive_control(
			'icon_only_size',
			array(
				'label'      => esc_html__( 'اندازه دکمه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 32, 'max' => 72 ),
				),
				'default'    => array(
					'size' => 44,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-hc-trigger--style-1 .webmz-account__trigger, {{WRAPPER}} .webmz-hc-trigger--style-1 .webmz-hcart__trigger' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-hc-trigger--style-2 .webmz-account__trigger, {{WRAPPER}} .webmz-hc-trigger--style-2 .webmz-hcart__trigger' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-hc-trigger--style-3 .webmz-account__trigger, {{WRAPPER}} .webmz-hc-trigger--style-3 .webmz-hcart__trigger' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'trigger_style!' => 'default',
				),
			)
		);
	}

	/**
	 * Register style-tab controls for icon-only trigger presets.
	 *
	 * @return void
	 */
	protected function register_header_commerce_icon_trigger_style_controls() {
		$trigger_selector = '{{WRAPPER}} .webmz-hc-trigger--style-1 .webmz-account__trigger, {{WRAPPER}} .webmz-hc-trigger--style-1 .webmz-hcart__trigger, {{WRAPPER}} .webmz-hc-trigger--style-2 .webmz-account__trigger, {{WRAPPER}} .webmz-hc-trigger--style-2 .webmz-hcart__trigger, {{WRAPPER}} .webmz-hc-trigger--style-3 .webmz-account__trigger, {{WRAPPER}} .webmz-hc-trigger--style-3 .webmz-hcart__trigger';
		$trigger_hover_selector = '{{WRAPPER}} .webmz-hc-trigger--style-1 .webmz-account__trigger:hover, {{WRAPPER}} .webmz-hc-trigger--style-1 .webmz-account.is-open .webmz-account__trigger, {{WRAPPER}} .webmz-hc-trigger--style-1 .webmz-hcart__trigger:hover, {{WRAPPER}} .webmz-hc-trigger--style-1 .webmz-hcart.is-open .webmz-hcart__trigger, {{WRAPPER}} .webmz-hc-trigger--style-2 .webmz-account__trigger:hover, {{WRAPPER}} .webmz-hc-trigger--style-2 .webmz-account.is-open .webmz-account__trigger, {{WRAPPER}} .webmz-hc-trigger--style-2 .webmz-hcart__trigger:hover, {{WRAPPER}} .webmz-hc-trigger--style-2 .webmz-hcart.is-open .webmz-hcart__trigger, {{WRAPPER}} .webmz-hc-trigger--style-3 .webmz-account__trigger:hover, {{WRAPPER}} .webmz-hc-trigger--style-3 .webmz-account.is-open .webmz-account__trigger, {{WRAPPER}} .webmz-hc-trigger--style-3 .webmz-hcart__trigger:hover, {{WRAPPER}} .webmz-hc-trigger--style-3 .webmz-hcart.is-open .webmz-hcart__trigger';

		$this->add_control(
			'icon_only_style_heading',
			array(
				'label'     => esc_html__( 'استایل دکمه آیکون', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'trigger_style!' => 'default',
				),
			)
		);

		$this->add_control(
			'icon_only_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه دکمه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$trigger_selector => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'trigger_style!' => 'default',
				),
			)
		);

		$this->add_control(
			'icon_only_background_hover',
			array(
				'label'     => esc_html__( 'پس‌زمینه دکمه در هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$trigger_hover_selector => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'trigger_style!' => 'default',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'icon_only_border',
				'selector'       => $trigger_selector,
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
						'default' => '#e2e8f0',
					),
				),
				'condition'      => array(
					'trigger_style' => array( 'style-1', 'style-3' ),
				),
			)
		);
	}

	/**
	 * Get sanitized trigger style slug from settings.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return string
	 */
	protected function get_header_commerce_trigger_style( $settings ) {
		$style = isset( $settings['trigger_style'] ) ? sanitize_key( (string) $settings['trigger_style'] ) : 'default';
		$valid = array_keys( $this->get_header_commerce_trigger_style_options() );

		if ( ! in_array( $style, $valid, true ) ) {
			$style = 'default';
		}

		return $style;
	}

	/**
	 * Get CSS class for the selected trigger style.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return string
	 */
	protected function get_header_commerce_trigger_style_class( $settings ) {
		$style = $this->get_header_commerce_trigger_style( $settings );

		if ( 'default' === $style ) {
			return '';
		}

		return 'webmz-hc-trigger--' . $style;
	}

	/**
	 * Whether the widget should render only the icon trigger.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return bool
	 */
	protected function is_header_commerce_icon_only( $settings ) {
		if ( 'default' !== $this->get_header_commerce_trigger_style( $settings ) ) {
			return true;
		}

		return isset( $settings['show_label'] ) && 'yes' !== $settings['show_label'];
	}

	/**
	 * Build root widget class list.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @param string              $base     Base widget class.
	 * @return string
	 */
	protected function get_header_commerce_root_classes( $settings, $base ) {
		$classes = array( trim( $base ) );
		$style   = $this->get_header_commerce_trigger_style_class( $settings );

		if ( $style ) {
			$classes[] = $style;
		}

		if ( $this->is_header_commerce_icon_only( $settings ) ) {
			$classes[] = 'is-icon-only';
		}

		return implode( ' ', array_filter( $classes ) );
	}
}
