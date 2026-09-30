<?php
/**
 * Table of Contents block — control schema.
 *
 * @package Etublocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Included within Schemas::get()'s method scope; local, not global.
$etu_toc_levels = array(
	array( 'value' => '2', 'label' => 'H2' ),
	array( 'value' => '3', 'label' => 'H3' ),
	array( 'value' => '4', 'label' => 'H4' ),
	array( 'value' => '5', 'label' => 'H5' ),
	array( 'value' => '6', 'label' => 'H6' ),
);
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
						array( 'value' => 'boxed', 'label' => __( 'Boxed', 'etu-blocks' ) ),
						array( 'value' => 'bordered', 'label' => __( 'Bordered', 'etu-blocks' ) ),
						array( 'value' => 'plain', 'label' => __( 'Plain', 'etu-blocks' ) ),
					),
				),
				array(
					'key'     => 'listType',
					'type'    => 'segmented',
					'label'   => __( 'Markers', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'number', 'label' => __( 'Numbered', 'etu-blocks' ) ),
						array( 'value' => 'bullet', 'label' => __( 'Bulleted', 'etu-blocks' ) ),
						array( 'value' => 'plain', 'label' => __( 'Plain', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'showTitle',
					'type'  => 'toggle',
					'label' => __( 'Show title', 'etu-blocks' ),
				),
				array(
					'key'   => 'title',
					'type'  => 'text',
					'label' => __( 'Title', 'etu-blocks' ),
				),
				array(
					'key'   => 'collapsible',
					'type'  => 'toggle',
					'label' => __( 'Collapsible', 'etu-blocks' ),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Headings', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'minLevel',
					'type'    => 'select',
					'label'   => __( 'From', 'etu-blocks' ),
					'options' => $etu_toc_levels,
				),
				array(
					'key'     => 'maxLevel',
					'type'    => 'select',
					'label'   => __( 'To', 'etu-blocks' ),
					'options' => $etu_toc_levels,
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Spacing', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'blockPadding',
					'type'  => 'spacing',
					'label' => __( 'Padding', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-toc__inner', 'property' => 'padding' ),
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
					'key'   => 'accentColor',
					'type'  => 'color',
					'label' => __( 'Accent (links & active)', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-toc-accent' ),
				),
				array(
					'key'   => 'background',
					'type'  => 'color',
					'label' => __( 'Background', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-toc-bg' ),
				),
				array(
					'key'   => 'textColor',
					'type'  => 'color',
					'label' => __( 'Text colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-toc__inner', 'property' => 'color' ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Behaviour', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'sticky',
					'type'  => 'toggle',
					'label' => __( 'Sticky on scroll', 'etu-blocks' ),
				),
				array(
					'key'   => 'smoothScroll',
					'type'  => 'toggle',
					'label' => __( 'Smooth scroll', 'etu-blocks' ),
				),
				array(
					'key'   => 'scrollspy',
					'type'  => 'toggle',
					'label' => __( 'Highlight active section', 'etu-blocks' ),
				),
				array(
					'key'   => 'initialCollapsed',
					'type'  => 'toggle',
					'label' => __( 'Start collapsed', 'etu-blocks' ),
				),
			),
		),
	),
);
