<?php
/**
 * Functional layer for the Tadris content/course widgets.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register fields required by video posts and course products.
 *
 * @return void
 */
function webmz_tadris_register_meta_boxes() {
	add_meta_box(
		'webmz_tadris_video_meta',
		esc_html__( 'اطلاعات ویدیو آکام', 'tadris' ),
		'webmz_tadris_render_video_meta_box',
		'post',
		'normal',
		'high'
	);

	if ( post_type_exists( 'product' ) ) {
		add_meta_box(
			'webmz_tadris_course_meta',
			esc_html__( 'اطلاعات دوره آکام', 'tadris' ),
			'webmz_tadris_render_course_meta_box',
			'product',
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'webmz_tadris_register_meta_boxes' );

/**
 * Render post video meta fields.
 *
 * The old manual views field has intentionally been removed. Post views are now
 * counted automatically when a single post page is viewed.
 *
 * @param WP_Post $post Current post object.
 * @return void
 */
function webmz_tadris_render_video_meta_box( $post ) {
	wp_nonce_field( 'webmz_tadris_save_meta', 'webmz_tadris_meta_nonce' );

	$video_url       = get_post_meta( $post->ID, '_webmz_video_url', true );
	$views           = webmz_tadris_get_post_views( $post->ID );
	$training_levels = webmz_get_post_training_levels();
	$training_level  = (string) get_post_meta( $post->ID, '_webmz_post_training_level', true );
	$training_level  = isset( $training_levels[ $training_level ] ) ? $training_level : '';
	?>
	<p>
		<label for="webmz_video_url"><strong><?php esc_html_e( 'لینک فایل ویدیو', 'tadris' ); ?></strong></label><br>
		<input class="widefat" type="url" id="webmz_video_url" name="webmz_video_url" value="<?php echo esc_attr( $video_url ); ?>" placeholder="https://example.com/video.mp4">
	</p>

	<p>
		<label for="webmz_post_training_level"><strong><?php esc_html_e( 'سطح آموزش', 'tadris' ); ?></strong></label><br>
		<select id="webmz_post_training_level" name="webmz_post_training_level">
			<option value=""><?php esc_html_e( '— انتخاب نشده —', 'tadris' ); ?></option>
			<?php foreach ( $training_levels as $level_key => $level_label ) : ?>
				<option value="<?php echo esc_attr( $level_key ); ?>" <?php selected( $training_level, $level_key ); ?>><?php echo esc_html( $level_label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>

	<p class="description">
		<?php esc_html_e( 'تصویر شاخص نوشته به‌عنوان پوستر ویدیو و تصویر کارت استفاده می‌شود. نام و توضیحات مدرس از اطلاعات نویسنده نوشته خوانده می‌شود.', 'tadris' ); ?>
	</p>

	<p class="description">
		<strong><?php esc_html_e( 'بازدید فعلی:', 'tadris' ); ?></strong>
		<?php echo esc_html( number_format_i18n( $views ) ); ?>
		<br>
		<?php esc_html_e( 'بازدیدها به‌صورت خودکار با هر بار مشاهده صفحه تکی نوشته افزایش پیدا می‌کنند و دیگر قابل ویرایش دستی نیستند.', 'tadris' ); ?>
	</p>
	<?php
}

/**
 * Render product course meta fields.
 *
 * @param WP_Post $post Current product object.
 * @return void
 */
function webmz_tadris_render_course_meta_box( $post ) {
	wp_nonce_field( 'webmz_tadris_save_meta', 'webmz_tadris_meta_nonce' );

	$status          = get_post_meta( $post->ID, '_webmz_course_status', true );
	$sessions        = absint( get_post_meta( $post->ID, '_webmz_course_sessions', true ) );
	$student_source  = get_post_meta( $post->ID, '_webmz_students_source', true );
	$manual_students = absint( get_post_meta( $post->ID, '_webmz_students_manual', true ) );
	$training_level  = get_post_meta( $post->ID, '_webmz_course_training_level', true );
	$status          = in_array( $status, array( 'finished', 'recording' ), true ) ? $status : 'finished';
	$student_source  = in_array( $student_source, array( 'sales', 'manual' ), true ) ? $student_source : 'sales';
	$training_levels = webmz_get_course_training_levels();
	$training_level  = isset( $training_levels[ $training_level ] ) ? $training_level : '';
	?>
	<p>
		<label for="webmz_course_training_level"><strong><?php esc_html_e( 'سطح آموزش', 'tadris' ); ?></strong></label><br>
		<select id="webmz_course_training_level" name="webmz_course_training_level">
			<option value=""><?php esc_html_e( '— انتخاب نشده —', 'tadris' ); ?></option>
			<?php foreach ( $training_levels as $level_key => $level_label ) : ?>
				<option value="<?php echo esc_attr( $level_key ); ?>" <?php selected( $training_level, $level_key ); ?>><?php echo esc_html( $level_label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>

	<p>
		<label for="webmz_course_status"><strong><?php esc_html_e( 'وضعیت دوره', 'tadris' ); ?></strong></label><br>
		<select id="webmz_course_status" name="webmz_course_status">
			<option value="finished" <?php selected( $status, 'finished' ); ?>><?php esc_html_e( 'تکمیل شده', 'tadris' ); ?></option>
			<option value="recording" <?php selected( $status, 'recording' ); ?>><?php esc_html_e( 'درحال ضبط', 'tadris' ); ?></option>
		</select>
	</p>

	<p>
		<label for="webmz_course_sessions"><strong><?php esc_html_e( 'تعداد جلسات', 'tadris' ); ?></strong></label><br>
		<input type="number" min="0" id="webmz_course_sessions" name="webmz_course_sessions" value="<?php echo esc_attr( $sessions ); ?>">
	</p>

	<p>
		<label for="webmz_students_source"><strong><?php esc_html_e( 'مبنای تعداد دانشجویان', 'tadris' ); ?></strong></label><br>
		<select id="webmz_students_source" name="webmz_students_source">
			<option value="sales" <?php selected( $student_source, 'sales' ); ?>><?php esc_html_e( 'تعداد خرید محصول', 'tadris' ); ?></option>
			<option value="manual" <?php selected( $student_source, 'manual' ); ?>><?php esc_html_e( 'وارد کردن دستی', 'tadris' ); ?></option>
		</select>
	</p>

	<p>
		<label for="webmz_students_manual"><strong><?php esc_html_e( 'تعداد دانشجویان دستی', 'tadris' ); ?></strong></label><br>
		<input type="number" min="0" id="webmz_students_manual" name="webmz_students_manual" value="<?php echo esc_attr( $manual_students ); ?>">
	</p>

	<p class="description">
		<?php esc_html_e( 'نام مدرس دوره از نویسنده محصول گرفته می‌شود و امتیاز دوره از دیدگاه‌های ووکامرس نمایش داده می‌شود.', 'tadris' ); ?>
	</p>
	<?php
}

/**
 * Securely save widget meta fields.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return void
 */
function webmz_tadris_save_meta_fields( $post_id, $post ) {
	if ( ! isset( $_POST['webmz_tadris_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_tadris_meta_nonce'] ) ), 'webmz_tadris_save_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( 'post' === $post->post_type ) {
		$video_url       = isset( $_POST['webmz_video_url'] ) ? esc_url_raw( wp_unslash( $_POST['webmz_video_url'] ) ) : '';
		$training_levels = webmz_get_post_training_levels();
		$training_level  = isset( $_POST['webmz_post_training_level'] ) ? sanitize_key( wp_unslash( $_POST['webmz_post_training_level'] ) ) : '';

		if ( ! isset( $training_levels[ $training_level ] ) ) {
			$training_level = '';
		}

		update_post_meta( $post_id, '_webmz_video_url', $video_url );
		update_post_meta( $post_id, '_webmz_post_training_level', $training_level );
	}

	if ( 'product' === $post->post_type ) {
		$status          = isset( $_POST['webmz_course_status'] ) && in_array( $_POST['webmz_course_status'], array( 'finished', 'recording' ), true ) ? sanitize_key( $_POST['webmz_course_status'] ) : 'finished';
		$sessions        = isset( $_POST['webmz_course_sessions'] ) ? absint( $_POST['webmz_course_sessions'] ) : 0;
		$student_source  = isset( $_POST['webmz_students_source'] ) && in_array( $_POST['webmz_students_source'], array( 'sales', 'manual' ), true ) ? sanitize_key( $_POST['webmz_students_source'] ) : 'sales';
		$manual_students = isset( $_POST['webmz_students_manual'] ) ? absint( $_POST['webmz_students_manual'] ) : 0;
		$training_levels = webmz_get_course_training_levels();
		$training_level  = isset( $_POST['webmz_course_training_level'] ) ? sanitize_key( wp_unslash( $_POST['webmz_course_training_level'] ) ) : '';

		if ( ! isset( $training_levels[ $training_level ] ) ) {
			$training_level = '';
		}

		update_post_meta( $post_id, '_webmz_course_status', $status );
		update_post_meta( $post_id, '_webmz_course_sessions', $sessions );
		update_post_meta( $post_id, '_webmz_students_source', $student_source );
		update_post_meta( $post_id, '_webmz_students_manual', $manual_students );
		update_post_meta( $post_id, '_webmz_course_training_level', $training_level );
	}
}
add_action( 'save_post', 'webmz_tadris_save_meta_fields', 10, 2 );

/**
 * Available course training level options.
 *
 * @return array<string,string>
 */
function webmz_get_course_training_levels() {
	return array(
		'beginner'     => esc_html__( 'مبتدی', 'tadris' ),
		'intermediate' => esc_html__( 'متوسط', 'tadris' ),
		'advanced'     => esc_html__( 'پیشرفته', 'tadris' ),
	);
}

/**
 * Available training level options for post videos.
 *
 * @return array<string,string>
 */
function webmz_get_post_training_levels() {
	return webmz_get_course_training_levels();
}

/**
 * Get post training level label.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function webmz_get_post_training_level_label( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
		return '';
	}

	$level  = (string) get_post_meta( $post_id, '_webmz_post_training_level', true );
	$levels = webmz_get_post_training_levels();

	return isset( $levels[ $level ] ) ? $levels[ $level ] : '';
}

/**
 * Get saved training level key for a product.
 *
 * @param int $product_id Product ID.
 * @return string
 */
function webmz_get_course_training_level( $product_id ) {
	$level  = (string) get_post_meta( $product_id, '_webmz_course_training_level', true );
	$levels = webmz_get_course_training_levels();

	return isset( $levels[ $level ] ) ? $level : '';
}

/**
 * Get localized training level label for a product.
 *
 * @param int $product_id Product ID.
 * @return string
 */
function webmz_get_course_training_level_label( $product_id ) {
	$level  = webmz_get_course_training_level( $product_id );
	$levels = webmz_get_course_training_levels();

	return $level ? $levels[ $level ] : '';
}

/**
 * Current post views meta key.
 *
 * @return string
 */
function webmz_tadris_get_post_views_meta_key() {
	return '_webmz_post_views';
}

/**
 * Get real automatic views for a post.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function webmz_tadris_get_post_views( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
		return 0;
	}

	$key       = webmz_tadris_get_post_views_meta_key();
	$value     = get_post_meta( $post_id, $key, true );
	$old_value = get_post_meta( $post_id, '_webmz_video_views', true );

	if ( '' === $value && '' !== $old_value ) {
		$value = absint( $old_value );
		update_post_meta( $post_id, $key, $value );
	}

	return absint( $value );
}

/**
 * Increment post views by one.
 *
 * @param int $post_id Post ID.
 * @return int New views count.
 */
function webmz_tadris_increment_post_views( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
		return 0;
	}

	$key   = webmz_tadris_get_post_views_meta_key();
	$count = webmz_tadris_get_post_views( $post_id ) + 1;

	update_post_meta( $post_id, $key, $count );

	return $count;
}

/**
 * Count a view whenever a single published post is viewed.
 *
 * @return void
 */
function webmz_tadris_track_single_post_view() {
	if ( is_admin() || wp_doing_ajax() || is_feed() || is_preview() || is_robots() ) {
		return;
	}

	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$post_id = get_queried_object_id();

	if ( $post_id ) {
		webmz_tadris_increment_post_views( $post_id );
	}
}
add_action( 'template_redirect', 'webmz_tadris_track_single_post_view', 20 );

/**
 * Resolve a selected post or latest post within a category.
 *
 * @param string              $source      manual|category.
 * @param int                 $post_id     Selected post ID.
 * @param int                 $category_id Selected category ID.
 * @param array<string,mixed> $extra_args  Optional WP_Query args.
 * @return WP_Post|null
 */
function webmz_tadris_resolve_post( $source, $post_id, $category_id, $extra_args = array() ) {
	$source = in_array( $source, array( 'manual', 'category' ), true ) ? $source : 'manual';

	if ( 'manual' === $source && $post_id ) {
		$post = get_post( absint( $post_id ) );
		return $post && 'post' === $post->post_type && 'publish' === $post->post_status ? $post : null;
	}

	$args = wp_parse_args(
		is_array( $extra_args ) ? $extra_args : array(),
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'cat'                    => absint( $category_id ),
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => true,
		)
	);

	$query = new WP_Query( $args );

	return $query->have_posts() ? $query->posts[0] : null;
}

/**
 * Backward-compatible resolver for the large video widget.
 *
 * @param string $source      manual|category.
 * @param int    $post_id     Selected post ID.
 * @param int    $category_id Selected category ID.
 * @return WP_Post|null
 */
function webmz_tadris_resolve_video_post( $source, $post_id, $category_id ) {
	return webmz_tadris_resolve_post( $source, $post_id, $category_id );
}

/**
 * Get available posts for Elementor controls.
 *
 * @return array<int|string,string>
 */
function webmz_tadris_get_post_options() {
	$choices = array( '' => esc_html__( 'انتخاب نوشته', 'tadris' ) );
	$posts   = get_posts(
		array(
			'post_type'   => 'post',
			'post_status' => 'publish',
			'numberposts' => 100,
			'orderby'     => 'date',
			'order'       => 'DESC',
		)
	);

	foreach ( $posts as $post ) {
		$choices[ $post->ID ] = $post->post_title;
	}

	return $choices;
}

/**
 * Get available products for Elementor controls.
 *
 * @return array<int|string,string>
 */
function webmz_tadris_get_product_options() {
	$choices = array( '' => esc_html__( 'انتخاب محصول', 'tadris' ) );

	if ( ! post_type_exists( 'product' ) ) {
		return $choices;
	}

	$products = get_posts(
		array(
			'post_type'   => 'product',
			'post_status' => 'publish',
			'numberposts' => 200,
			'orderby'     => 'date',
			'order'       => 'DESC',
		)
	);

	foreach ( $products as $product_post ) {
		$choices[ $product_post->ID ] = $product_post->post_title;
	}

	return $choices;
}

/**
 * Get category options for Elementor controls.
 *
 * @return array<int|string,string>
 */
function webmz_tadris_get_post_category_options() {
	$choices = array( '' => esc_html__( 'همه دسته‌ها', 'tadris' ) );
	$terms   = get_categories( array( 'hide_empty' => false ) );

	foreach ( $terms as $term ) {
		$choices[ $term->term_id ] = $term->name;
	}

	return $choices;
}

/**
 * Estimate post reading time in minutes.
 *
 * @param int $post_id Post ID.
 * @param int $wpm     Words per minute.
 * @return int
 */
function webmz_tadris_get_post_reading_time( $post_id = 0, $wpm = 200 ) {
	$post_id = absint( $post_id );
	$wpm     = max( 50, absint( $wpm ) );

	if ( ! $post_id ) {
		return 1;
	}

	$content = (string) get_post_field( 'post_content', $post_id );
	$excerpt = (string) get_post_field( 'post_excerpt', $post_id );
	$text    = trim( wp_strip_all_tags( $excerpt ? $excerpt : $content ) );

	if ( '' === $text ) {
		return 1;
	}

	$words = preg_split( '/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY );
	$count = is_array( $words ) ? count( $words ) : 0;

	return max( 1, (int) ceil( $count / $wpm ) );
}

/**
 * Human-readable relative publish date for post cards.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function webmz_tadris_get_post_relative_date( $post_id = 0 ) {
	$post_id = absint( $post_id );

	if ( ! $post_id ) {
		return '';
	}

	$timestamp = get_post_timestamp( $post_id );

	if ( ! $timestamp && function_exists( 'get_post_time' ) ) {
		$timestamp = (int) get_post_time( 'U', true, $post_id );
	}

	if ( ! $timestamp ) {
		return '';
	}

	$diff = human_time_diff( $timestamp, current_time( 'timestamp' ) );
	$out  = $diff . ' ' . esc_html__( 'پیش', 'tadris' );

	return function_exists( 'webmz_to_persian_digits' ) ? webmz_to_persian_digits( $out ) : $out;
}

/**
 * Check whether a post has a video URL saved in its metabox.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function webmz_post_has_video_url( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
		return false;
	}

	$video_url = esc_url_raw( (string) get_post_meta( $post_id, '_webmz_video_url', true ) );

	return '' !== $video_url;
}

/**
 * Saved post IDs for the current user.
 *
 * @param int $user_id Optional user ID.
 * @return array<int>
 */
function webmz_tadris_get_saved_posts( $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();
	$saved   = $user_id ? get_user_meta( $user_id, '_webmz_saved_video_posts', true ) : array();

	return array_values( array_filter( array_map( 'absint', is_array( $saved ) ? $saved : array() ) ) );
}

/**
 * Check whether a user saved a given post.
 *
 * @param int $user_id User ID.
 * @param int $post_id Post ID.
 * @return bool
 */
function webmz_tadris_user_has_favorite( $user_id, $post_id ) {
	$user_id = absint( $user_id );
	$post_id = absint( $post_id );

	if ( ! $user_id || ! $post_id ) {
		return false;
	}

	return in_array( $post_id, webmz_tadris_get_saved_posts( $user_id ), true );
}


/**
 * Render shared post save/share action buttons.
 *
 * This is the single markup entry point used by video/podcast widgets. It keeps
 * the same Ajax save mechanism (`data-webmz-favorite-post`) and the same share
 * modal mechanism (`data-webmz-share-toggle`) handled by assets/js/tadris-widgets.js.
 *
 * @param int                  $post_id Post ID.
 * @param array<string,mixed>  $args    Optional classes and display settings.
 * @return void
 */
function webmz_tadris_render_post_action_buttons( $post_id = 0, $args = array() ) {
	$defaults = array(
		'preview'       => false,
		'wrapper_class' => 'tadris-post-actions',
		'save_class'    => 'tadrist-video-aciton video-action-save',
		'share_class'   => 'tadrist-video-aciton video-action-share',
		'save_label'    => esc_html__( 'ذخیره', 'tadris' ),
		'remove_label'  => esc_html__( 'حذف از ذخیره', 'tadris' ),
		'share_label'   => esc_html__( 'اشتراک گذاری', 'tadris' ),
		'icon_only'     => false,
		'show_save'     => true,
		'show_share'    => true,
	);

	$args = wp_parse_args( $args, $defaults );

	if ( empty( $args['preview'] ) ) {
		$post_id = $post_id ? absint( $post_id ) : get_the_ID();

		if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
			return;
		}
	} else {
		$post_id = 0;
	}

	$show_save  = ! empty( $args['show_save'] );
	$show_share = ! empty( $args['show_share'] );

	if ( ! $show_save && ! $show_share ) {
		return;
	}

	$is_saved   = $post_id ? webmz_tadris_user_has_favorite( get_current_user_id(), $post_id ) : false;
	$post_url   = $post_id ? get_permalink( $post_id ) : '#';
	$post_title = $post_id ? get_the_title( $post_id ) : esc_html__( 'عنوان نوشته', 'tadris' );
	$save_class = trim( $args['save_class'] . ' video-action-save' . ( $is_saved ? ' is-saved' : '' ) );
	$share_class = trim( $args['share_class'] . ' video-action-share' );
	$label_class = ! empty( $args['icon_only'] ) ? 'webmz-favorite-label screen-reader-text' : 'webmz-favorite-label';
	$share_label_class = ! empty( $args['icon_only'] ) ? 'screen-reader-text' : '';
	?>
	<div class="<?php echo esc_attr( $args['wrapper_class'] ); ?>">
		<?php if ( $show_save ) : ?>
		<button
			type="button"
			class="<?php echo esc_attr( $save_class ); ?>"
			<?php if ( $post_id ) : ?>
			data-webmz-favorite-post="<?php echo esc_attr( $post_id ); ?>"
			<?php endif; ?>
			data-save-label="<?php echo esc_attr( $args['save_label'] ); ?>"
			data-remove-label="<?php echo esc_attr( $args['remove_label'] ); ?>"
			aria-pressed="<?php echo $is_saved ? 'true' : 'false'; ?>"
			<?php echo empty( $args['preview'] ) ? '' : ' disabled'; ?>
		>
			<span class="<?php echo esc_attr( $label_class ); ?>">
				<?php echo esc_html( $is_saved ? $args['remove_label'] : $args['save_label'] ); ?>
			</span>

			<span class="webmz-favorite-icon webmz-favorite-icon--save" aria-hidden="true">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-bookmark">
					<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
					<path d="M18 7v14l-6 -4l-6 4v-14a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4"/>
				</svg>
			</span>

			<span class="webmz-favorite-icon webmz-favorite-icon--remove" aria-hidden="true">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-bookmark-off">
					<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
					<path d="M7.708 3.721a3.982 3.982 0 0 1 2.292 -.721h4a4 4 0 0 1 4 4v7m0 4v3l-6 -4l-6 4v-14c0 -.308 .035 -.609 .1 -.897"/>
					<path d="M3 3l18 18"/>
				</svg>
			</span>
		</button>
		<?php endif; ?>

		<?php if ( $show_share ) : ?>
		<button
			type="button"
			class="<?php echo esc_attr( $share_class ); ?>"
			<?php if ( $post_id ) : ?>
			data-webmz-share-toggle="<?php echo esc_attr( $post_id ); ?>"
			<?php endif; ?>
			data-share-url="<?php echo esc_url( $post_url ); ?>"
			data-share-title="<?php echo esc_attr( $post_title ); ?>"
			<?php echo empty( $args['preview'] ) ? '' : ' disabled'; ?>
		>
			<span class="<?php echo esc_attr( $share_label_class ); ?>"><?php echo esc_html( $args['share_label'] ); ?></span>
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-share" aria-hidden="true">
				<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
				<path d="M3 12a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/>
				<path d="M15 6a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/>
				<path d="M15 18a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/>
				<path d="M8.7 10.7l6.6 -3.4"/>
				<path d="M8.7 13.3l6.6 3.4"/>
			</svg>
		</button>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Favorite Ajax toggle.
 *
 * @return void
 */
function webmz_tadris_ajax_toggle_favorite() {
	check_ajax_referer( 'webmz_tadris_widgets', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( 'ابتدا وارد سایت شوید.', 'tadris' ) ), 401 );
	}

	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'نوشته معتبر نیست.', 'tadris' ) ), 404 );
	}

	$saved = webmz_tadris_get_saved_posts();
	$index = array_search( $post_id, $saved, true );

	if ( false === $index ) {
		$saved[] = $post_id;
		$active  = true;
		$message = esc_html__( 'به ذخیره‌شده‌ها اضافه شد.', 'tadris' );
	} else {
		unset( $saved[ $index ] );
		$active  = false;
		$message = esc_html__( 'از ذخیره‌شده‌ها حذف شد.', 'tadris' );
	}

	update_user_meta( get_current_user_id(), '_webmz_saved_video_posts', array_values( $saved ) );

	wp_send_json_success(
		array(
			'active'  => $active,
			'message' => $message,
		)
	);
}
add_action( 'wp_ajax_webmz_tadris_toggle_favorite', 'webmz_tadris_ajax_toggle_favorite' );
add_action( 'wp_ajax_nopriv_webmz_tadris_toggle_favorite', 'webmz_tadris_ajax_toggle_favorite' );

