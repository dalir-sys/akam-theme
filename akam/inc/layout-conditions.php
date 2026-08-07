<?php
/**
 * Display conditions for WebMZ Elementor layouts.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

define( 'WEBMZ_LAYOUT_CONDITIONS_META', '_webmz_layout_conditions' );
define( 'WEBMZ_LAYOUT_CONDITION_META', '_webmz_layout_condition' );

/**
 * Get condition types used by the display rules.
 *
 * @return array<string,array<string,mixed>>
 */
function webmz_get_layout_condition_definitions() {
	$definitions = array(
		'general'  => array(
			'label'    => esc_html__( 'کل سایت / Entire Website', 'tadris' ),
			'global'   => true,
			'priority' => 1,
		),
		'category' => array(
			'label'    => esc_html__( 'دسته‌بندی', 'tadris' ),
			'taxonomy' => 'category',
			'priority' => 100,
		),
		'post_tag' => array(
			'label'    => esc_html__( 'برچسب', 'tadris' ),
			'taxonomy' => 'post_tag',
			'priority' => 100,
		),
		'page'     => array(
			'label'     => esc_html__( 'برگه خاص', 'tadris' ),
			'post_type' => 'page',
			'priority'  => 100,
		),
	);

	if ( class_exists( 'WooCommerce' ) ) {
		$definitions['product_cat'] = array(
			'label'    => esc_html__( 'دسته محصولات', 'tadris' ),
			'taxonomy' => 'product_cat',
			'priority' => 100,
		);
		$definitions['product_tag'] = array(
			'label'    => esc_html__( 'برچسب محصولات', 'tadris' ),
			'taxonomy' => 'product_tag',
			'priority' => 100,
		);
	}

	return $definitions;
}

/**
 * Sanitize one condition rule row.
 *
 * @param mixed $raw Raw rule.
 * @return array<string,mixed>|null
 */
function webmz_sanitize_layout_condition_rule( $raw ) {
	if ( ! is_array( $raw ) ) {
		return null;
	}

	$definitions = webmz_get_layout_condition_definitions();
	$name        = isset( $raw['name'] ) ? sanitize_key( $raw['name'] ) : '';

	if ( ! isset( $definitions[ $name ] ) ) {
		return null;
	}

	if ( ! empty( $definitions[ $name ]['global'] ) ) {
		return array(
			'type'       => 'include',
			'name'       => 'general',
			'object_ids' => array(),
		);
	}

	$object_ids = array();

	if ( isset( $raw['object_ids'] ) && is_array( $raw['object_ids'] ) ) {
		foreach ( $raw['object_ids'] as $object_id ) {
			$object_id = absint( $object_id );

			if ( $object_id && webmz_layout_object_is_valid( $name, $object_id ) ) {
				$object_ids[] = $object_id;
			}
		}
	}

	$object_ids = array_values( array_unique( $object_ids ) );

	if ( empty( $object_ids ) ) {
		return null;
	}

	return array(
		'type'       => 'include',
		'name'       => $name,
		'object_ids' => $object_ids,
	);
}

/**
 * Sanitize all rules.
 *
 * @param mixed $raw Raw rules.
 * @return array<int,array<string,mixed>>
 */
function webmz_sanitize_layout_condition_rules( $raw ) {
	if ( ! is_array( $raw ) ) {
		return array();
	}

	$clean = array();

	foreach ( $raw as $rule ) {
		$sanitized = webmz_sanitize_layout_condition_rule( $rule );

		if ( $sanitized ) {
			$clean[] = $sanitized;
		}
	}

	return array_values( $clean );
}

/**
 * Validate one object ID for a condition type.
 *
 * @param string $name      Condition name.
 * @param int    $object_id Object ID.
 * @return bool
 */
function webmz_layout_object_is_valid( $name, $object_id ) {
	$object_id   = absint( $object_id );
	$definitions = webmz_get_layout_condition_definitions();

	if ( ! $object_id || ! isset( $definitions[ $name ] ) ) {
		return false;
	}

	if ( 'page' === $name ) {
		$post = get_post( $object_id );

		return $post && 'page' === $post->post_type && 'publish' === $post->post_status;
	}

	if ( ! empty( $definitions[ $name ]['taxonomy'] ) ) {
		$term = get_term( $object_id, $definitions[ $name ]['taxonomy'] );

		return $term && ! is_wp_error( $term );
	}

	return false;
}

/**
 * Read stored rules for a layout (with legacy migration).
 *
 * @param int $layout_id Layout post ID.
 * @return array<int,array<string,mixed>>
 */
