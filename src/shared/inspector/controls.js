/**
 * Shared control primitives for the schema-driven Inspector.
 *
 * Each control receives { control, value, onChange } and is chosen by
 * `control.type` in renderControl(). Responsive controls manage their own device
 * switch and store a { desktop, tablet, mobile } value; length controls store
 * { value, unit }. See references/editor-architecture.md.
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import {
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	__experimentalToggleGroupControlOptionIcon as ToggleGroupControlOptionIcon,
	RangeControl,
	SelectControl,
	ToggleControl,
	CheckboxControl,
	TextControl,
	BaseControl,
	ColorPalette,
	Button,
	Flex,
	FlexItem,
	FlexBlock,
	__experimentalBoxControl as BoxControl,
} from '@wordpress/components';
import {
	desktop,
	tablet,
	mobile,
	alignLeft,
	alignCenter,
	alignRight,
	alignJustify,
	rotateLeft,
} from '@wordpress/icons';

const DEVICES = [
	{ key: 'desktop', icon: desktop, label: __( 'Desktop', 'etu-blocks' ) },
	{ key: 'tablet', icon: tablet, label: __( 'Tablet', 'etu-blocks' ) },
	{ key: 'mobile', icon: mobile, label: __( 'Mobile', 'etu-blocks' ) },
];

const ALIGN_OPTIONS = [
	{ value: 'left', icon: alignLeft, label: __( 'Left', 'etu-blocks' ) },
	{ value: 'center', icon: alignCenter, label: __( 'Center', 'etu-blocks' ) },
	{ value: 'right', icon: alignRight, label: __( 'Right', 'etu-blocks' ) },
	{ value: 'justify', icon: alignJustify, label: __( 'Justify', 'etu-blocks' ) },
];

function Segmented( { control, value, onChange } ) {
	return (
		<ToggleGroupControl
			__nextHasNoMarginBottom
			isBlock
			label={ control.label }
			value={ value }
			onChange={ onChange }
		>
			{ ( control.options || [] ).map( ( option ) => (
				<ToggleGroupControlOption
					key={ option.value }
					value={ option.value }
					label={ option.label }
				/>
			) ) }
		</ToggleGroupControl>
	);
}

function Alignment( { control, value, onChange } ) {
	return (
		<ToggleGroupControl
			__nextHasNoMarginBottom
			isBlock
			label={ control.label }
			value={ value }
			onChange={ onChange }
		>
			{ ALIGN_OPTIONS.map( ( option ) => (
				<ToggleGroupControlOptionIcon
					key={ option.value }
					value={ option.value }
					icon={ option.icon }
					label={ option.label }
				/>
			) ) }
		</ToggleGroupControl>
	);
}

function ColorControl( { control, value, onChange } ) {
	return (
		<BaseControl __nextHasNoMarginBottom label={ control.label }>
			<ColorPalette
				value={ value }
				onChange={ ( color ) => onChange( color ) }
			/>
		</BaseControl>
	);
}

function ResponsiveRange( { control, value, onChange } ) {
	const responsive = !! control.responsive;
	const units = Array.isArray( control.units ) ? control.units : null;
	const [ device, setDevice ] = useState( 'desktop' );

	const current = responsive ? value && value[ device ] : value;
	const num =
		current && typeof current === 'object'
			? current.value
			: typeof current === 'number'
			? current
			: '';
	const unit =
		current && typeof current === 'object'
			? current.unit
			: units
			? units[ 0 ]
			: undefined;

	const commit = ( nextNum, nextUnit ) => {
		const packed = units
			? { value: nextNum, unit: nextUnit ?? unit ?? units[ 0 ] }
			: nextNum;
		if ( responsive ) {
			onChange( { ...( value || {} ), [ device ]: packed } );
		} else {
			onChange( packed );
		}
	};

	const reset = () => {
		if ( responsive ) {
			const next = { ...( value || {} ) };
			delete next[ device ];
			onChange( next );
		} else {
			onChange( undefined );
		}
	};

	return (
		<div className="etu-control etu-control--range">
			<Flex align="center" justify="space-between">
				<FlexItem>
					<span className="etu-control__label">{ control.label }</span>
				</FlexItem>
				<FlexItem>
					<Flex gap={ 0 }>
						{ responsive &&
							DEVICES.map( ( d ) => (
								<Button
									key={ d.key }
									size="small"
									icon={ d.icon }
									label={ d.label }
									isPressed={ device === d.key }
									onClick={ () => setDevice( d.key ) }
								/>
							) ) }
						<Button
							size="small"
							icon={ rotateLeft }
							label={ __( 'Reset', 'etu-blocks' ) }
							onClick={ reset }
						/>
					</Flex>
				</FlexItem>
			</Flex>
			<Flex align="center">
				<FlexBlock>
					<RangeControl
						__nextHasNoMarginBottom
						hideLabelFromVision
						label={ control.label }
						value={ typeof num === 'number' ? num : undefined }
						min={ control.min ?? 0 }
						max={ control.max ?? 100 }
						step={ control.step ?? 1 }
						onChange={ ( v ) => commit( v ?? 0, unit ) }
					/>
				</FlexBlock>
				{ units && (
					<FlexItem>
						<SelectControl
							__nextHasNoMarginBottom
							hideLabelFromVision
							label={ __( 'Unit', 'etu-blocks' ) }
							value={ unit }
							options={ units.map( ( u ) => ( { value: u, label: u } ) ) }
							onChange={ ( u ) => commit( typeof num === 'number' ? num : 0, u ) }
						/>
					</FlexItem>
				) }
			</Flex>
		</div>
	);
}

const FONT_WEIGHTS = [
	{ value: '', label: __( 'Default', 'etu-blocks' ) },
	{ value: '300', label: __( 'Light (300)', 'etu-blocks' ) },
	{ value: '400', label: __( 'Normal (400)', 'etu-blocks' ) },
	{ value: '500', label: __( 'Medium (500)', 'etu-blocks' ) },
	{ value: '600', label: __( 'Semi-bold (600)', 'etu-blocks' ) },
	{ value: '700', label: __( 'Bold (700)', 'etu-blocks' ) },
	{ value: '800', label: __( 'Extra-bold (800)', 'etu-blocks' ) },
];

const TEXT_TRANSFORMS = [
	{ value: '', label: __( 'Default', 'etu-blocks' ) },
	{ value: 'none', label: __( 'None', 'etu-blocks' ) },
	{ value: 'uppercase', label: __( 'Uppercase', 'etu-blocks' ) },
	{ value: 'lowercase', label: __( 'Lowercase', 'etu-blocks' ) },
	{ value: 'capitalize', label: __( 'Capitalize', 'etu-blocks' ) },
];

const BOX_UNITS = [
	{ value: 'px', label: 'px' },
	{ value: 'em', label: 'em' },
	{ value: 'rem', label: 'rem' },
	{ value: '%', label: '%' },
];

function Typography( { control, value, onChange } ) {
	const v = value || {};
	const set = ( key, val ) => onChange( { ...v, [ key ]: val } );
	const setLength = ( key, num, unit ) =>
		set( key, { value: num ?? 0, unit } );
	const fontSize = v.fontSize || {};
	const letterSpacing = v.letterSpacing || {};

	return (
		<div className="etu-typography">
			<span className="etu-control__label">
				{ control.label || __( 'Typography', 'etu-blocks' ) }
			</span>
			<RangeControl
				__nextHasNoMarginBottom
				label={ __( 'Font size (px)', 'etu-blocks' ) }
				value={ fontSize.value }
				min={ 8 }
				max={ 96 }
				onChange={ ( n ) => setLength( 'fontSize', n, fontSize.unit || 'px' ) }
			/>
			<SelectControl
				__nextHasNoMarginBottom
				label={ __( 'Font weight', 'etu-blocks' ) }
				value={ v.fontWeight || '' }
				options={ FONT_WEIGHTS }
				onChange={ ( x ) => set( 'fontWeight', x ) }
			/>
			<RangeControl
				__nextHasNoMarginBottom
				label={ __( 'Letter spacing (px)', 'etu-blocks' ) }
				value={ letterSpacing.value }
				min={ -2 }
				max={ 10 }
				step={ 0.1 }
				onChange={ ( n ) =>
					setLength( 'letterSpacing', n, letterSpacing.unit || 'px' )
				}
			/>
			<SelectControl
				__nextHasNoMarginBottom
				label={ __( 'Text transform', 'etu-blocks' ) }
				value={ v.textTransform || '' }
				options={ TEXT_TRANSFORMS }
				onChange={ ( x ) => set( 'textTransform', x ) }
			/>
			<TextControl
				__nextHasNoMarginBottom
				label={ __( 'Line height', 'etu-blocks' ) }
				type="number"
				step={ 0.1 }
				value={ v.lineHeight || '' }
				onChange={ ( x ) => set( 'lineHeight', x ) }
			/>
		</div>
	);
}

function Spacing( { control, value, onChange } ) {
	return (
		<BoxControl
			__next40pxDefaultSize
			label={ control.label }
			values={ value || {} }
			units={ BOX_UNITS }
			onChange={ ( next ) => onChange( next ) }
		/>
	);
}

function Preset( { control, value, onChange } ) {
	const options = control.options || [];
	return (
		<BaseControl __nextHasNoMarginBottom label={ control.label }>
			<div className="etu-preset-grid">
				{ options.map( ( option ) => (
					<button
						key={ option.value }
						type="button"
						className={
							'etu-preset' +
							( value === option.value ? ' is-selected' : '' )
						}
						aria-pressed={ value === option.value }
						onClick={ () => onChange( option.value ) }
					>
						<span
							className={ `etu-preset__preview etu-preset__preview--${ option.value }` }
							aria-hidden="true"
						/>
						<span className="etu-preset__label">
							{ option.label }
						</span>
					</button>
				) ) }
			</div>
		</BaseControl>
	);
}

function Media( { control, value, onChange } ) {
	const v = value || {};
	return (
		<BaseControl __nextHasNoMarginBottom label={ control.label }>
			<MediaUploadCheck>
				<MediaUpload
					allowedTypes={ [ 'image' ] }
					value={ v.id }
					onSelect={ ( media ) =>
						onChange( {
							id: media.id,
							url: media.url,
							alt: media.alt || '',
						} )
					}
					render={ ( { open } ) => (
						<div className="etu-media-control">
							{ v.url && (
								<img
									className="etu-media-control__preview"
									src={ v.url }
									alt=""
								/>
							) }
							<div className="etu-media-control__buttons">
								<Button
									variant="secondary"
									size="small"
									onClick={ open }
								>
									{ v.url
										? __( 'Replace', 'etu-blocks' )
										: __( 'Select image', 'etu-blocks' ) }
								</Button>
								{ v.url && (
									<Button
										variant="link"
										size="small"
										isDestructive
										onClick={ () => onChange( {} ) }
									>
										{ __( 'Remove', 'etu-blocks' ) }
									</Button>
								) }
							</div>
						</div>
					) }
				/>
			</MediaUploadCheck>
		</BaseControl>
	);
}

function Checklist( { control, value, onChange } ) {
	const selected = Array.isArray( value ) ? value : [];
	const toggle = ( key, on ) => {
		onChange(
			on
				? [ ...selected, key ]
				: selected.filter( ( k ) => k !== key )
		);
	};
	return (
		<BaseControl __nextHasNoMarginBottom label={ control.label }>
			<div className="etu-checklist">
				{ ( control.options || [] ).map( ( option ) => (
					<CheckboxControl
						__nextHasNoMarginBottom
						key={ option.value }
						label={ option.label }
						checked={ selected.includes( option.value ) }
						onChange={ ( on ) => toggle( option.value, on ) }
					/>
				) ) }
			</div>
		</BaseControl>
	);
}

/**
 * Render one control from its schema entry.
 *
 * @param {Object}   props               Props.
 * @param {Object}   props.control       Schema control entry.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 * @return {JSX.Element|null} The control.
 */
