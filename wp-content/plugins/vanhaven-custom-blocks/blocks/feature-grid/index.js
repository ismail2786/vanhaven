/**
 * VH Feature Grid — editor script (no build step).
 * Inline repeater: add/remove cards, edit fields directly, add/remove meta rows,
 * pick an icon per card. Columns + accent in the Inspector.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { InspectorControls, useBlockProps, RichText } = wp.blockEditor;
	const { PanelBody, RangeControl, TextControl, SelectControl, Button } = wp.components;
	const { __ } = wp.i18n;

	// Icon set mirrored from PHP (keys must match VHFG_Block::icon_svg).
	const ICONS = {
		shield: '<path d="M12 2 4 5v6c0 5 3.4 8.5 8 10 4.6-1.5 8-5 8-10V5l-8-3z"/>',
		gear: '<path d="M12 8a4 4 0 100 8 4 4 0 000-8z"/>',
		wrench: '<path d="M21 5a4 4 0 01-5 5l-7 7-3-3 7-7a4 4 0 015-5l-2.5 2.5L17 7l1.5-.5L21 4z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
		briefcase: '<rect x="3" y="7" width="18" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2" fill="none" stroke="currentColor" stroke-width="1.6"/>',
		users: '<circle cx="9" cy="8" r="3" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 20a6 6 0 0112 0M16 5a3 3 0 010 6M21 20a6 6 0 00-4-5.6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
		award: '<circle cx="12" cy="9" r="5" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M9 13l-1.5 8L12 18l4.5 3L15 13" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
		layers: '<path d="M12 3l9 5-9 5-9-5 9-5zM3 13l9 5 9-5M3 17l9 5 9-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
		chat: '<path d="M21 12a8 8 0 01-11.6 7.1L3 21l1.9-6.4A8 8 0 1121 12z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
		pin: '<path d="M12 21s7-5.7 7-11a7 7 0 10-14 0c0 5.3 7 11 7 11z" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="10" r="2.5" fill="none" stroke="currentColor" stroke-width="1.6"/>',
		phone: '<path d="M6 3h5l2 5-3 2a11 11 0 005 5l2-3 5 2v5a2 2 0 01-2 2A17 17 0 014 5a2 2 0 012-2z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
		calendar: '<rect x="3" y="5" width="18" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 9h18M8 3v4M16 3v4" fill="none" stroke="currentColor" stroke-width="1.6"/>',
		car: '<path d="M5 11l1.5-4.5A2 2 0 018.4 5h7.2a2 2 0 011.9 1.5L19 11M5 11h14v5H5zM5 16v2M19 16v2" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><circle cx="8" cy="16" r="1.3"/><circle cx="16" cy="16" r="1.3"/>',
		gauge: '<path d="M4 16a8 8 0 1116 0" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M12 16l4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
		sparkle: '<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
		check: '<path d="M5 12l4 4 10-10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
		star: '<path d="M12 3l2.9 6 6.1.9-4.5 4.3 1.1 6L12 17.8 6.4 20.2l1.1-6L3 9.9 9.1 9 12 3z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>'
	};
	const ICON_KEYS = Object.keys( ICONS );

	function iconSvg( name ) {
		const body = ICONS[ name ] || ICONS.shield;
		return { __html: '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">' + body + '</svg>' };
	}

	registerBlockType( 'vanhaven/feature-grid', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const { heading, subheading, columns, iconStyle, accentColor, cards } = attributes;

			const blockProps = useBlockProps( {
				className: 'vhfg vhfg--editor vhfg--cols-' + columns + ' vhfg--icon-' + iconStyle,
				style: { '--vhfg-accent': accentColor },
			} );

			function updateCard( index, patch ) {
				const next = cards.map( function ( c, i ) {
					return i === index ? Object.assign( {}, c, patch ) : c;
				} );
				setAttributes( { cards: next } );
			}

			function addCard() {
				setAttributes( {
					cards: cards.concat( [ {
						icon: 'star',
						title: __( 'New card', 'vanhaven-custom-blocks' ),
						description: '',
						rows: [],
						linkText: '',
						linkUrl: '',
					} ] ),
				} );
			}

			function removeCard( index ) {
				setAttributes( { cards: cards.filter( function ( c, i ) { return i !== index; } ) } );
			}

			function moveCard( index, dir ) {
				const target = index + dir;
				if ( target < 0 || target >= cards.length ) { return; }
				const next = cards.slice();
				const tmp = next[ index ];
				next[ index ] = next[ target ];
				next[ target ] = tmp;
				setAttributes( { cards: next } );
			}

			function updateRow( cardIndex, rowIndex, patch ) {
				const card = cards[ cardIndex ];
				const rows = ( card.rows || [] ).map( function ( r, i ) {
					return i === rowIndex ? Object.assign( {}, r, patch ) : r;
				} );
				updateCard( cardIndex, { rows: rows } );
			}

			function addRow( cardIndex ) {
				const card = cards[ cardIndex ];
				updateCard( cardIndex, { rows: ( card.rows || [] ).concat( [ { label: '', value: '' } ] ) } );
			}

			function removeRow( cardIndex, rowIndex ) {
				const card = cards[ cardIndex ];
				updateCard( cardIndex, { rows: ( card.rows || [] ).filter( function ( r, i ) { return i !== rowIndex; } ) } );
			}

			// ---- Inspector ----
			const inspector = el(
				InspectorControls,
				{},
				el(
					PanelBody,
					{ title: __( 'Layout', 'vanhaven-custom-blocks' ), initialOpen: true },
					el( RangeControl, {
						label: __( 'Columns', 'vanhaven-custom-blocks' ),
						value: columns,
						min: 2,
						max: 4,
						onChange: function ( v ) { setAttributes( { columns: v } ); },
					} ),
					el( SelectControl, {
						label: __( 'Icon style', 'vanhaven-custom-blocks' ),
						value: iconStyle,
						options: [
							{ label: __( 'Filled (orange circle)', 'vanhaven-custom-blocks' ), value: 'filled' },
							{ label: __( 'Outline', 'vanhaven-custom-blocks' ), value: 'outline' },
							{ label: __( 'None', 'vanhaven-custom-blocks' ), value: 'none' },
						],
						onChange: function ( v ) { setAttributes( { iconStyle: v } ); },
					} ),
					el( TextControl, {
						label: __( 'Accent color', 'vanhaven-custom-blocks' ),
						value: accentColor,
						onChange: function ( v ) { setAttributes( { accentColor: v } ); },
					} )
				),
				el(
					PanelBody,
					{ title: __( 'Section header (optional)', 'vanhaven-custom-blocks' ), initialOpen: false },
					el( TextControl, {
						label: __( 'Heading', 'vanhaven-custom-blocks' ),
						value: heading,
						onChange: function ( v ) { setAttributes( { heading: v } ); },
					} ),
					el( TextControl, {
						label: __( 'Subheading', 'vanhaven-custom-blocks' ),
						value: subheading,
						onChange: function ( v ) { setAttributes( { subheading: v } ); },
					} )
				)
			);

			// ---- Card editor tiles ----
			const cardEls = ( cards || [] ).map( function ( card, i ) {
				return el(
					'div',
					{ key: i, className: 'vhfg__card vhfg__card--edit' },

					// toolbar
					el( 'div', { className: 'vhfg__edit-toolbar' },
						el( Button, { icon: 'arrow-up-alt2', label: __( 'Move up', 'vanhaven-custom-blocks' ), onClick: function () { moveCard( i, -1 ); }, disabled: i === 0 } ),
						el( Button, { icon: 'arrow-down-alt2', label: __( 'Move down', 'vanhaven-custom-blocks' ), onClick: function () { moveCard( i, 1 ); }, disabled: i === cards.length - 1 } ),
						el( Button, { icon: 'trash', isDestructive: true, label: __( 'Remove card', 'vanhaven-custom-blocks' ), onClick: function () { removeCard( i ); } } )
					),

					// icon + picker
					iconStyle !== 'none'
						? el( 'div', { className: 'vhfg__icon-edit' },
							el( 'span', { className: 'vhfg__icon', dangerouslySetInnerHTML: iconSvg( card.icon ) } ),
							el( SelectControl, {
								value: card.icon,
								options: ICON_KEYS.map( function ( k ) { return { label: k, value: k }; } ),
								onChange: function ( v ) { updateCard( i, { icon: v } ); },
							} )
						  )
						: null,

					el( RichText, {
						tagName: 'h3',
						className: 'vhfg__card-title',
						value: card.title,
						allowedFormats: [],
						placeholder: __( 'Card title', 'vanhaven-custom-blocks' ),
						onChange: function ( v ) { updateCard( i, { title: v } ); },
					} ),

					el( RichText, {
						tagName: 'p',
						className: 'vhfg__card-desc',
						value: card.description,
						allowedFormats: [],
						placeholder: __( 'Description', 'vanhaven-custom-blocks' ),
						onChange: function ( v ) { updateCard( i, { description: v } ); },
					} ),

					// meta rows
					el( 'div', { className: 'vhfg__rows-edit' },
						( card.rows || [] ).map( function ( row, ri ) {
							return el( 'div', { key: ri, className: 'vhfg__row-edit' },
								el( TextControl, {
									placeholder: __( 'Label', 'vanhaven-custom-blocks' ),
									value: row.label,
									onChange: function ( v ) { updateRow( i, ri, { label: v } ); },
								} ),
								el( TextControl, {
									placeholder: __( 'Value', 'vanhaven-custom-blocks' ),
									value: row.value,
									onChange: function ( v ) { updateRow( i, ri, { value: v } ); },
								} ),
								el( Button, { icon: 'no-alt', label: __( 'Remove row', 'vanhaven-custom-blocks' ), onClick: function () { removeRow( i, ri ); } } )
							);
						} ),
						el( Button, { variant: 'secondary', className: 'vhfg__add-row', onClick: function () { addRow( i ); } }, __( '+ Add row', 'vanhaven-custom-blocks' ) )
					),

					// link
					el( 'details', { className: 'vhfg__link-edit' },
						el( 'summary', {}, __( 'Link (optional)', 'vanhaven-custom-blocks' ) ),
						el( TextControl, {
							label: __( 'Link text', 'vanhaven-custom-blocks' ),
							value: card.linkText,
							onChange: function ( v ) { updateCard( i, { linkText: v } ); },
						} ),
						el( TextControl, {
							label: __( 'Link URL', 'vanhaven-custom-blocks' ),
							value: card.linkUrl,
							onChange: function ( v ) { updateCard( i, { linkUrl: v } ); },
						} )
					)
				);
			} );

			return el(
				Fragment,
				{},
				inspector,
				el( 'div', blockProps,
					( heading || subheading )
						? el( 'div', { className: 'vhfg__head' },
							heading ? el( 'h2', { className: 'vhfg__title' }, heading ) : null,
							subheading ? el( 'p', { className: 'vhfg__sub' }, subheading ) : null
						  )
						: null,
					el( 'div', { className: 'vhfg__grid' }, cardEls ),
					el( 'div', { className: 'vhfg__add-card-wrap' },
						el( Button, { variant: 'primary', onClick: addCard }, __( '+ Add card', 'vanhaven-custom-blocks' ) )
					)
				)
			);
		},

		// Dynamic block (PHP render).
		save: function () { return null; },
	} );
} )( window.wp );
