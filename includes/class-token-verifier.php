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
 * @package Trusteed
 * @since   1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Verification state enum.
 */
class Trusteed_Token_State {
	const VALID         = 'valid';
	const INVALID       = 'invalid';
	const INDETERMINATE = 'indeterminate';
}

/**
 * Result of agent token verification.
 *
 * @since 1.4.0
 */
class Trusteed_Token_Verify_Result {

	/** @var string Trusteed_Token_State::* constant. */
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
	 * Single-use identifier of the token used for replay protection (spec-048
	 * P2.8): the canonical `nonce` claim, or the legacy `jti` alias when
	 * `nonce` is absent (see Trusteed_Token_Verifier::extract_replay_identifier).
	 * The property keeps its historical name `jti`. Empty string if absent or
	 * malformed.
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
		return Trusteed_Token_State::INVALID === $this->state;
	}

	/** Whether the signature is confirmed valid. */
	public function is_valid(): bool {
		return Trusteed_Token_State::VALID === $this->state;
	}
}

/**
 * Class Trusteed_Token_Verifier
 *
 * @since 1.4.0
 */
class Trusteed_Token_Verifier {

	/** Expected JWS typ header value. */
	private const EXPECTED_TYP = 'trusteed-agent-token+jwt';

	/** Expected audience claim. */
	private const EXPECTED_AUD = 'trusteed';

	/**
	 * Max allowed token age from iat to now (seconds). 30s grace on exp.
	 *
	 * Decision 48 (2026-10-02): the SAME number in every verifier — the 300 s
	 * the contract declares (`AgentTokenPayloadSchema`: `exp - iat <= 300`).
	 * Agents mint their own tokens with up to 300 s of life, so a lower cap
	 * (this one was 120) rejects legitimate tokens that are still valid for
	 * the server and for the Shopify Function. Tied across languages by
	 * packages/shared/src/enforcement/__tests__/agent-token-max-age-parity.test.ts.
	 */
	private const MAX_AGE_SECONDS = 300;

	/**
	 * Format gate of the single-use identifier (spec-048 P2.8): base64url
	 * characters, 16–64 chars. 16–64 is the length the canonical token schema
	 * (`AgentTokenPayloadSchema.nonce`) allows; the alphabet is the one the
	 * server's nonce-consume endpoint (agent-events-nonce.routes.ts) accepts,
	 * so a token this gate lets through can always be consumed.
	 */
	private const REPLAY_ID_RE = '/^[A-Za-z0-9_-]{16,64}$/';

	/**
	 * Verify a JWS Compact agent token.
	 *
	 * @param string $jws_compact  JWS Compact Serialization.
	 * @param array  $did_resolver Array of { did, publicKeyJwk: { kty, crv, x } }.
	 * @param string $merchant_id  Expected merchantId claim.
	 * @return Trusteed_Token_Verify_Result
	 */
	public static function verify(
		string $jws_compact,
		array $did_resolver,
		string $merchant_id
	): Trusteed_Token_Verify_Result {
		if ( ! function_exists( 'sodium_crypto_sign_verify_detached' ) ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INDETERMINATE, '', 0.0, 'sodium_unavailable' );
		}

