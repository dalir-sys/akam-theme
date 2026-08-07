<?php
/**
 * Assignment and rendering of Elementor-built layouts.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Determine whether Elementor frontend renderer is available.
 *
 * @return bool
 */
function webmz_elementor_is_ready() {
	return class_exists( '\\Elementor\\Plugin' ) && \Elementor\Plugin::$instance && isset( \Elementor\Plugin::$instance->frontend );
}

/**
 * Validate an Elementor layout post that can be rendered publicly.
 *
 * Layout types are organizational only. This prevents a freshly created,
 * published layout without a chosen type from disappearing from selects.
 *
 * @param int $layout_id Layout ID.
 * @return int Valid layout ID or zero.
 */
function webmz_validate_layout_id( $layout_id ) {
	$layout_id = absint( $layout_id );

	if ( ! $layout_id || 'webmz_layout' !== get_post_type( $layout_id ) || 'publish' !== get_post_status( $layout_id ) ) {
		return 0;
	}

	return $layout_id;
}

/**
 * Map WebMZ layout locations to Elementor Pro Theme Builder locations.
 *
 * @param string $location WebMZ location key.
 * @return string
 */
function webmz_map_location_to_elementor_pro( $location ) {
	$map = array(
		'header'          => 'header',
		'footer'          => 'footer',
		'single_post'     => 'single',
		'single_product'  => 'single',
		'page'            => 'single',
		'archive_post'    => 'archive',
		'archive_teacher' => 'archive',
		'single_teacher'  => 'single',
		'archive_product' => 'archive',
		'search'          => 'archive',
		'404'             => 'single',
	);

	return isset( $map[ $location ] ) ? $map[ $location ] : '';
}

/**
 * Whether Elementor Pro Theme Builder has a template for this location on the current request.
 *
 * @param string $location WebMZ location key.
 * @return bool
 */
function webmz_elementor_pro_has_location_template( $location ) {
	if ( ! class_exists( '\ElementorPro\Plugin' ) ) {
		return false;
	}

	$plugin = \ElementorPro\Plugin::instance();

	if ( ! isset( $plugin->modules_manager ) ) {
		return false;
	}

	$module = $plugin->modules_manager->get_modules( 'theme-builder' );

	if ( ! $module || ! method_exists( $module, 'get_conditions_manager' ) ) {
		return false;
	}

	$elementor_location = webmz_map_location_to_elementor_pro( $location );

	if ( ! $elementor_location ) {
		return false;
	}

	$documents = $module->get_conditions_manager()->get_documents_for_location( $elementor_location );

	return ! empty( $documents );
}

/**
 * Return the assigned layout ID only when it is valid and published.
 *
 * Priority:
 * 1. Elementor Pro Theme Builder (handled in render; returns 0 here)
 * 2. Layout display conditions
 * 3. Theme options fallback
 *
 * @param string $location Theme builder location.
 * @return int
 */
function webmz_get_assigned_layout_id( $location ) {
	$location = sanitize_key( $location );

	if ( webmz_elementor_pro_has_location_template( $location ) ) {
		return 0;
	}

	if ( function_exists( 'webmz_find_conditional_layout_id' ) ) {
		$conditional_id = webmz_find_conditional_layout_id( $location );

		if ( $conditional_id ) {
			return $conditional_id;
		}
	}

	return webmz_validate_layout_id(
		webmz_get_option( 'layout_' . $location )
	);
}

/**
 * Get every Elementor layout that will be rendered on the current request.
 *
 * Header/footer layouts are rendered after wp_head() by the template, so their
 * generated Elementor CSS must be known and enqueued before the document head
 * is printed.
 *
 * @return array<int>
 */
