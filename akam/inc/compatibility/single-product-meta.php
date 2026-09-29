<?php
/**
 * Product meta fields for Webmasters single product widgets.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register single-product metabox on WooCommerce products.
 *
 * @return void
 */
function webmz_spw_register_meta_boxes() {
	if ( ! post_type_exists( 'product' ) ) {
		return;
	}

	add_meta_box(
		'webmz_spw_product_meta',
		esc_html__( 'صفحه سینگل محصول - آکام', 'tadris' ),
		'webmz_spw_render_product_meta_box',
		'product',
		'normal',
		'high'
	);

	add_meta_box(
		'webmz_spw_product_features_meta',
		esc_html__( 'ویژگی‌های محصول (اسلایدر پیشنهاد شگفت‌انگیز)', 'tadris' ),
		'webmz_spw_render_product_features_meta_box',
		'product',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'webmz_spw_register_meta_boxes' );

/**
 * Render single product metabox fields.
 *
 * @param WP_Post $post Product post.
 * @return void
 */
function webmz_spw_render_product_meta_box( $post ) {
	wp_nonce_field( 'webmz_spw_save_meta', 'webmz_spw_meta_nonce' );

	$intro_video     = get_post_meta( $post->ID, '_webmz_product_intro_video', true );
	$single_title    = get_post_meta( $post->ID, '_webmz_single_product_title', true );
	$short_desc      = get_post_meta( $post->ID, '_webmz_single_short_description', true );
	$faq_items       = webmz_spw_get_product_faq_items( $post->ID );
	$highlight_color = get_post_meta( $post->ID, '_webmz_single_title_highlight_color', true );
	$highlight_color = $highlight_color ? sanitize_hex_color( $highlight_color ) : '';
	?>
	<p>
		<label for="webmz_product_intro_video"><strong><?php esc_html_e( 'ویدیو معرفی محصول (MP4، آپارات یا یوتیوب)', 'tadris' ); ?></strong></label><br>
		<input class="widefat" type="url" dir="ltr" id="webmz_product_intro_video" name="webmz_product_intro_video" value="<?php echo esc_attr( $intro_video ); ?>" placeholder="<?php echo esc_attr( webmz_video_field_placeholder() ); ?>">
	</p>
	<p class="description"><?php esc_html_e( 'در صورت پر شدن، در ویجت تصویر محصول به‌جای تصویر شاخص، ویدیو نمایش داده می‌شود. لینک مستقیم MP4 با پلیر plyr و پوستر تصویر شاخص پخش می‌شود؛ لینک آپارات یا یوتیوب به‌صورت جاسازی‌شده نمایش داده می‌شود.', 'tadris' ); ?></p>

	<p>
		<label for="webmz_single_product_title"><strong><?php esc_html_e( 'عنوان سفارشی صفحه سینگل', 'tadris' ); ?></strong></label><br>
		<input class="widefat" type="text" id="webmz_single_product_title" name="webmz_single_product_title" value="<?php echo esc_attr( $single_title ); ?>">
	</p>
	<p class="description">
		<?php esc_html_e( 'برای رنگی کردن بخشی از عنوان از تگ [hl]متن[/hl] استفاده کنید. مثال: آموزش پروژه محور [hl]NestJS[/hl] از صفر!', 'tadris' ); ?>
	</p>

	<p>
		<label for="webmz_single_title_highlight_color"><strong><?php esc_html_e( 'رنگ بخش برجسته عنوان (اختیاری)', 'tadris' ); ?></strong></label><br>
		<input type="text" class="webmz-color-picker" id="webmz_single_title_highlight_color" name="webmz_single_title_highlight_color" value="<?php echo esc_attr( $highlight_color ); ?>" data-default-color="">
	</p>

	<p>
		<label for="webmz_single_short_description"><strong><?php esc_html_e( 'توضیح کوتاه صفحه سینگل', 'tadris' ); ?></strong></label><br>
		<textarea class="widefat" rows="4" id="webmz_single_short_description" name="webmz_single_short_description"><?php echo esc_textarea( $short_desc ); ?></textarea>
	</p>

	<hr>

	<p><strong><?php esc_html_e( 'سوالات متداول', 'tadris' ); ?></strong></p>
	<p class="description"><?php esc_html_e( 'سوالات در ویجت FAQ صفحه سینگل نمایش داده می‌شوند.', 'tadris' ); ?></p>

	<div id="webmz-spw-faq-repeater">
		<?php
		if ( empty( $faq_items ) ) {
			$faq_items = array(
				array(
					'question' => '',
					'answer'   => '',
				),
			);
		}

		foreach ( $faq_items as $index => $item ) :
			?>
			<div class="webmz-spw-faq-row" style="border:1px solid #ddd;padding:12px;margin-bottom:10px;background:#fff;">
				<p>
					<label><strong><?php esc_html_e( 'سؤال', 'tadris' ); ?></strong></label><br>
					<input class="widefat" type="text" name="webmz_product_faq[<?php echo esc_attr( $index ); ?>][question]" value="<?php echo esc_attr( $item['question'] ?? '' ); ?>">
				</p>
				<p>
					<label><strong><?php esc_html_e( 'پاسخ', 'tadris' ); ?></strong></label><br>
					<textarea class="widefat" rows="3" name="webmz_product_faq[<?php echo esc_attr( $index ); ?>][answer]"><?php echo esc_textarea( $item['answer'] ?? '' ); ?></textarea>
				</p>
				<p><button type="button" class="button webmz-spw-faq-remove"><?php esc_html_e( 'حذف', 'tadris' ); ?></button></p>
			</div>
			<?php
		endforeach;
		?>
	</div>
	<p><button type="button" class="button button-secondary" id="webmz-spw-faq-add"><?php esc_html_e( 'افزودن سؤال', 'tadris' ); ?></button></p>

	<script>
	(function () {
		var wrap = document.getElementById('webmz-spw-faq-repeater');
		var addBtn = document.getElementById('webmz-spw-faq-add');

		if (!wrap || !addBtn) {
			return;
		}

		function nextIndex() {
			return wrap.querySelectorAll('.webmz-spw-faq-row').length;
		}

		addBtn.addEventListener('click', function () {
			var index = nextIndex();
			var row = document.createElement('div');
			row.className = 'webmz-spw-faq-row';
			row.style.cssText = 'border:1px solid #ddd;padding:12px;margin-bottom:10px;background:#fff;';
			row.innerHTML =
				'<p><label><strong><?php echo esc_js( __( 'سؤال', 'tadris' ) ); ?></strong></label><br>' +
				'<input class="widefat" type="text" name="webmz_product_faq[' + index + '][question]" value=""></p>' +
				'<p><label><strong><?php echo esc_js( __( 'پاسخ', 'tadris' ) ); ?></strong></label><br>' +
				'<textarea class="widefat" rows="3" name="webmz_product_faq[' + index + '][answer]"></textarea></p>' +
				'<p><button type="button" class="button webmz-spw-faq-remove"><?php echo esc_js( __( 'حذف', 'tadris' ) ); ?></button></p>';
			wrap.appendChild(row);
		});

		wrap.addEventListener('click', function (event) {
			if (event.target.classList.contains('webmz-spw-faq-remove')) {
				event.preventDefault();
				var rows = wrap.querySelectorAll('.webmz-spw-faq-row');
				if (rows.length <= 1) {
					rows[0].querySelectorAll('input, textarea').forEach(function (field) {
						field.value = '';
					});
					return;
				}
				event.target.closest('.webmz-spw-faq-row').remove();
			}
		});
	}());
	</script>
	<?php
}

/**
 * Render product features repeater metabox.
 *
 * @param WP_Post $post Product post.
 * @return void
 */
function webmz_spw_render_product_features_meta_box( $post ) {
	wp_nonce_field( 'webmz_spw_save_product_features', 'webmz_spw_product_features_nonce' );

	$features = webmz_spw_get_product_feature_items( $post->ID );

	if ( empty( $features ) ) {
		$features = array(
			array(
				'text' => '',
			),
		);
	}
	?>
	<p class="description">
		<?php esc_html_e( 'ویژگی‌های کلیدی محصول را اضافه کنید. در ویجت «اسلایدر پیشنهاد شگفت‌انگیز» و سایر بخش‌های مرتبط نمایش داده می‌شوند.', 'tadris' ); ?>
	</p>

	<div id="webmz-spw-features-repeater">
		<?php foreach ( $features as $index => $item ) : ?>
			<div class="webmz-spw-feature-row" style="border:1px solid #ddd;padding:12px;margin-bottom:10px;background:#fff;">
				<p>
					<label><strong><?php esc_html_e( 'متن ویژگی', 'tadris' ); ?></strong></label><br>
					<input
						class="widefat"
						type="text"
						name="webmz_product_features[<?php echo esc_attr( $index ); ?>][text]"
						value="<?php echo esc_attr( $item['text'] ?? '' ); ?>"
						placeholder="<?php esc_attr_e( 'مثال: پنل اختصاصی دانشجویان', 'tadris' ); ?>"
					>
				</p>
				<p><button type="button" class="button webmz-spw-feature-remove"><?php esc_html_e( 'حذف', 'tadris' ); ?></button></p>
			</div>
		<?php endforeach; ?>
	</div>

	<p><button type="button" class="button button-secondary" id="webmz-spw-features-add"><?php esc_html_e( 'افزودن ویژگی', 'tadris' ); ?></button></p>

	<script>
	(function () {
		var wrap = document.getElementById('webmz-spw-features-repeater');
		var addBtn = document.getElementById('webmz-spw-features-add');

		if (!wrap || !addBtn) {
			return;
		}

		function nextIndex() {
			return wrap.querySelectorAll('.webmz-spw-feature-row').length;
		}

		addBtn.addEventListener('click', function () {
			var index = nextIndex();
			var row = document.createElement('div');
			row.className = 'webmz-spw-feature-row';
			row.style.cssText = 'border:1px solid #ddd;padding:12px;margin-bottom:10px;background:#fff;';
			row.innerHTML =
				'<p><label><strong><?php echo esc_js( __( 'متن ویژگی', 'tadris' ) ); ?></strong></label><br>' +
				'<input class="widefat" type="text" name="webmz_product_features[' + index + '][text]" value="" placeholder="<?php echo esc_js( __( 'مثال: پنل اختصاصی دانشجویان', 'tadris' ) ); ?>"></p>' +
				'<p><button type="button" class="button webmz-spw-feature-remove"><?php echo esc_js( __( 'حذف', 'tadris' ) ); ?></button></p>';
			wrap.appendChild(row);
		});

		wrap.addEventListener('click', function (event) {
			if (event.target.classList.contains('webmz-spw-feature-remove')) {
				event.preventDefault();
				var rows = wrap.querySelectorAll('.webmz-spw-feature-row');

				if (rows.length <= 1) {
					rows[0].querySelectorAll('input').forEach(function (field) {
						field.value = '';
					});
					return;
				}

				event.target.closest('.webmz-spw-feature-row').remove();
			}
		});
	}());
	</script>
	<?php
}

/**
 * Enqueue color picker on product edit screen.
 *
 * @param string $hook Current admin page hook.
 * @return void
 */
function webmz_spw_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || 'product' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	wp_add_inline_script(
		'wp-color-picker',
		"jQuery(function($){ $('.webmz-color-picker').wpColorPicker(); });"
	);
}
add_action( 'admin_enqueue_scripts', 'webmz_spw_admin_assets' );

