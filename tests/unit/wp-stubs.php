<?php

declare(strict_types=1);

/**
 * Canonical WordPress function stubs shared across all unit test files.
 *
 * Why this exists: PHPUnit loads every test file into a single PHP process.
 * Each test file historically defined its own `if (!function_exists(...))`
 * stubs, so the FIRST file (alphabetically) to declare a function won
 * process-wide and silently overrode the behaviour later files relied on
 * (e.g. a no-op `add_action` shadowing a capturing one, or an
 * `update_option` writing to a different backing store than `get_option`
 * reads). That made results order-dependent: files passed in isolation but
 * failed in the full suite.
 *
 * This file is required FIRST from bootstrap.php, so these canonical
 * definitions win deterministically and every per-file guard is skipped.
 * Behaviours are the UNION of what the individual test files expect, using
 * the same `$GLOBALS` keys each file's setUp() already resets — so per-test
 * isolation keeps working unchanged.
 *
 * Classes with a single definition (WC_Order) stay in their owning test file:
 * only FUNCTIONS collided, not classes. WP_Error graduated to this file once a
 * second test file needed it (see its declaration below).
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}

// Shared hook registry read by ClassicCheckoutMetaPersistenceTest /
// MultiAddHandlerTest. Defined here so the canonical add_action can capture
// into it regardless of file load order.
if (!class_exists('Stub_Captured_Hooks_Registry')) {
    final class Stub_Captured_Hooks_Registry
    {
        /** @var array<string,callable[]> */
        public static array $hooks = [];
    }
}

// Backing stores. Initialised once; each test's setUp() resets the relevant
// keys, so we only seed them if absent.
$GLOBALS['__amcp_options_store']                  = $GLOBALS['__amcp_options_store']                  ?? [];
$GLOBALS['__amcp_option_calls']                   = $GLOBALS['__amcp_option_calls']                   ?? [];
$GLOBALS['__amcp_wp_remote_post_calls']           = $GLOBALS['__amcp_wp_remote_post_calls']           ?? 0;
$GLOBALS['__amcp_wp_remote_post_last_url']        = $GLOBALS['__amcp_wp_remote_post_last_url']        ?? null;
$GLOBALS['__amcp_wp_remote_post_last_args']       = $GLOBALS['__amcp_wp_remote_post_last_args']       ?? null;
$GLOBALS['__amcp_wp_schedule_single_event_calls'] = $GLOBALS['__amcp_wp_schedule_single_event_calls'] ?? [];
$GLOBALS['__amcp_transients_store']               = $GLOBALS['__amcp_transients_store']               ?? [];
$GLOBALS['__amcp_deleted_options']                = $GLOBALS['__amcp_deleted_options']                ?? [];
$GLOBALS['__amcp_deleted_transients']             = $GLOBALS['__amcp_deleted_transients']             ?? [];

if (!function_exists('add_action')) {
    function add_action(string $hook, callable $cb, int $prio = 10, int $args = 1): void
    {
        Stub_Captured_Hooks_Registry::$hooks[$hook][] = $cb;
    }
}

if (!function_exists('update_option')) {
    /**
     * Writes to BOTH backing stores so callers that inspect either work:
     *  - `__amcp_options_store` is read by the canonical get_option (CatalogSync).
     *  - `__amcp_option_calls` is asserted directly by the AgentEvent* tests.
     */
    function update_option($name, $value, $autoload = null): bool
    {
        $GLOBALS['__amcp_options_store'][$name] = $value;
        $GLOBALS['__amcp_option_calls'][$name]  = $value;
        return true;
    }
}

if (!function_exists('get_option')) {
    function get_option($key, $default = false)
    {
        return $GLOBALS['__amcp_options_store'][$key] ?? $default;
    }
}

if (!function_exists('delete_option')) {
    function delete_option($key): bool
    {
        $existed = array_key_exists($key, $GLOBALS['__amcp_options_store']);
        unset($GLOBALS['__amcp_options_store'][$key], $GLOBALS['__amcp_option_calls'][$key]);
        $GLOBALS['__amcp_deleted_options'][] = $key;
        return $existed;
    }
}

if (!function_exists('delete_transient')) {
    function delete_transient($key): bool
    {
        $existed = array_key_exists($key, $GLOBALS['__amcp_transients_store']);
        unset($GLOBALS['__amcp_transients_store'][$key]);
        $GLOBALS['__amcp_deleted_transients'][] = $key;
        return $existed;
    }
}

if (!function_exists('get_transient')) {
    function get_transient($key)
    {
        return $GLOBALS['__amcp_transients_store'][$key] ?? false;
    }
}

if (!function_exists('wp_json_encode')) {
    function wp_json_encode($data)
    {
        return json_encode($data);
    }
}

if (!function_exists('absint')) {
    function absint($value): int
    {
        return abs((int) $value);
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($v): string
    {
        return is_string($v) ? trim(strip_tags($v)) : '';
    }
}

if (!function_exists('wp_remote_post')) {
    function wp_remote_post($url, $args)
    {
        $GLOBALS['__amcp_wp_remote_post_calls']++;
        $GLOBALS['__amcp_wp_remote_post_last_url']  = $url;
        $GLOBALS['__amcp_wp_remote_post_last_args'] = $args;
        return ['response' => ['code' => 200], 'body' => '{}'];
    }
}

if (!function_exists('wp_remote_retrieve_response_code')) {
    function wp_remote_retrieve_response_code($response): int
    {
        return is_array($response) && isset($response['response']['code'])
            ? (int) $response['response']['code']
            : 0;
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing): bool
    {
        return $thing instanceof WP_Error;
    }
}

if (!function_exists('wc_get_order')) {
    function wc_get_order($id)
    {
        return null;
    }
}

if (!function_exists('wp_schedule_single_event')) {
    function wp_schedule_single_event($timestamp, $hook, $args = [])
    {
        $GLOBALS['__amcp_wp_schedule_single_event_calls'][] = [
            'timestamp' => $timestamp,
            'hook'      => $hook,
            'args'      => $args,
        ];
        return true;
    }
}

// WP_Error moved here from CatalogSyncErrorHandlingTest once a SECOND test file
// (ApiClientSsrfGuardTest) needed it: the owning-file convention only holds
// while exactly one file declares a class, and PHPUnit's process isolation
// means a child process may load a test file WITHOUT its former owner. That
// file's `class_exists` guard now simply skips. Keep the API in sync with the
// subset of WP_Error the plugin actually calls.
if (!class_exists('WP_Error')) {
    class WP_Error
    {
        /** @var string */
        private $code;
        /** @var string */
        private $message;

        public function __construct(string $code = '', string $message = '')
        {
            $this->code    = $code;
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
