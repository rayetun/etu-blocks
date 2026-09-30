/**
 * Data Table block editor.
 *
 * A compact grid editor: edit the caption inline, type column headers and cells
 * directly, set each column's alignment and sort type, and add/remove rows and
 * columns. The preview reuses the front-end table classes so colours, stripes and
 * borders reflect live. Sorting/search are front-end only.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';
import { useEffect } from '@wordpress/element';
import {
	plus,
	trash,
	alignLeft,
	alignCenter,
	alignRight,
} from '@wordpress/icons';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

const ALIGNS = [
	{ value: 'left', icon: alignLeft, label: __( 'Left', 'etu-blocks' ) },
	{ value: 'center', icon: alignCenter, label: __( 'Center', 'etu-blocks' ) },
	{ value: 'right', icon: alignRight, label: __( 'Right', 'etu-blocks' ) },
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		columns,
		rows,
		caption,
		striped,
		bordered,
		compact,
		responsive,
		blockId,
	} = attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( {
				blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ),
			} );
		}
	}, [ blockId, clientId, setAttributes ] );

	const setColumn = ( ci, patch ) => {
		const next = columns.map( ( c, i ) =>
			i === ci ? { ...c, ...patch } : c
		);
		setAttributes( { columns: next } );
	};

	const setCell = ( ri, ci, value ) => {
		const next = rows.map( ( r, i ) => {
			if ( i !== ri ) {
				return r;
			}
			const row = r.slice();
			row[ ci ] = value;
			return row;
		} );
		setAttributes( { rows: next } );
	};

	const addColumn = () => {
		setAttributes( {
			columns: [
				...columns,
				{ label: '', align: 'left', type: 'text' },
			],
			rows: rows.map( ( r ) => [ ...r, '' ] ),
		} );
	};

	const removeColumn = ( ci ) => {
		if ( columns.length <= 1 ) {
			return;
		}
		setAttributes( {
			columns: columns.filter( ( _, i ) => i !== ci ),
			rows: rows.map( ( r ) => r.filter( ( _, i ) => i !== ci ) ),
		} );
	};

	const addRow = () => {
		setAttributes( { rows: [ ...rows, columns.map( () => '' ) ] } );
	};

	const removeRow = ( ri ) => {
		setAttributes( { rows: rows.filter( ( _, i ) => i !== ri ) } );
	};

	const modifierClasses = [
		striped ? 'is-striped' : '',
		bordered ? 'is-bordered' : '',
		compact ? 'is-compact' : '',
	]
		.filter( Boolean )
		.join( ' ' );

	const blockProps = useBlockProps( {
		className: `etu-data-table etu-data-table--resp-${ responsive } etu-data-table--editor ${ modifierClasses } ${
			blockId ? `etu-data-table-${ blockId }` : ''
		}`,
	} );

	return (
		<>
			<EtuInspector
				name="etu/data-table"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/data-table"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				<RichText
					tagName="p"
					className="etu-data-table__caption etu-dt-editor__caption"
					value={ caption }
					onChange={ ( v ) => setAttributes( { caption: v } ) }
					placeholder={ __( 'Table caption (optional)…', 'etu-blocks' ) }
					allowedFormats={ [ 'core/bold', 'core/italic' ] }
				/>

				<div className="etu-dt-editor__scroll">
					<table className="etu-data-table__table etu-dt-editor__table">
						<thead>
							<tr>
								{ columns.map( ( col, ci ) => (
									<th
										key={ ci }
										className={ `etu-data-table__th etu-data-table__cell--${ col.align }` }
									>
										<input
											className="etu-dt-editor__input etu-dt-editor__head-input"
											type="text"
											value={ col.label }
											onChange={ ( e ) =>
												setColumn( ci, {
													label: e.target.value,
												} )
											}
											placeholder={ __(
												'Column',
												'etu-blocks'
											) }
											aria-label={ __(
												'Column heading',
												'etu-blocks'
											) }
										/>
										<div className="etu-dt-editor__coltools">
											{ ALIGNS.map( ( a ) => (
												<Button
													key={ a.value }
													size="small"
													icon={ a.icon }
													label={ a.label }
													isPressed={
														col.align === a.value
													}
													onClick={ () =>
														setColumn( ci, {
															align: a.value,
														} )
													}
												/>
											) ) }
											<select
												className="etu-dt-editor__type"
												value={ col.type }
												onChange={ ( e ) =>
													setColumn( ci, {
														type: e.target.value,
													} )
												}
												aria-label={ __(
													'Sort type',
													'etu-blocks'
												) }
											>
												<option value="text">
													{ __( 'Text', 'etu-blocks' ) }
												</option>
												<option value="number">
													{ __(
														'Number',
														'etu-blocks'
													) }
												</option>
											</select>
											<Button
												size="small"
												icon={ trash }
												isDestructive
												label={ __(
													'Remove column',
													'etu-blocks'
												) }
												onClick={ () =>
													removeColumn( ci )
												}
												disabled={
													columns.length <= 1
												}
											/>
										</div>
									</th>
								) ) }
								<th className="etu-dt-editor__ctrl">
									<Button
										size="small"
										icon={ plus }
										variant="secondary"
										label={ __(
											'Add column',
											'etu-blocks'
										) }
										onClick={ addColumn }
									/>
								</th>
							</tr>
						</thead>
						<tbody>
							{ rows.map( ( row, ri ) => (
								<tr key={ ri }>
									{ columns.map( ( col, ci ) => (
										<td
											key={ ci }
											className={ `etu-data-table__cell etu-data-table__cell--${ col.align }` }
										>
											<input
												className="etu-dt-editor__input"
												type="text"
												value={ row[ ci ] || '' }
												onChange={ ( e ) =>
													setCell(
														ri,
														ci,
														e.target.value
													)
												}
												aria-label={ __(
													'Cell value',
													'etu-blocks'
												) }
											/>
										</td>
									) ) }
									<td className="etu-dt-editor__ctrl">
										<Button
											size="small"
											icon={ trash }
											isDestructive
											label={ __(
												'Remove row',
												'etu-blocks'
											) }
											onClick={ () => removeRow( ri ) }
										/>
									</td>
								</tr>
							) ) }
						</tbody>
					</table>
				</div>

				<div className="etu-dt-editor__actions">
					<Button
						variant="secondary"
						size="small"
						icon={ plus }
						onClick={ addRow }
					>
						{ __( 'Add row', 'etu-blocks' ) }
					</Button>
				</div>
			</div>
		</>
	);
}
