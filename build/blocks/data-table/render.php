<?php
/**
 * Data Table block — server render.
 *
 * A real, accessible <table>: <caption>, <th scope="col">, and a keyboard-focusable
 * scroll region. Sorting and search enhance it on the front end via the Interactivity
 * store, but the full data is present and readable with no JS. Sortable headers carry
 * aria-sort; search updates a polite live region with the visible-row count.
 *
 * @package Etublocks
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused (dynamic block).
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Included within the block render callback's method scope; these are local, not global.

$raw_cols = is_array( $attributes['columns'] ?? null ) ? $attributes['columns'] : array();
$raw_rows = is_array( $attributes['rows'] ?? null ) ? $attributes['rows'] : array();

// Normalise columns.
$columns = array();
foreach ( $raw_cols as $col ) {
	if ( ! is_array( $col ) ) {
		continue;
	}
	$align = (string) ( $col['align'] ?? 'left' );
	if ( ! in_array( $align, array( 'left', 'center', 'right' ), true ) ) {
		$align = 'left';
	}
	$type = 'number' === ( $col['type'] ?? 'text' ) ? 'number' : 'text';
	$columns[] = array(
		'label' => (string) ( $col['label'] ?? '' ),
		'align' => $align,
		'type'  => $type,
	);
}

if ( empty( $columns ) ) {
	return; // Nothing to show without columns.
}
$col_count = count( $columns );

$caption   = trim( (string) ( $attributes['caption'] ?? '' ) );
$sortable  = ! isset( $attributes['sortable'] ) || ! empty( $attributes['sortable'] );
$search    = ! empty( $attributes['searchable'] );
$sticky    = ! empty( $attributes['stickyHeader'] );
$striped   = ! isset( $attributes['striped'] ) || ! empty( $attributes['striped'] );
$bordered  = ! empty( $attributes['bordered'] );
$compact   = ! empty( $attributes['compact'] );
$responsive = 'stack' === ( $attributes['responsive'] ?? 'scroll' ) ? 'stack' : 'scroll';

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}
$table_id = 'etu-dt-' . $block_id;

$inline_allowed = array( 'strong' => array(), 'em' => array(), 'b' => array(), 'i' => array(), 'br' => array() );

$scope         = '.etu-data-table-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/data-table', $attributes, $scope );

$classes = array( 'etu-data-table', 'etu-data-table--resp-' . $responsive, 'etu-data-table-' . $block_id );
if ( $striped ) {
	$classes[] = 'is-striped';
}
if ( $bordered ) {
	$classes[] = 'is-bordered';
}
if ( $compact ) {
	$classes[] = 'is-compact';
}
if ( $sticky ) {
	$classes[] = 'is-sticky';
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

$region_label = '' !== $caption ? wp_strip_all_tags( $caption ) : __( 'Data table', 'etu-blocks' );

\Etublocks\CSS_Generator::inject( 'etu/data-table', $generated_css );

echo wp_kses( '<div ' . $wrapper_attributes . ' data-wp-interactive="etu/data-table">', \Etublocks\Rendering::allowed() );

if ( $search ) {
	echo '<div class="etu-data-table__toolbar">';
	echo '<input type="search" class="etu-data-table__search" aria-controls="' . esc_attr( $table_id ) . '" placeholder="' . esc_attr__( 'Search…', 'etu-blocks' ) . '" aria-label="' . esc_attr__( 'Search table', 'etu-blocks' ) . '" data-wp-on--input="actions.search" />';
	/* translators: 1: number of matching rows, 2: total number of rows. */
	echo '<span class="etu-data-table__status" role="status" aria-live="polite" data-tpl="' . esc_attr__( '%1$s of %2$s rows', 'etu-blocks' ) . '"></span>';
	echo '</div>';
}

echo '<div class="etu-data-table__scroll" role="region" aria-label="' . esc_attr( $region_label ) . '" tabindex="0">';
echo '<table id="' . esc_attr( $table_id ) . '" class="etu-data-table__table">';

if ( '' !== $caption ) {
	echo '<caption class="etu-data-table__caption">' . wp_kses( $caption, $inline_allowed ) . '</caption>';
}

echo '<thead><tr>';
foreach ( $columns as $i => $col ) {
	$th_class = 'etu-data-table__th etu-data-table__cell--' . $col['align'];
	if ( $sortable ) {
		echo '<th scope="col" class="' . esc_attr( $th_class ) . '" data-col-index="' . esc_attr( (string) $i ) . '" data-col-type="' . esc_attr( $col['type'] ) . '" aria-sort="none">';
		echo '<button type="button" class="etu-data-table__sort" data-wp-on--click="actions.sort">';
		echo '<span class="etu-data-table__sort-label">' . esc_html( $col['label'] ) . '</span>';
		echo '<span class="etu-data-table__sort-ind" aria-hidden="true"></span>';
		echo '</button>';
		echo '</th>';
	} else {
		echo '<th scope="col" class="' . esc_attr( $th_class ) . '">' . esc_html( $col['label'] ) . '</th>';
	}
}
echo '</tr></thead>';

echo '<tbody>';
foreach ( $raw_rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}
	// Build the row's search haystack from its cells.
	$cells    = array();
	$haystack = array();
	for ( $i = 0; $i < $col_count; $i++ ) {
		$val      = isset( $row[ $i ] ) ? (string) $row[ $i ] : '';
		$cells[]  = $val;
		$haystack[] = $val;
	}
	echo '<tr data-search="' . esc_attr( strtolower( implode( ' ', $haystack ) ) ) . '">';
	foreach ( $columns as $i => $col ) {
		$td_class = 'etu-data-table__cell etu-data-table__cell--' . $col['align'];
		echo '<td class="' . esc_attr( $td_class ) . '" data-label="' . esc_attr( $col['label'] ) . '">' . esc_html( $cells[ $i ] ) . '</td>';
	}
	echo '</tr>';
}
echo '</tbody>';

echo '</table>';
echo '</div>'; // scroll
echo '</div>'; // root
