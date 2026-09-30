/**
 * Google Map block editor.
 *
 * Previews the premium facade (stylised map backdrop, pin and info card) without
 * ever calling Google in the editor — the real iframe only loads on the front end.
 * All options come from the shared schema Inspector.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

const Pin = () => (
	<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
		<path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z" />
	</svg>
);

const Arrow = () => (
	<svg
		viewBox="0 0 24 24"
		fill="none"
		stroke="currentColor"
		strokeWidth="2"
		strokeLinecap="round"
		strokeLinejoin="round"
		aria-hidden="true"
		focusable="false"
	>
		<path d="M5 12h14M13 6l6 6-6 6" />
	</svg>
);

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		query,
		aspectRatio,
		grayscale,
		clickToLoad,
		title,
		address,
		showInfoCard,
		showDirections,
		blockId,
	} = attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( {
				blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ),
			} );
		}
	}, [ blockId, clientId, setAttributes ] );

	const location = address || query;
	const hasInfo =
		showInfoCard && ( title || address || showDirections );

	const blockProps = useBlockProps( {
		className: `etu-map etu-map--ar-${ aspectRatio } etu-map--editor ${
			grayscale ? 'is-grayscale' : ''
		} ${ blockId ? `etu-map-${ blockId }` : '' }`,
	} );

	return (
		<>
			<EtuInspector
				name="etu/map"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/map"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<div { ...blockProps }>
				<div className="etu-map__frame">
					{ query ? (
						<>
							<div className="etu-map__facade">
								<span className="etu-map__facade-bg" />
								<span className="etu-map__pin">
									<Pin />
								</span>
								{ clickToLoad && (
									<div className="etu-map__facade-card">
										<p className="etu-map__facade-loc">
											{ location }
										</p>
										<span className="etu-map__load">
											{ __( 'Load map', 'etu-blocks' ) }
										</span>
										<span className="etu-map__facade-note">
											{ __(
												'Loads Google Maps',
												'etu-blocks'
											) }
										</span>
									</div>
								) }
							</div>

							{ hasInfo && ! clickToLoad && (
								<div className="etu-map__info">
									{ ( title || address ) && (
										<div className="etu-map__info-body">
											{ title && (
												<p className="etu-map__info-title">
													{ title }
												</p>
											) }
											{ address && (
												<p className="etu-map__info-address">
													{ address }
												</p>
											) }
										</div>
									) }
									{ showDirections && (
										<span className="etu-map__directions">
											<span>
												{ __(
													'Get directions',
													'etu-blocks'
												) }
											</span>
											<Arrow />
										</span>
									) }
								</div>
							) }
						</>
					) : (
						<div className="etu-map__setup">
							<span className="etu-map__setup-pin">
								<Pin />
							</span>
							<p>
								{ __(
									'Enter an address or place in the block settings.',
									'etu-blocks'
								) }
							</p>
						</div>
					) }
				</div>
			</div>
		</>
	);
}
