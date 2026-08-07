<?php
/**
 * Dynamic WooCommerce widgets for WebMZ layouts.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Get the product represented by the current layout context.
 *
 * @return \WC_Product|false
 */
function webmz_widget_current_product() {
	$id = \webmz_get_context_post_id();
	return $id ? wc_get_product( $id ) : false;
}

class Product_Title_Widget extends Widget_Base {
	public function get_name() { return 'webmz-product-title'; }
	public function get_title() { return esc_html__( 'عنوان محصول', 'tadris' ); }
	public function get_icon() { return 'eicon-product-title'; }
	public function get_categories() { return array( 'webmz-shop-widgets' ); }
	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<h1 class="product_title entry-title webmz-product-title">' . esc_html__( 'عنوان داینامیک محصول', 'tadris' ) . '</h1>';
			return;
		}

		global $product;
		$previous = $product;
		$product  = webmz_widget_current_product();
		if ( $product ) {
			woocommerce_template_single_title();
		}
		$product = $previous;
	}
}

class Product_Price_Widget extends Widget_Base {
	public function get_name() { return 'webmz-product-price'; }
	public function get_title() { return esc_html__( 'قیمت محصول', 'tadris' ); }
	public function get_icon() { return 'eicon-product-price'; }
	public function get_categories() { return array( 'webmz-shop-widgets' ); }
	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<p class="price webmz-editor-placeholder">' . esc_html__( 'قیمت داینامیک محصول', 'tadris' ) . '</p>';
			return;
		}
		global $product;
		$previous = $product;
		$product  = webmz_widget_current_product();
		if ( $product ) {
			woocommerce_template_single_price();
		}
		$product = $previous;
	}
}

class Product_Rating_Widget extends Widget_Base {
	public function get_name() { return 'webmz-product-rating'; }
	public function get_title() { return esc_html__( 'امتیاز محصول', 'tadris' ); }
	public function get_icon() { return 'eicon-product-rating'; }
	public function get_categories() { return array( 'webmz-shop-widgets' ); }
	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<div class="webmz-editor-placeholder">★★★★★</div>';
			return;
		}
		global $product;
		$previous = $product;
		$product  = webmz_widget_current_product();
		if ( $product ) {
			woocommerce_template_single_rating();
		}
		$product = $previous;
	}
}

class Product_Gallery_Widget extends Widget_Base {
	public function get_name() { return 'webmz-product-gallery'; }
	public function get_title() { return esc_html__( 'گالری محصول', 'tadris' ); }
	public function get_icon() { return 'eicon-product-images'; }
	public function get_categories() { return array( 'webmz-shop-widgets' ); }
	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<div class="webmz-product-gallery webmz-editor-placeholder">' . esc_html__( 'گالری داینامیک محصول', 'tadris' ) . '</div>';
			return;
		}
		global $product;
		$previous = $product;
		$product  = webmz_widget_current_product();
		if ( $product ) {
			woocommerce_show_product_images();
		}
		$product = $previous;
	}
}

class Product_Short_Description_Widget extends Widget_Base {
	public function get_name() { return 'webmz-product-short-description'; }
	public function get_title() { return esc_html__( 'توضیح کوتاه محصول', 'tadris' ); }
	public function get_icon() { return 'eicon-product-description'; }
	public function get_categories() { return array( 'webmz-shop-widgets' ); }
	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'توضیح کوتاه محصول در این قسمت نمایش داده می‌شود.', 'tadris' ) . '</div>';
			return;
		}
		global $product;
		$previous = $product;
		$product  = webmz_widget_current_product();
		if ( $product ) {
			woocommerce_template_single_excerpt();
		}
		$product = $previous;
	}
}

class Product_Add_To_Cart_Widget extends Widget_Base {
	public function get_name() { return 'webmz-product-add-to-cart'; }
	public function get_title() { return esc_html__( 'افزودن به سبد خرید', 'tadris' ); }
	public function get_icon() { return 'eicon-product-add-to-cart'; }
	public function get_categories() { return array( 'webmz-shop-widgets' ); }
	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<button type="button" class="button webmz-editor-placeholder">' . esc_html__( 'افزودن به سبد خرید', 'tadris' ) . '</button>';
			return;
		}
		global $product;
		$previous = $product;
		$product  = webmz_widget_current_product();
		if ( $product ) {
			woocommerce_template_single_add_to_cart();
		}
		$product = $previous;
	}
}

