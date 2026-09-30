<?php
/**
 * Google Map block — server render.
 *
 * Uses Google's keyless Maps embed (no API key to configure or leak). By default the
 * map is a privacy-first facade: a styled placeholder renders on page view and the
 * Google iframe loads only when the visitor clicks "Load map" — so no third-party
 * request or cookie is made until they opt in. A premium info card shows the title,
 * address and a keyless "Get directions" deep link.
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

$query = trim( (string) ( $attributes['query'] ?? '' ) );
if ( '' === $query ) {
	return; // No location set.
}

$zoom = max( 1, min( 20, (int) ( $attributes['zoom'] ?? 14 ) ) );
$map_type = (string) ( $attributes['mapType'] ?? 'm' );
if ( ! in_array( $map_type, array( 'm', 'k', 'h', 'p' ), true ) ) {
	$map_type = 'm';
}
$ratio = (string) ( $attributes['aspectRatio'] ?? '16x9' );
if ( ! in_array( $ratio, array( '16x9', '4x3', '21x9', '1x1', 'tall' ), true ) ) {
	$ratio = '16x9';
}
$click_to_load = ! isset( $attributes['clickToLoad'] ) || ! empty( $attributes['clickToLoad'] );
$grayscale     = ! empty( $attributes['grayscale'] );
$show_info     = ! isset( $attributes['showInfoCard'] ) || ! empty( $attributes['showInfoCard'] );
$show_dir      = ! isset( $attributes['showDirections'] ) || ! empty( $attributes['showDirections'] );

$title   = trim( (string) ( $attributes['title'] ?? '' ) );
$address = trim( (string) ( $attributes['address'] ?? '' ) );
$location_label = '' !== $address ? $address : $query;

$locale = substr( get_locale(), 0, 2 );

// If the site owner has entered a Google Maps API key in Settings, use the
// official Embed API (higher quality, rate-limit safe). Otherwise fall back to
// the keyless embed that works for most sites.
$api_key = (string) \Etublocks\Admin\Settings::get( 'maps_api_key' );

if ( '' !== $api_key ) {
	$maptype_map = array( 'm' => 'roadmap', 'k' => 'satellite', 'h' => 'hybrid', 'p' => 'terrain' );
	$embed_url   = add_query_arg(
		array(
			'key'      => rawurlencode( $api_key ),
			'q'        => rawurlencode( $query ),
			'zoom'     => $zoom,
			'maptype'  => $maptype_map[ $map_type ] ?? 'roadmap',
			'language' => $locale,
		),
		'https://www.google.com/maps/embed/v1/place'
	);
} else {
	$embed_url = add_query_arg(
		array(
			'q'      => rawurlencode( $query ),
			'z'      => $zoom,
			't'      => $map_type,
			'hl'     => $locale,
			'output' => 'embed',
		),
		'https://maps.google.com/maps'
	);
}

$directions_url = add_query_arg(
	array(
		'api'         => '1',
		'destination' => rawurlencode( $query ),
	),
	'https://www.google.com/maps/dir/'
);

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

/* translators: %s: the location. */
$iframe_title = '' !== $title ? $title : sprintf( __( 'Map of %s', 'etu-blocks' ), $location_label );

$scope         = '.etu-map-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/map', $attributes, $scope );

$classes = array( 'etu-map', 'etu-map--ar-' . $ratio, 'etu-map-' . $block_id );
if ( $grayscale ) {
	$classes[] = 'is-grayscale';
}
if ( $click_to_load ) {
	$classes[] = 'etu-map--facade';
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

$icon_pin   = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>';
$icon_arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';

$has_info   = $show_info && ( '' !== $title || '' !== $address || $show_dir );
$info_inner = '';
if ( $has_info ) {
	if ( '' !== $title || '' !== $address ) {
		$info_inner .= '<div class="etu-map__info-body">';
		if ( '' !== $title ) {
			$info_inner .= '<p class="etu-map__info-title">' . esc_html( $title ) . '</p>';
		}
		if ( '' !== $address ) {
			$info_inner .= '<p class="etu-map__info-address">' . esc_html( $address ) . '</p>';
		}
		$info_inner .= '</div>';
	}
	if ( $show_dir ) {
		$info_inner .= '<a class="etu-map__directions" href="' . esc_url( $directions_url ) . '" target="_blank" rel="noopener noreferrer">'
			. '<span>' . esc_html__( 'Get directions', 'etu-blocks' ) . '</span>' . $icon_arrow . '</a>';
	}
}

\Etublocks\CSS_Generator::inject( 'etu/map', $generated_css );

if ( $click_to_load ) {
	$context = wp_interactivity_data_wp_context(
		array(
			'loaded'    => false,
			'embedUrl'  => $embed_url,
			'iframeSrc' => 'about:blank',
		)
	);
	echo wp_kses( '<div ' . $wrapper_attributes . ' data-wp-interactive="etu/map" ' . $context . ' data-wp-class--is-loaded="context.loaded">', \Etublocks\Rendering::allowed() );
	echo '<div class="etu-map__frame">';
	echo '<iframe class="etu-map__iframe" src="about:blank" data-wp-bind--src="context.iframeSrc" title="' . esc_attr( $iframe_title ) . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>';

	echo '<div class="etu-map__facade">';
	echo '<span class="etu-map__facade-bg" aria-hidden="true"></span>';
	echo wp_kses( '<span class="etu-map__pin" aria-hidden="true">' . $icon_pin . '</span>', \Etublocks\Rendering::allowed() );
	echo '<div class="etu-map__facade-card">';
	echo '<p class="etu-map__facade-loc">' . esc_html( $location_label ) . '</p>';
	/* translators: %s: the location. */
	echo '<button type="button" class="etu-map__load" data-wp-on--click="actions.load" aria-label="' . esc_attr( sprintf( __( 'Load the map of %s', 'etu-blocks' ), $location_label ) ) . '">' . esc_html__( 'Load map', 'etu-blocks' ) . '</button>';
	echo '<span class="etu-map__facade-note">' . esc_html__( 'Loads Google Maps', 'etu-blocks' ) . '</span>';
	echo '</div>';
	echo '</div>';

	// The info card duplicates the facade card's location, so CSS keeps it hidden
	// until the map is loaded (the .is-loaded class), then it floats over the map.
	if ( $has_info ) {
		echo wp_kses( '<div class="etu-map__info">' . $info_inner . '</div>', \Etublocks\Rendering::allowed() );
	}
	echo '</div>'; // frame
	echo '</div>'; // root
} else {
	echo wp_kses( '<div ' . $wrapper_attributes . '>', \Etublocks\Rendering::allowed() );
	echo '<div class="etu-map__frame">';
	echo '<iframe class="etu-map__iframe" src="' . esc_url( $embed_url ) . '" title="' . esc_attr( $iframe_title ) . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>';
	if ( $has_info ) {
		echo wp_kses( '<div class="etu-map__info">' . $info_inner . '</div>', \Etublocks\Rendering::allowed() );
	}
	echo '</div>'; // frame
	echo '</div>'; // root
}
