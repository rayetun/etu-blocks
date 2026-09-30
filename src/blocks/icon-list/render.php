<?php
/**
 * Icon List block — server render.
 *
 * A semantic list (<ul>) of items, each with a decorative icon and text. Styling
 * comes from the schema-driven generator + preset/column classes. Icons are
 * aria-hidden; text is escaped.
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
			return is_array( $item ) && '' !== trim( (string) ( $item['text'] ?? '' ) );
		}
	)
);

if ( empty( $items ) ) {
	return;
}

$inline_allowed = array(
	'strong' => array(),
	'em'     => array(),
	'b'      => array(),
	'i'      => array(),
	'br'     => array(),
	'a'      => array( 'href' => true, 'target' => true, 'rel' => true ),
);

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$preset = (string) ( $attributes['preset'] ?? 'checklist' );
if ( ! in_array( $preset, array( 'checklist', 'plain', 'boxed' ), true ) ) {
	$preset = 'checklist';
}

$columns = (string) ( $attributes['columns'] ?? '1' );
if ( ! in_array( $columns, array( '1', '2', '3' ), true ) ) {
	$columns = '1';
}

$scope         = '.etu-icon-list-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/icon-list', $attributes, $scope );

$classes = array(
	'etu-icon-list',
	'etu-icon-list--preset-' . $preset,
	'etu-icon-list--cols-' . $columns,
	'etu-icon-list-' . $block_id,
);

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

\Etublocks\CSS_Generator::inject( 'etu/icon-list', $generated_css );
echo wp_kses( '<ul ' . $wrapper_attributes . ' role="list">', \Etublocks\Rendering::allowed() );
foreach ( $items as $item ) {
	$icon = (string) ( $item['icon'] ?? '' );
	$text = (string) ( $item['text'] ?? '' );
	echo '<li class="etu-icon-list__item">';
	echo '<span class="etu-icon-list__icon" aria-hidden="true">' . esc_html( '' !== trim( $icon ) ? $icon : '✓' ) . '</span>';
	echo '<span class="etu-icon-list__text">' . wp_kses( $text, $inline_allowed ) . '</span>';
	echo '</li>';
}
echo '</ul>';
