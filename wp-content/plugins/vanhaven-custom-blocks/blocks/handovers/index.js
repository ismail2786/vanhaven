/**
 * VanHaven Handovers Gallery — editor script (no build step).
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment, useState, useEffect } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, RangeControl, TextControl, TextareaControl, Spinner, Notice, Button } = wp.components;
	const { __ } = wp.i18n;
	const apiFetch = wp.apiFetch;

	registerBlockType( 'vanhaven/handovers', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const { heading, subheading, limit, rows, accentColor } = attributes;

			const [ items, setItems ] = useState( [] );
			const [ loading, setLoading ] = useState( true );

			useEffect( function () {
				apiFetch( { path: '/vanhaven-handovers/v1/items?limit=' + limit } ).then( function ( res ) {
					setItems( res || [] );
					setLoading( false );
				} );
			}, [ limit ] );

			const blockProps = useBlockProps( {
				className: 'vhhg vhhg--editor',
				style: { '--vhhg-accent': accentColor, '--vhhg-rows': rows },
			} );

			const inspector = el(
				InspectorControls,
				{},
				el(
					PanelBody,
					{ title: __( 'Layout', 'vanhaven-handovers' ), initialOpen: true },
					el( RangeControl, {
						label: __( 'Rows', 'vanhaven-handovers' ),
						value: rows,
						min: 1,
						max: 3,
						onChange: function ( v ) { setAttributes( { rows: v } ); },
					} ),
					el( RangeControl, {
						label: __( 'Max images', 'vanhaven-handovers' ),
						value: limit,
						min: 2,
						max: 30,
						onChange: function ( v ) { setAttributes( { limit: v } ); },
					} ),
					el( 'p', { style: { fontSize: '12px', opacity: 0.8 } },
						__( 'Add or edit images under "Handovers" in the admin menu. Each handover uses its Featured Image and Tag field.', 'vanhaven-handovers' )
					)
				),
				el(
					PanelBody,
					{ title: __( 'Content', 'vanhaven-handovers' ), initialOpen: false },
					el( TextControl, {
						label: __( 'Heading', 'vanhaven-handovers' ),
						value: heading,
						onChange: function ( v ) { setAttributes( { heading: v } ); },
					} ),
					el( TextareaControl, {
						label: __( 'Subheading', 'vanhaven-handovers' ),
						value: subheading,
						onChange: function ( v ) { setAttributes( { subheading: v } ); },
					} ),
					el( TextControl, {
						label: __( 'Accent color', 'vanhaven-handovers' ),
						value: accentColor,
						onChange: function ( v ) { setAttributes( { accentColor: v } ); },
					} )
				)
			);

			let preview;
			if ( loading ) {
				preview = el( Spinner );
			} else if ( ! items.length ) {
				preview = el(
					Notice,
					{ status: 'info', isDismissible: false },
					el( Fragment, {},
						__( 'No handovers found. ', 'vanhaven-handovers' ),
						el( Button, {
							variant: 'link',
							href: ( window.vhhgAdmin && window.vhhgAdmin.newUrl ) || '/wp-admin/post-new.php?post_type=vh_handover',
							target: '_blank',
						}, __( 'Add your first handover', 'vanhaven-handovers' ) )
					)
				);
			} else {
				preview = el(
					Fragment,
					{},
					el( 'div', { className: 'vhhg__head' },
						el( 'h2', { className: 'vhhg__title' }, heading ),
						subheading ? el( 'p', { className: 'vhhg__sub' }, subheading ) : null
					),
					el( 'div', { className: 'vhhg__viewport' },
						el( 'div', { className: 'vhhg__grid' },
							items.map( function ( it, i ) {
								return el( 'figure', { key: i, className: 'vhhg__tile' },
									it.tag ? el( 'figcaption', { className: 'vhhg__badge' }, it.tag ) : null,
									el( 'img', { className: 'vhhg__img', src: it.image, alt: it.title } )
								);
							} )
						)
					)
				);
			}

			return el( Fragment, {}, inspector, el( 'div', blockProps, preview ) );
		},
		save: function () { return null; },
	} );
} )( window.wp );
