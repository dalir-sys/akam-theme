<?php
/**
 * Instagram-style stories display widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Story box carousel widget.
 */
class Stories_Widget extends Widget_Base {

	public function get_name() {
		return 'webmz-stories';
	}

	public function get_title() {
		return esc_html__( 'نمایش استوری‌ها', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-instagram-post';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-stories' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-stories' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$options = webmz_get_story_box_options();

		$this->add_control(
			'story_box_id',
			array(
				'label'       => esc_html__( 'باکس استوری', 'tadris' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $options ? $options : array( '' => esc_html__( 'باکس استوری منتشر شده‌ای وجود ندارد', 'tadris' ) ),
				'default'     => $options ? (string) array_key_first( $options ) : '',
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style',
			array(
				'label' => esc_html__( 'استایل', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'item_width',
			array(
				'label'      => esc_html__( 'عرض آیتم', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 60,
						'max' => 120,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 82,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-stories__item' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ring_size',
			array(
				'label'      => esc_html__( 'اندازه دایره', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 48,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 72,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-stories__ring' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-stories__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$box_id   = isset( $settings['story_box_id'] ) ? absint( $settings['story_box_id'] ) : 0;

		if ( ! $box_id ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="webmz-stories webmz-stories--placeholder">' . esc_html__( 'یک باکس استوری انتخاب کنید.', 'tadris' ) . '</div>';
			}
			return;
		}

		$lazy = ! \Elementor\Plugin::$instance->editor->is_edit_mode();

		$output = webmz_render_story_box(
			$box_id,
			array(
				'class' => 'webmz-stories--elementor',
				'lazy'  => $lazy,
			)
		);

		if ( '' === $output && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			echo '<div class="webmz-stories webmz-stories--placeholder">' . esc_html__( 'این باکس استوری محتوای قابل نمایشی ندارد.', 'tadris' ) . '</div>';
			return;
		}

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
