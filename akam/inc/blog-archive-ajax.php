<?php
/**
 * Blog archive widget AJAX helpers and endpoint.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get the default active filter tab.
 *
 * @param array<string,mixed> $context Archive context.
 * @return string category|post_tag
 */
function webmz_blog_archive_get_initial_tab( $context ) {
	return ( isset( $context['type'] ) && 'tag' === $context['type'] ) ? 'post_tag' : 'category';
}

/**
 * Get archive context metadata for the widget.
 *
 * @return array<string,mixed>
 */
function webmz_blog_archive_get_context() {
	$context  = array(
		'type'           => 'blog',
		'taxonomy'       => 'category',
		'term_id'        => 0,
		'search'         => '',
		'archive_title'  => '',
		'description'    => '',
	);

	if ( is_search() ) {
		$context['type']   = 'search';
		$context['search'] = get_search_query();
		/* translators: %s: search query. */
		$context['archive_title'] = sprintf( __( 'نتایج جستجو برای: %s', 'tadris' ), $context['search'] );
	} elseif ( is_category() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$context['type']      = 'category';
			$context['taxonomy']  = 'category';
			$context['term_id']   = (int) $term->term_id;
			$context['archive_title'] = single_cat_title( '', false );
			$context['description']   = term_description( $term->term_id, 'category' );
		}
	} elseif ( is_tag() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$context['type']      = 'tag';
			$context['taxonomy']  = 'post_tag';
			$context['term_id']   = (int) $term->term_id;
			$context['archive_title'] = single_tag_title( '', false );
			$context['description']   = term_description( $term->term_id, 'post_tag' );
		}
	} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
		$posts_page_id = absint( get_option( 'page_for_posts' ) );
		$context['type']          = 'blog';
		$context['archive_title'] = get_the_title( $posts_page_id );
		$context['description']   = get_post_field( 'post_content', $posts_page_id );
	} elseif ( is_author() ) {
		$context['type']          = 'author';
		$context['archive_title'] = get_the_archive_title();
		$context['description']   = get_the_archive_description();
	} elseif ( is_archive() ) {
		$context['type']          = 'archive';
		$context['archive_title'] = get_the_archive_title();
		$context['description']   = get_the_archive_description();
	}

	if ( '' === $context['description'] && ! is_search() && ! is_home() ) {
		$context['description'] = get_the_archive_description();
	}

	return $context;
}

/**
 * Build WP_Query args for blog archive widget.
 *
 * @param array<string,mixed> $args Request args.
 * @return array<string,mixed>
 */
function webmz_blog_archive_build_query_args( $args ) {
	$posts_per_page = isset( $args['posts_per_page'] ) ? max( 1, absint( $args['posts_per_page'] ) ) : 6;
	$page           = isset( $args['page'] ) ? max( 1, absint( $args['page'] ) ) : 1;
	$search         = isset( $args['search'] ) ? sanitize_text_field( (string) $args['search'] ) : '';
	$taxonomy       = isset( $args['taxonomy'] ) ? sanitize_key( (string) $args['taxonomy'] ) : 'category';
	$term_id        = isset( $args['term_id'] ) ? absint( $args['term_id'] ) : 0;

	if ( ! in_array( $taxonomy, array( 'category', 'post_tag' ), true ) ) {
		$taxonomy = 'category';
	}

	$query_args = array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => $posts_per_page,
		'paged'                  => $page,
		'ignore_sticky_posts'    => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
	);

	if ( '' !== $search ) {
		$query_args['s'] = $search;
	}

	if ( $term_id > 0 ) {
		$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => array( $term_id ),
			),
		);
	}

	return $query_args;
}

/**
 * Format a post date for archive cards.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function webmz_blog_archive_format_post_date( $post_id ) {
	$timestamp = get_post_timestamp( $post_id );

	if ( ! $timestamp && function_exists( 'get_post_time' ) ) {
		$timestamp = (int) get_post_time( 'U', true, $post_id );
	}

	if ( $timestamp && function_exists( 'webmz_format_jalali_date' ) ) {
		return webmz_format_jalali_date( $timestamp, false, false );
	}

	return get_the_date( '', $post_id );
}

/**
 * Get primary category name for a post card.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function webmz_blog_archive_get_post_category_label( $post_id ) {
	$categories = get_the_category( $post_id );

	if ( empty( $categories ) || ! is_array( $categories ) ) {
		return esc_html__( 'دسته‌بندی نشده', 'tadris' );
	}

	if ( function_exists( 'webmz_get_deepest_term' ) ) {
		$term = webmz_get_deepest_term( $categories );
		if ( $term instanceof WP_Term ) {
			return $term->name;
		}
	}

	return $categories[0]->name;
}

/**
 * Get trimmed excerpt for archive cards (~2 lines).
 *
 * @param int $post_id Post ID.
 * @return string
 */
