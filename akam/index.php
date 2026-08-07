<?php
/** Fallback index template. @package WebMZ */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="primary" class="site-main webmz-container webmz-listing-page">
	<?php if ( have_posts() ) : ?>
		<div class="webmz-loop webmz-columns-3"><?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content/card', get_post_type() ); endwhile; ?></div>
		<?php the_posts_pagination(); ?>
	<?php else : get_template_part( 'template-parts/content/none' ); endif; ?>
</main>
<?php get_footer(); ?>
