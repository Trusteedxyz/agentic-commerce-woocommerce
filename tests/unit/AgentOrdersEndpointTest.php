<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/AgentOrdersFakes.php';
require_once __DIR__ . '/../../includes/class-agent-request-auth.php';
require_once __DIR__ . '/../../includes/class-payment-verifier.php';
require_once __DIR__ . '/../../includes/class-payref-lock.php';
require_once __DIR__ . '/../../includes/class-cart-bridge.php';
require_once __DIR__ . '/../../includes/class-enforcement-api-client.php';
require_once __DIR__ . '/../../includes/class-offline-safety-valve-evaluator.php';
require_once __DIR__ . '/../../includes/class-agent-event-webhook.php';
require_once __DIR__ . '/../../includes/class-multi-add-handler.php';
require_once __DIR__ . '/../../includes/class-cart-signals.php';
require_once __DIR__ . '/../../includes/class-checkout-enforcer.php';
require_once __DIR__ . '/../../includes/class-agent-orders-endpoint.php';

if (!function_exists('register_rest_route')) {
    function register_rest_route($ns, $route, $args = [])
    {
        $GLOBALS['__trusteed_test_routes'][] = [$ns, $route, $args];
        return true;
    }
}

/**
 * Endpoints `trusteed/v1/{quote,orders,orders/{id}/paid}` (plan 2026-10-05, tareas 4-6).
 *
 * Se prueba la decisión del endpoint sobre un puerto Woo falso: firma, opción
 * apagada, idempotencia, desajuste de total, reglas locales y pagado.
 */
final class AgentOrdersEndpointTest extends TestCase
{
    private const SECRET = 'secreto-de-instalacion-de-prueba';
    private const NOW    = 1790000000;

    private Trusteed_Test_Woo_Port $port;
    private Trusteed_Agent_Orders_Endpoint $endpoint;
    /** Respuestas del PSP falso, por URL. @var array<string,array{status:int,body:string}> */
    private array $psp = [];

    protected function setUp(): void
    {
        $GLOBALS['__amcp_transients_store'] = [];
        $GLOBALS['__amcp_transient_expiry'] = [];
        $GLOBALS['__amcp_options_store']    = ['trusteed_agent_orders_enabled' => 'yes'];
        $GLOBALS['__trusteed_test_routes']  = [];
        $this->port     = new Trusteed_Test_Woo_Port();
        // PSP falso que confirma el cobro del pedido de prueba (6655 EUR) en los dos rails.
        $this->psp = [
            'https://api.stripe.com/v1/payment_intents/pi_3Abc'                                => ['status' => 200, 'body' => '{"status":"succeeded","amount_received":6655,"currency":"eur"}'],
            'https://api-m.paypal.com/v1/oauth2/token'                                         => ['status' => 200, 'body' => '{"access_token":"tok"}'],
            'https://api-m.paypal.com/v2/payments/captures/8TW16685UK546374C'                  => ['status' => 200, 'body' => '{"status":"COMPLETED","amount":{"value":"66.55","currency_code":"EUR"}}'],
        ];
        $this->endpoint = new Trusteed_Agent_Orders_Endpoint($this->port, fn(): string => self::SECRET, fn(): int => self::NOW, $this->verifier());
    }

    private function verifier(): Trusteed_Payment_Verifier
    {
        return new Trusteed_Payment_Verifier(
            fn(string $url, array $args): array => $this->psp[$url] ?? ['status' => 0, 'body' => ''],
            fn(): string => 'sk_' . 'test_' . 'AbCdEf0123456789AbCdEf01',
            fn(): ?array => ['client_id' => 'cid', 'client_secret' => 'sec', 'mode' => 'production'],
            fn(string $ref, int $id): bool => false
        );
    }

    /** @param array<string,mixed> $body */
    private function signed(array $body, array $params = [], ?int $t = null, string $secret = self::SECRET): Trusteed_Test_Rest_Request
    {
        $raw = json_encode($body);
        $t   = $t ?? self::NOW;
        $sig = 't=' . $t . ',v1=' . hash_hmac('sha256', $t . '.' . $raw, $secret);
        return new Trusteed_Test_Rest_Request($raw, ['x-trusteed-signature' => $sig, 'x_trusteed_signature' => $sig], $params);
    }

