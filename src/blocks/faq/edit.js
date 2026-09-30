/**
 * FAQ block editor.
 *
 * Canvas: inline-editable question/answer repeater. Inspector: the shared
 * schema-driven EtuInspector (Layout / Style / Advanced) — no per-block controls.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';
import { trash } from '@wordpress/icons';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { items, preset, blockId } = attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const blockProps = useBlockProps( {
		className: `etu-faq etu-faq--preset-${ preset } ${ blockId ? `etu-faq-${ blockId }` : '' } etu-faq--editor`,
	} );

	const updateItem = ( index, key, value ) => {
		const next = items.map( ( item, i ) =>
			i === index ? { ...item, [ key ]: value } : item
		);
		setAttributes( { items: next } );
	};

	const addItem = () => {
		setAttributes( { items: [ ...items, { question: '', answer: '' } ] } );
	};

	const removeItem = ( index ) => {
		setAttributes( { items: items.filter( ( _, i ) => i !== index ) } );
	};

	return (
		<>
			<EtuInspector
				name="etu/faq"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/faq"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				{ items.map( ( item, index ) => (
					<div className="etu-faq__item" key={ index }>
						<RichText
							tagName="p"
							className="etu-faq__question-text"
							value={ item.question }
							onChange={ ( value ) =>
								updateItem( index, 'question', value )
							}
							placeholder={ __( 'Question…', 'etu-blocks' ) }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
						/>
						<RichText
							tagName="div"
							className="etu-faq__answer"
							value={ item.answer }
							onChange={ ( value ) =>
								updateItem( index, 'answer', value )
							}
							placeholder={ __( 'Answer…', 'etu-blocks' ) }
						/>
						<Button
							size="small"
							icon={ trash }
							isDestructive
							label={ __( 'Remove question', 'etu-blocks' ) }
							className="etu-faq__remove"
							onClick={ () => removeItem( index ) }
						/>
					</div>
				) ) }

				<Button variant="secondary" onClick={ addItem }>
					{ __( 'Add question', 'etu-blocks' ) }
				</Button>
			</div>
		</>
	);
}
