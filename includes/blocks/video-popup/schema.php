<?php
/**
 * Video Popup block — control schema.
 *
 * The video source and poster are the content; the rest is layout and style. The
 * embed itself is built server-side in render.php (with a privacy-friendly no-cookie
 * host) and only loaded when the visitor clicks play.
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
			'title'    => __( 'Video', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'videoType',
					'type'    => 'select',
					'label'   => __( 'Source', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'youtube', 'label' => __( 'YouTube', 'etu-blocks' ) ),
						array( 'value' => 'vimeo', 'label' => __( 'Vimeo', 'etu-blocks' ) ),
						array( 'value' => 'self', 'label' => __( 'Self-hosted', 'etu-blocks' ) ),
					),
				),
				array(
					'key'      => 'videoUrl',
					'type'     => 'url',
					'label'    => __( 'Video URL', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'videoType', 'in' => array( 'youtube', 'vimeo' ) ),
				),
				array(
					'key'      => 'videoFile',
					'type'     => 'media',
					'label'    => __( 'Video file (MP4)', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'videoType', 'value' => 'self' ),
				),
				array(
					'key'   => 'autoplay',
					'type'  => 'toggle',
					'label' => __( 'Play automatically on open', 'etu-blocks' ),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Poster', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'posterImage',
					'type'  => 'media',
					'label' => __( 'Poster image', 'etu-blocks' ),
					'help'  => __( 'Optional. YouTube videos fall back to their thumbnail.', 'etu-blocks' ),
				),
				array(
					'key'   => 'title',
					'type'  => 'text',
					'label' => __( 'Title', 'etu-blocks' ),
					'help'  => __( 'Used as the play button and dialog label.', 'etu-blocks' ),
				),
				array(
					'key'   => 'showTitle',
					'type'  => 'toggle',
					'label' => __( 'Show title on the poster', 'etu-blocks' ),
				),
				array(
					'key'     => 'aspectRatio',
					'type'    => 'select',
					'label'   => __( 'Aspect ratio', 'etu-blocks' ),
					'options' => array(
						array( 'value' => '16x9', 'label' => __( 'Wide (16:9)', 'etu-blocks' ) ),
						array( 'value' => '4x3', 'label' => __( 'Classic (4:3)', 'etu-blocks' ) ),
						array( 'value' => '1x1', 'label' => __( 'Square (1:1)', 'etu-blocks' ) ),
						array( 'value' => '21x9', 'label' => __( 'Cinematic (21:9)', 'etu-blocks' ) ),
					),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Layout', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'   => 'rounded',
					'type'  => 'range',
					'label' => __( 'Corner radius', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 0,
					'max'   => 48,
					'css'   => array( 'selector' => '.etu-video-popup__poster', 'property' => 'border-radius' ),
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
					'label' => __( 'Play button colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-vp-accent' ),
				),
				array(
					'key'   => 'overlayColor',
					'type'  => 'color',
					'label' => __( 'Poster overlay colour', 'etu-blocks' ),
					'css'   => array( 'selector' => '', 'property' => '--etu-vp-overlay-color' ),
				),
				array(
					'key'   => 'overlayOpacity',
					'type'  => 'range',
					'label' => __( 'Poster overlay opacity', 'etu-blocks' ),
					'units' => array( '%' ),
					'min'   => 0,
					'max'   => 100,
					'css'   => array( 'selector' => '', 'property' => '--etu-vp-overlay-opacity' ),
				),
			),
		),
	),
);
