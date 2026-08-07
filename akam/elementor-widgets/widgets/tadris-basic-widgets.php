<?php
/**
 * HTML-template based WebMZ widgets requested for Tadris sections.
 * No fixed frontend CSS is emitted here; Elementor controls generate styles.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

 defined( 'ABSPATH' ) || exit;

/** Text contact callout widget. */
class Tadris_Text_Box_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;
	public function get_name() { return 'webmz-tadris-text-box'; }
	public function get_title() { return esc_html__( 'ارتباط متنی آکام', 'tadris' ); }
	public function get_icon() { return 'eicon-call-to-action'; }
	public function get_categories() { return array( 'webmz-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'link_text', array( 'label' => esc_html__( 'متن لینک', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'ارتباط با ما', 'tadris' ), 'label_block' => true ) );
		$this->add_control( 'subtitle', array( 'label' => esc_html__( 'توضیح', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'ارتباط با پشتیبانی آنلاین', 'tadris' ), 'label_block' => true ) );
		$this->add_control( 'link', array( 'label' => esc_html__( 'لینک', 'tadris' ), 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ) ) );
		$this->add_control( 'icon', array( 'label' => esc_html__( 'آیکون', 'tadris' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-chevron-left', 'library' => 'fa-solid' ) ) );
		$this->webmz_register_icon_color_mode_control();
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'box_style', esc_html__( 'باکس', 'tadris' ), '.tadris-text-box-1' );
		$this->webmz_register_text_style_controls( 'link_style', esc_html__( 'متن لینک', 'tadris' ), '.tadris-text-box-1 > a' );
		$this->webmz_register_text_style_controls( 'subtitle_style', esc_html__( 'توضیح', 'tadris' ), '.tadris-text-box-1 > span' );
		$this->webmz_register_icon_style_controls( 'icon_style', esc_html__( 'آیکون', 'tadris' ), '.tadris-text-box-1 > a' );
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		$this->add_link_attributes( 'link', $s['link'] );
		?>
		<div class="tadris-text-box-1">
			<a <?php echo $this->get_render_attribute_string( 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php echo esc_html( $s['link_text'] ); ?>
				<span class="tadris-text-box-icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s ) ); ?>"><?php Icons_Manager::render_icon( $s['icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
			</a>
			<span><?php echo esc_html( $s['subtitle'] ); ?></span>
		</div>
		<?php
	}
}

/** Footer phone/contact box widget. */
class Tadris_Contact_Box_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;
	public function get_name() { return 'webmz-tadris-contact-box'; }
	public function get_title() { return esc_html__( 'باکس تماس فوتر', 'tadris' ); }
	public function get_icon() { return 'eicon-contact'; }
	public function get_categories() { return array( 'webmz-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'number_prefix', array( 'label' => esc_html__( 'پیش‌شماره', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => '021 -' ) );
		$this->add_control( 'phone_number', array( 'label' => esc_html__( 'شماره تماس', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => '0000000' ) );
		$this->add_control( 'caption', array( 'label' => esc_html__( 'عنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'ارتبـاط با برتر وردپرس', 'tadris' ), 'label_block' => true ) );
		$this->add_control( 'link', array( 'label' => esc_html__( 'لینک تماس', 'tadris' ), 'type' => Controls_Manager::URL, 'default' => array( 'url' => 'tel:0210000000' ) ) );
		$this->add_control( 'icon', array( 'label' => esc_html__( 'آیکون', 'tadris' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-phone-volume', 'library' => 'fa-solid' ) ) );
		$this->webmz_register_icon_color_mode_control();
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'box_style', esc_html__( 'باکس تماس', 'tadris' ), '.tadris-contact-box-1' );
		$this->webmz_register_icon_style_controls( 'icon_style', esc_html__( 'آیکون', 'tadris' ), '.tadris-contact-icon' );
		$this->webmz_register_text_style_controls( 'number_style', esc_html__( 'شماره تماس', 'tadris' ), '.tadris-contact-number' );
		$this->webmz_register_text_style_controls( 'caption_style', esc_html__( 'عنوان', 'tadris' ), '.tadris-contact-text > span' );
	}
	protected function render_contact_icon( $settings ) {
		ob_start();
		if ( ! empty( $settings['icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) );
		}
		$icon_markup = trim( ob_get_clean() );

		if ( '' !== $icon_markup ) {
			echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}
		?>
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
			<path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"/>
			<path d="M15 7a2 2 0 0 1 2 2"/>
			<path d="M15 3a6 6 0 0 1 6 6"/>
		</svg>
		<?php
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$this->add_link_attributes( 'link', $s['link'] );
		?>
		<a class="tadris-contact-box-1" <?php echo $this->get_render_attribute_string( 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="tadris-contact-icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s ) ); ?>"><?php $this->render_contact_icon( $s ); ?></div>
			<div class="tadris-contact-text">
				<div class="tadris-contact-number"><?php echo esc_html( $s['number_prefix'] ); ?> <strong><?php echo esc_html( $s['phone_number'] ); ?></strong></div>
				<span><?php echo esc_html( $s['caption'] ); ?></span>
			</div>
		</a>
		<?php
	}
}

/** Tadris icon information card. */
class Tadris_Icon_Box_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;
	public function get_name() { return 'webmz-tadris-icon-box'; }
	public function get_title() { return esc_html__( 'جعبه آیکون آکام', 'tadris' ); }
	public function get_icon() { return 'eicon-info-box'; }
	public function get_categories() { return array( 'webmz-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'icon', array( 'label' => esc_html__( 'آیکون', 'tadris' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-award', 'library' => 'fa-solid' ) ) );
		$this->webmz_register_icon_color_mode_control();
		$this->add_control( 'title', array( 'label' => esc_html__( 'عنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'تضمین کیفیت محتوا', 'tadris' ), 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => esc_html__( 'توضیحات', 'tadris' ), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__( 'تضمین کیفیت محتوای دوره‌های برتر وردپرس', 'tadris' ) ) );
		$this->add_control( 'link', array( 'label' => esc_html__( 'لینک اختیاری', 'tadris' ), 'type' => Controls_Manager::URL ) );
		$this->add_control(
			'layout',
			array(
				'label'        => esc_html__( 'چیدمان', 'tadris' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'right',
				'prefix_class' => 'webmz-tadris-icon-box--layout-',
				'options'      => array(
					'right'  => esc_html__( 'راست‌چین (آیکون راست)', 'tadris' ),
					'left'   => esc_html__( 'چپ‌چین (آیکون چپ)', 'tadris' ),
					'center' => esc_html__( 'وسط‌چین', 'tadris' ),
				),
			)
		);
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'box_style', esc_html__( 'جعبه', 'tadris' ), '.tadris-icon-box-1' );
		$this->webmz_register_icon_style_controls(
			'icon_style',
			esc_html__( 'آیکون', 'tadris' ),
			'.icon-box-1-icon',
			array(
				'hover'          => true,
				'hover_selector' => '{{WRAPPER}} .tadris-icon-box-1:hover .icon-box-1-icon, {{WRAPPER}} .tadris-icon-box-1:focus-within .icon-box-1-icon',
			)
		);
		$this->register_icon_box_shape_controls();
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.icon-box-1-content-tag' );
		$this->webmz_register_text_style_controls( 'description_style', esc_html__( 'توضیحات', 'tadris' ), '.icon-box-1-content p' );
	}
	protected function register_icon_box_shape_controls() {
		$this->start_controls_section(
			'icon_box_shape',
			array(
				'label' => esc_html__( 'شکل باکس آیکون', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_box_round',
			array(
				'label'        => esc_html__( 'باکس گرد', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'default'      => '',
				'return_value' => 'yes',
				'selectors'    => array(
					'{{WRAPPER}} .icon-box-1-icon' => 'border-radius: 50%;',
				),
			)
		);

		$this->add_control(
			'icon_box_hide_shadow',
			array(
				'label'        => esc_html__( 'حذف سایه زیر باکس', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'default'      => '',
				'return_value' => 'yes',
				'selectors'    => array(
					'{{WRAPPER}} .icon-box-1-icon:after' => 'content: none; display: none;',
				),
			)
		);

		$this->add_responsive_control(
			'icon_box_min_width',
			array(
				'label'      => esc_html__( 'حداقل عرض باکس آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 32,
						'max' => 200,
					),
				),
				'default'    => array(
					'size' => 64,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .icon-box-1-icon' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_box_min_height',
			array(
				'label'      => esc_html__( 'حداقل ارتفاع باکس آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 32,
						'max' => 200,
					),
				),
				'default'    => array(
					'size' => 64,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .icon-box-1-icon' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$tag = ! empty( $s['link']['url'] ) ? 'a' : 'div';
		if ( 'a' === $tag ) { $this->add_link_attributes( 'link', $s['link'] ); }
		$layout = ! empty( $s['layout'] ) ? sanitize_key( (string) $s['layout'] ) : 'right';
		if ( ! in_array( $layout, array( 'right', 'left', 'center' ), true ) ) {
			$layout = 'right';
		}
		?>
		<<?php echo esc_html( $tag ); ?> class="tadris-icon-box-1 tadris-icon-box-1--layout-<?php echo esc_attr( $layout ); ?>" <?php echo 'a' === $tag ? $this->get_render_attribute_string( 'link' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="icon-box-1-icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s ) ); ?>"><?php Icons_Manager::render_icon( $s['icon'], array( 'aria-hidden' => 'true' ) ); ?></div>
			<div class="icon-box-1-content">
				<h4 class="icon-box-1-content-tag"><?php echo esc_html( $s['title'] ); ?></h4>
				<p><?php echo esc_html( $s['description'] ); ?></p>
			</div>
		</<?php echo esc_html( $tag ); ?>>
		<?php
	}
}

/** Section heading widget with duplicated decorative icon. */
class Tadris_Heading_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;
	public function get_name() { return 'webmz-tadris-heading'; }
	public function get_title() { return esc_html__( 'هدینگ آکام ۱', 'tadris' ); }
	public function get_icon() { return 'eicon-heading'; }
	public function get_categories() { return array( 'webmz-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'icon', array( 'label' => esc_html__( 'آیکون', 'tadris' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-video', 'library' => 'fa-solid' ) ) );
		$this->webmz_register_icon_color_mode_control();
		$this->add_control( 'title', array( 'label' => esc_html__( 'عنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'وی تی برتر', 'tadris' ), 'label_block' => true ) );
		$this->add_control( 'description', array( 'label' => esc_html__( 'توضیحات', 'tadris' ), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__( 'آخرین ویدیوهای آموزشی رایگان برتر وردپرس', 'tadris' ) ) );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'box_style', esc_html__( 'باکس عنوان', 'tadris' ), '.tadris-heading' );
		$this->webmz_register_icon_style_controls( 'icon_style', esc_html__( 'آیکون', 'tadris' ), '.tadris-heading-icon' );
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.tadris-heading-title-tag' );
		$this->webmz_register_text_style_controls( 'description_style', esc_html__( 'توضیحات', 'tadris' ), '.tadris-heading-description p' );
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="tadris-heading">
			<div class="tadris-heading-icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s ) ); ?>">
				<?php Icons_Manager::render_icon( $s['icon'], array( 'aria-hidden' => 'true' ) ); ?>
				<?php Icons_Manager::render_icon( $s['icon'], array( 'class' => 'icon-clone', 'aria-hidden' => 'true' ) ); ?>
			</div>
			<div class="tadris-heading-title"><h2 class="tadris-heading-title-tag"><?php echo esc_html( $s['title'] ); ?></h2></div>
			<div class="tadris-heading-description"><p><?php echo esc_html( $s['description'] ); ?></p></div>
		</div>
		<?php
	}
}

/** Centered section heading with subtitle, title, and description. */
class Tadris_Heading_2_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;
	public function get_name() { return 'webmz-tadris-heading-2'; }
	public function get_title() { return esc_html__( 'هدینگ آکام ۲', 'tadris' ); }
	public function get_icon() { return 'eicon-heading'; }
	public function get_categories() { return array( 'webmz-widgets' ); }
	public function get_style_depends() { return array( 'webmz-tadris-heading-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'subtitle', array( 'label' => esc_html__( 'زیرعنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'چه خدماتی', 'tadris' ), 'label_block' => true ) );
		$this->add_control( 'title', array( 'label' => esc_html__( 'عنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'در وب پرداز ارائه می‌دهیم؟', 'tadris' ), 'label_block' => true ) );
		$this->webmz_register_title_tag_control();
		$this->add_control( 'description', array( 'label' => esc_html__( 'توضیحات', 'tadris' ), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__( 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.', 'tadris' ) ) );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'box_style', esc_html__( 'باکس', 'tadris' ), '.tadris-heading-2' );
		$this->webmz_register_text_style_controls( 'subtitle_style', esc_html__( 'زیرعنوان', 'tadris' ), '.tadris-heading-2__subtitle' );
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.tadris-heading-2__title' );
		$this->webmz_register_text_style_controls( 'description_style', esc_html__( 'توضیحات', 'tadris' ), '.tadris-heading-2__description' );
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		$title_tag = $this->webmz_get_title_tag( $s );
		?>
		<div class="tadris-heading-2">
			<?php if ( ! empty( $s['subtitle'] ) ) : ?>
				<span class="tadris-heading-2__subtitle"><?php echo esc_html( $s['subtitle'] ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $s['title'] ) ) : ?>
				<<?php echo esc_html( $title_tag ); ?> class="tadris-heading-2__title"><?php echo esc_html( $s['title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
			<?php endif; ?>
			<?php if ( ! empty( $s['description'] ) ) : ?>
				<p class="tadris-heading-2__description"><?php echo esc_html( $s['description'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
}

/** Horizontal section heading with title, subtitle, and view-all button. */
class Tadris_Heading_3_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;
	public function get_name() { return 'webmz-tadris-heading-3'; }
	public function get_title() { return esc_html__( 'هدینگ آکام ۳', 'tadris' ); }
	public function get_icon() { return 'eicon-heading'; }
	public function get_categories() { return array( 'webmz-widgets' ); }
	public function get_style_depends() { return array( 'webmz-tadris-heading-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'title', array( 'label' => esc_html__( 'عنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'دوره های آموزشی وب', 'tadris' ), 'label_block' => true ) );
		$this->webmz_register_title_tag_control();
		$this->add_control( 'subtitle', array( 'label' => esc_html__( 'زیرعنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'دوره های آموزشی طراحی و توسعه وبسایت', 'tadris' ), 'label_block' => true ) );
		$this->add_control( 'show_button', array( 'label' => esc_html__( 'نمایش دکمه', 'tadris' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'button_text', array( 'label' => esc_html__( 'متن دکمه', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'مشاهده همه', 'tadris' ), 'condition' => array( 'show_button' => 'yes' ) ) );
		$this->add_control( 'button_link', array( 'label' => esc_html__( 'لینک دکمه', 'tadris' ), 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ), 'condition' => array( 'show_button' => 'yes' ) ) );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'box_style', esc_html__( 'باکس', 'tadris' ), '.tadris-heading-3', array( 'flat' => true ) );
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.tadris-heading-3__title' );
		$this->webmz_register_text_style_controls( 'subtitle_style', esc_html__( 'زیرعنوان', 'tadris' ), '.tadris-heading-3__subtitle' );
		$this->webmz_register_box_style_controls( 'button_style', esc_html__( 'دکمه', 'tadris' ), '.tadris-heading-3__button', array( 'bordered' => true, 'default_background' => '#ffffff' ) );
		$this->webmz_register_text_style_controls( 'button_text_style', esc_html__( 'متن دکمه', 'tadris' ), '.tadris-heading-3__button' );
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		$title_tag = $this->webmz_get_title_tag( $s );
		?>
		<div class="tadris-heading-3">
			<div class="tadris-heading-3__text">
				<?php if ( ! empty( $s['title'] ) ) : ?>
					<<?php echo esc_html( $title_tag ); ?> class="tadris-heading-3__title"><?php echo esc_html( $s['title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
				<?php endif; ?>
				<?php if ( ! empty( $s['subtitle'] ) ) : ?>
					<p class="tadris-heading-3__subtitle"><?php echo esc_html( $s['subtitle'] ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( 'yes' === $s['show_button'] && ! empty( $s['button_text'] ) ) : ?>
				<?php $this->add_link_attributes( 'button_link', $s['button_link'] ); ?>
				<a class="tadris-heading-3__button" <?php echo $this->get_render_attribute_string( 'button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php echo esc_html( $s['button_text'] ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}

/** Horizontal section heading with icon box, title, description, and optional view-all button. */
class Tadris_Heading_4_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;
	public function get_name() { return 'webmz-tadris-heading-4'; }
	public function get_title() { return esc_html__( 'هدینگ آکام ۴', 'tadris' ); }
	public function get_icon() { return 'eicon-heading'; }
	public function get_categories() { return array( 'webmz-widgets' ); }
	public function get_style_depends() { return array( 'webmz-tadris-heading-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'show_icon', array( 'label' => esc_html__( 'نمایش آیکون', 'tadris' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'icon', array( 'label' => esc_html__( 'آیکون', 'tadris' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-file-lines', 'library' => 'fa-solid' ), 'condition' => array( 'show_icon' => 'yes' ) ) );
		$this->webmz_register_icon_color_mode_control( 'icon_color_mode', '', array( 'show_icon' => 'yes' ) );
		$this->add_control( 'title', array( 'label' => esc_html__( 'عنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'مقالات آموزشی', 'tadris' ), 'label_block' => true ) );
		$this->webmz_register_title_tag_control();
		$this->add_control( 'description', array( 'label' => esc_html__( 'توضیحات', 'tadris' ), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__( 'جدیدترین مقالات و نکات آموزشی زبان را بخوانید.', 'tadris' ) ) );
		$this->add_control( 'show_button', array( 'label' => esc_html__( 'نمایش دکمه مشاهده همه', 'tadris' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'button_text', array( 'label' => esc_html__( 'متن دکمه', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'مشاهده همه مقالات', 'tadris' ), 'condition' => array( 'show_button' => 'yes' ) ) );
		$this->add_control( 'button_link', array( 'label' => esc_html__( 'لینک دکمه', 'tadris' ), 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ), 'condition' => array( 'show_button' => 'yes' ) ) );
		$this->add_control( 'button_icon', array( 'label' => esc_html__( 'آیکون دکمه', 'tadris' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-chevron-left', 'library' => 'fa-solid' ), 'condition' => array( 'show_button' => 'yes' ) ) );
		$this->webmz_register_icon_color_mode_control( 'button_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون دکمه', 'tadris' ), array( 'show_button' => 'yes' ) );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'box_style', esc_html__( 'باکس', 'tadris' ), '.tadris-heading-4', array( 'flat' => true ) );
		$this->webmz_register_box_style_controls( 'icon_box_style', esc_html__( 'باکس آیکون', 'tadris' ), '.tadris-heading-4__icon', array( 'bordered' => true, 'default_background' => '#ffffff' ) );
		$this->webmz_register_icon_style_controls( 'icon_style', esc_html__( 'آیکون', 'tadris' ), '.tadris-heading-4__icon' );
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.tadris-heading-4__title' );
		$this->webmz_register_text_style_controls( 'description_style', esc_html__( 'توضیحات', 'tadris' ), '.tadris-heading-4__description' );
		$this->webmz_register_box_style_controls( 'button_style', esc_html__( 'دکمه', 'tadris' ), '.tadris-heading-4__button', array( 'bordered' => true, 'default_background' => '#ffffff' ) );
		$this->webmz_register_text_style_controls( 'button_text_style', esc_html__( 'متن دکمه', 'tadris' ), '.tadris-heading-4__button' );
		$this->webmz_register_icon_style_controls( 'button_icon_style', esc_html__( 'آیکون دکمه', 'tadris' ), '.tadris-heading-4__button-icon' );
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		$title_tag = $this->webmz_get_title_tag( $s );
		?>
		<div class="tadris-heading-4">
			<div class="tadris-heading-4__lead">
				<?php if ( 'yes' === $s['show_icon'] && ! empty( $s['icon']['value'] ) ) : ?>
					<div class="tadris-heading-4__icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s ) ); ?>">
						<?php Icons_Manager::render_icon( $s['icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</div>
				<?php endif; ?>
				<div class="tadris-heading-4__text">
					<?php if ( ! empty( $s['title'] ) ) : ?>
						<<?php echo esc_html( $title_tag ); ?> class="tadris-heading-4__title"><?php echo esc_html( $s['title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
					<?php endif; ?>
					<?php if ( ! empty( $s['description'] ) ) : ?>
						<p class="tadris-heading-4__description"><?php echo esc_html( $s['description'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>
			<?php if ( 'yes' === $s['show_button'] && ! empty( $s['button_text'] ) ) : ?>
				<?php $this->add_link_attributes( 'button_link', $s['button_link'] ); ?>
				<a class="tadris-heading-4__button" <?php echo $this->get_render_attribute_string( 'button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span class="tadris-heading-4__button-text"><?php echo esc_html( $s['button_text'] ); ?></span>
					<?php if ( ! empty( $s['button_icon']['value'] ) ) : ?>
						<span class="tadris-heading-4__button-icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s, 'button_icon_color_mode' ) ); ?>">
							<?php Icons_Manager::render_icon( $s['button_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}

/** Right-aligned section heading with red subtitle, bold title, and accent line. */
class Tadris_Heading_5_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;
	public function get_name() { return 'webmz-tadris-heading-5'; }
	public function get_title() { return esc_html__( 'هدینگ آکام ۵', 'tadris' ); }
	public function get_icon() { return 'eicon-heading'; }
	public function get_categories() { return array( 'webmz-widgets' ); }
	public function get_style_depends() { return array( 'webmz-tadris-heading-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'subtitle', array( 'label' => esc_html__( 'زیرعنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'دوره‌های محبوب', 'tadris' ), 'label_block' => true ) );
		$this->add_control( 'title', array( 'label' => esc_html__( 'عنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'انتخابی مناسب برای شما', 'tadris' ), 'label_block' => true ) );
		$this->webmz_register_title_tag_control();
		$this->add_control( 'show_accent', array( 'label' => esc_html__( 'نمایش خط تزئینی', 'tadris' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'box_style', esc_html__( 'باکس', 'tadris' ), '.tadris-heading-5' );
		$this->webmz_register_text_style_controls( 'subtitle_style', esc_html__( 'زیرعنوان', 'tadris' ), '.tadris-heading-5__subtitle' );
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.tadris-heading-5__title' );
		$this->start_controls_section( 'accent_style', array( 'label' => esc_html__( 'خط تزئینی', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'show_accent' => 'yes' ) ) );
		$this->add_control( 'accent_color', array( 'label' => esc_html__( 'رنگ', 'tadris' ), 'type' => Controls_Manager::COLOR, 'default' => '#f21e3f', 'selectors' => array( '{{WRAPPER}} .tadris-heading-5__accent' => 'background-color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'accent_width', array( 'label' => esc_html__( 'عرض', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'range' => array( 'px' => array( 'min' => 20, 'max' => 200 ) ), 'default' => array( 'size' => 48, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .tadris-heading-5__accent' => 'width: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'accent_height', array( 'label' => esc_html__( 'ارتفاع', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'range' => array( 'px' => array( 'min' => 2, 'max' => 12 ) ), 'default' => array( 'size' => 4, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .tadris-heading-5__accent' => 'height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'accent_radius', array( 'label' => esc_html__( 'گردی گوشه', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'range' => array( 'px' => array( 'min' => 0, 'max' => 20 ) ), 'default' => array( 'size' => 2, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .tadris-heading-5__accent' => 'border-radius: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		$title_tag = $this->webmz_get_title_tag( $s );
		?>
		<div class="tadris-heading-5">
			<?php if ( ! empty( $s['subtitle'] ) ) : ?>
				<span class="tadris-heading-5__subtitle"><?php echo esc_html( $s['subtitle'] ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $s['title'] ) ) : ?>
				<<?php echo esc_html( $title_tag ); ?> class="tadris-heading-5__title"><?php echo esc_html( $s['title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
			<?php endif; ?>
			<?php if ( 'yes' === $s['show_accent'] ) : ?>
				<span class="tadris-heading-5__accent" aria-hidden="true"></span>
			<?php endif; ?>
		</div>
		<?php
	}
}

/** Standalone view-all link. */
class Tadris_View_All_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;
	public function get_name() { return 'webmz-tadris-view-all'; }
	public function get_title() { return esc_html__( 'مشاهده همه', 'tadris' ); }
	public function get_icon() { return 'eicon-button'; }
	public function get_categories() { return array( 'webmz-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'text', array( 'label' => esc_html__( 'متن', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'مشاهده همه', 'tadris' ) ) );
		$this->add_control( 'link', array( 'label' => esc_html__( 'لینک', 'tadris' ), 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ) ) );
		$this->add_control( 'icon', array( 'label' => esc_html__( 'آیکون', 'tadris' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-arrow-left', 'library' => 'fa-solid' ) ) );
		$this->webmz_register_icon_color_mode_control();
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'box_style', esc_html__( 'لینک مشاهده همه', 'tadris' ), '.tadris-view-all' );
		$this->webmz_register_text_style_controls( 'text_style', esc_html__( 'متن', 'tadris' ), '.tadris-view-all' );
		$this->webmz_register_icon_style_controls( 'icon_style', esc_html__( 'آیکون', 'tadris' ), '.tadris-view-all' );
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		$this->add_link_attributes( 'link', $s['link'] );
		?>
		<a class="tadris-view-all <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s ) ); ?>" <?php echo $this->get_render_attribute_string( 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php echo esc_html( $s['text'] ); ?>
			<?php Icons_Manager::render_icon( $s['icon'], array( 'aria-hidden' => 'true' ) ); ?>
		</a>
		<?php
	}
}

/** Count-up number widget matching the supplied HTML structure. */
class Tadris_Counter_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;
	public function get_name() { return 'webmz-tadris-counter'; }
	public function get_title() { return esc_html__( 'شمارنده آکام', 'tadris' ); }
	public function get_icon() { return 'eicon-counter'; }
	public function get_categories() { return array( 'webmz-widgets' ); }
	public function get_script_depends() { return array( 'webmz-tadris-widgets' ); }
	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'title', array( 'label' => esc_html__( 'عنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'دوره‌ی آموزشی رایگان برتر وردپرس', 'tadris' ), 'label_block' => true ) );
		$this->add_control( 'start_number', array( 'label' => esc_html__( 'عدد شروع', 'tadris' ), 'type' => Controls_Manager::NUMBER, 'default' => 0 ) );
		$this->add_control( 'end_number', array( 'label' => esc_html__( 'عدد نهایی', 'tadris' ), 'type' => Controls_Manager::NUMBER, 'default' => 250 ) );
		$this->add_control( 'prefix', array( 'label' => esc_html__( 'پیشوند', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => '+' ) );
		$this->add_control( 'suffix', array( 'label' => esc_html__( 'پسوند', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => '' ) );
		$this->add_control( 'duration', array( 'label' => esc_html__( 'مدت انیمیشن (میلی ثانیه)', 'tadris' ), 'type' => Controls_Manager::NUMBER, 'default' => 1400, 'min' => 100, 'max' => 10000 ) );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'box_style', esc_html__( 'باکس', 'tadris' ), '.tadris-counter' );
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.tadris-counter-title' );
		$this->webmz_register_text_style_controls( 'number_style', esc_html__( 'عدد', 'tadris' ), '.tadris-counter-number' );
	}
	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="tadris-counter">
			<h2 class="tadris-counter-title"><?php echo esc_html( $s['title'] ); ?></h2>
			<p class="tadris-counter-number" data-count-start="<?php echo esc_attr( (int) $s['start_number'] ); ?>" data-count-end="<?php echo esc_attr( (int) $s['end_number'] ); ?>" data-count-duration="<?php echo esc_attr( absint( $s['duration'] ) ); ?>" data-count-prefix="<?php echo esc_attr( $s['prefix'] ); ?>" data-count-suffix="<?php echo esc_attr( $s['suffix'] ); ?>"><?php echo esc_html( $s['prefix'] . (int) $s['end_number'] . $s['suffix'] ); ?></p>
		</div>
		<?php
	}
}