function webmz_get_layout_condition_rules( $layout_id ) {
	$layout_id = absint( $layout_id );
	$stored    = get_post_meta( $layout_id, WEBMZ_LAYOUT_CONDITIONS_META, true );

	if ( is_array( $stored ) && ! empty( $stored ) && isset( $stored[0]['type'] ) ) {
		return webmz_sanitize_layout_condition_rules( $stored );
	}

	$legacy = get_post_meta( $layout_id, WEBMZ_LAYOUT_CONDITION_META, true );

	if ( is_array( $legacy ) && ! empty( $legacy['name'] ) && 'general' === $legacy['name'] ) {
		return webmz_sanitize_layout_condition_rules(
			array(
				array(
					'type'       => 'include',
					'name'       => 'general',
					'object_ids' => array(),
				),
			)
		);
	}

	if ( is_array( $legacy ) && ! empty( $legacy['name'] ) && ! empty( $legacy['object_id'] ) ) {
		return webmz_sanitize_layout_condition_rules(
			array(
				array(
					'type'       => 'include',
					'name'       => $legacy['name'],
					'object_ids' => array( (int) $legacy['object_id'] ),
				),
			)
		);
	}

	return array();
}

/**
 * Backward-compatible alias.
 *
 * @param int $layout_id Layout post ID.
 * @return array<int,array<string,mixed>>
 */
function webmz_get_layout_conditions( $layout_id ) {
	return webmz_get_layout_condition_rules( $layout_id );
}

/**
 * Get label for one stored object.
 *
 * @param string $name      Condition name.
 * @param int    $object_id Object ID.
 * @return string
 */
function webmz_layout_object_label( $name, $object_id ) {
	$object_id = absint( $object_id );

	if ( ! $object_id ) {
		return '';
	}

	if ( 'page' === $name ) {
		$title = get_the_title( $object_id );
		return $title ? $title : '#' . $object_id;
	}

	$definitions = webmz_get_layout_condition_definitions();

	if ( empty( $definitions[ $name ]['taxonomy'] ) ) {
		return '#' . $object_id;
	}

	$term = get_term( $object_id, $definitions[ $name ]['taxonomy'] );

	return ( $term && ! is_wp_error( $term ) ) ? $term->name : '#' . $object_id;
}

/**
 * Build admin list summary.
 *
 * @param mixed $rules Rules or layout ID.
 * @return string
 */
function webmz_format_layout_conditions_summary( $rules ) {
	if ( is_numeric( $rules ) ) {
		$rules = webmz_get_layout_condition_rules( (int) $rules );
	} elseif ( isset( $rules['name'] ) ) {
		$rules = webmz_get_layout_condition_rules( 0 );
	}

	$rules = is_array( $rules ) ? $rules : array();

	if ( empty( $rules ) ) {
		return esc_html__( 'فقط از تنظیمات پوسته', 'tadris' );
	}

	$definitions = webmz_get_layout_condition_definitions();
	$parts       = array();

	foreach ( $rules as $rule ) {
		$type_label = isset( $definitions[ $rule['name'] ]['label'] ) ? $definitions[ $rule['name'] ]['label'] : $rule['name'];

		if ( 'general' === $rule['name'] ) {
			$parts[] = esc_html__( 'نمایش در', 'tadris' ) . ' ' . $type_label;
			continue;
		}

		$labels     = array();

		foreach ( $rule['object_ids'] as $object_id ) {
			$labels[] = webmz_layout_object_label( $rule['name'], $object_id );
		}

		$parts[] = esc_html__( 'نمایش در', 'tadris' ) . ' ' . $type_label . ': ' . implode( '، ', $labels );
	}

	return implode( ' | ', $parts );
}

/**
 * Check if one object matches the current request.
 *
 * @param string $name      Condition name.
 * @param int    $object_id Object ID.
 * @return bool
 */
function webmz_layout_object_matches_request( $name, $object_id ) {
	$object_id = absint( $object_id );

	if ( 'general' === $name ) {
		return true;
	}

	if ( ! $object_id ) {
		return false;
	}

	switch ( $name ) {
		case 'page':
			return is_page( $object_id );

		case 'category':
			if ( is_category( $object_id ) ) {
				return true;
			}
			return is_singular( 'post' ) && has_category( $object_id );

		case 'post_tag':
			if ( is_tag( $object_id ) ) {
				return true;
			}
			return is_singular( 'post' ) && has_tag( $object_id );

		case 'product_cat':
			if ( function_exists( 'is_product_category' ) && is_product_category( $object_id ) ) {
				return true;
			}
			return function_exists( 'is_product' ) && is_product() && has_term( $object_id, 'product_cat' );

		case 'product_tag':
			if ( function_exists( 'is_product_tag' ) && is_product_tag( $object_id ) ) {
				return true;
			}
			return function_exists( 'is_product' ) && is_product() && has_term( $object_id, 'product_tag' );
	}

	return false;
}

