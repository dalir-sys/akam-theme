<?php
/** Search results template. @package WebMZ */
defined( 'ABSPATH' ) || exit;
get_header();
if ( ! webmz_render_location( 'search' ) ) :
	?>
	<main id="primary" class="site-main webmz-container webmz-listing-page">
		<header class="page-header"><h1 class="page-title"><?php printf( esc_html__( 'نتایج جستجو برای: %s', 'tadris' ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?></h1></header>
		<?php if ( have_posts() ) : ?>
			<div class="webmz-loop webmz-columns-3"><?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content/card', get_post_type() ); endwhile; ?></div>
			<?php the_posts_pagination(); ?>
		<?php else : get_template_part( 'template-parts/content/none' ); endif; ?>
	</main>
	<?php
endif;
get_footer();
