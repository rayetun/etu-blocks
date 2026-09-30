/**
 * EtuBlockStyle — live editor preview of the schema-generated CSS.
 *
 * The front end generates each block's CSS in PHP (class-css-generator.php). The
 * editor canvas doesn't run that, so control changes (alignment, colours,
 * typography, spacing, gap) wouldn't preview. This is a faithful JS mirror of the
 * PHP generator: it reads the same schema (window.etublocksSchemas) + attributes,
 * builds the same scoped CSS, and injects it as a <style> in the block's canvas.
 * Keep it in sync with class-css-generator.php.
 */
import { getBlockSlug } from './slug';

const BREAKPOINTS = { tablet: 1024, mobile: 767 };

function lengthStr( raw, control ) {
	const units = Array.isArray( control.units ) ? control.units : [ 'px' ];
	const def = units[ 0 ] || 'px';
	let num;
	let unit;
	if ( raw && typeof raw === 'object' && 'value' in raw ) {
		num = raw.value;
		unit = raw.unit || def;
	} else if ( typeof raw === 'number' ) {
		num = raw;
		unit = def;
	} else {
		return '';
	}
	if ( typeof num !== 'number' || isNaN( num ) ) {
		return '';
	}
	if ( control.min != null ) {
		num = Math.max( control.min, num );
	}
	if ( control.max != null ) {
		num = Math.min( control.max, num );
	}
	if ( ! units.includes( unit ) ) {
		unit = def;
	}
	return `${ num }${ unit }`;
}

function boxStr( value ) {
	if ( typeof value !== 'string' ) {
		return '';
	}
	const v = value.trim();
	return /^-?\d*\.?\d+(px|em|rem|%|vh|vw)$/.test( v ) ? v : '';
}

