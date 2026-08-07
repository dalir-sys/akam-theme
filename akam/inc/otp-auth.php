<?php
/**
 * WebMZ SMS OTP and Google login system.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WEBMZ_OTP_META_KEY' ) ) {
	define( 'WEBMZ_OTP_META_KEY', '_webmz_mobile' );
}

/**
 * Fixed demo OTP used only on localhost / webmz.ir hosts.
 *
 * @return string
 */
function webmz_otp_demo_code() {
	return '0000';
}

/**
 * Whether OTP should run in demo/mock mode (no real SMS).
 * Only on localhost and webmz.ir / *.webmz.ir.
 *
 * @return bool
 */
function webmz_otp_is_mock_mode() {
	if ( function_exists( 'webmz_is_license_whitelisted_host' ) ) {
		return webmz_is_license_whitelisted_host();
	}

	$host = '';
	if ( ! empty( $_SERVER['HTTP_HOST'] ) ) {
		$host = strtolower( (string) wp_unslash( $_SERVER['HTTP_HOST'] ) );
	}
	$host = preg_replace( '/:\d+$/', '', $host );

	if ( in_array( $host, array( 'localhost', '127.0.0.1', '::1' ), true ) ) {
		return true;
	}

	return ( 'webmz.ir' === $host || substr( $host, -9 ) === '.webmz.ir' );
}

/**
 * Build AJAX success payload for a generated OTP, including demo fields when needed.
 *
 * @param string              $code            Generated OTP.
 * @param array<string,mixed> $settings        OTP settings.
 * @param string              $mobile          Normalized mobile.
 * @param string              $default_message Default success message when not mock.
 * @return array<string,mixed>
 */
function webmz_otp_request_success_payload( $code, $settings, $mobile, $default_message ) {
	$code    = (string) $code;
	$is_mock = webmz_otp_is_mock_mode();
	$payload = array(
		'message' => $default_message,
		'mode'    => webmz_otp_find_user_by_mobile( $mobile ) ? 'login' : 'register',
		'resend'  => absint( $settings['resend_seconds'] ),
	);

	if ( $is_mock ) {
		$code                = webmz_otp_demo_code();
		$payload['message']  = esc_html__( 'کد در حالت دمو 0000 است.', 'tadris' );
		$payload['mock']     = true;
		$payload['code']     = $code;
		$payload['otp']      = $code;
		$payload['demo_otp'] = $code;
	}

	return $payload;
}

/**
 * All known Iranian mobile operator prefixes (4-digit 09XX).
 *
 * @return array<int,string>
 */
function webmz_otp_default_iran_mobile_prefixes() {
	return array(
		'0900', '0901', '0902', '0903', '0904', '0905',
		'0910', '0911', '0912', '0913', '0914', '0915', '0916', '0917', '0918', '0919',
		'0920', '0921', '0922', '0923',
		'0930', '0931', '0932', '0933', '0934', '0935', '0936', '0937', '0938', '0939',
		'0941',
		'0955',
		'0990', '0991', '0992', '0993', '0994', '0998', '0999',
	);
}

/**
 * Default OTP settings.
 *
 * @return array<string,mixed>
 */
function webmz_otp_default_settings() {
	return array(
		'enabled'                 => 'yes',
		'provider'                => 'kavenegar',
		'send_mode'               => 'pattern',
		'api_key'                 => '',
		'username'                => '',
		'password'                => '',
		'sender'                  => '',
		'pattern_id'              => '',
		'pattern_variable'        => 'code',
		'message_template'        => 'کد ورود شما: {code}',
		'otp_length'              => 5,
		'otp_ttl'                 => 180,
		'resend_seconds'          => 60,
		'fast_send'               => 'yes',
		'sms_timeout'             => 20,
		'max_phone_hour'          => 5,
		'max_ip_hour'             => 15,
		'max_attempts'            => 5,
		'lockout_minutes'         => 15,
		'auto_popup'              => 'yes',
		'replace_wc_forms'        => 'yes',
		'require_otp_checkout'    => 'yes',
		'require_login_download'  => 'no',
		'allow_google'            => 'no',
		'google_client_id'        => '',
		'google_client_secret'    => '',
		'google_button_label'     => 'ورود با گوگل',
		'send_ticket_sms'         => 'no',
		'send_order_sms'          => 'no',
		'ticket_sms_template'     => 'به تیکت شما در {site} پاسخ داده شد: {url}',
		'order_sms_template'      => 'سفارش شما در {site} با موفقیت ثبت شد. شماره سفارش: {order_id}',
		'custom_url'              => '',
		'custom_method'           => 'POST',
		'custom_headers'          => '',
		'custom_body'             => '',
		'notes'                   => '',
		'firewall_enabled'        => 'yes',
		'firewall_honeypot'       => 'yes',
		'firewall_min_seconds'    => 3,
		'firewall_max_seconds'    => 600,
		'firewall_block_empty_ua' => 'yes',
		'prefix_whitelist_enabled'=> 'yes',
		'prefix_whitelist'      => implode( "\n", webmz_otp_default_iran_mobile_prefixes() ),
	);
}

/**
 * Supported SMS provider labels.
 *
 * @return array<string,string>
 */
function webmz_otp_provider_labels() {
	return array(
		'kavenegar'   => 'Kavenegar / کاوه‌نگار',
		'smsir'       => 'SMS.ir',
		'melipayamak' => 'MeliPayamak / ملی پیامک',
		'ippanel'     => 'IPPanel',
		'farazsms'    => 'FarazSMS / فراز اس‌ام‌اس',
		'ghasedak'    => 'Ghasedak / قاصدک',
		'amootsms'    => 'AmootSMS / آموت',
		'payamito'    => 'Payamito / پیامیتو',
		'custom'      => 'وب‌سرویس سفارشی',
	);
}

/** Get OTP settings. */
function webmz_otp_get_settings() {
	$settings = get_option( 'webmz_otp_settings', array() );
	$settings = is_array( $settings ) ? $settings : array();
	$settings = wp_parse_args( $settings, webmz_otp_default_settings() );
	$settings['send_ticket_sms'] = 'no';
	$settings['send_order_sms']  = 'no';
	return $settings;
}

/** Sanitize OTP settings. */
function webmz_otp_sanitize_settings( $raw ) {
	$raw      = is_array( $raw ) ? $raw : array();
	$defaults = webmz_otp_default_settings();
	$clean    = $defaults;
	$providers = array_keys( webmz_otp_provider_labels() );

	foreach ( array( 'enabled', 'auto_popup', 'replace_wc_forms', 'require_otp_checkout', 'require_login_download', 'allow_google', 'fast_send', 'firewall_enabled', 'firewall_honeypot', 'firewall_block_empty_ua', 'prefix_whitelist_enabled' ) as $key ) {
		$clean[ $key ] = isset( $raw[ $key ] ) && 'yes' === $raw[ $key ] ? 'yes' : 'no';
	}

	$provider = isset( $raw['provider'] ) ? sanitize_key( $raw['provider'] ) : $defaults['provider'];
	$clean['provider'] = in_array( $provider, $providers, true ) ? $provider : $defaults['provider'];
	$clean['send_mode'] = isset( $raw['send_mode'] ) && 'simple' === $raw['send_mode'] ? 'simple' : 'pattern';
	$clean['custom_method'] = isset( $raw['custom_method'] ) && 'GET' === strtoupper( $raw['custom_method'] ) ? 'GET' : 'POST';

	foreach ( array( 'api_key', 'username', 'password', 'sender', 'pattern_id', 'pattern_variable', 'google_client_id', 'google_client_secret' ) as $key ) {
		$clean[ $key ] = isset( $raw[ $key ] ) ? sanitize_text_field( wp_unslash( $raw[ $key ] ) ) : '';
	}

	foreach ( array( 'message_template', 'ticket_sms_template', 'order_sms_template', 'custom_headers', 'custom_body', 'notes' ) as $key ) {
		$clean[ $key ] = isset( $raw[ $key ] ) ? sanitize_textarea_field( wp_unslash( $raw[ $key ] ) ) : $defaults[ $key ];
	}

	$clean['send_ticket_sms'] = 'no';
	$clean['send_order_sms']  = 'no';
	$clean['custom_url']      = isset( $raw['custom_url'] ) ? esc_url_raw( wp_unslash( $raw['custom_url'] ) ) : '';
	$clean['google_button_label'] = isset( $raw['google_button_label'] ) ? sanitize_text_field( wp_unslash( $raw['google_button_label'] ) ) : $defaults['google_button_label'];

	$clean['otp_length']      = min( 8, max( 4, isset( $raw['otp_length'] ) ? absint( $raw['otp_length'] ) : $defaults['otp_length'] ) );
	$clean['otp_ttl']         = min( 900, max( 60, isset( $raw['otp_ttl'] ) ? absint( $raw['otp_ttl'] ) : $defaults['otp_ttl'] ) );
	$clean['resend_seconds']  = min( 300, max( 20, isset( $raw['resend_seconds'] ) ? absint( $raw['resend_seconds'] ) : $defaults['resend_seconds'] ) );
	$clean['sms_timeout']     = min( 60, max( 5, isset( $raw['sms_timeout'] ) ? absint( $raw['sms_timeout'] ) : $defaults['sms_timeout'] ) );
	$clean['max_phone_hour']  = min( 30, max( 1, isset( $raw['max_phone_hour'] ) ? absint( $raw['max_phone_hour'] ) : $defaults['max_phone_hour'] ) );
	$clean['max_ip_hour']     = min( 100, max( 3, isset( $raw['max_ip_hour'] ) ? absint( $raw['max_ip_hour'] ) : $defaults['max_ip_hour'] ) );
	$clean['max_attempts']    = min( 10, max( 3, isset( $raw['max_attempts'] ) ? absint( $raw['max_attempts'] ) : $defaults['max_attempts'] ) );
	$clean['lockout_minutes'] = min( 120, max( 5, isset( $raw['lockout_minutes'] ) ? absint( $raw['lockout_minutes'] ) : $defaults['lockout_minutes'] ) );
	$clean['firewall_min_seconds'] = min( 30, max( 0, isset( $raw['firewall_min_seconds'] ) ? absint( $raw['firewall_min_seconds'] ) : $defaults['firewall_min_seconds'] ) );
	$clean['firewall_max_seconds'] = min( 3600, max( 60, isset( $raw['firewall_max_seconds'] ) ? absint( $raw['firewall_max_seconds'] ) : $defaults['firewall_max_seconds'] ) );

	$raw_prefixes = isset( $raw['prefix_whitelist'] ) ? sanitize_textarea_field( wp_unslash( $raw['prefix_whitelist'] ) ) : $defaults['prefix_whitelist'];
	$prefix_lines   = preg_split( '/[\r\n,،]+/u', (string) $raw_prefixes );
	$valid_prefixes = array();
	foreach ( (array) $prefix_lines as $line ) {
		$digits = preg_replace( '/\D/', '', trim( (string) $line ) );
		if ( preg_match( '/^9\d{3}$/', $digits ) ) {
			$valid_prefixes[] = '0' . $digits;
		} elseif ( preg_match( '/^09\d{2}$/', $digits ) ) {
			$valid_prefixes[] = $digits;
		}
	}
	$valid_prefixes = array_values( array_unique( $valid_prefixes ) );
	$clean['prefix_whitelist'] = ! empty( $valid_prefixes ) ? implode( "\n", $valid_prefixes ) : $defaults['prefix_whitelist'];

	return $clean;
}

