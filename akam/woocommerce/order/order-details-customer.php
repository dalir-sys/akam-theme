<?php
/**
 * Order Customer Details
 *
 * @package WebMZ
 * @version 8.7.0
 */

defined( 'ABSPATH' ) || exit;

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<section class="woocommerce-customer-details webmz-checkout-order-section webmz-order-customer-details">

	<?php webmz_wc_section_heading( esc_html__( 'اطلاعات شما', 'tadris' ), 'h3' ); ?>

	<div class="webmz-order-details-inner">
		<table class="webmz-order-info-table">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'آدرس صورتحساب', 'tadris' ); ?></th>
					<td class="webmz-order-info-table__address-cell"><?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'نامشخص', 'tadris' ) ) ); ?></td>
				</tr>

				<?php if ( $order->get_billing_phone() ) : ?>
					<tr>
						<th scope="row"><?php esc_html_e( 'تلفن', 'tadris' ); ?></th>
						<td class="webmz-order-info-table__ltr"><?php echo esc_html( $order->get_billing_phone() ); ?></td>
					</tr>
				<?php endif; ?>

				<?php if ( $order->get_billing_email() ) : ?>
					<tr>
						<th scope="row"><?php esc_html_e( 'ایمیل', 'tadris' ); ?></th>
						<td class="webmz-order-info-table__ltr"><?php echo esc_html( $order->get_billing_email() ); ?></td>
					</tr>
				<?php endif; ?>

				<?php do_action( 'woocommerce_order_details_after_customer_address', 'billing', $order ); ?>
			</tbody>
		</table>

		<?php if ( $show_shipping ) : ?>
			<table class="webmz-order-info-table webmz-order-info-table--shipping">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'آدرس ارسال', 'tadris' ); ?></th>
						<td class="webmz-order-info-table__address-cell"><?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'نامشخص', 'tadris' ) ) ); ?></td>
					</tr>

					<?php if ( $order->get_shipping_phone() ) : ?>
						<tr>
							<th scope="row"><?php esc_html_e( 'تلفن ارسال', 'tadris' ); ?></th>
							<td class="webmz-order-info-table__ltr"><?php echo esc_html( $order->get_shipping_phone() ); ?></td>
						</tr>
					<?php endif; ?>

					<?php do_action( 'woocommerce_order_details_after_customer_address', 'shipping', $order ); ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

</section>
