<?php
// wp eval-file d4_content.php <nd_dir>
$ND = rtrim( $args[0], '/' );
require $ND . '/lib/common.php';
$IMG = $ND . '/d4img';
$P = akd_json( "$ND/d4_products.json" );
$POSTS = akd_json( "$ND/d4_posts.json" );
$CATS = akd_json( "$ND/d4_cats.json" );
$map = array( 'img' => array(), 'cat' => array(), 'product' => array(), 'user' => array(), 'post' => array() );
$img = function ( $f, $t = '' ) use ( $IMG, &$map ) { $id = akd_image( "$IMG/$f", $t ); $map['img'][ $f ] = array( $id, wp_get_attachment_url( $id ) ); return $id; };

// ---------- site options ----------
update_option( 'blogname', 'آکام فایل' );
update_option( 'blogdescription', 'مرجع خرید فایل‌های دیجیتال فارسی' );
$o = get_option( 'webmz_options', array() );
$o = array_merge( $o, array(
	'color_primary' => '#3b5bfd', 'color_secondary' => '#3b5bfd', 'color_primary_hover' => '#2f49e0', 'color_primary_light' => '#eaf0ff',
	'color_text_dark' => '#0f172a', 'color_text_gray' => '#64748b', 'color_text_navy' => '#0b1b4d', 'color_background' => '#f5f7fb',
	'font_family' => 'dana', 'popular_searches' => "قالب فروشگاهی\nافزونه پیامک\nفونت فارسی\nیو آی کیت\nسئو",
	'account_accent_light' => '#eaf0ff', 'related_box_accent' => '#3b5bfd', 'related_box_bg' => '#eef2ff',
	'floating_contact_button_color' => '#3b5bfd', 'floating_contact_button_hover_color' => '#2f49e0',
) );
$o['favicon_id'] = $img( 'favicon.png', 'آیکون آکام فایل' );
update_option( 'webmz_options', $o );

// ---------- images ----------
foreach ( glob( "$IMG/*.{png,jpg}", GLOB_BRACE ) as $f ) {
	if ( preg_match( '/^cover-.*\\.png$/', basename( $f ) ) ) continue;
	$b = basename( $f );
	if ( $b[0] === '_' ) continue;
	$img( $b );
}
// menu icons (svg)
foreach ( glob( "$IMG/menu-*.svg" ) as $f ) $img( basename( $f ) );

// testimonial avatars from the base demo
foreach ( array( 'img-1.jpg', 'img-2.jpg', 'img-3.jpg', 'img-4.jpg' ) as $a ) { $id = akd_image( WP_CONTENT_DIR . '/uploads/2026/06/' . $a ); $map['img'][ $a ] = array( $id, wp_get_attachment_url( $id ) ); }

// ---------- vendors ----------
$vendors = array(
	'استودیو نوا' => array( 'nava-studio', 'thumb-nava-shop-theme.png' ),
	'تیم پارس‌کد' => array( 'parscode', 'thumb-formino-form-builder.png' ),
	'گروه طراحی نگار' => array( 'negar-design', 'thumb-sabadak-ui-kit.png' ),
	'نشر دیجیتال آکام' => array( 'akam-publishing', 'thumb-online-store-ebook.png' ),
	'تایپ‌استودیو سپهر' => array( 'sepehr-type', 'thumb-sepehr-font.png' ),
);
foreach ( $vendors as $name => $v ) {
	$uid = akd_user( $v[0], $name, $v[0] . '@akamfile.test' );
	update_user_meta( $uid, 'description', "{$name}؛ فروشنده برتر آکام فایل" );
	$map['user'][ $name ] = $uid;
}

// ---------- product categories ----------
foreach ( $CATS as $c ) {
	$tid = akd_term( 'product_cat', $c[1], $c[0], array( 'description' => 'جدیدترین و پرفروش‌ترین ' . $c[1] . '‌ها' ) );
	update_term_meta( $tid, 'thumbnail_id', $img( $c[2] . '.png' ) );
	$map['cat'][ $c[0] ] = $tid;
}
foreach ( get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ) as $t ) {
	if ( ! isset( $map['cat'][ $t->slug ] ) && 'uncategorized' !== $t->slug && get_option( 'default_product_cat' ) != $t->term_id ) wp_delete_term( $t->term_id, 'product_cat' );
}

