<?php
/**
 * Theme favicon output from panel settings.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get the configured favicon attachment ID.
 *
 * @return int
 */
function webmz_get_favicon_id() {
	$id = absint( webmz_get_option( 'favicon_id' ) );

	if ( ! $id || 'attachment' !== get_post_type( $id ) ) {
		return 0;
	}

	return $id;
}

/**
 * Suppress WordPress default site icon when a theme favicon is set.
 *
 * @param array<int,string> $meta_tags Site icon meta tags.
 * @return array<int,string>
 */
function webmz_filter_site_icon_meta_tags( $meta_tags ) {
	if ( webmz_get_favicon_id() ) {
		return array();
	}

	return $meta_tags;
}
add_filter( 'site_icon_meta_tags', 'webmz_filter_site_icon_meta_tags' );

/**
 * Output favicon link tags in the document head.
 *
 * @return void
 */
function webmz_output_favicon() {
	$favicon_id = webmz_get_favicon_id();

	if ( ! $favicon_id ) {
		return;
	}

	$sizes = array(
		32  => 'icon',
		180 => 'apple-touch-icon',
		192 => 'icon',
	);

	$mime_type = get_post_mime_type( $favicon_id );
	$tags      = array();

	foreach ( $sizes as $size => $rel ) {
		$url = wp_get_attachment_image_url( $favicon_id, array( $size, $size ) );

		if ( ! $url ) {
			continue;
		}

		$attrs = sprintf( 'rel="%s" href="%s"', esc_attr( $rel ), esc_url( $url ) );

		if ( $mime_type ) {
			$attrs .= sprintf( ' type="%s"', esc_attr( $mime_type ) );
		}

		if ( 'icon' === $rel ) {
			$attrs .= sprintf( ' sizes="%dx%d"', absint( $size ), absint( $size ) );
		}

		$tags[] = sprintf( '<link %s>', $attrs );
	}

	if ( empty( $tags ) ) {
		$url = wp_get_attachment_url( $favicon_id );

		if ( $url ) {
			$attrs = sprintf( 'rel="icon" href="%s"', esc_url( $url ) );

			if ( $mime_type ) {
				$attrs .= sprintf( ' type="%s"', esc_attr( $mime_type ) );
			}

			$tags[] = sprintf( '<link %s>', $attrs );
		}
	}

	if ( empty( $tags ) ) {
		return;
	}

	echo "\n" . implode( "\n", $tags ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'webmz_output_favicon', 1 );
