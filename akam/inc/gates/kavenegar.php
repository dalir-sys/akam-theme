<?php
/**
 * Kavenegar SMS gateway.
 *
 * Docs:
 * - Simple send: https://api.kavenegar.com/v1/{API-KEY}/sms/send.json
 * - Lookup OTP: https://api.kavenegar.com/v1/{API-KEY}/verify/lookup.json
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Send SMS via Kavenegar.
 */
function webmz_otp_gate_kavenegar_send( $mobile, $code, $text, $settings, $use_pattern, $timeout, &$debug ) {
	if ( empty( $settings['api_key'] ) ) {
		return null;
	}

	if ( $use_pattern && ! empty( $settings['pattern_id'] ) ) {
		$url = 'https://api.kavenegar.com/v1/' . rawurlencode( $settings['api_key'] ) . '/verify/lookup.json';

		return webmz_otp_remote_request_with_debug(
			'POST',
			$url,
			array(
				'timeout' => $timeout,
				'body'    => array(
					'receptor' => $mobile,
					'token'    => $code,
					'template' => $settings['pattern_id'],
				),
			),
			$debug
		);
	}

	$url = 'https://api.kavenegar.com/v1/' . rawurlencode( $settings['api_key'] ) . '/sms/send.json';

	return webmz_otp_remote_request_with_debug(
		'POST',
		$url,
		array(
			'timeout' => $timeout,
			'body'    => array(
				'receptor' => $mobile,
				'sender'   => $settings['sender'],
				'message'  => $text,
			),
		),
		$debug
	);
}
