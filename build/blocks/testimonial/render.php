<?php
/**
 * Testimonial block — server render.
 *
 * A quote in a <blockquote>, an optional star rating exposed as a single labelled
 * image (role="img"), and the author's avatar (decorative), name and role. All
 * text is escaped; styling comes from the schema-driven generator + preset class.
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

$quote       = (string) ( $attributes['quote'] ?? '' );
$name        = (string) ( $attributes['authorName'] ?? '' );
$role        = (string) ( $attributes['authorRole'] ?? '' );
$avatar_url  = trim( (string) ( $attributes['avatarUrl'] ?? '' ) );
$rating      = max( 0, min( 5, (int) round( (float) ( $attributes['rating'] ?? 5 ) ) ) );
$show_rating = ! isset( $attributes['showRating'] ) || ! empty( $attributes['showRating'] );

$inline_allowed = array(
	'strong' => array(),
	'em'     => array(),
	'b'      => array(),
	'i'      => array(),
	'br'     => array(),
);
$quote_allowed = $inline_allowed + array( 'a' => array( 'href' => true, 'target' => true, 'rel' => true ) );

if ( '' === trim( $quote ) && '' === trim( $name ) ) {
	return;
}

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$preset = (string) ( $attributes['preset'] ?? 'card' );
if ( ! in_array( $preset, array( 'card', 'quote', 'minimal' ), true ) ) {
	$preset = 'card';
}

$scope         = '.etu-testimonial-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/testimonial', $attributes, $scope );

$classes = array( 'etu-testimonial', 'etu-testimonial--preset-' . $preset, 'etu-testimonial-' . $block_id );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

// Star rating markup — one labelled image, stars decorative.
$rating_html = '';
if ( $show_rating ) {
	$star  = '<svg class="%s" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l2.9 6.3 6.9.6-5.2 4.6 1.6 6.7L12 17.3 5.8 20.8l1.6-6.7L2.2 9.5l6.9-.6z"/></svg>';
	$stars = '';
	for ( $i = 0; $i < 5; $i++ ) {
		$cls    = $i < $rating ? 'etu-testimonial__star' : 'etu-testimonial__star etu-testimonial__star--empty';
		$stars .= sprintf( $star, esc_attr( $cls ) );
	}
	$label       = sprintf(
		/* translators: %d: star rating out of five. */
		esc_attr__( 'Rated %d out of 5', 'etu-blocks' ),
		$rating
	);
	$rating_html = '<div class="etu-testimonial__rating" role="img" aria-label="' . $label . '">' . $stars . '</div>';
}

$quote_html = '' !== trim( $quote )
	? '<blockquote class="etu-testimonial__quote">' . wp_kses( $quote, $quote_allowed ) . '</blockquote>'
	: '';

$avatar_html = '';
if ( '' !== $avatar_url ) {
	$avatar_html = '<img class="etu-testimonial__avatar" src="' . esc_url( $avatar_url ) . '" alt="" loading="lazy" decoding="async" />';
}
$name_html = '' !== trim( $name ) ? '<span class="etu-testimonial__name">' . wp_kses( $name, $inline_allowed ) . '</span>' : '';
$role_html = '' !== trim( $role ) ? '<span class="etu-testimonial__role">' . wp_kses( $role, $inline_allowed ) . '</span>' : '';

$author_html = '';
if ( '' !== $avatar_html || '' !== $name_html || '' !== $role_html ) {
	$author_html  = '<div class="etu-testimonial__author">' . $avatar_html;
	if ( '' !== $name_html || '' !== $role_html ) {
		$author_html .= '<div class="etu-testimonial__author-text">' . $name_html . $role_html . '</div>';
	}
	$author_html .= '</div>';
}

\Etublocks\CSS_Generator::inject( 'etu/testimonial', $generated_css );
echo wp_kses( ( '<div ' . $wrapper_attributes . '>'
	. $rating_html
	. $quote_html
	. $author_html
	. '</div>' ), \Etublocks\Rendering::allowed() );
