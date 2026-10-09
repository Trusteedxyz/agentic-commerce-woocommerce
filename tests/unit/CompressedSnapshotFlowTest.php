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
 * Task 24 (plan 04) — snapshot comprimido (`zip: "DEF"`, payload > 50 KB).
 *
 * Consume la sección `compressedSnapshot` del fixture compartido: el JWS lo
 * produjeron el builder y el firmador REALES del servidor, con claves
 * derivadas de etiquetas públicas. Los 12 vectores pasan por el cliente de
 * snapshot REAL (JWKS del fixture servido por el stub HTTP) y los 11 cuya
 * firma verifica, por el decodificador con su motivo; y el enforcer real
 * evalúa con ese snapshot: el agente con token no queda retenido por el
 * tamaño, su token verifica contra el resolver de 300 DIDs, la persona pasa,
 * la válvula lee el tope R030 del payload comprimido y la bomba se maneja sin
 * reservar más que el tope.
 */
final class CompressedSnapshotFlowTest extends TestCase
{
    private const API    = 'https://api.trusteed.test';
    private const INST   = 'inst-24';
    private const SECRET = 'example-hmac-secret-not-real-24';

    private static ?array $section = null;
    private Trusteed_Test_Wc_Session $session;
    private int $jtiSeq = 0;

