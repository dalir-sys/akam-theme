<?php
/**
 * Course category cards widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Course category cards with unified gradient color.
 */
class Course_Category_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-course-category';
	}

	public function get_title() {
		return esc_html__( 'دسته‌بندی دوره‌ها', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'course', 'category', 'language', 'دوره', 'دسته‌بندی', 'زبان' );
	}

	public function get_style_depends() {
		return array( 'webmz-course-category' );
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
				'label' => esc_html__( 'کارت‌ها', 'tadris' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'background_image',
			array(
				'label'   => esc_html__( 'تصویر پس‌زمینه', 'tadris' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'انگلیسی', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'زیرعنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'از مبتدی تا پیشرفته', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'level',
			array(
				'label'   => esc_html__( 'سطح', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'A1 - C1',
			)
		);

		$repeater->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'متن دکمه', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مشاهده دوره', 'tadris' ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'   => esc_html__( 'لینک', 'tadris' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'لیست دسته‌ها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'title'       => esc_html__( 'انگلیسی', 'tadris' ),
						'subtitle'    => esc_html__( 'از مبتدی تا پیشرفته', 'tadris' ),
						'level'       => 'A1 - C1',
						'button_text' => esc_html__( 'مشاهده دوره', 'tadris' ),
						'link'        => array( 'url' => '#' ),
					),
					array(
						'title'       => esc_html__( 'آلمانی', 'tadris' ),
						'subtitle'    => esc_html__( 'از مبتدی تا پیشرفته', 'tadris' ),
						'level'       => 'A1 - C1',
						'button_text' => esc_html__( 'مشاهده دوره', 'tadris' ),
						'link'        => array( 'url' => '#' ),
					),
				),
			)
		);

		$this->webmz_register_title_tag_control();

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
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-cc' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'فاصله بین کارت‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 20 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cc' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_min_height',
			array(
				'label'      => esc_html__( 'حداقل ارتفاع کارت', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 180, 'max' => 500 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 280 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cc__card' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 18 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cc__card' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => '28',
					'right'  => '24',
					'bottom' => '28',
					'left'   => '24',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-cc__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'style_card',
			array(
				'label' => esc_html__( 'گرادینت کارت', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_gradient_color',
			array(
				'label'     => esc_html__( 'رنگ گرادینت', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#c41e3a',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cc' => '--webmz-cc-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_gradient_hover_color',
			array(
				'label'     => esc_html__( 'رنگ گرادینت هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#a3162f',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cc' => '--webmz-cc-hover-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls(
			'style_title',
			esc_html__( 'عنوان', 'tadris' ),
			'.webmz-cc__title',
			'#ffffff'
		);

		$this->webmz_register_text_style_controls(
			'style_subtitle',
			esc_html__( 'زیرعنوان', 'tadris' ),
			'.webmz-cc__subtitle',
			'rgba(255,255,255,0.92)'
		);

		$this->start_controls_section(
			'style_level',
			array(
				'label' => esc_html__( 'برچسب سطح', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'level_bg_opacity',
			array(
				'label'     => esc_html__( 'تیرگی پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ),
				),
				'default'   => array( 'size' => 0.28 ),
				'selectors' => array(
					'{{WRAPPER}} .webmz-cc__level' => '--webmz-cc-level-overlay: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'level_typography',
				'selector' => '{{WRAPPER}} .webmz-cc__level',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_button',
			array(
				'label' => esc_html__( 'دکمه', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-cc__btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .webmz-cc__btn',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get background image URL for a card.
	 *
	 * @param array<string,mixed> $item Repeater item.
	 * @return string
	 */
	private function get_card_image_url( $item ) {
		$image = ! empty( $item['background_image'] ) && is_array( $item['background_image'] ) ? $item['background_image'] : array();

		return ! empty( $image['url'] ) ? (string) $image['url'] : '';
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$items     = ! empty( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();
		$title_tag = $this->webmz_get_title_tag( $settings );

		if ( empty( $items ) ) {
			return;
		}
		?>
		<div class="webmz-cc">
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$link_key = 'cc_link_' . $index;
				if ( ! empty( $item['link'] ) && is_array( $item['link'] ) ) {
					$this->add_link_attributes( $link_key, $item['link'] );
				}

				$image_url  = $this->get_card_image_url( $item );
				$btn_text   = ! empty( $item['button_text'] ) ? $item['button_text'] : '';
				$card_class = 'webmz-cc__card' . ( $image_url ? ' webmz-cc__card--has-image' : '' );
				?>
				<article class="<?php echo esc_attr( $card_class ); ?>">
					<?php if ( $image_url ) : ?>
						<div class="webmz-cc__bg" style="background-image: url(<?php echo esc_url( $image_url ); ?>);" aria-hidden="true"></div>
						<div class="webmz-cc__overlay" aria-hidden="true"></div>
					<?php endif; ?>
					<div class="webmz-cc__body">
						<?php if ( ! empty( $item['title'] ) ) : ?>
							<<?php echo esc_html( $title_tag ); ?> class="webmz-cc__title"><?php echo esc_html( $item['title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
						<?php endif; ?>

						<?php if ( ! empty( $item['subtitle'] ) ) : ?>
							<p class="webmz-cc__subtitle"><?php echo esc_html( $item['subtitle'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $item['level'] ) ) : ?>
							<span class="webmz-cc__level"><?php echo esc_html( $item['level'] ); ?></span>
						<?php endif; ?>

						<?php if ( $btn_text ) : ?>
							<a class="webmz-cc__btn" <?php echo $this->get_render_attribute_string( $link_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<?php echo esc_html( $btn_text ); ?>
							</a>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
