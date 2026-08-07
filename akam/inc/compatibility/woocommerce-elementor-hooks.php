<?php
/**
 * WooCommerce template hooks for Elementor layouts and custom widgets.
 *
 * Elementor shop/single layouts replace native templates, so this file
 * re-fires the structural WooCommerce actions plugins rely on, while
 * stripping default WC UI callbacks that would duplicate Elementor widgets.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether an Elementor (or Elementor Pro) layout is assigned for a WC location.
 *
 * @param string $location Location key: single_product|archive_product.
 * @return bool
 */
function webmz_has_woocommerce_elementor_layout( $location = '' ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}

	$location = sanitize_key( $location );

	if ( '' === $location ) {
		if ( function_exists( 'is_product' ) && is_product() ) {
			$location = 'single_product';
		} elseif (
			function_exists( 'is_shop' )
			&& (
				is_shop()
				|| ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() )
			)
		) {
			$location = 'archive_product';
		} else {
			return false;
		}
	}

	if ( ! in_array( $location, array( 'single_product', 'archive_product' ), true ) ) {
		return false;
	}

	if (
		function_exists( 'webmz_elementor_pro_has_location_template' )
		&& webmz_elementor_pro_has_location_template( $location )
	) {
		return true;
	}

	return function_exists( 'webmz_get_assigned_layout_id' )
		&& (bool) webmz_get_assigned_layout_id( $location );
}

/**
 * Strip default WooCommerce template callbacks that conflict with Elementor layouts.
 *
 * @return void
 */
function webmz_prepare_woocommerce_elementor_hooks() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$is_single  = webmz_has_woocommerce_elementor_layout( 'single_product' );
	$is_archive = webmz_has_woocommerce_elementor_layout( 'archive_product' );

	if ( ! $is_single && ! $is_archive ) {
		return;
	}

	// Theme wrappers output another <main>; Elementor layouts already wrap in main.
	remove_action( 'woocommerce_before_main_content', 'webmz_woocommerce_wrapper_start', 10 );
	remove_action( 'woocommerce_after_main_content', 'webmz_woocommerce_wrapper_end', 10 );
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

	if ( $is_archive ) {
		remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header', 10 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	}

	if ( $is_single ) {
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
	}
}
add_action( 'wp', 'webmz_prepare_woocommerce_elementor_hooks', 20 );

/**
 * Remove default shop-loop-item template callbacks (for custom product cards).
 *
 * @return array<int,array{hook:string,callback:callable|string,priority:int}> Removed hooks.
 */
function webmz_woocommerce_remove_default_loop_item_actions() {
	$hooks = array(
		array( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 ),
		array( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 ),
		array( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 ),
		array( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 ),
		array( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 ),
		array( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 ),
		array( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 ),
		array( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 ),
	);

	$removed = array();

	foreach ( $hooks as $item ) {
		list( $hook, $callback, $priority ) = $item;

		if ( has_action( $hook, $callback ) ) {
			remove_action( $hook, $callback, $priority );
			$removed[] = array(
				'hook'     => $hook,
				'callback' => $callback,
				'priority' => $priority,
			);
		}
	}

	return $removed;
}

/**
 * Restore previously removed shop-loop-item callbacks.
 *
 * @param array<int,array{hook:string,callback:callable|string,priority:int}> $removed Removed hooks.
 * @return void
 */
function webmz_woocommerce_restore_default_loop_item_actions( $removed ) {
	foreach ( (array) $removed as $item ) {
		if ( empty( $item['hook'] ) || empty( $item['callback'] ) ) {
			continue;
		}

		add_action(
			$item['hook'],
			$item['callback'],
			isset( $item['priority'] ) ? (int) $item['priority'] : 10
		);
	}
}

/**
 * Render a custom product card inside the standard shop-loop-item hook chain.
 *
 * @param callable $callback Renders the card markup.
 * @return void
 */
function webmz_woocommerce_render_with_loop_item_hooks( $callback ) {
	if ( ! is_callable( $callback ) ) {
		return;
	}

	$removed = webmz_woocommerce_remove_default_loop_item_actions();

	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );

	call_user_func( $callback );

	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 */
	do_action( 'woocommerce_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item_title.
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 */
	do_action( 'woocommerce_after_shop_loop_item' );

	webmz_woocommerce_restore_default_loop_item_actions( $removed );
}

/**
 * Fire woocommerce_before_shop_loop once per request (notices, extensions).
 *
 * @return void
 */
function webmz_woocommerce_maybe_do_before_shop_loop() {
	static $done = false;

	if ( $done || ! function_exists( 'woocommerce_product_loop' ) ) {
		return;
	}

	$done = true;

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 */
	do_action( 'woocommerce_before_shop_loop' );
}

