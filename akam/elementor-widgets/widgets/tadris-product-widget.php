<?php
/**
 * Custom course-product list widget using the requested HTML structure.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * Custom WooCommerce course/product cards.
 */
class Tadris_Product_Loop_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-tadris-product-loop';
	}

	public function get_title() {
		return esc_html__( 'لوپ دوره‌ها آکام', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_categories() {
		return array( 'webmz-shop-widgets' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-tadris-widgets', 'webmz-header-commerce', 'webmz-mobile-offcanvas' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper' );
	}

	/**
	 * Product category control options.
	 *
	 * @return array<string,string>
	 */
	private function category_options() {
		$options = array(
			'' => esc_html__( 'همه دسته‌ها', 'tadris' ),
		);

		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}

		return $options;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'query',
			array(
				'label' => esc_html__( 'کوئری محصولات', 'tadris' ),
			)
		);

		$this->add_control(
			'category',
			array(
				'label'   => esc_html__( 'دسته محصول', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->category_options(),
				'default' => '',
			)
		);

		$this->add_control(
			'count',
			array(
				'label'   => esc_html__( 'تعداد محصولات', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4,
				'min'     => 1,
				'max'     => 24,
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'   => esc_html__( 'مرتب‌سازی', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'       => esc_html__( 'جدیدترین', 'tadris' ),
					'popularity' => esc_html__( 'پرفروش‌ترین', 'tadris' ),
					'rating'     => esc_html__( 'بالاترین امتیاز', 'tadris' ),
					'rand'       => esc_html__( 'تصادفی', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'display_type',
			array(
				'label'   => esc_html__( 'نحوه نمایش', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid'   => esc_html__( 'گرید', 'tadris' ),
					'slider' => esc_html__( 'اسلایدی', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'slides_desktop',
			array(
				'label'     => esc_html__( 'تعداد اسلاید دسکتاپ', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3,
				'min'       => 1,
				'max'       => 6,
				'condition' => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'grid_columns_heading',
			array(
				'label'     => esc_html__( 'تنظیمات گرید', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'display_type' => 'grid',
				),
			)
		);

		$this->add_control(
			'grid_columns_desktop',
			array(
				'label'     => esc_html__( 'تعداد ستون دسکتاپ', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '3',
				'options'   => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors' => array(
					'{{WRAPPER}} .tadris-products-grid' => '--webmz-grid-columns: {{VALUE}};',
				),
				'condition' => array(
					'display_type' => 'grid',
				),
			)
		);

		$this->add_control(
			'grid_columns_tablet',
			array(
				'label'     => esc_html__( 'تعداد ستون تبلت', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '2',
				'options'   => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors' => array(
					'{{WRAPPER}} .tadris-products-grid' => '--webmz-grid-tablet-columns: {{VALUE}};',
				),
				'condition' => array(
					'display_type' => 'grid',
				),
			)
		);

		$this->add_control(
			'grid_columns_mobile',
			array(
				'label'     => esc_html__( 'تعداد ستون موبایل', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '1',
				'options'   => array(
					'1' => '1',
					'2' => '2',
				),
				'selectors' => array(
					'{{WRAPPER}} .tadris-products-grid' => '--webmz-grid-mobile-columns: {{VALUE}};',
				),
				'condition' => array(
					'display_type' => 'grid',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'فاصله بین آیتم‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .tadris-products-grid' => '--webmz-grid-gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'display_type' => 'grid',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_product_loop_card_1_visibility_controls();

		$this->start_controls_section(
			'labels',
			array(
				'label' => esc_html__( 'متن و آیکون‌ها', 'tadris' ),
			)
		);

		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ HTML عنوان محصول', 'tadris' ) );

		$this->add_control(
			'rating_label',
			array(
				'label'   => esc_html__( 'عنوان امتیاز', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'امتیاز دانشجویان', 'tadris' ),
			)
		);

		$this->add_control(
			'sessions_label',
			array(
				'label'   => esc_html__( 'عنوان جلسات', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'تعداد جلسات', 'tadris' ),
			)
		);

		$this->add_control(
			'instructor_label',
			array(
				'label'   => esc_html__( 'عنوان مدرس', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مدرس دوره', 'tadris' ),
			)
		);

		$this->add_control(
			'students_label',
			array(
				'label'   => esc_html__( 'عنوان دانشجویان', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'دانشجویان', 'tadris' ),
			)
		);

		$this->add_control(
			'sessions_suffix',
			array(
				'label'   => esc_html__( 'پسوند تعداد جلسات', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'جلسه', 'tadris' ),
			)
		);

		$this->add_control(
			'students_suffix',
			array(
				'label'   => esc_html__( 'پسوند تعداد دانشجویان', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'نفر', 'tadris' ),
			)
		);

		$this->add_control(
			'status_finished_label',
			array(
				'label'   => esc_html__( 'برچسب وضعیت «تکمیل شده»', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'تکمیل شده', 'tadris' ),
			)
		);

		$this->add_control(
			'status_recording_label',
			array(
				'label'   => esc_html__( 'برچسب وضعیت «درحال ضبط»', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'درحال ضبط', 'tadris' ),
			)
		);

		$this->add_control(
			'free_price_text',
			array(
				'label'   => esc_html__( 'متن قیمت رایگان', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'رایگان', 'tadris' ),
			)
		);

		$this->add_control(
			'unavailable_price_text',
			array(
				'label'   => esc_html__( 'متن قیمت ناموجود', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'ناموجود', 'tadris' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'متن دکمه محصول ساده', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'شرکت در دوره', 'tadris' ),
			)
		);

		$this->add_control(
			'variable_button_text',
			array(
				'label'   => esc_html__( 'متن دکمه محصول متغیر', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'انتخاب گزینه‌ها', 'tadris' ),
			)
		);

		$this->add_control(
			'unavailable_button_text',
			array(
				'label'   => esc_html__( 'متن دکمه محصول ناموجود', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'ناموجود', 'tadris' ),
			)
		);

		$this->add_control(
			'rating_icon',
			array(
				'label'   => esc_html__( 'آیکون امتیاز', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
			)
		);


		$this->webmz_register_icon_color_mode_control( 'rating_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون امتیاز', 'tadris' ) );

		$this->add_control(
			'instructor_icon',
			array(
				'label'   => esc_html__( 'آیکون مدرس', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'far fa-user',
					'library' => 'fa-regular',
				),
			)
		);


		$this->webmz_register_icon_color_mode_control( 'instructor_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون مدرس', 'tadris' ) );

		$this->add_control(
			'students_icon',
			array(
				'label'   => esc_html__( 'آیکون دانشجویان', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-graduation-cap',
					'library' => 'fa-solid',
				),
			)
		);


		$this->webmz_register_icon_color_mode_control( 'students_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون دانشجویان', 'tadris' ) );
		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'loop_style', esc_html__( 'ردیف/اسلایدر', 'tadris' ), '.tadris-products-loop' );
		$this->webmz_register_box_style_controls( 'card_style', esc_html__( 'کارت محصول', 'tadris' ), '.tadris-product-type-1' );
		$this->webmz_register_box_style_controls( 'image_style', esc_html__( 'تصویر محصول', 'tadris' ), '.product-type-1-figure' );
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان محصول', 'tadris' ), '.product-type-1-header-tag a' );
		$this->webmz_register_text_style_controls( 'rating_style', esc_html__( 'امتیاز', 'tadris' ), '.product-type-1-rating' );
		$this->webmz_register_text_style_controls( 'state_style', esc_html__( 'وضعیت و جلسات', 'tadris' ), '.product-type-1-info' );
		$this->webmz_register_text_style_controls( 'meta_style', esc_html__( 'مدرس و دانشجویان', 'tadris' ), '.product-type-1-stats' );
		$this->webmz_register_text_style_controls( 'price_style', esc_html__( 'قیمت', 'tadris' ), '.tadris-price' );
		$this->webmz_register_box_style_controls( 'footer_style', esc_html__( 'فوتر کارت', 'tadris' ), '.product-type-1-footer' );
		$this->webmz_register_box_style_controls( 'button_box_style', esc_html__( 'دکمه شرکت در دوره', 'tadris' ), '.type-1-add-to-cart-btn', array( 'default_background' => 'var(--webmz-color-secondary)' ) );
		$this->webmz_register_text_style_controls( 'button_style', esc_html__( 'متن دکمه', 'tadris' ), '.type-1-add-to-cart-btn' );
	}

	/**
	 * Build WP_Query args for requested product ordering.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<string,mixed>
	 */
	private function query_args( $settings ) {
		$args = array(
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'posts_per_page'         => min( 24, max( 1, absint( $settings['count'] ) ) ),
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => true,
		);

		if ( ! empty( $settings['category'] ) ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => sanitize_title( $settings['category'] ),
				),
			);
		}

		switch ( $settings['order_by'] ) {
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
	 * Render one custom course card.
	 *
	 * @param \WC_Product          $product Product object.
	 * @param array<string,mixed> $s       Widget settings.
	 * @return void
	 */
	private function render_product_card( $product, $s ) {
		ob_start();
		Icons_Manager::render_icon( $s['rating_icon'], array( 'aria-hidden' => 'true' ) );
		$rating_icon_html = (string) ob_get_clean();

		ob_start();
		Icons_Manager::render_icon( $s['instructor_icon'], array( 'aria-hidden' => 'true' ) );
		$instructor_icon_html = (string) ob_get_clean();

		ob_start();
		Icons_Manager::render_icon( $s['students_icon'], array( 'aria-hidden' => 'true' ) );
		$students_icon_html = (string) ob_get_clean();

		webmz_render_product_loop_card(
			$product,
			$this->webmz_build_product_loop_card_1_args(
				$s,
				array(
					'rating_icon_html'            => $rating_icon_html,
					'rating_icon_color_class'     => $this->webmz_get_icon_color_mode_class( $s, 'rating_icon_color_mode' ),
					'instructor_icon_html'        => $instructor_icon_html,
					'instructor_icon_color_class' => $this->webmz_get_icon_color_mode_class( $s, 'instructor_icon_color_mode' ),
					'students_icon_html'          => $students_icon_html,
					'students_icon_color_class'   => $this->webmz_get_icon_color_mode_class( $s, 'students_icon_color_mode' ),
				)
			)
		);
	}

	protected function render() {
		if ( ! function_exists( 'wc_get_product' ) ) {
			return;
		}

		$s = $this->get_settings_for_display();
		$q = new WP_Query( $this->query_args( $s ) );

		if ( ! $q->have_posts() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'محصولی برای نمایش یافت نشد.', 'tadris' ) . '</div>';
			return;
		}

		$is_slider = 'slider' === $s['display_type'];
		?>
		<div class="tadris-products-loop <?php echo $is_slider ? 'swiper tadris-products-swiper' : 'tadris-products-grid webmz-loop-grid'; ?>"
			<?php
			if ( $is_slider ) :
				?>
				data-slides-desktop="<?php echo esc_attr( absint( $s['slides_desktop'] ) ); ?>"
			<?php else : ?>
				style="
					--webmz-grid-columns: <?php echo esc_attr( $s['grid_columns_desktop'] ?? 3 ); ?>;
					--webmz-grid-tablet-columns: <?php echo esc_attr( $s['grid_columns_tablet'] ?? 2 ); ?>;
					--webmz-grid-mobile-columns: <?php echo esc_attr( $s['grid_columns_mobile'] ?? 1 ); ?>;
				"
			<?php endif; ?>
		>
			<?php if ( $is_slider ) : ?>
				<div class="swiper-wrapper">
			<?php endif; ?>

			<?php
			while ( $q->have_posts() ) :
				$q->the_post();

				$product = wc_get_product( get_the_ID() );

				if ( ! $product ) {
					continue;
				}
				?>

				<?php if ( $is_slider ) : ?>
					<div class="swiper-slide">
				<?php endif; ?>

				<?php $this->render_product_card( $product, $s ); ?>

				<?php if ( $is_slider ) : ?>
					</div>
				<?php endif; ?>

			<?php endwhile; ?>

			<?php wp_reset_postdata(); ?>

			<?php if ( $is_slider ) : ?>
				</div>
				<div class="tadris-products-pagination swiper-pagination"></div>
			<?php endif; ?>
		</div>
		<?php
	}
}