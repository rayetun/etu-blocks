<?php
/**
 * CSS Generator — turns a block's schema + saved attributes into scoped CSS.
 *
 * Walks the block's schema (Schemas::get), reads each control's value and its
 * `css` descriptor, and emits rules scoped under a per-instance class so styles
 * never leak. Responsive values become base + breakpoint media queries. Every
 * value is sanitised against its control type before printing — a raw attribute
 * is never echoed. Output is cached per attribute-set. See
 * references/editor-architecture.md.
 *
 * @package Etublocks
 */

namespace Etublocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Schema-driven CSS generator.
 */
final class CSS_Generator {

	/**
	 * Max-width breakpoints (px) for the responsive tiers.
	 */
	const BREAKPOINTS = [
		'tablet' => 1024,
		'mobile' => 767,
	];

	/**
	 * Shared handle used to enqueue per-instance inline CSS from server-side
	 * block render. Registered lazily on first use.
	 */
	const INLINE_HANDLE = 'etu-blocks-inline';

	/**
	 * Attach generated per-instance CSS to WordPress' style pipeline via
	 * `wp_add_inline_style()` rather than echoing raw `<style>` tags — the
	 * WordPress.org-recommended pattern for dynamic block CSS.
	 *
	 * The style is attached to the block's own registered style handle when
	 * present (registered automatically by core from `block.json`'s `style`
	 * field, e.g. block "etu/modal" → handle "etu-modal-style"); otherwise it
	 * attaches to a shared empty handle registered on demand.
	 *
	 * @param string $block_name Full block name, e.g. "etu/modal".
	 * @param string $css        Generated CSS. No-op when empty.
	 * @return void
	 */
	public static function inject( string $block_name, string $css ): void {
		if ( '' === $css ) {
			return;
		}
		// Core's generate_block_asset_handle() produces "{namespace}-{name}-style"
		// for non-core blocks.
		$handle = str_replace( '/', '-', $block_name ) . '-style';
		if ( ! wp_style_is( $handle, 'registered' ) ) {
			if ( ! wp_style_is( self::INLINE_HANDLE, 'registered' ) ) {
				wp_register_style( self::INLINE_HANDLE, false, array(), ETUBLOCKS_VERSION );
			}
			if ( ! wp_style_is( self::INLINE_HANDLE, 'enqueued' ) ) {
				wp_enqueue_style( self::INLINE_HANDLE );
			}
			$handle = self::INLINE_HANDLE;
		}
		wp_add_inline_style( $handle, $css );
	}

	/**
	 * Generate the CSS for one block instance.
	 *
	 * @param string $name       Block name (e.g. "etu/tabs").
	 * @param array  $attributes Block attributes.
	 * @param string $scope      Per-instance selector (e.g. ".etu-tabs-abc123").
	 * @return string CSS, or '' when nothing to output.
	 */
	public static function for_block( string $name, array $attributes, string $scope ): string {
		$schema = Schemas::get( $name );
		if ( ! $schema || empty( $schema['sections'] ) || '' === $scope ) {
			return '';
		}

		$key    = 'etublocks_css_' . md5( $name . '|' . (string) wp_json_encode( $attributes ) . '|' . $scope . '|' . ETUBLOCKS_VERSION );
		$cached = wp_cache_get( $key, 'etublocks' );
		if ( is_string( $cached ) ) {
			return $cached;
		}

		// buckets[ device ][ selector ][ prop ] = value.
		$buckets = [
			'base'   => [],
			'tablet' => [],
			'mobile' => [],
		];

		foreach ( $schema['sections'] as $section ) {
			foreach ( ( $section['controls'] ?? [] ) as $control ) {
				if ( empty( $control['css'] ) || empty( $control['key'] ) ) {
					continue;
				}
				$raw = $attributes[ $control['key'] ] ?? null;
				if ( null === $raw || '' === $raw || [] === $raw ) {
					continue;
				}

				$selector = trim( $scope . ' ' . (string) ( $control['css']['selector'] ?? '' ) );
				$decls    = self::declarations( $control, $raw );

				foreach ( $decls as $device => $props ) {
					foreach ( $props as $prop => $val ) {
						if ( '' === $val ) {
							continue;
						}
						$buckets[ $device ][ $selector ][ $prop ] = $val;
					}
				}
			}
		}

		$css = self::render_rules( $buckets['base'] );
		if ( ! empty( $buckets['tablet'] ) ) {
			$css .= '@media (max-width:' . self::BREAKPOINTS['tablet'] . 'px){' . self::render_rules( $buckets['tablet'] ) . '}';
		}
		if ( ! empty( $buckets['mobile'] ) ) {
			$css .= '@media (max-width:' . self::BREAKPOINTS['mobile'] . 'px){' . self::render_rules( $buckets['mobile'] ) . '}';
		}

		wp_cache_set( $key, $css, 'etublocks' );
		return $css;
	}

