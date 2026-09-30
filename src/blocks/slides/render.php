<?php
/**
 * Slides / Carousel block — server render.
 *
 * Wraps each inner "slide" block in a labelled slide group and adds the carousel
 * controls. Behaviour (scroll, index tracking, inert off-screen slides, autoplay)
 * is handled by the Interactivity store; the track is a native scroll-snap
 * container so touch and keyboard work with no JS. The APG "carousel" pattern:
 * region + aria-roledescription, button arrows/dots, a visible Pause/Play, and
 * polite announcements.
 *
 * @package Etublocks
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused — inner slides are rendered per-item below.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Included within the block render callback's method scope; these are local, not global.

$slides = isset( $block->inner_blocks ) ? $block->inner_blocks : array();
$total  = is_countable( $slides ) ? count( $slides ) : 0;
if ( 0 === $total ) {
	return;
}

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$show_arrows = ! isset( $attributes['showArrows'] ) || ! empty( $attributes['showArrows'] );
$show_dots   = ! isset( $attributes['showDots'] ) || ! empty( $attributes['showDots'] );
$autoplay    = ! empty( $attributes['autoplay'] );
$loop        = ! isset( $attributes['loop'] ) || ! empty( $attributes['loop'] );
$pause_hover = ! isset( $attributes['pauseOnHover'] ) || ! empty( $attributes['pauseOnHover'] );

$arrow_pos = (string) ( $attributes['arrowPosition'] ?? 'edge' );
if ( ! in_array( $arrow_pos, array( 'edge', 'outside', 'bottom' ), true ) ) {
	$arrow_pos = 'edge';
}

$speed    = (int) ( $attributes['autoplaySpeed'] ?? 5 );
$speed    = max( 1, min( 15, $speed ) );
$interval = $speed * 1000;

// Responsive "slides per view" as a scoped CSS var.
$per    = is_array( $attributes['perView'] ?? null ) ? $attributes['perView'] : array();
$per_d  = max( 1, min( 6, (int) ( $per['desktop'] ?? 3 ) ) );
$per_t  = max( 1, min( 6, (int) ( $per['tablet'] ?? $per_d ) ) );
$per_m  = max( 1, min( 6, (int) ( $per['mobile'] ?? 1 ) ) );

$scope         = '.etu-slides-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/slides', $attributes, $scope );
$generated_css .= $scope . '{--etu-slides-per:' . $per_d . ';}';
$generated_css .= '@media (max-width:1024px){' . $scope . '{--etu-slides-per:' . $per_t . ';}}';
$generated_css .= '@media (max-width:767px){' . $scope . '{--etu-slides-per:' . $per_m . ';}}';

$aria_label = trim( (string) ( $attributes['ariaLabel'] ?? '' ) );
if ( '' === $aria_label ) {
	$aria_label = __( 'Carousel', 'etu-blocks' );
}

$classes = array( 'etu-slides', 'etu-slides-' . $block_id );
if ( $show_arrows ) {
	$classes[] = 'etu-slides--has-arrows';
	$classes[] = 'etu-slides--arrows-' . $arrow_pos;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

$context = wp_interactivity_data_wp_context(
	array(
		'current'        => 0,
		'total'          => $total,
		'autoplay'       => $autoplay,
		'paused'         => false,
		'interval'       => $interval,
		'loop'           => $loop,
		'pauseOnHover'   => $pause_hover,
		/* translators: 1: current slide number, 2: total slides. */
		'statusTemplate' => __( 'Slide %1$d of %2$d', 'etu-blocks' ),
		'playText'       => __( 'Play', 'etu-blocks' ),
		'pauseText'      => __( 'Pause', 'etu-blocks' ),
	)
);

$chevron_prev = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M15 18l-6-6 6-6"/></svg>';
$chevron_next = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M9 18l6-6-6-6"/></svg>';
$icon_pause   = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M6 5h4v14H6zM14 5h4v14h-4z"/></svg>';
$icon_play    = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M8 5v14l11-7z"/></svg>';

$arrow_prev_btn = '<button type="button" class="etu-slides__arrow etu-slides__arrow--prev" data-wp-on--click="actions.prev" aria-label="' . esc_attr__( 'Previous slide', 'etu-blocks' ) . '">' . $chevron_prev . '</button>';
$arrow_next_btn = '<button type="button" class="etu-slides__arrow etu-slides__arrow--next" data-wp-on--click="actions.next" aria-label="' . esc_attr__( 'Next slide', 'etu-blocks' ) . '">' . $chevron_next . '</button>';

