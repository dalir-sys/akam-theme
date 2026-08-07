<?php
/**
 * AJAX handlers for the Special Offer Slider widget.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build intro video payload for a WooCommerce product.
 *
 * @param int $product_id Product ID.
 * @return array<string,string>|null
 */
function webmz_sos_get_intro_video_data( $product_id ) {
	$product_id = absint( $product_id );

	if ( ! $product_id || 'product' !== get_post_type( $product_id ) || 'publish' !== get_post_status( $product_id ) ) {
		return null;
	}

	if ( ! function_exists( 'wc_get_product' ) ) {
		return null;
	}

	$product = wc_get_product( $product_id );

	if ( ! $product ) {
		return null;
	}

	$video_url = function_exists( 'webmz_spw_get_product_intro_video_url' )
		? webmz_spw_get_product_intro_video_url( $product_id )
		: '';

	if ( '' === $video_url ) {
		return null;
	}

	$poster = has_post_thumbnail( $product_id ) ? get_the_post_thumbnail_url( $product_id, webmz_get_loop_image_size() ) : '';

	return array(
		'video_url' => esc_url_raw( $video_url ),
		'poster'    => $poster ? esc_url_raw( $poster ) : '',
		'title'     => $product->get_name(),
	);
}

/**
 * Verify special offer slider ajax nonce.
 *
 * @return void
 */
function webmz_sos_verify_intro_video_nonce() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

	if ( ! wp_verify_nonce( $nonce, 'webmz_sos_intro_video' ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'درخواست نامعتبر است. صفحه را تازه‌سازی کنید.', 'tadris' ),
			),
			403
		);
	}
}

/**
 * Ajax: load product intro video for modal player.
 *
 * @return void
 */
function webmz_sos_ajax_intro_video() {
	webmz_sos_verify_intro_video_nonce();

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$data       = webmz_sos_get_intro_video_data( $product_id );

	if ( null === $data ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'ویدیو معرفی برای این محصول یافت نشد.', 'tadris' ),
			),
			404
		);
	}

	wp_send_json_success( $data );
}
add_action( 'wp_ajax_webmz_sos_intro_video', 'webmz_sos_ajax_intro_video' );
add_action( 'wp_ajax_nopriv_webmz_sos_intro_video', 'webmz_sos_ajax_intro_video' );
