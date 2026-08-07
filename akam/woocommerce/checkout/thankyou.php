<?php
/**
 * Thankyou page
 *
 * @package WebMZ
 * @version 8.1.0
 *
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="woocommerce-order webmz-order-received">

	<?php if ( $order ) : ?>

		<?php do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<div class="webmz-checkout-order-section webmz-order-received-failed">
				<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">
					<?php esc_html_e( 'متأسفانه سفارش شما به‌دلیل رد تراکنش توسط بانک یا درگاه پرداخت قابل پردازش نیست. لطفاً دوباره تلاش کنید.', 'tadris' ); ?>
				</p>

				<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'پرداخت مجدد', 'tadris' ); ?></a>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'حساب کاربری', 'tadris' ); ?></a>
					<?php endif; ?>
				</p>
			</div>

		<?php else : ?>

			<?php
			$webmz_received_text = apply_filters(
				'woocommerce_thankyou_order_received_text',
				esc_html__( 'سپاسگزاریم. جزئیات سفارش برای شما ارسال شد و به‌زودی پردازش می‌شود.', 'tadris' ),
				$order
			);
			?>

			<div class="webmz-order-received-hero">
				<div class="webmz-order-received-hero__icon" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
				</div>
				<div class="webmz-order-received-hero__body">
					<p class="webmz-order-received-hero__badge"><?php echo esc_html( sprintf( __( 'سفارش #%s', 'tadris' ), $order->get_order_number() ) ); ?></p>
					<h2 class="webmz-order-received-hero__title"><?php esc_html_e( 'سفارش شما با موفقیت ثبت شد', 'tadris' ); ?></h2>
					<p class="webmz-order-received-hero__text woocommerce-thankyou-order-received"><?php echo wp_kses_post( $webmz_received_text ); ?></p>
				</div>
			</div>

			<div class="webmz-checkout-order-section webmz-order-received-meta">
				<div class="webmz-order-meta">
					<div class="webmz-order-meta__item">
						<span class="webmz-order-meta__label"><?php esc_html_e( 'تاریخ', 'tadris' ); ?></span>
						<span class="webmz-order-meta__value"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></span>
					</div>

					<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
						<div class="webmz-order-meta__item">
							<span class="webmz-order-meta__label"><?php esc_html_e( 'ایمیل', 'tadris' ); ?></span>
							<span class="webmz-order-meta__value"><?php echo esc_html( $order->get_billing_email() ); ?></span>
						</div>
					<?php endif; ?>

					<?php if ( $order->get_payment_method_title() ) : ?>
						<div class="webmz-order-meta__item">
							<span class="webmz-order-meta__label"><?php esc_html_e( 'روش پرداخت', 'tadris' ); ?></span>
							<span class="webmz-order-meta__value"><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></span>
						</div>
					<?php endif; ?>

					<div class="webmz-order-meta__item webmz-order-meta__item--total">
						<span class="webmz-order-meta__label"><?php esc_html_e( 'مبلغ پرداختی', 'tadris' ); ?></span>
						<span class="webmz-order-meta__value"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
					</div>
				</div>

				<div class="webmz-order-received-actions">
					<?php if ( is_user_logged_in() ) : ?>
						<a class="button webmz-order-btn webmz-order-btn--orders" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'مشاهده سفارش‌ها', 'tadris' ); ?></a>
					<?php endif; ?>
					<a class="button alt webmz-order-btn webmz-order-btn--shop" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'ادامه خرید', 'tadris' ); ?></a>
				</div>
			</div>

		<?php endif; ?>

		<div class="webmz-order-received-details">
			<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
			<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
		</div>

	<?php else : ?>

		<div class="webmz-order-received-hero webmz-order-received-hero--simple">
			<?php wc_get_template( 'checkout/order-received.php', array( 'order' => false ) ); ?>
		</div>

	<?php endif; ?>

</div>
