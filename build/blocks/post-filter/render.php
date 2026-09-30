<?php
/**
 * Post Filter block — server render.
 *
 * Renders a filter bar (search + taxonomy dropdowns + reset) and a results grid.
 * The bar is a real GET <form>, and the current filters are read from the query
 * string, so everything works with JavaScript disabled (submitting reloads with the
 * filtered results). On the front end the Interactivity store intercepts changes and
 * calls the REST route to swap the results in place. Query building and card markup
 * are shared with the REST handler in Post_Filter, so results are always identical.
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

use Etublocks\Post_Filter;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Included within the block render callback's method scope; these are local, not global.

$cfg = Post_Filter::sanitize_config(
	array(
		'postType'   => $attributes['postType'] ?? 'post',
		'perPage'    => $attributes['perPage'] ?? 9,
		'orderBy'    => $attributes['orderBy'] ?? 'date',
		'order'      => $attributes['order'] ?? 'desc',
		'taxonomies' => is_array( $attributes['filterTaxonomies'] ?? null ) ? $attributes['filterTaxonomies'] : array(),
	)
);
$card = Post_Filter::sanitize_card( is_array( $attributes ) ? $attributes : array() );

$enable_search = ! isset( $attributes['enableSearch'] ) || ! empty( $attributes['enableSearch'] );
$enable_reset  = ! isset( $attributes['enableReset'] ) || ! empty( $attributes['enableReset'] );

$columns = (string) ( $attributes['columns'] ?? '3' );
if ( ! in_array( $columns, array( '2', '3', '4' ), true ) ) {
	$columns = '3';
}

$hover = (string) ( $attributes['hoverEffect'] ?? 'lift' );
if ( ! in_array( $hover, array( 'none', 'lift', 'zoom', 'both' ), true ) ) {
	$hover = 'lift';
}

$card_style = (string) ( $attributes['cardStyle'] ?? 'card' );
if ( ! in_array( $card_style, array( 'card', 'overlay' ), true ) ) {
	$card_style = 'card';
}

// Read the active filters from the query string (no-JS support). Nonce is not
// needed: this only reads and displays public, published posts.
// phpcs:disable WordPress.Security.NonceVerification.Recommended
$active_s = isset( $_GET['etu_s'] ) ? sanitize_text_field( wp_unslash( $_GET['etu_s'] ) ) : '';
$page     = isset( $_GET['etu_page'] ) ? max( 1, absint( wp_unslash( $_GET['etu_page'] ) ) ) : 1;
$active_terms = array();
foreach ( $cfg['taxonomies'] as $tax ) {
	$key = 'etu_' . $tax;
	if ( isset( $_GET[ $key ] ) ) {
		$active_terms[ $tax ] = sanitize_title( wp_unslash( $_GET[ $key ] ) );
	}
}
// phpcs:enable WordPress.Security.NonceVerification.Recommended

$query = new WP_Query( Post_Filter::query_args( $cfg, $active_s, $active_terms, $page ) );
$found = (int) $query->found_posts;
$pages = (int) $query->max_num_pages;

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$scope         = '.etu-post-filter-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/post-filter', $attributes, $scope );

$classes = array(
	'etu-post-filter',
	'etu-post-filter--cols-' . $columns,
	'etu-post-filter--ratio-' . $card['imageRatio'],
	'etu-post-filter--hover-' . $hover,
	'etu-post-filter--card-' . $card_style,
	'etu-post-filter-' . $block_id,
);
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

$base_url = get_permalink();
if ( ! $base_url ) {
	$base_url = home_url( '/' );
}
$next_url = add_query_arg( array_merge( array( 'etu_s' => $active_s ), $active_terms, array( 'etu_page' => $page + 1 ) ), $base_url );

/* translators: %s: number of matching posts. */
$status_template = __( '%s results', 'etu-blocks' );
$status_initial  = sprintf( $status_template, number_format_i18n( $found ) );

