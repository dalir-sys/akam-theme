<?php
/**
 * Copyable discount code Elementor widget (works inside popups).
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

class Coupon_Code_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-coupon-code';
	}

	public function get_title() {
		return esc_html__( 'کد تخفیف (قابل کپی)', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-price-list';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'coupon', 'discount', 'code', 'copy', 'کد تخفیف', 'کوپن', 'تخفیف' );
	}

	public function get_script_depends() {
		return array( 'webmz-coupon-code' );
	}

	public function get_style_depends() {
		return array( 'webmz-coupon-code' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'محتوا', 'tadris' ) ) );

		$this->add_control( 'title', array(
			'label'       => esc_html__( 'عنوان', 'tadris' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( '۲۰٪ تخفیف ویژه برای شما', 'tadris' ),
			'label_block' => true,
		) );
		$this->webmz_register_title_tag_control( 'title_tag' );
		$this->add_control( 'description', array(
			'label'   => esc_html__( 'توضیحات', 'tadris' ),
			'type'    => Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'کد زیر را کپی کنید و هنگام پرداخت در سبد خرید وارد کنید.', 'tadris' ),
		) );
		$this->add_control( 'code', array(
			'label'       => esc_html__( 'کد تخفیف', 'tadris' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => 'AKAM20',
			'label_block' => true,
			'dynamic'     => array( 'active' => true ),
		) );
		$this->add_control( 'copy_text', array(
			'label'   => esc_html__( 'متن دکمه کپی', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'کپی کد', 'tadris' ),
		) );
		$this->add_control( 'copied_text', array(
			'label'   => esc_html__( 'متن پس از کپی', 'tadris' ),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__( 'کپی شد!', 'tadris' ),
		) );
		$this->add_control( 'copy_icon', array(
			'label'   => esc_html__( 'آیکون دکمه کپی', 'tadris' ),
			'type'    => Controls_Manager::ICONS,
			'default' => array( 'value' => 'far fa-copy', 'library' => 'fa-regular' ),
		) );
		$this->add_control( 'note', array(
			'label'       => esc_html__( 'یادداشت پایین (اختیاری)', 'tadris' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'فقط تا پایان این هفته معتبر است.', 'tadris' ),
			'label_block' => true,
		) );
		$this->add_control( 'cta_text', array(
			'label'     => esc_html__( 'متن دکمه اقدام (اختیاری)', 'tadris' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => '',
			'separator' => 'before',
		) );
		$this->add_control( 'cta_link', array(
			'label'     => esc_html__( 'لینک دکمه اقدام', 'tadris' ),
			'type'      => Controls_Manager::URL,
			'dynamic'   => array( 'active' => true ),
			'condition' => array( 'cta_text!' => '' ),
		) );
		$this->add_responsive_control( 'align', array(
			'label'     => esc_html__( 'چینش', 'tadris' ),
			'type'      => Controls_Manager::CHOOSE,
			'default'   => 'center',
			'options'   => array(
				'right'  => array( 'title' => esc_html__( 'راست', 'tadris' ), 'icon' => 'eicon-text-align-right' ),
				'center' => array( 'title' => esc_html__( 'وسط', 'tadris' ), 'icon' => 'eicon-text-align-center' ),
				'left'   => array( 'title' => esc_html__( 'چپ', 'tadris' ), 'icon' => 'eicon-text-align-left' ),
			),
			'selectors' => array( '{{WRAPPER}} .webmz-coupon' => 'text-align: {{VALUE}};' ),
		) );

		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'box', esc_html__( 'کادر', 'tadris' ), '.webmz-coupon', array( 'bordered' => true ) );
		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.webmz-coupon__title' );
		$this->webmz_register_text_style_controls( 'description_style', esc_html__( 'توضیحات', 'tadris' ), '.webmz-coupon__description' );
		$this->webmz_register_box_style_controls( 'code_box', esc_html__( 'کادر کد', 'tadris' ), '.webmz-coupon__code-box' );
		$this->webmz_register_text_style_controls( 'code_style', esc_html__( 'متن کد', 'tadris' ), '.webmz-coupon__code' );
		$this->webmz_register_box_style_controls( 'copy_button', esc_html__( 'دکمه کپی', 'tadris' ), '.webmz-coupon__copy' );
		$this->webmz_register_text_style_controls( 'copy_button_text', esc_html__( 'تایپوگرافی دکمه کپی', 'tadris' ), '.webmz-coupon__copy' );
		$this->webmz_register_text_style_controls( 'note_style', esc_html__( 'یادداشت', 'tadris' ), '.webmz-coupon__note' );
		$this->webmz_register_box_style_controls( 'cta_button', esc_html__( 'دکمه اقدام', 'tadris' ), '.webmz-coupon__cta' );
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$code = trim( (string) ( $s['code'] ?? '' ) );

		if ( '' === $code ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \webmz_is_layout_editing_context() ) {
				echo '<div class="webmz-editor-placeholder">' . esc_html__( 'کد تخفیف را وارد کنید.', 'tadris' ) . '</div>';
			}
			return;
		}

		$title_tag   = $this->webmz_get_title_tag( $s, 'title_tag' );
		$copy_text   = ! empty( $s['copy_text'] ) ? $s['copy_text'] : esc_html__( 'کپی کد', 'tadris' );
		$copied_text = ! empty( $s['copied_text'] ) ? $s['copied_text'] : esc_html__( 'کپی شد!', 'tadris' );

		if ( ! empty( $s['cta_text'] ) && ! empty( $s['cta_link']['url'] ) ) {
			$this->add_link_attributes( 'cta', $s['cta_link'] );
			$this->add_render_attribute( 'cta', 'class', 'webmz-coupon__cta' );
		}
		?>
		<div class="webmz-coupon" data-webmz-coupon>
			<?php if ( ! empty( $s['title'] ) ) : ?>
				<<?php echo esc_attr( $title_tag ); ?> class="webmz-coupon__title"><?php echo esc_html( $s['title'] ); ?></<?php echo esc_attr( $title_tag ); ?>>
			<?php endif; ?>

			<?php if ( ! empty( $s['description'] ) ) : ?>
				<p class="webmz-coupon__description"><?php echo esc_html( $s['description'] ); ?></p>
			<?php endif; ?>

			<div class="webmz-coupon__code-box">
				<code class="webmz-coupon__code" dir="ltr"><?php echo esc_html( $code ); ?></code>
				<button
					type="button"
					class="webmz-coupon__copy"
					data-webmz-coupon-copy="<?php echo esc_attr( $code ); ?>"
					data-copied-text="<?php echo esc_attr( $copied_text ); ?>"
					aria-live="polite"
				>
					<?php if ( ! empty( $s['copy_icon']['value'] ) ) : ?>
						<span class="webmz-coupon__copy-icon" aria-hidden="true"><?php Icons_Manager::render_icon( $s['copy_icon'], array( 'aria-hidden' => 'true' ) ); ?></span>
					<?php endif; ?>
					<span class="webmz-coupon__copy-label"><?php echo esc_html( $copy_text ); ?></span>
				</button>
			</div>

			<?php if ( ! empty( $s['note'] ) ) : ?>
				<p class="webmz-coupon__note"><?php echo esc_html( $s['note'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $s['cta_text'] ) && ! empty( $s['cta_link']['url'] ) ) : ?>
				<a <?php $this->print_render_attribute_string( 'cta' ); ?>><?php echo esc_html( $s['cta_text'] ); ?></a>
			<?php endif; ?>
		</div>
		<?php
	}
}