/**
 * Fire woocommerce_after_shop_loop, optionally without WC pagination.
 *
 * @param bool $with_pagination Whether to keep the default pagination callback.
 * @return void
 */
function webmz_woocommerce_do_after_shop_loop( $with_pagination = true ) {
	$removed = false;

	if ( ! $with_pagination && has_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination' ) ) {
		remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
		$removed = true;
	}

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );

	if ( $removed ) {
		add_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
	}
}

/**
 * Wrap Elementor WC layout HTML with the native WooCommerce shell hooks.
 *
 * @param string $location Location key.
 * @param string $content  Rendered Elementor HTML.
 * @return void
 */
function webmz_render_woocommerce_elementor_layout_content( $location, $content ) {
	$location   = sanitize_key( $location );
	$is_single  = ( 'single_product' === $location );
	$is_archive = ( 'archive_product' === $location );

	echo '<main id="primary" class="site-main webmz-layout-main webmz-woocommerce">';

	/**
	 * Hook: woocommerce_before_main_content.
	 */
	do_action( 'woocommerce_before_main_content' );

	if ( $is_archive ) {
		/**
		 * Hook: woocommerce_shop_loop_header.
		 */
		do_action( 'woocommerce_shop_loop_header' );
	}

	if ( $is_single && have_posts() ) {
		while ( have_posts() ) {
			the_post();

			global $product;

			if ( ! $product instanceof WC_Product ) {
				$product = wc_get_product( get_the_ID() );
			}

			/**
			 * Hook: woocommerce_before_single_product.
			 *
			 * @hooked woocommerce_output_all_notices - 10
			 */
			do_action( 'woocommerce_before_single_product' );

			if ( post_password_required() ) {
				echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				continue;
			}

			if ( ! $product ) {
				echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				continue;
			}
			?>
			<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'webmz-elementor-product', $product ); ?>>
				<?php
				/**
				 * Hook: woocommerce_before_single_product_summary.
				 * Default gallery/sale flash removed for Elementor layouts.
				 */
				do_action( 'woocommerce_before_single_product_summary' );

				if ( has_action( 'woocommerce_single_product_summary' ) ) :
					?>
					<div class="summary entry-summary webmz-elementor-product-summary-hooks">
						<?php
						/**
						 * Hook: woocommerce_single_product_summary.
						 * Default title/price/cart callbacks removed; third-party callbacks remain.
						 */
						do_action( 'woocommerce_single_product_summary' );
						?>
					</div>
					<?php
				endif;

				echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				/**
				 * Hook: woocommerce_after_single_product_summary.
				 * Default tabs/upsells/related removed for Elementor layouts.
				 */
				do_action( 'woocommerce_after_single_product_summary' );
				?>
			</div>
			<?php

			/**
			 * Hook: woocommerce_after_single_product.
			 */
			do_action( 'woocommerce_after_single_product' );
		}
	} else {
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Hook: woocommerce_after_main_content.
	 */
	do_action( 'woocommerce_after_main_content' );

	/**
	 * Hook: woocommerce_sidebar.
	 * Default sidebar removed for Elementor layouts.
	 */
	do_action( 'woocommerce_sidebar' );

	echo '</main>';
}

/**
 * Fire woocommerce_no_products_found without the default WC notice markup.
 *
 * Useful when a custom empty state is already rendered.
 *
 * @return void
 */
function webmz_woocommerce_do_no_products_found_hooks_only() {
	$had_default = has_action( 'woocommerce_no_products_found', 'wc_no_products_found' );

	if ( $had_default ) {
		remove_action( 'woocommerce_no_products_found', 'wc_no_products_found', 10 );
	}

	/**
	 * Hook: woocommerce_no_products_found.
	 */
	do_action( 'woocommerce_no_products_found' );

	if ( $had_default ) {
		add_action( 'woocommerce_no_products_found', 'wc_no_products_found', 10 );
	}
}

/**
 * Open add-to-cart form hooks for custom (non-template) buttons.
 *
 * @return void
 */
function webmz_woocommerce_do_before_add_to_cart_hooks() {
	/**
	 * Hook: woocommerce_before_add_to_cart_form.
	 */
	do_action( 'woocommerce_before_add_to_cart_form' );

	/**
	 * Hook: woocommerce_before_add_to_cart_button.
	 */
	do_action( 'woocommerce_before_add_to_cart_button' );
}

/**
 * Close add-to-cart form hooks for custom (non-template) buttons.
 *
 * @return void
 */
function webmz_woocommerce_do_after_add_to_cart_hooks() {
	/**
	 * Hook: woocommerce_after_add_to_cart_button.
	 */
	do_action( 'woocommerce_after_add_to_cart_button' );

	/**
	 * Hook: woocommerce_after_add_to_cart_form.
	 */
	do_action( 'woocommerce_after_add_to_cart_form' );
}
