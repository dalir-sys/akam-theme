<?php
/**
 * Shared Elementor controls for Tadris/WebMZ HTML widgets.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;


/**
 * Return CSS class used for SVG icon color-mode handling.
 *
 * Mode meanings:
 * - auto: only color is inherited; original SVG fill/stroke attributes remain untouched.
 * - stroke: force stroke to currentColor and remove non-none fills.
 * - fill: force fill to currentColor and remove non-none strokes.
 * - both: force both stroke and fill to currentColor.
 *
 * @param array<string,mixed> $settings Elementor settings.
 * @param string              $key      Setting key.
 * @return string
 */
function webmz_get_icon_color_mode_class( $settings, $key = 'icon_color_mode' ) {
	$mode = isset( $settings[ $key ] ) ? sanitize_key( (string) $settings[ $key ] ) : 'auto';

	if ( ! in_array( $mode, array( 'auto', 'stroke', 'fill', 'both' ), true ) ) {
		$mode = 'auto';
	}

	return 'webmz-icon-color-mode-' . $mode;
}


/**
 * Sanitize a user selected HTML heading tag.
 *
 * @deprecated 2.1.18 Use global webmz_sanitize_heading_tag() from helpers.php.
 * @param string $tag Heading tag.
 * @return string
 */
function webmz_sanitize_heading_tag( $tag ) {
	return \webmz_sanitize_heading_tag( $tag );
}

trait Tadris_Widget_Controls_Trait {

