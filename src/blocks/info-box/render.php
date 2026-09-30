<?php
/**
 * Info Box block — server render.
 *
 * A static content block: a decorative icon, a heading, a description and an
 * optional button. Styling comes from the schema-driven CSS generator (scoped
 * inline) plus the preset design class. All user text is escaped; the icon is
 * decorative (aria-hidden); a new-tab button announces itself to assistive tech.
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

$icon        = trim( (string) ( $attributes['icon'] ?? '' ) );
$title       = (string) ( $attributes['title'] ?? '' );
$description = (string) ( $attributes['description'] ?? '' );
$button_text = trim( (string) ( $attributes['buttonText'] ?? '' ) );
$button_url  = trim( (string) ( $attributes['buttonUrl'] ?? '' ) );
$new_tab     = ! empty( $attributes['buttonNewTab'] );
$position    = 'inline' === ( $attributes['iconPosition'] ?? 'stacked' ) ? 'inline' : 'stacked';

$allowed_levels = array( 'h2', 'h3', 'h4', 'h5', 'h6', 'p' );
$heading_level  = strtolower( (string) ( $attributes['headingLevel'] ?? 'h3' ) );
if ( ! in_array( $heading_level, $allowed_levels, true ) ) {
	$heading_level = 'h3';
}

$inline_allowed = array(
	'strong' => array(),
	'em'     => array(),
	'b'      => array(),
	'i'      => array(),
	'br'     => array(),
);

if ( '' === trim( $title ) && '' === trim( $description ) && '' === $icon && '' === $button_text ) {
	return;
}

$icon_html = '';
if ( '' !== $icon ) {
	$icon_html = '<span class="etu-info-box__icon" aria-hidden="true">' . esc_html( $icon ) . '</span>';
}

$title_html = '';
if ( '' !== trim( $title ) ) {
	$title_html = '<' . $heading_level . ' class="etu-info-box__title">'
		. wp_kses( $title, $inline_allowed )
		. '</' . $heading_level . '>';
}

$description_html = '';
if ( '' !== trim( $description ) ) {
	$description_html = '<div class="etu-info-box__description">' . wp_kses_post( $description ) . '</div>';
}

$button_html = '';
if ( '' !== $button_text && '' !== $button_url ) {
	$button_attrs = 'class="etu-info-box__button" href="' . esc_url( $button_url ) . '"';
	$suffix       = '';
	if ( $new_tab ) {
		$button_attrs .= ' target="_blank" rel="noopener noreferrer"';
		$suffix        = '<span class="screen-reader-text"> ' . esc_html__( '(opens in a new tab)', 'etu-blocks' ) . '</span>';
	}
	$button_html = '<a ' . $button_attrs . '>' . esc_html( $button_text ) . $suffix . '</a>';
}

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$preset = (string) ( $attributes['preset'] ?? 'card' );
if ( ! in_array( $preset, array( 'minimal', 'card', 'bordered' ), true ) ) {
	$preset = 'card';
}

$scope         = '.etu-info-box-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/info-box', $attributes, $scope );

$classes = array(
	'etu-info-box',
	'etu-info-box--preset-' . $preset,
	'etu-info-box--' . $position,
	'etu-info-box-' . $block_id,
);

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

\Etublocks\CSS_Generator::inject( 'etu/info-box', $generated_css );
echo wp_kses( ( '<div ' . $wrapper_attributes . '>'
	. $icon_html
	. '<div class="etu-info-box__body">'
	. $title_html
	. $description_html
	. $button_html
	. '</div>'
	. '</div>' ), \Etublocks\Rendering::allowed() );
