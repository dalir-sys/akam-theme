<?php
/**
 * Site document head and header location.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'رفتن به محتوا', 'tadris' ); ?></a>
	<?php if ( ! webmz_render_location( 'header' ) ) : ?>
		<?php get_template_part( 'template-parts/header/default' ); ?>
	<?php endif; ?>
