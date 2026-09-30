<?php
/**
 * Timeline block — server render.
 *
 * A semantic ordered list of dated entries. Markers and the connecting line are
 * decorative; the list order and text carry the meaning.
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

$items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();
$items = array_values(
	array_filter(
		$items,
		static function ( $item ) {
			return is_array( $item ) && ( '' !== trim( (string) ( $item['title'] ?? '' ) ) || '' !== trim( (string) ( $item['date'] ?? '' ) ) );
		}
	)
);

if ( empty( $items ) ) {
	return;
}

$inline_allowed = array( 'strong' => array(), 'em' => array(), 'b' => array(), 'i' => array() );
$desc_allowed   = $inline_allowed + array( 'br' => array(), 'a' => array( 'href' => true, 'target' => true, 'rel' => true ) );

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$preset = (string) ( $attributes['preset'] ?? 'line' );
if ( ! in_array( $preset, array( 'line', 'card', 'minimal' ), true ) ) {
	$preset = 'line';
}

$scope         = '.etu-timeline-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/timeline', $attributes, $scope );

$classes = array( 'etu-timeline', 'etu-timeline--preset-' . $preset, 'etu-timeline-' . $block_id );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

\Etublocks\CSS_Generator::inject( 'etu/timeline', $generated_css );
echo wp_kses( '<ol ' . $wrapper_attributes . '>', \Etublocks\Rendering::allowed() );
foreach ( $items as $item ) {
	$date  = (string) ( $item['date'] ?? '' );
	$title = (string) ( $item['title'] ?? '' );
	$desc  = (string) ( $item['description'] ?? '' );
	echo '<li class="etu-timeline__item">';
	echo '<span class="etu-timeline__marker" aria-hidden="true"></span>';
	echo '<div class="etu-timeline__content">';
	if ( '' !== trim( $date ) ) {
		echo '<span class="etu-timeline__date">' . wp_kses( $date, $inline_allowed ) . '</span>';
	}
	if ( '' !== trim( $title ) ) {
		echo '<p class="etu-timeline__title">' . wp_kses( $title, $inline_allowed ) . '</p>';
	}
	if ( '' !== trim( $desc ) ) {
		echo '<div class="etu-timeline__description">' . wp_kses( $desc, $desc_allowed ) . '</div>';
	}
	echo '</div>';
	echo '</li>';
}
echo '</ol>';
