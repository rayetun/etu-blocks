<?php
/**
 * Counter block — server render.
 *
 * Renders the final number server-side (so it's present for no-JS, SEO and
 * assistive tech), bound to `context.displayValue`. On the front end the
 * Interactivity store animates the value from zero when the block scrolls into
 * view, honouring prefers-reduced-motion.
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

$number_raw = (string) ( $attributes['number'] ?? '0' );
$target_num = (float) preg_replace( '/[^0-9.\-]/', '', $number_raw );
$prefix     = (string) ( $attributes['prefix'] ?? '' );
$suffix     = (string) ( $attributes['suffix'] ?? '' );
$label      = (string) ( $attributes['label'] ?? '' );
$duration   = max( 200, min( 10000, (int) ( $attributes['duration'] ?? 2000 ) ) );
$use_sep    = ! isset( $attributes['separator'] ) || ! empty( $attributes['separator'] );

$display = $use_sep ? number_format( $target_num, 0 ) : (string) (int) $target_num;

$inline_allowed = array(
	'strong' => array(),
	'em'     => array(),
	'b'      => array(),
	'i'      => array(),
	'br'     => array(),
);

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$preset = (string) ( $attributes['preset'] ?? 'minimal' );
if ( ! in_array( $preset, array( 'minimal', 'card', 'boxed' ), true ) ) {
	$preset = 'minimal';
}

$scope         = '.etu-counter-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/counter', $attributes, $scope );

$classes = array( 'etu-counter', 'etu-counter--preset-' . $preset, 'etu-counter-' . $block_id );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );
$wrapper_context    = wp_interactivity_data_wp_context(
	array(
		'target'       => $target_num,
		'duration'     => $duration,
		'separator'    => $use_sep,
		'displayValue' => $display,
	)
);

\Etublocks\CSS_Generator::inject( 'etu/counter', $generated_css );
echo wp_kses( '<div ' . $wrapper_attributes . ' data-wp-interactive="etu/counter" ' . $wrapper_context . ' data-wp-init="callbacks.start">', \Etublocks\Rendering::allowed() );

echo '<div class="etu-counter__number">';
if ( '' !== $prefix ) {
	echo '<span class="etu-counter__prefix">' . esc_html( $prefix ) . '</span>';
}
echo '<span class="etu-counter__value" data-wp-text="context.displayValue">' . esc_html( $display ) . '</span>';
if ( '' !== $suffix ) {
	echo '<span class="etu-counter__suffix">' . esc_html( $suffix ) . '</span>';
}
echo '</div>';

if ( '' !== trim( $label ) ) {
	echo '<div class="etu-counter__label">' . wp_kses( $label, $inline_allowed ) . '</div>';
}

echo '</div>';