/** Whether OTP system is active. */
function webmz_otp_is_enabled() {
	$s = webmz_otp_get_settings();
	return 'yes' === $s['enabled'];
}

/** Normalize Iranian mobile number to 09xxxxxxxxx. */
function webmz_otp_normalize_mobile( $mobile ) {
	$mobile = preg_replace( '/[^0-9+]/', '', (string) $mobile );
	$mobile = str_replace( array( '+98', '0098' ), '0', $mobile );
	if ( preg_match( '/^98(9\d{9})$/', $mobile, $m ) ) {
		$mobile = '0' . $m[1];
	}
	if ( preg_match( '/^9\d{9}$/', $mobile ) ) {
		$mobile = '0' . $mobile;
	}
	return preg_match( '/^09\d{9}$/', $mobile ) ? $mobile : '';
}

/**
 * Parsed prefix whitelist from settings.
 *
 * @return array<int,string>
 */
function webmz_otp_get_prefix_whitelist() {
	$settings = webmz_otp_get_settings();
	$lines    = preg_split( '/[\r\n]+/', (string) $settings['prefix_whitelist'] );
	$prefixes = array_filter( array_map( 'trim', (array) $lines ) );

	if ( empty( $prefixes ) ) {
		return webmz_otp_default_iran_mobile_prefixes();
	}

	return array_values( array_unique( $prefixes ) );
}

/**
 * Whether a normalized mobile matches the configured prefix whitelist.
 *
 * @param string $mobile Normalized mobile number.
 * @return bool
 */
function webmz_otp_mobile_prefix_is_allowed( $mobile ) {
	$settings = webmz_otp_get_settings();
	if ( 'yes' !== $settings['prefix_whitelist_enabled'] ) {
		return true;
	}

	$mobile = webmz_otp_normalize_mobile( $mobile );
	if ( ! $mobile ) {
		return false;
	}

	foreach ( webmz_otp_get_prefix_whitelist() as $prefix ) {
		if ( 0 === strpos( $mobile, $prefix ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Issue a signed firewall token for OTP AJAX requests.
 *
 * @return array{token:string,issued:int}
 */
function webmz_otp_firewall_issue_token() {
	$issued = time();
	$token  = wp_hash( $issued . '|webmz_otp_fw|' . wp_salt( 'auth' ) );

	return array(
		'token'  => $token,
		'issued' => $issued,
	);
}

/**
 * Verify bot-firewall checks on OTP login/register AJAX.
 *
 * @return true|WP_Error
 */
function webmz_otp_firewall_verify_request() {
	$settings = webmz_otp_get_settings();
	if ( 'yes' !== $settings['firewall_enabled'] ) {
		return true;
	}

	if ( 'yes' === $settings['firewall_honeypot'] ) {
		$honeypot = isset( $_POST['webmz_otp_hp'] ) ? trim( wp_unslash( $_POST['webmz_otp_hp'] ) ) : '';
		if ( '' !== $honeypot ) {
			return new WP_Error( 'webmz_otp_bot', esc_html__( 'درخواست معتبر نیست. صفحه را رفرش کنید و دوباره تلاش کنید.', 'tadris' ) );
		}
	}

	if ( 'yes' === $settings['firewall_block_empty_ua'] ) {
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? trim( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		if ( '' === $user_agent ) {
			return new WP_Error( 'webmz_otp_bot', esc_html__( 'درخواست معتبر نیست. صفحه را رفرش کنید و دوباره تلاش کنید.', 'tadris' ) );
		}
	}

	$token  = isset( $_POST['webmz_otp_fw_token'] ) ? sanitize_text_field( wp_unslash( $_POST['webmz_otp_fw_token'] ) ) : '';
	$issued = isset( $_POST['webmz_otp_fw_issued'] ) ? absint( $_POST['webmz_otp_fw_issued'] ) : 0;
	if ( ! $token || ! $issued ) {
		return new WP_Error( 'webmz_otp_bot', esc_html__( 'درخواست معتبر نیست. صفحه را رفرش کنید و دوباره تلاش کنید.', 'tadris' ) );
	}

	$expected = wp_hash( $issued . '|webmz_otp_fw|' . wp_salt( 'auth' ) );
	if ( ! hash_equals( $expected, $token ) ) {
		return new WP_Error( 'webmz_otp_bot', esc_html__( 'درخواست معتبر نیست. صفحه را رفرش کنید و دوباره تلاش کنید.', 'tadris' ) );
	}

	$elapsed = time() - $issued;
	$min     = absint( $settings['firewall_min_seconds'] );
	$max     = absint( $settings['firewall_max_seconds'] );
	if ( $min > 0 && $elapsed < $min ) {
		return new WP_Error( 'webmz_otp_bot', esc_html__( 'درخواست معتبر نیست. صفحه را رفرش کنید و دوباره تلاش کنید.', 'tadris' ) );
	}
	if ( $max > 0 && $elapsed > $max ) {
		return new WP_Error( 'webmz_otp_bot', esc_html__( 'زمان اعتبار فرم منقضی شده است. صفحه را رفرش کنید و دوباره تلاش کنید.', 'tadris' ) );
	}

	return true;
}

/**
 * Send JSON error when firewall blocks a request.
 *
 * @return void
 */
function webmz_otp_firewall_guard() {
	$result = webmz_otp_firewall_verify_request();
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ), 403 );
	}
}

/** Return client IP-ish fingerprint. */
function webmz_otp_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
	return preg_replace( '/[^0-9a-fA-F:\.]/', '', $ip );
}

function webmz_otp_transient_key( $prefix, $value ) {
	return 'webmz_otp_' . $prefix . '_' . md5( (string) $value . '|' . wp_salt( 'auth' ) );
}

function webmz_otp_increment_counter( $key, $ttl ) {
	$count = absint( get_transient( $key ) );
	$count++;
	set_transient( $key, $count, absint( $ttl ) );
	return $count;
}

/** Generate numeric OTP. */
function webmz_otp_generate_code( $length ) {
	$length = min( 8, max( 4, absint( $length ) ) );
	$min = (int) pow( 10, $length - 1 );
	$max = (int) pow( 10, $length ) - 1;
	return (string) wp_rand( $min, $max );
}

/** Find user by mobile meta or Woo billing phone. */
function webmz_otp_find_user_by_mobile( $mobile ) {
	$mobile = webmz_otp_normalize_mobile( $mobile );
	if ( ! $mobile ) {
		return 0;
	}

	$users = get_users(
		array(
			'fields'     => 'ids',
			'number'     => 1,
			'meta_query' => array(
				'relation' => 'OR',
				array( 'key' => WEBMZ_OTP_META_KEY, 'value' => $mobile ),
				array( 'key' => 'billing_phone', 'value' => $mobile ),
			),
		)
	);

	return ! empty( $users ) ? absint( $users[0] ) : 0;
}

/** Create a random username like r8s955af. */
function webmz_otp_generate_username() {
	for ( $i = 0; $i < 100; $i++ ) {
		$random   = strtolower( wp_generate_password( 7, false, false ) );
		$username = 'r' . preg_replace( '/[^a-z0-9]/', '', $random );

		if ( strlen( $username ) < 8 ) {
			$username .= strtolower( wp_generate_password( 8 - strlen( $username ), false, false ) );
			$username  = preg_replace( '/[^a-z0-9]/', '', $username );
		}

		if ( ! username_exists( $username ) ) {
			return $username;
		}
	}

	// Final fallback, still checked against the database because duplicate usernames are a bad comedy routine.
	for ( $i = 0; $i < 100; $i++ ) {
		$username = 'r' . strtolower( wp_generate_password( 12, false, false ) );
		$username = preg_replace( '/[^a-z0-9]/', '', $username );
		if ( ! username_exists( $username ) ) {
			return $username;
		}
	}

	return 'r' . str_replace( '.', '', uniqid( '', true ) );
}

/** Create user from mobile. */
function webmz_otp_create_user( $mobile ) {
	$username = webmz_otp_generate_username();
	$password = wp_generate_password( 24, true, true );
	$user_id = wp_insert_user(
		array(
			'user_login'   => $username,
			'user_pass'    => $password,
			'display_name' => $username,
			'nickname'     => $username,
			'role'         => class_exists( 'WooCommerce' ) ? 'customer' : get_option( 'default_role', 'subscriber' ),
		)
	);

	if ( is_wp_error( $user_id ) ) {
		return $user_id;
	}

	update_user_meta( $user_id, WEBMZ_OTP_META_KEY, $mobile );
	update_user_meta( $user_id, 'billing_phone', $mobile );
	update_user_meta( $user_id, 'shipping_phone', $mobile );
	return absint( $user_id );
}

/** Set auth cookies for a user. */
function webmz_otp_login_user( $user_id ) {
	$user_id = absint( $user_id );
	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id, true, is_ssl() );
	do_action( 'wp_login', get_userdata( $user_id )->user_login, get_userdata( $user_id ) );
}

/** Replace placeholders in message text. */
function webmz_otp_template( $template, $mobile, $code, $extra = array() ) {
	$replacements = wp_parse_args(
		$extra,
		array(
			'{code}'     => $code,
			'{mobile}'   => $mobile,
			'{site}'     => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
			'{site_url}' => home_url( '/' ),
		)
	);
	return strtr( (string) $template, $replacements );
}

