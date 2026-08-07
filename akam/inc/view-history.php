<?php
/**
 * User media viewing history for video and podcast posts.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WEBMZ_VIEW_HISTORY_ENDPOINT' ) ) {
	define( 'WEBMZ_VIEW_HISTORY_ENDPOINT', 'view-history' );
}

if ( ! defined( 'WEBMZ_VIEW_HISTORY_USER_META' ) ) {
	define( 'WEBMZ_VIEW_HISTORY_USER_META', '_webmz_media_view_history' );
}

/**
 * Get podcast meta key safely.
 *
 * @return string
 */
function webmz_view_history_get_podcast_meta_key() {
	return defined( 'TADRIS_PODCAST_AUDIO_META_KEY' ) ? TADRIS_PODCAST_AUDIO_META_KEY : '_tadris_podcast_audio_url';
}

/**
 * Return media data for a post if it can be tracked.
 *
 * @param int $post_id Post ID.
 * @return array{type:string,url:string}|false
 */
function webmz_view_history_get_post_media( $post_id, $preferred_type = '' ) {
	$post_id        = absint( $post_id );
	$preferred_type = in_array( $preferred_type, array( 'video', 'podcast' ), true ) ? $preferred_type : '';

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
		return false;
	}

	$video_url   = esc_url_raw( get_post_meta( $post_id, '_webmz_video_url', true ) );
	$podcast_url = esc_url_raw( get_post_meta( $post_id, webmz_view_history_get_podcast_meta_key(), true ) );

	if ( 'podcast' === $preferred_type && ! empty( $podcast_url ) ) {
		return array(
			'type' => 'podcast',
			'url'  => $podcast_url,
		);
	}

	if ( 'video' === $preferred_type && ! empty( $video_url ) ) {
		return array(
			'type' => 'video',
			'url'  => $video_url,
		);
	}

	if ( ! empty( $video_url ) ) {
		return array(
			'type' => 'video',
			'url'  => $video_url,
		);
	}

	if ( ! empty( $podcast_url ) ) {
		return array(
			'type' => 'podcast',
			'url'  => $podcast_url,
		);
	}

	return false;
}

/**
 * Check if a post is trackable.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function webmz_view_history_is_trackable_post( $post_id ) {
	return false !== webmz_view_history_get_post_media( $post_id );
}

/**
 * Get saved history for a user.
 *
 * @param int $user_id User ID.
 * @return array<int,array<string,mixed>>
 */
function webmz_view_history_get_user_history( $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();

	if ( ! $user_id ) {
		return array();
	}

	$raw = get_user_meta( $user_id, WEBMZ_VIEW_HISTORY_USER_META, true );
	$raw = is_array( $raw ) ? $raw : array();
	$out = array();

	foreach ( $raw as $post_id => $item ) {
		$post_id = absint( is_array( $item ) && isset( $item['post_id'] ) ? $item['post_id'] : $post_id );

		if ( ! $post_id || ! webmz_view_history_is_trackable_post( $post_id ) || ! is_array( $item ) ) {
			continue;
		}

		$current  = isset( $item['current'] ) ? max( 0, (float) $item['current'] ) : 0;
		$duration = isset( $item['duration'] ) ? max( 0, (float) $item['duration'] ) : 0;
		$updated  = isset( $item['updated'] ) ? absint( $item['updated'] ) : 0;
		$type     = isset( $item['type'] ) && in_array( $item['type'], array( 'video', 'podcast' ), true ) ? $item['type'] : webmz_view_history_get_post_media( $post_id )['type'];

		if ( $duration > 0 && $current > $duration ) {
			$current = $duration;
		}

		if ( $current < 1 ) {
			continue;
		}

		$out[ $post_id ] = array(
			'post_id'  => $post_id,
			'type'     => $type,
			'current'  => $current,
			'duration' => $duration,
			'updated'  => $updated,
		);
	}

	uasort(
		$out,
		static function ( $a, $b ) {
			return absint( $b['updated'] ) <=> absint( $a['updated'] );
		}
	);

	return $out;
}

/**
 * Get one saved progress item for the current/user selected user.
 *
 * @param int $post_id Post ID.
 * @param int $user_id User ID.
 * @return array<string,mixed>
 */
