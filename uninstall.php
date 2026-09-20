<?php
/**
 * Uninstall handler for Trusteed for WooCommerce.
 *
 * Removes every plugin option row from wp_options on uninstall, honouring the
 * commitment in PRIVACY_POLICY.md that merchant data is permanently removed.
 *
 * Three namespaces exist and all three must be cleared:
 *
 *   - `agenticmcp_*`  original prefix, still used for catalog/billing state.
 *   - `trusteed_*`    current prefix (2.1.0 rename), holds the secrets.
 *   - `amcp_*`        legacy prefix that Trusteed_Options::get_option() still
 *                     read-falls-back to. Deleting only the `trusteed_*` row
 *                     would leave a live secret that a reinstall resurrects.
 *
 * Kept deliberately dependency-free: uninstall.php must not boot plugin
 * classes, so option names are literals here rather than reads of
 * Trusteed_Options. tests/unit/UninstallOptionCleanupTest.php is a drift gate
 * that scans `includes/` for every writable option key and fails if this list
 * falls behind.
 *
 * @package Trusteed
 */

// Prevent direct access — only run via WordPress uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Cached snapshot transient, keyed by a digest of the merchant id. Resolved
 * BEFORE the option loop below deletes that id.
 */
$trusteed_merchant_id = (string) get_option( 'trusteed_merchant_id', '' );
if ( '' === $trusteed_merchant_id ) {
	$trusteed_merchant_id = (string) get_option( 'amcp_merchant_id', '' );
}
if ( '' !== $trusteed_merchant_id ) {
	// Mirrors Trusteed_Snapshot_Client_Woo::get_cached_payload().
	delete_transient( 'trusteed_snap_' . substr( md5( $trusteed_merchant_id ), 0, 12 ) );
}

/**
 * Fixed-key transients. The per-agent `trusteed_kid_fs_<sha256>` first-seen
 * markers are NOT removed here: their keys are unbounded and undiscoverable
 * without a direct wp_options LIKE query. They hold no secret — only a
 * timestamp keyed by a hash of an agent key id.
 *
 * @var string[]
 */
$trusteed_transients = array(
	'trusteed_jwks',
	'trusteed_eval_4xx_rate',
	'trusteed_eval_config_drift_notice',
);

foreach ( $trusteed_transients as $trusteed_transient ) {
	delete_transient( $trusteed_transient );
}

/**
 * Options stored under the original `agenticmcp_` prefix.
 *
 * @var string[]
 */
$agenticmcp_options = array(
	'agenticmcp_api_key',
	'agenticmcp_store_slug',
	'agenticmcp_mcp_endpoint',
	'agenticmcp_tier',
	'agenticmcp_previous_tier',
	'agenticmcp_last_sync',
	'agenticmcp_last_sync_attempt',
	'agenticmcp_last_sync_success',
	'agenticmcp_webhook_secret',
	'agenticmcp_last_billing_event_id',
	'agenticmcp_trial_ends_at',
);

/**
 * Bare option keys addressed through Trusteed_Options. Each one exists twice in
 * the database: once as `trusteed_<key>` (written) and once as `amcp_<key>`
 * (legacy, still read as a fallback).
 *
 * `embed_wp_secret`, `enforcement_hmac_secret` and `woo_webhook_secret` are
 * encrypted secrets — these are the rows that made leaving them behind a
 * privacy and security problem rather than untidiness.
 *
 * @var string[]
 */
$trusteed_prefixed_options = array(
	'api_base_url',
	'embed_wp_secret',
	'enforcement_hmac_missing_notice',
	'enforcement_hmac_secret',
	'enforcement_installation_id',
	'enforcement_installation_stub_notice',
	'failure_mode',
	'merchant_id',
	'woo_webhook_secret',
);

foreach ( $trusteed_prefixed_options as $trusteed_bare_key ) {
	$agenticmcp_options[] = 'trusteed_' . $trusteed_bare_key;
	$agenticmcp_options[] = 'amcp_' . $trusteed_bare_key;
}

foreach ( $agenticmcp_options as $option ) {
	delete_option( $option );
}
