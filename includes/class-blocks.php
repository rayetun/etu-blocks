<?php
/**
 * Block registrar.
 *
 * Registers every built block by discovering its `block.json` under `build/`,
 * behind a filter so the Pro add-on (or third parties) can add or remove blocks
 * without forking the free base. Blocks are dynamic (`render.php`) and, where
 * interactive, ship a view script module + Interactivity API store.
 *
 * @package Etublocks
 */

namespace Etublocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Discovers and registers the plugin's blocks.
 */
class Blocks {

	/**
	 * Block category slug used by all Etu blocks.
	 */
	const CATEGORY = 'etu-blocks';

	/**
	 * Option holding the list of disabled block names (e.g. "etu/faq").
	 */
	const DISABLED_OPTION = 'etublocks_disabled';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_filter( 'block_categories_all', [ __CLASS__, 'register_category' ], 10, 1 );
		add_action( 'init', [ __CLASS__, 'register_blocks' ] );
	}

	/**
	 * Add the "Etu Blocks" category to the inserter.
	 *
	 * @param array<int, array<string, mixed>> $categories Existing categories.
	 * @return array<int, array<string, mixed>>
	 */
	public static function register_category( $categories ) {
		if ( ! is_array( $categories ) ) {
			return $categories;
		}

		array_unshift(
			$categories,
			[
				'slug'  => self::CATEGORY,
				'title' => __( 'Etu Blocks', 'etu-blocks' ),
				'icon'  => null,
			]
		);

		return $categories;
	}

	/**
	 * Register all discovered blocks.
	 *
	 * @return void
	 */
	public static function register_blocks(): void {
		$disabled = self::disabled();

		foreach ( self::block_paths() as $path ) {
			if ( ! is_dir( $path ) || ! file_exists( $path . '/block.json' ) ) {
				continue;
			}

			// A "disabled" block is not registered at all: it disappears from the
			// inserter and any existing usage renders WordPress's "block not found"
			// placeholder, exactly as if the block did not exist.
			if ( $disabled ) {
				$meta = wp_json_file_decode( $path . '/block.json', array( 'associative' => true ) );
				$name = is_array( $meta ) && isset( $meta['name'] ) ? (string) $meta['name'] : '';
				if ( '' !== $name && in_array( $name, $disabled, true ) ) {
					continue;
				}
			}

			register_block_type( $path );
		}
	}

	/**
	 * Block names the site owner has disabled from the dashboard.
	 *
	 * @return array<int, string>
	 */
	public static function disabled(): array {
		$disabled = get_option( self::DISABLED_OPTION, array() );
		if ( ! is_array( $disabled ) ) {
			return array();
		}
		return array_values( array_filter( array_map( 'strval', $disabled ) ) );
	}

	/**
	 * The list of block directories to register.
	 *
	 * Discovered from `build/` and passed through a filter so add-ons can extend
	 * or trim the set.
	 *
	 * @return array<int, string> Absolute paths to block directories.
	 */
	public static function block_paths(): array {
		$paths = [];
		$build = ETUBLOCKS_DIR . 'build';

		if ( is_dir( $build ) ) {
			$iterator = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator( $build, \FilesystemIterator::SKIP_DOTS )
			);
			foreach ( $iterator as $file ) {
				if ( 'block.json' === $file->getFilename() ) {
					$paths[] = dirname( $file->getPathname() );
				}
			}
			$paths = array_values( array_unique( $paths ) );
		}

		/**
		 * Filters the block directories Etu registers.
		 *
		 * @since 1.0.0
		 *
		 * @param array<int, string> $paths Absolute paths to block directories.
		 */
		return (array) apply_filters( 'etublocks_register_blocks', $paths );
	}
}
