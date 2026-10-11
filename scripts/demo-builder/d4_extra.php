<?php
$ND = rtrim( $args[0], '/' );
require $ND . '/lib/common.php';
$map = akd_json( "$ND/d4_map.json" );
foreach ( array( 'logo-light.png', 'news-art.png', 'why-1.png', 'why-2.png', 'why-3.png', 'why-4.png' ) as $f ) { $id = akd_image( "$ND/d4img/$f" ); $map['img'][ $f ] = array( $id, wp_get_attachment_url( $id ) ); }
foreach ( array( '2026/06/image-1.png', '2026/06/image-2.png', '2026/07/image-1.png', '2026/07/image-2.png' ) as $rel ) {
	$p = WP_CONTENT_DIR . '/uploads/' . $rel;
	if ( file_exists( $p ) && ! isset( $map['img'][ 'badge-' . basename( $rel ) ] ) ) { $id = akd_image( $p ); $map['img'][ 'badge-' . basename( $rel ) ] = array( $id, wp_get_attachment_url( $id ) ); }
}
file_put_contents( "$ND/d4_map.json", wp_json_encode( $map, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
WP_CLI::success( 'extra images: ' . implode( ', ', array_keys( array_filter( $map['img'], fn( $k ) => str_starts_with( $k, 'badge' ) || str_starts_with( $k, 'why' ) || str_starts_with( $k, 'news' ) || str_starts_with( $k, 'logo-l' ), ARRAY_FILTER_USE_KEY ) ) ) );
