<?php
/**
 * Tabs block — server render.
 *
 * Implements the ARIA Tabs pattern: a `tablist` of `tab` buttons and matching
 * `tabpanel`s. Selection lives in each element's `context.selected` boolean (so
 * the server resolves `aria-selected`, roving `tabindex`, and `hidden` at render
 * time), coordinated at runtime through the wrapper's shared `activeId`. The
 * store handles click selection and arrow / Home / End keyboard navigation.
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

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- This template is included within the block render callback's method scope; the variables below are local to that scope, not global.

$tabs = isset( $attributes['tabs'] ) && is_array( $attributes['tabs'] ) ? $attributes['tabs'] : array();

$tabs = array_values(
	array_filter(
		$tabs,
		static function ( $tab ) {
			return is_array( $tab ) && '' !== trim( (string) ( $tab['label'] ?? '' ) );
		}
	)
);

if ( empty( $tabs ) ) {
	return;
}

$label_allowed = array(
	'strong' => array(),
	'em'     => array(),
	'b'      => array(),
	'i'      => array(),
);

$base       = wp_unique_id( 'etu-tabs-' );
$tab_ids    = array_map( 'strval', array_keys( $tabs ) );
$aria_label = trim( (string) ( $attributes['ariaLabel'] ?? '' ) );
if ( '' === $aria_label ) {
	$aria_label = __( 'Tabs', 'etu-blocks' );
}

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( $base ), 0, 10 );
}

$preset = (string) ( $attributes['preset'] ?? 'line' );
if ( ! in_array( $preset, array( 'line', 'pill', 'card' ), true ) ) {
	$preset = 'line';
}

$scope         = '.etu-tabs-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/tabs', $attributes, $scope );

$classes = array( 'etu-tabs', 'etu-tabs--preset-' . $preset, 'etu-tabs-' . $block_id );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );
$wrapper_context    = wp_interactivity_data_wp_context(
	array(
		'activeId' => '0',
		'tabIds'   => $tab_ids,
		'baseId'   => $base,
	)
);

\Etublocks\CSS_Generator::inject( 'etu/tabs', $generated_css );
echo wp_kses( '<div ' . $wrapper_attributes . ' data-wp-interactive="etu/tabs" ' . $wrapper_context . '>', \Etublocks\Rendering::allowed() );

// Tablist.
echo '<div class="etu-tabs__list" role="tablist" aria-label="' . esc_attr( $aria_label ) . '">';
foreach ( $tabs as $i => $tab ) {
	$selected = ( 0 === $i );
	$tab_id   = (string) $i;
	$btn_id   = $base . '-tab-' . $i;
	$panel_id = $base . '-panel-' . $i;

	$tab_context = wp_interactivity_data_wp_context(
		array(
			'id'       => $tab_id,
			'selected' => $selected,
			'tabindex' => $selected ? 0 : -1,
		)
	);

	echo wp_kses( ( '<button type="button" class="etu-tabs__tab" role="tab"'
		. ' id="' . esc_attr( $btn_id ) . '"'
		. ' aria-controls="' . esc_attr( $panel_id ) . '"'
		. ' ' . $tab_context
		. ' data-wp-watch="callbacks.syncSelected"'
		. ' data-wp-bind--aria-selected="context.selected"'
		. ' data-wp-bind--tabindex="context.tabindex"'
		. ' data-wp-on--click="actions.select"'
		. ' data-wp-on--keydown="actions.onKeydown">' ), \Etublocks\Rendering::allowed() );
	echo wp_kses( (string) $tab['label'], $label_allowed );
	echo '</button>';
}
echo '</div>';

// Panels.
foreach ( $tabs as $i => $tab ) {
	$selected = ( 0 === $i );
	$tab_id   = (string) $i;
	$btn_id   = $base . '-tab-' . $i;
	$panel_id = $base . '-panel-' . $i;

	$panel_context = wp_interactivity_data_wp_context(
		array(
			'id'       => $tab_id,
			'selected' => $selected,
		)
	);

	echo wp_kses( ( '<div class="etu-tabs__panel" role="tabpanel" tabindex="0"'
		. ' id="' . esc_attr( $panel_id ) . '"'
		. ' aria-labelledby="' . esc_attr( $btn_id ) . '"'
		. ' ' . $panel_context
		. ' data-wp-watch="callbacks.syncSelected"'
		. ' data-wp-bind--hidden="!context.selected">' ), \Etublocks\Rendering::allowed() );
	echo '<div class="etu-tabs__content">' . wp_kses_post( (string) ( $tab['content'] ?? '' ) ) . '</div>';
	echo '</div>';
}

echo '</div>';
