<?php
/**
 * Footer Links — vertical link column with title and accent line.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Simple footer links widget with editable title and repeater links.
 */
class Footer_Links_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-footer-links';
	}

	public function get_title() {
		return esc_html__( 'لینک‌های فوتر', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-editor-list-ul';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'footer', 'links', 'menu', 'فوتر', 'لینک', 'منو' );
	}

	public function get_style_depends() {
		return array( 'webmz-footer-links' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دوره‌ها', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ عنوان', 'tadris' ) );

		$this->add_control(
			'show_accent',
			array(
				'label'   => esc_html__( 'نمایش خط تزئینی', 'tadris' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'link_title',
			array(
				'label'       => esc_html__( 'متن لینک', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دوره‌های انگلیسی', 'tadris' ),
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
					array(
						'link_title' => esc_html__( 'دوره‌های انگلیسی', 'tadris' ),
						'link_url'   => array( 'url' => '#' ),
					),
					array(
						'link_title' => esc_html__( 'دوره‌های آلمانی', 'tadris' ),
						'link_url'   => array( 'url' => '#' ),
					),
					array(
						'link_title' => esc_html__( 'دوره‌های فرانسوی', 'tadris' ),
						'link_url'   => array( 'url' => '#' ),
					),
					array(
						'link_title' => esc_html__( 'دوره‌های اسپانیایی', 'tadris' ),
						'link_url'   => array( 'url' => '#' ),
					),
					array(
						'link_title' => esc_html__( 'دوره‌های ترکی', 'tadris' ),
						'link_url'   => array( 'url' => '#' ),
					),
					array(
						'link_title' => esc_html__( 'همه دوره‌ها', 'tadris' ),
						'link_url'   => array( 'url' => '#' ),
					),
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
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-links' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_links_gap',
			array(
				'label'      => esc_html__( 'فاصله عنوان و لینک‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
				),
				'default'    => array(
					'size' => 20,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-links__head' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'items_gap',
			array(
				'label'      => esc_html__( 'فاصله لینک‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-links__list' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style_section',
			array(
				'label' => esc_html__( 'عنوان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-dark, #1e293b)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-links__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'تایپوگرافی عنوان', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-footer-links__title',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'accent_style_section',
			array(
				'label'     => esc_html__( 'خط تزئینی', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_accent' => 'yes' ),
			)
		);

		$this->add_control(
			'accent_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-red, #f21e3f)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-links__accent' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'accent_width',
			array(
				'label'      => esc_html__( 'عرض', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 20, 'max' => 200 ),
				),
				'default'    => array(
					'size' => 48,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-links__accent' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'accent_height',
			array(
				'label'      => esc_html__( 'ارتفاع', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 2, 'max' => 12 ),
				),
				'default'    => array(
					'size' => 4,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-links__accent' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'accent_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 20 ),
				),
				'default'    => array(
					'size' => 2,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-links__accent' => 'border-radius: {{SIZE}}{{UNIT}};',
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
				'default'   => 'var(--webmz-color-text-gray, #64748b)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-links__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_hover_color',
			array(
				'label'     => esc_html__( 'رنگ لینک در هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary, #0878f9)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-links__link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'label'    => esc_html__( 'تایپوگرافی لینک‌ها', 'tadris' ),
				'selector' => '{{WRAPPER}} .webmz-footer-links__link',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$title     = ! empty( $settings['title'] ) ? $settings['title'] : '';
		$title_tag = $this->webmz_get_title_tag( $settings, 'title_tag' );
		?>
		<nav class="webmz-footer-links" aria-label="<?php echo esc_attr( $title ? $title : __( 'لینک‌های فوتر', 'tadris' ) ); ?>">
			<?php if ( $title ) : ?>
				<div class="webmz-footer-links__head">
					<<?php echo esc_html( $title_tag ); ?> class="webmz-footer-links__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $title_tag ); ?>>
					<?php if ( 'yes' === $settings['show_accent'] ) : ?>
						<span class="webmz-footer-links__accent" aria-hidden="true"></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $settings['links'] ) && is_array( $settings['links'] ) ) : ?>
				<ul class="webmz-footer-links__list">
					<?php foreach ( $settings['links'] as $index => $item ) : ?>
						<?php
						$link_title = ! empty( $item['link_title'] ) ? $item['link_title'] : '';
						if ( '' === $link_title ) {
							continue;
						}
						$key = 'footer_links_item_' . absint( $index );
						$this->add_link_attributes( $key, ! empty( $item['link_url'] ) && is_array( $item['link_url'] ) ? $item['link_url'] : array( 'url' => '#' ) );
						?>
						<li class="webmz-footer-links__item">
							<a class="webmz-footer-links__link" <?php echo $this->get_render_attribute_string( $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $link_title ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</nav>
		<?php
	}
}
