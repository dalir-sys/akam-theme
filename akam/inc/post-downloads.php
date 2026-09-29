<?php
/**
 * Repeatable download files meta box for posts.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta key for post download items.
 *
 * @return string
 */
function webmz_get_post_downloads_meta_key() {
	return '_webmz_post_downloads';
}

/**
 * Get sanitized download items for a post.
 *
 * @param int $post_id Post ID.
 * @return array<int,array{name:string,size:string,extension:string,link:string}>
 */
function webmz_get_post_downloads( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
		return array();
	}

	$raw = get_post_meta( $post_id, webmz_get_post_downloads_meta_key(), true );

	if ( ! is_array( $raw ) ) {
		return array();
	}

	$items = array();

	foreach ( $raw as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$name = isset( $row['name'] ) ? trim( (string) $row['name'] ) : '';
		$link = isset( $row['link'] ) ? trim( (string) $row['link'] ) : '';

		if ( '' === $name || '' === $link ) {
			continue;
		}

		$items[] = array(
			'name'      => sanitize_text_field( $name ),
			'size'      => isset( $row['size'] ) ? sanitize_text_field( (string) $row['size'] ) : '',
			'extension' => isset( $row['extension'] ) ? sanitize_text_field( (string) $row['extension'] ) : '',
			'link'      => esc_url_raw( $link ),
		);
	}

	return $items;
}

/**
 * Register post downloads meta box.
 *
 * @return void
 */
