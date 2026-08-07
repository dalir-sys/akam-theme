<?php
/**
 * Zhaket product tabs — shared renderers and AJAX handlers.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sanitize zhaket product tabs config from frontend.
 *
 * @param array<string,mixed> $config Raw config.
 * @return array<string,mixed>
 */
function webmz_zhaket_product_tabs_sanitize_config( $config ) {
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
				$card_args[ sanitize_key( (string) $key ) ] = is_string( $value ) ? sanitize_text_field( $value ) : $value;
			}
		}
	}

	$nav_icon_prev = isset( $config['navIconPrevHtml'] ) ? wp_kses_post( (string) $config['navIconPrevHtml'] ) : '';
	$nav_icon_next = isset( $config['navIconNextHtml'] ) ? wp_kses_post( (string) $config['navIconNextHtml'] ) : '';

	return array(
		'displayType'          => isset( $config['displayType'] ) && 'grid' === $config['displayType'] ? 'grid' : 'slider',
		'count'                => min( 24, max( 1, absint( $config['count'] ?? 8 ) ) ),
		'order_by'             => in_array( $config['order_by'] ?? 'date', array( 'date', 'popularity', 'rating', 'rand' ), true ) ? sanitize_key( $config['order_by'] ) : 'date',
		'gridColumnsDesktop'   => min( 6, max( 1, absint( $config['gridColumnsDesktop'] ?? 4 ) ) ),
		'gridColumnsTablet'    => min( 4, max( 1, absint( $config['gridColumnsTablet'] ?? 2 ) ) ),
		'gridColumnsMobile'    => min( 2, max( 1, absint( $config['gridColumnsMobile'] ?? 1 ) ) ),
		'slidesDesktop'        => min( 6, max( 1, absint( $config['slidesDesktop'] ?? 4 ) ) ),
		'slidesTablet'         => min( 6, max( 1, absint( $config['slidesTablet'] ?? 3 ) ) ),
		'slidesMobile'         => min( 6, max( 1, absint( $config['slidesMobile'] ?? 1 ) ) ),
		'spaceBetween'         => absint( $config['spaceBetween'] ?? 20 ),
		'sliderLoop'           => ! empty( $config['sliderLoop'] ),
		'sliderAutoplay'       => ! empty( $config['sliderAutoplay'] ),
		'sliderAutoplayDelay'  => absint( $config['sliderAutoplayDelay'] ?? 4000 ),
		'sliderNavigation'     => ! isset( $config['sliderNavigation'] ) || ! empty( $config['sliderNavigation'] ),
		'sliderPagination'     => ! isset( $config['sliderPagination'] ) || ! empty( $config['sliderPagination'] ),
		'navIconPrevHtml'      => $nav_icon_prev,
		'navIconNextHtml'      => $nav_icon_next,
		'tabs'                 => $tabs,
		'cardArgs'             => $card_args,
	);
}

/**
 * Render slider navigation icon markup.
 *
 * @param array<string,mixed> $config    Widget/AJAX config.
 * @param string              $direction prev|next.
 * @return string
 */
