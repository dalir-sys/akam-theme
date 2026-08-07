<?php
/**
 * Back to top v2 Elementor widget for WebMZ/Tadris.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/** Back to top tab-shaped button widget. */
class Back_To_Top_2_Widget extends Widget_Base {

	public function get_name() {
		return 'webmz-back-to-top-2';
	}

	public function get_title() {
		return esc_html__( 'رفتن به بالا ۲', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-arrow-up';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'back to top', 'scroll top', 'top', 'بالا', 'اسکرول', 'رفتن به بالا' );
	}

	public function get_script_depends() {
		return array( 'webmz-tadris-widgets' );
	}

	public function get_style_depends() {
		return array( 'webmz-back-to-top-2' );
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
			'fixed_position',
			array(
				'label'        => esc_html__( 'شناور روی صفحه', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_responsive_control(
			'fixed_offset_y',
			array(
				'label'      => esc_html__( 'فاصله از پایین', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 180 ) ),
				'default'    => array( 'size' => 0, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-back-to-top-2--fixed' => '--webmz-btt2-y: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .webmz-back-to-top-2-wrap' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'shape_width',
			array(
				'label'      => esc_html__( 'عرض', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 120, 'max' => 800 ),
					'%'  => array( 'min' => 10, 'max' => 100 ),
				),
				'default'    => array( 'size' => 489, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-back-to-top-2' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'shape_color_heading',
			array(
				'label' => esc_html__( 'پس‌زمینه شکل', 'tadris' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'shape_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F6F5F8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-back-to-top-2__shape-bg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'shape_hover_color',
			array(
				'label'     => esc_html__( 'رنگ در هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ECEAEF',
				'selectors' => array(
					'{{WRAPPER}} .webmz-back-to-top-2:hover .webmz-back-to-top-2__shape-bg, {{WRAPPER}} .webmz-back-to-top-2:focus-visible .webmz-back-to-top-2__shape-bg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_color_heading',
			array(
				'label'     => esc_html__( 'فلش', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'     => esc_html__( 'ضخامت فلش', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 1, 'max' => 6 ) ),
				'default'   => array( 'size' => 2, 'unit' => 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-back-to-top-2__shape-arrow' => 'stroke-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6B7280',
				'selectors' => array(
					'{{WRAPPER}} .webmz-back-to-top-2__shape-arrow' => 'stroke: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_hover_color',
			array(
				'label'     => esc_html__( 'رنگ در هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#374151',
				'selectors' => array(
					'{{WRAPPER}} .webmz-back-to-top-2:hover .webmz-back-to-top-2__shape-arrow, {{WRAPPER}} .webmz-back-to-top-2:focus-visible .webmz-back-to-top-2__shape-arrow' => 'stroke: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render inline SVG shape markup.
	 *
	 * @param string $clip_id Unique clip-path id.
	 * @return void
	 */
	protected function render_shape_svg( $clip_id ) {
		?>
		<svg class="webmz-back-to-top-2__shape" viewBox="0 0 489 55" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
			<g class="webmz-back-to-top-2__shape-inner" clip-path="url(#<?php echo esc_attr( $clip_id ); ?>)">
				<path class="webmz-back-to-top-2__shape-bg" d="M245 0C249.409 0 253.652 0.7135 257.619 2.03135C283.825 10.737 307.386 40 335 40H439C466.614 40 489 62.3858 489 90V108C489 135.614 466.614 158 439 158H50C22.3858 158 0 135.614 0 108V90C0 62.3858 22.3858 40 50 40H155C182.614 40 206.175 10.7369 232.381 2.03134C236.348 0.713499 240.591 0 245 0Z" />
				<g class="webmz-back-to-top-2__shape-arrows">
					<path class="webmz-back-to-top-2__shape-arrow webmz-back-to-top-2__shape-arrow--top" d="M238.5 19.5L245 13L251.5 19.5" />
					<path class="webmz-back-to-top-2__shape-arrow webmz-back-to-top-2__shape-arrow--bottom" d="M238.5 27.5L245 21L251.5 27.5" />
				</g>
			</g>
			<defs>
				<clipPath id="<?php echo esc_attr( $clip_id ); ?>" class="webmz-back-to-top-2__shape-clip">
					<rect class="webmz-back-to-top-2__shape-clip-rect" width="489" height="55" fill="white" />
				</clipPath>
			</defs>
		</svg>
		<?php
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$label         = ! empty( $settings['label'] ) ? $settings['label'] : esc_html__( 'رفتن به بالای صفحه', 'tadris' );
		$fixed         = ! empty( $settings['fixed_position'] ) && 'yes' === $settings['fixed_position'];
		$show_after    = ! empty( $settings['show_after_scroll'] ) && 'yes' === $settings['show_after_scroll'];
		$threshold     = ! empty( $settings['show_after_px'] ) ? absint( $settings['show_after_px'] ) : 300;
		$duration      = isset( $settings['scroll_duration'] ) ? absint( $settings['scroll_duration'] ) : 650;
		$button_class  = 'webmz-back-to-top-2';
		$clip_id       = 'webmz-btt2-clip-' . $this->get_id();

		if ( $fixed ) {
			$button_class .= ' webmz-back-to-top-2--fixed';
		}

		if ( $show_after ) {
			$button_class .= ' webmz-back-to-top-2--reveal';
		}
		?>
		<div class="webmz-back-to-top-2-wrap">
			<button
				type="button"
				class="<?php echo esc_attr( $button_class ); ?>"
				data-webmz-back-to-top
				data-threshold="<?php echo esc_attr( $threshold ); ?>"
				data-duration="<?php echo esc_attr( $duration ); ?>"
				aria-label="<?php echo esc_attr( $label ); ?>"
			>
				<?php $this->render_shape_svg( $clip_id ); ?>
			</button>
		</div>
		<?php
	}
}