// ---------- products ----------
akd_delete_all( 'product_variation' );
akd_delete_all( 'product' );
$names = array( 'سارا محمدی', 'امیر رضایی', 'مهسا کریمی', 'علی حسینی', 'نازنین احمدی', 'رضا مرادی', 'الهام قاسمی', 'حمید نوری', 'فاطمه رحیمی', 'کاوه شریفی' );
$texts = array(
	5 => array( 'کیفیت فایل عالی بود و نصبش هم خیلی راحت انجام شد. ممنون از پشتیبانی سریع.', 'دقیقاً همون چیزی بود که دنبالش بودم؛ مستنداتش هم کامل و فارسیه.', 'ارزش خرید داره. چند بار آپدیت رایگان هم گرفتم.', 'پشتیبانی کمتر از یک ساعت جواب داد و مشکلم حل شد.' ),
	4 => array( 'در کل خوبه، فقط کاش آموزش ویدیویی بیشتری داشت.', 'محصول باکیفیتیه؛ با یه کم شخصی‌سازی کاملاً مناسب کارم شد.' ),
);
$i = 0;
foreach ( $P as $p ) {
	$i++;
	$cover = $img( 'cover-' . $p['slug'] . '.jpg', $p['title'] );
	$thumb = $img( 'thumb-' . $p['slug'] . '.png', $p['title'] . ' - آیکون' );
	$product = new WC_Product_Simple();
	$product->set_name( $p['title'] );
	$product->set_slug( $p['slug'] );
	$product->set_status( 'publish' );
	$product->set_description( $p['desc'] );
	$product->set_short_description( $p['short'] );
	$product->set_regular_price( (string) $p['price'] );
	if ( $p['sale'] ) {
		$product->set_sale_price( (string) $p['sale'] );
		$product->set_date_on_sale_to( gmdate( 'Y-m-d 23:59:59', time() + ( 2 + $i % 5 ) * DAY_IN_SECONDS ) );
	}
	$product->set_virtual( true );
	$product->set_downloadable( true );
	$dl = new WC_Product_Download();
	$dl->set_name( $p['file'][0] );
	$dl->set_file( akd_download_file( $p['file'][0], $p['title'] ) );
	$dl->set_id( md5( $p['slug'] ) );
	$product->set_downloads( array( $dl ) );
	$product->set_download_limit( -1 );
	$product->set_download_expiry( -1 );
	$product->set_category_ids( array( $map['cat'][ $p['cat'] ] ) );
	$product->set_image_id( $cover );
	$product->set_reviews_allowed( true );
	$pid = $product->save();
	wp_update_post( array( 'ID' => $pid, 'post_author' => $map['user'][ $p['vendor'] ], 'post_date' => gmdate( 'Y-m-d H:i:s', time() - ( 20 - $i ) * DAY_IN_SECONDS ), 'menu_order' => $i ) );
	// reviews
	$rv = array();
	for ( $r = 0; $r < 4; $r++ ) {
		$rating = ( $r === 3 && $p['rating'] < 4.85 ) ? 4 : 5;
		$pool = $texts[ $rating ];
		$rv[] = array( 'name' => $names[ ( $i + $r * 3 ) % count( $names ) ], 'rating' => $rating, 'text' => $pool[ ( $i + $r ) % count( $pool ) ] );
	}
	akd_reviews( $pid, $rv );
	$hl = preg_replace( '/^(.*?)(\S+)$/u', '$1[hl]$2[/hl]', $p['title'] );
	$meta = array(
		'total_sales' => $p['sales'], '_wc_average_rating' => number_format( $p['rating'], 2 ), '_wc_review_count' => $p['reviews'],
		'_wc_rating_count' => array( 5 => (int) round( $p['reviews'] * 0.86 ), 4 => $p['reviews'] - (int) round( $p['reviews'] * 0.86 ) ),
		'_webmz_product_loop_thumb' => $thumb, '_webmz_product_loop_badge' => $p['vendor'],
		'_webmz_product_preview_url' => $p['preview'], '_webmz_single_product_title' => $hl,
		'_webmz_single_short_description' => $p['short'], '_webmz_students_source' => 'manual', '_webmz_students_manual' => $p['sales'],
		'_webmz_product_features' => array_map( fn( $f ) => array( 'text' => $f ), $p['feats'] ),
		'_webmz_product_faq' => $p['faq'], '_webmz_spw_features' => $p['spw'],
		'_webmz_spw_purchase_items' => array(
			array( 'icon' => 'fas fa-bolt', 'text' => 'دانلود فوری پس از پرداخت' ),
			array( 'icon' => 'fas fa-sync-alt', 'text' => 'به‌روزرسانی رایگان مادام‌العمر' ),
			array( 'icon' => 'fas fa-undo', 'text' => '۷ روز ضمانت بازگشت وجه' ),
		),
		'_webmz_add_to_cart_text' => $p['price'] ? 'خرید و دانلود' : 'دانلود رایگان',
		'_webmz_course_training_level' => '', '_webmz_course_status' => 'finished',
	);
	if ( $p['preview'] ) { $meta['_webmz_extra_button_text'] = 'مشاهده دموی آنلاین'; $meta['_webmz_extra_button_url'] = $p['preview']; $meta['_webmz_extra_button_new_tab'] = '1'; }
	foreach ( $meta as $k => $v ) update_post_meta( $pid, $k, $v );
	$map['product'][ $p['slug'] ] = $pid;
}
// related "top" products for the vendor widget
WP_CLI::log( 'products: ' . count( $map['product'] ) );

