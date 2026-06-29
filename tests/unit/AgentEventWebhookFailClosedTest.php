<?php

declare(strict_types=1);

/**
 * F5.S3 — Plugin fail-closed when enforcement_hmac_secret is empty.
 *
 * Asserts:
 *   - build_signed_headers() returns null when hmac_secret is empty.
 *   - build_signed_headers() returns a fully-signed header array when
 *     hmac_secret is set, and the literal `'dev-bypass'` is NEVER emitted.
 *   - post_signed() short-circuits (no wp_remote_post call) when secret
 *     is empty.
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}

// Stub WP helpers used by the SUT.
if (!function_exists('wp_json_encode')) {
    function wp_json_encode($data) {
        return json_encode($data);
    }
}
if (!function_exists('update_option')) {
    $GLOBALS['__amcp_option_calls'] = [];
    function update_option($name, $value, $autoload = null) {
        $GLOBALS['__amcp_option_calls'][$name] = $value;
        return true;
    }
}
if (!function_exists('wp_remote_post')) {
    $GLOBALS['__amcp_wp_remote_post_calls'] = 0;
    function wp_remote_post($url, $args) {
        $GLOBALS['__amcp_wp_remote_post_calls']++;
        return ['response' => ['code' => 200], 'body' => '{}'];
    }
}
if (!function_exists('wp_remote_retrieve_response_code')) {
    function wp_remote_retrieve_response_code($response) {
        return is_array($response) && isset($response['response']['code'])
            ? (int) $response['response']['code']
            : 0;
    }
}
if (!function_exists('is_wp_error')) {
    function is_wp_error($x) {
        return false;
    }
}
if (!function_exists('add_action')) {
    function add_action() { /* noop */ }
}
if (!function_exists('wc_get_order')) {
    function wc_get_order($id) {
        return null;
    }
}

require_once __DIR__ . '/../../includes/class-agent-event-webhook.php';

use PHPUnit\Framework\TestCase;

final class AgentEventWebhookFailClosedTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['__amcp_option_calls'] = [];
        $GLOBALS['__amcp_wp_remote_post_calls'] = 0;
    }

    public function test_build_signed_headers_returns_null_when_secret_empty(): void
    {
        $webhook = new Amcp_Agent_Event_Webhook(
            'https://api.example.com',
            'install-1',
            '', // empty secret — fail-closed path
            'merchant-1'
        );

        $method = new ReflectionMethod($webhook, 'build_signed_headers');
        $method->setAccessible(true);
        $headers = $method->invoke($webhook, '{"hello":"world"}');

        $this->assertNull($headers, 'build_signed_headers must return null when secret missing (fail-closed)');
        $this->assertArrayHasKey(
            Amcp_Agent_Event_Webhook::NOTICE_OPTION_HMAC_MISSING,
            $GLOBALS['__amcp_option_calls'],
            'admin-notice flag must be set so merchant is alerted'
        );
    }

    public function test_build_signed_headers_signs_correctly_when_secret_present(): void
    {
        $webhook = new Amcp_Agent_Event_Webhook(
            'https://api.example.com',
            'install-1',
            'super-secret-hmac-key',
            'merchant-1'
        );

        $method = new ReflectionMethod($webhook, 'build_signed_headers');
        $method->setAccessible(true);
        $headers = $method->invoke($webhook, '{"hello":"world"}');

        $this->assertIsArray($headers);
        $this->assertArrayHasKey('X-Trusteed-Signature', $headers);
        $this->assertStringNotContainsString(
            'dev-bypass',
            $headers['X-Trusteed-Signature'],
            'legacy dev-bypass literal must never appear in signed headers'
        );
        $this->assertMatchesRegularExpression(
            '/^t=\d+,s=[0-9a-f]{64}$/',
            $headers['X-Trusteed-Signature'],
            'signature must be t=<unix>,s=<hex-sha256>'
        );
    }

    public function test_post_signed_does_not_dispatch_when_secret_empty(): void
    {
        $webhook = new Amcp_Agent_Event_Webhook(
            'https://api.example.com',
            'install-1',
            '', // empty
            'merchant-1'
        );

        $method = new ReflectionMethod($webhook, 'post_signed');
        $method->setAccessible(true);
        $method->invoke($webhook, ['kind' => 'refunded', 'orderId' => '1']);

        $this->assertSame(
            0,
            $GLOBALS['__amcp_wp_remote_post_calls'],
            'wp_remote_post must NOT be invoked when fail-closed (no placeholder dispatch)'
        );
    }
}
