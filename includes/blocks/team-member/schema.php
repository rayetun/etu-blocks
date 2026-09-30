<?php
/**
 * Team Member block — control schema.
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
						array( 'value' => 'card', 'label' => __( 'Card', 'etu-blocks' ) ),
						array( 'value' => 'overlay', 'label' => __( 'Overlay', 'etu-blocks' ) ),
						array( 'value' => 'horizontal', 'label' => __( 'Horizontal', 'etu-blocks' ) ),
						array( 'value' => 'minimal', 'label' => __( 'Minimal', 'etu-blocks' ) ),
					),
				),
				array(
					'key'   => 'contentAlign',
					'type'  => 'alignment',
					'label' => __( 'Alignment', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => 'text-align' ),
				),
				array(
					'key'   => 'avatarRadius',
					'type'  => 'range',
					'label' => __( 'Photo corner radius', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 0,
					'max'   => 120,
					'css'   => array( 'selector' => '.etu-team-member__avatar', 'property' => 'border-radius' ),
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
					'css'   => array( 'selector' => '', 'property' => '--etu-team-accent' ),
				),
				array(
					'key'   => 'nameColor',
					'type'  => 'color',
					'label' => __( 'Name colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-team-member__name', 'property' => 'color' ),
				),
				array(
					'key'   => 'roleColor',
					'type'  => 'color',
					'label' => __( 'Role colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-team-member__role', 'property' => 'color' ),
				),
				array(
					'key'   => 'bioColor',
					'type'  => 'color',
					'label' => __( 'Bio colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-team-member__bio', 'property' => 'color' ),
				),
				array(
					'key'   => 'socialColor',
					'type'  => 'color',
					'label' => __( 'Social icon colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-team-member__social', 'property' => 'color' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Overlay (Overlay preset)', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'overlayColor',
					'type'  => 'color',
					'label' => __( 'Overlay colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-team-overlay-color' ),
				),
				array(
					'key'   => 'overlayOpacity',
					'type'  => 'range',
					'label' => __( 'Overlay opacity', 'etu-blocks' ),
					'units' => array( '%' ),
					'min'   => 0,
					'max'   => 100,
					'css'   => array( 'selector' => '', 'property' => '--etu-team-overlay-opacity' ),
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
					'label' => __( 'Name', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-team-member__name' ),
				),
				array(
					'key'   => 'roleTypography',
					'type'  => 'typography',
					'label' => __( 'Role', 'etu-blocks' ),
					'css'   => array( 'selector' => '.etu-team-member__role' ),
				),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Social links', 'etu-blocks' ),
			'controls' => array(
				array( 'key' => 'socialTwitter', 'type' => 'url', 'label' => __( 'X / Twitter URL', 'etu-blocks' ) ),
				array( 'key' => 'socialLinkedin', 'type' => 'url', 'label' => __( 'LinkedIn URL', 'etu-blocks' ) ),
				array( 'key' => 'socialGithub', 'type' => 'url', 'label' => __( 'GitHub URL', 'etu-blocks' ) ),
				array( 'key' => 'socialWebsite', 'type' => 'url', 'label' => __( 'Website URL', 'etu-blocks' ) ),
				array( 'key' => 'socialEmail', 'type' => 'text', 'label' => __( 'Email address', 'etu-blocks' ) ),
			),
		),
	),
);
