<?php
/**
 * Gutenberg block: manual related content box.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the related post block and editor assets.
 *
 * @return void
 */
function webmz_register_related_post_block() {
	wp_register_script(
		'webmz-block-related-post',
		WEBMZ_URI . 'assets/js/blocks/related-post.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
		WEBMZ_VERSION,
		true
	);

	register_block_type(
		'webmz/related-post',
		array(
			'api_version'     => 3,
			'title'           => esc_html__( 'مطالب مشابه', 'tadris' ),
			'description'     => esc_html__( 'باکس مطلب مرتبط را به‌صورت دستی در محتوا درج کنید.', 'tadris' ),
			'category'        => 'webmz-blocks',
			'icon'            => 'admin-links',
			'keywords'        => array( 'related', 'tadris', 'مطالب', 'مشابه', 'CTA' ),
			'editor_script'   => 'webmz-block-related-post',
			'render_callback' => 'webmz_render_related_post_block',
			'attributes'      => array(
				'titleBefore'    => array(
					'type'    => 'string',
					'default' => 'در مورد ',
				),
				'titleHighlight' => array(
					'type'    => 'string',
					'default' => '',
				),
				'titleAfter'     => array(
					'type'    => 'string',
					'default' => ' بیشتر بدانید',
				),
				'description'    => array(
					'type'    => 'string',
					'default' => '',
				),
				'buttonText'     => array(
					'type'    => 'string',
					'default' => 'مشاهده مقاله',
				),
				'buttonUrl'      => array(
					'type'    => 'string',
					'default' => '',
				),
				'linkTarget'     => array(
					'type'    => 'string',
					'default' => '',
				),
			),
			'supports'        => array(
				'html'  => false,
				'align' => array( 'wide', 'full' ),
			),
		)
	);
}
add_action( 'init', 'webmz_register_related_post_block' );

/**
 * Add WebMZ block category in the block inserter.
 *
 * @param array<int,array<string,mixed>> $categories Block categories.
 * @return array<int,array<string,mixed>>
 */
function webmz_register_block_category( $categories ) {
	$categories[] = array(
		'slug'  => 'webmz-blocks',
		'title' => esc_html__( 'آکام', 'tadris' ),
		'icon'  => null,
	);

	return $categories;
}
add_filter( 'block_categories_all', 'webmz_register_block_category' );

/**
 * Render the related post Gutenberg block on the front end.
 *
 * @param array<string,mixed> $attributes Block attributes.
 * @return string
 */
function webmz_render_related_post_block( $attributes ) {
	return webmz_render_related_content_box( is_array( $attributes ) ? $attributes : array() );
}

/**
 * Output related-box CSS variables inside the block editor.
 *
 * @return void
 */
function webmz_enqueue_related_post_block_editor_styles() {
	$css = function_exists( 'webmz_get_related_box_css_vars_declaration' )
		? webmz_get_related_box_css_vars_declaration()
		: '';

	if ( '' === $css ) {
		return;
	}

	wp_add_inline_style( 'wp-edit-blocks', ':root{' . $css . '}' );
}
add_action( 'enqueue_block_editor_assets', 'webmz_enqueue_related_post_block_editor_styles', 20 );
