<?php
/**
 * Footer and social Elementor widgets for WebMZ/Tadris.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

 defined( 'ABSPATH' ) || exit;

/** Instagram follow button widget. */
class Tadris_Instagram_Follow_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-tadris-instagram-follow';
	}

	public function get_title() {
		return esc_html__( 'دکمه دنبال کردن اینستاگرام', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-instagram-gallery';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'instagram', 'follow', 'social', 'اینستاگرام', 'دنبال کردن' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'text',
			array(
				'label'       => esc_html__( 'متن اصلی', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'پیج اینستاگرام ما رو دنبال کنید', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'username',
			array(
				'label'       => esc_html__( 'آیدی اینستاگرام', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '@rezadailirofficial',
				'label_block' => true,
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => esc_html__( 'لینک اینستاگرام', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#' ),
				'placeholder' => 'https://instagram.com/username',
			)
		);

		$this->add_control(
			'image',
			array(
				'label'       => esc_html__( 'تصویر / آیکون اینستاگرام', 'tadris' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => esc_html__( 'برای حالت مشابه نمونه، یک تصویر PNG سه‌بعدی اینستاگرام آپلود کنید. اگر خالی باشد آیکون پیش‌فرض نمایش داده می‌شود.', 'tadris' ),
			)
		);

		$this->add_control(
			'arrow_icon',
			array(
				'label'   => esc_html__( 'آیکون دکمه کوچک', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'arrow_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون کوچک', 'tadris' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'box_style_section',
			array(
				'label' => esc_html__( 'باکس', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'box_width',
			array(
				'label'      => esc_html__( 'عرض', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 180, 'max' => 900 ),
					'%'  => array( 'min' => 10, 'max' => 100 ),
				),
				'default'    => array( 'size' => 620, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-instagram-follow' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_min_height',
			array(
				'label'      => esc_html__( 'حداقل ارتفاع', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 48, 'max' => 160 ),
				),
				'default'    => array( 'size' => 74, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-instagram-follow' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'box_bg',
			array(
				'label'     => esc_html__( 'رنگ پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-instagram-follow' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => 16,
					'right'    => 16,
					'bottom'   => 16,
					'left'     => 16,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-instagram-follow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'top'      => 10,
					'right'    => 14,
					'bottom'   => 10,
					'left'     => 22,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-instagram-follow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_gap',
			array(
				'label'     => esc_html__( 'فاصله اجزا', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'   => array( 'size' => 18, 'unit' => 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-instagram-follow' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'text_style_section',
			array(
				'label' => esc_html__( 'متن‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'رنگ متن اصلی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-instagram-follow__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'label'    => esc_html__( 'تایپوگرافی متن اصلی', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-instagram-follow__text',
			)
		);

		$this->add_control(
			'username_color',
			array(
				'label'     => esc_html__( 'رنگ آیدی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-instagram-follow__username' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'username_typography',
				'label'    => esc_html__( 'تایپوگرافی آیدی', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-instagram-follow__username',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'icons_style_section',
			array(
				'label' => esc_html__( 'آیکون و تصویر', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_size',
			array(
				'label'     => esc_html__( 'اندازه تصویر اینستاگرام', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 24, 'max' => 150 ) ),
				'default'   => array( 'size' => 92, 'unit' => 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-instagram-follow__image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'     => esc_html__( 'اندازه دکمه کوچک', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 28, 'max' => 90 ) ),
				'default'   => array( 'size' => 52, 'unit' => 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-instagram-follow__arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_icon_size',
			array(
				'label'     => esc_html__( 'اندازه آیکون کوچک', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 8, 'max' => 50 ) ),
				'default'   => array( 'size' => 16, 'unit' => 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-instagram-follow__arrow i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-instagram-follow__arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'arrow_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه دکمه کوچک', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-instagram-follow__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون کوچک', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-instagram-follow__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$gradient_id = 'webmz-instagram-gradient-' . esc_attr( $this->get_id() );
		$link        = ! empty( $settings['link'] ) && is_array( $settings['link'] ) ? $settings['link'] : array( 'url' => '#' );
		$this->add_link_attributes( 'instagram_link', $link );
		?>
		<a class="webmz-instagram-follow" <?php echo $this->get_render_attribute_string( 'instagram_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<span class="webmz-instagram-follow__arrow <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'arrow_icon_color_mode' ) ); ?>">
				<?php Icons_Manager::render_icon( $settings['arrow_icon'], array( 'aria-hidden' => 'true' ) ); ?>
			</span>
			<span class="webmz-instagram-follow__content">
				<strong class="webmz-instagram-follow__text"><?php echo esc_html( $settings['text'] ); ?></strong>
				<span class="webmz-instagram-follow__username"><?php echo esc_html( $settings['username'] ); ?></span>
			</span>
			<span class="webmz-instagram-follow__image" aria-hidden="true">
				<?php if ( ! empty( $settings['image']['id'] ) ) : ?>
					<?php echo wp_get_attachment_image( absint( $settings['image']['id'] ), 'medium', false, array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
				<?php else : ?>
					<svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<defs>
							<linearGradient id="<?php echo esc_attr( $gradient_id ); ?>" x1="20" y1="105" x2="105" y2="15" gradientUnits="userSpaceOnUse">
								<stop stop-color="#FEDA75" />
								<stop offset="0.32" stop-color="#FA7E1E" />
								<stop offset="0.58" stop-color="#D62976" />
								<stop offset="1" stop-color="#4F5BD5" />
							</linearGradient>
						</defs>
						<rect x="14" y="14" width="92" height="92" rx="28" fill="url(#<?php echo esc_attr( $gradient_id ); ?>)" />
						<rect x="39" y="39" width="42" height="42" rx="14" stroke="white" stroke-width="7" />
						<circle cx="60" cy="60" r="11" stroke="white" stroke-width="7" />
						<circle cx="83" cy="37" r="5" fill="white" />
					</svg>
				<?php endif; ?>
			</span>
		</a>
		<?php
	}
}

/** Footer menu widget. */
class Tadris_Footer_Menu_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-tadris-footer-menu';
	}

	public function get_title() {
		return esc_html__( 'منوی فوتر آکام', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-editor-list-ul';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'footer', 'menu', 'links', 'فوتر', 'منو', 'لینک' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'آیکون', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-th-large',
					'library' => 'fa-solid',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control();

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دسته‌بندی‌ها', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ عنوان', 'tadris' ) );

		$repeater = new Repeater();
		$repeater->add_control(
			'link_title',
			array(
				'label'       => esc_html__( 'عنوان لینک', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'آموزش وردپرس رایگان', 'tadris' ),
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'link_url',
			array(
				'label'       => esc_html__( 'آدرس لینک', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#' ),
				'placeholder' => 'https://example.com',
			)
		);

		$this->add_control(
			'links',
			array(
				'label'       => esc_html__( 'لینک‌ها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ link_title }}}',
				'default'     => array(
					array( 'link_title' => esc_html__( 'ستارگان وب', 'tadris' ), 'link_url' => array( 'url' => '#' ) ),
					array( 'link_title' => esc_html__( 'آموزش وردپرس رایگان', 'tadris' ), 'link_url' => array( 'url' => '#' ) ),
					array( 'link_title' => esc_html__( 'دوره‌های رایگان', 'tadris' ), 'link_url' => array( 'url' => '#' ) ),
					array( 'link_title' => esc_html__( 'پادکست‌ها', 'tadris' ), 'link_url' => array( 'url' => '#' ) ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'box_style_section',
			array(
				'label' => esc_html__( 'باکس', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-menu-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'items_gap',
			array(
				'label'     => esc_html__( 'فاصله لینک‌ها', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'   => array( 'size' => 12, 'unit' => 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-menu-widget__list' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'header_style_section',
			array(
				'label' => esc_html__( 'عنوان و آیکون', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-secondary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-menu-widget__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-footer-menu-widget__title',
			)
		);

		$this->add_responsive_control(
			'icon_box_size',
			array(
				'label'     => esc_html__( 'اندازه باکس آیکون', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 24, 'max' => 90 ) ),
				'default'   => array( 'size' => 42, 'unit' => 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-menu-widget__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'     => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 10, 'max' => 50 ) ),
				'default'   => array( 'size' => 18, 'unit' => 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-menu-widget__icon i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-footer-menu-widget__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-menu-widget__icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-menu-widget__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'links_style_section',
			array(
				'label' => esc_html__( 'لینک‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => esc_html__( 'رنگ لینک', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-gray)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-menu-widget__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_hover_color',
			array(
				'label'     => esc_html__( 'رنگ لینک در هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-menu-widget__link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'label'    => esc_html__( 'تایپوگرافی لینک‌ها', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-footer-menu-widget__link',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$title_tag = $this->webmz_get_title_tag( $settings );
		?>
		<nav class="webmz-footer-menu-widget" aria-label="<?php echo esc_attr( $settings['title'] ); ?>">
			<div class="webmz-footer-menu-widget__head">
				<span class="webmz-footer-menu-widget__icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings ) ); ?>" aria-hidden="true">
					<?php Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</span>
				<<?php echo esc_html( $title_tag ); ?> class="webmz-footer-menu-widget__title"><?php echo esc_html( $settings['title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
			</div>

			<?php if ( ! empty( $settings['links'] ) && is_array( $settings['links'] ) ) : ?>
				<ul class="webmz-footer-menu-widget__list">
					<?php foreach ( $settings['links'] as $index => $item ) : ?>
						<?php
						$title = ! empty( $item['link_title'] ) ? $item['link_title'] : '';
						if ( '' === $title ) {
							continue;
						}
						$key = 'footer_menu_link_' . absint( $index );
						$this->add_link_attributes( $key, ! empty( $item['link_url'] ) && is_array( $item['link_url'] ) ? $item['link_url'] : array( 'url' => '#' ) );
						?>
						<li class="webmz-footer-menu-widget__item">
							<a class="webmz-footer-menu-widget__link" <?php echo $this->get_render_attribute_string( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $title ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</nav>
		<?php
	}
}
