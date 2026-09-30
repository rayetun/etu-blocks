<?php
/**
 * Info Box block — control schema.
 *
 * Single source of truth for the Info Box Inspector UI and its generated CSS.
 * See references/editor-architecture.md.
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
						array( 'value' => 'bordered', 'label' => __( 'Bordered', 'etu-blocks' ) ),
					),
				),
				array(
					'key'     => 'iconPosition',
					'type'    => 'segmented',
					'label'   => __( 'Icon position', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'stacked', 'label' => __( 'Top', 'etu-blocks' ) ),
						array( 'value' => 'inline', 'label' => __( 'Left', 'etu-blocks' ) ),
					),
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
					'css'   => array( 'selector' => '', 'property' => '--etu-info-accent' ),
				),
				array(
					'key'   => 'iconColor',
					'type'  => 'color',
					'label' => __( 'Icon colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-info-box__icon', 'property' => 'color' ),
				),
				array(
					'key'   => 'titleColor',
					'type'  => 'color',
					'label' => __( 'Title colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-info-box__title', 'property' => 'color' ),
				),
				array(
					'key'   => 'descriptionColor',
					'type'  => 'color',
					'label' => __( 'Description colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-info-box__description', 'property' => 'color' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Typography', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'titleTypography',
					'type'  => 'typography',
					'label' => __( 'Title', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-info-box__title' ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Content', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'headingLevel',
					'type'    => 'select',
					'label'   => __( 'Heading level', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'h2', 'label' => 'H2' ),
						array( 'value' => 'h3', 'label' => 'H3' ),
						array( 'value' => 'h4', 'label' => 'H4' ),
						array( 'value' => 'h5', 'label' => 'H5' ),
						array( 'value' => 'h6', 'label' => 'H6' ),
						array( 'value' => 'p', 'label' => __( 'Paragraph', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'icon',
					'type'  => 'text',
					'label' => __( 'Icon (emoji or character)', 'etu-blocks' ),
				),
				array(
					'key'   => 'buttonUrl',
					'type'  => 'url',
					'label' => __( 'Button URL', 'etu-blocks' ),
				),
				array(
					'key'   => 'buttonNewTab',
					'type'  => 'toggle',
					'label' => __( 'Open button in a new tab', 'etu-blocks' ),
				),
			),
		),
	),
);
