<?php
/**
 * Post Filter block — control schema (posts + terms are queried at render/REST).
 *
 * @package Etublocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Included within Schemas::get()'s method scope; local, not global.
$etu_pf_post_types = array( array( 'value' => 'post', 'label' => __( 'Posts', 'etu-blocks' ) ) );
$etu_pf_taxes      = array( array( 'value' => 'category', 'label' => __( 'Categories', 'etu-blocks' ) ) );

if ( is_admin() ) {
	$etu_pf_post_types = array();
	foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $etu_pf_pt ) {
		if ( 'attachment' === $etu_pf_pt->name ) {
			continue;
		}
		$etu_pf_post_types[] = array( 'value' => $etu_pf_pt->name, 'label' => $etu_pf_pt->labels->name );
	}

	$etu_pf_taxes = array();
	foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $etu_pf_tax ) {
		if ( ! $etu_pf_tax->show_ui ) {
			continue;
		}
		$etu_pf_taxes[] = array( 'value' => $etu_pf_tax->name, 'label' => $etu_pf_tax->labels->name );
	}
}
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

return array(
	'sections' => array(
		array(
			'tab'      => 'layout',
			'title'    => __( 'Query', 'etu-blocks' ),
			'controls' => array(
				array( 'key' => 'postType', 'type' => 'select', 'label' => __( 'Post type', 'etu-blocks' ), 'options' => $etu_pf_post_types ),
				array(
					'key'     => 'perPage',
					'type'    => 'range',
					'label'   => __( 'Posts per page', 'etu-blocks' ),
					'min'     => 1,
					'max'     => 24,
					'step'    => 1,
				),
				array(
					'key'     => 'orderBy',
					'type'    => 'select',
					'label'   => __( 'Order by', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'date', 'label' => __( 'Date', 'etu-blocks' ) ),
						array( 'value' => 'title', 'label' => __( 'Title', 'etu-blocks' ) ),
						array( 'value' => 'menu_order', 'label' => __( 'Menu order', 'etu-blocks' ) ),
						array( 'value' => 'comment_count', 'label' => __( 'Comment count', 'etu-blocks' ) ),
						array( 'value' => 'rand', 'label' => __( 'Random', 'etu-blocks' ) ),
					),
				),
				array(
					'key'     => 'order',
					'type'    => 'segmented',
					'label'   => __( 'Order', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'desc', 'label' => __( 'Desc', 'etu-blocks' ) ),
						array( 'value' => 'asc', 'label' => __( 'Asc', 'etu-blocks' ) ),
					),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Filters', 'etu-blocks' ),
			'controls' => array(
				array( 'key' => 'enableSearch', 'type' => 'toggle', 'label' => __( 'Search field', 'etu-blocks' ) ),
				array(
					'key'     => 'filterTaxonomies',
					'type'    => 'checklist',
					'label'   => __( 'Taxonomy filters', 'etu-blocks' ),
					'help'    => __( 'Each shows as a dropdown. Only taxonomies on the chosen post type apply.', 'etu-blocks' ),
					'options' => $etu_pf_taxes,
				),
				array( 'key' => 'enableReset', 'type' => 'toggle', 'label' => __( 'Reset button', 'etu-blocks' ) ),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Cards', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'cardStyle',
					'type'    => 'preset',
					'label'   => __( 'Card style', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'card', 'label' => __( 'Card', 'etu-blocks' ) ),
						array( 'value' => 'overlay', 'label' => __( 'Overlay', 'etu-blocks' ) ),
					),
				),
				array(
					'key'      => 'columns',
					'type'     => 'segmented',
					'label'    => __( 'Columns', 'etu-blocks' ),
					'options'  => array(
						array( 'value' => '2', 'label' => '2' ),
						array( 'value' => '3', 'label' => '3' ),
						array( 'value' => '4', 'label' => '4' ),
					),
				),
				array(
					'key'     => 'hoverEffect',
					'type'    => 'segmented',
					'label'   => __( 'Hover effect', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'none', 'label' => __( 'None', 'etu-blocks' ) ),
						array( 'value' => 'lift', 'label' => __( 'Lift', 'etu-blocks' ) ),
						array( 'value' => 'zoom', 'label' => __( 'Zoom', 'etu-blocks' ) ),
						array( 'value' => 'both', 'label' => __( 'Both', 'etu-blocks' ) ),
					),
				),
				array( 'key' => 'showImage', 'type' => 'toggle', 'label' => __( 'Featured image', 'etu-blocks' ) ),
				array(
					'key'      => 'imageRatio',
					'type'     => 'segmented',
					'label'    => __( 'Image ratio', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'showImage', 'value' => true ),
					'options'  => array(
						array( 'value' => 'landscape', 'label' => __( '16:9', 'etu-blocks' ) ),
						array( 'value' => 'square', 'label' => __( '1:1', 'etu-blocks' ) ),
						array( 'value' => 'portrait', 'label' => __( '3:4', 'etu-blocks' ) ),
					),
				),
				array(
					'key'      => 'showPlaceholder',
					'type'     => 'toggle',
					'label'    => __( 'Placeholder when no image', 'etu-blocks' ),
					'help'     => __( 'Show a graphic when a post has no featured image.', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'showImage', 'value' => true ),
				),
				array( 'key' => 'showCategory', 'type' => 'toggle', 'label' => __( 'Category badge', 'etu-blocks' ) ),
				array( 'key' => 'showDate', 'type' => 'toggle', 'label' => __( 'Date', 'etu-blocks' ) ),
				array( 'key' => 'showExcerpt', 'type' => 'toggle', 'label' => __( 'Excerpt', 'etu-blocks' ) ),
				array(
					'key'      => 'excerptWords',
					'type'     => 'range',
					'label'    => __( 'Excerpt words', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'showExcerpt', 'value' => true ),
					'min'      => 5,
					'max'      => 60,
					'step'     => 1,
				),
				array( 'key' => 'showButton', 'type' => 'toggle', 'label' => __( 'Read-more button', 'etu-blocks' ) ),
				array(
					'key'      => 'buttonText',
					'type'     => 'text',
					'label'    => __( 'Button text', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'showButton', 'value' => true ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Colours', 'etu-blocks' ),
			'controls' => array(
				array( 'key' => 'accentColor', 'type' => 'color', 'label' => __( 'Accent', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => '--etu-pf-accent' ) ),
				array(
					'key'      => 'overlayColor',
					'type'     => 'color',
					'label'    => __( 'Overlay colour', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'cardStyle', 'value' => 'overlay' ),
					'css'      => array( 'selector' => '', 'property' => '--etu-pf-overlay-color' ),
				),
				array(
					'key'      => 'overlayOpacity',
					'type'     => 'range',
					'label'    => __( 'Overlay opacity', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'cardStyle', 'value' => 'overlay' ),
					'units'    => array( '%' ),
					'min'      => 0,
					'max'      => 100,
					'css'      => array( 'selector' => '', 'property' => '--etu-pf-overlay-opacity' ),
				),
				array( 'key' => 'cardBg', 'type' => 'color', 'label' => __( 'Card background', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => '--etu-pf-card-bg' ) ),
				array( 'key' => 'titleColor', 'type' => 'color', 'label' => __( 'Title colour', 'etu-blocks' ), 'css' => array( 'selector' => '.etu-post-filter__title', 'property' => 'color' ) ),
				array( 'key' => 'textColor', 'type' => 'color', 'label' => __( 'Text colour', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => '--etu-pf-text' ) ),
			),
		),
		array(
			'tab'      => 'advanced',
			'title'    => __( 'Spacing', 'etu-blocks' ),
			'controls' => array(
				array( 'key' => 'blockPadding', 'type' => 'spacing', 'label' => __( 'Padding', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => 'padding' ) ),
				array( 'key' => 'blockMargin', 'type' => 'spacing', 'label' => __( 'Margin', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => 'margin' ) ),
			),
		),
	),
);