/** Redact sensitive values before showing debug information. */
function webmz_otp_redact_value( $value ) {
	$value = (string) $value;
	if ( '' === $value ) {
		return '';
	}
	$len = strlen( $value );
	if ( $len <= 6 ) {
		return str_repeat( '*', $len );
	}
	return substr( $value, 0, 3 ) . str_repeat( '*', max( 3, $len - 6 ) ) . substr( $value, -3 );
}

/** Prepare request data for safe debug output. */
function webmz_otp_prepare_debug_args( $args ) {
	$args = is_array( $args ) ? $args : array();
	$masked = $args;
	$sensitive_keys = array( 'apikey', 'api_key', 'x-api-key', 'ApiKey', 'Api-Key', 'Authorization', 'authorization', 'password', 'Password', 'pass', 'Pass', 'user', 'username', 'UserName', 'token', 'Token' );

	if ( isset( $masked['headers'] ) && is_array( $masked['headers'] ) ) {
		foreach ( $masked['headers'] as $key => $val ) {
			foreach ( $sensitive_keys as $sensitive ) {
				if ( 0 === strcasecmp( (string) $key, (string) $sensitive ) ) {
					$masked['headers'][ $key ] = webmz_otp_redact_value( $val );
				}
			}
		}
	}

	$mask_body = function( $body ) use ( $sensitive_keys, &$mask_body ) {
		if ( is_array( $body ) ) {
			foreach ( $body as $key => $val ) {
				foreach ( $sensitive_keys as $sensitive ) {
					if ( 0 === strcasecmp( (string) $key, (string) $sensitive ) ) {
						$body[ $key ] = webmz_otp_redact_value( $val );
						continue 2;
					}
				}
				$body[ $key ] = $mask_body( $val );
			}
			return $body;
		}
		return $body;
	};

	if ( isset( $masked['body'] ) ) {
		$decoded = is_string( $masked['body'] ) ? json_decode( $masked['body'], true ) : null;
		if ( is_array( $decoded ) ) {
			$masked['body'] = $mask_body( $decoded );
		} else {
			$masked['body'] = $mask_body( $masked['body'] );
		}
	}

	return $masked;
}

/** Run HTTP request and keep detailed debug data for testing. */
function webmz_otp_remote_request_with_debug( $method, $url, $args, &$debug ) {
	$method = 'GET' === strtoupper( $method ) ? 'GET' : 'POST';
	$is_async = ! empty( $GLOBALS['webmz_otp_async_sms_request'] );

	if ( $is_async ) {
		$args['blocking']    = false;
		$args['timeout']     = 1;
		$args['redirection'] = 0;
	}

	$debug[] = array(
		'method'  => $method,
		'url'     => preg_replace( '#/v1/([A-Za-z0-9_\-]{20,})/#', '/v1/***API_KEY***/', (string) $url ),
		'async'   => $is_async ? 'yes' : 'no',
		'request' => webmz_otp_prepare_debug_args( $args ),
	);

	$response = 'GET' === $method ? wp_remote_get( $url, $args ) : wp_remote_post( $url, $args );
	$index = count( $debug ) - 1;

	if ( is_wp_error( $response ) ) {
		$debug[ $index ]['wp_error'] = $response->get_error_message();
		return $response;
	}

	$debug[ $index ]['http_code']    = wp_remote_retrieve_response_code( $response );
	$debug[ $index ]['http_message'] = wp_remote_retrieve_response_message( $response );
	$debug[ $index ]['body']         = wp_remote_retrieve_body( $response );
	return $response;
}

/** Format debug details for AJAX output. */
function webmz_otp_format_debug_message( $provider, $debug, $base_message = '' ) {
	$lines = array();
	if ( $base_message ) {
		$lines[] = $base_message;
	}
	$lines[] = 'Provider: ' . $provider;
	foreach ( (array) $debug as $i => $item ) {
		$lines[] = '--- Request #' . ( $i + 1 ) . ' ---';
		$lines[] = 'Method: ' . ( isset( $item['method'] ) ? $item['method'] : '' );
		$lines[] = 'URL: ' . ( isset( $item['url'] ) ? $item['url'] : '' );
		if ( isset( $item['request'] ) ) {
			$lines[] = 'Request: ' . wp_json_encode( $item['request'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		}
		if ( isset( $item['http_code'] ) ) {
			$lines[] = 'HTTP: ' . $item['http_code'] . ' ' . ( isset( $item['http_message'] ) ? $item['http_message'] : '' );
		}
		if ( ! empty( $item['wp_error'] ) ) {
			$lines[] = 'WP_Error: ' . $item['wp_error'];
		}
		if ( isset( $item['body'] ) && '' !== trim( (string) $item['body'] ) ) {
			$lines[] = 'Response: ' . trim( wp_strip_all_tags( (string) $item['body'] ) );
		}
	}
	return implode( "\n", $lines );
}


/** Load separate SMS gateway files. */
function webmz_otp_load_sms_gates() {
	static $loaded = false;

	if ( $loaded ) {
		return;
	}

	$loaded = true;
	$gate_files = array(
		'kavenegar.php',
		'smsir.php',
		'melipayamak.php',
		'ippanel.php',
		'farazsms.php',
		'ghasedak.php',
		'amootsms.php',
		'payamito.php',
		'custom.php',
	);

	foreach ( $gate_files as $gate_file ) {
		$path = get_template_directory() . '/inc/gates/' . $gate_file;
		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
}


/**
 * Decide if a provider response looks successful.
 *
 * Providers return wildly different JSON structures because apparently every SMS panel
 * went to a separate school for API design. This keeps successful HTTP responses from
 * hiding provider-level errors.
 */
function webmz_otp_response_looks_successful( $http_code, $body_raw ) {
	$http_code = absint( $http_code );
	if ( $http_code < 200 || $http_code >= 300 ) {
		return false;
	}

	$body_raw = trim( (string) $body_raw );
	if ( '' === $body_raw ) {
		return true;
	}

	$body_json = json_decode( $body_raw, true );
	if ( ! is_array( $body_json ) ) {
		$lower = strtolower( $body_raw );
		if ( false !== strpos( $lower, 'error' ) || false !== strpos( $lower, 'permission_denied' ) || false !== strpos( $lower, 'unauthorized' ) ) {
			return false;
		}
		return true;
	}

	// Kavenegar: return.status must be 200 when present.
	if ( isset( $body_json['return'] ) && is_array( $body_json['return'] ) && isset( $body_json['return']['status'] ) ) {
		return 200 === absint( $body_json['return']['status'] );
	}

	// Ghasedak new REST API.
	if ( array_key_exists( 'IsSuccess', $body_json ) ) {
		return (bool) $body_json['IsSuccess'];
	}
	if ( isset( $body_json['Data'] ) && is_array( $body_json['Data'] ) && array_key_exists( 'IsSuccess', $body_json['Data'] ) ) {
		return (bool) $body_json['Data']['IsSuccess'];
	}

	// Payamak-panel/Payamito/MeliPayamak REST responses use RetStatus/StrRetStatus.
	if ( isset( $body_json['RetStatus'] ) ) {
		return 1 === absint( $body_json['RetStatus'] ) && ( ! isset( $body_json['StrRetStatus'] ) || 'ok' === strtolower( trim( (string) $body_json['StrRetStatus'] ) ) );
	}
	if ( isset( $body_json['MyBase'] ) && is_array( $body_json['MyBase'] ) && isset( $body_json['MyBase']['RetStatus'] ) ) {
		return 1 === absint( $body_json['MyBase']['RetStatus'] ) && ( ! isset( $body_json['MyBase']['StrRetStatus'] ) || 'ok' === strtolower( trim( (string) $body_json['MyBase']['StrRetStatus'] ) ) );
	}

	// Common booleans used by several panels.
	foreach ( array( 'success', 'Success', 'successful', 'Successful' ) as $success_key ) {
		if ( array_key_exists( $success_key, $body_json ) ) {
			return (bool) $body_json[ $success_key ];
		}
	}

	$json_code = null;
	foreach ( array( 'code', 'Code', 'status', 'Status', 'statusCode', 'StatusCode' ) as $code_key ) {
		if ( isset( $body_json[ $code_key ] ) && is_scalar( $body_json[ $code_key ] ) ) {
			$json_code = (string) $body_json[ $code_key ];
			break;
		}
	}
	if ( null === $json_code && isset( $body_json[0] ) && is_numeric( $body_json[0] ) ) {
		$json_code = (string) $body_json[0];
	}

	if ( null !== $json_code ) {
		$json_code_lc = strtolower( trim( $json_code ) );
		if ( in_array( $json_code_lc, array( '0', '1', '2', '200', '201', 'ok', 'success', 'successful', 'true' ), true ) ) {
			return true;
		}
		if ( in_array( $json_code_lc, array( '-1', '400', '401', '403', '404', '500', 'false', 'error', 'failed', 'failure' ), true ) ) {
			return false;
		}
	}

	return true;
}


/** Build provider request and send SMS. */
function webmz_otp_send_sms( $mobile, $code, $purpose = 'otp', $message = '', $async = false ) {
	// Demo hosts: skip real SMS gateways entirely.
	if ( webmz_otp_is_mock_mode() ) {
		return true;
	}

	$settings = webmz_otp_get_settings();
	$provider = sanitize_key( $settings['provider'] );
	$text     = $message ? $message : webmz_otp_template( $settings['message_template'], $mobile, $code );
	$timeout  = min( 60, max( 5, absint( isset( $settings['sms_timeout'] ) ? $settings['sms_timeout'] : 20 ) ) );
	$response = null;
	$debug    = array();
	$use_pattern = ( 'pattern' === $settings['send_mode'] && '' !== (string) $code );

	webmz_otp_load_sms_gates();

	/**
	 * Allow plugins/child themes to load or override gateway handlers.
	 * Expected callable name: webmz_otp_gate_{provider}_send.
	 */
	do_action( 'webmz_otp_before_sms_gate_send', $provider, $mobile, $code, $purpose, $settings );

	$gate_function = 'webmz_otp_gate_' . $provider . '_send';
	if ( function_exists( $gate_function ) ) {
		$previous_async_flag = isset( $GLOBALS['webmz_otp_async_sms_request'] ) ? $GLOBALS['webmz_otp_async_sms_request'] : null;
		$GLOBALS['webmz_otp_async_sms_request'] = $async ? true : false;

		$response = call_user_func_array(
			$gate_function,
			array(
				$mobile,
				$code,
				$text,
				$settings,
				$use_pattern,
				$timeout,
				&$debug,
			)
		);

		if ( null === $previous_async_flag ) {
			unset( $GLOBALS['webmz_otp_async_sms_request'] );
		} else {
			$GLOBALS['webmz_otp_async_sms_request'] = $previous_async_flag;
		}
	}

	if ( $async ) {
		return is_wp_error( $response ) ? $response : true;
	}

	if ( null === $response ) {
		$error = new WP_Error( 'sms_not_configured', 'هیچ درخواست پیامکی ساخته نشد؛ تنظیمات پنل، روش ارسال، API Key/نام کاربری و شناسه پترن را بررسی کنید.' );
		$error->add_data( array( 'debug' => webmz_otp_format_debug_message( $provider, $debug, $error->get_error_message() ) ) );
		return $error;
	}

	if ( is_wp_error( $response ) ) {
		$response->add_data( array( 'debug' => webmz_otp_format_debug_message( $provider, $debug, $response->get_error_message() ) ) );
		return $response;
	}

	$code_http = wp_remote_retrieve_response_code( $response );
	$body_raw  = wp_remote_retrieve_body( $response );
	$looks_successful = webmz_otp_response_looks_successful( $code_http, $body_raw );

	if ( ! $looks_successful ) {
		$error = new WP_Error( 'sms_failed', sprintf( 'ارسال پیامک ناموفق بود. HTTP %d', $code_http ) );
		$error->add_data( array( 'debug' => webmz_otp_format_debug_message( $provider, $debug, $error->get_error_message() ) ) );
		return $error;
	}

	return true;
}

/** AJAX: request OTP. */
function webmz_otp_ajax_request() {
	check_ajax_referer( 'webmz_otp_auth', 'nonce' );
	webmz_otp_firewall_guard();

	if ( ! webmz_otp_is_enabled() ) {
		wp_send_json_error( array( 'message' => esc_html__( 'ورود پیامکی در حال حاضر غیرفعال است.', 'tadris' ) ), 403 );
	}

	$settings = webmz_otp_get_settings();
	$mobile   = isset( $_POST['mobile'] ) ? webmz_otp_normalize_mobile( wp_unslash( $_POST['mobile'] ) ) : '';
	if ( ! $mobile ) {
		wp_send_json_error( array( 'message' => esc_html__( 'شماره موبایل ایران را به‌درستی وارد کنید.', 'tadris' ) ), 400 );
	}
	if ( ! webmz_otp_mobile_prefix_is_allowed( $mobile ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'پیشوند این شماره موبایل مجاز نیست.', 'tadris' ) ), 400 );
	}

	$lock_key = webmz_otp_transient_key( 'lock', $mobile );
	if ( get_transient( $lock_key ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'به دلیل تلاش ناموفق زیاد، کمی بعد دوباره تلاش کنید.', 'tadris' ) ), 429 );
	}

	$cooldown_key = webmz_otp_transient_key( 'cooldown', $mobile );
	$last_sent = absint( get_transient( $cooldown_key ) );
	if ( $last_sent && time() - $last_sent < absint( $settings['resend_seconds'] ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'برای ارسال مجدد کمی صبر کنید.', 'tadris' ), 'wait' => absint( $settings['resend_seconds'] ) - ( time() - $last_sent ) ), 429 );
	}

	$ip = webmz_otp_client_ip();
	$phone_hour_count = webmz_otp_increment_counter( webmz_otp_transient_key( 'hour_phone', $mobile ), HOUR_IN_SECONDS );
	if ( $phone_hour_count > absint( $settings['max_phone_hour'] ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'تعداد درخواست‌های این شماره بیش از حد مجاز است. کمی بعد دوباره تلاش کنید.', 'tadris' ) ), 429 );
	}

	$ip_hour_count = webmz_otp_increment_counter( webmz_otp_transient_key( 'hour_ip', $ip ), HOUR_IN_SECONDS );
	if ( $ip_hour_count > absint( $settings['max_ip_hour'] ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'تعداد درخواست‌های این اتصال بیش از حد مجاز است. کمی بعد دوباره تلاش کنید.', 'tadris' ) ), 429 );
	}

	$code = (string) webmz_otp_generate_code( $settings['otp_length'] );
	$fast_send = isset( $settings['fast_send'] ) && 'yes' === $settings['fast_send'];
	$is_mock   = webmz_otp_is_mock_mode();

	if ( $is_mock ) {
		$code = webmz_otp_demo_code();
	}

	if ( $fast_send || $is_mock ) {
		set_transient( webmz_otp_transient_key( 'code', $mobile ), array( 'hash' => wp_hash_password( $code ), 'attempts' => 0, 'created' => time() ), absint( $settings['otp_ttl'] ) );
		set_transient( $cooldown_key, time(), absint( $settings['resend_seconds'] ) );

		if ( ! $is_mock ) {
			webmz_otp_send_sms( $mobile, $code, 'otp', '', true );
		}

		wp_send_json_success(
			webmz_otp_request_success_payload(
				$code,
				$settings,
				$mobile,
				esc_html__( 'کد تأیید ارسال شد.', 'tadris' )
			)
		);
	}

	$result = webmz_otp_send_sms( $mobile, $code, 'otp' );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'ارسال پیامک ناموفق بود. لطفاً کمی بعد دوباره تلاش کنید.', 'tadris' ),
			),
			500
		);
	}

	set_transient( webmz_otp_transient_key( 'code', $mobile ), array( 'hash' => wp_hash_password( $code ), 'attempts' => 0, 'created' => time() ), absint( $settings['otp_ttl'] ) );
	set_transient( $cooldown_key, time(), absint( $settings['resend_seconds'] ) );

	wp_send_json_success(
		webmz_otp_request_success_payload(
			$code,
			$settings,
			$mobile,
			esc_html__( 'کد تأیید ارسال شد.', 'tadris' )
		)
	);
}
add_action( 'wp_ajax_webmz_otp_request', 'webmz_otp_ajax_request' );
add_action( 'wp_ajax_nopriv_webmz_otp_request', 'webmz_otp_ajax_request' );

