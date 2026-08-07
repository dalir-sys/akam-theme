<?php
/**
 * Advanced CTA button widget for Tadris layouts.
 *
 * @package WebMZ
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

/**
 * Advanced button with icon, layout, and full style controls.
 */
class Tadris_Advanced_Button_Widget extends Widget_Base {

	public function get_name() {
		return 'tadris_advanced_button';
	}

	public function get_title() {
		return esc_html__( 'دکمه پیشرفته آکام', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'button', 'cta', 'دکمه', 'آکام', 'آیکون', 'پیشرفته' );
	}

	public function get_style_depends() {
		return array( 'webmz-tadris-advanced-button' );
	}

	protected function register_controls() {
		$link_selector  = '.tadris-advanced-button__link';
		$icon_selector  = '.tadris-advanced-button__icon';
		$text_selector  = '.tadris-advanced-button__text';
		$wrap_selector  = '.tadris-advanced-button';

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'متن دکمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'مشاهده بیشتر', 'tadris' ),
				'placeholder' => esc_html__( 'متن دکمه', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'         => esc_html__( 'لینک', 'tadris' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://example.com',
				'default'       => array( 'url' => '#' ),
				'show_external' => true,
			)
		);

		$this->add_control(
			'selected_icon',
			array(
				'label'   => esc_html__( 'آیکون', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrow-left',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'selected_icon_color_mode',
			array(
				'label'       => esc_html__( 'نوع رنگ‌دهی آیکون SVG', 'tadris' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'auto',
				'options'     => array(
					'auto'   => esc_html__( 'خودکار', 'tadris' ),
					'stroke' => esc_html__( 'Stroke / خطی', 'tadris' ),
					'fill'   => esc_html__( 'Fill / توپر', 'tadris' ),
					'both'   => esc_html__( 'هر دو', 'tadris' ),
				),
				'description' => esc_html__( 'برای SVG خطی Stroke و برای SVG توپر Fill را انتخاب کنید.', 'tadris' ),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'   => esc_html__( 'موقعیت آیکون', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'right' => esc_html__( 'سمت راست', 'tadris' ),
					'left'  => esc_html__( 'سمت چپ', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'button_width_type',
			array(
				'label'   => esc_html__( 'نوع عرض', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => array(
					'inline' => esc_html__( 'اندازه محتوا', 'tadris' ),
					'full'   => esc_html__( 'تمام عرض', 'tadris' ),
				),
			)
		);

		$this->add_responsive_control(
			'button_alignment',
			array(
				'label'     => esc_html__( 'چیدمان', 'tadris' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'راست', 'tadris' ),
						'icon'  => 'eicon-h-align-right',
					),
					'center'     => array(
						'title' => esc_html__( 'وسط', 'tadris' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'چپ', 'tadris' ),
						'icon'  => 'eicon-h-align-left',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} ' . $wrap_selector => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => esc_html__( 'استایل دکمه', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} ' . $link_selector,
			)
		);

		$this->add_responsive_control(
			'button_min_height',
			array(
				'label'      => esc_html__( 'حداقل ارتفاع', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 120 ) ),
				'default'    => array( 'size' => 48, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $link_selector => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'default'    => array(
					'top'      => '10',
					'right'    => '20',
					'bottom'   => '10',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $link_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_gap',
			array(
				'label'     => esc_html__( 'فاصله متن و آیکون', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'   => array( 'size' => 8, 'unit' => 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $link_selector => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => '10',
					'right'    => '10',
					'bottom'   => '10',
					'left'     => '10',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $link_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} ' . $link_selector,
			)
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		$this->start_controls_tab(
			'button_normal_tab',
			array( 'label' => esc_html__( 'عادی', 'tadris' ) )
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} ' . $text_selector => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} ' . $link_selector,
				'fields_options' => array(
					'background' => array( 'default' => 'classic' ),
					'color'      => array( 'default' => '#e11d48' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} ' . $link_selector,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_hover_tab',
			array( 'label' => esc_html__( 'هاور', 'tadris' ) )
		);

		$this->add_control(
			'button_hover_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $link_selector . ':hover ' . $text_selector => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_hover_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} ' . $link_selector . ':hover',
			)
		);

		$this->add_control(
			'button_hover_border_color',
			array(
				'label'     => esc_html__( 'رنگ بوردر هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $link_selector . ':hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}} ' . $link_selector . ':hover',
			)
		);

		$this->add_responsive_control(
			'button_hover_translate_y',
			array(
				'label'     => esc_html__( 'حرکت هاور', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => -20, 'max' => 20 ) ),
				'default'   => array( 'size' => -2, 'unit' => 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $link_selector . ':hover' => 'transform: translateY({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'button_transition',
			array(
				'label'      => esc_html__( 'مدت انیمیشن', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's', 'ms' ),
				'range'      => array(
					's'  => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ),
					'ms' => array( 'min' => 0, 'max' => 3000, 'step' => 50 ),
				),
				'default'    => array( 'size' => 0.2, 'unit' => 's' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $link_selector => 'transition: all {{SIZE}}{{UNIT}} ease;',
				),
				'separator'  => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			array(
				'label' => esc_html__( 'استایل آیکون', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'icon_box_size',
			array(
				'label'      => esc_html__( 'اندازه باکس آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 16, 'max' => 80 ) ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $icon_selector => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'اندازه خود آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 50 ) ),
				'default'    => array( 'size' => 16, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $icon_selector . ' i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $icon_selector . ' svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_radius',
			array(
				'label'      => esc_html__( 'گردی باکس آیکون', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $icon_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'icon_style_tabs' );

		$this->start_controls_tab(
			'icon_normal_tab',
			array( 'label' => esc_html__( 'عادی', 'tadris' ) )
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $icon_selector => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_background_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $icon_selector => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_hover_tab',
			array( 'label' => esc_html__( 'هاور', 'tadris' ) )
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $link_selector . ':hover ' . $icon_selector => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_background_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه آیکون هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $link_selector . ':hover ' . $icon_selector => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_hover_translate_x',
			array(
				'label'     => esc_html__( 'حرکت آیکون در هاور', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => -20, 'max' => 20 ) ),
				'default'   => array( 'size' => -3, 'unit' => 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $link_selector . ':hover ' . $icon_selector => 'transform: translateX({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @param array<string,mixed> $settings Widget settings.
	 * @return string
	 */
	protected function get_icon_color_mode_class( $settings ) {
		if ( function_exists( '\WebMZ\Elementor\webmz_get_icon_color_mode_class' ) ) {
			return \WebMZ\Elementor\webmz_get_icon_color_mode_class( $settings, 'selected_icon_color_mode' );
		}

		$mode = ! empty( $settings['selected_icon_color_mode'] ) ? sanitize_key( $settings['selected_icon_color_mode'] ) : 'auto';
		if ( ! in_array( $mode, array( 'auto', 'stroke', 'fill', 'both' ), true ) ) {
			$mode = 'auto';
		}

		return 'webmz-icon-color-mode-' . $mode;
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$button_text   = ! empty( $settings['button_text'] ) ? $settings['button_text'] : esc_html__( 'مشاهده بیشتر', 'tadris' );
		$has_icon      = ! empty( $settings['selected_icon']['value'] );
		$icon_position = ! empty( $settings['icon_position'] ) ? $settings['icon_position'] : 'right';
		$icon_class    = $has_icon ? $this->get_icon_color_mode_class( $settings ) : '';

		$this->add_render_attribute( 'button', 'class', 'tadris-advanced-button__link' );

		if ( ! empty( $settings['button_link']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['button_link'] );
		} else {
			$this->add_render_attribute( 'button', 'href', '#' );
		}

		if ( ! empty( $settings['button_width_type'] ) && 'full' === $settings['button_width_type'] ) {
			$this->add_render_attribute( 'button', 'class', 'tadris-advanced-button__link--full' );
		}
		?>
		<div class="tadris-advanced-button">
			<a <?php echo $this->get_render_attribute_string( 'button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php if ( $has_icon && 'right' === $icon_position ) : ?>
					<span class="tadris-advanced-button__icon <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true">
						<?php Icons_Manager::render_icon( $settings['selected_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php endif; ?>

				<span class="tadris-advanced-button__text"><?php echo esc_html( $button_text ); ?></span>

				<?php if ( $has_icon && 'left' === $icon_position ) : ?>
					<span class="tadris-advanced-button__icon <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true">
						<?php Icons_Manager::render_icon( $settings['selected_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php endif; ?>
			</a>
		</div>
		<?php
	}
}
