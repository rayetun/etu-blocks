<?php
/**
 * Social Share block — server render.
 *
 * Network buttons are plain anchors (work with JS off); Copy link and Native
 * share are buttons driven by the Interactivity store. Icons are decorative;
 * every control carries an accessible name, and a polite live region announces
 * the copy result.
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

require_once ETUBLOCKS_DIR . 'includes/blocks/social-share/networks.php';

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- This template is included within the block render callback's method scope; the variables below are local to that scope, not global.

$catalogue = etublocks_social_networks();

$requested = isset( $attributes['networks'] ) && is_array( $attributes['networks'] ) ? $attributes['networks'] : array();
// Keep the catalogue's canonical order; drop anything unknown.
$selected = array_values( array_filter( array_keys( $catalogue ), static function ( $key ) use ( $requested ) {
	return in_array( $key, $requested, true );
} ) );

if ( empty( $selected ) ) {
	return;
}

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$preset = (string) ( $attributes['preset'] ?? 'solid' );
if ( ! in_array( $preset, array( 'solid', 'outline', 'minimal' ), true ) ) {
	$preset = 'solid';
}

$shape = (string) ( $attributes['buttonShape'] ?? 'rounded' );
if ( ! in_array( $shape, array( 'rounded', 'pill', 'circle' ), true ) ) {
	$shape = 'rounded';
}

$brand       = ! empty( $attributes['useBrandColors'] );
$new_tab      = ! isset( $attributes['openInNewTab'] ) || ! empty( $attributes['openInNewTab'] );
$show_labels = ! isset( $attributes['showLabels'] ) || ! empty( $attributes['showLabels'] );
$labels      = $show_labels && 'circle' !== $shape;

// What to share: an explicit URL/text, else the current page.
$qid        = get_queried_object_id();
$share_url  = trim( (string) ( $attributes['shareUrl'] ?? '' ) );
if ( '' === $share_url ) {
	$share_url = $qid ? (string) get_permalink( $qid ) : home_url( '/' );
}
$share_text = trim( (string) ( $attributes['shareText'] ?? '' ) );
if ( '' === $share_text ) {
	$share_text = $qid ? wp_strip_all_tags( (string) get_the_title( $qid ) ) : (string) get_bloginfo( 'name' );
}

$enc_url  = rawurlencode( esc_url_raw( $share_url ) );
$enc_text = rawurlencode( $share_text );

$has_js = in_array( 'copy', $selected, true ) || in_array( 'native', $selected, true );

$scope         = '.etu-social-share-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/social-share', $attributes, $scope );

$classes = array(
	'etu-social-share',
	'etu-social-share--preset-' . $preset,
	'etu-social-share--shape-' . $shape,
	'etu-social-share-' . $block_id,
);
if ( $brand ) {
	$classes[] = 'etu-social-share--brand';
}
if ( ! $labels ) {
	$classes[] = 'etu-social-share--icon-only';
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

$svg_open = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">';

\Etublocks\CSS_Generator::inject( 'etu/social-share', $generated_css );

$open = '<div ' . $wrapper_attributes;
if ( $has_js ) {
	$context = wp_interactivity_data_wp_context(
		array(
			'copied'     => false,
			'canShare'   => false,
			'url'        => $share_url,
			'title'      => $share_text,
			'copiedText' => __( 'Link copied to clipboard', 'etu-blocks' ),
		)
	);
	$open .= ' data-wp-interactive="etu/social-share" ' . $context . ' data-wp-init="callbacks.init"';
}
$open .= '>';
echo wp_kses( $open, \Etublocks\Rendering::allowed() );

echo '<ul class="etu-social-share__list" role="list">';

foreach ( $selected as $key ) {
	$net   = $catalogue[ $key ];
	$type  = $net['type'];
	$label = $net['label'];

	$inner = '<span class="etu-social-share__icon">' . $svg_open . $net['svg'] . '</svg></span>';
	if ( $labels ) {
		$inner .= '<span class="etu-social-share__label">' . esc_html( $label ) . '</span>';
	}

	echo '<li class="etu-social-share__item">';

	if ( 'copy' === $type ) {
		echo wp_kses( ( '<button type="button" class="etu-social-share__btn etu-social-share__btn--copy" '
			. 'data-wp-on--click="actions.copy" data-wp-class--is-copied="context.copied" '
			. 'aria-label="' . esc_attr__( 'Copy link', 'etu-blocks' ) . '">'
			. $inner
			. '<span class="etu-social-share__tip" aria-hidden="true">' . esc_html__( 'Copied!', 'etu-blocks' ) . '</span>'
			. '</button>' ), \Etublocks\Rendering::allowed() );
	} elseif ( 'native' === $type ) {
		echo wp_kses( ( '<button type="button" class="etu-social-share__btn etu-social-share__btn--native" '
			. 'data-wp-on--click="actions.nativeShare" data-wp-bind--hidden="!context.canShare" '
			. 'aria-label="' . esc_attr__( 'Share…', 'etu-blocks' ) . '">'
			. $inner
			. '</button>' ), \Etublocks\Rendering::allowed() );
	} else {
		$href = esc_url( sprintf( $net['url'], $enc_url, $enc_text ) );
		if ( 'email' === $key ) {
			$aria = esc_attr__( 'Share by email', 'etu-blocks' );
		} else {
			/* translators: %s: social network name. */
			$aria = esc_attr( sprintf( __( 'Share on %s', 'etu-blocks' ), $label ) );
		}

		$rel_target = '';
		if ( $new_tab && 'email' !== $key ) {
			$rel_target = ' target="_blank" rel="noopener noreferrer"';
			$aria      .= ' ' . esc_attr__( '(opens in a new tab)', 'etu-blocks' );
		}

		echo wp_kses( ( '<a class="etu-social-share__btn etu-social-share__btn--' . esc_attr( $key ) . '" '
			. 'href="' . $href . '"' . $rel_target . ' aria-label="' . $aria . '">'
			. $inner
			. '</a>' ), \Etublocks\Rendering::allowed() );
	}

	echo '</li>';
}

echo '</ul>';

if ( $has_js ) {
	echo '<span class="etu-social-share__status screen-reader-text" role="status" aria-live="polite" data-wp-text="state.statusText"></span>';
}

echo '</div>';
