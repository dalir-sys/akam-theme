<?php
/**
 * Shared video URL handling: direct files, Aparat and YouTube links.
 *
 * Every video field in the theme (post video, product intro video, course lessons,
 * playlist, download box) accepts any of these link types and renders through
 * webmz_render_video_player() so behaviour stays consistent.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Parse a video URL into its provider and embed data.
 *
 * @param string $url Video URL.
 * @return array{type:string,id:string,url:string,embed_url:string} type is file|aparat|youtube, or '' for empty input.
 */
function webmz_video_parse_url( $url ) {
	$url  = trim( (string) $url );
	$data = array(
		'type'      => '',
		'id'        => '',
		'url'       => $url,
		'embed_url' => '',
	);

	if ( '' === $url ) {
		return $data;
	}

	if ( preg_match( '~aparat\.com/(?:v/|video/video/embed/videohash/|embed/)([A-Za-z0-9]+)~i', $url, $matches ) ) {
		$data['type']      = 'aparat';
		$data['id']        = $matches[1];
		$data['embed_url'] = 'https://www.aparat.com/video/video/embed/videohash/' . rawurlencode( $matches[1] ) . '/vt/frame';
		return $data;
	}

	if ( preg_match( '~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:embed/|shorts/|live/|v/|watch\?(?:.*&)?v=))([A-Za-z0-9_-]{6,})~i', $url, $matches ) ) {
		$data['type']      = 'youtube';
		$data['id']        = $matches[1];
		$data['embed_url'] = 'https://www.youtube.com/embed/' . rawurlencode( $matches[1] );
		return $data;
	}

	$data['type'] = 'file';

	return $data;
}

/**
 * Whether a URL points to a hosted video page (Aparat/YouTube) rather than a file.
 *
 * @param string $url Video URL.
 * @return bool
 */
function webmz_video_is_embed_url( $url ) {
	$type = webmz_video_parse_url( $url )['type'];

	return 'aparat' === $type || 'youtube' === $type;
}

/**
 * Guess the MIME type of a direct video file for the <source> tag.
 *
 * @param string $url File URL.
 * @return string
 */
function webmz_video_file_mime( $url ) {
	$path = (string) wp_parse_url( (string) $url, PHP_URL_PATH );
	$ext  = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
	$map  = array(
		'webm' => 'video/webm',
		'ogv'  => 'video/ogg',
		'ogg'  => 'video/ogg',
		'mov'  => 'video/mp4',
		'm4v'  => 'video/mp4',
	);

	return isset( $map[ $ext ] ) ? $map[ $ext ] : 'video/mp4';
}

/**
 * Render a video player for any supported URL.
 *
 * Direct files render a <video> (Plyr-enhanced, keeps view-history attributes),
 * YouTube renders a Plyr embed, Aparat renders a responsive iframe (Aparat has no
 * player API, so view history/progress is not tracked for it).
 *
 * @param string              $url  Video URL.
 * @param array<string,mixed> $args {
 *     @type string $poster      Poster image URL (files only).
 *     @type string $video_class Classes for the <video> / embed element.
 *     @type array  $video_attrs Extra attributes for the <video> element (files only).
 *     @type string $title       Accessible iframe title.
 * }
 * @return string
 */
