/**
 * Countdown block editor.
 *
 * The visual options (preset, alignment, labels, colours) come from the shared
 * schema Inspector; this file adds a "Countdown" panel for the target — a fixed date
 * (DateTimePicker) or an evergreen per-visitor duration — plus the completion
 * behaviour. The canvas previews the computed remaining time.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	DateTimePicker,
	Flex,
	FlexItem,
	__experimentalNumberControl as NumberControl,
	BaseControl,
} from '@wordpress/components';
import { useEffect } from '@wordpress/element';

import EtuInspector from '../../shared/inspector/EtuInspector';
import EtuBlockStyle from '../../shared/inspector/EtuBlockStyle';
import './editor.scss';

const pad = ( n ) => String( Math.max( 0, Math.floor( n ) ) ).padStart( 2, '0' );

const splitSeconds = ( total ) => {
	let s = Math.max( 0, Math.floor( total ) );
	const d = Math.floor( s / 86400 );
	s -= d * 86400;
	const h = Math.floor( s / 3600 );
	s -= h * 3600;
	const m = Math.floor( s / 60 );
	s -= m * 60;
	return { d: pad( d ), h: pad( h ), m: pad( m ), s: pad( s ) };
};

const LABELS = {
	d: __( 'Days', 'etu-blocks' ),
	h: __( 'Hours', 'etu-blocks' ),
	m: __( 'Minutes', 'etu-blocks' ),
	s: __( 'Seconds', 'etu-blocks' ),
};

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		mode,
		targetDate,
		evDays,
		evHours,
		evMinutes,
		showLabels,
		hideSeconds,
		onComplete,
		completedText,
		preset,
		blockId,
	} = attributes;

	useEffect( () => {
		if ( ! blockId ) {
			setAttributes( {
				blockId: clientId.replace( /-/g, '' ).slice( 0, 10 ),
			} );
		}
	}, [ blockId, clientId, setAttributes ] );

	let parts = { d: '00', h: '00', m: '00', s: '00' };
	let needsDate = false;
	if ( mode === 'evergreen' ) {
		parts = splitSeconds(
			( Number( evDays ) || 0 ) * 86400 +
				( Number( evHours ) || 0 ) * 3600 +
				( Number( evMinutes ) || 0 ) * 60
		);
	} else if ( targetDate ) {
		const remaining = Math.floor(
			( new Date( targetDate ).getTime() - Date.now() ) / 1000
		);
		parts = splitSeconds( remaining );
	} else {
		needsDate = true;
	}

	const units = hideSeconds ? [ 'd', 'h', 'm' ] : [ 'd', 'h', 'm', 's' ];

	const blockProps = useBlockProps( {
		className: `etu-countdown etu-countdown--preset-${ preset } etu-countdown--editor ${
			blockId ? `etu-countdown-${ blockId }` : ''
		}`,
	} );

	return (
		<>
			<EtuInspector
				name="etu/countdown"
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<EtuBlockStyle
				name="etu/countdown"
				attributes={ attributes }
				blockId={ blockId }
			/>

			<InspectorControls>
				<PanelBody title={ __( 'Countdown', 'etu-blocks' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Type', 'etu-blocks' ) }
						value={ mode }
						options={ [
							{
								value: 'date',
								label: __( 'To a fixed date', 'etu-blocks' ),
							},
							{
								value: 'evergreen',
								label: __(
									'Evergreen (per visitor)',
									'etu-blocks'
								),
							},
						] }
						onChange={ ( v ) => setAttributes( { mode: v } ) }
					/>

					{ mode === 'date' ? (
						<BaseControl
							__nextHasNoMarginBottom
							label={ __( 'Target date & time', 'etu-blocks' ) }
						>
							<DateTimePicker
								currentDate={ targetDate || undefined }
								onChange={ ( v ) =>
									setAttributes( { targetDate: v } )
								}
								is12Hour
							/>
						</BaseControl>
					) : (
						<Flex>
							<FlexItem>
								<NumberControl
									label={ __( 'Days', 'etu-blocks' ) }
									min={ 0 }
									value={ evDays }
									onChange={ ( v ) =>
										setAttributes( {
											evDays: parseInt( v, 10 ) || 0,
										} )
									}
								/>
							</FlexItem>
							<FlexItem>
								<NumberControl
									label={ __( 'Hours', 'etu-blocks' ) }
									min={ 0 }
									value={ evHours }
									onChange={ ( v ) =>
										setAttributes( {
											evHours: parseInt( v, 10 ) || 0,
										} )
									}
								/>
							</FlexItem>
							<FlexItem>
								<NumberControl
									label={ __( 'Minutes', 'etu-blocks' ) }
									min={ 0 }
									value={ evMinutes }
									onChange={ ( v ) =>
										setAttributes( {
											evMinutes: parseInt( v, 10 ) || 0,
										} )
									}
								/>
							</FlexItem>
						</Flex>
					) }

					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'When it ends', 'etu-blocks' ) }
						value={ onComplete }
						options={ [
							{
								value: 'message',
								label: __( 'Show a message', 'etu-blocks' ),
							},
							{
								value: 'hide',
								label: __( 'Hide the block', 'etu-blocks' ),
							},
						] }
						onChange={ ( v ) => setAttributes( { onComplete: v } ) }
					/>

					{ onComplete === 'message' && (
						<TextControl
							__nextHasNoMarginBottom
							label={ __( 'Completed message', 'etu-blocks' ) }
							value={ completedText }
							onChange={ ( v ) =>
								setAttributes( { completedText: v } )
							}
							placeholder={ __(
								'The countdown has ended.',
								'etu-blocks'
							) }
						/>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ needsDate ? (
					<div className="etu-countdown__setup">
						{ __(
							'Set a target date in the Countdown settings.',
							'etu-blocks'
						) }
					</div>
				) : (
					<div className="etu-countdown__timer" role="timer">
						{ units.map( ( u ) => (
							<div key={ u } className="etu-countdown__unit">
								<span className="etu-countdown__value">
									{ parts[ u ] }
								</span>
								{ showLabels && (
									<span className="etu-countdown__label">
										{ LABELS[ u ] }
									</span>
								) }
							</div>
						) ) }
					</div>
				) }
			</div>
		</>
	);
}
