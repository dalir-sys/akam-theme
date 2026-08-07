<?php
/** Posts index template. @package WebMZ */
defined( 'ABSPATH' ) || exit;
get_header();
if ( ! webmz_render_location( 'archive_post' ) ) :
	?>
	<main id="primary" class="site-main webmz-container webmz-listing-page">
		<header class="page-header"><h1 class="page-title"><?php single_post_title(); ?></h1></header>
		<?php if ( have_posts() ) : ?>
			<div class="webmz-loop webmz-columns-3">
				<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content/card', get_post_type() ); endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : get_template_part( 'template-parts/content/none' ); endif; ?>
	</main>
	<?php
endif;
get_footer();
