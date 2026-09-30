/**
 * Video Popup block editor.
 *
 * Shows the poster facade (thumbnail + play badge + optional title) exactly as it
 * renders on the front end; the dialog itself is front-end-only. When no source is
 * set yet we show a setup panel. YouTube posters fall back to the video thumbnail.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

const parseId = ( value, type ) => {
	const v = ( value || '' ).trim();
	if ( type === 'youtube' ) {
		const m = v.match(
			/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/|v\/))([A-Za-z0-9_-]{11})/
		);
		if ( m ) {
			return m[ 1 ];
		}
		return /^[A-Za-z0-9_-]{11}$/.test( v ) ? v : '';
	}
	const m = v.match( /vimeo\.com\/(?:video\/)?(\d+)/ );
	if ( m ) {
		return m[ 1 ];
	}
	return /^\d+$/.test( v ) ? v : '';
};

const PlayIcon = () => (
	<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
		<path d="M8 5v14l11-7z" />
	</svg>
);

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		videoType,
		videoUrl,
		videoFile,
		posterImage,
		title,
		showTitle,
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

	const ytId = videoType === 'youtube' ? parseId( videoUrl, 'youtube' ) : '';
	const hasSource =
		videoType === 'self'
			? !! ( videoFile && videoFile.url )
			: !! parseId( videoUrl, videoType );

	let posterUrl = posterImage && posterImage.url ? posterImage.url : '';
	if ( ! posterUrl && ytId ) {
		posterUrl = `https://i.ytimg.com/vi/${ ytId }/hqdefault.jpg`;
	}

	const blockProps = useBlockProps( {
		className: `etu-video-popup etu-video-popup--ar-${ aspectRatio } etu-video-popup--${ videoType } etu-video-popup--editor ${
			blockId ? `etu-video-popup-${ blockId }` : ''
		}`,
	} );

	return (
		<>
			<EtuInspector
				name="etu/video-popup"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/video-popup"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				{ hasSource || posterUrl ? (
					<div className="etu-video-popup__poster">
						{ posterUrl ? (
							<img
								className="etu-video-popup__thumb"
								src={ posterUrl }
								alt=""
							/>
						) : (
							<span className="etu-video-popup__thumb etu-video-popup__thumb--blank" />
						) }
						<span className="etu-video-popup__overlay" />
						<span className="etu-video-popup__play">
							<PlayIcon />
						</span>
						{ showTitle && title && (
							<span className="etu-video-popup__title">
								{ title }
							</span>
						) }
					</div>
				) : (
					<div className="etu-video-popup__setup">
						<span className="etu-video-popup__setup-icon">
							<PlayIcon />
						</span>
						<p className="etu-video-popup__setup-title">
							{ __( 'Video Popup', 'etu-blocks' ) }
						</p>
						<p className="etu-video-popup__setup-help">
							{ videoType === 'self'
								? __(
										'Choose a video file, or set a poster image.',
										'etu-blocks'
								  )
								: __(
										'Paste a YouTube or Vimeo URL in the block settings.',
										'etu-blocks'
								  ) }
						</p>
						{ videoType === 'self' && (
							<MediaUploadCheck>
								<MediaUpload
									allowedTypes={ [ 'video' ] }
									value={ videoFile && videoFile.id }
									onSelect={ ( media ) =>
										setAttributes( {
											videoFile: {
												id: media.id,
												url: media.url,
											},
										} )
									}
									render={ ( { open } ) => (
										<Button
											variant="secondary"
											size="small"
											onClick={ open }
										>
											{ __(
												'Select video',
												'etu-blocks'
											) }
										</Button>
									) }
								/>
							</MediaUploadCheck>
						) }
					</div>
				) }
			</div>
		</>
	);
}
