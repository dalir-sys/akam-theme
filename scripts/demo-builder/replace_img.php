<?php
// wp eval-file replace_img.php <dir> file1 file2 ... : overwrite attachments imported from these files and regenerate sizes
require_once ABSPATH . 'wp-admin/includes/image.php';
$dir = array_shift( $args );
foreach ( $args as $f ) {
	$ids = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'any', 'meta_key' => '_akd_src', 'meta_value' => 'akd:' . $f, 'fields' => 'ids' ) );
	foreach ( $ids as $id ) { $path = get_attached_file( $id ); copy( "$dir/$f", $path ); wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $path ) ); echo "replaced $f ($id)\n"; }
}
