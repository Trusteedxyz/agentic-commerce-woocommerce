<?php
/**
 * Checkout enforcer — intercepts WooCommerce checkout for agent-initiated orders.
 *
 * Supports both checkout paths:
 *
 * CLASSIC checkout (woocommerce_checkout_process):
 *  1. Cart bridge appends ?amcp_agent_id=<did> to the checkout URL.
 *  2. `woocommerce_before_checkout_form` saves the DID to WC session.
 *  3. `woocommerce_checkout_process` fires on POST: calls /v1/rules/evaluate.
 *  4. On BLOCK → wc_add_notice() aborts.
 *
 * BLOCKS checkout (woocommerce_store_api_checkout_update_order_from_request):
 *  1. Same URL capture and WC session storage (step 2 above fires on page load).
 *  2. Store API fires the hook on the AJAX checkout POST.
 *  3. On BLOCK → RouteException with 403 aborts the order.
 *
 * Fail-open design: connectivity issues, timeouts, or misconfiguration
 * never prevent a legitimate checkout from completing.
 *
 * @package AgenticMCPStores
 * @since   1.3.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Amcp_Checkout_Enforcer
 *
 * @since 1.3.0
 */
class Amcp_Checkout_Enforcer {

	/**
	 * Enforcement API client.
	 *
	 * @since 1.3.0
	 * @var Amcp_Enforcement_Api_Client
	 */
	private Amcp_Enforcement_Api_Client $api_client;

	/**
	 * Merchant UUID.
	 *
	 * @since 1.3.0
	 * @var string
	 */
	private string $merchant_id;

	/**
	 * Installation UUID passed in evaluate requests.
	 *
	 * @since 1.3.0
	 * @var string
	 */
	private string $installation_id;

	/**
	 * Snapshot client — fetches agentDidResolver for Ed25519 verification.
	 *
	 * @since 1.4.0
	 * @var Amcp_Snapshot_Client_Woo|null
	 */
	private ?Amcp_Snapshot_Client_Woo $snapshot_client;

	/**
	 * Failure mode policy (spec-048 Gap 5/6 tri-state fail-closed).
	 *
	 * 'enforce' → INDETERMINATE maps to BLOCK (fail-closed).
	 * 'observe' → INDETERMINATE maps to ALLOW + telemetry log (fail-open).
	 *
	 * @since 1.5.0
	 * @var string
	 */
	private string $failure_mode;

	/**
	 * Optional agent-event webhook used to POST `enforcement_indeterminate`
	 * telemetry to backend so `enforcement_api_fail_total` Prometheus counter
	 * ticks per fail-open / fail-closed event.
	 *
	 * @since 1.5.0
	 * @var Amcp_Agent_Event_Webhook|null
	 */
	private ?Amcp_Agent_Event_Webhook $event_webhook;

	/**
	 * Constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param Amcp_Enforcement_Api_Client   $api_client      Enforcement API client.
	 * @param string                        $merchant_id     Merchant UUID.
	 * @param string                        $installation_id Installation UUID.
	 * @param Amcp_Snapshot_Client_Woo|null $snapshot_client Optional snapshot client for token verification.
	 * @param string                        $failure_mode    'enforce' (fail-closed) or 'observe' (fail-open).
	 */
	public function __construct(
		Amcp_Enforcement_Api_Client $api_client,
		string $merchant_id,
		string $installation_id,
		?Amcp_Snapshot_Client_Woo $snapshot_client = null,
		string $failure_mode = 'enforce',
		?Amcp_Agent_Event_Webhook $event_webhook = null
	) {
		$this->api_client      = $api_client;
		$this->merchant_id     = $merchant_id;
		$this->installation_id = $installation_id;
		$this->snapshot_client = $snapshot_client;
		$this->failure_mode    = 'observe' === $failure_mode ? 'observe' : 'enforce';
		$this->event_webhook   = $event_webhook;
	}

