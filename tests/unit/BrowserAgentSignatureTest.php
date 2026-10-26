<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/class-cart-bridge.php';
require_once __DIR__ . '/../../includes/class-cart-signals.php';
require_once __DIR__ . '/../../includes/class-classic-meta-persister.php';
require_once __DIR__ . '/../../includes/class-enforcement-api-client.php';
require_once __DIR__ . '/../../includes/class-offline-safety-valve-evaluator.php';
require_once __DIR__ . '/../../includes/class-agent-event-webhook.php';
require_once __DIR__ . '/../../includes/class-multi-add-handler.php';
require_once __DIR__ . '/../../includes/class-browser-agent-signature.php';
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

if (!class_exists('WC_Order')) {
    class WC_Order
    {
        /** @var array<string,string> */
        public array $meta = [];
        public function update_meta_data(string $key, string $value): void { $this->meta[$key] = $value; }
        public function save_meta_data(): void {}
    }
}

final class Trusteed_Bas_Empty_Cart
{
    public int $id = 0;
    public function get_total($ctx = 'view') { return '50.00'; }
    public function get_cart(): array { return []; }
    public function get_cart_contents_count(): int { return 0; }
    public function get_applied_coupons(): array { return []; }
}

final class Trusteed_Bas_Request
{
    public function get_json_params(): array { return []; }
    public function get_param(string $key) { return null; }
}

final class Trusteed_Bas_Order
{
    public array $meta = [];
    public function get_id(): int { return 0; }
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

/**
 * Agente que compra NAVEGANDO la web (browser-use): el checkout lo ve como
 * humano salvo que firme RFC 9421. Se verifica la firma contra la API
 * (verify-agent-signature) y, según `browser_agent_signature_mode`, se anota
 * (observe) o se clasifica (enforce).
 */
final class BrowserAgentSignatureTest extends TestCase
{
    private object $client;
    private Trusteed_Test_Wc_Session $session;

    protected function setUp(): void
    {
        $this->session               = new Trusteed_Test_Wc_Session();
        $GLOBALS['trusteed_test_wc'] = (object) ['session' => $this->session, 'cart' => null];
        $GLOBALS['__amcp_wp_remote_post_calls']    = 0;
        $GLOBALS['__amcp_wp_remote_post_response'] = null;
        $this->client = new class('https://api.trusteed.test', 'inst', 'example-secret') extends Trusteed_Enforcement_Api_Client {
            public ?array $last_payload = null;
            public function evaluate(array $payload): Trusteed_Eval_Result
            {
                $this->last_payload = $payload;
                return new Trusteed_Eval_Result(Trusteed_Eval_Outcome::ALLOW, 'ok', 200);
            }
        };
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_HOST']      = 'shop.example.com';
        $_SERVER['REQUEST_URI']    = '/wp-json/wc/store/v1/checkout';
        $_SERVER['HTTPS']          = 'on';
        Stub_Captured_Hooks_Registry::$hooks = [];
        $_POST = [];
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['trusteed_test_wc'], $GLOBALS['trusteed_test_is_checkout']);
        $GLOBALS['__amcp_wp_remote_post_response'] = null;
        Stub_Captured_Hooks_Registry::$hooks = [];
        $_POST = [];
        foreach (['HTTP_COOKIE', 'HTTP_AUTHORIZATION', 'HTTP_PROXY_AUTHORIZATION', 'HTTP_X_WP_NONCE', 'HTTP_SIGNATURE', 'HTTP_SIGNATURE_INPUT', 'HTTP_SIGNATURE_AGENT', 'HTTP_CONTENT_DIGEST', 'HTTP_X_CUSTOM', 'HTTP_HOST', 'REQUEST_URI', 'REQUEST_METHOD', 'HTTPS'] as $k) {
            unset($_SERVER[$k]);
        }
    }