/**
 * Save single product metabox fields.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return void
 */
function webmz_spw_save_meta_fields( $post_id, $post ) {
	if ( ! isset( $_POST['webmz_spw_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_spw_meta_nonce'] ) ), 'webmz_spw_save_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) || 'product' !== $post->post_type ) {
		return;
	}

	$intro_video = isset( $_POST['webmz_product_intro_video'] ) ? esc_url_raw( wp_unslash( $_POST['webmz_product_intro_video'] ) ) : '';
	$single_title = isset( $_POST['webmz_single_product_title'] ) ? sanitize_text_field( wp_unslash( $_POST['webmz_single_product_title'] ) ) : '';
	$short_desc   = isset( $_POST['webmz_single_short_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['webmz_single_short_description'] ) ) : '';
	$highlight    = isset( $_POST['webmz_single_title_highlight_color'] ) ? sanitize_hex_color( wp_unslash( $_POST['webmz_single_title_highlight_color'] ) ) : '';

	update_post_meta( $post_id, '_webmz_product_intro_video', $intro_video );
	update_post_meta( $post_id, '_webmz_single_product_title', $single_title );
	update_post_meta( $post_id, '_webmz_single_short_description', $short_desc );
	update_post_meta( $post_id, '_webmz_single_title_highlight_color', $highlight ? $highlight : '' );

	$faq_items = array();

	if ( isset( $_POST['webmz_product_faq'] ) && is_array( $_POST['webmz_product_faq'] ) ) {
		foreach ( wp_unslash( $_POST['webmz_product_faq'] ) as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$question = isset( $item['question'] ) ? sanitize_text_field( $item['question'] ) : '';
			$answer   = isset( $item['answer'] ) ? sanitize_textarea_field( $item['answer'] ) : '';

			if ( '' === trim( $question ) && '' === trim( $answer ) ) {
				continue;
			}

			$faq_items[] = array(
				'question' => $question,
				'answer'   => $answer,
			);
		}
	}

	update_post_meta( $post_id, '_webmz_product_faq', $faq_items );
}
add_action( 'save_post', 'webmz_spw_save_meta_fields', 10, 2 );

/**
 * Save product features metabox fields.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return void
 */
function webmz_spw_save_product_features_meta( $post_id, $post ) {
	if ( ! isset( $_POST['webmz_spw_product_features_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_spw_product_features_nonce'] ) ), 'webmz_spw_save_product_features' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) || 'product' !== $post->post_type ) {
		return;
	}

	$features = array();

	if ( isset( $_POST['webmz_product_features'] ) && is_array( $_POST['webmz_product_features'] ) ) {
		foreach ( wp_unslash( $_POST['webmz_product_features'] ) as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$text = isset( $item['text'] ) ? sanitize_text_field( $item['text'] ) : '';

			if ( '' === trim( $text ) ) {
				continue;
			}

			$features[] = array(
				'text' => $text,
			);
		}
	}

	update_post_meta( $post_id, '_webmz_product_features', $features );
}
add_action( 'save_post', 'webmz_spw_save_product_features_meta', 10, 2 );

