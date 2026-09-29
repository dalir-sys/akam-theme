<?php
/**
 * Course curriculum metabox and helpers for Webmasters single product widgets.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register curriculum metabox on WooCommerce products.
 *
 * @return void
 */
function webmz_spw_register_curriculum_meta_box() {
	if ( ! post_type_exists( 'product' ) ) {
		return;
	}

	add_meta_box(
		'webmz_spw_curriculum_meta',
		esc_html__( 'سرفصل‌های دوره', 'tadris' ),
		'webmz_spw_render_curriculum_meta_box',
		'product',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'webmz_spw_register_curriculum_meta_box' );

/**
 * Render curriculum metabox with nested section/lesson repeaters.
 *
 * @param WP_Post $post Product post.
 * @return void
 */
function webmz_spw_render_curriculum_meta_box( $post ) {
	wp_nonce_field( 'webmz_spw_save_curriculum', 'webmz_spw_curriculum_nonce' );

	$sections = webmz_spw_get_product_curriculum( $post->ID );

	if ( empty( $sections ) ) {
		$sections = array(
			array(
				'title'       => '',
				'description' => '',
				'lessons'     => array(
					array(
						'title'     => '',
						'duration'  => '',
						'is_free'   => '0',
						'video_url' => '',
					),
				),
			),
		);
	}
	?>
	<p class="description">
		<?php esc_html_e( 'سرفصل‌ها و جلسات در ویجت «سرفصل‌های دوره» صفحه سینگل نمایش داده می‌شوند. تعداد جلسات و مدت زمان هر سرفصل به‌صورت خودکار محاسبه می‌شود.', 'tadris' ); ?>
	</p>

	<div id="webmz-spw-curriculum-repeater">
		<?php foreach ( $sections as $section_index => $section ) : ?>
			<?php webmz_spw_render_curriculum_section_row( $section_index, $section ); ?>
		<?php endforeach; ?>
	</div>

	<p>
		<button type="button" class="button button-secondary" id="webmz-spw-curriculum-add-section">
			<?php esc_html_e( 'افزودن سرفصل', 'tadris' ); ?>
		</button>
	</p>

	<script type="text/html" id="tmpl-webmz-spw-curriculum-section">
		<?php webmz_spw_render_curriculum_section_row( '{{sectionIndex}}', array( 'title' => '', 'description' => '', 'lessons' => array( array( 'title' => '', 'duration' => '', 'is_free' => '0', 'video_url' => '' ) ) ), true ); ?>
	</script>

	<script type="text/html" id="tmpl-webmz-spw-curriculum-lesson">
		<?php webmz_spw_render_curriculum_lesson_row( '{{sectionIndex}}', '{{lessonIndex}}', array( 'title' => '', 'duration' => '', 'is_free' => '0', 'video_url' => '' ), true ); ?>
	</script>

	<script>
	(function () {
		var wrap = document.getElementById('webmz-spw-curriculum-repeater');
		var addSectionBtn = document.getElementById('webmz-spw-curriculum-add-section');
		var sectionTemplate = document.getElementById('tmpl-webmz-spw-curriculum-section');
		var lessonTemplate = document.getElementById('tmpl-webmz-spw-curriculum-lesson');

		if (!wrap || !addSectionBtn || !sectionTemplate || !lessonTemplate) {
			return;
		}

		function nextSectionIndex() {
			return wrap.querySelectorAll('.webmz-spw-curriculum-section').length;
		}

		function renderTemplate(template, replacements) {
			var html = template.innerHTML;
			Object.keys(replacements).forEach(function (key) {
				html = html.split('{{' + key + '}}').join(String(replacements[key]));
			});
			return html;
		}

		addSectionBtn.addEventListener('click', function () {
			var index = nextSectionIndex();
			var block = document.createElement('div');
			block.innerHTML = renderTemplate(sectionTemplate, { sectionIndex: index });
			wrap.appendChild(block.firstElementChild);
		});

		wrap.addEventListener('click', function (event) {
			var target = event.target;

			if (target.classList.contains('webmz-spw-curriculum-add-lesson')) {
				event.preventDefault();
				var section = target.closest('.webmz-spw-curriculum-section');
				if (!section) {
					return;
				}
				var sectionIndex = section.getAttribute('data-section-index');
				var lessonsWrap = section.querySelector('.webmz-spw-curriculum-lessons');
				var lessonIndex = lessonsWrap.querySelectorAll('.webmz-spw-curriculum-lesson').length;
				var block = document.createElement('div');
				block.innerHTML = renderTemplate(lessonTemplate, {
					sectionIndex: sectionIndex,
					lessonIndex: lessonIndex
				});
				lessonsWrap.appendChild(block.firstElementChild);
				return;
			}

			if (target.classList.contains('webmz-spw-curriculum-remove-lesson')) {
				event.preventDefault();
				var lessonSection = target.closest('.webmz-spw-curriculum-section');
				var lessons = lessonSection ? lessonSection.querySelectorAll('.webmz-spw-curriculum-lesson') : [];
				if (lessons.length <= 1) {
					lessons[0].querySelectorAll('input:not([type="checkbox"])').forEach(function (field) {
						field.value = '';
					});
					var freeCheckbox = lessons[0].querySelector('input[type="checkbox"]');
					if (freeCheckbox) {
						freeCheckbox.checked = false;
					}
					return;
				}
				target.closest('.webmz-spw-curriculum-lesson').remove();
				return;
			}

			if (target.classList.contains('webmz-spw-curriculum-remove-section')) {
				event.preventDefault();
				var sections = wrap.querySelectorAll('.webmz-spw-curriculum-section');
				if (sections.length <= 1) {
					sections[0].querySelectorAll('input:not([type="checkbox"])').forEach(function (field) {
						field.value = '';
					});
					sections[0].querySelectorAll('input[type="checkbox"]').forEach(function (field) {
						field.checked = false;
					});
					return;
				}
				target.closest('.webmz-spw-curriculum-section').remove();
			}
		});
	}());
	</script>
	<?php
}

/**
 * Render one curriculum section row in admin.
 *
 * @param int|string              $section_index Section index.
 * @param array<string,mixed>     $section       Section data.
 * @param bool                    $template      Whether rendering for JS template.
 * @return void
 */
function webmz_spw_render_curriculum_section_row( $section_index, $section, $template = false ) {
	$title       = $section['title'] ?? '';
	$description = $section['description'] ?? '';
	$lessons     = isset( $section['lessons'] ) && is_array( $section['lessons'] ) ? $section['lessons'] : array();

	if ( empty( $lessons ) ) {
		$lessons = array(
			array(
				'title'     => '',
				'duration'  => '',
				'is_free'   => '0',
				'video_url' => '',
			),
		);
	}

	$name_prefix = $template ? 'webmz_course_curriculum[{{sectionIndex}}]' : 'webmz_course_curriculum[' . esc_attr( $section_index ) . ']';
	$data_index  = $template ? '{{sectionIndex}}' : $section_index;
	?>
	<div class="webmz-spw-curriculum-section" data-section-index="<?php echo esc_attr( (string) $data_index ); ?>" style="border:1px solid #ccd0d4;padding:16px;margin-bottom:16px;background:#f9f9f9;border-radius:4px;">
		<p style="margin-top:0;">
			<strong><?php esc_html_e( 'سرفصل', 'tadris' ); ?></strong>
			<button type="button" class="button-link-delete webmz-spw-curriculum-remove-section" style="float:left;color:#b32d2e;">
				<?php esc_html_e( 'حذف سرفصل', 'tadris' ); ?>
			</button>
		</p>
		<p>
			<label><strong><?php esc_html_e( 'نام سرفصل', 'tadris' ); ?></strong></label><br>
			<input class="widefat" type="text" name="<?php echo esc_attr( $name_prefix ); ?>[title]" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label><strong><?php esc_html_e( 'توضیح کوتاه سرفصل', 'tadris' ); ?></strong></label><br>
			<input class="widefat" type="text" name="<?php echo esc_attr( $name_prefix ); ?>[description]" value="<?php echo esc_attr( $description ); ?>">
		</p>

		<p><strong><?php esc_html_e( 'جلسات', 'tadris' ); ?></strong></p>
		<div class="webmz-spw-curriculum-lessons">
			<?php foreach ( $lessons as $lesson_index => $lesson ) : ?>
				<?php webmz_spw_render_curriculum_lesson_row( $data_index, $lesson_index, $lesson, $template ); ?>
			<?php endforeach; ?>
		</div>
		<p>
			<button type="button" class="button button-secondary webmz-spw-curriculum-add-lesson">
				<?php esc_html_e( 'افزودن جلسه', 'tadris' ); ?>
			</button>
		</p>
	</div>
	<?php
}

/**
 * Render one curriculum lesson row in admin.
 *
 * @param int|string          $section_index Section index.
 * @param int|string          $lesson_index  Lesson index.
 * @param array<string,mixed> $lesson        Lesson data.
 * @param bool                $template      Whether rendering for JS template.
 * @return void
 */
function webmz_spw_render_curriculum_lesson_row( $section_index, $lesson_index, $lesson, $template = false ) {
	$title     = $lesson['title'] ?? '';
	$duration  = $lesson['duration'] ?? '';
	$is_free   = ! empty( $lesson['is_free'] ) && '0' !== (string) $lesson['is_free'];
	$video_url = $lesson['video_url'] ?? '';

	if ( $template ) {
		$name_prefix = 'webmz_course_curriculum[{{sectionIndex}}][lessons][{{lessonIndex}}]';
	} else {
		$name_prefix = 'webmz_course_curriculum[' . esc_attr( $section_index ) . '][lessons][' . esc_attr( $lesson_index ) . ']';
	}
	?>
	<div class="webmz-spw-curriculum-lesson" style="border:1px solid #ddd;padding:12px;margin-bottom:10px;background:#fff;border-radius:4px;">
		<p style="margin-top:0;">
			<strong><?php esc_html_e( 'جلسه', 'tadris' ); ?></strong>
			<button type="button" class="button-link-delete webmz-spw-curriculum-remove-lesson" style="float:left;color:#b32d2e;">
				<?php esc_html_e( 'حذف', 'tadris' ); ?>
			</button>
		</p>
		<p>
			<label><strong><?php esc_html_e( 'نام جلسه', 'tadris' ); ?></strong></label><br>
			<input class="widefat" type="text" name="<?php echo esc_attr( $name_prefix ); ?>[title]" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label><strong><?php esc_html_e( 'مدت زمان (HH:MM:SS)', 'tadris' ); ?></strong></label><br>
			<input class="widefat" type="text" name="<?php echo esc_attr( $name_prefix ); ?>[duration]" value="<?php echo esc_attr( $duration ); ?>" placeholder="00:05:36">
		</p>
		<p>
			<label>
				<input type="checkbox" name="<?php echo esc_attr( $name_prefix ); ?>[is_free]" value="1"<?php checked( $is_free ); ?>>
				<strong><?php esc_html_e( 'جلسه رایگان (قابل مشاهده قبل از خرید)', 'tadris' ); ?></strong>
			</label>
		</p>
		<p>
			<label><strong><?php esc_html_e( 'لینک مشاهده جلسه', 'tadris' ); ?></strong></label><br>
			<input class="widefat" type="url" dir="ltr" name="<?php echo esc_attr( $name_prefix ); ?>[video_url]" value="<?php echo esc_attr( $video_url ); ?>" placeholder="<?php echo esc_attr( webmz_video_field_placeholder() ); ?>">
			<span class="description"><?php echo esc_html( webmz_video_field_help() ); ?> <?php esc_html_e( 'لینک‌های ویدیویی در پلیر همین صفحه پخش می‌شوند و سایر لینک‌ها در تب جدید باز می‌شوند.', 'tadris' ); ?></span>
		</p>
	</div>
	<?php
}

/**
 * Save curriculum metabox fields.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return void
 */
function webmz_spw_save_curriculum_meta_fields( $post_id, $post ) {
	if ( ! isset( $_POST['webmz_spw_curriculum_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_spw_curriculum_nonce'] ) ), 'webmz_spw_save_curriculum' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) || 'product' !== $post->post_type ) {
		return;
	}

	$sections = array();

	if ( isset( $_POST['webmz_course_curriculum'] ) && is_array( $_POST['webmz_course_curriculum'] ) ) {
		foreach ( wp_unslash( $_POST['webmz_course_curriculum'] ) as $section ) {
			if ( ! is_array( $section ) ) {
				continue;
			}

			$title       = isset( $section['title'] ) ? sanitize_text_field( $section['title'] ) : '';
			$description = isset( $section['description'] ) ? sanitize_text_field( $section['description'] ) : '';
			$lessons     = array();

			if ( isset( $section['lessons'] ) && is_array( $section['lessons'] ) ) {
				foreach ( $section['lessons'] as $lesson ) {
					if ( ! is_array( $lesson ) ) {
						continue;
					}

					$lesson_title = isset( $lesson['title'] ) ? sanitize_text_field( $lesson['title'] ) : '';
					$duration     = isset( $lesson['duration'] ) ? webmz_spw_sanitize_duration( $lesson['duration'] ) : '';
					$is_free      = ! empty( $lesson['is_free'] ) ? '1' : '0';
					$video_url    = isset( $lesson['video_url'] ) ? esc_url_raw( $lesson['video_url'] ) : '';

					if ( '' === trim( $lesson_title ) && '' === trim( $duration ) && '' === trim( $video_url ) ) {
						continue;
					}

					$lessons[] = array(
						'title'     => $lesson_title,
						'duration'  => $duration,
						'is_free'   => $is_free,
						'video_url' => $video_url,
					);
				}
			}

			if ( '' === trim( $title ) && '' === trim( $description ) && empty( $lessons ) ) {
				continue;
			}

			$sections[] = array(
				'title'       => $title,
				'description' => $description,
				'lessons'     => $lessons,
			);
		}
	}

	update_post_meta( $post_id, '_webmz_course_curriculum', $sections );
}
add_action( 'save_post', 'webmz_spw_save_curriculum_meta_fields', 10, 2 );

