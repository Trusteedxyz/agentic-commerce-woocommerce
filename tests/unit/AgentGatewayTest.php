<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

if (!class_exists('WC_Payment_Gateway')) {
    class WC_Payment_Gateway
    {
        public string $id = '';
        public string $method_title = '';
        public string $method_description = '';
        public string $title = '';
        public string $enabled = 'yes';
        public bool $has_fields = false;
        public array $supports = ['products'];
    }
}

require_once __DIR__ . '/../../includes/class-agent-gateway.php';
require_once __DIR__ . '/../../includes/class-agent-gateway-class.php';

/** Pasarela `trusteed_agent` (plan 2026-10-05, tarea 3): existe para etiquetar pedidos, nunca para cobrar en el front. */
final class AgentGatewayTest extends TestCase
{
    public function test_id_y_titulo(): void
    {
        $g = new Trusteed_Agent_Gateway();
        $this->assertSame('trusteed_agent', $g->id);
        $this->assertSame('Trusteed (agente)', $g->method_title);
    }

    public function test_nunca_esta_disponible_en_el_front(): void
    {
        $this->assertFalse((new Trusteed_Agent_Gateway())->is_available());
    }

    public function test_process_payment_no_cobra(): void
    {
        $r = (new Trusteed_Agent_Gateway())->process_payment(1);
        $this->assertSame('failure', $r['result']);
    }

    public function test_el_registrador_anade_la_clase_al_filtro(): void
    {
        $out = Trusteed_Agent_Gateway_Registrar::filter_gateways(['WC_Gateway_BACS']);
        $this->assertContains('Trusteed_Agent_Gateway', $out);
        $this->assertContains('WC_Gateway_BACS', $out);
    }

    public function test_el_registrador_no_duplica(): void
    {
        $out = Trusteed_Agent_Gateway_Registrar::filter_gateways(['Trusteed_Agent_Gateway']);
        $this->assertSame(['Trusteed_Agent_Gateway'], $out);
    }
}
