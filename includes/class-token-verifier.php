<?php
/**
 * Agent token JWS verifier — Ed25519 offline verification.
 *
 * Verifies a Trusteed agent token (JWS Compact Serialization with EdDSA/Ed25519)
 * against a public key resolved from the enforcement snapshot's agentDidResolver.
 *
 * Three-state result:
 *   VALID        — signature cryptographically correct
 *   INVALID      — signature wrong or claims rejected (set _agent_token_signature_invalid)
 *   INDETERMINATE — cannot verify (no sodium, DID not in resolver) — fail open
 *
 * Mirrors pattern in packages/prestashop-module-agenticmcpstores/src/Enforcement/TokenVerifier.php.
 *
 * @package AgenticMCPStores
 * @since   1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Verification state enum.
 */
class Amcp_Token_State {
	const VALID         = 'valid';
	const INVALID       = 'invalid';
	const INDETERMINATE = 'indeterminate';
}

/**
 * Result of agent token verification.
 *
 * @since 1.4.0
 */
class Amcp_Token_Verify_Result {

	/** @var string Amcp_Token_State::* constant. */
	public string $state;

	/** @var string Agent DID, or '' if absent. */
	public string $agent_did;

	/** @var float Trust score 0–1, or 0.0 if absent. */
	public float $trust_score;

	/** @var string Short error code, or '' on success. */
	public string $error;

	/** @var string Raw kid header value (e.g. "did:web:example.com#key-1"), or '' if absent. Used for R004 first-seen tracking. */
	public string $kid;

	/**
	 * Token `jti` claim — opaque single-use identifier used for replay
	 * protection (spec-048 P2.8). Empty string if absent or malformed.
	 *
	 * @var string
	 */
	public string $jti;

	/**
	 * Token `exp` claim (unix seconds). Zero if absent. Used to compute
	 * the nonce-store TTL so backend rows can be reclaimed safely.
	 *
	 * @var int
	 */
	public int $exp;

	public function __construct(
		string $state,
		string $agent_did = '',
		float $trust_score = 0.0,
		string $error = '',
		string $kid = '',
		string $jti = '',
		int $exp = 0
	) {
		$this->state       = $state;
		$this->agent_did   = $agent_did;
		$this->trust_score = $trust_score;
		$this->error       = $error;
		$this->kid         = $kid;
		$this->jti         = $jti;
		$this->exp         = $exp;
	}

	/** Whether the token was present (any state other than no-token). */
	public function token_present(): bool {
		return true;
	}

	/** Whether the signature is confirmed invalid (should set _agent_token_signature_invalid). */
	public function is_invalid(): bool {
		return Amcp_Token_State::INVALID === $this->state;
	}

	/** Whether the signature is confirmed valid. */
	public function is_valid(): bool {
		return Amcp_Token_State::VALID === $this->state;
	}
}

/**
 * Class Amcp_Token_Verifier
 *
 * @since 1.4.0
 */
class Amcp_Token_Verifier {

	/** Expected JWS typ header value. */
	private const EXPECTED_TYP = 'trusteed-agent-token+jwt';

	/** Expected audience claim. */
	private const EXPECTED_AUD = 'trusteed';

	/** Max allowed token age from iat to now (seconds). 30s grace on exp. */
	private const MAX_AGE_SECONDS = 120;

	/**
	 * `jti` format gate (spec-048 P2.8). Base64url-ish characters, 16–128 chars.
	 * Mirrors backend Zod regex in agent-events-nonce.routes.ts.
	 */
	private const JTI_RE = '/^[A-Za-z0-9_-]{16,128}$/';

	/**
	 * Verify a JWS Compact agent token.
	 *
	 * @param string $jws_compact  JWS Compact Serialization.
	 * @param array  $did_resolver Array of { did, publicKeyJwk: { kty, crv, x } }.
	 * @param string $merchant_id  Expected merchantId claim.
	 * @return Amcp_Token_Verify_Result
	 */
	public static function verify(
		string $jws_compact,
		array $did_resolver,
		string $merchant_id
	): Amcp_Token_Verify_Result {
		if ( ! function_exists( 'sodium_crypto_sign_verify_detached' ) ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INDETERMINATE, '', 0.0, 'sodium_unavailable' );
		}

