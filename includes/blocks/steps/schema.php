<?php
/**
 * Steps block — control schema.
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
						array( 'value' => 'connected', 'label' => __( 'Connected', 'etu-blocks' ) ),
						array( 'value' => 'numbered', 'label' => __( 'Numbered', 'etu-blocks' ) ),
						array( 'value' => 'minimal', 'label' => __( 'Minimal', 'etu-blocks' ) ),
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
					'css'   => array( 'selector' => '', 'property' => '--etu-steps-accent' ),
				),
				array(
					'key'   => 'titleColor',
					'type'  => 'color',
					'label' => __( 'Title colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-steps__title', 'property' => 'color' ),
				),
				array(
					'key'   => 'descColor',
					'type'  => 'color',
					'label' => __( 'Description colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-steps__description', 'property' => 'color' ),
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
					'css'   => array( 'selector' => '.etu-steps__title' ),
				),
			),
		),
	),
);
