<?php
/**
 * Elementor icon-oriented navigation menu widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Walker that renders icons above labels with inline submenu arrows.
 */
class Icon_Navigation_Menu_Walker extends \Walker_Nav_Menu {

	/**
	 * Begin a submenu level.
	 *
	 * @param string   $output Used to append additional content.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   Menu arguments.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$level   = absint( $depth + 1 );
		$output .= "\n{$indent}<ul class=\"webmz-nav__submenu webmz-icon-nav__submenu webmz-nav__submenu--level-{$level}\">\n";
	}

	/**
	 * Start a single menu item.
	 *
	 * @param string   $output Used to append additional content.
	 * @param WP_Post  $item   Menu item object.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   Menu arguments.
	 * @param int      $id     Current item ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$indent       = $depth ? str_repeat( "\t", $depth ) : '';
		$core_classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes      = array(
			'webmz-nav__item',
			'webmz-icon-nav__item',
			'webmz-nav__item--level-' . absint( $depth ),
		);
		$has_children = in_array( 'menu-item-has-children', $core_classes, true );
		$mega_content = 0 === absint( $depth ) && function_exists( 'webmz_get_nav_menu_item_mega_content_id' ) ? webmz_get_nav_menu_item_mega_content_id( $item->ID ) : 0;
		$has_dropdown = $has_children || (bool) $mega_content;

		if ( $has_dropdown ) {
			$classes[] = 'has-children';
		}

		if ( $mega_content ) {
			$classes[] = 'has-mega-menu';
		}

		if ( array_intersect( array( 'current-menu-item', 'current_page_item' ), $core_classes ) ) {
			$classes[] = 'is-current';
		}

		if ( array_intersect( array( 'current-menu-parent', 'current-menu-ancestor', 'current_page_parent', 'current_page_ancestor' ), $core_classes ) ) {
			$classes[] = 'is-current-ancestor';
		}

		$class_names = implode( ' ', array_map( 'sanitize_html_class', $classes ) );
		$item_id     = 'webmz-icon-nav-item-' . absint( $item->ID );
		$output     .= $indent . '<li id="' . esc_attr( $item_id ) . '" class="' . esc_attr( $class_names ) . '">';

		$attributes = array(
			'title'  => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'target' => ! empty( $item->target ) ? $item->target : '',
			'rel'    => ! empty( $item->xfn ) ? $item->xfn : '',
			'href'   => ! empty( $item->url ) ? $item->url : '',
			'class'  => 'webmz-nav__link webmz-icon-nav__link webmz-nav__link--level-' . absint( $depth ),
		);

		if ( $has_dropdown ) {
			$attributes['aria-haspopup'] = 'true';
		}

		$attributes = apply_filters( 'nav_menu_link_attributes', $attributes, $item, $args, $depth );
		$atts       = '';

		foreach ( $attributes as $attribute => $value ) {
			if ( '' === (string) $value || false === $value ) {
				continue;
			}

			$value = 'href' === $attribute ? esc_url( $value ) : esc_attr( $value );
			$atts .= ' ' . $attribute . '="' . $value . '"';
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$icon_html = '';
		if ( function_exists( 'webmz_get_nav_menu_item_icon_svg' ) ) {
			$markup = webmz_get_nav_menu_item_icon_svg( $item->ID );
			if ( '' !== $markup ) {
				$icon_html = '<span class="webmz-nav__icon webmz-icon-nav__icon" aria-hidden="true">' . $markup . '</span>';
			}
		}

		$item_output  = isset( $args->before ) ? $args->before : '';
		$item_output .= '<a' . $atts . '>';
		$item_output .= $icon_html;
		$item_output .= '<span class="webmz-icon-nav__label">';
		$item_output .= '<span class="webmz-icon-nav__text">' . ( isset( $args->link_before ) ? $args->link_before : '' ) . esc_html( $title ) . ( isset( $args->link_after ) ? $args->link_after : '' ) . '</span>';

		if ( $has_dropdown ) {
			$item_output .= '<span class="webmz-icon-nav__arrow" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></span>';
		}

		$item_output .= '</span>';
		$item_output .= '</a>';

		if ( $has_dropdown && ! empty( $args->webmz_show_indicator ) ) {
			$item_output .= '<button type="button" class="webmz-nav__toggle webmz-icon-nav__toggle" aria-expanded="false" aria-label="' . esc_attr__( 'باز کردن زیرمنو', 'tadris' ) . '">';
			$item_output .= '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';
			$item_output .= '</button>';
		}

		if ( $mega_content && function_exists( 'webmz_render_mega_menu_content' ) ) {
			$mega_menu = webmz_render_mega_menu_content( $mega_content );

			if ( '' !== trim( $mega_menu ) ) {
				$item_output .= '<div class="webmz-nav__mega-menu webmz-icon-nav__mega-menu" aria-label="' . esc_attr__( 'مگامنو', 'tadris' ) . '">' . $mega_menu . '</div>';
			}
		}

		$item_output .= isset( $args->after ) ? $args->after : '';
		$output      .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

/**
 * Horizontal icon-first navigation menu with mega menu support.
 */
class Icon_Navigation_Menu_Widget extends Widget_Base {

