<?php
/**
 * AJAX handlers and helpers for YouTube playlist widget.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

function webmz_youtube_playlist_format_duration( $seconds ) {
	$seconds = absint( $seconds );

	if ( $seconds <= 0 ) {
		return '';
	}

	$hours   = (int) floor( $seconds / 3600 );
	$minutes = (int) floor( ( $seconds % 3600 ) / 60 );
	$secs    = $seconds % 60;

	if ( $hours > 0 ) {
		return sprintf( '%02d:%02d:%02d', $hours, $minutes, $secs );
	}

	return sprintf( '%02d:%02d', $minutes, $secs );
}

function webmz_youtube_playlist_get_video_duration( $video_url ) {
	$video_url = esc_url_raw( $video_url );

	if ( '' === $video_url ) {
		return 0;
	}

	$attachment_id = attachment_url_to_postid( $video_url );

	if ( ! $attachment_id ) {
		return 0;
	}

	$metadata = wp_get_attachment_metadata( $attachment_id );

	if ( empty( $metadata['length'] ) ) {
		return 0;
	}

	return absint( $metadata['length'] );
}

function webmz_youtube_playlist_get_item_data( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
		return null;
	}

	$video_url = esc_url_raw( get_post_meta( $post_id, '_webmz_video_url', true ) );

	if ( '' === $video_url ) {
		return null;
	}

	$title         = get_the_title( $post_id );
	$image_url     = get_the_post_thumbnail_url( $post_id, webmz_get_loop_image_size() );
	$thumb_url     = get_the_post_thumbnail_url( $post_id, webmz_get_loop_image_size() );
	$duration      = webmz_youtube_playlist_get_video_duration( $video_url );
	$level_label   = function_exists( 'webmz_get_post_training_level_label' ) ? webmz_get_post_training_level_label( $post_id ) : '';
	$downloads     = function_exists( 'webmz_get_post_downloads' ) ? webmz_get_post_downloads( $post_id ) : array();
	$progress      = function_exists( 'webmz_view_history_get_user_post_progress' ) ? webmz_view_history_get_user_post_progress( $post_id ) : array();
	$is_saved      = function_exists( 'webmz_tadris_user_has_favorite' ) ? webmz_tadris_user_has_favorite( get_current_user_id(), $post_id ) : false;
	$author_id     = (int) get_post_field( 'post_author', $post_id );
	$author_name   = get_the_author_meta( 'display_name', $author_id );
	$author_avatar = get_avatar_url( $author_id, array( 'size' => 64 ) );
	$excerpt       = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ), 28 );
	$image_html    = '';
	$progress_time = ! empty( $progress['current'] ) ? (float) $progress['current'] : 0.0;

	if ( $image_url ) {
		$image_html = webmz_get_post_loop_thumbnail(
			$post_id,
			array(
				'class' => 'webmz-ytp__thumb',
				'alt'   => esc_attr( $title ),
			)
		);
	}

	return array(
		'id'              => $post_id,
		'title'           => $title,
		'video_url'       => $video_url,
		'permalink'       => get_permalink( $post_id ),
		'image_url'       => $image_url ? $image_url : '',
		'thumb_url'       => $thumb_url ? $thumb_url : '',
		'image_html'      => $image_html,
		'duration'        => $duration,
		'duration_label'  => webmz_youtube_playlist_format_duration( $duration ),
		'level_label'     => $level_label,
		'excerpt'         => $excerpt,
		'author_name'     => $author_name,
		'author_avatar'   => $author_avatar ? $author_avatar : '',
		'downloads'       => $downloads,
		'is_saved'        => $is_saved,
		'progress_current'=> $progress_time,
	);
}

function webmz_youtube_playlist_verify_nonce() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

	if ( ! wp_verify_nonce( $nonce, 'webmz_youtube_playlist' ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'درخواست نامعتبر است. صفحه را تازه‌سازی کنید.', 'tadris' ),
			),
			403
		);
	}
}

function webmz_youtube_playlist_ajax_load() {
	webmz_youtube_playlist_verify_nonce();

	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$data    = webmz_youtube_playlist_get_item_data( $post_id );

	if ( null === $data ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'ویدیو معتبر نیست.', 'tadris' ),
			),
			404
		);
	}

	wp_send_json_success( $data );
}
add_action( 'wp_ajax_webmz_youtube_playlist_load', 'webmz_youtube_playlist_ajax_load' );
add_action( 'wp_ajax_nopriv_webmz_youtube_playlist_load', 'webmz_youtube_playlist_ajax_load' );
