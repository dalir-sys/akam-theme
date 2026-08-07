<?php
/**
 * Blog CTA widgets: newsletter subscribe and article request.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Shared helpers for blog CTA widgets.
 */
trait WebMZ_Blog_CTA_Widgets_Trait {
	public function get_style_depends() {
		return array( 'webmz-blog-cta-widgets' );
	}

	protected function render_cta_image( $image, $class, $size = 'medium' ) {
		$image = is_array( $image ) ? $image : array();

		if ( ! empty( $image['id'] ) ) {
			echo wp_get_attachment_image( absint( $image['id'] ), $size, false, array( 'class' => $class ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		if ( empty( $image['url'] ) ) {
			return;
		}
		?>
		<img class="<?php echo esc_attr( $class ); ?>" src="<?php echo esc_url( $image['url'] ); ?>" alt="">
		<?php
	}
}

/**
 * Newsletter subscription card widget.
 */
class Newsletter_Subscribe_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;
	use WebMZ_Blog_CTA_Widgets_Trait;

	public function get_name() {
		return 'webmz-newsletter-subscribe';
	}

	public function get_title() {
		return esc_html__( 'عضویت در خبرنامه', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-email-field';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'newsletter', 'email', 'subscribe', 'خبرنامه', 'ایمیل' );
	}

	public function get_script_depends() {
		return array( 'webmz-blog-cta-widgets' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );

		$this->add_control(
			'icon_image',
			array(
				'label' => esc_html__( 'تصویر آیکون', 'tadris' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'عضویت در خبرنامه', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control();

		$this->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'زیرعنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'جدیدترین مقاله‌ها و دوره‌ها را از دست ندهید.', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'email_placeholder',
			array(
				'label'   => esc_html__( 'متن placeholder ایمیل', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'ایمیل خود را وارد کنید', 'tadris' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'متن دکمه', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'عضویت', 'tadris' ),
			)
		);

		$this->add_control(
			'success_message',
			array(
				'label'   => esc_html__( 'پیام موفقیت', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'عضویت شما با موفقیت ثبت شد.', 'tadris' ),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls(
			'box_style',
			esc_html__( 'کارت', 'tadris' ),
			'.webmz-newsletter',
			array(
				'default_background' => '#faf6f6',
			)
		);

		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.webmz-newsletter__title' );
		$this->webmz_register_text_style_controls( 'subtitle_style', esc_html__( 'زیرعنوان', 'tadris' ), '.webmz-newsletter__subtitle' );
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$title_tag  = $this->webmz_get_title_tag( $settings );
		$form_id    = 'webmz-newsletter-' . $this->get_id();
		$nonce      = wp_create_nonce( 'webmz_newsletter_subscribe' );
		$success    = ! empty( $settings['success_message'] ) ? $settings['success_message'] : __( 'عضویت شما با موفقیت ثبت شد.', 'tadris' );
		?>
		<div class="webmz-blog-cta webmz-newsletter" data-success-message="<?php echo esc_attr( $success ); ?>">
			<?php if ( ! empty( $settings['icon_image']['url'] ) || ! empty( $settings['icon_image']['id'] ) ) : ?>
				<div class="webmz-blog-cta__icon webmz-blog-cta__icon--start" aria-hidden="true">
					<?php $this->render_cta_image( $settings['icon_image'], 'webmz-blog-cta__img' ); ?>
				</div>
			<?php endif; ?>

			<div class="webmz-blog-cta__body">
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<<?php echo esc_html( $title_tag ); ?> class="webmz-newsletter__title"><?php echo esc_html( $settings['title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
				<?php endif; ?>

				<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
					<p class="webmz-newsletter__subtitle"><?php echo esc_html( $settings['subtitle'] ); ?></p>
				<?php endif; ?>

				<form class="webmz-newsletter__form" id="<?php echo esc_attr( $form_id ); ?>" novalidate>
					<input type="hidden" name="action" value="webmz_newsletter_subscribe">
					<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">
					<input type="text" class="webmz-newsletter__honeypot" name="webmz_newsletter_website" value="" tabindex="-1" autocomplete="off" aria-hidden="true">
					<div class="webmz-newsletter__field">
						<label class="screen-reader-text" for="<?php echo esc_attr( $form_id ); ?>-email"><?php esc_html_e( 'ایمیل', 'tadris' ); ?></label>
						<input
							type="email"
							id="<?php echo esc_attr( $form_id ); ?>-email"
							class="webmz-newsletter__input"
							name="email"
							placeholder="<?php echo esc_attr( $settings['email_placeholder'] ); ?>"
							required
							autocomplete="email"
						>
						<button type="submit" class="webmz-newsletter__submit">
							<span class="webmz-newsletter__submit-text"><?php echo esc_html( $settings['button_text'] ); ?></span>
							<span class="webmz-newsletter__submit-loading" aria-hidden="true"><?php esc_html_e( '...', 'tadris' ); ?></span>
						</button>
					</div>
					<p class="webmz-newsletter__message" role="status" aria-live="polite" hidden></p>
				</form>
			</div>
		</div>
		<?php
	}
}

/**
 * Article request CTA card widget.
 */
class Article_Request_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;
	use WebMZ_Blog_CTA_Widgets_Trait;

	public function get_name() {
		return 'webmz-article-request';
	}

	public function get_title() {
		return esc_html__( 'درخواست مقاله', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'article', 'request', 'blog', 'مقاله', 'درخواست' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );

		$this->add_control(
			'icon_start',
			array(
				'label' => esc_html__( 'تصویر آیکون راست', 'tadris' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'icon_end',
			array(
				'label' => esc_html__( 'تصویر آیکون چپ', 'tadris' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'به دنبال موضوع خاصی هستید؟', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control();

		$this->add_control(
			'subtitle_prefix',
			array(
				'label'   => esc_html__( 'زیرعنوان (قبل از برجسته)', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'درخواست', 'tadris' ),
			)
		);

		$this->add_control(
			'subtitle_highlight',
			array(
				'label'   => esc_html__( 'متن برجسته زیرعنوان', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'مقاله خود', 'tadris' ),
			)
		);

		$this->add_control(
			'subtitle_suffix',
			array(
				'label'   => esc_html__( 'زیرعنوان (بعد از برجسته)', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'را برای ما ارسال کنید.', 'tadris' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'متن دکمه', 'tadris' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'درخواست مقاله', 'tadris' ),
			)
		);

		$this->add_control(
			'button_url',
			array(
				'label'   => esc_html__( 'لینک دکمه', 'tadris' ),
				'type'    => Controls_Manager::URL,
				'default' => array( 'url' => '#' ),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls(
			'box_style',
			esc_html__( 'کارت', 'tadris' ),
			'.webmz-article-request',
			array(
				'default_background' => '#faf6f6',
			)
		);

		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.webmz-article-request__title' );
		$this->webmz_register_text_style_controls( 'subtitle_style', esc_html__( 'زیرعنوان', 'tadris' ), '.webmz-article-request__subtitle' );

		$this->start_controls_section(
			'highlight_style',
			array(
				'label' => esc_html__( 'متن برجسته', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'highlight_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary, #e11d48)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-article-request__highlight' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_style',
			array(
				'label' => esc_html__( 'دکمه', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => esc_html__( 'رنگ متن', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'var(--webmz-color-primary, #e11d48)',
				'selectors' => array(
					'{{WRAPPER}} .webmz-article-request__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .webmz-article-request__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$title_tag = $this->webmz_get_title_tag( $settings );

		if ( ! empty( $settings['button_url'] ) && is_array( $settings['button_url'] ) ) {
			$this->add_link_attributes( 'button_url', $settings['button_url'] );
		}
		?>
		<div class="webmz-blog-cta webmz-article-request">
			<?php if ( ! empty( $settings['icon_start']['url'] ) || ! empty( $settings['icon_start']['id'] ) ) : ?>
				<div class="webmz-blog-cta__icon webmz-blog-cta__icon--start" aria-hidden="true">
					<?php $this->render_cta_image( $settings['icon_start'], 'webmz-blog-cta__img' ); ?>
				</div>
			<?php endif; ?>

			<div class="webmz-blog-cta__body">
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<<?php echo esc_html( $title_tag ); ?> class="webmz-article-request__title"><?php echo esc_html( $settings['title'] ); ?></<?php echo esc_html( $title_tag ); ?>>
				<?php endif; ?>

				<?php if ( ! empty( $settings['subtitle_prefix'] ) || ! empty( $settings['subtitle_highlight'] ) || ! empty( $settings['subtitle_suffix'] ) ) : ?>
					<p class="webmz-article-request__subtitle">
						<?php if ( ! empty( $settings['subtitle_prefix'] ) ) : ?>
							<span><?php echo esc_html( $settings['subtitle_prefix'] ); ?></span><?php echo ! empty( $settings['subtitle_highlight'] ) ? ' ' : ''; ?>
						<?php endif; ?>
						<?php if ( ! empty( $settings['subtitle_highlight'] ) ) : ?>
							<span class="webmz-article-request__highlight"><?php echo esc_html( $settings['subtitle_highlight'] ); ?></span><?php echo ! empty( $settings['subtitle_suffix'] ) ? ' ' : ''; ?>
						<?php endif; ?>
						<?php if ( ! empty( $settings['subtitle_suffix'] ) ) : ?>
							<span><?php echo esc_html( $settings['subtitle_suffix'] ); ?></span>
						<?php endif; ?>
					</p>
				<?php endif; ?>

				<?php if ( ! empty( $settings['button_text'] ) ) : ?>
					<a class="webmz-article-request__button" <?php echo $this->get_render_attribute_string( 'button_url' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo esc_html( $settings['button_text'] ); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $settings['icon_end']['url'] ) || ! empty( $settings['icon_end']['id'] ) ) : ?>
				<div class="webmz-blog-cta__icon webmz-blog-cta__icon--end" aria-hidden="true">
					<?php $this->render_cta_image( $settings['icon_end'], 'webmz-blog-cta__img' ); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
