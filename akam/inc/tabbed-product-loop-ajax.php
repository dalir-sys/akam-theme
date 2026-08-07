<?php
/**
 * AJAX handlers for tabbed product loop widget.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sanitize tabbed product loop config received from the frontend.
 *
 * @param array<string,mixed> $config Raw config.
 * @return array<string,mixed>
 */
function webmz_tabbed_product_loop_sanitize_config( $config ) {
	if ( ! is_array( $config ) ) {
		return array();
	}

	$tabs = array();

	if ( ! empty( $config['tabs'] ) && is_array( $config['tabs'] ) ) {
		foreach ( array_values( $config['tabs'] ) as $tab ) {
			if ( ! is_array( $tab ) ) {
				continue;
			}

			$filter_type = isset( $tab['filter_type'] ) ? sanitize_key( $tab['filter_type'] ) : 'all';

			if ( ! in_array( $filter_type, array( 'all', 'category', 'tag' ), true ) ) {
				$filter_type = 'all';
			}

			$tabs[] = array(
				'filter_type' => $filter_type,
				'category'    => isset( $tab['category'] ) ? sanitize_title( (string) $tab['category'] ) : '',
				'tag'         => isset( $tab['tag'] ) ? sanitize_title( (string) $tab['tag'] ) : '',
				'label'       => isset( $tab['label'] ) ? sanitize_text_field( (string) $tab['label'] ) : '',
			);
		}
	}

	$card_args = array();

	if ( ! empty( $config['cardArgs'] ) && is_array( $config['cardArgs'] ) ) {
		foreach ( $config['cardArgs'] as $key => $value ) {
			if ( is_scalar( $value ) || null === $value ) {
				$card_args[ sanitize_key( (string) $key ) ] = is_string( $value ) ? wp_kses_post( $value ) : $value;
			}
		}
	}

	return array(
		'loopStyle'            => webmz_sanitize_product_loop_style( $config['loopStyle'] ?? '1' ),
		'count'                => min( 24, max( 1, absint( $config['count'] ?? 4 ) ) ),
		'order_by'             => in_array( $config['order_by'] ?? 'date', array( 'date', 'popularity', 'rating', 'rand' ), true ) ? sanitize_key( $config['order_by'] ) : 'date',
		'displayType'          => isset( $config['displayType'] ) && 'slider' === $config['displayType'] ? 'slider' : 'grid',
		'gridGap'              => absint( $config['gridGap'] ?? 20 ),
		'gridColumnsDesktop'   => sanitize_text_field( (string) ( $config['gridColumnsDesktop'] ?? '4' ) ),
		'gridColumnsTablet'    => sanitize_text_field( (string) ( $config['gridColumnsTablet'] ?? '2' ) ),
		'gridColumnsMobile'    => sanitize_text_field( (string) ( $config['gridColumnsMobile'] ?? '1' ) ),
		'slidesDesktop'        => absint( $config['slidesDesktop'] ?? 4 ),
		'slidesTablet'         => absint( $config['slidesTablet'] ?? 2 ),
		'slidesMobile'         => absint( $config['slidesMobile'] ?? 1 ),
		'sliderLoop'           => ! empty( $config['sliderLoop'] ),
		'sliderAutoplay'       => ! empty( $config['sliderAutoplay'] ),
		'sliderAutoplayDelay'  => absint( $config['sliderAutoplayDelay'] ?? 3500 ),
		'sliderNavigation'     => ! empty( $config['sliderNavigation'] ),
		'sliderPagination'     => ! isset( $config['sliderPagination'] ) || ! empty( $config['sliderPagination'] ),
		'tabs'                 => $tabs,
		'cardArgs'             => $card_args,
	);
}

/**
 * Load tabbed product loop panel markup via AJAX.
 *
 * @return void
 */
function webmz_tabbed_product_loop_ajax_load() {
	check_ajax_referer( 'webmz_tabbed_product_loop', 'nonce' );

	if ( ! function_exists( 'wc_get_product' ) || ! function_exists( 'webmz_tabbed_product_loop_render_panel_html' ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'ووکامرس فعال نیست.', 'tadris' ),
			)
		);
	}

	$tab_index = isset( $_POST['tab_index'] ) ? absint( $_POST['tab_index'] ) : 0;
	$config    = array();

	if ( isset( $_POST['config'] ) ) {
		$raw_config = wp_unslash( $_POST['config'] );
		if ( is_string( $raw_config ) ) {
			$decoded = json_decode( $raw_config, true );
			$config  = is_array( $decoded ) ? $decoded : array();
		} elseif ( is_array( $raw_config ) ) {
			$config = $raw_config;
		}
	}

	$config = webmz_tabbed_product_loop_sanitize_config( $config );

	if ( empty( $config['tabs'] ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'تب‌ای برای نمایش تعریف نشده است.', 'tadris' ),
			)
		);
	}

	$html = webmz_tabbed_product_loop_render_panel_html( $config, $tab_index );

	if ( '' === $html ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'محتوایی برای نمایش وجود ندارد.', 'tadris' ),
			)
		);
	}

	wp_send_json_success(
		array(
			'html' => $html,
		)
	);
}
add_action( 'wp_ajax_webmz_tabbed_product_loop_load', 'webmz_tabbed_product_loop_ajax_load' );
add_action( 'wp_ajax_nopriv_webmz_tabbed_product_loop_load', 'webmz_tabbed_product_loop_ajax_load' );