/**
 * Get FAQ items for a product.
 *
 * @param int $product_id Product ID.
 * @return array<int,array{question:string,answer:string}>
 */
function webmz_spw_get_product_faq_items( $product_id ) {
	$product_id = absint( $product_id );
	$items      = get_post_meta( $product_id, '_webmz_product_faq', true );

	if ( ! is_array( $items ) ) {
		return array();
	}

	$normalized = array();

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$question = isset( $item['question'] ) ? (string) $item['question'] : '';
		$answer   = isset( $item['answer'] ) ? (string) $item['answer'] : '';

		if ( '' === trim( $question ) && '' === trim( $answer ) ) {
			continue;
		}

		$normalized[] = array(
			'question' => $question,
			'answer'   => $answer,
		);
	}

	return $normalized;
}

/**
 * Get raw product feature repeater items.
 *
 * @param int $product_id Product ID.
 * @return array<int,array{text:string}>
 */
function webmz_spw_get_product_feature_items( $product_id ) {
	$product_id = absint( $product_id );
	$items      = get_post_meta( $product_id, '_webmz_product_features', true );

	if ( ! is_array( $items ) ) {
		return array();
	}

	$normalized = array();

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$text = isset( $item['text'] ) ? sanitize_text_field( (string) $item['text'] ) : '';

		if ( '' === trim( $text ) ) {
			continue;
		}

		$normalized[] = array(
			'text' => $text,
		);
	}

	return $normalized;
}

/**
 * Get product feature labels for front-end widgets.
 *
 * @param int $product_id Product ID.
 * @return string[]
 */
function webmz_spw_get_product_features( $product_id ) {
	$items = webmz_spw_get_product_feature_items( $product_id );

	if ( empty( $items ) ) {
		return array();
	}

	return array_values(
		array_map(
			static function ( $item ) {
				return (string) ( $item['text'] ?? '' );
			},
			$items
		)
	);
}

/**
 * Get students/participants count for a product.
 *
 * @param int                $product_id Product ID.
 * @param \WC_Product|null   $product    Optional product object.
 * @return int
 */