	/**
	 * Register a user-facing SVG color-mode control for any Elementor icon.
	 *
	 * @param string              $control_id Control ID.
	 * @param string              $label      Control label.
	 * @param array<string,mixed> $condition  Optional Elementor condition.
	 * @return void
	 */
	protected function webmz_register_icon_color_mode_control( $control_id = 'icon_color_mode', $label = '', $condition = array() ) {
		$args = array(
			'label'       => $label ? $label : esc_html__( 'نوع رنگ‌دهی آیکون', 'tadris' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'auto',
			'options'     => array(
				'auto'   => esc_html__( 'خودکار / بدون اجبار', 'tadris' ),
				'stroke' => esc_html__( 'Stroke / خطی', 'tadris' ),
				'fill'   => esc_html__( 'Fill / توپر', 'tadris' ),
				'both'   => esc_html__( 'هر دو', 'tadris' ),
			),
			'description' => esc_html__( 'برای SVGهای خطی گزینه Stroke و برای SVGهای توپر گزینه Fill را انتخاب کنید تا ظاهر آیکون خراب نشود.', 'tadris' ),
		);

		if ( ! empty( $condition ) ) {
			$args['condition'] = $condition;
		}

		$this->add_control( $control_id, $args );
	}

	/**
	 * Get icon color-mode class from widget settings.
	 *
	 * @param array<string,mixed> $settings Elementor settings.
	 * @param string              $key      Setting key.
	 * @return string
	 */
	protected function webmz_get_icon_color_mode_class( $settings, $key = 'icon_color_mode' ) {
		return webmz_get_icon_color_mode_class( $settings, $key );
	}



	/**
	 * Register a SEO-friendly heading tag selector for content card titles.
	 *
	 * @param string              $control_id Control ID.
	 * @param string              $label      Control label.
	 * @param array<string,mixed> $condition  Optional Elementor condition.
	 * @return void
	 */
	protected function webmz_register_title_tag_control( $control_id = 'title_tag', $label = '', $condition = array() ) {
		$args = array(
			'label'       => $label ? $label : esc_html__( 'تگ HTML عنوان', 'tadris' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'h3',
			'options'     => array(
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'h5' => 'H5',
				'h6' => 'H6',
			),
			'description' => esc_html__( 'برای ساختار سئویی صفحه، تگ مناسب عنوان کارت را انتخاب کنید. مقدار پیش‌فرض H3 است.', 'tadris' ),
		);

		if ( ! empty( $condition ) ) {
			$args['condition'] = $condition;
		}

		$this->add_control( $control_id, $args );
	}

	/**
	 * Get sanitized heading tag from widget settings.
	 *
	 * @param array<string,mixed> $settings Elementor settings.
	 * @param string              $key      Setting key.
	 * @return string
	 */
	protected function webmz_get_title_tag( $settings, $key = 'title_tag' ) {
		$tag = isset( $settings[ $key ] ) ? $settings[ $key ] : 'h3';

		return webmz_sanitize_heading_tag( $tag );
	}

	/**
	 * Register common box controls.
	 *
	 * @param string               $id       Control section ID.
	 * @param string               $label    Section label.
	 * @param string               $selector CSS selector.
	 * @param array<string, mixed> $config   Optional control defaults.
	 * @return void
	 */
	protected function webmz_register_box_style_controls( $id, $label, $selector, $config = array() ) {
		$background_args = array(
			'name'     => $id . '_background',
			'selector' => '{{WRAPPER}} ' . $selector,
		);
		$shadow_args     = array(
			'name'     => $id . '_shadow',
			'selector' => '{{WRAPPER}} ' . $selector,
		);

		$border_args     = array(
			'name'     => $id . '_border',
			'selector' => '{{WRAPPER}} ' . $selector,
		);

		if ( ! empty( $config['flat'] ) ) {
			$background_args['fields_options'] = array(
				'background' => array(
					'default' => 'classic',
				),
				'color'      => array(
					'default' => 'transparent',
				),
			);
			$shadow_args['fields_options']     = array(
				'box_shadow_type' => array(
					'default' => '',
				),
			);
		}

		if ( ! empty( $config['bordered'] ) ) {
			$shadow_args['fields_options'] = array(
				'box_shadow_type' => array(
					'default' => '',
				),
			);
			$border_args['fields_options'] = array(
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
					'default' => 'rgba(15, 23, 42, 0.08)',
				),
			);
		}

		if ( ! empty( $config['default_background'] ) ) {
			$background_args['fields_options'] = array(
				'background' => array(
					'default' => 'classic',
				),
				'color'      => array(
					'default' => $config['default_background'],
				),
			);
		}

		if ( ! empty( $config['gradient'] ) ) {
			$background_args['types'] = array( 'classic', 'gradient' );

			$gradient_config = is_array( $config['gradient'] ) ? $config['gradient'] : array();
			$gradient_fields = array(
				'color_b'        => array(
					'default' => ! empty( $gradient_config['color_b'] ) ? $gradient_config['color_b'] : '#5b21b6',
				),
				'gradient_type'  => array(
					'default' => ! empty( $gradient_config['type'] ) ? $gradient_config['type'] : 'linear',
				),
				'gradient_angle' => array(
					'default' => array(
						'unit' => 'deg',
						'size' => ! empty( $gradient_config['angle'] ) ? (int) $gradient_config['angle'] : 135,
					),
				),
			);

			if ( ! empty( $gradient_config['color'] ) ) {
				$gradient_fields['color'] = array(
					'default' => $gradient_config['color'],
				);
			}

			$background_args['fields_options'] = array_merge(
				! empty( $background_args['fields_options'] ) ? $background_args['fields_options'] : array(),
				$gradient_fields
			);
		}
		$this->start_controls_section(
			$id,
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			$background_args
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			$border_args
		);

		$this->add_responsive_control(
			$id . '_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			$id . '_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			$id . '_margin',
			array(
				'label'      => esc_html__( 'فاصله بیرونی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			$shadow_args
		);

		$this->end_controls_section();
	}

	/**
	 * Register common text controls.
	 *
	 * @param string $id       Control section ID.
	 * @param string $label    Section label.
	 * @param string $selector CSS selector.
	 * @return void
	 */
	protected function webmz_register_text_style_controls( $id, $label, $selector ) {
		$this->start_controls_section(
			$id,
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			$id . '_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $selector => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$id . '_hover_color',
			array(
				'label'     => esc_html__( 'رنگ هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $selector . ':hover, {{WRAPPER}} ' . $selector . ':hover *' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $id . '_typography',
				'selector' => '{{WRAPPER}} ' . $selector,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Elementor color control selectors that also recolor SVG paths.
	 *
	 * @param string $selector Full CSS selector (may include {{WRAPPER}}).
	 * @return array<string, string>
	 */
	protected function webmz_get_icon_color_value_selectors( $selector ) {
		$selectors = array_map( 'trim', explode( ',', $selector ) );
		$rules     = array();

		foreach ( $selectors as $single_selector ) {
			$rules[ $single_selector ]                                        = 'color: {{VALUE}};';
			$rules[ $single_selector . ' svg, ' . $single_selector . ' i' ]    = 'color: {{VALUE}};';
			$rules[ $single_selector . ' svg [fill]:not([fill="none"])' ]      = 'fill: {{VALUE}} !important;';
			$rules[ $single_selector . ' svg [stroke]:not([stroke="none"])' ] = 'stroke: {{VALUE}} !important;';
		}

		return $rules;
	}

	/**
	 * Register common icon controls.
	 *
	 * @param string               $id       Section ID.
	 * @param string               $label    Section label.
	 * @param string               $selector CSS selector.
	 * @param array<string, mixed> $config   Optional config. Supports `hover` and `hover_selector`.
	 * @return void
	 */
	protected function webmz_register_icon_style_controls( $id, $label, $selector, $config = array() ) {
		$has_hover      = ! empty( $config['hover'] );
		$hover_selector = ! empty( $config['hover_selector'] )
			? $config['hover_selector']
			: '{{WRAPPER}} ' . $selector . ':hover';

		$this->start_controls_section(
			$id,
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		if ( $has_hover ) {
			$this->start_controls_tabs( $id . '_tabs' );

			$this->start_controls_tab(
				$id . '_normal_tab',
				array(
					'label' => esc_html__( 'عادی', 'tadris' ),
				)
			);
		}

		$this->add_control(
			$id . '_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $this->webmz_get_icon_color_value_selectors( '{{WRAPPER}} ' . $selector ),
			)
		);

		$this->add_responsive_control(
			$id . '_size',
			array(
				'label'      => esc_html__( 'اندازه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 160 ) ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $selector . ' svg, {{WRAPPER}} ' . $selector . ' i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => $id . '_background',
				'selector' => '{{WRAPPER}} ' . $selector,
			)
		);

		if ( $has_hover ) {
			$this->end_controls_tab();

			$this->start_controls_tab(
				$id . '_hover_tab',
				array(
					'label' => esc_html__( 'هاور', 'tadris' ),
				)
			);

			$this->add_control(
				$id . '_hover_color',
				array(
					'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => $this->webmz_get_icon_color_value_selectors( $hover_selector ),
				)
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				array(
					'name'     => $id . '_hover_background',
					'selector' => $hover_selector,
				)
			);

			$this->end_controls_tab();
			$this->end_controls_tabs();
		}

		$this->end_controls_section();
	}

	/**
	 * Register section heading icon box style controls.
	 *
	 * @param string               $selector CSS selector without wrapper prefix.
	 * @param array<string, mixed> $config   Optional control defaults.
	 * @return void
	 */
	protected function webmz_register_heading_icon_box_style_controls( $selector, $config = array() ) {
		$defaults = array(
			'section_id'     => 'heading_icon_box_style',
			'prefix'         => 'heading_icon_',
			'condition'      => array(),
			'bg_default'     => '',
			'icon_default'   => '',
			'use_icon_mode'  => true,
			'include_shadow' => false,
			'radius_default' => 999,
		);
		$config           = array_merge( $defaults, $config );
		$prefix           = $config['prefix'];
		$wrapper_selector = '{{WRAPPER}} ' . $selector;
		$icon_selector    = $wrapper_selector . ' svg, ' . $wrapper_selector . ' i';
		$section_args     = array(
			'label' => esc_html__( 'باکس آیکون هدینگ', 'tadris' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		);

		if ( ! empty( $config['condition'] ) ) {
			$section_args['condition'] = $config['condition'];
		}

		$this->start_controls_section( $config['section_id'], $section_args );

		$this->add_responsive_control(
			$prefix . 'box_size',
			array(
				'label'      => esc_html__( 'اندازه باکس آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 24, 'max' => 80 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 40,
				),
				'selectors'  => array(
					$wrapper_selector => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			$prefix . 'inner_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 8, 'max' => 48 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					$icon_selector => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$bg_args = array(
			'label'     => esc_html__( 'پس‌زمینه باکس', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				$wrapper_selector => 'background-color: {{VALUE}};',
			),
		);

		if ( '' !== $config['bg_default'] ) {
			$bg_args['default'] = $config['bg_default'];
		}

		$this->add_control( $prefix . 'bg_color', $bg_args );

		$icon_color_args = array(
			'label' => esc_html__( 'رنگ آیکون', 'tadris' ),
			'type'  => Controls_Manager::COLOR,
		);

		if ( $config['use_icon_mode'] ) {
			$icon_color_args['selectors'] = $this->webmz_get_icon_color_value_selectors( $wrapper_selector );
		} else {
			$icon_color_args['selectors'] = array(
				$wrapper_selector => 'color: {{VALUE}};',
			);
		}

		if ( '' !== $config['icon_default'] ) {
			$icon_color_args['default'] = $config['icon_default'];
		}

		$this->add_control( $prefix . 'color', $icon_color_args );

		if ( $config['include_shadow'] ) {
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'           => $prefix . 'shadow',
					'selector'       => $wrapper_selector,
					'fields_options' => array(
						'box_shadow_type' => array(
							'default' => 'yes',
						),
						'box_shadow'      => array(
							'default' => array(
								'horizontal' => 0,
								'vertical'   => 4,
								'blur'       => 12,
								'spread'     => 0,
								'color'      => 'rgba(0, 0, 0, 0.12)',
							),
						),
					),
				)
			);
		}

		$this->add_responsive_control(
			$prefix . 'radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 999 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => $config['radius_default'],
				),
				'selectors'  => array(
					$wrapper_selector => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Whether an Elementor switcher setting is enabled.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @param string              $key      Setting key.
	 * @param bool                $default  Default when unset.
	 * @return bool
	 */
	protected function webmz_is_yes( $settings, $key, $default = true ) {
		if ( ! isset( $settings[ $key ] ) ) {
			return $default;
		}

		return 'yes' === $settings[ $key ];
	}

	/**
	 * Register a card section visibility switcher.
	 *
	 * @param string $id      Control ID.
	 * @param string $label   Control label.
	 * @param string $default Default value (`yes` or empty).
	 * @return void
	 */
	protected function webmz_register_card_visibility_control( $id, $label, $default = 'yes' ) {
		$this->add_control(
			$id,
			array(
				'label'        => $label,
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => $default,
			)
		);
	}

	/**
	 * Register visibility controls for product loop card type 1.
	 *
	 * @return void
	 */
	protected function webmz_register_product_loop_card_1_visibility_controls() {
		$this->start_controls_section(
			'card_visibility',
			array(
				'label' => esc_html__( 'نمایش بخش‌های کارت', 'tadris' ),
			)
		);

		$this->webmz_register_card_visibility_control( 'show_image', esc_html__( 'تصویر محصول', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_rating', esc_html__( 'امتیاز', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_title', esc_html__( 'عنوان محصول', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_course_status', esc_html__( 'وضعیت دوره', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_sessions', esc_html__( 'تعداد جلسات', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_instructor', esc_html__( 'مدرس دوره', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_students', esc_html__( 'تعداد دانشجویان', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_price', esc_html__( 'قیمت', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_add_to_cart', esc_html__( 'دکمه ثبت‌نام', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_sale_badge', esc_html__( 'برچسب تخفیف روی تصویر', 'tadris' ), '' );

		$this->end_controls_section();
	}

	/**
	 * Register visibility controls for product loop card type 2.
	 *
	 * @return void
	 */
	protected function webmz_register_product_loop_card_2_visibility_controls() {
		$this->start_controls_section(
			'card_visibility',
			array(
				'label' => esc_html__( 'نمایش بخش‌های کارت', 'tadris' ),
			)
		);

		$this->webmz_register_card_visibility_control( 'show_image', esc_html__( 'تصویر محصول', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_category', esc_html__( 'دسته‌بندی', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_rating', esc_html__( 'امتیاز ستاره‌ای', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_title', esc_html__( 'عنوان محصول', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_excerpt', esc_html__( 'خلاصه محصول', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_duration', esc_html__( 'مدت دوره', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_sessions', esc_html__( 'تعداد جلسات', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_instructor', esc_html__( 'مدرس دوره', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_price', esc_html__( 'قیمت', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_add_to_cart', esc_html__( 'دکمه ثبت‌نام', 'tadris' ) );

		$this->end_controls_section();
	}

	/**
	 * Register visibility controls for product loop card type 3.
	 *
	 * @return void
	 */
	protected function webmz_register_product_loop_card_3_visibility_controls() {
		$this->start_controls_section(
			'card_visibility',
			array(
				'label' => esc_html__( 'نمایش بخش‌های کارت', 'tadris' ),
			)
		);

		$this->webmz_register_card_visibility_control( 'show_image', esc_html__( 'تصویر محصول', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_category_flag', esc_html__( 'پرچم دسته‌بندی', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_training_level', esc_html__( 'سطح دوره', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_sale_badge', esc_html__( 'برچسب تخفیف روی تصویر', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_title', esc_html__( 'عنوان محصول', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_excerpt', esc_html__( 'خلاصه محصول', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_instructor', esc_html__( 'مدرس دوره', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_rating', esc_html__( 'امتیاز', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_students', esc_html__( 'تعداد دانشجویان', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_reviews', esc_html__( 'تعداد دیدگاه‌ها', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_sessions_meta', esc_html__( 'تعداد درس‌ها', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_duration_meta', esc_html__( 'مدت دوره', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_price', esc_html__( 'قیمت', 'tadris' ) );
		$this->webmz_register_card_visibility_control( 'show_button', esc_html__( 'دکمه مشاهده دوره', 'tadris' ) );

		$this->end_controls_section();
	}

	/**
	 * Build render args for product loop card type 1.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @param array<string,mixed> $extras   Icon HTML and other runtime values.
	 * @return array<string,mixed>
	 */
	protected function webmz_build_product_loop_card_1_args( $settings, $extras = array() ) {
		return array_merge(
			array(
				'show_image'                => $this->webmz_is_yes( $settings, 'show_image' ),
				'show_rating'               => $this->webmz_is_yes( $settings, 'show_rating' ),
				'show_title'                => $this->webmz_is_yes( $settings, 'show_title' ),
				'show_course_meta'          => true,
				'show_course_status'        => $this->webmz_is_yes( $settings, 'show_course_status' ),
				'show_sessions'             => $this->webmz_is_yes( $settings, 'show_sessions' ),
				'show_instructor'           => $this->webmz_is_yes( $settings, 'show_instructor' ),
				'show_students'             => $this->webmz_is_yes( $settings, 'show_students' ),
				'show_price'                => $this->webmz_is_yes( $settings, 'show_price' ),
				'show_add_to_cart'          => $this->webmz_is_yes( $settings, 'show_add_to_cart' ),
				'show_sale_badge'           => $this->webmz_is_yes( $settings, 'show_sale_badge', false ),
				'title_tag'                 => $this->webmz_get_title_tag( $settings, 'title_tag' ),
				'rating_label'              => $settings['rating_label'] ?? '',
				'sessions_label'            => $settings['sessions_label'] ?? '',
				'sessions_suffix'           => $settings['sessions_suffix'] ?? '',
				'instructor_label'          => $settings['instructor_label'] ?? '',
				'students_label'            => $settings['students_label'] ?? '',
				'students_suffix'           => $settings['students_suffix'] ?? '',
				'status_finished_label'     => $settings['status_finished_label'] ?? '',
				'status_recording_label'    => $settings['status_recording_label'] ?? '',
				'free_price_text'           => $settings['free_price_text'] ?? '',
				'unavailable_price_text'    => $settings['unavailable_price_text'] ?? '',
				'button_text'               => $settings['button_text'] ?? '',
				'variable_button_text'      => $settings['variable_button_text'] ?? '',
				'unavailable_button_text'   => $settings['unavailable_button_text'] ?? '',
			),
			$extras
		);
	}

	/**
	 * Build render args for product loop card type 2.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @param array<string,mixed> $extras   Icon HTML and other runtime values.
	 * @return array<string,mixed>
	 */
	protected function webmz_build_product_loop_card_2_args( $settings, $extras = array() ) {
		return array_merge(
			array(
				'show_image'              => $this->webmz_is_yes( $settings, 'show_image' ),
				'show_category'           => $this->webmz_is_yes( $settings, 'show_category' ),
				'show_rating'             => $this->webmz_is_yes( $settings, 'show_rating' ),
				'show_title'              => $this->webmz_is_yes( $settings, 'show_title' ),
				'show_short_description'  => $this->webmz_is_yes( $settings, 'show_excerpt' ),
				'show_course_meta'        => true,
				'show_duration'           => $this->webmz_is_yes( $settings, 'show_duration' ),
				'show_sessions'           => $this->webmz_is_yes( $settings, 'show_sessions' ),
				'show_instructor'         => $this->webmz_is_yes( $settings, 'show_instructor' ),
				'show_price'              => $this->webmz_is_yes( $settings, 'show_price' ),
				'show_add_to_cart'        => $this->webmz_is_yes( $settings, 'show_add_to_cart' ),
				'title_tag'               => $this->webmz_get_title_tag( $settings, 'title_tag' ),
				'price_label'             => $settings['price_label'] ?? '',
				'duration_label'          => $settings['duration_label'] ?? '',
				'sessions_label'          => $settings['sessions_label'] ?? '',
				'sessions_suffix'         => $settings['sessions_suffix'] ?? '',
				'instructor_label'        => $settings['instructor_label'] ?? '',
				'free_price_text'         => $settings['free_price_text'] ?? '',
				'unavailable_price_text'  => $settings['unavailable_price_text'] ?? '',
				'excerpt_words'           => absint( $settings['excerpt_words'] ?? 14 ),
				'button_text'             => $settings['button_text'] ?? '',
				'variable_button_text'    => $settings['variable_button_text'] ?? '',
				'unavailable_button_text' => $settings['unavailable_button_text'] ?? '',
			),
			$extras
		);
	}

	/**
	 * Build render args for product loop card type 3.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @param array<string,mixed> $extras   Icon HTML and other runtime values.
	 * @return array<string,mixed>
	 */
	protected function webmz_build_product_loop_card_3_args( $settings, $extras = array() ) {
		return array_merge(
			array(
				'show_image'              => $this->webmz_is_yes( $settings, 'show_image' ),
				'show_category_flag'      => $this->webmz_is_yes( $settings, 'show_category_flag' ),
				'show_training_level'     => $this->webmz_is_yes( $settings, 'show_training_level' ),
				'show_sale_badge'         => $this->webmz_is_yes( $settings, 'show_sale_badge' ),
				'show_title'              => $this->webmz_is_yes( $settings, 'show_title' ),
				'show_short_description'  => $this->webmz_is_yes( $settings, 'show_excerpt' ),
				'show_instructor'         => $this->webmz_is_yes( $settings, 'show_instructor' ),
				'show_rating'             => $this->webmz_is_yes( $settings, 'show_rating' ),
				'show_student_count'      => $this->webmz_is_yes( $settings, 'show_students' ),
				'show_reviews'            => $this->webmz_is_yes( $settings, 'show_reviews' ),
				'show_course_meta'        => true,
				'show_sessions_meta'      => $this->webmz_is_yes( $settings, 'show_sessions_meta' ),
				'show_duration_meta'      => $this->webmz_is_yes( $settings, 'show_duration_meta' ),
				'show_price'              => $this->webmz_is_yes( $settings, 'show_price' ),
				'show_button'             => $this->webmz_is_yes( $settings, 'show_button' ),
				'title_tag'               => $this->webmz_get_title_tag( $settings, 'title_tag' ),
				'rating_label'            => $settings['rating_label'] ?? '',
				'instructor_label'        => $settings['instructor_label'] ?? '',
				'students_label'          => $settings['students_label'] ?? '',
				'students_suffix'         => $settings['students_suffix'] ?? '',
				'reviews_label'           => $settings['reviews_label'] ?? '',
				'sessions_label'          => $settings['sessions_label'] ?? '',
				'sessions_suffix'         => $settings['sessions_suffix'] ?? '',
				'duration_label'          => $settings['duration_label'] ?? '',
				'free_price_text'         => $settings['free_price_text'] ?? '',
				'unavailable_price_text'  => $settings['unavailable_price_text'] ?? '',
				'excerpt_words'           => absint( $settings['excerpt_words'] ?? 10 ),
				'button_text'             => $settings['button_text'] ?? '',
			),
			$extras
		);
	}
}
