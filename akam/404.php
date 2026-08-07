<?php
/** 404 template. @package WebMZ */
defined( 'ABSPATH' ) || exit;
get_header();
if ( ! webmz_render_location( '404' ) ) :
	get_template_part( 'template-parts/404/default' );
endif;
get_footer();