function webmz_spw_get_product_students_count( $product_id, $product = null ) {
	$product_id = absint( $product_id );

	if ( ! $product_id ) {
		return 0;
	}

	$source = get_post_meta( $product_id, '_webmz_students_source', true );
	$source = in_array( $source, array( 'sales', 'manual' ), true ) ? $source : 'sales';

	if ( 'manual' === $source ) {
		return absint( get_post_meta( $product_id, '_webmz_students_manual', true ) );
	}

	if ( ! $product && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( $product_id );
	}

	return $product ? absint( $product->get_total_sales() ) : 0;
}

/**
 * Parse highlighted title markup.
 *
 * @param string $text           Title text with [hl] tags.
 * @param string $highlight_class CSS class for highlighted spans.
 * @return string
 */
function webmz_spw_parse_highlighted_title( $text, $highlight_class = 'webmz-spw-title-highlight' ) {
	$text = (string) $text;

	if ( '' === trim( $text ) ) {
		return '';
	}

	$parts  = preg_split( '/(\[hl\].*?\[\/hl\])/s', $text, -1, PREG_SPLIT_DELIM_CAPTURE );
	$output = '';

	if ( ! is_array( $parts ) ) {
		return esc_html( $text );
	}

	foreach ( $parts as $part ) {
		if ( preg_match( '/^\[hl\](.*?)\[\/hl\]$/s', $part, $matches ) ) {
			$output .= '<span class="' . esc_attr( $highlight_class ) . '">' . esc_html( $matches[1] ) . '</span>';
		} else {
			$output .= esc_html( $part );
		}
	}

	return $output;
}

/**
 * Get rendered single product title HTML.
 *
 * @param int $product_id Product ID.
 * @return string
 */
function webmz_spw_get_product_title_html( $product_id ) {
	$product_id = absint( $product_id );

	if ( ! $product_id ) {
		return '';
	}

	$custom = get_post_meta( $product_id, '_webmz_single_product_title', true );

	if ( '' !== trim( (string) $custom ) ) {
		return webmz_spw_parse_highlighted_title( $custom );
	}

	return esc_html( get_the_title( $product_id ) );
}

/**
 * Get single page short description.
 *
 * @param int $product_id Product ID.
 * @return string
 */
function webmz_spw_get_product_short_description( $product_id ) {
	$product_id = absint( $product_id );

	if ( ! $product_id ) {
		return '';
	}

	$custom = get_post_meta( $product_id, '_webmz_single_short_description', true );

	if ( '' !== trim( (string) $custom ) ) {
		return (string) $custom;
	}

	if ( function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( $product_id );

		if ( $product ) {
			return (string) $product->get_short_description();
		}
	}

	return '';
}

/**
 * Get intro video URL for product.
 *
 * @param int $product_id Product ID.
 * @return string
 */
function webmz_spw_get_product_intro_video_url( $product_id ) {
	return esc_url_raw( (string) get_post_meta( absint( $product_id ), '_webmz_product_intro_video', true ) );
}

/**
 * Get optional per-product highlight color.
 *
 * @param int $product_id Product ID.
 * @return string
 */
function webmz_spw_get_product_title_highlight_color( $product_id ) {
	$color = get_post_meta( absint( $product_id ), '_webmz_single_title_highlight_color', true );

	return $color ? sanitize_hex_color( $color ) : '';
}

/**
 * Check whether sticky add-to-cart bar is enabled in theme settings.
 *
 * @return bool
 */
function webmz_spw_sticky_cart_is_enabled() {
	$options = webmz_get_options();

	return ! isset( $options['sticky_add_to_cart_enabled'] ) || 'yes' === $options['sticky_add_to_cart_enabled'];
}

/**
 * Render sticky add-to-cart bar on single product pages.
 *
 * @return void
 */
function webmz_spw_render_sticky_add_to_cart_bar() {
	if ( ! webmz_spw_sticky_cart_is_enabled() ) {
		return;
	}

	if ( ! function_exists( 'is_product' ) || ! is_product() || ! function_exists( 'wc_get_product' ) ) {
		return;
	}

	$product = wc_get_product( get_queried_object_id() );

	if ( ! $product || ! $product->is_purchasable() ) {
		return;
	}

	$product_id     = $product->get_id();
	$is_variable    = $product->is_type( 'variable' );
	$is_simple_ajax = $product->is_type( 'simple' ) && $product->is_in_stock();
	$unavailable    = ! $product->is_in_stock() || ! $product->is_purchasable();

	if ( $unavailable ) {
		$button_text = esc_html__( 'ناموجود', 'tadris' );
	} elseif ( $is_variable ) {
		$button_text = esc_html__( 'انتخاب و خرید', 'tadris' );
	} else {
		$button_text = webmz_spw_get_add_to_cart_text( $product_id );
		$button_text = '' !== $button_text ? $button_text : esc_html__( 'ثبت‌نام در دوره', 'tadris' );
	}
	?>
	<div
		class="webmz-spw-sticky-cart<?php echo $is_variable ? ' webmz-spw-sticky-cart--scroll-only' : ''; ?>"
		data-webmz-spw-sticky-cart
		<?php echo $is_variable ? ' data-webmz-spw-sticky-scroll-only="yes"' : ''; ?>
		aria-hidden="true"
	>
		<div class="webmz-spw-sticky-cart__inner">
			<div class="webmz-spw-sticky-cart__price webmz-spw-price" data-webmz-spw-sticky-price aria-hidden="true"></div>
			<?php if ( ! $is_variable ) : ?>
				<button
					type="button"
					class="webmz-spw-sticky-cart__btn webmz-spw-add-to-cart-btn<?php echo $unavailable ? ' is-disabled' : ''; ?>"
					data-webmz-spw-sticky-add
					<?php if ( $is_simple_ajax ) : ?>
						data-webmz-spw-add
						data-product-id="<?php echo esc_attr( (string) $product_id ); ?>"
						data-quantity="1"
					<?php elseif ( $unavailable ) : ?>
						disabled aria-disabled="true"
					<?php endif; ?>
				>
					<span><?php echo esc_html( $button_text ); ?></span>
				</button>
			<?php else : ?>
				<button type="button" class="webmz-spw-sticky-cart__btn webmz-spw-add-to-cart-btn" data-webmz-spw-sticky-add>
					<span><?php echo esc_html( $button_text ); ?></span>
				</button>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'webmz_spw_render_sticky_add_to_cart_bar', 20 );

