<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

$current_user     = wp_get_current_user();
$current_endpoint = function_exists( 'webmz_account_get_current_endpoint' ) ? webmz_account_get_current_endpoint() : 'dashboard';
$display_name     = $current_user && $current_user->exists() ? ( $current_user->display_name ? $current_user->display_name : $current_user->user_login ) : '';
?>

<div class="webmz-account-panel" data-webmz-account-panel data-current-endpoint="<?php echo esc_attr( $current_endpoint ); ?>" dir="rtl">
	<aside class="webmz-account-sidebar">
		<?php if ( function_exists( 'webmz_account_render_profile_card' ) ) : ?>
			<?php webmz_account_render_profile_card(); ?>
		<?php endif; ?>

		<?php
		/**
		 * My Account navigation.
		 *
		 * @since 2.6.0
		 */
		if ( function_exists( 'webmz_account_render_navigation' ) ) {
			webmz_account_render_navigation();
		} else {
			do_action( 'woocommerce_account_navigation' );
		}
		?>
	</aside>

	<section class="webmz-account-main">
		<header class="webmz-account-hero">
			<div>
				<span class="webmz-account-hero__label"><?php esc_html_e( 'پنل کاربری', 'tadris' ); ?></span>
				<h1><?php esc_html_e( 'حساب کاربری من', 'tadris' ); ?></h1>
				<?php if ( $display_name ) : ?>
					<p><?php echo esc_html( sprintf( __( 'سلام %s، خوش آمدید.', 'tadris' ), $display_name ) ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( function_exists( 'webmz_account_render_stats' ) ) : ?>
				<?php webmz_account_render_stats(); ?>
			<?php endif; ?>
		</header>

		<div class="webmz-account-content-wrap">
			<div class="webmz-account-loading" aria-hidden="true">
				<span></span>
				<?php esc_html_e( 'در حال بارگذاری...', 'tadris' ); ?>
			</div>

			<div class="woocommerce-MyAccount-content webmz-account-content" data-webmz-account-content>
				<?php
				/**
				 * My Account content.
				 *
				 * @since 2.6.0
				 */
				do_action( 'woocommerce_account_content' );
				?>
			</div>
		</div>
	</section>
</div>
