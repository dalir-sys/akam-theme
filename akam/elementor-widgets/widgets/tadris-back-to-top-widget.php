<?php
/**
 * Back to top Elementor widget for WebMZ/Tadris.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

 defined( 'ABSPATH' ) || exit;

/** Back to top button widget. */
class Tadris_Back_To_Top_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-tadris-back-to-top';
	}

	public function get_title() {
		return esc_html__( 'برو به بالا', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-arrow-up';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'back to top', 'scroll top', 'top', 'بالا', 'اسکرول' );
	}

	public function get_script_depends() {
		return array( 'webmz-tadris-widgets' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'label',
			array(
				'label'       => esc_html__( 'متن دسترس‌پذیری', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'رفتن به بالای صفحه', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'آیکون', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrow-up',
					'library' => 'fa-solid',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون', 'tadris' ) );

		$this->add_control(
			'fixed_position',
			array(
				'label'        => esc_html__( 'شناور روی صفحه', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'fixed_side',
			array(
				'label'     => esc_html__( 'سمت قرارگیری', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => array(
					'right' => esc_html__( 'راست', 'tadris' ),
					'left'  => esc_html__( 'چپ', 'tadris' ),
				),
				'condition' => array(
					'fixed_position' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'fixed_offset_x',
			array(
				'label'      => esc_html__( 'فاصله افقی', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-back-to-top--fixed' => '--webmz-backtop-x: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'fixed_position' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'fixed_offset_y',
			array(
				'label'      => esc_html__( 'فاصله از پایین', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 180 ) ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-back-to-top--fixed' => '--webmz-backtop-y: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'fixed_position' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_after_scroll',
			array(
				'label'        => esc_html__( 'نمایش بعد از اسکرول', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_after_px',
			array(
				'label'     => esc_html__( 'مقدار اسکرول برای نمایش', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 300,
				'min'       => 0,
				'max'       => 3000,
				'step'      => 10,
				'condition' => array(
					'show_after_scroll' => 'yes',
				),
			)
		);

		$this->add_control(
			'scroll_duration',
			array(
				'label'   => esc_html__( 'مدت انیمیشن اسکرول', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 650,
				'min'     => 0,
				'max'     => 3000,
				'step'    => 50,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			array(
				'label' => esc_html__( 'استایل', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label'     => esc_html__( 'چیدمان', 'tadris' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array( 'title' => esc_html__( 'راست', 'tadris' ), 'icon' => 'eicon-h-align-right' ),
					'center'     => array( 'title' => esc_html__( 'وسط', 'tadris' ), 'icon' => 'eicon-h-align-center' ),
					'flex-end'   => array( 'title' => esc_html__( 'چپ', 'tadris' ), 'icon' => 'eicon-h-align-left' ),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .webmz-back-to-top-wrap' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'outer_size',
			array(
				'label'      => esc_html__( 'اندازه هاله بیرونی', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 44, 'max' => 160 ) ),
				'default'    => array( 'size' => 92, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-back-to-top' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'inner_size',
			array(
				'label'      => esc_html__( 'اندازه دکمه داخلی', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 32, 'max' => 130 ) ),
				'default'    => array( 'size' => 70, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-back-to-top__button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'     => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 10, 'max' => 64 ) ),
				'default'   => array( 'size' => 26, 'unit' => 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-back-to-top__button i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-back-to-top__button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'outer_color',
			array(
				'label'     => esc_html__( 'رنگ هاله بیرونی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary-light)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-back-to-top' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'رنگ دکمه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-back-to-top__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-back-to-top__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_hover_color',
			array(
				'label'     => esc_html__( 'رنگ دکمه در هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary-hover)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-back-to-top:hover .webmz-back-to-top__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'shadow',
				'selector' => '{{WRAPPER}} .webmz-back-to-top__button',
			)
		);

		$this->add_responsive_control(
			'radius',
			array(
				'label'      => esc_html__( 'گردی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 999,
					'right'    => 999,
					'bottom'   => 999,
					'left'     => 999,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-back-to-top, {{WRAPPER}} .webmz-back-to-top__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings     = $this->get_settings_for_display();
		$label        = ! empty( $settings['label'] ) ? $settings['label'] : esc_html__( 'رفتن به بالای صفحه', 'tadris' );
		$fixed        = ! empty( $settings['fixed_position'] ) && 'yes' === $settings['fixed_position'];
		$fixed_side   = ! empty( $settings['fixed_side'] ) && 'right' === $settings['fixed_side'] ? 'right' : 'left';
		$show_after   = ! empty( $settings['show_after_scroll'] ) && 'yes' === $settings['show_after_scroll'];
		$threshold    = ! empty( $settings['show_after_px'] ) ? absint( $settings['show_after_px'] ) : 300;
		$duration     = isset( $settings['scroll_duration'] ) ? absint( $settings['scroll_duration'] ) : 650;
		$wrapper_class = 'webmz-back-to-top-wrap';
		$button_class  = 'webmz-back-to-top';

		if ( $fixed ) {
			$button_class .= ' webmz-back-to-top--fixed webmz-back-to-top--' . $fixed_side;
		}

		if ( $show_after ) {
			$button_class .= ' webmz-back-to-top--reveal';
		}
		?>
		<div class="<?php echo esc_attr( $wrapper_class ); ?>">
			<div class="<?php echo esc_attr( $button_class ); ?>" data-webmz-back-to-top data-threshold="<?php echo esc_attr( $threshold ); ?>" data-duration="<?php echo esc_attr( $duration ); ?>">
				<button type="button" class="webmz-back-to-top__button <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings ) ); ?>" aria-label="<?php echo esc_attr( $label ); ?>">
					<?php Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</button>
			</div>
		</div>
		<?php
	}
}
