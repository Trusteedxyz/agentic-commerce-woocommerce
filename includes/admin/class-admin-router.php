<?php
/**
 * Admin menu router — registers top-level "Trusteed" menu with submenus.
 *
 * @package Trusteed
 * @since   1.1.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Trusteed_Admin_Router
 *
 * Registers the WP-admin top-level menu "Trusteed" and its three submenus:
 * Trust Center, Merchant Center, and Configuración.
 * Each submenu renders the SPA shell via spa-mount.php with the correct section.
 *
 * @since 1.1.0
 */
class Trusteed_Admin_Router {

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
	 * Agent Readiness submenu slug (spec 065 F1).
	 *
	 * @since 1.3.0
	 * @var string
	 */
	private const SLUG_AGENT_READINESS = 'amcp-agent-readiness';

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
	 * Payment methods submenu slug.
	 *
	 * @since 1.4.0
	 * @var string
	 */
	private const SLUG_PAYMENT_METHODS = 'amcp-payment-methods';

	/**
	 * Slugs registered but hidden from the sidebar (still reachable by URL).
	 *
	 * @since 1.4.0
	 * @var string[]
	 */
	private const HIDDEN_SLUGS = array(
		self::SLUG_AGENT_READINESS,
		self::SLUG_SEGURIDAD,
		self::SLUG_MERCHANT_CENTER,
	);

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
		add_action( 'admin_head', array( $this, 'hide_legacy_submenus' ) );
	}

	/**
	 * Register the top-level menu, its seven visible submenus and three hidden pages.
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
			__( 'Trusteed', 'trusteed-for-woocommerce' ),
			__( 'Trusteed', 'trusteed-for-woocommerce' ),
			self::REQUIRED_CAP,
			self::SLUG_INICIO,
			array( $this, 'render_inicio' ),
			$icon_svg,
			56
		);

		// Unified 7-entry menu (design 2026-10-03 §4): same order and wording as
		// Shopify, PrestaShop and Magento. Slugs never change (they are the permission
		// key and the target of the SPA's internal links).
		$visible = array(
			// 1. Home — main dashboard (replaces auto-generated duplicate top-level entry).
			array( self::SLUG_INICIO, __( 'Home', 'trusteed-for-woocommerce' ), 'render_inicio' ),
			// 2. My sales — orders + AI receipts combined.
			array( self::SLUG_MIS_VENTAS, __( 'My sales', 'trusteed-for-woocommerce' ), 'render_mis_ventas' ),
			// 3. Agents — buyer agents panel.
			array( self::SLUG_AGENTES, __( 'Agents', 'trusteed-for-woocommerce' ), 'render_agentes' ),
			// 4. My rules — trust rule management.
			array( self::SLUG_MIS_REGLAS, __( 'My rules', 'trusteed-for-woocommerce' ), 'render_mis_reglas' ),
			// 5. Payment methods — own entry (was a tab inside Merchant Center).
			array( self::SLUG_PAYMENT_METHODS, __( 'Payment methods', 'trusteed-for-woocommerce' ), 'render_payment_methods' ),
			// 6. Trust — store overview (absorbs Agent Readiness and Security in the SPA).
			array( self::SLUG_TRUST_CENTER, __( 'Trust', 'trusteed-for-woocommerce' ), 'render_trust_center' ),
			// 7. Settings — fallback mode + payment configuration.
			array( self::SLUG_SETTINGS, __( 'Settings', 'trusteed-for-woocommerce' ), 'render_settings' ),
		);

		foreach ( $visible as $entry ) {
			add_submenu_page(
				self::SLUG_INICIO,
				$entry[1],
				$entry[1],
				self::REQUIRED_CAP,
				$entry[0],
				array( $this, $entry[2] )
			);
		}

		// Pages that left the menu but stay registered (reachable by URL and by the
		// SPA's internal links). They are registered here and hidden later, see
		// hide_legacy_submenus().
		$hidden = array(
			array( self::SLUG_AGENT_READINESS, __( 'Agent Readiness', 'trusteed-for-woocommerce' ), 'render_agent_readiness' ),
			array( self::SLUG_SEGURIDAD, __( 'Security', 'trusteed-for-woocommerce' ), 'render_seguridad' ),
			array( self::SLUG_MERCHANT_CENTER, __( 'Merchant Center', 'trusteed-for-woocommerce' ), 'render_merchant_center' ),
		);

		foreach ( $hidden as $entry ) {
			add_submenu_page(
				self::SLUG_INICIO,
				$entry[1],
				$entry[1],
				self::REQUIRED_CAP,
				$entry[0],
				array( $this, $entry[2] )
			);
		}
	}

	/**
	 * Hide the pages that left the menu, without unregistering them.
	 *
	 * Hooked to admin_head, NOT run inside admin_menu: wp-admin/admin.php resolves
	 * the page's parent and runs user_can_access_admin_page() against $submenu
	 * BEFORE admin_head, and the menu HTML is printed AFTER it. Removing the entries
	 * on admin_menu would make a direct visit to admin.php?page=amcp-seguridad
	 * answer "Sorry, you are not allowed to access this page". Removing them here
	 * only affects what the sidebar renders; the registered page and its parent
	 * highlighting keep working.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function hide_legacy_submenus(): void {
		foreach ( self::HIDDEN_SLUGS as $slug ) {
			remove_submenu_page( self::SLUG_INICIO, $slug );
		}
	}

	/**
	 * Render the Payment methods SPA shell.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function render_payment_methods(): void {
		$this->render_spa_shell( 'payment-methods' );
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
	 * Render the Agent Readiness SPA shell (spec 065 F1).
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function render_agent_readiness(): void {
		$this->render_spa_shell( 'agent-readiness' );
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
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'trusteed-for-woocommerce' ) );
		}

		$view_path = $this->plugin_path . 'includes/admin/views/spa-mount.php';

		if ( ! file_exists( $view_path ) ) {
			wp_die( esc_html__( 'Internal error: SPA view not found.', 'trusteed-for-woocommerce' ) );
		}

		// $section is available inside the included template.
		include $view_path;
	}
}
