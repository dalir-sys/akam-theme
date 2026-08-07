<?php
/**
 * Elementor layout post type used by the free theme-builder layer.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get layout types used by the theme options and metabox.
 *
 * @return array<string,string>
 */
function webmz_get_layout_types() {
	return array(
		'header'          => esc_html__( 'هدر وبسایت', 'tadris' ),
		'footer'          => esc_html__( 'فوتر وبسایت', 'tadris' ),
		'single_post'     => esc_html__( 'سینگل نوشته', 'tadris' ),
		'single_teacher'  => esc_html__( 'سینگل مدرس', 'tadris' ),
		'single_product'  => esc_html__( 'سینگل محصول', 'tadris' ),
		'archive_post'    => esc_html__( 'آرشیو نوشته‌ها', 'tadris' ),
		'archive_teacher' => esc_html__( 'آرشیو مدرسین', 'tadris' ),
		'archive_product' => esc_html__( 'آرشیو محصولات', 'tadris' ),
		'page'            => esc_html__( 'صفحه برگه‌ها', 'tadris' ),
		'search'          => esc_html__( 'صفحه جستجو', 'tadris' ),
		'404'             => esc_html__( 'صفحه 404', 'tadris' ),
	);
}

/**
 * Register the layout post type.
 *
 * @return void
 */
function webmz_register_layout_post_type() {
	$labels = array(
		'name'               => esc_html__( 'لایه‌بندی‌ها', 'tadris' ),
		'singular_name'      => esc_html__( 'لایه‌بندی', 'tadris' ),
		'add_new'            => esc_html__( 'افزودن لایه', 'tadris' ),
		'add_new_item'       => esc_html__( 'افزودن لایه جدید', 'tadris' ),
		'edit_item'          => esc_html__( 'ویرایش لایه', 'tadris' ),
		'new_item'           => esc_html__( 'لایه جدید', 'tadris' ),
		'view_item'          => esc_html__( 'نمایش لایه', 'tadris' ),
		'search_items'       => esc_html__( 'جستجوی لایه‌ها', 'tadris' ),
		'not_found'          => esc_html__( 'لایه‌ای پیدا نشد.', 'tadris' ),
		'not_found_in_trash' => esc_html__( 'لایه‌ای در زباله‌دان نیست.', 'tadris' ),
		'menu_name'          => esc_html__( 'لایه‌بندی', 'tadris' ),
	);

	register_post_type( 'webmz_layout', array(
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_menu'        => 'webmz-options',
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-layout',
		'supports'            => array( 'title', 'editor', 'thumbnail', 'elementor' ),
		'has_archive'         => false,
		'rewrite'             => false,
		'query_var'           => true,
	) );
}
add_action( 'init', 'webmz_register_layout_post_type' );

/**
 * Register display conditions meta box.
 *
 * @return void
 */