		$parts = explode( '.', $jws_compact );
		if ( 3 !== count( $parts ) ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, '', 0.0, 'malformed_jws' );
		}

		[ $header_b64, $payload_b64, $sig_b64 ] = $parts;

		$header_json = self::b64url_decode( $header_b64 );
		if ( false === $header_json ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, '', 0.0, 'bad_header_encoding' );
		}
		$header = json_decode( $header_json, true );
		if ( ! is_array( $header ) ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, '', 0.0, 'bad_header_json' );
		}

		if ( 'EdDSA' !== ( $header['alg'] ?? '' ) ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, '', 0.0, 'wrong_alg' );
		}
		if ( self::EXPECTED_TYP !== ( $header['typ'] ?? '' ) ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, '', 0.0, 'wrong_typ' );
		}

		// Extract DID from kid: "did:web:example.com#key-1" → "did:web:example.com".
		$kid = (string) ( $header['kid'] ?? '' );
		$did = false !== strpos( $kid, '#' ) ? explode( '#', $kid )[0] : $kid;
		if ( '' === $did ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, '', 0.0, 'missing_kid' );
		}

		$payload_json = self::b64url_decode( $payload_b64 );
		if ( false === $payload_json ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, $did, 0.0, 'bad_payload_encoding' );
		}
		$payload = json_decode( $payload_json, true );
		if ( ! is_array( $payload ) ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, $did, 0.0, 'bad_payload_json' );
		}

		// Key-confusion guard (HIGH-4): iss must match kid-derived DID.
		if ( ( $payload['iss'] ?? '' ) !== $did ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, $did, 0.0, 'iss_kid_mismatch' );
		}

		if ( self::EXPECTED_AUD !== ( $payload['aud'] ?? '' ) ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, $did, 0.0, 'wrong_aud' );
		}

		if ( isset( $payload['merchantId'] ) && $payload['merchantId'] !== $merchant_id ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, $did, 0.0, 'merchant_id_mismatch' );
		}

		$now = time();
		$exp = isset( $payload['exp'] ) ? (int) $payload['exp'] : 0;
		if ( $exp > 0 && $now > $exp + 30 ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, $did, 0.0, 'expired' );
		}

		$iat = isset( $payload['iat'] ) ? (int) $payload['iat'] : 0;
		if ( $iat > 0 && ( $now - $iat ) > self::MAX_AGE_SECONDS ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, $did, 0.0, 'too_old' );
		}

		// Resolve public key from agentDidResolver (keyed by DID).
		$resolver_map = self::build_resolver_map( $did_resolver );
		$jwk          = $resolver_map[ $did ] ?? null;
		if ( null === $jwk ) {
			// DID not in resolver — we cannot verify (not necessarily spoofed). Fail open.
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INDETERMINATE, $did, 0.0, 'did_not_in_resolver', $kid );
		}

		if ( 'OKP' !== ( $jwk['kty'] ?? '' ) || 'Ed25519' !== ( $jwk['crv'] ?? '' ) ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, $did, 0.0, 'wrong_key_type' );
		}

		$pubkey_bytes = self::b64url_decode( $jwk['x'] ?? '' );
		if ( false === $pubkey_bytes || SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES !== strlen( $pubkey_bytes ) ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, $did, 0.0, 'bad_pubkey' );
		}

		$sig_bytes = self::b64url_decode( $sig_b64 );
		if ( false === $sig_bytes || SODIUM_CRYPTO_SIGN_BYTES !== strlen( $sig_bytes ) ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, $did, 0.0, 'bad_sig_encoding' );
		}

		$signing_input = $header_b64 . '.' . $payload_b64;

		try {
			$valid = sodium_crypto_sign_verify_detached( $sig_bytes, $signing_input, $pubkey_bytes );
		} catch ( \SodiumException $e ) {
			// sodium internal error — fail open (indeterminate).
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INDETERMINATE, $did, 0.0, 'sodium_exception', $kid );
		}

		if ( ! $valid ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, $did, 0.0, 'sig_invalid' );
		}

		// Spec-048 P2.8 — extract `jti` for replay protection. Missing or
		// malformed jti is treated as INVALID to prevent unbounded replay
		// via tokens minted without single-use identifiers.
		$jti_raw = isset( $payload['jti'] ) ? (string) $payload['jti'] : '';
		if ( '' === $jti_raw ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, $did, 0.0, 'missing_jti', $kid );
		}
		if ( ! preg_match( self::JTI_RE, $jti_raw ) ) {
			return new Amcp_Token_Verify_Result( Amcp_Token_State::INVALID, $did, 0.0, 'bad_jti', $kid );
		}

		$trust_score = isset( $payload['agentTrustScore'] ) ? (float) $payload['agentTrustScore'] : 0.0;
		return new Amcp_Token_Verify_Result(
			Amcp_Token_State::VALID,
			$did,
			$trust_score,
			'',
			$kid,
			$jti_raw,
			$exp
		);
	}

	/**
	 * Convert the agentDidResolver array (list of {did, publicKeyJwk}) to a map keyed by DID.
	 *
	 * @param array $did_resolver
	 * @return array<string, array> DID → publicKeyJwk
	 */
	private static function build_resolver_map( array $did_resolver ): array {
		$map = array();
		foreach ( $did_resolver as $entry ) {
			if ( isset( $entry['did'], $entry['publicKeyJwk'] ) && is_string( $entry['did'] ) ) {
				$map[ $entry['did'] ] = $entry['publicKeyJwk'];
			}
		}
		return $map;
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
