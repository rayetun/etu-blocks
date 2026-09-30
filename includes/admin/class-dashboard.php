<?php
/**
 * Admin dashboard.
 *
 * A SaaS-style settings screen (see the Etu design system) rendered server-side
 * and styled with scoped design tokens. The block manager lets the owner search,
 * filter by type, and enable/disable each block — showing how often each is used
 * so nothing in use is turned off by accident. Its section registry is filterable
 * so an "Etu Blocks Pro" add-on can inject its own panels without editing the base.
 *
 * @package Etublocks
 */

namespace Etublocks\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Etublocks\Blocks;

/**
 * Registers and renders the plugin's admin dashboard.
 */
class Dashboard {

	const PAGE_SLUG    = 'etu-blocks';
	const USAGE_CACHE  = 'etublocks_usage';
	const SUPPORT_URL  = 'https://wordpress.org/support/plugin/etu-blocks/';
	const REVIEW_URL   = 'https://wordpress.org/support/plugin/etu-blocks/reviews/#new-post';
	// Filter with etublocks_donate_url to override.
	const DONATE_URL   = 'https://wise.com/pay/me/mdrayhanu2';

	/**
	 * Menu hook suffix, used to scope asset loading to this screen only.
	 *
	 * @var string
	 */
	private static $hook_suffix = '';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'wp_ajax_etublocks_toggle', array( __CLASS__, 'ajax_toggle' ) );
		add_action( 'save_post', array( __CLASS__, 'clear_usage_cache' ) );
		add_action( 'deleted_post', array( __CLASS__, 'clear_usage_cache' ) );
	}

	/**
	 * Add the top-level "Etu Blocks" menu.
	 *
	 * @return void
	 */
	public static function register_menu(): void {
		self::$hook_suffix = (string) add_menu_page(
			__( 'Etu Blocks', 'etu-blocks' ),
			__( 'Etu Blocks', 'etu-blocks' ),
			'manage_options',
			self::PAGE_SLUG,
			array( __CLASS__, 'render' ),
			'dashicons-layout',
			58
		);
	}

	/**
	 * Enqueue the dashboard assets on this screen only.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public static function enqueue_assets( $hook_suffix ): void {
		if ( $hook_suffix !== self::$hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'etu-blocks-admin',
			ETUBLOCKS_URL . 'assets/admin/dashboard.css',
			array(),
			ETUBLOCKS_VERSION
		);

		wp_enqueue_script(
			'etu-blocks-admin',
			ETUBLOCKS_URL . 'assets/admin/dashboard.js',
			array(),
			ETUBLOCKS_VERSION,
			true
		);

		wp_localize_script(
			'etu-blocks-admin',
			'etublocksAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'etublocks_admin' ),
			)
		);
	}

	/**
	 * Map of block name => type group (for the filter). Unlisted blocks fall
	 * back to "content".
	 *
	 * @return array<string, string>
	 */
	private static function group_map(): array {
		return array(
			'etu/banner'           => 'marketing',
			'etu/cta'              => 'marketing',
			'etu/pricing-table'    => 'marketing',
			'etu/comparison-table' => 'marketing',
			'etu/data-table'       => 'content',
			'etu/countdown'        => 'interactive',
			'etu/faq'              => 'interactive',
			'etu/tabs'             => 'interactive',
			'etu/modal'            => 'interactive',
			'etu/notice'           => 'interactive',
			'etu/slides'           => 'interactive',
			'etu/social-share'     => 'media',
			'etu/before-after'     => 'media',
			'etu/video-popup'      => 'media',
			'etu/map'              => 'media',
			'etu/lottie'           => 'media',
			'etu/post-grid'        => 'dynamic',
			'etu/taxonomy'         => 'dynamic',
			'etu/post-filter'      => 'dynamic',
		);
	}

	/**
	 * Filter groups shown above the block grid.
	 *
	 * @return array<string, string> slug => label.
	 */
	private static function groups(): array {
		return array(
			'all'         => __( 'All', 'etu-blocks' ),
			'content'     => __( 'Content', 'etu-blocks' ),
			'marketing'   => __( 'Marketing', 'etu-blocks' ),
			'interactive' => __( 'Interactive', 'etu-blocks' ),
			'media'       => __( 'Media', 'etu-blocks' ),
			'dynamic'     => __( 'Dynamic', 'etu-blocks' ),
		);
	}

	/**
	 * How many published items use each block (cached).
	 *
	 * @return array<string, int>
	 */
	private static function usage_counts(): array {
		$cached = get_transient( self::USAGE_CACHE );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$names  = array();
		foreach ( Blocks::block_paths() as $path ) {
			$meta = wp_json_file_decode( $path . '/block.json', array( 'associative' => true ) );
			if ( is_array( $meta ) && ! empty( $meta['name'] ) ) {
				$names[] = (string) $meta['name'];
			}
		}

		$counts = array_fill_keys( $names, 0 );

		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- one scan of posts containing any Etu block; the result is cached in a transient below.
		$rows = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT post_content FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_content LIKE %s",
				'%' . $wpdb->esc_like( '<!-- wp:etu/' ) . '%'
			)
		);
		foreach ( (array) $rows as $content ) {
			foreach ( $names as $name ) {
				if ( false !== strpos( (string) $content, '<!-- wp:' . $name . ' ' ) ) {
					++$counts[ $name ];
				}
			}
		}

		set_transient( self::USAGE_CACHE, $counts, 6 * HOUR_IN_SECONDS );
		return $counts;
	}

	/**
	 * Clear the cached usage counts.
	 *
	 * @return void
	 */
	public static function clear_usage_cache(): void {
		delete_transient( self::USAGE_CACHE );
	}

	/**
	 * The user-facing blocks, with metadata for the manager UI.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private static function block_cards(): array {
		$disabled = Blocks::disabled();
		$usage    = self::usage_counts();
		$groups   = self::group_map();
		$cards    = array();

		foreach ( Blocks::block_paths() as $path ) {
			$json = $path . '/block.json';
			if ( ! file_exists( $json ) ) {
				continue;
			}
			$meta = wp_json_file_decode( $json, array( 'associative' => true ) );
			if ( ! is_array( $meta ) || empty( $meta['name'] ) ) {
				continue;
			}
			// Skip inner/child blocks (not inserted directly, e.g. etu/slide).
			if ( isset( $meta['supports']['inserter'] ) && false === $meta['supports']['inserter'] ) {
				continue;
			}

			$name     = (string) $meta['name'];
			$keywords = isset( $meta['keywords'] ) && is_array( $meta['keywords'] ) ? implode( ' ', $meta['keywords'] ) : '';

			$cards[] = array(
				'name'        => $name,
				'title'       => isset( $meta['title'] ) ? (string) $meta['title'] : $name,
				'description' => isset( $meta['description'] ) ? (string) $meta['description'] : '',
				'group'       => isset( $groups[ $name ] ) ? $groups[ $name ] : 'content',
				'usage'       => isset( $usage[ $name ] ) ? (int) $usage[ $name ] : 0,
				'enabled'     => ! in_array( $name, $disabled, true ),
				'search'      => strtolower( ( $meta['title'] ?? '' ) . ' ' . $keywords ),
			);
		}

		usort(
			$cards,
			static function ( $a, $b ) {
				return strcasecmp( $a['title'], $b['title'] );
			}
		);

		return $cards;
	}

	/**
	 * Save a block enable/disable toggle.
	 *
	 * @return void
	 */
	public static function ajax_toggle(): void {
		check_ajax_referer( 'etublocks_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'etu-blocks' ) ), 403 );
		}

		$block   = isset( $_POST['block'] ) ? sanitize_text_field( wp_unslash( $_POST['block'] ) ) : '';
		$enabled = isset( $_POST['enabled'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['enabled'] ) );

		if ( '' === $block || 0 !== strpos( $block, 'etu/' ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid block.', 'etu-blocks' ) ), 400 );
		}

		$disabled = Blocks::disabled();
		if ( $enabled ) {
			$disabled = array_values( array_diff( $disabled, array( $block ) ) );
		} elseif ( ! in_array( $block, $disabled, true ) ) {
			$disabled[] = $block;
		}
		update_option( Blocks::DISABLED_OPTION, $disabled );

		wp_send_json_success( array( 'block' => $block, 'enabled' => $enabled ) );
	}

	/**
	 * The dashboard navigation items.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private static function nav_items(): array {
		$current = self::current_tab();
		$items   = array(
			array( 'id' => 'blocks', 'label' => __( 'Blocks', 'etu-blocks' ), 'current' => 'blocks' === $current ),
			array( 'id' => 'settings', 'label' => __( 'Settings', 'etu-blocks' ), 'current' => 'settings' === $current ),
		);

		/**
		 * Filters the dashboard navigation items. Pro adds its own here.
		 *
		 * @since 1.0.0
		 *
		 * @param array<int, array<string, mixed>> $items Navigation items.
		 */
		return (array) apply_filters( 'etublocks_dashboard_nav', $items );
	}

	/**
	 * Which tab is being viewed. Reads the ?tab= query var and clamps it to a
	 * known value so a hand-crafted URL can only ever hit an existing tab.
	 *
	 * @return string
	 */
	private static function current_tab(): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only navigation selector; no nonce needed for a GET-based tab switch.
		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'blocks';
		return in_array( $tab, array( 'blocks', 'settings' ), true ) ? $tab : 'blocks';
	}

	/**
	 * Absolute URL of a given dashboard tab.
	 *
	 * @param string $tab Tab id.
	 * @return string
	 */
	private static function tab_url( string $tab ): string {
		$args = array( 'page' => self::PAGE_SLUG );
		if ( 'blocks' !== $tab ) {
			$args['tab'] = $tab;
		}
		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	/**
	 * Render the dashboard page.
	 *
	 * @return void
	 */
	public static function render(): void {
		$tab = self::current_tab();

		if ( 'settings' === $tab ) {
			$title    = __( 'Settings', 'etu-blocks' );
			$subtitle = __( 'Plugin-wide options. Every block also has its own inspector controls.', 'etu-blocks' );
		} else {
			$cards    = self::block_cards();
			$groups   = self::groups();
			$active   = count( array_filter( $cards, static function ( $c ) { return $c['enabled']; } ) );
			$title    = __( 'Blocks', 'etu-blocks' );
			$subtitle = sprintf(
				/* translators: 1: active block count, 2: total block count. */
				__( '%1$d of %2$d blocks enabled. Turn off any you do not need.', 'etu-blocks' ),
				(int) $active,
				count( $cards )
			);
		}
		?>
		<div class="etu-admin">
			<aside class="etu-admin__sidebar">
				<div class="etu-admin__brand">
					<span class="etu-admin__brand-name"><?php echo esc_html__( 'Etu Blocks', 'etu-blocks' ); ?></span>
					<span class="etu-admin__brand-badge">v<?php echo esc_html( ETUBLOCKS_VERSION ); ?></span>
				</div>
				<nav class="etu-admin__nav" aria-label="<?php echo esc_attr__( 'Etu Blocks sections', 'etu-blocks' ); ?>">
					<ul>
						<?php foreach ( self::nav_items() as $item ) : ?>
							<li>
								<a
									href="<?php echo esc_url( self::tab_url( (string) $item['id'] ) ); ?>"
									class="etu-admin__nav-link<?php echo ! empty( $item['current'] ) ? ' is-active' : ''; ?>"
									<?php echo ! empty( $item['current'] ) ? 'aria-current="page"' : ''; ?>
								>
									<?php echo esc_html( $item['label'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			</aside>

			<main class="etu-admin__main">
				<header class="etu-admin__header">
					<div>
						<h1 class="etu-admin__title"><?php echo esc_html( $title ); ?></h1>
						<p class="etu-admin__subtitle"><?php echo esc_html( $subtitle ); ?></p>
					</div>
					<div class="etu-admin__header-actions">
						<button type="button" class="etu-admin__theme-toggle" aria-label="<?php echo esc_attr__( 'Toggle colour theme', 'etu-blocks' ); ?>">
							<span class="etu-admin__theme-icon" aria-hidden="true"></span>
							<span class="etu-admin__theme-label"><?php echo esc_html__( 'Theme', 'etu-blocks' ); ?></span>
						</button>
						<a class="etu-admin__btn etu-admin__btn--primary" href="<?php echo esc_url( self::SUPPORT_URL ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html__( 'Support', 'etu-blocks' ); ?>
						</a>
					</div>
				</header>

				<?php if ( 'blocks' === $tab ) : ?>
				<section class="etu-admin__section" aria-labelledby="etu-blocks-heading">
					<h2 id="etu-blocks-heading" class="screen-reader-text"><?php echo esc_html__( 'Manage blocks', 'etu-blocks' ); ?></h2>

					<div class="etu-blocks-controls">
						<input
							type="search"
							class="etu-blocks-search"
							placeholder="<?php echo esc_attr__( 'Search blocks…', 'etu-blocks' ); ?>"
							aria-label="<?php echo esc_attr__( 'Search blocks', 'etu-blocks' ); ?>"
						/>
						<div class="etu-blocks-filters" role="group" aria-label="<?php echo esc_attr__( 'Filter by type', 'etu-blocks' ); ?>">
							<?php foreach ( $groups as $slug => $label ) : ?>
								<button
									type="button"
									class="etu-blocks-filter<?php echo 'all' === $slug ? ' is-active' : ''; ?>"
									data-group="<?php echo esc_attr( $slug ); ?>"
									aria-pressed="<?php echo 'all' === $slug ? 'true' : 'false'; ?>"
								>
									<?php echo esc_html( $label ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					</div>

					<?php if ( empty( $cards ) ) : ?>
						<p class="etu-admin__empty">
							<?php echo esc_html__( 'No blocks are registered yet. Build the plugin to see blocks here.', 'etu-blocks' ); ?>
						</p>
					<?php else : ?>
						<div class="etu-admin__grid">
							<?php foreach ( $cards as $card ) : ?>
								<article
									class="etu-admin__card etu-blocks-card<?php echo $card['enabled'] ? '' : ' is-disabled'; ?>"
									data-group="<?php echo esc_attr( $card['group'] ); ?>"
									data-search="<?php echo esc_attr( $card['search'] ); ?>"
								>
									<div class="etu-blocks-card__head">
										<h3 class="etu-admin__card-title"><?php echo esc_html( $card['title'] ); ?></h3>
										<label class="etu-switch">
											<input
												type="checkbox"
												class="etu-blocks-toggle"
												data-block="<?php echo esc_attr( $card['name'] ); ?>"
												<?php checked( $card['enabled'] ); ?>
											/>
											<span class="etu-switch__slider" aria-hidden="true"></span>
											<span class="screen-reader-text">
												<?php
												/* translators: %s: block title. */
												printf( esc_html__( 'Enable %s', 'etu-blocks' ), esc_html( $card['title'] ) );
												?>
											</span>
										</label>
									</div>
									<p class="etu-admin__card-desc"><?php echo esc_html( $card['description'] ); ?></p>
									<div class="etu-blocks-card__meta">
										<span class="etu-blocks-card__usage<?php echo $card['usage'] > 0 ? ' is-used' : ''; ?>">
											<?php
											if ( $card['usage'] > 0 ) {
												printf(
													/* translators: %s: number of items. */
													esc_html( _n( 'Used on %s item', 'Used on %s items', $card['usage'], 'etu-blocks' ) ),
													esc_html( number_format_i18n( $card['usage'] ) )
												);
											} else {
												echo esc_html__( 'Not used yet', 'etu-blocks' );
											}
											?>
										</span>
										<span class="etu-blocks-card__group"><?php echo esc_html( $groups[ $card['group'] ] ?? $card['group'] ); ?></span>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
						<p class="etu-blocks-noresults" hidden><?php echo esc_html__( 'No blocks match your search.', 'etu-blocks' ); ?></p>
					<?php endif; ?>
				</section>
				<?php endif; // blocks tab. ?>

				<?php if ( 'settings' === $tab ) : ?>
					<?php Settings::render(); ?>
				<?php endif; ?>

				<?php
				/**
				 * Fires inside the dashboard main column, after the core sections.
				 *
				 * Pro / add-ons render their own panels here.
				 *
				 * @since 1.0.0
				 */
				do_action( 'etublocks_dashboard_sections' );

				// Dormant Pro upsell — hidden until the etublocks_show_upsell
				// filter default is flipped to true in a future free release.
				\Etublocks\Upsell::render_dashboard_card();
				?>

				<section class="etu-admin__banner" aria-label="<?php echo esc_attr__( 'Support Etu Blocks', 'etu-blocks' ); ?>">
					<p class="etu-admin__banner-text">
						<?php echo esc_html__( 'Enjoying Etu Blocks? A quick review or a message on the support forum keeps it maintained and improving.', 'etu-blocks' ); ?>
					</p>
					<div class="etu-admin__banner-actions">
						<a class="etu-admin__btn etu-admin__btn--amber" href="<?php echo esc_url( self::REVIEW_URL ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html__( 'Leave a Review', 'etu-blocks' ); ?>
						</a>
						<a class="etu-admin__btn etu-admin__btn--ghost" href="<?php echo esc_url( (string) apply_filters( 'etublocks_donate_url', self::DONATE_URL ) ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html__( 'Donate', 'etu-blocks' ); ?>
						</a>
						<a class="etu-admin__btn etu-admin__btn--ghost" href="<?php echo esc_url( self::SUPPORT_URL ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html__( 'Get Support', 'etu-blocks' ); ?>
						</a>
					</div>
				</section>
			</main>
		</div>
		<?php
	}
}
