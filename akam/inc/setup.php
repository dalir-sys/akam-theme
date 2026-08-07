<?php
/**
 * Theme setup and common WordPress support.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports and navigation areas.
 *
 * @return void
 */
function webmz_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 300,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/main.css' );

	register_nav_menus( array(
		'primary' => esc_html__( 'منوی اصلی', 'webmz' ),
		'footer'  => esc_html__( 'منوی فوتر', 'webmz' ),
	) );
}
add_action( 'after_setup_theme', 'webmz_setup' );

/**
 * Define content width used by WordPress embeds and images.
 *
 * @return void
 */
function webmz_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'webmz_content_width', 1240 );
}
add_action( 'after_setup_theme', 'webmz_content_width', 0 );

/**
 * Register a simple widget area for fallback templates.
 *
 * @return void
 */
function webmz_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'سایدبار اصلی', 'webmz' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'ویجت‌های صفحات پیش‌فرض قالب.', 'webmz' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'webmz_widgets_init' );
