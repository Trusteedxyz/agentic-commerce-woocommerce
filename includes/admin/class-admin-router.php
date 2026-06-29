<?php
/**
 * Admin menu router — registers top-level "Trusteed" menu with submenus.
 *
 * @package AgenticMCPStores
 * @since   1.1.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Amcp_Admin_Router
 *
 * Registers the WP-admin top-level menu "Trusteed" and its three submenus:
 * Trust Center, Merchant Center, and Configuración.
 * Each submenu renders the SPA shell via spa-mount.php with the correct section.
 *
 * @since 1.1.0
 */
class Amcp_Admin_Router {

	/**
	 * Menu capability required for all Trusteed pages.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private const REQUIRED_CAP = 'manage_woocommerce';

	/**
	 * Top-level menu slug (also the Trust Center slug).
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private const SLUG_TRUST_CENTER = 'amcp-trust-center';

	/**
	 * Inicio submenu slug.
	 *
	 * @since 1.2.0
	 * @var string
	 */
	private const SLUG_INICIO = 'amcp-inicio';

	/**
	 * Mis ventas submenu slug.
	 *
	 * @since 1.2.0
	 * @var string
	 */
	private const SLUG_MIS_VENTAS = 'amcp-mis-ventas';

	/**
	 * Mis Reglas submenu slug.
	 *
	 * @since 1.2.0
	 * @var string
	 */
	private const SLUG_MIS_REGLAS = 'amcp-mis-reglas';

	/**
	 * Agentes submenu slug.
	 *
	 * @since 1.2.0
	 * @var string
	 */
	private const SLUG_AGENTES = 'amcp-agentes';

	/**
	 * Seguridad submenu slug.
	 *
	 * @since 1.2.0
	 * @var string
	 */
	private const SLUG_SEGURIDAD = 'amcp-seguridad';

