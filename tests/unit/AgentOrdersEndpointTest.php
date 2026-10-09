<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/AgentOrdersFakes.php';
require_once __DIR__ . '/../../includes/class-agent-request-auth.php';
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

    protected function setUp(): void
    {
        $GLOBALS['__amcp_transients_store'] = [];
        $GLOBALS['__amcp_transient_expiry'] = [];
        $GLOBALS['__amcp_options_store']    = ['trusteed_agent_orders_enabled' => 'yes'];
        $GLOBALS['__trusteed_test_routes']  = [];
        $this->port     = new Trusteed_Test_Woo_Port();
        $this->endpoint = new Trusteed_Agent_Orders_Endpoint($this->port, fn(): string => self::SECRET, fn(): int => self::NOW);
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
