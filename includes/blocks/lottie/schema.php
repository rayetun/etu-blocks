<?php
/**
 * Lottie block — control schema.
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
			'title'    => __( 'Animation', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'trigger',
					'type'    => 'select',
					'label'   => __( 'Play', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'autoplay', 'label' => __( 'On load', 'etu-blocks' ) ),
						array( 'value' => 'viewport', 'label' => __( 'When scrolled into view', 'etu-blocks' ) ),
						array( 'value' => 'hover', 'label' => __( 'On hover / focus', 'etu-blocks' ) ),
						array( 'value' => 'click', 'label' => __( 'On click', 'etu-blocks' ) ),
					),
				),
				array( 'key' => 'loop', 'type' => 'toggle', 'label' => __( 'Loop', 'etu-blocks' ) ),
				array(
					'key'   => 'speedPct',
					'type'  => 'range',
					'label' => __( 'Speed (%)', 'etu-blocks' ),
					'min'   => 25,
					'max'   => 300,
					'step'  => 5,
				),
				array(
					'key'   => 'maxWidth',
					'type'  => 'range',
					'label' => __( 'Max width', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 80,
					'max'   => 1000,
					'css'   => array( 'selector' => '.etu-lottie__canvas', 'property' => 'max-width' ),
				),
				array(
					'key'   => 'align',
					'type'  => 'alignment',
					'label' => __( 'Alignment', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => 'justify-content' ),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Accessibility', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'decorative',
					'type'  => 'toggle',
					'label' => __( 'Decorative', 'etu-blocks' ),
					'help'  => __( 'Hide from screen readers. Turn off if the animation conveys meaning.', 'etu-blocks' ),
				),
				array(
					'key'      => 'title',
					'type'     => 'text',
					'label'    => __( 'Text alternative', 'etu-blocks' ),
					'help'     => __( 'Describes the animation for assistive tech.', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'decorative', 'value' => false ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Spacing', 'etu-blocks' ),
			'controls' => array(
				array( 'key' => 'blockPadding', 'type' => 'spacing', 'label' => __( 'Padding', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => 'padding' ) ),
				array( 'key' => 'blockMargin', 'type' => 'spacing', 'label' => __( 'Margin', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => 'margin' ) ),
			),
		),
	),
);
