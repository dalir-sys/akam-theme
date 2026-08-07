<?php
/**
 * Instagram-style stories: custom post types, meta boxes, helpers, shortcode.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WEBMZ_STORY_POST_TYPE' ) ) {
	define( 'WEBMZ_STORY_POST_TYPE', 'webmz_story' );
}

if ( ! defined( 'WEBMZ_STORY_BOX_POST_TYPE' ) ) {
	define( 'WEBMZ_STORY_BOX_POST_TYPE', 'webmz_story_box' );
}

/**
 * Global story defaults (used when box setting is «جهانی»).
 *
 * @return array<string,mixed>
 */
function webmz_get_stories_global_settings() {
	$defaults = array(
		'render_type'     => 'server',
		'style'           => 'instagram',
		'reporting'       => 'no',
		'fullscreen'      => 'no',
		'mute_videos'     => 'no',
		'swipe_up_button' => 'yes',
		'custom_style'    => 'yes',
		'custom_timer'    => 'yes',
		'full_size_media' => 'yes',
		'default_duration'=> 3,
	);

	$saved = get_option( 'webmz_stories_settings', array() );

	return wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
}

/**
 * Icon setting keys for the story viewer.
 *
 * @return array<int,string>
 */
function webmz_get_stories_icon_keys() {
	return array(
		'play',
		'pause',
		'mute',
		'unmute',
		'nav_prev',
		'nav_next',
		'close',
	);
}

/**
 * Default labels for story viewer icons.
 *
 * @return array<string,string>
 */
function webmz_get_stories_icon_labels() {
	return array(
		'play'     => esc_html__( 'آیکون پخش', 'tadris' ),
		'pause'    => esc_html__( 'آیکون توقف', 'tadris' ),
		'mute'     => esc_html__( 'آیکون بی‌صدا', 'tadris' ),
		'unmute'   => esc_html__( 'آیکون صدا', 'tadris' ),
		'nav_prev' => esc_html__( 'آیکون قبلی', 'tadris' ),
		'nav_next' => esc_html__( 'آیکون بعدی', 'tadris' ),
		'close'    => esc_html__( 'آیکون بستن', 'tadris' ),
	);
}

/**
 * Allowed HTML tags for story icon SVG markup.
 *
 * @return array<string,array<string,bool>>
 */
function webmz_get_story_icon_svg_allowed_html() {
	return array(
		'svg'      => array(
			'class'           => true,
			'xmlns'           => true,
			'viewbox'         => true,
			'viewBox'         => true,
			'width'           => true,
			'height'          => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'aria-hidden'     => true,
			'role'            => true,
			'focusable'       => true,
		),
		'g'        => array(
			'fill'      => true,
			'stroke'    => true,
			'transform' => true,
		),
		'path'     => array(
			'd'               => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'fill-rule'       => true,
			'clip-rule'       => true,
		),
		'circle'   => array(
			'cx'     => true,
			'cy'     => true,
			'r'      => true,
			'fill'   => true,
			'stroke' => true,
		),
		'rect'     => array(
			'x'      => true,
			'y'      => true,
			'width'  => true,
			'height' => true,
			'rx'     => true,
			'fill'   => true,
			'stroke' => true,
		),
		'line'     => array(
			'x1'     => true,
			'y1'     => true,
			'x2'     => true,
			'y2'     => true,
			'stroke' => true,
		),
		'polygon'  => array(
			'points' => true,
			'fill'   => true,
			'stroke' => true,
		),
		'polyline' => array(
			'points' => true,
			'fill'   => true,
			'stroke' => true,
		),
		'defs'     => array(),
		'use'      => array(
			'href'      => true,
			'xlink:href'=> true,
		),
	);
}

/**
 * Sanitize inline SVG icon markup.
 *
 * @param string $svg Raw SVG string.
 * @return string
 */
function webmz_sanitize_story_icon_svg( $svg ) {
	$svg = trim( (string) $svg );

	if ( '' === $svg || false === stripos( $svg, '<svg' ) ) {
		return '';
	}

	return wp_kses( $svg, webmz_get_story_icon_svg_allowed_html() );
}

/**
 * Get story viewer icon SVG markup for frontend.
 *
 * @return array<string,string>
 */
function webmz_get_stories_icon_settings() {
	$saved = get_option( 'webmz_stories_icon_settings', array() );
	$icons = array();

	foreach ( webmz_get_stories_icon_keys() as $key ) {
		$icons[ $key ] = '';

		if ( ! is_array( $saved ) || ! isset( $saved[ $key ] ) ) {
			continue;
		}

		$row = $saved[ $key ];

		if ( is_array( $row ) && ! empty( $row['svg'] ) ) {
			$icons[ $key ] = webmz_sanitize_story_icon_svg( (string) $row['svg'] );
		}
	}

	return $icons;
}

/**
 * Register icons settings submenu under stories.
 *
 * @return void
 */
function webmz_stories_register_icons_menu() {
	add_submenu_page(
		'edit.php?post_type=' . WEBMZ_STORY_POST_TYPE,
		esc_html__( 'آیکون تمام بخش‌ها', 'tadris' ),
		esc_html__( 'آیکون تمام بخش‌ها', 'tadris' ),
		'manage_options',
		'webmz-story-icons',
		'webmz_stories_render_icons_settings_page'
	);
}
add_action( 'admin_menu', 'webmz_stories_register_icons_menu' );

/**
 * Render one SVG icon field.
 *
 * @param string $key   Icon key.
 * @param string $label Field label.
 * @param array  $value Stored value.
 * @return void
 */
function webmz_stories_render_icon_field( $key, $label, $value = array() ) {
	$svg = '';

	if ( is_array( $value ) && ! empty( $value['svg'] ) ) {
		$svg = (string) $value['svg'];
	}

	$safe_svg = webmz_sanitize_story_icon_svg( $svg );
	?>
	<div class="webmz-story-icon-field" data-webmz-story-icon-field>
		<label class="webmz-story-icon-field__label" for="webmz_story_icon_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
		<div class="webmz-story-icon-field__preview" data-webmz-story-icon-preview>
			<?php if ( $safe_svg ) : ?>
				<?php echo $safe_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php else : ?>
				<span class="description"><?php esc_html_e( 'پیش‌نمایش SVG', 'tadris' ); ?></span>
			<?php endif; ?>
		</div>
		<textarea
			id="webmz_story_icon_<?php echo esc_attr( $key ); ?>"
			class="widefat code webmz-story-icon-field__input"
			name="webmz_stories_icons[<?php echo esc_attr( $key ); ?>][svg]"
			rows="6"
			data-webmz-story-icon-input
			placeholder="<?php esc_attr_e( '<svg ...>...</svg>', 'tadris' ); ?>"
		><?php echo esc_textarea( $svg ); ?></textarea>
		<p class="description"><?php esc_html_e( 'کد SVG را مستقیماً وارد کنید. در فرانت به‌صورت inline SVG نمایش داده می‌شود.', 'tadris' ); ?></p>
	</div>
	<?php
}

