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
		esc_html__( 'ویژگی‌های محصول', 'tadris' ),
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
		<label for="webmz_product_intro_video"><strong><?php esc_html_e( 'ویدیو معرفی محصول (MP4)', 'tadris' ); ?></strong></label><br>
		<input class="widefat" type="url" id="webmz_product_intro_video" name="webmz_product_intro_video" value="<?php echo esc_attr( $intro_video ); ?>" placeholder="https://example.com/intro.mp4">
	</p>
	<p class="description"><?php esc_html_e( 'در صورت پر شدن، در ویجت تصویر محصول به‌جای تصویر شاخص، پلیر plyr با پوستر تصویر شاخص نمایش داده می‌شود.', 'tadris' ); ?></p>

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
		$button_text = esc_html__( 'ثبت‌نام در دوره', 'tadris' );
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
