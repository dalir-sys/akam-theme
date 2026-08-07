<?php
/**
 * AJAX handlers for file-loop widget hover popup.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render hover popup HTML for a product.
 *
 * @param \WC_Product $product      Product object.
 * @param string      $badge_fallback Fallback badge text.
 * @return string
 */
function webmz_file_loop_render_hover_popup_html( $product, $badge_fallback = '' ) {
	if ( ! $product ) {
		return '';
	}

	$product_id = $product->get_id();
	$image_id   = $product->get_image_id();
	$image_html = '';

	if ( $image_id ) {
		$image_html = webmz_get_attachment_loop_image(
			$image_id,
			array(
				'class'    => 'webmz-fl__popup-img',
				'alt'      => $product->get_name(),
				'loading'  => 'eager',
			)
		);
	} else {
		$placeholder = function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( 'medium' ) : '';
		if ( $placeholder ) {
			$image_html = sprintf(
				'<img class="webmz-fl__popup-img" src="%s" alt="%s">',
				esc_url( $placeholder ),
				esc_attr( $product->get_name() )
			);
		}
	}

	$price_html = '';
	if ( function_exists( 'webmz_get_product_loop_price_data' ) ) {
		$price = webmz_get_product_loop_price_data( $product );

		if ( 'unavailable' === $price['state'] ) {
			$price_html = '<span class="webmz-fl__popup-price webmz-fl__popup-price--muted">' . esc_html__( 'ناموجود', 'tadris' ) . '</span>';
		} elseif ( 'free' === $price['state'] ) {
			$price_html = '<span class="webmz-fl__popup-price">' . esc_html__( 'رایگان', 'tadris' ) . '</span>';
		} else {
			$price_html = sprintf(
				'<span class="webmz-fl__popup-price">%s %s</span>',
				esc_html( $price['amount'] ),
				esc_html( $price['currency'] )
			);
		}
	}

	$badge_text = function_exists( 'webmz_pll_get_product_badge_text' )
		? webmz_pll_get_product_badge_text( $product_id, $badge_fallback )
		: $badge_fallback;

	ob_start();
	?>
	<div class="webmz-fl__popup-inner">
		<?php if ( $image_html ) : ?>
			<div class="webmz-fl__popup-media">
				<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>

		<div class="webmz-fl__popup-title"><?php echo esc_html( $product->get_name() ); ?></div>

		<div class="webmz-fl__popup-footer">
			<?php if ( $price_html ) : ?>
				<div class="webmz-fl__popup-price-wrap">
					<?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>

			<?php if ( $badge_text ) : ?>
				<div class="webmz-fl__popup-badge"><?php echo esc_html( $badge_text ); ?></div>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Load hover popup markup via AJAX.
 *
 * @return void
 */
function webmz_file_loop_ajax_hover_popup() {
	check_ajax_referer( 'webmz_file_loop', 'nonce' );

	if ( ! function_exists( 'wc_get_product' ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'ووکامرس فعال نیست.', 'tadris' ),
			)
		);
	}

	$product_id     = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$badge_fallback = isset( $_POST['badge_fallback'] ) ? sanitize_text_field( wp_unslash( $_POST['badge_fallback'] ) ) : '';

	if ( ! $product_id ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'محصول نامعتبر است.', 'tadris' ),
			)
		);
	}

	$product = wc_get_product( $product_id );

	if ( ! $product || ! $product->is_visible() ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'محصول یافت نشد.', 'tadris' ),
			)
		);
	}

	$cache_key = 'webmz_fl_popup_' . $product_id . '_' . md5( $badge_fallback );
	$cached    = get_transient( $cache_key );

	if ( is_string( $cached ) && '' !== $cached ) {
		wp_send_json_success(
			array(
				'html' => $cached,
				'url'  => $product->get_permalink(),
			)
		);
	}

	$html = webmz_file_loop_render_hover_popup_html( $product, $badge_fallback );

	if ( '' === $html ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'محتوایی برای نمایش وجود ندارد.', 'tadris' ),
			)
		);
	}

	set_transient( $cache_key, $html, 15 * MINUTE_IN_SECONDS );

	wp_send_json_success(
		array(
			'html' => $html,
			'url'  => $product->get_permalink(),
		)
	);
}
add_action( 'wp_ajax_webmz_file_loop_hover', 'webmz_file_loop_ajax_hover_popup' );
add_action( 'wp_ajax_nopriv_webmz_file_loop_hover', 'webmz_file_loop_ajax_hover_popup' );
