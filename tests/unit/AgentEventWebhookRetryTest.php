<?php

declare(strict_types=1);

/**
 * F6.PHP1 — Coverage for the wp-cron retry pipeline:
 *   - S7  : handle_retry() payload validation (anti event injection)
 *   - CR1 : schedule_retry() body size cap (anti wp_options bloat)
 *   - CR1-backoff : exact 60s / 120s / 240s sequence + naming
 *
 * Asserts the public + private surface of Amcp_Agent_Event_Webhook without a
 * WordPress runtime. WP helpers are stubbed below; behaviour is driven by
 * globals so the same stubs can coexist with the FailClosed test file in the
 * same PHPUnit suite (first-loaded wins via `function_exists` guards).
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}

// ----------------------------------------------------------------------------
// WP helper stubs. Each guarded with function_exists so order with sibling
// test files (e.g. AgentEventWebhookFailClosedTest) does not matter.
// ----------------------------------------------------------------------------

if (!function_exists('wp_json_encode')) {
    function wp_json_encode($data) {
        return json_encode($data);
    }
}
if (!function_exists('absint')) {
    function absint($value) {
        return abs((int) $value);
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
        $GLOBALS['__amcp_wp_remote_post_last_url']  = $url;
        $GLOBALS['__amcp_wp_remote_post_last_args'] = $args;
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
// wp_schedule_single_event is unique to this test family — captures every call
// so the sequence test can inspect the exact timestamp/hook/args triple.
if (!function_exists('wp_schedule_single_event')) {
    $GLOBALS['__amcp_wp_schedule_single_event_calls'] = [];
    function wp_schedule_single_event($timestamp, $hook, $args = []) {
        $GLOBALS['__amcp_wp_schedule_single_event_calls'][] = [
            'timestamp' => $timestamp,
            'hook'      => $hook,
            'args'      => $args,
        ];
        return true;
    }
}

require_once __DIR__ . '/../../includes/class-agent-event-webhook.php';

use PHPUnit\Framework\TestCase;

final class AgentEventWebhookRetryTest extends TestCase
{
    private const MERCHANT_ID     = 'merchant-1';
    private const INSTALLATION_ID = 'install-1';
    private const HMAC_SECRET     = 'super-secret-hmac-key';

    protected function setUp(): void
    {
        $GLOBALS['__amcp_wp_schedule_single_event_calls'] = [];
        $GLOBALS['__amcp_wp_remote_post_calls']           = 0;
        $GLOBALS['__amcp_wp_remote_post_last_url']        = null;
        $GLOBALS['__amcp_wp_remote_post_last_args']       = null;
        $GLOBALS['__amcp_option_calls']                   = [];
    }

    /**
     * Helper: minimal webhook with a non-empty HMAC secret so signed-header
     * construction succeeds.
     */
    private function makeWebhook(): Amcp_Agent_Event_Webhook
    {
        return new Amcp_Agent_Event_Webhook(
            'https://api.example.com',
            self::INSTALLATION_ID,
            self::HMAC_SECRET,
            self::MERCHANT_ID
        );
    }

    /**
     * Helper: build a well-formed cron payload that should pass every S7 check.
     */
    private function validCronPayload(int $attempt = 2, string $kind = 'refunded'): array
    {
        $body = json_encode([
            'merchantId'     => self::MERCHANT_ID,
            'installationId' => self::INSTALLATION_ID,
            'platform'       => 'woocommerce',
            'agentId'        => 'did:example:agent',
            'orderId'        => '42',
            'kind'           => $kind,
            'timestamp'      => '2026-05-24T00:00:00Z',
        ]);
        return [
            'body'    => $body,
            'attempt' => $attempt,
            'reason'  => 'http_503',
        ];
    }

    private function invokePrivate(Amcp_Agent_Event_Webhook $webhook, string $method, array $args = [])
    {
        $ref = new ReflectionMethod($webhook, $method);
        $ref->setAccessible(true);
        return $ref->invokeArgs($webhook, $args);
    }

    // ------------------------------------------------------------------------
    // CR1-backoff — exact 60s / 120s / 240s sequence.
    // ------------------------------------------------------------------------

    public function test_retry_sequence_is_exactly_60_120_240_seconds(): void
    {
        $webhook = $this->makeWebhook();
        $body    = '{"merchantId":"' . self::MERCHANT_ID . '","installationId":"' . self::INSTALLATION_ID . '","kind":"refunded"}';

        $before = time();
        $this->invokePrivate($webhook, 'schedule_retry', [$body, 1, 'http_503']);
        $this->invokePrivate($webhook, 'schedule_retry', [$body, 2, 'http_503']);
        $this->invokePrivate($webhook, 'schedule_retry', [$body, 3, 'http_503']);
        $after = time();

        $calls = $GLOBALS['__amcp_wp_schedule_single_event_calls'];
        $this->assertCount(3, $calls, 'three schedule_retry invocations must yield three cron rows');

        $delays = array_map(
            static fn (array $c): int => (int) $c['timestamp'] - $before,
            $calls
        );

        // Use a one-second tolerance because time() may roll between $before
        // and the schedule_retry call.
        $tolerance = ($after - $before) + 1;
        $this->assertGreaterThanOrEqual(60, $delays[0]);
        $this->assertLessThanOrEqual(60 + $tolerance, $delays[0]);
        $this->assertGreaterThanOrEqual(120, $delays[1]);
        $this->assertLessThanOrEqual(120 + $tolerance, $delays[1]);
        $this->assertGreaterThanOrEqual(240, $delays[2]);
        $this->assertLessThanOrEqual(240 + $tolerance, $delays[2]);

        // The attempt index stored in the cron payload must be completed+1.
        $this->assertSame(2, $calls[0]['args'][0]['attempt']);
        $this->assertSame(3, $calls[1]['args'][0]['attempt']);
        $this->assertSame(4, $calls[2]['args'][0]['attempt']);
    }

    // ------------------------------------------------------------------------
    // CR1 — Body size cap.
    // ------------------------------------------------------------------------

    public function test_payload_size_over_64kb_skips_retry(): void
    {
        $webhook  = $this->makeWebhook();
        $oversize = str_repeat('a', 65 * 1024); // 66560 bytes > 64KB cap

        // Capture error_log via output buffer redirection.
        ini_set('log_errors', '1');
        ini_set('error_log', '/dev/null');

        $this->invokePrivate($webhook, 'schedule_retry', [$oversize, 1, 'http_503']);

        $this->assertCount(
            0,
            $GLOBALS['__amcp_wp_schedule_single_event_calls'],
            'oversize bodies must not be persisted into wp_options'
        );
    }

    // ------------------------------------------------------------------------
    // S7 — handle_retry payload validation.
    // ------------------------------------------------------------------------

    public function test_handle_retry_rejects_payload_with_wrong_merchant_id(): void
    {
        $webhook = $this->makeWebhook();
        $payload = $this->validCronPayload();

        // Tamper with merchantId — simulate attacker-injected cron row.
        $decoded               = json_decode($payload['body'], true);
        $decoded['merchantId'] = 'attacker';
        $payload['body']       = json_encode($decoded);

        $webhook->handle_retry($payload);

        $this->assertSame(
            0,
            $GLOBALS['__amcp_wp_remote_post_calls'],
            'merchantId mismatch must abort dispatch (no signed POST)'
        );
    }

    public function test_handle_retry_rejects_payload_with_invalid_kind(): void
    {
        $webhook = $this->makeWebhook();
        $payload = $this->validCronPayload(2, 'evil_kind');

        $webhook->handle_retry($payload);

        $this->assertSame(
            0,
            $GLOBALS['__amcp_wp_remote_post_calls'],
            'disallowed kind must abort dispatch'
        );
    }

    public function test_handle_retry_rejects_payload_with_invalid_installation_id(): void
    {
        $webhook = $this->makeWebhook();
        $payload = $this->validCronPayload();

        $decoded                   = json_decode($payload['body'], true);
        $decoded['installationId'] = 'other-install';
        $payload['body']           = json_encode($decoded);

        $webhook->handle_retry($payload);

        $this->assertSame(0, $GLOBALS['__amcp_wp_remote_post_calls']);
    }

    public function test_handle_retry_rejects_malformed_json_body(): void
    {
        $webhook = $this->makeWebhook();
        $payload = [
            'body'    => 'not json at all',
            'attempt' => 2,
            'reason'  => 'http_503',
        ];

        $webhook->handle_retry($payload);

        $this->assertSame(0, $GLOBALS['__amcp_wp_remote_post_calls']);
    }

    public function test_handle_retry_rejects_legacy_payload_without_merchant_id(): void
    {
        // Backward-compat check: payloads from older code versions that did
        // not include merchantId MUST be rejected fail-closed, never sent.
        $webhook = $this->makeWebhook();
        $payload = [
            'body'    => json_encode([
                'installationId' => self::INSTALLATION_ID,
                'kind'           => 'refunded',
            ]),
            'attempt' => 2,
            'reason'  => 'http_503',
        ];

        $webhook->handle_retry($payload);

        $this->assertSame(0, $GLOBALS['__amcp_wp_remote_post_calls']);
    }

    public function test_handle_retry_max_attempts_4_returns_without_dispatch(): void
    {
        $webhook = $this->makeWebhook();
        $payload = $this->validCronPayload(4);

        $webhook->handle_retry($payload);

        $this->assertSame(
            0,
            $GLOBALS['__amcp_wp_remote_post_calls'],
            'attempt past MAX_RETRY_ATTEMPTS must short-circuit before dispatch'
        );
    }

    public function test_handle_retry_rejects_negative_attempt(): void
    {
        $webhook = $this->makeWebhook();
        $payload = $this->validCronPayload();
        $payload['attempt'] = 0;

        $webhook->handle_retry($payload);

        $this->assertSame(0, $GLOBALS['__amcp_wp_remote_post_calls']);
    }

    public function test_handle_retry_rejects_oversize_body(): void
    {
        $webhook = $this->makeWebhook();
        $payload = $this->validCronPayload();
        // Pad body beyond 64KB while keeping it valid JSON.
        $decoded            = json_decode($payload['body'], true);
        $decoded['padding'] = str_repeat('x', 70 * 1024);
        $payload['body']    = json_encode($decoded);

        $webhook->handle_retry($payload);

        $this->assertSame(0, $GLOBALS['__amcp_wp_remote_post_calls']);
    }

    public function test_handle_retry_valid_payload_dispatches(): void
    {
        $webhook = $this->makeWebhook();
        $payload = $this->validCronPayload(2, 'order_placed');

        $webhook->handle_retry($payload);

        $this->assertSame(
            1,
            $GLOBALS['__amcp_wp_remote_post_calls'],
            'a well-formed payload must trigger a single signed POST'
        );

        $args = $GLOBALS['__amcp_wp_remote_post_last_args'];
        $this->assertIsArray($args);
        $this->assertArrayHasKey('headers', $args);
        $this->assertArrayHasKey('X-Trusteed-Signature', $args['headers']);
        $this->assertMatchesRegularExpression(
            '/^t=\d+,s=[0-9a-f]{64}$/',
            $args['headers']['X-Trusteed-Signature']
        );
    }

    public function test_handle_retry_rejects_non_array_payload(): void
    {
        $webhook = $this->makeWebhook();
        $webhook->handle_retry('not an array');
        $this->assertSame(0, $GLOBALS['__amcp_wp_remote_post_calls']);
    }

    public function test_handle_retry_rejects_missing_attempt(): void
    {
        $webhook = $this->makeWebhook();
        $payload = $this->validCronPayload();
        unset($payload['attempt']);

        $webhook->handle_retry($payload);

        $this->assertSame(0, $GLOBALS['__amcp_wp_remote_post_calls']);
    }
}
