<?php
/**
 * Before / After block — server render.
 *
 * Two images share one frame: the "after" image is the base and the "before"
 * image sits on top, clipped to the reveal position (so left/top reads as before,
 * right/bottom as after). The reveal is driven by a real <input type="range">
 * (keyboard + assistive-tech operable) — the visible divider and handle are a
 * decorative layer over it. All reactive styles bind to
 * the Interactivity *context* (resolved server-side) so the block renders at its
 * start position with JS disabled, then updates live on the front end.
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

$before = is_array( $attributes['beforeImage'] ?? null ) ? $attributes['beforeImage'] : array();
$after  = is_array( $attributes['afterImage'] ?? null ) ? $attributes['afterImage'] : array();

$before_url = isset( $before['url'] ) ? esc_url_raw( (string) $before['url'] ) : '';
$after_url  = isset( $after['url'] ) ? esc_url_raw( (string) $after['url'] ) : '';

// A comparison needs both images; otherwise there is nothing to render.
if ( '' === $before_url || '' === $after_url ) {
	return;
}

$before_label = (string) ( $attributes['beforeLabel'] ?? '' );
$after_label  = (string) ( $attributes['afterLabel'] ?? '' );
$show_labels  = ! isset( $attributes['showLabels'] ) || ! empty( $attributes['showLabels'] );

$before_alt = isset( $before['alt'] ) && '' !== trim( (string) $before['alt'] ) ? (string) $before['alt'] : $before_label;
$after_alt  = isset( $after['alt'] ) && '' !== trim( (string) $after['alt'] ) ? (string) $after['alt'] : $after_label;

$position = (int) round( (float) ( $attributes['startPosition'] ?? 50 ) );
$position = max( 0, min( 100, $position ) );

$orientation = (string) ( $attributes['orientation'] ?? 'horizontal' );
if ( ! in_array( $orientation, array( 'horizontal', 'vertical' ), true ) ) {
	$orientation = 'horizontal';
}
$is_vertical = 'vertical' === $orientation;

$ratio = (string) ( $attributes['aspectRatio'] ?? '16x9' );
if ( ! in_array( $ratio, array( '16x9', '3x2', '4x3', '1x1', '21x9' ), true ) ) {
	$ratio = '16x9';
}

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

// The "after" image is the base (fills the frame); the "before" image is the
// clipped overlay, shown from the start edge (left / top) up to the position, so
// reading order is intuitive: before on the left/top, after on the right/bottom.
$before_clip = $is_vertical
	? 'inset(0 0 ' . ( 100 - $position ) . '% 0)'
	: 'inset(0 ' . ( 100 - $position ) . '% 0 0)';
$divider_pos = $position . '%';

$scope         = '.etu-before-after-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/before-after', $attributes, $scope );

$classes = array(
	'etu-before-after',
	'etu-before-after--' . $orientation,
	'etu-before-after--ar-' . $ratio,
	'etu-before-after-' . $block_id,
);

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );
$wrapper_context    = wp_interactivity_data_wp_context(
	array(
		'position'    => $position,
		'orientation' => $orientation,
		'beforeClip'  => $before_clip,
		'dividerPos'  => $divider_pos,
		'valueText'   => $position . '%',
	)
);

if ( '' !== trim( $before_label ) && '' !== trim( $after_label ) ) {
	/* translators: 1: before label, 2: after label. */
	$range_label = sprintf( __( '%1$s / %2$s comparison — drag to reveal', 'etu-blocks' ), $before_label, $after_label );
} else {
	$range_label = __( 'Before / after comparison — drag to reveal', 'etu-blocks' );
}

$divider_side = $is_vertical ? 'top' : 'left';

\Etublocks\CSS_Generator::inject( 'etu/before-after', $generated_css );

echo wp_kses( '<div ' . $wrapper_attributes . ' data-wp-interactive="etu/before-after" ' . $wrapper_context . '>', \Etublocks\Rendering::allowed() );
echo '<div class="etu-before-after__frame">';

// Base (after) image — fills the whole frame.
echo '<img class="etu-before-after__image etu-before-after__image--after" src="' . esc_url( $after_url ) . '" alt="' . esc_attr( $after_alt ) . '" loading="lazy" decoding="async" />';

// Range input (transparent, covers the whole frame so a drag anywhere works).
echo( '<input type="range" class="etu-before-after__range" min="0" max="100" step="1" value="' . esc_attr( (string) $position ) . '"'
	. ' aria-label="' . esc_attr( $range_label ) . '"'
	. ' data-wp-on--input="actions.slide" data-wp-bind--value="context.position" data-wp-bind--aria-valuetext="context.valueText" />' );

// Overlay (before) image, clipped to the reveal position.
echo '<div class="etu-before-after__before" data-wp-style--clip-path="context.beforeClip" style="clip-path:' . esc_attr( $before_clip ) . ';">';
echo '<img class="etu-before-after__image etu-before-after__image--before" src="' . esc_url( $before_url ) . '" alt="' . esc_attr( $before_alt ) . '" loading="lazy" decoding="async" />';
echo '</div>';

if ( $show_labels ) {
	if ( '' !== trim( $before_label ) ) {
		echo '<span class="etu-before-after__label etu-before-after__label--before" aria-hidden="true">' . esc_html( $before_label ) . '</span>';
	}
	if ( '' !== trim( $after_label ) ) {
		echo '<span class="etu-before-after__label etu-before-after__label--after" aria-hidden="true">' . esc_html( $after_label ) . '</span>';
	}
}

// Decorative divider + handle, positioned by the context.
echo( '<div class="etu-before-after__divider" aria-hidden="true"'
	. ' data-wp-style--' . esc_attr( $divider_side ) . '="context.dividerPos"'
	. ' style="' . esc_attr( $divider_side ) . ':' . esc_attr( $divider_pos ) . ';">' );
echo '<span class="etu-before-after__handle"></span>';
echo '</div>';

echo '</div>'; // frame
echo '</div>'; // wrapper
