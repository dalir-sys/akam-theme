<?php
/**
 * Zhaket top developer widget — shared helpers.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get authors who have at least one published product.
 *
 * @return array<int|string,string>
 */
function webmz_zhaket_get_author_options() {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	$options = array(
		'' => esc_html__( '— انتخاب نویسنده —', 'tadris' ),
	);

	if ( ! post_type_exists( 'product' ) ) {
		$cache = $options;
		return $cache;
	}

	global $wpdb;

	$author_ids = $wpdb->get_col(
		"SELECT DISTINCT post_author
		FROM {$wpdb->posts}
		WHERE post_type = 'product'
		AND post_status = 'publish'
		ORDER BY post_author ASC"
	);

	foreach ( $author_ids as $author_id ) {
		$author_id = absint( $author_id );

		if ( ! $author_id ) {
			continue;
		}

		$user = get_userdata( $author_id );

		if ( ! $user ) {
			continue;
		}

		$options[ $author_id ] = $user->display_name;
	}

	$cache = $options;

	return $cache;
}

/**
 * Aggregate WooCommerce stats for a product author.
 *
 * @param int $author_id Author user ID.
 * @return array<string,int|float>
 */
function webmz_zhaket_top_developer_get_wp_stats( $author_id ) {
	$author_id = absint( $author_id );

	$stats = array(
		'products_count' => 0,
		'total_sales'    => 0,
		'total_revenue'  => 0,
		'avg_rating'     => 0,
		'reviews_count'  => 0,
	);

	if ( ! $author_id || ! class_exists( 'WooCommerce' ) ) {
		return $stats;
	}

	$product_ids = get_posts(
		array(
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'author'                 => $author_id,
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( empty( $product_ids ) ) {
		return $stats;
	}

	$stats['products_count'] = count( $product_ids );

	$rating_sum   = 0;
	$rating_count = 0;

	foreach ( $product_ids as $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			continue;
		}

		$sales = (int) $product->get_total_sales();
		$stats['total_sales']   += $sales;
		$stats['reviews_count'] += (int) $product->get_review_count();
		$stats['total_revenue'] += (float) $product->get_price() * $sales;

		$rating = (float) $product->get_average_rating();

		if ( $rating > 0 ) {
			$rating_sum   += $rating;
			$rating_count++;
		}
	}

	if ( $rating_count > 0 ) {
		$stats['avg_rating'] = round( $rating_sum / $rating_count, 1 );
	}

	return $stats;
}

/**
 * Resolve a stat display value from widget item settings.
 *
 * @param array<string,mixed> $item      Repeater item.
 * @param array<string,mixed> $wp_stats  Aggregated WP stats.
 * @return string
 */
function webmz_zhaket_top_developer_resolve_stat_value( $item, $wp_stats ) {
	$source = isset( $item['value_source'] ) ? sanitize_key( (string) $item['value_source'] ) : 'wp';
	$raw    = '';

	if ( 'manual' === $source ) {
		$raw = isset( $item['manual_value'] ) ? (string) $item['manual_value'] : '';
	} else {
		$key = isset( $item['wp_key'] ) ? sanitize_key( (string) $item['wp_key'] ) : 'total_sales';

		if ( isset( $wp_stats[ $key ] ) ) {
			$raw = (string) $wp_stats[ $key ];
		}
	}

	if ( '' === trim( $raw ) ) {
		return '';
	}

	$format = isset( $item['number_format'] ) ? sanitize_key( (string) $item['number_format'] ) : 'plain';

	if ( 'number' === $format && is_numeric( $raw ) ) {
		$decimals = isset( $item['decimals'] ) ? absint( $item['decimals'] ) : 0;

		if ( function_exists( 'webmz_zhaket_product_loop_format_number' ) ) {
			$raw = webmz_zhaket_product_loop_format_number( (float) $raw, $decimals );
		} else {
			$raw = number_format_i18n( (float) $raw, $decimals );
		}
	}

	$prefix = isset( $item['value_prefix'] ) ? (string) $item['value_prefix'] : '';
	$suffix = isset( $item['value_suffix'] ) ? (string) $item['value_suffix'] : '';

	return $prefix . $raw . $suffix;
}

/**
 * Query product IDs for an author.
 *
 * @param int                 $author_id Author ID.
 * @param array<string,mixed> $args      Query args.
 * @return array<int>
 */
function webmz_zhaket_top_developer_get_product_ids( $author_id, $args = array() ) {
	$author_id = absint( $author_id );

	if ( ! $author_id || ! post_type_exists( 'product' ) ) {
		return array();
	}

	$defaults = array(
		'count'    => 8,
		'order_by' => 'date',
		'order'    => 'DESC',
	);

	$args = wp_parse_args( $args, $defaults );

	$query_args = array(
		'post_type'              => 'product',
		'post_status'            => 'publish',
		'author'                 => $author_id,
		'posts_per_page'         => min( 20, max( 1, absint( $args['count'] ) ) ),
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	);

	$order_by = sanitize_key( (string) $args['order_by'] );

	switch ( $order_by ) {
		case 'popularity':
			$query_args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$query_args['orderby']  = 'meta_value_num';
			$query_args['order']    = 'DESC';
			break;

		case 'rating':
			$query_args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$query_args['orderby']  = 'meta_value_num';
			$query_args['order']    = 'DESC';
			break;

		case 'title':
			$query_args['orderby'] = 'title';
			$query_args['order']   = 'ASC';
			break;

		case 'rand':
			$query_args['orderby'] = 'rand';
			break;

		default:
			$query_args['orderby'] = 'date';
			$query_args['order']   = 'DESC' === strtoupper( (string) $args['order'] ) ? 'DESC' : 'ASC';
			break;
	}

	return get_posts( $query_args );
}
