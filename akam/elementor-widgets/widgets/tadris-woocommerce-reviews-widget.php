<?php
/**
 * WebMZ WooCommerce product reviews widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * AJAX chat-style WooCommerce reviews widget for single products.
 */
class Tadris_WooCommerce_Reviews_Widget extends Widget_Base {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'webmz_wc_reviews_widget';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'ویجت نظرات ووکامرس آکام', 'tadris' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-review';
	}

	/**
	 * Widget categories.
	 *
	 * @return array<int,string>
	 */
	public function get_categories() {
		return array( function_exists( 'webmz_elementor_single_product_category_slug' ) ? webmz_elementor_single_product_category_slug() : 'general' );
	}

	/**
	 * Widget keywords.
	 *
	 * @return array<int,string>
	 */
	public function get_keywords() {
		return array( 'woocommerce', 'reviews', 'rating', 'comments', 'ajax', 'نظرات', 'امتیاز', 'ووکامرس', 'آکام' );
	}

	/**
	 * Style deps.
	 *
	 * @return array<int,string>
	 */
	public function get_style_depends() {
		return array( 'webmz-comments-widget', 'webmz-wc-reviews-widget' );
	}

	/**
	 * Script deps.
	 *
	 * @return array<int,string>
	 */
	public function get_script_depends() {
		return array( 'webmz-wc-reviews-widget' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'widget_title',
			array(
				'label'       => esc_html__( 'عنوان بخش', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'نظرات و امتیاز کاربران', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_count',
			array(
				'label'        => esc_html__( 'نمایش تعداد نظرات', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_average_rating',
			array(
				'label'        => esc_html__( 'نمایش میانگین امتیاز', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'بله', 'tadris' ),
				'label_off'    => esc_html__( 'خیر', 'tadris' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'form_title',
			array(
				'label'       => esc_html__( 'عنوان فرم', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'نظر خود را بنویسید', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'rating_label',
			array(
				'label'       => esc_html__( 'برچسب امتیازدهی', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'امتیاز شما', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'textarea_placeholder',
			array(
				'label'       => esc_html__( 'متن placeholder', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'تجربه خود از این محصول را بنویسید...', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'submit_text',
			array(
				'label'       => esc_html__( 'متن دکمه ارسال', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'ارسال نظر', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'box_style_section',
			array(
				'label' => esc_html__( 'استایل جعبه', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'box_background',
				'selector' => '{{WRAPPER}} .webmz-comments-widget',
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'top'      => 28,
					'right'    => 28,
					'bottom'   => 28,
					'left'     => 28,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-comments-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه‌ها', 'tadris' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => 28,
					'right'    => 28,
					'bottom'   => 28,
					'left'     => 28,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-comments-widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .webmz-comments-widget',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .webmz-comments-widget',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'typography_style_section',
			array(
				'label' => esc_html__( 'تایپوگرافی و رنگ‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'رنگ عنوان', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => array(
					'{{WRAPPER}} .webmz-comments-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .webmz-comments-title',
			)
		);

		$this->add_control(
			'accent_color',
			array(
				'label'     => esc_html__( 'رنگ اصلی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .webmz-comments-widget' => '--webmz-comments-accent: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get current product ID.
	 *
	 * @return int
	 */
	private function get_current_product_id() {
		if ( function_exists( 'webmz_get_context_post_id' ) ) {
			$product_id = webmz_get_context_post_id();
		} else {
			$product_id = get_the_ID();
		}

		if ( ! $product_id ) {
			$post = get_post();
			$product_id = $post ? $post->ID : 0;
		}

		return absint( $product_id );
	}

	/**
	 * Whether widget is in Elementor editor.
	 *
	 * @return bool
	 */
	private function is_editor_mode() {
		return class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode();
	}

	/**
	 * Render widget.
	 *
	 * @return void
	 */
	protected function render() {
		$settings    = $this->get_settings_for_display();
		$product_id  = $this->get_current_product_id();
		$ratings_on  = function_exists( 'webmz_wc_reviews_widget_ratings_enabled' ) && webmz_wc_reviews_widget_ratings_enabled();
		$rating_req  = $ratings_on && function_exists( 'webmz_wc_reviews_widget_rating_required' ) && webmz_wc_reviews_widget_rating_required();

		if ( ! function_exists( 'webmz_wc_reviews_widget_is_valid_product' ) || ! webmz_wc_reviews_widget_is_valid_product( $product_id ) ) {
			if ( $this->is_editor_mode() ) {
				echo '<div class="webmz-comments-editor-note">' . esc_html__( 'این ویجت برای صفحه تکی محصولات ووکامرس طراحی شده است.', 'tadris' ) . '</div>';
			}
			return;
		}

		$count          = function_exists( 'webmz_wc_reviews_widget_count' ) ? webmz_wc_reviews_widget_count( $product_id ) : 0;
		$average        = function_exists( 'webmz_wc_reviews_widget_average_rating' ) ? webmz_wc_reviews_widget_average_rating( $product_id ) : 0;
		$reviews_html   = function_exists( 'webmz_wc_reviews_widget_render_list' ) ? webmz_wc_reviews_widget_render_list( $product_id ) : '';
		$comments_open  = comments_open( $product_id );
		$must_login     = get_option( 'comment_registration' ) && ! is_user_logged_in();
		$require_fields = ! is_user_logged_in() && get_option( 'require_name_email' );
		$user_id        = get_current_user_id();
		$user_email     = $user_id ? wp_get_current_user()->user_email : '';
		$can_review     = function_exists( 'webmz_wc_reviews_widget_can_review' ) && webmz_wc_reviews_widget_can_review( $product_id, $user_id, $user_email );
		$verification   = function_exists( 'webmz_wc_reviews_widget_verification_required' ) && webmz_wc_reviews_widget_verification_required();
		?>
		<section
			id="reviews"
			class="webmz-comments-widget webmz-wc-reviews-widget"
			data-webmz-wc-reviews-widget
			data-product-id="<?php echo esc_attr( $product_id ); ?>"
			data-ratings-enabled="<?php echo $ratings_on ? '1' : '0'; ?>"
			data-rating-required="<?php echo $rating_req ? '1' : '0'; ?>"
		>
			<header class="webmz-comments-header">
				<div>
					<h3 class="webmz-comments-title"><?php echo esc_html( $settings['widget_title'] ); ?></h3>
					<p class="webmz-comments-subtitle"><?php esc_html_e( 'نظرات خریداران به صورت گفت‌وگو نمایش داده می‌شوند.', 'tadris' ); ?></p>
				</div>

				<div class="webmz-wc-reviews-header-meta">
					<?php if ( isset( $settings['show_average_rating'] ) && 'yes' === $settings['show_average_rating'] && $ratings_on && $count > 0 ) : ?>
						<div class="webmz-wc-reviews-average" data-webmz-wc-average-rating>
							<?php echo function_exists( 'webmz_wc_reviews_widget_render_stars' ) ? webmz_wc_reviews_widget_render_stars( $average ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<strong><?php echo esc_html( number_format_i18n( $average, 1 ) ); ?></strong>
						</div>
					<?php endif; ?>

					<?php if ( isset( $settings['show_count'] ) && 'yes' === $settings['show_count'] ) : ?>
						<span class="webmz-comments-count" data-webmz-wc-reviews-count><?php echo esc_html( number_format_i18n( $count ) ); ?></span>
					<?php endif; ?>
				</div>
			</header>

			<div class="webmz-comments-list" data-webmz-wc-reviews-list>
				<?php echo $reviews_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<?php if ( $comments_open ) : ?>
				<div class="webmz-comments-form-card">
					<?php if ( $must_login ) : ?>
						<div class="webmz-comments-login-required">
							<?php
							echo wp_kses_post(
								sprintf(
									/* translators: %s: login link. */
									esc_html__( 'برای ثبت نظر باید %s.', 'tadris' ),
									'<a href="' . esc_url( wp_login_url( get_permalink( $product_id ) ) ) . '">' . esc_html__( 'وارد حساب کاربری شوید', 'tadris' ) . '</a>'
								)
							);
							?>
						</div>
					<?php elseif ( ! $can_review && $verification ) : ?>
						<div class="webmz-comments-closed"><?php esc_html_e( 'فقط خریداران تأییدشده می‌توانند نظر ثبت کنند.', 'tadris' ); ?></div>
					<?php elseif ( ! $can_review ) : ?>
						<div class="webmz-comments-closed"><?php esc_html_e( 'امکان ثبت نظر برای این محصول وجود ندارد.', 'tadris' ); ?></div>
					<?php else : ?>
						<form class="webmz-comments-form webmz-wc-reviews-form" data-webmz-wc-reviews-form>
							<input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>">
							<input type="hidden" name="parent" value="0" data-webmz-wc-review-parent>
							<input type="hidden" name="rating" value="0" data-webmz-wc-review-rating>

							<div class="webmz-comments-form-head">
								<strong><?php echo esc_html( $settings['form_title'] ); ?></strong>
								<div class="webmz-comments-replying" data-webmz-wc-replying hidden>
									<span></span>
									<button type="button" data-webmz-wc-cancel-reply><?php esc_html_e( 'لغو پاسخ', 'tadris' ); ?></button>
								</div>
							</div>

							<?php if ( $ratings_on ) : ?>
								<div class="webmz-wc-review-rating-field" data-webmz-wc-rating-field>
									<span class="webmz-wc-review-rating-label"><?php echo esc_html( $settings['rating_label'] ); ?></span>
									<div class="webmz-wc-review-rating-input" role="radiogroup" aria-label="<?php echo esc_attr( $settings['rating_label'] ); ?>">
										<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
											<button type="button" class="webmz-wc-review-rating-star" data-rating-value="<?php echo esc_attr( (string) $i ); ?>" aria-label="<?php echo esc_attr( sprintf( __( '%d ستاره', 'tadris' ), $i ) ); ?>">
												<span aria-hidden="true">★</span>
											</button>
										<?php endfor; ?>
									</div>
								</div>
							<?php endif; ?>

							<?php if ( $require_fields ) : ?>
								<div class="webmz-comments-guest-fields">
									<label>
										<span><?php esc_html_e( 'نام شما', 'tadris' ); ?></span>
										<input type="text" name="author" autocomplete="name" required>
									</label>
									<label>
										<span><?php esc_html_e( 'ایمیل شما', 'tadris' ); ?></span>
										<input type="email" name="email" autocomplete="email" required>
									</label>
								</div>
							<?php endif; ?>

							<label class="webmz-comments-textarea-wrap">
								<span class="screen-reader-text"><?php esc_html_e( 'متن نظر', 'tadris' ); ?></span>
								<textarea name="content" rows="5" placeholder="<?php echo esc_attr( $settings['textarea_placeholder'] ); ?>" required></textarea>
							</label>

							<div class="webmz-comments-form-footer">
								<div class="webmz-comments-message" data-webmz-wc-reviews-message aria-live="polite"></div>
								<button type="submit" class="webmz-comments-submit">
									<span><?php echo esc_html( $settings['submit_text'] ); ?></span>
								</button>
							</div>
						</form>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div class="webmz-comments-closed"><?php esc_html_e( 'نظرات برای این محصول بسته شده‌اند.', 'tadris' ); ?></div>
			<?php endif; ?>
		</section>
		<?php
	}
}
