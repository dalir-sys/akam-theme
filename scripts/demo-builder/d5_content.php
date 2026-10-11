<?php
$ND = rtrim( $args[0], '/' );
require $ND . '/lib/common.php';
$IMG = "$ND/d5img";
$D = akd_json( "$ND/d5_data.json" );
$map = array( 'img' => array(), 'cat' => array(), 'product' => array(), 'teacher' => array(), 'post' => array() );
$img = function ( $f, $t = '' ) use ( $IMG, &$map ) { $id = akd_image( "$IMG/$f", $t ); $map['img'][ $f ] = array( $id, wp_get_attachment_url( $id ) ); return $id; };
$up = WP_CONTENT_DIR . '/uploads/2026/07/';
$video = content_url( 'uploads/2026/07/video.mp4' );

update_option( 'blogname', 'آکام ذهن' );
update_option( 'blogdescription', 'آکادمی آنلاین روانشناسی و مشاوره' );
$o = array_merge( get_option( 'webmz_options', array() ), array(
	'color_primary' => '#0d9488', 'color_secondary' => '#0d9488', 'color_primary_hover' => '#0f766e', 'color_primary_light' => '#e0f5f1',
	'color_text_dark' => '#12324a', 'color_text_gray' => '#64748b', 'color_text_navy' => '#12324a', 'color_background' => '#f5f8f7',
	'font_family' => 'peyda', 'popular_searches' => "مدیریت اضطراب\nفرزندپروری\nهوش هیجانی\nذهن‌آگاهی\nخواب",
	'account_accent_light' => '#e0f5f1', 'related_box_accent' => '#0d9488', 'related_box_bg' => '#eef8f6',
	'floating_contact_button_color' => '#0d9488', 'floating_contact_button_hover_color' => '#0f766e',
) );
$o['favicon_id'] = $img( 'favicon.png', 'آیکون آکام ذهن' );
if ( ! empty( $o['account_custom_endpoints'][0] ) ) { $o['account_custom_endpoints'][0]['title'] = 'رزرو جلسه مشاوره'; $o['account_custom_endpoints'][0]['content'] = 'برای رزرو جلسه مشاوره آنلاین، زمان دلخواه خود را از تقویم زیر انتخاب کنید.'; }
update_option( 'webmz_options', $o );

foreach ( glob( "$IMG/*.{png,jpg,svg}", GLOB_BRACE ) as $f ) if ( basename( $f )[0] !== '_' ) $img( basename( $f ) );
foreach ( array( 't-1.png', 't-2.png', 't-3.png', 't-4.png', 'gmail.png', '3d-book.png', 'headphone-smile.png' ) as $a ) { $id = akd_image( $up . $a ); $map['img'][ $a ] = array( $id, wp_get_attachment_url( $id ) ); }

// ---------- teachers ----------
$tids = get_posts( array( 'post_type' => 'webmz_teacher', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'ASC' ) );
foreach ( array_slice( $tids, count( $D['teachers'] ) ) as $x ) wp_delete_post( $x, true );
foreach ( $D['teachers'] as $k => $t ) {
	$data = array( 'post_type' => 'webmz_teacher', 'post_status' => 'publish', 'post_title' => $t['name'], 'post_name' => $t['slug'], 'menu_order' => $k,
		'post_content' => '<h2>درباره ' . $t['name'] . '</h2><p>' . $t['bio'] . ' او در دوره‌های آکام ذهن، مفاهیم علمی روانشناسی را با مثال‌های واقعی و تمرین‌های عملی آموزش می‌دهد و در کنار تدریس، جلسات مشاوره فردی نیز برگزار می‌کند.</p>', 'post_excerpt' => $t['bio'] );
	if ( isset( $tids[ $k ] ) ) { $data['ID'] = $tids[ $k ]; $tid = wp_update_post( $data ); } else { $tid = wp_insert_post( $data ); }
	$photo = akd_image( $up . $t['photo'], $t['name'] );
	set_post_thumbnail( $tid, $photo );
	$map['teacher'][ $t['slug'] ] = $tid;
	$map['user'][ $t['slug'] ] = akd_user( $t['slug'], $t['name'], $t['slug'] . '@akamzehn.test' );
	update_user_meta( $map['user'][ $t['slug'] ], 'description', $t['bio'] );
}
wp_update_user( array( 'ID' => 1, 'display_name' => 'تحریریه آکام ذهن', 'nickname' => 'تحریریه آکام ذهن' ) );

