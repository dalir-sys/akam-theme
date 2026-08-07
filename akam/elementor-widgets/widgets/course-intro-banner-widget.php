<?php
/**
 * Course intro banner widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Horizontal course introduction banner with product data.
 */
class Course_Intro_Banner_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-course-intro-banner';
	}

	public function get_title() {
		return esc_html__( 'بنر معرفی دوره', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	public function get_categories() {
		return array( 'webmz-shop-widgets' );
	}

	public function get_keywords() {
		return array( 'course', 'product', 'banner', 'دوره', 'محصول', 'بنر' );
	}

	public function get_style_depends() {
		return array( 'webmz-course-intro-banner' );
	}

	protected function register_controls() {
		$this->register_product_controls();
		$this->register_stats_controls();
		$this->register_display_controls();
		$this->register_style_controls();
	}

	protected function register_product_controls() {
		$this->start_controls_section( 'section_product', array( 'label' => esc_html__( 'محصول', 'tadris' ) ) );

		$this->add_control(
			'product_id',
			array(
				'label'       => esc_html__( 'انتخاب دوره', 'tadris' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => \webmz_tadris_get_product_options(),
				'label_block' => true,
			)
		);

		$this->end_controls_section();
	}

	protected function register_stats_controls() {
		$this->start_controls_section( 'section_stats', array( 'label' => esc_html__( 'آمار', 'tadris' ) ) );

		$this->add_control(
			'sales_icon',
			array(
				'label' => esc_html__( 'آیکون فروش', 'tadris' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'sales_source',
			array(
				'label'   => esc_html__( 'منبع فروش', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'product',
				'options' => array(
					'product' => esc_html__( 'از محصول', 'tadris' ),
					'manual'  => esc_html__( 'دستی', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'sales_manual',
			array(
				'label'     => esc_html__( 'تعداد فروش', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'min'       => 0,
				'condition' => array( 'sales_source' => 'manual' ),
			)
		);

		$this->add_control(
			'sales_label',
			array(
				'label'   => esc_html__( 'برچسب فروش', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'فروش:', 'tadris' ),
			)
		);

		$this->add_control(
			'satisfaction_icon',
			array(
				'label'     => esc_html__( 'آیکون رضایت', 'tadris' ),
				'type'      => Controls_Manager::MEDIA,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'satisfaction_source',
			array(
				'label'   => esc_html__( 'منبع رضایت', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => array(
					'rating' => esc_html__( 'از امتیاز محصول', 'tadris' ),
					'manual' => esc_html__( 'دستی', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'satisfaction_manual',
			array(
				'label'     => esc_html__( 'درصد رضایت', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 98,
				'min'       => 0,
				'max'       => 100,
				'condition' => array( 'satisfaction_source' => 'manual' ),
			)
		);

		$this->add_control(
			'satisfaction_label',
			array(
				'label'   => esc_html__( 'برچسب رضایت', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'رضایت:', 'tadris' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_display_controls() {
		$this->start_controls_section( 'section_display', array( 'label' => esc_html__( 'نمایش', 'tadris' ) ) );

		$this->add_control(
			'show_accent',
			array(
				'label'        => esc_html__( 'کادر بنفش تزئینی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'banner_image',
			array(
				'label'   => esc_html__( 'تصویر بنر', 'tadris' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'banner_image_size',
			array(
				'label'   => esc_html__( 'سایز رندر تصویر', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'large',
				'options' => array(
					'medium'       => esc_html__( 'متوسط', 'tadris' ),
					'medium_large' => esc_html__( 'متوسط بزرگ', 'tadris' ),
					'large'        => esc_html__( 'بزرگ', 'tadris' ),
					'full'         => esc_html__( 'کامل', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'link_banner_image',
			array(
				'label'        => esc_html__( 'لینک تصویر به صفحه محصول', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_media_frame',
			array(
				'label'        => esc_html__( 'قاب سفید پس‌زمینه تصویر', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'excerpt_words',
			array(
				'label'   => esc_html__( 'تعداد کلمات توضیحات', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 20,
				'min'     => 5,
				'max'     => 60,
			)
		);

		$this->add_control(
			'price_label',
			array(
				'label'   => esc_html__( 'برچسب قیمت', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'قیمت دوره', 'tadris' ),
			)
		);

		$this->add_control(
			'show_date',
			array(
				'label'        => esc_html__( 'تاریخ انتشار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'duration_icon',
			array(
				'label'   => esc_html__( 'آیکون مدت', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array( 'value' => 'far fa-clock', 'library' => 'fa-regular' ),
			)
		);

		$this->add_control(
			'sessions_icon',
			array(
				'label'   => esc_html__( 'آیکون جلسات', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array( 'value' => 'far fa-calendar', 'library' => 'fa-regular' ),
			)
		);

		$this->add_control(
			'date_icon',
			array(
				'label'   => esc_html__( 'آیکون تاریخ', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array( 'value' => 'far fa-calendar-check', 'library' => 'fa-regular' ),
			)
		);

		$this->add_control(
			'rating_icon',
			array(
				'label'   => esc_html__( 'آیکون ستاره', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array( 'value' => 'fas fa-star', 'library' => 'fa-solid' ),
			)
		);

		$this->webmz_register_title_tag_control();

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->webmz_register_box_style_controls(
			'style_panel',
			esc_html__( 'کادر بنر', 'tadris' ),
			'.webmz-cib',
			array( 'default_background' => '#f3f4f6' )
		);

		$this->start_controls_section( 'style_accent', array(
			'label' => esc_html__( 'کادر بنفش', 'tadris' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'accent_color', array(
			'label'     => esc_html__( 'رنگ', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#7c5cfc',
			'selectors' => array( '{{WRAPPER}} .webmz-cib__accent' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_responsive_control( 'accent_width', array(
			'label'      => esc_html__( 'عرض', 'tadris' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 48, 'max' => 200 ) ),
			'default'    => array( 'unit' => 'px', 'size' => 100 ),
			'selectors'  => array( '{{WRAPPER}} .webmz-cib__accent' => 'width: {{SIZE}}{{UNIT}};' ),
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'style_media', array(
			'label' => esc_html__( 'تصویر بنر', 'tadris' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_responsive_control( 'media_width', array(
			'label'      => esc_html__( 'عرض', 'tadris' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 100, 'max' => 280 ) ),
			'default'    => array( 'unit' => 'px', 'size' => 190 ),
			'selectors'  => array( '{{WRAPPER}} .webmz-cib__media-img' => 'width: {{SIZE}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'media_overflow', array(
			'label'      => esc_html__( 'بیرون‌زدن از پایین', 'tadris' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
			'default'    => array( 'unit' => 'px', 'size' => 28 ),
			'selectors'  => array( '{{WRAPPER}} .webmz-cib__media' => 'margin-bottom: calc(-1 * {{SIZE}}{{UNIT}});' ),
		) );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array(
			'name'      => 'media_shadow',
			'selector'  => '{{WRAPPER}} .webmz-cib__media-frame',
			'condition' => array( 'show_media_frame' => 'yes' ),
		) );

		$this->end_controls_section();

		$this->webmz_register_text_style_controls( 'style_title', esc_html__( 'عنوان', 'tadris' ), '.webmz-cib__title' );
		$this->webmz_register_text_style_controls( 'style_price', esc_html__( 'قیمت', 'tadris' ), '.webmz-cib__price-value' );

		$this->start_controls_section( 'style_stats', array(
			'label' => esc_html__( 'کارت آمار', 'tadris' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'stat_bg', array(
			'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => array( '{{WRAPPER}} .webmz-cib__stat' => 'background-color: {{VALUE}};' ),
		) );

		$this->end_controls_section();
	}

	/** @return \WC_Product|false */
	private function get_product( $settings ) {
		if ( ! function_exists( 'wc_get_product' ) || empty( $settings['product_id'] ) ) {
			return false;
		}
		$product = wc_get_product( absint( $settings['product_id'] ) );
		return ( $product && 'publish' === get_post_status( $product->get_id() ) ) ? $product : false;
	}

	private function format_number( $n ) {
		$out = number_format_i18n( $n );
		return function_exists( 'webmz_to_persian_digits' ) ? webmz_to_persian_digits( $out ) : $out;
	}

	private function icon_html( $settings, $key ) {
		if ( empty( $settings[ $key ]['value'] ) ) {
			return '';
		}
		ob_start();
		Icons_Manager::render_icon( $settings[ $key ], array( 'aria-hidden' => 'true' ) );
		return (string) ob_get_clean();
	}

	private function stat_icon_html( $settings, $key, $class ) {
		$img = $settings[ $key ] ?? array();
		if ( ! empty( $img['id'] ) ) {
			return wp_get_attachment_image( absint( $img['id'] ), 'thumbnail', false, array( 'class' => $class ) );
		}
		if ( ! empty( $img['url'] ) ) {
			return sprintf( '<img class="%1$s" src="%2$s" alt="" loading="lazy">', esc_attr( $class ), esc_url( $img['url'] ) );
		}
		return '';
	}

	private function get_title_html( $product ) {
		if ( function_exists( 'webmz_spw_get_product_title_html' ) ) {
			$html = webmz_spw_get_product_title_html( $product->get_id() );
			if ( '' !== trim( wp_strip_all_tags( $html ) ) ) {
				return $html;
			}
		}
		return esc_html( $product->get_name() );
	}

	private function get_excerpt( $product, $words ) {
		$id = $product->get_id();
		if ( function_exists( 'webmz_spw_get_product_short_description' ) ) {
			$custom = trim( (string) webmz_spw_get_product_short_description( $id ) );
			if ( '' !== $custom ) {
				return wp_trim_words( wp_strip_all_tags( $custom ), $words, '…' );
			}
		}
		if ( function_exists( 'webmz_get_product_loop_short_excerpt' ) ) {
			return webmz_get_product_loop_short_excerpt( $product, $words );
		}
		$text = trim( wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ) );
		return '' !== $text ? wp_trim_words( $text, $words, '…' ) : '';
	}

	private function get_date_label( $product_id ) {
		$ts = (int) get_post_time( 'U', true, $product_id );
		if ( ! $ts ) {
			return '';
		}
		$diff = human_time_diff( $ts, current_time( 'timestamp' ) );
		return function_exists( 'webmz_to_persian_digits' )
			? webmz_to_persian_digits( $diff ) . ' ' . esc_html__( 'پیش', 'tadris' )
			: $diff . ' ' . esc_html__( 'پیش', 'tadris' );
	}

	/**
	 * Render banner image markup.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @param \WC_Product         $product  Product object.
	 * @return string
	 */
	private function get_banner_image_html( $settings, $product ) {
		$image = $settings['banner_image'] ?? array();
		$size  = ! empty( $settings['banner_image_size'] ) ? sanitize_key( $settings['banner_image_size'] ) : 'large';
		$alt   = $product->get_name();

		if ( ! empty( $image['id'] ) ) {
			return wp_get_attachment_image(
				absint( $image['id'] ),
				$size,
				false,
				array(
					'class'    => 'webmz-cib__media-img',
					'alt'      => $alt,
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);
		}

		if ( ! empty( $image['url'] ) ) {
			return sprintf(
				'<img class="webmz-cib__media-img" src="%s" alt="%s" loading="lazy" decoding="async">',
				esc_url( $image['url'] ),
				esc_attr( $alt )
			);
		}

		return '';
	}

	protected function render() {
		if ( ! function_exists( 'wc_get_product' ) ) {
			echo '<p class="webmz-editor-placeholder">' . esc_html__( 'ووکامرس فعال نیست.', 'tadris' ) . '</p>';
			return;
		}

		$s       = $this->get_settings_for_display();
		$product = $this->get_product( $s );

		if ( ! $product ) {
			echo '<p class="webmz-editor-placeholder">' . esc_html__( 'یک محصول انتخاب کنید.', 'tadris' ) . '</p>';
			return;
		}

		$pid         = $product->get_id();
		$title_tag   = $this->webmz_get_title_tag( $s, 'title_tag' );
		$url         = $product->get_permalink();
		$rating      = (float) $product->get_average_rating();
		$meta        = function_exists( 'webmz_get_product_loop_card_v2_meta' )
			? webmz_get_product_loop_card_v2_meta( $product )
			: array( 'duration' => '', 'sessions' => 0 );
		$price       = function_exists( 'webmz_get_product_loop_price_data' )
			? webmz_get_product_loop_price_data( $product )
			: array( 'state' => 'unavailable', 'amount' => '', 'currency' => '' );
		$words       = max( 5, absint( $s['excerpt_words'] ?? 20 ) );
		$excerpt     = $this->get_excerpt( $product, $words );
		$sales       = ( 'manual' === ( $s['sales_source'] ?? 'product' ) )
			? absint( $s['sales_manual'] ?? 0 )
			: absint( $product->get_total_sales() );
		$satisfaction = ( 'manual' === ( $s['satisfaction_source'] ?? 'manual' ) )
			? min( 100, absint( $s['satisfaction_manual'] ?? 0 ) )
			: min( 100, (int) round( ( $rating / 5 ) * 100 ) );
		$show_accent      = ! isset( $s['show_accent'] ) || 'yes' === $s['show_accent'];
		$show_date        = ! isset( $s['show_date'] ) || 'yes' === $s['show_date'];
		$show_media_frame = ! isset( $s['show_media_frame'] ) || 'yes' === $s['show_media_frame'];
		$banner_img  = $this->get_banner_image_html( $s, $product );
		$link_image  = ! isset( $s['link_banner_image'] ) || 'yes' === $s['link_banner_image'];
		?>
		<div class="webmz-cib">
			<?php if ( $show_accent ) : ?>
				<div class="webmz-cib__accent" aria-hidden="true"></div>
			<?php endif; ?>

			<div class="webmz-cib__stats">
				<div class="webmz-cib__stat">
					<?php echo $this->stat_icon_html( $s, 'sales_icon', 'webmz-cib__stat-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div class="webmz-cib__stat-text">
						<span><?php echo esc_html( $s['sales_label'] ?? esc_html__( 'فروش:', 'tadris' ) ); ?></span>
						<strong><?php echo esc_html( $this->format_number( $sales ) ); ?></strong>
					</div>
				</div>
				<div class="webmz-cib__stat">
					<?php echo $this->stat_icon_html( $s, 'satisfaction_icon', 'webmz-cib__stat-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div class="webmz-cib__stat-text">
						<span><?php echo esc_html( $s['satisfaction_label'] ?? esc_html__( 'رضایت:', 'tadris' ) ); ?></span>
						<strong><?php echo esc_html( $this->format_number( $satisfaction ) . '%' ); ?></strong>
					</div>
				</div>
			</div>

			<div class="webmz-cib__body">
				<<?php echo esc_html( $title_tag ); ?> class="webmz-cib__title">
					<a href="<?php echo esc_url( $url ); ?>"><?php echo $this->get_title_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</<?php echo esc_html( $title_tag ); ?>>

				<?php if ( $rating > 0 && function_exists( 'webmz_render_product_loop_rating_stars_v2' ) ) : ?>
					<div class="webmz-cib__stars">
						<?php webmz_render_product_loop_rating_stars_v2( $rating, $this->icon_html( $s, 'rating_icon' ) ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $excerpt ) : ?>
					<p class="webmz-cib__excerpt"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>

				<ul class="webmz-cib__meta">
					<?php if ( ! empty( $meta['duration'] ) ) : ?>
						<li><?php echo $this->icon_html( $s, 'duration_icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( function_exists( 'webmz_to_persian_digits' ) ? webmz_to_persian_digits( $meta['duration'] ) : $meta['duration'] ); ?></span></li>
					<?php endif; ?>
					<?php if ( ! empty( $meta['sessions'] ) ) : ?>
						<li><?php echo $this->icon_html( $s, 'sessions_icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( $this->format_number( $meta['sessions'] ) . ' ' . esc_html__( 'جلسه', 'tadris' ) ); ?></span></li>
					<?php endif; ?>
					<?php if ( $show_date && ( $date = $this->get_date_label( $pid ) ) ) : ?>
						<li><?php echo $this->icon_html( $s, 'date_icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( $date ); ?></span></li>
					<?php endif; ?>
				</ul>

				<div class="webmz-cib__price">
					<span class="webmz-cib__price-label"><?php echo esc_html( $s['price_label'] ?? esc_html__( 'قیمت دوره', 'tadris' ) ); ?></span>
					<div class="webmz-cib__price-value">
						<?php if ( 'unavailable' === $price['state'] ) : ?>
							<span><?php esc_html_e( 'ناموجود', 'tadris' ); ?></span>
						<?php elseif ( 'free' === $price['state'] ) : ?>
							<span><?php esc_html_e( 'رایگان', 'tadris' ); ?></span>
						<?php else : ?>
							<?php if ( ! empty( $price['regular_amount'] ) ) : ?>
								<del><?php echo esc_html( $price['regular_amount'] . ' ' . $price['currency'] ); ?></del>
							<?php endif; ?>
							<strong><?php echo esc_html( $price['amount'] . ' ' . $price['currency'] ); ?></strong>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<?php if ( $banner_img ) : ?>
				<div class="webmz-cib__media<?php echo $show_media_frame ? '' : ' webmz-cib__media--plain'; ?>">
					<?php if ( $link_image ) : ?>
						<a class="webmz-cib__media-frame" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
							<?php echo $banner_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php else : ?>
						<div class="webmz-cib__media-frame">
							<?php echo $banner_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
