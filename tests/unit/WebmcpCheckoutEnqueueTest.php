<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/AgentOrdersFakes.php';
require_once __DIR__ . '/../../includes/class-cart-bridge.php';
require_once __DIR__ . '/../../includes/class-enforcement-api-client.php';
require_once __DIR__ . '/../../includes/class-offline-safety-valve-evaluator.php';
require_once __DIR__ . '/../../includes/class-agent-event-webhook.php';
require_once __DIR__ . '/../../includes/class-multi-add-handler.php';
require_once __DIR__ . '/../../includes/class-checkout-enforcer.php';
require_once __DIR__ . '/../../includes/class-webmcp-checkout.php';

if (!defined('TRUSTEED_PLUGIN_URL')) {
    define('TRUSTEED_PLUGIN_URL', 'https://shop.example.test/wp-content/plugins/trusteed/');
}
if (!defined('TRUSTEED_VERSION')) {
    define('TRUSTEED_VERSION', '9.9.9');
}
if (!function_exists('rest_url')) {
    function rest_url($path = '') { return 'https://shop.example.test/wp-json/' . $path; }
}
if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action) { return 'nonce-for:' . $action; }
}
if (!function_exists('wp_json_encode')) {
    function wp_json_encode($v) { return json_encode($v); }
}
if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script($h, $src = '', $deps = [], $ver = false, $foot = false)
    {
        $GLOBALS['trusteed_test_enqueued'][] = [$h, $src];
    }
}
if (!function_exists('wp_add_inline_script')) {
    function wp_add_inline_script($h, $code, $pos = 'after')
    {
        $GLOBALS['trusteed_test_inline'][] = [$h, $code, $pos];
        return true;
    }
}
if (!function_exists('is_order_received_page')) {
    function is_order_received_page(): bool { return (bool) ($GLOBALS['trusteed_test_received'] ?? false); }
}

if (!class_exists('Trusteed_Test_Wc_Session')) {
    final class Trusteed_Test_Wc_Session
    {
        public array $data = [];
        public function get(string $key) { return $this->data[$key] ?? null; }
        public function set(string $key, $value): void { $this->data[$key] = $value; }
        public function save_data(): void {}
    }
}

