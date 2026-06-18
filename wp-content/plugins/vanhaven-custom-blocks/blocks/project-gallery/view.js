/**
 * VH Project Gallery — frontend.
 * - Click a tile → open lightbox with magnified image (prev/next, keyboard, backdrop close).
 * - Load More → fetch next batch via REST, append tiles, update "Showing X of Y".
 */
( function () {
	'use strict';

	function esc( s ) {
		const d = document.createElement( 'div' );
		d.textContent = s == null ? '' : String( s );
		return d.innerHTML;
	}

	function initBlock( root ) {
		const grid    = root.querySelector( '.vhpg__grid' );
		const moreBtn = root.querySelector( '.vhpg__more' );
		const shownEl = root.querySelector( '.vhpg__shown' );
		const totalEl = root.querySelector( '.vhpg__total' );
		if ( ! grid ) { return; }

		let restBase = root.getAttribute( 'data-rest' ) || '';
		if ( ! restBase ) {
			const fallback = ( window.wpApiSettings && window.wpApiSettings.root ) || '/wp-json/';
			restBase = fallback.replace( /\/$/, '' ) + '/vanhaven-projects/v1/';
		}
		if ( restBase.charAt( restBase.length - 1 ) !== '/' ) { restBase += '/'; }
		const nonce   = root.getAttribute( 'data-nonce' ) || '';
		const perPage = parseInt( root.getAttribute( 'data-perpage' ), 10 ) || 5;
		let total     = parseInt( root.getAttribute( 'data-total' ), 10 ) || 0;
		let shown     = parseInt( root.getAttribute( 'data-shown' ), 10 ) || 0;

		/* ---------------- Lightbox ---------------- */
		const lb        = root.querySelector( '.vhpg__lightbox' );
		const lbImg     = root.querySelector( '.vhpg__lb-img' );
		const lbCaption = root.querySelector( '.vhpg__lb-caption' );
		const lbClose   = root.querySelector( '.vhpg__lb-close' );
		const lbPrev    = root.querySelector( '.vhpg__lb-prev' );
		const lbNext    = root.querySelector( '.vhpg__lb-next' );
		let currentIndex = -1;

		function tiles() {
			return Array.prototype.slice.call( grid.querySelectorAll( '.vhpg__tile' ) );
		}

		function openAt( index ) {
			const list = tiles();
			if ( index < 0 || index >= list.length ) { return; }
			currentIndex = index;
			const tile = list[ index ];
			const src  = tile.getAttribute( 'data-full' ) || tile.getAttribute( 'data-large' );
			const cap  = tile.getAttribute( 'data-caption' ) || '';
			lbImg.setAttribute( 'src', src );
			lbImg.setAttribute( 'alt', cap );
			lbCaption.textContent = cap;
			lbCaption.style.display = cap ? '' : 'none';
			lb.classList.add( 'is-open' );
			lb.setAttribute( 'aria-hidden', 'false' );
			document.body.style.overflow = 'hidden';
			updateArrows();
		}

		function close() {
			lb.classList.remove( 'is-open' );
			lb.setAttribute( 'aria-hidden', 'true' );
			document.body.style.overflow = '';
			currentIndex = -1;
		}

		function updateArrows() {
			const count = tiles().length;
			if ( lbPrev ) { lbPrev.style.visibility = currentIndex > 0 ? 'visible' : 'hidden'; }
			if ( lbNext ) { lbNext.style.visibility = currentIndex < count - 1 ? 'visible' : 'hidden'; }
		}

		function step( dir ) {
			openAt( currentIndex + dir );
		}

		// Delegate tile clicks (works for appended tiles too).
		grid.addEventListener( 'click', function ( e ) {
			const tile = e.target.closest( '.vhpg__tile' );
			if ( ! tile ) { return; }
			openAt( tiles().indexOf( tile ) );
		} );

		if ( lbClose ) { lbClose.addEventListener( 'click', close ); }
		if ( lbPrev ) { lbPrev.addEventListener( 'click', function () { step( -1 ); } ); }
		if ( lbNext ) { lbNext.addEventListener( 'click', function () { step( 1 ); } ); }
		if ( lb ) {
			lb.addEventListener( 'click', function ( e ) {
				if ( e.target === lb ) { close(); }
			} );
		}
		document.addEventListener( 'keydown', function ( e ) {
			if ( ! lb.classList.contains( 'is-open' ) ) { return; }
			if ( e.key === 'Escape' ) { close(); }
			if ( e.key === 'ArrowLeft' ) { step( -1 ); }
			if ( e.key === 'ArrowRight' ) { step( 1 ); }
		} );

		/* ---------------- Load More ---------------- */
		function buildTile( item ) {
			const btn = document.createElement( 'button' );
			btn.type = 'button';
			btn.className = 'vhpg__tile';
			btn.setAttribute( 'data-full', item.full );
			btn.setAttribute( 'data-large', item.large );
			btn.setAttribute( 'data-caption', item.caption || item.title || '' );
			btn.setAttribute( 'aria-label', item.alt || item.title || '' );
			btn.innerHTML =
				'<img class="vhpg__img" src="' + esc( item.thumb ) + '" alt="' + esc( item.alt || item.title || '' ) + '" loading="lazy" />' +
				'<span class="vhpg__zoom" aria-hidden="true"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3M11 8v6M8 11h6"/></svg></span>';
			return btn;
		}

		function updateCounter() {
			if ( shownEl ) { shownEl.textContent = String( shown ); }
			if ( totalEl ) { totalEl.textContent = String( total ); }
			if ( moreBtn && shown >= total ) { moreBtn.setAttribute( 'hidden', '' ); }
		}

		if ( moreBtn ) {
			moreBtn.addEventListener( 'click', function () {
				moreBtn.classList.add( 'is-loading' );
				moreBtn.disabled = true;

				const url = restBase + 'items?offset=' + shown + '&per_page=' + perPage;
				const headers = {};
				if ( nonce ) { headers[ 'X-WP-Nonce' ] = nonce; }

				fetch( url, { headers: headers, credentials: 'same-origin' } )
					.then( function ( r ) {
						if ( ! r.ok ) { throw new Error( 'HTTP ' + r.status ); }
						return r.json();
					} )
					.then( function ( res ) {
						const items = ( res && res.items ) || [];
						total = typeof res.total === 'number' ? res.total : total;
						const frag = document.createDocumentFragment();
						items.forEach( function ( item ) { frag.appendChild( buildTile( item ) ); } );
						grid.appendChild( frag );
						shown += items.length;
						updateCounter();
						moreBtn.classList.remove( 'is-loading' );
						moreBtn.disabled = false;
					} )
					.catch( function () {
						moreBtn.classList.remove( 'is-loading' );
						moreBtn.disabled = false;
						moreBtn.textContent = 'Try again';
					} );
			} );
		}
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.vhpg:not(.vhpg--editor)' ).forEach( initBlock );
	} );
} )();
