<?php
/**
 * Cart Page
 *
 * @package WebMZ
 * @version 10.8.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );
?>

<div class="webmz-cart-layout">
	<div class="webmz-cart-main">
		<?php webmz_wc_section_heading( esc_html__( 'سبد خرید', 'tadris' ) ); ?>

		<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<?php do_action( 'woocommerce_before_cart_table' ); ?>

			<div class="webmz-cart-table-wrap">
				<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents webmz-cart-table" cellspacing="0">
					<thead>
						<tr>
							<th scope="col" class="product-row"><?php esc_html_e( 'ردیف', 'tadris' ); ?></th>
							<th class="product-thumbnail"><span class="screen-reader-text"><?php esc_html_e( 'تصویر محصول', 'tadris' ); ?></span></th>
							<th scope="col" class="product-name"><?php esc_html_e( 'محصول', 'tadris' ); ?></th>
							<th scope="col" class="product-price"><?php esc_html_e( 'قیمت', 'tadris' ); ?></th>
							<th scope="col" class="product-quantity"><?php esc_html_e( 'تعداد', 'tadris' ); ?></th>
							<th scope="col" class="product-subtotal"><?php esc_html_e( 'جمع جزء', 'tadris' ); ?></th>
							<th scope="col" class="product-remove"><span class="screen-reader-text"><?php esc_html_e( 'عملیات', 'tadris' ); ?></span><?php esc_html_e( 'عملیات‌ها', 'tadris' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php do_action( 'woocommerce_before_cart_contents' ); ?>

						<?php
						$row_number = 0;
						foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
							$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
							$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
							$visible    = apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key );

							if ( ! ( $_product instanceof WC_Product ) || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! $visible ) {
								continue;
							}

							++$row_number;
							$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
							$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							?>
							<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
								<td class="product-row" data-title="<?php esc_attr_e( 'ردیف', 'tadris' ); ?>">
									<span class="webmz-cart-row-num"><?php echo esc_html( (string) $row_number ); ?></span>
								</td>

								<td class="product-thumbnail">
									<?php
									$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', webmz_get_product_loop_image( $_product ), $cart_item, $cart_item_key );

									if ( ! $product_permalink ) {
										echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									} else {
										printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
									?>
								</td>

								<td scope="row" role="rowheader" class="product-name" data-title="<?php esc_attr_e( 'محصول', 'tadris' ); ?>">
									<?php
									if ( ! $product_permalink ) {
										echo wp_kses_post( $product_name . '&nbsp;' );
									} else {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
									}

									do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
									echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

									if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
									}
									?>
								</td>

								<td class="product-price" data-title="<?php esc_attr_e( 'قیمت', 'tadris' ); ?>">
									<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</td>

								<td class="product-quantity" data-title="<?php esc_attr_e( 'تعداد', 'tadris' ); ?>">
									<?php
									if ( $_product->is_sold_individually() ) {
										$min_quantity = 1;
										$max_quantity = 1;
									} else {
										$min_quantity = 0;
										$max_quantity = $_product->get_max_purchase_quantity();
									}

									$product_quantity = woocommerce_quantity_input(
										array(
											'input_name'   => "cart[{$cart_item_key}][qty]",
											'input_value'  => $cart_item['quantity'],
											'max_value'    => $max_quantity,
											'min_value'    => $min_quantity,
											'product_name' => $product_name,
										),
										$_product,
										false
									);

									echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									?>
								</td>

								<td class="product-subtotal" data-title="<?php esc_attr_e( 'جمع جزء', 'tadris' ); ?>">
									<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</td>

								<td class="product-remove" data-title="<?php esc_attr_e( 'عملیات‌ها', 'tadris' ); ?>">
									<?php
									echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										'woocommerce_cart_item_remove_link',
										sprintf(
											'<a role="button" href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
											esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
											esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
											esc_attr( $product_id ),
											esc_attr( $_product->get_sku() )
										),
										$cart_item_key
									);
									?>
								</td>
							</tr>
							<?php
						}
						?>

						<?php do_action( 'woocommerce_cart_contents' ); ?>
					</tbody>
				</table>
			</div>

			<?php if ( wc_coupons_enabled() ) : ?>
				<div class="webmz-cart-coupon-wrap">
					<div class="webmz-cart-coupon coupon">
						<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'کد تخفیف', 'tadris' ); ?></label>
						<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'کد تخفیف', 'tadris' ); ?>" />
						<button type="submit" class="button webmz-wc-btn-arrow<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'اعمال تخفیف', 'tadris' ); ?>">
							<?php esc_html_e( 'اعمال تخفیف', 'tadris' ); ?>
						</button>
						<?php do_action( 'woocommerce_cart_coupon' ); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="webmz-cart-form-actions">
				<button type="submit" class="button webmz-cart-update<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="update_cart" value="<?php esc_attr_e( 'به‌روزرسانی سبد', 'tadris' ); ?>">
					<?php esc_html_e( 'به‌روزرسانی سبد', 'tadris' ); ?>
				</button>
				<?php do_action( 'woocommerce_cart_actions' ); ?>
				<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
			</div>

			<?php do_action( 'woocommerce_after_cart_table' ); ?>
		</form>
	</div>

	<div class="webmz-cart-sidebar">
		<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
		<div class="cart-collaterals">
			<?php do_action( 'woocommerce_cart_collaterals' ); ?>
		</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