/** AJAX: verify OTP and login/register. */
function webmz_otp_ajax_verify() {
	check_ajax_referer( 'webmz_otp_auth', 'nonce' );
	webmz_otp_firewall_guard();
	$settings = webmz_otp_get_settings();
	$mobile = isset( $_POST['mobile'] ) ? webmz_otp_normalize_mobile( wp_unslash( $_POST['mobile'] ) ) : '';
	$code   = isset( $_POST['code'] ) ? preg_replace( '/\D/', '', wp_unslash( $_POST['code'] ) ) : '';
	if ( ! $mobile || ! $code ) {
		wp_send_json_error( array( 'message' => esc_html__( 'شماره یا کد تأیید معتبر نیست.', 'tadris' ) ), 400 );
	}
	if ( ! webmz_otp_mobile_prefix_is_allowed( $mobile ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'پیشوند این شماره موبایل مجاز نیست.', 'tadris' ) ), 400 );
	}

	$transient_key = webmz_otp_transient_key( 'code', $mobile );
	$data = get_transient( $transient_key );
	if ( ! is_array( $data ) || empty( $data['hash'] ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'کد منقضی شده است. دوباره کد بگیرید.', 'tadris' ) ), 400 );
	}

	$attempts = isset( $data['attempts'] ) ? absint( $data['attempts'] ) : 0;
	if ( ! wp_check_password( $code, $data['hash'] ) ) {
		$attempts++;
		$data['attempts'] = $attempts;
		if ( $attempts >= absint( $settings['max_attempts'] ) ) {
			delete_transient( $transient_key );
			set_transient( webmz_otp_transient_key( 'lock', $mobile ), 1, absint( $settings['lockout_minutes'] ) * MINUTE_IN_SECONDS );
		}
		set_transient( $transient_key, $data, absint( $settings['otp_ttl'] ) );
		wp_send_json_error( array( 'message' => esc_html__( 'کد وارد شده صحیح نیست.', 'tadris' ) ), 400 );
	}

	$user_id = webmz_otp_find_user_by_mobile( $mobile );
	$is_new  = false;
	if ( ! $user_id ) {
		$user_id = webmz_otp_create_user( $mobile );
		if ( is_wp_error( $user_id ) ) {
			wp_send_json_error( array( 'message' => $user_id->get_error_message() ), 500 );
		}
		$is_new = true;
	}

	update_user_meta( $user_id, WEBMZ_OTP_META_KEY, $mobile );
	update_user_meta( $user_id, 'billing_phone', $mobile );
	delete_transient( $transient_key );
	webmz_otp_login_user( $user_id );

	$redirect = isset( $_POST['redirect'] ) ? esc_url_raw( wp_unslash( $_POST['redirect'] ) ) : '';
	if ( empty( $redirect ) ) {
		$redirect = wp_get_referer();
	}
	if ( empty( $redirect ) && function_exists( 'wc_get_checkout_url' ) ) {
		$redirect = wc_get_checkout_url();
	}

	wp_send_json_success( array(
		'message'  => $is_new ? esc_html__( 'ثبت‌نام با موفقیت انجام شد.', 'tadris' ) : esc_html__( 'ورود با موفقیت انجام شد.', 'tadris' ),
		'redirect' => wp_validate_redirect( $redirect, home_url( '/' ) ),
	) );
}
add_action( 'wp_ajax_webmz_otp_verify', 'webmz_otp_ajax_verify' );
add_action( 'wp_ajax_nopriv_webmz_otp_verify', 'webmz_otp_ajax_verify' );

/** Hidden honeypot field for bot firewall. */
function webmz_otp_render_firewall_honeypot() {
	$s = webmz_otp_get_settings();
	if ( 'yes' !== $s['firewall_enabled'] || 'yes' !== $s['firewall_honeypot'] ) {
		return;
	}
	?>
	<div class="webmz-otp-hp" aria-hidden="true">
		<label>
			<span><?php esc_html_e( 'وب‌سایت', 'tadris' ); ?></span>
			<input type="text" name="webmz_otp_hp" value="" tabindex="-1" autocomplete="off" data-webmz-otp-hp>
		</label>
	</div>
	<?php
}

