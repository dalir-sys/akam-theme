<?php
/**
 * Teachers custom post type, metaboxes, and helpers.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WEBMZ_TEACHER_POST_TYPE' ) ) {
	define( 'WEBMZ_TEACHER_POST_TYPE', 'webmz_teacher' );
}

/**
 * Register teachers post type.
 *
 * @return void
 */
function webmz_teacher_register_post_type() {
	$labels = array(
		'name'               => esc_html__( 'مدرسین', 'tadris' ),
		'singular_name'      => esc_html__( 'مدرس', 'tadris' ),
		'add_new'            => esc_html__( 'افزودن مدرس', 'tadris' ),
		'add_new_item'       => esc_html__( 'افزودن مدرس جدید', 'tadris' ),
		'edit_item'          => esc_html__( 'ویرایش مدرس', 'tadris' ),
		'new_item'           => esc_html__( 'مدرس جدید', 'tadris' ),
		'view_item'          => esc_html__( 'مشاهده مدرس', 'tadris' ),
		'search_items'       => esc_html__( 'جستجوی مدرسین', 'tadris' ),
		'not_found'          => esc_html__( 'مدرسی پیدا نشد.', 'tadris' ),
		'not_found_in_trash' => esc_html__( 'مدرسی در زباله‌دان نیست.', 'tadris' ),
		'menu_name'          => esc_html__( 'مدرسین', 'tadris' ),
		'all_items'          => esc_html__( 'همه مدرسین', 'tadris' ),
		'archives'           => esc_html__( 'آرشیو مدرسین', 'tadris' ),
	);

	register_post_type(
		WEBMZ_TEACHER_POST_TYPE,
		array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => 'webmz-options',
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-groups',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'elementor', 'comments' ),
			'has_archive'         => true,
			'rewrite'             => array(
				'slug'       => 'mentors',
				'with_front' => false,
			),
			'query_var'           => true,
			'capability_type'     => 'post',
		)
	);
}
add_action( 'init', 'webmz_teacher_register_post_type' );

/**
 * Flush rewrite rules after teachers post type is registered.
 *
 * @return void
 */
function webmz_teacher_flush_rewrite_rules() {
	webmz_teacher_register_post_type();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'webmz_teacher_flush_rewrite_rules' );

/**
 * Meta keys used by teacher profiles.
 *
 * @return array<string,string>
 */
function webmz_teacher_meta_keys() {
	return array(
		'job_title'            => '_webmz_teacher_job_title',
		'short_bio'            => '_webmz_teacher_short_bio',
		'student_count'        => '_webmz_teacher_student_count',
		'experience_years'     => '_webmz_teacher_experience_years',
		'course_count'         => '_webmz_teacher_course_count',
		'rating'               => '_webmz_teacher_rating',
		'expertise_photo'      => '_webmz_teacher_expertise_photo',
		'intro_video_url'      => '_webmz_teacher_intro_video_url',
		'teaching_experience'  => '_webmz_teacher_teaching_experience',
		'experience'           => '_webmz_teacher_experience',
		'specialties'          => '_webmz_teacher_specialties',
		'courses'              => '_webmz_teacher_courses',
	);
}

/**
 * Get a teacher meta value.
 *
 * @param int    $post_id Post ID.
 * @param string $field   Field key.
 * @param mixed  $default Default value.
 * @return mixed
 */
function webmz_get_teacher_meta( $post_id, $field, $default = '' ) {
	$keys = webmz_teacher_meta_keys();

	if ( ! isset( $keys[ $field ] ) ) {
		return $default;
	}

	$value = get_post_meta( absint( $post_id ), $keys[ $field ], true );

	return ( '' === $value || null === $value ) ? $default : $value;
}

/**
 * Get teacher job title.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function webmz_get_teacher_job_title( $post_id ) {
	return sanitize_text_field( (string) webmz_get_teacher_meta( $post_id, 'job_title', '' ) );
}

/**
 * Get teacher short bio.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function webmz_get_teacher_short_bio( $post_id ) {
	return sanitize_textarea_field( (string) webmz_get_teacher_meta( $post_id, 'short_bio', '' ) );
}

/**
 * Get teacher expertise photo attachment ID.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function webmz_get_teacher_expertise_photo_id( $post_id ) {
	return absint( webmz_get_teacher_meta( $post_id, 'expertise_photo', 0 ) );
}

/**
 * Get teacher expertise photo URL.
 *
 * @param int    $post_id Post ID.
 * @param string $size    Image size.
 * @return string
 */
function webmz_get_teacher_expertise_photo_url( $post_id, $size = 'thumbnail' ) {
	$attachment_id = webmz_get_teacher_expertise_photo_id( $post_id );

	if ( ! $attachment_id ) {
		return '';
	}

	$url = wp_get_attachment_image_url( $attachment_id, $size );

	return $url ? esc_url( $url ) : '';
}