	/**
	 * Register WooCommerce hooks.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function init(): void {
		// Capture agent_id from URL into WC session on any front-end page load.
		// template_redirect fires after WC is fully initialized (including session).
		add_action( 'template_redirect', array( $this, 'capture_agent_session_early' ), 1 );

		// Classic checkout template hook (shortcode path).
		add_action( 'woocommerce_before_checkout_form', array( $this, 'capture_agent_session' ), 1 );

		// Classic checkout: fires on POST form submit.
		add_action( 'woocommerce_checkout_process', array( $this, 'enforce_rules_classic' ) );

		// Blocks / Store API checkout: fires during AJAX order creation.
		add_action( 'woocommerce_store_api_checkout_update_order_from_request', array( $this, 'enforce_rules_blocks' ), 10, 2 );
	}

	/**
	 * Capture agent_id early (before woocommerce_before_checkout_form fires).
	 *
	 * For blocks checkout the template hook may not fire, so we also hook on
	 * 'wp' which runs on every front-end page load including the checkout page.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function capture_agent_session_early(): void {
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
			return;
		}
		// Bail if no DID param in URL — avoid unnecessary work.
		if ( ! isset( $_GET[ Trusteed_Cart_Bridge::AGENT_ID_PARAM ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		$this->capture_agent_session();
	}

	/**
	 * Save the agent_id and optional agent JWS token URL params into the WC session.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function capture_agent_session(): void {
		if ( ! isset( $_GET[ Trusteed_Cart_Bridge::AGENT_ID_PARAM ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		$raw = sanitize_text_field(
			rawurldecode( wp_unslash( $_GET[ Trusteed_Cart_Bridge::AGENT_ID_PARAM ] ) ) // phpcs:ignore WordPress.Security.NonceVerification
		);

		if ( ! preg_match( Trusteed_Cart_Bridge::AGENT_DID_RE, $raw ) ) {
			return;
		}

		if ( WC()->session ) {
			WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENT_ID, $raw );

			// Capture agent JWS token if present (used for R002 signature verification).
			if ( isset( $_GET[ Trusteed_Cart_Bridge::AGENT_TOKEN_PARAM ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$raw_token = sanitize_text_field(
					rawurldecode( wp_unslash( $_GET[ Trusteed_Cart_Bridge::AGENT_TOKEN_PARAM ] ) ) // phpcs:ignore WordPress.Security.NonceVerification
				);
				// Basic format gate: JWS Compact is three base64url segments separated by dots.
				if ( preg_match( '/^[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]*$/', $raw_token ) ) {
					WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $raw_token );
				}
			}

			WC()->session->save_data();
		}
	}

	/**
	 * Enforce rules for the classic checkout (woocommerce_checkout_process).
	 *
	 * Throws \Exception on BLOCK — WooCommerce catches it during
	 * checkout processing and aborts order creation, re-rendering the
	 * checkout page with the error message. This matches the hard-fail
	 * semantics of the Store API path (spec-048 enforcement parity).
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 * @throws \Exception On BLOCK decision.
	 */
	public function enforce_rules_classic(): void {
		// Snapshot the agent DID BEFORE run_evaluation clears the WC session.
		// Required so the deferred meta-persistence hook below can stamp the
		// order with `_trusteed_agent_did` (parity with Blocks/Store API path)
		// so refund/cancel webhook + R023 refund-abuse-guard can fire for
		// shortcode-checkout orders too.
		$agent_id_for_meta = WC()->session
			? WC()->session->get( Trusteed_Cart_Bridge::SESSION_AGENT_ID )
			: null;

		$decision = $this->run_evaluation( $this->get_order_context_from_post() );

		if ( 'BLOCK' === $decision ) {
			throw new \Exception(
				esc_html__(
					'Your order could not be completed because it did not pass the store\'s agent verification rules. Please contact the store if you believe this is an error.',
					'agenticmcpstores'
				)
			);
		}

		// ALLOW path — delegate to Amcp_Classic_Meta_Persister which registers
		// a one-shot `woocommerce_checkout_create_order` hook to stamp the
		// three metas the Blocks path writes inline.
		if ( ! empty( $agent_id_for_meta ) && is_string( $agent_id_for_meta ) ) {
			Amcp_Classic_Meta_Persister::register( $agent_id_for_meta );
		}
	}

