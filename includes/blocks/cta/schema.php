<?php
/**
 * CTA block — control schema.
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
						array( 'value' => 'centered', 'label' => __( 'Centered', 'etu-blocks' ) ),
						array( 'value' => 'split', 'label' => __( 'Split', 'etu-blocks' ) ),
						array( 'value' => 'banner', 'label' => __( 'Banner', 'etu-blocks' ) ),
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
					'css'   => array( 'selector' => '', 'property' => '--etu-cta-accent' ),
				),
				array(
					'key'   => 'headingColor',
					'type'  => 'color',
					'label' => __( 'Heading colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-cta__heading', 'property' => 'color' ),
				),
				array(
					'key'   => 'descriptionColor',
					'type'  => 'color',
					'label' => __( 'Description colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-cta__description', 'property' => 'color' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Typography', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'headingTypography',
					'type'  => 'typography',
					'label' => __( 'Heading', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-cta__heading' ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Buttons', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'primaryUrl',
					'type'  => 'url',
					'label' => __( 'Primary button URL', 'etu-blocks' ),
				),
				array(
					'key'   => 'secondaryUrl',
					'type'  => 'url',
					'label' => __( 'Secondary button URL', 'etu-blocks' ),
				),
				array(
					'key'   => 'buttonNewTab',
					'type'  => 'toggle',
					'label' => __( 'Open buttons in a new tab', 'etu-blocks' ),
				),
			),
		),
	),
);
