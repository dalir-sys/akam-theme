<?php
/**
 * YouTube-like playlist Elementor widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use WP_Query;

defined( 'ABSPATH' ) || exit;

class Youtube_Playlist_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-youtube-playlist';
	}

	public function get_title() {
		return esc_html__( 'پلی لیست یوتوبی', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-play';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'playlist', 'youtube', 'video', 'پلی لیست', 'یوتوب' );
	}

	public function get_script_depends() {
		return array( 'webmz-plyr', 'webmz-tadris-widgets', 'webmz-view-history', 'webmz-youtube-playlist' );
	}

	public function get_style_depends() {
		return array( 'webmz-plyr', 'webmz-plyr-widgets', 'webmz-youtube-playlist' );
	}

	private function get_category_options() {
		$options = array( '' => esc_html__( 'همه دسته‌ها', 'tadris' ) );
		$terms   = get_terms(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $term->name;
			}
		}

		return $options;
	}

	private function get_tag_options() {
		$options = array( '' => esc_html__( 'همه برچسب‌ها', 'tadris' ) );
		$terms   = get_terms(
			array(
				'taxonomy'   => 'post_tag',
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $term->name;
			}
		}

		return $options;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_query',
			array(
				'label' => esc_html__( 'تنظیمات محتوا', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'playlist_title',
			array(
				'label'       => esc_html__( 'عنوان پلی‌لیست', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'خالی = عنوان اولین ویدیو', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'filter_by',
			array(
				'label'   => esc_html__( 'فیلتر بر اساس', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => array(
					'category' => esc_html__( 'دسته‌بندی', 'tadris' ),
					'tag'      => esc_html__( 'برچسب', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'category',
			array(
				'label'       => esc_html__( 'دسته‌بندی', 'tadris' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->get_category_options(),
				'condition'   => array(
					'filter_by' => 'category',
				),
			)
		);

		$this->add_control(
			'tags',
			array(
				'label'       => esc_html__( 'برچسب', 'tadris' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->get_tag_options(),
				'condition'   => array(
					'filter_by' => 'tag',
				),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'تعداد ویدیوها', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8,
				'min'     => 1,
				'max'     => 50,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => esc_html__( 'مرتب‌سازی', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'     => esc_html__( 'تاریخ', 'tadris' ),
					'title'    => esc_html__( 'عنوان', 'tadris' ),
					'modified' => esc_html__( 'آخرین ویرایش', 'tadris' ),
					'rand'     => esc_html__( 'تصادفی', 'tadris' ),
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
			'require_login_for_download',
			array(
				'label'        => esc_html__( 'دانلود فقط برای کاربران لاگین', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'فعال', 'tadris' ),
				'label_off'    => esc_html__( 'غیرفعال', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_icons',
			array(
				'label' => esc_html__( 'آیکون‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'item_active_icon',
			array(
				'label'   => esc_html__( 'آیکون جلسه فعال', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-play-circle',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'item_done_icon',
			array(
				'label'   => esc_html__( 'آیکون سایر جلسات', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-check-circle',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'download_icon',
			array(
				'label'   => esc_html__( 'آیکون دانلود', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-download',
					'library' => 'fa-solid',
				),
			)
		);

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
			'saved_remove_icon',
			array(
				'label'   => esc_html__( 'آیکون حذف از ذخیره', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-bookmark',
					'library' => 'fa-solid',
				),
			)
		);

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

		$this->end_controls_section();
	}

	private function get_query_args( $settings ) {
		$args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => ! empty( $settings['posts_per_page'] ) ? absint( $settings['posts_per_page'] ) : 8,
			'orderby'             => ! empty( $settings['orderby'] ) ? sanitize_key( $settings['orderby'] ) : 'date',
			'order'               => ! empty( $settings['order'] ) ? sanitize_key( $settings['order'] ) : 'DESC',
			'ignore_sticky_posts' => true,
			'meta_query'          => array(
				array(
					'key'     => '_webmz_video_url',
					'value'   => '',
					'compare' => '!=',
				),
			),
		);

		$filter_by = ! empty( $settings['filter_by'] ) ? sanitize_key( $settings['filter_by'] ) : 'category';

		if ( 'tag' === $filter_by && ! empty( $settings['tags'] ) && is_array( $settings['tags'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'post_tag',
					'field'    => 'term_id',
					'terms'    => array_map( 'absint', $settings['tags'] ),
				),
			);
		} elseif ( ! empty( $settings['category'] ) && is_array( $settings['category'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => array_map( 'absint', $settings['category'] ),
				),
			);
		}

		return $args;
	}

	private function is_item_completed( $item ) {
		if ( empty( $item['duration'] ) || empty( $item['progress_current'] ) ) {
			return false;
		}

		$duration  = (float) $item['duration'];
		$current   = (float) $item['progress_current'];
		$threshold = max( $duration - 3, $duration * 0.95 );

		return $current >= $threshold;
	}

	private function get_total_progress( $items ) {
		$total_duration = 0.0;
		$total_current  = 0.0;

		foreach ( $items as $item ) {
			if ( empty( $item['duration'] ) ) {
				continue;
			}

			$duration = (float) $item['duration'];
			$current  = ! empty( $item['progress_current'] ) ? (float) $item['progress_current'] : 0.0;

			$total_duration += $duration;
			$total_current  += min( $duration, max( 0.0, $current ) );
		}

		if ( $total_duration <= 0 ) {
			return 0;
		}

		return (int) round( min( 100, max( 0, ( $total_current / $total_duration ) * 100 ) ) );
	}

	private function get_total_duration_label( $items ) {
		$total = 0;
		foreach ( $items as $item ) {
			$total += ! empty( $item['duration'] ) ? absint( $item['duration'] ) : 0;
		}

		if ( $total <= 0 ) {
			return esc_html__( 'نامشخص', 'tadris' );
		}

		$minutes = (int) ceil( $total / 60 );
		return sprintf( esc_html__( '%s دقیقه', 'tadris' ), number_format_i18n( $minutes ) );
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$query    = new WP_Query( $this->get_query_args( $settings ) );
		$items    = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$item = function_exists( 'webmz_youtube_playlist_get_item_data' )
					? webmz_youtube_playlist_get_item_data( get_the_ID() )
					: null;

				if ( null !== $item ) {
					$items[] = $item;
				}
			}
			wp_reset_postdata();
		}

		if ( empty( $items ) ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'ویدیویی برای نمایش وجود ندارد.', 'tadris' ) . '</div>';
			return;
		}

		$active      = $items[0];
		$total_count = count( $items );
		$progress    = $this->get_total_progress( $items );
		$total_time  = $this->get_total_duration_label( $items );
		$playlist_id = 'ytp-' . $this->get_id();
		$playlist_items_meta = array();

		foreach ( $items as $item ) {
			$playlist_items_meta[] = array(
				'id'        => (int) $item['id'],
				'duration'  => ! empty( $item['duration'] ) ? (int) $item['duration'] : 0,
				'progress'  => ! empty( $item['progress_current'] ) ? (float) $item['progress_current'] : 0,
				'video_url' => ! empty( $item['video_url'] ) ? (string) $item['video_url'] : '',
			);
		}

		$guard_links = ( ! is_user_logged_in() && ! empty( $settings['require_login_for_download'] ) && 'yes' === $settings['require_login_for_download'] );
		$author_id   = (int) get_post_field( 'post_author', $active['id'] );
		$author_name = get_the_author_meta( 'display_name', $author_id );
		$author_img  = get_avatar_url( $author_id, array( 'size' => 64 ) );
		$excerpt     = ! empty( $active['excerpt'] ) ? $active['excerpt'] : wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', $active['id'] ) ), 28 );
		$playlist_title = ! empty( $settings['playlist_title'] ) ? $settings['playlist_title'] : $active['title'];
		$item_active_icon   = ! empty( $settings['item_active_icon'] ) ? $settings['item_active_icon'] : array( 'value' => 'fas fa-play-circle', 'library' => 'fa-solid' );
		$item_done_icon     = ! empty( $settings['item_done_icon'] ) ? $settings['item_done_icon'] : array( 'value' => 'fas fa-check-circle', 'library' => 'fa-solid' );
		$download_icon      = ! empty( $settings['download_icon'] ) ? $settings['download_icon'] : array( 'value' => 'fas fa-download', 'library' => 'fa-solid' );
		$save_icon          = ! empty( $settings['save_icon'] ) ? $settings['save_icon'] : array( 'value' => 'far fa-bookmark', 'library' => 'fa-regular' );
		$saved_remove_icon  = ! empty( $settings['saved_remove_icon'] ) ? $settings['saved_remove_icon'] : array( 'value' => 'fas fa-bookmark', 'library' => 'fa-solid' );
		$share_icon         = ! empty( $settings['share_icon'] ) ? $settings['share_icon'] : array( 'value' => 'fas fa-share-alt', 'library' => 'fa-solid' );
		?>
		<div
			class="webmz-ytp"
			dir="rtl"
			data-webmz-ytp-playlist-id="<?php echo esc_attr( $playlist_id ); ?>"
			data-webmz-ytp-items="<?php echo esc_attr( wp_json_encode( $playlist_items_meta ) ); ?>"
			data-require-login-download="<?php echo $guard_links ? 'yes' : 'no'; ?>"
		>
			<div class="webmz-ytp__main" data-webmz-ytp-main>
				<?php $active_is_embed = \webmz_video_is_embed_url( $active['video_url'] ); ?>
				<div class="webmz-ytp__player-wrap webmz-plyr-widget<?php echo $active_is_embed ? ' is-embed' : ''; ?>">
					<?php // The <video> stays mounted for MP4 lessons and progress tracking; Aparat/YouTube lessons play in the embed slot. ?>
					<video
						class="webmz-ytp__video"
						playsinline
						controls
						preload="metadata"
						data-webmz-history-player="1"
						data-webmz-history-type="video"
						<?php echo $active_is_embed ? '' : 'data-webmz-history-post-id="' . esc_attr( $active['id'] ) . '"'; ?>
						<?php echo ! empty( $active['image_url'] ) ? 'poster="' . esc_url( $active['image_url'] ) . '"' : ''; ?>
					>
						<?php if ( ! $active_is_embed ) : ?>
							<source src="<?php echo esc_url( $active['video_url'] ); ?>" type="<?php echo esc_attr( \webmz_video_file_mime( $active['video_url'] ) ); ?>">
						<?php endif; ?>
					</video>
					<div class="webmz-ytp__embed" data-webmz-ytp-embed>
						<?php
						if ( $active_is_embed ) {
							\webmz_render_video_player( $active['video_url'], array( 'title' => $active['title'] ) );
						}
						?>
					</div>
				</div>

				<div class="webmz-ytp__content">
					<div class="webmz-ytp__title-row">
						<h3 class="webmz-ytp__title">
							<a href="<?php echo esc_url( $active['permalink'] ); ?>" data-webmz-ytp-title><?php echo esc_html( $active['title'] ); ?></a>
						</h3>
						<span class="webmz-ytp__lesson-badge" data-webmz-ytp-lesson-badge><?php echo esc_html( sprintf( __( 'درس %s', 'tadris' ), number_format_i18n( 1 ) ) ); ?></span>
					</div>
					<?php if ( ! empty( $active['level_label'] ) ) : ?>
						<div class="webmz-ytp__meta">
							<span class="webmz-ytp__level" data-webmz-ytp-level><?php echo esc_html( $active['level_label'] ); ?></span>
						</div>
					<?php else : ?>
						<span class="webmz-ytp__level" data-webmz-ytp-level hidden></span>
					<?php endif; ?>
					<p class="webmz-ytp__excerpt" data-webmz-ytp-excerpt><?php echo esc_html( $excerpt ); ?></p>
					<div class="webmz-ytp__actions">
						<a class="webmz-ytp__action-btn webmz-ytp__action-btn--download" href="<?php echo esc_url( $guard_links ? '#' : ( ! empty( $active['downloads'][0]['link'] ) ? $active['downloads'][0]['link'] : '#' ) ); ?>" <?php echo $guard_links ? 'data-webmz-download-guard="yes"' : ''; ?>>
							<span class="webmz-ytp__action-icon" aria-hidden="true"><?php Icons_Manager::render_icon( $download_icon, array( 'aria-hidden' => 'true' ) ); ?></span>
							<?php esc_html_e( 'دانلود ویدیو', 'tadris' ); ?>
						</a>
						<button
							type="button"
							class="webmz-ytp__action-btn webmz-ytp__favorite video-action-save<?php echo ! empty( $active['is_saved'] ) ? ' is-saved' : ''; ?>"
							data-webmz-favorite-post="<?php echo esc_attr( $active['id'] ); ?>"
							data-save-label="<?php esc_attr_e( 'ذخیره', 'tadris' ); ?>"
							data-remove-label="<?php esc_attr_e( 'حذف از ذخیره', 'tadris' ); ?>"
							aria-pressed="<?php echo ! empty( $active['is_saved'] ) ? 'true' : 'false'; ?>"
						>
							<span class="webmz-favorite-label"><?php echo ! empty( $active['is_saved'] ) ? esc_html__( 'حذف از ذخیره', 'tadris' ) : esc_html__( 'افزودن به علاقه‌مندی', 'tadris' ); ?></span>
							<span class="webmz-ytp__action-icon webmz-ytp__action-icon--save" aria-hidden="true">
								<?php Icons_Manager::render_icon( $save_icon, array( 'aria-hidden' => 'true' ) ); ?>
							</span>
							<span class="webmz-ytp__action-icon webmz-ytp__action-icon--remove" aria-hidden="true">
								<?php Icons_Manager::render_icon( $saved_remove_icon, array( 'aria-hidden' => 'true' ) ); ?>
							</span>
						</button>
						<button type="button" class="webmz-ytp__action-btn video-action-share" data-webmz-share-toggle="<?php echo esc_attr( $active['id'] ); ?>" data-share-url="<?php echo esc_url( $active['permalink'] ); ?>" data-share-title="<?php echo esc_attr( $active['title'] ); ?>">
							<span class="webmz-ytp__action-icon" aria-hidden="true"><?php Icons_Manager::render_icon( $share_icon, array( 'aria-hidden' => 'true' ) ); ?></span>
							<?php esc_html_e( 'اشتراک‌گذاری', 'tadris' ); ?>
						</button>
					</div>

					<div class="webmz-ytp__info-box">
						<div class="webmz-ytp__info-item">
							<span><?php esc_html_e( 'مدت زمان درس', 'tadris' ); ?></span>
							<strong data-webmz-ytp-duration><?php echo esc_html( ! empty( $active['duration_label'] ) ? $active['duration_label'] : '--:--' ); ?></strong>
						</div>
						<div class="webmz-ytp__info-item">
							<span><?php esc_html_e( 'سطح درس', 'tadris' ); ?></span>
							<strong data-webmz-ytp-level-text><?php echo esc_html( ! empty( $active['level_label'] ) ? $active['level_label'] : esc_html__( '—', 'tadris' ) ); ?></strong>
						</div>
						<div class="webmz-ytp__info-item webmz-ytp__info-item--teacher">
							<span><?php esc_html_e( 'مدرس', 'tadris' ); ?></span>
							<strong data-webmz-ytp-author><?php echo esc_html( $author_name ); ?></strong>
							<?php if ( ! empty( $author_img ) ) : ?>
								<img src="<?php echo esc_url( $author_img ); ?>" alt="<?php echo esc_attr( $author_name ); ?>" data-webmz-ytp-author-avatar>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<div class="webmz-ytp__playlist">
				<div class="webmz-ytp__playlist-head">
					<div class="webmz-ytp__playlist-title">
						<strong data-webmz-ytp-playlist-title><?php echo esc_html( $playlist_title ); ?></strong>
						<span><?php echo esc_html( number_format_i18n( $total_count ) ); ?> <?php esc_html_e( 'درس', 'tadris' ); ?> • <?php echo esc_html( $total_time ); ?></span>
					</div>
				</div>
				<div class="webmz-ytp__progress">
					<div class="webmz-ytp__progress-label">
						<span><?php esc_html_e( 'میزان پیشرفت', 'tadris' ); ?></span>
						<strong data-webmz-ytp-progress-text><?php echo esc_html( number_format_i18n( $progress ) ); ?>%</strong>
					</div>
					<div class="webmz-ytp__progress-bar"><span style="width: <?php echo esc_attr( $progress ); ?>%;" data-webmz-ytp-progress-bar></span></div>
				</div>
				<ul class="webmz-ytp__items" data-webmz-ytp-playlist>
					<?php foreach ( $items as $index => $item ) : ?>
						<?php $is_completed = $this->is_item_completed( $item ); ?>
						<li
							class="webmz-ytp__item<?php echo 0 === $index ? ' is-active' : ''; ?><?php echo $is_completed ? ' is-completed' : ''; ?>"
							data-post-id="<?php echo esc_attr( $item['id'] ); ?>"
							<?php echo ! empty( $item['duration'] ) ? 'data-duration="' . esc_attr( $item['duration'] ) . '"' : ''; ?>
						>
							<button type="button" class="webmz-ytp__item-trigger">
								<span class="webmz-ytp__item-state" aria-hidden="true">
									<span class="webmz-ytp__item-state-active">
										<?php Icons_Manager::render_icon( $item_active_icon, array( 'aria-hidden' => 'true' ) ); ?>
									</span>
									<span class="webmz-ytp__item-state-done">
										<?php Icons_Manager::render_icon( $item_done_icon, array( 'aria-hidden' => 'true' ) ); ?>
									</span>
								</span>
								<span class="webmz-ytp__item-thumb-wrap">
									<?php if ( ! empty( $item['thumb_url'] ) ) : ?>
										<img src="<?php echo esc_url( $item['thumb_url'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" class="webmz-ytp__item-thumb">
									<?php endif; ?>
								</span>
								<span class="webmz-ytp__item-index"><?php echo esc_html( number_format_i18n( $index + 1 ) ); ?></span>
								<span class="webmz-ytp__item-info">
									<strong><?php echo esc_html( $item['title'] ); ?></strong>
									<small><?php echo esc_html( ! empty( $item['duration_label'] ) ? $item['duration_label'] : '--:--' ); ?></small>
								</span>
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<?php
	}
}
