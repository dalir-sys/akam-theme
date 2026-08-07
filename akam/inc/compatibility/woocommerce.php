<?php
/**
 * WooCommerce support, wrappers and the isolated WebMZ header cart API.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Declare WooCommerce and gallery support.
 *
 * @return void
 */
function webmz_woocommerce_support() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 420,
			'single_image_width'    => 760,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'max_rows'        => 8,
				'default_columns' => 4,
				'min_columns'     => 1,
				'max_columns'     => 5,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'webmz_woocommerce_support', 20 );

/**
 * Replace default WooCommerce wrappers with the theme's container.
 *
 * @return void
 */
function webmz_woocommerce_wrapper_hooks() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	add_action( 'woocommerce_before_main_content', 'webmz_woocommerce_wrapper_start', 10 );
	add_action( 'woocommerce_after_main_content', 'webmz_woocommerce_wrapper_end', 10 );
}
add_action( 'wp', 'webmz_woocommerce_wrapper_hooks' );

/** Print WooCommerce opening wrapper. */
function webmz_woocommerce_wrapper_start() {
	echo '<main id="primary" class="site-main webmz-container webmz-woocommerce">';
}

/** Print WooCommerce closing wrapper. */
function webmz_woocommerce_wrapper_end() {
	echo '</main>';
}

/**
 * Print WooCommerce notices inside the theme container width.
 *
 * @return void
 */
function webmz_woocommerce_output_notices_in_container() {
	if ( ! function_exists( 'wc_get_notices' ) || ! function_exists( 'woocommerce_output_all_notices' ) ) {
		return;
	}

	if ( empty( wc_get_notices() ) ) {
		return;
	}

	echo '<div class="webmz-container webmz-wc-notices">';
	woocommerce_output_all_notices();
	echo '</div>';
}

/**
 * Replace default WooCommerce notice hooks so messages respect theme container width.
 *
 * @return void
 */
function webmz_woocommerce_notices_container_hooks() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );

	add_action( 'woocommerce_before_single_product', 'webmz_woocommerce_output_notices_in_container', 10 );
	add_action( 'woocommerce_before_shop_loop', 'webmz_woocommerce_output_notices_in_container', 10 );
}
add_action( 'wp', 'webmz_woocommerce_notices_container_hooks', 30 );

/**
 * Make sure a cart instance is available during normal and AJAX requests.
 *
 * @return bool
 */
function webmz_header_cart_is_ready() {
	if ( ! function_exists( 'WC' ) || ! class_exists( 'WooCommerce' ) ) {
		return false;
	}

	if ( ! WC()->cart && function_exists( 'wc_load_cart' ) ) {
		wc_load_cart();
	}

	return (bool) WC()->cart;
}

/**
 * Return a WooCommerce formatted monetary value as text only.
 *
 * Native price formatting is retained, but WooCommerce HTML classes are not
 * output inside the custom header cart component.
 *
 * @param string $formatted_price Price HTML returned by WooCommerce.
 * @return string
 */
function webmz_header_cart_price_text( $formatted_price ) {
	return trim( wp_strip_all_tags( html_entity_decode( (string) $formatted_price, ENT_QUOTES, get_bloginfo( 'charset' ) ) ) );
}

/**
 * Return display-safe variation labels using WebMZ-only markup at output time.
 *
 * @param array<string,mixed> $cart_item Cart item data.
 * @return array<int,array{label:string,value:string}>
 */
function webmz_header_cart_variation_rows( $cart_item ) {
	$rows = array();

	if ( empty( $cart_item['variation'] ) || ! is_array( $cart_item['variation'] ) ) {
		return $rows;
	}

	foreach ( $cart_item['variation'] as $attribute => $value ) {
		if ( '' === (string) $value ) {
			continue;
		}

		$taxonomy = str_replace( 'attribute_', '', $attribute );
		$label    = function_exists( 'wc_attribute_label' ) ? wc_attribute_label( $taxonomy ) : $taxonomy;
		$display  = $value;

		if ( taxonomy_exists( $taxonomy ) ) {
			$term = get_term_by( 'slug', $value, $taxonomy );
			if ( $term && ! is_wp_error( $term ) ) {
				$display = $term->name;
			}
		}

		$rows[] = array(
			'label' => sanitize_text_field( $label ),
			'value' => sanitize_text_field( $display ),
		);
	}

	return $rows;
}

/**
 * Render fully isolated WebMZ header cart content.
 *
 * No WooCommerce template is rendered and no WooCommerce presentation class is
 * emitted. WooCommerce is used only as the cart/data source.
 *
 * @return string
 */
