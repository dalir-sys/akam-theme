<?php
/**
 * Zhaket icon list — horizontal trust bar with icon + label items.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Repeater-based horizontal icon list (Zhaket style).
 */
class Zhaket_Icon_List_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-zhaket-icon-list';
	}

	public function get_title() {
		return esc_html__( 'لیست آیکون ژاکت', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'zhaket', 'icon', 'list', 'trust', 'features', 'ژاکت', 'آیکون', 'لیست', 'ویژگی' );
	}

	public function get_style_depends() {
		return array( 'webmz-zhaket-icon-list' );
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
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				),
			)
		);

		$repeater->add_control(
			'text',
			array(
				'label'       => esc_html__( 'متن', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'ضمانت بازگشت وجه', 'tadris' ),
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
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array(
						'icon' => array( 'value' => 'fas fa-award', 'library' => 'fa-solid' ),
						'text' => esc_html__( 'ضمانت بازگشت وجه', 'tadris' ),
					),
					array(
						'icon' => array( 'value' => 'fas fa-lock-open', 'library' => 'fa-solid' ),
						'text' => esc_html__( 'پشتیبانی حرفه‌ای', 'tadris' ),
					),
					array(
						'icon' => array( 'value' => 'fas fa-arrows-rotate', 'library' => 'fa-solid' ),
						'text' => esc_html__( 'به‌روزرسانی خودکار', 'tadris' ),
					),
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون', 'tadris' ) );

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
			'items_distribution',
			array(
				'label'   => esc_html__( 'توزیع آیتم‌ها', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'space-between',
				'options' => array(
					'flex-start'    => esc_html__( 'شروع', 'tadris' ),
					'center'        => esc_html__( 'وسط', 'tadris' ),
					'flex-end'      => esc_html__( 'پایان', 'tadris' ),
					'space-between' => esc_html__( 'فاصله یکنواخت', 'tadris' ),
					'space-around'  => esc_html__( 'فاصله اطراف', 'tadris' ),
					'space-evenly'  => esc_html__( 'فاصله مساوی', 'tadris' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-zil__list' => 'justify-content: {{VALUE}};',
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
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zil__list' => 'gap: {{SIZE}}{{UNIT}};',
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
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 8,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zil__item-inner' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_alignment',
			array(
				'label'   => esc_html__( 'تراز آیتم', 'tadris' ),
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
					'{{WRAPPER}} .webmz-zil__item-inner' => 'justify-content: {{VALUE}};',
				),
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
			'.webmz-zil',
			array(
				'default_background' => '#ffffff',
				'flat'               => true,
			)
		);

		$this->webmz_register_icon_style_controls(
			'item_icon_style',
			esc_html__( 'آیکون', 'tadris' ),
			'.webmz-zil__icon'
		);

		$this->webmz_register_text_style_controls(
			'text_style',
			esc_html__( 'متن', 'tadris' ),
			'.webmz-zil__text'
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
				'selectors'  => array(
					'{{WRAPPER}} .webmz-zil__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render a single repeater item.
	 *
	 * @param array<string,mixed> $item       Repeater item settings.
	 * @param int                 $index      Item index.
	 * @param string              $icon_class Icon color mode class.
	 * @return void
	 */
	protected function render_item( $item, $index, $icon_class ) {
		$text = isset( $item['text'] ) ? trim( (string) $item['text'] ) : '';

		if ( '' === $text && empty( $item['icon']['value'] ) ) {
			return;
		}

		$has_link = 'yes' === ( $item['enable_link'] ?? '' ) && ! empty( $item['link']['url'] );
		$tag      = $has_link ? 'a' : 'div';
		$key      = 'zil_item_' . absint( $index );

		$this->add_render_attribute( $key, 'class', 'webmz-zil__item' );

		if ( $has_link ) {
			$this->add_link_attributes( $key, $item['link'] );
		}

		?>
		<<?php echo tag_escape( $tag ); ?> <?php echo $this->get_render_attribute_string( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="webmz-zil__item-inner">
				<?php if ( ! empty( $item['icon']['value'] ) ) : ?>
					<span class="webmz-zil__icon <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true">
						<?php Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php endif; ?>
				<?php if ( '' !== $text ) : ?>
					<span class="webmz-zil__text"><?php echo esc_html( $text ); ?></span>
				<?php endif; ?>
			</div>
		</<?php echo tag_escape( $tag ); ?>>
		<?php
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$items      = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();
		$icon_class = $this->webmz_get_icon_color_mode_class( $settings );
		$stack      = ! empty( $settings['mobile_stack'] ) && 'yes' === $settings['mobile_stack'];
		$classes    = 'webmz-zil' . ( $stack ? ' webmz-zil--stack-mobile' : '' );

		if ( empty( $items ) ) {
			return;
		}
		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<div class="webmz-zil__list">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php $this->render_item( $item, $index, $icon_class ); ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
