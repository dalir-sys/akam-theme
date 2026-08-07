<?php
/** Search form. @package WebMZ */
defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label><span class="screen-reader-text"><?php esc_html_e( 'جستجو برای:', 'tadris' ); ?></span><input type="search" class="search-field" placeholder="<?php esc_attr_e( 'جستجو...', 'tadris' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s"></label>
</form>
