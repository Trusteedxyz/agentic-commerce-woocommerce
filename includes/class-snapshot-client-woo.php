<?php
/**
 * WooCommerce rule-snapshot client.
 *
 * Fetches the signed RuleSnapshot JWS from GET /v1/rules/snapshot/:merchantId,
 * verifies its Ed25519 signature against the Trusteed JWKS, and returns the
 * agentDidResolver array for use in agent token verification.
 *
 * Caching:
 *   - Snapshot  : WP transient, TTL = min(validUntil, 300s)
 *   - JWKS      : WP transient, TTL = 3600s
 *
 * Since 2.3.6 (plan 04, Task 23a) it says WHY there is no snapshot
 * ({@see get_snapshot_result()}): `tampered` (the served JWS does not verify,
 * even after re-downloading the JWKS once) or `unavailable` (network/HTTP
 * error, JWKS unreachable, no `sodium`). A payload that could not be verified
 * is never used any more: before 2.3.6 a JWKS outage, a missing `sodium` or a
 * SodiumException decoded the payload WITHOUT verifying it, so its resolver
 * and its rules (offline valve) were taken on trust. The checkout enforcer
 * decides with Trusteed_Enforcement_Failure_Policy::decide_degraded().
 * get_did_resolver()/get_rules() keep returning an empty array when there is
 * no verified snapshot.
 *
 * Also since 2.3.6 (Task 24) a verified snapshot whose header says
 * `zip: DEF` (the service compresses payloads over 50 KB) is inflated with a
 * 2 MiB cap by Trusteed_Snapshot_Payload_Decoder; one that cannot be read is
 * `unavailable`, not `tampered` (before, every such snapshot was `tampered`).
 *
 * @package Trusteed
 * @since   1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Task 24: payloads `zip: DEF` (inflated, capped, after the signature).
require_once __DIR__ . '/class-snapshot-payload-decoder.php';

/**
 * Class Trusteed_Snapshot_Client_Woo
 *
 * @since 1.4.0
 */
class Trusteed_Snapshot_Client_Woo {

	/** WP transient key prefix for snapshot cache (per merchant). */
	private const SNAP_TRANSIENT_PREFIX = 'trusteed_snap_';

	/** WP transient key for JWKS cache. */
	private const JWKS_TRANSIENT_KEY = 'trusteed_jwks';

	/** JWKS cache TTL in seconds. */
	private const JWKS_TTL_SECONDS = 3600;

	/** HTTP timeout for snapshot and JWKS requests. */
	private const TIMEOUT_SECONDS = 5;

	/**
	 * Task 29b (L3): how long a forced JWKS re-download that did NOT rescue the
	 * signature is remembered (WP transient). Inside the window a snapshot that
	 * does not verify is classified with what that retry already found, without
	 * a second download.
	 */
	const JWKS_NEGATIVE_TTL_SECONDS = 60;

	/** Prefix of the WP transient key of the negative cache of the forced JWKS retry (per server and kid). */
	private const JWKS_NEGATIVE_KEY = 'trusteed_jwks_forced_retry_failed';

	/**
	 * Task 29b (L6b): cap on the snapshot HTTP body, checked before verifying.
	 * A legitimate compact JWS is at most ~2.7 MiB (2 MiB inflate cap, base64url).
	 * `limit_response_size` truncates instead of failing, so the request asks
	 * for one byte more and anything longer than the cap is refused.
	 */
	const MAX_RESPONSE_BYTES = 4194304;

