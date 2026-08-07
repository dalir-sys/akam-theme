<?php
/**
 * Public AJAX live-search endpoint.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Count UTF-8 characters safely.
 *
 * @param string $term Search term.
 * @return int
 */
function webmz_ajax_search_term_length( $term ) {
	return function_exists( 'mb_strlen' )
		? mb_strlen( $term, 'UTF-8' )
		: strlen( $term );
}

/**
 * Return an empty-state Swiper slide.
 *
 * @param string $message User-facing message.
 * @return string
 */
function webmz_ajax_search_empty_slide( $message ) {
	return '<div class="swiper-slide webmz-ajax-search__empty">' . esc_html( $message ) . '</div>';
}

/**
 * Format a product sales count for Zhaket-style search cards.
 *
 * @param int $sales Total sales count.
 * @return string
 */
function webmz_ajax_search_format_sales_count( $sales ) {
	$formatted = number_format_i18n( absint( $sales ) );

	if ( function_exists( 'webmz_to_persian_digits' ) ) {
		$formatted = webmz_to_persian_digits( $formatted );
	}

	/* translators: %s: formatted sales count. */
	return sprintf( esc_html__( '%s فروش', 'tadris' ), $formatted );
}

/**
 * Get product thumbnail markup for Zhaket-style search cards.
 *
 * Uses the loop-thumbnail metabox when available.
 *
 * @param int    $product_id Product ID.
 * @param string $size       Image size.
 * @return string
 */
function webmz_ajax_search_product_thumb_html( $product_id, $size = 'thumbnail' ) {
	$product_id = absint( $product_id );

	if ( function_exists( 'webmz_pll_get_product_thumb_url' ) ) {
		$thumb_url = webmz_pll_get_product_thumb_url( $product_id, $size );

		if ( $thumb_url ) {
			return sprintf(
				'<img src="%1$s" alt="" loading="lazy" decoding="async">',
				esc_url( $thumb_url )
			);
		}
	}

	if ( has_post_thumbnail( $product_id ) ) {
		return webmz_get_post_loop_thumbnail( $product_id );
	}

	if ( function_exists( 'wc_placeholder_img' ) ) {
		return wp_kses_post( wc_placeholder_img( $size ) );
	}

	return '';
}

/**
 * Render bestseller products for the Zhaket-style search popup idle state.
 *
 * @param int $limit Maximum number of products.
 * @return string
 */
