<?php
/**
 * Classic-checkout agent-DID meta persister (Gap 1).
 *
 * Extracted from Amcp_Checkout_Enforcer to keep that file under the 800-line
 * limit set in CLAUDE.md while preserving the single-responsibility split:
 * the enforcer decides ALLOW/BLOCK, this helper stamps order meta when the
 * classic-checkout path can't write to a WC_Order inline.
 *
 * @package AgenticMCPStores
 * @since   1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Amcp_Classic_Meta_Persister
 *
 * @since 1.6.0
 */
class Amcp_Classic_Meta_Persister {

	/**
	 * Per-request guard so repeated invocations (e.g. payment gateway
	 * re-entry) do not stack multiple closures on the same hook.
	 *
	 * @var array<string,bool>
	 */
	private static array $registered_for = array();

	/**
	 * Register a one-shot `woocommerce_checkout_create_order` closure that
	 * stamps the order with `_trusteed_agent_did` + `_trusteed_agent_status`
	 * + `_trusteed_eval_at` (parity with the Blocks/Store API path).
	 *
	 * @param string $agent_did Verified agent DID captured pre-evaluation.
	 * @return void
	 */
	public static function register( string $agent_did ): void {
		if ( '' === $agent_did ) {
			return;
		}
		if ( isset( self::$registered_for[ $agent_did ] ) ) {
			return;
		}
		self::$registered_for[ $agent_did ] = true;

		$persist = static function ( $order ) use ( $agent_did ): void {
			if ( ! $order instanceof \WC_Order ) {
				return;
			}
			$order->update_meta_data( '_trusteed_agent_did', sanitize_text_field( $agent_did ) );
			$order->update_meta_data( '_trusteed_agent_status', 'ok' );
			$order->update_meta_data( '_trusteed_eval_at', gmdate( DATE_ATOM ) );
			// Meta is persisted automatically when WooCommerce saves the order
			// later in the checkout flow; an explicit save_meta_data() here
			// would emit a redundant UPDATE query.
		};

		add_action( 'woocommerce_checkout_create_order', $persist, 10, 1 );
	}

	/**
	 * Test-only: reset the static guard between cases.
	 *
	 * @internal
	 * @return void
	 */
	public static function reset_for_tests(): void {
		self::$registered_for = array();
	}
}
