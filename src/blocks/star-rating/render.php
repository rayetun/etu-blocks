<?php
/**
 * Star Rating block — server render.
 *
 * A fractional rating drawn with two layers (empty row + a clipped filled row) so
 * half values render cleanly. The whole rating is a single labelled image
 * (role="img"); the glyphs are decorative.
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

$max        = max( 1, min( 10, (int) ( $attributes['maxStars'] ?? 5 ) ) );
$rating     = max( 0, min( (float) $max, (float) ( $attributes['rating'] ?? 0 ) ) );
$show_value = ! isset( $attributes['showValue'] ) || ! empty( $attributes['showValue'] );
$label      = (string) ( $attributes['label'] ?? '' );

$preset = (string) ( $attributes['preset'] ?? 'star' );
$glyphs = array(
	'star'   => '★',
	'heart'  => '♥',
	'circle' => '●',
);
if ( ! isset( $glyphs[ $preset ] ) ) {
	$preset = 'star';
}
$glyph = $glyphs[ $preset ];

$percent = ( $rating / $max ) * 100;
$row     = str_repeat( $glyph, $max );

// "4.5" stays; "4.0" becomes "4".
$rating_display = rtrim( rtrim( number_format( $rating, 1 ), '0' ), '.' );

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$scope         = '.etu-star-rating-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/star-rating', $attributes, $scope );

$classes = array( 'etu-star-rating', 'etu-star-rating--' . $preset, 'etu-star-rating-' . $block_id );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

$aria = ( '' !== trim( $label ) ? wp_strip_all_tags( $label ) . ': ' : '' )
	. sprintf(
		/* translators: 1: rating value, 2: maximum. */
		esc_attr__( 'Rated %1$s out of %2$d', 'etu-blocks' ),
		$rating_display,
		$max
	);

\Etublocks\CSS_Generator::inject( 'etu/star-rating', $generated_css );
echo wp_kses( '<div ' . $wrapper_attributes . ' role="img" aria-label="' . esc_attr( $aria ) . '">', \Etublocks\Rendering::allowed() );

if ( '' !== trim( $label ) ) {
	echo '<span class="etu-star-rating__label">' . esc_html( $label ) . '</span>';
}

echo '<span class="etu-star-rating__stars" aria-hidden="true">';
echo '<span class="etu-star-rating__empty">' . esc_html( $row ) . '</span>';
echo '<span class="etu-star-rating__filled" style="width:' . esc_attr( (string) round( $percent, 2 ) ) . '%">' . esc_html( $row ) . '</span>';
echo '</span>';

if ( $show_value ) {
	echo '<span class="etu-star-rating__value">' . esc_html( $rating_display ) . '</span>';
}

echo '</div>';
