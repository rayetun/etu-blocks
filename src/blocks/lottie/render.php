<?php
/**
 * Lottie block — server render.
 *
 * Outputs a container that the Interactivity view module fills with a Lottie
 * animation (the lottie-web player is bundled with the block, not loaded from a
 * CDN). Playback is triggered on load, on scroll into view, on hover/focus, or on
 * click. Reduced-motion is honoured (auto triggers show a still first frame).
 * Accessibility: decorative animations are hidden from assistive tech; meaningful
 * ones expose a text alternative.
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

$source = 'file' === ( $attributes['source'] ?? 'url' ) ? 'file' : 'url';
$file   = is_array( $attributes['file'] ?? null ) ? $attributes['file'] : array();

if ( 'file' === $source ) {
	$src = isset( $file['url'] ) ? esc_url_raw( (string) $file['url'] ) : '';
} else {
	$src = esc_url_raw( trim( (string) ( $attributes['src'] ?? '' ) ) );
}
if ( '' === $src ) {
	return; // No animation set.
}

$trigger = (string) ( $attributes['trigger'] ?? 'viewport' );
if ( ! in_array( $trigger, array( 'autoplay', 'viewport', 'hover', 'click' ), true ) ) {
	$trigger = 'viewport';
}

$loop     = ! isset( $attributes['loop'] ) || ! empty( $attributes['loop'] );
$speed    = max( 25, min( 300, (int) ( $attributes['speedPct'] ?? 100 ) ) ) / 100;
$decor    = ! isset( $attributes['decorative'] ) || ! empty( $attributes['decorative'] );
$title    = trim( (string) ( $attributes['title'] ?? '' ) );

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$scope         = '.etu-lottie-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/lottie', $attributes, $scope );

$classes = array( 'etu-lottie', 'etu-lottie--' . $trigger, 'etu-lottie-' . $block_id );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );
$context            = wp_interactivity_data_wp_context(
	array(
		'src'     => $src,
		'loop'    => $loop,
		'speed'   => $speed,
		'trigger' => $trigger,
	)
);

// Canvas element + its a11y and interaction attributes.
$canvas_aria = '';
if ( $decor ) {
	$canvas_aria = ' aria-hidden="true"';
} elseif ( 'click' !== $trigger ) {
	$label       = '' !== $title ? $title : __( 'Animation', 'etu-blocks' );
	$canvas_aria = ' role="img" aria-label="' . esc_attr( $label ) . '"';
}

\Etublocks\CSS_Generator::inject( 'etu/lottie', $generated_css );

$wrapper_hidden = $decor ? ' aria-hidden="true"' : '';
echo wp_kses( '<div ' . $wrapper_attributes . $wrapper_hidden . ' data-wp-interactive="etu/lottie" ' . $context . ' data-wp-init="callbacks.init">', \Etublocks\Rendering::allowed() );

if ( 'click' === $trigger ) {
	$label = '' !== $title ? $title : __( 'Play animation', 'etu-blocks' );
	echo '<button type="button" class="etu-lottie__canvas etu-lottie__canvas--button" data-wp-on--click="actions.toggle" aria-label="' . esc_attr( $label ) . '"' . ( $decor ? ' aria-hidden="true" tabindex="-1"' : '' ) . '></button>';
} elseif ( 'hover' === $trigger ) {
	echo wp_kses( ( '<div class="etu-lottie__canvas"' . $canvas_aria . ' tabindex="0"'
		. ' data-wp-on--mouseenter="actions.playFromStart" data-wp-on--mouseleave="actions.pause"'
		. ' data-wp-on--focusin="actions.playFromStart" data-wp-on--focusout="actions.pause"></div>' ), \Etublocks\Rendering::allowed() );
} else {
	echo wp_kses( '<div class="etu-lottie__canvas"' . $canvas_aria . '></div>', \Etublocks\Rendering::allowed() );
}

echo '</div>';
