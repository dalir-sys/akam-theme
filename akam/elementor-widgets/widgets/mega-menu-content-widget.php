<?php
/**
 * Elementor widget for editable mega menu content.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Mega menu content widget with separately managed main and submenu items.
 */
class Mega_Menu_Content_Widget extends Widget_Base {

	public function get_name() {
		return 'webmz-mega-menu-content';
	}

	public function get_title() {
		return esc_html__( 'محتوای مگامنو', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-mega-menu';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'mega menu', 'menu', 'megamenu', 'مگامنو', 'منو' );
	}

	public function get_style_depends() {
		return array( 'webmz-mega-menu-content' );
	}

	public function get_script_depends() {
		return array( 'webmz-mega-menu-content' );
	}

	/**
	 * Parent choices used by submenu items.
	 *
	 * Elementor controls cannot reliably build select options from another
	 * repeater while the panel is open, so submenu items target the main item
	 * position instead of a typed key.
	 *
	 * @return array<string,string>
	 */
	private function get_parent_select_options() {
		$options = array();

		for ( $index = 1; $index <= 8; $index++ ) {
			$options[ 'item-' . $index ] = sprintf(
				/* translators: %d: Main menu item number. */
				esc_html__( 'منوی اصلی %d', 'tadris' ),
				$index
			);
		}

		return $options;
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'آیتم‌های مگامنو', 'tadris' ) )
		);

		$main_repeater = new Repeater();
		$main_repeater->add_control(
			'item_key',
			array(
				'type'        => Controls_Manager::HIDDEN,
				'default'     => 'item-1',
			)
		);
		$main_repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان منوی اصلی', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'آموزش وردپرس رایگان', 'tadris' ),
				'label_block' => true,
			)
		);
		$main_repeater->add_control(
			'icon',
			array(
				'label'            => esc_html__( 'آیکون منو', 'tadris' ),
				'type'             => Controls_Manager::ICONS,
				'skin'             => 'inline',
				'default'          => array(
					'value'   => '',
					'library' => 'fa-solid',
				),
			)
		);
		$main_repeater->add_control(
			'link',
			array(
				'label'       => esc_html__( 'لینک', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => home_url( '/' ),
				'default'     => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'main_items',
			array(
				'label'       => esc_html__( 'منوهای اصلی', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $main_repeater->get_controls(),
				'default'     => array(
					array( 'item_key' => 'item-1', 'title' => esc_html__( 'آموزش وردپرس رایگان', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'item_key' => 'item-2', 'title' => esc_html__( 'آموزش رایگان', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'item_key' => 'item-3', 'title' => esc_html__( 'مقالات', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'item_key' => 'item-4', 'title' => esc_html__( 'ویدیوها', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'item_key' => 'item-5', 'title' => esc_html__( 'پادکست‌ها', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'item_key' => 'item-6', 'title' => esc_html__( 'دوره‌های آموزشی', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'item_key' => 'item-7', 'title' => esc_html__( 'دانلودها', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'item_key' => 'item-8', 'title' => esc_html__( 'پرسش‌های متداول', 'tadris' ), 'link' => array( 'url' => '#' ) ),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->add_control(
			'arrow_icon',
			array(
				'label'            => esc_html__( 'آیکون فلش همه آیتم‌ها', 'tadris' ),
				'type'             => Controls_Manager::ICONS,
				'skin'             => 'inline',
				'exclude_inline_options' => array( 'icon' ),
				'default'          => array(
					'value'   => 'fas fa-angle-left',
					'library' => 'fa-solid',
				),
				'separator'        => 'before',
			)
		);

		$submenu_repeater = new Repeater();
		$submenu_repeater->add_control(
			'parent_key',
			array(
				'label'       => esc_html__( 'والد زیرمنو', 'tadris' ),
				'description' => esc_html__( 'انتخاب کنید این زیرمنو زیر کدام منوی اصلی نمایش داده شود.', 'tadris' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $this->get_parent_select_options(),
				'default'     => 'item-1',
				'label_block' => true,
			)
		);
		$submenu_repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان زیرمنو', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'آموزش وردپرس رایگان', 'tadris' ),
				'label_block' => true,
			)
		);
		$submenu_repeater->add_control(
			'link',
			array(
				'label'       => esc_html__( 'لینک', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => home_url( '/' ),
				'default'     => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'submenu_items',
			array(
				'label'       => esc_html__( 'زیرمنوها', 'tadris' ),
				'description' => esc_html__( 'برای هر زیرمنو، والد آن را از لیست انتخاب کنید.', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $submenu_repeater->get_controls(),
				'default'     => array(
					array( 'parent_key' => 'item-1', 'title' => esc_html__( 'آموزش وردپرس رایگان', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-1', 'title' => esc_html__( 'آموزش المنتور', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-1', 'title' => esc_html__( 'آموزش طراحی سایت', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-2', 'title' => esc_html__( 'آموزش وردپرس رایگان', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-2', 'title' => esc_html__( 'آموزش ووکامرس رایگان', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-2', 'title' => esc_html__( 'آموزش سئو رایگان', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-3', 'title' => esc_html__( 'مقالات', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-3', 'title' => esc_html__( 'مقالات وردپرس', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-3', 'title' => esc_html__( 'مقالات المنتور', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-4', 'title' => esc_html__( 'ویدیوها', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-4', 'title' => esc_html__( 'ویدیوهای آموزشی', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-4', 'title' => esc_html__( 'وبینارها', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-5', 'title' => esc_html__( 'پادکست‌های آموزشی', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-5', 'title' => esc_html__( 'مصاحبه‌ها', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-5', 'title' => esc_html__( 'نکات کوتاه', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-6', 'title' => esc_html__( 'دوره وردپرس', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-6', 'title' => esc_html__( 'دوره المنتور', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-6', 'title' => esc_html__( 'دوره فروشگاه‌سازی', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-7', 'title' => esc_html__( 'دانلود قالب', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-7', 'title' => esc_html__( 'دانلود افزونه', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-7', 'title' => esc_html__( 'فایل‌های آموزشی', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-8', 'title' => esc_html__( 'سوالات وردپرس', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-8', 'title' => esc_html__( 'سوالات خرید دوره', 'tadris' ), 'link' => array( 'url' => '#' ) ),
					array( 'parent_key' => 'item-8', 'title' => esc_html__( 'راهنمای پشتیبانی', 'tadris' ), 'link' => array( 'url' => '#' ) ),
				),
				'title_field' => '{{{ title }}} - {{{ parent_key }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_box_style',
			array(
				'label' => esc_html__( 'کادر مگامنو', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'background_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-mega-content' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'min_height',
			array(
				'label'      => esc_html__( 'حداقل ارتفاع', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 120, 'max' => 700 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 320 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-mega-content' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_responsive_control(
			'border_radius',
			array(
				'label'      => esc_html__( 'گردی کادر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array( 'unit' => 'px', 'size' => 18 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-mega-content' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .webmz-mega-content',
			)
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .webmz-mega-content',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_item_style',
			array(
				'label' => esc_html__( 'منوهای اصلی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'item_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4b5563',
				'selectors' => array(
					'{{WRAPPER}} .webmz-mega-content__main-link' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'item_hover_color',
			array(
				'label'     => esc_html__( 'رنگ هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .webmz-mega-content__item:focus-within .webmz-mega-content__main-link, {{WRAPPER}} .webmz-mega-content__item.is-active .webmz-mega-content__main-link' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'item_hover_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff4e6',
				'selectors' => array(
					'{{WRAPPER}} .webmz-mega-content__item:focus-within .webmz-mega-content__main-link, {{WRAPPER}} .webmz-mega-content__item.is-active .webmz-mega-content__main-link' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'item_hover_accent',
			array(
				'label'     => esc_html__( 'خط هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f59e0b',
				'selectors' => array(
					'{{WRAPPER}} .webmz-mega-content__main-link::before' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_typography',
				'selector' => '{{WRAPPER}} .webmz-mega-content__main-link',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_submenu_style',
			array(
				'label' => esc_html__( 'زیرمنوها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'submenu_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4b5563',
				'selectors' => array(
					'{{WRAPPER}} .webmz-mega-content__submenu-link' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'submenu_hover_color',
			array(
				'label'     => esc_html__( 'رنگ هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111827',
				'selectors' => array(
					'{{WRAPPER}} .webmz-mega-content__submenu-link:hover, {{WRAPPER}} .webmz-mega-content__submenu-link:focus' => 'color: {{VALUE}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'submenu_typography',
				'selector' => '{{WRAPPER}} .webmz-mega-content__submenu-link',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render a URL control as link attributes.
	 *
	 * @param array<string,mixed> $link Elementor URL control value.
	 * @return string
	 */
	private function get_link_attributes( $link ) {
		if ( empty( $link['url'] ) ) {
			return ' href="#"';
		}

		$attributes = ' href="' . esc_url( $link['url'] ) . '"';

		if ( ! empty( $link['is_external'] ) ) {
			$attributes .= ' target="_blank"';
		}

		if ( ! empty( $link['nofollow'] ) ) {
			$attributes .= ' rel="nofollow"';
		}

		return $attributes;
	}

	/**
	 * Normalize user-entered keys used to connect submenu items to parents.
	 *
	 * @param string $key Raw key.
	 * @return string
	 */
	private function normalize_item_key( $key ) {
		$key = sanitize_key( (string) $key );

		return '' !== $key ? $key : 'item';
	}

	/**
	 * Get submenu items that belong to a main item.
	 *
	 * @param array<string,mixed> $item     Main item settings.
	 * @param int                 $index    Main item index.
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<int,array>
	 */
	private function get_submenu_items_for_main_item( $item, $index, $settings ) {
		$item_key        = 'item-' . absint( $index + 1 );
		$legacy_item_key = ! empty( $item['item_key'] ) ? $this->normalize_item_key( $item['item_key'] ) : '';

		if ( ! empty( $settings['submenu_items'] ) && is_array( $settings['submenu_items'] ) ) {
			$submenu_items = array();

			foreach ( $settings['submenu_items'] as $submenu_item ) {
				$parent_key = ! empty( $submenu_item['parent_key'] ) ? $this->normalize_item_key( $submenu_item['parent_key'] ) : '';

				if ( $item_key === $parent_key || ( $legacy_item_key && $legacy_item_key === $parent_key ) ) {
					$submenu_items[] = $submenu_item;
				}
			}

			return $submenu_items;
		}

		return ! empty( $item['submenu_items'] ) && is_array( $item['submenu_items'] ) ? $item['submenu_items'] : array();
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = ! empty( $settings['main_items'] ) && is_array( $settings['main_items'] ) ? $settings['main_items'] : array();
		$arrow    = ! empty( $settings['arrow_icon'] ) && is_array( $settings['arrow_icon'] ) ? $settings['arrow_icon'] : array();

		if ( empty( $items ) ) {
			return;
		}
		?>
		<div class="webmz-mega-content" dir="rtl" data-webmz-mega-content>
			<ul class="webmz-mega-content__main-list">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php
					$title          = isset( $item['title'] ) ? $item['title'] : '';
					$item_key       = 'item-' . absint( $index + 1 );
					$submenu_items  = $this->get_submenu_items_for_main_item( $item, $index, $settings );
					$item_classes   = array( 'webmz-mega-content__item' );
					if ( 0 === $index ) {
						$item_classes[] = 'is-active';
					}
					?>
					<li class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>" data-webmz-mega-item="<?php echo esc_attr( $item_key ); ?>">
						<a class="webmz-mega-content__main-link"<?php echo $this->get_link_attributes( isset( $item['link'] ) ? $item['link'] : array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<span class="webmz-mega-content__main-text"><?php echo esc_html( $title ); ?></span>
							<?php if ( ! empty( $item['icon']['value'] ) ) : ?>
								<span class="webmz-mega-content__item-icon" aria-hidden="true">
									<?php Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) ); ?>
								</span>
							<?php endif; ?>
							<span class="webmz-mega-content__main-icon" aria-hidden="true">
								<?php Icons_Manager::render_icon( $arrow, array( 'aria-hidden' => 'true' ) ); ?>
							</span>
						</a>

						<?php if ( ! empty( $submenu_items ) ) : ?>
							<div class="webmz-mega-content__panel">
								<ul class="webmz-mega-content__submenu-list">
									<?php foreach ( $submenu_items as $submenu_item ) : ?>
										<?php $submenu_title = isset( $submenu_item['title'] ) ? $submenu_item['title'] : ''; ?>
										<li class="webmz-mega-content__submenu-item">
											<a class="webmz-mega-content__submenu-link"<?php echo $this->get_link_attributes( isset( $submenu_item['link'] ) ? $submenu_item['link'] : array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
												<?php echo esc_html( $submenu_title ); ?>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}
