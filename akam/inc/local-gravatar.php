<?php
/**
 * Local Gravatar replacement for WebMZ.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Check whether local avatar replacement is enabled.
 *
 * @return bool
 */
function webmz_local_gravatar_is_enabled() {
	return 'yes' === (string) webmz_get_option( 'local_gravatar_enable' );
}

/**
 * Get configured local avatar URL.
 *
 * @return string
 */
function webmz_local_gravatar_get_url() {
	$url = (string) webmz_get_option( 'local_gravatar_url' );

	if ( '' === $url ) {
		return '';
	}

	return esc_url_raw( $url );
}

/**
 * Filter avatar data before WordPress builds Gravatar URLs.
 *
 * @param array        $args Avatar args.
 * @param mixed|string $id_or_email User identifier.
 * @return array
 */
function webmz_local_gravatar_pre_data( $args, $id_or_email ) {
	if ( ! webmz_local_gravatar_is_enabled() ) {
		return $args;
	}

	$local_url = webmz_local_gravatar_get_url();

	if ( '' === $local_url ) {
		return $args;
	}

	$args['url']          = esc_url( $local_url );
	$args['found_avatar'] = true;

	return $args;
}
add_filter( 'pre_get_avatar_data', 'webmz_local_gravatar_pre_data', 20, 2 );

/**
 * Replace avatar URL globally for code paths that call get_avatar_url().
 *
 * @param string $url Avatar URL.
 * @param mixed  $id_or_email User identifier.
 * @param array  $args Avatar args.
 * @return string
 */
function webmz_local_gravatar_replace_url( $url, $id_or_email, $args ) {
	if ( ! webmz_local_gravatar_is_enabled() ) {
		return $url;
	}

	$local_url = webmz_local_gravatar_get_url();

	if ( '' === $local_url ) {
		return $url;
	}

	return esc_url( $local_url );
}
add_filter( 'get_avatar_url', 'webmz_local_gravatar_replace_url', 20, 3 );

/**
 * Replace generated avatar HTML globally.
 *
 * @param string $avatar Avatar HTML.
 * @param mixed  $id_or_email User identifier.
 * @param int    $size Avatar size.
 * @param string $default Default avatar.
 * @param string $alt Alt text.
 * @param array  $args Avatar args.
 * @return string
 */
function webmz_local_gravatar_replace_html( $avatar, $id_or_email, $size, $default, $alt, $args ) {
	if ( ! webmz_local_gravatar_is_enabled() ) {
		return $avatar;
	}

	$local_url = webmz_local_gravatar_get_url();

	if ( '' === $local_url ) {
		return $avatar;
	}

	$size = absint( $size );

	if ( ! $size ) {
		$size = 96;
	}

	$alt = '' !== $alt ? $alt : esc_attr__( 'آواتار کاربر', 'tadris' );

	$classes = array( 'avatar', 'avatar-' . $size, 'photo', 'webmz-local-avatar' );

	if ( ! empty( $args['class'] ) ) {
		$extra_classes = is_array( $args['class'] ) ? $args['class'] : preg_split( '/\s+/', (string) $args['class'] );
		$classes       = array_merge( $classes, array_filter( array_map( 'sanitize_html_class', $extra_classes ) ) );
	}

	$class_attr = esc_attr( implode( ' ', array_unique( array_filter( $classes ) ) ) );

	return sprintf(
		'<img alt="%1$s" src="%2$s" class="%4$s" height="%3$d" width="%3$d" loading="lazy" decoding="async" />',
		esc_attr( $alt ),
		esc_url( $local_url ),
		$size,
		$class_attr
	);
}
add_filter( 'get_avatar', 'webmz_local_gravatar_replace_html', 20, 6 );

/**
 * Register local avatar as an available default avatar option.
 *
 * @param array $avatar_defaults Default avatar choices.
 * @return array
 */
function webmz_local_gravatar_default_avatar( $avatar_defaults ) {
	if ( ! webmz_local_gravatar_is_enabled() ) {
		return $avatar_defaults;
	}

	$local_url = webmz_local_gravatar_get_url();

	if ( '' === $local_url ) {
		return $avatar_defaults;
	}

	$avatar_defaults[ esc_url( $local_url ) ] = esc_html__( 'آواتار لوکال آکام', 'tadris' );

	return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'webmz_local_gravatar_default_avatar' );
