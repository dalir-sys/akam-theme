<?php
/**
 * Animated hero section — orbiting icon links around a tilted center image.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Hero section with orbiting linked icons and vanilla-tilt center image.
 */
class Animated_Hero_Section_Widget extends Widget_Base {

	/**
	 * Maximum number of orbit icons.
	 */
	const MAX_ICONS = 6;

	public function get_name() {
		return 'webmz-animated-hero-section';
	}

	public function get_title() {
		return esc_html__( 'انیمیشن هیرو سکشن', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return array( webmz_elementor_category_slug() );
	}

	public function get_keywords() {
		return array( 'hero', 'orbit', 'animation', 'tilt', 'icon', 'هیرو', 'انیمیشن', 'چرخش', 'آیکون' );
	}

	public function get_style_depends() {
		return array( 'webmz-animated-hero-section' );
	}

	public function get_script_depends() {
		return array( 'webmz-tadris-elementor-animations', 'webmz-animated-hero-section' );
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
			)
		);

		$this->add_control(
			'center_image',
			array(
				'label'   => esc_html__( 'تصویر مرکزی', 'tadris' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'center_image_size',
			array(
				'label'   => esc_html__( 'سایز تصویر', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'large',
				'options' => array(
					'thumbnail'    => esc_html__( 'بندانگشتی', 'tadris' ),
					'medium'       => esc_html__( 'متوسط', 'tadris' ),
					'medium_large' => esc_html__( 'متوسط بزرگ', 'tadris' ),
					'large'        => esc_html__( 'بزرگ', 'tadris' ),
					'full'         => esc_html__( 'کامل', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'center_image_alt',
			array(
				'label'       => esc_html__( 'متن جایگزین تصویر', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_orbit_ring',
			array(
				'label'        => esc_html__( 'نمایش دایره خط‌چین', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon_label',
			array(
				'label'       => esc_html__( 'برچسب (فقط پنل)', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'آیکون', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'icon_source',
			array(
				'label'   => esc_html__( 'نوع آیکون', 'tadris' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'icon'  => array(
						'title' => esc_html__( 'آیکون', 'tadris' ),
						'icon'  => 'eicon-star',
					),
					'image' => array(
						'title' => esc_html__( 'تصویر', 'tadris' ),
						'icon'  => 'eicon-image',
					),
				),
				'default' => 'icon',
				'toggle'  => false,
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label'     => esc_html__( 'آیکون', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fab fa-html5',
					'library' => 'fa-brands',
				),
				'condition' => array( 'icon_source' => 'icon' ),
			)
		);

		$repeater->add_control(
			'icon_image',
			array(
				'label'     => esc_html__( 'تصویر آیکون', 'tadris' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array( 'icon_source' => 'image' ),
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

		$this->add_control(
			'icons',
			array(
				'label'       => esc_html__( 'آیکون‌های مدار', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ icon_label }}}',
				'max_items'   => self::MAX_ICONS,
				'default'     => array(
					array(
						'icon_label' => 'Figma',
						'icon'       => array( 'value' => 'fab fa-figma', 'library' => 'fa-brands' ),
						'link'       => array( 'url' => '#' ),
					),
					array(
						'icon_label' => 'HTML5',
						'icon'       => array( 'value' => 'fab fa-html5', 'library' => 'fa-brands' ),
						'link'       => array( 'url' => '#' ),
					),
					array(
						'icon_label' => 'React',
						'icon'       => array( 'value' => 'fab fa-react', 'library' => 'fa-brands' ),
						'link'       => array( 'url' => '#' ),
					),
					array(
						'icon_label' => 'JavaScript',
						'icon'       => array( 'value' => 'fab fa-js', 'library' => 'fa-brands' ),
						'link'       => array( 'url' => '#' ),
					),
					array(
						'icon_label' => 'CSS3',
						'icon'       => array( 'value' => 'fab fa-css3-alt', 'library' => 'fa-brands' ),
						'link'       => array( 'url' => '#' ),
					),
					array(
						'icon_label' => 'Dribbble',
						'icon'       => array( 'value' => 'fab fa-dribbble', 'library' => 'fa-brands' ),
						'link'       => array( 'url' => '#' ),
					),
				),
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
			'stage_size',
			array(
				'label'      => esc_html__( 'اندازه صحنه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 260, 'max' => 720 ),
					'vw' => array( 'min' => 50, 'max' => 100 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 520,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ahs' => '--webmz-ahs-stage-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'orbit_radius',
			array(
				'label'      => esc_html__( 'شعاع مدار', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 100, 'max' => 320 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 210,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ahs' => '--webmz-ahs-orbit-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'center_width',
			array(
				'label'      => esc_html__( 'عرض تصویر مرکزی', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 120, 'max' => 420 ),
					'%'  => array( 'min' => 30, 'max' => 80 ),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 52,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ahs' => '--webmz-ahs-center-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 36, 'max' => 96 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 58,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ahs' => '--webmz-ahs-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'orbit_duration',
			array(
				'label'      => esc_html__( 'مدت یک دور چرخش (ثانیه)', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array(
					's' => array( 'min' => 8, 'max' => 60, 'step' => 1 ),
				),
				'default'    => array(
					'unit' => 's',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ahs' => '--webmz-ahs-orbit-duration: {{SIZE}}s;',
				),
			)
		);

		$this->add_control(
			'tilt_max',
			array(
				'label'      => esc_html__( 'حداکثر زاویه Tilt', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => 0, 'max' => 30 ),
				),
				'default'    => array(
					'unit' => 'deg',
					'size' => 12,
				),
			)
		);

		$this->add_control(
			'tilt_speed',
			array(
				'label'   => esc_html__( 'سرعت Tilt', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 400,
				'min'     => 100,
				'max'     => 1000,
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'section_style_ring',
			array(
				'label' => esc_html__( 'دایره مدار', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'ring_color',
			array(
				'label'     => esc_html__( 'رنگ خط', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(96, 165, 250, 0.45)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ahs' => '--webmz-ahs-ring-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ring_width',
			array(
				'label'      => esc_html__( 'ضخامت خط', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 1, 'max' => 6 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 2,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ahs' => '--webmz-ahs-ring-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			array(
				'label' => esc_html__( 'آیکون‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_color_display',
			array(
				'label'        => esc_html__( 'نمایش رنگ آیکون‌ها', 'tadris' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'grayscale',
				'prefix_class' => 'webmz-ahs-icons--',
				'options'      => array(
					'grayscale' => esc_html__( 'سیاه‌وسفید (رنگی در هاور)', 'tadris' ),
					'color'     => esc_html__( 'همیشه رنگی', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'icon_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ahs__icon-box' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ahs__icon-box' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'icon_shadow',
				'selector' => '{{WRAPPER}} .webmz-ahs__icon-box',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render a single orbit icon.
	 *
	 * @param array<string,mixed> $item  Repeater item.
	 * @param int                 $index Item index.
	 * @param int                 $total Total icon count.
	 * @return void
	 */
	protected function render_icon_item( $item, $index, $total ) {
		$total = max( 1, (int) $total );
		$angle = ( 360 / $total ) * $index;
		$key   = 'ahs_icon_' . absint( $index );

		$this->add_render_attribute( $key, 'class', 'webmz-ahs__icon' );
		$this->add_render_attribute( $key, 'style', '--webmz-ahs-angle: ' . esc_attr( (string) $angle ) . 'deg;' );
		$this->add_link_attributes( $key, ! empty( $item['link'] ) && is_array( $item['link'] ) ? $item['link'] : array( 'url' => '#' ) );

		$icon_source = isset( $item['icon_source'] ) ? (string) $item['icon_source'] : 'icon';
		$has_icon    = 'icon' === $icon_source && ! empty( $item['icon']['value'] );
		$image_url   = '';

		if ( 'image' === $icon_source && ! empty( $item['icon_image']['url'] ) ) {
			$image_url = (string) $item['icon_image']['url'];
		}

		if ( ! $has_icon && '' === $image_url ) {
			return;
		}

		$label = isset( $item['icon_label'] ) ? trim( (string) $item['icon_label'] ) : '';
		?>
		<a <?php echo $this->get_render_attribute_string( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<span class="webmz-ahs__icon-box"<?php echo '' !== $label ? ' aria-label="' . esc_attr( $label ) . '"' : ''; ?>>
				<?php if ( '' !== $image_url ) : ?>
					<img class="webmz-ahs__icon-image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $label ); ?>" loading="lazy" decoding="async" />
				<?php elseif ( $has_icon ) : ?>
					<?php Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) ); ?>
				<?php endif; ?>
			</span>
		</a>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$icons    = ! empty( $settings['icons'] ) && is_array( $settings['icons'] ) ? array_slice( $settings['icons'], 0, self::MAX_ICONS ) : array();

		if ( empty( $icons ) ) {
			return;
		}

		$image_html = '';
		if ( ! empty( $settings['center_image']['id'] ) ) {
			$image_html = wp_get_attachment_image(
				(int) $settings['center_image']['id'],
				! empty( $settings['center_image_size'] ) ? (string) $settings['center_image_size'] : 'large',
				false,
				array(
					'class'   => 'webmz-ahs__image',
					'loading' => 'lazy',
					'decoding'=> 'async',
					'alt'     => ! empty( $settings['center_image_alt'] ) ? (string) $settings['center_image_alt'] : '',
				)
			);
		} elseif ( ! empty( $settings['center_image']['url'] ) ) {
			$image_html = sprintf(
				'<img class="webmz-ahs__image" src="%1$s" alt="%2$s" loading="lazy" decoding="async" />',
				esc_url( (string) $settings['center_image']['url'] ),
				esc_attr( ! empty( $settings['center_image_alt'] ) ? (string) $settings['center_image_alt'] : '' )
			);
		}

		$tilt_max   = isset( $settings['tilt_max']['size'] ) ? (float) $settings['tilt_max']['size'] : 12;
		$tilt_speed = isset( $settings['tilt_speed'] ) ? (int) $settings['tilt_speed'] : 400;
		$show_ring  = ! isset( $settings['show_orbit_ring'] ) || 'yes' === $settings['show_orbit_ring'];

		$this->add_render_attribute( 'center_tilt', 'class', 'webmz-ahs__tilt webmz-tadris-mouse-tilt' );
		$this->add_render_attribute( 'center_tilt', 'data-tilt', '' );
		$this->add_render_attribute( 'center_tilt', 'data-webmz-mouse-tilt-scope', 'page' );
		$this->add_render_attribute( 'center_tilt', 'data-tilt-full-page-listening', 'true' );
		$this->add_render_attribute( 'center_tilt', 'data-tilt-max', (string) $tilt_max );
		$this->add_render_attribute( 'center_tilt', 'data-tilt-speed', (string) $tilt_speed );

		$icon_count = count( $icons );
		?>
		<div class="webmz-ahs">
			<div class="webmz-ahs__stage">
				<?php if ( $show_ring ) : ?>
					<div class="webmz-ahs__ring" aria-hidden="true"></div>
				<?php endif; ?>

				<div class="webmz-ahs__orbit" aria-hidden="false">
					<?php foreach ( $icons as $index => $item ) : ?>
						<?php $this->render_icon_item( $item, (int) $index, $icon_count ); ?>
					<?php endforeach; ?>
				</div>

				<?php if ( $image_html ) : ?>
					<div class="webmz-ahs__center">
						<div <?php echo $this->get_render_attribute_string( 'center_tilt' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
