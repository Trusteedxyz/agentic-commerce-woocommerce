<?php
/**
 * Main plugin class — singleton entry point.
 *
 * @package Trusteed
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Trusteed_Plugin
 *
 * Bootstraps the plugin: loads dependencies, registers hooks,
 * and provides activation/deactivation handlers.
 */
class Trusteed_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Trusteed_Plugin|null
	 */
	private static $instance = null;

	/**
	 * API client instance.
	 *
	 * @var Trusteed_Api_Client|null
	 */
	private $api_client = null;

	/**
	 * Catalog sync instance.
	 *
	 * @var AgenticMCP_Catalog_Sync|null
	 */
	private $catalog_sync = null;

	/**
	 * Cart bridge instance.
	 *
	 * @var Trusteed_Cart_Bridge|null
	 */
	private $cart_bridge = null;

	/**
	 * Settings instance.
	 *
	 * @var Trusteed_Settings|null
	 */
	private $settings = null;

	/**
	 * Billing webhooks instance.
	 *
	 * @var AgenticMCP_Billing_Webhooks|null
	 */
	private $billing_webhooks = null;

	/**
	 * Checkout enforcer instance.
	 *
	 * @var Amcp_Checkout_Enforcer|null
	 */
	private $checkout_enforcer = null;

	/**
	 * Agent event webhook instance (R023 refund/cancel emitter).
	 *
	 * @var Amcp_Agent_Event_Webhook|null
	 */
	private $agent_event_webhook = null;

	/**
	 * Multi-item add-to-cart handler (Gap 2 — consumes `agenticmcp_multi_add=1`).
	 *
	 * @var Amcp_Multi_Add_Handler|null
	 */
	private $multi_add_handler = null;

	/**
	 * Allowed values for the `amcp_failure_mode` option.
	 *
	 * @var string[]
	 */
	private const ALLOWED_FAILURE_MODES = array( 'enforce', 'observe' );

	/**
	 * F6.PHP2 / M1 — sentinel UUID used as fallback when no real
	 * `amcp_enforcement_installation_id` has been provisioned by the
	 * backend onboarding flow. Backend fail-closes any request signed
	 * with this stub; the admin notice asks the merchant to reconnect.
	 *
	 * @var string
	 */
	public const STUB_INSTALLATION_ID = '00000000-0000-0000-0000-000000000001';

	/**
	 * F6.PHP2 / M1 — option key flag flipped to `1` when the plugin
	 * boots with the stub installation id (i.e. upgrade without
	 * re-onboard). Cleared on successful onboard. Surfaced via an
	 * admin notice rendered from class-settings.php.
	 *
	 * @var string
	 */
	public const NOTICE_OPTION_INSTALLATION_STUB = 'amcp_enforcement_installation_stub_notice';

	/**
	 * Get the singleton instance.
	 *
	 * @return Trusteed_Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor — use get_instance().
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	private function init() {
		$this->load_dependencies();
		$this->define_hooks();
	}

	/**
	 * Load all required class files.
	 *
	 * @return void
	 */
	private function load_dependencies() {
		// S039-SEC-004: crypto helper must be loaded before settings and token broker
		// so Amcp_Crypto_Helper is available when secrets are read or written.
		require_once TRUSTEED_PLUGIN_DIR . 'includes/class-crypto-helper.php';
		require_once TRUSTEED_PLUGIN_DIR . 'includes/class-api-client.php';
		require_once TRUSTEED_PLUGIN_DIR . 'includes/class-catalog-sync.php';
		require_once TRUSTEED_PLUGIN_DIR . 'includes/class-cart-bridge.php';
		require_once TRUSTEED_PLUGIN_DIR . 'includes/class-settings.php';
		require_once TRUSTEED_PLUGIN_DIR . 'includes/class-billing-webhooks.php';
		require_once TRUSTEED_PLUGIN_DIR . 'includes/class-enforcement-api-client.php';
		require_once TRUSTEED_PLUGIN_DIR . 'includes/class-token-verifier.php';
		require_once TRUSTEED_PLUGIN_DIR . 'includes/class-snapshot-client-woo.php';
		require_once TRUSTEED_PLUGIN_DIR . 'includes/class-cart-signals.php';
		require_once TRUSTEED_PLUGIN_DIR . 'includes/class-classic-meta-persister.php';
		require_once TRUSTEED_PLUGIN_DIR . 'includes/class-checkout-enforcer.php';
		require_once TRUSTEED_PLUGIN_DIR . 'includes/class-agent-event-webhook.php';
		require_once TRUSTEED_PLUGIN_DIR . 'includes/class-multi-add-handler.php';

		$this->api_client       = new Trusteed_Api_Client();
		$this->catalog_sync     = new AgenticMCP_Catalog_Sync( $this->api_client );
		$this->cart_bridge      = new Trusteed_Cart_Bridge();
		$this->settings         = new Trusteed_Settings();
		$this->billing_webhooks = new AgenticMCP_Billing_Webhooks();

		// Checkout enforcement (Spec-048 WooCommerce integration).
		// Single source of truth: default to TRUSTEED_API_BASE constant (defined in
		// trusteed-for-woocommerce.php, overridable via wp-config.php). Avoids divergent
		// hardcoded defaults across plugin components.
		$api_base_url    = (string) get_option( 'amcp_api_base_url', TRUSTEED_API_BASE );
		$merchant_id     = (string) get_option( 'amcp_merchant_id', '' );
		$installation_id = (string) get_option( 'amcp_enforcement_installation_id', self::STUB_INSTALLATION_ID );
		$raw_hmac_secret = (string) get_option( 'amcp_enforcement_hmac_secret', '' );

		// F6.PHP2 / M1 — detect stub / empty installation id and flag the
		// admin notice so the merchant sees an actionable error instead of
		// silent backend rejections. Cleared after a successful onboard
		// (see Amcp_Settings::ajax_onboard).
		if ( '' === $installation_id || self::STUB_INSTALLATION_ID === $installation_id ) {
			update_option( self::NOTICE_OPTION_INSTALLATION_STUB, 1, false );
		} else {
			// Cheap self-heal: clear stale notice if a real id is in place.
			if ( get_option( self::NOTICE_OPTION_INSTALLATION_STUB ) ) {
				delete_option( self::NOTICE_OPTION_INSTALLATION_STUB );
			}
		}
		$hmac_secret     = Amcp_Crypto_Helper::decrypt( $raw_hmac_secret );

		$enforcement_client = new Amcp_Enforcement_Api_Client( $api_base_url, $installation_id, $hmac_secret );
		$snapshot_client    = new Amcp_Snapshot_Client_Woo( $api_base_url, $installation_id, $hmac_secret );

		// Failure-mode policy (spec-048 Gap 5/6). Default 'enforce' fail-closed.
		// Operators can switch to 'observe' via amcp_failure_mode option during canary.
		// Gap 4 — defensive normalization with explicit log when value drifts
		// (typo in wp-cli, manual DB edit, plugin downgrade).
		$failure_mode = self::normalize_failure_mode( (string) get_option( 'amcp_failure_mode', 'enforce' ) );

		// R023 refund/cancel + enforcement_indeterminate emitter — wires WC
		// order events + checkout-enforcer fail-open telemetry to backend.
		$this->agent_event_webhook = new Amcp_Agent_Event_Webhook(
			$api_base_url,
			$installation_id,
			$hmac_secret,
			$merchant_id
		);

		$this->checkout_enforcer = new Amcp_Checkout_Enforcer(
			$enforcement_client,
			$merchant_id,
			$installation_id,
			$snapshot_client,
			$failure_mode,
			$this->agent_event_webhook
		);

		// Gap 2 — handle multi-item agent carts emitted with
		// `?agenticmcp_multi_add=1&items[N][...]`. Native WC ignores `items[]`.
		$this->multi_add_handler = new Amcp_Multi_Add_Handler();

		// Token broker REST endpoint: must be registered outside is_admin() because
		// REST API requests (/wp-json/*) are processed without WP_ADMIN defined,
		// so is_admin() returns false and the route would never be registered.
		require_once TRUSTEED_PLUGIN_DIR . 'includes/admin/class-token-broker.php';
		$api_base = (string) get_option( 'amcp_api_base_url', TRUSTEED_API_BASE );
		( new Amcp_Token_Broker( $api_base, TRUSTEED_VERSION ) )->init();

		// Admin embed shell — spec 039 F3 (T039-310..T039-313).
		if ( is_admin() ) {
			require_once TRUSTEED_PLUGIN_DIR . 'includes/admin/class-admin-router.php';
			require_once TRUSTEED_PLUGIN_DIR . 'includes/admin/class-admin-spa-loader.php';

			$plugin_url  = TRUSTEED_PLUGIN_URL;
			$plugin_path = TRUSTEED_PLUGIN_DIR;

			( new Amcp_Admin_Router( $plugin_url, $plugin_path ) )->init();
			( new Amcp_Admin_Spa_Loader( $plugin_url, $plugin_path ) )->init();
		}
	}

	/**
	 * Register all WordPress and WooCommerce hooks.
	 *
	 * @return void
	 */
	private function define_hooks() {
		// Admin menu for plugin settings.
		add_action( 'admin_menu', array( $this->settings, 'register_menu' ) );

		// REST API endpoints for cart bridge and billing webhooks.
		add_action( 'rest_api_init', array( $this->cart_bridge, 'register_routes' ) );
		add_action( 'rest_api_init', array( $this->billing_webhooks, 'register_routes' ) );

		// Sync products on save/update/delete/stock change.
		add_action( 'save_post_product', array( $this->catalog_sync, 'on_product_save' ), 10, 3 );
		add_action( 'woocommerce_product_set_stock', array( $this->catalog_sync, 'on_stock_change' ), 10, 1 );
		add_action( 'woocommerce_delete_product', array( $this->catalog_sync, 'on_product_delete' ), 10, 1 );
		add_action( 'woocommerce_trash_product', array( $this->catalog_sync, 'on_product_delete' ), 10, 1 );

		// AJAX handler for manual catalog sync from settings page.
		add_action( 'wp_ajax_agenticmcp_sync_catalog', array( $this->catalog_sync, 'ajax_sync_catalog' ) );

		// Enqueue admin assets on plugin settings page.
		add_action( 'admin_enqueue_scripts', array( $this->settings, 'enqueue_assets' ) );

		// Load text domain for translations.
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Checkout enforcement for agent-initiated orders.
		$this->checkout_enforcer->init();

		// R023 refund/cancel webhook — propagates events to backend for R023 evaluation.
		$this->agent_event_webhook->init();

		// Gap 2 — register the multi-add URL handler.
		if ( null !== $this->multi_add_handler ) {
			$this->multi_add_handler->init();
		}
	}

	/**
	 * Gap 4 — coerce the raw `amcp_failure_mode` option to a whitelisted value
	 * and emit a structured log line when drift is detected so ops can spot
	 * misconfiguration before the silent fallback hides it.
	 *
	 * @param string $raw Raw option value (untrusted).
	 * @return string One of 'enforce' | 'observe'. Falls back to 'enforce'.
	 */
	private static function normalize_failure_mode( string $raw ): string {
		$normalized = strtolower( trim( $raw ) );

		if ( in_array( $normalized, self::ALLOWED_FAILURE_MODES, true ) ) {
			return $normalized;
		}

		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			sprintf(
				'[amcp.config_drift] amcp_failure_mode has invalid value %s — falling back to enforce',
				wp_json_encode( $raw )
			)
		);

		return 'enforce';
	}

	/**
	 * Load the plugin text domain for translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'trusteed-for-woocommerce',
			false,
			dirname( plugin_basename( TRUSTEED_PLUGIN_DIR . 'trusteed-for-woocommerce.php' ) ) . '/languages'
		);
	}

	/**
	 * Plugin activation handler.
	 *
	 * @return void
	 */
	public static function activate() {
		// Stub — future: schedule cron events, create DB tables, etc.
	}

	/**
	 * Plugin deactivation handler.
	 *
	 * @return void
	 */
	public static function deactivate() {
		// Stub — future: clear scheduled cron events, flush rewrite rules, etc.
	}
}
