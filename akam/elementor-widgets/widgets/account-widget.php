<?php
/**
 * Elementor customer account dropdown widget.
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
 * Header account trigger and WooCommerce My Account dropdown.
 */
class Account_Widget extends Widget_Base {
	use Header_Commerce_Widget_Trait;

	public function get_name() {
		return 'webmz-account';
	}

	public function get_title() {
		return esc_html__( 'حساب کاربری', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'account', 'login', 'woocommerce', 'user', 'حساب', 'ورود' );
	}

	public function get_style_depends() {
		return array( 'webmz-header-commerce' );
	}

	public function get_script_depends() {
		return array( 'webmz-header-commerce' );
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'account_icon',
			array(
				'label'   => esc_html__( 'آیکون حساب', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-user',
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

		$this->register_header_commerce_trigger_style_controls(
			array(
				'show_label_control' => true,
			)
		);

		$this->add_control(
			'guest_label',
			array(
				'label'       => esc_html__( 'متن کاربر مهمان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'ورود / ثبت نام', 'tadris' ),
				'label_block' => true,
				'condition'   => array(
					'trigger_style' => 'default',
					'show_label'    => 'yes',
				),
			)
		);

		$this->add_control(
			'logged_in_label',
			array(
				'label'       => esc_html__( 'متن کاربر واردشده', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'حساب کاربری', 'tadris' ),
				'label_block' => true,
				'condition'   => array(
					'trigger_style' => 'default',
					'show_label'    => 'yes',
				),
			)
		);

		$this->add_control(
			'logout_label',
			array(
				'label'       => esc_html__( 'متن دکمه خروج', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'خروج از حساب کاربری', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_arrow',
			array(
				'label'        => esc_html__( 'نمایش فلش منو', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'trigger_style' => 'default',
					'show_label'    => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => esc_html__( 'دکمه حساب', 'tadris' ),
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
					'{{WRAPPER}} .webmz-account__trigger' => 'background-color: {{VALUE}};',
				),
				'condition'   => array(
					'trigger_style' => 'default',
				),
			)
		);

		$this->add_control(
			'button_background_hover',
			array(
				'label'     => esc_html__( 'پس‌زمینه در هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-account__trigger:hover, {{WRAPPER}} .webmz-account.is-open .webmz-account__trigger' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'trigger_style' => 'default',
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-account__trigger' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .webmz-account__icon' => 'color: {{VALUE}};',
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
					'px' => array( 'min' => 10, 'max' => 48 ),
				),
				'default'    => array( 'size' => 18, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-account__icon, {{WRAPPER}} .webmz-account__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_gap',
			array(
				'label'      => esc_html__( 'فاصله اجزا', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 10, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-account__trigger' => 'gap: {{SIZE}}{{UNIT}};',
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
				'default'    => array( 'top' => 14, 'right' => 18, 'bottom' => 14, 'left' => 18, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-account__trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .webmz-account__trigger' => 'border-radius: {{SIZE}}{{UNIT}};',
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
				'selector'  => '{{WRAPPER}} .webmz-account__trigger',
				'condition' => array(
					'trigger_style' => 'default',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'button_border',
				'selector'  => '{{WRAPPER}} .webmz-account__trigger',
				'condition' => array(
					'trigger_style' => 'default',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'button_shadow',
				'selector'  => '{{WRAPPER}} .webmz-account__trigger',
				'condition' => array(
					'trigger_style' => 'default',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_dropdown_style',
			array(
				'label' => esc_html__( 'منوی بازشونده', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'dropdown_background',
			array(
				'label'     => esc_html__( 'رنگ پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-account__dropdown' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_width',
			array(
				'label'      => esc_html__( 'عرض منو', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 160, 'max' => 420 ) ),
				'default'    => array( 'size' => 265, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-account__dropdown' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_radius',
			array(
				'label'      => esc_html__( 'گردی منو', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-account__dropdown' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'dropdown_border',
				'selector' => '{{WRAPPER}} .webmz-account__dropdown',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'dropdown_shadow',
				'selector' => '{{WRAPPER}} .webmz-account__dropdown',
			)
		);

		$this->add_control(
			'item_color',
			array(
				'label'     => esc_html__( 'رنگ متن آیتم‌ها', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-account__menu a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_color_hover',
			array(
				'label'     => esc_html__( 'رنگ متن آیتم در هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-account__menu a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_background_hover',
			array(
				'label'     => esc_html__( 'پس‌زمینه هاور آیتم', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-account__menu a:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'logout_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه خروج', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff1f2',
				'selectors' => array(
					'{{WRAPPER}} .webmz-account__logout' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'logout_color',
			array(
				'label'     => esc_html__( 'رنگ متن خروج', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#dc2626',
				'selectors' => array(
					'{{WRAPPER}} .webmz-account__logout' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'logout_background_hover',
			array(
				'label'     => esc_html__( 'پس‌زمینه هاور خروج', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffe4e6',
				'selectors' => array(
					'{{WRAPPER}} .webmz-account__logout:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'logout_color_hover',
			array(
				'label'     => esc_html__( 'رنگ متن خروج در هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#b91c1c',
				'selectors' => array(
					'{{WRAPPER}} .webmz-account__logout:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی آیتم‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 11, 'right' => 12, 'bottom' => 11, 'left' => 12, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-account__menu a, {{WRAPPER}} .webmz-account__logout' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_typography',
				'selector' => '{{WRAPPER}} .webmz-account__menu a, {{WRAPPER}} .webmz-account__logout',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get the account page URL with a WordPress fallback.
	 *
	 * @return string
	 */
	private function get_account_url() {
		if ( function_exists( 'wc_get_page_permalink' ) ) {
			$url = wc_get_page_permalink( 'myaccount' );

			if ( $url ) {
				return $url;
			}
		}

		return is_user_logged_in() ? get_edit_profile_url( get_current_user_id() ) : wp_login_url( home_url( '/' ) );
	}

	/**
	 * Render account menu content for signed-in customers.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_dropdown( $settings ) {
		if ( function_exists( 'wc_get_account_menu_items' ) && function_exists( 'wc_get_account_endpoint_url' ) ) {
			$items = wc_get_account_menu_items();
		} else {
			$items = array(
				'dashboard' => esc_html__( 'پروفایل کاربری', 'tadris' ),
			);
		}

		?>
		<div class="webmz-account__dropdown" role="menu">
			<nav class="webmz-account__menu" aria-label="<?php esc_attr_e( 'منوی حساب کاربری', 'tadris' ); ?>">
				<?php foreach ( $items as $endpoint => $label ) : ?>
					<?php if ( 'customer-logout' === $endpoint ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<?php
					$url = function_exists( 'wc_get_account_endpoint_url' )
						? wc_get_account_endpoint_url( $endpoint )
						: get_edit_profile_url( get_current_user_id() );
					?>
					<a href="<?php echo esc_url( $url ); ?>" role="menuitem"><?php echo esc_html( wp_strip_all_tags( (string) $label ) ); ?></a>
				<?php endforeach; ?>
			</nav>

			<?php
			$logout_url = function_exists( 'wc_logout_url' )
				? wc_logout_url()
				: wp_logout_url( home_url( '/' ) );
			?>
			<a class="webmz-account__logout" href="<?php echo esc_url( $logout_url ); ?>">
				<?php echo esc_html( $settings['logout_label'] ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Render the widget.
	 *
	 * @return void
	 */
	protected function render() {
		$settings     = $this->get_settings_for_display();
		$is_logged_in = is_user_logged_in();
		$account_url  = $this->get_account_url();
		$label        = $is_logged_in ? $settings['logged_in_label'] : $settings['guest_label'];
		$show_label   = ! $this->is_header_commerce_icon_only( $settings );
		$root_class   = $this->get_header_commerce_root_classes(
			$settings,
			'webmz-account' . ( $is_logged_in ? ' is-authenticated' : ' is-guest' )
		);
		?>
		<div class="<?php echo esc_attr( $root_class ); ?>">
			<?php if ( $is_logged_in ) : ?>
				<button type="button" class="webmz-account__trigger" aria-haspopup="true" aria-expanded="false" aria-label="<?php echo esc_attr( $label ); ?>">
					<span class="webmz-account__icon <?php echo esc_attr( webmz_get_icon_color_mode_class( $settings ) ); ?>"><?php Icons_Manager::render_icon( $settings['account_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
					<?php if ( $show_label ) : ?>
						<span class="webmz-account__label"><?php echo esc_html( $label ); ?></span>
						<?php if ( 'yes' === $settings['show_arrow'] ) : ?>
							<span class="webmz-account__arrow" aria-hidden="true">⌄</span>
						<?php endif; ?>
					<?php endif; ?>
				</button>

				<?php $this->render_dropdown( $settings ); ?>
			<?php else : ?>
				<a class="webmz-account__trigger" href="<?php echo esc_url( function_exists( 'webmz_otp_is_enabled' ) && webmz_otp_is_enabled() ? add_query_arg( 'webmz-login', '1', $account_url ) : $account_url ); ?>" aria-label="<?php echo esc_attr( $label ); ?>" <?php echo ( function_exists( 'webmz_otp_is_enabled' ) && webmz_otp_is_enabled() ) ? 'data-webmz-otp-open' : ''; ?>>
					<span class="webmz-account__icon <?php echo esc_attr( webmz_get_icon_color_mode_class( $settings ) ); ?>"><?php Icons_Manager::render_icon( $settings['account_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
					<?php if ( $show_label ) : ?>
						<span class="webmz-account__label"><?php echo esc_html( $label ); ?></span>
					<?php endif; ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}
