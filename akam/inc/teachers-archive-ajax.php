<?php
/**
 * Teachers archive AJAX helpers and rendering.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build teachers archive query args.
 *
 * @param array<string,mixed> $args Query args.
 * @return array<string,mixed>
 */
function webmz_teachers_archive_build_query_args( $args = array() ) {
	$posts_per_page = isset( $args['posts_per_page'] ) ? max( 1, absint( $args['posts_per_page'] ) ) : 12;
	$page           = isset( $args['page'] ) ? max( 1, absint( $args['page'] ) ) : 1;
	$orderby        = isset( $args['orderby'] ) ? sanitize_key( (string) $args['orderby'] ) : 'date';
	$order          = isset( $args['order'] ) ? strtoupper( sanitize_key( (string) $args['order'] ) ) : 'DESC';
	$search         = isset( $args['search'] ) ? sanitize_text_field( (string) $args['search'] ) : '';
	$include        = isset( $args['include'] ) ? array_map( 'absint', (array) $args['include'] ) : array();

	if ( ! in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
		$order = 'DESC';
	}

	$allowed_orderby = array( 'date', 'title', 'menu_order', 'rand' );
	if ( ! in_array( $orderby, $allowed_orderby, true ) ) {
		$orderby = 'date';
	}

	$query_args = array(
		'post_type'              => WEBMZ_TEACHER_POST_TYPE,
		'post_status'            => 'publish',
		'posts_per_page'         => $posts_per_page,
		'paged'                  => $page,
		'orderby'                => $orderby,
		'order'                  => $order,
		'ignore_sticky_posts'    => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => false,
	);

	if ( '' !== $search ) {
		$query_args['s'] = $search;
	}

	$include = array_values( array_filter( $include ) );
	if ( ! empty( $include ) ) {
		$query_args['post__in'] = $include;
		$query_args['orderby']  = 'post__in';
	}

	return $query_args;
}

/**
 * Render teachers grid HTML.
 *
 * @param WP_Query            $query    Teachers query.
 * @param array<string,mixed> $settings Card settings.
 * @return string
 */
function webmz_teachers_archive_render_cards_html( $query, $settings = array() ) {
	if ( ! $query instanceof WP_Query || ! $query->have_posts() ) {
		return '<div class="webmz-teachers-archive__empty">' . esc_html__( 'مدرسی یافت نشد.', 'tadris' ) . '</div>';
	}

	$html = '';

	while ( $query->have_posts() ) {
		$query->the_post();
		$html .= webmz_render_teacher_card( get_the_ID(), $settings );
	}

	wp_reset_postdata();

	return $html;
}

/**
 * Get per-page options for teachers archive.
 *
 * @return array<int,int>
 */
function webmz_teachers_archive_per_page_options() {
	return array( 8, 12, 16, 24 );
}

/**
 * Render teachers archive pagination.
 *
 * @param WP_Query $query    Teachers query.
 * @param int      $page     Current page.
 * @param int      $per_page Posts per page.
 * @return string
 */
