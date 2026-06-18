/**
 * VanHaven Product Showcase — frontend hydration.
 * Handles tab switching (fetch products), and slider navigation.
 */
( function () {
	'use strict';

	function buildCard( p, ctaLabel ) {
		const specs =
			p.attributes && p.attributes.length
				? '<p class="vhsc__specs">' + p.attributes.map( esc ).join( '  |  ' ) + '</p>'
				: '';
		const badge = p.badge ? '<span class="vhsc__badge">' + esc( p.badge ) + '</span>' : '';
		return (
			'<article class="vhsc__card"><div class="vhsc__media">' +
			badge +
			'<img class="vhsc__img" src="' + esc( p.image ) + '" alt="' + esc( p.title ) + '" loading="lazy" />' +
			'<div class="vhsc__overlay"></div>' +
			'<div class="vhsc__body">' +
			'<h3 class="vhsc__name">' + esc( p.title ) + '</h3>' +
			specs +
			'<div class="vhsc__foot">' +
			'<span class="vhsc__price">' + ( p.priceHtml || '' ) + '</span>' +
			'<a class="vhsc__cta" href="' + esc( p.permalink ) + '">' + esc( ctaLabel ) + ' <span aria-hidden="true">\u2197</span></a>' +
			'</div></div></div></article>'
		);
	}

	function esc( s ) {
		const d = document.createElement( 'div' );
		d.textContent = s == null ? '' : String( s );
		return d.innerHTML;
	}

	function initBlock( root ) {
		const rail = root.querySelector( '.vhsc__rail' );
		const tabs = root.querySelectorAll( '.vhsc__tab' );
		const prev = root.querySelector( '.vhsc__nav--prev' );
		const next = root.querySelector( '.vhsc__nav--next' );
		const limit = root.getAttribute( 'data-limit' ) || 8;
		const badge = root.getAttribute( 'data-badge' ) || '';
		const ctaLabel = root.getAttribute( 'data-cta' ) || 'View Details';

		// REST base + nonce are injected by PHP on the wrapper. Fall back gracefully.
		let restBase = root.getAttribute( 'data-rest' ) || '';
		if ( ! restBase ) {
			const fallback = ( window.wpApiSettings && window.wpApiSettings.root ) || '/wp-json/';
			restBase = fallback.replace( /\/$/, '' ) + '/vanhaven/v1/';
		}
		if ( restBase.charAt( restBase.length - 1 ) !== '/' ) {
			restBase += '/';
		}
		const restNonce = root.getAttribute( 'data-nonce' ) || '';

		// Tab switching.
		tabs.forEach( function ( tab ) {
			tab.addEventListener( 'click', function () {
				if ( tab.classList.contains( 'is-active' ) ) {
					return;
				}
				tabs.forEach( function ( t ) {
					t.classList.remove( 'is-active' );
					t.setAttribute( 'aria-selected', 'false' );
				} );
				tab.classList.add( 'is-active' );
				tab.setAttribute( 'aria-selected', 'true' );

				const cat = tab.getAttribute( 'data-cat' );
				rail.classList.add( 'is-loading' );

				const url =
					restBase + 'products?category=' + cat +
					'&limit=' + limit +
					'&badgeMetaKey=' + encodeURIComponent( badge );

				const headers = {};
				if ( restNonce ) {
					headers[ 'X-WP-Nonce' ] = restNonce;
				}

				fetch( url, { headers: headers, credentials: 'same-origin' } )
					.then( function ( r ) {
						if ( ! r.ok ) {
							throw new Error( 'HTTP ' + r.status );
						}
						return r.json();
					} )
					.then( function ( products ) {
						rail.setAttribute( 'data-active-cat', cat );
						if ( ! Array.isArray( products ) || ! products.length ) {
							rail.innerHTML = '<p class="vhsc__empty">No products found in this category.</p>';
						} else {
							rail.innerHTML = products
								.map( function ( p ) { return buildCard( p, ctaLabel ); } )
								.join( '' );
						}
						rail.scrollTo( { left: 0, behavior: 'smooth' } );
						rail.classList.remove( 'is-loading' );
					} )
					.catch( function ( err ) {
						rail.innerHTML = '<p class="vhsc__empty">Could not load products (' + ( err.message || 'error' ) + '). Please refresh.</p>';
						rail.classList.remove( 'is-loading' );
					} );
			} );
		} );

		// Slider navigation.
		function scrollByCards( dir ) {
			const card = rail.querySelector( '.vhsc__card' );
			const amount = card ? card.offsetWidth + 24 : 320;
			rail.scrollBy( { left: dir * amount, behavior: 'smooth' } );
		}
		if ( next ) {
			next.addEventListener( 'click', function () { scrollByCards( 1 ); } );
		}
		if ( prev ) {
			prev.addEventListener( 'click', function () { scrollByCards( -1 ); } );
		}

		// Drag-to-scroll (pointer).
		let isDown = false, startX = 0, scrollLeft = 0;
		rail.addEventListener( 'pointerdown', function ( e ) {
			isDown = true;
			startX = e.pageX - rail.offsetLeft;
			scrollLeft = rail.scrollLeft;
			rail.classList.add( 'is-grabbing' );
		} );
		[ 'pointerup', 'pointerleave' ].forEach( function ( ev ) {
			rail.addEventListener( ev, function () {
				isDown = false;
				rail.classList.remove( 'is-grabbing' );
			} );
		} );
		rail.addEventListener( 'pointermove', function ( e ) {
			if ( ! isDown ) { return; }
			e.preventDefault();
			const x = e.pageX - rail.offsetLeft;
			rail.scrollLeft = scrollLeft - ( x - startX ) * 1.2;
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.vhsc:not(.vhsc--editor)' ).forEach( initBlock );
	} );
} )();
