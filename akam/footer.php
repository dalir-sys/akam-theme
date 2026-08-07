<?php
/**
 * Site footer and document closing tags.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;
?>
	<?php if ( ! webmz_render_location( 'footer' ) ) : ?>
		<?php get_template_part( 'template-parts/footer/default' ); ?>
	<?php endif; ?>
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
