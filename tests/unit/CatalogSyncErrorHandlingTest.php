<?php

declare(strict_types=1);

/**
 * F3.T6 — Gap #6 honest sync error handling.
 *
 * Verifies:
 *   - log_sync_outcome() handles WP_Error, non-array, HTTP 4xx/5xx, HTTP 2xx
 *     without throwing and without leaking secrets, AND emits structured
 *     error_log lines with the expected `[amcp.catalog_sync]` tag + context
 *     + sanitised entity_id.
 *   - sync_full_catalog() option-write semantics:
 *       * agenticmcp_last_sync_attempt always updates
 *       * agenticmcp_last_sync_success only updates on full success
 *       * legacy agenticmcp_last_sync stays in sync with success only
 *
 * F6.PHP2 / hygiene — previous version used assertTrue(true) for the
 * log-outcome paths. This version captures error_log via per-test temp
 * file (ini_set('error_log', ...)) and asserts on log shape.
 *
 * No WordPress runtime is loaded — we stub the few WP globals the SUT calls.
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}

// -----------------------------------------------------------------------------
// Minimal WP option store + helpers
// -----------------------------------------------------------------------------
if (!isset($GLOBALS['__amcp_options_store'])) {
    $GLOBALS['__amcp_options_store'] = [];
}

if (!function_exists('get_option')) {
    function get_option(string $key, $default = false)
    {
        return $GLOBALS['__amcp_options_store'][$key] ?? $default;
    }
}

if (!function_exists('update_option')) {
    function update_option(string $key, $value): bool
    {
        $GLOBALS['__amcp_options_store'][$key] = $value;
        return true;
    }
}

if (!function_exists('absint')) {
    function absint($v): int
    {
        return abs((int) $v);
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing): bool
    {
        return $thing instanceof WP_Error;
    }
}

if (!class_exists('WP_Error')) {
    final class WP_Error
    {
        /** @var string */
        private $code;
        /** @var string */
        private $message;

        public function __construct(string $code = '', string $message = '')
        {
            $this->code = $code;
            $this->message = $message;
        }

        public function get_error_code(): string
        {
            return $this->code;
        }

        public function get_error_message(): string
        {
            return $this->message;
        }
    }
}

// Stub the api client type (the SUT only needs the symbol for typehint).
if (!class_exists('AgenticMCP_Api_Client')) {
    class AgenticMCP_Api_Client
    {
        /** @var array */
        public $next_response = ['status' => 200, 'body' => []];

        public function post(string $endpoint, array $body)
        {
            return $this->next_response;
        }
    }
}

require_once __DIR__ . '/../../includes/class-catalog-sync.php';

use PHPUnit\Framework\TestCase;

final class CatalogSyncErrorHandlingTest extends TestCase
{
    /** Path of the per-test captured error_log stream. */
    private string $error_log_file;
    /** Original error_log ini value, restored in tearDown. */
    private string $original_error_log;

    protected function setUp(): void
    {
        $this->original_error_log = (string) ini_get('error_log');
        $tmp = tempnam(sys_get_temp_dir(), 'amcp_sync_log_');
        if (false === $tmp) {
            $this->fail('Unable to allocate temp file for error_log capture.');
        }
        $this->error_log_file = $tmp;
        ini_set('error_log', $this->error_log_file);
    }

    protected function tearDown(): void
    {
        ini_set('error_log', $this->original_error_log);
        if (file_exists($this->error_log_file)) {
            @unlink($this->error_log_file);
        }
    }

    private function captured_log(): string
    {
        clearstatcache(true, $this->error_log_file);
        return file_exists($this->error_log_file)
            ? (string) file_get_contents($this->error_log_file)
            : '';
    }

    private function makeSync(): AgenticMCP_Catalog_Sync
    {
        return new AgenticMCP_Catalog_Sync(new AgenticMCP_Api_Client());
    }

    private function invokeLog(AgenticMCP_Catalog_Sync $sync, string $context, int $entity_id, $response): void
    {
        $ref = new \ReflectionClass($sync);
        $method = $ref->getMethod('log_sync_outcome');
        $method->setAccessible(true);
        $method->invoke($sync, $context, $entity_id, $response);
    }

