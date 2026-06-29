<?php
/**
 * Agent event webhook — emits R023 (refund-abuse-guard) signals to backend.
 *
 * Listens for WooCommerce refund and cancellation events on orders that
 * carry the `_trusteed_agent_did` meta (set by the checkout enforcer on
 * agent-initiated ALLOW). POSTs a signed event to /v1/agent-events so the
 * backend can update PlatformOrder dispute/cancel counters that feed R023
 * during subsequent evaluations.
 *
 * Hooks:
 *   - woocommerce_order_refunded       → reason=refunded
 *   - woocommerce_order_status_cancelled → reason=cancelled
 *
 * Fail-open silently on transport errors — these events are best-effort
 * telemetry; the source of truth remains the merchant's order DB.
 *
 * @package AgenticMCPStores
 * @since   1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Amcp_Agent_Event_Webhook
 *
 * @since 1.5.0
 */
class Amcp_Agent_Event_Webhook {

	private string $api_base;
	private string $installation_id;
	private string $hmac_secret;
	private string $merchant_id;

	private const TIMEOUT_SECONDS = 4;

	/**
	 * Gap #10 — blocking-mode visibility constants.
	 *
	 * BLOCKING_TIMEOUT_SECONDS: short to avoid impacting admin UI (refund/cancel
	 *   happen synchronously from WP admin; a long timeout would stall it).
	 * MAX_RETRY_ATTEMPTS: hard cap on cron retries; after that we give up.
	 * RETRY_BASE_DELAY_SECONDS: first retry delay; subsequent retries use
	 *   exponential backoff (60s, 120s, 240s).
	 * RETRY_HOOK: WP-Cron action name used to schedule deferred retries.
	 */
	private const BLOCKING_TIMEOUT_SECONDS  = 3;
	private const MAX_RETRY_ATTEMPTS        = 3;
	private const RETRY_BASE_DELAY_SECONDS  = 60;
	private const RETRY_HOOK                = 'amcp_agent_event_retry';

	/**
	 * F6.PHP1 / CR1 — Hard cap on the JSON body bytes persisted into a cron
	 * row. `wp_schedule_single_event` serialises the args into the
	 * `wp_options` table (cron array). Without this guard, a refund on an
	 * order with many line items could bloat the options blob, slowing every
	 * subsequent autoload. 64KB is comfortably above realistic agent-event
	 * payloads while bounding worst-case DB row size.
	 */
	private const MAX_RETRY_BODY_SIZE = 65536; // 64 * 1024

	/**
	 * F6.PHP1 / S7 — Whitelist of `kind` values the plugin itself emits.
	 *
	 * `handle_retry()` validates the decoded body's `kind` against this set
	 * so an attacker with DB write access (compromised WP) cannot inject a
	 * cron row that would cause the plugin to sign and POST an arbitrary
	 * event type with the merchant's credentials.
	 *
	 * Kept in sync with the literals emitted by `emit_event()` (refunded,
	 * cancelled, order_placed) and `emit_indeterminate()`
	 * (enforcement_indeterminate). Adding a new kind requires updating this
	 * list AND the corresponding emitter call site.
	 */
	private const ALLOWED_RETRY_KINDS = array(
		'refunded',
		'cancelled',
		'order_placed',
		'enforcement_indeterminate',
	);

	/**
	 * F5.S3 — Admin-notice option flag set when an attempted dispatch is
	 * aborted because the HMAC secret is empty (plugin misconfigured or
	 * disconnected mid-flight). Surfaced by class-settings.php via an
	 * admin_notices hook.
	 */
	public const NOTICE_OPTION_HMAC_MISSING = 'amcp_enforcement_hmac_missing_notice';

	public function __construct(
		string $api_base,
		string $installation_id,
		string $hmac_secret,
		string $merchant_id
	) {
		$this->api_base        = rtrim( $api_base, '/' );
		$this->installation_id = $installation_id;
		$this->hmac_secret     = $hmac_secret;
		$this->merchant_id     = $merchant_id;
	}

	/**
	 * Register WooCommerce hooks.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'woocommerce_order_refunded', array( $this, 'on_order_refunded' ), 10, 2 );
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'on_order_cancelled' ), 10, 2 );
		// Gap 7 — emit `order_placed` so backend has a baseline order count to
		// normalize R023 refund ratio. Fires once when checkout finishes and
		// the order object is fully assembled with totals + meta.
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'on_order_placed' ), 10, 3 );

		// Gap #10 — WP-Cron retry hook for transient 5xx / transport failures.
		add_action( self::RETRY_HOOK, array( $this, 'handle_retry' ), 10, 1 );
	}

	/**
	 * Hook: woocommerce_order_refunded.
	 *
	 * @since 1.5.0
	 *
	 * @param int $order_id  Order ID.
	 * @param int $refund_id Refund ID.
	 * @return void
	 */
	public function on_order_refunded( $order_id, $refund_id ): void {
		$this->emit_event( (int) $order_id, 'refunded', array( 'refund_id' => (int) $refund_id ) );
	}

