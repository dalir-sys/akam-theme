<?php
/**
 * FarazSMS gateway.
 *
 * Current FarazSMS pattern documentation uses IranPayamak's REST endpoint:
 *   POST https://api.iranpayamak.com/ws/v1/sms/pattern
 *
 * Required headers:
 *   Accept: application/json
 *   Api-Key: <api-key>
 *   Content-Type: application/json
 *
 * Required JSON body:
 *   code, attributes, recipient, line_number, number_format
 *
 * Older Faraz/IPPanel username-password routes are kept only as a fallback for
 * non-pattern/simple sends when username and password are configured.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build Faraz/IranPayamak pattern attributes.
 *
 * The Faraz pattern API uses an attributes object. The OTP settings screen has
 * one variable name field, so set it to the exact variable in your Faraz pattern
 * such as var1, token, code, etc. Developers may return a richer array with the
 * filter below.
 *
 * @param string $mobile   Mobile number.
 * @param string $code     OTP code.
 * @param string $text     Rendered SMS text.
 * @param array  $settings OTP settings.
 * @return array<string,string>
 */
function webmz_otp_gate_farazsms_pattern_attributes( $mobile, $code, $text, $settings ) {
	$variable_name = ! empty( $settings['pattern_variable'] ) ? sanitize_key( $settings['pattern_variable'] ) : 'var1';

	$attributes = apply_filters(
		'webmz_otp_farazsms_pattern_attributes',
		array( $variable_name => (string) $code ),
		$mobile,
		$code,
		$text,
		$settings
	);

	if ( ! is_array( $attributes ) || empty( $attributes ) ) {
		$attributes = array( $variable_name => (string) $code );
	}

	$clean = array();
	foreach ( $attributes as $key => $value ) {
		$key = sanitize_key( (string) $key );
		if ( '' === $key ) {
			continue;
		}
		$clean[ $key ] = sanitize_text_field( (string) $value );
	}

	return ! empty( $clean ) ? $clean : array( 'var1' => (string) $code );
}

/**
 * Validate Faraz/IranPayamak response early for clearer debug messages.
 *
 * @param array|WP_Error $response HTTP response.
 * @return array|WP_Error
 */
function webmz_otp_gate_farazsms_validate_response( $response ) {
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$http_code = absint( wp_remote_retrieve_response_code( $response ) );
	$body_raw  = trim( (string) wp_remote_retrieve_body( $response ) );

	if ( 401 === $http_code || 403 === $http_code ) {
		return new WP_Error(
			'farazsms_permission_denied',
			'درگاه فراز اس‌ام‌اس اجازه ارسال نداد. API Key، دسترسی وب‌سرویس، خط ارسال و فعال بودن پترن برای همین API Key را بررسی کنید.'
		);
	}

	if ( $http_code < 200 || $http_code >= 300 ) {
		return $response;
	}

	if ( '' === $body_raw ) {
		return new WP_Error( 'farazsms_empty_response', 'پاسخ فراز اس‌ام‌اس خالی بود.' );
	}

	$body = json_decode( $body_raw, true );
	if ( ! is_array( $body ) ) {
		$lower = strtolower( $body_raw );
		if ( false !== strpos( $lower, 'permission_denied' ) || false !== strpos( $lower, 'unauthorized' ) || false !== strpos( $lower, 'error' ) ) {
			return new WP_Error( 'farazsms_provider_error', 'فراز اس‌ام‌اس پاسخ خطا برگرداند: ' . wp_strip_all_tags( $body_raw ) );
		}
		return $response;
	}

	// Common success shapes used by IranPayamak/Faraz style APIs.
	foreach ( array( 'success', 'Success', 'successful', 'Successful' ) as $success_key ) {
		if ( array_key_exists( $success_key, $body ) ) {
			return (bool) $body[ $success_key ] ? $response : new WP_Error( 'farazsms_provider_error', 'فراز اس‌ام‌اس ارسال را ناموفق اعلام کرد.' );
		}
	}

	foreach ( array( 'status', 'Status', 'code', 'Code', 'statusCode', 'StatusCode' ) as $key ) {
		if ( ! isset( $body[ $key ] ) || is_array( $body[ $key ] ) ) {
			continue;
		}

		$status = strtolower( trim( (string) $body[ $key ] ) );
		if ( in_array( $status, array( 'ok', 'success', 'successful', 'true', '1', '200', '201' ), true ) ) {
			return $response;
		}
		if ( in_array( $status, array( 'false', 'failed', 'failure', 'error', '0', '-1', '400', '401', '403', '404', '500' ), true ) ) {
			$message = '';
			foreach ( array( 'message', 'Message', 'error', 'Error', 'description', 'Description' ) as $message_key ) {
				if ( ! empty( $body[ $message_key ] ) && is_scalar( $body[ $message_key ] ) ) {
					$message = (string) $body[ $message_key ];
					break;
				}
			}
			return new WP_Error( 'farazsms_provider_error', 'فراز اس‌ام‌اس ارسال را ناموفق اعلام کرد.' . ( $message ? ' ' . $message : '' ) );
		}
	}

	return $response;
}

