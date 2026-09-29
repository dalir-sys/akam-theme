<?php
/**
 * AI support chat (AvalAI / GapGPT or any OpenAI-compatible API).
 *
 * Visitors talk to WordPress (admin-ajax); WordPress calls the provider with the
 * site owner's API key, so the key never reaches the browser.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Known providers and their OpenAI-compatible API base URLs.
 *
 * @return array<string,array{label:string,base_url:string}>
 */
function webmz_ai_chat_providers() {
	return apply_filters(
		'webmz_ai_chat_providers',
		array(
			'avalai' => array(
				'label'    => 'AvalAI (اول‌ای‌آی)',
				'base_url' => 'https://api.avalai.ir/v1',
			),
			'gapgpt' => array(
				'label'    => 'GapGPT (گپ‌جی‌پی‌تی)',
				'base_url' => 'https://api.gapgpt.app/v1',
			),
			'custom' => array(
				'label'    => __( 'سرویس سازگار با OpenAI (آدرس دلخواه)', 'tadris' ),
				'base_url' => '',
			),
		)
	);
}

/**
 * Default settings.
 *
 * @return array<string,mixed>
 */
function webmz_ai_chat_default_settings() {
	return array(
		'enabled'             => 'no',
		'provider'            => 'avalai',
		'api_key'             => '',
		'base_url'            => '',
		'model'               => 'gpt-4o-mini',
		'bot_name'            => __( 'دستیار هوشمند', 'tadris' ),
		'welcome'             => __( 'سلام! 👋 من دستیار هوشمند سایت هستم. سوالتان درباره دوره‌ها، خرید یا استفاده از سایت را بپرسید.', 'tadris' ),
		'system_prompt'       => __( 'تو دستیار پشتیبانی این وب‌سایت هستی. به فارسی روان، کوتاه و دوستانه پاسخ بده. فقط بر اساس اطلاعات داده‌شده درباره سایت پاسخ بده و اگر پاسخ را نمی‌دانی، حدس نزن و کاربر را به پشتیبانی انسانی راهنمایی کن. درباره موضوعات نامرتبط با سایت پاسخ نده.', 'tadris' ),
		'knowledge'           => '',
		'include_products'    => 'yes',
		'suggestions'         => __( "چطور در دوره ثبت‌نام کنم؟\nروش‌های پرداخت چیست؟\nبعد از خرید به دوره چطور دسترسی دارم؟", 'tadris' ),
		'placeholder'         => __( 'پیام خود را بنویسید...', 'tadris' ),
		'fallback_text'       => __( 'گفتگو با پشتیبانی', 'tadris' ),
		'fallback_url'        => '',
		'visibility'          => 'all',
		'position'            => 'right',
		'offset_x'            => 24,
		'offset_bottom'       => 24,
		'color'               => '#0878f9',
		'temperature'         => 0.4,
		'max_tokens'          => 700,
		'history_limit'       => 10,
		'max_input_chars'     => 600,
		'rate_limit_per_hour' => 20,
	);
}

/**
 * Stored settings merged with defaults.
 *
 * @return array<string,mixed>
 */
function webmz_ai_chat_get_settings() {
	$stored = get_option( 'webmz_ai_chat_settings', array() );

	return wp_parse_args( is_array( $stored ) ? $stored : array(), webmz_ai_chat_default_settings() );
}

/**
 * Sanitize settings posted from the theme options page.
 *
 * An empty API key field keeps the saved key (the key is never printed back into the form).
 *
 * @param mixed $raw Raw input.
 * @return array<string,mixed>
 */
