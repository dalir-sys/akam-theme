( function ( blocks, element, blockEditor, components, i18n ) {
	'use strict';

	var el = element.createElement;
	var Fragment = element.Fragment;
	var registerBlockType = blocks.registerBlockType;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;
	var TextareaControl = components.TextareaControl;
	var ToggleControl = components.ToggleControl;
	var __ = i18n.__;

	function RelatedBoxPreview( props ) {
		var attrs = props.attributes;
		var titleBefore = attrs.titleBefore || '';
		var titleHighlight = attrs.titleHighlight || '';
		var titleAfter = attrs.titleAfter || '';
		var description = attrs.description || '';
		var buttonText = attrs.buttonText || __( 'مشاهده مقاله', 'webmz' );
		var buttonUrl = attrs.buttonUrl || '#';

		return el(
			'aside',
			{ className: 'webmz-inline-related', 'aria-label': __( 'مطلب مرتبط', 'webmz' ) },
			el(
				'div',
				{ className: 'webmz-inline-related__inner' },
				el(
					'div',
					{ className: 'webmz-inline-related__content' },
					el(
						'p',
						{ className: 'webmz-inline-related__title' },
						titleBefore,
						titleHighlight
							? el( 'strong', null, titleHighlight )
							: null,
						titleAfter
					),
					description
						? el( 'p', { className: 'webmz-inline-related__desc' }, description )
						: null
				),
				el(
					'a',
					{
						className: 'webmz-inline-related__btn',
						href: buttonUrl || '#',
						onClick: function ( event ) {
							event.preventDefault();
						},
					},
					buttonText
				)
			)
		);
	}

	registerBlockType( 'webmz/related-post', {
		edit: function ( props ) {
			var attrs = props.attributes;
			var setAttributes = props.setAttributes;

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'محتوای باکس', 'webmz' ), initialOpen: true },
						el( TextControl, {
							label: __( 'قبل از عنوان برجسته', 'webmz' ),
							value: attrs.titleBefore,
							onChange: function ( value ) {
								setAttributes( { titleBefore: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'عنوان برجسته', 'webmz' ),
							value: attrs.titleHighlight,
							onChange: function ( value ) {
								setAttributes( { titleHighlight: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'بعد از عنوان برجسته', 'webmz' ),
							value: attrs.titleAfter,
							onChange: function ( value ) {
								setAttributes( { titleAfter: value } );
							},
						} ),
						el( TextareaControl, {
							label: __( 'توضیحات', 'webmz' ),
							value: attrs.description,
							onChange: function ( value ) {
								setAttributes( { description: value } );
							},
							rows: 4,
						} ),
						el( TextControl, {
							label: __( 'متن دکمه', 'webmz' ),
							value: attrs.buttonText,
							onChange: function ( value ) {
								setAttributes( { buttonText: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'لینک دکمه', 'webmz' ),
							value: attrs.buttonUrl,
							onChange: function ( value ) {
								setAttributes( { buttonUrl: value } );
							},
							type: 'url',
							help: __( 'آدرس مقاله، ویدیو یا هر صفحه مرتبط.', 'webmz' ),
						} ),
						el( ToggleControl, {
							label: __( 'باز شدن در تب جدید', 'webmz' ),
							checked: attrs.linkTarget === '_blank',
							onChange: function ( value ) {
								setAttributes( { linkTarget: value ? '_blank' : '' } );
							},
						} )
					)
				),
				el( RelatedBoxPreview, { attributes: attrs } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n );