    public function test_log_sync_outcome_swallows_wp_error_without_throw(): void
    {
        $sync = $this->makeSync();
        $this->invokeLog($sync, 'on_product_save', 42, new WP_Error('agenticmcp_api_timeout', 'timed out'));

        $log = $this->captured_log();
        $this->assertStringContainsString('[amcp.catalog_sync]', $log);
        $this->assertStringContainsString('on_product_save', $log);
        $this->assertStringContainsString('WP_Error', $log);
        $this->assertStringContainsString('agenticmcp_api_timeout', $log);
        $this->assertStringContainsString('entity=42', $log);
    }

    public function test_log_sync_outcome_handles_http_4xx_without_throw(): void
    {
        $sync = $this->makeSync();
        $this->invokeLog($sync, 'on_stock_change', 7, ['status' => 422, 'body' => ['error' => 'bad payload']]);

        $log = $this->captured_log();
        $this->assertStringContainsString('[amcp.catalog_sync]', $log);
        $this->assertStringContainsString('on_stock_change', $log);
        $this->assertStringContainsString('HTTP 422', $log);
        $this->assertStringContainsString('entity=7', $log);
    }

    public function test_log_sync_outcome_handles_http_5xx_without_throw(): void
    {
        $sync = $this->makeSync();
        $this->invokeLog($sync, 'on_product_delete', 99, ['status' => 500, 'body' => null]);

        $log = $this->captured_log();
        $this->assertStringContainsString('[amcp.catalog_sync]', $log);
        $this->assertStringContainsString('on_product_delete', $log);
        $this->assertStringContainsString('HTTP 500', $log);
        $this->assertStringContainsString('entity=99', $log);
    }

    public function test_log_sync_outcome_silent_on_2xx(): void
    {
        $sync = $this->makeSync();
        $this->invokeLog($sync, 'on_product_save', 1, ['status' => 200, 'body' => ['data' => []]]);

        $log = $this->captured_log();
        // Success path MUST NOT emit anything tagged for this subsystem.
        $this->assertStringNotContainsString('[amcp.catalog_sync]', $log);
    }

    public function test_log_sync_outcome_handles_unexpected_response_type(): void
    {
        $sync = $this->makeSync();
        $this->invokeLog($sync, 'on_product_save', 1, 'unexpected-string');

        $log = $this->captured_log();
        $this->assertStringContainsString('[amcp.catalog_sync]', $log);
        $this->assertStringContainsString('on_product_save', $log);
        $this->assertStringContainsString('unexpected response type', $log);
        $this->assertStringContainsString('entity=1', $log);
    }

    public function test_log_sync_outcome_sanitises_negative_entity_id(): void
    {
        $sync = $this->makeSync();
        // absint(-42) → 42 — proves the helper does not propagate raw user data.
        $this->invokeLog($sync, 'on_product_save', -42, ['status' => 500, 'body' => null]);

        $log = $this->captured_log();
        $this->assertStringContainsString('entity=42', $log);
        $this->assertStringNotContainsString('entity=-42', $log);
    }

    public function test_log_sync_outcome_does_not_leak_api_key(): void
    {
        // Seed a sentinel secret in the option store so we can grep for it.
        $sentinel_secret = 'SENTINEL_API_KEY_DO_NOT_LOG_abc123';
        $GLOBALS['__amcp_options_store']['agenticmcp_api_key'] = $sentinel_secret;

        $sync = $this->makeSync();
        $this->invokeLog($sync, 'on_product_save', 1, ['status' => 500, 'body' => ['secret' => $sentinel_secret]]);

        $log = $this->captured_log();
        $this->assertStringNotContainsString($sentinel_secret, $log);
    }

    public function test_sync_full_catalog_option_semantics_on_success(): void
    {
        // Reset options.
        $GLOBALS['__amcp_options_store'] = [];

        // Stub wc_get_products to return [] so the loop exits immediately with 0 errors.
        if (!function_exists('wc_get_products')) {
            function wc_get_products($args): array
            {
                return [];
            }
        }

        $sync = $this->makeSync();
        $result = $sync->sync_full_catalog();

        $this->assertSame(0, $result['total_errors']);
        $this->assertArrayHasKey('last_sync_attempt', $result);
        $this->assertArrayHasKey('last_sync_success', $result);
        $this->assertSame($result['last_sync_attempt'], $result['last_sync_success']);
        $this->assertSame($result['last_sync_attempt'], $result['last_sync']);

        // Options written.
        $this->assertSame($result['last_sync_attempt'], get_option('agenticmcp_last_sync_attempt'));
        $this->assertSame($result['last_sync_attempt'], get_option('agenticmcp_last_sync_success'));
        $this->assertSame($result['last_sync_attempt'], get_option('agenticmcp_last_sync'));
    }
}
