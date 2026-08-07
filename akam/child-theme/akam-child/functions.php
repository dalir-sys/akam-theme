<?php
/**
 * Akam child theme bootstrap.
 *
 * @package WebMZ_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load child theme translations.
 *
 * @return void
 */
function webmz_child_setup() {
	load_child_theme_textdomain( 'akam-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'webmz_child_setup' );
