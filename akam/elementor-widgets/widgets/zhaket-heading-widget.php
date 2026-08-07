<?php
/**
 * Zhaket section heading — icon badge, title, and view-all link.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Horizontal RTL heading row (Zhaket marketplace style).
 */
class Zhaket_Heading_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-zhaket-heading';
	}

	public function get_title() {
		return esc_html__( 'هدینگ ژاکت', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-heading';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'zhaket', 'heading', 'section', 'view all', 'ژاکت', 'هدینگ', 'مشاهده همه', 'تخفیف' );
	}

	public function get_style_depends() {
		return array( 'webmz-zhaket-heading' );
	}

	/**
	 * Theme primary color from options.
	 *
	 * @return string
	 */
	private function theme_primary_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_primary' ) : '#0878f9';
	}

	/**
	 * Theme text dark color from options.
	 *
	 * @return string
	 */
	private function theme_text_dark_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_text_dark' ) : '#111827';
	}

	/**
	 * Theme primary light color from options.
	 *
	 * @return string
	 */
	private function theme_primary_light_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_primary_light' ) : '#ffffff';
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_layout_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_badge',
			array(
				'label'        => esc_html__( 'نمایش نشان آیکون', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'badge_icon',
			array(
				'label'     => esc_html__( 'آیکون نشان', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-percent',
					'library' => 'fa-solid',
				),
				'condition' => array( 'show_badge' => 'yes' ),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'badge_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون نشان', 'tadris' ),
			array( 'show_badge' => 'yes' )
		);

		$this->add_control(
			'heading_text',
			array(
				'label'       => esc_html__( 'متن عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'پرتخفیف های ژاکت، همین حالا بخرید!', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control( 'heading_tag', esc_html__( 'تگ HTML عنوان', 'tadris' ) );

		$this->add_control(
			'show_button',
			array(
				'label'        => esc_html__( 'نمایش دکمه مشاهده همه', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'متن دکمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'مشاهده همه', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'       => esc_html__( 'لینک دکمه', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#' ),
				'condition'   => array( 'show_button' => 'yes' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'button_icon',
			array(
				'label'     => esc_html__( 'آیکون دکمه', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
				'condition' => array( 'show_button' => 'yes' ),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'button_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون دکمه', 'tadris' ),
			array( 'show_button' => 'yes' )
		);

		$this->add_control(
			'button_icon_position',
			array(
				'label'     => esc_html__( 'موقعیت آیکون دکمه', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'before',
				'options'   => array(
					'before' => esc_html__( 'قبل از متن', 'tadris' ),
					'after'  => esc_html__( 'بعد از متن', 'tadris' ),
				),
				'condition' => array( 'show_button' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'چیدمان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => esc_html__( 'فاصله بین عنوان و دکمه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zh-heading' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'lead_gap',
			array(
				'label'      => esc_html__( 'فاصله نشان و عنوان', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zh-heading__lead' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_gap',
			array(
				'label'      => esc_html__( 'فاصله متن و آیکون دکمه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 30 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 6,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zh-heading__button' => 'gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_control(
			'mobile_stack',
			array(
				'label'        => esc_html__( 'چیدمان عمودی در موبایل', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->webmz_register_box_style_controls(
			'box_style',
			esc_html__( 'باکس', 'tadris' ),
			'.webmz-zh-heading',
			array( 'flat' => true )
		);

		$this->webmz_register_heading_icon_box_style_controls(
			'.webmz-zh-heading__badge',
			array(
				'condition'      => array( 'show_badge' => 'yes' ),
				'bg_default'     => $this->theme_primary_color(),
				'icon_default'   => $this->theme_primary_light_color(),
				'include_shadow' => true,
			)
		);

		$this->start_controls_section(
			'heading_text_style',
			array(
				'label' => esc_html__( 'عنوان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_text_dark_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zh-heading__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'selector'       => '{{WRAPPER}} .webmz-zh-heading__title',
				'fields_options' => array(
					'font_weight' => array(
						'default' => '700',
					),
					'font_size'   => array(
						'default' => array(
							'unit' => 'px',
							'size' => 18,
						),
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_box_style',
			array(
				'label'     => esc_html__( 'دکمه', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'           => 'button_box_style_background',
				'selector'       => '{{WRAPPER}} .webmz-zh-heading__button',
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
					'color'      => array(
						'default' => 'transparent',
					),
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'           => 'button_box_style_border',
				'selector'       => '{{WRAPPER}} .webmz-zh-heading__button',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'unit'     => 'px',
							'isLinked' => true,
						),
					),
					'color'  => array(
						'default' => 'rgba(15, 23, 42, 0.12)',
					),
				),
			)
		);

		$this->add_responsive_control(
			'button_box_style_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => 8,
					'right'    => 8,
					'bottom'   => 8,
					'left'     => 8,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zh-heading__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_box_style_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'default'    => array(
					'top'      => 8,
					'right'    => 16,
					'bottom'   => 8,
					'left'     => 16,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zh-heading__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_style_shadow',
				'selector' => '{{WRAPPER}} .webmz-zh-heading__button',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_text_style',
			array(
				'label'     => esc_html__( 'متن دکمه', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zh-heading__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_text_hover_color',
			array(
				'label'     => esc_html__( 'رنگ هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_primary_hover' ) : '#0662cc',
				'selectors' => array(
					'{{WRAPPER}} .webmz-zh-heading__button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'           => 'button_typography',
				'selector'       => '{{WRAPPER}} .webmz-zh-heading__button',
				'fields_options' => array(
					'font_weight' => array(
						'default' => '500',
					),
					'font_size'   => array(
						'default' => array(
							'unit' => 'px',
							'size' => 14,
						),
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_icon_style',
			array(
				'label'     => esc_html__( 'آیکون دکمه', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_button' => 'yes' ),
			)
		);

		$this->add_control(
			'button_icon_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $this->theme_primary_color(),
				'selectors' => $this->webmz_get_icon_color_value_selectors( '{{WRAPPER}} .webmz-zh-heading__button-icon' ),
			)
		);

		$this->add_control(
			'button_icon_hover_color',
			array(
				'label'     => esc_html__( 'رنگ هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_primary_hover' ) : '#0662cc',
				'selectors' => $this->webmz_get_icon_color_value_selectors( '{{WRAPPER}} .webmz-zh-heading__button:hover .webmz-zh-heading__button-icon' ),
			)
		);

		$this->add_responsive_control(
			'button_icon_size',
			array(
				'label'      => esc_html__( 'اندازه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 8, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zh-heading__button-icon svg, {{WRAPPER}} .webmz-zh-heading__button-icon i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$heading     = isset( $settings['heading_text'] ) ? trim( (string) $settings['heading_text'] ) : '';
		$show_badge  = 'yes' === ( $settings['show_badge'] ?? '' );
		$show_button = 'yes' === ( $settings['show_button'] ?? '' );
		$stack       = ! empty( $settings['mobile_stack'] ) && 'yes' === $settings['mobile_stack'];
		$title_tag   = $this->webmz_get_title_tag( $settings, 'heading_tag' );
		$classes     = 'webmz-zh-heading' . ( $stack ? ' webmz-zh-heading--stack-mobile' : '' );

		if ( '' === $heading && ! $show_badge && ! $show_button ) {
			return;
		}
		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<div class="webmz-zh-heading__lead">
				<?php if ( $show_badge && ! empty( $settings['badge_icon']['value'] ) ) : ?>
					<span class="webmz-zh-heading__badge <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'badge_icon_color_mode' ) ); ?>" aria-hidden="true">
						<?php Icons_Manager::render_icon( $settings['badge_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php endif; ?>

				<?php if ( '' !== $heading ) : ?>
					<<?php echo esc_html( $title_tag ); ?> class="webmz-zh-heading__title"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $title_tag ); ?>>
				<?php endif; ?>
			</div>

			<?php if ( $show_button && ! empty( $settings['button_text'] ) ) : ?>
				<?php
				$this->add_render_attribute( 'button_link', 'class', 'webmz-zh-heading__button' );
				if ( ! empty( $settings['button_link']['url'] ) ) {
					$this->add_link_attributes( 'button_link', $settings['button_link'] );
				} else {
					$this->add_render_attribute( 'button_link', 'href', '#' );
				}
				$icon_position = ! empty( $settings['button_icon_position'] ) ? $settings['button_icon_position'] : 'before';
				?>
				<a <?php echo $this->get_render_attribute_string( 'button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php if ( 'before' === $icon_position && ! empty( $settings['button_icon']['value'] ) ) : ?>
						<span class="webmz-zh-heading__button-icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'button_icon_color_mode' ) ); ?>" aria-hidden="true">
							<?php Icons_Manager::render_icon( $settings['button_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>

					<span class="webmz-zh-heading__button-text"><?php echo esc_html( $settings['button_text'] ); ?></span>

					<?php if ( 'after' === $icon_position && ! empty( $settings['button_icon']['value'] ) ) : ?>
						<span class="webmz-zh-heading__button-icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'button_icon_color_mode' ) ); ?>" aria-hidden="true">
							<?php Icons_Manager::render_icon( $settings['button_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}