$context = wp_interactivity_data_wp_context(
	array(
		'restUrl'        => rest_url( Post_Filter::REST_NS . Post_Filter::REST_ROUTE ),
		'nonce'          => wp_create_nonce( 'wp_rest' ),
		'cfg'            => array(
			'postType'   => $cfg['postType'],
			'perPage'    => $cfg['perPage'],
			'orderBy'    => $cfg['orderBy'],
			'order'      => $cfg['order'],
			'taxonomies' => $cfg['taxonomies'],
			'card'       => $card,
		),
		'filters'        => array( 's' => $active_s, 'terms' => (object) $active_terms ),
		'page'           => $page,
		'pages'          => $pages,
		'total'          => $found,
		'hasMore'        => $page < $pages,
		'loading'        => false,
		'statusText'     => $status_initial,
		'statusTemplate' => $status_template,
	)
);

\Etublocks\CSS_Generator::inject( 'etu/post-filter', $generated_css );

echo wp_kses( '<div ' . $wrapper_attributes . ' data-wp-interactive="etu/post-filter" ' . $context . '>', \Etublocks\Rendering::allowed() );

// ---- Filter bar (real GET form) ----
echo '<form class="etu-post-filter__bar" role="search" method="get" action="' . esc_url( $base_url ) . '" data-wp-on--submit="actions.submit">';

if ( $enable_search ) {
	echo '<div class="etu-post-filter__field etu-post-filter__field--search">';
	echo '<input type="search" name="etu_s" class="etu-post-filter__search" value="' . esc_attr( $active_s ) . '" placeholder="' . esc_attr__( 'Search…', 'etu-blocks' ) . '" aria-label="' . esc_attr__( 'Search posts', 'etu-blocks' ) . '" data-wp-on--input="actions.search" />';
	echo '</div>';
}

foreach ( $cfg['taxonomies'] as $tax ) {
	$tax_obj = get_taxonomy( $tax );
	if ( ! $tax_obj ) {
		continue;
	}
	$terms = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => true, 'number' => 100 ) );
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		continue;
	}
	$selected = isset( $active_terms[ $tax ] ) ? $active_terms[ $tax ] : '';
	echo '<div class="etu-post-filter__field">';
	echo '<select name="etu_' . esc_attr( $tax ) . '" class="etu-post-filter__select" data-tax="' . esc_attr( $tax ) . '" aria-label="' . esc_attr( $tax_obj->labels->name ) . '" data-wp-on--change="actions.filterTax">';
	/* translators: %s: taxonomy name (e.g. Categories). */
	echo '<option value="">' . esc_html( sprintf( __( 'All %s', 'etu-blocks' ), $tax_obj->labels->name ) ) . '</option>';
	foreach ( $terms as $term ) {
		echo '<option value="' . esc_attr( $term->slug ) . '"' . selected( $selected, $term->slug, false ) . '>' . esc_html( $term->name ) . '</option>';
	}
	echo '</select>';
	echo '</div>';
}

echo '<button type="submit" class="etu-post-filter__apply">' . esc_html__( 'Filter', 'etu-blocks' ) . '</button>';

if ( $enable_reset ) {
	echo '<a class="etu-post-filter__reset" href="' . esc_url( $base_url ) . '" data-wp-on--click="actions.reset">' . esc_html__( 'Reset', 'etu-blocks' ) . '</a>';
}

echo '</form>';

// ---- Result count (announced politely on change) ----
echo '<p class="etu-post-filter__count" role="status" aria-live="polite" data-wp-text="context.statusText">' . esc_html( $status_initial ) . '</p>';

// ---- Results ----
echo '<div class="etu-post-filter__results" aria-busy="false" data-wp-bind--aria-busy="context.loading">';
echo '<div class="etu-post-filter__list">';
echo wp_kses( Post_Filter::render_cards( $query, $card ), \Etublocks\Rendering::allowed() );
echo '</div>';
echo '</div>';

// ---- Load more (real next-page link, upgraded to append by JS) ----
echo '<div class="etu-post-filter__footer' . ( $page < $pages ? ' is-visible' : '' ) . '" data-wp-class--is-visible="context.hasMore">';
echo '<a class="etu-post-filter__loadmore" href="' . esc_url( $next_url ) . '" data-wp-on--click="actions.loadMore">' . esc_html__( 'Load more', 'etu-blocks' ) . '</a>';
echo '</div>';

echo '</div>'; // root
