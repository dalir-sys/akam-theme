<?php
/**
 * Front page template.
 *
 * Displays the assigned static front page content when a page is selected in
 * Settings > Reading. Otherwise, displays the default WebMZ landing page for
 * the latest-posts homepage configuration.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/*
 * WordPress always loads front-page.php for the site front page when this file
 * exists. Therefore we must explicitly detect whether the administrator has
 * assigned a static page in Settings > Reading.
 */
$webmz_show_on_front = get_option( 'show_on_front' );
$webmz_front_page_id = absint( get_option( 'page_on_front' ) );

$webmz_has_static_front_page = (
	'page' === $webmz_show_on_front
	&& $webmz_front_page_id > 0
	&& is_page( $webmz_front_page_id )
);

get_header();

/*
 * Static homepage selected in WordPress Reading Settings.
 *
 * We intentionally render the assigned page content directly instead of the
 * default WebMZ hero layout. This allows a page designed with Elementor to
 * appear exactly as designed, including full-width sections.
 */
if ( $webmz_has_static_front_page ) :
	?>
	<main id="primary" class="site-main webmz-static-front-page">
		<?php
		while ( have_posts() ) :
			the_post();

			the_content();

			wp_link_pages(
				array(
					'before' => '<nav class="page-links">' . esc_html__( 'صفحات:', 'tadris' ),
					'after'  => '</nav>',
				)
			);
		endwhile;
		?>
	</main>
	<?php

	get_footer();
	return;
endif;

/*
 * Default WebMZ homepage.
 *
 * This part is displayed only when Settings > Reading is configured to show
 * the site's latest posts on the homepage.
 */
if ( ! webmz_render_location( 'archive_post' ) ) :
	?>
	<main id="primary" class="webmz-home">
		<section class="webmz-hero">
			<div class="webmz-container webmz-hero__grid">
				<div class="webmz-hero__content">
					<span class="webmz-kicker">
						<?php esc_html_e( 'قالب آکام', 'tadris' ); ?>
					</span>

					<h1>
						<?php esc_html_e( 'طراحی یک سایت حرفه ای، ساده تر از همیشه', 'tadris' ); ?>
					</h1>

					<p>
						<?php esc_html_e( 'هسته ای سبک برای ساخت صفحات اختصاصی با المنتور و راه اندازی فروشگاه ووکامرسی؛ با ظاهر یکپارچه و آماده توسعه.', 'tadris' ); ?>
					</p>

					<div class="webmz-hero__actions">
						<a class="webmz-button webmz-button--large" href="#webmz-features">
							<?php esc_html_e( 'شروع کنید', 'tadris' ); ?>
						</a>

						<a class="webmz-link-button" href="<?php echo esc_url( home_url( '/?s=' ) ); ?>">
							<?php esc_html_e( 'جستجو در سایت', 'tadris' ); ?>
						</a>
					</div>
				</div>

				<div class="webmz-hero__visual" aria-hidden="true">
					<div class="webmz-dashboard">
						<div class="webmz-dashboard__bar">
							<span></span>
							<span></span>
							<span></span>
						</div>

						<div class="webmz-dashboard__body">
							<div class="webmz-dashboard__panel webmz-dashboard__panel--large"></div>

							<div class="webmz-dashboard__cards">
								<span></span>
								<span></span>
								<span></span>
							</div>
						</div>
					</div>

					<div class="webmz-float-card webmz-float-card--one">
						<strong>+250</strong>
						<small><?php esc_html_e( 'بخش قابل طراحی', 'tadris' ); ?></small>
					</div>

					<div class="webmz-float-card webmz-float-card--two">
						<strong>Elementor</strong>
						<small><?php esc_html_e( 'سازگار و سریع', 'tadris' ); ?></small>
					</div>
				</div>
			</div>
		</section>

		<section class="webmz-stats">
			<div class="webmz-container webmz-stats__grid">
				<div>
					<strong>RTL</strong>
					<span><?php esc_html_e( 'طراحی فارسی', 'tadris' ); ?></span>
				</div>

				<div>
					<strong>Ajax</strong>
					<span><?php esc_html_e( 'پنل تنظیمات', 'tadris' ); ?></span>
				</div>

				<div>
					<strong>Woo</strong>
					<span><?php esc_html_e( 'آماده فروشگاه', 'tadris' ); ?></span>
				</div>

				<div>
					<strong>Free</strong>
					<span><?php esc_html_e( 'قالب ساز المنتوری', 'tadris' ); ?></span>
				</div>
			</div>
		</section>

		<section id="webmz-features" class="webmz-home-section">
			<div class="webmz-container">
				<header class="webmz-section-heading">
					<span class="webmz-kicker">
						<?php esc_html_e( 'امکانات قالب', 'tadris' ); ?>
					</span>

					<h2>
						<?php esc_html_e( 'پایه ای تمیز برای پروژه بعدی شما', 'tadris' ); ?>
					</h2>
				</header>

				<div class="webmz-feature-grid">
					<article class="webmz-feature">
						<span>⚡</span>
						<h3><?php esc_html_e( 'سبک و سریع', 'tadris' ); ?></h3>
						<p><?php esc_html_e( 'ساختار مینیمال با فایل های قابل توسعه برای پروژه های واقعی.', 'tadris' ); ?></p>
					</article>

					<article class="webmz-feature">
						<span>▣</span>
						<h3><?php esc_html_e( 'قالب ساز', 'tadris' ); ?></h3>
						<p><?php esc_html_e( 'طراحی هدر، فوتر و صفحات مهم با لایه های المنتوری.', 'tadris' ); ?></p>
					</article>

					<article class="webmz-feature">
						<span>✓</span>
						<h3><?php esc_html_e( 'فروشگاه آماده', 'tadris' ); ?></h3>
						<p><?php esc_html_e( 'پشتیبانی از ووکامرس و ویجت های داینامیک محصولات.', 'tadris' ); ?></p>
					</article>
				</div>
			</div>
		</section>

		<?php if ( have_posts() ) : ?>
			<section class="webmz-home-content">
				<div class="webmz-container">
					<header class="webmz-section-heading">
						<span class="webmz-kicker">
							<?php esc_html_e( 'تازه ترین مطالب', 'tadris' ); ?>
						</span>

						<h2>
							<?php esc_html_e( 'نوشته های جدید سایت', 'tadris' ); ?>
						</h2>
					</header>

					<div class="webmz-loop webmz-columns-3">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/content/card', get_post_type() );
						endwhile;
						?>
					</div>

					<?php the_posts_pagination(); ?>
				</div>
			</section>
		<?php endif; ?>
	</main>
	<?php
endif;

get_footer();