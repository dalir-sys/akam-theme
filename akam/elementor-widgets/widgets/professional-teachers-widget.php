<?php
/**
 * Professional teachers display Elementor widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * Display professional teacher cards in grid or slider layout.
 */
class Professional_Teachers_Widget extends Widget_Base {
	use Teachers_Widget_Trait;

	public function get_name() {
		return 'webmz-professional-teachers';
	}

	public function get_title() {
		return esc_html__( 'نمایش مدرسین حرفه‌ای', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return array( webmz_elementor_category_slug() );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-plyr', 'webmz-plyr-widgets', 'webmz-professional-teachers' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-plyr', 'webmz-professional-teachers' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'تعداد مدرسین', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 4,
				'min'     => 1,
				'max'     => 24,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => esc_html__( 'مرتب‌سازی', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'       => esc_html__( 'تاریخ', 'tadris' ),
					'title'      => esc_html__( 'عنوان', 'tadris' ),
					'menu_order' => esc_html__( 'ترتیب دستی', 'tadris' ),
					'rand'       => esc_html__( 'تصادفی', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'display_type',
			array(
				'label'   => esc_html__( 'نحوه نمایش', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'grid'   => esc_html__( 'گرید', 'tadris' ),
					'slider' => esc_html__( 'اسلایدی (Swiper)', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'grid_columns_heading',
			array(
				'label'     => esc_html__( 'تنظیمات گرید', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'display_type' => 'grid',
				),
			)
		);

		$this->register_teacher_grid_controls();

		$this->add_control(
			'slider_heading',
			array(
				'label'     => esc_html__( 'تنظیمات اسلایدر', 'tadris' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'display_type' => 'slider',
				),
			)
		);

		foreach ( array( 'desktop' => esc_html__( 'دسکتاپ', 'tadris' ), 'tablet' => esc_html__( 'تبلت', 'tadris' ), 'mobile' => esc_html__( 'موبایل', 'tadris' ) ) as $device => $label ) {
			$this->add_control(
				'slides_' . $device,
				array(
					'label'     => sprintf( esc_html__( 'تعداد اسلاید %s', 'tadris' ), $label ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 'mobile' === $device ? 1 : ( 'tablet' === $device ? 2 : 4 ),
					'min'       => 1,
					'max'       => 6,
					'condition' => array(
						'display_type' => 'slider',
					),
				)
			);
		}

		$this->add_control(
			'slider_loop',
			array(
				'label'        => esc_html__( 'لوپ اسلایدر', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'slider_autoplay',
			array(
				'label'        => esc_html__( 'پخش خودکار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'slider_autoplay_delay',
			array(
				'label'     => esc_html__( 'زمان پخش خودکار (میلی‌ثانیه)', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3500,
				'min'       => 1000,
				'step'      => 100,
				'condition' => array(
					'display_type'     => 'slider',
					'slider_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_slider_nav',
			array(
				'label'        => esc_html__( 'نمایش فلش‌ها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'show_slider_pagination',
			array(
				'label'        => esc_html__( 'نمایش صفحه‌بندی', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'display_type' => 'slider',
				),
			)
		);

		$this->add_control(
			'card_title_tag',
			array(
				'label'     => esc_html__( 'تگ عنوان کارت', 'tadris' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => array(
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build card settings from widget settings.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<string,mixed>
	 */
	private function get_card_settings( $settings ) {
		return array(
			'title_tag' => \webmz_sanitize_heading_tag( isset( $settings['card_title_tag'] ) ? $settings['card_title_tag'] : 'h3' ),
		);
	}

	/**
	 * Render editor preview cards.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_editor_preview( $settings ) {
		$is_slider = 'slider' === $settings['display_type'];
		$gap       = isset( $settings['grid_gap']['size'] ) ? absint( $settings['grid_gap']['size'] ) : 24;
		?>
		<div class="webmz-pro-teachers webmz-pro-teachers--editor">
			<?php if ( $is_slider ) : ?>
				<div class="webmz-pro-teachers__slider swiper" style="--webmz-pro-teachers-gap: <?php echo esc_attr( $gap ); ?>px;">
					<div class="swiper-wrapper">
						<?php for ( $i = 0; $i < 4; $i++ ) : ?>
							<div class="swiper-slide">
								<?php $this->render_demo_card(); ?>
							</div>
						<?php endfor; ?>
					</div>
				</div>
			<?php else : ?>
				<div
					class="webmz-pro-teachers__grid"
					style="--webmz-grid-columns: <?php echo esc_attr( $settings['grid_columns_desktop'] ); ?>; --webmz-grid-tablet-columns: <?php echo esc_attr( $settings['grid_columns_tablet'] ); ?>; --webmz-grid-mobile-columns: <?php echo esc_attr( $settings['grid_columns_mobile'] ); ?>; --webmz-grid-gap: <?php echo esc_attr( $gap ); ?>px;"
				>
					<?php for ( $i = 0; $i < 4; $i++ ) : ?>
						<?php $this->render_demo_card(); ?>
					<?php endfor; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render a demo card in the editor.
	 *
	 * @return void
	 */
	private function render_demo_card() {
		?>
		<article class="webmz-pro-teacher-card">
			<div class="webmz-pro-teacher-card__media-wrap">
				<div class="webmz-pro-teacher-card__media">
					<span class="webmz-pro-teacher-card__placeholder" aria-hidden="true">م</span>
				</div>
				<span class="webmz-pro-teacher-card__expertise">
					<span class="webmz-pro-teacher-card__expertise-demo" aria-hidden="true"></span>
				</span>
				<button type="button" class="webmz-pro-teacher-card__video" aria-hidden="true" tabindex="-1">
					<span class="webmz-pro-teacher-card__video-icon" aria-hidden="true"></span>
				</button>
			</div>
			<div class="webmz-pro-teacher-card__body">
				<h3 class="webmz-pro-teacher-card__name"><span><?php esc_html_e( 'نام مدرس', 'tadris' ); ?></span></h3>
				<p class="webmz-pro-teacher-card__role"><?php esc_html_e( 'مدرس زبان ترکی', 'tadris' ); ?></p>
				<p class="webmz-pro-teacher-card__experience"><?php esc_html_e( '۱۰ سال سابقه تدریس', 'tadris' ); ?></p>
			</div>
		</article>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( \webmz_is_layout_editing_context() ) {
			$this->render_editor_preview( $settings );
			return;
		}

		$card_settings = $this->get_card_settings( $settings );
		$gap           = isset( $settings['grid_gap']['size'] ) ? absint( $settings['grid_gap']['size'] ) : 24;
		$is_slider     = 'slider' === $settings['display_type'];
		$query         = new WP_Query(
			webmz_teachers_archive_build_query_args(
				array(
					'posts_per_page' => isset( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : 4,
					'orderby'        => isset( $settings['orderby'] ) ? $settings['orderby'] : 'date',
				)
			)
		);

		if ( ! $query->have_posts() ) {
			echo '<div class="webmz-pro-teachers__empty">' . esc_html__( 'مدرسی یافت نشد.', 'tadris' ) . '</div>';
			return;
		}

		if ( $is_slider ) {
			$slider_conf = array(
				'slidesDesktop' => ! empty( $settings['slides_desktop'] ) ? absint( $settings['slides_desktop'] ) : 4,
				'slidesTablet'  => ! empty( $settings['slides_tablet'] ) ? absint( $settings['slides_tablet'] ) : 2,
				'slidesMobile'  => ! empty( $settings['slides_mobile'] ) ? absint( $settings['slides_mobile'] ) : 1,
				'spaceBetween'  => $gap,
				'loop'          => isset( $settings['slider_loop'] ) && 'yes' === $settings['slider_loop'],
				'autoplay'      => isset( $settings['slider_autoplay'] ) && 'yes' === $settings['slider_autoplay'],
				'autoplayDelay' => ! empty( $settings['slider_autoplay_delay'] ) ? absint( $settings['slider_autoplay_delay'] ) : 3500,
				'navigation'    => ! isset( $settings['show_slider_nav'] ) || 'yes' === $settings['show_slider_nav'],
				'pagination'    => ! isset( $settings['show_slider_pagination'] ) || 'yes' === $settings['show_slider_pagination'],
			);
			?>
			<div class="webmz-pro-teachers">
				<div
					class="webmz-pro-teachers__slider swiper"
					data-webmz-pro-teachers-slider="<?php echo esc_attr( wp_json_encode( $slider_conf ) ); ?>"
					style="--webmz-pro-teachers-gap: <?php echo esc_attr( $gap ); ?>px;"
				>
					<div class="swiper-wrapper">
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							?>
							<div class="swiper-slide">
								<?php echo webmz_render_professional_teacher_card( get_the_ID(), $card_settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						<?php endwhile; ?>
					</div>

					<?php if ( $slider_conf['navigation'] ) : ?>
						<div class="webmz-pro-teachers__arrow webmz-pro-teachers__arrow--next swiper-button-next" aria-label="<?php esc_attr_e( 'بعدی', 'tadris' ); ?>"></div>
						<div class="webmz-pro-teachers__arrow webmz-pro-teachers__arrow--prev swiper-button-prev" aria-label="<?php esc_attr_e( 'قبلی', 'tadris' ); ?>"></div>
					<?php endif; ?>

					<?php if ( $slider_conf['pagination'] ) : ?>
						<div class="webmz-pro-teachers__pagination swiper-pagination"></div>
					<?php endif; ?>
				</div>
			</div>
			<?php
			wp_reset_postdata();
		} else {
			?>
			<div class="webmz-pro-teachers">
				<div
					class="webmz-pro-teachers__grid"
					style="--webmz-grid-columns: <?php echo esc_attr( $settings['grid_columns_desktop'] ); ?>; --webmz-grid-tablet-columns: <?php echo esc_attr( $settings['grid_columns_tablet'] ); ?>; --webmz-grid-mobile-columns: <?php echo esc_attr( $settings['grid_columns_mobile'] ); ?>; --webmz-grid-gap: <?php echo esc_attr( $gap ); ?>px;"
				>
					<?php echo webmz_professional_teachers_render_cards_html( $query, $card_settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
			<?php
		}
	}
}
