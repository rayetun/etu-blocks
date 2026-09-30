/**
 * Progress Bar block editor.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { label, value, showValue, preset, blockId } = attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const clamped = Math.max( 0, Math.min( 100, Number( value ) || 0 ) );
	const blockProps = useBlockProps( {
		className: `etu-progress-bar etu-progress-bar--preset-${ preset } ${ blockId ? `etu-progress-bar-${ blockId }` : '' } etu-progress-bar--editor`,
	} );

	return (
		<>
			<EtuInspector
				name="etu/progress-bar"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/progress-bar"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				<div className="etu-progress-bar__label">
					<RichText
						tagName="span"
						className="etu-progress-bar__text"
						value={ label }
						onChange={ ( v ) => setAttributes( { label: v } ) }
						placeholder={ __( 'Label…', 'etu-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
					{ showValue && (
						<span className="etu-progress-bar__value">
							{ clamped }%
						</span>
					) }
				</div>
				<div className="etu-progress-bar__track">
					<div
						className="etu-progress-bar__fill"
						style={ { width: `${ clamped }%` } }
					/>
				</div>
			</div>
		</>
	);
}
