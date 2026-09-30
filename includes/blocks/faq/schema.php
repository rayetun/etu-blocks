<?php
/**
 * FAQ block — control schema.
 *
 * Single source of truth for the FAQ Inspector UI and its generated CSS. The
 * `preset` control swaps a premium design class (styled in style.scss). See
 * references/editor-architecture.md.
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
						array( 'value' => 'boxed', 'label' => __( 'Boxed', 'etu-blocks' ) ),
						array( 'value' => 'line', 'label' => __( 'Line', 'etu-blocks' ) ),
						array( 'value' => 'filled', 'label' => __( 'Filled', 'etu-blocks' ) ),
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
					'css'   => array( 'selector' => '', 'property' => '--etu-faq-accent' ),
				),
				array(
					'key'   => 'questionColor',
					'type'  => 'color',
					'label' => __( 'Question colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-faq__trigger', 'property' => 'color' ),
				),
				array(
					'key'   => 'answerColor',
					'type'  => 'color',
					'label' => __( 'Answer colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-faq__answer', 'property' => 'color' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Typography & spacing', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'questionTypography',
					'type'  => 'typography',
					'label' => __( 'Question', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-faq__trigger' ),
				),
				array(
					'key'   => 'itemPadding',
					'type'  => 'spacing',
					'label' => __( 'Question padding', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-faq__trigger', 'property' => 'padding' ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Behaviour', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'allowMultipleOpen',
					'type'  => 'toggle',
					'label' => __( 'Allow multiple open at once', 'etu-blocks' ),
				),
				array(
					'key'   => 'showSchema',
					'type'  => 'toggle',
					'label' => __( 'Output FAQ schema (SEO)', 'etu-blocks' ),
				),
			),
		),
	),
);
