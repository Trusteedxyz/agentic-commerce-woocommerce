<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/class-cart-bridge.php';
require_once __DIR__ . '/../../includes/class-enforcement-api-client.php';
require_once __DIR__ . '/../../includes/class-offline-safety-valve-evaluator.php';
require_once __DIR__ . '/../../includes/class-agent-event-webhook.php';
require_once __DIR__ . '/../../includes/class-multi-add-handler.php';
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
 * A8 (auditoría 2026-09-27, carril C): un comprador humano nunca se bloquea por
 * una avería nuestra. Conductual: ejecuta `run_evaluation()` real (por
 * reflexión) con un cliente que devuelve INDETERMINATE, la misma forma que da
 * `Trusteed_Enforcement_Api_Client` ante red caída, 5xx, 4xx (429 incluido) o
 * el 503 del timeout de 200 ms del servidor.
 */
final class HumanCheckoutNotBlockedByOutageTest extends TestCase
{
    private object $client;
    private Trusteed_Test_Wc_Session $session;

    protected function setUp(): void
    {
        $this->session               = new Trusteed_Test_Wc_Session();
        $GLOBALS['trusteed_test_wc'] = (object) ['session' => $this->session, 'cart' => null];
        $this->client                = self::client_returning(
            new Trusteed_Eval_Result(Trusteed_Eval_Outcome::INDETERMINATE, 'http_5xx', 503)
        );
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['trusteed_test_wc'], $GLOBALS['trusteed_test_is_checkout']);
        $_GET = [];
    }

    private static function client_returning(Trusteed_Eval_Result $result): object
    {
        return new class('https://api.trusteed.test', 'inst', 'example-secret', $result) extends Trusteed_Enforcement_Api_Client {
            public ?array $last_payload = null;
            public int $calls = 0;
            private Trusteed_Eval_Result $result;
            public function __construct(string $a, string $b, string $c, Trusteed_Eval_Result $result)
            {
                parent::__construct($a, $b, $c);
                $this->result = $result;
            }
            public function evaluate(array $payload): Trusteed_Eval_Result
            {
                $this->calls++;
                $this->last_payload = $payload;
                return $this->result;
            }
        };
    }

    private static function webhook_spy(): object
    {
        return new class('https://api.trusteed.test', 'inst', 'example-secret', 'merchant-1') extends Trusteed_Agent_Event_Webhook {
            public array $indeterminate = [];
            public function emit_indeterminate(string $agent_did, string $reason, string $mode): void
            {
                $this->indeterminate[] = [$agent_did, $reason, $mode];
            }
        };
    }

    private static function snapshot_with_rules(array $rules): Trusteed_Snapshot_Client_Woo
    {
        return new class('https://api.trusteed.test', 'inst', 'example-secret', $rules) extends Trusteed_Snapshot_Client_Woo {
            private array $fixed_rules;
            public function __construct(string $a, string $b, string $c, array $rules)
            {
                parent::__construct($a, $b, $c);
                $this->fixed_rules = $rules;
            }
            // Task 23a: el enforcer lee el snapshot VERIFICADO con su estado.
            public function get_snapshot_result(string $merchant_id): array
            {
                return ['status' => self::STATUS_OK, 'payload' => ['rules' => $this->fixed_rules, 'agentDidResolver' => []]];
            }
        };
    }

    private function run_eval(
        string $merchant_id = 'merchant-1',
        string $failure_mode = 'enforce',
        ?Trusteed_Agent_Event_Webhook $webhook = null,
        ?Trusteed_Snapshot_Client_Woo $snapshot = null,
        array $order_context = ['cartTotalCents' => 5000, 'currency' => 'EUR', 'itemCount' => 1]
    ): string {
        $enforcer = new Trusteed_Checkout_Enforcer($this->client, $merchant_id, 'inst', $snapshot, $failure_mode, $webhook);
        $method   = new ReflectionMethod($enforcer, 'run_evaluation');
        $method->setAccessible(true);
        return $method->invoke($enforcer, static fn(): array => $order_context);
    }

    public function test_human_checkout_is_allowed_when_the_api_returns_503_in_enforce_mode(): void
    {
        self::assertSame('ALLOW', $this->run_eval());
        self::assertSame(1, $this->client->calls);
        self::assertArrayNotHasKey('endpointClass', $this->client->last_payload, 'el payload orgánico no cambia');
    }

    public static function indeterminate_reasons(): array
    {
        return [
            'red caída'          => [new Trusteed_Eval_Result(Trusteed_Eval_Outcome::INDETERMINATE, 'network_error')],
            'http 429'           => [new Trusteed_Eval_Result(Trusteed_Eval_Outcome::INDETERMINATE, 'http_4xx', 429)],
            '503 del timeout'    => [new Trusteed_Eval_Result(Trusteed_Eval_Outcome::INDETERMINATE, 'http_5xx', 503)],
            'respuesta ilegible' => [new Trusteed_Eval_Result(Trusteed_Eval_Outcome::INDETERMINATE, 'bad_response', 200)],
        ];
    }

    /** @dataProvider indeterminate_reasons */
    public function test_no_indeterminate_reason_blocks_a_human_in_enforce_mode(Trusteed_Eval_Result $result): void
    {
        $this->client = self::client_returning($result);
        self::assertSame('ALLOW', $this->run_eval());
    }

    /**
     * Review Focus 2: el payload orgánico es byte a byte el que construía HEAD
     * (mismas seis claves, mismo orden, mismos valores). Este test pasa también
     * contra HEAD 365d36319: es el control de que no cambió.
     */
    public function test_organic_payload_is_byte_identical_to_head(): void
    {
        $ctx = ['cartTotalCents' => 5000, 'currency' => 'EUR', 'itemCount' => 1];
        $this->run_eval('merchant-1', 'enforce', null, null, $ctx);
        $p = $this->client->last_payload;
        $expected = json_encode([
            'merchantId'     => 'merchant-1',
            'agentId'        => null,
            'orderContext'   => $ctx,
            'platform'       => 'WOOCOMMERCE',
            'installationId' => 'inst',
            'timestamp'      => $p['timestamp'],
        ]);
        self::assertSame($expected, json_encode($p));
    }

    public function test_agent_checkout_still_blocks_on_503_in_enforce_mode(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, 'did:web:agent.example.com');
        // 5b: cerrado por avería nuestra, no por una regla (antes 'BLOCK' = mensaje de regla).
        self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $this->run_eval());
        self::assertSame('agentic_endpoint', $this->client->last_payload['endpointClass']);
        // Task 16: un DID sólo declarado (sin token válido) viaja como agente anónimo.
        self::assertNull($this->client->last_payload['agentId']);
    }

    public function test_agent_checkout_passes_on_503_in_observe_mode(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, 'did:web:agent.example.com');
        self::assertSame('ALLOW', $this->run_eval('merchant-1', 'observe'));
    }

    public function test_origin_marker_alone_is_an_anonymous_agent(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN, '1');
        self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $this->run_eval());
        self::assertNull($this->client->last_payload['agentId']);
        self::assertSame('agentic_endpoint', $this->client->last_payload['endpointClass']);
    }

    public function test_token_alone_is_an_anonymous_agent(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, 'aGVhZGVy.cGF5bG9hZA.c2ln');
        self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $this->run_eval());
        self::assertSame('agentic_endpoint', $this->client->last_payload['endpointClass']);
    }

    /** CARRY de la Task 1: un modo de otro vocabulario no abre a un agente. */
    public function test_failure_mode_from_another_vocabulary_blocks_an_agent_and_spares_a_human(): void
    {
        self::assertSame('ALLOW', $this->run_eval('merchant-1', 'permissive'), 'humano');
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, 'did:web:agent.example.com');
        self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $this->run_eval('merchant-1', 'permissive'), 'agente');
    }

    public function test_failure_mode_is_trimmed_and_lowercased(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, 'did:web:agent.example.com');
        self::assertSame('ALLOW', $this->run_eval('merchant-1', '  OBSERVE '));
    }

    public function test_unconfigured_merchant_never_blocks_a_human(): void
    {
        self::assertSame('ALLOW', $this->run_eval(''));
        self::assertSame(0, $this->client->calls, 'sin comerciante no hay a quién preguntar');
    }

    /**
     * Task 23a (plan 04, semántica fijada en `degradedEnforcement`): sustituye a
     * `test_unconfigured_merchant_still_blocks_an_agent_in_enforce_mode`. Un
     * comerciante sin conectar es `layer_disabled` —configuración, no avería—,
     * igual que WC-AgenticTools/PrestaShop/Odoo sin configurar: no se retiene a
     * nadie y no se pregunta a nadie.
     */
    public function test_unconfigured_merchant_is_layer_disabled_and_holds_no_agent(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, 'did:web:agent.example.com');
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, 'aaa.bbb.ccc');
        self::assertSame('ALLOW', $this->run_eval(''));
        self::assertSame(0, $this->client->calls);
    }

    /**
     * La telemetría `enforcement_indeterminate` leía el DID de la sesión
     * DESPUÉS de que run_evaluation() la borrara: nunca se emitía.
     */
    public function test_indeterminate_telemetry_carries_the_did_read_before_the_session_is_cleared(): void
    {
        $webhook = self::webhook_spy();
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, 'did:web:agent.example.com');
        $this->run_eval('merchant-1', 'enforce', $webhook);
        self::assertSame([['did:web:agent.example.com', 'http_5xx', 'enforce']], $webhook->indeterminate);
        self::assertNull($this->session->get(Trusteed_Cart_Bridge::SESSION_AGENT_ID), 'la sesión sí se borra');
    }

    public function test_session_markers_are_cleared_after_evaluation(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN, '1');
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, 'aGVhZGVy.cGF5bG9hZA.c2ln');
        $this->run_eval();
        self::assertNull($this->session->get(Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN));
        self::assertNull($this->session->get(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN));
    }

    /**
     * Review Focus 1: offline, a un humano sólo lo puede bloquear una regla
     * `appliesTo: ALL` del comerciante (el tope de R030); una regla AGENT de la
     * válvula (R014 aquí) ya no le toca. Al agente sí.
     */
    public function test_offline_valve_applies_only_all_rules_to_a_human(): void
    {
        $agent_only = self::snapshot_with_rules([
            ['ruleCode' => 'R014.delivery-risk-guard', 'enabled' => true, 'appliesTo' => 'AGENT', 'params' => ['highRiskCountries' => ['XX']]],
        ]);
        $ctx = ['cartTotalCents' => 5000, 'currency' => 'EUR', 'itemCount' => 1, 'billingCountry' => 'XX'];
        self::assertSame('ALLOW', $this->run_eval('merchant-1', 'observe', null, $agent_only, $ctx), 'humano: regla AGENT no aplica');

        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, 'did:web:agent.example.com');
        self::assertSame('BLOCK', $this->run_eval('merchant-1', 'observe', null, $agent_only, $ctx), 'agente: la regla AGENT sí');
    }

    public function test_merchant_own_cap_still_blocks_a_human_offline(): void
    {
        $cap = self::snapshot_with_rules([
            ['ruleCode' => 'R030.simple-controls', 'enabled' => true, 'appliesTo' => 'ALL', 'params' => ['maxAmountCents' => 1000]],
        ]);
        self::assertSame('BLOCK', $this->run_eval('merchant-1', 'observe', null, $cap));
    }

    public function test_origin_param_is_captured_into_the_session_without_a_did(): void
    {
        $_GET = [Trusteed_Cart_Bridge::ORIGIN_PARAM => 'mcp'];
        $GLOBALS['trusteed_test_is_checkout'] = true;
        $enforcer = new Trusteed_Checkout_Enforcer($this->client, 'merchant-1', 'inst');
        $enforcer->capture_agent_session_early();
        self::assertSame('1', $this->session->get(Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN));
        self::assertNull($this->session->get(Trusteed_Cart_Bridge::SESSION_AGENT_ID));
    }

    public function test_cart_bridge_marks_every_checkout_url_with_the_origin(): void
    {
        $bridge = new Trusteed_Cart_Bridge();
        $build  = new ReflectionMethod($bridge, 'build_checkout_url');
        $build->setAccessible(true);
        $one  = $build->invoke($bridge, [['product_id' => 7, 'quantity' => 1, 'variation_id' => 0]]);
        $many = $build->invoke($bridge, [
            ['product_id' => 7, 'quantity' => 1, 'variation_id' => 0],
            ['product_id' => 8, 'quantity' => 2, 'variation_id' => 0],
        ]);
        foreach ([$one, $many] as $url) {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            self::assertSame('mcp', $query[Trusteed_Cart_Bridge::ORIGIN_PARAM] ?? null, $url);
        }
    }

    /** El carrito de varias líneas pasa por una redirección: la marca tiene que sobrevivirla. */
    public function test_multi_add_redirect_forwards_the_origin_marker(): void
    {
        $forwarded = Trusteed_Multi_Add_Handler::forwarded_query_args([
            Trusteed_Cart_Bridge::ORIGIN_PARAM => 'mcp',
            'items'                            => [['product_id' => 7, 'quantity' => 1]],
        ]);
        self::assertSame([Trusteed_Cart_Bridge::ORIGIN_PARAM => 'mcp'], $forwarded);
        self::assertSame(
            [Trusteed_Cart_Bridge::AGENT_ID_PARAM => rawurlencode('did:web:agent.example.com'), Trusteed_Cart_Bridge::ORIGIN_PARAM => 'mcp'],
            Trusteed_Multi_Add_Handler::forwarded_query_args([Trusteed_Cart_Bridge::AGENT_ID_PARAM => 'did:web:agent.example.com', Trusteed_Cart_Bridge::ORIGIN_PARAM => 'x'])
        );
        self::assertSame([], Trusteed_Multi_Add_Handler::forwarded_query_args(['items' => []]));
    }
}