	/**
	 * Merchant Center submenu slug.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private const SLUG_MERCHANT_CENTER = 'amcp-merchant-center';

	/**
	 * Settings submenu slug.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private const SLUG_SETTINGS = 'amcp-settings-embed';

	/**
	 * Absolute filesystem path to the plugin root (trailing slash).
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private string $plugin_path;

	/**
	 * Base URL of the plugin root (trailing slash).
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private string $plugin_url;

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
		add_action( 'admin_menu', array( $this, 'register_menus' ) );
	}

	/**
	 * Register the top-level menu and its seven submenus (mirrors Shopify nav).
	 *
	 * Hooked to admin_menu.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function register_menus(): void {
		// SVG icon encoded as a data URI (monochrome, WP admin-bar compatible).
		$icon_svg = 'data:image/svg+xml;base64,' . base64_encode(
			'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none">'
			. '<path fill="#a7aaad" d="M10 2L3 6v4c0 4.4 3 8.5 7 9.5 4-1 7-5.1 7-9.5V6L10 2z"/>'
			. '</svg>'
		);

		// Top-level menu page (renders Inicio/dashboard).
		add_menu_page(
			__( 'Trusteed', 'agenticmcpstores' ),
			__( 'Trusteed', 'agenticmcpstores' ),
			self::REQUIRED_CAP,
			self::SLUG_INICIO,
			array( $this, 'render_inicio' ),
			$icon_svg,
			56
		);

		// 1. Home — main dashboard (replaces auto-generated duplicate top-level entry).
		add_submenu_page(
			self::SLUG_INICIO,
			__( 'Home', 'agenticmcpstores' ),
			__( 'Home', 'agenticmcpstores' ),
			self::REQUIRED_CAP,
			self::SLUG_INICIO,
			array( $this, 'render_inicio' )
		);

		// 2. Trust Center — store overview.
		add_submenu_page(
			self::SLUG_INICIO,
			__( 'Trust Center', 'agenticmcpstores' ),
			__( 'Trust Center', 'agenticmcpstores' ),
			self::REQUIRED_CAP,
			self::SLUG_TRUST_CENTER,
			array( $this, 'render_trust_center' )
		);

		// 3. My Sales — orders + AI receipts combined.
		add_submenu_page(
			self::SLUG_INICIO,
			__( 'My Sales', 'agenticmcpstores' ),
			__( 'My Sales', 'agenticmcpstores' ),
			self::REQUIRED_CAP,
			self::SLUG_MIS_VENTAS,
			array( $this, 'render_mis_ventas' )
		);

		// 4. My Rules — R001-R010 trust rule management.
		add_submenu_page(
			self::SLUG_INICIO,
			__( 'My Rules', 'agenticmcpstores' ),
			__( 'My Rules', 'agenticmcpstores' ),
			self::REQUIRED_CAP,
			self::SLUG_MIS_REGLAS,
			array( $this, 'render_mis_reglas' )
		);

		// 5. Agents — buyer agents panel.
		add_submenu_page(
			self::SLUG_INICIO,
			__( 'Agents', 'agenticmcpstores' ),
			__( 'Agents', 'agenticmcpstores' ),
			self::REQUIRED_CAP,
			self::SLUG_AGENTES,
			array( $this, 'render_agentes' )
		);

		// 6. Security — keys + audit log combined.
		add_submenu_page(
			self::SLUG_INICIO,
			__( 'Security', 'agenticmcpstores' ),
			__( 'Security', 'agenticmcpstores' ),
			self::REQUIRED_CAP,
			self::SLUG_SEGURIDAD,
			array( $this, 'render_seguridad' )
		);

		// 7. Merchant Center — payment methods, orders, shops.
		add_submenu_page(
			self::SLUG_INICIO,
			__( 'Merchant Center', 'agenticmcpstores' ),
			__( 'Merchant Center', 'agenticmcpstores' ),
			self::REQUIRED_CAP,
			self::SLUG_MERCHANT_CENTER,
			array( $this, 'render_merchant_center' )
		);

		// 8. Settings — fallback mode + payment configuration.
		add_submenu_page(
			self::SLUG_INICIO,
			__( 'Settings', 'agenticmcpstores' ),
			__( 'Settings', 'agenticmcpstores' ),
			self::REQUIRED_CAP,
			self::SLUG_SETTINGS,
			array( $this, 'render_settings' )
		);
	}

	/**
	 * Render the Inicio SPA shell.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function render_inicio(): void {
		$this->render_spa_shell( 'inicio' );
	}

	/**
	 * Render the Trust Center SPA shell.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function render_trust_center(): void {
		$this->render_spa_shell( 'trust-center' );
	}

	/**
	 * Render the Mis Ventas SPA shell.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function render_mis_ventas(): void {
		$this->render_spa_shell( 'mis-ventas' );
	}

	/**
	 * Render the Mis Reglas SPA shell.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function render_mis_reglas(): void {
		$this->render_spa_shell( 'mis-reglas' );
	}

	/**
	 * Render the Agentes SPA shell.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function render_agentes(): void {
		$this->render_spa_shell( 'agentes' );
	}

	/**
	 * Render the Seguridad SPA shell.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function render_seguridad(): void {
		$this->render_spa_shell( 'seguridad' );
	}

	/**
	 * Render the Merchant Center SPA shell.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function render_merchant_center(): void {
		$this->render_spa_shell( 'merchant-center' );
	}

	/**
	 * Render the Settings SPA shell.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function render_settings(): void {
		$this->render_spa_shell( 'settings' );
	}

	/**
	 * Shared render logic: capability check + include spa-mount.php.
	 *
	 * @since 1.1.0
	 *
	 * @param string $section One of 'trust-center' | 'merchant-center' | 'settings'.
	 * @return void
	 */
	private function render_spa_shell( string $section ): void {
		if ( ! current_user_can( self::REQUIRED_CAP ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'agenticmcpstores' ) );
		}

		$view_path = $this->plugin_path . 'includes/admin/views/spa-mount.php';

		if ( ! file_exists( $view_path ) ) {
			wp_die( esc_html__( 'Internal error: SPA view not found.', 'agenticmcpstores' ) );
		}

		// $section is available inside the included template.
		include $view_path;
	}
}