/**
 * Enqueue sticky cart assets on single product pages.
 *
 * @return void
 */
function webmz_spw_enqueue_sticky_cart_assets() {
	if ( ! webmz_spw_sticky_cart_is_enabled() ) {
		return;
	}

	if ( ! function_exists( 'is_product' ) || ! is_product() || ! function_exists( 'wc_get_product' ) ) {
		return;
	}

	$product = wc_get_product( get_queried_object_id() );

	if ( ! $product || ! $product->is_purchasable() ) {
		return;
	}

	if ( wp_style_is( 'webmz-single-product-webmasters', 'registered' ) ) {
		wp_enqueue_style( 'webmz-single-product-webmasters' );
	}

	if ( wp_script_is( 'webmz-tadris-widgets', 'registered' ) ) {
		wp_enqueue_script( 'webmz-tadris-widgets' );
	}

	if ( wp_script_is( 'webmz-single-product-webmasters', 'registered' ) ) {
		wp_enqueue_script( 'webmz-single-product-webmasters' );
	}

	if ( wp_style_is( 'webmz-header-commerce', 'registered' ) ) {
		wp_enqueue_style( 'webmz-header-commerce' );
	}

	if ( wp_script_is( 'webmz-header-commerce', 'registered' ) ) {
		wp_enqueue_script( 'webmz-header-commerce' );
	}

	if ( wp_style_is( 'webmz-mobile-offcanvas', 'registered' ) ) {
		wp_enqueue_style( 'webmz-mobile-offcanvas' );
	}

	if ( wp_script_is( 'webmz-mobile-offcanvas', 'registered' ) ) {
		wp_enqueue_script( 'webmz-mobile-offcanvas' );
	}

	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_script( 'wc-add-to-cart' );
	}
}
add_action( 'wp_enqueue_scripts', 'webmz_spw_enqueue_sticky_cart_assets', 30 );

/**
 * Register the purchase box / features bar metabox.
 *
 * Mirrors the FAQ and curriculum architecture: data saved on the product wins,
 * and the Elementor widget settings are only a fallback when the product has none.
 *
 * @return void
 */
