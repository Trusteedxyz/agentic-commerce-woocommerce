<?php

declare(strict_types=1);

/**
 * Spec-048 Sprint P2.8 + B5 (2026-10-01) — single-use identifier of the token.
 *
 * The CANONICAL claim is `nonce` (AgentTokenPayloadSchema; the server verifier
 * keys its anti-replay store on it and generate-test-agent-token.ts emits only
 * `nonce`). `jti` is a LEGACY ALIAS read only when `nonce` is absent. Both are
 * 16–64 base64url characters (the length of the canonical schema; the alphabet
 * of the server's nonce-consume endpoint). The `missing_jti` / `bad_jti` codes
 * are kept (the server knows the `_agent_token_jti_*` attributes) and now cover
 * the nonce.
 *
 * Before B5 this file fixed "jti mandatory": a canonical token (nonce, no jti)
 * was INVALID for the plugin and the jti range was 16–128.
 *
 * Cases:
 *   JTI-01 — jti alias present + well-formed (no nonce) → VALID, jti+exp populated.
 *   JTI-02 — neither nonce nor jti → INVALID error=missing_jti.
 *   JTI-03 — jti alias malformed (contains `.`) → INVALID error=bad_jti.
 *   JTI-04 — identifier at 16 / 64 chars → VALID; 15 / 65 chars → INVALID (nonce and jti alike).
 *   NON-01 — canonical token (nonce, NO jti) → VALID, identifier = nonce.
 *   NON-02 — nonce and jti both present and different → nonce is used.
 *   NON-03 — nonce present but malformed is not rescued by a valid jti.
 *   NON-04 — empty nonce falls back to the jti alias.
 */

use PHPUnit\Framework\TestCase;

class TokenVerifierJtiTest extends TestCase
{
    private const MERCHANT_ID = 'test-merchant-p28-12345';
    private const AGENT_DID   = 'did:web:agent.example.com';

    private static string $pubkeyRaw;
    private static string $privkeyRaw;

    public static function setUpBeforeClass(): void
    {
        if (!extension_loaded('sodium')) {
            self::markTestSkipped('ext-sodium is required for token verification tests');
        }
        $kp = sodium_crypto_sign_keypair();
        self::$pubkeyRaw  = sodium_crypto_sign_publickey($kp);
        self::$privkeyRaw = sodium_crypto_sign_secretkey($kp);
    }

    // ── JTI-01 — present + well-formed ───────────────────────────────────────

