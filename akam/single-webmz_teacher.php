<?php
/** Single teacher template. @package WebMZ */
defined( 'ABSPATH' ) || exit;
get_header();
if ( ! webmz_render_location( 'single_teacher' ) ) :
	?>
	<main id="primary" class="site-main webmz-container webmz-teachers-page webmz-teacher-single">
		<?php
		while ( have_posts() ) :
			the_post();
			$post_id = get_the_ID();
			?>
			<article <?php post_class( 'webmz-teacher-single__article' ); ?>>
				<?php echo webmz_render_teacher_single_hero( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( get_the_content() ) : ?>
					<div class="webmz-teacher-single__content"><?php the_content(); ?></div>
				<?php endif; ?>
				<?php echo webmz_render_teacher_single_sections( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo webmz_render_teacher_single_courses( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo webmz_render_teacher_single_comments( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</article>
			<?php
		endwhile;
		?>
	</main>
	<?php
endif;
get_footer();
