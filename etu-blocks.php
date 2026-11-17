<?php
/**
 * Plugin Name:       Etu Blocks – Accessible Blocks & Page Builder for the Block Editor
 * Description:       Build pages everyone can use. Accessible, interactive page builder blocks for the block editor, built to WCAG 2.2 AA.
 * Version:           1.0.1
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Etu
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       etu-blocks
 * Domain Path:       /languages
 *
 * @package Etublocks
 */

namespace Etublocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ETUBLOCKS_VERSION', '1.0.1' );
define( 'ETUBLOCKS_FILE', __FILE__ );
define( 'ETUBLOCKS_DIR', plugin_dir_path( __FILE__ ) );
define( 'ETUBLOCKS_URL', plugin_dir_url( __FILE__ ) );
define( 'ETUBLOCKS_MIN_WP', '6.7' );

require_once ETUBLOCKS_DIR . 'includes/class-pro.php';
require_once ETUBLOCKS_DIR . 'includes/class-upsell.php';
require_once ETUBLOCKS_DIR . 'includes/class-schemas.php';
require_once ETUBLOCKS_DIR . 'includes/class-css-generator.php';
require_once ETUBLOCKS_DIR . 'includes/class-rendering.php';
require_once ETUBLOCKS_DIR . 'includes/class-blocks.php';
require_once ETUBLOCKS_DIR . 'includes/class-toc.php';
require_once ETUBLOCKS_DIR . 'includes/class-post-filter.php';
require_once ETUBLOCKS_DIR . 'includes/class-lottie-uploads.php';
require_once ETUBLOCKS_DIR . 'includes/admin/class-settings.php';
require_once ETUBLOCKS_DIR . 'includes/admin/class-dashboard.php';

/**
 * Boot the plugin once WordPress and plugins are loaded.
 */
function bootstrap() {
	if ( version_compare( get_bloginfo( 'version' ), ETUBLOCKS_MIN_WP, '<' ) ) {
		add_action( 'admin_notices', __NAMESPACE__ . '\\wp_version_notice' );
		return;
	}

	Schemas::init();
	Blocks::init();
	TOC::init();
	Post_Filter::init();
	Lottie_Uploads::init();
	Admin\Settings::init();
	Admin\Dashboard::init();

	/**
	 * Fires after the free base has booted, before Pro add-ons wire in.
	 *
	 * Pro / third-party add-ons hook here to register blocks, dashboard sections,
	 * and settings against the base's public API.
	 *
	 * @since 1.0.0
	 */
	do_action( 'etublocks_loaded' );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );

/**
 * Admin notice shown when the site's WordPress version is unsupported.
 */
function wp_version_notice() {
	printf(
		'<div class="notice notice-error"><p>%s</p></div>',
		esc_html(
			sprintf(
				/* translators: %s: minimum WordPress version. */
				__( 'Etu Blocks requires WordPress %s or higher.', 'etu-blocks' ),
				ETUBLOCKS_MIN_WP
			)
		)
	);
}