function webmz_add_layout_conditions_meta_box() {
	add_meta_box(
		'webmz-layout-conditions',
		esc_html__( 'شرایط نمایش', 'tadris' ),
		'webmz_render_layout_conditions_meta_box',
		'webmz_layout',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_webmz_layout', 'webmz_add_layout_conditions_meta_box' );

/**
 * Print the display conditions meta box.
 *
 * @param WP_Post $post Current post.
 * @return void
 */
function webmz_render_layout_conditions_meta_box( $post ) {
	$rules       = webmz_get_layout_condition_rules( $post->ID );
	$definitions = webmz_get_layout_condition_definitions();
	$include     = $rules;
	$layout_type = get_post_meta( $post->ID, '_webmz_layout_type', true );
	$types       = webmz_get_layout_types();
	$has_type    = $layout_type && isset( $types[ $layout_type ] );

	if ( empty( $include ) ) {
		$include = array( array( 'type' => 'include', 'name' => 'general', 'object_ids' => array() ) );
	}

	wp_nonce_field( 'webmz_layout_conditions_save', 'webmz_layout_conditions_nonce' );
	wp_nonce_field( 'webmz_layout_type_save', 'webmz_layout_type_nonce' );
	?>
	<div class="webmz-layout-type-field">
		<label for="webmz-layout-type-select" class="webmz-layout-type-field__label"><?php esc_html_e( 'نوع لایه', 'tadris' ); ?></label>
		<select id="webmz-layout-type-select" name="webmz_layout_type" class="webmz-layout-type-field__select">
			<option value=""><?php esc_html_e( 'انتخاب نوع لایه', 'tadris' ); ?></option>
			<?php foreach ( $types as $key => $label ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $layout_type, $key ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<p class="webmz-layout-type-field__hint description"><?php esc_html_e( 'ابتدا نوع لایه را انتخاب کنید تا بتوانید شرایط نمایش را تنظیم کنید.', 'tadris' ); ?></p>
	</div>

	<div id="webmz-layout-settings" class="webmz-layout-settings<?php echo $has_type ? '' : ' is-locked'; ?>">
		<?php if ( ! $has_type ) : ?>
			<div class="webmz-layout-settings__lock-notice">
				<?php esc_html_e( 'برای تنظیم اولویت و شرایط نمایش، ابتدا «نوع لایه» را انتخاب کنید.', 'tadris' ); ?>
			</div>
		<?php endif; ?>
		<fieldset class="webmz-layout-settings__body"<?php echo $has_type ? '' : ' disabled'; ?>>
			<div class="webmz-layout-priority">
				<div class="webmz-layout-priority__head">
					<span class="webmz-layout-priority__icon" aria-hidden="true">⚡</span>
					<strong><?php esc_html_e( 'اولویت نمایش', 'tadris' ); ?></strong>
				</div>
				<ol class="webmz-layout-priority__list">
					<li>
						<span class="webmz-layout-priority__step">1</span>
						<span><?php esc_html_e( 'شرایط نمایش در ویرایش لایه‌بندی', 'tadris' ); ?></span>
					</li>
					<li>
						<span class="webmz-layout-priority__step">2</span>
						<span><?php esc_html_e( 'قالب انتخاب‌شده در تنظیمات پوسته → ساختار المنتوری', 'tadris' ); ?></span>
					</li>
				</ol>
				<?php if ( class_exists( '\ElementorPro\Plugin' ) ) : ?>
					<p class="webmz-layout-priority__note"><?php esc_html_e( 'اگر Elementor Pro و Theme Builder برای همین بخش قالب فعال داشته باشد، WebMZ در آن بخش کنار می‌کشد تا تداخل ایجاد نشود.', 'tadris' ); ?></p>
				<?php endif; ?>
			</div>

			<div id="webmz-layout-rules" class="webmz-layout-conditions">
				<?php $rule_index = 0; ?>
				<div class="webmz-layout-conditions__section" data-section="include">
					<div class="webmz-layout-conditions__section-head">
						<h3><?php esc_html_e( 'نمایش در', 'tadris' ); ?></h3>
						<button type="button" class="button button-secondary webmz-layout-conditions__add" data-type="include">+</button>
					</div>
					<div class="webmz-layout-conditions__rows">
						<?php foreach ( $include as $rule ) : ?>
							<?php webmz_render_layout_condition_rule_row( $rule, 'include', $rule_index, $definitions ); ?>
							<?php ++$rule_index; ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</fieldset>
	</div>

	<script type="text/html" id="tmpl-webmz-layout-condition-rule">
		<?php webmz_render_layout_condition_rule_row( array( 'type' => 'include', 'name' => 'general', 'object_ids' => array() ), 'include', '__INDEX__', $definitions ); ?>
	</script>
	<?php
}

/**
 * Render one display rule row.
 *
 * @param array<string,mixed>              $rule        Rule data.
 * @param string                           $type        Rule type, kept for template compatibility.
 * @param int|string                       $index       Row index.
 * @param array<string,array<string,mixed>> $definitions Condition definitions.
 * @return void
 */
function webmz_render_layout_condition_rule_row( $rule, $type, $index, $definitions ) {
	$name       = isset( $rule['name'] ) && isset( $definitions[ $rule['name'] ] ) ? $rule['name'] : 'general';
	$object_ids = isset( $rule['object_ids'] ) && is_array( $rule['object_ids'] ) ? array_map( 'absint', $rule['object_ids'] ) : array();
	?>
	<div class="webmz-layout-rule" data-index="<?php echo esc_attr( (string) $index ); ?>">
		<input type="hidden" name="webmz_layout_conditions[<?php echo esc_attr( (string) $index ); ?>][type]" value="include" class="webmz-layout-rule__type">
		<div class="webmz-layout-rule__main">
			<span class="webmz-layout-rule__label"><?php esc_html_e( 'نمایش در', 'tadris' ); ?></span>
			<select name="webmz_layout_conditions[<?php echo esc_attr( (string) $index ); ?>][name]" class="webmz-layout-rule__name">
				<?php foreach ( $definitions as $key => $definition ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $name, $key ); ?>><?php echo esc_html( $definition['label'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="webmz-layout-rule__picker<?php echo 'general' === $name ? ' is-hidden' : ''; ?>">
			<input type="search" class="webmz-layout-rule__search regular-text" placeholder="<?php esc_attr_e( 'جستجو و انتخاب...', 'tadris' ); ?>" autocomplete="off">
			<div class="webmz-layout-rule__results"></div>
			<div class="webmz-layout-rule__choices">
				<?php foreach ( $object_ids as $object_id ) : ?>
					<label class="webmz-layout-rule__choice">
						<input type="checkbox" name="webmz_layout_conditions[<?php echo esc_attr( (string) $index ); ?>][object_ids][]" value="<?php echo esc_attr( $object_id ); ?>" checked>
						<span><?php echo esc_html( webmz_layout_object_label( $name, $object_id ) ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>
		<button type="button" class="webmz-layout-rule__remove" aria-label="<?php esc_attr_e( 'حذف قانون', 'tadris' ); ?>">&times;</button>
	</div>
	<?php
}

/**
 * Save layout display conditions securely.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function webmz_save_layout_conditions( $post_id ) {
	if ( ! isset( $_POST['webmz_layout_conditions_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_layout_conditions_nonce'] ) ), 'webmz_layout_conditions_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$layout_type = isset( $_POST['webmz_layout_type'] ) ? sanitize_key( wp_unslash( $_POST['webmz_layout_type'] ) ) : '';
	$types       = webmz_get_layout_types();

	if ( ! $layout_type || ! isset( $types[ $layout_type ] ) ) {
		delete_post_meta( $post_id, WEBMZ_LAYOUT_CONDITIONS_META );
		delete_post_meta( $post_id, WEBMZ_LAYOUT_CONDITION_META );
		return;
	}

	$raw   = isset( $_POST['webmz_layout_conditions'] ) ? wp_unslash( $_POST['webmz_layout_conditions'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$clean = webmz_sanitize_layout_condition_rules( $raw );

	delete_post_meta( $post_id, WEBMZ_LAYOUT_CONDITION_META );

	if ( empty( $clean ) ) {
		delete_post_meta( $post_id, WEBMZ_LAYOUT_CONDITIONS_META );
		return;
	}

	update_post_meta( $post_id, WEBMZ_LAYOUT_CONDITIONS_META, $clean );
}
add_action( 'save_post_webmz_layout', 'webmz_save_layout_conditions' );

/**
 * Load admin assets for layout condition UI.
 *
 * @param string $hook Admin hook suffix.
 * @return void
 */
function webmz_layout_conditions_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || 'webmz_layout' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_style( 'webmz-layout-conditions', WEBMZ_URI . 'assets/css/layout-conditions.css', array(), WEBMZ_VERSION );
	wp_enqueue_script( 'webmz-layout-conditions', WEBMZ_URI . 'assets/js/layout-conditions.js', array( 'jquery' ), WEBMZ_VERSION, true );
	wp_localize_script(
		'webmz-layout-conditions',
		'webmzLayoutConditions',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'webmz_layout_conditions' ),
			'definitions' => webmz_get_layout_condition_definitions(),
			'i18n'        => array(
				'search'       => esc_html__( 'جستجو...', 'tadris' ),
				'noResult'     => esc_html__( 'موردی یافت نشد.', 'tadris' ),
				'showIn'       => esc_html__( 'نمایش در', 'tadris' ),
				'lockNotice'   => esc_html__( 'برای تنظیم اولویت و شرایط نمایش، ابتدا «نوع لایه» را انتخاب کنید.', 'tadris' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'webmz_layout_conditions_admin_assets' );

/**
 * Save layout meta securely.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function webmz_save_layout_type( $post_id ) {
	if (
		! isset( $_POST['webmz_layout_type_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['webmz_layout_type_nonce'] ) ), 'webmz_layout_type_save' )
	) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$type  = isset( $_POST['webmz_layout_type'] ) ? sanitize_key( wp_unslash( $_POST['webmz_layout_type'] ) ) : '';
	$types = webmz_get_layout_types();
	if ( isset( $types[ $type ] ) ) {
		update_post_meta( $post_id, '_webmz_layout_type', $type );
	} else {
		delete_post_meta( $post_id, '_webmz_layout_type' );
	}
}
add_action( 'save_post_webmz_layout', 'webmz_save_layout_type' );

/**
 * Add the type column to the layout listing.
 *
 * @param array<string,string> $columns Existing columns.
 * @return array<string,string>
 */
function webmz_layout_columns( $columns ) {
	$new = array();

	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;

		if ( 'title' === $key ) {
			$new['webmz_layout_type']        = esc_html__( 'نوع لایه', 'tadris' );
			$new['webmz_layout_conditions']  = esc_html__( 'شرایط نمایش', 'tadris' );
		}
	}

	if ( ! isset( $new['webmz_layout_type'] ) ) {
		$new['webmz_layout_type']       = esc_html__( 'نوع لایه', 'tadris' );
		$new['webmz_layout_conditions'] = esc_html__( 'شرایط نمایش', 'tadris' );
	}

	return $new;
}
add_filter( 'manage_webmz_layout_posts_columns', 'webmz_layout_columns' );

/**
 * Render layout type in admin table.
 *
 * @param string $column Column slug.
 * @param int    $post_id Post ID.
 * @return void
 */
function webmz_layout_column_content( $column, $post_id ) {
	if ( 'webmz_layout_type' === $column ) {
		$type  = get_post_meta( $post_id, '_webmz_layout_type', true );
		$types = webmz_get_layout_types();
		echo isset( $types[ $type ] ) ? esc_html( $types[ $type ] ) : '&mdash;';
		return;
	}

	if ( 'webmz_layout_conditions' === $column ) {
		echo esc_html( webmz_format_layout_conditions_summary( webmz_get_layout_condition_rules( $post_id ) ) );
	}
}
add_action( 'manage_webmz_layout_posts_custom_column', 'webmz_layout_column_content', 10, 2 );

/**
 * Enable Elementor editing for the layout post type once the theme activates.
 *
 * @return void
 */
function webmz_enable_elementor_layout_support() {
	if ( ! class_exists( '\\Elementor\\Plugin' ) ) {
		return;
	}
	$cpts = get_option( 'elementor_cpt_support', array( 'page', 'post' ) );
	$cpts = is_array( $cpts ) ? $cpts : array( 'page', 'post' );
	if ( ! in_array( 'webmz_layout', $cpts, true ) ) {
		$cpts[] = 'webmz_layout';
		update_option( 'elementor_cpt_support', array_values( array_unique( $cpts ) ) );
	}
}
add_action( 'after_switch_theme', 'webmz_enable_elementor_layout_support' );
add_action( 'admin_init', 'webmz_enable_elementor_layout_support' );
