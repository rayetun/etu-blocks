<?php
/**
 * Steps block — server render.
 *
 * A semantic ordered list (<ol>) so the sequence is conveyed to assistive tech.
 * The visible number badges are decorative (aria-hidden); the list order carries
 * the meaning. Styling comes from the schema generator + preset class.
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
			return is_array( $item ) && '' !== trim( (string) ( $item['title'] ?? '' ) );
		}
	)
);

if ( empty( $items ) ) {
	return;
}

$title_allowed = array( 'strong' => array(), 'em' => array(), 'b' => array(), 'i' => array() );
$desc_allowed  = $title_allowed + array( 'br' => array(), 'a' => array( 'href' => true, 'target' => true, 'rel' => true ) );

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$preset = (string) ( $attributes['preset'] ?? 'connected' );
if ( ! in_array( $preset, array( 'connected', 'numbered', 'minimal' ), true ) ) {
	$preset = 'connected';
}

$scope         = '.etu-steps-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/steps', $attributes, $scope );

$classes = array( 'etu-steps', 'etu-steps--preset-' . $preset, 'etu-steps-' . $block_id );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

\Etublocks\CSS_Generator::inject( 'etu/steps', $generated_css );
echo wp_kses( '<ol ' . $wrapper_attributes . '>', \Etublocks\Rendering::allowed() );
foreach ( $items as $index => $item ) {
	$title = (string) ( $item['title'] ?? '' );
	$desc  = (string) ( $item['description'] ?? '' );
	echo '<li class="etu-steps__item">';
	echo '<span class="etu-steps__number" aria-hidden="true">' . esc_html( (string) ( $index + 1 ) ) . '</span>';
	echo '<div class="etu-steps__content">';
	echo '<p class="etu-steps__title">' . wp_kses( $title, $title_allowed ) . '</p>';
	if ( '' !== trim( $desc ) ) {
		echo '<div class="etu-steps__description">' . wp_kses( $desc, $desc_allowed ) . '</div>';
	}
	echo '</div>';
	echo '</li>';
}
echo '</ol>';
