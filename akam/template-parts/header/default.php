<?php
/**
 * Modern default site header shown when no Elementor header has been assigned.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

$account_url = wp_login_url();
$cart_url    = '';
if ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_permalink' ) ) {
	$account_url = wc_get_page_permalink( 'myaccount' );
	$cart_url    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '';
}
?>
<div class="webmz-promo-bar">
	<div class="webmz-container webmz-promo-bar__inner">
		<span><?php esc_html_e( 'آکام؛ هسته ساده برای طراحی سایت حرفه ای با المنتور', 'tadris' ); ?></span>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'مشاهده سایت', 'tadris' ); ?> <span aria-hidden="true">←</span></a>
	</div>
</div>
<header id="masthead" class="site-header">
	<div class="webmz-container site-header__main">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="site-brandmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<span class="site-brandmark__icon">W</span>
					<span>
						<strong class="site-title"><?php bloginfo( 'name' ); ?></strong>
						<?php if ( get_bloginfo( 'description' ) ) : ?><small class="site-description"><?php bloginfo( 'description' ); ?></small><?php endif; ?>
					</span>
				</a>
			<?php endif; ?>
		</div>
		<div class="webmz-header-search"><?php get_search_form(); ?></div>
		<div class="webmz-header-actions">
			<?php if ( $cart_url ) : ?>
				<a class="webmz-icon-action" href="<?php echo esc_url( $cart_url ); ?>" aria-label="<?php esc_attr_e( 'سبد خرید', 'tadris' ); ?>">🛒</a>
			<?php endif; ?>
			<a class="webmz-account-action" href="<?php echo esc_url( $account_url ); ?>"><?php esc_html_e( 'حساب کاربری', 'tadris' ); ?></a>
		</div>
	</div>
	<div class="webmz-nav-shell">
		<nav id="site-navigation" class="main-navigation webmz-container" aria-label="<?php esc_attr_e( 'منوی اصلی', 'tadris' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_id'        => 'primary-menu',
					'container'      => false,
					'fallback_cb'    => 'wp_page_menu',
				)
			);
			?>
		</nav>
	</div>
</header>
