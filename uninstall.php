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
 * Dar de baja el comercio en Trusteed ANTES de borrar nada — 2026-09-05.
 *
 * Hasta hoy este fichero borraba las opciones locales y no avisaba a nadie: el
 * comercio desaparecía de su WordPress y seguía publicado en
 * `/.well-known/merchant-index.json`, en `/api/v1/stores/directory` y con su
 * endpoint MCP anunciado, con la puntuación recalculándose sobre un catálogo
 * congelado. No había ninguna otra señal — WooCommerce no tiene el equivalente
 * al `app/uninstalled` de Shopify.
 *
 * Va primero a propósito: necesita la clave y la URL base que el bucle de
 * abajo borra. Y es best-effort: si el backend no contesta, la desinstalación
 * sigue igual. Un `uninstall.php` que falla deja al comerciante con un plugin
 * a medio borrar, y eso es peor que un registro desactualizado — para eso está
 * el barrido por latido, que no depende de este aviso.
 *
 * Se cargan DOS clases sueltas, y ninguna arranca el plugin ni registra hooks:
 * son las dos autocontenidas que hacen falta para hablar con la API.
 */
// `__DIR__` y no `plugin_dir_path()`: una función menos de WordPress en un
// fichero cuya cabecera exige mantenerlo libre de dependencias.
require_once __DIR__ . '/includes/class-crypto-helper.php';
require_once __DIR__ . '/includes/class-disconnect.php';

$trusteed_uninstall_key = Trusteed_Crypto_Helper::decrypt(
	(string) get_option( 'agenticmcp_api_key', '' )
);

if ( '' !== $trusteed_uninstall_key ) {
	$trusteed_uninstall_base = (string) get_option( 'trusteed_api_base_url', '' );
	if ( '' === $trusteed_uninstall_base ) {
		$trusteed_uninstall_base = (string) get_option( 'amcp_api_base_url', '' );
	}
	if ( '' === $trusteed_uninstall_base ) {
		$trusteed_uninstall_base = defined( 'TRUSTEED_API_BASE' )
			? (string) TRUSTEED_API_BASE
			: 'https://api.trusteed.xyz';
	}
	// `rtrim` y no `untrailingslashit()`: misma razón que `__DIR__` arriba.
	$trusteed_uninstall_base = rtrim( $trusteed_uninstall_base, '/' );

	Trusteed_Disconnect::run(
		static function ( $endpoint, $body ) use ( $trusteed_uninstall_base, $trusteed_uninstall_key ) {
			$response = wp_remote_post(
				$trusteed_uninstall_base . $endpoint,
				array(
					'timeout' => 10,
					'headers' => array(
						'Content-Type'     => 'application/json',
						'Accept'           => 'application/json',
						'X-AgenticMCP-Key' => $trusteed_uninstall_key,
					),
					'body'    => wp_json_encode( $body ),
				)
			);

			if ( is_wp_error( $response ) ) {
				return null;
			}

			$decoded = json_decode( (string) wp_remote_retrieve_body( $response ), true );

			return array(
				'status' => (int) wp_remote_retrieve_response_code( $response ),
				'body'   => is_array( $decoded ) ? $decoded : array(),
			);
		}
	);
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
	'agent_orders_enabled',
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
