<?php
/**
 * Checkout coupon form
 *
 * @package WebMZ
 * @version 9.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) {
	return;
}
?>
<div class="woocommerce-form-coupon-toggle">
	<?php
	wc_print_notice(
		apply_filters(
			'woocommerce_checkout_coupon_message',
			esc_html__( 'کد تخفیف دارید؟', 'tadris' ) . '&nbsp;<a href="#" role="button" aria-label="' . esc_attr__( 'وارد کردن کد تخفیف', 'tadris' ) . '" aria-controls="woocommerce-checkout-form-coupon" aria-expanded="false" class="showcoupon">' . esc_html__( 'اینجا کلیک کنید', 'tadris' ) . '</a>'
		),
		'notice'
	);
	?>
</div>

<form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none" id="woocommerce-checkout-form-coupon">
	<p class="form-row form-row-first">
		<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'کد تخفیف', 'tadris' ); ?></label>
		<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'کد تخفیف', 'tadris' ); ?>" id="coupon_code" value="" />
	</p>

	<p class="form-row form-row-last">
		<button type="submit" class="button webmz-wc-btn-arrow<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'اعمال تخفیف', 'tadris' ); ?>">
			<?php esc_html_e( 'اعمال تخفیف', 'tadris' ); ?>
		</button>
	</p>

	<div class="clear"></div>
</form>
