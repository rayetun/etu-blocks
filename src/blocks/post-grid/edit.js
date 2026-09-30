/**
 * Post Grid block editor.
 *
 * Uses ServerSideRender so the editor shows the real queried posts (and the
 * scoped, generated CSS) as the controls change. The filter / carousel scripts
 * only run on the front end.
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
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const blockProps = useBlockProps( { className: 'etu-post-grid--editor' } );

	return (
		<>
			<EtuInspector
				name="etu/post-grid"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<div { ...blockProps }>
				<ServerSideRender
					block="etu/post-grid"
					attributes={ attributes }
					EmptyResponsePlaceholder={ () => (
						<p className="etu-post-grid__empty">
							{ __(
								'No posts match this query yet.',
								'etu-blocks'
							) }
						</p>
					) }
				/>
			</div>
		</>
	);
}