function webmz_get_current_render_layout_ids() {
	$layout_ids = array();

	foreach ( array( 'header', 'footer' ) as $location ) {
		$layout_id = webmz_get_assigned_layout_id( $location );

		if ( $layout_id ) {
			$layout_ids[] = $layout_id;
		}
	}

	$content_location = webmz_get_current_content_location();

	if ( $content_location ) {
		$content_id = webmz_get_assigned_layout_id( $content_location );

		if ( $content_id ) {
			$layout_ids[] = $content_id;
		}
	}

	$layout_ids = array_map( 'absint', $layout_ids );
	$layout_ids = array_filter( $layout_ids );

	return array_values( array_unique( $layout_ids ) );
}

/**
 * Recursively check Elementor saved data for a particular widget type.
 *
 * @param array<int,array<string,mixed>> $elements Elementor elements.
 * @param string                         $widget_name Elementor widget type.
 * @return bool
 */
function webmz_elementor_elements_contain_widget( $elements, $widget_name ) {
	foreach ( (array) $elements as $element ) {
		if ( ! is_array( $element ) ) {
			continue;
		}

		if (
			isset( $element['elType'], $element['widgetType'] )
			&& 'widget' === $element['elType']
			&& $widget_name === $element['widgetType']
		) {
			return true;
		}

		if (
			! empty( $element['elements'] )
			&& webmz_elementor_elements_contain_widget( $element['elements'], $widget_name )
		) {
			return true;
		}
	}

	return false;
}

/**
 * Check whether a saved WebMZ Elementor layout contains a custom widget.
 *
 * This allows widget assets used in injected headers or footers to be queued
 * before wp_head() prints stylesheets.
 *
 * @param int    $layout_id Layout post ID.
 * @param string $widget_name Elementor widget name.
 * @return bool
 */
function webmz_layout_contains_elementor_widget( $layout_id, $widget_name ) {
	$layout_id = webmz_validate_layout_id( $layout_id );

	if ( ! $layout_id ) {
		return false;
	}

	$raw_data = get_post_meta( $layout_id, '_elementor_data', true );
	$elements = is_string( $raw_data ) ? json_decode( $raw_data, true ) : $raw_data;

	if ( ! is_array( $elements ) ) {
		return false;
	}

	return webmz_elementor_elements_contain_widget( $elements, $widget_name );
}

/**
 * Enqueue Elementor frontend assets for injected WebMZ layouts.
 *
 * Elementor layouts assigned as theme header/footer are not necessarily the
 * current Elementor document. Therefore Elementor base frontend styles and the
 * generated CSS file of each assigned layout must be loaded manually before
 * wp_head() prints stylesheet links.
 *
 * @return void
 */