// ---------- blog ----------
akd_delete_all( 'post' );
$bcat = array();
foreach ( array( 'آموزش وردپرس' => 'wordpress-tutorials', 'طراحی و گرافیک' => 'design', 'کسب‌وکار آنلاین' => 'online-business' ) as $n => $s ) $bcat[ $n ] = akd_term( 'category', $n, $s );
foreach ( get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false ) ) as $t ) if ( ! in_array( $t->term_id, $bcat, true ) && $t->term_id != get_option( 'default_category' ) ) wp_delete_term( $t->term_id, 'category' );
$admin = get_user_by( 'login', 'localadmin' ) ?: get_users( array( 'role' => 'administrator', 'number' => 1 ) )[0];
$authors = array( $map['user']['نشر دیجیتال آکام'], $map['user']['استودیو نوا'], $map['user']['گروه طراحی نگار'] );
foreach ( $POSTS as $k => $b ) {
	$pid = wp_insert_post( array(
		'post_type' => 'post', 'post_status' => 'publish', 'post_title' => $b['title'], 'post_name' => $b['slug'], 'post_content' => $b['content'],
		'post_excerpt' => mb_substr( wp_strip_all_tags( $b['content'] ), 0, 160 ) . '…', 'post_author' => $authors[ $k % 3 ],
		'post_date' => gmdate( 'Y-m-d H:i:s', time() - ( $k + 1 ) * 2 * DAY_IN_SECONDS ), 'post_category' => array( $bcat[ $b['cat'] ] ),
	) );
	set_post_thumbnail( $pid, $img( 'post-' . $b['slug'] . '.jpg', $b['title'] ) );
	update_post_meta( $pid, '_webmz_post_views', 180 + $k * 37 );
	update_post_meta( $pid, '_webmz_post_training_level', 'beginner' );
	if ( $k < 2 ) {
		update_post_meta( $pid, '_webmz_post_downloads', array(
			array( 'name' => 'چک‌لیست PDF این مقاله', 'size' => '۱٫۲ مگابایت', 'extension' => 'PDF', 'link' => akd_download_file( $b['slug'] . '-checklist.pdf', $b['title'] ) ),
		) );
	}
	akd_comments( $pid, array( array( 'مهسا کریمی', 'خیلی کاربردی بود، ممنون از مطلب خوبتون.' ), array( 'امیر رضایی', 'لطفاً درباره این موضوع ویدیو هم منتشر کنید.' ) ) );
	$map['post'][ $b['slug'] ] = $pid;
}

// ---------- stories ----------
$story_ids = get_posts( array( 'post_type' => 'webmz_story', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'ASC' ) );
$stories = array( array( 'تخفیف هفته', 'مشاهده تخفیف', '/shop/' ), array( 'محصول جدید', 'مشاهده محصول', '/product/payamax-sms-login/' ), array( 'آموزش نصب', 'مطالعه آموزش', '/blog/' ), array( 'فونت رایگان', 'دریافت فونت', '/product/nasim-font/' ), array( 'کد تخفیف', 'استفاده از کد', '/shop/' ) );
foreach ( $stories as $k => $s ) {
	$sid = $story_ids[ $k ] ?? wp_insert_post( array( 'post_type' => 'webmz_story', 'post_status' => 'publish', 'post_title' => $s[0] ) );
	wp_update_post( array( 'ID' => $sid, 'post_title' => $s[0] ) );
	$mid = $img( 'story-' . ( $k + 1 ) . '.jpg', $s[0] );
	set_post_thumbnail( $sid, $mid );
	update_post_meta( $sid, '_webmz_story_items', array( array( 'button_title' => $s[1], 'button_link' => home_url( $s[2] ), 'button_new_tab' => false, 'media_id' => $mid, 'media_url' => wp_get_attachment_url( $mid ), 'media_type' => 'image', 'duration' => 5, 'sync_video_duration' => false, 'disabled' => false ) ) );
	$story_ids[ $k ] = $sid;
}
$box = get_posts( array( 'post_type' => 'webmz_story_box', 'numberposts' => 1, 'fields' => 'ids' ) );
if ( $box ) { update_post_meta( $box[0], '_webmz_story_box_story_ids', array_slice( array_map( 'intval', $story_ids ), 0, 5 ) ); wp_update_post( array( 'ID' => $box[0], 'post_title' => 'استوری‌های آکام فایل' ) ); $map['story_box'] = $box[0]; }

