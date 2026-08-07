<?php
/**
 * Blog article tile loop widget — bento/mosaic card layout.
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
 * Blog post cards in a tile grid with save, views, and comments.
 */
class Tadris_Blog_Tile_Loop_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-tadris-blog-tile-loop';
	}

	public function get_title() {
		return esc_html__( 'لوپ مقالات کاشی', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'blog', 'loop', 'post', 'tile', 'grid', 'مقاله', 'کاشی', 'لوپ' );
	}

	public function get_script_depends() {
		return array( 'webmz-tadris-widgets' );
	}

	public function get_style_depends() {
		return array( 'webmz-tadris-blog-tile-loop' );
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

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'فاصله بین کاشی‌ها', 'tadris' ),
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
					'{{WRAPPER}} .tadris-blog-tile-loop' => '--tblt-gap: {{SIZE}}{{UNIT}};',
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
				'label'   => esc_html__( 'تعداد کلمات خلاصه (کارت بزرگ)', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 22,
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
			'show_category',
			array(
				'label'        => esc_html__( 'نمایش دسته‌بندی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_excerpt',
			array(
				'label'        => esc_html__( 'نمایش خلاصه در کارت بزرگ', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_author',
			array(
				'label'        => esc_html__( 'نمایش نویسنده (کارت بزرگ)', 'tadris' ),
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
				'label'        => esc_html__( 'نمایش تاریخ', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_views',
			array(
				'label'        => esc_html__( 'نمایش بازدیدها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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

		$this->add_control(
			'views_icon',
			array(
				'label'     => esc_html__( 'آیکون بازدید', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'far fa-eye',
					'library' => 'fa-regular',
				),
				'condition' => array(
					'show_views' => 'yes',
				),
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
				'label'     => esc_html__( 'رنگ تاکیدی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary, #0878f9)',
				'selectors' => array(
					'{{WRAPPER}}' => '--tblt-primary: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'loop_style', esc_html__( 'باکس لوپ', 'tadris' ), '.tadris-blog-tile-loop' );
		$this->webmz_register_box_style_controls( 'card_style', esc_html__( 'کارت مقاله', 'tadris' ), '.tadris-blog-tile-card' );
		$this->webmz_register_box_style_controls( 'image_style', esc_html__( 'تصویر', 'tadris' ), '.blog-tile-figure img, .blog-tile-no-image' );
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.blog-tile-header-tag a' );
		$this->webmz_register_text_style_controls( 'excerpt_style', esc_html__( 'خلاصه', 'tadris' ), '.blog-tile-excerpt' );
		$this->webmz_register_text_style_controls( 'meta_style', esc_html__( 'متادیتا', 'tadris' ), '.blog-tile-meta-item' );
		$this->webmz_register_box_style_controls( 'save_style', esc_html__( 'دکمه ذخیره', 'tadris' ), '.blog-tile-actions .blog-tile-action-save' );
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
			'update_post_meta_cache' => true,
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
	 * Get category label for a post.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	private function get_category_label( $post_id ) {
		if ( function_exists( 'webmz_blog_archive_get_post_category_label' ) ) {
			return webmz_blog_archive_get_post_category_label( $post_id );
		}

		$terms = get_the_category( $post_id );

		return ! empty( $terms[0]->name ) ? $terms[0]->name : '';
	}

	/**
	 * Get formatted post date.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	private function get_post_date_label( $post_id ) {
		if ( function_exists( 'webmz_blog_archive_format_post_date' ) ) {
			$label = webmz_blog_archive_format_post_date( $post_id );
		} else {
			$label = get_the_date( '', $post_id );
		}

		return function_exists( 'webmz_to_persian_digits' ) ? webmz_to_persian_digits( $label ) : $label;
	}

	/**
	 * Get post views count.
	 *
	 * @param int $post_id Post ID.
	 * @return int
	 */
	private function get_post_views( $post_id ) {
		if ( function_exists( 'webmz_tadris_get_post_views' ) ) {
			return webmz_tadris_get_post_views( $post_id );
		}

		return absint( get_post_meta( $post_id, '_webmz_post_views', true ) );
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
		<div class="blog-tile-no-image" aria-hidden="true"><?php echo esc_html( $letter ); ?></div>
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
		if ( function_exists( 'webmz_blog_archive_get_card_excerpt' ) ) {
			$excerpt = webmz_blog_archive_get_card_excerpt( $post_id );
			return wp_trim_words( $excerpt, max( 4, absint( $words ) ), ' [...]' );
		}

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
	 * Format a numeric stat for display.
	 *
	 * @param int $value Numeric value.
	 * @return string
	 */
	private function format_stat( $value ) {
		$label = number_format_i18n( absint( $value ) );

		return function_exists( 'webmz_to_persian_digits' ) ? webmz_to_persian_digits( $label ) : $label;
	}

	/**
	 * Render one blog tile card.
	 *
	 * @param int                 $post_id  Post ID.
	 * @param array<string,mixed> $settings Widget settings.
	 * @param bool                $featured Whether this is the featured/large card.
	 * @return void
	 */
	private function render_blog_card( $post_id, $settings, $featured = false ) {
		$post_id     = absint( $post_id );
		$title       = get_the_title( $post_id );
		$link        = get_permalink( $post_id );
		$title_tag   = $this->webmz_get_title_tag( $settings, 'title_tag' );
		$author_id   = (int) get_post_field( 'post_author', $post_id );
		$author_name = get_the_author_meta( 'display_name', $author_id );
		$card_class  = 'tadris-blog-tile-card' . ( $featured ? ' tadris-blog-tile-card--featured' : ' tadris-blog-tile-card--standard' );
		?>
		<article class="<?php echo esc_attr( $card_class ); ?>" data-post-id="<?php echo esc_attr( (string) $post_id ); ?>">
			<div class="blog-tile-inner">
				<figure class="blog-tile-figure">
					<a href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
						<?php
						if ( has_post_thumbnail( $post_id ) ) {
							echo webmz_get_post_loop_thumbnail( $post_id, array( 'alt' => esc_attr( $title ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							$this->render_placeholder_image( $title );
						}
						?>
					</a>

					<?php if ( isset( $settings['show_category'] ) && 'yes' === $settings['show_category'] ) : ?>
						<?php $category = $this->get_category_label( $post_id ); ?>
						<?php if ( '' !== $category ) : ?>
							<span class="blog-tile-category"><?php echo esc_html( $category ); ?></span>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( isset( $settings['show_save'] ) && 'yes' === $settings['show_save'] && function_exists( '\webmz_tadris_render_post_action_buttons' ) ) : ?>
						<?php
						\webmz_tadris_render_post_action_buttons(
							$post_id,
							array(
								'wrapper_class' => 'blog-tile-actions',
								'save_class'    => 'blog-tile-action-button blog-tile-action-save',
								'icon_only'     => true,
								'show_share'    => false,
							)
						);
						?>
					<?php endif; ?>
				</figure>

				<div class="blog-tile-body">
					<header class="blog-tile-header">
						<<?php echo esc_attr( $title_tag ); ?> class="blog-tile-header-tag">
							<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a>
						</<?php echo esc_attr( $title_tag ); ?>>
					</header>

					<?php if ( $featured && isset( $settings['show_excerpt'] ) && 'yes' === $settings['show_excerpt'] ) : ?>
						<?php $excerpt = $this->get_card_excerpt( $post_id, $settings['excerpt_words'] ?? 22 ); ?>
						<?php if ( '' !== $excerpt ) : ?>
							<div class="blog-tile-excerpt"><p><?php echo esc_html( $excerpt ); ?></p></div>
						<?php endif; ?>
					<?php endif; ?>

					<?php
					$show_reading  = isset( $settings['show_reading_time'] ) && 'yes' === $settings['show_reading_time'];
					$show_date     = isset( $settings['show_date'] ) && 'yes' === $settings['show_date'];
					$show_views    = isset( $settings['show_views'] ) && 'yes' === $settings['show_views'];
					$show_comments = isset( $settings['show_comments'] ) && 'yes' === $settings['show_comments'];
					$show_author   = $featured && isset( $settings['show_author'] ) && 'yes' === $settings['show_author'];
					$has_meta      = $show_reading || $show_date || $show_views || $show_comments || $show_author;
					?>
					<?php if ( $has_meta ) : ?>
						<footer class="blog-tile-meta">
							<div class="blog-tile-meta-start">
								<?php if ( $show_author ) : ?>
									<span class="blog-tile-meta-item blog-tile-meta-author">
										<?php echo get_avatar( $author_id, 32, '', $author_name, array( 'class' => 'blog-tile-author-avatar' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<span class="blog-tile-author-name"><?php echo esc_html( $author_name ); ?></span>
									</span>
								<?php endif; ?>

								<?php if ( $show_date || $show_views || $show_comments ) : ?>
									<div class="blog-tile-meta-stats">
										<?php if ( $show_date ) : ?>
											<span class="blog-tile-meta-item blog-tile-meta-date">
												<?php Icons_Manager::render_icon( $settings['date_icon'], array( 'aria-hidden' => 'true' ) ); ?>
												<span><?php echo esc_html( $this->get_post_date_label( $post_id ) ); ?></span>
											</span>
										<?php endif; ?>

										<?php if ( $show_views ) : ?>
											<span class="blog-tile-meta-item blog-tile-meta-views">
												<?php Icons_Manager::render_icon( $settings['views_icon'], array( 'aria-hidden' => 'true' ) ); ?>
												<span><?php echo esc_html( $this->format_stat( $this->get_post_views( $post_id ) ) ); ?></span>
											</span>
										<?php endif; ?>

										<?php if ( $show_comments ) : ?>
											<span class="blog-tile-meta-item blog-tile-meta-comments">
												<?php Icons_Manager::render_icon( $settings['comments_icon'], array( 'aria-hidden' => 'true' ) ); ?>
												<span><?php echo esc_html( $this->format_stat( get_comments_number( $post_id ) ) ); ?></span>
											</span>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>

							<?php if ( $show_reading ) : ?>
								<div class="blog-tile-meta-end">
									<span class="blog-tile-meta-item blog-tile-meta-reading">
										<?php Icons_Manager::render_icon( $settings['reading_icon'], array( 'aria-hidden' => 'true' ) ); ?>
										<span><?php echo esc_html( $this->get_reading_time_label( $post_id, $settings ) ); ?></span>
									</span>
								</div>
							<?php endif; ?>
						</footer>
					<?php endif; ?>
				</div>
			</div>
		</article>
		<?php
	}

	/**
	 * Render one tile group (up to 6 posts).
	 *
	 * @param array<int>          $post_ids Post IDs.
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_tile_group( $post_ids, $settings ) {
		$post_ids = array_values( array_filter( array_map( 'absint', $post_ids ) ) );

		if ( empty( $post_ids ) ) {
			return;
		}

		$bento_posts = array_slice( $post_ids, 0, 3 );
		$equal_posts = array_slice( $post_ids, 3, 3 );
		$featured_id = $bento_posts[0] ?? 0;
		$side_posts  = array_slice( $bento_posts, 1 );
		?>
		<div class="tadris-blog-tile-group">
			<?php if ( $featured_id ) : ?>
				<div class="tadris-blog-tile-row tadris-blog-tile-row--bento">
					<?php $this->render_blog_card( $featured_id, $settings, true ); ?>

					<?php if ( ! empty( $side_posts ) ) : ?>
						<div class="tadris-blog-tile-stack">
							<?php foreach ( $side_posts as $post_id ) : ?>
								<?php $this->render_blog_card( $post_id, $settings, false ); ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $equal_posts ) ) : ?>
				<div class="tadris-blog-tile-row tadris-blog-tile-row--equal">
					<?php foreach ( $equal_posts as $post_id ) : ?>
						<?php $this->render_blog_card( $post_id, $settings, false ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$query    = new WP_Query( $this->query_args( $settings ) );

		if ( ! $query->have_posts() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'مقاله‌ای برای نمایش یافت نشد.', 'tadris' ) . '</div>';
			return;
		}

		$post_ids = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_ids[] = get_the_ID();
		}

		wp_reset_postdata();

		$groups = array_chunk( $post_ids, 6 );
		?>
		<div class="tadris-blog-tile-loop">
			<?php foreach ( $groups as $group_ids ) : ?>
				<?php $this->render_tile_group( $group_ids, $settings ); ?>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
