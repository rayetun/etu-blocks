<?php
/**
 * Notice block — server render.
 *
 * A coloured callout with an icon, title and message, and an optional dismiss.
 * Dismissal is handled by the Interactivity store; with "remember" on it sets a
 * cookie and this template skips rendering on later loads (see below). The icon
 * is decorative; the dismiss button is labelled.
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

$title       = (string) ( $attributes['title'] ?? '' );
$content     = (string) ( $attributes['content'] ?? '' );
$icon        = trim( (string) ( $attributes['icon'] ?? '' ) );
$dismissible = ! empty( $attributes['dismissible'] );
$remember    = ! empty( $attributes['rememberDismiss'] );

$inline_allowed = array(
	'strong' => array(),
	'em'     => array(),
	'b'      => array(),
	'i'      => array(),
	'br'     => array(),
	'a'      => array( 'href' => true, 'target' => true, 'rel' => true ),
);

if ( '' === trim( $title ) && '' === trim( $content ) ) {
	return;
}

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

// "Remember dismissal" is persisted client-side as a cookie; when present, skip
// rendering entirely so a remembered notice never flashes visible then hides.
$cookie_key = 'etu-notice-' . $block_id;
if ( $remember && isset( $_COOKIE[ $cookie_key ] ) && '1' === sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_key ] ) ) ) {
	return;
}

$preset       = (string) ( $attributes['preset'] ?? 'info' );
$preset_icons = array(
	'info'    => 'ℹ',
	'success' => '✓',
	'warning' => '⚠',
	'danger'  => '✕',
	'neutral' => '•',
);
if ( ! isset( $preset_icons[ $preset ] ) ) {
	$preset = 'info';
}
if ( '' === $icon ) {
	$icon = $preset_icons[ $preset ];
}

$display = (string) ( $attributes['display'] ?? 'inline' );
if ( ! in_array( $display, array( 'inline', 'top', 'bottom' ), true ) ) {
	$display = 'inline';
}

$scope         = '.etu-notice-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/notice', $attributes, $scope );

$align = (string) ( $attributes['noticeAlign'] ?? 'left' );
if ( ! in_array( $align, array( 'left', 'center', 'right' ), true ) ) {
	$align = 'left';
}

$classes = array( 'etu-notice', 'etu-notice--type-' . $preset, 'etu-notice--display-' . $display, 'etu-notice--align-' . $align, 'etu-notice-' . $block_id );
if ( $dismissible ) {
	$classes[] = 'etu-notice--dismissible';
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );
$wrapper_context    = wp_interactivity_data_wp_context(
	array(
		'dismissed' => false,
		'remember'  => $remember,
		'display'   => $display,
		'key'       => 'etu-notice-' . $block_id,
	)
);

\Etublocks\CSS_Generator::inject( 'etu/notice', $generated_css );
echo wp_kses( ( '<div ' . $wrapper_attributes . ' data-wp-interactive="etu/notice" ' . $wrapper_context
	. ' data-wp-init="callbacks.init" data-wp-bind--hidden="context.dismissed">' ), \Etublocks\Rendering::allowed() );

echo '<span class="etu-notice__icon" aria-hidden="true">' . esc_html( $icon ) . '</span>';

echo '<div class="etu-notice__body">';
if ( '' !== trim( $title ) ) {
	echo '<p class="etu-notice__title">' . wp_kses( $title, $inline_allowed ) . '</p>';
}
if ( '' !== trim( $content ) ) {
	echo '<div class="etu-notice__content">' . wp_kses( $content, $inline_allowed ) . '</div>';
}
echo '</div>';

if ( $dismissible ) {
	echo '<button type="button" class="etu-notice__dismiss" aria-label="' . esc_attr__( 'Dismiss', 'etu-blocks' ) . '" data-wp-on--click="actions.dismiss">'
		. '<span aria-hidden="true">&times;</span></button>';
}

echo '</div>';
