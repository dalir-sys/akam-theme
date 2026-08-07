<?php
/**
 * Floating contact button output.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sort contact items by configured order.
 *
 * @param array<int,array<string,mixed>> $items Contact items.
 * @return array<int,array<string,mixed>>
 */
function webmz_sort_floating_contact_items( $items ) {
	usort(
		$items,
		static function ( $a, $b ) {
			$a_order = isset( $a['order'] ) ? absint( $a['order'] ) : 9999;
			$b_order = isset( $b['order'] ) ? absint( $b['order'] ) : 9999;

			return $a_order <=> $b_order;
		}
	);

	return $items;
}

/**
 * Return configured contact items with defaults as fallback.
 *
 * @return array<int,array<string,mixed>>
 */
function webmz_get_floating_contact_items() {
	$items = webmz_get_option( 'floating_contact_items' );

	if ( ! is_array( $items ) || empty( $items ) ) {
		$items = webmz_get_default_floating_contact_items();
	}

	return webmz_sort_floating_contact_items( $items );
}

/**
 * Check if floating contact should be rendered.
 *
 * @return bool
 */
function webmz_floating_contact_is_enabled() {
	return 'yes' === webmz_get_option( 'floating_contact_enabled' );
}

/**
 * Render a compact fallback SVG for a contact item.
 *
 * @param string $icon Icon slug.
 * @return string
 */
function webmz_floating_contact_icon_svg( $icon ) {
	$icon = sanitize_key( $icon );
	$path = 'M12 3a9 9 0 0 0-7.8 13.5L3 21l4.7-1.2A9 9 0 1 0 12 3Zm-3.8 7.2c.1-.7.5-1.3 1.1-1.3h.8c.3 0 .6.2.7.5l.6 1.5c.1.3 0 .6-.2.8l-.5.5c.6 1.1 1.5 2 2.7 2.6l.5-.5c.2-.2.5-.3.8-.2l1.5.6c.3.1.5.4.5.7v.8c0 .6-.6 1-1.3 1.1-3.6.2-7.4-3.6-7.2-7.1Z';

	switch ( $icon ) {
		case 'phone':
			$path = 'M6.6 3.8 8.8 3c.5-.2 1.1.1 1.3.6l1 2.4c.2.4.1.9-.3 1.2L9.6 8.3a11.4 11.4 0 0 0 6.1 6.1l1.1-1.2c.3-.4.8-.5 1.2-.3l2.4 1c.5.2.8.8.6 1.3l-.8 2.2c-.2.5-.6.8-1.1.8C10.7 18.2 5.8 13.3 5.8 4.9c0-.5.3-.9.8-1.1Z';
			break;
		case 'telegram':
			$path = 'M21.5 4.8 18.3 20c-.2.8-.8 1-1.5.6l-4.6-3.4-2.2 2.1c-.2.2-.5.5-1 .5l.4-4.7 8.5-7.7c.4-.3-.1-.5-.5-.2L6.8 13.8 2.3 12.4c-1-.3-1-1 0-1.4L20 4.2c.8-.3 1.6.2 1.5.6Z';
			break;
		case 'instagram':
			$path = 'M8 3h8a5 5 0 0 1 5 5v8a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5V8a5 5 0 0 1 5-5Zm4 5.2a3.8 3.8 0 1 0 0 7.6 3.8 3.8 0 0 0 0-7.6Zm5-1.1a1.1 1.1 0 1 0 0 2.2 1.1 1.1 0 0 0 0-2.2Zm-5 3a1.9 1.9 0 1 1 0 3.8 1.9 1.9 0 0 1 0-3.8Z';
			break;
		case 'support':
			$path = 'M12 3a8 8 0 0 0-8 8v3a3 3 0 0 0 3 3h1v-6H6a6 6 0 0 1 12 0h-2v6h1.7a5 5 0 0 1-4.7 3h-2v-2h2a3 3 0 0 0 3-3v-4a8 8 0 0 0-8-8Z';
			break;
	}

	return '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="' . esc_attr( $path ) . '"/></svg>';
}

/**
 * Build the icon markup for a contact item.
 *
 * @param array<string,mixed> $item Contact item.
 * @return string
 */
function webmz_floating_contact_item_icon_html( $item ) {
	$icon_id = isset( $item['icon_id'] ) ? absint( $item['icon_id'] ) : 0;

	if ( $icon_id ) {
		$image = wp_get_attachment_image( $icon_id, 'thumbnail', false, array( 'alt' => '' ) );

		if ( $image ) {
			return $image;
		}
	}

	return webmz_floating_contact_icon_svg( isset( $item['icon'] ) ? $item['icon'] : 'chat' );
}

