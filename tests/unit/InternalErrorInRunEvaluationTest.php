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
 * Task 27c (M6): una excepción NUESTRA dentro de `run_evaluation()` —el
 * constructor de contexto, la válvula offline, el cliente— no puede abortar el
 * pedido de un HUMANO ni ser un permiso para un AGENTE: agente retenido en
 * TODOS los modos (como avería, no como «una regla te bloqueó»), humano pasa.
 * Ejecuta el `run_evaluation()` real por reflexión.
 */
final class InternalErrorInRunEvaluationTest extends TestCase
{
    private const MODES = ['enforce', 'observe', 'balanced', 'permissive', 'raro'];

    private Trusteed_Test_Wc_Session $session;
    private string $errorLog = '';

    protected function setUp(): void
    {
        $this->session               = new Trusteed_Test_Wc_Session();
        $GLOBALS['trusteed_test_wc'] = (object) ['session' => $this->session, 'cart' => null];
        $this->errorLog              = (string) tempnam(sys_get_temp_dir(), 'trusteed-wc-log');
        ini_set('error_log', $this->errorLog);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['trusteed_test_wc'], $GLOBALS['trusteed_test_is_checkout']);
        @unlink($this->errorLog);
        ini_restore('error_log');
        $_GET = [];
    }

    private function log(): string
    {
        return (string) file_get_contents($this->errorLog);
    }

    /** Cliente cuyo evaluate() lanza lo que se le diga (o devuelve INDETERMINATE). */
    private static function client(?\Throwable $throws, ?Trusteed_Eval_Result $result = null): object
    {
        return new class('https://api.trusteed.test', 'inst', 'example-secret', $throws, $result) extends Trusteed_Enforcement_Api_Client {
            public function __construct(string $a, string $b, string $c, private ?\Throwable $throws, private ?Trusteed_Eval_Result $result)
            {
                parent::__construct($a, $b, $c);
            }
            public function evaluate(array $payload): Trusteed_Eval_Result
            {
                if (null !== $this->throws) {
                    throw $this->throws;
                }
                return $this->result ?? new Trusteed_Eval_Result(Trusteed_Eval_Outcome::ALLOW, '', 200);
            }
        };
    }

    /** Snapshot verificado con una regla R019 cuyo parámetro malformado hace fallar la válvula (TypeError). */
    private static function snapshot_with_broken_valve_rule(): Trusteed_Snapshot_Client_Woo
    {
        return new class('https://api.trusteed.test', 'inst', 'example-secret') extends Trusteed_Snapshot_Client_Woo {
            public function get_snapshot_result(string $merchant_id): array
            {
                // R018 con un array donde va un número: `5000 / [1]` lanza TypeError. (Antes se rompía con
                // `blockedCountries => 'KP'`, pero la válvula ya normaliza listas de países y tolera una cadena.)
                $rule = ['ruleCode' => 'R018', 'enabled' => true, 'appliesTo' => 'ALL', 'params' => ['merchantAvgOrderCents' => [1]]];
                return ['status' => self::STATUS_OK, 'payload' => ['rules' => [$rule], 'agentDidResolver' => []]];
            }
        };
    }

    private function run_eval(object $client, string $mode, ?Trusteed_Snapshot_Client_Woo $snapshot = null): string
    {
        $enforcer = new Trusteed_Checkout_Enforcer($client, 'merchant-1', 'inst', $snapshot, $mode, null);
        $method   = new ReflectionMethod($enforcer, 'run_evaluation');
        $method->setAccessible(true);
        return $method->invoke($enforcer, static fn(): array => ['cartTotalCents' => 5000, 'currency' => 'EUR', 'itemCount' => 1, 'billingCountry' => 'ES']);
    }

    private function agent(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, 'did:web:agent.example.com');
    }

    public function test_an_internal_error_never_aborts_a_human_checkout_in_any_mode(): void
    {
        foreach (self::MODES as $mode) {
            $decision = $this->run_eval(self::client(new \RuntimeException('secret-internal-detail')), $mode);
            self::assertSame('ALLOW', $decision, $mode);
        }
        self::assertStringNotContainsString('secret-internal-detail', $this->log(), 'el detalle no va ni al log');
        self::assertSame(count(self::MODES), substr_count($this->log(), '[amcp.enforcement_internal_error]'));
    }

    public function test_an_internal_error_holds_an_agent_in_every_mode_as_an_outage_not_a_rule(): void
    {
        foreach (self::MODES as $mode) {
            $this->agent();
            $decision = $this->run_eval(self::client(new \RuntimeException('boom')), $mode);
            self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $decision, $mode);
        }
    }

    public function test_an_error_thrown_as_a_php_error_is_also_caught(): void
    {
        $this->agent();
        self::assertSame(
            Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE,
            $this->run_eval(self::client(new \TypeError('bad')), 'observe')
        );
    }

    /** El fallo de la válvula offline (Task 5 M2) ya no aborta el pedido de una persona. */
    public function test_a_failing_offline_valve_does_not_abort_a_human(): void
    {
        $indeterminate = new Trusteed_Eval_Result(Trusteed_Eval_Outcome::INDETERMINATE, 'http_5xx', 503);
        foreach (self::MODES as $mode) {
            $decision = $this->run_eval(self::client(null, $indeterminate), $mode, self::snapshot_with_broken_valve_rule());
            self::assertSame('ALLOW', $decision, $mode);
        }
        self::assertSame(count(self::MODES), substr_count($this->log(), '[amcp.enforcement_internal_error]'), 'la válvula sí falló de verdad');
    }

    public function test_a_failing_offline_valve_holds_an_agent_even_after_the_session_was_cleared(): void
    {
        $indeterminate = new Trusteed_Eval_Result(Trusteed_Eval_Outcome::INDETERMINATE, 'http_5xx', 503);
        foreach (self::MODES as $mode) {
            $this->agent(); // run_evaluation borra la sesión a mitad: el agente se lee ANTES.
            $decision = $this->run_eval(self::client(null, $indeterminate), $mode, self::snapshot_with_broken_valve_rule());
            self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $decision, $mode);
        }
    }

    /** Si ni siquiera se puede leer la sesión no se sabe que sea una persona: se trata como agente. */
    public function test_an_unreadable_session_is_treated_as_an_agent(): void
    {
        $GLOBALS['trusteed_test_wc']->session = new class {
            public function get(string $key)
            {
                throw new \LogicException('session store unavailable');
            }
            public function set(string $key, $value): void {}
            public function save_data(): void {}
        };
        foreach (self::MODES as $mode) {
            self::assertSame(
                Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE,
                $this->run_eval(self::client(null), $mode),
                $mode
            );
        }
    }

    public function test_the_policy_is_mode_independent(): void
    {
        self::assertSame('BLOCK', Trusteed_Enforcement_Failure_Policy::decide_on_internal_error(true));
        self::assertSame('ALLOW', Trusteed_Enforcement_Failure_Policy::decide_on_internal_error(false));
    }

    // ── Task 29b: el constructor de contexto corre DENTRO de la guarda ────────

    private function run_with_context(callable $build, object $client, string $mode): string
    {
        $enforcer = new Trusteed_Checkout_Enforcer($client, 'merchant-1', 'inst', null, $mode, null);
        $method   = new ReflectionMethod($enforcer, 'run_evaluation');
        $method->setAccessible(true);
        return $method->invoke($enforcer, \Closure::fromCallable($build));
    }

    public function test_a_context_builder_error_never_aborts_a_human_in_any_mode(): void
    {
        foreach (self::MODES as $mode) {
            $decision = $this->run_with_context(
                static function (): array {
                    throw new \RuntimeException('cart-secret-detail');
                },
                self::client(null),
                $mode
            );
            self::assertSame('ALLOW', $decision, $mode);
        }
        self::assertStringNotContainsString('cart-secret-detail', $this->log());
        self::assertSame(count(self::MODES), substr_count($this->log(), '[amcp.enforcement_internal_error]'));
    }

    public function test_a_context_builder_error_holds_an_agent_in_every_mode_without_calling_the_server(): void
    {
        $client = new class('https://api.trusteed.test', 'inst', 'example-secret') extends Trusteed_Enforcement_Api_Client {
            public int $calls = 0;
            public function evaluate(array $payload): Trusteed_Eval_Result
            {
                ++$this->calls;
                return new Trusteed_Eval_Result(Trusteed_Eval_Outcome::ALLOW, '', 200);
            }
        };
        foreach (self::MODES as $mode) {
            $this->agent();
            $decision = $this->run_with_context(
                static function (): array {
                    throw new \TypeError('bad cart');
                },
                $client,
                $mode
            );
            self::assertSame(Trusteed_Checkout_Enforcer::DECISION_UNAVAILABLE, $decision, $mode);
        }
        self::assertSame(0, $client->calls, 'sin contexto no hay evaluación: un fallo propio no es un permiso');
    }

    /** El camino real del checkout clásico: WooCommerce sin carrito lanza dentro del constructor. */
    public function test_the_classic_entry_point_lets_a_human_order_through_when_the_cart_cannot_be_read(): void
    {
        $enforcer = new Trusteed_Checkout_Enforcer(self::client(null), 'merchant-1', 'inst', null, 'enforce', null);

        $enforcer->enforce_rules_classic(); // cart = null ⇒ Error dentro del constructor de contexto

        self::assertStringContainsString('[amcp.enforcement_internal_error]', $this->log(), 'el constructor sí lanzó');
    }

    public function test_the_classic_entry_point_holds_an_agent_when_the_cart_cannot_be_read(): void
    {
        $this->agent();
        $enforcer = new Trusteed_Checkout_Enforcer(self::client(null), 'merchant-1', 'inst', null, 'observe', null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('could not be verified right now');
        $enforcer->enforce_rules_classic();
    }

    /** El camino real de la Store API: un pedido cuyo constructor de contexto lanza. */
    public function test_the_store_api_entry_point_lets_a_human_through_and_holds_an_agent_when_the_order_cannot_be_read(): void
    {
        $GLOBALS['__amcp_wc_notices'] = [];
        $order = new class {
            public function get_currency(): string
            {
                throw new \LogicException('order store unavailable');
            }
            public function get_meta(string $key, bool $single = true): string
            {
                return '';
            }
        };
        $enforcer = new Trusteed_Checkout_Enforcer(self::client(null), 'merchant-1', 'inst', null, 'observe', null);

        $enforcer->enforce_rules_blocks($order, null);
        self::assertSame([], $GLOBALS['__amcp_wc_notices'], 'la persona no ve ningún aviso');

        $this->agent();
        $enforcer->enforce_rules_blocks($order, null);
        self::assertCount(1, $GLOBALS['__amcp_wc_notices'], 'el agente queda retenido con el aviso de avería');
        self::assertStringContainsString('could not be verified right now', $GLOBALS['__amcp_wc_notices'][0][1]);
    }

    public function test_no_error_keeps_the_previous_verdicts(): void
    {
        self::assertSame('ALLOW', $this->run_eval(self::client(null), 'enforce'));
        $blocking = new Trusteed_Eval_Result(Trusteed_Eval_Outcome::BLOCK, 'rule', 200);
        self::assertSame('BLOCK', $this->run_eval(self::client(null, $blocking), 'observe'), 'una regla del servidor sigue bloqueando a la persona');
        self::assertStringNotContainsString('enforcement_internal_error', $this->log());
    }
}
