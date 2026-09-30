/**
 * Before / After block editor.
 *
 * Mirrors the server markup so the editor preview matches the front end: the
 * "after" image is the base, the "before" image is a clipped overlay, and a range
 * input sets the reveal position live. When either image is missing we show a
 * setup panel with a picker for each slot.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

const clamp = ( n ) => Math.max( 0, Math.min( 100, Number( n ) || 0 ) );

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		beforeImage,
		afterImage,
		beforeLabel,
		afterLabel,
		showLabels,
		startPosition,
		orientation,
		aspectRatio,
		blockId,
	} = attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( {
				blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ),
			} );
		}
	}, [ blockId, clientId, setAttributes ] );

	const pick = ( key ) => ( media ) =>
		setAttributes( {
			[ key ]: { id: media.id, url: media.url, alt: media.alt || '' },
		} );

	const hasBefore = beforeImage && beforeImage.url;
	const hasAfter = afterImage && afterImage.url;

	const blockProps = useBlockProps( {
		className: `etu-before-after etu-before-after--${ orientation } etu-before-after--ar-${ aspectRatio } etu-before-after--editor ${
			blockId ? `etu-before-after-${ blockId }` : ''
		}`,
	} );

	const p = clamp( startPosition );
	const beforeClip =
		orientation === 'vertical'
			? `inset(0 0 ${ 100 - p }% 0)`
			: `inset(0 ${ 100 - p }% 0 0)`;
	const dividerStyle =
		orientation === 'vertical' ? { top: `${ p }%` } : { left: `${ p }%` };

	const slotButton = ( key, img, label ) => (
		<MediaUploadCheck>
			<MediaUpload
				allowedTypes={ [ 'image' ] }
				value={ img && img.id }
				onSelect={ pick( key ) }
				render={ ( { open } ) => (
					<div className="etu-before-after__slot">
						{ img && img.url ? (
							<img src={ img.url } alt="" />
						) : (
							<span className="etu-before-after__slot-empty" />
						) }
						<Button variant="secondary" size="small" onClick={ open }>
							{ img && img.url
								? /* translators: %s: slot name (Before/After). */
								  __( 'Replace', 'etu-blocks' )
								: label }
						</Button>
					</div>
				) }
			/>
		</MediaUploadCheck>
	);

	return (
		<>
			<EtuInspector
				name="etu/before-after"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/before-after"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				{ hasBefore && hasAfter ? (
					<div className="etu-before-after__frame">
						<img
							className="etu-before-after__image etu-before-after__image--after"
							src={ afterImage.url }
							alt=""
						/>
						<input
							type="range"
							className="etu-before-after__range"
							min="0"
							max="100"
							step="1"
							value={ p }
							aria-label={ __( 'Reveal amount', 'etu-blocks' ) }
							onChange={ ( e ) =>
								setAttributes( {
									startPosition: clamp( e.target.value ),
								} )
							}
						/>
						<div
							className="etu-before-after__before"
							style={ { clipPath: beforeClip } }
						>
							<img
								className="etu-before-after__image etu-before-after__image--before"
								src={ beforeImage.url }
								alt=""
							/>
						</div>
						{ showLabels && (
							<>
								{ beforeLabel && (
									<span className="etu-before-after__label etu-before-after__label--before">
										{ beforeLabel }
									</span>
								) }
								{ afterLabel && (
									<span className="etu-before-after__label etu-before-after__label--after">
										{ afterLabel }
									</span>
								) }
							</>
						) }
						<div
							className="etu-before-after__divider"
							style={ dividerStyle }
						>
							<span className="etu-before-after__handle" />
						</div>
					</div>
				) : (
					<div className="etu-before-after__setup">
						<p className="etu-before-after__setup-title">
							{ __( 'Before / After comparison', 'etu-blocks' ) }
						</p>
						<p className="etu-before-after__setup-help">
							{ __(
								'Choose the two images to compare.',
								'etu-blocks'
							) }
						</p>
						<div className="etu-before-after__slots">
							{ slotButton(
								'beforeImage',
								beforeImage,
								__( 'Select before image', 'etu-blocks' )
							) }
							{ slotButton(
								'afterImage',
								afterImage,
								__( 'Select after image', 'etu-blocks' )
							) }
						</div>
					</div>
				) }
			</div>
		</>
	);
}