/**
 * Sanitize duration string to HH:MM:SS.
 *
 * @param string $duration Raw duration.
 * @return string
 */
function webmz_spw_sanitize_duration( $duration ) {
	$duration = trim( (string) $duration );

	if ( '' === $duration ) {
		return '';
	}

	$english = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
	$persian = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
	$arabic  = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );
	$duration = str_replace( $persian, $english, $duration );
	$duration = str_replace( $arabic, $english, $duration );
	$duration = preg_replace( '/[^\d:]/', '', $duration );

	if ( ! is_string( $duration ) || ! preg_match( '/^(\d{1,2}:)?\d{1,2}:\d{2}$/', $duration ) ) {
		return '';
	}

	$parts = array_map( 'intval', explode( ':', $duration ) );

	if ( 2 === count( $parts ) ) {
		array_unshift( $parts, 0 );
	}

	if ( 3 !== count( $parts ) ) {
		return '';
	}

	list( $hours, $minutes, $seconds ) = $parts;

	if ( $minutes > 59 || $seconds > 59 ) {
		return '';
	}

	return sprintf( '%02d:%02d:%02d', $hours, $minutes, $seconds );
}

/**
 * Parse duration string to total seconds.
 *
 * @param string $duration Duration in HH:MM:SS or MM:SS format.
 * @return int
 */
