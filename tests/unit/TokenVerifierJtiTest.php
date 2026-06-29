<?php

declare(strict_types=1);

/**
 * Spec-048 Sprint P2.8 — TokenVerifier jti-claim tests.
 *
 * Validates JWS `jti` extraction + format validation needed to support
 * single-use nonce enforcement via POST /v1/agent-events/nonce-consume.
 *
 * Cases:
 *   JTI-01 — jti present + well-formed → VALID, jti+exp populated.
 *   JTI-02 — jti claim absent → INVALID error=missing_jti.
 *   JTI-03 — jti claim malformed (contains `.`) → INVALID error=bad_jti.
 *   JTI-04 — jti at lower (16 chars) and upper (128 chars) length boundaries → VALID.
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

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::VALID, $result->state, 'jti present → VALID');
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

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('missing_jti', $result->error);
        $this->assertSame('', $result->jti);
    }

    // ── JTI-03 — jti malformed (illegal char) ────────────────────────────────

    public function testMalformedJtiReturnsInvalid(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);
        // `.` is outside the [A-Za-z0-9_-]{16,128} alphabet.
        $jws = $this->makeToken(['jti' => 'has.dot.in.it.illegal0001']);

        $result = Amcp_Token_Verifier::verify($jws, $resolver, self::MERCHANT_ID);

        $this->assertSame(Amcp_Token_State::INVALID, $result->state);
        $this->assertSame('bad_jti', $result->error);
    }

    // ── JTI-04 — boundary lengths (16 chars and 128 chars) ───────────────────

    public function testJtiBoundaryLengthsAccepted(): void
    {
        $resolver = $this->buildResolver(self::AGENT_DID, self::$pubkeyRaw);

        $jti16  = str_repeat('a', 16);                              // exactly 16 chars
        $jti128 = str_repeat('b', 128);                             // exactly 128 chars
        $jti15  = str_repeat('c', 15);                              // 15 — should reject
        $jti129 = str_repeat('d', 129);                             // 129 — should reject

        $r16 = Amcp_Token_Verifier::verify(
            $this->makeToken(['jti' => $jti16]),
            $resolver,
            self::MERCHANT_ID
        );
        $this->assertSame(Amcp_Token_State::VALID, $r16->state, '16-char jti accepted');
        $this->assertSame($jti16, $r16->jti);

        $r128 = Amcp_Token_Verifier::verify(
            $this->makeToken(['jti' => $jti128]),
            $resolver,
            self::MERCHANT_ID
        );
        $this->assertSame(Amcp_Token_State::VALID, $r128->state, '128-char jti accepted');
        $this->assertSame($jti128, $r128->jti);

        $r15 = Amcp_Token_Verifier::verify(
            $this->makeToken(['jti' => $jti15]),
            $resolver,
            self::MERCHANT_ID
        );
        $this->assertSame(Amcp_Token_State::INVALID, $r15->state, '15-char jti rejected');
        $this->assertSame('bad_jti', $r15->error);

        $r129 = Amcp_Token_Verifier::verify(
            $this->makeToken(['jti' => $jti129]),
            $resolver,
            self::MERCHANT_ID
        );
        $this->assertSame(Amcp_Token_State::INVALID, $r129->state, '129-char jti rejected');
        $this->assertSame('bad_jti', $r129->error);
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
