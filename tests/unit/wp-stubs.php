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
        // Reloj falso (Task 29b): `$GLOBALS['__amcp_clock']` sustituye a time() para
        // expirar lo escrito con TTL; lo sembrado a mano en el almacén no expira.
        $expiry = $GLOBALS['__amcp_transient_expiry'][$key] ?? null;
        if (null !== $expiry && ($GLOBALS['__amcp_clock'] ?? time()) >= $expiry) {
            unset($GLOBALS['__amcp_transients_store'][$key], $GLOBALS['__amcp_transient_expiry'][$key]);
        }
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
        // Respuesta forzada opcional (p. ej. un 400 del servidor).
        return $GLOBALS['__amcp_wp_remote_post_response'] ?? ['response' => ['code' => 200], 'body' => '{}'];
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
    /** Sin nada en `$GLOBALS['trusteed_test_orders']`, null (comportamiento histórico). */
    function wc_get_order($id)
    {
        return $GLOBALS['trusteed_test_orders'][$id] ?? null;
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
        /**
         * Tercer argumento del WP_Error real. Este stub lo DESCARTABA, así que
         * cualquier aserto sobre los datos de un error miraba a la nada — y el
         * plugin sí los usa: `parse_response()` mete ahí `status`, `body` y
         * `headers`, que es de donde debe leer el código de error quien
         * ramifique por él (2026-09-09).
         *
         * @var mixed
         */
        private $data;

        public function __construct(string $code = '', string $message = '', $data = '')
        {
            $this->code    = $code;
            $this->message = $message;
            $this->data    = $data;
        }

        public function get_error_code(): string
        {
            return $this->code;
        }

        public function get_error_message(): string
        {
            return $this->message;
        }

        /**
         * @return mixed
         */
        public function get_error_data()
        {
            return $this->data;
        }
    }
}

// ── Checkout flow (Task 5 del plan 04, 2026-09-30) ──────────────────────────
// `WC()` devuelve lo que el test deje en `$GLOBALS['trusteed_test_wc']`; sin
// nada, un WooCommerce sin sesión ni carrito (así `WC()->session` es null y el
// código de producción toma sus ramas «sin sesión»).
if (!function_exists('WC')) {
    function WC()
    {
        return $GLOBALS['trusteed_test_wc'] ?? (object) ['session' => null, 'cart' => null];
    }
}
if (!function_exists('wp_unslash')) {
    function wp_unslash($value)
    {
        return is_string($value) ? stripslashes($value) : $value;
    }
}
if (!function_exists('is_checkout')) {
    function is_checkout(): bool
    {
        return (bool) ($GLOBALS['trusteed_test_is_checkout'] ?? false);
    }
}
if (!function_exists('wc_get_checkout_url')) {
    function wc_get_checkout_url(): string
    {
        return 'https://shop.example.test/checkout/';
    }
}
// ── Atribución del agente (Task 5b del plan 04, 2026-09-30) ─────────────────
if (!function_exists('add_filter')) {
    /** Captura en el mismo registro que add_action (un filtro es un hook). */
    function add_filter(string $hook, callable $cb, int $prio = 10, int $args = 1): void
    {
        Stub_Captured_Hooks_Registry::$hooks[$hook][] = $cb;
    }
}
if (!function_exists('get_woocommerce_currency')) {
    function get_woocommerce_currency(): string
    {
        return 'EUR';
    }
}
if (!function_exists('set_transient')) {
    function set_transient($key, $value, $ttl = 0): bool
    {
        // Un almacén que rechaza escrituras (`__amcp_transients_reject_writes`)
        // devuelve false, como set_transient() de WordPress ante un fallo.
        if (!empty($GLOBALS['__amcp_transients_reject_writes'])) {
            return false;
        }
        $GLOBALS['__amcp_transients_store'][$key]   = $value;
        $GLOBALS['__amcp_transient_ttls'][$key]     = $ttl;
        if ($ttl > 0) {
            $GLOBALS['__amcp_transient_expiry'][$key] = ($GLOBALS['__amcp_clock'] ?? time()) + $ttl;
        } else {
            unset($GLOBALS['__amcp_transient_expiry'][$key]);
        }
        return true;
    }
}
if (!function_exists('__')) {
    function __($text, $domain = null)
    {
        return $text;
    }
}
if (!function_exists('esc_html__')) {
    function esc_html__($text, $domain = null)
    {
        return htmlspecialchars((string) $text, ENT_QUOTES);
    }
}
if (!function_exists('wc_add_notice')) {
    function wc_add_notice($message, $type = 'success'): void
    {
        $GLOBALS['__amcp_wc_notices'][] = [$type, $message];
    }
}
if (!defined('YEAR_IN_SECONDS')) {
    define('YEAR_IN_SECONDS', 31536000);
}
if (!function_exists('add_query_arg')) {
    /** Subconjunto de add_query_arg( array $args, string $url ) que usa el plugin. */
    function add_query_arg(array $args, string $url): string
    {
        return $url . (strpos($url, '?') === false ? '?' : '&') . http_build_query($args);
    }
}

// ── Snapshot client real (Task 23a del plan 04, 2026-09-30) ─────────────────
// `wp_remote_get` sirve lo que el test registre en
// `$GLOBALS['__amcp_remote_get_responses'][<url>]` y anota cada petición en
// `$GLOBALS['__amcp_remote_get_calls']`. Sin respuesta registrada, un error de
// red: ningún test puede salir a la red por accidente.
if (!function_exists('wp_remote_get')) {
    function wp_remote_get($url, $args = [])
    {
        $GLOBALS['__amcp_remote_get_calls'][] = ['url' => $url, 'args' => $args];
        return $GLOBALS['__amcp_remote_get_responses'][$url]
            ?? new WP_Error('http_request_failed', 'no stubbed response for ' . $url);
    }
}
if (!function_exists('wp_remote_retrieve_body')) {
    function wp_remote_retrieve_body($response)
    {
        return is_array($response) ? (string) ($response['body'] ?? '') : '';
    }
}