function webmz_ajax_search_bestsellers_html( $limit = 3 ) {
	if ( ! class_exists( 'WooCommerce' ) || ! post_type_exists( 'product' ) ) {
		return '<p class="webmz-zhaket-search__empty">' . esc_html__( 'برای نمایش محصولات، ووکامرس باید فعال باشد.', 'tadris' ) . '</p>';
	}

	$limit     = min( 6, max( 1, absint( $limit ) ) );
	$tax_query = array();
	$visibility_terms = function_exists( 'wc_get_product_visibility_term_ids' )
		? wc_get_product_visibility_term_ids()
		: array();

	if ( isset( $visibility_terms['exclude-from-catalog'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'term_taxonomy_id',
			'terms'    => array( $visibility_terms['exclude-from-catalog'] ),
			'operator' => 'NOT IN',
		);
	}

	$args = array(
		'post_type'              => 'product',
		'post_status'            => 'publish',
		'posts_per_page'         => $limit,
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'meta_key'               => 'total_sales', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'orderby'                => 'meta_value_num',
		'order'                  => 'DESC',
		'update_post_meta_cache' => true,
		'update_post_term_cache' => false,
	);

	if ( ! empty( $tax_query ) ) {
		$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return '<p class="webmz-zhaket-search__empty">' . esc_html__( 'محصول پرفروشی یافت نشد.', 'tadris' ) . '</p>';
	}

	ob_start();

	echo '<div class="webmz-zhaket-search__products">';

	while ( $query->have_posts() ) {
		$query->the_post();

		$product = wc_get_product( get_the_ID() );

		if ( ! $product ) {
			continue;
		}
		?>
		<a class="webmz-zhaket-search__product" href="<?php the_permalink(); ?>">
			<span class="webmz-zhaket-search__product-thumb">
				<?php echo webmz_ajax_search_product_thumb_html( get_the_ID(), 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
			<span class="webmz-zhaket-search__product-meta">
				<span class="webmz-zhaket-search__product-title"><?php the_title(); ?></span>
				<span class="webmz-zhaket-search__product-sales"><?php echo esc_html( webmz_ajax_search_format_sales_count( $product->get_total_sales() ) ); ?></span>
			</span>
		</a>
		<?php
	}

	echo '</div>';

	wp_reset_postdata();

	return (string) ob_get_clean();
}

/**
 * Render matching published products as Swiper slides.
 *
 * @param string $term Search term.
 * @param int    $limit Maximum result count.
 * @return string
 */
function webmz_ajax_search_products_html( $term, $limit ) {
	if ( ! class_exists( 'WooCommerce' ) || ! post_type_exists( 'product' ) ) {
		return webmz_ajax_search_empty_slide(
			esc_html__( 'برای نمایش محصولات، ووکامرس باید فعال باشد.', 'tadris' )
		);
	}

	$tax_query        = array();
	$visibility_terms = function_exists( 'wc_get_product_visibility_term_ids' )
		? wc_get_product_visibility_term_ids()
		: array();

	if ( isset( $visibility_terms['exclude-from-search'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'term_taxonomy_id',
			'terms'    => array( $visibility_terms['exclude-from-search'] ),
			'operator' => 'NOT IN',
		);
	}

	$args = array(
		'post_type'              => 'product',
		'post_status'            => 'publish',
		's'                      => $term,
		'posts_per_page'         => $limit,
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => false,
	);

	if ( ! empty( $tax_query ) ) {
		$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return webmz_ajax_search_empty_slide(
			esc_html__( 'محصول مرتبطی پیدا نشد.', 'tadris' )
		);
	}

	ob_start();

	while ( $query->have_posts() ) {
		$query->the_post();

		$product = wc_get_product( get_the_ID() );

		if ( ! $product ) {
			continue;
		}
		?>
		<div class="swiper-slide">
			<a class="webmz-search-product" href="<?php the_permalink(); ?>">
				<span class="webmz-search-product__image">
					<?php
					if ( has_post_thumbnail() ) {
						echo webmz_get_post_loop_thumbnail(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} elseif ( function_exists( 'wc_placeholder_img' ) ) {
						echo wp_kses_post( wc_placeholder_img( 'woocommerce_thumbnail' ) );
					}
					?>
				</span>

				<span class="webmz-search-product__title"><?php the_title(); ?></span>

				<span class="webmz-search-product__price">
					<?php echo wp_kses_post( $product->get_price_html() ); ?>
				</span>
			</a>
		</div>
		<?php
	}

	wp_reset_postdata();

	return (string) ob_get_clean();
}

/**
 * Render matching published blog posts as Swiper slides.
 *
 * @param string $term Search term.
 * @param int    $limit Maximum result count.
 * @return string
 */
function webmz_ajax_search_posts_html( $term, $limit ) {
	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			's'                      => $term,
			'posts_per_page'         => $limit,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'has_password'           => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( ! $query->have_posts() ) {
		return webmz_ajax_search_empty_slide(
			esc_html__( 'مقاله مرتبطی پیدا نشد.', 'tadris' )
		);
	}

	ob_start();

	while ( $query->have_posts() ) {
		$query->the_post();
		?>
		<div class="swiper-slide">
			<a class="webmz-search-post" href="<?php the_permalink(); ?>">
				<span class="webmz-search-post__image">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php echo webmz_get_post_loop_thumbnail(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php else : ?>
						<span class="webmz-search-post__placeholder" aria-hidden="true">✎</span>
					<?php endif; ?>
				</span>

				<span class="webmz-search-post__content">
					<span class="webmz-search-post__title"><?php the_title(); ?></span>
					<span class="webmz-search-post__date"><?php echo esc_html( get_the_date() ); ?></span>
				</span>
			</a>
		</div>
		<?php
	}

	wp_reset_postdata();

	return (string) ob_get_clean();
}

/**
 * Render a mobile list empty state item.
 *
 * @param string $message User-facing message.
 * @return string
 */
function webmz_ajax_search_mobile_empty_item( $message ) {
	return '<li class="webmz-msearch__empty">' . esc_html( $message ) . '</li>';
}

/**
 * Render matching products as a compact mobile list.
 *
 * @param string $term  Search term.
 * @param int    $limit Maximum result count.
 * @return string
 */
function webmz_ajax_search_products_mobile_html( $term, $limit ) {
	if ( ! class_exists( 'WooCommerce' ) || ! post_type_exists( 'product' ) ) {
		return webmz_ajax_search_mobile_empty_item(
			esc_html__( 'برای نمایش محصولات، ووکامرس باید فعال باشد.', 'tadris' )
		);
	}

	$tax_query        = array();
	$visibility_terms = function_exists( 'wc_get_product_visibility_term_ids' )
		? wc_get_product_visibility_term_ids()
		: array();

	if ( isset( $visibility_terms['exclude-from-search'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'term_taxonomy_id',
			'terms'    => array( $visibility_terms['exclude-from-search'] ),
			'operator' => 'NOT IN',
		);
	}

	$args = array(
		'post_type'              => 'product',
		'post_status'            => 'publish',
		's'                      => $term,
		'posts_per_page'         => $limit,
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => false,
	);

	if ( ! empty( $tax_query ) ) {
		$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return webmz_ajax_search_mobile_empty_item(
			esc_html__( 'محصول مرتبطی پیدا نشد.', 'tadris' )
		);
	}

	ob_start();

	while ( $query->have_posts() ) {
		$query->the_post();
		?>
		<li>
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</li>
		<?php
	}

	wp_reset_postdata();

	return (string) ob_get_clean();
}

/**
 * Render matching posts as a compact mobile list.
 *
 * @param string $term  Search term.
 * @param int    $limit Maximum result count.
 * @return string
 */
function webmz_ajax_search_posts_mobile_html( $term, $limit ) {
	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			's'                      => $term,
			'posts_per_page'         => $limit,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'has_password'           => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( ! $query->have_posts() ) {
		return webmz_ajax_search_mobile_empty_item(
			esc_html__( 'مقاله مرتبطی پیدا نشد.', 'tadris' )
		);
	}

	ob_start();

	while ( $query->have_posts() ) {
		$query->the_post();
		?>
		<li>
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</li>
		<?php
	}

	wp_reset_postdata();

	return (string) ob_get_clean();
}

/**
 * Respond to a public Ajax live-search request.
 *
 * @return void
 */
function webmz_ajax_live_search() {
	check_ajax_referer( 'webmz_ajax_search', 'nonce' );

	$term = isset( $_POST['term'] )
		? sanitize_text_field( wp_unslash( $_POST['term'] ) )
		: '';

	$term = trim( $term );

	if ( webmz_ajax_search_term_length( $term ) < 3 ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'حداقل سه حرف برای جستجو وارد کنید.', 'tadris' ),
			),
			422
		);
	}

	$product_limit = isset( $_POST['products_limit'] ) ? absint( $_POST['products_limit'] ) : 8;
	$post_limit    = isset( $_POST['posts_limit'] ) ? absint( $_POST['posts_limit'] ) : 8;

	$product_limit = min( 12, max( 1, $product_limit ) );
	$post_limit    = min( 12, max( 1, $post_limit ) );
	$is_mobile     = ! empty( $_POST['mobile'] );

	wp_send_json_success(
		array(
			'term'          => $term,
			'products_html' => $is_mobile
				? webmz_ajax_search_products_mobile_html( $term, $product_limit )
				: webmz_ajax_search_products_html( $term, $product_limit ),
			'posts_html'    => $is_mobile
				? webmz_ajax_search_posts_mobile_html( $term, $post_limit )
				: webmz_ajax_search_posts_html( $term, $post_limit ),
		)
	);
}
add_action( 'wp_ajax_webmz_live_search', 'webmz_ajax_live_search' );
add_action( 'wp_ajax_nopriv_webmz_live_search', 'webmz_ajax_live_search' );
