<?php
$ND = rtrim( $args[0], '/' );
require $ND . '/lib/common.php';
$O = "$ND/d5out";
$map = akd_json( "$ND/d5_map.json" );
foreach ( array( 103 => 'home', 15 => 'header', 565 => 'footer', 1185 => 'mega', 976 => 'single-product', 632 => 'single-post', 37 => 'about', 1309 => 'contact', 1115 => 'blog-archive', 1153 => 'shop-archive' ) as $id => $f ) akd_set_elementor( $id, "$O/$f.json" );
wp_update_post( array( 'ID' => 103, 'post_title' => 'صفحه اصلی آکام ذهن' ) );
$ta = get_posts( array( 'post_type' => 'webmz_layout', 'meta_key' => '_webmz_layout_type', 'meta_value' => 'archive_teacher', 'numberposts' => 1, 'fields' => 'ids' ) );
$ta = $ta ? $ta[0] : wp_insert_post( array( 'post_type' => 'webmz_layout', 'post_status' => 'publish', 'post_title' => 'قالب آرشیو روانشناسان' ) );
update_post_meta( $ta, '_webmz_layout_type', 'archive_teacher' );
akd_set_elementor( $ta, "$O/teacher-archive.json" );
$o = get_option( 'webmz_options' ); $o['layout_archive_teacher'] = $ta; update_option( 'webmz_options', $o );

// Elementor kit colours (red -> teal)
$kit = (int) get_option( 'elementor_active_kit' );
$ks = get_post_meta( $kit, '_elementor_page_settings', true );
if ( is_array( $ks ) ) {
	$json = strtr( wp_json_encode( $ks ), array( '#D22022' => '#0D9488', '#d22022' => '#0d9488', '#AE1E1E' => '#0F766E', '#FFE5E5' => '#E0F5F1' ) );
	update_post_meta( $kit, '_elementor_page_settings', json_decode( $json, true ) );
}

// menus
$cats = array( 'mental-health', 'relationships', 'child-psychology', 'personal-growth' );
$c = fn( $s ) => array( 'title' => get_term_by( 'slug', $s, 'product_cat' )->name, 'term' => $map['cat'][ $s ], 'tax' => 'product_cat' );
$page = fn( $title, $id ) => array( 'title' => $title, 'page' => $id );
$courses = $page( 'دوره‌ها', 8 ); $courses['meta'] = array( '_webmz_mega_menu_enabled' => '1', '_webmz_mega_menu_content_id' => 1185 );
$psy = array( 'title' => 'روانشناسان', 'url' => home_url( '/mentors/' ) );
akd_menu( 17, array( $courses, $psy, $page( 'مقالات', 1148 ), $page( 'درباره ما', 37 ), $page( 'تماس با ما', 1309 ) ) );
$shop = $page( 'دوره‌ها', 8 ); $shop['children'] = array_map( $c, $cats );
akd_menu( 18, array( array( 'title' => 'صفحه اصلی', 'url' => home_url( '/' ) ), $shop, $psy, $page( 'مقالات', 1148 ), $page( 'درباره ما', 37 ), $page( 'تماس با ما', 1309 ), $page( 'حساب کاربری من', 11 ) ) );
require $ND . '/globals.php';
WP_CLI::success( 'demo 5 pages applied' );