	/**
	 * Hook: woocommerce_order_status_cancelled.
	 *
	 * @since 1.5.0
	 *
	 * @param int $order_id Order ID.
	 * @param mixed $order   Order object (unused, kept for hook signature).
	 * @return void
	 */
	public function on_order_cancelled( $order_id, $order = null ): void {
		$this->emit_event( (int) $order_id, 'cancelled', array() );
	}

	/**
	 * Hook: woocommerce_checkout_order_processed.
	 *
	 * Emits `kind=order_placed` for agent-initiated orders so the backend can
	 * compute the R023 refund-abuse-guard denominator (orders placed per agent)
	 * without inferring it from the refund/cancel stream.
	 *
	 * Skipped silently for orders without `_trusteed_agent_did` (non-agent
	 * orders). Gap 1 ensures classic-checkout orders also carry that meta.
	 *
	 * @since 1.6.0
	 *
	 * @param int   $order_id Order ID.
	 * @param array $posted   Posted checkout data (unused).
	 * @param mixed $order    Order object (passed by some WC versions).
	 * @return void
	 */
	public function on_order_placed( $order_id, $posted = array(), $order = null ): void {
		$order_id = (int) $order_id;
		if ( $order_id <= 0 ) {
			return;
		}

		$wc_order = function_exists( 'wc_get_order' ) ? wc_get_order( $order_id ) : null;
		if ( ! $wc_order ) {
			return;
		}

		$total    = (float) ( method_exists( $wc_order, 'get_total' ) ? $wc_order->get_total() : 0 );
		$currency = (string) ( method_exists( $wc_order, 'get_currency' ) ? $wc_order->get_currency() : '' );

		$this->emit_event(
			$order_id,
			'order_placed',
			array(
				'total'    => $total,
				'currency' => strtoupper( $currency ),
			)
		);
	}

	/**
	 * Emit a synthetic `enforcement_indeterminate` event when the evaluator
	 * call returns INDETERMINATE (network error / 5xx / bad response). Allows
	 * ops to alert on backend Prometheus counter `enforcement_api_fail_total`
	 * instead of grepping plugin error_log entries.
	 *
	 * Carries no order_id — uses synthetic ref `indeterminate_<ts>` so the
	 * event-stream validator accepts the payload.
	 *
	 * @since 1.5.0
	 *
	 * @param string $agent_did Agent DID from current session.
	 * @param string $reason    Short reason code from API client.
	 * @param string $mode      'enforce' or 'observe'.
	 * @return void
	 */
	public function emit_indeterminate( string $agent_did, string $reason, string $mode ): void {
		if ( '' === $agent_did || empty( $this->merchant_id ) || empty( $this->api_base ) ) {
			return;
		}

		$payload = array(
			'merchantId'     => $this->merchant_id,
			'installationId' => $this->installation_id,
			'platform'       => 'woocommerce',
			'agentId'        => $agent_did,
			'orderId'        => 'indeterminate_' . time(),
			'kind'           => 'enforcement_indeterminate',
			'reason'         => $reason,
			'mode'           => $mode,
			'timestamp'      => gmdate( DATE_ATOM ),
		);

		$this->post_signed( $payload );
	}

	/**
	 * Emit a signed agent event to the backend.
	 *
	 * Skips silently when:
	 *   - Order has no _trusteed_agent_did meta (not agent-initiated)
	 *   - merchant_id or api_base unset (plugin not configured)
	 *
	 * @since 1.5.0
	 *
	 * @param int    $order_id Order ID.
	 * @param string $kind     'refunded' | 'cancelled'.
	 * @param array  $extra    Extra payload keys.
	 * @return void
	 */
	private function emit_event( int $order_id, string $kind, array $extra ): void {
		if ( empty( $this->merchant_id ) || empty( $this->api_base ) ) {
			return;
		}

		$order = function_exists( 'wc_get_order' ) ? wc_get_order( $order_id ) : null;
		if ( ! $order ) {
			return;
		}

		$agent_did = (string) $order->get_meta( '_trusteed_agent_did', true );
		if ( '' === $agent_did ) {
			return;
		}

		$payload = array_merge(
			array(
				'merchantId'     => $this->merchant_id,
				'installationId' => $this->installation_id,
				'platform'       => 'woocommerce',
				'agentId'        => $agent_did,
				'orderId'        => (string) $order_id,
				'kind'           => $kind,
				'timestamp'      => gmdate( DATE_ATOM ),
			),
			$extra
		);

		$this->post_signed( $payload );
	}

