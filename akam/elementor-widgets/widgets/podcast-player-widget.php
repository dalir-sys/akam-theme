<?php
/**
 * Podcast Player Elementor widget.
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
 * RTL podcast player with playlist and AJAX track switching.
 */
class Podcast_Player_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-podcast-player';
	}

	public function get_title() {
		return esc_html__( 'پلیر پادکست‌ها', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-play';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'podcast', 'audio', 'plyr', 'پادکست', 'پلیر' );
	}

	public function get_script_depends() {
		return array( 'webmz-plyr', 'webmz-tadris-widgets', 'webmz-podcast-player' );
	}

	public function get_style_depends() {
		return array( 'webmz-plyr', 'webmz-plyr-widgets', 'webmz-podcast-player' );
	}

	/**
	 * Category options.
	 *
	 * @return array<int|string,string>
	 */
	private function get_category_options() {
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
	 * Tag options.
	 *
	 * @return array<int|string,string>
	 */
	private function get_tag_options() {
		$options = array(
			'' => esc_html__( 'همه برچسب‌ها', 'tadris' ),
		);

		$terms = get_terms(
			array(
				'taxonomy'   => 'post_tag',
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

	protected function register_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'تنظیمات محتوا', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
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
				'label'   => esc_html__( 'تعداد پادکست‌ها', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5,
				'min'     => 1,
				'max'     => 30,
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

		$this->webmz_register_title_tag_control( 'active_title_tag', esc_html__( 'تگ HTML عنوان پخش‌کننده', 'tadris' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_actions',
			array(
				'label' => esc_html__( 'آیکون‌های عملیات لیست', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'post_link_icon',
			array(
				'label'   => esc_html__( 'آیکون رفتن به مطلب', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'post_link_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون رفتن به مطلب', 'tadris' )
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

		$this->webmz_register_icon_color_mode_control(
			'save_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون ذخیره', 'tadris' )
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls(
			'style_box',
			esc_html__( 'کانتینر', 'tadris' ),
			'.webmz-pp',
			array(
				'default_background' => '#ffffff',
				'bordered'           => true,
			)
		);

		$this->start_controls_section(
			'section_typography',
			array(
				'label' => esc_html__( 'رنگ و تایپوگرافی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'accent_color',
			array(
				'label'     => esc_html__( 'رنگ تأکید (عنوان و پلیر)', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ff8b00',
				'selectors' => array(
					'{{WRAPPER}} .webmz-pp' => '--webmz-pp-accent: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'active_title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان فعال', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-pp__active-title',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'playlist_title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان لیست', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-pp__item-title',
			)
		);

		$this->add_control(
			'playlist_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان لیست', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4b5563',
				'selectors' => array(
					'{{WRAPPER}} .webmz-pp__item-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'duration_color',
			array(
				'label'     => esc_html__( 'رنگ مدت زمان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9ca3af',
				'selectors' => array(
					'{{WRAPPER}} .webmz-pp__item-duration' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build query args from widget settings.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<string,mixed>
	 */
	private function get_query_args( $settings ) {
		$args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => ! empty( $settings['posts_per_page'] ) ? absint( $settings['posts_per_page'] ) : 5,
			'orderby'             => ! empty( $settings['orderby'] ) ? sanitize_key( $settings['orderby'] ) : 'date',
			'order'               => ! empty( $settings['order'] ) ? sanitize_key( $settings['order'] ) : 'DESC',
			'ignore_sticky_posts' => true,
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
	 * Render active player thumb.
	 *
	 * @param array<string,mixed> $item Podcast item data.
	 * @return void
	 */
	private function render_thumb( $item ) {
		if ( ! empty( $item['image_html'] ) ) {
			echo $item['image_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		$initial = function_exists( 'mb_substr' ) ? mb_substr( (string) $item['title'], 0, 1 ) : substr( (string) $item['title'], 0, 1 );
		?>
		<div class="webmz-pp__thumb-fallback" aria-hidden="true"><?php echo esc_html( $initial ); ?></div>
		<?php
	}

	/**
	 * Render playlist item.
	 *
	 * @param array<string,mixed> $item      Podcast item data.
	 * @param array<string,mixed> $settings  Widget settings.
	 * @param bool                $is_active Whether item is active.
	 * @return void
	 */
	private function render_playlist_item( $item, $settings, $is_active = false ) {
		$duration_label = ! empty( $item['duration_label'] ) ? $item['duration_label'] : '--:--';
		$item_class     = 'webmz-pp__item' . ( $is_active ? ' is-active' : '' );
		$is_saved       = function_exists( '\webmz_tadris_user_has_favorite' )
			? \webmz_tadris_user_has_favorite( get_current_user_id(), $item['id'] )
			: false;
		$save_icon_class = $this->webmz_get_icon_color_mode_class( $settings, 'save_icon_color_mode' );
		$link_icon_class = $this->webmz_get_icon_color_mode_class( $settings, 'post_link_icon_color_mode' );
		$save_icon       = ! empty( $settings['save_icon'] ) ? $settings['save_icon'] : array( 'value' => 'far fa-bookmark', 'library' => 'fa-regular' );
		$remove_icon     = ! empty( $settings['saved_remove_icon'] ) ? $settings['saved_remove_icon'] : array( 'value' => 'fas fa-bookmark', 'library' => 'fa-solid' );
		$link_icon       = ! empty( $settings['post_link_icon'] ) ? $settings['post_link_icon'] : array( 'value' => 'fas fa-chevron-left', 'library' => 'fa-solid' );
		?>
		<li
			class="<?php echo esc_attr( $item_class ); ?>"
			data-post-id="<?php echo esc_attr( $item['id'] ); ?>"
			data-audio-url="<?php echo esc_url( $item['audio_url'] ); ?>"
			<?php echo ! empty( $item['duration'] ) ? 'data-duration="' . esc_attr( $item['duration'] ) . '"' : ''; ?>
		>
			<span class="webmz-pp__item-side">
				<span class="webmz-pp__item-duration"><?php echo esc_html( $duration_label ); ?></span>
				<span class="webmz-pp__item-spinner" aria-hidden="true"></span>
			</span>
			<button type="button" class="webmz-pp__item-trigger" aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>">
				<span class="webmz-pp__item-title"><?php echo esc_html( $item['title'] ); ?></span>
			</button>
			<div class="webmz-pp__item-actions">
				<?php if ( ! empty( $item['permalink'] ) ) : ?>
					<a
						class="webmz-pp__link-btn <?php echo esc_attr( $link_icon_class ); ?>"
						href="<?php echo esc_url( $item['permalink'] ); ?>"
						aria-label="<?php echo esc_attr( sprintf( esc_html__( 'مشاهده پادکست: %s', 'tadris' ), $item['title'] ) ); ?>"
					>
						<?php Icons_Manager::render_icon( $link_icon, array( 'aria-hidden' => 'true' ) ); ?>
					</a>
				<?php endif; ?>
				<div class="webmz-pp__item-save">
					<button
						type="button"
						class="webmz-pp__save-btn video-action-save<?php echo $is_saved ? ' is-saved' : ''; ?>"
						data-webmz-favorite-post="<?php echo esc_attr( $item['id'] ); ?>"
						data-save-label="<?php echo esc_attr__( 'ذخیره', 'tadris' ); ?>"
						data-remove-label="<?php echo esc_attr__( 'حذف از ذخیره', 'tadris' ); ?>"
						aria-pressed="<?php echo $is_saved ? 'true' : 'false'; ?>"
					>
						<span class="webmz-favorite-label screen-reader-text">
							<?php echo esc_html( $is_saved ? __( 'حذف از ذخیره', 'tadris' ) : __( 'ذخیره', 'tadris' ) ); ?>
						</span>
						<span class="webmz-favorite-icon webmz-favorite-icon--save <?php echo esc_attr( $save_icon_class ); ?>" aria-hidden="true">
							<?php Icons_Manager::render_icon( $save_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
						<span class="webmz-favorite-icon webmz-favorite-icon--remove <?php echo esc_attr( $save_icon_class ); ?>" aria-hidden="true">
							<?php Icons_Manager::render_icon( $remove_icon, array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					</button>
				</div>
			</div>
		</li>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$query    = new WP_Query( $this->get_query_args( $settings ) );
		$items    = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$item = function_exists( 'webmz_podcast_player_get_item_data' )
					? webmz_podcast_player_get_item_data( get_the_ID() )
					: null;

				if ( null !== $item ) {
					$items[] = $item;
				}
			}
			wp_reset_postdata();
		}

		if ( empty( $items ) ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'پادکستی برای نمایش وجود ندارد.', 'tadris' ) . '</div>';
			return;
		}

		$active    = $items[0];
		$title_tag = $this->webmz_get_title_tag( $settings, 'active_title_tag' );
		?>
		<div class="webmz-pp" dir="rtl">
			<div class="webmz-pp__active" data-webmz-pp-active>
				<div class="webmz-pp__active-head">
					<div class="webmz-pp__thumb" data-webmz-pp-thumb>
						<?php $this->render_thumb( $active ); ?>
					</div>
					<<?php echo esc_attr( $title_tag ); ?> class="webmz-pp__active-title" data-webmz-pp-title>
						<?php echo esc_html( $active['title'] ); ?>
					</<?php echo esc_attr( $title_tag ); ?>>
				</div>

				<div class="webmz-pp__player webmz-plyr-widget webmz-plyr-widget--audio" data-webmz-pp-player-wrap>
					<audio
						class="webmz-pp__audio"
						preload="metadata"
						data-webmz-history-player="1"
						data-webmz-history-type="podcast"
						data-webmz-history-post-id="<?php echo esc_attr( $active['id'] ); ?>"
					>
						<source src="<?php echo esc_url( $active['audio_url'] ); ?>" type="audio/mp3">
					</audio>
				</div>
			</div>

			<ul class="webmz-pp__playlist" data-webmz-pp-playlist>
				<?php foreach ( $items as $index => $item ) : ?>
					<?php $this->render_playlist_item( $item, $settings, 0 === $index ); ?>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}
