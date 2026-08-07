<?php
/**
 * Elementor AJAX search popup widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Search trigger that opens a popup with live AJAX search results.
 */
class Ajax_Search_Popup_Widget extends Widget_Base {

	public function get_name() {
		return 'webmz-ajax-search-popup';
	}

	public function get_title() {
		return esc_html__( 'جستجوی پاپ‌آپ آکام', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-search-bold';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'search', 'ajax', 'popup', 'جستجو', 'پاپ آپ', 'محصول', 'مقاله' );
	}

	public function get_style_depends() {
		return array( 'webmz-ajax-search', 'webmz-ajax-search-popup' );
	}

	public function get_script_depends() {
		return array( 'webmz-ajax-search', 'webmz-ajax-search-popup' );
	}

	/**
	 * Register widget editor controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_trigger',
			array(
				'label' => esc_html__( 'دکمه جستجو', 'tadris' ),
			)
		);

		$this->add_control(
			'hide_button_text',
			array(
				'label'        => esc_html__( 'عدم نمایش متن دکمه', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'placeholder',
			array(
				'label'       => esc_html__( 'متن دکمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دنبال چی می‌گردی؟', 'tadris' ),
				'label_block' => true,
				'condition'   => array(
					'hide_button_text!' => 'yes',
				),
			)
		);

		$this->add_control(
			'search_icon',
			array(
				'label'   => esc_html__( 'آیکون جستجو', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-search',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'icon_color_mode',
			array(
				'label'       => esc_html__( 'نوع رنگ‌دهی آیکون SVG', 'tadris' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'auto',
				'options'     => array(
					'auto'   => esc_html__( 'خودکار / بدون اجبار', 'tadris' ),
					'stroke' => esc_html__( 'Stroke / خطی', 'tadris' ),
					'fill'   => esc_html__( 'Fill / توپر', 'tadris' ),
					'both'   => esc_html__( 'هر دو', 'tadris' ),
				),
				'description' => esc_html__( 'برای SVGهای خطی Stroke و برای SVGهای توپر Fill را انتخاب کنید.', 'tadris' ),
			)
		);

		$this->add_control(
			'button_label',
			array(
				'label'       => esc_html__( 'برچسب دسترس‌پذیری', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'باز کردن جستجو', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_search',
			array(
				'label' => esc_html__( 'جستجو', 'tadris' ),
			)
		);

		$this->add_control(
			'search_input_placeholder',
			array(
				'label'       => esc_html__( 'متن داخل کادر جستجو', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دنبال چی می‌گردید؟', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'products_limit',
			array(
				'label'   => esc_html__( 'تعداد محصولات', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8,
				'min'     => 1,
				'max'     => 12,
			)
		);

		$this->add_control(
			'posts_limit',
			array(
				'label'   => esc_html__( 'تعداد مقالات', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8,
				'min'     => 1,
				'max'     => 12,
			)
		);

		$this->add_control(
			'zhaket_style',
			array(
				'label'        => esc_html__( 'پاپ‌آپ مشابه ژاکت', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'zhaket_banner',
			array(
				'label'     => esc_html__( 'تصویر بنر', 'tadris' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(),
				'condition' => array(
					'zhaket_style' => 'yes',
				),
			)
		);

		$this->add_control(
			'zhaket_bestsellers_limit',
			array(
				'label'     => esc_html__( 'تعداد محصولات پرفروش', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3,
				'min'       => 1,
				'max'       => 6,
				'condition' => array(
					'zhaket_style' => 'yes',
				),
			)
		);

		$this->add_control(
			'zhaket_bestsellers_title',
			array(
				'label'       => esc_html__( 'عنوان محصولات پرفروش', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'محصولات پرفروش', 'tadris' ),
				'label_block' => true,
				'condition'   => array(
					'zhaket_style' => 'yes',
				),
			)
		);

		$this->add_control(
			'zhaket_popular_title',
			array(
				'label'       => esc_html__( 'عنوان جستجوهای محبوب', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'جستجوهای محبوب', 'tadris' ),
				'label_block' => true,
				'condition'   => array(
					'zhaket_style' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_result_content',
			array(
				'label' => esc_html__( 'بخش‌های نتیجه', 'tadris' ),
			)
		);

		$headings = array(
			'products_title' => esc_html__( 'محصولات مرتبط', 'tadris' ),
			'posts_title'    => esc_html__( 'مقالات مرتبط', 'tadris' ),
			'recent_title'   => esc_html__( 'آخرین جستجوهای شما', 'tadris' ),
			'popular_title'  => esc_html__( 'جستجوهای پرطرفدار', 'tadris' ),
		);

		foreach ( $headings as $key => $default ) {
			$this->add_control(
				$key,
				array(
					'label'       => $default,
					'type'        => Controls_Manager::TEXT,
					'default'     => $default,
					'label_block' => true,
				)
			);
		}

		$this->add_control(
			'show_recent',
			array(
				'label'        => esc_html__( 'نمایش آخرین جستجوها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_popular',
			array(
				'label'        => esc_html__( 'نمایش جستجوهای پرطرفدار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_trigger_style',
			array(
				'label' => esc_html__( 'دکمه جستجو', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'trigger_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-search-popup__trigger' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'trigger_border_color',
			array(
				'label'     => esc_html__( 'رنگ حاشیه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8e8e8',
				'selectors' => array(
					'{{WRAPPER}} .webmz-search-popup__trigger' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'trigger_height',
			array(
				'label'      => esc_html__( 'ارتفاع', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 40,
						'max' => 90,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 48,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-search-popup__trigger' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'trigger_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
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
					'size' => 999,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-search-popup__trigger' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'trigger_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-search-popup__trigger' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'hide_button_text!' => 'yes',
				),
			)
		);

		$this->add_control(
			'trigger_icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .webmz-search-popup__trigger-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'trigger_typography',
				'selector' => '{{WRAPPER}} .webmz-search-popup__trigger',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_popup_style',
			array(
				'label' => esc_html__( 'پاپ‌آپ', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'popup_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه پاپ‌آپ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-search-popup__dialog' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'popup_radius',
			array(
				'label'      => esc_html__( 'گردی پاپ‌آپ', 'tadris' ),
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
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-search-popup__dialog' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'popup_shadow',
				'selector' => '{{WRAPPER}} .webmz-search-popup__dialog',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_search_box_style',
			array(
				'label' => esc_html__( 'کادر جستجو', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'box_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه کادر', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f2f7fc',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ajax-search__form' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'box_border_color',
			array(
				'label'     => esc_html__( 'رنگ حاشیه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e5edf5',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ajax-search__form' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'input_color',
			array(
				'label'     => esc_html__( 'رنگ متن ورودی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-ajax-search__input' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'placeholder_color',
			array(
				'label'     => esc_html__( 'رنگ Placeholder', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-ajax-search__input::placeholder' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => esc_html__( 'رنگ پس‌زمینه دکمه جستجو', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-ajax-search__submit' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون دکمه جستجو', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ajax-search__submit' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان‌ها', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-ajax-search__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'chip_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه عبارت‌های پیشنهادی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-ajax-search__chip' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget markup.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( 'yes' === ( $settings['zhaket_style'] ?? '' ) ) {
			$this->render_zhaket_popup( $settings );
			return;
		}

		$this->render_default_popup( $settings );
	}

	/**
	 * Render the default animated search popup.
	 *
	 * @param array $settings Widget settings.
	 * @return void
	 */
	private function render_default_popup( $settings ) {
		$popular_terms = function_exists( '\webmz_get_popular_search_terms' )
			? \webmz_get_popular_search_terms()
			: array();

		$product_limit    = min( 12, max( 1, absint( $settings['products_limit'] ) ) );
		$post_limit       = min( 12, max( 1, absint( $settings['posts_limit'] ) ) );
		$input_id         = 'webmz-search-popup-input-' . $this->get_id();
		$popup_id         = 'webmz-search-popup-' . $this->get_id();
		$hide_button_text = 'yes' === ( $settings['hide_button_text'] ?? '' );
		$popup_classes    = array( 'webmz-search-popup', 'webmz-search-popup--boot' );

		if ( $hide_button_text ) {
			$popup_classes[] = 'webmz-search-popup--icon-only';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $popup_classes ) ); ?>" data-webmz-search-popup>
			<button
				type="button"
				class="webmz-search-popup__trigger"
				data-webmz-search-popup-trigger
				aria-haspopup="dialog"
				aria-controls="<?php echo esc_attr( $popup_id ); ?>"
				aria-label="<?php echo esc_attr( $settings['button_label'] ); ?>"
			>
				<?php if ( ! $hide_button_text ) : ?>
					<span class="webmz-search-popup__trigger-text"><?php echo esc_html( $settings['placeholder'] ); ?></span>
				<?php endif; ?>
				<span class="webmz-search-popup__trigger-icon <?php echo esc_attr( webmz_get_icon_color_mode_class( $settings ) ); ?>">
					<?php Icons_Manager::render_icon( $settings['search_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</span>
			</button>

			<div
				id="<?php echo esc_attr( $popup_id ); ?>"
				class="webmz-search-popup__overlay"
				data-webmz-search-popup-overlay
				role="dialog"
				aria-modal="true"
				aria-label="<?php echo esc_attr( $settings['button_label'] ); ?>"
				hidden
				style="display:none;opacity:0;visibility:hidden;pointer-events:none;"
			>
				<div class="webmz-search-popup__cosmos" aria-hidden="true">
					<span class="webmz-search-popup__nebula webmz-search-popup__nebula--1"></span>
					<span class="webmz-search-popup__nebula webmz-search-popup__nebula--2"></span>
					<span class="webmz-search-popup__nebula webmz-search-popup__nebula--3"></span>
					<span class="webmz-search-popup__stars"></span>
				</div>

				<div class="webmz-search-popup__shell">
					<div class="webmz-search-popup__aura" aria-hidden="true"></div>
					<div class="webmz-search-popup__dialog" data-webmz-search-popup-dialog>
					<button
						type="button"
						class="webmz-search-popup__close"
						data-webmz-search-popup-close
						aria-label="<?php esc_attr_e( 'بستن', 'tadris' ); ?>"
					>
						<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
					</button>

					<div class="webmz-search-popup__body">
					<div
						class="webmz-ajax-search webmz-ajax-search--popup"
						data-products-limit="<?php echo esc_attr( $product_limit ); ?>"
						data-posts-limit="<?php echo esc_attr( $post_limit ); ?>"
						data-min-chars="3"
					>
						<form class="webmz-ajax-search__form" role="search" autocomplete="off">
							<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>">
								<?php echo esc_html( $settings['search_input_placeholder'] ); ?>
							</label>
							<input
								id="<?php echo esc_attr( $input_id ); ?>"
								class="webmz-ajax-search__input"
								type="search"
								name="webmz_search"
								placeholder="<?php echo esc_attr( $settings['search_input_placeholder'] ); ?>"
								autocomplete="off"
							>
							<button class="webmz-ajax-search__submit <?php echo esc_attr( webmz_get_icon_color_mode_class( $settings ) ); ?>" type="submit" aria-label="<?php echo esc_attr( $settings['button_label'] ); ?>">
								<?php Icons_Manager::render_icon( $settings['search_icon'], array( 'aria-hidden' => 'true' ) ); ?>
							</button>
						</form>

						<div class="webmz-ajax-search__panel" aria-live="polite" hidden>
							<div class="webmz-ajax-search__loading" hidden><?php esc_html_e( 'در حال جستجو...', 'tadris' ); ?></div>

							<section class="webmz-ajax-search__block">
								<header class="webmz-ajax-search__block-head">
									<h3 class="webmz-ajax-search__heading"><?php echo esc_html( $settings['products_title'] ); ?></h3>
									<div class="webmz-ajax-search__arrows">
										<button type="button" class="webmz-search-prev" aria-label="<?php esc_attr_e( 'قبلی', 'tadris' ); ?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M15 6l-6 6l6 6" /></svg>
										</button>
										<button type="button" class="webmz-search-next" aria-label="<?php esc_attr_e( 'بعدی', 'tadris' ); ?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 6l6 6l-6 6" /></svg>
										</button>
									</div>
								</header>
								<div class="swiper webmz-ajax-search__slider webmz-ajax-search__slider--products">
									<div class="swiper-wrapper" data-webmz-results="products"></div>
								</div>
							</section>

							<section class="webmz-ajax-search__block">
								<header class="webmz-ajax-search__block-head">
									<h3 class="webmz-ajax-search__heading"><?php echo esc_html( $settings['posts_title'] ); ?></h3>
									<div class="webmz-ajax-search__arrows">
										<button type="button" class="webmz-search-prev" aria-label="<?php esc_attr_e( 'قبلی', 'tadris' ); ?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M15 6l-6 6l6 6" /></svg>
										</button>
										<button type="button" class="webmz-search-next" aria-label="<?php esc_attr_e( 'بعدی', 'tadris' ); ?>">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 6l6 6l-6 6" /></svg>
										</button>
									</div>
								</header>
								<div class="swiper webmz-ajax-search__slider webmz-ajax-search__slider--posts">
									<div class="swiper-wrapper" data-webmz-results="posts"></div>
								</div>
							</section>

							<div class="webmz-ajax-search__suggestions">
								<?php if ( 'yes' === $settings['show_recent'] ) : ?>
									<section class="webmz-ajax-search__chips-block">
										<h3 class="webmz-ajax-search__heading"><?php echo esc_html( $settings['recent_title'] ); ?></h3>
										<div class="webmz-ajax-search__chips" data-webmz-recent></div>
									</section>
								<?php endif; ?>

								<?php if ( 'yes' === $settings['show_popular'] ) : ?>
									<section class="webmz-ajax-search__chips-block">
										<h3 class="webmz-ajax-search__heading"><?php echo esc_html( $settings['popular_title'] ); ?></h3>
										<div class="webmz-ajax-search__chips">
											<?php if ( $popular_terms ) : ?>
												<?php foreach ( $popular_terms as $term ) : ?>
													<button type="button" class="webmz-ajax-search__chip" data-webmz-term="<?php echo esc_attr( $term ); ?>">
														<?php echo esc_html( $term ); ?>
													</button>
												<?php endforeach; ?>
											<?php else : ?>
												<span class="webmz-ajax-search__muted"><?php esc_html_e( 'هنوز عبارتی در پنل قالب ثبت نشده است.', 'tadris' ); ?></span>
											<?php endif; ?>
										</div>
									</section>
								<?php endif; ?>
							</div>
						</div>
					</div>
					</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the Zhaket-inspired compact search popup.
	 *
	 * @param array $settings Widget settings.
	 * @return void
	 */
	private function render_zhaket_popup( $settings ) {
		$popular_terms = function_exists( '\webmz_get_popular_search_terms' )
			? \webmz_get_popular_search_terms()
			: array();

		$product_limit       = min( 12, max( 1, absint( $settings['products_limit'] ) ) );
		$post_limit          = min( 12, max( 1, absint( $settings['posts_limit'] ) ) );
		$bestsellers_limit   = min( 6, max( 1, absint( $settings['zhaket_bestsellers_limit'] ?? 3 ) ) );
		$input_id            = 'webmz-search-popup-input-' . $this->get_id();
		$popup_id            = 'webmz-search-popup-' . $this->get_id();
		$hide_button_text    = 'yes' === ( $settings['hide_button_text'] ?? '' );
		$popup_classes       = array( 'webmz-search-popup', 'webmz-search-popup--boot', 'webmz-search-popup--zhaket' );
		$banner_url          = ! empty( $settings['zhaket_banner']['url'] ) ? $settings['zhaket_banner']['url'] : '';
		$bestsellers_title   = ! empty( $settings['zhaket_bestsellers_title'] )
			? $settings['zhaket_bestsellers_title']
			: esc_html__( 'محصولات پرفروش', 'tadris' );
		$popular_title       = ! empty( $settings['zhaket_popular_title'] )
			? $settings['zhaket_popular_title']
			: esc_html__( 'جستجوهای محبوب', 'tadris' );
		$bestsellers_html    = function_exists( '\webmz_ajax_search_bestsellers_html' )
			? \webmz_ajax_search_bestsellers_html( $bestsellers_limit )
			: '';

		if ( $hide_button_text ) {
			$popup_classes[] = 'webmz-search-popup--icon-only';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $popup_classes ) ); ?>" data-webmz-search-popup>
			<button
				type="button"
				class="webmz-search-popup__trigger"
				data-webmz-search-popup-trigger
				aria-haspopup="dialog"
				aria-controls="<?php echo esc_attr( $popup_id ); ?>"
				aria-label="<?php echo esc_attr( $settings['button_label'] ); ?>"
			>
				<?php if ( ! $hide_button_text ) : ?>
					<span class="webmz-search-popup__trigger-text"><?php echo esc_html( $settings['placeholder'] ); ?></span>
				<?php endif; ?>
				<span class="webmz-search-popup__trigger-icon <?php echo esc_attr( webmz_get_icon_color_mode_class( $settings ) ); ?>">
					<?php Icons_Manager::render_icon( $settings['search_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</span>
			</button>

			<div
				id="<?php echo esc_attr( $popup_id ); ?>"
				class="webmz-search-popup__overlay"
				data-webmz-search-popup-overlay
				role="dialog"
				aria-modal="true"
				aria-label="<?php echo esc_attr( $settings['button_label'] ); ?>"
				hidden
				style="display:none;opacity:0;visibility:hidden;pointer-events:none;"
			>
				<div class="webmz-search-popup__shell">
					<div class="webmz-search-popup__dialog webmz-search-popup__dialog--zhaket" data-webmz-search-popup-dialog>
						<button
							type="button"
							class="webmz-search-popup__close"
							data-webmz-search-popup-close
							aria-label="<?php esc_attr_e( 'بستن', 'tadris' ); ?>"
						>
							<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
						</button>

						<div class="webmz-search-popup__body webmz-search-popup__body--zhaket">
							<div
								class="webmz-ajax-search webmz-ajax-search--popup webmz-ajax-search--zhaket"
								data-products-limit="<?php echo esc_attr( $product_limit ); ?>"
								data-posts-limit="<?php echo esc_attr( $post_limit ); ?>"
								data-min-chars="3"
							>
								<form class="webmz-ajax-search__form webmz-ajax-search__form--zhaket" role="search" autocomplete="off">
									<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>">
										<?php echo esc_html( $settings['search_input_placeholder'] ); ?>
									</label>
									<span class="webmz-ajax-search__icon <?php echo esc_attr( webmz_get_icon_color_mode_class( $settings ) ); ?>" aria-hidden="true">
										<?php Icons_Manager::render_icon( $settings['search_icon'], array( 'aria-hidden' => 'true' ) ); ?>
									</span>
									<input
										id="<?php echo esc_attr( $input_id ); ?>"
										class="webmz-ajax-search__input"
										type="search"
										name="webmz_search"
										placeholder="<?php echo esc_attr( $settings['search_input_placeholder'] ); ?>"
										autocomplete="off"
									>
									<button class="screen-reader-text" type="submit"><?php echo esc_html( $settings['button_label'] ); ?></button>
								</form>

								<?php if ( $banner_url ) : ?>
									<div class="webmz-zhaket-search__banner" data-webmz-zhaket-banner>
										<img src="<?php echo esc_url( $banner_url ); ?>" alt="" loading="lazy" decoding="async">
									</div>
								<?php endif; ?>

								<div class="webmz-ajax-search__panel webmz-ajax-search__panel--zhaket" aria-live="polite">
									<div class="webmz-ajax-search__loading" hidden><?php esc_html_e( 'در حال جستجو...', 'tadris' ); ?></div>

									<div class="webmz-zhaket-search__idle" data-webmz-zhaket-idle>
										<section class="webmz-zhaket-search__section">
											<header class="webmz-zhaket-search__section-head">
												<span class="webmz-zhaket-search__section-icon" aria-hidden="true">
													<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
												</span>
												<h3 class="webmz-zhaket-search__heading"><?php echo esc_html( $bestsellers_title ); ?></h3>
											</header>
											<?php echo $bestsellers_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</section>

										<section class="webmz-zhaket-search__section webmz-zhaket-search__section--popular">
											<header class="webmz-zhaket-search__section-head">
												<span class="webmz-zhaket-search__section-icon" aria-hidden="true">
													<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
												</span>
												<h3 class="webmz-zhaket-search__heading"><?php echo esc_html( $popular_title ); ?></h3>
											</header>
											<div class="webmz-ajax-search__chips">
												<?php if ( $popular_terms ) : ?>
													<?php foreach ( $popular_terms as $term ) : ?>
														<button type="button" class="webmz-ajax-search__chip" data-webmz-term="<?php echo esc_attr( $term ); ?>">
															<?php echo esc_html( $term ); ?>
														</button>
													<?php endforeach; ?>
												<?php else : ?>
													<span class="webmz-ajax-search__muted"><?php esc_html_e( 'هنوز عبارتی در پنل قالب ثبت نشده است.', 'tadris' ); ?></span>
												<?php endif; ?>
											</div>
										</section>
									</div>

									<div class="webmz-zhaket-search__results" data-webmz-zhaket-results hidden>
										<section class="webmz-ajax-search__block">
											<header class="webmz-ajax-search__block-head">
												<h3 class="webmz-ajax-search__heading"><?php echo esc_html( $settings['products_title'] ); ?></h3>
												<div class="webmz-ajax-search__arrows">
													<button type="button" class="webmz-search-prev" aria-label="<?php esc_attr_e( 'قبلی', 'tadris' ); ?>">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M15 6l-6 6l6 6" /></svg>
													</button>
													<button type="button" class="webmz-search-next" aria-label="<?php esc_attr_e( 'بعدی', 'tadris' ); ?>">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 6l6 6l-6 6" /></svg>
													</button>
												</div>
											</header>
											<div class="swiper webmz-ajax-search__slider webmz-ajax-search__slider--products">
												<div class="swiper-wrapper" data-webmz-results="products"></div>
											</div>
										</section>

										<section class="webmz-ajax-search__block">
											<header class="webmz-ajax-search__block-head">
												<h3 class="webmz-ajax-search__heading"><?php echo esc_html( $settings['posts_title'] ); ?></h3>
												<div class="webmz-ajax-search__arrows">
													<button type="button" class="webmz-search-prev" aria-label="<?php esc_attr_e( 'قبلی', 'tadris' ); ?>">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M15 6l-6 6l6 6" /></svg>
													</button>
													<button type="button" class="webmz-search-next" aria-label="<?php esc_attr_e( 'بعدی', 'tadris' ); ?>">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 6l6 6l-6 6" /></svg>
													</button>
												</div>
											</header>
											<div class="swiper webmz-ajax-search__slider webmz-ajax-search__slider--posts">
												<div class="swiper-wrapper" data-webmz-results="posts"></div>
											</div>
										</section>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
