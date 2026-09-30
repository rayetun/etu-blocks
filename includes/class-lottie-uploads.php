<?php
/**
 * Lottie uploads — allow admins to upload .json and .lottie animation files.
 *
 * WordPress rejects .json and .lottie uploads by default because neither is in the
 * default mime allowlist. Both are legitimate for the Lottie block: .json is a
 * plain-text Bodymovin export, .lottie is a small zip archive containing the same.
 * We allow them only for users who can already upload files (author+), and we still
 * gate .json by requiring the file to actually parse as JSON — this rejects any
 * .json-renamed file that contains PHP, HTML or scripts.
 *
 * @package Etublocks
 */

namespace Etublocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers Lottie mime types + a small safety check for .json content.
 */
final class Lottie_Uploads {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_filter( 'upload_mimes', array( __CLASS__, 'allow_mimes' ) );
		add_filter( 'wp_check_filetype_and_ext', array( __CLASS__, 'check_ext' ), 10, 4 );
		add_filter( 'wp_handle_upload_prefilter', array( __CLASS__, 'reject_unsafe_json' ) );
	}

	/**
	 * Allow .json and .lottie in the mime allowlist for users who can upload.
	 *
	 * @param array<string,string> $mimes Existing allowed mimes.
	 * @return array<string,string>
	 */
	public static function allow_mimes( $mimes ) {
		if ( ! is_array( $mimes ) ) {
			return $mimes;
		}
		// Respect the Settings tab toggle (default on). A site can opt out.
		if ( ! \Etublocks\Admin\Settings::get( 'allow_lottie_uploads' ) ) {
			return $mimes;
		}
		if ( current_user_can( 'upload_files' ) ) {
			$mimes['json']   = 'application/json';
			$mimes['lottie'] = 'application/zip';
		}
		return $mimes;
	}

	/**
	 * Re-approve .json/.lottie files that WordPress' extra sniff would otherwise
	 * strip because the ext/type combination isn't in its finfo tables.
	 *
	 * @param array  $data     File data (ext/type/proper_filename).
	 * @param string $file     Absolute file path.
	 * @param string $filename Provided filename.
	 * @param array  $mimes    Allowed mimes.
	 * @return array
	 */
	public static function check_ext( $data, $file, $filename, $mimes ) {
		$ext = strtolower( pathinfo( (string) $filename, PATHINFO_EXTENSION ) );
		if ( 'json' === $ext ) {
			$data['ext']  = 'json';
			$data['type'] = 'application/json';
		} elseif ( 'lottie' === $ext ) {
			$data['ext']  = 'lottie';
			$data['type'] = 'application/zip';
		}
		return $data;
	}

	/**
	 * Reject any .json upload that isn't valid JSON — stops someone smuggling
	 * PHP/HTML/JS through a .json extension.
	 *
	 * @param array $file $_FILES entry.
	 * @return array
	 */
	public static function reject_unsafe_json( $file ) {
		if ( ! isset( $file['name'], $file['tmp_name'] ) ) {
			return $file;
		}
		$ext = strtolower( pathinfo( (string) $file['name'], PATHINFO_EXTENSION ) );
		if ( 'json' !== $ext ) {
			return $file;
		}
		$content = @file_get_contents( (string) $file['tmp_name'], false, null, 0, 200 * 1024 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading an uploaded temp file to validate its JSON shape.
		if ( false === $content || null === json_decode( $content ) ) {
			$file['error'] = __( 'This file is not valid JSON.', 'etu-blocks' );
		}
		return $file;
	}
}
