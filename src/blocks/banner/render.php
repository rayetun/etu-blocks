<?php
/**
 * Banner (Hero) block — server render.
 *
 * A hero section: background colour + optional image with a colour overlay, then
 * an eyebrow / heading / text / buttons stack positioned within the banner. The
 * heading carries the meaning; the background and overlay are decorative.
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

$eyebrow   = (string) ( $attributes['eyebrow'] ?? '' );
$heading   = (string) ( $attributes['heading'] ?? '' );
$text      = (string) ( $attributes['text'] ?? '' );
$p_text    = trim( (string) ( $attributes['primaryText'] ?? '' ) );
$p_url     = trim( (string) ( $attributes['primaryUrl'] ?? '' ) );
$s_text    = trim( (string) ( $attributes['secondaryText'] ?? '' ) );
$s_url     = trim( (string) ( $attributes['secondaryUrl'] ?? '' ) );

if ( '' === trim( $heading ) && '' === trim( $text ) && '' === $p_text ) {
	return;
}

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$height = (string) ( $attributes['heightPreset'] ?? 'medium' );
if ( ! in_array( $height, array( 'small', 'medium', 'large', 'full' ), true ) ) {
	$height = 'medium';
}
$c_align = (string) ( $attributes['contentAlign'] ?? 'center' );
if ( ! in_array( $c_align, array( 'left', 'center', 'right' ), true ) ) {
	$c_align = 'center';
}
$v_align = (string) ( $attributes['verticalAlign'] ?? 'center' );
if ( ! in_array( $v_align, array( 'top', 'center', 'bottom' ), true ) ) {
	$v_align = 'center';
}

$bg_image = is_array( $attributes['bgImage'] ?? null ) ? $attributes['bgImage'] : array();
$bg_url   = isset( $bg_image['url'] ) ? esc_url_raw( (string) $bg_image['url'] ) : '';

$inline_allowed = array( 'strong' => array(), 'em' => array(), 'b' => array(), 'i' => array(), 'br' => array() );

$scope         = '.etu-banner-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/banner', $attributes, $scope );
if ( '' !== $bg_url ) {
	$generated_css .= $scope . '{background-image:url(' . esc_url( $bg_url ) . ');}';
}

$classes = array(
	'etu-banner',
	'etu-banner--h-' . $height,
	'etu-banner--align-' . $c_align,
	'etu-banner--valign-' . $v_align,
	'etu-banner-' . $block_id,
);
if ( '' !== $bg_url ) {
	$classes[] = 'etu-banner--has-image';
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

/**
 * Render a hero button (link when a URL is set, else a focusable button).
 */
$render_btn = static function ( $text_val, $url, $variant ) {
	$cls = 'etu-banner__btn etu-banner__btn--' . $variant;
	if ( '' !== $url ) {
		return '<a class="' . $cls . '" href="' . esc_url( $url ) . '">' . esc_html( $text_val ) . '</a>';
	}
	return '<button type="button" class="' . $cls . '">' . esc_html( $text_val ) . '</button>';
};

\Etublocks\CSS_Generator::inject( 'etu/banner', $generated_css );

echo wp_kses( '<div ' . $wrapper_attributes . '>', \Etublocks\Rendering::allowed() );
echo '<div class="etu-banner__overlay" aria-hidden="true"></div>';
echo '<div class="etu-banner__content">';

if ( '' !== trim( $eyebrow ) ) {
	echo '<p class="etu-banner__eyebrow">' . wp_kses( $eyebrow, $inline_allowed ) . '</p>';
}
if ( '' !== trim( $heading ) ) {
	echo '<h2 class="etu-banner__heading">' . wp_kses( $heading, $inline_allowed ) . '</h2>';
}
if ( '' !== trim( $text ) ) {
	echo '<p class="etu-banner__text">' . wp_kses( $text, $inline_allowed ) . '</p>';
}

if ( '' !== $p_text || '' !== $s_text ) {
	echo '<div class="etu-banner__buttons">';
	if ( '' !== $p_text ) {
		echo wp_kses( $render_btn( $p_text, $p_url, 'primary' ), \Etublocks\Rendering::allowed() );
	}
	if ( '' !== $s_text ) {
		echo wp_kses( $render_btn( $s_text, $s_url, 'secondary' ), \Etublocks\Rendering::allowed() );
	}
	echo '</div>';
}

echo '</div>'; // content
echo '</div>'; // banner
