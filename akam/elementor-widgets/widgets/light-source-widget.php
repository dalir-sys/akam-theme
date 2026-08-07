<?php
/**
 * Decorative light source / mesh gradient Elementor widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Soft glow light sources for page decoration.
 */
class Light_Source_Widget extends Widget_Base {

	public function get_name() {
		return 'webmz-light-source';
	}

	public function get_title() {
		return esc_html__( 'منبع نور', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-lightbox';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'light', 'glow', 'gradient', 'mesh', 'decoration', 'نور', 'درخشش', 'مش', 'گرادینت', 'دکوری' );
	}

	public function get_style_depends() {
		return array( 'webmz-light-source' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_lights',
			array(
				'label' => esc_html__( 'منابع نور', 'tadris' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'light_title',
			array(
				'label'       => esc_html__( 'نام (فقط در پنل)', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'نور ۱', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'light_color',
			array(
				'label'   => esc_html__( 'رنگ', 'tadris' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#FF8A8A',
			)
		);

		$repeater->add_control(
			'light_opacity',
			array(
				'label'   => esc_html__( 'شدت رنگ', 'tadris' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array(
					'px' => array( 'min' => 0.05, 'max' => 1, 'step' => 0.05 ),
				),
				'default' => array( 'size' => 0.55 ),
			)
		);

		$repeater->add_control(
			'light_x',
			array(
				'label'   => esc_html__( 'موقعیت افقی', 'tadris' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array(
					'px' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
				),
				'default' => array( 'size' => 18 ),
			)
		);

		$repeater->add_control(
			'light_y',
			array(
				'label'   => esc_html__( 'موقعیت عمودی', 'tadris' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array(
					'px' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
				),
				'default' => array( 'size' => 12 ),
			)
		);

		$repeater->add_control(
			'light_size',
			array(
				'label'      => esc_html__( 'اندازه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 80, 'max' => 900 ),
					'%'  => array( 'min' => 20, 'max' => 120 ),
					'vw' => array( 'min' => 10, 'max' => 80 ),
				),
				'default'    => array( 'size' => 420, 'unit' => 'px' ),
			)
		);

		$repeater->add_control(
			'light_blur',
			array(
				'label'   => esc_html__( 'محوشدگی (Blur)', 'tadris' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array(
					'px' => array( 'min' => 20, 'max' => 200, 'step' => 2 ),
				),
				'default' => array( 'size' => 90 ),
			)
		);

		$this->add_control(
			'lights',
			array(
				'label'       => esc_html__( 'لیست نورها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ light_title }}}',
				'default'     => array(
					array(
						'light_title'  => esc_html__( 'نور قرمز', 'tadris' ),
						'light_color'  => '#FF8A8A',
						'light_opacity' => array( 'size' => 0.62 ),
						'light_x'      => array( 'size' => 16 ),
						'light_y'      => array( 'size' => 10 ),
						'light_size'   => array( 'size' => 480, 'unit' => 'px' ),
						'light_blur'   => array( 'size' => 100 ),
					),
					array(
						'light_title'  => esc_html__( 'نور صورتی', 'tadris' ),
						'light_color'  => '#FFB4B4',
						'light_opacity' => array( 'size' => 0.38 ),
						'light_x'      => array( 'size' => 42 ),
						'light_y'      => array( 'size' => 28 ),
						'light_size'   => array( 'size' => 360, 'unit' => 'px' ),
						'light_blur'   => array( 'size' => 80 ),
					),
				),
			)
		);

		$this->add_control(
			'render_mode',
			array(
				'label'   => esc_html__( 'نوع نمایش', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'mesh',
				'options' => array(
					'mesh'  => esc_html__( 'مش گرادینت (ترکیب همه نورها)', 'tadris' ),
					'blobs' => esc_html__( 'نورهای جداگانه', 'tadris' ),
					'both'  => esc_html__( 'هر دو (مش + نور جدا)', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'behind_content_heading',
			array(
				'label'     => esc_html__( 'لایه پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'behind_content',
			array(
				'label'        => esc_html__( 'قرارگیری مطلق (پشت المان‌ها)', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => '',
				'prefix_class' => 'webmz-ls-behind-',
				'description'  => esc_html__( 'ویجت را absolute می‌کند و زیر سایر ویجت‌های همان ستون/کانتینر قرار می‌گیرد. این ویجت را در همان بخشی که محتوا دارد اضافه کنید.', 'tadris' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_container',
			array(
				'label' => esc_html__( 'ظرف نور', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'container_width',
			array(
				'label'      => esc_html__( 'عرض', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 100, 'max' => 2000 ),
					'%'  => array( 'min' => 10, 'max' => 100 ),
					'vw' => array( 'min' => 10, 'max' => 100 ),
				),
				'default'    => array( 'size' => 100, 'unit' => '%' ),
				'condition'  => array(
					'behind_content!' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-light-source' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_height',
			array(
				'label'      => esc_html__( 'ارتفاع', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 80, 'max' => 1200 ),
					'vh' => array( 'min' => 10, 'max' => 100 ),
				),
				'default'    => array( 'size' => 520, 'unit' => 'px' ),
				'condition'  => array(
					'behind_content!' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-light-source' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'container_opacity',
			array(
				'label'     => esc_html__( 'شفافیت کلی', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ),
				),
				'default'   => array( 'size' => 1 ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-light-source__canvas' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'mesh_blur',
			array(
				'label'     => esc_html__( 'Blur مش گرادینت', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'min' => 0, 'max' => 120, 'step' => 2 ),
				),
				'default'   => array( 'size' => 52 ),
				'condition' => array(
					'render_mode' => array( 'mesh', 'both' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-light-source__mesh' => 'filter: blur({{SIZE}}px);',
				),
			)
		);

		$this->add_control(
			'blend_mode',
			array(
				'label'     => esc_html__( 'حالت ترکیب', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'normal',
				'options'   => array(
					'normal'      => esc_html__( 'عادی', 'tadris' ),
					'multiply'    => 'Multiply',
					'screen'      => 'Screen',
					'overlay'     => 'Overlay',
					'soft-light'  => 'Soft Light',
					'hard-light'  => 'Hard Light',
					'color-dodge' => 'Color Dodge',
					'color-burn'  => 'Color Burn',
					'difference'  => 'Difference',
					'exclusion'   => 'Exclusion',
					'hue'         => 'Hue',
					'saturation'  => 'Saturation',
					'color'       => 'Color',
					'luminosity'  => 'Luminosity',
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-light-source__canvas' => 'mix-blend-mode: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'enable_animation',
			array(
				'label'        => esc_html__( 'انیمیشن تنفس', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'animation_speed',
			array(
				'label'     => esc_html__( 'سرعت انیمیشن (ثانیه)', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'min' => 3, 'max' => 30, 'step' => 1 ),
				),
				'default'   => array( 'size' => 8 ),
				'condition' => array(
					'enable_animation' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-light-source__canvas' => '--webmz-ls-anim-duration: {{SIZE}}s;',
				),
			)
		);

		$this->add_control(
			'overflow_visible',
			array(
				'label'        => esc_html__( 'نور از ظرف بیرون بزند', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'selectors'    => array(
					'{{WRAPPER}} .webmz-light-source' => 'overflow: {{VALUE}};',
				),
				'selectors_dictionary' => array(
					'yes' => 'visible',
					''    => 'hidden',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_position',
			array(
				'label'     => esc_html__( 'موقعیت لایه پس‌زمینه', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'behind_content' => 'yes',
				),
			)
		);

		$this->add_control(
			'z_index',
			array(
				'label'       => esc_html__( 'Z-Index', 'tadris' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'description' => esc_html__( 'مقدار 0 یعنی پشت سایر ویجت‌های همان ستون. برای رفتن عمیق‌تر به پشت، از اعداد منفی استفاده کنید.', 'tadris' ),
				'selectors'   => array(
					'{{WRAPPER}}' => 'z-index: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'position_top',
			array(
				'label'      => esc_html__( 'فاصله از بالا', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => -500, 'max' => 500 ),
					'%'  => array( 'min' => -50, 'max' => 100 ),
					'vh' => array( 'min' => -50, 'max' => 100 ),
				),
				'default'    => array( 'size' => 0, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'position_right',
			array(
				'label'      => esc_html__( 'فاصله از راست', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => -500, 'max' => 500 ),
					'%'  => array( 'min' => -50, 'max' => 100 ),
					'vw' => array( 'min' => -50, 'max' => 100 ),
				),
				'default'    => array( 'size' => 0, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'position_bottom',
			array(
				'label'      => esc_html__( 'فاصله از پایین', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => -500, 'max' => 500 ),
					'%'  => array( 'min' => -50, 'max' => 100 ),
					'vh' => array( 'min' => -50, 'max' => 100 ),
				),
				'default'    => array( 'size' => 0, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}' => 'bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'position_left',
			array(
				'label'      => esc_html__( 'فاصله از چپ', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => -500, 'max' => 500 ),
					'%'  => array( 'min' => -50, 'max' => 100 ),
					'vw' => array( 'min' => -50, 'max' => 100 ),
				),
				'default'    => array( 'size' => 0, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Parse color string to RGBA.
	 *
	 * @param string $color Color value.
	 * @param float  $alpha Alpha channel.
	 * @return string
	 */
	protected function color_to_rgba( $color, $alpha = 1 ) {
		$color = trim( (string) $color );
		$alpha = max( 0, min( 1, (float) $alpha ) );

		if ( preg_match( '/^rgba?\(([^)]+)\)$/i', $color, $matches ) ) {
			$parts = array_map( 'trim', explode( ',', $matches[1] ) );
			$r     = isset( $parts[0] ) ? (int) $parts[0] : 0;
			$g     = isset( $parts[1] ) ? (int) $parts[1] : 0;
			$b     = isset( $parts[2] ) ? (int) $parts[2] : 0;

			return sprintf( 'rgba(%d,%d,%d,%s)', $r, $g, $b, $alpha );
		}

		$hex = ltrim( $color, '#' );

		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
			return sprintf( 'rgba(255,138,138,%s)', $alpha );
		}

		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );

		return sprintf( 'rgba(%d,%d,%d,%s)', $r, $g, $b, $alpha );
	}

	/**
	 * Build mesh gradient CSS from light items.
	 *
	 * @param array<int,array<string,mixed>> $lights Light repeater items.
	 * @return string
	 */
	protected function build_mesh_gradient( array $lights ) {
		$layers = array();

		foreach ( $lights as $light ) {
			$x       = isset( $light['light_x']['size'] ) ? (float) $light['light_x']['size'] : 50;
			$y       = isset( $light['light_y']['size'] ) ? (float) $light['light_y']['size'] : 50;
			$color   = ! empty( $light['light_color'] ) ? $light['light_color'] : '#FF8A8A';
			$opacity = isset( $light['light_opacity']['size'] ) ? (float) $light['light_opacity']['size'] : 0.5;

			$center = $this->color_to_rgba( $color, $opacity );
			$mid    = $this->color_to_rgba( $color, $opacity * 0.35 );
			$layers[] = sprintf(
				'radial-gradient(circle at %s%% %s%%, %s 0%%, %s 42%%, transparent 70%%)',
				$x,
				$y,
				$center,
				$mid
			);
		}

		return implode( ', ', $layers );
	}

	/**
	 * Get slider value with unit.
	 *
	 * @param array<string,mixed> $slider Slider settings.
	 * @param string              $default_unit Default unit.
	 * @param float               $default_size Default size.
	 * @return string
	 */
	protected function get_slider_value( $slider, $default_unit = 'px', $default_size = 0 ) {
		if ( empty( $slider ) || ! is_array( $slider ) ) {
			return $default_size . $default_unit;
		}

		$size = isset( $slider['size'] ) ? $slider['size'] : $default_size;
		$unit = ! empty( $slider['unit'] ) ? $slider['unit'] : $default_unit;

		return esc_attr( $size . $unit );
	}

	/**
	 * Render a single blob light.
	 *
	 * @param array<string,mixed> $light Light item.
	 * @param int                 $index Item index.
	 * @return void
	 */
	protected function render_blob( $light, $index ) {
		$color   = ! empty( $light['light_color'] ) ? $light['light_color'] : '#FF8A8A';
		$opacity = isset( $light['light_opacity']['size'] ) ? (float) $light['light_opacity']['size'] : 0.5;
		$x       = isset( $light['light_x']['size'] ) ? (float) $light['light_x']['size'] : 50;
		$y       = isset( $light['light_y']['size'] ) ? (float) $light['light_y']['size'] : 50;
		$size    = $this->get_slider_value( $light['light_size'] ?? array(), 'px', 320 );
		$blur    = isset( $light['light_blur']['size'] ) ? (float) $light['light_blur']['size'] : 80;
		$rgba    = $this->color_to_rgba( $color, $opacity );
		?>
		<span
			class="webmz-light-source__blob webmz-light-source__blob--<?php echo esc_attr( (string) ( $index + 1 ) ); ?>"
			style="
				--webmz-ls-blob-x: <?php echo esc_attr( $x ); ?>%;
				--webmz-ls-blob-y: <?php echo esc_attr( $y ); ?>%;
				--webmz-ls-blob-size: <?php echo esc_attr( $size ); ?>;
				--webmz-ls-blob-blur: <?php echo esc_attr( $blur ); ?>px;
				--webmz-ls-blob-color: <?php echo esc_attr( $rgba ); ?>;
			"
		></span>
		<?php
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$lights      = ! empty( $settings['lights'] ) && is_array( $settings['lights'] ) ? $settings['lights'] : array();
		$render_mode = ! empty( $settings['render_mode'] ) ? sanitize_key( $settings['render_mode'] ) : 'mesh';
		$animated    = ( ! empty( $settings['enable_animation'] ) && 'yes' === $settings['enable_animation'] );

		if ( empty( $lights ) ) {
			return;
		}

		$canvas_classes = array( 'webmz-light-source__canvas' );
		if ( $animated ) {
			$canvas_classes[] = 'webmz-light-source__canvas--animated';
		}

		$show_mesh  = in_array( $render_mode, array( 'mesh', 'both' ), true );
		$show_blobs = in_array( $render_mode, array( 'blobs', 'both' ), true );
		$mesh_style = $show_mesh ? $this->build_mesh_gradient( $lights ) : '';
		?>
		<div class="webmz-light-source" aria-hidden="true">
			<div class="<?php echo esc_attr( implode( ' ', $canvas_classes ) ); ?>">
				<?php if ( $show_mesh && $mesh_style ) : ?>
					<div class="webmz-light-source__mesh" style="background: <?php echo esc_attr( $mesh_style ); ?>;"></div>
				<?php endif; ?>

				<?php if ( $show_blobs ) : ?>
					<div class="webmz-light-source__blobs">
						<?php foreach ( $lights as $index => $light ) : ?>
							<?php $this->render_blob( $light, (int) $index ); ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