/**
 * Render the floating contact widget in the footer.
 *
 * @return void
 */
function webmz_render_floating_contact() {
	if ( is_admin() || ! webmz_floating_contact_is_enabled() ) {
		return;
	}

	$options  = webmz_get_options();
	$items    = array_filter(
		webmz_get_floating_contact_items(),
		static function ( $item ) {
			return is_array( $item ) && isset( $item['enabled'] ) && 'yes' === $item['enabled'];
		}
	);
	$position = isset( $options['floating_contact_position'] ) && 'right' === $options['floating_contact_position'] ? 'right' : 'left';
	$title    = ! empty( $options['floating_contact_panel_title'] ) ? $options['floating_contact_panel_title'] : __( 'پاسخگوی شما هستیم', 'tadris' );
	$desc     = ! empty( $options['floating_contact_panel_desc'] ) ? $options['floating_contact_panel_desc'] : __( 'یکی از راه‌های زیر را برای ارتباط انتخاب کنید', 'tadris' );
	$button_color = ! empty( $options['floating_contact_button_color'] ) ? $options['floating_contact_button_color'] : '#b65a78';
	$button_hover_color = ! empty( $options['floating_contact_button_hover_color'] ) ? $options['floating_contact_button_hover_color'] : '#a54c6b';
	$offset_x = isset( $options['floating_contact_offset_x'] ) ? absint( $options['floating_contact_offset_x'] ) : 24;
	$offset_bottom = isset( $options['floating_contact_offset_bottom'] ) ? absint( $options['floating_contact_offset_bottom'] ) : 24;

	if ( empty( $items ) ) {
		return;
	}
	?>
	<div class="webmz-floating-contact webmz-floating-contact--<?php echo esc_attr( $position ); ?>" style="--webmz-floating-button-color:<?php echo esc_attr( $button_color ); ?>;--webmz-floating-button-hover-color:<?php echo esc_attr( $button_hover_color ); ?>;--webmz-floating-offset-x:<?php echo esc_attr( $offset_x ); ?>px;--webmz-floating-offset-bottom:<?php echo esc_attr( $offset_bottom ); ?>px;">
		<div class="webmz-floating-contact__backdrop" data-webmz-floating-close></div>
		<div class="webmz-floating-contact__panel" id="webmz-floating-contact-panel" role="dialog" aria-modal="false" aria-labelledby="webmz-floating-contact-title">
			<div class="webmz-floating-contact__header">
				<strong id="webmz-floating-contact-title"><?php echo esc_html( $title ); ?></strong>
				<span><?php echo esc_html( $desc ); ?></span>
			</div>
			<div class="webmz-floating-contact__items">
				<?php foreach ( $items as $item ) : ?>
					<?php
					$item_title = isset( $item['title'] ) ? $item['title'] : '';
					$item_url   = isset( $item['url'] ) && '' !== trim( (string) $item['url'] ) ? $item['url'] : '#';
					$item_color = isset( $item['color'] ) && $item['color'] ? $item['color'] : '#2563eb';
					$is_blank   = 0 === strpos( $item_url, 'http' );
					?>
					<a class="webmz-floating-contact__item" href="<?php echo esc_url( $item_url ); ?>" style="--webmz-contact-item-color:<?php echo esc_attr( $item_color ); ?>"<?php echo $is_blank ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
						<span class="webmz-floating-contact__item-icon"><?php echo webmz_floating_contact_item_icon_html( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="webmz-floating-contact__item-title"><?php echo esc_html( $item_title ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
		<button class="webmz-floating-contact__toggle" type="button" aria-expanded="false" aria-controls="webmz-floating-contact-panel" aria-label="<?php esc_attr_e( 'نمایش راه‌های ارتباطی', 'tadris' ); ?>">
			<span class="webmz-floating-contact__toggle-open">
				<?php
				$button_icon_id = isset( $options['floating_contact_button_icon_id'] ) ? absint( $options['floating_contact_button_icon_id'] ) : 0;
				if ( $button_icon_id ) {
					echo wp_get_attachment_image( $button_icon_id, 'thumbnail', false, array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo webmz_floating_contact_icon_svg( 'chat' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</span>
			<span class="webmz-floating-contact__toggle-close" aria-hidden="true">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
			</span>
		</button>
	</div>
	<?php
}
add_action( 'wp_footer', 'webmz_render_floating_contact', 5 );
