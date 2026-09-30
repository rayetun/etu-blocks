<?php
/**
 * Before / After block — control schema.
 *
 * The two images are the block's content; everything else is layout and style.
 * The reveal position is stored as `startPosition` (no CSS — it seeds the
 * Interactivity context and the divider's initial inline position).
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
			'title'    => __( 'Images', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'beforeImage',
					'type'  => 'media',
					'label' => __( 'Before image', 'etu-blocks' ),
				),
				array(
					'key'   => 'afterImage',
					'type'  => 'media',
					'label' => __( 'After image', 'etu-blocks' ),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Layout', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'orientation',
					'type'    => 'segmented',
					'label'   => __( 'Slider direction', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'horizontal', 'label' => __( 'Horizontal', 'etu-blocks' ) ),
						array( 'value' => 'vertical', 'label' => __( 'Vertical', 'etu-blocks' ) ),
					),
				),
				array(
					'key'     => 'aspectRatio',
					'type'    => 'select',
					'label'   => __( 'Aspect ratio', 'etu-blocks' ),
					'options' => array(
						array( 'value' => '16x9', 'label' => __( 'Wide (16:9)', 'etu-blocks' ) ),
						array( 'value' => '3x2', 'label' => __( 'Photo (3:2)', 'etu-blocks' ) ),
						array( 'value' => '4x3', 'label' => __( 'Classic (4:3)', 'etu-blocks' ) ),
						array( 'value' => '1x1', 'label' => __( 'Square (1:1)', 'etu-blocks' ) ),
						array( 'value' => '21x9', 'label' => __( 'Cinematic (21:9)', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'startPosition',
					'type'  => 'range',
					'label' => __( 'Start position (%)', 'etu-blocks' ),
					'min'   => 0,
					'max'   => 100,
				),
				array(
					'key'   => 'rounded',
					'type'  => 'range',
					'label' => __( 'Corner radius', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 0,
					'max'   => 48,
					'css'   => array( 'selector' => '.etu-before-after__frame', 'property' => 'border-radius' ),
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
			'tab'      => 'layout',
			'title'    => __( 'Labels', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'showLabels',
					'type'  => 'toggle',
					'label' => __( 'Show labels', 'etu-blocks' ),
					'help'  => __( 'Overlay “Before” and “After” badges on the images.', 'etu-blocks' ),
				),
				array(
					'key'      => 'beforeLabel',
					'type'     => 'text',
					'label'    => __( 'Before label', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'showLabels', 'value' => true ),
				),
				array(
					'key'      => 'afterLabel',
					'type'     => 'text',
					'label'    => __( 'After label', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'showLabels', 'value' => true ),
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
					'label' => __( 'Divider & handle colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-ba-accent' ),
				),
			),
		),
	),
);