function webmz_enqueue_assigned_elementor_layout_assets() {
	if ( is_admin() || ! webmz_elementor_is_ready() || webmz_is_layout_editing_context() ) {
		return;
	}

	$layout_ids = webmz_get_current_render_layout_ids();

	if ( empty( $layout_ids ) ) {
		return;
	}

	$frontend = \Elementor\Plugin::$instance->frontend;

	/*
	 * Elementor registers its frontend styles at priority 5. This fallback
	 * protects the injected-layout workflow in case the handle is not yet
	 * available for any reason.
	 */
	if (
		! wp_style_is( 'elementor-frontend', 'registered' )
		&& is_callable( array( $frontend, 'register_styles' ) )
	) {
		$frontend->register_styles();
	}

	/*
	 * Required for Flexbox Containers (.e-con).
	 * The post-{id}.css file only contains container variables such as
	 * --display and --flex-direction; their implementation exists here.
	 */
	if ( is_callable( array( $frontend, 'enqueue_styles' ) ) ) {
		$frontend->enqueue_styles();
	}

	if ( wp_style_is( 'elementor-frontend', 'registered' ) ) {
		wp_enqueue_style( 'elementor-frontend' );
	}

	/*
	 * Inform Elementor that these injected layout documents are rendered on
	 * the current request. This also helps Elementor register required
	 * document/widget assets in newer versions.
	 */
	foreach ( $layout_ids as $layout_id ) {
		do_action( 'elementor/post/render', $layout_id );
	}

	/*
	 * Enqueue generated stylesheet for every assigned WebMZ layout:
	 * header, footer and the selected content layout.
	 */
	if ( class_exists( '\\Elementor\\Core\\Files\\CSS\\Post' ) ) {
		foreach ( $layout_ids as $layout_id ) {
			$css_file = new \Elementor\Core\Files\CSS\Post( $layout_id );
			$css_file->enqueue();
		}
	}

	/*
	 * Elementor discovers widget dependencies too late when a saved layout is
	 * injected from header.php or footer.php after wp_head(). Queue styles and
	 * scripts for custom header widgets actually present in assigned layouts.
	 */
	$uses_ajax_search    = false;
	$uses_account        = false;
	$uses_mini_cart      = false;
	$uses_mobile_cart    = false;
	$uses_mobile_account = false;
	$uses_mobile_menu    = false;
	$uses_navigation     = false;
	$uses_tadris_script  = false;
	$uses_tadris_video   = false;
	$uses_tadris_product = false;
	$uses_post_toc       = false;
	$uses_spw            = false;
	$uses_spw_media      = false;
	$uses_spw_cart       = false;
	$spw_widget_names    = array(
		'webmz-spw-product-media',
		'webmz-spw-participants',
		'webmz-spw-rating',
		'webmz-spw-product-title',
		'webmz-spw-short-description',
		'webmz-spw-product-price',
		'webmz-spw-add-to-cart',
		'webmz-spw-advanced-add-to-cart',
		'webmz-spw-course-features',
		'webmz-spw-purchase-box',
		'webmz-spw-instructor-box',
		'webmz-spw-product-content',
		'webmz-spw-faq',
		'webmz-spw-section-heading',
		'webmz-spw-course-curriculum',
	);
	$require_download_login = function_exists( 'webmz_otp_get_settings' )
		&& function_exists( 'webmz_otp_is_enabled' )
		&& webmz_otp_is_enabled()
		&& 'yes' === webmz_otp_get_settings()['require_login_download']
		&& ! is_user_logged_in();

	foreach ( $layout_ids as $layout_id ) {
		$uses_ajax_search = $uses_ajax_search || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-ajax-search' );
		$uses_account        = $uses_account || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-account' );
		$uses_mini_cart      = $uses_mini_cart || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-mini-cart' );
		$uses_mobile_cart    = $uses_mobile_cart || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-mobile-mini-cart' );
		$uses_mobile_account = $uses_mobile_account || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-mobile-account' );
		$uses_mobile_menu    = $uses_mobile_menu || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-mobile-menu' );
		$uses_navigation     = $uses_navigation || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-navigation-menu' );
		$uses_tadris_video   = $uses_tadris_video || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-tadris-large-video' );
		$uses_tadris_product = $uses_tadris_product || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-tadris-product-loop' ) || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-tadris-product-loop-2' ) || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-tadris-product-loop-3' ) || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-tadris-tabbed-product-loop' );
		$uses_tadris_script  = $uses_tadris_script || $uses_tadris_video || $uses_tadris_product || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-tadris-counter' ) || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-post-share-save' ) || ( $require_download_login && webmz_layout_contains_elementor_widget( $layout_id, 'webmz-post-download-box' ) );
		$uses_post_toc       = $uses_post_toc || webmz_layout_contains_elementor_widget( $layout_id, 'webmz-post-toc' );

		foreach ( $spw_widget_names as $spw_widget_name ) {
			if ( webmz_layout_contains_elementor_widget( $layout_id, $spw_widget_name ) ) {
				$uses_spw = true;

				if ( 'webmz-spw-product-media' === $spw_widget_name ) {
					$uses_spw_media = true;
				}

				if ( in_array( $spw_widget_name, array( 'webmz-spw-add-to-cart', 'webmz-spw-advanced-add-to-cart', 'webmz-spw-purchase-box' ), true ) ) {
					$uses_spw_cart = true;
				}
			}
		}
	}

	if ( $uses_ajax_search ) {
		if ( wp_style_is( 'webmz-ajax-search', 'registered' ) ) {
			wp_enqueue_style( 'webmz-ajax-search' );
		}

		if ( wp_script_is( 'webmz-ajax-search', 'registered' ) ) {
			wp_enqueue_script( 'webmz-ajax-search' );
		}
	}

	if ( $uses_navigation ) {
		if ( wp_style_is( 'webmz-navigation-menu', 'registered' ) ) {
			wp_enqueue_style( 'webmz-navigation-menu' );
		}

		if ( wp_script_is( 'webmz-navigation-menu', 'registered' ) ) {
			wp_enqueue_script( 'webmz-navigation-menu' );
		}
	}

	if ( $uses_account || $uses_mini_cart || $uses_mobile_cart ) {
		if ( wp_style_is( 'webmz-header-commerce', 'registered' ) ) {
			wp_enqueue_style( 'webmz-header-commerce' );
		}

		if ( wp_script_is( 'webmz-header-commerce', 'registered' ) ) {
			wp_enqueue_script( 'webmz-header-commerce' );
		}
	}

	if ( ( $uses_mini_cart || $uses_mobile_cart ) && class_exists( 'WooCommerce' ) ) {
		wp_enqueue_script( 'wc-add-to-cart' );
	}

	if ( $uses_mobile_cart || $uses_mobile_account || $uses_mobile_menu || $uses_mini_cart ) {
		if ( wp_style_is( 'webmz-mobile-offcanvas', 'registered' ) ) {
			wp_enqueue_style( 'webmz-mobile-offcanvas' );
		}

		if ( wp_script_is( 'webmz-mobile-offcanvas', 'registered' ) ) {
			wp_enqueue_script( 'webmz-mobile-offcanvas' );
		}
	}

	if ( $uses_mobile_menu ) {
		if ( wp_style_is( 'webmz-navigation-menu', 'registered' ) ) {
			wp_enqueue_style( 'webmz-navigation-menu' );
		}
	}

	if ( $uses_tadris_video ) {
		wp_enqueue_style( 'webmz-plyr' );
		wp_enqueue_script( 'webmz-plyr' );
	}

	if ( $uses_tadris_product ) {
		wp_enqueue_style( 'webmz-swiper' );
		wp_enqueue_script( 'webmz-swiper' );
	}

	if ( $uses_tadris_script && wp_script_is( 'webmz-tadris-widgets', 'registered' ) ) {
		wp_enqueue_script( 'webmz-tadris-widgets' );
	}

	if ( $uses_spw ) {
		if ( wp_style_is( 'webmz-single-product-webmasters', 'registered' ) ) {
			wp_enqueue_style( 'webmz-single-product-webmasters' );
		}

		if ( wp_script_is( 'webmz-single-product-webmasters', 'registered' ) ) {
			wp_enqueue_script( 'webmz-single-product-webmasters' );
		}
	}

	if ( $uses_spw_media ) {
		wp_enqueue_style( 'webmz-swiper' );
		wp_enqueue_style( 'webmz-plyr' );
		wp_enqueue_style( 'webmz-plyr-widgets' );
		wp_enqueue_script( 'webmz-swiper' );
		wp_enqueue_script( 'webmz-plyr' );

		if ( wp_script_is( 'webmz-tadris-widgets', 'registered' ) ) {
			wp_enqueue_script( 'webmz-tadris-widgets' );
		}
	}

	if ( $uses_spw_cart ) {
		if ( wp_style_is( 'webmz-header-commerce', 'registered' ) ) {
			wp_enqueue_style( 'webmz-header-commerce' );
		}

		if ( wp_script_is( 'webmz-header-commerce', 'registered' ) ) {
			wp_enqueue_script( 'webmz-header-commerce' );
		}

		if ( wp_style_is( 'webmz-mobile-offcanvas', 'registered' ) ) {
			wp_enqueue_style( 'webmz-mobile-offcanvas' );
		}

		if ( wp_script_is( 'webmz-mobile-offcanvas', 'registered' ) ) {
			wp_enqueue_script( 'webmz-mobile-offcanvas' );
		}

		if ( wp_script_is( 'webmz-tadris-widgets', 'registered' ) ) {
			wp_enqueue_script( 'webmz-tadris-widgets' );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			wp_enqueue_script( 'wc-add-to-cart' );
		}
	}

	if ( $uses_post_toc && wp_script_is( 'webmz-post-toc', 'registered' ) ) {
		wp_enqueue_script( 'webmz-post-toc' );
	}
}
add_action( 'wp_enqueue_scripts', 'webmz_enqueue_assigned_elementor_layout_assets', 25 );

