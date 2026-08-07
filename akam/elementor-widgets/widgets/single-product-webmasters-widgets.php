<?php
/**
 * Webmasters single product Elementor widgets.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

require_once WEBMZ_DIR . 'elementor-widgets/widgets/single-product-webmasters-trait.php';

/** 1. Product image or intro Plyr video + gallery thumbs. */
class SPW_Product_Media_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-product-media'; }
	public function get_title() { return esc_html__( 'تصویر / ویدیو محصول', 'tadris' ); }
	public function get_icon() { return 'eicon-product-images'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_script_depends() { return array( 'webmz-swiper', 'webmz-plyr', 'webmz-tadris-widgets', 'webmz-single-product-webmasters' ); }
	public function get_style_depends() { return array( 'webmz-swiper', 'webmz-plyr', 'webmz-plyr-widgets', 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'image_size', array(
			'label'   => esc_html__( 'اندازه تصویر', 'tadris' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'woocommerce_single',
			'options' => array(
				'woocommerce_single'           => esc_html__( 'تک محصول', 'tadris' ),
				'woocommerce_thumbnail'        => esc_html__( 'بندانگشتی', 'tadris' ),
				'large'                        => esc_html__( 'بزرگ', 'tadris' ),
				'full'                         => esc_html__( 'کامل', 'tadris' ),
			),
		) );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'wrapper', esc_html__( 'باکس رسانه', 'tadris' ), '.webmz-spw-media' );
		$this->webmz_register_box_style_controls( 'image', esc_html__( 'تصویر', 'tadris' ), '.webmz-spw-media__image' );
		$this->webmz_register_box_style_controls( 'thumbs', esc_html__( 'گالری بندانگشتی', 'tadris' ), '.webmz-spw-media__thumbs' );
	}

	/**
	 * Build a single attachment media item.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $image_size    Main image size.
	 * @param string $poster        Fallback video poster URL.
	 * @return array<string,mixed>|null
	 */
	protected function spw_build_attachment_media_item( $attachment_id, $image_size = 'woocommerce_single', $poster = '' ) {
		$attachment_id = absint( $attachment_id );
		$thumb_size    = 'woocommerce_gallery_thumbnail';

		if ( $attachment_id <= 0 ) {
			return null;
		}

		if ( wp_attachment_is( 'video', $attachment_id ) ) {
			$src = (string) wp_get_attachment_url( $attachment_id );
			if ( ! $src ) {
				return null;
			}
			$video_poster = (string) get_the_post_thumbnail_url( $attachment_id, 'large' );
			if ( ! $video_poster ) {
				$video_poster = $poster;
			}
			$thumb_html = $video_poster
				? '<img class="webmz-spw-media__thumb-img" src="' . esc_url( $video_poster ) . '" alt="" loading="lazy" decoding="async" />'
				: '';

			return array(
				'type'       => 'video',
				'src'        => $src,
				'poster'     => $video_poster,
				'thumb_html' => $thumb_html,
			);
		}

		if ( ! wp_attachment_is_image( $attachment_id ) ) {
			return null;
		}

		$full_html = (string) wp_get_attachment_image(
			$attachment_id,
			$image_size,
			false,
			array( 'class' => 'webmz-spw-media__full-img' )
		);
		$thumb_html = (string) wp_get_attachment_image(
			$attachment_id,
			$thumb_size,
			false,
			array( 'class' => 'webmz-spw-media__thumb-img', 'alt' => '' )
		);

		if ( ! $full_html ) {
			return null;
		}

		return array(
			'type'       => 'image',
			'full_html'  => $full_html,
			'thumb_html' => $thumb_html,
		);
	}

	/**
	 * Collect product media items.
	 * Gallery thumbs only when WooCommerce product gallery has images.
	 *
	 * @param \WC_Product $product    Product.
	 * @param string      $image_size Main image size.
	 * @return array{items:array<int,array<string,mixed>>,show_gallery:bool}
	 */
	protected function spw_collect_media_items( $product, $image_size = 'woocommerce_single' ) {
		$items       = array();
		$product_id  = $product->get_id();
		$video_url   = function_exists( 'webmz_spw_get_product_intro_video_url' ) ? webmz_spw_get_product_intro_video_url( $product_id ) : '';
		$poster      = has_post_thumbnail( $product_id ) ? (string) get_the_post_thumbnail_url( $product_id, 'large' ) : '';
		$thumb_size  = 'woocommerce_gallery_thumbnail';
		$gallery_ids = array_values( array_filter( array_map( 'absint', (array) $product->get_gallery_image_ids() ) ) );
		$has_gallery = ! empty( $gallery_ids );

		// No product gallery: show only intro video OR featured image (not both).
		if ( ! $has_gallery ) {
			if ( $video_url ) {
				$items[] = array(
					'type'       => 'video',
					'src'        => $video_url,
					'poster'     => $poster,
					'thumb_html' => '',
				);
			} elseif ( (int) $product->get_image_id() > 0 ) {
				$item = $this->spw_build_attachment_media_item( (int) $product->get_image_id(), $image_size, $poster );
				if ( $item ) {
					$items[] = $item;
				}
			}

			return array(
				'items'        => $items,
				'show_gallery' => false,
			);
		}

		if ( $video_url ) {
			$thumb_html = '';
			if ( has_post_thumbnail( $product_id ) ) {
				$thumb_html = (string) get_the_post_thumbnail( $product_id, $thumb_size, array( 'class' => 'webmz-spw-media__thumb-img', 'alt' => '' ) );
			}
			$items[] = array(
				'type'       => 'video',
				'src'        => $video_url,
				'poster'     => $poster,
				'thumb_html' => $thumb_html,
			);
		}

		$attachment_ids = array_values(
			array_filter(
				array_unique(
					array_merge(
						array( (int) $product->get_image_id() ),
						$gallery_ids
					)
				)
			)
		);

		foreach ( $attachment_ids as $attachment_id ) {
			$item = $this->spw_build_attachment_media_item( $attachment_id, $image_size, $poster );
			if ( $item ) {
				$items[] = $item;
			}
		}

		return array(
			'items'        => $items,
			'show_gallery' => count( $items ) > 1,
		);
	}

	/**
	 * Play icon markup for video thumbs.
	 *
	 * @return string
	 */
	protected function spw_media_play_icon() {
		return '<span class="webmz-spw-media__thumb-play" aria-hidden="true"><svg viewBox="0 0 48 48" width="28" height="28" focusable="false"><circle cx="24" cy="24" r="22" fill="rgba(15,23,42,.55)"/><path d="M19 15.5v17l14-8.5-14-8.5z" fill="#fff"/></svg></span>';
	}

	/**
	 * Render one stage media item.
	 *
	 * @param array<string,mixed> $item   Media item.
	 * @param bool                $active Whether active.
	 * @return void
	 */
	protected function spw_render_media_stage_item( $item, $active = false ) {
		$type    = isset( $item['type'] ) ? (string) $item['type'] : 'image';
		$classes = 'webmz-spw-media__item' . ( $active ? ' is-active' : '' );
		?>
		<div class="<?php echo esc_attr( $classes ); ?>" data-webmz-spw-media-item="<?php echo esc_attr( $type ); ?>"<?php echo $active ? ' aria-hidden="false"' : ' aria-hidden="true"'; ?>>
			<?php if ( 'video' === $type ) : ?>
				<?php
				$poster = ! empty( $item['poster'] ) ? (string) $item['poster'] : '';
				$src    = ! empty( $item['src'] ) ? (string) $item['src'] : '';
				?>
				<div class="webmz-plyr-widget webmz-plyr-widget--video webmz-spw-media__video">
					<video class="tadris-player-tag webmz-standalone-plyr webmz-standalone-plyr--video" playsinline controls preload="metadata"<?php echo $poster ? ' poster="' . esc_url( $poster ) . '" data-poster="' . esc_url( $poster ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<source src="<?php echo esc_url( $src ); ?>" type="video/mp4">
					</video>
				</div>
			<?php else : ?>
				<figure class="webmz-spw-media__image">
					<?php echo isset( $item['full_html'] ) ? $item['full_html'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</figure>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$product = $this->spw_product();

		if ( $this->spw_should_use_demo( $product ) ) {
			$this->spw_render_demo_media();
			return;
		}

		if ( ! $this->spw_is_valid_product( $product ) ) {
			return;
		}

		$image_size   = $s['image_size'] ?? 'woocommerce_single';
		$media        = $this->spw_collect_media_items( $product, $image_size );
		$items        = isset( $media['items'] ) ? $media['items'] : array();
		$show_thumbs  = ! empty( $media['show_gallery'] );

		if ( ! $items ) {
			if ( $this->spw_is_editor_demo() ) {
				$this->spw_render_demo_media();
			}
			return;
		}
		?>
		<div class="webmz-spw-media<?php echo $show_thumbs ? ' webmz-spw-media--has-gallery' : ''; ?>" data-webmz-spw-media>
			<div class="webmz-spw-media__stage">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php $this->spw_render_media_stage_item( $item, 0 === $index ); ?>
				<?php endforeach; ?>
			</div>
			<?php if ( $show_thumbs ) : ?>
				<div class="webmz-spw-media__thumbs swiper" data-webmz-spw-media-thumbs dir="rtl">
					<div class="swiper-wrapper">
						<?php foreach ( $items as $index => $item ) : ?>
							<?php
							$is_video = isset( $item['type'] ) && 'video' === $item['type'];
							$label    = $is_video
								? esc_html__( 'نمایش ویدیو', 'tadris' )
								: esc_html__( 'نمایش تصویر', 'tadris' );
							?>
							<div class="swiper-slide">
								<button
									type="button"
									class="webmz-spw-media__thumb<?php echo 0 === $index ? ' is-active' : ''; ?><?php echo $is_video ? ' webmz-spw-media__thumb--video' : ''; ?>"
									data-webmz-spw-media-thumb
									data-index="<?php echo esc_attr( (string) $index ); ?>"
									aria-label="<?php echo esc_attr( $label ); ?>"
									aria-current="<?php echo 0 === $index ? 'true' : 'false'; ?>"
								>
									<span class="webmz-spw-media__thumb-inner">
										<?php
										if ( ! empty( $item['thumb_html'] ) ) {
											echo $item['thumb_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										} else {
											echo '<span class="webmz-spw-media__thumb-fallback" aria-hidden="true"></span>';
										}
										if ( $is_video ) {
											echo $this->spw_media_play_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										}
										?>
									</span>
								</button>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}

/** 2. Participants badge. */
class SPW_Participants_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-participants'; }
	public function get_title() { return esc_html__( 'تعداد شرکت‌کنندگان', 'tadris' ); }
	public function get_icon() { return 'eicon-person'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_style_depends() { return array( 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'label', array(
			'label'   => esc_html__( 'برچسب', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'شرکت‌کننده', 'tadris' ),
		) );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'badge', esc_html__( 'نشان', 'tadris' ), '.webmz-spw-participants' );
		$this->webmz_register_text_style_controls( 'text', esc_html__( 'متن', 'tadris' ), '.webmz-spw-participants' );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$product = $this->spw_product();

		if ( $this->spw_should_use_demo( $product ) ) {
			$this->spw_render_demo_participants( $s );
			return;
		}

		if ( ! $this->spw_is_valid_product( $product ) ) {
			return;
		}

		$count = function_exists( 'webmz_spw_get_product_students_count' )
			? webmz_spw_get_product_students_count( $product->get_id(), $product )
			: 0;
		?>
		<span class="webmz-spw-participants">
			<?php echo esc_html( number_format_i18n( $count ) . ' ' . $s['label'] ); ?>
		</span>
		<?php
	}
}

/** 3. Rating with scroll to reviews. */
class SPW_Rating_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-rating'; }
	public function get_title() { return esc_html__( 'امتیاز و دیدگاه‌ها', 'tadris' ); }
	public function get_icon() { return 'eicon-rating'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_script_depends() { return array( 'webmz-single-product-webmasters' ); }
	public function get_style_depends() { return array( 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'rating_label', array(
			'label'   => esc_html__( 'برچسب امتیاز', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'امتیاز', 'tadris' ),
		) );
		$this->add_control( 'scroll_target', array(
			'label'       => esc_html__( 'هدف اسکرول (CSS Selector)', 'tadris' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '#reviews',
			'label_block' => true,
		) );
		$this->add_control( 'rating_icon', array(
			'label'   => esc_html__( 'آیکون امتیاز', 'tadris' ),
			'type'    => Controls_Manager::ICONS,
			'default' => array( 'value' => 'fas fa-star', 'library' => 'fa-solid' ),
		) );
		$this->webmz_register_icon_color_mode_control( 'rating_icon_color_mode' );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'wrapper', esc_html__( 'باکس', 'tadris' ), '.webmz-spw-rating' );
		$this->webmz_register_text_style_controls( 'rating', esc_html__( 'امتیاز', 'tadris' ), '.webmz-spw-rating__star' );
		$this->webmz_register_icon_style_controls( 'icon', esc_html__( 'آیکون', 'tadris' ), '.webmz-spw-rating__star' );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$product = $this->spw_product();

		if ( $this->spw_should_use_demo( $product ) ) {
			$this->spw_render_demo_rating( $s );
			return;
		}

		if ( ! $this->spw_is_valid_product( $product ) ) {
			return;
		}

		$rating  = (float) $product->get_average_rating();
		$reviews = absint( $product->get_rating_count() );
		$target  = ! empty( $s['scroll_target'] ) ? $s['scroll_target'] : '#reviews';
		?>
		<button type="button" class="webmz-spw-rating product-type-1-rating" data-webmz-spw-scroll="<?php echo esc_attr( $target ); ?>">
			<p><?php echo esc_html( $s['rating_label'] ?? esc_html__( 'امتیاز', 'tadris' ) ); ?></p>
			<div class="webmz-spw-rating__star product-type-1-rating-star <?php echo esc_attr( $this->webmz_get_icon_color_mode_class( $s, 'rating_icon_color_mode' ) ); ?>">
				<?php Icons_Manager::render_icon( $s['rating_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				<strong><?php echo esc_html( number_format_i18n( $rating, 1 ) ); ?></strong>
				<span>(<?php echo esc_html( number_format_i18n( $reviews ) ); ?>)</span>
			</div>
		</button>
		<?php
	}
}

/** 4. Product title with highlight. */
class SPW_Product_Title_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-product-title'; }
	public function get_title() { return esc_html__( 'عنوان محصول (سینگل)', 'tadris' ); }
	public function get_icon() { return 'eicon-product-title'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_style_depends() { return array( 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ HTML', 'tadris' ) );
		$this->end_controls_section();
		$this->webmz_register_text_style_controls( 'title', esc_html__( 'عنوان', 'tadris' ), '.webmz-spw-title' );
		$this->start_controls_section( 'highlight_style', array( 'label' => esc_html__( 'بخش برجسته', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'highlight_color', array(
			'label'     => esc_html__( 'رنگ پیش‌فرض برجسته', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => array( '{{WRAPPER}} .webmz-spw-title-highlight' => 'color: {{VALUE}};' ),
		) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'     => 'highlight_typography',
			'selector' => '{{WRAPPER}} .webmz-spw-title-highlight',
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$s         = $this->get_settings_for_display();
		$title_tag = $this->webmz_get_title_tag( $s, 'title_tag' );
		$product   = $this->spw_product();

		if ( $this->spw_should_use_demo( $product ) ) {
			$this->spw_render_demo_title( $s, $title_tag );
			return;
		}

		if ( ! $this->spw_is_valid_product( $product ) ) {
			return;
		}

		$title_html = function_exists( 'webmz_spw_get_product_title_html' )
			? webmz_spw_get_product_title_html( $product->get_id() )
			: esc_html( $product->get_name() );

		if ( '' === trim( wp_strip_all_tags( $title_html ) ) && $this->spw_is_editor_demo() ) {
			$this->spw_render_demo_title( $s, $title_tag );
			return;
		}

		$inline_color = function_exists( 'webmz_spw_get_product_title_highlight_color' )
			? webmz_spw_get_product_title_highlight_color( $product->get_id() )
			: '';

		$style_attr = $inline_color ? ' style="--webmz-spw-title-highlight:' . esc_attr( $inline_color ) . ';"' : '';
		?>
		<<?php echo esc_attr( $title_tag ); ?> class="webmz-spw-title"<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</<?php echo esc_attr( $title_tag ); ?>>
		<?php
	}
}

/** 5. Short description from metabox. */
class SPW_Short_Description_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-short-description'; }
	public function get_title() { return esc_html__( 'توضیح کوتاه (سینگل)', 'tadris' ); }
	public function get_icon() { return 'eicon-text'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_style_depends() { return array( 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->webmz_register_text_style_controls( 'text', esc_html__( 'متن', 'tadris' ), '.webmz-spw-short-desc' );
		$this->webmz_register_box_style_controls( 'box', esc_html__( 'باکس', 'tadris' ), '.webmz-spw-short-desc' );
	}

	protected function render() {
		$product = $this->spw_product();

		if ( $this->spw_should_use_demo( $product ) ) {
			$this->spw_render_demo_short_description();
			return;
		}

		if ( ! $this->spw_is_valid_product( $product ) ) {
			return;
		}

		$text = function_exists( 'webmz_spw_get_product_short_description' )
			? webmz_spw_get_product_short_description( $product->get_id() )
			: '';

		if ( '' === trim( $text ) ) {
			if ( $this->spw_is_editor_demo() ) {
				$this->spw_render_demo_short_description();
			}
			return;
		}
		?>
		<div class="webmz-spw-short-desc"><?php echo wp_kses_post( wpautop( $text ) ); ?></div>
		<?php
	}
}

/** 6. Product price (Tadris style). */
class SPW_Product_Price_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-product-price'; }
	public function get_title() { return esc_html__( 'قیمت محصول (سینگل)', 'tadris' ); }
	public function get_icon() { return 'eicon-product-price'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_style_depends() { return array( 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->spw_register_price_style_controls();
		$this->webmz_register_box_style_controls( 'wrapper', esc_html__( 'باکس', 'tadris' ), '.webmz-spw-price' );
	}

	protected function render() {
		$product = $this->spw_product();

		if ( $this->spw_should_use_demo( $product ) ) {
			$this->spw_render_demo_price();
			return;
		}

		if ( ! $this->spw_is_valid_product( $product ) ) {
			return;
		}

		echo '<div class="webmz-spw-price">';
		$this->spw_render_price( $this->spw_get_price_data( $product ), 'tadris-price' );
		echo '</div>';
	}
}

/** 7. AJAX add to cart button. */
class SPW_Add_To_Cart_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-add-to-cart'; }
	public function get_title() { return esc_html__( 'دکمه افزودن به سبد (سینگل)', 'tadris' ); }
	public function get_icon() { return 'eicon-product-add-to-cart'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_script_depends() { return array( 'webmz-tadris-widgets', 'webmz-single-product-webmasters', 'webmz-header-commerce', 'webmz-mobile-offcanvas' ); }
	public function get_style_depends() { return array( 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->spw_register_add_to_cart_content_controls();
		$this->add_control( 'show_variations', array(
			'label'        => esc_html__( 'نمایش انتخاب متغیر', 'tadris' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'return_value' => 'yes',
		) );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'button', esc_html__( 'دکمه', 'tadris' ), '.webmz-spw-add-to-cart-btn' );
		$this->webmz_register_text_style_controls( 'button_text_style', esc_html__( 'تایپوگرافی دکمه', 'tadris' ), '.webmz-spw-add-to-cart-btn' );
		$this->webmz_register_box_style_controls( 'variation', esc_html__( 'گزینه متغیر', 'tadris' ), '.webmz-spw-variation-option' );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$product = $this->spw_product();

		if ( $this->spw_should_use_demo( $product ) ) {
			$this->spw_render_demo_add_to_cart( $s );
			return;
		}

		if ( ! $this->spw_is_valid_product( $product ) ) {
			return;
		}
		?>
		<div class="webmz-spw-add-to-cart-wrap webmz-spw" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"<?php echo $this->spw_cart_scroll_id_attr(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( 'yes' === ( $s['show_variations'] ?? 'yes' ) && $product->is_type( 'variable' ) ) : ?>
				<?php $this->spw_render_variable_form( $product ); ?>
			<?php endif; ?>
			<?php $this->spw_render_add_to_cart_button( $product, $s ); ?>
		</div>
		<?php
	}
}

/** 7b. Advanced single add to cart with synced variations + live price. */
class SPW_Advanced_Add_To_Cart_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-advanced-add-to-cart'; }
	public function get_title() { return esc_html__( 'افزودن به سبد خرید سینگل پیشرفته', 'tadris' ); }
	public function get_icon() { return 'eicon-product-add-to-cart'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_keywords() {
		return array( 'woocommerce', 'add to cart', 'variation', 'قیمت', 'سبد', 'متغیر' );
	}
	public function get_script_depends() { return array( 'webmz-tadris-widgets', 'webmz-single-product-webmasters', 'webmz-header-commerce', 'webmz-mobile-offcanvas' ); }
	public function get_style_depends() { return array( 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->spw_register_add_to_cart_content_controls();
		$this->add_control( 'show_variations', array(
			'label'        => esc_html__( 'نمایش انتخاب متغیر', 'tadris' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'return_value' => 'yes',
		) );
		$this->add_control( 'show_price', array(
			'label'        => esc_html__( 'نمایش قیمت', 'tadris' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'return_value' => 'yes',
		) );
		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'wrapper', esc_html__( 'باکس', 'tadris' ), '.webmz-spw-advanced-atc', array( 'bordered' => true ) );
		$this->webmz_register_text_style_controls( 'variation_label', esc_html__( 'برچسب متغیر', 'tadris' ), '.webmz-spw-advanced-atc .webmz-spw-variation-label' );
		$this->webmz_register_box_style_controls( 'variation', esc_html__( 'گزینه متغیر', 'tadris' ), '.webmz-spw-advanced-atc .webmz-spw-variation-option' );
		$this->webmz_register_box_style_controls( 'variation_selected', esc_html__( 'گزینه انتخاب‌شده', 'tadris' ), '.webmz-spw-advanced-atc .webmz-spw-variation-option.is-selected' );
		$this->webmz_register_text_style_controls( 'price_amount', esc_html__( 'قیمت', 'tadris' ), '.webmz-spw-advanced-atc__amount' );
		$this->webmz_register_text_style_controls( 'price_regular', esc_html__( 'قیمت قبل تخفیف', 'tadris' ), '.webmz-spw-advanced-atc__regular' );
		$this->webmz_register_text_style_controls( 'price_discount', esc_html__( 'درصد تخفیف', 'tadris' ), '.webmz-spw-advanced-atc__discount' );
		$this->webmz_register_text_style_controls( 'price_currency', esc_html__( 'واحد پول', 'tadris' ), '.webmz-spw-advanced-atc__currency' );
		$this->webmz_register_box_style_controls( 'button', esc_html__( 'دکمه', 'tadris' ), '.webmz-spw-advanced-atc__btn' );
		$this->webmz_register_text_style_controls( 'button_text_style', esc_html__( 'تایپوگرافی دکمه', 'tadris' ), '.webmz-spw-advanced-atc__btn' );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$product = $this->spw_product();

		if ( $this->spw_should_use_demo( $product ) ) {
			$this->spw_render_demo_advanced_add_to_cart( $s );
			return;
		}

		if ( ! $this->spw_is_valid_product( $product ) ) {
			return;
		}

		$price      = $this->spw_get_price_data( $product );
		$show_price = 'yes' === ( $s['show_price'] ?? 'yes' );
		$show_vars  = 'yes' === ( $s['show_variations'] ?? 'yes' ) && $product->is_type( 'variable' );
		?>
		<div
			class="webmz-spw-advanced-atc webmz-spw"
			data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
			data-webmz-spw-sync="1"
			<?php echo $this->spw_cart_scroll_id_attr(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		>
			<div class="webmz-spw-advanced-atc__body">
				<div class="webmz-spw-advanced-atc__main">
					<?php if ( $show_vars ) : ?>
						<?php $this->spw_render_variable_form( $product, 'webmz-spw-variation-form webmz-spw-advanced-atc__variations' ); ?>
					<?php endif; ?>
					<div class="webmz-spw-advanced-atc__action">
						<?php $this->spw_render_add_to_cart_button( $product, $s, 'webmz-spw-advanced-atc__btn webmz-spw-add-to-cart-btn' ); ?>
					</div>
				</div>
				<?php if ( $show_price ) : ?>
					<div
						class="webmz-spw-advanced-atc__price"
						data-webmz-spw-price-display
						data-webmz-spw-base-price="<?php echo esc_attr( wp_json_encode( $price ) ); ?>"
					>
						<?php $this->spw_render_advanced_price_contents( $price ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}

/** 8. Course features repeater (widget settings). */
class SPW_Course_Features_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-course-features'; }
	public function get_title() { return esc_html__( 'ویژگی‌های دوره', 'tadris' ); }
	public function get_icon() { return 'eicon-bullet-list'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_style_depends() { return array( 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'ویژگی‌ها', 'tadris' ) ) );
		$repeater = new Repeater();
		$repeater->add_control( 'icon', array(
			'label'   => esc_html__( 'آیکون', 'tadris' ),
			'type'    => Controls_Manager::ICONS,
			'default' => array( 'value' => 'fas fa-infinity', 'library' => 'fa-solid' ),
		) );
		$repeater->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'دسترسی همیشگی', 'tadris' ),
		) );
		$repeater->add_control( 'subtitle', array(
			'label'   => esc_html__( 'زیرعنوان', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'دسترسی همیشگی به ویدیوها', 'tadris' ),
		) );
		$this->add_control( 'features', array(
			'label'   => esc_html__( 'لیست ویژگی‌ها', 'tadris' ),
			'type'    => Controls_Manager::REPEATER,
			'fields'  => $repeater->get_controls(),
			'default' => array(
				array( 'title' => esc_html__( 'دسترسی همیشگی', 'tadris' ), 'subtitle' => esc_html__( 'دسترسی همیشگی به ویدیوها', 'tadris' ) ),
				array( 'title' => esc_html__( 'دانلود', 'tadris' ), 'subtitle' => esc_html__( 'امکان دانلود ویدیوها', 'tadris' ) ),
				array( 'title' => esc_html__( 'آموزش پروژه محور', 'tadris' ), 'subtitle' => esc_html__( 'یادگیری با تمرین و عملی', 'tadris' ) ),
			),
			'title_field' => '{{{ title }}}',
		) );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'wrapper', esc_html__( 'باکس', 'tadris' ), '.webmz-spw-features', array( 'bordered' => true ) );
		$this->webmz_register_box_style_controls( 'icon_box', esc_html__( 'باکس آیکون', 'tadris' ), '.webmz-spw-features__icon' );
		$this->webmz_register_icon_style_controls( 'icon', esc_html__( 'آیکون', 'tadris' ), '.webmz-spw-features__icon' );
		$this->webmz_register_text_style_controls( 'title', esc_html__( 'عنوان', 'tadris' ), '.webmz-spw-features__title' );
		$this->webmz_register_text_style_controls( 'subtitle', esc_html__( 'زیرعنوان', 'tadris' ), '.webmz-spw-features__subtitle' );
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$items = $s['features'] ?? array();

		if ( empty( $items ) && $this->spw_is_editor_demo() ) {
			$items = array(
				array(
					'title'    => esc_html__( 'دسترسی همیشگی', 'tadris' ),
					'subtitle' => esc_html__( 'دسترسی همیشگی به ویدیوها', 'tadris' ),
					'icon'     => array( 'value' => 'fas fa-infinity', 'library' => 'fa-solid' ),
				),
				array(
					'title'    => esc_html__( 'دانلود', 'tadris' ),
					'subtitle' => esc_html__( 'امکان دانلود ویدیوها', 'tadris' ),
					'icon'     => array( 'value' => 'fas fa-download', 'library' => 'fa-solid' ),
				),
			);
		}

		if ( empty( $items ) ) {
			return;
		}
		?>
		<div class="webmz-spw-features">
			<?php foreach ( $items as $item ) : ?>
				<div class="webmz-spw-features__item">
					<div class="webmz-spw-features__icon">
						<?php Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</div>
					<div class="webmz-spw-features__text">
						<strong class="webmz-spw-features__title"><?php echo esc_html( $item['title'] ?? '' ); ?></strong>
						<span class="webmz-spw-features__subtitle"><?php echo esc_html( $item['subtitle'] ?? '' ); ?></span>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}

/** 9. Purchase box with price, button and feature lines. */
class SPW_Purchase_Box_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-purchase-box'; }
	public function get_title() { return esc_html__( 'کادر خرید دوره', 'tadris' ); }
	public function get_icon() { return 'eicon-cart'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_script_depends() { return array( 'webmz-tadris-widgets', 'webmz-single-product-webmasters', 'webmz-header-commerce', 'webmz-mobile-offcanvas' ); }
	public function get_style_depends() { return array( 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'payment_label', array(
			'label'   => esc_html__( 'برچسب نحوه پرداخت', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'نحوه پرداخت:', 'tadris' ),
		) );
		$this->add_control( 'cash_label', array(
			'label'   => esc_html__( 'برچسب نقدی', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'نقدی', 'tadris' ),
		) );
		$this->add_control( 'total_label', array(
			'label'   => esc_html__( 'برچسب قیمت کل', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'قیمت کل:', 'tadris' ),
		) );
		$this->spw_register_add_to_cart_content_controls();

		$repeater = new Repeater();
		$repeater->add_control( 'icon', array(
			'label'   => esc_html__( 'آیکون', 'tadris' ),
			'type'    => Controls_Manager::ICONS,
			'default' => array( 'value' => 'fas fa-paperclip', 'library' => 'fa-solid' ),
		) );
		$repeater->add_control( 'text', array(
			'label'   => esc_html__( 'متن', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'دسترسی همیشگی به ویدیوها', 'tadris' ),
		) );
		$this->add_control( 'bottom_items', array(
			'label'   => esc_html__( 'موارد زیر دکمه', 'tadris' ),
			'type'    => Controls_Manager::REPEATER,
			'fields'  => $repeater->get_controls(),
			'default' => array(
				array( 'text' => esc_html__( 'دسترسی همیشگی به ویدیوها', 'tadris' ) ),
				array( 'text' => esc_html__( 'امکان دانلود ویدیوها', 'tadris' ) ),
				array( 'text' => esc_html__( 'گواهینامه آکام', 'tadris' ) ),
			),
			'title_field' => '{{{ text }}}',
		) );
		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'box', esc_html__( 'کادر', 'tadris' ), '.webmz-spw-purchase-box', array( 'bordered' => true ) );
		$this->webmz_register_box_style_controls( 'main_price', esc_html__( 'باکس قیمت اصلی', 'tadris' ), '.webmz-spw-purchase-box__main-price' );
		$this->webmz_register_text_style_controls( 'main_price_text', esc_html__( 'متن قیمت اصلی', 'tadris' ), '.webmz-spw-purchase-box__main-price' );
		$this->webmz_register_box_style_controls( 'total_row', esc_html__( 'ردیف قیمت کل', 'tadris' ), '.webmz-spw-purchase-box__total' );
		$this->webmz_register_box_style_controls( 'button', esc_html__( 'دکمه', 'tadris' ), '.webmz-spw-purchase-box__btn' );
		$this->webmz_register_text_style_controls( 'bottom_text', esc_html__( 'متن پایین', 'tadris' ), '.webmz-spw-purchase-box__bottom-item span' );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$product = $this->spw_product();

		if ( $this->spw_should_use_demo( $product ) ) {
			$this->spw_render_demo_purchase_box( $s );
			return;
		}

		if ( ! $this->spw_is_valid_product( $product ) ) {
			return;
		}

		$price = $this->spw_get_price_data( $product );
		?>
		<div class="webmz-spw-purchase-box webmz-spw" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" data-webmz-spw-sync="1"<?php echo $this->spw_cart_scroll_id_attr(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="webmz-spw-purchase-box__payment">
				<span class="webmz-spw-purchase-box__payment-label"><?php echo esc_html( $s['payment_label'] ); ?></span>
				<span class="webmz-spw-purchase-box__payment-cash is-active"><?php echo esc_html( $s['cash_label'] ); ?></span>
			</div>

			<?php if ( $product->is_type( 'variable' ) ) : ?>
				<?php $this->spw_render_variable_form( $product, 'webmz-spw-variation-form webmz-spw-purchase-box__variations' ); ?>
			<?php endif; ?>

			<div class="webmz-spw-purchase-box__main-price" data-webmz-spw-price-display data-webmz-spw-base-price="<?php echo esc_attr( wp_json_encode( $price ) ); ?>">
				<?php $this->spw_render_purchase_price_contents( $price ); ?>
			</div>

			<div class="webmz-spw-purchase-box__total">
				<span><?php echo esc_html( $s['total_label'] ); ?></span>
				<strong data-webmz-spw-price-display data-webmz-spw-base-price="<?php echo esc_attr( wp_json_encode( $price ) ); ?>">
					<?php $this->spw_render_purchase_price_contents( $price ); ?>
				</strong>
			</div>

			<div class="webmz-spw-purchase-box__action">
				<?php $this->spw_render_add_to_cart_button( $product, $s, 'webmz-spw-purchase-box__btn webmz-spw-add-to-cart-btn' ); ?>
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
}

/** 10. Instructor box from post author. */
class SPW_Instructor_Box_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-instructor-box'; }
	public function get_title() { return esc_html__( 'باکس استاد دوره', 'tadris' ); }
	public function get_icon() { return 'eicon-person'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_style_depends() { return array( 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'job_title', array(
			'label'       => esc_html__( 'عنوان شغلی (پیش‌فرض)', 'tadris' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'مدرس دوره', 'tadris' ),
			'description' => esc_html__( 'اگر در پروفایل کاربر فیلد «اطلاعات بیوگرافیک» پر باشد، نام نمایشی و بیو از پروفایل خوانده می‌شود.', 'tadris' ),
		) );
		$this->add_control( 'profile_button', array(
			'label'   => esc_html__( 'متن دکمه پروفایل', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'مشاهده پروفایل استاد', 'tadris' ),
		) );
		$this->add_control( 'avatar_size', array(
			'label'      => esc_html__( 'اندازه آواتار', 'tadris' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 48, 'max' => 160 ) ),
			'default'    => array( 'unit' => 'px', 'size' => 96 ),
			'selectors'  => array( '{{WRAPPER}} .webmz-spw-instructor__avatar img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ),
		) );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'box', esc_html__( 'کادر', 'tadris' ), '.webmz-spw-instructor', array( 'bordered' => true ) );
		$this->webmz_register_text_style_controls( 'name', esc_html__( 'نام', 'tadris' ), '.webmz-spw-instructor__name' );
		$this->webmz_register_text_style_controls( 'role', esc_html__( 'عنوان شغلی', 'tadris' ), '.webmz-spw-instructor__role' );
		$this->webmz_register_text_style_controls( 'bio', esc_html__( 'بیو', 'tadris' ), '.webmz-spw-instructor__bio' );
		$this->webmz_register_box_style_controls( 'button', esc_html__( 'دکمه', 'tadris' ), '.webmz-spw-instructor__btn' );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$product = $this->spw_product();

		if ( $this->spw_should_use_demo( $product ) ) {
			$this->spw_render_demo_instructor( $s );
			return;
		}

		if ( ! $this->spw_is_valid_product( $product ) ) {
			return;
		}

		$author_id   = (int) get_post_field( 'post_author', $product->get_id() );
		$author_name = get_the_author_meta( 'display_name', $author_id );
		$author_bio  = get_the_author_meta( 'description', $author_id );
		$author_url  = get_author_posts_url( $author_id );

		if ( ! $author_name && $this->spw_is_editor_demo() ) {
			$this->spw_render_demo_instructor( $s );
			return;
		}
		?>
		<div class="webmz-spw-instructor">
			<div class="webmz-spw-instructor__avatar"><?php echo get_avatar( $author_id, 192 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<strong class="webmz-spw-instructor__name"><?php echo esc_html( $author_name ); ?></strong>
			<span class="webmz-spw-instructor__role"><?php echo esc_html( $s['job_title'] ); ?></span>
			<?php if ( $author_bio ) : ?>
				<p class="webmz-spw-instructor__bio"><?php echo esc_html( $author_bio ); ?></p>
			<?php endif; ?>
			<?php if ( $author_url ) : ?>
				<a class="webmz-spw-instructor__btn" href="<?php echo esc_url( $author_url ); ?>"><?php echo esc_html( $s['profile_button'] ); ?></a>
			<?php endif; ?>
		</div>
		<?php
	}
}

/** 11. Product content with read more. */
class SPW_Product_Content_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-product-content'; }
	public function get_title() { return esc_html__( 'محتوای محصول', 'tadris' ); }
	public function get_icon() { return 'eicon-post-content'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_script_depends() { return array( 'webmz-single-product-webmasters' ); }
	public function get_style_depends() { return array( 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'enable_read_more', array(
			'label'        => esc_html__( 'فعال‌سازی مشاهده بیشتر', 'tadris' ),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'return_value' => 'yes',
		) );
		$this->add_control( 'collapsed_height', array(
			'label'      => esc_html__( 'ارتفاع بسته (px)', 'tadris' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 100, 'max' => 800 ) ),
			'default'    => array( 'unit' => 'px', 'size' => 320 ),
			'condition'  => array( 'enable_read_more' => 'yes' ),
		) );
		$this->add_control( 'read_more_text', array(
			'label'     => esc_html__( 'متن دکمه', 'tadris' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__( 'مشاهده بیشتر', 'tadris' ),
			'condition' => array( 'enable_read_more' => 'yes' ),
		) );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'content', esc_html__( 'محتوا', 'tadris' ), '.webmz-spw-product-content', array( 'flat' => true ) );
		$this->webmz_register_box_style_controls( 'button', esc_html__( 'دکمه', 'tadris' ), '.webmz-spw-read-more-btn' );
		$this->webmz_register_text_style_controls( 'text', esc_html__( 'متن', 'tadris' ), '.webmz-spw-product-content__inner' );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$product = $this->spw_product();

		if ( $this->spw_should_use_demo( $product ) ) {
			$this->spw_render_demo_product_content( $s );
			return;
		}

		if ( ! $this->spw_is_valid_product( $product ) ) {
			return;
		}

		$post = get_post( $product->get_id() );
		if ( ! $post ) {
			if ( $this->spw_is_editor_demo() ) {
				$this->spw_render_demo_product_content( $s );
			}
			return;
		}

		if ( '' === trim( wp_strip_all_tags( $post->post_content ) ) && $this->spw_is_editor_demo() ) {
			$this->spw_render_demo_product_content( $s );
			return;
		}

		$enable = 'yes' === ( $s['enable_read_more'] ?? 'yes' );
		$height   = isset( $s['collapsed_height']['size'] ) ? absint( $s['collapsed_height']['size'] ) : 320;
		?>
		<div class="webmz-spw-product-content"<?php echo $enable ? ' data-webmz-spw-read-more style="--webmz-spw-content-max:' . esc_attr( $height ) . 'px;"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="webmz-spw-product-content__inner">
				<?php echo apply_filters( 'the_content', $post->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<?php if ( $enable ) : ?>
				<div class="webmz-spw-product-content__fade"></div>
				<div class="webmz-spw-product-content__toggle-wrap">
					<button type="button" class="webmz-spw-read-more-btn" data-webmz-spw-read-more-btn>
						<?php echo esc_html( $s['read_more_text'] ?? esc_html__( 'مشاهده بیشتر', 'tadris' ) ); ?>
					</button>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}

/** 12. FAQ accordion from product metabox. */
class SPW_FAQ_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-faq'; }
	public function get_title() { return esc_html__( 'سوالات متداول', 'tadris' ); }
	public function get_icon() { return 'eicon-accordion'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_script_depends() { return array( 'webmz-single-product-webmasters' ); }
	public function get_style_depends() { return array( 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'empty_message', array(
			'label'   => esc_html__( 'پیام خالی', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'سوالی ثبت نشده است.', 'tadris' ),
		) );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'item', esc_html__( 'آیتم', 'tadris' ), '.webmz-spw-faq__item' );
		$this->webmz_register_text_style_controls( 'question', esc_html__( 'سؤال', 'tadris' ), '.webmz-spw-faq__question-text' );
		$this->webmz_register_text_style_controls( 'answer', esc_html__( 'پاسخ', 'tadris' ), '.webmz-spw-faq__answer' );
		$this->webmz_register_box_style_controls( 'icon', esc_html__( 'آیکون سؤال', 'tadris' ), '.webmz-spw-faq__icon' );
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$product = $this->spw_product();

		if ( $this->spw_should_use_demo( $product ) ) {
			$this->spw_render_demo_faq();
			return;
		}

		if ( ! $this->spw_is_valid_product( $product ) ) {
			return;
		}

		$items = function_exists( 'webmz_spw_get_product_faq_items' )
			? webmz_spw_get_product_faq_items( $product->get_id() )
			: array();

		if ( empty( $items ) ) {
			if ( $this->spw_is_editor_demo() ) {
				$this->spw_render_demo_faq();
			}
			return;
		}
		?>
		<div class="webmz-spw-faq" data-webmz-spw-faq>
			<?php foreach ( $items as $index => $item ) : ?>
				<div class="webmz-spw-faq__item<?php echo 0 === $index ? ' is-open' : ''; ?>">
					<button type="button" class="webmz-spw-faq__question" aria-expanded="<?php echo 0 === $index ? 'true' : 'false'; ?>">
						<span class="webmz-spw-faq__icon">?</span>
						<span class="webmz-spw-faq__question-text"><?php echo esc_html( $item['question'] ); ?></span>
						<span class="webmz-spw-faq__chevron" aria-hidden="true"></span>
					</button>
					<div class="webmz-spw-faq__answer" aria-hidden="<?php echo 0 === $index ? 'false' : 'true'; ?>">
						<div class="webmz-spw-faq__answer-inner">
							<?php echo wp_kses_post( wpautop( $item['answer'] ) ); ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}

/** 13. Course curriculum accordion from product metabox. */
class SPW_Course_Curriculum_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-course-curriculum'; }
	public function get_title() { return esc_html__( 'سرفصل‌های دوره', 'tadris' ); }
	public function get_icon() { return 'eicon-post-list'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_script_depends() { return array( 'webmz-single-product-webmasters' ); }
	public function get_style_depends() { return array( 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'display_only', array(
			'label'        => esc_html__( 'فقط نمایشی', 'tadris' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'بله', 'tadris' ),
			'label_off'    => esc_html__( 'خیر', 'tadris' ),
			'return_value' => 'yes',
			'default'      => '',
			'description'  => esc_html__( 'در حالت نمایشی، حتی پس از خرید دوره لینک مشاهده جلسات قفل‌شده نمایش داده نمی‌شود (مثلاً برای دوره‌های اسپات‌پلیر).', 'tadris' ),
		) );
		$this->add_control( 'session_label', array(
			'label'   => esc_html__( 'برچسب جلسه', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'جلسه', 'tadris' ),
		) );
		$this->add_control( 'free_badge', array(
			'label'   => esc_html__( 'برچسب رایگان', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'رایگان', 'tadris' ),
		) );
		$this->add_control( 'watch_text', array(
			'label'   => esc_html__( 'متن مشاهده ویدیو', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'مشاهده ویدیو', 'tadris' ),
		) );
		$this->add_control( 'register_text', array(
			'label'   => esc_html__( 'متن ثبت‌نام', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'ثبت نام در دوره', 'tadris' ),
		) );
		$this->add_control( 'locked_alert_text', array(
			'label'   => esc_html__( 'پیام جلسات قفل', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'برای مشاهده جلسات قفل باید در دوره ثبت نام نمایید', 'tadris' ),
		) );
		$this->add_control( 'empty_message', array(
			'label'   => esc_html__( 'پیام خالی', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'سرفصلی ثبت نشده است.', 'tadris' ),
		) );
		$this->add_control( 'watch_icon', array(
			'label'   => esc_html__( 'آیکون مشاهده', 'tadris' ),
			'type'    => Controls_Manager::ICONS,
			'default' => array(
				'value'   => 'fas fa-arrow-left',
				'library' => 'fa-solid',
			),
		) );
		$this->add_control( 'register_icon', array(
			'label'   => esc_html__( 'آیکون ثبت‌نام', 'tadris' ),
			'type'    => Controls_Manager::ICONS,
			'default' => array(
				'value'   => 'fas fa-arrow-left',
				'library' => 'fa-solid',
			),
		) );
		$this->end_controls_section();
		$this->webmz_register_box_style_controls( 'section', esc_html__( 'سرفصل', 'tadris' ), '.webmz-spw-curriculum__section' );
		$this->webmz_register_text_style_controls( 'section_title', esc_html__( 'عنوان سرفصل', 'tadris' ), '.webmz-spw-curriculum__section-title' );
		$this->webmz_register_text_style_controls( 'lesson_title', esc_html__( 'عنوان جلسه', 'tadris' ), '.webmz-spw-curriculum__lesson-title' );
	}

	protected function render() {
		$s              = $this->get_settings_for_display();
		$product        = $this->spw_product();
		$display_only   = 'yes' === ( $s['display_only'] ?? '' );
		$session_label  = ! empty( $s['session_label'] ) ? $s['session_label'] : esc_html__( 'جلسه', 'tadris' );
		$free_badge     = ! empty( $s['free_badge'] ) ? $s['free_badge'] : esc_html__( 'رایگان', 'tadris' );
		$watch_text     = ! empty( $s['watch_text'] ) ? $s['watch_text'] : esc_html__( 'مشاهده ویدیو', 'tadris' );
		$register_text   = ! empty( $s['register_text'] ) ? $s['register_text'] : esc_html__( 'ثبت نام در دوره', 'tadris' );
		$locked_alert    = ! empty( $s['locked_alert_text'] ) ? $s['locked_alert_text'] : esc_html__( 'برای مشاهده جلسات قفل باید در دوره ثبت نام نمایید', 'tadris' );
		$watch_icon     = ! empty( $s['watch_icon']['value'] )
			? Icons_Manager::try_get_icon_html( $s['watch_icon'], array( 'aria-hidden' => 'true' ) )
			: '';
		$register_icon  = ! empty( $s['register_icon']['value'] )
			? Icons_Manager::try_get_icon_html( $s['register_icon'], array( 'aria-hidden' => 'true' ) )
			: '';

		if ( $this->spw_should_use_demo( $product ) ) {
			$this->spw_render_demo_curriculum( $s );
			return;
		}

		if ( ! $this->spw_is_valid_product( $product ) ) {
			return;
		}

		$sections = function_exists( 'webmz_spw_get_product_curriculum' )
			? webmz_spw_get_product_curriculum( $product->get_id() )
			: array();

		if ( empty( $sections ) ) {
			if ( $this->spw_is_editor_demo() ) {
				$this->spw_render_demo_curriculum( $s );
			}
			return;
		}

		$this->spw_render_curriculum_markup(
			$sections,
			$product->get_id(),
			array(
				'display_only'    => $display_only,
				'session_label'   => $session_label,
				'free_badge'      => $free_badge,
				'watch_text'      => $watch_text,
				'register_text'    => $register_text,
				'locked_alert'     => $locked_alert,
				'watch_icon'      => $watch_icon,
				'register_icon'   => $register_icon,
			)
		);
	}
}

/** 14. Section heading divider. */
class SPW_Section_Heading_Widget extends Widget_Base {
	use Single_Product_Webmasters_Trait;

	public function get_name() { return 'webmz-spw-section-heading'; }
	public function get_title() { return esc_html__( 'هدینگ بخش (سینگل)', 'tadris' ); }
	public function get_icon() { return 'eicon-heading'; }
	public function get_categories() { return array( $this->spw_category() ); }
	public function get_style_depends() { return array( 'webmz-single-product-webmasters' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );
		$this->add_control( 'title', array(
			'label'   => esc_html__( 'عنوان', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'سوالات متداول', 'tadris' ),
		) );
		$this->webmz_register_title_tag_control( 'title_tag' );
		$this->end_controls_section();
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.webmz-spw-section-heading__title' );
		$this->start_controls_section( 'bar_style', array( 'label' => esc_html__( 'نوار', 'tadris' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'bar_color', array(
			'label'     => esc_html__( 'رنگ نوار', 'tadris' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => array( '{{WRAPPER}} .webmz-spw-section-heading__bar' => 'background-color: {{VALUE}};' ),
		) );
		$this->add_responsive_control( 'bar_width', array(
			'label'      => esc_html__( 'عرض نوار', 'tadris' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 8, 'max' => 80 ) ),
			'default'    => array( 'unit' => 'px', 'size' => 24 ),
			'selectors'  => array( '{{WRAPPER}} .webmz-spw-section-heading__bar' => 'width: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_responsive_control( 'bar_height', array(
			'label'      => esc_html__( 'ارتفاع نوار', 'tadris' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 2, 'max' => 12 ) ),
			'default'    => array( 'unit' => 'px', 'size' => 4 ),
			'selectors'  => array( '{{WRAPPER}} .webmz-spw-section-heading__bar' => 'height: {{SIZE}}{{UNIT}};' ),
		) );
		$this->end_controls_section();
	}

	protected function render() {
		$s         = $this->get_settings_for_display();
		$title_tag = $this->webmz_get_title_tag( $s, 'title_tag' );
		?>
		<div class="webmz-spw-section-heading">
			<span class="webmz-spw-section-heading__bar" aria-hidden="true"></span>
			<<?php echo esc_attr( $title_tag ); ?> class="webmz-spw-section-heading__title"><?php echo esc_html( $s['title'] ); ?></<?php echo esc_attr( $title_tag ); ?>>
		</div>
		<?php
	}
}
