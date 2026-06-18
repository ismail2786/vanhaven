/**
 * VH Gallery with Filters — editor script (no build step).
 * Live preview of tabs + grid from REST; functional controls are frontend-only.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment, useState, useEffect } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, RangeControl, TextControl, ToggleControl, Spinner, Notice, Button } = wp.components;
	const { __ } = wp.i18n;
	const apiFetch = wp.apiFetch;

	registerBlockType( 'vanhaven/van-gallery', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const { perPage, accentColor, showVideoToggle, showSort } = attributes;

			const [ filters, setFilters ] = useState( [] );
			const [ data, setData ] = useState( { items: [], total: 0 } );
			const [ loading, setLoading ] = useState( true );

			useEffect( function () {
				Promise.all( [
					apiFetch( { path: '/vanhaven-gallery/v1/filters' } ),
					apiFetch( { path: '/vanhaven-gallery/v1/items?category=all&type=photo&sort=featured&offset=0&per_page=' + perPage } ),
				] ).then( function ( res ) {
					setFilters( res[ 0 ] || [] );
					setData( res[ 1 ] || { items: [], total: 0 } );
					setLoading( false );
				} );
			}, [ perPage ] );

			const blockProps = useBlockProps( {
				className: 'vhvg vhvg--editor',
				style: { '--vhvg-accent': accentColor },
			} );

			const inspector = el(
				InspectorControls,
				{},
				el(
					PanelBody,
					{ title: __( 'Gallery', 'vanhaven-custom-blocks' ), initialOpen: true },
					el( RangeControl, {
						label: __( 'Images per load', 'vanhaven-custom-blocks' ),
						value: perPage,
						min: 2,
						max: 24,
						onChange: function ( v ) { setAttributes( { perPage: v } ); },
					} ),
					el( ToggleControl, {
						label: __( 'Show Photos/Videos toggle', 'vanhaven-custom-blocks' ),
						checked: showVideoToggle,
						onChange: function ( v ) { setAttributes( { showVideoToggle: v } ); },
					} ),
					el( ToggleControl, {
						label: __( 'Show Sort dropdown', 'vanhaven-custom-blocks' ),
						checked: showSort,
						onChange: function ( v ) { setAttributes( { showSort: v } ); },
					} ),
					el( TextControl, {
						label: __( 'Accent color', 'vanhaven-custom-blocks' ),
						value: accentColor,
						onChange: function ( v ) { setAttributes( { accentColor: v } ); },
					} ),
					el( 'p', { style: { fontSize: '12px', opacity: 0.8 } },
						__( 'Manage media under VanHaven → Van Gallery. Add filter tabs under Van Gallery → Categories (each can have a thumbnail).', 'vanhaven-custom-blocks' )
					)
				)
			);

			let body;
			if ( loading ) {
				body = el( Spinner );
			} else if ( ! data.items.length && filters.length <= 2 ) {
				body = el(
					Notice,
					{ status: 'info', isDismissible: false },
					el( Fragment, {},
						__( 'No media found. ', 'vanhaven-custom-blocks' ),
						el( Button, {
							variant: 'link',
							href: ( window.vhvgAdmin && window.vhvgAdmin.newUrl ) || '/wp-admin/post-new.php?post_type=vh_media',
							target: '_blank',
						}, __( 'Add media', 'vanhaven-custom-blocks' ) )
					)
				);
			} else {
				body = el(
					Fragment,
					{},
					el( 'div', { className: 'vhvg__cats' },
						filters.map( function ( f, i ) {
							return el( 'button', {
								key: i,
								type: 'button',
								className: 'vhvg__cat' + ( i === 0 ? ' is-active' : '' ),
								style: f.thumb ? { backgroundImage: 'url(' + f.thumb + ')' } : {},
							}, el( 'span', { className: 'vhvg__cat-label' }, f.name ) );
						} )
					),
					el( 'div', { className: 'vhvg__controls' },
						showVideoToggle
							? el( 'div', { className: 'vhvg__types' },
								el( 'button', { className: 'vhvg__type is-active', type: 'button' }, __( 'Photos', 'vanhaven-custom-blocks' ) ),
								el( 'button', { className: 'vhvg__type', type: 'button' }, __( 'Videos', 'vanhaven-custom-blocks' ) )
							  )
							: null,
						showSort
							? el( 'label', { className: 'vhvg__sort' },
								el( 'span', { className: 'vhvg__sort-label' }, __( 'Sort by:', 'vanhaven-custom-blocks' ) ),
								el( 'select', { className: 'vhvg__sort-select', disabled: true },
									el( 'option', {}, __( 'Featured', 'vanhaven-custom-blocks' ) )
								)
							  )
							: null
					),
					el( 'div', { className: 'vhvg__grid' },
						data.items.map( function ( it, i ) {
							return el( 'div', { key: i, className: 'vhvg__tile' + ( it.type === 'video' ? ' vhvg__tile--video' : '' ) },
								it.thumb ? el( 'img', { className: 'vhvg__img', src: it.thumb, alt: it.title } ) : null,
								el( 'span', { className: 'vhvg__zoom', dangerouslySetInnerHTML: {
									__html: it.type === 'video'
										? '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>'
										: '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3M11 8v6M8 11h6"/></svg>'
								} } )
							);
						} )
					),
					el( 'div', { className: 'vhvg__footer' },
						el( 'p', { className: 'vhvg__count' },
							__( 'Showing ', 'vanhaven-custom-blocks' ),
							el( 'span', { className: 'vhvg__shown' }, String( data.items.length ) ),
							__( ' of ', 'vanhaven-custom-blocks' ),
							el( 'span', { className: 'vhvg__total' }, String( data.total ) ),
							__( ' Projects', 'vanhaven-custom-blocks' )
						),
						data.items.length < data.total
							? el( 'button', { className: 'vhvg__more', type: 'button', disabled: true }, __( 'Load More', 'vanhaven-custom-blocks' ) )
							: null
					)
				);
			}

			return el( Fragment, {}, inspector, el( 'div', blockProps, body ) );
		},
		save: function () { return null; },
	} );
} )( window.wp );