function webmz_spw_register_purchase_meta_box() {
	if ( ! post_type_exists( 'product' ) ) {
		return;
	}

	add_meta_box(
		'webmz_spw_purchase_meta',
		esc_html__( 'کادر خرید و ویژگی‌های صفحه محصول - آکام', 'tadris' ),
		'webmz_spw_render_purchase_meta_box',
		'product',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'webmz_spw_register_purchase_meta_box' );

/**
 * Render one icon + text(s) repeater row.
 *
 * @param string               $name   Field base name.
 * @param int|string           $index  Row index (or {{index}} in templates).
 * @param array<string,string> $item   Row values.
 * @param array<string,string> $fields Field key => label (besides icon).
 * @return void
 */
function webmz_spw_render_icon_repeater_row( $name, $index, $item, $fields ) {
	?>
	<div class="webmz-spw-icon-row" style="display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end;border:1px solid #ddd;padding:10px;margin-bottom:8px;background:#fff;">
		<p style="margin:0;flex:0 0 170px;">
			<label><strong><?php esc_html_e( 'آیکون (کلاس Font Awesome)', 'tadris' ); ?></strong></label><br>
			<input class="widefat" type="text" dir="ltr" list="webmz-spw-icon-suggestions" name="<?php echo esc_attr( $name . '[' . $index . '][icon]' ); ?>" value="<?php echo esc_attr( $item['icon'] ?? '' ); ?>" placeholder="fas fa-check">
		</p>
		<?php foreach ( $fields as $key => $label ) : ?>
			<p style="margin:0;flex:1 1 200px;">
				<label><strong><?php echo esc_html( $label ); ?></strong></label><br>
				<input class="widefat" type="text" name="<?php echo esc_attr( $name . '[' . $index . '][' . $key . ']' ); ?>" value="<?php echo esc_attr( $item[ $key ] ?? '' ); ?>">
			</p>
		<?php endforeach; ?>
		<p style="margin:0;"><button type="button" class="button webmz-spw-icon-row-remove"><?php esc_html_e( 'حذف', 'tadris' ); ?></button></p>
	</div>
	<?php
}

/**
 * Render an icon repeater with its add button and row template.
 *
 * @param string                          $name   Field base name.
 * @param array<int,array<string,string>> $items  Saved rows.
 * @param array<string,string>            $fields Field key => label (besides icon).
 * @param string                          $add    Add button label.
 * @return void
 */
function webmz_spw_render_icon_repeater( $name, $items, $fields, $add ) {
	?>
	<div class="webmz-spw-icon-repeater" data-webmz-spw-icon-repeater>
		<div class="webmz-spw-icon-repeater__rows">
			<?php
			foreach ( array_values( $items ) as $index => $item ) {
				webmz_spw_render_icon_repeater_row( $name, $index, $item, $fields );
			}
			?>
		</div>
		<template class="webmz-spw-icon-repeater__template">
			<?php webmz_spw_render_icon_repeater_row( $name, '{{index}}', array(), $fields ); ?>
		</template>
		<p><button type="button" class="button button-secondary webmz-spw-icon-repeater__add"><?php echo esc_html( $add ); ?></button></p>
	</div>
	<?php
}

/**
 * Render purchase box / features bar metabox.
 *
 * @param WP_Post $post Product post.
 * @return void
 */
function webmz_spw_render_purchase_meta_box( $post ) {
	wp_nonce_field( 'webmz_spw_save_purchase_meta', 'webmz_spw_purchase_meta_nonce' );

	$button_text  = (string) get_post_meta( $post->ID, '_webmz_add_to_cart_text', true );
	$extra        = webmz_spw_get_extra_button( $post->ID, false );
	$features     = webmz_spw_get_single_feature_items( $post->ID );
	$box_items    = webmz_spw_get_purchase_box_items( $post->ID );
	$icon_choices = array( 'fas fa-check', 'fas fa-check-circle', 'fas fa-infinity', 'fas fa-download', 'fas fa-certificate', 'fas fa-award', 'fas fa-headset', 'fas fa-clock', 'fas fa-video', 'fas fa-play-circle', 'fas fa-file-alt', 'fas fa-gift', 'fas fa-shield-alt', 'fas fa-bolt', 'fas fa-users', 'fas fa-user-graduate', 'fas fa-laptop-code', 'fas fa-mobile-alt', 'fas fa-comments', 'fas fa-star', 'fas fa-paperclip', 'fas fa-sync' );
	?>
	<datalist id="webmz-spw-icon-suggestions">
		<?php foreach ( $icon_choices as $icon_choice ) : ?>
			<option value="<?php echo esc_attr( $icon_choice ); ?>"></option>
		<?php endforeach; ?>
	</datalist>

	<p class="description">
		<?php esc_html_e( 'هر مقداری که اینجا پر شود فقط برای همین محصول در ویجت‌های صفحه سینگل نمایش داده می‌شود. بخش‌های خالی از تنظیمات ویجت در المنتور استفاده می‌کنند.', 'tadris' ); ?>
	</p>

	<h4><?php esc_html_e( 'دکمه خرید', 'tadris' ); ?></h4>
	<p>
		<label for="webmz_add_to_cart_text"><strong><?php esc_html_e( 'متن دکمه افزودن به سبد خرید', 'tadris' ); ?></strong></label><br>
		<input class="widefat" type="text" id="webmz_add_to_cart_text" name="webmz_add_to_cart_text" value="<?php echo esc_attr( $button_text ); ?>" placeholder="<?php esc_attr_e( 'مثال: ثبت‌نام در دوره، خرید خدمت، دریافت فایل', 'tadris' ); ?>">
		<span class="description"><?php esc_html_e( 'خالی = متن تنظیم‌شده در ویجت. روی همه دکمه‌های خرید این محصول و نوار خرید چسبان اعمال می‌شود.', 'tadris' ); ?></span>
	</p>

	<h4><?php esc_html_e( 'دکمه دوم کادر خرید (اختیاری)', 'tadris' ); ?></h4>
	<p class="description"><?php esc_html_e( 'برای نسخه رایگان، دمو، فایل نمونه یا هر لینک دیگری زیر دکمه خرید. لینک ویدیو (MP4، آپارات یا یوتیوب) در پلیر همین صفحه پخش می‌شود.', 'tadris' ); ?></p>
	<p style="display:flex;flex-wrap:wrap;gap:8px;">
		<span style="flex:1 1 200px;">
			<label for="webmz_extra_button_text"><strong><?php esc_html_e( 'متن دکمه', 'tadris' ); ?></strong></label><br>
			<input class="widefat" type="text" id="webmz_extra_button_text" name="webmz_extra_button_text" value="<?php echo esc_attr( $extra['text'] ); ?>" placeholder="<?php esc_attr_e( 'مثال: دانلود نسخه رایگان', 'tadris' ); ?>">
		</span>
		<span style="flex:2 1 300px;">
			<label for="webmz_extra_button_url"><strong><?php esc_html_e( 'لینک', 'tadris' ); ?></strong></label><br>
			<input class="widefat" type="url" dir="ltr" id="webmz_extra_button_url" name="webmz_extra_button_url" value="<?php echo esc_attr( $extra['url'] ); ?>" placeholder="https://">
		</span>
	</p>
	<p>
		<label><input type="checkbox" name="webmz_extra_button_new_tab" value="1"<?php checked( $extra['new_tab'] ); ?>> <?php esc_html_e( 'باز شدن در تب جدید', 'tadris' ); ?></label>
	</p>

	<hr>
	<h4><?php esc_html_e( 'نوار ویژگی‌ها (ویجت «ویژگی‌های دوره»)', 'tadris' ); ?></h4>
	<?php
	webmz_spw_render_icon_repeater(
		'webmz_spw_features',
		$features,
		array(
			'title'    => __( 'عنوان', 'tadris' ),
			'subtitle' => __( 'زیرعنوان', 'tadris' ),
		),
		__( 'افزودن ویژگی', 'tadris' )
	);
	?>

	<hr>
	<h4><?php esc_html_e( 'موارد زیر دکمه در «کادر خرید دوره»', 'tadris' ); ?></h4>
	<?php
	webmz_spw_render_icon_repeater(
		'webmz_spw_purchase_items',
		$box_items,
		array(
			'text' => __( 'متن', 'tadris' ),
		),
		__( 'افزودن مورد', 'tadris' )
	);
	?>
	<p class="description"><?php esc_html_e( 'آیکون خالی = آیکون همان ردیف در تنظیمات ویجت.', 'tadris' ); ?></p>

	<script>
	(function () {
		document.querySelectorAll('[data-webmz-spw-icon-repeater]').forEach(function (repeater) {
			var rows = repeater.querySelector('.webmz-spw-icon-repeater__rows');
			var template = repeater.querySelector('.webmz-spw-icon-repeater__template');
			var counter = rows.children.length;

			repeater.querySelector('.webmz-spw-icon-repeater__add').addEventListener('click', function () {
				var html = template.innerHTML.replace(/\{\{index\}\}/g, String(counter++));
				rows.insertAdjacentHTML('beforeend', html);
			});

			rows.addEventListener('click', function (event) {
				if (event.target.classList.contains('webmz-spw-icon-row-remove')) {
					event.preventDefault();
					event.target.closest('.webmz-spw-icon-row').remove();
				}
			});
		});
	}());
	</script>
	<?php
}

/**
 * Sanitize an icon repeater from $_POST.
 *
 * @param string   $key    POST key.
 * @param string[] $fields Text field keys (besides icon); the first one is required.
 * @return array<int,array<string,string>>
 */
function webmz_spw_sanitize_icon_repeater( $key, $fields ) {
	$items = array();

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified by caller.
	if ( empty( $_POST[ $key ] ) || ! is_array( $_POST[ $key ] ) ) {
		return $items;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.
	foreach ( wp_unslash( $_POST[ $key ] ) as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$item = array( 'icon' => webmz_spw_sanitize_icon_class( $row['icon'] ?? '' ) );

		foreach ( $fields as $field ) {
			$item[ $field ] = isset( $row[ $field ] ) ? sanitize_text_field( $row[ $field ] ) : '';
		}

		if ( '' === trim( $item[ $fields[0] ] ) ) {
			continue;
		}

		$items[] = $item;
	}

	return $items;
}

/**
 * Keep only characters valid in Font Awesome class lists.
 *
 * @param string $value Raw class list.
 * @return string
 */
function webmz_spw_sanitize_icon_class( $value ) {
	$value = strtolower( trim( (string) $value ) );
	$value = preg_replace( '/[^a-z0-9\- ]/', '', $value );

	return trim( preg_replace( '/\s+/', ' ', (string) $value ) );
}

/**
 * Save purchase box / features bar metabox.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return void
 */
function webmz_spw_save_purchase_meta( $post_id, $post ) {
	if ( ! isset( $_POST['webmz_spw_purchase_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_spw_purchase_meta_nonce'] ) ), 'webmz_spw_save_purchase_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) || 'product' !== $post->post_type ) {
		return;
	}

	update_post_meta( $post_id, '_webmz_add_to_cart_text', isset( $_POST['webmz_add_to_cart_text'] ) ? sanitize_text_field( wp_unslash( $_POST['webmz_add_to_cart_text'] ) ) : '' );
	update_post_meta( $post_id, '_webmz_extra_button_text', isset( $_POST['webmz_extra_button_text'] ) ? sanitize_text_field( wp_unslash( $_POST['webmz_extra_button_text'] ) ) : '' );
	update_post_meta( $post_id, '_webmz_extra_button_url', isset( $_POST['webmz_extra_button_url'] ) ? esc_url_raw( wp_unslash( $_POST['webmz_extra_button_url'] ) ) : '' );
	update_post_meta( $post_id, '_webmz_extra_button_new_tab', empty( $_POST['webmz_extra_button_new_tab'] ) ? '' : '1' );
	update_post_meta( $post_id, '_webmz_spw_features', webmz_spw_sanitize_icon_repeater( 'webmz_spw_features', array( 'title', 'subtitle' ) ) );
	update_post_meta( $post_id, '_webmz_spw_purchase_items', webmz_spw_sanitize_icon_repeater( 'webmz_spw_purchase_items', array( 'text' ) ) );
}
add_action( 'save_post', 'webmz_spw_save_purchase_meta', 10, 2 );

/**
 * Normalize a stored icon repeater.
 *
 * @param mixed    $items  Stored value.
 * @param string[] $fields Text field keys; the first one is required.
 * @return array<int,array<string,string>>
 */
function webmz_spw_normalize_icon_items( $items, $fields ) {
	if ( ! is_array( $items ) ) {
		return array();
	}

	$normalized = array();

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) || '' === trim( (string) ( $item[ $fields[0] ] ?? '' ) ) ) {
			continue;
		}

		$row = array( 'icon' => webmz_spw_sanitize_icon_class( $item['icon'] ?? '' ) );

		foreach ( $fields as $field ) {
			$row[ $field ] = (string) ( $item[ $field ] ?? '' );
		}

		$normalized[] = $row;
	}

	return $normalized;
}

