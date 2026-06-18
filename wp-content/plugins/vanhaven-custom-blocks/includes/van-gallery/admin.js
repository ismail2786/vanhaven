/**
 * VH Gallery admin JS:
 *  - term thumbnail uploader (WP media frame)
 *  - show/hide video URL row based on type radio
 */
( function ( $ ) {
	'use strict';

	$( function () {
		// ---- Term thumbnail uploader ----
		var frame;
		$( document ).on( 'click', '.vhvg-upload-thumb', function ( e ) {
			e.preventDefault();
			if ( frame ) { frame.open(); return; }
			frame = wp.media( {
				title: 'Select thumbnail',
				button: { text: 'Use image' },
				multiple: false,
			} );
			frame.on( 'select', function () {
				var att = frame.state().get( 'selection' ).first().toJSON();
				$( '#vhvg_term_thumb' ).val( att.id );
				var url = ( att.sizes && att.sizes.thumbnail ) ? att.sizes.thumbnail.url : att.url;
				$( '#vhvg_term_thumb_preview' ).html( '<img src="' + url + '" style="max-width:120px;height:auto;border-radius:6px;" />' );
			} );
			frame.open();
		} );

		$( document ).on( 'click', '.vhvg-remove-thumb', function ( e ) {
			e.preventDefault();
			$( '#vhvg_term_thumb' ).val( '' );
			$( '#vhvg_term_thumb_preview' ).empty();
		} );

		// ---- Video URL row toggle ----
		$( document ).on( 'change', 'input[name="vhvg_type"]', function () {
			if ( $( this ).val() === 'video' && $( this ).is( ':checked' ) ) {
				$( '.vhvg-video-row' ).show();
			} else {
				$( '.vhvg-video-row' ).hide();
			}
		} );
	} );
} )( jQuery );
