<?php
/**
 * Comparison Table block — server render.
 *
 * A real <table>: column headers are <th scope="col">, feature labels are
 * <th scope="row">, and check / cross cells carry visually-hidden text so screen
 * readers hear "Included" / "Not included", not just an icon. The featured column
 * is highlighted; the label column sticks while scrolling on small screens.
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

$columns = isset( $attributes['columns'] ) && is_array( $attributes['columns'] ) ? array_values( $attributes['columns'] ) : array();
$rows    = isset( $attributes['rows'] ) && is_array( $attributes['rows'] ) ? array_values( $attributes['rows'] ) : array();

if ( empty( $columns ) || empty( $rows ) ) {
	return;
}

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$preset = (string) ( $attributes['preset'] ?? 'modern' );
if ( ! in_array( $preset, array( 'modern', 'bordered', 'minimal' ), true ) ) {
	$preset = 'modern';
}

$cell_align = 'left' === ( $attributes['cellAlign'] ?? 'center' ) ? 'left' : 'center';
$stripe     = ! isset( $attributes['stripe'] ) || ! empty( $attributes['stripe'] );

$inline_allowed = array( 'strong' => array(), 'em' => array(), 'b' => array(), 'i' => array(), 'br' => array() );

$icon_yes = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg>';
$icon_no  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M6 6l12 12"/><path d="M18 6 6 18"/></svg>';

// Per-instance scope must match EtuBlockStyle's `.etu-<slug>-<id>` so the editor
// preview and the front end target the same element.
$scope         = '.etu-comparison-table-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/comparison-table', $attributes, $scope );

$classes = array(
	'etu-ct',
	'etu-ct--preset-' . $preset,
	'etu-ct--cells-' . $cell_align,
	'etu-comparison-table-' . $block_id,
);
if ( $stripe ) {
	$classes[] = 'etu-ct--striped';
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

/**
 * Render one data cell's inner HTML.
 *
 * @param array $cell Cell { type, text }.
 * @return string
 */
$render_cell = static function ( $cell ) use ( $icon_yes, $icon_no, $inline_allowed ) {
	$type = is_array( $cell ) ? (string) ( $cell['type'] ?? 'text' ) : 'text';
	$text = is_array( $cell ) ? trim( (string) ( $cell['text'] ?? '' ) ) : trim( (string) $cell );

	if ( 'check' === $type ) {
		return '<span class="etu-ct__mark etu-ct__mark--yes">' . $icon_yes . '</span>'
			. '<span class="screen-reader-text">' . esc_html__( 'Included', 'etu-blocks' ) . '</span>';
	}
	if ( 'cross' === $type ) {
		return '<span class="etu-ct__mark etu-ct__mark--no">' . $icon_no . '</span>'
			. '<span class="screen-reader-text">' . esc_html__( 'Not included', 'etu-blocks' ) . '</span>';
	}
	if ( '' === $text || '-' === $text || '—' === $text ) {
		return '<span class="etu-ct__dash" aria-hidden="true">–</span>'
			. '<span class="screen-reader-text">' . esc_html__( 'Not included', 'etu-blocks' ) . '</span>';
	}
	return wp_kses( $text, $inline_allowed );
};

\Etublocks\CSS_Generator::inject( 'etu/comparison-table', $generated_css );

echo wp_kses( '<div ' . $wrapper_attributes . '>', \Etublocks\Rendering::allowed() );
echo '<div class="etu-ct__scroll">';
echo '<table class="etu-ct__table">';

// Header row.
echo '<thead><tr>';
echo '<td class="etu-ct__corner"></td>';
foreach ( $columns as $col ) {
	$is_high  = ! empty( $col['highlighted'] );
	$title    = (string) ( $col['title'] ?? '' );
	$badge    = trim( (string) ( $col['badge'] ?? '' ) );
	$btn_text = trim( (string) ( $col['buttonText'] ?? '' ) );
	$btn_url  = trim( (string) ( $col['buttonUrl'] ?? '' ) );

	echo '<th scope="col" class="etu-ct__col-head' . ( $is_high ? ' etu-ct__col-head--highlighted' : '' ) . '">';
	if ( '' !== $badge ) {
		echo '<span class="etu-ct__badge">' . esc_html( $badge ) . '</span>';
	}
	echo '<span class="etu-ct__col-title">' . wp_kses( $title, $inline_allowed ) . '</span>';
	if ( '' !== $btn_text ) {
		if ( '' !== $btn_url ) {
			echo '<a class="etu-ct__btn" href="' . esc_url( $btn_url ) . '">' . esc_html( $btn_text ) . '</a>';
		} else {
			echo '<span class="etu-ct__btn">' . esc_html( $btn_text ) . '</span>';
		}
	}
	echo '</th>';
}
echo '</tr></thead>';

// Body rows.
echo '<tbody>';
foreach ( $rows as $row ) {
	$label = (string) ( $row['label'] ?? '' );
	$cells = isset( $row['cells'] ) && is_array( $row['cells'] ) ? array_values( $row['cells'] ) : array();

	echo '<tr class="etu-ct__row">';
	echo '<th scope="row" class="etu-ct__row-head">' . wp_kses( $label, $inline_allowed ) . '</th>';
	foreach ( $columns as $ci => $col ) {
		$is_high = ! empty( $col['highlighted'] );
		$cell    = $cells[ $ci ] ?? array( 'type' => 'text', 'text' => '' );
		echo '<td class="etu-ct__cell' . ( $is_high ? ' etu-ct__cell--highlighted' : '' ) . '">';
		echo wp_kses( $render_cell( $cell ), \Etublocks\Rendering::allowed() );
		echo '</td>';
	}
	echo '</tr>';
}
echo '</tbody>';

echo '</table>';
echo '</div>';
echo '</div>';
