<?php
/**
 * Settings — a small options screen for the free plugin.
 *
 * Ships three settings only, following the "if it doesn't earn a control here it
 * shouldn't be here" rule: an optional Google Maps API key (advanced users on
 * high-traffic sites), a delete-data-on-uninstall opt-in, and a toggle for the
 * Lottie helper class that lets logged-in editors upload .json / .lottie files.
 *
 * Uses the standard WordPress Settings API — `register_setting()` + `settings_fields()`
 * — so submissions post to options.php with the nonce WP already handles, and the
 * sanitize callback runs once at save time (never in the getter).
 *
 * @package Etublocks
 */

namespace Etublocks\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings screen + option accessor.
 */
final class Settings {

	const OPTION = 'etublocks_settings';
	const GROUP  = 'etublocks_settings_group';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'admin_init', array( __CLASS__, 'register_setting' ) );
	}

	/**
	 * Registered defaults — the sole source of truth for setting shape and types.
	 *
	 * @return array<string, mixed>
	 */
	public static function defaults(): array {
		return array(
			'maps_api_key'         => '',
			'delete_on_uninstall'  => false,
			'allow_lottie_uploads' => true,
		);
	}

	/**
	 * Register the option with WordPress so options.php will save it.
	 *
	 * @return void
	 */
	public static function register_setting(): void {
		register_setting(
			self::GROUP,
			self::OPTION,
			array(
				'type'              => 'array',
				'default'           => self::defaults(),
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'show_in_rest'      => false,
			)
		);
	}

	/**
	 * Sanitise the submitted settings.
	 *
	 * Called once by WordPress at save time via register_setting().
	 *
	 * @param mixed $raw Raw input from options.php.
	 * @return array<string, mixed>
	 */
	public static function sanitize( $raw ): array {
		$out = self::defaults();
		if ( ! is_array( $raw ) ) {
			return $out;
		}

		$out['maps_api_key']         = sanitize_text_field( (string) ( $raw['maps_api_key'] ?? '' ) );
		$out['delete_on_uninstall']  = ! empty( $raw['delete_on_uninstall'] );
		$out['allow_lottie_uploads'] = ! empty( $raw['allow_lottie_uploads'] );

		return $out;
	}

	/**
	 * Read one setting, merged over the defaults so keys always exist.
	 *
	 * @param string $key Setting key.
	 * @return mixed
	 */
	public static function get( string $key ) {
		$stored = get_option( self::OPTION, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}
		$merged = array_merge( self::defaults(), $stored );
		return $merged[ $key ] ?? null;
	}

	/**
	 * Render the Settings tab inside the dashboard shell.
	 *
	 * @return void
	 */
	public static function render(): void {
		$s = array_merge( self::defaults(), (array) get_option( self::OPTION, array() ) );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only flag written by options.php after its own nonce check succeeded.
		$updated = isset( $_GET['settings-updated'] ) && 'true' === sanitize_key( wp_unslash( $_GET['settings-updated'] ) );
		?>
		<section class="etu-admin__section etu-admin__settings" aria-labelledby="etu-settings-heading">
			<h2 id="etu-settings-heading" class="screen-reader-text"><?php echo esc_html__( 'Etu Blocks settings', 'etu-blocks' ); ?></h2>

			<?php if ( $updated ) : ?>
				<div class="etu-admin__notice etu-admin__notice--success" role="status">
					<?php echo esc_html__( 'Settings saved.', 'etu-blocks' ); ?>
				</div>
			<?php endif; ?>

			<form method="post" action="options.php" class="etu-admin__form">
				<?php settings_fields( self::GROUP ); ?>

				<div class="etu-admin__field">
					<label class="etu-admin__field-label" for="etu-maps-api-key">
						<?php echo esc_html__( 'Google Maps API key', 'etu-blocks' ); ?>
					</label>
					<input
						type="text"
						id="etu-maps-api-key"
						class="etu-admin__field-input"
						name="<?php echo esc_attr( self::OPTION ); ?>[maps_api_key]"
						value="<?php echo esc_attr( (string) $s['maps_api_key'] ); ?>"
						autocomplete="off"
						spellcheck="false"
					/>
					<p class="etu-admin__field-help">
						<?php echo esc_html__( 'Optional. Without a key the Google Map block uses a keyless embed that works for most sites. Add your own API key here if your site is high-traffic and you see rate-limit warnings.', 'etu-blocks' ); ?>
					</p>
				</div>

				<div class="etu-admin__field">
					<label class="etu-admin__field-check">
						<input
							type="checkbox"
							name="<?php echo esc_attr( self::OPTION ); ?>[allow_lottie_uploads]"
							value="1"
							<?php checked( ! empty( $s['allow_lottie_uploads'] ) ); ?>
						/>
						<span><?php echo esc_html__( 'Allow admins to upload .json and .lottie files to the Media Library', 'etu-blocks' ); ?></span>
					</label>
					<p class="etu-admin__field-help">
						<?php echo esc_html__( 'The Lottie block accepts URLs and file uploads. Files still have to be valid JSON (or a valid .lottie archive) to be accepted — a file with the wrong contents disguised as .json is rejected.', 'etu-blocks' ); ?>
					</p>
				</div>

				<div class="etu-admin__field">
					<label class="etu-admin__field-check">
						<input
							type="checkbox"
							name="<?php echo esc_attr( self::OPTION ); ?>[delete_on_uninstall]"
							value="1"
							<?php checked( ! empty( $s['delete_on_uninstall'] ) ); ?>
						/>
						<span><?php echo esc_html__( 'Delete all Etu Blocks data when the plugin is uninstalled', 'etu-blocks' ); ?></span>
					</label>
					<p class="etu-admin__field-help">
						<?php echo esc_html__( 'Off by default. When on, uninstalling the plugin also removes its options and caches. Your posts, pages and blocks are never touched — just the plugin\'s own settings and stored usage cache.', 'etu-blocks' ); ?>
					</p>
				</div>

				<div class="etu-admin__field-actions">
					<?php submit_button( __( 'Save changes', 'etu-blocks' ), 'primary etu-admin__btn etu-admin__btn--primary', 'submit', false ); ?>
				</div>
			</form>
		</section>
		<?php
	}
}
