<?php
/**
 * Pay for order form
 *
 * @package WebMZ
 * @version 8.2.0
 */

defined( 'ABSPATH' ) || exit;

$totals = $order->get_order_item_totals(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
?>

<div class="webmz-checkout-order-section order_review_tag">
	<?php webmz_wc_section_heading( esc_html__( 'خلاصه سفارش و پرداخت', 'tadris' ), 'h3' ); ?>

	<form id="order_review" method="post" class="webmz-order-pay-form">
		<div class="woocommerce-checkout-review-order webmz-checkout-order-grid">
			<div class="webmz-checkout-totals-col">
				<table class="shop_table woocommerce-checkout-review-order-table webmz-checkout-totals-table">
					<tbody>
						<?php if ( count( $order->get_items() ) > 0 ) : ?>
							<?php foreach ( $order->get_items() as $item_id => $item ) : ?>
								<?php
								if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
									continue;
								}
								?>
								<tr class="webmz-checkout-product-row <?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
									<th class="product-name">
										<?php
										echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );
										echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', esc_html( $item->get_quantity() ) ) . '</strong>', $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

										do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

										wc_display_item_meta( $item );

										do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
										?>
									</th>
									<td class="product-total"><?php echo $order->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
					<?php if ( $totals ) : ?>
						<tfoot>
							<?php foreach ( $totals as $total_key => $total ) : ?>
								<tr class="<?php echo esc_attr( $total_key ); ?>">
									<th scope="row"><?php echo $total['label']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
									<td class="product-total"><?php echo $total['value']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
								</tr>
							<?php endforeach; ?>
						</tfoot>
					<?php endif; ?>
				</table>

				<div class="webmz-checkout-place-order">
					<input type="hidden" name="woocommerce_pay" value="1" />

					<?php wc_get_template( 'checkout/terms.php' ); ?>

					<?php do_action( 'woocommerce_pay_order_before_submit' ); ?>

					<?php
					echo apply_filters(
						'woocommerce_pay_order_button_html',
						'<button type="submit" class="button alt webmz-wc-btn-arrow' . esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ) . '" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>'
					); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>

					<?php do_action( 'woocommerce_pay_order_after_submit' ); ?>

					<?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
				</div>
			</div>

			<div class="webmz-checkout-payment-col">
				<?php do_action( 'woocommerce_pay_order_before_payment' ); ?>

				<div id="payment" class="woocommerce-checkout-payment">
					<?php if ( $order->needs_payment() ) : ?>
						<?php webmz_wc_section_heading( esc_html__( 'روش پرداخت', 'tadris' ), 'h3' ); ?>
						<ul class="wc_payment_methods payment_methods methods">
							<?php
							if ( ! empty( $available_gateways ) ) {
								foreach ( $available_gateways as $gateway ) {
									wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
								}
							} else {
								echo '<li>';
								wc_print_notice( apply_filters( 'woocommerce_no_available_payment_methods_message', esc_html__( 'متأسفانه روش پرداختی برای موقعیت شما در دسترس نیست. در صورت نیاز با ما تماس بگیرید.', 'tadris' ) ), 'notice' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
								echo '</li>';
							}
							?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</form>
</div>
