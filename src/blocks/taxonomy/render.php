<?php
/**
 * Taxonomy block — server render.
 *
 * A visual browser for a taxonomy's terms: a grid of cards (representative image +
 * name + count + description), a compact list, or pills — each linking to the term
 * archive. The card image is the term's own image meta (e.g. WooCommerce
 * thumbnail_id) when present, otherwise the featured image of the term's latest
 * post, so every term gets a visual without extra setup. Image lookups are cached
 * in a short-lived transient.
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

$taxonomy = (string) ( $attributes['taxonomy'] ?? 'category' );
if ( ! taxonomy_exists( $taxonomy ) ) {
	return;
}
$tax_object = get_taxonomy( $taxonomy );
if ( ! $tax_object || ! $tax_object->public ) {
	return;
}

$layout = (string) ( $attributes['layout'] ?? 'grid' );
if ( ! in_array( $layout, array( 'grid', 'list', 'pills' ), true ) ) {
	$layout = 'grid';
}
$columns = (string) ( $attributes['columns'] ?? '4' );
if ( ! in_array( $columns, array( '2', '3', '4', '5' ), true ) ) {
	$columns = '4';
}
$image_source = 'none' === ( $attributes['imageSource'] ?? 'auto' ) ? 'none' : 'auto';
$show_count   = ! isset( $attributes['showCount'] ) || ! empty( $attributes['showCount'] );
$show_desc    = ! empty( $attributes['showDescription'] );
$hide_empty   = ! isset( $attributes['hideEmpty'] ) || ! empty( $attributes['hideEmpty'] );
$order_by     = 'count' === ( $attributes['orderBy'] ?? 'name' ) ? 'count' : 'name';
$order        = 'desc' === ( $attributes['order'] ?? 'asc' ) ? 'DESC' : 'ASC';
$number       = max( 0, min( 200, (int) ( $attributes['number'] ?? 0 ) ) );
$top_only     = 'top' === ( $attributes['parentScope'] ?? 'all' );

$term_args = array(
	'taxonomy'   => $taxonomy,
	'hide_empty' => $hide_empty,
	'orderby'    => $order_by,
	'order'      => $order,
);
if ( $number > 0 ) {
	$term_args['number'] = $number;
}
if ( $top_only ) {
	$term_args['parent'] = 0;
}

$terms = get_terms( $term_args );
if ( is_wp_error( $terms ) || empty( $terms ) ) {
	return;
}

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

/**
 * Resolve a representative image URL for each term, cached for six hours.
 */
$images = array();
if ( 'grid' === $layout && 'auto' === $image_source ) {
	$cache_key = 'etublocks_taximg_' . md5( $taxonomy );
	$cached    = get_transient( $cache_key );
	$images    = is_array( $cached ) ? $cached : array();
	$dirty     = false;
	$post_types = $tax_object->object_type ? $tax_object->object_type : array( 'post' );

	foreach ( $terms as $etu_term ) {
		if ( array_key_exists( $etu_term->term_id, $images ) ) {
			continue;
		}
		$url = '';
		$thumb_id = get_term_meta( $etu_term->term_id, 'thumbnail_id', true );
		if ( $thumb_id && is_numeric( $thumb_id ) ) {
			$url = (string) wp_get_attachment_image_url( (int) $thumb_id, 'medium_large' );
		}
		if ( '' === $url ) {
			$latest = new WP_Query(
				array(
					'post_type'           => $post_types,
					'post_status'         => 'publish',
					'posts_per_page'      => 1,
					'fields'              => 'ids',
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
					'meta_key'            => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- one row, cached in a transient below.
					'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- one row, cached in a transient below.
						array(
							'taxonomy' => $taxonomy,
							'field'    => 'term_id',
							'terms'    => $etu_term->term_id,
						),
					),
				)
			);
			if ( ! empty( $latest->posts ) ) {
				$url = (string) get_the_post_thumbnail_url( (int) $latest->posts[0], 'medium_large' );
			}
		}
		$images[ $etu_term->term_id ] = $url;
		$dirty = true;
	}
	if ( $dirty ) {
		set_transient( $cache_key, $images, 6 * HOUR_IN_SECONDS );
	}
}

$scope         = '.etu-taxonomy-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/taxonomy', $attributes, $scope );

$classes = array( 'etu-taxonomy', 'etu-taxonomy--' . $layout, 'etu-taxonomy-' . $block_id );
if ( 'grid' === $layout ) {
	$classes[] = 'etu-taxonomy--cols-' . $columns;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );
$list_class         = 'grid' === $layout ? 'etu-taxonomy__grid' : ( 'list' === $layout ? 'etu-taxonomy__list' : 'etu-taxonomy__pills' );

\Etublocks\CSS_Generator::inject( 'etu/taxonomy', $generated_css );

echo wp_kses( '<nav ' . $wrapper_attributes . ' aria-label="' . esc_attr( $tax_object->labels->name ) . '">', \Etublocks\Rendering::allowed() );
echo '<ul class="' . esc_attr( $list_class ) . '">';

foreach ( $terms as $etu_term ) {
	$link = get_term_link( $etu_term );
	if ( is_wp_error( $link ) ) {
		continue;
	}
	$name  = $etu_term->name;
	$count = (int) $etu_term->count;

	echo '<li class="etu-taxonomy__item">';

	if ( 'pills' === $layout ) {
		echo '<a class="etu-taxonomy__pill" href="' . esc_url( $link ) . '">';
		echo '<span class="etu-taxonomy__name">' . esc_html( $name ) . '</span>';
		if ( $show_count ) {
			echo '<span class="etu-taxonomy__count">' . esc_html( number_format_i18n( $count ) ) . '</span>';
		}
		echo '</a>';
		echo '</li>';
		continue;
	}

	echo '<a class="etu-taxonomy__card" href="' . esc_url( $link ) . '">';

	if ( 'grid' === $layout && 'none' !== $image_source ) {
		$img = isset( $images[ $etu_term->term_id ] ) ? $images[ $etu_term->term_id ] : '';
		if ( '' !== $img ) {
			echo '<span class="etu-taxonomy__media"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async" /></span>';
		} else {
			$hue = ( $etu_term->term_id * 47 ) % 360;
			$initial = function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 1 ) : substr( $name, 0, 1 );
			echo '<span class="etu-taxonomy__media etu-taxonomy__media--blank" style="--etu-tax-hue:' . esc_attr( (string) $hue ) . '" aria-hidden="true">' . esc_html( mb_strtoupper( $initial ) ) . '</span>';
		}
	}

	echo '<span class="etu-taxonomy__body">';
	echo '<span class="etu-taxonomy__row">';
	echo '<span class="etu-taxonomy__name">' . esc_html( $name ) . '</span>';
	if ( $show_count ) {
		echo '<span class="etu-taxonomy__count">' . esc_html( number_format_i18n( $count ) );
		/* translators: %s: number of posts (already shown). */
		echo '<span class="etu-taxonomy__sr"> ' . esc_html( _n( 'post', 'posts', $count, 'etu-blocks' ) ) . '</span>';
		echo '</span>';
	}
	echo '</span>';

	if ( $show_desc ) {
		$desc = trim( wp_strip_all_tags( (string) $etu_term->description ) );
		if ( '' !== $desc ) {
			echo '<span class="etu-taxonomy__desc">' . esc_html( wp_trim_words( $desc, 22 ) ) . '</span>';
		}
	}

	echo '</span>'; // body
	echo '</a>';
	echo '</li>';
}

echo '</ul>';
echo '</nav>';