/**
 * Get teacher intro video URL.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function webmz_get_teacher_intro_video_url( $post_id ) {
	return esc_url( (string) webmz_get_teacher_meta( $post_id, 'intro_video_url', '' ) );
}

/**
 * Get teacher teaching experience label.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function webmz_get_teacher_teaching_experience( $post_id ) {
	return sanitize_text_field( (string) webmz_get_teacher_meta( $post_id, 'teaching_experience', '' ) );
}

/**
 * Get sanitized list items for experience or specialties.
 *
 * @param int    $post_id Post ID.
 * @param string $field   experience|specialties.
 * @return array<int,string>
 */
function webmz_get_teacher_list_items( $post_id, $field ) {
	$raw = webmz_get_teacher_meta( $post_id, $field, array() );

	if ( ! is_array( $raw ) ) {
		return array();
	}

	$items = array();

	foreach ( $raw as $row ) {
		if ( is_array( $row ) ) {
			$text = isset( $row['text'] ) ? trim( (string) $row['text'] ) : '';
		} else {
			$text = trim( (string) $row );
		}

		if ( '' !== $text ) {
			$items[] = sanitize_text_field( $text );
		}
	}

	return $items;
}

/**
 * Get related course product IDs.
 *
 * @param int $post_id Post ID.
 * @return array<int,int>
 */
function webmz_get_teacher_courses( $post_id ) {
	$raw = webmz_get_teacher_meta( $post_id, 'courses', array() );

	if ( ! is_array( $raw ) ) {
		return array();
	}

	$ids = array();

	foreach ( $raw as $id ) {
		$id = absint( $id );
		if ( $id > 0 ) {
			$ids[] = $id;
		}
	}

	return array_values( array_unique( $ids ) );
}

/**
 * Build teacher stats for display.
 *
 * @param int $post_id Post ID.
 * @return array<string,string>
 */
function webmz_get_teacher_stats( $post_id ) {
	$student_count    = absint( webmz_get_teacher_meta( $post_id, 'student_count', 0 ) );
	$experience_years = sanitize_text_field( (string) webmz_get_teacher_meta( $post_id, 'experience_years', '' ) );
	$course_count     = absint( webmz_get_teacher_meta( $post_id, 'course_count', 0 ) );
	$rating           = webmz_get_teacher_meta( $post_id, 'rating', 0 );

	if ( ! $course_count ) {
		$course_count = count( webmz_get_teacher_courses( $post_id ) );
	}

	return array(
		'students'   => $student_count ? sprintf(
			/* translators: %s: student count */
			esc_html__( '%s دانشجو', 'tadris' ),
			webmz_to_persian_digits( (string) $student_count )
		) : '',
		'experience' => $experience_years ? sprintf(
			/* translators: %s: years of experience */
			esc_html__( '%s تجربه', 'tadris' ),
			webmz_to_persian_digits( $experience_years )
		) : '',
		'courses'    => $course_count ? sprintf(
			/* translators: %s: course count */
			esc_html__( '%s دوره', 'tadris' ),
			webmz_to_persian_digits( (string) $course_count )
		) : '',
		'rating'     => $rating > 0 ? sprintf(
			/* translators: %s: rating value */
			esc_html__( 'امتیاز %s', 'tadris' ),
			webmz_to_persian_digits( number_format_i18n( $rating, 1 ) )
		) : '',
	);
}

/**
 * Get inline SVG icon for a teacher stat key.
 *
 * @param string $key rating|students|courses|experience.
 * @return string
 */
function webmz_get_teacher_stat_icon_svg( $key ) {
	$icons = array(
		'rating'     => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873l-6.158 -3.245"/></svg>',
		'students'   => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M22 9l-10 -4l-10 4l10 4l10 -4v6"/><path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4"/></svg>',
		'courses'    => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 4v16l13 -8l-13 -8"/></svg>',
		'experience' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 13a7 7 0 1 0 14 0a7 7 0 0 0 -14 0"/><path d="M14.5 10.5l-2.5 2.5"/><path d="M17 8l1 -1"/><path d="M14 3h-4"/></svg>',
	);

	return isset( $icons[ $key ] ) ? $icons[ $key ] : '';
}

/**
 * Render a sanitized teacher stat SVG icon.
 *
 * @param string $key rating|students|courses|experience.
 * @return void
 */
