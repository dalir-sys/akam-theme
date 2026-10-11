<?php
$names = array_slice( $args, 0 );
$wm = \Elementor\Plugin::instance()->widgets_manager;
foreach ( $names as $n ) {
	$w = $wm->get_widget_types( $n );
	if ( ! $w ) { echo "!! $n missing\n"; continue; }
	echo "=== $n (" . $w->get_title() . ")\n";
	foreach ( $w->get_controls() as $k => $c ) {
		if ( in_array( $c['type'], array( 'section', 'tab', 'tabs', 'heading', 'color', 'slider', 'dimensions', 'choose', 'divider', 'popover_toggle' ), true ) || $k[0] === '_' || ( isset( $c['tab'] ) && $c['tab'] === 'style' ) || preg_match( '/typography|border|shadow|background|_color|padding|margin|css_filters|text_stroke/', $k ) ) continue;
		$d = isset( $c['default'] ) ? $c['default'] : '';
		if ( is_array( $d ) ) {
			if ( $c['type'] === 'repeater' ) {
				$fields = array_keys( $c['fields'] );
				$d = 'repeater(' . count( $d ) . ') fields=' . implode( ',', array_filter( $fields, fn( $f ) => $f[0] !== '_' ) ) . ' first=' . json_encode( $d[0] ?? null, JSON_UNESCAPED_UNICODE );
			} else { $d = json_encode( $d, JSON_UNESCAPED_UNICODE ); }
		}
		$opts = isset( $c['options'] ) && is_array( $c['options'] ) && $c['type'] === 'select' ? ' opts=' . implode( '|', array_keys( $c['options'] ) ) : '';
		echo "  $k [{$c['type']}] " . ( $c['label'] ?? '' ) . " = " . mb_substr( (string) $d, 0, 260 ) . "$opts\n";
	}
}
