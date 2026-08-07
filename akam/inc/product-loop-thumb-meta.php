<?php
/**
 * Product loop thumbnail metabox for file-loop widget.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register loop thumbnail metabox on WooCommerce products.
 *
 * @return void
 */
function webmz_pll_register_meta_boxes() {
	if ( ! post_type_exists( 'product' ) ) {
		return;
	}

	add_meta_box(
		'webmz_pll_product_meta',
		esc_html__( 'تامنیل لوپ فایل‌ها', 'tadris' ),
		'webmz_pll_render_product_meta_box',
		'product',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'webmz_pll_register_meta_boxes' );

/**
 * Render loop thumbnail metabox fields.
 *
 * @param WP_Post $post Product post.
 * @return void
 */
function webmz_pll_render_product_meta_box( $post ) {
	wp_nonce_field( 'webmz_pll_save_meta', 'webmz_pll_meta_nonce' );

	$thumb_id   = absint( get_post_meta( $post->ID, '_webmz_product_loop_thumb', true ) );
	$badge_text = get_post_meta( $post->ID, '_webmz_product_loop_badge', true );
	$thumb_url  = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '';
	?>
	<div class="webmz-pll-meta">
		<p>
			<label for="webmz_product_loop_thumb"><strong><?php esc_html_e( 'تصویر کوچک (آیکون)', 'tadris' ); ?></strong></label>
		</p>
		<p class="description"><?php esc_html_e( 'در ویجت لوپ فایل‌ها به‌جای تصویر شاخص نمایش داده می‌شود.', 'tadris' ); ?></p>

		<input type="hidden" id="webmz_product_loop_thumb" name="webmz_product_loop_thumb" value="<?php echo esc_attr( $thumb_id ); ?>">

		<div class="webmz-pll-meta__preview<?php echo $thumb_url ? '' : ' is-empty'; ?>" data-webmz-pll-media-preview="thumb">
			<?php if ( $thumb_url ) : ?>
				<img src="<?php echo esc_url( $thumb_url ); ?>" alt="">
			<?php else : ?>
				<span class="description"><?php esc_html_e( 'تصویری انتخاب نشده', 'tadris' ); ?></span>
			<?php endif; ?>
		</div>

		<p class="webmz-pll-meta__actions">
			<button type="button" class="button" data-webmz-pll-media-select="thumb"><?php esc_html_e( 'انتخاب تصویر', 'tadris' ); ?></button>
			<button type="button" class="button-link-delete" data-webmz-pll-media-remove="thumb" <?php echo $thumb_url ? '' : 'hidden'; ?>><?php esc_html_e( 'حذف', 'tadris' ); ?></button>
		</p>

		<hr>

		<p>
			<label for="webmz_product_loop_badge"><strong><?php esc_html_e( 'متن متا (پاپ‌آپ هاور)', 'tadris' ); ?></strong></label><br>
			<input class="widefat" type="text" id="webmz_product_loop_badge" name="webmz_product_loop_badge" value="<?php echo esc_attr( $badge_text ); ?>" placeholder="<?php esc_attr_e( 'مثال: محصول ویژه ژاکت', 'tadris' ); ?>">
		</p>
	</div>
	<?php
}

/**
 * Enqueue admin assets for loop thumbnail metabox.
 *
 * @param string $hook Current admin page hook.
 * @return void
 */
function webmz_pll_enqueue_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || 'product' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_style(
		'webmz-pll-admin',
		WEBMZ_URI . 'assets/css/product-loop-thumb-admin.css',
		array(),
		WEBMZ_VERSION
	);
	wp_enqueue_media();
	wp_enqueue_script(
		'webmz-pll-admin',
		WEBMZ_URI . 'assets/js/product-loop-thumb-admin.js',
		array( 'jquery' ),
		WEBMZ_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'webmz_pll_enqueue_admin_assets' );

/**
 * Save loop thumbnail metabox fields.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function webmz_pll_save_meta_fields( $post_id ) {
	if ( ! isset( $_POST['webmz_pll_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_pll_meta_nonce'] ) ), 'webmz_pll_save_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$thumb_id = isset( $_POST['webmz_product_loop_thumb'] ) ? absint( $_POST['webmz_product_loop_thumb'] ) : 0;

	if ( $thumb_id && ! wp_attachment_is_image( $thumb_id ) ) {
		$thumb_id = 0;
	}

	update_post_meta( $post_id, '_webmz_product_loop_thumb', $thumb_id );

	$badge_text = isset( $_POST['webmz_product_loop_badge'] ) ? sanitize_text_field( wp_unslash( $_POST['webmz_product_loop_badge'] ) ) : '';
	update_post_meta( $post_id, '_webmz_product_loop_badge', $badge_text );
}
add_action( 'save_post_product', 'webmz_pll_save_meta_fields' );

/**
 * Get loop thumbnail attachment ID for a product.
 *
 * @param int $product_id Product ID.
 * @return int
 */
function webmz_pll_get_product_thumb_id( $product_id ) {
	return absint( get_post_meta( $product_id, '_webmz_product_loop_thumb', true ) );
}

/**
 * Get loop thumbnail URL for a product.
 *
 * @param int    $product_id Product ID.
 * @param string $size       Image size.
 * @return string
 */
function webmz_pll_get_product_thumb_url( $product_id, $size = 'thumbnail' ) {
	$thumb_id = webmz_pll_get_product_thumb_id( $product_id );

	if ( $thumb_id ) {
		$url = wp_get_attachment_image_url( $thumb_id, $size );
		if ( $url ) {
			return $url;
		}
	}

	$fallback = get_the_post_thumbnail_url( $product_id, $size );

	return $fallback ? $fallback : '';
}

/**
 * Get badge text for hover popup.
 *
 * @param int    $product_id Product ID.
 * @param string $fallback   Fallback text when meta is empty.
 * @return string
 */
function webmz_pll_get_product_badge_text( $product_id, $fallback = '' ) {
	$badge = get_post_meta( $product_id, '_webmz_product_loop_badge', true );

	if ( is_string( $badge ) && '' !== trim( $badge ) ) {
		return trim( $badge );
	}

	return $fallback;
}
