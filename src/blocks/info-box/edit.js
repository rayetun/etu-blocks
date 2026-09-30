/**
 * Info Box block editor.
 *
 * Canvas: inline-editable icon (from Inspector), title, description and button
 * label. Inspector: the shared schema-driven EtuInspector.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		icon,
		title,
		description,
		buttonText,
		preset,
		iconPosition,
		headingLevel,
		blockId,
	} = attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const blockProps = useBlockProps( {
		className: `etu-info-box etu-info-box--preset-${ preset } etu-info-box--${ iconPosition } ${ blockId ? `etu-info-box-${ blockId }` : '' } etu-info-box--editor`,
	} );

	return (
		<>
			<EtuInspector
				name="etu/info-box"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/info-box"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				{ '' !== icon && (
					<span className="etu-info-box__icon" aria-hidden="true">
						{ icon }
					</span>
				) }
				<div className="etu-info-box__body">
					<RichText
						tagName={ headingLevel }
						className="etu-info-box__title"
						value={ title }
						onChange={ ( value ) => setAttributes( { title: value } ) }
						placeholder={ __( 'Info box title…', 'etu-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
					<RichText
						tagName="div"
						className="etu-info-box__description"
						value={ description }
						onChange={ ( value ) =>
							setAttributes( { description: value } )
						}
						placeholder={ __( 'Description…', 'etu-blocks' ) }
					/>
					<RichText
						tagName="span"
						className="etu-info-box__button"
						value={ buttonText }
						onChange={ ( value ) =>
							setAttributes( { buttonText: value } )
						}
						placeholder={ __( 'Button label…', 'etu-blocks' ) }
						allowedFormats={ [] }
					/>
				</div>
			</div>
		</>
	);
}
