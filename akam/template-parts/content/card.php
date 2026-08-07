<?php
/** Card content for fallback archives and dynamic archive widget. @package WebMZ */
defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'webmz-post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="webmz-post-card__image" href="<?php the_permalink(); ?>"><?php echo webmz_get_post_loop_thumbnail(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
	<?php else : ?>
		<a class="webmz-post-card__image webmz-post-card__image--empty" href="<?php the_permalink(); ?>" aria-hidden="true"><span>W</span></a>
	<?php endif; ?>
	<div class="webmz-post-card__body">
		<div class="webmz-post-card__meta"><?php echo esc_html( get_the_date() ); ?></div>
		<h2 class="webmz-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<div class="webmz-post-card__excerpt"><?php the_excerpt(); ?></div>
		<a class="webmz-post-card__link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'ادامه مطلب', 'tadris' ); ?> <span aria-hidden="true">←</span></a>
	</div>
</article>
