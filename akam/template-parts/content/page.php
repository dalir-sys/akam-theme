<?php
/** Page fallback content. @package WebMZ */
defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'webmz-entry' ); ?>>
	<header class="entry-header"><h1 class="entry-title"><?php the_title(); ?></h1></header>
	<?php if ( has_post_thumbnail() ) : ?><div class="entry-thumbnail"><?php echo webmz_get_post_loop_thumbnail(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php endif; ?>
	<div class="entry-content"><?php the_content(); wp_link_pages(); ?></div>
</article>
