/**
 * Lottie block editor.
 *
 * A "Source" panel lets the author pick between uploading a Lottie file (JSON or
 * .lottie) from the media library, or pasting a direct URL. The canvas previews the
 * animation live using the same bundled player as the front end — including .lottie
 * archives, which are fetched and unzipped in-place.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	Button,
	TextControl,
	Notice,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import { useEffect, useRef } from '@wordpress/element';
import lottie from 'lottie-web/build/player/lottie_light';
import { unzipSync, strFromU8 } from 'fflate';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

const isDotLottie = ( url ) => /\.lottie(\?|#|$)/i.test( url || '' );

function warnAboutUrl( source, url ) {
	if ( source !== 'url' || ! url ) {
		return null;
	}
	if ( /lottiefiles\.com\/(free-animation|animations|packages)/i.test( url ) ) {
		return __(
			'That is the LottieFiles page URL. Open the animation on LottieFiles, click “</> Get URL” and copy the Lottie JSON URL (usually https://lottie.host/…).',
			'etu-blocks'
		);
	}
	if ( ! /\.(json|lottie)(\?|#|$)/i.test( url ) && ! /lottie\.host/i.test( url ) ) {
		return __(
			'A Lottie source should be a direct .json or .lottie URL. If nothing shows up, this is likely the wrong link.',
			'etu-blocks'
		);
	}
	return null;
}

async function loadForPreview( canvas, url, loop, speedPct ) {
	if ( isDotLottie( url ) ) {
		const res = await window.fetch( url );
		const buf = new Uint8Array( await res.arrayBuffer() );
		const entries = unzipSync( buf );
		const key = Object.keys( entries ).find( ( name ) =>
			/^animations\/.+\.json$/i.test( name )
		);
		if ( ! key ) {
			throw new Error( 'no animation' );
		}
		const data = JSON.parse( strFromU8( entries[ key ] ) );
		const a = lottie.loadAnimation( {
			container: canvas,
			renderer: 'svg',
			loop: !! loop,
			autoplay: true,
			animationData: data,
		} );
		a.setSpeed( ( speedPct || 100 ) / 100 );
		return a;
	}
	const a = lottie.loadAnimation( {
		container: canvas,
		renderer: 'svg',
		loop: !! loop,
		autoplay: true,
		path: url,
	} );
	a.setSpeed( ( speedPct || 100 ) / 100 );
	return a;
}

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { source, src, file, trigger, loop, speedPct, blockId } = attributes;
	const canvasRef = useRef( null );

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( {
				blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ),
			} );
		}
	}, [ blockId, clientId, setAttributes ] );

	const activeUrl = source === 'file' ? file && file.url : src;

	useEffect( () => {
		if ( ! activeUrl || ! canvasRef.current ) {
			return undefined;
		}
		let anim;
		let cancelled = false;
		loadForPreview( canvasRef.current, activeUrl, loop, speedPct )
			.then( ( a ) => {
				if ( cancelled ) {
					a.destroy();
					return;
				}
				anim = a;
			} )
			.catch( () => {} );
		return () => {
			cancelled = true;
			if ( anim ) {
				anim.destroy();
			}
		};
	}, [ activeUrl, loop, speedPct ] );

	const blockProps = useBlockProps( {
		className: `etu-lottie etu-lottie--${ trigger } etu-lottie--editor ${
			blockId ? `etu-lottie-${ blockId }` : ''
		}`,
	} );

	const warning = warnAboutUrl( source, src );
	const fileName = file && file.url ? file.url.split( '/' ).pop() : '';

	return (
		<>
			<EtuInspector
				name="etu/lottie"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/lottie"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<InspectorControls>
				<PanelBody
					title={ __( 'Source', 'etu-blocks' ) }
					initialOpen={ true }
				>
					<ToggleGroupControl
						__nextHasNoMarginBottom
						isBlock
						label={ __( 'Source', 'etu-blocks' ) }
						value={ source || 'url' }
						onChange={ ( v ) => setAttributes( { source: v } ) }
					>
						<ToggleGroupControlOption
							value="file"
							label={ __( 'File', 'etu-blocks' ) }
						/>
						<ToggleGroupControlOption
							value="url"
							label={ __( 'URL', 'etu-blocks' ) }
						/>
					</ToggleGroupControl>

					{ source === 'file' ? (
						<div className="etu-lottie__file-picker">
							<MediaUploadCheck>
								<MediaUpload
									allowedTypes={ [ 'application/json', 'application/zip', 'application/octet-stream' ] }
									value={ file && file.id }
									onSelect={ ( media ) =>
										setAttributes( {
											file: {
												id: media.id,
												url: media.url,
											},
										} )
									}
									render={ ( { open } ) => (
										<>
											<Button
												variant="secondary"
												onClick={ open }
											>
												{ file && file.url
													? __( 'Replace file', 'etu-blocks' )
													: __( 'Upload file', 'etu-blocks' ) }
											</Button>
											{ file && file.url && (
												<Button
													variant="link"
													isDestructive
													onClick={ () =>
														setAttributes( {
															file: {},
														} )
													}
												>
													{ __( 'Remove', 'etu-blocks' ) }
												</Button>
											) }
											<p className="etu-lottie__hint">
												{ file && file.url
													? fileName
													: __(
															'Upload a Lottie JSON or .lottie file.',
															'etu-blocks'
													  ) }
											</p>
										</>
									) }
								/>
							</MediaUploadCheck>
						</div>
					) : (
						<TextControl
							__nextHasNoMarginBottom
							label={ __( 'Lottie URL', 'etu-blocks' ) }
							type="url"
							value={ src || '' }
							onChange={ ( v ) => setAttributes( { src: v } ) }
							placeholder="https://lottie.host/…/animation.json"
							help={ __(
								'Direct .json or .lottie URL. On LottieFiles use “</> Get URL” → Lottie JSON.',
								'etu-blocks'
							) }
						/>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ warning && (
					<Notice
						status="warning"
						isDismissible={ false }
						className="etu-lottie__editor-notice"
					>
						{ warning }
					</Notice>
				) }
				{ activeUrl ? (
					<div className="etu-lottie__canvas" ref={ canvasRef } />
				) : (
					<div className="etu-lottie__setup">
						<p className="etu-lottie__setup-title">
							{ __( 'Lottie animation', 'etu-blocks' ) }
						</p>
						<p className="etu-lottie__setup-help">
							{ source === 'file'
								? __(
										'Upload a Lottie JSON or .lottie file in the Source panel.',
										'etu-blocks'
								  )
								: __(
										'Paste a Lottie URL in the Source panel.',
										'etu-blocks'
								  ) }
						</p>
					</div>
				) }
			</div>
		</>
	);
}
