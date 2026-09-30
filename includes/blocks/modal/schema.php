<?php
/**
 * Modal / Popup block — control schema.
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
			'title'    => __( 'Opening', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'trigger',
					'type'    => 'segmented',
					'label'   => __( 'Open with', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'button', 'label' => __( 'Button', 'etu-blocks' ) ),
						array( 'value' => 'load', 'label' => __( 'Page load', 'etu-blocks' ) ),
						array( 'value' => 'delay', 'label' => __( 'Delay', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'delaySeconds',
					'type'  => 'range',
					'label' => __( 'Delay (seconds)', 'etu-blocks' ),
					'min'   => 1,
					'max'   => 30,
					'step'  => 1,
					'help'  => __( 'Used when "Open with" is Delay.', 'etu-blocks' ),
				),
				array(
					'key'   => 'buttonAlign',
					'type'  => 'alignment',
					'label' => __( 'Button alignment', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-modal__trigger-wrap', 'property' => 'justify-content' ),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Dialog', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'size',
					'type'    => 'segmented',
					'label'   => __( 'Size', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'small', 'label' => __( 'Small', 'etu-blocks' ) ),
						array( 'value' => 'medium', 'label' => __( 'Medium', 'etu-blocks' ) ),
						array( 'value' => 'large', 'label' => __( 'Large', 'etu-blocks' ) ),
					),
				),
				array(
					'key'     => 'position',
					'type'    => 'segmented',
					'label'   => __( 'Position', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'center', 'label' => __( 'Center', 'etu-blocks' ) ),
						array( 'value' => 'top', 'label' => __( 'Top', 'etu-blocks' ) ),
					),
				),
				array(
					'key'     => 'animation',
					'type'    => 'segmented',
					'label'   => __( 'Animation', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'fade', 'label' => __( 'Fade', 'etu-blocks' ) ),
						array( 'value' => 'zoom', 'label' => __( 'Zoom', 'etu-blocks' ) ),
						array( 'value' => 'slide', 'label' => __( 'Slide', 'etu-blocks' ) ),
					),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Overlay', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'overlayColor',
					'type'  => 'color',
					'label' => __( 'Overlay colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-modal-overlay-color' ),
				),
				array(
					'key'   => 'overlayOpacity',
					'type'  => 'range',
					'label' => __( 'Overlay opacity', 'etu-blocks' ),
					'units' => array( '%' ),
					'min'   => 0,
					'max'   => 100,
					'css'   => array( 'selector' => '', 'property' => '--etu-modal-overlay-opacity' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Dialog & button', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'modalBackground',
					'type'  => 'color',
					'label' => __( 'Dialog background', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-modal-bg' ),
				),
				array(
					'key'   => 'modalRadius',
					'type'  => 'range',
					'label' => __( 'Corner radius', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 0,
					'max'   => 40,
					'css'   => array( 'selector' => '', 'property' => '--etu-modal-radius' ),
				),
				array(
					'key'   => 'modalPadding',
					'type'  => 'spacing',
					'label' => __( 'Dialog padding', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-modal__dialog', 'property' => 'padding' ),
				),
				array(
					'key'   => 'accentColor',
					'type'  => 'color',
					'label' => __( 'Button colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-modal-accent' ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Behaviour', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'dialogLabel',
					'type'  => 'text',
					'label' => __( 'Dialog label', 'etu-blocks' ),
					'help'  => __( 'Accessible name announced when the dialog opens.', 'etu-blocks' ),
				),
				array(
					'key'   => 'showClose',
					'type'  => 'toggle',
					'label' => __( 'Close button', 'etu-blocks' ),
				),
				array(
					'key'   => 'closeOnBackdrop',
					'type'  => 'toggle',
					'label' => __( 'Close on backdrop click', 'etu-blocks' ),
				),
				array(
					'key'   => 'showOnce',
					'type'  => 'toggle',
					'label' => __( 'Show only once', 'etu-blocks' ),
					'help'  => __( 'For Page load / Delay: remembers via a cookie so it shows once per visitor.', 'etu-blocks' ),
				),
			),
		),
	),
);