/**
 * Render one saved Elementor layout while preserving the queried content context.
 *
 * @param int $layout_id Saved WebMZ layout post ID.
 * @return bool True if rendered.
 */
function webmz_render_elementor_layout( $layout_id ) {
	$layout_id = webmz_validate_layout_id( $layout_id );

	if ( ! $layout_id || ! webmz_elementor_is_ready() || webmz_is_layout_editing_context() ) {
		return false;
	}

	$context_id = get_queried_object_id();
	$previous   = isset( $GLOBALS['webmz_render_context_post_id'] )
		? $GLOBALS['webmz_render_context_post_id']
		: null;

	$GLOBALS['webmz_render_context_post_id'] = $context_id;

	$content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $layout_id, true );

	if ( null === $previous ) {
		unset( $GLOBALS['webmz_render_context_post_id'] );
	} else {
		$GLOBALS['webmz_render_context_post_id'] = $previous;
	}

	if ( '' === trim( (string) $content ) ) {
		return false;
	}

	/*
	 * Elementor has already generated the trusted rendered HTML
	 * for this saved layout.
	 */
	echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	return true;
}

/**
 * Whether a layout location is primary page content (needs a main landmark).
 *
 * @param string $location Location key.
 * @return bool
 */
function webmz_is_content_layout_location( $location ) {
	$location = sanitize_key( $location );

	return '' !== $location && ! in_array( $location, array( 'header', 'footer' ), true );
}

