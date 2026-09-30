/**
 * Modal / Popup block editor.
 *
 * The dialog is shown inline (open) here so its content is editable; on the front
 * end it is hidden until triggered. The trigger button label is edited in place.
 */
import { __, sprintf } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	RichText,
} from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

const TEMPLATE = [
	[ 'core/heading', { level: 3, placeholder: __( 'Popup title', 'etu-blocks' ) } ],
	[ 'core/paragraph', { placeholder: __( 'Popup content…', 'etu-blocks' ) } ],
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		trigger,
		buttonText,
		size,
		position,
		animation,
		showClose,
		delaySeconds,
		blockId,
	} = attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const blockProps = useBlockProps( {
		className: `etu-modal etu-modal--size-${ size } etu-modal--pos-${ position } etu-modal--anim-${ animation } etu-modal--editor ${
			blockId ? `etu-modal-${ blockId }` : ''
		}`,
	} );

	const innerProps = useInnerBlocksProps(
		{ className: 'etu-modal__content' },
		{ template: TEMPLATE, templateLock: false }
	);

	const autoNote =
		trigger === 'load'
			? __( 'Opens automatically on page load', 'etu-blocks' )
			: sprintf(
					/* translators: %d: number of seconds. */
					__( 'Opens automatically after %d seconds', 'etu-blocks' ),
					delaySeconds
			  );

	return (
		<>
			<EtuInspector
				name="etu/modal"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/modal"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				{ trigger === 'button' ? (
					<div className="etu-modal__trigger-wrap">
						<RichText
							tagName="span"
							className="etu-modal__trigger"
							value={ buttonText }
							onChange={ ( v ) =>
								setAttributes( { buttonText: v } )
							}
							placeholder={ __( 'Open', 'etu-blocks' ) }
							allowedFormats={ [] }
						/>
					</div>
				) : (
					<div className="etu-modal__auto-note">{ autoNote }</div>
				) }

				<div className="etu-modal__dialog etu-modal__editor-dialog">
					<div className="etu-modal__editor-label">
						{ __( 'Popup content', 'etu-blocks' ) }
					</div>
					{ showClose && (
						<span
							className="etu-modal__close"
							aria-hidden="true"
						>
							×
						</span>
					) }
					<div { ...innerProps } />
				</div>
			</div>
		</>
	);
}