function webmz_spw_duration_to_seconds( $duration ) {
	$duration = webmz_spw_sanitize_duration( $duration );

	if ( '' === $duration ) {
		return 0;
	}

	$parts = array_map( 'intval', explode( ':', $duration ) );

	return ( $parts[0] * 3600 ) + ( $parts[1] * 60 ) + $parts[2];
}

/**
 * Format seconds as HH:MM:SS with optional Persian digits.
 *
 * @param int  $seconds        Total seconds.
 * @param bool $persian_digits Whether to convert digits to Persian.
 * @return string
 */
function webmz_spw_format_duration( $seconds, $persian_digits = true ) {
	$seconds = max( 0, absint( $seconds ) );
	$hours   = (int) floor( $seconds / 3600 );
	$minutes = (int) floor( ( $seconds % 3600 ) / 60 );
	$secs    = $seconds % 60;
	$output  = sprintf( '%02d:%02d:%02d', $hours, $minutes, $secs );

	if ( $persian_digits && function_exists( 'webmz_to_persian_digits' ) ) {
		return webmz_to_persian_digits( $output );
	}

	return $output;
}

/**
 * Get normalized curriculum sections for a product.
 *
 * @param int $product_id Product ID.
 * @return array<int,array{title:string,description:string,lessons:array<int,array{title:string,duration:string,is_free:string,video_url:string}>>>
 */
