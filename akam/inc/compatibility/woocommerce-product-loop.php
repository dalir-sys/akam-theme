<?php
/**
 * Webmasters product loop card markup shared by the shop archive and Elementor widget.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the current request should render the Webmasters product card in loops.
 *
 * @return bool
 */
function webmz_should_use_webmasters_product_loop_card() {
	if ( ! function_exists( 'is_shop' ) ) {
		return false;
	}

	return ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) && ! is_product();
}

/**
 * Default card rendering arguments.
 *
 * @return array<string,mixed>
 */
function webmz_get_product_loop_card_defaults() {
	return array(
		'show_image'                => true,
		'show_title'                => true,
		'show_price'                => true,
		'show_rating'               => false,
		'show_course_meta'          => false,
		'show_course_status'        => true,
		'show_sessions'             => true,
		'show_instructor'           => true,
		'show_students'             => true,
		'title_tag'                 => 'h2',
		'rating_label'              => esc_html__( 'امتیاز دانشجویان', 'tadris' ),
		'sessions_label'            => esc_html__( 'تعداد جلسات', 'tadris' ),
		'instructor_label'          => esc_html__( 'مدرس دوره', 'tadris' ),
		'students_label'            => esc_html__( 'دانشجویان', 'tadris' ),
		'status_finished_label'     => esc_html__( 'تکمیل شده', 'tadris' ),
		'status_recording_label'    => esc_html__( 'درحال ضبط', 'tadris' ),
		'button_text'               => esc_html__( 'ثبت نام', 'tadris' ),
		'variable_button_text'      => esc_html__( 'انتخاب گزینه‌ها', 'tadris' ),
		'unavailable_button_text'   => esc_html__( 'ناموجود', 'tadris' ),
		'free_price_text'           => esc_html__( 'رایگان', 'tadris' ),
		'unavailable_price_text'    => esc_html__( 'ناموجود', 'tadris' ),
		'rating_icon_html'          => '',
		'rating_icon_color_class'   => '',
		'instructor_icon_html'      => '',
		'instructor_icon_color_class' => '',
		'students_icon_html'        => '',
		'students_icon_color_class' => '',
		'show_category'             => false,
		'show_short_description'    => false,
		'show_sale_badge'           => false,
		'card_modifier_class'       => '',
		'show_add_to_cart'          => true,
		'price_label'               => esc_html__( 'قیمت دوره', 'tadris' ),
		'duration_label'            => '',
		'sessions_suffix'           => esc_html__( 'جلسه', 'tadris' ),
		'students_suffix'           => esc_html__( 'نفر', 'tadris' ),
		'excerpt_words'             => 14,
		'show_duration'             => true,
		'show_excerpt'              => true,
		'show_reviews'              => true,
		'show_button'               => true,
		'show_category_flag'        => true,
		'show_training_level'       => true,
		'show_student_count'        => true,
		'show_sessions_meta'        => true,
		'show_duration_meta'        => true,
		'reviews_label'             => '',
		'duration_icon_html'        => '',
		'duration_icon_color_class' => '',
		'sessions_icon_html'        => '',
		'sessions_icon_color_class' => '',
	);
}

/**
 * Get course loop card v2 meta (duration, sessions, instructor).
 *
 * @param \WC_Product $product Product object.
 * @return array{duration:string,sessions:int,instructor:string}
 */
function webmz_get_product_loop_card_v2_meta( $product ) {
	$product_id = $product->get_id();
	$sessions   = absint( get_post_meta( $product_id, '_webmz_course_sessions', true ) );
	$duration   = '';
	$curriculum = function_exists( 'webmz_spw_get_product_curriculum_stats' )
		? webmz_spw_get_product_curriculum_stats( $product_id )
		: array(
			'count'            => 0,
			'duration_seconds' => 0,
			'duration_label'   => '',
		);

	if ( $sessions <= 0 && ! empty( $curriculum['count'] ) ) {
		$sessions = absint( $curriculum['count'] );
	}

	if ( ! empty( $curriculum['duration_seconds'] ) ) {
		$duration = (string) $curriculum['duration_label'];
	}

	$instructor = get_the_author_meta(
		'display_name',
		(int) get_post_field( 'post_author', $product_id )
	);

	return array(
		'duration'   => $duration,
		'sessions'   => $sessions,
		'instructor' => $instructor,
	);
}

/**
 * Get product discount percent for loop badges.
 *
 * @param \WC_Product $product Product object.
 * @return int
 */
function webmz_get_product_loop_discount_percent( $product ) {
	if ( ! $product || ! $product->is_on_sale() ) {
		return 0;
	}

	if ( $product->is_type( 'variable' ) ) {
		$regular = (float) $product->get_variation_regular_price( 'min', true );
		$sale    = (float) $product->get_variation_price( 'min', true );
	} else {
		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_price();
	}

	if ( $regular <= 0 || $sale <= 0 || $sale >= $regular ) {
		return 0;
	}

	return (int) round( ( ( $regular - $sale ) / $regular ) * 100 );
}

/**
 * Get WooCommerce scheduled sale end timestamp for a product.
 *
 * @param \WC_Product $product Product object.
 * @return int Unix timestamp, or 0 when no end date is set.
 */
function webmz_get_product_sale_end_timestamp( $product ) {
	if ( ! $product ) {
		return 0;
	}

	$date = $product->get_date_on_sale_to();

	if ( $date ) {
		return (int) $date->getTimestamp();
	}

	$dates_to = (int) get_post_meta( $product->get_id(), '_sale_price_dates_to', true );

	return $dates_to > 0 ? $dates_to : 0;
}

/**
 * Get WooCommerce scheduled sale start timestamp for a product.
 *
 * @param \WC_Product $product Product object.
 * @return int Unix timestamp, or 0 when no start date is set.
 */
function webmz_get_product_sale_start_timestamp( $product ) {
	if ( ! $product ) {
		return 0;
	}

	$date = $product->get_date_on_sale_from();

	if ( $date ) {
		return (int) $date->getTimestamp();
	}

	$dates_from = (int) get_post_meta( $product->get_id(), '_sale_price_dates_from', true );

	return $dates_from > 0 ? $dates_from : 0;
}

/**
 * Normalize sale start timestamp for countdown progress calculations.
 *
 * @param \WC_Product $product Product object.
 * @param int         $end_ts  Sale end timestamp.
 * @param int|null    $now     Current timestamp.
 * @return int
 */
function webmz_normalize_product_sale_start_timestamp( $product, $end_ts = 0, $now = null ) {
	if ( ! $product ) {
		return 0;
	}

	$now    = null !== $now ? (int) $now : time();
	$end_ts = $end_ts > 0 ? (int) $end_ts : webmz_get_product_sale_end_timestamp( $product );

	if ( $end_ts <= $now ) {
		return 0;
	}

	$start_ts = webmz_get_product_sale_start_timestamp( $product );

	if ( $start_ts > 0 && $start_ts <= $now && $end_ts > $start_ts ) {
		return (int) $start_ts;
	}

	return max( 0, $now - ( 30 * DAY_IN_SECONDS ) );
}

/**
 * Get remaining sale window progress percent (100 = full time left).
 *
 * @param \WC_Product $product Product object.
 * @return float
 */
function webmz_get_product_sale_time_progress( $product ) {
	$end_ts = webmz_get_product_sale_end_timestamp( $product );

	if ( $end_ts <= 0 ) {
		return 0.0;
	}

	$now = time();

	if ( $end_ts <= $now ) {
		return 0.0;
	}

	$start_ts = webmz_normalize_product_sale_start_timestamp( $product, $end_ts, $now );
	$total    = $end_ts - $start_ts;

	if ( $total <= 0 ) {
		return 100.0;
	}

	$remaining = max( 0, $end_ts - $now );

	return min( 100.0, max( 0.0, ( $remaining / $total ) * 100 ) );
}

/**
 * Parse key feature lines from a product short description.
 *
 * @param \WC_Product $product   Product object.
 * @param int         $max_items Maximum items to return.
 * @return string[]
 */
