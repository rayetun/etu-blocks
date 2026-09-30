<?php
/**
 * Modal / Popup block — server render.
 *
 * A dialog built on the Interactivity store: role="dialog" + aria-modal, focus is
 * trapped while open and restored to the trigger on close, Esc and the backdrop
 * close it, and body scroll is locked. The dialog is hidden (and untabbable) until
 * opened. Content is whatever blocks the author nested inside.
 *
 * @package Etublocks
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Rendered inner blocks (dialog body).
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Included within the block render callback's method scope; these are local, not global.

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$trigger = (string) ( $attributes['trigger'] ?? 'button' );
if ( ! in_array( $trigger, array( 'button', 'load', 'delay' ), true ) ) {
	$trigger = 'button';
}

$size = (string) ( $attributes['size'] ?? 'medium' );
if ( ! in_array( $size, array( 'small', 'medium', 'large' ), true ) ) {
	$size = 'medium';
}

$position = 'top' === ( $attributes['position'] ?? 'center' ) ? 'top' : 'center';

$animation = (string) ( $attributes['animation'] ?? 'zoom' );
if ( ! in_array( $animation, array( 'fade', 'zoom', 'slide' ), true ) ) {
	$animation = 'zoom';
}

$button_text = trim( (string) ( $attributes['buttonText'] ?? '' ) );
if ( '' === $button_text ) {
	$button_text = __( 'Open', 'etu-blocks' );
}

$show_close   = ! isset( $attributes['showClose'] ) || ! empty( $attributes['showClose'] );
$close_backdrop = ! isset( $attributes['closeOnBackdrop'] ) || ! empty( $attributes['closeOnBackdrop'] );
$show_once    = ! empty( $attributes['showOnce'] );

$delay = (int) ( $attributes['delaySeconds'] ?? 3 );
$delay = max( 1, min( 30, $delay ) );

$label = trim( (string) ( $attributes['dialogLabel'] ?? '' ) );
if ( '' === $label ) {
	$label = 'button' === $trigger ? $button_text : __( 'Dialog', 'etu-blocks' );
}

// Remembered "show once" dismissals are skipped server-side (no flash).
$cookie_key = 'etu-modal-' . $block_id;
$auto_open  = 'button' !== $trigger;
if ( $auto_open && $show_once && isset( $_COOKIE[ $cookie_key ] ) && '1' === sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_key ] ) ) ) {
	// Still render the (closed) dialog so a button-less popup can be reopened only
	// by reload; but with show-once remembered we simply skip auto-open. To avoid
	// rendering an unreachable dialog, bail when there is no button trigger.
	$auto_open = false;
}

$scope         = '.etu-modal-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/modal', $attributes, $scope );

$classes = array(
	'etu-modal',
	'etu-modal--size-' . $size,
	'etu-modal--pos-' . $position,
	'etu-modal--anim-' . $animation,
	'etu-modal-' . $block_id,
);

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

$context = wp_interactivity_data_wp_context(
	array(
		'open'            => false,
		'trigger'         => $trigger,
		'delay'           => $delay * 1000,
		'autoOpen'        => $auto_open,
		'showOnce'        => $show_once,
		'closeOnBackdrop' => $close_backdrop,
		'key'             => $cookie_key,
	)
);

$icon_close = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6 6 18"/></svg>';

\Etublocks\CSS_Generator::inject( 'etu/modal', $generated_css );

echo wp_kses( '<div ' . $wrapper_attributes . ' data-wp-interactive="etu/modal" ' . $context . ' data-wp-init="callbacks.init" data-wp-watch="callbacks.onToggle">', \Etublocks\Rendering::allowed() );

if ( 'button' === $trigger ) {
	echo '<div class="etu-modal__trigger-wrap">';
	echo '<button type="button" class="etu-modal__trigger" data-wp-on--click="actions.open">' . esc_html( $button_text ) . '</button>';
	echo '</div>';
}

echo '<div class="etu-modal__overlay" data-wp-class--is-open="context.open" data-wp-on--click="actions.backdropClick" data-wp-on--keydown="actions.onKeydown">';
echo '<div class="etu-modal__dialog" role="dialog" aria-modal="true" aria-label="' . esc_attr( $label ) . '" tabindex="-1">';

if ( $show_close ) {
	echo wp_kses( '<button type="button" class="etu-modal__close" data-wp-on--click="actions.close" aria-label="' . esc_attr__( 'Close dialog', 'etu-blocks' ) . '">' . $icon_close . '</button>', \Etublocks\Rendering::allowed() );
}

echo '<div class="etu-modal__content">';
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $content is the block's inner blocks, already rendered and escaped by WordPress core.
echo $content;
echo '</div>';

echo '</div>'; // dialog
echo '</div>'; // overlay
echo '</div>'; // root
