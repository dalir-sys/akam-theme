<?php
/**
 * Visual effects: theme-wide hover effects and the Three.js background assets.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hover effects level from theme options.
 *
 * @return string off|subtle|vivid
 */
function webmz_get_hover_effects_level() {
	$options = function_exists( 'webmz_get_options' ) ? webmz_get_options() : array();
	$level   = isset( $options['hover_effects'] ) ? (string) $options['hover_effects'] : 'subtle';

	return in_array( $level, array( 'off', 'subtle', 'vivid' ), true ) ? $level : 'subtle';
}

/**
 * Add the hover effects body classes.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function webmz_hover_effects_body_class( $classes ) {
	$level = webmz_get_hover_effects_level();

	if ( 'off' !== $level ) {
		$classes[] = 'webmz-fx';
		$classes[] = 'webmz-fx--' . $level;
	}

	return $classes;
}
add_filter( 'body_class', 'webmz_hover_effects_body_class' );

/**
 * Enqueue hover effects and register the 3D background assets.
 *
 * @return void
 */
function webmz_visual_effects_assets() {
	if ( 'off' !== webmz_get_hover_effects_level() ) {
		wp_enqueue_style( 'webmz-hover-effects', WEBMZ_URI . 'assets/css/hover-effects.css', array(), WEBMZ_VERSION );
		wp_enqueue_script( 'webmz-hover-effects', WEBMZ_URI . 'assets/js/hover-effects.js', array(), WEBMZ_VERSION, true );
	}

	// Loaded only on pages that contain the "پس‌زمینه سه‌بعدی" widget (via its dependencies).
	wp_register_style( 'webmz-three-background', WEBMZ_URI . 'assets/css/three-background.css', array(), WEBMZ_VERSION );
	wp_register_script( 'webmz-three-background', WEBMZ_URI . 'assets/js/three-background.min.js', array(), WEBMZ_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'webmz_visual_effects_assets', 20 );
