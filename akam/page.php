<?php
/** Page template. @package WebMZ */
defined( 'ABSPATH' ) || exit;
get_header();
if ( ! webmz_render_location( 'page' ) ) :
	?>
	<main id="primary" class="site-main webmz-container webmz-content-narrow webmz-inner-page">
		<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content/page' ); endwhile; ?>
	</main>
	<?php
endif;
get_footer();
