<?php
/**
 * Post download box widget for WebMZ template builder layouts.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Display downloadable files from the current post meta box.
 */
class Post_Download_Box_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-post-download-box';
	}

	public function get_title() {
		return esc_html__( 'باکس دانلود', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-download-button';
	}

	public function get_categories() {
		return array( 'webmz-dynamic-widgets' );
	}

	public function get_keywords() {
		return array( 'download', 'file', 'دانلود', 'فایل', 'باکس' );
	}

	/**
	 * Whether download links should require login.
	 *
	 * @return bool
	 */
	protected function requires_login_for_download() {
		if ( is_user_logged_in() || ! function_exists( 'webmz_otp_get_settings' ) || ! function_exists( 'webmz_otp_is_enabled' ) ) {
			return false;
		}

		$settings = webmz_otp_get_settings();

		return webmz_otp_is_enabled() && 'yes' === $settings['require_login_download'];
	}

	public function get_script_depends() {
		if ( ! $this->requires_login_for_download() ) {
			return array();
		}

		return array( 'webmz-tadris-widgets' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'show_header',
			array(
				'label'        => esc_html__( 'نمایش هدر', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'نمایش', 'tadris' ),
				'label_off'    => esc_html__( 'مخفی', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'header_title',
			array(
				'label'     => esc_html__( 'عنوان', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'باکس دانلود', 'tadris' ),
				'condition' => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->add_control(
			'header_subtitle',
			array(
				'label'     => esc_html__( 'زیرعنوان', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'دانلود ویدیو ها و فایل های این دوره', 'tadris' ),
				'condition' => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->add_control(
			'header_icon',
			array(
				'label'     => esc_html__( 'آیکون هدر', 'tadris' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-download',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->webmz_register_icon_color_mode_control(
			'header_icon_color_mode',
			esc_html__( 'نوع رنگ‌دهی آیکون هدر', 'tadris' ),
			array( 'show_header' => 'yes' )
		);

		$this->add_control(
			'download_button_text',
			array(
				'label'       => esc_html__( 'متن دکمه دانلود', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دانلود به صورت مستقیم', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'meta_extension_format',
			array(
				'label'       => esc_html__( 'قالب پسوند فایل', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'فایل {{extension}}', 'tadris' ),
				'description' => esc_html__( 'از {{extension}} برای جایگذاری پسوند فایل استفاده کنید.', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'meta_size_format',
			array(
				'label'       => esc_html__( 'قالب حجم فایل', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'حجم {{size}}', 'tadris' ),
				'description' => esc_html__( 'از {{size}} برای جایگذاری حجم فایل استفاده کنید.', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_box',
			array(
				'label' => esc_html__( 'باکس', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'box_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-download-box' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => '24',
					'right'  => '24',
					'bottom' => '24',
					'left'   => '24',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-download-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'box_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 14,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-download-box' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'box_border_color',
			array(
				'label'     => esc_html__( 'رنگ حاشیه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15, 23, 42, 0.12)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-download-box' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'box_border_width',
			array(
				'label'      => esc_html__( 'ضخامت حاشیه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 1,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-download-box' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_header',
			array(
				'label'     => esc_html__( 'هدر', 'tadris' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_header' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'header_gap',
			array(
				'label'      => esc_html__( 'فاصله بین آیکون و متن', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 14,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-download-box__header' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_bottom_spacing',
			array(
				'label'      => esc_html__( 'فاصله زیر هدر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-download-box__header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'header_icon_heading',
			array(
				'label'     => esc_html__( 'آیکون', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'header_icon_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 48,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-download-box__header-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-download-box__header-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_icon_box_size',
			array(
				'label'      => esc_html__( 'اندازه باکس آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 32,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 48,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-download-box__header-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'header_icon_bg',
			array(
				'label'     => esc_html__( 'پس‌زمینه آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0878f9',
				'selectors' => array(
					'{{WRAPPER}} .webmz-download-box__header-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-download-box__header-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_icon_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 24,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-download-box__header-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'header_title_heading',
			array(
				'label'     => esc_html__( 'عنوان', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'header_title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1e293b',
				'selectors' => array(
					'{{WRAPPER}} .webmz-download-box__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'header_title_typography',
				'selector' => '{{WRAPPER}} .webmz-download-box__title',
			)
		);

		$this->add_control(
			'header_subtitle_heading',
			array(
				'label'     => esc_html__( 'زیرعنوان', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'header_subtitle_color',
			array(
				'label'     => esc_html__( 'رنگ زیرعنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#94a3b8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-download-box__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'header_subtitle_typography',
				'selector' => '{{WRAPPER}} .webmz-download-box__subtitle',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_row',
			array(
				'label' => esc_html__( 'ردیف فایل', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'row_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f4f7fa',
				'selectors' => array(
					'{{WRAPPER}} .webmz-download-box__item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'row_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => '16',
					'right'  => '20',
					'bottom' => '16',
					'left'   => '20',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-download-box__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'row_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-download-box__item' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => esc_html__( 'فاصله بین ردیف‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-download-box__list' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'row_inner_gap',
			array(
				'label'      => esc_html__( 'فاصله بین عناصر ردیف', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 16,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-download-box__item' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_name',
			array(
				'label' => esc_html__( 'نام فایل', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1e293b',
				'selectors' => array(
					'{{WRAPPER}} .webmz-download-box__item-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .webmz-download-box__item-name',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_meta',
			array(
				'label' => esc_html__( 'جزئیات فایل', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#64748b',
				'selectors' => array(
					'{{WRAPPER}} .webmz-download-box__item-meta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_typography',
				'selector' => '{{WRAPPER}} .webmz-download-box__item-meta-part',
			)
		);

		$this->add_control(
			'meta_separator_color',
			array(
				'label'     => esc_html__( 'رنگ جداکننده', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(100, 116, 139, 0.35)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-download-box__item-meta-sep' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_button',
			array(
				'label' => esc_html__( 'دکمه دانلود', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0878f9',
				'selectors' => array(
					'{{WRAPPER}} .webmz-download-box__item-btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-download-box__item-btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg_hover',
			array(
				'label'     => esc_html__( 'پس‌زمینه (هاور)', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0666d4',
				'selectors' => array(
					'{{WRAPPER}} .webmz-download-box__item-btn:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_color_hover',
			array(
				'label'     => esc_html__( 'رنگ متن (هاور)', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-download-box__item-btn:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .webmz-download-box__item-btn',
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => '12',
					'right'  => '20',
					'bottom' => '12',
					'left'   => '20',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-download-box__item-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-download-box__item-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Sample items for layout editor preview.
	 *
	 * @return array<int,array{name:string,size:string,extension:string,link:string}>
	 */
	protected function get_preview_items() {
		return array(
			array(
				'name'      => esc_html__( 'فایل ویدیوی آموزشی این دوره', 'tadris' ),
				'size'      => esc_html__( '260 مگابایت', 'tadris' ),
				'extension' => 'MKV',
				'link'      => '#',
			),
			array(
				'name'      => esc_html__( 'فایل ویدیوی آموزشی این دوره', 'tadris' ),
				'size'      => esc_html__( '260 مگابایت', 'tadris' ),
				'extension' => 'MKV',
				'link'      => '#',
			),
			array(
				'name'      => esc_html__( 'فایل ویدیوی آموزشی این دوره', 'tadris' ),
				'size'      => esc_html__( '260 مگابایت', 'tadris' ),
				'extension' => 'MKV',
				'link'      => '#',
			),
		);
	}

	/**
	 * Resolve download items for current context.
	 *
	 * @return array<int,array{name:string,size:string,extension:string,link:string}>
	 */
	protected function get_download_items() {
		if ( \webmz_is_layout_editing_context() ) {
			return $this->get_preview_items();
		}

		$post_id = \webmz_get_context_post_id();

		if ( ! $post_id ) {
			return array();
		}

		return webmz_get_post_downloads( $post_id );
	}

	/**
	 * Build meta parts for a download row.
	 *
	 * @param array<string,string> $item     Download item.
	 * @param array<string,mixed>  $settings Widget settings.
	 * @return array<int,string>
	 */
	protected function get_item_meta_parts( $item, $settings ) {
		$extension = isset( $item['extension'] ) ? trim( (string) $item['extension'] ) : '';
		$size      = isset( $item['size'] ) ? trim( (string) $item['size'] ) : '';
		$parts     = array();

		if ( '' !== $extension ) {
			$extension_format = ! empty( $settings['meta_extension_format'] )
				? (string) $settings['meta_extension_format']
				: esc_html__( 'فایل {{extension}}', 'tadris' );

			$parts[] = trim( str_replace( '{{extension}}', $extension, $extension_format ) );
		}

		if ( '' !== $size ) {
			$size_format = ! empty( $settings['meta_size_format'] )
				? (string) $settings['meta_size_format']
				: esc_html__( 'حجم {{size}}', 'tadris' );

			$parts[] = trim( str_replace( '{{size}}', $size, $size_format ) );
		}

		return array_values( array_filter( $parts ) );
	}

	/**
	 * Render widget header.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	protected function render_header( $settings ) {
		if ( empty( $settings['show_header'] ) || 'yes' !== $settings['show_header'] ) {
			return;
		}
		?>
		<div class="webmz-download-box__header">
			<div class="webmz-download-box__header-icon <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $settings, 'header_icon_color_mode' ) ); ?>">
				<?php Icons_Manager::render_icon( $settings['header_icon'], array( 'aria-hidden' => 'true' ) ); ?>
			</div>
			<div class="webmz-download-box__header-text">
				<?php if ( ! empty( $settings['header_title'] ) ) : ?>
					<h2 class="webmz-download-box__title"><?php echo esc_html( $settings['header_title'] ); ?></h2>
				<?php endif; ?>
				<?php if ( ! empty( $settings['header_subtitle'] ) ) : ?>
					<p class="webmz-download-box__subtitle"><?php echo esc_html( $settings['header_subtitle'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	protected function render() {
		$items = $this->get_download_items();

		if ( empty( $items ) ) {
			return;
		}

		$settings    = $this->get_settings_for_display();
		$button_text = ! empty( $settings['download_button_text'] ) ? $settings['download_button_text'] : esc_html__( 'دانلود به صورت مستقیم', 'tadris' );
		$guard_links = $this->requires_login_for_download();
		?>
		<div class="webmz-download-box">
			<?php $this->render_header( $settings ); ?>

			<div class="webmz-download-box__list">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php
					$meta_parts = $this->get_item_meta_parts( $item, $settings );
					$link_key   = 'download_link_' . $index;
					$link_url   = $guard_links ? '#' : $item['link'];
					$this->add_link_attributes( $link_key, array( 'url' => $link_url ) );
					$this->add_render_attribute( $link_key, 'class', 'webmz-download-box__item-btn' );
					if ( $guard_links ) {
						$this->add_render_attribute( $link_key, 'data-webmz-download-guard', 'yes' );
					}
					?>
					<div class="webmz-download-box__item">
						<strong class="webmz-download-box__item-name"><?php echo esc_html( $item['name'] ); ?></strong>
						<?php if ( ! empty( $meta_parts ) ) : ?>
							<span class="webmz-download-box__item-meta">
								<?php foreach ( $meta_parts as $part_index => $part_text ) : ?>
									<?php if ( $part_index > 0 ) : ?>
										<span class="webmz-download-box__item-meta-sep" aria-hidden="true"></span>
									<?php endif; ?>
									<span class="webmz-download-box__item-meta-part"><?php echo esc_html( $part_text ); ?></span>
								<?php endforeach; ?>
							</span>
						<?php endif; ?>
						<a <?php echo $this->get_render_attribute_string( $link_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php echo esc_html( $button_text ); ?>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
