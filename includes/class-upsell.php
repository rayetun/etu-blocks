<?php
/**
 * Upsell — soft "Pro is available" nudge inside the free dashboard.
 *
 * The upsell is a single card rendered at the end of the blocks section. It is
 * hidden by default and only appears when *both* of these are true:
 *
 * 1. Etu Blocks Pro is NOT active (via Pro::is_active()).
 * 2. The `etublocks_show_upsell` filter returns true.
 *
 * The filter defaults to false in the free 1.0.0 release so nothing visible ships
 * until Pro is ready. When Pro launches, a free 1.1.0 release flips the default to
 * true. The action point `etublocks_dashboard_upsell` lets Pro or third parties
 * substitute their own card if they want.
 *
 * @package Etublocks
 */

namespace Etublocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the free-plugin upsell card. Dormant until Pro launches.
 */
final class Upsell {

	/**
	 * URL where Pro information will live once it's released. Overridable so users
	 * or affiliates can point it wherever they need.
	 */
	const DEFAULT_URL = 'https://etublock.com/pro/';

	/**
	 * Render the upsell card in the dashboard.
	 *
	 * Called from the dashboard main column. Returns silently unless the upsell is
	 * both applicable (Pro not active) and enabled (filter returns true).
	 *
	 * @return void
	 */
	public static function render_dashboard_card(): void {
		if ( Pro::is_active() ) {
			return;
		}

		/**
		 * Filters whether the free plugin's Pro upsell card is shown.
		 *
		 * Defaults to false in Etu Blocks 1.0.0 — nothing ships visible until the
		 * Pro add-on is publicly available. A later free release flips the default.
		 * Sites can also disable the upsell entirely by returning false here.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $show Whether to render the upsell. Default false.
		 */
		if ( ! apply_filters( 'etublocks_show_upsell', false ) ) {
			return;
		}

		/**
		 * Fires in place of the default upsell card so an add-on can render its own.
		 *
		 * If any callback is attached, the default card is skipped so the add-on has
		 * full control of the slot.
		 *
		 * @since 1.0.0
		 */
		if ( has_action( 'etublocks_dashboard_upsell' ) ) {
			do_action( 'etublocks_dashboard_upsell' );
			return;
		}

		$url = (string) apply_filters( 'etublocks_upsell_url', self::DEFAULT_URL );
		?>
		<section class="etu-admin__upsell" aria-labelledby="etu-upsell-heading">
			<div class="etu-admin__upsell-body">
				<p class="etu-admin__upsell-eyebrow"><?php echo esc_html__( 'Etu Blocks Pro', 'etu-blocks' ); ?></p>
				<h2 id="etu-upsell-heading" class="etu-admin__upsell-title">
					<?php echo esc_html__( 'Get more blocks and premium features', 'etu-blocks' ); ?>
				</h2>
				<p class="etu-admin__upsell-text">
					<?php echo esc_html__( 'Extend Etu Blocks with premium blocks, template kits and priority support — built on the same accessible foundation.', 'etu-blocks' ); ?>
				</p>
			</div>
			<div class="etu-admin__upsell-actions">
				<a class="etu-admin__btn etu-admin__btn--primary" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php echo esc_html__( 'Learn more', 'etu-blocks' ); ?>
				</a>
			</div>
		</section>
		<?php
	}
}