function webmz_zhaket_product_tabs_get_nav_icon_html( $config, $direction ) {
	$key = 'prev' === $direction ? 'navIconPrevHtml' : 'navIconNextHtml';

	if ( ! empty( $config[ $key ] ) && is_string( $config[ $key ] ) ) {
		return $config[ $key ];
	}

	if ( 'prev' === $direction ) {
		return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	}

	return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

/**
 * Get vendor label for zhaket product card.
 *
 * @param \WC_Product $product Product.
 * @param array<string,mixed> $args Card args.
 * @return string
 */
function webmz_zhaket_product_tabs_get_vendor_name( $product, $args = array() ) {
	$source = isset( $args['vendor_source'] ) ? sanitize_key( $args['vendor_source'] ) : 'badge';

	if ( 'author' === $source ) {
		$author_id = (int) get_post_field( 'post_author', $product->get_id() );

		return $author_id ? (string) get_the_author_meta( 'display_name', $author_id ) : '';
	}

	$fallback = isset( $args['vendor_fallback'] ) ? (string) $args['vendor_fallback'] : '';

	if ( function_exists( 'webmz_pll_get_product_badge_text' ) ) {
		return webmz_pll_get_product_badge_text( $product->get_id(), $fallback );
	}

	return $fallback;
}

/**
 * Get vendor icon URL for zhaket product card.
 *
 * @param \WC_Product $product Product.
 * @param array<string,mixed> $args Card args.
 * @return string
 */
function webmz_zhaket_product_tabs_get_vendor_icon_url( $product, $args = array() ) {
	$source = isset( $args['vendor_icon_source'] ) ? sanitize_key( $args['vendor_icon_source'] ) : 'loop_thumb';

	if ( 'author' === $source ) {
		$author_id = (int) get_post_field( 'post_author', $product->get_id() );

		if ( $author_id && function_exists( 'get_avatar_url' ) ) {
			return (string) get_avatar_url( $author_id, array( 'size' => 64 ) );
		}

		return '';
	}

	if ( function_exists( 'webmz_pll_get_product_thumb_url' ) ) {
		return webmz_pll_get_product_thumb_url( $product->get_id(), 'thumbnail' );
	}

	$thumb_id = get_post_thumbnail_id( $product->get_id() );

	return $thumb_id ? (string) wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '';
}

/**
 * Render price markup for zhaket product card.
 *
 * @param \WC_Product $product Product.
 * @param array<string,mixed> $args Card args.
 * @return string
 */
function webmz_zhaket_product_tabs_render_price_html( $product, $args = array() ) {
	if ( ! function_exists( 'webmz_get_product_loop_price_data' ) ) {
		return '';
	}

	$price = webmz_get_product_loop_price_data( $product );

	if ( 'unavailable' === $price['state'] ) {
		return '<span class="webmz-zpt__price webmz-zpt__price--muted">' . esc_html( $args['unavailable_text'] ?? esc_html__( 'ناموجود', 'tadris' ) ) . '</span>';
	}

	if ( 'free' === $price['state'] ) {
		return '<span class="webmz-zpt__price">' . esc_html( $args['free_text'] ?? esc_html__( 'رایگان', 'tadris' ) ) . '</span>';
	}

	$html = '<span class="webmz-zpt__price webmz-zpt__price--current">' . esc_html( $price['amount'] ) . ' <span class="webmz-zpt__currency">' . esc_html( $price['currency'] ) . '</span></span>';

	if ( ! empty( $price['regular_amount'] ) ) {
		$html .= '<span class="webmz-zpt__price-regular">' . esc_html( $price['regular_amount'] ) . '</span>';
	}

	return $html;
}

/**
 * Render one zhaket product card.
 *
 * @param \WC_Product          $product      Product.
 * @param array<string,mixed> $args         Card args.
 * @param string               $display_type Display mode: slider|grid.
 * @return string
 */
function webmz_zhaket_product_tabs_render_product_card( $product, $args = array(), $display_type = 'slider' ) {
	if ( ! $product || ! $product->is_visible() ) {
		return '';
	}

	$args = wp_parse_args(
		is_array( $args ) ? $args : array(),
		array(
			'add_to_cart_text'    => esc_html__( 'افزودن به سبد خرید', 'tadris' ),
			'preview_text'        => esc_html__( 'پیشنمایش', 'tadris' ),
			'unavailable_text'    => esc_html__( 'ناموجود', 'tadris' ),
			'variable_text'       => esc_html__( 'انتخاب گزینه‌ها', 'tadris' ),
			'free_text'           => esc_html__( 'رایگان', 'tadris' ),
			'show_vendor'         => true,
			'vendor_source'       => 'badge',
			'vendor_icon_source'  => 'loop_thumb',
			'vendor_fallback'     => '',
			'title_tag'           => 'h3',
		)
	);

	$product_id   = $product->get_id();
	$title        = $product->get_name();
	$title_tag    = tag_escape( $args['title_tag'] );
	$image_id     = $product->get_image_id();
	$image_html   = '';

	if ( $image_id ) {
		$image_html = webmz_get_attachment_loop_image(
			$image_id,
			array(
				'class' => 'webmz-zpt__card-img',
				'alt'   => $title,
			)
		);
	} else {
		$placeholder = function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( 'woocommerce_single' ) : '';

		if ( $placeholder ) {
			$image_html = sprintf(
				'<img class="webmz-zpt__card-img" src="%s" alt="%s" loading="lazy" decoding="async">',
				esc_url( $placeholder ),
				esc_attr( $title )
			);
		}
	}

	$vendor_name  = $args['show_vendor'] ? webmz_zhaket_product_tabs_get_vendor_name( $product, $args ) : '';
	$vendor_icon  = $args['show_vendor'] ? webmz_zhaket_product_tabs_get_vendor_icon_url( $product, $args ) : '';
	$price_html   = webmz_zhaket_product_tabs_render_price_html( $product, $args );
	$preview_url  = function_exists( 'webmz_get_product_preview_url' ) ? webmz_get_product_preview_url( $product_id ) : '';
	$product_url  = $product->get_permalink();
	$can_purchase = $product->is_purchasable() && $product->is_in_stock();
	$is_variable  = $product->is_type( 'variable' );
	$is_simple    = $product->is_type( 'simple' );

	$wrap_class = 'webmz-zpt__card-wrap';

	if ( 'grid' !== $display_type ) {
		$wrap_class .= ' swiper-slide';
	}

	ob_start();
	?>
	<div class="<?php echo esc_attr( $wrap_class ); ?>">
		<article class="webmz-zpt__card" data-product-id="<?php echo esc_attr( $product_id ); ?>">
			<a class="webmz-zpt__card-media-link" href="<?php echo esc_url( $product_url ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
				<div class="webmz-zpt__card-media">
					<?php if ( $image_html ) : ?>
						<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>
					<div class="webmz-zpt__card-overlay" aria-hidden="true"></div>
				</div>
			</a>

			<div class="webmz-zpt__card-content">
				<<?php echo esc_html( $title_tag ); ?> class="webmz-zpt__card-title">
					<a class="webmz-zpt__card-title-link" href="<?php echo esc_url( $product_url ); ?>"><?php echo esc_html( $title ); ?></a>
				</<?php echo esc_html( $title_tag ); ?>>

				<div class="webmz-zpt__card-footer">
					<?php if ( $price_html ) : ?>
						<div class="webmz-zpt__card-price-wrap">
							<?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>

					<?php if ( $vendor_name ) : ?>
						<div class="webmz-zpt__card-vendor">
							<?php if ( $vendor_icon ) : ?>
								<img class="webmz-zpt__card-vendor-icon" src="<?php echo esc_url( $vendor_icon ); ?>" alt="" loading="lazy" decoding="async">
							<?php endif; ?>
							<span class="webmz-zpt__card-vendor-name"><?php echo esc_html( $vendor_name ); ?></span>
						</div>
					<?php endif; ?>
				</div>

				<div class="webmz-zpt__card-actions">
					<?php if ( ! $can_purchase ) : ?>
						<span class="webmz-zpt__btn webmz-zpt__btn--cart is-disabled" aria-disabled="true">
							<?php echo esc_html( $args['unavailable_text'] ); ?>
						</span>
					<?php elseif ( $is_variable ) : ?>
						<a class="webmz-zpt__btn webmz-zpt__btn--cart is-variable" href="<?php echo esc_url( $product_url ); ?>">
							<?php echo esc_html( $args['variable_text'] ); ?>
						</a>
					<?php elseif ( $is_simple ) : ?>
						<button
							type="button"
							class="webmz-zpt__btn webmz-zpt__btn--cart"
							data-webmz-zpt-add-to-cart
							data-product-id="<?php echo esc_attr( $product_id ); ?>"
							data-quantity="1"
						>
							<span class="webmz-zpt__btn-label"><?php echo esc_html( $args['add_to_cart_text'] ); ?></span>
							<span class="webmz-zpt__btn-spinner" aria-hidden="true" hidden></span>
						</button>
					<?php else : ?>
						<a class="webmz-zpt__btn webmz-zpt__btn--cart" href="<?php echo esc_url( $product_url ); ?>">
							<?php echo esc_html( $args['add_to_cart_text'] ); ?>
						</a>
					<?php endif; ?>

					<?php if ( $preview_url ) : ?>
						<a class="webmz-zpt__btn webmz-zpt__btn--preview" href="<?php echo esc_url( $preview_url ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html( $args['preview_text'] ); ?>
						</a>
					<?php else : ?>
						<a class="webmz-zpt__btn webmz-zpt__btn--preview" href="<?php echo esc_url( $product_url ); ?>">
							<?php echo esc_html( $args['preview_text'] ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</article>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render zhaket product tabs slider panel HTML.
 *
 * @param array<string,mixed> $config    Widget/AJAX config.
 * @param int                 $tab_index Active tab index.
 * @return string
 */
function webmz_zhaket_product_tabs_render_panel_html( $config, $tab_index ) {
	if ( ! function_exists( 'wc_get_product' ) || ! function_exists( 'webmz_tabbed_product_loop_build_query_args' ) ) {
		return '';
	}

	$config    = webmz_zhaket_product_tabs_sanitize_config( $config );
	$tab_index = max( 0, absint( $tab_index ) );
	$tabs      = $config['tabs'] ?? array();

	if ( empty( $tabs[ $tab_index ] ) ) {
		return '<div class="webmz-zpt__empty">' . esc_html__( 'تب انتخاب‌شده یافت نشد.', 'tadris' ) . '</div>';
	}

	$query = new WP_Query( webmz_tabbed_product_loop_build_query_args( $config, $tabs[ $tab_index ] ) );

	if ( ! $query->have_posts() ) {
		wp_reset_postdata();
		return '<div class="webmz-zpt__empty">' . esc_html__( 'محصولی برای نمایش یافت نشد.', 'tadris' ) . '</div>';
	}

	$card_args    = isset( $config['cardArgs'] ) && is_array( $config['cardArgs'] ) ? $config['cardArgs'] : array();
	$display_type = isset( $config['displayType'] ) && 'grid' === $config['displayType'] ? 'grid' : 'slider';

	ob_start();

	if ( 'grid' === $display_type ) {
		$grid_style = sprintf(
			'--webmz-zpt-cols-mobile:%1$d;--webmz-zpt-cols-tablet:%2$d;--webmz-zpt-cols-desktop:%3$d;',
			absint( $config['gridColumnsMobile'] ?? 1 ),
			absint( $config['gridColumnsTablet'] ?? 2 ),
			absint( $config['gridColumnsDesktop'] ?? 4 )
		);
		?>
		<div class="webmz-zpt__grid" style="<?php echo esc_attr( $grid_style ); ?>">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				$product = wc_get_product( get_the_ID() );

				if ( ! $product ) {
					continue;
				}

				echo webmz_zhaket_product_tabs_render_product_card( $product, $card_args, 'grid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			endwhile;
			?>
		</div>
		<?php
	} else {
		$slider_conf = array(
			'slidesDesktop' => absint( $config['slidesDesktop'] ?? 4 ),
			'slidesTablet'  => absint( $config['slidesTablet'] ?? 3 ),
			'slidesMobile'  => absint( $config['slidesMobile'] ?? 1 ),
			'spaceBetween'  => absint( $config['spaceBetween'] ?? 20 ),
			'loop'          => ! empty( $config['sliderLoop'] ),
			'autoplay'      => ! empty( $config['sliderAutoplay'] ),
			'autoplayDelay' => absint( $config['sliderAutoplayDelay'] ?? 4000 ),
			'navigation'    => ! isset( $config['sliderNavigation'] ) || ! empty( $config['sliderNavigation'] ),
			'pagination'    => ! isset( $config['sliderPagination'] ) || ! empty( $config['sliderPagination'] ),
		);
		?>
		<div class="webmz-zpt__track-wrap webmz-zpt__track-wrap--slider">
			<div class="webmz-zpt__slider-viewport">
				<div class="webmz-zpt__slider swiper" data-webmz-zpt-slider="<?php echo esc_attr( wp_json_encode( $slider_conf ) ); ?>">
					<div class="swiper-wrapper">
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							$product = wc_get_product( get_the_ID() );

							if ( ! $product ) {
								continue;
							}

							echo webmz_zhaket_product_tabs_render_product_card( $product, $card_args, 'slider' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						endwhile;
						?>
					</div>
				</div>
			</div>

			<?php if ( $slider_conf['navigation'] || $slider_conf['pagination'] ) : ?>
				<div class="webmz-zpt__slider-footer">
					<?php if ( $slider_conf['navigation'] ) : ?>
						<button type="button" class="webmz-zpt__nav webmz-zpt__nav--prev" aria-label="<?php esc_attr_e( 'اسلاید قبلی', 'tadris' ); ?>">
							<span class="webmz-zpt__nav-icon" aria-hidden="true"><?php echo webmz_zhaket_product_tabs_get_nav_icon_html( $config, 'prev' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</button>
					<?php endif; ?>

					<?php if ( $slider_conf['pagination'] ) : ?>
						<div class="webmz-zpt__pagination swiper-pagination"></div>
					<?php endif; ?>

					<?php if ( $slider_conf['navigation'] ) : ?>
						<button type="button" class="webmz-zpt__nav webmz-zpt__nav--next" aria-label="<?php esc_attr_e( 'اسلاید بعدی', 'tadris' ); ?>">
							<span class="webmz-zpt__nav-icon" aria-hidden="true"><?php echo webmz_zhaket_product_tabs_get_nav_icon_html( $config, 'next' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</button>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	wp_reset_postdata();

	return (string) ob_get_clean();
}

/**
 * Load zhaket product tabs panel via AJAX.
 *
 * @return void
 */
function webmz_zhaket_product_tabs_ajax_load() {
	check_ajax_referer( 'webmz_zhaket_product_tabs', 'nonce' );

	if ( ! function_exists( 'wc_get_product' ) || ! function_exists( 'webmz_zhaket_product_tabs_render_panel_html' ) ) {
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

	$config = webmz_zhaket_product_tabs_sanitize_config( $config );

	if ( empty( $config['tabs'] ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'تب‌ای برای نمایش تعریف نشده است.', 'tadris' ),
			)
		);
	}

	$html = webmz_zhaket_product_tabs_render_panel_html( $config, $tab_index );

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
add_action( 'wp_ajax_webmz_zhaket_product_tabs_load', 'webmz_zhaket_product_tabs_ajax_load' );
add_action( 'wp_ajax_nopriv_webmz_zhaket_product_tabs_load', 'webmz_zhaket_product_tabs_ajax_load' );