function webmz_ai_chat_sanitize_settings( $raw ) {
	$raw      = is_array( $raw ) ? $raw : array();
	$defaults = webmz_ai_chat_default_settings();
	$current  = webmz_ai_chat_get_settings();
	$clean    = array();

	$clean['enabled']          = isset( $raw['enabled'] ) && 'yes' === $raw['enabled'] ? 'yes' : 'no';
	$clean['include_products'] = isset( $raw['include_products'] ) && 'yes' === $raw['include_products'] ? 'yes' : 'no';

	$provider          = isset( $raw['provider'] ) ? sanitize_key( $raw['provider'] ) : $defaults['provider'];
	$clean['provider'] = array_key_exists( $provider, webmz_ai_chat_providers() ) ? $provider : $defaults['provider'];

	$new_key = isset( $raw['api_key'] ) ? trim( sanitize_text_field( $raw['api_key'] ) ) : '';
	if ( ! empty( $raw['api_key_clear'] ) ) {
		$clean['api_key'] = '';
	} else {
		$clean['api_key'] = '' !== $new_key ? $new_key : (string) $current['api_key'];
	}

	$clean['base_url'] = isset( $raw['base_url'] ) ? untrailingslashit( esc_url_raw( trim( (string) $raw['base_url'] ) ) ) : '';
	$clean['model']    = isset( $raw['model'] ) ? preg_replace( '/[^A-Za-z0-9._:\/\-]/', '', (string) $raw['model'] ) : $defaults['model'];
	$clean['model']    = '' !== $clean['model'] ? $clean['model'] : $defaults['model'];

	foreach ( array( 'bot_name', 'placeholder', 'fallback_text' ) as $key ) {
		$clean[ $key ] = isset( $raw[ $key ] ) ? sanitize_text_field( $raw[ $key ] ) : $defaults[ $key ];
	}

	foreach ( array( 'welcome', 'system_prompt', 'knowledge', 'suggestions' ) as $key ) {
		$clean[ $key ] = isset( $raw[ $key ] ) ? sanitize_textarea_field( $raw[ $key ] ) : $defaults[ $key ];
	}

	// Keep the knowledge text within a sane prompt budget.
	$clean['knowledge'] = function_exists( 'mb_substr' ) ? mb_substr( $clean['knowledge'], 0, 20000 ) : substr( $clean['knowledge'], 0, 40000 );

	$clean['fallback_url'] = isset( $raw['fallback_url'] ) ? esc_url_raw( $raw['fallback_url'] ) : '';
	$clean['visibility']   = isset( $raw['visibility'] ) && 'logged_in' === $raw['visibility'] ? 'logged_in' : 'all';
	$clean['position']     = isset( $raw['position'] ) && 'left' === $raw['position'] ? 'left' : 'right';

	$color          = isset( $raw['color'] ) ? sanitize_hex_color( $raw['color'] ) : '';
	$clean['color'] = $color ? $color : $defaults['color'];

	$clean['offset_x']            = isset( $raw['offset_x'] ) ? min( 200, absint( $raw['offset_x'] ) ) : $defaults['offset_x'];
	$clean['offset_bottom']       = isset( $raw['offset_bottom'] ) ? min( 300, absint( $raw['offset_bottom'] ) ) : $defaults['offset_bottom'];
	$clean['temperature']         = isset( $raw['temperature'] ) ? max( 0, min( 1.5, round( (float) $raw['temperature'], 2 ) ) ) : $defaults['temperature'];
	$clean['max_tokens']          = isset( $raw['max_tokens'] ) ? max( 100, min( 4000, absint( $raw['max_tokens'] ) ) ) : $defaults['max_tokens'];
	$clean['history_limit']       = isset( $raw['history_limit'] ) ? max( 2, min( 30, absint( $raw['history_limit'] ) ) ) : $defaults['history_limit'];
	$clean['max_input_chars']     = isset( $raw['max_input_chars'] ) ? max( 100, min( 4000, absint( $raw['max_input_chars'] ) ) ) : $defaults['max_input_chars'];
	$clean['rate_limit_per_hour'] = isset( $raw['rate_limit_per_hour'] ) ? max( 1, min( 500, absint( $raw['rate_limit_per_hour'] ) ) ) : $defaults['rate_limit_per_hour'];

	return $clean;
}

/**
 * API base URL for the configured provider.
 *
 * @param array<string,mixed> $settings Settings.
 * @return string
 */
