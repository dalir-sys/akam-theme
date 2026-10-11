<?php
/** Shared helpers for building Akam demo content with wp eval-file. */
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

function akd_json( $file ) {
	$d = json_decode( file_get_contents( $file ), true );
	if ( null === $d ) WP_CLI::error( "bad json $file" );
	return $d;
}

/** Import (or reuse) an image; keyed by source basename. */
function akd_image( $path, $title = '' ) {
	static $cache = array();
	$key = 'akd:' . basename( $path );
	if ( isset( $cache[ $key ] ) ) return $cache[ $key ];
	$found = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'any', 'meta_key' => '_akd_src', 'meta_value' => $key, 'fields' => 'ids', 'numberposts' => 1 ) );
	if ( $found ) return $cache[ $key ] = (int) $found[0];
	if ( ! file_exists( $path ) ) WP_CLI::error( "missing image $path" );
	$tmp = wp_tempnam( basename( $path ) );
	copy( $path, $tmp );
	$id = media_handle_sideload( array( 'name' => basename( $path ), 'tmp_name' => $tmp ), 0, $title ?: pathinfo( $path, PATHINFO_FILENAME ) );
	if ( is_wp_error( $id ) ) WP_CLI::error( $id->get_error_message() );
	update_post_meta( $id, '_akd_src', $key );
	return $cache[ $key ] = (int) $id;
}

function akd_delete_all( $post_type ) {
	$ids = get_posts( array( 'post_type' => $post_type, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
	foreach ( $ids as $id ) wp_delete_post( $id, true );
	return count( $ids );
}

function akd_term( $taxonomy, $name, $slug, $args = array() ) {
	$t = get_term_by( 'slug', $slug, $taxonomy );
	if ( $t ) { wp_update_term( $t->term_id, $taxonomy, array_merge( array( 'name' => $name ), $args ) ); return (int) $t->term_id; }
	$r = wp_insert_term( $name, $taxonomy, array_merge( array( 'slug' => $slug ), $args ) );
	if ( is_wp_error( $r ) ) WP_CLI::error( $r->get_error_message() );
	return (int) $r['term_id'];
}

function akd_user( $login, $display, $email ) {
	$u = get_user_by( 'login', $login );
	if ( $u ) { wp_update_user( array( 'ID' => $u->ID, 'display_name' => $display ) ); return $u->ID; }
	$parts = explode( ' ', $display, 2 );
	return wp_insert_user( array( 'user_login' => $login, 'user_pass' => wp_generate_password(), 'user_email' => $email, 'display_name' => $display, 'nickname' => $display, 'first_name' => $parts[0], 'last_name' => $parts[1] ?? '', 'role' => 'author' ) );
}

/** Add approved WooCommerce reviews and refresh rating meta. */
function akd_reviews( $product_id, $reviews ) {
	foreach ( get_comments( array( 'post_id' => $product_id, 'fields' => 'ids' ) ) as $cid ) wp_delete_comment( $cid, true );
	$counts = array(); $sum = 0;
	foreach ( $reviews as $i => $r ) {
		$cid = wp_insert_comment( array(
			'comment_post_ID' => $product_id, 'comment_author' => $r['name'], 'comment_author_email' => 'user' . $i . '@example.com',
			'comment_content' => $r['text'], 'comment_type' => 'review', 'comment_approved' => 1,
			'comment_date' => gmdate( 'Y-m-d H:i:s', time() - ( $i + 1 ) * 86400 * 3 ),
		) );
		update_comment_meta( $cid, 'rating', $r['rating'] );
		update_comment_meta( $cid, 'verified', 1 );
		$counts[ $r['rating'] ] = ( $counts[ $r['rating'] ] ?? 0 ) + 1; $sum += $r['rating'];
	}
	return array( $counts, $reviews ? $sum / count( $reviews ) : 0 );
}

function akd_comments( $post_id, $items ) {
	foreach ( $items as $i => $c ) {
		wp_insert_comment( array( 'comment_post_ID' => $post_id, 'comment_author' => $c[0], 'comment_author_email' => 'reader' . $i . '@example.com', 'comment_content' => $c[1], 'comment_approved' => 1, 'comment_date' => gmdate( 'Y-m-d H:i:s', time() - ( $i + 1 ) * 7200 ) ) );
	}
}

function akd_set_elementor( $post_id, $json_file ) {
	$json = file_get_contents( $json_file );
	update_post_meta( $post_id, '_elementor_data', wp_slash( $json ) );
	update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
	if ( ! get_post_meta( $post_id, '_elementor_version', true ) ) update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );
	foreach ( array( '_elementor_css', '_elementor_element_cache', '_elementor_page_assets' ) as $k ) delete_post_meta( $post_id, $k );
}

/** Write a small placeholder file into WooCommerce's protected uploads folder and return its URL. */
function akd_download_file( $name, $label ) {
	$up = wp_upload_dir();
	$dir = $up['basedir'] . '/woocommerce_uploads/akam-demo-files';
	wp_mkdir_p( $dir );
	file_put_contents( $dir . '/' . $name, "Akam demo file: $label\n" );
	return $up['baseurl'] . '/woocommerce_uploads/akam-demo-files/' . $name;
}

/** Replace menu items of a menu with a list of [title, url, icon_id|null, children[]] */
function akd_menu( $menu_id, $items, $parent = 0 ) {
	if ( 0 === $parent ) foreach ( wp_get_nav_menu_items( $menu_id ) ?: array() as $it ) wp_delete_post( $it->ID, true );
	$ids = array();
	foreach ( $items as $pos => $it ) {
		$args = array( 'menu-item-title' => $it['title'], 'menu-item-status' => 'publish', 'menu-item-parent-id' => $parent, 'menu-item-position' => $pos + 1 );
		if ( ! empty( $it['page'] ) ) { $args += array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $it['page'] ); }
		elseif ( ! empty( $it['term'] ) ) { $args += array( 'menu-item-type' => 'taxonomy', 'menu-item-object' => $it['tax'], 'menu-item-object-id' => $it['term'] ); }
		else { $args += array( 'menu-item-type' => 'custom', 'menu-item-url' => $it['url'] ?? '#' ); }
		$id = wp_update_nav_menu_item( $menu_id, 0, $args );
		foreach ( $it['meta'] ?? array() as $k => $v ) update_post_meta( $id, $k, $v );
		if ( ! empty( $it['children'] ) ) akd_menu( $menu_id, $it['children'], $id );
		$ids[] = $id;
	}
	return $ids;
}

add_filter( 'upload_mimes', function ( $m ) { $m['svg'] = 'image/svg+xml'; return $m; } );
add_filter( 'wp_check_filetype_and_ext', function ( $data, $file, $filename ) {
	if ( str_ends_with( strtolower( $filename ), '.svg' ) ) { $data['ext'] = 'svg'; $data['type'] = 'image/svg+xml'; $data['proper_filename'] = $filename; }
	return $data;
}, 10, 3 );
