<?php
/**
 * Membership pricing plans widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Repeater-based membership / pricing plans section.
 */
class Membership_Pricing_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-membership-pricing';
	}

	public function get_title() {
		return esc_html__( 'پلن‌های عضویت ویژه', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_keywords() {
		return array( 'pricing', 'membership', 'plan', 'tariff', 'عضویت', 'تعرفه', 'پلن', 'قیمت' );
	}

	public function get_style_depends() {
		return array( 'webmz-membership-pricing' );
	}

	protected function register_controls() {
		$this->register_header_controls();
		$this->register_plans_controls();
		$this->register_layout_controls();
		$this->register_style_controls();
	}

	protected function register_header_controls() {
		$this->start_controls_section(
			'section_header',
			array(
				'label' => esc_html__( 'عنوان بخش', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'زیرعنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'تعرفه‌ها', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'عنوان', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'پلنی مناسب هر هدف و بودجه', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ HTML عنوان', 'tadris' ) );

		$this->add_control(
			'description',
			array(
				'label'   => esc_html__( 'توضیحات', 'tadris' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'بدون قرارداد بلندمدت؛ هر زمان بخواهید می‌توانید پلن خود را تغییر دهید یا لغو کنید.', 'tadris' ),
				'rows'    => 3,
			)
		);

		$this->end_controls_section();
	}

	protected function register_plans_controls() {
		$this->start_controls_section(
			'section_plans',
			array(
				'label' => esc_html__( 'پلن‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$feature_repeater = new Repeater();

		$feature_repeater->add_control(
			'text',
			array(
				'label'       => esc_html__( 'ویژگی', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'دسترسی به یک زبان', 'tadris' ),
				'label_block' => true,
			)
		);

		$plan_repeater = new Repeater();

		$plan_repeater->add_control(
			'plan_name',
			array(
				'label'       => esc_html__( 'نام پلن', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'پایه', 'tadris' ),
				'label_block' => true,
			)
		);

		$plan_repeater->add_control(
			'plan_description',
			array(
				'label'       => esc_html__( 'توضیح کوتاه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'برای شروع یادگیری به‌صورت خودآموز.', 'tadris' ),
				'label_block' => true,
			)
		);

		$plan_repeater->add_control(
			'price',
			array(
				'label'       => esc_html__( 'قیمت', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '۲۹۹,۰۰۰',
				'label_block' => true,
			)
		);

		$plan_repeater->add_control(
			'price_suffix',
			array(
				'label'       => esc_html__( 'پسوند قیمت', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'تومان / ماهانه', 'tadris' ),
				'label_block' => true,
			)
		);

		$plan_repeater->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'متن دکمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'شروع کنید', 'tadris' ),
				'label_block' => true,
			)
		);

		$plan_repeater->add_control(
			'button_link',
			array(
				'label' => esc_html__( 'لینک دکمه', 'tadris' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$plan_repeater->add_control(
			'is_featured',
			array(
				'label'        => esc_html__( 'پلن ویژه', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$plan_repeater->add_control(
			'badge_text',
			array(
				'label'       => esc_html__( 'متن برچسب ویژه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'پیشنهاد ویژه', 'tadris' ),
				'label_block' => true,
				'condition'   => array( 'is_featured' => 'yes' ),
			)
		);

		$plan_repeater->add_control(
			'features',
			array(
				'label'       => esc_html__( 'ویژگی‌ها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $feature_repeater->get_controls(),
				'default'     => array(
					array( 'text' => esc_html__( 'دسترسی به یک زبان', 'tadris' ) ),
					array( 'text' => esc_html__( 'بیش از ۲۰۰ درس ویدیویی', 'tadris' ) ),
					array( 'text' => esc_html__( 'تمرین‌های تعاملی', 'tadris' ) ),
					array( 'text' => esc_html__( 'پشتیبانی ایمیلی', 'tadris' ) ),
				),
				'title_field' => '{{{ text }}}',
			)
		);

		$this->add_control(
			'plans',
			array(
				'label'       => esc_html__( 'لیست پلن‌ها', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $plan_repeater->get_controls(),
				'title_field' => '{{{ plan_name }}}',
				'default'     => array(
					array(
						'plan_name'        => esc_html__( 'پایه', 'tadris' ),
						'plan_description' => esc_html__( 'برای شروع یادگیری به‌صورت خودآموز.', 'tadris' ),
						'price'            => '۲۹۹,۰۰۰',
						'price_suffix'     => esc_html__( 'تومان / ماهانه', 'tadris' ),
						'button_text'      => esc_html__( 'شروع کنید', 'tadris' ),
						'is_featured'      => '',
						'features'         => array(
							array( 'text' => esc_html__( 'دسترسی به یک زبان', 'tadris' ) ),
							array( 'text' => esc_html__( 'بیش از ۲۰۰ درس ویدیویی', 'tadris' ) ),
							array( 'text' => esc_html__( 'تمرین‌های تعاملی', 'tadris' ) ),
							array( 'text' => esc_html__( 'پشتیبانی ایمیلی', 'tadris' ) ),
						),
					),
					array(
						'plan_name'        => esc_html__( 'حرفه‌ای', 'tadris' ),
						'plan_description' => esc_html__( 'محبوب‌ترین پلن برای یادگیری جدی.', 'tadris' ),
						'price'            => '۵۹۹,۰۰۰',
						'price_suffix'     => esc_html__( 'تومان / ماهانه', 'tadris' ),
						'button_text'      => esc_html__( 'انتخاب پلن حرفه‌ای', 'tadris' ),
						'is_featured'      => 'yes',
						'badge_text'       => esc_html__( 'پیشنهاد ویژه', 'tadris' ),
						'features'         => array(
							array( 'text' => esc_html__( 'دسترسی به همه زبان‌ها', 'tadris' ) ),
							array( 'text' => esc_html__( 'کلاس‌های مکالمه زنده گروهی', 'tadris' ) ),
							array( 'text' => esc_html__( 'مسیر یادگیری شخصی', 'tadris' ) ),
							array( 'text' => esc_html__( 'گزارش پیشرفت پیشرفته', 'tadris' ) ),
							array( 'text' => esc_html__( 'گواهی پایان دوره', 'tadris' ) ),
						),
					),
					array(
						'plan_name'        => esc_html__( 'اختصاصی', 'tadris' ),
						'plan_description' => esc_html__( 'آموزش خصوصی و کاملاً شخصی‌سازی‌شده.', 'tadris' ),
						'price'            => '۱,۲۹۰,۰۰۰',
						'price_suffix'     => esc_html__( 'تومان / ماهانه', 'tadris' ),
						'button_text'      => esc_html__( 'مشاوره رایگان', 'tadris' ),
						'is_featured'      => '',
						'features'         => array(
							array( 'text' => esc_html__( 'تمام امکانات پلن حرفه‌ای', 'tadris' ) ),
							array( 'text' => esc_html__( 'جلسات خصوصی با استاد', 'tadris' ) ),
							array( 'text' => esc_html__( 'برنامه درسی اختصاصی', 'tadris' ) ),
							array( 'text' => esc_html__( 'پشتیبانی اولویت‌دار ۲۴ ساعته', 'tadris' ) ),
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'چیدمان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'   => esc_html__( 'تعداد ستون', 'tadris' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors' => array(
					'{{WRAPPER}} .webmz-mp__grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'cards_gap',
			array(
				'label'      => esc_html__( 'فاصله بین کارت‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-mp__grid' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_spacing',
			array(
				'label'      => esc_html__( 'فاصله عنوان تا کارت‌ها', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 120 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 40,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-mp' => '--webmz-mp-header-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'فاصله داخلی کارت', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => 12, 'max' => 64 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 32,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-mp__card' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'گردی گوشه کارت', 'tadris' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .webmz-mp' => '--webmz-mp-card-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->start_controls_section(
			'style_colors',
			array(
				'label' => esc_html__( 'رنگ‌ها', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'accent_color',
			array(
				'label'     => esc_html__( 'رنگ اصلی', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .webmz-mp' => '--webmz-mp-accent: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_bg_color',
			array(
				'label'     => esc_html__( 'پس‌زمینه کارت', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .webmz-mp' => '--webmz-mp-card-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_border_color',
			array(
				'label'     => esc_html__( 'حاشیه کارت', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .webmz-mp' => '--webmz-mp-card-border: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls(
			'style_subtitle',
			esc_html__( 'زیرعنوان بخش', 'tadris' ),
			'.webmz-mp__subtitle',
			'var(--webmz-color-primary, #0878f9)'
		);

		$this->webmz_register_text_style_controls(
			'style_title',
			esc_html__( 'عنوان بخش', 'tadris' ),
			'.webmz-mp__title',
			'var(--text-navy, #023047)'
		);

		$this->webmz_register_text_style_controls(
			'style_description',
			esc_html__( 'توضیحات بخش', 'tadris' ),
			'.webmz-mp__description',
			'var(--text-gray, #585858)'
		);

		$this->start_controls_section(
			'style_plan_name',
			array(
				'label' => esc_html__( 'نام پلن', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'plan_name_typography',
				'selector' => '{{WRAPPER}} .webmz-mp__plan-name',
			)
		);

		$this->add_control(
			'plan_name_color',
			array(
				'label'     => esc_html__( 'رنگ', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-mp__plan-name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_price',
			array(
				'label' => esc_html__( 'قیمت', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_typography',
				'selector' => '{{WRAPPER}} .webmz-mp__price-value',
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => esc_html__( 'رنگ قیمت', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .webmz-mp__price-value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render checkmark icon for feature list items.
	 *
	 * @return void
	 */
	private function render_check_icon() {
		?>
		<span class="webmz-mp__check" aria-hidden="true">
			<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
				<polyline points="20 6 9 17 4 12"></polyline>
			</svg>
		</span>
		<?php
	}

	/**
	 * Render a single plan card.
	 *
	 * @param array<string,mixed> $plan  Plan settings.
	 * @param int                 $index Plan index.
	 * @return void
	 */
	private function render_plan_card( $plan, $index ) {
		$is_featured = ! empty( $plan['is_featured'] ) && 'yes' === $plan['is_featured'];
		$plan_name   = ! empty( $plan['plan_name'] ) ? trim( (string) $plan['plan_name'] ) : '';
		$description = ! empty( $plan['plan_description'] ) ? trim( (string) $plan['plan_description'] ) : '';
		$price       = ! empty( $plan['price'] ) ? trim( (string) $plan['price'] ) : '';
		$suffix      = ! empty( $plan['price_suffix'] ) ? trim( (string) $plan['price_suffix'] ) : '';
		$button_text = ! empty( $plan['button_text'] ) ? trim( (string) $plan['button_text'] ) : '';
		$badge_text  = ! empty( $plan['badge_text'] ) ? trim( (string) $plan['badge_text'] ) : esc_html__( 'پیشنهاد ویژه', 'tadris' );
		$features    = ! empty( $plan['features'] ) && is_array( $plan['features'] ) ? $plan['features'] : array();
		$has_link    = ! empty( $plan['button_link']['url'] );
		$link_key    = 'mp_plan_link_' . absint( $index );

		if ( $has_link ) {
			$this->add_link_attributes( $link_key, $plan['button_link'] );
		}

		$card_classes = array( 'webmz-mp__card' );
		if ( $is_featured ) {
			$card_classes[] = 'webmz-mp__card--featured';
		}
		?>
		<article class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>">
			<?php if ( $is_featured && $badge_text ) : ?>
				<span class="webmz-mp__badge"><?php echo esc_html( $badge_text ); ?></span>
			<?php endif; ?>

			<div class="webmz-mp__card-head">
				<?php if ( $plan_name ) : ?>
					<h3 class="webmz-mp__plan-name"><?php echo esc_html( $plan_name ); ?></h3>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<p class="webmz-mp__plan-desc"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( $price || $suffix ) : ?>
				<div class="webmz-mp__price">
					<?php if ( $price ) : ?>
						<span class="webmz-mp__price-value"><?php echo esc_html( $price ); ?></span>
					<?php endif; ?>
					<?php if ( $suffix ) : ?>
						<span class="webmz-mp__price-suffix"><?php echo esc_html( $suffix ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $button_text ) : ?>
				<div class="webmz-mp__action">
					<?php if ( $has_link ) : ?>
						<a class="webmz-mp__btn<?php echo $is_featured ? ' webmz-mp__btn--primary' : ''; ?>" <?php echo $this->get_render_attribute_string( $link_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php echo esc_html( $button_text ); ?>
						</a>
					<?php else : ?>
						<span class="webmz-mp__btn<?php echo $is_featured ? ' webmz-mp__btn--primary' : ''; ?>">
							<?php echo esc_html( $button_text ); ?>
						</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $features ) ) : ?>
				<ul class="webmz-mp__features">
					<?php foreach ( $features as $feature ) : ?>
						<?php
						$feature_text = ! empty( $feature['text'] ) ? trim( (string) $feature['text'] ) : '';
						if ( '' === $feature_text ) {
							continue;
						}
						?>
						<li class="webmz-mp__feature">
							<?php $this->render_check_icon(); ?>
							<span><?php echo esc_html( $feature_text ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</article>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$plans    = ! empty( $settings['plans'] ) && is_array( $settings['plans'] ) ? $settings['plans'] : array();
		$subtitle = ! empty( $settings['subtitle'] ) ? trim( (string) $settings['subtitle'] ) : '';
		$title    = ! empty( $settings['title'] ) ? trim( (string) $settings['title'] ) : '';
		$desc     = ! empty( $settings['description'] ) ? trim( (string) $settings['description'] ) : '';
		$title_tag = $this->webmz_get_title_tag( $settings, 'title_tag' );

		if ( empty( $plans ) ) {
			return;
		}
		?>
		<div class="webmz-mp">
			<?php if ( $subtitle || $title || $desc ) : ?>
				<header class="webmz-mp__header">
					<?php if ( $subtitle ) : ?>
						<span class="webmz-mp__subtitle"><?php echo esc_html( $subtitle ); ?></span>
					<?php endif; ?>
					<?php if ( $title ) : ?>
						<<?php echo esc_html( $title_tag ); ?> class="webmz-mp__title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $title_tag ); ?>>
					<?php endif; ?>
					<?php if ( $desc ) : ?>
						<p class="webmz-mp__description"><?php echo esc_html( $desc ); ?></p>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<div class="webmz-mp__grid">
				<?php foreach ( $plans as $index => $plan ) : ?>
					<?php $this->render_plan_card( $plan, (int) $index ); ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
