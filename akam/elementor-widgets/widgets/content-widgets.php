<?php
/**
 * Dynamic content widgets for WebMZ layouts.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Dynamic post/page title widget.
 */
class Post_Title_Widget extends Widget_Base {
	public function get_name() { return 'webmz-post-title'; }
	public function get_title() { return esc_html__( 'عنوان داینامیک محتوا', 'tadris' ); }
	public function get_icon() { return 'eicon-post-title'; }
	public function get_categories() { return array( 'webmz-dynamic-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'tag', array(
			'label'   => esc_html__( 'تگ HTML', 'tadris' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h1',
			'options' => array(
				'h1'  => 'H1',
				'h2'  => 'H2',
				'h3'  => 'H3',
				'h4'  => 'H4',
				'h5'  => 'H5',
				'h6'  => 'H6',
				'div' => 'DIV',
			),
		) );
		$this->add_responsive_control( 'align', array(
			'label' => esc_html__( 'چینش', 'tadris' ),
			'type' => Controls_Manager::CHOOSE,
			'options' => array(
				'right' => array( 'title' => esc_html__( 'راست', 'tadris' ), 'icon' => 'eicon-text-align-right' ),
				'center' => array( 'title' => esc_html__( 'وسط', 'tadris' ), 'icon' => 'eicon-text-align-center' ),
				'left' => array( 'title' => esc_html__( 'چپ', 'tadris' ), 'icon' => 'eicon-text-align-left' ),
			),
			'selectors' => array( '{{WRAPPER}} .webmz-dynamic-title' => 'text-align: {{VALUE}};' ),
		) );
		$this->end_controls_section();

		$this->start_controls_section(
			'style_title',
			array(
				'label' => esc_html__( 'استایل عنوان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control( 'title_color', array(
			'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .webmz-dynamic-title' => 'color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'     => 'title_typography',
			'label'    => esc_html__( 'تایپوگرافی', 'tadris' ),
			'selector' => '{{WRAPPER}} .webmz-dynamic-title',
		) );

		$this->add_responsive_control( 'title_margin', array(
			'label'      => esc_html__( 'فاصله خارجی', 'tadris' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em', 'rem', '%' ),
			'selectors'  => array( '{{WRAPPER}} .webmz-dynamic-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'title_padding', array(
			'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em', 'rem', '%' ),
			'selectors'  => array( '{{WRAPPER}} .webmz-dynamic-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->add_control( 'title_bg', array(
			'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .webmz-dynamic-title' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_responsive_control( 'title_radius', array(
			'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%', 'em', 'rem' ),
			'selectors'  => array( '{{WRAPPER}} .webmz-dynamic-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->add_control( 'title_display', array(
			'label'   => esc_html__( 'نوع نمایش', 'tadris' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'block',
			'options' => array(
				'block'        => esc_html__( 'Block', 'tadris' ),
				'inline-block' => esc_html__( 'Inline Block', 'tadris' ),
			),
			'selectors' => array( '{{WRAPPER}} .webmz-dynamic-title' => 'display: {{VALUE}};' ),
		) );

		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$allowed  = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div' );
		$tag      = in_array( $settings['tag'], $allowed, true ) ? $settings['tag'] : 'h1';
		$title    = \webmz_is_layout_editing_context() ? esc_html__( 'عنوان داینامیک نوشته یا برگه', 'tadris' ) : get_the_title( \webmz_get_context_post_id() );
		printf( '<%1$s class="webmz-dynamic-title">%2$s</%1$s>', esc_attr( $tag ), esc_html( $title ) );
	}
}


/**
 * Dynamic body content widget.
 */
class Post_Content_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() { return 'webmz-post-content'; }
	public function get_title() { return esc_html__( 'محتوای داینامیک', 'tadris' ); }
	public function get_icon() { return 'eicon-post-content'; }
	public function get_categories() { return array( 'webmz-dynamic-widgets' ); }

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'inject_related',
			array(
				'label'        => esc_html__( 'نمایش مطالب مشابه بین پاراگراف‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'باکس مطالب مرتبط را بعد از پاراگراف‌های محتوا نمایش می‌دهد.', 'tadris' ),
			)
		);

		$this->add_control(
			'related_interval',
			array(
				'label'       => esc_html__( 'هر چند پاراگراف', 'tadris' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'max'         => 20,
				'step'        => 1,
				'default'     => 3,
				'description' => esc_html__( 'مثلاً ۳ یعنی بعد از هر ۳ پاراگراف یک باکس مطلب مشابه نمایش داده شود.', 'tadris' ),
				'condition'   => array(
					'inject_related' => 'yes',
				),
			)
		);

		$this->add_control(
			'related_max_boxes',
			array(
				'label'     => esc_html__( 'حداکثر تعداد باکس', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'default'   => 3,
				'condition' => array(
					'inject_related' => 'yes',
				),
			)
		);

		$this->add_control(
			'related_title_template',
			array(
				'label'       => esc_html__( 'قالب عنوان باکس', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'در مورد {title} بیشتر بدانید', 'tadris' ),
				'label_block' => true,
				'description' => esc_html__( 'از {title} برای نام مطلب مرتبط استفاده کنید.', 'tadris' ),
				'condition'   => array(
					'inject_related' => 'yes',
				),
			)
		);

		$this->add_control(
			'related_button_text',
			array(
				'label'       => esc_html__( 'متن دکمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => esc_html__( 'خالی بگذارید تا به‌صورت خودکار «مشاهده ویدیو» یا «مشاهده مقاله» نمایش داده شود.', 'tadris' ),
				'condition'   => array(
					'inject_related' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_tags_heading',
			array(
				'label'     => esc_html__( 'برچسب‌ها', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_tags',
			array(
				'label'        => esc_html__( 'نمایش برچسب‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'برچسب‌های مقاله (post_tag) یا محصول (product_tag) را زیر محتوا نمایش می‌دهد.', 'tadris' ),
			)
		);

		$this->add_control(
			'tags_label',
			array(
				'label'       => esc_html__( 'متن برچسب بخش', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'برچسب‌ها:', 'tadris' ),
				'label_block' => true,
				'condition'   => array(
					'show_tags' => 'yes',
				),
			)
		);

		$this->add_control(
			'link_tags',
			array(
				'label'        => esc_html__( 'لینک به آرشیو برچسب', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'show_tags' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_content',
			array(
				'label' => esc_html__( 'استایل محتوا', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'content_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-dynamic-content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .webmz-dynamic-content',
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-dynamic-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_related_box',
			array(
				'label'     => esc_html__( 'باکس مطالب مشابه', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'inject_related' => 'yes',
				),
			)
		);

		$this->add_control(
			'related_box_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eef3f8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-inline-related' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'related_accent_color',
			array(
				'label'     => esc_html__( 'رنگ نوار کناری', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f97316',
				'selectors' => array(
					'{{WRAPPER}} .webmz-inline-related' => 'border-right-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'related_accent_width',
			array(
				'label'      => esc_html__( 'عرض نوار کناری', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 4,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-inline-related' => 'border-right-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'related_box_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-inline-related' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'related_box_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-inline-related__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'related_box_margin',
			array(
				'label'      => esc_html__( 'فاصله بیرونی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-inline-related' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'related_box_gap',
			array(
				'label'      => esc_html__( 'فاصله بین متن و دکمه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-inline-related__inner' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_related_title',
			array(
				'label'     => esc_html__( 'عنوان مطالب مشابه', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'inject_related' => 'yes',
				),
			)
		);

		$this->add_control(
			'related_title_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-inline-related__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'related_title_bold_color',
			array(
				'label'     => esc_html__( 'رنگ بخش برجسته', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-inline-related__title strong' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'related_title_typography',
				'selector' => '{{WRAPPER}} .webmz-inline-related__title',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_related_desc',
			array(
				'label'     => esc_html__( 'توضیحات مطالب مشابه', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'inject_related' => 'yes',
				),
			)
		);

		$this->add_control(
			'related_desc_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => array(
					'{{WRAPPER}} .webmz-inline-related__desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'related_desc_typography',
				'selector' => '{{WRAPPER}} .webmz-inline-related__desc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_related_button',
			array(
				'label'     => esc_html__( 'دکمه مطالب مشابه', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'inject_related' => 'yes',
				),
			)
		);

		$this->add_control(
			'related_btn_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-inline-related__btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'related_btn_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2563eb',
				'selectors' => array(
					'{{WRAPPER}} .webmz-inline-related__btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'related_btn_hover_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-inline-related__btn:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'related_btn_typography',
				'selector' => '{{WRAPPER}} .webmz-inline-related__btn',
			)
		);

		$this->add_responsive_control(
			'related_btn_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-inline-related__btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'related_btn_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-inline-related__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_tags',
			array(
				'label'     => esc_html__( 'برچسب‌ها', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_tags' => 'yes',
				),
			)
		);

		$this->add_control(
			'tags_label_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان بخش', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => array(
					'{{WRAPPER}} .webmz-content-tags__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tags_label_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-content-tags__label',
			)
		);

		$this->add_control(
			'tag_color',
			array(
				'label'     => esc_html__( 'رنگ متن برچسب', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0878f9',
				'selectors' => array(
					'{{WRAPPER}} .webmz-content-tags__item' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tag_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه برچسب', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eef5ff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-content-tags__item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tag_hover_color',
			array(
				'label'     => esc_html__( 'رنگ متن هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.webmz-content-tags__item:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tag_hover_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.webmz-content-tags__item:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tag_typography',
				'label'    => esc_html__( 'تایپوگرافی برچسب', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-content-tags__item',
			)
		);

		$this->add_responsive_control(
			'tag_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 6,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-content-tags__item' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tag_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی برچسب', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-content-tags__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tags_gap',
			array(
				'label'      => esc_html__( 'فاصله بین برچسب‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 8,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-content-tags__list' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tags_margin',
			array(
				'label'      => esc_html__( 'فاصله بیرونی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-content-tags' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Resolve the tag taxonomy for a post.
	 *
	 * @param \WP_Post $post Post object.
	 * @return string Taxonomy slug or empty.
	 */
	protected function get_tags_taxonomy( $post ) {
		if ( ! $post instanceof \WP_Post ) {
			return '';
		}

		if ( 'product' === $post->post_type && taxonomy_exists( 'product_tag' ) ) {
			return 'product_tag';
		}

		if ( is_object_in_taxonomy( $post->post_type, 'post_tag' ) ) {
			return 'post_tag';
		}

		return '';
	}

	/**
	 * Render post/product tags under dynamic content.
	 *
	 * @param \WP_Post|null $post     Post object.
	 * @param array         $settings Widget settings.
	 * @param bool          $preview  Whether editor preview.
	 * @return void
	 */
	protected function render_content_tags( $post, $settings, $preview = false ) {
		if ( empty( $settings['show_tags'] ) || 'yes' !== $settings['show_tags'] ) {
			return;
		}

		$label    = isset( $settings['tags_label'] ) ? (string) $settings['tags_label'] : '';
		$as_link  = ! empty( $settings['link_tags'] ) && 'yes' === $settings['link_tags'];
		$terms    = array();

		if ( $preview ) {
			$terms = array(
				(object) array(
					'name' => esc_html__( 'وردپرس', 'tadris' ),
					'link' => '#',
				),
				(object) array(
					'name' => esc_html__( 'آموزش', 'tadris' ),
					'link' => '#',
				),
				(object) array(
					'name' => esc_html__( 'پلاگین', 'tadris' ),
					'link' => '#',
				),
			);
		} else {
			$taxonomy = $this->get_tags_taxonomy( $post );

			if ( '' === $taxonomy ) {
				return;
			}

			$fetched = get_the_terms( $post->ID, $taxonomy );

			if ( empty( $fetched ) || is_wp_error( $fetched ) ) {
				return;
			}

			foreach ( $fetched as $term ) {
				$term_link = get_term_link( $term );

				$terms[] = (object) array(
					'name' => $term->name,
					'link' => is_wp_error( $term_link ) ? '' : $term_link,
				);
			}
		}

		if ( empty( $terms ) ) {
			return;
		}

		echo '<div class="webmz-content-tags">';

		if ( '' !== $label ) {
			echo '<span class="webmz-content-tags__label">' . esc_html( $label ) . '</span>';
		}

		echo '<div class="webmz-content-tags__list">';

		foreach ( $terms as $term ) {
			$name = (string) $term->name;
			$url  = isset( $term->link ) ? (string) $term->link : '';

			if ( $as_link && $url ) {
				printf(
					'<a class="webmz-content-tags__item" href="%1$s" rel="tag">%2$s</a>',
					esc_url( $url ),
					esc_html( $name )
				);
			} else {
				echo '<span class="webmz-content-tags__item">' . esc_html( $name ) . '</span>';
			}
		}

		echo '</div>';
		echo '</div>';
	}

	/**
	 * Render editor preview for the inline related box.
	 *
	 * @return void
	 */
	protected function render_related_box_preview() {
		$settings = $this->get_settings_for_display();
		$sample   = array(
			'post_title'   => esc_html__( 'خطای 404', 'tadris' ),
			'post_excerpt' => esc_html__( 'ما در مقاله‌ای جداگانه به صورت تخصصی به خطای 404 وردپرس پرداخته‌ایم که شاید برایتان مفید باشد.', 'tadris' ),
			'permalink'    => '#',
			'ID'           => 0,
		);

		echo \webmz_render_inline_related_box( (object) $sample, $settings, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( \webmz_is_layout_editing_context() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'محتوای نوشته/برگه در این قسمت نمایش داده می‌شود.', 'tadris' ) . '</div>';

			if ( ! empty( $settings['inject_related'] ) && 'yes' === $settings['inject_related'] ) {
				echo '<div class="webmz-dynamic-content">';
				$this->render_related_box_preview();
				echo '</div>';
			}

			$this->render_content_tags( null, $settings, true );

			return;
		}

		$post = get_post( \webmz_get_context_post_id() );

		if ( ! $post ) {
			return;
		}

		if ( ! empty( $settings['inject_related'] ) && 'yes' === $settings['inject_related'] ) {
			$GLOBALS['webmz_runtime_inline_related'] = array(
				'enabled'  => true,
				'post_id'  => $post->ID,
				'settings' => $settings,
			);
		}

		$content = apply_filters( 'the_content', $post->post_content );
		$content = apply_filters( 'webmz_dynamic_post_content', $content );

		unset( $GLOBALS['webmz_runtime_inline_related'] );

		echo '<div class="webmz-dynamic-content">' . $content . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$this->render_content_tags( $post, $settings );
	}
}

/**
 * Dynamic featured image widget.
 */
class Featured_Image_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() { return 'webmz-featured-image'; }
	public function get_title() { return esc_html__( 'تصویر شاخص داینامیک', 'tadris' ); }
	public function get_icon() { return 'eicon-featured-image'; }
	public function get_categories() { return array( 'webmz-dynamic-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'تصویر', 'tadris' ) ) );
		$this->add_control( 'size', array(
			'label'   => esc_html__( 'اندازه تصویر', 'tadris' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'large',
			'options' => array( 'thumbnail' => esc_html__( 'بندانگشتی', 'tadris' ), 'medium' => esc_html__( 'متوسط', 'tadris' ), 'large' => esc_html__( 'بزرگ', 'tadris' ), 'full' => esc_html__( 'کامل', 'tadris' ) ),
		) );
		$this->add_control(
			'hide_when_video',
			array(
				'label'        => esc_html__( 'عدم نمایش در صورت وجود ویدیو', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'اگر در متاباکس نوشته، «لینک فایل ویدیو» پر شده باشد، تصویر شاخص نمایش داده نمی‌شود.', 'tadris' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'style_layout',
			array(
				'label' => esc_html__( 'چینش', 'tadris' ),
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
				'selectors' => array(
					'{{WRAPPER}} .webmz-dynamic-featured-image' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'      => esc_html__( 'عرض تصویر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1200,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-dynamic-featured-image img' => 'width: {{SIZE}}{{UNIT}}; max-width: 100%;',
				),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => esc_html__( 'ارتفاع تصویر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 900,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-dynamic-featured-image img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'object_fit',
			array(
				'label'     => esc_html__( 'حالت برش تصویر', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => array(
					'fill'    => 'Fill',
					'cover'   => 'Cover',
					'contain' => 'Contain',
					'none'    => 'None',
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-dynamic-featured-image img' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'image_style', esc_html__( 'تصویر', 'tadris' ), '.webmz-dynamic-featured-image img' );
		$this->webmz_register_box_style_controls( 'wrapper_style', esc_html__( 'باکس تصویر', 'tadris' ), '.webmz-dynamic-featured-image' );
	}
	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'تصویر شاخص داینامیک', 'tadris' ) . '</div>';
			return;
		}
		$id       = \webmz_get_context_post_id();
		$settings = $this->get_settings_for_display();

		if (
			! empty( $settings['hide_when_video'] )
			&& 'yes' === $settings['hide_when_video']
			&& function_exists( 'webmz_post_has_video_url' )
			&& webmz_post_has_video_url( $id )
		) {
			return;
		}

		if ( has_post_thumbnail( $id ) ) {
			echo '<div class="webmz-dynamic-featured-image">' . webmz_get_post_loop_thumbnail( $id ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

/**
 * Dynamic archive/search heading widget.
 */
class Archive_Title_Widget extends Widget_Base {
	public function get_name() { return 'webmz-archive-title'; }
	public function get_title() { return esc_html__( 'عنوان داینامیک آرشیو', 'tadris' ); }
	public function get_icon() { return 'eicon-archive-title'; }
	public function get_categories() { return array( 'webmz-dynamic-widgets' ); }
	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<h1 class="webmz-dynamic-title">' . esc_html__( 'عنوان آرشیو یا نتیجه جستجو', 'tadris' ) . '</h1>';
			return;
		}
		if ( is_search() ) {
			$title = sprintf( esc_html__( 'نتایج جستجو برای: %s', 'tadris' ), get_search_query() );
		} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
			$title = get_the_title( absint( get_option( 'page_for_posts' ) ) );
		} else {
			$title = get_the_archive_title();
		}
		echo '<h1 class="webmz-dynamic-title">' . esc_html( $title ) . '</h1>';
	}
}

/**
 * Archive loop widget using the current WordPress query.
 */
class Archive_Loop_Widget extends Widget_Base {
	public function get_name() { return 'webmz-archive-loop'; }
	public function get_title() { return esc_html__( 'حلقه آرشیو نوشته‌ها', 'tadris' ); }
	public function get_icon() { return 'eicon-post-list'; }
	public function get_categories() { return array( 'webmz-dynamic-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'layout', array( 'label' => esc_html__( 'چیدمان', 'tadris' ) ) );
		$this->add_control( 'columns', array(
			'label'   => esc_html__( 'تعداد ستون', 'tadris' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '3',
			'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
		) );
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$columns  = in_array( $settings['columns'], array( '1', '2', '3', '4' ), true ) ? $settings['columns'] : '3';
		if ( \webmz_is_layout_editing_context() ) {
			echo '<div class="webmz-loop webmz-columns-' . esc_attr( $columns ) . '">';
			for ( $i = 0; $i < 3; $i++ ) {
				echo '<article class="webmz-card webmz-editor-placeholder">' . esc_html__( 'آیتم آرشیو داینامیک', 'tadris' ) . '</article>';
			}
			echo '</div>';
			return;
		}
		if ( have_posts() ) {
			echo '<div class="webmz-loop webmz-columns-' . esc_attr( $columns ) . '">';
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/content/card', get_post_type() );
			}
			echo '</div>';
			the_posts_pagination();
		} else {
			get_template_part( 'template-parts/content/none' );
		}
		wp_reset_postdata();
	}
}

/**
 * Dynamic post breadcrumb widget.
 */
class Post_Breadcrumb_Widget extends Widget_Base {
	public function get_name() {
		return 'webmz-post-breadcrumb';
	}

	public function get_title() {
		return esc_html__( 'خرده‌نان پست فعلی', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-navigation-horizontal';
	}

	public function get_categories() {
		return array( 'webmz-dynamic-widgets' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'home_label',
			array(
				'label'       => esc_html__( 'عنوان صفحه اصلی', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => get_bloginfo( 'name' ),
				'placeholder' => esc_html__( 'صفحه اصلی', 'tadris' ),
			)
		);

		$this->add_control(
			'show_home',
			array(
				'label'        => esc_html__( 'نمایش صفحه اصلی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_blog_page',
			array(
				'label'        => esc_html__( 'نمایش صفحه بلاگ', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_shop_page',
			array(
				'label'        => esc_html__( 'نمایش صفحه فروشگاه', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_categories',
			array(
				'label'        => esc_html__( 'نمایش دسته‌بندی‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'separator',
			array(
				'label'   => esc_html__( 'جداکننده', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '›',
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
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-breadcrumb__list' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_links',
			array(
				'label' => esc_html__( 'لینک‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => esc_html__( 'رنگ لینک', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#94a3b8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-breadcrumb__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_hover_color',
			array(
				'label'     => esc_html__( 'رنگ هاور لینک', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-breadcrumb__link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'label'    => esc_html__( 'تایپوگرافی لینک', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-post-breadcrumb__link',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_current',
			array(
				'label' => esc_html__( 'عنوان فعلی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'current_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-breadcrumb__current' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'current_typography',
				'label'    => esc_html__( 'تایپوگرافی', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-post-breadcrumb__current',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_separator',
			array(
				'label' => esc_html__( 'جداکننده', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'separator_color',
			array(
				'label'     => esc_html__( 'رنگ جداکننده', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cbd5e1',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-breadcrumb__sep' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'separator_accent_color',
			array(
				'label'     => esc_html__( 'رنگ جداکننده قبل از عنوان فعلی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0878f9',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-breadcrumb__sep.is-accent' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'separator_size',
			array(
				'label'      => esc_html__( 'اندازه جداکننده', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 30,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-post-breadcrumb__sep' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render placeholder breadcrumb for layout editor.
	 *
	 * @param string $separator Separator character.
	 * @return void
	 */
	protected function render_editor_breadcrumb( $separator ) {
		$items = array(
			array(
				'label'   => get_bloginfo( 'name' ),
				'current' => false,
			),
		);

		if ( class_exists( 'WooCommerce' ) ) {
			$items[] = array(
				'label'   => esc_html__( 'فروشگاه', 'tadris' ),
				'current' => false,
			);
			$items[] = array(
				'label'   => esc_html__( 'دسته محصول', 'tadris' ),
				'current' => false,
			);
			$items[] = array(
				'label'   => esc_html__( 'عنوان محصول فعلی', 'tadris' ),
				'current' => true,
			);
		} else {
			$items[] = array(
				'label'   => esc_html__( 'بلاگ', 'tadris' ),
				'current' => false,
			);
			$items[] = array(
				'label'   => esc_html__( 'دسته‌بندی', 'tadris' ),
				'current' => false,
			);
			$items[] = array(
				'label'   => esc_html__( 'عنوان نوشته فعلی', 'tadris' ),
				'current' => true,
			);
		}

		echo '<nav class="webmz-post-breadcrumb" aria-label="' . esc_attr__( 'مسیر صفحه', 'tadris' ) . '">';
		echo '<ol class="webmz-post-breadcrumb__list">';

		foreach ( $items as $index => $item ) {
			$is_current = ! empty( $item['current'] );

			echo '<li class="webmz-post-breadcrumb__item' . ( $is_current ? ' is-current' : '' ) . '">';

			if ( $index > 0 ) {
				$sep_class = $is_current ? ' is-accent' : '';
				echo '<span class="webmz-post-breadcrumb__sep' . esc_attr( $sep_class ) . '" aria-hidden="true">' . esc_html( $separator ) . '</span>';
			}

			if ( $is_current ) {
				echo '<span class="webmz-post-breadcrumb__current">' . esc_html( $item['label'] ) . '</span>';
			} else {
				echo '<a class="webmz-post-breadcrumb__link" href="#">' . esc_html( $item['label'] ) . '</a>';
			}

			echo '</li>';
		}

		echo '</ol>';
		echo '</nav>';
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$separator = isset( $settings['separator'] ) ? (string) $settings['separator'] : '›';

		if ( \webmz_is_layout_editing_context() ) {
			$this->render_editor_breadcrumb( $separator );
			return;
		}

		$items = \webmz_get_context_breadcrumb_items(
			array(
				'home_label'      => isset( $settings['home_label'] ) ? $settings['home_label'] : get_bloginfo( 'name' ),
				'show_home'       => ! empty( $settings['show_home'] ) && 'yes' === $settings['show_home'],
				'show_blog_page'  => ! empty( $settings['show_blog_page'] ) && 'yes' === $settings['show_blog_page'],
				'show_shop_page'  => ! array_key_exists( 'show_shop_page', $settings ) || 'yes' === $settings['show_shop_page'],
				'show_categories' => ! empty( $settings['show_categories'] ) && 'yes' === $settings['show_categories'],
			)
		);

		if ( empty( $items ) ) {
			return;
		}

		echo '<nav class="webmz-post-breadcrumb" aria-label="' . esc_attr__( 'مسیر صفحه', 'tadris' ) . '">';
		echo '<ol class="webmz-post-breadcrumb__list">';

		$total = count( $items );

		foreach ( $items as $index => $item ) {
			$is_current = ! empty( $item['current'] );

			echo '<li class="webmz-post-breadcrumb__item' . ( $is_current ? ' is-current' : '' ) . '">';

			if ( $index > 0 ) {
				$sep_class = ( $is_current ) ? ' is-accent' : '';
				echo '<span class="webmz-post-breadcrumb__sep' . esc_attr( $sep_class ) . '" aria-hidden="true">' . esc_html( $separator ) . '</span>';
			}

			if ( $is_current ) {
				echo '<span class="webmz-post-breadcrumb__current" aria-current="page">' . esc_html( $item['label'] ) . '</span>';
			} else {
				echo '<a class="webmz-post-breadcrumb__link" href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
			}

			echo '</li>';
		}

		echo '</ol>';
		echo '</nav>';
	}
}

/**
 * Dynamic post last-updated date widget.
 */
class Post_Modified_Date_Widget extends Widget_Base {
	public function get_name() {
		return 'webmz-post-modified-date';
	}

	public function get_title() {
		return esc_html__( 'تاریخ آخرین بروزرسانی', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-calendar';
	}

	public function get_categories() {
		return array( 'webmz-dynamic-widgets' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'label_text',
			array(
				'label'   => esc_html__( 'برچسب', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'آخرین بروزرسانی:', 'tadris' ),
			)
		);

		$this->add_control(
			'date_source',
			array(
				'label'   => esc_html__( 'منبع تاریخ', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'modified',
				'options' => array(
					'modified'  => esc_html__( 'آخرین بروزرسانی', 'tadris' ),
					'published' => esc_html__( 'تاریخ انتشار', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'show_weekday',
			array(
				'label'        => esc_html__( 'نمایش روز هفته', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_month_word',
			array(
				'label'        => esc_html__( 'نمایش کلمه «ماه»', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-modified' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_label',
			array(
				'label' => esc_html__( 'برچسب', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'رنگ برچسب', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-modified__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'label'    => esc_html__( 'تایپوگرافی', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-post-modified__label',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_date',
			array(
				'label' => esc_html__( 'تاریخ', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'date_color',
			array(
				'label'     => esc_html__( 'رنگ تاریخ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-modified__date' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'date_typography',
				'label'    => esc_html__( 'تایپوگرافی', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-post-modified__date',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$label_text      = isset( $settings['label_text'] ) ? (string) $settings['label_text'] : esc_html__( 'آخرین بروزرسانی:', 'tadris' );
		$show_weekday    = ! empty( $settings['show_weekday'] ) && 'yes' === $settings['show_weekday'];
		$show_month_word = ! empty( $settings['show_month_word'] ) && 'yes' === $settings['show_month_word'];
		$use_modified    = empty( $settings['date_source'] ) || 'modified' === $settings['date_source'];
		$timestamp       = 0;

		if ( \webmz_is_layout_editing_context() ) {
			$timestamp = time();
		} else {
			$post_id = \webmz_get_context_post_id();

			if ( ! $post_id ) {
				return;
			}

			$timestamp = $use_modified
				? (int) get_post_modified_time( 'U', false, $post_id )
				: (int) get_post_time( 'U', false, $post_id );
		}

		if ( ! $timestamp ) {
			return;
		}

		$formatted = \webmz_format_jalali_date( $timestamp, $show_weekday, $show_month_word );

		if ( '' === $formatted ) {
			return;
		}

		echo '<div class="webmz-post-modified">';
		if ( '' !== trim( $label_text ) ) {
			echo '<span class="webmz-post-modified__label">' . esc_html( $label_text ) . '</span> ';
		}
		printf(
			'<time class="webmz-post-modified__date" datetime="%1$s">%2$s</time>',
			esc_attr( wp_date( 'c', $timestamp ) ),
			esc_html( $formatted )
		);
		echo '</div>';
	}
}

/**
 * Dynamic primary category widget for the current post.
 */
class Post_Primary_Category_Widget extends Widget_Base {
	public function get_name() {
		return 'webmz-post-primary-category';
	}

	public function get_title() {
		return esc_html__( 'دسته‌بندی اصلی پست', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-folder';
	}

	public function get_categories() {
		return array( 'webmz-dynamic-widgets' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'show_icon',
			array(
				'label'        => esc_html__( 'نمایش آیکون', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'link_category',
			array(
				'label'        => esc_html__( 'لینک به آرشیو دسته', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-primary-category' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_category',
			array(
				'label' => esc_html__( 'دسته‌بندی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'category_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0878f9',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-primary-category__label' => 'color: {{VALUE}};',
					'{{WRAPPER}} .webmz-post-primary-category__link'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0878f9',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-primary-category__icon' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_icon' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'category_typography',
				'label'    => esc_html__( 'تایپوگرافی', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-post-primary-category__label, {{WRAPPER}} .webmz-post-primary-category__link',
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 32,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 18,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-post-primary-category__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'show_icon' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Print the default category grid icon.
	 *
	 * @return void
	 */
	protected function render_category_icon() {
		?>
		<span class="webmz-post-primary-category__icon" aria-hidden="true">
			<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
				<rect x="3" y="3" width="8" height="8" rx="1.5"/>
				<rect x="13" y="3" width="8" height="8" rx="1.5"/>
				<rect x="3" y="13" width="8" height="8" rx="1.5"/>
				<rect x="13" y="13" width="8" height="8" rx="1.5"/>
			</svg>
		</span>
		<?php
	}

	/**
	 * Render category label markup.
	 *
	 * @param string $name     Category name.
	 * @param string $url      Category URL.
	 * @param bool   $as_link  Whether to render as link.
	 * @return void
	 */
	protected function render_category_label( $name, $url, $as_link ) {
		if ( $as_link && $url ) {
			printf(
				'<a class="webmz-post-primary-category__link" href="%1$s">%2$s</a>',
				esc_url( $url ),
				esc_html( $name )
			);
			return;
		}

		echo '<span class="webmz-post-primary-category__label">' . esc_html( $name ) . '</span>';
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$show_icon  = ! empty( $settings['show_icon'] ) && 'yes' === $settings['show_icon'];
		$as_link    = ! empty( $settings['link_category'] ) && 'yes' === $settings['link_category'];
		$name       = '';
		$url        = '';

		if ( \webmz_is_layout_editing_context() ) {
			$name = esc_html__( 'مقالات آموزشی وردپرس', 'tadris' );
			$url  = '#';
		} else {
			$post_id  = \webmz_get_context_post_id();
			$category = \webmz_get_deepest_post_category( $post_id );

			if ( ! $category instanceof \WP_Term ) {
				return;
			}

			$term_link = get_term_link( $category );

			if ( is_wp_error( $term_link ) ) {
				return;
			}

			$name = $category->name;
			$url  = $term_link;
		}

		echo '<div class="webmz-post-primary-category">';

		if ( $show_icon ) {
			$this->render_category_icon();
		}

		$this->render_category_label( $name, $url, $as_link );
		echo '</div>';
	}
}

/**
 * Dynamic share and save actions for the current post.
 */
class Post_Share_Save_Widget extends Widget_Base {
	public function get_name() {
		return 'webmz-post-share-save';
	}

	public function get_title() {
		return esc_html__( 'اشتراک‌گذاری و ذخیره پست', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-share';
	}

	public function get_categories() {
		return array( 'webmz-dynamic-widgets' );
	}

	public function get_script_depends() {
		return array( 'webmz-tadris-widgets' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'show_save',
			array(
				'label'        => esc_html__( 'نمایش ذخیره', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'save_label',
			array(
				'label'     => esc_html__( 'متن ذخیره', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'ذخیره', 'tadris' ),
				'condition' => array(
					'show_save' => 'yes',
				),
			)
		);

		$this->add_control(
			'remove_label',
			array(
				'label'     => esc_html__( 'متن حذف از ذخیره', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'حذف از ذخیره', 'tadris' ),
				'condition' => array(
					'show_save' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_share',
			array(
				'label'        => esc_html__( 'نمایش اشتراک‌گذاری', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'share_label',
			array(
				'label'     => esc_html__( 'متن اشتراک‌گذاری', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'اشتراک گذاری', 'tadris' ),
				'condition' => array(
					'show_share' => 'yes',
				),
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
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-actions' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_actions',
			array(
				'label' => esc_html__( 'دکمه‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'action_color',
			array(
				'label'     => esc_html__( 'رنگ متن و آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#585858',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-actions .webmz-post-action' => 'color: {{VALUE}};',
					'{{WRAPPER}} .webmz-post-actions .webmz-post-action svg' => 'stroke: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'action_hover_color',
			array(
				'label'     => esc_html__( 'رنگ هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-actions .webmz-post-action:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .webmz-post-actions .webmz-post-action:hover svg' => 'stroke: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'action_typography',
				'label'    => esc_html__( 'تایپوگرافی', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-post-actions .webmz-post-action',
			)
		);

		$this->add_responsive_control(
			'action_gap',
			array(
				'label'      => esc_html__( 'فاصله بین دکمه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-post-actions' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render editor preview using the shared markup helper.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	protected function render_editor_preview( $settings ) {
		if ( ! function_exists( '\webmz_tadris_render_post_action_buttons' ) ) {
			return;
		}

		\webmz_tadris_render_post_action_buttons(
			0,
			array(
				'preview'       => true,
				'wrapper_class' => 'webmz-post-actions',
				'save_class'    => 'webmz-post-action webmz-post-action--save',
				'share_class'   => 'webmz-post-action webmz-post-action--share',
				'save_label'    => isset( $settings['save_label'] ) ? $settings['save_label'] : esc_html__( 'ذخیره', 'tadris' ),
				'remove_label'  => isset( $settings['remove_label'] ) ? $settings['remove_label'] : esc_html__( 'حذف از ذخیره', 'tadris' ),
				'share_label'   => isset( $settings['share_label'] ) ? $settings['share_label'] : esc_html__( 'اشتراک گذاری', 'tadris' ),
				'show_save'     => empty( $settings['show_save'] ) || 'yes' === $settings['show_save'],
				'show_share'    => empty( $settings['show_share'] ) || 'yes' === $settings['show_share'],
			)
		);
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( \webmz_is_layout_editing_context() ) {
			$this->render_editor_preview( $settings );
			return;
		}

		if ( ! function_exists( '\webmz_tadris_render_post_action_buttons' ) ) {
			return;
		}

		$post_id = \webmz_get_context_post_id();

		if ( ! $post_id ) {
			return;
		}

		\webmz_tadris_render_post_action_buttons(
			$post_id,
			array(
				'wrapper_class' => 'webmz-post-actions',
				'save_class'    => 'webmz-post-action webmz-post-action--save',
				'share_class'   => 'webmz-post-action webmz-post-action--share',
				'save_label'    => isset( $settings['save_label'] ) ? $settings['save_label'] : esc_html__( 'ذخیره', 'tadris' ),
				'remove_label'  => isset( $settings['remove_label'] ) ? $settings['remove_label'] : esc_html__( 'حذف از ذخیره', 'tadris' ),
				'share_label'   => isset( $settings['share_label'] ) ? $settings['share_label'] : esc_html__( 'اشتراک گذاری', 'tadris' ),
				'show_save'     => empty( $settings['show_save'] ) || 'yes' === $settings['show_save'],
				'show_share'    => empty( $settings['show_share'] ) || 'yes' === $settings['show_share'],
			)
		);
	}
}

/**
 * Dynamic table of contents widget for the current post.
 */
class Post_TOC_Widget extends Widget_Base {
	public function get_name() {
		return 'webmz-post-toc';
	}

	public function get_title() {
		return esc_html__( 'فهرست مطالب پست', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	public function get_categories() {
		return array( 'webmz-dynamic-widgets' );
	}

	public function get_script_depends() {
		return array( 'webmz-post-toc' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'toc_title',
			array(
				'label'       => esc_html__( 'عنوان فهرست', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'فهرستی از مطالبی که در این مقاله می‌خوانید', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'heading_tags_heading',
			array(
				'label'     => esc_html__( 'تگ‌های قابل نمایش', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		foreach ( array(
			'h1' => esc_html__( 'H1', 'tadris' ),
			'h2' => esc_html__( 'H2', 'tadris' ),
			'h3' => esc_html__( 'H3', 'tadris' ),
			'h4' => esc_html__( 'H4', 'tadris' ),
			'h5' => esc_html__( 'H5', 'tadris' ),
			'h6' => esc_html__( 'H6', 'tadris' ),
		) as $tag => $label ) {
			$this->add_control(
				'include_' . $tag,
				array(
					'label'        => $label,
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'بله', 'tadris' ),
					'label_off'    => esc_html__( 'خیر', 'tadris' ),
					'return_value' => 'yes',
					'default'      => in_array( $tag, array( 'h2', 'h3', 'h4' ), true ) ? 'yes' : '',
				)
			);
		}

		$this->add_control(
			'collapsible',
			array(
				'label'        => esc_html__( 'قابل جمع‌شدن', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'start_collapsed',
			array(
				'label'        => esc_html__( 'شروع در حالت بسته', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'collapsible' => 'yes',
				),
			)
		);

		$this->add_control(
			'scroll_offset',
			array(
				'label'   => esc_html__( 'فاصله اسکرول از بالا (px)', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 300,
				'step'    => 1,
				'default' => 80,
			)
		);

		$this->add_control(
			'content_selector',
			array(
				'label'       => esc_html__( 'سلکتور محتوای پست', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '.webmz-dynamic-content',
				'description' => esc_html__( 'برای یافتن تیترها در محتوای نوشته استفاده می‌شود.', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_box',
			array(
				'label' => esc_html__( 'باکس', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'box_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f4f7fa',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-toc__box' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'    => '20',
					'right'  => '24',
					'bottom' => '20',
					'left'   => '24',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-post-toc__box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'top'    => '14',
					'right'  => '14',
					'bottom' => '14',
					'left'   => '14',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-post-toc__box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_title',
			array(
				'label' => esc_html__( 'عنوان فهرست', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0878f9',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-toc__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'تایپوگرافی', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-post-toc__title',
			)
		);

		$this->add_control(
			'chevron_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون جمع/باز', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-toc__chevron' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'collapsible' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_links',
			array(
				'label' => esc_html__( 'آیتم‌های فهرست', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => esc_html__( 'رنگ آیتم', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-toc__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_hover_color',
			array(
				'label'     => esc_html__( 'رنگ هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0878f9',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-toc__link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'label'    => esc_html__( 'تایپوگرافی', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-post-toc__link',
			)
		);

		$this->add_responsive_control(
			'item_spacing',
			array(
				'label'      => esc_html__( 'فاصله بین آیتم‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-post-toc__list' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get editor preview TOC items.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	protected function get_editor_preview_items() {
		return array(
			array(
				'id'    => 'preview-1',
				'text'  => esc_html__( 'خطای 403 - دسترسی ممنوع', 'tadris' ),
				'level' => 2,
				'index' => 0,
			),
			array(
				'id'    => 'preview-2',
				'text'  => esc_html__( 'خطای 404 - صفحه مورد نظر یافت نشد', 'tadris' ),
				'level' => 2,
				'index' => 1,
			),
			array(
				'id'    => 'preview-3',
				'text'  => esc_html__( 'خطای 500 - خطای داخل سرور', 'tadris' ),
				'level' => 2,
				'index' => 2,
			),
		);
	}

	/**
	 * Render TOC list markup.
	 *
	 * @param array<int,array<string,mixed>> $items TOC items.
	 * @return void
	 */
	protected function render_toc_list( $items ) {
		if ( empty( $items ) ) {
			echo '<p class="webmz-post-toc__empty">' . esc_html__( 'تیتری برای نمایش پیدا نشد.', 'tadris' ) . '</p>';
			return;
		}

		echo '<ol class="webmz-post-toc__list">';

		foreach ( $items as $item ) {
			$level = isset( $item['level'] ) ? absint( $item['level'] ) : 2;
			$index = isset( $item['index'] ) ? absint( $item['index'] ) : 0;
			$id    = isset( $item['id'] ) ? (string) $item['id'] : 'webmz-toc-' . $index;

			echo '<li class="webmz-post-toc__item webmz-post-toc__item--level-' . esc_attr( $level ) . '">';
			printf(
				'<a class="webmz-post-toc__link" href="#%1$s" data-webmz-toc-link="%2$d">%3$s</a>',
				esc_attr( $id ),
				$index,
				esc_html( isset( $item['text'] ) ? $item['text'] : '' )
			);
			echo '</li>';
		}

		echo '</ol>';
	}

	protected function render() {
		$settings     = $this->get_settings_for_display();
		$allowed_tags = \webmz_toc_settings_to_tags( $settings );
		$title        = isset( $settings['toc_title'] ) ? (string) $settings['toc_title'] : '';
		$collapsible  = ! empty( $settings['collapsible'] ) && 'yes' === $settings['collapsible'];
		$collapsed    = $collapsible && ! empty( $settings['start_collapsed'] ) && 'yes' === $settings['start_collapsed'];
		$offset       = isset( $settings['scroll_offset'] ) ? absint( $settings['scroll_offset'] ) : 80;
		$selector     = ! empty( $settings['content_selector'] ) ? (string) $settings['content_selector'] : '.webmz-dynamic-content';

		if ( empty( $allowed_tags ) ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'حداقل یک تگ H1 تا H6 را برای فهرست انتخاب کنید.', 'tadris' ) . '</div>';
			return;
		}

		if ( \webmz_is_layout_editing_context() ) {
			$items = $this->get_editor_preview_items();
		} else {
			$post_id = \webmz_get_context_post_id();
			$items   = $post_id ? \webmz_get_post_toc_items( $post_id, $allowed_tags ) : array();
		}

		$classes = array( 'webmz-post-toc' );
		if ( $collapsed ) {
			$classes[] = 'is-collapsed';
		}

		printf(
			'<nav class="%1$s" data-webmz-post-toc="1" data-toc-tags="%2$s" data-toc-content="%3$s" data-toc-offset="%4$d" aria-label="%5$s">',
			esc_attr( implode( ' ', $classes ) ),
			esc_attr( implode( ',', $allowed_tags ) ),
			esc_attr( $selector ),
			$offset,
			esc_attr__( 'فهرست مطالب', 'tadris' )
		);

		echo '<div class="webmz-post-toc__box">';

		if ( $collapsible ) {
			printf(
				'<button type="button" class="webmz-post-toc__toggle" data-webmz-toc-toggle="1" aria-expanded="%1$s">',
				$collapsed ? 'false' : 'true'
			);
		} else {
			echo '<div class="webmz-post-toc__toggle webmz-post-toc__toggle--static">';
		}

		if ( '' !== trim( $title ) ) {
			echo '<span class="webmz-post-toc__title">' . esc_html( $title ) . '</span>';
		}

		if ( $collapsible ) {
			echo '<span class="webmz-post-toc__chevron" aria-hidden="true">';
			echo '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>';
			echo '</span>';
			echo '</button>';
		} else {
			echo '</div>';
		}

		echo '<div class="webmz-post-toc__panel">';
		$this->render_toc_list( $items );
		echo '</div>';
		echo '</div>';
		echo '</nav>';
	}
}

/**
 * Dynamic author box for the current post.
 */
class Post_Author_Box_Widget extends Widget_Base {
	public function get_name() {
		return 'webmz-post-author-box';
	}

	public function get_title() {
		return esc_html__( 'باکس نویسنده مطلب', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return array( 'webmz-dynamic-widgets' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'author_label',
			array(
				'label'   => esc_html__( 'برچسب نویسنده', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مدرس دوره:', 'tadris' ),
			)
		);

		$this->add_control(
			'avatar_size',
			array(
				'label'      => esc_html__( 'اندازه تصویر نویسنده', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 40,
						'max' => 120,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 64,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-post-author-box__avatar img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_box',
			array(
				'label' => esc_html__( 'باکس', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'box_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-author-box' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => '20',
					'right'  => '24',
					'bottom' => '20',
					'left'   => '24',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-post-author-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'box_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-post-author-box' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'box_border_color',
			array(
				'label'     => esc_html__( 'رنگ حاشیه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15, 23, 42, 0.08)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-author-box' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'box_border_width',
			array(
				'label'      => esc_html__( 'ضخامت حاشیه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 6,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 1,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-post-author-box' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_label',
			array(
				'label' => esc_html__( 'برچسب', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#94a3b8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-author-box__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .webmz-post-author-box__label',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_name',
			array(
				'label' => esc_html__( 'نام نویسنده', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1e293b',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-author-box__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .webmz-post-author-box__name',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_bio',
			array(
				'label' => esc_html__( 'توضیحات', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bio_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-author-box__bio' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'bio_typography',
				'selector' => '{{WRAPPER}} .webmz-post-author-box__bio',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Resolve author data for the current or preview context.
	 *
	 * @return array{author_id:int,author_name:string,author_bio:string,avatar_size:int}|null
	 */
	protected function get_author_data() {
		if ( \webmz_is_layout_editing_context() ) {
			return array(
				'author_id'   => 0,
				'author_name' => esc_html__( 'محمد حسین والیزاده', 'tadris' ),
				'author_bio'  => esc_html__( 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است.', 'tadris' ),
				'avatar_size' => 64,
			);
		}

		$post_id = \webmz_get_context_post_id();

		if ( ! $post_id ) {
			return null;
		}

		$post = get_post( $post_id );

		if ( ! $post ) {
			return null;
		}

		$author_id   = (int) $post->post_author;
		$author_name = get_the_author_meta( 'display_name', $author_id );
		$author_bio  = get_the_author_meta( 'description', $author_id );

		if ( ! $author_bio ) {
			$author_bio = '';
		}

		return array(
			'author_id'   => $author_id,
			'author_name' => $author_name,
			'author_bio'  => $author_bio,
			'avatar_size' => 64,
		);
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$data     = $this->get_author_data();

		if ( ! $data ) {
			return;
		}

		$label = isset( $settings['author_label'] ) ? $settings['author_label'] : esc_html__( 'مدرس دوره:', 'tadris' );
		$size  = ! empty( $settings['avatar_size']['size'] ) ? absint( $settings['avatar_size']['size'] ) : 64;
		?>
		<div class="webmz-post-author-box webmz-dynamic-card">
			<div class="webmz-post-author-box__inner">
				<div class="webmz-post-author-box__profile">
					<div class="webmz-post-author-box__avatar">
						<?php if ( $data['author_id'] ) : ?>
							<?php echo get_avatar( $data['author_id'], $size, '', $data['author_name'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php else : ?>
							<img src="<?php echo esc_url( get_avatar_url( 0, array( 'size' => $size ) ) ); ?>" alt="" width="<?php echo esc_attr( $size ); ?>" height="<?php echo esc_attr( $size ); ?>" />
						<?php endif; ?>
					</div>

					<div class="webmz-post-author-box__meta">
						<?php if ( '' !== trim( $label ) ) : ?>
							<span class="webmz-post-author-box__label"><?php echo esc_html( $label ); ?></span>
						<?php endif; ?>
						<strong class="webmz-post-author-box__name"><?php echo esc_html( $data['author_name'] ); ?></strong>
					</div>
				</div>

				<?php if ( $data['author_bio'] ) : ?>
					<div class="webmz-post-author-box__bio"><?php echo esc_html( $data['author_bio'] ); ?></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}

/**
 * Dynamic previous/next post navigation for the current post.
 */
class Post_Navigation_Widget extends Widget_Base {
	public function get_name() {
		return 'webmz-post-navigation';
	}

	public function get_title() {
		return esc_html__( 'مطلب قبلی و بعدی', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-post-navigation';
	}

	public function get_categories() {
		return array( 'webmz-dynamic-widgets' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'prev_label',
			array(
				'label'   => esc_html__( 'برچسب مطلب قبلی', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'جلسه قبلی', 'tadris' ),
			)
		);

		$this->add_control(
			'next_label',
			array(
				'label'   => esc_html__( 'برچسب مطلب بعدی', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'جلسه بعدی', 'tadris' ),
			)
		);

		$this->add_control(
			'empty_prev_text',
			array(
				'label'   => esc_html__( 'متن جایگزین مطلب قبلی', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مطلب قبلی وجود ندارد', 'tadris' ),
			)
		);

		$this->add_control(
			'empty_next_text',
			array(
				'label'   => esc_html__( 'متن جایگزین مطلب بعدی', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مطلب بعدی وجود ندارد', 'tadris' ),
			)
		);

		$this->add_responsive_control(
			'items_gap',
			array(
				'label'      => esc_html__( 'فاصله بین باکس‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-post-nav' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_box',
			array(
				'label' => esc_html__( 'باکس', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'item_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه فعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-nav__item:not(.is-disabled)' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_disabled_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه غیرفعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f1f5f9',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-nav__item.is-disabled' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => '20',
					'right'  => '20',
					'bottom' => '20',
					'left'   => '20',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-post-nav__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-post-nav__item' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_border_color',
			array(
				'label'     => esc_html__( 'رنگ حاشیه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15, 23, 42, 0.08)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-nav__item' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_border_width',
			array(
				'label'      => esc_html__( 'ضخامت حاشیه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 6,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 1,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-post-nav__item' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_text',
			array(
				'label' => esc_html__( 'متن', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'nav_label_color',
			array(
				'label'     => esc_html__( 'رنگ برچسب', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#94a3b8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-nav__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'nav_label_typography',
				'selector' => '{{WRAPPER}} .webmz-post-nav__label',
			)
		);

		$this->add_control(
			'nav_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#334155',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-nav__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_title_disabled_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان غیرفعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#94a3b8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-nav__item.is-disabled .webmz-post-nav__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'nav_title_typography',
				'selector' => '{{WRAPPER}} .webmz-post-nav__title',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_icons',
			array(
				'label' => esc_html__( 'آیکون‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'prev_icon_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه آیکون قبلی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f97316',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-nav__icon--prev' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'next_icon_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه آیکون بعدی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#22c55e',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-nav__icon--next' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-nav__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'disabled_icon_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه آیکون غیرفعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cbd5e1',
				'selectors' => array(
					'{{WRAPPER}} .webmz-post-nav__item.is-disabled .webmz-post-nav__icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Mark widget output as dynamic so Elementor does not serve stale HTML.
	 *
	 * @return bool
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Resolve adjacent posts for the current or preview context.
	 *
	 * @return array{prev:\WP_Post|null,next:\WP_Post|null}
	 */
	protected function get_adjacent_posts() {
		if ( \webmz_is_layout_editing_context() ) {
			$preview = new \stdClass();
			$preview->post_title = esc_html__( 'عنوان نمونه مطلب', 'tadris' );
			$preview->ID         = 0;

			return array(
				'prev' => $preview,
				'next' => $preview,
			);
		}

		$post_id = \webmz_get_context_post_id();

		if ( ! $post_id || ( function_exists( 'webmz_is_content_context_post' ) && ! \webmz_is_content_context_post( $post_id ) ) ) {
			return array(
				'prev' => null,
				'next' => null,
			);
		}

		$prev = function_exists( 'webmz_get_adjacent_post' ) ? \webmz_get_adjacent_post( $post_id, 'prev' ) : null;
		$next = function_exists( 'webmz_get_adjacent_post' ) ? \webmz_get_adjacent_post( $post_id, 'next' ) : null;

		return array(
			'prev' => $prev instanceof \WP_Post ? $prev : null,
			'next' => $next instanceof \WP_Post ? $next : null,
		);
	}

	/**
	 * Render one navigation item.
	 *
	 * @param string               $type     Item type: prev|next.
	 * @param \WP_Post|object|null $adjacent Adjacent post object.
	 * @param array<string,mixed>  $settings Widget settings.
	 * @return void
	 */
	protected function render_nav_item( $type, $adjacent, $settings ) {
		$is_prev    = 'prev' === $type;
		$is_active  = (bool) $adjacent;
		$is_preview = \webmz_is_layout_editing_context();
		$label      = $is_prev
			? ( isset( $settings['prev_label'] ) ? $settings['prev_label'] : esc_html__( 'جلسه قبلی', 'tadris' ) )
			: ( isset( $settings['next_label'] ) ? $settings['next_label'] : esc_html__( 'جلسه بعدی', 'tadris' ) );
		$empty_text = $is_prev
			? ( isset( $settings['empty_prev_text'] ) ? $settings['empty_prev_text'] : esc_html__( 'مطلب قبلی وجود ندارد', 'tadris' ) )
			: ( isset( $settings['empty_next_text'] ) ? $settings['empty_next_text'] : esc_html__( 'مطلب بعدی وجود ندارد', 'tadris' ) );
		$title      = $is_active ? $adjacent->post_title : $empty_text;
		$classes    = array(
			'webmz-post-nav__item',
			$is_prev ? 'webmz-post-nav__item--prev' : 'webmz-post-nav__item--next',
		);

		if ( $is_active || $is_preview ) {
			$classes[] = 'webmz-dynamic-card';
		}

		if ( ! $is_active && ! $is_preview ) {
			$classes[] = 'is-disabled';
		}

		$icon_class = $is_prev ? 'webmz-post-nav__icon--prev' : 'webmz-post-nav__icon--next';
		$chevron    = $is_prev
			? '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>'
			: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>';

		$inner  = '<span class="webmz-post-nav__icon ' . esc_attr( $icon_class ) . '">' . $chevron . '</span>';
		$inner .= '<span class="webmz-post-nav__content">';
		$inner .= '<span class="webmz-post-nav__label">' . esc_html( $label ) . '</span>';
		$inner .= '<span class="webmz-post-nav__title">' . esc_html( $title ) . '</span>';
		$inner .= '</span>';

		if ( $is_active && ! $is_preview && ! empty( $adjacent->ID ) ) {
			printf(
				'<a class="%1$s" href="%2$s">%3$s</a>',
				esc_attr( implode( ' ', $classes ) ),
				esc_url( get_permalink( $adjacent->ID ) ),
				$inner // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
			return;
		}

		printf(
			'<div class="%1$s" aria-disabled="true">%2$s</div>',
			esc_attr( implode( ' ', $classes ) ),
			$inner // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$adjacent = $this->get_adjacent_posts();

		echo '<div class="webmz-post-nav">';
		$this->render_nav_item( 'next', $adjacent['next'], $settings );
		$this->render_nav_item( 'prev', $adjacent['prev'], $settings );
		echo '</div>';
	}
}