    private function sign_request(): void
    {
        $_SERVER['HTTP_SIGNATURE_INPUT'] = 'sig1=("@method" "@authority" "x-custom");created=1;keyid="k1";tag="web-bot-auth"';
        $_SERVER['HTTP_SIGNATURE']       = 'sig1=:YWJj:';
        $_SERVER['HTTP_SIGNATURE_AGENT'] = '"https://agent.example"';
        $_SERVER['HTTP_X_CUSTOM']        = 'covered';
    }

    private function api_says(string $status, string $provider = 'openai', string $key = 'key-123'): void
    {
        $GLOBALS['__amcp_wp_remote_post_response'] = [
            'response' => ['code' => 200],
            'body'     => json_encode(['verified' => 'verified' === $status, 'verificationStatus' => $status, 'provider' => $provider, 'keyId' => $key]),
        ];
    }

    private static function make_sig(string $mode, $secret = 'embed-secret'): Trusteed_Browser_Agent_Signature
    {
        return new Trusteed_Browser_Agent_Signature('https://api.trusteed.test/api', 'merchant-1', $secret, $mode, 'https://shop.example.com');
    }

    private function run_eval(string $mode, ?Trusteed_Browser_Agent_Signature $sig = null): string
    {
        $sig ??= self::make_sig($mode);
        $enforcer = new Trusteed_Checkout_Enforcer($this->client, 'merchant-1', 'inst', null, 'enforce', null, $sig);
        $m = new ReflectionMethod($enforcer, 'run_evaluation');
        $m->setAccessible(true);
        $out = $m->invoke($enforcer, static fn(): array => ['cartTotalCents' => 5000, 'currency' => 'EUR', 'itemCount' => 1]);
        $this->enforcer = $enforcer;
        return $out;
    }

    private ?Trusteed_Checkout_Enforcer $enforcer = null;

    public function test_without_signature_headers_makes_no_call_and_payload_is_organic(): void
    {
        $this->run_eval('enforce');
        self::assertSame(0, $GLOBALS['__amcp_wp_remote_post_calls']);
        self::assertArrayNotHasKey('endpointClass', $this->client->last_payload);
    }

    public function test_off_mode_makes_no_call(): void
    {
        $this->sign_request();
        $this->api_says('verified');
        $this->run_eval('off');
        self::assertSame(0, $GLOBALS['__amcp_wp_remote_post_calls']);
        self::assertArrayNotHasKey('endpointClass', $this->client->last_payload);
    }

    public function test_observe_verified_calls_records_meta_and_does_not_mark_agent(): void
    {
        $this->sign_request();
        $this->api_says('verified');
        $this->run_eval('observe');
        self::assertSame(1, $GLOBALS['__amcp_wp_remote_post_calls']);
        self::assertArrayNotHasKey('endpointClass', $this->client->last_payload);
        self::assertNull($this->client->last_payload['agentId']);
        $meta = json_decode($this->enforcer->browser_signature_meta(), true);
        self::assertSame(['status' => 'verified', 'provider' => 'openai', 'keyId' => 'key-123'], $meta);
    }

    public function test_request_sent_to_api_carries_s2s_secret_merchant_and_covered_headers(): void
    {
        $this->sign_request();
        $this->api_says('verified');
        $this->run_eval('observe');
        $args = $GLOBALS['__amcp_wp_remote_post_last_args'];
        self::assertSame('https://api.trusteed.test/api/v1/embed/agentic-tools/verify-agent-signature', $GLOBALS['__amcp_wp_remote_post_last_url']);
        self::assertSame('embed-secret', $args['headers']['x-trusteed-s2s-secret']);
        self::assertLessThanOrEqual(1.5, $args['timeout']);
        $body = json_decode($args['body'], true);
        self::assertSame('merchant-1', $body['merchant_id']);
        self::assertSame('POST', $body['method']);
        self::assertSame('https://shop.example.com/wp-json/wc/store/v1/checkout', $body['url']);
        self::assertSame('covered', $body['headers']['x-custom']);
        self::assertSame('shop.example.com', $body['headers']['host']);
        self::assertSame('"https://agent.example"', $body['headers']['signature-agent']);
    }