/**
 * Per-product features bar items.
 *
 * @param int $product_id Product ID.
 * @return array<int,array{icon:string,title:string,subtitle:string}>
 */
function webmz_spw_get_single_feature_items( $product_id ) {
	return webmz_spw_normalize_icon_items( get_post_meta( absint( $product_id ), '_webmz_spw_features', true ), array( 'title', 'subtitle' ) );
}

/**
 * Per-product purchase box lines.
 *
 * @param int $product_id Product ID.
 * @return array<int,array{icon:string,text:string}>
 */
function webmz_spw_get_purchase_box_items( $product_id ) {
	return webmz_spw_normalize_icon_items( get_post_meta( absint( $product_id ), '_webmz_spw_purchase_items', true ), array( 'text' ) );
}

/**
 * Per-product add-to-cart label.
 *
 * @param int $product_id Product ID.
 * @return string Empty when the product does not override it.
 */
function webmz_spw_get_add_to_cart_text( $product_id ) {
	return trim( (string) get_post_meta( absint( $product_id ), '_webmz_add_to_cart_text', true ) );
}

/**
 * Per-product secondary purchase box button.
 *
 * @param int  $product_id    Product ID.
 * @param bool $require_link  Return empty values when no URL is set.
 * @return array{text:string,url:string,new_tab:bool}
 */
