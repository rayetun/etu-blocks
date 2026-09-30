/**
 * Banner (Hero) block editor.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { Button, Popover, TextControl } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { link as linkIcon } from '@wordpress/icons';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		eyebrow,
		heading,
		text,
		primaryText,
		primaryUrl,
		secondaryText,
		secondaryUrl,
		bgImage,
		heightPreset,
		contentAlign,
		verticalAlign,
		blockId,
	} = attributes;
	const [ linkOpen, setLinkOpen ] = useState( null );

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const hasImage = bgImage && bgImage.url;
	const blockProps = useBlockProps( {
		className: `etu-banner etu-banner--h-${ heightPreset } etu-banner--align-${ contentAlign } etu-banner--valign-${ verticalAlign } etu-banner--editor ${
			hasImage ? 'etu-banner--has-image' : ''
		} ${ blockId ? `etu-banner-${ blockId }` : '' }`,
		style: hasImage ? { backgroundImage: `url(${ bgImage.url })` } : undefined,
	} );

	const btnLink = ( which, urlKey, urlVal ) => (
		<>
			<Button
				size="small"
				icon={ linkIcon }
				isPressed={ !! urlVal }
				label={ __( 'Set link', 'etu-blocks' ) }
				onClick={ () => setLinkOpen( linkOpen === which ? null : which ) }
			/>
			{ linkOpen === which && (
				<Popover placement="bottom" onClose={ () => setLinkOpen( null ) }>
					<div className="etu-banner__link-popover">
						<TextControl
							__nextHasNoMarginBottom
							label={ __( 'Button URL', 'etu-blocks' ) }
							type="url"
							value={ urlVal }
							onChange={ ( v ) => setAttributes( { [ urlKey ]: v } ) }
							placeholder="https://…"
						/>
					</div>
				</Popover>
			) }
		</>
	);

	return (
		<>
			<EtuInspector
				name="etu/banner"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/banner"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				<div className="etu-banner__overlay" aria-hidden="true" />
				<div className="etu-banner__content">
					<RichText
						tagName="p"
						className="etu-banner__eyebrow"
						value={ eyebrow }
						onChange={ ( v ) => setAttributes( { eyebrow: v } ) }
						placeholder={ __( 'Eyebrow (optional)', 'etu-blocks' ) }
						allowedFormats={ [] }
					/>
					<RichText
						tagName="h2"
						className="etu-banner__heading"
						value={ heading }
						onChange={ ( v ) => setAttributes( { heading: v } ) }
						placeholder={ __( 'Headline…', 'etu-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
					<RichText
						tagName="p"
						className="etu-banner__text"
						value={ text }
						onChange={ ( v ) => setAttributes( { text: v } ) }
						placeholder={ __( 'Supporting text…', 'etu-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
					/>
					<div className="etu-banner__buttons">
						<span className="etu-banner__btn etu-banner__btn--primary">
							<RichText
								tagName="span"
								value={ primaryText }
								onChange={ ( v ) =>
									setAttributes( { primaryText: v } )
								}
								placeholder={ __( 'Primary', 'etu-blocks' ) }
								allowedFormats={ [] }
							/>
						</span>
						{ btnLink( 'primary', 'primaryUrl', primaryUrl ) }
						<span className="etu-banner__btn etu-banner__btn--secondary">
							<RichText
								tagName="span"
								value={ secondaryText }
								onChange={ ( v ) =>
									setAttributes( { secondaryText: v } )
								}
								placeholder={ __(
									'Secondary (optional)',
									'etu-blocks'
								) }
								allowedFormats={ [] }
							/>
						</span>
						{ btnLink( 'secondary', 'secondaryUrl', secondaryUrl ) }
					</div>
				</div>
			</div>
		</>
	);
}
