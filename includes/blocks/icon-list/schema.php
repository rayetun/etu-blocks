<?php
/**
 * Icon List block — control schema.
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
						array( 'value' => 'checklist', 'label' => __( 'Checklist', 'etu-blocks' ) ),
						array( 'value' => 'plain', 'label' => __( 'Plain', 'etu-blocks' ) ),
						array( 'value' => 'boxed', 'label' => __( 'Boxed', 'etu-blocks' ) ),
					),
				),
				array(
					'key'     => 'columns',
					'type'    => 'segmented',
					'label'   => __( 'Columns', 'etu-blocks' ),
					'options' => array(
						array( 'value' => '1', 'label' => '1' ),
						array( 'value' => '2', 'label' => '2' ),
						array( 'value' => '3', 'label' => '3' ),
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
					'css'   => array( 'selector' => '', 'property' => '--etu-icon-list-accent' ),
				),
				array(
					'key'   => 'iconColor',
					'type'  => 'color',
					'label' => __( 'Icon colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-icon-list__icon', 'property' => 'color' ),
				),
				array(
					'key'   => 'textColor',
					'type'  => 'color',
					'label' => __( 'Text colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-icon-list__text', 'property' => 'color' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Typography', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'textTypography',
					'type'  => 'typography',
					'label' => __( 'Text', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-icon-list__text' ),
				),
			),
		),
	),
);
