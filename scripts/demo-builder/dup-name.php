<?php
// wp --require=dup-name.php duplicator build ... : names the Duplicator package after AKAM_DUP_NAME (e.g. demo_4).
WP_CLI::add_wp_hook( 'duplicator_name_format_result', function ( $name ) { return getenv( 'AKAM_DUP_NAME' ) ?: $name; } );
