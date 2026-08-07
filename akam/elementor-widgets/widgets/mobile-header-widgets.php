<?php
/**
 * Mobile header off-canvas Elementor widgets.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Shared helpers for mobile header widgets.
 */
trait Mobile_Header_Widget_Trait {

	/**
	 * Off-canvas side control options.
	 *
	 * @return array<string,string>
	 */
	protected function get_offcanvas_side_options() {
		return array(
			'right' => esc_html__( 'راست', 'tadris' ),
			'left'  => esc_html__( 'چپ', 'tadris' ),
		);
	}

	/**
	 * WordPress menu options.
	 *
	 * @return array<int|string,string>
	 */
	protected function get_menu_options() {
		$options = array( '' => esc_html__( 'یک منو انتخاب کنید', 'tadris' ) );

		foreach ( wp_get_nav_menus() as $menu ) {
			$options[ absint( $menu->term_id ) ] = $menu->name;
		}

		return $options;
	}

	/**
	 * Render off-canvas shell close button and overlay.
	 *
	 * @param string $side Panel side.
	 * @return void
	 */
	protected function render_offcanvas_shell_open( $side, $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'show_head' => true,
			)
		);
		$side = function_exists( 'webmz_mobile_offcanvas_side' ) ? webmz_mobile_offcanvas_side( $side ) : 'right';
		?>
		<div class="webmz-offcanvas webmz-offcanvas--boot webmz-offcanvas--el<?php echo esc_attr( $this->get_id() ); ?><?php echo $args['show_head'] ? '' : ' webmz-offcanvas--no-head'; ?>" data-webmz-offcanvas-panel role="dialog" aria-modal="false" aria-hidden="true" inert>
			<div class="webmz-offcanvas__overlay" data-webmz-offcanvas-close></div>
			<div class="webmz-offcanvas__panel webmz-offcanvas__panel--<?php echo esc_attr( $side ); ?>">
				<?php if ( $args['show_head'] ) : ?>
					<div class="webmz-offcanvas__head">
						<?php $this->render_offcanvas_title(); ?>
						<button type="button" class="webmz-offcanvas__close" data-webmz-offcanvas-close aria-label="<?php esc_attr_e( 'بستن', 'tadris' ); ?>">
							<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18"/></svg>
						</button>
					</div>
				<?php else : ?>
					<button type="button" class="webmz-offcanvas__close webmz-offcanvas__close--floating" data-webmz-offcanvas-close aria-label="<?php esc_attr_e( 'بستن', 'tadris' ); ?>">
						<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18"/></svg>
					</button>
				<?php endif; ?>
				<div class="webmz-offcanvas__body">
		<?php
	}

	/**
	 * Render panel title placeholder for child classes.
	 *
	 * @return void
	 */
	protected function render_offcanvas_title() {
		echo '<h3 class="webmz-offcanvas__title"></h3>';
	}

	/**
	 * Close off-canvas shell markup.
	 *
	 * @return void
	 */
	protected function render_offcanvas_shell_close() {
		?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Register shared trigger button style controls.
	 *
	 * @param string $trigger_selector CSS selector for trigger element.
	 * @return void
	 */
	protected function register_mobile_trigger_style_controls( $trigger_selector = '.webmz-mtrigger' ) {
		$this->start_controls_section(
			'section_trigger_style',
			array(
				'label' => esc_html__( 'دکمه آیکن', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'trigger_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $trigger_selector => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'trigger_background_hover',
			array(
				'label'     => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $trigger_selector . ':hover, {{WRAPPER}} ' . $trigger_selector . ':focus-visible' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'trigger_icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $trigger_selector . ' .webmz-mtrigger__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} ' . $trigger_selector . ' .webmz-mmenu-trigger__bars span' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'trigger_icon_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 14, 'max' => 40 ) ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $trigger_selector . ' .webmz-mtrigger__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $trigger_selector . ' .webmz-mmenu-trigger__bars' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'trigger_min_size',
			array(
				'label'      => esc_html__( 'اندازه دکمه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 36, 'max' => 72 ) ),
				'default'    => array(
					'unit' => 'px',
					'size' => 40,
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $trigger_selector => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'trigger_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $trigger_selector => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'badge_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه نشانگر', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-mtrigger__badge' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'trigger_shadow',
				'selector' => '{{WRAPPER}} ' . $trigger_selector,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register shared off-canvas panel style controls.
	 *
	 * @return void
	 */
	protected function register_mobile_panel_style_controls() {
		$this->start_controls_section(
			'section_panel_style',
			array(
				'label' => esc_html__( 'پنل کشویی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'panel_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه پنل', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.webmz-offcanvas--el{{ID}} .webmz-offcanvas__panel' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'panel_overlay',
			array(
				'label'     => esc_html__( 'رنگ ماسک پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.webmz-offcanvas--el{{ID}} .webmz-offcanvas__overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'panel_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان پنل', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.webmz-offcanvas--el{{ID}} .webmz-offcanvas__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'panel_shadow',
				'selector' => '.webmz-offcanvas--el{{ID}} .webmz-offcanvas__panel',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'panel_border',
				'selector' => '.webmz-offcanvas--el{{ID}} .webmz-offcanvas__panel',
			)
		);

		$this->end_controls_section();
	}
}

/**
 * Mobile AJAX mini-cart off-canvas widget.
 */
class Mobile_Mini_Cart_Widget extends Widget_Base {
	use Mobile_Header_Widget_Trait;

	public function get_name() {
		return 'webmz-mobile-mini-cart';
	}

	public function get_title() {
		return esc_html__( 'سبد خرید موبایلی', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-cart';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'cart', 'mobile', 'mini cart', 'offcanvas', 'سبد خرید موبایلی' );
	}

	public function get_style_depends() {
		return array( 'webmz-header-commerce', 'webmz-mobile-offcanvas' );
	}

	public function get_script_depends() {
		return array( 'webmz-mobile-offcanvas', 'webmz-header-commerce', 'wc-add-to-cart' );
	}

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

	protected function register_controls() {
		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'سبد خرید موبایلی', 'tadris' ) ) );

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
			'panel_title',
			array(
				'label'       => esc_html__( 'عنوان پنل', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'سبد خرید', 'tadris' ),
				'label_block' => true,
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

		$this->end_controls_section();

		$this->register_mobile_trigger_style_controls( '.webmz-mcart__trigger' );
		$this->register_mobile_panel_style_controls();
	}

	protected function render_offcanvas_title() {
		$settings = $this->get_settings_for_display();
		?>
		<h3 class="webmz-offcanvas__title"><?php echo esc_html( $settings['panel_title'] ); ?></h3>
		<?php
	}

	protected function render() {
		if ( ! function_exists( 'webmz_header_cart_is_ready' ) || ! webmz_header_cart_is_ready() ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		$cart     = WC()->cart;
		$count    = function_exists( 'webmz_mobile_header_cart_count' ) ? webmz_mobile_header_cart_count() : absint( $cart->get_cart_contents_count() );
		$total    = function_exists( 'webmz_header_cart_price_text' ) ? webmz_header_cart_price_text( $cart->get_cart_subtotal() ) : '';
		$is_edit_mode = $this->is_elementor_edit_mode();
		?>
		<?php
		$cart_trigger_label = '' !== trim( (string) $settings['panel_title'] )
			? $settings['panel_title']
			: __( 'سبد خرید', 'tadris' );
		if ( $count > 0 ) {
			$cart_trigger_label = sprintf(
				/* translators: 1: cart label, 2: item count */
				__( '%1$s، %2$d مورد', 'tadris' ),
				$cart_trigger_label,
				$count
			);
		}
		?>
		<div class="webmz-mcart<?php echo $cart->is_empty() ? ' is-empty' : ''; ?>" data-webmz-mobile-cart data-webmz-offcanvas-root data-webmz-header-cart>
			<button type="button" class="webmz-mtrigger webmz-mcart__trigger" data-webmz-offcanvas-trigger aria-haspopup="dialog" aria-label="<?php echo esc_attr( $cart_trigger_label ); ?>">
				<span class="webmz-mtrigger__icon"><?php Icons_Manager::render_icon( $settings['cart_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
				<?php if ( 'yes' === $settings['show_count'] ) : ?>
					<span class="webmz-mtrigger__badge" data-webmz-hcart-count><?php echo esc_html( $count ); ?></span>
				<?php endif; ?>
			</button>

			<?php if ( ! $is_edit_mode ) : ?>
				<div class="webmz-mcart-panel">
					<?php $this->render_offcanvas_shell_open( $settings['panel_side'] ); ?>
					<div class="webmz-hcart__body" data-webmz-hcart-body>
						<?php echo webmz_get_header_cart_content_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<?php if ( '' !== $total ) : ?>
						<span class="screen-reader-text" data-webmz-hcart-total><?php echo esc_html( $total ); ?></span>
					<?php endif; ?>
					<?php $this->render_offcanvas_shell_close(); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}

/**
 * Mobile account off-canvas / OTP popup widget.
 */
class Mobile_Account_Widget extends Widget_Base {
	use Mobile_Header_Widget_Trait;

	public function get_name() {
		return 'webmz-mobile-account';
	}

	public function get_title() {
		return esc_html__( 'حساب کاربری موبایلی', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'account', 'mobile', 'login', 'offcanvas', 'حساب کاربری موبایلی' );
	}

	public function get_style_depends() {
		return array( 'webmz-mobile-offcanvas' );
	}

	public function get_script_depends() {
		return array( 'webmz-mobile-offcanvas' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'حساب کاربری موبایلی', 'tadris' ) ) );

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
			'panel_title',
			array(
				'label'       => esc_html__( 'عنوان پنل', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'حساب کاربری', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'guest_label',
			array(
				'label'       => esc_html__( 'متن کاربر مهمان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'ورود / ثبت نام', 'tadris' ),
				'label_block' => true,
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

		$this->end_controls_section();

		$this->register_mobile_trigger_style_controls( '.webmz-maccount__trigger' );
		$this->register_mobile_panel_style_controls();
	}

	protected function render_offcanvas_title() {
		$settings = $this->get_settings_for_display();
		?>
		<h3 class="webmz-offcanvas__title"><?php echo esc_html( $settings['panel_title'] ); ?></h3>
		<?php
	}

	protected function render() {
		$settings     = $this->get_settings_for_display();
		$is_logged_in = is_user_logged_in();
		$account_url  = function_exists( 'webmz_mobile_account_url' ) ? webmz_mobile_account_url() : home_url( '/' );
		$uses_otp     = function_exists( 'webmz_mobile_account_uses_otp_popup' ) && webmz_mobile_account_uses_otp_popup();
		$trigger_label = $is_logged_in
			? ( '' !== trim( (string) $settings['panel_title'] ) ? $settings['panel_title'] : __( 'حساب کاربری', 'tadris' ) )
			: ( '' !== trim( (string) $settings['guest_label'] ) ? $settings['guest_label'] : __( 'ورود / ثبت نام', 'tadris' ) );
		?>
		<div class="webmz-maccount" data-webmz-mobile-account data-webmz-offcanvas-root>
			<?php if ( $is_logged_in ) : ?>
				<button type="button" class="webmz-mtrigger webmz-maccount__trigger" data-webmz-offcanvas-trigger aria-haspopup="dialog" aria-label="<?php echo esc_attr( $trigger_label ); ?>">
					<span class="webmz-mtrigger__icon"><?php Icons_Manager::render_icon( $settings['account_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
				</button>

				<div class="webmz-maccount-panel">
					<?php $this->render_offcanvas_shell_open( $settings['panel_side'] ); ?>
					<?php
					if ( function_exists( 'webmz_mobile_render_account_menu' ) ) {
						webmz_mobile_render_account_menu( $settings['logout_label'] );
					}
					?>
					<?php $this->render_offcanvas_shell_close(); ?>
				</div>
			<?php else : ?>
				<a
					class="webmz-mtrigger webmz-maccount__trigger"
					href="<?php echo esc_url( $uses_otp ? add_query_arg( 'webmz-login', '1', $account_url ) : $account_url ); ?>"
					aria-label="<?php echo esc_attr( $trigger_label ); ?>"
					<?php echo $uses_otp ? 'data-webmz-maccount-open-login data-webmz-otp-open' : ''; ?>
				>
					<span class="webmz-mtrigger__icon"><?php Icons_Manager::render_icon( $settings['account_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
					<span class="screen-reader-text"><?php echo esc_html( $trigger_label ); ?></span>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}

/**
 * Mobile Webmasters menu off-canvas widget.
 */
class Mobile_Menu_Widget extends Widget_Base {
	use Mobile_Header_Widget_Trait;

	public function get_name() {
		return 'webmz-mobile-menu';
	}

	public function get_title() {
		return esc_html__( 'منو موبایلی آکام', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'menu', 'mobile', 'hamburger', 'offcanvas', 'منو موبایلی' );
	}

	public function get_style_depends() {
		return array( 'webmz-mobile-offcanvas', 'webmz-navigation-menu' );
	}

	public function get_script_depends() {
		return array( 'webmz-mobile-offcanvas' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_panel', array( 'label' => esc_html__( 'پنل منو', 'tadris' ) ) );

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
			'trigger_type',
			array(
				'label'   => esc_html__( 'نوع آیکن منو', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bars',
				'options' => array(
					'bars' => esc_html__( 'همبرگری (سه خط)', 'tadris' ),
					'icon' => esc_html__( 'آیکن دلخواه', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'menu_icon',
			array(
				'label'     => esc_html__( 'آیکن منو', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-bars',
					'library' => 'fa-solid',
				),
				'condition' => array( 'trigger_type' => 'icon' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_logo', array( 'label' => esc_html__( 'لوگو', 'tadris' ) ) );

		$this->add_control(
			'logo_image',
			array(
				'label' => esc_html__( 'تصویر لوگو', 'tadris' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'logo_alt',
			array(
				'label'       => esc_html__( 'متن جایگزین لوگو', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_search', array( 'label' => esc_html__( 'جستجوی موبایلی', 'tadris' ) ) );

		$this->add_control(
			'show_search',
			array(
				'label'        => esc_html__( 'نمایش جستجو', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'search_placeholder',
			array(
				'label'       => esc_html__( 'متن placeholder', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'جستجو کنید', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_search' => 'yes' ),
			)
		);

		$this->add_control(
			'products_limit',
			array(
				'label'     => esc_html__( 'تعداد محصولات', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4,
				'min'       => 1,
				'max'       => 8,
				'condition' => array( 'show_search' => 'yes' ),
			)
		);

		$this->add_control(
			'posts_limit',
			array(
				'label'     => esc_html__( 'تعداد مقالات', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4,
				'min'       => 1,
				'max'       => 8,
				'condition' => array( 'show_search' => 'yes' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_menu', array( 'label' => esc_html__( 'منو', 'tadris' ) ) );

		$menus = $this->get_menu_options();
		$keys  = array_keys( $menus );
		$first = count( $keys ) > 1 ? (string) $keys[1] : '';

		$this->add_control(
			'menu_id',
			array(
				'label'       => esc_html__( 'انتخاب منو', 'tadris' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $menus,
				'default'     => $first,
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_quick_links', array( 'label' => esc_html__( 'لینک‌های سریع', 'tadris' ) ) );

		$this->add_control(
			'account_label',
			array(
				'label'       => esc_html__( 'عنوان حساب کاربری', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'حساب کاربری', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'account_icon',
			array(
				'label'   => esc_html__( 'آیکون حساب کاربری', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-user',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'cart_label',
			array(
				'label'       => esc_html__( 'عنوان سبد خرید', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'سبد خرید', 'tadris' ),
				'label_block' => true,
			)
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
			'show_cart_count',
			array(
				'label'        => esc_html__( 'نمایش تعداد سبد خرید', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'section_contact', array( 'label' => esc_html__( 'ارتباط با ما', 'tadris' ) ) );

		$this->add_control(
			'show_contact',
			array(
				'label'        => esc_html__( 'نمایش باکس تماس', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'number_prefix',
			array(
				'label'     => esc_html__( 'پیش‌شماره', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '021 -',
				'condition' => array( 'show_contact' => 'yes' ),
			)
		);

		$this->add_control(
			'phone_number',
			array(
				'label'     => esc_html__( 'شماره تماس', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '0000000',
				'condition' => array( 'show_contact' => 'yes' ),
			)
		);

		$this->add_control(
			'caption',
			array(
				'label'       => esc_html__( 'عنوان تماس', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'ارتباط با برتر وردپرس', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_contact' => 'yes' ),
			)
		);

		$this->add_control(
			'contact_link',
			array(
				'label'     => esc_html__( 'لینک تماس', 'tadris' ),
				'type'      => Controls_Manager::URL,
				'default'   => array( 'url' => 'tel:0210000000' ),
				'condition' => array( 'show_contact' => 'yes' ),
			)
		);

		$this->add_control(
			'contact_icon',
			array(
				'label'     => esc_html__( 'آیکون تماس', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-phone-volume',
					'library' => 'fa-solid',
				),
				'condition' => array( 'show_contact' => 'yes' ),
			)
		);

		$this->end_controls_section();

		$this->register_mobile_trigger_style_controls( '.webmz-mmenu-trigger' );
		$this->register_mobile_panel_style_controls();
	}

	/**
	 * Render RTL-friendly chevron for quick links.
	 *
	 * @return void
	 */
	protected function render_quick_chevron() {
		?>
		<span class="webmz-mmenu-panel__quick-arrow" aria-hidden="true">
			<svg viewBox="0 0 24 24" width="16" height="16" focusable="false"><path fill="currentColor" d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6z"/></svg>
		</span>
		<?php
	}

	/**
	 * Render contact icon markup.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_contact_icon( $settings ) {
		if ( ! empty( $settings['contact_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['contact_icon'], array( 'aria-hidden' => 'true' ) );
			return;
		}
		?>
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
			<path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"/>
		</svg>
		<?php
	}

	protected function render_offcanvas_title() {
		echo '<span class="screen-reader-text">' . esc_html__( 'منوی موبایل', 'tadris' ) . '</span>';
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$menu_id     = absint( $settings['menu_id'] );
		$logo_url    = ! empty( $settings['logo_image']['url'] ) ? $settings['logo_image']['url'] : '';
		$logo_alt    = '' !== trim( (string) $settings['logo_alt'] ) ? $settings['logo_alt'] : get_bloginfo( 'name' );
		$cart_count  = function_exists( 'webmz_mobile_header_cart_count' ) ? webmz_mobile_header_cart_count() : 0;
		$account_url = function_exists( 'webmz_mobile_account_url' ) ? webmz_mobile_account_url() : home_url( '/' );
		$cart_url    = function_exists( 'webmz_mobile_cart_url' ) ? webmz_mobile_cart_url() : home_url( '/' );
		$uses_otp    = function_exists( 'webmz_mobile_account_uses_otp_popup' ) && webmz_mobile_account_uses_otp_popup();
		$is_logged_in = is_user_logged_in();
		?>
		<div class="webmz-mmenu" data-webmz-offcanvas-root>
			<button type="button" class="webmz-mtrigger webmz-mmenu-trigger" data-webmz-offcanvas-trigger aria-haspopup="dialog" aria-label="<?php esc_attr_e( 'باز کردن منو', 'tadris' ); ?>">
				<?php if ( 'icon' === $settings['trigger_type'] ) : ?>
					<span class="webmz-mtrigger__icon"><?php Icons_Manager::render_icon( $settings['menu_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
				<?php else : ?>
					<span class="webmz-mmenu-trigger__bars" aria-hidden="true">
						<span></span><span></span><span></span>
					</span>
				<?php endif; ?>
			</button>

			<div class="webmz-mmenu-panel">
				<?php $this->render_offcanvas_shell_open( $settings['panel_side'], array( 'show_head' => false ) ); ?>

				<?php if ( $logo_url ) : ?>
					<a class="webmz-mmenu-panel__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $logo_alt ); ?>" loading="lazy" decoding="async" />
					</a>
				<?php endif; ?>

				<?php if ( 'yes' === $settings['show_search'] ) : ?>
					<div
						class="webmz-msearch"
						data-webmz-msearch
						data-products-limit="<?php echo esc_attr( absint( $settings['products_limit'] ) ); ?>"
						data-posts-limit="<?php echo esc_attr( absint( $settings['posts_limit'] ) ); ?>"
					>
						<form class="webmz-msearch__form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
							<label class="screen-reader-text" for="webmz-msearch-<?php echo esc_attr( $this->get_id() ); ?>"><?php esc_html_e( 'جستجو', 'tadris' ); ?></label>
							<input
								id="webmz-msearch-<?php echo esc_attr( $this->get_id() ); ?>"
								class="webmz-msearch__input"
								type="search"
								name="s"
								placeholder="<?php echo esc_attr( $settings['search_placeholder'] ); ?>"
								autocomplete="off"
							/>
							<button type="submit" class="webmz-msearch__submit" aria-label="<?php esc_attr_e( 'جستجو', 'tadris' ); ?>">
								<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
							</button>
						</form>

						<div class="webmz-msearch__box" data-webmz-msearch-box hidden>
							<button type="button" class="webmz-msearch__close" data-webmz-msearch-close aria-label="<?php esc_attr_e( 'بستن نتایج', 'tadris' ); ?>">
								<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
							</button>
							<div class="webmz-msearch__loading" data-webmz-msearch-loading hidden>
								<span class="webmz-msearch__spinner" aria-hidden="true"></span>
								<span class="webmz-msearch__loading-text"><?php esc_html_e( 'در حال جستجو...', 'tadris' ); ?></span>
							</div>
							<div class="webmz-msearch__section" data-webmz-msearch-results hidden>
								<p class="webmz-msearch__heading"><?php esc_html_e( 'محصولات مرتبط', 'tadris' ); ?></p>
								<ul class="webmz-msearch__list" data-webmz-msearch-products></ul>
								<p class="webmz-msearch__heading"><?php esc_html_e( 'مقالات مرتبط', 'tadris' ); ?></p>
								<ul class="webmz-msearch__list" data-webmz-msearch-posts></ul>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $menu_id ) : ?>
					<div class="webmz-mmenu-panel__nav">
						<?php
						wp_nav_menu(
							array(
								'menu'                 => $menu_id,
								'container'            => 'nav',
								'container_class'      => 'webmz-mmenu-panel__nav-inner',
								'container_aria_label' => __( 'منوی موبایل', 'tadris' ),
								'menu_class'           => 'webmz-nav webmz-nav--vertical',
								'fallback_cb'          => false,
								'depth'                => 3,
								'walker'               => new Navigation_Menu_Walker(),
								'webmz_show_indicator' => true,
							)
						);
						?>
					</div>
				<?php endif; ?>

				<div class="webmz-mmenu-panel__quick">
					<?php if ( $is_logged_in ) : ?>
						<a class="webmz-mmenu-panel__quick-link" href="<?php echo esc_url( $account_url ); ?>" data-webmz-open-mobile-account>
							<span class="webmz-mmenu-panel__quick-main">
								<span class="webmz-mmenu-panel__quick-icon"><?php Icons_Manager::render_icon( $settings['account_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
								<span><?php echo esc_html( $settings['account_label'] ); ?></span>
							</span>
							<?php $this->render_quick_chevron(); ?>
						</a>
					<?php else : ?>
						<a
							class="webmz-mmenu-panel__quick-link"
							href="<?php echo esc_url( $uses_otp ? add_query_arg( 'webmz-login', '1', $account_url ) : $account_url ); ?>"
							<?php echo $uses_otp ? 'data-webmz-maccount-open-login data-webmz-otp-open' : ''; ?>
						>
							<span class="webmz-mmenu-panel__quick-main">
								<span class="webmz-mmenu-panel__quick-icon"><?php Icons_Manager::render_icon( $settings['account_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
								<span><?php echo esc_html( $settings['account_label'] ); ?></span>
							</span>
							<?php $this->render_quick_chevron(); ?>
						</a>
					<?php endif; ?>

					<a class="webmz-mmenu-panel__quick-link" href="<?php echo esc_url( $cart_url ); ?>" data-webmz-open-mobile-cart>
						<span class="webmz-mmenu-panel__quick-main">
							<span class="webmz-mmenu-panel__quick-icon"><?php Icons_Manager::render_icon( $settings['cart_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
							<span><?php echo esc_html( $settings['cart_label'] ); ?></span>
							<?php if ( 'yes' === $settings['show_cart_count'] ) : ?>
								<span class="webmz-mtrigger__badge webmz-mtrigger__badge--inline" data-webmz-hcart-count><?php echo esc_html( $cart_count ); ?></span>
							<?php endif; ?>
						</span>
						<?php $this->render_quick_chevron(); ?>
					</a>
				</div>

				<?php if ( 'yes' === $settings['show_contact'] ) : ?>
					<div class="webmz-mmenu-panel__contact">
						<?php
						$link = $settings['contact_link'];
						$url  = ! empty( $link['url'] ) ? $link['url'] : 'tel:0210000000';
						?>
						<a class="tadris-contact-box-1" href="<?php echo esc_url( $url ); ?>"<?php echo ! empty( $link['is_external'] ) ? ' target="_blank"' : ''; ?><?php echo ! empty( $link['nofollow'] ) ? ' rel="nofollow"' : ''; ?>>
							<div class="tadris-contact-icon"><?php $this->render_contact_icon( $settings ); ?></div>
							<div class="tadris-contact-text">
								<div class="tadris-contact-number"><?php echo esc_html( $settings['number_prefix'] ); ?> <strong><?php echo esc_html( $settings['phone_number'] ); ?></strong></div>
								<span><?php echo esc_html( $settings['caption'] ); ?></span>
							</div>
						</a>
					</div>
				<?php endif; ?>

				<?php $this->render_offcanvas_shell_close(); ?>
			</div>
		</div>
		<?php
	}
}
