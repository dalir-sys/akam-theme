<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

$webmz_shipping_fields                  = $checkout->get_checkout_fields( 'shipping' );
$webmz_has_shipping_fields              = ! empty( $webmz_shipping_fields ) && is_array( $webmz_shipping_fields );
$webmz_has_order_notes                  = function_exists( 'webmz_checkout_is_field_visible' ) ? webmz_checkout_is_field_visible( 'order_comments' ) : true;
$webmz_render_checkout_secondary_column = $webmz_has_shipping_fields || $webmz_has_order_notes;

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout webmz-checkout-form" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set webmz-checkout-customer <?php echo esc_attr( $webmz_render_checkout_secondary_column ? 'webmz-checkout-has-secondary-fields' : 'webmz-checkout-no-secondary-fields' ); ?>" id="customer_details">
			<div class="col-1">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<?php if ( $webmz_render_checkout_secondary_column ) : ?>
				<div class="col-2">
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>

	<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

	<div class="webmz-checkout-order-section order_review_tag">
		<?php webmz_wc_section_heading( esc_html__( 'خلاصه سفارش و پرداخت', 'tadris' ), 'h3' ); ?>

		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

		<div id="order_review" class="woocommerce-checkout-review-order webmz-checkout-order-grid">
			<?php do_action( 'woocommerce_checkout_order_review' ); ?>
		</div>

		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
	</div>

	<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
