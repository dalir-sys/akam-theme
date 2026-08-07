<?php
/**
 * SMS.ir gateway.
 *
 * Docs:
 * - Verify: https://api.sms.ir/v1/send/verify
 * - Bulk:   https://api.sms.ir/v1/send/bulk
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Send SMS via SMS.ir.
 */
function webmz_otp_gate_smsir_send( $mobile, $code, $text, $settings, $use_pattern, $timeout, &$debug ) {
	if ( empty( $settings['api_key'] ) ) {
		return null;
	}

	if ( $use_pattern && ! empty( $settings['pattern_id'] ) ) {
		return webmz_otp_remote_request_with_debug(
			'POST',
			'https://api.sms.ir/v1/send/verify',
			array(
				'timeout' => $timeout,
				'headers' => array(
					'Content-Type' => 'application/json',
					'Accept'       => 'text/plain',
					'x-api-key'    => $settings['api_key'],
				),
				'body'    => wp_json_encode(
					array(
						'mobile'     => $mobile,
						'templateId' => is_numeric( $settings['pattern_id'] ) ? absint( $settings['pattern_id'] ) : $settings['pattern_id'],
						'parameters' => array(
							array(
								'name'  => $settings['pattern_variable'],
								'value' => $code,
							),
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
		'https://api.sms.ir/v1/send/bulk',
		array(
			'timeout' => $timeout,
			'headers' => array(
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
				'x-api-key'    => $settings['api_key'],
			),
			'body'    => wp_json_encode(
				array(
					'lineNumber'  => $settings['sender'],
					'messageText' => $text,
					'mobiles'     => array( $mobile ),
				),
				JSON_UNESCAPED_UNICODE
			),
		),
		$debug
	);
}
