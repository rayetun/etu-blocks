<?php
/**
 * Tabs block — control schema.
 *
 * Single source of truth for the Tabs Inspector UI and its generated CSS. Each
 * control's `key` matches a block.json attribute; a `css` descriptor (where
 * present) tells the CSS generator how the value becomes a scoped rule. The
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
						array( 'value' => 'line', 'label' => __( 'Line', 'etu-blocks' ) ),
						array( 'value' => 'pill', 'label' => __( 'Pill', 'etu-blocks' ) ),
						array( 'value' => 'card', 'label' => __( 'Card', 'etu-blocks' ) ),
					),
					// No `css`: this maps to a design class in render.php.
				),
				array(
					'key'   => 'tabAlign',
					'type'  => 'alignment',
					'label' => __( 'Alignment', 'etu-blocks' ),
					'css'   => array(
						'selector' => '.etu-tabs__list',
						'property' => 'justify-content',
					),
				),
				array(
					'key'        => 'tabGap',
					'type'       => 'range',
					'label'      => __( 'Gap between tabs', 'etu-blocks' ),
					'responsive' => true,
					'units'      => array( 'px', 'em', 'rem' ),
					'min'        => 0,
					'max'        => 80,
					'css'        => array(
						'selector' => '.etu-tabs__list',
						'property' => 'gap',
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
					'css'   => array(
						'selector' => '',
						'property' => '--etu-tabs-accent',
					),
				),
				array(
					'key'   => 'tabTextColor',
					'type'  => 'color',
					'label' => __( 'Text colour', 'etu-blocks' ),
					'css'   => array(
						'selector' => '.etu-tabs__tab',
						'property' => 'color',
					),
				),
				array(
					'key'   => 'tabTextColorHover',
					'type'  => 'color',
					'label' => __( 'Text colour (hover)', 'etu-blocks' ),
					'css'   => array(
						'selector' => '.etu-tabs__tab:hover',
						'property' => 'color',
					),
				),
				array(
					'key'   => 'tabActiveColor',
					'type'  => 'color',
					'label' => __( 'Active tab colour', 'etu-blocks' ),
					'css'   => array(
						'selector' => '.etu-tabs__tab[aria-selected="true"]',
						'property' => 'color',
					),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Typography & spacing', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'tabTypography',
					'type'  => 'typography',
					'label' => __( 'Tab label', 'etu-blocks' ),
					'css'   => array(
						'selector' => '.etu-tabs__tab',
					),
				),
				array(
					'key'   => 'tabPadding',
					'type'  => 'spacing',
					'label' => __( 'Tab padding', 'etu-blocks' ),
					'css'   => array(
						'selector' => '.etu-tabs__tab',
						'property' => 'padding',
					),
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
					'label' => __( 'Accessible label (aria-label)', 'etu-blocks' ),
				),
			),
		),
	),
);
