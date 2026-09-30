<?php
/**
 * Table of Contents — heading collection + anchor injection.
 *
 * The TOC block links to the page's headings, so the headings need matching
 * `id` anchors. We collect headings once from the post's block markup (with
 * stable, de-duplicated ids) and, when a TOC block is present on a singular
 * view, inject those same ids into the rendered headings — matched by order so
 * the links and anchors always agree.
 *
 * @package Etublocks
 */

namespace Etublocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the heading map and keeps heading anchors in sync with the TOC links.
 */
final class TOC {

	/**
	 * Per-request cache of collected headings, keyed by post ID.
	 *
	 * @var array<int, array<int, array{level:int,text:string,id:string}>>
	 */
	private static $cache = array();

	/**
	 * Counter used while injecting ids into rendered headings.
	 *
	 * @var int
	 */
	private static $counter = 0;

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'wp', array( __CLASS__, 'maybe_enable' ) );
	}

	/**
	 * On singular views that contain a TOC block, keep heading anchors in sync.
	 *
	 * @return void
	 */
	public static function maybe_enable(): void {
		if ( ! is_singular() ) {
			return;
		}
		$post = get_queried_object();
		if ( $post instanceof \WP_Post && has_block( 'etu/toc', $post ) ) {
			self::$counter = 0;
			add_filter( 'render_block_core/heading', array( __CLASS__, 'add_anchor' ), 10, 2 );
		}
	}

	/**
	 * Collected headings for a post, with final (de-duplicated) ids.
	 *
	 * @param int $post_id Post ID.
	 * @return array<int, array{level:int,text:string,id:string}>
	 */
	public static function get_headings( int $post_id ): array {
		if ( isset( self::$cache[ $post_id ] ) ) {
			return self::$cache[ $post_id ];
		}

		$post = get_post( $post_id );
		$flat = array();
		if ( $post instanceof \WP_Post ) {
			self::walk( parse_blocks( $post->post_content ), $flat );
		}

		$used = array();
		foreach ( $flat as &$item ) {
			$base = '' !== $item['anchor'] ? $item['anchor'] : sanitize_title( $item['text'] );
			if ( '' === $base ) {
				$base = 'section';
			}
			$id = $base;
			$n  = 2;
			while ( isset( $used[ $id ] ) ) {
				$id = $base . '-' . $n;
				++$n;
			}
			$used[ $id ] = true;
			$item['id']  = $id;
			unset( $item['anchor'] );
		}
		unset( $item );

		self::$cache[ $post_id ] = $flat;
		return $flat;
	}

	/**
	 * Recursively collect core/heading blocks in document order.
	 *
	 * @param array $blocks Parsed blocks.
	 * @param array $out    Accumulator (by reference).
	 * @return void
	 */
	private static function walk( array $blocks, array &$out ): void {
		foreach ( $blocks as $block ) {
			if ( 'core/heading' === ( $block['blockName'] ?? '' ) ) {
				$text = trim( wp_strip_all_tags( (string) ( $block['innerHTML'] ?? '' ) ) );
				if ( '' !== $text ) {
					$out[] = array(
						'level'  => (int) ( $block['attrs']['level'] ?? 2 ),
						'text'   => $text,
						'anchor' => (string) ( $block['attrs']['anchor'] ?? '' ),
					);
				}
			}
			if ( ! empty( $block['innerBlocks'] ) ) {
				self::walk( $block['innerBlocks'], $out );
			}
		}
	}

	/**
	 * Inject the matching id into a rendered heading that has none.
	 *
	 * @param string $content Block HTML.
	 * @param array  $block   Parsed block.
	 * @return string
	 */
	public static function add_anchor( string $content, array $block ): string {
		$headings = self::get_headings( get_the_ID() );
		$index    = self::$counter++;

		if ( ! isset( $headings[ $index ] ) ) {
			return $content;
		}
		$id = $headings[ $index ]['id'];

		// Already anchored — leave it (the id list already used that anchor).
		if ( '' === $id || false !== strpos( $content, ' id=' ) ) {
			return $content;
		}

		return preg_replace(
			'/<h([1-6])((?:\s[^>]*)?)>/i',
			'<h$1$2 id="' . esc_attr( $id ) . '">',
			$content,
			1
		);
	}
}
