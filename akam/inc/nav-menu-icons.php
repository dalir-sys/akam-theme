<?php
/**
 * SVG icon field for WordPress navigation menu items.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Allowed SVG tags for menu item icons.
 *
 * @return array<string,array<string,bool>>
 */
function webmz_nav_menu_icon_allowed_html() {
	return array(
		'svg'     => array(
			'xmlns'       => true,
			'viewbox'     => true,
			'viewBox'     => true,
			'width'       => true,
			'height'      => true,
			'fill'        => true,
			'stroke'      => true,
			'class'       => true,
			'aria-hidden' => true,
			'role'        => true,
		),
		'g'       => array(
			'fill'        => true,
			'stroke'      => true,
			'class'       => true,
			'transform'   => true,
		),
		'path'    => array(
			'd'               => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'class'           => true,
		),
		'circle'  => array(
			'cx'     => true,
			'cy'     => true,
			'r'      => true,
			'fill'   => true,
			'stroke' => true,
		),
		'rect'    => array(
			'x'      => true,
			'y'      => true,
			'width'  => true,
			'height' => true,
			'rx'     => true,
			'fill'   => true,
			'stroke' => true,
		),
		'line'    => array(
			'x1'     => true,
			'y1'     => true,
			'x2'     => true,
			'y2'     => true,
			'stroke' => true,
		),
		'polygon' => array(
			'points' => true,
			'fill'   => true,
			'stroke' => true,
		),
		'polyline' => array(
			'points' => true,
			'fill'   => true,
			'stroke' => true,
		),
		'ellipse' => array(
			'cx'     => true,
			'cy'     => true,
			'rx'     => true,
			'ry'     => true,
			'fill'   => true,
			'stroke' => true,
		),
		'defs'    => array(),
		'style'   => array(),
		'clipPath' => array(
			'id' => true,
		),
		'mask'    => array(
			'id' => true,
		),
		'linearGradient' => array(
			'id'             => true,
			'x1'             => true,
			'y1'             => true,
			'x2'             => true,
			'y2'             => true,
			'gradientUnits'  => true,
			'gradientTransform' => true,
		),
		'radialGradient' => array(
			'id' => true,
			'cx' => true,
			'cy' => true,
			'r'  => true,
		),
		'stop'    => array(
			'offset'       => true,
			'stop-color'   => true,
			'stop-opacity' => true,
			'style'        => true,
		),
		'use'     => array(
			'href'       => true,
			'xlink:href' => true,
			'x'          => true,
			'y'          => true,
			'width'      => true,
			'height'     => true,
		),
	);
}

/**
 * Sanitize inline SVG markup for menu icons.
 *
 * @param string $svg Raw SVG string.
 * @return string
 */
function webmz_sanitize_nav_menu_icon_svg( $svg ) {
	$svg = trim( (string) $svg );

	if ( '' === $svg ) {
		return '';
	}

	return wp_kses( $svg, webmz_nav_menu_icon_allowed_html() );
}

/**
 * Return icon markup from a media library attachment.
 *
 * @param int $attachment_id Attachment post ID.
 * @return string
 */
