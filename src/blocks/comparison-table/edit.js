/**
 * Comparison Table block editor.
 *
 * A live table grid: column headers (title / badge / highlight / button) across
 * the top, feature rows down the side, and per-cell yes / no / text cells you
 * cycle with one click. Design controls live in the shared Inspector.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { Button, Popover, TextControl } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import {
	plus,
	trash,
	starFilled,
	starEmpty,
	link as linkIcon,
	rotateRight,
} from '@wordpress/icons';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

const YesIcon = () => (
	<svg
		viewBox="0 0 24 24"
		width="20"
		height="20"
		fill="none"
		stroke="currentColor"
		strokeWidth="2.5"
		strokeLinecap="round"
		strokeLinejoin="round"
		aria-hidden="true"
	>
		<path d="M20 6 9 17l-5-5" />
	</svg>
);

const NoIcon = () => (
	<svg
		viewBox="0 0 24 24"
		width="20"
		height="20"
		fill="none"
		stroke="currentColor"
		strokeWidth="2.5"
		strokeLinecap="round"
		strokeLinejoin="round"
		aria-hidden="true"
	>
		<path d="M6 6l12 12" />
		<path d="M18 6 6 18" />
	</svg>
);

const newCell = () => ( { type: 'check', text: '' } );

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { columns, rows, preset, cellAlign, stripe, blockId } = attributes;
	const [ linkCol, setLinkCol ] = useState( null );

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const updateColumn = ( ci, key, val ) =>
		setAttributes( {
			columns: columns.map( ( c, i ) =>
				i === ci ? { ...c, [ key ]: val } : c
			),
		} );

	const updateRow = ( ri, key, val ) =>
		setAttributes( {
			rows: rows.map( ( r, i ) => ( i === ri ? { ...r, [ key ]: val } : r ) ),
		} );

	const updateCell = ( ri, ci, patch ) =>
		setAttributes( {
			rows: rows.map( ( r, i ) => {
				if ( i !== ri ) {
					return r;
				}
				const cells = ( r.cells || [] ).map( ( cell, j ) =>
					j === ci ? { ...cell, ...patch } : cell
				);
				return { ...r, cells };
			} ),
		} );

	const cycleCell = ( ri, ci ) => {
		const cell = ( rows[ ri ].cells || [] )[ ci ] || { type: 'text' };
		const next =
			cell.type === 'check'
				? 'cross'
				: cell.type === 'cross'
				? 'text'
				: 'check';
		updateCell( ri, ci, { type: next } );
	};

	const toggleHighlight = ( ci ) =>
		setAttributes( {
			columns: columns.map( ( c, i ) =>
				i === ci ? { ...c, highlighted: ! c.highlighted } : c
			),
		} );

	const addColumn = () =>
		setAttributes( {
			columns: [
				...columns,
				{ title: '', badge: '', highlighted: false, buttonText: '', buttonUrl: '' },
			],
			rows: rows.map( ( r ) => ( {
				...r,
				cells: [ ...( r.cells || [] ), newCell() ],
			} ) ),
		} );

	const removeColumn = ( ci ) =>
		setAttributes( {
			columns: columns.filter( ( _, i ) => i !== ci ),
			rows: rows.map( ( r ) => ( {
				...r,
				cells: ( r.cells || [] ).filter( ( _, j ) => j !== ci ),
			} ) ),
		} );

	const addRow = () =>
		setAttributes( {
			rows: [ ...rows, { label: '', cells: columns.map( () => newCell() ) } ],
		} );

	const removeRow = ( ri ) =>
		setAttributes( { rows: rows.filter( ( _, i ) => i !== ri ) } );

	const blockProps = useBlockProps( {
		className: `etu-ct etu-ct--preset-${ preset } etu-ct--cells-${ cellAlign } ${
			stripe ? 'etu-ct--striped' : ''
		} etu-ct--editor ${
			blockId ? `etu-comparison-table-${ blockId }` : ''
		}`,
	} );

	return (
		<>
			<EtuInspector
				name="etu/comparison-table"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/comparison-table"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				<div className="etu-ct__scroll">
					<table className="etu-ct__table">
						<thead>
							<tr>
								<td className="etu-ct__corner" />
								{ columns.map( ( col, ci ) => (
									<th
										key={ ci }
										scope="col"
										className={ `etu-ct__col-head ${
											col.highlighted
												? 'etu-ct__col-head--highlighted'
												: ''
										}` }
									>
										<div className="etu-ct__col-tools">
											<Button
												size="small"
												icon={
													col.highlighted
														? starFilled
														: starEmpty
												}
												isPressed={ col.highlighted }
												label={ __(
													'Feature this column',
													'etu-blocks'
												) }
												onClick={ () =>
													toggleHighlight( ci )
												}
											/>
											<Button
												size="small"
												icon={ linkIcon }
												isPressed={
													!! col.buttonText ||
													!! col.buttonUrl
												}
												label={ __(
													'Button & link',
													'etu-blocks'
												) }
												onClick={ () =>
													setLinkCol(
														linkCol === ci
															? null
															: ci
													)
												}
											/>
											<Button
												size="small"
												icon={ trash }
												isDestructive
												disabled={ columns.length <= 1 }
												label={ __(
													'Remove column',
													'etu-blocks'
												) }
												onClick={ () =>
													removeColumn( ci )
												}
											/>
											{ linkCol === ci && (
												<Popover
													placement="bottom"
													onClose={ () =>
														setLinkCol( null )
													}
												>
													<div className="etu-ct__link-popover">
														<TextControl
															__nextHasNoMarginBottom
															label={ __(
																'Button label',
																'etu-blocks'
															) }
															value={
																col.buttonText
															}
															onChange={ ( v ) =>
																updateColumn(
																	ci,
																	'buttonText',
																	v
																)
															}
															placeholder={ __(
																'e.g. Get started',
																'etu-blocks'
															) }
														/>
														<TextControl
															__nextHasNoMarginBottom
															label={ __(
																'Button URL',
																'etu-blocks'
															) }
															type="url"
															value={
																col.buttonUrl
															}
															onChange={ ( v ) =>
																updateColumn(
																	ci,
																	'buttonUrl',
																	v
																)
															}
															placeholder="https://…"
														/>
													</div>
												</Popover>
											) }
										</div>
										<RichText
											tagName="span"
											className="etu-ct__badge"
											value={ col.badge }
											onChange={ ( v ) =>
												updateColumn( ci, 'badge', v )
											}
											placeholder={ __(
												'Badge',
												'etu-blocks'
											) }
											allowedFormats={ [] }
										/>
										<RichText
											tagName="span"
											className="etu-ct__col-title"
											value={ col.title }
											onChange={ ( v ) =>
												updateColumn( ci, 'title', v )
											}
											placeholder={ __(
												'Plan',
												'etu-blocks'
											) }
											allowedFormats={ [ 'core/bold' ] }
										/>
										{ col.buttonText && (
											<span className="etu-ct__btn">
												{ col.buttonText }
											</span>
										) }
									</th>
								) ) }
								<th className="etu-ct__add-col">
									<Button
										icon={ plus }
										label={ __( 'Add column', 'etu-blocks' ) }
										onClick={ addColumn }
									/>
								</th>
							</tr>
						</thead>
						<tbody>
							{ rows.map( ( row, ri ) => (
								<tr key={ ri } className="etu-ct__row">
									<th
										scope="row"
										className="etu-ct__row-head"
									>
										<RichText
											tagName="span"
											value={ row.label }
											onChange={ ( v ) =>
												updateRow( ri, 'label', v )
											}
											placeholder={ __(
												'Feature',
												'etu-blocks'
											) }
											allowedFormats={ [
												'core/bold',
												'core/italic',
											] }
										/>
										<Button
											size="small"
											icon={ trash }
											isDestructive
											className="etu-ct__row-remove"
											label={ __(
												'Remove row',
												'etu-blocks'
											) }
											onClick={ () => removeRow( ri ) }
										/>
									</th>
									{ columns.map( ( col, ci ) => {
										const cell = ( row.cells &&
											row.cells[ ci ] ) || {
											type: 'text',
											text: '',
										};
										return (
											<td
												key={ ci }
												className={ `etu-ct__cell ${
													col.highlighted
														? 'etu-ct__cell--highlighted'
														: ''
												}` }
											>
												<div className="etu-ct__cell-inner">
													{ cell.type === 'text' ? (
														<RichText
															tagName="span"
															className="etu-ct__cell-text"
															value={ cell.text }
															onChange={ ( v ) =>
																updateCell(
																	ri,
																	ci,
																	{ text: v }
																)
															}
															placeholder="—"
															allowedFormats={ [
																'core/bold',
															] }
														/>
													) : cell.type ===
													  'check' ? (
														<span className="etu-ct__mark etu-ct__mark--yes">
															<YesIcon />
														</span>
													) : (
														<span className="etu-ct__mark etu-ct__mark--no">
															<NoIcon />
														</span>
													) }
													<Button
														className="etu-ct__cell-cycle"
														size="small"
														icon={ rotateRight }
														label={ __(
															'Change cell: yes / no / text',
															'etu-blocks'
														) }
														onClick={ () =>
															cycleCell( ri, ci )
														}
													/>
												</div>
											</td>
										);
									} ) }
									<td className="etu-ct__spacer" />
								</tr>
							) ) }
						</tbody>
					</table>
				</div>
				<Button
					className="etu-ct__add-row"
					variant="secondary"
					icon={ plus }
					onClick={ addRow }
				>
					{ __( 'Add row', 'etu-blocks' ) }
				</Button>
			</div>
		</>
	);
}
