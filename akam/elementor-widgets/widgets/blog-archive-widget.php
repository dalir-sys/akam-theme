<?php
/**
 * Blog archive Elementor widget with Ajax filters.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use WP_Query;

defined( 'ABSPATH' ) || exit;

/**
 * Archive blog widget with sidebar filters and responsive post grid.
 */
class Blog_Archive_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'webmz-blog-archive';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'آرشیو بلاگ', 'tadris' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-archive-posts';
	}

	/**
	 * Widget categories.
	 *
	 * @return array<int,string>
	 */
	public function get_categories() {
		return array( webmz_elementor_dynamic_category_slug() );
	}

	/**
	 * Widget keywords.
	 *
	 * @return array<int,string>
	 */
	public function get_keywords() {
		return array( 'blog', 'archive', 'filter', 'search', 'category', 'tag', 'آرشیو', 'بلاگ', 'فیلتر' );
	}

	/**
	 * Style dependencies.
	 *
	 * @return array<int,string>
	 */
	public function get_style_depends() {
		return array( 'webmz-blog-archive' );
	}

	/**
	 * Script dependencies.
	 *
	 * @return array<int,string>
	 */
	public function get_script_depends() {
		return array( 'webmz-blog-archive' );
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			array(
				'label' => esc_html__( 'عنوان بخش', 'tadris' ),
			)
		);

		$this->add_control(
			'title_text',
			array(
				'label'       => esc_html__( 'متن عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'خالی = عنوان داینامیک آرشیو', 'tadris' ),
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
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
			)
		);

		$this->add_responsive_control(
			'title_align',
			array(
				'label'     => esc_html__( 'چینش عنوان', 'tadris' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'right'  => array(
						'title' => esc_html__( 'راست', 'tadris' ),
						'icon'  => 'eicon-text-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'وسط', 'tadris' ),
						'icon'  => 'eicon-text-align-center',
					),
					'left'   => array(
						'title' => esc_html__( 'چپ', 'tadris' ),
						'icon'  => 'eicon-text-align-left',
					),
				),
				'default'   => 'right',
				'selectors' => array(
					'{{WRAPPER}} .webmz-blog-archive__heading' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'filter_section',
			array(
				'label' => esc_html__( 'فیلترها', 'tadris' ),
			)
		);

		$this->add_control(
			'show_filters',
			array(
				'label'        => esc_html__( 'نمایش فیلترها', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'search_placeholder',
			array(
				'label'     => esc_html__( 'متن جستجو', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'جستجو', 'tadris' ),
				'condition' => array(
					'show_filters' => 'yes',
				),
			)
		);

		$this->add_control(
			'category_label',
			array(
				'label'     => esc_html__( 'عنوان لیست دسته‌بندی', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'دسته بندی مقالات', 'tadris' ),
				'condition' => array(
					'show_filters' => 'yes',
				),
			)
		);

		$this->add_control(
			'tag_label',
			array(
				'label'     => esc_html__( 'عنوان لیست برچسب', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'برچسب مقالات', 'tadris' ),
				'condition' => array(
					'show_filters' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'posts_section',
			array(
				'label' => esc_html__( 'نمایش مطالب', 'tadris' ),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'تعداد در هر صفحه', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 1,
				'max'     => 48,
			)
		);

		$this->add_control(
			'grid_columns_desktop',
			array(
				'label'   => esc_html__( 'ستون دسکتاپ', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '2',
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
						'min' => 8,
						'max' => 48,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-blog-archive__grid' => '--webmz-grid-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'load_mode',
			array(
				'label'   => esc_html__( 'بارگذاری بیشتر', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'pagination',
				'options' => array(
					'pagination'     => esc_html__( 'صفحه‌بندی', 'tadris' ),
					'infinite_scroll' => esc_html__( 'اسکرول بی‌نهایت', 'tadris' ),
				),
			)
		);

		$this->add_control(
			'infinite_offset',
			array(
				'label'     => esc_html__( 'فاصله تا انتهای صفحه (px)', 'tadris' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 240,
				'min'       => 50,
				'max'       => 1000,
				'condition' => array(
					'load_mode' => 'infinite_scroll',
				),
			)
		);

		$this->add_control(
			'read_more_text',
			array(
				'label'   => esc_html__( 'متن دکمه مطالعه', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مطالعه بیشتر', 'tadris' ),
			)
		);

		$this->webmz_register_title_tag_control( 'card_title_tag', esc_html__( 'تگ عنوان کارت', 'tadris' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'description_section',
			array(
				'label' => esc_html__( 'توضیحات آرشیو', 'tadris' ),
			)
		);

		$this->add_control(
			'show_description',
			array(
				'label'        => esc_html__( 'نمایش توضیحات', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_heading',
			array(
				'label' => esc_html__( 'استایل عنوان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-blog-archive__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .webmz-blog-archive__heading',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Whether Elementor editor preview should be rendered.
	 *
	 * @return bool
	 */
	private function should_render_editor_preview() {
		if ( \webmz_is_layout_editing_context() ) {
			return true;
		}

		if ( class_exists( '\Elementor\Plugin' ) ) {
			$plugin = \Elementor\Plugin::$instance;
			if ( isset( $plugin->editor ) && method_exists( $plugin->editor, 'is_edit_mode' ) && $plugin->editor->is_edit_mode() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Whether filter sidebar should be rendered.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return bool
	 */
	private function should_show_filters( $settings ) {
		return ! isset( $settings['show_filters'] ) || 'yes' === $settings['show_filters'];
	}

	/**
	 * Sticky sidebar attributes for filter column.
	 *
	 * @return string
	 */
	private function get_sidebar_sticky_attrs() {
		return ' class="webmz-blog-archive__sidebar webmz-sticky-container-element" data-webmz-sticky-container="1" data-webmz-sticky-container-stay="1" data-webmz-sticky-container-offset="24"';
	}

	/**
	 * Render filter sidebar markup.
	 *
	 * @param array<string,mixed> $settings    Widget settings.
	 * @param array<string,mixed> $context     Archive context.
	 * @param string              $active_tab  Active tab slug.
	 * @param bool                $is_editor   Whether editor preview is rendered.
	 * @return void
	 */
	private function render_filter_sidebar( $settings, $context, $active_tab, $is_editor = false ) {
		?>
		<aside<?php echo $this->get_sidebar_sticky_attrs(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-label="<?php esc_attr_e( 'فیلتر مطالب', 'tadris' ); ?>">
			<div class="webmz-blog-archive__filter-card">
				<label class="webmz-blog-archive__search">
					<span class="webmz-blog-archive__search-icon" aria-hidden="true"></span>
					<input
						type="search"
						class="webmz-blog-archive__search-input"
						placeholder="<?php echo esc_attr( $settings['search_placeholder'] ); ?>"
						<?php if ( $is_editor ) : ?>
							disabled
						<?php else : ?>
							value="<?php echo esc_attr( 'search' === $context['type'] ? $context['search'] : '' ); ?>"
							autocomplete="off"
						<?php endif; ?>
					>
				</label>

				<div class="webmz-blog-archive__terms-wrap">
					<?php echo webmz_blog_archive_render_filter_sidebar( $settings, $context, $active_tab ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</aside>
		<?php
	}

	/**
	 * Resolve section heading text.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @param array<string,mixed> $context  Archive context.
	 * @return string
	 */
	private function resolve_heading_text( $settings, $context ) {
		if ( ! empty( $settings['title_text'] ) ) {
			return (string) $settings['title_text'];
		}

		if ( ! empty( $context['archive_title'] ) ) {
			return (string) $context['archive_title'];
		}

		return esc_html__( 'آرشیو مقالات', 'tadris' );
	}

	/**
	 * Build initial query for first render.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @param array<string,mixed> $context  Archive context.
	 * @return WP_Query
	 */
	private function get_initial_query( $settings, $context ) {
		$active_term = 0;

		if ( in_array( $context['type'], array( 'category', 'tag' ), true ) ) {
			$active_term = (int) $context['term_id'];
		}

		$args = webmz_blog_archive_build_query_args(
			array(
				'posts_per_page' => isset( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : 6,
				'page'           => max( 1, (int) get_query_var( 'paged', 1 ) ),
				'search'         => 'search' === $context['type'] ? $context['search'] : '',
				'taxonomy'       => isset( $context['taxonomy'] ) ? $context['taxonomy'] : 'category',
				'term_id'        => $active_term,
			)
		);

		return new WP_Query( $args );
	}

	/**
	 * Render editor placeholders.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_editor_preview( $settings ) {
		$title_tag = \webmz_sanitize_heading_tag( isset( $settings['title_tag'] ) ? $settings['title_tag'] : 'h1' );
		$gap       = isset( $settings['grid_gap']['size'] ) ? absint( $settings['grid_gap']['size'] ) : 24;
		$card_settings = array(
			'card_title_tag' => $this->webmz_get_title_tag( $settings, 'card_title_tag' ),
			'read_more_text' => ! empty( $settings['read_more_text'] ) ? $settings['read_more_text'] : esc_html__( 'مطالعه بیشتر', 'tadris' ),
		);
		$preview_query = new WP_Query(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 2,
				'no_found_rows'  => true,
			)
		);
		$preview_context = array(
			'type'    => 'blog',
			'term_id' => 0,
		);
		$show_filters    = $this->should_show_filters( $settings );
		?>
		<div class="webmz-blog-archive webmz-blog-archive--editor">
			<<?php echo esc_attr( $title_tag ); ?> class="webmz-blog-archive__heading"><?php esc_html_e( 'عنوان آرشیو بلاگ', 'tadris' ); ?></<?php echo esc_attr( $title_tag ); ?>>
			<div class="webmz-blog-archive__layout<?php echo $show_filters ? '' : ' webmz-blog-archive__layout--no-filters'; ?>">
				<?php if ( $show_filters ) : ?>
					<?php $this->render_filter_sidebar( $settings, $preview_context, 'category', true ); ?>
				<?php endif; ?>
				<div class="webmz-blog-archive__main">
					<div
						class="webmz-blog-archive__grid webmz-loop-grid"
						style="--webmz-grid-columns: <?php echo esc_attr( $settings['grid_columns_desktop'] ); ?>; --webmz-grid-tablet-columns: <?php echo esc_attr( $settings['grid_columns_tablet'] ); ?>; --webmz-grid-mobile-columns: <?php echo esc_attr( $settings['grid_columns_mobile'] ); ?>; --webmz-grid-gap: <?php echo esc_attr( $gap ); ?>px;"
					>
						<?php
						if ( $preview_query->have_posts() ) {
							echo webmz_blog_archive_render_posts_html( $preview_query, $card_settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							for ( $i = 0; $i < 2; $i++ ) :
								?>
								<article class="webmz-blog-archive-card">
									<div class="webmz-blog-archive-card__image"><span class="webmz-blog-archive-card__placeholder" aria-hidden="true">م</span></div>
									<div class="webmz-blog-archive-card__body">
										<div class="webmz-blog-archive-card__meta">
											<span class="webmz-blog-archive-card__author"><?php esc_html_e( 'نویسنده نمونه', 'tadris' ); ?></span>
											<span class="webmz-blog-archive-card__date"><?php esc_html_e( '۸ دی ۱۴۰۴', 'tadris' ); ?></span>
										</div>
										<h3 class="webmz-blog-archive-card__title"><?php esc_html_e( 'عنوان نمونه مقاله', 'tadris' ); ?></h3>
										<div class="webmz-blog-archive-card__excerpt"><p><?php esc_html_e( 'خلاصه مقاله در این بخش نمایش داده می‌شود.', 'tadris' ); ?></p></div>
										<div class="webmz-blog-archive-card__footer">
											<span class="webmz-blog-archive-card__more"><?php esc_html_e( 'مطالعه بیشتر', 'tadris' ); ?></span>
											<span class="webmz-blog-archive-card__tag"><?php esc_html_e( 'دسته نمونه', 'tadris' ); ?></span>
										</div>
									</div>
								</article>
								<?php
							endfor;
						}
						?>
					</div>
				</div>
			</div>
			<?php if ( isset( $settings['show_description'] ) && 'yes' === $settings['show_description'] ) : ?>
				<div class="webmz-blog-archive__description"><?php esc_html_e( 'توضیحات دسته یا برچسب در این باکس نمایش داده می‌شود.', 'tadris' ); ?></div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( $this->should_render_editor_preview() ) {
			$this->render_editor_preview( $settings );
			return;
		}

		$context       = webmz_blog_archive_get_context();
		$query         = $this->get_initial_query( $settings, $context );
		$title_tag     = \webmz_sanitize_heading_tag( isset( $settings['title_tag'] ) ? $settings['title_tag'] : 'h1' );
		$heading       = $this->resolve_heading_text( $settings, $context );
		$active_tab    = webmz_blog_archive_get_initial_tab( $context );
		$active_term   = in_array( $context['type'], array( 'category', 'tag' ), true ) ? (int) $context['term_id'] : 0;
		$active_taxonomy = isset( $context['taxonomy'] ) ? $context['taxonomy'] : 'category';
		$gap          = isset( $settings['grid_gap']['size'] ) ? absint( $settings['grid_gap']['size'] ) : 24;
		$load_mode    = isset( $settings['load_mode'] ) ? sanitize_key( $settings['load_mode'] ) : 'pagination';
		$current_page = max( 1, (int) get_query_var( 'paged', 1 ) );
		$card_settings = array(
			'card_title_tag' => $this->webmz_get_title_tag( $settings, 'card_title_tag' ),
			'read_more_text' => ! empty( $settings['read_more_text'] ) ? $settings['read_more_text'] : esc_html__( 'مطالعه بیشتر', 'tadris' ),
		);
		$show_filters  = $this->should_show_filters( $settings );

		$config = array(
			'postsPerPage'   => isset( $settings['posts_per_page'] ) ? absint( $settings['posts_per_page'] ) : 6,
			'loadMode'       => in_array( $load_mode, array( 'pagination', 'infinite_scroll' ), true ) ? $load_mode : 'pagination',
			'infiniteOffset' => isset( $settings['infinite_offset'] ) ? absint( $settings['infinite_offset'] ) : 240,
			'taxonomy'       => $active_taxonomy,
			'activeTab'      => $active_tab,
			'termId'         => $active_term,
			'cardTitleTag'   => $card_settings['card_title_tag'],
			'readMoreText'   => $card_settings['read_more_text'],
			'currentPage'    => $current_page,
			'maxPages'       => (int) $query->max_num_pages,
			'baseUrl'        => webmz_blog_archive_get_base_url(),
		);
		?>
		<div
			class="webmz-blog-archive<?php echo $show_filters ? '' : ' webmz-blog-archive--no-filters'; ?>"
			data-webmz-blog-archive="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
		>
			<<?php echo esc_attr( $title_tag ); ?> class="webmz-blog-archive__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_attr( $title_tag ); ?>>

			<div class="webmz-blog-archive__layout<?php echo $show_filters ? '' : ' webmz-blog-archive__layout--no-filters'; ?>">
				<?php if ( $show_filters ) : ?>
					<?php $this->render_filter_sidebar( $settings, $context, $active_tab ); ?>
				<?php endif; ?>

				<div class="webmz-blog-archive__main">
					<div
						class="webmz-blog-archive__status"
						role="status"
						aria-live="polite"
						aria-atomic="true"
						hidden
					></div>

					<div
						class="webmz-blog-archive__grid webmz-loop-grid"
						style="--webmz-grid-columns: <?php echo esc_attr( $settings['grid_columns_desktop'] ); ?>; --webmz-grid-tablet-columns: <?php echo esc_attr( $settings['grid_columns_tablet'] ); ?>; --webmz-grid-mobile-columns: <?php echo esc_attr( $settings['grid_columns_mobile'] ); ?>; --webmz-grid-gap: <?php echo esc_attr( $gap ); ?>px;"
					>
						<?php echo webmz_blog_archive_render_posts_html( $query, $card_settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>

					<?php if ( 'pagination' === $load_mode ) : ?>
						<?php echo webmz_blog_archive_render_pagination_html( $query, $current_page ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php else : ?>
						<div class="webmz-blog-archive__infinite-sentinel" aria-hidden="true"></div>
						<div class="webmz-blog-archive__infinite-loader" hidden>
							<span class="webmz-blog-archive__spinner" aria-hidden="true"></span>
							<span><?php esc_html_e( 'در حال بارگذاری...', 'tadris' ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( isset( $settings['show_description'] ) && 'yes' === $settings['show_description'] ) : ?>
				<?php
				$description = ! empty( $context['description'] ) ? $context['description'] : '';
				if ( $description ) :
					?>
					<div class="webmz-blog-archive__description">
						<?php echo wp_kses_post( wpautop( $description ) ); ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}
}
