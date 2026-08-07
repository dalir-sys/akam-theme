<?php
/**
 * Header contact box Elementor widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Horizontal header contact strip with icon, phone number, and message.
 */
class Header_Contact_Box_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-header-contact-box';
	}

	public function get_title() {
		return esc_html__( 'باکس تماس هدر', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'contact', 'phone', 'header', 'تماس', 'هدر', 'تلفن' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'آیکون', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-phone-volume',
					'library' => 'fa-solid',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control();

		$this->add_control(
			'phone_prefix',
			array(
				'label'       => esc_html__( 'بخش اول شماره', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '۰۹۱۵',
				'description' => esc_html__( 'معمولاً پیش‌شماره یا ابتدای شماره تماس.', 'tadris' ),
			)
		);

		$this->add_control(
			'phone_main',
			array(
				'label'       => esc_html__( 'بخش دوم شماره', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '۰۰۰ ۰۰۰۰',
				'description' => esc_html__( 'ادامه شماره تماس که معمولاً برجسته نمایش داده می‌شود.', 'tadris' ),
			)
		);

		$this->add_control(
			'separator',
			array(
				'label'   => esc_html__( 'جداکننده', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => ':',
			)
		);

		$this->add_control(
			'text_before',
			array(
				'label'       => esc_html__( 'متن قبل از نام برند', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'کارشناسان', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'text_brand',
			array(
				'label'       => esc_html__( 'نام برند (برجسته)', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'وب پرداز', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'text_after',
			array(
				'label'       => esc_html__( 'متن بعد از نام برند', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'پاسخگوی سوالات شما هستند', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'enable_link',
			array(
				'label'        => esc_html__( 'فعال‌سازی لینک', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'link',
			array(
				'label'     => esc_html__( 'لینک تماس', 'tadris' ),
				'type'      => Controls_Manager::URL,
				'default'   => array( 'url' => 'tel:09150000000' ),
				'condition' => array( 'enable_link' => 'yes' ),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls(
			'box_style',
			esc_html__( 'باکس', 'tadris' ),
			'.webmz-header-contact-box',
			array( 'flat' => true )
		);

		$this->start_controls_section(
			'layout_style',
			array(
				'label' => esc_html__( 'چیدمان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'layout_direction',
			array(
				'label'   => esc_html__( 'جهت نمایش', 'tadris' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'rtl' => array(
						'title' => esc_html__( 'راست به چپ', 'tadris' ),
						'icon'  => 'eicon-text-align-right',
					),
					'ltr' => array(
						'title' => esc_html__( 'چپ به راست', 'tadris' ),
						'icon'  => 'eicon-text-align-left',
					),
				),
				'default'   => 'ltr',
				'selectors' => array(
					'{{WRAPPER}} .webmz-header-contact-box' => 'direction: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'layout_align',
			array(
				'label'     => esc_html__( 'چینش افقی', 'tadris' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'شروع', 'tadris' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'وسط', 'tadris' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'پایان', 'tadris' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .webmz-header-contact-box' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'layout_gap',
			array(
				'label'      => esc_html__( 'فاصله بین آیکون و متن', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-header-contact-box' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_gap',
			array(
				'label'      => esc_html__( 'فاصله بین بخش‌های متن', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 6,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-header-contact-box__content' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_icon_style_controls(
			'icon_style',
			esc_html__( 'آیکون', 'tadris' ),
			'.webmz-header-contact-box__icon'
		);

		$this->webmz_register_text_style_controls(
			'phone_prefix_style',
			esc_html__( 'بخش اول شماره', 'tadris' ),
			'.webmz-header-contact-box__phone-prefix'
		);

		$this->webmz_register_text_style_controls(
			'phone_main_style',
			esc_html__( 'بخش دوم شماره', 'tadris' ),
			'.webmz-header-contact-box__phone-main'
		);

		$this->webmz_register_text_style_controls(
			'separator_style',
			esc_html__( 'جداکننده', 'tadris' ),
			'.webmz-header-contact-box__separator'
		);

		$this->webmz_register_text_style_controls(
			'text_before_style',
			esc_html__( 'متن قبل از برند', 'tadris' ),
			'.webmz-header-contact-box__text-before'
		);

		$this->webmz_register_text_style_controls(
			'text_brand_style',
			esc_html__( 'نام برند', 'tadris' ),
			'.webmz-header-contact-box__text-brand'
		);

		$this->webmz_register_text_style_controls(
			'text_after_style',
			esc_html__( 'متن بعد از برند', 'tadris' ),
			'.webmz-header-contact-box__text-after'
		);
	}

	/**
	 * Render icon markup with optional fallback SVG.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	protected function render_icon( $settings ) {
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
		$settings = $this->get_settings_for_display();
		$tag      = 'div';
		$attrs    = 'class="webmz-header-contact-box"';

		if ( 'yes' === $settings['enable_link'] && ! empty( $settings['link']['url'] ) ) {
			$tag = 'a';
			$this->add_link_attributes( 'link', $settings['link'] );
			$attrs = 'class="webmz-header-contact-box" ' . $this->get_render_attribute_string( 'link' );
		}
		?>
		<<?php echo tag_escape( $tag ); ?> <?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="webmz-header-contact-box__icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings ) ); ?>">
				<?php $this->render_icon( $settings ); ?>
			</div>
			<div class="webmz-header-contact-box__content">
				<?php if ( '' !== trim( (string) $settings['phone_prefix'] ) || '' !== trim( (string) $settings['phone_main'] ) ) : ?>
					<span class="webmz-header-contact-box__phone">
						<?php if ( '' !== trim( (string) $settings['phone_prefix'] ) ) : ?>
							<span class="webmz-header-contact-box__phone-prefix"><?php echo esc_html( $settings['phone_prefix'] ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== trim( (string) $settings['phone_main'] ) ) : ?>
							<span class="webmz-header-contact-box__phone-main"><?php echo esc_html( $settings['phone_main'] ); ?></span>
						<?php endif; ?>
					</span>
				<?php endif; ?>
				<?php if ( '' !== trim( (string) $settings['separator'] ) ) : ?>
					<span class="webmz-header-contact-box__separator" aria-hidden="true"><?php echo esc_html( $settings['separator'] ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== trim( (string) $settings['text_before'] ) || '' !== trim( (string) $settings['text_brand'] ) || '' !== trim( (string) $settings['text_after'] ) ) : ?>
					<span class="webmz-header-contact-box__message">
						<?php if ( '' !== trim( (string) $settings['text_before'] ) ) : ?>
							<span class="webmz-header-contact-box__text-before"><?php echo esc_html( $settings['text_before'] ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== trim( (string) $settings['text_brand'] ) ) : ?>
							<span class="webmz-header-contact-box__text-brand"><?php echo esc_html( $settings['text_brand'] ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== trim( (string) $settings['text_after'] ) ) : ?>
							<span class="webmz-header-contact-box__text-after"><?php echo esc_html( $settings['text_after'] ); ?></span>
						<?php endif; ?>
					</span>
				<?php endif; ?>
			</div>
		</<?php echo tag_escape( $tag ); ?>>
		<?php
	}
}