// ---------- categories ----------
foreach ( $D['cats'] as $c ) {
	$tid = akd_term( 'product_cat', $c[1], $c[0], array( 'description' => 'دوره‌های ' . $c[1] ) );
	update_term_meta( $tid, 'thumbnail_id', $img( $c[2] . '.jpg' ) );
	$map['cat'][ $c[0] ] = $tid;
}
foreach ( get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ) as $t ) if ( ! in_array( $t->term_id, $map['cat'], true ) && get_option( 'default_product_cat' ) != $t->term_id ) wp_delete_term( $t->term_id, 'product_cat' );

// ---------- courses ----------
akd_delete_all( 'product_variation' );
akd_delete_all( 'product' );
$names = array( 'سمیرا نادری', 'محمد جلالی', 'زهرا امیری', 'حامد صالحی', 'لیلا کریمی', 'پرهام رستمی', 'شیوا احمدی', 'بهنام قاسمی', 'آزاده فرهمند', 'مهدی شکوهی' );
$texts = array( 'محتوای دوره خیلی کاربردی بود و تمرین‌هایش واقعاً به زندگی روزمره‌ام کمک کرد.', 'بیان استاد آرام و قابل فهم بود؛ بعد از چند هفته تغییر را حس کردم.', 'کاربرگ‌ها و تمرین‌ها عالی بودند. پشتیبانی هم سریع جواب می‌داد.', 'بهترین دوره‌ای که در این زمینه دیدم؛ به دوستانم هم پیشنهاد کردم.' );
foreach ( $D['courses'] as $i => $c ) {
	$t = null; foreach ( $D['teachers'] as $tt ) if ( $tt['slug'] === $c['teacher'] ) $t = $tt;
	$p = new WC_Product_Simple();
	$p->set_name( $c['title'] ); $p->set_slug( $c['slug'] ); $p->set_status( 'publish' );
	$p->set_description( $c['desc'] ); $p->set_short_description( $c['short'] );
	$p->set_regular_price( (string) $c['price'] );
	if ( $c['sale'] ) { $p->set_sale_price( (string) $c['sale'] ); $p->set_date_on_sale_to( gmdate( 'Y-m-d 23:59:59', time() + ( 3 + $i ) * DAY_IN_SECONDS ) ); }
	$p->set_virtual( true ); $p->set_sold_individually( true );
	$p->set_category_ids( array( $map['cat'][ $c['cat'] ] ) );
	$p->set_image_id( $img( 'cover-' . $c['slug'] . '.jpg', $c['title'] ) );
	$p->set_reviews_allowed( true );
	$pid = $p->save();
	wp_update_post( array( 'ID' => $pid, 'post_author' => $map['user'][ $c['teacher'] ], 'post_date' => gmdate( 'Y-m-d H:i:s', time() - ( 15 - $i ) * DAY_IN_SECONDS ), 'menu_order' => $i ) );
	$rv = array(); for ( $r = 0; $r < 4; $r++ ) $rv[] = array( 'name' => $names[ ( $i + $r * 3 ) % 10 ], 'rating' => ( $r === 3 && $i % 3 === 1 ) ? 4 : 5, 'text' => $texts[ ( $i + $r ) % 4 ] );
	akd_reviews( $pid, $rv );
	$curr = array(); $n = 0;
	foreach ( $c['secs'] as $s ) {
		$lessons = array();
		foreach ( $s[2] as $l ) { $n++; $lessons[] = array( 'title' => $l, 'duration' => sprintf( '00:%02d:%02d', 8 + ( $n * 7 ) % 17, ( $n * 13 ) % 60 ), 'is_free' => $n <= 2 ? 1 : 0, 'video_url' => $video ); }
		$curr[] = array( 'title' => $s[0], 'description' => $s[1], 'lessons' => $lessons );
	}
	$reviews = 40 + $i * 17;
	$hl = preg_replace( '/^(\S+\s+\S+)(.*)$/u', '[hl]$1[/hl]$2', $c['title'] );
	foreach ( array(
		'total_sales' => $c['students'], '_wc_average_rating' => $i % 3 === 1 ? '4.80' : '4.90', '_wc_review_count' => $reviews, '_wc_rating_count' => array( 5 => $reviews - 4, 4 => 4 ),
		'_webmz_course_curriculum' => $curr, '_webmz_course_sessions' => $c['sessions'], '_webmz_course_status' => $i % 4 === 3 ? 'recording' : 'finished',
		'_webmz_course_training_level' => $c['level'], '_webmz_product_features' => array_map( fn( $f ) => array( 'text' => $f ), $c['feats'] ),
		'_webmz_product_faq' => $c['faq'], '_webmz_product_intro_video' => $video, '_webmz_product_loop_badge' => $t['name'], '_webmz_product_loop_thumb' => 0,
		'_webmz_product_preview_url' => '', '_webmz_single_product_title' => $hl, '_webmz_single_short_description' => $c['short'],
		'_webmz_students_source' => 'manual', '_webmz_students_manual' => $c['students'], '_webmz_course_teacher' => $map['teacher'][ $c['teacher'] ],
		'_webmz_spw_features' => $c['spw'], '_webmz_add_to_cart_text' => $c['price'] ? 'ثبت‌نام در دوره' : 'شروع رایگان دوره',
		'_webmz_spw_purchase_items' => array( array( 'icon' => 'fas fa-infinity', 'text' => 'دسترسی همیشگی به جلسات' ), array( 'icon' => 'fas fa-file-download', 'text' => 'کاربرگ‌ها و تمرین‌های قابل دانلود' ), array( 'icon' => 'fas fa-certificate', 'text' => 'گواهی پایان دوره آکام ذهن' ) ),
		'_webmz_extra_button_text' => 'مشاهده جلسه رایگان', '_webmz_extra_button_url' => $video, '_webmz_extra_button_new_tab' => '',
	) as $mk => $mv ) update_post_meta( $pid, $mk, $mv );
	$map['product'][ $c['slug'] ] = $pid;
}
// teacher meta (needs product ids)
foreach ( $D['teachers'] as $k => $t ) {
	$courses = array(); foreach ( $D['courses'] as $c ) if ( $c['teacher'] === $t['slug'] ) $courses[] = $map['product'][ $c['slug'] ];
	$tid = $map['teacher'][ $t['slug'] ];
	foreach ( array(
		'_webmz_teacher_job_title' => $t['job'], '_webmz_teacher_short_bio' => $t['bio'], '_webmz_teacher_student_count' => $t['students'],
		'_webmz_teacher_experience_years' => $t['years'] . ' سال', '_webmz_teacher_course_count' => count( $courses ), '_webmz_teacher_rating' => 4.9 - ( $k % 3 ) * 0.1,
		'_webmz_teacher_teaching_experience' => $t['years'] . ' سال سابقه درمان و تدریس', '_webmz_teacher_expertise_photo' => get_post_thumbnail_id( $tid ),
		'_webmz_teacher_intro_video_url' => $video,
		'_webmz_teacher_specialties' => array_map( fn( $s ) => array( 'text' => $s ), $t['spec'] ),
		'_webmz_teacher_experience' => array( array( 'text' => 'عضو سازمان نظام روانشناسی و مشاوره' ), array( 'text' => 'مدرس کارگاه‌های تخصصی سلامت روان' ) ),
		'_webmz_teacher_courses' => $courses,
	) as $mk => $mv ) update_post_meta( $tid, $mk, $mv );
}