    private function orderBody(array $over = []): array
    {
        return array_merge([
            'trusteed_order_id'   => 'ord_123',
            'checkout_session_id' => 'cs_456',
            'agent_did'           => 'did:web:agent.example',
            'lines'               => [['product_id' => 12, 'variation_id' => 0, 'qty' => 2]],
            'shipping_address'    => ['country' => 'ES', 'postcode' => '28001', 'city' => 'Madrid', 'address_1' => 'Calle 1'],
            'shipping_method_id'  => 'flat_rate:1',
            'coupons'             => [],
            'expected_total_cents' => 6655,
        ], $over);
    }

    // ── Opción apagada ────────────────────────────────────────────────────────

    public function test_con_la_opcion_apagada_no_se_registra_ninguna_ruta(): void
    {
        $GLOBALS['__amcp_options_store'] = [];
        $this->endpoint->register_routes();
        $this->assertSame([], $GLOBALS['__trusteed_test_routes']);
    }

    public function test_con_la_opcion_en_yes_se_registran_las_tres_rutas(): void
    {
        $this->endpoint->register_routes();
        $routes = array_map(fn($r) => $r[0] . $r[1], $GLOBALS['__trusteed_test_routes']);
        $this->assertContains('trusteed/v1/quote', $routes);
        $this->assertContains('trusteed/v1/orders', $routes);
        $this->assertContains('trusteed/v1/orders/(?P<id>\d+)/paid', $routes);
    }

    public function test_si_la_opcion_se_apaga_tras_registrar_el_handler_responde_404(): void
    {
        $GLOBALS['__amcp_options_store'] = [];
        $res = $this->endpoint->handle_quote($this->signed(['lines' => []]));
        $this->assertSame(404, $res->get_status());
        $this->assertSame([], $this->port->calls);
    }

    // ── Autenticación ─────────────────────────────────────────────────────────

    public function test_firma_valida_autoriza(): void
    {
        $this->assertTrue($this->endpoint->authorize($this->signed(['x' => 1])));
    }

    public function test_firma_invalida_da_401_generico(): void
    {
        $r = $this->endpoint->authorize($this->signed(['x' => 1], [], null, 'otro-secreto'));
        $this->assertInstanceOf(WP_Error::class, $r);
        $this->assertSame(401, $r->get_error_data()['status']);
        $this->assertStringNotContainsString('signature', strtolower($r->get_error_message()));
    }

    public function test_firma_caducada_da_401(): void
    {
        $r = $this->endpoint->authorize($this->signed(['x' => 1], [], self::NOW - 400));
        $this->assertInstanceOf(WP_Error::class, $r);
        $this->assertSame(401, $r->get_error_data()['status']);
    }

    public function test_sin_cabecera_da_401(): void
    {
        $r = $this->endpoint->authorize(new Trusteed_Test_Rest_Request('{}'));
        $this->assertInstanceOf(WP_Error::class, $r);
    }

    public function test_replay_de_la_misma_peticion_da_401_la_segunda_vez(): void
    {
        $req = $this->signed(['x' => 1]);
        $this->assertTrue($this->endpoint->authorize($req));
        $r = $this->endpoint->authorize($req);
        $this->assertInstanceOf(WP_Error::class, $r);
        $this->assertSame(401, $r->get_error_data()['status']);
    }

    public function test_sin_secreto_configurado_da_401(): void
    {
        $e = new Trusteed_Agent_Orders_Endpoint($this->port, fn(): string => '', fn(): int => self::NOW);
        $this->assertInstanceOf(WP_Error::class, $e->authorize($this->signed(['x' => 1])));
    }

    // ── /quote ────────────────────────────────────────────────────────────────