function webmz_spw_get_product_curriculum( $product_id ) {
	$product_id = absint( $product_id );
	$sections   = get_post_meta( $product_id, '_webmz_course_curriculum', true );

	if ( ! is_array( $sections ) ) {
		return array();
	}

	$normalized = array();

	foreach ( $sections as $section ) {
		if ( ! is_array( $section ) ) {
			continue;
		}

		$title       = isset( $section['title'] ) ? (string) $section['title'] : '';
		$description = isset( $section['description'] ) ? (string) $section['description'] : '';
		$lessons     = array();

		if ( isset( $section['lessons'] ) && is_array( $section['lessons'] ) ) {
			foreach ( $section['lessons'] as $lesson ) {
				if ( ! is_array( $lesson ) ) {
					continue;
				}

				$lesson_title = isset( $lesson['title'] ) ? (string) $lesson['title'] : '';
				$duration     = isset( $lesson['duration'] ) ? webmz_spw_sanitize_duration( $lesson['duration'] ) : '';
				$is_free      = ! empty( $lesson['is_free'] ) && '0' !== (string) $lesson['is_free'] ? '1' : '0';
				$video_url    = isset( $lesson['video_url'] ) ? esc_url_raw( (string) $lesson['video_url'] ) : '';

				if ( '' === trim( $lesson_title ) && '' === trim( $duration ) && '' === trim( $video_url ) ) {
					continue;
				}

				$lessons[] = array(
					'title'     => $lesson_title,
					'duration'  => $duration,
					'is_free'   => $is_free,
					'video_url' => $video_url,
				);
			}
		}

		if ( '' === trim( $title ) && '' === trim( $description ) && empty( $lessons ) ) {
			continue;
		}

		$normalized[] = array(
			'title'       => $title,
			'description' => $description,
			'lessons'     => $lessons,
		);
	}

	return $normalized;
}

