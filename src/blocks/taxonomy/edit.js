/**
 * Taxonomy block editor.
 *
 * Uses ServerSideRender so the editor shows the real terms (and the scoped generated
 * CSS) as the controls change — matching the front end exactly.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import './editor.scss';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { blockId } = attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( {
				blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ),
			} );
		}
	}, [ blockId, clientId, setAttributes ] );

	const blockProps = useBlockProps( { className: 'etu-taxonomy--editor' } );

	return (
		<>
			<EtuInspector
				name="etu/taxonomy"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<div { ...blockProps }>
				<ServerSideRender
					block="etu/taxonomy"
					attributes={ attributes }
					EmptyResponsePlaceholder={ () => (
						<p className="etu-taxonomy__empty">
							{ __(
								'No terms found for this taxonomy yet.',
								'etu-blocks'
							) }
						</p>
					) }
				/>
			</div>
		</>
	);
}
