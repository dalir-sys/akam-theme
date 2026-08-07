<?php
/**
 * Mega menu post type, menu item fields, and frontend helpers.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Elementor-editable mega menu content post type.
 *
 * @return void
 */
function webmz_register_mega_menu_post_type() {
	$labels = array(
		'name'               => esc_html__( 'محتوای مگامنو', 'tadris' ),
		'singular_name'      => esc_html__( 'محتوای مگامنو', 'tadris' ),
		'add_new'            => esc_html__( 'افزودن محتوای مگامنو', 'tadris' ),
		'add_new_item'       => esc_html__( 'افزودن محتوای مگامنو جدید', 'tadris' ),
		'edit_item'          => esc_html__( 'ویرایش محتوای مگامنو', 'tadris' ),
		'new_item'           => esc_html__( 'محتوای مگامنو جدید', 'tadris' ),
		'view_item'          => esc_html__( 'نمایش محتوای مگامنو', 'tadris' ),
		'search_items'       => esc_html__( 'جستجوی محتوای مگامنو', 'tadris' ),
		'not_found'          => esc_html__( 'محتوای مگامنویی پیدا نشد.', 'tadris' ),
		'not_found_in_trash' => esc_html__( 'محتوای مگامنویی در زباله‌دان نیست.', 'tadris' ),
		'menu_name'          => esc_html__( 'محتوای مگامنو', 'tadris' ),
	);

	register_post_type(
		'webmz_mega_menu',
		array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => 'webmz-options',
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-screenoptions',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'elementor' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => true,
		)
	);
}
add_action( 'init', 'webmz_register_mega_menu_post_type' );

/**
 * Detect the Elementor editor/preview request for a mega menu content post.
 *
 * @return bool
 */
