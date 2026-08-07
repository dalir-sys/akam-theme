<?php
/**
 * Special offer slider widget — timed WooCommerce sale products.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Swiper slider for products with scheduled sale countdown.
 */
class Special_Offer_Slider_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-special-offer-slider';
	}

	public function get_title() {
		return esc_html__( 'اسلایدر پیشنهاد شگفت‌انگیز', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_categories() {
		return array( 'webmz-shop-widgets' );
	}

	public function get_keywords() {
		return array( 'offer', 'sale', 'countdown', 'swiper', 'product', 'تخفیف', 'پیشنهاد', 'اسلایدر' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-plyr', 'webmz-plyr-widgets', 'webmz-special-offer-slider' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-plyr', 'webmz-special-offer-slider' );
	}

	/**
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

	/**
	 * @return array<string,string>
	 */
	private function tag_options() {
		$options = array(
			'' => esc_html__( 'همه برچسب‌ها', 'tadris' ),
		);

		$terms = get_terms(
			array(
				'taxonomy'   => 'product_tag',
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
		$this->register_query_controls();
		$this->register_label_controls();
		$this->register_icon_controls();
		$this->register_display_controls();
		$this->register_slider_controls();
		$this->register_style_controls();
	}

	protected function register_query_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'کوئری محصولات', 'tadris' ),
			)
		);

		$this->add_control(
			'filter_by',
			array(
				'label'   => esc_html__( 'فیلتر بر اساس', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => array(
					'category' => esc_html__( 'دسته‌بندی', 'tadris' ),
					'tag'      => esc_html__( 'برچسب', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'category',
			array(
				'label'     => esc_html__( 'دسته محصول', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->category_options(),
				'default'   => '',
				'condition' => array(
					'filter_by' => 'category',
				),
			)
		);

		$this->add_control(
			'tag',
			array(
				'label'     => esc_html__( 'برچسب محصول', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->tag_options(),
				'default'   => '',
				'condition' => array(
					'filter_by' => 'tag',
				),
			)
		);

		$this->add_control(
			'count',
			array(
				'label'   => esc_html__( 'تعداد محصولات', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4,
				'min'     => 1,
				'max'     => 12,
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'   => esc_html__( 'مرتب‌سازی', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'sale_end',
				'options' => array(
					'sale_end'   => esc_html__( 'زودتر تمام‌شونده', 'tadris' ),
					'date'       => esc_html__( 'جدیدترین', 'tadris' ),
					'popularity' => esc_html__( 'پرفروش‌ترین', 'tadris' ),
					'rating'     => esc_html__( 'بالاترین امتیاز', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'include_ended',
			array(
				'label'        => esc_html__( 'نمایش تخفیف‌های تمام‌شده', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'description'  => esc_html__( 'در حالت فعال، محصولاتی که زمان تخفیفشان گذشته نیز نمایش داده می‌شوند.', 'tadris' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_label_controls() {
		$this->start_controls_section(
			'section_labels',
			array(
				'label' => esc_html__( 'برچسب‌ها', 'tadris' ),
			)
		);

		$this->add_control(
			'section_title',
			array(
				'label'       => esc_html__( 'عنوان بخش', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'اسلایدر پیشنهاد شگفت‌انگیز', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_header_lines',
			array(
				'label'        => esc_html__( 'نمایش خطوط تزئینی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'timer_label',
			array(
				'label'       => esc_html__( 'برچسب تایمر', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'تخفیف ویژه', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'timer_ended_label',
			array(
				'label'       => esc_html__( 'متن پایان تخفیف', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'پایان تخفیف', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'features_title',
			array(
				'label'       => esc_html__( 'عنوان ویژگی‌ها', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'ویژگی‌های کلیدی', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'author_label',
			array(
				'label'       => esc_html__( 'برچسب نویسنده / توسعه‌دهنده', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'توسعه‌دهنده:', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'sales_label',
			array(
				'label'       => esc_html__( 'برچسب فروش', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'فروش موفق', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'rating_from_label',
			array(
				'label'       => esc_html__( 'برچسب تعداد رأی', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'از {count} رأی', 'tadris' ),
				'label_block' => true,
				'description' => esc_html__( 'از {count} برای جایگذاری تعداد رأی استفاده کنید.', 'tadris' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'متن دکمه خرید', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'مشاهده پیش‌نمایش و دریافت', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();
	}

	protected function register_icon_controls() {
		$this->start_controls_section(
			'section_icons',
			array(
				'label' => esc_html__( 'آیکون‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'play_icon',
			array(
				'label'   => esc_html__( 'آیکون پخش ویدیو', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-play',
					'library' => 'fa-solid',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'play_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون پخش', 'tadris' ) );

		$this->add_control(
			'sales_icon',
			array(
				'label'   => esc_html__( 'آیکون فروش', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-award',
					'library' => 'fa-solid',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'sales_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون فروش', 'tadris' ) );

		$this->add_control(
			'timer_icon',
			array(
				'label'   => esc_html__( 'آیکون تایمر', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-clock',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'feature_icon',
			array(
				'label'   => esc_html__( 'آیکون ویژگی', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				),
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
			'price_icon',
			array(
				'label'   => esc_html__( 'آیکون قیمت', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-tags',
					'library' => 'fa-solid',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'price_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون قیمت', 'tadris' ) );

		$this->add_control(
			'author_icon',
			array(
				'label'   => esc_html__( 'آیکون نویسنده', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-user-edit',
					'library' => 'fa-solid',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'author_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون نویسنده', 'tadris' ) );

		$this->end_controls_section();
	}

	protected function register_display_controls() {
		$this->start_controls_section(
			'section_display',
			array(
				'label' => esc_html__( 'نمایش', 'tadris' ),
			)
		);

		$this->add_control(
			'show_section_header',
			array(
				'label'        => esc_html__( 'نمایش عنوان بخش', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_features',
			array(
				'label'        => esc_html__( 'نمایش ویژگی‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'features_max',
			array(
				'label'     => esc_html__( 'حداکثر ویژگی', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 6,
				'min'       => 1,
				'max'       => 12,
				'condition' => array(
					'show_features' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_rating',
			array(
				'label'        => esc_html__( 'نمایش امتیاز', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_sales',
			array(
				'label'        => esc_html__( 'نمایش فروش', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_author',
			array(
				'label'        => esc_html__( 'نمایش نویسنده', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->webmz_register_title_tag_control();

		$this->end_controls_section();
	}

	protected function register_slider_controls() {
		$this->start_controls_section(
			'section_slider',
			array(
				'label' => esc_html__( 'اسلایدر', 'tadris' ),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'پخش خودکار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => esc_html__( 'تأخیر (میلی‌ثانیه)', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 6000,
				'min'       => 2000,
				'max'       => 20000,
				'condition' => array(
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'        => esc_html__( 'نمایش فلش‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'        => esc_html__( 'نمایش نقطه‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'style_section_header',
			array(
				'label' => esc_html__( 'عنوان بخش', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'header_line_color',
			array(
				'label'     => esc_html__( 'رنگ خطوط عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#c8c4e8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-sos' => '--webmz-sos-header-line: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls(
			'style_section_header_title',
			esc_html__( 'متن عنوان بخش', 'tadris' ),
			'.webmz-sos__header-title',
			'#2d2e5f'
		);

		$this->webmz_register_box_style_controls(
			'style_content_box',
			esc_html__( 'باکس محتوا', 'tadris' ),
			'.webmz-sos__box--content',
			array(
				'default_background' => 'rgba(255, 255, 255, 0.58)',
				'bordered'           => true,
			)
		);

		$this->webmz_register_box_style_controls(
			'style_media_box',
			esc_html__( 'باکس رسانه', 'tadris' ),
			'.webmz-sos__box--media',
			array(
				'default_background' => 'rgba(255, 255, 255, 0.58)',
				'bordered'           => true,
			)
		);

		$this->start_controls_section(
			'style_timer',
			array(
				'label' => esc_html__( 'تایمر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'timer_accent_color',
			array(
				'label'     => esc_html__( 'رنگ فعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f97316',
				'selectors' => array(
					'{{WRAPPER}}' => '--webmz-sos-timer-accent: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'timer_ended_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه پایان تخفیف', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e5e7eb',
				'selectors' => array(
					'{{WRAPPER}}' => '--webmz-sos-timer-ended-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'timer_ended_color',
			array(
				'label'     => esc_html__( 'رنگ متن پایان تخفیف', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6b7280',
				'selectors' => array(
					'{{WRAPPER}}' => '--webmz-sos-timer-ended-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_features',
			array(
				'label' => esc_html__( 'ویژگی‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'feature_icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#22c55e',
				'selectors' => array(
					'{{WRAPPER}}' => '--webmz-sos-feature-icon: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls( 'style_title', esc_html__( 'عنوان محصول', 'tadris' ), '.webmz-sos__product-title' );
		$this->webmz_register_text_style_controls( 'style_price', esc_html__( 'قیمت', 'tadris' ), '.webmz-sos__price-value' );

		$this->start_controls_section(
			'style_button',
			array(
				'label' => esc_html__( 'دکمه خرید', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f97316',
				'selectors' => array(
					'{{WRAPPER}} .webmz-sos__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-sos__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .webmz-sos__button',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @param array<string,mixed> $settings Widget settings.
	 * @return \WC_Product[]
	 */
	private function get_products( $settings ) {
		if ( ! function_exists( 'webmz_get_timed_sale_products' ) ) {
			return array();
		}

		return webmz_get_timed_sale_products(
			array(
				'filter_by'     => $settings['filter_by'] ?? 'category',
				'category'      => $settings['category'] ?? '',
				'tag'           => $settings['tag'] ?? '',
				'count'         => $settings['count'] ?? 4,
				'order_by'      => $settings['order_by'] ?? 'sale_end',
				'include_ended' => ! empty( $settings['include_ended'] ) && 'yes' === $settings['include_ended'],
			)
		);
	}

	private function format_number( $number ) {
		$decimals = 0;

		if ( is_string( $number ) ) {
			if ( false !== strpos( $number, '.' ) ) {
				$decimals = 1;
			}
			$number = str_replace( ',', '', $number );
		} elseif ( is_float( $number ) && floor( $number ) !== $number ) {
			$decimals = 1;
		}

		$formatted = number_format_i18n( (float) $number, $decimals );

		return function_exists( 'webmz_to_persian_digits' )
			? webmz_to_persian_digits( $formatted )
			: $formatted;
	}

	private function format_timer_unit( $value ) {
		$output = sprintf( '%02d', max( 0, (int) $value ) );

		return function_exists( 'webmz_to_persian_digits' )
			? webmz_to_persian_digits( $output )
			: $output;
	}

	private function has_icon_setting( $settings, $key ) {
		$icon = $settings[ $key ] ?? null;

		if ( ! is_array( $icon ) ) {
			return false;
		}

		if ( ! empty( $icon['url'] ) ) {
			return true;
		}

		$value = $icon['value'] ?? '';

		if ( is_array( $value ) ) {
			return ! empty( $value['url'] ) || ! empty( $value['id'] );
		}

		return '' !== (string) $value;
	}

	private function icon_html( $settings, $key ) {
		$icon = $settings[ $key ] ?? array();

		if ( ! $this->has_icon_setting( $settings, $key ) ) {
			return '';
		}

		if ( method_exists( Icons_Manager::class, 'try_get_icon_html' ) ) {
			$html = Icons_Manager::try_get_icon_html( $icon, array( 'aria-hidden' => 'true' ) );

			return is_string( $html ) ? $html : '';
		}

		ob_start();
		Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );

		return (string) ob_get_clean();
	}

	private function icon_wrap_html( $settings, $key, $class, $color_mode_key ) {
		$icon = $this->icon_html( $settings, $key );

		if ( '' === $icon ) {
			return '';
		}

		$mode_class = $this->webmz_get_icon_color_mode_class( $settings, $color_mode_key );

		return sprintf(
			'<span class="%1$s %2$s">%3$s</span>',
			esc_attr( $class ),
			esc_attr( $mode_class ),
			$icon // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}

	/**
	 * Render play icon from Content tab (with default fallback).
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return string
	 */
	private function render_play_icon_html( $settings ) {
		$html = $this->icon_html( $settings, 'play_icon' );

		if ( '' !== $html ) {
			return $html;
		}

		if ( method_exists( Icons_Manager::class, 'try_get_icon_html' ) ) {
			$fallback = Icons_Manager::try_get_icon_html(
				array(
					'value'   => 'fas fa-play',
					'library' => 'fa-solid',
				),
				array( 'aria-hidden' => 'true' )
			);

			return is_string( $fallback ) ? $fallback : '';
		}

		ob_start();
		Icons_Manager::render_icon(
			array(
				'value'   => 'fas fa-play',
				'library' => 'fa-solid',
			),
			array( 'aria-hidden' => 'true' )
		);

		return (string) ob_get_clean();
	}

	private function get_product_title_html( $product ) {
		if ( function_exists( 'webmz_spw_get_product_title_html' ) ) {
			$html = webmz_spw_get_product_title_html( $product->get_id() );

			if ( '' !== trim( wp_strip_all_tags( $html ) ) ) {
				return $html;
			}
		}

		return esc_html( $product->get_name() );
	}

	private function get_rating_from_label( $settings, $count ) {
		$label = $settings['rating_from_label'] ?? esc_html__( 'از {count} رأی', 'tadris' );

		return str_replace( '{count}', $this->format_number( $count ), $label );
	}

	/**
	 * @param array<string,mixed> $settings Widget settings.
	 * @param \WC_Product         $product  Product object.
	 */
	private function render_timer( $settings, $product ) {
		$end_ts      = function_exists( 'webmz_get_product_sale_end_timestamp' )
			? webmz_get_product_sale_end_timestamp( $product )
			: 0;
		$start_ts    = function_exists( 'webmz_get_product_sale_start_timestamp' )
			? webmz_get_product_sale_start_timestamp( $product )
			: 0;
		$is_ended    = $end_ts <= time();
		$timer_label = $settings['timer_label'] ?? esc_html__( 'تخفیف ویژه', 'tadris' );
		$ended_label = $settings['timer_ended_label'] ?? esc_html__( 'پایان تخفیف', 'tadris' );
		$now         = time();
		$diff        = max( 0, $end_ts - $now );
		$days        = (int) floor( $diff / DAY_IN_SECONDS );
		$hours       = (int) floor( ( $diff % DAY_IN_SECONDS ) / HOUR_IN_SECONDS );
		$minutes     = (int) floor( ( $diff % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS );
		$seconds     = (int) ( $diff % MINUTE_IN_SECONDS );

		if ( $end_ts > $now && function_exists( 'webmz_normalize_product_sale_start_timestamp' ) ) {
			$start_ts = webmz_normalize_product_sale_start_timestamp( $product, $end_ts, $now );
		} elseif ( $start_ts <= 0 && $end_ts > 0 ) {
			$start_ts = $end_ts - ( 30 * DAY_IN_SECONDS );
		}

		$progress = 0.0;
		if ( $end_ts > $now && $start_ts > 0 && $end_ts > $start_ts ) {
			$progress = min( 100.0, max( 0.0, ( $diff / ( $end_ts - $start_ts ) ) * 100 ) );
		}
		?>
		<div
			class="webmz-sos__timer<?php echo $is_ended ? ' is-ended' : ''; ?>"
			data-webmz-sos-timer
			data-start="<?php echo esc_attr( (string) $start_ts ); ?>"
			data-end="<?php echo esc_attr( (string) $end_ts ); ?>"
			data-ended-label="<?php echo esc_attr( $ended_label ); ?>"
			data-timer-label="<?php echo esc_attr( $timer_label ); ?>"
		>
			<div class="webmz-sos__timer-top">
				<div class="webmz-sos__timer-label-wrap">
					<div class="webmz-sos__timer-head">
						<span class="webmz-sos__timer-icon"><?php echo $this->icon_html( $settings, 'timer_icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="webmz-sos__timer-label"><?php echo esc_html( $is_ended ? $ended_label : $timer_label ); ?></span>
					</div>
				</div>
				<div class="webmz-sos__timer-digits"<?php echo $is_ended ? ' hidden' : ''; ?>>
					<span class="webmz-sos__timer-unit" data-unit="days"><?php echo esc_html( $this->format_timer_unit( $days ) ); ?></span>
					<span class="webmz-sos__timer-sep" aria-hidden="true">:</span>
					<span class="webmz-sos__timer-unit" data-unit="hours"><?php echo esc_html( $this->format_timer_unit( $hours ) ); ?></span>
					<span class="webmz-sos__timer-sep" aria-hidden="true">:</span>
					<span class="webmz-sos__timer-unit" data-unit="minutes"><?php echo esc_html( $this->format_timer_unit( $minutes ) ); ?></span>
					<span class="webmz-sos__timer-sep" aria-hidden="true">:</span>
					<span class="webmz-sos__timer-unit" data-unit="seconds"><?php echo esc_html( $this->format_timer_unit( $seconds ) ); ?></span>
				</div>
			</div>
			<div class="webmz-sos__timer-progress" aria-hidden="true"<?php echo $is_ended ? ' hidden' : ''; ?>>
				<span class="webmz-sos__timer-progress-bar" style="width: <?php echo esc_attr( $is_ended ? '0' : number_format( $progress, 2, '.', '' ) ); ?>%;"></span>
			</div>
		</div>
		<?php
	}

	/**
	 * Render product image or intro video play trigger.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @param \WC_Product         $product  Product object.
	 * @param string              $url      Product permalink.
	 */
	private function render_product_media( $settings, $product, $url ) {
		$product_id = $product->get_id();
		$video_url  = function_exists( 'webmz_spw_get_product_intro_video_url' )
			? webmz_spw_get_product_intro_video_url( $product_id )
			: '';

		if ( $video_url ) {
			$poster = has_post_thumbnail( $product_id ) ? get_the_post_thumbnail_url( $product_id, 'large' ) : '';
			?>
			<figure class="webmz-sos__figure webmz-sos__figure--video">
				<?php if ( $poster ) : ?>
					<img
						class="webmz-sos__image webmz-sos__poster"
						src="<?php echo esc_url( $poster ); ?>"
						alt="<?php echo esc_attr( $product->get_name() ); ?>"
						loading="lazy"
						decoding="async"
					>
				<?php else : ?>
					<span class="webmz-sos__video-placeholder" aria-hidden="true"></span>
				<?php endif; ?>
				<button
					type="button"
					class="webmz-sos__play"
					data-webmz-sos-play
					data-product-id="<?php echo esc_attr( (string) $product_id ); ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'پخش ویدیو معرفی %s', 'tadris' ), $product->get_name() ) ); ?>"
				>
					<span class="webmz-sos__play-icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'play_icon_color_mode' ) ); ?>" aria-hidden="true">
						<?php echo $this->render_play_icon_html( $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
				</button>
			</figure>
			<?php
			return;
		}

		if ( ! has_post_thumbnail( $product_id ) ) {
			return;
		}
		?>
		<figure class="webmz-sos__figure">
			<a href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
				<?php
				echo webmz_get_post_loop_thumbnail(
					$product_id,
					array(
						'class' => 'webmz-sos__image',
					)
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</a>
		</figure>
		<?php
	}

	/**
	 * @param array<string,mixed> $settings Widget settings.
	 * @param \WC_Product         $product  Product object.
	 */
	private function render_slide( $settings, $product ) {
		$product_id   = $product->get_id();
		$url          = $product->get_permalink();
		$title_tag    = $this->webmz_get_title_tag( $settings, 'title_tag' );
		$price        = function_exists( 'webmz_get_product_loop_price_data' )
			? webmz_get_product_loop_price_data( $product )
			: array(
				'state'    => 'unavailable',
				'amount'   => '',
				'currency' => '',
			);
		$rating       = (float) $product->get_average_rating();
		$rating_count = absint( $product->get_rating_count() );
		$sales        = absint( $product->get_total_sales() );
		$author_id    = (int) get_post_field( 'post_author', $product_id );
		$author_name  = get_the_author_meta( 'display_name', $author_id );
		$features     = ( ! isset( $settings['show_features'] ) || 'yes' === $settings['show_features'] ) && function_exists( 'webmz_get_product_key_features' )
			? webmz_get_product_key_features( $product, absint( $settings['features_max'] ?? 6 ) )
			: array();
		?>
		<article class="webmz-sos__slide<?php echo empty( $features ) ? ' webmz-sos__slide--no-features' : ''; ?>">
			<div class="webmz-sos__box-shell webmz-sos__box-shell--content">
				<span class="webmz-sos__box-decor webmz-sos__box-decor--primary" aria-hidden="true"></span>
				<div class="webmz-sos__box webmz-sos__box--content">
				<div class="webmz-sos__content-grid">
					<div class="webmz-sos__col webmz-sos__col--details">
						<<?php echo esc_html( $title_tag ); ?> class="webmz-sos__product-title">
							<a href="<?php echo esc_url( $url ); ?>"><?php echo $this->get_product_title_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
						</<?php echo esc_html( $title_tag ); ?>>

						<?php if ( ( ! isset( $settings['show_author'] ) || 'yes' === $settings['show_author'] ) && $author_name ) : ?>
							<div class="webmz-sos__author">
								<div class="webmz-sos__author-avatar-wrap">
									<?php echo get_avatar( $author_id, 48, '', $author_name, array( 'class' => 'webmz-sos__author-avatar' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
								<div class="webmz-sos__author-text">
									<span class="webmz-sos__author-label"><?php echo esc_html( $settings['author_label'] ?? '' ); ?></span>
									<strong class="webmz-sos__author-name"><?php echo esc_html( $author_name ); ?></strong>
								</div>
							</div>
						<?php endif; ?>

						<div class="webmz-sos__stats">
							<?php if ( ( ! isset( $settings['show_rating'] ) || 'yes' === $settings['show_rating'] ) && $rating > 0 ) : ?>
								<div class="webmz-sos__stat webmz-sos__stat--rating">
									<?php echo $this->icon_wrap_html( $settings, 'rating_icon', 'webmz-sos__stat-icon', 'rating_icon_color_mode' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<strong><?php echo esc_html( $this->format_number( number_format( $rating, 1, '.', '' ) ) ); ?></strong>
									<?php if ( $rating_count > 0 ) : ?>
										<span class="webmz-sos__stat-meta"><?php echo esc_html( $this->get_rating_from_label( $settings, $rating_count ) ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if ( ( ! isset( $settings['show_sales'] ) || 'yes' === $settings['show_sales'] ) && $sales > 0 ) : ?>
								<div class="webmz-sos__stat webmz-sos__stat--sales">
									<?php echo $this->icon_wrap_html( $settings, 'sales_icon', 'webmz-sos__stat-icon', 'sales_icon_color_mode' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<strong><?php echo esc_html( $this->format_number( $sales ) ); ?></strong>
									<span class="webmz-sos__stat-meta"><?php echo esc_html( $settings['sales_label'] ?? '' ); ?></span>
								</div>
							<?php endif; ?>
						</div>

						<div class="webmz-sos__price">
							<?php echo $this->icon_wrap_html( $settings, 'price_icon', 'webmz-sos__price-icon', 'price_icon_color_mode' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<div class="webmz-sos__price-body">
								<?php if ( 'unavailable' === $price['state'] ) : ?>
									<span class="webmz-sos__price-value"><?php esc_html_e( 'ناموجود', 'tadris' ); ?></span>
								<?php elseif ( 'free' === $price['state'] ) : ?>
									<span class="webmz-sos__price-value"><?php esc_html_e( 'رایگان', 'tadris' ); ?></span>
								<?php else : ?>
									<?php if ( ! empty( $price['regular_amount'] ) ) : ?>
										<del class="webmz-sos__price-regular"><?php echo esc_html( $this->format_number( $price['regular_amount'] ) . ' ' . $price['currency'] ); ?></del>
									<?php endif; ?>
									<span class="webmz-sos__price-value"><?php echo esc_html( $this->format_number( $price['amount'] ) ); ?></span>
									<span class="webmz-sos__price-currency"><?php echo esc_html( $price['currency'] ); ?></span>
								<?php endif; ?>
							</div>
						</div>

						<?php if ( ! empty( $settings['button_text'] ) ) : ?>
							<a class="webmz-sos__button" href="<?php echo esc_url( $url ); ?>">
								<?php echo esc_html( $settings['button_text'] ); ?>
							</a>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $features ) ) : ?>
						<div class="webmz-sos__col webmz-sos__col--features">
							<?php if ( ! empty( $settings['features_title'] ) ) : ?>
								<h3 class="webmz-sos__features-title"><?php echo esc_html( $settings['features_title'] ); ?></h3>
							<?php endif; ?>
							<ul class="webmz-sos__features">
								<?php foreach ( $features as $feature ) : ?>
									<li class="webmz-sos__feature">
										<span class="webmz-sos__feature-text"><?php echo esc_html( $feature ); ?></span>
										<span class="webmz-sos__feature-icon"><?php echo $this->icon_html( $settings, 'feature_icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
				</div>
			</div>

			<div class="webmz-sos__box-shell webmz-sos__box-shell--media">
				<span class="webmz-sos__box-decor webmz-sos__box-decor--primary" aria-hidden="true"></span>
				<div class="webmz-sos__box webmz-sos__box--media">
					<?php $this->render_timer( $settings, $product ); ?>
					<?php $this->render_product_media( $settings, $product, $url ); ?>
				</div>
			</div>
		</article>
		<?php
	}

	protected function render() {
		if ( ! function_exists( 'wc_get_product' ) ) {
			echo '<p class="webmz-editor-placeholder">' . esc_html__( 'ووکامرس فعال نیست.', 'tadris' ) . '</p>';
			return;
		}

		$settings = $this->get_settings_for_display();
		$products = $this->get_products( $settings );

		if ( empty( $products ) ) {
			echo '<p class="webmz-editor-placeholder">' . esc_html__( 'محصولی با تایمر تخفیف زمان‌دار یافت نشد. در ویرایش محصول، تاریخ پایان فروش ویژه را تنظیم کنید.', 'tadris' ) . '</p>';
			return;
		}

		$swiper_config = wp_json_encode(
			array(
				'autoplay'   => ! isset( $settings['autoplay'] ) || 'yes' === $settings['autoplay'],
				'delay'      => absint( $settings['autoplay_delay'] ?? 6000 ),
				'showArrows' => ! isset( $settings['show_arrows'] ) || 'yes' === $settings['show_arrows'],
			)
		);
		?>
		<div class="webmz-sos">
			<?php
			$show_header_lines = ! empty( $settings['show_header_lines'] ) && 'yes' === $settings['show_header_lines'];
			if ( ( ! isset( $settings['show_section_header'] ) || 'yes' === $settings['show_section_header'] ) && ! empty( $settings['section_title'] ) ) :
				?>
				<header class="webmz-sos__header">
					<?php if ( $show_header_lines ) : ?>
						<span class="webmz-sos__header-line" aria-hidden="true"></span>
					<?php endif; ?>
					<h2 class="webmz-sos__header-title"><?php echo esc_html( $settings['section_title'] ); ?></h2>
					<?php if ( $show_header_lines ) : ?>
						<span class="webmz-sos__header-line" aria-hidden="true"></span>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<div class="webmz-sos__slider-wrap">
				<div class="swiper webmz-sos__swiper" data-webmz-special-offer-slider='<?php echo esc_attr( $swiper_config ); ?>'>
					<div class="swiper-wrapper">
						<?php foreach ( $products as $product ) : ?>
							<div class="swiper-slide">
								<?php $this->render_slide( $settings, $product ); ?>
							</div>
						<?php endforeach; ?>
					</div>

					<?php if ( ! isset( $settings['show_arrows'] ) || 'yes' === $settings['show_arrows'] ) : ?>
						<button type="button" class="webmz-sos__arrow webmz-sos__arrow--prev" aria-label="<?php esc_attr_e( 'اسلاید قبلی', 'tadris' ); ?>"></button>
						<button type="button" class="webmz-sos__arrow webmz-sos__arrow--next" aria-label="<?php esc_attr_e( 'اسلاید بعدی', 'tadris' ); ?>"></button>
					<?php endif; ?>

					<?php if ( ! isset( $settings['show_pagination'] ) || 'yes' === $settings['show_pagination'] ) : ?>
						<div class="webmz-sos__pagination swiper-pagination"></div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
