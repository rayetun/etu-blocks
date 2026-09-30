<?php
/**
 * Post Grid block — control schema (design + query; posts are queried at render).
 *
 * @package Etublocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Included within Schemas::get()'s method scope; local, not global.

// Dynamic option lists are only needed in the editor; skip the queries on the front end.
$etu_pg_post_types = array( array( 'value' => 'post', 'label' => __( 'Posts', 'etu-blocks' ) ) );
$etu_pg_categories = array();

if ( is_admin() ) {
	$etu_pg_post_types = array();
	foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $etu_pg_pt ) {
		if ( in_array( $etu_pg_pt->name, array( 'attachment' ), true ) ) {
			continue;
		}
		$etu_pg_post_types[] = array( 'value' => $etu_pg_pt->name, 'label' => $etu_pg_pt->labels->name );
	}

	$etu_pg_terms = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false, 'number' => 100 ) );
	if ( is_array( $etu_pg_terms ) ) {
		foreach ( $etu_pg_terms as $etu_pg_term ) {
			$etu_pg_categories[] = array( 'value' => $etu_pg_term->slug, 'label' => $etu_pg_term->name );
		}
	}
}
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

return array(
	'sections' => array(
		array(
			'tab'      => 'layout',
			'title'    => __( 'Layout', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'layout',
					'type'    => 'segmented',
					'label'   => __( 'Layout', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'grid', 'label' => __( 'Grid', 'etu-blocks' ) ),
						array( 'value' => 'masonry', 'label' => __( 'Masonry', 'etu-blocks' ) ),
						array( 'value' => 'carousel', 'label' => __( 'Carousel', 'etu-blocks' ) ),
					),
				),
				array(
					'key'      => 'columns',
					'type'     => 'segmented',
					'label'    => __( 'Columns', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'layout', 'in' => array( 'grid', 'masonry' ) ),
					'options'  => array(
						array( 'value' => '2', 'label' => '2' ),
						array( 'value' => '3', 'label' => '3' ),
						array( 'value' => '4', 'label' => '4' ),
					),
				),
				array(
					'key'        => 'carouselPerView',
					'type'       => 'range',
					'label'      => __( 'Slides per view', 'etu-blocks' ),
					'showWhen'   => array( 'key' => 'layout', 'value' => 'carousel' ),
					'responsive' => true,
					'min'        => 1,
					'max'        => 6,
					'step'       => 1,
				),
				array(
					'key'     => 'cardStyle',
					'type'    => 'preset',
					'label'   => __( 'Card style', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'elevated', 'label' => __( 'Elevated', 'etu-blocks' ) ),
						array( 'value' => 'bordered', 'label' => __( 'Bordered', 'etu-blocks' ) ),
						array( 'value' => 'minimal', 'label' => __( 'Minimal', 'etu-blocks' ) ),
						array( 'value' => 'overlay', 'label' => __( 'Overlay', 'etu-blocks' ) ),
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
				array(
					'key'   => 'cardGap',
					'type'  => 'range',
					'label' => __( 'Gap', 'etu-blocks' ),
					'units' => array( 'px' ),
					'min'   => 0,
					'max'   => 60,
					'css'   => array( 'selector' => '', 'property' => '--etu-pg-gap' ),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Query', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'     => 'postType',
					'type'    => 'select',
					'label'   => __( 'Post type', 'etu-blocks' ),
					'options' => $etu_pg_post_types,
				),
				array(
					'key'   => 'perPage',
					'type'  => 'range',
					'label' => __( 'Number of posts', 'etu-blocks' ),
					'min'   => 1,
					'max'   => 24,
					'step'  => 1,
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
				array(
					'key'     => 'queryCategories',
					'type'    => 'checklist',
					'label'   => __( 'Categories', 'etu-blocks' ),
					'help'    => __( 'Leave empty for all categories.', 'etu-blocks' ),
					'options' => $etu_pg_categories,
				),
				array(
					'key'   => 'offset',
					'type'  => 'range',
					'label' => __( 'Skip (offset)', 'etu-blocks' ),
					'min'   => 0,
					'max'   => 20,
					'step'  => 1,
				),
				array(
					'key'   => 'excludeCurrent',
					'type'  => 'toggle',
					'label' => __( 'Exclude current post', 'etu-blocks' ),
				),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Card content', 'etu-blocks' ),
			'controls' => array(
				array( 'key' => 'showImage', 'type' => 'toggle', 'label' => __( 'Featured image', 'etu-blocks' ) ),
				array(
					'key'     => 'imageRatio',
					'type'    => 'segmented',
					'label'   => __( 'Image ratio', 'etu-blocks' ),
					'options' => array(
						array( 'value' => 'landscape', 'label' => __( '16:9', 'etu-blocks' ) ),
						array( 'value' => 'square', 'label' => __( '1:1', 'etu-blocks' ) ),
						array( 'value' => 'portrait', 'label' => __( '3:4', 'etu-blocks' ) ),
						array( 'value' => 'auto', 'label' => __( 'Auto', 'etu-blocks' ) ),
					),
				),
				array( 'key' => 'showCategory', 'type' => 'toggle', 'label' => __( 'Category badge', 'etu-blocks' ) ),
				array( 'key' => 'showTitle', 'type' => 'toggle', 'label' => __( 'Title', 'etu-blocks' ) ),
				array( 'key' => 'showMeta', 'type' => 'toggle', 'label' => __( 'Date', 'etu-blocks' ) ),
				array( 'key' => 'showAuthor', 'type' => 'toggle', 'label' => __( 'Author', 'etu-blocks' ) ),
				array( 'key' => 'showExcerpt', 'type' => 'toggle', 'label' => __( 'Excerpt', 'etu-blocks' ) ),
				array(
					'key'   => 'excerptLength',
					'type'  => 'range',
					'label' => __( 'Excerpt words', 'etu-blocks' ),
					'min'   => 5,
					'max'   => 60,
					'step'  => 1,
				),
				array( 'key' => 'showButton', 'type' => 'toggle', 'label' => __( 'Read-more button', 'etu-blocks' ) ),
				array( 'key' => 'buttonText', 'type' => 'text', 'label' => __( 'Button text', 'etu-blocks' ) ),
			),
		),
		array(
			'tab'      => 'layout',
			'title'    => __( 'Filter & navigation', 'etu-blocks' ),
			'controls' => array(
				array(
					'key'      => 'enableFilter',
					'type'     => 'toggle',
					'label'    => __( 'Category filter bar', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'layout', 'in' => array( 'grid', 'masonry' ) ),
				),
				array(
					'key'      => 'showArrows',
					'type'     => 'toggle',
					'label'    => __( 'Carousel arrows', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'layout', 'value' => 'carousel' ),
				),
				array(
					'key'      => 'showDots',
					'type'     => 'toggle',
					'label'    => __( 'Carousel dots', 'etu-blocks' ),
					'showWhen' => array( 'key' => 'layout', 'value' => 'carousel' ),
				),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Colours', 'etu-blocks' ),
			'controls' => array(
				array( 'key' => 'accentColor', 'type' => 'color', 'label' => __( 'Accent', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => '--etu-pg-accent' ) ),
				array( 'key' => 'cardBackground', 'type' => 'color', 'label' => __( 'Card background', 'etu-blocks' ), 'css' => array( 'selector' => '', 'property' => '--etu-pg-card-bg' ) ),
				array( 'key' => 'titleColor', 'type' => 'color', 'label' => __( 'Title colour', 'etu-blocks' ), 'css' => array( 'selector' => '.etu-post-grid__title', 'property' => 'color' ) ),
				array( 'key' => 'textColor', 'type' => 'color', 'label' => __( 'Text colour', 'etu-blocks' ), 'css' => array( 'selector' => '.etu-post-grid__card', 'property' => 'color' ) ),
			),
		),
		array(
			'tab'      => 'style',
			'title'    => __( 'Typography', 'etu-blocks' ),
			'controls' => array(
				array( 'key' => 'titleTypography', 'type' => 'typography', 'label' => __( 'Title', 'etu-blocks' ), 'css' => array( 'selector' => '.etu-post-grid__title' ) ),
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
