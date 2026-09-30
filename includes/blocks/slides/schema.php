<?php
/**
 * Slides / Carousel block — control schema.
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
					'key'        => 'perView',
					'type'       => 'range',
					'label'      => __( 'Slides per view', 'etu-blocks' ),
					'responsive' => true,
					'min'        => 1,
					'max'        => 6,
					'step'       => 1,
					// No `css`: rendered as a responsive CSS var by render.php.
				),
				array(
					'key'   => 'gap',
					'type'  => 'range',
					'label' => __( 'Gap', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 0,
					'max'   => 60,
					'css'   => array( 'selector' => '', 'property' => '--etu-slides-gap' ),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Navigation', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'showArrows',
					'type'  => 'toggle',
					'label' => __( 'Arrows', 'etu-blocks' ),
				),
				array(
					'key'     => 'arrowPosition',
					'type'    => 'segmented',
					'label'   => __( 'Arrow position', 'etu-blocks' ),
					'help'    => __( 'Over edges suits full-width heroes; Outside needs room beside the carousel; Bottom never covers content.', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'edge', 'label' => __( 'Over edges', 'etu-blocks' ) ),
						array( 'value' => 'outside', 'label' => __( 'Outside', 'etu-blocks' ) ),
						array( 'value' => 'bottom', 'label' => __( 'Bottom', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'showDots',
					'type'  => 'toggle',
					'label' => __( 'Dots', 'etu-blocks' ),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Autoplay', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'autoplay',
					'type'  => 'toggle',
					'label' => __( 'Autoplay', 'etu-blocks' ),
					'help'  => __( 'Never runs when the visitor prefers reduced motion.', 'etu-blocks' ),
				),
				array(
					'key'   => 'autoplaySpeed',
					'type'  => 'range',
					'label' => __( 'Autoplay delay (seconds)', 'etu-blocks' ),
					'min'   => 1,
					'max'   => 15,
					'step'  => 1,
				),
				array(
					'key'   => 'pauseOnHover',
					'type'  => 'toggle',
					'label' => __( 'Pause on hover / focus', 'etu-blocks' ),
				),
				array(
					'key'   => 'loop',
					'type'  => 'toggle',
					'label' => __( 'Loop', 'etu-blocks' ),
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
					'label' => __( 'Controls colour', 'etu-blocks' ),
					'help'  => __( 'Arrows and the active dot.', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-slides-accent' ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Accessibility', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'ariaLabel',
					'type'  => 'text',
					'label' => __( 'Carousel label', 'etu-blocks' ),
					'help'  => __( 'Describes the carousel for screen readers, e.g. "Customer stories".', 'etu-blocks' ),
				),
			),
		),
	),
);
