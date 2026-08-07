( function ( $ ) {
	'use strict';

	var i18n = window.webmzStoriesAdmin || {};

	function getNextIndex( $container ) {
		var max = -1;

		$container.find( '[data-webmz-story-item]' ).each( function () {
			var $inputs = $( this ).find( 'input[name^="webmz_story_items["], select[name^="webmz_story_items["]' );
			if ( ! $inputs.length ) {
				return;
			}

			var match = $inputs.first().attr( 'name' ).match( /webmz_story_items\[(\d+)\]/ );
			if ( match ) {
				max = Math.max( max, parseInt( match[ 1 ], 10 ) );
			}
		} );

		return max + 1;
	}

	function reindexStoryItems( $container ) {
		$container.find( '[data-webmz-story-item]' ).each( function ( index ) {
			$( this ).find( '[name^="webmz_story_items["]' ).each( function () {
				var name = $( this ).attr( 'name' );
				if ( ! name ) {
					return;
				}
				$( this ).attr( 'name', name.replace( /webmz_story_items\[\d+\]/, 'webmz_story_items[' + index + ']' ) );
			} );
		} );
	}

	function updateSummary( $item ) {
		var value = $.trim( $item.find( '[data-webmz-story-summary-source]' ).val() || '' );
		$item.find( '.webmz-story-item__summary' ).text( value || i18n.itemLabel || 'آیتم استوری' );
	}

	function bindStoryItemEvents( $scope ) {
		$scope.find( '[data-webmz-story-item-remove]' ).off( 'click.webmzStory' ).on( 'click.webmzStory', function ( event ) {
			event.preventDefault();
			var $container = $( this ).closest( '[data-webmz-story-items]' );
			var $items = $container.find( '[data-webmz-story-item]' );

			if ( $items.length <= 1 ) {
				$( this ).closest( '[data-webmz-story-item]' ).find( 'input[type="text"], input[type="url"], input[type="number"], input[type="hidden"]' ).val( '' );
				$( this ).closest( '[data-webmz-story-item]' ).find( 'input[type="checkbox"]' ).prop( 'checked', false );
				$( this ).closest( '[data-webmz-story-item]' ).find( '.webmz-story-item__media-preview' ).html( '<span class="description">انتخاب نشده</span>' );
				updateSummary( $( this ).closest( '[data-webmz-story-item]' ) );
				return;
			}

			$( this ).closest( '[data-webmz-story-item]' ).remove();
			reindexStoryItems( $container );
		} );

		$scope.find( '[data-webmz-story-item-duplicate]' ).off( 'click.webmzStory' ).on( 'click.webmzStory', function ( event ) {
			event.preventDefault();
			var $container = $( this ).closest( '[data-webmz-story-items]' );
			var $clone = $( this ).closest( '[data-webmz-story-item]' ).clone( false );
			var index = getNextIndex( $container );

			$clone.find( '[name^="webmz_story_items["]' ).each( function () {
				var name = $( this ).attr( 'name' );
				if ( name ) {
					$( this ).attr( 'name', name.replace( /webmz_story_items\[\d+\]/, 'webmz_story_items[' + index + ']' ) );
				}
			} );

			$container.append( $clone );
			bindStoryItemEvents( $clone );
			updateSummary( $clone );
		} );

		$scope.find( '[data-webmz-story-item-toggle]' ).off( 'click.webmzStory' ).on( 'click.webmzStory', function ( event ) {
			event.preventDefault();
			var $item = $( this ).closest( '[data-webmz-story-item]' );
			$item.toggleClass( 'is-collapsed' );
			$( this ).attr( 'aria-expanded', $item.hasClass( 'is-collapsed' ) ? 'false' : 'true' );
		} );

		$scope.find( '[data-webmz-story-summary-source]' ).off( 'input.webmzStory' ).on( 'input.webmzStory', function () {
			updateSummary( $( this ).closest( '[data-webmz-story-item]' ) );
		} );

		$scope.find( '[data-webmz-story-media-select]' ).off( 'click.webmzStory' ).on( 'click.webmzStory', function ( event ) {
			event.preventDefault();

			if ( ! wp || ! wp.media ) {
				return;
			}

			var $item = $( this ).closest( '[data-webmz-story-item]' );
			var frame = wp.media( {
				title: i18n.mediaTitle || 'انتخاب رسانه',
				button: { text: i18n.mediaButton || 'استفاده' },
				library: { type: [ 'image', 'video' ] },
				multiple: false
			} );

			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				var type = attachment.type === 'video' ? 'video' : 'image';
				var preview = '';

				if ( type === 'video' ) {
					preview = '<video src="' + attachment.url + '" muted playsinline></video>';
				} else {
					preview = '<img src="' + attachment.url + '" alt="">';
				}

				$item.find( '[data-webmz-story-media-id]' ).val( attachment.id || '' );
				$item.find( '[data-webmz-story-media-url]' ).val( attachment.url || '' );
				$item.find( '[data-webmz-story-media-type]' ).val( type );
				$item.find( '.webmz-story-item__media-preview' ).html( preview );
				$item.find( '[data-webmz-story-media-remove]' ).prop( 'hidden', false );
				updateStoryItemVideoFields( $item );
			} );

			frame.open();
		} );

		$scope.find( '[data-webmz-story-media-remove]' ).off( 'click.webmzStory' ).on( 'click.webmzStory', function ( event ) {
			event.preventDefault();
			var $item = $( this ).closest( '[data-webmz-story-item]' );
			$item.find( '[data-webmz-story-media-id], [data-webmz-story-media-url]' ).val( '' );
			$item.find( '[data-webmz-story-media-type]' ).val( 'image' );
			$item.find( '.webmz-story-item__media-preview' ).html( '<span class="description">انتخاب نشده</span>' );
			$( this ).prop( 'hidden', true );
			updateStoryItemVideoFields( $item );
		} );

		bindStoryItemVideoSync( $scope );
	}

	function updateStoryItemVideoFields( $item ) {
		var type = $item.find( '[data-webmz-story-media-type]' ).val();
		var isVideo = type === 'video';
		var $wrap = $item.find( '[data-webmz-story-sync-wrap]' );
		var $sync = $item.find( '[data-webmz-story-sync-video]' );
		var $duration = $item.find( '[data-webmz-story-duration-input]' );

		if ( isVideo ) {
			$wrap.prop( 'hidden', false );
			$sync.prop( 'disabled', false );
		} else {
			$wrap.prop( 'hidden', true );
			$sync.prop( 'checked', false ).prop( 'disabled', true );
			$duration.prop( 'disabled', false );
		}

		$duration.prop( 'disabled', isVideo && $sync.is( ':checked' ) );
	}

	function bindStoryItemVideoSync( $scope ) {
		$scope.find( '[data-webmz-story-sync-video]' ).off( 'change.webmzStory' ).on( 'change.webmzStory', function () {
			var $item = $( this ).closest( '[data-webmz-story-item]' );
			$item.find( '[data-webmz-story-duration-input]' ).prop( 'disabled', $( this ).is( ':checked' ) );
		} );
	}

	function initStoryItemsMetabox() {
		var $metabox = $( '[data-webmz-story-items-metabox]' );
		if ( ! $metabox.length ) {
			return;
		}

		var $container = $metabox.find( '[data-webmz-story-items]' );

		$container.sortable( {
			handle: '.webmz-story-item__drag',
			items: '[data-webmz-story-item]',
			axis: 'y',
			tolerance: 'pointer',
			update: function () {
				reindexStoryItems( $container );
			}
		} );

		bindStoryItemEvents( $metabox );
		bindStoryItemVideoSync( $metabox );
		$metabox.find( '[data-webmz-story-item]' ).each( function () {
			updateStoryItemVideoFields( $( this ) );
		} );

		$metabox.on( 'click', '[data-webmz-story-item-add]', function ( event ) {
			event.preventDefault();
			var template = $( '#tmpl-webmz-story-item-row' ).html();
			if ( ! template ) {
				return;
			}

			var index = getNextIndex( $container );
			var html = template.replace( /__INDEX__/g, String( index ) );
			var $row = $( html );

			$container.append( $row );
			bindStoryItemEvents( $row );
			bindStoryItemVideoSync( $row );
		} );
	}

	function initStoryBoxMetabox() {
		var $metabox = $( '[data-webmz-story-box-metabox]' );
		if ( ! $metabox.length ) {
			return;
		}

		$metabox.on( 'change', 'input[name="webmz_story_box_stories_source"]', function () {
			var source = $( this ).val();
			$metabox.find( '[data-webmz-story-source-panel]' ).prop( 'hidden', true );
			$metabox.find( '[data-webmz-story-source-panel="' + source + '"]' ).prop( 'hidden', false );
		} );

		$metabox.find( '[data-webmz-story-box-order]' ).each( function () {
			initStoryBoxOrderPicker( $( this ) );
		} );
	}

	function initStoryBoxOrderPicker( $picker ) {
		var $list = $picker.find( '[data-webmz-story-order-list]' );
		var $select = $picker.find( '[data-webmz-story-order-select]' );
		var $template = $picker.find( '.webmz-story-box-order-template' );
		var fieldName = $template.attr( 'data-field-name' ) || 'webmz_story_box_story_ids[]';

		$list.sortable( {
			handle: '.webmz-story-box-order__drag',
			axis: 'y',
			placeholder: 'webmz-story-box-order__placeholder'
		} );

		$picker.on( 'click', '[data-webmz-story-order-add]', function ( event ) {
			event.preventDefault();

			var id = $select.val();
			var title = $.trim( $select.find( 'option:selected' ).text() );

			if ( ! id ) {
				return;
			}

			if ( $list.find( '[data-id="' + id + '"]' ).length ) {
				return;
			}

			var html = $template.html()
				.replace( /__ID__/g, id )
				.replace( /__TITLE__/g, title );

			$list.append( html );
			$select.find( 'option[value="' + id + '"]' ).prop( 'hidden', true ).prop( 'selected', false );
			$select.val( '' );
		} );

		$picker.on( 'click', '[data-webmz-story-order-remove]', function ( event ) {
			event.preventDefault();
			var $item = $( this ).closest( '[data-webmz-story-order-item]' );
			var id = $item.attr( 'data-id' );
			$item.remove();
			$select.find( 'option[value="' + id + '"]' ).prop( 'hidden', false );
		} );
	}

	$( function () {
		initStoryItemsMetabox();
		initStoryBoxMetabox();
		initStoryIconsPage();
	} );

	function initStoryIconsPage() {
		var $page = $( '.webmz-story-icons-page' );
		if ( ! $page.length ) {
			return;
		}

		$page.on( 'input', '[data-webmz-story-icon-input]', function () {
			var value = $( this ).val();
			var $preview = $( this ).closest( '[data-webmz-story-icon-field]' ).find( '[data-webmz-story-icon-preview]' );

			if ( value && value.toLowerCase().indexOf( '<svg' ) !== -1 ) {
				$preview.html( value );
			} else {
				$preview.html( '<span class="description">' + ( i18n.svgPreviewEmpty || 'پیش‌نمایش SVG' ) + '</span>' );
			}
		} );
	}
}( jQuery ) );