	/**
	 * POST a signed payload to /v1/agent-events in blocking mode.
	 *
	 * Gap #10 (2026-05-24) — switched from fire-and-forget to blocking with a
	 * short 3s timeout. HTTP-level outcomes are now observable:
	 *   - 2xx / 3xx → success, no log.
	 *   - 4xx       → bug nuestro/auth/validation; log NON-retryable.
	 *   - 5xx       → backend transient; log + schedule WP-Cron retry.
	 *   - WP_Error  → transport failure (timeout/dns/blocked host); retry.
	 *
	 * @since 1.5.0
	 *
	 * @param array $payload Event payload.
	 * @return void
	 */
	private function post_signed( array $payload ): void {
		$raw_body = wp_json_encode( $payload );
		if ( false === $raw_body ) {
			return;
		}

		$headers = $this->build_signed_headers( $raw_body );
		if ( null === $headers ) {
			// F5.S3 — Fail-closed: dispatch aborted upstream when secret missing.
			return;
		}

		$this->dispatch_signed( $raw_body, $headers, 1 );
	}

	/**
	 * Build the signed-request headers for a serialized payload.
	 *
	 * Extracted so retry attempts re-sign with a fresh timestamp (required —
	 * the backend rejects signatures older than its skew window, so reusing
	 * the original `t=` on a retry minutes later would always 401).
	 *
	 * @since 1.7.0
	 *
	 * F5.S3 — Returns null when the HMAC secret is empty. Callers MUST treat
	 * null as fail-closed (do NOT dispatch unsigned/placeholder payloads). The
	 * legacy literal placeholder `'dev-bypass'` is removed entirely so a
	 * misconfigured plugin can never POST a forged-looking signature header.
	 *
	 * @param string $raw_body JSON-encoded payload.
	 * @return array<string,string>|null
	 */
	private function build_signed_headers( string $raw_body ) {
		if ( empty( $this->hmac_secret ) ) {
			// F5.S3 — fail-closed: log + set admin-notice flag. NOT a fatal
			// error to avoid crashing WP admin during refund/cancel; the
			// merchant must reconnect to restore the enforcement_hmac_secret.
			error_log( '[amcp.agent_event] fail-closed: enforcement_hmac_secret missing — dispatch aborted' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			if ( function_exists( 'update_option' ) ) {
				update_option( self::NOTICE_OPTION_HMAC_MISSING, 1, false );
			}
			return null;
		}

		$ts        = time();
		$signature = hash_hmac( 'sha256', $ts . '.' . $raw_body, $this->hmac_secret );

		return array(
			'Content-Type'               => 'application/json',
			'X-Trusteed-Installation-Id' => $this->installation_id,
			'X-Trusteed-Signature'       => 't=' . $ts . ',s=' . $signature,
		);
	}

	/**
	 * Perform the actual blocking POST and decide retry behaviour.
	 *
	 * @since 1.7.0
	 *
	 * @param string                $raw_body Serialized JSON payload.
	 * @param array<string,string>  $headers  HTTP headers (already signed).
	 * @param int                   $attempt  1-based attempt index (1 = first
	 *                                        send, 2..N = cron retry passes).
	 * @return void
	 */
	private function dispatch_signed( string $raw_body, array $headers, int $attempt ): void {
		$response = wp_remote_post(
			$this->api_base . '/v1/agent-events',
			array(
				'timeout'  => self::BLOCKING_TIMEOUT_SECONDS,
				'headers'  => $headers,
				'body'     => $raw_body,
				'blocking' => true,
			)
		);

		// Gap 6 — `blocking => false` means wp_remote_post returns immediately
		// after handing the request to the transport. The only WP_Error path
		// here is a PRE-TRANSPORT failure (malformed URL, blocked host via
		// `WP_HTTP_BLOCK_EXTERNAL`, missing curl/streams transport). HTTP-level
		// errors from the backend are NOT visible in fire-and-forget mode.
		// We intentionally keep this guard as a defense against config drift
		// (e.g. operator sets WP_HTTP_BLOCK_EXTERNAL and forgets to whitelist).
		if ( is_wp_error( $response ) ) {
			error_log( sprintf( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				'[amcp.agent_event] transport error (attempt=%d): %s',
				$attempt,
				$response->get_error_message()
			) );
			$this->schedule_retry( $raw_body, $attempt, 'wp_error' );
			return;
		}

		$http_code = (int) wp_remote_retrieve_response_code( $response );

		if ( $http_code >= 200 && $http_code < 300 ) {
			return;
		}

		if ( $http_code >= 500 ) {
			error_log( sprintf( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				'[amcp.agent_event] HTTP %d retry-able (attempt=%d)',
				$http_code,
				$attempt
			) );
			$this->schedule_retry( $raw_body, $attempt, sprintf( 'http_%d', $http_code ) );
			return;
		}

		// 4xx → bug nuestro/auth/validation; no retry, just surface.
		error_log( sprintf( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			'[amcp.agent_event] HTTP %d non-retryable (attempt=%d)',
			$http_code,
			$attempt
		) );
	}

	/**
	 * Schedule a WP-Cron retry with exponential backoff.
	 *
	 * The body is re-signed at dispatch time, so we persist only the raw
	 * payload bytes — NOT the original `X-Trusteed-Signature` header — to
	 * avoid signature-window expiry between attempts.
	 *
	 * Backoff sequence (with RETRY_BASE_DELAY_SECONDS=60):
	 *   completed_attempt=1 (initial blocking send failed) → +60s  → schedule retry #2
	 *   completed_attempt=2 (retry #2 failed)              → +120s → schedule retry #3
	 *   completed_attempt=3 (retry #3 failed)              → +240s → schedule retry #4
	 *   completed_attempt=4                                → give up (budget exhausted)
	 *
	 * F6.PHP1 / CR1-backoff — 1-indexed semantics so the docblock matches the
	 * formula exactly:
	 *   delay = RETRY_BASE_DELAY_SECONDS * 2^(completed_attempt - 1)
	 *
	 * F6.PHP1 / CR1 — Bodies larger than MAX_RETRY_BODY_SIZE are dropped to
	 * avoid `wp_options` bloat (cron args are serialised into a single row).
	 *
	 * @since 1.7.0
	 *
	 * @param string $raw_body          JSON-encoded payload.
	 * @param int    $completed_attempt 1-based index of the attempt that just
	 *                                  failed (1 = initial blocking send,
	 *                                  2..N = cron retry passes).
	 * @param string $reason            Short reason code for log correlation.
	 * @return void
	 */
	private function schedule_retry( string $raw_body, int $completed_attempt, string $reason ): void {
		// F6.PHP1 / CR1 — size cap guard FIRST: never schedule oversize payloads.
		$body_size = strlen( $raw_body );
		if ( $body_size > self::MAX_RETRY_BODY_SIZE ) {
			error_log( sprintf( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				'[amcp.agent_event_retry] body size %d exceeds %d, skip retry (reason=%s)',
				$body_size,
				self::MAX_RETRY_BODY_SIZE,
				$reason
			) );
			return;
		}

		// MAX_RETRY_ATTEMPTS=3 means up to 3 cron retries scheduled AFTER the
		// initial blocking send (completed_attempt=1). When completed_attempt
		// already equals MAX_RETRY_ATTEMPTS+1=4, all retries have run.
		$next_attempt = $completed_attempt + 1;
		if ( $completed_attempt >= self::MAX_RETRY_ATTEMPTS + 1 ) {
			error_log( sprintf( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				'[amcp.agent_event] retry budget exhausted (max=%d, reason=%s)',
				self::MAX_RETRY_ATTEMPTS,
				$reason
			) );
			return;
		}

		// CR1-backoff: delay grows as 60s, 120s, 240s for completed_attempt 1,2,3.
		$delay = self::RETRY_BASE_DELAY_SECONDS * (int) pow( 2, max( 0, $completed_attempt - 1 ) );

		// WP-Cron serializes args verbatim. Wrap in a single-element array so
		// the action handler receives ONE positional arg (the payload), not
		// each field as separate args.
		$cron_payload = array(
			'body'    => $raw_body,
			'attempt' => $next_attempt,
			'reason'  => $reason,
		);

		wp_schedule_single_event(
			time() + $delay,
			self::RETRY_HOOK,
			array( $cron_payload )
		);
	}

	/**
	 * WP-Cron callback for retry attempts.
	 *
	 * Re-signs the body with a fresh timestamp and dispatches. On further
	 * failure, dispatch_signed() will re-schedule (up to MAX_RETRY_ATTEMPTS).
	 *
	 * Public because WP-Cron hooks must be reachable callbacks.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed $payload Expected shape: { body: string, attempt: int, reason: string }.
	 *                       `body` must be JSON whose decoded shape matches
	 *                       this plugin's own emitter contract (S7 validation).
	 * @return void
	 */
	public function handle_retry( $payload ): void {
		// F6.PHP1 / S7 — Shape validation: anti event-injection. We never trust
		// the cron payload blindly because an attacker with DB write access
		// (compromised WP install) could insert a row that, without these
		// checks, would cause this method to sign + POST arbitrary events
		// using the merchant's credentials.

		if ( ! is_array( $payload ) || ! isset( $payload['body'] ) || ! is_string( $payload['body'] ) ) {
			error_log( '[amcp.agent_event_retry] payload validation failed: malformed envelope' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return;
		}

		// attempt must be an int ≥ 1. Use absint() to reject negatives that
		// could underflow downstream backoff math.
		if ( ! isset( $payload['attempt'] ) || ! is_int( $payload['attempt'] ) ) {
			error_log( '[amcp.agent_event_retry] payload validation failed: attempt missing or not int' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return;
		}
		$attempt = absint( $payload['attempt'] );
		if ( $attempt < 1 ) {
			error_log( sprintf( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				'[amcp.agent_event_retry] payload validation failed: attempt %d below 1',
				$attempt
			) );
			return;
		}

		// Hard stop once the attempt index has walked past the configured
		// budget. Separate branch + distinct log line so ops can tell "max
		// attempts" apart from "validation failed".
		if ( $attempt > self::MAX_RETRY_ATTEMPTS ) {
			error_log( sprintf( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				'[amcp.agent_event_retry] max attempts reached; dropping (attempt=%d, max=%d)',
				$attempt,
				self::MAX_RETRY_ATTEMPTS
			) );
			return;
		}

		$raw_body = $payload['body'];

		// Body size sanity (defence-in-depth — schedule_retry already capped
		// it before insertion, but cron rows can outlive code deploys).
		if ( strlen( $raw_body ) > self::MAX_RETRY_BODY_SIZE ) {
			error_log( sprintf( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				'[amcp.agent_event_retry] payload validation failed: body size %d exceeds %d',
				strlen( $raw_body ),
				self::MAX_RETRY_BODY_SIZE
			) );
			return;
		}

		// Decode + structural validation. Reject everything that does not
		// match the strict envelope our own emitters produce.
		$decoded = json_decode( $raw_body, true );
		if ( ! is_array( $decoded ) || JSON_ERROR_NONE !== json_last_error() ) {
			error_log( '[amcp.agent_event_retry] payload validation failed: body is not valid JSON object' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return;
		}

		// merchantId must equal the installation-bound value. Prevents an
		// attacker from swapping in another merchant's identifier to make
		// telemetry land on the wrong account.
		if ( ! isset( $decoded['merchantId'] ) || ! is_string( $decoded['merchantId'] )
			|| $decoded['merchantId'] !== $this->merchant_id ) {
			error_log( '[amcp.agent_event_retry] payload validation failed: merchantId mismatch' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return;
		}

		// installationId must equal our own. Same threat model as merchantId.
		if ( ! isset( $decoded['installationId'] ) || ! is_string( $decoded['installationId'] )
			|| $decoded['installationId'] !== $this->installation_id ) {
			error_log( '[amcp.agent_event_retry] payload validation failed: installationId mismatch' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return;
		}

		// kind must be one of the values this plugin itself emits. Blocks
		// injection of arbitrary event types.
		if ( ! isset( $decoded['kind'] ) || ! is_string( $decoded['kind'] )
			|| ! in_array( $decoded['kind'], self::ALLOWED_RETRY_KINDS, true ) ) {
			$kind_for_log = ( isset( $decoded['kind'] ) && is_string( $decoded['kind'] ) )
				? substr( $decoded['kind'], 0, 32 )
				: '<missing>';
			error_log( sprintf( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				'[amcp.agent_event_retry] payload validation failed: disallowed kind=%s',
				$kind_for_log
			) );
			return;
		}

		$headers = $this->build_signed_headers( $raw_body );
		if ( null === $headers ) {
			// F5.S3 — secret rotated/disconnected between schedule and dispatch.
			return;
		}

		// Safe log: size + kind + attempt only. NEVER log the full body — it
		// can contain order PII (addresses, totals, refund reasons).
		// Decision: dispatch.
		$this->dispatch_signed( $raw_body, $headers, $attempt );
	}
}