function webmz_register_post_downloads_meta_box() {
	add_meta_box(
		'webmz_post_downloads',
		esc_html__( 'فایل‌های قابل دانلود', 'tadris' ),
		'webmz_render_post_downloads_meta_box',
		'post',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'webmz_register_post_downloads_meta_box' );

/**
 * Enqueue admin assets for the downloads meta box.
 *
 * @param string $hook Current admin page hook.
 * @return void
 */
function webmz_enqueue_post_downloads_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || 'post' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_style(
		'webmz-post-downloads-admin',
		WEBMZ_URI . 'assets/css/post-downloads-admin.css',
		array(),
		WEBMZ_VERSION
	);

	wp_enqueue_script(
		'webmz-post-downloads-admin',
		WEBMZ_URI . 'assets/js/post-downloads-admin.js',
		array( 'jquery' ),
		WEBMZ_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'webmz_enqueue_post_downloads_admin_assets' );

/**
 * Render one download row in the meta box.
 *
 * @param int                 $index Row index.
 * @param array<string,mixed> $item  Row data.
 * @return void
 */
function webmz_render_post_download_row( $index, $item = array() ) {
	$name      = isset( $item['name'] ) ? (string) $item['name'] : '';
	$size      = isset( $item['size'] ) ? (string) $item['size'] : '';
	$extension = isset( $item['extension'] ) ? (string) $item['extension'] : '';
	$link      = isset( $item['link'] ) ? (string) $item['link'] : '';
	?>
	<div class="webmz-download-row" data-webmz-download-row>
		<div class="webmz-download-row__fields">
			<p>
				<label><?php esc_html_e( 'نام', 'tadris' ); ?></label>
				<input type="text" class="widefat" name="webmz_post_downloads[<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'مثال: فایل ویدیوی آموزشی این دوره', 'tadris' ); ?>">
			</p>
			<p>
				<label><?php esc_html_e( 'حجم', 'tadris' ); ?></label>
				<input type="text" class="widefat" name="webmz_post_downloads[<?php echo esc_attr( $index ); ?>][size]" value="<?php echo esc_attr( $size ); ?>" placeholder="<?php esc_attr_e( 'مثال: 260 مگابایت', 'tadris' ); ?>">
			</p>
			<p>
				<label><?php esc_html_e( 'پسوند فایل', 'tadris' ); ?></label>
				<input type="text" class="widefat" name="webmz_post_downloads[<?php echo esc_attr( $index ); ?>][extension]" value="<?php echo esc_attr( $extension ); ?>" placeholder="<?php esc_attr_e( 'مثال: MKV', 'tadris' ); ?>">
			</p>
			<p>
				<label><?php esc_html_e( 'لینک دانلود', 'tadris' ); ?></label>
				<input type="url" class="widefat ltr" name="webmz_post_downloads[<?php echo esc_attr( $index ); ?>][link]" value="<?php echo esc_attr( $link ); ?>" placeholder="https://example.com/file.zip">
			</p>
		</div>
		<button type="button" class="button-link-delete webmz-download-row__remove" data-webmz-download-remove aria-label="<?php esc_attr_e( 'حذف ردیف', 'tadris' ); ?>"><?php esc_html_e( 'حذف', 'tadris' ); ?></button>
	</div>
	<?php
}

/**
 * Render post downloads meta box.
 *
 * @param WP_Post $post Current post.
 * @return void
 */
function webmz_render_post_downloads_meta_box( $post ) {
	wp_nonce_field( 'webmz_save_post_downloads', 'webmz_post_downloads_nonce' );

	$items = get_post_meta( $post->ID, webmz_get_post_downloads_meta_key(), true );
	$items = is_array( $items ) ? $items : array();

	if ( empty( $items ) ) {
		$items = array(
			array(
				'name'      => '',
				'size'      => '',
				'extension' => '',
				'link'      => '',
			),
		);
	}
	?>
	<div class="webmz-download-metabox" data-webmz-download-metabox>
		<p class="description">
			<?php esc_html_e( 'فایل‌های قابل دانلود این نوشته را اضافه کنید. اگر حداقل یک مورد معتبر (نام + لینک) ذخیره شود، ویجت «باکس دانلود» در قالب‌ساز نمایش داده می‌شود.', 'tadris' ); ?>
			<?php esc_html_e( 'اگر لینک یک ردیف، لینک ویدیو در آپارات یا یوتیوب باشد، دکمه آن ردیف به «مشاهده آنلاین» تبدیل می‌شود و ویدیو در همان صفحه پخش می‌شود.', 'tadris' ); ?>
		</p>

		<div class="webmz-download-rows" data-webmz-download-rows>
			<?php foreach ( $items as $index => $item ) : ?>
				<?php webmz_render_post_download_row( (int) $index, is_array( $item ) ? $item : array() ); ?>
			<?php endforeach; ?>
		</div>

		<p>
			<button type="button" class="button button-secondary" data-webmz-download-add><?php esc_html_e( 'افزودن فایل', 'tadris' ); ?></button>
		</p>

		<script type="text/html" id="tmpl-webmz-download-row">
			<?php webmz_render_post_download_row( '__INDEX__', array() ); ?>
		</script>
	</div>
	<?php
}

/**
 * Save post downloads meta box.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return void
 */
function webmz_save_post_downloads_meta_box( $post_id, $post ) {
	if ( ! isset( $_POST['webmz_post_downloads_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_post_downloads_nonce'] ) ), 'webmz_save_post_downloads' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( 'post' !== $post->post_type ) {
		return;
	}

	$raw   = isset( $_POST['webmz_post_downloads'] ) && is_array( $_POST['webmz_post_downloads'] ) ? wp_unslash( $_POST['webmz_post_downloads'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$items = array();

	foreach ( $raw as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$name = isset( $row['name'] ) ? sanitize_text_field( (string) $row['name'] ) : '';
		$link = isset( $row['link'] ) ? esc_url_raw( (string) $row['link'] ) : '';

		if ( '' === $name || '' === $link ) {
			continue;
		}

		$items[] = array(
			'name'      => $name,
			'size'      => isset( $row['size'] ) ? sanitize_text_field( (string) $row['size'] ) : '',
			'extension' => isset( $row['extension'] ) ? sanitize_text_field( (string) $row['extension'] ) : '',
			'link'      => $link,
		);
	}

	if ( ! empty( $items ) ) {
		update_post_meta( $post_id, webmz_get_post_downloads_meta_key(), $items );
	} else {
		delete_post_meta( $post_id, webmz_get_post_downloads_meta_key() );
	}
}
add_action( 'save_post', 'webmz_save_post_downloads_meta_box', 10, 2 );
