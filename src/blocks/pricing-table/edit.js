/**
 * Pricing Table block editor.
 *
 * Plans are edited inline as cards; each has an inline feature list (toggle
 * include/exclude, add/remove). Design lives in the shared Inspector.
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
} from '@wordpress/icons';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

const YesIcon = () => (
	<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
		<path d="M20 6 9 17l-5-5" />
	</svg>
);
const NoIcon = () => (
	<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
		<path d="M6 6l12 12" />
		<path d="M18 6 6 18" />
	</svg>
);

const newFeature = () => ( { text: '', included: true } );
const newPlan = () => ( {
	name: '',
	badge: '',
	highlighted: false,
	currency: '$',
	price: '0',
	priceYearly: '0',
	period: '/mo',
	periodYearly: '/yr',
	description: '',
	features: [ newFeature() ],
	buttonText: __( 'Choose plan', 'etu-blocks' ),
	buttonUrl: '',
} );

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { plans, preset, columns, enableBilling, blockId } = attributes;
	const [ linkPlan, setLinkPlan ] = useState( null );

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const setPlans = ( next ) => setAttributes( { plans: next } );
	const updatePlan = ( pi, key, val ) =>
		setPlans( plans.map( ( p, i ) => ( i === pi ? { ...p, [ key ]: val } : p ) ) );
	const updateFeature = ( pi, fi, patch ) =>
		setPlans(
			plans.map( ( p, i ) => {
				if ( i !== pi ) {
					return p;
				}
				const features = ( p.features || [] ).map( ( f, j ) =>
					j === fi ? { ...f, ...patch } : f
				);
				return { ...p, features };
			} )
		);
	const addFeature = ( pi ) =>
		setPlans(
			plans.map( ( p, i ) =>
				i === pi
					? { ...p, features: [ ...( p.features || [] ), newFeature() ] }
					: p
			)
		);
	const removeFeature = ( pi, fi ) =>
		setPlans(
			plans.map( ( p, i ) =>
				i === pi
					? { ...p, features: ( p.features || [] ).filter( ( _, j ) => j !== fi ) }
					: p
			)
		);
	const toggleHighlight = ( pi ) =>
		setPlans(
			plans.map( ( p, i ) => ( i === pi ? { ...p, highlighted: ! p.highlighted } : p ) )
		);
	const addPlan = () => setPlans( [ ...plans, newPlan() ] );
	const removePlan = ( pi ) => setPlans( plans.filter( ( _, i ) => i !== pi ) );

	const blockProps = useBlockProps( {
		className: `etu-pricing etu-pricing--preset-${ preset } etu-pricing--cols-${ columns } etu-pricing--editor ${
			blockId ? `etu-pricing-${ blockId }` : ''
		}`,
	} );

	return (
		<>
			<EtuInspector
				name="etu/pricing-table"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/pricing-table"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				{ enableBilling && (
					<div className="etu-pricing__billing" aria-hidden="true">
						<span className="etu-pricing__billing-opt is-on">
							{ attributes.monthlyLabel || __( 'Monthly', 'etu-blocks' ) }
						</span>
						<span className="etu-pricing__billing-opt">
							{ attributes.yearlyLabel || __( 'Yearly', 'etu-blocks' ) }
						</span>
					</div>
				) }

				<div className="etu-pricing__grid">
					{ plans.map( ( plan, pi ) => (
						<div
							key={ pi }
							className={ `etu-pricing__card ${
								plan.highlighted
									? 'etu-pricing__card--featured'
									: ''
							}` }
						>
							<div className="etu-pricing__card-tools">
								<Button
									size="small"
									icon={ plan.highlighted ? starFilled : starEmpty }
									isPressed={ plan.highlighted }
									label={ __( 'Feature this plan', 'etu-blocks' ) }
									onClick={ () => toggleHighlight( pi ) }
								/>
								<Button
									size="small"
									icon={ trash }
									isDestructive
									disabled={ plans.length <= 1 }
									label={ __( 'Remove plan', 'etu-blocks' ) }
									onClick={ () => removePlan( pi ) }
								/>
							</div>

							<RichText
								tagName="span"
								className="etu-pricing__badge"
								value={ plan.badge }
								onChange={ ( v ) => updatePlan( pi, 'badge', v ) }
								placeholder={ __( 'Badge', 'etu-blocks' ) }
								allowedFormats={ [] }
							/>
							<RichText
								tagName="h3"
								className="etu-pricing__name"
								value={ plan.name }
								onChange={ ( v ) => updatePlan( pi, 'name', v ) }
								placeholder={ __( 'Plan name', 'etu-blocks' ) }
								allowedFormats={ [] }
							/>

							<div className="etu-pricing__price">
								<RichText
									tagName="span"
									className="etu-pricing__currency"
									value={ plan.currency }
									onChange={ ( v ) => updatePlan( pi, 'currency', v ) }
									placeholder="$"
									allowedFormats={ [] }
								/>
								<RichText
									tagName="span"
									className="etu-pricing__amount"
									value={ plan.price }
									onChange={ ( v ) => updatePlan( pi, 'price', v ) }
									placeholder="0"
									allowedFormats={ [] }
								/>
								<RichText
									tagName="span"
									className="etu-pricing__period"
									value={ plan.period }
									onChange={ ( v ) => updatePlan( pi, 'period', v ) }
									placeholder="/mo"
									allowedFormats={ [] }
								/>
							</div>

							{ enableBilling && (
								<div className="etu-pricing__yearly-edit">
									<span>{ __( 'Yearly:', 'etu-blocks' ) }</span>
									<RichText
										tagName="span"
										value={ plan.priceYearly }
										onChange={ ( v ) => updatePlan( pi, 'priceYearly', v ) }
										placeholder="0"
										allowedFormats={ [] }
									/>
									<RichText
										tagName="span"
										value={ plan.periodYearly }
										onChange={ ( v ) => updatePlan( pi, 'periodYearly', v ) }
										placeholder="/yr"
										allowedFormats={ [] }
									/>
								</div>
							) }

							<RichText
								tagName="p"
								className="etu-pricing__desc"
								value={ plan.description }
								onChange={ ( v ) => updatePlan( pi, 'description', v ) }
								placeholder={ __( 'Short description', 'etu-blocks' ) }
								allowedFormats={ [ 'core/bold', 'core/italic' ] }
							/>

							<ul className="etu-pricing__features">
								{ ( plan.features || [] ).map( ( feature, fi ) => (
									<li
										key={ fi }
										className={ `etu-pricing__feature ${
											feature.included
												? ''
												: 'etu-pricing__feature--excluded'
										}` }
									>
										<Button
											className="etu-pricing__feature-toggle"
											label={ __(
												'Included / not included',
												'etu-blocks'
											) }
											onClick={ () =>
												updateFeature( pi, fi, {
													included: ! feature.included,
												} )
											}
										>
											{ feature.included ? (
												<YesIcon />
											) : (
												<NoIcon />
											) }
										</Button>
										<RichText
											tagName="span"
											className="etu-pricing__feature-text"
											value={ feature.text }
											onChange={ ( v ) =>
												updateFeature( pi, fi, { text: v } )
											}
											placeholder={ __(
												'Feature',
												'etu-blocks'
											) }
											allowedFormats={ [ 'core/bold' ] }
										/>
										<Button
											size="small"
											icon={ trash }
											isDestructive
											className="etu-pricing__feature-remove"
											label={ __(
												'Remove feature',
												'etu-blocks'
											) }
											onClick={ () => removeFeature( pi, fi ) }
										/>
									</li>
								) ) }
							</ul>
							<Button
								className="etu-pricing__add-feature"
								variant="link"
								icon={ plus }
								onClick={ () => addFeature( pi ) }
							>
								{ __( 'Add feature', 'etu-blocks' ) }
							</Button>

							<div className="etu-pricing__btn-edit">
								<RichText
									tagName="span"
									className="etu-pricing__btn"
									value={ plan.buttonText }
									onChange={ ( v ) => updatePlan( pi, 'buttonText', v ) }
									placeholder={ __( 'Button…', 'etu-blocks' ) }
									allowedFormats={ [] }
								/>
								<Button
									size="small"
									icon={ linkIcon }
									isPressed={ !! plan.buttonUrl }
									label={ __( 'Set button link', 'etu-blocks' ) }
									onClick={ () =>
										setLinkPlan( linkPlan === pi ? null : pi )
									}
								/>
								{ linkPlan === pi && (
									<Popover
										placement="bottom"
										onClose={ () => setLinkPlan( null ) }
									>
										<div className="etu-pricing__link-popover">
											<TextControl
												__nextHasNoMarginBottom
												label={ __( 'Button URL', 'etu-blocks' ) }
												type="url"
												value={ plan.buttonUrl }
												onChange={ ( v ) =>
													updatePlan( pi, 'buttonUrl', v )
												}
												placeholder="https://…"
											/>
										</div>
									</Popover>
								) }
							</div>
						</div>
					) ) }
				</div>

				<Button
					className="etu-pricing__add-plan"
					variant="secondary"
					icon={ plus }
					onClick={ addPlan }
				>
					{ __( 'Add plan', 'etu-blocks' ) }
				</Button>
			</div>
		</>
	);
}