function webmz_blog_archive_get_card_excerpt( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id ) {
		return '';
	}

	$excerpt = get_the_excerpt( $post_id );

	if ( '' === trim( $excerpt ) ) {
		$excerpt = (string) get_post_field( 'post_content', $post_id );
	}

	return wp_trim_words( wp_strip_all_tags( $excerpt ), 16, '…' );
}

/**
 * Base URL for unfiltered blog archive view.
 *
 * @return string
 */
function webmz_blog_archive_get_base_url() {
	$page_for_posts = absint( get_option( 'page_for_posts' ) );

	if ( $page_for_posts ) {
		return get_permalink( $page_for_posts );
	}

	$archive_link = get_post_type_archive_link( 'post' );

	return $archive_link ? $archive_link : home_url( '/' );
}

/**
 * Render a single archive post card.
 *
 * @param int                 $post_id  Post ID.
 * @param array<string,mixed> $settings Widget settings.
 * @return void
 */
function webmz_blog_archive_render_card( $post_id, $settings = array() ) {
	$post_id = absint( $post_id );

	if ( ! $post_id ) {
		return;
	}

	$title     = get_the_title( $post_id );
	$permalink = get_permalink( $post_id );
	$author_id = (int) get_post_field( 'post_author', $post_id );
	$author    = get_the_author_meta( 'display_name', $author_id );
	$date      = webmz_blog_archive_format_post_date( $post_id );
	$excerpt   = webmz_blog_archive_get_card_excerpt( $post_id );
	$tag       = webmz_blog_archive_get_post_category_label( $post_id );
	$title_tag = isset( $settings['card_title_tag'] ) ? webmz_sanitize_heading_tag( $settings['card_title_tag'] ) : 'h3';
	$read_more = ! empty( $settings['read_more_text'] ) ? $settings['read_more_text'] : esc_html__( 'مطالعه بیشتر', 'tadris' );
	?>
	<article class="webmz-blog-archive-card" data-post-id="<?php echo esc_attr( (string) $post_id ); ?>">
		<a class="webmz-blog-archive-card__image" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
			<?php
			if ( has_post_thumbnail( $post_id ) ) {
				echo webmz_get_post_loop_thumbnail( $post_id, array( 'alt' => esc_attr( $title ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				$letter = function_exists( 'mb_substr' ) ? mb_substr( $title, 0, 1 ) : substr( $title, 0, 1 );
				?>
				<span class="webmz-blog-archive-card__placeholder" aria-hidden="true"><?php echo esc_html( $letter ); ?></span>
				<?php
			}
			?>
		</a>
		<div class="webmz-blog-archive-card__body">
			<div class="webmz-blog-archive-card__meta">
				<?php echo get_avatar( $author_id, 40, '', $author, array( 'class' => 'webmz-blog-archive-card__avatar' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="webmz-blog-archive-card__meta-text">
					<span class="webmz-blog-archive-card__author"><?php echo esc_html( $author ); ?></span>
					<span class="webmz-blog-archive-card__date"><?php echo esc_html( $date ); ?></span>
				</div>
			</div>
			<<?php echo esc_attr( $title_tag ); ?> class="webmz-blog-archive-card__title">
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
			</<?php echo esc_attr( $title_tag ); ?>>
			<div class="webmz-blog-archive-card__excerpt"><p><?php echo esc_html( $excerpt ); ?></p></div>
			<div class="webmz-blog-archive-card__footer">
				<a class="webmz-blog-archive-card__more" href="<?php echo esc_url( $permalink ); ?>">
					<span aria-hidden="true" class="webmz-blog-archive-card__more-icon">←</span>
					<?php echo esc_html( $read_more ); ?>
				</a>
				<span class="webmz-blog-archive-card__tag"><?php echo esc_html( $tag ); ?></span>
			</div>
		</div>
	</article>
	<?php
}

/**
 * Render posts grid HTML.
 *
 * @param WP_Query            $query    Posts query.
 * @param array<string,mixed> $settings Widget settings.
 * @return string
 */
function webmz_blog_archive_render_posts_html( $query, $settings = array() ) {
	if ( ! $query instanceof WP_Query || ! $query->have_posts() ) {
		return '<div class="webmz-blog-archive__empty">' . esc_html__( 'مطلبی یافت نشد.', 'tadris' ) . '</div>';
	}

	ob_start();

	while ( $query->have_posts() ) {
		$query->the_post();
		webmz_blog_archive_render_card( get_the_ID(), $settings );
	}

	wp_reset_postdata();

	return (string) ob_get_clean();
}

/**
 * Render numeric pagination markup.
 *
 * @param WP_Query $query Query object.
 * @param int      $page  Current page.
 * @return string
 */
function webmz_blog_archive_render_pagination_html( $query, $page = 1 ) {
	$total = (int) $query->max_num_pages;
	$page  = max( 1, absint( $page ) );

	if ( $total <= 1 ) {
		return '';
	}

	$links = paginate_links(
		array(
			'base'      => '%_%',
			'format'    => '?page=%#%',
			'current'   => $page,
			'total'     => $total,
			'type'      => 'list',
			'prev_text' => esc_html__( 'قبلی', 'tadris' ),
			'next_text' => esc_html__( 'بعدی', 'tadris' ),
		)
	);

	if ( ! $links ) {
		return '';
	}

	return '<nav class="webmz-blog-archive__pagination" aria-label="' . esc_attr__( 'صفحه‌بندی مطالب', 'tadris' ) . '">' . $links . '</nav>';
}

/**
 * Render taxonomy filter list.
 *
 * @param string $taxonomy   Taxonomy slug.
 * @param int    $active_id  Active term ID.
 * @return string
 */
function webmz_blog_archive_render_terms_html( $taxonomy, $active_id = 0 ) {
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '<p class="webmz-blog-archive__terms-empty">' . esc_html__( 'موردی برای فیلتر وجود ندارد.', 'tadris' ) . '</p>';
	}

	ob_start();
	?>
	<button
		type="button"
		class="webmz-blog-archive__term webmz-blog-archive__term--all<?php echo 0 === $active_id ? ' is-active' : ''; ?>"
		data-term-id="0"
		data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"
	>
		<span class="webmz-blog-archive__term-name"><?php esc_html_e( 'همه', 'tadris' ); ?></span>
	</button>
	<ul class="webmz-blog-archive__terms" role="list" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>">
		<?php foreach ( $terms as $term ) : ?>
			<?php
			if ( ! $term instanceof WP_Term ) {
				continue;
			}
			$term_link = get_term_link( $term );
			$term_url  = is_wp_error( $term_link ) ? '' : $term_link;
			?>
			<li>
				<button type="button" class="webmz-blog-archive__term<?php echo (int) $term->term_id === $active_id ? ' is-active' : ''; ?>" data-term-id="<?php echo esc_attr( (string) $term->term_id ); ?>" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"<?php echo $term_url ? ' data-term-url="' . esc_url( $term_url ) . '"' : ''; ?>>
					<span class="webmz-blog-archive__term-count"><?php echo esc_html( function_exists( 'webmz_to_persian_digits' ) ? webmz_to_persian_digits( (string) $term->count ) : (string) $term->count ); ?></span>
					<span class="webmz-blog-archive__term-name"><?php echo esc_html( $term->name ); ?></span>
				</button>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render category/tag filter tabs and panels.
 *
 * @param array<string,mixed> $settings    Widget settings.
 * @param array<string,mixed> $context     Archive context.
 * @param string              $active_tab  Active tab slug.
 * @return string
 */
function webmz_blog_archive_render_filter_sidebar( $settings, $context, $active_tab = 'category' ) {
	$active_tab   = in_array( $active_tab, array( 'category', 'post_tag' ), true ) ? $active_tab : 'category';
	$active_term  = in_array( $context['type'], array( 'category', 'tag' ), true ) ? (int) $context['term_id'] : 0;
	$category_lbl = ! empty( $settings['category_label'] ) ? $settings['category_label'] : esc_html__( 'دسته بندی مقالات', 'tadris' );
	$tag_lbl      = ! empty( $settings['tag_label'] ) ? $settings['tag_label'] : esc_html__( 'برچسب مقالات', 'tadris' );
	$cat_active   = 'category' === $active_tab ? $active_term : 0;
	$tag_active   = 'post_tag' === $active_tab ? $active_term : 0;

	ob_start();
	?>
	<div class="webmz-blog-archive__tabs" role="tablist" aria-label="<?php esc_attr_e( 'فیلتر دسته و برچسب', 'tadris' ); ?>">
		<button
			type="button"
			class="webmz-blog-archive__tab<?php echo 'category' === $active_tab ? ' is-active' : ''; ?>"
			role="tab"
			aria-selected="<?php echo 'category' === $active_tab ? 'true' : 'false'; ?>"
			data-tab="category"
		><?php echo esc_html( $category_lbl ); ?></button>
		<button
			type="button"
			class="webmz-blog-archive__tab<?php echo 'post_tag' === $active_tab ? ' is-active' : ''; ?>"
			role="tab"
			aria-selected="<?php echo 'post_tag' === $active_tab ? 'true' : 'false'; ?>"
			data-tab="post_tag"
		><?php echo esc_html( $tag_lbl ); ?></button>
	</div>
	<div class="webmz-blog-archive__tab-panels">
		<div
			class="webmz-blog-archive__tab-panel<?php echo 'category' === $active_tab ? ' is-active' : ''; ?>"
			role="tabpanel"
			data-tab-panel="category"
			<?php echo 'category' === $active_tab ? '' : 'hidden'; ?>
		>
			<?php echo webmz_blog_archive_render_terms_html( 'category', $cat_active ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div
			class="webmz-blog-archive__tab-panel<?php echo 'post_tag' === $active_tab ? ' is-active' : ''; ?>"
			role="tabpanel"
			data-tab-panel="post_tag"
			<?php echo 'post_tag' === $active_tab ? '' : 'hidden'; ?>
		>
			<?php echo webmz_blog_archive_render_terms_html( 'post_tag', $tag_active ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * AJAX: filter and paginate blog archive posts.
 *
 * @return void
 */
function webmz_blog_archive_ajax_filter() {
	check_ajax_referer( 'webmz_blog_archive', 'nonce' );

	$posts_per_page = isset( $_POST['posts_per_page'] ) ? max( 1, absint( wp_unslash( $_POST['posts_per_page'] ) ) ) : 6;
	$page           = isset( $_POST['page'] ) ? max( 1, absint( wp_unslash( $_POST['page'] ) ) ) : 1;
	$search         = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
	$taxonomy       = isset( $_POST['taxonomy'] ) ? sanitize_key( wp_unslash( $_POST['taxonomy'] ) ) : 'category';
	$term_id        = isset( $_POST['term_id'] ) ? absint( wp_unslash( $_POST['term_id'] ) ) : 0;
	$load_mode      = isset( $_POST['load_mode'] ) ? sanitize_key( wp_unslash( $_POST['load_mode'] ) ) : 'pagination';
	$card_title_tag = isset( $_POST['card_title_tag'] ) ? webmz_sanitize_heading_tag( wp_unslash( $_POST['card_title_tag'] ) ) : 'h3';
	$read_more_text = isset( $_POST['read_more_text'] ) ? sanitize_text_field( wp_unslash( $_POST['read_more_text'] ) ) : esc_html__( 'مطالعه بیشتر', 'tadris' );

	$settings = array(
		'card_title_tag' => $card_title_tag,
		'read_more_text' => $read_more_text,
	);

	$query_args = webmz_blog_archive_build_query_args(
		array(
			'posts_per_page' => $posts_per_page,
			'page'           => $page,
			'search'         => $search,
			'taxonomy'       => $taxonomy,
			'term_id'        => $term_id,
		)
	);

	$query = new WP_Query( $query_args );

	wp_send_json_success(
		array(
			'html'        => webmz_blog_archive_render_posts_html( $query, $settings ),
			'pagination'  => 'pagination' === $load_mode ? webmz_blog_archive_render_pagination_html( $query, $page ) : '',
			'currentPage' => $page,
			'maxPages'    => (int) $query->max_num_pages,
			'foundPosts'  => (int) $query->found_posts,
			'hasMore'     => $page < (int) $query->max_num_pages,
		)
	);
}
add_action( 'wp_ajax_webmz_blog_archive_filter', 'webmz_blog_archive_ajax_filter' );
add_action( 'wp_ajax_nopriv_webmz_blog_archive_filter', 'webmz_blog_archive_ajax_filter' );
