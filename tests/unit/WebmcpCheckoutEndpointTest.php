<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/AgentOrdersFakes.php';
require_once __DIR__ . '/../../includes/class-cart-bridge.php';
require_once __DIR__ . '/../../includes/class-webmcp-checkout.php';

if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action)
    {
        $GLOBALS['trusteed_test_nonce_actions'][] = $action;
        return $nonce === 'good-nonce' && $action === 'woocommerce-trusteed-webmcp-checkout' ? 1 : false;
    }
}

if (!function_exists('home_url')) {
    function home_url($path = '')
    {
        return 'https://shop.example.test' . $path;
    }
}
if (!function_exists('wc_load_cart')) {
    function wc_load_cart()
    {
        $GLOBALS['trusteed_test_calls'][] = 'wc_load_cart';
    }
}

/** WC() falso con shipping(). */
final class Trusteed_Test_Webmcp_Wc
{
    public $session;
    public $cart;
    public $customer;
    /** @var array<int,array<string,mixed>> */
    public array $packages = [['rates' => ['flat_rate:1' => 1, 'free_shipping:2' => 1]]];
    public function shipping(): object
    {
        // Como Woo en una petición REST nueva: sin tarifas hasta que se calculan los totales.
        $p = $this->cart->calc > 0 ? $this->packages : [];
        return new class($p) { public function __construct(private array $p) {} public function get_packages(): array { return $this->p; } };
    }
}

/** Sesión WC en memoria. */
final class Trusteed_Test_Webmcp_Session
{
    /** @var array<string,mixed> */
    public array $data = [];
    public function set($k, $v): void { $this->data[$k] = $v; }
    public function get($k) { return $this->data[$k] ?? null; }
}

/** Cliente WC en memoria: cualquier set_x/get_x. */
final class Trusteed_Test_Webmcp_Customer
{
    /** @var array<string,mixed> */
    public array $f = [];
    public function __call($n, $a)
    {
        if (strpos($n, 'set_') === 0) { $this->f[substr($n, 4)] = $a[0]; return null; }
        return $this->f[substr($n, 4)] ?? '';
    }
}

final class Trusteed_Test_Webmcp_Product
{
    public function get_name(): string { return 'Camiseta'; }
}

final class Trusteed_Test_Webmcp_Cart
{
    public bool $empty = false;
    public int $calc = 0;
    public function is_empty(): bool { return $this->empty; }
    public function calculate_totals(): void { $this->calc++; }
    public function get_cart(): array { return [['data' => new Trusteed_Test_Webmcp_Product(), 'quantity' => 2, 'line_total' => 40.0]]; }
    public function get_subtotal() { return 40.0; }
    public function get_shipping_total() { return 5.0; }
    public function get_total_tax() { return 9.45; }
    public function get_total($ctx = 'view') { return 54.45; }
}

final class WebmcpCheckoutEndpointTest extends TestCase
{
    private Trusteed_Test_Webmcp_Session $session;
    private Trusteed_Test_Webmcp_Cart $cart;
    private Trusteed_Test_Webmcp_Customer $customer;
    private Trusteed_Webmcp_Checkout $endpoint;