/** Render OTP form. */
function webmz_otp_render_form( $args = array() ) {
	if ( is_user_logged_in() ) {
		return '<div class="webmz-otp-logged-in">' . esc_html__( 'شما وارد حساب کاربری شده‌اید.', 'tadris' ) . '</div>';
	}
	$s = webmz_otp_get_settings();
	$args = wp_parse_args( $args, array( 'mode' => 'inline', 'redirect' => '' ) );
	$redirect = $args['redirect'] ? esc_url( $args['redirect'] ) : esc_url( ( is_ssl() ? 'https://' : 'http://' ) . ( isset( $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ) : home_url( '/' ) ) );
	ob_start();
	?>
	<div class="webmz-otp-auth webmz-otp-auth--<?php echo esc_attr( $args['mode'] ); ?>" data-webmz-otp-auth data-redirect="<?php echo esc_url( $redirect ); ?>">
		<div class="webmz-otp-auth__head">
			<div class="webmz-otp-auth__icon" aria-hidden="true">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-lock-open"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2l0 -6" /><path d="M11 16a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M8 11v-5a4 4 0 0 1 8 0" /></svg>
			</div>
			<div class="webmz-otp-auth__titles">
				<strong><?php esc_html_e( 'ورود یا ثبت‌نام', 'tadris' ); ?></strong>
				<span><?php esc_html_e( 'شماره موبایل خود را وارد کنید تا کد تأیید برایتان ارسال شود.', 'tadris' ); ?></span>
			</div>
		</div>
		<div class="webmz-otp-auth__message" data-webmz-otp-message hidden></div>
		<?php webmz_otp_render_firewall_honeypot(); ?>
		<div class="webmz-otp-step is-active" data-step="mobile">
			<label class="webmz-otp-field"><span><?php esc_html_e( 'شماره موبایل', 'tadris' ); ?></span><span class="webmz-otp-input-wrap">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-device-mobile"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6 5a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-14" /><path d="M11 4h2" /><path d="M12 17v.01" /></svg>
			<input type="tel" inputmode="numeric" autocomplete="tel" data-webmz-otp-mobile placeholder="09123456789"></span></label>
			<button type="button" class="webmz-otp-button" data-webmz-otp-send><?php esc_html_e( 'دریافت کد تأیید', 'tadris' ); ?></button>
		</div>
		<div class="webmz-otp-step" data-step="code">
			<p class="webmz-otp-auth__hint" data-webmz-otp-hint></p>
			<label class="webmz-otp-field"><span><?php esc_html_e( 'کد تأیید', 'tadris' ); ?></span><span class="webmz-otp-input-wrap">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-password"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 10v4" /><path d="M10 13l4 -2" /><path d="M10 11l4 2" /><path d="M5 10v4" /><path d="M3 13l4 -2" /><path d="M3 11l4 2" /><path d="M19 10v4" /><path d="M17 13l4 -2" /><path d="M17 11l4 2" /></svg>	
			<input type="text" inputmode="numeric" autocomplete="one-time-code" data-webmz-otp-code maxlength="<?php echo esc_attr( absint( $s['otp_length'] ) ); ?>" placeholder="<?php echo esc_attr( str_repeat( '•', absint( $s['otp_length'] ) ) ); ?>"></span></label>
			<div class="webmz-otp-actions"><button type="button" class="webmz-otp-button" data-webmz-otp-verify><?php esc_html_e( 'ورود به سایت', 'tadris' ); ?></button><button type="button" class="webmz-otp-link" data-webmz-otp-change><?php esc_html_e( 'تغییر شماره', 'tadris' ); ?></button></div>
			<button type="button" class="webmz-otp-resend" data-webmz-otp-resend disabled><?php esc_html_e( 'ارسال مجدد کد', 'tadris' ); ?></button>
		</div>
		<?php if ( 'yes' === $s['allow_google'] && ! empty( $s['google_client_id'] ) && ! empty( $s['google_client_secret'] ) ) : ?>
			<div class="webmz-otp-google"><span><?php esc_html_e( 'یا', 'tadris' ); ?></span><a href="<?php echo esc_url( webmz_otp_google_start_url( $redirect ) ); ?>"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.1 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.5-.4-3.5z"/><path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 15.1 18.9 12 24 12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.1 6.1 29.3 4 24 4 16.2 4 9.5 8.4 6.3 14.7z"/><path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.5-5.2l-6.2-5.2C29.3 35.1 26.8 36 24 36c-5.2 0-9.6-3.3-11.3-7.9l-6.5 5C9.4 39.6 16.1 44 24 44z"/><path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.3 4.2-4 5.6l6.2 5.2C36.9 39.3 44 34 44 24c0-1.3-.1-2.5-.4-3.5z"/></svg><span><?php echo esc_html( $s['google_button_label'] ); ?></span></a></div>
		<?php endif; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'webmz_otp_login', 'webmz_otp_render_form' );

/** Render global popup in footer. */
function webmz_otp_render_global_popup() {
	$s = webmz_otp_get_settings();
	if ( ! webmz_otp_is_enabled() || is_user_logged_in() ) {
		return;
	}

	$needs_popup = 'yes' === $s['auto_popup'] || 'yes' === $s['require_login_download'];
	if ( ! $needs_popup ) {
		return;
	}
	?>
	<div class="webmz-otp-popup" data-webmz-otp-popup hidden>
		<div class="webmz-otp-popup__overlay" data-webmz-otp-close></div>
		<div class="webmz-otp-popup__box" role="dialog" aria-modal="true">
			<button type="button" class="webmz-otp-popup__close" data-webmz-otp-close aria-label="<?php esc_attr_e( 'بستن', 'tadris' ); ?>"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
			<?php echo webmz_otp_render_form( array( 'mode' => 'popup' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'webmz_otp_render_global_popup', 30 );

/** Add class to login/account buttons for popup opening. */
function webmz_otp_login_url( $url, $redirect = '' ) {
	return add_query_arg( 'webmz-login', '1', $url );
}

/** WooCommerce replace login/register forms. */
function webmz_otp_wc_login_form_replacement() {
	$s = webmz_otp_get_settings();
	if ( ! webmz_otp_is_enabled() || is_user_logged_in() || 'yes' !== $s['replace_wc_forms'] ) {
		return;
	}
	echo '<div class="webmz-otp-wc-replacement">' . webmz_otp_render_form( array( 'mode' => 'woocommerce', 'redirect' => wc_get_page_permalink( 'myaccount' ) ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<style>.woocommerce form.login,.woocommerce form.register,.woocommerce #customer_login{display:none!important}</style>';
}
add_action( 'woocommerce_before_customer_login_form', 'webmz_otp_wc_login_form_replacement', 5 );

/** Show OTP form before checkout if login is required. */
function webmz_otp_checkout_login_notice() {
	$s = webmz_otp_get_settings();
	if ( ! webmz_otp_is_enabled() || is_user_logged_in() || 'yes' !== $s['require_otp_checkout'] || ! function_exists( 'WC' ) ) {
		return;
	}
	if ( 'no' === get_option( 'woocommerce_enable_guest_checkout', 'yes' ) ) {
		echo '<div class="webmz-otp-checkout-gate">' . webmz_otp_render_form( array( 'mode' => 'checkout', 'redirect' => wc_get_checkout_url() ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
add_action( 'woocommerce_before_checkout_form', 'webmz_otp_checkout_login_notice', 3 );


/** Hide the default WooCommerce login reminder when OTP checkout gate is active. */
function webmz_otp_hide_checkout_login_reminder() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_user_logged_in() || ! function_exists( 'WC' ) ) {
		return;
	}

	$s = webmz_otp_get_settings();
	if ( ! webmz_otp_is_enabled() || 'yes' !== $s['require_otp_checkout'] || 'no' !== get_option( 'woocommerce_enable_guest_checkout', 'yes' ) ) {
		return;
	}

	?>
	<style id="webmz-otp-hide-wc-login-reminder">
		.woocommerce-form-login-toggle,
		.woocommerce .woocommerce-form-login-toggle,
		.woocommerce form.checkout_coupon + .woocommerce-form-login-toggle {
			display: none !important;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'webmz_otp_hide_checkout_login_reminder', 40 );

/** Add verified mobile-change UI to WooCommerce edit-account. */
function webmz_otp_edit_account_field() {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$mobile = get_user_meta( get_current_user_id(), WEBMZ_OTP_META_KEY, true );
	?>
	<div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide webmz-otp-account-mobile" data-webmz-otp-account-mobile>
		<?php webmz_otp_render_firewall_honeypot(); ?>
		<label><?php esc_html_e( 'شماره موبایل تأییدشده', 'tadris' ); ?></label>
		<div class="webmz-otp-account-mobile__current">
			<input type="tel" class="woocommerce-Input input-text" value="<?php echo esc_attr( $mobile ); ?>" readonly data-webmz-account-current-mobile>
		</div>
		<div class="webmz-otp-account-mobile__change">
			<label for="webmz_account_new_mobile"><?php esc_html_e( 'تغییر شماره موبایل', 'tadris' ); ?></label>
			<div class="webmz-otp-account-mobile__row">
				<input type="tel" class="woocommerce-Input input-text" id="webmz_account_new_mobile" value="" placeholder="09123456789" data-webmz-account-new-mobile>
				<button type="button" class="webmz-otp-account-btn" data-webmz-account-mobile-send><?php esc_html_e( 'ارسال کد تأیید', 'tadris' ); ?></button>
			</div>
		</div>
		<div class="webmz-otp-account-mobile__verify" data-webmz-account-mobile-verify-box hidden>
			<label for="webmz_account_mobile_code"><?php esc_html_e( 'کد تأیید شماره جدید', 'tadris' ); ?></label>
			<div class="webmz-otp-account-mobile__row">
				<input type="text" inputmode="numeric" autocomplete="one-time-code" class="woocommerce-Input input-text webmz-otp-code-input" id="webmz_account_mobile_code" maxlength="<?php echo esc_attr( absint( webmz_otp_get_settings()['otp_length'] ) ); ?>" placeholder="<?php echo esc_attr( str_repeat( '•', absint( webmz_otp_get_settings()['otp_length'] ) ) ); ?>" data-webmz-account-mobile-code>
				<button type="button" class="webmz-otp-account-btn webmz-otp-account-btn--primary" data-webmz-account-mobile-verify><?php esc_html_e( 'تأیید و ذخیره شماره', 'tadris' ); ?></button>
			</div>
		</div>
		<div class="webmz-otp-account-mobile__message" data-webmz-account-mobile-message hidden></div>
		<p class="webmz-otp-account-mobile__hint"><?php esc_html_e( 'برای تغییر شماره موبایل، کد تأیید به شماره جدید ارسال می‌شود و بعد از تأیید، شماره حساب و فیلد تلفن تسویه‌حساب ووکامرس به‌روزرسانی خواهد شد.', 'tadris' ); ?></p>
	</div>
	<?php
}
add_action( 'woocommerce_edit_account_form', 'webmz_otp_edit_account_field' );

/**
 * Direct mobile edits from the account form are intentionally ignored.
 * The number must be changed through OTP verification for the new mobile.
 */
function webmz_otp_save_account_field( $user_id ) {
	return;
}
add_action( 'woocommerce_save_account_details', 'webmz_otp_save_account_field' );

/** AJAX: send OTP for changing the logged-in user's mobile number. */
function webmz_otp_ajax_account_mobile_request() {
	check_ajax_referer( 'webmz_otp_auth', 'nonce' );
	webmz_otp_firewall_guard();

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( 'برای تغییر شماره باید وارد حساب کاربری باشید.', 'tadris' ) ), 403 );
	}

	if ( ! webmz_otp_is_enabled() ) {
		wp_send_json_error( array( 'message' => esc_html__( 'ورود پیامکی در حال حاضر غیرفعال است.', 'tadris' ) ), 403 );
	}

	$settings = webmz_otp_get_settings();
	$user_id  = get_current_user_id();
	$mobile   = isset( $_POST['mobile'] ) ? webmz_otp_normalize_mobile( wp_unslash( $_POST['mobile'] ) ) : '';

	if ( ! $mobile ) {
		wp_send_json_error( array( 'message' => esc_html__( 'شماره موبایل ایران را به‌درستی وارد کنید.', 'tadris' ) ), 400 );
	}
	if ( ! webmz_otp_mobile_prefix_is_allowed( $mobile ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'پیشوند این شماره موبایل مجاز نیست.', 'tadris' ) ), 400 );
	}

	$current_mobile = webmz_otp_normalize_mobile( get_user_meta( $user_id, WEBMZ_OTP_META_KEY, true ) );
	if ( $current_mobile && $current_mobile === $mobile ) {
		wp_send_json_error( array( 'message' => esc_html__( 'این شماره همین حالا روی حساب شما ثبت شده است.', 'tadris' ) ), 400 );
	}

	$existing = webmz_otp_find_user_by_mobile( $mobile );
	if ( $existing && (int) $existing !== (int) $user_id ) {
		wp_send_json_error( array( 'message' => esc_html__( 'این شماره موبایل قبلاً برای حساب دیگری ثبت شده است.', 'tadris' ) ), 409 );
	}

	$cooldown_key = webmz_otp_transient_key( 'account_mobile_cooldown_' . $user_id, $mobile );
	$last_sent    = absint( get_transient( $cooldown_key ) );
	if ( $last_sent && time() - $last_sent < absint( $settings['resend_seconds'] ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'برای ارسال مجدد کمی صبر کنید.', 'tadris' ), 'wait' => absint( $settings['resend_seconds'] ) - ( time() - $last_sent ) ), 429 );
	}

	$phone_hour_count = webmz_otp_increment_counter( webmz_otp_transient_key( 'account_hour_phone', $mobile ), HOUR_IN_SECONDS );
	if ( $phone_hour_count > absint( $settings['max_phone_hour'] ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'تعداد درخواست‌های این شماره بیش از حد مجاز است. کمی بعد دوباره تلاش کنید.', 'tadris' ) ), 429 );
	}

	$ip_hour_count = webmz_otp_increment_counter( webmz_otp_transient_key( 'account_hour_ip', webmz_otp_client_ip() ), HOUR_IN_SECONDS );
	if ( $ip_hour_count > absint( $settings['max_ip_hour'] ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'تعداد درخواست‌های این اتصال بیش از حد مجاز است. کمی بعد دوباره تلاش کنید.', 'tadris' ) ), 429 );
	}

	$code    = (string) webmz_otp_generate_code( $settings['otp_length'] );
	$is_mock = webmz_otp_is_mock_mode();
	if ( $is_mock ) {
		$code = webmz_otp_demo_code();
	}
	$result = webmz_otp_send_sms( $mobile, $code, 'account_mobile_change' );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'ارسال پیامک ناموفق بود. لطفاً کمی بعد دوباره تلاش کنید.', 'tadris' ) ), 500 );
	}

	set_transient( webmz_otp_transient_key( 'account_mobile_code_' . $user_id, $mobile ), array( 'hash' => wp_hash_password( $code ), 'attempts' => 0, 'created' => time() ), absint( $settings['otp_ttl'] ) );
	set_transient( $cooldown_key, time(), absint( $settings['resend_seconds'] ) );

	$response = array(
		'message' => $is_mock
			? esc_html__( 'کد در حالت دمو 0000 است.', 'tadris' )
			: esc_html__( 'کد تأیید برای شماره جدید ارسال شد.', 'tadris' ),
		'resend'  => absint( $settings['resend_seconds'] ),
	);

	if ( $is_mock ) {
		$response['mock']     = true;
		$response['code']     = $code;
		$response['otp']      = $code;
		$response['demo_otp'] = $code;
	}

	wp_send_json_success( $response );
}
add_action( 'wp_ajax_webmz_otp_account_mobile_request', 'webmz_otp_ajax_account_mobile_request' );