function webmz_ai_chat_base_url( $settings ) {
	if ( ! empty( $settings['base_url'] ) ) {
		return untrailingslashit( (string) $settings['base_url'] );
	}

	$providers = webmz_ai_chat_providers();

	return isset( $providers[ $settings['provider'] ] ) ? untrailingslashit( $providers[ $settings['provider'] ]['base_url'] ) : '';
}

/**
 * Whether the chat is configured and should appear for the current visitor.
 *
 * @return bool
 */
function webmz_ai_chat_is_active() {
	$settings = webmz_ai_chat_get_settings();

	if ( 'yes' !== $settings['enabled'] || '' === $settings['api_key'] || '' === webmz_ai_chat_base_url( $settings ) ) {
		return false;
	}

	if ( 'logged_in' === $settings['visibility'] && ! is_user_logged_in() ) {
		return false;
	}

	return (bool) apply_filters( 'webmz_ai_chat_is_active', true, $settings );
}

/**
 * Short product catalogue added to the system prompt so the bot can answer about courses.
 *
 * @return string
 */
function webmz_ai_chat_products_context() {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return '';
	}

	$cached = get_transient( 'webmz_ai_chat_products_ctx' );
	if ( false !== $cached ) {
		return (string) $cached;
	}

	$products = wc_get_products(
		array(
			'status'  => 'publish',
			'limit'   => 40,
			'orderby' => 'popularity',
			'order'   => 'DESC',
		)
	);
	$lines    = array();

	foreach ( $products as $product ) {
		if ( ! $product->is_visible() ) {
			continue;
		}

		$price = wp_strip_all_tags( html_entity_decode( (string) $product->get_price_html(), ENT_QUOTES, 'UTF-8' ) );
		$short = wp_trim_words( wp_strip_all_tags( (string) $product->get_short_description() ), 25, '…' );
		$lines[] = sprintf( '- %s | %s | %s%s', $product->get_name(), '' !== trim( $price ) ? $price : __( 'قیمت نامشخص', 'tadris' ), $product->get_permalink(), '' !== $short ? ' | ' . $short : '' );
	}

	$context = implode( "\n", $lines );
	set_transient( 'webmz_ai_chat_products_ctx', $context, HOUR_IN_SECONDS );

	return $context;
}

/**
 * Drop cached product context when products change.
 *
 * @return void
 */
function webmz_ai_chat_flush_products_context() {
	delete_transient( 'webmz_ai_chat_products_ctx' );
}
add_action( 'woocommerce_update_product', 'webmz_ai_chat_flush_products_context' );
add_action( 'woocommerce_new_product', 'webmz_ai_chat_flush_products_context' );

/**
 * Build the system prompt from settings and site context.
 *
 * @param array<string,mixed> $settings Settings.
 * @return string
 */
function webmz_ai_chat_system_prompt( $settings ) {
	$parts   = array( trim( (string) $settings['system_prompt'] ) );
	$parts[] = sprintf(
		/* translators: 1: site name, 2: site tagline, 3: site URL */
		__( "اطلاعات سایت:\nنام سایت: %1\$s\nشعار: %2\$s\nآدرس: %3\$s", 'tadris' ),
		get_bloginfo( 'name' ),
		get_bloginfo( 'description' ),
		home_url( '/' )
	);

	if ( '' !== trim( (string) $settings['knowledge'] ) ) {
		$parts[] = __( "اطلاعات و پاسخ‌های پشتیبانی:\n", 'tadris' ) . trim( (string) $settings['knowledge'] );
	}

	if ( 'yes' === $settings['include_products'] ) {
		$products = webmz_ai_chat_products_context();
		if ( '' !== $products ) {
			$parts[] = __( "محصولات و دوره‌های سایت (نام | قیمت | لینک | توضیح):\n", 'tadris' ) . $products;
		}
	}

	if ( '' !== (string) $settings['fallback_url'] ) {
		$parts[] = sprintf(
			/* translators: %s: support URL */
			__( 'اگر نیاز به پشتیبانی انسانی بود، این لینک را معرفی کن: %s', 'tadris' ),
			$settings['fallback_url']
		);
	}

	return (string) apply_filters( 'webmz_ai_chat_system_prompt', implode( "\n\n", array_filter( $parts ) ), $settings );
}

