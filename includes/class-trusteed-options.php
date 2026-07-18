<?php
/**
 * Option accessor with legacy `amcp_*` read-fallback.
 *
 * The plugin was renamed from `agenticmcpstores` to `trusteed`. Persisted
 * option rows written by pre-rename installs still carry the `amcp_` prefix.
 * Writes always target the new `trusteed_*` key; reads prefer the new key and
 * fall back to the legacy `amcp_*` key so already-installed merchants keep
 * working after upgrade without a data migration.
 *
 * @package Trusteed
 * @since   2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Trusteed_Options {

	/**
	 * Read an option by its bare key (no prefix), preferring the `trusteed_`
	 * namespace and falling back to the legacy `amcp_` namespace.
	 *
	 * @param string $key     Bare option key, e.g. `merchant_id`.
	 * @param mixed  $default Value returned when neither key exists.
	 * @return mixed Stored value or $default.
	 */
	public static function get_option( string $key, $default = false ) {
		$value = get_option( "trusteed_{$key}", null );
		if ( null !== $value ) {
			return $value;
		}
		return get_option( "amcp_{$key}", $default );
	}

	/**
	 * Delete both the new and legacy rows for a bare key. Used on disconnect
	 * so stale credentials cannot be resurrected by the read-fallback.
	 *
	 * @param string $key Bare option key.
	 * @return void
	 */
	public static function delete_option( string $key ): void {
		delete_option( "trusteed_{$key}" );
		delete_option( "amcp_{$key}" );
	}
}
