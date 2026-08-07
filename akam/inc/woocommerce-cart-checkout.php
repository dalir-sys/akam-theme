<?php
/**
 * WebMZ WooCommerce cart and checkout customizations.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether current request is the order-pay checkout endpoint.
 *
 * @return bool
 */
function webmz_wc_is_order_pay_page() {
	return function_exists( 'is_checkout' ) && is_checkout() && is_wc_endpoint_url( 'order-pay' );
}

/**
 * Whether current request is the order-received checkout endpoint.
 *
 * @return bool
 */
function webmz_wc_is_order_received_page() {
	return function_exists( 'is_checkout' ) && is_checkout() && is_wc_endpoint_url( 'order-received' );
}

/**
 * Whether current request should use cart/checkout theme styles.
 *
 * @return bool
 */
function webmz_wc_is_styled_checkout_page() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		return false;
	}

	return ! is_wc_endpoint_url() || webmz_wc_is_order_pay_page() || webmz_wc_is_order_received_page();
}

/**
 * Add body classes for cart and checkout pages.
 *
 * @param array<int,string> $classes Body classes.
 * @return array<int,string>
 */
function webmz_wc_cart_checkout_body_class( $classes ) {
	if ( function_exists( 'is_cart' ) && is_cart() ) {
		$classes[] = 'webmz-wc-cart';
	}

	if ( webmz_wc_is_styled_checkout_page() ) {
		$classes[] = 'webmz-wc-checkout';
	}

	return $classes;
}
add_filter( 'body_class', 'webmz_wc_cart_checkout_body_class' );

/**
 * Enqueue cart/checkout stylesheet.
 *
 * @return void
 */
function webmz_wc_cart_checkout_enqueue_assets() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	if ( ! is_cart() && ! webmz_wc_is_styled_checkout_page() ) {
		return;
	}

	$deps = array( 'webmz-main' );

	foreach ( array( 'woocommerce-general', 'woocommerce-layout', 'woocommerce-smallscreen' ) as $handle ) {
		if ( wp_style_is( $handle, 'registered' ) ) {
			$deps[] = $handle;
		}
	}

	if ( is_rtl() ) {
		if ( wp_style_is( 'woocommerce-rtl', 'registered' ) ) {
			$deps[] = 'woocommerce-rtl';
		}
		if ( wp_style_is( 'webmz-rtl', 'registered' ) ) {
			$deps[] = 'webmz-rtl';
		}
	}

	wp_enqueue_style(
		'webmz-wc-cart-checkout',
		WEBMZ_URI . 'assets/css/woocommerce-cart-checkout.css',
		$deps,
		WEBMZ_VERSION
	);

	wp_add_inline_style(
		'webmz-wc-cart-checkout',
		'.webmz-wc-cart,.webmz-wc-checkout{--woocommerce:var(--webmz-wc-primary);--wc-primary:var(--webmz-wc-primary);--wc-primary-text:#fff;}'
	);

	if ( is_cart() ) {
		wp_enqueue_script(
			'webmz-wc-cart',
			WEBMZ_URI . 'assets/js/woocommerce-cart.js',
			array( 'jquery', 'wc-cart' ),
			WEBMZ_VERSION,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'webmz_wc_cart_checkout_enqueue_assets', 100 );

/**
 * Render WooCommerce breadcrumb trail.
 *
 * @return void
 */
function webmz_wc_render_breadcrumb() {
	if ( ! function_exists( 'is_cart' ) ) {
		return;
	}

	$is_cart = is_cart();

	if ( ! $is_cart && ! webmz_wc_is_styled_checkout_page() ) {
		return;
	}

	$site_name = get_bloginfo( 'name' );
	$items     = array(
		array(
			'label' => $site_name,
			'url'   => home_url( '/' ),
		),
	);

	if ( $is_cart ) {
		$items[] = array(
			'label' => esc_html__( 'سبد خرید', 'tadris' ),
			'url'   => '',
		);
	} elseif ( webmz_wc_is_order_received_page() ) {
		$items[] = array(
			'label' => esc_html__( 'تأیید سفارش', 'tadris' ),
			'url'   => '',
		);
	} elseif ( webmz_wc_is_order_pay_page() ) {
		$items[] = array(
			'label' => esc_html__( 'پرداخت سفارش', 'tadris' ),
			'url'   => '',
		);
	} elseif ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url() ) {
		$items[] = array(
			'label' => esc_html__( 'پرداخت', 'tadris' ),
			'url'   => '',
		);
	}

	echo '<nav class="webmz-wc-breadcrumb" aria-label="' . esc_attr__( 'مسیر صفحه', 'tadris' ) . '">';
	echo '<ol class="webmz-wc-breadcrumb__list">';

	$total = count( $items );
	foreach ( $items as $index => $item ) {
		$is_current = ( $index === $total - 1 );
		echo '<li class="webmz-wc-breadcrumb__item' . ( $is_current ? ' is-current' : '' ) . '">';

		if ( $index > 0 ) {
			echo '<span class="webmz-wc-breadcrumb__sep" aria-hidden="true">/</span>';
		}

		if ( $is_current || empty( $item['url'] ) ) {
			echo '<span class="webmz-wc-breadcrumb__current"' . ( $is_current ? ' aria-current="page"' : '' ) . '>' . esc_html( $item['label'] ) . '</span>';
		} else {
			echo '<a class="webmz-wc-breadcrumb__link" href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
		}

		echo '</li>';
	}

	echo '</ol>';
	echo '</nav>';
}
add_action( 'woocommerce_before_cart', 'webmz_wc_render_breadcrumb', 5 );
add_action( 'woocommerce_before_checkout_form', 'webmz_wc_render_breadcrumb', 5 );
add_action( 'before_woocommerce_pay_form', 'webmz_wc_render_breadcrumb', 5 );
add_action( 'woocommerce_before_main_content', 'webmz_wc_render_order_received_breadcrumb', 15 );

