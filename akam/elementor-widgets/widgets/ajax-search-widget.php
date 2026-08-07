<?php
/**
 * Elementor AJAX search widget.
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
 * Live product and article search widget with Swiper result lists.
 */
class Ajax_Search_Widget extends Widget_Base {

	public function get_name() {
		return 'webmz-ajax-search';
	}

	public function get_title() {
		return esc_html__( 'جستجوی ایجکسی آکام', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-search';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'search', 'ajax', 'جستجو', 'محصول', 'مقاله' );
	}

	public function get_style_depends() {
		return array( 'webmz-ajax-search' );
	}

	public function get_script_depends() {
		return array( 'webmz-ajax-search' );
	}

	/**
	 * Register widget editor controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_search',
			array(
				'label' => esc_html__( 'جستجو', 'tadris' ),
			)
		);

		$this->add_control(
			'placeholder',
			array(
				'label'       => esc_html__( 'متن داخل کادر', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دنبال چی می‌گردید؟', 'tadris' ),
				'label_block' => true,
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
				'label'       => esc_html__( 'برچسب دسترس‌پذیری دکمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'جستجو', 'tadris' ),
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
			'section_box_style',
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

		$this->add_responsive_control(
			'box_height',
			array(
				'label'      => esc_html__( 'ارتفاع کادر', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 40,
						'max' => 110,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 58,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ajax-search__form' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_radius',
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
					'size' => 12,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ajax-search__form' => 'border-radius: {{SIZE}}{{UNIT}};',
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => esc_html__( 'دکمه جستجو', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'       => esc_html__( 'رنگ پس‌زمینه دکمه', 'tadris' ),
				'description' => esc_html__( 'در صورت خالی بودن، رنگ اصلی پنل تنظیمات قالب استفاده می‌شود.', 'tadris' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'{{WRAPPER}} .webmz-ajax-search__submit' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background_hover',
			array(
				'label'       => esc_html__( 'رنگ دکمه در هاور', 'tadris' ),
				'description' => esc_html__( 'در صورت خالی بودن، هاور رنگ اصلی قالب استفاده می‌شود.', 'tadris' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'{{WRAPPER}} .webmz-ajax-search__submit:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'رنگ آیکون', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ajax-search__submit' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_width',
			array(
				'label'      => esc_html__( 'عرض دکمه', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 40,
						'max' => 110,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 58,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ajax-search__submit' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'اندازه آیکون', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 42,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 19,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ajax-search__submit' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .webmz-ajax-search__submit svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_panel_style',
			array(
				'label' => esc_html__( 'پنل نتایج', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'panel_background',
			array(
				'label'     => esc_html__( 'پس‌زمینه پنل', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ajax-search__panel' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'panel_border_color',
			array(
				'label'     => esc_html__( 'رنگ حاشیه پنل', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e5edf5',
				'selectors' => array(
					'{{WRAPPER}} .webmz-ajax-search__panel' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'panel_radius',
			array(
				'label'      => esc_html__( 'گردی پنل', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 18,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-ajax-search__panel' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'panel_shadow',
				'selector' => '{{WRAPPER}} .webmz-ajax-search__panel',
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .webmz-ajax-search__heading',
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
		$settings      = $this->get_settings_for_display();
		$popular_terms = function_exists( '\webmz_get_popular_search_terms' )
			? \webmz_get_popular_search_terms()
			: array();

		$product_limit = min( 12, max( 1, absint( $settings['products_limit'] ) ) );
		$post_limit    = min( 12, max( 1, absint( $settings['posts_limit'] ) ) );
		$input_id      = 'webmz-search-input-' . $this->get_id();
		?>
		<div
			class="webmz-ajax-search"
			data-products-limit="<?php echo esc_attr( $product_limit ); ?>"
			data-posts-limit="<?php echo esc_attr( $post_limit ); ?>"
			data-min-chars="3"
		>
			<form class="webmz-ajax-search__form" role="search" autocomplete="off">
				<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>">
					<?php echo esc_html( $settings['button_label'] ); ?>
				</label>
				<input
					id="<?php echo esc_attr( $input_id ); ?>"
					class="webmz-ajax-search__input"
					type="search"
					name="webmz_search"
					placeholder="<?php echo esc_attr( $settings['placeholder'] ); ?>"
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
		<?php
	}
}
