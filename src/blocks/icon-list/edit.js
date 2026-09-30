/**
 * Icon List block editor.
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
	const { items, preset, columns, blockId } = attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const blockProps = useBlockProps( { className: 'etu-icon-list--editor-wrap' } );

	const updateItem = ( index, key, value ) => {
		setAttributes( {
			items: items.map( ( item, i ) =>
				i === index ? { ...item, [ key ]: value } : item
			),
		} );
	};
	const addItem = () =>
		setAttributes( { items: [ ...items, { icon: '✓', text: '' } ] } );
	const removeItem = ( index ) =>
		setAttributes( { items: items.filter( ( _, i ) => i !== index ) } );

	return (
		<>
			<EtuInspector
				name="etu/icon-list"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/icon-list"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				<ul
					className={ `etu-icon-list etu-icon-list--preset-${ preset } etu-icon-list--cols-${ columns } ${ blockId ? `etu-icon-list-${ blockId }` : '' } etu-icon-list--editor` }
				>
					{ items.map( ( item, index ) => (
						<li className="etu-icon-list__item" key={ index }>
							<RichText
								tagName="span"
								className="etu-icon-list__icon"
								value={ item.icon }
								onChange={ ( value ) =>
									updateItem( index, 'icon', value )
								}
								allowedFormats={ [] }
							/>
							<RichText
								tagName="span"
								className="etu-icon-list__text"
								value={ item.text }
								onChange={ ( value ) =>
									updateItem( index, 'text', value )
								}
								placeholder={ __( 'List item…', 'etu-blocks' ) }
								allowedFormats={ [ 'core/bold', 'core/italic' ] }
							/>
							<Button
								size="small"
								icon={ trash }
								isDestructive
								label={ __( 'Remove item', 'etu-blocks' ) }
								className="etu-icon-list__remove"
								onClick={ () => removeItem( index ) }
							/>
						</li>
					) ) }
				</ul>
				<Button
					variant="secondary"
					className="etu-icon-list__add"
					onClick={ addItem }
				>
					{ __( 'Add item', 'etu-blocks' ) }
				</Button>
			</div>
		</>
	);
}
