/**
 * EtuInspector — the shared, schema-driven Inspector renderer.
 *
 * Reads a block's schema from window.etublocksSchemas[name] (localized from PHP),
 * groups its sections into Layout / Style / Advanced tabs, and renders each
 * control by type. Controls may declare `showWhen` to appear only for certain
 * attribute values. This is the entire Inspector for every Etu block — no
 * per-block control code. See references/editor-architecture.md.
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { TabPanel, PanelBody } from '@wordpress/components';

import { renderControl } from './controls';
import './inspector.scss';

const TABS = [
	{ name: 'layout', title: __( 'Layout', 'etu-blocks' ) },
	{ name: 'style', title: __( 'Style', 'etu-blocks' ) },
	{ name: 'advanced', title: __( 'Advanced', 'etu-blocks' ) },
];

/**
 * Whether a control is visible for the current attributes.
 *
 * `showWhen`: { key, value } | { key, in: [] } | { key, notIn: [] }
 *
 * @param {Object} control    Schema control.
 * @param {Object} attributes Block attributes.
 * @return {boolean} Visible.
 */
function isVisible( control, attributes ) {
	const cond = control.showWhen;
	if ( ! cond || ! cond.key ) {
		return true;
	}
	const val = attributes[ cond.key ];
	if ( 'value' in cond ) {
		return val === cond.value;
	}
	if ( Array.isArray( cond.in ) ) {
		return cond.in.includes( val );
	}
	if ( Array.isArray( cond.notIn ) ) {
		return ! cond.notIn.includes( val );
	}
	return true;
}

export default function EtuInspector( { name, attributes, setAttributes } ) {
	const schema =
		( typeof window !== 'undefined' &&
			window.etublocksSchemas &&
			window.etublocksSchemas[ name ] ) ||
		{ sections: [] };
	const sections = schema.sections || [];

	const visibleControls = ( section ) =>
		( section.controls || [] ).filter( ( c ) => isVisible( c, attributes ) );

	const sectionsForTab = ( tabName ) =>
		sections.filter(
			( section ) =>
				( section.tab || 'style' ) === tabName &&
				visibleControls( section ).length
		);

	const tabs = TABS.filter( ( tab ) => sectionsForTab( tab.name ).length );
	if ( ! tabs.length ) {
		return null;
	}

	return (
		<InspectorControls>
			<TabPanel className="etu-inspector-tabs" tabs={ tabs }>
				{ ( tab ) =>
					sectionsForTab( tab.name ).map( ( section, sIndex ) => (
						<PanelBody
							key={ sIndex }
							title={ section.title || '' }
							initialOpen={ true }
						>
							{ visibleControls( section ).map( ( control ) => (
								<div
									key={ control.key }
									className="etu-control-row"
								>
									{ renderControl( {
										control,
										attributes,
										setAttributes,
									} ) }
								</div>
							) ) }
						</PanelBody>
					) )
				}
			</TabPanel>
		</InspectorControls>
	);
}
