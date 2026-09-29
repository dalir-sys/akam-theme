<?php
/**
 * Standalone Plyr media widgets for WebMZ/Tadris.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/** Shared helpers for the standalone Plyr widgets. */
trait Tadris_Plyr_Widget_Helper_Trait {
	use Tadris_Widget_Controls_Trait;

	/**
	 * Register common media source controls.
	 *
	 * @param string $type Media type.
	 * @return void
	 */
	protected function register_media_source_controls( $type = 'video' ) {
		$is_video = 'video' === $type;

		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'منبع محتوا', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'current_post',
				'options' => array(
					'current_post' => $is_video ? esc_html__( 'گرفتن ویدیو از همین پست', 'tadris' ) : esc_html__( 'گرفتن پادکست از همین پست', 'tadris' ),
					'custom_url'   => $is_video ? esc_html__( 'وارد کردن لینک MP4', 'tadris' ) : esc_html__( 'وارد کردن لینک MP3', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'custom_url',
			array(
				'label'       => $is_video ? esc_html__( 'لینک MP4', 'tadris' ) : esc_html__( 'لینک MP3', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $is_video ? 'https://example.com/video.mp4' : 'https://example.com/audio.mp3',
				'label_block' => true,
				'condition'   => array(
					'source' => 'custom_url',
				),
			)
		);

		$this->add_control(
			'empty_message',
			array(
				'label'       => esc_html__( 'پیام نبود محتوا', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $is_video ? esc_html__( 'لینک ویدیویی برای این نوشته ثبت نشده است.', 'tadris' ) : esc_html__( 'لینک پادکستی برای این نوشته ثبت نشده است.', 'tadris' ),
				'label_block' => true,
			)
		);

		if ( $is_video ) {
			$this->add_control(
				'poster_source',
				array(
					'label'   => esc_html__( 'پوستر ویدیو', 'tadris' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'featured',
					'options' => array(
						'featured' => esc_html__( 'تصویر شاخص همین پست', 'tadris' ),
						'custom'   => esc_html__( 'تصویر دلخواه', 'tadris' ),
						'none'     => esc_html__( 'بدون پوستر', 'tadris' ),
					),
				)
			);

			$this->add_control(
				'custom_poster',
				array(
					'label'     => esc_html__( 'تصویر پوستر دلخواه', 'tadris' ),
					'type'      => Controls_Manager::MEDIA,
					'condition' => array(
						'poster_source' => 'custom',
					),
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Register shared style controls.
	 *
	 * @param string $type Media type.
	 * @return void
	 */
	protected function register_media_style_controls( $type = 'video' ) {
		$is_video = 'video' === $type;

		$this->start_controls_section(
			'wrapper_style_section',
			array(
				'label' => esc_html__( 'استایل پلیر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'wrapper_background',
			array(
				'label'     => esc_html__( 'رنگ پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $is_video ? '#0f172a' : '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-plyr-widget' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'      => $is_video ? 0 : 14,
					'right'    => $is_video ? 0 : 14,
					'bottom'   => $is_video ? 0 : 14,
					'left'     => $is_video ? 0 : 14,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-plyr-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => 20,
					'right'    => 20,
					'bottom'   => 20,
					'left'     => 20,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-plyr-widget, {{WRAPPER}} .webmz-plyr-widget video, {{WRAPPER}} .webmz-plyr-widget .plyr' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		if ( ! $is_video ) {
			$this->add_responsive_control(
				'player_min_height',
				array(
					'label'      => esc_html__( 'حداقل ارتفاع پلیر', 'tadris' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'vh', 'rem' ),
					'range'      => array(
						'px' => array( 'min' => 44, 'max' => 180 ),
						'vh' => array( 'min' => 10, 'max' => 90 ),
					),
					'default'    => array( 'size' => 54, 'unit' => 'px' ),
					'selectors'  => array(
						'{{WRAPPER}} .webmz-plyr-widget--audio .plyr' => 'min-height: {{SIZE}}{{UNIT}};',
					),
				)
			);
		}

		if ( $is_video ) {
			$this->add_control(
				'video_fit',
				array(
					'label'     => esc_html__( 'حالت پوشش ویدیو', 'tadris' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'cover',
					'options'   => array(
						'cover'   => esc_html__( 'Cover', 'tadris' ),
						'contain' => esc_html__( 'Contain', 'tadris' ),
					),
					'selectors' => array(
						'{{WRAPPER}} .webmz-plyr-widget video' => 'object-fit: {{VALUE}};',
					),
				)
			);
		}

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'wrapper_shadow',
				'selector' => '{{WRAPPER}} .webmz-plyr-widget',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Return current post ID in frontend/editor contexts.
	 *
	 * @return int
	 */
	protected function get_context_post_id() {
		$post_id = get_the_ID();

		if ( is_singular( 'post' ) ) {
			$post_id = get_queried_object_id();
		}

		return absint( $post_id );
	}

	/**
	 * Get saved start time for the current user and post.
	 *
	 * @param int $post_id Post ID.
	 * @return int
	 */
	protected function get_history_start_time( $post_id ) {
		if ( function_exists( '\webmz_view_history_get_user_post_progress' ) ) {
			$progress = \webmz_view_history_get_user_post_progress( $post_id );
			return isset( $progress['current'] ) ? max( 0, (int) floor( (float) $progress['current'] ) ) : 0;
		}

		return 0;
	}

	/**
	 * Whether the widget is being rendered inside Elementor editor/preview.
	 *
	 * @return bool
	 */
	protected function is_elementor_edit_context() {
		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance ) ) {
			$plugin = \Elementor\Plugin::$instance;

			if ( isset( $plugin->editor ) && method_exists( $plugin->editor, 'is_edit_mode' ) && $plugin->editor->is_edit_mode() ) {
				return true;
			}

			if ( isset( $plugin->preview ) && method_exists( $plugin->preview, 'is_preview_mode' ) && $plugin->preview->is_preview_mode() ) {
				return true;
			}
		}

		return false;
	}

}

/** Standalone video Plyr widget. */
class Tadris_Video_Plyr_Widget extends Widget_Base {
	use Tadris_Plyr_Widget_Helper_Trait;

	public function get_name() {
		return 'webmz-tadris-video-plyr';
	}

	public function get_title() {
		return esc_html__( 'ویدیو plyr آکام', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-video-camera';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'video', 'plyr', 'mp4', 'ویدیو', 'آکام' );
	}

	public function get_script_depends() {
		return array( 'webmz-plyr', 'webmz-tadris-widgets', 'webmz-view-history' );
	}

	public function get_style_depends() {
		return array( 'webmz-plyr', 'webmz-plyr-widgets' );
	}

	protected function register_controls() {
		$this->register_media_source_controls( 'video' );
		$this->register_media_style_controls( 'video' );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$post_id = $this->get_context_post_id();
		$url     = '';

		if ( 'custom_url' === $s['source'] ) {
			$url = ! empty( $s['custom_url'] ) ? esc_url_raw( $s['custom_url'] ) : '';
		} elseif ( $post_id ) {
			$url = esc_url_raw( get_post_meta( $post_id, '_webmz_video_url', true ) );
		}

		if ( empty( $url ) ) {
			if ( $this->is_elementor_edit_context() ) {
				echo '<div class="webmz-editor-placeholder">' . esc_html( $s['empty_message'] ) . '</div>';
			}
			return;
		}

		$poster = '';
		if ( 'featured' === ( $s['poster_source'] ?? 'featured' ) && $post_id && has_post_thumbnail( $post_id ) ) {
			$poster = get_the_post_thumbnail_url( $post_id, 'large' );
		} elseif ( 'custom' === ( $s['poster_source'] ?? '' ) && ! empty( $s['custom_poster']['url'] ) ) {
			$poster = esc_url_raw( $s['custom_poster']['url'] );
		}

		$start = $post_id ? $this->get_history_start_time( $post_id ) : 0;
		?>
		<div class="webmz-plyr-widget webmz-plyr-widget--video"<?php echo $post_id ? ' data-tadris-post-id="' . esc_attr( $post_id ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php
			\webmz_render_video_player(
				$url,
				array(
					'poster'      => $poster ? $poster : '',
					'title'       => $post_id ? get_the_title( $post_id ) : '',
					'video_class' => 'tadris-player-tag webmz-standalone-plyr webmz-standalone-plyr--video',
					'video_attrs' => array(
						'data-webmz-history-player'  => '1',
						'data-webmz-history-type'    => 'video',
						'data-webmz-history-post-id' => $post_id ? $post_id : '',
						'data-webmz-history-start'   => $start > 0 ? $start : '',
					),
				)
			);
			?>
		</div>
		<?php
	}
}

/** Standalone podcast Plyr widget. */
class Tadris_Podcast_Plyr_Widget extends Widget_Base {
	use Tadris_Plyr_Widget_Helper_Trait;

	public function get_name() {
		return 'webmz-tadris-podcast-plyr';
	}

	public function get_title() {
		return esc_html__( 'پادکست plyr آکام', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-headphones';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'podcast', 'audio', 'plyr', 'mp3', 'پادکست', 'آکام' );
	}

	public function get_script_depends() {
		return array( 'webmz-plyr', 'webmz-tadris-widgets', 'webmz-view-history' );
	}

	public function get_style_depends() {
		return array( 'webmz-plyr', 'webmz-plyr-widgets' );
	}

	protected function register_controls() {
		$this->register_media_source_controls( 'podcast' );
		$this->register_media_style_controls( 'podcast' );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$post_id = $this->get_context_post_id();
		$url     = '';

		if ( 'custom_url' === $s['source'] ) {
			$url = ! empty( $s['custom_url'] ) ? esc_url_raw( $s['custom_url'] ) : '';
		} elseif ( $post_id ) {
			if ( function_exists( '\webmz_tadris_get_podcast_audio_url' ) ) {
				$url = \webmz_tadris_get_podcast_audio_url( $post_id );
			} else {
				$meta_key = defined( 'TADRIS_PODCAST_AUDIO_META_KEY' ) ? TADRIS_PODCAST_AUDIO_META_KEY : '_tadris_podcast_audio_url';
				$url      = esc_url_raw( get_post_meta( $post_id, $meta_key, true ) );
			}
		}

		if ( empty( $url ) ) {
			if ( $this->is_elementor_edit_context() ) {
				echo '<div class="webmz-editor-placeholder">' . esc_html( $s['empty_message'] ) . '</div>';
			}
			return;
		}

		$start = $post_id ? $this->get_history_start_time( $post_id ) : 0;
		?>
		<div class="webmz-plyr-widget webmz-plyr-widget--audio"<?php echo $post_id ? ' data-tadris-post-id="' . esc_attr( $post_id ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<audio class="tadris-player-tag tadris-sound-tag webmz-standalone-plyr webmz-standalone-plyr--audio" controls preload="metadata" data-webmz-history-player="1" data-webmz-history-type="podcast"<?php echo $post_id ? ' data-webmz-history-post-id="' . esc_attr( $post_id ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo $start > 0 ? ' data-webmz-history-start="' . esc_attr( $start ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<source src="<?php echo esc_url( $url ); ?>" type="audio/mp3">
			</audio>
		</div>
		<?php
	}
}