/** AJAX: verify and save logged-in user's new mobile number. */
function webmz_otp_ajax_account_mobile_verify() {
	check_ajax_referer( 'webmz_otp_auth', 'nonce' );
	webmz_otp_firewall_guard();

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( 'برای تغییر شماره باید وارد حساب کاربری باشید.', 'tadris' ) ), 403 );
	}

	$settings = webmz_otp_get_settings();
	$user_id  = get_current_user_id();
	$mobile   = isset( $_POST['mobile'] ) ? webmz_otp_normalize_mobile( wp_unslash( $_POST['mobile'] ) ) : '';
	$code     = isset( $_POST['code'] ) ? preg_replace( '/\D/', '', wp_unslash( $_POST['code'] ) ) : '';

	if ( ! $mobile || ! $code ) {
		wp_send_json_error( array( 'message' => esc_html__( 'شماره یا کد تأیید معتبر نیست.', 'tadris' ) ), 400 );
	}
	if ( ! webmz_otp_mobile_prefix_is_allowed( $mobile ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'پیشوند این شماره موبایل مجاز نیست.', 'tadris' ) ), 400 );
	}

	$existing = webmz_otp_find_user_by_mobile( $mobile );
	if ( $existing && (int) $existing !== (int) $user_id ) {
		wp_send_json_error( array( 'message' => esc_html__( 'این شماره موبایل قبلاً برای حساب دیگری ثبت شده است.', 'tadris' ) ), 409 );
	}

	$transient_key = webmz_otp_transient_key( 'account_mobile_code_' . $user_id, $mobile );
	$data          = get_transient( $transient_key );
	if ( ! is_array( $data ) || empty( $data['hash'] ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'کد منقضی شده است. دوباره کد بگیرید.', 'tadris' ) ), 400 );
	}

	$attempts = isset( $data['attempts'] ) ? absint( $data['attempts'] ) : 0;
	if ( ! wp_check_password( $code, $data['hash'] ) ) {
		$attempts++;
		$data['attempts'] = $attempts;
		set_transient( $transient_key, $data, absint( $settings['otp_ttl'] ) );
		wp_send_json_error( array( 'message' => esc_html__( 'کد وارد شده صحیح نیست.', 'tadris' ) ), 400 );
	}

	update_user_meta( $user_id, WEBMZ_OTP_META_KEY, $mobile );
	update_user_meta( $user_id, 'billing_phone', $mobile );
	update_user_meta( $user_id, 'shipping_phone', $mobile );
	delete_transient( $transient_key );

	wp_send_json_success( array( 'message' => esc_html__( 'شماره موبایل با موفقیت تغییر کرد.', 'tadris' ), 'mobile' => $mobile ) );
}
add_action( 'wp_ajax_webmz_otp_account_mobile_verify', 'webmz_otp_ajax_account_mobile_verify' );

/** Prefill Woo checkout phone from OTP mobile. */
function webmz_otp_checkout_default_value( $value, $input ) {
	if ( 'billing_phone' === $input && is_user_logged_in() && empty( $value ) ) {
		$mobile = get_user_meta( get_current_user_id(), WEBMZ_OTP_META_KEY, true );
		return $mobile ? $mobile : $value;
	}
	return $value;
}
add_filter( 'woocommerce_checkout_get_value', 'webmz_otp_checkout_default_value', 10, 2 );

/** Google login start URL. */
function webmz_otp_google_redirect_uri() {
	return add_query_arg( 'webmz_google_callback', '1', home_url( '/' ) );
}
function webmz_otp_google_start_url( $redirect = '' ) {
	return add_query_arg(
		array(
			'webmz_google_login' => '1',
			'redirect'           => rawurlencode( $redirect ? $redirect : home_url( '/' ) ),
		),
		home_url( '/' )
	);
}

