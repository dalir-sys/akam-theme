<?php
/**
 * "Order received" message.
 *
 * @package WebMZ
 * @version 8.8.0
 *
 * @var WC_Order|false $order
 */

defined( 'ABSPATH' ) || exit;
?>

<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received webmz-order-received-notice">
	<?php
	$message = apply_filters(
		'woocommerce_thankyou_order_received_text',
		esc_html__( 'سپاسگزاریم. سفارش شما دریافت شد.', 'tadris' ),
		$order
	);

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $message;
	?>
</p>
