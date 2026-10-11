<?php
// Point theme widget colours that default to #0878f9 at the Elementor global primary colour.
$map = array(
	'webmz-post-content' => array( 'tag_color' ), 'webmz-post-breadcrumb' => array( 'separator_accent_color' ),
	'webmz-post-primary-category' => array( 'category_color', 'icon_color' ), 'webmz-post-toc' => array( 'title_color', 'link_hover_color' ),
	'webmz-post-download-box' => array( 'header_icon_bg', 'button_bg_color' ),
);
$walk = function ( &$els ) use ( &$walk, $map, &$n ) {
	foreach ( $els as &$e ) {
		$t = $e['widgetType'] ?? '';
		if ( isset( $map[ $t ] ) ) {
			if ( ! is_array( $e['settings'] ) ) $e['settings'] = array();
			foreach ( $map[ $t ] as $k ) { if ( empty( $e['settings'][ $k ] ) ) { $e['settings']['__globals__'][ $k ] = 'globals/colors?id=primary'; $n++; } }
		}
		if ( ! empty( $e['elements'] ) ) $walk( $e['elements'] );
	}
};
$n = 0;
global $wpdb;
foreach ( $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_elementor_data'" ) as $pid ) {
	$data = json_decode( get_post_meta( $pid, '_elementor_data', true ), true );
	if ( ! is_array( $data ) ) continue;
	$before = $n; $walk( $data );
	if ( $n > $before ) { update_post_meta( $pid, '_elementor_data', wp_slash( wp_json_encode( $data, JSON_UNESCAPED_UNICODE ) ) ); delete_post_meta( $pid, '_elementor_css' ); }
}
echo "linked $n colour settings to the global primary\n";
