<?php
/**
 * Zhaket "Why Buy" footer trust box — dark banner with stats, badges, and heading.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Zhaket-style "Why buy from us" footer trust box.
 */
class Zhaket_Why_Buy_Footer_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-zhaket-why-buy-footer';
	}

	public function get_title() {
		return esc_html__( 'باکس چرا ما فوتر ژاکت', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-info-box';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'zhaket', 'why', 'buy', 'footer', 'trust', 'stats', 'ژاکت', 'چرا', 'فوتر', 'اعتماد' );
	}

	public function get_style_depends() {
		return array( 'webmz-zhaket-why-buy-footer' );
	}

	protected function register_controls() {
		$this->register_heading_controls();
		$this->register_rating_controls();
		$this->register_products_controls();
		$this->register_guarantee_controls();
		$this->register_security_controls();
		$this->register_layout_controls();
		$this->register_box_style_controls();
		$this->register_heading_style_controls();
		$this->register_stat_style_controls();
		$this->register_label_style_controls();
		$this->register_stars_style_controls();
		$this->register_badge_style_controls();
		$this->register_guarantee_style_controls();
		$this->register_decoration_style_controls();
	}

	protected function register_heading_controls() {
		$this->start_controls_section(
			'section_heading',
			array(
				'label' => esc_html__( 'سرتیتر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_heading',
			array(
				'label'        => esc_html__( 'نمایش سرتیتر', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'heading_title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'چرا از ژاکت بخرم؟', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_heading' => 'yes' ),
			)
		);

		$this->webmz_register_title_tag_control( 'heading_title_tag', esc_html__( 'تگ HTML عنوان', 'tadris' ), array( 'show_heading' => 'yes' ) );

		$this->add_control(
			'heading_subtitle',
			array(
				'label'       => esc_html__( 'زیرعنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'معتبرترین سامانه خرید افزونه و پلاگین فارسی', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_heading' => 'yes' ),
			)
		);

		$this->add_control(
			'show_decoration',
			array(
				'label'        => esc_html__( 'نمایش تزئین الماسی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'show_heading' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_rating_controls() {
		$this->start_controls_section(
			'section_rating',
			array(
				'label' => esc_html__( 'رضایت خرید', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_rating',
			array(
				'label'        => esc_html__( 'نمایش بخش رضایت', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'rating_value',
			array(
				'label'     => esc_html__( 'مقدار امتیاز', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '+۴.۸', 'tadris' ),
				'condition' => array( 'show_rating' => 'yes' ),
			)
		);

		$this->add_control(
			'rating_stars_count',
			array(
				'label'     => esc_html__( 'تعداد ستاره', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5,
				'min'       => 1,
				'max'       => 10,
				'condition' => array( 'show_rating' => 'yes' ),
			)
		);

		$this->add_control(
			'rating_star_icon',
			array(
				'label'     => esc_html__( 'آیکون ستاره', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition' => array( 'show_rating' => 'yes' ),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'rating_star_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون ستاره', 'tadris' ),
			array( 'show_rating' => 'yes' )
		);

		$this->add_control(
			'rating_label',
			array(
				'label'       => esc_html__( 'برچسب', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'رضایت خرید کاربران', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_rating' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_products_controls() {
		$this->start_controls_section(
			'section_products',
			array(
				'label' => esc_html__( 'تعداد محصولات', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_products',
			array(
				'label'        => esc_html__( 'نمایش بخش محصولات', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'products_count',
			array(
				'label'     => esc_html__( 'مقدار', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '+۳۱۰۰', 'tadris' ),
				'condition' => array( 'show_products' => 'yes' ),
			)
		);

		$logo_repeater = new Repeater();

		$logo_repeater->add_control(
			'logo_image',
			array(
				'label'   => esc_html__( 'لوگو', 'tadris' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$logo_repeater->add_control(
			'logo_alt',
			array(
				'label'       => esc_html__( 'متن جایگزین', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);

		$this->add_control(
			'product_logos',
			array(
				'label'       => esc_html__( 'لوگوهای محصول', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $logo_repeater->get_controls(),
				'default'     => array(
					array( 'logo_alt' => esc_html__( 'محصول ۱', 'tadris' ) ),
					array( 'logo_alt' => esc_html__( 'محصول ۲', 'tadris' ) ),
					array( 'logo_alt' => esc_html__( 'محصول ۳', 'tadris' ) ),
				),
				'title_field' => '{{{ logo_alt }}}',
				'condition'   => array( 'show_products' => 'yes' ),
			)
		);

		$this->add_control(
			'products_label',
			array(
				'label'       => esc_html__( 'برچسب', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'محصول مختلف', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_products' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_guarantee_controls() {
		$this->start_controls_section(
			'section_guarantee',
			array(
				'label' => esc_html__( 'ضمانت', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_guarantee',
			array(
				'label'        => esc_html__( 'نمایش بخش ضمانت', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'guarantee_icon_type',
			array(
				'label'     => esc_html__( 'نوع آیکون', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'icon',
				'options'   => array(
					'icon'  => esc_html__( 'آیکون', 'tadris' ),
					'image' => esc_html__( 'تصویر', 'tadris' ),
				),
				'condition' => array( 'show_guarantee' => 'yes' ),
			)
		);

		$this->add_control(
			'guarantee_icon',
			array(
				'label'     => esc_html__( 'آیکون', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-award',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_guarantee'     => 'yes',
					'guarantee_icon_type' => 'icon',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'guarantee_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون', 'tadris' ),
			array(
				'show_guarantee'      => 'yes',
				'guarantee_icon_type' => 'icon',
			)
		);

		$this->add_control(
			'guarantee_image',
			array(
				'label'     => esc_html__( 'تصویر', 'tadris' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array( 'url' => '' ),
				'condition' => array(
					'show_guarantee'      => 'yes',
					'guarantee_icon_type' => 'image',
				),
			)
		);

		$this->add_control(
			'guarantee_label',
			array(
				'label'       => esc_html__( 'برچسب', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'ضمانت بازگشت وجه', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_guarantee' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_security_controls() {
		$this->start_controls_section(
			'section_security',
			array(
				'label' => esc_html__( 'امنیت و بروزرسانی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_security',
			array(
				'label'        => esc_html__( 'نمایش بخش امنیت', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$badge_repeater = new Repeater();

		$badge_repeater->add_control(
			'badge_icon',
			array(
				'label'   => esc_html__( 'آیکون', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-lock',
					'library' => 'fa-solid',
				),
			)
		);

		$badge_repeater->add_control(
			'badge_text',
			array(
				'label'       => esc_html__( 'متن', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'GUARD',
				'label_block' => true,
			)
		);

		$badge_repeater->add_control(
			'badge_gradient_start',
			array(
				'label'   => esc_html__( 'رنگ گرادیان (شروع)', 'tadris' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#7c3aed',
			)
		);

		$badge_repeater->add_control(
			'badge_gradient_end',
			array(
				'label'   => esc_html__( 'رنگ گرادیان (پایان)', 'tadris' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#ec4899',
			)
		);

		$this->add_control(
			'security_badges',
			array(
				'label'       => esc_html__( 'نشان‌ها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $badge_repeater->get_controls(),
				'default'     => array(
					array(
						'badge_icon'           => array( 'value' => 'fas fa-lock', 'library' => 'fa-solid' ),
						'badge_text'           => 'GUARD',
						'badge_gradient_start' => '#7c3aed',
						'badge_gradient_end'   => '#ec4899',
					),
					array(
						'badge_icon'           => array( 'value' => 'fas fa-arrows-rotate', 'library' => 'fa-solid' ),
						'badge_text'           => 'UPDATER',
						'badge_gradient_start' => '#f97316',
						'badge_gradient_end'   => '#22c55e',
					),
				),
				'title_field' => '{{{ badge_text }}}',
				'condition'   => array( 'show_security' => 'yes' ),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'security_badge_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون نشان', 'tadris' ),
			array( 'show_security' => 'yes' )
		);

		$this->add_control(
			'security_label',
			array(
				'label'       => esc_html__( 'برچسب', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'امنیت و بروزرسانی', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_security' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'چیدمان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'   => esc_html__( 'تعداد ستون', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '5',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zwb__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'      => esc_html__( 'فاصله بین ستون‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 24 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zwb__grid' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'column_align',
			array(
				'label'   => esc_html__( 'تراز عمودی ستون‌ها', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'center',
				'options' => array(
					'flex-start' => esc_html__( 'بالا', 'tadris' ),
					'center'     => esc_html__( 'وسط', 'tadris' ),
					'flex-end'   => esc_html__( 'پایین', 'tadris' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zwb__col' => 'align-self: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mobile_stack',
			array(
				'label'        => esc_html__( 'چیدمان عمودی در موبایل', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => esc_html__( 'در موبایل ستون‌ها به صورت عمودی چیده می‌شوند.', 'tadris' ),
			)
		);

		$this->add_responsive_control(
			'heading_column_span',
			array(
				'label'   => esc_html__( 'عرض ستون سرتیتر', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => array(
					'auto' => esc_html__( 'خودکار', 'tadris' ),
					'1'    => '1',
					'2'    => '2',
					'3'    => '3',
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zwb__col--heading' => 'grid-column: span {{VALUE}};',
				),
				'condition' => array( 'heading_column_span!' => 'auto' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_box_style_controls() {
		$this->start_controls_section(
			'box_style',
			array(
				'label' => esc_html__( 'باکس', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'box_background',
				'selector' => '{{WRAPPER}} .webmz-zwb__box',
				'fields_options' => array(
					'background' => array( 'default' => 'classic' ),
					'color'      => array( 'default' => 'var(--webmz-color-secondary, #092c4c)' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .webmz-zwb__box',
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'    => '16',
					'right'  => '16',
					'bottom' => '16',
					'left'   => '16',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zwb__box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'default'    => array(
					'top'    => '32',
					'right'  => '40',
					'bottom' => '32',
					'left'   => '40',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zwb__box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .webmz-zwb__box',
			)
		);

		$this->end_controls_section();
	}

	protected function register_heading_style_controls() {
		$this->webmz_register_text_style_controls(
			'heading_title_style',
			esc_html__( 'عنوان سرتیتر', 'tadris' ),
			'.webmz-zwb__heading-title'
		);

		$this->start_controls_section(
			'heading_subtitle_style',
			array(
				'label' => esc_html__( 'زیرعنوان سرتیتر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_subtitle_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-gray, #94a3b8)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zwb__heading-subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_subtitle_typography',
				'selector' => '{{WRAPPER}} .webmz-zwb__heading-subtitle',
			)
		);

		$this->end_controls_section();
	}

	protected function register_stat_style_controls() {
		$this->start_controls_section(
			'stat_style',
			array(
				'label' => esc_html__( 'مقادیر آماری', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'stat_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary-light, #ffffff)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zwb__stat' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'stat_typography',
				'selector' => '{{WRAPPER}} .webmz-zwb__stat',
			)
		);

		$this->end_controls_section();
	}

	protected function register_label_style_controls() {
		$this->start_controls_section(
			'label_style',
			array(
				'label' => esc_html__( 'برچسب‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary-light, #ffffff)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zwb__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .webmz-zwb__label',
			)
		);

		$this->end_controls_section();
	}

	protected function register_stars_style_controls() {
		$this->start_controls_section(
			'stars_style',
			array(
				'label' => esc_html__( 'ستاره‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'stars_color',
			array(
				'label'     => esc_html__( 'رنگ ستاره', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff9800',
				'selectors' => $this->webmz_get_icon_color_value_selectors( '{{WRAPPER}} .webmz-zwb__stars' ),
			)
		);

		$this->add_responsive_control(
			'stars_size',
			array(
				'label'      => esc_html__( 'اندازه ستاره', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 16 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zwb__stars svg, {{WRAPPER}} .webmz-zwb__stars i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'stars_gap',
			array(
				'label'      => esc_html__( 'فاصله بین ستاره‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 20 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 3 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zwb__stars' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_badge_style_controls() {
		$this->start_controls_section(
			'badge_style',
			array(
				'label' => esc_html__( 'نشان‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'badge_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'    => '4',
					'right'  => '12',
					'bottom' => '4',
					'left'   => '12',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zwb__badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'badge_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 20 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zwb__badge' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .webmz-zwb__badge-text',
			)
		);

		$this->add_control(
			'badge_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن نشان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zwb__badge' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'badge_icon_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون نشان', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 32 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 12 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zwb__badge-icon svg, {{WRAPPER}} .webmz-zwb__badge-icon i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'badges_gap',
			array(
				'label'      => esc_html__( 'فاصله بین نشان‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 6 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zwb__badges' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_guarantee_style_controls() {
		$this->webmz_register_icon_style_controls(
			'guarantee_icon_style',
			esc_html__( 'آیکون ضمانت', 'tadris' ),
			'.webmz-zwb__guarantee-icon'
		);

		$this->start_controls_section(
			'product_logos_style',
			array(
				'label' => esc_html__( 'لوگوهای محصول', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'logo_size',
			array(
				'label'      => esc_html__( 'اندازه لوگو', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 16, 'max' => 80 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 28 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zwb__logo' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'logos_gap',
			array(
				'label'      => esc_html__( 'فاصله بین لوگوها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 6 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zwb__logos' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'logo_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه لوگو', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
				'default'    => array( 'unit' => '%', 'size' => 50 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zwb__logo' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_decoration_style_controls() {
		$this->start_controls_section(
			'decoration_style',
			array(
				'label' => esc_html__( 'تزئین الماسی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'decoration_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff9800',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zwb__decoration' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'decoration_opacity',
			array(
				'label'     => esc_html__( 'شفافیت', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ) ),
				'default'   => array( 'size' => 0.15 ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zwb__decoration' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render diamond decoration SVG pattern.
	 *
	 * @return void
	 */
	protected function render_decoration() {
		?>
		<div class="webmz-zwb__decoration" aria-hidden="true">
			<svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
				<rect x="30" y="10" width="50" height="50" rx="4" transform="rotate(45 55 35)" stroke="currentColor" stroke-width="1.5"/>
				<rect x="60" y="40" width="50" height="50" rx="4" transform="rotate(45 85 65)" stroke="currentColor" stroke-width="1.5"/>
				<rect x="10" y="50" width="40" height="40" rx="3" transform="rotate(45 30 70)" stroke="currentColor" stroke-width="1.5"/>
			</svg>
		</div>
		<?php
	}

	/**
	 * Render star icons.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @param string              $icon_class Icon color mode class.
	 * @return void
	 */
	protected function render_stars( $settings, $icon_class ) {
		$count = isset( $settings['rating_stars_count'] ) ? absint( $settings['rating_stars_count'] ) : 5;
		$count = max( 1, min( 10, $count ) );
		$icon  = ! empty( $settings['rating_star_icon'] ) ? $settings['rating_star_icon'] : array();

		if ( empty( $icon['value'] ) ) {
			return;
		}
		?>
		<div class="webmz-zwb__stars <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true">
			<?php for ( $i = 0; $i < $count; $i++ ) : ?>
				<span class="webmz-zwb__star">
					<?php Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) ); ?>
				</span>
			<?php endfor; ?>
		</div>
		<?php
	}

	/**
	 * Render product logos.
	 *
	 * @param array<int,array<string,mixed>> $logos Logo repeater items.
	 * @return void
	 */
	protected function render_logos( $logos ) {
		if ( empty( $logos ) || ! is_array( $logos ) ) {
			return;
		}

		$has_logo = false;
		foreach ( $logos as $logo ) {
			if ( ! empty( $logo['logo_image']['url'] ) ) {
				$has_logo = true;
				break;
			}
		}

		if ( ! $has_logo ) {
			return;
		}
		?>
		<div class="webmz-zwb__logos">
			<?php foreach ( $logos as $logo ) : ?>
				<?php
				$url = ! empty( $logo['logo_image']['url'] ) ? (string) $logo['logo_image']['url'] : '';
				if ( '' === $url ) {
					continue;
				}
				$alt = ! empty( $logo['logo_alt'] ) ? (string) $logo['logo_alt'] : '';
				?>
				<img class="webmz-zwb__logo" src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async" />
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render security badges.
	 *
	 * @param array<int,array<string,mixed>> $badges Badge repeater items.
	 * @param string                         $icon_class Icon color mode class.
	 * @return void
	 */
	protected function render_badges( $badges, $icon_class ) {
		if ( empty( $badges ) || ! is_array( $badges ) ) {
			return;
		}
		?>
		<div class="webmz-zwb__badges">
			<?php foreach ( $badges as $index => $badge ) : ?>
				<?php
				$text  = isset( $badge['badge_text'] ) ? trim( (string) $badge['badge_text'] ) : '';
				$start = ! empty( $badge['badge_gradient_start'] ) ? (string) $badge['badge_gradient_start'] : '#7c3aed';
				$end   = ! empty( $badge['badge_gradient_end'] ) ? (string) $badge['badge_gradient_end'] : '#ec4899';
				$style = sprintf( 'background:linear-gradient(90deg,%s,%s);', esc_attr( $start ), esc_attr( $end ) );
				?>
				<span class="webmz-zwb__badge" style="<?php echo esc_attr( $style ); ?>">
					<?php if ( ! empty( $badge['badge_icon']['value'] ) ) : ?>
						<span class="webmz-zwb__badge-icon <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true">
							<?php Icons_Manager::render_icon( $badge['badge_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
					<?php if ( '' !== $text ) : ?>
						<span class="webmz-zwb__badge-text"><?php echo esc_html( $text ); ?></span>
					<?php endif; ?>
				</span>
			<?php endforeach; ?>
		</div>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$show_heading   = ! empty( $settings['show_heading'] ) && 'yes' === $settings['show_heading'];
		$show_rating    = ! empty( $settings['show_rating'] ) && 'yes' === $settings['show_rating'];
		$show_products  = ! empty( $settings['show_products'] ) && 'yes' === $settings['show_products'];
		$show_guarantee = ! empty( $settings['show_guarantee'] ) && 'yes' === $settings['show_guarantee'];
		$show_security  = ! empty( $settings['show_security'] ) && 'yes' === $settings['show_security'];

		if ( ! $show_heading && ! $show_rating && ! $show_products && ! $show_guarantee && ! $show_security ) {
			return;
		}

		$title_tag           = $this->webmz_get_title_tag( $settings, 'heading_title_tag' );
		$star_icon_class     = $this->webmz_get_icon_color_mode_class( $settings, 'rating_star_icon_color_mode' );
		$guarantee_icon_class = $this->webmz_get_icon_color_mode_class( $settings, 'guarantee_icon_color_mode' );
		$badge_icon_class    = $this->webmz_get_icon_color_mode_class( $settings, 'security_badge_icon_color_mode' );
		$stack               = ! empty( $settings['mobile_stack'] ) && 'yes' === $settings['mobile_stack'];
		$classes             = 'webmz-zwb' . ( $stack ? ' webmz-zwb--stack-mobile' : '' );

		$heading_title    = isset( $settings['heading_title'] ) ? trim( (string) $settings['heading_title'] ) : '';
		$heading_subtitle = isset( $settings['heading_subtitle'] ) ? trim( (string) $settings['heading_subtitle'] ) : '';
		$rating_value     = isset( $settings['rating_value'] ) ? trim( (string) $settings['rating_value'] ) : '';
		$rating_label     = isset( $settings['rating_label'] ) ? trim( (string) $settings['rating_label'] ) : '';
		$products_count   = isset( $settings['products_count'] ) ? trim( (string) $settings['products_count'] ) : '';
		$products_label   = isset( $settings['products_label'] ) ? trim( (string) $settings['products_label'] ) : '';
		$guarantee_label  = isset( $settings['guarantee_label'] ) ? trim( (string) $settings['guarantee_label'] ) : '';
		$security_label   = isset( $settings['security_label'] ) ? trim( (string) $settings['security_label'] ) : '';
		$product_logos      = ! empty( $settings['product_logos'] ) && is_array( $settings['product_logos'] ) ? $settings['product_logos'] : array();
		$security_badges    = ! empty( $settings['security_badges'] ) && is_array( $settings['security_badges'] ) ? $settings['security_badges'] : array();
		$show_decoration    = ! empty( $settings['show_decoration'] ) && 'yes' === $settings['show_decoration'];
		$guarantee_icon_type = isset( $settings['guarantee_icon_type'] ) ? (string) $settings['guarantee_icon_type'] : 'icon';
		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<div class="webmz-zwb__box">
				<div class="webmz-zwb__grid">
					<?php if ( $show_heading ) : ?>
						<div class="webmz-zwb__col webmz-zwb__col--heading">
							<div class="webmz-zwb__heading">
								<?php if ( $show_decoration ) : ?>
									<?php $this->render_decoration(); ?>
								<?php endif; ?>
								<div class="webmz-zwb__heading-content">
									<?php if ( '' !== $heading_title ) : ?>
										<<?php echo esc_html( $title_tag ); ?> class="webmz-zwb__heading-title"><?php echo esc_html( $heading_title ); ?></<?php echo esc_html( $title_tag ); ?>>
									<?php endif; ?>
									<?php if ( '' !== $heading_subtitle ) : ?>
										<p class="webmz-zwb__heading-subtitle"><?php echo esc_html( $heading_subtitle ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( $show_rating ) : ?>
						<div class="webmz-zwb__col webmz-zwb__col--rating">
							<div class="webmz-zwb__cell">
								<?php if ( '' !== $rating_value ) : ?>
									<div class="webmz-zwb__stat"><?php echo esc_html( $rating_value ); ?></div>
								<?php endif; ?>
								<?php $this->render_stars( $settings, $star_icon_class ); ?>
								<?php if ( '' !== $rating_label ) : ?>
									<div class="webmz-zwb__label"><?php echo esc_html( $rating_label ); ?></div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( $show_products ) : ?>
						<div class="webmz-zwb__col webmz-zwb__col--products">
							<div class="webmz-zwb__cell">
								<?php if ( '' !== $products_count ) : ?>
									<div class="webmz-zwb__stat"><?php echo esc_html( $products_count ); ?></div>
								<?php endif; ?>
								<?php $this->render_logos( $product_logos ); ?>
								<?php if ( '' !== $products_label ) : ?>
									<div class="webmz-zwb__label"><?php echo esc_html( $products_label ); ?></div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( $show_guarantee ) : ?>
						<div class="webmz-zwb__col webmz-zwb__col--guarantee">
							<div class="webmz-zwb__cell">
								<div class="webmz-zwb__guarantee-icon-wrap">
									<?php if ( 'image' === $guarantee_icon_type && ! empty( $settings['guarantee_image']['url'] ) ) : ?>
										<img class="webmz-zwb__guarantee-image" src="<?php echo esc_url( $settings['guarantee_image']['url'] ); ?>" alt="" loading="lazy" decoding="async" />
									<?php elseif ( ! empty( $settings['guarantee_icon']['value'] ) ) : ?>
										<span class="webmz-zwb__guarantee-icon <?php echo esc_attr( $guarantee_icon_class ); ?>" aria-hidden="true">
											<?php Icons_Manager::render_icon( $settings['guarantee_icon'], array( 'aria-hidden' => 'true' ) ); ?>
										</span>
									<?php endif; ?>
								</div>
								<?php if ( '' !== $guarantee_label ) : ?>
									<div class="webmz-zwb__label"><?php echo esc_html( $guarantee_label ); ?></div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( $show_security ) : ?>
						<div class="webmz-zwb__col webmz-zwb__col--security">
							<div class="webmz-zwb__cell">
								<?php $this->render_badges( $security_badges, $badge_icon_class ); ?>
								<?php if ( '' !== $security_label ) : ?>
									<div class="webmz-zwb__label"><?php echo esc_html( $security_label ); ?></div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
