<?php
// wp eval-file palette.php <primary> <hover> <light>
[ $pri, $hover, $light ] = $args;
$k = (int) get_option( 'elementor_active_kit' );
$s = get_post_meta( $k, '_elementor_page_settings', true );
foreach ( $s['system_colors'] as &$c ) {
	if ( in_array( $c['_id'], array( 'primary', 'secondary' ), true ) ) $c['color'] = strtoupper( $pri );
	if ( 'accent' === $c['_id'] ) $c['color'] = strtoupper( $hover );
}
unset( $c );
foreach ( $s['custom_colors'] as &$c ) if ( '4f3df54' === $c['_id'] ) $c['color'] = strtoupper( $light );
unset( $c );
update_post_meta( $k, '_elementor_page_settings', $s );
$o = get_option( 'webmz_options' );
$o['color_secondary'] = $pri; $o['color_primary'] = $pri; $o['color_primary_hover'] = $hover; $o['color_primary_light'] = $light; $o['account_accent_light'] = $light;
update_option( 'webmz_options', $o );
echo "palette set: $pri\n";
