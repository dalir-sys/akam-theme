<?php
/**
 * AJAX handlers for lazy-loaded story boxes.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load story box track markup and payload.
 *
 * @return void
 */
function webmz_stories_ajax_load_box() {
	check_ajax_referer( 'webmz_stories', 'nonce' );

	$box_id = isset( $_POST['box_id'] ) ? absint( $_POST['box_id'] ) : 0;

	if ( ! $box_id ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'باکس استوری نامعتبر است.', 'tadris' ),
			)
		);
	}

	$data = webmz_get_story_box_frontend_data( $box_id );

	if ( empty( $data['groups'] ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'استوری‌ای برای نمایش وجود ندارد.', 'tadris' ),
			)
		);
	}

	wp_send_json_success(
		array(
			'track'   => webmz_render_story_box_track_html( $data ),
			'payload' => $data,
		)
	);
}
add_action( 'wp_ajax_webmz_load_story_box', 'webmz_stories_ajax_load_box' );
add_action( 'wp_ajax_nopriv_webmz_load_story_box', 'webmz_stories_ajax_load_box' );
