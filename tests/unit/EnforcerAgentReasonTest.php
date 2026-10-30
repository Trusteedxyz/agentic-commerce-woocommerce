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
 * El veredicto estructurado del servidor (`agent_reason`) sobrevive a run_evaluation():
 * el agente recibe el motivo del bloqueo, saneado, sin cambiar el retorno string.
 */
final class EnforcerAgentReasonTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['trusteed_test_wc'] = (object) ['session' => new Trusteed_Test_Wc_Session(), 'cart' => null];
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['trusteed_test_wc']);
    }

    private static function client(?\Throwable $throws, Trusteed_Eval_Result $result): object
    {
        return new class('https://api.trusteed.test', 'inst', 'example-secret', $throws, $result) extends Trusteed_Enforcement_Api_Client {
            public function __construct(string $a, string $b, string $c, private ?\Throwable $throws, private Trusteed_Eval_Result $result)
            {
                parent::__construct($a, $b, $c);
            }
            public function evaluate(array $payload): Trusteed_Eval_Result
            {
                if (null !== $this->throws) {
                    throw $this->throws;
                }
                return $this->result;
            }
        };
    }

    /** @return array{0:string,1:Trusteed_Checkout_Enforcer} */
    private function eval_with(Trusteed_Eval_Result $result, ?\Throwable $throws = null): array
    {
        $enforcer = new Trusteed_Checkout_Enforcer(self::client($throws, $result), 'merchant-1', 'inst', null, 'enforce', null);
        $method   = new ReflectionMethod($enforcer, 'run_evaluation');
        $method->setAccessible(true);
        $d = $method->invoke($enforcer, static fn(): array => ['cartTotalCents' => 5000, 'currency' => 'EUR', 'itemCount' => 1, 'billingCountry' => 'ES']);
        return [$d, $enforcer];
    }

    public function test_block_del_servidor_conserva_agent_reason_saneado(): void
    {
        $reason = [
            'reason_category' => 'restricted_item',
            'rule_code'       => 'R012',
            'reason'          => 'Checkout declined: remove the items.',
            'next_action'     => 'remove_items',
            'extra'           => 'no',
        ];
        [$d, $e] = $this->eval_with(new Trusteed_Eval_Result(Trusteed_Eval_Outcome::BLOCK, 'ok', 200, null, Trusteed_Eval_Result::sanitize_agent_reason($reason)));
        self::assertSame('BLOCK', $d);
        self::assertSame(
            ['reason_category' => 'restricted_item', 'rule_code' => 'R012', 'reason' => 'Checkout declined: remove the items.', 'next_action' => 'remove_items'],
            $e->get_last_agent_reason()
        );
    }

    public function test_el_parseo_del_cliente_descarta_lo_que_no_son_strings_y_lo_demasiado_largo(): void
    {
        $clean = Trusteed_Eval_Result::sanitize_agent_reason([
            'reason_category' => 'order_limits',
            'rule_code'       => ['x'],
            'reason'          => 'Checkout declined: adjust the order and retry.',
            'next_action'     => str_repeat('a', 65),
        ]);
        self::assertNull($clean, 'next_action largo se descarta (y es obligatorio), no se trunca');
        $ok = Trusteed_Eval_Result::sanitize_agent_reason(['reason_category' => 'order_limits', 'rule_code' => ['x'], 'reason' => 'Checkout declined: adjust the order and retry.', 'next_action' => 'adjust_order']);
        self::assertSame(['reason_category', 'reason', 'next_action'], array_keys($ok));
        self::assertNull(Trusteed_Eval_Result::sanitize_agent_reason(['reason_category' => 'order_limits', 'reason' => str_repeat('a', 401), 'next_action' => 'adjust_order']), 'reason largo: todo fuera, sin truncar');
        self::assertNull(Trusteed_Eval_Result::sanitize_agent_reason('x'));
        self::assertNull(Trusteed_Eval_Result::sanitize_agent_reason(['reason_category' => 'order_limits']));
    }

    /** @return array<string,array{0:string}> */
    public static function motivos_fuera_de_contrato(): array
    {
        return array(
            'digito'         => array( 'Limit of 5000 items' ),
            'digito unicode' => array( "Limit \u{0665} items" ),
            'menor que'      => array( 'Remove <b>items</b>' ),
            'mayor que'      => array( 'Items > limit' ),
            'entidad'        => array( 'Remove &lt;items' ),
            'ancho cero'     => array( "a\u{200B}b" ),
            'marca arabe'    => array( "a\u{061C}b" ),
            'separador'      => array( "a\u{2028}b" ),
            'bidi'           => array( "a\u{202E}b" ),
        );
    }

    /** @dataProvider motivos_fuera_de_contrato */
    public function test_un_reason_fuera_de_contrato_descarta_todo_el_agent_reason( string $reason ): void
    {
        self::assertNull(Trusteed_Eval_Result::sanitize_agent_reason(['reason_category' => 'order_limits', 'reason' => $reason, 'next_action' => 'adjust_order']));
    }

    public function test_una_frase_del_contrato_pasa(): void
    {
        $s = 'Checkout declined: remove the restricted items and retry. Nothing was charged.';
        $c = Trusteed_Eval_Result::sanitize_agent_reason(['reason_category' => 'restricted_item', 'reason' => $s, 'next_action' => 'remove_items']);
        self::assertSame($s, $c['reason']);
    }

    public function test_block_sin_agent_reason_deriva_merchant_policy_sin_regla(): void
    {
        [$d, $e] = $this->eval_with(new Trusteed_Eval_Result(Trusteed_Eval_Outcome::BLOCK, 'ok', 200));
        self::assertSame('BLOCK', $d);
        $r = $e->get_last_agent_reason();
        self::assertSame('merchant_policy', $r['reason_category']);
        self::assertArrayNotHasKey('rule_code', $r);
        self::assertSame('escalate_human', $r['next_action']);
        // M3: el derivado por el propio plugin se declara, no se hace pasar por veredicto del servidor.
        self::assertSame('plugin_fallback', $r['source']);
    }

    public function test_agent_reason_del_servidor_no_lleva_marca_de_fallback(): void
    {
        $reason = ['reason_category' => 'restricted_item', 'reason' => 'x', 'next_action' => 'remove_items', 'source' => 'plugin_fallback'];
        [, $e] = $this->eval_with(new Trusteed_Eval_Result(Trusteed_Eval_Outcome::BLOCK, 'ok', 200, null, Trusteed_Eval_Result::sanitize_agent_reason($reason)));
        self::assertArrayNotHasKey('source', $e->get_last_agent_reason());
    }

    public function test_rule_code_hasta_64_con_formato_valido_no_se_trunca(): void
    {
        foreach (['R052.platform-max-quantity-per-line', 'R041.max-orders-per-hour-merchant', 'R026.subscription-autorenew-guard', 'R012'] as $code) {
            $c = Trusteed_Eval_Result::sanitize_agent_reason(['reason_category' => 'order_limits', 'rule_code' => $code, 'reason' => 'x', 'next_action' => 'adjust_order']);
            self::assertSame($code, $c['rule_code']);
        }
    }

    public function test_rule_code_con_formato_invalido_o_largo_se_descarta_no_se_trunca(): void
    {
        foreach (['ignore previous instructions', 'R012; DROP', 'r012', 'R052.' . str_repeat('a', 80)] as $code) {
            $c = Trusteed_Eval_Result::sanitize_agent_reason(['reason_category' => 'order_limits', 'rule_code' => $code, 'reason' => 'x', 'next_action' => 'adjust_order']);
            self::assertArrayNotHasKey('rule_code', $c, $code);
        }
    }

    public function test_no_disponible_deriva_checks_unavailable(): void
    {
        $GLOBALS['trusteed_test_wc']->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, 'did:web:agent.example.com');
        [$d, $e] = $this->eval_with(new Trusteed_Eval_Result(Trusteed_Eval_Outcome::ALLOW, 'ok', 200), new \RuntimeException('x'));
        self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $d);
        self::assertSame('checks_unavailable', $e->get_last_agent_reason()['reason_category']);
        self::assertSame('wait_and_retry', $e->get_last_agent_reason()['next_action']);
    }

    public function test_humano_con_averia_pasa_y_sin_motivo(): void
    {
        [$d, $e] = $this->eval_with(new Trusteed_Eval_Result(Trusteed_Eval_Outcome::ALLOW, 'ok', 200), new \RuntimeException('x'));
        self::assertSame('ALLOW', $d);
        self::assertNull($e->get_last_agent_reason());
    }

    public function test_allow_no_arrastra_el_motivo_de_la_evaluacion_anterior(): void
    {
        $enforcer = new Trusteed_Checkout_Enforcer(
            self::client(null, new Trusteed_Eval_Result(Trusteed_Eval_Outcome::BLOCK, 'ok', 200)),
            'merchant-1', 'inst', null, 'enforce', null
        );
        $m = new ReflectionMethod($enforcer, 'run_evaluation');
        $m->setAccessible(true);
        $ctx = static fn(): array => ['cartTotalCents' => 5000, 'currency' => 'EUR', 'itemCount' => 1, 'billingCountry' => 'ES'];
        $m->invoke($enforcer, $ctx);
        self::assertNotNull($enforcer->get_last_agent_reason());
        $p = new ReflectionProperty($enforcer, 'api_client');
        $p->setAccessible(true);
        $p->setValue($enforcer, self::client(null, new Trusteed_Eval_Result(Trusteed_Eval_Outcome::ALLOW, 'ok', 200)));
        $m->invoke($enforcer, $ctx);
        self::assertNull($enforcer->get_last_agent_reason());
    }

    // ── Store API: la RouteException 403 lleva el motivo estructurado ──

    private static function additional_data(Trusteed_Checkout_Enforcer $e): array
    {
        $m = new ReflectionMethod($e, 'block_additional_data');
        $m->setAccessible(true);
        return $m->invoke($e);
    }

    public function test_store_api_additional_data_lleva_los_cuatro_campos_del_servidor(): void
    {
        $reason = ['reason_category' => 'restricted_item', 'rule_code' => 'R012', 'reason' => 'Checkout declined: remove the items.', 'next_action' => 'remove_items'];
        [, $e] = $this->eval_with(new Trusteed_Eval_Result(Trusteed_Eval_Outcome::BLOCK, 'ok', 200, null, Trusteed_Eval_Result::sanitize_agent_reason($reason)));
        self::assertSame($reason, self::additional_data($e));
    }

    public function test_store_api_additional_data_declara_el_fallback_y_no_inventa_regla(): void
    {
        [, $e] = $this->eval_with(new Trusteed_Eval_Result(Trusteed_Eval_Outcome::BLOCK, 'ok', 200));
        $data = self::additional_data($e);
        self::assertSame('merchant_policy', $data['reason_category']);
        self::assertSame('plugin_fallback', $data['reason_source']);
        self::assertArrayNotHasKey('rule_code', $data);
        self::assertArrayNotHasKey('source', $data);
    }

    public function test_store_api_additional_data_vacio_sin_evaluacion(): void
    {
        $e = new Trusteed_Checkout_Enforcer(self::client(null, new Trusteed_Eval_Result(Trusteed_Eval_Outcome::ALLOW, 'ok', 200)), 'merchant-1', 'inst', null, 'enforce', null);
        self::assertSame([], self::additional_data($e));
    }

    // ── Claves opcionales nuevas: human_checkout, blocked_item, published_limit ──

    private const BASE = ['reason_category' => 'restricted_item', 'reason' => 'Checkout declined: remove the items.', 'next_action' => 'remove_items'];

    public function test_claves_nuevas_validas_pasan(): void
    {
        $extra = [
            'human_checkout'  => ['available' => true, 'url' => 'https://shop.example.com/checkout', 'message' => 'Buy as a person, 5 min.'],
            'blocked_item'    => ['kind' => 'category', 'value' => 'gift cards/digital-1'],
            'published_limit' => ['kind' => 'max_cart_value', 'amount_cents' => 50000, 'unit' => 'minor_units_of_store_currency', 'source' => 'merchant_rule'],
        ];
        $c = Trusteed_Eval_Result::sanitize_agent_reason(self::BASE + $extra);
        foreach ($extra as $k => $v) {
            self::assertEquals($v, $c[$k], $k);
        }
        $u = Trusteed_Eval_Result::sanitize_agent_reason(self::BASE + ['published_limit' => ['kind' => 'max_units_per_line', 'units' => 3, 'source' => 'r052']]);
        self::assertSame(3, $u['published_limit']['units']);
    }

    public function test_sin_claves_nuevas_el_resultado_es_el_previo(): void
    {
        self::assertSame(self::BASE, Trusteed_Eval_Result::sanitize_agent_reason(self::BASE));
    }

    /** @return array<string,array{0:string,1:mixed}> */
    public static function claves_nuevas_invalidas(): array
    {
        return array(
            'url javascript'     => array('human_checkout', ['available' => true, 'url' => 'javascript:alert(1)', 'message' => 'x']),
            'url ftp'            => array('human_checkout', ['available' => true, 'url' => 'ftp://a.example.com/x', 'message' => 'x']),
            'available string'   => array('human_checkout', ['available' => 'true', 'message' => 'x']),
            'message con <'      => array('human_checkout', ['available' => false, 'message' => 'a <b>x</b>']),
            'message entidad'    => array('human_checkout', ['available' => false, 'message' => 'a &lt; b']),
            'message largo'      => array('human_checkout', ['available' => false, 'message' => str_repeat('a', 401)]),
            'kind desconocido'   => array('blocked_item', ['kind' => 'brand', 'value' => 'x']),
            'value con marcado'  => array('blocked_item', ['kind' => 'sku', 'value' => '<b>x</b>']),
            'value largo'        => array('blocked_item', ['kind' => 'sku', 'value' => str_repeat('a', 65)]),
            'amount string'      => array('published_limit', ['kind' => 'max_cart_value', 'amount_cents' => '500', 'unit' => 'minor_units_of_store_currency', 'source' => 's']),
            'amount negativo'    => array('published_limit', ['kind' => 'max_cart_value', 'amount_cents' => -1, 'unit' => 'minor_units_of_store_currency', 'source' => 's']),
            'unit incorrecto'    => array('published_limit', ['kind' => 'max_cart_value', 'amount_cents' => 1, 'unit' => 'eur', 'source' => 's']),
            'units float'        => array('published_limit', ['kind' => 'max_units_per_line', 'units' => 2.5, 'source' => 's']),
            'no es array'        => array('published_limit', 'x'),
        );
    }

    /** @dataProvider claves_nuevas_invalidas */
    public function test_clave_nueva_invalida_se_descarta_sola(string $key, $value): void
    {
        $companions = ['human_checkout' => ['available' => false, 'message' => 'ok'], 'blocked_item' => ['kind' => 'sku', 'value' => 'ABC-1'], 'published_limit' => ['kind' => 'max_units_per_line', 'units' => 1, 'source' => 's']];
        $others     = array_diff_key($companions, [$key => 1]);
        $c          = Trusteed_Eval_Result::sanitize_agent_reason(array_merge(self::BASE, $others, [$key => $value]));
        self::assertNotNull($c, 'el bloque sobrevive');
        self::assertArrayNotHasKey($key, $c);
        self::assertSame($others, array_intersect_key($c, $companions), 'las otras claves sobreviven');
        self::assertSame(self::BASE['reason'], $c['reason']);
    }

    public function test_message_de_human_checkout_admite_cifras_y_reason_sigue_sin_ellas(): void
    {
        $c = Trusteed_Eval_Result::sanitize_agent_reason(self::BASE + ['human_checkout' => ['available' => false, 'message' => 'Call 24h support.']]);
        self::assertSame('Call 24h support.', $c['human_checkout']['message']);
        self::assertNull(Trusteed_Eval_Result::sanitize_agent_reason(['reason' => 'Limit 5'] + self::BASE));
    }

    public function test_additional_data_reenvia_las_claves_nuevas(): void
    {
        $reason = self::BASE + ['blocked_item' => ['kind' => 'category', 'value' => 'alcohol'], 'published_limit' => ['kind' => 'max_units_per_line', 'units' => 2, 'source' => 's']];
        [, $e] = $this->eval_with(new Trusteed_Eval_Result(Trusteed_Eval_Outcome::BLOCK, 'ok', 200, null, Trusteed_Eval_Result::sanitize_agent_reason($reason)));
        $data = self::additional_data($e);
        self::assertSame($reason['blocked_item'], $data['blocked_item']);
        self::assertSame($reason['published_limit'], $data['published_limit']);
        self::assertArrayNotHasKey('human_checkout', $data);
    }

    public function test_texto_humano_escapado_con_item_limite_y_enlace(): void
    {
        $t = Trusteed_Eval_Result::human_detail([
            'blocked_item'    => ['kind' => 'category', 'value' => 'a & b'],
            'published_limit' => ['kind' => 'max_cart_value', 'amount_cents' => 12345, 'unit' => 'minor_units_of_store_currency', 'source' => 's'],
            'human_checkout'  => ['available' => true, 'url' => 'https://shop.example.com/c?a=1&b=2', 'message' => 'm'],
        ]);
        self::assertStringContainsString('a &amp; b', $t);
        self::assertStringContainsString('123.45', $t);
        self::assertStringContainsString('https://shop.example.com/c?a=1&amp;b=2', $t);
        self::assertStringContainsString('max. 2 units per line', Trusteed_Eval_Result::human_detail(['published_limit' => ['kind' => 'max_units_per_line', 'units' => 2, 'source' => 's']]));
        self::assertSame('No online checkout.', Trusteed_Eval_Result::human_detail(['human_checkout' => ['available' => false, 'message' => 'No online checkout.']]));
        self::assertSame('', Trusteed_Eval_Result::human_detail(self::BASE));
    }

    // ── limit_exceeded ──

    private const LIMIT_EXCEEDED = [
        'rule' => 'R038', 'rule_code' => 'R038.max-items-per-order', 'rule_name' => 'Máximo de artículos por pedido',
        'kind' => 'max_items_per_order', 'limit' => 5, 'observed' => 10, 'unit' => 'units',
        'message' => 'Regla R038 (Máximo de artículos por pedido): el comercio acepta como máximo 5 artículos por pedido de un agente; este pedido lleva 10.',
    ];

    public function test_limit_exceeded_valido_pasa_y_permite_cifras_en_reason(): void
    {
        $c = Trusteed_Eval_Result::sanitize_agent_reason(['reason_category' => 'order_limits', 'reason' => 'Max 5 items per order.', 'next_action' => 'adjust_order', 'rule_code' => 'R038.max-items-per-order', 'limit_exceeded' => self::LIMIT_EXCEEDED]);
        self::assertSame(self::LIMIT_EXCEEDED, $c['limit_exceeded']);
        self::assertSame('Max 5 items per order.', $c['reason']);
        self::assertSame('R038.max-items-per-order', $c['rule_code']);
    }

    /** @return array<string,array{0:string,1:mixed}> */
    public static function limit_exceeded_invalidos(): array
    {
        return array(
            'rule mal'         => array('rule', 'R38'),
            'rule_code mal'    => array('rule_code', 'R038'),
            'rule_code mayus'  => array('rule_code', 'R038.Max'),
            'rule_code largo'  => array('rule_code', 'R038.' . str_repeat('a', 61)),
            'rule_name vacio'  => array('rule_name', ''),
            'rule_name largo'  => array('rule_name', str_repeat('a', 81)),
            'kind desconocido' => array('kind', 'max_x'),
            'limit string'     => array('limit', '5'),
            'limit negativo'   => array('limit', -1),
            'observed float'   => array('observed', 1.5),
            'unit incorrecto'  => array('unit', 'eur'),
            'message vacio'    => array('message', ''),
            'message largo'    => array('message', str_repeat('a', 401)),
            'message con <'    => array('message', 'a <b>10</b>'),
        );
    }

    /** @dataProvider limit_exceeded_invalidos */
    public function test_limit_exceeded_con_un_campo_invalido_se_descarta_entero(string $field, $value): void
    {
        $c = Trusteed_Eval_Result::sanitize_agent_reason(self::BASE + ['blocked_item' => ['kind' => 'sku', 'value' => 'ABC-1'], 'limit_exceeded' => array_merge(self::LIMIT_EXCEEDED, [$field => $value])]);
        self::assertNotNull($c);
        self::assertArrayNotHasKey('limit_exceeded', $c);
        self::assertSame(['kind' => 'sku', 'value' => 'ABC-1'], $c['blocked_item']);
    }

    public function test_sin_limit_exceeded_valido_reason_con_cifras_sigue_descartando_el_bloque(): void
    {
        $r = ['reason_category' => 'order_limits', 'reason' => 'Max 5 items.', 'next_action' => 'adjust_order'];
        self::assertNull(Trusteed_Eval_Result::sanitize_agent_reason($r));
        self::assertNull(Trusteed_Eval_Result::sanitize_agent_reason($r + ['limit_exceeded' => array_merge(self::LIMIT_EXCEEDED, ['limit' => -1])]));
    }

    public function test_limit_exceeded_se_reenvia_y_se_muestra_escapado(): void
    {
        $reason = self::BASE + ['limit_exceeded' => ['message' => 'Regla R038 (A & B): como máximo 5; lleva 10.'] + self::LIMIT_EXCEEDED];
        [, $e] = $this->eval_with(new Trusteed_Eval_Result(Trusteed_Eval_Outcome::BLOCK, 'ok', 200, null, Trusteed_Eval_Result::sanitize_agent_reason($reason)));
        self::assertEquals($reason['limit_exceeded'], self::additional_data($e)['limit_exceeded']);
        self::assertSame('Regla R038 (A &amp; B): como máximo 5; lleva 10.', Trusteed_Eval_Result::human_detail($reason));
    }
}