// ---------- developers (teacher post type) ----------
$devs = array(
	array( 'نیما رستگار', 'مدیر استودیو نوا', 'person-2.png', 'توسعه‌دهنده قالب‌های ووکامرس با بیش از ۱۰ سال تجربه در طراحی فروشگاه‌های اینترنتی.', array( 'WooCommerce', 'Elementor', 'PHP' ), 'nava-studio' ),
	array( 'سارا نیک‌نام', 'مدیر هنری گروه نگار', 'female-2.png', 'طراح رابط کاربری و هویت بصری؛ خالق یو آی کیت سبدک و پک موکاپ نگار.', array( 'Figma', 'UI/UX', 'Branding' ), 'negar-design' ),
	array( 'آرش کامرانی', 'توسعه‌دهنده ارشد پارس‌کد', 'person-5.png', 'متخصص توسعه افزونه‌های وردپرس با تمرکز روی امنیت و کارایی.', array( 'WordPress API', 'Laravel', 'React' ), 'parscode' ),
	array( 'مریم صادقی', 'نویسنده و مشاور سئو', 'female-4.png', 'نویسنده کتاب «سئو در ۳۰ روز» و مشاور سئوی ده‌ها فروشگاه اینترنتی.', array( 'SEO', 'Content', 'Analytics' ), 'akam-publishing' ),
	array( 'پیمان ایزدی', 'طراح فونت', 'person-6.png', 'طراح حروف فارسی و مدیر تایپ‌استودیو سپهر؛ طراح فونت‌های سپهر و نسیم.', array( 'Type Design', 'Glyphs', 'Calligraphy' ), 'sepehr-type' ),
	array( 'الهام جعفری', 'طراح گرافیک سه‌بعدی', 'female-7.png', 'طراح آیکون و تصویرسازی سه‌بعدی؛ همکار گروه طراحی نگار.', array( 'Blender', '3D Icons', 'Illustration' ), 'negar-design' ),
);
$tids = get_posts( array( 'post_type' => 'webmz_teacher', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'ASC' ) );
foreach ( array_slice( $tids, count( $devs ) ) as $extra ) wp_delete_post( $extra, true );
foreach ( $devs as $k => $d ) {
	$slugs = array( 'nima-rastegar', 'sara-niknam', 'arash-kamrani', 'maryam-sadeghi', 'peyman-izadi', 'elham-jafari' );
	$data = array( 'post_type' => 'webmz_teacher', 'post_status' => 'publish', 'post_title' => $d[0], 'post_name' => $slugs[ $k ], 'post_content' => '<h2>درباره ' . $d[0] . '</h2><p>' . $d[3] . ' محصولات او در آکام فایل همواره جزو پرفروش‌ترین‌ها بوده و به دلیل کیفیت و پشتیبانی دقیق، رضایت خریداران را جلب کرده است.</p>', 'post_excerpt' => $d[3] );
	if ( isset( $tids[ $k ] ) ) { $data['ID'] = $tids[ $k ]; $tid = wp_update_post( $data ); } else { $tid = wp_insert_post( $data ); }
	$photo = akd_image( WP_CONTENT_DIR . '/uploads/2026/07/' . $d[2], $d[0] );
	set_post_thumbnail( $tid, $photo );
	$vendor_products = array();
	foreach ( $P as $pp ) if ( $vendors[ $pp['vendor'] ][0] === $d[5] ) $vendor_products[] = $map['product'][ $pp['slug'] ];
	foreach ( array(
		'_webmz_teacher_job_title' => $d[1], '_webmz_teacher_short_bio' => $d[3], '_webmz_teacher_student_count' => 900 + $k * 410,
		'_webmz_teacher_experience_years' => ( 6 + $k ) . ' سال', '_webmz_teacher_course_count' => max( 1, count( $vendor_products ) ), '_webmz_teacher_rating' => 4.9 - $k * 0.05,
		'_webmz_teacher_teaching_experience' => ( 6 + $k ) . ' سال سابقه طراحی و توسعه', '_webmz_teacher_expertise_photo' => $photo,
		'_webmz_teacher_specialties' => array_map( fn( $s ) => array( 'text' => $s ), $d[4] ),
		'_webmz_teacher_experience' => array( array( 'text' => 'فروشنده برتر آکام فایل' ), array( 'text' => 'همکاری با بیش از ۵۰ کسب‌وکار' ) ),
		'_webmz_teacher_courses' => $vendor_products,
	) as $mk => $mv ) update_post_meta( $tid, $mk, $mv );
	$map['teacher'][] = $tid;
}

file_put_contents( "$ND/d4_map.json", wp_json_encode( $map, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
WP_CLI::success( 'demo 4 content imported' );