function webmz_render_teacher_stat_icon( $key ) {
	$svg = webmz_get_teacher_stat_icon_svg( $key );

	if ( '' === $svg ) {
		return;
	}

	$allowed = function_exists( 'webmz_nav_menu_icon_allowed_html' )
		? webmz_nav_menu_icon_allowed_html()
		: array();

	if ( isset( $allowed['svg'] ) ) {
		$allowed['svg']['stroke-width']    = true;
		$allowed['svg']['stroke-linecap']  = true;
		$allowed['svg']['stroke-linejoin'] = true;
	}

	echo wp_kses( $svg, $allowed ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Register teacher metaboxes.
 *
 * @return void
 */
function webmz_teacher_register_meta_boxes() {
	add_meta_box(
		'webmz_teacher_details',
		esc_html__( 'اطلاعات مدرس', 'tadris' ),
		'webmz_teacher_render_details_meta_box',
		WEBMZ_TEACHER_POST_TYPE,
		'normal',
		'high'
	);

	add_meta_box(
		'webmz_teacher_lists',
		esc_html__( 'تجربه و تخصص‌ها', 'tadris' ),
		'webmz_teacher_render_lists_meta_box',
		WEBMZ_TEACHER_POST_TYPE,
		'normal',
		'default'
	);

	if ( post_type_exists( 'product' ) ) {
		add_meta_box(
			'webmz_teacher_courses',
			esc_html__( 'دوره‌های مرتبط', 'tadris' ),
			'webmz_teacher_render_courses_meta_box',
			WEBMZ_TEACHER_POST_TYPE,
			'side',
			'default'
		);
	}
}
add_action( 'add_meta_boxes', 'webmz_teacher_register_meta_boxes' );

/**
 * Render teacher details metabox.
 *
 * @param WP_Post $post Teacher post.
 * @return void
 */
function webmz_teacher_render_details_meta_box( $post ) {
	wp_nonce_field( 'webmz_teacher_save_meta', 'webmz_teacher_meta_nonce' );

	$job_title         = webmz_get_teacher_job_title( $post->ID );
	$short_bio         = webmz_get_teacher_short_bio( $post->ID );
	$student_count     = absint( webmz_get_teacher_meta( $post->ID, 'student_count', 0 ) );
	$experience_years  = sanitize_text_field( (string) webmz_get_teacher_meta( $post->ID, 'experience_years', '' ) );
	$course_count      = absint( webmz_get_teacher_meta( $post->ID, 'course_count', 0 ) );
	$rating            = webmz_get_teacher_meta( $post->ID, 'rating', 0 );
	$expertise_photo   = webmz_get_teacher_expertise_photo_id( $post->ID );
	$intro_video_url   = webmz_get_teacher_intro_video_url( $post->ID );
	$teaching_experience = webmz_get_teacher_teaching_experience( $post->ID );
	$expertise_preview = $expertise_photo ? wp_get_attachment_image( $expertise_photo, 'thumbnail', false, array( 'alt' => '' ) ) : '';
	?>
	<table class="form-table webmz-teacher-meta-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row"><label for="webmz_teacher_job_title"><?php esc_html_e( 'سمت / تخصص', 'tadris' ); ?></label></th>
				<td>
					<input type="text" class="large-text" id="webmz_teacher_job_title" name="webmz_teacher_job_title" value="<?php echo esc_attr( $job_title ); ?>" placeholder="<?php esc_attr_e( 'مثال: مدرس طراحی رابط کاربری', 'tadris' ); ?>">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="webmz_teacher_short_bio"><?php esc_html_e( 'بیوگرافی کوتاه', 'tadris' ); ?></label></th>
				<td>
					<textarea class="large-text" rows="4" id="webmz_teacher_short_bio" name="webmz_teacher_short_bio" placeholder="<?php esc_attr_e( 'متن معرفی کوتاه برای بخش هیرو صفحه مدرس', 'tadris' ); ?>"><?php echo esc_textarea( $short_bio ); ?></textarea>
					<p class="description"><?php esc_html_e( 'محتوای کامل در ویرایشگر اصلی نوشته ذخیره می‌شود.', 'tadris' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="webmz_teacher_student_count"><?php esc_html_e( 'تعداد دانشجو', 'tadris' ); ?></label></th>
				<td><input type="number" min="0" class="small-text" id="webmz_teacher_student_count" name="webmz_teacher_student_count" value="<?php echo esc_attr( $student_count ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="webmz_teacher_experience_years"><?php esc_html_e( 'سال‌های تجربه', 'tadris' ); ?></label></th>
				<td><input type="text" class="regular-text" id="webmz_teacher_experience_years" name="webmz_teacher_experience_years" value="<?php echo esc_attr( $experience_years ); ?>" placeholder="<?php esc_attr_e( 'مثال: ۵ سال', 'tadris' ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="webmz_teacher_course_count"><?php esc_html_e( 'تعداد دوره', 'tadris' ); ?></label></th>
				<td>
					<input type="number" min="0" class="small-text" id="webmz_teacher_course_count" name="webmz_teacher_course_count" value="<?php echo esc_attr( $course_count ); ?>">
					<p class="description"><?php esc_html_e( 'اگر خالی باشد، از تعداد دوره‌های انتخاب‌شده محاسبه می‌شود.', 'tadris' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="webmz_teacher_rating"><?php esc_html_e( 'امتیاز', 'tadris' ); ?></label></th>
				<td><input type="number" min="0" max="5" step="0.1" class="small-text" id="webmz_teacher_rating" name="webmz_teacher_rating" value="<?php echo esc_attr( $rating ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'عکس تخصص', 'tadris' ); ?></th>
				<td>
					<div class="webmz-teacher-media-field">
						<input type="hidden" id="webmz_teacher_expertise_photo" name="webmz_teacher_expertise_photo" value="<?php echo esc_attr( $expertise_photo ); ?>">
						<div class="webmz-teacher-media-field__preview<?php echo $expertise_preview ? '' : ' is-empty'; ?>" data-webmz-teacher-media-preview="expertise">
							<?php
							if ( $expertise_preview ) {
								echo $expertise_preview; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} else {
								echo '<span class="description">' . esc_html__( 'تصویری انتخاب نشده', 'tadris' ) . '</span>';
							}
							?>
						</div>
						<p class="webmz-teacher-media-field__actions">
							<button type="button" class="button" data-webmz-teacher-media-select="expertise"><?php esc_html_e( 'انتخاب تصویر', 'tadris' ); ?></button>
							<button type="button" class="button" data-webmz-teacher-media-remove="expertise"<?php echo $expertise_photo ? '' : ' hidden'; ?>><?php esc_html_e( 'حذف', 'tadris' ); ?></button>
						</p>
						<p class="description"><?php esc_html_e( 'مثلاً پرچم کشور یا آیکون تخصص که روی کارت مدرس نمایش داده می‌شود.', 'tadris' ); ?></p>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="webmz_teacher_intro_video_url"><?php esc_html_e( 'ویدیو معرفی', 'tadris' ); ?></label></th>
				<td>
					<input type="url" class="large-text" id="webmz_teacher_intro_video_url" name="webmz_teacher_intro_video_url" value="<?php echo esc_attr( $intro_video_url ); ?>" placeholder="<?php esc_attr_e( 'https://example.com/video.mp4', 'tadris' ); ?>">
					<p class="description"><?php esc_html_e( 'لینک ویدیو معرفی مدرس. در صورت پر شدن، آیکون ویدیو روی کارت نمایش داده می‌شود.', 'tadris' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="webmz_teacher_teaching_experience"><?php esc_html_e( 'سابقه تدریس', 'tadris' ); ?></label></th>
				<td>
					<input type="text" class="regular-text" id="webmz_teacher_teaching_experience" name="webmz_teacher_teaching_experience" value="<?php echo esc_attr( $teaching_experience ); ?>" placeholder="<?php esc_attr_e( 'مثال: ۱۰ سال سابقه تدریس', 'tadris' ); ?>">
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Render one repeater row.
 *
 * @param string $name  Input name prefix.
 * @param int    $index Row index.
 * @param string $value Row value.
 * @return void
 */
function webmz_teacher_render_list_row( $name, $index, $value = '' ) {
	?>
	<div class="webmz-teacher-list-row" data-index="<?php echo esc_attr( (string) $index ); ?>">
		<input type="text" class="regular-text" name="<?php echo esc_attr( $name ); ?>[<?php echo esc_attr( (string) $index ); ?>][text]" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'متن مورد', 'tadris' ); ?>">
		<button type="button" class="button webmz-teacher-remove-row" aria-label="<?php esc_attr_e( 'حذف', 'tadris' ); ?>">&times;</button>
	</div>
	<?php
}

/**
 * Render experience and specialties metabox.
 *
 * @param WP_Post $post Teacher post.
 * @return void
 */
function webmz_teacher_render_lists_meta_box( $post ) {
	$experience  = webmz_get_teacher_list_items( $post->ID, 'experience' );
	$specialties = webmz_get_teacher_list_items( $post->ID, 'specialties' );

	if ( empty( $experience ) ) {
		$experience = array( '' );
	}
	if ( empty( $specialties ) ) {
		$specialties = array( '' );
	}
	?>
	<div class="webmz-teacher-meta-section">
		<h4><?php esc_html_e( 'تجربه کاری و آموزشی', 'tadris' ); ?></h4>
		<div id="webmz-teacher-experience-list" class="webmz-teacher-list-wrap">
			<?php foreach ( $experience as $index => $item ) : ?>
				<?php webmz_teacher_render_list_row( 'webmz_teacher_experience', (int) $index, $item ); ?>
			<?php endforeach; ?>
		</div>
		<p><button type="button" class="button button-secondary" data-webmz-teacher-add="webmz-teacher-experience-list" data-webmz-teacher-name="webmz_teacher_experience"><?php esc_html_e( 'افزودن مورد', 'tadris' ); ?></button></p>
	</div>

	<div class="webmz-teacher-meta-section">
		<h4><?php esc_html_e( 'تخصص‌ها', 'tadris' ); ?></h4>
		<div id="webmz-teacher-specialties-list" class="webmz-teacher-list-wrap">
			<?php foreach ( $specialties as $index => $item ) : ?>
				<?php webmz_teacher_render_list_row( 'webmz_teacher_specialties', (int) $index, $item ); ?>
			<?php endforeach; ?>
		</div>
		<p><button type="button" class="button button-secondary" data-webmz-teacher-add="webmz-teacher-specialties-list" data-webmz-teacher-name="webmz_teacher_specialties"><?php esc_html_e( 'افزودن مورد', 'tadris' ); ?></button></p>
	</div>

	<script type="text/html" id="tmpl-webmz-teacher-list-row">
		<div class="webmz-teacher-list-row" data-index="{{index}}">
			<input type="text" class="regular-text" name="{{name}}[{{index}}][text]" value="" placeholder="<?php echo esc_attr__( 'متن مورد', 'tadris' ); ?>">
			<button type="button" class="button webmz-teacher-remove-row" aria-label="<?php echo esc_attr__( 'حذف', 'tadris' ); ?>">&times;</button>
		</div>
	</script>
	<?php
}

/**
 * Render related courses metabox.
 *
 * @param WP_Post $post Teacher post.
 * @return void
 */
function webmz_teacher_render_courses_meta_box( $post ) {
	$selected = webmz_get_teacher_courses( $post->ID );
	$products = get_posts(
		array(
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'posts_per_page'         => 200,
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	?>
	<div class="webmz-teacher-courses-wrap">
		<?php if ( empty( $products ) ) : ?>
			<p><?php esc_html_e( 'محصولی برای انتخاب وجود ندارد.', 'tadris' ); ?></p>
		<?php else : ?>
			<?php foreach ( $products as $product_post ) : ?>
				<label class="webmz-teacher-course-option">
					<input type="checkbox" name="webmz_teacher_courses[]" value="<?php echo esc_attr( (string) $product_post->ID ); ?>" <?php checked( in_array( (int) $product_post->ID, $selected, true ) ); ?>>
					<span><?php echo esc_html( get_the_title( $product_post ) ); ?></span>
				</label>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Enqueue admin assets for teacher metaboxes.
 *
 * @param string $hook Current admin page hook.
 * @return void
 */
function webmz_teacher_enqueue_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || WEBMZ_TEACHER_POST_TYPE !== $screen->post_type ) {
		return;
	}

	wp_enqueue_style(
		'webmz-teachers-admin',
		WEBMZ_URI . 'assets/css/teachers-admin.css',
		array(),
		WEBMZ_VERSION
	);
	wp_enqueue_media();
	wp_enqueue_script(
		'webmz-teachers-admin',
		WEBMZ_URI . 'assets/js/teachers-admin.js',
		array( 'jquery' ),
		WEBMZ_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'webmz_teacher_enqueue_admin_assets' );

/**
 * Sanitize repeater list rows from POST.
 *
 * @param string $key POST array key.
 * @return array<int,array{text:string}>
 */
function webmz_teacher_sanitize_list_rows( $key ) {
	if ( empty( $_POST[ $key ] ) || ! is_array( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return array();
	}

	$rows  = array();
	$index = 0;

	foreach ( wp_unslash( $_POST[ $key ] ) as $row ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$text = '';

		if ( is_array( $row ) && isset( $row['text'] ) ) {
			$text = trim( (string) $row['text'] );
		}

		if ( '' === $text ) {
			continue;
		}

		$rows[] = array(
			'text' => sanitize_text_field( $text ),
		);
		++$index;
	}

	return $rows;
}

/**
 * Save teacher metabox fields.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function webmz_teacher_save_meta_fields( $post_id ) {
	if ( ! isset( $_POST['webmz_teacher_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_teacher_meta_nonce'] ) ), 'webmz_teacher_save_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( WEBMZ_TEACHER_POST_TYPE !== get_post_type( $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_webmz_teacher_job_title', isset( $_POST['webmz_teacher_job_title'] ) ? sanitize_text_field( wp_unslash( $_POST['webmz_teacher_job_title'] ) ) : '' );
	update_post_meta( $post_id, '_webmz_teacher_short_bio', isset( $_POST['webmz_teacher_short_bio'] ) ? sanitize_textarea_field( wp_unslash( $_POST['webmz_teacher_short_bio'] ) ) : '' );
	update_post_meta( $post_id, '_webmz_teacher_student_count', isset( $_POST['webmz_teacher_student_count'] ) ? absint( wp_unslash( $_POST['webmz_teacher_student_count'] ) ) : 0 );
	update_post_meta( $post_id, '_webmz_teacher_experience_years', isset( $_POST['webmz_teacher_experience_years'] ) ? sanitize_text_field( wp_unslash( $_POST['webmz_teacher_experience_years'] ) ) : '' );
	update_post_meta( $post_id, '_webmz_teacher_course_count', isset( $_POST['webmz_teacher_course_count'] ) ? absint( wp_unslash( $_POST['webmz_teacher_course_count'] ) ) : 0 );
	update_post_meta( $post_id, '_webmz_teacher_rating', isset( $_POST['webmz_teacher_rating'] ) ? wp_unslash( $_POST['webmz_teacher_rating'] ) : 0 );
	update_post_meta( $post_id, '_webmz_teacher_expertise_photo', isset( $_POST['webmz_teacher_expertise_photo'] ) ? absint( wp_unslash( $_POST['webmz_teacher_expertise_photo'] ) ) : 0 );
	update_post_meta( $post_id, '_webmz_teacher_intro_video_url', isset( $_POST['webmz_teacher_intro_video_url'] ) ? esc_url_raw( wp_unslash( $_POST['webmz_teacher_intro_video_url'] ) ) : '' );
	update_post_meta( $post_id, '_webmz_teacher_teaching_experience', isset( $_POST['webmz_teacher_teaching_experience'] ) ? sanitize_text_field( wp_unslash( $_POST['webmz_teacher_teaching_experience'] ) ) : '' );

	update_post_meta( $post_id, '_webmz_teacher_experience', webmz_teacher_sanitize_list_rows( 'webmz_teacher_experience' ) );
	update_post_meta( $post_id, '_webmz_teacher_specialties', webmz_teacher_sanitize_list_rows( 'webmz_teacher_specialties' ) );

	$courses = array();

	if ( ! empty( $_POST['webmz_teacher_courses'] ) && is_array( $_POST['webmz_teacher_courses'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		foreach ( wp_unslash( $_POST['webmz_teacher_courses'] ) as $course_id ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$course_id = absint( $course_id );
			if ( $course_id > 0 ) {
				$courses[] = $course_id;
			}
		}
	}

	update_post_meta( $post_id, '_webmz_teacher_courses', array_values( array_unique( $courses ) ) );
}
add_action( 'save_post', 'webmz_teacher_save_meta_fields' );

/**
 * Render a teacher archive card.
 *
 * @param int                 $post_id  Teacher post ID.
 * @param array<string,mixed> $settings Display settings.
 * @return string
 */
function webmz_render_teacher_card( $post_id, $settings = array() ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || WEBMZ_TEACHER_POST_TYPE !== get_post_type( $post_id ) ) {
		return '';
	}

	$settings = wp_parse_args(
		$settings,
		array(
			'title_tag'      => 'h3',
			'button_text'    => esc_html__( 'مشاهده پروفایل', 'tadris' ),
			'show_button'    => true,
			'show_stats'     => false,
		)
	);

	$title_tag = webmz_sanitize_heading_tag( $settings['title_tag'] );
	$job_title = webmz_get_teacher_job_title( $post_id );
	$permalink = get_permalink( $post_id );
	$stats     = webmz_get_teacher_stats( $post_id );

	ob_start();
	?>
	<article class="webmz-teacher-card">
		<a class="webmz-teacher-card__media" href="<?php echo esc_url( $permalink ); ?>">
			<?php if ( has_post_thumbnail( $post_id ) ) : ?>
				<?php echo webmz_get_post_loop_thumbnail( $post_id, array( 'class' => 'webmz-teacher-card__image' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php else : ?>
				<span class="webmz-teacher-card__placeholder" aria-hidden="true"><?php echo esc_html( mb_substr( get_the_title( $post_id ), 0, 1 ) ); ?></span>
			<?php endif; ?>
		</a>
		<div class="webmz-teacher-card__body">
			<<?php echo esc_attr( $title_tag ); ?> class="webmz-teacher-card__name">
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
			</<?php echo esc_attr( $title_tag ); ?>>
			<?php if ( $job_title ) : ?>
				<p class="webmz-teacher-card__role"><?php echo esc_html( $job_title ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $settings['show_stats'] ) ) : ?>
				<div class="webmz-teacher-card__stats">
					<?php foreach ( $stats as $stat ) : ?>
						<?php if ( $stat ) : ?>
							<span class="webmz-teacher-card__stat"><?php echo esc_html( $stat ); ?></span>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php if ( ! empty( $settings['show_button'] ) ) : ?>
			<a class="webmz-teacher-card__button" href="<?php echo esc_url( $permalink ); ?>">
				<span><?php echo esc_html( $settings['button_text'] ); ?></span>
				<span class="webmz-teacher-card__button-icon" aria-hidden="true"></span>
			</a>
		<?php endif; ?>
	</article>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render a professional teacher card for the display widget.
 *
 * @param int                 $post_id  Teacher post ID.
 * @param array<string,mixed> $settings Display settings.
 * @return string
 */
function webmz_render_professional_teacher_card( $post_id, $settings = array() ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || WEBMZ_TEACHER_POST_TYPE !== get_post_type( $post_id ) ) {
		return '';
	}

	$settings = wp_parse_args(
		$settings,
		array(
			'title_tag' => 'h3',
		)
	);

	$title_tag           = webmz_sanitize_heading_tag( $settings['title_tag'] );
	$job_title           = webmz_get_teacher_job_title( $post_id );
	$teaching_experience = webmz_get_teacher_teaching_experience( $post_id );
	$intro_video_url     = webmz_get_teacher_intro_video_url( $post_id );
	$expertise_photo_url = webmz_get_teacher_expertise_photo_url( $post_id, 'thumbnail' );
	$permalink           = get_permalink( $post_id );
	$poster_url          = get_the_post_thumbnail_url( $post_id, 'large' );

	ob_start();
	?>
	<article class="webmz-pro-teacher-card">
		<div class="webmz-pro-teacher-card__media-wrap">
			<a class="webmz-pro-teacher-card__media" href="<?php echo esc_url( $permalink ); ?>">
				<?php if ( has_post_thumbnail( $post_id ) ) : ?>
					<?php echo webmz_get_post_loop_thumbnail( $post_id, array( 'class' => 'webmz-pro-teacher-card__image' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php else : ?>
					<span class="webmz-pro-teacher-card__placeholder" aria-hidden="true"><?php echo esc_html( mb_substr( get_the_title( $post_id ), 0, 1 ) ); ?></span>
				<?php endif; ?>
			</a>

			<?php if ( $expertise_photo_url ) : ?>
				<span class="webmz-pro-teacher-card__expertise">
					<img src="<?php echo esc_url( $expertise_photo_url ); ?>" alt="" loading="lazy" width="32" height="32">
				</span>
			<?php endif; ?>

			<?php if ( $intro_video_url ) : ?>
				<button
					type="button"
					class="webmz-pro-teacher-card__video"
					data-webmz-pro-teacher-video="<?php echo esc_url( $intro_video_url ); ?>"
					data-webmz-pro-teacher-video-title="<?php echo esc_attr( get_the_title( $post_id ) ); ?>"
					<?php echo $poster_url ? 'data-webmz-pro-teacher-video-poster="' . esc_url( $poster_url ) . '"' : ''; ?>
					aria-label="<?php esc_attr_e( 'مشاهده ویدیو معرفی', 'tadris' ); ?>"
				>
					<span class="webmz-pro-teacher-card__video-icon" aria-hidden="true"></span>
				</button>
			<?php endif; ?>
		</div>

		<div class="webmz-pro-teacher-card__body">
			<<?php echo esc_attr( $title_tag ); ?> class="webmz-pro-teacher-card__name">
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
			</<?php echo esc_attr( $title_tag ); ?>>

			<?php if ( $job_title ) : ?>
				<p class="webmz-pro-teacher-card__role"><?php echo esc_html( $job_title ); ?></p>
			<?php endif; ?>

			<?php if ( $teaching_experience ) : ?>
				<p class="webmz-pro-teacher-card__experience"><?php echo esc_html( $teaching_experience ); ?></p>
			<?php endif; ?>
		</div>
	</article>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render professional teachers loop HTML.
 *
 * @param WP_Query            $query    Teachers query.
 * @param array<string,mixed> $settings Display settings.
 * @return string
 */
function webmz_professional_teachers_render_cards_html( $query, $settings = array() ) {
	if ( ! $query->have_posts() ) {
		return '<div class="webmz-pro-teachers__empty">' . esc_html__( 'مدرسی یافت نشد.', 'tadris' ) . '</div>';
	}

	$html = '';

	while ( $query->have_posts() ) {
		$query->the_post();
		$html .= webmz_render_professional_teacher_card( get_the_ID(), $settings );
	}

	wp_reset_postdata();

	return $html;
}

/**
 * Render teacher single hero section.
 *
 * @param int $post_id Teacher post ID.
 * @return string
 */
function webmz_render_teacher_single_hero( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || WEBMZ_TEACHER_POST_TYPE !== get_post_type( $post_id ) ) {
		return '';
	}

	$stats = webmz_get_teacher_stats( $post_id );

	ob_start();
	?>
	<div class="webmz-teacher-single-hero">
		<div class="webmz-teacher-single-hero__media">
			<?php
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
		<div class="webmz-teacher-single-hero__content">
			<?php webmz_render_breadcrumb_nav( 'webmz-teacher-single__breadcrumb' ); ?>
			<h1 class="webmz-teacher-single-hero__name"><?php echo esc_html( get_the_title( $post_id ) ); ?></h1>
			<?php if ( webmz_get_teacher_job_title( $post_id ) ) : ?>
				<p class="webmz-teacher-single-hero__role"><?php echo esc_html( webmz_get_teacher_job_title( $post_id ) ); ?></p>
			<?php endif; ?>
			<?php if ( webmz_get_teacher_short_bio( $post_id ) ) : ?>
				<p class="webmz-teacher-single-hero__bio"><?php echo esc_html( webmz_get_teacher_short_bio( $post_id ) ); ?></p>
			<?php endif; ?>
			<div class="webmz-teacher-single-hero__stats">
				<?php foreach ( $stats as $key => $label ) : ?>
					<?php if ( $label ) : ?>
						<div class="webmz-teacher-single-hero__stat">
							<span class="webmz-teacher-single-hero__stat-icon"><?php webmz_render_teacher_stat_icon( $key ); ?></span>
							<span class="webmz-teacher-single-hero__stat-text"><?php echo esc_html( $label ); ?></span>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render teacher experience and specialties sections.
 *
 * @param int $post_id Teacher post ID.
 * @return string
 */
function webmz_render_teacher_single_sections( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || WEBMZ_TEACHER_POST_TYPE !== get_post_type( $post_id ) ) {
		return '';
	}

	$experience  = webmz_get_teacher_list_items( $post_id, 'experience' );
	$specialties = webmz_get_teacher_list_items( $post_id, 'specialties' );

	if ( empty( $experience ) && empty( $specialties ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="webmz-teacher-single__sections">
		<?php if ( ! empty( $experience ) ) : ?>
			<div class="webmz-teacher-single__block">
				<h2 class="webmz-teacher-single__section-title"><?php esc_html_e( 'تجربه کاری و آموزشی', 'tadris' ); ?></h2>
				<ul class="webmz-teacher-single__list webmz-teacher-single__list--experience">
					<?php foreach ( $experience as $item ) : ?>
						<li class="webmz-teacher-single__list-item"><?php echo esc_html( $item ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $specialties ) ) : ?>
			<div class="webmz-teacher-single__block">
				<h2 class="webmz-teacher-single__section-title"><?php esc_html_e( 'تخصص‌ها', 'tadris' ); ?></h2>
				<ul class="webmz-teacher-single__list webmz-teacher-single__list--specialties">
					<?php foreach ( $specialties as $item ) : ?>
						<li class="webmz-teacher-single__list-item"><?php echo esc_html( $item ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render teacher related courses section.
 *
 * @param int $post_id Teacher post ID.
 * @return string
 */
function webmz_render_teacher_single_courses( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || WEBMZ_TEACHER_POST_TYPE !== get_post_type( $post_id ) || ! class_exists( 'WooCommerce' ) || ! function_exists( 'webmz_render_product_loop_card' ) ) {
		return '';
	}

	$courses = webmz_get_teacher_courses( $post_id );

	if ( empty( $courses ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="webmz-teacher-single__block">
		<h2 class="webmz-teacher-single__section-title"><?php esc_html_e( 'دوره‌های مدرس', 'tadris' ); ?></h2>
		<ul class="webmz-teacher-single__courses" style="--webmz-grid-columns: 2; --webmz-grid-tablet-columns: 2;">
			<?php foreach ( $courses as $product_id ) : ?>
				<?php
				$product = wc_get_product( $product_id );
				if ( ! $product || ! $product->is_visible() ) {
					continue;
				}
				?>
				<li class="webmz-shop-product-item">
					<?php webmz_render_product_loop_card( $product ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render teacher AJAX comments section.
 *
 * @param int                 $post_id Teacher post ID.
 * @param array<string,mixed> $args    Optional display args.
 * @return string
 */
function webmz_render_teacher_single_comments( $post_id, $args = array() ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || WEBMZ_TEACHER_POST_TYPE !== get_post_type( $post_id ) || ! function_exists( 'webmz_render_comments_widget' ) ) {
		return '';
	}

	$args = wp_parse_args(
		$args,
		array(
			'widget_title' => esc_html__( 'دیدگاه کاربران', 'tadris' ),
		)
	);

	$html = webmz_render_comments_widget(
		$post_id,
		array(
			'widget_title'  => $args['widget_title'],
			'show_count'    => true,
			'wrapper_class' => 'webmz-teacher-single__comments-widget',
		)
	);

	if ( '' === $html ) {
		return '';
	}

	return '<div class="webmz-teacher-single__block webmz-teacher-single__comments">' . $html . '</div>';
}