// ---------- blog ----------
akd_delete_all( 'post' );
$bcat = array();
foreach ( array( 'سلامت روان' => 'mental-health-articles', 'روابط و خانواده' => 'family-articles', 'رشد فردی' => 'growth-articles' ) as $n => $s ) $bcat[ $n ] = akd_term( 'category', $n, $s );
foreach ( get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false ) ) as $t ) if ( ! in_array( $t->term_id, $bcat, true ) && $t->term_id != get_option( 'default_category' ) ) wp_delete_term( $t->term_id, 'category' );
$authors = array_values( $map['teacher'] );
foreach ( $D['posts'] as $k => $b ) {
	$pid = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => $b['title'], 'post_name' => $b['slug'], 'post_content' => $b['content'],
		'post_excerpt' => mb_substr( wp_strip_all_tags( $b['content'] ), 0, 150 ) . '…', 'post_author' => array_values( $map['user'] )[ $k % 6 ],
		'post_date' => gmdate( 'Y-m-d H:i:s', time() - ( $k + 1 ) * 2 * DAY_IN_SECONDS ), 'post_category' => array( $bcat[ $b['cat'] ] ) ) );
	set_post_thumbnail( $pid, $img( 'post-' . $b['slug'] . '.jpg', $b['title'] ) );
	update_post_meta( $pid, '_webmz_post_views', 240 + $k * 53 );
	update_post_meta( $pid, '_webmz_video_url', $video );
	update_post_meta( $pid, '_webmz_post_training_level', 'beginner' );
	if ( $k < 3 ) update_post_meta( $pid, '_webmz_post_downloads', array( array( 'name' => 'کاربرگ تمرین این مقاله', 'size' => '۸۵۰ کیلوبایت', 'extension' => 'PDF', 'link' => akd_download_file( $b['slug'] . '-worksheet.pdf', $b['title'] ) ) ) );
	akd_comments( $pid, array( array( 'سمیرا نادری', 'ممنون از مطلب مفیدتون، خیلی به موقع بود.' ), array( 'حامد صالحی', 'تمرین تنفس را امتحان کردم و واقعاً اثر داشت.' ) ) );
	$map['post'][ $b['slug'] ] = $pid;
}
$map['blog_cats'] = array_values( $bcat );

