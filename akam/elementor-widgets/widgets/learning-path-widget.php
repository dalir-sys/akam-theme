<?php
/**
 * Learning path roadmap widget.
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
 * Learning path with customizable steps and arrows.
 */
class Learning_Path_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-learning-path';
	}

	public function get_title() {
		return esc_html__( 'مسیر یادگیری', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-sitemap';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'learning', 'path', 'roadmap', 'steps', 'مسیر', 'یادگیری', 'مراحل' );
	}

	public function get_style_depends() {
		return array( 'webmz-learning-path' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_layout_controls();
		$this->register_style_controls();
	}

	protected function register_content_controls() {
		$this->start_controls_section(
			'section_header',
			array(
				'label' => esc_html__( 'عنوان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'       => esc_html__( 'متن عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'مسیر یادگیری فرانت اند', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_header_lines',
			array(
				'label'        => esc_html__( 'نمایش خطوط تزئینی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->webmz_register_title_tag_control( 'heading_tag', esc_html__( 'تگ HTML عنوان', 'tadris' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_steps',
			array(
				'label' => esc_html__( 'مراحل', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon_source',
			array(
				'label'   => esc_html__( 'نوع آیکون', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => array(
					'image' => esc_html__( 'تصویر', 'tadris' ),
					'icon'  => esc_html__( 'آیکون المنتور', 'tadris' ),
				),
			)
		);

		$repeater->add_control(
			'icon_image',
			array(
				'label'     => esc_html__( 'تصویر آیکون', 'tadris' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'icon_source' => 'image' ),
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label'     => esc_html__( 'آیکون', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fab fa-html5',
					'library' => 'fa-brands',
				),
				'condition' => array( 'icon_source' => 'icon' ),
			)
		);

		$repeater->add_control(
			'icon_color_heading',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'icon_source' => 'icon' ),
			)
		);

		$repeater->add_control(
			'icon_color',
			array(
				'label'       => esc_html__( 'رنگ', 'tadris' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'در صورت خالی بودن، از رنگ پیش‌فرض تب استایل استفاده می‌شود.', 'tadris' ),
				'condition'   => array( 'icon_source' => 'icon' ),
			)
		);

		$repeater->add_control(
			'icon_color_mode',
			array(
				'label'       => esc_html__( 'نوع رنگ‌دهی', 'tadris' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'fill',
				'options'     => array(
					'auto'   => esc_html__( 'خودکار / بدون اجبار', 'tadris' ),
					'stroke' => esc_html__( 'Stroke / خطی', 'tadris' ),
					'fill'   => esc_html__( 'Fill / توپر', 'tadris' ),
					'both'   => esc_html__( 'هر دو', 'tadris' ),
				),
				'description' => esc_html__( 'برای آیکون‌های خطی Stroke و برای آیکون‌های توپر Fill را انتخاب کنید.', 'tadris' ),
				'condition'   => array( 'icon_source' => 'icon' ),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'نام مرحله', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'HTML CSS', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label' => esc_html__( 'لینک (اختیاری)', 'tadris' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$repeater->add_control(
			'arrow_heading',
			array(
				'label'     => esc_html__( 'فلش بعد از این مرحله', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'show_arrow',
			array(
				'label'        => esc_html__( 'نمایش فلش', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$repeater->add_control(
			'arrow_image',
			array(
				'label'       => esc_html__( 'تصویر فلش سفارشی', 'tadris' ),
				'type'        => Controls_Manager::MEDIA,
				'description' => esc_html__( 'در صورت خالی بودن، از فلش پیش‌فرض استفاده می‌شود.', 'tadris' ),
				'condition'   => array( 'show_arrow' => 'yes' ),
			)
		);

		$repeater->add_control(
			'arrow_style',
			array(
				'label'     => esc_html__( 'نوع فلش پیش‌فرض', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'down',
				'options'   => array(
					'down' => esc_html__( 'منحنی پایین', 'tadris' ),
					'up'   => esc_html__( 'منحنی بالا', 'tadris' ),
				),
				'condition' => array(
					'show_arrow' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'arrow_text',
			array(
				'label'       => esc_html__( 'متن روی فلش', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'condition'   => array( 'show_arrow' => 'yes' ),
			)
		);

		$this->add_control(
			'steps',
			array(
				'label'       => esc_html__( 'لیست مراحل', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'title'           => 'HTML CSS',
						'icon_source'     => 'icon',
						'icon'            => array( 'value' => 'fab fa-html5', 'library' => 'fa-brands' ),
						'icon_color_mode' => 'fill',
						'show_arrow'      => 'yes',
						'arrow_style'     => 'down',
						'arrow_text'      => esc_html__( 'گام اول', 'tadris' ),
					),
					array(
						'title'           => esc_html__( 'جاوا اسکریپت', 'tadris' ),
						'icon_source'     => 'icon',
						'icon'            => array( 'value' => 'fab fa-js', 'library' => 'fa-brands' ),
						'icon_color_mode' => 'fill',
						'show_arrow'      => 'yes',
						'arrow_style'     => 'up',
						'arrow_text'      => esc_html__( 'گام دوم', 'tadris' ),
					),
					array(
						'title'           => esc_html__( 'گیت', 'tadris' ),
						'icon_source'     => 'icon',
						'icon'            => array( 'value' => 'fab fa-git-alt', 'library' => 'fa-brands' ),
						'icon_color_mode' => 'fill',
						'show_arrow'      => 'yes',
						'arrow_style'     => 'down',
						'arrow_text'      => esc_html__( 'گام سوم', 'tadris' ),
					),
					array(
						'title'           => esc_html__( 'ریکت', 'tadris' ),
						'icon_source'     => 'icon',
						'icon'            => array( 'value' => 'fab fa-react', 'library' => 'fa-brands' ),
						'icon_color_mode' => 'fill',
						'show_arrow'      => 'yes',
						'arrow_style'     => 'up',
						'arrow_text'      => esc_html__( 'گام چهارم', 'tadris' ),
					),
					array(
						'title'           => esc_html__( 'تیلویند', 'tadris' ),
						'icon_source'     => 'icon',
						'icon'            => array( 'value' => 'fas fa-wind', 'library' => 'fa-solid' ),
						'icon_color_mode' => 'fill',
						'show_arrow'      => 'yes',
						'arrow_style'     => 'down',
						'arrow_text'      => esc_html__( 'گام پنجم', 'tadris' ),
					),
					array(
						'title'           => esc_html__( 'نکست', 'tadris' ),
						'icon_source'     => 'icon',
						'icon'            => array( 'value' => 'fas fa-n', 'library' => 'fa-solid' ),
						'icon_color_mode' => 'fill',
						'show_arrow'      => '',
					),
				),
			)
		);

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
			'icon_size',
			array(
				'label'      => esc_html__( 'اندازه باکس آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 48, 'max' => 120 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 72 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-lp' => '--webmz-lp-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_width',
			array(
				'label'      => esc_html__( 'عرض فلش', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 32, 'max' => 160 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 88 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-lp' => '--webmz-lp-arrow-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_radius',
			array(
				'label'      => esc_html__( 'گردی باکس آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 18 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-lp__icon-box' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'track_gap',
			array(
				'label'      => esc_html__( 'فاصله بین مراحل', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 0 ),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-lp__track' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'style_colors',
			array(
				'label' => esc_html__( 'رنگ‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه باکس آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-lp' => '--webmz-lp-icon-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'       => esc_html__( 'رنگ پیش‌فرض آیکون', 'tadris' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#2d2e5f',
				'description' => esc_html__( 'برای مراحلی که رنگ اختصاصی ندارند اعمال می‌شود.', 'tadris' ),
				'selectors'   => array(
					'{{WRAPPER}} .webmz-lp' => '--webmz-lp-icon-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان مرحله', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2d2e5f',
				'selectors' => array(
					'{{WRAPPER}} .webmz-lp' => '--webmz-lp-label-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'رنگ فلش', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#c5c8d8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-lp' => '--webmz-lp-arrow-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_line_color',
			array(
				'label'     => esc_html__( 'رنگ خطوط عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#c8c4e8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-lp' => '--webmz-lp-header-line: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls(
			'style_heading',
			esc_html__( 'عنوان اصلی', 'tadris' ),
			'.webmz-lp__title',
			'#2d2e5f'
		);

		$this->start_controls_section(
			'style_step_label',
			array(
				'label' => esc_html__( 'نام مراحل', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'step_label_typography',
				'selector' => '{{WRAPPER}} .webmz-lp__label',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_arrow_text',
			array(
				'label' => esc_html__( 'متن فلش', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'arrow_text_typography',
				'selector' => '{{WRAPPER}} .webmz-lp__arrow-text',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get media URL from Elementor media control value.
	 *
	 * @param array<string,mixed> $media Media control value.
	 * @return string
	 */
	private function get_media_url( $media ) {
		if ( empty( $media ) || ! is_array( $media ) ) {
			return '';
		}

		return ! empty( $media['url'] ) ? (string) $media['url'] : '';
	}

	/**
	 * Get inline SVG markup for default arrows.
	 *
	 * @param string $style Arrow style slug.
	 * @return string
	 */
	private function get_default_arrow_svg( $style ) {
		if ( 'up' === $style ) {
			return '<svg class="webmz-lp__arrow-default" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 48" fill="none" aria-hidden="true"><path d="M6 32C30 44 54 20 78 32C94 40 100 36 106 30" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M106 24L118 30L106 36Z" fill="currentColor"/></svg>';
		}

		return '<svg class="webmz-lp__arrow-default" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 48" fill="none" aria-hidden="true"><path d="M6 16C30 4 54 28 78 16C94 8 100 12 106 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M106 12L118 18L106 24Z" fill="currentColor"/></svg>';
	}

	/**
	 * Get icon color-mode class for a single step.
	 *
	 * @param array<string,mixed> $item Step settings.
	 * @return string
	 */
	private function get_step_icon_color_mode_class( $item ) {
		if ( empty( $item['icon_color_mode'] ) ) {
			$item['icon_color_mode'] = 'fill';
		}

		return webmz_get_icon_color_mode_class( $item, 'icon_color_mode' );
	}

	/**
	 * Get icon box class list for a single step.
	 *
	 * @param array<string,mixed> $item Step settings.
	 * @return string
	 */
	private function get_step_icon_box_class( $item ) {
		$classes = array(
			'webmz-lp__icon-box',
			$this->get_step_icon_color_mode_class( $item ),
		);

		if ( $this->get_step_icon_color_value( $item ) ) {
			$classes[] = 'webmz-lp__icon-box--has-color';
		}

		return implode( ' ', $classes );
	}

	/**
	 * Get sanitized icon color from step settings.
	 *
	 * @param array<string,mixed> $item Step settings.
	 * @return string
	 */
	private function get_step_icon_color_value( $item ) {
		if ( empty( $item['icon_color'] ) ) {
			return '';
		}

		return sanitize_hex_color( (string) $item['icon_color'] ) ?: (string) $item['icon_color'];
	}

	/**
	 * Get inline color style for a single step icon box.
	 *
	 * @param array<string,mixed> $item Step settings.
	 * @return string
	 */
	private function get_step_icon_color_style( $item ) {
		$color = $this->get_step_icon_color_value( $item );

		if ( ! $color ) {
			return '';
		}

		return '--webmz-lp-step-icon-color:' . esc_attr( $color ) . ';color:' . esc_attr( $color ) . ';';
	}

	/**
	 * Render step icon markup.
	 *
	 * @param array<string,mixed> $item       Step settings.
	 * @param string              $icon_class Icon color mode class.
	 * @return void
	 */
	private function render_step_icon( $item, $icon_class ) {
		$source    = ! empty( $item['icon_source'] ) ? (string) $item['icon_source'] : 'image';
		$image_url = $this->get_media_url( $item['icon_image'] ?? array() );

		if ( 'image' === $source && $image_url ) {
			?>
			<img src="<?php echo esc_url( $image_url ); ?>" alt="" loading="lazy" decoding="async" />
			<?php
			return;
		}

		if ( 'icon' === $source && ! empty( $item['icon']['value'] ) ) {
			Icons_Manager::render_icon(
				$item['icon'],
				array(
					'class'       => $icon_class,
					'aria-hidden' => 'true',
				)
			);
		}
	}

	/**
	 * Render arrow between steps.
	 *
	 * @param array<string,mixed> $item Step settings.
	 * @return void
	 */
	private function render_arrow( $item ) {
		if ( empty( $item['show_arrow'] ) || 'yes' !== $item['show_arrow'] ) {
			return;
		}

		$style      = ! empty( $item['arrow_style'] ) && 'up' === $item['arrow_style'] ? 'up' : 'down';
		$arrow_url  = $this->get_media_url( $item['arrow_image'] ?? array() );
		$arrow_text = ! empty( $item['arrow_text'] ) ? trim( (string) $item['arrow_text'] ) : '';
		?>
		<div class="webmz-lp__arrow webmz-lp__arrow--<?php echo esc_attr( $style ); ?>" aria-hidden="true">
			<?php if ( $arrow_text ) : ?>
				<span class="webmz-lp__arrow-text"><?php echo esc_html( $arrow_text ); ?></span>
			<?php endif; ?>
			<?php if ( $arrow_url ) : ?>
				<img class="webmz-lp__arrow-media" src="<?php echo esc_url( $arrow_url ); ?>" alt="" loading="lazy" decoding="async" />
			<?php else : ?>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inline trusted SVG markup.
				echo $this->get_default_arrow_svg( $style );
				?>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function render() {
		$settings    = $this->get_settings_for_display();
		$steps       = ! empty( $settings['steps'] ) && is_array( $settings['steps'] ) ? $settings['steps'] : array();
		$heading     = ! empty( $settings['heading'] ) ? trim( (string) $settings['heading'] ) : '';
		$heading_tag = $this->webmz_get_title_tag( $settings, 'heading_tag' );
		$show_lines  = ! empty( $settings['show_header_lines'] ) && 'yes' === $settings['show_header_lines'];
		$total       = count( $steps );

		if ( empty( $steps ) ) {
			return;
		}
		?>
		<div class="webmz-lp">
			<?php if ( $heading ) : ?>
				<header class="webmz-lp__header">
					<?php if ( $show_lines ) : ?>
						<span class="webmz-lp__header-line" aria-hidden="true"></span>
					<?php endif; ?>
					<<?php echo esc_html( $heading_tag ); ?> class="webmz-lp__title"><?php echo esc_html( $heading ); ?></<?php echo esc_html( $heading_tag ); ?>>
					<?php if ( $show_lines ) : ?>
						<span class="webmz-lp__header-line" aria-hidden="true"></span>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<div class="webmz-lp__track">
				<?php foreach ( $steps as $index => $item ) : ?>
					<?php
					$title         = ! empty( $item['title'] ) ? trim( (string) $item['title'] ) : '';
					$has_link      = ! empty( $item['link']['url'] );
					$link_key      = 'lp_step_link_' . absint( $index );
					$icon_class = $this->get_step_icon_color_mode_class( $item );
					$icon_style = $this->get_step_icon_color_style( $item );
					$icon_box_class = $this->get_step_icon_box_class( $item );

					if ( $has_link ) {
						$this->add_link_attributes( $link_key, $item['link'] );
					}
					?>
					<div class="webmz-lp__step">
						<?php if ( $has_link ) : ?>
							<a class="webmz-lp__step-link" <?php echo $this->get_render_attribute_string( $link_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php endif; ?>

						<span class="<?php echo esc_attr( $icon_box_class ); ?>"<?php echo $icon_style ? ' style="' . esc_attr( $icon_style ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php $this->render_step_icon( $item, $icon_class ); ?>
						</span>

						<?php if ( $title ) : ?>
							<span class="webmz-lp__label"><?php echo esc_html( $title ); ?></span>
						<?php endif; ?>

						<?php if ( $has_link ) : ?>
							</a>
						<?php endif; ?>
					</div>

					<?php if ( $index < $total - 1 ) : ?>
						<?php $this->render_arrow( $item ); ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
