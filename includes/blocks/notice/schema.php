<?php
/**
 * Notice block — control schema.
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
			'title'    => __( 'Type', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'preset',
					'type'    => 'preset',
					'label'   => __( 'Type', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'info', 'label' => __( 'Info', 'etu-blocks' ) ),
						array( 'value' => 'success', 'label' => __( 'Success', 'etu-blocks' ) ),
						array( 'value' => 'warning', 'label' => __( 'Warning', 'etu-blocks' ) ),
						array( 'value' => 'danger', 'label' => __( 'Danger', 'etu-blocks' ) ),
						array( 'value' => 'neutral', 'label' => __( 'Neutral', 'etu-blocks' ) ),
					),
				),
				array(
					'key'     => 'display',
					'type'    => 'segmented',
					'label'   => __( 'Display', 'etu-blocks' ),
					'help'    => __( 'Top/Bottom pin the notice to the screen on the front end.', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'inline', 'label' => __( 'Inline', 'etu-blocks' ) ),
						array( 'value' => 'top', 'label' => __( 'Top bar', 'etu-blocks' ) ),
						array( 'value' => 'bottom', 'label' => __( 'Bottom bar', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'noticeAlign',
					'type'  => 'alignment',
					'label' => __( 'Content alignment', 'etu-blocks' ),
					// Class-driven (etu-notice--align-*) so the icon + text move as
					// one group; see the block's style.scss.
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
					'label' => __( 'Accent (icon/border)', 'etu-blocks' ),
					'help'  => __( 'Overrides the type colour.', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-notice-accent' ),
				),
				array(
					'key'   => 'noticeBackground',
					'type'  => 'color',
					'label' => __( 'Background colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => 'background-color' ),
				),
				array(
					'key'   => 'noticeTextColor',
					'type'  => 'color',
					'label' => __( 'Text colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => 'color' ),
				),
				array(
					'key'   => 'noticeShadowColor',
					'type'  => 'color',
					'label' => __( 'Shadow colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-notice-shadow' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Shape & type', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'noticeRadius',
					'type'  => 'range',
					'label' => __( 'Corner radius', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 0,
					'max'   => 40,
					'css'   => array( 'selector' => '', 'property' => 'border-radius' ),
				),
				array(
					'key'   => 'titleTypography',
					'type'  => 'typography',
					'label' => __( 'Title', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-notice__title' ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Behaviour', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'icon',
					'type'  => 'text',
					'label' => __( 'Custom icon (emoji)', 'etu-blocks' ),
				),
				array(
					'key'   => 'dismissible',
					'type'  => 'toggle',
					'label' => __( 'Dismissible', 'etu-blocks' ),
				),
				array(
					'key'   => 'rememberDismiss',
					'type'  => 'toggle',
					'label' => __( 'Remember dismissal', 'etu-blocks' ),
					'help'  => __( 'Keeps it hidden ~1 year (cookie) after the visitor closes it.', 'etu-blocks' ),
				),
			),
		),
	),
);
