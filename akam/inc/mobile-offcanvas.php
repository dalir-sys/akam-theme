<?php
/**
 * Helpers for mobile off-canvas header widgets.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return a sanitized off-canvas side slug.
 *
 * @param string $side Requested side.
 * @return string
 */
function webmz_mobile_offcanvas_side( $side ) {
	$side = sanitize_key( $side );

	return in_array( $side, array( 'left', 'right' ), true ) ? $side : 'right';
}

/**
 * Return cart item count for header badges.
 *
 * @return int
 */
function webmz_mobile_header_cart_count() {
	if ( ! function_exists( 'webmz_header_cart_is_ready' ) || ! webmz_header_cart_is_ready() ) {
		return 0;
	}

	return absint( WC()->cart->get_cart_contents_count() );
}

/**
 * Return WooCommerce cart page URL.
 *
 * @return string
 */
function webmz_mobile_cart_url() {
	if ( function_exists( 'wc_get_cart_url' ) ) {
		return (string) wc_get_cart_url();
	}

	return home_url( '/' );
}

/**
 * Return account URL for guests or logged-in users.
 *
 * @return string
 */
function webmz_mobile_account_url() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$url = wc_get_page_permalink( 'myaccount' );

		if ( $url ) {
			return $url;
		}
	}

	return is_user_logged_in() ? get_edit_profile_url( get_current_user_id() ) : wp_login_url( home_url( '/' ) );
}

/**
 * Whether OTP popup should open for guest account links.
 *
 * @return bool
 */
function webmz_mobile_account_uses_otp_popup() {
	return function_exists( 'webmz_otp_is_enabled' ) && webmz_otp_is_enabled();
}

/**
 * Render WooCommerce account menu links for mobile off-canvas.
 *
 * @param string $logout_label Logout button label.
 * @return void
 */
function webmz_mobile_render_account_menu( $logout_label ) {
	if ( function_exists( 'wc_get_account_menu_items' ) && function_exists( 'wc_get_account_endpoint_url' ) ) {
		$items = wc_get_account_menu_items();
	} else {
		$items = array(
			'dashboard' => esc_html__( 'پروفایل کاربری', 'tadris' ),
		);
	}
	?>
	<nav class="webmz-maccount-panel__menu" aria-label="<?php esc_attr_e( 'منوی حساب کاربری', 'tadris' ); ?>">
		<?php foreach ( $items as $endpoint => $label ) : ?>
			<?php if ( 'customer-logout' === $endpoint ) : ?>
				<?php continue; ?>
			<?php endif; ?>
			<?php
			$url = function_exists( 'wc_get_account_endpoint_url' )
				? wc_get_account_endpoint_url( $endpoint )
				: get_edit_profile_url( get_current_user_id() );
			?>
			<a class="webmz-maccount-panel__link" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
		<?php endforeach; ?>
	</nav>
	<?php
	$logout_url = function_exists( 'wc_logout_url' )
		? wc_logout_url()
		: wp_logout_url( home_url( '/' ) );
	?>
	<a class="webmz-maccount-panel__logout" href="<?php echo esc_url( $logout_url ); ?>">
		<?php echo esc_html( $logout_label ); ?>
	</a>
	<?php
}
