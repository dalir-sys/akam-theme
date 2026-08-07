<?php
/**
 * Icon details box — horizontal stats bar with repeater items.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Repeater-based icon + title + subtitle details box.
 */
class Icon_Details_Box_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-icon-details-box';
	}

	public function get_title() {
		return esc_html__( 'باکس جزئیات آیکون‌دار', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-info-box';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'stats', 'icon', 'details', 'box', 'repeater', 'آمار', 'آیکون', 'جزئیات', 'باکس' );
	}

	public function get_style_depends() {
		return array( 'webmz-icon-details-box' );
	}

	protected function register_controls() {
		$this->register_items_controls();
		$this->register_layout_controls();
		$this->register_style_controls();
	}

	protected function register_items_controls() {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => esc_html__( 'آیتم‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'آیکون', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-users',
					'library' => 'fa-solid',
				),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان (مقدار)', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( '150+', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'زیرعنوان (برچسب)', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'استاد حرفه‌ای', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'enable_link',
			array(
				'label'        => esc_html__( 'فعال‌سازی لینک', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'     => esc_html__( 'لینک', 'tadris' ),
				'type'      => Controls_Manager::URL,
				'default'   => array( 'url' => '' ),
				'condition' => array( 'enable_link' => 'yes' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'لیست آیتم‌ها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'icon'     => array( 'value' => 'fas fa-globe', 'library' => 'fa-solid' ),
						'title'    => esc_html__( '+20 کشور', 'tadris' ),
						'subtitle' => esc_html__( 'اساتید بین‌المللی', 'tadris' ),
					),
					array(
						'icon'     => array( 'value' => 'fas fa-award', 'library' => 'fa-solid' ),
						'title'    => esc_html__( '+10 سال', 'tadris' ),
						'subtitle' => esc_html__( 'میانگین سابقه', 'tadris' ),
					),
					array(
						'icon'     => array( 'value' => 'fas fa-users', 'library' => 'fa-solid' ),
						'title'    => esc_html__( '150+', 'tadris' ),
						'subtitle' => esc_html__( 'استاد حرفه‌ای', 'tadris' ),
					),
				),
			)
		);

		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ HTML عنوان', 'tadris' ) );
		$this->webmz_register_icon_color_mode_control();

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
			'columns',
			array(
				'label'   => esc_html__( 'تعداد ستون', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-idb__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'items_gap',
			array(
				'label'      => esc_html__( 'فاصله بین آیتم‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-idb__grid' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_inner_gap',
			array(
				'label'      => esc_html__( 'فاصله آیکون و متن', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-idb__item-inner' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_alignment',
			array(
				'label'   => esc_html__( 'تراز افقی آیتم', 'tadris' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'شروع', 'tadris' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'وسط', 'tadris' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'پایان', 'tadris' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .webmz-idb__item-inner' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_vertical_alignment',
			array(
				'label'   => esc_html__( 'تراز عمودی آیتم', 'tadris' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'بالا', 'tadris' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'وسط', 'tadris' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'پایین', 'tadris' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .webmz-idb__item-inner' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'show_dividers',
			array(
				'label'        => esc_html__( 'نمایش جداکننده', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_responsive_control(
			'divider_width',
			array(
				'label'      => esc_html__( 'ضخامت جداکننده', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 1, 'max' => 10 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 1,
				),
				'condition'  => array( 'show_dividers' => 'yes' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-idb__item:not(:last-child)' => 'border-inline-end-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'divider_color',
			array(
				'label'     => esc_html__( 'رنگ جداکننده', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eeeeee',
				'condition' => array( 'show_dividers' => 'yes' ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-idb__item:not(:last-child)' => 'border-inline-end-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'divider_spacing',
			array(
				'label'      => esc_html__( 'فاصله داخلی جداکننده', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'condition'  => array( 'show_dividers' => 'yes' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-idb__item:not(:last-child)' => 'padding-inline-end: {{SIZE}}{{UNIT}}; margin-inline-end: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->webmz_register_box_style_controls(
			'box_style',
			esc_html__( 'باکس', 'tadris' ),
			'.webmz-idb',
			array(
				'default_background' => '#ffffff',
				'bordered'           => true,
			)
		);

		$this->webmz_register_icon_style_controls(
			'item_icon_style',
			esc_html__( 'آیکون', 'tadris' ),
			'.webmz-idb__icon'
		);

		$this->start_controls_section(
			'item_icon_box_style',
			array(
				'label' => esc_html__( 'باکس آیکون', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'item_icon_box_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => array(
					'{{WRAPPER}} .webmz-idb__icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_icon_box_size',
			array(
				'label'      => esc_html__( 'اندازه باکس', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 20, 'max' => 120 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 32,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-idb__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_icon_box_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 50 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-idb__icon' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_icon_box_border',
				'selector' => '{{WRAPPER}} .webmz-idb__icon',
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls(
			'title_style',
			esc_html__( 'عنوان', 'tadris' ),
			'.webmz-idb__title'
		);

		$this->webmz_register_text_style_controls(
			'subtitle_style',
			esc_html__( 'زیرعنوان', 'tadris' ),
			'.webmz-idb__subtitle'
		);

		$this->start_controls_section(
			'item_style',
			array(
				'label' => esc_html__( 'آیتم', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی آیتم', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'    => '16',
					'right'  => '20',
					'bottom' => '16',
					'left'   => '20',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-idb__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render a single repeater item.
	 *
	 * @param array<string,mixed> $item         Repeater item settings.
	 * @param int                 $index        Item index.
	 * @param string              $title_tag    Sanitized heading tag.
	 * @param string              $icon_class   Icon color mode class.
	 * @return void
	 */
	protected function render_item( $item, $index, $title_tag, $icon_class ) {
		$title    = isset( $item['title'] ) ? trim( (string) $item['title'] ) : '';
		$subtitle = isset( $item['subtitle'] ) ? trim( (string) $item['subtitle'] ) : '';

		if ( '' === $title && '' === $subtitle ) {
			return;
		}

		$has_link = 'yes' === ( $item['enable_link'] ?? '' ) && ! empty( $item['link']['url'] );
		$tag      = $has_link ? 'a' : 'div';
		$key      = 'idb_item_' . absint( $index );

		$this->add_render_attribute( $key, 'class', 'webmz-idb__item' );

		if ( $has_link ) {
			$this->add_link_attributes( $key, $item['link'] );
		}

		?>
		<<?php echo tag_escape( $tag ); ?> <?php echo $this->get_render_attribute_string( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="webmz-idb__item-inner" dir="ltr">
				<span class="webmz-idb__icon <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true">
					<?php
					if ( ! empty( $item['icon']['value'] ) ) {
						Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) );
					}
					?>
				</span>
				<div class="webmz-idb__text">
					<?php if ( '' !== $title ) : ?>
						<<?php echo esc_html( $title_tag ); ?> class="webmz-idb__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $title_tag ); ?>>
					<?php endif; ?>
					<?php if ( '' !== $subtitle ) : ?>
						<span class="webmz-idb__subtitle"><?php echo esc_html( $subtitle ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</<?php echo tag_escape( $tag ); ?>>
		<?php
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$items       = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();
		$title_tag   = $this->webmz_get_title_tag( $settings, 'title_tag' );
		$icon_class  = $this->webmz_get_icon_color_mode_class( $settings );
		$dividers    = ! empty( $settings['show_dividers'] ) && 'yes' === $settings['show_dividers'];
		$box_classes = 'webmz-idb' . ( $dividers ? ' webmz-idb--dividers' : '' );

		if ( empty( $items ) ) {
			return;
		}
		?>
		<div class="<?php echo esc_attr( $box_classes ); ?>">
			<div class="webmz-idb__grid">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php $this->render_item( $item, $index, $title_tag, $icon_class ); ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
