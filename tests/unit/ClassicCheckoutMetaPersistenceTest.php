<?php

declare(strict_types=1);

/**
 * Gap 1 — Verify the classic-checkout path stamps `_trusteed_agent_did`
 * (parity with the Blocks/Store API path).
 *
 * Tests Amcp_Classic_Meta_Persister::register() which is the extracted
 * helper called by Amcp_Checkout_Enforcer on the ALLOW branch of the
 * classic-checkout path.
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}

if (!class_exists('Stub_Captured_Hooks_Registry')) {
    final class Stub_Captured_Hooks_Registry
    {
        /** @var array<string,callable[]> */
        public static array $hooks = [];
    }
}

if (!function_exists('add_action')) {
    function add_action(string $hook, callable $cb, int $prio = 10, int $args = 1): void
    {
        Stub_Captured_Hooks_Registry::$hooks[$hook][] = $cb;
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($v): string
    {
        return is_string($v) ? trim(strip_tags($v)) : '';
    }
}

if (!class_exists('WC_Order')) {
    class WC_Order
    {
        /** @var array<string,string> */
        public array $meta = [];

        public function update_meta_data(string $key, string $value): void
        {
            $this->meta[$key] = $value;
        }

        public function save_meta_data(): void
        {
            // no-op
        }
    }
}

require_once __DIR__ . '/../../includes/class-classic-meta-persister.php';

use PHPUnit\Framework\TestCase;

final class ClassicCheckoutMetaPersistenceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Stub_Captured_Hooks_Registry::$hooks = [];
        \Amcp_Classic_Meta_Persister::reset_for_tests();
    }

    public function test_persister_registers_action_and_stamps_metas(): void
    {
        $did = 'did:web:agent.example.com';
        \Amcp_Classic_Meta_Persister::register($did);

        $this->assertArrayHasKey('woocommerce_checkout_create_order', Stub_Captured_Hooks_Registry::$hooks);
        $this->assertCount(1, Stub_Captured_Hooks_Registry::$hooks['woocommerce_checkout_create_order']);

        $order   = new \WC_Order();
        $closure = Stub_Captured_Hooks_Registry::$hooks['woocommerce_checkout_create_order'][0];
        $closure($order);

        $this->assertSame($did, $order->meta['_trusteed_agent_did']);
        $this->assertSame('ok', $order->meta['_trusteed_agent_status']);
        $this->assertArrayHasKey('_trusteed_eval_at', $order->meta);
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+00:00$/',
            $order->meta['_trusteed_eval_at']
        );
    }

    public function test_persister_is_idempotent_per_did_in_same_request(): void
    {
        $did = 'did:web:dup.example.com';
        \Amcp_Classic_Meta_Persister::register($did);
        \Amcp_Classic_Meta_Persister::register($did);
        \Amcp_Classic_Meta_Persister::register($did);

        $this->assertCount(
            1,
            Stub_Captured_Hooks_Registry::$hooks['woocommerce_checkout_create_order'] ?? [],
            'Duplicate registrations for the same DID must be suppressed'
        );
    }

    public function test_persister_silently_ignores_empty_did(): void
    {
        \Amcp_Classic_Meta_Persister::register('');
        $this->assertArrayNotHasKey(
            'woocommerce_checkout_create_order',
            Stub_Captured_Hooks_Registry::$hooks,
            'Empty DID must not register a hook'
        );
    }

    public function test_persister_silently_ignores_non_order_payload(): void
    {
        \Amcp_Classic_Meta_Persister::register('did:web:safe.example.com');

        $closure = Stub_Captured_Hooks_Registry::$hooks['woocommerce_checkout_create_order'][0];
        // Should not throw nor warn on non-WC_Order input.
        $closure(null);
        $closure(new stdClass());
        $this->assertTrue(true);
    }
}