    public function testJtiPresentReturnsValidAndPopulatesFields(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $expAt    = time() + 300;
        $jws      = $this->makeToken(['jti' => 'wellFormedJti0123ABCD', 'exp' => $expAt]);

        $result = Trusteed_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Trusteed_Token_State::VALID, $result->state, 'jti alias (no nonce) → VALID');
        $this->assertSame('wellFormedJti0123ABCD', $result->jti);
        $this->assertSame($expAt, $result->exp);
        $this->assertSame('', $result->error);
    }

    // ── JTI-02 — jti absent ──────────────────────────────────────────────────

    public function testMissingJtiReturnsInvalid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);

        // Build claims manually without jti.
        $claims = [
            'iss'        => self::AGENT_DID,
            'aud'        => 'trusteed',
            'merchantId' => self::MERCHANT_ID,
            'iat'        => time(),
            'exp'        => time() + 300,
        ];
        $jws = $this->signToken($this->defaultHeader(), $claims);

        $result = Trusteed_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Trusteed_Token_State::INVALID, $result->state);
        $this->assertSame('missing_jti', $result->error);
        $this->assertSame('', $result->jti);
    }

    // ── JTI-03 — jti malformed (illegal char) ────────────────────────────────

    public function testMalformedJtiReturnsInvalid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        // `.` is outside the [A-Za-z0-9_-]{16,128} alphabet.
        $jws = $this->makeToken(['jti' => 'has.dot.in.it.illegal0001']);

        $result = Trusteed_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Trusteed_Token_State::INVALID, $result->state);
        $this->assertSame('bad_jti', $result->error);
    }

    // ── JTI-04 — boundary lengths (16 and 64 chars; 15 and 65 rejected) ──────

    public function testJtiAliasBoundaryLengths(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);

        foreach ([16, 64] as $len) {
            $id = str_repeat('a', $len);
            $r  = Trusteed_Token_Verifier::verify($this->makeToken(['jti' => $id]), $resolver, self::MERCHANT_ID);
            $this->assertSame(Trusteed_Token_State::VALID, $r->state, "{$len}-char jti accepted");
            $this->assertSame($id, $r->jti);
        }
        // 65 was accepted (the range was 16–128) before B5: the alias now has the nonce's limits.
        foreach ([15, 65, 128, 129] as $len) {
            $r = Trusteed_Token_Verifier::verify($this->makeToken(['jti' => str_repeat('c', $len)]), $resolver, self::MERCHANT_ID);
            $this->assertSame(Trusteed_Token_State::INVALID, $r->state, "{$len}-char jti rejected");
            $this->assertSame('bad_jti', $r->error);
        }
    }

    public function testNonceBoundaryLengths(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);

        foreach ([16, 64] as $len) {
            $id = str_repeat('n', $len);
            $r  = Trusteed_Token_Verifier::verify($this->makeCanonicalToken($id), $resolver, self::MERCHANT_ID);
            $this->assertSame(Trusteed_Token_State::VALID, $r->state, "{$len}-char nonce accepted");
            $this->assertSame($id, $r->jti);
        }
        foreach ([15, 65] as $len) {
            $r = Trusteed_Token_Verifier::verify($this->makeCanonicalToken(str_repeat('n', $len)), $resolver, self::MERCHANT_ID);
            $this->assertSame(Trusteed_Token_State::INVALID, $r->state, "{$len}-char nonce rejected");
            $this->assertSame('bad_jti', $r->error);
            $this->assertSame('', $r->jti);
        }
    }

    // ── NON-01 — the canonical token (exact generate-test-agent-token.ts shape) ─

    public function testCanonicalTokenWithNonceAndNoJtiIsValid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $nonce    = bin2hex(random_bytes(16)); // randomBytes(16).toString("hex") → 32 chars
        $jws      = $this->makeCanonicalToken($nonce);

        $result = Trusteed_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Trusteed_Token_State::VALID, $result->state, 'canonical token (nonce, no jti) is VALID');
        $this->assertSame('', $result->error);
        $this->assertSame($nonce, $result->jti, 'the single-use identifier is the nonce');
        $this->assertSame(self::AGENT_DID, $result->agent_did);
    }

    // ── NON-02 — both present and different → nonce wins ─────────────────────

    public function testNonceWinsOverDifferentJti(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $jws      = $this->makeToken(['nonce' => 'theCanonicalNonce0001', 'jti' => 'theLegacyAliasJti0002']);

        $result = Trusteed_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Trusteed_Token_State::VALID, $result->state);
        $this->assertSame('theCanonicalNonce0001', $result->jti);
    }

    // ── NON-03 — a malformed nonce is not rescued by a valid jti ─────────────

    public function testMalformedNonceIsNotRescuedByValidJti(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $jws      = $this->makeToken(['nonce' => 'short', 'jti' => 'aValidLegacyJti00001']);

        $result = Trusteed_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Trusteed_Token_State::INVALID, $result->state);
        $this->assertSame('bad_jti', $result->error);
    }

    // ── NON-04 — empty nonce falls back to the alias; non-string is malformed ─

    public function testEmptyNonceFallsBackToJtiAliasAndNonStringIsMalformed(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);

        $r = Trusteed_Token_Verifier::verify($this->makeToken(['nonce' => '', 'jti' => 'aValidLegacyJti00001']), $resolver, self::MERCHANT_ID);
        $this->assertSame(Trusteed_Token_State::VALID, $r->state);
        $this->assertSame('aValidLegacyJti00001', $r->jti);

        $r = Trusteed_Token_Verifier::verify($this->makeToken(['nonce' => ['x'], 'jti' => 'aValidLegacyJti00001']), $resolver, self::MERCHANT_ID);
        $this->assertSame(Trusteed_Token_State::INVALID, $r->state);
        $this->assertSame('bad_jti', $r->error);
    }

    // ── The identifier must come from the SIGNED payload ─────────────────────

    public function testIdentifierOfAnUnsignedTokenIsNeverReturned(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        $other    = sodium_crypto_sign_secretkey(sodium_crypto_sign_keypair());
        $jws      = $this->signToken($this->defaultHeader(), [
            'iss' => self::AGENT_DID, 'aud' => 'trusteed', 'merchantId' => self::MERCHANT_ID,
            'iat' => time(), 'exp' => time() + 300, 'nonce' => bin2hex(random_bytes(16)),
        ], $other);

        $result = Trusteed_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Trusteed_Token_State::INVALID, $result->state);
        $this->assertSame('sig_invalid', $result->error);
        $this->assertSame('', $result->jti, 'a token whose signature fails exposes no identifier to consume');
    }

    // ── Helpers (mirror TokenVerifierTest) ───────────────────────────────────

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

    private function makeToken(array $extraClaims): string
    {
        $base = [
            'iss'        => self::AGENT_DID,
            'aud'        => 'trusteed',
            'merchantId' => self::MERCHANT_ID,
            'iat'        => time(),
            'exp'        => time() + 300,
            'jti'        => 'defaultJtiTokenABCD0001',
        ];
        return $this->signToken($this->defaultHeader(), array_merge($base, $extraClaims));
    }

    /** Exact payload of apps/api/scripts/generate-test-agent-token.ts: `nonce`, NO `jti`. */
    private function makeCanonicalToken(string $nonce): string
    {
        $now = time();
        return $this->signToken($this->defaultHeader(), [
            'iss'                => self::AGENT_DID,
            'aud'                => 'trusteed',
            'merchantId'         => self::MERCHANT_ID,
            'iat'                => $now,
            'exp'                => $now + 300,
            'nonce'              => $nonce,
            'checkoutIntentHash' => 'sha256:' . str_repeat('0', 64),
        ]);
    }

    private function signToken(array $header, array $claims, ?string $privkeyRaw = null): string
    {
        $privkeyRaw ??= self::$privkeyRaw;
        $h = $this->b64url((string) json_encode($header, JSON_THROW_ON_ERROR));
        $p = $this->b64url((string) json_encode($claims, JSON_THROW_ON_ERROR));
        $signingInput = $h . '.' . $p;
        $sig = sodium_crypto_sign_detached($signingInput, $privkeyRaw);
        return $signingInput . '.' . $this->b64url($sig);
    }

    private function defaultHeader(): array
    {
        return [
            'alg' => 'EdDSA',
            'typ' => 'trusteed-agent-token+jwt',
            'kid' => self::AGENT_DID . '#key-1',
        ];
    }

    private function b64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
