/**
 * Timeline block editor.
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

	const blockProps = useBlockProps( { className: 'etu-timeline--editor-wrap' } );

	const updateItem = ( index, key, value ) => {
		setAttributes( {
			items: items.map( ( item, i ) =>
				i === index ? { ...item, [ key ]: value } : item
			),
		} );
	};
	const addItem = () =>
		setAttributes( {
			items: [ ...items, { date: '', title: '', description: '' } ],
		} );
	const removeItem = ( index ) =>
		setAttributes( { items: items.filter( ( _, i ) => i !== index ) } );

	return (
		<>
			<EtuInspector
				name="etu/timeline"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/timeline"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				<ol
					className={ `etu-timeline etu-timeline--preset-${ preset } ${ blockId ? `etu-timeline-${ blockId }` : '' } etu-timeline--editor` }
				>
					{ items.map( ( item, index ) => (
						<li className="etu-timeline__item" key={ index }>
							<span
								className="etu-timeline__marker"
								aria-hidden="true"
							/>
							<div className="etu-timeline__content">
								<RichText
									tagName="span"
									className="etu-timeline__date"
									value={ item.date }
									onChange={ ( v ) =>
										updateItem( index, 'date', v )
									}
									placeholder={ __( 'Date', 'etu-blocks' ) }
									allowedFormats={ [] }
								/>
								<RichText
									tagName="p"
									className="etu-timeline__title"
									value={ item.title }
									onChange={ ( v ) =>
										updateItem( index, 'title', v )
									}
									placeholder={ __( 'Title…', 'etu-blocks' ) }
									allowedFormats={ [ 'core/bold', 'core/italic' ] }
								/>
								<RichText
									tagName="div"
									className="etu-timeline__description"
									value={ item.description }
									onChange={ ( v ) =>
										updateItem( index, 'description', v )
									}
									placeholder={ __( 'Description…', 'etu-blocks' ) }
									allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
								/>
							</div>
							<Button
								size="small"
								icon={ trash }
								isDestructive
								label={ __( 'Remove event', 'etu-blocks' ) }
								className="etu-timeline__remove"
								onClick={ () => removeItem( index ) }
							/>
						</li>
					) ) }
				</ol>
				<Button
					variant="secondary"
					className="etu-timeline__add"
					onClick={ addItem }
				>
					{ __( 'Add event', 'etu-blocks' ) }
				</Button>
			</div>
		</>
	);
}
