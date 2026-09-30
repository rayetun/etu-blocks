/**
 * Tabs block editor.
 *
 * Canvas: inline-editable tab labels + active panel content. Inspector: the
 * shared schema-driven EtuInspector (Layout / Style / Advanced) — no per-block
 * control code.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { tabs, preset, blockId } = attributes;
	const [ active, setActive ] = useState( 0 );

	// Stable per-instance id for CSS scoping.
	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const blockProps = useBlockProps( {
		className: `etu-tabs etu-tabs--preset-${ preset } ${ blockId ? `etu-tabs-${ blockId }` : '' } etu-tabs--editor`,
	} );

	const current = Math.min( active, tabs.length - 1 );

	const updateTab = ( index, key, value ) => {
		const next = tabs.map( ( tab, i ) =>
			i === index ? { ...tab, [ key ]: value } : tab
		);
		setAttributes( { tabs: next } );
	};

	const addTab = () => {
		setAttributes( { tabs: [ ...tabs, { label: '', content: '' } ] } );
		setActive( tabs.length );
	};

	const removeTab = ( index ) => {
		setAttributes( { tabs: tabs.filter( ( _, i ) => i !== index ) } );
		setActive( Math.max( 0, index - 1 ) );
	};

	return (
		<>
			<EtuInspector
				name="etu/tabs"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/tabs"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				<div className="etu-tabs__list" role="tablist">
					{ tabs.map( ( tab, index ) => (
						<div
							key={ index }
							className={
								'etu-tabs__tab' +
								( index === current ? ' is-active' : '' )
							}
						>
							<RichText
								tagName="span"
								value={ tab.label }
								onChange={ ( value ) =>
									updateTab( index, 'label', value )
								}
								onFocus={ () => setActive( index ) }
								placeholder={ __( 'Tab label…', 'etu-blocks' ) }
								allowedFormats={ [ 'core/bold', 'core/italic' ] }
							/>
							<Button
								variant="link"
								isDestructive
								label={ __( 'Remove tab', 'etu-blocks' ) }
								onClick={ () => removeTab( index ) }
							>
								×
							</Button>
						</div>
					) ) }
					<Button variant="secondary" onClick={ addTab }>
						{ __( 'Add tab', 'etu-blocks' ) }
					</Button>
				</div>

				{ tabs[ current ] && (
					<div className="etu-tabs__panel">
						<RichText
							tagName="div"
							className="etu-tabs__content"
							value={ tabs[ current ].content }
							onChange={ ( value ) =>
								updateTab( current, 'content', value )
							}
							placeholder={ __( 'Tab content…', 'etu-blocks' ) }
						/>
					</div>
				) }
			</div>
		</>
	);
}