    public function test_enforce_verified_marks_agent_with_key_identity(): void
    {
        $this->sign_request();
        $this->api_says('verified');
        $this->run_eval('enforce');
        self::assertSame('agentic_endpoint', $this->client->last_payload['endpointClass']);
        self::assertSame('wba:https://agent.example#key-123', $this->client->last_payload['agentId']);
        self::assertSame('verified_signature', $this->client->last_payload['agentIdentityLevel']);
    }

    public function test_enforce_spoofed_marks_agent_without_identity(): void
    {
        $this->sign_request();
        $this->api_says('spoofed');
        $this->run_eval('enforce');
        self::assertSame('agentic_endpoint', $this->client->last_payload['endpointClass']);
        self::assertNull($this->client->last_payload['agentId']);
    }

    public function test_enforce_unverified_stays_human(): void
    {
        $this->sign_request();
        $this->api_says('unverified');
        $this->run_eval('enforce');
        self::assertArrayNotHasKey('endpointClass', $this->client->last_payload);
    }

    public function test_enforce_timeout_or_error_fails_open_as_human(): void
    {
        $this->sign_request();
        $GLOBALS['__amcp_wp_remote_post_response'] = new WP_Error('http_request_failed', 'timed out');
        $this->run_eval('enforce');
        self::assertSame(1, $GLOBALS['__amcp_wp_remote_post_calls']);
        self::assertArrayNotHasKey('endpointClass', $this->client->last_payload);

        $GLOBALS['__amcp_wp_remote_post_response'] = ['response' => ['code' => 503], 'body' => ''];
        $this->run_eval('enforce');
        self::assertArrayNotHasKey('endpointClass', $this->client->last_payload);
    }

    public function test_result_is_cached_within_the_request(): void
    {
        $this->sign_request();
        $this->api_says('verified');
        $sig = self::make_sig('enforce');
        $this->run_eval('enforce', $sig);
        $this->run_eval('enforce', $sig);
        self::assertSame(1, $GLOBALS['__amcp_wp_remote_post_calls']);
    }

    public function test_mode_defaults_to_observe_and_rejects_unknown_values(): void
    {
        self::assertSame('observe', Trusteed_Browser_Agent_Signature::normalize_mode(null));
        self::assertSame('observe', Trusteed_Browser_Agent_Signature::normalize_mode(''));
        self::assertSame('observe', Trusteed_Browser_Agent_Signature::normalize_mode('bogus'));
        self::assertSame('off', Trusteed_Browser_Agent_Signature::normalize_mode(' OFF '));
        self::assertSame('enforce', Trusteed_Browser_Agent_Signature::normalize_mode('enforce'));
    }

    // ── Revisión de seguridad ────────────────────────────────────────────────

    public function test_url_and_host_come_from_the_site_never_from_the_host_header(): void
    {
        $this->sign_request();
        $this->api_says('verified');
        $_SERVER['HTTP_HOST']   = 'evil.example';
        $_SERVER['REQUEST_URI'] = '/wp-json/wc/store/v1/checkout?a=1';
        $this->run_eval('observe');
        $body = json_decode($GLOBALS['__amcp_wp_remote_post_last_args']['body'], true);
        self::assertSame('https://shop.example.com/wp-json/wc/store/v1/checkout?a=1', $body['url']);
        self::assertSame('shop.example.com', $body['headers']['host']);
    }

    public function test_request_uri_cannot_smuggle_another_authority(): void
    {
        $this->sign_request();
        $this->api_says('verified');
        $_SERVER['REQUEST_URI'] = '//evil.example/x?y=1';
        $this->run_eval('observe');
        $body = json_decode($GLOBALS['__amcp_wp_remote_post_last_args']['body'], true);
        self::assertSame('https://shop.example.com/x?y=1', $body['url']);
    }