		$parts = explode( '.', $jws_compact );
		if ( 3 !== count( $parts ) ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, '', 0.0, 'malformed_jws' );
		}

		[ $header_b64, $payload_b64, $sig_b64 ] = $parts;

		$header_json = self::b64url_decode( $header_b64 );
		if ( false === $header_json ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, '', 0.0, 'bad_header_encoding' );
		}
		$header = json_decode( $header_json, true );
		if ( ! is_array( $header ) ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, '', 0.0, 'bad_header_json' );
		}

		if ( 'EdDSA' !== ( $header['alg'] ?? '' ) ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, '', 0.0, 'wrong_alg' );
		}
		if ( self::EXPECTED_TYP !== ( $header['typ'] ?? '' ) ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, '', 0.0, 'wrong_typ' );
		}
		if ( array_key_exists( 'crit', $header ) ) {
			// RFC 7515 §4.1.11: critical extensions we do not understand.
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, '', 0.0, 'crit_not_supported' );
		}

		// Extract DID from kid: "did:web:example.com#key-1" → "did:web:example.com".
		$kid = (string) ( $header['kid'] ?? '' );
		$did = false !== strpos( $kid, '#' ) ? explode( '#', $kid )[0] : $kid;
		if ( '' === $did ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, '', 0.0, 'missing_kid' );
		}

		$payload_json = self::b64url_decode( $payload_b64 );
		if ( false === $payload_json ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, 'bad_payload_encoding' );
		}
		$payload = json_decode( $payload_json, true );
		if ( ! is_array( $payload ) ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, 'bad_payload_json' );
		}

		// Key-confusion guard (HIGH-4): iss must match kid-derived DID.
		if ( ( $payload['iss'] ?? '' ) !== $did ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, 'iss_kid_mismatch' );
		}

		if ( self::EXPECTED_AUD !== ( $payload['aud'] ?? '' ) ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, 'wrong_aud' );
		}

		if ( isset( $payload['merchantId'] ) && $payload['merchantId'] !== $merchant_id ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, 'merchant_id_mismatch' );
		}

		$now = time();

		// H4 (2026-07-30) — `exp` and `iat` are MANDATORY. The canonical token
		// schema requires both as positive integers, but both guards below used
		// to hang off `> 0`, so a token that simply OMITTED the claim skipped
		// the check entirely: without `exp` it never expired, without `iat` it
		// had no maximum age. Rejecting is fail-closed and matches the
		// PrestaShop, Magento and Odoo verifiers.
		if ( ! isset( $payload['exp'] ) || ! is_numeric( $payload['exp'] ) ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, 'missing_exp' );
		}
		if ( ! isset( $payload['iat'] ) || ! is_numeric( $payload['iat'] ) ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, 'missing_iat' );
		}

		$exp = (int) $payload['exp'];
		if ( $now > $exp + 30 ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, 'expired' );
		}

		$iat = (int) $payload['iat'];

		// A future `iat` combined with the max-age window below gives a sliding
		// lifetime: `now - iat` stays small for as long as the issuer pushed the
		// claim forward, so the token effectively never grows old. 30s of clock
		// skew is tolerated, the same grace used on `exp`.
		if ( ( $iat - 30 ) > $now ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, 'iat_in_future' );
		}

		if ( ( $now - $iat ) > self::MAX_AGE_SECONDS ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, 'too_old' );
		}

		// Resolve public key from agentDidResolver (the list the server emits; the
		// legacy DID-keyed map is accepted too — 2.3.6, Task 23a).
		$jwk = self::resolve_agent_did_public_key( $did_resolver, $did );
		if ( null === $jwk ) {
			// DID not in resolver — we cannot verify (not necessarily spoofed). Fail open.
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INDETERMINATE, $did, 0.0, 'did_not_in_resolver', $kid );
		}

		if ( 'OKP' !== ( $jwk['kty'] ?? '' ) || 'Ed25519' !== ( $jwk['crv'] ?? '' ) ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, 'wrong_key_type' );
		}

		$pubkey_bytes = self::b64url_decode( $jwk['x'] ?? '' );
		if ( false === $pubkey_bytes || SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES !== strlen( $pubkey_bytes ) ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, 'bad_pubkey' );
		}

		$sig_bytes = self::b64url_decode( $sig_b64 );
		if ( false === $sig_bytes || SODIUM_CRYPTO_SIGN_BYTES !== strlen( $sig_bytes ) ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, 'bad_sig_encoding' );
		}

		$signing_input = $header_b64 . '.' . $payload_b64;

		try {
			$valid = sodium_crypto_sign_verify_detached( $sig_bytes, $signing_input, $pubkey_bytes );
		} catch ( \SodiumException $e ) {
			// sodium internal error — fail open (indeterminate).
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INDETERMINATE, $did, 0.0, 'sodium_exception', $kid );
		}

		if ( ! $valid ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, 'sig_invalid' );
		}

		// Spec-048 P2.8 — single-use identifier for replay protection (B5,
		// 2026-10-01). Read from the SIGNED payload (the signature was verified
		// above): the canonical claim is `nonce`; `jti` is only a legacy alias.
		// Missing or malformed is INVALID to prevent unbounded replay via tokens
		// minted without a single-use identifier. The `missing_jti` / `bad_jti`
		// codes (and the `_agent_token_jti_*` attributes the server knows) are
		// kept for compatibility: they now cover the nonce.
		list( $jti_raw, $replay_error ) = self::extract_replay_identifier( $payload );
		if ( '' !== $replay_error ) {
			return new Trusteed_Token_Verify_Result( Trusteed_Token_State::INVALID, $did, 0.0, $replay_error, $kid );
		}

		$trust_score = isset( $payload['agentTrustScore'] ) ? (float) $payload['agentTrustScore'] : 0.0;
		return new Trusteed_Token_Verify_Result(
			Trusteed_Token_State::VALID,
			$did,
			$trust_score,
			'',
			$kid,
			$jti_raw,
			$exp
		);
	}

	/**
	 * Single-use identifier of a token payload (B5). The canonical contract is
	 * `nonce` (AgentTokenPayloadSchema; the server verifier keys its anti-replay
	 * store on it). `jti` is read ONLY as a legacy alias when `nonce` is absent
	 * or empty; if both are present and differ, `nonce` wins. A `nonce` that is
	 * present but malformed is NOT rescued by a valid `jti`.
	 *
	 * @param array $payload Decoded, signature-verified payload.
	 * @return array{0: string, 1: string} [ identifier, error ]: error is '' | 'missing_jti' | 'bad_jti'.
	 */
	public static function extract_replay_identifier( array $payload ): array {
		$raw = $payload['nonce'] ?? null;
		if ( null === $raw || '' === $raw ) {
			$raw = $payload['jti'] ?? null;
		}
		if ( null === $raw || '' === $raw ) {
			return array( '', 'missing_jti' );
		}
		if ( ! is_string( $raw ) || 1 !== preg_match( self::REPLAY_ID_RE, $raw ) ) {
			return array( '', 'bad_jti' );
		}
		return array( $raw, '' );
	}

	/**
	 * Port of `resolveAgentDidPublicKey` (packages/shared/src/enforcement/platform-flow.ts,
	 * vectors `snapshotResolverShape` of the shared fixture): the public key of
	 * `$did` in the snapshot `agentDidResolver`.
	 *
	 * - List (what the server emits): the LAST well-formed entry whose `did`
	 *   matches exactly (as before). A later malformed entry no longer
	 *   overrides a good one (before 2.3.6 it did, and the token went INVALID).
	 * - Legacy map `{did: jwk}`: the exact key only.
	 * - Malformed entries (not an object, `did` not a string, `publicKeyJwk`
	 *   not an object, `x` missing/empty) are ignored without throwing.
	 *
	 * Only resolves: `kty`/`crv` and the signature are checked by verify().
	 *
	 * @param mixed  $resolver `agentDidResolver` as received.
	 * @param string $did      DID to resolve.
	 * @return array{x:string,kty?:string,crv?:string,kid?:string}|null
	 */
	public static function resolve_agent_did_public_key( $resolver, string $did ): ?array {
		if ( '' === $did || ! is_array( $resolver ) ) {
			return null;
		}
		if ( array() !== $resolver && array_is_list( $resolver ) ) {
			$found = null;
			foreach ( $resolver as $entry ) {
				if ( ! self::is_json_object( $entry ) || ! isset( $entry['did'] ) || $entry['did'] !== $did ) {
					continue;
				}
				$found = self::to_public_key( $entry['publicKeyJwk'] ?? null ) ?? $found;
			}
			return $found;
		}
		return array_key_exists( $did, $resolver ) ? self::to_public_key( $resolver[ $did ] ) : null;
	}

	/**
	 * A decoded JSON object: an associative (or empty) array, never a list.
	 *
	 * @param mixed $value Decoded value.
	 * @return bool
	 */
	private static function is_json_object( $value ): bool {
		return is_array( $value ) && ( array() === $value || ! array_is_list( $value ) );
	}

	/**
	 * @param mixed $jwk JWK as received.
	 * @return array{x:string,kty?:string,crv?:string,kid?:string}|null
	 */
	private static function to_public_key( $jwk ): ?array {
		if ( ! self::is_json_object( $jwk ) || ! isset( $jwk['x'] ) || ! is_string( $jwk['x'] ) || '' === $jwk['x'] ) {
			return null;
		}
		$key = array( 'x' => $jwk['x'] );
		foreach ( array( 'kty', 'crv', 'kid' ) as $field ) {
			if ( isset( $jwk[ $field ] ) && is_string( $jwk[ $field ] ) ) {
				$key[ $field ] = $jwk[ $field ];
			}
		}
		return $key;
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
