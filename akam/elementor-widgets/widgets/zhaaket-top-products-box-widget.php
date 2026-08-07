<?php
/**
 * Zhaaket top products box — featured product grid card with CTA buttons.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Card widget with 2×2 product thumbnails (loop metabox) and dual CTA buttons.
 */
class Zhaaket_Top_Products_Box_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-zhaaket-top-products-box';
	}

	public function get_title() {
		return esc_html__( 'باکس محصولات درجه‌یک ژاکت', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-info-box';
	}

	public function get_categories() {
		return array( 'webmz-shop-widgets' );
	}

	public function get_keywords() {
		return array( 'zhaaket', 'zhaket', 'product', 'box', 'ژاکت', 'محصول', 'باکس', 'درجه یک' );
	}

	public function get_style_depends() {
		return array( 'webmz-zhaaket-top-products-box' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_products',
			array(
				'label' => esc_html__( 'محصولات', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'product_ids',
			array(
				'label'       => esc_html__( 'انتخاب ۴ محصول', 'tadris' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => \webmz_tadris_get_product_options(),
				'multiple'    => true,
				'label_block' => true,
				'description' => esc_html__( 'حداکثر ۴ محصول انتخاب کنید. تصویر کوچک از متاباکس «تامنیل لوپ فایل‌ها» خوانده می‌شود.', 'tadris' ),
			)
		);

		$this->add_control(
			'product_label_source',
			array(
				'label'   => esc_html__( 'متن زیر تصویر', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'badge',
				'options' => array(
					'badge' => esc_html__( 'متن متا (متاباکس محصول)', 'tadris' ),
					'title' => esc_html__( 'عنوان محصول', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'product_link',
			array(
				'label'        => esc_html__( 'لینک به صفحه محصول', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text',
			array(
				'label' => esc_html__( 'متن‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'محصولات ایرانی', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control();

		$this->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'زیرعنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'پشتیبانی درجه یک', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_decor',
			array(
				'label'        => esc_html__( 'نمایش تزئینات پس‌زمینه', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_buttons',
			array(
				'label' => esc_html__( 'دکمه‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'button_1_text',
			array(
				'label'   => esc_html__( 'متن دکمه اول', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'قالب ایرانی', 'tadris' ),
			)
		);

		$this->add_control(
			'button_1_url',
			array(
				'label'   => esc_html__( 'لینک دکمه اول', 'tadris' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'button_2_text',
			array(
				'label'     => esc_html__( 'متن دکمه دوم', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'افزونه ایرانی', 'tadris' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_2_url',
			array(
				'label'   => esc_html__( 'لینک دکمه دوم', 'tadris' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'section_box_style',
			array(
				'label' => esc_html__( 'کارت', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'box_background',
				'selector' => '{{WRAPPER}} .webmz-ztpb',
				'fields_options' => array(
					'background' => array(
						'default' => 'gradient',
					),
					'color'      => array(
						'default' => 'var(--webmz-account-accent-light)',
					),
					'color_b'    => array(
						'default' => 'var(--webmz-color-primary-light)',
					),
					'gradient_angle' => array(
						'default' => array(
							'unit' => 'deg',
							'size' => 180,
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'      => '28',
					'right'    => '24',
					'bottom'   => '28',
					'left'     => '24',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ztpb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ztpb' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .webmz-ztpb',
			)
		);

		$this->add_control(
			'decor_color',
			array(
				'label'     => esc_html__( 'رنگ تزئینات پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-secondary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztpb' => '--webmz-ztpb-decor-color: {{VALUE}};',
				),
				'condition' => array(
					'show_decor' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_products_style',
			array(
				'label' => esc_html__( 'گرید محصولات', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'products_gap',
			array(
				'label'      => esc_html__( 'فاصله بین آیتم‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ztpb__products' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'product_thumb_size',
			array(
				'label'      => esc_html__( 'اندازه تصویر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 40, 'max' => 120 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 56,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ztpb__product-thumb' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'product_tile_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه کاشی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztpb__product-thumb' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'product_tile_radius',
			array(
				'label'      => esc_html__( 'گردی کاشی', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 30 ),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ztpb__product-thumb' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-ztpb__product-thumb img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'product_tile_shadow',
				'selector' => '{{WRAPPER}} .webmz-ztpb__product-thumb',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'product_label_typography',
				'label'    => esc_html__( 'تایپوگرافی متن محصول', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-ztpb__product-label',
			)
		);

		$this->add_control(
			'product_label_color',
			array(
				'label'     => esc_html__( 'رنگ متن محصول', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-dark)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztpb__product-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls(
			'title_style',
			esc_html__( 'عنوان', 'tadris' ),
			'.webmz-ztpb__title'
		);

		$this->update_control(
			'title_style_color',
			array(
				'default' => 'var(--webmz-color-text-dark)',
			)
		);

		$this->start_controls_section(
			'subtitle_style',
			array(
				'label' => esc_html__( 'زیرعنوان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .webmz-ztpb__subtitle',
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-gray)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztpb__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'subtitle_spacing',
			array(
				'label'      => esc_html__( 'فاصله از عنوان', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 6,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ztpb__subtitle' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'buttons_style',
			array(
				'label' => esc_html__( 'دکمه‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'buttons_gap',
			array(
				'label'      => esc_html__( 'فاصله بین دکمه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ztpb__buttons' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .webmz-ztpb__btn',
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی دکمه', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => '12',
					'right'    => '16',
					'bottom'   => '12',
					'left'     => '16',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ztpb__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => esc_html__( 'گردی دکمه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ztpb__btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		$this->start_controls_tab(
			'button_normal_tab',
			array(
				'label' => esc_html__( 'عادی', 'tadris' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_background',
				'selector' => '{{WRAPPER}} .webmz-ztpb__btn',
				'fields_options' => array(
					'background' => array(
						'default' => 'gradient',
					),
					'color'      => array(
						'default' => 'var(--webmz-color-primary)',
					),
					'color_b'    => array(
						'default' => 'var(--webmz-color-primary-hover)',
					),
					'gradient_angle' => array(
						'default' => array(
							'unit' => 'deg',
							'size' => 90,
						),
					),
				),
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztpb__btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_hover_tab',
			array(
				'label' => esc_html__( 'هاور', 'tadris' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_hover_background',
				'selector' => '{{WRAPPER}} .webmz-ztpb__btn:hover, {{WRAPPER}} .webmz-ztpb__btn:focus-visible',
			)
		);

		$this->add_control(
			'button_hover_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-ztpb__btn:hover, {{WRAPPER}} .webmz-ztpb__btn:focus-visible' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Resolve up to four product IDs from widget settings.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<int>
	 */
	private function get_product_ids( $settings ) {
		$raw = isset( $settings['product_ids'] ) ? $settings['product_ids'] : array();

		if ( ! is_array( $raw ) ) {
			$raw = array( $raw );
		}

		$ids = array_values( array_unique( array_filter( array_map( 'absint', $raw ) ) ) );

		return array_slice( $ids, 0, 4 );
	}

	/**
	 * Get display label for a product tile.
	 *
	 * @param int                 $product_id Product ID.
	 * @param array<string,mixed> $settings   Widget settings.
	 * @return string
	 */
	private function get_product_label( $product_id, $settings ) {
		$source = isset( $settings['product_label_source'] ) ? $settings['product_label_source'] : 'badge';

		if ( 'title' === $source ) {
			$product = function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
			return $product ? $product->get_name() : get_the_title( $product_id );
		}

		if ( function_exists( 'webmz_pll_get_product_badge_text' ) ) {
			$badge = webmz_pll_get_product_badge_text( $product_id, '' );
			if ( '' !== $badge ) {
				return $badge;
			}
		}

		$product = function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
		return $product ? $product->get_name() : get_the_title( $product_id );
	}

	/**
	 * Render one product tile.
	 *
	 * @param int                 $product_id Product ID.
	 * @param array<string,mixed> $settings   Widget settings.
	 * @return void
	 */
	private function render_product_tile( $product_id, $settings ) {
		$label     = $this->get_product_label( $product_id, $settings );
		$thumb_url = function_exists( 'webmz_pll_get_product_thumb_url' )
			? webmz_pll_get_product_thumb_url( $product_id, 'thumbnail' )
			: get_the_post_thumbnail_url( $product_id, webmz_get_loop_image_size() );
		$link      = 'yes' === ( $settings['product_link'] ?? 'yes' );
		$permalink = get_permalink( $product_id );
		$tag       = ( $link && $permalink ) ? 'a' : 'div';
		$key       = 'ztpb_product_' . $product_id;

		$this->add_render_attribute( $key, 'class', 'webmz-ztpb__product' );

		if ( $link && $permalink ) {
			$this->add_render_attribute( $key, 'href', esc_url( $permalink ) );
		}

		?>
		<<?php echo tag_escape( $tag ); ?> <?php echo $this->get_render_attribute_string( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<span class="webmz-ztpb__product-thumb">
				<?php if ( $thumb_url ) : ?>
					<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $label ); ?>" loading="lazy" decoding="async">
				<?php else : ?>
					<span class="webmz-ztpb__product-thumb-placeholder" aria-hidden="true"></span>
				<?php endif; ?>
			</span>
			<?php if ( '' !== trim( $label ) ) : ?>
				<span class="webmz-ztpb__product-label"><?php echo esc_html( $label ); ?></span>
			<?php endif; ?>
		</<?php echo tag_escape( $tag ); ?>>
		<?php
	}

	/**
	 * Render one CTA button.
	 *
	 * @param string              $text_key Text setting key.
	 * @param string              $url_key  URL setting key.
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_button( $text_key, $url_key, $settings ) {
		$text = isset( $settings[ $text_key ] ) ? trim( (string) $settings[ $text_key ] ) : '';

		if ( '' === $text ) {
			return;
		}

		$url_settings = ! empty( $settings[ $url_key ] ) && is_array( $settings[ $url_key ] ) ? $settings[ $url_key ] : array();
		$key          = 'ztpb_' . $text_key;

		$this->add_render_attribute( $key, 'class', 'webmz-ztpb__btn' );

		if ( ! empty( $url_settings['url'] ) ) {
			$this->add_link_attributes( $key, $url_settings );
		} else {
			$this->add_render_attribute( $key, 'href', '#' );
		}

		?>
		<a <?php echo $this->get_render_attribute_string( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php echo esc_html( $text ); ?>
		</a>
		<?php
	}

	protected function render() {
		if ( ! function_exists( 'wc_get_product' ) ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'برای استفاده از این ویجت، ووکامرس باید فعال باشد.', 'tadris' ) . '</div>';
			return;
		}

		$settings    = $this->get_settings_for_display();
		$product_ids = $this->get_product_ids( $settings );
		$title_tag   = $this->webmz_get_title_tag( $settings );
		$show_decor  = 'yes' === ( $settings['show_decor'] ?? 'yes' );
		$classes     = 'webmz-ztpb' . ( $show_decor ? ' webmz-ztpb--decor' : '' );

		if ( empty( $product_ids ) ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( '۴ محصول را از بخش محتوا انتخاب کنید.', 'tadris' ) . '</div>';
			return;
		}
		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<?php if ( $show_decor ) : ?>
				<span class="webmz-ztpb__decor" aria-hidden="true"></span>
			<?php endif; ?>

			<div class="webmz-ztpb__products">
				<?php foreach ( $product_ids as $product_id ) : ?>
					<?php $this->render_product_tile( $product_id, $settings ); ?>
				<?php endforeach; ?>
			</div>

			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<<?php echo esc_html( $title_tag ); ?> class="webmz-ztpb__title"><?php echo esc_html( $settings['title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
			<?php endif; ?>

			<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
				<p class="webmz-ztpb__subtitle"><?php echo esc_html( $settings['subtitle'] ); ?></p>
			<?php endif; ?>

			<div class="webmz-ztpb__buttons">
				<?php $this->render_button( 'button_1_text', 'button_1_url', $settings ); ?>
				<?php $this->render_button( 'button_2_text', 'button_2_url', $settings ); ?>
			</div>
		</div>
		<?php
	}
}
