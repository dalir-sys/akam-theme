<?php
/** Teachers archive template. @package WebMZ */
defined( 'ABSPATH' ) || exit;
get_header();
if ( ! webmz_render_location( 'archive_teacher' ) ) :
	global $wp_query;
	$current_page   = max( 1, (int) get_query_var( 'paged', 1 ) );
	$posts_per_page = max( 1, (int) ( $wp_query->get( 'posts_per_page' ) ?: get_option( 'posts_per_page', 12 ) ) );
	$card_settings  = array(
		'title_tag'   => 'h3',
		'button_text' => esc_html__( 'مشاهده پروفایل', 'tadris' ),
		'show_button' => true,
		'show_stats'  => false,
	);
	$config         = array(
		'postsPerPage' => $posts_per_page,
		'titleTag'     => 'h3',
		'buttonText'   => $card_settings['button_text'],
		'showStats'    => false,
		'currentPage'  => $current_page,
		'maxPages'     => (int) $wp_query->max_num_pages,
	);
	?>
	<main id="primary" class="site-main webmz-container webmz-teachers-page">
		<div class="webmz-teachers-archive" data-webmz-teachers-archive="<?php echo esc_attr( wp_json_encode( $config ) ); ?>">
			<?php webmz_render_breadcrumb_nav( 'webmz-teachers-archive__breadcrumb' ); ?>
			<h1 class="webmz-teachers-archive__heading"><?php esc_html_e( 'آرشیو اساتید', 'tadris' ); ?></h1>
			<div class="webmz-teachers-archive__status" role="status" aria-live="polite" aria-atomic="true" hidden></div>
			<?php if ( have_posts() ) : ?>
				<div class="webmz-teachers-archive__grid" style="--webmz-grid-columns: 4; --webmz-grid-tablet-columns: 2; --webmz-grid-mobile-columns: 1; --webmz-grid-gap: 24px;">
					<?php
					while ( have_posts() ) :
						the_post();
						echo webmz_render_teacher_card( get_the_ID(), $card_settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					endwhile;
					?>
				</div>
				<div class="webmz-teachers-archive__pagination-wrap">
					<?php echo webmz_teachers_archive_render_pagination_html( $wp_query, $current_page, $posts_per_page ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php else : ?>
				<div class="webmz-teachers-archive__empty"><?php esc_html_e( 'مدرسی یافت نشد.', 'tadris' ); ?></div>
			<?php endif; ?>
		</div>
	</main>
	<?php
endif;
get_footer();
