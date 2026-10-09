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

// Misma forma que el stub de ClassicCheckoutMetaPersistenceTest (el primero que
// carga gana; ambos son compatibles): el persister exige `instanceof WC_Order`.
if (!class_exists('WC_Order')) {
    class WC_Order
    {
        /** @var array<string,string> */
        public array $meta = [];
        public function update_meta_data(string $key, string $value): void { $this->meta[$key] = $value; }
        public function save_meta_data(): void {}
    }
}

/**
 * Task 5b (plan 04, CARRY de la Task 6): el DID que llega de fuera (URL,
 * cuerpo, meta) NO es una identidad verificada. Sólo un token VÁLIDO de ese
 * mismo DID lo convierte en la atribución que el pedido lleva a R023
 * (`_trusteed_agent_did`). Cabecera/URL ≠ token ⇒ token inválido, agentId =
 * el declarado, jti sin consumir, sin atribución. Sin DID declarado + token
 * válido ⇒ el DID del token.
 *
 * Conductual: ejecuta `enforce_rules_blocks()` y `enforce_rules_classic()`
 * reales con tokens Ed25519 firmados de verdad (ext-sodium) y un resolver del
 * snapshot como el que da producción.
 */
final class AgentAttributionRequiresVerifiedTokenTest extends TestCase
{
    private const MERCHANT = 'merchant-5b';
    private const DID_A    = 'did:web:agent-a.example.com';
    private const DID_B    = 'did:web:agent-b.example.com';

    private static string $privA;
    private static string $pubA;
    private static string $privB;
    private static string $pubB;

    private Trusteed_Test_Wc_Session $session;
    private object $client;
    private int $jtiSeq = 0;

    public static function setUpBeforeClass(): void
    {
        if (!extension_loaded('sodium')) {
            self::markTestSkipped('ext-sodium is required');
        }
        $a = sodium_crypto_sign_keypair();
        self::$privA = sodium_crypto_sign_secretkey($a);
        self::$pubA  = sodium_crypto_sign_publickey($a);
        $b = sodium_crypto_sign_keypair();
        self::$privB = sodium_crypto_sign_secretkey($b);
        self::$pubB  = sodium_crypto_sign_publickey($b);
    }

