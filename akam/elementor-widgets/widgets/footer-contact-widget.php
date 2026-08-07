<?php
/**
 * Footer contact methods Elementor widget.
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
 * Vertical footer contact list with heading and repeater items.
 */
class Footer_Contact_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-footer-contact';
	}

	public function get_title() {
		return esc_html__( 'راه های تماس فوتر', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'footer', 'contact', 'phone', 'email', 'فوتر', 'تماس', 'راه ارتباطی' );
	}

	public function get_style_depends() {
		return array( 'webmz-footer-contact' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_heading',
			array(
				'label' => esc_html__( 'عنوان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
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
				'default'     => esc_html__( 'پاسخگوی سوالات شما هستند:', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control( 'heading_tag', esc_html__( 'تگ عنوان', 'tadris' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_items',
			array(
				'label' => esc_html__( 'راه‌های تماس', 'tadris' ),
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
					'value'   => 'fas fa-phone',
					'library' => 'fa-solid',
				),
			)
		);

		$repeater->add_control(
			'text',
			array(
				'label'       => esc_html__( 'متن', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( '۰۹۱۵ ۰۰۰ ۰۰۰۰', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'enable_link',
			array(
				'label'        => esc_html__( 'فعال‌سازی لینک', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'     => esc_html__( 'لینک', 'tadris' ),
				'type'      => Controls_Manager::URL,
				'default'   => array( 'url' => 'tel:09150000000' ),
				'condition' => array( 'enable_link' => 'yes' ),
			)
		);

		$repeater->add_control(
			'text_size',
			array(
				'label'   => esc_html__( 'اندازه متن', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => array(
					'normal' => esc_html__( 'معمولی', 'tadris' ),
					'large'  => esc_html__( 'بزرگ', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'آیتم‌ها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => array(
					array(
						'icon'      => array( 'value' => 'fas fa-phone', 'library' => 'fa-solid' ),
						'text'      => '۰۹۱۵ ۰۰۰ ۰۰۰۰',
						'link'      => array( 'url' => 'tel:09150000000' ),
						'text_size' => 'large',
					),
					array(
						'icon' => array( 'value' => 'far fa-envelope', 'library' => 'fa-regular' ),
						'text' => 'mail@example.com',
						'link' => array( 'url' => 'mailto:mail@example.com' ),
					),
					array(
						'icon'       => array( 'value' => 'fas fa-map-marked-alt', 'library' => 'fa-solid' ),
						'text'       => esc_html__( 'مشهد، کیان سنتر یک، طبقه سه، متر مربع', 'tadris' ),
						'enable_link' => '',
						'link'       => array( 'url' => '' ),
					),
					array(
						'icon' => array( 'value' => 'fab fa-telegram-plane', 'library' => 'fa-brands' ),
						'text' => esc_html__( 'پشتیبانی تلگرام: ۰۹۱۵۰۰۰۰۰۰۰', 'tadris' ),
						'link' => array( 'url' => 'https://t.me/' ),
					),
				),
			)
		);

		$this->webmz_register_icon_color_mode_control();

		$this->end_controls_section();

		$this->start_controls_section(
			'layout_style',
			array(
				'label' => esc_html__( 'چیدمان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'items_gap',
			array(
				'label'      => esc_html__( 'فاصله بین آیتم‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 14,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-contact__list' => 'gap: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .webmz-footer-contact__item' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'heading_spacing',
			array(
				'label'      => esc_html__( 'فاصله عنوان تا لیست', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 18,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-contact__heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls(
			'heading_style',
			esc_html__( 'عنوان', 'tadris' ),
			'.webmz-footer-contact__heading'
		);

		$this->start_controls_section(
			'heading_brand_style',
			array(
				'label' => esc_html__( 'نام برند در عنوان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_brand_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-contact__text-brand' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_brand_typography',
				'selector' => '{{WRAPPER}} .webmz-footer-contact__text-brand',
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls(
			'item_text_style',
			esc_html__( 'متن آیتم‌ها', 'tadris' ),
			'.webmz-footer-contact__text'
		);

		$this->start_controls_section(
			'item_large_text_style',
			array(
				'label' => esc_html__( 'متن بزرگ آیتم‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'item_large_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-navy)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-contact__item--large .webmz-footer-contact__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_large_typography',
				'selector' => '{{WRAPPER}} .webmz-footer-contact__item--large .webmz-footer-contact__text',
			)
		);

		$this->end_controls_section();

		$this->webmz_register_icon_style_controls(
			'item_icon_style',
			esc_html__( 'آیکون آیتم‌ها', 'tadris' ),
			'.webmz-footer-contact__icon'
		);
	}

	/**
	 * Render a single repeater item.
	 *
	 * @param array<string,mixed> $item    Repeater item settings.
	 * @param int                 $index   Item index.
	 * @return void
	 */
	protected function render_item( $item, $index ) {
		$text = isset( $item['text'] ) ? trim( (string) $item['text'] ) : '';

		if ( '' === $text ) {
			return;
		}

		$size_class = ( ! empty( $item['text_size'] ) && 'large' === $item['text_size'] ) ? ' webmz-footer-contact__item--large' : '';
		$has_link   = 'yes' === ( $item['enable_link'] ?? 'yes' ) && ! empty( $item['link']['url'] );
		$tag        = $has_link ? 'a' : 'div';
		$class      = 'webmz-footer-contact__item' . $size_class;

		if ( $has_link ) {
			$key = 'footer_contact_item_' . absint( $index );
			$this->add_render_attribute( $key, 'class', $class );
			$this->add_link_attributes( $key, $item['link'] );
			$attrs = $this->get_render_attribute_string( $key );
		} else {
			$this->add_render_attribute( 'footer_contact_item_static_' . absint( $index ), 'class', $class );
			$attrs = $this->get_render_attribute_string( 'footer_contact_item_static_' . absint( $index ) );
		}

		$icon_class = $this->webmz_get_icon_color_mode_class( $this->get_settings_for_display() );
		?>
		<<?php echo tag_escape( $tag ); ?> <?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<span class="webmz-footer-contact__text"><?php echo esc_html( $text ); ?></span>
			<span class="webmz-footer-contact__icon <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true">
				<?php
				if ( ! empty( $item['icon']['value'] ) ) {
					Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) );
				}
				?>
			</span>
		</<?php echo tag_escape( $tag ); ?>>
		<?php
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$heading_tag = $this->webmz_get_title_tag( $settings, 'heading_tag' );
		$items      = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();
		$has_heading = '' !== trim( (string) ( $settings['text_before'] ?? '' ) )
			|| '' !== trim( (string) ( $settings['text_brand'] ?? '' ) )
			|| '' !== trim( (string) ( $settings['text_after'] ?? '' ) );
		?>
		<div class="webmz-footer-contact">
			<?php if ( $has_heading ) : ?>
				<<?php echo esc_html( $heading_tag ); ?> class="webmz-footer-contact__heading">
					<?php if ( '' !== trim( (string) ( $settings['text_before'] ?? '' ) ) ) : ?>
						<span class="webmz-footer-contact__text-before"><?php echo esc_html( $settings['text_before'] ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== trim( (string) ( $settings['text_brand'] ?? '' ) ) ) : ?>
						<span class="webmz-footer-contact__text-brand"><?php echo esc_html( $settings['text_brand'] ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== trim( (string) ( $settings['text_after'] ?? '' ) ) ) : ?>
						<span class="webmz-footer-contact__text-after"><?php echo esc_html( $settings['text_after'] ); ?></span>
					<?php endif; ?>
				</<?php echo esc_html( $heading_tag ); ?>>
			<?php endif; ?>

			<?php if ( ! empty( $items ) ) : ?>
				<div class="webmz-footer-contact__list">
					<?php foreach ( $items as $index => $item ) : ?>
						<?php $this->render_item( $item, $index ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