function webmz_get_header_cart_content_html() {
	if ( ! webmz_header_cart_is_ready() ) {
		return '';
	}

	$cart = WC()->cart;

	ob_start();
	?>
	<div class="webmz-hcart__content" data-webmz-hcart-content>
		<?php if ( $cart->is_empty() ) : ?>
			<div class="webmz-hcart__empty">
				<span class="webmz-hcart__empty-icon" aria-hidden="true">🛒</span>
				<p><?php esc_html_e( 'سبد خرید شما خالی است.', 'tadris' ); ?></p>
				<a class="webmz-hcart__empty-link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
					<?php esc_html_e( 'مشاهده محصولات', 'tadris' ); ?>
				</a>
			</div>
		<?php else : ?>
			<ul class="webmz-hcart__items" aria-label="<?php esc_attr_e( 'محصولات سبد خرید', 'tadris' ); ?>">
				<?php foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) : ?>
					<?php
					$product = isset( $cart_item['data'] ) ? $cart_item['data'] : false;
					$qty     = isset( $cart_item['quantity'] ) ? absint( $cart_item['quantity'] ) : 0;

					if ( ! $product || ! $product->exists() || $qty < 1 ) {
						continue;
					}

					$product_id  = $product->get_id();
					$product_url = $product->is_visible() ? $product->get_permalink( $cart_item ) : '';
					$image_id    = $product->get_image_id();
					$price_text  = webmz_header_cart_price_text( $cart->get_product_price( $product ) );
					$variations  = webmz_header_cart_variation_rows( $cart_item );
					?>
					<li class="webmz-hcart__item">
						<button
							type="button"
							class="webmz-hcart__remove"
							data-webmz-remove-item="<?php echo esc_attr( $cart_item_key ); ?>"
							aria-label="<?php echo esc_attr( sprintf( __( 'حذف %s از سبد خرید', 'tadris' ), $product->get_name() ) ); ?>"
						>
							<span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg></span>
						</button>

						<?php if ( $product_url ) : ?>
							<a class="webmz-hcart__media" href="<?php echo esc_url( $product_url ); ?>">
						<?php else : ?>
							<span class="webmz-hcart__media">
						<?php endif; ?>

							<?php if ( $image_id ) : ?>
								<?php echo webmz_get_attachment_loop_image( $image_id, array( 'class' => 'webmz-hcart__image' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php else : ?>
								<span class="webmz-hcart__image-placeholder" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-contrast-2"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2l0 -14" /><path d="M3 19h2.25c3.728 0 6.75 -3.134 6.75 -7s3.022 -7 6.75 -7h2.25" /></svg></span>
							<?php endif; ?>

						<?php if ( $product_url ) : ?>
							</a>
						<?php else : ?>
							</span>
						<?php endif; ?>

						<div class="webmz-hcart__details">
							<?php if ( $product_url ) : ?>
								<a class="webmz-hcart__product-name" href="<?php echo esc_url( $product_url ); ?>">
									<?php echo esc_html( $product->get_name() ); ?>
								</a>
							<?php else : ?>
								<span class="webmz-hcart__product-name"><?php echo esc_html( $product->get_name() ); ?></span>
							<?php endif; ?>

							<?php if ( $variations ) : ?>
								<div class="webmz-hcart__variation">
									<?php foreach ( $variations as $variation ) : ?>
										<span><?php echo esc_html( $variation['label'] . ': ' . $variation['value'] ); ?></span>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<span class="webmz-hcart__line-price">
								<?php echo esc_html( sprintf( '%1$d × %2$s', $qty, $price_text ) ); ?>
							</span>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>

			<div class="webmz-hcart__subtotal">
				<span><?php esc_html_e( 'جمع سبد خرید', 'tadris' ); ?></span>
				<strong><?php echo esc_html( webmz_header_cart_price_text( $cart->get_cart_subtotal() ) ); ?></strong>
			</div>

			<div class="webmz-hcart__actions">
				<a class="webmz-hcart__action webmz-hcart__action--cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
					<?php esc_html_e( 'سبد خرید', 'tadris' ); ?>
				</a>
				<a class="webmz-hcart__action webmz-hcart__action--checkout" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
					<?php esc_html_e( 'تسویه حساب', 'tadris' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Build the live payload consumed by the custom WebMZ AJAX mini-cart.
 *
 * @return array<string,mixed>
 */
function webmz_get_header_cart_payload() {
	if ( ! webmz_header_cart_is_ready() ) {
		return array(
			'content_html' => '',
			'count'        => 0,
			'total_text'   => '',
			'is_empty'     => true,
		);
	}

	$cart = WC()->cart;

	return array(
		'content_html' => webmz_get_header_cart_content_html(),
		'count'        => absint( $cart->get_cart_contents_count() ),
		'total_text'   => webmz_header_cart_price_text( $cart->get_cart_subtotal() ),
		'is_empty'     => $cart->is_empty(),
	);
}

/**
 * Return current WebMZ cart contents through AJAX.
 *
 * @return void
 */
function webmz_ajax_header_cart_snapshot() {
	check_ajax_referer( 'webmz_header_cart', 'nonce' );

	if ( ! webmz_header_cart_is_ready() ) {
		wp_send_json_error( array( 'message' => esc_html__( 'سبد خرید در دسترس نیست.', 'tadris' ) ), 400 );
	}

	wp_send_json_success( webmz_get_header_cart_payload() );
}
add_action( 'wp_ajax_webmz_header_cart_snapshot', 'webmz_ajax_header_cart_snapshot' );
add_action( 'wp_ajax_nopriv_webmz_header_cart_snapshot', 'webmz_ajax_header_cart_snapshot' );

/**
 * Remove one cart item through the dedicated WebMZ AJAX endpoint.
 *
 * @return void
 */
function webmz_ajax_header_cart_remove_item() {
	check_ajax_referer( 'webmz_header_cart', 'nonce' );

	if ( ! webmz_header_cart_is_ready() ) {
		wp_send_json_error( array( 'message' => esc_html__( 'سبد خرید در دسترس نیست.', 'tadris' ) ), 400 );
	}

	$cart_item_key = isset( $_POST['cart_item_key'] )
		? wc_clean( wp_unslash( $_POST['cart_item_key'] ) )
		: '';

	if ( '' === $cart_item_key || ! isset( WC()->cart->get_cart()[ $cart_item_key ] ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'محصول موردنظر در سبد خرید پیدا نشد.', 'tadris' ) ), 404 );
	}

	if ( false === WC()->cart->remove_cart_item( $cart_item_key ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'حذف محصول انجام نشد.', 'tadris' ) ), 500 );
	}

	WC()->cart->calculate_totals();
	WC()->cart->set_session();

	wp_send_json_success( webmz_get_header_cart_payload() );
}
add_action( 'wp_ajax_webmz_header_cart_remove_item', 'webmz_ajax_header_cart_remove_item' );
add_action( 'wp_ajax_nopriv_webmz_header_cart_remove_item', 'webmz_ajax_header_cart_remove_item' );