/**
 * Call the chat completions API.
 *
 * @param array<string,mixed>            $settings Settings.
 * @param array<int,array<string,string>> $messages Conversation (user/assistant roles only).
 * @return string|WP_Error Assistant reply.
 */
function webmz_ai_chat_request( $settings, $messages ) {
	$base_url = webmz_ai_chat_base_url( $settings );

	if ( '' === $base_url || '' === (string) $settings['api_key'] ) {
		return new WP_Error( 'webmz_ai_chat_not_configured', __( 'دستیار هوشمند هنوز تنظیم نشده است.', 'tadris' ) );
	}

	$payload = array(
		'model'       => (string) $settings['model'],
		'messages'    => array_merge(
			array(
				array(
					'role'    => 'system',
					'content' => webmz_ai_chat_system_prompt( $settings ),
				),
			),
			$messages
		),
		'temperature' => (float) $settings['temperature'],
		'max_tokens'  => (int) $settings['max_tokens'],
	);

	$response = wp_remote_post(
		$base_url . '/chat/completions',
		array(
			'timeout' => 45,
			'headers' => array(
				'Authorization' => 'Bearer ' . $settings['api_key'],
				'Content-Type'  => 'application/json',
			),
			'body'    => wp_json_encode( apply_filters( 'webmz_ai_chat_request_payload', $payload, $settings ) ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'webmz_ai_chat_http', $response->get_error_message() );
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );

	if ( $code < 200 || $code >= 300 ) {
		$message = is_array( $body ) && ! empty( $body['error']['message'] ) ? (string) $body['error']['message'] : 'HTTP ' . $code;
		return new WP_Error( 'webmz_ai_chat_api', $message, array( 'status' => $code ) );
	}

	$reply = is_array( $body ) && isset( $body['choices'][0]['message']['content'] ) ? trim( (string) $body['choices'][0]['message']['content'] ) : '';

	if ( '' === $reply ) {
		return new WP_Error( 'webmz_ai_chat_empty', __( 'پاسخی از سرویس هوش مصنوعی دریافت نشد.', 'tadris' ) );
	}

	return $reply;
}

/**
 * Per-visitor rate limit key (user ID, else a hash of the IP).
 *
 * @return string
 */
function webmz_ai_chat_rate_key() {
	$user_id = get_current_user_id();

	if ( $user_id ) {
		return 'webmz_ai_rl_u' . $user_id;
	}

	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

	return 'webmz_ai_rl_' . md5( $ip . wp_salt( 'nonce' ) );
}

/**
 * Count one message against the hourly limit.
 *
 * @param int $limit Messages per hour.
 * @return bool False when the limit is reached.
 */
function webmz_ai_chat_consume_rate_limit( $limit ) {
	$key   = webmz_ai_chat_rate_key();
	$state = get_transient( $key );
	$now   = time();

	if ( ! is_array( $state ) || empty( $state['start'] ) || $now - (int) $state['start'] >= HOUR_IN_SECONDS ) {
		$state = array(
			'start' => $now,
			'count' => 0,
		);
	}

	if ( (int) $state['count'] >= $limit ) {
		return false;
	}

	++$state['count'];
	set_transient( $key, $state, HOUR_IN_SECONDS );

	return true;
}

/**
 * Normalize the conversation sent by the browser.
 *
 * @param mixed               $raw      Raw messages.
 * @param array<string,mixed> $settings Settings.
 * @return array<int,array<string,string>>
 */
function webmz_ai_chat_clean_messages( $raw, $settings ) {
	$raw      = is_array( $raw ) ? $raw : array();
	$messages = array();
	$max      = (int) $settings['max_input_chars'];

	foreach ( $raw as $item ) {
		if ( ! is_array( $item ) || ! isset( $item['role'], $item['content'] ) ) {
			continue;
		}

		// Only user/assistant turns are accepted; the system prompt is always built on the server.
		$role = 'assistant' === $item['role'] ? 'assistant' : ( 'user' === $item['role'] ? 'user' : '' );
		if ( '' === $role ) {
			continue;
		}

		// Sent only to the AI API as JSON and escaped by the chat script before display, so tags are kept
		// (visitors may ask about HTML/code); just drop invalid UTF-8 and control characters.
		$content = wp_check_invalid_utf8( (string) $item['content'], true );
		$content = trim( (string) preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $content ) );
		if ( '' === $content ) {
			continue;
		}

		$limit      = 'user' === $role ? $max : 4000;
		$messages[] = array(
			'role'    => $role,
			'content' => function_exists( 'mb_substr' ) ? mb_substr( $content, 0, $limit ) : substr( $content, 0, $limit ),
		);
	}

	$messages = array_slice( $messages, -1 * (int) $settings['history_limit'] );

	// The conversation must end with the visitor's new message.
	if ( empty( $messages ) || 'user' !== $messages[ count( $messages ) - 1 ]['role'] ) {
		return array();
	}

	return array_values( $messages );
}

