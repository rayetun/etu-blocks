/**
 * Table of Contents block editor.
 *
 * Shows a live preview built from the current post's headings (via the editor
 * store) so the author sees the TOC update as they write. The real, linked TOC
 * is produced by render.php on the front end.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

const stripHtml = ( html ) => ( html || '' ).replace( /<[^>]+>/g, '' ).trim();

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, showTitle, listType, minLevel, maxLevel, preset, blockId } =
		attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const min = parseInt( minLevel, 10 ) || 2;
	const max = parseInt( maxLevel, 10 ) || 4;

	const headings = useSelect( ( select ) => {
		const out = [];
		const walk = ( blocks ) =>
			blocks.forEach( ( b ) => {
				if ( b.name === 'core/heading' ) {
					out.push( {
						level: b.attributes.level || 2,
						text: stripHtml( b.attributes.content ),
					} );
				}
				if ( b.innerBlocks && b.innerBlocks.length ) {
					walk( b.innerBlocks );
				}
			} );
		walk( select( blockEditorStore ).getBlocks() );
		return out;
	}, [] );

	const items = headings.filter(
		( h ) => h.level >= min && h.level <= max && h.text
	);

	const blockProps = useBlockProps( {
		className: `etu-toc etu-toc--preset-${ preset } etu-toc--list-${ listType } etu-toc--editor ${
			blockId ? `etu-toc-${ blockId }` : ''
		}`,
	} );

	const ListTag = listType === 'number' ? 'ol' : 'ul';

	return (
		<>
			<EtuInspector
				name="etu/toc"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/toc"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				<nav className="etu-toc__inner">
					{ showTitle && (
						<p className="etu-toc__title">
							{ title || __( 'Table of contents', 'etu-blocks' ) }
						</p>
					) }
					{ items.length === 0 ? (
						<p className="etu-toc__empty">
							{ __(
								'Add headings to this page and they will appear here.',
								'etu-blocks'
							) }
						</p>
					) : (
						<ListTag className="etu-toc__list">
							{ items.map( ( it, i ) => (
								<li
									key={ i }
									className="etu-toc__item"
									style={ {
										marginInlineStart: `${
											( it.level - min ) * 18
										}px`,
									} }
								>
									<span className="etu-toc__link">
										{ it.text }
									</span>
								</li>
							) ) }
						</ListTag>
					) }
				</nav>
			</div>
		</>
	);
}