/**
 * Check whether a simple or variation product is already in the cart.
 *
 * @param int $product_id   Parent/simple product ID.
 * @param int $variation_id Variation ID when applicable.
 * @return bool
 */
function webmz_cart_contains_product( $product_id, $variation_id = 0 ) {
	if ( ! webmz_header_cart_is_ready() ) {
		return false;
	}

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		$item_product_id   = (int) $cart_item['product_id'];
		$item_variation_id = (int) $cart_item['variation_id'];

		if ( $variation_id > 0 ) {
			if ( $item_variation_id === (int) $variation_id ) {
				return true;
			}
			continue;
		}

		if ( $item_product_id === (int) $product_id && 0 === $item_variation_id ) {
			return true;
		}
	}

	return false;
}

/**
 * Detect sold-individually products that are already present in the cart.
 *
 * @param int $product_id   Parent/simple product ID.
 * @param int $variation_id Variation ID when applicable.
 * @return bool
 */
function webmz_is_sold_individually_already_in_cart( $product_id, $variation_id = 0 ) {
	$check_id = $variation_id > 0 ? $variation_id : $product_id;
	$product  = wc_get_product( $check_id );

	if ( ! $product || ! $product->is_sold_individually() ) {
		return false;
	}

	return webmz_cart_contains_product( $product_id, $variation_id );
}

/**
 * Return a dedicated AJAX payload when a sold-individually product is re-added.
 *
 * @return void
 */
function webmz_ajax_add_to_cart_already_in_cart_check() {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce add_to_cart endpoint.
	if ( empty( $_POST['product_id'] ) || ! webmz_header_cart_is_ready() ) {
		return;
	}

	$product_id   = absint( wp_unslash( $_POST['product_id'] ) );
	$variation_id = ! empty( $_POST['variation_id'] ) ? absint( wp_unslash( $_POST['variation_id'] ) ) : 0;
	$product      = wc_get_product( $product_id );

	/*
	 * WC AJAX add_to_cart expects variation ID as product_id for variable products.
	 * Normalize to parent + variation before the sold-individually check.
	 */
	if ( $product && $product->is_type( 'variation' ) ) {
		$variation_id = $product_id;
		$product_id   = (int) $product->get_parent_id();
	}

	if ( ! webmz_is_sold_individually_already_in_cart( $product_id, $variation_id ) ) {
		return;
	}

	wp_send_json(
		array(
			'error'           => true,
			'already_in_cart' => true,
			'message'         => esc_html__( 'شما قبلاً این محصول را به سبد خریدتان اضافه کرده‌اید.', 'tadris' ),
			'checkout_url'    => wc_get_checkout_url(),
		)
	);
}
add_action( 'wc_ajax_add_to_cart', 'webmz_ajax_add_to_cart_already_in_cart_check', 1 );
