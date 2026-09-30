/**
 * Post Filter block editor.
 *
 * Uses ServerSideRender so the editor shows the real filter bar and the initial
 * results (with the scoped generated CSS). Filtering itself runs on the front end.
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

	const blockProps = useBlockProps( { className: 'etu-post-filter--editor' } );

	return (
		<>
			<EtuInspector
				name="etu/post-filter"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<div { ...blockProps }>
				<ServerSideRender
					block="etu/post-filter"
					attributes={ attributes }
					EmptyResponsePlaceholder={ () => (
						<p className="etu-post-filter__empty">
							{ __(
								'No posts found for this query yet.',
								'etu-blocks'
							) }
						</p>
					) }
				/>
			</div>
		</>
	);
}