    protected function setUp(): void
    {
        $GLOBALS['__amcp_transients_store']     = [];
        $GLOBALS['__amcp_remote_get_responses'] = [];
        $GLOBALS['__amcp_remote_get_calls']     = [];
        $this->session = new Trusteed_Test_Wc_Session();
        $GLOBALS['trusteed_test_wc'] = (object) ['session' => $this->session, 'cart' => null];
        $GLOBALS['__amcp_remote_get_responses'][self::API . '/.well-known/jwks.json'] = ['response' => ['code' => 200], 'body' => (string) json_encode(self::section()['jwks'])];
        $this->serveVector('valid.deflated-by-real-signer');
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['trusteed_test_wc']);
        $GLOBALS['__amcp_transients_store']     = [];
        $GLOBALS['__amcp_remote_get_responses'] = [];
        $GLOBALS['__amcp_remote_get_calls']     = [];
    }

    // ── Conformidad ─────────────────────────────────────────────────────────

    public function test_the_fixture_cap_is_the_port_cap(): void
    {
        self::assertSame(Trusteed_Snapshot_Payload_Decoder::MAX_INFLATED_BYTES, self::section()['maxInflatedBytes']);
        self::assertSame('DEF', Trusteed_Snapshot_Payload_Decoder::ZIP_DEFLATE);
    }

    public function test_every_vector_through_the_real_snapshot_client(): void
    {
        $run = 0;
        foreach (self::section()['vectors'] as $v) {
            $GLOBALS['__amcp_transients_store'] = [];
            $this->serveVector($v['id']);
            $result = (new Trusteed_Snapshot_Client_Woo(self::API, self::INST, self::SECRET))->get_snapshot_result(self::merchant());
            self::assertSame($v['expected']['status'], $result['status'], $v['id']);
            if ('ok' === $result['status']) {
                self::assertSame($v['expected']['ruleSetVersion'], $result['payload']['ruleSetVersion'], $v['id']);
                self::assertSame($v['expected']['merchantId'], $result['payload']['merchantId'], $v['id']);
                self::assertCount($v['expected']['agentDidResolverCount'], $result['payload']['agentDidResolver'], $v['id']);
            } else {
                self::assertNull($result['payload'], $v['id']);
            }
            $run++;
        }
        self::assertSame(12, $run);
    }

    public function test_every_signature_valid_vector_through_the_decoder_with_its_reason(): void
    {
        $run = 0;
        foreach (self::section()['vectors'] as $v) {
            if ('tampered' === $v['expected']['status']) {
                continue;
            }
            [$h, $p] = explode('.', $v['jws']);
            [$reason, $payload] = Trusteed_Snapshot_Payload_Decoder::decode(json_decode(self::unb64($h), true), self::unb64($p));
            if ('ok' === $v['expected']['status']) {
                self::assertSame(Trusteed_Snapshot_Payload_Decoder::OK, $reason, $v['id']);
                self::assertSame($v['expected']['ruleSetVersion'], $payload['ruleSetVersion'], $v['id']);
            } else {
                self::assertSame($v['expected']['reason'], $reason, $v['id']);
                self::assertNull($payload, $v['id']);
            }
            $run++;
        }
        self::assertSame(11, $run);
    }

    public function test_the_bomb_is_rejected_without_reserving_more_than_the_cap(): void
    {
        [$h, $p] = explode('.', self::vector('bomb.over-cap')['jws']);
        $before  = memory_get_usage();
        memory_reset_peak_usage();
        [$reason] = Trusteed_Snapshot_Payload_Decoder::decode(json_decode(self::unb64($h), true), self::unb64($p));

        self::assertSame(Trusteed_Snapshot_Payload_Decoder::INFLATE_OVER_CAP, $reason);
        self::assertLessThan(6 * 1024 * 1024, memory_get_peak_usage() - $before, 'la bomba (8 MiB) no se infla entera');
    }

    // ── Flujo real del enforcer ─────────────────────────────────────────────

    /** @return array<string,array{0:string}> */
    public static function modes(): array
    {
        return ['enforce' => ['enforce'], 'observe' => ['observe']];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modes')]
    public function test_a_legitimate_agent_is_not_held_by_the_size_and_its_token_verifies(string $mode): void
    {
        $this->agentWithToken($this->conformanceToken());
        $client = self::client();

        self::assertSame('ALLOW', $this->run_eval($client, $mode), 'antes: snapshot comprimido = ilegible ⇒ agente retenido');
        self::assertSame(self::section()['agent']['did'], $client->payloads[0]['agentId']);
        self::assertArrayNotHasKey('_agent_token_signature_invalid', $client->payloads[0]['orderContext']['cartAttributes']);
        self::assertCount(1, $client->nonces, 'el token verificó: su jti se consume');
    }

    public function test_a_forged_token_is_still_invalid_against_the_compressed_resolver(): void
    {
        $this->agentWithToken($this->token(sodium_crypto_sign_secretkey(sodium_crypto_sign_keypair())));
        $client = self::client();

        $this->run_eval($client, 'enforce');
        self::assertNull($client->payloads[0]['agentId']);
        self::assertSame('true', $client->payloads[0]['orderContext']['cartAttributes']['_agent_token_signature_invalid'] ?? null);
        self::assertSame([], $client->nonces);
    }

    public function test_a_person_passes_with_the_compressed_snapshot(): void
    {
        self::assertSame('ALLOW', $this->run_eval(self::client(self::indeterminate()), 'enforce'));
    }

    public function test_the_offline_valve_reads_the_cap_from_the_compressed_payload(): void
    {
        $ctx = ['cartTotalCents' => 999999, 'currency' => 'EUR', 'itemCount' => 1];
        self::assertSame('BLOCK', $this->run_eval(self::client(self::indeterminate()), 'observe', $ctx), 'R030 (5000,00) viene DENTRO del payload comprimido');
        self::assertSame('ALLOW', $this->run_eval(self::client(self::indeterminate()), 'observe'));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modes')]
    public function test_the_bomb_holds_an_agent_with_a_token_and_lets_a_person_through(string $mode): void
    {
        $this->serveVector('bomb.over-cap');
        $this->agentWithToken($this->conformanceToken());
        $client = self::client();

        self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $this->run_eval($client, $mode));
        self::assertSame([], $client->payloads);
        self::assertSame([], $client->nonces);
        self::assertSame('ALLOW', $this->run_eval(self::client(self::indeterminate()), $mode), 'una persona nunca queda retenida por esto');
    }

    public function test_the_bomb_is_unavailable_and_a_bad_signature_is_tampered(): void
    {
        $this->serveVector('bomb.over-cap');
        self::assertSame('unavailable', (new Trusteed_Snapshot_Client_Woo(self::API, self::INST, self::SECRET))->get_snapshot_result(self::merchant())['status']);
        $this->serveVector('bomb.bad-signature');
        self::assertSame('tampered', (new Trusteed_Snapshot_Client_Woo(self::API, self::INST, self::SECRET))->get_snapshot_result(self::merchant())['status'], 'la firma se mira antes de inflar');
    }

    // ── Arnés ────────────────────────────────────────────────────────────────

    private static function section(): array
    {
        if (null === self::$section) {
            $path = __DIR__ . '/../../../../shared/src/enforcement/__fixtures__/platform-flow-conformance.json';
            $json = json_decode((string) file_get_contents($path), true);
            self::assertIsArray($json, 'el fixture tiene que ser alcanzable desde el monorepo');
            self::$section = $json['compressedSnapshot'];
        }
        return self::$section;
    }

    private static function vector(string $id): array
    {
        foreach (self::section()['vectors'] as $v) {
            if ($v['id'] === $id) {
                return $v;
            }
        }
        self::fail('vector ausente: ' . $id);
    }

    private static function merchant(): string
    {
        return (string) self::vector('valid.deflated-by-real-signer')['expected']['merchantId'];
    }

    private function serveVector(string $id): void
    {
        $GLOBALS['__amcp_remote_get_responses'][self::API . '/v1/rules/snapshot/' . rawurlencode(self::merchant())] = ['response' => ['code' => 200], 'body' => self::vector($id)['jws']];
    }

    private function run_eval(object $client, string $mode, array $ctx = ['cartTotalCents' => 5000, 'currency' => 'EUR', 'itemCount' => 1]): string
    {
        $snapshot = new Trusteed_Snapshot_Client_Woo(self::API, self::INST, self::SECRET);
        $enforcer = new Trusteed_Checkout_Enforcer($client, self::merchant(), self::INST, $snapshot, $mode, null);
        $method   = new ReflectionMethod($enforcer, 'run_evaluation');
        $method->setAccessible(true);
        return $method->invoke($enforcer, static fn(): array => $ctx);
    }

    private function agentWithToken(string $token): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::section()['agent']['did']);
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $token);
    }

    private static function indeterminate(): Trusteed_Eval_Result
    {
        return new Trusteed_Eval_Result(Trusteed_Eval_Outcome::INDETERMINATE, 'http_5xx', 503);
    }

    private static function client(?Trusteed_Eval_Result $eval = null): object
    {
        $eval ??= new Trusteed_Eval_Result(Trusteed_Eval_Outcome::ALLOW, 'ok', 200);
        $nonce = new Trusteed_Nonce_Result(Trusteed_Nonce_Outcome::ACCEPTED, 'ok', 200);
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

    /** Clave del agente de conformidad: semilla = SHA-256(etiqueta pública). */
    private function conformanceToken(): string
    {
        $seed = hash('sha256', self::section()['agent']['seedLabel'], true);
        return $this->token(sodium_crypto_sign_secretkey(sodium_crypto_sign_seed_keypair($seed)));
    }

    private function token(string $priv): string
    {
        $this->jtiSeq++;
        $did = self::section()['agent']['did'];
        $h   = self::b64((string) json_encode(['alg' => 'EdDSA', 'typ' => 'trusteed-agent-token+jwt', 'kid' => self::section()['agent']['kid']]));
        $p   = self::b64((string) json_encode([
            'iss' => $did, 'aud' => 'trusteed', 'merchantId' => self::merchant(),
            'iat' => time(), 'exp' => time() + 120, 'jti' => 'jti24Token' . str_pad((string) $this->jtiSeq, 12, '0', STR_PAD_LEFT),
        ]));
        return $h . '.' . $p . '.' . self::b64(sodium_crypto_sign_detached($h . '.' . $p, $priv));
    }

    private static function b64(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    private static function unb64(string $s): string
    {
        return (string) base64_decode(strtr($s, '-_', '+/'));
    }
}
