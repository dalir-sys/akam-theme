<?php
/**
 * Payamito SMS gateway.
 *
 * The uploaded Payamito documents show that the panel is compatible with the
 * payamak-panel webservice family. For OTP we prefer the REST shared-service
 * endpoint because it is intended for approved service templates/bodyId.
 *
 * Pattern mode:
 *   POST https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber
 *   UserName, PassWord, text, to, bodyId
 *
 * Simple mode:
 *   POST https://rest.payamak-panel.com/api/SendSMS/SendSMS
 *   username, password, from, to, text, isFlash
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build semicolon-separated Payamito shared-number variables.
 *
 * The Payamito SharedNumber REST document says text variables must be sent in
 * order separated by semicolons. The current OTP UI has one variable field, so
 * the default is just the OTP code. If a developer needs more variables later,
 * this filter can return an array or a semicolon-separated string.
 *
 * @param string $mobile   Normalized mobile number.
 * @param string $code     OTP code.
 * @param string $text     Rendered message text.
 * @param array  $settings OTP settings.
 * @return string
 */
function webmz_otp_gate_payamito_pattern_text( $mobile, $code, $text, $settings ) {
	$variables = apply_filters(
		'webmz_otp_payamito_pattern_variables',
		array( $code ),
		$mobile,
		$code,
		$text,
		$settings
	);

	if ( is_array( $variables ) ) {
		$variables = array_map( 'sanitize_text_field', array_map( 'strval', $variables ) );
		$variables = implode( ';', $variables );
	}

	$variables = trim( (string) $variables );

	return '' !== $variables ? $variables : (string) $code;
}

/**
 * Translate common Payamito/Payamak-panel return values for readable debug.
 *
 * @param mixed $value Return value/code.
 * @return string
 */
function webmz_otp_gate_payamito_error_message( $value ) {
	$value = trim( (string) $value );

	$messages = array(
		'-7' => 'خطایی در شماره فرستنده رخ داده است؛ با پشتیبانی پیامیتو تماس بگیرید.',
		'-6' => 'خطای داخلی سرویس پیامیتو رخ داده است.',
		'-5' => 'متن ارسالی با متغیرهای متن پیشفرض همخوانی ندارد.',
		'-4' => 'کد متن/Body ID صحیح نیست یا توسط مدیریت سامانه تأیید نشده است.',
		'-3' => 'خط ارسالی در سیستم تعریف نشده است.',
		'-2' => 'محدودیت تعداد شماره؛ در ارسال خدماتی اشتراکی فقط یک شماره مجاز است.',
		'-1' => 'دسترسی استفاده از این وب‌سرویس غیرفعال است.',
		'0'  => 'نام کاربری یا رمز عبور پیامیتو صحیح نیست.',
		'2'  => 'اعتبار کافی نیست.',
		'6'  => 'سامانه در حال بروزرسانی است.',
		'7'  => 'متن شامل کلمه فیلتر شده است.',
		'10' => 'کاربر موردنظر فعال نیست.',
		'11' => 'پیامک ارسال نشده است.',
		'12' => 'مدارک کاربر کامل نیست.',
	);

	return isset( $messages[ $value ] ) ? $messages[ $value ] : '';
}

/**
 * Send SMS via Payamito.
 *
 * @param string $mobile      Mobile number.
 * @param string $code        OTP code.
 * @param string $text        SMS text.
 * @param array  $settings    OTP settings.
 * @param bool   $use_pattern Whether pattern mode should be used.
 * @param int    $timeout     HTTP timeout.
 * @param array  $debug       Debug data reference.
 * @return array|WP_Error|null
 */
