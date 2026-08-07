<?php
/**
 * Secure ticket attachment upload, validation, and delivery.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extensions that are always blocked regardless of admin settings.
 *
 * @return array<int,string>
 */
function webmz_ticket_attachment_blocked_extensions() {
	return array(
		'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'phps',
		'asp', 'aspx', 'jsp', 'cgi', 'pl', 'py', 'rb', 'sh', 'bash', 'zsh',
		'exe', 'dll', 'so', 'bat', 'cmd', 'com', 'msi', 'scr', 'vbs', 'vbe',
		'js', 'mjs', 'cjs', 'ts', 'jsx', 'tsx', 'html', 'htm', 'xhtml', 'shtml',
		'svg', 'xml', 'xsl', 'xslt', 'swf', 'jar', 'war', 'htaccess', 'htpasswd',
		'ini', 'env', 'sql', 'sqlite', 'db', 'bak', 'config', 'yml', 'yaml',
		'wasm', 'lnk', 'reg', 'ps1', 'psm1', 'gadget',
	);
}

/**
 * Dangerous byte patterns scanned inside uploaded files.
 *
 * @return array<int,string>
 */
function webmz_ticket_attachment_malware_signatures() {
	return array(
		'<?php',
		'<? ',
		'<%',
		'<script',
		'javascript:',
		'eval(',
		'base64_decode',
		'gzinflate',
		'str_rot13',
		'passthru',
		'shell_exec',
		'system(',
		'exec(',
		'proc_open',
		'popen(',
		'assert(',
		'create_function',
		'__halt_compiler',
		'chmod(',
		'file_put_contents',
		'fopen(',
		'include(',
		'require(',
		'$_GET',
		'$_POST',
		'$_REQUEST',
		'$_SERVER',
		'$_FILES',
	);
}

/**
 * MIME map for allowed extensions.
 *
 * @return array<string,array<int,string>>
 */
function webmz_ticket_attachment_mime_map() {
	return array(
		'jpg'  => array( 'image/jpeg' ),
		'jpeg' => array( 'image/jpeg' ),
		'png'  => array( 'image/png' ),
		'gif'  => array( 'image/gif' ),
		'webp' => array( 'image/webp' ),
		'pdf'  => array( 'application/pdf', 'application/x-pdf', 'application/acrobat', 'applications/vnd.pdf' ),
	);
}

/**
 * Get allowed extensions from ticket settings.
 *
 * @return array<int,string>
 */
function webmz_ticket_attachment_get_allowed_extensions() {
	$settings = webmz_ticket_get_settings();
	$raw      = isset( $settings['attachments_extensions'] ) ? (string) $settings['attachments_extensions'] : 'jpg,jpeg,png,gif,webp,pdf';
	$parts    = preg_split( '/[\s,،;|]+/u', strtolower( $raw ) );
	$allowed  = array();

	foreach ( (array) $parts as $part ) {
		$part = strtolower( preg_replace( '/[^a-z0-9]/', '', sanitize_key( $part ) ) );
		if ( '' === $part || in_array( $part, webmz_ticket_attachment_blocked_extensions(), true ) ) {
			continue;
		}
		if ( ! isset( webmz_ticket_attachment_mime_map()[ $part ] ) ) {
			continue;
		}
		$allowed[] = $part;
	}

	if ( empty( $allowed ) ) {
		$allowed = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf' );
	}

	return array_values( array_unique( $allowed ) );
}

/**
 * Check if attachments are enabled.
 *
 * @return bool
 */
function webmz_ticket_attachments_enabled() {
	$settings = webmz_ticket_get_settings();

	return 'yes' === ( $settings['attachments_enabled'] ?? 'yes' );
}

/**
 * Max files per message.
 *
 * @return int
 */
function webmz_ticket_attachment_get_max_files() {
	$settings = webmz_ticket_get_settings();
	$max      = isset( $settings['attachments_max_files'] ) ? absint( $settings['attachments_max_files'] ) : 3;

	return max( 1, min( 10, $max ) );
}

