<?php
/**
 * Elementor isolated AJAX header-cart widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Custom header cart. The widget ID remains stable for existing Elementor layouts.
 */
class Mini_Cart_Widget extends Widget_Base {
	use Mobile_Header_Widget_Trait;
	use Header_Commerce_Widget_Trait;

	public function get_name() {
		return 'webmz-mini-cart';
	}

	public function get_title() {
		return esc_html__( 'سبد خرید ایجکسی', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-cart';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'cart', 'mini cart', 'woocommerce', 'ajax', 'سبد خرید' );
	}

	public function get_style_depends() {
		return array( 'webmz-header-commerce', 'webmz-mobile-offcanvas' );
	}

	public function get_script_depends() {
		return array( 'webmz-mobile-offcanvas', 'webmz-header-commerce', 'wc-add-to-cart' );
	}

	/** Check whether the widget is being rendered inside the Elementor editor. */
	private function is_elementor_edit_mode() {
		$is_plugin_edit_mode = class_exists( '\Elementor\Plugin' )
			&& isset( \Elementor\Plugin::$instance->editor )
			&& \Elementor\Plugin::$instance->editor->is_edit_mode();

		$is_plugin_preview_mode = class_exists( '\Elementor\Plugin' )
			&& isset( \Elementor\Plugin::$instance->preview )
			&& method_exists( \Elementor\Plugin::$instance->preview, 'is_preview_mode' )
			&& \Elementor\Plugin::$instance->preview->is_preview_mode();

		return $is_plugin_edit_mode
			|| $is_plugin_preview_mode
			|| isset( $_GET['elementor-preview'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/** Register widget controls. */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'سبد خرید', 'tadris' ) )
		);

		$this->add_control(
			'cart_icon',
			array(
				'label'   => esc_html__( 'آیکون سبد خرید', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-shopping-basket',
					'library' => 'fa-solid',
				),
			)
		);



		$this->add_control(
			'icon_color_mode',
			array(
				'label'       => esc_html__( 'نوع رنگ‌دهی آیکون SVG', 'tadris' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'stroke',
				'options'     => array(
					'auto'   => esc_html__( 'خودکار / بدون اجبار', 'tadris' ),
					'stroke' => esc_html__( 'Stroke / خطی', 'tadris' ),
					'fill'   => esc_html__( 'Fill / توپر', 'tadris' ),
					'both'   => esc_html__( 'هر دو', 'tadris' ),
				),
				'description' => esc_html__( 'برای SVGهای خطی Stroke و برای SVGهای توپر Fill را انتخاب کنید.', 'tadris' ),
			)
		);

		$this->register_header_commerce_trigger_style_controls();

		$this->add_control(
			'button_label',
			array(
				'label'       => esc_html__( 'متن دکمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'سبد خرید', 'tadris' ),
				'label_block' => true,
				'condition'   => array(
					'trigger_style' => 'default',
				),
			)
		);

		$this->add_control(
			'panel_title',
			array(
				'label'       => esc_html__( 'عنوان پنل', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'سبد خرید شما', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'panel_side',
			array(
				'label'   => esc_html__( 'جهت باز شدن پنل', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => $this->get_offcanvas_side_options(),
			)
		);

		$this->add_control(
			'show_count',
			array(
				'label'        => esc_html__( 'نمایش تعداد محصولات', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_total',
			array(
				'label'        => esc_html__( 'نمایش جمع مبلغ در سربرگ', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => esc_html__( 'دکمه سبد خرید', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->register_header_commerce_icon_trigger_style_controls();

		$this->add_control(
			'button_background',
			array(
				'label'       => esc_html__( 'رنگ پس‌زمینه', 'tadris' ),
				'description' => esc_html__( 'در صورت خالی بودن، رنگ اصلی کمرنگ پنل پوسته استفاده می‌شود.', 'tadris' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'{{WRAPPER}} .webmz-hcart__trigger' => 'background-color: {{VALUE}};',
				),
				'condition'   => array(
					'trigger_style' => 'default',
				),
			)
		);

		$this->add_control(
			'button_background_hover',
			array(
				'label'     => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-hcart__trigger:hover, {{WRAPPER}} .webmz-hcart.is-open .webmz-hcart__trigger' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'trigger_style' => 'default',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-hcart__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-hcart__trigger' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'trigger_style' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 12, 'max' => 48 ) ),
				'default'    => array( 'size' => 20, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-hcart__icon, {{WRAPPER}} .webmz-hcart__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_size',
			array(
				'label'      => esc_html__( 'حداقل اندازه دکمه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 35, 'max' => 110 ) ),
				'default'    => array( 'size' => 54, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-hcart__trigger' => 'min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'trigger_style' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 12, 'right' => 14, 'bottom' => 12, 'left' => 14, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-hcart__trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'trigger_style' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-hcart__trigger' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'trigger_style' => 'default',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'button_typography',
				'selector'  => '{{WRAPPER}} .webmz-hcart__trigger',
				'condition' => array(
					'trigger_style' => 'default',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'button_border',
				'selector'  => '{{WRAPPER}} .webmz-hcart__trigger',
				'condition' => array(
					'trigger_style' => 'default',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_panel_style',
			array(
				'label' => esc_html__( 'پنل Mini Cart', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'panel_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه پنل', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-offcanvas__panel' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'panel_width',
			array(
				'label'      => esc_html__( 'عرض پنل', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 250, 'max' => 520 ) ),
				'default'    => array( 'size' => 360, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-offcanvas__panel' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'panel_radius',
			array(
				'label'      => esc_html__( 'گردی پنل', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-offcanvas__panel' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array( 'name' => 'panel_border', 'selector' => '{{WRAPPER}} .webmz-offcanvas__panel' )
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array( 'name' => 'panel_shadow', 'selector' => '{{WRAPPER}} .webmz-offcanvas__panel' )
		);

		$this->add_control(
			'cart_button_heading',
			array(
				'label'     => esc_html__( 'دکمه مشاهده سبد خرید', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'view_cart_background',
			array(
				'label'       => esc_html__( 'رنگ پس‌زمینه', 'tadris' ),
				'description' => esc_html__( 'در صورت خالی بودن، رنگ اصلی کمرنگ پنل پوسته استفاده می‌شود.', 'tadris' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'{{WRAPPER}} .webmz-hcart__action--cart' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'view_cart_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-hcart__action--cart' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'view_cart_background_hover',
			array(
				'label'     => esc_html__( 'رنگ پس‌زمینه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-hcart__action--cart:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'view_cart_color_hover',
			array(
				'label'     => esc_html__( 'رنگ متن هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-hcart__action--cart:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'checkout_button_heading',
			array(
				'label'     => esc_html__( 'دکمه تسویه حساب', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'checkout_background',
			array(
				'label'       => esc_html__( 'رنگ پس‌زمینه', 'tadris' ),
				'description' => esc_html__( 'در صورت خالی بودن، رنگ اصلی پنل پوسته استفاده می‌شود.', 'tadris' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'{{WRAPPER}} .webmz-hcart__action--checkout' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'checkout_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-hcart__action--checkout' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'checkout_background_hover',
			array(
				'label'     => esc_html__( 'رنگ پس‌زمینه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-hcart__action--checkout:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'checkout_color_hover',
			array(
				'label'     => esc_html__( 'رنگ متن هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-hcart__action--checkout:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => 'panel_typography', 'selector' => '{{WRAPPER}} .webmz-offcanvas__panel' )
		);

		$this->end_controls_section();
	}

	/** Render off-canvas panel title. */
	protected function render_offcanvas_title() {
		$settings = $this->get_settings_for_display();
		$cart     = \WC()->cart;
		$total    = \webmz_header_cart_price_text( $cart->get_cart_subtotal() );
		?>
		<div class="webmz-offcanvas__head-main">
			<h3 class="webmz-offcanvas__title"><?php echo esc_html( $settings['panel_title'] ); ?></h3>
			<?php if ( 'yes' === $settings['show_total'] && '' !== $total ) : ?>
				<span class="webmz-hcart__header-total" data-webmz-hcart-total><?php echo esc_html( $total ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	}

	/** Render the widget. */
	protected function render() {
		if ( ! function_exists( '\webmz_header_cart_is_ready' ) || ! \webmz_header_cart_is_ready() ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		$cart     = \WC()->cart;
		$count    = absint( $cart->get_cart_contents_count() );
		$total    = \webmz_header_cart_price_text( $cart->get_cart_subtotal() );
		$is_edit_mode = $this->is_elementor_edit_mode();
		$show_label   = 'default' === $this->get_header_commerce_trigger_style( $settings ) && '' !== trim( (string) $settings['button_label'] );
		$root_class   = $this->get_header_commerce_root_classes(
			$settings,
			'webmz-hcart' . ( $cart->is_empty() ? ' is-empty' : '' )
		);
		?>
		<div class="<?php echo esc_attr( $root_class ); ?>" data-webmz-header-cart data-webmz-offcanvas-root>
			<button type="button" class="webmz-hcart__trigger" data-webmz-offcanvas-trigger aria-haspopup="dialog" aria-expanded="false" aria-label="<?php echo esc_attr( '' !== trim( (string) $settings['button_label'] ) ? $settings['button_label'] : esc_html__( 'سبد خرید', 'tadris' ) ); ?>">
				<span class="webmz-hcart__icon <?php echo esc_attr( webmz_get_icon_color_mode_class( $settings ) ); ?>"><?php Icons_Manager::render_icon( $settings['cart_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
				<?php if ( $show_label ) : ?>
					<span class="webmz-hcart__label"><?php echo esc_html( $settings['button_label'] ); ?></span>
				<?php endif; ?>
				<?php if ( 'yes' === $settings['show_count'] ) : ?>
					<span class="webmz-hcart__badge" data-webmz-hcart-count><?php echo esc_html( $count ); ?></span>
				<?php endif; ?>
			</button>

			<?php if ( ! $is_edit_mode ) : ?>
				<div class="webmz-hcart-panel">
					<?php $this->render_offcanvas_shell_open( $settings['panel_side'] ); ?>
					<div class="webmz-hcart__body" data-webmz-hcart-body>
						<?php echo \webmz_get_header_cart_content_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<?php if ( 'yes' !== $settings['show_total'] && '' !== $total ) : ?>
						<span class="screen-reader-text" data-webmz-hcart-total><?php echo esc_html( $total ); ?></span>
					<?php endif; ?>
					<?php $this->render_offcanvas_shell_close(); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
