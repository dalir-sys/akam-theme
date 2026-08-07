<?php
/**
 * Related posts grid widget for WebMZ template builder layouts.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * Display related posts in a featured + 2x2 grid layout.
 */
class Related_Posts_Grid_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-related-posts-grid';
	}

	public function get_title() {
		return esc_html__( 'مطالب مشابه', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return array( 'webmz-dynamic-widgets' );
	}

	public function get_keywords() {
		return array( 'related', 'posts', 'articles', 'مطالب مشابه', 'مقالات', 'وبلاگ' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_header',
			array(
				'label' => esc_html__( 'هدر بخش', 'tadris' ),
			)
		);

		$this->add_control(
			'show_header',
			array(
				'label'        => esc_html__( 'نمایش هدر', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'header_title',
			array(
				'label'     => esc_html__( 'عنوان', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'مقالات', 'tadris' ),
				'condition' => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->add_control(
			'header_subtitle',
			array(
				'label'     => esc_html__( 'زیرعنوان', 'tadris' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => esc_html__( 'مقالات آموزشی برتر وردپرس', 'tadris' ),
				'condition' => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->add_control(
			'header_icon',
			array(
				'label'     => esc_html__( 'آیکون هدر', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'far fa-comment-dots',
					'library' => 'fa-regular',
				),
				'condition' => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'header_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون هدر', 'tadris' ),
			array( 'show_header' => 'yes' )
		);

		$this->add_control(
			'show_view_all',
			array(
				'label'        => esc_html__( 'نمایش «مشاهده همه»', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->add_control(
			'view_all_text',
			array(
				'label'     => esc_html__( 'متن مشاهده همه', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'مشاهده همه', 'tadris' ),
				'condition' => array(
					'show_header'  => 'yes',
					'show_view_all' => 'yes',
				),
			)
		);

		$this->add_control(
			'view_all_link_type',
			array(
				'label'     => esc_html__( 'لینک مشاهده همه', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'category',
				'options'   => array(
					'category' => esc_html__( 'آرشیو دسته‌بندی مطلب فعلی', 'tadris' ),
					'blog'     => esc_html__( 'آرشیو نوشته‌ها', 'tadris' ),
					'custom'   => esc_html__( 'لینک دلخواه', 'tadris' ),
				),
				'condition' => array(
					'show_header'  => 'yes',
					'show_view_all' => 'yes',
				),
			)
		);

		$this->add_control(
			'view_all_link',
			array(
				'label'     => esc_html__( 'لینک دلخواه', 'tadris' ),
				'type'      => Controls_Manager::URL,
				'default'   => array(
					'url' => '#',
				),
				'condition' => array(
					'show_header'       => 'yes',
					'show_view_all'     => 'yes',
					'view_all_link_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'view_all_icon',
			array(
				'label'     => esc_html__( 'آیکون مشاهده همه', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-arrow-left',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_header'  => 'yes',
					'show_view_all' => 'yes',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'view_all_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون مشاهده همه', 'tadris' ),
			array(
				'show_header'  => 'yes',
				'show_view_all' => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'منبع مطالب', 'tadris' ),
			)
		);

		$this->add_control(
			'query_source',
			array(
				'label'   => esc_html__( 'نوع مطالب', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'related',
				'options' => array(
					'related'  => esc_html__( 'مطالب مشابه (همان دسته‌بندی)', 'tadris' ),
					'category' => esc_html__( 'از یک دسته‌بندی مشخص', 'tadris' ),
					'latest'   => esc_html__( 'جدیدترین نوشته‌ها', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'category_id',
			array(
				'label'     => esc_html__( 'دسته‌بندی', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => \webmz_tadris_get_post_category_options(),
				'condition' => array(
					'query_source' => 'category',
				),
			)
		);

		$this->add_control(
			'small_cards_count',
			array(
				'label'   => esc_html__( 'تعداد کارت‌های کوچک', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4,
				'min'     => 1,
				'max'     => 8,
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
					'comment_count' => esc_html__( 'بیشترین دیدگاه', 'tadris' ),
					'rand'          => esc_html__( 'تصادفی', 'tadris' ),
					'modified'      => esc_html__( 'آخرین ویرایش', 'tadris' ),
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_featured',
			array(
				'label' => esc_html__( 'کارت برگزیده', 'tadris' ),
			)
		);

		$this->add_control(
			'show_badge',
			array(
				'label'        => esc_html__( 'نمایش نشان برگزیده', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'badge_text',
			array(
				'label'     => esc_html__( 'متن نشان', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'برگزیده', 'tadris' ),
				'condition' => array(
					'show_badge' => 'yes',
				),
			)
		);

		$this->add_control(
			'badge_icon',
			array(
				'label'     => esc_html__( 'آیکون نشان', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-th-large',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_badge' => 'yes',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'badge_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون نشان', 'tadris' ),
			array( 'show_badge' => 'yes' )
		);

		$this->webmz_register_title_tag_control( 'featured_title_tag', esc_html__( 'تگ HTML عنوان برگزیده', 'tadris' ) );
		$this->webmz_register_title_tag_control( 'small_title_tag', esc_html__( 'تگ HTML عنوان کارت‌های کوچک', 'tadris' ) );

		$this->add_control(
			'show_author',
			array(
				'label'        => esc_html__( 'نمایش نویسنده', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'author_label',
			array(
				'label'     => esc_html__( 'برچسب نویسنده', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'نویسنده:', 'tadris' ),
				'condition' => array(
					'show_author' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_comments',
			array(
				'label'        => esc_html__( 'نمایش تعداد دیدگاه', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'comments_icon',
			array(
				'label'     => esc_html__( 'آیکون دیدگاه', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'far fa-comment-dots',
					'library' => 'fa-regular',
				),
				'condition' => array(
					'show_comments' => 'yes',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'comments_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون دیدگاه', 'tadris' ),
			array( 'show_comments' => 'yes' )
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'widget_style', esc_html__( 'باکس ویجت', 'tadris' ), '.webmz-related-posts' );

		$this->start_controls_section(
			'header_layout_style',
			array(
				'label' => esc_html__( 'چیدمان هدر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'header_gap',
			array(
				'label'      => esc_html__( 'فاصله بین عنوان و مشاهده همه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-related-posts__header' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_bottom_spacing',
			array(
				'label'      => esc_html__( 'فاصله زیر هدر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 32,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-related-posts__header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'header_heading_style', esc_html__( 'باکس عنوان هدر', 'tadris' ), '.webmz-related-posts__heading' );
		$this->webmz_register_icon_style_controls( 'header_icon_style', esc_html__( 'آیکون هدر', 'tadris' ), '.webmz-related-posts__heading-icon' );
		$this->webmz_register_text_style_controls( 'header_title_style', esc_html__( 'عنوان هدر', 'tadris' ), '.webmz-related-posts__heading-title' );
		$this->webmz_register_text_style_controls( 'header_subtitle_style', esc_html__( 'زیرعنوان هدر', 'tadris' ), '.webmz-related-posts__heading-subtitle' );
		$this->webmz_register_box_style_controls( 'view_all_style', esc_html__( 'لینک مشاهده همه', 'tadris' ), '.webmz-related-posts__view-all' );
		$this->webmz_register_text_style_controls( 'view_all_text_style', esc_html__( 'متن مشاهده همه', 'tadris' ), '.webmz-related-posts__view-all' );
		$this->webmz_register_icon_style_controls( 'view_all_icon_style', esc_html__( 'آیکون مشاهده همه', 'tadris' ), '.webmz-related-posts__view-all' );

		$this->start_controls_section(
			'grid_layout_style',
			array(
				'label' => esc_html__( 'چیدمان گرید', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'body_gap',
			array(
				'label'      => esc_html__( 'فاصله بین برگزیده و گرید', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-related-posts__body' => '--webmz-related-body-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cards_gap',
			array(
				'label'      => esc_html__( 'فاصله بین کارت‌های کوچک', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-related-posts__grid' => '--webmz-related-cards-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'featured_column_width',
			array(
				'label'      => esc_html__( 'عرض ستون برگزیده', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array(
					'%' => array(
						'min' => 20,
						'max' => 60,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 33,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-related-posts__body' => '--webmz-related-featured-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'small_columns',
			array(
				'label'     => esc_html__( 'ستون‌های کارت کوچک', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '2',
				'options'   => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-related-posts__grid' => '--webmz-related-small-columns: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mobile_featured_position',
			array(
				'label'   => esc_html__( 'جایگاه برگزیده در موبایل', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'first',
				'options' => array(
					'first' => esc_html__( 'بالا', 'tadris' ),
					'last'  => esc_html__( 'پایین', 'tadris' ),
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'featured_card_style', esc_html__( 'کارت برگزیده', 'tadris' ), '.webmz-related-posts__featured .tadris-blog-type-1' );
		$this->webmz_register_box_style_controls( 'featured_image_style', esc_html__( 'تصویر برگزیده', 'tadris' ), '.webmz-related-posts__featured .blog-type-1-figure img' );
		$this->webmz_register_box_style_controls( 'featured_content_style', esc_html__( 'باکس محتوای برگزیده', 'tadris' ), '.webmz-related-posts__featured .blog-type-1-content' );
		$this->webmz_register_box_style_controls( 'featured_badge_box_style', esc_html__( 'باکس نشان برگزیده', 'tadris' ), '.webmz-related-posts__featured .blog-type-1-badge' );
		$this->webmz_register_text_style_controls( 'featured_badge_text_style', esc_html__( 'متن نشان برگزیده', 'tadris' ), '.webmz-related-posts__featured .blog-type-1-badge' );
		$this->webmz_register_icon_style_controls( 'featured_badge_icon_style', esc_html__( 'آیکون نشان برگزیده', 'tadris' ), '.webmz-related-posts__featured .blog-type-1-badge' );
		$this->webmz_register_text_style_controls( 'featured_title_style', esc_html__( 'عنوان برگزیده', 'tadris' ), '.webmz-related-posts__featured .blog-type-1-header-tag a' );
		$this->webmz_register_text_style_controls( 'featured_author_style', esc_html__( 'نویسنده برگزیده', 'tadris' ), '.webmz-related-posts__featured .blog-type-1-author' );
		$this->webmz_register_text_style_controls( 'featured_comments_style', esc_html__( 'دیدگاه‌های برگزیده', 'tadris' ), '.webmz-related-posts__featured .blog-type-1-comments' );
		$this->webmz_register_icon_style_controls( 'featured_comments_icon_style', esc_html__( 'آیکون دیدگاه برگزیده', 'tadris' ), '.webmz-related-posts__featured .blog-type-1-comments' );

		$this->webmz_register_box_style_controls( 'small_card_style', esc_html__( 'کارت کوچک', 'tadris' ), '.webmz-related-posts__grid .tadris-blog-type-2' );
		$this->webmz_register_box_style_controls( 'small_image_style', esc_html__( 'تصویر کارت کوچک', 'tadris' ), '.webmz-related-posts__grid .blog-type-2-figure img' );
		$this->webmz_register_box_style_controls( 'small_overlay_style', esc_html__( 'لایه روی تصویر کارت کوچک', 'tadris' ), '.webmz-related-posts__grid .blog-type-2-header' );
		$this->webmz_register_text_style_controls( 'small_title_style', esc_html__( 'عنوان کارت کوچک', 'tadris' ), '.webmz-related-posts__grid .blog-type-2-header-tag a' );
	}

	/**
	 * Build preview posts for layout editing context.
	 *
	 * @param int $count Number of posts.
	 * @return array<int,\stdClass>
	 */
	protected function get_preview_posts( $count ) {
		$count = max( 1, absint( $count ) );
		$titles = array(
			esc_html__( 'آموزش ساخت سایت با وردپرس', 'tadris' ),
			esc_html__( 'راهنمای کامل سئو وردپرس', 'tadris' ),
			esc_html__( 'بهترین افزونه‌های فروشگاهی', 'tadris' ),
			esc_html__( 'طراحی قالب اختصاصی', 'tadris' ),
			esc_html__( 'امنیت سایت وردپرسی', 'tadris' ),
			esc_html__( 'بهینه‌سازی سرعت سایت', 'tadris' ),
			esc_html__( 'مدیریت محتوای حرفه‌ای', 'tadris' ),
			esc_html__( 'راه‌اندازی فروشگاه آنلاین', 'tadris' ),
			esc_html__( 'آموزش المنتور پیشرفته', 'tadris' ),
		);
		$posts = array();

		for ( $i = 0; $i < $count; $i++ ) {
			$post              = new \stdClass();
			$post->ID          = 0;
			$post->post_title  = $titles[ $i % count( $titles ) ];
			$post->post_author = 0;
			$posts[]           = $post;
		}

		return $posts;
	}

	/**
	 * Resolve posts for the widget.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<int,\WP_Post|\stdClass>
	 */
	protected function get_posts( $settings ) {
		$small_count = min( 8, max( 1, ! empty( $settings['small_cards_count'] ) ? absint( $settings['small_cards_count'] ) : 4 ) );
		$total       = $small_count + 1;

		if ( \webmz_is_layout_editing_context() ) {
			return $this->get_preview_posts( $total );
		}

		$post_id = \webmz_get_context_post_id();
		$source  = isset( $settings['query_source'] ) ? sanitize_key( $settings['query_source'] ) : 'related';

		$allowed_orderby = array( 'date', 'comment_count', 'rand', 'modified' );
		$orderby         = ! empty( $settings['order_by'] ) && in_array( $settings['order_by'], $allowed_orderby, true ) ? $settings['order_by'] : 'date';
		$order           = ! empty( $settings['order'] ) && in_array( strtoupper( $settings['order'] ), array( 'ASC', 'DESC' ), true ) ? strtoupper( $settings['order'] ) : 'DESC';

		$args = array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => $total,
			'orderby'                => sanitize_key( $orderby ),
			'order'                  => $order,
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
		);

		if ( $post_id ) {
			$args['post__not_in'] = array( $post_id );
		}

		if ( 'related' === $source && $post_id ) {
			$categories = wp_get_post_categories( $post_id );
			if ( ! empty( $categories ) ) {
				$args['category__in'] = $categories;
			}
		} elseif ( 'category' === $source && ! empty( $settings['category_id'] ) ) {
			$args['cat'] = absint( $settings['category_id'] );
		}

		$query = new WP_Query( $args );

		return $query->posts;
	}

	/**
	 * Resolve view-all URL.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return string
	 */
	protected function get_view_all_url( $settings ) {
		$type = isset( $settings['view_all_link_type'] ) ? sanitize_key( $settings['view_all_link_type'] ) : 'category';

		if ( 'custom' === $type ) {
			return ! empty( $settings['view_all_link']['url'] ) ? (string) $settings['view_all_link']['url'] : '#';
		}

		if ( 'blog' === $type ) {
			$archive = get_post_type_archive_link( 'post' );
			return $archive ? $archive : '#';
		}

		$post_id = \webmz_get_context_post_id();
		if ( $post_id && function_exists( '\webmz_get_deepest_post_category' ) ) {
			$category_id = \webmz_get_deepest_post_category( $post_id );
			if ( $category_id ) {
				$link = get_category_link( $category_id );
				if ( $link && ! is_wp_error( $link ) ) {
					return $link;
				}
			}
		}

		$archive = get_post_type_archive_link( 'post' );
		return $archive ? $archive : '#';
	}

	/**
	 * Render placeholder image letter.
	 *
	 * @param string $title Post title.
	 * @param string $type  Card type: featured|small.
	 * @return void
	 */
	protected function render_placeholder_image( $title, $type = 'small' ) {
		$letter = function_exists( 'mb_substr' ) ? mb_substr( $title, 0, 1 ) : substr( $title, 0, 1 );
		$class  = 'featured' === $type ? 'blog-type-1-no-image' : 'blog-type-2-no-image';
		?>
		<div class="<?php echo esc_attr( $class ); ?>" aria-hidden="true"><?php echo esc_html( $letter ); ?></div>
		<?php
	}

	/**
	 * Render featured card markup.
	 *
	 * @param \WP_Post|\stdClass    $post     Post object.
	 * @param array<string,mixed>   $settings Widget settings.
	 * @return void
	 */
	protected function render_featured_card( $post, $settings ) {
		$post_id        = isset( $post->ID ) ? absint( $post->ID ) : 0;
		$is_preview     = \webmz_is_layout_editing_context();
		$title          = isset( $post->post_title ) ? (string) $post->post_title : '';
		$link           = $post_id ? get_permalink( $post_id ) : '#';
		$author_id      = isset( $post->post_author ) ? (int) $post->post_author : 0;
		$author_name    = $author_id ? get_the_author_meta( 'display_name', $author_id ) : esc_html__( 'نویسنده نمونه', 'tadris' );
		$comments_count = $post_id ? get_comments_number( $post_id ) : 26;
		$title_tag      = $this->webmz_get_title_tag( $settings, 'featured_title_tag' );
		?>
		<article class="tadris-blog-type-1">
			<figure class="blog-type-1-figure">
				<?php
				if ( $post_id && has_post_thumbnail( $post_id ) ) {
					echo webmz_get_post_loop_thumbnail( $post_id, array( 'alt' => esc_attr( $title ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					$this->render_placeholder_image( $title, 'featured' );
				}
				?>
			</figure>

			<div class="blog-type-1-content">
				<?php if ( isset( $settings['show_badge'] ) && 'yes' === $settings['show_badge'] ) : ?>
					<span class="blog-type-1-badge <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'badge_icon_color_mode' ) ); ?>">
						<?php Icons_Manager::render_icon( $settings['badge_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						<?php echo esc_html( $settings['badge_text'] ); ?>
					</span>
				<?php endif; ?>

				<header class="blog-type-1-header">
					<<?php echo esc_attr( $title_tag ); ?> class="blog-type-1-header-tag">
						<?php if ( $post_id || $is_preview ) : ?>
							<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a>
						<?php else : ?>
							<span><?php echo esc_html( $title ); ?></span>
						<?php endif; ?>
					</<?php echo esc_attr( $title_tag ); ?>>
				</header>

				<footer class="blog-type-1-footer">
					<?php if ( isset( $settings['show_author'] ) && 'yes' === $settings['show_author'] ) : ?>
						<div class="blog-type-1-author">
							<?php echo get_avatar( $author_id, 48, '', $author_name, array( 'class' => 'blog-type-1-cv' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( $settings['author_label'] ); ?></span>
							<strong><?php echo esc_html( $author_name ); ?></strong>
						</div>
					<?php endif; ?>

					<?php if ( isset( $settings['show_comments'] ) && 'yes' === $settings['show_comments'] ) : ?>
						<div class="blog-type-1-comments <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'comments_icon_color_mode' ) ); ?>">
							<?php Icons_Manager::render_icon( $settings['comments_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							<span><?php echo esc_html( number_format_i18n( $comments_count ) ); ?></span>
						</div>
					<?php endif; ?>
				</footer>
			</div>
		</article>
		<?php
	}

	/**
	 * Render one small card markup.
	 *
	 * @param \WP_Post|\stdClass  $post     Post object.
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	protected function render_small_card( $post, $settings ) {
		$post_id    = isset( $post->ID ) ? absint( $post->ID ) : 0;
		$is_preview = \webmz_is_layout_editing_context();
		$title      = isset( $post->post_title ) ? (string) $post->post_title : '';
		$link       = $post_id ? get_permalink( $post_id ) : '#';
		$title_tag  = $this->webmz_get_title_tag( $settings, 'small_title_tag' );
		?>
		<article class="tadris-blog-type-2">
			<figure class="blog-type-2-figure">
				<?php if ( $post_id || $is_preview ) : ?>
					<a href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
				<?php endif; ?>
					<?php
					if ( $post_id && has_post_thumbnail( $post_id ) ) {
						echo webmz_get_post_loop_thumbnail( $post_id, array( 'alt' => esc_attr( $title ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						$this->render_placeholder_image( $title, 'small' );
					}
					?>
				<?php if ( $post_id || $is_preview ) : ?>
					</a>
				<?php endif; ?>
			</figure>

			<header class="blog-type-2-header">
				<<?php echo esc_attr( $title_tag ); ?> class="blog-type-2-header-tag">
					<?php if ( $post_id || $is_preview ) : ?>
						<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a>
					<?php else : ?>
						<span><?php echo esc_html( $title ); ?></span>
					<?php endif; ?>
				</<?php echo esc_attr( $title_tag ); ?>>
			</header>
		</article>
		<?php
	}

	/**
	 * Render section header.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	protected function render_header( $settings ) {
		if ( empty( $settings['show_header'] ) || 'yes' !== $settings['show_header'] ) {
			return;
		}

		$view_all_url = $this->get_view_all_url( $settings );
		?>
		<div class="webmz-related-posts__header">
			<div class="webmz-related-posts__heading">
				<div class="webmz-related-posts__heading-icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'header_icon_color_mode' ) ); ?>">
					<?php Icons_Manager::render_icon( $settings['header_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</div>
				<div class="webmz-related-posts__heading-text">
					<?php if ( ! empty( $settings['header_title'] ) ) : ?>
						<h2 class="webmz-related-posts__heading-title"><?php echo esc_html( $settings['header_title'] ); ?></h2>
					<?php endif; ?>
					<?php if ( ! empty( $settings['header_subtitle'] ) ) : ?>
						<p class="webmz-related-posts__heading-subtitle"><?php echo esc_html( $settings['header_subtitle'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( ! empty( $settings['show_view_all'] ) && 'yes' === $settings['show_view_all'] ) : ?>
				<?php
				$this->add_link_attributes( 'view_all', array( 'url' => $view_all_url ) );
				if ( 'custom' === ( $settings['view_all_link_type'] ?? '' ) && ! empty( $settings['view_all_link']['is_external'] ) ) {
					$this->add_render_attribute( 'view_all', 'target', '_blank' );
				}
				if ( 'custom' === ( $settings['view_all_link_type'] ?? '' ) && ! empty( $settings['view_all_link']['nofollow'] ) ) {
					$this->add_render_attribute( 'view_all', 'rel', 'nofollow' );
				}
				?>
				<a class="webmz-related-posts__view-all <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'view_all_icon_color_mode' ) ); ?>" <?php echo $this->get_render_attribute_string( 'view_all' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span><?php echo esc_html( $settings['view_all_text'] ); ?></span>
					<?php Icons_Manager::render_icon( $settings['view_all_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$posts    = $this->get_posts( $settings );

		if ( empty( $posts ) ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'مطلب مشابهی برای نمایش یافت نشد.', 'tadris' ) . '</div>';
			return;
		}

		$featured = array_shift( $posts );
		$mobile_position = isset( $settings['mobile_featured_position'] ) && 'last' === $settings['mobile_featured_position'] ? 'last' : 'first';
		?>
		<div class="webmz-related-posts is-featured-mobile-<?php echo esc_attr( $mobile_position ); ?>">
			<?php $this->render_header( $settings ); ?>

			<div class="webmz-related-posts__body">
				<div class="webmz-related-posts__featured">
					<?php $this->render_featured_card( $featured, $settings ); ?>
				</div>

				<?php if ( ! empty( $posts ) ) : ?>
					<div class="webmz-related-posts__grid">
						<?php foreach ( $posts as $post ) : ?>
							<?php $this->render_small_card( $post, $settings ); ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
