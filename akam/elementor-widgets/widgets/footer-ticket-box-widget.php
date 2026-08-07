<?php
/**
 * Footer ticket CTA box — Zhaket marketplace style.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Horizontal RTL footer support/ticket call-to-action with overlapping avatars.
 */
class Footer_Ticket_Box_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-footer-ticket-box';
	}

	public function get_title() {
		return esc_html__( 'باکس تیکت فوتر ژاکت', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'footer', 'ticket', 'support', 'zhaket', 'فوتر', 'تیکت', 'پشتیبانی', 'ژاکت' );
	}

	public function get_style_depends() {
		return array( 'webmz-footer-ticket-box' );
	}

	/**
	 * Theme primary color from options.
	 *
	 * @return string
	 */
	private function theme_primary_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_primary' ) : '#0878f9';
	}

	/**
	 * Theme text dark color from options.
	 *
	 * @return string
	 */
	private function theme_text_dark_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_text_dark' ) : '#111827';
	}

	/**
	 * Theme text gray color from options.
	 *
	 * @return string
	 */
	private function theme_text_gray_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_text_gray' ) : '#6b7280';
	}

	/**
	 * Theme primary light color from options.
	 *
	 * @return string
	 */
	private function theme_primary_light_color() {
		return function_exists( 'webmz_get_option' ) ? (string) webmz_get_option( 'color_primary_light' ) : '#eef4ff';
	}

	/**
	 * Default support tickets URL.
	 *
	 * @return string
	 */
	private function default_ticket_url() {
		if ( function_exists( 'webmz_ticket_get_account_url' ) ) {
			return webmz_ticket_get_account_url();
		}

		if ( function_exists( 'wc_get_account_endpoint_url' ) ) {
			return wc_get_account_endpoint_url( 'support-tickets' );
		}

		return home_url( '/my-account/support-tickets/' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_layout_style_controls();
		$this->register_box_style_controls();
		$this->register_text_style_controls();
		$this->register_avatar_style_controls();
		$this->register_button_style_controls();
	}

	protected function register_content_controls() {
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
				'default'     => esc_html__( 'سوالی دارید؟ بپرسید', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control();

		$this->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'زیرعنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'ابتدا عضو شوید و سپس تیکت بفرستید', 'tadris' ),
				'label_block' => true,
				'rows'        => 2,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'متن دکمه', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'ارسال تیکت', 'tadris' ),
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'       => esc_html__( 'لینک دکمه', 'tadris' ),
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => $this->default_ticket_url() ),
				'placeholder' => 'https://example.com',
			)
		);

		$this->add_control(
			'avatars_heading',
			array(
				'label'     => esc_html__( 'آواتارهای پشتیبانی', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_avatars',
			array(
				'label'        => esc_html__( 'نمایش آواتارها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'avatar_back',
			array(
				'label'     => esc_html__( 'آواتار پشت', 'tadris' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array( 'show_avatars' => 'yes' ),
			)
		);

		$this->add_control(
			'avatar_front',
			array(
				'label'     => esc_html__( 'آواتار جلو', 'tadris' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array( 'show_avatars' => 'yes' ),
			)
		);

		$this->add_control(
			'avatar_size_render',
			array(
				'label'     => esc_html__( 'سایز رندر تصویر', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'thumbnail',
				'options'   => array(
					'thumbnail'    => esc_html__( 'بندانگشتی', 'tadris' ),
					'medium'       => esc_html__( 'متوسط', 'tadris' ),
					'medium_large' => esc_html__( 'متوسط بزرگ', 'tadris' ),
				),
				'condition' => array( 'show_avatars' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_layout_style_controls() {
		$this->start_controls_section(
			'layout_style',
			array(
				'label' => esc_html__( 'چیدمان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_align',
			array(
				'label'     => esc_html__( 'تراز افقی', 'tadris' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start'    => array(
						'title' => esc_html__( 'راست', 'tadris' ),
						'icon'  => 'eicon-text-align-right',
					),
					'center'        => array(
						'title' => esc_html__( 'وسط', 'tadris' ),
						'icon'  => 'eicon-text-align-center',
					),
					'space-between' => array(
						'title' => esc_html__( 'پخش', 'tadris' ),
						'icon'  => 'eicon-justify-space-between-h',
					),
				),
				'default'   => 'space-between',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-ticket-box' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'items_align',
			array(
				'label'     => esc_html__( 'تراز عمودی', 'tadris' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
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
					'{{WRAPPER}} .webmz-footer-ticket-box' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_gap',
			array(
				'label'      => esc_html__( 'فاصله بین بخش‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-ticket-box' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'main_gap',
			array(
				'label'      => esc_html__( 'فاصله آواتار و متن', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
				'default'    => array( 'size' => 16, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-ticket-box__main' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'text_gap',
			array(
				'label'      => esc_html__( 'فاصله عنوان و زیرعنوان', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 4, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-ticket-box__content' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'stack_mobile',
			array(
				'label'                => esc_html__( 'چیدمان عمودی در موبایل', 'tadris' ),
				'type'                 => Controls_Manager::SWITCHER,
				'default'              => 'yes',
				'return_value'         => 'yes',
				'selectors_dictionary' => array(
					'yes' => 'column',
					''    => 'row',
				),
				'selectors'            => array(
					'(mobile){{WRAPPER}} .webmz-footer-ticket-box' => 'flex-direction: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'mobile_align',
			array(
				'label'     => esc_html__( 'تراز در موبایل', 'tadris' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'center'     => array(
						'title' => esc_html__( 'وسط', 'tadris' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-start' => array(
						'title' => esc_html__( 'راست', 'tadris' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'(mobile){{WRAPPER}} .webmz-footer-ticket-box' => 'align-items: {{VALUE}};',
					'(mobile){{WRAPPER}} .webmz-footer-ticket-box__main' => 'align-items: {{VALUE}};',
					'(mobile){{WRAPPER}} .webmz-footer-ticket-box__content' => 'text-align: {{VALUE}};',
				),
				'condition' => array( 'stack_mobile' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_box_style_controls() {
		$this->webmz_register_box_style_controls(
			'box_style',
			esc_html__( 'باکس', 'tadris' ),
			'.webmz-footer-ticket-box',
			array(
				'default_background' => 'var(--webmz-bg, #f8f9fb)',
				'bordered'           => true,
			)
		);
	}

	protected function register_text_style_controls() {
		$this->start_controls_section(
			'title_style',
			array(
				'label' => esc_html__( 'عنوان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-dark)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-ticket-box__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .webmz-footer-ticket-box__title',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'subtitle_style',
			array(
				'label' => esc_html__( 'زیرعنوان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-text-gray)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-ticket-box__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .webmz-footer-ticket-box__subtitle',
			)
		);

		$this->end_controls_section();
	}

	protected function register_avatar_style_controls() {
		$this->start_controls_section(
			'avatar_style',
			array(
				'label'     => esc_html__( 'آواتارها', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_avatars' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'avatar_size',
			array(
				'label'      => esc_html__( 'اندازه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 28, 'max' => 80 ) ),
				'default'    => array( 'size' => 44, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-ticket-box' => '--webmz-ftb-avatar-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_overlap',
			array(
				'label'      => esc_html__( 'میزان هم‌پوشانی', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'size' => 14, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-ticket-box' => '--webmz-ftb-avatar-overlap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_back_offset',
			array(
				'label'      => esc_html__( 'جابه‌جایی عمودی آواتار پشت', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 20 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-ticket-box' => '--webmz-ftb-avatar-back-offset: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'avatar_border_color',
			array(
				'label'     => esc_html__( 'رنگ حاشیه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-ticket-box__avatar' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_border_width',
			array(
				'label'      => esc_html__( 'ضخامت حاشیه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 6 ) ),
				'default'    => array( 'size' => 2, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-ticket-box__avatar' => 'border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_button_style_controls() {
		$this->start_controls_section(
			'button_style',
			array(
				'label' => esc_html__( 'دکمه', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'       => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'پیش‌فرض: ترکیب رنگ اصلی پوسته با سفید.', 'tadris' ),
				'selectors'   => array(
					'{{WRAPPER}} .webmz-footer-ticket-box__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg_hover',
			array(
				'label'     => esc_html__( 'پس‌زمینه هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-ticket-box__button:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-ticket-box__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_color_hover',
			array(
				'label'     => esc_html__( 'رنگ متن هاور', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-footer-ticket-box__button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .webmz-footer-ticket-box__button',
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'      => '10',
					'right'    => '22',
					'bottom'   => '10',
					'left'     => '22',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-ticket-box__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 50 ) ),
				'default'    => array( 'size' => 12, 'unit' => 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-footer-ticket-box__button' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render avatar image markup.
	 *
	 * @param array<string,mixed> $image Image settings.
	 * @param string              $class CSS class.
	 * @param string              $size  Image size.
	 * @return void
	 */
	protected function render_avatar_image( $image, $class, $size = 'thumbnail' ) {
		$image = is_array( $image ) ? $image : array();

		if ( ! empty( $image['id'] ) ) {
			echo wp_get_attachment_image( absint( $image['id'] ), $size, false, array( 'class' => $class ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		if ( empty( $image['url'] ) ) {
			return;
		}
		?>
		<img class="<?php echo esc_attr( $class ); ?>" src="<?php echo esc_url( $image['url'] ); ?>" alt="" loading="lazy" decoding="async">
		<?php
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$title_tag = $this->webmz_get_title_tag( $settings );
		$show_avatars = 'yes' === ( $settings['show_avatars'] ?? '' );
		$avatar_size  = ! empty( $settings['avatar_size_render'] ) ? $settings['avatar_size_render'] : 'thumbnail';

		$button_link = ! empty( $settings['button_link'] ) && is_array( $settings['button_link'] )
			? $settings['button_link']
			: array( 'url' => $this->default_ticket_url() );

		if ( empty( $button_link['url'] ) ) {
			$button_link['url'] = $this->default_ticket_url();
		}

		$this->add_render_attribute( 'button', 'class', 'webmz-footer-ticket-box__button' );
		$this->add_link_attributes( 'button', $button_link );
		?>
		<div class="webmz-footer-ticket-box">
			<div class="webmz-footer-ticket-box__main">
				<?php if ( $show_avatars ) : ?>
					<div class="webmz-footer-ticket-box__avatars" aria-hidden="true">
						<?php
						$this->render_avatar_image(
							$settings['avatar_back'] ?? array(),
							'webmz-footer-ticket-box__avatar webmz-footer-ticket-box__avatar--back',
							$avatar_size
						);
						$this->render_avatar_image(
							$settings['avatar_front'] ?? array(),
							'webmz-footer-ticket-box__avatar webmz-footer-ticket-box__avatar--front',
							$avatar_size
						);
						?>
					</div>
				<?php endif; ?>

				<div class="webmz-footer-ticket-box__content">
					<?php if ( ! empty( $settings['title'] ) ) : ?>
						<<?php echo esc_html( $title_tag ); ?> class="webmz-footer-ticket-box__title"><?php echo esc_html( $settings['title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
					<?php endif; ?>

					<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
						<p class="webmz-footer-ticket-box__subtitle"><?php echo esc_html( $settings['subtitle'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( ! empty( $settings['button_text'] ) ) : ?>
				<div class="webmz-footer-ticket-box__action">
					<a <?php echo $this->get_render_attribute_string( 'button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo esc_html( $settings['button_text'] ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
