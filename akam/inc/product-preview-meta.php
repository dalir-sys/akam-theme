<?php
/**
 * Product preview URL metabox for WooCommerce products.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register preview URL metabox on WooCommerce products.
 *
 * @return void
 */
function webmz_product_preview_register_meta_boxes() {
	if ( ! post_type_exists( 'product' ) ) {
		return;
	}

	add_meta_box(
		'webmz_product_preview_meta',
		esc_html__( 'پیش‌نمایش محصول', 'tadris' ),
		'webmz_product_preview_render_meta_box',
		'product',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'webmz_product_preview_register_meta_boxes' );

/**
 * Render preview URL metabox fields.
 *
 * @param WP_Post $post Product post.
 * @return void
 */
function webmz_product_preview_render_meta_box( $post ) {
	wp_nonce_field( 'webmz_product_preview_save_meta', 'webmz_product_preview_meta_nonce' );

	$preview_url = get_post_meta( $post->ID, '_webmz_product_preview_url', true );
	?>
	<div class="webmz-product-preview-meta">
		<p class="description">
			<?php esc_html_e( 'آدرس دمو یا پیش‌نمایش زنده محصول. در ویجت «لوپ محصولات ژاکت» برای دکمه پیش‌نمایش استفاده می‌شود.', 'tadris' ); ?>
		</p>
		<p>
			<label for="webmz_product_preview_url"><strong><?php esc_html_e( 'لینک پیش‌نمایش', 'tadris' ); ?></strong></label>
			<input
				class="widefat"
				type="url"
				id="webmz_product_preview_url"
				name="webmz_product_preview_url"
				value="<?php echo esc_attr( $preview_url ); ?>"
				placeholder="https://"
			>
		</p>
	</div>
	<?php
}

/**
 * Save preview URL metabox fields.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function webmz_product_preview_save_meta_fields( $post_id ) {
	if ( ! isset( $_POST['webmz_product_preview_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_product_preview_meta_nonce'] ) ), 'webmz_product_preview_save_meta' ) ) {
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

	$preview_url = isset( $_POST['webmz_product_preview_url'] ) ? esc_url_raw( wp_unslash( $_POST['webmz_product_preview_url'] ) ) : '';
	update_post_meta( $post_id, '_webmz_product_preview_url', $preview_url );
}
add_action( 'save_post_product', 'webmz_product_preview_save_meta_fields' );

/**
 * Get product preview URL.
 *
 * @param int $product_id Product ID.
 * @return string
 */
function webmz_get_product_preview_url( $product_id ) {
	$url = get_post_meta( absint( $product_id ), '_webmz_product_preview_url', true );

	return is_string( $url ) ? esc_url( $url ) : '';
}
