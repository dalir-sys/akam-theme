<?php
/**
 * Store archive widget AJAX helpers and endpoint.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get the default active filter tab.
 *
 * @param array<string,mixed> $context Archive context.
 * @return string product_cat|product_tag
 */
function webmz_store_archive_get_initial_tab( $context ) {
	return ( isset( $context['type'] ) && 'tag' === $context['type'] ) ? 'product_tag' : 'product_cat';
}

/**
 * Get archive context metadata for the store widget.
 *
 * @return array<string,mixed>
 */
function webmz_store_archive_get_context() {
	$context = array(
		'type'           => 'shop',
		'taxonomy'       => 'product_cat',
		'term_id'        => 0,
		'search'         => '',
		'archive_title'  => '',
		'description'    => '',
	);

	if ( ! class_exists( 'WooCommerce' ) ) {
		return $context;
	}

	if ( is_search() ) {
		$context['type']   = 'search';
		$context['search'] = get_search_query();
		/* translators: %s: search query. */
		$context['archive_title'] = sprintf( __( 'نتایج جستجو برای: %s', 'tadris' ), $context['search'] );
	} elseif ( function_exists( 'is_product_category' ) && is_product_category() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$context['type']          = 'category';
			$context['taxonomy']      = 'product_cat';
			$context['term_id']       = (int) $term->term_id;
			$context['archive_title'] = single_term_title( '', false );
			$context['description']   = term_description( $term->term_id, 'product_cat' );
		}
	} elseif ( function_exists( 'is_product_tag' ) && is_product_tag() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$context['type']          = 'tag';
			$context['taxonomy']      = 'product_tag';
			$context['term_id']       = (int) $term->term_id;
			$context['archive_title'] = single_term_title( '', false );
			$context['description']   = term_description( $term->term_id, 'product_tag' );
		}
	} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
		$shop_page_id = absint( wc_get_page_id( 'shop' ) );
		$context['type']          = 'shop';
		$context['archive_title'] = $shop_page_id ? get_the_title( $shop_page_id ) : esc_html__( 'فروشگاه', 'tadris' );
		$context['description']   = $shop_page_id ? get_post_field( 'post_content', $shop_page_id ) : '';
	} elseif ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$context['type']          = 'archive';
			$context['taxonomy']      = $term->taxonomy;
			$context['term_id']       = (int) $term->term_id;
			$context['archive_title'] = single_term_title( '', false );
			$context['description']   = term_description( $term->term_id, $term->taxonomy );
		}
	}

	if ( '' === $context['description'] && ! is_search() ) {
		$context['description'] = get_the_archive_description();
	}

	return $context;
}

/**
 * Product visibility tax query for catalog listings.
 *
 * @return array<int,array<string,mixed>>
 */
