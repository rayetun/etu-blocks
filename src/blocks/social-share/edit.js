/**
 * Social Share block editor.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import { NETWORKS } from './networks';
import './editor.scss';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		preset,
		buttonShape,
		showLabels,
		useBrandColors,
		networks,
		blockId,
	} = attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( { blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ) } );
		}
	}, [ blockId, clientId, setAttributes ] );

	const selected = Array.isArray( networks ) ? networks : [];
	const ordered = NETWORKS.filter( ( n ) => selected.includes( n.key ) );
	const labels = showLabels && buttonShape !== 'circle';

	const classes = [
		'etu-social-share',
		`etu-social-share--preset-${ preset }`,
		`etu-social-share--shape-${ buttonShape }`,
		useBrandColors ? 'etu-social-share--brand' : '',
		labels ? '' : 'etu-social-share--icon-only',
		blockId ? `etu-social-share-${ blockId }` : '',
		'etu-social-share--editor',
	]
		.filter( Boolean )
		.join( ' ' );

	const blockProps = useBlockProps( { className: classes } );

	return (
		<>
			<EtuInspector
				name="etu/social-share"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/social-share"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				{ ordered.length === 0 ? (
					<p className="etu-social-share__empty">
						{ __(
							'Choose at least one network in the Layout tab.',
							'etu-blocks'
						) }
					</p>
				) : (
					<ul className="etu-social-share__list" role="list">
						{ ordered.map( ( net ) => (
							<li
								key={ net.key }
								className="etu-social-share__item"
							>
								<span
									className={ `etu-social-share__btn etu-social-share__btn--${ net.key }` }
								>
									<span
										className="etu-social-share__icon"
										// eslint-disable-next-line react/no-danger
										dangerouslySetInnerHTML={ {
											__html: `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">${ net.svg }</svg>`,
										} }
									/>
									{ labels && (
										<span className="etu-social-share__label">
											{ net.label }
										</span>
									) }
								</span>
							</li>
						) ) }
					</ul>
				) }
			</div>
		</>
	);
}
