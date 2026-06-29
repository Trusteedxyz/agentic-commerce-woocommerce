<?php
/**
 * Enforcement API client — calls POST /v1/rules/evaluate.
 *
 * Signs requests with HMAC-SHA256 in Stripe-style format:
 *   X-Trusteed-Signature: t=<unix>,s=<sha256-hex>
 *   where signature = HMAC-SHA256("${t}.${rawBody}", secret)
 *
 * Returns tri-state outcome: ALLOW | BLOCK | INDETERMINATE.
 * Caller decides BLOCK vs ALLOW on INDETERMINATE based on failure_mode
 * (spec-048 Gap 5/6 — fail-closed when ENFORCE, fail-open + telemetry when OBSERVE).
 *
 * @package AgenticMCPStores
 * @since   1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Outcome enum for evaluate() results.
 *
 * @since 1.5.0
 */
class Amcp_Eval_Outcome {
	const ALLOW         = 'ALLOW';
	const BLOCK         = 'BLOCK';
	const INDETERMINATE = 'INDETERMINATE';
}

/**
 * Outcome of consume_nonce() — single-use replay protection (spec-048 P2.8).
 *
 *   ACCEPTED       — HTTP 200, nonce stored, token may be used.
 *   REPLAY         — HTTP 409, nonce already seen — caller must mark token INVALID.
 *   INDETERMINATE  — network / 5xx / 4xx / bad response — caller maps per failure_mode.
 *
 * @since 1.6.0
 */
class Amcp_Nonce_Outcome {
	const ACCEPTED      = 'ACCEPTED';
	const REPLAY        = 'REPLAY';
	const INDETERMINATE = 'INDETERMINATE';
}

/**
 * Result of consume_nonce().
 *
 * @since 1.6.0
 */
class Amcp_Nonce_Result {

	/** @var string Amcp_Nonce_Outcome::* */
	public string $outcome;

	/** @var string Short reason code. */
	public string $reason;

	/** @var int|null HTTP status when available. */
	public ?int $http_status;

	public function __construct( string $outcome, string $reason, ?int $http_status = null ) {
		$this->outcome     = $outcome;
		$this->reason      = $reason;
		$this->http_status = $http_status;
	}
}

/**
 * Result of an evaluate call.
 *
 * @since 1.5.0
 */
class Amcp_Eval_Result {

	/** @var string Amcp_Eval_Outcome::* */
	public string $outcome;

	/** @var string Short reason code (network_error, http_5xx, bad_response, ok). */
	public string $reason;

	/** @var int|null HTTP status if response received, null otherwise. */
	public ?int $http_status;

	public function __construct( string $outcome, string $reason, ?int $http_status = null ) {
		$this->outcome     = $outcome;
		$this->reason      = $reason;
		$this->http_status = $http_status;
	}
}

/**
 * Class Amcp_Enforcement_Api_Client
 *
 * @since 1.3.0
 */
class Amcp_Enforcement_Api_Client {

	private string $api_base;
	private string $installation_id;
	private string $hmac_secret;

	private const TIMEOUT_SECONDS = 5;

	/**
	 * Gap 5 — sliding window + threshold for the config-drift admin notice.
	 * Counts /v1/rules/evaluate HTTP-4xx responses; when the count exceeds
	 * the threshold within the window, we set a 24h notice transient.
	 */
	private const EVAL_4XX_RATE_TRANSIENT      = 'amcp_eval_4xx_rate';
	private const EVAL_4XX_RATE_WINDOW_SECONDS = 900;   // 15 min
	private const EVAL_4XX_RATE_THRESHOLD      = 10;
	public const  EVAL_4XX_NOTICE_TRANSIENT    = 'amcp_eval_config_drift_notice';
	private const EVAL_4XX_NOTICE_TTL_SECONDS  = 86400; // 24h

	public function __construct( string $api_base, string $installation_id, string $hmac_secret ) {
		$this->api_base        = rtrim( $api_base, '/' );
		$this->installation_id = $installation_id;
		$this->hmac_secret     = $hmac_secret;
	}