	/**
	 * Build the per-device property declarations for one control.
	 *
	 * @param array $control Control schema entry.
	 * @param mixed $raw     Raw attribute value.
	 * @return array<string, array<string, string>> device => [ prop => value ].
	 */
	private static function declarations( array $control, $raw ): array {
		$out      = [ 'base' => [], 'tablet' => [], 'mobile' => [] ];
		$type     = (string) ( $control['type'] ?? '' );
		$property = (string) ( $control['css']['property'] ?? '' );

		switch ( $type ) {
			case 'range':
			case 'unit':
				if ( ! empty( $control['responsive'] ) && is_array( $raw ) ) {
					foreach ( [ 'desktop' => 'base', 'tablet' => 'tablet', 'mobile' => 'mobile' ] as $device => $bucket ) {
						if ( isset( $raw[ $device ] ) ) {
							$val = self::length( $raw[ $device ], $control );
							if ( '' !== $val && '' !== $property ) {
								$out[ $bucket ][ $property ] = $val;
							}
						}
					}
				} else {
					$val = self::length( $raw, $control );
					if ( '' !== $val && '' !== $property ) {
						$out['base'][ $property ] = $val;
					}
				}
				break;

			case 'color':
				$val = self::color( $raw );
				if ( '' !== $val && '' !== $property ) {
					$out['base'][ $property ] = $val;
				}
				break;

			case 'alignment':
				$val = self::alignment( (string) $raw, $property );
				if ( '' !== $val ) {
					$out['base'][ '' !== $property ? $property : 'justify-content' ] = $val;
				}
				break;

			case 'typography':
				if ( is_array( $raw ) ) {
					$out['base'] = self::typography( $raw );
				}
				break;

			case 'spacing':
				if ( is_array( $raw ) ) {
					$prop = '' !== $property ? $property : 'padding';
					foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
						if ( empty( $raw[ $side ] ) ) {
							continue;
						}
						$len = self::length_string( (string) $raw[ $side ] );
						if ( '' !== $len ) {
							$out['base'][ $prop . '-' . $side ] = $len;
						}
					}
				}
				break;
		}