    protected function setUp(): void
    {
        $this->session = new Trusteed_Test_Wc_Session();
        $GLOBALS['trusteed_test_wc'] = (object) ['session' => $this->session, 'cart' => new Trusteed_5b_Empty_Cart()];
        Stub_Captured_Hooks_Registry::$hooks = [];
        Trusteed_Classic_Meta_Persister::reset_for_tests();
        $this->client = self::client(new Trusteed_Nonce_Result(Trusteed_Nonce_Outcome::ACCEPTED, 'ok', 200));
        $_POST = [];
        $_GET  = [];
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['trusteed_test_wc'], $GLOBALS['trusteed_test_is_checkout'], $GLOBALS['trusteed_test_orders']);
        $_POST = [];
        $_GET  = [];
        Stub_Captured_Hooks_Registry::$hooks = [];
    }

    // ── Blocks / Store API ───────────────────────────────────────────────────

    /**
     * Task 16 (revisión de seguridad, PoC): este test fijaba que el servidor
     * recibía el DID declarado como `agentId` sin token. Era el comportamiento
     * vulnerable — el historial (R003/R005/R010/R021/R023/R024) se indexa por
     * el hash de `agentId`, así que el agente heredaba el historial de otro DID
     * o quemaba el suyo. Sustituido: DID declarado sin token ⇒ agente ANÓNIMO.
     */
    public function test_declared_did_without_token_is_an_anonymous_agent_and_never_attributed(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::DID_A);
        $order = $this->blocks(new Trusteed_5b_Order(), []);
        $p = $this->client->payloads[0];
        self::assertNull($p['agentId'], 'un DID sólo declarado no viaja como identidad');
        self::assertSame('agentic_endpoint', $p['endpointClass'], 'sigue contando como agente');
        self::assertArrayNotHasKey('_agent_token_signature_invalid', $p['orderContext']['cartAttributes'] ?? [], 'sin token no hay token inválido');
        self::assertArrayNotHasKey('_trusteed_agent_did', $order->meta, 'un DID de la URL no atribuye el pedido');
    }

    public function test_valid_token_of_the_declared_did_attributes_the_order(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::DID_A);
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->token(self::DID_A, self::$privA));
        $order = $this->blocks(new Trusteed_5b_Order(), []);
        self::assertSame(self::DID_A, $order->meta['_trusteed_agent_did'] ?? null);
        self::assertSame('ok', $order->meta['_trusteed_agent_status'] ?? null);
        self::assertSame(1, count($this->client->nonces));
        self::assertSame(self::DID_A, $this->client->nonces[0][1]);
        self::assertArrayNotHasKey('_agent_token_signature_invalid', $this->client->payloads[0]['orderContext']['cartAttributes']);
    }

    public function test_valid_token_of_another_agent_is_an_invalid_token_and_attributes_nothing(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::DID_A);
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->token(self::DID_B, self::$privB));
        $order = $this->blocks(new Trusteed_5b_Order(), []);
        $p = $this->client->payloads[0];
        self::assertNull($p['agentId'], 'ni el declarado ni el del token ajeno (Task 16)');
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_signature_invalid'] ?? null);
        self::assertSame([], $this->client->nonces, 'el jti de un token que no es suyo no se consume');
        self::assertArrayNotHasKey('_trusteed_agent_did', $order->meta);
    }

    public function test_valid_token_without_declared_did_provides_the_token_did(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->token(self::DID_A, self::$privA));
        $order = $this->blocks(new Trusteed_5b_Order(), []);
        self::assertSame(self::DID_A, $this->client->payloads[0]['agentId']);
        self::assertSame(self::DID_A, $order->meta['_trusteed_agent_did'] ?? null);
    }

    public function test_store_api_body_token_makes_the_checkout_agentic_and_verified(): void
    {
        $order = $this->blocks(new Trusteed_5b_Order(), ['_trusteed_agent_token' => $this->token(self::DID_A, self::$privA)]);
        $p = $this->client->payloads[0];
        self::assertSame('agentic_endpoint', $p['endpointClass'] ?? null, 'un agente con sólo token no es una persona');
        self::assertSame(self::DID_A, $p['agentId']);
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_present'] ?? null);
        self::assertSame(self::DID_A, $order->meta['_trusteed_agent_did'] ?? null);
    }

    public function test_order_meta_token_makes_the_checkout_agentic(): void
    {
        $order = new Trusteed_5b_Order();
        $order->meta['_trusteed_agent_token'] = $this->token(self::DID_A, self::$privA);
        $this->blocks($order, []);
        self::assertSame('agentic_endpoint', $this->client->payloads[0]['endpointClass'] ?? null);
        self::assertSame(self::DID_A, $this->client->payloads[0]['agentId']);
    }

    public function test_malformed_body_token_is_an_invalid_agent_token_not_a_human(): void
    {
        $order = $this->blocks(new Trusteed_5b_Order(), ['_trusteed_agent_token' => 'not-a-jws']);
        $p = $this->client->payloads[0];
        self::assertSame('agentic_endpoint', $p['endpointClass'] ?? null);
        self::assertNull($p['agentId']);
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_present'] ?? null);
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_signature_invalid'] ?? null);
        self::assertArrayNotHasKey('_trusteed_agent_did', $order->meta);
    }

    public function test_indeterminate_token_does_not_attribute_and_counts_as_invalid(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, 'did:web:unknown.example.com');
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->token('did:web:unknown.example.com', self::$privA));
        $order = $this->blocks(new Trusteed_5b_Order(), []);
        $p = $this->client->payloads[0];
        self::assertSame([], $this->client->nonces);
        self::assertNull($p['agentId']);
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_signature_invalid'] ?? null, 'INDETERMINATE = inválido para el agente');
        self::assertArrayNotHasKey('_trusteed_agent_did', $order->meta, 'un DID fuera del resolver no está verificado');
    }

    /** PoC de la revisión: DID de la víctima + token autofirmado con un did:key desconocido. */
    public function test_victim_did_with_a_self_signed_unknown_did_key_token_is_anonymous_and_invalid(): void
    {
        $attacker = sodium_crypto_sign_keypair();
        $didKey   = 'did:key:z6MkAttackerUnknownKey000000000000000000000000000';
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::DID_A);
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->token($didKey, sodium_crypto_sign_secretkey($attacker)));
        $order = $this->blocks(new Trusteed_5b_Order(), []);
        $p = $this->client->payloads[0];
        self::assertNull($p['agentId'], 'el historial de la víctima no se toca');
        self::assertSame('agentic_endpoint', $p['endpointClass']);
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_signature_invalid'] ?? null);
        self::assertSame([], $this->client->nonces);
        self::assertArrayNotHasKey('_agent_key_age_hours', $p['orderContext']['cartAttributes'], 'R004: la clave de un token sin probar no envejece a nadie');
        self::assertArrayNotHasKey('_trusteed_agent_did', $order->meta);
    }

    public function test_token_without_a_snapshot_to_verify_it_counts_as_invalid(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::DID_A);
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->token(self::DID_A, self::$privA));
        $enforcer = new Trusteed_Checkout_Enforcer($this->client, self::MERCHANT, 'inst', null, 'enforce', null);
        $this->blocks(new Trusteed_5b_Order(), [], $enforcer);
        $p = $this->client->payloads[0];
        self::assertNull($p['agentId']);
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_signature_invalid'] ?? null);
    }

    public function test_replayed_token_does_not_attribute(): void
    {
        $this->client = self::client(new Trusteed_Nonce_Result(Trusteed_Nonce_Outcome::REPLAY, 'replay', 409));
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::DID_A);
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->token(self::DID_A, self::$privA));
        $order = $this->blocks(new Trusteed_5b_Order(), []);
        self::assertSame('true', $this->client->payloads[0]['orderContext']['cartAttributes']['_agent_token_replay'] ?? null);
        self::assertNull($this->client->payloads[0]['agentId'], 'un token reutilizado no toca el historial de su DID');
        self::assertArrayNotHasKey('_trusteed_agent_did', $order->meta);
    }

    // ── Decisión 2: la firma verificó y sólo el almacén de nonces no contesta ──
    //
    // El DID es AUTÉNTICO: la identidad se conserva (agentId), el payload lleva
    // `_agent_token_nonce_unavailable` y el modo decide: `enforce` retiene al
    // agente antes de preguntar al servidor (avería nuestra, no «una regla»);
    // `observe` lo deja pasar con la marca. Sin nonce aceptado el pedido NO se
    // atribuye (R023). Distinto del INDETERMINATE del token (DID fuera del
    // resolver), que sigue siendo agente anónimo.

    private function nonceDownClient(?Trusteed_Eval_Result $eval = null): object
    {
        return self::client(new Trusteed_Nonce_Result(Trusteed_Nonce_Outcome::INDETERMINATE, 'network_error', 0), $eval);
    }

    private function enforcerInMode(string $mode): Trusteed_Checkout_Enforcer
    {
        return new Trusteed_Checkout_Enforcer($this->client, self::MERCHANT, 'inst', self::snapshot(), $mode, null);
    }

    /** observe: pasa con la identidad y la marca, sin atribuir el pedido. */
    public function test_nonce_store_down_in_observe_keeps_the_identity_and_marks_it_but_does_not_attribute(): void
    {
        $this->client = $this->nonceDownClient();
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->token(self::DID_A, self::$privA));
        $order = $this->blocks(new Trusteed_5b_Order(), [], $this->enforcerInMode('observe'));
        $p = $this->client->payloads[0];
        self::assertSame(self::DID_A, $p['agentId'], 'el DID es auténtico: la firma verificó');
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_nonce_unavailable'] ?? null);
        self::assertArrayNotHasKey('_agent_token_signature_invalid', $p['orderContext']['cartAttributes']);
        self::assertSame('agentic_endpoint', $p['endpointClass']);
        self::assertSame([], $order->meta, 'sin nonce aceptado no se guarda el DID para R023');
    }

    /** observe en el clásico: el pedido pasa y no se registra la atribución. */
    public function test_nonce_store_down_in_observe_classic_passes_without_registering_attribution(): void
    {
        $this->client = $this->nonceDownClient();
        $_POST['_trusteed_agent_token'] = $this->token(self::DID_A, self::$privA);
        $this->enforcerInMode('observe')->enforce_rules_classic();
        self::assertSame(self::DID_A, $this->client->payloads[0]['agentId']);
        self::assertArrayNotHasKey('woocommerce_checkout_create_order', Stub_Captured_Hooks_Registry::$hooks, 'no se engancha el persistidor de atribución');
    }

    /** observe con un BLOCK real del servidor: sigue siendo «una regla». */
    public function test_nonce_store_down_in_observe_still_reports_a_real_server_block_as_a_rule(): void
    {
        $this->client = $this->nonceDownClient(new Trusteed_Eval_Result(Trusteed_Eval_Outcome::BLOCK, 'R030', 200));
        $_POST['_trusteed_agent_token'] = $this->token(self::DID_A, self::$privA);
        try {
            $this->enforcerInMode('observe')->enforce_rules_classic();
            self::fail('el BLOCK del servidor se respeta en observe');
        } catch (\Exception $e) {
            self::assertStringContainsString('did not pass', $e->getMessage());
        }
        self::assertSame(self::DID_A, $this->client->payloads[0]['agentId']);
    }

    /** enforce (strict): retiene al agente con el mensaje de avería, sin preguntar al servidor. */
    public function test_nonce_store_down_in_enforce_holds_the_agent_with_the_outage_message(): void
    {
        $this->client = $this->nonceDownClient(new Trusteed_Eval_Result(Trusteed_Eval_Outcome::BLOCK, 'ok', 200));
        $_POST['_trusteed_agent_token'] = $this->token(self::DID_A, self::$privA);
        try {
            $this->enforcer()->enforce_rules_classic();
            self::fail('en enforce, sin anti-replay, el agente se retiene');
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        self::assertStringContainsString('could not be verified', $message);
        self::assertStringNotContainsString('did not pass', $message, 'una avería no se anuncia como regla');
        self::assertSame([], $this->client->payloads, 'se retiene antes de preguntar al servidor');
        self::assertCount(1, $this->client->nonces);
        self::assertArrayNotHasKey('woocommerce_checkout_create_order', Stub_Captured_Hooks_Registry::$hooks, 'ni se atribuye');
    }

    /** Task 16 decía «el servidor dijo ALLOW, pasa»: con la identidad auténtica el modo manda. */
    public function test_nonce_store_down_in_enforce_holds_the_agent_even_when_the_server_would_allow(): void
    {
        $this->client = $this->nonceDownClient();
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->token(self::DID_A, self::$privA));
        $enforcer = $this->enforcer();
        $order    = new Trusteed_5b_Order();
        $enforcer->enforce_rules_blocks($order, new Trusteed_5b_Request([]));
        self::assertSame([], $this->client->payloads);
        self::assertSame([], $order->meta);
        self::assertNull($this->session->get(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN), 'la sesión del agente se limpia');
    }

    /**
     * El plugin sólo conoce `enforce` y `observe`: `balanced`/`permissive` (de otras
     * plataformas) se normalizan a `enforce`, que retiene.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('foreignModes')]
    public function test_nonce_store_down_with_a_foreign_failure_mode_holds_like_enforce(string $mode): void
    {
        $this->client = $this->nonceDownClient();
        $_POST['_trusteed_agent_token'] = $this->token(self::DID_A, self::$privA);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('could not be verified');
        $this->enforcerInMode($mode)->enforce_rules_classic();
    }

    public static function foreignModes(): array
    {
        return ['balanced' => ['balanced'], 'permissive' => ['permissive'], 'strict' => ['strict'], 'basura' => ['no-es-un-modo']];
    }

    /** Sin comparar con (ii): un token cuyo DID no está en el resolver sigue siendo anónimo, con o sin almacén de nonces. */
    public function test_an_unverifiable_token_stays_anonymous_whatever_the_nonce_store_does(): void
    {
        $this->client = $this->nonceDownClient();
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->token('did:web:unknown.example.com', self::$privA));
        $order = $this->blocks(new Trusteed_5b_Order(), [], $this->enforcerInMode('observe'));
        $p = $this->client->payloads[0];
        self::assertSame([], $this->client->nonces, 'sin firma verificada no se llega al almacén de nonces');
        self::assertNull($p['agentId']);
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_signature_invalid'] ?? null);
        self::assertArrayNotHasKey('_agent_token_nonce_unavailable', $p['orderContext']['cartAttributes']);
        self::assertSame([], $order->meta);
    }

    /** Un humano nunca se retiene por el almacén de nonces (no hay token que verificar). */
    public function test_nonce_store_down_never_holds_a_person(): void
    {
        $this->client = $this->nonceDownClient();
        $this->enforcer()->enforce_rules_classic();
        self::assertCount(1, $this->client->payloads);
        self::assertSame([], $this->client->nonces);
    }

    public function test_a_previous_verified_run_does_not_leak_into_the_next_checkout(): void
    {
        $enforcer = $this->enforcer();
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->token(self::DID_A, self::$privA));
        $first = new Trusteed_5b_Order();
        $enforcer->enforce_rules_blocks($first, new Trusteed_5b_Request([]));
        self::assertSame(self::DID_A, $first->meta['_trusteed_agent_did'] ?? null);

        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::DID_B);
        $second = new Trusteed_5b_Order();
        $enforcer->enforce_rules_blocks($second, new Trusteed_5b_Request([]));
        self::assertArrayNotHasKey('_trusteed_agent_did', $second->meta);
    }

    public function test_organic_blocks_payload_is_unchanged_and_nothing_is_attributed(): void
    {
        $order = $this->blocks(new Trusteed_5b_Order(), ['billing_address' => ['country' => 'ES']]);
        $p = $this->client->payloads[0];
        self::assertSame(['merchantId', 'agentId', 'orderContext', 'platform', 'installationId', 'timestamp'], array_keys($p));
        self::assertNull($p['agentId']);
        self::assertArrayNotHasKey('_agent_token_present', $p['orderContext']['cartAttributes'] ?? []);
        self::assertSame([], $order->meta);
    }

    // ── Clásico ──────────────────────────────────────────────────────────────

    public function test_classic_declared_did_without_token_registers_no_attribution(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::DID_A);
        $this->enforcer()->enforce_rules_classic();
        self::assertNull($this->client->payloads[0]['agentId'], 'Task 16: el DID declarado no viaja sin token válido');
        self::assertSame('agentic_endpoint', $this->client->payloads[0]['endpointClass']);
        self::assertArrayNotHasKey('woocommerce_checkout_create_order', Stub_Captured_Hooks_Registry::$hooks);
    }

    public function test_classic_post_token_is_an_agent_signal_and_attributes_the_verified_did(): void
    {
        $_POST['_trusteed_agent_token'] = $this->token(self::DID_A, self::$privA);
        $this->enforcer()->enforce_rules_classic();
        self::assertSame('agentic_endpoint', $this->client->payloads[0]['endpointClass'] ?? null);
        self::assertSame(self::DID_A, $this->client->payloads[0]['agentId']);
        $hooks = Stub_Captured_Hooks_Registry::$hooks['woocommerce_checkout_create_order'] ?? [];
        self::assertCount(1, $hooks);
        $order = new WC_Order();
        $hooks[0]($order);
        self::assertSame(self::DID_A, $order->meta['_trusteed_agent_did'] ?? null);
    }

    public function test_classic_mismatched_token_registers_no_attribution(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::DID_A);
        $_POST['_trusteed_agent_token'] = $this->token(self::DID_B, self::$privB);
        $this->enforcer()->enforce_rules_classic();
        self::assertNull($this->client->payloads[0]['agentId'], 'Task 16: ni A (declarado) ni B (token ajeno)');
        self::assertSame('true', $this->client->payloads[0]['orderContext']['cartAttributes']['_agent_token_signature_invalid'] ?? null);
        self::assertArrayNotHasKey('woocommerce_checkout_create_order', Stub_Captured_Hooks_Registry::$hooks);
    }

    // ── Avería ≠ regla en el mensaje ─────────────────────────────────────────

    public function test_classic_agent_closed_by_our_outage_is_told_it_could_not_be_verified(): void
    {
        $this->client = self::client(
            new Trusteed_Nonce_Result(Trusteed_Nonce_Outcome::ACCEPTED, 'ok', 200),
            new Trusteed_Eval_Result(Trusteed_Eval_Outcome::INDETERMINATE, 'http_5xx', 503)
        );
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::DID_A);
        try {
            $this->enforcer()->enforce_rules_classic();
            self::fail('un agente en enforce se cierra ante la caída');
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        self::assertStringContainsString('could not be verified', $message);
        self::assertStringNotContainsString('did not pass', $message, 'una avería no se anuncia como regla');
    }

    public function test_classic_rule_block_keeps_the_rule_message(): void
    {
        $this->client = self::client(
            new Trusteed_Nonce_Result(Trusteed_Nonce_Outcome::ACCEPTED, 'ok', 200),
            new Trusteed_Eval_Result(Trusteed_Eval_Outcome::BLOCK, 'ok', 200)
        );
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_ID, self::DID_A);
        try {
            $this->enforcer()->enforce_rules_classic();
            self::fail('una regla bloquea');
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        self::assertStringContainsString('did not pass', $message);
    }

    public function test_blocks_fallback_notice_distinguishes_outage_from_rule(): void
    {
        $GLOBALS['__amcp_wc_notices'] = [];
        $this->client = self::client(
            new Trusteed_Nonce_Result(Trusteed_Nonce_Outcome::ACCEPTED, 'ok', 200),
            new Trusteed_Eval_Result(Trusteed_Eval_Outcome::INDETERMINATE, 'network_error')
        );
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN, '1');
        $order = $this->blocks(new Trusteed_5b_Order(), []);
        self::assertCount(1, $GLOBALS['__amcp_wc_notices']);
        self::assertStringContainsString('could not be verified', $GLOBALS['__amcp_wc_notices'][0][1]);
        self::assertSame([], $order->meta);
    }

    public function test_human_is_not_blocked_by_our_outage(): void
    {
        $this->client = self::client(
            new Trusteed_Nonce_Result(Trusteed_Nonce_Outcome::ACCEPTED, 'ok', 200),
            new Trusteed_Eval_Result(Trusteed_Eval_Outcome::INDETERMINATE, 'http_5xx', 503)
        );
        $this->enforcer()->enforce_rules_classic();
        self::assertArrayNotHasKey('endpointClass', $this->client->payloads[0]);
    }

    // ── Captura de la URL ────────────────────────────────────────────────────

    public function test_url_token_without_did_is_captured(): void
    {
        $GLOBALS['trusteed_test_is_checkout'] = true;
        $token = $this->token(self::DID_A, self::$privA);
        $_GET  = [Trusteed_Cart_Bridge::AGENT_TOKEN_PARAM => rawurlencode($token)];
        $this->enforcer()->capture_agent_session_early();
        self::assertSame($token, $this->session->get(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN));
        self::assertNull($this->session->get(Trusteed_Cart_Bridge::SESSION_AGENT_ID));
    }

    public function test_malformed_url_token_marks_the_session_as_an_invalid_agent_token(): void
    {
        $GLOBALS['trusteed_test_is_checkout'] = true;
        $_GET = [Trusteed_Cart_Bridge::AGENT_TOKEN_PARAM => 'garbage'];
        $enforcer = $this->enforcer();
        $enforcer->capture_agent_session_early();
        $this->blocks(new Trusteed_5b_Order(), [], $enforcer);
        $p = $this->client->payloads[0];
        self::assertSame('agentic_endpoint', $p['endpointClass'] ?? null);
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_signature_invalid'] ?? null);
        self::assertNull($this->session->get(Trusteed_Agent_Identity::SESSION_TOKEN_REJECTED), 'se borra con el resto');
    }

    /**
     * Task 16 (punto 4): `?trusteed_agent_token[]=x` llegaba como array a
     * rawurldecode() ⇒ TypeError de PHP 8 ⇒ 500 en template_redirect. Ahora
     * un parámetro que no es una cadena cuenta como señal de agente rota.
     */
    public function test_array_shaped_url_params_do_not_throw_and_count_as_broken_agent_signals(): void
    {
        $GLOBALS['trusteed_test_is_checkout'] = true;
        $_GET = [
            Trusteed_Cart_Bridge::AGENT_TOKEN_PARAM => ['x'],
            Trusteed_Cart_Bridge::AGENT_ID_PARAM    => ['did:web:agent-a.example.com'],
        ];
        $enforcer = $this->enforcer();
        $enforcer->capture_agent_session_early();
        self::assertNull($this->session->get(Trusteed_Cart_Bridge::SESSION_AGENT_ID));
        self::assertNull($this->session->get(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN));
        $this->blocks(new Trusteed_5b_Order(), [], $enforcer);
        $p = $this->client->payloads[0];
        self::assertSame('agentic_endpoint', $p['endpointClass'] ?? null);
        self::assertNull($p['agentId']);
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_signature_invalid'] ?? null);
    }

    public function test_array_shaped_did_param_alone_still_marks_an_agent(): void
    {
        $GLOBALS['trusteed_test_is_checkout'] = true;
        $_GET = [Trusteed_Cart_Bridge::AGENT_ID_PARAM => ['did:web:agent-a.example.com']];
        $enforcer = $this->enforcer();
        $enforcer->capture_agent_session_early();
        $this->blocks(new Trusteed_5b_Order(), [], $enforcer);
        $p = $this->client->payloads[0];
        self::assertSame('agentic_endpoint', $p['endpointClass'] ?? null, 'un parámetro de agente roto sólo endurece');
        self::assertNull($p['agentId']);
    }

    // ── REST de pedidos ──────────────────────────────────────────────────────

    public function test_enforcer_init_registers_the_rest_attribution_guard(): void
    {
        $this->enforcer()->init();
        $cbs = Stub_Captured_Hooks_Registry::$hooks['woocommerce_rest_pre_insert_shop_order_object'] ?? [];
        self::assertCount(1, $cbs);
        $order = new Trusteed_5b_Order();
        $order->meta['_trusteed_agent_did'] = 'did:web:forged.example.com';
        $out = $cbs[0]($order, new Trusteed_5b_Request(['meta_data' => [['key' => '_trusteed_agent_did', 'value' => 'did:web:forged.example.com']]]));
        self::assertArrayNotHasKey('_trusteed_agent_did', $out->meta);
    }

    public function test_rest_cannot_overwrite_the_attribution_of_an_existing_order(): void
    {
        $stored = new Trusteed_5b_Order(42);
        $stored->meta['_trusteed_agent_did'] = self::DID_A;
        $GLOBALS['trusteed_test_orders'] = [42 => $stored];
        $edited = new Trusteed_5b_Order(42);
        $edited->meta = ['_trusteed_agent_did' => 'did:web:forged.example.com', '_trusteed_agent_status' => 'ok', '_trusteed_agent_id' => 'did:web:forged.example.com', 'note' => 'x'];
        $out = Trusteed_Rest_Order_Meta_Guard::restore_attribution_meta($edited, new Trusteed_5b_Request(['meta_data' => [
            ['key' => '_trusteed_agent_did', 'value' => 'did:web:forged.example.com'],
            ['key' => ' _TRUSTEED_AGENT_STATUS', 'value' => 'ok'],
            ['key' => '_trusteed_agent_id', 'value' => 'did:web:forged.example.com'],
            ['key' => 'note', 'value' => 'x'],
        ]]));
        self::assertSame(self::DID_A, $out->meta['_trusteed_agent_did']);
        self::assertArrayNotHasKey('_trusteed_agent_status', $out->meta, 'lo que el pedido guardado no tenía no se inventa');
        self::assertArrayNotHasKey('_trusteed_agent_id', $out->meta, 'la reserva que lee WC-AgenticTools tampoco');
        self::assertSame('x', $out->meta['note'], 'la meta ajena no se toca');
    }

    public function test_rest_request_without_attribution_meta_is_untouched(): void
    {
        $order = new Trusteed_5b_Order(7);
        $order->meta['_trusteed_agent_did'] = self::DID_A;
        $out = Trusteed_Rest_Order_Meta_Guard::restore_attribution_meta($order, new Trusteed_5b_Request(['status' => 'completed']));
        self::assertSame($order, $out);
        self::assertSame(self::DID_A, $out->meta['_trusteed_agent_did']);
    }

    // ── B5 (2026-10-01) — el contrato canónico del token: `nonce`, no `jti` ───

    /** Un token con la forma EXACTA de generate-test-agent-token.ts (nonce, sin jti) es VÁLIDO y consume su nonce. */
    public function test_canonical_token_with_nonce_and_no_jti_is_valid_and_consumes_the_nonce(): void
    {
        $nonce = bin2hex(random_bytes(16));
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->canonicalToken(self::DID_A, self::$privA, ['nonce' => $nonce]));
        $order = $this->blocks(new Trusteed_5b_Order(), []);
        $attrs = $this->client->payloads[0]['orderContext']['cartAttributes'] ?? [];
        self::assertSame(self::DID_A, $this->client->payloads[0]['agentId'], 'atribuido');
        self::assertSame(self::DID_A, $order->meta['_trusteed_agent_did'] ?? null);
        self::assertCount(1, $this->client->nonces);
        self::assertSame($nonce, $this->client->nonces[0][2], 'el servidor recibe el NONCE del payload firmado, no un jti inexistente');
        self::assertArrayNotHasKey('_agent_token_signature_invalid', $attrs);
        self::assertArrayNotHasKey('_agent_token_jti_missing', $attrs);
    }

    public function test_replay_of_a_canonical_token_nonce_is_an_invalid_token(): void
    {
        $this->client = self::client(new Trusteed_Nonce_Result(Trusteed_Nonce_Outcome::REPLAY, 'replay_detected', 409));
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->canonicalToken(self::DID_A, self::$privA, []));
        $order = $this->blocks(new Trusteed_5b_Order(), []);
        $p = $this->client->payloads[0];
        self::assertCount(1, $this->client->nonces, 'el nonce se presentó al servidor');
        self::assertNull($p['agentId'], 'un nonce ya gastado no prueba identidad');
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_signature_invalid'] ?? null);
        self::assertArrayNotHasKey('_trusteed_agent_did', $order->meta);
    }

    /** Alias heredado: sin `nonce`, el `jti` hace de identificador. */
    public function test_legacy_jti_without_nonce_is_valid_through_the_alias(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->canonicalToken(self::DID_A, self::$privA, ['jti' => 'legacyAliasJti000001'], ['nonce']));
        $this->blocks(new Trusteed_5b_Order(), []);
        self::assertSame(self::DID_A, $this->client->payloads[0]['agentId']);
        self::assertSame('legacyAliasJti000001', $this->client->nonces[0][2]);
    }

    public function test_nonce_wins_when_both_claims_are_present_and_different(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->canonicalToken(self::DID_A, self::$privA, ['nonce' => 'canonicalNonce000001', 'jti' => 'legacyAliasJti000001']));
        $this->blocks(new Trusteed_5b_Order(), []);
        self::assertSame('canonicalNonce000001', $this->client->nonces[0][2]);
    }

    public function test_token_with_neither_nonce_nor_jti_is_invalid_and_spends_nothing(): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->canonicalToken(self::DID_A, self::$privA, [], ['nonce']));
        $order = $this->blocks(new Trusteed_5b_Order(), []);
        $p = $this->client->payloads[0];
        self::assertSame([], $this->client->nonces);
        self::assertNull($p['agentId']);
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_jti_missing'] ?? null, 'el atributo heredado cubre el nonce ausente');
        self::assertArrayNotHasKey('_trusteed_agent_did', $order->meta);
    }

    /** @dataProvider malformed_identifiers */
    public function test_malformed_nonce_is_invalid_and_spends_nothing(string $nonce): void
    {
        $this->session->set(Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $this->canonicalToken(self::DID_A, self::$privA, ['nonce' => $nonce]));
        $order = $this->blocks(new Trusteed_5b_Order(), []);
        $p = $this->client->payloads[0];
        self::assertSame([], $this->client->nonces);
        self::assertNull($p['agentId']);
        self::assertSame('true', $p['orderContext']['cartAttributes']['_agent_token_jti_malformed'] ?? null);
        self::assertArrayNotHasKey('_trusteed_agent_did', $order->meta);
    }

    public static function malformed_identifiers(): array
    {
        return [
            '15 caracteres' => [str_repeat('n', 15)],
            '65 caracteres' => [str_repeat('n', 65)],
            'alfabeto fuera de base64url' => ['has.dot.in.it.illegal0001'],
        ];
    }

    // ── Puros ────────────────────────────────────────────────────────────────

    public function test_bind_token_matrix(): void
    {
        self::assertSame(['state' => 'valid', 'did' => self::DID_A, 'mismatch' => false], Trusteed_Agent_Identity::bind_token('', 'valid', self::DID_A));
        self::assertSame(['state' => 'valid', 'did' => self::DID_A, 'mismatch' => false], Trusteed_Agent_Identity::bind_token(self::DID_A, 'valid', self::DID_A));
        // Task 16: `did` es SÓLO la identidad probada; nunca la declarada sin prueba.
        self::assertSame(['state' => 'invalid', 'did' => '', 'mismatch' => true], Trusteed_Agent_Identity::bind_token(self::DID_A, 'valid', self::DID_B));
        self::assertSame(['state' => 'indeterminate', 'did' => '', 'mismatch' => false], Trusteed_Agent_Identity::bind_token(self::DID_A, 'indeterminate', ''));
        self::assertSame(['state' => 'indeterminate', 'did' => '', 'mismatch' => false], Trusteed_Agent_Identity::bind_token(self::DID_A, 'indeterminate', self::DID_A));
        self::assertSame(['state' => 'invalid', 'did' => '', 'mismatch' => false], Trusteed_Agent_Identity::bind_token('', 'valid', ''));
        self::assertSame(['state' => 'invalid', 'did' => '', 'mismatch' => false], Trusteed_Agent_Identity::bind_token('', 'invalid', self::DID_B));
    }

    // ── Arnés ────────────────────────────────────────────────────────────────

    private function enforcer(): Trusteed_Checkout_Enforcer
    {
        return new Trusteed_Checkout_Enforcer($this->client, self::MERCHANT, 'inst', self::snapshot(), 'enforce', null);
    }

    private function blocks(object $order, array $body, ?Trusteed_Checkout_Enforcer $enforcer = null): object
    {
        ($enforcer ?? $this->enforcer())->enforce_rules_blocks($order, new Trusteed_5b_Request($body));
        self::assertCount(1, $this->client->payloads, 'se evaluó exactamente una vez');
        return $order;
    }

    private static function client(Trusteed_Nonce_Result $nonce, ?Trusteed_Eval_Result $eval = null): object
    {
        $eval ??= new Trusteed_Eval_Result(Trusteed_Eval_Outcome::ALLOW, 'ok', 200);
        return new class('https://api.trusteed.test', 'inst', 'example-secret', $nonce, $eval) extends Trusteed_Enforcement_Api_Client {
            public array $payloads = [];
            public array $nonces = [];
            private Trusteed_Nonce_Result $nonce;
            private Trusteed_Eval_Result $eval;
            public function __construct(string $a, string $b, string $c, Trusteed_Nonce_Result $nonce, Trusteed_Eval_Result $eval)
            {
                parent::__construct($a, $b, $c);
                $this->nonce = $nonce;
                $this->eval  = $eval;
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

    private static function snapshot(): Trusteed_Snapshot_Client_Woo
    {
        $resolver = [
            ['did' => self::DID_A, 'publicKeyJwk' => ['kty' => 'OKP', 'crv' => 'Ed25519', 'x' => self::b64(self::$pubA)]],
            ['did' => self::DID_B, 'publicKeyJwk' => ['kty' => 'OKP', 'crv' => 'Ed25519', 'x' => self::b64(self::$pubB)]],
        ];
        return new class('https://api.trusteed.test', 'inst', 'example-secret', $resolver) extends Trusteed_Snapshot_Client_Woo {
            private array $resolver;
            public function __construct(string $a, string $b, string $c, array $resolver)
            {
                parent::__construct($a, $b, $c);
                $this->resolver = $resolver;
            }
            // Task 23a: el enforcer lee el snapshot VERIFICADO con su estado.
            public function get_snapshot_result(string $merchant_id): array
            {
                return ['status' => self::STATUS_OK, 'payload' => ['rules' => [], 'agentDidResolver' => $this->resolver]];
            }
        };
    }

    private function token(string $did, string $priv): string
    {
        $this->jtiSeq++;
        $h = self::b64((string) json_encode(['alg' => 'EdDSA', 'typ' => 'trusteed-agent-token+jwt', 'kid' => $did . '#key-1']));
        $p = self::b64((string) json_encode([
            'iss' => $did, 'aud' => 'trusteed', 'merchantId' => self::MERCHANT,
            'iat' => time(), 'exp' => time() + 300, 'jti' => 'jti5bToken' . str_pad((string) $this->jtiSeq, 12, '0', STR_PAD_LEFT),
        ]));
        return $h . '.' . $p . '.' . self::b64(sodium_crypto_sign_detached($h . '.' . $p, $priv));
    }

    /**
     * Payload exacto de apps/api/scripts/generate-test-agent-token.ts (`nonce`, sin `jti`).
     *
     * @param array<string,mixed> $override claims que se añaden o sustituyen
     * @param string[]            $drop     claims que se quitan
     */
    private function canonicalToken(string $did, string $priv, array $override, array $drop = []): string
    {
        $claims = array_merge([
            'iss' => $did, 'aud' => 'trusteed', 'merchantId' => self::MERCHANT,
            'iat' => time(), 'exp' => time() + 300,
            'nonce' => bin2hex(random_bytes(16)),
            'checkoutIntentHash' => 'sha256:' . str_repeat('0', 64),
        ], $override);
        foreach ($drop as $k) {
            unset($claims[$k]);
        }
        $h = self::b64((string) json_encode(['alg' => 'EdDSA', 'typ' => 'trusteed-agent-token+jwt', 'kid' => $did . '#key-1']));
        $p = self::b64((string) json_encode($claims));
        return $h . '.' . $p . '.' . self::b64(sodium_crypto_sign_detached($h . '.' . $p, $priv));
    }

    private static function b64(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }
}

final class Trusteed_5b_Empty_Cart
{
    public int $id = 0;
    public function get_total($ctx = 'view') { return '50.00'; }
    public function get_cart(): array { return []; }
    public function get_cart_contents_count(): int { return 0; }
    public function get_applied_coupons(): array { return []; }
}

final class Trusteed_5b_Request
{
    private array $body;
    public function __construct(array $body) { $this->body = $body; }
    public function get_json_params(): array { return $this->body; }
    public function get_param(string $key) { return $this->body[$key] ?? null; }
}

final class Trusteed_5b_Order
{
    public array $meta = [];
    private int $id;
    public function __construct(int $id = 0) { $this->id = $id; }
    public function get_id(): int { return $this->id; }
    public function get_currency(): string { return 'EUR'; }
    public function get_total() { return '50.00'; }
    public function get_items(): array { return []; }
    public function get_coupon_codes(): array { return []; }
    public function get_billing_country(): string { return ''; }
    public function get_shipping_country(): string { return ''; }
    public function get_payment_method(): string { return ''; }
    public function get_shipping_address_1(): string { return ''; }
    public function get_shipping_address_2(): string { return ''; }
    public function get_shipping_company(): string { return ''; }
    public function get_billing_company(): string { return ''; }
    public function get_meta(string $key, bool $single = true) { return $this->meta[$key] ?? ''; }
    public function update_meta_data(string $key, $value): void { $this->meta[$key] = $value; }
    public function delete_meta_data(string $key): void { unset($this->meta[$key]); }
    public function save_meta_data(): void {}
}
