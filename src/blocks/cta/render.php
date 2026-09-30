<?php
/**
 * CTA block — server render.
 *
 * A heading, supporting description and up to two buttons. Static; styling comes
 * from the schema-driven generator + preset class. All text escaped; new-tab
 * buttons announce themselves to assistive tech.
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

$heading     = (string) ( $attributes['heading'] ?? '' );
$description = (string) ( $attributes['description'] ?? '' );
$p_text      = trim( (string) ( $attributes['primaryText'] ?? '' ) );
$p_url       = trim( (string) ( $attributes['primaryUrl'] ?? '' ) );
$s_text      = trim( (string) ( $attributes['secondaryText'] ?? '' ) );
$s_url       = trim( (string) ( $attributes['secondaryUrl'] ?? '' ) );
$new_tab     = ! empty( $attributes['buttonNewTab'] );

$inline_allowed = array(
	'strong' => array(),
	'em'     => array(),
	'b'      => array(),
	'i'      => array(),
	'br'     => array(),
);

if ( '' === trim( $heading ) && '' === trim( $description ) && '' === $p_text && '' === $s_text ) {
	return;
}

$make_button = static function ( $text, $url, $modifier, $new_tab ) {
	if ( '' === $text || '' === $url ) {
		return '';
	}
	$attrs  = 'class="etu-cta__button etu-cta__button--' . esc_attr( $modifier ) . '" href="' . esc_url( $url ) . '"';
	$suffix = '';
	if ( $new_tab ) {
		$attrs  .= ' target="_blank" rel="noopener noreferrer"';
		$suffix  = '<span class="screen-reader-text"> ' . esc_html__( '(opens in a new tab)', 'etu-blocks' ) . '</span>';
	}
	return '<a ' . $attrs . '>' . esc_html( $text ) . $suffix . '</a>';
};

$primary_html   = $make_button( $p_text, $p_url, 'primary', $new_tab );
$secondary_html = $make_button( $s_text, $s_url, 'secondary', $new_tab );

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$preset = (string) ( $attributes['preset'] ?? 'centered' );
if ( ! in_array( $preset, array( 'centered', 'split', 'banner' ), true ) ) {
	$preset = 'centered';
}

$scope         = '.etu-cta-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/cta', $attributes, $scope );

$classes = array( 'etu-cta', 'etu-cta--preset-' . $preset, 'etu-cta-' . $block_id );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

$heading_html     = '' !== trim( $heading ) ? '<h2 class="etu-cta__heading">' . wp_kses( $heading, $inline_allowed ) . '</h2>' : '';
$description_html = '' !== trim( $description ) ? '<div class="etu-cta__description">' . wp_kses( $description, $inline_allowed ) . '</div>' : '';

$content_html = '';
if ( '' !== $heading_html || '' !== $description_html ) {
	$content_html = '<div class="etu-cta__content">' . $heading_html . $description_html . '</div>';
}
$actions_html = '';
if ( '' !== $primary_html || '' !== $secondary_html ) {
	$actions_html = '<div class="etu-cta__actions">' . $primary_html . $secondary_html . '</div>';
}

\Etublocks\CSS_Generator::inject( 'etu/cta', $generated_css );
echo wp_kses( '<div ' . $wrapper_attributes . '>' . $content_html . $actions_html . '</div>', \Etublocks\Rendering::allowed() );
