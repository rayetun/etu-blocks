/**
 * CTA block editor.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, description, primaryText, secondaryText, preset, blockId } =
		attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const blockProps = useBlockProps( {
		className: `etu-cta etu-cta--preset-${ preset } ${ blockId ? `etu-cta-${ blockId }` : '' } etu-cta--editor`,
	} );

	return (
		<>
			<EtuInspector
				name="etu/cta"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/cta"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				<div className="etu-cta__content">
					<RichText
						tagName="h2"
						className="etu-cta__heading"
						value={ heading }
						onChange={ ( value ) => setAttributes( { heading: value } ) }
						placeholder={ __( 'Heading…', 'etu-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
					<RichText
						tagName="div"
						className="etu-cta__description"
						value={ description }
						onChange={ ( value ) =>
							setAttributes( { description: value } )
						}
						placeholder={ __( 'Description…', 'etu-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
				</div>
				<div className="etu-cta__actions">
					<RichText
						tagName="span"
						className="etu-cta__button etu-cta__button--primary"
						value={ primaryText }
						onChange={ ( value ) =>
							setAttributes( { primaryText: value } )
						}
						placeholder={ __( 'Primary', 'etu-blocks' ) }
						allowedFormats={ [] }
					/>
					<RichText
						tagName="span"
						className="etu-cta__button etu-cta__button--secondary"
						value={ secondaryText }
						onChange={ ( value ) =>
							setAttributes( { secondaryText: value } )
						}
						placeholder={ __( 'Secondary', 'etu-blocks' ) }
						allowedFormats={ [] }
					/>
				</div>
			</div>
		</>
	);
}
