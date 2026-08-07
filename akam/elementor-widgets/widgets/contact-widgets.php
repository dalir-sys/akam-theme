<?php
/**
 * Contact/about Elementor widgets for WebMZ.
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
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Shared helpers for contact page widgets.
 */
trait WebMZ_Contact_Widgets_Trait {
	public function get_style_depends() {
		return array( 'webmz-contact-widgets' );
	}

	public function get_script_depends() {
		return array( 'webmz-contact-widgets' );
	}

	protected function render_theme_icon( $icon, $fallback = 'fas fa-info-circle' ) {
		if ( empty( $icon['value'] ) ) {
			$icon = array(
				'value'   => $fallback,
				'library' => 'fa-solid',
			);
		}

		Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
	}

	protected function render_image( $image, $size = 'large', $class = '' ) {
		$image = is_array( $image ) ? $image : array();

		if ( ! empty( $image['id'] ) ) {
			echo wp_get_attachment_image( absint( $image['id'] ), $size, false, array( 'class' => $class ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		$url = ! empty( $image['url'] ) ? $image['url'] : Utils::get_placeholder_image_src();
		?>
		<img class="<?php echo esc_attr( $class ); ?>" src="<?php echo esc_url( $url ); ?>" alt="">
		<?php
	}

	protected function register_box_style_controls( $section_id, $label, $selector, $defaults = array() ) {
		$this->start_controls_section(
			$section_id,
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			$section_id . '_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => isset( $defaults['bg'] ) ? $defaults['bg'] : '',
				'selectors' => array(
					'{{WRAPPER}} ' . $selector => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => $section_id . '_border',
				'selector' => '{{WRAPPER}} ' . $selector,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => $section_id . '_shadow',
				'selector' => '{{WRAPPER}} ' . $selector,
			)
		);

		$this->add_responsive_control(
			$section_id . '_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			$section_id . '_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			$section_id . '_margin',
			array(
				'label'      => esc_html__( 'فاصله خارجی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_text_style_controls( $section_id, $label, $selector, $default_color = '' ) {
		$this->start_controls_section(
			$section_id,
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			$section_id . '_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $default_color,
				'selectors' => array(
					'{{WRAPPER}} ' . $selector => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $section_id . '_typography',
				'selector' => '{{WRAPPER}} ' . $selector,
			)
		);

		$this->add_responsive_control(
			$section_id . '_align',
			array(
				'label'     => esc_html__( 'چینش', 'tadris' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'right'  => array( 'title' => esc_html__( 'راست', 'tadris' ), 'icon' => 'eicon-text-align-right' ),
					'center' => array( 'title' => esc_html__( 'وسط', 'tadris' ), 'icon' => 'eicon-text-align-center' ),
					'left'   => array( 'title' => esc_html__( 'چپ', 'tadris' ), 'icon' => 'eicon-text-align-left' ),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $selector => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			$section_id . '_spacing',
			array(
				'label'     => esc_html__( 'فاصله پایین', 'tadris' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'selectors' => array(
					'{{WRAPPER}} ' . $selector => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_button_style_controls( $section_id, $label, $selector ) {
		$this->start_controls_section(
			$section_id,
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control( $section_id . '_color', array( 'label' => esc_html__( 'رنگ متن', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} ' . $selector => 'color: {{VALUE}};' ) ) );
		$this->add_control( $section_id . '_bg', array( 'label' => esc_html__( 'پس‌زمینه', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} ' . $selector => 'background: {{VALUE}};' ) ) );
		$this->add_control( $section_id . '_hover_color', array( 'label' => esc_html__( 'رنگ متن هاور', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} ' . $selector . ':hover' => 'color: {{VALUE}};' ) ) );
		$this->add_control( $section_id . '_hover_bg', array( 'label' => esc_html__( 'پس‌زمینه هاور', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} ' . $selector . ':hover' => 'background: {{VALUE}};' ) ) );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $section_id . '_typography',
				'selector' => '{{WRAPPER}} ' . $selector,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => $section_id . '_border',
				'selector' => '{{WRAPPER}} ' . $selector,
			)
		);

		$this->add_responsive_control(
			$section_id . '_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			$section_id . '_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}
}

class WebMZ_Jobs_Widget extends Widget_Base {
	use WebMZ_Contact_Widgets_Trait;

	public function get_name() { return 'webmz-jobs-callout'; }
	public function get_title() { return esc_html__( 'فرصت‌های شغلی', 'tadris' ); }
	public function get_icon() { return 'eicon-call-to-action'; }
	public function get_categories() { return array( 'webmz-widgets' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'title', array( 'label' => esc_html__( 'عنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'جای شما در تیم ما خالی‌ست!', 'tadris' ), 'label_block' => true ) );
		$this->add_control( 'highlight', array( 'label' => esc_html__( 'بخش رنگی عنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'شما', 'tadris' ) ) );
		$this->add_control( 'description', array( 'label' => esc_html__( 'توضیحات', 'tadris' ), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__( 'ما همیشه به دنبال نیروهای توانمند و خلاق برای گسترش خانواده آکام هستیم.', 'tadris' ) ) );
		$this->add_control( 'button_text', array( 'label' => esc_html__( 'متن دکمه', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'مشاهده فرصت‌های شغلی', 'tadris' ) ) );
		$this->add_control( 'button_link', array( 'label' => esc_html__( 'لینک دکمه', 'tadris' ), 'type' => Controls_Manager::URL, 'default' => array( 'url' => '#' ) ) );
		$this->add_control( 'icon', array( 'label' => esc_html__( 'آیکون', 'tadris' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-user-headset', 'library' => 'fa-solid' ) ) );
		$this->end_controls_section();

		$this->register_box_style_controls( 'jobs_box_style', esc_html__( 'باکس اصلی', 'tadris' ), '.webmz-jobs-callout', array( 'bg' => '#ffffff' ) );
		$this->register_text_style_controls( 'jobs_title_style', esc_html__( 'عنوان', 'tadris' ), '.webmz-jobs-callout__title', 'var(--webmz-color-text-dark)' );
		$this->register_text_style_controls( 'jobs_highlight_style', esc_html__( 'بخش رنگی عنوان', 'tadris' ), '.webmz-jobs-callout__title span', 'var(--webmz-color-primary)' );
		$this->register_text_style_controls( 'jobs_desc_style', esc_html__( 'توضیحات', 'tadris' ), '.webmz-jobs-callout__desc', 'var(--webmz-color-text-gray)' );
		$this->start_controls_section( 'jobs_icon_style', array( 'label' => esc_html__( 'آیکون', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'jobs_icon_color', array( 'label' => esc_html__( 'رنگ آیکون', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-jobs-callout__icon' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'jobs_icon_bg', array( 'label' => esc_html__( 'پس‌زمینه آیکون', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-jobs-callout__icon' => 'background: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'jobs_icon_size', array( 'label' => esc_html__( 'اندازه باکس', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 30, 'max' => 160 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-jobs-callout__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'jobs_icon_font_size', array( 'label' => esc_html__( 'اندازه آیکون', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 12, 'max' => 90 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-jobs-callout__icon' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .webmz-jobs-callout__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'jobs_icon_radius', array( 'label' => esc_html__( 'گردی گوشه‌ها', 'tadris' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => array( '{{WRAPPER}} .webmz-jobs-callout__icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->end_controls_section();
		$this->register_button_style_controls( 'jobs_button_style', esc_html__( 'دکمه', 'tadris' ), '.webmz-jobs-callout__button' );
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$title = (string) $s['title'];

		if ( ! empty( $s['highlight'] ) ) {
			$title = str_replace( $s['highlight'], '<span>' . esc_html( $s['highlight'] ) . '</span>', esc_html( $title ) );
		} else {
			$title = esc_html( $title );
		}

		if ( ! empty( $s['button_link'] ) && is_array( $s['button_link'] ) ) {
			$this->add_link_attributes( 'button_link', $s['button_link'] );
		}
		?>
		<section class="webmz-jobs-callout">
			<div class="webmz-jobs-callout__icon"><?php $this->render_theme_icon( $s['icon'], 'fas fa-user-headset' ); ?></div>
			<div class="webmz-jobs-callout__content">
				<h2 class="webmz-jobs-callout__title"><?php echo $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<p class="webmz-jobs-callout__desc"><?php echo esc_html( $s['description'] ); ?></p>
			</div>
			<?php if ( ! empty( $s['button_text'] ) ) : ?>
				<a class="webmz-jobs-callout__button" <?php echo $this->get_render_attribute_string( 'button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $s['button_text'] ); ?></a>
			<?php endif; ?>
		</section>
		<?php
	}
}

class WebMZ_Team_Widget extends Widget_Base {
	use WebMZ_Contact_Widgets_Trait;

	public function get_name() { return 'webmz-team-list'; }
	public function get_title() { return esc_html__( 'تیم ما', 'tadris' ); }
	public function get_icon() { return 'eicon-person'; }
	public function get_categories() { return array( 'webmz-widgets' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'اعضای تیم', 'tadris' ) ) );
		$repeater = new Repeater();
		$repeater->add_control( 'photo', array( 'label' => esc_html__( 'عکس', 'tadris' ), 'type' => Controls_Manager::MEDIA, 'default' => array( 'url' => Utils::get_placeholder_image_src() ) ) );
		$repeater->add_control( 'name', array( 'label' => esc_html__( 'نام', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'محمدامین سعادت‌پور', 'tadris' ), 'label_block' => true ) );
		$repeater->add_control( 'position', array( 'label' => esc_html__( 'سمت', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'CEO', 'tadris' ) ) );
		$repeater->add_control( 'telegram', array( 'label' => esc_html__( 'تلگرام', 'tadris' ), 'type' => Controls_Manager::URL ) );
		$repeater->add_control( 'x', array( 'label' => esc_html__( 'ایکس', 'tadris' ), 'type' => Controls_Manager::URL ) );
		$repeater->add_control( 'linkedin', array( 'label' => esc_html__( 'لینکدین', 'tadris' ), 'type' => Controls_Manager::URL ) );
		$repeater->add_control( 'instagram', array( 'label' => esc_html__( 'اینستاگرام', 'tadris' ), 'type' => Controls_Manager::URL ) );
		$this->add_control(
			'members',
			array(
				'label'       => esc_html__( 'لیست اعضا', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => array(
					array( 'name' => esc_html__( 'محمدامین سعادت‌پور', 'tadris' ), 'position' => 'CEO' ),
					array( 'name' => esc_html__( 'رضا شفیع‌آبادی', 'tadris' ), 'position' => esc_html__( 'کنترل کیفی', 'tadris' ) ),
					array( 'name' => esc_html__( 'محمدجعفر هنما', 'tadris' ), 'position' => esc_html__( 'برنامه‌نویس ارشد', 'tadris' ) ),
					array( 'name' => esc_html__( 'عباس امینی', 'tadris' ), 'position' => esc_html__( 'عکاس', 'tadris' ) ),
				),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'team_social_icons_section', array( 'label' => esc_html__( 'آیکون‌های شبکه‌های اجتماعی', 'tadris' ) ) );
		$this->add_control( 'telegram_icon', array( 'label' => esc_html__( 'آیکون تلگرام', 'tadris' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fab fa-telegram-plane', 'library' => 'fa-brands' ) ) );
		$this->add_control( 'x_icon', array( 'label' => esc_html__( 'آیکون ایکس', 'tadris' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fab fa-x-twitter', 'library' => 'fa-brands' ) ) );
		$this->add_control( 'linkedin_icon', array( 'label' => esc_html__( 'آیکون لینکدین', 'tadris' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fab fa-linkedin-in', 'library' => 'fa-brands' ) ) );
		$this->add_control( 'instagram_icon', array( 'label' => esc_html__( 'آیکون اینستاگرام', 'tadris' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fab fa-instagram', 'library' => 'fa-brands' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'team_layout_style', array( 'label' => esc_html__( 'چیدمان', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control( 'team_columns', array( 'label' => esc_html__( 'تعداد ستون', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 1, 'max' => 6 ) ), 'default' => array( 'size' => 4 ), 'selectors' => array( '{{WRAPPER}} .webmz-team' => 'grid-template-columns: repeat({{SIZE}}, minmax(0, 1fr));' ) ) );
		$this->add_responsive_control( 'team_gap', array( 'label' => esc_html__( 'فاصله کارت‌ها', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 80 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-team' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
		$this->register_box_style_controls( 'team_card_style', esc_html__( 'کارت عضو', 'tadris' ), '.webmz-team__card', array( 'bg' => '#ffffff' ) );
		$this->start_controls_section( 'team_image_style', array( 'label' => esc_html__( 'تصویر', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control( 'team_image_height', array( 'label' => esc_html__( 'ارتفاع تصویر', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 120, 'max' => 520 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-team__photo' => 'height: {{SIZE}}{{UNIT}}; aspect-ratio: auto;' ) ) );
		$this->add_responsive_control( 'team_image_radius', array( 'label' => esc_html__( 'گردی تصویر', 'tadris' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => array( '{{WRAPPER}} .webmz-team__photo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->end_controls_section();
		$this->register_text_style_controls( 'team_name_style', esc_html__( 'نام عضو', 'tadris' ), '.webmz-team__name', 'var(--webmz-color-primary)' );
		$this->register_text_style_controls( 'team_position_style', esc_html__( 'سمت', 'tadris' ), '.webmz-team__position', 'var(--webmz-color-text-gray)' );
		$this->start_controls_section( 'team_social_style', array( 'label' => esc_html__( 'شبکه‌های اجتماعی', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'team_social_color', array( 'label' => esc_html__( 'رنگ آیکون', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-team__social' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'team_social_bg', array( 'label' => esc_html__( 'پس‌زمینه', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-team__social' => 'background: {{VALUE}};' ) ) );
		$this->add_control( 'team_social_hover_color', array( 'label' => esc_html__( 'رنگ هاور', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-team__social:hover' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'team_social_hover_bg', array( 'label' => esc_html__( 'پس‌زمینه هاور', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-team__social:hover' => 'background: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'team_social_size', array( 'label' => esc_html__( 'اندازه باکس', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 18, 'max' => 70 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-team__social' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'team_social_icon_size', array( 'label' => esc_html__( 'اندازه آیکون', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 8, 'max' => 48 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-team__social svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .webmz-team__social i' => 'font-size: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'team_social_gap', array( 'label' => esc_html__( 'فاصله آیکون‌ها', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-team__socials' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	protected function render_social_link( $member, $key, $label, $icon ) {
		if ( empty( $member[ $key ]['url'] ) ) {
			return;
		}

		$link_key = $key . '_' . md5( $member[ $key ]['url'] );
		$this->add_link_attributes( $link_key, $member[ $key ] );
		?>
		<a class="webmz-team__social" aria-label="<?php echo esc_attr( $label ); ?>" <?php echo $this->get_render_attribute_string( $link_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) ); ?>
		</a>
		<?php
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$members = ! empty( $s['members'] ) && is_array( $s['members'] ) ? $s['members'] : array();
		?>
		<div class="webmz-team">
			<?php foreach ( $members as $member ) : ?>
				<article class="webmz-team__card">
					<div class="webmz-team__photo"><?php $this->render_image( isset( $member['photo'] ) ? $member['photo'] : array(), 'medium_large', 'webmz-team__img' ); ?></div>
					<h3 class="webmz-team__name"><?php echo esc_html( isset( $member['name'] ) ? $member['name'] : '' ); ?></h3>
					<p class="webmz-team__position"><?php echo esc_html( isset( $member['position'] ) ? $member['position'] : '' ); ?></p>
					<div class="webmz-team__socials">
						<?php $this->render_social_link( $member, 'telegram', 'Telegram', isset( $s['telegram_icon'] ) ? $s['telegram_icon'] : array() ); ?>
						<?php $this->render_social_link( $member, 'x', 'X', isset( $s['x_icon'] ) ? $s['x_icon'] : array() ); ?>
						<?php $this->render_social_link( $member, 'linkedin', 'LinkedIn', isset( $s['linkedin_icon'] ) ? $s['linkedin_icon'] : array() ); ?>
						<?php $this->render_social_link( $member, 'instagram', 'Instagram', isset( $s['instagram_icon'] ) ? $s['instagram_icon'] : array() ); ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<?php
	}
}

class WebMZ_Image_Gallery_Swiper_Widget extends Widget_Base {
	use WebMZ_Contact_Widgets_Trait;

	public function get_name() { return 'webmz-image-gallery-swiper'; }
	public function get_title() { return esc_html__( 'گالری تصاویر Swiper', 'tadris' ); }
	public function get_icon() { return 'eicon-slider-push'; }
	public function get_categories() { return array( 'webmz-widgets' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'تصاویر', 'tadris' ) ) );
		$this->add_control( 'gallery', array( 'label' => esc_html__( 'گالری', 'tadris' ), 'type' => Controls_Manager::GALLERY ) );
		$this->add_control( 'slides_per_view', array( 'label' => esc_html__( 'تعداد نمایش', 'tadris' ), 'type' => Controls_Manager::NUMBER, 'default' => 3, 'min' => 1, 'max' => 6 ) );
		$this->add_control( 'loop', array( 'label' => esc_html__( 'اسلایدر بی‌نهایت', 'tadris' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'autoplay', array( 'label' => esc_html__( 'پخش خودکار', 'tadris' ), 'type' => Controls_Manager::SWITCHER, 'default' => '' ) );
		$this->add_control( 'left_arrow_icon', array( 'label' => esc_html__( 'آیکون فلش چپ', 'tadris' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-arrow-left', 'library' => 'fa-solid' ) ) );
		$this->add_control( 'right_arrow_icon', array( 'label' => esc_html__( 'آیکون فلش راست', 'tadris' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-arrow-right', 'library' => 'fa-solid' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'gallery_slider_style', array( 'label' => esc_html__( 'اسلایدر', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control( 'gallery_height', array( 'label' => esc_html__( 'ارتفاع تصویر', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 100, 'max' => 620 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-gallery-swiper__item' => 'height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'gallery_bottom_space', array( 'label' => esc_html__( 'فاصله پایین ناوبری', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 120 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-gallery-swiper' => 'padding-bottom: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'gallery_item_radius', array( 'label' => esc_html__( 'گردی تصاویر', 'tadris' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => array( '{{WRAPPER}} .webmz-gallery-swiper__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->end_controls_section();
		$this->start_controls_section( 'gallery_nav_style', array( 'label' => esc_html__( 'دکمه‌های ناوبری', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'gallery_nav_color', array( 'label' => esc_html__( 'رنگ', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-gallery-swiper__button' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'gallery_nav_bg', array( 'label' => esc_html__( 'پس‌زمینه', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-gallery-swiper__button' => 'background: {{VALUE}};' ) ) );
		$this->add_control( 'gallery_nav_hover_color', array( 'label' => esc_html__( 'رنگ هاور', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-gallery-swiper__button:hover' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'gallery_nav_hover_bg', array( 'label' => esc_html__( 'پس‌زمینه هاور', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-gallery-swiper__button:hover' => 'background: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'gallery_nav_size', array( 'label' => esc_html__( 'اندازه دکمه', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 20, 'max' => 80 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-gallery-swiper__button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'gallery_nav_gap', array( 'label' => esc_html__( 'فاصله دکمه‌ها', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 80 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-gallery-swiper__nav' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$gallery = ! empty( $s['gallery'] ) && is_array( $s['gallery'] ) ? $s['gallery'] : array();
		$config  = array(
			'slidesPerView' => max( 1, absint( $s['slides_per_view'] ) ),
			'loop'          => 'yes' === $s['loop'],
			'autoplay'      => 'yes' === $s['autoplay'],
		);

		if ( empty( $gallery ) ) {
			$gallery = array(
				array( 'url' => Utils::get_placeholder_image_src() ),
				array( 'url' => Utils::get_placeholder_image_src() ),
				array( 'url' => Utils::get_placeholder_image_src() ),
			);
		}
		?>
		<div class="webmz-gallery-swiper swiper" dir="rtl" data-webmz-gallery-swiper="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">
			<div class="swiper-wrapper">
				<?php foreach ( $gallery as $image ) : ?>
					<div class="swiper-slide">
						<div class="webmz-gallery-swiper__item"><?php $this->render_image( $image, 'large', 'webmz-gallery-swiper__img' ); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="webmz-gallery-swiper__nav">
				<button class="webmz-gallery-swiper__button webmz-gallery-swiper__button--prev" type="button" aria-label="<?php esc_attr_e( 'قبلی', 'tadris' ); ?>"><?php Icons_Manager::render_icon( $s['left_arrow_icon'], array( 'aria-hidden' => 'true' ) ); ?></button>
				<button class="webmz-gallery-swiper__button webmz-gallery-swiper__button--next" type="button" aria-label="<?php esc_attr_e( 'بعدی', 'tadris' ); ?>"><?php Icons_Manager::render_icon( $s['right_arrow_icon'], array( 'aria-hidden' => 'true' ) ); ?></button>
			</div>
		</div>
		<?php
	}
}

class WebMZ_Contact_Tabs_Widget extends Widget_Base {
	use WebMZ_Contact_Widgets_Trait;

	public function get_name() { return 'webmz-contact-tabs'; }
	public function get_title() { return esc_html__( 'راه‌های ارتباطی تب‌دار', 'tadris' ); }
	public function get_icon() { return 'eicon-tabs'; }
	public function get_categories() { return array( 'webmz-widgets' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'دفترها', 'tadris' ) ) );
		$this->add_control( 'section_title', array( 'label' => esc_html__( 'عنوان بخش', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'راه‌های ارتباطی', 'tadris' ), 'label_block' => true ) );
		$repeater = new Repeater();
		$repeater->add_control( 'title', array( 'label' => esc_html__( 'عنوان تب', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'دفتر تهران', 'tadris' ), 'label_block' => true ) );
		$repeater->add_control( 'work_time', array( 'label' => esc_html__( 'ساعت کاری', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'شنبه تا پنجشنبه ساعت ۹:۰۰ تا ۱۷:۰۰', 'tadris' ), 'label_block' => true ) );
		$repeater->add_control( 'phone', array( 'label' => esc_html__( 'تلفن', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => '۰۲۱۰۰۰۰۰۰۰' ) );
		$repeater->add_control( 'address', array( 'label' => esc_html__( 'آدرس', 'tadris' ), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__( 'تهران، عباس‌آباد، خیابان سهروردی، کوچه عشوری', 'tadris' ) ) );
		$repeater->add_control( 'postal_code', array( 'label' => esc_html__( 'کد پستی', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => '۶۷۹۷۴۳۴۵۶۷' ) );
		$repeater->add_control( 'email', array( 'label' => esc_html__( 'ایمیل', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => 'mail@example.com' ) );
		$repeater->add_control( 'map_embed', array( 'label' => esc_html__( 'Embed نقشه گوگل', 'tadris' ), 'type' => Controls_Manager::TEXTAREA, 'description' => esc_html__( 'کد iframe نقشه یا آدرس src آن را وارد کنید.', 'tadris' ) ) );
		$this->add_control(
			'offices',
			array(
				'label'       => esc_html__( 'دفترها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array( 'title' => esc_html__( 'دفتر تهران', 'tadris' ) ),
					array( 'title' => esc_html__( 'دفتر شهرکرد', 'tadris' ) ),
					array( 'title' => esc_html__( 'دفتر تبریز', 'tadris' ) ),
				),
			)
		);
		$this->end_controls_section();

		$this->register_box_style_controls( 'tabs_box_style', esc_html__( 'باکس اصلی', 'tadris' ), '.webmz-contact-tabs', array( 'bg' => '#ffffff' ) );
		$this->register_text_style_controls( 'tabs_title_style', esc_html__( 'عنوان بخش', 'tadris' ), '.webmz-contact-tabs__head h2', 'var(--webmz-color-secondary)' );
		$this->start_controls_section( 'tabs_buttons_style', array( 'label' => esc_html__( 'تب‌ها', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control( 'tabs_button_gap', array( 'label' => esc_html__( 'فاصله تب‌ها', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 50 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs__buttons' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'tabs_button_color', array( 'label' => esc_html__( 'رنگ متن', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs__tab' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'tabs_button_bg', array( 'label' => esc_html__( 'پس‌زمینه', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs__tab' => 'background: {{VALUE}};' ) ) );
		$this->add_control( 'tabs_button_active_color', array( 'label' => esc_html__( 'رنگ تب فعال', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs__tab.is-active' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'tabs_button_active_bg', array( 'label' => esc_html__( 'پس‌زمینه تب فعال', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs__tab.is-active' => 'background: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'tabs_button_typography', 'selector' => '{{WRAPPER}} .webmz-contact-tabs__tab' ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'tabs_button_border', 'selector' => '{{WRAPPER}} .webmz-contact-tabs__tab' ) );
		$this->add_responsive_control( 'tabs_button_radius', array( 'label' => esc_html__( 'گردی تب‌ها', 'tadris' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs__tab' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->end_controls_section();
		$this->start_controls_section( 'tabs_info_style', array( 'label' => esc_html__( 'اطلاعات تماس', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control( 'tabs_panel_gap', array( 'label' => esc_html__( 'فاصله اطلاعات و نقشه', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 90 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs__panel' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'tabs_label_color', array( 'label' => esc_html__( 'رنگ برچسب‌ها', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs dt' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'tabs_value_color', array( 'label' => esc_html__( 'رنگ مقادیر', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs dd' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'tabs_email_color', array( 'label' => esc_html__( 'رنگ ایمیل', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs dd a' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'tabs_time_color', array( 'label' => esc_html__( 'رنگ ساعت کاری', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs__time' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'tabs_time_bg', array( 'label' => esc_html__( 'پس‌زمینه ساعت کاری', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs__time' => 'background: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'tabs_info_typography', 'selector' => '{{WRAPPER}} .webmz-contact-tabs dl, {{WRAPPER}} .webmz-contact-tabs__time' ) );
		$this->end_controls_section();
		$this->start_controls_section( 'tabs_map_style', array( 'label' => esc_html__( 'نقشه', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control( 'tabs_map_height', array( 'label' => esc_html__( 'ارتفاع نقشه', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 160, 'max' => 700 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs__map, {{WRAPPER}} .webmz-contact-tabs__map iframe, {{WRAPPER}} .webmz-contact-tabs__map-placeholder' => 'min-height: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'tabs_map_bg', array( 'label' => esc_html__( 'پس‌زمینه جایگزین', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs__map' => 'background: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'tabs_map_radius', array( 'label' => esc_html__( 'گردی نقشه', 'tadris' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => array( '{{WRAPPER}} .webmz-contact-tabs__map' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	protected function render_map( $embed ) {
		$embed = trim( (string) $embed );

		if ( '' === $embed ) {
			echo '<div class="webmz-contact-tabs__map-placeholder"></div>';
			return;
		}

		if ( false !== stripos( $embed, '<iframe' ) ) {
			echo wp_kses( $embed, array( 'iframe' => array( 'src' => true, 'width' => true, 'height' => true, 'style' => true, 'allowfullscreen' => true, 'loading' => true, 'referrerpolicy' => true ) ) );
			return;
		}

		?>
		<iframe src="<?php echo esc_url( $embed ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
		<?php
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$offices = ! empty( $s['offices'] ) && is_array( $s['offices'] ) ? array_values( $s['offices'] ) : array();
		?>
		<section class="webmz-contact-tabs" data-webmz-contact-tabs>
			<div class="webmz-contact-tabs__head">
				<h2><?php echo esc_html( $s['section_title'] ); ?></h2>
				<div class="webmz-contact-tabs__buttons" role="tablist">
					<?php foreach ( $offices as $index => $office ) : ?>
						<button class="webmz-contact-tabs__tab <?php echo 0 === $index ? 'is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>" data-webmz-tab="<?php echo esc_attr( $index ); ?>"><?php echo esc_html( $office['title'] ); ?></button>
					<?php endforeach; ?>
				</div>
			</div>
			<?php foreach ( $offices as $index => $office ) : ?>
				<div class="webmz-contact-tabs__panel <?php echo 0 === $index ? 'is-active' : ''; ?>" data-webmz-panel="<?php echo esc_attr( $index ); ?>">
					<div class="webmz-contact-tabs__info">
						<?php if ( ! empty( $office['work_time'] ) ) : ?><span class="webmz-contact-tabs__time"><?php esc_html_e( 'ساعت کاری:', 'tadris' ); ?> <?php echo esc_html( $office['work_time'] ); ?></span><?php endif; ?>
						<dl>
							<?php if ( ! empty( $office['phone'] ) ) : ?><dt><?php esc_html_e( 'تلفن:', 'tadris' ); ?></dt><dd><?php echo esc_html( $office['phone'] ); ?></dd><?php endif; ?>
							<?php if ( ! empty( $office['address'] ) ) : ?><dt><?php esc_html_e( 'آدرس:', 'tadris' ); ?></dt><dd><?php echo esc_html( $office['address'] ); ?></dd><?php endif; ?>
							<?php if ( ! empty( $office['postal_code'] ) ) : ?><dt><?php esc_html_e( 'کد پستی:', 'tadris' ); ?></dt><dd><?php echo esc_html( $office['postal_code'] ); ?></dd><?php endif; ?>
							<?php if ( ! empty( $office['email'] ) ) : ?><dt><?php esc_html_e( 'ایمیل:', 'tadris' ); ?></dt><dd><a href="mailto:<?php echo esc_attr( antispambot( $office['email'] ) ); ?>"><?php echo esc_html( $office['email'] ); ?></a></dd><?php endif; ?>
						</dl>
					</div>
					<div class="webmz-contact-tabs__map"><?php $this->render_map( isset( $office['map_embed'] ) ? $office['map_embed'] : '' ); ?></div>
				</div>
			<?php endforeach; ?>
		</section>
		<?php
	}
}

class WebMZ_Ajax_Contact_Form_Widget extends Widget_Base {
	use WebMZ_Contact_Widgets_Trait;

	public function get_name() { return 'webmz-ajax-contact-form'; }
	public function get_title() { return esc_html__( 'فرم تماس ایجکسی', 'tadris' ); }
	public function get_icon() { return 'eicon-form-horizontal'; }
	public function get_categories() { return array( 'webmz-widgets' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'title', array( 'label' => esc_html__( 'عنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'فرم درخواست ارتباط', 'tadris' ), 'label_block' => true ) );
		$this->add_control( 'highlight', array( 'label' => esc_html__( 'بخش رنگی عنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'درخواست ارتباط', 'tadris' ) ) );
		$this->add_control( 'description', array( 'label' => esc_html__( 'توضیحات', 'tadris' ), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__( 'برای دریافت مشاوره رایگان و اختصاصی، کافیست اطلاعات تماس خود را در فرم زیر وارد کنید.', 'tadris' ) ) );
		$this->add_control( 'submit_text', array( 'label' => esc_html__( 'متن دکمه ارسال', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'ارسال درخواست', 'tadris' ) ) );
		$this->add_control( 'success_message', array( 'label' => esc_html__( 'متن موفقیت بعد از ارسال', 'tadris' ), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__( 'درخواست شما با موفقیت ثبت شد.', 'tadris' ), 'label_block' => true ) );
		$this->add_control( 'hourly_limit', array( 'label' => esc_html__( 'حداکثر ارسال در ساعت برای هر IP', 'tadris' ), 'type' => Controls_Manager::NUMBER, 'default' => 5, 'min' => 1, 'max' => 50 ) );
		$this->end_controls_section();

		$this->start_controls_section( 'fields_section', array( 'label' => esc_html__( 'فیلدها', 'tadris' ) ) );
		$repeater = new Repeater();
		$repeater->add_control( 'label', array( 'label' => esc_html__( 'لیبل', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'نام و نام خانوادگی', 'tadris' ), 'label_block' => true ) );
		$repeater->add_control( 'field_id', array( 'label' => esc_html__( 'شناسه فیلد انگلیسی', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => 'full_name', 'description' => esc_html__( 'فقط حروف انگلیسی، عدد و زیرخط.', 'tadris' ) ) );
		$repeater->add_control( 'type', array( 'label' => esc_html__( 'نوع فیلد', 'tadris' ), 'type' => Controls_Manager::SELECT, 'default' => 'text', 'options' => array( 'text' => esc_html__( 'متن', 'tadris' ), 'email' => esc_html__( 'ایمیل', 'tadris' ), 'tel' => esc_html__( 'تلفن', 'tadris' ), 'textarea' => esc_html__( 'متن چندخطی', 'tadris' ), 'select' => esc_html__( 'انتخابی', 'tadris' ), 'checkbox' => esc_html__( 'چک‌باکس', 'tadris' ) ) ) );
		$repeater->add_control( 'placeholder', array( 'label' => esc_html__( 'Placeholder', 'tadris' ), 'type' => Controls_Manager::TEXT ) );
		$repeater->add_control( 'options', array( 'label' => esc_html__( 'گزینه‌ها', 'tadris' ), 'type' => Controls_Manager::TEXTAREA, 'description' => esc_html__( 'برای select هر گزینه را در یک خط وارد کنید.', 'tadris' ), 'condition' => array( 'type' => 'select' ) ) );
		$repeater->add_control( 'required', array( 'label' => esc_html__( 'اجباری', 'tadris' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$repeater->add_control( 'width', array( 'label' => esc_html__( 'عرض', 'tadris' ), 'type' => Controls_Manager::SELECT, 'default' => '50', 'options' => array( '50' => esc_html__( 'نیم‌عرض', 'tadris' ), '100' => esc_html__( 'تمام‌عرض', 'tadris' ) ) ) );
		$this->add_control(
			'fields',
			array(
				'label'       => esc_html__( 'فیلدهای فرم', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ label }}}',
				'default'     => array(
					array( 'label' => esc_html__( 'نام و نام خانوادگی', 'tadris' ), 'field_id' => 'full_name', 'type' => 'text', 'width' => '50' ),
					array( 'label' => esc_html__( 'شماره تماس', 'tadris' ), 'field_id' => 'phone', 'type' => 'tel', 'width' => '50' ),
					array( 'label' => esc_html__( 'آدرس ایمیل', 'tadris' ), 'field_id' => 'email', 'type' => 'email', 'width' => '50' ),
					array( 'label' => esc_html__( 'موضوع درخواست', 'tadris' ), 'field_id' => 'subject', 'type' => 'text', 'width' => '50' ),
					array( 'label' => esc_html__( 'پیام شما', 'tadris' ), 'field_id' => 'message', 'type' => 'textarea', 'width' => '100' ),
				),
			)
		);
		$this->end_controls_section();

		$this->register_box_style_controls( 'form_box_style', esc_html__( 'باکس فرم', 'tadris' ), '.webmz-ajax-form', array( 'bg' => '#ffffff' ) );
		$this->register_text_style_controls( 'form_title_style', esc_html__( 'عنوان فرم', 'tadris' ), '.webmz-ajax-form__header h2', 'var(--webmz-color-text-dark)' );
		$this->register_text_style_controls( 'form_highlight_style', esc_html__( 'بخش رنگی عنوان', 'tadris' ), '.webmz-ajax-form__header h2 span', 'var(--webmz-color-primary)' );
		$this->register_text_style_controls( 'form_desc_style', esc_html__( 'توضیحات فرم', 'tadris' ), '.webmz-ajax-form__header p', 'var(--webmz-color-text-dark)' );
		$this->start_controls_section( 'form_fields_style', array( 'label' => esc_html__( 'فیلدها', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control( 'form_rows_gap', array( 'label' => esc_html__( 'فاصله ردیف‌ها', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 80 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-ajax-form__grid' => 'row-gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'form_cols_gap', array( 'label' => esc_html__( 'فاصله ستون‌ها', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 100 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-ajax-form__grid' => 'column-gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'form_label_color', array( 'label' => esc_html__( 'رنگ لیبل', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-ajax-form__field > span:first-child' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'form_label_typography', 'selector' => '{{WRAPPER}} .webmz-ajax-form__field > span:first-child' ) );
		$this->add_control( 'form_input_color', array( 'label' => esc_html__( 'رنگ متن فیلد', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-ajax-form input:not([type="checkbox"]):not([type="file"]), {{WRAPPER}} .webmz-ajax-form textarea, {{WRAPPER}} .webmz-ajax-form select' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'form_input_bg', array( 'label' => esc_html__( 'پس‌زمینه فیلد', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-ajax-form input:not([type="checkbox"]):not([type="file"]), {{WRAPPER}} .webmz-ajax-form textarea, {{WRAPPER}} .webmz-ajax-form select' => 'background: {{VALUE}};' ) ) );
		$this->add_control( 'form_input_border_color', array( 'label' => esc_html__( 'رنگ حاشیه فیلد', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-ajax-form input:not([type="checkbox"]):not([type="file"]), {{WRAPPER}} .webmz-ajax-form textarea, {{WRAPPER}} .webmz-ajax-form select' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'form_input_focus_color', array( 'label' => esc_html__( 'رنگ فوکوس', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-ajax-form input:focus, {{WRAPPER}} .webmz-ajax-form textarea:focus, {{WRAPPER}} .webmz-ajax-form select:focus' => 'border-color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'form_input_typography', 'selector' => '{{WRAPPER}} .webmz-ajax-form input:not([type="checkbox"]):not([type="file"]), {{WRAPPER}} .webmz-ajax-form textarea, {{WRAPPER}} .webmz-ajax-form select' ) );
		$this->add_responsive_control( 'form_input_height', array( 'label' => esc_html__( 'ارتفاع فیلدها', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 34, 'max' => 100 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-ajax-form input:not([type="checkbox"]):not([type="file"]), {{WRAPPER}} .webmz-ajax-form select' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'form_textarea_height', array( 'label' => esc_html__( 'ارتفاع textarea', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 100, 'max' => 520 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-ajax-form textarea' => 'min-height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'form_input_radius', array( 'label' => esc_html__( 'گردی فیلدها', 'tadris' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => array( '{{WRAPPER}} .webmz-ajax-form input:not([type="checkbox"]):not([type="file"]), {{WRAPPER}} .webmz-ajax-form textarea, {{WRAPPER}} .webmz-ajax-form select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->end_controls_section();
		$this->register_button_style_controls( 'form_submit_style', esc_html__( 'دکمه ارسال', 'tadris' ), '.webmz-ajax-form__submit' );
		$this->start_controls_section( 'form_message_style', array( 'label' => esc_html__( 'پیام فرم', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'form_message_color', array( 'label' => esc_html__( 'رنگ عادی', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-ajax-form__message' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'form_success_color', array( 'label' => esc_html__( 'رنگ موفقیت', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-ajax-form__message.is-success' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'form_error_color', array( 'label' => esc_html__( 'رنگ خطا', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-ajax-form__message.is-error' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'form_message_typography', 'selector' => '{{WRAPPER}} .webmz-ajax-form__message' ) );
		$this->end_controls_section();
	}

	protected function normalize_fields( $fields ) {
		$clean = array();
		$used  = array();

		foreach ( (array) $fields as $field ) {
			$id = ! empty( $field['field_id'] ) ? sanitize_key( $field['field_id'] ) : sanitize_key( $field['label'] );
			if ( '' === $id || isset( $used[ $id ] ) ) {
				$id = 'field_' . ( count( $clean ) + 1 );
			}
			$used[ $id ] = true;

			$type = ! empty( $field['type'] ) ? sanitize_key( $field['type'] ) : 'text';
			if ( ! in_array( $type, array( 'text', 'email', 'tel', 'textarea', 'select', 'checkbox' ), true ) ) {
				$type = 'text';
			}

			$clean[] = array(
				'id'          => $id,
				'label'       => isset( $field['label'] ) ? sanitize_text_field( $field['label'] ) : $id,
				'type'        => $type,
				'placeholder' => isset( $field['placeholder'] ) ? sanitize_text_field( $field['placeholder'] ) : '',
				'options'     => isset( $field['options'] ) ? array_filter( array_map( 'sanitize_text_field', preg_split( '/\r\n|\r|\n/', (string) $field['options'] ) ) ) : array(),
				'required'    => ! empty( $field['required'] ) && 'yes' === $field['required'],
				'width'       => isset( $field['width'] ) && '100' === (string) $field['width'] ? '100' : '50',
			);
		}

		return $clean;
	}

	protected function render_field( $field ) {
		$name     = 'fields[' . $field['id'] . ']';
		$required = $field['required'] ? ' required' : '';
		?>
		<label class="webmz-ajax-form__field webmz-ajax-form__field--<?php echo esc_attr( $field['width'] ); ?> webmz-ajax-form__field--<?php echo esc_attr( $field['type'] ); ?>">
			<span><?php echo esc_html( $field['label'] ); ?></span>
			<?php if ( 'textarea' === $field['type'] ) : ?>
				<textarea name="<?php echo esc_attr( $name ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"<?php echo $required; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>></textarea>
			<?php elseif ( 'select' === $field['type'] ) : ?>
				<select name="<?php echo esc_attr( $name ); ?>"<?php echo $required; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<option value=""><?php echo esc_html( $field['placeholder'] ? $field['placeholder'] : __( 'انتخاب کنید', 'tadris' ) ); ?></option>
					<?php foreach ( $field['options'] as $option ) : ?><option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option><?php endforeach; ?>
				</select>
			<?php elseif ( 'checkbox' === $field['type'] ) : ?>
				<input type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="yes"<?php echo $required; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php else : ?>
				<input type="<?php echo esc_attr( $field['type'] ); ?>" name="<?php echo esc_attr( $name ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"<?php echo $required; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php endif; ?>
		</label>
		<?php
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$fields = $this->normalize_fields( $s['fields'] );
		$title  = esc_html( $s['title'] );

		if ( ! empty( $s['highlight'] ) ) {
			$title = str_replace( esc_html( $s['highlight'] ), '<span>' . esc_html( $s['highlight'] ) . '</span>', $title );
		}

		$config = array(
			'form_title'      => sanitize_text_field( $s['title'] ),
			'success_message' => sanitize_text_field( $s['success_message'] ),
			'hourly_limit'    => max( 1, min( 50, absint( $s['hourly_limit'] ) ) ),
			'fields'          => $fields,
		);
		$encoded   = base64_encode( wp_json_encode( $config ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$signature = function_exists( 'webmz_contact_form_sign_config' ) ? webmz_contact_form_sign_config( $encoded ) : '';
		?>
		<section class="webmz-ajax-form">
			<header class="webmz-ajax-form__header">
				<h2><?php echo $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<p><?php echo esc_html( $s['description'] ); ?></p>
			</header>
			<form class="webmz-ajax-form__form" data-webmz-ajax-form enctype="multipart/form-data">
				<input type="hidden" name="action" value="webmz_ajax_contact_form_submit">
				<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'webmz_ajax_contact_form' ) ); ?>">
				<input type="hidden" name="config" value="<?php echo esc_attr( $encoded ); ?>">
				<input type="hidden" name="signature" value="<?php echo esc_attr( $signature ); ?>">
				<input type="text" name="webmz_contact_website" value="" tabindex="-1" autocomplete="off" class="webmz-ajax-form__honeypot" aria-hidden="true">
				<div class="webmz-ajax-form__grid">
					<?php foreach ( $fields as $field ) : ?>
						<?php $this->render_field( $field ); ?>
					<?php endforeach; ?>
				</div>
				<div class="webmz-ajax-form__footer">
					<div class="webmz-ajax-form__actions">
						<button class="webmz-ajax-form__submit" type="submit"><?php echo esc_html( $s['submit_text'] ); ?></button>
						<div class="webmz-ajax-form__message" aria-live="polite"></div>
					</div>
				</div>
			</form>
		</section>
		<?php
	}
}

class WebMZ_Offices_Address_Widget extends Widget_Base {
	use WebMZ_Contact_Widgets_Trait;

	public function get_name() { return 'webmz-offices-address'; }
	public function get_title() { return esc_html__( 'آدرس دفاتر ما', 'tadris' ); }
	public function get_icon() { return 'eicon-google-maps'; }
	public function get_categories() { return array( 'webmz-widgets' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'دفترها', 'tadris' ) ) );
		$this->add_control( 'phone_label', array( 'label' => esc_html__( 'لیبل تلفن', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'تلفن:', 'tadris' ) ) );
		$this->add_control( 'address_label', array( 'label' => esc_html__( 'لیبل آدرس', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'آدرس:', 'tadris' ) ) );
		$repeater = new Repeater();
		$repeater->add_control( 'title', array( 'label' => esc_html__( 'عنوان', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'دفتر تهران', 'tadris' ), 'label_block' => true ) );
		$repeater->add_control( 'phone', array( 'label' => esc_html__( 'تلفن', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => '۰۲۱ ۰۰۰ ۰۰۰' ) );
		$repeater->add_control( 'address', array( 'label' => esc_html__( 'آدرس', 'tadris' ), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__( 'تهران، عباس آباد، خیابان سهروردی، کوچه عشوری', 'tadris' ) ) );
		$repeater->add_control( 'map_image', array( 'label' => esc_html__( 'تصویر نقشه/استان', 'tadris' ), 'type' => Controls_Manager::MEDIA ) );
		$this->add_control(
			'offices',
			array(
				'label'       => esc_html__( 'دفترها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array( 'title' => esc_html__( 'دفتر تهران', 'tadris' ) ),
					array( 'title' => esc_html__( 'دفتر شهرکرد', 'tadris' ) ),
					array( 'title' => esc_html__( 'دفتر تبریز', 'tadris' ) ),
				),
			)
		);
		$this->end_controls_section();

		$this->register_box_style_controls( 'offices_box_style', esc_html__( 'باکس اصلی', 'tadris' ), '.webmz-office-addresses' );
		$this->register_box_style_controls( 'offices_item_style', esc_html__( 'کارت دفتر', 'tadris' ), '.webmz-office-addresses__item' );
		$this->start_controls_section( 'offices_accent_style', array( 'label' => esc_html__( 'نشان رنگی کارت', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'offices_accent_color', array( 'label' => esc_html__( 'رنگ نشان', 'tadris' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .webmz-office-addresses__item::after' => 'background: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'offices_accent_width', array( 'label' => esc_html__( 'عرض نشان', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 6, 'max' => 80 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-office-addresses__item::after' => 'width: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'offices_accent_height', array( 'label' => esc_html__( 'ارتفاع نشان', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 4, 'max' => 30 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-office-addresses__item::after' => 'height: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
		$this->register_text_style_controls( 'offices_title_style', esc_html__( 'عنوان دفتر', 'tadris' ), '.webmz-office-addresses__content h3', '#ffffff' );
		$this->register_text_style_controls( 'offices_text_style', esc_html__( 'متن اطلاعات', 'tadris' ), '.webmz-office-addresses__content p', '#ffffff' );
		$this->register_text_style_controls( 'offices_label_style', esc_html__( 'لیبل اطلاعات', 'tadris' ), '.webmz-office-addresses__content span', 'rgba(255,255,255,.58)' );
		$this->start_controls_section( 'offices_map_style', array( 'label' => esc_html__( 'تصویر نقشه', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control( 'offices_map_column', array( 'label' => esc_html__( 'عرض ستون نقشه', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 70, 'max' => 360 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-office-addresses__item' => 'grid-template-columns: {{SIZE}}{{UNIT}} 1fr;' ) ) );
		$this->add_responsive_control( 'offices_map_opacity', array( 'label' => esc_html__( 'شفافیت تصویر', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => .05 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-office-addresses__map' => 'opacity: {{SIZE}};' ) ) );
		$this->add_responsive_control( 'offices_map_size', array( 'label' => esc_html__( 'اندازه تصویر', 'tadris' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 40, 'max' => 260 ) ), 'selectors' => array( '{{WRAPPER}} .webmz-office-addresses__map-img' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$offices = ! empty( $s['offices'] ) && is_array( $s['offices'] ) ? $s['offices'] : array();
		?>
		<section class="webmz-office-addresses">
			<?php foreach ( $offices as $office ) : ?>
				<article class="webmz-office-addresses__item">
					<div class="webmz-office-addresses__map">
						<?php if ( ! empty( $office['map_image']['id'] ) || ! empty( $office['map_image']['url'] ) ) : ?>
							<?php $this->render_image( $office['map_image'], 'medium', 'webmz-office-addresses__map-img' ); ?>
						<?php endif; ?>
					</div>
					<div class="webmz-office-addresses__content">
						<h3><?php echo esc_html( $office['title'] ); ?></h3>
						<p><span><?php echo esc_html( $s['phone_label'] ); ?></span> <?php echo esc_html( $office['phone'] ); ?></p>
						<p><span><?php echo esc_html( $s['address_label'] ); ?></span> <?php echo esc_html( $office['address'] ); ?></p>
					</div>
				</article>
			<?php endforeach; ?>
		</section>
		<?php
	}
}
