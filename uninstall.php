<?php
/**
 * Uninstall handler.
 *
 * Runs when a user deletes the plugin from Plugins → Delete (never on deactivate).
 * By default the plugin's options and caches stay put so a re-install picks up where
 * things left off. The Settings tab has a "Delete all Etu Blocks data when the plugin
 * is uninstalled" checkbox — when that's on, this file removes everything the plugin
 * ever stored.
 *
 * Your posts, pages, and blocks are never touched, either way.
 *
 * @package Etublocks
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$etublocks_settings = get_option( 'etublocks_settings', array() );
if ( ! is_array( $etublocks_settings ) || empty( $etublocks_settings['delete_on_uninstall'] ) ) {
	return;
}

// Options.
delete_option( 'etublocks_disabled' );
delete_option( 'etublocks_settings' );

// Fixed-key transients.
delete_transient( 'etublocks_usage' );

global $wpdb;

// Prefix-scoped transients (per-taxonomy image cache, per-block CSS cache).
// Every key we create starts with `etublocks_`, so this LIKE scan is safe.
$etublocks_prefixes = array(
	'_transient_etublocks_taximg_',
	'_transient_timeout_etublocks_taximg_',
	'_transient_etublocks_css_',
	'_transient_timeout_etublocks_css_',
);
foreach ( $etublocks_prefixes as $etublocks_prefix ) {
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Uninstall cleanup; caching is irrelevant here.
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
			$wpdb->esc_like( $etublocks_prefix ) . '%'
		)
	);
}