function webmz_view_history_get_user_post_progress( $post_id, $user_id = 0 ) {
	$post_id = absint( $post_id );
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();

	if ( ! $post_id || ! $user_id ) {
		return array();
	}

	$history = webmz_view_history_get_user_history( $user_id );

	return isset( $history[ $post_id ] ) && is_array( $history[ $post_id ] ) ? $history[ $post_id ] : array();
}

/**
 * Format seconds as a readable duration.
 *
 * @param float|int $seconds Seconds.
 * @return string
 */
function webmz_view_history_format_time( $seconds ) {
	$seconds = max( 0, (int) round( (float) $seconds ) );
	$hours   = floor( $seconds / 3600 );
	$minutes = floor( ( $seconds % 3600 ) / 60 );
	$secs    = $seconds % 60;

	if ( $hours > 0 ) {
		return sprintf( '%s:%s:%s', number_format_i18n( $hours ), zeroise( $minutes, 2 ), zeroise( $secs, 2 ) );
	}

	return sprintf( '%s:%s', number_format_i18n( $minutes ), zeroise( $secs, 2 ) );
}

/**
 * Register WooCommerce account endpoint.
 *
 * @return void
 */
function webmz_view_history_register_endpoint() {
	add_rewrite_endpoint( WEBMZ_VIEW_HISTORY_ENDPOINT, EP_ROOT | EP_PAGES );
}
add_action( 'init', 'webmz_view_history_register_endpoint', 12 );

/**
 * Flush rewrite rules once for this endpoint.
 *
 * @return void
 */
function webmz_view_history_maybe_flush_endpoint() {
	if ( '1' !== get_option( 'webmz_view_history_endpoint_v1' ) ) {
		flush_rewrite_rules( false );
		update_option( 'webmz_view_history_endpoint_v1', '1' );
	}
}
add_action( 'init', 'webmz_view_history_maybe_flush_endpoint', 100 );

/**
 * Add endpoint label to the theme account settings list.
 *
 * @param array<string,string> $items Labels.
 * @return array<string,string>
 */
function webmz_view_history_add_endpoint_label( $items ) {
	$items[ WEBMZ_VIEW_HISTORY_ENDPOINT ] = esc_html__( 'تاریخچه مشاهدات', 'tadris' );
	return $items;
}
add_filter( 'webmz_account_default_endpoint_labels', 'webmz_view_history_add_endpoint_label' );

/**
 * Prevent the slug from being used for custom endpoints.
 *
 * @param array<int,string> $slugs Reserved slugs.
 * @return array<int,string>
 */
function webmz_view_history_reserved_endpoint_slug( $slugs ) {
	$slugs[] = WEBMZ_VIEW_HISTORY_ENDPOINT;
	return array_values( array_unique( $slugs ) );
}
add_filter( 'webmz_account_reserved_endpoint_slugs', 'webmz_view_history_reserved_endpoint_slug' );

/**
 * Add menu item before logout.
 *
 * @param array<string,string> $items Menu items.
 * @return array<string,string>
 */
function webmz_view_history_account_menu_item( $items ) {
	if ( ! class_exists( 'WooCommerce' ) || ! is_array( $items ) ) {
		return $items;
	}

	$logout = isset( $items['customer-logout'] ) ? $items['customer-logout'] : null;
	if ( null !== $logout ) {
		unset( $items['customer-logout'] );
	}

	$items[ WEBMZ_VIEW_HISTORY_ENDPOINT ] = esc_html__( 'تاریخچه مشاهدات', 'tadris' );

	if ( null !== $logout ) {
		$items['customer-logout'] = $logout;
	}

	return $items;
}
add_filter( 'woocommerce_account_menu_items', 'webmz_view_history_account_menu_item', 30 );

/**
 * If the admin already selected visible endpoints before this feature existed,
 * make this endpoint visible once. The admin can hide it later from theme options.
 *
 * @return void
 */
function webmz_view_history_maybe_add_to_visible_options() {
	if ( '1' === get_option( 'webmz_view_history_visible_migrated_v1' ) ) {
		return;
	}

	$options = get_option( 'webmz_options', array() );

	if ( is_array( $options ) && ! empty( $options['account_visible_endpoints'] ) && is_array( $options['account_visible_endpoints'] ) ) {
		$visible = array_values( array_unique( array_filter( array_map( 'sanitize_key', $options['account_visible_endpoints'] ) ) ) );
		if ( ! in_array( WEBMZ_VIEW_HISTORY_ENDPOINT, $visible, true ) ) {
			$visible[] = WEBMZ_VIEW_HISTORY_ENDPOINT;
			$options['account_visible_endpoints'] = $visible;
			update_option( 'webmz_options', $options );
		}
	}

	update_option( 'webmz_view_history_visible_migrated_v1', '1' );
}
add_action( 'init', 'webmz_view_history_maybe_add_to_visible_options', 40 );