    public function test_cookie_authorization_and_nonce_are_never_forwarded_even_if_covered(): void
    {
        $_SERVER['HTTP_SIGNATURE_INPUT'] = 'sig1=("@authority" "cookie" "authorization" "proxy-authorization" "x-wp-nonce" "x-custom");keyid="k1"';
        $_SERVER['HTTP_SIGNATURE']       = 'sig1=:YWJj:';
        $_SERVER['HTTP_SIGNATURE_AGENT'] = '"https://agent.example"';
        $_SERVER['HTTP_X_CUSTOM']        = 'covered';
        $_SERVER['HTTP_COOKIE']          = 'wordpress_logged_in=secret';
        $_SERVER['HTTP_AUTHORIZATION']   = 'Bearer secret';
        $_SERVER['HTTP_PROXY_AUTHORIZATION'] = 'Basic secret';
        $_SERVER['HTTP_X_WP_NONCE']      = 'nonce';
        $this->api_says('verified');
        $this->run_eval('observe');
        $raw = $GLOBALS['__amcp_wp_remote_post_last_args']['body'];
        $headers = json_decode($raw, true)['headers'];
        foreach (['cookie', 'authorization', 'proxy-authorization', 'x-wp-nonce'] as $h) {
            self::assertArrayNotHasKey($h, $headers);
        }
        self::assertStringNotContainsString('secret', $raw);
        self::assertSame('covered', $headers['x-custom']);
    }

    public function test_verified_without_authority_or_target_uri_covered_is_not_verified(): void
    {
        $_SERVER['HTTP_SIGNATURE_INPUT'] = 'sig1=("@method" "x-custom");keyid="k1"';
        $_SERVER['HTTP_SIGNATURE']       = 'sig1=:YWJj:';
        $_SERVER['HTTP_SIGNATURE_AGENT'] = '"https://agent.example"';
        $this->api_says('verified');
        $this->run_eval('enforce');
        self::assertArrayNotHasKey('endpointClass', $this->client->last_payload);
        self::assertNull($this->client->last_payload['agentId']);
        self::assertSame('unverified', json_decode($this->enforcer->browser_signature_meta(), true)['status']);
    }

    public function test_target_uri_covered_is_enough(): void
    {
        $_SERVER['HTTP_SIGNATURE_INPUT'] = 'sig1=("@method" "@target-uri");keyid="k1"';
        $_SERVER['HTTP_SIGNATURE']       = 'sig1=:YWJj:';
        $_SERVER['HTTP_SIGNATURE_AGENT'] = '"https://agent.example"';
        $this->api_says('verified');
        $this->run_eval('enforce');
        self::assertSame('wba:https://agent.example#key-123', $this->client->last_payload['agentId']);
    }

    public function test_identity_is_bound_to_the_signature_agent_origin(): void
    {
        $this->sign_request();
        $_SERVER['HTTP_SIGNATURE_AGENT'] = 'sig1="https://Agent.Example:8443/.well-known/x"';
        $this->api_says('verified');
        $this->run_eval('enforce');
        self::assertSame('wba:https://agent.example:8443#key-123', $this->client->last_payload['agentId']);
    }

    public function test_unparseable_or_non_https_signature_agent_is_not_verified(): void
    {
        foreach (['"not a url"', '"http://agent.example"'] as $agent) {
            $this->sign_request();
            $_SERVER['HTTP_SIGNATURE_AGENT'] = $agent;
            $this->api_says('verified');
            $this->run_eval('enforce');
            self::assertArrayNotHasKey('endpointClass', $this->client->last_payload, $agent);
        }
    }

