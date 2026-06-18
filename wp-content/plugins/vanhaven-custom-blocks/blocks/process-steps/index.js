/**
 * VH Process Steps — editor script (no build step).
 * Inline repeater for numbered steps.
 */
( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { InspectorControls, useBlockProps, RichText } = wp.blockEditor;
	const { PanelBody, RangeControl, TextControl, ToggleControl, Button } = wp.components;
	const { __ } = wp.i18n;

	function pad( n, on ) {
		if ( ! on ) { return String( n ); }
		const s = String( n );
		return s.length < 2 ? '0' + s : s;
	}

	registerBlockType( 'vanhaven/process-steps', {
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const { heading, subheading, columns, zeroPad, accentColor, steps } = attributes;

			const blockProps = useBlockProps( {
				className: 'vhps vhps--editor vhps--cols-' + columns,
				style: { '--vhps-accent': accentColor },
			} );

			function updateStep( index, patch ) {
				setAttributes( {
					steps: steps.map( function ( s, i ) {
						return i === index ? Object.assign( {}, s, patch ) : s;
					} ),
				} );
			}
			function addStep() {
				setAttributes( { steps: steps.concat( [ { number: '', title: __( 'New step', 'vanhaven-custom-blocks' ), description: '' } ] ) } );
			}
			function removeStep( index ) {
				setAttributes( { steps: steps.filter( function ( s, i ) { return i !== index; } ) } );
			}
			function moveStep( index, dir ) {
				const target = index + dir;
				if ( target < 0 || target >= steps.length ) { return; }
				const next = steps.slice();
				const tmp = next[ index ]; next[ index ] = next[ target ]; next[ target ] = tmp;
				setAttributes( { steps: next } );
			}

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
					el( ToggleControl, {
						label: __( 'Zero-pad numbers (01, 02...)', 'vanhaven-custom-blocks' ),
						checked: zeroPad,
						onChange: function ( v ) { setAttributes( { zeroPad: v } ); },
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

			const stepEls = ( steps || [] ).map( function ( step, i ) {
				const displayNum = step.number && step.number !== '' ? step.number : pad( i + 1, zeroPad );
				return el(
					'div',
					{ key: i, className: 'vhps__step vhps__step--edit' },
					el( 'div', { className: 'vhps__edit-toolbar' },
						el( Button, { icon: 'arrow-left-alt2', label: __( 'Move left', 'vanhaven-custom-blocks' ), onClick: function () { moveStep( i, -1 ); }, disabled: i === 0 } ),
						el( Button, { icon: 'arrow-right-alt2', label: __( 'Move right', 'vanhaven-custom-blocks' ), onClick: function () { moveStep( i, 1 ); }, disabled: i === steps.length - 1 } ),
						el( Button, { icon: 'trash', isDestructive: true, label: __( 'Remove step', 'vanhaven-custom-blocks' ), onClick: function () { removeStep( i ); } } )
					),
					el( 'span', { className: 'vhps__num' }, displayNum ),
					el( RichText, {
						tagName: 'h3',
						className: 'vhps__step-title',
						value: step.title,
						allowedFormats: [],
						placeholder: __( 'Step title', 'vanhaven-custom-blocks' ),
						onChange: function ( v ) { updateStep( i, { title: v } ); },
					} ),
					el( RichText, {
						tagName: 'p',
						className: 'vhps__step-desc',
						value: step.description,
						allowedFormats: [],
						placeholder: __( 'Description', 'vanhaven-custom-blocks' ),
						onChange: function ( v ) { updateStep( i, { description: v } ); },
					} ),
					el( 'details', { className: 'vhps__num-edit' },
						el( 'summary', {}, __( 'Custom number', 'vanhaven-custom-blocks' ) ),
						el( TextControl, {
							placeholder: __( 'Auto', 'vanhaven-custom-blocks' ),
							value: step.number,
							onChange: function ( v ) { updateStep( i, { number: v } ); },
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
						? el( 'div', { className: 'vhps__head' },
							heading ? el( 'h2', { className: 'vhps__title' }, heading ) : null,
							subheading ? el( 'p', { className: 'vhps__sub' }, subheading ) : null
						  )
						: null,
					el( 'div', { className: 'vhps__grid' }, stepEls ),
					el( 'div', { className: 'vhps__add-wrap' },
						el( Button, { variant: 'primary', onClick: addStep }, __( '+ Add step', 'vanhaven-custom-blocks' ) )
					)
				)
			);
		},
		save: function () { return null; },
	} );
} )( window.wp );
