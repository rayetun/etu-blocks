<?php
/**
 * Progress Bar block — server render.
 *
 * A labelled progress bar exposed as role="progressbar" with aria-valuenow. The
 * fill is rendered at its final width server-side (correct for no-JS and AT); on
 * the front end the Interactivity store animates it from zero when it scrolls into
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

$label      = (string) ( $attributes['label'] ?? '' );
$value      = max( 0, min( 100, (int) round( (float) ( $attributes['value'] ?? 0 ) ) ) );
$show_value = ! isset( $attributes['showValue'] ) || ! empty( $attributes['showValue'] );

$inline_allowed = array(
	'strong' => array(),
	'em'     => array(),
	'b'      => array(),
	'i'      => array(),
);

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$preset = (string) ( $attributes['preset'] ?? 'rounded' );
if ( ! in_array( $preset, array( 'rounded', 'striped', 'minimal' ), true ) ) {
	$preset = 'rounded';
}

$scope         = '.etu-progress-bar-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/progress-bar', $attributes, $scope );

$classes = array( 'etu-progress-bar', 'etu-progress-bar--preset-' . $preset, 'etu-progress-bar-' . $block_id );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );
$wrapper_context    = wp_interactivity_data_wp_context(
	array(
		'target' => $value,
		'width'  => $value . '%',
	)
);

$label_id  = $block_id . '-label';
$has_label = '' !== trim( $label );
$aria      = $has_label
	? ' aria-labelledby="' . esc_attr( $label_id ) . '"'
	: ' aria-label="' . esc_attr__( 'Progress', 'etu-blocks' ) . '"';

\Etublocks\CSS_Generator::inject( 'etu/progress-bar', $generated_css );
echo wp_kses( ( '<div ' . $wrapper_attributes . ' role="progressbar" aria-valuenow="' . esc_attr( (string) $value ) . '" aria-valuemin="0" aria-valuemax="100"' . $aria
	. ' data-wp-interactive="etu/progress-bar" ' . $wrapper_context . ' data-wp-init="callbacks.start">' ), \Etublocks\Rendering::allowed() );

if ( $has_label || $show_value ) {
	echo '<div class="etu-progress-bar__label">';
	if ( $has_label ) {
		echo '<span class="etu-progress-bar__text" id="' . esc_attr( $label_id ) . '">' . wp_kses( $label, $inline_allowed ) . '</span>';
	}
	if ( $show_value ) {
		echo '<span class="etu-progress-bar__value">' . esc_html( (string) $value ) . '%</span>';
	}
	echo '</div>';
}

echo '<div class="etu-progress-bar__track"><div class="etu-progress-bar__fill" data-wp-style--width="context.width"></div></div>';
echo '</div>';
