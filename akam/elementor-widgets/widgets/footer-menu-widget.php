<?php
/**
 * Footer Menu — multi-column link grid with shared chevron icon.
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

/**
 * Simple footer menu widget with repeater items and a global item icon.
 */
class Footer_Menu_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-footer-menu';
	}

	public function get_title() {
		return esc_html__( 'منو فوتر', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'footer', 'menu', 'links', 'فوتر', 'منو', 'لینک' );
	}

	public function get_style_depends() {
		return array( 'webmz-footer-menu' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'item_icon',
			array(
				'label'   => esc_html__( 'آیکون آیتم‌ها', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'item_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون', 'tadris' ) );

		$repeater = new Repeater();
		$repeater->add_control(
			'label',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دوره های آموزشی', 'tadris' ),
				'label_block' => true,
			)
		);
		$repeater->add_control(
			'link',
			array(
				'label'       => esc_html__( 'لینک', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => '#' ),
				'placeholder' => 'https://example.com',
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'آیتم‌ها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array(
						'label' => esc_html__( 'دوره های آموزشی', 'tadris' ),
						'link'  => array( 'url' => '#' ),
					),
					array(
						'label' => esc_html__( 'دوره های رایگان ما', 'tadris' ),
						'link'  => array( 'url' => '#' ),
					),
					array(
						'label' => esc_html__( 'دانشنامه مقالات', 'tadris' ),
						'link'  => array( 'url' => '#' ),
					),
					array(
						'label' => esc_html__( 'قالب های وردپرس', 'tadris' ),
						'link'  => array( 'url' => '#' ),
					),
					array(
						'label' => esc_html__( 'افزونه های وردپرس', 'tadris' ),
						'link'  => array( 'url' => '#' ),
					),
					array(
						'label' => esc_html__( 'سایت های آماده', 'tadris' ),
						'link'  => array( 'url' => '#' ),
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'layout_style_section',
			array(
				'label' => esc_html__( 'چیدمان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'     => esc_html__( 'تعداد ستون', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 6,
				'default'   => 2,
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-menu__list' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'      => esc_html__( 'فاصله ستون‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 120 ),
				),
				'default'    => array(
					'size' => 40,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-menu__list' => 'column-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => esc_html__( 'فاصله ردیف‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
				),
				'default'    => array(
					'size' => 14,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-menu__list' => 'row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_gap',
			array(
				'label'      => esc_html__( 'فاصله آیکون و متن', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'size' => 8,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-menu__link' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'label_style_section',
			array(
				'label' => esc_html__( 'متن آیتم‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-secondary, #092c4c)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-menu__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'label_hover_color',
			array(
				'label'     => esc_html__( 'رنگ متن در هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary, #0878f9)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-menu__link:hover .webmz-footer-menu__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .webmz-footer-menu__label',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'icon_style_section',
			array(
				'label' => esc_html__( 'آیکون آیتم‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-gray, #94a3b8)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-menu__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون در هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary, #0878f9)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-menu__link:hover .webmz-footer-menu__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 6, 'max' => 40 ),
				),
				'default'    => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-menu__icon i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-footer-menu__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$icon_class = $this->webmz_get_icon_color_mode_class( $settings, 'item_icon_color_mode' );
		$has_icon   = ! empty( $settings['item_icon']['value'] );
		?>
		<nav class="webmz-footer-menu" aria-label="<?php echo esc_attr__( 'منو فوتر', 'tadris' ); ?>">
			<?php if ( ! empty( $settings['items'] ) && is_array( $settings['items'] ) ) : ?>
				<ul class="webmz-footer-menu__list">
					<?php foreach ( $settings['items'] as $index => $item ) : ?>
						<?php
						$label = ! empty( $item['label'] ) ? $item['label'] : '';
						if ( '' === $label ) {
							continue;
						}
						$key = 'footer_menu_item_' . absint( $index );
						$this->add_link_attributes( $key, ! empty( $item['link'] ) && is_array( $item['link'] ) ? $item['link'] : array( 'url' => '#' ) );
						?>
						<li class="webmz-footer-menu__item">
							<a class="webmz-footer-menu__link" <?php echo $this->get_render_attribute_string( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<?php if ( $has_icon ) : ?>
									<span class="webmz-footer-menu__icon <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true">
										<?php Icons_Manager::render_icon( $settings['item_icon'], array( 'aria-hidden' => 'true' ) ); ?>
									</span>
								<?php endif; ?>
								<span class="webmz-footer-menu__label"><?php echo esc_html( $label ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</nav>
		<?php
	}
}
