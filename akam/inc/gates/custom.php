<?php
/**
 * Custom SMS gateway.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Send SMS via custom endpoint.
 */
function webmz_otp_gate_custom_send( $mobile, $code, $text, $settings, $use_pattern, $timeout, &$debug ) {
	if ( empty( $settings['custom_url'] ) ) {
		return null;
	}

	$headers = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $settings['custom_headers'] ) as $line ) {
		if ( false !== strpos( $line, ':' ) ) {
			list( $h_key, $h_val ) = array_map( 'trim', explode( ':', $line, 2 ) );
			$headers[ $h_key ] = webmz_otp_template( $h_val, $mobile, $code );
		}
	}

	$body = webmz_otp_template( $settings['custom_body'], $mobile, $code );
	$url  = webmz_otp_template( $settings['custom_url'], $mobile, $code );

	if ( 'GET' === $settings['custom_method'] ) {
		return webmz_otp_remote_request_with_debug(
			'GET',
			$url,
			array(
				'timeout' => $timeout,
				'headers' => $headers,
			),
			$debug
		);
	}

	return webmz_otp_remote_request_with_debug(
		'POST',
		$url,
		array(
			'timeout' => $timeout,
			'headers' => $headers,
			'body'    => $body,
		),
		$debug
	);
}
