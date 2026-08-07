<?php
/**
 * AJAX handlers for the Podcast Player widget.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Format seconds as mm:ss.
 *
 * @param int $seconds Duration in seconds.
 * @return string
 */
function webmz_podcast_player_format_duration( $seconds ) {
	$seconds = absint( $seconds );

	if ( $seconds <= 0 ) {
		return '';
	}

	$minutes = (int) floor( $seconds / 60 );
	$secs    = $seconds % 60;

	return sprintf( '%02d:%02d', $minutes, $secs );
}

/**
 * Try to read audio duration from a media-library attachment.
 *
 * Uses attachment metadata when available (frontend-safe). Duration for
 * external URLs or missing metadata is resolved client-side via JS.
 *
 * @param string $audio_url Audio file URL.
 * @return int Duration in seconds.
 */
function webmz_podcast_player_get_audio_duration( $audio_url ) {
	$audio_url = esc_url_raw( $audio_url );

	if ( '' === $audio_url ) {
		return 0;
	}

	$attachment_id = attachment_url_to_postid( $audio_url );

	if ( ! $attachment_id ) {
		return 0;
	}

	$metadata = wp_get_attachment_metadata( $attachment_id );

	if ( empty( $metadata['length'] ) ) {
		return 0;
	}

	return absint( $metadata['length'] );
}

/**
 * Build podcast payload for the player widget.
 *
 * @param int $post_id Post ID.
 * @return array<string,mixed>|null
 */
function webmz_podcast_player_get_item_data( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
		return null;
	}

	$audio_url = function_exists( 'webmz_tadris_get_podcast_audio_url' )
		? webmz_tadris_get_podcast_audio_url( $post_id )
		: '';

	if ( '' === $audio_url ) {
		return null;
	}

	$title     = get_the_title( $post_id );
	$image_url = get_the_post_thumbnail_url( $post_id, webmz_get_loop_image_size() );
	$duration  = webmz_podcast_player_get_audio_duration( $audio_url );

	$image_html = '';

	if ( $image_url ) {
		$image_html = webmz_get_post_loop_thumbnail(
			$post_id,
			array(
				'class' => 'webmz-pp__thumb-img',
				'alt'   => esc_attr( $title ),
			)
		);
	}

	return array(
		'id'              => $post_id,
		'title'           => $title,
		'permalink'       => get_permalink( $post_id ),
		'audio_url'       => $audio_url,
		'image_url'       => $image_url ? $image_url : '',
		'image_html'      => $image_html,
		'duration'        => $duration,
		'duration_label'  => webmz_podcast_player_format_duration( $duration ),
	);
}

/**
 * Verify podcast player ajax nonce.
 *
 * @return void
 */
function webmz_podcast_player_verify_nonce() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

	if ( ! wp_verify_nonce( $nonce, 'webmz_podcast_player' ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'درخواست نامعتبر است. صفحه را تازه‌سازی کنید.', 'tadris' ),
			),
			403
		);
	}
}

/**
 * Ajax: load podcast item for active player.
 *
 * @return void
 */
function webmz_podcast_player_ajax_load() {
	webmz_podcast_player_verify_nonce();

	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$data    = webmz_podcast_player_get_item_data( $post_id );

	if ( null === $data ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'پادکست معتبر نیست.', 'tadris' ),
			),
			404
		);
	}

	wp_send_json_success( $data );
}
add_action( 'wp_ajax_webmz_podcast_player_load', 'webmz_podcast_player_ajax_load' );
add_action( 'wp_ajax_nopriv_webmz_podcast_player_load', 'webmz_podcast_player_ajax_load' );