/**
 * Send SMS via FarazSMS.
 *
 * @param string $mobile      Mobile number.
 * @param string $code        OTP code.
 * @param string $text        Rendered message text.
 * @param array  $settings    OTP settings.
 * @param bool   $use_pattern Whether pattern mode should be used.
 * @param int    $timeout     HTTP timeout.
 * @param array  $debug       Debug data reference.
 * @return array|WP_Error|null
 */
function webmz_otp_gate_farazsms_send( $mobile, $code, $text, $settings, $use_pattern, $timeout, &$debug ) {
	$api_key    = isset( $settings['api_key'] ) ? trim( (string) $settings['api_key'] ) : '';
	$sender     = isset( $settings['sender'] ) ? trim( (string) $settings['sender'] ) : '';
	$pattern_id = isset( $settings['pattern_id'] ) ? trim( (string) $settings['pattern_id'] ) : '';

	if ( $use_pattern ) {
		if ( '' === $api_key ) {
			return new WP_Error( 'farazsms_missing_api_key', 'برای ارسال پترن فراز اس‌ام‌اس باید API Key را وارد کنید.' );
		}
		if ( '' === $pattern_id ) {
			return new WP_Error( 'farazsms_missing_pattern', 'برای ارسال پترن فراز اس‌ام‌اس باید کد پترن را وارد کنید.' );
		}
		if ( '' === $sender ) {
			return new WP_Error( 'farazsms_missing_line_number', 'برای ارسال پترن فراز اس‌ام‌اس باید شماره خط یا line_number را وارد کنید.' );
		}

		$body = array(
			'code'          => $pattern_id,
			'attributes'    => webmz_otp_gate_farazsms_pattern_attributes( $mobile, $code, $text, $settings ),
			'recipient'     => $mobile,
			'line_number'   => $sender,
			'number_format' => 'english',
		);

		$body = apply_filters( 'webmz_otp_farazsms_pattern_body', $body, $mobile, $code, $text, $settings );

		$response = webmz_otp_remote_request_with_debug(
			'POST',
			'https://api.iranpayamak.com/ws/v1/sms/pattern',
			array(
				'timeout' => $timeout,
				'headers' => array(
					'Accept'       => 'application/json',
					'Api-Key'      => $api_key,
					'Content-Type' => 'application/json',
				),
				'body'    => wp_json_encode( $body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ),
			),
			$debug
		);

		return webmz_otp_gate_farazsms_validate_response( $response );
	}

	// Event SMS such as order/ticket notifications may be non-pattern. If the
	// account still uses the classic Faraz/IPPanel username-password webservice,
	// keep that fallback so existing installations do not break.
	if ( ! empty( $settings['username'] ) && ! empty( $settings['password'] ) ) {
		if ( ! function_exists( 'webmz_otp_gate_ippanel_send' ) ) {
			require_once __DIR__ . '/ippanel.php';
		}
		return webmz_otp_gate_ippanel_send( $mobile, $code, $text, $settings, false, $timeout, $debug );
	}

	return new WP_Error(
		'farazsms_simple_not_configured',
		'برای ارسال غیرپترنی فراز اس‌ام‌اس، نام کاربری/رمز عبور کلاسیک را وارد کنید یا پیامک‌های رویدادی را با یک درگاه ساده‌ارسال جداگانه تنظیم کنید. برای OTP از حالت پترن با API Key استفاده شود.'
	);
}