\Etublocks\CSS_Generator::inject( 'etu/slides', $generated_css );

$open = '<div ' . $wrapper_attributes
	. ' data-wp-interactive="etu/slides" ' . $context
	. ' data-wp-init="callbacks.init"'
	. ' role="region" aria-roledescription="' . esc_attr__( 'carousel', 'etu-blocks' ) . '"'
	. ' aria-label="' . esc_attr( $aria_label ) . '"';
if ( $autoplay && $pause_hover ) {
	$open .= ' data-wp-on--mouseenter="actions.hoverPause" data-wp-on--mouseleave="actions.hoverResume"'
		. ' data-wp-on--focusin="actions.hoverPause" data-wp-on--focusout="actions.hoverResume"';
}
$open .= '>';
echo wp_kses( $open, \Etublocks\Rendering::allowed() );

echo '<div class="etu-slides__viewport">';
echo '<ul class="etu-slides__track" tabindex="0" aria-label="' . esc_attr__( 'Slides', 'etu-blocks' ) . '">';

$i = 0;
foreach ( $slides as $slide ) {
	++$i;
	/* translators: 1: current slide number, 2: total slides. */
	$slide_label = sprintf( __( '%1$d of %2$d', 'etu-blocks' ), $i, $total );
	echo '<li class="etu-slides__slide" role="group" aria-roledescription="' . esc_attr__( 'slide', 'etu-blocks' ) . '" aria-label="' . esc_attr( $slide_label ) . '">';
	echo wp_kses( $slide->render(), \Etublocks\Rendering::allowed() );
	echo '</li>';
}

echo '</ul>';

// Over-edges / Outside arrows overlay the track; Bottom arrows go in the row below.
if ( $show_arrows && 'bottom' !== $arrow_pos ) {
	echo wp_kses( $arrow_prev_btn, \Etublocks\Rendering::allowed() );
	echo wp_kses( $arrow_next_btn, \Etublocks\Rendering::allowed() );
}

echo '</div>';

$bottom_arrows    = $show_arrows && 'bottom' === $arrow_pos;
$has_controls_row = $show_dots || $autoplay || $bottom_arrows;
if ( $has_controls_row ) {
	echo '<div class="etu-slides__controls">';

	if ( $bottom_arrows ) {
		echo wp_kses( $arrow_prev_btn, \Etublocks\Rendering::allowed() );
	}

	if ( $autoplay ) {
		echo wp_kses( ( '<button type="button" class="etu-slides__play" data-wp-on--click="actions.togglePlay" data-wp-bind--aria-label="state.playLabel" data-wp-bind--aria-pressed="state.isPaused">'
			. '<span class="etu-slides__play-icon" data-wp-bind--hidden="context.paused">' . $icon_pause . '</span>'
			. '<span class="etu-slides__play-icon" data-wp-bind--hidden="!context.paused">' . $icon_play . '</span>'
			. '</button>' ), \Etublocks\Rendering::allowed() );
	}

	if ( $show_dots ) {
		echo '<div class="etu-slides__dots" role="group" aria-label="' . esc_attr__( 'Choose slide to display', 'etu-blocks' ) . '">';
		for ( $d = 0; $d < $total; $d++ ) {
			/* translators: %d: slide number. */
			$dot_label = sprintf( __( 'Go to slide %d', 'etu-blocks' ), $d + 1 );
			echo( '<button type="button" class="etu-slides__dot" data-wp-context=\'{"index":' . (int) $d . '}\' '
				. 'data-wp-on--click="actions.goTo" data-wp-class--is-active="state.isCurrentDot" '
				. 'data-wp-bind--aria-current="state.isCurrentDot" aria-label="' . esc_attr( $dot_label ) . '"></button>' );
		}
		echo '</div>';
	}

	if ( $bottom_arrows ) {
		echo wp_kses( $arrow_next_btn, \Etublocks\Rendering::allowed() );
	}

	echo '</div>';
}

echo '<span class="etu-slides__status screen-reader-text" data-wp-bind--aria-live="state.liveMode" data-wp-text="state.statusText"></span>';

echo '</div>';