/**
 * Add saved-video account endpoint.
 *
 * @return void
 */
function webmz_tadris_add_saved_endpoint() {
	add_rewrite_endpoint( 'saved-videos', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'webmz_tadris_add_saved_endpoint' );

/**
 * Flush rewrite rules once after this feature is installed or updated.
 *
 * @return void
 */
function webmz_tadris_maybe_flush_saved_endpoint() {
	if ( '1' !== get_option( 'webmz_saved_videos_endpoint_v1' ) ) {
		flush_rewrite_rules( false );
		update_option( 'webmz_saved_videos_endpoint_v1', '1' );
	}
}
add_action( 'init', 'webmz_tadris_maybe_flush_saved_endpoint', 99 );

/**
 * Add account menu item before logout.
 *
 * @param array<string,string> $items Account menu items.
 * @return array<string,string>
 */
function webmz_tadris_account_menu_items( $items ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return $items;
	}

	$logout = isset( $items['customer-logout'] ) ? $items['customer-logout'] : null;

	if ( $logout ) {
		unset( $items['customer-logout'] );
	}

	$items['saved-videos'] = esc_html__( 'ذخیره شده‌ها', 'tadris' );

	if ( $logout ) {
		$items['customer-logout'] = $logout;
	}

	return $items;
}
add_filter( 'woocommerce_account_menu_items', 'webmz_tadris_account_menu_items' );

