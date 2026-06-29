<?php
/**
 * Admin SPA asset loader — enqueues the Vite-built React bundle from manifest.
 *
 * @package AgenticMCPStores
 * @since   1.1.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Amcp_Admin_Spa_Loader
 *
 * Reads the Vite manifest at assets/admin-spa/.vite/manifest.json and
 * enqueues the compiled JS + CSS on any Trusteed admin page.
 *
 * Security: wp_localize_script only passes public, non-sensitive data.
 * Tokens are NEVER included here (constraint C-003).
 *
 * @since 1.1.0
 */
class Amcp_Admin_Spa_Loader {

	/**
	 * Path to the Vite manifest relative to assets/.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private const MANIFEST_RELATIVE = 'admin-spa/.vite/manifest.json';

	/**
	 * Vite entry point key expected in the manifest.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private const VITE_ENTRY = 'src/main.tsx';

	/**
	 * Handle prefix used for wp_enqueue_script / wp_enqueue_style.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private const HANDLE_PREFIX = 'amcp-admin-spa';

	/**
	 * Base URL of the plugin root (trailing slash).
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private string $plugin_url;

	/**
	 * Filesystem path to the plugin root (trailing slash).
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private string $plugin_path;

	/**
	 * Constructor.
	 *
	 * @since 1.1.0
	 *
	 * @param string $plugin_url  Base URL of the plugin root (with trailing slash).
	 * @param string $plugin_path Filesystem path to the plugin root (with trailing slash).
	 */
	public function __construct( string $plugin_url, string $plugin_path ) {
		$this->plugin_url  = $plugin_url;
		$this->plugin_path = $plugin_path;
	}

	/**
	 * Wire up WordPress hooks.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 10, 1 );
	}

	/**
	 * Conditionally enqueue the SPA bundle on Trusteed admin pages.
	 *
	 * Hooked to admin_enqueue_scripts.
	 *
	 * @since 1.1.0
	 *
	 * @param string $hook Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue( string $hook ): void {
		// Only run on Trusteed pages (slugs contain 'amcp-').
		if ( false === strpos( $hook, 'amcp-' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$manifest_file = $this->plugin_path . 'assets/' . self::MANIFEST_RELATIVE;

		if ( ! file_exists( $manifest_file ) ) {
			$this->maybe_show_build_notice();
			return;
		}

		$raw      = file_get_contents( $manifest_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$manifest = json_decode( $raw, true );

		if ( ! is_array( $manifest ) ) {
			$this->maybe_show_build_notice();
			return;
		}

		$entry = $manifest[ self::VITE_ENTRY ] ?? null;

		if ( ! is_array( $entry ) || empty( $entry['file'] ) ) {
			return;
		}

		$base_url   = $this->plugin_url . 'assets/admin-spa/';
		$script_url = $base_url . $entry['file'];

		wp_enqueue_script( self::HANDLE_PREFIX, $script_url, array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters

		// Enqueue all CSS chunks referenced in the manifest entry.
		foreach ( $entry['css'] ?? array() as $css_file ) {
			$css_handle = self::HANDLE_PREFIX . '-' . md5( $css_file );
			$css_url    = $base_url . $css_file;
			wp_enqueue_style( $css_handle, $css_url, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters
		}

		// Pass only public, non-sensitive config to the SPA (constraint C-003).
		// NEVER include tokens, secrets, or credentials here.
		$api_base = untrailingslashit( (string) get_option( 'amcp_api_base_url', TRUSTEED_API_BASE ) );

		// Dev: when the stored URL is a private Docker bridge IP (172.x.x.x) or the
		// Docker host alias (host.docker.internal), the SPA runs in the user's
		// browser where that host is unreachable (private IP blocked by Chrome PNA,
		// or the alias only resolves inside containers). The token broker still uses
		// the original $api_base server-side (WP→API), but the SPA must reach the dev
		// API server via localhost. Rewrite only the host portion for the SPA.
		$spa_api_base = $api_base;
		$parsed_host  = (string) wp_parse_url( $api_base, PHP_URL_HOST );
		if ( preg_match( '/^172\.(1[6-9]|2\d|3[01])\./', $parsed_host ) || 'host.docker.internal' === $parsed_host ) {
			$spa_api_base = preg_replace( '/^(https?:\/\/)' . preg_quote( $parsed_host, '/' ) . '/', '$1localhost', $api_base );
		}

		wp_localize_script(
			self::HANDLE_PREFIX,
			'__AMCP_CONFIG__',
			array(
				'restRoot' => esc_url_raw( rest_url() ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'apiBase'  => esc_url_raw( $spa_api_base ),
				'locale'   => get_locale(),
			)
		);
	}

	/**
	 * Display a WP admin notice when the bundle is missing (dev/WP_DEBUG only).
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	private function maybe_show_build_notice(): void {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}

		add_action(
			'admin_notices',
			static function (): void {
				printf(
					'<div class="notice notice-warning"><p>%s</p></div>',
					esc_html__( 'Trusteed: el bundle admin-spa no ha sido compilado. Ejecuta pnpm build en packages/wp-plugin.', 'agenticmcpstores' )
				);
			}
		);
	}
}