export function renderControl( { control, attributes, setAttributes } ) {
	const value = attributes[ control.key ];
	const onChange = ( next ) => setAttributes( { [ control.key ]: next } );
	const common = { control, value, onChange };

	switch ( control.type ) {
		case 'segmented':
			return <Segmented { ...common } />;
		case 'alignment':
			return <Alignment { ...common } />;
		case 'range':
		case 'unit':
			return <ResponsiveRange { ...common } />;
		case 'color':
			return <ColorControl { ...common } />;
		case 'typography':
			return <Typography { ...common } />;
		case 'spacing':
			return <Spacing { ...common } />;
		case 'preset':
			return <Preset { ...common } />;
		case 'checklist':
			return <Checklist { ...common } />;
		case 'media':
			return <Media { ...common } />;
		case 'toggle':
			return (
				<ToggleControl
					__nextHasNoMarginBottom
					label={ control.label }
					help={ control.help }
					checked={ !! value }
					onChange={ onChange }
				/>
			);
		case 'select':
			return (
				<SelectControl
					__nextHasNoMarginBottom
					label={ control.label }
					help={ control.help }
					value={ value }
					options={ control.options || [] }
					onChange={ onChange }
				/>
			);
		case 'text':
		case 'url':
			return (
				<TextControl
					__nextHasNoMarginBottom
					label={ control.label }
					type={ control.type === 'url' ? 'url' : 'text' }
					value={ value || '' }
					onChange={ onChange }
				/>
			);
		default:
			return null;
	}
}
