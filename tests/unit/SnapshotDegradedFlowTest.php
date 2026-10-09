<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/class-cart-bridge.php';
require_once __DIR__ . '/../../includes/class-cart-signals.php';
require_once __DIR__ . '/../../includes/class-enforcement-api-client.php';
require_once __DIR__ . '/../../includes/class-offline-safety-valve-evaluator.php';
require_once __DIR__ . '/../../includes/class-agent-event-webhook.php';
require_once __DIR__ . '/../../includes/class-classic-meta-persister.php';
require_once __DIR__ . '/../../includes/class-checkout-enforcer.php';

if (!class_exists('Trusteed_Test_Wc_Session')) {
    final class Trusteed_Test_Wc_Session
    {
        public array $data = [];
        public function get(string $key) { return $this->data[$key] ?? null; }
        public function set(string $key, $value): void { $this->data[$key] = $value; }
        public function save_data(): void {}
    }
}

/**
 * Task 23a (plan 04) — WC principal: el cliente de snapshot deja de ser
 * fail-open y distingue la CAUSA (`tampered` / `unavailable`), y el enforcer
 * decide con `decide_degraded()` (port de `decideOnDegradedEnforcement`).
 *
 * Conductual y con el cliente de snapshot REAL: sirve por el stub HTTP el
 * `snapshotPayload` del fixture compartido (resolver en LISTA) firmado de
 * verdad (Ed25519 + JWKS), con la clave del agente de prueba al final de la
 * lista, y un token Ed25519 firmado con esa clave. Sólo el cliente de la API
 * de reglas es un doble (registra lo que se le pide; la respuesta la fija el
 * test).
 */
final class SnapshotDegradedFlowTest extends TestCase
{
    private const API      = 'https://api.trusteed.test';
    private const KID      = 'snap-kid-23a';
    private const AGENT    = 'did:web:agent-23a.example.com';
    private const INST     = 'inst-23a';
    private const SECRET   = 'example-hmac-secret-not-real-23a';

    private string $platformPriv = '';
    private string $platformPub  = '';
    private string $agentPriv    = '';
    private string $agentPub     = '';
    private string $merchant     = '';
    private int $jtiSeq          = 0;
    private Trusteed_Test_Wc_Session $session;

