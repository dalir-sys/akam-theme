<?php
/** Default not found display. @package WebMZ */
defined( 'ABSPATH' ) || exit;
?>
<main id="primary" class="site-main webmz-container webmz-404">
	<p class="webmz-404__code">404</p>
	<h1><?php esc_html_e( 'صفحه موردنظر پیدا نشد', 'tadris' ); ?></h1>
	<p><?php esc_html_e( 'آدرس واردشده معتبر نیست یا این صفحه حذف شده است.', 'tadris' ); ?></p>
	<a class="webmz-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'بازگشت به صفحه اصلی', 'tadris' ); ?></a>
</main>
