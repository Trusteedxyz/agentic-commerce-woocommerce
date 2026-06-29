<?php

declare(strict_types=1);

/**
 * TokenVerifierTest — PHPUnit 10 tests for Amcp_Token_Verifier.
 *
 * Tests use real Ed25519 cryptography via ext-sodium (sodium_crypto_sign_*).
 * No WordPress runtime is needed — Amcp_Token_Verifier is pure PHP.
 *
 * Three-state model verified:
 *   VALID        — signature correct, all claims pass
 *   INVALID      — signature wrong OR structural/claim failure
 *   INDETERMINATE— cannot verify (DID absent from resolver, sodium missing)
 *
 * Test inventory:
 *   TV-01  Valid token + correct signature → state=valid
 *   TV-02  Valid token + agentTrustScore populated → trust_score returned
 *   TV-03  Malformed JWS (2 parts only) → state=invalid, error=malformed_jws
 *   TV-04  Wrong alg (RS256) → state=invalid, error=wrong_alg
 *   TV-05  Wrong typ header → state=invalid, error=wrong_typ
 *   TV-06  Missing kid → state=invalid, error=missing_kid
 *   TV-07  iss ≠ kid-derived DID (key-confusion guard) → state=invalid, error=iss_kid_mismatch
 *   TV-08  Wrong audience (aud ≠ "trusteed") → state=invalid, error=wrong_aud
 *   TV-09  merchantId mismatch → state=invalid, error=merchant_id_mismatch
 *   TV-10  Expired token (exp beyond 30s grace) → state=invalid, error=expired
 *   TV-11  Token too old (iat window exceeded) → state=invalid, error=too_old
 *   TV-12  Signature tampered (payload modified after signing) → state=invalid, error=sig_invalid
 *   TV-13  DID not present in resolver → state=indeterminate, error=did_not_in_resolver
 *   TV-14  Empty resolver → state=indeterminate, error=did_not_in_resolver
 *   TV-15  Wrong JWK key type (rsa instead of OKP) → state=invalid, error=wrong_key_type
 *   TV-16  JWK with bad pubkey bytes (wrong length) → state=invalid, error=bad_pubkey
 *   TV-17  Token with DID fragment kid → DID extracted correctly, valid state
 *   TV-18  Token with merchantId absent → passes check (merchantId optional)
 *   TV-19  Token exactly at exp boundary (within 30s grace) → state=valid
 *   TV-20  Token at exact MAX_AGE_SECONDS for iat → state=valid (boundary)
 *
 * Run:
 *   cd packages/wp-plugin/agenticmcpstores-for-woocommerce
 *   composer install
 *   vendor/bin/phpunit tests/unit/TokenVerifierTest.php
 */

use PHPUnit\Framework\TestCase;

class TokenVerifierTest extends TestCase
{
    private const MERCHANT_ID = 'test-merchant-4fccd2f8';
    private const AGENT_DID   = 'did:web:agent.example.com';

    /** Ed25519 signing keypair generated once per test class. */
    private static string $pubkeyRaw;
    private static string $privkeyRaw;

    /** A second keypair for wrong-key tests. */
    private static string $otherPubkeyRaw;
    private static string $otherPrivkeyRaw;

    public static function setUpBeforeClass(): void
    {
        if (!extension_loaded('sodium')) {
            self::markTestSkipped('ext-sodium is required for token verification tests');
        }

        $kp = sodium_crypto_sign_keypair();
        self::$pubkeyRaw  = sodium_crypto_sign_publickey($kp);
        self::$privkeyRaw = sodium_crypto_sign_secretkey($kp);

        $kp2 = sodium_crypto_sign_keypair();
        self::$otherPubkeyRaw  = sodium_crypto_sign_publickey($kp2);
        self::$otherPrivkeyRaw = sodium_crypto_sign_secretkey($kp2);
    }

    // ── TV-01: Valid token ───────────────────────────────────────────────────

