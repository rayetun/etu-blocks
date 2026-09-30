<?php
/**
 * Post Grid block — server render.
 *
 * Queries posts and lays them out as a grid, masonry column layout or a
 * scroll-snap carousel. Cards are <article>s with a heading title. The optional
 * category filter and the carousel controls run on the Interactivity store; the
 * grid works fully with JS off.
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

$post_type = sanitize_key( (string) ( $attributes['postType'] ?? 'post' ) );
if ( ! post_type_exists( $post_type ) ) {
	$post_type = 'post';
}

$per_page = max( 1, min( 24, (int) ( $attributes['perPage'] ?? 6 ) ) );
$offset   = max( 0, min( 50, (int) ( $attributes['offset'] ?? 0 ) ) );

$order_by = (string) ( $attributes['orderBy'] ?? 'date' );
if ( ! in_array( $order_by, array( 'date', 'title', 'menu_order', 'comment_count', 'rand' ), true ) ) {
	$order_by = 'date';
}
$order = 'asc' === strtolower( (string) ( $attributes['order'] ?? 'desc' ) ) ? 'ASC' : 'DESC';

$categories = isset( $attributes['queryCategories'] ) && is_array( $attributes['queryCategories'] )
	? array_map( 'sanitize_title', $attributes['queryCategories'] )
	: array();

$query_args = array(
	'post_type'           => $post_type,
	'posts_per_page'      => $per_page,
	'offset'              => $offset,
	'orderby'             => $order_by,
	'order'               => $order,
	'post_status'         => 'publish',
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
);
if ( ! empty( $categories ) ) {
	$query_args['tax_query'] = array(
		array(
			'taxonomy' => 'category',
			'field'    => 'slug',
			'terms'    => $categories,
		),
	);
}
if ( ! empty( $attributes['excludeCurrent'] ) && is_singular() ) {
	$query_args['post__not_in'] = array( get_the_ID() );
}

$query = new WP_Query( $query_args );
if ( ! $query->have_posts() ) {
	return;
}

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$layout = (string) ( $attributes['layout'] ?? 'grid' );
if ( ! in_array( $layout, array( 'grid', 'masonry', 'carousel' ), true ) ) {
	$layout = 'grid';
}
$columns = (string) ( $attributes['columns'] ?? '3' );
if ( ! in_array( $columns, array( '2', '3', '4' ), true ) ) {
	$columns = '3';
}
$card_style = (string) ( $attributes['cardStyle'] ?? 'elevated' );
if ( ! in_array( $card_style, array( 'elevated', 'bordered', 'minimal', 'overlay' ), true ) ) {
	$card_style = 'elevated';
}
$ratio = (string) ( $attributes['imageRatio'] ?? 'landscape' );
if ( ! in_array( $ratio, array( 'landscape', 'square', 'portrait', 'auto' ), true ) ) {
	$ratio = 'landscape';
}

$hover = (string) ( $attributes['hoverEffect'] ?? 'both' );
if ( ! in_array( $hover, array( 'none', 'lift', 'zoom', 'both' ), true ) ) {
	$hover = 'both';
}

$show_image    = ! isset( $attributes['showImage'] ) || ! empty( $attributes['showImage'] );
$show_title    = ! isset( $attributes['showTitle'] ) || ! empty( $attributes['showTitle'] );
$show_excerpt  = ! isset( $attributes['showExcerpt'] ) || ! empty( $attributes['showExcerpt'] );
$show_meta     = ! isset( $attributes['showMeta'] ) || ! empty( $attributes['showMeta'] );
$show_author   = ! empty( $attributes['showAuthor'] );
$show_category = ! isset( $attributes['showCategory'] ) || ! empty( $attributes['showCategory'] );
$show_button   = ! empty( $attributes['showButton'] );
$button_text   = trim( (string) ( $attributes['buttonText'] ?? '' ) );
if ( '' === $button_text ) {
	$button_text = __( 'Read more', 'etu-blocks' );
}
$excerpt_len = max( 5, min( 60, (int) ( $attributes['excerptLength'] ?? 20 ) ) );

$is_carousel   = 'carousel' === $layout;
$enable_filter = ! empty( $attributes['enableFilter'] ) && ! $is_carousel;
$show_arrows   = $is_carousel && ( ! isset( $attributes['showArrows'] ) || ! empty( $attributes['showArrows'] ) );
$show_dots     = $is_carousel && ( ! isset( $attributes['showDots'] ) || ! empty( $attributes['showDots'] ) );
$interactive   = $enable_filter || $is_carousel;

// Categories present among the results (for the filter bar).
$present_cats = array();
if ( $enable_filter ) {
	foreach ( $query->posts as $p ) {
		foreach ( get_the_category( $p->ID ) as $c ) {
			$present_cats[ $c->slug ] = $c->name;
		}
	}
}

$scope         = '.etu-post-grid-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/post-grid', $attributes, $scope );

// Responsive slides-per-view for the carousel layout.
if ( $is_carousel ) {
	$pv    = is_array( $attributes['carouselPerView'] ?? null ) ? $attributes['carouselPerView'] : array();
	$pv_d  = max( 1, min( 6, (int) ( $pv['desktop'] ?? 3 ) ) );
	$pv_t  = max( 1, min( 6, (int) ( $pv['tablet'] ?? $pv_d ) ) );
	$pv_m  = max( 1, min( 6, (int) ( $pv['mobile'] ?? 1 ) ) );
	$generated_css .= $scope . '{--etu-pg-per:' . $pv_d . ';}';
	$generated_css .= '@media (max-width:1024px){' . $scope . '{--etu-pg-per:' . $pv_t . ';}}';
	$generated_css .= '@media (max-width:767px){' . $scope . '{--etu-pg-per:' . $pv_m . ';}}';
}

$classes = array(
	'etu-post-grid',
	'etu-post-grid--layout-' . $layout,
	'etu-post-grid--cols-' . $columns,
	'etu-post-grid--card-' . $card_style,
	'etu-post-grid--ratio-' . $ratio,
	'etu-post-grid--hover-' . $hover,
	'etu-post-grid-' . $block_id,
);
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

$icon_prev = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M15 18l-6-6 6-6"/></svg>';
$icon_next = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M9 18l6-6-6-6"/></svg>';

$img_size = 'landscape' === $ratio ? 'large' : 'medium_large';

/**
 * Render one post card.
 */
