<?php
/**
 * Customer testimonials slider widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * RTL testimonial cards slider with repeater items.
 */
class Customer_Testimonials_Slider_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-customer-testimonials-slider';
	}

	public function get_title() {
		return esc_html__( 'اسلایدر نظرات مشتریان', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-customer-testimonials-slider' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-customer-testimonials-slider' );
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
				'default'     => esc_html__( 'مرضیه شریفات', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'job_title',
			array(
				'label'       => esc_html__( 'سمت / عنوان شغلی', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'طراح گرافیک', 'tadris' ),
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
						'name'      => esc_html__( 'مرضیه شریفات', 'tadris' ),
						'job_title' => esc_html__( 'طراح گرافیک', 'tadris' ),
						'rating'    => 5,
						'content'   => $default_text,
					),
					array(
						'name'      => esc_html__( 'علی محمدی', 'tadris' ),
						'job_title' => esc_html__( 'توسعه‌دهنده وب', 'tadris' ),
						'rating'    => 5,
						'content'   => $default_text,
					),
					array(
						'name'      => esc_html__( 'سارا احمدی', 'tadris' ),
						'job_title' => esc_html__( 'مدیر محصول', 'tadris' ),
						'rating'    => 4,
						'content'   => $default_text,
					),
				),
			)
		);

		$this->add_control(
			'name_job_separator',
			array(
				'label'       => esc_html__( 'جداکننده نام و سمت', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => ' / ',
				'label_block' => true,
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
					'default' => 'mobile' === $device ? 1 : ( 'tablet' === $device ? 2 : 3 ),
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
				'default' => 24,
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

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->webmz_register_box_style_controls(
			'style_container',
			esc_html__( 'کانتینر', 'tadris' ),
			'.webmz-cts',
			array( 'default_background' => '#ffffff' )
		);

		$this->webmz_register_box_style_controls(
			'style_card',
			esc_html__( 'کارت نظر', 'tadris' ),
			'.webmz-cts__card',
			array(
				'default_background' => '#ffffff',
				'bordered'           => true,
			)
		);

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
					'px' => array( 'min' => 40, 'max' => 120 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 64,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cts__avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'avatar_border_color',
			array(
				'label'     => esc_html__( 'رنگ فاصله داخلی (حلقه سفید)', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts__avatar' => 'border-color: {{VALUE}}; background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'avatar_border_width',
			array(
				'label'     => esc_html__( 'ضخامت فاصله داخلی', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'min' => 0, 'max' => 12 ),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 3,
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts__avatar' => 'border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'avatar_ring_color',
			array(
				'label'     => esc_html__( 'رنگ حاشیه بیرونی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8e8e8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts__avatar' => '--webmz-cts-avatar-ring-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'avatar_ring_width',
			array(
				'label'     => esc_html__( 'ضخامت حاشیه بیرونی', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'min' => 0, 'max' => 6 ),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1,
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts__avatar' => '--webmz-cts-avatar-ring-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_offset_y',
			array(
				'label'      => esc_html__( 'جابه‌جایی عمودی', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => -60, 'max' => 20 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => -32,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cts__avatar' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_offset_x',
			array(
				'label'      => esc_html__( 'جابه‌جایی افقی', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cts__avatar' => 'inset-inline-end: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .webmz-cts__star.is-filled' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .webmz-cts__star:not(.is-filled)' => 'color: {{VALUE}};',
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
					'px' => array( 'min' => 10, 'max' => 32 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 14,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cts__star' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-cts__star svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'star_gap',
			array(
				'label'      => esc_html__( 'فاصله ستاره‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 12 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cts__stars' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls( 'style_name', esc_html__( 'نام', 'tadris' ), '.webmz-cts__name' );
		$this->webmz_register_text_style_controls( 'style_job', esc_html__( 'سمت', 'tadris' ), '.webmz-cts__job' );
		$this->webmz_register_text_style_controls( 'style_content', esc_html__( 'متن نظر', 'tadris' ), '.webmz-cts__text' );

		$this->start_controls_section(
			'style_header',
			array(
				'label' => esc_html__( 'سربرگ کارت', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'header_gap',
			array(
				'label'      => esc_html__( 'فاصله نام و ستاره‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cts__header' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_margin_bottom',
			array(
				'label'      => esc_html__( 'فاصله تا متن', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cts__header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

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
				'default'   => '#94a3b8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f1f5f9',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts__arrow' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .webmz-cts__arrow:hover:not(.swiper-button-disabled)' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_hover_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-secondary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cts__arrow:hover:not(.swiper-button-disabled)' => 'background-color: {{VALUE}};',
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
					'px' => array( 'min' => 28, 'max' => 72 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 44,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cts__arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_gap',
			array(
				'label'      => esc_html__( 'فاصله از اسلایدر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 48 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cts__shell' => 'gap: {{SIZE}}{{UNIT}};',
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
					'class' => 'webmz-cts__avatar-img',
					'alt'   => $alt,
				)
			);
			return;
		}

		$url = ! empty( $image['url'] ) ? $image['url'] : Utils::get_placeholder_image_src();
		printf(
			'<img class="webmz-cts__avatar-img" src="%s" alt="%s">',
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
		$icon_html = ob_get_clean();
		$stars_class = 'webmz-cts__stars';

		if ( $icon_color_class ) {
			$stars_class .= ' ' . $icon_color_class;
		}
		?>
		<div class="<?php echo esc_attr( $stars_class ); ?>" aria-label="<?php echo esc_attr( sprintf( esc_html__( 'امتیاز %1$d از 5', 'tadris' ), $rating ) ); ?>">
			<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
				<span class="webmz-cts__star <?php echo $rating >= $i ? 'is-filled' : ''; ?>" aria-hidden="true">
					<?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
			<?php endfor; ?>
		</div>
		<?php
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$testimonials  = ! empty( $settings['testimonials'] ) && is_array( $settings['testimonials'] ) ? $settings['testimonials'] : array();
		$show_nav      = 'yes' === ( $settings['show_navigation'] ?? 'yes' );
		$separator     = isset( $settings['name_job_separator'] ) ? (string) $settings['name_job_separator'] : ' / ';
		$star_icon     = ! empty( $settings['star_icon'] ) ? $settings['star_icon'] : array( 'value' => 'fas fa-star', 'library' => 'fa-solid' );
		$star_icon_class = $this->webmz_get_icon_color_mode_class( $settings, 'star_icon_color_mode' );
		$nav_icon_class  = $this->webmz_get_icon_color_mode_class( $settings, 'nav_icon_color_mode' );

		if ( empty( $testimonials ) ) {
			return;
		}

		$config = array(
			'slidesDesktop' => ! empty( $settings['slides_desktop'] ) ? absint( $settings['slides_desktop'] ) : 3,
			'slidesTablet'  => ! empty( $settings['slides_tablet'] ) ? absint( $settings['slides_tablet'] ) : 2,
			'slidesMobile'  => ! empty( $settings['slides_mobile'] ) ? absint( $settings['slides_mobile'] ) : 1,
			'spaceBetween'  => isset( $settings['space_between'] ) ? absint( $settings['space_between'] ) : 24,
			'loop'          => 'yes' === ( $settings['loop'] ?? '' ),
			'autoplay'      => 'yes' === ( $settings['autoplay'] ?? '' ),
			'autoplayDelay' => max( 1000, absint( $settings['autoplay_delay'] ?? 5000 ) ),
			'navigation'    => $show_nav,
		);
		?>
		<div class="webmz-cts" dir="rtl">
			<div class="webmz-cts__shell">
				<?php if ( $show_nav ) : ?>
					<button class="webmz-cts__arrow webmz-cts__arrow--prev <?php echo esc_attr( $nav_icon_class ); ?>" type="button" aria-label="<?php esc_attr_e( 'نظر قبلی', 'tadris' ); ?>">
						<?php Icons_Manager::render_icon( $settings['prev_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</button>
				<?php endif; ?>

				<div
					class="webmz-cts__slider swiper"
					data-webmz-cts-slider="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
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
								<article class="webmz-cts__card">
									<div class="webmz-cts__avatar">
										<?php $this->render_avatar( $item['avatar'] ?? array(), $name ); ?>
									</div>

									<div class="webmz-cts__header">
										<div class="webmz-cts__info">
											<?php if ( $name || $job_title ) : ?>
												<div class="webmz-cts__meta">
													<?php if ( $name ) : ?>
														<span class="webmz-cts__name"><?php echo esc_html( $name ); ?></span>
													<?php endif; ?>
													<?php if ( $name && $job_title && '' !== $separator ) : ?>
														<span class="webmz-cts__sep" aria-hidden="true"><?php echo esc_html( $separator ); ?></span>
													<?php endif; ?>
													<?php if ( $job_title ) : ?>
														<span class="webmz-cts__job"><?php echo esc_html( $job_title ); ?></span>
													<?php endif; ?>
												</div>
											<?php endif; ?>

											<?php if ( $rating > 0 ) : ?>
												<?php $this->render_stars( $rating, $star_icon, $star_icon_class ); ?>
											<?php endif; ?>
										</div>
									</div>

									<?php if ( $content ) : ?>
										<p class="webmz-cts__text"><?php echo esc_html( $content ); ?></p>
									<?php endif; ?>
								</article>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( $show_nav ) : ?>
					<button class="webmz-cts__arrow webmz-cts__arrow--next <?php echo esc_attr( $nav_icon_class ); ?>" type="button" aria-label="<?php esc_attr_e( 'نظر بعدی', 'tadris' ); ?>">
						<?php Icons_Manager::render_icon( $settings['next_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
