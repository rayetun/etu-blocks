/**
 * Star Rating block editor.
 */
import { useBlockProps } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

const GLYPHS = { star: '★', heart: '♥', circle: '●' };

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { rating, maxStars, showValue, label, preset, blockId } = attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const glyph = GLYPHS[ preset ] || '★';
	const max = Math.max( 1, Math.min( 10, Number( maxStars ) || 5 ) );
	const value = Math.max( 0, Math.min( max, Number( rating ) || 0 ) );
	const percent = ( value / max ) * 100;
	const row = glyph.repeat( max );
	const display = ( Math.round( value * 10 ) / 10 ).toString();

	const blockProps = useBlockProps( {
		className: `etu-star-rating etu-star-rating--${ preset } ${ blockId ? `etu-star-rating-${ blockId }` : '' } etu-star-rating--editor`,
	} );

	return (
		<>
			<EtuInspector
				name="etu/star-rating"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/star-rating"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				{ '' !== label && (
					<span className="etu-star-rating__label">{ label }</span>
				) }
				<span className="etu-star-rating__stars" aria-hidden="true">
					<span className="etu-star-rating__empty">{ row }</span>
					<span
						className="etu-star-rating__filled"
						style={ { width: `${ percent }%` } }
					>
						{ row }
					</span>
				</span>
				{ showValue && (
					<span className="etu-star-rating__value">{ display }</span>
				) }
			</div>
		</>
	);
}
