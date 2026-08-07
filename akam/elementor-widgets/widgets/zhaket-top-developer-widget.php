<?php
/**
 * Zhaket top developer widget — featured vendor showcase with stats and products.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * RTL marketplace-style top developer box (Zhaket).
 */
class Zhaket_Top_Developer_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-zhaket-top-developer';
	}

	public function get_title() {
		return esc_html__( 'توسعه‌دهنده برتر ژاکت', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return array( 'webmz-shop-widgets' );
	}

	public function get_keywords() {
		return array( 'zhaket', 'developer', 'vendor', 'author', 'ژاکت', 'توسعه‌دهنده', 'فروشنده', 'نویسنده' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-zhaket-top-developer' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-zhaket-top-developer' );
	}

	/**
	 * Theme primary color.
	 *
	 * @return string
	 */
	private function theme_primary_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_primary' ) : '#0878f9';
	}

	/**
	 * Theme secondary color.
	 *
	 * @return string
	 */
	private function theme_secondary_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_secondary' ) : '#092c4c';
	}

	/**
	 * Theme text gray color.
	 *
	 * @return string
	 */
	private function theme_text_gray_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_text_gray' ) : '#64748b';
	}

	protected function register_controls() {
		$this->register_developer_controls();
		$this->register_stats_controls();
		$this->register_products_controls();
		$this->register_cta_controls();
		$this->register_slider_controls();
		$this->register_style_controls();
	}

	protected function register_developer_controls() {
		$this->start_controls_section(
			'section_developer',
			array(
				'label' => esc_html__( 'توسعه‌دهنده', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'author_id',
			array(
				'label'       => esc_html__( 'انتخاب نویسنده', 'tadris' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => function_exists( 'webmz_zhaket_get_author_options' ) ? webmz_zhaket_get_author_options() : array(),
				'label_block' => true,
			)
		);

		$this->add_control(
			'section_title',
			array(
				'label'       => esc_html__( 'عنوان بخش', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'توسعه‌دهنده برتر هفته', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'section_icon',
			array(
				'label'   => esc_html__( 'آیکون عنوان', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-fire',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'name_source',
			array(
				'label'   => esc_html__( 'نام توسعه‌دهنده', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'author',
				'options' => array(
					'author' => esc_html__( 'از نویسنده انتخاب‌شده', 'tadris' ),
					'manual' => esc_html__( 'دستی', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'developer_name_manual',
			array(
				'label'       => esc_html__( 'نام دستی', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => array( 'name_source' => 'manual' ),
			)
		);

		$this->add_control(
			'avatar_source',
			array(
				'label'   => esc_html__( 'تصویر پروفایل', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'author',
				'options' => array(
					'author' => esc_html__( 'آواتار نویسنده', 'tadris' ),
					'custom' => esc_html__( 'تصویر سفارشی', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'avatar_custom',
			array(
				'label'     => esc_html__( 'تصویر سفارشی', 'tadris' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'avatar_source' => 'custom' ),
			)
		);

		$this->add_control(
			'profile_link',
			array(
				'label'       => esc_html__( 'لینک پروفایل', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => esc_html__( 'خالی = آرشیو نویسنده', 'tadris' ),
				'default'     => array( 'url' => '' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control( 'developer_title_tag', esc_html__( 'تگ HTML عنوان بخش', 'tadris' ) );

		$this->end_controls_section();
	}

	protected function register_stats_controls() {
		$this->start_controls_section(
			'section_stats',
			array(
				'label' => esc_html__( 'آمار فروشنده', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_stats',
			array(
				'label'        => esc_html__( 'نمایش آمار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'آیکون', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-shopping-cart',
					'library' => 'fa-solid',
				),
			)
		);

		$repeater->add_control(
			'value_source',
			array(
				'label'   => esc_html__( 'منبع مقدار', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'wp',
				'options' => array(
					'wp'     => esc_html__( 'از وردپرس / ووکامرس', 'tadris' ),
					'manual' => esc_html__( 'دستی', 'tadris' ),
				),
			)
		);

		$repeater->add_control(
			'wp_key',
			array(
				'label'     => esc_html__( 'فیلد وردپرس', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'total_sales',
				'options'   => array(
					'total_sales'    => esc_html__( 'تعداد فروش', 'tadris' ),
					'products_count' => esc_html__( 'تعداد محصولات', 'tadris' ),
					'total_revenue'  => esc_html__( 'درآمد تقریبی', 'tadris' ),
					'avg_rating'     => esc_html__( 'میانگین امتیاز', 'tadris' ),
					'reviews_count'  => esc_html__( 'تعداد دیدگاه', 'tadris' ),
				),
				'condition' => array( 'value_source' => 'wp' ),
			)
		);

		$repeater->add_control(
			'manual_value',
			array(
				'label'       => esc_html__( 'مقدار دستی', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '0',
				'label_block' => true,
				'condition'   => array( 'value_source' => 'manual' ),
			)
		);

		$repeater->add_control(
			'number_format',
			array(
				'label'   => esc_html__( 'قالب عدد', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'number',
				'options' => array(
					'plain'  => esc_html__( 'متن ساده', 'tadris' ),
					'number' => esc_html__( 'عدد با جداکننده', 'tadris' ),
				),
			)
		);

		$repeater->add_control(
			'decimals',
			array(
				'label'     => esc_html__( 'اعشار', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'min'       => 0,
				'max'       => 2,
				'condition' => array( 'number_format' => 'number' ),
			)
		);

		$repeater->add_control(
			'value_prefix',
			array(
				'label' => esc_html__( 'پیشوند', 'tadris' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'value_suffix',
			array(
				'label' => esc_html__( 'پسوند', 'tadris' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'badge_tooltip',
			array(
				'label'       => esc_html__( 'متن تولتیپ', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => esc_html__( 'با هاور روی نشان نمایش داده می‌شود.', 'tadris' ),
			)
		);

		$repeater->add_control(
			'badge_color',
			array(
				'label'   => esc_html__( 'رنگ نشان', 'tadris' ),
				'type'    => Controls_Manager::COLOR,
				'default' => 'var(--webmz-color-primary)',
			)
		);

		$this->add_control(
			'stats_items',
			array(
				'label'       => esc_html__( 'نشان‌های آمار', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->get_default_stats_items(),
				'title_field' => '{{{ badge_tooltip || (value_source === "manual" ? manual_value : wp_key) }}}',
				'condition'   => array( 'show_stats' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Default stat ribbon items.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private function get_default_stats_items() {
		return array(
			array(
				'icon'          => array( 'value' => 'fas fa-shopping-cart', 'library' => 'fa-solid' ),
				'value_source'  => 'wp',
				'wp_key'        => 'total_sales',
				'number_format' => 'number',
				'badge_tooltip' => esc_html__( 'تعداد کل فروش محصولات', 'tadris' ),
				'badge_color'   => 'var(--webmz-color-primary)',
			),
			array(
				'icon'          => array( 'value' => 'fas fa-box', 'library' => 'fa-solid' ),
				'value_source'  => 'wp',
				'wp_key'        => 'products_count',
				'number_format' => 'number',
				'badge_tooltip' => esc_html__( 'تعداد محصولات منتشرشده', 'tadris' ),
				'badge_color'   => '#ff6b4a',
			),
			array(
				'icon'          => array( 'value' => 'fas fa-coins', 'library' => 'fa-solid' ),
				'value_source'  => 'wp',
				'wp_key'        => 'total_revenue',
				'number_format' => 'number',
				'value_suffix'  => '',
				'badge_tooltip' => esc_html__( 'درآمد تقریبی از فروش', 'tadris' ),
				'badge_color'   => '#ff4d8d',
			),
			array(
				'icon'          => array( 'value' => 'fas fa-star', 'library' => 'fa-solid' ),
				'value_source'  => 'wp',
				'wp_key'        => 'avg_rating',
				'number_format' => 'number',
				'decimals'      => 1,
				'badge_tooltip' => esc_html__( 'میانگین امتیاز محصولات', 'tadris' ),
				'badge_color'   => '#3b82f6',
			),
			array(
				'icon'          => array( 'value' => 'fas fa-comments', 'library' => 'fa-solid' ),
				'value_source'  => 'wp',
				'wp_key'        => 'reviews_count',
				'number_format' => 'number',
				'badge_tooltip' => esc_html__( 'تعداد دیدگاه‌های دریافتی', 'tadris' ),
				'badge_color'   => '#8b5cf6',
			),
			array(
				'icon'          => array( 'value' => 'fas fa-trophy', 'library' => 'fa-solid' ),
				'value_source'  => 'manual',
				'manual_value'  => '1',
				'number_format' => 'plain',
				'badge_tooltip' => esc_html__( 'رتبه در بین توسعه‌دهندگان', 'tadris' ),
				'badge_color'   => '#22c55e',
			),
			array(
				'icon'          => array( 'value' => 'fas fa-clock', 'library' => 'fa-solid' ),
				'value_source'  => 'manual',
				'manual_value'  => '5+',
				'number_format' => 'plain',
				'badge_tooltip' => esc_html__( 'سابقه فعالیت در ژاکت', 'tadris' ),
				'badge_color'   => '#6366f1',
			),
		);
	}

	protected function register_products_controls() {
		$this->start_controls_section(
			'section_products',
			array(
				'label' => esc_html__( 'محصولات', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_products',
			array(
				'label'        => esc_html__( 'نمایش محصولات', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'products_count',
			array(
				'label'     => esc_html__( 'تعداد محصولات', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 8,
				'min'       => 1,
				'max'       => 20,
				'condition' => array( 'show_products' => 'yes' ),
			)
		);

		$this->add_control(
			'products_order_by',
			array(
				'label'     => esc_html__( 'مرتب‌سازی', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'popularity',
				'options'   => array(
					'popularity' => esc_html__( 'پرفروش‌ترین', 'tadris' ),
					'rating'     => esc_html__( 'بیشترین امتیاز', 'tadris' ),
					'date'       => esc_html__( 'جدیدترین', 'tadris' ),
					'title'      => esc_html__( 'عنوان', 'tadris' ),
					'rand'       => esc_html__( 'تصادفی', 'tadris' ),
				),
				'condition' => array( 'show_products' => 'yes' ),
			)
		);

		$this->add_control(
			'product_subtitle_source',
			array(
				'label'     => esc_html__( 'زیرعنوان کارت', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'badge',
				'options'   => array(
					'badge' => esc_html__( 'متن متا (لوپ فایل‌ها)', 'tadris' ),
					'excerpt' => esc_html__( 'خلاصه محصول', 'tadris' ),
					'none'  => esc_html__( 'بدون زیرعنوان', 'tadris' ),
				),
				'condition' => array( 'show_products' => 'yes' ),
			)
		);

		$this->add_control(
			'product_link',
			array(
				'label'        => esc_html__( 'لینک به صفحه محصول', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'show_products' => 'yes' ),
			)
		);

		$this->add_control(
			'show_product_nav',
			array(
				'label'        => esc_html__( 'دکمه ناوبری', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array( 'show_products' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_cta_controls() {
		$this->start_controls_section(
			'section_cta',
			array(
				'label' => esc_html__( 'بخش کسب درآمد', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_cta',
			array(
				'label'        => esc_html__( 'نمایش بخش CTA', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'cta_code_image',
			array(
				'label'     => esc_html__( 'تصویر کد / هدر', 'tadris' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array( 'show_cta' => 'yes' ),
			)
		);

		$this->add_control(
			'cta_badge_image',
			array(
				'label'     => esc_html__( 'نشان / آیکون روی تصویر', 'tadris' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'show_cta' => 'yes' ),
			)
		);

		$this->add_control(
			'cta_title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دنبال کسب درآمد هستید؟', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_cta' => 'yes' ),
			)
		);

		$this->add_control(
			'cta_subtitle',
			array(
				'label'       => esc_html__( 'زیرعنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'در ژاکت توسعه‌دهنده شوید', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_cta' => 'yes' ),
			)
		);

		$this->add_control(
			'cta_button_text',
			array(
				'label'       => esc_html__( 'متن دکمه ثبت‌نام', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'ثبت نام', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_cta' => 'yes' ),
			)
		);

		$this->add_control(
			'cta_button_link',
			array(
				'label'       => esc_html__( 'لینک ثبت‌نام', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#' ),
				'label_block' => true,
				'condition'   => array( 'show_cta' => 'yes' ),
			)
		);

		$this->add_control(
			'cta_more_text',
			array(
				'label'       => esc_html__( 'متن لینک اطلاعات بیشتر', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'اطلاعات بیشتر', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_cta' => 'yes' ),
			)
		);

		$this->add_control(
			'cta_more_link',
			array(
				'label'       => esc_html__( 'لینک اطلاعات بیشتر', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#' ),
				'label_block' => true,
				'condition'   => array( 'show_cta' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_slider_controls() {
		$this->start_controls_section(
			'section_slider',
			array(
				'label'     => esc_html__( 'تنظیمات اسلایدر', 'tadris' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'show_products' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'slides_per_view',
			array(
				'label'          => esc_html__( 'تعداد کارت در هر ردیف', 'tadris' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 4,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'min'            => 1,
				'max'            => 6,
			)
		);

		$this->add_control(
			'slides_gap',
			array(
				'label'   => esc_html__( 'فاصله کارت‌ها (px)', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 14,
				'min'     => 0,
				'max'     => 40,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'پخش خودکار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => esc_html__( 'تأخیر پخش خودکار (ms)', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4500,
				'min'       => 2000,
				'max'       => 12000,
				'step'      => 500,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => esc_html__( 'حلقه بی‌نهایت', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'section_style_box',
			array(
				'label' => esc_html__( 'باکس اصلی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'box_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary-light)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'box_border_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'size' => 20, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ztd' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .webmz-ztd',
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => '24',
					'right'  => '24',
					'bottom' => '24',
					'left'   => '24',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ztd' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_developer',
			array(
				'label' => esc_html__( 'توسعه‌دهنده', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'dev_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان بخش', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-gray)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__section-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'dev_title_typography',
				'selector' => '{{WRAPPER}} .webmz-ztd__section-title',
			)
		);

		$this->add_control(
			'dev_name_color',
			array(
				'label'     => esc_html__( 'رنگ نام', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-dark)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__dev-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'dev_name_typography',
				'selector' => '{{WRAPPER}} .webmz-ztd__dev-name',
			)
		);

		$this->add_control(
			'dev_icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__section-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'dev_avatar_size',
			array(
				'label'      => esc_html__( 'اندازه آواتار', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 40, 'max' => 120 ) ),
				'default'    => array( 'size' => 64, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ztd' => '--webmz-ztd-avatar-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_stats',
			array(
				'label'     => esc_html__( 'آمار', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_stats' => 'yes' ),
			)
		);

		$this->add_control(
			'stats_value_color',
			array(
				'label'     => esc_html__( 'رنگ عدد', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__stat-value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'stats_icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__stat-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'stats_badge_size',
			array(
				'label'      => esc_html__( 'عرض نشان', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 44, 'max' => 80 ) ),
				'default'    => array( 'size' => 56, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ztd' => '--webmz-ztd-stat-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'stats_badge_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه نشان', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 24 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ztd' => '--webmz-ztd-stat-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'stats_tooltip_heading',
			array(
				'label'     => esc_html__( 'تولتیپ', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'stats_tooltip_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه تولتیپ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-secondary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd' => '--webmz-ztd-tooltip-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'stats_tooltip_color',
			array(
				'label'     => esc_html__( 'رنگ متن تولتیپ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__stat-tooltip' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_cta',
			array(
				'label'     => esc_html__( 'بخش کسب درآمد', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_cta' => 'yes' ),
			)
		);

		$this->add_control(
			'cta_divider_color',
			array(
				'label'     => esc_html__( 'رنگ جداکننده', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'color-mix(in srgb, var(--webmz-color-text-gray) 18%, transparent)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__cta' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cta_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-dark)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__cta-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cta_subtitle_color',
			array(
				'label'     => esc_html__( 'رنگ زیرعنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-gray)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__cta-subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cta_btn_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه دکمه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__cta-btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cta_btn_color',
			array(
				'label'     => esc_html__( 'رنگ متن دکمه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary-light)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__cta-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cta_more_color',
			array(
				'label'     => esc_html__( 'رنگ لینک بیشتر', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-gray)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__cta-more' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_products',
			array(
				'label'     => esc_html__( 'محصولات', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_products' => 'yes' ),
			)
		);

		$this->add_control(
			'product_card_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه کارت', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__product-card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'product_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان محصول', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-dark)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__product-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'product_subtitle_color',
			array(
				'label'     => esc_html__( 'رنگ زیرعنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-gray)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__product-subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_btn_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه دکمه ناوبری', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__nav-btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_btn_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون ناوبری', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-dark)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztd__nav-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Resolve developer display name.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return string
	 */
	private function get_developer_name( $settings ) {
		if ( 'manual' === ( $settings['name_source'] ?? 'author' ) ) {
			return isset( $settings['developer_name_manual'] ) ? (string) $settings['developer_name_manual'] : '';
		}

		$author_id = absint( $settings['author_id'] ?? 0 );

		if ( ! $author_id ) {
			return '';
		}

		$user = get_userdata( $author_id );

		return $user ? $user->display_name : '';
	}

	/**
	 * Resolve profile URL.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return string
	 */
	private function get_profile_url( $settings ) {
		$link = isset( $settings['profile_link'] ) ? $settings['profile_link'] : array();

		if ( ! empty( $link['url'] ) ) {
			return (string) $link['url'];
		}

		$author_id = absint( $settings['author_id'] ?? 0 );

		if ( ! $author_id ) {
			return '';
		}

		return get_author_posts_url( $author_id );
	}

	/**
	 * Render developer avatar.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_avatar( $settings ) {
		$author_id = absint( $settings['author_id'] ?? 0 );

		if ( 'custom' === ( $settings['avatar_source'] ?? 'author' ) ) {
			$url = ! empty( $settings['avatar_custom']['url'] ) ? (string) $settings['avatar_custom']['url'] : '';

			if ( $url ) {
				printf(
					'<img class="webmz-ztd__avatar" src="%s" alt="" loading="lazy" decoding="async">',
					esc_url( $url )
				);
				return;
			}
		}

		if ( $author_id ) {
			echo get_avatar( $author_id, 128, '', '', array( 'class' => 'webmz-ztd__avatar' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Build slider config JSON.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @param int                 $count    Slide count.
	 * @return array<string,mixed>
	 */
	private function get_slider_config( $settings, $count ) {
		$wants_loop = isset( $settings['loop'] ) && 'yes' === $settings['loop'];

		return array(
			'slidesPerView' => array(
				'desktop' => max( 1, (int) ( $settings['slides_per_view'] ?? 4 ) ),
				'tablet'  => max( 1, (int) ( $settings['slides_per_view_tablet'] ?? 3 ) ),
				'mobile'  => max( 1, (int) ( $settings['slides_per_view_mobile'] ?? 2 ) ),
			),
			'spaceBetween'  => absint( $settings['slides_gap'] ?? 14 ),
			'autoplay'      => isset( $settings['autoplay'] ) && 'yes' === $settings['autoplay'],
			'autoplayDelay' => absint( $settings['autoplay_delay'] ?? 4500 ),
			'loop'          => $wants_loop && $count > 1,
		);
	}

	/**
	 * Get product card subtitle.
	 *
	 * @param int                 $product_id Product ID.
	 * @param array<string,mixed> $settings   Widget settings.
	 * @return string
	 */
	private function get_product_subtitle( $product_id, $settings ) {
		$source = $settings['product_subtitle_source'] ?? 'badge';

		if ( 'none' === $source ) {
			return '';
		}

		if ( 'excerpt' === $source ) {
			$product = wc_get_product( $product_id );

			if ( $product ) {
				return wp_trim_words( wp_strip_all_tags( $product->get_short_description() ), 12, '…' );
			}

			return '';
		}

		if ( function_exists( 'webmz_pll_get_product_badge_text' ) ) {
			return webmz_pll_get_product_badge_text( $product_id );
		}

		return '';
	}

	/**
	 * Render product thumb.
	 *
	 * @param int $product_id Product ID.
	 * @return void
	 */
	private function render_product_thumb( $product_id ) {
		$url = '';

		if ( function_exists( 'webmz_pll_get_product_thumb_url' ) ) {
			$url = webmz_pll_get_product_thumb_url( $product_id, 'medium' );
		}

		if ( ! $url ) {
			$url = get_the_post_thumbnail_url( $product_id, webmz_get_loop_image_size() );
		}

		if ( $url ) {
			printf(
				'<img class="webmz-ztd__product-thumb" src="%s" alt="" loading="lazy" decoding="async">',
				esc_url( $url )
			);
		} else {
			echo '<span class="webmz-ztd__product-thumb-placeholder" aria-hidden="true"></span>';
		}
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$author_id = absint( $settings['author_id'] ?? 0 );
		$wp_stats  = function_exists( 'webmz_zhaket_top_developer_get_wp_stats' )
			? webmz_zhaket_top_developer_get_wp_stats( $author_id )
			: array();

		$dev_name    = $this->get_developer_name( $settings );
		$profile_url = $this->get_profile_url( $settings );
		$title_tag   = $this->webmz_get_title_tag( $settings, 'developer_title_tag' );

		$product_ids = array();

		if ( 'yes' === ( $settings['show_products'] ?? 'yes' ) && $author_id && function_exists( 'webmz_zhaket_top_developer_get_product_ids' ) ) {
			$product_ids = webmz_zhaket_top_developer_get_product_ids(
				$author_id,
				array(
					'count'    => $settings['products_count'] ?? 8,
					'order_by' => $settings['products_order_by'] ?? 'popularity',
				)
			);
		}

		$slider_config = $this->get_slider_config( $settings, count( $product_ids ) );
		$show_cta      = 'yes' === ( $settings['show_cta'] ?? 'yes' );
		$show_stats    = 'yes' === ( $settings['show_stats'] ?? 'yes' );
		$show_products = 'yes' === ( $settings['show_products'] ?? 'yes' ) && ! empty( $product_ids );
		?>
		<div class="webmz-ztd" dir="rtl">
			<div class="webmz-ztd__grid<?php echo $show_cta ? '' : ' webmz-ztd__grid--no-cta'; ?>">
				<?php if ( $show_cta ) : ?>
					<aside class="webmz-ztd__cta">
						<?php $this->render_cta( $settings ); ?>
					</aside>
				<?php endif; ?>

				<div class="webmz-ztd__main">
					<div class="webmz-ztd__header">
						<div class="webmz-ztd__dev">
							<div class="webmz-ztd__dev-avatar-wrap">
								<?php $this->render_avatar( $settings ); ?>
							</div>

							<div class="webmz-ztd__dev-text">
								<<?php echo esc_attr( $title_tag ); ?> class="webmz-ztd__section-title">
									<?php if ( ! empty( $settings['section_icon']['value'] ) ) : ?>
										<span class="webmz-ztd__section-icon" aria-hidden="true">
											<?php Icons_Manager::render_icon( $settings['section_icon'], array( 'aria-hidden' => 'true' ) ); ?>
										</span>
									<?php endif; ?>
									<span><?php echo esc_html( $settings['section_title'] ?? '' ); ?></span>
								</<?php echo esc_attr( $title_tag ); ?>>

								<?php if ( $dev_name ) : ?>
									<?php if ( $profile_url ) : ?>
										<a class="webmz-ztd__dev-name" href="<?php echo esc_url( $profile_url ); ?>">
											<span><?php echo esc_html( $dev_name ); ?></span>
											<svg class="webmz-ztd__dev-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
										</a>
									<?php else : ?>
										<div class="webmz-ztd__dev-name">
											<span><?php echo esc_html( $dev_name ); ?></span>
										</div>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>

						<?php if ( $show_stats && ! empty( $settings['stats_items'] ) ) : ?>
							<div class="webmz-ztd__stats" role="list">
								<?php foreach ( $settings['stats_items'] as $item ) : ?>
									<?php
									$value = function_exists( 'webmz_zhaket_top_developer_resolve_stat_value' )
										? webmz_zhaket_top_developer_resolve_stat_value( $item, $wp_stats )
										: '';

									if ( '' === $value ) {
										continue;
									}

									$badge_color = ! empty( $item['badge_color'] ) ? (string) $item['badge_color'] : $this->theme_primary_color();
									$tooltip     = isset( $item['badge_tooltip'] ) ? trim( (string) $item['badge_tooltip'] ) : '';
									?>
									<div
										class="webmz-ztd__stat-wrap"
										role="listitem"
										<?php echo $tooltip ? ' tabindex="0"' : ''; ?>
										<?php echo $tooltip ? ' aria-label="' . esc_attr( $tooltip ) . '"' : ''; ?>
									>
										<?php if ( $tooltip ) : ?>
											<span class="webmz-ztd__stat-tooltip" role="tooltip"><?php echo esc_html( $tooltip ); ?></span>
										<?php endif; ?>
										<div class="webmz-ztd__stat" style="--webmz-ztd-stat-color: <?php echo esc_attr( $badge_color ); ?>">
											<span class="webmz-ztd__stat-icon" aria-hidden="true">
												<?php Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) ); ?>
											</span>
											<span class="webmz-ztd__stat-value"><?php echo esc_html( $value ); ?></span>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>

					<?php if ( $show_products ) : ?>
						<div class="webmz-ztd__products-wrap">
							<?php if ( 'yes' === ( $settings['show_product_nav'] ?? 'yes' ) ) : ?>
								<button type="button" class="webmz-ztd__nav-btn webmz-ztd__nav-btn--prev" aria-label="<?php esc_attr_e( 'قبلی', 'tadris' ); ?>">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
								</button>
							<?php endif; ?>

							<div
								class="swiper webmz-ztd__swiper"
								data-webmz-zhaket-top-developer="<?php echo esc_attr( wp_json_encode( $slider_config ) ); ?>"
							>
								<div class="swiper-wrapper">
									<?php foreach ( $product_ids as $product_id ) : ?>
										<?php
										$product = wc_get_product( $product_id );

										if ( ! $product ) {
											continue;
										}

										$subtitle   = $this->get_product_subtitle( $product_id, $settings );
										$permalink  = get_permalink( $product_id );
										$use_link   = 'yes' === ( $settings['product_link'] ?? 'yes' );
										$tag        = $use_link ? 'a' : 'div';
										$href_attr  = $use_link ? ' href="' . esc_url( $permalink ) . '"' : '';
										?>
										<div class="swiper-slide">
											<<?php echo esc_attr( $tag ); ?> class="webmz-ztd__product-card"<?php echo $href_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
												<div class="webmz-ztd__product-thumb-wrap">
													<?php $this->render_product_thumb( $product_id ); ?>
												</div>
												<div class="webmz-ztd__product-body">
													<span class="webmz-ztd__product-title"><?php echo esc_html( $product->get_name() ); ?></span>
													<?php if ( $subtitle ) : ?>
														<span class="webmz-ztd__product-subtitle"><?php echo esc_html( $subtitle ); ?></span>
													<?php endif; ?>
												</div>
											</<?php echo esc_attr( $tag ); ?>>
										</div>
									<?php endforeach; ?>
								</div>
							</div>

							<?php if ( 'yes' === ( $settings['show_product_nav'] ?? 'yes' ) ) : ?>
								<button type="button" class="webmz-ztd__nav-btn webmz-ztd__nav-btn--next" aria-label="<?php esc_attr_e( 'بعدی', 'tadris' ); ?>">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
								</button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render CTA column.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_cta( $settings ) {
		$code_url  = ! empty( $settings['cta_code_image']['url'] ) ? (string) $settings['cta_code_image']['url'] : '';
		$badge_url = ! empty( $settings['cta_badge_image']['url'] ) ? (string) $settings['cta_badge_image']['url'] : '';
		$btn_link  = isset( $settings['cta_button_link'] ) ? $settings['cta_button_link'] : array();
		$more_link = isset( $settings['cta_more_link'] ) ? $settings['cta_more_link'] : array();
		?>
		<?php if ( $code_url ) : ?>
			<div class="webmz-ztd__cta-visual">
				<img class="webmz-ztd__cta-code" src="<?php echo esc_url( $code_url ); ?>" alt="" loading="lazy" decoding="async">
				<?php if ( $badge_url ) : ?>
					<img class="webmz-ztd__cta-badge" src="<?php echo esc_url( $badge_url ); ?>" alt="" loading="lazy" decoding="async">
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $settings['cta_title'] ) ) : ?>
			<h4 class="webmz-ztd__cta-title"><?php echo esc_html( $settings['cta_title'] ); ?></h4>
		<?php endif; ?>

		<?php if ( ! empty( $settings['cta_subtitle'] ) ) : ?>
			<p class="webmz-ztd__cta-subtitle"><?php echo esc_html( $settings['cta_subtitle'] ); ?></p>
		<?php endif; ?>

		<div class="webmz-ztd__cta-actions">
			<?php if ( ! empty( $settings['cta_button_text'] ) && ! empty( $btn_link['url'] ) ) : ?>
				<a
					class="webmz-ztd__cta-btn"
					href="<?php echo esc_url( $btn_link['url'] ); ?>"
					<?php echo ! empty( $btn_link['is_external'] ) ? ' target="_blank"' : ''; ?>
					<?php echo ! empty( $btn_link['nofollow'] ) ? ' rel="nofollow"' : ''; ?>
				>
					<?php echo esc_html( $settings['cta_button_text'] ); ?>
				</a>
			<?php endif; ?>

			<?php if ( ! empty( $settings['cta_more_text'] ) && ! empty( $more_link['url'] ) ) : ?>
				<a
					class="webmz-ztd__cta-more"
					href="<?php echo esc_url( $more_link['url'] ); ?>"
					<?php echo ! empty( $more_link['is_external'] ) ? ' target="_blank"' : ''; ?>
					<?php echo ! empty( $more_link['nofollow'] ) ? ' rel="nofollow"' : ''; ?>
				>
					<?php echo esc_html( $settings['cta_more_text'] ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}