/**
 * Save history via AJAX.
 *
 * @return void
 */
function webmz_view_history_ajax_save() {
	check_ajax_referer( 'webmz_view_history', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( 'برای ذخیره تاریخچه باید وارد سایت شوید.', 'tadris' ) ), 401 );
	}

	$post_id  = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$current  = isset( $_POST['current'] ) ? (float) sanitize_text_field( wp_unslash( $_POST['current'] ) ) : 0;
	$duration = isset( $_POST['duration'] ) ? (float) sanitize_text_field( wp_unslash( $_POST['duration'] ) ) : 0;
	$requested_type = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : '';
	$requested_type = in_array( $requested_type, array( 'video', 'podcast' ), true ) ? $requested_type : '';

	if ( ! $post_id || ! webmz_view_history_is_trackable_post( $post_id ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'این محتوا قابل ثبت در تاریخچه نیست.', 'tadris' ) ), 404 );
	}

	$current  = max( 0, min( $current, 86400 ) );
	$duration = max( 0, min( $duration, 86400 ) );

	if ( $duration > 0 && $current > $duration ) {
		$current = $duration;
	}

	if ( $current < 1 ) {
		wp_send_json_error( array( 'message' => esc_html__( 'اطلاعات پخش کافی نیست.', 'tadris' ) ), 400 );
	}

	$media   = webmz_view_history_get_post_media( $post_id, $requested_type );
	$user_id = get_current_user_id();
	$history = webmz_view_history_get_user_history( $user_id );

	$history[ $post_id ] = array(
		'post_id'  => $post_id,
		'type'     => $media['type'],
		'current'  => $current,
		'duration' => $duration,
		'updated'  => time(),
	);

	uasort(
		$history,
		static function ( $a, $b ) {
			return absint( $b['updated'] ) <=> absint( $a['updated'] );
		}
	);

	$history = array_slice( $history, 0, 10, true );
	update_user_meta( $user_id, WEBMZ_VIEW_HISTORY_USER_META, $history );

	wp_send_json_success(
		array(
			'message' => esc_html__( 'تاریخچه مشاهده ذخیره شد.', 'tadris' ),
		)
	);
}
add_action( 'wp_ajax_webmz_save_view_history', 'webmz_view_history_ajax_save' );

/**
 * Render one history card.
 *
 * @param array<string,mixed> $item History item.
 * @return void
 */
