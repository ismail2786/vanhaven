/**
 * VanHaven Bespoke Solutions — editor script (no build).
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment, useState, useEffect } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, RangeControl, TextControl, TextareaControl, Spinner, Notice, Button } = wp.components;
	const { __ } = wp.i18n;
	const apiFetch = wp.apiFetch;

	registerBlockType( 'vanhaven/solutions', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const { heading, subheading, limit, accentColor } = attributes;

			const [ items, setItems ] = useState( [] );
			const [ active, setActive ] = useState( 0 );
			const [ loading, setLoading ] = useState( true );

			useEffect( function () {
				apiFetch( { path: '/vanhaven-solutions/v1/items?limit=' + limit } ).then( function ( res ) {
					setItems( res || [] );
					setLoading( false );
				} );
			}, [ limit ] );

			const blockProps = useBlockProps( {
				className: 'vhs vhs--editor',
				style: { '--vhs-accent': accentColor },
			} );

			const inspector = el(
				InspectorControls,
				{},
				el(
					PanelBody,
					{ title: __( 'Content', 'vanhaven-solutions' ), initialOpen: true },
					el( TextControl, {
						label: __( 'Heading', 'vanhaven-solutions' ),
						value: heading,
						onChange: function ( v ) { setAttributes( { heading: v } ); },
					} ),
					el( TextareaControl, {
						label: __( 'Subheading', 'vanhaven-solutions' ),
						value: subheading,
						onChange: function ( v ) { setAttributes( { subheading: v } ); },
					} ),
					el( RangeControl, {
						label: __( 'Max solutions', 'vanhaven-solutions' ),
						value: limit,
						min: 1,
						max: 30,
						onChange: function ( v ) { setAttributes( { limit: v } ); },
					} ),
					el( TextControl, {
						label: __( 'Accent color', 'vanhaven-solutions' ),
						value: accentColor,
						onChange: function ( v ) { setAttributes( { accentColor: v } ); },
					} ),
					el( 'p', { style: { fontSize: '12px', opacity: 0.8 } },
						__( 'Add or edit each solution (tab) under "Solutions" in the admin menu.', 'vanhaven-solutions' )
					)
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
						__( 'No solutions found. ', 'vanhaven-solutions' ),
						el( Button, {
							variant: 'link',
							href: ( window.vhsAdmin && window.vhsAdmin.newUrl ) || '/wp-admin/post-new.php?post_type=vh_solution',
							target: '_blank',
						}, __( 'Add your first solution', 'vanhaven-solutions' ) )
					)
				);
			} else {
				const cur = items[ active ] || items[ 0 ];
				preview = el(
					Fragment,
					{},
					el( 'div', { className: 'vhs__head' },
						el( 'h2', { className: 'vhs__title' }, heading ),
						subheading ? el( 'p', { className: 'vhs__sub' }, subheading ) : null
					),
					el( 'div', { className: 'vhs__tabs', role: 'tablist' },
						items.map( function ( it, i ) {
							return el( 'button', {
								key: i,
								type: 'button',
								className: 'vhs__tab' + ( i === active ? ' is-active' : '' ),
								onClick: function () { setActive( i ); },
							}, it.tabLabel );
						} )
					),
					el( 'div', { className: 'vhs__viewport' },
						el( 'div', { className: 'vhs__track vhs__track--editor' },
							el( 'article', { className: 'vhs__slide is-active' },
								el( 'div', { className: 'vhs__card' },
									el( 'div', { className: 'vhs__content' },
										el( 'h3', { className: 'vhs__heading' }, cur.heading ),
										cur.description ? el( 'p', { className: 'vhs__desc' }, cur.description ) : null,
										cur.features && cur.features.length
											? el( 'ul', { className: 'vhs__features' },
												cur.features.map( function ( f, fi ) {
													return el( 'li', { key: fi, className: 'vhs__feature' }, f );
												} )
											  )
											: null,
										cur.ctaLabel
											? el( 'span', { className: 'vhs__cta' }, cur.ctaLabel + ' \u2197' )
											: null
									),
									el( 'div', { className: 'vhs__media' },
										cur.badge ? el( 'span', { className: 'vhs__badge' }, cur.badge ) : null,
										cur.image ? el( 'img', { className: 'vhs__img', src: cur.image, alt: cur.heading } ) : null
									)
								)
							)
						)
					)
				);
			}

			return el( Fragment, {}, inspector, el( 'div', blockProps, preview ) );
		},
		save: function () { return null; },
	} );
} )( window.wp );
