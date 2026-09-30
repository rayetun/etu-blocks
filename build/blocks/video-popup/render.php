<?php
/**
 * Video Popup block — server render.
 *
 * A poster (image + play button) that opens the video in an accessible dialog
 * (role="dialog", focus trap, Esc, scroll lock — the same recipe as the Modal
 * block). The heavy third-party embed is a *facade*: the YouTube/Vimeo iframe has
 * no src until the visitor clicks play, so no third-party script or cookie loads on
 * page view. YouTube uses the youtube-nocookie host; Vimeo gets dnt=1. Self-hosted
 * video uses a native <video preload="none"> so nothing downloads until play.
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

$type = (string) ( $attributes['videoType'] ?? 'youtube' );
if ( ! in_array( $type, array( 'youtube', 'vimeo', 'self' ), true ) ) {
	$type = 'youtube';
}

$video_url = trim( (string) ( $attributes['videoUrl'] ?? '' ) );
$video_file = is_array( $attributes['videoFile'] ?? null ) ? $attributes['videoFile'] : array();
$file_url   = isset( $video_file['url'] ) ? esc_url_raw( (string) $video_file['url'] ) : '';
$autoplay   = ! isset( $attributes['autoplay'] ) || ! empty( $attributes['autoplay'] );

/**
 * Pull a YouTube ID (11 chars) or Vimeo ID (digits) out of a URL or raw ID.
 */
$parse_id = static function ( $value, $kind ) {
	$value = trim( $value );
	if ( 'youtube' === $kind ) {
		if ( preg_match( '~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/|v/))([A-Za-z0-9_-]{11})~', $value, $m ) ) {
			return $m[1];
		}
		if ( preg_match( '~^[A-Za-z0-9_-]{11}$~', $value ) ) {
			return $value;
		}
	} else {
		if ( preg_match( '~vimeo\.com/(?:video/)?(\d+)~', $value, $m ) ) {
			return $m[1];
		}
		if ( preg_match( '~^\d+$~', $value ) ) {
			return $value;
		}
	}
	return '';
};

$embed_url  = '';
$poster_url = '';

if ( 'self' === $type ) {
	$embed_url = $file_url;
} else {
	$id = $parse_id( $video_url, $type );
	if ( '' === $id ) {
		return; // No usable video.
	}
	if ( 'youtube' === $type ) {
		$args      = array( 'rel' => '0', 'modestbranding' => '1', 'playsinline' => '1' );
		if ( $autoplay ) {
			$args['autoplay'] = '1';
		}
		$embed_url  = add_query_arg( $args, 'https://www.youtube-nocookie.com/embed/' . rawurlencode( $id ) );
		$poster_url = 'https://i.ytimg.com/vi/' . rawurlencode( $id ) . '/hqdefault.jpg';
	} else {
		$args = array( 'dnt' => '1' );
		if ( $autoplay ) {
			$args['autoplay'] = '1';
		}
		$embed_url = add_query_arg( $args, 'https://player.vimeo.com/video/' . rawurlencode( $id ) );
	}
}

if ( '' === $embed_url ) {
	return; // Nothing to play.
}

// A custom poster always wins; otherwise use the YouTube thumbnail if we have one.
$poster = is_array( $attributes['posterImage'] ?? null ) ? $attributes['posterImage'] : array();
if ( isset( $poster['url'] ) && '' !== trim( (string) $poster['url'] ) ) {
	$poster_url = esc_url_raw( (string) $poster['url'] );
}
$poster_alt = isset( $poster['alt'] ) ? (string) $poster['alt'] : '';

$title = trim( (string) ( $attributes['title'] ?? '' ) );
$show_title = ! empty( $attributes['showTitle'] ) && '' !== $title;

