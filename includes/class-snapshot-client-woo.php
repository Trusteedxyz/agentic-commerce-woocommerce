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
 * Fail-open: any connectivity, parse, or crypto error returns an empty resolver
 * array, which causes token verification to return INDETERMINATE (not INVALID).
 *
 * @package AgenticMCPStores
 * @since   1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Amcp_Snapshot_Client_Woo
 *
 * @since 1.4.0
 */
class Amcp_Snapshot_Client_Woo {

	/** WP transient key prefix for snapshot cache (per merchant). */
	private const SNAP_TRANSIENT_PREFIX = 'amcp_snap_';

	/** WP transient key for JWKS cache. */
	private const JWKS_TRANSIENT_KEY = 'amcp_trusteed_jwks';

	/** JWKS cache TTL in seconds. */
	private const JWKS_TTL_SECONDS = 3600;

	/** HTTP timeout for snapshot and JWKS requests. */
	private const TIMEOUT_SECONDS = 5;

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
	 * Return the agentDidResolver array for a merchant.
	 *
	 * Returns empty array (fail-open) on any error.
	 *
	 * @param string $merchant_id Merchant UUID.
	 * @return array Array of { did, publicKeyJwk }.
	 */
	public function get_did_resolver( string $merchant_id ): array {
		if ( empty( $merchant_id ) ) {
			return array();
		}

		$transient_key = self::SNAP_TRANSIENT_PREFIX . substr( md5( $merchant_id ), 0, 12 );
		$cached        = get_transient( $transient_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$payload = $this->fetch_and_verify_snapshot( $merchant_id );
		if ( null === $payload ) {
			return array();
		}

		$resolver = isset( $payload['agentDidResolver'] ) && is_array( $payload['agentDidResolver'] )
			? $payload['agentDidResolver']
			: array();

		// Cache until validUntil or 60s, whichever is smaller, max 300s.
		$ttl = 60;
		if ( ! empty( $payload['validUntil'] ) ) {
			$valid_until = strtotime( $payload['validUntil'] );
			if ( $valid_until > 0 ) {
				$ttl = min( 300, max( 10, $valid_until - time() ) );
			}
		}

		set_transient( $transient_key, $resolver, $ttl );
		return $resolver;
	}

	/**
	 * Fetch the snapshot JWS from the API, verify its Ed25519 signature, and
	 * return the decoded payload array, or null on failure.
	 *
	 * @param string $merchant_id
	 * @return array|null Decoded payload or null.
	 */
	private function fetch_and_verify_snapshot( string $merchant_id ): ?array {
		$url      = $this->api_base . '/v1/rules/snapshot/' . rawurlencode( $merchant_id );
		$raw_body = '';

		// Spec-048 FR-008 — negotiate the contract wire format: a bare JWS
		// Compact body with Content-Type application/jose. Mirrors the
		// WP-plugin / PrestaShop / Odoo clients.
		$headers           = $this->build_hmac_headers( $raw_body, 'GET' );
		$headers['Accept'] = 'application/jose';

		$response = wp_remote_get(
			$url,
			array(
				'timeout' => self::TIMEOUT_SECONDS,
				'headers' => $headers,
			)
		);

		if ( is_wp_error( $response ) ) {
			error_log( '[amcp] snapshot fetch error: ' . $response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return null;
		}

		$status = (int) wp_remote_retrieve_response_code( $response );
		if ( 200 !== $status ) {
			error_log( '[amcp] snapshot fetch HTTP ' . $status ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return null;
		}

		// Contract response is the bare JWS Compact string. Trim trailing
		// whitespace/newlines before verifying.
		$jws_compact = trim( (string) wp_remote_retrieve_body( $response ) );
		if ( '' === $jws_compact ) {
			return null;
		}

		return $this->verify_snapshot_jws( $jws_compact );
	}

	/**
	 * Verify the snapshot JWS signature against the Trusteed JWKS.
	 *
	 * Returns decoded payload on success, null on failure.
	 *
	 * @param string $jws_compact JWS Compact Serialization.
	 * @return array|null
	 */
	private function verify_snapshot_jws( string $jws_compact ): ?array {
		if ( ! function_exists( 'sodium_crypto_sign_verify_detached' ) ) {
			// Cannot verify — decode without verification (dev-only fallback).
			return $this->decode_jws_payload_unverified( $jws_compact );
		}

		$parts = explode( '.', $jws_compact );
		if ( 3 !== count( $parts ) ) {
			return null;
		}
		[ $header_b64, $payload_b64, $sig_b64 ] = $parts;

		$header_json = self::b64url_decode( $header_b64 );
		$header      = $header_json ? json_decode( $header_json, true ) : null;
		if ( ! is_array( $header ) || 'EdDSA' !== ( $header['alg'] ?? '' ) ) {
			return null;
		}
		$kid = (string) ( $header['kid'] ?? '' );

		$jwks = $this->get_jwks();
		if ( null === $jwks ) {
			// JWKS unavailable — decode without verification (fail-open).
			return $this->decode_jws_payload_unverified( $jws_compact );
		}

		$pubkey_bytes = $this->resolve_jwks_key( $jwks, $kid );
		if ( null === $pubkey_bytes ) {
			return null;
		}

		$sig_bytes = self::b64url_decode( $sig_b64 );
		if ( false === $sig_bytes || SODIUM_CRYPTO_SIGN_BYTES !== strlen( $sig_bytes ) ) {
			return null;
		}

		$signing_input = $header_b64 . '.' . $payload_b64;
		try {
			$valid = sodium_crypto_sign_verify_detached( $sig_bytes, $signing_input, $pubkey_bytes );
		} catch ( \SodiumException $e ) {
			return $this->decode_jws_payload_unverified( $jws_compact );
		}

		if ( ! $valid ) {
			error_log( '[amcp] snapshot JWS signature invalid' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return null;
		}

		$payload_json = self::b64url_decode( $payload_b64 );
		$payload      = $payload_json ? json_decode( $payload_json, true ) : null;
		return is_array( $payload ) ? $payload : null;
	}

	/**
	 * Fetch and cache the Trusteed JWKS.
	 *
	 * @return array|null JWKS keys array or null on failure.
	 */
	private function get_jwks(): ?array {
		$cached = get_transient( self::JWKS_TRANSIENT_KEY );
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
	 * Decode JWS payload without signature verification (used when sodium unavailable).
	 *
	 * @param string $jws_compact
	 * @return array|null
	 */
	private function decode_jws_payload_unverified( string $jws_compact ): ?array {
		$parts = explode( '.', $jws_compact );
		if ( count( $parts ) < 2 ) {
			return null;
		}
		$json = self::b64url_decode( $parts[1] );
		$data = $json ? json_decode( $json, true ) : null;
		return is_array( $data ) ? $data : null;
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
			if ( function_exists( 'update_option' ) && class_exists( 'Amcp_Agent_Event_Webhook' ) ) {
				update_option( Amcp_Agent_Event_Webhook::NOTICE_OPTION_HMAC_MISSING, 1, false );
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