function webmz_store_archive_get_visibility_tax_query() {
	$tax_query = array();
	$term_ids  = function_exists( 'wc_get_product_visibility_term_ids' )
		? wc_get_product_visibility_term_ids()
		: array();

	if ( isset( $term_ids['exclude-from-catalog'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'term_taxonomy_id',
			'terms'    => array( $term_ids['exclude-from-catalog'] ),
			'operator' => 'NOT IN',
		);
	}

	return $tax_query;
}

/**
 * Get min and max catalog prices for the range slider.
 *
 * @return array{min:int,max:int}
 */
function webmz_store_archive_get_price_bounds() {
	$bounds = array(
		'min' => 0,
		'max' => 10000000,
	);

	if ( ! class_exists( 'WooCommerce' ) ) {
		return $bounds;
	}

	global $wpdb;

	$row = $wpdb->get_row(
		"SELECT MIN( CAST( meta_value AS DECIMAL(12,2) ) ) AS min_price, MAX( CAST( meta_value AS DECIMAL(12,2) ) ) AS max_price
		FROM {$wpdb->postmeta}
		WHERE meta_key = '_price'
		AND meta_value != ''",
		ARRAY_A
	);

	if ( ! empty( $row['min_price'] ) ) {
		$bounds['min'] = max( 0, (int) floor( (float) $row['min_price'] ) );
	}

	if ( ! empty( $row['max_price'] ) ) {
		$bounds['max'] = max( $bounds['min'], (int) ceil( (float) $row['max_price'] ) );
	}

	return $bounds;
}

/**
 * Sanitize catalog orderby key.
 *
 * @param string $orderby Raw orderby.
 * @return string
 */
function webmz_store_archive_sanitize_orderby( $orderby ) {
	$allowed = array( 'menu_order', 'date', 'popularity', 'rating', 'price', 'price-desc' );
	$orderby = sanitize_key( (string) $orderby );

	return in_array( $orderby, $allowed, true ) ? $orderby : 'menu_order';
}

/**
 * Build WP_Query args for store archive widget.
 *
 * @param array<string,mixed> $args Request args.
 * @return array<string,mixed>
 */
function webmz_store_archive_build_query_args( $args ) {
	$posts_per_page = isset( $args['posts_per_page'] ) ? max( 1, absint( $args['posts_per_page'] ) ) : 6;
	$page           = isset( $args['page'] ) ? max( 1, absint( $args['page'] ) ) : 1;
	$search         = isset( $args['search'] ) ? sanitize_text_field( (string) $args['search'] ) : '';
	$taxonomy       = isset( $args['taxonomy'] ) ? sanitize_key( (string) $args['taxonomy'] ) : 'product_cat';
	$term_id        = isset( $args['term_id'] ) ? absint( $args['term_id'] ) : 0;
	$min_price      = isset( $args['min_price'] ) ? max( 0, (float) $args['min_price'] ) : 0;
	$max_price      = isset( $args['max_price'] ) ? max( 0, (float) $args['max_price'] ) : 0;
	$orderby        = webmz_store_archive_sanitize_orderby( isset( $args['orderby'] ) ? $args['orderby'] : 'menu_order' );
	$bounds         = webmz_store_archive_get_price_bounds();

	if ( ! in_array( $taxonomy, array( 'product_cat', 'product_tag' ), true ) ) {
		$taxonomy = 'product_cat';
	}

	if ( $max_price <= 0 ) {
		$max_price = (float) $bounds['max'];
	}

	if ( $min_price > $max_price ) {
		$min_price = 0;
	}

	$query_args = array(
		'post_type'              => 'product',
		'post_status'            => 'publish',
		'posts_per_page'         => $posts_per_page,
		'paged'                  => $page,
		'ignore_sticky_posts'    => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
	);

	if ( '' !== $search ) {
		$query_args['s'] = $search;
	}

	$tax_query = webmz_store_archive_get_visibility_tax_query();

	if ( $term_id > 0 ) {
		$tax_query[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'term_id',
			'terms'    => array( $term_id ),
		);
	}

	if ( ! empty( $tax_query ) ) {
		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}
		$query_args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}

	$meta_query = array();

	if ( $min_price > 0 || $max_price < (float) $bounds['max'] ) {
		$meta_query[] = array(
			'key'     => '_price',
			'value'   => array( $min_price, $max_price ),
			'compare' => 'BETWEEN',
			'type'    => 'NUMERIC',
		);
	}

	switch ( $orderby ) {
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

		case 'date':
			$query_args['orderby'] = 'date';
			$query_args['order']   = 'DESC';
			break;

		case 'price':
			$query_args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$query_args['orderby']  = 'meta_value_num';
			$query_args['order']    = 'ASC';
			break;

		case 'price-desc':
			$query_args['meta_key'] = '_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$query_args['orderby']  = 'meta_value_num';
			$query_args['order']    = 'DESC';
			break;

		default:
			$query_args['orderby'] = 'menu_order title';
			$query_args['order']    = 'ASC';
			break;
	}

	if ( ! empty( $meta_query ) ) {
		$query_args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
	}

	return $query_args;
}

/**
 * Base URL for unfiltered shop archive view.
 *
 * @return string
 */
function webmz_store_archive_get_base_url() {
	if ( ! function_exists( 'wc_get_page_permalink' ) ) {
		return home_url( '/' );
	}

	$shop_url = wc_get_page_permalink( 'shop' );

	return $shop_url ? $shop_url : home_url( '/' );
}

/**
 * Format a price amount for filter labels.
 *
 * @param float $amount Price amount.
 * @return string
 */
function webmz_store_archive_format_price_amount( $amount ) {
	$formatted = number_format( (float) $amount, 0, '.', ',' );

	if ( function_exists( 'webmz_to_persian_digits' ) ) {
		return webmz_to_persian_digits( $formatted );
	}

	return $formatted;
}

