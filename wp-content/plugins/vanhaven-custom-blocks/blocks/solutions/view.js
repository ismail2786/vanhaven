/**
 * VanHaven Bespoke Solutions — frontend.
 * Tabs switch the active slide; prev/next arrows step through slides.
 */
( function () {
	'use strict';

	function initBlock( root ) {
		const tabs = Array.prototype.slice.call( root.querySelectorAll( '.vhs__tab' ) );
		const slides = Array.prototype.slice.call( root.querySelectorAll( '.vhs__slide' ) );
		const prev = root.querySelector( '.vhs__nav--prev' );
		const next = root.querySelector( '.vhs__nav--next' );
		if ( ! slides.length ) { return; }

		let current = 0;

		function show( index ) {
			if ( index < 0 ) { index = slides.length - 1; }
			if ( index >= slides.length ) { index = 0; }
			current = index;

			slides.forEach( function ( s, i ) {
				s.classList.toggle( 'is-active', i === current );
			} );
			tabs.forEach( function ( t, i ) {
				const on = i === current;
				t.classList.toggle( 'is-active', on );
				t.setAttribute( 'aria-selected', on ? 'true' : 'false' );
			} );
		}

		tabs.forEach( function ( tab, i ) {
			tab.addEventListener( 'click', function () { show( i ); } );
		} );
		if ( next ) { next.addEventListener( 'click', function () { show( current + 1 ); } ); }
		if ( prev ) { prev.addEventListener( 'click', function () { show( current - 1 ); } ); }

		// Keyboard support on tabs.
		root.querySelector( '.vhs__tabs' ).addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'ArrowRight' ) { show( current + 1 ); tabs[ current ].focus(); }
			if ( e.key === 'ArrowLeft' ) { show( current - 1 ); tabs[ current ].focus(); }
		} );

		show( 0 );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.vhs:not(.vhs--editor)' ).forEach( initBlock );
	} );
} )();