/** Plugin: enqueue de las tools y canal 'webmcp' en la evaluación del checkout. */
final class WebmcpCheckoutEnqueueTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['trusteed_test_enqueued'] = [];
        $GLOBALS['trusteed_test_inline']   = [];
        $GLOBALS['trusteed_test_is_checkout'] = true;
        $GLOBALS['trusteed_test_received'] = false;
        $GLOBALS['trusteed_test_wc'] = (object) ['session' => new Trusteed_Test_Wc_Session(), 'cart' => null];
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['trusteed_test_wc'], $_GET['agentic'], $_COOKIE['trusteed_agentic_origin']);
        $GLOBALS['trusteed_test_is_checkout'] = false;
    }

    public function test_enqueues_only_on_checkout_with_config(): void
    {
        (new Trusteed_Webmcp_Checkout())->enqueue();
        self::assertCount(1, $GLOBALS['trusteed_test_enqueued']);
        self::assertStringEndsWith('assets/js/checkout-tools.iife.js', $GLOBALS['trusteed_test_enqueued'][0][1]);
        [, $code, $pos] = $GLOBALS['trusteed_test_inline'][0];
        self::assertSame('before', $pos);
        $json = json_decode(substr($code, strlen('window.__trusteedCheckoutTools = '), -1), true);
        self::assertSame('https://shop.example.test/wp-json/trusteed/v1/webmcp/checkout', $json['endpoint']);
        self::assertSame('nonce-for:woocommerce-trusteed-webmcp-checkout', $json['nonce']);
        self::assertSame('X-Trusteed-Nonce', $json['nonceHeader']);
        self::assertSame(['X-WP-Nonce' => 'nonce-for:wp_rest'], $json['extraHeaders']);
        self::assertSame('woocommerce', $json['platform']);
    }

    public function test_not_enqueued_off_checkout_or_on_order_received(): void
    {
        $GLOBALS['trusteed_test_is_checkout'] = false;
        (new Trusteed_Webmcp_Checkout())->enqueue();
        $GLOBALS['trusteed_test_is_checkout'] = true;
        $GLOBALS['trusteed_test_received'] = true;
        (new Trusteed_Webmcp_Checkout())->enqueue();
        self::assertSame([], $GLOBALS['trusteed_test_enqueued']);
    }

    public function test_off_setting_registers_neither_route_nor_enqueue(): void
    {
        $src = file_get_contents(__DIR__ . '/../../includes/class-plugin.php');
        self::assertMatchesRegularExpression("/'on' === Trusteed_Options::get_option\\( 'webmcp_checkout_tools', 'off' \\)\\s*\\) \\{[^}]*register_routes[^}]*'enqueue'/s", $src);
    }

    /** @return array{0:Trusteed_Checkout_Enforcer,1:object} */
    private function enforcer(): array
    {
        $client = new class('https://api.trusteed.test', 'inst', 'example-secret') extends Trusteed_Enforcement_Api_Client {
            public array $payload = [];
            public function evaluate(array $payload): Trusteed_Eval_Result
            {
                $this->payload = $payload;
                return new Trusteed_Eval_Result(Trusteed_Eval_Outcome::ALLOW, 'ok', 200, null, null);
            }
        };
        return [new Trusteed_Checkout_Enforcer($client, 'merchant-1', 'inst', null, 'enforce', null), $client];
    }

    private function evaluate(Trusteed_Checkout_Enforcer $e): string
    {
        $m = new ReflectionMethod($e, 'run_evaluation');
        $m->setAccessible(true);
        return $m->invoke($e, static fn(): array => ['cartTotalCents' => 5000, 'currency' => 'EUR', 'itemCount' => 1, 'billingCountry' => 'ES']);
    }

    private function mark_session(): void
    {
        $s = $GLOBALS['trusteed_test_wc']->session;
        $s->set(Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN, '1');
        $s->set(Trusteed_Cart_Bridge::SESSION_AGENTIC_CHANNEL, 'webmcp');
    }

    public function test_webmcp_channel_applies_agent_rules(): void
    {
        [$e, $client] = $this->enforcer();
        $this->mark_session();
        self::assertSame('ALLOW', $this->evaluate($e));
        self::assertSame('agentic_endpoint', $client->payload['endpointClass'] ?? null);
        self::assertSame('webmcp', $e->agentic_channel());
    }

    public function test_client_cannot_clear_agentic_mark(): void
    {
        [$e, $client] = $this->enforcer();
        $this->mark_session();
        $_GET['agentic'] = '0';
        unset($_COOKIE['trusteed_agentic_origin']);
        $e->capture_agent_session();
        self::assertSame('1', $GLOBALS['trusteed_test_wc']->session->get(Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN));
        $this->evaluate($e);
        self::assertSame('agentic_endpoint', $client->payload['endpointClass'] ?? null);
    }

    public function test_unmarked_session_is_human_with_no_channel(): void
    {
        [$e, $client] = $this->enforcer();
        $this->evaluate($e);
        self::assertArrayNotHasKey('endpointClass', $client->payload);
        self::assertSame('', $e->agentic_channel());
    }

    public function test_order_gets_channel_meta_blocks_path_and_session_is_cleared(): void
    {
        [$e] = $this->enforcer();
        $this->mark_session();
        $this->evaluate($e);
        self::assertNull($GLOBALS['trusteed_test_wc']->session->get(Trusteed_Cart_Bridge::SESSION_AGENTIC_CHANNEL));
        $src = file_get_contents(__DIR__ . '/../../includes/class-checkout-enforcer.php');
        // Blocks y clásico estampan el mismo meta desde el canal leído antes de limpiar.
        self::assertStringContainsString('$order->update_meta_data( self::CHANNEL_META_KEY, $this->agentic_channel )', $src);
        self::assertStringContainsString('$this->stamp_next_classic_channel();', $src);
        self::assertSame('_trusteed_agentic_channel', Trusteed_Checkout_Enforcer::CHANNEL_META_KEY);
    }

    public function test_classic_hook_stamps_meta_on_the_order(): void
    {
        if (!class_exists('WC_Order')) {
            eval('class WC_Order { public array $meta = []; public function update_meta_data($k, $v) { $this->meta[$k] = $v; } }');
        }
        [$e] = $this->enforcer();
        $this->mark_session();
        $this->evaluate($e);
        $m = new ReflectionMethod($e, 'stamp_next_classic_channel');
        $m->setAccessible(true);
        Stub_Captured_Hooks_Registry::$hooks = [];
        $m->invoke($e);
        $order = new WC_Order();
        foreach (Stub_Captured_Hooks_Registry::$hooks['woocommerce_checkout_create_order'] ?? [] as $cb) {
            $cb($order);
        }
        self::assertSame(['_trusteed_agentic_channel' => 'webmcp'], $order->meta);
    }
}
