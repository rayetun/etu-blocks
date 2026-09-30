<?php
/**
 * Rendering — the allowed-HTML list used to escape block markup on output.
 *
 * Dynamic blocks build their markup from static plugin strings plus values that
 * are individually escaped with `esc_html()`, `esc_attr()`, `esc_url()` etc. at
 * the point they are added. When such a fragment is echoed, it is passed through
 * `wp_kses()` with {@see Rendering::allowed()} so the escaping is applied late,
 * with the correct function for the context, and is plainly visible at the call
 * site — e.g. `echo wp_kses( $html, \Etublocks\Rendering::allowed() );`.
 *
 * The list starts from WordPress' `post` context (which already permits the
 * Interactivity API `data-wp-*` directives and `aria-*`/`role` on standard
 * elements) and adds the inline SVG and media attributes the icon set needs.
 * `wp_kses()` lower-cases `viewBox` to `viewbox`; the HTML parser's SVG
 * attribute-adjustment step restores the correct casing, so icons still scale.
 *
 * This class does not output anything and never echoes an arbitrary argument —
 * it only supplies the allow-list that `wp_kses()` enforces.
 *
 * @package Etublocks
 */

namespace Etublocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Supplies the wp_kses() allow-list for block render output.
 */
final class Rendering {

	/**
	 * Cached allow-list, built once per request.
	 *
	 * @var array<string,mixed>|null
	 */
	private static $allowed = null;

	/**
	 * The allow-list for `wp_kses()` when escaping block markup on output.
	 *
	 * @return array<string,mixed> Element => allowed-attributes map.
	 */
	public static function allowed(): array {
		if ( null !== self::$allowed ) {
			return self::$allowed;
		}

		$allowed = wp_kses_allowed_html( 'post' );

		// Media attributes core's `post` list omits.
		if ( isset( $allowed['img'] ) ) {
			$allowed['img'] = array_merge(
				$allowed['img'],
				array(
					'decoding'      => true,
					'fetchpriority' => true,
					'srcset'        => true,
					'sizes'         => true,
					'width'         => true,
					'height'        => true,
					'loading'       => true,
				)
			);
		}

		// Inline SVG icons. Attributes are static; `data-*`/`aria-*` on these
		// elements are listed explicitly because the global allowance applies
		// only to the elements in core's own list.
		$svg_attr = array(
			'class'               => true,
			'style'               => true,
			'id'                  => true,
			'role'                => true,
			'focusable'           => true,
			'aria-hidden'         => true,
			'aria-label'          => true,
			'aria-labelledby'     => true,
			'xmlns'               => true,
			'viewbox'             => true,
			'preserveaspectratio' => true,
			'width'               => true,
			'height'              => true,
			'fill'                => true,
			'fill-rule'           => true,
			'clip-rule'           => true,
			'stroke'              => true,
			'stroke-width'        => true,
			'stroke-linecap'      => true,
			'stroke-linejoin'     => true,
			'stroke-dasharray'    => true,
			'stroke-dashoffset'   => true,
			'stroke-miterlimit'   => true,
			'd'                   => true,
			'points'              => true,
			'transform'           => true,
			'opacity'             => true,
			'offset'              => true,
			'stop-color'          => true,
			'stop-opacity'        => true,
			'gradientunits'       => true,
			'gradienttransform'   => true,
			'cx'                  => true,
			'cy'                  => true,
			'r'                   => true,
			'rx'                  => true,
			'ry'                  => true,
			'x'                   => true,
			'y'                   => true,
			'x1'                  => true,
			'y1'                  => true,
			'x2'                  => true,
			'y2'                  => true,
		);
		foreach (
			array(
				'svg',
				'path',
				'g',
				'circle',
				'rect',
				'line',
				'polyline',
				'polygon',
				'ellipse',
				'use',
				'defs',
				'clippath',
				'title',
				'desc',
				'stop',
				'lineargradient',
				'radialgradient',
				'mask',
				'pattern',
				'symbol',
			) as $svg_tag
		) {
			$allowed[ $svg_tag ] = $svg_attr;
		}

		self::$allowed = $allowed;
		return $allowed;
	}
}