function webmz_get_product_key_features( $product, $max_items = 8 ) {
	if ( ! $product ) {
		return array();
	}

	$max_items = max( 1, absint( $max_items ) );

	if ( function_exists( 'webmz_spw_get_product_features' ) ) {
		$features = webmz_spw_get_product_features( $product->get_id() );

		if ( ! empty( $features ) ) {
			return array_slice( $features, 0, $max_items );
		}
	}

	$html = '';

	if ( function_exists( 'webmz_spw_get_product_short_description' ) ) {
		$html = trim( (string) webmz_spw_get_product_short_description( $product->get_id() ) );
	}

	if ( '' === $html ) {
		$html = trim( (string) $product->get_short_description() );
	}

	if ( '' === $html ) {
		$html = trim( (string) $product->get_description() );
	}

	if ( '' === $html ) {
		return array();
	}

	if ( preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $html, $matches ) && ! empty( $matches[1] ) ) {
		$items = array_map(
			static function ( $item ) {
				return trim( wp_strip_all_tags( $item ) );
			},
			$matches[1]
		);

		$items = array_values( array_filter( $items ) );

		return array_slice( $items, 0, $max_items );
	}

	$plain = trim( wp_strip_all_tags( $html ) );

	if ( '' === $plain ) {
		return array();
	}

	$lines = preg_split( '/\r\n|\r|\n/u', $plain );
	$lines = array_values(
		array_filter(
			array_map(
				static function ( $line ) {
					$line = trim( (string) $line );
					$line = preg_replace( '/^[\-\*\•\–\—]+\s*/u', '', $line );

					return trim( (string) $line );
				},
				is_array( $lines ) ? $lines : array()
			)
		)
	);

	return array_slice( $lines, 0, $max_items );
}

/**
 * Query products with an active scheduled sale countdown.
 *
 * @param array<string,mixed> $args Query arguments.
 * @return \WC_Product[]
 */
function webmz_get_timed_sale_products( $args = array() ) {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return array();
	}

	$args = wp_parse_args(
		$args,
		array(
			'filter_by'     => 'category',
			'category'      => '',
			'tag'           => '',
			'count'         => 6,
			'order_by'      => 'sale_end',
			'include_ended' => false,
		)
	);

	$count = max( 1, min( 24, absint( $args['count'] ) ) );
	$query = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => $count * 3,
		'fields'         => 'ids',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => '_sale_price_dates_to',
				'value'   => 0,
				'compare' => '>',
				'type'    => 'NUMERIC',
			),
		),
	);

	if ( empty( $args['include_ended'] ) ) {
		$query['meta_query'][] = array(
			'key'     => '_sale_price_dates_to',
			'value'   => time(),
			'compare' => '>',
			'type'    => 'NUMERIC',
		);
	}

	if ( 'tag' === $args['filter_by'] && ! empty( $args['tag'] ) ) {
		$query['tax_query'] = array(
			array(
				'taxonomy' => 'product_tag',
				'field'    => 'slug',
				'terms'    => sanitize_title( (string) $args['tag'] ),
			),
		);
	} elseif ( ! empty( $args['category'] ) ) {
		$query['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => sanitize_title( (string) $args['category'] ),
			),
		);
	}

	switch ( $args['order_by'] ) {
		case 'popularity':
			$query['meta_key'] = 'total_sales';
			$query['orderby']  = 'meta_value_num';
			$query['order']    = 'DESC';
			break;
		case 'rating':
			$query['meta_key'] = '_wc_average_rating';
			$query['orderby']  = 'meta_value_num';
			$query['order']    = 'DESC';
			break;
		case 'date':
			$query['orderby'] = 'date';
			$query['order']   = 'DESC';
			break;
		case 'sale_end':
		default:
			$query['meta_key'] = '_sale_price_dates_to';
			$query['orderby']  = 'meta_value_num';
			$query['order']    = 'ASC';
			break;
	}

	$product_ids = get_posts( $query );

	if ( empty( $product_ids ) ) {
		return array();
	}

	$products = array();

	foreach ( $product_ids as $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			continue;
		}

		$sale_end = webmz_get_product_sale_end_timestamp( $product );

		if ( ! $sale_end ) {
			$dates_to = (int) get_post_meta( $product_id, '_sale_price_dates_to', true );

			if ( $dates_to <= 0 ) {
				continue;
			}

			if ( empty( $args['include_ended'] ) && $dates_to <= time() ) {
				continue;
			}

			if ( ! $product->is_on_sale() && empty( $args['include_ended'] ) ) {
				continue;
			}
		} elseif ( ! $product->is_on_sale() && empty( $args['include_ended'] ) ) {
			continue;
		}

		$products[] = $product;

		if ( count( $products ) >= $count ) {
			break;
		}
	}

	return $products;
}

/**
 * Get trimmed short description for loop cards.
 *
 * @param \WC_Product $product Product object.
 * @param int         $words   Word limit.
 * @return string
 */
function webmz_get_product_loop_short_excerpt( $product, $words = 14 ) {
	if ( ! $product ) {
		return '';
	}

	$text = $product->get_short_description();

	if ( ! $text ) {
		$text = $product->get_description();
	}

	$text = trim( wp_strip_all_tags( $text ) );

	if ( '' === $text ) {
		return '';
	}

	return wp_trim_words( $text, $words, '…' );
}

/**
 * Get price display state for loop cards.
 *
 * @param \WC_Product $product Product object.
 * @return array{state:string,amount:string,regular_amount?:string,currency:string}
 */
function webmz_get_product_loop_price_data( $product ) {
	$currency = html_entity_decode(
		get_woocommerce_currency_symbol(),
		ENT_QUOTES,
		get_bloginfo( 'charset' )
	);

	if ( ! $product->is_in_stock() ) {
		return array(
			'state'    => 'unavailable',
			'amount'   => '',
			'currency' => $currency,
		);
	}

	if ( '' === $product->get_price() && ! $product->is_type( 'variable' ) ) {
		return array(
			'state'    => 'unavailable',
			'amount'   => '',
			'currency' => $currency,
		);
	}

	$raw_price = (float) wc_get_price_to_display( $product );

	if ( $product->is_type( 'variable' ) && $raw_price <= 0 ) {
		$min_price = $product->get_variation_price( 'min', true );

		if ( '' !== $min_price ) {
			$raw_price = (float) wc_get_price_to_display( $product, array( 'price' => $min_price ) );
		}
	}

	if ( $raw_price <= 0 && ! $product->is_type( 'variable' ) ) {
		return array(
			'state'          => 'free',
			'amount'         => '',
			'regular_amount' => '',
			'currency'       => $currency,
		);
	}

	if ( $raw_price <= 0 && $product->is_type( 'variable' ) ) {
		return array(
			'state'    => 'unavailable',
			'amount'   => '',
			'currency' => $currency,
		);
	}

	$regular_display_price = 0;

	if ( $product->is_on_sale() ) {
		$regular_price = $product->is_type( 'variable' ) && method_exists( $product, 'get_variation_regular_price' )
			? $product->get_variation_regular_price( 'min', true )
			: $product->get_regular_price();

		if ( '' !== $regular_price ) {
			$regular_display_price = (float) wc_get_price_to_display( $product, array( 'price' => $regular_price ) );
		}
	}

	return array(
		'state'          => 'priced',
		'amount'         => number_format( $raw_price, 0, '.', ',' ),
		'regular_amount' => $regular_display_price > $raw_price ? number_format( $regular_display_price, 0, '.', ',' ) : '',
		'currency'       => $currency,
	);
}

/**
 * Render Tadris-style price markup for loop cards.
 *
 * @param array{state:string,amount:string,regular_amount?:string,currency:string} $price  Price data.
 * @param array<string,string>                                                     $labels Optional text overrides.
 * @return void
 */
