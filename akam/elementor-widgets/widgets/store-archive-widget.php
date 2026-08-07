<?php
/**
 * Store archive Elementor widget with Ajax filters.
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
 * Archive store widget with sidebar filters and responsive product grid.
 */
class Store_Archive_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'webmz-store-archive';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'آرشیو فروشگاه', 'tadris' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-products-archive';
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
		return array( 'shop', 'store', 'archive', 'product', 'filter', 'woocommerce', 'فروشگاه', 'آرشیو', 'محصول', 'فیلتر' );
	}

	/**
	 * Style dependencies.
	 *
	 * @return array<int,string>
	 */
	public function get_style_depends() {
		return array( 'webmz-store-archive' );
	}

	/**
	 * Script dependencies.
	 *
	 * @return array<int,string>
	 */
	public function get_script_depends() {
		$deps = array( 'webmz-store-archive' );

		if ( wp_script_is( 'webmz-tadris-widgets', 'registered' ) ) {
			$deps[] = 'webmz-tadris-widgets';
		}

		return $deps;
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
					'{{WRAPPER}} .webmz-store-archive__heading' => 'text-align: {{VALUE}};',
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
				'label'     => esc_html__( 'متن جستجو (سایدبار)', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'جستجو', 'tadris' ),
				'condition' => array(
					'show_filters' => 'yes',
				),
			)
		);

		$this->add_control(
			'main_search_placeholder',
			array(
				'label'   => esc_html__( 'متن جستجو (بالای لیست)', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'جستجوی محصولات...', 'tadris' ),
			)
		);

		$this->add_control(
			'sort_label',
			array(
				'label'   => esc_html__( 'عنوان مرتب‌سازی', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مرتب‌سازی:', 'tadris' ),
			)
		);

		$this->add_control(
			'price_filter_label',
			array(
				'label'     => esc_html__( 'عنوان فیلتر قیمت', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'فیلتر قیمت', 'tadris' ),
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
				'default'   => esc_html__( 'دسته‌بندی محصولات', 'tadris' ),
				'condition' => array(
					'show_filters' => 'yes',
				),
			)
		);

		$this->add_control(
			'active_filters_label',
			array(
				'label'     => esc_html__( 'عنوان فیلترهای فعال', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'فیلترهای فعال', 'tadris' ),
				'condition' => array(
					'show_filters' => 'yes',
				),
			)
		);

		$this->add_control(
			'clear_all_filters_text',
			array(
				'label'     => esc_html__( 'متن حذف همه فیلترها', 'tadris' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'حذف همه فیلترها', 'tadris' ),
				'condition' => array(
					'show_filters' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'products_section',
			array(
				'label' => esc_html__( 'نمایش محصولات', 'tadris' ),
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
				'default' => '3',
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
					'{{WRAPPER}} .webmz-store-archive__grid' => '--webmz-grid-gap: {{SIZE}}{{UNIT}};',
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
					'pagination'      => esc_html__( 'صفحه‌بندی', 'tadris' ),
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

		$this->webmz_register_title_tag_control( 'card_title_tag', esc_html__( 'تگ عنوان کارت', 'tadris' ) );

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'متن دکمه محصول ساده', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مشاهده محصول', 'tadris' ),
			)
		);

		$this->add_control(
			'variable_button_text',
			array(
				'label'   => esc_html__( 'متن دکمه محصول متغیر', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'انتخاب گزینه‌ها', 'tadris' ),
			)
		);

		$this->add_control(
			'unavailable_button_text',
			array(
				'label'   => esc_html__( 'متن دکمه محصول ناموجود', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'ناموجود', 'tadris' ),
			)
		);

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
					'{{WRAPPER}} .webmz-store-archive__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'selector' => '{{WRAPPER}} .webmz-store-archive__heading',
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
		return ' class="webmz-store-archive__sidebar webmz-sticky-container-element" data-webmz-sticky-container="1" data-webmz-sticky-container-stay="1" data-webmz-sticky-container-offset="24"';
	}

	/**
	 * Render sort select options.
	 *
	 * @param string $current Current orderby value.
	 * @return void
	 */
	private function render_sort_options( $current = 'menu_order' ) {
		$options = array(
			'menu_order' => esc_html__( 'مرتب‌سازی پیش‌فرض', 'tadris' ),
			'date'       => esc_html__( 'جدیدترین', 'tadris' ),
			'popularity' => esc_html__( 'پرفروش‌ترین', 'tadris' ),
			'rating'     => esc_html__( 'بالاترین امتیاز', 'tadris' ),
			'price'      => esc_html__( 'ارزان‌ترین', 'tadris' ),
			'price-desc' => esc_html__( 'گران‌ترین', 'tadris' ),
		);

		foreach ( $options as $value => $label ) {
			printf(
				'<option value="%1$s"%2$s>%3$s</option>',
				esc_attr( $value ),
				selected( $current, $value, false ),
				esc_html( $label )
			);
		}
	}

	/**
	 * Render toolbar with sort and search.
	 *
	 * @param array<string,mixed> $settings  Widget settings.
	 * @param array<string,mixed> $context   Archive context.
	 * @param bool                $is_editor Editor preview flag.
	 * @return void
	 */
	private function render_toolbar( $settings, $context, $is_editor = false ) {
		$sort_label = ! empty( $settings['sort_label'] ) ? $settings['sort_label'] : esc_html__( 'مرتب‌سازی:', 'tadris' );
		?>
		<div class="webmz-store-archive__toolbar">
			<label class="webmz-store-archive__sort">
				<span class="webmz-store-archive__sort-label"><?php echo esc_html( $sort_label ); ?></span>
				<select class="webmz-store-archive__sort-select"<?php echo $is_editor ? ' disabled' : ''; ?>>
					<?php $this->render_sort_options(); ?>
				</select>
			</label>

			<label class="webmz-store-archive__main-search">
				<span class="webmz-store-archive__search-icon" aria-hidden="true"></span>
				<input
					type="search"
					class="webmz-store-archive__main-search-input"
					placeholder="<?php echo esc_attr( $settings['main_search_placeholder'] ); ?>"
					<?php if ( $is_editor ) : ?>
						disabled
					<?php else : ?>
						value="<?php echo esc_attr( 'search' === $context['type'] ? $context['search'] : '' ); ?>"
						autocomplete="off"
					<?php endif; ?>
				>
			</label>
		</div>
		<?php
	}

	/**
	 * Render active filters summary box.
	 *
	 * @param array<string,mixed> $settings  Widget settings.
	 * @param bool                $is_editor Editor preview flag.
	 * @return void
	 */
	private function render_active_filters_box( $settings, $is_editor = false ) {
		$title     = ! empty( $settings['active_filters_label'] ) ? $settings['active_filters_label'] : esc_html__( 'فیلترهای فعال', 'tadris' );
		$clear_txt = ! empty( $settings['clear_all_filters_text'] ) ? $settings['clear_all_filters_text'] : esc_html__( 'حذف همه فیلترها', 'tadris' );
		?>
		<div class="webmz-store-archive__active-filters-card webmz-store-archive__active-filters"<?php echo $is_editor ? '' : ' hidden'; ?>>
			<div class="webmz-store-archive__active-filters-head">
				<h3 class="webmz-store-archive__active-filters-title"><?php echo esc_html( $title ); ?></h3>
				<button
					type="button"
					class="webmz-store-archive__active-filters-clear"
					<?php echo $is_editor ? 'disabled' : ''; ?>
				><?php echo esc_html( $clear_txt ); ?></button>
			</div>
			<ul class="webmz-store-archive__active-filters-list" role="list">
				<?php if ( $is_editor ) : ?>
					<li class="webmz-store-archive__active-filter">
						<span class="webmz-store-archive__active-filter-label"><?php esc_html_e( 'مرتب‌سازی:', 'tadris' ); ?></span>
						<span class="webmz-store-archive__active-filter-value"><?php esc_html_e( 'جدیدترین', 'tadris' ); ?></span>
					</li>
					<li class="webmz-store-archive__active-filter">
						<span class="webmz-store-archive__active-filter-label"><?php esc_html_e( 'دسته‌بندی:', 'tadris' ); ?></span>
						<span class="webmz-store-archive__active-filter-value"><?php esc_html_e( 'دسته نمونه', 'tadris' ); ?></span>
					</li>
				<?php endif; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render filter sidebar markup.
	 *
	 * @param array<string,mixed> $settings    Widget settings.
	 * @param array<string,mixed> $context     Archive context.
	 * @param bool                $is_editor   Whether editor preview is rendered.
	 * @return void
	 */
	private function render_filter_sidebar( $settings, $context, $is_editor = false ) {
		$bounds = webmz_store_archive_get_price_bounds();
		?>
		<aside<?php echo $this->get_sidebar_sticky_attrs(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-label="<?php esc_attr_e( 'فیلتر محصولات', 'tadris' ); ?>">
			<div class="webmz-store-archive__filter-card">
				<label class="webmz-store-archive__search">
					<span class="webmz-store-archive__search-icon" aria-hidden="true"></span>
					<input
						type="search"
						class="webmz-store-archive__search-input"
						placeholder="<?php echo esc_attr( $settings['search_placeholder'] ); ?>"
						<?php if ( $is_editor ) : ?>
							disabled
						<?php else : ?>
							value="<?php echo esc_attr( 'search' === $context['type'] ? $context['search'] : '' ); ?>"
							autocomplete="off"
						<?php endif; ?>
					>
				</label>

				<?php echo webmz_store_archive_render_price_filter_html( $settings, (int) $bounds['min'], (int) $bounds['max'], $is_editor ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<div class="webmz-store-archive__terms-wrap">
					<?php echo webmz_store_archive_render_filter_sidebar_terms( $settings, $context ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
			<?php $this->render_active_filters_box( $settings, $is_editor ); ?>
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

		return esc_html__( 'آرشیو محصولات', 'tadris' );
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

		$bounds = webmz_store_archive_get_price_bounds();

		$args = webmz_store_archive_build_query_args(
			array(
				'posts_per_page' => isset( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : 6,
				'page'           => max( 1, (int) get_query_var( 'paged', 1 ) ),
				'search'         => 'search' === $context['type'] ? $context['search'] : '',
				'taxonomy'       => isset( $context['taxonomy'] ) ? $context['taxonomy'] : 'product_cat',
				'term_id'        => $active_term,
				'min_price'      => $bounds['min'],
				'max_price'      => $bounds['max'],
				'orderby'        => 'menu_order',
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
		$title_tag     = \webmz_sanitize_heading_tag( isset( $settings['title_tag'] ) ? $settings['title_tag'] : 'h1' );
		$gap           = isset( $settings['grid_gap']['size'] ) ? absint( $settings['grid_gap']['size'] ) : 24;
		$preview_context = array(
			'type'    => 'shop',
			'term_id' => 0,
			'search'  => '',
		);
		$show_filters  = $this->should_show_filters( $settings );
		?>
		<div class="webmz-store-archive webmz-store-archive--editor">
			<<?php echo esc_attr( $title_tag ); ?> class="webmz-store-archive__heading"><?php esc_html_e( 'عنوان آرشیو فروشگاه', 'tadris' ); ?></<?php echo esc_attr( $title_tag ); ?>>
			<div class="webmz-store-archive__layout<?php echo $show_filters ? '' : ' webmz-store-archive__layout--no-filters'; ?>">
				<?php if ( $show_filters ) : ?>
					<?php $this->render_filter_sidebar( $settings, $preview_context, true ); ?>
				<?php endif; ?>
				<div class="webmz-store-archive__main">
					<?php $this->render_toolbar( $settings, $preview_context, true ); ?>
					<div
						class="webmz-store-archive__grid tadris-products-grid webmz-loop-grid"
						style="--webmz-grid-columns: <?php echo esc_attr( $settings['grid_columns_desktop'] ); ?>; --webmz-grid-tablet-columns: <?php echo esc_attr( $settings['grid_columns_tablet'] ); ?>; --webmz-grid-mobile-columns: <?php echo esc_attr( $settings['grid_columns_mobile'] ); ?>; --webmz-grid-gap: <?php echo esc_attr( $gap ); ?>px;"
					>
						<article class="tadris-product-type-1 tadris-product-type-1--store-archive">
							<figure class="product-type-1-figure"><span class="webmz-store-archive__placeholder" aria-hidden="true">د</span></figure>
							<div class="product-type-1-body">
								<span class="product-type-1-category"><?php esc_html_e( 'دسته نمونه', 'tadris' ); ?></span>
								<header class="product-type-1-header"><h2 class="product-type-1-header-tag"><?php esc_html_e( 'عنوان نمونه محصول', 'tadris' ); ?></h2></header>
								<p class="product-type-1-excerpt"><?php esc_html_e( 'خلاصه کوتاه محصول در این بخش نمایش داده می‌شود.', 'tadris' ); ?></p>
							</div>
							<footer class="product-type-1-footer">
								<div class="tadris-price"><div class="tadris-price-amount">۱,۲۵۰,۰۰۰</div></div>
								<div class="type-1-add-to-cart"><span class="type-1-add-to-cart-btn"><?php esc_html_e( 'مشاهده محصول', 'tadris' ); ?></span></div>
							</footer>
						</article>
						<article class="tadris-product-type-1 tadris-product-type-1--store-archive">
							<figure class="product-type-1-figure"><span class="webmz-store-archive__placeholder" aria-hidden="true">د</span></figure>
							<div class="product-type-1-body">
								<span class="product-type-1-category"><?php esc_html_e( 'فایل', 'tadris' ); ?></span>
								<header class="product-type-1-header"><h2 class="product-type-1-header-tag"><?php esc_html_e( 'محصول نمونه دوم', 'tadris' ); ?></h2></header>
								<p class="product-type-1-excerpt"><?php esc_html_e( 'توضیح کوتاه برای هر نوع محصول.', 'tadris' ); ?></p>
							</div>
							<footer class="product-type-1-footer">
								<div class="tadris-price"><div class="tadris-free-price"><?php esc_html_e( 'رایگان', 'tadris' ); ?></div></div>
								<div class="type-1-add-to-cart"><span class="type-1-add-to-cart-btn"><?php esc_html_e( 'مشاهده محصول', 'tadris' ); ?></span></div>
							</footer>
						</article>
					</div>
				</div>
			</div>
			<?php if ( isset( $settings['show_description'] ) && 'yes' === $settings['show_description'] ) : ?>
				<div class="webmz-store-archive__description"><?php esc_html_e( 'توضیحات دسته یا برچسب در این باکس نمایش داده می‌شود.', 'tadris' ); ?></div>
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
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<div class="webmz-editor-placeholder">' . esc_html__( 'برای استفاده از این ویجت، ووکامرس باید فعال باشد.', 'tadris' ) . '</div>';
			return;
		}

		$settings = $this->get_settings_for_display();

		if ( $this->should_render_editor_preview() ) {
			$this->render_editor_preview( $settings );
			return;
		}

		$context         = webmz_store_archive_get_context();
		$query           = $this->get_initial_query( $settings, $context );
		$title_tag       = \webmz_sanitize_heading_tag( isset( $settings['title_tag'] ) ? $settings['title_tag'] : 'h1' );
		$heading         = $this->resolve_heading_text( $settings, $context );
		$active_term     = 'category' === $context['type'] ? (int) $context['term_id'] : 0;
		$gap             = isset( $settings['grid_gap']['size'] ) ? absint( $settings['grid_gap']['size'] ) : 24;
		$load_mode       = isset( $settings['load_mode'] ) ? sanitize_key( $settings['load_mode'] ) : 'pagination';
		$current_page    = max( 1, (int) get_query_var( 'paged', 1 ) );
		$bounds          = webmz_store_archive_get_price_bounds();
		$show_filters    = $this->should_show_filters( $settings );

		$config = array(
			'postsPerPage'          => isset( $settings['posts_per_page'] ) ? absint( $settings['posts_per_page'] ) : 6,
			'loadMode'              => in_array( $load_mode, array( 'pagination', 'infinite_scroll' ), true ) ? $load_mode : 'pagination',
			'infiniteOffset'        => isset( $settings['infinite_offset'] ) ? absint( $settings['infinite_offset'] ) : 240,
			'taxonomy'              => 'product_cat',
			'termId'                => $active_term,
			'cardTitleTag'          => $this->webmz_get_title_tag( $settings, 'card_title_tag' ),
			'buttonText'            => ! empty( $settings['button_text'] ) ? $settings['button_text'] : '',
			'variableButtonText'    => ! empty( $settings['variable_button_text'] ) ? $settings['variable_button_text'] : '',
			'unavailableButtonText' => ! empty( $settings['unavailable_button_text'] ) ? $settings['unavailable_button_text'] : '',
			'currentPage'           => $current_page,
			'maxPages'              => (int) $query->max_num_pages,
			'baseUrl'               => webmz_store_archive_get_base_url(),
			'minPrice'              => (int) $bounds['min'],
			'maxPrice'              => (int) $bounds['max'],
			'orderby'               => 'menu_order',
			'activeFiltersTitle'    => ! empty( $settings['active_filters_label'] ) ? $settings['active_filters_label'] : esc_html__( 'فیلترهای فعال', 'tadris' ),
			'clearAllFiltersText'   => ! empty( $settings['clear_all_filters_text'] ) ? $settings['clear_all_filters_text'] : esc_html__( 'حذف همه فیلترها', 'tadris' ),
			'chipSortLabel'         => esc_html__( 'مرتب‌سازی', 'tadris' ),
			'chipSearchLabel'       => esc_html__( 'جستجو', 'tadris' ),
			'chipPriceLabel'        => esc_html__( 'قیمت', 'tadris' ),
			'chipCategoryLabel'     => esc_html__( 'دسته‌بندی', 'tadris' ),
		);
		?>
		<div
			class="webmz-store-archive<?php echo $show_filters ? '' : ' webmz-store-archive--no-filters'; ?>"
			data-webmz-store-archive="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
		>
			<<?php echo esc_attr( $title_tag ); ?> class="webmz-store-archive__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_attr( $title_tag ); ?>>

			<div class="webmz-store-archive__layout<?php echo $show_filters ? '' : ' webmz-store-archive__layout--no-filters'; ?>">
				<?php if ( $show_filters ) : ?>
					<?php $this->render_filter_sidebar( $settings, $context ); ?>
				<?php endif; ?>

					<div class="webmz-store-archive__main">
					<?php
					$has_products = ( $query instanceof \WP_Query && $query->post_count > 0 );

					if ( function_exists( 'webmz_woocommerce_maybe_do_before_shop_loop' ) ) {
						webmz_woocommerce_maybe_do_before_shop_loop();
					} else {
						do_action( 'woocommerce_before_shop_loop' );
					}
					?>
					<?php $this->render_toolbar( $settings, $context ); ?>

					<div
						class="webmz-store-archive__status"
						role="status"
						aria-live="polite"
						aria-atomic="true"
						hidden
					></div>

					<div
						class="webmz-store-archive__grid tadris-products-grid webmz-loop-grid"
						style="--webmz-grid-columns: <?php echo esc_attr( $settings['grid_columns_desktop'] ); ?>; --webmz-grid-tablet-columns: <?php echo esc_attr( $settings['grid_columns_tablet'] ); ?>; --webmz-grid-mobile-columns: <?php echo esc_attr( $settings['grid_columns_mobile'] ); ?>; --webmz-grid-gap: <?php echo esc_attr( $gap ); ?>px;"
					>
						<?php echo webmz_store_archive_render_products_html( $query, $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>

					<?php if ( 'pagination' === $load_mode ) : ?>
						<?php echo webmz_store_archive_render_pagination_html( $query, $current_page ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php else : ?>
						<div class="webmz-store-archive__infinite-sentinel" aria-hidden="true"></div>
						<div class="webmz-store-archive__infinite-loader" hidden>
							<span class="webmz-store-archive__spinner" aria-hidden="true"></span>
							<span><?php esc_html_e( 'در حال بارگذاری...', 'tadris' ); ?></span>
						</div>
					<?php endif; ?>

					<?php
					if ( $has_products ) {
						if ( function_exists( 'webmz_woocommerce_do_after_shop_loop' ) ) {
							webmz_woocommerce_do_after_shop_loop( false );
						} else {
							do_action( 'woocommerce_after_shop_loop' );
						}
					} elseif ( function_exists( 'webmz_woocommerce_do_no_products_found_hooks_only' ) ) {
						webmz_woocommerce_do_no_products_found_hooks_only();
					} else {
						do_action( 'woocommerce_no_products_found' );
					}
					?>
				</div>
			</div>

			<?php if ( isset( $settings['show_description'] ) && 'yes' === $settings['show_description'] ) : ?>
				<?php
				$description = ! empty( $context['description'] ) ? $context['description'] : '';
				if ( $description ) :
					?>
					<div class="webmz-store-archive__description">
						<?php echo wp_kses_post( wpautop( $description ) ); ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}
}
