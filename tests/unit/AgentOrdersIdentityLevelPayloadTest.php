<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/class-cart-bridge.php';
require_once __DIR__ . '/../../includes/class-enforcement-api-client.php';
require_once __DIR__ . '/../../includes/class-offline-safety-valve-evaluator.php';
require_once __DIR__ . '/../../includes/class-agent-event-webhook.php';
require_once __DIR__ . '/../../includes/class-multi-add-handler.php';
require_once __DIR__ . '/../../includes/class-cart-signals.php';
require_once __DIR__ . '/../../includes/class-checkout-enforcer.php';

/**
 * El pedido que crea Trusteed (POST trusteed/v1/orders) se reevalúa aquí: el nivel
 * de identidad del agente, autenticado por el HMAC de instalación, tiene que llegar
 * a /v1/rules/evaluate bajo la clave `agentIdentityLevel`, y solo por esa vía.
 */
final class AgentOrdersIdentityLevelPayloadTest extends TestCase
{
    private object $client;

    protected function setUp(): void
    {
        $GLOBALS['trusteed_test_wc'] = (object) ['session' => null, 'cart' => null];
        $this->client = new class('https://api.trusteed.test', 'inst', 'example-secret') extends Trusteed_Enforcement_Api_Client {
            public ?array $last_payload = null;
            public function evaluate(array $payload): Trusteed_Eval_Result
            {
                $this->last_payload = $payload;
                return new Trusteed_Eval_Result(Trusteed_Eval_Outcome::ALLOW, 'ok', 200);
            }
        };
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['trusteed_test_wc']);
    }

    private static function order(): object
    {
        return new class {
            public function get_currency(): string { return 'EUR'; }
            public function get_total(): string { return '50.00'; }
            public function get_items(): array { return []; }
            public function get_coupon_codes(): array { return []; }
            public function __call(string $name, array $args) { return ''; }
        };
    }

    private function evaluar(?string $did, ?string $level = null, bool $pasar_nivel = true): array
    {
        $enforcer = new Trusteed_Checkout_Enforcer($this->client, 'merchant-1', 'inst', null, 'enforce', null);
        if ($pasar_nivel) {
            $enforcer->evaluate_order_for_agent_api(self::order(), $did, $level);
        } else {
            $enforcer->evaluate_order_for_agent_api(self::order(), $did);
        }
        return $this->client->last_payload;
    }

    public function test_el_payload_lleva_el_nivel_valido(): void
    {
        $p = $this->evaluar('did:web:a.example', 'verified_signature');
        self::assertSame('verified_signature', $p['agentIdentityLevel']);
    }

    public function test_un_nivel_fuera_del_vocabulario_no_viaja(): void
    {
        self::assertArrayNotHasKey('agentIdentityLevel', $this->evaluar('did:web:a.example', 'root'));
    }

    public function test_sin_nivel_el_payload_no_cambia(): void
    {
        $sin    = $this->evaluar('did:web:a.example', null, false);
        $nulo   = $this->evaluar('did:web:a.example', null);
        self::assertArrayNotHasKey('agentIdentityLevel', $sin);
        self::assertSame(
            ['merchantId', 'agentId', 'orderContext', 'platform', 'installationId', 'timestamp', 'endpointClass'],
            array_keys($sin)
        );
        self::assertSame(array_keys($sin), array_keys($nulo));
    }
}