    protected function setUp(): void
    {
        $p = sodium_crypto_sign_keypair();
        $this->platformPriv = sodium_crypto_sign_secretkey($p);
        $this->platformPub  = sodium_crypto_sign_publickey($p);
        $a = sodium_crypto_sign_keypair();
        $this->agentPriv = sodium_crypto_sign_secretkey($a);
        $this->agentPub  = sodium_crypto_sign_publickey($a);
        $this->merchant  = (string) self::fixturePayload()['merchantId'];

        $GLOBALS['__amcp_transients_store']     = [];
        $GLOBALS['__amcp_remote_get_responses'] = [];
        $GLOBALS['__amcp_remote_get_calls']     = [];
        $this->session = new Trusteed_Test_Wc_Session();
        $GLOBALS['trusteed_test_wc'] = (object) ['session' => $this->session, 'cart' => null];
        $this->publishJwks([self::KID => $this->platformPub]);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['trusteed_test_wc']);
        $GLOBALS['__amcp_transients_store']     = [];
        $GLOBALS['__amcp_remote_get_responses'] = [];
        $GLOBALS['__amcp_remote_get_calls']     = [];
    }

    // ── invalid_signature ───────────────────────────────────────────────────

    /** @return array<string,array{0:string}> */
    public static function modes(): array
    {
        return ['enforce' => ['enforce'], 'observe' => ['observe']];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modes')]
    public function test_tampered_snapshot_with_a_token_holds_the_agent_in_every_mode(string $mode): void
    {
        $this->serveSnapshot($this->tamperedJws());
        $this->agentWithToken($this->validToken());
        $client = self::client();

        self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $this->run_eval($client, $mode));
        self::assertSame([], $client->payloads, 'con el snapshot manipulado no se pregunta al servidor');
        self::assertSame([], $client->nonces, 'ni se gasta el jti');
        self::assertNull($this->session->get(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN), 'la sesión se limpia igual');
    }

    public function test_tampered_snapshot_without_token_leaves_the_decision_to_the_server(): void
    {
        $this->serveSnapshot($this->tamperedJws());
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::AGENT);
        $client = self::client();

        self::assertSame('ALLOW', $this->run_eval($client, 'enforce'), 'sin token no se consulta el snapshot: decide el servidor');
        self::assertCount(1, $client->payloads);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modes')]
    public function test_tampered_snapshot_and_server_down_hold_a_tokenless_agent_in_every_mode(string $mode): void
    {
        $this->serveSnapshot($this->tamperedJws());
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::AGENT);

        self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $this->run_eval(self::client(self::indeterminate()), $mode));
    }

    public function test_tampered_snapshot_never_blocks_a_person_even_with_a_cap_rule_in_it(): void
    {
        $this->serveSnapshot($this->signedSnapshot(self::fixturePayload(['rules' => [self::capRule()]]), sodium_crypto_sign_secretkey(sodium_crypto_sign_keypair())));

        self::assertSame('ALLOW', $this->run_eval(self::client(self::indeterminate()), 'enforce', ['cartTotalCents' => 999999, 'currency' => 'EUR', 'itemCount' => 1]), 'las reglas de un snapshot manipulado no son del comerciante');
    }

    public function test_a_key_rotation_within_the_jwks_hour_is_not_tampering(): void
    {
        $rotated = sodium_crypto_sign_keypair();
        $GLOBALS['__amcp_transients_store']['trusteed_jwks'] = [self::jwk(self::KID, $this->platformPub)];
        $this->publishJwks(['kid-rotated' => sodium_crypto_sign_publickey($rotated)]);
        $this->serveSnapshot($this->signedSnapshot(self::fixturePayload(), sodium_crypto_sign_secretkey($rotated), 'kid-rotated'));
        $this->agentWithToken($this->validToken());
        $client = self::client();

        self::assertSame('ALLOW', $this->run_eval($client, 'enforce'));
        self::assertSame(self::AGENT, $client->payloads[0]['agentId']);
    }

    // ── missing_snapshot ────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\DataProvider('modes')]
    public function test_outage_with_a_token_holds_the_agent_in_every_mode(string $mode): void
    {
        $this->failSnapshot();
        $this->agentWithToken($this->validToken());
        $client = self::client();

        self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $this->run_eval($client, $mode));
        self::assertSame([], $client->payloads);
    }

    public function test_outage_without_token_follows_the_mode_when_the_server_is_down_too(): void
    {
        $this->failSnapshot();
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::AGENT);
        self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $this->run_eval(self::client(self::indeterminate()), 'enforce'));

        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::AGENT);
        self::assertSame('ALLOW', $this->run_eval(self::client(self::indeterminate()), 'observe'));
    }

    /**
     * Task 38c: a snapshot signed by our server always carries `validUntil`;
     * one that arrives without it (signature valid) is malformed and is held
     * exactly like an outage (`missing_snapshot`), never served and never
     * read as manipulation. The server is NOT asked and the jti is not spent.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('modes')]
    public function test_a_signed_snapshot_without_valid_until_holds_a_token_agent_like_an_outage(string $mode): void
    {
        $payload = $this->withAgentKey(self::fixturePayload());
        unset($payload['validUntil']);
        $this->serveSnapshot($this->signedSnapshot($payload));
        $this->agentWithToken($this->validToken());
        $client = self::client();

        self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $this->run_eval($client, $mode));
        self::assertSame([], $client->payloads);
        self::assertSame([], $client->nonces);
    }

    public function test_the_same_snapshot_with_valid_until_serves_the_token_agent(): void
    {
        // Control of the test above: only `validUntil` differs.
        $this->serveSnapshot($this->signedSnapshot($this->withAgentKey(self::fixturePayload())));
        $this->agentWithToken($this->validToken());
        $client = self::client();

        self::assertSame('ALLOW', $this->run_eval($client, 'enforce'));
        self::assertSame(self::AGENT, $client->payloads[0]['agentId']);
    }

    public function test_a_person_is_never_held_by_a_missing_snapshot(): void
    {
        $this->failSnapshot();
        self::assertSame('ALLOW', $this->run_eval(self::client(self::indeterminate()), 'enforce'));
    }

    /** Antes de 2.3.6 un JWKS inalcanzable decodificaba el payload SIN verificar. */
    public function test_jwks_unreachable_is_an_outage_and_the_unverified_resolver_is_never_used(): void
    {
        unset($GLOBALS['__amcp_remote_get_responses'][self::API . '/.well-known/jwks.json']);
        $this->serveSnapshot($this->signedSnapshot(self::fixturePayload()));
        $this->agentWithToken($this->validToken());
        $client = self::client();

        self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $this->run_eval($client, 'enforce'));
        self::assertSame([], $client->payloads);
        self::assertSame([], $client->nonces);
    }

    // ── evaluator_unavailable: snapshot verificado (caché) + capa 2 caída ───

    public function test_layer_two_down_with_a_cached_snapshot_holds_an_enforce_agent_and_spares_a_person(): void
    {
        $this->seedPayloadCache(self::fixturePayload());
        $this->failSnapshot();

        $this->agentWithToken($this->validToken());
        $agent = self::client(self::indeterminate());
        self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $this->run_eval($agent, 'enforce'));
        self::assertCount(1, $agent->nonces, 'el token se verificó contra la caché (su jti se consumió)');

        self::assertSame('ALLOW', $this->run_eval(self::client(self::indeterminate()), 'enforce'), 'una persona nunca se para por avería');
    }

    public function test_layer_two_down_with_a_cached_snapshot_lets_an_observe_agent_through(): void
    {
        $this->seedPayloadCache(self::fixturePayload());
        $this->failSnapshot();
        $this->agentWithToken($this->validToken());

        self::assertSame('ALLOW', $this->run_eval(self::client(self::indeterminate()), 'observe'));
    }

    public function test_layer_two_down_with_a_verified_cap_rule_still_blocks_a_person(): void
    {
        $this->seedPayloadCache(self::fixturePayload(['rules' => [self::capRule()]]));
        $this->failSnapshot();

        self::assertSame('BLOCK', $this->run_eval(self::client(self::indeterminate()), 'observe', ['cartTotalCents' => 999999, 'currency' => 'EUR', 'itemCount' => 1]));
    }

    // ── Tokens contra el resolver en LISTA del snapshot real ────────────────

    public function test_a_legitimate_token_verifies_against_the_list_and_its_jti_is_consumed(): void
    {
        $this->serveSnapshot($this->signedSnapshot(self::fixturePayload()));
        $this->agentWithToken($this->validToken());
        $client = self::client();

        self::assertSame('ALLOW', $this->run_eval($client, 'enforce'));
        $p = $client->payloads[0];
        self::assertSame(self::AGENT, $p['agentId']);
        self::assertArrayNotHasKey('_agent_token_signature_invalid', $p['orderContext']['cartAttributes']);
        self::assertCount(1, $client->nonces);
        self::assertSame([$this->merchant, self::AGENT], array_slice($client->nonces[0], 0, 2));
    }

    public function test_a_forged_token_is_invalid(): void
    {
        $this->serveSnapshot($this->signedSnapshot(self::fixturePayload()));
        $this->agentWithToken($this->token(self::AGENT, sodium_crypto_sign_secretkey(sodium_crypto_sign_keypair())));
        $client = self::client();

        $this->run_eval($client, 'enforce');
        $p = $client->payloads[0];
        self::assertNull($p['agentId']);
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_signature_invalid'] ?? null);
        self::assertSame([], $client->nonces);
    }

    /** Decisión 2: firma verificada + almacén de nonces caído en enforce = avería nuestra, retenida antes de preguntar al servidor. */
    public function test_nonce_store_down_in_enforce_is_an_outage_held_before_asking_the_server_with_the_real_snapshot(): void
    {
        $this->serveSnapshot($this->signedSnapshot(self::fixturePayload()));
        $this->agentWithToken($this->validToken());
        $client = self::client(
            new Trusteed_Eval_Result(Trusteed_Eval_Outcome::BLOCK, 'R002', 200),
            new Trusteed_Nonce_Result(Trusteed_Nonce_Outcome::INDETERMINATE, 'network_error')
        );

        self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $this->run_eval($client, 'enforce'));
        self::assertSame([], $client->payloads, 'no se pregunta al servidor: no es una regla');
        self::assertCount(1, $client->nonces);
    }

    /** Decisión 2: en observe pasa con la identidad auténtica y la marca. */
    public function test_nonce_store_down_in_observe_passes_with_the_authentic_identity_and_the_marker(): void
    {
        $this->serveSnapshot($this->signedSnapshot(self::fixturePayload()));
        $this->agentWithToken($this->validToken());
        $client = self::client(
            new Trusteed_Eval_Result(Trusteed_Eval_Outcome::ALLOW, 'ok', 200),
            new Trusteed_Nonce_Result(Trusteed_Nonce_Outcome::INDETERMINATE, 'network_error')
        );

        self::assertSame('ALLOW', $this->run_eval($client, 'observe'));
        self::assertSame(self::AGENT, $client->payloads[0]['agentId']);
        self::assertSame('true', $client->payloads[0]['orderContext']['cartAttributes']['_agent_token_nonce_unavailable'] ?? null);
        self::assertArrayNotHasKey('_agent_token_signature_invalid', $client->payloads[0]['orderContext']['cartAttributes']);
    }

    /** El snapshot se pide con las credenciales del plugin (el payload real no trae ninguna). */
    public function test_the_snapshot_is_pulled_with_the_plugin_credentials(): void
    {
        self::assertArrayNotHasKey('installationId', self::fixturePayload());
        $this->serveSnapshot($this->signedSnapshot(self::fixturePayload()));
        $this->agentWithToken($this->validToken());

        $this->run_eval(self::client(), 'enforce');

        $get = array_values(array_filter($GLOBALS['__amcp_remote_get_calls'], static fn (array $c): bool => str_contains($c['url'], '/v1/rules/snapshot/')))[0];
        self::assertSame(self::INST, $get['args']['headers']['X-Trusteed-Installation-Id']);
        self::assertSame(1, preg_match('/^t=(\d+),s=([0-9a-f]{64})$/', $get['args']['headers']['X-Trusteed-Signature'], $m));
        self::assertSame(hash_hmac('sha256', $m[1] . '.', self::SECRET), $m[2]);
    }

    /** Capa 2 y nonce con las credenciales del cliente (las de los ajustes), no del snapshot. */
    public function test_layer_two_and_nonce_are_signed_with_the_client_credentials(): void
    {
        $api = new Trusteed_Enforcement_Api_Client(self::API, self::INST, self::SECRET);
        $api->evaluate(['merchantId' => $this->merchant]);
        $this->assertSignedWithPluginCredentials();
        $api->consume_nonce($this->merchant, self::AGENT, 'jti-23a-000000000001', time() + 60);
        $this->assertSignedWithPluginCredentials();
        self::assertSame(self::INST, json_decode((string) $GLOBALS['__amcp_wp_remote_post_last_args']['body'], true)['installationId']);
    }

    // ── Arnés ────────────────────────────────────────────────────────────────

    private function run_eval(object $client, string $mode, array $ctx = ['cartTotalCents' => 5000, 'currency' => 'EUR', 'itemCount' => 1]): string
    {
        $snapshot = new Trusteed_Snapshot_Client_Woo(self::API, self::INST, self::SECRET);
        $enforcer = new Trusteed_Checkout_Enforcer($client, $this->merchant, self::INST, $snapshot, $mode, null);
        $method   = new ReflectionMethod($enforcer, 'run_evaluation');
        $method->setAccessible(true);
        return $method->invoke($enforcer, static fn(): array => $ctx);
    }

    private function agentWithToken(string $token): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::AGENT);
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $token);
    }

    private function assertSignedWithPluginCredentials(): void
    {
        $args = $GLOBALS['__amcp_wp_remote_post_last_args'];
        self::assertSame(self::INST, $args['headers']['X-Trusteed-Installation-Id']);
        self::assertSame(1, preg_match('/^t=(\d+),s=([0-9a-f]{64})$/', $args['headers']['X-Trusteed-Signature'], $m));
        self::assertSame(hash_hmac('sha256', $m[1] . '.' . $args['body'], self::SECRET), $m[2]);
    }

    private static function indeterminate(): Trusteed_Eval_Result
    {
        return new Trusteed_Eval_Result(Trusteed_Eval_Outcome::INDETERMINATE, 'http_5xx', 503);
    }

    private static function client(?Trusteed_Eval_Result $eval = null, ?Trusteed_Nonce_Result $nonce = null): object
    {
        $eval  ??= new Trusteed_Eval_Result(Trusteed_Eval_Outcome::ALLOW, 'ok', 200);
        $nonce ??= new Trusteed_Nonce_Result(Trusteed_Nonce_Outcome::ACCEPTED, 'ok', 200);
        return new class(self::API, self::INST, self::SECRET, $eval, $nonce) extends Trusteed_Enforcement_Api_Client {
            public array $payloads = [];
            public array $nonces   = [];
            public function __construct(string $a, string $b, string $c, private Trusteed_Eval_Result $eval, private Trusteed_Nonce_Result $nonce)
            {
                parent::__construct($a, $b, $c);
            }
            public function evaluate(array $payload): Trusteed_Eval_Result
            {
                $this->payloads[] = $payload;
                return $this->eval;
            }
            public function consume_nonce(string $merchant_id, string $agent_did, string $jti, int $exp): Trusteed_Nonce_Result
            {
                $this->nonces[] = [$merchant_id, $agent_did, $jti];
                return $this->nonce;
            }
        };
    }

    /** @return array<string,mixed> Regla R030 (tope) que la válvula offline evalúa. */
    private static function capRule(): array
    {
        return ['ruleCode' => 'R030.simple-controls', 'enabled' => true, 'mode' => 'enforce', 'appliesTo' => 'ALL', 'tier' => 1, 'params' => ['maxAmountCents' => 10000]];
    }

    /**
     * `snapshotPayload` real del fixture con fechas al día y la clave del
     * agente de prueba al FINAL de la lista (forma que emite el servidor).
     *
     * @param array{rules?:array<int,array<string,mixed>>} $over
     * @return array<string,mixed>
     */
    private static function fixturePayload(array $over = []): array
    {
        $path = __DIR__ . '/../../../../shared/src/enforcement/__fixtures__/platform-flow-conformance.json';
        $json = json_decode((string) file_get_contents($path), true);
        self::assertIsArray($json, 'el fixture tiene que ser alcanzable desde el monorepo');
        $p = $json['snapshotResolverShape']['snapshotPayload'];
        self::assertTrue(array_is_list($p['agentDidResolver']), 'el servidor emite una lista');
        $p['generatedAt'] = gmdate('Y-m-d\TH:i:s.000\Z', time() - 5);
        $p['validUntil']  = gmdate('Y-m-d\TH:i:s.000\Z', time() + 120);
        if (isset($over['rules'])) {
            $p['rules'] = array_merge($p['rules'], $over['rules']);
        }
        $p['agentDidResolver'][] = ['did' => self::AGENT, 'publicKeyJwk' => ['kty' => 'OKP', 'crv' => 'Ed25519', 'x' => '__AGENT_X__']];
        return $p;
    }

    /** @param array<string,mixed> $payload */
    private function withAgentKey(array $payload): array
    {
        foreach ($payload['agentDidResolver'] as $i => $e) {
            if (($e['publicKeyJwk']['x'] ?? '') === '__AGENT_X__') {
                $payload['agentDidResolver'][$i]['publicKeyJwk']['x'] = self::b64($this->agentPub);
            }
        }
        return $payload;
    }

    /** @param array<string,mixed> $payload */
    private function signedSnapshot(array $payload, ?string $priv = null, string $kid = self::KID): string
    {
        $h = self::b64((string) json_encode(['alg' => 'EdDSA', 'kid' => $kid, 'typ' => 'cel-snapshot+jwt']));
        $b = self::b64((string) json_encode($this->withAgentKey($payload), JSON_UNESCAPED_SLASHES));
        return $h . '.' . $b . '.' . self::b64(sodium_crypto_sign_detached($h . '.' . $b, $priv ?? $this->platformPriv));
    }

    private function tamperedJws(): string
    {
        return $this->signedSnapshot(self::fixturePayload(), sodium_crypto_sign_secretkey(sodium_crypto_sign_keypair()));
    }

    /** @param array<string,mixed> $payload */
    private function seedPayloadCache(array $payload): void
    {
        $GLOBALS['__amcp_transients_store']['trusteed_snap_' . substr(md5($this->merchant), 0, 12)] = $this->withAgentKey($payload);
    }

    private function serveSnapshot(string $jws): void
    {
        $GLOBALS['__amcp_remote_get_responses'][self::API . '/v1/rules/snapshot/' . rawurlencode($this->merchant)] = ['response' => ['code' => 200], 'body' => $jws];
    }

    private function failSnapshot(): void
    {
        unset($GLOBALS['__amcp_remote_get_responses'][self::API . '/v1/rules/snapshot/' . rawurlencode($this->merchant)]);
    }

    /** @param array<string,string> $keys kid => raw public key */
    private function publishJwks(array $keys): void
    {
        $jwks = [];
        foreach ($keys as $kid => $raw) {
            $jwks[] = self::jwk($kid, $raw);
        }
        $GLOBALS['__amcp_remote_get_responses'][self::API . '/.well-known/jwks.json'] = ['response' => ['code' => 200], 'body' => (string) json_encode(['keys' => $jwks])];
    }

    /** @return array<string,string> */
    private static function jwk(string $kid, string $raw): array
    {
        return ['kty' => 'OKP', 'crv' => 'Ed25519', 'kid' => $kid, 'x' => self::b64($raw)];
    }

    private function validToken(): string
    {
        return $this->token(self::AGENT, $this->agentPriv);
    }

    private function token(string $did, string $priv): string
    {
        $this->jtiSeq++;
        $h = self::b64((string) json_encode(['alg' => 'EdDSA', 'typ' => 'trusteed-agent-token+jwt', 'kid' => $did . '#key-1']));
        $p = self::b64((string) json_encode([
            'iss' => $did, 'aud' => 'trusteed', 'merchantId' => $this->merchant,
            'iat' => time(), 'exp' => time() + 120, 'jti' => 'jti23aToken' . str_pad((string) $this->jtiSeq, 12, '0', STR_PAD_LEFT),
        ]));
        return $h . '.' . $p . '.' . self::b64(sodium_crypto_sign_detached($h . '.' . $p, $priv));
    }

    private static function b64(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }
}
