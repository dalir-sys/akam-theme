<?php
/**
 * Video post Elementor widgets following the user supplied HTML templates.
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
 * Featured large video post widget.
 */
class Tadris_Large_Video_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-tadris-large-video';
	}

	public function get_title() {
		return esc_html__( 'پست بزرگ ویدیویی', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-video-camera';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_script_depends() {
		return array( 'webmz-plyr', 'webmz-tadris-widgets' );
	}

	public function get_style_depends() {
		return array( 'webmz-plyr' );
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
					'manual'   => esc_html__( 'انتخاب یک نوشته', 'tadris' ),
					'category' => esc_html__( 'آخرین نوشته یک دسته', 'tadris' ),
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
			'texts',
			array(
				'label' => esc_html__( 'متن و آیکون‌ها', 'tadris' ),
			)
		);

		$this->add_control(
			'play_icon',
			array(
				'label'   => esc_html__( 'آیکون عنوان', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-play',
					'library' => 'fa-solid',
				),
			)
		);

		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ HTML عنوان نوشته', 'tadris' ) );

		$this->add_control(
			'save_text',
			array(
				'label'   => esc_html__( 'متن ذخیره', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'ذخیره', 'tadris' ),
			)
		);

		$this->add_control(
			'remove_saved_text',
			array(
				'label'   => esc_html__( 'متن بعد از ذخیره', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'حذف از ذخیره', 'tadris' ),
			)
		);


		$this->webmz_register_icon_color_mode_control( 'play_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون پخش', 'tadris' ) );

		$this->add_control(
			'save_icon',
			array(
				'label'   => esc_html__( 'آیکون ذخیره', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'far fa-bookmark',
					'library' => 'fa-regular',
				),
			)
		);

		$this->add_control(
			'share_text',
			array(
				'label'   => esc_html__( 'متن اشتراک‌گذاری', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'اشتراک گذاری', 'tadris' ),
			)
		);


		$this->webmz_register_icon_color_mode_control( 'save_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون ذخیره', 'tadris' ) );

		$this->add_control(
			'share_icon',
			array(
				'label'   => esc_html__( 'آیکون اشتراک‌گذاری', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-share-alt',
					'library' => 'fa-solid',
				),
			)
		);


		$this->webmz_register_icon_color_mode_control( 'share_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون اشتراک‌گذاری', 'tadris' ) );

		$this->add_control(
			'instructor_label',
			array(
				'label'   => esc_html__( 'برچسب مدرس', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مدرس دوره', 'tadris' ),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'article_style', esc_html__( 'کادر ویدیو', 'tadris' ), '.tadris-lg-video' );
		$this->webmz_register_box_style_controls( 'player_style', esc_html__( 'پلیر', 'tadris' ), '.tadris-lg-video-player' );
		$this->webmz_register_box_style_controls( 'header_style', esc_html__( 'سربرگ', 'tadris' ), '.tadris-lg-video-header' );
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان نوشته', 'tadris' ), '.tadris-lg-video-header-tag a' );
		$this->webmz_register_icon_style_controls( 'header_icon_style', esc_html__( 'آیکون عنوان', 'tadris' ), '.tadris-lg-video-header-title' );
		$this->webmz_register_text_style_controls( 'action_style', esc_html__( 'دکمه‌های عملیاتی', 'tadris' ), '.tadrist-video-aciton' );
		$this->webmz_register_box_style_controls( 'action_box_style', esc_html__( 'باکس دکمه‌های عملیاتی', 'tadris' ), '.tadrist-video-aciton' );
		$this->webmz_register_text_style_controls( 'author_style', esc_html__( 'نام مدرس', 'tadris' ), '.tadris-lg-video-author-name' );
		$this->webmz_register_text_style_controls( 'description_style', esc_html__( 'درباره مدرس', 'tadris' ), '.tadris-lg-video-text p' );
		$this->webmz_register_box_style_controls( 'body_style', esc_html__( 'بدنه اطلاعات', 'tadris' ), '.tadris-lg-video-body' );
		$this->webmz_register_box_style_controls( 'share_style', esc_html__( 'باکس اشتراک‌گذاری', 'tadris' ), '.tadris-share-box' );
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$post = \webmz_tadris_resolve_video_post( $s['source'], absint( $s['post_id'] ), absint( $s['category_id'] ) );

		if ( ! $post ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'نوشته ویدیویی برای نمایش انتخاب نشده است.', 'tadris' ) . '</div>';
			return;
		}

		$video_url   = get_post_meta( $post->ID, '_webmz_video_url', true );
		$poster      = get_the_post_thumbnail_url( $post, 'large' );
		$author_id   = (int) $post->post_author;
		$author_name = get_the_author_meta( 'display_name', $author_id );
		$author_bio  = get_the_author_meta( 'description', $author_id );
		$author_bio  = $author_bio ? $author_bio : wp_trim_words( wp_strip_all_tags( $post->post_content ), 55 );
		$saved       = \webmz_tadris_user_has_favorite( get_current_user_id(), $post->ID );
		$link        = get_permalink( $post );
		$title_tag   = $this->webmz_get_title_tag( $s, 'title_tag' );
		?>
		<article class="tadris-lg-video" data-tadris-post-id="<?php echo esc_attr( $post->ID ); ?>">
			<div class="tadris-lg-video-player">
				<video class="tadris-player-tag" playsinline controls data-webmz-history-player="1" data-webmz-history-post-id="<?php echo esc_attr( $post->ID ); ?>" data-webmz-history-type="video"<?php echo $poster ? ' data-poster="' . esc_url( $poster ) . '" poster="' . esc_url( $poster ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php if ( $video_url ) : ?>
						<source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
					<?php endif; ?>
				</video>
			</div>

			<div class="tadris-lg-video-inner">
				<header class="tadris-lg-video-header">
					<div class="tadris-lg-video-header-title <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s, 'play_icon_color_mode' ) ); ?>">
						<?php Icons_Manager::render_icon( $s['play_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						<<?php echo esc_attr( $title_tag ); ?> class="tadris-lg-video-header-tag">
							<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a>
						</<?php echo esc_attr( $title_tag ); ?>>
					</div>

					<div class="tadris-lg-video-header-actions">
						<button
							type="button"
							class="tadrist-video-aciton video-action-save<?php echo $saved ? ' is-saved' : ''; ?>"
							data-webmz-favorite-post="<?php echo esc_attr( $post->ID ); ?>"
							data-save-label="<?php echo esc_attr( $s['save_text'] ); ?>"
							data-remove-label="<?php echo esc_attr( $s['remove_saved_text'] ); ?>"
							aria-pressed="<?php echo $saved ? 'true' : 'false'; ?>"
						>
							<span class="webmz-favorite-label">
								<?php echo esc_html( $saved ? $s['remove_saved_text'] : $s['save_text'] ); ?>
							</span>

							<span class="webmz-favorite-icon webmz-favorite-icon--save <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s, 'save_icon_color_mode' ) ); ?>" aria-hidden="true">
								<?php Icons_Manager::render_icon( $s['save_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							</span>

							<span class="webmz-favorite-icon webmz-favorite-icon--remove" aria-hidden="true">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-bookmark-off">
									<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
									<path d="M7.708 3.721a3.982 3.982 0 0 1 2.292 -.721h4a4 4 0 0 1 4 4v7m0 4v3l-6 -4l-6 4v-14c0 -.308 .035 -.609 .1 -.897"/>
									<path d="M3 3l18 18"/>
								</svg>
							</span>
						</button>

						<button
							type="button"
							class="tadrist-video-aciton video-action-share <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s, 'share_icon_color_mode' ) ); ?>"
							data-webmz-share-toggle="<?php echo esc_attr( $post->ID ); ?>"
							data-share-url="<?php echo esc_url( $link ); ?>"
							data-share-title="<?php echo esc_attr( get_the_title( $post ) ); ?>"
						>
							<?php echo esc_html( $s['share_text'] ); ?>
							<?php Icons_Manager::render_icon( $s['share_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</button>
					</div>
				</header>

				<div class="tadris-share-box" data-webmz-share-box="<?php echo esc_attr( $post->ID ); ?>" hidden>
					<a href="https://t.me/share/url?url=<?php echo rawurlencode( $link ); ?>&text=<?php echo rawurlencode( get_the_title( $post ) ); ?>" target="_blank" rel="noopener">Telegram</a>
					<a href="https://wa.me/?text=<?php echo rawurlencode( get_the_title( $post ) . ' ' . $link ); ?>" target="_blank" rel="noopener">WhatsApp</a>
					<a href="mailto:?subject=<?php echo rawurlencode( get_the_title( $post ) ); ?>&body=<?php echo rawurlencode( $link ); ?>">Email</a>
					<button type="button" data-webmz-copy-link="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'کپی لینک', 'tadris' ); ?></button>
				</div>

				<div class="tadris-lg-video-body">
					<div class="tadris-lg-video-author">
						<div class="tadris-lg-video-author-cv">
							<?php echo get_avatar( $author_id, 96, '', $author_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>

						<div class="tadris-lg-video-author-name">
							<span><?php echo esc_html( $s['instructor_label'] ); ?></span>
							<strong><?php echo esc_html( $author_name ); ?></strong>
						</div>
					</div>

					<div class="tadris-lg-video-text">
						<p><?php echo esc_html( $author_bio ); ?></p>
					</div>
				</div>
			</div>

			<div class="tadris-widget-message" data-webmz-widget-message aria-live="polite"></div>
		</article>
		<?php
	}
}

/**
 * Video post cards loop widget.
 */
class Tadris_Video_Loop_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-tadris-video-loop';
	}

	public function get_title() {
		return esc_html__( 'لوپ مطالب ویدیویی', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'query',
			array(
				'label' => esc_html__( 'کوئری مطالب', 'tadris' ),
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
				'label'   => esc_html__( 'تعداد نوشته', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4,
				'min'     => 1,
				'max'     => 24,
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
			'author_label',
			array(
				'label'   => esc_html__( 'برچسب مدرس', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مدرس', 'tadris' ),
			)
		);

		$this->add_control(
			'views_label',
			array(
				'label'   => esc_html__( 'برچسب بازدید', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'بازدیدها', 'tadris' ),
			)
		);

		$this->webmz_register_title_tag_control( 'loop_title_tag', esc_html__( 'تگ HTML عنوان کارت‌ها', 'tadris' ) );

		$this->add_control(
			'play_icon',
			array(
				'label'   => esc_html__( 'آیکون پخش', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-play',
					'library' => 'fa-solid',
				),
			)
		);


		$this->webmz_register_icon_color_mode_control( 'play_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون پخش', 'tadris' ) );

		$this->add_control(
			'author_icon',
			array(
				'label'   => esc_html__( 'آیکون مدرس', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'far fa-user',
					'library' => 'fa-regular',
				),
			)
		);


		$this->webmz_register_icon_color_mode_control( 'author_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون مدرس', 'tadris' ) );

		$this->add_control(
			'views_icon',
			array(
				'label'   => esc_html__( 'آیکون بازدید', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-play',
					'library' => 'fa-solid',
				),
			)
		);


		$this->webmz_register_icon_color_mode_control( 'views_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون بازدید', 'tadris' ) );
		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'loop_style', esc_html__( 'لوپ', 'tadris' ), '.tadris-loop-type-1' );
		$this->webmz_register_box_style_controls( 'card_style', esc_html__( 'کارت ویدیو', 'tadris' ), '.tadris-content-type-1' );
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.type-1-header-tag a' );
		$this->webmz_register_text_style_controls( 'meta_style', esc_html__( 'اطلاعات فوتر', 'tadris' ), '.type-1-footer-text' );
		$this->webmz_register_icon_style_controls( 'play_style', esc_html__( 'آیکون پخش', 'tadris' ), '.type-1-play-icon' );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$query_args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => min( 24, max( 1, absint( $s['posts_per_page'] ) ) ),
			'cat'                 => absint( $s['category_id'] ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( ! empty( $s['exclude_posts'] ) && is_array( $s['exclude_posts'] ) ) {
			$query_args['post__not_in'] = array_values( array_filter( array_map( 'absint', $s['exclude_posts'] ) ) );
		}

		$q = new WP_Query( $query_args );

		if ( ! $q->have_posts() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'مطلبی برای نمایش یافت نشد.', 'tadris' ) . '</div>';
			return;
		}
		?>
		<div class="tadris-loop-type-1 mt">
			<?php
			while ( $q->have_posts() ) :
				$q->the_post();

				$post   = get_post();
				$loop_title_tag = $this->webmz_get_title_tag( $s, 'loop_title_tag' );
				$author = get_the_author_meta( 'display_name', $post->post_author );
				$views  = \webmz_tadris_get_post_views( $post->ID );
				?>
				<article class="tadris-content-type-1">
					<figure class="type-1-figure">
						<a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
							<?php
							echo webmz_get_post_loop_thumbnail(
								get_the_ID(),
								array(
									'alt' => esc_attr( get_the_title() ),
								)
							); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							<div class="type-1-play-icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s, 'play_icon_color_mode' ) ); ?>" aria-hidden="true">
								<?php Icons_Manager::render_icon( $s['play_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							</div>
						</a>
					</figure>

					<div class="type-1-content">
						<header class="type-1-header">
							<<?php echo esc_attr( $loop_title_tag ); ?> class="type-1-header-tag">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</<?php echo esc_attr( $loop_title_tag ); ?>>
						</header>

						<footer class="type-1-footer">
							<div class="type-1-footer-badge">
								<div class="type-1-footer-icon icon-author-color <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s, 'author_icon_color_mode' ) ); ?>">
									<?php Icons_Manager::render_icon( $s['author_icon'], array( 'aria-hidden' => 'true' ) ); ?>
								</div>

								<div class="type-1-footer-text">
									<span><?php echo esc_html( $s['author_label'] ); ?></span>
									<strong><?php echo esc_html( $author ); ?></strong>
								</div>
							</div>

							<div class="type-1-footer-badge">
								<div class="type-1-footer-icon icon-plays-color <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s, 'views_icon_color_mode' ) ); ?>">
									<?php Icons_Manager::render_icon( $s['views_icon'], array( 'aria-hidden' => 'true' ) ); ?>
								</div>

								<div class="type-1-footer-text">
									<span><?php echo esc_html( $s['views_label'] ); ?></span>
									<strong><?php echo esc_html( number_format_i18n( $views ) ); ?></strong>
								</div>
							</div>
						</footer>
					</div>
				</article>
			<?php endwhile; ?>

			<?php wp_reset_postdata(); ?>
		</div>
		<?php
	}
}