/**
 * Render price range filter markup.
 *
 * @param array<string,mixed> $settings Widget settings.
 * @param int                 $min      Current min price.
 * @param int                 $max      Current max price.
 * @param bool                $is_editor Editor preview flag.
 * @return string
 */
function webmz_store_archive_render_price_filter_html( $settings, $min = 0, $max = 0, $is_editor = false ) {
	$bounds      = webmz_store_archive_get_price_bounds();
	$price_label = ! empty( $settings['price_filter_label'] ) ? $settings['price_filter_label'] : esc_html__( 'فیلتر قیمت', 'tadris' );
	$min_bound   = (int) $bounds['min'];
	$max_bound   = (int) $bounds['max'];
	$min_value   = $min > 0 ? $min : $min_bound;
	$max_value   = $max > 0 ? $max : $max_bound;

	if ( $min_value < $min_bound ) {
		$min_value = $min_bound;
	}

	if ( $max_value > $max_bound ) {
		$max_value = $max_bound;
	}

	if ( $min_value > $max_value ) {
		$min_value = $min_bound;
		$max_value = $max_bound;
	}

	$currency = html_entity_decode(
		get_woocommerce_currency_symbol(),
		ENT_QUOTES,
		get_bloginfo( 'charset' )
	);

	ob_start();
	?>
	<div class="webmz-store-archive__price-filter">
		<h3 class="webmz-store-archive__filter-title"><?php echo esc_html( $price_label ); ?></h3>
		<div
			class="webmz-store-archive__price-range"
			data-min-bound="<?php echo esc_attr( (string) $min_bound ); ?>"
			data-max-bound="<?php echo esc_attr( (string) $max_bound ); ?>"
		>
			<div class="webmz-store-archive__price-range-track" aria-hidden="true">
				<div class="webmz-store-archive__price-range-fill"></div>
			</div>
			<input
				type="range"
				class="webmz-store-archive__price-range-input webmz-store-archive__price-range-input--min"
				min="<?php echo esc_attr( (string) $min_bound ); ?>"
				max="<?php echo esc_attr( (string) $max_bound ); ?>"
				value="<?php echo esc_attr( (string) $min_value ); ?>"
				<?php echo $is_editor ? 'disabled' : ''; ?>
				aria-label="<?php esc_attr_e( 'حداقل قیمت', 'tadris' ); ?>"
			>
			<input
				type="range"
				class="webmz-store-archive__price-range-input webmz-store-archive__price-range-input--max"
				min="<?php echo esc_attr( (string) $min_bound ); ?>"
				max="<?php echo esc_attr( (string) $max_bound ); ?>"
				value="<?php echo esc_attr( (string) $max_value ); ?>"
				<?php echo $is_editor ? 'disabled' : ''; ?>
				aria-label="<?php esc_attr_e( 'حداکثر قیمت', 'tadris' ); ?>"
			>
		</div>
		<div class="webmz-store-archive__price-values">
			<span class="webmz-store-archive__price-value webmz-store-archive__price-value--min">
				<span class="webmz-store-archive__price-value-amount"><?php echo esc_html( webmz_store_archive_format_price_amount( $min_value ) ); ?></span>
				<span class="webmz-store-archive__price-value-currency"><?php echo esc_html( $currency ); ?></span>
			</span>
			<span class="webmz-store-archive__price-value webmz-store-archive__price-value--max">
				<span class="webmz-store-archive__price-value-amount"><?php echo esc_html( webmz_store_archive_format_price_amount( $max_value ) ); ?></span>
				<span class="webmz-store-archive__price-value-currency"><?php echo esc_html( $currency ); ?></span>
			</span>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render taxonomy filter list.
 *
 * @param string $taxonomy   Taxonomy slug.
 * @param int    $active_id  Active term ID.
 * @return string
 */
function webmz_store_archive_render_terms_html( $taxonomy, $active_id = 0 ) {
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '<p class="webmz-store-archive__terms-empty">' . esc_html__( 'موردی برای فیلتر وجود ندارد.', 'tadris' ) . '</p>';
	}

	ob_start();
	?>
	<button
		type="button"
		class="webmz-store-archive__term webmz-store-archive__term--all<?php echo 0 === $active_id ? ' is-active' : ''; ?>"
		data-term-id="0"
		data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"
	>
		<span class="webmz-store-archive__term-name"><?php esc_html_e( 'همه', 'tadris' ); ?></span>
	</button>
	<ul class="webmz-store-archive__terms" role="list" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>">
		<?php foreach ( $terms as $term ) : ?>
			<?php
			if ( ! $term instanceof WP_Term ) {
				continue;
			}
			$term_link = get_term_link( $term );
			$term_url  = is_wp_error( $term_link ) ? '' : $term_link;
			?>
			<li>
				<button type="button" class="webmz-store-archive__term<?php echo (int) $term->term_id === $active_id ? ' is-active' : ''; ?>" data-term-id="<?php echo esc_attr( (string) $term->term_id ); ?>" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"<?php echo $term_url ? ' data-term-url="' . esc_url( $term_url ) . '"' : ''; ?>>
					<span class="webmz-store-archive__term-count"><?php echo esc_html( function_exists( 'webmz_to_persian_digits' ) ? webmz_to_persian_digits( (string) $term->count ) : (string) $term->count ); ?></span>
					<span class="webmz-store-archive__term-name"><?php echo esc_html( $term->name ); ?></span>
				</button>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render category filter list for the sidebar.
 *
 * @param array<string,mixed> $settings Widget settings.
 * @param array<string,mixed> $context  Archive context.
 * @return string
 */
function webmz_store_archive_render_filter_sidebar_terms( $settings, $context ) {
	$active_term  = 'category' === $context['type'] ? (int) $context['term_id'] : 0;
	$category_lbl = ! empty( $settings['category_label'] ) ? $settings['category_label'] : esc_html__( 'دسته‌بندی محصولات', 'tadris' );

	ob_start();
	?>
	<h3 class="webmz-store-archive__filter-title"><?php echo esc_html( $category_lbl ); ?></h3>
	<?php echo webmz_store_archive_render_terms_html( 'product_cat', $active_term ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php
	return (string) ob_get_clean();
}

/**
 * Build card rendering args from widget settings.
 *
 * @param array<string,mixed> $settings Widget settings.
 * @return array<string,mixed>
 */
function webmz_store_archive_get_card_args( $settings ) {
	$defaults = webmz_get_product_loop_card_defaults();

	return array(
		'show_rating'             => false,
		'show_course_meta'        => false,
		'show_category'           => true,
		'show_short_description'  => true,
		'show_sale_badge'         => true,
		'card_modifier_class'     => 'tadris-product-type-1--store-archive',
		'title_tag'               => isset( $settings['card_title_tag'] ) ? webmz_sanitize_heading_tag( $settings['card_title_tag'] ) : $defaults['title_tag'],
		'button_text'             => ! empty( $settings['button_text'] ) ? $settings['button_text'] : $defaults['button_text'],
		'variable_button_text'    => ! empty( $settings['variable_button_text'] ) ? $settings['variable_button_text'] : $defaults['variable_button_text'],
		'unavailable_button_text' => ! empty( $settings['unavailable_button_text'] ) ? $settings['unavailable_button_text'] : $defaults['unavailable_button_text'],
	);
}

/**
 * Render products grid HTML.
 *
 * @param WP_Query            $query    Products query.
 * @param array<string,mixed> $settings Widget settings.
 * @return string
 */
function webmz_store_archive_render_products_html( $query, $settings = array() ) {
	if ( ! $query instanceof WP_Query || ! $query->have_posts() ) {
		return '<div class="webmz-store-archive__empty">' . esc_html__( 'محصولی یافت نشد.', 'tadris' ) . '</div>';
	}

	$card_args = webmz_store_archive_get_card_args( $settings );

	ob_start();

	while ( $query->have_posts() ) {
		$query->the_post();
		$product = wc_get_product( get_the_ID() );

		if ( ! $product ) {
			continue;
		}

		/**
		 * Hook: woocommerce_shop_loop.
		 */
		do_action( 'woocommerce_shop_loop' );

		if ( function_exists( 'webmz_woocommerce_render_with_loop_item_hooks' ) ) {
			webmz_woocommerce_render_with_loop_item_hooks(
				static function () use ( $product, $card_args ) {
					webmz_render_product_loop_card( $product, $card_args );
				}
			);
		} else {
			webmz_render_product_loop_card( $product, $card_args );
		}
	}

	wp_reset_postdata();

	return (string) ob_get_clean();
}

/**
 * Render numeric pagination markup.
 *
 * @param WP_Query $query Query object.
 * @param int      $page  Current page.
 * @return string
 */
function webmz_store_archive_render_pagination_html( $query, $page = 1 ) {
	$total = (int) $query->max_num_pages;
	$page  = max( 1, absint( $page ) );

	if ( $total <= 1 ) {
		return '';
	}

	$links = paginate_links(
		array(
			'base'      => '%_%',
			'format'    => '?page=%#%',
			'current'   => $page,
			'total'     => $total,
			'type'      => 'list',
			'prev_text' => esc_html__( 'قبلی', 'tadris' ),
			'next_text' => esc_html__( 'بعدی', 'tadris' ),
		)
	);

	if ( ! $links ) {
		return '';
	}

	return '<nav class="webmz-store-archive__pagination" aria-label="' . esc_attr__( 'صفحه‌بندی محصولات', 'tadris' ) . '">' . $links . '</nav>';
}

/**
 * AJAX: filter and paginate store archive products.
 *
 * @return void
 */
function webmz_store_archive_ajax_filter() {
	check_ajax_referer( 'webmz_store_archive', 'nonce' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'ووکامرس فعال نیست.', 'tadris' ) ) );
	}

	$posts_per_page = isset( $_POST['posts_per_page'] ) ? max( 1, absint( wp_unslash( $_POST['posts_per_page'] ) ) ) : 6;
	$page           = isset( $_POST['page'] ) ? max( 1, absint( wp_unslash( $_POST['page'] ) ) ) : 1;
	$search         = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
	$taxonomy       = isset( $_POST['taxonomy'] ) ? sanitize_key( wp_unslash( $_POST['taxonomy'] ) ) : 'product_cat';
	$term_id        = isset( $_POST['term_id'] ) ? absint( wp_unslash( $_POST['term_id'] ) ) : 0;
	$load_mode      = isset( $_POST['load_mode'] ) ? sanitize_key( wp_unslash( $_POST['load_mode'] ) ) : 'pagination';
	$min_price      = isset( $_POST['min_price'] ) ? max( 0, (float) wp_unslash( $_POST['min_price'] ) ) : 0;
	$max_price      = isset( $_POST['max_price'] ) ? max( 0, (float) wp_unslash( $_POST['max_price'] ) ) : 0;
	$orderby        = isset( $_POST['orderby'] ) ? sanitize_key( wp_unslash( $_POST['orderby'] ) ) : 'menu_order';

	$settings = array(
		'card_title_tag'          => isset( $_POST['card_title_tag'] ) ? webmz_sanitize_heading_tag( wp_unslash( $_POST['card_title_tag'] ) ) : 'h3',
		'button_text'             => isset( $_POST['button_text'] ) ? sanitize_text_field( wp_unslash( $_POST['button_text'] ) ) : '',
		'variable_button_text'    => isset( $_POST['variable_button_text'] ) ? sanitize_text_field( wp_unslash( $_POST['variable_button_text'] ) ) : '',
		'unavailable_button_text' => isset( $_POST['unavailable_button_text'] ) ? sanitize_text_field( wp_unslash( $_POST['unavailable_button_text'] ) ) : '',
	);

	$query_args = webmz_store_archive_build_query_args(
		array(
			'posts_per_page' => $posts_per_page,
			'page'           => $page,
			'search'         => $search,
			'taxonomy'       => $taxonomy,
			'term_id'        => $term_id,
			'min_price'      => $min_price,
			'max_price'      => $max_price,
			'orderby'        => $orderby,
		)
	);

	$query = new WP_Query( $query_args );

	wp_send_json_success(
		array(
			'html'        => webmz_store_archive_render_products_html( $query, $settings ),
			'pagination'  => 'pagination' === $load_mode ? webmz_store_archive_render_pagination_html( $query, $page ) : '',
			'currentPage' => $page,
			'maxPages'    => (int) $query->max_num_pages,
			'foundPosts'  => (int) $query->found_posts,
			'hasMore'     => $page < (int) $query->max_num_pages,
		)
	);
}
add_action( 'wp_ajax_webmz_store_archive_filter', 'webmz_store_archive_ajax_filter' );
add_action( 'wp_ajax_nopriv_webmz_store_archive_filter', 'webmz_store_archive_ajax_filter' );