	public function get_name() {
		return 'webmz-icon-navigation-menu';
	}

	public function get_title() {
		return esc_html__( 'منو آیکون‌محور', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-menu-bar';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'menu', 'navigation', 'icon', 'mega', 'منو', 'آیکون', 'مگامنو' );
	}

	public function get_style_depends() {
		return array( 'webmz-navigation-menu', 'webmz-icon-navigation-menu', 'webmz-mega-menu-content' );
	}

	public function get_script_depends() {
		return array( 'webmz-navigation-menu', 'webmz-mega-menu-content' );
	}

	/**
	 * Get saved WordPress navigation menus for the Elementor select.
	 *
	 * @return array<int|string,string>
	 */
	private function get_menu_options() {
		$options = array( '' => esc_html__( 'یک منو انتخاب کنید', 'tadris' ) );

		foreach ( wp_get_nav_menus() as $menu ) {
			$options[ absint( $menu->term_id ) ] = $menu->name;
		}

		return $options;
	}

	/** Register Elementor controls. */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'منو', 'tadris' ) )
		);

		$menus = $this->get_menu_options();
		$keys  = array_keys( $menus );
		$first = count( $keys ) > 1 ? (string) $keys[1] : '';

		$this->add_control(
			'menu_id',
			array(
				'label'       => esc_html__( 'انتخاب منو', 'tadris' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $menus,
				'default'     => $first,
				'label_block' => true,
			)
		);

		$this->add_control(
			'dropdown_trigger',
			array(
				'label'   => esc_html__( 'باز شدن زیرمنو در دسکتاپ', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => array(
					'hover' => esc_html__( 'هاور و فوکوس', 'tadris' ),
					'click' => esc_html__( 'کلیک روی فلش', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'show_indicator',
			array(
				'label'        => esc_html__( 'نمایش فلش زیرمنو', 'tadris' ),
				'description'  => esc_html__( 'در حالت باز شدن با کلیک، فلش برای دسترسی به زیرمنو همیشه نمایش داده می‌شود.', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'depth',
			array(
				'label'       => esc_html__( 'عمق زیرمنو', 'tadris' ),
				'description' => esc_html__( 'عدد ۰ یعنی نمایش تمام زیرمنوها.', 'tadris' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 5,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_main_menu_style',
			array(
				'label' => esc_html__( 'منوی اصلی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => esc_html__( 'چینش', 'tadris' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array( 'title' => esc_html__( 'ابتدا', 'tadris' ), 'icon' => 'eicon-h-align-right' ),
					'center'     => array( 'title' => esc_html__( 'وسط', 'tadris' ), 'icon' => 'eicon-h-align-center' ),
					'flex-end'   => array( 'title' => esc_html__( 'انتها', 'tadris' ), 'icon' => 'eicon-h-align-left' ),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .webmz-icon-nav__list--root' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => esc_html__( 'فاصله آیتم‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 120 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 32 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-icon-nav__list--root' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'link_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی لینک', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 8, 'right' => 16, 'bottom' => 8, 'left' => 16, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item > .webmz-icon-nav__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 16, 'max' => 72 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 32 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item > .webmz-icon-nav__link > .webmz-icon-nav__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_text_gap',
			array(
				'label'      => esc_html__( 'فاصله آیکون تا متن', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 10 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item > .webmz-icon-nav__link' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item > .webmz-icon-nav__link > .webmz-icon-nav__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون در هاور / فعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item:hover > .webmz-icon-nav__link > .webmz-icon-nav__icon, {{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item.is-current > .webmz-icon-nav__link > .webmz-icon-nav__icon, {{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item.is-current-ancestor > .webmz-icon-nav__link > .webmz-icon-nav__icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'       => esc_html__( 'رنگ متن', 'tadris' ),
				'description' => esc_html__( 'در صورت خالی بودن، رنگ پیش‌فرض پوسته استفاده می‌شود.', 'tadris' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'{{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item > .webmz-icon-nav__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه لینک', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item > .webmz-icon-nav__link' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_hover_color',
			array(
				'label'       => esc_html__( 'رنگ متن در هاور / فعال', 'tadris' ),
				'description' => esc_html__( 'در صورت خالی بودن، رنگ هاور اصلی پنل پوسته استفاده می‌شود.', 'tadris' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'{{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item:hover > .webmz-icon-nav__link, {{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item.is-current > .webmz-icon-nav__link, {{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item.is-current-ancestor > .webmz-icon-nav__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_hover_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه در هاور / فعال', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item:hover > .webmz-icon-nav__link, {{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item.is-current > .webmz-icon-nav__link, {{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item.is-current-ancestor > .webmz-icon-nav__link' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'link_radius',
			array(
				'label'      => esc_html__( 'گردی لینک', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 0 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item > .webmz-icon-nav__link' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'link_border',
				'selector' => '{{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item > .webmz-icon-nav__link',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'link_shadow',
				'selector' => '{{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item > .webmz-icon-nav__link',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'selector' => '{{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item > .webmz-icon-nav__link .webmz-icon-nav__text',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_dropdown_style',
			array(
				'label' => esc_html__( 'زیرمنو / Dropdown', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'dropdown_width',
			array(
				'label'      => esc_html__( 'عرض زیرمنو', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 120, 'max' => 500 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 230 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-icon-nav__submenu' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_offset',
			array(
				'label'      => esc_html__( 'فاصله زیرمنو از منو', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 8 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-icon-nav__list--root > .webmz-icon-nav__item > .webmz-icon-nav__submenu' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'dropdown_background',
			array(
				'label'     => esc_html__( 'رنگ پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-icon-nav__submenu' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'dropdown_border',
				'selector' => '{{WRAPPER}} .webmz-icon-nav__submenu',
			)
		);

		$this->add_responsive_control(
			'dropdown_radius',
			array(
				'label'      => esc_html__( 'گردی زیرمنو', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 12 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-icon-nav__submenu' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی Dropdown', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array( 'top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8, 'unit' => 'px', 'isLinked' => true ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-icon-nav__submenu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'dropdown_shadow',
				'selector' => '{{WRAPPER}} .webmz-icon-nav__submenu',
			)
		);

		$this->add_control(
			'dropdown_link_color',
			array(
				'label'     => esc_html__( 'رنگ لینک‌های Dropdown', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-icon-nav__submenu .webmz-icon-nav__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'dropdown_link_hover_color',
			array(
				'label'     => esc_html__( 'رنگ لینک در هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-icon-nav__submenu .webmz-icon-nav__item:hover > .webmz-icon-nav__link, {{WRAPPER}} .webmz-icon-nav__submenu .webmz-icon-nav__item.is-current > .webmz-icon-nav__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'dropdown_link_hover_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه لینک در هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-icon-nav__submenu .webmz-icon-nav__item:hover > .webmz-icon-nav__link, {{WRAPPER}} .webmz-icon-nav__submenu .webmz-icon-nav__item.is-current > .webmz-icon-nav__link' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_link_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی لینک Dropdown', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array( 'top' => 10, 'right' => 12, 'bottom' => 10, 'left' => 12, 'unit' => 'px', 'isLinked' => false ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-icon-nav__submenu .webmz-icon-nav__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'dropdown_typography',
				'selector' => '{{WRAPPER}} .webmz-icon-nav__submenu .webmz-icon-nav__link',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_arrow_style',
			array(
				'label' => esc_html__( 'فلش زیرمنو', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'رنگ فلش', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-icon-nav__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'      => esc_html__( 'اندازه فلش', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 24 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 14 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-icon-nav__arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_gap',
			array(
				'label'      => esc_html__( 'فاصله فلش از متن', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 20 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 4 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-icon-nav__label' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'indicator_color',
			array(
				'label'     => esc_html__( 'رنگ فلش کلیک', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-icon-nav__toggle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/** Render selected menu with icon-first markup. */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$menu_id  = isset( $settings['menu_id'] ) ? absint( $settings['menu_id'] ) : 0;

		if ( ! $menu_id ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p class="webmz-nav__notice">' . esc_html__( 'از تنظیمات ویجت، یک منو را انتخاب کنید.', 'tadris' ) . '</p>';
			}
			return;
		}

		$trigger        = in_array( $settings['dropdown_trigger'], array( 'hover', 'click' ), true ) ? $settings['dropdown_trigger'] : 'hover';
		$depth          = isset( $settings['depth'] ) ? min( 5, max( 0, absint( $settings['depth'] ) ) ) : 0;
		$show_indicator = ( 'yes' === ( $settings['show_indicator'] ?? 'yes' ) || 'click' === $trigger );

		$this->add_render_attribute(
			'wrapper',
			'class',
			array(
				'webmz-nav',
				'webmz-icon-nav',
				'webmz-nav--horizontal',
				'webmz-nav--trigger-' . $trigger,
			)
		);
		$this->add_render_attribute( 'wrapper', 'data-webmz-navigation', '' );
		$this->add_render_attribute( 'wrapper', 'data-webmz-icon-nav', '' );
		?>
		<nav <?php echo $this->get_render_attribute_string( 'wrapper' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-label="<?php esc_attr_e( 'منوی سایت', 'tadris' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'menu'                 => $menu_id,
					'container'            => false,
					'menu_class'           => 'webmz-nav__list webmz-nav__list--root webmz-icon-nav__list webmz-icon-nav__list--root',
					'menu_id'              => '',
					'fallback_cb'          => false,
					'depth'                => $depth,
					'item_spacing'         => 'discard',
					'webmz_show_indicator' => $show_indicator,
					'walker'               => new Icon_Navigation_Menu_Walker(),
				)
			);
			?>
		</nav>
		<?php
	}
}
