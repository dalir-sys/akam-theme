<?php
/**
 * Course product loop widget — card layout type 3.
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
 * WooCommerce course cards with grid/slider layout (type 3).
 */
class Tadris_Product_Loop_3_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-tadris-product-loop-3';
	}

	public function get_title() {
		return esc_html__( 'لوپ دوره‌ها آکام 3', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_categories() {
		return array( 'webmz-shop-widgets' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-tadris-widgets' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-tadris-product-loop-3' );
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
					'slider' => esc_html__( 'اسلایدی (Swiper)', 'tadris' ),
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
				'default'   => '4',
				'options'   => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
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
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .tadris-products-loop-3' => '--tpl3-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'slider_heading',
			array(
				'label'     => esc_html__( 'تنظیمات اسلایدر', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'display_type' => 'slider',
				),
			)
		);

		foreach ( array( 'desktop' => esc_html__( 'دسکتاپ', 'tadris' ), 'tablet' => esc_html__( 'تبلت', 'tadris' ), 'mobile' => esc_html__( 'موبایل', 'tadris' ) ) as $device => $label ) {
			$this->add_control(
				'slides_' . $device,
				array(
					'label'     => sprintf( esc_html__( 'تعداد اسلاید %s', 'tadris' ), $label ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 'mobile' === $device ? 1 : ( 'tablet' === $device ? 2 : 4 ),
					'min'       => 1,
					'max'       => 6,
					'condition' => array(
						'display_type' => 'slider',
					),
				)
			);
		}

		$this->add_control(
			'slider_loop',
			array(
				'label'        => esc_html__( 'لوپ اسلایدر', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'فعال', 'tadris' ),
				'label_off'    => esc_html__( 'غیرفعال', 'tadris' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'slider_autoplay',
			array(
				'label'        => esc_html__( 'پخش خودکار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'فعال', 'tadris' ),
				'label_off'    => esc_html__( 'غیرفعال', 'tadris' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'slider_autoplay_delay',
			array(
				'label'     => esc_html__( 'زمان پخش خودکار (میلی‌ثانیه)', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3500,
				'min'       => 1000,
				'step'      => 100,
				'condition' => array(
					'display_type'     => 'slider',
					'slider_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_slider_nav',
			array(
				'label'        => esc_html__( 'نمایش فلش‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'show_slider_pagination',
			array(
				'label'        => esc_html__( 'نمایش صفحه‌بندی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_product_loop_card_3_visibility_controls();

		$this->start_controls_section(
			'labels',
			array(
				'label' => esc_html__( 'متن و آیکون‌ها', 'tadris' ),
			)
		);

		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ HTML عنوان محصول', 'tadris' ) );

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'متن دکمه', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مشاهده دوره', 'tadris' ),
			)
		);

		$this->add_control(
			'rating_label',
			array(
				'label'   => esc_html__( 'برچسب امتیاز', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'instructor_label',
			array(
				'label'   => esc_html__( 'برچسب مدرس دوره', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'students_label',
			array(
				'label'   => esc_html__( 'برچسب دانشجویان', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'reviews_label',
			array(
				'label'   => esc_html__( 'برچسب دیدگاه‌ها', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'sessions_label',
			array(
				'label'   => esc_html__( 'برچسب تعداد درس‌ها', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'duration_label',
			array(
				'label'   => esc_html__( 'برچسب مدت دوره', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'sessions_suffix',
			array(
				'label'   => esc_html__( 'پسوند تعداد درس‌ها', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'درس', 'tadris' ),
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
			'excerpt_words',
			array(
				'label'   => esc_html__( 'تعداد کلمات خلاصه', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 10,
				'min'     => 4,
				'max'     => 40,
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
			'rating_icon',
			array(
				'label'   => esc_html__( 'آیکون ستاره امتیاز', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'rating_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون ستاره', 'tadris' ) );

		$this->add_control(
			'students_icon',
			array(
				'label'   => esc_html__( 'آیکون دانشجویان', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-user-friends',
					'library' => 'fa-solid',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'students_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون دانشجویان', 'tadris' ) );

		$this->add_control(
			'reviews_icon',
			array(
				'label'   => esc_html__( 'آیکون دیدگاه‌ها', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'far fa-comment',
					'library' => 'fa-regular',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'reviews_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون دیدگاه‌ها', 'tadris' ) );

		$this->add_control(
			'duration_icon',
			array(
				'label'   => esc_html__( 'آیکون مدت دوره', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'far fa-clock',
					'library' => 'fa-regular',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'duration_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون مدت', 'tadris' ) );

		$this->add_control(
			'sessions_icon',
			array(
				'label'   => esc_html__( 'آیکون درس‌ها', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'far fa-calendar-alt',
					'library' => 'fa-regular',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'sessions_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون درس‌ها', 'tadris' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'accent_style',
			array(
				'label' => esc_html__( 'رنگ اصلی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'primary_color',
			array(
				'label'     => esc_html__( 'رنگ اصلی (قیمت / دکمه / ستاره)', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e53935',
				'selectors' => array(
					'{{WRAPPER}}' => '--tpl3-primary: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'discount_color',
			array(
				'label'     => esc_html__( 'رنگ برچسب تخفیف', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#22c55e',
				'selectors' => array(
					'{{WRAPPER}}' => '--tpl3-discount: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'loop_style', esc_html__( 'ردیف/اسلایدر', 'tadris' ), '.tadris-products-loop-3' );
		$this->webmz_register_box_style_controls( 'card_style', esc_html__( 'کارت محصول', 'tadris' ), '.tadris-product-type-3' );
		$this->webmz_register_box_style_controls( 'image_style', esc_html__( 'تصویر محصول', 'tadris' ), '.product-type-3-figure' );
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان محصول', 'tadris' ), '.product-type-3-header-tag a' );
		$this->webmz_register_text_style_controls( 'excerpt_style', esc_html__( 'خلاصه', 'tadris' ), '.product-type-3-subtitle' );
		$this->webmz_register_text_style_controls( 'meta_style', esc_html__( 'مدت / درس / مدرس', 'tadris' ), '.product-type-3-meta-text, .product-type-3-instructor-name' );
		$this->webmz_register_text_style_controls( 'price_style', esc_html__( 'مبلغ قیمت', 'tadris' ), '.product-type-3-price-amount, .product-type-3-price-free' );
		$this->webmz_register_box_style_controls( 'button_box_style', esc_html__( 'دکمه مشاهده دوره', 'tadris' ), '.product-type-3-btn', array( 'default_background' => 'var(--tpl3-primary, #e53935)' ) );
		$this->webmz_register_text_style_controls( 'button_style', esc_html__( 'متن دکمه', 'tadris' ), '.product-type-3-btn' );
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
	 * Render icon HTML from settings.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @param string              $key      Icon setting key.
	 * @return string
	 */
	private function render_icon_html( $settings, $key ) {
		if ( empty( $settings[ $key ]['value'] ) ) {
			return '';
		}

		ob_start();
		Icons_Manager::render_icon( $settings[ $key ], array( 'aria-hidden' => 'true' ) );

		return (string) ob_get_clean();
	}

	/**
	 * Render one type-3 course card.
	 *
	 * @param \WC_Product          $product Product object.
	 * @param array<string,mixed> $s       Widget settings.
	 * @return void
	 */
	private function render_product_card( $product, $s ) {
		if ( ! function_exists( 'webmz_render_product_loop_card_v3' ) ) {
			return;
		}

		webmz_render_product_loop_card_v3(
			$product,
			$this->webmz_build_product_loop_card_3_args(
				$s,
				array(
					'rating_icon_html'          => $this->render_icon_html( $s, 'rating_icon' ),
					'rating_icon_color_class'   => $this->webmz_get_icon_color_mode_class( $s, 'rating_icon_color_mode' ),
					'students_icon_html'        => $this->render_icon_html( $s, 'students_icon' ),
					'students_icon_color_class' => $this->webmz_get_icon_color_mode_class( $s, 'students_icon_color_mode' ),
					'reviews_icon_html'         => $this->render_icon_html( $s, 'reviews_icon' ),
					'reviews_icon_color_class'  => $this->webmz_get_icon_color_mode_class( $s, 'reviews_icon_color_mode' ),
					'duration_icon_html'        => $this->render_icon_html( $s, 'duration_icon' ),
					'duration_icon_color_class' => $this->webmz_get_icon_color_mode_class( $s, 'duration_icon_color_mode' ),
					'sessions_icon_html'        => $this->render_icon_html( $s, 'sessions_icon' ),
					'sessions_icon_color_class' => $this->webmz_get_icon_color_mode_class( $s, 'sessions_icon_color_mode' ),
				)
			)
		);
	}

	protected function render() {
		if ( ! function_exists( 'wc_get_product' ) || ! function_exists( 'webmz_render_product_loop_card_v3' ) ) {
			return;
		}

		$s = $this->get_settings_for_display();
		$q = new WP_Query( $this->query_args( $s ) );

		if ( ! $q->have_posts() ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'محصولی برای نمایش یافت نشد.', 'tadris' ) . '</div>';
			return;
		}

		$is_slider = 'slider' === $s['display_type'];
		$gap       = isset( $s['grid_gap']['size'] ) ? absint( $s['grid_gap']['size'] ) : 20;

		if ( $is_slider ) {
			$slider_conf = array(
				'slidesDesktop' => ! empty( $s['slides_desktop'] ) ? absint( $s['slides_desktop'] ) : 4,
				'slidesTablet'  => ! empty( $s['slides_tablet'] ) ? absint( $s['slides_tablet'] ) : 2,
				'slidesMobile'  => ! empty( $s['slides_mobile'] ) ? absint( $s['slides_mobile'] ) : 1,
				'spaceBetween'  => $gap,
				'loop'          => isset( $s['slider_loop'] ) && 'yes' === $s['slider_loop'],
				'autoplay'      => isset( $s['slider_autoplay'] ) && 'yes' === $s['slider_autoplay'],
				'autoplayDelay' => ! empty( $s['slider_autoplay_delay'] ) ? absint( $s['slider_autoplay_delay'] ) : 3500,
				'navigation'    => isset( $s['show_slider_nav'] ) && 'yes' === $s['show_slider_nav'],
				'pagination'    => ! isset( $s['show_slider_pagination'] ) || 'yes' === $s['show_slider_pagination'],
			);
			?>
			<div
				class="tadris-products-loop-3 tadris-products-loop-3-swiper swiper"
				data-tadris-product-loop-3-swiper="<?php echo esc_attr( wp_json_encode( $slider_conf ) ); ?>"
				style="--tpl3-gap: <?php echo esc_attr( $gap ); ?>px;"
			>
				<div class="swiper-wrapper">
					<?php
					while ( $q->have_posts() ) :
						$q->the_post();
						$product = wc_get_product( get_the_ID() );
						if ( ! $product ) {
							continue;
						}
						?>
						<div class="swiper-slide">
							<?php $this->render_product_card( $product, $s ); ?>
						</div>
					<?php endwhile; ?>
				</div>

				<?php if ( $slider_conf['navigation'] ) : ?>
					<div class="tadris-products-loop-3-swiper-button tadris-products-loop-3-swiper-button-next swiper-button-next"></div>
					<div class="tadris-products-loop-3-swiper-button tadris-products-loop-3-swiper-button-prev swiper-button-prev"></div>
				<?php endif; ?>

				<?php if ( $slider_conf['pagination'] ) : ?>
					<div class="tadris-products-loop-3-swiper-pagination swiper-pagination"></div>
				<?php endif; ?>
			</div>
			<?php
		} else {
			?>
			<div
				class="tadris-products-loop-3 tadris-products-loop-3-grid webmz-loop-grid"
				style="
					--webmz-grid-columns: <?php echo esc_attr( $s['grid_columns_desktop'] ?? 4 ); ?>;
					--webmz-grid-tablet-columns: <?php echo esc_attr( $s['grid_columns_tablet'] ?? 2 ); ?>;
					--webmz-grid-mobile-columns: <?php echo esc_attr( $s['grid_columns_mobile'] ?? 1 ); ?>;
					--tpl3-gap: <?php echo esc_attr( $gap ); ?>px;
				"
			>
				<?php
				while ( $q->have_posts() ) :
					$q->the_post();
					$product = wc_get_product( get_the_ID() );
					if ( ! $product ) {
						continue;
					}
					$this->render_product_card( $product, $s );
				endwhile;
				?>
			</div>
			<?php
		}

		wp_reset_postdata();
	}
}
