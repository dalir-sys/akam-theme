<?php
/** Empty-result fallback. @package WebMZ */
defined( 'ABSPATH' ) || exit;
?>
<section class="no-results not-found webmz-empty-state">
	<h2><?php esc_html_e( 'موردی پیدا نشد', 'tadris' ); ?></h2>
	<p><?php esc_html_e( 'محتوایی برای نمایش وجود ندارد. می‌توانید عبارت دیگری را جستجو کنید.', 'tadris' ); ?></p>
	<?php get_search_form(); ?>
</section>
