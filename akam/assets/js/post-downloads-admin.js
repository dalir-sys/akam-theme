( function ( $ ) {
	'use strict';

	function getNextIndex( $rows ) {
		var max = -1;

		$rows.find( '[data-webmz-download-row]' ).each( function () {
			var $inputs = $( this ).find( 'input[name^="webmz_post_downloads["]' );
			if ( ! $inputs.length ) {
				return;
			}

			var match = $inputs.first().attr( 'name' ).match( /webmz_post_downloads\[(\d+)\]/ );
			if ( match ) {
				max = Math.max( max, parseInt( match[1], 10 ) );
			}
		} );

		return max + 1;
	}

	function bindRemove( $scope ) {
		$scope.find( '[data-webmz-download-remove]' ).off( 'click.webmzDownloads' ).on( 'click.webmzDownloads', function ( event ) {
			event.preventDefault();

			var $rows = $( this ).closest( '[data-webmz-download-rows]' );
			var $row  = $( this ).closest( '[data-webmz-download-row]' );

			if ( $rows.find( '[data-webmz-download-row]' ).length <= 1 ) {
				$row.find( 'input' ).val( '' );
				return;
			}

			$row.remove();
		} );
	}

	$( document ).on( 'click', '[data-webmz-download-add]', function ( event ) {
		event.preventDefault();

		var $metabox = $( this ).closest( '[data-webmz-download-metabox]' );
		var $rows    = $metabox.find( '[data-webmz-download-rows]' );
		var template = $( '#tmpl-webmz-download-row' ).html();

		if ( ! template ) {
			return;
		}

		var index = getNextIndex( $rows );
		var html  = template.replace( /__INDEX__/g, String( index ) );
		var $row  = $( html );

		$rows.append( $row );
		bindRemove( $row );
	} );

	$( function () {
		bindRemove( $( '[data-webmz-download-metabox]' ) );
	} );
}( jQuery ) );
