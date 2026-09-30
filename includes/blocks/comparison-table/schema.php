<?php
/**
 * Comparison Table block — control schema (design only; content is edited inline).
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
					'label'   => __( 'Style', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'modern', 'label' => __( 'Modern', 'etu-blocks' ) ),
						array( 'value' => 'bordered', 'label' => __( 'Bordered', 'etu-blocks' ) ),
						array( 'value' => 'minimal', 'label' => __( 'Minimal', 'etu-blocks' ) ),
					),
				),
				array(
					'key'     => 'cellAlign',
					'type'    => 'segmented',
					'label'   => __( 'Cell alignment', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'left', 'label' => __( 'Left', 'etu-blocks' ) ),
						array( 'value' => 'center', 'label' => __( 'Center', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'stripe',
					'type'  => 'toggle',
					'label' => __( 'Striped rows', 'etu-blocks' ),
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
					'label' => __( 'Accent (highlight & checks)', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-ct-accent' ),
				),
				array(
					'key'   => 'headingColor',
					'type'  => 'color',
					'label' => __( 'Heading colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-ct__col-title', 'property' => 'color' ),
				),
				array(
					'key'   => 'textColor',
					'type'  => 'color',
					'label' => __( 'Text colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-ct__table', 'property' => 'color' ),
				),
				array(
					'key'   => 'stripeColor',
					'type'  => 'color',
					'label' => __( 'Striped row colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-ct-stripe' ),
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
					'label' => __( 'Headings', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-ct__col-title' ),
				),
				array(
					'key'   => 'cellTypography',
					'type'  => 'typography',
					'label' => __( 'Cells', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-ct__cell' ),
				),
			),
		),
	),
);