	/**
	 * `validUntil` as the server emits it (`toISOString()`) or `date('c')`.
	 */
	private const ISO_8601 = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})$/D';

	/** get_snapshot_result() status: a verified snapshot (fresh or from the cache of verified payloads). */
	const STATUS_OK = 'ok';

	/** get_snapshot_result() status: the served JWS does not verify, even after re-downloading the JWKS once. */
	const STATUS_TAMPERED = 'tampered';

	/** get_snapshot_result() status: no verified snapshot (network/HTTP error, JWKS unreachable, no sodium). */
	const STATUS_UNAVAILABLE = 'unavailable';

	/**
	 * Outcome per merchant for this instance (one request): a failure is not
	 * re-fetched by the second reader (token verification, then the offline
	 * valve).
	 *
	 * @var array<string, array{status: string, payload: ?array}>
	 */
	private array $results = array();

	/** @var bool The JWKS was already re-downloaded once in this instance. */
	private bool $jwks_refreshed = false;

	/** @var string API base URL (no trailing slash). */
	private string $api_base;

	/** @var string Installation UUID. */
	private string $installation_id;

	/** @var string HMAC secret for X-Trusteed-Signature header. */
	private string $hmac_secret;

	/**
	 * Constructor.
	 *
	 * @param string $api_base        API base URL.
	 * @param string $installation_id Installation UUID.
	 * @param string $hmac_secret     HMAC-SHA256 secret (empty = dev bypass).
	 */
	public function __construct( string $api_base, string $installation_id, string $hmac_secret ) {
		$this->api_base        = rtrim( $api_base, '/' );
		$this->installation_id = $installation_id;
		$this->hmac_secret     = $hmac_secret;
	}

	/**
	 * Snapshot status ⇒ degraded kind for the failure policy (Task 23a):
	 * tampered ⇒ `invalid_signature`, unavailable ⇒ `missing_snapshot`, ok ⇒ null.
	 *
	 * @param string $status STATUS_*.
	 * @return string|null Trusteed_Enforcement_Failure_Policy::KIND_* (literal values).
	 */
	public static function degraded_kind( string $status ): ?string {
		if ( self::STATUS_OK === $status ) {
			return null;
		}
		return self::STATUS_TAMPERED === $status ? 'invalid_signature' : 'missing_snapshot';
	}

	/**
	 * Return the agentDidResolver array for a merchant.
	 *
	 * Returns empty array (fail-open) on any error.
	 *
	 * @param string $merchant_id Merchant UUID.
	 * @return array Array of { did, publicKeyJwk }.
	 */
	public function get_did_resolver( string $merchant_id ): array {
		$payload = $this->get_snapshot_result( $merchant_id )['payload'];
		if ( null === $payload ) {
			return array();
		}
		return isset( $payload['agentDidResolver'] ) && is_array( $payload['agentDidResolver'] )
			? $payload['agentDidResolver']
			: array();
	}

	/**
	 * Return the snapshot `rules[]` array for a merchant — the merchant-policy
	 * ruleset (ruleCode/enabled/params per rule) consumed by
	 * {@see Trusteed_Offline_Safety_Valve_Evaluator} when the remote
	 * `/v1/rules/evaluate` call is unavailable.
	 *
	 * App Store remediation (2026-07-11). Shares the same cached payload as
	 * {@see get_did_resolver()} — no extra network round-trip when both are
	 * called within the same request (the common case: token verification
	 * calls get_did_resolver() first, then a failed evaluate() call triggers
	 * the offline fallback which calls get_rules()).
	 *
	 * @param string $merchant_id Merchant UUID.
	 * @return array Snapshot `rules[]`, or empty array on any error.
	 */
	public function get_rules( string $merchant_id ): array {
		$payload = $this->get_snapshot_result( $merchant_id )['payload'];
		if ( null === $payload ) {
			return array();
		}
		return isset( $payload['rules'] ) && is_array( $payload['rules'] )
			? $payload['rules']
			: array();
	}

	/**
	 * The verified snapshot payload of a merchant, and why there is none
	 * (2.3.6, Task 23a). Shared by get_did_resolver(), get_rules() and the
	 * checkout enforcer, so all of them read from a single cache entry /
	 * network fetch.
	 *
	 * The transient cache only ever holds payloads that verified. A merchant
	 * id that is empty is `unavailable` (the enforcer treats an unconfigured
	 * merchant before getting here).
	 *
	 * @param string $merchant_id Merchant UUID.
	 * @return array{status: string, payload: ?array} status STATUS_OK | STATUS_TAMPERED | STATUS_UNAVAILABLE; payload only with STATUS_OK.
	 */
	public function get_snapshot_result( string $merchant_id ): array {
		if ( empty( $merchant_id ) ) {
			return array( 'status' => self::STATUS_UNAVAILABLE, 'payload' => null );
		}
		if ( isset( $this->results[ $merchant_id ] ) ) {
			return $this->results[ $merchant_id ];
		}

		$result                        = $this->load_snapshot( $merchant_id );
		$this->results[ $merchant_id ] = $result;
		return $result;
	}

	/**
	 * @param string $merchant_id Merchant UUID.
	 * @return array{status: string, payload: ?array}
	 */
	private function load_snapshot( string $merchant_id ): array {
		$transient_key = self::SNAP_TRANSIENT_PREFIX . substr( md5( $merchant_id ), 0, 12 );
		$cached        = get_transient( $transient_key );
		// L6a: the cache TTL is clamped to >= 10 s, so a copy can outlive its own
		// signed `validUntil`; an expired one is absent, not served. A copy with
		// no readable `validUntil` is malformed and absent too (Task 38c).
		if ( is_array( $cached ) && ! self::payload_not_usable( $cached ) ) {
			return array( 'status' => self::STATUS_OK, 'payload' => $cached );
		}

		$verdict = $this->fetch_and_verify_snapshot( $merchant_id );
		if ( self::STATUS_OK !== $verdict['status'] ) {
			return $verdict;
		}
		$payload = (array) $verdict['payload'];

		// L6a — anti-rollback: `validUntil` travels INSIDE the signed payload, so a
		// MITM cannot lengthen it but can replay an old, validly signed snapshot.
		// An expired snapshot is unavailable (same semantics as Magento, PrestaShop
		// and WooCommerce AgenticTools). Task 38c: a snapshot signed by our server
		// ALWAYS carries `validUntil`; absent or unreadable is a malformed payload
		// and gets the same classification as the expired one (`unavailable`, NOT
		// `tampered`: the signature verified).
		if ( self::payload_not_usable( $payload ) ) {
			error_log( '[amcp] snapshot expired or without a readable validUntil' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return array( 'status' => self::STATUS_UNAVAILABLE, 'payload' => null );
		}

		// Cache until validUntil or 60s, whichever is smaller, max 300s.
		$ttl = 60;
		if ( ! empty( $payload['validUntil'] ) ) {
			$valid_until = strtotime( $payload['validUntil'] );
			if ( $valid_until > 0 ) {
				$ttl = min( 300, max( 10, $valid_until - time() ) );
			}
		}

		set_transient( $transient_key, $payload, $ttl );
		return array( 'status' => self::STATUS_OK, 'payload' => $payload );
	}

	/**
	 * Fetch the snapshot JWS from the API and verify its Ed25519 signature.
	 *
	 * @param string $merchant_id
	 * @return array{status: string, payload: ?array}
	 */
	private function fetch_and_verify_snapshot( string $merchant_id ): array {
		$unavailable = array( 'status' => self::STATUS_UNAVAILABLE, 'payload' => null );
		$url         = $this->api_base . '/v1/rules/snapshot/' . rawurlencode( $merchant_id );
		$raw_body    = '';

		// Spec-048 FR-008 — negotiate the contract wire format: a bare JWS
		// Compact body with Content-Type application/jose. Mirrors the
		// WP-plugin / PrestaShop / Odoo clients.
		$headers           = $this->build_hmac_headers( $raw_body, 'GET' );
		$headers['Accept'] = 'application/jose';

		$response = wp_remote_get(
			$url,
			array(
				'timeout'             => self::TIMEOUT_SECONDS,
				'limit_response_size' => self::MAX_RESPONSE_BYTES + 1,
				'headers'             => $headers,
			)
		);

		if ( is_wp_error( $response ) ) {
			error_log( '[amcp] snapshot fetch error: ' . $response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return $unavailable;
		}

		$status = (int) wp_remote_retrieve_response_code( $response );
		if ( 200 !== $status ) {
			error_log( '[amcp] snapshot fetch HTTP ' . $status ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return $unavailable;
		}

		// Contract response is the bare JWS Compact string. Trim trailing
		// whitespace/newlines before verifying.
		$raw_body = (string) wp_remote_retrieve_body( $response );
		if ( strlen( $raw_body ) > self::MAX_RESPONSE_BYTES ) {
			error_log( '[amcp] snapshot response over the size cap' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return $unavailable; // Not something we would ever verify.
		}
		$jws_compact = trim( $raw_body );
		if ( '' === $jws_compact ) {
			return $unavailable;
		}

		$verdict = $this->verify_snapshot_jws( $jws_compact );
		if ( self::STATUS_TAMPERED === $verdict['status'] && ! $this->jwks_refreshed ) {
			// L3: a forced retry that failed less than a minute ago already told us
			// what a new one would; do not download the JWKS again.
			$known = $this->known_forced_retry_failure( $jws_compact );
			if ( null !== $known ) {
				return array( 'status' => $known, 'payload' => null );
			}
			// A key rotation within the JWKS hour must not look like tampering:
			// re-download the JWKS once, ignoring its cache, and verify again.
			$this->jwks_refreshed = true;
			$keys = $this->get_jwks( true );
			if ( null === $keys || array() === $keys ) {
				$this->remember_forced_retry_failure( $jws_compact, self::STATUS_UNAVAILABLE );
				return $unavailable; // JWKS down/empty: a rotation cannot be told from tampering.
			}
			$verdict = $this->verify_snapshot_jws( $jws_compact );
			if ( self::STATUS_TAMPERED === $verdict['status'] ) {
				$this->remember_forced_retry_failure( $jws_compact, self::STATUS_TAMPERED );
			}
		}
		if ( self::STATUS_TAMPERED === $verdict['status'] ) {
			error_log( '[amcp] snapshot JWS does not verify (tampered)' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		}
		return $verdict;
	}

	/**
	 * Verify the snapshot JWS signature against the Trusteed JWKS. Never
	 * decodes an unverified payload (before 2.3.6 a JWKS outage, a missing
	 * `sodium` or a SodiumException did).
	 *
	 * @param string $jws_compact JWS Compact Serialization.
	 * @return array{status: string, payload: ?array}
	 */
	private function verify_snapshot_jws( string $jws_compact ): array {
		$tampered    = array( 'status' => self::STATUS_TAMPERED, 'payload' => null );
		$unavailable = array( 'status' => self::STATUS_UNAVAILABLE, 'payload' => null );

		if ( ! function_exists( 'sodium_crypto_sign_verify_detached' ) ) {
			return $unavailable; // Cannot verify: our outage, not the snapshot's fault.
		}

		$parts = explode( '.', $jws_compact );
		if ( 3 !== count( $parts ) ) {
			return $tampered;
		}
		[ $header_b64, $payload_b64, $sig_b64 ] = $parts;

		$header_json = self::b64url_decode( $header_b64 );
		$header      = $header_json ? json_decode( $header_json, true ) : null;
		if ( ! is_array( $header ) || 'EdDSA' !== ( $header['alg'] ?? '' ) ) {
			return $tampered;
		}
		if ( array_key_exists( 'crit', $header ) ) {
			// RFC 7515 §4.1.11: a `crit` header lists extensions the verifier MUST
			// understand; this one understands none, so the JWS is rejected.
			return $tampered;
		}
		$kid = is_string( $header['kid'] ?? null ) ? $header['kid'] : '';

		$jwks = $this->get_jwks();
		if ( null === $jwks ) {
			return $unavailable; // JWKS unreachable.
		}

		$pubkey_bytes = $this->resolve_jwks_key( $jwks, $kid );
		if ( null === $pubkey_bytes ) {
			return $tampered; // Unknown kid (verify re-downloads the JWKS once).
		}

		$sig_bytes = self::b64url_decode( $sig_b64 );
		if ( false === $sig_bytes || SODIUM_CRYPTO_SIGN_BYTES !== strlen( $sig_bytes ) ) {
			return $tampered;
		}

		$signing_input = $header_b64 . '.' . $payload_b64;
		try {
			$valid = sodium_crypto_sign_verify_detached( $sig_bytes, $signing_input, $pubkey_bytes );
		} catch ( \SodiumException $e ) {
			return $tampered; // Lengths are checked above: a malformed input.
		}

		if ( ! $valid ) {
			return $tampered;
		}

		// Only after the signature verified: inflate (if `zip`), capped, and read.
		$payload_bytes = self::b64url_decode( $payload_b64 );
		list( $decoded, $payload ) = Trusteed_Snapshot_Payload_Decoder::decode( $header, false === $payload_bytes ? '' : $payload_bytes );
		if ( Trusteed_Snapshot_Payload_Decoder::OK === $decoded ) {
			return array( 'status' => self::STATUS_OK, 'payload' => $payload );
		}
		// With `zip` the platform's signature verified but we cannot read it
		// (unknown zip, bomb, broken stream): not usable, not tampering.
		return Trusteed_Snapshot_Payload_Decoder::declares_zip( $header )
			? $unavailable
			: $tampered; // An authentic signature over something that is not our payload.
	}

	/**
	 * Can the payload NOT be used? A snapshot signed by our server always carries
	 * `validUntil`, an ISO-8601 string with zone: absent, of another type,
	 * unreadable (`strtotime` alone would accept "tomorrow") or already past ⇒
	 * not usable.
	 *
	 * @param array $payload Verified snapshot payload.
	 * @return bool
	 */
	private static function payload_not_usable( array $payload ): bool {
		$raw = isset( $payload['validUntil'] ) ? $payload['validUntil'] : null;
		if ( ! is_string( $raw ) || 1 !== preg_match( self::ISO_8601, $raw ) ) {
			return true;
		}
		$until = strtotime( $raw );

		return false === $until || $until <= time();
	}

	/**
	 * Transient key of the negative cache of the forced JWKS retry: one entry per
	 * server AND per `kid` of the JWS being verified, so a failure with no
	 * relation to a key does not taint a legitimate rotation to another one
	 * (L1). A JWS whose header carries no readable string `kid` shares one fixed
	 * entry. The kid is hashed (the transient name is capped at 172 chars).
	 *
	 * @param string $jws_compact JWS Compact Serialization being verified.
	 * @return string
	 */
	private function negative_cache_key( string $jws_compact ): string {
		$kid   = '';
		$parts = explode( '.', $jws_compact );
		if ( isset( $parts[0] ) ) {
			$json   = self::b64url_decode( $parts[0] );
			$header = $json ? json_decode( $json, true ) : null;
			if ( is_array( $header ) && isset( $header['kid'] ) && is_string( $header['kid'] ) ) {
				$kid = $header['kid'];
			}
		}

		return self::JWKS_NEGATIVE_KEY . '_' . md5( $this->api_base ) . '_' . ( '' === $kid ? 'nokid' : md5( $kid ) );
	}

	/**
	 * What the last forced JWKS retry (< 60 s ago) concluded, if it failed to
	 * rescue the signature: STATUS_TAMPERED or STATUS_UNAVAILABLE. A store that
	 * cannot be read counts as "nothing known" (the retry runs).
	 *
	 * @return string|null
	 */
	private function known_forced_retry_failure( string $jws_compact ): ?string {
		try {
			$value = get_transient( $this->negative_cache_key( $jws_compact ) );
		} catch ( \Throwable $e ) {
			return null;
		}

		return in_array( $value, array( self::STATUS_TAMPERED, self::STATUS_UNAVAILABLE ), true ) ? $value : null;
	}

	/**
	 * Remember a forced retry that did not rescue the signature. A failed write
	 * is harmless: without the entry the next request simply retries.
	 *
	 * @param string $status STATUS_TAMPERED | STATUS_UNAVAILABLE.
	 * @return void
	 */
	private function remember_forced_retry_failure( string $jws_compact, string $status ): void {
		try {
			set_transient( $this->negative_cache_key( $jws_compact ), $status, self::JWKS_NEGATIVE_TTL_SECONDS );
		} catch ( \Throwable $e ) {
			// No negative cache: the retry just runs again next time.
		}
	}

	/**
	 * Fetch and cache the Trusteed JWKS.
	 *
	 * `$force` skips the 1 h cache (a key rotation). A failed download returns
	 * null and leaves the cache as it was.
	 *
	 * @param bool $force Ignore the cached JWKS.
	 * @return array|null JWKS keys array or null on failure.
	 */
	private function get_jwks( bool $force = false ): ?array {
		$cached = $force ? false : get_transient( self::JWKS_TRANSIENT_KEY );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$response = wp_remote_get(
			$this->api_base . '/.well-known/jwks.json',
			array( 'timeout' => self::TIMEOUT_SECONDS )
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return null;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		$keys = isset( $body['keys'] ) && is_array( $body['keys'] ) ? $body['keys'] : null;
		if ( null !== $keys ) {
			set_transient( self::JWKS_TRANSIENT_KEY, $keys, self::JWKS_TTL_SECONDS );
		}
		return $keys;
	}

	/**
	 * Find the Ed25519 public key bytes for $kid from the JWKS keys array.
	 *
	 * @param array  $keys JWKS keys array.
	 * @param string $kid  Key ID to look up.
	 * @return string|null 32-byte public key or null.
	 */
	private function resolve_jwks_key( array $keys, string $kid ): ?string {
		foreach ( $keys as $key ) {
			if ( ( $key['kid'] ?? '' ) !== $kid ) {
				continue;
			}
			if ( 'OKP' !== ( $key['kty'] ?? '' ) || 'Ed25519' !== ( $key['crv'] ?? '' ) ) {
				continue;
			}
			$pubkey = self::b64url_decode( $key['x'] ?? '' );
			if ( false !== $pubkey && SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES === strlen( $pubkey ) ) {
				return $pubkey;
			}
		}
		return null;
	}

	/**
	 * Build HMAC authentication headers for outbound requests.
	 *
	 * @param string $raw_body Request body (empty for GET).
	 * @param string $method   HTTP method (unused here, kept for parity).
	 * @return array<string, string>
	 */
	private function build_hmac_headers( string $raw_body, string $method = 'POST' ): array {
		if ( empty( $this->hmac_secret ) ) {
			// F5.S3 — fail-closed: emit a sentinel header that callers can
			// detect, but NEVER the legacy `'dev-bypass'` literal. The backend
			// regex SIG_RE will reject this with `malformed_header`, ensuring
			// the request fails 401 instead of being mistakenly accepted by a
			// permissive verifier. We still return headers so wp_remote_get
			// runs and the calling code's existing non-200 path triggers.
			error_log( '[amcp.snapshot] fail-closed: enforcement_hmac_secret missing' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			if ( function_exists( 'update_option' ) && class_exists( 'Trusteed_Agent_Event_Webhook' ) ) {
				update_option( Trusteed_Agent_Event_Webhook::NOTICE_OPTION_HMAC_MISSING, 1, false );
			}
			return array(
				'X-Trusteed-Installation-Id' => $this->installation_id,
				'X-Trusteed-Signature'       => 'missing',
			);
		}

		$ts        = time();
		$message   = $ts . '.' . $raw_body;
		$hex       = hash_hmac( 'sha256', $message, $this->hmac_secret );
		$signature = 't=' . $ts . ',s=' . $hex;

		return array(
			'X-Trusteed-Installation-Id' => $this->installation_id,
			'X-Trusteed-Signature'       => $signature,
		);
	}

	/**
	 * Decode a base64url string (no padding required).
	 *
	 * @param string $input
	 * @return string|false
	 */
	private static function b64url_decode( string $input ) {
		$padded = strtr( $input, '-_', '+/' );
		$mod    = strlen( $padded ) % 4;
		if ( 0 !== $mod ) {
			$padded .= str_repeat( '=', 4 - $mod );
		}
		return base64_decode( $padded, true );
	}
}
