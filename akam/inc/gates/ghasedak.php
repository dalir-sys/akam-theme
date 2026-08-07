<?php
/**
 * Ghasedak gateway.
 *
 * Docs:
 * - Single SMS: https://gateway.ghasedak.me/rest/api/v1/WebService/SendSingleSMS
 * - OTP:        https://gateway.ghasedak.me/rest/api/v1/WebService/SendOtpWithParams
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Send SMS via Ghasedak.
 */
function webmz_otp_gate_ghasedak_send( $mobile, $code, $text, $settings, $use_pattern, $timeout, &$debug ) {
	if ( empty( $settings['api_key'] ) ) {
		return null;
	}

	if ( $use_pattern && ! empty( $settings['pattern_id'] ) ) {
		return webmz_otp_remote_request_with_debug(
			'POST',
			'https://gateway.ghasedak.me/rest/api/v1/WebService/SendOtpWithParams',
			array(
				'timeout' => $timeout,
				'headers' => array(
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
					'ApiKey'       => $settings['api_key'],
				),
				'body'    => wp_json_encode(
					array(
						'receptors'    => array(
							array(
								'mobile'            => $mobile,
								'clientReferenceId' => (string) time(),
							),
						),
						'templateName' => $settings['pattern_id'],
						'param1'       => $code,
						'isVoice'      => false,
						'udh'          => false,
					),
					JSON_UNESCAPED_UNICODE
				),
			),
			$debug
		);
	}

	return webmz_otp_remote_request_with_debug(
		'POST',
		'https://gateway.ghasedak.me/rest/api/v1/WebService/SendSingleSMS',
		array(
			'timeout' => $timeout,
			'headers' => array(
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
				'ApiKey'       => $settings['api_key'],
			),
			'body'    => wp_json_encode(
				array(
					'lineNumber'        => $settings['sender'],
					'receptor'          => $mobile,
					'message'           => $text,
					'clientReferenceId' => (string) time(),
					'udh'               => false,
				),
				JSON_UNESCAPED_UNICODE
			),
		),
		$debug
	);
}
