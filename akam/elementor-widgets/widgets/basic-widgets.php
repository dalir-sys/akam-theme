<?php
/**
 * Simple visual widgets that belong exclusively to the WebMZ Elementor category.
 *
 * @package WebMZ
 */
namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Hero banner widget for quick landing-page sections.
 */
class Hero_Banner_Widget extends Widget_Base {
	public function get_name() { return 'webmz-hero-banner'; }
	public function get_title() { return esc_html__( 'بنر معرفی آکام', 'tadris' ); }
	public function get_icon() { return 'eicon-call-to-action'; }
	public function get_categories() { return array( 'webmz-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'eyebrow', array(
			'label'   => esc_html__( 'برچسب کوچک', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'آکام - طراحی سریع', 'tadris' ),
		) );
		$this->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان', 'tadris' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'سایت حرفه ای خود را با المنتور طراحی کنید', 'tadris' ),
		) );
		$this->add_control( 'description', array(
			'label'   => esc_html__( 'توضیحات', 'tadris' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'یک بخش معرفی ساده و تمیز برای صفحات اصلی یا لندینگ پیج های شما.', 'tadris' ),
		) );
		$this->add_control( 'button_text', array(
			'label'   => esc_html__( 'متن دکمه', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'شروع کنید', 'tadris' ),
		) );
		$this->add_control( 'button_url', array(
			'label'   => esc_html__( 'لینک دکمه', 'tadris' ),
			'type'    => Controls_Manager::URL,
			'default' => array( 'url' => '#' ),
		) );
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		$url      = isset( $settings['button_url']['url'] ) ? $settings['button_url']['url'] : '#';
		?>
		<section class="webmz-widget-hero">
			<div class="webmz-widget-hero__content">
				<?php if ( ! empty( $settings['eyebrow'] ) ) : ?><span class="webmz-kicker"><?php echo esc_html( $settings['eyebrow'] ); ?></span><?php endif; ?>
				<h2><?php echo esc_html( $settings['title'] ); ?></h2>
				<p><?php echo esc_html( $settings['description'] ); ?></p>
				<?php if ( ! empty( $settings['button_text'] ) ) : ?><a class="webmz-button" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $settings['button_text'] ); ?></a><?php endif; ?>
			</div>
			<div class="webmz-widget-hero__visual" aria-hidden="true"><div></div><div></div><div></div></div>
		</section>
		<?php
	}
}

/**
 * Page hero section with content, dual buttons, and framed image.
 */
