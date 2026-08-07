<?php
/**
 * Zhaket blog post loop — colored cards with full-bleed image and content below.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * Blog cards in Zhaket marketplace style (colored background, white content box).
 */
class Zhaket_Blog_Loop_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-zhaket-blog-loop';
	}

	public function get_title() {
		return esc_html__( 'لوپ مقالات ژاکت', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'zhaket', 'blog', 'loop', 'post', 'ژاکت', 'مقاله', 'وبلاگ', 'لوپ' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-zhaket-blog-loop' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-zhaket-blog-loop' );
	}

	/**
	 * Theme option helper.
	 *
	 * @param string $key      Option key.
	 * @param string $fallback Fallback value.
	 * @return string
	 */
	private function theme_color( $key, $fallback = '' ) {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( $key ) : $fallback;
	}

	protected function register_controls() {
		$this->register_header_controls();
		$this->register_query_controls();
		$this->register_card_content_controls();
		$this->register_style_controls();
	}

	protected function register_header_controls() {
		$this->start_controls_section(
			'section_header',
			array(
				'label' => esc_html__( 'هدینگ بخش', 'tadris' ),
			)
		);

		$this->add_control(
			'show_section_title',
			array(
				'label'        => esc_html__( 'نمایش عنوان بخش', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_badge',
			array(
				'label'        => esc_html__( 'نمایش نشان آیکون', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'show_section_title' => 'yes' ),
			)
		);

		$this->add_control(
			'badge_icon',
			array(
				'label'     => esc_html__( 'آیکون نشان', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-pen',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_section_title' => 'yes',
					'show_badge'         => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_text',
			array(
				'label'       => esc_html__( 'متن عنوان بخش', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'آخرین مطالب وبلاگ، همیشه به‌روز باشید', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_section_title' => 'yes' ),
			)
		);

		$this->webmz_register_title_tag_control(
			'heading_tag',
			esc_html__( 'تگ HTML عنوان بخش', 'tadris' ),
			array( 'show_section_title' => 'yes' )
		);

		$this->add_control(
			'show_button',
			array(
				'label'        => esc_html__( 'نمایش دکمه مشاهده بلاگ', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'متن دکمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'مشاهده بلاگ', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'       => esc_html__( 'لینک دکمه', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => get_post_type_archive_link( 'post' ) ?: '#' ),
				'label_block' => true,
				'condition'   => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_control(
			'mobile_stack_header',
			array(
				'label'        => esc_html__( 'چیدمان عمودی هدینگ در موبایل', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function register_query_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'کوئری مقالات', 'tadris' ),
			)
		);

		$this->add_control(
			'category_id',
			array(
				'label'   => esc_html__( 'دسته‌بندی', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'options' => \webmz_tadris_get_post_category_options(),
				'default' => '',
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'تعداد مقاله', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4,
				'min'     => 1,
				'max'     => 48,
			)
		);

		$this->add_control(
			'exclude_posts',
			array(
				'label'       => esc_html__( 'حذف نوشته‌ها از لوپ', 'tadris' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => \webmz_tadris_get_post_options(),
				'label_block' => true,
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'   => esc_html__( 'مرتب‌سازی', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'          => esc_html__( 'جدیدترین', 'tadris' ),
					'title'         => esc_html__( 'عنوان', 'tadris' ),
					'comment_count' => esc_html__( 'تعداد دیدگاه', 'tadris' ),
					'modified'      => esc_html__( 'آخرین ویرایش', 'tadris' ),
					'rand'          => esc_html__( 'تصادفی', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'ترتیب', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => esc_html__( 'نزولی', 'tadris' ),
					'ASC'  => esc_html__( 'صعودی', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'display_type',
			array(
				'label'   => esc_html__( 'نحوه نمایش', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid'   => esc_html__( 'گرید', 'tadris' ),
					'slider' => esc_html__( 'اسلایدی (Swiper)', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'grid_columns_heading',
			array(
				'label'     => esc_html__( 'تنظیمات گرید', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'display_type' => 'grid' ),
			)
		);

		foreach (
			array(
				'desktop' => array( 'label' => esc_html__( 'تعداد ستون دسکتاپ', 'tadris' ), 'default' => '4', 'max' => 6 ),
				'tablet'  => array( 'label' => esc_html__( 'تعداد ستون تبلت', 'tadris' ), 'default' => '2', 'max' => 4 ),
				'mobile'  => array( 'label' => esc_html__( 'تعداد ستون موبایل', 'tadris' ), 'default' => '1', 'max' => 2 ),
			) as $device => $config
		) {
			$options = array();
			for ( $i = 1; $i <= $config['max']; $i++ ) {
				$options[ (string) $i ] = (string) $i;
			}

			$this->add_control(
				'grid_columns_' . $device,
				array(
					'label'     => $config['label'],
					'type'      => Controls_Manager::SELECT,
					'default'   => $config['default'],
					'options'   => $options,
					'condition' => array( 'display_type' => 'grid' ),
				)
			);
		}

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'فاصله بین کارت‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbl' => '--webmz-zbl-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'slider_heading',
			array(
				'label'     => esc_html__( 'تنظیمات اسلایدر', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'display_type' => 'slider' ),
			)
		);

		foreach (
			array(
				'desktop' => array( 'label' => esc_html__( 'تعداد اسلاید دسکتاپ', 'tadris' ), 'default' => 4 ),
				'tablet'  => array( 'label' => esc_html__( 'تعداد اسلاید تبلت', 'tadris' ), 'default' => 2 ),
				'mobile'  => array( 'label' => esc_html__( 'تعداد اسلاید موبایل', 'tadris' ), 'default' => 1 ),
			) as $device => $config
		) {
			$this->add_control(
				'slides_' . $device,
				array(
					'label'     => $config['label'],
					'type'      => Controls_Manager::NUMBER,
					'default'   => $config['default'],
					'min'       => 1,
					'max'       => 6,
					'condition' => array( 'display_type' => 'slider' ),
				)
			);
		}

		$this->add_control(
			'slider_loop',
			array(
				'label'        => esc_html__( 'لوپ اسلایدر', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'display_type' => 'slider' ),
			)
		);

		$this->add_control(
			'slider_autoplay',
			array(
				'label'        => esc_html__( 'پخش خودکار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'display_type' => 'slider' ),
			)
		);

		$this->add_control(
			'slider_autoplay_delay',
			array(
				'label'     => esc_html__( 'زمان پخش خودکار (میلی‌ثانیه)', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4000,
				'min'       => 1000,
				'step'      => 100,
				'condition' => array(
					'display_type'    => 'slider',
					'slider_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_slider_nav',
			array(
				'label'        => esc_html__( 'نمایش فلش‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array( 'display_type' => 'slider' ),
			)
		);

		$this->add_control(
			'show_slider_pagination',
			array(
				'label'        => esc_html__( 'نمایش صفحه‌بندی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'display_type' => 'slider' ),
			)
		);

		$this->add_control(
			'slider_grab_cursor',
			array(
				'label'        => esc_html__( 'نشانگر درگ', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'display_type' => 'slider' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_card_content_controls() {
		$this->start_controls_section(
			'section_card_content',
			array(
				'label' => esc_html__( 'محتوای کارت', 'tadris' ),
			)
		);

		$this->add_control(
			'show_title',
			array(
				'label'        => esc_html__( 'نمایش عنوان مقاله', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->webmz_register_title_tag_control(
			'title_tag',
			esc_html__( 'تگ HTML عنوان مقاله', 'tadris' ),
			array( 'show_title' => 'yes' )
		);

		$this->add_control(
			'title_lines',
			array(
				'label'     => esc_html__( 'حداکثر خطوط عنوان', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '2',
				'options'   => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'0' => esc_html__( 'نامحدود', 'tadris' ),
				),
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'show_reading_time',
			array(
				'label'        => esc_html__( 'نمایش زمان مطالعه', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'words_per_minute',
			array(
				'label'   => esc_html__( 'کلمات در دقیقه', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 200,
				'min'     => 80,
				'max'     => 400,
			)
		);

		$this->add_control(
			'reading_time_suffix',
			array(
				'label'     => esc_html__( 'پسوند زمان مطالعه', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'زمان مطالعه', 'tadris' ),
				'condition' => array( 'show_reading_time' => 'yes' ),
			)
		);

		$this->add_control(
			'reading_icon',
			array(
				'label'     => esc_html__( 'آیکون زمان مطالعه', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'far fa-clock',
					'library' => 'fa-regular',
				),
				'condition' => array( 'show_reading_time' => 'yes' ),
			)
		);

		$this->add_control(
			'image_grayscale',
			array(
				'label'        => esc_html__( 'تصویر شاخص سیاه‌وسفید', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => esc_html__( 'اندازه تصویر شاخص', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'large',
				'options' => array(
					'thumbnail'    => esc_html__( 'بندانگشتی', 'tadris' ),
					'medium'       => esc_html__( 'متوسط', 'tadris' ),
					'medium_large' => esc_html__( 'متوسط بزرگ', 'tadris' ),
					'large'        => esc_html__( 'بزرگ', 'tadris' ),
					'full'         => esc_html__( 'کامل', 'tadris' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'section_colors',
			array(
				'label' => esc_html__( 'رنگ کارت‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'use_theme_palette',
			array(
				'label'        => esc_html__( 'پالت از رنگ‌های پوسته', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'رنگ‌های کارت از تنظیمات پوسته (رنگ اصلی، ثانویه و ...) به‌صورت خودکار ساخته می‌شوند.', 'tadris' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'card_color',
			array(
				'label'   => esc_html__( 'رنگ پس‌زمینه کارت', 'tadris' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#ddd6f3',
			)
		);

		$this->add_control(
			'card_palette',
			array(
				'label'       => esc_html__( 'پالت سفارشی', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'card_color' => '#ddd6f3' ),
					array( 'card_color' => '#ffdca8' ),
					array( 'card_color' => '#f472b6' ),
					array( 'card_color' => '#cdef8e' ),
				),
				'title_field' => esc_html__( 'رنگ کارت', 'tadris' ) . ' {{{ card_color }}}',
				'condition'   => array( 'use_theme_palette!' => 'yes' ),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_heading_icon_box_style_controls(
			'.webmz-zbl__badge',
			array(
				'condition'     => array(
					'show_section_title' => 'yes',
					'show_badge'         => 'yes',
				),
				'bg_default'    => $this->theme_color( 'color_primary', '#0878f9' ),
				'icon_default'  => $this->theme_color( 'color_primary_light', '#ffffff' ),
				'use_icon_mode' => false,
			)
		);

		$this->start_controls_section(
			'section_header_style',
			array(
				'label' => esc_html__( 'هدینگ بخش', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'header_spacing',
			array(
				'label'      => esc_html__( 'فاصله هدینگ تا لوپ', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbl__header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'section_heading_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان بخش', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_text_dark', '#111827' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbl__heading' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_section_title' => 'yes' ),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'section_heading_typography',
				'selector'  => '{{WRAPPER}} .webmz-zbl__heading',
				'condition' => array( 'show_section_title' => 'yes' ),
			)
		);

		$this->add_control(
			'button_background_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه دکمه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbl__button' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'show_button' => 'yes' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن دکمه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_text_gray', '#64748b' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbl__button' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_control(
			'button_border_color',
			array(
				'label'     => esc_html__( 'رنگ حاشیه دکمه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15, 23, 42, 0.12)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbl__button' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'button_typography',
				'selector'  => '{{WRAPPER}} .webmz-zbl__button',
				'condition' => array( 'show_button' => 'yes' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => esc_html__( 'کارت مقاله', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه کارت', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 48 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbl__card-inner' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'content_box_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه باکس عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbl__card-box' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_box_radius',
			array(
				'label'      => esc_html__( 'گردی باکس سفید', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 32 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbl__card-box' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_box_min_height',
			array(
				'label'      => esc_html__( 'حداقل ارتفاع باکس سفید', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 60, 'max' => 200 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 88,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zbl__card-box' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'card_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان مقاله', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_text_dark', '#111827' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbl__card-title' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_title' => 'yes' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'card_title_typography',
				'selector'  => '{{WRAPPER}} .webmz-zbl__card-title',
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'reading_time_color',
			array(
				'label'     => esc_html__( 'رنگ زمان مطالعه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_text_gray', '#64748b' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbl__card-reading' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_reading_time' => 'yes' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'reading_time_typography',
				'selector'  => '{{WRAPPER}} .webmz-zbl__card-reading',
				'condition' => array( 'show_reading_time' => 'yes' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_style',
			array(
				'label'     => esc_html__( 'اسلایدر', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'display_type' => 'slider' ),
			)
		);

		$this->add_control(
			'slider_nav_color',
			array(
				'label'     => esc_html__( 'رنگ فلش‌ها', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_primary', '#0878f9' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbl__arrow' => 'color: {{VALUE}};',
				),
				'condition' => array( 'show_slider_nav' => 'yes' ),
			)
		);

		$this->add_control(
			'slider_pagination_color',
			array(
				'label'     => esc_html__( 'رنگ صفحه‌بندی فعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_color( 'color_primary', '#0878f9' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zbl__pagination .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
				),
				'condition' => array( 'show_slider_pagination' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build WP_Query args.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<string,mixed>
	 */
	private function query_args( $settings ) {
		$allowed_orderby = array( 'date', 'title', 'comment_count', 'modified', 'rand' );
		$orderby         = ! empty( $settings['order_by'] ) && in_array( $settings['order_by'], $allowed_orderby, true ) ? $settings['order_by'] : 'date';
		$order           = ! empty( $settings['order'] ) && in_array( strtoupper( $settings['order'] ), array( 'ASC', 'DESC' ), true ) ? strtoupper( $settings['order'] ) : 'DESC';

		$args = array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => min( 48, max( 1, ! empty( $settings['posts_per_page'] ) ? absint( $settings['posts_per_page'] ) : 4 ) ),
			'orderby'                => sanitize_key( $orderby ),
			'order'                  => $order,
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
		);

		if ( ! empty( $settings['category_id'] ) ) {
			$args['cat'] = absint( $settings['category_id'] );
		}

		if ( ! empty( $settings['exclude_posts'] ) && is_array( $settings['exclude_posts'] ) ) {
			$args['post__not_in'] = array_values( array_filter( array_map( 'absint', $settings['exclude_posts'] ) ) );
		}

		return $args;
	}

	/**
	 * Resolve card background colors.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<int,string>
	 */
	private function get_card_colors( $settings ) {
		if ( ! empty( $settings['use_theme_palette'] ) && 'yes' === $settings['use_theme_palette'] ) {
			return array(
				'var(--webmz-zbl-theme-1)',
				'var(--webmz-zbl-theme-2)',
				'var(--webmz-zbl-theme-3)',
				'var(--webmz-zbl-theme-4)',
			);
		}

		$colors = array();

		if ( ! empty( $settings['card_palette'] ) && is_array( $settings['card_palette'] ) ) {
			foreach ( $settings['card_palette'] as $item ) {
				if ( ! empty( $item['card_color'] ) ) {
					$colors[] = $item['card_color'];
				}
			}
		}

		if ( empty( $colors ) ) {
			$colors = array( '#ddd6f3', '#ffdca8', '#f472b6', '#cdef8e' );
		}

		return $colors;
	}

	/**
	 * Format reading time label.
	 *
	 * @param int                 $post_id  Post ID.
	 * @param array<string,mixed> $settings Widget settings.
	 * @return string
	 */
	private function get_reading_time_label( $post_id, $settings ) {
		$wpm     = ! empty( $settings['words_per_minute'] ) ? absint( $settings['words_per_minute'] ) : 200;
		$minutes = function_exists( 'webmz_tadris_get_post_reading_time' )
			? webmz_tadris_get_post_reading_time( $post_id, $wpm )
			: 1;
		$suffix  = ! empty( $settings['reading_time_suffix'] ) ? $settings['reading_time_suffix'] : esc_html__( 'زمان مطالعه', 'tadris' );
		$label   = trim( number_format_i18n( $minutes ) . ' ' . esc_html__( 'دقیقه', 'tadris' ) . ' ' . $suffix );

		return function_exists( 'webmz_to_persian_digits' ) ? webmz_to_persian_digits( $label ) : $label;
	}

	/**
	 * Render placeholder when post has no thumbnail.
	 *
	 * @param string $title Post title.
	 * @return void
	 */
	private function render_placeholder_image( $title ) {
		$letter = function_exists( 'mb_substr' ) ? mb_substr( $title, 0, 1 ) : substr( $title, 0, 1 );
		?>
		<div class="webmz-zbl__card-placeholder" aria-hidden="true"><?php echo esc_html( $letter ); ?></div>
		<?php
	}

	/**
	 * Render one blog card.
	 *
	 * @param int                 $post_id     Post ID.
	 * @param array<string,mixed> $settings    Widget settings.
	 * @param string              $card_color  Card background color.
	 * @param int                 $card_index  Card index.
	 * @return void
	 */
	private function render_blog_card( $post_id, $settings, $card_color, $card_index ) {
		$post_id      = absint( $post_id );
		$title        = get_the_title( $post_id );
		$link         = get_permalink( $post_id );
		$title_tag    = $this->webmz_get_title_tag( $settings, 'title_tag' );
		$show_title   = ! isset( $settings['show_title'] ) || 'yes' === $settings['show_title'];
		$show_reading = ! isset( $settings['show_reading_time'] ) || 'yes' === $settings['show_reading_time'];
		$grayscale    = ! isset( $settings['image_grayscale'] ) || 'yes' === $settings['image_grayscale'];
		$title_lines  = isset( $settings['title_lines'] ) ? (string) $settings['title_lines'] : '2';
		$title_class  = 'webmz-zbl__card-title';

		if ( '0' !== $title_lines ) {
			$title_class .= ' webmz-zbl__card-title--lines-' . sanitize_html_class( $title_lines );
		}

		$card_classes = 'webmz-zbl__card';
		if ( $grayscale ) {
			$card_classes .= ' webmz-zbl__card--grayscale';
		}
		?>
		<article
			class="<?php echo esc_attr( $card_classes ); ?>"
			data-post-id="<?php echo esc_attr( (string) $post_id ); ?>"
			style="--webmz-zbl-card-bg: <?php echo esc_attr( $card_color ); ?>;"
		>
			<a class="webmz-zbl__card-link" href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
				<div class="webmz-zbl__card-inner">
					<div class="webmz-zbl__card-media">
						<div class="webmz-zbl__card-image">
							<?php
							if ( has_post_thumbnail( $post_id ) ) {
							echo webmz_get_post_loop_thumbnail(
								$post_id,
								array(
									'alt' => esc_attr( $title ),
								)
							); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} else {
								$this->render_placeholder_image( $title );
							}
							?>
						</div>
					</div>

					<?php if ( $show_title || $show_reading ) : ?>
						<div class="webmz-zbl__card-box">
							<?php if ( $show_title ) : ?>
								<<?php echo esc_attr( $title_tag ); ?> class="<?php echo esc_attr( $title_class ); ?>">
									<?php echo esc_html( $title ); ?>
								</<?php echo esc_attr( $title_tag ); ?>>
							<?php endif; ?>

							<?php if ( $show_reading ) : ?>
								<div class="webmz-zbl__card-meta">
									<span class="webmz-zbl__card-reading">
										<?php Icons_Manager::render_icon( $settings['reading_icon'], array( 'aria-hidden' => 'true' ) ); ?>
										<span><?php echo esc_html( $this->get_reading_time_label( $post_id, $settings ) ); ?></span>
									</span>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</a>
		</article>
		<?php
	}

	/**
	 * Render section header.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_header( $settings ) {
		$show_title  = ! empty( $settings['show_section_title'] ) && 'yes' === $settings['show_section_title'];
		$show_badge  = $show_title && ! empty( $settings['show_badge'] ) && 'yes' === $settings['show_badge'];
		$show_button = ! empty( $settings['show_button'] ) && 'yes' === $settings['show_button'];
		$heading     = isset( $settings['heading_text'] ) ? trim( (string) $settings['heading_text'] ) : '';
		$stack       = ! empty( $settings['mobile_stack_header'] ) && 'yes' === $settings['mobile_stack_header'];

		if ( ! $show_title && ! $show_button ) {
			return;
		}

		$header_class = 'webmz-zbl__header' . ( $stack ? ' webmz-zbl__header--stack-mobile' : '' );
		$title_tag    = $this->webmz_get_title_tag( $settings, 'heading_tag' );
		?>
		<div class="<?php echo esc_attr( $header_class ); ?>">
			<?php if ( $show_title ) : ?>
				<div class="webmz-zbl__header-lead">
					<?php if ( $show_badge && ! empty( $settings['badge_icon']['value'] ) ) : ?>
						<span class="webmz-zbl__badge" aria-hidden="true">
							<?php Icons_Manager::render_icon( $settings['badge_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>

					<?php if ( '' !== $heading ) : ?>
						<<?php echo esc_attr( $title_tag ); ?> class="webmz-zbl__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_attr( $title_tag ); ?>>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $show_button && ! empty( $settings['button_text'] ) ) : ?>
				<?php
				$this->add_render_attribute( 'blog_button', 'class', 'webmz-zbl__button' );
				if ( ! empty( $settings['button_link']['url'] ) ) {
					$this->add_link_attributes( 'blog_button', $settings['button_link'] );
				} else {
					$this->add_render_attribute( 'blog_button', 'href', '#' );
				}
				?>
				<a <?php echo $this->get_render_attribute_string( 'blog_button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php echo esc_html( $settings['button_text'] ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function render() {
		$settings     = $this->get_settings_for_display();
		$query        = new WP_Query( $this->query_args( $settings ) );
		$is_slider    = isset( $settings['display_type'] ) && 'slider' === $settings['display_type'];
		$card_colors  = $this->get_card_colors( $settings );
		$color_count  = count( $card_colors );
		$use_theme    = ! empty( $settings['use_theme_palette'] ) && 'yes' === $settings['use_theme_palette'];
		$wrapper_cls  = 'webmz-zbl';
		if ( $use_theme ) {
			$wrapper_cls .= ' webmz-zbl--theme-palette';
		}

		if ( ! $query->have_posts() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'مقاله‌ای برای نمایش یافت نشد.', 'tadris' ) . '</div>';
			return;
		}

		$gap = isset( $settings['grid_gap']['size'] ) ? absint( $settings['grid_gap']['size'] ) : 20;
		?>
		<div class="<?php echo esc_attr( $wrapper_cls ); ?>" style="--webmz-zbl-gap: <?php echo esc_attr( (string) $gap ); ?>px;">
			<?php $this->render_header( $settings ); ?>

			<?php if ( $is_slider ) : ?>
				<?php
				$slider_conf = array(
					'slidesDesktop' => ! empty( $settings['slides_desktop'] ) ? absint( $settings['slides_desktop'] ) : 4,
					'slidesTablet'  => ! empty( $settings['slides_tablet'] ) ? absint( $settings['slides_tablet'] ) : 2,
					'slidesMobile'  => ! empty( $settings['slides_mobile'] ) ? absint( $settings['slides_mobile'] ) : 1,
					'spaceBetween'  => $gap,
					'loop'          => isset( $settings['slider_loop'] ) && 'yes' === $settings['slider_loop'],
					'autoplay'      => isset( $settings['slider_autoplay'] ) && 'yes' === $settings['slider_autoplay'],
					'autoplayDelay' => ! empty( $settings['slider_autoplay_delay'] ) ? absint( $settings['slider_autoplay_delay'] ) : 4000,
					'navigation'    => isset( $settings['show_slider_nav'] ) && 'yes' === $settings['show_slider_nav'],
					'pagination'    => ! isset( $settings['show_slider_pagination'] ) || 'yes' === $settings['show_slider_pagination'],
					'grabCursor'    => ! isset( $settings['slider_grab_cursor'] ) || 'yes' === $settings['slider_grab_cursor'],
				);
				?>
				<div class="webmz-zbl__slider-wrap">
					<div
						class="webmz-zbl__slider swiper"
						data-webmz-zhaket-blog-loop="<?php echo esc_attr( wp_json_encode( $slider_conf ) ); ?>"
					>
						<div class="swiper-wrapper">
							<?php
							$index = 0;
							while ( $query->have_posts() ) :
								$query->the_post();
								$color = $card_colors[ $index % $color_count ];
								++$index;
								?>
								<div class="swiper-slide">
									<?php $this->render_blog_card( get_the_ID(), $settings, $color, $index ); ?>
								</div>
							<?php endwhile; ?>
						</div>
					</div>

					<?php if ( $slider_conf['navigation'] ) : ?>
						<button type="button" class="webmz-zbl__arrow webmz-zbl__arrow--prev swiper-button-prev" aria-label="<?php echo esc_attr__( 'اسلاید قبلی', 'tadris' ); ?>"></button>
						<button type="button" class="webmz-zbl__arrow webmz-zbl__arrow--next swiper-button-next" aria-label="<?php echo esc_attr__( 'اسلاید بعدی', 'tadris' ); ?>"></button>
					<?php endif; ?>

					<?php if ( $slider_conf['pagination'] ) : ?>
						<div class="webmz-zbl__pagination swiper-pagination"></div>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div
					class="webmz-zbl__grid webmz-loop-grid"
					style="
						--webmz-grid-columns: <?php echo esc_attr( $settings['grid_columns_desktop'] ?? 4 ); ?>;
						--webmz-grid-tablet-columns: <?php echo esc_attr( $settings['grid_columns_tablet'] ?? 2 ); ?>;
						--webmz-grid-mobile-columns: <?php echo esc_attr( $settings['grid_columns_mobile'] ?? 1 ); ?>;
					"
				>
					<?php
					$index = 0;
					while ( $query->have_posts() ) :
						$query->the_post();
						$color = $card_colors[ $index % $color_count ];
						++$index;
						$this->render_blog_card( get_the_ID(), $settings, $color, $index );
					endwhile;
					?>
				</div>
			<?php endif; ?>
		</div>
		<?php

		wp_reset_postdata();
	}
}