/**
 * Calculate section stats (lesson count and total duration).
 *
 * @param array<string,mixed> $section Section data.
 * @return array{count:int,duration_seconds:int,duration_label:string}
 */
function webmz_spw_get_curriculum_section_stats( $section ) {
	$lessons = isset( $section['lessons'] ) && is_array( $section['lessons'] ) ? $section['lessons'] : array();
	$seconds = 0;

	foreach ( $lessons as $lesson ) {
		$seconds += webmz_spw_duration_to_seconds( $lesson['duration'] ?? '' );
	}

	return array(
		'count'            => count( $lessons ),
		'duration_seconds' => $seconds,
		'duration_label'   => webmz_spw_format_duration( $seconds ),
	);
}

/**
 * Calculate total lesson count and duration for a product curriculum.
 *
 * @param int $product_id Product ID.
 * @return array{count:int,duration_seconds:int,duration_label:string}
 */
function webmz_spw_get_product_curriculum_stats( $product_id ) {
	$product_id = absint( $product_id );
	$sections   = webmz_spw_get_product_curriculum( $product_id );
	$count      = 0;
	$seconds    = 0;

	foreach ( $sections as $section ) {
		$stats    = webmz_spw_get_curriculum_section_stats( $section );
		$count   += (int) $stats['count'];
		$seconds += (int) $stats['duration_seconds'];
	}

	return array(
		'count'            => $count,
		'duration_seconds' => $seconds,
		'duration_label'   => webmz_spw_format_duration( $seconds ),
	);
}

/**
 * Check whether the current or given user purchased a product.
 *
 * @param int      $product_id Product ID.
 * @param int|null $user_id    Optional user ID.
 * @return bool
 */
function webmz_spw_user_has_purchased_product( $product_id, $user_id = null ) {
	$product_id = absint( $product_id );

	if ( ! $product_id || ! function_exists( 'wc_customer_bought_product' ) ) {
		return false;
	}

	if ( null === $user_id ) {
		$user_id = get_current_user_id();
	}

	$user_id = absint( $user_id );

	if ( ! $user_id ) {
		return false;
	}

	$user = get_userdata( $user_id );

	if ( ! $user || empty( $user->user_email ) ) {
		return false;
	}

	return (bool) wc_customer_bought_product( $user->user_email, $user_id, $product_id );
}

/**
 * Determine whether a lesson should show the watch action.
 *
 * @param array<string,mixed> $lesson        Lesson data.
 * @param int                 $product_id    Product ID.
 * @param bool                $display_only  Widget display-only mode.
 * @param int|null            $user_id       Optional user ID.
 * @return bool
 */
function webmz_spw_lesson_can_watch( $lesson, $product_id, $display_only = false, $user_id = null ) {
	if ( empty( $lesson['video_url'] ) ) {
		return false;
	}

	if ( ! empty( $lesson['is_free'] ) && '0' !== (string) $lesson['is_free'] ) {
		return true;
	}

	if ( $display_only ) {
		return false;
	}

	return webmz_spw_user_has_purchased_product( $product_id, $user_id );
}
