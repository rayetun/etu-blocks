<?php
/**
 * Countdown block — control schema (style + display).
 *
 * The timer target (fixed date vs evergreen duration) and completion behaviour are
 * edited in a dedicated "Countdown" panel in the editor; this schema drives the
 * visual options and the generated CSS variables.
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
			'title'    => __( 'Display', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'preset',
					'type'    => 'segmented',
					'label'   => __( 'Style', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'cards', 'label' => __( 'Cards', 'etu-blocks' ) ),
						array( 'value' => 'minimal', 'label' => __( 'Minimal', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'contentAlign',
					'type'  => 'alignment',
					'label' => __( 'Alignment', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-countdown__timer', 'property' => 'justify-content' ),
				),
				array(
					'key'   => 'showLabels',
					'type'  => 'toggle',
					'label' => __( 'Show unit labels', 'etu-blocks' ),
				),
				array(
					'key'   => 'hideSeconds',
					'type'  => 'toggle',
					'label' => __( 'Hide seconds', 'etu-blocks' ),
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
			'title'    => __( 'Colours', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'accentColor',
					'type'  => 'color',
					'label' => __( 'Accent / card colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-cd-accent' ),
				),
				array(
					'key'   => 'numberColor',
					'type'  => 'color',
					'label' => __( 'Number colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-cd-number' ),
				),
				array(
					'key'   => 'labelColor',
					'type'  => 'color',
					'label' => __( 'Label colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-cd-label' ),
				),
			),
		),
	),
);