/**
 * Render a named location if an Elementor layout is assigned.
 *
 * Content locations are wrapped in `<main id="primary">` so skip links and
 * accessibility scanners always find a main landmark. Header/footer use
 * matching HTML5 landmarks around Elementor's non-semantic wrappers.
 *
 * @param string $location Location key.
 * @return bool
 */
function webmz_render_location( $location ) {
	$location = sanitize_key( $location );

	$context_id = get_queried_object_id();
	$previous   = isset( $GLOBALS['webmz_render_context_post_id'] )
		? $GLOBALS['webmz_render_context_post_id']
		: null;

	$GLOBALS['webmz_render_context_post_id'] = $context_id;

	ob_start();

	if ( webmz_elementor_pro_has_location_template( $location ) && function_exists( 'elementor_theme_do_location' ) ) {
		$rendered = (bool) elementor_theme_do_location( webmz_map_location_to_elementor_pro( $location ) );
	} else {
		$rendered = webmz_render_elementor_layout(
			webmz_get_assigned_layout_id( $location )
		);
	}

	$content = ob_get_clean();

	if ( null === $previous ) {
		unset( $GLOBALS['webmz_render_context_post_id'] );
	} else {
		$GLOBALS['webmz_render_context_post_id'] = $previous;
	}

	if ( ! $rendered || '' === trim( (string) $content ) ) {
		return false;
	}

	if ( 'header' === $location ) {
		echo '<header id="masthead" class="webmz-layout-header">';
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</header>';

		return true;
	}

	if ( 'footer' === $location ) {
		echo '<footer id="colophon" class="webmz-layout-footer">';
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</footer>';

		return true;
	}

	if (
		in_array( $location, array( 'single_product', 'archive_product' ), true )
		&& function_exists( 'webmz_render_woocommerce_elementor_layout_content' )
		&& class_exists( 'WooCommerce' )
	) {
		webmz_render_woocommerce_elementor_layout_content( $location, $content );

		return true;
	}

	if ( webmz_is_content_layout_location( $location ) ) {
		echo '<main id="primary" class="site-main webmz-layout-main">';
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</main>';

		return true;
	}

	echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	return true;
}

