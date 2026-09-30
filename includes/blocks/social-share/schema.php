<?php
/**
 * Social Share block — control schema.
 *
 * @package Etublocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/networks.php';

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Included within Schemas::get()'s method scope; these are local, not global.
$etu_ss_network_options = array();
foreach ( etublocks_social_networks() as $etu_ss_key => $etu_ss_net ) {
	$etu_ss_network_options[] = array( 'value' => $etu_ss_key, 'label' => $etu_ss_net['label'] );
}
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

return array(
	'sections' => array(
		array(
			'tab'      => 'layout',
			'title'    => __( 'Design', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'preset',
					'type'    => 'preset',
					'label'   => __( 'Style', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'solid', 'label' => __( 'Solid', 'etu-blocks' ) ),
						array( 'value' => 'outline', 'label' => __( 'Outline', 'etu-blocks' ) ),
						array( 'value' => 'minimal', 'label' => __( 'Minimal', 'etu-blocks' ) ),
					),
				),
				array(
					'key'     => 'buttonShape',
					'type'    => 'segmented',
					'label'   => __( 'Shape', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'rounded', 'label' => __( 'Rounded', 'etu-blocks' ) ),
						array( 'value' => 'pill', 'label' => __( 'Pill', 'etu-blocks' ) ),
						array( 'value' => 'circle', 'label' => __( 'Circle', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'shareAlign',
					'type'  => 'alignment',
					'label' => __( 'Alignment', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-social-share__list', 'property' => 'justify-content' ),
				),
				array(
					'key'   => 'showLabels',
					'type'  => 'toggle',
					'label' => __( 'Show labels', 'etu-blocks' ),
					'help'  => __( 'Circle shape always hides labels.', 'etu-blocks' ),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Networks', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'networks',
					'type'    => 'checklist',
					'label'   => __( 'Networks to show', 'etu-blocks' ),
					'options' => $etu_ss_network_options,
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Spacing & size', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'iconSize',
					'type'  => 'range',
					'label' => __( 'Icon size', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 12,
					'max'   => 40,
					'css'   => array( 'selector' => '', 'property' => '--etu-ss-icon' ),
				),
				array(
					'key'   => 'buttonGap',
					'type'  => 'range',
					'label' => __( 'Gap', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 0,
					'max'   => 40,
					'css'   => array( 'selector' => '.etu-social-share__list', 'property' => 'gap' ),
				),
				array(
					'key'   => 'blockPadding',
					'type'  => 'spacing',
					'label' => __( 'Padding', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => 'padding' ),
				),
				array(
					'key'   => 'blockMargin',
					'type'  => 'spacing',
					'label' => __( 'Margin', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => 'margin' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Colours', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'useBrandColors',
					'type'  => 'toggle',
					'label' => __( 'Use brand colours', 'etu-blocks' ),
					'help'  => __( 'Off uses your accent colour for every button.', 'etu-blocks' ),
				),
				array(
					'key'   => 'accentColor',
					'type'  => 'color',
					'label' => __( 'Accent colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-ss-accent' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Typography', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'labelTypography',
					'type'  => 'typography',
					'label' => __( 'Label', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-social-share__label' ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Share target', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'shareUrl',
					'type'  => 'url',
					'label' => __( 'Custom URL', 'etu-blocks' ),
					'help'  => __( 'Leave empty to share the current page.', 'etu-blocks' ),
				),
				array(
					'key'   => 'shareText',
					'type'  => 'text',
					'label' => __( 'Custom text', 'etu-blocks' ),
				),
				array(
					'key'   => 'openInNewTab',
					'type'  => 'toggle',
					'label' => __( 'Open links in a new tab', 'etu-blocks' ),
				),
			),
		),
	),
);