$render_card = static function () use (
	$show_image, $show_title, $show_excerpt, $show_meta, $show_author, $show_category,
	$show_button, $button_text, $excerpt_len, $img_size, $enable_filter
) {
	$permalink = get_permalink();
	$cats      = get_the_category();

	$card_ctx = '';
	if ( $enable_filter ) {
		$slugs = array();
		foreach ( $cats as $c ) {
			$slugs[] = $c->slug;
		}
		$card_ctx = ' ' . wp_interactivity_data_wp_context( array( 'cats' => $slugs, 'visible' => true ) )
			. ' data-wp-watch="callbacks.filterCard" data-wp-bind--hidden="!context.visible"';
	}

	echo wp_kses( '<article class="etu-post-grid__card"' . $card_ctx . '>', \Etublocks\Rendering::allowed() );

	if ( $show_image && has_post_thumbnail() ) {
		echo '<a class="etu-post-grid__image" href="' . esc_url( $permalink ) . '" tabindex="-1" aria-hidden="true">';
		echo get_the_post_thumbnail( get_the_ID(), $img_size, array( 'loading' => 'lazy', 'alt' => '' ) );
		echo '</a>';
	}

	echo '<div class="etu-post-grid__body">';

	if ( $show_category && ! empty( $cats ) ) {
		echo '<a class="etu-post-grid__badge" href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
	}

	if ( $show_title ) {
		echo '<h3 class="etu-post-grid__title"><a href="' . esc_url( $permalink ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
	}

	if ( $show_meta || $show_author ) {
		echo '<div class="etu-post-grid__meta">';
		if ( $show_meta ) {
			echo '<span class="etu-post-grid__date">' . esc_html( get_the_date() ) . '</span>';
		}
		if ( $show_author ) {
			echo '<span class="etu-post-grid__author">' . esc_html( get_the_author() ) . '</span>';
		}
		echo '</div>';
	}

	if ( $show_excerpt ) {
		echo '<p class="etu-post-grid__excerpt">' . esc_html( wp_trim_words( get_the_excerpt(), $excerpt_len, '…' ) ) . '</p>';
	}

	if ( $show_button ) {
		/* translators: 1: read-more label, 2: post title. */
		$aria = esc_attr( sprintf( __( '%1$s: %2$s', 'etu-blocks' ), wp_strip_all_tags( $button_text ), get_the_title() ) );
		echo wp_kses( '<a class="etu-post-grid__more" href="' . esc_url( $permalink ) . '" aria-label="' . $aria . '">' . esc_html( $button_text ) . '</a>', \Etublocks\Rendering::allowed() );
	}

	echo '</div></article>';
};

\Etublocks\CSS_Generator::inject( 'etu/post-grid', $generated_css );

$open = '<div ' . $wrapper_attributes;
if ( $interactive ) {
	$open .= ' data-wp-interactive="etu/post-grid" ' . wp_interactivity_data_wp_context(
		array(
			'activeCat'       => '',
			'activeLabel'     => '',
			'current'         => 0,
			/* translators: %s: category name. */
			'showingTemplate' => __( 'Showing: %s', 'etu-blocks' ),
		)
	);
	if ( $is_carousel ) {
		$open .= ' data-wp-init="callbacks.init"';
	}
}
$open .= '>';
echo wp_kses( $open, \Etublocks\Rendering::allowed() );

// Filter bar.
if ( $enable_filter && ! empty( $present_cats ) ) {
	echo '<div class="etu-post-grid__filters" role="group" aria-label="' . esc_attr__( 'Filter posts by category', 'etu-blocks' ) . '">';
	echo wp_kses( '<button type="button" class="etu-post-grid__filter" ' . wp_interactivity_data_wp_context( array( 'slug' => '', 'label' => __( 'All', 'etu-blocks' ) ) )
		. ' data-wp-on--click="actions.setFilter" data-wp-bind--aria-pressed="state.filterActive" aria-pressed="true">'
		. esc_html__( 'All', 'etu-blocks' ) . '</button>', \Etublocks\Rendering::allowed() );
	foreach ( $present_cats as $slug => $name ) {
		echo wp_kses( ( '<button type="button" class="etu-post-grid__filter" ' . wp_interactivity_data_wp_context( array( 'slug' => $slug, 'label' => $name ) )
			. ' data-wp-on--click="actions.setFilter" data-wp-bind--aria-pressed="state.filterActive" aria-pressed="false">'
			. esc_html( $name ) . '</button>' ), \Etublocks\Rendering::allowed() );
	}
	echo '</div>';
}

if ( $is_carousel ) {
	echo '<div class="etu-post-grid__viewport">';
	echo '<div class="etu-post-grid__list etu-post-grid__track" tabindex="0" aria-label="' . esc_attr__( 'Posts', 'etu-blocks' ) . '">';
} else {
	echo '<div class="etu-post-grid__list">';
}

while ( $query->have_posts() ) {
	$query->the_post();
	$render_card();
}
wp_reset_postdata();

echo '</div>'; // list

if ( $is_carousel ) {
	if ( $show_arrows ) {
		echo wp_kses( '<button type="button" class="etu-post-grid__arrow etu-post-grid__arrow--prev" data-wp-on--click="actions.prev" aria-label="' . esc_attr__( 'Previous', 'etu-blocks' ) . '">' . $icon_prev . '</button>', \Etublocks\Rendering::allowed() );
		echo wp_kses( '<button type="button" class="etu-post-grid__arrow etu-post-grid__arrow--next" data-wp-on--click="actions.next" aria-label="' . esc_attr__( 'Next', 'etu-blocks' ) . '">' . $icon_next . '</button>', \Etublocks\Rendering::allowed() );
	}
	echo '</div>'; // viewport
	if ( $show_dots ) {
		// Page dots are built by the store from the responsive slides-per-view.
		echo '<div class="etu-post-grid__dots" role="group" aria-label="' . esc_attr__( 'Choose page', 'etu-blocks' ) . '" data-page-label="' . esc_attr__( 'Go to page', 'etu-blocks' ) . '"></div>';
	}
}

if ( $enable_filter ) {
	echo '<span class="etu-post-grid__status screen-reader-text" role="status" aria-live="polite" data-wp-text="state.statusText"></span>';
}

echo '</div>';