    public function test_quote_devuelve_totales_y_un_quote_hash_estable(): void
    {
        $body = ['lines' => [['product_id' => 12, 'variation_id' => 0, 'qty' => 2]], 'shipping_address' => ['country' => 'ES']];
        $a = $this->endpoint->handle_quote($this->signed($body));
        $b = $this->endpoint->handle_quote($this->signed($body));
        $this->assertSame(200, $a->get_status());
        $this->assertSame(6655, $a->get_data()['total_cents']);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $a->get_data()['quote_hash']);
        $this->assertSame($a->get_data()['quote_hash'], $b->get_data()['quote_hash']);
    }

    public function test_quote_hash_cambia_si_cambia_un_importe(): void
    {
        $body = ['lines' => [['product_id' => 12, 'variation_id' => 0, 'qty' => 2]]];
        $a = $this->endpoint->handle_quote($this->signed($body));
        $this->port->quote_result['tax_cents'] = 1;
        $b = $this->endpoint->handle_quote($this->signed($body));
        $this->assertNotSame($a->get_data()['quote_hash'], $b->get_data()['quote_hash']);
    }

    public function test_quote_sin_lineas_es_400(): void
    {
        $res = $this->endpoint->handle_quote($this->signed(['lines' => []]));
        $this->assertSame(400, $res->get_status());
        $this->assertNotContains('quote', $this->port->calls);
    }

    public function test_quote_propaga_el_error_de_stock_del_puerto(): void
    {
        $this->port->quote_result = new WP_Error('out_of_stock', 'Sin stock', ['status' => 409]);
        $res = $this->endpoint->handle_quote($this->signed(['lines' => [['product_id' => 12, 'variation_id' => 0, 'qty' => 99]]]));
        $this->assertSame(409, $res->get_status());
        $this->assertSame('out_of_stock', $res->get_data()['error']['code']);
    }

    // ── /orders ───────────────────────────────────────────────────────────────

    public function test_orders_crea_el_pedido_y_responde_la_forma_del_contrato(): void
    {
        $res = $this->endpoint->handle_create_order($this->signed($this->orderBody()));
        $this->assertSame(201, $res->get_status());
        $this->assertSame(
            ['order_id' => 100, 'order_number' => '100', 'total_cents' => 6655, 'currency' => 'EUR', 'status' => 'pending'],
            $res->get_data()
        );
        $this->assertSame('did:web:agent.example', $this->port->rules_agent_did);
    }

    public function test_orders_pending_con_enlace_de_pago_lo_devuelve_como_payment_url(): void
    {
        $this->port->payment_url = 'https://tienda.example/checkout/order-pay/100/?pay_for_order=true&key=wc_order_x';
        $res = $this->endpoint->handle_create_order($this->signed($this->orderBody()));
        $this->assertSame(201, $res->get_status());
        $this->assertSame($this->port->payment_url, $res->get_data()['payment_url']);
    }

    public function test_orders_sin_enlace_de_pago_no_lleva_la_clave(): void
    {
        $res = $this->endpoint->handle_create_order($this->signed($this->orderBody()));
        $this->assertArrayNotHasKey('payment_url', $res->get_data());
    }

    public function test_orders_con_enlace_vacio_o_no_texto_no_lleva_la_clave(): void
    {
        foreach (['', 123] as $i => $raw) {
            $id = $this->createdOrderId();
            $this->port->orders[$id]['payment_url'] = $raw;
            $res = $this->endpoint->handle_create_order($this->signed($this->orderBody(['trusteed_order_id' => $this->port->orders[$id]['trusteed_order_id']])));
            $this->assertArrayNotHasKey('payment_url', $res->get_data(), 'enlace: ' . json_encode($raw));
        }
    }

    public function test_pedido_ya_pagado_no_expone_enlace_de_pago_aunque_el_puerto_lo_traiga(): void
    {
        $id = $this->createdOrderId();
        $this->port->orders[$id]['payment_url'] = 'https://tienda.example/checkout/order-pay/' . $id . '/?key=k';
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $this->assertSame(200, $res->get_status());
        $this->assertArrayNotHasKey('payment_url', $res->get_data());
    }

    public function test_reenvia_el_nivel_valido_al_evaluar(): void
    {
        $res = $this->endpoint->handle_create_order($this->signed($this->orderBody(['agent_identity_level' => 'verified_signature'])));
        $this->assertSame(201, $res->get_status());
        $this->assertSame('verified_signature', $this->port->rules_identity_level);
    }

    public function test_descarta_un_nivel_fuera_del_vocabulario(): void
    {
        foreach (['root', '', 7, ['verified_signature'], 'VERIFIED_SIGNATURE'] as $i => $raw) {
            $this->port->rules_identity_level = 'sentinel';
            $this->endpoint->handle_create_order($this->signed($this->orderBody([
                'trusteed_order_id'    => 'ord_bad_' . $i,
                'agent_identity_level' => $raw,
            ])));
            $this->assertNull($this->port->rules_identity_level, 'nivel inválido: ' . json_encode($raw));
        }
    }

    public function test_sin_nivel_el_puerto_recibe_null(): void
    {
        $this->endpoint->handle_create_order($this->signed($this->orderBody()));
        $this->assertNull($this->port->rules_identity_level);
    }

    public function test_orders_es_idempotente_por_trusteed_order_id(): void
    {
        $a = $this->endpoint->handle_create_order($this->signed($this->orderBody()));
        $b = $this->endpoint->handle_create_order($this->signed($this->orderBody(), [], self::NOW - 1));
        $this->assertSame(200, $b->get_status());
        $this->assertSame($a->get_data()['order_id'], $b->get_data()['order_id']);
        $this->assertCount(1, $this->port->orders);
        $this->assertSame(1, count(array_keys($this->port->calls, 'create', true)));
    }

    public function test_orders_con_total_distinto_da_409_con_ambos_totales_y_borra_el_pedido(): void
    {
        $res = $this->endpoint->handle_create_order($this->signed($this->orderBody(['expected_total_cents' => 6000])));
        $this->assertSame(409, $res->get_status());
        $this->assertSame('total_mismatch', $res->get_data()['error']['code']);
        $this->assertSame(6000, $res->get_data()['error']['expected_total_cents']);
        $this->assertSame(6655, $res->get_data()['error']['actual_total_cents']);
        $this->assertSame([], $this->port->orders);
        $this->assertNotContains('rules', $this->port->calls);
    }

    public function test_orders_bloqueado_por_las_reglas_da_422_y_borra_el_pedido(): void
    {
        $this->port->rules_decision = 'BLOCK';
        $res = $this->endpoint->handle_create_order($this->signed($this->orderBody()));
        $this->assertSame(422, $res->get_status());
        $this->assertSame('rule_blocked', $res->get_data()['error']['code']);
        $this->assertSame([], $this->port->orders);
    }

    public function test_orders_bloqueado_lleva_el_motivo_del_servidor_en_el_422(): void
    {
        $this->port->rules_decision     = 'BLOCK';
        $this->port->rules_agent_reason = [
            'reason_category' => 'order_limits',
            'rule_code'       => 'R018',
            'reason'          => 'Checkout declined: adjust the order.',
            'next_action'     => 'adjust_order',
        ];
        $res = $this->endpoint->handle_create_order($this->signed($this->orderBody()));
        $err = $res->get_data()['error'];
        $this->assertSame(422, $res->get_status());
        $this->assertSame('rule_blocked', $err['code']);
        $this->assertSame('order_limits', $err['reason_category']);
        $this->assertSame('R018', $err['rule_code']);
        $this->assertSame('adjust_order', $err['next_action']);
        $this->assertSame('Checkout declined: adjust the order.', $err['reason']);
        $this->assertSame($err['reason'], $err['public_reason']);
    }

    public function test_orders_bloqueado_con_motivo_derivado_por_el_plugin_lo_declara(): void
    {
        $this->port->rules_decision     = 'BLOCK';
        $this->port->rules_agent_reason = [
            'reason_category' => 'merchant_policy',
            'reason'          => 'x',
            'next_action'     => 'escalate_human',
            'source'          => 'plugin_fallback',
        ];
        $err = $this->endpoint->handle_create_order($this->signed($this->orderBody()))->get_data()['error'];
        $this->assertSame('plugin_fallback', $err['reason_source']);
        $this->assertArrayNotHasKey('source', $err);
    }

    public function test_orders_bloqueado_con_motivo_del_servidor_no_lleva_reason_source(): void
    {
        $this->port->rules_decision     = 'BLOCK';
        $this->port->rules_agent_reason = ['reason_category' => 'order_limits', 'reason' => 'x', 'next_action' => 'adjust_order'];
        $err = $this->endpoint->handle_create_order($this->signed($this->orderBody()))->get_data()['error'];
        $this->assertArrayNotHasKey('reason_source', $err);
    }

    public function test_orders_bloqueado_reenvia_las_claves_nuevas_en_el_422(): void
    {
        $this->port->rules_decision     = 'BLOCK';
        $this->port->rules_agent_reason = [
            'reason_category' => 'order_limits',
            'reason'          => 'x',
            'next_action'     => 'adjust_order',
            'blocked_item'    => ['kind' => 'sku', 'value' => 'ABC-1'],
            'published_limit' => ['kind' => 'max_units_per_line', 'units' => 2, 'source' => 's'],
            'human_checkout'  => ['available' => false, 'message' => 'm'],
        ];
        $err = $this->endpoint->handle_create_order($this->signed($this->orderBody()))->get_data()['error'];
        $this->assertSame(['kind' => 'sku', 'value' => 'ABC-1'], $err['blocked_item']);
        $this->assertSame(2, $err['published_limit']['units']);
        $this->assertFalse($err['human_checkout']['available']);
    }

    public function test_orders_bloqueado_reenvia_limit_exceeded_en_el_422(): void
    {
        $le = ['rule' => 'R038', 'rule_code' => 'R038.max-items-per-order', 'rule_name' => 'n', 'kind' => 'max_items_per_order', 'limit' => 5, 'observed' => 10, 'unit' => 'units', 'message' => 'Regla R038: máximo 5; lleva 10.'];
        $this->port->rules_decision     = 'BLOCK';
        $this->port->rules_agent_reason = ['reason_category' => 'order_limits', 'reason' => 'x', 'next_action' => 'adjust_order', 'limit_exceeded' => $le];
        $err = $this->endpoint->handle_create_order($this->signed($this->orderBody()))->get_data()['error'];
        $this->assertSame($le, $err['limit_exceeded']);
    }

    public function test_orders_bloqueado_sin_motivo_del_servidor_degrada_sin_campos_inventados(): void
    {
        $this->port->rules_decision = 'BLOCK';
        $res = $this->endpoint->handle_create_order($this->signed($this->orderBody()));
        $err = $res->get_data()['error'];
        $this->assertSame('rule_blocked', $err['code']);
        $this->assertArrayNotHasKey('reason_category', $err);
        $this->assertArrayNotHasKey('public_reason', $err);
    }

    public function test_un_wp_error_sin_codigo_no_se_llama_rule_blocked(): void
    {
        $this->port->quote_result = new WP_Error('', 'raro');
        $res = $this->endpoint->handle_quote($this->signed($this->orderBody()));
        $this->assertSame('order_invalid', $res->get_data()['error']['code']);
    }

    public function test_orders_con_el_evaluador_no_disponible_cierra_con_503_y_borra_el_pedido(): void
    {
        $this->port->rules_decision = 'UNAVAILABLE';
        $res = $this->endpoint->handle_create_order($this->signed($this->orderBody()));
        $this->assertSame(503, $res->get_status());
        $this->assertSame([], $this->port->orders);
    }

    public function test_orders_con_cuerpo_incompleto_da_400_y_no_crea_nada(): void
    {
        $res = $this->endpoint->handle_create_order($this->signed($this->orderBody(['trusteed_order_id' => ''])));
        $this->assertSame(400, $res->get_status());
        $res2 = $this->endpoint->handle_create_order($this->signed($this->orderBody(['expected_total_cents' => 'x'])));
        $this->assertSame(400, $res2->get_status());
        $this->assertSame([], $this->port->orders);
    }

    // ── /orders/{id}/paid ─────────────────────────────────────────────────────

    private function createdOrderId(): int
    {
        $this->endpoint->handle_create_order($this->signed($this->orderBody()));
        return 100;
    }

    private function paidBody(array $over = []): array
    {
        return array_merge(['payment_reference' => 'pi_3Abc', 'amount_cents' => 6655, 'currency' => 'EUR'], $over);
    }

    public function test_paid_con_importe_correcto_completa_el_pago(): void
    {
        $id  = $this->createdOrderId();
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id], self::NOW));
        $this->assertSame(200, $res->get_status());
        $this->assertSame('processing', $this->port->orders[$id]['status']);
        $this->assertSame('pi_3Abc', $this->port->orders[$id]['transaction_id']);
    }

    // ── verificación del cobro con el PSP (C-A3) ──────────────────────────────

    public function test_paid_with_unverified_reference_holds_for_review_and_does_not_complete(): void
    {
        $id = $this->createdOrderId();
        $this->psp['https://api.stripe.com/v1/payment_intents/pi_3Abc'] = ['status' => 200, 'body' => '{"status":"requires_capture","amount_received":0,"currency":"eur"}'];
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $this->assertSame(409, $res->get_status());
        $this->assertSame('payment_not_verified', $res->get_data()['error']['code']);
        $this->assertContains('hold', $this->port->calls);
        $this->assertStringContainsString('not_succeeded', $this->port->orders[$id]['note']);
        $this->assertNotContains('complete', $this->port->calls);
    }

    public function test_paid_with_psp_unreachable_holds_and_answers_503(): void
    {
        $id = $this->createdOrderId();
        $this->psp = [];
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $this->assertSame(503, $res->get_status());
        $this->assertSame('payment_verification_unavailable', $res->get_data()['error']['code']);
        $this->assertSame('on-hold', $this->port->orders[$id]['status']);
        $this->assertNotContains('complete', $this->port->calls);
    }

    public function test_paid_with_psp_unreachable_can_be_retried_and_then_completes(): void
    {
        $id   = $this->createdOrderId();
        $good = $this->psp;
        $this->psp = [];
        $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $this->psp = $good;
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id], self::NOW - 1));
        $this->assertSame(200, $res->get_status());
        $this->assertSame('processing', $this->port->orders[$id]['status']);
    }

    public function test_paid_verified_completes(): void
    {
        $id  = $this->createdOrderId();
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $this->assertSame(200, $res->get_status());
        $this->assertContains('complete', $this->port->calls);
    }

    public function test_paid_without_verifier_answers_503(): void
    {
        $endpoint = new Trusteed_Agent_Orders_Endpoint($this->port, fn(): string => self::SECRET, fn(): int => self::NOW);
        $id       = $this->createdOrderId();
        $res      = $endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $this->assertSame(503, $res->get_status());
        $this->assertSame('payment_verification_unavailable', $res->get_data()['error']['code']);
        $this->assertNotContains('complete', $this->port->calls);
    }

    public function test_paid_with_amount_mismatch_at_the_psp_does_not_complete(): void
    {
        $id = $this->createdOrderId();
        $this->psp['https://api.stripe.com/v1/payment_intents/pi_3Abc'] = ['status' => 200, 'body' => '{"status":"succeeded","amount_received":1,"currency":"eur"}'];
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $this->assertSame(409, $res->get_status());
        $this->assertNotContains('complete', $this->port->calls);
    }

    public function test_paid_without_credentials_answers_503_not_a_permission(): void
    {
        $id = $this->createdOrderId();
        $endpoint = new Trusteed_Agent_Orders_Endpoint(
            $this->port,
            fn(): string => self::SECRET,
            fn(): int => self::NOW,
            new Trusteed_Payment_Verifier(fn(string $u, array $a): array => ['status' => 0, 'body' => ''], fn(): string => '', fn(): ?array => null, fn(string $r, int $i): bool => false)
        );
        $res = $endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $this->assertSame(503, $res->get_status());
        $this->assertNotContains('complete', $this->port->calls);
    }

    public function test_paid_with_reference_reused_by_another_order_is_not_verified(): void
    {
        $id = $this->createdOrderId();
        $endpoint = new Trusteed_Agent_Orders_Endpoint(
            $this->port,
            fn(): string => self::SECRET,
            fn(): int => self::NOW,
            new Trusteed_Payment_Verifier(fn(string $u, array $a): array => $this->psp[$u] ?? ['status' => 0, 'body' => ''], fn(): string => 'sk_' . 'test_x', fn(): ?array => null, fn(string $r, int $i): bool => true)
        );
        $res = $endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $this->assertSame(409, $res->get_status());
        $this->assertStringContainsString('reference_reused', $this->port->orders[$id]['note']);
        $this->assertNotContains('complete', $this->port->calls);
    }

    // ── ronda 1: referencia en _stripe_intent_id y candado por referencia ─────

    public function test_reference_in_use_looks_at_stripe_intent_id_meta(): void
    {
        $seen   = [];
        $finder = function (array $q) use (&$seen): array {
            $seen[] = $q;
            return isset($q['meta_query'][0]['key']) && '_stripe_intent_id' === $q['meta_query'][0]['key'] && 'pi_X' === $q['meta_query'][0]['value'] ? [77] : [];
        };
        $this->assertTrue(Trusteed_Agent_Orders_Endpoint::reference_in_use('pi_X', 5, $finder));
        $this->assertSame([5], $seen[1]['exclude']);
        $this->assertFalse(Trusteed_Agent_Orders_Endpoint::reference_in_use('pi_Y', 5, $finder));
    }

    public function test_reference_in_use_by_foreign_stripe_intent_id_is_reference_reused_without_http(): void
    {
        $id    = $this->createdOrderId();
        $calls = 0;
        $finder = fn(array $q): array => isset($q['meta_query']) ? [77] : [];
        $endpoint = new Trusteed_Agent_Orders_Endpoint(
            $this->port,
            fn(): string => self::SECRET,
            fn(): int => self::NOW,
            new Trusteed_Payment_Verifier(
                function (string $u, array $a) use (&$calls): array { $calls++; return ['status' => 0, 'body' => '']; },
                fn(): string => 'sk_' . 'test_x',
                fn(): ?array => null,
                fn(string $r, int $i): bool => Trusteed_Agent_Orders_Endpoint::reference_in_use($r, $i, $finder)
            )
        );
        $res = $endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $this->assertSame(409, $res->get_status());
        $this->assertStringContainsString('reference_reused', $this->port->orders[$id]['note']);
        $this->assertSame(0, $calls);
    }

    private function lockName(): string
    {
        return 'trusteed_payref_lock_' . sha1(':pi_3Abc');
    }

    public function test_paid_with_lock_taken_answers_503_without_completing(): void
    {
        $id = $this->createdOrderId();
        $GLOBALS['__amcp_options_store'][$this->lockName()] = time() . ':ajeno';
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $this->assertSame(503, $res->get_status());
        $this->assertSame('payment_verification_unavailable', $res->get_data()['error']['code']);
        $this->assertNotContains('complete', $this->port->calls);
        $this->assertArrayHasKey($this->lockName(), $GLOBALS['__amcp_options_store'], 'el candado ajeno no se libera');
    }

    public function test_paid_with_expired_lock_proceeds_and_releases(): void
    {
        $id = $this->createdOrderId();
        $GLOBALS['__amcp_options_store'][$this->lockName()] = (time() - 121) . ':viejo';
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $this->assertSame(200, $res->get_status());
        $this->assertArrayNotHasKey($this->lockName(), $GLOBALS['__amcp_options_store']);
    }

    public function test_lock_is_released_after_success_and_after_verifier_error(): void
    {
        $id = $this->createdOrderId();
        $this->psp = [];
        $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $this->assertArrayNotHasKey($this->lockName(), $GLOBALS['__amcp_options_store']);
        $this->psp['https://api.stripe.com/v1/payment_intents/pi_3Abc'] = ['status' => 200, 'body' => '{"status":"requires_capture"}'];
        $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id], self::NOW - 1));
        $this->assertArrayNotHasKey($this->lockName(), $GLOBALS['__amcp_options_store']);
    }

    public function test_owner_release_does_not_delete_a_lock_taken_over_by_another(): void
    {
        $name = 'trusteed_payref_lock_x';
        $a    = Trusteed_Payref_Lock::acquire($name);
        $this->assertNotNull($a);
        $GLOBALS['__amcp_options_store'][$name] = (time() - 200) . ':' . 'a-caducado'; // A tarda más de 120 s
        $b = Trusteed_Payref_Lock::acquire($name);
        $this->assertNotNull($b);
        Trusteed_Payref_Lock::release($name, $a); // A termina tarde
        $this->assertSame($b, $GLOBALS['__amcp_options_store'][$name]);
        Trusteed_Payref_Lock::release($name, $b);
        $this->assertArrayNotHasKey($name, $GLOBALS['__amcp_options_store']);
    }

    public function test_two_takeovers_of_the_same_expired_value_only_one_wins(): void
    {
        $name = 'trusteed_payref_lock_y';
        $old  = (time() - 200) . ':viejo';
        $GLOBALS['__amcp_options_store'][$name] = $old;
        $b = Trusteed_Payref_Lock::take_over($name, $old);
        $c = Trusteed_Payref_Lock::take_over($name, $old);
        $this->assertNotNull($b);
        $this->assertNull($c);
        $this->assertSame($b, $GLOBALS['__amcp_options_store'][$name]);
    }

    public function test_release_after_verifier_error_still_happens_for_the_owner(): void
    {
        $id = $this->createdOrderId();
        $this->psp = [];
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $this->assertSame(503, $res->get_status());
        $this->assertArrayNotHasKey($this->lockName(), $GLOBALS['__amcp_options_store']);
    }

    public function test_two_acquires_on_the_same_free_key_only_the_first_gets_a_token(): void
    {
        $name = 'trusteed_payref_lock_z';
        $first  = Trusteed_Payref_Lock::acquire($name);
        $second = Trusteed_Payref_Lock::acquire($name);
        $this->assertNotNull($first);
        $this->assertNull($second);
        $this->assertSame($first, $GLOBALS['__amcp_options_store'][$name]);
    }

    // ── rail PayPal (plan 2026-10-14) ─────────────────────────────────────────

    public function test_paid_con_paypal_acepta_el_capture_id_y_pasa_el_metodo_al_puerto(): void
    {
        $id  = $this->createdOrderId();
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(['payment_reference' => '8TW16685UK546374C', 'payment_method' => 'paypal']), ['id' => (string) $id], self::NOW));
        $this->assertSame(200, $res->get_status());
        $this->assertSame('processing', $this->port->orders[$id]['status']);
        $this->assertSame('8TW16685UK546374C', $this->port->orders[$id]['transaction_id']);
        $this->assertSame('paypal', $this->port->orders[$id]['payment_method']);
    }

    public function test_paid_sin_metodo_sigue_siendo_stripe_y_no_pasa_metodo(): void
    {
        $id = $this->createdOrderId();
        $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id], self::NOW));
        $this->assertSame('', $this->port->orders[$id]['payment_method'] ?? '');
    }

    public function test_paid_un_capture_id_de_paypal_sin_metodo_da_400(): void
    {
        $id  = $this->createdOrderId();
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(['payment_reference' => '8TW16685UK546374C']), ['id' => (string) $id]));
        $this->assertSame(400, $res->get_status());
        $this->assertNotContains('complete', $this->port->calls);
    }

    public function test_paid_con_metodo_paypal_y_referencia_de_stripe_da_400(): void
    {
        $id  = $this->createdOrderId();
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(['payment_method' => 'paypal']), ['id' => (string) $id]));
        $this->assertSame(400, $res->get_status());
        $this->assertNotContains('complete', $this->port->calls);
    }

    public function test_paid_con_un_metodo_desconocido_da_400(): void
    {
        $id  = $this->createdOrderId();
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(['payment_method' => 'bitcoin']), ['id' => (string) $id]));
        $this->assertSame(400, $res->get_status());
        $this->assertNotContains('complete', $this->port->calls);
    }

    /**
     * @dataProvider referencias_paypal_invalidas
     */
    public function test_paid_con_paypal_rechaza_referencias_mal_formadas(string $ref): void
    {
        $id  = $this->createdOrderId();
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(['payment_reference' => $ref, 'payment_method' => 'paypal']), ['id' => (string) $id]));
        $this->assertSame(400, $res->get_status());
        $this->assertNotContains('complete', $this->port->calls);
    }

    /** @return array<string,array{string}> */
    public static function referencias_paypal_invalidas(): array
    {
        return array(
            'corta'         => array('ABC123'),
            'minusculas'    => array('8tw16685uk546374c'),
            'con guion'     => array('8TW16685-UK546374C'),
            'demasiado larga' => array(str_repeat('A', 31)),
            'con espacios'  => array('8TW16685UK 546374C'),
        );
    }

    public function test_paid_con_paypal_es_idempotente_con_la_misma_referencia(): void
    {
        $id   = $this->createdOrderId();
        $body = $this->paidBody(['payment_reference' => '8TW16685UK546374C', 'payment_method' => 'paypal']);
        $this->endpoint->handle_paid($this->signed($body, ['id' => (string) $id]));
        $res = $this->endpoint->handle_paid($this->signed($body, ['id' => (string) $id], self::NOW - 1));
        $this->assertSame(200, $res->get_status());
        $this->assertTrue($res->get_data()['already_paid']);
        $this->assertSame(1, count(array_keys($this->port->calls, 'complete', true)));
    }

    public function test_paid_con_importe_incorrecto_deja_on_hold_con_nota_y_da_409(): void
    {
        $id  = $this->createdOrderId();
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(['amount_cents' => 1]), ['id' => (string) $id]));
        $this->assertSame(409, $res->get_status());
        $this->assertSame('on-hold', $this->port->orders[$id]['status']);
        $this->assertNotEmpty($this->port->orders[$id]['note']);
        $this->assertNotContains('complete', $this->port->calls);
    }

    public function test_paid_con_moneda_incorrecta_tambien_da_409(): void
    {
        $id  = $this->createdOrderId();
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(['currency' => 'USD']), ['id' => (string) $id]));
        $this->assertSame(409, $res->get_status());
        $this->assertNotContains('complete', $this->port->calls);
    }

    public function test_paid_es_idempotente_con_la_misma_referencia(): void
    {
        $id = $this->createdOrderId();
        $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id], self::NOW - 1));
        $this->assertSame(200, $res->get_status());
        $this->assertTrue($res->get_data()['already_paid']);
        $this->assertSame(1, count(array_keys($this->port->calls, 'complete', true)));
    }

    public function test_paid_con_otra_referencia_sobre_un_pedido_pagado_da_409(): void
    {
        $id = $this->createdOrderId();
        $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => (string) $id]));
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(['payment_reference' => 'pi_OTRA']), ['id' => (string) $id], self::NOW - 1));
        $this->assertSame(409, $res->get_status());
    }

    public function test_paid_sobre_un_pedido_ajeno_a_trusteed_da_404(): void
    {
        $this->port->orders[7] = ['id' => 7, 'number' => '7', 'status' => 'pending', 'total_cents' => 6655, 'currency' => 'EUR', 'trusteed_order_id' => '', 'is_paid' => false, 'transaction_id' => ''];
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => '7']));
        $this->assertSame(404, $res->get_status());
        $this->assertNotContains('complete', $this->port->calls);
    }

    public function test_paid_sobre_un_pedido_inexistente_da_404(): void
    {
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(), ['id' => '999']));
        $this->assertSame(404, $res->get_status());
    }

    public function test_paid_con_referencia_que_no_es_un_payment_intent_da_400(): void
    {
        $id  = $this->createdOrderId();
        $res = $this->endpoint->handle_paid($this->signed($this->paidBody(['payment_reference' => 'x; DROP']), ['id' => (string) $id]));
        $this->assertSame(400, $res->get_status());
    }
}
