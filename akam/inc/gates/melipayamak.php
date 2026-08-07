<?php
/**
 * MeliPayamak gateway.
 *
 * REST endpoints commonly used by MeliPayamak:
 * - SendSMS:           https://rest.payamak-panel.com/api/SendSMS/SendSMS
 * - BaseServiceNumber: https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Send SMS via MeliPayamak.
 */
function webmz_otp_gate_melipayamak_send( $mobile, $code, $text, $settings, $use_pattern, $timeout, &$debug ) {
	if ( empty( $settings['username'] ) || empty( $settings['password'] ) ) {
		return null;
	}

	if ( $use_pattern && ! empty( $settings['pattern_id'] ) ) {
		return webmz_otp_remote_request_with_debug(
			'POST',
			'https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber',
			array(
				'timeout' => $timeout,
				'headers' => array(
					'Accept' => 'application/json',
				),
				'body'    => array(
					'username' => $settings['username'],
					'password' => $settings['password'],
					'text'     => $code,
					'to'       => $mobile,
					'bodyId'   => absint( $settings['pattern_id'] ),
				),
			),
			$debug
		);
	}

	return webmz_otp_remote_request_with_debug(
		'POST',
		'https://rest.payamak-panel.com/api/SendSMS/SendSMS',
		array(
			'timeout' => $timeout,
			'headers' => array(
				'Accept' => 'application/json',
			),
			'body'    => array(
				'username' => $settings['username'],
				'password' => $settings['password'],
				'from'     => $settings['sender'],
				'to'       => $mobile,
				'text'     => $text,
				'isFlash'  => false,
			),
		),
		$debug
	);
}
