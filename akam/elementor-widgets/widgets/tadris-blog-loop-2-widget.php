<?php
/**
 * Blog article loop widget — card layout type 2.
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
 * Blog post cards with excerpt, meta, save button, grid/slider layout.
 */
class Tadris_Blog_Loop_2_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-tadris-blog-loop-2';
	}

	public function get_title() {
		return esc_html__( 'لوپ مقالات ۲', 'tadris' );
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
		return array( 'webmz-swiper', 'webmz-tadris-blog-loop-2' );
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
				'condition' => array(
					'display_type' => 'grid',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'فاصله بین آیتم‌ها', 'tadris' ),
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
					'{{WRAPPER}} .tadris-blog-loop-2' => '--tbl2-gap: {{SIZE}}{{UNIT}};',
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
					'default'   => 'mobile' === $device ? 1 : ( 'tablet' === $device ? 2 : 3 ),
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
				'label'     => esc_html__( 'زمان پخش خودکار (میلی‌ثانیه)', 'tadris' ),
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
			'card_content',
			array(
				'label' => esc_html__( 'محتوای کارت', 'tadris' ),
			)
		);

		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ HTML عنوان', 'tadris' ) );

		$this->add_control(
			'excerpt_words',
			array(
				'label'   => esc_html__( 'تعداد کلمات خلاصه', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 18,
				'min'     => 4,
				'max'     => 60,
			)
		);

		$this->add_control(
			'words_per_minute',
			array(
				'label'   => esc_html__( 'کلمات در دقیقه (زمان مطالعه)', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 200,
				'min'     => 80,
				'max'     => 400,
			)
		);

		$this->add_control(
			'reading_time_suffix',
			array(
				'label'   => esc_html__( 'پسوند زمان مطالعه', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'دقیقه مطالعه', 'tadris' ),
			)
		);

		$this->add_control(
			'show_excerpt',
			array(
				'label'        => esc_html__( 'نمایش خلاصه', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
			'show_date',
			array(
				'label'        => esc_html__( 'نمایش تاریخ نسبی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_save',
			array(
				'label'        => esc_html__( 'نمایش دکمه ذخیره', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
				'condition' => array(
					'show_reading_time' => 'yes',
				),
			)
		);

		$this->add_control(
			'date_icon',
			array(
				'label'     => esc_html__( 'آیکون تاریخ', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'far fa-calendar',
					'library' => 'fa-regular',
				),
				'condition' => array(
					'show_date' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'accent_style',
			array(
				'label' => esc_html__( 'رنگ اصلی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'primary_color',
			array(
				'label'     => esc_html__( 'رنگ زمان مطالعه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f97316',
				'selectors' => array(
					'{{WRAPPER}}' => '--tbl2-primary: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'loop_style', esc_html__( 'ردیف/اسلایدر', 'tadris' ), '.tadris-blog-loop-2' );
		$this->webmz_register_box_style_controls( 'card_style', esc_html__( 'کارت مقاله', 'tadris' ), '.tadris-blog-type-3' );
		$this->webmz_register_box_style_controls( 'image_style', esc_html__( 'تصویر', 'tadris' ), '.blog-type-3-figure img, .blog-type-3-no-image' );
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.blog-type-3-header-tag a' );
		$this->webmz_register_text_style_controls( 'excerpt_style', esc_html__( 'خلاصه', 'tadris' ), '.blog-type-3-excerpt' );
		$this->webmz_register_text_style_controls( 'meta_style', esc_html__( 'زمان مطالعه / تاریخ', 'tadris' ), '.blog-type-3-meta-item' );
		$this->webmz_register_box_style_controls( 'save_style', esc_html__( 'دکمه ذخیره', 'tadris' ), '.blog-type-3-actions .blog-type-3-action-save' );
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

	/**
	 * Render placeholder when post has no thumbnail.
	 *
	 * @param string $title Post title.
	 * @return void
	 */
	private function render_placeholder_image( $title ) {
		$letter = function_exists( 'mb_substr' ) ? mb_substr( $title, 0, 1 ) : substr( $title, 0, 1 );
		?>
		<div class="blog-type-3-no-image" aria-hidden="true"><?php echo esc_html( $letter ); ?></div>
		<?php
	}

	/**
	 * Get trimmed excerpt for a post card.
	 *
	 * @param int $post_id Post ID.
	 * @param int $words   Word limit.
	 * @return string
	 */
	private function get_card_excerpt( $post_id, $words ) {
		$post_id = absint( $post_id );
		$words   = max( 4, absint( $words ) );

		if ( ! $post_id ) {
			return '';
		}

		$excerpt = get_the_excerpt( $post_id );

		if ( '' === trim( $excerpt ) ) {
			$excerpt = (string) get_post_field( 'post_content', $post_id );
		}

		return wp_trim_words( wp_strip_all_tags( $excerpt ), $words, ' [...]' );
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
		$suffix  = ! empty( $settings['reading_time_suffix'] ) ? $settings['reading_time_suffix'] : esc_html__( 'دقیقه مطالعه', 'tadris' );
		$label   = trim( number_format_i18n( $minutes ) . ' ' . $suffix );

		return function_exists( 'webmz_to_persian_digits' ) ? webmz_to_persian_digits( $label ) : $label;
	}

	/**
	 * Render one blog card.
	 *
	 * @param int                 $post_id  Post ID.
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_blog_card( $post_id, $settings ) {
		$post_id   = absint( $post_id );
		$title     = get_the_title( $post_id );
		$link      = get_permalink( $post_id );
		$title_tag = $this->webmz_get_title_tag( $settings, 'title_tag' );
		?>
		<article class="tadris-blog-type-3" data-post-id="<?php echo esc_attr( (string) $post_id ); ?>">
			<figure class="blog-type-3-figure">
				<a href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
					<?php
					if ( has_post_thumbnail( $post_id ) ) {
						echo webmz_get_post_loop_thumbnail( $post_id, array( 'alt' => esc_attr( $title ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						$this->render_placeholder_image( $title );
					}
					?>
				</a>

				<?php if ( isset( $settings['show_save'] ) && 'yes' === $settings['show_save'] && function_exists( '\webmz_tadris_render_post_action_buttons' ) ) : ?>
					<?php
					\webmz_tadris_render_post_action_buttons(
						$post_id,
						array(
							'wrapper_class' => 'blog-type-3-actions',
							'save_class'    => 'blog-type-3-action-button blog-type-3-action-save',
							'icon_only'     => true,
							'show_share'    => false,
						)
					);
					?>
				<?php endif; ?>
			</figure>

			<div class="blog-type-3-body">
				<header class="blog-type-3-header">
					<<?php echo esc_attr( $title_tag ); ?> class="blog-type-3-header-tag">
						<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a>
					</<?php echo esc_attr( $title_tag ); ?>>
				</header>

				<?php if ( isset( $settings['show_excerpt'] ) && 'yes' === $settings['show_excerpt'] ) : ?>
					<?php $excerpt = $this->get_card_excerpt( $post_id, $settings['excerpt_words'] ?? 18 ); ?>
					<?php if ( '' !== $excerpt ) : ?>
						<div class="blog-type-3-excerpt"><p><?php echo esc_html( $excerpt ); ?></p></div>
					<?php endif; ?>
				<?php endif; ?>

				<?php
				$show_reading = isset( $settings['show_reading_time'] ) && 'yes' === $settings['show_reading_time'];
				$show_date    = isset( $settings['show_date'] ) && 'yes' === $settings['show_date'];
				?>
				<?php if ( $show_reading || $show_date ) : ?>
					<footer class="blog-type-3-meta">
						<?php if ( $show_reading ) : ?>
							<span class="blog-type-3-meta-item blog-type-3-meta-reading">
								<?php Icons_Manager::render_icon( $settings['reading_icon'], array( 'aria-hidden' => 'true' ) ); ?>
								<span><?php echo esc_html( $this->get_reading_time_label( $post_id, $settings ) ); ?></span>
							</span>
						<?php endif; ?>

						<?php if ( $show_date ) : ?>
							<span class="blog-type-3-meta-item blog-type-3-meta-date">
								<?php Icons_Manager::render_icon( $settings['date_icon'], array( 'aria-hidden' => 'true' ) ); ?>
								<span>
									<?php
									echo esc_html(
										function_exists( 'webmz_tadris_get_post_relative_date' )
											? webmz_tadris_get_post_relative_date( $post_id )
											: ''
									);
									?>
								</span>
							</span>
						<?php endif; ?>
					</footer>
				<?php endif; ?>
			</div>
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
				'pagination'    => ! isset( $settings['show_slider_pagination'] ) || 'yes' === $settings['show_slider_pagination'],
			);
			?>
			<div
				class="tadris-blog-loop-2 tadris-blog-loop-2-swiper swiper"
				data-tadris-blog-loop-2-swiper="<?php echo esc_attr( wp_json_encode( $slider_conf ) ); ?>"
				style="--tbl2-gap: <?php echo esc_attr( $gap ); ?>px;"
			>
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
					<div class="tadris-blog-loop-2-swiper-button tadris-blog-loop-2-swiper-button-next swiper-button-next"></div>
					<div class="tadris-blog-loop-2-swiper-button tadris-blog-loop-2-swiper-button-prev swiper-button-prev"></div>
				<?php endif; ?>

				<?php if ( $slider_conf['pagination'] ) : ?>
					<div class="tadris-blog-loop-2-swiper-pagination swiper-pagination"></div>
				<?php endif; ?>
			</div>
			<?php
		} else {
			?>
			<div
				class="tadris-blog-loop-2 tadris-blog-loop-2-grid webmz-loop-grid"
				style="
					--webmz-grid-columns: <?php echo esc_attr( $settings['grid_columns_desktop'] ?? 3 ); ?>;
					--webmz-grid-tablet-columns: <?php echo esc_attr( $settings['grid_columns_tablet'] ?? 2 ); ?>;
					--webmz-grid-mobile-columns: <?php echo esc_attr( $settings['grid_columns_mobile'] ?? 1 ); ?>;
					--tbl2-gap: <?php echo esc_attr( $gap ); ?>px;
				"
			>
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