    /** Valid token with correct Ed25519 signature → state=valid. */
    public function testValidTokenReturnsValidState(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $jws      = $this->makeToken([]);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::VALID, $result->state, 'State must be valid');
        $this->assertSame(self::AGENT_DID, $result->agent_did);
        $this->assertFalse($result->is_invalid());
        $this->assertTrue($result->is_valid());
        $this->assertSame('', $result->error);
    }

    // ── TV-02: trust_score extracted ─────────────────────────────────────────

    /** agentTrustScore claim is returned in trust_score field. */
    public function testTrustScoreExtractedFromValidToken(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $jws      = $this->makeToken(['agentTrustScore' => 0.85]);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::VALID, $result->state);
        $this->assertEqualsWithDelta(0.85, $result->trust_score, 0.001);
    }

    // ── TV-03: malformed JWS ─────────────────────────────────────────────────

    /** JWS with only two segments is malformed → invalid. */
    public function testMalformedJwsReturnsInvalid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);

        $result = Amcp_Token_Verifier::verify('onlytwoparts.here', $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('malformed_jws', $result->error);
        $this->assertTrue($result->is_invalid());
    }

    // ── TV-04: wrong algorithm ───────────────────────────────────────────────

    /** alg=RS256 is not accepted — must be EdDSA. */
    public function testWrongAlgorithmReturnsInvalid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $jws      = $this->makeTokenWithHeader(['alg' => 'RS256', 'typ' => 'trusteed-agent-token+jwt', 'kid' => self::AGENT_DID . '#key-1'], []);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('wrong_alg', $result->error);
    }

    // ── TV-05: wrong typ ─────────────────────────────────────────────────────

    /** typ must be "trusteed-agent-token+jwt". */
    public function testWrongTypReturnsInvalid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $jws      = $this->makeTokenWithHeader(['alg' => 'EdDSA', 'typ' => 'JWT', 'kid' => self::AGENT_DID . '#key-1'], []);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('wrong_typ', $result->error);
    }

    // ── TV-06: missing kid ───────────────────────────────────────────────────

    /** Header without kid → cannot resolve DID → invalid. */
    public function testMissingKidReturnsInvalid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $jws      = $this->makeTokenWithHeader(['alg' => 'EdDSA', 'typ' => 'trusteed-agent-token+jwt'], []);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('missing_kid', $result->error);
    }

    // ── TV-07: iss ≠ kid DID (key-confusion guard) ───────────────────────────

    /**
     * The HIGH-4 key-confusion guard requires iss to match the DID derived from
     * the kid header. A token where they diverge must be rejected.
     */
    public function testIssKidMismatchReturnsInvalid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        // iss is a different DID from what kid implies.
        $jws = $this->makeToken(['iss' => 'did:web:attacker.example.com']);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('iss_kid_mismatch', $result->error);
    }

    // ── TV-08: wrong audience ────────────────────────────────────────────────

    /** aud must be "trusteed". */
    public function testWrongAudienceReturnsInvalid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $jws      = $this->makeToken(['aud' => 'shopify']);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('wrong_aud', $result->error);
    }

    // ── TV-09: merchantId mismatch ───────────────────────────────────────────

    /** merchantId claim present but pointing to a different merchant. */
    public function testMerchantIdMismatchReturnsInvalid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $jws      = $this->makeToken(['merchantId' => 'other-merchant-uuid']);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('merchant_id_mismatch', $result->error);
    }

    // ── TV-10: expired token ─────────────────────────────────────────────────

    /** Token expired more than 30 seconds ago → invalid. */
    public function testExpiredTokenReturnsInvalid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $jws      = $this->makeToken(['exp' => time() - 31, 'iat' => time() - 400]);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('expired', $result->error);
    }

    // ── TV-11: token too old (iat) ───────────────────────────────────────────

    /** iat older than MAX_AGE_SECONDS (120s) → invalid. */
    public function testTooOldTokenReturnsInvalid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        // exp still valid but iat too far back.
        $jws = $this->makeToken([
            'iat' => time() - 331,
            'exp' => time() + 300,
        ]);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('too_old', $result->error);
    }

    // ── TV-12: tampered payload ──────────────────────────────────────────────

    /**
     * A valid JWS whose payload segment is modified after signing.
     * The signature covers the original payload → verify must fail.
     */
    public function testTamperedPayloadReturnsInvalid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $original = $this->makeToken([]);

        // Replace the payload segment with a modified one.
        $parts = explode('.', $original);
        $evilPayload = $this->b64url(json_encode([
            'iss'        => self::AGENT_DID,
            'aud'        => 'trusteed',
            'merchantId' => self::MERCHANT_ID,
            'iat'        => time(),
            'exp'        => time() + 300,
            // extra claim injected by attacker
            'admin'      => true,
        ], JSON_THROW_ON_ERROR));
        $tampered = $parts[0] . '.' . $evilPayload . '.' . $parts[2];

        $result = Amcp_Token_Verifier::verify($tampered, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('sig_invalid', $result->error);
    }

    // ── TV-13: DID not in resolver ───────────────────────────────────────────

    /** DID in token is not present in the agentDidResolver → indeterminate (fail open). */
    public function testDIDNotInResolverReturnsIndeterminate(): void
    {
        // Resolver maps a DIFFERENT DID.
        $resolver = $this->buildResolver('did:web:other.example.com', self::$pubkeyRaw);
        $jws      = $this->makeToken([]);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INDETERMINATE, $result->state);
        $this->assertSame('did_not_in_resolver', $result->error);
        $this->assertFalse($result->is_invalid(), 'INDETERMINATE must not be treated as invalid');
        $this->assertFalse($result->is_valid());
    }

    // ── TV-14: empty resolver ────────────────────────────────────────────────

    /** Empty agentDidResolver → indeterminate (snapshot stub case). */
    public function testEmptyResolverReturnsIndeterminate(): void
    {
        $jws    = $this->makeToken([]);
        $result = Amcp_Token_Verifier::verify($jws, [], self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INDETERMINATE, $result->state);
        $this->assertSame('did_not_in_resolver', $result->error);
    }

    // ── TV-15: wrong JWK kty ─────────────────────────────────────────────────

    /** JWK with kty=RSA instead of OKP is rejected. */
    public function testWrongJwkKeyTypeReturnsInvalid(): void
    {
        $resolver = [[
            'did'          => self::AGENT_DID,
            'publicKeyJwk' => ['kty' => 'RSA', 'crv' => 'Ed25519', 'x' => 'AAAA'],
        ]];
        $jws = $this->makeToken([]);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('wrong_key_type', $result->error);
    }

    // ── TV-16: bad pubkey bytes ──────────────────────────────────────────────

    /** JWK x field decodes to wrong byte length (not 32 bytes) → invalid. */
    public function testBadPubkeyLengthReturnsInvalid(): void
    {
        // Encode only 16 bytes (half the required 32).
        $shortKey  = $this->b64url(str_repeat("\x00", 16));
        $resolver  = [[
            'did'          => self::AGENT_DID,
            'publicKeyJwk' => ['kty' => 'OKP', 'crv' => 'Ed25519', 'x' => $shortKey],
        ]];
        $jws = $this->makeToken([]);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('bad_pubkey', $result->error);
    }

    // ── TV-17: DID fragment in kid ───────────────────────────────────────────

    /**
     * kid = "did:web:agent.example.com#key-1" → DID "did:web:agent.example.com"
     * is extracted correctly and resolved from the map.
     */
    public function testKidWithFragmentExtractsDIDCorrectly(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        // makeToken() already uses kid = AGENT_DID + '#key-1' — confirmed valid.
        $jws = $this->makeToken([]);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::VALID, $result->state);
        $this->assertSame(self::AGENT_DID, $result->agent_did);
    }

    // ── TV-18: merchantId absent → passes ────────────────────────────────────

    /** Token without merchantId claim is accepted (optional binding). */
    public function testTokenWithoutMerchantIdClaimPasses(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $claims   = $this->baseClaims();
        unset($claims['merchantId']);
        $jws = $this->signToken($this->defaultHeader(), $claims);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::VALID, $result->state);
    }

    // ── TV-19: exp within 30s grace ─────────────────────────────────────────

    /** Token expired 29 seconds ago is still within the 30s clock-skew grace. */
    public function testTokenExpiredWithinGracePeriodIsValid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $jws      = $this->makeToken([
            'exp' => time() - 29, // 29s ago, within 30s grace
            'iat' => time() - 60,
        ]);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::VALID, $result->state);
    }

    // ── TV-20: iat at exact MAX_AGE_SECONDS boundary ─────────────────────────

    /** Token whose iat is exactly MAX_AGE_SECONDS old is at the boundary → valid. */
    public function testIatAtMaxAgeBoundaryIsValid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $jws      = $this->makeToken([
            'iat' => time() - 120, // exactly MAX_AGE_SECONDS
            'exp' => time() + 10,
        ]);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::VALID, $result->state);
    }

    // ── TV-21: wrong signing key ─────────────────────────────────────────────

    /**
     * Token signed with a different key but resolver has the correct public key.
     * Signature fails verification → invalid.
     */
    public function testTokenSignedWithWrongKeyReturnsInvalid(): void
    {
        // Resolver has the correct key; token is signed with the OTHER key.
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $jws      = $this->signToken($this->defaultHeader(), $this->baseClaims(), self::$otherPrivkeyRaw);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('sig_invalid', $result->error);
    }

    // ── TV-22: token_present() always true ───────────────────────────────────

    /** token_present() returns true regardless of state (token was supplied). */
    public function testTokenPresentAlwaysTrue(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);

        $valid      = Amcp_Token_Verifier::verify($this->makeToken([]), $resolver, self::MERCHANT_ID);
        $indeterminate = Amcp_Token_Verifier::verify($this->makeToken([]), [], self::MERCHANT_ID);
        $invalid    = Amcp_Token_Verifier::verify('bad.jws.here', $resolver, self::MERCHANT_ID);

        $this->assertTrue($valid->token_present());
        $this->assertTrue($indeterminate->token_present());
        $this->assertTrue($invalid->token_present());
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Build a standard agentDidResolver array entry for the given DID.
     *
     * @param  string $did       Agent DID.
     * @param  string $pubkeyRaw 32-byte raw Ed25519 public key.
     * @return array
     */
    private function buildResolver(string $did, string $pubkeyRaw): array
    {
        return [[
            'did'          => $did,
            'publicKeyJwk' => [
                'kty' => 'OKP',
                'crv' => 'Ed25519',
                'x'   => $this->b64url($pubkeyRaw),
            ],
        ]];
    }

    /**
     * Build a valid JWS using the default keypair and header, merging extra claims.
     *
     * @param  array $extraClaims Claims to override in the base claim set.
     * @return string JWS Compact Serialization.
     */
    private function makeToken(array $extraClaims = []): string
    {
        return $this->signToken(
            $this->defaultHeader(),
            array_merge($this->baseClaims(), $extraClaims)
        );
    }

    /**
     * Build a JWS with a custom header (useful for alg/typ/kid override tests).
     */
    private function makeTokenWithHeader(array $header, array $extraClaims): string
    {
        return $this->signToken($header, array_merge($this->baseClaims(), $extraClaims));
    }

    /**
     * Sign a header+payload pair with the given private key (defaults to main keypair).
     */
    private function signToken(array $header, array $claims, string $privkeyRaw = null): string
    {
        $privkeyRaw ??= self::$privkeyRaw;

        $h = $this->b64url((string)json_encode($header, JSON_THROW_ON_ERROR));
        $p = $this->b64url((string)json_encode($claims, JSON_THROW_ON_ERROR));

        $signingInput = $h . '.' . $p;
        $sig          = sodium_crypto_sign_detached($signingInput, $privkeyRaw);

        return $signingInput . '.' . $this->b64url($sig);
    }

    /** Default valid JWS header. */
    private function defaultHeader(): array
    {
        return [
            'alg' => 'EdDSA',
            'typ' => 'trusteed-agent-token+jwt',
            'kid' => self::AGENT_DID . '#key-1',
        ];
    }

    /** Base valid claims set. Override fields via makeToken($extras). */
    private function baseClaims(): array
    {
        return [
            'iss'        => self::AGENT_DID,
            'aud'        => 'trusteed',
            'merchantId' => self::MERCHANT_ID,
            'iat'        => time(),
            'exp'        => time() + 300,
            // Spec-048 P2.8 — JWS `jti` required for single-use replay protection.
            'jti'        => 'baseClaimsJtiTokenABCD0001',
        ];
    }

    /** base64url-encode raw bytes (no padding). */
    private function b64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