$ratio = (string) ( $attributes['aspectRatio'] ?? '16x9' );
if ( ! in_array( $ratio, array( '16x9', '4x3', '1x1', '21x9' ), true ) ) {
	$ratio = '16x9';
}

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$dialog_label = '' !== $title ? $title : __( 'Video', 'etu-blocks' );
/* translators: %s: video title. */
$play_label = '' !== $title ? sprintf( __( 'Play video: %s', 'etu-blocks' ), $title ) : __( 'Play video', 'etu-blocks' );

$scope         = '.etu-video-popup-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/video-popup', $attributes, $scope );

$classes = array(
	'etu-video-popup',
	'etu-video-popup--ar-' . $ratio,
	'etu-video-popup--' . $type,
	'etu-video-popup-' . $block_id,
);

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );
$context            = wp_interactivity_data_wp_context(
	array(
		'open'      => false,
		'type'      => $type,
		'embedUrl'  => $embed_url,
		'autoplay'  => $autoplay,
		// Idle at about:blank (not ""), so the facade loads nothing on page view
		// and an empty src never makes the iframe reload the current page.
		'iframeSrc' => 'about:blank',
	)
);

$icon_play  = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M8 5v14l11-7z"/></svg>';
$icon_close = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6 6 18"/></svg>';

\Etublocks\CSS_Generator::inject( 'etu/video-popup', $generated_css );

echo wp_kses( '<div ' . $wrapper_attributes . ' data-wp-interactive="etu/video-popup" ' . $context . ' data-wp-init="callbacks.init" data-wp-watch="callbacks.onToggle">', \Etublocks\Rendering::allowed() );

// Poster button — the single, keyboard-focusable control that opens the dialog.
echo '<button type="button" class="etu-video-popup__poster" aria-haspopup="dialog" data-wp-on--click="actions.open" aria-label="' . esc_attr( $play_label ) . '">';
if ( '' !== $poster_url ) {
	echo '<img class="etu-video-popup__thumb" src="' . esc_url( $poster_url ) . '" alt="' . esc_attr( $poster_alt ) . '" loading="lazy" decoding="async" />';
} else {
	echo '<span class="etu-video-popup__thumb etu-video-popup__thumb--blank" aria-hidden="true"></span>';
}
echo '<span class="etu-video-popup__overlay" aria-hidden="true"></span>';
echo wp_kses( '<span class="etu-video-popup__play" aria-hidden="true">' . $icon_play . '</span>', \Etublocks\Rendering::allowed() );
if ( $show_title ) {
	echo '<span class="etu-video-popup__title" aria-hidden="true">' . esc_html( $title ) . '</span>';
}
echo '</button>';

// Dialog.
echo '<div class="etu-video-popup__backdrop" data-wp-class--is-open="context.open" data-wp-on--click="actions.backdropClick" data-wp-on--keydown="actions.onKeydown">';
echo '<div class="etu-video-popup__dialog" role="dialog" aria-modal="true" aria-label="' . esc_attr( $dialog_label ) . '" tabindex="-1">';
echo wp_kses( '<button type="button" class="etu-video-popup__close" data-wp-on--click="actions.close" aria-label="' . esc_attr__( 'Close video', 'etu-blocks' ) . '">' . $icon_close . '</button>', \Etublocks\Rendering::allowed() );
echo '<div class="etu-video-popup__player">';

if ( 'self' === $type ) {
	echo( '<video class="etu-video-popup__video" controls preload="none" playsinline'
		. ( $poster_url ? ' poster="' . esc_url( $poster_url ) . '"' : '' )
		. '><source src="' . esc_url( $embed_url ) . '" type="video/mp4" /></video>' );
} else {
	echo '<iframe class="etu-video-popup__iframe" src="about:blank" data-wp-bind--src="context.iframeSrc" title="' . esc_attr( $dialog_label ) . '" allow="autoplay; fullscreen; picture-in-picture; encrypted-media" allowfullscreen loading="lazy"></iframe>';
}

echo '</div>'; // player
echo '</div>'; // dialog
echo '</div>'; // backdrop
echo '</div>'; // root
