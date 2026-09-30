<?php
/**
 * Counter block — control schema.
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
						array( 'value' => 'minimal', 'label' => __( 'Minimal', 'etu-blocks' ) ),
						array( 'value' => 'card', 'label' => __( 'Card', 'etu-blocks' ) ),
						array( 'value' => 'boxed', 'label' => __( 'Boxed', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'contentAlign',
					'type'  => 'alignment',
					'label' => __( 'Alignment', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => 'text-align' ),
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
					'label' => __( 'Accent colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-counter-accent' ),
				),
				array(
					'key'   => 'numberColor',
					'type'  => 'color',
					'label' => __( 'Number colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-counter__number', 'property' => 'color' ),
				),
				array(
					'key'   => 'labelColor',
					'type'  => 'color',
					'label' => __( 'Label colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-counter__label', 'property' => 'color' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Typography', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'numberTypography',
					'type'  => 'typography',
					'label' => __( 'Number', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-counter__number' ),
				),
				array(
					'key'   => 'labelTypography',
					'type'  => 'typography',
					'label' => __( 'Label', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-counter__label' ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Content', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'number',
					'type'  => 'text',
					'label' => __( 'Number', 'etu-blocks' ),
				),
				array(
					'key'   => 'prefix',
					'type'  => 'text',
					'label' => __( 'Prefix', 'etu-blocks' ),
				),
				array(
					'key'   => 'suffix',
					'type'  => 'text',
					'label' => __( 'Suffix', 'etu-blocks' ),
				),
				array(
					'key'   => 'separator',
					'type'  => 'toggle',
					'label' => __( 'Thousands separator', 'etu-blocks' ),
				),
				array(
					'key'   => 'duration',
					'type'  => 'range',
					'label' => __( 'Animation duration (ms)', 'etu-blocks' ),
					'min'   => 200,
					'max'   => 6000,
				),
			),
		),
	),
);
