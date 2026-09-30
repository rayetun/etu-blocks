<?php
/**
 * Taxonomy block — control schema (terms are queried at render).
 *
 * The taxonomy option list is built in the editor only. Columns are class-driven
 * (responsive breakpoints in CSS); colours/gap generate CSS variables.
 *
 * @package Etublocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Included within Schemas::get()'s method scope; local, not global.
$etublocks_tax_options = array( array( 'value' => 'category', 'label' => __( 'Categories', 'etu-blocks' ) ) );

if ( is_admin() ) {
	$etublocks_tax_options = array();
	foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $etublocks_tax ) {
		if ( ! $etublocks_tax->show_ui ) {
			continue;
		}
		$etublocks_tax_options[] = array( 'value' => $etublocks_tax->name, 'label' => $etublocks_tax->labels->name );
	}
}
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

return array(
	'sections' => array(
		array(
			'tab'      => 'layout',
			'title'    => __( 'Layout', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'taxonomy',
					'type'    => 'select',
					'label'   => __( 'Taxonomy', 'etu-blocks' ),
					'options' => $etublocks_tax_options,
				),
				array(
					'key'     => 'layout',
					'type'    => 'segmented',
					'label'   => __( 'Layout', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'grid', 'label' => __( 'Grid', 'etu-blocks' ) ),
						array( 'value' => 'list', 'label' => __( 'List', 'etu-blocks' ) ),
						array( 'value' => 'pills', 'label' => __( 'Pills', 'etu-blocks' ) ),
					),
				),
				array(
					'key'      => 'columns',
					'type'     => 'segmented',
					'label'    => __( 'Columns', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'layout', 'value' => 'grid' ),
					'options'  => array(
						array( 'value' => '2', 'label' => '2' ),
						array( 'value' => '3', 'label' => '3' ),
						array( 'value' => '4', 'label' => '4' ),
						array( 'value' => '5', 'label' => '5' ),
					),
				),
				array(
					'key'      => 'imageSource',
					'type'     => 'segmented',
					'label'    => __( 'Card image', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'layout', 'value' => 'grid' ),
					'help'     => __( 'Uses the term image, or the latest post’s featured image.', 'etu-blocks' ),
					'options'  => array(
						array( 'value' => 'auto', 'label' => __( 'Auto', 'etu-blocks' ) ),
						array( 'value' => 'none', 'label' => __( 'None', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'cardGap',
					'type'  => 'range',
					'label' => __( 'Gap', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 0,
					'max'   => 48,
					'css'   => array( 'selector' => '', 'property' => '--etu-tax-gap' ),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Content', 'etu-blocks' ),
			'controls' => array(
				array( 'key' => 'showCount', 'type' => 'toggle', 'label' => __( 'Post count', 'etu-blocks' ) ),
				array( 'key' => 'showDescription', 'type' => 'toggle', 'label' => __( 'Description', 'etu-blocks' ) ),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Query', 'etu-blocks' ),
			'controls' => array(
				array( 'key' => 'hideEmpty', 'type' => 'toggle', 'label' => __( 'Hide empty terms', 'etu-blocks' ) ),
				array(
					'key'     => 'orderBy',
					'type'    => 'select',
					'label'   => __( 'Order by', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'name', 'label' => __( 'Name', 'etu-blocks' ) ),
						array( 'value' => 'count', 'label' => __( 'Post count', 'etu-blocks' ) ),
					),
				),
				array(
					'key'     => 'order',
					'type'    => 'segmented',
					'label'   => __( 'Order', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'asc', 'label' => __( 'Asc', 'etu-blocks' ) ),
						array( 'value' => 'desc', 'label' => __( 'Desc', 'etu-blocks' ) ),
					),
				),
				array(
					'key'     => 'parentScope',
					'type'    => 'segmented',
					'label'   => __( 'Show', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'all', 'label' => __( 'All', 'etu-blocks' ) ),
						array( 'value' => 'top', 'label' => __( 'Top level', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'number',
					'type'  => 'range',
					'label' => __( 'Limit (0 = all)', 'etu-blocks' ),
					'min'   => 0,
					'max'   => 48,
					'step'  => 1,
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Colours', 'etu-blocks' ),
			'controls' => array(
				array( 'key' => 'accentColor', 'type' => 'color', 'label' => __( 'Accent', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => '--etu-tax-accent' ) ),
				array( 'key' => 'cardBg', 'type' => 'color', 'label' => __( 'Card background', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => '--etu-tax-card-bg' ) ),
				array( 'key' => 'textColor', 'type' => 'color', 'label' => __( 'Text colour', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => '--etu-tax-text' ) ),
				array( 'key' => 'countColor', 'type' => 'color', 'label' => __( 'Count colour', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => '--etu-tax-count' ) ),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Spacing', 'etu-blocks' ),
			'controls' => array(
				array( 'key' => 'blockPadding', 'type' => 'spacing', 'label' => __( 'Padding', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => 'padding' ) ),
				array( 'key' => 'blockMargin', 'type' => 'spacing', 'label' => __( 'Margin', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => 'margin' ) ),
			),
		),
	),
);
