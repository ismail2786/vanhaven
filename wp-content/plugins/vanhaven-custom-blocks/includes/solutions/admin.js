/**
 * VanHaven Solutions — admin meta box: add/remove repeatable feature rows.
 */
( function () {
	'use strict';

	function makeRow() {
		const row = document.createElement( 'div' );
		row.className = 'vhs-feature-row';
		row.innerHTML =
			'<input type="text" name="vhs_features[]" value="" placeholder="Feature text" />' +
			'<button type="button" class="button vhs-remove-feature" aria-label="Remove">&times;</button>';
		return row;
	}

	document.addEventListener( 'click', function ( e ) {
		if ( e.target && e.target.id === 'vhs-add-feature' ) {
			e.preventDefault();
			const wrap = document.getElementById( 'vhs-features' );
			if ( wrap ) {
				wrap.appendChild( makeRow() );
			}
		}
		if ( e.target && e.target.classList.contains( 'vhs-remove-feature' ) ) {
			e.preventDefault();
			const rows = document.querySelectorAll( '#vhs-features .vhs-feature-row' );
			if ( rows.length > 1 ) {
				e.target.closest( '.vhs-feature-row' ).remove();
			} else {
				// Keep at least one, just clear it.
				const input = e.target.closest( '.vhs-feature-row' ).querySelector( 'input' );
				if ( input ) { input.value = ''; }
			}
		}
	} );
} )();
