<?php
/**
 * Countdown block — server render.
 *
 * Counts down to a fixed instant (author's date in the site timezone → an absolute
 * epoch shared by every visitor) or an evergreen per-visitor duration (the target is
 * resolved on the client and remembered in localStorage). The remaining time is
 * rendered server-side so it's correct with no JS; the store just keeps it ticking.
 *
 * Accessibility: the ticking digits are aria-hidden (they'd spam a screen reader);
 * the timer carries a descriptive label, and a single polite live region announces
 * completion once.
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

$mode = 'evergreen' === ( $attributes['mode'] ?? 'date' ) ? 'evergreen' : 'date';
$on_complete = 'hide' === ( $attributes['onComplete'] ?? 'message' ) ? 'hide' : 'message';

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$now = time();

// Split a positive number of seconds into padded day/hour/minute/second strings.
$split = static function ( $seconds ) {
	$seconds = max( 0, (int) $seconds );
	$d = (int) floor( $seconds / 86400 );
	$seconds -= $d * 86400;
	$h = (int) floor( $seconds / 3600 );
	$seconds -= $h * 3600;
	$m = (int) floor( $seconds / 60 );
	$s = $seconds - $m * 60;
	return array(
		'd' => str_pad( (string) $d, 2, '0', STR_PAD_LEFT ),
		'h' => str_pad( (string) $h, 2, '0', STR_PAD_LEFT ),
		'm' => str_pad( (string) $m, 2, '0', STR_PAD_LEFT ),
		's' => str_pad( (string) $s, 2, '0', STR_PAD_LEFT ),
	);
};

$target_ms    = 0;
$duration_ms  = 0;
$completed    = false;
$aria_label   = '';

if ( 'date' === $mode ) {
	$raw = trim( (string) ( $attributes['targetDate'] ?? '' ) );
	if ( '' === $raw ) {
		return; // No target set.
	}
	try {
		$dt = new DateTimeImmutable( $raw, wp_timezone() );
	} catch ( Exception $e ) {
		return; // Unparseable date.
	}
	$ts        = $dt->getTimestamp();
	$target_ms = $ts * 1000;
	$remaining = $ts - $now;
	$completed = $remaining <= 0;
	$parts     = $split( $remaining );
	/* translators: %s: the target date/time. */
	$aria_label = sprintf( __( 'Time remaining until %s', 'etu-blocks' ), wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $ts ) );
} else {
	$ev_days    = max( 0, (int) ( $attributes['evDays'] ?? 0 ) );
	$ev_hours   = max( 0, (int) ( $attributes['evHours'] ?? 0 ) );
	$ev_minutes = max( 0, (int) ( $attributes['evMinutes'] ?? 0 ) );
	$duration   = $ev_days * 86400 + $ev_hours * 3600 + $ev_minutes * 60;
	if ( $duration <= 0 ) {
		return; // No duration set.
	}
	$duration_ms = $duration * 1000;
	$parts       = $split( $duration );
	$aria_label  = __( 'Limited-time countdown', 'etu-blocks' );
}

// A finished fixed-date countdown set to "hide" renders nothing.
if ( $completed && 'hide' === $on_complete ) {
	return;
}

$preset = 'minimal' === ( $attributes['preset'] ?? 'cards' ) ? 'minimal' : 'cards';
$show_labels  = ! isset( $attributes['showLabels'] ) || ! empty( $attributes['showLabels'] );
$hide_seconds = ! empty( $attributes['hideSeconds'] );

$labels = array(
	'd' => __( 'Days', 'etu-blocks' ),
	'h' => __( 'Hours', 'etu-blocks' ),
	'm' => __( 'Minutes', 'etu-blocks' ),
	's' => __( 'Seconds', 'etu-blocks' ),
);

$completed_text = trim( (string) ( $attributes['completedText'] ?? '' ) );
if ( '' === $completed_text ) {
	$completed_text = __( 'The countdown has ended.', 'etu-blocks' );
}

$inline_allowed = array( 'strong' => array(), 'em' => array(), 'b' => array(), 'i' => array(), 'br' => array() );

$scope         = '.etu-countdown-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/countdown', $attributes, $scope );

$classes = array(
	'etu-countdown',
	'etu-countdown--preset-' . $preset,
	'etu-countdown-' . $block_id,
);

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );
$context            = wp_interactivity_data_wp_context(
	array(
		'mode'        => $mode,
		'targetMs'    => $target_ms,
		'durationMs'  => $duration_ms,
		'storageKey'  => 'etu-cd-' . $block_id,
		'completed'   => $completed,
		'cDays'       => $parts['d'],
		'cHours'      => $parts['h'],
		'cMinutes'    => $parts['m'],
		'cSeconds'    => $parts['s'],
		'liveText'    => '',
	)
);

$units = array( 'd', 'h', 'm' );
if ( ! $hide_seconds ) {
	$units[] = 's';
}
$value_ctx = array( 'd' => 'context.cDays', 'h' => 'context.cHours', 'm' => 'context.cMinutes', 's' => 'context.cSeconds' );

\Etublocks\CSS_Generator::inject( 'etu/countdown', $generated_css );

echo wp_kses( '<div ' . $wrapper_attributes . ' data-wp-interactive="etu/countdown" ' . $context . ' data-wp-init="callbacks.start" data-completed-text="' . esc_attr( $completed_text ) . '">', \Etublocks\Rendering::allowed() );

echo '<div class="etu-countdown__timer" role="timer" aria-label="' . esc_attr( $aria_label ) . '" data-wp-bind--hidden="context.completed">';
foreach ( $units as $u ) {
	echo '<div class="etu-countdown__unit">';
	echo '<span class="etu-countdown__value" aria-hidden="true" data-wp-text="' . esc_attr( $value_ctx[ $u ] ) . '">' . esc_html( $parts[ $u ] ) . '</span>';
	if ( $show_labels ) {
		echo '<span class="etu-countdown__label" aria-hidden="true">' . esc_html( $labels[ $u ] ) . '</span>';
	}
	echo '</div>';
}
echo '</div>';

if ( 'message' === $on_complete ) {
	echo '<div class="etu-countdown__completed" data-wp-bind--hidden="!context.completed">' . wp_kses( $completed_text, $inline_allowed ) . '</div>';
}

echo '<span class="etu-countdown__live" aria-live="polite" data-wp-text="context.liveText"></span>';

echo '</div>';