class Product_Meta_Widget extends Widget_Base {
	public function get_name() { return 'webmz-product-meta'; }
	public function get_title() { return esc_html__( 'متای محصول', 'tadris' ); }
	public function get_icon() { return 'eicon-product-meta'; }
	public function get_categories() { return array( 'webmz-shop-widgets' ); }
	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'شناسه، دسته و برچسب محصول', 'tadris' ) . '</div>';
			return;
		}
		global $product;
		$previous = $product;
		$product  = webmz_widget_current_product();
		if ( $product ) {
			woocommerce_template_single_meta();
		}
		$product = $previous;
	}
}

class Product_Tabs_Widget extends Widget_Base {
	public function get_name() { return 'webmz-product-tabs'; }
	public function get_title() { return esc_html__( 'تب‌های محصول', 'tadris' ); }
	public function get_icon() { return 'eicon-product-tabs'; }
	public function get_categories() { return array( 'webmz-shop-widgets' ); }
	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'تب توضیحات و دیدگاه محصول', 'tadris' ) . '</div>';
			return;
		}
		global $product;
		$previous = $product;
		$product  = webmz_widget_current_product();
		if ( $product ) {
			woocommerce_output_product_data_tabs();
		}
		$product = $previous;
	}
}

class Related_Products_Widget extends Widget_Base {
	public function get_name() { return 'webmz-related-products'; }
	public function get_title() { return esc_html__( 'محصولات مرتبط', 'tadris' ); }
	public function get_icon() { return 'eicon-products'; }
	public function get_categories() { return array( 'webmz-shop-widgets' ); }
	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'محصولات مرتبط', 'tadris' ) . '</div>';
			return;
		}
		global $product;
		$previous = $product;
		$product  = webmz_widget_current_product();
		if ( $product ) {
			woocommerce_output_related_products();
		}
		$product = $previous;
	}
}

class Products_Result_Count_Widget extends Widget_Base {
	public function get_name() { return 'webmz-products-result-count'; }
	public function get_title() { return esc_html__( 'تعداد نتایج محصولات', 'tadris' ); }
	public function get_icon() { return 'eicon-counter'; }
	public function get_categories() { return array( 'webmz-shop-widgets' ); }
	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<p class="woocommerce-result-count webmz-editor-placeholder">' . esc_html__( 'نمایش تعداد نتایج فروشگاه', 'tadris' ) . '</p>';
			return;
		}
		woocommerce_result_count();
	}
}

class Products_Ordering_Widget extends Widget_Base {
	public function get_name() { return 'webmz-products-ordering'; }
	public function get_title() { return esc_html__( 'مرتب‌سازی محصولات', 'tadris' ); }
	public function get_icon() { return 'eicon-filter'; }
	public function get_categories() { return array( 'webmz-shop-widgets' ); }
	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'انتخاب مرتب‌سازی محصولات', 'tadris' ) . '</div>';
			return;
		}
		woocommerce_catalog_ordering();
	}
}

class Products_Loop_Widget extends Widget_Base {
	public function get_name() { return 'webmz-products-loop'; }
	public function get_title() { return esc_html__( 'حلقه آرشیو محصولات', 'tadris' ); }
	public function get_icon() { return 'eicon-products'; }
	public function get_categories() { return array( 'webmz-shop-widgets' ); }
	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<div class="products columns-4 webmz-editor-placeholder">' . esc_html__( 'لیست محصولات آرشیو در این قسمت نمایش داده می‌شود.', 'tadris' ) . '</div>';
			return;
		}
		if ( woocommerce_product_loop() ) {
			if ( function_exists( 'webmz_woocommerce_maybe_do_before_shop_loop' ) ) {
				webmz_woocommerce_maybe_do_before_shop_loop();
			} else {
				do_action( 'woocommerce_before_shop_loop' );
			}

			woocommerce_product_loop_start();

			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();

					/**
					 * Hook: woocommerce_shop_loop.
					 */
					do_action( 'woocommerce_shop_loop' );

					wc_get_template_part( 'content', 'product' );
				}
			}

			woocommerce_product_loop_end();

			if ( function_exists( 'webmz_woocommerce_do_after_shop_loop' ) ) {
				webmz_woocommerce_do_after_shop_loop( true );
			} else {
				do_action( 'woocommerce_after_shop_loop' );
			}
		} else {
			/**
			 * Hook: woocommerce_no_products_found.
			 */
			do_action( 'woocommerce_no_products_found' );
		}
		wp_reset_postdata();
	}
}
