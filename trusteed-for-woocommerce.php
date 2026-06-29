<?php
/**
 * Plugin Name:       Trusteed for WooCommerce
 * Plugin URI:        https://trusteed.xyz/integrations/woocommerce
 * Description:       Connect your WooCommerce store to AI agents via MCP (Model Context Protocol). Enable Claude, ChatGPT, and any MCP-compatible agent to search your catalog and create carts.
 * Version:           2.0.1
 * Author:            Trusteed
 * Author URI:        https://trusteed.xyz
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       trusteed-for-woocommerce
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Tested up to:      6.7
 * WC requires at least: 8.0
 * WC tested up to:   9.5
 * Requires PHP:      7.4
 *
 * @package Trusteed
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version.
 *
 * @var string
 */
define( 'TRUSTEED_VERSION', '2.0.1' );

/**
 * Plugin directory path (with trailing slash).
 *
 * @var string
 */
define( 'TRUSTEED_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL (with trailing slash).
 *
 * @var string
 */
define( 'TRUSTEED_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Trusteed API base URL (canonical default).
 *
 * Defaults to the production MCP standard domain `https://api.trusteed.xyz`
 * per `docs/architecture/services-architecture.md` and project convention:
 *   - `trusteed.xyz` / `api.trusteed.xyz` → MCP standard (Streamable HTTP, Claude.ai, agents)
 *   - `agenticmcpstores-production.up.railway.app` → chatgpt-app only (PostMessage; NOT for Claude.ai)
 *
 * Override for dev/staging by predefining this constant in `wp-config.php`,
 * e.g. `define( 'TRUSTEED_API_BASE', 'https://staging.trusteed.xyz' );`.
 * The runtime option `amcp_api_base_url` (Settings → Trusteed) also
 * overrides this default per-install without touching code.
 *
 * @var string
 */
if ( ! defined( 'TRUSTEED_API_BASE' ) ) {
	define( 'TRUSTEED_API_BASE', 'https://api.trusteed.xyz' );
}

/**
 * Check if WooCommerce is active before initializing.
 *
 * @return bool True if WooCommerce is active, false otherwise.
 */
function agenticmcp_is_woocommerce_active() {
	// Check if WooCommerce class exists (works regardless of plugin directory name).
	if ( class_exists( 'WooCommerce' ) ) {
		return true;
	}
	/**
	 * Filters the list of active plugin paths.
	 *
	 * Mirrors WordPress core (wp_get_active_and_valid_plugins) so the fallback
	 * detection also catches network-activated WooCommerce installs.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $active_plugins Array of active plugin basenames.
	 */
	$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) );
	foreach ( $active_plugins as $plugin ) {
		if ( false !== strpos( $plugin, 'woocommerce.php' ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Display an admin notice when WooCommerce is not active.
 *
 * @return void
 */
function agenticmcp_woocommerce_missing_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<strong><?php esc_html_e( 'Trusteed for WooCommerce', 'trusteed-for-woocommerce' ); ?></strong>
			<?php esc_html_e( 'requires WooCommerce to be installed and active.', 'trusteed-for-woocommerce' ); ?>
		</p>
	</div>
	<?php
}

// Bail early if WooCommerce is not active.
if ( ! agenticmcp_is_woocommerce_active() ) {
	add_action( 'admin_notices', 'agenticmcp_woocommerce_missing_notice' );
	return;
}

/**
 * Declare compatibility with WooCommerce HPOS (High-Performance Order Storage)
 * and Cart/Checkout Blocks.
 *
 * @return void
 */
function agenticmcp_declare_woocommerce_compatibility() {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			__FILE__,
			true
		);
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'cart_checkout_blocks',
			__FILE__,
			true
		);
	}
}
add_action( 'before_woocommerce_init', 'agenticmcp_declare_woocommerce_compatibility' );

/**
 * Initialize the plugin on plugins_loaded.
 *
 * @return void
 */
function agenticmcp_init() {
	require_once TRUSTEED_PLUGIN_DIR . 'includes/class-plugin.php';
	Trusteed_Plugin::get_instance();
}
add_action( 'plugins_loaded', 'agenticmcp_init' );

/**
 * Plugin activation hook.
 *
 * @return void
 */
function agenticmcp_activate() {
	require_once TRUSTEED_PLUGIN_DIR . 'includes/class-plugin.php';
	Trusteed_Plugin::activate();
}
register_activation_hook( __FILE__, 'agenticmcp_activate' );

/**
 * Plugin deactivation hook.
 *
 * @return void
 */
function agenticmcp_deactivate() {
	require_once TRUSTEED_PLUGIN_DIR . 'includes/class-plugin.php';
	Trusteed_Plugin::deactivate();
}
register_deactivation_hook( __FILE__, 'agenticmcp_deactivate' );