// ---------- stories ----------
$story_ids = get_posts( array( 'post_type' => 'webmz_story', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'ASC' ) );
$stories = array( array( 'درباره ما', 'آشنایی با آکام ذهن', '/about-us/' ), array( 'مشاوره رایگان', 'رزرو مشاوره', '/contact-us/' ), array( 'تست شخصیت', 'شروع تست', '/blog/' ), array( 'کارگاه آنلاین', 'ثبت‌نام', '/product/anxiety-stress-management/' ), array( 'نظرات مراجعان', 'مشاهده نظرات', '/shop/' ) );
foreach ( $stories as $k => $s ) {
	$sid = $story_ids[ $k ] ?? wp_insert_post( array( 'post_type' => 'webmz_story', 'post_status' => 'publish', 'post_title' => $s[0] ) );
	wp_update_post( array( 'ID' => $sid, 'post_title' => $s[0] ) );
	$mid = $img( 'story-' . ( $k + 1 ) . '.jpg', $s[0] );
	set_post_thumbnail( $sid, $mid );
	update_post_meta( $sid, '_webmz_story_items', array( array( 'button_title' => $s[1], 'button_link' => home_url( $s[2] ), 'button_new_tab' => false, 'media_id' => $mid, 'media_url' => wp_get_attachment_url( $mid ), 'media_type' => 'image', 'duration' => 5, 'sync_video_duration' => false, 'disabled' => false ) ) );
	$story_ids[ $k ] = $sid;
}
$box = get_posts( array( 'post_type' => 'webmz_story_box', 'numberposts' => 1, 'fields' => 'ids' ) );
if ( $box ) { update_post_meta( $box[0], '_webmz_story_box_story_ids', array_slice( array_map( 'intval', $story_ids ), 0, 5 ) ); wp_update_post( array( 'ID' => $box[0], 'post_title' => 'استوری‌های آکام ذهن' ) ); $map['story_box'] = $box[0]; }

file_put_contents( "$ND/d5_map.json", wp_json_encode( $map, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) );
WP_CLI::success( 'demo 5 content imported' );
