<?php
/**
 * Pro gating service.
 *
 * The single source of truth for "is this capability available?". The free base
 * always answers "no" for Pro-only features; the separate Etu Blocks Pro add-on
 * (distributed off WordPress.org) filters these answers to "yes" when it is
 * active and licensed. No Pro feature code ever ships in the free plugin — only
 * these seams that Pro attaches to.
 *
 * @package Etublocks
 */

namespace Etublocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Free/Pro feature gate.
 */
final class Pro {

	/**
	 * Whether the Etu Blocks Pro add-on is active and licensed.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_active(): bool {
		/**
		 * Filters whether Pro is active. The Pro add-on returns true once licensed.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $active Default false in the free base.
		 */
		return (bool) apply_filters( 'etublocks_pro_active', false );
	}

	/**
	 * Whether a named feature is available on this install.
	 *
	 * Free code calls this before rendering a premium control and shows an upsell
	 * when it returns false. Pro registers the real implementation and filters the
	 * matching flag to true.
	 *
	 * @since 1.0.0
	 *
	 * @param string $feature Feature slug, e.g. 'dynamic_content'.
	 * @return bool
	 */
	public static function has( string $feature ): bool {
		$feature = sanitize_key( $feature );

		/**
		 * Filters availability of a single named feature.
		 *
		 * @since 1.0.0
		 *
		 * @param bool   $available Default false in the free base.
		 * @param string $feature   Feature slug.
		 */
		return (bool) apply_filters( "etublocks_feature_{$feature}", false, $feature );
	}
}