/** Start Google login before headers are sent. */
function webmz_otp_handle_google_start() {
	if ( empty( $_GET['webmz_google_login'] ) ) { return; }
	$settings = webmz_otp_get_settings();
	if ( 'yes' !== $settings['allow_google'] || empty( $settings['google_client_id'] ) || empty( $settings['google_client_secret'] ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
	$redirect = isset( $_GET['redirect'] ) ? esc_url_raw( rawurldecode( wp_unslash( $_GET['redirect'] ) ) ) : home_url( '/' );
	$state = wp_generate_password( 32, false, false );
	set_transient( webmz_otp_transient_key( 'google_state', $state ), wp_validate_redirect( $redirect, home_url( '/' ) ), 10 * MINUTE_IN_SECONDS );
	setcookie( 'webmz_google_state', $state, time() + 600, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
	$url = add_query_arg( array( 'client_id' => $settings['google_client_id'], 'redirect_uri' => webmz_otp_google_redirect_uri(), 'response_type' => 'code', 'scope' => 'openid email profile', 'state' => $state, 'prompt' => 'select_account' ), 'https://accounts.google.com/o/oauth2/v2/auth' );
	wp_safe_redirect( $url );
	exit;
}
add_action( 'init', 'webmz_otp_handle_google_start' );

/** Handle Google callback. */
function webmz_otp_handle_google_callback() {
	if ( empty( $_GET['webmz_google_callback'] ) ) { return; }
	$settings = webmz_otp_get_settings();
	$state = isset( $_GET['state'] ) ? sanitize_text_field( wp_unslash( $_GET['state'] ) ) : '';
	$cookie_state = isset( $_COOKIE['webmz_google_state'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['webmz_google_state'] ) ) : '';
	$redirect = $state ? get_transient( webmz_otp_transient_key( 'google_state', $state ) ) : '';
	if ( ! $state || ! $cookie_state || ! hash_equals( $cookie_state, $state ) || ! $redirect ) {
		wp_die( esc_html__( 'درخواست ورود گوگل معتبر نیست.', 'tadris' ) );
	}
	$code = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : '';
	if ( ! $code ) { wp_die( esc_html__( 'کد گوگل دریافت نشد.', 'tadris' ) ); }
	$token = wp_remote_post( 'https://oauth2.googleapis.com/token', array( 'timeout' => 15, 'body' => array( 'code' => $code, 'client_id' => $settings['google_client_id'], 'client_secret' => $settings['google_client_secret'], 'redirect_uri' => webmz_otp_google_redirect_uri(), 'grant_type' => 'authorization_code' ) ) );
	if ( is_wp_error( $token ) ) { wp_die( esc_html__( 'خطا در اتصال به گوگل.', 'tadris' ) ); }
	$body = json_decode( wp_remote_retrieve_body( $token ), true );
	if ( empty( $body['access_token'] ) ) { wp_die( esc_html__( 'توکن گوگل معتبر نیست.', 'tadris' ) ); }
	$userinfo = wp_remote_get( 'https://openidconnect.googleapis.com/v1/userinfo', array( 'timeout' => 15, 'headers' => array( 'Authorization' => 'Bearer ' . $body['access_token'] ) ) );
	$info = json_decode( wp_remote_retrieve_body( $userinfo ), true );
	$email = isset( $info['email'] ) ? sanitize_email( $info['email'] ) : '';
	$sub   = isset( $info['sub'] ) ? sanitize_text_field( $info['sub'] ) : '';
	if ( ! $email || ! is_email( $email ) || ! $sub ) { wp_die( esc_html__( 'اطلاعات حساب گوگل کامل نیست.', 'tadris' ) ); }
	$user = get_user_by( 'email', $email );
	if ( ! $user ) {
		$user_id = wp_insert_user( array( 'user_login' => webmz_otp_generate_username(), 'user_pass' => wp_generate_password( 24, true, true ), 'user_email' => $email, 'display_name' => isset( $info['name'] ) ? sanitize_text_field( $info['name'] ) : $email, 'role' => class_exists( 'WooCommerce' ) ? 'customer' : get_option( 'default_role', 'subscriber' ) ) );
		if ( is_wp_error( $user_id ) ) { wp_die( esc_html__( 'ایجاد کاربر گوگل ناموفق بود.', 'tadris' ) ); }
	} else {
		$user_id = $user->ID;
	}
	update_user_meta( $user_id, '_webmz_google_sub', $sub );
	delete_transient( webmz_otp_transient_key( 'google_state', $state ) );
	webmz_otp_login_user( $user_id );
	wp_safe_redirect( wp_validate_redirect( $redirect, home_url( '/' ) ) );
	exit;
}
add_action( 'init', 'webmz_otp_handle_google_callback' );

/** Send SMS on ticket answer. */
function webmz_otp_ticket_sms_notification( $ticket_id ) {
	$s = webmz_otp_get_settings();
	if ( 'yes' !== $s['send_ticket_sms'] ) { return; }
	$user_id = (int) get_post_field( 'post_author', absint( $ticket_id ) );
	$mobile = $user_id ? get_user_meta( $user_id, WEBMZ_OTP_META_KEY, true ) : '';
	$mobile = $mobile ? $mobile : get_user_meta( $user_id, 'billing_phone', true );
	$mobile = webmz_otp_normalize_mobile( $mobile );
	if ( ! $mobile ) { return; }
	$msg = webmz_otp_template( $s['ticket_sms_template'], $mobile, '', array( '{url}' => webmz_ticket_get_account_url( $ticket_id ), '{ticket_id}' => absint( $ticket_id ) ) );
	webmz_otp_send_sms( $mobile, '', 'ticket', $msg );
}
add_action( 'webmz_ticket_user_answered_sms', 'webmz_otp_ticket_sms_notification' );

/** Send SMS on successful WooCommerce order. */
function webmz_otp_order_success_sms( $order_id ) {
	$s = webmz_otp_get_settings();
	if ( 'yes' !== $s['send_order_sms'] || ! function_exists( 'wc_get_order' ) ) { return; }
	$order = wc_get_order( $order_id );
	if ( ! $order || $order->get_meta( '_webmz_otp_order_sms_sent' ) ) { return; }
	$mobile = webmz_otp_normalize_mobile( $order->get_billing_phone() );
	if ( ! $mobile && $order->get_user_id() ) {
		$mobile = webmz_otp_normalize_mobile( get_user_meta( $order->get_user_id(), WEBMZ_OTP_META_KEY, true ) );
	}
	if ( ! $mobile ) { return; }
	$msg = webmz_otp_template( $s['order_sms_template'], $mobile, '', array( '{order_id}' => $order->get_order_number(), '{total}' => wp_strip_all_tags( $order->get_formatted_order_total() ) ) );
	$result = webmz_otp_send_sms( $mobile, '', 'order', $msg );
	if ( true === $result ) { $order->update_meta_data( '_webmz_otp_order_sms_sent', '1' ); $order->save(); }
}
add_action( 'woocommerce_payment_complete', 'webmz_otp_order_success_sms' );
add_action( 'woocommerce_thankyou', 'webmz_otp_order_success_sms', 20 );

/** Render admin settings panel. */
function webmz_otp_render_settings_panel() {
	$s = webmz_otp_get_settings();
	$providers = webmz_otp_provider_labels();
	$google_console_url = 'https://console.cloud.google.com/';
	$google_consent_url = 'https://console.cloud.google.com/apis/credentials/consent';
	$google_credentials_url = 'https://console.cloud.google.com/apis/credentials';
	$google_docs_url = 'https://developers.google.com/identity/protocols/oauth2/web-server';
	?>
	<section class="webmz-panel" data-panel="otp">
		<div class="webmz-card">
			<h2><?php esc_html_e( 'ورود و ثبت‌نام پیامکی OTP', 'tadris' ); ?></h2>
			<div class="webmz-grid webmz-grid--2">
				<label class="webmz-toggle"><input type="checkbox" name="otp[enabled]" value="yes" <?php checked( $s['enabled'], 'yes' ); ?>><span><?php esc_html_e( 'فعال‌سازی سیستم OTP', 'tadris' ); ?></span></label>
				<label class="webmz-toggle"><input type="checkbox" name="otp[replace_wc_forms]" value="yes" <?php checked( $s['replace_wc_forms'], 'yes' ); ?>><span><?php esc_html_e( 'جایگزینی فرم‌های ورود ووکامرس', 'tadris' ); ?></span></label>
				<label class="webmz-toggle"><input type="checkbox" name="otp[auto_popup]" value="yes" <?php checked( $s['auto_popup'], 'yes' ); ?>><span><?php esc_html_e( 'فعال‌سازی پاپ‌آپ ورود/ثبت‌نام', 'tadris' ); ?></span></label>
				<label class="webmz-toggle"><input type="checkbox" name="otp[require_otp_checkout]" value="yes" <?php checked( $s['require_otp_checkout'], 'yes' ); ?>><span><?php esc_html_e( 'اجبار ورود OTP در تسویه‌حساب', 'tadris' ); ?></span></label>
				<label class="webmz-toggle"><input type="checkbox" name="otp[require_login_download]" value="yes" <?php checked( $s['require_login_download'], 'yes' ); ?>><span><?php esc_html_e( 'ورود اجباری برای دسترسی به باکس دانلود', 'tadris' ); ?></span></label>
			</div>
		</div>

		<div class="webmz-card">
			<h2><?php esc_html_e( 'اتصال به پنل پیامکی', 'tadris' ); ?></h2>
			<div class="webmz-grid webmz-grid--2">
				<label class="webmz-field"><span><?php esc_html_e( 'پنل پیامکی', 'tadris' ); ?></span><select name="otp[provider]"><?php foreach ( $providers as $key => $label ) : ?><option value="<?php echo esc_attr( $key ); ?>" <?php selected( $s['provider'], $key ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></label>
				<label class="webmz-field"><span><?php esc_html_e( 'روش ارسال', 'tadris' ); ?></span><select name="otp[send_mode]"><option value="pattern" <?php selected( $s['send_mode'], 'pattern' ); ?>><?php esc_html_e( 'پترن/خدماتی', 'tadris' ); ?></option><option value="simple" <?php selected( $s['send_mode'], 'simple' ); ?>><?php esc_html_e( 'ارسال ساده', 'tadris' ); ?></option></select></label>
				<label class="webmz-field"><span>API Key / Token</span><input type="text" name="otp[api_key]" value="<?php echo esc_attr( $s['api_key'] ); ?>" autocomplete="off"></label>
				<label class="webmz-field"><span><?php esc_html_e( 'نام کاربری', 'tadris' ); ?></span><input type="text" name="otp[username]" value="<?php echo esc_attr( $s['username'] ); ?>" autocomplete="off"></label>
				<label class="webmz-field"><span><?php esc_html_e( 'رمز عبور', 'tadris' ); ?></span><input type="password" name="otp[password]" value="<?php echo esc_attr( $s['password'] ); ?>" autocomplete="new-password"></label>
				<label class="webmz-field"><span><?php esc_html_e( 'خط ارسال‌کننده', 'tadris' ); ?></span><input type="text" name="otp[sender]" value="<?php echo esc_attr( $s['sender'] ); ?>"></label>
				<label class="webmz-field"><span><?php esc_html_e( 'شناسه پترن / قالب', 'tadris' ); ?></span><input type="text" name="otp[pattern_id]" value="<?php echo esc_attr( $s['pattern_id'] ); ?>"></label>
				<label class="webmz-field"><span><?php esc_html_e( 'نام متغیر کد در پترن', 'tadris' ); ?></span><input type="text" name="otp[pattern_variable]" value="<?php echo esc_attr( $s['pattern_variable'] ); ?>" placeholder="code"></label>
			</div>
			<label class="webmz-field"><span><?php esc_html_e( 'متن پیامک ساده', 'tadris' ); ?></span><textarea name="otp[message_template]" rows="3"><?php echo esc_textarea( $s['message_template'] ); ?></textarea><small>{code} {mobile} {site}</small></label>
			<div class="webmz-grid webmz-grid--2" style="margin-top:14px">
				<label class="webmz-toggle"><input type="checkbox" name="otp[fast_send]" value="yes" <?php checked( $s['fast_send'], 'yes' ); ?>><span><?php esc_html_e( 'ارسال سریع OTP', 'tadris' ); ?></span></label>
				<label class="webmz-field"><span><?php esc_html_e( 'زمان انتظار اتصال پیامکی / ثانیه', 'tadris' ); ?></span><input type="number" name="otp[sms_timeout]" value="<?php echo esc_attr( $s['sms_timeout'] ); ?>" min="5" max="60"></label>
			</div>
		</div>

		<div class="webmz-card">
			<h2><?php esc_html_e( 'فایروال شناسایی ربات', 'tadris' ); ?></h2>
			<p><?php esc_html_e( 'برای جلوگیری از حملات خودکار روی ورود و ثبت‌نام، درخواست‌های OTP با هانی‌پات، توکن زمانی و بررسی مرورگر فیلتر می‌شوند.', 'tadris' ); ?></p>
			<div class="webmz-grid webmz-grid--2" style="margin-top:14px">
				<label class="webmz-toggle"><input type="checkbox" name="otp[firewall_enabled]" value="yes" <?php checked( $s['firewall_enabled'], 'yes' ); ?>><span><?php esc_html_e( 'فعال‌سازی فایروال ربات', 'tadris' ); ?></span></label>
				<label class="webmz-toggle"><input type="checkbox" name="otp[firewall_honeypot]" value="yes" <?php checked( $s['firewall_honeypot'], 'yes' ); ?>><span><?php esc_html_e( 'فیلد هانی‌پات مخفی', 'tadris' ); ?></span></label>
				<label class="webmz-toggle"><input type="checkbox" name="otp[firewall_block_empty_ua]" value="yes" <?php checked( $s['firewall_block_empty_ua'], 'yes' ); ?>><span><?php esc_html_e( 'مسدودسازی درخواست بدون User-Agent', 'tadris' ); ?></span></label>
			</div>
			<div class="webmz-grid webmz-grid--2" style="margin-top:14px">
				<label class="webmz-field"><span><?php esc_html_e( 'حداقل زمان قبل از ارسال / ثانیه', 'tadris' ); ?></span><input type="number" name="otp[firewall_min_seconds]" value="<?php echo esc_attr( $s['firewall_min_seconds'] ); ?>" min="0" max="30"></label>
				<label class="webmz-field"><span><?php esc_html_e( 'اعتبار توکن فرم / ثانیه', 'tadris' ); ?></span><input type="number" name="otp[firewall_max_seconds]" value="<?php echo esc_attr( $s['firewall_max_seconds'] ); ?>" min="60" max="3600"></label>
			</div>
		</div>

		<div class="webmz-card">
			<h2><?php esc_html_e( 'وایت‌لیست پیشوند شماره موبایل', 'tadris' ); ?></h2>
			<p><?php esc_html_e( 'فقط شماره‌هایی با پیشوندهای مجاز می‌توانند کد OTP بگیرند. هر پیشوند را در یک خط وارد کنید (مثال: 0912 یا 912).', 'tadris' ); ?></p>
			<label class="webmz-toggle" style="margin-top:14px"><input type="checkbox" name="otp[prefix_whitelist_enabled]" value="yes" <?php checked( $s['prefix_whitelist_enabled'], 'yes' ); ?>><span><?php esc_html_e( 'فعال‌سازی وایت‌لیست پیشوند', 'tadris' ); ?></span></label>
			<label class="webmz-field" style="margin-top:14px"><span><?php esc_html_e( 'پیشوندهای مجاز ایران', 'tadris' ); ?></span><textarea name="otp[prefix_whitelist]" rows="10"><?php echo esc_textarea( $s['prefix_whitelist'] ); ?></textarea><small><?php esc_html_e( 'پیشوندهای پیش‌فرض شامل همراه اول، ایرانسل، رایتل، شاتل موبایل و MVNOها است.', 'tadris' ); ?></small></label>
		</div>

		<div class="webmz-card">
			<h2><?php esc_html_e( 'امنیت و محدودیت ارسال', 'tadris' ); ?></h2>
			<div class="webmz-grid webmz-grid--3">
				<label class="webmz-field"><span><?php esc_html_e( 'طول کد', 'tadris' ); ?></span><input type="number" name="otp[otp_length]" value="<?php echo esc_attr( $s['otp_length'] ); ?>" min="4" max="8"></label>
				<label class="webmz-field"><span><?php esc_html_e( 'اعتبار کد / ثانیه', 'tadris' ); ?></span><input type="number" name="otp[otp_ttl]" value="<?php echo esc_attr( $s['otp_ttl'] ); ?>" min="60" max="900"></label>
				<label class="webmz-field"><span><?php esc_html_e( 'ارسال مجدد بعد از / ثانیه', 'tadris' ); ?></span><input type="number" name="otp[resend_seconds]" value="<?php echo esc_attr( $s['resend_seconds'] ); ?>" min="20" max="300"></label>
				<label class="webmz-field"><span><?php esc_html_e( 'حداکثر ارسال هر شماره در ساعت', 'tadris' ); ?></span><input type="number" name="otp[max_phone_hour]" value="<?php echo esc_attr( $s['max_phone_hour'] ); ?>" min="1" max="30"></label>
				<label class="webmz-field"><span><?php esc_html_e( 'حداکثر ارسال هر IP در ساعت', 'tadris' ); ?></span><input type="number" name="otp[max_ip_hour]" value="<?php echo esc_attr( $s['max_ip_hour'] ); ?>" min="3" max="100"></label>
				<label class="webmz-field"><span><?php esc_html_e( 'حداکثر تلاش اشتباه', 'tadris' ); ?></span><input type="number" name="otp[max_attempts]" value="<?php echo esc_attr( $s['max_attempts'] ); ?>" min="3" max="10"></label>
				<label class="webmz-field"><span><?php esc_html_e( 'قفل بعد از خطا / دقیقه', 'tadris' ); ?></span><input type="number" name="otp[lockout_minutes]" value="<?php echo esc_attr( $s['lockout_minutes'] ); ?>" min="5" max="120"></label>
			</div>
		</div>

		<div class="webmz-card">
			<h2><?php esc_html_e( 'ورود با گوگل', 'tadris' ); ?></h2>
			<label class="webmz-toggle"><input type="checkbox" name="otp[allow_google]" value="yes" <?php checked( $s['allow_google'], 'yes' ); ?>><span><?php esc_html_e( 'نمایش ورود با گوگل زیر فرم موبایل', 'tadris' ); ?></span></label>
			<div class="webmz-grid webmz-grid--2">
				<label class="webmz-field"><span>Google Client ID</span><input type="text" name="otp[google_client_id]" value="<?php echo esc_attr( $s['google_client_id'] ); ?>" autocomplete="off"></label>
				<label class="webmz-field"><span>Google Client Secret</span><input type="password" name="otp[google_client_secret]" value="<?php echo esc_attr( $s['google_client_secret'] ); ?>" autocomplete="new-password"></label>
				<label class="webmz-field"><span><?php esc_html_e( 'متن دکمه گوگل', 'tadris' ); ?></span><input type="text" name="otp[google_button_label]" value="<?php echo esc_attr( $s['google_button_label'] ); ?>"></label>
				<label class="webmz-field"><span><?php esc_html_e( 'Redirect URI', 'tadris' ); ?></span><input type="text" readonly value="<?php echo esc_attr( webmz_otp_google_redirect_uri() ); ?>"></label>
			</div>
			<div class="webmz-google-guide" style="margin-top:16px;padding:16px;border:1px solid #e5e7eb;border-radius:16px;background:#f8fafc;color:#475569;line-height:2">
				<strong style="display:block;margin-bottom:8px;color:#0f172a"><?php esc_html_e( 'مراحل اتصال ورود با گوگل', 'tadris' ); ?></strong>
				<ol style="margin:0 18px 0 0;padding:0">
					<li><?php esc_html_e( 'در Google Cloud Console یک پروژه بسازید یا پروژه فعلی را انتخاب کنید.', 'tadris' ); ?> <a href="<?php echo esc_url( $google_console_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'ورود به Google Cloud Console', 'tadris' ); ?></a></li>
					<li><?php esc_html_e( 'در بخش OAuth consent screen اطلاعات اپلیکیشن، دامنه سایت و ایمیل پشتیبانی را کامل کنید.', 'tadris' ); ?> <a href="<?php echo esc_url( $google_consent_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'تنظیم OAuth consent screen', 'tadris' ); ?></a></li>
					<li><?php esc_html_e( 'در Credentials یک OAuth Client ID از نوع Web application بسازید.', 'tadris' ); ?> <a href="<?php echo esc_url( $google_credentials_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'ساخت Credentials', 'tadris' ); ?></a></li>
					<li><?php esc_html_e( 'آدرس Redirect URI بالا را در بخش Authorized redirect URIs وارد کنید.', 'tadris' ); ?></li>
					<li><?php esc_html_e( 'Client ID و Client Secret ساخته‌شده را در همین فرم وارد و ذخیره کنید.', 'tadris' ); ?> <a href="<?php echo esc_url( $google_docs_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'مستندات رسمی OAuth گوگل', 'tadris' ); ?></a></li>
				</ol>
			</div>
		</div>

		<div class="webmz-card">
			<h2><?php esc_html_e( 'وب‌سرویس سفارشی', 'tadris' ); ?></h2>
			<div class="webmz-grid webmz-grid--2">
				<label class="webmz-field"><span>URL</span><input type="url" name="otp[custom_url]" value="<?php echo esc_attr( $s['custom_url'] ); ?>"></label>
				<label class="webmz-field"><span>Method</span><select name="otp[custom_method]"><option value="POST" <?php selected( $s['custom_method'], 'POST' ); ?>>POST</option><option value="GET" <?php selected( $s['custom_method'], 'GET' ); ?>>GET</option></select></label>
			</div>
			<label class="webmz-field"><span>Headers</span><textarea name="otp[custom_headers]" rows="3" placeholder="Authorization: Bearer xxx"><?php echo esc_textarea( $s['custom_headers'] ); ?></textarea></label>
			<label class="webmz-field"><span>Body</span><textarea name="otp[custom_body]" rows="5" placeholder='{"mobile":"{mobile}","code":"{code}"}'><?php echo esc_textarea( $s['custom_body'] ); ?></textarea></label>
		</div>
	</section>
	<?php
}
