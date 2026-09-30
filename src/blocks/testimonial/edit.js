/**
 * Testimonial block editor.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import { Button } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		quote,
		authorName,
		authorRole,
		avatarUrl,
		avatarId,
		rating,
		showRating,
		preset,
		blockId,
	} = attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const blockProps = useBlockProps( {
		className: `etu-testimonial etu-testimonial--preset-${ preset } ${ blockId ? `etu-testimonial-${ blockId }` : '' } etu-testimonial--editor`,
	} );

	const stars = [];
	for ( let i = 0; i < 5; i++ ) {
		stars.push(
			<span
				key={ i }
				className={
					'etu-testimonial__star' +
					( i < Math.round( rating )
						? ''
						: ' etu-testimonial__star--empty' )
				}
			>
				★
			</span>
		);
	}

	return (
		<>
			<EtuInspector
				name="etu/testimonial"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/testimonial"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				{ showRating && (
					<div className="etu-testimonial__rating" aria-hidden="true">
						{ stars }
					</div>
				) }
				<RichText
					tagName="blockquote"
					className="etu-testimonial__quote"
					value={ quote }
					onChange={ ( value ) => setAttributes( { quote: value } ) }
					placeholder={ __( 'Testimonial quote…', 'etu-blocks' ) }
				/>
				<div className="etu-testimonial__author">
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( {
									avatarUrl: media.url,
									avatarId: media.id,
								} )
							}
							allowedTypes={ [ 'image' ] }
							value={ avatarId }
							render={ ( { open } ) =>
								avatarUrl ? (
									<button
										type="button"
										className="etu-testimonial__avatar-btn"
										onClick={ open }
									>
										<img
											className="etu-testimonial__avatar"
											src={ avatarUrl }
											alt=""
										/>
									</button>
								) : (
									<Button variant="secondary" onClick={ open }>
										{ __( 'Add avatar', 'etu-blocks' ) }
									</Button>
								)
							}
						/>
					</MediaUploadCheck>
					<div className="etu-testimonial__author-text">
						<RichText
							tagName="span"
							className="etu-testimonial__name"
							value={ authorName }
							onChange={ ( value ) =>
								setAttributes( { authorName: value } )
							}
							placeholder={ __( 'Name', 'etu-blocks' ) }
							allowedFormats={ [] }
						/>
						<RichText
							tagName="span"
							className="etu-testimonial__role"
							value={ authorRole }
							onChange={ ( value ) =>
								setAttributes( { authorRole: value } )
							}
							placeholder={ __( 'Role', 'etu-blocks' ) }
							allowedFormats={ [] }
						/>
					</div>
				</div>
			</div>
		</>
	);
}