class Page_Hero_Widget extends Widget_Base {
	public function get_name() { return 'webmz-page-hero'; }
	public function get_title() { return esc_html__( 'هیرو برگه‌ها آکام', 'tadris' ); }
	public function get_icon() { return 'eicon-featured-image'; }
	public function get_categories() { return array( 'webmz-widgets' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'eyebrow', array(
			'label'       => esc_html__( 'متن بالای عنوان', 'tadris' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'پل ارتباطی بین ما و شما', 'tadris' ),
			'label_block' => true,
		) );
		$this->add_control( 'title_tag', array(
			'label'   => esc_html__( 'تگ عنوان اصلی', 'tadris' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h1',
			'options' => array(
				'h1'  => 'H1',
				'h2'  => 'H2',
				'h3'  => 'H3',
				'h4'  => 'H4',
				'h5'  => 'H5',
				'h6'  => 'H6',
				'div' => 'div',
			),
		) );
		$this->add_control( 'title', array(
			'label'       => esc_html__( 'عنوان اصلی', 'tadris' ),
			'type'        => Controls_Manager::TEXTAREA,
			'default'     => wp_kses_post( __( 'با الفارس در <strong>تماس</strong> باشید', 'tadris' ) ),
			'label_block' => true,
			'description' => esc_html__( 'برای رنگی شدن بخشی از عنوان، آن را داخل تگ strong قرار دهید.', 'tadris' ),
		) );
		$this->add_control( 'description', array(
			'label'       => esc_html__( 'توضیحات', 'tadris' ),
			'type'        => Controls_Manager::TEXTAREA,
			'default'     => esc_html__( 'اگر سوالی درباره دوره‌ها دارید، نیاز به راهنمایی یا انتخاب مسیر آموزشی خود احساس می‌کنید یا پیشنهادی برای بهبود خدمات و محتوای سایت دارید، خوشحال می‌شویم از شما بشنویم.', 'tadris' ),
			'label_block' => true,
		) );
		$this->add_control( 'primary_button_text', array(
			'label'   => esc_html__( 'متن دکمه اول', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'فرم درخواست ارتباط', 'tadris' ),
		) );
		$this->add_control( 'primary_button_link', array(
			'label'   => esc_html__( 'لینک دکمه اول', 'tadris' ),
			'type'    => Controls_Manager::URL,
			'default' => array( 'url' => '#' ),
		) );
		$this->add_control( 'secondary_button_text', array(
			'label'   => esc_html__( 'متن دکمه دوم', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'مشاهده اطلاعات تماس', 'tadris' ),
		) );
		$this->add_control( 'secondary_button_link', array(
			'label'   => esc_html__( 'لینک دکمه دوم', 'tadris' ),
			'type'    => Controls_Manager::URL,
			'default' => array( 'url' => '#' ),
		) );
		$this->add_control( 'image', array(
			'label'   => esc_html__( 'عکس', 'tadris' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => array( 'url' => Utils::get_placeholder_image_src() ),
		) );
		$this->add_control( 'image_size', array(
			'label'       => esc_html__( 'سایز نمایش عکس', 'tadris' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'large',
			'options'     => array(
				'thumbnail'    => esc_html__( 'بندانگشتی', 'tadris' ),
				'medium'       => esc_html__( 'متوسط', 'tadris' ),
				'medium_large' => esc_html__( 'متوسط بزرگ', 'tadris' ),
				'large'        => esc_html__( 'بزرگ', 'tadris' ),
				'full'         => esc_html__( 'کامل (اصلی)', 'tadris' ),
			),
			'description' => esc_html__( 'سایز کوچک‌تر، حجم و رزولوشن بارگذاری تصویر را کمتر می‌کند.', 'tadris' ),
		) );
		$this->end_controls_section();

		$this->start_controls_section(
			'style_layout',
			array(
				'label' => esc_html__( 'چیدمان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control( 'content_align', array(
			'label'     => esc_html__( 'چینش متن', 'tadris' ),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => array(
				'right'  => array( 'title' => esc_html__( 'راست', 'tadris' ), 'icon' => 'eicon-text-align-right' ),
				'center' => array( 'title' => esc_html__( 'وسط', 'tadris' ), 'icon' => 'eicon-text-align-center' ),
				'left'   => array( 'title' => esc_html__( 'چپ', 'tadris' ), 'icon' => 'eicon-text-align-left' ),
			),
			'default'   => 'right',
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__content' => 'text-align: {{VALUE}};',
				'{{WRAPPER}} .webmz-page-hero__actions' => 'justify-content: {{VALUE}};',
			),
		) );
		$this->add_responsive_control( 'columns_gap', array(
			'label'      => esc_html__( 'فاصله متن و عکس', 'tadris' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', 'rem' ),
			'range'      => array(
				'px'  => array( 'min' => 0, 'max' => 160 ),
				'rem' => array( 'min' => 0, 'max' => 10 ),
			),
			'selectors'  => array(
				'{{WRAPPER}} .webmz-page-hero' => 'gap: {{SIZE}}{{UNIT}};',
			),
		) );
		$this->add_responsive_control( 'section_padding', array(
			'label'      => esc_html__( 'فاصله داخلی سکشن', 'tadris' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em', '%' ),
			'selectors'  => array(
				'{{WRAPPER}} .webmz-page-hero' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );
		$this->end_controls_section();

		$this->start_controls_section(
			'style_eyebrow',
			array(
				'label' => esc_html__( 'متن بالای عنوان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control( 'eyebrow_color', array(
			'label'     => esc_html__( 'رنگ متن', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__eyebrow' => 'color: {{VALUE}};',
			),
		) );
		$this->add_control( 'eyebrow_background', array(
			'label'     => esc_html__( 'رنگ پس‌زمینه', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__eyebrow' => 'background: {{VALUE}};',
			),
		) );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .webmz-page-hero__eyebrow',
			)
		);
		$this->add_responsive_control( 'eyebrow_padding', array(
			'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em' ),
			'selectors'  => array(
				'{{WRAPPER}} .webmz-page-hero__eyebrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );
		$this->add_responsive_control( 'eyebrow_radius', array(
			'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array(
				'{{WRAPPER}} .webmz-page-hero__eyebrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );
		$this->add_responsive_control( 'eyebrow_margin', array(
			'label'      => esc_html__( 'فاصله بیرونی', 'tadris' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em' ),
			'selectors'  => array(
				'{{WRAPPER}} .webmz-page-hero__eyebrow' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );
		$this->end_controls_section();

		$this->start_controls_section(
			'style_title',
			array(
				'label' => esc_html__( 'عنوان اصلی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control( 'title_color', array(
			'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__title' => 'color: {{VALUE}};',
			),
		) );
		$this->add_control( 'title_accent_color', array(
			'label'     => esc_html__( 'رنگ بخش برجسته', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__title strong, {{WRAPPER}} .webmz-page-hero__title span' => 'color: {{VALUE}};',
			),
		) );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .webmz-page-hero__title',
			)
		);
		$this->add_responsive_control( 'title_margin', array(
			'label'      => esc_html__( 'فاصله بیرونی', 'tadris' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em' ),
			'selectors'  => array(
				'{{WRAPPER}} .webmz-page-hero__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );
		$this->end_controls_section();

		$this->start_controls_section(
			'style_description',
			array(
				'label' => esc_html__( 'توضیحات', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control( 'description_color', array(
			'label'     => esc_html__( 'رنگ متن', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__description' => 'color: {{VALUE}};',
			),
		) );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .webmz-page-hero__description',
			)
		);
		$this->add_responsive_control( 'description_width', array(
			'label'      => esc_html__( 'حداکثر عرض', 'tadris' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', '%' ),
			'range'      => array(
				'px' => array( 'min' => 200, 'max' => 1200 ),
				'%'  => array( 'min' => 20, 'max' => 100 ),
			),
			'selectors'  => array(
				'{{WRAPPER}} .webmz-page-hero__description' => 'max-width: {{SIZE}}{{UNIT}};',
			),
		) );
		$this->add_responsive_control( 'description_margin', array(
			'label'      => esc_html__( 'فاصله بیرونی', 'tadris' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em' ),
			'selectors'  => array(
				'{{WRAPPER}} .webmz-page-hero__description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );
		$this->end_controls_section();

		$this->start_controls_section(
			'style_buttons',
			array(
				'label' => esc_html__( 'دکمه‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control( 'buttons_gap', array(
			'label'      => esc_html__( 'فاصله دکمه‌ها', 'tadris' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', 'em' ),
			'range'      => array(
				'px' => array( 'min' => 0, 'max' => 60 ),
				'em' => array( 'min' => 0, 'max' => 5 ),
			),
			'selectors'  => array(
				'{{WRAPPER}} .webmz-page-hero__actions' => 'gap: {{SIZE}}{{UNIT}};',
			),
		) );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'buttons_typography',
				'selector' => '{{WRAPPER}} .webmz-page-hero__button',
			)
		);
		$this->add_responsive_control( 'buttons_padding', array(
			'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em' ),
			'selectors'  => array(
				'{{WRAPPER}} .webmz-page-hero__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );
		$this->add_responsive_control( 'buttons_radius', array(
			'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array(
				'{{WRAPPER}} .webmz-page-hero__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );
		$this->add_control( 'primary_button_heading', array(
			'label'     => esc_html__( 'دکمه اول', 'tadris' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		) );
		$this->add_control( 'primary_button_color', array(
			'label'     => esc_html__( 'رنگ متن', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__button--primary' => 'color: {{VALUE}};',
			),
		) );
		$this->add_control( 'primary_button_background', array(
			'label'     => esc_html__( 'رنگ پس‌زمینه', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__button--primary' => 'background: {{VALUE}};',
			),
		) );
		$this->add_control( 'primary_button_hover_color', array(
			'label'     => esc_html__( 'رنگ متن هاور', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__button--primary:hover' => 'color: {{VALUE}};',
			),
		) );
		$this->add_control( 'primary_button_hover_background', array(
			'label'     => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__button--primary:hover' => 'background: {{VALUE}};',
			),
		) );
		$this->add_control( 'secondary_button_heading', array(
			'label'     => esc_html__( 'دکمه دوم', 'tadris' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		) );
		$this->add_control( 'secondary_button_color', array(
			'label'     => esc_html__( 'رنگ متن', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__button--secondary' => 'color: {{VALUE}};',
			),
		) );
		$this->add_control( 'secondary_button_background', array(
			'label'     => esc_html__( 'رنگ پس‌زمینه', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__button--secondary' => 'background: {{VALUE}};',
			),
		) );
		$this->add_control( 'secondary_button_border_color', array(
			'label'     => esc_html__( 'رنگ خط دور', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__button--secondary' => 'border-color: {{VALUE}};',
			),
		) );
		$this->add_control( 'secondary_button_hover_color', array(
			'label'     => esc_html__( 'رنگ متن هاور', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__button--secondary:hover' => 'color: {{VALUE}};',
			),
		) );
		$this->add_control( 'secondary_button_hover_background', array(
			'label'     => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__button--secondary:hover' => 'background: {{VALUE}};',
			),
		) );
		$this->add_control( 'secondary_button_hover_border_color', array(
			'label'     => esc_html__( 'خط دور هاور', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__button--secondary:hover' => 'border-color: {{VALUE}};',
			),
		) );
		$this->end_controls_section();

		$this->start_controls_section(
			'style_image',
			array(
				'label' => esc_html__( 'عکس', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control( 'image_align', array(
			'label'     => esc_html__( 'چینش عکس', 'tadris' ),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => array(
				'start'  => array( 'title' => esc_html__( 'شروع', 'tadris' ), 'icon' => 'eicon-h-align-left' ),
				'center' => array( 'title' => esc_html__( 'وسط', 'tadris' ), 'icon' => 'eicon-h-align-center' ),
				'end'    => array( 'title' => esc_html__( 'پایان', 'tadris' ), 'icon' => 'eicon-h-align-right' ),
			),
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__visual' => 'justify-self: {{VALUE}};',
			),
		) );
		$this->add_responsive_control( 'image_width', array(
			'label'      => esc_html__( 'عرض عکس', 'tadris' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', '%' ),
			'range'      => array(
				'px' => array( 'min' => 160, 'max' => 700 ),
				'%'  => array( 'min' => 20, 'max' => 100 ),
			),
			'selectors'  => array(
				'{{WRAPPER}} .webmz-page-hero__visual' => 'width: min(100%, {{SIZE}}{{UNIT}});',
			),
		) );
		$this->add_control( 'image_frame_color', array(
			'label'     => esc_html__( 'رنگ قاب پشت عکس', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .webmz-page-hero__visual::after' => 'background: {{VALUE}};',
			),
		) );
		$this->add_responsive_control( 'image_frame_offset', array(
			'label'      => esc_html__( 'فاصله قاب پشت عکس', 'tadris' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array(
				'px' => array( 'min' => 0, 'max' => 80 ),
			),
			'selectors'  => array(
				'{{WRAPPER}} .webmz-page-hero__visual'        => 'padding: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;',
				'{{WRAPPER}} .webmz-page-hero__visual::after' => 'width: calc(100% - {{SIZE}}{{UNIT}}); height: calc(100% - {{SIZE}}{{UNIT}});',
			),
		) );
		$this->add_responsive_control( 'image_radius', array(
			'label'      => esc_html__( 'گردی گوشه‌های عکس', 'tadris' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array(
				'{{WRAPPER}} .webmz-page-hero__image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} .webmz-page-hero__image',
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$title_tag   = ! empty( $settings['title_tag'] ) ? strtolower( $settings['title_tag'] ) : 'h1';
		$allowed_tag = in_array( $title_tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div' ), true ) ? $title_tag : 'h1';
		$title       = ! empty( $settings['title'] ) ? $settings['title'] : '';
		$title_html  = wp_kses( $title, array(
			'br'     => array(),
			'strong' => array(),
			'span'   => array( 'class' => array() ),
		) );
		$image       = ! empty( $settings['image'] ) && is_array( $settings['image'] ) ? $settings['image'] : array();
		$image_url   = ! empty( $image['url'] ) ? $image['url'] : Utils::get_placeholder_image_src();
		$image_size  = ! empty( $settings['image_size'] ) ? sanitize_key( $settings['image_size'] ) : 'large';
		$allowed_sizes = array( 'thumbnail', 'medium', 'medium_large', 'large', 'full' );

		if ( ! in_array( $image_size, $allowed_sizes, true ) ) {
			$image_size = 'large';
		}

		if ( ! empty( $settings['primary_button_link'] ) && is_array( $settings['primary_button_link'] ) ) {
			$this->add_link_attributes( 'primary_button_link', $settings['primary_button_link'] );
		}
		if ( ! empty( $settings['secondary_button_link'] ) && is_array( $settings['secondary_button_link'] ) ) {
			$this->add_link_attributes( 'secondary_button_link', $settings['secondary_button_link'] );
		}
		?>
		<section class="webmz-page-hero">
			<div class="webmz-page-hero__content">
				<?php if ( ! empty( $settings['eyebrow'] ) ) : ?>
					<span class="webmz-page-hero__eyebrow"><?php echo esc_html( $settings['eyebrow'] ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== $title_html ) : ?>
					<<?php echo esc_html( $allowed_tag ); ?> class="webmz-page-hero__title"><?php echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></<?php echo esc_html( $allowed_tag ); ?>>
				<?php endif; ?>
				<?php if ( ! empty( $settings['description'] ) ) : ?>
					<p class="webmz-page-hero__description"><?php echo esc_html( $settings['description'] ); ?></p>
				<?php endif; ?>
				<div class="webmz-page-hero__actions">
					<?php if ( ! empty( $settings['primary_button_text'] ) ) : ?>
						<a class="webmz-page-hero__button webmz-page-hero__button--primary" <?php echo $this->get_render_attribute_string( 'primary_button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php echo esc_html( $settings['primary_button_text'] ); ?>
						</a>
					<?php endif; ?>
					<?php if ( ! empty( $settings['secondary_button_text'] ) ) : ?>
						<a class="webmz-page-hero__button webmz-page-hero__button--secondary" <?php echo $this->get_render_attribute_string( 'secondary_button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php echo esc_html( $settings['secondary_button_text'] ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
			<div class="webmz-page-hero__visual">
				<?php if ( ! empty( $image['id'] ) ) : ?>
					<?php echo wp_get_attachment_image( absint( $image['id'] ), $image_size, false, array( 'class' => 'webmz-page-hero__image' ) ); ?>
				<?php else : ?>
					<img class="webmz-page-hero__image" src="<?php echo esc_url( $image_url ); ?>" alt="">
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}

/**
 * Simple feature/service card.
 */
class Feature_Card_Widget extends Widget_Base {
	public function get_name() { return 'webmz-feature-card'; }
	public function get_title() { return esc_html__( 'کارت ویژگی آکام', 'tadris' ); }
	public function get_icon() { return 'eicon-info-box'; }
	public function get_categories() { return array( 'webmz-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'icon', array(
			'label'   => esc_html__( 'نماد کوتاه', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '✓',
		) );
		$this->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'طراحی انعطاف پذیر', 'tadris' ),
		) );
		$this->add_control( 'description', array(
			'label'   => esc_html__( 'توضیحات', 'tadris' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'این کارت را برای معرفی قابلیت ها یا خدمات استفاده کنید.', 'tadris' ),
		) );
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="webmz-widget-feature">
			<span class="webmz-widget-feature__icon"><?php echo esc_html( $settings['icon'] ); ?></span>
			<h3><?php echo esc_html( $settings['title'] ); ?></h3>
			<p><?php echo esc_html( $settings['description'] ); ?></p>
		</div>
		<?php
	}
}

/**
 * Standalone CTA button widget.
 */
class Action_Button_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() { return 'webmz-action-button'; }
	public function get_title() { return esc_html__( 'دکمه آکام', 'tadris' ); }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return array( 'webmz-widgets' ); }

	protected function register_controls() {
		$button_selector = '.webmz-widget-action .webmz-button';
		$icon_selector   = '.webmz-widget-action .webmz-button__icon';
		$text_selector   = '.webmz-widget-action .webmz-button__text';

		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'دکمه', 'tadris' ) ) );
		$this->add_control( 'text', array( 'label' => esc_html__( 'متن', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'مشاهده بیشتر', 'tadris' ) ) );
		$this->add_control( 'url', array( 'label' => esc_html__( 'لینک', 'tadris' ), 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ) ) );
		$this->add_control(
			'selected_icon',
			array(
				'label' => esc_html__( 'آیکون', 'tadris' ),
				'type'  => Controls_Manager::ICONS,
			)
		);
		$this->webmz_register_icon_color_mode_control(
			'selected_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون SVG', 'tadris' ),
			array( 'selected_icon[value]!' => '' )
		);
		$this->add_control(
			'icon_position',
			array(
				'label'     => esc_html__( 'موقعیت آیکون', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => array(
					'right' => esc_html__( 'سمت راست', 'tadris' ),
					'left'  => esc_html__( 'سمت چپ', 'tadris' ),
				),
				'condition' => array( 'selected_icon[value]!' => '' ),
			)
		);
		$this->add_control(
			'button_width_type',
			array(
				'label'   => esc_html__( 'نوع عرض', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => array(
					'inline' => esc_html__( 'اندازه محتوا', 'tadris' ),
					'full'   => esc_html__( 'تمام عرض', 'tadris' ),
				),
			)
		);
		$this->add_responsive_control( 'align', array(
			'label'     => esc_html__( 'چینش', 'tadris' ),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => array(
				'right'  => array( 'title' => esc_html__( 'راست', 'tadris' ), 'icon' => 'eicon-text-align-right' ),
				'center' => array( 'title' => esc_html__( 'وسط', 'tadris' ), 'icon' => 'eicon-text-align-center' ),
				'left'   => array( 'title' => esc_html__( 'چپ', 'tadris' ), 'icon' => 'eicon-text-align-left' ),
			),
			'default'   => 'right',
			'selectors' => array( '{{WRAPPER}} .webmz-widget-action' => 'text-align: {{VALUE}};' ),
		) );
		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => esc_html__( 'استایل دکمه', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'label'    => esc_html__( 'تایپوگرافی', 'tadris' ),
				'selector' => '{{WRAPPER}} ' . $button_selector,
			)
		);

		$this->add_responsive_control(
			'button_min_height',
			array(
				'label'      => esc_html__( 'حداقل ارتفاع', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 120 ) ),
				'default'    => array( 'size' => 48, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $button_selector => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'default'    => array(
					'top'      => '10',
					'right'    => '21',
					'bottom'   => '10',
					'left'     => '21',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $button_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_gap',
			array(
				'label'     => esc_html__( 'فاصله متن و آیکون', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'   => array( 'size' => 8, 'unit' => 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $button_selector => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => '10',
					'right'    => '10',
					'bottom'   => '10',
					'left'     => '10',
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $button_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'button_border',
				'label'    => esc_html__( 'بوردر', 'tadris' ),
				'selector' => '{{WRAPPER}} ' . $button_selector,
			)
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		$this->start_controls_tab(
			'button_normal_tab',
			array( 'label' => esc_html__( 'عادی', 'tadris' ) )
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} ' . $text_selector => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_background',
				'label'    => esc_html__( 'پس‌زمینه', 'tadris' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} ' . $button_selector,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_shadow',
				'label'    => esc_html__( 'سایه', 'tadris' ),
				'selector' => '{{WRAPPER}} ' . $button_selector,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_hover_tab',
			array( 'label' => esc_html__( 'هاور', 'tadris' ) )
		);

		$this->add_control(
			'button_hover_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $button_selector . ':hover ' . $text_selector => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_hover_background',
				'label'    => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} ' . $button_selector . ':hover',
			)
		);

		$this->add_control(
			'button_hover_border_color',
			array(
				'label'     => esc_html__( 'رنگ بوردر هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $button_selector . ':hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_hover_shadow',
				'label'    => esc_html__( 'سایه هاور', 'tadris' ),
				'selector' => '{{WRAPPER}} ' . $button_selector . ':hover',
			)
		);

		$this->add_responsive_control(
			'button_hover_translate_y',
			array(
				'label'     => esc_html__( 'حرکت هاور', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => -20, 'max' => 20 ) ),
				'selectors' => array(
					'{{WRAPPER}} ' . $button_selector . ':hover' => 'transform: translateY({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'button_transition',
			array(
				'label'      => esc_html__( 'مدت انیمیشن', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's', 'ms' ),
				'range'      => array(
					's'  => array( 'min' => 0, 'max' => 3, 'step' => 0.1 ),
					'ms' => array( 'min' => 0, 'max' => 3000, 'step' => 50 ),
				),
				'default'    => array( 'size' => 0.2, 'unit' => 's' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $button_selector => 'transition: all {{SIZE}}{{UNIT}} ease;',
				),
				'separator'  => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			array(
				'label'     => esc_html__( 'استایل آیکون', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'selected_icon[value]!' => '' ),
			)
		);

		$this->add_responsive_control(
			'icon_box_size',
			array(
				'label'      => esc_html__( 'اندازه باکس آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 16, 'max' => 80 ) ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $icon_selector => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'اندازه خود آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 50 ) ),
				'default'    => array( 'size' => 16, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $icon_selector . ' i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $icon_selector . ' svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_radius',
			array(
				'label'      => esc_html__( 'گردی باکس آیکون', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $icon_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'icon_style_tabs' );

		$this->start_controls_tab(
			'icon_normal_tab',
			array( 'label' => esc_html__( 'عادی', 'tadris' ) )
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $icon_selector => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_background_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $icon_selector => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_hover_tab',
			array( 'label' => esc_html__( 'هاور', 'tadris' ) )
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $button_selector . ':hover ' . $icon_selector => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_background_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه آیکون هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $button_selector . ':hover ' . $icon_selector => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_hover_translate_x',
			array(
				'label'     => esc_html__( 'حرکت آیکون در هاور', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => -20, 'max' => 20 ) ),
				'selectors' => array(
					'{{WRAPPER}} ' . $button_selector . ':hover ' . $icon_selector => 'transform: translateX({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$has_icon      = ! empty( $settings['selected_icon']['value'] );
		$icon_position = ! empty( $settings['icon_position'] ) ? $settings['icon_position'] : 'right';
		$icon_class    = $has_icon ? $this->webmz_get_icon_color_mode_class( $settings, 'selected_icon_color_mode' ) : '';

		$this->add_render_attribute( 'button', 'class', 'webmz-button' );
		if ( 'full' === $settings['button_width_type'] ) {
			$this->add_render_attribute( 'button', 'class', 'webmz-button--full' );
		}
		if ( ! empty( $settings['url']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['url'] );
		} else {
			$this->add_render_attribute( 'button', 'href', '#' );
		}
		?>
		<div class="webmz-widget-action">
			<a <?php echo $this->get_render_attribute_string( 'button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php if ( $has_icon && 'right' === $icon_position ) : ?>
					<span class="webmz-button__icon <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true">
						<?php Icons_Manager::render_icon( $settings['selected_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php endif; ?>
				<span class="webmz-button__text"><?php echo esc_html( $settings['text'] ); ?></span>
				<?php if ( $has_icon && 'left' === $icon_position ) : ?>
					<span class="webmz-button__icon <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true">
						<?php Icons_Manager::render_icon( $settings['selected_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php endif; ?>
			</a>
		</div>
		<?php
	}
}