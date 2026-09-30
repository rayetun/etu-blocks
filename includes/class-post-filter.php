<?php
/**
 * Post Filter — REST endpoint + shared query/card rendering.
 *
 * The Post Filter block renders a filter bar and a results grid server-side (so it
 * works with JS disabled via a GET form). On the front end the Interactivity store
 * calls this REST route to re-query and swap the results without a reload. Both the
 * initial render and the REST responses share the same query builder and card
 * renderer here, so results are always identical. Only published posts of public
 * post types are ever exposed, and every parameter is validated and clamped.
 *
 * @package Etublocks
 */

namespace Etublocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Query + render helpers shared by the block and its REST route.
 */
final class Post_Filter {

	const REST_NS    = 'etu/v1';
	const REST_ROUTE = '/posts';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	/**
	 * Register the filter REST route.
	 *
	 * @return void
	 */
	public static function register_routes(): void {
		register_rest_route(
			self::REST_NS,
			self::REST_ROUTE,
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'rest_query' ),
				'permission_callback' => '__return_true', // Public, published posts only.
				'args'                => array(
					'postType' => array( 'type' => 'string' ),
					'perPage'  => array( 'type' => 'integer' ),
					'orderBy'  => array( 'type' => 'string' ),
					'order'    => array( 'type' => 'string' ),
					'page'     => array( 'type' => 'integer' ),
					's'        => array( 'type' => 'string' ),
					'tax'      => array( 'type' => 'object' ),
					'card'     => array( 'type' => 'object' ),
				),
			)
		);
	}

	/**
	 * Public taxonomies that apply to a post type and are safe to filter on.
	 *
	 * @param string $post_type Post type.
	 * @return array<int, string>
	 */
	public static function allowed_taxonomies( string $post_type ): array {
		$out = array();
		foreach ( get_object_taxonomies( $post_type, 'objects' ) as $tax ) {
			if ( $tax->public && $tax->show_ui ) {
				$out[] = $tax->name;
			}
		}
		return $out;
	}

	/**
	 * Sanitise a raw config (from block attributes or REST) into a safe shape.
	 *
	 * @param array $raw Raw config.
	 * @return array{postType:string,perPage:int,orderBy:string,order:string,taxonomies:array<int,string>}
	 */
	public static function sanitize_config( array $raw ): array {
		$post_type = sanitize_key( (string) ( $raw['postType'] ?? 'post' ) );
		$pt_object = get_post_type_object( $post_type );
		if ( ! $pt_object || ! $pt_object->public || 'attachment' === $post_type ) {
			$post_type = 'post';
		}

		$order_by = (string) ( $raw['orderBy'] ?? 'date' );
		if ( ! in_array( $order_by, array( 'date', 'title', 'menu_order', 'comment_count', 'rand' ), true ) ) {
			$order_by = 'date';
		}

		$order = 'asc' === strtolower( (string) ( $raw['order'] ?? 'desc' ) ) ? 'ASC' : 'DESC';

		$allowed    = self::allowed_taxonomies( $post_type );
		$taxonomies = array();
		$requested  = isset( $raw['taxonomies'] ) && is_array( $raw['taxonomies'] ) ? $raw['taxonomies'] : array();
		foreach ( $requested as $tax ) {
			$tax = sanitize_key( (string) $tax );
			if ( in_array( $tax, $allowed, true ) ) {
				$taxonomies[] = $tax;
			}
		}

		return array(
			'postType'   => $post_type,
			'perPage'    => max( 1, min( 24, (int) ( $raw['perPage'] ?? 9 ) ) ),
			'orderBy'    => $order_by,
			'order'      => $order,
			'taxonomies' => $taxonomies,
		);
	}

	/**
	 * Sanitise the card display config.
	 *
	 * @param array $raw Raw card config.
	 * @return array<string, mixed>
	 */
	public static function sanitize_card( array $raw ): array {
		$ratio = (string) ( $raw['imageRatio'] ?? 'landscape' );
		if ( ! in_array( $ratio, array( 'landscape', 'square', 'portrait' ), true ) ) {
			$ratio = 'landscape';
		}
		$button = trim( (string) ( $raw['buttonText'] ?? '' ) );
		return array(
			'showImage'       => ! isset( $raw['showImage'] ) || ! empty( $raw['showImage'] ),
			'imageRatio'      => $ratio,
			'showPlaceholder' => ! isset( $raw['showPlaceholder'] ) || ! empty( $raw['showPlaceholder'] ),
			'showCategory'    => ! empty( $raw['showCategory'] ),
			'showDate'        => ! isset( $raw['showDate'] ) || ! empty( $raw['showDate'] ),
			'showExcerpt'     => ! isset( $raw['showExcerpt'] ) || ! empty( $raw['showExcerpt'] ),
			'excerptWords'    => max( 5, min( 60, (int) ( $raw['excerptWords'] ?? 20 ) ) ),
			'showButton'      => ! empty( $raw['showButton'] ),
			'buttonText'      => '' !== $button ? $button : __( 'Read more', 'etu-blocks' ),
		);
	}

	/**
	 * Decorative SVG shown in place of a missing featured image.
	 *
	 * @return string
	 */
	public static function placeholder_svg(): string {
		return '<svg class="etu-post-filter__placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
			. '<rect x="3" y="4" width="18" height="16" rx="2"/>'
			. '<circle cx="8.5" cy="9.5" r="1.5"/>'
			. '<path d="M21 16l-5-5L5 20"/></svg>';
	}

	/**
	 * Build WP_Query args from a sanitised config plus the active filters.
	 *
	 * @param array  $cfg   Sanitised config.
	 * @param string $s     Search term.
	 * @param array  $terms Map of taxonomy => term slug.
	 * @param int    $page  Page number (1-based).
	 * @return array<string, mixed>
	 */
	public static function query_args( array $cfg, string $s, array $terms, int $page ): array {
		$args = array(
			'post_type'           => $cfg['postType'],
			'post_status'         => 'publish',
			'posts_per_page'      => $cfg['perPage'],
			'paged'               => max( 1, $page ),
			'orderby'             => $cfg['orderBy'],
			'order'               => $cfg['order'],
			'ignore_sticky_posts' => true,
		);

		if ( '' !== $s ) {
			$args['s'] = $s;
		}

		$tax_query = array();
		foreach ( $cfg['taxonomies'] as $tax ) {
			$slug = isset( $terms[ $tax ] ) ? sanitize_title( (string) $terms[ $tax ] ) : '';
			if ( '' !== $slug ) {
				$tax_query[] = array(
					'taxonomy' => $tax,
					'field'    => 'slug',
					'terms'    => $slug,
				);
			}
		}
		if ( ! empty( $tax_query ) ) {
			$tax_query['relation'] = 'AND';
			$args['tax_query']     = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- user-driven archive filtering; the terms are sanitised slugs.
		}

		return $args;
	}

	/**
	 * Render the cards for a query (or an empty-state message).
	 *
	 * @param \WP_Query $query The query.
	 * @param array     $card  Sanitised card config.
	 * @return string
	 */
	public static function render_cards( \WP_Query $query, array $card ): string {
		if ( ! $query->have_posts() ) {
			return '<p class="etu-post-filter__empty">' . esc_html__( 'No posts match your filters.', 'etu-blocks' ) . '</p>';
		}

		$img_size = 'landscape' === $card['imageRatio'] ? 'large' : 'medium_large';

		ob_start();
		while ( $query->have_posts() ) {
			$query->the_post();
			$permalink = get_permalink();
			$cats      = get_the_category();

			echo '<article class="etu-post-filter__card">';

			if ( $card['showImage'] && has_post_thumbnail() ) {
				echo '<a class="etu-post-filter__image" href="' . esc_url( $permalink ) . '" tabindex="-1" aria-hidden="true">';
				echo get_the_post_thumbnail( get_the_ID(), $img_size, array( 'loading' => 'lazy', 'alt' => '' ) );
				echo '</a>';
			} elseif ( $card['showImage'] && $card['showPlaceholder'] ) {
				echo wp_kses( '<a class="etu-post-filter__image etu-post-filter__image--placeholder" href="' . esc_url( $permalink ) . '" tabindex="-1" aria-hidden="true">' . self::placeholder_svg() . '</a>', \Etublocks\Rendering::allowed() );
			}

			echo '<div class="etu-post-filter__cardbody">';

			if ( $card['showCategory'] && ! empty( $cats ) ) {
				echo '<a class="etu-post-filter__badge" href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
			}

			echo '<h3 class="etu-post-filter__title"><a href="' . esc_url( $permalink ) . '">' . esc_html( get_the_title() ) . '</a></h3>';

			if ( $card['showDate'] ) {
				echo '<div class="etu-post-filter__meta"><span class="etu-post-filter__date">' . esc_html( get_the_date() ) . '</span></div>';
			}

			if ( $card['showExcerpt'] ) {
				echo '<p class="etu-post-filter__excerpt">' . esc_html( wp_trim_words( get_the_excerpt(), $card['excerptWords'], '…' ) ) . '</p>';
			}

			if ( $card['showButton'] ) {
				/* translators: 1: read-more label, 2: post title. */
				$aria = esc_attr( sprintf( __( '%1$s: %2$s', 'etu-blocks' ), wp_strip_all_tags( $card['buttonText'] ), get_the_title() ) );
				echo wp_kses( '<a class="etu-post-filter__more" href="' . esc_url( $permalink ) . '" aria-label="' . $aria . '">' . esc_html( $card['buttonText'] ) . '</a>', \Etublocks\Rendering::allowed() );
			}

			echo '</div></article>';
		}
		wp_reset_postdata();
		return (string) ob_get_clean();
	}

	/**
	 * REST: query posts and return rendered cards + pagination info.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response
	 */
	public static function rest_query( \WP_REST_Request $request ): \WP_REST_Response {
		$cfg = self::sanitize_config(
			array(
				'postType'   => $request['postType'],
				'perPage'    => $request['perPage'],
				'orderBy'    => $request['orderBy'],
				'order'      => $request['order'],
				'taxonomies' => array_keys( is_array( $request['tax'] ) ? $request['tax'] : array() ),
			)
		);
		$card = self::sanitize_card( is_array( $request['card'] ) ? $request['card'] : array() );

		$s     = sanitize_text_field( (string) ( $request['s'] ?? '' ) );
		$page  = max( 1, (int) ( $request['page'] ?? 1 ) );
		$terms = array();
		if ( is_array( $request['tax'] ) ) {
			foreach ( $request['tax'] as $tax => $slug ) {
				$terms[ sanitize_key( (string) $tax ) ] = sanitize_title( (string) $slug );
			}
		}

		$query = new \WP_Query( self::query_args( $cfg, $s, $terms, $page ) );
		$html  = self::render_cards( $query, $card );

		return new \WP_REST_Response(
			array(
				'html'  => $html,
				'page'  => $page,
				'pages' => (int) $query->max_num_pages,
				'total' => (int) $query->found_posts,
			),
			200
		);
	}
}
