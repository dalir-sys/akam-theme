<?php
/**
 * Service Box — icon card with glow, counter, dashed divider, and link.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Service box widget matching the Tadris design card.
 */
class Service_Box_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-service-box';
	}

	public function get_title() {
		return esc_html__( 'باکس خدمات', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-info-box';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_style_depends() {
		return array( 'webmz-service-box' );
	}

	public function get_script_depends() {
		return array( 'webmz-service-box' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => esc_html__( 'تصویر', 'tadris' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => esc_html__( 'سایز تصویر', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'medium',
				'options' => array(
					'thumbnail'    => esc_html__( 'بندانگشتی', 'tadris' ),
					'medium'       => esc_html__( 'متوسط', 'tadris' ),
					'medium_large' => esc_html__( 'متوسط بزرگ', 'tadris' ),
					'large'        => esc_html__( 'بزرگ', 'tadris' ),
					'full'         => esc_html__( 'کامل', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دوره های آموزشی', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control();

		$this->add_control(
			'counter_heading',
			array(
				'label'     => esc_html__( 'شمارنده', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'start_number',
			array(
				'label'   => esc_html__( 'عدد شروع', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
			)
		);

		$this->add_control(
			'end_number',
			array(
				'label'   => esc_html__( 'عدد نهایی', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 120,
			)
		);

		$this->add_control(
			'counter_prefix',
			array(
				'label'   => esc_html__( 'پیشوند شمارنده', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '+',
			)
		);

		$this->add_control(
			'counter_suffix',
			array(
				'label'   => esc_html__( 'پسوند شمارنده', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'counter_duration',
			array(
				'label'   => esc_html__( 'مدت انیمیشن (میلی‌ثانیه)', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1400,
				'min'     => 100,
				'max'     => 10000,
			)
		);

		$this->add_control(
			'description',
			array(
				'label'       => esc_html__( 'توضیحات', 'tadris' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'بیش از ۱۲۰ دوره آموزشی در زمینه های طراحی و توسعه وبسایت', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'link_heading',
			array(
				'label'     => esc_html__( 'لینک پایین', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'link_text',
			array(
				'label'   => esc_html__( 'متن لینک', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مشاهده دوره ها', 'tadris' ),
			)
		);

		$this->add_control(
			'link_url',
			array(
				'label'   => esc_html__( 'آدرس لینک', 'tadris' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->add_control(
			'link_icon',
			array(
				'label'   => esc_html__( 'آیکون لینک', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-bullhorn',
					'library' => 'fa-solid',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control( 'link_icon_color_mode', esc_html__( 'نوع رنگ‌دهی آیکون لینک', 'tadris' ) );

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->webmz_register_box_style_controls(
			'box_style',
			esc_html__( 'کارت', 'tadris' ),
			'.webmz-service-box',
			array(
				'default_background' => '#ffffff',
			)
		);

		$this->start_controls_section(
			'style_image',
			array(
				'label' => esc_html__( 'تصویر و درخشش', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'      => esc_html__( 'عرض تصویر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 40, 'max' => 280 ),
				),
				'default'    => array(
					'size' => 140,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-service-box__image' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'glow_color',
			array(
				'label'     => esc_html__( 'رنگ درخشش (secondary)', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-secondary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-service-box__glow' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'glow_size',
			array(
				'label'      => esc_html__( 'اندازه درخشش', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 40, 'max' => 240 ),
				),
				'default'    => array(
					'size' => 120,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-service-box__glow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'glow_opacity',
			array(
				'label'     => esc_html__( 'شفافیت درخشش', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ),
				),
				'default'   => array(
					'size' => 0.28,
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-service-box__glow' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'glow_blur',
			array(
				'label'     => esc_html__( 'محوشدگی درخشش', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
				),
				'default'   => array(
					'size' => 42,
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-service-box__glow' => 'filter: blur({{SIZE}}px);',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.webmz-service-box__title' );

		$this->start_controls_section(
			'counter_style',
			array(
				'label' => esc_html__( 'شمارنده', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'counter_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-service-box__counter' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'counter_typography',
				'selector' => '{{WRAPPER}} .webmz-service-box__counter',
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls( 'description_style', esc_html__( 'توضیحات', 'tadris' ), '.webmz-service-box__description' );

		$this->start_controls_section(
			'divider_style',
			array(
				'label' => esc_html__( 'خط جداکننده', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'divider_color',
			array(
				'label'     => esc_html__( 'رنگ خط', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e2e8f0',
				'selectors' => array(
					'{{WRAPPER}} .webmz-service-box__divider' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'divider_margin',
			array(
				'label'      => esc_html__( 'فاصله بیرونی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-service-box__divider' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls( 'link_text_style', esc_html__( 'متن لینک', 'tadris' ), '.webmz-service-box__link-text' );
		$this->webmz_register_icon_style_controls( 'link_icon_style', esc_html__( 'آیکون لینک', 'tadris' ), '.webmz-service-box__link-icon' );
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$title_tag  = $this->webmz_get_title_tag( $settings );
		$image      = ! empty( $settings['image'] ) && is_array( $settings['image'] ) ? $settings['image'] : array();
		$image_url  = ! empty( $image['url'] ) ? $image['url'] : Utils::get_placeholder_image_src();
		$image_size = ! empty( $settings['image_size'] ) ? sanitize_key( $settings['image_size'] ) : 'medium';
		$allowed_sizes = array( 'thumbnail', 'medium', 'medium_large', 'large', 'full' );

		if ( ! in_array( $image_size, $allowed_sizes, true ) ) {
			$image_size = 'medium';
		}

		$start_number = isset( $settings['start_number'] ) ? (int) $settings['start_number'] : 0;
		$end_number   = isset( $settings['end_number'] ) ? (int) $settings['end_number'] : 0;
		$prefix       = isset( $settings['counter_prefix'] ) ? (string) $settings['counter_prefix'] : '';
		$suffix       = isset( $settings['counter_suffix'] ) ? (string) $settings['counter_suffix'] : '';
		$duration     = ! empty( $settings['counter_duration'] ) ? absint( $settings['counter_duration'] ) : 1400;
		$icon_class   = $this->webmz_get_icon_color_mode_class( $settings, 'link_icon_color_mode' );

		if ( ! empty( $settings['link_url'] ) && is_array( $settings['link_url'] ) ) {
			$this->add_link_attributes( 'link_url', $settings['link_url'] );
		}
		?>
		<div class="webmz-service-box">
			<div class="webmz-service-box__visual">
				<span class="webmz-service-box__glow" aria-hidden="true"></span>
				<?php if ( ! empty( $image['id'] ) ) : ?>
					<?php echo wp_get_attachment_image( absint( $image['id'] ), $image_size, false, array( 'class' => 'webmz-service-box__image', 'alt' => '' ) ); ?>
				<?php else : ?>
					<img class="webmz-service-box__image" src="<?php echo esc_url( $image_url ); ?>" alt="">
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<<?php echo esc_html( $title_tag ); ?> class="webmz-service-box__title"><?php echo esc_html( $settings['title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
			<?php endif; ?>

			<p
				class="webmz-service-box__counter"
				data-count-start="<?php echo esc_attr( $start_number ); ?>"
				data-count-end="<?php echo esc_attr( $end_number ); ?>"
				data-count-duration="<?php echo esc_attr( $duration ); ?>"
				data-count-prefix="<?php echo esc_attr( $prefix ); ?>"
				data-count-suffix="<?php echo esc_attr( $suffix ); ?>"
			><?php echo esc_html( $prefix . $end_number . $suffix ); ?></p>

			<?php if ( ! empty( $settings['description'] ) ) : ?>
				<p class="webmz-service-box__description"><?php echo esc_html( $settings['description'] ); ?></p>
			<?php endif; ?>

			<hr class="webmz-service-box__divider" aria-hidden="true">

			<?php if ( ! empty( $settings['link_text'] ) ) : ?>
				<a class="webmz-service-box__link" <?php echo $this->get_render_attribute_string( 'link_url' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span class="webmz-service-box__link-text"><?php echo esc_html( $settings['link_text'] ); ?></span>
					<?php if ( ! empty( $settings['link_icon']['value'] ) ) : ?>
						<span class="webmz-service-box__link-icon <?php echo esc_attr( $icon_class ); ?>">
							<?php Icons_Manager::render_icon( $settings['link_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</span>
					<?php endif; ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}
