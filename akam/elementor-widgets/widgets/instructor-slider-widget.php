<?php
/**
 * Instructor showcase slider Elementor widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * RTL instructor slider with profile-thumbnail navigation.
 */
class Instructor_Slider_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-instructor-slider';
	}

	public function get_title() {
		return esc_html__( 'اسلایدر مدرسین', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return array( webmz_elementor_category_slug() );
	}

	public function get_style_depends() {
		return array( 'webmz-swiper', 'webmz-instructor-slider' );
	}

	public function get_script_depends() {
		return array( 'webmz-swiper', 'webmz-instructor-slider' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'تعداد مدرسین', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5,
				'min'     => 1,
				'max'     => 12,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => esc_html__( 'مرتب‌سازی', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'menu_order',
				'options' => array(
					'date'       => esc_html__( 'تاریخ', 'tadris' ),
					'title'      => esc_html__( 'عنوان', 'tadris' ),
					'menu_order' => esc_html__( 'ترتیب دستی', 'tadris' ),
					'rand'       => esc_html__( 'تصادفی', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => esc_html__( 'تگ عنوان', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => array(
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
				),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'متن دکمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دوره های مدرس', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'skills_label',
			array(
				'label'       => esc_html__( 'برچسب مهارت‌ها', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'مهارت‌ها', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'experience_label',
			array(
				'label'       => esc_html__( 'برچسب سابقه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'سابقه', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'background_label',
			array(
				'label'       => esc_html__( 'برچسب سوابق', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'سوابق', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section( 'slider_settings', array( 'label' => esc_html__( 'تنظیمات اسلایدر', 'tadris' ) ) );

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'پخش خودکار', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'autoplay_delay',
			array(
				'label'     => esc_html__( 'تأخیر پخش خودکار (ms)', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'min'       => 2000,
				'max'       => 15000,
				'step'      => 500,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => esc_html__( 'حلقه بی‌نهایت', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build slider config from settings.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @param int                 $count    Slide count.
	 * @return array<string,mixed>
	 */
	private function get_slider_config( $settings, $count ) {
		$wants_loop = isset( $settings['loop'] ) && 'yes' === $settings['loop'];

		return array(
			'autoplay'      => isset( $settings['autoplay'] ) && 'yes' === $settings['autoplay'],
			'autoplayDelay' => ! empty( $settings['autoplay_delay'] ) ? absint( $settings['autoplay_delay'] ) : 5000,
			'loop'          => $wants_loop && $count > 1,
		);
	}

	/**
	 * Render teacher portrait image.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $size    Image size.
	 * @param string $class   CSS class.
	 * @return void
	 */
	private function render_teacher_image( $post_id, $size, $class ) {
		if ( has_post_thumbnail( $post_id ) ) {
			echo webmz_get_post_loop_thumbnail(
				$post_id,
				array(
					'class' => $class,
					'alt'   => get_the_title( $post_id ),
				)
			);
			return;
		}

		printf(
			'<span class="%s webmz-instructor-slider__placeholder" aria-hidden="true">%s</span>',
			esc_attr( $class ),
			esc_html( mb_substr( get_the_title( $post_id ), 0, 1 ) )
		);
	}

	/**
	 * Build detail lines for a teacher slide.
	 *
	 * @param int                 $post_id  Post ID.
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<int,array{label:string,value:string}>
	 */
	private function get_teacher_details( $post_id, $settings ) {
		$details    = array();
		$specialties = webmz_get_teacher_list_items( $post_id, 'specialties' );
		$experience  = webmz_get_teacher_list_items( $post_id, 'experience' );

		if ( ! empty( $specialties ) && ! empty( $settings['skills_label'] ) ) {
			$details[] = array(
				'label' => $settings['skills_label'],
				'value' => implode( '، ', $specialties ),
			);
		}

		$teaching_experience = webmz_get_teacher_teaching_experience( $post_id );
		if ( ! $teaching_experience ) {
			$teaching_experience = sanitize_text_field( (string) webmz_get_teacher_meta( $post_id, 'experience_years', '' ) );
		}

		if ( $teaching_experience && ! empty( $settings['experience_label'] ) ) {
			$details[] = array(
				'label' => $settings['experience_label'],
				'value' => $teaching_experience,
			);
		}

		$background = '';
		if ( ! empty( $experience ) ) {
			$background = implode( '، ', $experience );
		} elseif ( webmz_get_teacher_short_bio( $post_id ) ) {
			$background = webmz_get_teacher_short_bio( $post_id );
		}

		if ( $background && ! empty( $settings['background_label'] ) ) {
			$details[] = array(
				'label' => $settings['background_label'],
				'value' => $background,
			);
		}

		return $details;
	}

	/**
	 * Render editor preview.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_editor_preview( $settings ) {
		$title_tag = \webmz_sanitize_heading_tag( isset( $settings['title_tag'] ) ? $settings['title_tag'] : 'h3' );
		$count     = min( 5, max( 1, absint( $settings['posts_per_page'] ?? 5 ) ) );
		?>
		<div class="webmz-instructor-slider webmz-instructor-slider--editor" dir="rtl">
			<span class="webmz-instructor-slider__decor webmz-instructor-slider__decor--primary" aria-hidden="true"></span>
			<span class="webmz-instructor-slider__decor webmz-instructor-slider__decor--accent" aria-hidden="true"></span>

			<div class="webmz-instructor-slider__card">
				<div class="webmz-instructor-slider__layout">
					<div class="webmz-instructor-slider__thumbs swiper" aria-hidden="true">
						<div class="swiper-wrapper">
							<?php for ( $i = 0; $i < $count; $i++ ) : ?>
								<div class="swiper-slide">
									<button type="button" class="webmz-instructor-slider__thumb<?php echo 0 === $i ? ' is-active' : ''; ?>" data-slide-index="<?php echo esc_attr( (string) $i ); ?>" tabindex="-1">
										<span class="webmz-instructor-slider__thumb-inner">
											<span class="webmz-instructor-slider__placeholder">م</span>
										</span>
									</button>
								</div>
							<?php endfor; ?>
						</div>
					</div>

					<div class="webmz-instructor-slider__main swiper">
						<div class="swiper-wrapper">
							<div class="swiper-slide">
								<div class="webmz-instructor-slider__panel">
									<div class="webmz-instructor-slider__photo">
										<div class="webmz-instructor-slider__photo-frame">
											<span class="webmz-instructor-slider__placeholder webmz-instructor-slider__photo-img">م</span>
										</div>
									</div>
									<div class="webmz-instructor-slider__info">
										<<?php echo esc_attr( $title_tag ); ?> class="webmz-instructor-slider__name"><?php esc_html_e( 'نام مدرس', 'tadris' ); ?></<?php echo esc_attr( $title_tag ); ?>>
										<p class="webmz-instructor-slider__role"><?php esc_html_e( 'مدرس پایتون و لاراول', 'tadris' ); ?></p>
										<div class="webmz-instructor-slider__details">
											<p class="webmz-instructor-slider__detail"><span class="webmz-instructor-slider__detail-label"><?php echo esc_html( $settings['skills_label'] ?? '' ); ?>:</span> <?php esc_html_e( 'برنامه‌نویسی وب، فریم‌ورک لاراول', 'tadris' ); ?></p>
											<p class="webmz-instructor-slider__detail"><span class="webmz-instructor-slider__detail-label"><?php echo esc_html( $settings['experience_label'] ?? '' ); ?>:</span> <?php esc_html_e( '۱۰ سال سابقه تدریس', 'tadris' ); ?></p>
										</div>
										<a class="webmz-instructor-slider__btn" href="#"><?php echo esc_html( $settings['button_text'] ?? '' ); ?></a>
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

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( \webmz_is_layout_editing_context() ) {
			$this->render_editor_preview( $settings );
			return;
		}

		$query = new WP_Query(
			webmz_teachers_archive_build_query_args(
				array(
					'posts_per_page' => isset( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : 5,
					'orderby'        => isset( $settings['orderby'] ) ? $settings['orderby'] : 'menu_order',
				)
			)
		);

		if ( ! $query->have_posts() ) {
			echo '<div class="webmz-instructor-slider__empty">' . esc_html__( 'مدرسی یافت نشد.', 'tadris' ) . '</div>';
			return;
		}

		$title_tag = \webmz_sanitize_heading_tag( isset( $settings['title_tag'] ) ? $settings['title_tag'] : 'h3' );
		$slides    = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id  = get_the_ID();
			$slides[] = array(
				'id'      => $post_id,
				'name'    => get_the_title(),
				'role'    => webmz_get_teacher_job_title( $post_id ),
				'url'     => get_permalink( $post_id ),
				'details' => $this->get_teacher_details( $post_id, $settings ),
			);
		}

		wp_reset_postdata();

		$slide_count = count( $slides );
		$config      = $this->get_slider_config( $settings, $slide_count );
		?>
		<div
			class="webmz-instructor-slider"
			dir="rtl"
			data-webmz-instructor-slider="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
		>
			<span class="webmz-instructor-slider__decor webmz-instructor-slider__decor--primary" aria-hidden="true"></span>
			<span class="webmz-instructor-slider__decor webmz-instructor-slider__decor--accent" aria-hidden="true"></span>

			<div class="webmz-instructor-slider__card">
				<div class="webmz-instructor-slider__layout">
					<nav class="webmz-instructor-slider__thumbs swiper" aria-label="<?php esc_attr_e( 'انتخاب مدرس', 'tadris' ); ?>">
						<div class="swiper-wrapper">
							<?php foreach ( $slides as $index => $slide ) : ?>
								<div class="swiper-slide">
									<button
										type="button"
										class="webmz-instructor-slider__thumb"
										data-slide-index="<?php echo esc_attr( (string) $index ); ?>"
										aria-label="<?php echo esc_attr( $slide['name'] ); ?>"
										<?php echo 0 === $index ? 'aria-current="true"' : ''; ?>
									>
										<span class="webmz-instructor-slider__thumb-inner">
											<?php $this->render_teacher_image( $slide['id'], 'thumbnail', 'webmz-instructor-slider__thumb-img' ); ?>
										</span>
									</button>
								</div>
							<?php endforeach; ?>
						</div>
					</nav>

					<div class="webmz-instructor-slider__main swiper">
						<div class="swiper-wrapper">
							<?php foreach ( $slides as $slide ) : ?>
								<div class="swiper-slide">
									<div class="webmz-instructor-slider__panel">
										<div class="webmz-instructor-slider__photo">
											<div class="webmz-instructor-slider__photo-frame">
												<?php $this->render_teacher_image( $slide['id'], 'large', 'webmz-instructor-slider__photo-img' ); ?>
											</div>
										</div>

										<div class="webmz-instructor-slider__info">
											<<?php echo esc_attr( $title_tag ); ?> class="webmz-instructor-slider__name"><?php echo esc_html( $slide['name'] ); ?></<?php echo esc_attr( $title_tag ); ?>>

											<?php if ( $slide['role'] ) : ?>
												<p class="webmz-instructor-slider__role"><?php echo esc_html( $slide['role'] ); ?></p>
											<?php endif; ?>

											<?php if ( ! empty( $slide['details'] ) ) : ?>
												<div class="webmz-instructor-slider__details">
													<?php foreach ( $slide['details'] as $detail ) : ?>
														<p class="webmz-instructor-slider__detail">
															<span class="webmz-instructor-slider__detail-label"><?php echo esc_html( $detail['label'] ); ?>:</span>
															<?php echo esc_html( $detail['value'] ); ?>
														</p>
													<?php endforeach; ?>
												</div>
											<?php endif; ?>

											<?php if ( ! empty( $settings['button_text'] ) ) : ?>
												<a class="webmz-instructor-slider__btn" href="<?php echo esc_url( $slide['url'] ); ?>">
													<?php echo esc_html( $settings['button_text'] ); ?>
												</a>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
