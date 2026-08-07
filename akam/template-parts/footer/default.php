<?php
/** Default footer content. @package WebMZ */
defined( 'ABSPATH' ) || exit;
?>
<footer id="colophon" class="site-footer">
	<div class="webmz-container site-footer__grid">
		<div class="site-footer__brand">
			<strong><?php bloginfo( 'name' ); ?></strong>
			<p><?php esc_html_e( 'قالب پایه، سریع و قابل توسعه برای وردپرس، المنتور و ووکامرس.', 'tadris' ); ?></p>
		</div>
		<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'منوی فوتر', 'tadris' ); ?>">
			<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'footer-menu', 'fallback_cb' => false ) ); ?>
		</nav>
	</div>
	<div class="webmz-container site-footer__bottom">
		<p class="site-info"><?php echo esc_html( sprintf( __( '© %1$s %2$s. تمامی حقوق محفوظ است.', 'tadris' ), gmdate( 'Y' ), get_bloginfo( 'name' ) ) ); ?></p>
	</div>
</footer>
