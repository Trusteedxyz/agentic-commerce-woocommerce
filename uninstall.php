<?php
/**
 * Uninstall handler for AgenticMCPStores for WooCommerce.
 *
 * Removes all plugin options from wp_options on uninstall.
 *
 * @package AgenticMCPStores
 */

// Prevent direct access — only run via WordPress uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * List of all plugin options to remove on uninstall.
 *
 * @var string[]
 */
$agenticmcp_options = array(
	'agenticmcp_api_key',
	'agenticmcp_store_slug',
	'agenticmcp_mcp_endpoint',
	'agenticmcp_tier',
	'agenticmcp_last_sync',
	'agenticmcp_webhook_secret',
	'agenticmcp_last_billing_event_id',
	'agenticmcp_trial_ends_at',
);

foreach ( $agenticmcp_options as $option ) {
	delete_option( $option );
}