function webmz_render_product_loop_price( $price, $labels = array() ) {
	$labels = wp_parse_args(
		$labels,
		array(
			'unavailable' => esc_html__( 'ناموجود', 'tadris' ),
			'free'        => esc_html__( 'رایگان', 'tadris' ),
		)
	);
	?>
	<div class="tadris-price">
		<?php if ( 'unavailable' === $price['state'] ) : ?>
			<div class="tadris-not-for-sale">
				<?php echo esc_html( $labels['unavailable'] ); ?>
			</div>
		<?php elseif ( 'free' === $price['state'] ) : ?>
			<div class="tadris-free-price">
				<?php echo esc_html( $labels['free'] ); ?>
			</div>
		<?php else : ?>
			<div class="tadris-price-amount">
				<?php echo esc_html( $price['amount'] ); ?>
			</div>
			<div class="tadris-price-row">
				<div class="tadris-price-currency">
					<?php echo esc_html( $price['currency'] ); ?>
				</div>
				<?php if ( ! empty( $price['regular_amount'] ) ) : ?>
					<div class="tadris-price-amount-del">
						<?php echo esc_html( $price['regular_amount'] ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render loop card action button.
 *
 * @param \WC_Product          $product Product object.
 * @param array<string,mixed> $args    Card args.
 * @return void
 */
function webmz_render_product_loop_button( $product, $args ) {
	$product_id = $product->get_id();

	if ( ! $product->is_in_stock() || ! $product->is_purchasable() ) {
		?>
		<div class="type-1-add-to-cart">
			<span class="type-1-add-to-cart-btn is-disabled" aria-disabled="true">
				<?php echo esc_html( $args['unavailable_button_text'] ); ?>
			</span>
		</div>
		<?php
		return;
	}

	if ( $product->is_type( 'variable' ) ) {
		?>
		<div class="type-1-add-to-cart">
			<a class="type-1-add-to-cart-btn" href="<?php echo esc_url( $product->get_permalink() ); ?>">
				<?php echo esc_html( $args['variable_button_text'] ); ?>
			</a>
		</div>
		<?php
		return;
	}

	if ( ! $product->is_type( 'simple' ) ) {
		?>
		<div class="type-1-add-to-cart">
			<a class="type-1-add-to-cart-btn" href="<?php echo esc_url( $product->get_permalink() ); ?>">
				<?php echo esc_html( $args['button_text'] ); ?>
			</a>
		</div>
		<?php
		return;
	}

	$is_ajax = $product->is_purchasable() && $product->is_in_stock();
	?>
	<div class="type-1-add-to-cart">
		<a
			class="type-1-add-to-cart-btn"
			href="<?php echo esc_url( $is_ajax ? '#' : $product->get_permalink() ); ?>"
			<?php if ( $is_ajax ) : ?>
				data-webmz-course-add
				data-product-id="<?php echo esc_attr( $product_id ); ?>"
				data-quantity="1"
			<?php endif; ?>
		>
			<?php echo esc_html( $args['button_text'] ); ?>
		</a>
	</div>
	<?php
}

/**
 * Render one Webmasters product loop card.
 *
 * @param \WC_Product          $product Product object.
 * @param array<string,mixed> $args    Optional overrides.
 * @return void
 */
function webmz_render_product_loop_card( $product, $args = array() ) {
	if ( ! $product ) {
		return;
	}

	$args       = wp_parse_args( $args, webmz_get_product_loop_card_defaults() );
	$product_id = $product->get_id();
	$title_tag  = tag_escape( $args['title_tag'] );
	$price      = webmz_get_product_loop_price_data( $product );

	$status   = '';
	$sessions = 0;
	$students = 0;
	$author   = '';

	if ( $args['show_course_meta'] ) {
		$status_value = get_post_meta( $product_id, '_webmz_course_status', true );
		$status       = 'recording' === $status_value ? 'recording' : 'finished';
		$sessions     = absint( get_post_meta( $product_id, '_webmz_course_sessions', true ) );

		$source = get_post_meta( $product_id, '_webmz_students_source', true );
		$students = 'manual' === $source
			? absint( get_post_meta( $product_id, '_webmz_students_manual', true ) )
			: absint( $product->get_total_sales() );

		$author = get_the_author_meta(
			'display_name',
			(int) get_post_field( 'post_author', $product_id )
		);
	}

	$rating  = (float) $product->get_average_rating();
	$ratings = absint( $product->get_rating_count() );
	$modifier_class = ! empty( $args['card_modifier_class'] ) ? $args['card_modifier_class'] : '';
	$discount_pct   = $args['show_sale_badge'] ? webmz_get_product_loop_discount_percent( $product ) : 0;
	$category_term  = $args['show_category'] ? webmz_get_deepest_product_category( $product_id ) : null;
	$excerpt        = $args['show_short_description'] ? webmz_get_product_loop_short_excerpt( $product ) : '';
	$has_body       = ( $args['show_category'] && $category_term instanceof WP_Term ) || '' !== $excerpt;
	$has_meta       = $args['show_course_meta'] && (
		$args['show_course_status'] ||
		$args['show_sessions'] ||
		$args['show_instructor'] ||
		$args['show_students']
	);
	$price_labels   = array(
		'unavailable' => $args['unavailable_price_text'],
		'free'        => $args['free_price_text'],
	);
	?>
	<article class="tadris-product-type-1<?php echo $modifier_class ? ' ' . esc_attr( $modifier_class ) : ''; ?>">
		<?php if ( $args['show_image'] ) : ?>
		<figure class="product-type-1-figure">
			<a href="<?php echo esc_url( $product->get_permalink() ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
				<?php echo webmz_get_product_loop_image( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
			<?php if ( $discount_pct > 0 ) : ?>
				<span class="product-type-1-sale-badge" aria-hidden="true">
					<?php
					$badge = '%' . $discount_pct;
					echo esc_html( function_exists( 'webmz_to_persian_digits' ) ? webmz_to_persian_digits( $badge ) : $badge );
					?>
				</span>
			<?php endif; ?>
		</figure>
		<?php endif; ?>

		<?php if ( $args['show_rating'] ) : ?>
			<div class="product-type-1-rating">
				<p><?php echo esc_html( $args['rating_label'] ); ?></p>
				<div class="product-type-1-rating-star <?php echo esc_attr( $args['rating_icon_color_class'] ); ?>">
					<?php
					if ( ! empty( $args['rating_icon_html'] ) ) {
						echo $args['rating_icon_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
					<strong><?php echo esc_html( number_format_i18n( $rating, 1 ) ); ?></strong>
					<span>(<?php echo esc_html( number_format_i18n( $ratings ) ); ?>)</span>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $has_body ) : ?>
			<div class="product-type-1-body">
				<?php if ( $args['show_category'] && $category_term instanceof WP_Term ) : ?>
					<?php
					$category_link = get_term_link( $category_term );
					$category_url  = is_wp_error( $category_link ) ? '' : $category_link;
					?>
					<?php if ( $category_url ) : ?>
						<a class="product-type-1-category" href="<?php echo esc_url( $category_url ); ?>">
							<?php echo esc_html( $category_term->name ); ?>
						</a>
					<?php else : ?>
						<span class="product-type-1-category"><?php echo esc_html( $category_term->name ); ?></span>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( $args['show_title'] ) : ?>
				<header class="product-type-1-header">
					<<?php echo esc_attr( $title_tag ); ?> class="product-type-1-header-tag">
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<?php echo esc_html( $product->get_name() ); ?>
						</a>
					</<?php echo esc_attr( $title_tag ); ?>>
				</header>
				<?php endif; ?>

				<?php if ( '' !== $excerpt ) : ?>
					<p class="product-type-1-excerpt"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>
			</div>
		<?php elseif ( $args['show_title'] ) : ?>
		<header class="product-type-1-header">
			<<?php echo esc_attr( $title_tag ); ?> class="product-type-1-header-tag">
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
					<?php echo esc_html( $product->get_name() ); ?>
				</a>
			</<?php echo esc_attr( $title_tag ); ?>>
		</header>
		<?php endif; ?>

		<?php if ( $has_meta ) : ?>
			<div class="product-type-1-content">
				<?php if ( $args['show_course_status'] || $args['show_sessions'] ) : ?>
				<div class="product-type-1-info">
					<?php if ( $args['show_course_status'] ) : ?>
					<div class="product-type-1-state">
						<span class="pd-state <?php echo 'finished' === $status ? 'state-finished' : 'state-holding'; ?>">
							<?php
							echo 'finished' === $status
								? esc_html( $args['status_finished_label'] )
								: esc_html( $args['status_recording_label'] );
							?>
						</span>
					</div>
					<?php endif; ?>

					<?php if ( $args['show_sessions'] ) : ?>
					<div class="product-type-1-sessions">
						<span><?php echo esc_html( $args['sessions_label'] ); ?></span>
						<strong>
							<?php
							echo esc_html(
								number_format_i18n( $sessions ) . ' ' . $args['sessions_suffix']
							);
							?>
						</strong>
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<?php if ( $args['show_instructor'] || $args['show_students'] ) : ?>
				<div class="product-type-1-stats">
					<?php if ( $args['show_instructor'] ) : ?>
					<div class="type-1-stats-item">
						<div class="type-1-stats-item-icon type-1-secondary <?php echo esc_attr( $args['instructor_icon_color_class'] ); ?>">
							<?php
							if ( ! empty( $args['instructor_icon_html'] ) ) {
								echo $args['instructor_icon_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div>
						<div class="type-1-stats-item-content">
							<span><?php echo esc_html( $args['instructor_label'] ); ?></span>
							<strong><?php echo esc_html( $author ); ?></strong>
						</div>
					</div>
					<?php endif; ?>

					<?php if ( $args['show_students'] ) : ?>
					<div class="type-1-stats-item">
						<div class="type-1-stats-item-icon <?php echo esc_attr( $args['students_icon_color_class'] ); ?>">
							<?php
							if ( ! empty( $args['students_icon_html'] ) ) {
								echo $args['students_icon_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div>
						<div class="type-1-stats-item-content">
							<span><?php echo esc_html( $args['students_label'] ); ?></span>
							<strong>
								<?php
								echo esc_html(
									number_format_i18n( $students ) . ' ' . $args['students_suffix']
								);
								?>
							</strong>
						</div>
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $args['show_price'] || $args['show_add_to_cart'] ) : ?>
		<footer class="product-type-1-footer">
			<?php if ( $args['show_price'] ) : ?>
				<?php webmz_render_product_loop_price( $price, $price_labels ); ?>
			<?php endif; ?>
			<?php if ( $args['show_add_to_cart'] ) : ?>
				<?php webmz_render_product_loop_button( $product, $args ); ?>
			<?php endif; ?>
		</footer>
		<?php endif; ?>
	</article>
	<?php
}

/**
 * Render Tadris-style price markup for type-2 loop cards.
 *
 * @param array{state:string,amount:string,regular_amount?:string,currency:string} $price  Price data.
 * @param string                                                                    $label  Price label.
 * @param array<string,string>                                                      $labels Optional text overrides.
 * @return void
 */
function webmz_render_product_loop_price_v2( $price, $label = '', $labels = array() ) {
	$label = $label ? $label : esc_html__( 'قیمت دوره', 'tadris' );
	$labels = wp_parse_args(
		$labels,
		array(
			'unavailable' => esc_html__( 'ناموجود', 'tadris' ),
			'free'        => esc_html__( 'رایگان', 'tadris' ),
		)
	);
	?>
	<div class="product-type-2-price-wrap">
		<span class="product-type-2-price-label"><?php echo esc_html( $label ); ?></span>
		<div class="product-type-2-price">
			<?php if ( 'unavailable' === $price['state'] ) : ?>
				<span class="product-type-2-price-unavailable"><?php echo esc_html( $labels['unavailable'] ); ?></span>
			<?php elseif ( 'free' === $price['state'] ) : ?>
				<span class="product-type-2-price-free"><?php echo esc_html( $labels['free'] ); ?></span>
			<?php else : ?>
				<?php if ( ! empty( $price['regular_amount'] ) ) : ?>
					<span class="product-type-2-price-regular"><?php echo esc_html( $price['regular_amount'] ); ?> <?php echo esc_html( $price['currency'] ); ?></span>
				<?php endif; ?>
				<div class="product-type-2-price-current">
					<span class="product-type-2-price-amount"><?php echo esc_html( $price['amount'] ); ?></span>
					<span class="product-type-2-price-currency"><?php echo esc_html( $price['currency'] ); ?></span>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Render star rating markup for product loop card v2.
 *
 * @param float  $rating            Average rating.
 * @param string $icon_html         Optional custom star icon HTML.
 * @param string $icon_color_class  Optional SVG color mode class.
 * @param int    $max               Maximum stars.
 * @return void
 */
function webmz_render_product_loop_rating_stars_v2( $rating, $icon_html = '', $icon_color_class = '', $max = 5 ) {
	$rating = max( 0, min( (float) $max, (float) $rating ) );
	$max    = max( 1, absint( $max ) );
	?>
	<div
		class="product-type-2-stars-rating<?php echo $icon_color_class ? ' ' . esc_attr( $icon_color_class ) : ''; ?>"
		aria-label="<?php echo esc_attr( sprintf( __( 'امتیاز %1$s از %2$s', 'tadris' ), $rating, $max ) ); ?>"
	>
		<?php for ( $i = 1; $i <= $max; $i++ ) : ?>
			<span class="product-type-2-star <?php echo $rating >= $i ? 'is-filled' : ''; ?>" aria-hidden="true">
				<?php
				if ( $icon_html ) {
					echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo '★'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</span>
		<?php endfor; ?>
	</div>
	<?php
}

/**
 * Render loop card action button for type-2 cards.
 *
 * @param \WC_Product          $product Product object.
 * @param array<string,mixed> $args    Card args.
 * @return void
 */
function webmz_render_product_loop_button_v2( $product, $args ) {
	$product_id = $product->get_id();

	if ( ! $product->is_in_stock() || ! $product->is_purchasable() ) {
		?>
		<div class="type-2-add-to-cart">
			<span class="type-2-add-to-cart-btn is-disabled" aria-disabled="true">
				<?php echo esc_html( $args['unavailable_button_text'] ); ?>
			</span>
		</div>
		<?php
		return;
	}

	if ( $product->is_type( 'variable' ) ) {
		?>
		<div class="type-2-add-to-cart">
			<a class="type-2-add-to-cart-btn" href="<?php echo esc_url( $product->get_permalink() ); ?>">
				<?php echo esc_html( $args['variable_button_text'] ); ?>
			</a>
		</div>
		<?php
		return;
	}

	if ( ! $product->is_type( 'simple' ) ) {
		?>
		<div class="type-2-add-to-cart">
			<a class="type-2-add-to-cart-btn" href="<?php echo esc_url( $product->get_permalink() ); ?>">
				<?php echo esc_html( $args['button_text'] ); ?>
			</a>
		</div>
		<?php
		return;
	}

	$is_ajax = $product->is_purchasable() && $product->is_in_stock();
	?>
	<div class="type-2-add-to-cart">
		<a
			class="type-2-add-to-cart-btn"
			href="<?php echo esc_url( $is_ajax ? '#' : $product->get_permalink() ); ?>"
			<?php if ( $is_ajax ) : ?>
				data-webmz-course-add
				data-product-id="<?php echo esc_attr( $product_id ); ?>"
				data-quantity="1"
			<?php endif; ?>
		>
			<?php echo esc_html( $args['button_text'] ); ?>
		</a>
	</div>
	<?php
}

/**
 * Render one Webmasters product loop card (type 2).
 *
 * @param \WC_Product          $product Product object.
 * @param array<string,mixed> $args    Optional overrides.
 * @return void
 */
function webmz_render_product_loop_card_v2( $product, $args = array() ) {
	if ( ! $product ) {
		return;
	}

	$args = wp_parse_args(
		$args,
		array_merge(
			webmz_get_product_loop_card_defaults(),
			array(
				'show_image'             => true,
				'show_rating'              => true,
				'show_course_meta'         => true,
				'show_category'            => true,
				'show_short_description'   => true,
				'show_title'               => true,
				'show_duration'            => true,
				'show_sessions'            => true,
				'show_instructor'          => true,
				'show_price'               => true,
				'show_add_to_cart'         => true,
			)
		)
	);

	$product_id     = $product->get_id();
	$title_tag      = tag_escape( $args['title_tag'] );
	$price          = webmz_get_product_loop_price_data( $product );
	$rating         = (float) $product->get_average_rating();
	$category_term  = $args['show_category'] ? webmz_get_deepest_product_category( $product_id ) : null;
	$excerpt        = '';

	if ( $args['show_short_description'] ) {
		if ( function_exists( 'webmz_spw_get_product_short_description' ) ) {
			$custom_excerpt = trim( wp_strip_all_tags( webmz_spw_get_product_short_description( $product_id ) ) );

			if ( '' !== $custom_excerpt ) {
				$excerpt = wp_trim_words( $custom_excerpt, absint( $args['excerpt_words'] ), '…' );
			}
		}

		if ( '' === $excerpt ) {
			$excerpt = webmz_get_product_loop_short_excerpt( $product, absint( $args['excerpt_words'] ) );
		}
	}
	$modifier_class = ! empty( $args['card_modifier_class'] ) ? $args['card_modifier_class'] : '';
	$course_meta    = $args['show_course_meta'] ? webmz_get_product_loop_card_v2_meta( $product ) : array(
		'duration'   => '',
		'sessions'   => 0,
		'instructor' => '',
	);
	$has_meta       = $args['show_course_meta'] && (
		( $args['show_duration'] && '' !== $course_meta['duration'] ) ||
		( $args['show_sessions'] && $course_meta['sessions'] > 0 ) ||
		( $args['show_instructor'] && '' !== $course_meta['instructor'] )
	);
	$price_labels   = array(
		'unavailable' => $args['unavailable_price_text'],
		'free'        => $args['free_price_text'],
	);
	?>
	<article class="tadris-product-type-2<?php echo $modifier_class ? ' ' . esc_attr( $modifier_class ) : ''; ?>">
		<?php if ( $args['show_image'] ) : ?>
		<figure class="product-type-2-figure">
			<a href="<?php echo esc_url( $product->get_permalink() ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
				<?php echo webmz_get_product_loop_image( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</figure>
		<?php endif; ?>

		<div class="product-type-2-body">
			<?php if ( $args['show_rating'] || ( $args['show_category'] && $category_term instanceof WP_Term ) ) : ?>
				<div class="product-type-2-meta">
					<?php if ( $args['show_category'] && $category_term instanceof WP_Term ) : ?>
						<?php
						$category_link = get_term_link( $category_term );
						$category_url  = is_wp_error( $category_link ) ? '' : $category_link;
						?>
						<?php if ( $category_url ) : ?>
							<a class="product-type-2-category" href="<?php echo esc_url( $category_url ); ?>">
								<?php echo esc_html( $category_term->name ); ?>
							</a>
						<?php else : ?>
							<span class="product-type-2-category"><?php echo esc_html( $category_term->name ); ?></span>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( $args['show_rating'] ) : ?>
						<div class="product-type-2-stars">
							<?php
							if ( function_exists( 'webmz_render_product_loop_rating_stars_v2' ) ) {
								webmz_render_product_loop_rating_stars_v2(
									$rating,
									$args['rating_icon_html'] ?? '',
									$args['rating_icon_color_class'] ?? ''
								);
							} elseif ( function_exists( 'webmz_wc_reviews_widget_render_stars' ) ) {
								echo webmz_wc_reviews_widget_render_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $args['show_title'] ) : ?>
			<header class="product-type-2-header">
				<<?php echo esc_attr( $title_tag ); ?> class="product-type-2-header-tag">
					<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
						<?php echo esc_html( $product->get_name() ); ?>
					</a>
				</<?php echo esc_attr( $title_tag ); ?>>
			</header>
			<?php endif; ?>

			<?php if ( $args['show_short_description'] && '' !== $excerpt ) : ?>
				<p class="product-type-2-excerpt"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>

			<?php if ( $has_meta ) : ?>
				<div class="product-type-2-info">
					<?php if ( $args['show_duration'] && '' !== $course_meta['duration'] ) : ?>
						<div class="product-type-2-info-item">
							<span class="product-type-2-info-icon <?php echo esc_attr( $args['duration_icon_color_class'] ); ?>">
								<?php
								if ( ! empty( $args['duration_icon_html'] ) ) {
									echo $args['duration_icon_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
							</span>
							<span class="product-type-2-info-text">
								<?php if ( '' !== $args['duration_label'] ) : ?>
									<span class="product-type-2-info-label"><?php echo esc_html( $args['duration_label'] ); ?></span>
								<?php endif; ?>
								<?php echo esc_html( $course_meta['duration'] ); ?>
							</span>
						</div>
					<?php endif; ?>

					<?php if ( $args['show_sessions'] && $course_meta['sessions'] > 0 ) : ?>
						<div class="product-type-2-info-item">
							<span class="product-type-2-info-icon <?php echo esc_attr( $args['sessions_icon_color_class'] ); ?>">
								<?php
								if ( ! empty( $args['sessions_icon_html'] ) ) {
									echo $args['sessions_icon_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
							</span>
							<span class="product-type-2-info-text">
								<?php if ( '' !== $args['sessions_label'] ) : ?>
									<span class="product-type-2-info-label"><?php echo esc_html( $args['sessions_label'] ); ?></span>
								<?php endif; ?>
								<?php
								echo esc_html(
									number_format_i18n( $course_meta['sessions'] ) . ' ' . $args['sessions_suffix']
								);
								?>
							</span>
						</div>
					<?php endif; ?>

					<?php if ( $args['show_instructor'] && '' !== $course_meta['instructor'] ) : ?>
						<div class="product-type-2-info-item">
							<span class="product-type-2-info-icon <?php echo esc_attr( $args['instructor_icon_color_class'] ); ?>">
								<?php
								if ( ! empty( $args['instructor_icon_html'] ) ) {
									echo $args['instructor_icon_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
							</span>
							<span class="product-type-2-info-text">
								<?php if ( '' !== $args['instructor_label'] ) : ?>
									<span class="product-type-2-info-label"><?php echo esc_html( $args['instructor_label'] ); ?></span>
								<?php endif; ?>
								<?php echo esc_html( $course_meta['instructor'] ); ?>
							</span>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $args['show_price'] || ! empty( $args['show_add_to_cart'] ) ) : ?>
		<footer class="product-type-2-footer">
			<?php if ( $args['show_price'] ) : ?>
				<?php webmz_render_product_loop_price_v2( $price, $args['price_label'], $price_labels ); ?>
			<?php endif; ?>

			<?php if ( ! empty( $args['show_add_to_cart'] ) ) : ?>
				<?php webmz_render_product_loop_button_v2( $product, $args ); ?>
			<?php endif; ?>
		</footer>
		<?php endif; ?>
	</article>
	<?php
}

/**
 * Get product category thumbnail URL for loop card flag badge.
 *
 * @param int $product_id Product ID.
 * @return string
 */
function webmz_get_product_category_flag_url( $product_id ) {
	$term = webmz_get_deepest_product_category( $product_id );

	if ( ! $term instanceof WP_Term ) {
		return '';
	}

	$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );

	if ( $thumbnail_id <= 0 ) {
		return '';
	}

	$url = wp_get_attachment_image_url( $thumbnail_id, 'thumbnail' );

	return $url ? $url : '';
}

/**
 * Get student count for loop cards.
 *
 * @param \WC_Product $product Product object.
 * @return int
 */
function webmz_get_product_loop_student_count( $product ) {
	if ( ! $product ) {
		return 0;
	}

	$product_id = $product->get_id();
	$source     = get_post_meta( $product_id, '_webmz_students_source', true );

	return 'manual' === $source
		? absint( get_post_meta( $product_id, '_webmz_students_manual', true ) )
		: absint( $product->get_total_sales() );
}

/**
 * Render loop card view-course button for type-3 cards.
 *
 * @param \WC_Product $product Product object.
 * @param string      $text    Button label.
 * @return void
 */
function webmz_render_product_loop_view_button_v3( $product, $text = '' ) {
	if ( ! $product ) {
		return;
	}

	$text = $text ? $text : esc_html__( 'مشاهده دوره', 'tadris' );
	?>
	<a class="product-type-3-btn" href="<?php echo esc_url( $product->get_permalink() ); ?>">
		<?php echo esc_html( $text ); ?>
	</a>
	<?php
}

/**
 * Render price row for type-3 loop cards.
 *
 * @param array{state:string,amount:string,regular_amount?:string,currency:string} $price        Price data.
 * @param int                                                                       $discount_pct Discount percent.
 * @param array<string,string>                                                      $labels       Optional text overrides.
 * @return void
 */
function webmz_render_product_loop_price_v3( $price, $discount_pct = 0, $labels = array() ) {
	$labels = wp_parse_args(
		$labels,
		array(
			'unavailable' => esc_html__( 'ناموجود', 'tadris' ),
			'free'        => esc_html__( 'رایگان', 'tadris' ),
		)
	);
	?>
	<div class="product-type-3-price-row">
		<?php if ( $discount_pct > 0 ) : ?>
			<span class="product-type-3-discount-badge">
				<?php
				$badge = $discount_pct . '%';
				echo esc_html( function_exists( 'webmz_to_persian_digits' ) ? webmz_to_persian_digits( $badge ) : $badge );
				?>
			</span>
		<?php else : ?>
			<span class="product-type-3-discount-badge is-empty" aria-hidden="true"></span>
		<?php endif; ?>

		<div class="product-type-3-price">
			<?php if ( 'unavailable' === $price['state'] ) : ?>
				<span class="product-type-3-price-unavailable"><?php echo esc_html( $labels['unavailable'] ); ?></span>
			<?php elseif ( 'free' === $price['state'] ) : ?>
				<span class="product-type-3-price-free"><?php echo esc_html( $labels['free'] ); ?></span>
			<?php else : ?>
				<?php if ( ! empty( $price['regular_amount'] ) ) : ?>
					<span class="product-type-3-price-regular">
						<?php echo esc_html( $price['regular_amount'] ); ?>
						<span class="product-type-3-price-currency"><?php echo esc_html( $price['currency'] ); ?></span>
					</span>
				<?php endif; ?>
				<div class="product-type-3-price-current">
					<span class="product-type-3-price-amount"><?php echo esc_html( $price['amount'] ); ?></span>
					<span class="product-type-3-price-currency"><?php echo esc_html( $price['currency'] ); ?></span>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Render one Webmasters product loop card (type 3).
 *
 * @param \WC_Product          $product Product object.
 * @param array<string,mixed> $args    Optional overrides.
 * @return void
 */
function webmz_render_product_loop_card_v3( $product, $args = array() ) {
	if ( ! $product ) {
		return;
	}

	$args = wp_parse_args(
		$args,
		array_merge(
			webmz_get_product_loop_card_defaults(),
			array(
				'show_image'             => true,
				'show_rating'            => true,
				'show_course_meta'       => true,
				'show_short_description' => true,
				'show_training_level'    => true,
				'show_category_flag'     => true,
				'show_student_count'     => true,
				'show_reviews'           => true,
				'show_sessions_meta'     => true,
				'show_duration_meta'     => true,
				'show_instructor'        => true,
				'show_title'             => true,
				'show_price'             => true,
				'show_button'            => true,
				'show_sale_badge'        => true,
				'sessions_suffix'        => esc_html__( 'درس', 'tadris' ),
				'button_text'            => esc_html__( 'مشاهده دوره', 'tadris' ),
				'students_suffix'        => esc_html__( 'نفر', 'tadris' ),
				'reviews_icon_html'      => '',
				'reviews_icon_color_class' => '',
			)
		)
	);

	$product_id     = $product->get_id();
	$title_tag      = tag_escape( $args['title_tag'] );
	$price          = webmz_get_product_loop_price_data( $product );
	$rating         = (float) $product->get_average_rating();
	$ratings_count  = absint( $product->get_rating_count() );
	$discount_pct   = $args['show_sale_badge'] ? webmz_get_product_loop_discount_percent( $product ) : 0;
	$modifier_class = ! empty( $args['card_modifier_class'] ) ? $args['card_modifier_class'] : '';
	$excerpt        = '';
	$author_id      = (int) get_post_field( 'post_author', $product_id );
	$instructor     = get_the_author_meta( 'display_name', $author_id );
	$flag_url       = $args['show_category_flag'] ? webmz_get_product_category_flag_url( $product_id ) : '';
	$level_label    = $args['show_training_level'] && function_exists( 'webmz_get_course_training_level_label' )
		? webmz_get_course_training_level_label( $product_id )
		: '';
	$students       = $args['show_student_count'] ? webmz_get_product_loop_student_count( $product ) : 0;
	$price_labels   = array(
		'unavailable' => $args['unavailable_price_text'],
		'free'        => $args['free_price_text'],
	);

	if ( $args['show_short_description'] ) {
		if ( function_exists( 'webmz_spw_get_product_short_description' ) ) {
			$custom_excerpt = trim( wp_strip_all_tags( webmz_spw_get_product_short_description( $product_id ) ) );

			if ( '' !== $custom_excerpt ) {
				$excerpt = wp_trim_words( $custom_excerpt, absint( $args['excerpt_words'] ), '…' );
			}
		}

		if ( '' === $excerpt ) {
			$excerpt = webmz_get_product_loop_short_excerpt( $product, absint( $args['excerpt_words'] ) );
		}
	}

	$course_meta = $args['show_course_meta'] ? webmz_get_product_loop_card_v2_meta( $product ) : array(
		'duration'   => '',
		'sessions'   => 0,
		'instructor' => '',
	);

	if ( '' === $instructor && ! empty( $course_meta['instructor'] ) ) {
		$instructor = $course_meta['instructor'];
	}

	$show_stats = ( $args['show_rating'] && $rating > 0 )
		|| ( $args['show_student_count'] && $students > 0 )
		|| ( $args['show_reviews'] && $ratings_count > 0 );
	$show_meta  = $args['show_course_meta'] && (
		( $args['show_sessions_meta'] && $course_meta['sessions'] > 0 ) ||
		( $args['show_duration_meta'] && '' !== $course_meta['duration'] )
	);
	?>
	<article class="tadris-product-type-3<?php echo $modifier_class ? ' ' . esc_attr( $modifier_class ) : ''; ?>">
		<?php if ( $args['show_image'] ) : ?>
		<figure class="product-type-3-figure">
			<a href="<?php echo esc_url( $product->get_permalink() ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
				<?php echo webmz_get_product_loop_image( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>

			<?php if ( $flag_url ) : ?>
				<span class="product-type-3-flag">
					<img src="<?php echo esc_url( $flag_url ); ?>" alt="" width="28" height="28" loading="lazy" decoding="async">
				</span>
			<?php endif; ?>

			<?php if ( '' !== $level_label ) : ?>
				<span class="product-type-3-level-badge"><?php echo esc_html( $level_label ); ?></span>
			<?php endif; ?>

			<?php if ( $discount_pct > 0 ) : ?>
				<span class="product-type-3-image-badge">
					<?php
					$badge = $discount_pct . '%';
					echo esc_html( function_exists( 'webmz_to_persian_digits' ) ? webmz_to_persian_digits( $badge ) : $badge );
					?>
				</span>
			<?php endif; ?>
		</figure>
		<?php endif; ?>

		<div class="product-type-3-body">
			<?php if ( $args['show_title'] ) : ?>
			<header class="product-type-3-header">
				<<?php echo esc_attr( $title_tag ); ?> class="product-type-3-header-tag">
					<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
						<?php echo esc_html( $product->get_name() ); ?>
					</a>
				</<?php echo esc_attr( $title_tag ); ?>>
			</header>
			<?php endif; ?>

			<?php if ( $args['show_short_description'] && '' !== $excerpt ) : ?>
				<p class="product-type-3-subtitle"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>

			<?php if ( $args['show_instructor'] && '' !== $instructor ) : ?>
				<div class="product-type-3-instructor">
					<?php echo get_avatar( $author_id, 32, '', $instructor, array( 'class' => 'product-type-3-instructor-avatar' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="product-type-3-instructor-name">
						<?php if ( '' !== $args['instructor_label'] ) : ?>
							<span class="product-type-3-instructor-label"><?php echo esc_html( $args['instructor_label'] ); ?></span>
						<?php endif; ?>
						<?php echo esc_html( $instructor ); ?>
					</span>
				</div>
			<?php endif; ?>

			<?php if ( $show_stats ) : ?>
				<div class="product-type-3-stats-row">
					<?php if ( $args['show_rating'] && $rating > 0 ) : ?>
						<div class="product-type-3-rating">
							<span class="product-type-3-rating-icon <?php echo esc_attr( $args['rating_icon_color_class'] ?? '' ); ?>">
								<?php
								if ( ! empty( $args['rating_icon_html'] ) ) {
									echo $args['rating_icon_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								} else {
									echo '★'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
							</span>
							<?php if ( '' !== $args['rating_label'] ) : ?>
								<span class="product-type-3-rating-label"><?php echo esc_html( $args['rating_label'] ); ?></span>
							<?php endif; ?>
							<strong class="product-type-3-rating-value"><?php echo esc_html( number_format_i18n( $rating, 1 ) ); ?></strong>
						</div>
					<?php endif; ?>

					<?php if ( $args['show_student_count'] && $students > 0 ) : ?>
						<div class="product-type-3-students">
							<span class="product-type-3-students-icon <?php echo esc_attr( $args['students_icon_color_class'] ?? '' ); ?>">
								<?php
								if ( ! empty( $args['students_icon_html'] ) ) {
									echo $args['students_icon_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
							</span>
							<?php if ( '' !== $args['students_label'] ) : ?>
								<span class="product-type-3-students-label"><?php echo esc_html( $args['students_label'] ); ?></span>
							<?php endif; ?>
							<span class="product-type-3-students-count">
								<?php
								echo esc_html(
									number_format_i18n( $students ) . ' ' . $args['students_suffix']
								);
								?>
							</span>
						</div>
					<?php endif; ?>

					<?php if ( $args['show_reviews'] && $ratings_count > 0 ) : ?>
						<div class="product-type-3-reviews">
							<span class="product-type-3-reviews-icon <?php echo esc_attr( $args['reviews_icon_color_class'] ?? '' ); ?>">
								<?php
								if ( ! empty( $args['reviews_icon_html'] ) ) {
									echo $args['reviews_icon_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
							</span>
							<?php if ( '' !== $args['reviews_label'] ) : ?>
								<span class="product-type-3-reviews-label"><?php echo esc_html( $args['reviews_label'] ); ?></span>
							<?php endif; ?>
							<span class="product-type-3-reviews-count"><?php echo esc_html( number_format_i18n( $ratings_count ) ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $show_meta ) : ?>
				<div class="product-type-3-meta-row">
					<?php if ( $args['show_sessions_meta'] && $course_meta['sessions'] > 0 ) : ?>
						<div class="product-type-3-meta-item">
							<span class="product-type-3-meta-icon <?php echo esc_attr( $args['sessions_icon_color_class'] ?? '' ); ?>">
								<?php
								if ( ! empty( $args['sessions_icon_html'] ) ) {
									echo $args['sessions_icon_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
							</span>
							<span class="product-type-3-meta-text">
								<?php if ( '' !== $args['sessions_label'] ) : ?>
									<span class="product-type-3-meta-label"><?php echo esc_html( $args['sessions_label'] ); ?></span>
								<?php endif; ?>
								<?php
								echo esc_html(
									number_format_i18n( $course_meta['sessions'] ) . ' ' . $args['sessions_suffix']
								);
								?>
							</span>
						</div>
					<?php endif; ?>

					<?php if ( $args['show_duration_meta'] && '' !== $course_meta['duration'] ) : ?>
						<div class="product-type-3-meta-item">
							<span class="product-type-3-meta-icon <?php echo esc_attr( $args['duration_icon_color_class'] ?? '' ); ?>">
								<?php
								if ( ! empty( $args['duration_icon_html'] ) ) {
									echo $args['duration_icon_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
							</span>
							<span class="product-type-3-meta-text">
								<?php if ( '' !== $args['duration_label'] ) : ?>
									<span class="product-type-3-meta-label"><?php echo esc_html( $args['duration_label'] ); ?></span>
								<?php endif; ?>
								<?php
								echo esc_html(
									function_exists( 'webmz_to_persian_digits' )
										? webmz_to_persian_digits( $course_meta['duration'] )
										: $course_meta['duration']
								);
								?>
							</span>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $args['show_price'] || $args['show_button'] ) : ?>
		<footer class="product-type-3-footer">
			<?php if ( $args['show_price'] ) : ?>
				<?php webmz_render_product_loop_price_v3( $price, $discount_pct, $price_labels ); ?>
			<?php endif; ?>
			<?php if ( $args['show_button'] ) : ?>
				<?php webmz_render_product_loop_view_button_v3( $product, $args['button_text'] ); ?>
			<?php endif; ?>
		</footer>
		<?php endif; ?>
	</article>
	<?php
}

/**
 * Use three columns on shop archives to match the Webmasters card layout.
 *
 * @param int $columns Default column count.
 * @return int
 */
function webmz_shop_loop_columns( $columns ) {
	if ( webmz_should_use_webmasters_product_loop_card() ) {
		return 3;
	}

	return $columns;
}
add_filter( 'loop_shop_columns', 'webmz_shop_loop_columns' );

/**
 * Replace default WooCommerce loop wrapper classes with the Webmasters grid.
 *
 * @param string $html Loop opening markup.
 * @return string
 */
function webmz_woocommerce_product_loop_start( $html ) {
	if ( ! webmz_should_use_webmasters_product_loop_card() ) {
		return $html;
	}

	$columns = absint( wc_get_loop_prop( 'columns', 3 ) );

	return sprintf(
		'<ul class="products columns-%1$d tadris-products-loop tadris-products-grid webmz-loop-grid webmz-shop-products-loop" style="--webmz-grid-columns:%1$d;--webmz-grid-tablet-columns:2;--webmz-grid-mobile-columns:1;">',
		$columns
	);
}
add_filter( 'woocommerce_product_loop_start', 'webmz_woocommerce_product_loop_start' );

/**
 * Enqueue assets required by Ajax add-to-cart buttons in the shop loop.
 *
 * @return void
 */
function webmz_enqueue_shop_product_loop_assets() {
	if ( ! webmz_should_use_webmasters_product_loop_card() ) {
		return;
	}

	if ( wp_script_is( 'webmz-tadris-widgets', 'registered' ) ) {
		wp_enqueue_script( 'webmz-tadris-widgets' );
	}

	if ( wp_script_is( 'webmz-header-commerce', 'registered' ) ) {
		wp_enqueue_style( 'webmz-header-commerce' );
		wp_enqueue_script( 'webmz-header-commerce' );
	}
}
add_action( 'wp_enqueue_scripts', 'webmz_enqueue_shop_product_loop_assets', 30 );

/**
 * Registered product loop card styles for widgets and AJAX handlers.
 *
 * Third-party code can register additional styles via the
 * `webmz_product_loop_styles` filter.
 *
 * @return array<string,array<string,mixed>>
 */
function webmz_get_product_loop_styles() {
	static $styles = null;

	if ( null !== $styles ) {
		return $styles;
	}

	$styles = array(
		'1' => array(
			'label'           => esc_html__( 'لوپ دوره آکام 1', 'tadris' ),
			'render_callback' => 'webmz_render_product_loop_card',
			'style_depends'   => array( 'webmz-swiper' ),
			'grid_class'      => 'tadris-products-loop tadris-products-grid webmz-loop-grid',
			'slider_class'    => 'tadris-products-loop swiper tadris-products-swiper',
		),
		'2' => array(
			'label'           => esc_html__( 'لوپ دوره آکام 2', 'tadris' ),
			'render_callback' => 'webmz_render_product_loop_card_v2',
			'style_depends'   => array( 'webmz-tadris-product-loop-2' ),
			'grid_class'      => 'tadris-products-loop-2 tadris-products-loop-2-grid webmz-loop-grid',
			'slider_class'    => 'tadris-products-loop-2 tadris-products-loop-2-swiper swiper',
			'slider_data'     => 'data-tadris-product-loop-2-swiper',
		),
		'3' => array(
			'label'           => esc_html__( 'لوپ دوره آکام 3', 'tadris' ),
			'render_callback' => 'webmz_render_product_loop_card_v3',
			'style_depends'   => array( 'webmz-tadris-product-loop-3' ),
			'grid_class'      => 'tadris-products-loop-3 tadris-products-loop-3-grid webmz-loop-grid',
			'slider_class'    => 'tadris-products-loop-3 tadris-products-loop-3-swiper swiper',
			'slider_data'     => 'data-tadris-product-loop-3-swiper',
		),
	);

	/**
	 * Filter registered product loop card styles.
	 *
	 * @param array<string,array<string,mixed>> $styles Style registry.
	 */
	$styles = apply_filters( 'webmz_product_loop_styles', $styles );

	return is_array( $styles ) ? $styles : array();
}

/**
 * Elementor SELECT options for product loop styles.
 *
 * @return array<string,string>
 */
function webmz_get_product_loop_style_options() {
	$options = array();

	foreach ( webmz_get_product_loop_styles() as $style_id => $style ) {
		$options[ (string) $style_id ] = isset( $style['label'] ) ? (string) $style['label'] : (string) $style_id;
	}

	return $options;
}

/**
 * Sanitize a product loop style key against the registry.
 *
 * @param string $style Style key.
 * @return string
 */
function webmz_sanitize_product_loop_style( $style ) {
	$style  = sanitize_key( (string) $style );
	$styles = webmz_get_product_loop_styles();

	return isset( $styles[ $style ] ) ? $style : (string) array_key_first( $styles );
}

/**
 * Render a product card using a registered loop style.
 *
 * @param \WC_Product          $product Product object.
 * @param string               $style   Loop style key.
 * @param array<string,mixed> $args    Card arguments.
 * @return void
 */
function webmz_render_product_loop_card_by_style( $product, $style, $args = array() ) {
	$styles   = webmz_get_product_loop_styles();
	$style    = webmz_sanitize_product_loop_style( $style );
	$callback = isset( $styles[ $style ]['render_callback'] ) ? $styles[ $style ]['render_callback'] : '';

	if ( ! $callback || ! is_callable( $callback ) ) {
		return;
	}

	call_user_func( $callback, $product, $args );
}

/**
 * Build WP_Query args for tabbed product loop tabs.
 *
 * @param array<string,mixed> $query_settings Shared query settings.
 * @param array<string,mixed> $tab            Tab configuration.
 * @return array<string,mixed>
 */
function webmz_tabbed_product_loop_build_query_args( $query_settings, $tab ) {
	$args = array(
		'post_type'              => 'product',
		'post_status'            => 'publish',
		'posts_per_page'         => min( 24, max( 1, absint( $query_settings['count'] ?? 4 ) ) ),
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'update_post_meta_cache' => true,
	);

	$filter_type = isset( $tab['filter_type'] ) ? sanitize_key( $tab['filter_type'] ) : 'all';
	$tax_query   = array();

	if ( 'category' === $filter_type && ! empty( $tab['category'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => sanitize_title( (string) $tab['category'] ),
		);
	} elseif ( 'tag' === $filter_type && ! empty( $tab['tag'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_tag',
			'field'    => 'slug',
			'terms'    => sanitize_title( (string) $tab['tag'] ),
		);
	}

	if ( ! empty( $tax_query ) ) {
		$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}

	$order_by = isset( $query_settings['order_by'] ) ? sanitize_key( $query_settings['order_by'] ) : 'date';

	switch ( $order_by ) {
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
 * Render tabbed product loop panel markup.
 *
 * @param array<string,mixed> $config    Widget/AJAX config.
 * @param int                 $tab_index Active tab index.
 * @return string
 */
function webmz_tabbed_product_loop_render_panel_html( $config, $tab_index ) {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return '';
	}

	$config    = is_array( $config ) ? $config : array();
	$tab_index = max( 0, absint( $tab_index ) );
	$tabs      = isset( $config['tabs'] ) && is_array( $config['tabs'] ) ? array_values( $config['tabs'] ) : array();

	if ( empty( $tabs[ $tab_index ] ) ) {
		return '<div class="webmz-tpl__empty">' . esc_html__( 'تب انتخاب‌شده یافت نشد.', 'tadris' ) . '</div>';
	}

	$tab         = $tabs[ $tab_index ];
	$loop_style  = webmz_sanitize_product_loop_style( $config['loopStyle'] ?? '1' );
	$styles      = webmz_get_product_loop_styles();
	$style_meta  = $styles[ $loop_style ] ?? array();
	$query       = new WP_Query( webmz_tabbed_product_loop_build_query_args( $config, $tab ) );
	$card_args   = isset( $config['cardArgs'] ) && is_array( $config['cardArgs'] ) ? $config['cardArgs'] : array();
	$display     = isset( $config['displayType'] ) && 'slider' === $config['displayType'] ? 'slider' : 'grid';
	$gap         = isset( $config['gridGap'] ) ? absint( $config['gridGap'] ) : 20;
	$empty_html  = '<div class="webmz-tpl__empty">' . esc_html__( 'محصولی برای نمایش یافت نشد.', 'tadris' ) . '</div>';

	if ( ! $query->have_posts() ) {
		wp_reset_postdata();
		return $empty_html;
	}

	ob_start();

	if ( 'slider' === $display ) {
		$slider_conf = array(
			'slidesDesktop' => absint( $config['slidesDesktop'] ?? 4 ),
			'slidesTablet'  => absint( $config['slidesTablet'] ?? 2 ),
			'slidesMobile'  => absint( $config['slidesMobile'] ?? 1 ),
			'spaceBetween'  => $gap,
			'loop'          => ! empty( $config['sliderLoop'] ),
			'autoplay'      => ! empty( $config['sliderAutoplay'] ),
			'autoplayDelay' => absint( $config['sliderAutoplayDelay'] ?? 3500 ),
			'navigation'    => ! empty( $config['sliderNavigation'] ),
			'pagination'    => ! isset( $config['sliderPagination'] ) || ! empty( $config['sliderPagination'] ),
		);

		$slider_class = isset( $style_meta['slider_class'] ) ? (string) $style_meta['slider_class'] : 'tadris-products-loop swiper tadris-products-swiper';
		$gap_var      = '3' === $loop_style ? '--tpl3-gap' : ( '2' === $loop_style ? '--tpl2-gap' : '' );
		?>
		<div
			class="<?php echo esc_attr( $slider_class ); ?>"
			<?php if ( '1' === $loop_style ) : ?>
				data-slides-desktop="<?php echo esc_attr( absint( $config['slidesDesktop'] ?? 4 ) ); ?>"
			<?php elseif ( '2' === $loop_style ) : ?>
				data-tadris-product-loop-2-swiper="<?php echo esc_attr( wp_json_encode( $slider_conf ) ); ?>"
			<?php elseif ( '3' === $loop_style ) : ?>
				data-tadris-product-loop-3-swiper="<?php echo esc_attr( wp_json_encode( $slider_conf ) ); ?>"
			<?php else : ?>
				<?php
				$data_attr = isset( $style_meta['slider_data'] ) ? (string) $style_meta['slider_data'] : '';
				if ( $data_attr ) {
					printf(
						'%1$s="%2$s"',
						esc_attr( $data_attr ),
						esc_attr( wp_json_encode( $slider_conf ) )
					);
				}
				?>
			<?php endif; ?>
			<?php if ( $gap_var ) : ?>
				style="<?php echo esc_attr( $gap_var ); ?>: <?php echo esc_attr( $gap ); ?>px;"
			<?php endif; ?>
		>
			<div class="swiper-wrapper">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					$product = wc_get_product( get_the_ID() );
					if ( ! $product ) {
						continue;
					}

					/**
					 * Hook: woocommerce_shop_loop.
					 */
					do_action( 'woocommerce_shop_loop' );
					?>
					<div class="swiper-slide">
						<?php
						if ( function_exists( 'webmz_woocommerce_render_with_loop_item_hooks' ) ) {
							webmz_woocommerce_render_with_loop_item_hooks(
								static function () use ( $product, $loop_style, $card_args ) {
									webmz_render_product_loop_card_by_style( $product, $loop_style, $card_args );
								}
							);
						} else {
							webmz_render_product_loop_card_by_style( $product, $loop_style, $card_args );
						}
						?>
					</div>
				<?php endwhile; ?>
			</div>
			<?php if ( '1' !== $loop_style && ! empty( $slider_conf['navigation'] ) ) : ?>
				<div class="tadris-products-loop-<?php echo esc_attr( $loop_style ); ?>-swiper-button tadris-products-loop-<?php echo esc_attr( $loop_style ); ?>-swiper-button-next swiper-button-next"></div>
				<div class="tadris-products-loop-<?php echo esc_attr( $loop_style ); ?>-swiper-button tadris-products-loop-<?php echo esc_attr( $loop_style ); ?>-swiper-button-prev swiper-button-prev"></div>
			<?php endif; ?>
			<?php if ( '1' !== $loop_style && ! empty( $slider_conf['pagination'] ) ) : ?>
				<div class="tadris-products-loop-<?php echo esc_attr( $loop_style ); ?>-swiper-pagination swiper-pagination"></div>
			<?php endif; ?>
		</div>
		<?php
	} else {
		$grid_class = isset( $style_meta['grid_class'] ) ? (string) $style_meta['grid_class'] : 'tadris-products-loop tadris-products-grid webmz-loop-grid';
		$gap_var    = '3' === $loop_style ? '--tpl3-gap' : ( '2' === $loop_style ? '--tpl2-gap' : '' );
		?>
		<div
			class="<?php echo esc_attr( $grid_class ); ?>"
			style="
				--webmz-grid-columns: <?php echo esc_attr( $config['gridColumnsDesktop'] ?? 4 ); ?>;
				--webmz-grid-tablet-columns: <?php echo esc_attr( $config['gridColumnsTablet'] ?? 2 ); ?>;
				--webmz-grid-mobile-columns: <?php echo esc_attr( $config['gridColumnsMobile'] ?? 1 ); ?>;
				<?php echo $gap_var ? esc_attr( $gap_var ) . ': ' . esc_attr( $gap ) . 'px;' : ''; ?>
			"
		>
			<?php
			while ( $query->have_posts() ) :
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
						static function () use ( $product, $loop_style, $card_args ) {
							webmz_render_product_loop_card_by_style( $product, $loop_style, $card_args );
						}
					);
				} else {
					webmz_render_product_loop_card_by_style( $product, $loop_style, $card_args );
				}
			endwhile;
			?>
		</div>
		<?php
	}

	wp_reset_postdata();

	return (string) ob_get_clean();
}
