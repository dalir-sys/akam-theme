<?php
/** Single post fallback content. @package WebMZ */
defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'webmz-entry' ); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<div class="entry-meta"><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></div>
	</header>
	<?php if ( has_post_thumbnail() ) : ?><div class="entry-thumbnail"><?php echo webmz_get_post_loop_thumbnail(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php endif; ?>
	<div class="entry-content"><?php the_content(); wp_link_pages(); ?></div>
	<footer class="entry-footer"><?php the_tags( '<div class="tags-links">', ' ', '</div>' ); ?></footer>
</article>