/**
 * Determine the applicable content location with explicit precedence.
 *
 * @return string
 */
function webmz_get_current_content_location() {
	if ( is_404() ) {
		return '404';
	}

	if ( is_search() ) {
		return 'search';
	}

	if ( function_exists( 'is_product' ) && is_product() ) {
		return 'single_product';
	}

	if (
		function_exists( 'is_shop' )
		&& (
			is_shop()
			|| (
				function_exists( 'is_product_taxonomy' )
				&& is_product_taxonomy()
			)
		)
	) {
		return 'archive_product';
	}

	if ( is_singular( 'post' ) ) {
		return 'single_post';
	}

	if ( defined( 'WEBMZ_TEACHER_POST_TYPE' ) && is_singular( WEBMZ_TEACHER_POST_TYPE ) ) {
		return 'single_teacher';
	}

	if ( is_page() ) {
		return 'page';
	}

	if ( defined( 'WEBMZ_TEACHER_POST_TYPE' ) && is_post_type_archive( WEBMZ_TEACHER_POST_TYPE ) ) {
		return 'archive_teacher';
	}

	if ( is_home() || is_archive() ) {
		return 'archive_post';
	}

	return '';
}

/**
 * Render the currently assigned content location, where applicable.
 *
 * @return bool
 */
function webmz_render_current_content_layout() {
	$location = webmz_get_current_content_location();

	return $location ? webmz_render_location( $location ) : false;
}

/**
 * Get published layout choices for admin selects.
 *
 * All published layouts remain visible, even if their type has not been set.
 * The requested type is placed first as a visual convenience only.
 *
 * @param string $preferred_type Intended location type.
 * @return array<int,string>
 */
function webmz_get_layout_choices( $preferred_type = '' ) {
	$preferred_type = sanitize_key( $preferred_type );
	$types          = webmz_get_layout_types();

	$posts = get_posts(
		array(
			'post_type'      => 'webmz_layout',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	usort(
		$posts,
		static function ( $a, $b ) use ( $preferred_type ) {
			$type_a = get_post_meta( $a->ID, '_webmz_layout_type', true );
			$type_b = get_post_meta( $b->ID, '_webmz_layout_type', true );

			$rank_a = ( $preferred_type && $preferred_type === $type_a )
				? 0
				: ( $type_a ? 2 : 1 );

			$rank_b = ( $preferred_type && $preferred_type === $type_b )
				? 0
				: ( $type_b ? 2 : 1 );

			if ( $rank_a !== $rank_b ) {
				return $rank_a - $rank_b;
			}

			return strcasecmp( $a->post_title, $b->post_title );
		}
	);

	$choices = array();

	foreach ( $posts as $layout ) {
		$type       = get_post_meta( $layout->ID, '_webmz_layout_type', true );
		$type_label = isset( $types[ $type ] )
			? $types[ $type ]
			: esc_html__( 'نوع تعیین نشده', 'tadris' );

		$title = $layout->post_title
			? $layout->post_title
			: sprintf( esc_html__( 'بدون عنوان #%d', 'tadris' ), $layout->ID );

		$choices[ $layout->ID ] = sprintf(
			'%1$s - %2$s',
			$title,
			$type_label
		);
	}

	return $choices;
}