function webmz_spw_get_extra_button( $product_id, $require_link = true ) {
	$product_id = absint( $product_id );
	$button     = array(
		'text'    => (string) get_post_meta( $product_id, '_webmz_extra_button_text', true ),
		'url'     => esc_url_raw( (string) get_post_meta( $product_id, '_webmz_extra_button_url', true ) ),
		'new_tab' => '1' === get_post_meta( $product_id, '_webmz_extra_button_new_tab', true ),
	);

	if ( $require_link && '' === $button['url'] ) {
		return array(
			'text'    => '',
			'url'     => '',
			'new_tab' => false,
		);
	}

	return $button;
}

/**
 * Convert a Font Awesome class list to an Elementor icon setting.
 *
 * @param string              $class    Class list, e.g. "fas fa-check".
 * @param array<string,mixed> $fallback Icon used when $class is empty.
 * @return array<string,mixed>
 */
function webmz_spw_icon_setting( $class, $fallback = array() ) {
	$class = webmz_spw_sanitize_icon_class( $class );

	if ( '' === $class ) {
		return is_array( $fallback ) ? $fallback : array();
	}

	$libraries = array(
		'fas' => 'fa-solid',
		'far' => 'fa-regular',
		'fab' => 'fa-brands',
	);
	$prefix    = strtok( $class, ' ' );

	return array(
		'value'   => $class,
		'library' => isset( $libraries[ $prefix ] ) ? $libraries[ $prefix ] : 'fa-solid',
	);
}

/**
 * Apply the per-product label to WooCommerce's own single add-to-cart button too.
 *
 * @param string          $text    Button text.
 * @param \WC_Product|null $product Product.
 * @return string
 */
function webmz_spw_filter_single_add_to_cart_text( $text, $product = null ) {
	if ( $product instanceof \WC_Product ) {
		$custom = webmz_spw_get_add_to_cart_text( $product->get_id() );

		if ( '' !== $custom ) {
			return $custom;
		}
	}

	return $text;
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'webmz_spw_filter_single_add_to_cart_text', 20, 2 );

/**
 * Find the widget type that directly follows an element in Elementor data.
 *
 * @param array<int,mixed> $elements   Elementor elements tree.
 * @param string           $element_id Element ID to look for.
 * @return string|null Next sibling widget type ('' when last), or null when not found.
 */
function webmz_spw_find_next_sibling_widget( $elements, $element_id ) {
	$elements = array_values( (array) $elements );

	foreach ( $elements as $index => $element ) {
		if ( ! is_array( $element ) ) {
			continue;
		}

		if ( isset( $element['id'] ) && (string) $element['id'] === $element_id ) {
			$next = $elements[ $index + 1 ] ?? array();

			return is_array( $next ) ? (string) ( $next['widgetType'] ?? '' ) : '';
		}

		if ( ! empty( $element['elements'] ) ) {
			$found = webmz_spw_find_next_sibling_widget( $element['elements'], $element_id );

			if ( null !== $found ) {
				return $found;
			}
		}
	}

	return null;
}

/**
 * Widget type that follows a single-product section heading in its layout.
 *
 * Read from the saved layout data rather than render hooks, because Elementor's
 * element cache renders widgets outside the normal before_render flow.
 *
 * @param string $heading_id Heading element ID.
 * @return string
 */
function webmz_spw_section_heading_next_widget( $heading_id ) {
	static $data_by_document = array();

	$document_ids = array();

	if ( ! empty( $GLOBALS['webmz_rendering_layout_id'] ) ) {
		$document_ids[] = absint( $GLOBALS['webmz_rendering_layout_id'] );
	}

	if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->documents ) ) {
		$document = \Elementor\Plugin::$instance->documents->get_current();

		if ( $document ) {
			$document_ids[] = (int) $document->get_main_id();
		}
	}

	foreach ( array_unique( array_filter( $document_ids ) ) as $document_id ) {
		if ( ! isset( $data_by_document[ $document_id ] ) ) {
			$raw  = get_post_meta( $document_id, '_elementor_data', true );
			$data = is_array( $raw ) ? $raw : json_decode( (string) $raw, true );

			$data_by_document[ $document_id ] = is_array( $data ) ? $data : array();
		}

		$next = webmz_spw_find_next_sibling_widget( $data_by_document[ $document_id ], (string) $heading_id );

		if ( null !== $next ) {
			return $next;
		}
	}

	return '';
}

/**
 * Whether a single-product section has content for a product.
 *
 * @param string $section    curriculum|faq|content, or a widget name mapping to one.
 * @param int    $product_id Product ID.
 * @return bool Unknown sections count as having content.
 */
function webmz_spw_product_section_has_content( $section, $product_id ) {
	$aliases = array(
		'webmz-spw-course-curriculum' => 'curriculum',
		'webmz-spw-faq'               => 'faq',
		'webmz-spw-product-content'   => 'content',
	);
	$section = isset( $aliases[ $section ] ) ? $aliases[ $section ] : $section;

	switch ( $section ) {
		case 'curriculum':
			return function_exists( 'webmz_spw_get_product_curriculum' ) && ! empty( webmz_spw_get_product_curriculum( $product_id ) );
		case 'faq':
			return ! empty( webmz_spw_get_product_faq_items( $product_id ) );
		case 'content':
			return '' !== trim( wp_strip_all_tags( (string) get_post_field( 'post_content', $product_id ) ) );
	}

	return true;
}
