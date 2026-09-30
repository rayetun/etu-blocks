<?php
/**
 * FAQ block — server render.
 *
 * Renders an accessible accordion: each question is a <button> inside a heading
 * (aria-expanded + aria-controls), each answer a labelled region that is hidden
 * when collapsed. Open/close state is driven by the Interactivity API store in
 * view.js — the ARIA state is bound to the same store, so it can never drift from
 * what is shown. Answers remain in the DOM (and in the optional FAQPage schema),
 * so search engines and assistive tech can read them regardless of open state.
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

// Drop empty rows.
$items = array_values(
	array_filter(
		$items,
		static function ( $item ) {
			return is_array( $item ) && '' !== trim( (string) ( $item['question'] ?? '' ) );
		}
	)
);

if ( empty( $items ) ) {
	return;
}

$allow_multiple = ! empty( $attributes['allowMultipleOpen'] );
$show_schema    = ! isset( $attributes['showSchema'] ) || ! empty( $attributes['showSchema'] );

$question_allowed = array(
	'strong' => array(),
	'em'     => array(),
	'b'      => array(),
	'i'      => array(),
	'br'     => array(),
);

$uid = wp_unique_id( 'etu-faq-' );

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( $uid ), 0, 10 );
}

$preset = (string) ( $attributes['preset'] ?? 'boxed' );
if ( ! in_array( $preset, array( 'boxed', 'line', 'filled' ), true ) ) {
	$preset = 'boxed';
}

$scope         = '.etu-faq-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/faq', $attributes, $scope );

$classes = array( 'etu-faq', 'etu-faq--preset-' . $preset, 'etu-faq-' . $block_id );

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );
$wrapper_context    = wp_interactivity_data_wp_context(
	array(
		'activeId'      => '',
		'allowMultiple' => $allow_multiple,
	)
);

\Etublocks\CSS_Generator::inject( 'etu/faq', $generated_css );
echo wp_kses( '<div ' . $wrapper_attributes . ' data-wp-interactive="etu/faq" ' . $wrapper_context . '>', \Etublocks\Rendering::allowed() );

foreach ( $items as $index => $item ) {
	$question = (string) ( $item['question'] ?? '' );
	$answer   = (string) ( $item['answer'] ?? '' );

	$item_id  = $uid . '-' . (int) $index;
	$btn_id   = $item_id . '-q';
	$panel_id = $item_id . '-a';

	$item_context = wp_interactivity_data_wp_context(
		array(
			'id'   => $item_id,
			'open' => false,
		)
	);

	echo wp_kses( '<div class="etu-faq__item" ' . $item_context . ' data-wp-watch="callbacks.syncOpen">', \Etublocks\Rendering::allowed() );

	echo '<h3 class="etu-faq__heading">';
	echo( '<button type="button" class="etu-faq__trigger" id="' . esc_attr( $btn_id ) . '"'
		. ' aria-controls="' . esc_attr( $panel_id ) . '"'
		. ' data-wp-on--click="actions.toggle"'
		. ' data-wp-bind--aria-expanded="context.open">' );
	echo '<span class="etu-faq__question-text">' . wp_kses( $question, $question_allowed ) . '</span>';
	echo '<span class="etu-faq__icon" aria-hidden="true"></span>';
	echo '</button>';
	echo '</h3>';

	echo( '<div class="etu-faq__panel" id="' . esc_attr( $panel_id ) . '" role="region"'
		. ' aria-labelledby="' . esc_attr( $btn_id ) . '"'
		. ' data-wp-bind--hidden="!context.open">' );
	echo '<div class="etu-faq__answer">' . wp_kses_post( $answer ) . '</div>';
	echo '</div>';

	echo '</div>';
}

echo '</div>';

// FAQPage structured data — plain-text mirror of the visible Q&A.
if ( $show_schema ) {
	$entities = array();
	foreach ( $items as $item ) {
		$q = wp_strip_all_tags( (string) ( $item['question'] ?? '' ) );
		$a = wp_strip_all_tags( (string) ( $item['answer'] ?? '' ) );
		if ( '' === $q || '' === $a ) {
			continue;
		}
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => $q,
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $a,
			),
		);
	}

	if ( ! empty( $entities ) ) {
		$schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $entities,
		);
		// wp_json_encode escapes slashes by default, so "</script>" cannot break out.
		echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
	}
}