/**
 * Render icons settings page.
 *
 * @return void
 */
function webmz_stories_render_icons_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$saved = get_option( 'webmz_stories_icon_settings', array() );
	$saved = is_array( $saved ) ? $saved : array();
	$labels = webmz_get_stories_icon_labels();
	?>
	<div class="wrap webmz-story-icons-page">
		<h1><?php esc_html_e( 'آیکون تمام بخش‌ها', 'tadris' ); ?></h1>
		<?php settings_errors( 'webmz_stories_icons' ); ?>
		<p class="description"><?php esc_html_e( 'برای هر بخش، کد SVG مربوطه را وارد کنید. در صورت خالی بودن، آیکون پیش‌فرض نمایش داده می‌شود.', 'tadris' ); ?></p>
		<form method="post" action="">
			<?php wp_nonce_field( 'webmz_save_stories_icons', 'webmz_stories_icons_nonce' ); ?>
			<div class="webmz-story-icons-grid">
				<?php foreach ( webmz_get_stories_icon_keys() as $key ) : ?>
					<?php
					$value = isset( $saved[ $key ] ) && is_array( $saved[ $key ] ) ? $saved[ $key ] : array();
					webmz_stories_render_icon_field( $key, $labels[ $key ], $value );
					?>
				<?php endforeach; ?>
			</div>
			<?php submit_button( esc_html__( 'ذخیره آیکون‌ها', 'tadris' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Save icons settings page.
 *
 * @return void
 */
function webmz_stories_save_icons_settings_page() {
	if ( ! isset( $_GET['page'] ) || 'webmz-story-icons' !== $_GET['page'] ) {
		return;
	}

	if ( ! isset( $_POST['webmz_stories_icons_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_stories_icons_nonce'] ) ), 'webmz_save_stories_icons' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$raw   = isset( $_POST['webmz_stories_icons'] ) ? wp_unslash( $_POST['webmz_stories_icons'] ) : array();
	$icons = array();

	if ( is_array( $raw ) ) {
		foreach ( webmz_get_stories_icon_keys() as $key ) {
			$row = isset( $raw[ $key ] ) && is_array( $raw[ $key ] ) ? $raw[ $key ] : array();
			$icons[ $key ] = array(
				'svg' => webmz_sanitize_story_icon_svg( isset( $row['svg'] ) ? (string) $row['svg'] : '' ),
			);
		}
	}

	update_option( 'webmz_stories_icon_settings', $icons );
	add_settings_error( 'webmz_stories_icons', 'saved', esc_html__( 'آیکون‌ها ذخیره شد.', 'tadris' ), 'updated' );
}
add_action( 'admin_init', 'webmz_stories_save_icons_settings_page' );

/**
 * Register stories post type.
 *
 * @return void
 */
function webmz_story_register_post_type() {
	$labels = array(
		'name'               => esc_html__( 'استوری‌ها', 'tadris' ),
		'singular_name'      => esc_html__( 'استوری', 'tadris' ),
		'add_new'            => esc_html__( 'اضافه کردن استوری جدید', 'tadris' ),
		'add_new_item'       => esc_html__( 'اضافه کردن استوری جدید', 'tadris' ),
		'edit_item'          => esc_html__( 'ویرایش استوری', 'tadris' ),
		'new_item'           => esc_html__( 'استوری جدید', 'tadris' ),
		'view_item'          => esc_html__( 'مشاهده استوری', 'tadris' ),
		'search_items'       => esc_html__( 'جستجوی استوری', 'tadris' ),
		'not_found'          => esc_html__( 'استوری‌ای پیدا نشد.', 'tadris' ),
		'not_found_in_trash' => esc_html__( 'استوری‌ای در زباله‌دان نیست.', 'tadris' ),
		'menu_name'          => esc_html__( 'استوری‌ها', 'tadris' ),
		'all_items'          => esc_html__( 'تمام استوری‌ها', 'tadris' ),
	);

	register_post_type(
		WEBMZ_STORY_POST_TYPE,
		array(
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'menu_icon'           => 'dashicons-instagram',
			'menu_position'       => 26,
			'supports'            => array( 'title', 'thumbnail' ),
			'has_archive'         => false,
			'capability_type'     => 'post',
		)
	);
}
add_action( 'init', 'webmz_story_register_post_type' );

/**
 * Register story box post type.
 *
 * @return void
 */
function webmz_story_box_register_post_type() {
	$labels = array(
		'name'               => esc_html__( 'باکس‌های استوری', 'tadris' ),
		'singular_name'      => esc_html__( 'باکس استوری', 'tadris' ),
		'add_new'            => esc_html__( 'افزودن باکس استوری', 'tadris' ),
		'add_new_item'       => esc_html__( 'افزودن باکس استوری جدید', 'tadris' ),
		'edit_item'          => esc_html__( 'ویرایش باکس استوری', 'tadris' ),
		'new_item'           => esc_html__( 'باکس استوری جدید', 'tadris' ),
		'view_item'          => esc_html__( 'مشاهده باکس استوری', 'tadris' ),
		'search_items'       => esc_html__( 'جستجوی باکس استوری', 'tadris' ),
		'not_found'          => esc_html__( 'باکس استوری پیدا نشد.', 'tadris' ),
		'not_found_in_trash' => esc_html__( 'باکس استوری در زباله‌دان نیست.', 'tadris' ),
		'menu_name'          => esc_html__( 'باکس‌های استوری', 'tadris' ),
		'all_items'          => esc_html__( 'باکس‌های استوری', 'tadris' ),
	);

	register_post_type(
		WEBMZ_STORY_BOX_POST_TYPE,
		array(
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=' . WEBMZ_STORY_POST_TYPE,
			'show_in_rest'        => false,
			'supports'            => array( 'title' ),
			'has_archive'         => false,
			'capability_type'     => 'post',
		)
	);
}
add_action( 'init', 'webmz_story_box_register_post_type' );

/**
 * Flush rewrite rules after stories post types are registered.
 *
 * @return void
 */
function webmz_stories_flush_rewrite_rules() {
	webmz_story_register_post_type();
	webmz_story_box_register_post_type();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'webmz_stories_flush_rewrite_rules' );

/**
 * Register optimized image size for story ring thumbnails.
 *
 * @return void
 */
function webmz_register_story_image_sizes() {
	add_image_size( 'webmz_story_ring', 96, 96, true );
}
add_action( 'after_setup_theme', 'webmz_register_story_image_sizes' );

/**
 * Get a small thumbnail URL for story ring display.
 *
 * @param int $post_id               Post ID.
 * @param int $fallback_attachment_id Optional attachment ID fallback.
 * @return string
 */
function webmz_get_story_ring_thumb_url( $post_id, $fallback_attachment_id = 0 ) {
	$post_id = absint( $post_id );

	if ( $post_id ) {
		$url = get_the_post_thumbnail_url( $post_id, 'webmz_story_ring' );
		if ( $url ) {
			return esc_url_raw( $url );
		}
	}

	$fallback_attachment_id = absint( $fallback_attachment_id );
	if ( $fallback_attachment_id ) {
		$url = wp_get_attachment_image_url( $fallback_attachment_id, 'webmz_story_ring' );
		if ( $url ) {
			return esc_url_raw( $url );
		}
	}

	return '';
}

/**
 * Whether story boxes should lazy-load via AJAX on the frontend.
 *
 * @return bool
 */
function webmz_stories_should_lazy_load() {
	if ( is_admin() ) {
		return false;
	}

	if ( class_exists( '\Elementor\Plugin' ) ) {
		$elementor = \Elementor\Plugin::$instance;

		if ( $elementor->editor && $elementor->editor->is_edit_mode() ) {
			return false;
		}

		if ( $elementor->preview && $elementor->preview->is_preview_mode() ) {
			return false;
		}
	}

	return true;
}

/**
 * Count story groups for skeleton placeholders.
 *
 * @param int $box_id Story box post ID.
 * @return int
 */
function webmz_get_story_box_skeleton_count( $box_id ) {
	$count = count( webmz_get_story_box_story_ids( absint( $box_id ) ) );

	if ( $count < 1 ) {
		return 4;
	}

	return $count;
}

/**
 * Meta key for story slide items.
 *
 * @return string
 */
function webmz_get_story_items_meta_key() {
	return '_webmz_story_items';
}

/**
 * Get sanitized story slide items.
 *
 * @param int $post_id Story post ID.
 * @return array<int,array<string,mixed>>
 */
function webmz_get_story_items( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || WEBMZ_STORY_POST_TYPE !== get_post_type( $post_id ) ) {
		return array();
	}

	$raw = get_post_meta( $post_id, webmz_get_story_items_meta_key(), true );

	if ( ! is_array( $raw ) ) {
		return array();
	}

	$defaults = webmz_get_stories_global_settings();
	$items    = array();

	foreach ( $raw as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		if ( ! empty( $row['disabled'] ) ) {
			continue;
		}

		$media_id  = isset( $row['media_id'] ) ? absint( $row['media_id'] ) : 0;
		$media_url = isset( $row['media_url'] ) ? trim( (string) $row['media_url'] ) : '';

		if ( $media_id && '' === $media_url ) {
			$media_url = (string) wp_get_attachment_url( $media_id );
		}

		if ( '' === $media_url ) {
			continue;
		}

		$media_type = isset( $row['media_type'] ) ? sanitize_key( (string) $row['media_type'] ) : 'image';
		if ( ! in_array( $media_type, array( 'image', 'video' ), true ) ) {
			$mime = $media_id ? get_post_mime_type( $media_id ) : '';
			$media_type = ( $mime && 0 === strpos( $mime, 'video/' ) ) ? 'video' : 'image';
		}

		$duration = isset( $row['duration'] ) ? absint( $row['duration'] ) : (int) $defaults['default_duration'];
		if ( $duration < 1 ) {
			$duration = (int) $defaults['default_duration'];
		}

		$sync_video = ! empty( $row['sync_video_duration'] ) && 'video' === $media_type;

		$items[] = array(
			'button_title'        => isset( $row['button_title'] ) ? sanitize_text_field( (string) $row['button_title'] ) : '',
			'button_link'         => isset( $row['button_link'] ) ? esc_url_raw( (string) $row['button_link'] ) : '',
			'button_new_tab'      => ! empty( $row['button_new_tab'] ),
			'media_id'            => $media_id,
			'media_url'           => esc_url_raw( $media_url ),
			'media_type'          => $media_type,
			'duration'            => $duration,
			'sync_video_duration' => $sync_video,
		);
	}

	return $items;
}

/**
 * Resolve a story box setting (global|yes|no).
 *
 * @param int    $box_id Story box post ID.
 * @param string $key    Setting key.
 * @return string
 */
function webmz_story_box_get_setting( $box_id, $key ) {
	$meta_key = '_webmz_story_box_' . $key;
	$value    = get_post_meta( absint( $box_id ), $meta_key, true );

	if ( '' === $value || 'global' === $value ) {
		$global = webmz_get_stories_global_settings();
		return isset( $global[ $key ] ) ? (string) $global[ $key ] : 'yes';
	}

	return (string) $value;
}

/**
 * Get ordered story IDs assigned to a story box.
 *
 * @param int $box_id Story box post ID.
 * @return array<int,int>
 */
function webmz_get_story_box_story_ids( $box_id ) {
	$box_id = absint( $box_id );

	if ( ! $box_id || WEBMZ_STORY_BOX_POST_TYPE !== get_post_type( $box_id ) ) {
		return array();
	}

	$source = get_post_meta( $box_id, '_webmz_story_box_stories_source', true );
	if ( '' === $source ) {
		$source = 'stories';
	}

	$ids = array();

	if ( 'stories' === $source ) {
		$raw = get_post_meta( $box_id, '_webmz_story_box_story_ids', true );
		if ( is_array( $raw ) ) {
			foreach ( $raw as $id ) {
				$id = absint( $id );
				if ( $id && WEBMZ_STORY_POST_TYPE === get_post_type( $id ) && 'publish' === get_post_status( $id ) ) {
					$ids[] = $id;
				}
			}
		}
	} elseif ( 'posts' === $source ) {
		$raw = get_post_meta( $box_id, '_webmz_story_box_post_ids', true );
		if ( is_array( $raw ) ) {
			foreach ( $raw as $id ) {
				$id = absint( $id );
				if ( $id && 'post' === get_post_type( $id ) && 'publish' === get_post_status( $id ) ) {
					$ids[] = $id;
				}
			}
		}
	} elseif ( 'categories' === $source ) {
		$term_ids = get_post_meta( $box_id, '_webmz_story_box_category_ids', true );
		if ( is_array( $term_ids ) && ! empty( $term_ids ) ) {
			$query = new WP_Query(
				array(
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => 20,
					'fields'         => 'ids',
					'category__in'   => array_map( 'absint', $term_ids ),
				)
			);
			$ids = array_map( 'absint', $query->posts );
		}
	} elseif ( 'ids' === $source ) {
		$raw_ids = get_post_meta( $box_id, '_webmz_story_box_manual_ids', true );
		if ( is_string( $raw_ids ) ) {
			$parts = preg_split( '/[\s,]+/', $raw_ids );
			foreach ( $parts as $part ) {
				$id = absint( $part );
				if ( $id ) {
					$ids[] = $id;
				}
			}
		}
	}

	return array_values( array_unique( $ids ) );
}

/**
 * Build one story group payload for the frontend track.
 *
 * @param int $story_id Story post ID.
 * @return array<string,mixed>|null
 */
function webmz_build_story_group_data( $story_id ) {
	$story_id = absint( $story_id );

	if ( ! $story_id || WEBMZ_STORY_POST_TYPE !== get_post_type( $story_id ) || 'publish' !== get_post_status( $story_id ) ) {
		return null;
	}

	$slides              = webmz_get_story_items( $story_id );
	$featured_id         = get_post_thumbnail_id( $story_id );
	$fallback_media_id   = ! empty( $slides[0]['media_id'] ) ? absint( $slides[0]['media_id'] ) : 0;
	$thumb_attachment_id = $fallback_media_id ? $fallback_media_id : (int) $featured_id;
	$thumb               = webmz_get_story_ring_thumb_url( $story_id, $thumb_attachment_id );

	if ( empty( $slides ) && $featured_id ) {
		$featured_url = (string) wp_get_attachment_url( $featured_id );

		if ( $featured_url ) {
			$slides = array(
				array(
					'button_title'        => '',
					'button_link'         => '',
					'button_new_tab'      => false,
					'media_id'            => (int) $featured_id,
					'media_url'           => esc_url_raw( $featured_url ),
					'media_type'          => 'image',
					'duration'            => (int) webmz_get_stories_global_settings()['default_duration'],
					'sync_video_duration' => false,
				),
			);
		}
	}

	if ( ! $thumb && ! empty( $slides[0]['media_url'] ) ) {
		$thumb = esc_url_raw( (string) $slides[0]['media_url'] );
	}

	return array(
		'id'     => $story_id,
		'title'  => get_the_title( $story_id ),
		'thumb'  => $thumb ? esc_url_raw( (string) $thumb ) : '',
		'slides' => $slides,
	);
}

/**
 * Build frontend payload for a story box.
 *
 * @param int $box_id Story box post ID.
 * @return array<string,mixed>
 */
function webmz_get_story_box_frontend_data( $box_id ) {
	$box_id = absint( $box_id );

	if ( ! $box_id || 'publish' !== get_post_status( $box_id ) ) {
		return array(
			'groups'   => array(),
			'settings' => webmz_get_stories_global_settings(),
		);
	}

	$source  = get_post_meta( $box_id, '_webmz_story_box_stories_source', true );
	$source  = $source ? $source : 'stories';
	$groups  = array();
	$ids     = webmz_get_story_box_story_ids( $box_id );

	foreach ( $ids as $id ) {
		if ( 'stories' === $source ) {
			$group = webmz_build_story_group_data( $id );

			if ( $group ) {
				$groups[] = $group;
			}
		} else {
			$thumb = webmz_get_story_ring_thumb_url( $id );
			if ( ! $thumb ) {
				$thumb_id = get_post_thumbnail_id( $id );
				$thumb    = $thumb_id ? esc_url_raw( (string) wp_get_attachment_image_url( $thumb_id, 'medium' ) ) : '';
			}
			$groups[] = array(
				'id'     => $id,
				'title'  => get_the_title( $id ),
				'thumb'  => $thumb ? esc_url_raw( $thumb ) : '',
				'slides' => array(
					array(
						'button_title'   => esc_html__( 'مشاهده', 'tadris' ),
						'button_link'    => esc_url_raw( get_permalink( $id ) ),
						'button_new_tab' => false,
						'media_id'       => 0,
						'media_url'      => $thumb ? esc_url_raw( $thumb ) : '',
						'media_type'     => 'image',
						'duration'       => (int) webmz_get_stories_global_settings()['default_duration'],
					),
				),
			);
		}
	}

	return array(
		'groups'   => $groups,
		'settings' => array(
			'mute_videos'     => webmz_story_box_get_setting( $box_id, 'mute_videos' ),
			'fullscreen'      => webmz_story_box_get_setting( $box_id, 'fullscreen' ),
			'swipe_up_button' => webmz_story_box_get_setting( $box_id, 'swipe_up_button' ),
			'full_size_media' => webmz_story_box_get_setting( $box_id, 'full_size_media' ),
			'style'           => webmz_get_stories_global_settings()['style'],
			'icons'           => webmz_get_stories_icon_settings(),
		),
	);
}

/**
 * Render story ring track buttons.
 *
 * @param array<string,mixed> $data Story box payload.
 * @return string
 */
function webmz_render_story_box_track_html( $data ) {
	if ( empty( $data['groups'] ) || ! is_array( $data['groups'] ) ) {
		return '';
	}

	ob_start();

	foreach ( $data['groups'] as $index => $group ) {
		webmz_render_story_box_track_item( (int) $index, $group );
	}

	return (string) ob_get_clean();
}

/**
 * Render one story track button.
 *
 * @param int                 $index Group index.
 * @param array<string,mixed> $group Group data.
 * @return void
 */
function webmz_render_story_box_track_item( $index, $group ) {
	$title      = isset( $group['title'] ) ? (string) $group['title'] : '';
	$thumb      = isset( $group['thumb'] ) ? (string) $group['thumb'] : '';
	$group_id   = isset( $group['id'] ) ? absint( $group['id'] ) : 0;
	$has_slides = ! empty( $group['slides'] ) && is_array( $group['slides'] );
	?>
	<button type="button" class="webmz-stories__item swiper-slide" role="listitem" data-story-group="<?php echo esc_attr( (string) $index ); ?>" data-story-group-id="<?php echo esc_attr( (string) $group_id ); ?>" aria-label="<?php echo esc_attr( $title ); ?>"<?php echo $has_slides ? '' : ' disabled aria-disabled="true"'; ?>>
		<span class="webmz-stories__ring">
			<?php if ( $thumb ) : ?>
				<img src="<?php echo esc_url( $thumb ); ?>" alt="" width="96" height="96" loading="lazy" decoding="async" draggable="false">
			<?php else : ?>
				<span class="webmz-stories__placeholder" aria-hidden="true"></span>
			<?php endif; ?>
		</span>
		<span class="webmz-stories__label"><?php echo esc_html( $title ); ?></span>
	</button>
	<?php
}

/**
 * Render skeleton placeholders for lazy-loaded story boxes.
 *
 * @param int $count Number of skeleton items.
 * @return string
 */
function webmz_render_story_box_skeleton_html( $count ) {
	$count = max( 1, absint( $count ) );

	ob_start();

	for ( $i = 0; $i < $count; $i++ ) {
		?>
		<div class="webmz-stories__item webmz-stories__item--skeleton swiper-slide" aria-hidden="true">
			<span class="webmz-stories__ring">
				<span class="webmz-stories__skeleton-circle"></span>
			</span>
			<span class="webmz-stories__label">
				<span class="webmz-stories__skeleton-line"></span>
			</span>
		</div>
		<?php
	}

	return (string) ob_get_clean();
}

/**
 * Render lazy story box shell with skeleton UI.
 *
 * @param int   $box_id Story box post ID.
 * @param array $args   Wrapper args.
 * @return string
 */
function webmz_render_story_box_lazy_shell( $box_id, $args = array() ) {
	$box_id = absint( $box_id );

	if ( ! $box_id || 'publish' !== get_post_status( $box_id ) ) {
		return '';
	}

	if ( empty( webmz_get_story_box_story_ids( $box_id ) ) ) {
		return '';
	}

	$args = wp_parse_args(
		$args,
		array(
			'class' => '',
		)
	);

	$instance_id = 'webmz-stories-' . $box_id . '-' . wp_unique_id();
	$classes     = trim( 'webmz-stories webmz-stories--loading ' . $args['class'] );
	$skeletons   = webmz_get_story_box_skeleton_count( $box_id );

	ob_start();
	?>
	<div class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $instance_id ); ?>" data-webmz-stories data-box-id="<?php echo esc_attr( (string) $box_id ); ?>">
		<div class="webmz-stories__swiper swiper">
			<div class="webmz-stories__track swiper-wrapper" role="list">
				<?php echo webmz_render_story_box_skeleton_html( $skeletons ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render story box markup.
 *
 * @param int   $box_id  Story box post ID.
 * @param array $args    Optional wrapper args.
 * @return string
 */
function webmz_render_story_box( $box_id, $args = array() ) {
	$box_id = absint( $box_id );

	$args = wp_parse_args(
		$args,
		array(
			'class' => '',
			'lazy'  => webmz_stories_should_lazy_load(),
		)
	);

	if ( $args['lazy'] ) {
		return webmz_render_story_box_lazy_shell( $box_id, $args );
	}

	$data = webmz_get_story_box_frontend_data( $box_id );

	if ( empty( $data['groups'] ) ) {
		return '';
	}

	$instance_id = 'webmz-stories-' . $box_id . '-' . wp_unique_id();
	$classes     = trim( 'webmz-stories ' . $args['class'] );

	ob_start();
	?>
	<div class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $instance_id ); ?>" data-webmz-stories data-box-id="<?php echo esc_attr( (string) $box_id ); ?>">
		<div class="webmz-stories__swiper swiper">
			<div class="webmz-stories__track swiper-wrapper" role="list">
				<?php echo webmz_render_story_box_track_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<script type="application/json" class="webmz-stories__data"><?php echo wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Story box shortcode.
 *
 * @param array<string,string> $atts Shortcode attributes.
 * @return string
 */
function webmz_story_box_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'id' => 0,
		),
		$atts,
		'webmz_story'
	);

	$box_id = absint( $atts['id'] );

	if ( ! $box_id ) {
		return '';
	}

	wp_enqueue_style( 'webmz-stories' );
	wp_enqueue_script( 'webmz-stories' );

	return webmz_render_story_box( $box_id );
}
add_shortcode( 'webmz_story', 'webmz_story_box_shortcode' );