function webmz_is_mega_menu_preview_request() {
	if ( is_singular( 'webmz_mega_menu' ) ) {
		return true;
	}

	if ( ! isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return false;
	}

	$preview_id = absint( wp_unslash( $_GET['elementor-preview'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	return $preview_id && 'webmz_mega_menu' === get_post_type( $preview_id );
}

/**
 * Use a minimal single template so Elementor can always find the_content().
 *
 * @param string $template Current template path.
 * @return string
 */
function webmz_mega_menu_template_include( $template ) {
	if ( ! webmz_is_mega_menu_preview_request() ) {
		return $template;
	}

	$mega_template = WEBMZ_DIR . 'template-parts/mega-menu-preview.php';

	return file_exists( $mega_template ) ? $mega_template : $template;
}
add_filter( 'template_include', 'webmz_mega_menu_template_include', 20 );

/**
 * Return published mega menu content posts for select fields.
 *
 * @return array<int,string>
 */
function webmz_get_mega_menu_content_options() {
	$options = array();
	$posts   = get_posts(
		array(
			'post_type'              => 'webmz_mega_menu',
			'post_status'            => 'publish',
			'posts_per_page'         => 100,
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	foreach ( $posts as $post ) {
		$options[ absint( $post->ID ) ] = get_the_title( $post );
	}

	return $options;
}

/**
 * Print mega menu controls on top-level Appearance > Menus items.
 *
 * @param int      $item_id Menu item ID.
 * @param WP_Post  $item    Menu item object.
 * @param int      $depth   Menu depth.
 * @param stdClass $args    Walker args.
 * @return void
 */
function webmz_nav_menu_mega_custom_field( $item_id, $item, $depth, $args ) {
	if ( 0 !== absint( $depth ) ) {
		return;
	}

	$is_enabled = '1' === get_post_meta( $item_id, '_webmz_mega_menu_enabled', true );
	$content_id = absint( get_post_meta( $item_id, '_webmz_mega_menu_content_id', true ) );
	$options    = webmz_get_mega_menu_content_options();
	$field_id   = 'edit-menu-item-webmz-mega-content-' . $item_id;
	?>
	<p class="field-webmz-mega-menu description description-wide">
		<label class="webmz-menu-toggle-field">
			<input
				type="checkbox"
				class="edit-menu-item-webmz-mega-enabled"
				name="menu-item-webmz-mega-enabled[<?php echo esc_attr( $item_id ); ?>]"
				value="1"
				<?php checked( $is_enabled ); ?>
			>
			<?php esc_html_e( 'فعال‌سازی مگامنو', 'tadris' ); ?>
		</label>
	</p>
	<p class="field-webmz-mega-content description description-wide<?php echo $is_enabled ? '' : ' is-hidden'; ?>">
		<label for="<?php echo esc_attr( $field_id ); ?>">
			<?php esc_html_e( 'انتخاب محتوای مگامنو', 'tadris' ); ?>
			<select id="<?php echo esc_attr( $field_id ); ?>" name="menu-item-webmz-mega-content-id[<?php echo esc_attr( $item_id ); ?>]" class="widefat">
				<option value="0"><?php esc_html_e( 'انتخاب کنید', 'tadris' ); ?></option>
				<?php foreach ( $options as $post_id => $title ) : ?>
					<option value="<?php echo esc_attr( $post_id ); ?>" <?php selected( $content_id, $post_id ); ?>><?php echo esc_html( $title ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<?php if ( empty( $options ) ) : ?>
			<span class="description"><?php esc_html_e( 'هنوز محتوایی در پست‌تایپ «محتوای مگامنو» منتشر نشده است.', 'tadris' ); ?></span>
		<?php endif; ?>
	</p>
	<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'webmz_nav_menu_mega_custom_field', 20, 4 );

/**
 * Save mega menu controls for a menu item.
 *
 * @param int   $menu_id         Menu term ID.
 * @param int   $menu_item_db_id Menu item post ID.
 * @param array $args            Menu item update args.
 * @return void
 */
function webmz_save_nav_menu_mega_field( $menu_id, $menu_item_db_id, $args ) {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$enabled = isset( $_POST['menu-item-webmz-mega-enabled'][ $menu_item_db_id ] ) ? '1' : '0'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$content = 0;

	if ( isset( $_POST['menu-item-webmz-mega-content-id'][ $menu_item_db_id ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$content = absint( wp_unslash( $_POST['menu-item-webmz-mega-content-id'][ $menu_item_db_id ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	}

	if ( '1' !== $enabled ) {
		delete_post_meta( $menu_item_db_id, '_webmz_mega_menu_enabled' );
		delete_post_meta( $menu_item_db_id, '_webmz_mega_menu_content_id' );
		return;
	}

	update_post_meta( $menu_item_db_id, '_webmz_mega_menu_enabled', '1' );

	if ( $content && 'webmz_mega_menu' === get_post_type( $content ) ) {
		update_post_meta( $menu_item_db_id, '_webmz_mega_menu_content_id', $content );
	} else {
		delete_post_meta( $menu_item_db_id, '_webmz_mega_menu_content_id' );
	}
}
add_action( 'wp_update_nav_menu_item', 'webmz_save_nav_menu_mega_field', 20, 3 );

/**
 * Determine whether a menu item has a valid mega menu selection.
 *
 * @param int $item_id Menu item post ID.
 * @return bool
 */
function webmz_nav_menu_item_has_mega_menu( $item_id ) {
	$content_id = webmz_get_nav_menu_item_mega_content_id( $item_id );

	return $content_id > 0;
}

/**
 * Return selected mega menu content post ID for a menu item.
 *
 * @param int $item_id Menu item post ID.
 * @return int
 */
function webmz_get_nav_menu_item_mega_content_id( $item_id ) {
	$item_id = absint( $item_id );

	if ( ! $item_id || '1' !== get_post_meta( $item_id, '_webmz_mega_menu_enabled', true ) ) {
		return 0;
	}

	$content_id = absint( get_post_meta( $item_id, '_webmz_mega_menu_content_id', true ) );

	if ( ! $content_id || 'webmz_mega_menu' !== get_post_type( $content_id ) || 'publish' !== get_post_status( $content_id ) ) {
		return 0;
	}

	return $content_id;
}

/**
 * Render selected mega menu content.
 *
 * @param int $content_id Mega menu content post ID.
 * @return string
 */
function webmz_render_mega_menu_content( $content_id ) {
	$content_id = absint( $content_id );

	if ( ! $content_id ) {
		return '';
	}

	if ( class_exists( '\Elementor\Plugin' ) ) {
		$elementor_content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $content_id );

		if ( '' !== trim( (string) $elementor_content ) ) {
			return $elementor_content;
		}
	}

	$post = get_post( $content_id );

	if ( ! $post ) {
		return '';
	}

	return apply_filters( 'the_content', $post->post_content );
}

/**
 * Add mega menu post type support to Elementor settings.
 *
 * @return void
 */
function webmz_enable_elementor_for_mega_menu_post_type() {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return;
	}

	$post_types = get_option( 'elementor_cpt_support', array() );

	if ( ! is_array( $post_types ) ) {
		$post_types = array();
	}

	if ( ! in_array( 'webmz_mega_menu', $post_types, true ) ) {
		$post_types[] = 'webmz_mega_menu';
		update_option( 'elementor_cpt_support', $post_types );
	}
}
add_action( 'after_switch_theme', 'webmz_enable_elementor_for_mega_menu_post_type' );
add_action( 'admin_init', 'webmz_enable_elementor_for_mega_menu_post_type' );
