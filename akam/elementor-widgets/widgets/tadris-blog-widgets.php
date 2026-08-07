<?php
/**
 * Blog widgets for Tadris/WebMZ sections.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * Featured large blog card widget.
 */
class Tadris_Featured_Blog_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-tadris-featured-blog';
	}

	public function get_title() {
		return esc_html__( 'وبلاگ برگزیده بزرگ', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-featured-image';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'blog', 'featured', 'post', 'برگزیده', 'وبلاگ', 'مقاله' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_source',
			array(
				'label' => esc_html__( 'انتخاب نوشته', 'tadris' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'منبع نمایش', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => array(
					'manual'   => esc_html__( 'انتخاب مستقیم یک نوشته', 'tadris' ),
					'category' => esc_html__( 'اولین نوشته از دسته‌بندی', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'post_id',
			array(
				'label'       => esc_html__( 'نوشته', 'tadris' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => \webmz_tadris_get_post_options(),
				'label_block' => true,
				'condition'   => array(
					'source' => 'manual',
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
					'source' => 'category',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_parts',
			array(
				'label' => esc_html__( 'متن و آیکون‌ها', 'tadris' ),
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
		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ HTML عنوان', 'tadris' ) );

		$this->add_control(
			'author_label',
			array(
				'label'   => esc_html__( 'برچسب نویسنده', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'نویسنده:', 'tadris' ),
			)
		);

		$this->add_control(
			'comments_icon',
			array(
				'label'   => esc_html__( 'آیکون دیدگاه', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'far fa-comment-dots',
					'library' => 'fa-regular',
				),
			)
		);


		$this->webmz_register_icon_color_mode_control( 'comments_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون دیدگاه', 'tadris' ) );
		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'featured_card_style', esc_html__( 'کارت برگزیده', 'tadris' ), '.tadris-blog-type-1' );
		$this->webmz_register_box_style_controls( 'featured_image_style', esc_html__( 'تصویر', 'tadris' ), '.blog-type-1-figure img' );
		$this->webmz_register_box_style_controls( 'featured_content_style', esc_html__( 'باکس محتوا', 'tadris' ), '.blog-type-1-content' );
		$this->webmz_register_box_style_controls( 'featured_badge_box_style', esc_html__( 'باکس نشان', 'tadris' ), '.blog-type-1-badge' );
		$this->webmz_register_text_style_controls( 'featured_badge_text_style', esc_html__( 'متن نشان', 'tadris' ), '.blog-type-1-badge' );
		$this->webmz_register_icon_style_controls( 'featured_badge_icon_style', esc_html__( 'آیکون نشان', 'tadris' ), '.blog-type-1-badge' );
		$this->webmz_register_text_style_controls( 'featured_title_style', esc_html__( 'عنوان', 'tadris' ), '.blog-type-1-header-tag a' );
		$this->webmz_register_text_style_controls( 'featured_author_style', esc_html__( 'نویسنده', 'tadris' ), '.blog-type-1-author' );
		$this->webmz_register_text_style_controls( 'featured_comments_style', esc_html__( 'دیدگاه‌ها', 'tadris' ), '.blog-type-1-comments' );
		$this->webmz_register_icon_style_controls( 'featured_comments_icon_style', esc_html__( 'آیکون دیدگاه‌ها', 'tadris' ), '.blog-type-1-comments' );
	}

	private function resolve_post( $settings ) {
		$source      = isset( $settings['source'] ) ? sanitize_key( $settings['source'] ) : 'manual';
		$post_id     = ! empty( $settings['post_id'] ) ? absint( $settings['post_id'] ) : 0;
		$category_id = ! empty( $settings['category_id'] ) ? absint( $settings['category_id'] ) : 0;

		if ( function_exists( '\webmz_tadris_resolve_post' ) ) {
			return \webmz_tadris_resolve_post( $source, $post_id, $category_id );
		}

		return \webmz_tadris_resolve_video_post( $source, $post_id, $category_id );
	}

	private function render_placeholder_image( $title ) {
		$letter = function_exists( 'mb_substr' ) ? mb_substr( $title, 0, 1 ) : substr( $title, 0, 1 );
		?>
		<div class="blog-type-1-no-image" aria-hidden="true"><?php echo esc_html( $letter ); ?></div>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$post     = $this->resolve_post( $settings );

		if ( ! $post ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'نوشته‌ای برای نمایش انتخاب نشده است.', 'tadris' ) . '</div>';
			return;
		}

		$author_id      = (int) $post->post_author;
		$author_name    = get_the_author_meta( 'display_name', $author_id );
		$comments_count = get_comments_number( $post->ID );
		$link           = get_permalink( $post );
		$title          = get_the_title( $post );
		$title_tag      = $this->webmz_get_title_tag( $settings, 'title_tag' );
		?>
		<article class="tadris-blog-type-1">
			<figure class="blog-type-1-figure">
				<?php
				if ( has_post_thumbnail( $post ) ) {
					echo webmz_get_post_loop_thumbnail( $post, array( 'alt' => esc_attr( $title ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					$this->render_placeholder_image( $title );
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
					<<?php echo esc_attr( $title_tag ); ?> class="blog-type-1-header-tag"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></<?php echo esc_attr( $title_tag ); ?>>
				</header>

				<footer class="blog-type-1-footer">
					<div class="blog-type-1-author">
						<?php echo get_avatar( $author_id, 48, '', $author_name, array( 'class' => 'blog-type-1-cv' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php echo esc_html( $settings['author_label'] ); ?></span>
						<strong><?php echo esc_html( $author_name ); ?></strong>
					</div>

					<div class="blog-type-1-comments <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'comments_icon_color_mode' ) ); ?>">
						<?php Icons_Manager::render_icon( $settings['comments_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						<span><?php echo esc_html( number_format_i18n( $comments_count ) ); ?></span>
					</div>
				</footer>
			</div>
		</article>
		<?php
	}
}

/**
 * Blog article loop widget.
 */
class Tadris_Blog_Loop_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-tadris-blog-loop';
	}

	public function get_title() {
		return esc_html__( 'لوپ مقالات ۱', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'blog', 'loop', 'post', 'مقاله', 'وبلاگ', 'لوپ' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-tadris-widgets' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'query',
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
				'default' => 6,
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
				'description' => esc_html__( 'برای مثال می‌توانید نوشته‌ای را که در ویجت «وبلاگ برگزیده بزرگ» نمایش داده‌اید از این لوپ حذف کنید.', 'tadris' ),
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

		$this->webmz_register_title_tag_control( 'loop_title_tag', esc_html__( 'تگ HTML عنوان کارت‌ها', 'tadris' ) );

		$this->add_control(
			'display_type',
			array(
				'label'   => esc_html__( 'نحوه نمایش', 'tadris' ),
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
				'default'   => '3',
				'options'   => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors' => array(
					'{{WRAPPER}} .tadris-blog-grid' => '--webmz-grid-columns: {{VALUE}};',
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
					'4' => '4',
				),
				'selectors' => array(
					'{{WRAPPER}} .tadris-blog-grid' => '--webmz-grid-tablet-columns: {{VALUE}};',
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
					'{{WRAPPER}} .tadris-blog-grid' => '--webmz-grid-mobile-columns: {{VALUE}};',
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
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .tadris-blog-grid'   => '--webmz-grid-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tadris-blog-swiper' => '--webmz-blog-slider-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'slider_heading',
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
					'default'   => 'mobile' === $device ? 1 : 3,
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

		$this->webmz_register_box_style_controls( 'blog_loop_style', esc_html__( 'لوپ', 'tadris' ), '.tadris-blog-loop' );
		$this->webmz_register_box_style_controls( 'blog_card_style', esc_html__( 'کارت مقاله', 'tadris' ), '.tadris-blog-type-2' );
		$this->webmz_register_box_style_controls( 'blog_image_style', esc_html__( 'تصویر', 'tadris' ), '.blog-type-2-figure img' );
		$this->webmz_register_box_style_controls( 'blog_overlay_style', esc_html__( 'لایه روی تصویر', 'tadris' ), '.blog-type-2-header' );
		$this->webmz_register_text_style_controls( 'blog_title_style', esc_html__( 'عنوان', 'tadris' ), '.blog-type-2-header-tag a' );
	}

	private function query_args( $settings ) {
		$allowed_orderby = array( 'date', 'title', 'comment_count', 'modified', 'rand' );
		$orderby         = ! empty( $settings['order_by'] ) && in_array( $settings['order_by'], $allowed_orderby, true ) ? $settings['order_by'] : 'date';
		$order           = ! empty( $settings['order'] ) && in_array( strtoupper( $settings['order'] ), array( 'ASC', 'DESC' ), true ) ? strtoupper( $settings['order'] ) : 'DESC';

		$args = array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => min( 48, max( 1, ! empty( $settings['posts_per_page'] ) ? absint( $settings['posts_per_page'] ) : 6 ) ),
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

	private function render_placeholder_image( $title ) {
		$letter = function_exists( 'mb_substr' ) ? mb_substr( $title, 0, 1 ) : substr( $title, 0, 1 );
		?>
		<div class="blog-type-2-no-image" aria-hidden="true"><?php echo esc_html( $letter ); ?></div>
		<?php
	}

	private function render_blog_card( $post_id, $settings = array() ) {
		$post_id = absint( $post_id );
		$title   = get_the_title( $post_id );
		$link    = get_permalink( $post_id );
		$title_tag = $this->webmz_get_title_tag( $settings, 'loop_title_tag' );
		?>
		<article class="tadris-blog-type-2">
			<figure class="blog-type-2-figure">
				<a href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
					<?php
					if ( has_post_thumbnail( $post_id ) ) {
						echo webmz_get_post_loop_thumbnail( $post_id, array( 'alt' => esc_attr( $title ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						$this->render_placeholder_image( $title );
					}
					?>
				</a>
			</figure>

			<header class="blog-type-2-header">
				<<?php echo esc_attr( $title_tag ); ?> class="blog-type-2-header-tag"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></<?php echo esc_attr( $title_tag ); ?>>
			</header>
		</article>
		<?php
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$query     = new WP_Query( $this->query_args( $settings ) );
		$is_slider = isset( $settings['display_type'] ) && 'slider' === $settings['display_type'];
		$gap       = isset( $settings['grid_gap']['size'] ) ? absint( $settings['grid_gap']['size'] ) : 24;

		if ( ! $query->have_posts() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'مقاله‌ای برای نمایش یافت نشد.', 'tadris' ) . '</div>';
			return;
		}

		if ( $is_slider ) {
			$slider_conf = array(
				'slidesDesktop' => ! empty( $settings['slides_desktop'] ) ? absint( $settings['slides_desktop'] ) : 3,
				'slidesTablet'  => ! empty( $settings['slides_tablet'] ) ? absint( $settings['slides_tablet'] ) : 2,
				'slidesMobile'  => ! empty( $settings['slides_mobile'] ) ? absint( $settings['slides_mobile'] ) : 1,
				'spaceBetween'  => $gap,
				'loop'          => isset( $settings['slider_loop'] ) && 'yes' === $settings['slider_loop'],
				'autoplay'      => isset( $settings['slider_autoplay'] ) && 'yes' === $settings['slider_autoplay'],
				'autoplayDelay' => ! empty( $settings['slider_autoplay_delay'] ) ? absint( $settings['slider_autoplay_delay'] ) : 3500,
				'navigation'    => isset( $settings['show_slider_nav'] ) && 'yes' === $settings['show_slider_nav'],
				'pagination'    => isset( $settings['show_slider_pagination'] ) && 'yes' === $settings['show_slider_pagination'],
			);
			?>
			<div class="tadris-blog-loop tadris-blog-swiper swiper" data-tadris-blog-swiper="<?php echo esc_attr( wp_json_encode( $slider_conf ) ); ?>">
				<div class="swiper-wrapper">
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						?>
						<div class="swiper-slide">
							<?php $this->render_blog_card( get_the_ID(), $settings ); ?>
						</div>
					<?php endwhile; ?>
				</div>

				<?php if ( $slider_conf['navigation'] ) : ?>
					<div class="tadris-blog-swiper-button tadris-blog-swiper-button-next swiper-button-next"></div>
					<div class="tadris-blog-swiper-button tadris-blog-swiper-button-prev swiper-button-prev"></div>
				<?php endif; ?>

				<?php if ( $slider_conf['pagination'] ) : ?>
					<div class="tadris-blog-swiper-pagination swiper-pagination"></div>
				<?php endif; ?>
			</div>
			<?php
		} else {
			?>
			<div class="tadris-blog-loop tadris-blog-grid webmz-loop-grid" style="--webmz-grid-columns: <?php echo esc_attr( $settings['grid_columns_desktop'] ?? 3 ); ?>; --webmz-grid-tablet-columns: <?php echo esc_attr( $settings['grid_columns_tablet'] ?? 2 ); ?>; --webmz-grid-mobile-columns: <?php echo esc_attr( $settings['grid_columns_mobile'] ?? 1 ); ?>; --webmz-grid-gap: <?php echo esc_attr( $gap ); ?>px;">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					$this->render_blog_card( get_the_ID(), $settings );
				endwhile;
				?>
			</div>
			<?php
		}

		wp_reset_postdata();
	}
}