/**
 * AJAX: answer a visitor message.
 *
 * @return void
 */
function webmz_ai_chat_ajax_send() {
	if ( ! check_ajax_referer( 'webmz_ai_chat', 'nonce', false ) ) {
		wp_send_json_error(
			array(
				'code'    => 'bad_nonce',
				'message' => __( 'نشست شما منقضی شده است. دوباره تلاش کنید.', 'tadris' ),
			),
			403
		);
	}

	if ( ! webmz_ai_chat_is_active() ) {
		wp_send_json_error( array( 'message' => __( 'دستیار هوشمند در حال حاضر در دسترس نیست.', 'tadris' ) ), 503 );
	}

	$settings = webmz_ai_chat_get_settings();
	$raw      = isset( $_POST['messages'] ) ? json_decode( wp_unslash( (string) $_POST['messages'] ), true ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- decoded and sanitized per message.
	$messages = webmz_ai_chat_clean_messages( $raw, $settings );

	if ( empty( $messages ) ) {
		wp_send_json_error( array( 'message' => __( 'پیام خالی است.', 'tadris' ) ), 400 );
	}

	if ( ! webmz_ai_chat_consume_rate_limit( (int) $settings['rate_limit_per_hour'] ) ) {
		wp_send_json_error( array( 'message' => __( 'تعداد پیام‌های شما در این ساعت به حد مجاز رسیده است. کمی بعد دوباره تلاش کنید.', 'tadris' ) ), 429 );
	}

	$reply = webmz_ai_chat_request( $settings, $messages );

	if ( is_wp_error( $reply ) ) {
		// Details go to the log for the site owner; visitors get a generic message.
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( 'Akam AI chat: ' . $reply->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}

		wp_send_json_error( array( 'message' => __( 'در حال حاضر امکان پاسخ‌گویی وجود ندارد. لطفاً کمی بعد دوباره تلاش کنید.', 'tadris' ) ), 502 );
	}

	wp_send_json_success( array( 'reply' => $reply ) );
}
add_action( 'wp_ajax_webmz_ai_chat_send', 'webmz_ai_chat_ajax_send' );
add_action( 'wp_ajax_nopriv_webmz_ai_chat_send', 'webmz_ai_chat_ajax_send' );

/**
 * AJAX: fresh nonce for pages served from a full-page cache.
 *
 * @return void
 */
function webmz_ai_chat_ajax_nonce() {
	wp_send_json_success( array( 'nonce' => wp_create_nonce( 'webmz_ai_chat' ) ) );
}
add_action( 'wp_ajax_webmz_ai_chat_nonce', 'webmz_ai_chat_ajax_nonce' );
add_action( 'wp_ajax_nopriv_webmz_ai_chat_nonce', 'webmz_ai_chat_ajax_nonce' );

/**
 * AJAX (admin): test the connection with the settings currently in the form.
 *
 * @return void
 */
function webmz_ai_chat_ajax_test() {
	check_ajax_referer( 'webmz_save_options', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'شما اجازه انجام این عملیات را ندارید.', 'tadris' ) ), 403 );
	}

	$input    = isset( $_POST['ai_chat'] ) ? wp_unslash( $_POST['ai_chat'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below.
	$settings = webmz_ai_chat_sanitize_settings( $input );
	$reply    = webmz_ai_chat_request(
		$settings,
		array(
			array(
				'role'    => 'user',
				'content' => __( 'سلام، در یک جمله خودت را معرفی کن.', 'tadris' ),
			),
		)
	);

	if ( is_wp_error( $reply ) ) {
		wp_send_json_error( array( 'message' => sprintf( /* translators: %s: error */ __( 'اتصال ناموفق بود: %s', 'tadris' ), $reply->get_error_message() ) ) );
	}

	wp_send_json_success( array( 'message' => sprintf( /* translators: %s: reply */ __( 'اتصال موفق بود. پاسخ نمونه: %s', 'tadris' ), $reply ) ) );
}
add_action( 'wp_ajax_webmz_ai_chat_test', 'webmz_ai_chat_ajax_test' );

/**
 * Enqueue chat assets when active.
 *
 * @return void
 */
function webmz_ai_chat_enqueue_assets() {
	if ( is_admin() || ! webmz_ai_chat_is_active() || ( function_exists( 'webmz_is_layout_editing_context' ) && webmz_is_layout_editing_context() ) ) {
		return;
	}

	$settings    = webmz_ai_chat_get_settings();
	$suggestions = array_values( array_filter( array_map( 'trim', explode( "\n", (string) $settings['suggestions'] ) ) ) );

	wp_enqueue_style( 'webmz-ai-chat', WEBMZ_URI . 'assets/css/ai-chat.css', array(), WEBMZ_VERSION );
	wp_add_inline_style(
		'webmz-ai-chat',
		sprintf(
			'.webmz-ai-chat{--webmz-ai-color:%1$s;--webmz-ai-x:%2$dpx;--webmz-ai-bottom:%3$dpx;}',
			esc_attr( $settings['color'] ),
			absint( $settings['offset_x'] ),
			absint( $settings['offset_bottom'] )
		)
	);
	wp_enqueue_script( 'webmz-ai-chat', WEBMZ_URI . 'assets/js/ai-chat.js', array(), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-ai-chat',
		'webmzAiChat',
		array(
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'nonce'        => wp_create_nonce( 'webmz_ai_chat' ),
			'botName'      => $settings['bot_name'],
			'welcome'      => $settings['welcome'],
			'placeholder'  => $settings['placeholder'],
			'suggestions'  => array_slice( $suggestions, 0, 6 ),
			'fallbackText' => $settings['fallback_text'],
			'fallbackUrl'  => $settings['fallback_url'],
			'maxChars'     => (int) $settings['max_input_chars'],
			'historyLimit' => (int) $settings['history_limit'],
			'position'     => $settings['position'],
			'i18n'         => array(
				'open'    => __( 'گفتگو با دستیار هوشمند', 'tadris' ),
				'close'   => __( 'بستن گفتگو', 'tadris' ),
				'send'    => __( 'ارسال', 'tadris' ),
				'reset'   => __( 'شروع گفتگوی جدید', 'tadris' ),
				'typing'  => __( 'در حال نوشتن...', 'tadris' ),
				'error'   => __( 'خطا در ارتباط. دوباره تلاش کنید.', 'tadris' ),
				'tooLong' => __( 'پیام طولانی‌تر از حد مجاز است.', 'tadris' ),
				'aiNote'  => __( 'پاسخ‌ها توسط هوش مصنوعی تولید می‌شوند و ممکن است خطا داشته باشند.', 'tadris' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'webmz_ai_chat_enqueue_assets', 30 );
