<?php
/**
 * Customer testimonials slider widget (layout 2).
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * RTL testimonial cards slider with quote icon and footer avatar layout.
 */
class Customer_Testimonials_Slider_2_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-customer-testimonials-slider-2';
	}

	public function get_title() {
		return esc_html__( 'اسلایدر نظرات مشتریان ۲', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-customer-testimonials-slider-2' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-customer-testimonials-slider-2' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_slider_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'نظرات', 'tadris' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'avatar',
			array(
				'label'   => esc_html__( 'تصویر پروفایل', 'tadris' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'       => esc_html__( 'نام', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'محمد کریمی', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'job_title',
			array(
				'label'       => esc_html__( 'دوره / عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دوره زبان فرانسه', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'rating',
			array(
				'label'   => esc_html__( 'امتیاز ستاره', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5,
				'min'     => 0,
				'max'     => 5,
				'step'    => 1,
			)
		);

		$repeater->add_control(
			'content',
			array(
				'label'       => esc_html__( 'متن نظر', 'tadris' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است.', 'tadris' ),
				'label_block' => true,
				'rows'        => 5,
			)
		);

		$default_text = esc_html__( 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است.', 'tadris' );

		$this->add_control(
			'testimonials',
			array(
				'label'       => esc_html__( 'لیست نظرات', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => array(
					array(
						'name'      => esc_html__( 'محمد کریمی', 'tadris' ),
						'job_title' => esc_html__( 'دوره زبان فرانسه', 'tadris' ),
						'rating'    => 5,
						'content'   => $default_text,
					),
					array(
						'name'      => esc_html__( 'مریم حسینی', 'tadris' ),
						'job_title' => esc_html__( 'دوره زبان ترکی', 'tadris' ),
						'rating'    => 5,
						'content'   => $default_text,
					),
					array(
						'name'      => esc_html__( 'علی رضایی', 'tadris' ),
						'job_title' => esc_html__( 'دوره زبان آلمانی', 'tadris' ),
						'rating'    => 5,
						'content'   => $default_text,
					),
					array(
						'name'      => esc_html__( 'نگار محمدی', 'tadris' ),
						'job_title' => esc_html__( 'دوره مکالمه انگلیسی', 'tadris' ),
						'rating'    => 5,
						'content'   => $default_text,
					),
				),
			)
		);

		$this->add_control(
			'quote_icon',
			array(
				'label'   => esc_html__( 'آیکون نقل‌قول', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-quote-right',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'star_icon',
			array(
				'label'   => esc_html__( 'آیکون ستاره', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_slider_controls() {
		$this->start_controls_section(
			'slider_settings',
			array(
				'label' => esc_html__( 'تنظیمات اسلایدر', 'tadris' ),
			)
		);

		foreach ( array( 'desktop' => esc_html__( 'دسکتاپ', 'tadris' ), 'tablet' => esc_html__( 'تبلت', 'tadris' ), 'mobile' => esc_html__( 'موبایل', 'tadris' ) ) as $device => $label ) {
			$this->add_control(
				'slides_' . $device,
				array(
					'label'   => sprintf( esc_html__( 'تعداد نمایش %s', 'tadris' ), $label ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 'mobile' === $device ? 1 : ( 'tablet' === $device ? 2 : 4 ),
					'min'     => 1,
					'max'     => 6,
				)
			);
		}

		$this->add_control(
			'space_between',
			array(
				'label'   => esc_html__( 'فاصله بین کارت‌ها (px)', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 20,
				'min'     => 0,
				'max'     => 80,
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => esc_html__( 'حلقه بی‌نهایت', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'پخش خودکار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => esc_html__( 'تأخیر پخش خودکار (ms)', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'min'       => 1000,
				'max'       => 15000,
				'step'      => 500,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'show_navigation',
			array(
				'label'        => esc_html__( 'نمایش فلش‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'prev_icon',
			array(
				'label'     => esc_html__( 'آیکون قبلی', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				),
				'condition' => array( 'show_navigation' => 'yes' ),
			)
		);

		$this->add_control(
			'next_icon',
			array(
				'label'     => esc_html__( 'آیکون بعدی', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
				'condition' => array( 'show_navigation' => 'yes' ),
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'        => esc_html__( 'نمایش صفحه‌بندی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->webmz_register_box_style_controls(
			'style_container',
			esc_html__( 'کانتینر', 'tadris' ),
			'.webmz-cts2',
			array( 'default_background' => 'transparent' )
		);

		$this->webmz_register_box_style_controls(
			'style_card',
			esc_html__( 'کارت نظر', 'tadris' ),
			'.webmz-cts2__card',
			array(
				'default_background' => '#ffffff',
				'bordered'           => true,
			)
		);

		$this->start_controls_section(
			'style_quote',
			array(
				'label' => esc_html__( 'آیکون نقل‌قول', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'quote_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون', 'tadris' )
		);

		$this->add_control(
			'quote_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e53935',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts2__quote' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'quote_size',
			array(
				'label'      => esc_html__( 'اندازه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 16, 'max' => 48 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 28,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cts2__quote' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-cts2__quote svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_avatar',
			array(
				'label' => esc_html__( 'آواتار', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'avatar_size',
			array(
				'label'      => esc_html__( 'اندازه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 36, 'max' => 96 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 52,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cts2__avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_stars',
			array(
				'label' => esc_html__( 'ستاره‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'star_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون ستاره', 'tadris' )
		);

		$this->add_control(
			'star_color',
			array(
				'label'     => esc_html__( 'رنگ ستاره فعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f5b301',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts2__star.is-filled' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'star_empty_color',
			array(
				'label'     => esc_html__( 'رنگ ستاره خالی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e2e8f0',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts2__star:not(.is-filled)' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'star_size',
			array(
				'label'      => esc_html__( 'اندازه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 10, 'max' => 24 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 14,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cts2__star' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-cts2__star svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls( 'style_name', esc_html__( 'نام', 'tadris' ), '.webmz-cts2__name' );
		$this->webmz_register_text_style_controls( 'style_job', esc_html__( 'دوره / عنوان', 'tadris' ), '.webmz-cts2__job' );
		$this->webmz_register_text_style_controls( 'style_content', esc_html__( 'متن نظر', 'tadris' ), '.webmz-cts2__text' );

		$this->start_controls_section(
			'style_nav',
			array(
				'label' => esc_html__( 'دکمه‌های ناوبری', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'nav_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون فلش', 'tadris' )
		);

		$this->add_control(
			'nav_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts2__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#94a3b8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts2__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_hover_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts2__arrow:hover:not(.swiper-button-disabled)' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_hover_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts2__arrow:hover:not(.swiper-button-disabled)' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_size',
			array(
				'label'      => esc_html__( 'اندازه دکمه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 32, 'max' => 64 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 44,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cts2__arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_pagination',
			array(
				'label' => esc_html__( 'صفحه‌بندی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => esc_html__( 'رنگ غیرفعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d1d5db',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts2__pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_active_color',
			array(
				'label'     => esc_html__( 'رنگ فعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e53935',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts2__pagination .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render avatar image.
	 *
	 * @param array<string,mixed> $image Image settings.
	 * @param string              $name  Person name for alt text.
	 * @return void
	 */
	protected function render_avatar( $image, $name = '' ) {
		$image = is_array( $image ) ? $image : array();
		$alt   = $name ? $name : esc_attr__( 'تصویر مشتری', 'tadris' );

		if ( ! empty( $image['id'] ) ) {
			echo wp_get_attachment_image(
				absint( $image['id'] ),
				'thumbnail',
				false,
				array(
					'class' => 'webmz-cts2__avatar-img',
					'alt'   => $alt,
				)
			);
			return;
		}

		$url = ! empty( $image['url'] ) ? $image['url'] : Utils::get_placeholder_image_src();
		printf(
			'<img class="webmz-cts2__avatar-img" src="%s" alt="%s">',
			esc_url( $url ),
			esc_attr( $alt )
		);
	}

	/**
	 * Render rating stars for a testimonial item.
	 *
	 * @param int                 $rating           Rating value.
	 * @param array<string,mixed> $icon             Elementor icon settings.
	 * @param string              $icon_color_class SVG color mode class.
	 * @return void
	 */
	protected function render_stars( $rating, $icon, $icon_color_class = '' ) {
		$rating = max( 0, min( 5, absint( $rating ) ) );
		ob_start();
		Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
		$icon_html   = ob_get_clean();
		$stars_class = 'webmz-cts2__stars';

		if ( $icon_color_class ) {
			$stars_class .= ' ' . $icon_color_class;
		}
		?>
		<div class="<?php echo esc_attr( $stars_class ); ?>" aria-label="<?php echo esc_attr( sprintf( esc_html__( 'امتیاز %1$d از 5', 'tadris' ), $rating ) ); ?>">
			<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
				<span class="webmz-cts2__star <?php echo $rating >= $i ? 'is-filled' : ''; ?>" aria-hidden="true">
					<?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
			<?php endfor; ?>
		</div>
		<?php
	}

	protected function render() {
		$settings        = $this->get_settings_for_display();
		$testimonials    = ! empty( $settings['testimonials'] ) && is_array( $settings['testimonials'] ) ? $settings['testimonials'] : array();
		$show_nav        = 'yes' === ( $settings['show_navigation'] ?? 'yes' );
		$show_pagination = 'yes' === ( $settings['show_pagination'] ?? 'yes' );
		$quote_icon      = ! empty( $settings['quote_icon'] ) ? $settings['quote_icon'] : array( 'value' => 'fas fa-quote-right', 'library' => 'fa-solid' );
		$star_icon       = ! empty( $settings['star_icon'] ) ? $settings['star_icon'] : array( 'value' => 'fas fa-star', 'library' => 'fa-solid' );
		$quote_icon_class = $this->webmz_get_icon_color_mode_class( $settings, 'quote_icon_color_mode' );
		$star_icon_class  = $this->webmz_get_icon_color_mode_class( $settings, 'star_icon_color_mode' );
		$nav_icon_class   = $this->webmz_get_icon_color_mode_class( $settings, 'nav_icon_color_mode' );

		if ( empty( $testimonials ) ) {
			return;
		}

		$config = array(
			'slidesDesktop' => ! empty( $settings['slides_desktop'] ) ? absint( $settings['slides_desktop'] ) : 4,
			'slidesTablet'  => ! empty( $settings['slides_tablet'] ) ? absint( $settings['slides_tablet'] ) : 2,
			'slidesMobile'  => ! empty( $settings['slides_mobile'] ) ? absint( $settings['slides_mobile'] ) : 1,
			'spaceBetween'  => isset( $settings['space_between'] ) ? absint( $settings['space_between'] ) : 20,
			'loop'          => 'yes' === ( $settings['loop'] ?? '' ),
			'autoplay'      => 'yes' === ( $settings['autoplay'] ?? '' ),
			'autoplayDelay' => max( 1000, absint( $settings['autoplay_delay'] ?? 5000 ) ),
			'navigation'    => $show_nav,
			'pagination'    => $show_pagination,
		);
		?>
		<div class="webmz-cts2" dir="rtl">
			<div class="webmz-cts2__shell">
				<?php if ( $show_nav ) : ?>
					<button class="webmz-cts2__arrow webmz-cts2__arrow--prev <?php echo esc_attr( $nav_icon_class ); ?>" type="button" aria-label="<?php esc_attr_e( 'نظر قبلی', 'tadris' ); ?>">
						<?php Icons_Manager::render_icon( $settings['prev_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</button>
				<?php endif; ?>

				<div class="webmz-cts2__main">
					<div
						class="webmz-cts2__slider swiper"
						data-webmz-cts2-slider="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
					>
						<div class="swiper-wrapper">
							<?php foreach ( $testimonials as $item ) : ?>
								<?php
								$name      = ! empty( $item['name'] ) ? $item['name'] : '';
								$job_title = ! empty( $item['job_title'] ) ? $item['job_title'] : '';
								$content   = ! empty( $item['content'] ) ? $item['content'] : '';
								$rating    = isset( $item['rating'] ) ? absint( $item['rating'] ) : 5;
								?>
								<div class="swiper-slide">
									<article class="webmz-cts2__card">
										<div class="webmz-cts2__quote <?php echo esc_attr( $quote_icon_class ); ?>" aria-hidden="true">
											<?php Icons_Manager::render_icon( $quote_icon, array( 'aria-hidden' => 'true' ) ); ?>
										</div>

										<?php if ( $content ) : ?>
											<p class="webmz-cts2__text"><?php echo esc_html( $content ); ?></p>
										<?php endif; ?>

										<footer class="webmz-cts2__footer">
											<div class="webmz-cts2__avatar">
												<?php $this->render_avatar( $item['avatar'] ?? array(), $name ); ?>
											</div>

											<div class="webmz-cts2__meta">
												<?php if ( $rating > 0 ) : ?>
													<?php $this->render_stars( $rating, $star_icon, $star_icon_class ); ?>
												<?php endif; ?>

												<?php if ( $name ) : ?>
													<div class="webmz-cts2__name"><?php echo esc_html( $name ); ?></div>
												<?php endif; ?>

												<?php if ( $job_title ) : ?>
													<div class="webmz-cts2__job"><?php echo esc_html( $job_title ); ?></div>
												<?php endif; ?>
											</div>
										</footer>
									</article>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<?php if ( $show_pagination ) : ?>
						<div class="webmz-cts2__pagination swiper-pagination"></div>
					<?php endif; ?>
				</div>

				<?php if ( $show_nav ) : ?>
					<button class="webmz-cts2__arrow webmz-cts2__arrow--next <?php echo esc_attr( $nav_icon_class ); ?>" type="button" aria-label="<?php esc_attr_e( 'نظر بعدی', 'tadris' ); ?>">
						<?php Icons_Manager::render_icon( $settings['next_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