/**
 * Render a basic saved-video card in the WooCommerce account endpoint.
 *
 * @param WP_Post $post Post instance.
 * @return void
 */
function webmz_tadris_render_video_card( $post ) {
	$author_id   = (int) $post->post_author;
	$author_name = get_the_author_meta( 'display_name', $author_id );
	$views       = webmz_tadris_get_post_views( $post->ID );
	?>
	<article class="tadris-content-type-1">
		<figure class="type-1-figure">
			<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" aria-label="<?php echo esc_attr( get_the_title( $post ) ); ?>">
				<?php echo webmz_get_post_loop_thumbnail( $post, array( 'alt' => esc_attr( get_the_title( $post ) ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</figure>

		<div class="type-1-content">
			<header class="type-1-header">
				<h3 class="type-1-header-tag">
					<a href="<?php echo esc_url( get_permalink( $post ) ); ?>">
						<?php echo esc_html( get_the_title( $post ) ); ?>
					</a>
				</h3>
			</header>

			<footer class="type-1-footer">
				<div class="type-1-footer-badge">
					<div class="type-1-footer-text">
						<span><?php esc_html_e( 'مدرس', 'tadris' ); ?></span>
						<strong><?php echo esc_html( $author_name ); ?></strong>
					</div>
				</div>

				<div class="type-1-footer-badge">
					<div class="type-1-footer-text">
						<span><?php esc_html_e( 'بازدیدها', 'tadris' ); ?></span>
						<strong><?php echo esc_html( number_format_i18n( $views ) ); ?></strong>
					</div>
				</div>
			</footer>
		</div>
	</article>
	<?php
}

/**
 * Render the My Account saved-videos page.
 *
 * @return void
 */
function webmz_tadris_saved_videos_account_content() {
	$ids = webmz_tadris_get_saved_posts();

	if ( empty( $ids ) ) {
		echo '<p class="tadris-saved-empty">' . esc_html__( 'هنوز ویدیویی ذخیره نکرده‌اید.', 'tadris' ) . '</p>';
		return;
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'post__in'       => $ids,
			'orderby'        => 'post__in',
			'posts_per_page' => -1,
		)
	);

	if ( $query->have_posts() ) {
		echo '<div class="tadris-loop-type-1 mt">';

		while ( $query->have_posts() ) {
			$query->the_post();
			webmz_tadris_render_video_card( get_post() );
		}

		echo '</div>';
	}

	wp_reset_postdata();
}
add_action( 'woocommerce_account_saved-videos_endpoint', 'webmz_tadris_saved_videos_account_content' );

/**
 * Podcast audio URL meta key.
 */
if ( ! defined( 'TADRIS_PODCAST_AUDIO_META_KEY' ) ) {
    define( 'TADRIS_PODCAST_AUDIO_META_KEY', '_tadris_podcast_audio_url' );
}

/**
 * Add podcast audio metabox to posts.
 */
function webmz_tadris_add_podcast_audio_metabox() {
    add_meta_box(
        'webmz_tadris_podcast_audio_metabox',
        'لینک فایل پادکست',
        'webmz_tadris_render_podcast_audio_metabox',
        'post',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'webmz_tadris_add_podcast_audio_metabox' );

/**
 * Render podcast audio metabox.
 */
function webmz_tadris_render_podcast_audio_metabox( $post ) {
    wp_nonce_field( 'webmz_tadris_save_podcast_audio', 'webmz_tadris_podcast_audio_nonce' );

    $audio_url = get_post_meta( $post->ID, TADRIS_PODCAST_AUDIO_META_KEY, true );
    ?>
    <p>
        <label for="webmz_tadris_podcast_audio_url">
            لینک فایل صوتی پادکست را وارد کنید:
        </label>
    </p>

    <input
        type="url"
        id="webmz_tadris_podcast_audio_url"
        name="webmz_tadris_podcast_audio_url"
        value="<?php echo esc_attr( $audio_url ); ?>"
        style="width: 100%; direction: ltr;"
        placeholder="https://example.com/podcast.mp3"
    >

    <p style="color:#666;margin-top:8px;">
        فرمت پیشنهادی: mp3. اگر این فیلد خالی باشد، این نوشته در ویجت «لود پادکست‌ها» نمایش داده نمی‌شود.
    </p>
    <?php
}

/**
 * Save podcast audio metabox.
 */
function webmz_tadris_save_podcast_audio_metabox( $post_id ) {
    if (
        ! isset( $_POST['webmz_tadris_podcast_audio_nonce'] ) ||
        ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_tadris_podcast_audio_nonce'] ) ), 'webmz_tadris_save_podcast_audio' )
    ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( 'post' !== get_post_type( $post_id ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $audio_url = '';

    if ( isset( $_POST['webmz_tadris_podcast_audio_url'] ) ) {
        $audio_url = esc_url_raw( wp_unslash( $_POST['webmz_tadris_podcast_audio_url'] ) );
    }

    if ( ! empty( $audio_url ) ) {
        update_post_meta( $post_id, TADRIS_PODCAST_AUDIO_META_KEY, $audio_url );
    } else {
        delete_post_meta( $post_id, TADRIS_PODCAST_AUDIO_META_KEY );
    }
}
add_action( 'save_post', 'webmz_tadris_save_podcast_audio_metabox' );

/**
 * Get podcast audio url.
 */
function webmz_tadris_get_podcast_audio_url( $post_id = 0 ) {
    $post_id = $post_id ? absint( $post_id ) : get_the_ID();

    if ( ! $post_id ) {
        return '';
    }

    return esc_url( get_post_meta( $post_id, TADRIS_PODCAST_AUDIO_META_KEY, true ) );
}
