<?php
/**
 * Decorative SVG Shape Elementor widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Shape builder widget for decorative SVG graphics.
 */
class Shape_Widget extends Widget_Base {

	public function get_name() {
		return 'webmz-shape';
	}

	public function get_title() {
		return esc_html__( 'شیپ ساز (Shape)', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-shape';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'shape', 'svg', 'decoration', 'graphic', 'شیپ', 'شکل', 'دکوری', 'گرافیک' );
	}

	public function get_style_depends() {
		return array( 'webmz-shape' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'شکل', 'tadris' ),
			)
		);

		$this->add_control(
			'shape',
			array(
				'label'   => esc_html__( 'انتخاب شکل', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'rounded_square',
				'options' => Shape_SVG_Library::get_select_options(),
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => esc_html__( 'لینک', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
				'default'     => array( 'url' => '' ),
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
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .webmz-shape-wrap' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'shape_size',
			array(
				'label'      => esc_html__( 'اندازه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 20, 'max' => 600 ),
					'%'  => array( 'min' => 5, 'max' => 100 ),
				),
				'default'    => array( 'size' => 120, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-shape' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'shape_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .webmz-shape' => '--webmz-shape-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'shape_opacity',
			array(
				'label'     => esc_html__( 'شفافیت', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ),
				),
				'default'   => array( 'size' => 1 ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-shape' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'rotate',
			array(
				'label'      => esc_html__( 'چرخش', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => 0, 'max' => 360, 'step' => 1 ),
				),
				'default'    => array( 'size' => 0, 'unit' => 'deg' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-shape' => 'transform: rotate({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->add_control(
			'stroke_width',
			array(
				'label'      => esc_html__( 'ضخامت خط', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 1, 'max' => 16, 'step' => 1 ),
				),
				'default'    => array( 'size' => 4, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-shape' => '--webmz-shape-stroke-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'shape_shadow',
				'selector' => '{{WRAPPER}} .webmz-shape',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render SVG markup for the selected shape.
	 *
	 * @param string $shape_slug Shape slug.
	 * @return void
	 */
	protected function render_shape_svg( $shape_slug ) {
		$mode     = Shape_SVG_Library::get_mode( $shape_slug );
		$content  = Shape_SVG_Library::get_content( $shape_slug );
		$clip     = Shape_SVG_Library::get_clip( $shape_slug );
		$clip_id  = 'webmz-shape-clip-' . $this->get_id();
		$classes  = array( 'webmz-shape', 'webmz-shape--' . sanitize_html_class( $shape_slug ) );

		if ( 'stroke' === $mode ) {
			$classes[] = 'webmz-shape--stroke';
		}
		?>
		<svg
			class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			viewBox="0 0 100 100"
			xmlns="http://www.w3.org/2000/svg"
			aria-hidden="true"
			focusable="false"
			role="presentation"
		>
			<?php if ( 'square' === $clip ) : ?>
				<defs>
					<clipPath id="<?php echo esc_attr( $clip_id ); ?>">
						<rect x="14" y="14" width="72" height="72" />
					</clipPath>
				</defs>
			<?php endif; ?>
			<g class="webmz-shape__inner"<?php echo 'square' === $clip ? ' clip-path="url(#' . esc_attr( $clip_id ) . ')"' : ''; ?>>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted SVG library markup.
				echo $content;
				?>
			</g>
		</svg>
		<?php
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$shape_slug = ! empty( $settings['shape'] ) ? sanitize_key( $settings['shape'] ) : 'rounded_square';
		$has_link   = ! empty( $settings['link']['url'] );
		$link_attrs = '';

		if ( $has_link ) {
			$this->add_link_attributes( 'shape_link', $settings['link'] );
			$link_attrs = $this->get_render_attribute_string( 'shape_link' );
		}
		?>
		<div class="webmz-shape-wrap">
			<?php if ( $has_link ) : ?>
				<a class="webmz-shape-link" <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php $this->render_shape_svg( $shape_slug ); ?>
				</a>
			<?php else : ?>
				<?php $this->render_shape_svg( $shape_slug ); ?>
			<?php endif; ?>
		</div>
		<?php
	}
}
