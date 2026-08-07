<?php
/**
 * WooCommerce checkout/cart steps indicator widget for WebMZ.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tadris_Checkout_Steps_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-checkout-steps';
	}

	public function get_title() {
		return esc_html__( 'indicator مراحل خرید آکام', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-navigation-horizontal';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'checkout', 'cart', 'steps', 'indicator', 'woocommerce', 'پرداخت', 'سبد خرید' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'مراحل', 'tadris' ),
			)
		);

		$this->add_control(
			'current_step',
			array(
				'label'   => esc_html__( 'مرحله فعال', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cart',
				'options' => array(
					'cart'     => esc_html__( 'سبد خرید', 'tadris' ),
					'checkout' => esc_html__( 'صورتحساب', 'tadris' ),
					'order'    => esc_html__( 'فاکتور', 'tadris' ),
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'icon_color_mode', esc_html__( 'نوع رنگ‌دهی SVG', 'tadris' ) );

		$this->add_control(
			'show_links',
			array(
				'label'        => esc_html__( 'لینک‌دار بودن مراحل', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'step_1_heading',
			array(
				'label'     => esc_html__( 'مرحله ۱', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'step_1_title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'سبد خرید', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'step_1_icon',
			array(
				'label'   => esc_html__( 'آیکون', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-shopping-basket',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'step_1_link',
			array(
				'label'       => esc_html__( 'لینک', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '',
				'default'     => array( 'url' => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '' ),
				'condition'   => array( 'show_links' => 'yes' ),
			)
		);

		$this->add_control(
			'step_2_heading',
			array(
				'label'     => esc_html__( 'مرحله ۲', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'step_2_title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'صورتحساب', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'step_2_icon',
			array(
				'label'   => esc_html__( 'آیکون', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'far fa-map',
					'library' => 'fa-regular',
				),
			)
		);

		$this->add_control(
			'step_2_link',
			array(
				'label'       => esc_html__( 'لینک', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : '',
				'default'     => array( 'url' => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : '' ),
				'condition'   => array( 'show_links' => 'yes' ),
			)
		);

		$this->add_control(
			'step_3_heading',
			array(
				'label'     => esc_html__( 'مرحله ۳', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'step_3_title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'فاکتور', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'step_3_icon',
			array(
				'label'   => esc_html__( 'آیکون', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'far fa-clipboard',
					'library' => 'fa-regular',
				),
			)
		);

		$this->add_control(
			'step_3_link',
			array(
				'label'       => esc_html__( 'لینک', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => '#',
				'default'     => array( 'url' => '' ),
				'condition'   => array( 'show_links' => 'yes' ),
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
			'gap',
			array(
				'label'      => esc_html__( 'فاصله مراحل', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 120 ) ),
				'default'    => array( 'size' => 42, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-checkout-steps' => '--webmz-step-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_box_size',
			array(
				'label'      => esc_html__( 'اندازه باکس آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 36, 'max' => 120 ) ),
				'default'    => array( 'size' => 64, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-checkout-step__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 14, 'max' => 54 ) ),
				'default'    => array( 'size' => 26, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-checkout-step__icon i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-checkout-step__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'active_color',
			array(
				'label'     => esc_html__( 'رنگ مرحله فعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary, #0878f9)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-checkout-steps' => '--webmz-step-active: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'inactive_color',
			array(
				'label'     => esc_html__( 'رنگ مرحله غیرفعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d5dbe7',
				'selectors' => array(
					'{{WRAPPER}} .webmz-checkout-steps' => '--webmz-step-inactive: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'line_color',
			array(
				'label'     => esc_html__( 'رنگ خط اتصال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#edf1f7',
				'selectors' => array(
					'{{WRAPPER}} .webmz-checkout-steps' => '--webmz-step-line: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-checkout-step__title',
			)
		);

		$this->add_responsive_control(
			'padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-checkout-steps' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'margin',
			array(
				'label'      => esc_html__( 'فاصله بیرونی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-checkout-steps' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function render_step_icon( $icon ) {
		if ( empty( $icon['value'] ) ) {
			return;
		}

		Icons_Manager::render_icon(
			$icon,
			array(
				'aria-hidden' => 'true',
			)
		);
	}

	private function get_step_class( $step_index, $current_index ) {
		$classes = array( 'webmz-checkout-step' );

		if ( $step_index === $current_index ) {
			$classes[] = 'is-active';
		} elseif ( $step_index < $current_index ) {
			$classes[] = 'is-completed';
		} else {
			$classes[] = 'is-disabled';
		}

		return implode( ' ', $classes );
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$current       = isset( $settings['current_step'] ) ? sanitize_key( $settings['current_step'] ) : 'cart';
		$current_index = array_search( $current, array( 'cart', 'checkout', 'order' ), true );
		$current_index = false === $current_index ? 1 : ( $current_index + 1 );
		$icon_mode     = $this->webmz_get_icon_color_mode_class( $settings, 'icon_color_mode' );
		$show_links    = isset( $settings['show_links'] ) && 'yes' === $settings['show_links'];

		$steps = array(
			1 => array(
				'title' => isset( $settings['step_1_title'] ) ? $settings['step_1_title'] : esc_html__( 'سبد خرید', 'tadris' ),
				'icon'  => isset( $settings['step_1_icon'] ) ? $settings['step_1_icon'] : array(),
				'link'  => isset( $settings['step_1_link']['url'] ) ? $settings['step_1_link']['url'] : '',
			),
			2 => array(
				'title' => isset( $settings['step_2_title'] ) ? $settings['step_2_title'] : esc_html__( 'صورتحساب', 'tadris' ),
				'icon'  => isset( $settings['step_2_icon'] ) ? $settings['step_2_icon'] : array(),
				'link'  => isset( $settings['step_2_link']['url'] ) ? $settings['step_2_link']['url'] : '',
			),
			3 => array(
				'title' => isset( $settings['step_3_title'] ) ? $settings['step_3_title'] : esc_html__( 'فاکتور', 'tadris' ),
				'icon'  => isset( $settings['step_3_icon'] ) ? $settings['step_3_icon'] : array(),
				'link'  => isset( $settings['step_3_link']['url'] ) ? $settings['step_3_link']['url'] : '',
			),
		);
		?>
		<div class="webmz-checkout-steps <?php echo esc_attr( $icon_mode ); ?>" dir="rtl" role="list">
			<?php foreach ( $steps as $index => $step ) : ?>
				<?php
				$tag        = ( $show_links && ! empty( $step['link'] ) ) ? 'a' : 'div';
				$class_name = $this->get_step_class( (int) $index, (int) $current_index );
				?>
				<<?php echo tag_escape( $tag ); ?> class="<?php echo esc_attr( $class_name ); ?>" <?php echo 'a' === $tag ? 'href="' . esc_url( $step['link'] ) . '"' : ''; ?> role="listitem">
					<span class="webmz-checkout-step__icon">
						<?php $this->render_step_icon( $step['icon'] ); ?>
					</span>
					<span class="webmz-checkout-step__title"><?php echo esc_html( $step['title'] ); ?></span>
				</<?php echo tag_escape( $tag ); ?>>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