		return $out;
	}

	/**
	 * Format a length value ({value,unit} or number) with an allow-listed unit.
	 *
	 * @param mixed $raw     Value.
	 * @param array $control Control schema entry.
	 * @return string
	 */
	private static function length( $raw, array $control ): string {
		$units   = ! empty( $control['units'] ) && is_array( $control['units'] ) ? $control['units'] : [ 'px' ];
		$default = (string) ( $units[0] ?? 'px' );

		if ( is_array( $raw ) && isset( $raw['value'] ) ) {
			$num  = $raw['value'];
			$unit = (string) ( $raw['unit'] ?? $default );
		} elseif ( is_numeric( $raw ) ) {
			$num  = $raw;
			$unit = $default;
		} else {
			return '';
		}

		if ( ! is_numeric( $num ) ) {
			return '';
		}
		$num = (float) $num;

		if ( isset( $control['min'] ) ) {
			$num = max( (float) $control['min'], $num );
		}
		if ( isset( $control['max'] ) ) {
			$num = min( (float) $control['max'], $num );
		}
		if ( ! in_array( $unit, $units, true ) ) {
			$unit = $default;
		}

		// Trim a trailing .0 for integers.
		$num_str = ( floor( $num ) === $num ) ? (string) (int) $num : (string) $num;
		return $num_str . $unit;
	}

	/**
	 * Validate a pre-formatted length string (e.g. "12px"), else ''.
	 *
	 * @param string $val Value.
	 * @return string
	 */
	private static function length_string( string $val ): string {
		$val = trim( $val );
		if ( preg_match( '/^-?\d*\.?\d+(px|em|rem|%|vh|vw)$/', $val ) ) {
			return $val;
		}
		return '';
	}

	/**
	 * Sanitise a colour to a hex or rg(a)/hsl(a) value, else ''.
	 *
	 * @param mixed $raw Value.
	 * @return string
	 */
	private static function color( $raw ): string {
		$val = trim( (string) $raw );
		if ( '' === $val ) {
			return '';
		}
		if ( preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $val ) ) {
			return $val;
		}
		if ( preg_match( '/^(rgb|rgba|hsl|hsla)\(\s*[0-9.,%\s\/]+\)$/i', $val ) ) {
			return $val;
		}
		return '';
	}

	/**
	 * Map an alignment keyword to a value for the given property.
	 *
	 * @param string $val      One of left|center|right|justify.
	 * @param string $property Target CSS property.
	 * @return string
	 */
	private static function alignment( string $val, string $property ): string {
		$val = strtolower( trim( $val ) );
		if ( 'justify-content' === $property || '' === $property ) {
			$map = [
				'left'    => 'flex-start',
				'center'  => 'center',
				'right'   => 'flex-end',
				'justify' => 'space-between',
			];
			return $map[ $val ] ?? '';
		}
		// text-align and friends accept the keyword directly.
		return in_array( $val, [ 'left', 'center', 'right', 'justify' ], true ) ? $val : '';
	}

	/**
	 * Map a typography object to CSS declarations.
	 *
	 * @param array $raw Typography value.
	 * @return array<string, string>
	 */
	private static function typography( array $raw ): array {
		$out = [];

		if ( ! empty( $raw['fontSize'] ) ) {
			$val = self::length( $raw['fontSize'], [ 'units' => [ 'px', 'em', 'rem', '%' ] ] );
			if ( '' !== $val ) {
				$out['font-size'] = $val;
			}
		}
		if ( ! empty( $raw['fontWeight'] ) ) {
			$weight = (string) $raw['fontWeight'];
			if ( preg_match( '/^(100|200|300|400|500|600|700|800|900|normal|bold|lighter|bolder)$/', $weight ) ) {
				$out['font-weight'] = $weight;
			}
		}
		if ( ! empty( $raw['letterSpacing'] ) ) {
			$val = self::length( $raw['letterSpacing'], [ 'units' => [ 'px', 'em', 'rem' ] ] );
			if ( '' !== $val ) {
				$out['letter-spacing'] = $val;
			}
		}
		if ( ! empty( $raw['textTransform'] ) ) {
			$transform = (string) $raw['textTransform'];
			if ( in_array( $transform, [ 'none', 'uppercase', 'lowercase', 'capitalize' ], true ) ) {
				$out['text-transform'] = $transform;
			}
		}
		if ( ! empty( $raw['lineHeight'] ) && is_numeric( $raw['lineHeight'] ) ) {
			$out['line-height'] = (string) (float) $raw['lineHeight'];
		}

		return $out;
	}

	/**
	 * Render a [ selector => [ prop => val ] ] map to a CSS string.
	 *
	 * @param array<string, array<string, string>> $rules Rules.
	 * @return string
	 */
	private static function render_rules( array $rules ): string {
		$css = '';
		foreach ( $rules as $selector => $props ) {
			if ( empty( $props ) ) {
				continue;
			}
			$body = '';
			foreach ( $props as $prop => $val ) {
				$body .= $prop . ':' . $val . ';';
			}
			$css .= $selector . '{' . $body . '}';
		}
		return $css;
	}
}
