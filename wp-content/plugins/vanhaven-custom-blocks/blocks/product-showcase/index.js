/**
 * VanHaven Product Showcase — editor script.
 * No build step required; uses WordPress global packages (wp.element, wp.blocks, etc).
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment, useState, useEffect } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const {
		PanelBody,
		RangeControl,
		TextControl,
		TextareaControl,
		FormTokenField,
		Spinner,
		Notice,
	} = wp.components;
	const { __ } = wp.i18n;
	const apiFetch = wp.apiFetch;

	registerBlockType( 'vanhaven/product-showcase', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const {
				heading,
				subheading,
				categories,
				limit,
				ctaLabel,
				badgeMetaKey,
				accentColor,
			} = attributes;

			const [ tabs, setTabs ] = useState( [] );
			const [ products, setProducts ] = useState( [] );
			const [ activeCat, setActiveCat ] = useState( 0 );
			const [ loading, setLoading ] = useState( true );

			// Load all available product categories for the token picker.
			useEffect( function () {
				apiFetch( { path: '/vanhaven/v1/tabs' } ).then( function ( res ) {
					setTabs( res || [] );
					setLoading( false );
				} );
			}, [] );

			// Determine which tabs to display (selected or all).
			const displayTabs =
				categories && categories.length
					? tabs.filter( function ( t ) {
							return categories.indexOf( t.id ) !== -1;
					  } )
					: tabs;

			// Load preview products when active tab / settings change.
			useEffect(
				function () {
					const cat = activeCat || ( displayTabs[ 0 ] ? displayTabs[ 0 ].id : 0 );
					apiFetch( {
						path:
							'/vanhaven/v1/products?category=' +
							cat +
							'&limit=' +
							limit +
							'&badgeMetaKey=' +
							encodeURIComponent( badgeMetaKey || '' ),
					} ).then( function ( res ) {
						setProducts( res || [] );
					} );
				},
				[ activeCat, limit, badgeMetaKey, tabs.length ]
			);

			const tabNamesById = {};
			tabs.forEach( function ( t ) {
				tabNamesById[ t.name ] = t.id;
			} );

			const blockProps = useBlockProps( {
				className: 'vhsc vhsc--editor',
				style: { '--vhsc-accent': accentColor },
			} );

			// ---- Inspector ----
			const inspector = el(
				InspectorControls,
				{},
				el(
					PanelBody,
					{ title: __( 'Tabs & Source', 'vanhaven-showcase' ), initialOpen: true },
					el( FormTokenField, {
						label: __( 'Categories shown as tabs', 'vanhaven-showcase' ),
						help: __( 'Leave empty to show all product categories.', 'vanhaven-showcase' ),
						value: ( categories || [] )
							.map( function ( id ) {
								const found = tabs.find( function ( t ) {
									return t.id === id;
								} );
								return found ? found.name : null;
							} )
							.filter( Boolean ),
						suggestions: tabs.map( function ( t ) {
							return t.name;
						} ),
						onChange: function ( names ) {
							const ids = names
								.map( function ( n ) {
									return tabNamesById[ n ];
								} )
								.filter( function ( v ) {
									return typeof v !== 'undefined';
								} );
							setAttributes( { categories: ids } );
						},
					} ),
					el( RangeControl, {
						label: __( 'Products per tab', 'vanhaven-showcase' ),
						value: limit,
						min: 1,
						max: 20,
						onChange: function ( v ) {
							setAttributes( { limit: v } );
						},
					} )
				),
				el(
					PanelBody,
					{ title: __( 'Content', 'vanhaven-showcase' ), initialOpen: false },
					el( TextControl, {
						label: __( 'Heading', 'vanhaven-showcase' ),
						value: heading,
						onChange: function ( v ) {
							setAttributes( { heading: v } );
						},
					} ),
					el( TextareaControl, {
						label: __( 'Subheading', 'vanhaven-showcase' ),
						value: subheading,
						onChange: function ( v ) {
							setAttributes( { subheading: v } );
						},
					} ),
					el( TextControl, {
						label: __( 'Button label', 'vanhaven-showcase' ),
						value: ctaLabel,
						onChange: function ( v ) {
							setAttributes( { ctaLabel: v } );
						},
					} )
				),
				el(
					PanelBody,
					{ title: __( 'Advanced', 'vanhaven-showcase' ), initialOpen: false },
					el( TextControl, {
						label: __( 'Badge meta key', 'vanhaven-showcase' ),
						help: __( 'Custom field key to show as a badge (e.g. ready_status). Falls back to "Featured".', 'vanhaven-showcase' ),
						value: badgeMetaKey,
						onChange: function ( v ) {
							setAttributes( { badgeMetaKey: v } );
						},
					} ),
					el( TextControl, {
						label: __( 'Accent color', 'vanhaven-showcase' ),
						type: 'text',
						value: accentColor,
						onChange: function ( v ) {
							setAttributes( { accentColor: v } );
						},
					} )
				)
			);

			// ---- Editor preview ----
			let preview;
			if ( loading ) {
				preview = el( Spinner );
			} else if ( ! displayTabs.length ) {
				preview = el(
					Notice,
					{ status: 'warning', isDismissible: false },
					__( 'No product categories found. Create WooCommerce products & categories first.', 'vanhaven-showcase' )
				);
			} else {
				preview = el(
					Fragment,
					{},
					el(
						'div',
						{ className: 'vhsc__head' },
						el( 'h2', { className: 'vhsc__title' }, heading ),
						subheading ? el( 'p', { className: 'vhsc__sub' }, subheading ) : null
					),
					el(
						'div',
						{ className: 'vhsc__tabs', role: 'tablist' },
						displayTabs.map( function ( t, i ) {
							const isActive = activeCat ? activeCat === t.id : i === 0;
							return el(
								'button',
								{
									key: t.id + '-' + i,
									type: 'button',
									className: 'vhsc__tab' + ( isActive ? ' is-active' : '' ),
									onClick: function () {
										setActiveCat( t.id );
									},
								},
								t.name
							);
						} )
					),
					el(
						'div',
						{ className: 'vhsc__viewport' },
						el(
							'div',
							{ className: 'vhsc__rail' },
							products.length
								? products.map( function ( p, idx ) {
										return el(
											'article',
											{ key: idx, className: 'vhsc__card' },
											el(
												'div',
												{ className: 'vhsc__media' },
												p.badge
													? el( 'span', { className: 'vhsc__badge' }, p.badge )
													: null,
												el( 'img', {
													className: 'vhsc__img',
													src: p.image,
													alt: p.title,
												} ),
												el( 'div', { className: 'vhsc__overlay' } ),
												el(
													'div',
													{ className: 'vhsc__body' },
													el( 'h3', { className: 'vhsc__name' }, p.title ),
													p.attributes && p.attributes.length
														? el(
																'p',
																{ className: 'vhsc__specs' },
																p.attributes.join( '  |  ' )
														  )
														: null,
													el(
														'div',
														{ className: 'vhsc__foot' },
														el( 'span', {
															className: 'vhsc__price',
															dangerouslySetInnerHTML: { __html: p.priceHtml },
														} ),
														el(
															'span',
															{ className: 'vhsc__cta' },
															ctaLabel + ' \u2197'
														)
													)
												)
											)
										);
								  } )
								: el( 'p', { className: 'vhsc__empty' }, __( 'No products in this category.', 'vanhaven-showcase' ) )
						)
					)
				);
			}

			return el( Fragment, {}, inspector, el( 'div', blockProps, preview ) );
		},

		// Dynamic block — rendered in PHP.
		save: function () {
			return null;
		},
	} );
} )( window.wp );