/**
 * Register story meta boxes.
 *
 * @return void
 */
function webmz_story_register_meta_boxes() {
	add_meta_box(
		'webmz_story_items',
		esc_html__( 'استوری‌ها', 'tadris' ),
		'webmz_story_render_items_meta_box',
		WEBMZ_STORY_POST_TYPE,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'webmz_story_register_meta_boxes' );

/**
 * Register story box meta boxes.
 *
 * @return void
 */
function webmz_story_box_register_meta_boxes() {
	add_meta_box(
		'webmz_story_box_settings',
		esc_html__( 'باکس استوری', 'tadris' ),
		'webmz_story_box_render_settings_meta_box',
		WEBMZ_STORY_BOX_POST_TYPE,
		'normal',
		'high'
	);

	add_meta_box(
		'webmz_story_box_shortcode',
		esc_html__( 'کوتاه‌کد', 'tadris' ),
		'webmz_story_box_render_shortcode_meta_box',
		WEBMZ_STORY_BOX_POST_TYPE,
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'webmz_story_box_register_meta_boxes' );

/**
 * Global / yes / no select helper.
 *
 * @param string $name    Field name.
 * @param string $value   Current value.
 * @param string $id      Field ID.
 * @return void
 */
function webmz_stories_render_global_select( $name, $value, $id = '' ) {
	$id = $id ? $id : sanitize_html_class( $name );
	?>
	<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" class="webmz-stories-select">
		<option value="global" <?php selected( $value, 'global' ); ?>><?php esc_html_e( 'جهانی', 'tadris' ); ?></option>
		<option value="yes" <?php selected( $value, 'yes' ); ?>><?php esc_html_e( 'بله', 'tadris' ); ?></option>
		<option value="no" <?php selected( $value, 'no' ); ?>><?php esc_html_e( 'خیر', 'tadris' ); ?></option>
	</select>
	<?php
}

/**
 * Render one story slide row.
 *
 * @param int                 $index Row index.
 * @param array<string,mixed> $item  Row data.
 * @return void
 */
function webmz_story_render_item_row( $index, $item = array() ) {
	$button_title   = isset( $item['button_title'] ) ? (string) $item['button_title'] : '';
	$button_link    = isset( $item['button_link'] ) ? (string) $item['button_link'] : '';
	$button_new_tab = ! empty( $item['button_new_tab'] );
	$media_id       = isset( $item['media_id'] ) ? absint( $item['media_id'] ) : 0;
	$media_url      = isset( $item['media_url'] ) ? (string) $item['media_url'] : '';
	$media_type     = isset( $item['media_type'] ) ? (string) $item['media_type'] : 'image';
	$duration       = isset( $item['duration'] ) ? absint( $item['duration'] ) : 3;
	$disabled       = ! empty( $item['disabled'] );
	$sync_video     = ! empty( $item['sync_video_duration'] );
	$is_video       = 'video' === $media_type || ( $media_id && 0 === strpos( (string) get_post_mime_type( $media_id ), 'video/' ) );
	$preview        = $media_url;

	if ( $media_id && ! $preview ) {
		$preview = (string) wp_get_attachment_url( $media_id );
	}

	$summary = $button_title ? $button_title : esc_html__( 'آیتم استوری', 'tadris' );
	?>
	<div class="webmz-story-item" data-webmz-story-item>
		<div class="webmz-story-item__header">
			<span class="webmz-story-item__drag dashicons dashicons-move" aria-hidden="true" title="<?php esc_attr_e( 'جابه‌جایی', 'tadris' ); ?>"></span>
			<button type="button" class="webmz-story-item__toggle" data-webmz-story-item-toggle aria-expanded="true">
				<span class="webmz-story-item__summary"><?php echo esc_html( $summary ); ?></span>
			</button>
			<div class="webmz-story-item__actions">
				<button type="button" class="button-link" data-webmz-story-item-duplicate aria-label="<?php esc_attr_e( 'تکثیر', 'tadris' ); ?>"><span class="dashicons dashicons-admin-page"></span></button>
				<button type="button" class="button-link-delete" data-webmz-story-item-remove aria-label="<?php esc_attr_e( 'حذف', 'tadris' ); ?>"><span class="dashicons dashicons-trash"></span></button>
			</div>
		</div>
		<div class="webmz-story-item__body">
			<div class="webmz-story-item__grid">
				<p>
					<label><?php esc_html_e( 'عنوان دکمه', 'tadris' ); ?></label>
					<input type="text" class="widefat" name="webmz_story_items[<?php echo esc_attr( (string) $index ); ?>][button_title]" value="<?php echo esc_attr( $button_title ); ?>" data-webmz-story-summary-source>
				</p>
				<p>
					<label><?php esc_html_e( 'لینک دکمه', 'tadris' ); ?></label>
					<input type="url" class="widefat ltr" name="webmz_story_items[<?php echo esc_attr( (string) $index ); ?>][button_link]" value="<?php echo esc_attr( $button_link ); ?>">
				</p>
				<p class="webmz-story-item__switch">
					<label>
						<input type="checkbox" name="webmz_story_items[<?php echo esc_attr( (string) $index ); ?>][button_new_tab]" value="1" <?php checked( $button_new_tab ); ?>>
						<?php esc_html_e( 'باز کردن در تب جدید', 'tadris' ); ?>
					</label>
				</p>
				<div class="webmz-story-item__media">
					<label><?php esc_html_e( 'رسانه', 'tadris' ); ?></label>
					<div class="webmz-story-item__media-preview">
						<?php if ( $preview ) : ?>
							<?php if ( 'video' === $media_type || ( $media_id && 0 === strpos( (string) get_post_mime_type( $media_id ), 'video/' ) ) ) : ?>
								<video src="<?php echo esc_url( $preview ); ?>" muted playsinline></video>
							<?php else : ?>
								<img src="<?php echo esc_url( $preview ); ?>" alt="">
							<?php endif; ?>
						<?php else : ?>
							<span class="description"><?php esc_html_e( 'انتخاب نشده', 'tadris' ); ?></span>
						<?php endif; ?>
					</div>
					<input type="hidden" name="webmz_story_items[<?php echo esc_attr( (string) $index ); ?>][media_id]" value="<?php echo esc_attr( (string) $media_id ); ?>" data-webmz-story-media-id>
					<input type="hidden" name="webmz_story_items[<?php echo esc_attr( (string) $index ); ?>][media_url]" value="<?php echo esc_attr( $media_url ); ?>" data-webmz-story-media-url>
					<input type="hidden" name="webmz_story_items[<?php echo esc_attr( (string) $index ); ?>][media_type]" value="<?php echo esc_attr( $media_type ); ?>" data-webmz-story-media-type>
					<button type="button" class="button button-secondary" data-webmz-story-media-select><?php esc_html_e( 'افزودن رسانه استوری', 'tadris' ); ?></button>
					<button type="button" class="button-link-delete" data-webmz-story-media-remove <?php echo $preview ? '' : 'hidden'; ?>><?php esc_html_e( 'حذف رسانه', 'tadris' ); ?></button>
				</div>
				<p class="webmz-story-item__duration-row">
					<label><?php esc_html_e( 'مدت زمان', 'tadris' ); ?></label>
					<span class="webmz-story-item__duration">
						<input type="number" min="1" max="300" step="1" name="webmz_story_items[<?php echo esc_attr( (string) $index ); ?>][duration]" value="<?php echo esc_attr( (string) max( 1, $duration ) ); ?>" data-webmz-story-duration-input <?php disabled( $sync_video && $is_video ); ?>>
						<span><?php esc_html_e( 'ثانیه', 'tadris' ); ?></span>
					</span>
					<label class="webmz-story-item__sync-video" data-webmz-story-sync-wrap <?php echo $is_video ? '' : 'hidden'; ?>>
						<input type="checkbox" name="webmz_story_items[<?php echo esc_attr( (string) $index ); ?>][sync_video_duration]" value="1" data-webmz-story-sync-video <?php checked( $sync_video && $is_video ); ?> <?php disabled( ! $is_video ); ?>>
						<?php esc_html_e( 'هم‌زمان با مدت زمان ویدیو', 'tadris' ); ?>
					</label>
				</p>
				<p class="webmz-story-item__switch">
					<label>
						<input type="checkbox" name="webmz_story_items[<?php echo esc_attr( (string) $index ); ?>][disabled]" value="1" <?php checked( $disabled ); ?>>
						<?php esc_html_e( 'غیرفعال کردن آیتم', 'tadris' ); ?>
					</label>
				</p>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render story items meta box.
 *
 * @param WP_Post $post Current post.
 * @return void
 */
function webmz_story_render_items_meta_box( $post ) {
	wp_nonce_field( 'webmz_save_story_items', 'webmz_story_items_nonce' );

	$items = get_post_meta( $post->ID, webmz_get_story_items_meta_key(), true );
	$items = is_array( $items ) ? $items : array();

	if ( empty( $items ) ) {
		$items = array(
			array(
				'button_title'   => '',
				'button_link'    => '',
				'button_new_tab' => false,
				'media_id'       => 0,
				'media_url'      => '',
				'media_type'     => 'image',
				'duration'       => 3,
				'disabled'       => false,
			),
		);
	}
	?>
	<div class="webmz-story-items-metabox" data-webmz-story-items-metabox>
		<div class="webmz-story-items" data-webmz-story-items>
			<?php foreach ( $items as $index => $item ) : ?>
				<?php webmz_story_render_item_row( (int) $index, is_array( $item ) ? $item : array() ); ?>
			<?php endforeach; ?>
		</div>
		<p>
			<button type="button" class="button button-primary" data-webmz-story-item-add><?php esc_html_e( '+ افزودن آیتم استوری', 'tadris' ); ?></button>
		</p>
		<script type="text/html" id="tmpl-webmz-story-item-row">
			<?php webmz_story_render_item_row( '__INDEX__', array() ); ?>
		</script>
	</div>
	<?php
}

/**
 * Render sortable story/post picker for story boxes.
 *
 * @param string       $type     stories|posts.
 * @param array<int>   $selected Selected post IDs in order.
 * @param array<WP_Post> $posts  Available posts.
 * @return void
 */
function webmz_story_box_render_order_picker( $type, $selected, $posts ) {
	$field_name = 'stories' === $type ? 'webmz_story_box_story_ids[]' : 'webmz_story_box_post_ids[]';
	$ordered    = array();

	foreach ( $selected as $selected_id ) {
		foreach ( $posts as $post_item ) {
			if ( (int) $post_item->ID === (int) $selected_id ) {
				$ordered[] = $post_item;
				break;
			}
		}
	}

	$available = array();

	foreach ( $posts as $post_item ) {
		if ( ! in_array( (int) $post_item->ID, $selected, true ) ) {
			$available[] = $post_item;
		}
	}
	?>
	<div class="webmz-story-box-order" data-webmz-story-box-order>
		<ul class="webmz-story-box-order__list" data-webmz-story-order-list>
			<?php foreach ( $ordered as $post_item ) : ?>
				<li class="webmz-story-box-order__item" data-webmz-story-order-item data-id="<?php echo esc_attr( (string) $post_item->ID ); ?>">
					<span class="webmz-story-box-order__drag dashicons dashicons-move" aria-hidden="true"></span>
					<span class="webmz-story-box-order__title"><?php echo esc_html( $post_item->post_title ); ?></span>
					<button type="button" class="webmz-story-box-order__remove" data-webmz-story-order-remove aria-label="<?php esc_attr_e( 'حذف', 'tadris' ); ?>"><span class="dashicons dashicons-no-alt"></span></button>
					<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( (string) $post_item->ID ); ?>">
				</li>
			<?php endforeach; ?>
		</ul>
		<div class="webmz-story-box-order__add">
			<select data-webmz-story-order-select>
				<option value=""><?php esc_html_e( 'انتخاب برای افزودن…', 'tadris' ); ?></option>
				<?php foreach ( $available as $post_item ) : ?>
					<option value="<?php echo esc_attr( (string) $post_item->ID ); ?>"><?php echo esc_html( $post_item->post_title ); ?></option>
				<?php endforeach; ?>
				<?php foreach ( $ordered as $post_item ) : ?>
					<option value="<?php echo esc_attr( (string) $post_item->ID ); ?>" hidden><?php echo esc_html( $post_item->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
			<button type="button" class="button button-secondary" data-webmz-story-order-add><?php esc_html_e( 'افزودن', 'tadris' ); ?></button>
		</div>
		<p class="description"><?php esc_html_e( 'برای تغییر ترتیب، بکشید و رها کنید.', 'tadris' ); ?></p>
		<script type="text/html" class="webmz-story-box-order-template" data-field-name="<?php echo esc_attr( $field_name ); ?>">
			<li class="webmz-story-box-order__item" data-webmz-story-order-item data-id="__ID__">
				<span class="webmz-story-box-order__drag dashicons dashicons-move" aria-hidden="true"></span>
				<span class="webmz-story-box-order__title">__TITLE__</span>
				<button type="button" class="webmz-story-box-order__remove" data-webmz-story-order-remove aria-label="<?php esc_attr_e( 'حذف', 'tadris' ); ?>"><span class="dashicons dashicons-no-alt"></span></button>
				<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" value="__ID__">
			</li>
		</script>
	</div>
	<?php
}

/**
 * Render story box settings meta box.
 *
 * @param WP_Post $post Current post.
 * @return void
 */
function webmz_story_box_render_settings_meta_box( $post ) {
	wp_nonce_field( 'webmz_save_story_box', 'webmz_story_box_nonce' );

	$source    = get_post_meta( $post->ID, '_webmz_story_box_stories_source', true );
	$source    = $source ? $source : 'stories';
	$story_ids = get_post_meta( $post->ID, '_webmz_story_box_story_ids', true );
	$story_ids = is_array( $story_ids ) ? array_map( 'absint', $story_ids ) : array();
	$post_ids  = get_post_meta( $post->ID, '_webmz_story_box_post_ids', true );
	$post_ids  = is_array( $post_ids ) ? array_map( 'absint', $post_ids ) : array();
	$category_ids = get_post_meta( $post->ID, '_webmz_story_box_category_ids', true );
	$category_ids = is_array( $category_ids ) ? array_map( 'absint', $category_ids ) : array();
	$manual_ids   = get_post_meta( $post->ID, '_webmz_story_box_manual_ids', true );

	$all_stories = get_posts(
		array(
			'post_type'      => WEBMZ_STORY_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	$all_posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	$categories = get_categories(
		array(
			'hide_empty' => false,
		)
	);
	?>
	<div class="webmz-story-box-metabox" data-webmz-story-box-metabox>
		<div class="webmz-story-box-source">
			<label><input type="radio" name="webmz_story_box_stories_source" value="stories" <?php checked( $source, 'stories' ); ?>><?php esc_html_e( 'از استوری‌ها', 'tadris' ); ?></label>
			<label><input type="radio" name="webmz_story_box_stories_source" value="posts" <?php checked( $source, 'posts' ); ?>><?php esc_html_e( 'از پست‌ها', 'tadris' ); ?></label>
			<label><input type="radio" name="webmz_story_box_stories_source" value="categories" <?php checked( $source, 'categories' ); ?>><?php esc_html_e( 'از دسته‌ها', 'tadris' ); ?></label>
			<label><input type="radio" name="webmz_story_box_stories_source" value="ids" <?php checked( $source, 'ids' ); ?>><?php esc_html_e( 'از شناسه‌ها', 'tadris' ); ?></label>
		</div>

		<div class="webmz-story-box-source-panel" data-webmz-story-source-panel="stories" <?php echo 'stories' !== $source ? 'hidden' : ''; ?>>
			<?php webmz_story_box_render_order_picker( 'stories', $story_ids, $all_stories ); ?>
		</div>

		<div class="webmz-story-box-source-panel" data-webmz-story-source-panel="posts" <?php echo 'posts' !== $source ? 'hidden' : ''; ?>>
			<?php webmz_story_box_render_order_picker( 'posts', $post_ids, $all_posts ); ?>
		</div>

		<div class="webmz-story-box-source-panel" data-webmz-story-source-panel="categories" <?php echo 'categories' !== $source ? 'hidden' : ''; ?>>
			<select multiple class="webmz-story-box-picker" name="webmz_story_box_category_ids[]">
				<?php foreach ( $categories as $category ) : ?>
					<option value="<?php echo esc_attr( (string) $category->term_id ); ?>" <?php selected( in_array( (int) $category->term_id, $category_ids, true ) ); ?>><?php echo esc_html( $category->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="webmz-story-box-source-panel" data-webmz-story-source-panel="ids" <?php echo 'ids' !== $source ? 'hidden' : ''; ?>>
			<input type="text" class="widefat ltr" name="webmz_story_box_manual_ids" value="<?php echo esc_attr( (string) $manual_ids ); ?>" placeholder="<?php esc_attr_e( 'مثال: 12, 45, 78', 'tadris' ); ?>">
		</div>
	</div>
	<?php
}

/**
 * Render story box shortcode meta box.
 *
 * @param WP_Post $post Current post.
 * @return void
 */
function webmz_story_box_render_shortcode_meta_box( $post ) {
	if ( 'auto-draft' === $post->post_status ) {
		echo '<p class="description">' . esc_html__( 'پس از انتشار، کوتاه‌کد نمایش داده می‌شود.', 'tadris' ) . '</p>';
		return;
	}
	?>
	<p>
		<input type="text" class="widefat ltr" readonly onfocus="this.select();" value="<?php echo esc_attr( '[webmz_story id="' . absint( $post->ID ) . '"]' ); ?>">
	</p>
	<p class="description"><?php esc_html_e( 'این کوتاه‌کد را در برگه یا ویجت المنتور قرار دهید.', 'tadris' ); ?></p>
	<?php
}

/**
 * Enqueue stories admin assets.
 *
 * @param string $hook Current admin page hook.
 * @return void
 */
function webmz_stories_enqueue_admin_assets( $hook ) {
	$is_icons_page = ( 'webmz_story_page_webmz-story-icons' === $hook );

	if ( ! $is_icons_page && ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	if ( ! $is_icons_page ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen || ! in_array( $screen->post_type, array( WEBMZ_STORY_POST_TYPE, WEBMZ_STORY_BOX_POST_TYPE ), true ) ) {
			return;
		}
	}

	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );

	wp_enqueue_style(
		'webmz-stories-admin',
		WEBMZ_URI . 'assets/css/stories-admin.css',
		array(),
		WEBMZ_VERSION
	);

	wp_enqueue_script(
		'webmz-stories-admin',
		WEBMZ_URI . 'assets/js/stories-admin.js',
		array( 'jquery', 'jquery-ui-sortable' ),
		WEBMZ_VERSION,
		true
	);

	wp_localize_script(
		'webmz-stories-admin',
		'webmzStoriesAdmin',
		array(
			'mediaTitle'  => esc_html__( 'انتخاب رسانه استوری', 'tadris' ),
			'mediaButton' => esc_html__( 'استفاده از این رسانه', 'tadris' ),
			'itemLabel'   => esc_html__( 'آیتم استوری', 'tadris' ),
			'svgPreviewEmpty' => esc_html__( 'پیش‌نمایش SVG', 'tadris' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'webmz_stories_enqueue_admin_assets' );

/**
 * Save story items meta box.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return void
 */
function webmz_story_save_items_meta_box( $post_id, $post ) {
	if ( ! isset( $_POST['webmz_story_items_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_story_items_nonce'] ) ), 'webmz_save_story_items' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( WEBMZ_STORY_POST_TYPE !== $post->post_type ) {
		return;
	}

	$raw   = isset( $_POST['webmz_story_items'] ) ? wp_unslash( $_POST['webmz_story_items'] ) : array();
	$items = array();

	if ( is_array( $raw ) ) {
		foreach ( $raw as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$items[] = array(
				'button_title'        => isset( $row['button_title'] ) ? sanitize_text_field( (string) $row['button_title'] ) : '',
				'button_link'         => isset( $row['button_link'] ) ? esc_url_raw( (string) $row['button_link'] ) : '',
				'button_new_tab'      => ! empty( $row['button_new_tab'] ),
				'media_id'            => isset( $row['media_id'] ) ? absint( $row['media_id'] ) : 0,
				'media_url'           => isset( $row['media_url'] ) ? esc_url_raw( (string) $row['media_url'] ) : '',
				'media_type'          => isset( $row['media_type'] ) ? sanitize_key( (string) $row['media_type'] ) : 'image',
				'duration'            => isset( $row['duration'] ) ? max( 1, absint( $row['duration'] ) ) : 3,
				'sync_video_duration' => ! empty( $row['sync_video_duration'] ),
				'disabled'            => ! empty( $row['disabled'] ),
			);
		}
	}

	update_post_meta( $post_id, webmz_get_story_items_meta_key(), $items );
}
add_action( 'save_post', 'webmz_story_save_items_meta_box', 10, 2 );

/**
 * Save story box meta box.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return void
 */
function webmz_story_box_save_meta_box( $post_id, $post ) {
	if ( ! isset( $_POST['webmz_story_box_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_story_box_nonce'] ) ), 'webmz_save_story_box' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( WEBMZ_STORY_BOX_POST_TYPE !== $post->post_type ) {
		return;
	}

	$source = isset( $_POST['webmz_story_box_stories_source'] ) ? sanitize_key( wp_unslash( $_POST['webmz_story_box_stories_source'] ) ) : 'stories';
	if ( ! in_array( $source, array( 'stories', 'posts', 'categories', 'ids' ), true ) ) {
		$source = 'stories';
	}

	update_post_meta( $post_id, '_webmz_story_box_stories_source', $source );

	$story_ids = isset( $_POST['webmz_story_box_story_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['webmz_story_box_story_ids'] ) ) : array();
	update_post_meta( $post_id, '_webmz_story_box_story_ids', array_values( array_filter( $story_ids ) ) );

	$post_ids = isset( $_POST['webmz_story_box_post_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['webmz_story_box_post_ids'] ) ) : array();
	update_post_meta( $post_id, '_webmz_story_box_post_ids', array_values( array_filter( $post_ids ) ) );

	$category_ids = isset( $_POST['webmz_story_box_category_ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['webmz_story_box_category_ids'] ) ) : array();
	update_post_meta( $post_id, '_webmz_story_box_category_ids', array_values( array_filter( $category_ids ) ) );

	$manual_ids = isset( $_POST['webmz_story_box_manual_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['webmz_story_box_manual_ids'] ) ) : '';
	update_post_meta( $post_id, '_webmz_story_box_manual_ids', $manual_ids );
}
add_action( 'save_post', 'webmz_story_box_save_meta_box', 10, 2 );

/**
 * Get published story boxes for select controls.
 *
 * @return array<int,string>
 */
function webmz_get_story_box_options() {
	$boxes = get_posts(
		array(
			'post_type'      => WEBMZ_STORY_BOX_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	$options = array();

	foreach ( $boxes as $box ) {
		$options[ (int) $box->ID ] = $box->post_title;
	}

	return $options;
}
