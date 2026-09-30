<?php
/**
 * Google Map block — control schema.
 *
 * The map uses Google's keyless embed (no API key to configure or expose). The
 * location and behaviour are set here; the embed URL is built in render.php.
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
			'title'    => __( 'Location', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'query',
					'type'  => 'text',
					'label' => __( 'Address or place', 'etu-blocks' ),
					'help'  => __( 'An address, place name, or "lat,lng".', 'etu-blocks' ),
				),
				array(
					'key'   => 'zoom',
					'type'  => 'range',
					'label' => __( 'Zoom', 'etu-blocks' ),
					'min'   => 1,
					'max'   => 20,
					'step'  => 1,
				),
				array(
					'key'     => 'mapType',
					'type'    => 'select',
					'label'   => __( 'Map type', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'm', 'label' => __( 'Road', 'etu-blocks' ) ),
						array( 'value' => 'k', 'label' => __( 'Satellite', 'etu-blocks' ) ),
						array( 'value' => 'h', 'label' => __( 'Hybrid', 'etu-blocks' ) ),
						array( 'value' => 'p', 'label' => __( 'Terrain', 'etu-blocks' ) ),
					),
				),
				array(
					'key'     => 'aspectRatio',
					'type'    => 'select',
					'label'   => __( 'Aspect ratio', 'etu-blocks' ),
					'options' => array(
						array( 'value' => '16x9', 'label' => __( 'Wide (16:9)', 'etu-blocks' ) ),
						array( 'value' => '4x3', 'label' => __( 'Classic (4:3)', 'etu-blocks' ) ),
						array( 'value' => '21x9', 'label' => __( 'Cinematic (21:9)', 'etu-blocks' ) ),
						array( 'value' => '1x1', 'label' => __( 'Square (1:1)', 'etu-blocks' ) ),
						array( 'value' => 'tall', 'label' => __( 'Tall (3:4)', 'etu-blocks' ) ),
					),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Info card', 'etu-blocks' ),
			'controls' => array(
				array( 'key' => 'showInfoCard', 'type' => 'toggle', 'label' => __( 'Show info card', 'etu-blocks' ) ),
				array( 'key' => 'title', 'type' => 'text', 'label' => __( 'Title', 'etu-blocks' ) ),
				array( 'key' => 'address', 'type' => 'text', 'label' => __( 'Address line', 'etu-blocks' ) ),
				array( 'key' => 'showDirections', 'type' => 'toggle', 'label' => __( 'Directions button', 'etu-blocks' ) ),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Display', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'clickToLoad',
					'type'  => 'toggle',
					'label' => __( 'Click to load (privacy)', 'etu-blocks' ),
					'help'  => __( 'Load Google Maps only after the visitor clicks — no third-party request on page view.', 'etu-blocks' ),
				),
				array( 'key' => 'grayscale', 'type' => 'toggle', 'label' => __( 'Grayscale (colour on hover)', 'etu-blocks' ) ),
				array(
					'key'   => 'rounded',
					'type'  => 'range',
					'label' => __( 'Corner radius', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 0,
					'max'   => 48,
					'css'   => array( 'selector' => '.etu-map__frame', 'property' => 'border-radius' ),
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
					'label' => __( 'Accent (pin & button)', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-map-accent' ),
				),
			),
		),
	),
);
