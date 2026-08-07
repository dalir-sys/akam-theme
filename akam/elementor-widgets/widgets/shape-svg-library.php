<?php
/**
 * SVG shape library for the Shape Elementor widget.
 *
 * @package WebMZ
 */

namespace WebMZ\Elementor;

defined( 'ABSPATH' ) || exit;

/**
 * Collection of decorative SVG shapes (viewBox 0 0 100 100).
 */
class Shape_SVG_Library {

	/**
	 * Return shape definitions keyed by slug.
	 *
	 * @return array<string, array{label: string, row: int, mode: string, content: string, clip?: string}>
	 */
	public static function get_shapes() {
		static $shapes = null;

		if ( null !== $shapes ) {
			return $shapes;
		}

		$shapes = array(
			// Row 1.
			'rounded_square'       => array(
				'label'   => esc_html__( 'مربع گرد', 'tadris' ),
				'row'     => 1,
				'mode'    => 'stroke',
				'content' => '<rect x="14" y="14" width="72" height="72" rx="20" stroke-width="8"/>',
			),
			'dot_grid'             => array(
				'label'   => esc_html__( 'شبکه نقطه‌ای', 'tadris' ),
				'row'     => 1,
				'mode'    => 'fill',
				'content' => self::dot_grid_markup( 5, 5 ),
			),
			'zigzag'               => array(
				'label'   => esc_html__( 'زیگزاگ', 'tadris' ),
				'row'     => 1,
				'mode'    => 'fill',
				'content' => '<path d="M10 22h8l6-10 6 10h8l6-10 6 10h8l6-10 6 10h8v6h-8l-6-10-6 10h-8l-6-10-6 10h-8l-6-10-6 10h-8v-6zm0 18h8l6-10 6 10h8l6-10 6 10h8l6-10 6 10h8v6h-8l-6-10-6 10h-8l-6-10-6 10h-8l-6-10-6 10h-8v-6zm0 18h8l6-10 6 10h8l6-10 6 10h8l6-10 6 10h8v6h-8l-6-10-6 10h-8l-6-10-6 10h-8l-6-10-6 10h-8v-6zm0 18h8l6-10 6 10h8l6-10 6 10h8l6-10 6 10h8v6h-8l-6-10-6 10h-8l-6-10-6 10h-8l-6-10-6 10h-8v-6z"/>',
			),
			'leaf_square'          => array(
				'label'   => esc_html__( 'مربع برگی', 'tadris' ),
				'row'     => 1,
				'mode'    => 'fill',
				'content' => '<path fill-rule="evenodd" d="M14 14h72v72H14V14zm36 0c-12 0-22 10-22 22v28c12 0 22-10 22-22V14z"/>',
			),
			'four_petal'           => array(
				'label'   => esc_html__( 'چهارپر', 'tadris' ),
				'row'     => 1,
				'mode'    => 'fill',
				'content' => '<path d="M50 18c-8 10-8 22 0 32 8-10 8-22 0-32zm-32 32c10 8 22 8 32 0-10-8-22-8-32 0zm32 32c8-10 8-22 0-32-8 10-8 22 0 32zm32-32c-10 8-22 8-32 0 10-8 22-8 32 0z"/>',
			),
			'double_leaf'          => array(
				'label'   => esc_html__( 'برگ دوتایی', 'tadris' ),
				'row'     => 1,
				'mode'    => 'fill',
				'content' => '<path d="M28 78c0-28 10-48 22-58 6 14 6 30 0 44-8-2-14-2-22 14zm44 0c0-28-10-48-22-58-6 14-6 30 0 44 8-2 14-2 22 14z"/>',
			),
			'square_circle_hybrid' => array(
				'label'   => esc_html__( 'دایره-مربع', 'tadris' ),
				'row'     => 1,
				'mode'    => 'fill',
				'content' => '<path fill-rule="evenodd" d="M50 12a38 38 0 1 1 0 76 38 38 0 0 1 0-76zm0 18a6 6 0 0 0-6 6v16a6 6 0 0 0 6 6h16a6 6 0 0 0 6-6V36a6 6 0 0 0-6-6H50zm-10-4a4 4 0 0 1 4-4h4v8h-4a4 4 0 0 1-4-4zm32 0a4 4 0 0 1-4 4h-4v-8h4a4 4 0 0 1 4 4zm-32 40a4 4 0 0 1 4 4v4h-8v-4a4 4 0 0 1 4-4zm32 0a4 4 0 0 1-4-4v-4h8v4a4 4 0 0 1-4 4z"/>',
			),
			'abstract_geometric'   => array(
				'label'   => esc_html__( 'هندسی انتزاعی', 'tadris' ),
				'row'     => 1,
				'mode'    => 'fill',
				'content' => '<path d="M18 62V38h24v24H18zm40-24h24v24H58V38z"/>',
			),
			'inward_corners'       => array(
				'label'   => esc_html__( 'گوشه‌های داخلی', 'tadris' ),
				'row'     => 1,
				'mode'    => 'fill',
				'content' => '<path fill-rule="evenodd" d="M14 14h72v72H14V14zm8 8v16a8 8 0 0 0 8 8h16v16a8 8 0 0 0 8 8h16V46a8 8 0 0 0-8-8H38V22a8 8 0 0 0-8-8H22z"/>',
			),
			'interlaced_cross'     => array(
				'label'   => esc_html__( 'ضربدر بافته', 'tadris' ),
				'row'     => 1,
				'mode'    => 'fill',
				'content' => '<path d="M14 30h20v16H14V30zm52 0h20v16H66V30zM30 14v20h16V14H30zm0 52v20h16V66H30z"/><path d="M30 30l16 16 4-4 16-16 4 4-16 16 16 16-4 4-16-16-16 16-4-4 16-16-16-16 4-4z"/>',
			),
			'c_shape'              => array(
				'label'   => esc_html__( 'حرف C', 'tadris' ),
				'row'     => 1,
				'mode'    => 'fill',
				'content' => '<path fill-rule="evenodd" d="M14 14h72v72H14V14zm16 16v40h40V30H30z"/>',
			),
			'concentric_squares'   => array(
				'label'   => esc_html__( 'مربع‌های متحدالمرکز', 'tadris' ),
				'row'     => 1,
				'mode'    => 'stroke',
				'content' => '<rect x="12" y="12" width="76" height="76" stroke-width="3"/><rect x="20" y="20" width="60" height="60" stroke-width="3"/><rect x="28" y="28" width="44" height="44" stroke-width="3"/><rect x="36" y="36" width="28" height="28" stroke-width="3"/><rect x="44" y="44" width="12" height="12" stroke-width="3"/>',
			),
			'thick_arc'            => array(
				'label'   => esc_html__( 'قوس ضخیم', 'tadris' ),
				'row'     => 1,
				'mode'    => 'fill',
				'content' => '<path d="M78 22A36 36 0 0 0 22 50h14A22 22 0 0 1 64 28V22z"/>',
			),
			'single_leaf'          => array(
				'label'   => esc_html__( 'برگ ساده', 'tadris' ),
				'row'     => 1,
				'mode'    => 'fill',
				'content' => '<path d="M50 14c-18 16-26 34-26 52 0 10 8 18 18 18s18-8 18-18c0-10-4-20-10-32z"/>',
			),
			'star_in_square'       => array(
				'label'   => esc_html__( 'ستاره در مربع', 'tadris' ),
				'row'     => 1,
				'mode'    => 'fill',
				'content' => '<path fill-rule="evenodd" d="M14 14h72v72H14V14zm36 18l8 16 18 2-13 13 3 18-16-8-16 8 3-18-13-13 18-2 8-16z"/>',
			),

			// Row 2.
			'crescent'             => array(
				'label'   => esc_html__( 'هلال', 'tadris' ),
				'row'     => 2,
				'mode'    => 'fill',
				'content' => '<path fill-rule="evenodd" d="M62 18a32 32 0 1 0 0 64 32 32 0 0 1 0-64z"/>',
			),
			'stacked_ovals'        => array(
				'label'   => esc_html__( 'بیضی‌های روی هم', 'tadris' ),
				'row'     => 2,
				'mode'    => 'fill',
				'content' => '<ellipse cx="50" cy="28" rx="34" ry="12"/><ellipse cx="50" cy="50" rx="34" ry="12"/><ellipse cx="50" cy="72" rx="34" ry="12"/>',
			),
			'swirling_sun'         => array(
				'label'   => esc_html__( 'خورشید چرخشی', 'tadris' ),
				'row'     => 2,
				'mode'    => 'stroke',
				'content' => self::swirl_sun_markup(),
			),
			'double_lens'          => array(
				'label'   => esc_html__( 'عدسی دوتایی', 'tadris' ),
				'row'     => 2,
				'mode'    => 'fill',
				'content' => '<path d="M22 42c10-12 24-12 34 0s24 12 34 0-24 12-34 0-24-12-34 0z"/><path d="M22 58c10-12 24-12 34 0s24 12 34 0-24 12-34 0-24-12-34 0z"/>',
			),
			'rounded_x'            => array(
				'label'   => esc_html__( 'X گرد', 'tadris' ),
				'row'     => 2,
				'mode'    => 'stroke',
				'content' => '<path d="M24 24 L42 42 M58 42 L76 24 M24 76 L42 58 M58 58 L76 76" stroke-linecap="round" stroke-width="10"/>',
			),
			'striped_square'       => array(
				'label'   => esc_html__( 'مربع راه‌راه', 'tadris' ),
				'row'     => 2,
				'mode'    => 'fill',
				'clip'    => 'square',
				'content' => '<rect x="24" y="-8" width="9" height="116" transform="rotate(45 50 50)"/><rect x="39" y="-8" width="9" height="116" transform="rotate(45 50 50)"/><rect x="54" y="-8" width="9" height="116" transform="rotate(45 50 50)"/><rect x="69" y="-8" width="9" height="116" transform="rotate(45 50 50)"/>',
			),
			'eight_petal'          => array(
				'label'   => esc_html__( 'گل هشت‌پر', 'tadris' ),
				'row'     => 2,
				'mode'    => 'fill',
				'content' => self::flower_markup( 8, 16, 14 ),
			),
			'triple_ring_ovals'    => array(
				'label'   => esc_html__( 'سه حلقه بیضی', 'tadris' ),
				'row'     => 2,
				'mode'    => 'stroke',
				'content' => '<ellipse cx="50" cy="28" rx="34" ry="12" stroke-width="4"/><ellipse cx="50" cy="50" rx="34" ry="12" stroke-width="4"/><ellipse cx="50" cy="72" rx="34" ry="12" stroke-width="4"/>',
			),
			'gear_sun'             => array(
				'label'   => esc_html__( 'چرخ‌دنده', 'tadris' ),
				'row'     => 2,
				'mode'    => 'fill',
				'content' => self::gear_sun_markup( 16, 6 ),
			),
			'spiky_star'           => array(
				'label'   => esc_html__( 'ستاره تیغ‌دار', 'tadris' ),
				'row'     => 2,
				'mode'    => 'fill',
				'content' => self::star_markup( 12, 38, 18 ),
			),
			'seven_point_star'     => array(
				'label'   => esc_html__( 'ستاره هفت‌پر', 'tadris' ),
				'row'     => 2,
				'mode'    => 'stroke',
				'content' => self::star_path_markup( 7, 38, 16, true ),
			),
			'six_petal'            => array(
				'label'   => esc_html__( 'گل شش‌پر', 'tadris' ),
				'row'     => 2,
				'mode'    => 'fill',
				'content' => self::flower_markup( 6, 18, 16 ),
			),
			'grid_2x2'             => array(
				'label'   => esc_html__( 'شبکه ۲×۲', 'tadris' ),
				'row'     => 2,
				'mode'    => 'fill',
				'content' => '<rect x="16" y="16" width="30" height="30"/><rect x="54" y="16" width="30" height="30"/><rect x="16" y="54" width="30" height="30"/><rect x="54" y="54" width="30" height="30"/>',
			),
			'diagonal_triangles'   => array(
				'label'   => esc_html__( 'مثلث‌های قطری', 'tadris' ),
				'row'     => 2,
				'mode'    => 'fill',
				'content' => '<polygon points="14,14 86,14 14,86"/><polygon points="86,86 14,86 86,14"/>',
			),
			'u_shape'              => array(
				'label'   => esc_html__( 'شکل U', 'tadris' ),
				'row'     => 2,
				'mode'    => 'fill',
				'content' => '<path fill-rule="evenodd" d="M14 14h72v72H14V14zm8 8v28a26 26 0 0 0 52 0V22H30v28a18 18 0 0 1-36 0V22h8z"/>',
			),

			// Row 3.
			'circle_in_square'     => array(
				'label'   => esc_html__( 'دایره در مربع', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => '<path fill-rule="evenodd" d="M14 14h72v72H14V14zm36 10a26 26 0 1 0 0 52 26 26 0 0 0 0-52z"/>',
			),
			'offset_squares'       => array(
				'label'   => esc_html__( 'مربع‌های آفست', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => '<rect x="18" y="26" width="36" height="36"/><rect x="46" y="38" width="36" height="36"/>',
			),
			'diagonal_arrow'       => array(
				'label'   => esc_html__( 'فلش قطری', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => '<path d="M18 62V18h44v16H42v28H18zm24 4l32 32V50L42 62z"/>',
			),
			'heart'                => array(
				'label'   => esc_html__( 'قلب', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => '<path d="M50 82s-30-18-30-40a18 18 0 0 1 30-12 18 18 0 0 1 30 12c0 22-30 40-30 40z"/>',
			),
			'triple_petal'         => array(
				'label'   => esc_html__( 'سه‌پر', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => self::flower_markup( 3, 20, 18 ),
			),
			'spiky_sun'            => array(
				'label'   => esc_html__( 'خورشید تیغ‌دار', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => self::gear_sun_markup( 20, 8 ),
			),
			'thin_four_star'       => array(
				'label'   => esc_html__( 'ستاره چهارپر نازک', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => '<path d="M50 8l10 32 32 10-32 10-10 32-10-32-32-10 32-10 10-32z"/>',
			),
			'rounded_eight_petal'  => array(
				'label'   => esc_html__( 'گل هشت‌پر گرد', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => self::flower_markup( 8, 14, 18 ),
			),
			'molecular_circle'     => array(
				'label'   => esc_html__( 'دایره مولکولی', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => self::molecular_circle_markup(),
			),
			'double_semicircle'    => array(
				'label'   => esc_html__( 'نیم‌دایره دوتایی', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => '<path d="M18 50a32 32 0 0 1 64 0H18zm0 0a32 32 0 0 0 64 0H18z"/>',
			),
			'curved_four_star'     => array(
				'label'   => esc_html__( 'ستاره چهارپر منحنی', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => '<path d="M50 12c8 14 18 24 32 32-14 8-24 18-32 32-8-14-18-24-32-32 14-8 24-18 32-32z"/>',
			),
			'beaded_ring'          => array(
				'label'   => esc_html__( 'حلقه مهره‌ای', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => self::beaded_ring_markup(),
			),
			'diagonal_quarters'    => array(
				'label'   => esc_html__( 'ربع‌های قطری', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => '<path d="M14 14h40v40H14V14zm36 36h40v40H50V50z"/>',
			),
			'shield_leaf'          => array(
				'label'   => esc_html__( 'برگ سپری', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => '<path d="M50 14c-20 12-28 30-28 48v20h56V62c0-18-8-36-28-48z"/>',
			),
			'bowtie'               => array(
				'label'   => esc_html__( 'پاپیون', 'tadris' ),
				'row'     => 3,
				'mode'    => 'fill',
				'content' => '<polygon points="14,50 42,26 42,74"/><polygon points="86,50 58,26 58,74"/>',
			),

			// Row 4.
			'diagonal_quarters_alt' => array(
				'label'   => esc_html__( 'ربع‌های قطری (۲)', 'tadris' ),
				'row'     => 4,
				'mode'    => 'fill',
				'content' => '<path d="M50 14h36v36H50V14zm-36 36h36v36H14V50z"/>',
			),
			'flower_square'        => array(
				'label'   => esc_html__( 'مربع گلی', 'tadris' ),
				'row'     => 4,
				'mode'    => 'fill',
				'content' => '<path fill-rule="evenodd" d="M50 14c-8 8-8 18 0 26 8-8 18-8 26 0 8-8 18-8 26 0-8 8-8 18 0 26-8 8-18 8-26 0-8-8-18-8-26 0zm0 20a6 6 0 1 0 0 12 6 6 0 0 0 0-12z"/>',
			),
			'line_sun'             => array(
				'label'   => esc_html__( 'خورشید خطی', 'tadris' ),
				'row'     => 4,
				'mode'    => 'stroke',
				'content' => self::line_sun_markup(),
			),
			'textured_ring'        => array(
				'label'   => esc_html__( 'حلقه بافت‌دار', 'tadris' ),
				'row'     => 4,
				'mode'    => 'stroke',
				'content' => self::textured_ring_markup(),
			),
			'linked_ovals'         => array(
				'label'   => esc_html__( 'بیضی‌های زنجیروار', 'tadris' ),
				'row'     => 4,
				'mode'    => 'stroke',
				'content' => '<ellipse cx="22" cy="50" rx="14" ry="28" stroke-width="3"/><ellipse cx="38" cy="50" rx="14" ry="28" stroke-width="3"/><ellipse cx="54" cy="50" rx="14" ry="28" stroke-width="3"/><ellipse cx="70" cy="50" rx="14" ry="28" stroke-width="3"/><ellipse cx="86" cy="50" rx="14" ry="28" stroke-width="3"/>',
			),
			'overlapping_circles'  => array(
				'label'   => esc_html__( 'دایره‌های هم‌پوشان', 'tadris' ),
				'row'     => 4,
				'mode'    => 'stroke',
				'content' => '<circle cx="38" cy="50" r="28" stroke-width="3"/><circle cx="62" cy="50" r="28" stroke-width="3"/>',
			),
			'line_flower'          => array(
				'label'   => esc_html__( 'گل خطی', 'tadris' ),
				'row'     => 4,
				'mode'    => 'stroke',
				'content' => self::radial_lines_markup( 16, 8, 38 ),
			),
			'concentric_arcs'      => array(
				'label'   => esc_html__( 'قوس‌های متحدالمرکز', 'tadris' ),
				'row'     => 4,
				'mode'    => 'stroke',
				'content' => '<path d="M14 72a36 36 0 0 1 72 0" stroke-width="3" fill="none"/><path d="M22 72a28 28 0 0 1 56 0" stroke-width="3" fill="none"/><path d="M30 72a20 20 0 0 1 40 0" stroke-width="3" fill="none"/><path d="M38 72a12 12 0 0 1 24 0" stroke-width="3" fill="none"/><path d="M46 72a4 4 0 0 1 8 0" stroke-width="3" fill="none"/>',
			),
			'dotted_sun'           => array(
				'label'   => esc_html__( 'خورشید نقطه‌ای', 'tadris' ),
				'row'     => 4,
				'mode'    => 'fill',
				'content' => self::dotted_sun_markup(),
			),
			'curved_star_outline'  => array(
				'label'   => esc_html__( 'ستاره منحنی خطی', 'tadris' ),
				'row'     => 4,
				'mode'    => 'stroke',
				'content' => '<path d="M50 14c8 14 18 24 32 32-14 8-24 18-32 32-8-14-18-24-32-32 14-8 24-18 32-32z" stroke-width="3" fill="none"/>',
			),
			'petal_outline'        => array(
				'label'   => esc_html__( 'گل خطی', 'tadris' ),
				'row'     => 4,
				'mode'    => 'stroke',
				'content' => '<path d="M50 16c-6 14-6 28 0 42 6-14 6-28 0-42zm-34 34c14 6 28 6 42 0-14-6-28-6-42 0zm34 34c6-14 6-28 0-42-6 14-6 28 0 42zm34-34c-14-6-28-6-42 0 14 6 28 6 42 0z" stroke-width="3" fill="none"/>',
			),
			'rounded_grid_2x2'     => array(
				'label'   => esc_html__( 'شبکه گرد ۲×۲', 'tadris' ),
				'row'     => 4,
				'mode'    => 'stroke',
				'content' => '<rect x="16" y="16" width="30" height="30" rx="8" stroke-width="3"/><rect x="54" y="16" width="30" height="30" rx="8" stroke-width="3"/><rect x="16" y="54" width="30" height="30" rx="8" stroke-width="3"/><rect x="54" y="54" width="30" height="30" rx="8" stroke-width="3"/>',
			),
			'donut'                => array(
				'label'   => esc_html__( 'دونات', 'tadris' ),
				'row'     => 4,
				'mode'    => 'fill',
				'content' => '<path fill-rule="evenodd" d="M50 14a36 36 0 1 1 0 72 36 36 0 0 1 0-72zm0 16a20 20 0 1 0 0 40 20 20 0 0 0 0-40z"/>',
			),
			'semicircle'           => array(
				'label'   => esc_html__( 'نیم‌دایره', 'tadris' ),
				'row'     => 4,
				'mode'    => 'fill',
				'content' => '<path d="M14 62a36 36 0 0 1 72 0H14z"/>',
			),
			'right_triangle'       => array(
				'label'   => esc_html__( 'مثلث قائم', 'tadris' ),
				'row'     => 4,
				'mode'    => 'fill',
				'content' => '<polygon points="18,82 18,18 82,82"/>',
			),
		);

		return $shapes;
	}

	/**
	 * Return select options for Elementor control.
	 *
	 * @return array<string, string>
	 */
	public static function get_select_options() {
		$options = array();
		$shapes  = self::get_shapes();

		foreach ( $shapes as $slug => $shape ) {
			$options[ $slug ] = sprintf(
				/* translators: 1: row number, 2: shape label */
				esc_html__( 'ردیف %1$d — %2$s', 'tadris' ),
				(int) $shape['row'],
				$shape['label']
			);
		}

		return $options;
	}

	/**
	 * Return inner SVG markup for a shape slug.
	 *
	 * @param string $slug Shape slug.
	 * @return string
	 */
	public static function get_content( $slug ) {
		$shapes = self::get_shapes();

		if ( empty( $shapes[ $slug ]['content'] ) ) {
			return $shapes['rounded_square']['content'];
		}

		return $shapes[ $slug ]['content'];
	}

	/**
	 * Return clip mode for a shape slug.
	 *
	 * @param string $slug Shape slug.
	 * @return string
	 */
	public static function get_clip( $slug ) {
		$shapes = self::get_shapes();

		return ! empty( $shapes[ $slug ]['clip'] ) ? $shapes[ $slug ]['clip'] : '';
	}

	/**
	 * Return render mode for a shape slug.
	 *
	 * @param string $slug Shape slug.
	 * @return string fill|stroke
	 */
	public static function get_mode( $slug ) {
		$shapes = self::get_shapes();

		return ! empty( $shapes[ $slug ]['mode'] ) ? $shapes[ $slug ]['mode'] : 'fill';
	}

	/**
	 * Build a dot-grid markup string.
	 *
	 * @param int $cols Columns.
	 * @param int $rows Rows.
	 * @return string
	 */
	private static function dot_grid_markup( $cols, $rows ) {
		$markup = '';
		$gap    = 80 / max( 1, $cols - 1 );
		$start  = 10.0;
		$r      = 3.2;

		for ( $row = 0; $row < $rows; $row++ ) {
			for ( $col = 0; $col < $cols; $col++ ) {
				$cx      = $start + ( $col * $gap );
				$cy      = $start + ( $row * $gap );
				$markup .= sprintf( '<circle cx="%.2f" cy="%.2f" r="%.2f"/>', $cx, $cy, $r );
			}
		}

		return $markup;
	}

	/**
	 * Build flower petal markup.
	 *
	 * @param int $petals Petal count.
	 * @param float $rx Horizontal radius.
	 * @param float $ry Vertical radius.
	 * @return string
	 */
	private static function flower_markup( $petals, $rx, $ry ) {
		$markup = '';
		$step   = 360 / max( 1, $petals );

		for ( $i = 0; $i < $petals; $i++ ) {
			$angle = deg2rad( $i * $step - 90 );
			$cx    = 50 + ( cos( $angle ) * 18 );
			$cy    = 50 + ( sin( $angle ) * 18 );
			$markup .= sprintf(
				'<ellipse cx="%.2f" cy="%.2f" rx="%.2f" ry="%.2f" transform="rotate(%.2f %.2f %.2f)"/>',
				$cx,
				$cy,
				$rx,
				$ry,
				( $i * $step ),
				$cx,
				$cy
			);
		}

		return $markup;
	}

	/**
	 * Build star polygon path.
	 *
	 * @param int   $points Point count.
	 * @param float $outer  Outer radius.
	 * @param float $inner  Inner radius.
	 * @param bool  $stroke Stroke-only path.
	 * @return string
	 */
	private static function star_path_markup( $points, $outer, $inner, $stroke = false ) {
		$coords = array();
		$step   = M_PI / $points;

		for ( $i = 0; $i < $points * 2; $i++ ) {
			$radius = 0 === $i % 2 ? $outer : $inner;
			$angle  = ( $i * $step ) - ( M_PI / 2 );
			$coords[] = 50 + ( cos( $angle ) * $radius );
			$coords[] = 50 + ( sin( $angle ) * $radius );
		}

		$path = 'M' . $coords[0] . ' ' . $coords[1];
		for ( $i = 2; $i < count( $coords ); $i += 2 ) {
			$path .= ' L' . $coords[ $i ] . ' ' . $coords[ $i + 1 ];
		}
		$path .= ' Z';

		if ( $stroke ) {
			return '<path d="' . esc_attr( $path ) . '" stroke-width="3" fill="none"/>';
		}

		return '<path d="' . esc_attr( $path ) . '"/>';
	}

	/**
	 * Build filled star markup.
	 *
	 * @param int   $points Point count.
	 * @param float $outer  Outer radius.
	 * @param float $inner  Inner radius.
	 * @return string
	 */
	private static function star_markup( $points, $outer, $inner ) {
		return self::star_path_markup( $points, $outer, $inner, false );
	}

	/**
	 * Build gear / spiky sun markup.
	 *
	 * @param int $teeth Tooth count.
	 * @param float $depth Tooth depth.
	 * @return string
	 */
	private static function gear_sun_markup( $teeth, $depth ) {
		$coords = array();
		$outer  = 38;
		$inner  = $outer - $depth;
		$step   = M_PI / $teeth;

		for ( $i = 0; $i < $teeth * 2; $i++ ) {
			$radius = 0 === $i % 2 ? $outer : $inner;
			$angle  = ( $i * $step ) - ( M_PI / 2 );
			$coords[] = 50 + ( cos( $angle ) * $radius );
			$coords[] = 50 + ( sin( $angle ) * $radius );
		}

		$path = 'M' . round( $coords[0], 2 ) . ' ' . round( $coords[1], 2 );
		for ( $i = 2; $i < count( $coords ); $i += 2 ) {
			$path .= ' L' . round( $coords[ $i ], 2 ) . ' ' . round( $coords[ $i + 1 ], 2 );
		}

		return '<path d="' . esc_attr( $path ) . ' Z"/>';
	}

	/**
	 * Build swirling sun rays.
	 *
	 * @return string
	 */
	private static function swirl_sun_markup() {
		$markup = '<circle cx="50" cy="50" r="10"/>';
		$rays   = 12;

		for ( $i = 0; $i < $rays; $i++ ) {
			$angle = deg2rad( $i * ( 360 / $rays ) );
			$x1    = 50 + cos( $angle ) * 14;
			$y1    = 50 + sin( $angle ) * 14;
			$x2    = 50 + cos( $angle + 0.35 ) * 38;
			$y2    = 50 + sin( $angle + 0.35 ) * 38;
			$markup .= sprintf(
				'<path d="M%.2f %.2f Q%.2f %.2f %.2f %.2f" stroke-width="5" fill="none"/>',
				$x1,
				$y1,
				50 + cos( $angle + 0.18 ) * 28,
				50 + sin( $angle + 0.18 ) * 28,
				$x2,
				$y2
			);
		}

		return $markup;
	}

	/**
	 * Build molecular dot circle.
	 *
	 * @return string
	 */
	private static function molecular_circle_markup() {
		$markup = '<circle cx="50" cy="50" r="4"/>';
		$rings  = array(
			array( 'r' => 16, 'count' => 8, 'size' => 3 ),
			array( 'r' => 28, 'count' => 14, 'size' => 2.8 ),
		);

		foreach ( $rings as $ring ) {
			for ( $i = 0; $i < $ring['count']; $i++ ) {
				$angle = deg2rad( ( 360 / $ring['count'] ) * $i );
				$cx    = 50 + cos( $angle ) * $ring['r'];
				$cy    = 50 + sin( $angle ) * $ring['r'];
				$markup .= sprintf( '<circle cx="%.2f" cy="%.2f" r="%.2f"/>', $cx, $cy, $ring['size'] );
			}
		}

		return $markup;
	}

	/**
	 * Build beaded ring markup.
	 *
	 * @return string
	 */
	private static function beaded_ring_markup() {
		$markup = '';
		$count  = 18;
		$radius = 30;
		$size   = 4.5;

		for ( $i = 0; $i < $count; $i++ ) {
			$angle = deg2rad( ( 360 / $count ) * $i );
			$cx    = 50 + cos( $angle ) * $radius;
			$cy    = 50 + sin( $angle ) * $radius;
			$markup .= sprintf( '<circle cx="%.2f" cy="%.2f" r="%.2f"/>', $cx, $cy, $size );
		}

		return $markup;
	}

	/**
	 * Build radial line sun markup.
	 *
	 * @return string
	 */
	private static function line_sun_markup() {
		return self::radial_lines_markup( 20, 4, 36, true );
	}

	/**
	 * Build radial lines.
	 *
	 * @param int   $count Line count.
	 * @param float $width Stroke width.
	 * @param float $radius Line radius.
	 * @param bool  $curved Use curved lines.
	 * @return string
	 */
	private static function radial_lines_markup( $count, $width, $radius, $curved = false ) {
		$markup = '';

		for ( $i = 0; $i < $count; $i++ ) {
			$angle = deg2rad( ( 360 / $count ) * $i );
			$x2    = 50 + cos( $angle ) * $radius;
			$y2    = 50 + sin( $angle ) * $radius;

			if ( $curved ) {
				$cx = 50 + cos( $angle + 0.2 ) * ( $radius * 0.55 );
				$cy = 50 + sin( $angle + 0.2 ) * ( $radius * 0.55 );
				$markup .= sprintf(
					'<path d="M50 50 Q%.2f %.2f %.2f %.2f" stroke-width="%.2f" fill="none"/>',
					$cx,
					$cy,
					$x2,
					$y2,
					$width
				);
			} else {
				$markup .= sprintf(
					'<line x1="50" y1="50" x2="%.2f" y2="%.2f" stroke-width="%.2f"/>',
					$x2,
					$y2,
					$width
				);
			}
		}

		return $markup;
	}

	/**
	 * Build textured ring markup.
	 *
	 * @return string
	 */
	private static function textured_ring_markup() {
		$markup = '';
		$count  = 24;
		$radius = 34;

		for ( $i = 0; $i < $count; $i++ ) {
			$angle = deg2rad( ( 360 / $count ) * $i );
			$x1    = 50 + cos( $angle ) * ( $radius - 6 );
			$y1    = 50 + sin( $angle ) * ( $radius - 6 );
			$x2    = 50 + cos( $angle + 0.12 ) * ( $radius + 4 );
			$y2    = 50 + sin( $angle + 0.12 ) * ( $radius + 4 );
			$markup .= sprintf(
				'<path d="M%.2f %.2f Q%.2f %.2f %.2f %.2f" stroke-width="2.5" fill="none"/>',
				$x1,
				$y1,
				50 + cos( $angle + 0.06 ) * $radius,
				50 + sin( $angle + 0.06 ) * $radius,
				$x2,
				$y2
			);
		}

		return $markup;
	}

	/**
	 * Build dotted sunburst markup.
	 *
	 * @return string
	 */
	private static function dotted_sun_markup() {
		$markup = '';
		$rays   = 12;

		for ( $i = 0; $i < $rays; $i++ ) {
			$angle = deg2rad( ( 360 / $rays ) * $i );
			for ( $j = 1; $j <= 5; $j++ ) {
				$dist = 10 + ( $j * 6 );
				$cx   = 50 + cos( $angle ) * $dist;
				$cy   = 50 + sin( $angle ) * $dist;
				$markup .= sprintf( '<circle cx="%.2f" cy="%.2f" r="2.2"/>', $cx, $cy );
			}
		}

		return $markup;
	}
}