function webmz_view_history_render_card( $item ) {
	$post_id = absint( $item['post_id'] );
	$post    = get_post( $post_id );
	$media   = webmz_view_history_get_post_media( $post_id );

	if ( ! $post || ! $media ) {
		return;
	}

	$current     = isset( $item['current'] ) ? (float) $item['current'] : 0;
	$duration    = isset( $item['duration'] ) ? (float) $item['duration'] : 0;
	$percent     = $duration > 0 ? min( 100, max( 0, ( $current / $duration ) * 100 ) ) : 0;
	$author_id   = (int) $post->post_author;
	$author_name = get_the_author_meta( 'display_name', $author_id );
	$type_label  = 'video' === $media['type'] ? esc_html__( 'ویدیو', 'tadris' ) : esc_html__( 'پادکست', 'tadris' );
	$updated     = ! empty( $item['updated'] ) ? human_time_diff( absint( $item['updated'] ), current_time( 'timestamp' ) ) . ' ' . esc_html__( 'پیش', 'tadris' ) : '';
	$continue    = add_query_arg( 'webmz_start', max( 0, (int) floor( $current ) ), get_permalink( $post ) );
	$poster      = get_the_post_thumbnail_url( $post, webmz_get_loop_image_size() );
	?>
	<article class="tadris-content-type-1 webmz-view-history-card" data-tadris-post-id="<?php echo esc_attr( $post_id ); ?>">
		<figure class="type-1-figure webmz-view-history-card__figure">
			<a href="<?php echo esc_url( $continue ); ?>" aria-label="<?php echo esc_attr( get_the_title( $post ) ); ?>">
				<?php
				if ( has_post_thumbnail( $post_id ) ) {
					echo webmz_get_post_loop_thumbnail( $post_id, array( 'alt' => esc_attr( get_the_title( $post ) ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo '<span class="webmz-view-history-card__no-image" aria-hidden="true">' . esc_html( mb_substr( get_the_title( $post ), 0, 1 ) ) . '</span>';
				}
				?>
			</a>
		</figure>

		<div class="type-1-content webmz-view-history-card__content">
			<header class="type-1-header">
				<span class="webmz-view-history-card__type"><?php echo esc_html( $type_label ); ?></span>
				<h3 class="type-1-header-tag">
					<a href="<?php echo esc_url( $continue ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a>
				</h3>
			</header>

			<div class="webmz-view-history-progress" aria-label="<?php esc_attr_e( 'میزان مشاهده', 'tadris' ); ?>">
				<div class="webmz-view-history-progress__bar">
					<span style="width: <?php echo esc_attr( round( $percent, 2 ) ); ?>%;"></span>
				</div>
				<div class="webmz-view-history-progress__meta">
					<span><?php echo esc_html( sprintf( __( 'مشاهده تا %1$s', 'tadris' ), webmz_view_history_format_time( $current ) ) ); ?></span>
					<span><?php echo $duration > 0 ? esc_html( sprintf( __( 'از %s', 'tadris' ), webmz_view_history_format_time( $duration ) ) ) : esc_html__( 'مدت نامشخص', 'tadris' ); ?></span>
				</div>
			</div>

			<footer class="type-1-footer webmz-view-history-card__footer">
				<div class="type-1-footer-badge">
					<div class="type-1-footer-text">
						<span><?php esc_html_e( 'مدرس', 'tadris' ); ?></span>
						<strong><?php echo esc_html( $author_name ); ?></strong>
					</div>
				</div>
				<?php if ( $updated ) : ?>
					<div class="type-1-footer-badge">
						<div class="type-1-footer-text">
							<span><?php esc_html_e( 'آخرین مشاهده', 'tadris' ); ?></span>
							<strong><?php echo esc_html( $updated ); ?></strong>
						</div>
					</div>
				<?php endif; ?>
				<a class="webmz-view-history-card__continue" href="<?php echo esc_url( $continue ); ?>"><?php esc_html_e( 'ادامه مشاهده', 'tadris' ); ?></a>
			</footer>
		</div>
	</article>
	<?php
}

/**
 * Render account endpoint content.
 *
 * @return void
 */
function webmz_view_history_account_content() {
	$history = webmz_view_history_get_user_history();

	echo '<div class="webmz-view-history-endpoint">';
	echo '<div class="webmz-view-history-endpoint__head"><h2>' . esc_html__( 'تاریخچه مشاهدات', 'tadris' ) . '</h2><p>' . esc_html__( 'ویدیوها و پادکست‌هایی که شروع کرده‌اید اینجا ذخیره می‌شوند و از همان زمان ادامه پیدا می‌کنند.', 'tadris' ) . '</p></div>';

	if ( empty( $history ) ) {
		echo '</div>';
		return;
	}

	echo '<div class="webmz-view-history-loop webmz-loop-grid" style="--webmz-grid-columns:3;--webmz-grid-tablet-columns:2;--webmz-grid-mobile-columns:1;--webmz-grid-gap:24px;">';
	foreach ( $history as $item ) {
		webmz_view_history_render_card( $item );
	}
	echo '</div>';
	echo '</div>';
}
add_action( 'woocommerce_account_' . WEBMZ_VIEW_HISTORY_ENDPOINT . '_endpoint', 'webmz_view_history_account_content' );

/**
 * Enqueue history scripts and styles.
 *
 * @return void
 */
function webmz_view_history_enqueue_assets() {
	if ( is_user_logged_in() ) {
		wp_enqueue_script( 'webmz-view-history' );
		wp_localize_script(
			'webmz-view-history',
			'webmzViewHistory',
			array(
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'webmz_view_history' ),
				'contextPostId' => is_singular( 'post' ) ? get_queried_object_id() : 0,
			)
		);
	}

	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		wp_enqueue_style( 'webmz-view-history', WEBMZ_URI . 'assets/css/view-history.css', array( 'webmz-main' ), WEBMZ_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'webmz_view_history_enqueue_assets', 30 );
