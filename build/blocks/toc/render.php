<?php
/**
 * Table of Contents block — server render.
 *
 * Builds a nested list of anchor links from the page's headings (collected by
 * Etublocks\TOC). The links are real in-page anchors, so the TOC works with no
 * JS; the Interactivity store adds smooth scroll, active-section highlighting
 * and the optional collapse.
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

$post_id  = get_the_ID();
$headings = $post_id ? \Etublocks\TOC::get_headings( (int) $post_id ) : array();

$min = (int) ( $attributes['minLevel'] ?? 2 );
$max = (int) ( $attributes['maxLevel'] ?? 4 );
if ( $min > $max ) {
	$tmp = $min;
	$min = $max;
	$max = $tmp;
}

$items = array_values(
	array_filter(
		$headings,
		static function ( $h ) use ( $min, $max ) {
			return $h['level'] >= $min && $h['level'] <= $max;
		}
	)
);

if ( empty( $items ) ) {
	return;
}

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$preset = (string) ( $attributes['preset'] ?? 'boxed' );
if ( ! in_array( $preset, array( 'boxed', 'bordered', 'plain' ), true ) ) {
	$preset = 'boxed';
}

$list_type = (string) ( $attributes['listType'] ?? 'number' );
if ( ! in_array( $list_type, array( 'number', 'bullet', 'plain' ), true ) ) {
	$list_type = 'number';
}
$list_tag = 'number' === $list_type ? 'ol' : 'ul';

$show_title  = ! isset( $attributes['showTitle'] ) || ! empty( $attributes['showTitle'] );
$title       = trim( (string) ( $attributes['title'] ?? '' ) );
if ( '' === $title ) {
	$title = __( 'Table of contents', 'etu-blocks' );
}
$collapsible = ! empty( $attributes['collapsible'] );
$collapsed   = $collapsible && ! empty( $attributes['initialCollapsed'] );
$sticky      = ! empty( $attributes['sticky'] );
$smooth      = ! isset( $attributes['smoothScroll'] ) || ! empty( $attributes['smoothScroll'] );
$spy         = ! isset( $attributes['scrollspy'] ) || ! empty( $attributes['scrollspy'] );

$interactive = $collapsible || $smooth || $spy || $sticky;

$scope         = '.etu-toc-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/toc', $attributes, $scope );

$classes = array(
	'etu-toc',
	'etu-toc--preset-' . $preset,
	'etu-toc--list-' . $list_type,
	'etu-toc-' . $block_id,
);
if ( $sticky ) {
	$classes[] = 'etu-toc--sticky';
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

$title_id = 'etu-toc-title-' . $block_id;
$list_id  = 'etu-toc-list-' . $block_id;

$chevron = '<svg class="etu-toc__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M6 9l6 6 6-6"/></svg>';

// Build the nested list.
$list_attrs = 'id="' . esc_attr( $list_id ) . '" class="etu-toc__list"';
if ( $collapsible ) {
	$list_attrs .= ' data-wp-bind--hidden="context.collapsed"';
}

$list_html = '';
$cur       = null;
$open      = 0;
foreach ( $items as $it ) {
	$lvl = $it['level'];
	if ( null === $cur ) {
		$list_html .= '<' . $list_tag . ' ' . $list_attrs . '>';
		$open       = 1;
		$cur        = $lvl;
	} elseif ( $lvl > $cur ) {
		for ( $k = 0; $k < $lvl - $cur; $k++ ) {
			$list_html .= '<' . $list_tag . ' class="etu-toc__sublist">';
			++$open;
		}
		$cur = $lvl;
	} elseif ( $lvl < $cur ) {
		$list_html .= '</li>';
		for ( $k = 0; $k < $cur - $lvl && $open > 1; $k++ ) {
			$list_html .= '</' . $list_tag . '></li>';
			--$open;
		}
		$cur = $lvl;
	} else {
		$list_html .= '</li>';
	}

	$id   = $it['id'];
	$link = '<a class="etu-toc__link" href="#' . esc_attr( $id ) . '"';
	if ( $spy ) {
		$link .= ' ' . wp_interactivity_data_wp_context( array( 'id' => $id ) )
			. ' data-wp-bind--aria-current="state.currentMark" data-wp-class--is-active="state.isActive"';
	}
	if ( $smooth ) {
		$link .= ' data-wp-on--click="actions.scrollTo"';
	}
	$link .= '>' . esc_html( $it['text'] ) . '</a>';

	$list_html .= '<li class="etu-toc__item">' . $link;
}
if ( null !== $cur ) {
	$list_html .= '</li>';
	for ( $k = 1; $k < $open; $k++ ) {
		$list_html .= '</' . $list_tag . '></li>';
	}
	$list_html .= '</' . $list_tag . '>';
}

\Etublocks\CSS_Generator::inject( 'etu/toc', $generated_css );

$open = '<div ' . $wrapper_attributes;
if ( $interactive ) {
	$open .= ' data-wp-interactive="etu/toc" ' . wp_interactivity_data_wp_context(
		array(
			'activeId'  => '',
			'collapsed' => $collapsed,
			'spy'       => $spy,
			'sticky'    => $sticky,
		)
	);
	if ( $spy || $sticky ) {
		$open .= ' data-wp-init="callbacks.init"';
	}
}
$open .= '>';
echo wp_kses( $open, \Etublocks\Rendering::allowed() );

$aria = $show_title ? ' aria-labelledby="' . esc_attr( $title_id ) . '"' : ' aria-label="' . esc_attr__( 'Table of contents', 'etu-blocks' ) . '"';
echo wp_kses( '<nav class="etu-toc__inner"' . $aria . '>', \Etublocks\Rendering::allowed() );

if ( $show_title ) {
	if ( $collapsible ) {
		echo wp_kses( ( '<button type="button" class="etu-toc__title etu-toc__toggle" id="' . esc_attr( $title_id ) . '" '
			. 'data-wp-on--click="actions.toggle" data-wp-bind--aria-expanded="state.expanded" aria-expanded="' . ( $collapsed ? 'false' : 'true' ) . '" '
			. 'aria-controls="' . esc_attr( $list_id ) . '">'
			. '<span class="etu-toc__title-text">' . esc_html( $title ) . '</span>' . $chevron
			. '</button>' ), \Etublocks\Rendering::allowed() );
	} else {
		echo '<p class="etu-toc__title" id="' . esc_attr( $title_id ) . '">' . esc_html( $title ) . '</p>';
	}
}

echo wp_kses( $list_html, \Etublocks\Rendering::allowed() );

echo '</nav>';
echo '</div>';
