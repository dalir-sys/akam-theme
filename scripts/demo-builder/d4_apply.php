<?php
$ND = rtrim( $args[0], '/' );
require $ND . '/lib/common.php';
$O = "$ND/d4out";
$map = akd_json( "$ND/d4_map.json" );
foreach ( array( 632 => 'single-post', 103 => 'home', 15 => 'header', 565 => 'footer', 1185 => 'mega', 976 => 'single-product', 37 => 'about', 1309 => 'contact' ) as $id => $f ) akd_set_elementor( $id, "$O/$f.json" );
wp_update_post( array( 'ID' => 103, 'post_title' => 'صفحه اصلی آکام فایل' ) );
wp_update_post( array( 'ID' => 976, 'post_title' => 'قالب سینگل محصولات فایل' ) );
foreach ( get_posts( array( 'post_type' => 'page', 'name' => 'zhtest', 'numberposts' => -1, 'post_status' => 'any' ) ) as $p ) wp_delete_post( $p->ID, true );

// product -> seller profile (teacher post) for the instructor box
$devs = $map['teacher'];
$vendor_dev = array( 'استودیو نوا' => 0, 'گروه طراحی نگار' => 1, 'تیم پارس‌کد' => 2, 'نشر دیجیتال آکام' => 3, 'تایپ‌استودیو سپهر' => 4 );
foreach ( akd_json( "$ND/d4_products.json" ) as $p ) update_post_meta( $map['product'][ $p['slug'] ], '_webmz_course_teacher', $devs[ $vendor_dev[ $p['vendor'] ] ] );

// menus
$c = fn( $s ) => array( 'title' => get_term_by( 'slug', $s, 'product_cat' )->name, 'term' => $map['cat'][ $s ], 'tax' => 'product_cat' );
$cats = array( 'wordpress-themes', 'wordpress-plugins', 'graphic-files', 'ebooks', 'persian-fonts' );
$page = fn( $title, $id ) => array( 'title' => $title, 'page' => $id );
$sellers = array( 'title' => 'فروشندگان', 'url' => home_url( '/mentors/' ) );
akd_menu( 27, array( $page( 'فروشگاه', 8 ), $page( 'بلاگ', 1148 ), $sellers, $page( 'درباره ما', 37 ), $page( 'تماس با ما', 1309 ) ) );
$icons = array( 'menu-themes.svg', 'menu-plugins.svg', 'menu-graphics.svg', 'menu-ebooks.svg', 'menu-fonts.svg' );
$items = array();
foreach ( $cats as $k => $s ) {
	$it = $c( $s );
	$it['meta'] = array( '_webmz_menu_icon_id' => $map['img'][ $icons[ $k ] ][0] );
	if ( 0 === $k ) $it['meta'] += array( '_webmz_mega_menu_enabled' => '1', '_webmz_mega_menu_content_id' => 1185 );
	$items[] = $it;
}
akd_menu( 28, $items );
$shop = $page( 'فروشگاه', 8 ); $shop['children'] = array_map( $c, $cats );
akd_menu( 17, array( $shop, $page( 'بلاگ', 1148 ), $sellers, $page( 'درباره ما', 37 ), $page( 'تماس با ما', 1309 ) ) );
$home = array( 'title' => 'صفحه اصلی', 'url' => home_url( '/' ) );
akd_menu( 18, array( $home, $shop, $page( 'بلاگ', 1148 ), $sellers, $page( 'درباره ما', 37 ), $page( 'تماس با ما', 1309 ), $page( 'حساب کاربری من', 11 ) ) );
$ta = get_posts( array( 'post_type' => 'webmz_layout', 'meta_key' => '_webmz_layout_type', 'meta_value' => 'archive_teacher', 'numberposts' => 1, 'fields' => 'ids' ) );
$ta = $ta ? $ta[0] : wp_insert_post( array( 'post_type' => 'webmz_layout', 'post_status' => 'publish', 'post_title' => 'قالب آرشیو فروشندگان' ) );
update_post_meta( $ta, '_webmz_layout_type', 'archive_teacher' );
akd_set_elementor( $ta, "$O/teacher-archive.json" );
$o = get_option( 'webmz_options' ); $o['layout_archive_teacher'] = $ta; update_option( 'webmz_options', $o );
require $ND . '/globals.php';
WP_CLI::success( 'pages and menus applied' );
