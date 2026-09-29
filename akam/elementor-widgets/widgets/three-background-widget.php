<?php
/**
 * Three.js animated 3D background Elementor widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

class Three_Background_Widget extends Widget_Base {

	public function get_name() {
		return 'webmz-three-background';
	}

	public function get_title() {
		return esc_html__( 'پس‌زمینه سه‌بعدی (Three.js)', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-animation';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( '3d', 'three', 'threejs', 'background', 'particles', 'سه بعدی', 'پس زمینه', 'ذرات' );
	}

	public function get_script_depends() {
		return array( 'webmz-three-background' );
	}

	public function get_style_depends() {
		return array( 'webmz-three-background' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'افکت', 'tadris' ) ) );

		$this->add_control( 'preset', array(
			'label'   => esc_html__( 'نوع افکت', 'tadris' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'network',
			'options' => array(
				'network' => esc_html__( 'شبکه ذرات', 'tadris' ),
				'waves'   => esc_html__( 'موج نقطه‌ای', 'tadris' ),
				'orbs'    => esc_html__( 'اشکال شناور سه‌بعدی', 'tadris' ),
				'globe'   => esc_html__( 'کره زمین نقطه‌ای', 'tadris' ),
			),
		) );

		$this->add_control( 'fill_section', array(
			'label'        => esc_html__( 'پس‌زمینه کل بخش', 'tadris' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'return_value' => 'yes',
			'prefix_class' => 'webmz-3d-fill-',
			'description'  => esc_html__( 'افکت پشت سایر المان‌های همین کانتینر قرار می‌گیرد و کل بخش را پر می‌کند. برای استفاده به‌عنوان یک المان مستقل، خاموش کنید.', 'tadris' ),
		) );

		$this->add_responsive_control( 'height', array(
			'label'      => esc_html__( 'ارتفاع', 'tadris' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', 'vh' ),
			'range'      => array(
				'px' => array( 'min' => 160, 'max' => 1200 ),
				'vh' => array( 'min' => 20, 'max' => 100 ),
			),
			'default'    => array( 'unit' => 'px', 'size' => 420 ),
			'selectors'  => array( '{{WRAPPER}} .webmz-3d' => 'height: {{SIZE}}{{UNIT}};' ),
			'condition'  => array( 'fill_section!' => 'yes' ),
		) );

		$this->add_control( 'color1', array(
			'label'       => esc_html__( 'رنگ اول', 'tadris' ),
			'type'        => Controls_Manager::COLOR,
			'default'     => '',
			'description' => esc_html__( 'خالی = رنگ اصلی قالب', 'tadris' ),
		) );

		$this->add_control( 'color2', array(
			'label'   => esc_html__( 'رنگ دوم', 'tadris' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#8b5cf6',
		) );

		$this->add_control( 'density', array(
			'label'   => esc_html__( 'تراکم', 'tadris' ),
			'type'    => Controls_Manager::SLIDER,
			'range'   => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
			'default' => array( 'size' => 50 ),
		) );

		$this->add_control( 'speed', array(
			'label'   => esc_html__( 'سرعت', 'tadris' ),
			'type'    => Controls_Manager::SLIDER,
			'range'   => array( 'px' => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ) ),
			'default' => array( 'size' => 1 ),
		) );

		$this->add_control( 'opacity', array(
			'label'   => esc_html__( 'شفافیت', 'tadris' ),
			'type'    => Controls_Manager::SLIDER,
			'range'   => array( 'px' => array( 'min' => 0.1, 'max' => 1, 'step' => 0.05 ) ),
			'default' => array( 'size' => 1 ),
		) );

		$this->add_control( 'mouse', array(
			'label'        => esc_html__( 'حرکت با ماوس', 'tadris' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'return_value' => 'yes',
		) );

		$this->add_control( 'performance_note', array(
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => esc_html__( 'افکت فقط وقتی در صفحه دیده می‌شود اجرا می‌شود، در موبایل سبک‌تر است و برای کاربرانی که «کاهش حرکت» را در سیستم فعال کرده‌اند به‌صورت تصویر ثابت نمایش داده می‌شود.', 'tadris' ),
			'content_classes' => 'elementor-descriptor',
		) );

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$config = array(
			'preset'  => in_array( $s['preset'] ?? '', array( 'network', 'waves', 'orbs', 'globe' ), true ) ? $s['preset'] : 'network',
			'color1'  => sanitize_hex_color( $s['color1'] ?? '' ) ? $s['color1'] : '',
			'color2'  => sanitize_hex_color( $s['color2'] ?? '' ) ? $s['color2'] : '#8b5cf6',
			'density' => isset( $s['density']['size'] ) ? (float) $s['density']['size'] : 50,
			'speed'   => isset( $s['speed']['size'] ) ? (float) $s['speed']['size'] : 1,
			'opacity' => isset( $s['opacity']['size'] ) ? (float) $s['opacity']['size'] : 1,
			'mouse'   => 'yes' === ( $s['mouse'] ?? 'yes' ),
		);
		?>
		<div class="webmz-3d webmz-3d--<?php echo esc_attr( $config['preset'] ); ?>" data-webmz-3d="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"></div>
		<?php
	}
}