function webmz_teachers_archive_render_pagination_html( $query, $page = 1, $per_page = 12 ) {
	$total    = (int) $query->max_num_pages;
	$page     = max( 1, absint( $page ) );
	$per_page = max( 1, absint( $per_page ) );

	if ( ! $query->found_posts ) {
		return '';
	}

	$options = webmz_teachers_archive_per_page_options();
	$found_posts = (int) $query->found_posts;

	ob_start();
	?>
	<nav class="webmz-teachers-archive__pagination" aria-label="<?php esc_attr_e( 'صفحه‌بندی مدرسین', 'tadris' ); ?>">
		<div class="webmz-teachers-archive__per-page">
			<span class="webmz-teachers-archive__per-page-label"><?php esc_html_e( 'تعداد در صفحه', 'tadris' ); ?></span>
			<label class="webmz-teachers-archive__per-page-field">
				<span class="screen-reader-text"><?php esc_html_e( 'تعداد در صفحه', 'tadris' ); ?></span>
				<select class="webmz-teachers-archive__per-page-select">
					<?php foreach ( $options as $option ) : ?>
						<option value="<?php echo esc_attr( (string) $option ); ?>" <?php selected( $per_page, $option ); ?>>
							<?php echo esc_html( webmz_to_persian_digits( (string) $option ) ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
			<span class="webmz-teachers-archive__results-count">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: number of found results */
						esc_html__( '%s نتیجه', 'tadris' ),
						webmz_to_persian_digits( (string) $found_posts )
					)
				);
				?>
			</span>
		</div>

		<div class="webmz-teachers-archive__pagination-main">
			<?php if ( $page < $total ) : ?>
				<button type="button" class="webmz-teachers-archive__next" data-page="<?php echo esc_attr( (string) ( $page + 1 ) ); ?>">
					<span class="webmz-teachers-archive__next-icon" aria-hidden="true"></span>
					<span><?php esc_html_e( 'صفحه بعد', 'tadris' ); ?></span>
				</button>
			<?php endif; ?>

			<div class="webmz-teachers-archive__pages">
				<?php for ( $i = 1; $i <= $total; $i++ ) : ?>
					<button
						type="button"
						class="webmz-teachers-archive__page<?php echo $i === $page ? ' is-active' : ''; ?>"
						data-page="<?php echo esc_attr( (string) $i ); ?>"
						<?php echo $i === $page ? 'aria-current="page"' : ''; ?>
					><?php echo esc_html( webmz_to_persian_digits( (string) $i ) ); ?></button>
				<?php endfor; ?>
			</div>
		</div>
	</nav>
	<?php
	return (string) ob_get_clean();
}

/**
 * AJAX: paginate teachers archive.
 *
 * @return void
 */
function webmz_teachers_archive_ajax_filter() {
	check_ajax_referer( 'webmz_teachers_archive', 'nonce' );

	$posts_per_page = isset( $_POST['posts_per_page'] ) ? max( 1, absint( wp_unslash( $_POST['posts_per_page'] ) ) ) : 12;
	$page           = isset( $_POST['page'] ) ? max( 1, absint( wp_unslash( $_POST['page'] ) ) ) : 1;
	$title_tag      = isset( $_POST['title_tag'] ) ? webmz_sanitize_heading_tag( wp_unslash( $_POST['title_tag'] ) ) : 'h3';
	$button_text    = isset( $_POST['button_text'] ) ? sanitize_text_field( wp_unslash( $_POST['button_text'] ) ) : esc_html__( 'مشاهده پروفایل', 'tadris' );
	$show_stats     = ! empty( $_POST['show_stats'] ) && '1' === (string) wp_unslash( $_POST['show_stats'] );

	$settings = array(
		'title_tag'   => $title_tag,
		'button_text' => $button_text,
		'show_stats'  => $show_stats,
	);

	$query = new WP_Query(
		webmz_teachers_archive_build_query_args(
			array(
				'posts_per_page' => $posts_per_page,
				'page'           => $page,
			)
		)
	);

	wp_send_json_success(
		array(
			'html'         => webmz_teachers_archive_render_cards_html( $query, $settings ),
			'pagination'   => webmz_teachers_archive_render_pagination_html( $query, $page, $posts_per_page ),
			'currentPage'  => $page,
			'maxPages'     => (int) $query->max_num_pages,
			'foundPosts'   => (int) $query->found_posts,
		)
	);
}
add_action( 'wp_ajax_webmz_teachers_archive_filter', 'webmz_teachers_archive_ajax_filter' );
add_action( 'wp_ajax_nopriv_webmz_teachers_archive_filter', 'webmz_teachers_archive_ajax_filter' );

/**
 * Default teachers archive to 12 posts per page.
 *
 * @param WP_Query $query Main query.
 * @return void
 */
function webmz_teachers_archive_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( WEBMZ_TEACHER_POST_TYPE ) ) {
		return;
	}

	$query->set( 'posts_per_page', 12 );
}
add_action( 'pre_get_posts', 'webmz_teachers_archive_pre_get_posts' );
