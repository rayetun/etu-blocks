/**
 * Slides / Carousel block editor (parent).
 *
 * Slides are shown stacked and clearly bounded here for easy editing; the
 * carousel behaviour (scroll-snap, arrows, dots, autoplay) is applied on the
 * front end by render.php + the Interactivity store.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';
import { useDispatch } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';
import { Button } from '@wordpress/components';
import { plus } from '@wordpress/icons';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

const TEMPLATE = [ [ 'etu/slide' ], [ 'etu/slide' ], [ 'etu/slide' ] ];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { blockId } = attributes;
	const { insertBlock } = useDispatch( blockEditorStore );

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const blockProps = useBlockProps( {
		className: `etu-slides etu-slides--editor ${
			blockId ? `etu-slides-${ blockId }` : ''
		}`,
	} );

	const innerProps = useInnerBlocksProps(
		{ className: 'etu-slides__editor-track' },
		{
			allowedBlocks: [ 'etu/slide' ],
			template: TEMPLATE,
			orientation: 'vertical',
			renderAppender: false,
		}
	);

	const addSlide = () => {
		const slide = createBlock( 'etu/slide', {}, [
			createBlock( 'core/paragraph' ),
		] );
		insertBlock( slide, undefined, clientId );
	};

	return (
		<>
			<EtuInspector
				name="etu/slides"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/slides"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				<div className="etu-slides__editor-hint">
					{ __(
						'Carousel — each slide can hold any blocks. Preview the carousel on the front end.',
						'etu-blocks'
					) }
				</div>
				<div { ...innerProps } />
				<Button
					className="etu-slides__add-slide"
					variant="primary"
					icon={ plus }
					onClick={ addSlide }
				>
					{ __( 'Add slide', 'etu-blocks' ) }
				</Button>
			</div>
		</>
	);
}
