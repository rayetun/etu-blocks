<?php
/**
 * Data Table block — control schema.
 *
 * Columns and rows are edited directly in the canvas (a small grid editor); this
 * schema drives the behaviour toggles, layout options and colours, plus the
 * generated CSS variables the table reads.
 *
 * @package Etublocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'sections' => array(
		array(
			'tab'      => 'layout',
			'title'    => __( 'Table', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'sortable',
					'type'  => 'toggle',
					'label' => __( 'Sortable columns', 'etu-blocks' ),
					'help'  => __( 'Let visitors sort by clicking a column header.', 'etu-blocks' ),
				),
				array(
					'key'   => 'searchable',
					'type'  => 'toggle',
					'label' => __( 'Search box', 'etu-blocks' ),
					'help'  => __( 'Show a field that filters rows as you type.', 'etu-blocks' ),
				),
				array(
					'key'   => 'stickyHeader',
					'type'  => 'toggle',
					'label' => __( 'Sticky header', 'etu-blocks' ),
				),
				array(
					'key'     => 'responsive',
					'type'    => 'select',
					'label'   => __( 'On small screens', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'scroll', 'label' => __( 'Scroll sideways', 'etu-blocks' ) ),
						array( 'value' => 'stack', 'label' => __( 'Stack into cards', 'etu-blocks' ) ),
					),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Style', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'striped',
					'type'  => 'toggle',
					'label' => __( 'Striped rows', 'etu-blocks' ),
				),
				array(
					'key'   => 'bordered',
					'type'  => 'toggle',
					'label' => __( 'Cell borders', 'etu-blocks' ),
				),
				array(
					'key'   => 'compact',
					'type'  => 'toggle',
					'label' => __( 'Compact spacing', 'etu-blocks' ),
				),
				array(
					'key'   => 'blockPadding',
					'type'  => 'spacing',
					'label' => __( 'Padding', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => 'padding' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Colours', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'headerBg',
					'type'  => 'color',
					'label' => __( 'Header background', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-dt-header-bg' ),
				),
				array(
					'key'   => 'headerColor',
					'type'  => 'color',
					'label' => __( 'Header text', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-dt-header-color' ),
				),
				array(
					'key'   => 'stripeColor',
					'type'  => 'color',
					'label' => __( 'Stripe colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-dt-stripe' ),
				),
				array(
					'key'   => 'accentColor',
					'type'  => 'color',
					'label' => __( 'Accent (sort & focus)', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-dt-accent' ),
				),
			),
		),
	),
);