function webmz_get_nav_menu_icon_attachment_markup( $attachment_id ) {
	$attachment_id = absint( $attachment_id );

	if ( ! $attachment_id || 'attachment' !== get_post_type( $attachment_id ) ) {
		return '';
	}

	$mime = get_post_mime_type( $attachment_id );

	if ( 'image/svg+xml' === $mime ) {
		$file   = get_attached_file( $attachment_id );
		$inline = '';

		if ( $file && is_readable( $file ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$inline = webmz_sanitize_nav_menu_icon_svg( file_get_contents( $file ) );
		}

		if ( $inline && preg_match( '/<(path|circle|rect|polygon|polyline|line|use|ellipse)\b/i', $inline ) ) {
			return $inline;
		}

		$url = wp_get_attachment_url( $attachment_id );

		if ( $url ) {
			return sprintf(
				'<img src="%s" class="webmz-nav__icon-image" alt="" loading="lazy" decoding="async" />',
				esc_url( $url )
			);
		}
	}

	$image = wp_get_attachment_image(
		$attachment_id,
		'thumbnail',
		false,
		array(
			'class'    => 'webmz-nav__icon-image',
			'alt'      => '',
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);

	return $image ? $image : '';
}

/**
 * Return sanitized icon markup for a menu item.
 *
 * @param int $item_id Menu item post ID.
 * @return string
 */
function webmz_get_nav_menu_item_icon_svg( $item_id ) {
	$item_id = absint( $item_id );

	if ( ! $item_id ) {
		return '';
	}

	$attachment_id = absint( get_post_meta( $item_id, '_webmz_menu_icon_id', true ) );

	if ( $attachment_id ) {
		$markup = webmz_get_nav_menu_icon_attachment_markup( $attachment_id );

		if ( '' !== $markup ) {
			return $markup;
		}
	}

	$svg = get_post_meta( $item_id, '_webmz_menu_icon_svg', true );

	return webmz_sanitize_nav_menu_icon_svg( $svg );
}

/**
 * Render menu item icon markup.
 *
 * @param int $item_id Menu item post ID.
 * @return void
 */
function webmz_render_nav_menu_item_icon( $item_id ) {
	$svg = webmz_get_nav_menu_item_icon_svg( $item_id );

	if ( '' === $svg ) {
		return;
	}

	echo '<span class="webmz-nav__icon" aria-hidden="true">' . $svg . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Enqueue media picker assets on Appearance > Menus.
 *
 * @param string $hook Current admin page hook.
 * @return void
 */
function webmz_nav_menu_icon_admin_assets( $hook ) {
	if ( 'nav-menus.php' !== $hook ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style( 'webmz-admin-options', WEBMZ_URI . 'assets/css/admin-options.css', array(), WEBMZ_VERSION );
	wp_enqueue_script(
		'webmz-admin-nav-menu-icons',
		WEBMZ_URI . 'assets/js/admin-nav-menu-icons.js',
		array( 'jquery' ),
		WEBMZ_VERSION,
		true
	);
	wp_localize_script(
		'webmz-admin-nav-menu-icons',
		'webmzNavMenuIcons',
		array(
			'mediaTitle'  => esc_html__( 'انتخاب آیکون منو', 'tadris' ),
			'mediaButton' => esc_html__( 'استفاده از آیکون', 'tadris' ),
			'emptyLabel'  => esc_html__( 'بدون آیکون', 'tadris' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'webmz_nav_menu_icon_admin_assets' );

/**
 * Output icon upload field in Appearance > Menus.
 *
 * @param int      $item_id Menu item ID.
 * @param WP_Post  $item    Menu item object.
 * @param int      $depth   Menu depth.
 * @param stdClass $args    Walker args.
 * @return void
 */
function webmz_nav_menu_icon_custom_field( $item_id, $item, $depth, $args ) {
	$attachment_id = absint( get_post_meta( $item_id, '_webmz_menu_icon_id', true ) );
	$preview_url   = '';

	if ( $attachment_id ) {
		$preview_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );

		if ( ! $preview_url ) {
			$preview_url = wp_get_attachment_url( $attachment_id );
		}
	}
	$field_id      = 'edit-menu-item-webmz-icon-' . $item_id;
	?>
	<p class="field-webmz-icon description description-wide">
		<span class="webmz-nav-menu-icon-label"><?php esc_html_e( 'آیکون منو', 'tadris' ); ?></span>
		<span class="description"><?php esc_html_e( 'فایل SVG یا تصویر آیکون را آپلود کنید. در ویجت‌های منو آکام، منو آیکون‌محور و منو موبایلی نمایش داده می‌شود.', 'tadris' ); ?></span>
		<span class="webmz-media-field webmz-media-field--menu">
			<span class="webmz-media-field__body">
				<span class="webmz-media-preview <?php echo $preview_url ? '' : 'is-empty'; ?>" data-webmz-media-preview="<?php echo esc_attr( $field_id ); ?>">
					<?php if ( $preview_url ) : ?>
						<img src="<?php echo esc_url( $preview_url ); ?>" alt="">
					<?php else : ?>
						<span><?php esc_html_e( 'بدون آیکون', 'tadris' ); ?></span>
					<?php endif; ?>
				</span>
				<input
					type="hidden"
					id="<?php echo esc_attr( $field_id ); ?>"
					class="edit-menu-item-webmz-icon"
					name="menu-item-webmz-icon-id[<?php echo esc_attr( $item_id ); ?>]"
					value="<?php echo esc_attr( $attachment_id ); ?>"
				>
				<span class="webmz-media-actions">
					<button
						type="button"
						class="button webmz-nav-menu-icon-upload"
						data-target="#<?php echo esc_attr( $field_id ); ?>"
						data-preview="[data-webmz-media-preview='<?php echo esc_attr( $field_id ); ?>']"
						data-title="<?php esc_attr_e( 'انتخاب آیکون منو', 'tadris' ); ?>"
						data-button="<?php esc_attr_e( 'استفاده از آیکون', 'tadris' ); ?>"
					><?php esc_html_e( 'آپلود/انتخاب', 'tadris' ); ?></button>
					<button
						type="button"
						class="button webmz-nav-menu-icon-remove"
						data-target="#<?php echo esc_attr( $field_id ); ?>"
						data-preview="[data-webmz-media-preview='<?php echo esc_attr( $field_id ); ?>']"
					><?php esc_html_e( 'حذف', 'tadris' ); ?></button>
				</span>
			</span>
		</span>
	</p>
	<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'webmz_nav_menu_icon_custom_field', 10, 4 );

/**
 * Persist icon attachment when a menu item is saved.
 *
 * @param int   $menu_id         Menu term ID.
 * @param int   $menu_item_db_id Menu item post ID.
 * @param array $args            Menu item update args.
 * @return void
 */
function webmz_save_nav_menu_icon_field( $menu_id, $menu_item_db_id, $args ) {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	if ( ! isset( $_POST['menu-item-webmz-icon-id'][ $menu_item_db_id ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	$attachment_id = absint( wp_unslash( $_POST['menu-item-webmz-icon-id'][ $menu_item_db_id ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( ! $attachment_id || 'attachment' !== get_post_type( $attachment_id ) ) {
		delete_post_meta( $menu_item_db_id, '_webmz_menu_icon_id' );
		return;
	}

	update_post_meta( $menu_item_db_id, '_webmz_menu_icon_id', $attachment_id );
}
add_action( 'wp_update_nav_menu_item', 'webmz_save_nav_menu_icon_field', 10, 3 );
