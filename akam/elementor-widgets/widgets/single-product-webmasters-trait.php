<?php
/**
 * Shared helpers for Webmasters single product Elementor widgets.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

defined( 'ABSPATH' ) || exit;

trait Single_Product_Webmasters_Trait {
	use Tadris_Widget_Controls_Trait;

	/**
	 * Elementor category slug for single product widgets.
	 *
	 * @return string
	 */
	protected function spw_category() {
		return webmz_elementor_single_product_category_slug();
	}

	/**
	 * Get current layout product.
	 *
	 * @return \WC_Product|false
	 */
	protected function spw_product() {
		if ( ! function_exists( 'wc_get_product' ) ) {
			return false;
		}

		$id = \webmz_get_context_post_id();

		if ( ! $id || 'product' !== get_post_type( $id ) ) {
			return false;
		}

		$product = wc_get_product( $id );

		return ( $product instanceof \WC_Product ) ? $product : false;
	}

	/**
	 * Whether a WooCommerce product object is usable.
	 *
	 * @param mixed $product Product candidate.
	 * @return bool
	 */
	protected function spw_is_valid_product( $product ) {
		return $product instanceof \WC_Product && $product->get_id() > 0;
	}

	/**
	 * Whether widget renders inside Elementor editor preview.
	 *
	 * @return bool
	 */
	protected function spw_is_editor_demo() {
		if ( \webmz_is_layout_editing_context() ) {
			return true;
		}

		if ( wp_doing_ajax() && ! empty( $_REQUEST['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$action = sanitize_key( wp_unslash( (string) $_REQUEST['action'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( 0 === strpos( $action, 'elementor_' ) ) {
				return true;
			}
		}

		if ( ! class_exists( '\Elementor\Plugin' ) || ! isset( \Elementor\Plugin::$instance ) ) {
			return false;
		}

		$plugin = \Elementor\Plugin::$instance;

		if ( isset( $plugin->editor ) && method_exists( $plugin->editor, 'is_edit_mode' ) && $plugin->editor->is_edit_mode() ) {
			return true;
		}

		if ( isset( $plugin->preview ) && method_exists( $plugin->preview, 'is_preview_mode' ) && $plugin->preview->is_preview_mode() ) {
			return true;
		}

		return false;
	}

	/**
	 * Backward-compatible alias.
	 *
	 * @return bool
	 */
	protected function spw_is_preview() {
		return $this->spw_is_editor_demo();
	}

	/**
	 * Use demo markup when designing in Elementor or when product context is missing.
	 *
	 * @param \WC_Product|false|null $product Product object.
	 * @return bool
	 */
	protected function spw_should_use_demo( $product = null ) {
		if ( $this->spw_is_valid_product( $product ) ) {
			return false;
		}

		return $this->spw_is_editor_demo();
	}

	/**
	 * Demo price data for editor preview.
	 *
	 * @return array{state:string,amount:string,regular_amount:string,currency:string}
	 */
	protected function spw_get_demo_price_data() {
		$currency = html_entity_decode(
			get_woocommerce_currency_symbol(),
			ENT_QUOTES,
			get_bloginfo( 'charset' )
		);

		return array(
			'state'             => 'priced',
			'amount'            => '2,500,000',
			'regular_amount'    => '5,000,000',
			'discount_percent'  => 50,
			'currency'          => $currency ? $currency : esc_html__( 'تومان', 'tadris' ),
		);
	}

	/**
	 * Render demo product media.
	 *
	 * @return void
	 */
	protected function spw_render_demo_media() {
		$placeholder = function_exists( 'wc_placeholder_img' ) ? wc_placeholder_img( 'woocommerce_single', array( 'class' => 'webmz-spw-demo-media-img' ) ) : '';
		$thumb       = function_exists( 'wc_placeholder_img' ) ? wc_placeholder_img( 'woocommerce_gallery_thumbnail', array( 'class' => 'webmz-spw-media__thumb-img' ) ) : '';
		?>
		<div class="webmz-spw-media webmz-spw-media--has-gallery webmz-spw-demo" data-webmz-spw-media>
			<div class="webmz-spw-media__stage">
				<div class="webmz-spw-media__item is-active" data-webmz-spw-media-item="image">
					<figure class="webmz-spw-media__image">
						<?php
						if ( $placeholder ) {
							echo $placeholder; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							?>
							<div class="webmz-spw-demo-media-fallback" aria-hidden="true"></div>
							<?php
						}
						?>
					</figure>
				</div>
			</div>
			<?php if ( $thumb ) : ?>
				<div class="webmz-spw-media__thumbs swiper" data-webmz-spw-media-thumbs dir="rtl">
					<div class="swiper-wrapper">
						<?php for ( $i = 0; $i < 4; $i++ ) : ?>
							<div class="swiper-slide">
								<button type="button" class="webmz-spw-media__thumb<?php echo 0 === $i ? ' is-active' : ''; ?>" data-webmz-spw-media-thumb data-index="<?php echo esc_attr( (string) $i ); ?>" aria-current="<?php echo 0 === $i ? 'true' : 'false'; ?>">
									<span class="webmz-spw-media__thumb-inner">
										<?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php if ( 3 === $i ) : ?>
											<span class="webmz-spw-media__thumb-play" aria-hidden="true"><svg viewBox="0 0 48 48" width="28" height="28" focusable="false"><circle cx="24" cy="24" r="22" fill="rgba(15,23,42,.55)"/><path d="M19 15.5v17l14-8.5-14-8.5z" fill="#fff"/></svg></span>
										<?php endif; ?>
									</span>
								</button>
							</div>
						<?php endfor; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render demo participants badge.
	 *
	 * @param array<string,mixed> $s Widget settings.
	 * @return void
	 */
	protected function spw_render_demo_participants( $s ) {
		$label = ! empty( $s['label'] ) ? $s['label'] : esc_html__( 'شرکت‌کننده', 'tadris' );
		?>
		<span class="webmz-spw-participants webmz-spw-demo"><?php echo esc_html( '۲ ' . $label ); ?></span>
		<?php
	}

	/**
	 * Render demo rating block.
	 *
	 * @param array<string,mixed> $s Widget settings.
	 * @return void
	 */
	protected function spw_render_demo_rating( $s ) {
		$target = ! empty( $s['scroll_target'] ) ? $s['scroll_target'] : '#reviews';
		?>
		<button type="button" class="webmz-spw-rating product-type-1-rating webmz-spw-demo" data-webmz-spw-scroll="<?php echo esc_attr( $target ); ?>">
			<p><?php echo esc_html( $s['rating_label'] ?? esc_html__( 'امتیاز', 'tadris' ) ); ?></p>
			<div class="webmz-spw-rating__star product-type-1-rating-star <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s, 'rating_icon_color_mode' ) ); ?>">
				<?php Icons_Manager::render_icon( $s['rating_icon'] ?? array( 'value' => 'fas fa-star', 'library' => 'fa-solid' ), array( 'aria-hidden' => 'true' ) ); ?>
				<strong>5.0</strong>
				<span>(12)</span>
			</div>
		</button>
		<?php
	}

	/**
	 * Render demo product title.
	 *
	 * @param array<string,mixed> $s         Widget settings.
	 * @param string              $title_tag Heading tag.
	 * @return void
	 */
	protected function spw_render_demo_title( $s, $title_tag ) {
		?>
		<<?php echo esc_attr( $title_tag ); ?> class="webmz-spw-title webmz-spw-demo">
			<?php
			$demo_title = __( 'آموزش پروژه محور [hl]NestJS[/hl] از صفر!', 'tadris' );
			echo function_exists( 'webmz_spw_parse_highlighted_title' )
				? wp_kses_post( webmz_spw_parse_highlighted_title( $demo_title ) )
				: esc_html( $demo_title );
			?>
		</<?php echo esc_attr( $title_tag ); ?>>
		<?php
	}

	/**
	 * Render demo short description.
	 *
	 * @return void
	 */
	protected function spw_render_demo_short_description() {
		?>
		<div class="webmz-spw-short-desc webmz-spw-demo">
			<p><?php esc_html_e( 'در این دوره NestJS را از پایه تا ساخت پروژه واقعی یاد می‌گیرید؛ مناسب توسعه‌دهندگانی که می‌خواهند بک‌اند مدرن Node.js را حرفه‌ای یاد بگیرند.', 'tadris' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render demo price block.
	 *
	 * @return void
	 */
	protected function spw_render_demo_price() {
		echo '<div class="webmz-spw-price webmz-spw-demo">';
		$this->spw_render_price( $this->spw_get_demo_price_data() );
		echo '</div>';
	}

	/**
	 * Resolve add-to-cart button label: product override, then widget setting.
	 *
	 * @param array<string,mixed> $s Widget settings.
	 * @return string
	 */
	protected function spw_get_button_text( $s ) {
		$product = $this->spw_product();

		if ( $this->spw_is_valid_product( $product ) && function_exists( 'webmz_spw_get_add_to_cart_text' ) ) {
			$custom = webmz_spw_get_add_to_cart_text( $product->get_id() );

			if ( '' !== $custom ) {
				return $custom;
			}
		}

		$text = isset( $s['button_text'] ) ? trim( (string) $s['button_text'] ) : '';

		if ( '' === $text ) {
			return esc_html__( 'ثبت‌نام در دوره', 'tadris' );
		}

		return $text;
	}

	/**
	 * Return cart scroll anchor attributes for add-to-cart widgets.
	 *
	 * Always outputs a data-anchor so sticky cart works even when Elementor
	 * renders the widget more than once (static id flags can skip the real output).
	 *
	 * @return string
	 */
	protected function spw_cart_scroll_id_attr() {
		static $id_used = false;

		$attr = ' data-webmz-spw-cart-anchor';

		if ( ! $id_used ) {
			$id_used = true;
			$attr    = ' id="cart-area-scroll"' . $attr;
		}

		return $attr;
	}

	/**
	 * Register shared add-to-cart button content controls.
	 *
	 * @return void
	 */
	protected function spw_register_add_to_cart_content_controls() {
		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'متن دکمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'ثبت‌نام در دوره', 'tadris' ),
				'label_block' => true,
				'description' => esc_html__( 'اگر در ویرایش محصول «متن دکمه افزودن به سبد خرید» پر شده باشد، همان متن نمایش داده می‌شود.', 'tadris' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'unavailable_text',
			array(
				'label'       => esc_html__( 'متن ناموجود', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'ناموجود', 'tadris' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'button_icon',
			array(
				'label'   => esc_html__( 'آیکون دکمه', 'tadris' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrow-left',
					'library' => 'fa-solid',
				),
			)
		);
	}

	/**
	 * Render demo add-to-cart button.
	 *
	 * @param array<string,mixed> $s Widget settings.
	 * @return void
	 */
	protected function spw_render_demo_add_to_cart( $s ) {
		$icon_html = ! empty( $s['button_icon']['value'] )
			? Icons_Manager::try_get_icon_html( $s['button_icon'], array( 'aria-hidden' => 'true' ) )
			: '';
		?>
		<div class="webmz-spw-add-to-cart-wrap webmz-spw-demo">
			<button type="button" class="webmz-spw-add-to-cart-btn" disabled>
				<?php if ( $icon_html ) : ?>
					<span class="webmz-spw-btn-icon"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
				<span><?php echo esc_html( $this->spw_get_button_text( $s ) ); ?></span>
			</button>
		</div>
		<?php
	}

	/**
	 * Render demo advanced add-to-cart widget.
	 *
	 * @param array<string,mixed> $s Widget settings.
	 * @return void
	 */
	protected function spw_render_demo_advanced_add_to_cart( $s ) {
		$price     = $this->spw_get_demo_price_data();
		$icon_html = ! empty( $s['button_icon']['value'] )
			? Icons_Manager::try_get_icon_html( $s['button_icon'], array( 'aria-hidden' => 'true' ) )
			: '';
		?>
		<div class="webmz-spw-advanced-atc webmz-spw webmz-spw-demo">
			<div class="webmz-spw-advanced-atc__body">
				<div class="webmz-spw-advanced-atc__main">
					<div class="webmz-spw-variation-form webmz-spw-advanced-atc__variations">
						<div class="webmz-spw-variation-row">
							<label class="webmz-spw-variation-label" for="webmz-spw-demo-level"><?php esc_html_e( 'سطح آموزش', 'tadris' ); ?></label>
							<div class="webmz-spw-variation-options" role="group">
								<button type="button" id="webmz-spw-demo-level" class="webmz-spw-variation-option is-selected" disabled><?php esc_html_e( 'پیشرفته', 'tadris' ); ?></button>
							</div>
						</div>
						<div class="webmz-spw-variation-row">
							<label class="webmz-spw-variation-label" for="webmz-spw-demo-sessions"><?php esc_html_e( 'تعداد جلسات', 'tadris' ); ?></label>
							<div class="webmz-spw-variation-options" role="group">
								<button type="button" id="webmz-spw-demo-sessions" class="webmz-spw-variation-option is-selected" disabled>۹</button>
							</div>
						</div>
						<div class="webmz-spw-variation-row">
							<label class="webmz-spw-variation-label" for="webmz-spw-demo-access-online"><?php esc_html_e( 'نوع دسترسی', 'tadris' ); ?></label>
							<div class="webmz-spw-variation-options" role="group">
								<button type="button" id="webmz-spw-demo-access-lifetime" class="webmz-spw-variation-option" disabled><?php esc_html_e( 'مادام‌العمر', 'tadris' ); ?></button>
								<button type="button" id="webmz-spw-demo-access-online" class="webmz-spw-variation-option is-selected" disabled><?php esc_html_e( 'آنلاین', 'tadris' ); ?></button>
							</div>
						</div>
					</div>
					<div class="webmz-spw-advanced-atc__action">
						<button type="button" class="webmz-spw-advanced-atc__btn webmz-spw-add-to-cart-btn" disabled>
							<?php if ( $icon_html ) : ?>
								<span class="webmz-spw-btn-icon"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php endif; ?>
							<span><?php echo esc_html( $this->spw_get_button_text( $s ) ); ?></span>
						</button>
					</div>
				</div>
				<div class="webmz-spw-advanced-atc__price" data-webmz-spw-price-display>
					<?php $this->spw_render_advanced_price_contents( $price ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render demo purchase box.
	 *
	 * @param array<string,mixed> $s Widget settings.
	 * @return void
	 */
	protected function spw_render_demo_purchase_box( $s ) {
		$price     = $this->spw_get_demo_price_data();
		$icon_html = ! empty( $s['button_icon']['value'] )
			? Icons_Manager::try_get_icon_html( $s['button_icon'], array( 'aria-hidden' => 'true' ) )
			: '';
		?>
		<div class="webmz-spw-purchase-box webmz-spw-demo">
			<div class="webmz-spw-purchase-box__payment">
				<span class="webmz-spw-purchase-box__payment-label"><?php echo esc_html( $s['payment_label'] ?? esc_html__( 'نحوه پرداخت:', 'tadris' ) ); ?></span>
				<span class="webmz-spw-purchase-box__payment-cash is-active"><?php echo esc_html( $s['cash_label'] ?? esc_html__( 'نقدی', 'tadris' ) ); ?></span>
			</div>
			<div class="webmz-spw-purchase-box__main-price">
				<?php $this->spw_render_purchase_price_contents( $price ); ?>
			</div>
			<div class="webmz-spw-purchase-box__total">
				<span><?php echo esc_html( $s['total_label'] ?? esc_html__( 'قیمت کل:', 'tadris' ) ); ?></span>
				<strong><?php $this->spw_render_purchase_price_contents( $price ); ?></strong>
			</div>
			<div class="webmz-spw-purchase-box__action">
				<button type="button" class="webmz-spw-purchase-box__btn webmz-spw-add-to-cart-btn" disabled>
					<?php if ( $icon_html ) : ?>
						<span class="webmz-spw-btn-icon"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
					<span><?php echo esc_html( $this->spw_get_button_text( $s ) ); ?></span>
				</button>
			</div>
			<?php if ( ! empty( $s['bottom_items'] ) ) : ?>
				<ul class="webmz-spw-purchase-box__bottom">
					<?php foreach ( $s['bottom_items'] as $item ) : ?>
						<li class="webmz-spw-purchase-box__bottom-item">
							<?php Icons_Manager::render_icon( $item['icon'] ?? array(), array( 'aria-hidden' => 'true' ) ); ?>
							<span><?php echo esc_html( $item['text'] ?? '' ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render demo instructor box.
	 *
	 * @param array<string,mixed> $s Widget settings.
	 * @return void
	 */
	protected function spw_render_demo_instructor( $s ) {
		?>
		<div class="webmz-spw-instructor webmz-spw-demo">
			<div class="webmz-spw-instructor__avatar"><?php echo get_avatar( 0, 192 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<strong class="webmz-spw-instructor__name"><?php esc_html_e( 'رضا محمدی', 'tadris' ); ?></strong>
			<span class="webmz-spw-instructor__role"><?php echo esc_html( $s['job_title'] ?? esc_html__( 'مدرس دوره', 'tadris' ) ); ?></span>
			<p class="webmz-spw-instructor__bio"><?php esc_html_e( 'توسعه‌دهنده فول‌استک با بیش از ۸ سال تجربه در Node.js و وردپرس. مدرس دوره‌های پروژه‌محور آکام.', 'tadris' ); ?></p>
			<span class="webmz-spw-instructor__btn"><?php echo esc_html( $s['profile_button'] ?? esc_html__( 'مشاهده پروفایل استاد', 'tadris' ) ); ?></span>
		</div>
		<?php
	}

	/**
	 * Render demo product content with read-more UI.
	 *
	 * @param array<string,mixed> $s Widget settings.
	 * @return void
	 */
	protected function spw_render_demo_product_content( $s ) {
		$enable = 'yes' === ( $s['enable_read_more'] ?? 'yes' );
		$height = isset( $s['collapsed_height']['size'] ) ? absint( $s['collapsed_height']['size'] ) : 320;
		?>
		<div class="webmz-spw-product-content webmz-spw-demo"<?php echo $enable ? ' data-webmz-spw-read-more style="--webmz-spw-content-max:' . esc_attr( $height ) . 'px;"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="webmz-spw-product-content__inner">
				<p><?php esc_html_e( 'در این دوره با مفاهیم پایه NestJS آشنا می‌شوید، ساخت API، احراز هویت، کار با دیتابیس و معماری ماژولار را تمرین می‌کنید و در پایان یک پروژه واقعی تحویل می‌دهید.', 'tadris' ); ?></p>
				<p><?php esc_html_e( 'سرفصل‌ها شامل مقدمات TypeScript، ساختار پروژه، Dependency Injection، Guards، Interceptors و استقرار روی سرور است.', 'tadris' ); ?></p>
			</div>
			<?php if ( $enable ) : ?>
				<div class="webmz-spw-product-content__fade"></div>
				<div class="webmz-spw-product-content__toggle-wrap">
					<button type="button" class="webmz-spw-read-more-btn" data-webmz-spw-read-more-btn disabled>
						<?php echo esc_html( $s['read_more_text'] ?? esc_html__( 'مشاهده بیشتر', 'tadris' ) ); ?>
					</button>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render demo FAQ accordion.
	 *
	 * @return void
	 */
	protected function spw_render_demo_faq() {
		$items = array(
			array(
				'question' => esc_html__( 'Nest.js چیه؟', 'tadris' ),
				'answer'   => esc_html__( 'NestJS یک فریم‌ورک Node.js برای ساخت API و بک‌اند مقیاس‌پذیر با TypeScript است.', 'tadris' ),
			),
			array(
				'question' => esc_html__( 'آیا پیش‌نیاز دارد؟', 'tadris' ),
				'answer'   => esc_html__( 'آشنایی پایه با JavaScript کافی است؛ مفاهیم TypeScript داخل دوره آموزش داده می‌شود.', 'tadris' ),
			),
			array(
				'question' => esc_html__( 'دسترسی به دوره چگونه است؟', 'tadris' ),
				'answer'   => esc_html__( 'پس از ثبت‌نام، دسترسی دائمی به ویدیوها و آپدیت‌های دوره برای شما فعال می‌شود.', 'tadris' ),
			),
		);
		?>
		<div class="webmz-spw-faq webmz-spw-demo">
			<?php foreach ( $items as $index => $item ) : ?>
				<div class="webmz-spw-faq__item<?php echo 0 === $index ? ' is-open' : ''; ?>">
					<button type="button" class="webmz-spw-faq__question" aria-expanded="<?php echo 0 === $index ? 'true' : 'false'; ?>" disabled>
						<span class="webmz-spw-faq__icon">?</span>
						<span class="webmz-spw-faq__question-text"><?php echo esc_html( $item['question'] ); ?></span>
						<span class="webmz-spw-faq__chevron" aria-hidden="true"></span>
					</button>
					<div class="webmz-spw-faq__answer" aria-hidden="<?php echo 0 === $index ? 'false' : 'true'; ?>">
						<div class="webmz-spw-faq__answer-inner">
							<p><?php echo esc_html( $item['answer'] ); ?></p>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render demo curriculum accordion for Elementor preview.
	 *
	 * @param array<string,mixed> $s Widget settings.
	 * @return void
	 */
	protected function spw_render_demo_curriculum( $s ) {
		$sections = array(
			array(
				'title'       => esc_html__( 'TypeScript', 'tadris' ),
				'description' => esc_html__( 'آموزش TypeScript (مفاهیم اصلی و مورد استفاده در Nest)', 'tadris' ),
				'lessons'     => array(
					array(
						'title'     => esc_html__( 'معرفی فصل تایپ اسکریپت در نست', 'tadris' ),
						'duration'  => '00:05:36',
						'is_free'   => '1',
						'video_url' => '#',
					),
					array(
						'title'     => esc_html__( 'نصب TypeScript و تنظیمات اولیه', 'tadris' ),
						'duration'  => '00:12:48',
						'is_free'   => '0',
						'video_url' => '#',
					),
					array(
						'title'     => esc_html__( 'تایپ‌ها و Interface در NestJS', 'tadris' ),
						'duration'  => '00:18:22',
						'is_free'   => '0',
						'video_url' => '#',
					),
				),
			),
			array(
				'title'       => esc_html__( 'NestJS Core', 'tadris' ),
				'description' => esc_html__( 'ساختار پروژه، ماژول‌ها و Dependency Injection', 'tadris' ),
				'lessons'     => array(
					array(
						'title'     => esc_html__( 'ایجاد پروژه NestJS', 'tadris' ),
						'duration'  => '00:09:15',
						'is_free'   => '1',
						'video_url' => '#',
					),
					array(
						'title'     => esc_html__( 'Controllers و Providers', 'tadris' ),
						'duration'  => '00:22:40',
						'is_free'   => '0',
						'video_url' => '#',
					),
				),
			),
		);

		$watch_icon = ! empty( $s['watch_icon']['value'] )
			? Icons_Manager::try_get_icon_html( $s['watch_icon'], array( 'aria-hidden' => 'true' ) )
			: '';
		$register_icon = ! empty( $s['register_icon']['value'] )
			? Icons_Manager::try_get_icon_html( $s['register_icon'], array( 'aria-hidden' => 'true' ) )
			: '';

		$this->spw_render_curriculum_markup(
			$sections,
			0,
			array(
				'display_only'    => false,
				'session_label'   => ! empty( $s['session_label'] ) ? $s['session_label'] : esc_html__( 'جلسه', 'tadris' ),
				'free_badge'      => ! empty( $s['free_badge'] ) ? $s['free_badge'] : esc_html__( 'رایگان', 'tadris' ),
				'watch_text'      => ! empty( $s['watch_text'] ) ? $s['watch_text'] : esc_html__( 'مشاهده ویدیو', 'tadris' ),
				'register_text'   => ! empty( $s['register_text'] ) ? $s['register_text'] : esc_html__( 'ثبت نام در دوره', 'tadris' ),
				'locked_alert'    => ! empty( $s['locked_alert_text'] ) ? $s['locked_alert_text'] : esc_html__( 'برای مشاهده جلسات قفل باید در دوره ثبت نام نمایید', 'tadris' ),
				'watch_icon'      => $watch_icon,
				'register_icon'   => $register_icon,
				'is_demo'         => true,
			)
		);
	}

	/**
	 * Render curriculum accordion markup.
	 *
	 * @param array<int,array<string,mixed>> $sections   Sections data.
	 * @param int                            $product_id Product ID.
	 * @param array<string,mixed>            $args       Render args.
	 * @return void
	 */
	protected function spw_render_curriculum_markup( $sections, $product_id, $args ) {
		$display_only    = ! empty( $args['display_only'] );
		$is_demo         = ! empty( $args['is_demo'] );
		$session_label   = $args['session_label'] ?? esc_html__( 'جلسه', 'tadris' );
		$free_badge      = $args['free_badge'] ?? esc_html__( 'رایگان', 'tadris' );
		$watch_text      = $args['watch_text'] ?? esc_html__( 'مشاهده ویدیو', 'tadris' );
		$register_text = $args['register_text'] ?? esc_html__( 'ثبت نام در دوره', 'tadris' );
		$locked_alert  = $args['locked_alert'] ?? esc_html__( 'برای مشاهده جلسات قفل باید در دوره ثبت نام نمایید', 'tadris' );
		$watch_icon    = $args['watch_icon'] ?? '';
		$register_icon   = $args['register_icon'] ?? '';
		?>
		<div class="webmz-spw-curriculum<?php echo $is_demo ? ' webmz-spw-demo' : ''; ?>" data-webmz-spw-curriculum data-webmz-spw-curriculum-alert="<?php echo esc_attr( $locked_alert ); ?>">
			<?php foreach ( $sections as $section_index => $section ) : ?>
				<?php
				$stats       = function_exists( 'webmz_spw_get_curriculum_section_stats' )
					? webmz_spw_get_curriculum_section_stats( $section )
					: array( 'count' => 0, 'duration_label' => '00:00:00' );
				$count_label = function_exists( 'webmz_to_persian_digits' )
					? webmz_to_persian_digits( (string) $stats['count'] )
					: (string) $stats['count'];
				$is_open     = 0 === $section_index;
				?>
				<div class="webmz-spw-curriculum__section<?php echo $is_open ? ' is-open' : ''; ?>">
					<button type="button" class="webmz-spw-curriculum__header" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"<?php echo $is_demo ? ' disabled' : ''; ?>>
						<span class="webmz-spw-curriculum__header-main">
							<?php if ( ! empty( $section['title'] ) ) : ?>
								<strong class="webmz-spw-curriculum__section-title"><?php echo esc_html( $section['title'] ); ?></strong>
							<?php endif; ?>
							<?php if ( ! empty( $section['description'] ) ) : ?>
								<?php if ( ! empty( $section['title'] ) ) : ?>
									<span class="webmz-spw-curriculum__dot" aria-hidden="true"></span>
								<?php endif; ?>
								<span class="webmz-spw-curriculum__section-desc"><?php echo esc_html( $section['description'] ); ?></span>
							<?php endif; ?>
						</span>
						<span class="webmz-spw-curriculum__header-meta">
							<span class="webmz-spw-curriculum__count"><?php echo esc_html( $count_label . ' ' . $session_label ); ?></span>
							<span class="webmz-spw-curriculum__meta-divider" aria-hidden="true"></span>
							<span class="webmz-spw-curriculum__duration"><?php echo esc_html( $stats['duration_label'] ); ?></span>
							<span class="webmz-spw-curriculum__chevron" aria-hidden="true">
								<svg class="webmz-spw-curriculum__chevron-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
						</span>
					</button>
					<div class="webmz-spw-curriculum__panel" aria-hidden="<?php echo $is_open ? 'false' : 'true'; ?>">
						<div class="webmz-spw-curriculum__panel-inner">
							<?php if ( ! empty( $section['lessons'] ) ) : ?>
								<ul class="webmz-spw-curriculum__lessons">
									<?php foreach ( $section['lessons'] as $lesson_index => $lesson ) : ?>
										<?php
										$lesson_number = function_exists( 'webmz_to_persian_digits' )
											? webmz_to_persian_digits( (string) ( $lesson_index + 1 ) )
											: (string) ( $lesson_index + 1 );
										$is_free       = ! empty( $lesson['is_free'] ) && '0' !== (string) $lesson['is_free'];
										$can_watch     = $is_demo
											? $is_free
											: ( function_exists( 'webmz_spw_lesson_can_watch' )
												? webmz_spw_lesson_can_watch( $lesson, $product_id, $display_only )
												: false );
										$duration      = ! empty( $lesson['duration'] )
											? ( function_exists( 'webmz_spw_format_duration' )
												? webmz_spw_format_duration( webmz_spw_duration_to_seconds( $lesson['duration'] ) )
												: $lesson['duration'] )
											: '';
										?>
										<li class="webmz-spw-curriculum__lesson">
											<div class="webmz-spw-curriculum__lesson-main">
												<span class="webmz-spw-curriculum__lesson-num"><?php echo esc_html( $lesson_number ); ?></span>
												<div class="webmz-spw-curriculum__lesson-info">
													<div class="webmz-spw-curriculum__lesson-title-row">
														<span class="webmz-spw-curriculum__lesson-title"><?php echo esc_html( $lesson['title'] ); ?></span>
														<?php if ( $is_free ) : ?>
															<span class="webmz-spw-curriculum__free-badge"><?php echo esc_html( $free_badge ); ?></span>
														<?php endif; ?>
													</div>
													<?php if ( $duration ) : ?>
														<span class="webmz-spw-curriculum__lesson-duration">
															<svg class="webmz-spw-curriculum__clock" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
															<?php echo esc_html( $duration ); ?>
														</span>
													<?php endif; ?>
												</div>
											</div>
											<div class="webmz-spw-curriculum__lesson-action">
												<?php if ( $can_watch ) : ?>
													<?php
													// Video links (MP4/Aparat/YouTube) play in the popup player; other links (e.g. SpotPlayer) open in a new tab.
													$watch_popup = ! $is_demo && function_exists( 'webmz_video_is_playable_url' ) && webmz_video_is_playable_url( $lesson['video_url'] );
													?>
													<a class="webmz-spw-curriculum__btn webmz-spw-curriculum__btn--watch" href="<?php echo esc_url( $lesson['video_url'] ); ?>"<?php echo $is_demo ? '' : ' target="_blank" rel="noopener noreferrer"'; ?><?php echo $watch_popup ? ' data-webmz-video-modal data-webmz-video-title="' . esc_attr( $lesson['title'] ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
														<?php if ( $watch_icon ) : ?>
															<span class="webmz-spw-curriculum__btn-icon"><?php echo $watch_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
														<?php endif; ?>
														<span><?php echo esc_html( $watch_text ); ?></span>
													</a>
												<?php else : ?>
													<button type="button" class="webmz-spw-curriculum__btn webmz-spw-curriculum__btn--register" data-webmz-spw-curriculum-register<?php echo $is_demo ? ' disabled' : ''; ?>>
														<?php if ( $register_icon ) : ?>
															<span class="webmz-spw-curriculum__btn-icon"><?php echo $register_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
														<?php endif; ?>
														<span><?php echo esc_html( $register_text ); ?></span>
													</button>
												<?php endif; ?>
											</div>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Get price display data (same logic as course loop widget).
	 *
	 * @param \WC_Product $product Product object.
	 * @return array{state:string,amount:string,regular_amount?:string,currency:string}
	 */
	protected function spw_get_price_data( $product ) {
		$currency = html_entity_decode(
			get_woocommerce_currency_symbol(),
			ENT_QUOTES,
			get_bloginfo( 'charset' )
		);

		if ( ! $product->is_in_stock() ) {
			return array(
				'state'    => 'unavailable',
				'amount'   => '',
				'currency' => $currency,
			);
		}

		if ( '' === $product->get_price() && ! $product->is_type( 'variable' ) ) {
			return array(
				'state'    => 'unavailable',
				'amount'   => '',
				'currency' => $currency,
			);
		}

		$raw_price = (float) wc_get_price_to_display( $product );

		if ( $product->is_type( 'variable' ) && $raw_price <= 0 ) {
			$min_price = $product->get_variation_price( 'min', true );

			if ( '' !== $min_price ) {
				$raw_price = (float) wc_get_price_to_display( $product, array( 'price' => $min_price ) );
			}
		}

		if ( $raw_price <= 0 && ! $product->is_type( 'variable' ) ) {
			return array(
				'state'          => 'free',
				'amount'         => '',
				'regular_amount' => '',
				'currency'       => $currency,
			);
		}

		if ( $raw_price <= 0 && $product->is_type( 'variable' ) ) {
			return array(
				'state'    => 'unavailable',
				'amount'   => '',
				'currency' => $currency,
			);
		}

		$regular_display_price = 0;
		$discount_percent      = 0;

		if ( $product->is_on_sale() ) {
			$regular_price = $product->is_type( 'variable' ) && method_exists( $product, 'get_variation_regular_price' )
				? $product->get_variation_regular_price( 'min', true )
				: $product->get_regular_price();

			if ( '' !== $regular_price ) {
				$regular_display_price = (float) wc_get_price_to_display( $product, array( 'price' => $regular_price ) );
			}

			if ( $regular_display_price > $raw_price && $regular_display_price > 0 ) {
				$discount_percent = (int) round( ( ( $regular_display_price - $raw_price ) / $regular_display_price ) * 100 );
			}
		}

		return array(
			'state'            => 'priced',
			'amount'           => number_format( $raw_price, 0, '.', ',' ),
			'regular_amount'   => $regular_display_price > $raw_price ? number_format( $regular_display_price, 0, '.', ',' ) : '',
			'discount_percent' => $discount_percent > 0 ? $discount_percent : 0,
			'currency'         => $currency,
		);
	}

	/**
	 * Render Tadris-style price markup.
	 *
	 * @param array{state:string,amount:string,regular_amount?:string,currency:string} $price Price data.
	 * @param string                                                                     $wrapper_class Optional wrapper class.
	 * @return void
	 */
	protected function spw_render_price( $price, $wrapper_class = 'tadris-price' ) {
		?>
		<div class="<?php echo esc_attr( $wrapper_class ); ?>">
			<?php if ( 'unavailable' === $price['state'] ) : ?>
				<div class="tadris-not-for-sale"><?php esc_html_e( 'ناموجود', 'tadris' ); ?></div>
			<?php elseif ( 'free' === $price['state'] ) : ?>
				<div class="tadris-free-price"><?php esc_html_e( 'رایگان', 'tadris' ); ?></div>
			<?php else : ?>
				<div class="tadris-price-amount"><?php echo esc_html( $price['amount'] ); ?></div>
				<div class="tadris-price-row">
					<div class="tadris-price-currency"><?php echo esc_html( $price['currency'] ); ?></div>
					<?php if ( ! empty( $price['regular_amount'] ) ) : ?>
						<div class="tadris-price-amount-del"><?php echo esc_html( $price['regular_amount'] ); ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Format full price string for purchase box.
	 *
	 * @param array{state:string,amount:string,regular_amount?:string,currency:string} $price Price data.
	 * @return string
	 */
	protected function spw_format_price_text( $price ) {
		if ( 'unavailable' === $price['state'] ) {
			return esc_html__( 'ناموجود', 'tadris' );
		}

		if ( 'free' === $price['state'] ) {
			return esc_html__( 'رایگان', 'tadris' );
		}

		return trim( $price['amount'] . ' ' . $price['currency'] );
	}

	/**
	 * Render purchase box price markup with separate amount and currency.
	 *
	 * @param array{state:string,amount:string,regular_amount?:string,currency:string} $price Price data.
	 * @return void
	 */
	protected function spw_render_purchase_price_contents( $price ) {
		if ( 'unavailable' === $price['state'] ) {
			esc_html_e( 'ناموجود', 'tadris' );
			return;
		}

		if ( 'free' === $price['state'] ) {
			esc_html_e( 'رایگان', 'tadris' );
			return;
		}
		?>
		<span class="webmz-spw-purchase-box__amount"><?php echo esc_html( $price['amount'] ); ?></span>
		<span class="webmz-spw-purchase-box__currency"><?php echo esc_html( $price['currency'] ); ?></span>
		<?php
	}

	/**
	 * Register shared style sections for Tadris price block.
	 *
	 * @param string $id       Section ID suffix.
	 * @param string $selector CSS selector.
	 * @return void
	 */
	protected function spw_register_price_style_controls( $id = 'price', $selector = '.webmz-spw-price .tadris-price' ) {
		$this->webmz_register_text_style_controls( $id . '_style', esc_html__( 'قیمت', 'tadris' ), $selector );
	}

	/**
	 * Render variable product attribute selectors.
	 *
	 * @param \WC_Product_Variable $product   Variable product.
	 * @param string               $form_class Form CSS class.
	 * @return void
	 */
	protected function spw_render_variable_form( $product, $form_class = 'webmz-spw-variation-form' ) {
		if ( ! $product || ! $product->is_type( 'variable' ) ) {
			return;
		}

		$attributes = $product->get_variation_attributes();

		if ( empty( $attributes ) ) {
			return;
		}

		$available = $product->get_available_variations();
		$defaults  = $product->get_default_attributes();

		/**
		 * Hook: woocommerce_before_variations_form.
		 */
		do_action( 'woocommerce_before_variations_form' );
		?>
		<form class="<?php echo esc_attr( $form_class ); ?>" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
			<?php
			/**
			 * Hook: woocommerce_before_single_variation.
			 */
			do_action( 'woocommerce_before_single_variation' );
			?>
			<?php foreach ( $attributes as $attribute_name => $options ) : ?>
				<?php
				$label         = wc_attribute_label( $attribute_name );
				$attr_key      = sanitize_title( $attribute_name );
				$default_value = '';
				$options       = array_values( (array) $options );
				$group_id      = 'webmz-spw-attr-' . $product->get_id() . '-' . $attr_key;

				if ( isset( $defaults[ $attribute_name ] ) ) {
					$default_value = (string) $defaults[ $attribute_name ];
				} elseif ( isset( $defaults[ $attr_key ] ) ) {
					$default_value = (string) $defaults[ $attr_key ];
				} elseif ( 1 === count( $options ) ) {
					$default_value = (string) $options[0];
				}

				$first_option    = isset( $options[0] ) ? (string) $options[0] : '';
				$first_option_id = $first_option !== '' ? $group_id . '-' . sanitize_title( $first_option ) : $group_id;
				?>
				<div class="webmz-spw-variation-row">
					<label class="webmz-spw-variation-label" for="<?php echo esc_attr( $first_option_id ); ?>"><?php echo esc_html( $label ); ?></label>
					<div class="webmz-spw-variation-options" data-attribute="<?php echo esc_attr( $attr_key ); ?>" role="group" aria-label="<?php echo esc_attr( $label ); ?>">
						<?php foreach ( $options as $option_index => $option ) : ?>
							<?php
							$option          = (string) $option;
							$is_selected     = $option === $default_value;
							$opt_class       = 'webmz-spw-variation-option' . ( $is_selected ? ' is-selected' : '' );
							$option_id       = $group_id . '-' . sanitize_title( $option );
							?>
							<button
								type="button"
								id="<?php echo esc_attr( $option_id ); ?>"
								class="<?php echo esc_attr( $opt_class ); ?>"
								data-value="<?php echo esc_attr( $option ); ?>"
								aria-pressed="<?php echo $is_selected ? 'true' : 'false'; ?>"
							>
								<?php echo esc_html( $option ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
			<input type="hidden" name="variation_id" value="">
			<script type="application/json" class="webmz-spw-variations-data"><?php echo wp_json_encode( $available ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
			<?php
			/**
			 * Hook: woocommerce_after_single_variation.
			 */
			do_action( 'woocommerce_after_single_variation' );
			?>
		</form>
		<?php
		/**
		 * Hook: woocommerce_after_variations_form.
		 */
		do_action( 'woocommerce_after_variations_form' );
	}

	/**
	 * Format discount badge text for advanced add-to-cart price.
	 *
	 * @param int $percent Discount percent.
	 * @return string
	 */
	protected function spw_format_discount_badge( $percent ) {
		$percent = absint( $percent );

		if ( $percent <= 0 ) {
			return '';
		}

		$badge = $percent . '%';

		if ( function_exists( 'webmz_to_persian_digits' ) ) {
			$badge = webmz_to_persian_digits( $badge );
		}

		return str_replace( '%', '٪', $badge );
	}

	/**
	 * Render advanced add-to-cart price block.
	 *
	 * @param array{state:string,amount:string,regular_amount?:string,discount_percent?:int,currency:string} $price Price data.
	 * @return void
	 */
	protected function spw_render_advanced_price_contents( $price ) {
		if ( 'unavailable' === $price['state'] ) {
			echo '<span class="webmz-spw-advanced-atc__amount">' . esc_html__( 'ناموجود', 'tadris' ) . '</span>';
			return;
		}

		if ( 'free' === $price['state'] ) {
			echo '<span class="webmz-spw-advanced-atc__amount">' . esc_html__( 'رایگان', 'tadris' ) . '</span>';
			return;
		}

		$regular_amount   = ! empty( $price['regular_amount'] ) ? (string) $price['regular_amount'] : '';
		$discount_percent = isset( $price['discount_percent'] ) ? absint( $price['discount_percent'] ) : 0;
		$discount_badge   = $this->spw_format_discount_badge( $discount_percent );
		?>
		<?php if ( $regular_amount || $discount_badge ) : ?>
			<span class="webmz-spw-advanced-atc__sale-meta">
				<?php if ( $regular_amount ) : ?>
					<span class="webmz-spw-advanced-atc__regular"><?php echo esc_html( $regular_amount ); ?></span>
				<?php endif; ?>
				<?php if ( $discount_badge ) : ?>
					<span class="webmz-spw-advanced-atc__discount"><?php echo esc_html( $discount_badge ); ?></span>
				<?php endif; ?>
			</span>
		<?php endif; ?>
		<span class="webmz-spw-advanced-atc__current">
			<span class="webmz-spw-advanced-atc__amount"><?php echo esc_html( $price['amount'] ); ?></span>
			<span class="webmz-spw-advanced-atc__currency"><?php echo esc_html( $price['currency'] ); ?></span>
		</span>
		<?php
	}

	/**
	 * Render add-to-cart button for simple/variable products.
	 *
	 * @param \WC_Product          $product Product.
	 * @param array<string,mixed> $s       Widget settings.
	 * @param string               $btn_class Button class.
	 * @return void
	 */
	protected function spw_render_add_to_cart_button( $product, $s, $btn_class = 'webmz-spw-add-to-cart-btn' ) {
		$product_id = $product->get_id();
		$icon_html  = ! empty( $s['button_icon']['value'] )
			? \Elementor\Icons_Manager::try_get_icon_html( $s['button_icon'], array( 'aria-hidden' => 'true' ) )
			: '';

		if ( function_exists( 'webmz_woocommerce_do_before_add_to_cart_hooks' ) ) {
			webmz_woocommerce_do_before_add_to_cart_hooks();
		} else {
			do_action( 'woocommerce_before_add_to_cart_form' );
			do_action( 'woocommerce_before_add_to_cart_button' );
		}

		if ( ! $product->is_in_stock() || ! $product->is_purchasable() ) {
			?>
			<span class="<?php echo esc_attr( $btn_class ); ?> is-disabled" aria-disabled="true">
				<?php echo esc_html( $s['unavailable_text'] ?? esc_html__( 'ناموجود', 'tadris' ) ); ?>
			</span>
			<?php
			if ( function_exists( 'webmz_woocommerce_do_after_add_to_cart_hooks' ) ) {
				webmz_woocommerce_do_after_add_to_cart_hooks();
			} else {
				do_action( 'woocommerce_after_add_to_cart_button' );
				do_action( 'woocommerce_after_add_to_cart_form' );
			}
			return;
		}

		if ( $product->is_type( 'variable' ) ) {
			?>
			<button type="button" class="<?php echo esc_attr( $btn_class ); ?> is-variable" disabled data-webmz-spw-add data-product-id="<?php echo esc_attr( $product_id ); ?>" data-quantity="1">
				<?php if ( $icon_html ) : ?>
					<span class="webmz-spw-btn-icon"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<?php endif; ?>
				<span><?php echo esc_html( $this->spw_get_button_text( $s ) ); ?></span>
			</button>
			<?php
			if ( function_exists( 'webmz_woocommerce_do_after_add_to_cart_hooks' ) ) {
				webmz_woocommerce_do_after_add_to_cart_hooks();
			} else {
				do_action( 'woocommerce_after_add_to_cart_button' );
				do_action( 'woocommerce_after_add_to_cart_form' );
			}
			return;
		}

		$is_ajax = $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock();
		?>
		<button
			type="button"
			class="<?php echo esc_attr( $btn_class ); ?>"
			<?php if ( $is_ajax ) : ?>
				data-webmz-spw-add
				data-product-id="<?php echo esc_attr( $product_id ); ?>"
				data-quantity="1"
			<?php else : ?>
				disabled
			<?php endif; ?>
		>
			<?php if ( $icon_html ) : ?>
				<span class="webmz-spw-btn-icon"><?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<?php endif; ?>
			<span><?php echo esc_html( $this->spw_get_button_text( $s ) ); ?></span>
		</button>
		<?php
		if ( function_exists( 'webmz_woocommerce_do_after_add_to_cart_hooks' ) ) {
			webmz_woocommerce_do_after_add_to_cart_hooks();
		} else {
			do_action( 'woocommerce_after_add_to_cart_button' );
			do_action( 'woocommerce_after_add_to_cart_form' );
		}
	}
}