	/**
	 * Enforce rules for the Blocks / Store API checkout.
	 *
	 * Fires via woocommerce_store_api_checkout_update_order_from_request.
	 * Throws RouteException on BLOCK so the Store API returns a 422 with
	 * the block reason visible to the client.
	 *
	 * @since 1.3.0
	 *
	 * @param \WC_Order        $order   The order being created.
	 * @param \WP_REST_Request $request The Store API request.
	 *
	 * @return void
	 * @throws \Automattic\WooCommerce\StoreApi\Exceptions\RouteException On BLOCK.
	 */
	public function enforce_rules_blocks( $order, $request ): void {
		// Snapshot agent_id before run_evaluation clears the session.
		$agent_id_for_meta = WC()->session
			? WC()->session->get( Trusteed_Cart_Bridge::SESSION_AGENT_ID )
			: null;

		$order_context = $this->build_order_context_from_order( $order, $request );
		$decision      = $this->run_evaluation( $order_context );

		if ( 'BLOCK' === $decision ) {
			if ( class_exists( '\Automattic\WooCommerce\StoreApi\Exceptions\RouteException' ) ) {
				throw new \Automattic\WooCommerce\StoreApi\Exceptions\RouteException(
					'trusteed_agent_blocked',
					__(
						'Your order could not be completed because it did not pass the store\'s agent verification rules. Please contact the store if you believe this is an error.',
						'agenticmcpstores'
					),
					403
				);
			}
			// Fallback: add notice (will surface on next page load).
			wc_add_notice(
				__(
					'Your order could not be completed because it did not pass the store\'s agent verification rules.',
					'agenticmcpstores'
				),
				'error'
			);
			return;
		}

		// ALLOW path: persist agent DID + R022 cart-attr status so the order webhook
		// can propagate agentIdHash to PlatformOrder for R023 refund-abuse-guard
		// enforcement and downstream readers can verify agent provenance.
		if ( ! empty( $agent_id_for_meta ) && is_string( $agent_id_for_meta ) ) {
			$order->update_meta_data( '_trusteed_agent_did', sanitize_text_field( $agent_id_for_meta ) );
			$order->update_meta_data( '_trusteed_agent_status', 'ok' );
			$order->update_meta_data( '_trusteed_eval_at', gmdate( DATE_ATOM ) );
			$order->save_meta_data();
		}
	}

