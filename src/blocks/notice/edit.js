/**
 * Notice block editor.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

const TYPE_ICONS = {
	info: 'ℹ',
	success: '✓',
	warning: '⚠',
	danger: '✕',
	neutral: '•',
};

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, content, icon, preset, display, dismissible, noticeAlign, blockId } =
		attributes;
	const align = [ 'left', 'center', 'right' ].includes( noticeAlign )
		? noticeAlign
		: 'left';

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const displayIcon = icon || TYPE_ICONS[ preset ] || 'ℹ';
	const blockProps = useBlockProps( {
		className: `etu-notice etu-notice--type-${ preset } etu-notice--display-${ display } etu-notice--align-${ align } ${ dismissible ? 'etu-notice--dismissible' : '' } ${ blockId ? `etu-notice-${ blockId }` : '' } etu-notice--editor`,
	} );

	return (
		<>
			<EtuInspector
				name="etu/notice"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/notice"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				<span className="etu-notice__icon" aria-hidden="true">
					{ displayIcon }
				</span>
				<div className="etu-notice__body">
					<RichText
						tagName="p"
						className="etu-notice__title"
						value={ title }
						onChange={ ( v ) => setAttributes( { title: v } ) }
						placeholder={ __( 'Notice title…', 'etu-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
					<RichText
						tagName="div"
						className="etu-notice__content"
						value={ content }
						onChange={ ( v ) => setAttributes( { content: v } ) }
						placeholder={ __( 'Message…', 'etu-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
					/>
				</div>
				{ dismissible && (
					<button
						type="button"
						className="etu-notice__dismiss"
						aria-hidden="true"
						tabIndex={ -1 }
					>
						<span aria-hidden="true">×</span>
					</button>
				) }
			</div>
		</>
	);
}
