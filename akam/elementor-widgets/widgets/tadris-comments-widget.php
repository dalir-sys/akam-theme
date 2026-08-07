<?php
/**
 * WebMZ post comments widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * AJAX chat-style comments widget for single posts.
 */
class Tadris_Comments_Widget extends Widget_Base {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'webmz_comments_widget';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'ویجت نظرات آکام', 'tadris' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-comments';
	}

	/**
	 * Widget categories.
	 *
	 * @return array<int,string>
	 */
	public function get_categories() {
		return array( function_exists( 'webmz_elementor_dynamic_category_slug' ) ? webmz_elementor_dynamic_category_slug() : 'general' );
	}

	/**
	 * Widget keywords.
	 *
	 * @return array<int,string>
	 */
	public function get_keywords() {
		return array( 'comments', 'comment', 'ajax', 'chat', 'نظرات', 'دیدگاه', 'آکام' );
	}

	/**
	 * Style deps.
	 *
	 * @return array<int,string>
	 */
	public function get_style_depends() {
		return array( 'webmz-comments-widget' );
	}

	/**
	 * Script deps.
	 *
	 * @return array<int,string>
	 */
	public function get_script_depends() {
		return array( 'webmz-comments-widget' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'widget_title',
			array(
				'label'       => esc_html__( 'عنوان بخش', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'گفت‌وگو و دیدگاه‌ها', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_count',
			array(
				'label'        => esc_html__( 'نمایش تعداد دیدگاه‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'form_title',
			array(
				'label'       => esc_html__( 'عنوان فرم', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دیدگاه خود را بنویسید', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'textarea_placeholder',
			array(
				'label'       => esc_html__( 'متن placeholder', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'نظر، سؤال یا پاسخ خود را بنویسید...', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'submit_text',
			array(
				'label'       => esc_html__( 'متن دکمه ارسال', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'ارسال دیدگاه', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'box_style_section',
			array(
				'label' => esc_html__( 'استایل جعبه', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'box_background',
				'selector' => '{{WRAPPER}} .webmz-comments-widget',
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'      => 28,
					'right'    => 28,
					'bottom'   => 28,
					'left'     => 28,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-comments-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => 28,
					'right'    => 28,
					'bottom'   => 28,
					'left'     => 28,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-comments-widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .webmz-comments-widget',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .webmz-comments-widget',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'typography_style_section',
			array(
				'label' => esc_html__( 'تایپوگرافی و رنگ‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => array(
					'{{WRAPPER}} .webmz-comments-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .webmz-comments-title',
			)
		);

		$this->add_control(
			'accent_color',
			array(
				'label'     => esc_html__( 'رنگ اصلی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .webmz-comments-widget' => '--webmz-comments-accent: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get current post ID.
	 *
	 * @return int
	 */
	private function get_current_post_id() {
		$post_id = get_the_ID();

		if ( ! $post_id ) {
			$post = get_post();
			$post_id = $post ? $post->ID : 0;
		}

		return absint( $post_id );
	}

	/**
	 * Render widget.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$post_id  = $this->get_current_post_id();

		if ( ! function_exists( 'webmz_comments_widget_is_valid_post' ) || ! webmz_comments_widget_is_valid_post( $post_id ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="webmz-comments-editor-note">' . esc_html__( 'این ویجت برای صفحه تکی نوشته و مدرس طراحی شده است.', 'tadris' ) . '</div>';
			}
			return;
		}

		echo webmz_render_comments_widget( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$post_id,
			array(
				'widget_title'         => $settings['widget_title'],
				'show_count'           => isset( $settings['show_count'] ) && 'yes' === $settings['show_count'],
				'form_title'           => $settings['form_title'],
				'textarea_placeholder' => $settings['textarea_placeholder'],
				'submit_text'          => $settings['submit_text'],
			)
		);
	}
}
