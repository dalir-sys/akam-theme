<?php
/**
 * Elementor website-logo widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Selectable website logo linked automatically to the home URL.
 */
class Site_Logo_Widget extends Widget_Base {

	public function get_name() {
		return 'webmz-site-logo';
	}

	public function get_title() {
		return esc_html__( 'لوگو وبسایت', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-site-logo';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'logo', 'site', 'home', 'لوگو', 'خانه' );
	}

	public function get_style_depends() {
		return array( 'webmz-site-logo' );
	}

	/**
	 * Register Elementor controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_logo',
			array(
				'label' => esc_html__( 'لوگو', 'tadris' ),
			)
		);

		$this->add_control(
			'logo_image',
			array(
				'label'   => esc_html__( 'تصویر لوگو', 'tadris' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => '',
				),
			)
		);

		$this->add_control(
			'alt_text',
			array(
				'label'       => esc_html__( 'متن جایگزین', 'tadris' ),
				'description' => esc_html__( 'در صورت خالی بودن، نام سایت استفاده می‌شود.', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);

		$this->add_control(
			'light_sweep_effect',
			array(
				'label'        => esc_html__( 'افکت نور عبوری', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_logo_style',
			array(
				'label' => esc_html__( 'اندازه و چیدمان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => esc_html__( 'چینش', 'tadris' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'right'  => array(
						'title' => esc_html__( 'راست', 'tadris' ),
						'icon'  => 'eicon-text-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'وسط', 'tadris' ),
						'icon'  => 'eicon-text-align-center',
					),
					'left'   => array(
						'title' => esc_html__( 'چپ', 'tadris' ),
						'icon'  => 'eicon-text-align-left',
					),
				),
				'default'   => 'right',
				'selectors' => array(
					'{{WRAPPER}} .webmz-site-logo' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'logo_width',
			array(
				'label'      => esc_html__( 'عرض لوگو', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 700,
					),
					'%' => array(
						'min' => 1,
						'max' => 100,
					),
					'vw' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 150,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-site-logo__image' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'logo_max_height',
			array(
				'label'      => esc_html__( 'حداکثر ارتفاع لوگو', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 350,
					),
					'vh' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 90,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-site-logo__image' => 'max-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render a logo whose link is always the website homepage.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$image_id = ! empty( $settings['logo_image']['id'] ) ? absint( $settings['logo_image']['id'] ) : 0;
		$image_url = ! empty( $settings['logo_image']['url'] ) ? $settings['logo_image']['url'] : '';

		if ( ! $image_id && has_custom_logo() ) {
			$image_id  = absint( get_theme_mod( 'custom_logo' ) );
			$image_url = wp_get_attachment_image_url( $image_id, 'full' );
		}

		if ( ! $image_url ) {
			return;
		}

		$alt = ! empty( $settings['alt_text'] )
			? $settings['alt_text']
			: get_post_meta( $image_id, '_wp_attachment_image_alt', true );

		if ( '' === trim( (string) $alt ) ) {
			$alt = get_bloginfo( 'name' );
		}

		$this->add_render_attribute( 'link', 'class', 'webmz-site-logo__link' );
		$this->add_render_attribute( 'link', 'href', home_url( '/' ) );

		if ( ! isset( $settings['light_sweep_effect'] ) || 'yes' === $settings['light_sweep_effect'] ) {
			$this->add_render_attribute( 'link', 'class', 'light-sweep-effect' );
		}

		$this->add_render_attribute( 'link', 'aria-label', get_bloginfo( 'name' ) );

		$this->add_render_attribute( 'image', 'class', 'webmz-site-logo__image' );
		$this->add_render_attribute( 'image', 'src', esc_url( $image_url ) );
		$this->add_render_attribute( 'image', 'alt', $alt );
		?>
		<div class="webmz-site-logo">
			<a <?php echo $this->get_render_attribute_string( 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<img <?php echo $this->get_render_attribute_string( 'image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			</a>
		</div>
		<?php
	}
}
