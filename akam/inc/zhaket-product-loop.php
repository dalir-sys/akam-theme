<?php
/**
 * Zhaket product loop — shared card renderer and query helpers.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build WP_Query args for zhaket product loop widgets.
 *
 * @param array<string,mixed> $settings Widget settings.
 * @return array<string,mixed>
 */
function webmz_zhaket_product_loop_query_args( $settings ) {
	$args = array(
		'post_type'              => 'product',
		'post_status'            => 'publish',
		'posts_per_page'         => min( 24, max( 1, absint( $settings['count'] ?? 8 ) ) ),
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'update_post_meta_cache' => true,
	);

	$tax_query = array();

	if ( function_exists( 'wc_get_product_visibility_term_ids' ) ) {
		$term_ids = wc_get_product_visibility_term_ids();

		if ( isset( $term_ids['exclude-from-catalog'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => array( $term_ids['exclude-from-catalog'] ),
				'operator' => 'NOT IN',
			);
		}
	}

	$filter_by = isset( $settings['filter_by'] ) ? $settings['filter_by'] : 'category';

	if ( 'tag' === $filter_by && ! empty( $settings['tag'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_tag',
			'field'    => 'slug',
			'terms'    => sanitize_title( (string) $settings['tag'] ),
		);
	} elseif ( ! empty( $settings['category'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => sanitize_title( (string) $settings['category'] ),
		);
	}

	if ( ! empty( $tax_query ) ) {
		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}
		$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}

	switch ( $settings['order_by'] ?? 'date' ) {
		case 'popularity':
			$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'DESC';
			break;

		case 'rating':
			$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'DESC';
			break;

		case 'rand':
			$args['orderby'] = 'rand';
			break;

		default:
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
			break;
	}

	return $args;
}

/**
 * Format discount percent for zhaket loop badge.
 *
 * @param int $percent Discount percent.
 * @return string
 */
function webmz_zhaket_product_loop_format_discount( $percent ) {
	$badge = absint( $percent ) . '%';

	if ( function_exists( 'webmz_to_persian_digits' ) ) {
		$badge = webmz_to_persian_digits( $badge );
	}

	return str_replace( '%', '٪', $badge );
}

/**
 * Format a number for zhaket loop display.
 *
 * @param float|int|string $number Number.
 * @param int              $decimals Decimal places.
 * @return string
 */
function webmz_zhaket_product_loop_format_number( $number, $decimals = 0 ) {
	if ( is_string( $number ) ) {
		$number = str_replace( ',', '', $number );
	}

	$formatted = number_format_i18n( (float) $number, $decimals );

	return function_exists( 'webmz_to_persian_digits' )
		? webmz_to_persian_digits( $formatted )
		: $formatted;
}

/**
 * Default card argument values.
 *
 * @return array<string,mixed>
 */
function webmz_zhaket_product_loop_card_defaults() {
	return array(
		'add_to_cart_text'     => esc_html__( 'افزودن به سبد خرید', 'tadris' ),
		'preview_text'         => esc_html__( 'پیشنمایش', 'tadris' ),
		'unavailable_text'     => esc_html__( 'ناموجود', 'tadris' ),
		'variable_text'        => esc_html__( 'انتخاب گزینه‌ها', 'tadris' ),
		'free_text'            => esc_html__( 'رایگان', 'tadris' ),
		'show_sales'           => true,
		'show_rating'          => true,
		'show_discount_badge'  => true,
		'show_price'           => true,
		'show_preview_button'  => true,
		'show_cart_button'     => true,
		'hide_preview_no_url'  => false,
		'title_tag'            => 'h3',
		'image_size'           => 'woocommerce_single',
	);
}

/**
 * Render one zhaket marketplace product card.
 *
 * @param \WC_Product          $product Product.
 * @param array<string,mixed> $args    Card args.
 * @param bool                 $as_slide Whether card is inside swiper slide.
 * @return void
 */
function webmz_zhaket_product_loop_render_card( $product, $args = array(), $as_slide = false ) {
	if ( ! $product || ! $product->is_visible() ) {
		return;
	}

	$args = wp_parse_args( is_array( $args ) ? $args : array(), webmz_zhaket_product_loop_card_defaults() );

	$product_id  = $product->get_id();
	$title       = $product->get_name();
	$title_tag   = tag_escape( $args['title_tag'] );
	$product_url = $product->get_permalink();
	$image_size  = sanitize_key( (string) $args['image_size'] );
	$image_id    = $product->get_image_id();
	$image_html  = '';

	if ( $image_id ) {
		$image_html = webmz_get_attachment_loop_image(
			$image_id,
			array(
				'class'    => 'webmz-zpl__media-img',
				'alt'      => $title,
			)
		);
	} elseif ( function_exists( 'wc_placeholder_img' ) ) {
		$image_html = wc_placeholder_img(
			$image_size ? $image_size : 'woocommerce_single',
			array( 'class' => 'webmz-zpl__media-img' )
		);
	}

	$price         = function_exists( 'webmz_get_product_loop_price_data' ) ? webmz_get_product_loop_price_data( $product ) : array();
	$discount_pct  = 0;

	if ( ! empty( $args['show_discount_badge'] ) && function_exists( 'webmz_get_product_loop_discount_percent' ) ) {
		$discount_pct = webmz_get_product_loop_discount_percent( $product );
	}

	$rating       = (float) $product->get_average_rating();
	$sales_count  = absint( $product->get_total_sales() );
	$preview_url  = function_exists( 'webmz_get_product_preview_url' ) ? webmz_get_product_preview_url( $product_id ) : '';
	$can_purchase = $product->is_purchasable() && $product->is_in_stock();
	$is_variable  = $product->is_type( 'variable' );
	$is_simple    = $product->is_type( 'simple' );
	$wrap_tag     = $as_slide ? 'div' : 'div';
	$wrap_class   = 'webmz-zpl__card-wrap' . ( $as_slide ? ' swiper-slide' : '' );
	?>
	<<?php echo esc_html( $wrap_tag ); ?> class="<?php echo esc_attr( $wrap_class ); ?>">
		<article class="webmz-zpl__card" data-product-id="<?php echo esc_attr( $product_id ); ?>">
			<a class="webmz-zpl__media-link" href="<?php echo esc_url( $product_url ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
				<div class="webmz-zpl__media">
					<?php if ( $image_html ) : ?>
						<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>
				</div>
			</a>

			<div class="webmz-zpl__body">
				<<?php echo esc_html( $title_tag ); ?> class="webmz-zpl__title">
					<a class="webmz-zpl__title-link" href="<?php echo esc_url( $product_url ); ?>"><?php echo esc_html( $title ); ?></a>
				</<?php echo esc_html( $title_tag ); ?>>

				<div class="webmz-zpl__footer">
					<div class="webmz-zpl__info">
					<?php if ( ! empty( $args['show_price'] ) ) : ?>
						<div class="webmz-zpl__price-row">
							<?php if ( $discount_pct > 0 ) : ?>
								<span class="webmz-zpl__discount" aria-hidden="true"><?php echo esc_html( webmz_zhaket_product_loop_format_discount( $discount_pct ) ); ?></span>
							<?php endif; ?>

							<div class="webmz-zpl__prices">
								<?php if ( 'unavailable' === ( $price['state'] ?? '' ) ) : ?>
									<span class="webmz-zpl__price webmz-zpl__price--label webmz-zpl__price--muted"><?php echo esc_html( $args['unavailable_text'] ); ?></span>
								<?php elseif ( 'free' === ( $price['state'] ?? '' ) ) : ?>
									<span class="webmz-zpl__price webmz-zpl__price--label"><?php echo esc_html( $args['free_text'] ); ?></span>
								<?php else : ?>
									<?php if ( ! empty( $price['regular_amount'] ) ) : ?>
										<span class="webmz-zpl__price-regular"><?php echo esc_html( $price['regular_amount'] ); ?></span>
									<?php endif; ?>
									<span class="webmz-zpl__price">
										<span class="webmz-zpl__price-amount"><?php echo esc_html( $price['amount'] ?? '' ); ?></span>
										<span class="webmz-zpl__price-currency"><?php echo esc_html( $price['currency'] ?? '' ); ?></span>
									</span>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $args['show_sales'] ) || ! empty( $args['show_rating'] ) ) : ?>
						<div class="webmz-zpl__meta">
							<?php if ( ! empty( $args['show_sales'] ) ) : ?>
								<span class="webmz-zpl__meta-item webmz-zpl__meta-item--sales" aria-label="<?php esc_attr_e( 'تعداد فروش', 'tadris' ); ?>">
									<svg class="webmz-zpl__meta-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6h15l-1.5 9h-12L6 6z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><circle cx="9" cy="19" r="1.4" fill="currentColor"/><circle cx="17" cy="19" r="1.4" fill="currentColor"/><path d="M6 6L5 3H2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
									<span class="webmz-zpl__meta-value"><?php echo esc_html( webmz_zhaket_product_loop_format_number( $sales_count ) ); ?></span>
								</span>
							<?php endif; ?>

							<?php if ( ! empty( $args['show_rating'] ) ) : ?>
								<span class="webmz-zpl__meta-item webmz-zpl__meta-item--rating" aria-label="<?php esc_attr_e( 'امتیاز', 'tadris' ); ?>">
									<svg class="webmz-zpl__meta-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3.5l2.35 4.76 5.25.76-3.8 3.7.9 5.24L12 15.9l-4.7 2.46.9-5.24-3.8-3.7 5.25-.76L12 3.5z" fill="currentColor"/></svg>
									<span class="webmz-zpl__meta-value"><?php echo esc_html( webmz_zhaket_product_loop_format_number( $rating, 1 ) ); ?></span>
								</span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $args['show_cart_button'] ) || ! empty( $args['show_preview_button'] ) ) : ?>
					<div class="webmz-zpl__actions">
						<?php if ( ! empty( $args['show_cart_button'] ) ) : ?>
							<?php if ( ! $can_purchase ) : ?>
								<span class="webmz-zpl__btn webmz-zpl__btn--cart is-disabled" aria-disabled="true">
									<?php echo esc_html( $args['unavailable_text'] ); ?>
								</span>
							<?php elseif ( $is_variable ) : ?>
								<a class="webmz-zpl__btn webmz-zpl__btn--cart" href="<?php echo esc_url( $product_url ); ?>">
									<?php echo esc_html( $args['variable_text'] ); ?>
								</a>
							<?php elseif ( $is_simple ) : ?>
								<button
									type="button"
									class="webmz-zpl__btn webmz-zpl__btn--cart"
									data-webmz-zpl-add-to-cart
									data-product-id="<?php echo esc_attr( $product_id ); ?>"
									data-quantity="1"
								><?php echo esc_html( $args['add_to_cart_text'] ); ?></button>
							<?php else : ?>
								<a class="webmz-zpl__btn webmz-zpl__btn--cart" href="<?php echo esc_url( $product_url ); ?>">
									<?php echo esc_html( $args['add_to_cart_text'] ); ?>
								</a>
							<?php endif; ?>
						<?php endif; ?>

						<?php if ( ! empty( $args['show_preview_button'] ) ) : ?>
							<?php if ( $preview_url || empty( $args['hide_preview_no_url'] ) ) : ?>
								<a
									class="webmz-zpl__btn webmz-zpl__btn--preview"
									href="<?php echo esc_url( $preview_url ? $preview_url : $product_url ); ?>"
									<?php echo $preview_url ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
								><?php echo esc_html( $args['preview_text'] ); ?></a>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				</div>
			</div>
		</article>
	</div>
	<?php
}
