<?php
/**
 * Progress Bar block — control schema.
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
			'title'    => __( 'Design', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'preset',
					'type'    => 'preset',
					'label'   => __( 'Preset', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'rounded', 'label' => __( 'Rounded', 'etu-blocks' ) ),
						array( 'value' => 'striped', 'label' => __( 'Striped', 'etu-blocks' ) ),
						array( 'value' => 'minimal', 'label' => __( 'Minimal', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'height',
					'type'  => 'range',
					'label' => __( 'Bar height', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 4,
					'max'   => 40,
					'css'   => array( 'selector' => '.etu-progress-bar__track', 'property' => 'height' ),
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
					'key'   => 'accentColor',
					'type'  => 'color',
					'label' => __( 'Bar colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-progress-accent' ),
				),
				array(
					'key'   => 'trackColor',
					'type'  => 'color',
					'label' => __( 'Track colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-progress-bar__track', 'property' => 'background-color' ),
				),
				array(
					'key'   => 'labelColor',
					'type'  => 'color',
					'label' => __( 'Label colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-progress-bar__label', 'property' => 'color' ),
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
					'css'   => array( 'selector' => '.etu-progress-bar__label' ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Value', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'value',
					'type'  => 'range',
					'label' => __( 'Value (%)', 'etu-blocks' ),
					'min'   => 0,
					'max'   => 100,
				),
				array(
					'key'   => 'showValue',
					'type'  => 'toggle',
					'label' => __( 'Show value', 'etu-blocks' ),
				),
			),
		),
	),
);