function webmz_get_video_player_html( $url, $args = array() ) {
	$video = webmz_video_parse_url( $url );

	if ( '' === $video['type'] ) {
		return '';
	}

	$args = wp_parse_args(
		$args,
		array(
			'poster'      => '',
			'video_class' => 'tadris-player-tag',
			'video_attrs' => array(),
			'title'       => '',
		)
	);

	$title = '' !== $args['title'] ? $args['title'] : __( 'ویدیو', 'tadris' );

	if ( 'aparat' === $video['type'] ) {
		return sprintf(
			'<div class="webmz-video-embed webmz-video-embed--aparat" data-webmz-video-type="aparat"><iframe src="%1$s" title="%2$s" loading="lazy" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" allow="autoplay; fullscreen; picture-in-picture"></iframe></div>',
			esc_url( $video['embed_url'] ),
			esc_attr( $title )
		);
	}

	if ( 'youtube' === $video['type'] ) {
		$query = array(
			'origin'         => home_url(),
			'iv_load_policy' => 3,
			'modestbranding' => 1,
			'playsinline'    => 1,
			'rel'            => 0,
			'enablejsapi'    => 1,
		);

		// Plyr enhances .plyr__video-embed wrappers, so YouTube keeps the theme's player UI.
		return sprintf(
			'<div class="webmz-video-embed webmz-video-embed--youtube" data-webmz-video-type="youtube"><div class="plyr__video-embed %1$s"><iframe src="%2$s" title="%3$s" allowfullscreen allowtransparency allow="autoplay; fullscreen; picture-in-picture"></iframe></div></div>',
			esc_attr( $args['video_class'] ),
			esc_url( add_query_arg( $query, $video['embed_url'] ) ),
			esc_attr( $title )
		);
	}

	$attrs = array_merge(
		array(
			'class'       => $args['video_class'],
			'playsinline' => true,
			'controls'    => true,
			'preload'     => 'metadata',
		),
		(array) $args['video_attrs']
	);

	if ( '' !== $args['poster'] ) {
		$attrs['poster']      = esc_url( $args['poster'] );
		$attrs['data-poster'] = esc_url( $args['poster'] );
	}

	$attr_html = '';

	foreach ( $attrs as $name => $value ) {
		if ( false === $value || null === $value || '' === $value ) {
			continue;
		}

		$attr_html .= true === $value
			? ' ' . esc_attr( $name )
			: sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( (string) $value ) );
	}

	return sprintf(
		'<video%1$s><source src="%2$s" type="%3$s"></video>',
		$attr_html,
		esc_url( $video['url'] ),
		esc_attr( webmz_video_file_mime( $video['url'] ) )
	);
}

/**
 * Echo a video player for any supported URL.
 *
 * @param string              $url  Video URL.
 * @param array<string,mixed> $args See webmz_get_video_player_html().
 * @return void
 */
function webmz_render_video_player( $url, $args = array() ) {
	echo webmz_get_video_player_html( $url, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped while building.
}

/**
 * Placeholder text for admin video URL fields.
 *
 * @return string
 */
function webmz_video_field_placeholder() {
	return 'https://example.com/video.mp4 | https://www.aparat.com/v/xxxxx | https://youtu.be/xxxxx';
}

/**
 * Help text for admin video URL fields.
 *
 * @return string
 */
function webmz_video_field_help() {
	return __( 'لینک مستقیم فایل (MP4)، لینک صفحه ویدیو در آپارات یا لینک یوتیوب را وارد کنید.', 'tadris' );
}

/**
 * Register shared video assets and attach them to the theme players.
 *
 * @return void
 */
function webmz_video_register_assets() {
	wp_register_style( 'webmz-video-embeds', WEBMZ_URI . 'assets/css/video-embeds.css', array(), WEBMZ_VERSION );
	wp_register_script( 'webmz-video-embeds', WEBMZ_URI . 'assets/js/video-embeds.js', array(), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-video-embeds',
		'webmzVideoEmbeds',
		array(
			'closeLabel' => esc_html__( 'بستن', 'tadris' ),
			'videoLabel' => esc_html__( 'ویدیو', 'tadris' ),
		)
	);

	// Every theme player stylesheet/script pulls these in, so no widget has to list them.
	foreach ( array( 'webmz-plyr-widgets', 'webmz-single-product-webmasters' ) as $handle ) {
		$style = wp_styles()->query( $handle, 'registered' );
		if ( $style && ! in_array( 'webmz-video-embeds', $style->deps, true ) ) {
			$style->deps[] = 'webmz-video-embeds';
		}
	}

	$script = wp_scripts()->query( 'webmz-tadris-widgets', 'registered' );
	if ( $script && ! in_array( 'webmz-video-embeds', $script->deps, true ) ) {
		$script->deps[] = 'webmz-video-embeds';
	}
}
// After webmz_enqueue_assets (priority 10) has registered the handles we attach to.
add_action( 'wp_enqueue_scripts', 'webmz_video_register_assets', 20 );

/**
 * Whether a URL can be played by the theme player (embed page or a browser-playable file).
 *
 * Used where a field may also hold non-video links, e.g. course lessons pointing to SpotPlayer.
 *
 * @param string $url URL.
 * @return bool
 */
function webmz_video_is_playable_url( $url ) {
	$video = webmz_video_parse_url( $url );

	if ( 'aparat' === $video['type'] || 'youtube' === $video['type'] ) {
		return true;
	}

	if ( 'file' !== $video['type'] ) {
		return false;
	}

	$path = (string) wp_parse_url( $video['url'], PHP_URL_PATH );

	return (bool) preg_match( '/\.(mp4|m4v|webm|ogv|mov)$/i', $path );
}