	/**
	 * Call /v1/rules/evaluate and return a tri-state result.
	 *
	 * Network errors, timeouts, HTTP 5xx, malformed responses → INDETERMINATE.
	 * Caller maps INDETERMINATE to BLOCK or ALLOW per failure_mode policy.
	 *
	 * @since 1.5.0
	 *
	 * @param array $payload Evaluate payload.
	 * @return Amcp_Eval_Result
	 */
	public function evaluate( array $payload ): Amcp_Eval_Result {
		$url      = $this->api_base . '/v1/rules/evaluate';
		$raw_body = wp_json_encode( $payload );

		if ( false === $raw_body ) {
			return new Amcp_Eval_Result( Amcp_Eval_Outcome::INDETERMINATE, 'json_encode_failed' );
		}

		$signature = $this->build_signature( $raw_body );
		if ( '' === $signature ) {
			// F5.S3 — fail-closed: never POST a placeholder/unsigned request.
			return new Amcp_Eval_Result( Amcp_Eval_Outcome::INDETERMINATE, 'hmac_secret_missing' );
		}

		$headers = array(
			'Content-Type'               => 'application/json',
			'X-Trusteed-Installation-Id' => $this->installation_id,
			'X-Trusteed-Signature'       => $signature,
		);

		$response = wp_remote_post(
			$url,
			array(
				'timeout' => self::TIMEOUT_SECONDS,
				'headers' => $headers,
				'body'    => $raw_body,
			)
		);

		if ( is_wp_error( $response ) ) {
			error_log( '[amcp] enforcement evaluate network error: ' . $response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return new Amcp_Eval_Result( Amcp_Eval_Outcome::INDETERMINATE, 'network_error' );
		}

		$status = (int) wp_remote_retrieve_response_code( $response );

		if ( $status >= 500 ) {
			error_log( '[amcp] enforcement evaluate HTTP ' . $status ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return new Amcp_Eval_Result( Amcp_Eval_Outcome::INDETERMINATE, 'http_5xx', $status );
		}

		if ( 200 !== $status ) {
			// 4xx — backend rejected payload. Treat as INDETERMINATE (config bug),
			// not as ALLOW, so ENFORCE mode surfaces it instead of silently passing.
			error_log( '[amcp] enforcement evaluate unexpected HTTP ' . $status ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			$this->record_4xx_rate();
			return new Amcp_Eval_Result( Amcp_Eval_Outcome::INDETERMINATE, 'http_4xx', $status );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) || empty( $body['decision'] ) ) {
			return new Amcp_Eval_Result( Amcp_Eval_Outcome::INDETERMINATE, 'bad_response', $status );
		}

		if ( 'BLOCK' === $body['decision'] ) {
			return new Amcp_Eval_Result( Amcp_Eval_Outcome::BLOCK, 'ok', $status );
		}

		return new Amcp_Eval_Result( Amcp_Eval_Outcome::ALLOW, 'ok', $status );
	}

	/**
	 * Call POST /v1/agent-events/nonce-consume to record a single-use
	 * agent-token jti (spec-048 P2.8 replay protection).
	 *
	 * @since 1.6.0
	 *
	 * @param string $merchant_id Merchant UUID (must match installation merchant).
	 * @param string $agent_did   Agent DID from the verified JWS iss/kid claim.
	 * @param string $jti         Single-use nonce — base64url 16–128 chars.
	 * @param int    $exp         Token exp (unix seconds). Used as nonce TTL upper bound.
	 * @return Amcp_Nonce_Result
	 */
	public function consume_nonce( string $merchant_id, string $agent_did, string $jti, int $exp ): Amcp_Nonce_Result {
		$url = $this->api_base . '/v1/agent-events/nonce-consume';

		$payload = array(
			'merchantId'     => $merchant_id,
			'installationId' => $this->installation_id,
			'agentId'        => $agent_did,
			'nonce'          => $jti,
			'expiresAt'      => gmdate( 'Y-m-d\TH:i:s\Z', $exp > 0 ? $exp : ( time() + 300 ) ),
		);

		$raw_body = wp_json_encode( $payload );
		if ( false === $raw_body ) {
			return new Amcp_Nonce_Result( Amcp_Nonce_Outcome::INDETERMINATE, 'json_encode_failed' );
		}

		$signature = $this->build_signature( $raw_body );
		if ( '' === $signature ) {
			// F5.S3 — fail-closed.
			return new Amcp_Nonce_Result( Amcp_Nonce_Outcome::INDETERMINATE, 'hmac_secret_missing' );
		}

		$headers = array(
			'Content-Type'               => 'application/json',
			'X-Trusteed-Installation-Id' => $this->installation_id,
			'X-Trusteed-Signature'       => $signature,
		);

		$response = wp_remote_post(
			$url,
			array(
				'timeout' => self::TIMEOUT_SECONDS,
				'headers' => $headers,
				'body'    => $raw_body,
			)
		);

		if ( is_wp_error( $response ) ) {
			return new Amcp_Nonce_Result( Amcp_Nonce_Outcome::INDETERMINATE, 'network_error' );
		}

		$status = (int) wp_remote_retrieve_response_code( $response );

		if ( 200 === $status ) {
			return new Amcp_Nonce_Result( Amcp_Nonce_Outcome::ACCEPTED, 'ok', $status );
		}
		if ( 409 === $status ) {
			return new Amcp_Nonce_Result( Amcp_Nonce_Outcome::REPLAY, 'replay_detected', $status );
		}
		if ( $status >= 500 ) {
			return new Amcp_Nonce_Result( Amcp_Nonce_Outcome::INDETERMINATE, 'http_5xx', $status );
		}
		return new Amcp_Nonce_Result( Amcp_Nonce_Outcome::INDETERMINATE, 'http_4xx', $status );
	}

	/**
	 * Gap 5 — track HTTP-4xx evaluate responses in a 15-minute sliding window
	 * and, when they exceed the threshold, set a 24h admin-notice transient
	 * so the merchant sees that their plugin is misconfigured (bad
	 * installationId / merchantId / hmac_secret) instead of relying on ops
	 * spotting it in error_log.
	 *
	 * Best-effort: failures to read/write transients are swallowed so the
	 * evaluate hot path is never affected.
	 *
	 * @return void
	 */
	private function record_4xx_rate(): void {
		if ( ! function_exists( 'get_transient' ) || ! function_exists( 'set_transient' ) ) {
			return;
		}

		$now    = time();
		$record = get_transient( self::EVAL_4XX_RATE_TRANSIENT );
		if ( ! is_array( $record ) || ! isset( $record['start'] ) || ( $now - (int) $record['start'] ) > self::EVAL_4XX_RATE_WINDOW_SECONDS ) {
			$record = array(
				'start' => $now,
				'count' => 0,
			);
		}

		$record = array(
			'start' => (int) $record['start'],
			'count' => ( (int) $record['count'] ) + 1,
		);

		set_transient( self::EVAL_4XX_RATE_TRANSIENT, $record, self::EVAL_4XX_RATE_WINDOW_SECONDS );

		if ( $record['count'] >= self::EVAL_4XX_RATE_THRESHOLD ) {
			set_transient( self::EVAL_4XX_NOTICE_TRANSIENT, 1, self::EVAL_4XX_NOTICE_TTL_SECONDS );
		}
	}

	/**
	 * Build the `t=<ts>,s=<hex>` signature header.
	 *
	 * F5.S3 — Returns empty string when the HMAC secret is unset. Callers
	 * MUST treat empty as fail-closed (do NOT send the request). The legacy
	 * `'dev-bypass'` literal is removed so a misconfigured plugin can never
	 * emit a forged placeholder header that a buggy backend might accept.
	 *
	 * @param string $raw_body Request body.
	 * @return string Signature header value, or empty string on fail-closed.
	 */
	private function build_signature( string $raw_body ): string {
		if ( empty( $this->hmac_secret ) ) {
			error_log( '[amcp.enforcement_api] fail-closed: enforcement_hmac_secret missing' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			if ( function_exists( 'update_option' ) ) {
				update_option( Amcp_Agent_Event_Webhook::NOTICE_OPTION_HMAC_MISSING, 1, false );
			}
			return '';
		}

		$ts        = time();
		$message   = $ts . '.' . $raw_body;
		$signature = hash_hmac( 'sha256', $message, $this->hmac_secret );

		return 't=' . $ts . ',s=' . $signature;
	}
}
