<?php
/**
 * Heading with typing animation on a rotating accent word.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Animated heading widget with typewriter effect on one rotating word.
 */
class Tadris_Heading_Animation_Widget extends Widget_Base {
	use Tadris_Widget_Controls_Trait;

	public function get_name() {
		return 'webmz-tadris-heading-animation';
	}

	public function get_title() {
		return esc_html__( 'هدینگ انیمیشن', 'tadris' );
	}

	public function get_icon() {
		return 'eicon-animation-text';
	}

	public function get_categories() {
		return array( 'webmz-widgets' );
	}

	public function get_style_depends() {
		return array( 'webmz-tadris-heading-animation' );
	}

	public function get_script_depends() {
		return array( 'webmz-tadris-heading-animation' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array(
				'label' => esc_html__( 'محتوا', 'tadris' ),
			)
		);

		$this->add_control(
			'prefix',
			array(
				'label'       => esc_html__( 'متن قبل از کلمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'یادگیری ', 'tadris' ),
				'label_block' => true,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'word',
			array(
				'label'       => esc_html__( 'کلمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'وردپرس', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'words_list',
			array(
				'label'       => esc_html__( 'کلمات متغیر', 'tadris' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ word }}}',
				'default'     => array(
					array( 'word' => esc_html__( 'وردپرس', 'tadris' ) ),
					array( 'word' => esc_html__( 'طراحی وب', 'tadris' ) ),
					array( 'word' => esc_html__( 'برنامه‌نویسی', 'tadris' ) ),
					array( 'word' => esc_html__( 'سئو', 'tadris' ) ),
				),
			)
		);

		$this->add_control(
			'suffix',
			array(
				'label'       => esc_html__( 'متن بعد از کلمه', 'tadris' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( ' با ما شروع کنید', 'tadris' ),
				'label_block' => true,
			)
		);

		$this->webmz_register_title_tag_control( 'title_tag', esc_html__( 'تگ HTML عنوان', 'tadris' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'animation',
			array(
				'label' => esc_html__( 'انیمیشن', 'tadris' ),
			)
		);

		$this->add_control(
			'type_speed',
			array(
				'label'   => esc_html__( 'سرعت تایپ (میلی‌ثانیه)', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 80,
				'min'     => 20,
				'max'     => 500,
			)
		);

		$this->add_control(
			'delete_speed',
			array(
				'label'   => esc_html__( 'سرعت پاک‌کردن (میلی‌ثانیه)', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 40,
				'min'     => 10,
				'max'     => 300,
			)
		);

		$this->add_control(
			'pause',
			array(
				'label'   => esc_html__( 'مکث بین کلمات (میلی‌ثانیه)', 'tadris' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 2000,
				'min'     => 500,
				'max'     => 10000,
			)
		);

		$this->add_control(
			'show_cursor',
			array(
				'label'        => esc_html__( 'نمایش مکان‌نما', 'tadris' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();

		$this->webmz_register_box_style_controls( 'box_style', esc_html__( 'باکس', 'tadris' ), '.tadris-heading-animation' );

		$this->start_controls_section(
			'layout_style',
			array(
				'label' => esc_html__( 'چیدمان', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_align',
			array(
				'label'     => esc_html__( 'چینش متن', 'tadris' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'right'  => array(
						'title' => esc_html__( 'راست', 'tadris' ),
						'icon'  => 'eicon-text-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'وسط', 'tadris' ),
						'icon'  => 'eicon-text-align-center',
					),
					'left'   => array(
						'title' => esc_html__( 'چپ', 'tadris' ),
						'icon'  => 'eicon-text-align-left',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .tadris-heading-animation' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->webmz_register_text_style_controls( 'title_style', esc_html__( 'عنوان', 'tadris' ), '.tadris-heading-animation__title' );
		$this->webmz_register_text_style_controls( 'prefix_style', esc_html__( 'متن قبل', 'tadris' ), '.tadris-heading-animation__prefix' );
		$this->webmz_register_text_style_controls( 'suffix_style', esc_html__( 'متن بعد', 'tadris' ), '.tadris-heading-animation__suffix' );

		$this->start_controls_section(
			'typed_word_style',
			array(
				'label' => esc_html__( 'کلمه انیمیشنی', 'tadris' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'typed_color',
			array(
				'label'     => esc_html__( 'رنگ کلمه', 'tadris' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f21e3f',
				'selectors' => array(
					'{{WRAPPER}} .tadris-heading-animation__typed' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tadris-heading-animation__cursor' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typed_typography',
				'selector' => '{{WRAPPER}} .tadris-heading-animation__typed',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Collect non-empty words from repeater settings.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return string[]
	 */
	protected function get_words( $settings ) {
		$words = array();

		if ( empty( $settings['words_list'] ) || ! is_array( $settings['words_list'] ) ) {
			return $words;
		}

		foreach ( $settings['words_list'] as $item ) {
			$word = isset( $item['word'] ) ? trim( (string) $item['word'] ) : '';

			if ( '' !== $word ) {
				$words[] = $word;
			}
		}

		return $words;
	}

	protected function render() {
		$s         = $this->get_settings_for_display();
		$words     = $this->get_words( $s );
		$title_tag = $this->webmz_get_title_tag( $s );

		if ( empty( $words ) ) {
			$words = array( esc_html__( 'وردپرس', 'tadris' ) );
		}
		?>
		<div class="tadris-heading-animation"
			data-words="<?php echo esc_attr( wp_json_encode( $words, JSON_UNESCAPED_UNICODE ) ); ?>"
			data-type-speed="<?php echo esc_attr( max( 20, (int) $s['type_speed'] ) ); ?>"
			data-delete-speed="<?php echo esc_attr( max( 10, (int) $s['delete_speed'] ) ); ?>"
			data-pause="<?php echo esc_attr( max( 500, (int) $s['pause'] ) ); ?>"
			data-show-cursor="<?php echo 'yes' === $s['show_cursor'] ? 'yes' : 'no'; ?>">
			<<?php echo esc_html( $title_tag ); ?> class="tadris-heading-animation__title">
				<?php if ( ! empty( $s['prefix'] ) ) : ?>
					<span class="tadris-heading-animation__prefix"><?php echo esc_html( $s['prefix'] ); ?></span>
				<?php endif; ?>

				<span class="tadris-heading-animation__typed-line">
					<span class="tadris-heading-animation__typed" aria-live="polite"></span>
					<?php if ( 'yes' === $s['show_cursor'] ) : ?>
						<span class="tadris-heading-animation__cursor" aria-hidden="true">|</span>
					<?php endif; ?>
				</span>

				<?php if ( ! empty( $s['suffix'] ) ) : ?>
					<span class="tadris-heading-animation__suffix"><?php echo esc_html( $s['suffix'] ); ?></span>
				<?php endif; ?>
			</<?php echo esc_html( $title_tag ); ?>>
		</div>
		<?php
	}
}
