<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;

class Tadris_View_All_V2_Widget extends Widget_Base {

    public function get_name() {
        return 'tadris_view_all_v2';
    }

    public function get_title() {
        return 'مشاهده همه ورژن ۲';
    }

    public function get_icon() {
        return 'eicon-button';
    }

    public function get_categories() {
        return [ 'webmz-widgets' ];
    }

    public function get_keywords() {
        return [ 'view all', 'button', 'مشاهده همه', 'ویدیوها', 'آکام' ];
    }

    protected function register_controls() {

        /*
        |--------------------------------------------------------------------------
        | Content
        |--------------------------------------------------------------------------
        */

        $this->start_controls_section(
            'section_content',
            [
                'label' => 'محتوا',
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label'       => 'متن دکمه',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'مشاهده همه ویدیوها',
                'placeholder' => 'مثلا: مشاهده همه ویدیوها',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'button_link',
            [
                'label'       => 'لینک',
                'type'        => Controls_Manager::URL,
                'placeholder' => 'https://example.com/videos',
                'default'     => [
                    'url' => '#',
                ],
                'show_external' => true,
            ]
        );

        $this->add_control(
            'selected_icon',
            [
                'label'   => 'آیکون',
                'type'    => Controls_Manager::ICONS,
                'default' => [
                    'value'   => 'fas fa-chevron-left',
                    'library' => 'fa-solid',
                ],
            ]
        );


        $this->add_control(
            'selected_icon_color_mode',
            [
                'label'       => 'نوع رنگ‌دهی آیکون SVG',
                'type'        => Controls_Manager::SELECT,
                'default'     => 'auto',
                'options'     => [
                    'auto'   => 'خودکار / بدون اجبار',
                    'stroke' => 'Stroke / خطی',
                    'fill'   => 'Fill / توپر',
                    'both'   => 'هر دو',
                ],
                'description' => 'برای SVGهای خطی Stroke و برای SVGهای توپر Fill را انتخاب کنید.',
            ]
        );
        $this->add_control(
            'icon_position',
            [
                'label'   => 'موقعیت آیکون',
                'type'    => Controls_Manager::SELECT,
                'default' => 'after',
                'options' => [
                    'after'  => 'بعد از متن',
                    'before' => 'قبل از متن',
                ],
            ]
        );

        $this->add_control(
            'button_alignment',
            [
                'label'   => 'چیدمان',
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => 'راست',
                        'icon'  => 'eicon-h-align-right',
                    ],
                    'center' => [
                        'title' => 'وسط',
                        'icon'  => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => 'چپ',
                        'icon'  => 'eicon-h-align-left',
                    ],
                ],
                'default'   => 'flex-start',
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-button' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_width_type',
            [
                'label'   => 'نوع عرض',
                'type'    => Controls_Manager::SELECT,
                'default' => 'inline',
                'options' => [
                    'inline' => 'اندازه محتوا',
                    'full'   => 'تمام عرض',
                ],
            ]
        );

        $this->end_controls_section();

        /*
        |--------------------------------------------------------------------------
        | Button Style
        |--------------------------------------------------------------------------
        */

        $this->start_controls_section(
            'section_button_style',
            [
                'label' => 'استایل دکمه',
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'button_typography',
                'label'    => 'تایپوگرافی',
                'selector' => '{{WRAPPER}} .tadris-view-all-button-link',
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label'      => 'فاصله داخلی',
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', 'rem' ],
                'default'    => [
                    'top'      => 10,
                    'right'    => 16,
                    'bottom'   => 10,
                    'left'     => 12,
                    'unit'     => 'px',
                    'isLinked' => false,
                ],
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-button-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_gap',
            [
                'label' => 'فاصله متن و آیکون',
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 40,
                    ],
                ],
                'default' => [
                    'size' => 8,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-button-link' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_radius',
            [
                'label'      => 'گردی گوشه‌ها',
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'default'    => [
                    'top'      => 999,
                    'right'    => 999,
                    'bottom'   => 999,
                    'left'     => 999,
                    'unit'     => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-button-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'button_border',
                'label'    => 'بوردر',
                'selector' => '{{WRAPPER}} .tadris-view-all-button-link',
            ]
        );

        $this->start_controls_tabs( 'button_style_tabs' );

        $this->start_controls_tab(
            'button_normal_tab',
            [
                'label' => 'عادی',
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label'     => 'رنگ متن',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#111827',
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-button-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label'     => 'رنگ پس‌زمینه',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#F3F4F6',
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-button-link' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover_tab',
            [
                'label' => 'هاور',
            ]
        );

        $this->add_control(
            'button_hover_text_color',
            [
                'label'     => 'رنگ متن هاور',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-button-link:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_background_color',
            [
                'label'     => 'رنگ پس‌زمینه هاور',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#111827',
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-button-link:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_hover_translate_y',
            [
                'label' => 'حرکت هاور',
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -20,
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'size' => -2,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-button-link:hover' => 'transform: translateY({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        /*
        |--------------------------------------------------------------------------
        | Icon Style
        |--------------------------------------------------------------------------
        */

        $this->start_controls_section(
            'section_icon_style',
            [
                'label' => 'استایل آیکون',
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'icon_box_size',
            [
                'label' => 'اندازه باکس آیکون',
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 16,
                        'max' => 80,
                    ],
                ],
                'default' => [
                    'size' => 32,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => 'اندازه خود آیکون',
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 8,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 18,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-icon i'   => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .tadris-view-all-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_radius',
            [
                'label'      => 'گردی باکس آیکون',
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'default'    => [
                    'top'      => 999,
                    'right'    => 999,
                    'bottom'   => 999,
                    'left'     => 999,
                    'unit'     => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'icon_style_tabs' );

        $this->start_controls_tab(
            'icon_normal_tab',
            [
                'label' => 'عادی',
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'     => 'رنگ آیکون',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#111827',
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_background_color',
            [
                'label'     => 'پس‌زمینه آیکون',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'icon_hover_tab',
            [
                'label' => 'هاور',
            ]
        );

        $this->add_control(
            'icon_hover_color',
            [
                'label'     => 'رنگ آیکون هاور',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#111827',
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-button-link:hover .tadris-view-all-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_hover_background_color',
            [
                'label'     => 'پس‌زمینه آیکون هاور',
                'type'      => Controls_Manager::COLOR,
                'default'   => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-button-link:hover .tadris-view-all-icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_hover_translate_x',
            [
                'label' => 'حرکت آیکون در هاور',
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -20,
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'size' => -3,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .tadris-view-all-button-link:hover .tadris-view-all-icon' => 'transform: translateX({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }


    protected function get_icon_color_mode_class( $settings ) {
        if ( function_exists( '\WebMZ\Elementor\webmz_get_icon_color_mode_class' ) ) {
            return \WebMZ\Elementor\webmz_get_icon_color_mode_class( $settings, 'selected_icon_color_mode' );
        }

        $mode = ! empty( $settings['selected_icon_color_mode'] ) ? sanitize_key( $settings['selected_icon_color_mode'] ) : 'auto';
        if ( ! in_array( $mode, [ 'auto', 'stroke', 'fill', 'both' ], true ) ) {
            $mode = 'auto';
        }

        return 'webmz-icon-color-mode-' . $mode;
    }

    protected function render_icon( $settings ) {
        if ( ! empty( $settings['selected_icon']['value'] ) ) {
            Icons_Manager::render_icon(
                $settings['selected_icon'],
                [
                    'aria-hidden' => 'true',
                ]
            );
        } else {
            ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M15 6l-6 6l6 6"/>
            </svg>
            <?php
        }
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $button_text = ! empty( $settings['button_text'] ) ? $settings['button_text'] : 'مشاهده همه';

        $this->add_render_attribute( 'button', 'class', 'tadris-view-all-button-link' );

        if ( ! empty( $settings['button_link']['url'] ) ) {
            $this->add_link_attributes( 'button', $settings['button_link'] );
        } else {
            $this->add_render_attribute( 'button', 'href', '#' );
        }

        if ( 'full' === $settings['button_width_type'] ) {
            $this->add_render_attribute( 'button', 'class', 'tadris-view-all-button-full' );
        }

        $icon_position = ! empty( $settings['icon_position'] ) ? $settings['icon_position'] : 'after';
        ?>

        <div class="tadris-view-all-button">
            <a <?php echo $this->get_render_attribute_string( 'button' ); ?>>
                
                <?php if ( 'before' === $icon_position ) : ?>
                    <span class="tadris-view-all-icon <?php echo esc_attr( $this->get_icon_color_mode_class( $settings ) ); ?>">
                        <?php $this->render_icon( $settings ); ?>
                    </span>
                <?php endif; ?>

                <span class="tadris-view-all-text">
                    <?php echo esc_html( $button_text ); ?>
                </span>

                <?php if ( 'after' === $icon_position ) : ?>
                    <span class="tadris-view-all-icon <?php echo esc_attr( $this->get_icon_color_mode_class( $settings ) ); ?>">
                        <?php $this->render_icon( $settings ); ?>
                    </span>
                <?php endif; ?>

            </a>
        </div>

        <?php
    }
}