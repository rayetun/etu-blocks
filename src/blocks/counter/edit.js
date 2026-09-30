/**
 * Counter block editor.
 *
 * Canvas: a static preview of the number (edited in the Inspector) + an inline-
 * editable label. Inspector: the shared schema-driven EtuInspector.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { number, prefix, suffix, label, separator, preset, blockId } =
		attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const blockProps = useBlockProps( {
		className: `etu-counter etu-counter--preset-${ preset } ${ blockId ? `etu-counter-${ blockId }` : '' } etu-counter--editor`,
	} );

	const num =
		parseFloat( String( number ).replace( /[^0-9.\-]/g, '' ) ) || 0;
	const display = separator
		? Math.round( num ).toLocaleString()
		: String( Math.round( num ) );

	return (
		<>
			<EtuInspector
				name="etu/counter"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/counter"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				<div className="etu-counter__number">
					{ prefix && (
						<span className="etu-counter__prefix">{ prefix }</span>
					) }
					<span className="etu-counter__value">{ display }</span>
					{ suffix && (
						<span className="etu-counter__suffix">{ suffix }</span>
					) }
				</div>
				<RichText
					tagName="div"
					className="etu-counter__label"
					value={ label }
					onChange={ ( value ) => setAttributes( { label: value } ) }
					placeholder={ __( 'Label…', 'etu-blocks' ) }
					allowedFormats={ [ 'core/bold', 'core/italic' ] }
				/>
			</div>
		</>
	);
}
