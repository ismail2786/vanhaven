/**
 * VanHaven Handovers Gallery — frontend carousel.
 * The grid scrolls horizontally one "page" at a time; rows stack via CSS grid.
 */
( function () {
	'use strict';

	function initBlock( root ) {
		const grid = root.querySelector( '.vhhg__grid' );
		const prev = root.querySelector( '.vhhg__btn--prev' );
		const next = root.querySelector( '.vhhg__btn--next' );
		if ( ! grid ) { return; }

		function page() {
			// Scroll by roughly one viewport width of the grid.
			return grid.clientWidth * 0.9;
		}

		function update() {
			const maxScroll = grid.scrollWidth - grid.clientWidth - 2;
			if ( prev ) { prev.disabled = grid.scrollLeft <= 2; }
			if ( next ) { next.disabled = grid.scrollLeft >= maxScroll; }
		}

		if ( next ) {
			next.addEventListener( 'click', function () {
				grid.scrollBy( { left: page(), behavior: 'smooth' } );
			} );
		}
		if ( prev ) {
			prev.addEventListener( 'click', function () {
				grid.scrollBy( { left: -page(), behavior: 'smooth' } );
			} );
		}

		grid.addEventListener( 'scroll', update, { passive: true } );
		window.addEventListener( 'resize', update );
		update();

		// Drag-to-scroll.
		let down = false, startX = 0, start = 0;
		grid.addEventListener( 'pointerdown', function ( e ) {
			down = true;
			startX = e.pageX;
			start = grid.scrollLeft;
			grid.classList.add( 'is-grabbing' );
		} );
		[ 'pointerup', 'pointerleave' ].forEach( function ( ev ) {
			grid.addEventListener( ev, function () {
				down = false;
				grid.classList.remove( 'is-grabbing' );
			} );
		} );
		grid.addEventListener( 'pointermove', function ( e ) {
			if ( ! down ) { return; }
			e.preventDefault();
			grid.scrollLeft = start - ( e.pageX - startX ) * 1.2;
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.vhhg:not(.vhhg--editor)' ).forEach( initBlock );
	} );
} )();
