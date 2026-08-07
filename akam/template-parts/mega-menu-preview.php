<?php
/**
 * Minimal preview template for Elementor-editable mega menu content.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

get_header();
$preview_post = null;

if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$preview_id = absint( wp_unslash( $_GET['elementor-preview'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( $preview_id && 'webmz_mega_menu' === get_post_type( $preview_id ) ) {
		$preview_post = get_post( $preview_id );
	}
}
?>
<main id="primary" class="site-main webmz-mega-menu-preview webmz-container webmz-inner-page">
	<?php
	if ( $preview_post ) {
		$GLOBALS['post'] = $preview_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		setup_postdata( $preview_post );
	}

	if ( $preview_post || have_posts() ) :
		if ( ! $preview_post ) {
			the_post();
		}
		?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'webmz-mega-menu-preview__entry' ); ?>>
				<div class="entry-content webmz-mega-menu-preview__content">
					<?php the_content(); ?>
				</div>
			</article>
		<?php
		wp_reset_postdata();
	endif;
	?>
</main>
<?php
get_footer();
