<?php
/**
 * Pricing Table block — control schema (design only; plans are edited inline).
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
					'key'     => 'preset',
					'type'    => 'preset',
					'label'   => __( 'Card style', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'modern', 'label' => __( 'Modern', 'etu-blocks' ) ),
						array( 'value' => 'outline', 'label' => __( 'Outline', 'etu-blocks' ) ),
						array( 'value' => 'minimal', 'label' => __( 'Minimal', 'etu-blocks' ) ),
					),
				),
				array(
					'key'     => 'columns',
					'type'    => 'segmented',
					'label'   => __( 'Columns', 'etu-blocks' ),
					'options' => array(
						array( 'value' => '2', 'label' => '2' ),
						array( 'value' => '3', 'label' => '3' ),
						array( 'value' => '4', 'label' => '4' ),
					),
				),
				array(
					'key'   => 'cardGap',
					'type'  => 'range',
					'label' => __( 'Gap', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 0,
					'max'   => 60,
					'css'   => array( 'selector' => '.etu-pricing__grid', 'property' => 'gap' ),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Billing toggle', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'enableBilling',
					'type'  => 'toggle',
					'label' => __( 'Monthly / yearly toggle', 'etu-blocks' ),
					'help'  => __( 'Visitors switch between the monthly and yearly prices.', 'etu-blocks' ),
				),
				array(
					'key'   => 'yearlyBadge',
					'type'  => 'text',
					'label' => __( 'Yearly badge', 'etu-blocks' ),
					'help'  => __( 'Optional, e.g. "Save 20%".', 'etu-blocks' ),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Spacing', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'cardPadding',
					'type'  => 'spacing',
					'label' => __( 'Card padding', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-pricing__card', 'property' => 'padding' ),
				),
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
					'label' => __( 'Accent (featured & buttons)', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-pricing-accent' ),
				),
				array(
					'key'   => 'cardBackground',
					'type'  => 'color',
					'label' => __( 'Card background', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-pricing-card-bg' ),
				),
				array(
					'key'   => 'headingColor',
					'type'  => 'color',
					'label' => __( 'Plan name colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-pricing__name', 'property' => 'color' ),
				),
				array(
					'key'   => 'priceColor',
					'type'  => 'color',
					'label' => __( 'Price colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-pricing__price', 'property' => 'color' ),
				),
				array(
					'key'   => 'textColor',
					'type'  => 'color',
					'label' => __( 'Text colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-pricing__card', 'property' => 'color' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Typography', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'nameTypography',
					'type'  => 'typography',
					'label' => __( 'Plan name', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-pricing__name' ),
				),
				array(
					'key'   => 'priceTypography',
					'type'  => 'typography',
					'label' => __( 'Price', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-pricing__amount' ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Billing labels', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'monthlyLabel',
					'type'  => 'text',
					'label' => __( 'Monthly label', 'etu-blocks' ),
				),
				array(
					'key'   => 'yearlyLabel',
					'type'  => 'text',
					'label' => __( 'Yearly label', 'etu-blocks' ),
				),
			),
		),
	),
);
