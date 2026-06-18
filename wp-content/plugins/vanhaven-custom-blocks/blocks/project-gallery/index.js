/**
 * VH Project Gallery — editor script (no build step).
 * Shows a live mosaic preview from the REST endpoint + settings.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment, useState, useEffect } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, RangeControl, TextControl, TextareaControl, Spinner, Notice, Button } = wp.components;
	const { __ } = wp.i18n;
	const apiFetch = wp.apiFetch;

	registerBlockType( 'vanhaven/project-gallery', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const { heading, subheading, perPage, accentColor } = attributes;

			const [ data, setData ] = useState( { items: [], total: 0 } );
			const [ loading, setLoading ] = useState( true );

			useEffect( function () {
				apiFetch( { path: '/vanhaven-projects/v1/items?offset=0&per_page=' + perPage } ).then( function ( res ) {
					setData( res || { items: [], total: 0 } );
					setLoading( false );
				} );
			}, [ perPage ] );

			const blockProps = useBlockProps( {
				className: 'vhpg vhpg--editor',
				style: { '--vhpg-accent': accentColor },
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
						min: 1,
						max: 12,
						onChange: function ( v ) { setAttributes( { perPage: v } ); },
					} ),
					el( TextControl, {
						label: __( 'Accent color', 'vanhaven-custom-blocks' ),
						value: accentColor,
						onChange: function ( v ) { setAttributes( { accentColor: v } ); },
					} ),
					el( 'p', { style: { fontSize: '12px', opacity: 0.8 } },
						__( 'Manage images under VanHaven → Projects (each Project uses its Featured Image).', 'vanhaven-custom-blocks' )
					)
				),
				el(
					PanelBody,
					{ title: __( 'Header', 'vanhaven-custom-blocks' ), initialOpen: false },
					el( TextControl, {
						label: __( 'Heading', 'vanhaven-custom-blocks' ),
						value: heading,
						onChange: function ( v ) { setAttributes( { heading: v } ); },
					} ),
					el( TextareaControl, {
						label: __( 'Subheading', 'vanhaven-custom-blocks' ),
						value: subheading,
						onChange: function ( v ) { setAttributes( { subheading: v } ); },
					} )
				)
			);

			let body;
			if ( loading ) {
				body = el( Spinner );
			} else if ( ! data.items.length ) {
				body = el(
					Notice,
					{ status: 'info', isDismissible: false },
					el( Fragment, {},
						__( 'No projects found. ', 'vanhaven-custom-blocks' ),
						el( Button, {
							variant: 'link',
							href: ( window.vhpgAdmin && window.vhpgAdmin.newUrl ) || '/wp-admin/post-new.php?post_type=vh_project',
							target: '_blank',
						}, __( 'Add your first project', 'vanhaven-custom-blocks' ) )
					)
				);
			} else {
				body = el(
					Fragment,
					{},
					el( 'div', { className: 'vhpg__grid' },
						data.items.map( function ( it, i ) {
							return el( 'div', { key: i, className: 'vhpg__tile' },
								el( 'img', { className: 'vhpg__img', src: it.thumb, alt: it.title } ),
								el( 'span', { className: 'vhpg__zoom', dangerouslySetInnerHTML: {
									__html: '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3M11 8v6M8 11h6"/></svg>'
								} } )
							);
						} )
					),
					el( 'div', { className: 'vhpg__footer' },
						el( 'p', { className: 'vhpg__count' },
							__( 'Showing ', 'vanhaven-custom-blocks' ),
							el( 'span', { className: 'vhpg__shown' }, String( data.items.length ) ),
							__( ' of ', 'vanhaven-custom-blocks' ),
							el( 'span', { className: 'vhpg__total' }, String( data.total ) ),
							__( ' Projects', 'vanhaven-custom-blocks' )
						),
						data.items.length < data.total
							? el( 'button', { className: 'vhpg__more', type: 'button', disabled: true }, __( 'Load More', 'vanhaven-custom-blocks' ) )
							: null
					)
				);
			}

			return el( Fragment, {}, inspector, el( 'div', blockProps,
				( heading || subheading )
					? el( 'div', { className: 'vhpg__head' },
						heading ? el( 'h2', { className: 'vhpg__title' }, heading ) : null,
						subheading ? el( 'p', { className: 'vhpg__sub' }, subheading ) : null
					  )
					: null,
				body
			) );
		},
		save: function () { return null; },
	} );
} )( window.wp );
