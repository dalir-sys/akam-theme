<?php
/**
 * WebMZ OTP login Elementor widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

class Tadris_OTP_Login_Widget extends Widget_Base {
	public function get_name() { return 'webmz-otp-login'; }
	public function get_title() { return esc_html__( 'فرم ورود پیامکی آکام', 'tadris' ); }
	public function get_icon() { return 'eicon-lock-user'; }
	public function get_categories() { return array( webmz_elementor_category_slug() ); }
	public function get_keywords() { return array( 'otp', 'sms', 'login', 'register', 'ورود', 'ثبت نام', 'پیامک' ); }
	public function get_style_depends() { return array( 'webmz-otp-auth' ); }
	public function get_script_depends() { return array( 'webmz-otp-auth' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content_section', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'redirect_url', array( 'label' => esc_html__( 'آدرس انتقال بعد از ورود', 'tadris' ), 'type' => Controls_Manager::URL, 'placeholder' => home_url( '/' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style_section', array( 'label' => esc_html__( 'استایل', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'box_bg', array( 'label' => esc_html__( 'پس‌زمینه', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-otp-auth' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'box_padding', array( 'label' => esc_html__( 'فاصله داخلی', 'tadris' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', 'rem' ), 'selectors' => array( '{{WRAPPER}} .webmz-otp-auth' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'box_radius', array( 'label' => esc_html__( 'گردی گوشه‌ها', 'tadris' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', '%', 'rem' ), 'selectors' => array( '{{WRAPPER}} .webmz-otp-auth' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'box_border', 'selector' => '{{WRAPPER}} .webmz-otp-auth' ) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array( 'name' => 'box_shadow', 'selector' => '{{WRAPPER}} .webmz-otp-auth' ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'title_typo', 'selector' => '{{WRAPPER}} .webmz-otp-auth__head strong' ) );
		$this->add_control( 'button_bg', array( 'label' => esc_html__( 'رنگ دکمه', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-otp-button' => 'background-color: {{VALUE}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$url = ! empty( $settings['redirect_url']['url'] ) ? $settings['redirect_url']['url'] : '';
		if ( function_exists( 'webmz_otp_render_form' ) ) {
			echo webmz_otp_render_form( array( 'mode' => 'widget', 'redirect' => $url ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}
