<?php
/**
 * Banner (Hero) block — control schema (content is edited inline).
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
			'title'    => __( 'Layout', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'heightPreset',
					'type'    => 'segmented',
					'label'   => __( 'Height', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'small', 'label' => __( 'S', 'etu-blocks' ) ),
						array( 'value' => 'medium', 'label' => __( 'M', 'etu-blocks' ) ),
						array( 'value' => 'large', 'label' => __( 'L', 'etu-blocks' ) ),
						array( 'value' => 'full', 'label' => __( 'Full', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'contentAlign',
					'type'  => 'alignment',
					'label' => __( 'Content alignment', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-banner__content', 'property' => 'text-align' ),
				),
				array(
					'key'     => 'verticalAlign',
					'type'    => 'segmented',
					'label'   => __( 'Vertical position', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'top', 'label' => __( 'Top', 'etu-blocks' ) ),
						array( 'value' => 'center', 'label' => __( 'Center', 'etu-blocks' ) ),
						array( 'value' => 'bottom', 'label' => __( 'Bottom', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'contentWidth',
					'type'  => 'range',
					'label' => __( 'Content width', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 320,
					'max'   => 1100,
					'css'   => array( 'selector' => '.etu-banner__content', 'property' => 'max-width' ),
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
			'title'    => __( 'Background', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'bgImage',
					'type'  => 'media',
					'label' => __( 'Background image', 'etu-blocks' ),
				),
				array(
					'key'   => 'bgColor',
					'type'  => 'color',
					'label' => __( 'Background colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => 'background-color' ),
				),
				array(
					'key'   => 'overlayColor',
					'type'  => 'color',
					'label' => __( 'Overlay colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-banner-overlay-color' ),
				),
				array(
					'key'   => 'overlayOpacity',
					'type'  => 'range',
					'label' => __( 'Overlay opacity', 'etu-blocks' ),
					'units' => array( '%' ),
					'min'   => 0,
					'max'   => 100,
					'css'   => array( 'selector' => '', 'property' => '--etu-banner-overlay-opacity' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Text & buttons', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'textColor',
					'type'  => 'color',
					'label' => __( 'Text colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-banner__content', 'property' => 'color' ),
				),
				array(
					'key'   => 'accentColor',
					'type'  => 'color',
					'label' => __( 'Button colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-banner-accent' ),
				),
				array(
					'key'   => 'headingTypography',
					'type'  => 'typography',
					'label' => __( 'Heading', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-banner__heading' ),
				),
			),
		),
	),
);