    public function test_incomplete_or_mismatched_headers_make_zero_calls(): void
    {
        $cases = [
            'sin Signature'        => ['HTTP_SIGNATURE'],
            'sin Signature-Input'  => ['HTTP_SIGNATURE_INPUT'],
            'sin Signature-Agent'  => ['HTTP_SIGNATURE_AGENT'],
        ];
        foreach ($cases as $name => $drop) {
            $this->sign_request();
            unset($_SERVER[$drop[0]]);
            $this->api_says('verified');
            $this->run_eval('enforce');
            self::assertSame(0, $GLOBALS['__amcp_wp_remote_post_calls'], $name);
        }
        $this->sign_request();
        $_SERVER['HTTP_SIGNATURE'] = 'other=:YWJj:';
        $this->run_eval('enforce');
        self::assertSame(0, $GLOBALS['__amcp_wp_remote_post_calls'], 'etiqueta distinta');
    }

    public function test_secret_is_decrypted_lazily_only_when_a_call_is_needed(): void
    {
        $calls = 0;
        $sig   = self::make_sig('enforce', function () use (&$calls): string { $calls++; return 'lazy-secret'; });
        $this->run_eval('enforce', $sig);
        self::assertSame(0, $calls, 'sin firma no se descifra');
        $this->sign_request();
        $this->api_says('verified');
        $this->run_eval('enforce', $sig);
        self::assertSame(1, $calls);
        self::assertSame('lazy-secret', $GLOBALS['__amcp_wp_remote_post_last_args']['headers']['x-trusteed-s2s-secret']);
    }

    public function test_without_a_signature_the_payload_has_no_identity_level(): void
    {
        $this->run_eval('enforce');
        self::assertArrayNotHasKey('agentIdentityLevel', $this->client->last_payload);
    }

    public function test_a_verified_token_or_trusted_did_wins_over_the_signature(): void
    {
        $this->sign_request();
        $this->api_says('verified');
        $sig      = self::make_sig('enforce');
        $enforcer = new Trusteed_Checkout_Enforcer($this->client, 'merchant-1', 'inst', null, 'enforce', null, $sig);
        $prop = new ReflectionProperty($enforcer, 'trusted_agent_did');
        $prop->setAccessible(true);
        $prop->setValue($enforcer, 'did:web:trusted.example');
        $m = new ReflectionMethod($enforcer, 'run_evaluation');
        $m->setAccessible(true);
        $m->invoke($enforcer, static fn(): array => ['cartTotalCents' => 5000, 'currency' => 'EUR', 'itemCount' => 1]);
        self::assertSame('did:web:trusted.example', $this->client->last_payload['agentId']);
    }

    public function test_meta_is_written_on_the_blocks_path(): void
    {
        $this->sign_request();
        $this->api_says('verified');
        $this->session->data = [];
        $GLOBALS['trusteed_test_wc']->cart = new Trusteed_Bas_Empty_Cart();
        $enforcer = new Trusteed_Checkout_Enforcer($this->client, 'merchant-1', 'inst', null, 'enforce', null, self::make_sig('observe'));
        $order    = new Trusteed_Bas_Order();
        $enforcer->enforce_rules_blocks($order, new Trusteed_Bas_Request());
        $meta = json_decode($order->meta[Trusteed_Browser_Agent_Signature::META_KEY], true);
        self::assertSame(['status' => 'verified', 'provider' => 'openai', 'keyId' => 'key-123'], $meta);
    }

    public function test_meta_is_stamped_on_the_classic_path(): void
    {
        $this->sign_request();
        $this->api_says('verified');
        $GLOBALS['trusteed_test_wc']->cart = new Trusteed_Bas_Empty_Cart();
        $enforcer = new Trusteed_Checkout_Enforcer($this->client, 'merchant-1', 'inst', null, 'enforce', null, self::make_sig('observe'));
        $enforcer->enforce_rules_classic();
        $hooks = Stub_Captured_Hooks_Registry::$hooks['woocommerce_checkout_create_order'] ?? [];
        self::assertCount(1, $hooks);
        $order = new WC_Order();
        $hooks[0]($order);
        self::assertSame('verified', json_decode($order->meta[Trusteed_Browser_Agent_Signature::META_KEY], true)['status']);
    }
}
