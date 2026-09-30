<?php
/**
 * Schemas — the single source of truth for each block's controls + CSS.
 *
 * Every block declares one schema (includes/blocks/<slug>/schema.php) that drives
 * its Inspector UI, its attributes, and its generated CSS. Schemas are pure data
 * (JSON-serializable), authored in PHP because the CSS generator needs them
 * server-side, and exposed to the editor so the shared Inspector renderer builds
 * the same controls. See references/editor-architecture.md.
 *
 * @package Etublocks
 */

namespace Etublocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads and exposes per-block control schemas.
 */
final class Schemas {

	/**
	 * Cache of loaded schemas, keyed by block name.
	 *
	 * @var array<string, array>|null
	 */
	private static $schemas = null;

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'enqueue_block_editor_assets', [ __CLASS__, 'expose_to_editor' ], 5 );
	}

	/**
	 * All schemas, keyed by block name (e.g. "etu/tabs").
	 *
	 * @return array<string, array>
	 */
	public static function all(): array {
		if ( null !== self::$schemas ) {
			return self::$schemas;
		}

		self::$schemas = [];
		$files = glob( ETUBLOCKS_DIR . 'includes/blocks/*/schema.php' );
		if ( is_array( $files ) ) {
			foreach ( $files as $file ) {
				$slug   = basename( dirname( $file ) );
				$schema = require $file;
				if ( is_array( $schema ) ) {
					self::$schemas[ 'etu/' . $slug ] = $schema;
				}
			}
		}

		return self::$schemas;
	}

	/**
	 * One block's schema, or null.
	 *
	 * @param string $name Block name.
	 * @return array|null
	 */
	public static function get( string $name ): ?array {
		$all = self::all();
		return $all[ $name ] ?? null;
	}

	/**
	 * Expose all schemas to the block editor as `window.etublocksSchemas`.
	 *
	 * @return void
	 */
	public static function expose_to_editor(): void {
		$json = wp_json_encode( self::all() );
		if ( false === $json ) {
			$json = '{}';
		}
		wp_add_inline_script( 'wp-blocks', 'window.etublocksSchemas = ' . $json . ';', 'before' );
	}
}