function colorStr( value ) {
	if ( typeof value !== 'string' ) {
		return '';
	}
	const v = value.trim();
	if ( /^#([0-9a-fA-F]{3}|[0-9a-fA-F]{4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/.test( v ) ) {
		return v;
	}
	if ( /^(rgb|rgba|hsl|hsla)\(/i.test( v ) ) {
		return v;
	}
	return '';
}

function alignVal( value, prop ) {
	const v = String( value ).toLowerCase();
	if ( prop === 'justify-content' || ! prop ) {
		return (
			{ left: 'flex-start', center: 'center', right: 'flex-end', justify: 'space-between' }[ v ] || ''
		);
	}
	return [ 'left', 'center', 'right', 'justify' ].includes( v ) ? v : '';
}

function typographyDecls( raw ) {
	const out = {};
	if ( raw.fontSize ) {
		const s = lengthStr( raw.fontSize, { units: [ 'px', 'em', 'rem', '%' ] } );
		if ( s ) {
			out[ 'font-size' ] = s;
		}
	}
	if (
		raw.fontWeight &&
		/^(100|200|300|400|500|600|700|800|900|normal|bold|lighter|bolder)$/.test( String( raw.fontWeight ) )
	) {
		out[ 'font-weight' ] = String( raw.fontWeight );
	}
	if ( raw.letterSpacing ) {
		const s = lengthStr( raw.letterSpacing, { units: [ 'px', 'em', 'rem' ] } );
		if ( s ) {
			out[ 'letter-spacing' ] = s;
		}
	}
	if ( raw.textTransform && [ 'none', 'uppercase', 'lowercase', 'capitalize' ].includes( raw.textTransform ) ) {
		out[ 'text-transform' ] = raw.textTransform;
	}
	if ( raw.lineHeight != null && raw.lineHeight !== '' && ! isNaN( Number( raw.lineHeight ) ) ) {
		out[ 'line-height' ] = String( Number( raw.lineHeight ) );
	}
	return out;
}

export function generateCss( name, attributes, scope ) {
	const schema =
		( typeof window !== 'undefined' && window.etublocksSchemas && window.etublocksSchemas[ name ] ) || {
			sections: [],
		};
	const buckets = { base: {}, tablet: {}, mobile: {} };
	const add = ( device, selector, prop, val ) => {
		if ( ! val ) {
			return;
		}
		if ( ! buckets[ device ][ selector ] ) {
			buckets[ device ][ selector ] = {};
		}
		buckets[ device ][ selector ][ prop ] = val;
	};

	( schema.sections || [] ).forEach( ( section ) => {
		( section.controls || [] ).forEach( ( control ) => {
			if ( ! control.css || ! control.key ) {
				return;
			}
			const raw = attributes[ control.key ];
			if ( raw == null || raw === '' || ( Array.isArray( raw ) && ! raw.length ) ) {
				return;
			}
			const selector = ( scope + ' ' + ( control.css.selector || '' ) ).trim();
			const prop = control.css.property || '';
			switch ( control.type ) {
				case 'range':
				case 'unit':
					if ( control.responsive && raw && typeof raw === 'object' ) {
						[
							[ 'desktop', 'base' ],
							[ 'tablet', 'tablet' ],
							[ 'mobile', 'mobile' ],
						].forEach( ( [ d, b ] ) => {
							if ( raw[ d ] != null ) {
								const v = lengthStr( raw[ d ], control );
								if ( v && prop ) {
									add( b, selector, prop, v );
								}
							}
						} );
					} else {
						const v = lengthStr( raw, control );
						if ( v && prop ) {
							add( 'base', selector, prop, v );
						}
					}
					break;
				case 'color': {
					const v = colorStr( raw );
					if ( v && prop ) {
						add( 'base', selector, prop, v );
					}
					break;
				}
				case 'alignment': {
					const v = alignVal( raw, prop );
					if ( v ) {
						add( 'base', selector, prop || 'justify-content', v );
					}
					break;
				}
				case 'typography':
					if ( raw && typeof raw === 'object' ) {
						Object.entries( typographyDecls( raw ) ).forEach( ( [ p, v ] ) =>
							add( 'base', selector, p, v )
						);
					}
					break;
				case 'spacing':
					if ( raw && typeof raw === 'object' ) {
						const p = prop || 'padding';
						[ 'top', 'right', 'bottom', 'left' ].forEach( ( side ) => {
							const v = boxStr( raw[ side ] );
							if ( v ) {
								add( 'base', selector, `${ p }-${ side }`, v );
							}
						} );
					}
					break;
			}
		} );
	} );

	const renderRules = ( rules ) =>
		Object.entries( rules )
			.map( ( [ sel, props ] ) =>
				Object.keys( props ).length
					? sel +
					  '{' +
					  Object.entries( props )
							.map( ( [ p, v ] ) => `${ p }:${ v }` )
							.join( ';' ) +
					  '}'
					: ''
			)
			.join( '' );

	let css = renderRules( buckets.base );
	if ( Object.keys( buckets.tablet ).length ) {
		css += `@media (max-width:${ BREAKPOINTS.tablet }px){${ renderRules( buckets.tablet ) }}`;
	}
	if ( Object.keys( buckets.mobile ).length ) {
		css += `@media (max-width:${ BREAKPOINTS.mobile }px){${ renderRules( buckets.mobile ) }}`;
	}
	return css;
}

/**
 * Inject the scoped generated CSS into the block canvas for a live preview.
 *
 * @param {Object} props            Props.
 * @param {string} props.name       Block name (e.g. "etu/tabs").
 * @param {Object} props.attributes Block attributes.
 * @param {string} props.blockId    Per-instance id.
 * @return {JSX.Element|null} A <style> element, or null.
 */
export default function EtuBlockStyle( { name, attributes, blockId } ) {
	if ( ! blockId ) {
		return null;
	}
	const scope = `.etu-${ getBlockSlug( name ) }-${ blockId }`;
	const css = generateCss( name, attributes, scope );
	if ( ! css ) {
		return null;
	}
	return <style>{ css }</style>;
}
