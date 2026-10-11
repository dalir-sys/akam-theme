<?php
$r = wp_update_custom_css_post( file_get_contents( $args[0] ), array( 'stylesheet' => get_stylesheet() ) );
echo is_wp_error( $r ) ? $r->get_error_message() : 'custom css id ' . $r->ID . ' for ' . get_stylesheet(), "\n";