function webmz_otp_gate_payamito_send( $mobile, $code, $text, $settings, $use_pattern, $timeout, &$debug ) {
	$username = isset( $settings['username'] ) ? trim( (string) $settings['username'] ) : '';
	$password = isset( $settings['password'] ) ? trim( (string) $settings['password'] ) : '';

	if ( '' === $username || '' === $password ) {
		return new WP_Error(
			'payamito_missing_credentials',
			'برای درگاه پیامیتو باید نام کاربری و رمز عبور پنل را وارد کنید. API Key در مستندات ارسالی پیامیتو برای این وب‌سرویس استفاده نمی‌شود.'
		);
	}

	if ( $use_pattern && ! empty( $settings['pattern_id'] ) ) {
		$body_id = absint( $settings['pattern_id'] );
		if ( ! $body_id ) {
			return new WP_Error( 'payamito_missing_body_id', 'شناسه پترن/Body ID پیامیتو معتبر نیست.' );
		}

		$pattern_text = webmz_otp_gate_payamito_pattern_text( $mobile, $code, $text, $settings );

		$response = webmz_otp_remote_request_with_debug(
			'POST',
			'https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber',
			array(
				'timeout' => $timeout,
				'headers' => array(
					'Accept' => 'application/json',
				),
				'body'    => array(
					'UserName' => $username,
					'PassWord' => $password,
					'text'     => $pattern_text,
					'to'       => $mobile,
					'bodyId'   => $body_id,
				),
			),
			$debug
		);

		return webmz_otp_gate_payamito_validate_response( $response );
	}

	$sender = isset( $settings['sender'] ) ? trim( (string) $settings['sender'] ) : '';
	if ( '' === $sender ) {
		return new WP_Error( 'payamito_missing_sender', 'برای ارسال ساده پیامیتو باید خط ارسال‌کننده را وارد کنید. برای OTP بهتر است حالت پترن/خدماتی و Body ID را استفاده کنید.' );
	}

	$response = webmz_otp_remote_request_with_debug(
		'POST',
		'https://rest.payamak-panel.com/api/SendSMS/SendSMS',
		array(
			'timeout' => $timeout,
			'headers' => array(
				'Accept' => 'application/json',
			),
			'body'    => array(
				'username' => $username,
				'password' => $password,
				'to'       => $mobile,
				'from'     => $sender,
				'text'     => $text,
				'isFlash'  => false,
			),
		),
		$debug
	);

	return webmz_otp_gate_payamito_validate_response( $response );
}

/**
 * Validate Payamito REST response early so RetStatus/Value errors do not pass
 * as successful HTTP 200 responses.
 *
 * @param array|WP_Error $response WordPress HTTP response.
 * @return array|WP_Error
 */
function webmz_otp_gate_payamito_validate_response( $response ) {
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$http_code = wp_remote_retrieve_response_code( $response );
	if ( $http_code < 200 || $http_code >= 300 ) {
		return $response;
	}

	$body_raw = trim( (string) wp_remote_retrieve_body( $response ) );
	if ( '' === $body_raw ) {
		return new WP_Error( 'payamito_empty_response', 'پاسخ پیامیتو خالی بود.' );
	}

	$body = json_decode( $body_raw, true );
	if ( ! is_array( $body ) ) {
		return $response;
	}

	$ret_status = null;
	$str_status = '';
	$value      = null;

	if ( isset( $body['RetStatus'] ) ) {
		$ret_status = (int) $body['RetStatus'];
		$str_status = isset( $body['StrRetStatus'] ) ? (string) $body['StrRetStatus'] : '';
		$value      = isset( $body['Value'] ) ? $body['Value'] : null;
	} elseif ( isset( $body['MyBase'] ) && is_array( $body['MyBase'] ) ) {
		$ret_status = isset( $body['MyBase']['RetStatus'] ) ? (int) $body['MyBase']['RetStatus'] : null;
		$str_status = isset( $body['MyBase']['StrRetStatus'] ) ? (string) $body['MyBase']['StrRetStatus'] : '';
		$value      = isset( $body['MyBase']['Value'] ) ? $body['MyBase']['Value'] : null;
	}

	if ( 1 === $ret_status && 'ok' === strtolower( trim( $str_status ) ) ) {
		return $response;
	}

	if ( null !== $ret_status && 1 !== $ret_status ) {
		$provider_message = webmz_otp_gate_payamito_error_message( $value );
		if ( '' === $provider_message ) {
			$provider_message = 'ارسال پیامک توسط پیامیتو ناموفق بود.';
		}
		return new WP_Error(
			'payamito_provider_error',
			sprintf(
				'%s RetStatus: %s, StrRetStatus: %s, Value: %s',
				$provider_message,
				(string) $ret_status,
				$str_status,
				is_scalar( $value ) ? (string) $value : wp_json_encode( $value, JSON_UNESCAPED_UNICODE )
			)
		);
	}

	return $response;
}
