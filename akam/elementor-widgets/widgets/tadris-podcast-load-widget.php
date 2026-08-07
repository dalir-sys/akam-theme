<?php
/**
 * Podcast loop Elementor widget for Tadris/WebMZ.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * Load podcast posts from normal WordPress posts.
 */
class Tadris_Podcast_Load_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'webmz-tadris-podcast-load';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'لود پادکست‌ها', 'tadris' );
	}

	/**
	 * Elementor icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-headphones';
	}

	/**
	 * Widget category.
	 *
	 * @return array<int,string>
	 */
	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	/**
	 * Search keywords.
	 *
	 * @return array<int,string>
	 */
	public function get_keywords() {
		return array( 'podcast', 'audio', 'پادکست', 'لود پادکست', 'آکام' );
	}

	/**
	 * Required scripts.
	 *
	 * @return array<int,string>
	 */
	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-plyr', 'webmz-tadris-widgets' );
	}

	/**
	 * Required styles.
	 *
	 * @return array<int,string>
	 */
	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-plyr' );
	}

	/**
	 * Category options for posts.
	 *
	 * @return array<int|string,string>
	 */
	private function get_post_categories() {
		$options = array(
			'' => esc_html__( 'همه دسته‌ها', 'tadris' ),
		);

		$terms = get_terms(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $term->name;
			}
		}

		return $options;
	}

	/**
	 * Register Elementor controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'تنظیمات محتوا', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'تعداد پادکست‌ها', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 1,
				'max'     => 50,
			)
		);

		$this->add_control(
			'category',
			array(
				'label'       => esc_html__( 'دسته‌بندی', 'tadris' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->get_post_categories(),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => esc_html__( 'مرتب‌سازی بر اساس', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'          => esc_html__( 'تاریخ', 'tadris' ),
					'title'         => esc_html__( 'عنوان', 'tadris' ),
					'rand'          => esc_html__( 'تصادفی', 'tadris' ),
					'comment_count' => esc_html__( 'تعداد دیدگاه', 'tadris' ),
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

		$this->add_control(
			'show_only_podcasts',
			array(
				'label'        => esc_html__( 'فقط نوشته‌های دارای لینک پادکست', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'تنظیمات نمایش', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'display_type',
			array(
				'label'   => esc_html__( 'نوع نمایش', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid'   => esc_html__( 'گرید', 'tadris' ),
					'slider' => esc_html__( 'اسلایدی', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'grid_columns_heading',
			array(
				'label'     => esc_html__( 'تنظیمات گرید', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'display_type' => 'grid',
				),
			)
		);

		$this->add_control(
			'grid_columns_desktop',
			array(
				'label'     => esc_html__( 'تعداد ستون دسکتاپ', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '2',
				'options'   => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors' => array(
					'{{WRAPPER}} .tadris-podcast-grid' => '--webmz-grid-columns: {{VALUE}};',
				),
				'condition' => array(
					'display_type' => 'grid',
				),
			)
		);

		$this->add_control(
			'grid_columns_tablet',
			array(
				'label'     => esc_html__( 'تعداد ستون تبلت', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '2',
				'options'   => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
				'selectors' => array(
					'{{WRAPPER}} .tadris-podcast-grid' => '--webmz-grid-tablet-columns: {{VALUE}};',
				),
				'condition' => array(
					'display_type' => 'grid',
				),
			)
		);

		$this->add_control(
			'grid_columns_mobile',
			array(
				'label'     => esc_html__( 'تعداد ستون موبایل', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '1',
				'options'   => array(
					'1' => '1',
					'2' => '2',
				),
				'selectors' => array(
					'{{WRAPPER}} .tadris-podcast-grid' => '--webmz-grid-mobile-columns: {{VALUE}};',
				),
				'condition' => array(
					'display_type' => 'grid',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'فاصله آیتم‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 24,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .tadris-podcast-grid'   => '--webmz-grid-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tadris-podcast-swiper' => '--webmz-podcast-slider-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'slider_settings_heading',
			array(
				'label'     => esc_html__( 'تنظیمات اسلایدر', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'display_type' => 'slider',
				),
			)
		);

		foreach ( array( 'desktop' => esc_html__( 'دسکتاپ', 'tadris' ), 'tablet' => esc_html__( 'تبلت', 'tadris' ), 'mobile' => esc_html__( 'موبایل', 'tadris' ) ) as $device => $label ) {
			$this->add_control(
				'slides_' . $device,
				array(
					'label'     => sprintf( esc_html__( 'تعداد اسلاید %s', 'tadris' ), $label ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 'mobile' === $device ? 1 : 2,
					'min'       => 1,
					'max'       => 6,
					'condition' => array(
						'display_type' => 'slider',
					),
				)
			);
		}

		$this->add_control(
			'slider_loop',
			array(
				'label'        => esc_html__( 'لوپ اسلایدر', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'فعال', 'tadris' ),
				'label_off'    => esc_html__( 'غیرفعال', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'slider_autoplay',
			array(
				'label'        => esc_html__( 'پخش خودکار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'فعال', 'tadris' ),
				'label_off'    => esc_html__( 'غیرفعال', 'tadris' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'slider_autoplay_delay',
			array(
				'label'     => esc_html__( 'زمان پخش خودکار', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3500,
				'min'       => 1000,
				'step'      => 100,
				'condition' => array(
					'display_type'     => 'slider',
					'slider_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_slider_nav',
			array(
				'label'        => esc_html__( 'نمایش فلش‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'show_slider_pagination',
			array(
				'label'        => esc_html__( 'نمایش صفحه‌بندی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_parts',
			array(
				'label' => esc_html__( 'اجزای کارت', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ HTML عنوان کارت‌ها', 'tadris' ) );

		foreach ( array(
			'show_author'  => esc_html__( 'نمایش گوینده', 'tadris' ),
			'show_footer'  => esc_html__( 'نمایش اطلاعات پایین کارت', 'tadris' ),
			'show_player'  => esc_html__( 'نمایش پلیر', 'tadris' ),
			'show_actions' => esc_html__( 'نمایش ذخیره و اشتراک‌گذاری', 'tadris' ),
		) as $key => $label ) {
			$this->add_control(
				$key,
				array(
					'label'        => $label,
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'نمایش', 'tadris' ),
					'label_off'    => esc_html__( 'مخفی', 'tadris' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => esc_html__( 'استایل کارت', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه کارت', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .tadris-podcast-type-1' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی کارت', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'      => 16,
					'right'    => 16,
					'bottom'   => 16,
					'left'     => 16,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .tadris-podcast-type-1' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'گردی کارت', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => 16,
					'right'    => 16,
					'bottom'   => 16,
					'left'     => 16,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .tadris-podcast-type-1' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .tadris-podcast-type-1',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .tadris-podcast-type-1',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => esc_html__( 'استایل تصویر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'      => esc_html__( 'عرض تصویر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 60,
						'max' => 320,
					),
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 186,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .podcast-type-1-figure' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'     => esc_html__( 'ارتفاع تصویر', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 60,
						'max' => 320,
					),
				),
				'default'   => array(
					'size' => 186,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .podcast-type-1-figure img, {{WRAPPER}} .podcast-type-1-no-image' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_radius',
			array(
				'label'      => esc_html__( 'گردی تصویر', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => 8,
					'right'    => 8,
					'bottom'   => 8,
					'left'     => 8,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .podcast-type-1-figure img, {{WRAPPER}} .podcast-type-1-no-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_typography_style',
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
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .podcast-type-1-header-tag a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'tadris' ),
				'selector' => '{{WRAPPER}} .podcast-type-1-header-tag',
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => esc_html__( 'رنگ اطلاعات', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6B7280',
				'selectors' => array(
					'{{WRAPPER}} .podcast-type-1-author, {{WRAPPER}} .podcast-type-1-footer' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'meta_strong_color',
			array(
				'label'     => esc_html__( 'رنگ مقدارهای برجسته', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .podcast-type-1-author strong, {{WRAPPER}} .podcast-footer-inner strong' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_actions_style',
			array(
				'label' => esc_html__( 'استایل دکمه‌های ذخیره و اشتراک‌گذاری', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'action_size',
			array(
				'label'     => esc_html__( 'اندازه دکمه', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 28,
						'max' => 80,
					),
				),
				'default'   => array(
					'size' => 42,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .podcast-action-button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'action_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .podcast-action-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'action_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه دکمه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F3F4F6',
				'selectors' => array(
					'{{WRAPPER}} .podcast-action-button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'action_hover_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .podcast-action-button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'action_hover_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .podcast-action-button:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build widget query arguments.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<string,mixed>
	 */
	private function get_query_args( $settings ) {
		$args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => ! empty( $settings['posts_per_page'] ) ? absint( $settings['posts_per_page'] ) : 6,
			'orderby'             => ! empty( $settings['orderby'] ) ? sanitize_key( $settings['orderby'] ) : 'date',
			'order'               => ! empty( $settings['order'] ) ? sanitize_key( $settings['order'] ) : 'DESC',
			'ignore_sticky_posts' => true,
		);

		if ( ! empty( $settings['category'] ) && is_array( $settings['category'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => array_map( 'absint', $settings['category'] ),
				),
			);
		}

		if ( isset( $settings['show_only_podcasts'] ) && 'yes' === $settings['show_only_podcasts'] && defined( 'TADRIS_PODCAST_AUDIO_META_KEY' ) ) {
			$args['meta_query'] = array(
				array(
					'key'     => TADRIS_PODCAST_AUDIO_META_KEY,
					'value'   => '',
					'compare' => '!=',
				),
			);
		}

		return $args;
	}

	/**
	 * Get automatic post views.
	 *
	 * @param int $post_id Post ID.
	 * @return int
	 */
	private function get_post_views_count( $post_id ) {
		if ( function_exists( '\webmz_tadris_get_post_views' ) ) {
			return \webmz_tadris_get_post_views( $post_id );
		}

		return absint( get_post_meta( $post_id, '_webmz_post_views', true ) );
	}

	/**
	 * Get first category name.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	private function get_first_category_name( $post_id ) {
		$terms = get_the_terms( $post_id, 'category' );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return esc_html__( 'بدون دسته', 'tadris' );
		}

		return $terms[0]->name;
	}

	/**
	 * Render one podcast card.
	 *
	 * @param int                 $post_id  Post ID.
	 * @param array<string,mixed> $settings Widget settings.
	 * @return bool True when rendered.
	 */
	private function render_podcast_card( $post_id, $settings ) {
		$post_id   = absint( $post_id );
		$audio_url = function_exists( '\webmz_tadris_get_podcast_audio_url' ) ? \webmz_tadris_get_podcast_audio_url( $post_id ) : '';

		if ( empty( $audio_url ) ) {
			return false;
		}

		$post_link = get_permalink( $post_id );
		$title     = get_the_title( $post_id );
		$title_tag = $this->webmz_get_title_tag( $settings, 'title_tag' );
		$views     = $this->get_post_views_count( $post_id );
		$category  = $this->get_first_category_name( $post_id );
		$author_id = (int) get_post_field( 'post_author', $post_id );
		$author    = get_the_author_meta( 'display_name', $author_id );
		$avatar    = get_avatar_url( $author_id, array( 'size' => 96 ) );
		$date      = sprintf(
			/* translators: %s: human readable time difference. */
			esc_html__( '%s پیش', 'tadris' ),
			human_time_diff( get_the_time( 'U', $post_id ), current_time( 'timestamp' ) )
		);
		?>

		<article class="tadris-podcast-type-1" data-tadris-post-id="<?php echo esc_attr( $post_id ); ?>">
			<figure class="podcast-type-1-figure">
				<a href="<?php echo esc_url( $post_link ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
					<?php if ( has_post_thumbnail( $post_id ) ) : ?>
						<?php echo webmz_get_post_loop_thumbnail( $post_id, array( 'alt' => esc_attr( $title ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php else : ?>
						<div class="podcast-type-1-no-image" aria-hidden="true">
							<?php echo esc_html( function_exists( 'mb_substr' ) ? mb_substr( $title, 0, 1 ) : substr( $title, 0, 1 ) ); ?>
						</div>
					<?php endif; ?>
				</a>
			</figure>

			<div class="podcast-type-1-body">
				<header class="podcast-type-1-header">
					<<?php echo esc_attr( $title_tag ); ?> class="podcast-type-1-header-tag">
						<a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $title ); ?></a>
					</<?php echo esc_attr( $title_tag ); ?>>
				</header>

				<?php if ( isset( $settings['show_author'] ) && 'yes' === $settings['show_author'] ) : ?>
					<div class="podcast-type-1-author">
						<img src="<?php echo esc_url( $avatar ); ?>" alt="<?php echo esc_attr( $author ); ?>" class="podcast-type-1-cv">
						<span><?php esc_html_e( 'گوینده:', 'tadris' ); ?></span>
						<strong><?php echo esc_html( $author ); ?></strong>
					</div>
				<?php endif; ?>

				<?php if ( isset( $settings['show_footer'] ) && 'yes' === $settings['show_footer'] ) : ?>
					<footer class="podcast-type-1-footer">
						<ul>
							<li>
								<div class="podcast-footer-inner"><strong><?php echo esc_html( number_format_i18n( $views ) ); ?></strong> <?php esc_html_e( 'بار پخش شده', 'tadris' ); ?></div>
							</li>
							<li>
								<div class="podcast-footer-inner"><strong><?php echo esc_html( $date ); ?></strong></div>
							</li>
							<li>
								<div class="podcast-footer-inner"><strong><?php esc_html_e( 'دسته', 'tadris' ); ?></strong> <?php echo esc_html( $category ); ?></div>
							</li>
						</ul>
					</footer>
				<?php endif; ?>

				<?php if ( isset( $settings['show_player'] ) && 'yes' === $settings['show_player'] ) : ?>
					<div class="podcast-type-1-player">
						<audio class="tadris-sound-tag tadris-player-tag" controls preload="none" data-webmz-history-player="1" data-webmz-history-post-id="<?php echo esc_attr( $post_id ); ?>" data-webmz-history-type="podcast">
							<source src="<?php echo esc_url( $audio_url ); ?>" type="audio/mp3">
						</audio>
					</div>
				<?php endif; ?>
			</div>

			<?php
			if ( isset( $settings['show_actions'] ) && 'yes' === $settings['show_actions'] && function_exists( '\webmz_tadris_render_post_action_buttons' ) ) {
				\webmz_tadris_render_post_action_buttons(
					$post_id,
					array(
						'wrapper_class' => 'podcast-type-1-actions',
						'save_class'    => 'podcast-action-button podcast-action-save',
						'share_class'   => 'podcast-action-button podcast-action-share',
						'icon_only'     => true,
					)
				);
			}
			?>
		</article>

		<?php
		return true;
	}

	/**
	 * Render widget.
	 *
	 * @return void
	 */
	protected function render() {
		$settings    = $this->get_settings_for_display();
		$query       = new WP_Query( $this->get_query_args( $settings ) );
		$is_slider   = isset( $settings['display_type'] ) && 'slider' === $settings['display_type'];
		$rendered    = 0;
		$slider_id   = 'tadris-podcast-swiper-' . $this->get_id();
		$gap         = isset( $settings['grid_gap']['size'] ) ? absint( $settings['grid_gap']['size'] ) : 24;
		$slider_conf = array(
			'slidesDesktop' => ! empty( $settings['slides_desktop'] ) ? absint( $settings['slides_desktop'] ) : 2,
			'slidesTablet'  => ! empty( $settings['slides_tablet'] ) ? absint( $settings['slides_tablet'] ) : 2,
			'slidesMobile'  => ! empty( $settings['slides_mobile'] ) ? absint( $settings['slides_mobile'] ) : 1,
			'spaceBetween'  => $gap,
			'loop'          => isset( $settings['slider_loop'] ) && 'yes' === $settings['slider_loop'],
			'autoplay'      => isset( $settings['slider_autoplay'] ) && 'yes' === $settings['slider_autoplay'],
			'autoplayDelay' => ! empty( $settings['slider_autoplay_delay'] ) ? absint( $settings['slider_autoplay_delay'] ) : 3500,
			'navigation'    => isset( $settings['show_slider_nav'] ) && 'yes' === $settings['show_slider_nav'],
			'pagination'    => isset( $settings['show_slider_pagination'] ) && 'yes' === $settings['show_slider_pagination'],
		);

		if ( ! $query->have_posts() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'پادکستی برای نمایش وجود ندارد.', 'tadris' ) . '</div>';
			return;
		}

		if ( $is_slider ) :
			?>
			<div id="<?php echo esc_attr( $slider_id ); ?>" class="tadris-podcast-loop tadris-podcast-swiper swiper" data-tadris-podcast-swiper="<?php echo esc_attr( wp_json_encode( $slider_conf ) ); ?>">
				<div class="swiper-wrapper">
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						ob_start();
						$did_render = $this->render_podcast_card( get_the_ID(), $settings );
						$card_html  = ob_get_clean();

						if ( ! $did_render ) {
							continue;
						}

						++$rendered;
						?>
						<div class="swiper-slide">
							<?php echo $card_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endwhile; ?>
				</div>

				<?php if ( $slider_conf['navigation'] ) : ?>
					<div class="tadris-podcast-swiper-button tadris-podcast-swiper-button-next swiper-button-next"></div>
					<div class="tadris-podcast-swiper-button tadris-podcast-swiper-button-prev swiper-button-prev"></div>
				<?php endif; ?>

				<?php if ( $slider_conf['pagination'] ) : ?>
					<div class="tadris-podcast-swiper-pagination swiper-pagination"></div>
				<?php endif; ?>
			</div>
			<?php
		else :
			?>
			<div class="tadris-podcast-loop tadris-podcast-grid webmz-loop-grid" style="--webmz-grid-columns: <?php echo esc_attr( $settings['grid_columns_desktop'] ?? 2 ); ?>; --webmz-grid-tablet-columns: <?php echo esc_attr( $settings['grid_columns_tablet'] ?? 2 ); ?>; --webmz-grid-mobile-columns: <?php echo esc_attr( $settings['grid_columns_mobile'] ?? 1 ); ?>; --webmz-grid-gap: <?php echo esc_attr( $gap ); ?>px;">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					if ( $this->render_podcast_card( get_the_ID(), $settings ) ) {
						++$rendered;
					}
				endwhile;
				?>
			</div>
			<?php
		endif;

		wp_reset_postdata();

		if ( 0 === $rendered ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'پادکستی برای نمایش وجود ندارد.', 'tadris' ) . '</div>';
		}
	}
}
