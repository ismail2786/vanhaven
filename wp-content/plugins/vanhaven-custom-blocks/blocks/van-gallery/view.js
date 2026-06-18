/**
 * VH Gallery with Filters — frontend.
 * Category tabs + Photos/Videos toggle + Sort dropdown all re-query the REST
 * endpoint and replace the grid. Load More appends. Lightbox supports images
 * and embedded videos (YouTube/Vimeo/MP4).
 */
( function () {
	'use strict';

	function esc( s ) {
		const d = document.createElement( 'div' );
		d.textContent = s == null ? '' : String( s );
		return d.innerHTML;
	}

	function videoEmbed( url ) {
		if ( ! url ) { return ''; }
		// YouTube
		let m = url.match( /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([\w-]{11})/ );
		if ( m ) {
			return '<iframe src="https://www.youtube.com/embed/' + m[ 1 ] + '?autoplay=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
		}
		// Vimeo
		m = url.match( /vimeo\.com\/(?:video\/)?(\d+)/ );
		if ( m ) {
			return '<iframe src="https://player.vimeo.com/video/' + m[ 1 ] + '?autoplay=1" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
		}
		// Direct file
		return '<video src="' + esc( url ) + '" controls autoplay playsinline></video>';
	}

	function initBlock( root ) {
		const grid    = root.querySelector( '.vhvg__grid' );
		const moreBtn = root.querySelector( '.vhvg__more' );
		const shownEl = root.querySelector( '.vhvg__shown' );
		const totalEl = root.querySelector( '.vhvg__total' );
		const cats    = Array.prototype.slice.call( root.querySelectorAll( '.vhvg__cat' ) );
		const types   = Array.prototype.slice.call( root.querySelectorAll( '.vhvg__type' ) );
		const sortSel = root.querySelector( '.vhvg__sort-select' );
		if ( ! grid ) { return; }

		let restBase = root.getAttribute( 'data-rest' ) || '';
		if ( ! restBase ) {
			const fallback = ( window.wpApiSettings && window.wpApiSettings.root ) || '/wp-json/';
			restBase = fallback.replace( /\/$/, '' ) + '/vanhaven-gallery/v1/';
		}
		if ( restBase.charAt( restBase.length - 1 ) !== '/' ) { restBase += '/'; }
		const nonce   = root.getAttribute( 'data-nonce' ) || '';
		const perPage = parseInt( root.getAttribute( 'data-perpage' ), 10 ) || 10;

		const state = {
			category: root.getAttribute( 'data-category' ) || 'all',
			type:     root.getAttribute( 'data-type' ) || 'photo',
			sort:     root.getAttribute( 'data-sort' ) || 'featured',
			shown:    parseInt( root.getAttribute( 'data-shown' ), 10 ) || 0,
			total:    parseInt( root.getAttribute( 'data-total' ), 10 ) || 0,
		};

		function headers() {
			const h = {};
			if ( nonce ) { h[ 'X-WP-Nonce' ] = nonce; }
			return h;
		}

		function tileHtml( item ) {
			const isVid = item.type === 'video';
			const icon = isVid
				? '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>'
				: '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3M11 8v6M8 11h6"/></svg>';
			return '<button type="button" class="vhvg__tile' + ( isVid ? ' vhvg__tile--video' : '' ) + '"' +
				' data-type="' + esc( item.type ) + '"' +
				' data-full="' + esc( item.full ) + '"' +
				' data-video="' + esc( item.video || '' ) + '"' +
				' data-caption="' + esc( item.title ) + '"' +
				' aria-label="' + esc( item.alt || item.title ) + '">' +
				( item.thumb ? '<img class="vhvg__img" src="' + esc( item.thumb ) + '" alt="' + esc( item.alt || item.title ) + '" loading="lazy" />' : '' ) +
				'<span class="vhvg__zoom" aria-hidden="true">' + icon + '</span>' +
				'</button>';
		}

		function url( offset ) {
			return restBase + 'items?category=' + encodeURIComponent( state.category ) +
				'&type=' + encodeURIComponent( state.type ) +
				'&sort=' + encodeURIComponent( state.sort ) +
				'&offset=' + offset + '&per_page=' + perPage;
		}

		function updateCounter() {
			if ( shownEl ) { shownEl.textContent = String( state.shown ); }
			if ( totalEl ) { totalEl.textContent = String( state.total ); }
			if ( moreBtn ) {
				if ( state.shown >= state.total ) { moreBtn.setAttribute( 'hidden', '' ); }
				else { moreBtn.removeAttribute( 'hidden' ); }
			}
		}

		function reload() {
			grid.classList.add( 'is-loading' );
			fetch( url( 0 ), { headers: headers(), credentials: 'same-origin' } )
				.then( function ( r ) { if ( ! r.ok ) { throw new Error( 'HTTP ' + r.status ); } return r.json(); } )
				.then( function ( res ) {
					const items = ( res && res.items ) || [];
					state.total = typeof res.total === 'number' ? res.total : 0;
					state.shown = items.length;
					grid.innerHTML = items.length ? items.map( tileHtml ).join( '' ) : '<p class="vhvg__empty">No items found.</p>';
					updateCounter();
					grid.classList.remove( 'is-loading' );
				} )
				.catch( function () { grid.classList.remove( 'is-loading' ); } );
		}

		function loadMore() {
			moreBtn.disabled = true;
			moreBtn.classList.add( 'is-loading' );
			fetch( url( state.shown ), { headers: headers(), credentials: 'same-origin' } )
				.then( function ( r ) { if ( ! r.ok ) { throw new Error( 'HTTP ' + r.status ); } return r.json(); } )
				.then( function ( res ) {
					const items = ( res && res.items ) || [];
					state.total = typeof res.total === 'number' ? res.total : state.total;
					const frag = document.createElement( 'div' );
					frag.innerHTML = items.map( tileHtml ).join( '' );
					while ( frag.firstChild ) { grid.appendChild( frag.firstChild ); }
					state.shown += items.length;
					updateCounter();
					moreBtn.disabled = false;
					moreBtn.classList.remove( 'is-loading' );
				} )
				.catch( function () {
					moreBtn.disabled = false;
					moreBtn.classList.remove( 'is-loading' );
				} );
		}

		// Category tabs.
		cats.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				if ( btn.classList.contains( 'is-active' ) ) { return; }
				cats.forEach( function ( b ) { b.classList.remove( 'is-active' ); } );
				btn.classList.add( 'is-active' );
				state.category = btn.getAttribute( 'data-cat' );
				reload();
			} );
		} );

		// Type toggle.
		types.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				if ( btn.classList.contains( 'is-active' ) ) { return; }
				types.forEach( function ( b ) { b.classList.remove( 'is-active' ); } );
				btn.classList.add( 'is-active' );
				state.type = btn.getAttribute( 'data-type' );
				reload();
			} );
		} );

		// Sort.
		if ( sortSel ) {
			sortSel.addEventListener( 'change', function () {
				state.sort = sortSel.value;
				reload();
			} );
		}

		if ( moreBtn ) { moreBtn.addEventListener( 'click', loadMore ); }

		/* ---------------- Lightbox ---------------- */
		const lb       = root.querySelector( '.vhvg__lightbox' );
		const stage    = root.querySelector( '.vhvg__lb-stage' );
		const lbClose  = root.querySelector( '.vhvg__lb-close' );
		const lbPrev   = root.querySelector( '.vhvg__lb-prev' );
		const lbNext   = root.querySelector( '.vhvg__lb-next' );
		let current = -1;

		function tiles() { return Array.prototype.slice.call( grid.querySelectorAll( '.vhvg__tile' ) ); }

		function openAt( i ) {
			const list = tiles();
			if ( i < 0 || i >= list.length ) { return; }
			current = i;
			const tile = list[ i ];
			const type = tile.getAttribute( 'data-type' );
			const cap  = tile.getAttribute( 'data-caption' ) || '';
			let inner;
			if ( type === 'video' ) {
				inner = '<div class="vhvg__lb-video">' + videoEmbed( tile.getAttribute( 'data-video' ) ) + '</div>';
			} else {
				inner = '<img class="vhvg__lb-img" src="' + esc( tile.getAttribute( 'data-full' ) ) + '" alt="' + esc( cap ) + '" />';
			}
			stage.innerHTML = inner + ( cap ? '<p class="vhvg__lb-caption">' + esc( cap ) + '</p>' : '' );
			lb.classList.add( 'is-open' );
			lb.setAttribute( 'aria-hidden', 'false' );
			document.body.style.overflow = 'hidden';
			if ( lbPrev ) { lbPrev.style.visibility = i > 0 ? 'visible' : 'hidden'; }
			if ( lbNext ) { lbNext.style.visibility = i < list.length - 1 ? 'visible' : 'hidden'; }
		}

		function close() {
			lb.classList.remove( 'is-open' );
			lb.setAttribute( 'aria-hidden', 'true' );
			stage.innerHTML = ''; // stops video playback
			document.body.style.overflow = '';
			current = -1;
		}

		grid.addEventListener( 'click', function ( e ) {
			const tile = e.target.closest( '.vhvg__tile' );
			if ( ! tile ) { return; }
			openAt( tiles().indexOf( tile ) );
		} );
		if ( lbClose ) { lbClose.addEventListener( 'click', close ); }
		if ( lbPrev ) { lbPrev.addEventListener( 'click', function () { openAt( current - 1 ); } ); }
		if ( lbNext ) { lbNext.addEventListener( 'click', function () { openAt( current + 1 ); } ); }
		if ( lb ) { lb.addEventListener( 'click', function ( e ) { if ( e.target === lb ) { close(); } } ); }
		document.addEventListener( 'keydown', function ( e ) {
			if ( ! lb.classList.contains( 'is-open' ) ) { return; }
			if ( e.key === 'Escape' ) { close(); }
			if ( e.key === 'ArrowLeft' ) { openAt( current - 1 ); }
			if ( e.key === 'ArrowRight' ) { openAt( current + 1 ); }
		} );

		updateCounter();
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.vhvg:not(.vhvg--editor)' ).forEach( initBlock );
	} );
} )();
