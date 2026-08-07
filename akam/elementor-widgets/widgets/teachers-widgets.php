<?php
/**
 * Teachers Elementor widgets.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * Shared helpers for teacher widgets.
 */
trait Teachers_Widget_Trait {

	/**
	 * Register common grid controls.
	 *
	 * @return void
	 */
	protected function register_teacher_grid_controls() {
		$this->add_control(
			'grid_columns_desktop',
			array(
				'label'   => esc_html__( 'ستون دسکتاپ', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
			)
		);

		$this->add_control(
			'grid_columns_tablet',
			array(
				'label'   => esc_html__( 'ستون تبلت', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '2',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
			)
		);

		$this->add_control(
			'grid_columns_mobile',
			array(
				'label'   => esc_html__( 'ستون موبایل', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => array(
					'1' => '1',
					'2' => '2',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'فاصله کارت‌ها', 'tadris' ),
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
					'size' => 24,
				),
			)
		);
	}

	/**
	 * Build card settings array.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<string,mixed>
	 */
	protected function get_teacher_card_settings( $settings ) {
		return array(
			'title_tag'   => \webmz_sanitize_heading_tag( isset( $settings['card_title_tag'] ) ? $settings['card_title_tag'] : 'h3' ),
			'button_text' => ! empty( $settings['button_text'] ) ? $settings['button_text'] : esc_html__( 'مشاهده پروفایل', 'tadris' ),
			'show_button' => true,
			'show_stats'  => ! empty( $settings['show_stats'] ) && 'yes' === $settings['show_stats'],
		);
	}

	/**
	 * Render breadcrumb navigation.
	 *
	 * @param string $wrapper_class Wrapper class.
	 * @return void
	 */
	protected function render_teacher_breadcrumb( $wrapper_class = 'webmz-teachers-archive__breadcrumb' ) {
		webmz_render_breadcrumb_nav( $wrapper_class );
	}
}

/**
 * Teachers archive widget for layout builder.
 */
class Teachers_Archive_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;
	use Teachers_Widget_Trait;

	public function get_name() {
		return 'webmz-teachers-archive';
	}

	public function get_title() {
		return esc_html__( 'آرشیو مدرسین', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return array( webmz_elementor_dynamic_category_slug() );
	}

	public function get_style_depends() {
		return array( 'webmz-teachers' );
	}

	public function get_script_depends() {
		return array( 'webmz-teachers-archive' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_title', array( 'label' => esc_html__( 'عنوان بخش', 'tadris' ) ) );
		$this->add_control(
			'title_text',
			array(
				'label'       => esc_html__( 'متن عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'آرشیو اساتید', 'tadris' ),
				'label_block' => true,
			)
		);
		$this->add_control(
			'title_tag',
			array(
				'label'   => esc_html__( 'تگ HTML عنوان', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h1',
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
				),
			)
		);
		$this->add_control(
			'show_breadcrumb',
			array(
				'label'        => esc_html__( 'نمایش خرده‌نان', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'posts_section', array( 'label' => esc_html__( 'نمایش مدرسین', 'tadris' ) ) );
		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'تعداد در هر صفحه', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 12,
				'min'     => 1,
				'max'     => 48,
			)
		);
		$this->register_teacher_grid_controls();
		$this->add_control(
			'card_title_tag',
			array(
				'label'   => esc_html__( 'تگ عنوان کارت', 'tadris' ),
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
				'label'   => esc_html__( 'متن دکمه', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مشاهده پروفایل', 'tadris' ),
			)
		);
		$this->add_control(
			'show_stats',
			array(
				'label'        => esc_html__( 'نمایش آمار در کارت', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render editor preview cards.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_editor_preview( $settings ) {
		$title_tag = \webmz_sanitize_heading_tag( isset( $settings['title_tag'] ) ? $settings['title_tag'] : 'h1' );
		$gap       = isset( $settings['grid_gap']['size'] ) ? absint( $settings['grid_gap']['size'] ) : 24;
		$card      = $this->get_teacher_card_settings( $settings );
		?>
		<div class="webmz-teachers-archive webmz-teachers-archive--editor">
			<?php if ( ! empty( $settings['show_breadcrumb'] ) && 'yes' === $settings['show_breadcrumb'] ) : ?>
				<nav class="webmz-teachers-archive__breadcrumb webmz-wc-breadcrumb" aria-hidden="true">
					<ol class="webmz-wc-breadcrumb__list">
						<li class="webmz-wc-breadcrumb__item"><span class="webmz-wc-breadcrumb__link"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span></li>
						<li class="webmz-wc-breadcrumb__item"><span class="webmz-wc-breadcrumb__current"><?php esc_html_e( 'آرشیو اساتید', 'tadris' ); ?></span></li>
					</ol>
				</nav>
			<?php endif; ?>
			<<?php echo esc_attr( $title_tag ); ?> class="webmz-teachers-archive__heading"><?php echo esc_html( $settings['title_text'] ); ?></<?php echo esc_attr( $title_tag ); ?>>
			<div
				class="webmz-teachers-archive__grid"
				style="--webmz-grid-columns: <?php echo esc_attr( $settings['grid_columns_desktop'] ); ?>; --webmz-grid-tablet-columns: <?php echo esc_attr( $settings['grid_columns_tablet'] ); ?>; --webmz-grid-mobile-columns: <?php echo esc_attr( $settings['grid_columns_mobile'] ); ?>; --webmz-grid-gap: <?php echo esc_attr( $gap ); ?>px;"
			>
				<?php for ( $i = 0; $i < 4; $i++ ) : ?>
					<article class="webmz-teacher-card">
						<div class="webmz-teacher-card__media"><span class="webmz-teacher-card__placeholder" aria-hidden="true">م</span></div>
						<div class="webmz-teacher-card__body">
							<h3 class="webmz-teacher-card__name"><span><?php esc_html_e( 'نام مدرس نمونه', 'tadris' ); ?></span></h3>
							<p class="webmz-teacher-card__role"><?php esc_html_e( 'مدرس طراحی رابط کاربری', 'tadris' ); ?></p>
						</div>
						<span class="webmz-teacher-card__button"><span><?php echo esc_html( $card['button_text'] ); ?></span><span class="webmz-teacher-card__button-icon" aria-hidden="true"></span></span>
					</article>
				<?php endfor; ?>
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

		$posts_per_page = isset( $settings['posts_per_page'] ) ? max( 1, absint( $settings['posts_per_page'] ) ) : 12;
		$current_page   = max( 1, (int) get_query_var( 'paged', 1 ) );
		$query          = new WP_Query(
			webmz_teachers_archive_build_query_args(
				array(
					'posts_per_page' => $posts_per_page,
					'page'           => $current_page,
				)
			)
		);
		$title_tag = \webmz_sanitize_heading_tag( isset( $settings['title_tag'] ) ? $settings['title_tag'] : 'h1' );
		$gap       = isset( $settings['grid_gap']['size'] ) ? absint( $settings['grid_gap']['size'] ) : 24;
		$card      = $this->get_teacher_card_settings( $settings );
		$config    = array(
			'postsPerPage' => $posts_per_page,
			'titleTag'     => $card['title_tag'],
			'buttonText'   => $card['button_text'],
			'showStats'    => ! empty( $card['show_stats'] ),
			'currentPage'  => $current_page,
			'maxPages'     => (int) $query->max_num_pages,
		);
		?>
		<div class="webmz-teachers-archive" data-webmz-teachers-archive="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">
			<?php if ( ! empty( $settings['show_breadcrumb'] ) && 'yes' === $settings['show_breadcrumb'] ) : ?>
				<?php $this->render_teacher_breadcrumb(); ?>
			<?php endif; ?>

			<<?php echo esc_attr( $title_tag ); ?> class="webmz-teachers-archive__heading"><?php echo esc_html( $settings['title_text'] ); ?></<?php echo esc_attr( $title_tag ); ?>>

			<div class="webmz-teachers-archive__status" role="status" aria-live="polite" aria-atomic="true" hidden></div>

			<div
				class="webmz-teachers-archive__grid"
				style="--webmz-grid-columns: <?php echo esc_attr( $settings['grid_columns_desktop'] ); ?>; --webmz-grid-tablet-columns: <?php echo esc_attr( $settings['grid_columns_tablet'] ); ?>; --webmz-grid-mobile-columns: <?php echo esc_attr( $settings['grid_columns_mobile'] ); ?>; --webmz-grid-gap: <?php echo esc_attr( $gap ); ?>px;"
			>
				<?php echo webmz_teachers_archive_render_cards_html( $query, $card ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<div class="webmz-teachers-archive__pagination-wrap">
				<?php echo webmz_teachers_archive_render_pagination_html( $query, $current_page, $posts_per_page ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
	}
}

/**
 * Display teachers widget for general pages.
 */
class Teachers_Display_Widget extends Widget_Base {
	use Teachers_Widget_Trait;

	public function get_name() {
		return 'webmz-teachers-display';
	}

	public function get_title() {
		return esc_html__( 'نمایش مدرسین', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return array( webmz_elementor_category_slug() );
	}

	public function get_style_depends() {
		return array( 'webmz-teachers' );
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
		$this->register_teacher_grid_controls();
		$this->add_control(
			'card_title_tag',
			array(
				'label'   => esc_html__( 'تگ عنوان کارت', 'tadris' ),
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
				'label'   => esc_html__( 'متن دکمه', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مشاهده پروفایل', 'tadris' ),
			)
		);
		$this->add_control(
			'show_stats',
			array(
				'label'        => esc_html__( 'نمایش آمار در کارت', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$gap      = isset( $settings['grid_gap']['size'] ) ? absint( $settings['grid_gap']['size'] ) : 24;
		$card     = $this->get_teacher_card_settings( $settings );
		$query    = new WP_Query(
			webmz_teachers_archive_build_query_args(
				array(
					'posts_per_page' => isset( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : 4,
					'orderby'        => isset( $settings['orderby'] ) ? $settings['orderby'] : 'date',
				)
			)
		);
		?>
		<div class="webmz-teachers-display<?php echo \webmz_is_layout_editing_context() ? ' webmz-teachers-display--editor' : ''; ?>">
			<div
				class="webmz-teachers-display__grid"
				style="--webmz-grid-columns: <?php echo esc_attr( $settings['grid_columns_desktop'] ); ?>; --webmz-grid-tablet-columns: <?php echo esc_attr( $settings['grid_columns_tablet'] ); ?>; --webmz-grid-mobile-columns: <?php echo esc_attr( $settings['grid_columns_mobile'] ); ?>; --webmz-grid-gap: <?php echo esc_attr( $gap ); ?>px;"
			>
				<?php echo webmz_teachers_archive_render_cards_html( $query, $card ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
	}
}

/**
 * Single teacher hero widget.
 */
class Teacher_Single_Hero_Widget extends Widget_Base {
	use Teachers_Widget_Trait;

	public function get_name() {
		return 'webmz-teacher-single-hero';
	}

	public function get_title() {
		return esc_html__( 'هیرو مدرس', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return array( webmz_elementor_dynamic_category_slug() );
	}

	public function get_style_depends() {
		return array( 'webmz-teachers' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control(
			'show_breadcrumb',
			array(
				'label'        => esc_html__( 'نمایش خرده‌نان', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render demo hero in editor.
	 *
	 * @return void
	 */
	private function render_demo() {
		?>
		<div class="webmz-teacher-single-hero">
			<div class="webmz-teacher-single-hero__media"><span class="webmz-teacher-card__placeholder" aria-hidden="true">م</span></div>
			<div class="webmz-teacher-single-hero__content">
				<?php if ( ! empty( $this->get_settings_for_display()['show_breadcrumb'] ) && 'yes' === $this->get_settings_for_display()['show_breadcrumb'] ) : ?>
					<nav class="webmz-teacher-single__breadcrumb webmz-wc-breadcrumb" aria-hidden="true">
						<ol class="webmz-wc-breadcrumb__list">
							<li class="webmz-wc-breadcrumb__item"><span class="webmz-wc-breadcrumb__link"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span></li>
							<li class="webmz-wc-breadcrumb__item"><span class="webmz-wc-breadcrumb__link"><?php esc_html_e( 'آرشیو اساتید', 'tadris' ); ?></span></li>
							<li class="webmz-wc-breadcrumb__item"><span class="webmz-wc-breadcrumb__current"><?php esc_html_e( 'نام مدرس', 'tadris' ); ?></span></li>
						</ol>
					</nav>
				<?php endif; ?>
				<h1 class="webmz-teacher-single-hero__name"><?php esc_html_e( 'نام مدرس', 'tadris' ); ?></h1>
				<p class="webmz-teacher-single-hero__role"><?php esc_html_e( 'مدرس طراحی رابط کاربری', 'tadris' ); ?></p>
				<p class="webmz-teacher-single-hero__bio"><?php esc_html_e( 'بیوگرافی کوتاه مدرس در این بخش نمایش داده می‌شود.', 'tadris' ); ?></p>
				<div class="webmz-teacher-single-hero__stats">
					<div class="webmz-teacher-single-hero__stat"><span class="webmz-teacher-single-hero__stat-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-star"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873l-6.158 -3.245" /></svg></span><span class="webmz-teacher-single-hero__stat-text"><?php esc_html_e( 'امتیاز ۴.۹', 'tadris' ); ?></span></div>
					<div class="webmz-teacher-single-hero__stat"><span class="webmz-teacher-single-hero__stat-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-school"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M22 9l-10 -4l-10 4l10 4l10 -4v6" /><path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4" /></svg></span><span class="webmz-teacher-single-hero__stat-text"><?php esc_html_e( '۱۲۵۰ دانشجو', 'tadris' ); ?></span></div>
					<div class="webmz-teacher-single-hero__stat"><span class="webmz-teacher-single-hero__stat-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-player-play"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M7 4v16l13 -8l-13 -8" /></svg></span><span class="webmz-teacher-single-hero__stat-text"><?php esc_html_e( '۸ دوره', 'tadris' ); ?></span></div>
					<div class="webmz-teacher-single-hero__stat"><span class="webmz-teacher-single-hero__stat-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-stopwatch"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 13a7 7 0 1 0 14 0a7 7 0 0 0 -14 0" /><path d="M14.5 10.5l-2.5 2.5" /><path d="M17 8l1 -1" /><path d="M14 3h-4" /></svg></span><span class="webmz-teacher-single-hero__stat-text"><?php esc_html_e( '۵ سال تجربه', 'tadris' ); ?></span></div>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( \webmz_is_layout_editing_context() ) {
			$this->render_demo();
			return;
		}

		$post_id = \webmz_get_context_post_id();

		if ( ! $post_id || WEBMZ_TEACHER_POST_TYPE !== get_post_type( $post_id ) ) {
			return;
		}

		$stats = webmz_get_teacher_stats( $post_id );
		?>
		<div class="webmz-teacher-single-hero">
			<div class="webmz-teacher-single-hero__media">
				<?php
				if ( has_post_thumbnail( $post_id ) ) {
					echo get_the_post_thumbnail( $post_id, 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<div class="webmz-teacher-single-hero__content">
				<?php if ( ! empty( $settings['show_breadcrumb'] ) && 'yes' === $settings['show_breadcrumb'] ) : ?>
					<?php $this->render_teacher_breadcrumb( 'webmz-teacher-single__breadcrumb' ); ?>
				<?php endif; ?>
				<h1 class="webmz-teacher-single-hero__name"><?php echo esc_html( get_the_title( $post_id ) ); ?></h1>
				<?php if ( webmz_get_teacher_job_title( $post_id ) ) : ?>
					<p class="webmz-teacher-single-hero__role"><?php echo esc_html( webmz_get_teacher_job_title( $post_id ) ); ?></p>
				<?php endif; ?>
				<?php if ( webmz_get_teacher_short_bio( $post_id ) ) : ?>
					<p class="webmz-teacher-single-hero__bio"><?php echo esc_html( webmz_get_teacher_short_bio( $post_id ) ); ?></p>
				<?php endif; ?>
				<div class="webmz-teacher-single-hero__stats">
					<?php foreach ( $stats as $key => $label ) : ?>
						<?php if ( $label ) : ?>
							<div class="webmz-teacher-single-hero__stat">
								<span class="webmz-teacher-single-hero__stat-icon"><?php webmz_render_teacher_stat_icon( $key ); ?></span>
								<span class="webmz-teacher-single-hero__stat-text"><?php echo esc_html( $label ); ?></span>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}
}

/**
 * Single teacher content widget.
 */
class Teacher_Single_Content_Widget extends Widget_Base {
	public function get_name() {
		return 'webmz-teacher-single-content';
	}

	public function get_title() {
		return esc_html__( 'محتوای مدرس', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-post-content';
	}

	public function get_categories() {
		return array( webmz_elementor_dynamic_category_slug() );
	}

	public function get_style_depends() {
		return array( 'webmz-teachers' );
	}

	protected function render() {
		if ( \webmz_is_layout_editing_context() ) {
			echo '<div class="webmz-teacher-single__content webmz-editor-placeholder">' . esc_html__( 'محتوای کامل مدرس در این بخش نمایش داده می‌شود.', 'tadris' ) . '</div>';
			return;
		}

		$post = get_post( \webmz_get_context_post_id() );

		if ( ! $post || WEBMZ_TEACHER_POST_TYPE !== $post->post_type ) {
			return;
		}

		echo '<div class="webmz-teacher-single__content">' . apply_filters( 'the_content', $post->post_content ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Single teacher sections widget.
 */
class Teacher_Single_Sections_Widget extends Widget_Base {
	public function get_name() {
		return 'webmz-teacher-single-sections';
	}

	public function get_title() {
		return esc_html__( 'بخش‌های مدرس', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	public function get_categories() {
		return array( webmz_elementor_dynamic_category_slug() );
	}

	public function get_style_depends() {
		return array( 'webmz-teachers' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'عناوین', 'tadris' ) ) );
		$this->add_control( 'experience_title', array( 'label' => esc_html__( 'عنوان تجربه', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'تجربه کاری و آموزشی', 'tadris' ) ) );
		$this->add_control( 'specialties_title', array( 'label' => esc_html__( 'عنوان تخصص‌ها', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'تخصص‌ها', 'tadris' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( \webmz_is_layout_editing_context() ) {
			?>
			<div class="webmz-teacher-single__sections">
				<div class="webmz-teacher-single__block">
					<h2 class="webmz-teacher-single__section-title"><?php echo esc_html( $settings['experience_title'] ); ?></h2>
					<ul class="webmz-teacher-single__list webmz-teacher-single__list--experience"><li class="webmz-teacher-single__list-item"><?php esc_html_e( 'نمونه مورد تجربه', 'tadris' ); ?></li></ul>
				</div>
			</div>
			<?php
			return;
		}

		$post_id     = \webmz_get_context_post_id();
		$experience  = webmz_get_teacher_list_items( $post_id, 'experience' );
		$specialties = webmz_get_teacher_list_items( $post_id, 'specialties' );

		if ( empty( $experience ) && empty( $specialties ) ) {
			return;
		}
		?>
		<div class="webmz-teacher-single__sections">
			<?php if ( ! empty( $experience ) ) : ?>
				<div class="webmz-teacher-single__block">
					<h2 class="webmz-teacher-single__section-title"><?php echo esc_html( $settings['experience_title'] ); ?></h2>
					<ul class="webmz-teacher-single__list webmz-teacher-single__list--experience">
						<?php foreach ( $experience as $item ) : ?>
							<li class="webmz-teacher-single__list-item"><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $specialties ) ) : ?>
				<div class="webmz-teacher-single__block">
					<h2 class="webmz-teacher-single__section-title"><?php echo esc_html( $settings['specialties_title'] ); ?></h2>
					<ul class="webmz-teacher-single__list webmz-teacher-single__list--specialties">
						<?php foreach ( $specialties as $item ) : ?>
							<li class="webmz-teacher-single__list-item"><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}

/**
 * Single teacher related courses widget.
 */
class Teacher_Single_Courses_Widget extends Widget_Base {
	public function get_name() {
		return 'webmz-teacher-single-courses';
	}

	public function get_title() {
		return esc_html__( 'دوره‌های مدرس', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-products';
	}

	public function get_categories() {
		return array( webmz_elementor_dynamic_category_slug() );
	}

	public function get_style_depends() {
		return array( 'webmz-teachers', 'webmz-store-archive' );
	}

	public function get_script_depends() {
		$deps = array();

		if ( wp_script_is( 'webmz-tadris-widgets', 'registered' ) ) {
			$deps[] = 'webmz-tadris-widgets';
		}

		if ( wp_script_is( 'webmz-header-commerce', 'registered' ) ) {
			$deps[] = 'webmz-header-commerce';
		}

		return $deps;
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'section_title', array( 'label' => esc_html__( 'عنوان بخش', 'tadris' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'دوره‌های مدرس', 'tadris' ) ) );
		$this->add_control( 'grid_columns_desktop', array( 'label' => esc_html__( 'ستون دسکتاپ', 'tadris' ), 'type' => Controls_Manager::SELECT, 'default' => '2', 'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4' ) ) );
		$this->add_control( 'grid_columns_tablet', array( 'label' => esc_html__( 'ستون تبلت', 'tadris' ), 'type' => Controls_Manager::SELECT, 'default' => '2', 'options' => array( '1' => '1', '2' => '2', '3' => '3' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( \webmz_is_layout_editing_context() ) {
			echo '<div class="webmz-teacher-single__block"><h2 class="webmz-teacher-single__section-title">' . esc_html( $settings['section_title'] ) . '</h2><div class="webmz-editor-placeholder">' . esc_html__( 'کارت‌های دوره در این بخش نمایش داده می‌شوند.', 'tadris' ) . '</div></div>';
			return;
		}

		if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'webmz_render_product_loop_card' ) ) {
			return;
		}

		$post_id  = \webmz_get_context_post_id();
		$courses  = webmz_get_teacher_courses( $post_id );

		if ( empty( $courses ) ) {
			return;
		}
		?>
		<div class="webmz-teacher-single__block">
			<h2 class="webmz-teacher-single__section-title"><?php echo esc_html( $settings['section_title'] ); ?></h2>
			<ul
				class="webmz-teacher-single__courses"
				style="--webmz-grid-columns: <?php echo esc_attr( $settings['grid_columns_desktop'] ); ?>; --webmz-grid-tablet-columns: <?php echo esc_attr( $settings['grid_columns_tablet'] ); ?>;"
			>
				<?php foreach ( $courses as $product_id ) : ?>
					<?php
					$product = wc_get_product( $product_id );
					if ( ! $product || ! $product->is_visible() ) {
						continue;
					}
					?>
					<li class="webmz-shop-product-item">
						<?php webmz_render_product_loop_card( $product ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}

/**
 * Single teacher comments widget.
 */
class Teacher_Single_Comments_Widget extends Widget_Base {
	public function get_name() {
		return 'webmz-teacher-single-comments';
	}

	public function get_title() {
		return esc_html__( 'دیدگاه‌های مدرس', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-comments';
	}

	public function get_categories() {
		return array( webmz_elementor_dynamic_category_slug() );
	}

	public function get_style_depends() {
		return array( 'webmz-teachers', 'webmz-comments-widget' );
	}

	public function get_script_depends() {
		return array( 'webmz-comments-widget' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control(
			'section_title',
			array(
				'label'   => esc_html__( 'عنوان بخش', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'دیدگاه کاربران', 'tadris' ),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( \webmz_is_layout_editing_context() ) {
			echo '<div class="webmz-teacher-single__block webmz-teacher-single__comments"><div class="webmz-editor-placeholder">' . esc_html__( 'ویجت دیدگاه ایجکس در این بخش نمایش داده می‌شود.', 'tadris' ) . '</div></div>';
			return;
		}

		$post_id = \webmz_get_context_post_id();

		if ( ! $post_id || WEBMZ_TEACHER_POST_TYPE !== get_post_type( $post_id ) ) {
			return;
		}

		echo webmz_render_teacher_single_comments( $post_id, array( 'widget_title' => $settings['section_title'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
