<?php
/**
 * Star Rating block — control schema.
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
					'label'   => __( 'Icon', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'star', 'label' => __( 'Star', 'etu-blocks' ) ),
						array( 'value' => 'heart', 'label' => __( 'Heart', 'etu-blocks' ) ),
						array( 'value' => 'circle', 'label' => __( 'Circle', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'size',
					'type'  => 'range',
					'label' => __( 'Icon size', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 12,
					'max'   => 48,
					'css'   => array( 'selector' => '.etu-star-rating__stars', 'property' => 'font-size' ),
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
					'label' => __( 'Filled colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-star-accent' ),
				),
				array(
					'key'   => 'emptyColor',
					'type'  => 'color',
					'label' => __( 'Empty colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-star-rating__empty', 'property' => 'color' ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Rating', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'rating',
					'type'  => 'range',
					'label' => __( 'Rating', 'etu-blocks' ),
					'min'   => 0,
					'max'   => 5,
					'step'  => 0.5,
				),
				array(
					'key'   => 'maxStars',
					'type'  => 'range',
					'label' => __( 'Out of', 'etu-blocks' ),
					'min'   => 1,
					'max'   => 10,
				),
				array(
					'key'   => 'showValue',
					'type'  => 'toggle',
					'label' => __( 'Show value', 'etu-blocks' ),
				),
				array(
					'key'   => 'label',
					'type'  => 'text',
					'label' => __( 'Label', 'etu-blocks' ),
				),
			),
		),
	),
);
