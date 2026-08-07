<?php
/**
 * IPPanel gateway.
 *
 * Supports:
 * - Classic username/password endpoint: https://ippanel.com/api/select
 * - Token endpoint: api2.ippanel.com
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Send SMS via IPPanel.
 */
function webmz_otp_gate_ippanel_send( $mobile, $code, $text, $settings, $use_pattern, $timeout, &$debug ) {
	if ( ! empty( $settings['username'] ) && ! empty( $settings['password'] ) ) {
		if ( $use_pattern && ! empty( $settings['pattern_id'] ) ) {
			return webmz_otp_remote_request_with_debug(
				'POST',
				'https://ippanel.com/api/select',
				array(
					'timeout' => $timeout,
					'headers' => array(
						'Content-Type' => 'application/json',
						'Accept'       => 'application/json',
					),
					'body'    => wp_json_encode(
						array(
							'op'          => 'pattern',
							'user'        => $settings['username'],
							'pass'        => $settings['password'],
							'fromNum'     => $settings['sender'],
							'toNum'       => $mobile,
							'patternCode' => $settings['pattern_id'],
							'inputData'   => array(
								array( $settings['pattern_variable'] => $code ),
							),
						),
						JSON_UNESCAPED_UNICODE
					),
				),
				$debug
			);
		}

		return webmz_otp_remote_request_with_debug(
			'POST',
			'https://ippanel.com/api/select',
			array(
				'timeout' => $timeout,
				'headers' => array(
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
				),
				'body'    => wp_json_encode(
					array(
						'op'             => 'send',
						'user'           => $settings['username'],
						'pass'           => $settings['password'],
						'fromNum'        => $settings['sender'],
						'toNum'          => $mobile,
						'messageContent' => $text,
					),
					JSON_UNESCAPED_UNICODE
				),
			),
			$debug
		);
	}

	if ( empty( $settings['api_key'] ) ) {
		return null;
	}

	if ( $use_pattern && ! empty( $settings['pattern_id'] ) ) {
		return webmz_otp_remote_request_with_debug(
			'POST',
			'https://api2.ippanel.com/api/v1/sms/pattern/normal/send',
			array(
				'timeout' => $timeout,
				'headers' => array(
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
					'apikey'       => $settings['api_key'],
				),
				'body'    => wp_json_encode(
					array(
						'code'      => $settings['pattern_id'],
						'sender'    => $settings['sender'],
						'recipient' => $mobile,
						'variable'  => array( $settings['pattern_variable'] => $code ),
					),
					JSON_UNESCAPED_UNICODE
				),
			),
			$debug
		);
	}

	return webmz_otp_remote_request_with_debug(
		'POST',
		'https://api2.ippanel.com/api/v1/sms/send/webservice/single',
		array(
			'timeout' => $timeout,
			'headers' => array(
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
				'apikey'       => $settings['api_key'],
			),
			'body'    => wp_json_encode(
				array(
					'sender'    => $settings['sender'],
					'recipient' => $mobile,
					'message'   => $text,
				),
				JSON_UNESCAPED_UNICODE
			),
		),
		$debug
	);
}
