<?php
/**
 * Testimonial block — control schema.
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
						array( 'value' => 'card', 'label' => __( 'Card', 'etu-blocks' ) ),
						array( 'value' => 'quote', 'label' => __( 'Quote', 'etu-blocks' ) ),
						array( 'value' => 'minimal', 'label' => __( 'Minimal', 'etu-blocks' ) ),
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
					'css'   => array( 'selector' => '', 'property' => '--etu-testimonial-accent' ),
				),
				array(
					'key'   => 'quoteColor',
					'type'  => 'color',
					'label' => __( 'Quote colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-testimonial__quote', 'property' => 'color' ),
				),
				array(
					'key'   => 'authorColor',
					'type'  => 'color',
					'label' => __( 'Author colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-testimonial__name', 'property' => 'color' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Typography', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'quoteTypography',
					'type'  => 'typography',
					'label' => __( 'Quote', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-testimonial__quote' ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Rating', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'showRating',
					'type'  => 'toggle',
					'label' => __( 'Show star rating', 'etu-blocks' ),
				),
				array(
					'key'   => 'rating',
					'type'  => 'range',
					'label' => __( 'Rating (0–5)', 'etu-blocks' ),
					'min'   => 0,
					'max'   => 5,
				),
			),
		),
	),
);