/**
 * Render breadcrumb on the order-received endpoint.
 *
 * @return void
 */
function webmz_wc_render_order_received_breadcrumb() {
	if ( ! webmz_wc_is_order_received_page() ) {
		return;
	}

	webmz_wc_render_breadcrumb();
}

/**
 * Custom cart remove link markup.
 *
 * @param string $link    Remove link HTML.
 * @param string $cart_item_key Cart item key.
 * @return string
 */
function webmz_wc_cart_item_remove_link( $link, $cart_item_key ) {
	$cart_item = WC()->cart->get_cart()[ $cart_item_key ] ?? null;

	if ( ! $cart_item ) {
		return $link;
	}

	$_product   = $cart_item['data'];
	$product_id = $cart_item['product_id'];

	return sprintf(
		'<a role="button" href="%s" class="remove webmz-wc-remove-btn" aria-label="%s" data-product_id="%s" data-product_sku="%s">%s</a>',
		esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
		esc_attr( sprintf( __( 'حذف %s از سبد خرید', 'tadris' ), wp_strip_all_tags( $_product->get_name() ) ) ),
		esc_attr( $product_id ),
		esc_attr( $_product->get_sku() ),
		esc_html__( 'حذف', 'tadris' )
	);
}
add_filter( 'woocommerce_cart_item_remove_link', 'webmz_wc_cart_item_remove_link', 10, 2 );

/**
 * Reorder checkout order review hooks for split layout.
 *
 * @return void
 */
function webmz_wc_checkout_order_review_hooks() {
	if ( ! is_checkout() || is_wc_endpoint_url() ) {
		return;
	}

	remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
	remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

	add_action( 'woocommerce_checkout_order_review', 'webmz_wc_checkout_render_totals_column', 10 );
	add_action( 'woocommerce_checkout_order_review', 'webmz_wc_checkout_render_payment_column', 20 );
}
add_action( 'wp', 'webmz_wc_checkout_order_review_hooks' );

/**
 * Render checkout totals column.
 *
 * @return void
 */
function webmz_wc_checkout_render_totals_column() {
	echo '<div class="webmz-checkout-totals-col">';
	wc_get_template( 'checkout/review-order.php' );
	echo '<div class="webmz-checkout-place-order">';
	wc_get_template( 'checkout/terms.php' );
	do_action( 'woocommerce_review_order_before_submit' );
	echo apply_filters(
		'woocommerce_order_button_html',
		'<button type="submit" class="button alt webmz-wc-btn-arrow" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( apply_filters( 'woocommerce_order_button_text', __( 'انتقال به درگاه پرداخت', 'tadris' ) ) ) . '" data-value="' . esc_attr( apply_filters( 'woocommerce_order_button_text', __( 'انتقال به درگاه پرداخت', 'tadris' ) ) ) . '">' . esc_html( apply_filters( 'woocommerce_order_button_text', __( 'انتقال به درگاه پرداخت', 'tadris' ) ) ) . '</button>'
	);
	do_action( 'woocommerce_review_order_after_submit' );
	echo '</div>';
	echo '</div>';
}

/**
 * Render checkout payment methods column.
 *
 * @return void
 */
function webmz_wc_checkout_render_payment_column() {
	echo '<div class="webmz-checkout-payment-col">';
	wc_get_template(
		'checkout/payment.php',
		array(
			'checkout'           => WC()->checkout(),
			'available_gateways' => WC()->payment_gateways()->get_available_payment_gateways(),
			'order_button_text'  => apply_filters( 'woocommerce_order_button_text', __( 'انتقال به درگاه پرداخت', 'tadris' ) ),
			'webmz_hide_submit'  => true,
		)
	);
	echo '</div>';
}

/**
 * Customize checkout order button text.
 *
 * @param string $text Button text.
 * @return string
 */
function webmz_wc_order_button_text( $text ) {
	return esc_html__( 'انتقال به درگاه پرداخت', 'tadris' );
}
add_filter( 'woocommerce_order_button_text', 'webmz_wc_order_button_text' );
add_filter( 'woocommerce_pay_order_button_text', 'webmz_wc_order_button_text' );

/**
 * Whether the payment column should hide its own submit button.
 *
 * @return bool
 */
function webmz_wc_should_hide_payment_submit() {
	return function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url();
}

/**
 * Section heading with decorative icon.
 *
 * @param string $title Section title.
 * @param string $tag   HTML tag.
 * @return void
 */
function webmz_wc_section_heading( $title, $tag = 'h2' ) {
	$allowed = array( 'h2', 'h3' );
	$tag     = in_array( $tag, $allowed, true ) ? $tag : 'h2';

	printf(
		'<%1$s class="webmz-wc-section-title"><span class="webmz-wc-section-title__icon" aria-hidden="true"></span><span class="webmz-wc-section-title__text">%2$s</span></%1$s>',
		$tag,
		esc_html( $title )
	);
}
