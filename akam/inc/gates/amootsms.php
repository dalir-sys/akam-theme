<?php
/**
 * AmootSMS gateway.
 *
 * Public docs show the REST SendSimple endpoint:
 * https://portal.amootsms.com/rest/SendSimple
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Send SMS via AmootSMS.
 */
function webmz_otp_gate_amootsms_send( $mobile, $code, $text, $settings, $use_pattern, $timeout, &$debug ) {
	$token = ! empty( $settings['api_key'] ) ? $settings['api_key'] : $settings['password'];
	if ( empty( $token ) ) {
		return null;
	}

	// Amoot's public REST docs use token + Mobiles + SMSMessageText. For OTP we send the rendered template text.
	$message = $text;
	if ( $use_pattern && ! empty( $settings['message_template'] ) ) {
		$message = webmz_otp_template( $settings['message_template'], $mobile, $code );
	}

	return webmz_otp_remote_request_with_debug(
		'POST',
		'https://portal.amootsms.com/rest/SendSimple',
		array(
			'timeout' => $timeout,
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Accept'       => 'application/json',
			),
			'body'    => array(
				'token'          => $token,
				'Mobiles'        => $mobile,
				'SendDateTime'   => '0',
				'SMSMessageText' => $message,
				'LineNumber'     => ! empty( $settings['sender'] ) ? $settings['sender'] : 'Public',
			),
		),
		$debug
	);
}