    protected function setUp(): void
    {
        $this->session  = new Trusteed_Test_Webmcp_Session();
        $this->cart     = new Trusteed_Test_Webmcp_Cart();
        $this->customer = new Trusteed_Test_Webmcp_Customer();
        $wc = new Trusteed_Test_Webmcp_Wc();
        $wc->session = $this->session; $wc->cart = $this->cart; $wc->customer = $this->customer;
        $GLOBALS['trusteed_test_wc'] = $wc;
        $GLOBALS['trusteed_test_calls'] = [];
        $GLOBALS['trusteed_test_nonce_actions'] = [];
        $this->endpoint = new Trusteed_Webmcp_Checkout();
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['trusteed_test_wc'], $GLOBALS['trusteed_test_calls']);
    }

    /** @param array<string,mixed> $args */
    private function call(string $tool, array $args = [], string $nonce = 'good-nonce', array $extra = []): Trusteed_Test_Rest_Request
    {
        return new Trusteed_Test_Rest_Request((string) json_encode(['tool' => $tool, 'arguments' => $args]), ['x-trusteed-nonce' => $nonce] + $extra);
    }

    public function test_get_marks_session_agentic_and_channel(): void
    {
        $res  = $this->endpoint->handle($this->call('get_checkout'));
        $data = $res->get_data();
        $this->assertSame(200, $res->get_status());
        $this->assertSame('1', $this->session->get(Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN));
        $this->assertSame('webmcp', $this->session->get(Trusteed_Cart_Bridge::SESSION_AGENTIC_CHANNEL));
        $this->assertSame('open', $data['status']);
        $this->assertSame('EUR', $data['currency']);
        $this->assertSame(54.45, $data['totals']['total']);
        $this->assertSame('Camiseta', $data['items'][0]['name']);
        $this->assertSame(2, $data['items'][0]['quantity']);
    }

    public function test_get_without_cart_does_not_mark(): void
    {
        $this->cart->empty = true;
        $res = $this->endpoint->handle($this->call('get_checkout'));
        $this->assertSame(['status' => 'empty'], $res->get_data());
        $this->assertSame(200, $res->get_status());
        $this->assertSame([], $this->session->data);

        $GLOBALS['trusteed_test_wc'] = (object) ['session' => null, 'cart' => null, 'customer' => null];
        $this->assertSame(['status' => 'empty'], $this->endpoint->handle($this->call('get_checkout'))->get_data());
    }

    public function test_missing_or_bad_nonce_is_403_and_unmarked(): void
    {
        $this->assertFalse($this->endpoint->check_nonce($this->call('get_checkout', [], 'forged')));
        $this->assertFalse($this->endpoint->check_nonce(new Trusteed_Test_Rest_Request('{}', [])));
        $this->assertTrue($this->endpoint->check_nonce($this->call('get_checkout')));
        // permission_callback devuelve false => WP responde 403 sin llamar a handle().
        $this->assertSame([], $this->session->data);
    }

    public function test_update_applies_email_address_and_shipping_method(): void
    {
        $res = $this->endpoint->handle($this->call('update_checkout', ['checkout' => [
            'buyer'       => ['email' => 'a@b.es'],
            'fulfillment' => [
                'address'   => ['address1' => 'Calle 1', 'city' => 'Madrid', 'postal_code' => '28001', 'country_code' => 'ES'],
                'method_id' => 'flat_rate:1',
            ],
        ]]));
        $this->assertSame('a@b.es', $this->customer->f['billing_email']);
        $this->assertSame('28001', $this->customer->f['shipping_postcode']);
        $this->assertSame('ES', $this->customer->f['shipping_country']);
        $this->assertSame(['flat_rate:1'], $this->session->get('chosen_shipping_methods'));
        $this->assertSame(2, $this->cart->calc); // una al cargar, otra tras aplicar
        $this->assertSame('flat_rate:1', $res->get_data()['fulfillment']['method_id']);
        $this->assertSame('1', $this->session->get(Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN));
    }

    public function test_update_ignores_unknown_fields(): void
    {
        $this->endpoint->handle($this->call('update_checkout', ['checkout' => [
            'line_items'  => [['id' => 99]],
            'attribution' => 'evil',
            'buyer'       => ['email' => 'not-an-email', 'phone' => '123'],
        ]]));
        $this->assertSame([], $this->customer->f);
        $this->assertNull($this->session->get('chosen_shipping_methods'));
    }

    public function test_complete_never_creates_order(): void
    {
        $res = $this->endpoint->handle($this->call('complete_checkout'));
        $this->assertSame(
            ['status' => 'requires_buyer_confirmation', 'checkout_url' => 'https://shop.example.test/checkout/'],
            $res->get_data()
        );
        // El stub de WC() no tiene checkout(); wc_create_order no existe aquí: cualquier llamada habría sido fatal.
        $this->assertFalse(function_exists('wc_create_order'));
    }

    public function test_unknown_tool_is_400_and_oversized_is_413(): void
    {
        $this->assertSame(400, $this->endpoint->handle($this->call('delete_everything'))->get_status());
        $this->assertSame([], $this->session->data);
        $big = new Trusteed_Test_Rest_Request(str_repeat('a', 16385), ['x-trusteed-nonce' => 'good-nonce']);
        $this->assertSame(413, $this->endpoint->handle($big)->get_status());
    }

    public function test_loads_cart_before_nonce_check_and_before_reading_cart(): void
    {
        $this->endpoint->check_nonce($this->call('get_checkout'));
        $this->assertSame(['wc_load_cart'], $GLOBALS['trusteed_test_calls']);
        $this->endpoint->handle($this->call('get_checkout'));
        $this->assertSame(['wc_load_cart', 'wc_load_cart'], $GLOBALS['trusteed_test_calls']);
    }

    public function test_update_rejects_unknown_shipping_method_and_writes_nothing(): void
    {
        $res = $this->endpoint->handle($this->call('update_checkout', ['checkout' => [
            'buyer'       => ['email' => 'a@b.es'],
            'fulfillment' => ['method_id' => 'made_up:9'],
        ]]));
        $this->assertSame(400, $res->get_status());
        $this->assertSame('invalid_shipping_method', $res->get_data()['error']);
        $this->assertSame([], $this->customer->f);
        $this->assertSame([], $this->session->data);
    }

    public function test_nonce_uses_own_action_and_wp_rest_only_is_rejected(): void
    {
        $this->assertTrue($this->endpoint->check_nonce($this->call('get_checkout')));
        $this->assertSame(['woocommerce-trusteed-webmcp-checkout'], $GLOBALS['trusteed_test_nonce_actions']);
        // Sólo X-WP-Nonce (wp_rest): no vale.
        $wpRestOnly = new Trusteed_Test_Rest_Request('{}', ['x-wp-nonce' => 'good-nonce']);
        $this->assertFalse($this->endpoint->check_nonce($wpRestOnly));
        $this->assertSame('X-Trusteed-Nonce', Trusteed_Webmcp_Checkout::NONCE_HEADER);
    }

    public function test_foreign_origin_is_rejected_and_same_or_absent_origin_allowed(): void
    {
        $this->assertFalse($this->endpoint->check_nonce($this->call('get_checkout', [], 'good-nonce', ['origin' => 'https://evil.example'])));
        $this->assertFalse($this->endpoint->check_nonce($this->call('get_checkout', [], 'good-nonce', ['origin' => 'http://shop.example.test'])));
        $this->assertFalse($this->endpoint->check_nonce($this->call('get_checkout', [], 'good-nonce', ['origin' => 'null'])));
        $this->assertTrue($this->endpoint->check_nonce($this->call('get_checkout', [], 'good-nonce', ['origin' => 'https://shop.example.test'])));
        $this->assertTrue($this->endpoint->check_nonce($this->call('get_checkout')));
        $this->assertSame([], $this->session->data);
    }

    public function test_valid_method_accepted_only_because_totals_are_calculated_first(): void
    {
        $this->assertSame([], $GLOBALS['trusteed_test_wc']->shipping()->get_packages());
        $res = $this->endpoint->handle($this->call('update_checkout', ['checkout' => ['fulfillment' => ['method_id' => 'free_shipping:2']]]));
        $this->assertSame(200, $res->get_status());
        $this->assertSame(['free_shipping:2'], $this->session->get('chosen_shipping_methods'));
    }
}