/**
 * Check if one rule matches the current request.
 *
 * @param array<string,mixed> $rule Stored rule.
 * @return bool
 */
function webmz_layout_rule_matches_request( $rule ) {
	if ( isset( $rule['name'] ) && 'general' === $rule['name'] ) {
		return true;
	}

	foreach ( (array) $rule['object_ids'] as $object_id ) {
		if ( webmz_layout_object_matches_request( $rule['name'], $object_id ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Evaluate include rules for a layout.
 *
 * @param array<int,array<string,mixed>> $rules Stored rules.
 * @return bool
 */
function webmz_layout_rules_pass( $rules ) {
	$includes = webmz_sanitize_layout_condition_rules( $rules );

	if ( empty( $includes ) ) {
		return false;
	}

	foreach ( $includes as $rule ) {
		if ( webmz_layout_rule_matches_request( $rule ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Score a layout for the current request.
 *
 * @param int $layout_id Layout post ID.
 * @return int
 */
function webmz_layout_condition_match_score( $layout_id ) {
	$layout_id = absint( $layout_id );
	$rules     = webmz_get_layout_condition_rules( $layout_id );

	if ( ! webmz_layout_rules_pass( $rules ) ) {
		return 0;
	}

	$score = 0;
	$definitions = webmz_get_layout_condition_definitions();

	foreach ( $rules as $rule ) {
		$priority = isset( $definitions[ $rule['name'] ]['priority'] ) ? (int) $definitions[ $rule['name'] ]['priority'] : 100;

		if ( 'general' === $rule['name'] && webmz_layout_rule_matches_request( $rule ) ) {
			$score = max( $score, $priority );
			continue;
		}

		foreach ( (array) $rule['object_ids'] as $object_id ) {
			if ( webmz_layout_object_matches_request( $rule['name'], $object_id ) ) {
				$score = max( $score, $priority );
			}
		}
	}

	return $score;
}

/**
 * Find the best matching published layout for a location.
 *
 * @param string $location Layout location/type key.
 * @return int
 */
function webmz_find_conditional_layout_id( $location ) {
	$location = sanitize_key( $location );
	$types    = webmz_get_layout_types();

	if ( ! isset( $types[ $location ] ) ) {
		return 0;
	}

	$posts = get_posts(
		array(
			'post_type'      => 'webmz_layout',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'   => '_webmz_layout_type',
					'value' => $location,
				),
			),
		)
	);

	$best_id    = 0;
	$best_score = 0;

	foreach ( $posts as $layout ) {
		$score = webmz_layout_condition_match_score( $layout->ID );

		if ( $score > $best_score ) {
			$best_score = $score;
			$best_id    = $layout->ID;
		}
	}

	return webmz_validate_layout_id( $best_id );
}

/**
 * AJAX: search objects for condition selection.
 *
 * @return void
 */
function webmz_ajax_search_layout_condition_objects() {
	check_ajax_referer( 'webmz_layout_conditions', 'nonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'دسترسی غیرمجاز.', 'tadris' ) ), 403 );
	}

	$query       = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
	$name        = isset( $_GET['name'] ) ? sanitize_key( wp_unslash( $_GET['name'] ) ) : '';
	$definitions = webmz_get_layout_condition_definitions();
	$results     = array();

	if ( ! isset( $definitions[ $name ] ) ) {
		wp_send_json_success( array( 'results' => $results ) );
	}

	if ( 'page' === $name ) {
		$posts = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				's'              => $query,
				'posts_per_page' => 30,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		foreach ( $posts as $post ) {
			$results[] = array(
				'id'    => $post->ID,
				'label' => $post->post_title ? $post->post_title : sprintf( esc_html__( 'بدون عنوان #%d', 'tadris' ), $post->ID ),
			);
		}
	} elseif ( ! empty( $definitions[ $name ]['taxonomy'] ) ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $definitions[ $name ]['taxonomy'],
				'hide_empty' => false,
				'number'     => 30,
				'search'     => $query,
			)
		);

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$results[] = array(
					'id'    => $term->term_id,
					'label' => $term->name,
				);
			}
		}
	}

	wp_send_json_success( array( 'results' => $results ) );
}
add_action( 'wp_ajax_webmz_search_layout_condition_objects', 'webmz_ajax_search_layout_condition_objects' );