/**
 * Max file size in bytes.
 *
 * @return int
 */
function webmz_ticket_attachment_get_max_size_bytes() {
	$settings = webmz_ticket_get_settings();
	$kb       = isset( $settings['attachments_max_size'] ) ? absint( $settings['attachments_max_size'] ) : 2048;

	return max( 100, min( 10240, $kb ) ) * 1024;
}

/**
 * Base upload directory for ticket attachments.
 *
 * @return array{path:string,url:string}
 */
function webmz_ticket_attachment_upload_dir() {
	$upload = wp_upload_dir();

	return array(
		'path' => trailingslashit( $upload['basedir'] ) . 'webmz-tickets',
		'url'  => trailingslashit( $upload['baseurl'] ) . 'webmz-tickets',
	);
}

/**
 * Ensure upload directory exists and is protected.
 *
 * @return void
 */
function webmz_ticket_attachment_ensure_protection() {
	$dir = webmz_ticket_attachment_upload_dir()['path'];

	if ( ! wp_mkdir_p( $dir ) ) {
		return;
	}

	$index = trailingslashit( $dir ) . 'index.php';
	if ( ! file_exists( $index ) ) {
		file_put_contents( $index, "<?php\n// Silence is golden.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	}

	$htaccess = trailingslashit( $dir ) . '.htaccess';
	if ( ! file_exists( $htaccess ) ) {
		$rules = "# WebMZ Ticket Attachments Security\n";
		$rules .= "<FilesMatch \"\\.(php|php3|php4|php5|php7|php8|phtml|phar|phps|asp|aspx|jsp|cgi|pl|py|sh|exe|bat|cmd|js|html|htm|svg)$\">\n";
		$rules .= "    Require all denied\n";
		$rules .= "</FilesMatch>\n";
		$rules .= "Options -Indexes\n";
		$rules .= "<IfModule mod_php.c>\n";
		$rules .= "    php_flag engine off\n";
		$rules .= "</IfModule>\n";
		file_put_contents( $htaccess, $rules ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	}
}
add_action( 'init', 'webmz_ticket_attachment_ensure_protection', 5 );

/**
 * Extract extension from filename safely.
 *
 * @param string $filename Filename.
 * @return string
 */
function webmz_ticket_attachment_get_extension( $filename ) {
	$filename = wp_basename( (string) $filename );
	$filename = str_replace( "\0", '', $filename );

	if ( preg_match( '/\.([a-z0-9]{1,8})$/i', $filename, $matches ) ) {
		return strtolower( $matches[1] );
	}

	return '';
}

/**
 * Detect all extensions in a filename (anti double-extension).
 *
 * @param string $filename Filename.
 * @return array<int,string>
 */
function webmz_ticket_attachment_get_all_extensions( $filename ) {
	$filename = wp_basename( (string) $filename );
	$parts    = explode( '.', $filename );
	array_shift( $parts );

	return array_values(
		array_filter(
			array_map(
				static function( $part ) {
					return strtolower( preg_replace( '/[^a-z0-9]/', '', $part ) );
				},
				$parts
			)
		)
	);
}

/**
 * Scan file content for malicious patterns.
 *
 * @param string $filepath File path.
 * @return true|WP_Error
 */
function webmz_ticket_attachment_scan_content( $filepath ) {
	if ( ! is_readable( $filepath ) ) {
		return new WP_Error( 'unreadable', esc_html__( 'فایل قابل خواندن نیست.', 'tadris' ) );
	}

	$max_scan = 1024 * 512;
	$handle   = fopen( $filepath, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen

	if ( ! $handle ) {
		return new WP_Error( 'unreadable', esc_html__( 'فایل قابل خواندن نیست.', 'tadris' ) );
	}

	$sample = fread( $handle, $max_scan ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread
	fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

	if ( false === $sample ) {
		return new WP_Error( 'unreadable', esc_html__( 'فایل قابل خواندن نیست.', 'tadris' ) );
	}

	$lower = strtolower( $sample );

	foreach ( webmz_ticket_attachment_malware_signatures() as $signature ) {
		if ( false !== strpos( $lower, strtolower( $signature ) ) ) {
			return new WP_Error( 'malware_detected', esc_html__( 'فایل حاوی محتوای مشکوک است و پذیرفته نشد.', 'tadris' ) );
		}
	}

	if ( preg_match( '/<\?[\s=]/i', $sample ) ) {
		return new WP_Error( 'malware_detected', esc_html__( 'فایل حاوی محتوای مشکوک است و پذیرفته نشد.', 'tadris' ) );
	}

	return true;
}

/**
 * Validate MIME type using finfo.
 *
 * @param string $filepath File path.
 * @param string $extension Expected extension.
 * @return string|WP_Error
 */
function webmz_ticket_attachment_detect_mime( $filepath, $extension ) {
	$mime_map = webmz_ticket_attachment_mime_map();
	$allowed  = isset( $mime_map[ $extension ] ) ? $mime_map[ $extension ] : array();

	if ( empty( $allowed ) ) {
		return new WP_Error( 'invalid_extension', esc_html__( 'پسوند فایل مجاز نیست.', 'tadris' ) );
	}

	$detected = '';

	if ( function_exists( 'finfo_open' ) ) {
		$finfo = finfo_open( FILEINFO_MIME_TYPE ); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.finfo_openFound
		if ( $finfo ) {
			$detected = (string) finfo_file( $finfo, $filepath ); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.finfo_fileFound
			finfo_close( $finfo ); // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.finfo_closeFound
		}
	}

	if ( '' === $detected && function_exists( 'mime_content_type' ) ) {
		$detected = (string) mime_content_type( $filepath ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.mime_content_type_mime_content_type
	}

	$detected = strtolower( trim( $detected ) );

	if ( '' === $detected || ! in_array( $detected, $allowed, true ) ) {
		return new WP_Error( 'invalid_mime', esc_html__( 'نوع فایل با پسوند اعلام‌شده مطابقت ندارد.', 'tadris' ) );
	}

	return $detected;
}

/**
 * Validate image integrity.
 *
 * @param string $filepath File path.
 * @return true|WP_Error
 */
function webmz_ticket_attachment_validate_image( $filepath ) {
	$info = @getimagesize( $filepath ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

	if ( ! is_array( $info ) || empty( $info[0] ) || empty( $info[1] ) ) {
		return new WP_Error( 'invalid_image', esc_html__( 'فایل تصویر معتبر نیست.', 'tadris' ) );
	}

	if ( $info[0] > 8000 || $info[1] > 8000 ) {
		return new WP_Error( 'invalid_image', esc_html__( 'ابعاد تصویر بیش از حد مجاز است.', 'tadris' ) );
	}

	return true;
}

/**
 * Validate PDF header.
 *
 * @param string $filepath File path.
 * @return true|WP_Error
 */
function webmz_ticket_attachment_validate_pdf( $filepath ) {
	$handle = fopen( $filepath, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	if ( ! $handle ) {
		return new WP_Error( 'invalid_pdf', esc_html__( 'فایل PDF معتبر نیست.', 'tadris' ) );
	}

	$header = fread( $handle, 8 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread
	fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

	if ( 0 !== strpos( (string) $header, '%PDF-' ) ) {
		return new WP_Error( 'invalid_pdf', esc_html__( 'فایل PDF معتبر نیست.', 'tadris' ) );
	}

	return true;
}

/**
 * Validate one uploaded file array item.
 *
 * @param array<string,mixed> $file $_FILES item.
 * @return array<string,mixed>|WP_Error
 */
function webmz_ticket_attachment_validate_file( $file ) {
	if ( empty( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
		return new WP_Error( 'upload_error', esc_html__( 'فایل آپلود شده معتبر نیست.', 'tadris' ) );
	}

	if ( ! empty( $file['error'] ) && UPLOAD_ERR_OK !== (int) $file['error'] ) {
		return new WP_Error( 'upload_error', esc_html__( 'خطا در آپلود فایل.', 'tadris' ) );
	}

	$max_size = webmz_ticket_attachment_get_max_size_bytes();
	$size     = isset( $file['size'] ) ? absint( $file['size'] ) : 0;

	if ( $size <= 0 || $size > $max_size ) {
		return new WP_Error(
			'file_too_large',
			sprintf(
				/* translators: %s: max file size in KB */
				esc_html__( 'حجم هر فایل نباید بیشتر از %s کیلوبایت باشد.', 'tadris' ),
				number_format_i18n( (int) round( $max_size / 1024 ) )
			)
		);
	}

	$original_name = isset( $file['name'] ) ? (string) $file['name'] : '';
	if ( false !== strpos( $original_name, "\0" ) || false !== strpos( $original_name, '../' ) || false !== strpos( $original_name, '..\\' ) ) {
		return new WP_Error( 'invalid_filename', esc_html__( 'نام فایل نامعتبر است.', 'tadris' ) );
	}

	$all_extensions = webmz_ticket_attachment_get_all_extensions( $original_name );
	if ( empty( $all_extensions ) ) {
		return new WP_Error( 'invalid_extension', esc_html__( 'فایل باید دارای پسوند معتبر باشد.', 'tadris' ) );
	}

	foreach ( $all_extensions as $ext_part ) {
		if ( in_array( $ext_part, webmz_ticket_attachment_blocked_extensions(), true ) ) {
			return new WP_Error( 'blocked_extension', esc_html__( 'پسوند فایل مجاز نیست.', 'tadris' ) );
		}
	}

	$extension = end( $all_extensions );
	$allowed   = webmz_ticket_attachment_get_allowed_extensions();

	if ( ! in_array( $extension, $allowed, true ) ) {
		return new WP_Error( 'invalid_extension', esc_html__( 'پسوند فایل مجاز نیست.', 'tadris' ) );
	}

	$scan = webmz_ticket_attachment_scan_content( $file['tmp_name'] );
	if ( is_wp_error( $scan ) ) {
		return $scan;
	}

	$mime = webmz_ticket_attachment_detect_mime( $file['tmp_name'], $extension );
	if ( is_wp_error( $mime ) ) {
		return $mime;
	}

	if ( in_array( $extension, array( 'jpg', 'jpeg', 'png', 'gif', 'webp' ), true ) ) {
		$image_check = webmz_ticket_attachment_validate_image( $file['tmp_name'] );
		if ( is_wp_error( $image_check ) ) {
			return $image_check;
		}
	}

	if ( 'pdf' === $extension ) {
		$pdf_check = webmz_ticket_attachment_validate_pdf( $file['tmp_name'] );
		if ( is_wp_error( $pdf_check ) ) {
			return $pdf_check;
		}
	}

	$safe_original = sanitize_file_name( wp_basename( $original_name ) );
	if ( '' === $safe_original ) {
		$safe_original = 'file.' . $extension;
	}

	return array(
		'tmp_name'      => $file['tmp_name'],
		'size'          => $size,
		'extension'     => $extension,
		'mime'          => $mime,
		'original_name' => $safe_original,
	);
}

/**
 * Normalize $_FILES attachments array.
 *
 * @param array<string,mixed> $files Raw files array.
 * @return array<int,array<string,mixed>>
 */
function webmz_ticket_attachment_normalize_files_array( $files ) {
	$normalized = array();

	if ( empty( $files ) || ! is_array( $files ) ) {
		return $normalized;
	}

	if ( isset( $files['name'] ) && is_array( $files['name'] ) ) {
		$count = count( $files['name'] );
		for ( $i = 0; $i < $count; $i++ ) {
			if ( empty( $files['name'][ $i ] ) ) {
				continue;
			}
			$normalized[] = array(
				'name'     => $files['name'][ $i ],
				'type'     => $files['type'][ $i ] ?? '',
				'tmp_name' => $files['tmp_name'][ $i ] ?? '',
				'error'    => $files['error'][ $i ] ?? UPLOAD_ERR_NO_FILE,
				'size'     => $files['size'][ $i ] ?? 0,
			);
		}
	} elseif ( isset( $files['name'] ) ) {
		$normalized[] = $files;
	}

	return $normalized;
}

/**
 * Process and store validated attachments for a ticket message.
 *
 * @param int   $ticket_id Ticket ID.
 * @param int   $user_id User ID.
 * @param array $files $_FILES['attachments'] structure.
 * @return array<int,array<string,mixed>>|WP_Error
 */
function webmz_ticket_attachment_process_uploads( $ticket_id, $user_id, $files ) {
	if ( ! webmz_ticket_attachments_enabled() ) {
		return array();
	}

	$ticket_id = absint( $ticket_id );
	$user_id   = absint( $user_id );

	if ( ! $ticket_id || ! $user_id ) {
		return new WP_Error( 'invalid_context', esc_html__( 'اطلاعات تیکت نامعتبر است.', 'tadris' ) );
	}

	$normalized = webmz_ticket_attachment_normalize_files_array( $files );
	$max_files  = webmz_ticket_attachment_get_max_files();

	if ( count( $normalized ) > $max_files ) {
		return new WP_Error(
			'too_many_files',
			sprintf(
				/* translators: %d: max files */
				esc_html__( 'حداکثر %d فایل می‌توانید پیوست کنید.', 'tadris' ),
				$max_files
			)
		);
	}

	if ( empty( $normalized ) ) {
		return array();
	}

	webmz_ticket_attachment_ensure_protection();

	$base_dir = trailingslashit( webmz_ticket_attachment_upload_dir()['path'] ) . $ticket_id;
	if ( ! wp_mkdir_p( $base_dir ) ) {
		return new WP_Error( 'storage_error', esc_html__( 'امکان ذخیره فایل وجود ندارد.', 'tadris' ) );
	}

	$stored      = array();
	$stored_paths = array();

	foreach ( $normalized as $file ) {
		$validated = webmz_ticket_attachment_validate_file( $file );
		if ( is_wp_error( $validated ) ) {
			webmz_ticket_attachment_cleanup_files( $stored_paths );
			return $validated;
		}

		$attachment_id = wp_generate_uuid4();
		$stored_name   = $attachment_id . '.' . $validated['extension'];
		$destination   = trailingslashit( $base_dir ) . $stored_name;

		if ( ! @move_uploaded_file( $validated['tmp_name'], $destination ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			webmz_ticket_attachment_cleanup_files( $stored_paths );
			return new WP_Error( 'storage_error', esc_html__( 'امکان ذخیره فایل وجود ندارد.', 'tadris' ) );
		}

		@chmod( $destination, 0644 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_chmod

		$stored_paths[] = $destination;
		$stored[]     = array(
			'id'            => $attachment_id,
			'filename'      => $validated['original_name'],
			'stored_name'   => $stored_name,
			'mime'          => $validated['mime'],
			'size'          => $validated['size'],
			'ext'           => $validated['extension'],
			'hash'          => hash_file( 'sha256', $destination ),
			'uploaded_by'   => $user_id,
			'uploaded_at'   => current_time( 'mysql' ),
		);
	}

	return $stored;
}

/**
 * Delete physical attachment files.
 *
 * @param array<int,string> $paths File paths.
 * @return void
 */
function webmz_ticket_attachment_cleanup_files( $paths ) {
	foreach ( (array) $paths as $path ) {
		if ( is_string( $path ) && file_exists( $path ) ) {
			wp_delete_file( $path );
		}
	}
}

/**
 * Build secure download URL for an attachment.
 *
 * @param int    $ticket_id Ticket ID.
 * @param string $attachment_id Attachment UUID.
 * @return string
 */
function webmz_ticket_attachment_get_download_url( $ticket_id, $attachment_id, $inline = false ) {
	return add_query_arg(
		array(
			'action'        => 'webmz_ticket_download_attachment',
			'ticket_id'     => absint( $ticket_id ),
			'attachment_id' => sanitize_text_field( $attachment_id ),
			'nonce'         => wp_create_nonce( 'webmz_ticket_attachment_' . absint( $ticket_id ) . '_' . sanitize_text_field( $attachment_id ) ),
			'inline'        => $inline ? '1' : '0',
		),
		admin_url( 'admin-ajax.php' )
	);
}

/**
 * Find attachment metadata in ticket messages.
 *
 * @param int    $ticket_id Ticket ID.
 * @param string $attachment_id Attachment UUID.
 * @return array<string,mixed>|null
 */
function webmz_ticket_attachment_find( $ticket_id, $attachment_id ) {
	$messages = webmz_ticket_get_messages( $ticket_id );

	foreach ( $messages as $message ) {
		if ( empty( $message['attachments'] ) || ! is_array( $message['attachments'] ) ) {
			continue;
		}

		foreach ( $message['attachments'] as $attachment ) {
			if ( isset( $attachment['id'] ) && $attachment['id'] === $attachment_id ) {
				return $attachment;
			}
		}
	}

	return null;
}

/**
 * Get absolute path for stored attachment.
 *
 * @param int                   $ticket_id Ticket ID.
 * @param array<string,mixed>   $attachment Attachment data.
 * @return string
 */
function webmz_ticket_attachment_get_path( $ticket_id, $attachment ) {
	$stored_name = isset( $attachment['stored_name'] ) ? sanitize_file_name( $attachment['stored_name'] ) : '';

	return trailingslashit( webmz_ticket_attachment_upload_dir()['path'] ) . absint( $ticket_id ) . '/' . $stored_name;
}

/**
 * Check if user can download attachment.
 *
 * @param int $ticket_id Ticket ID.
 * @param int $user_id User ID.
 * @return bool
 */
function webmz_ticket_attachment_user_can_download( $ticket_id, $user_id = 0 ) {
	return webmz_ticket_user_can_view( $ticket_id, $user_id );
}

/**
 * Render attachments HTML block.
 *
 * @param array<int,array<string,mixed>> $attachments Attachments.
 * @param int                            $ticket_id Ticket ID.
 * @return string
 */
function webmz_ticket_render_attachments_html( $attachments, $ticket_id ) {
	$attachments = is_array( $attachments ) ? $attachments : array();
	$ticket_id   = absint( $ticket_id );

	if ( empty( $attachments ) || ! $ticket_id ) {
		return '';
	}

	$image_exts = array( 'jpg', 'jpeg', 'png', 'gif', 'webp' );

	ob_start();
	?>
	<div class="webmz-ticket-attachments">
		<strong class="webmz-ticket-attachments__title"><?php esc_html_e( 'پیوست‌ها', 'tadris' ); ?></strong>
		<ul class="webmz-ticket-attachments__list">
			<?php foreach ( $attachments as $attachment ) : ?>
				<?php
				$attachment_id = isset( $attachment['id'] ) ? sanitize_text_field( $attachment['id'] ) : '';
				$filename      = isset( $attachment['filename'] ) ? sanitize_text_field( $attachment['filename'] ) : '';
				$ext           = isset( $attachment['ext'] ) ? sanitize_key( $attachment['ext'] ) : '';
				$size          = isset( $attachment['size'] ) ? absint( $attachment['size'] ) : 0;
				$url           = $attachment_id ? webmz_ticket_attachment_get_download_url( $ticket_id, $attachment_id ) : '';
				$preview_url   = ( $attachment_id && in_array( $ext, $image_exts, true ) )
					? webmz_ticket_attachment_get_download_url( $ticket_id, $attachment_id, true )
					: '';
				$is_image      = in_array( $ext, $image_exts, true );
				?>
				<li class="webmz-ticket-attachments__item <?php echo $is_image ? 'is-image' : 'is-file'; ?>">
					<a class="webmz-ticket-attachments__link" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
						<?php if ( $is_image ) : ?>
							<span class="webmz-ticket-attachments__thumb webmz-ticket-attachments__thumb--image" aria-hidden="true">
								<img src="<?php echo esc_url( $preview_url ); ?>" alt="" loading="lazy" width="34" height="34">
							</span>
						<?php else : ?>
							<span class="webmz-ticket-attachments__thumb" aria-hidden="true">
								<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm1 7V3.5L18.5 9H15ZM8 13h8v2H8v-2Zm0 4h5v2H8v-2Z"/></svg>
							</span>
						<?php endif; ?>
						<span class="webmz-ticket-attachments__meta">
							<span class="webmz-ticket-attachments__name"><?php echo esc_html( $filename ); ?></span>
							<?php if ( $size ) : ?>
								<span class="webmz-ticket-attachments__size"><?php echo esc_html( size_format( $size ) ); ?></span>
							<?php endif; ?>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php

	return (string) ob_get_clean();
}

/**
 * AJAX secure attachment download.
 *
 * @return void
 */
function webmz_ticket_ajax_download_attachment() {
	$ticket_id     = isset( $_GET['ticket_id'] ) ? absint( $_GET['ticket_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$attachment_id = isset( $_GET['attachment_id'] ) ? sanitize_text_field( wp_unslash( $_GET['attachment_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$nonce         = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! $ticket_id || '' === $attachment_id || ! wp_verify_nonce( $nonce, 'webmz_ticket_attachment_' . $ticket_id . '_' . $attachment_id ) ) {
		wp_die( esc_html__( 'دسترسی به فایل مجاز نیست.', 'tadris' ), 403 );
	}

	if ( ! is_user_logged_in() || ! webmz_ticket_attachment_user_can_download( $ticket_id ) ) {
		wp_die( esc_html__( 'دسترسی به فایل مجاز نیست.', 'tadris' ), 403 );
	}

	$attachment = webmz_ticket_attachment_find( $ticket_id, $attachment_id );
	if ( ! $attachment ) {
		wp_die( esc_html__( 'فایل پیدا نشد.', 'tadris' ), 404 );
	}

	$path = webmz_ticket_attachment_get_path( $ticket_id, $attachment );
	if ( ! file_exists( $path ) || ! is_readable( $path ) ) {
		wp_die( esc_html__( 'فایل پیدا نشد.', 'tadris' ), 404 );
	}

	$stored_hash = isset( $attachment['hash'] ) ? (string) $attachment['hash'] : '';
	if ( $stored_hash && ! hash_equals( $stored_hash, hash_file( 'sha256', $path ) ) ) {
		wp_die( esc_html__( 'فایل دستکاری شده و قابل دانلود نیست.', 'tadris' ), 403 );
	}

	$filename   = isset( $attachment['filename'] ) ? sanitize_file_name( $attachment['filename'] ) : 'attachment';
	$mime       = isset( $attachment['mime'] ) ? sanitize_mime_type( $attachment['mime'] ) : 'application/octet-stream';
	$inline     = isset( $_GET['inline'] ) && '1' === (string) $_GET['inline']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$image_exts = array( 'jpg', 'jpeg', 'png', 'gif', 'webp' );
	$ext        = isset( $attachment['ext'] ) ? sanitize_key( $attachment['ext'] ) : '';
	$disposition = ( $inline && in_array( $ext, $image_exts, true ) ) ? 'inline' : 'attachment';

	nocache_headers();
	header( 'Content-Type: ' . $mime );
	header( 'Content-Disposition: ' . $disposition . '; filename="' . rawurlencode( $filename ) . '"' );
	header( 'Content-Length: ' . filesize( $path ) );
	header( 'X-Content-Type-Options: nosniff' );

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
	readfile( $path );
	exit;
}
add_action( 'wp_ajax_webmz_ticket_download_attachment', 'webmz_ticket_ajax_download_attachment' );

/**
 * Upload config for frontend scripts.
 *
 * @return array<string,mixed>
 */
function webmz_ticket_attachment_get_frontend_config() {
	$extensions = webmz_ticket_attachment_get_allowed_extensions();
	$accept     = array();

	foreach ( $extensions as $ext ) {
		$accept[] = '.' . $ext;
	}

	return array(
		'enabled'    => webmz_ticket_attachments_enabled(),
		'maxFiles'   => webmz_ticket_attachment_get_max_files(),
		'maxSize'    => webmz_ticket_attachment_get_max_size_bytes(),
		'maxSizeKb'  => (int) round( webmz_ticket_attachment_get_max_size_bytes() / 1024 ),
		'extensions' => $extensions,
		'accept'     => implode( ',', $accept ),
		'labels'     => array(
			'dropzone'    => esc_html__( 'فایل‌ها را اینجا رها کنید یا کلیک کنید', 'tadris' ),
			'hint'        => sprintf(
				/* translators: 1: max files, 2: max size KB, 3: extensions */
				esc_html__( 'حداکثر %1$d فایل — هر فایل تا %2$s کیلوبایت — فرمت‌های مجاز: %3$s', 'tadris' ),
				webmz_ticket_attachment_get_max_files(),
				number_format_i18n( (int) round( webmz_ticket_attachment_get_max_size_bytes() / 1024 ) ),
				implode( ', ', $extensions )
			),
			'tooMany'     => sprintf(
				/* translators: %d: max files */
				esc_html__( 'حداکثر %d فایل می‌توانید انتخاب کنید.', 'tadris' ),
				webmz_ticket_attachment_get_max_files()
			),
			'tooLarge'    => sprintf(
				/* translators: %s: max size KB */
				esc_html__( 'حجم فایل نباید بیشتر از %s کیلوبایت باشد.', 'tadris' ),
				number_format_i18n( (int) round( webmz_ticket_attachment_get_max_size_bytes() / 1024 ) )
			),
			'invalidType' => esc_html__( 'فرمت فایل مجاز نیست.', 'tadris' ),
			'remove'      => esc_html__( 'حذف', 'tadris' ),
		),
	);
}

/**
 * Render optional attachment upload field for ticket forms.
 *
 * @return void
 */
function webmz_ticket_render_attachment_upload_field() {
	if ( ! webmz_ticket_attachments_enabled() ) {
		return;
	}

	$upload_cfg = webmz_ticket_attachment_get_frontend_config();
	?>
	<div class="webmz-ticket-upload" data-webmz-ticket-upload>
		<span class="webmz-ticket-upload__label"><?php esc_html_e( 'پیوست فایل (اختیاری)', 'tadris' ); ?></span>
		<div class="webmz-ticket-upload__dropzone" data-webmz-ticket-dropzone tabindex="0" role="button" aria-label="<?php esc_attr_e( 'انتخاب یا رها کردن فایل', 'tadris' ); ?>">
			<span class="webmz-ticket-upload__dropzone-icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor"><path d="M19.35 10.04A7.49 7.49 0 0 0 12 4C9.11 4 6.6 5.64 5.35 8.04A5.994 5.994 0 0 0 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96ZM14 13v4h-4v-4H7l5-5 5 5h-3Z"/></svg>
			</span>
			<span class="webmz-ticket-upload__dropzone-text" data-webmz-ticket-dropzone-text"><?php echo esc_html( $upload_cfg['labels']['dropzone'] ?? '' ); ?></span>
			<span class="webmz-ticket-upload__hint"><?php echo esc_html( $upload_cfg['labels']['hint'] ?? '' ); ?></span>
		</div>
		<input type="file" name="attachments[]" data-webmz-ticket-file-input multiple accept="<?php echo esc_attr( $upload_cfg['accept'] ?? '' ); ?>" hidden>
		<ul class="webmz-ticket-upload__list" data-webmz-ticket-upload-list></ul>
	</div>
	<?php
}