	/**
	 * Read agent_id and token from session, verify signature, call evaluate, clear session.
	 *
	 * Tri-state outcome from API client (spec-048 Gap 5/6):
	 *   ALLOW         → return 'ALLOW'
	 *   BLOCK         → return 'BLOCK'
	 *   INDETERMINATE → 'enforce' mode returns 'BLOCK' (fail-closed),
	 *                   'observe' mode returns 'ALLOW' + telemetry log.
	 *
	 * Skipped entirely (return 'ALLOW') when not an agent cart, merchant unset,
	 * or WC session unavailable.
	 *
	 * @since 1.3.0
	 *
	 * @param array $order_context Pre-built orderContext array.
	 * @return string 'ALLOW' or 'BLOCK'.
	 */
	private function run_evaluation( array $order_context ): string {
		if ( ! WC()->session ) {
			return 'ALLOW';
		}

		$agent_id = WC()->session->get( Trusteed_Cart_Bridge::SESSION_AGENT_ID );

		// Not an agent-initiated cart — skip enforcement entirely.
		if ( empty( $agent_id ) || ! is_string( $agent_id ) ) {
			return 'ALLOW';
		}

		// Merchant not configured — cannot evaluate. In enforce mode this is a
		// hard fail (would silently bypass otherwise); in observe mode allow + log.
		if ( empty( $this->merchant_id ) ) {
			return $this->apply_failure_mode( 'merchant_id_unset' );
		}

		// Verify agent token signature and enrich orderContext cartAttributes.
		$order_context = $this->enrich_with_token_verification( $order_context );

		$payload = array(
			'merchantId'     => $this->merchant_id,
			'agentId'        => $agent_id,
			'orderContext'   => $order_context,
			'platform'       => 'WOOCOMMERCE',
			'installationId' => $this->installation_id,
			'timestamp'      => gmdate( DATE_ATOM ),
		);

		$result = $this->api_client->evaluate( $payload );

		// Clear session regardless of decision to avoid re-checking on retry.
		WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENT_ID, null );
		WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, null );

		if ( Amcp_Eval_Outcome::INDETERMINATE === $result->outcome ) {
			return $this->apply_failure_mode( $result->reason );
		}

		return Amcp_Eval_Outcome::BLOCK === $result->outcome ? 'BLOCK' : 'ALLOW';
	}

	/**
	 * Map INDETERMINATE result to ALLOW/BLOCK per failure_mode policy.
	 *
	 * Emits:
	 *   - Structured error_log line (grep-able fallback).
	 *   - POST /v1/agent-events kind=enforcement_indeterminate when an
	 *     event_webhook + agent_did are available, so backend Prometheus
	 *     counter `enforcement_api_fail_total{platform,reason,mode}` ticks.
	 *
	 * @since 1.5.0
	 *
	 * @param string $reason Short reason code from API client.
	 * @return string 'ALLOW' (observe) or 'BLOCK' (enforce).
	 */
	private function apply_failure_mode( string $reason ): string {
		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			sprintf(
				'[amcp.enforcement_indeterminate] platform=woocommerce merchant=%s mode=%s reason=%s',
				$this->merchant_id,
				$this->failure_mode,
				$reason
			)
		);

		if ( null !== $this->event_webhook && WC()->session ) {
			$agent_did = (string) ( WC()->session->get( Trusteed_Cart_Bridge::SESSION_AGENT_ID ) ?? '' );
			if ( '' !== $agent_did ) {
				$this->event_webhook->emit_indeterminate( $agent_did, $reason, $this->failure_mode );
			}
		}

		return 'enforce' === $this->failure_mode ? 'BLOCK' : 'ALLOW';
	}

	/**
	 * Read the agent JWS token from session, verify its Ed25519 signature, and
	 * add _agent_token_present / _agent_token_signature_invalid to cartAttributes.
	 *
	 * Fail-open: if token is absent or verification is INDETERMINATE, no attributes
	 * are added (R002 will not fire).
	 *
	 * @since 1.4.0
	 *
	 * @param array $order_context Existing orderContext.
	 * @return array orderContext with cartAttributes enriched.
	 */
	private function enrich_with_token_verification( array $order_context ): array {
		$jws_token = WC()->session ? WC()->session->get( Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN ) : null;

		if ( empty( $jws_token ) || ! is_string( $jws_token ) ) {
			// No token present — no attributes added. Fail open.
			return $order_context;
		}

		if ( ! isset( $order_context['cartAttributes'] ) || ! is_array( $order_context['cartAttributes'] ) ) {
			$order_context['cartAttributes'] = array();
		}

		$order_context['cartAttributes']['_agent_token_present'] = 'true';

		if ( null === $this->snapshot_client ) {
			// Snapshot client not configured — cannot verify. Fail open.
			return $order_context;
		}

		$did_resolver = $this->snapshot_client->get_did_resolver( $this->merchant_id );
		$result       = Amcp_Token_Verifier::verify( $jws_token, $did_resolver, $this->merchant_id );

		if ( $result->is_invalid() ) {
			$order_context['cartAttributes']['_agent_token_signature_invalid'] = 'true';

			// Surface jti-specific failures as explicit attributes so operators can
			// diagnose tokens minted without single-use identifiers (spec-048 P2.8).
			if ( 'missing_jti' === $result->error ) {
				$order_context['cartAttributes']['_agent_token_jti_missing'] = 'true';
			} elseif ( 'bad_jti' === $result->error ) {
				$order_context['cartAttributes']['_agent_token_jti_malformed'] = 'true';
			}
		}
		// VALID or INDETERMINATE → no _agent_token_signature_invalid flag (fail open).

		// Spec-048 P2.8 — single-use replay protection. Only consume nonces for
		// VALID tokens (INDETERMINATE / INVALID are already handled above).
		if ( $result->is_valid() && '' !== $result->jti ) {
			$nonce_outcome = $this->api_client->consume_nonce(
				$this->merchant_id,
				$result->agent_did,
				$result->jti,
				$result->exp
			);

			if ( Amcp_Nonce_Outcome::REPLAY === $nonce_outcome->outcome ) {
				// Token reused — downgrade to INVALID and tag attributes so the rule
				// evaluator can reject (R002 / R005 etc.) and ops can alert.
				$order_context['cartAttributes']['_agent_token_signature_invalid'] = 'true';
				$order_context['cartAttributes']['_agent_token_replay']            = 'true';
			} elseif ( Amcp_Nonce_Outcome::INDETERMINATE === $nonce_outcome->outcome ) {
				// Honor failure_mode: enforce → mark INVALID (fail-closed); observe →
				// leave token VALID + emit telemetry (parity with rules/evaluate path).
				if ( 'enforce' === $this->failure_mode ) {
					$order_context['cartAttributes']['_agent_token_signature_invalid']   = 'true';
					$order_context['cartAttributes']['_agent_token_nonce_unavailable'] = 'true';
				} else {
					$order_context['cartAttributes']['_agent_token_nonce_unavailable'] = 'true';
					error_log( '[amcp] nonce-consume indeterminate (observe): ' . $nonce_outcome->reason ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				}
			}
			// ACCEPTED → nothing to do, token remains VALID.
		}

		// R004: first-seen key age tracking via WP transients (persistent across requests).
		if ( '' !== $result->kid ) {
			$transient_key = 'amcp_kid_fs_' . hash( 'sha256', $result->kid );
			$first_seen    = get_transient( $transient_key );
			if ( false === $first_seen ) {
				$first_seen = time();
				set_transient( $transient_key, $first_seen, YEAR_IN_SECONDS );
			}
			$order_context['cartAttributes']['_agent_key_age_hours'] = (string) round( ( time() - (int) $first_seen ) / 3600, 2 );
		}

		return $order_context;
	}

	/**
	 * Build orderContext from classic checkout $_POST data.
	 *
	 * @since 1.3.0
	 *
	 * @return array orderContext payload.
	 */
	private function get_order_context_from_post(): array {
		$cart     = WC()->cart;
		$currency = get_woocommerce_currency();

		$total_cents = (int) round( (float) $cart->get_total( 'edit' ) * 100 );

		$line_items       = array();
		$product_cat_slugs = array();
		foreach ( $cart->get_cart() as $item ) {
			$product = $item['data'];
			if ( ! $product instanceof \WC_Product ) {
				continue;
			}
			$line_items[] = array(
				'id'         => (string) $item['product_id'],
				'qty'        => (int) $item['quantity'],
				'priceCents' => (int) round( (float) $product->get_price() * 100 ),
			);
			// Collect server-side product categories (R012 high-risk-category enforcement).
			$terms = wp_get_post_terms( (int) $item['product_id'], 'product_cat', array( 'fields' => 'slugs' ) );
			if ( is_array( $terms ) ) {
				foreach ( $terms as $slug ) {
					$product_cat_slugs[ $slug ] = true;
				}
			}
		}

		$context = array(
			'cartTotalCents' => $total_cents,
			'currency'       => strtoupper( $currency ),
			'itemCount'      => (int) $cart->get_cart_contents_count(),
			'discountCodes'  => array_values( array_keys( WC()->cart->get_applied_coupons() ) ),
			'lineItems'      => $line_items,
		);

		if ( ! empty( $product_cat_slugs ) ) {
			$context['cartAttributes']['_product_categories'] = implode( ',', array_keys( $product_cat_slugs ) );
			// M1 (audit): declare the platform so the enforcement engine maps native
			// WooCommerce term slugs (wine/spirits) to canonical taxonomy (alcohol)
			// for R006/R012/R032. Without it those rules silently raw-match only.
			$context['cartAttributes']['_product_platform'] = 'woocommerce';
		}

		if ( ! empty( $_POST['shipping_country'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$sc = strtoupper( sanitize_text_field( wp_unslash( $_POST['shipping_country'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
			if ( 2 === strlen( $sc ) ) {
				$context['shippingCountry'] = $sc;
			}
		}

		if ( ! empty( $_POST['billing_country'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$bc = strtoupper( sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
			if ( 2 === strlen( $bc ) ) {
				$context['billingCountry'] = $bc;
			}
		}

		if ( ! empty( $_POST['payment_method'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$context['paymentMethod'] = sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		// B6 — R015/R016/R025/R026/R027/R028 signal injection.
		$context = $this->apply_cart_signals_from_cart( $cart, $context );

		// B6 — PO box / freight forwarder from POST address fields.
		$ship_a1 = isset( $_POST['shipping_address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['shipping_address_1'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$ship_a2 = isset( $_POST['shipping_address_2'] ) ? sanitize_text_field( wp_unslash( $_POST['shipping_address_2'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$ship_co = isset( $_POST['shipping_company'] )   ? sanitize_text_field( wp_unslash( $_POST['shipping_company'] ) )   : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$bill_co = isset( $_POST['billing_company'] )    ? sanitize_text_field( wp_unslash( $_POST['billing_company'] ) )    : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$context = $this->apply_address_signals( $context, $ship_a1, $ship_a2, $ship_co, $bill_co );

		return $context;
	}

	/**
	 * Build orderContext from a WC_Order and Store API request (blocks checkout).
	 *
	 * @since 1.3.0
	 *
	 * @param \WC_Order        $order   The WC order object.
	 * @param \WP_REST_Request $request The Store API request.
	 * @return array orderContext payload.
	 */
	private function build_order_context_from_order( $order, $request ): array {
		$currency    = $order->get_currency();
		$total_cents = (int) round( (float) $order->get_total() * 100 );

		$line_items        = array();
		$item_count        = 0;
		$product_cat_slugs = array();
		foreach ( $order->get_items() as $item ) {
			$product_id = (int) $item->get_product_id();
			$qty        = (int) $item->get_quantity();
			$subtotal   = (float) $item->get_subtotal();
			$unit_cents = $qty > 0 ? (int) round( ( $subtotal / $qty ) * 100 ) : 0;

			$line_items[] = array(
				'id'         => (string) $product_id,
				'qty'        => $qty,
				'priceCents' => $unit_cents,
			);
			$item_count  += $qty;

			// Collect server-side product categories (R012 high-risk-category enforcement).
			$terms = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'slugs' ) );
			if ( is_array( $terms ) ) {
				foreach ( $terms as $slug ) {
					$product_cat_slugs[ $slug ] = true;
				}
			}
		}

		$coupon_codes = array();
		foreach ( $order->get_coupon_codes() as $code ) {
			$coupon_codes[] = $code;
		}

		$context = array(
			'cartTotalCents' => $total_cents,
			'currency'       => strtoupper( $currency ),
			'itemCount'      => $item_count,
			'discountCodes'  => $coupon_codes,
			'lineItems'      => $line_items,
		);

		if ( ! empty( $product_cat_slugs ) ) {
			$context['cartAttributes']['_product_categories'] = implode( ',', array_keys( $product_cat_slugs ) );
			// M1 (audit): declare the platform so the enforcement engine maps native
			// WooCommerce term slugs (wine/spirits) to canonical taxonomy (alcohol)
			// for R006/R012/R032. Without it those rules silently raw-match only.
			$context['cartAttributes']['_product_platform'] = 'woocommerce';
		}

		$billing_country = $order->get_billing_country();
		if ( ! empty( $billing_country ) ) {
			$context['billingCountry'] = strtoupper( $billing_country );
		}

		$shipping_country = $order->get_shipping_country();
		if ( ! empty( $shipping_country ) ) {
			$context['shippingCountry'] = strtoupper( $shipping_country );
		}

		$payment_method = $order->get_payment_method();
		if ( ! empty( $payment_method ) ) {
			$context['paymentMethod'] = $payment_method;
		}

		// B6 — R015/R016/R025/R026/R027/R028 signal injection.
		$context = $this->apply_cart_signals_from_order( $order, $context );
		$context = $this->apply_address_signals(
			$context,
			(string) $order->get_shipping_address_1(),
			(string) $order->get_shipping_address_2(),
			(string) $order->get_shipping_company(),
			(string) $order->get_billing_company()
		);

		return $context;
	}

	/**
	 * B6 — inject R015/R016/R026/R027 cartAttributes from a WC_Cart.
	 *
	 * @param \WC_Cart $cart    Active cart.
	 * @param array    $context Existing orderContext.
	 * @return array
	 */
	private function apply_cart_signals_from_cart( $cart, array $context ): array {
		$items = array();
		foreach ( $cart->get_cart() as $cart_item ) {
			$product = $cart_item['data'] ?? null;
			if ( ! $product instanceof \WC_Product ) {
				continue;
			}
			$items[] = $this->wc_product_to_signal( $product, (int) ( $cart_item['quantity'] ?? 1 ) );
		}
		return $this->merge_signals( $context, $items, (int) ( $cart->id ?? 0 ) );
	}

	/**
	 * B6 — inject R015/R016/R026/R027 cartAttributes from a WC_Order.
	 *
	 * @param \WC_Order $order   Order being placed.
	 * @param array     $context Existing orderContext.
	 * @return array
	 */
	private function apply_cart_signals_from_order( $order, array $context ): array {
		$items = array();
		foreach ( $order->get_items() as $item ) {
			$product = method_exists( $item, 'get_product' ) ? $item->get_product() : null;
			if ( ! $product instanceof \WC_Product ) {
				continue;
			}
			$items[] = $this->wc_product_to_signal( $product, (int) $item->get_quantity() );
		}
		return $this->merge_signals( $context, $items, (int) $order->get_id() );
	}

	/**
	 * Convert a WC_Product + quantity into the primitive signal dict used by
	 * AgenticMCP_Cart_Signals helpers. Pure data — no further WC calls.
	 *
	 * @param \WC_Product $product Product object.
	 * @param int         $qty     Line quantity.
	 * @return array{id:string,type:string,price_cents:int,qty:int,stock:int|null,tags:string[],categories:string[]}
	 */
	private function wc_product_to_signal( $product, int $qty ): array {
		$pid    = (int) $product->get_id();
		$type   = (string) $product->get_type();
		$price  = (int) round( (float) $product->get_price() * 100 );
		$stock  = $product->managing_stock() ? (int) $product->get_stock_quantity() : null;
		$tags   = wp_get_post_terms( $pid, 'product_tag', array( 'fields' => 'slugs' ) );
		$cats   = wp_get_post_terms( $pid, 'product_cat', array( 'fields' => 'slugs' ) );
		return array(
			'id'          => (string) $pid,
			'type'        => $type,
			'price_cents' => $price,
			'qty'         => max( 1, $qty ),
			'stock'       => $stock,
			'tags'        => is_array( $tags ) ? array_values( $tags ) : array(),
			'categories'  => is_array( $cats ) ? array_values( $cats ) : array(),
		);
	}

	/**
	 * Merge per-item primitive signals into orderContext.cartAttributes via
	 * the pure-function helpers in AgenticMCP_Cart_Signals.
	 *
	 * R015 price-snap verification: reads the per-merchant HMAC key from the
	 * latest signed snapshot (R015.params.priceSnapHmacKeyHex). Cookie
	 * `amcp_price_snap_{cart_id}` carries the envelope written at add-to-cart
	 * time. Missing key / cookie / invalid HMAC → R015 simply has no evidence.
	 *
	 * @param array $context Existing orderContext.
	 * @param array $items   Primitive item signal dicts.
	 * @param int   $cart_id Numeric cart identifier (used for the price-snap cookie key).
	 * @return array
	 */
	private function merge_signals( array $context, array $items, int $cart_id ): array {
		if ( empty( $items ) ) {
			return $context;
		}
		if ( ! isset( $context['cartAttributes'] ) || ! is_array( $context['cartAttributes'] ) ) {
			$context['cartAttributes'] = array();
		}

		$lowest = AgenticMCP_Cart_Signals::lowest_stock( $items );
		if ( $lowest !== null ) {
			$context['cartAttributes']['_lowest_stock'] = (string) $lowest;
		}

		if ( AgenticMCP_Cart_Signals::cart_has_subscription( $items ) ) {
			$context['cartAttributes']['_subscription'] = 'true';
		}

		$stored = AgenticMCP_Cart_Signals::stored_value_cents( $items );
		if ( $stored > 0 ) {
			$context['cartAttributes']['_stored_value_cents'] = (string) $stored;
		}

		// R015 — verify HMAC-signed price snapshot cookie if present.
		$hmac_key = $this->resolve_r015_hmac_key();
		$cookie   = isset( $_COOKIE[ 'amcp_price_snap_' . $cart_id ] ) ? (string) $_COOKIE[ 'amcp_price_snap_' . $cart_id ] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification
		if ( $cookie !== '' && $hmac_key !== '' ) {
			$snapshot = AgenticMCP_Cart_Signals::verify_price_snap( $cookie, $hmac_key );
			if ( ! empty( $snapshot ) ) {
				$current = array();
				foreach ( $items as $signal ) {
					$current[ (string) $signal['id'] ] = (int) $signal['price_cents'];
				}
				$delta = AgenticMCP_Cart_Signals::max_price_delta_bps( $current, $snapshot );
				if ( $delta > 0 ) {
					$context['cartAttributes']['_price_delta_bps'] = (string) $delta;
				}
			}
		}

		return $context;
	}

	/**
	 * B6 — inject R025 + R028 cartAttributes derived from address fields.
	 *
	 * @param array  $context Existing orderContext.
	 * @param string $ship_a1 Shipping address line 1.
	 * @param string $ship_a2 Shipping address line 2.
	 * @param string $ship_co Shipping company (used as freight-forwarder probe).
	 * @param string $bill_co Billing company (primary B2B signal).
	 * @return array
	 */
	private function apply_address_signals( array $context, string $ship_a1, string $ship_a2, string $ship_co, string $bill_co ): array {
		if ( ! isset( $context['cartAttributes'] ) || ! is_array( $context['cartAttributes'] ) ) {
			$context['cartAttributes'] = array();
		}

		if ( AgenticMCP_Cart_Signals::detect_po_box( $ship_a1, $ship_a2 ) ) {
			$context['cartAttributes']['_shipping_po_box'] = 'true';
		}
		if ( AgenticMCP_Cart_Signals::detect_freight_forwarder( $ship_a1, $ship_a2, $ship_co ) ) {
			$context['cartAttributes']['_shipping_freight_forwarder'] = 'true';
		}

		$user_id = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0;
		$roles   = array();
		if ( $user_id > 0 && function_exists( 'wp_get_current_user' ) ) {
			$user = wp_get_current_user();
			if ( $user && isset( $user->roles ) && is_array( $user->roles ) ) {
				$roles = $user->roles;
			}
		}
		if ( AgenticMCP_Cart_Signals::is_b2b_order( $bill_co, $user_id, $roles ) ) {
			$context['cartAttributes']['_b2b_order'] = 'true';
		}

		return $context;
	}

	/**
	 * B6 — read per-merchant R015 HMAC key from the latest signed snapshot.
	 * Returns '' when the snapshot client is absent or R015 is not configured.
	 */
	private function resolve_r015_hmac_key(): string {
		if ( null === $this->snapshot_client || ! method_exists( $this->snapshot_client, 'get_rules' ) ) {
			return '';
		}
		try {
			$rules = $this->snapshot_client->get_rules( $this->merchant_id );
		} catch ( \Throwable $e ) {
			return '';
		}
		if ( ! is_array( $rules ) ) {
			return '';
		}
		foreach ( $rules as $rule ) {
			if ( ! is_array( $rule ) ) {
				continue;
			}
			$code = (string) ( $rule['ruleCode'] ?? '' );
			if ( $code !== 'R015' && strpos( $code, 'R015.' ) !== 0 ) {
				continue;
			}
			$params = $rule['params'] ?? array();
			if ( is_array( $params ) && ! empty( $params['priceSnapHmacKeyHex'] ) ) {
				return (string) $params['priceSnapHmacKeyHex'];
			}
		}
		return '';
	}
}
