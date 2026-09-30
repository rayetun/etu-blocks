/**
 * Team Member block editor.
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

const ICONS = {
	twitter:
		'M18.9 1.9h3.68l-8.04 9.19L24 22.1h-7.41l-5.8-7.58L4.2 22.1H.5l8.6-9.83L0 1.9h7.6l5.24 6.93L18.9 1.9z',
	linkedin:
		'M4.98 3.5a2.5 2.5 0 11-.01 5.01A2.5 2.5 0 014.98 3.5zM3 9h4v12H3V9zm6.5 0H13v1.64h.05c.49-.93 1.7-1.9 3.5-1.9 3.74 0 4.43 2.46 4.43 5.66V21H20v-5.7c0-1.34-.03-3.07-1.87-3.07-1.87 0-2.16 1.46-2.16 2.97V21h-4V9z',
	github:
		'M12 2a10 10 0 00-3.16 19.49c.5.09.68-.22.68-.48v-1.7c-2.78.6-3.37-1.34-3.37-1.34-.45-1.16-1.1-1.47-1.1-1.47-.9-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.9 1.52 2.34 1.08 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.94 0-1.09.39-1.98 1.03-2.68-.1-.25-.45-1.27.1-2.65 0 0 .84-.27 2.75 1.02a9.56 9.56 0 015 0c1.91-1.29 2.75-1.02 2.75-1.02.55 1.38.2 2.4.1 2.65.64.7 1.03 1.59 1.03 2.68 0 3.84-2.34 4.68-4.57 4.93.36.31.68.92.68 1.85v2.74c0 .27.18.58.69.48A10 10 0 0012 2z',
	website:
		'M12 2a10 10 0 100 20 10 10 0 000-20zm6.93 6h-2.95a15.7 15.7 0 00-1.38-3.56A8.03 8.03 0 0118.93 8zM12 4c.83 1.2 1.48 2.53 1.91 4h-3.82C10.52 6.53 11.17 5.2 12 4zM4.26 14a7.96 7.96 0 010-4h3.38a16.5 16.5 0 000 4H4.26zm.81 2h2.95c.32 1.25.78 2.45 1.38 3.56A8.03 8.03 0 015.07 16zm2.95-8H5.07a8.03 8.03 0 014.33-3.56A15.7 15.7 0 008.02 8zM12 20c-.83-1.2-1.48-2.53-1.91-4h3.82C13.48 17.47 12.83 18.8 12 20zm2.34-6H9.66a14.6 14.6 0 010-4h4.68a14.6 14.6 0 010 4zm.27 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95a8.03 8.03 0 01-4.33 3.56zM16.36 14a16.5 16.5 0 000-4h3.38a7.96 7.96 0 010 4h-3.38z',
	email:
		'M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm0 2v.01L12 11l8-4.99V6H4zm16 12V8.24l-8 5-8-5V18h16z',
};

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		name,
		role,
		bio,
		avatarUrl,
		avatarId,
		preset,
		blockId,
		socialTwitter,
		socialLinkedin,
		socialGithub,
		socialWebsite,
		socialEmail,
	} = attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const blockProps = useBlockProps( {
		className: `etu-team-member etu-team-member--preset-${ preset } ${ blockId ? `etu-team-member-${ blockId }` : '' } etu-team-member--editor`,
	} );

	const activeSocials = [
		[ 'twitter', socialTwitter ],
		[ 'linkedin', socialLinkedin ],
		[ 'github', socialGithub ],
		[ 'website', socialWebsite ],
		[ 'email', socialEmail ],
	].filter( ( [ , v ] ) => v );

	return (
		<>
			<EtuInspector
				name="etu/team-member"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/team-member"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				<div className="etu-team-member__media">
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
										className="etu-team-member__avatar-btn"
										onClick={ open }
									>
										<img
											className="etu-team-member__avatar"
											src={ avatarUrl }
											alt=""
										/>
									</button>
								) : (
									<Button
										variant="secondary"
										className="etu-team-member__add"
										onClick={ open }
									>
										{ __( 'Add photo', 'etu-blocks' ) }
									</Button>
								)
							}
						/>
					</MediaUploadCheck>
				</div>
				<div className="etu-team-member__body">
					<RichText
						tagName="p"
						className="etu-team-member__name"
						value={ name }
						onChange={ ( v ) => setAttributes( { name: v } ) }
						placeholder={ __( 'Name', 'etu-blocks' ) }
						allowedFormats={ [] }
					/>
					<RichText
						tagName="p"
						className="etu-team-member__role"
						value={ role }
						onChange={ ( v ) => setAttributes( { role: v } ) }
						placeholder={ __( 'Role', 'etu-blocks' ) }
						allowedFormats={ [] }
					/>
					<RichText
						tagName="div"
						className="etu-team-member__bio"
						value={ bio }
						onChange={ ( v ) => setAttributes( { bio: v } ) }
						placeholder={ __( 'Short bio…', 'etu-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
					{ activeSocials.length > 0 && (
						<div className="etu-team-member__socials">
							{ activeSocials.map( ( [ key ] ) => (
								<span
									key={ key }
									className="etu-team-member__social"
								>
									<svg viewBox="0 0 24 24" aria-hidden="true">
										<path fill="currentColor" d={ ICONS[ key ] } />
									</svg>
								</span>
							) ) }
						</div>
					) }
				</div>
			</div>
		</>
	);
}
