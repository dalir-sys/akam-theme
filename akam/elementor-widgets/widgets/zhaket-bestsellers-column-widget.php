<?php
/**
 * Zhaket bestsellers column — featured product + compact list per category column.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Multi-column bestsellers widget (Zhaket marketplace style).
 */
class Zhaket_Bestsellers_Column_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-zhaket-bestsellers-column';
	}

	public function get_title() {
		return esc_html__( 'ستون پرفروش‌ترین‌های ژاکت', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_categories() {
		return array( 'webmz-shop-widgets' );
	}

	public function get_keywords() {
		return array( 'zhaket', 'bestsellers', 'column', 'product', 'ژاکت', 'پرفروش', 'ستون', 'محصول' );
	}

	public function get_style_depends() {
		return array( 'webmz-zhaket-bestsellers-column' );
	}

	/**
	 * Theme primary color from options.
	 *
	 * @return string
	 */
	private function theme_primary_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_primary' ) : '#0878f9';
	}

	/**
	 * @return array<string,string>
	 */
	private function category_options() {
		$options = array(
			'' => esc_html__( 'همه دسته‌ها', 'tadris' ),
		);

		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}

		return $options;
	}

	/**
	 * @return array<string,string>
	 */
	private function tag_options() {
		$options = array(
			'' => esc_html__( 'همه برچسب‌ها', 'tadris' ),
		);

		$terms = get_terms(
			array(
				'taxonomy'   => 'product_tag',
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}

		return $options;
	}

	protected function register_controls() {
		$this->register_columns_controls();
		$this->register_display_controls();
		$this->register_layout_style_controls();
		$this->register_column_style_controls();
		$this->register_header_style_controls();
		$this->register_featured_style_controls();
		$this->register_list_style_controls();
		$this->register_meta_style_controls();
		$this->register_button_style_controls();
	}

	protected function register_columns_controls() {
		$this->start_controls_section(
			'section_columns',
			array(
				'label' => esc_html__( 'ستون‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'column_title',
			array(
				'label'       => esc_html__( 'عنوان ستون', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'پرفروش‌ترین افزونه‌ها', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'show_badge',
			array(
				'label'        => esc_html__( 'نمایش نشان آیکون', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$repeater->add_control(
			'badge_icon',
			array(
				'label'     => esc_html__( 'آیکون نشان', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-plug',
					'library' => 'fa-solid',
				),
				'condition' => array( 'show_badge' => 'yes' ),
			)
		);

		$repeater->add_control(
			'source',
			array(
				'label'     => esc_html__( 'منبع محصولات', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'category',
				'options'   => array(
					'category' => esc_html__( 'دسته محصول', 'tadris' ),
					'tag'      => esc_html__( 'برچسب محصول', 'tadris' ),
					'manual'   => esc_html__( 'انتخاب دستی', 'tadris' ),
				),
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'category',
			array(
				'label'     => esc_html__( 'دسته محصول', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->category_options(),
				'default'   => '',
				'condition' => array( 'source' => 'category' ),
			)
		);

		$repeater->add_control(
			'tag',
			array(
				'label'     => esc_html__( 'برچسب محصول', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->tag_options(),
				'default'   => '',
				'condition' => array( 'source' => 'tag' ),
			)
		);

		$repeater->add_control(
			'product_ids',
			array(
				'label'       => esc_html__( 'انتخاب محصولات', 'tadris' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => \webmz_tadris_get_product_options(),
				'multiple'    => true,
				'label_block' => true,
				'description' => esc_html__( 'حداکثر ۵ محصول. اولین مورد به‌عنوان محصول ویژه نمایش داده می‌شود.', 'tadris' ),
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$repeater->add_control(
			'count',
			array(
				'label'     => esc_html__( 'تعداد محصولات', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5,
				'min'       => 1,
				'max'       => 10,
				'condition' => array( 'source!' => 'manual' ),
			)
		);

		$repeater->add_control(
			'order_by',
			array(
				'label'     => esc_html__( 'مرتب‌سازی', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'popularity',
				'options'   => array(
					'popularity' => esc_html__( 'پرفروش‌ترین', 'tadris' ),
					'rating'     => esc_html__( 'بالاترین امتیاز', 'tadris' ),
					'date'       => esc_html__( 'جدیدترین', 'tadris' ),
					'rand'       => esc_html__( 'تصادفی', 'tadris' ),
				),
				'condition' => array( 'source!' => 'manual' ),
			)
		);

		$repeater->add_control(
			'show_button',
			array(
				'label'        => esc_html__( 'نمایش دکمه مشاهده همه', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$repeater->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'متن دکمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'مشاهده برترین افزونه‌ها', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_button' => 'yes' ),
			)
		);

		$repeater->add_control(
			'button_link',
			array(
				'label'       => esc_html__( 'لینک دکمه', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#' ),
				'label_block' => true,
				'condition'   => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'       => esc_html__( 'ستون‌ها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'column_title' => esc_html__( 'پرفروش‌ترین افزونه‌ها', 'tadris' ),
						'badge_icon'   => array(
							'value'   => 'fas fa-plug',
							'library' => 'fa-solid',
						),
						'button_text'  => esc_html__( 'مشاهده برترین افزونه‌ها', 'tadris' ),
					),
					array(
						'column_title' => esc_html__( 'پرفروش‌ترین قالب‌ها', 'tadris' ),
						'badge_icon'   => array(
							'value'   => 'fas fa-layer-group',
							'library' => 'fa-solid',
						),
						'button_text'  => esc_html__( 'مشاهده برترین قالب‌ها', 'tadris' ),
					),
					array(
						'column_title' => esc_html__( 'پرفروش‌ترین اسکریپت‌ها', 'tadris' ),
						'badge_icon'   => array(
							'value'   => 'fas fa-code',
							'library' => 'fa-solid',
						),
						'button_text'  => esc_html__( 'مشاهده برترین اسکریپت‌ها', 'tadris' ),
					),
				),
				'title_field' => '{{{ column_title }}}',
			)
		);

		$this->webmz_register_title_tag_control( 'column_title_tag', esc_html__( 'تگ HTML عنوان ستون', 'tadris' ) );

		$this->end_controls_section();
	}

	protected function register_display_controls() {
		$this->start_controls_section(
			'section_display',
			array(
				'label' => esc_html__( 'نمایش', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'product_link',
			array(
				'label'        => esc_html__( 'لینک محصولات به صفحه محصول', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_rating',
			array(
				'label'        => esc_html__( 'نمایش امتیاز', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_sales',
			array(
				'label'        => esc_html__( 'نمایش تعداد فروش', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'featured_image_size',
			array(
				'label'   => esc_html__( 'اندازه تصویر محصول ویژه', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'woocommerce_single',
				'options' => array(
					'medium'              => esc_html__( 'متوسط', 'tadris' ),
					'medium_large'        => esc_html__( 'متوسط بزرگ', 'tadris' ),
					'large'               => esc_html__( 'بزرگ', 'tadris' ),
					'woocommerce_single'  => esc_html__( 'تک محصول ووکامرس', 'tadris' ),
					'woocommerce_thumbnail' => esc_html__( 'بندانگشتی ووکامرس', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'list_thumb_size',
			array(
				'label'   => esc_html__( 'اندازه تامنیل لیست', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'thumbnail',
				'options' => array(
					'thumbnail' => esc_html__( 'کوچک', 'tadris' ),
					'medium'    => esc_html__( 'متوسط', 'tadris' ),
				),
				'description' => esc_html__( 'تامنیل لیست از متاباکس «تامنیل لوپ فایل‌ها» خوانده می‌شود.', 'tadris' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_layout_style_controls() {
		$this->start_controls_section(
			'section_layout_style',
			array(
				'label' => esc_html__( 'چیدمان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'columns_count',
			array(
				'label'          => esc_html__( 'تعداد ستون در هر ردیف', 'tadris' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors'      => array(
					'{{WRAPPER}} .webmz-zbsc__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'columns_gap',
			array(
				'label'      => esc_html__( 'فاصله بین ستون‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbsc__grid' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_column_style_controls() {
		$this->start_controls_section(
			'section_column_style',
			array(
				'label' => esc_html__( 'کارت ستون', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'column_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__column' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'column_border_color',
			array(
				'label'     => esc_html__( 'رنگ حاشیه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15, 23, 42, 0.08)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__column' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'column_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'      => '20',
					'right'    => '20',
					'bottom'   => '20',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbsc__column' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'column_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbsc__column' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'column_shadow',
				'selector' => '{{WRAPPER}} .webmz-zbsc__column',
			)
		);

		$this->end_controls_section();
	}

	protected function register_header_style_controls() {
		$this->webmz_register_heading_icon_box_style_controls(
			'.webmz-zbsc__badge',
			array(
				'bg_default'   => $this->theme_primary_color(),
				'icon_default' => function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_primary_light' ) : '#ffffff',
				'include_shadow' => true,
			)
		);

		$this->start_controls_section(
			'section_header_style',
			array(
				'label' => esc_html__( 'عنوان ستون', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'header_spacing',
			array(
				'label'      => esc_html__( 'فاصله از محتوا', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbsc__header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'column_title_typography',
				'selector' => '{{WRAPPER}} .webmz-zbsc__title',
			)
		);

		$this->add_control(
			'column_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-dark)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_featured_style_controls() {
		$this->start_controls_section(
			'section_featured_style',
			array(
				'label' => esc_html__( 'محصول ویژه', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'featured_spacing',
			array(
				'label'      => esc_html__( 'فاصله از لیست', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 14,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbsc__featured' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'featured_image_radius',
			array(
				'label'      => esc_html__( 'گردی تصویر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 30 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 8,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbsc__featured-media' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'featured_title_typography',
				'selector' => '{{WRAPPER}} .webmz-zbsc__featured-title',
			)
		);

		$this->add_control(
			'featured_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-dark)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__featured-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .webmz-zbsc__featured:hover .webmz-zbsc__featured-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'featured_title_hover_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__featured:hover .webmz-zbsc__featured-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'featured_divider_color',
			array(
				'label'     => esc_html__( 'رنگ جداکننده', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15, 23, 42, 0.08)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__featured' => 'border-bottom-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_list_style_controls() {
		$this->start_controls_section(
			'section_list_style',
			array(
				'label' => esc_html__( 'لیست محصولات', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'list_item_gap',
			array(
				'label'      => esc_html__( 'فاصله بین آیتم‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 30 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbsc__list-item + .webmz-zbsc__list-item' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'list_thumb_size',
			array(
				'label'      => esc_html__( 'اندازه تامنیل', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 32, 'max' => 80 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 48,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbsc__list-thumb' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'list_thumb_radius',
			array(
				'label'      => esc_html__( 'گردی تامنیل', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 24 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 8,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbsc__list-thumb' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'list_title_typography',
				'selector' => '{{WRAPPER}} .webmz-zbsc__list-title',
			)
		);

		$this->add_control(
			'list_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-dark)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__list-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'list_title_hover_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__list-link:hover .webmz-zbsc__list-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'list_divider_color',
			array(
				'label'     => esc_html__( 'رنگ جداکننده', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15, 23, 42, 0.08)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__list-item' => 'border-bottom-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_meta_style_controls() {
		$this->start_controls_section(
			'section_meta_style',
			array(
				'label' => esc_html__( 'امتیاز و فروش', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_typography',
				'selector' => '{{WRAPPER}} .webmz-zbsc__meta-value',
			)
		);

		$this->add_control(
			'meta_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-gray)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__meta-value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'rating_icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون امتیاز', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5c542',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__meta-item--rating' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sales_icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون فروش', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-gray)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__meta-item--sales' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_button_style_controls() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => esc_html__( 'دکمه مشاهده همه', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'button_spacing',
			array(
				'label'      => esc_html__( 'فاصله از لیست', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbsc__footer-btn' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .webmz-zbsc__footer-btn',
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => '12',
					'right'    => '16',
					'bottom'   => '12',
					'left'     => '16',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbsc__footer-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 30 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 8,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbsc__footer-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		$this->start_controls_tab(
			'button_normal_tab',
			array(
				'label' => esc_html__( 'عادی', 'tadris' ),
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-gray)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__footer-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__footer-btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_border_color',
			array(
				'label'     => esc_html__( 'رنگ حاشیه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15, 23, 42, 0.12)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__footer-btn' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_hover_tab',
			array(
				'label' => esc_html__( 'هاور', 'tadris' ),
			)
		);

		$this->add_control(
			'button_hover_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__footer-btn:hover, {{WRAPPER}} .webmz-zbsc__footer-btn:focus-visible' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_hover_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__footer-btn:hover, {{WRAPPER}} .webmz-zbsc__footer-btn:focus-visible' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_hover_border_color',
			array(
				'label'     => esc_html__( 'رنگ حاشیه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbsc__footer-btn:hover, {{WRAPPER}} .webmz-zbsc__footer-btn:focus-visible' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Resolve product IDs for one column.
	 *
	 * @param array<string,mixed> $item Column repeater item.
	 * @return array<int>
	 */
	private function get_column_product_ids( $item ) {
		$source = $item['source'] ?? 'category';

		if ( 'manual' === $source ) {
			$raw = isset( $item['product_ids'] ) ? $item['product_ids'] : array();

			if ( ! is_array( $raw ) ) {
				$raw = array( $raw );
			}

			return array_values( array_unique( array_filter( array_map( 'absint', $raw ) ) ) );
		}

		$count = min( 10, max( 1, absint( $item['count'] ?? 5 ) ) );

		if ( function_exists( 'webmz_zhaket_product_loop_query_args' ) ) {
			$query_settings = array(
				'count'     => $count,
				'order_by'  => $item['order_by'] ?? 'popularity',
				'filter_by' => 'category',
				'category'  => '',
				'tag'       => '',
			);

			if ( 'tag' === $source ) {
				$query_settings['filter_by'] = 'tag';
				$query_settings['tag']       = $item['tag'] ?? '';
			} else {
				$query_settings['category'] = $item['category'] ?? '';
			}

			$query = new \WP_Query( webmz_zhaket_product_loop_query_args( $query_settings ) );
		} else {
			$query = new \WP_Query( $this->fallback_query_args( $item, $count ) );
		}

		$ids = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$ids[] = get_the_ID();
			}
		}

		wp_reset_postdata();

		return $ids;
	}

	/**
	 * Fallback query when shared helper is unavailable.
	 *
	 * @param array<string,mixed> $item  Column item.
	 * @param int                 $count Product count.
	 * @return array<string,mixed>
	 */
	private function fallback_query_args( $item, $count ) {
		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $count,
			'no_found_rows'  => true,
		);

		$source = $item['source'] ?? 'category';

		if ( 'category' === $source && ! empty( $item['category'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => sanitize_title( (string) $item['category'] ),
				),
			);
		} elseif ( 'tag' === $source && ! empty( $item['tag'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_tag',
					'field'    => 'slug',
					'terms'    => sanitize_title( (string) $item['tag'] ),
				),
			);
		}

		$order_by = $item['order_by'] ?? 'popularity';

		switch ( $order_by ) {
			case 'popularity':
				$args['meta_key'] = 'total_sales';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;

			case 'rating':
				$args['meta_key'] = '_wc_average_rating';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;

			case 'rand':
				$args['orderby'] = 'rand';
				break;

			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
		}

		return $args;
	}

	/**
	 * Format number for display.
	 *
	 * @param float|int|string $number   Number.
	 * @param int              $decimals Decimal places.
	 * @return string
	 */
	private function format_number( $number, $decimals = 0 ) {
		if ( function_exists( 'webmz_zhaket_product_loop_format_number' ) ) {
			return webmz_zhaket_product_loop_format_number( $number, $decimals );
		}

		return number_format_i18n( (float) $number, $decimals );
	}

	/**
	 * Render rating and sales meta row.
	 *
	 * @param \WC_Product         $product  Product.
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_product_meta( $product, $settings ) {
		$show_rating = 'yes' === ( $settings['show_rating'] ?? 'yes' );
		$show_sales  = 'yes' === ( $settings['show_sales'] ?? 'yes' );

		if ( ! $show_rating && ! $show_sales ) {
			return;
		}
		?>
		<div class="webmz-zbsc__meta">
			<?php if ( $show_rating ) : ?>
				<span class="webmz-zbsc__meta-item webmz-zbsc__meta-item--rating" aria-label="<?php esc_attr_e( 'امتیاز', 'tadris' ); ?>">
					<svg class="webmz-zbsc__meta-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3.5l2.35 4.76 5.25.76-3.8 3.7.9 5.24L12 15.9l-4.7 2.46.9-5.24-3.8-3.7 5.25-.76L12 3.5z" fill="currentColor"/></svg>
					<span class="webmz-zbsc__meta-value"><?php echo esc_html( $this->format_number( $product->get_average_rating(), 1 ) ); ?></span>
				</span>
			<?php endif; ?>

			<?php if ( $show_sales ) : ?>
				<span class="webmz-zbsc__meta-item webmz-zbsc__meta-item--sales" aria-label="<?php esc_attr_e( 'تعداد فروش', 'tadris' ); ?>">
					<svg class="webmz-zbsc__meta-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6h15l-1.5 9h-12L6 6z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><circle cx="9" cy="19" r="1.4" fill="currentColor"/><circle cx="17" cy="19" r="1.4" fill="currentColor"/><path d="M6 6L5 3H2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
					<span class="webmz-zbsc__meta-value"><?php echo esc_html( $this->format_number( $product->get_total_sales() ) ); ?></span>
				</span>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render featured (first) product.
	 *
	 * @param \WC_Product         $product  Product.
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_featured_product( $product, $settings ) {
		$product_id  = $product->get_id();
		$title       = $product->get_name();
		$permalink   = $product->get_permalink();
		$image_size  = sanitize_key( (string) ( $settings['featured_image_size'] ?? 'woocommerce_single' ) );
		$image_id    = $product->get_image_id();
		$link        = 'yes' === ( $settings['product_link'] ?? 'yes' );
		$tag         = $link ? 'a' : 'div';
		$key         = 'zbsc_featured_' . $product_id;

		$this->add_render_attribute( $key, 'class', 'webmz-zbsc__featured' );

		if ( $link && $permalink ) {
			$this->add_render_attribute( $key, 'href', esc_url( $permalink ) );
		}
		?>
		<<?php echo tag_escape( $tag ); ?> <?php echo $this->get_render_attribute_string( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="webmz-zbsc__featured-media">
				<?php
				if ( $image_id ) {
					echo webmz_get_attachment_loop_image(
						$image_id,
						array(
							'class' => 'webmz-zbsc__featured-img',
							'alt'   => $title,
						)
					);
				} elseif ( function_exists( 'wc_placeholder_img' ) ) {
					echo wc_placeholder_img(
						$image_size ? $image_size : 'woocommerce_single',
						array( 'class' => 'webmz-zbsc__featured-img' )
					);
				}
				?>
			</div>
			<h4 class="webmz-zbsc__featured-title"><?php echo esc_html( $title ); ?></h4>
			<?php $this->render_product_meta( $product, $settings ); ?>
		</<?php echo tag_escape( $tag ); ?>>
		<?php
	}

	/**
	 * Render one list item product.
	 *
	 * @param \WC_Product         $product  Product.
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_list_product( $product, $settings ) {
		$product_id = $product->get_id();
		$title      = $product->get_name();
		$permalink  = $product->get_permalink();
		$thumb_size = sanitize_key( (string) ( $settings['list_thumb_size'] ?? 'thumbnail' ) );
		$thumb_url  = function_exists( 'webmz_pll_get_product_thumb_url' )
			? webmz_pll_get_product_thumb_url( $product_id, $thumb_size )
			: get_the_post_thumbnail_url( $product_id, webmz_get_loop_image_size() );
		$link       = 'yes' === ( $settings['product_link'] ?? 'yes' );
		$tag        = $link ? 'a' : 'div';
		$key        = 'zbsc_list_' . $product_id;

		$this->add_render_attribute( $key, 'class', 'webmz-zbsc__list-link' );

		if ( $link && $permalink ) {
			$this->add_render_attribute( $key, 'href', esc_url( $permalink ) );
		}
		?>
		<li class="webmz-zbsc__list-item">
			<<?php echo tag_escape( $tag ); ?> <?php echo $this->get_render_attribute_string( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<span class="webmz-zbsc__list-body">
					<span class="webmz-zbsc__list-title"><?php echo esc_html( $title ); ?></span>
					<?php $this->render_product_meta( $product, $settings ); ?>
				</span>
				<span class="webmz-zbsc__list-thumb">
					<?php if ( $thumb_url ) : ?>
						<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" decoding="async">
					<?php else : ?>
						<span class="webmz-zbsc__list-thumb-placeholder" aria-hidden="true"></span>
					<?php endif; ?>
				</span>
			</<?php echo tag_escape( $tag ); ?>>
		</li>
		<?php
	}

	/**
	 * Render column footer button.
	 *
	 * @param array<string,mixed> $item Column repeater item.
	 * @param int                 $index Column index.
	 * @return void
	 */
	private function render_column_button( $item, $index ) {
		if ( 'yes' !== ( $item['show_button'] ?? 'yes' ) ) {
			return;
		}

		$text = isset( $item['button_text'] ) ? trim( (string) $item['button_text'] ) : '';

		if ( '' === $text ) {
			return;
		}

		$url_settings = ! empty( $item['button_link'] ) && is_array( $item['button_link'] ) ? $item['button_link'] : array();
		$key          = 'zbsc_button_' . $index;

		$this->add_render_attribute( $key, 'class', 'webmz-zbsc__footer-btn' );

		if ( ! empty( $url_settings['url'] ) ) {
			$this->add_link_attributes( $key, $url_settings );
		} else {
			$this->add_render_attribute( $key, 'href', '#' );
		}
		?>
		<a <?php echo $this->get_render_attribute_string( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php echo esc_html( $text ); ?>
		</a>
		<?php
	}

	/**
	 * Render one column.
	 *
	 * @param array<string,mixed> $item     Column repeater item.
	 * @param int                 $index    Column index.
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_column( $item, $index, $settings ) {
		$product_ids = $this->get_column_product_ids( $item );
		$title       = isset( $item['column_title'] ) ? trim( (string) $item['column_title'] ) : '';
		$show_badge  = 'yes' === ( $item['show_badge'] ?? 'yes' );
		$title_tag   = $this->webmz_get_title_tag( $settings, 'column_title_tag' );

		if ( empty( $product_ids ) ) {
			return;
		}
		?>
		<div class="webmz-zbsc__column">
			<?php if ( '' !== $title || ( $show_badge && ! empty( $item['badge_icon']['value'] ) ) ) : ?>
				<header class="webmz-zbsc__header">
					<?php if ( $show_badge && ! empty( $item['badge_icon']['value'] ) ) : ?>
						<span class="webmz-zbsc__badge" aria-hidden="true">
							<?php Icons_Manager::render_icon( $item['badge_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>

					<?php if ( '' !== $title ) : ?>
						<<?php echo esc_html( $title_tag ); ?> class="webmz-zbsc__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $title_tag ); ?>>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<?php
			$first_id = array_shift( $product_ids );
			$first    = function_exists( 'wc_get_product' ) ? wc_get_product( $first_id ) : null;

			if ( $first && $first->is_visible() ) {
				$this->render_featured_product( $first, $settings );
			}

			if ( ! empty( $product_ids ) ) :
				?>
				<ul class="webmz-zbsc__list">
					<?php
					foreach ( $product_ids as $product_id ) {
						$product = wc_get_product( $product_id );

						if ( ! $product || ! $product->is_visible() ) {
							continue;
						}

						$this->render_list_product( $product, $settings );
					}
					?>
				</ul>
			<?php endif; ?>

			<?php $this->render_column_button( $item, $index ); ?>
		</div>
		<?php
	}

	protected function render() {
		if ( ! function_exists( 'wc_get_product' ) ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'برای استفاده از این ویجت، ووکامرس باید فعال باشد.', 'tadris' ) . '</div>';
			return;
		}

		$settings = $this->get_settings_for_display();
		$columns  = isset( $settings['columns'] ) && is_array( $settings['columns'] ) ? $settings['columns'] : array();

		if ( empty( $columns ) ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'حداقل یک ستون اضافه کنید.', 'tadris' ) . '</div>';
			return;
		}
		?>
		<div class="webmz-zbsc">
			<div class="webmz-zbsc__grid">
				<?php
				foreach ( $columns as $index => $item ) {
					$this->render_column( $item, $index, $settings );
				}
				?>
			</div>
		</div>
		<?php
	}
}
