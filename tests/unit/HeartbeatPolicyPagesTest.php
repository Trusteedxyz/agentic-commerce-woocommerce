<?php

declare(strict_types=1);

/**
 * D6 (2026-10-15): el latido lleva la URL, el título y la fecha de modificación
 * de las páginas de términos y de privacidad de la tienda, y SÓLO si están
 * publicadas. Sin extracto del cuerpo.
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}
if (!defined('TRUSTEED_VERSION')) {
    define('TRUSTEED_VERSION', '9.9.9-test');
}
if (!function_exists('get_post_status')) {
    function get_post_status($id) { return $GLOBALS['__trusteed_pages'][(int) $id]['status'] ?? false; }
}
if (!function_exists('get_permalink')) {
    function get_permalink($id) { return $GLOBALS['__trusteed_pages'][(int) $id]['url'] ?? false; }
}
if (!function_exists('get_the_title')) {
    function get_the_title($id) { return $GLOBALS['__trusteed_pages'][(int) $id]['title'] ?? ''; }
}
if (!function_exists('get_post_field')) {
    function get_post_field($field, $id) { return $GLOBALS['__trusteed_pages'][(int) $id][$field] ?? ''; }
}

require_once __DIR__ . '/../../includes/class-trusteed-options.php';
require_once __DIR__ . '/../../includes/class-crypto-helper.php';
require_once __DIR__ . '/../../includes/class-capabilities-reporter.php';
require_once __DIR__ . '/../../includes/class-heartbeat.php';

use PHPUnit\Framework\TestCase;

final class HeartbeatPolicyPagesTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['__amcp_options_store'] = [];
        $GLOBALS['__trusteed_pages'] = [
            10 => ['status' => 'publish', 'url' => 'https://tienda.example/terminos/', 'title' => 'Términos y condiciones', 'post_modified_gmt' => '2026-09-01 10:00:00'],
            11 => ['status' => 'publish', 'url' => 'https://tienda.example/privacidad/', 'title' => 'Privacidad', 'post_modified_gmt' => '2026-09-02 11:30:00'],
            12 => ['status' => 'draft', 'url' => 'https://tienda.example/?p=12', 'title' => 'Borrador', 'post_modified_gmt' => '2026-09-03 00:00:00'],
        ];
    }

    public function test_publishes_terms_and_privacy_with_url_title_and_modified_date(): void
    {
        $GLOBALS['__amcp_options_store']['woocommerce_terms_page_id'] = '10';
        $GLOBALS['__amcp_options_store']['wp_page_for_privacy_policy'] = '11';
        $this->assertSame(
            [
                ['type' => 'terms', 'url' => 'https://tienda.example/terminos/', 'title' => 'Términos y condiciones', 'page_modified_at' => '2026-09-01T10:00:00Z'],
                ['type' => 'privacy', 'url' => 'https://tienda.example/privacidad/', 'title' => 'Privacidad', 'page_modified_at' => '2026-09-02T11:30:00Z'],
            ],
            Trusteed_Heartbeat::policy_pages()
        );
    }

    public function test_omits_unpublished_missing_and_unset_pages(): void
    {
        $GLOBALS['__amcp_options_store']['woocommerce_terms_page_id'] = '12'; // borrador
        $GLOBALS['__amcp_options_store']['wp_page_for_privacy_policy'] = '999'; // no existe
        $this->assertSame([], Trusteed_Heartbeat::policy_pages());
        $GLOBALS['__amcp_options_store'] = [];
        $this->assertSame([], Trusteed_Heartbeat::policy_pages());
    }

    public function test_never_sends_body_excerpt(): void
    {
        $GLOBALS['__amcp_options_store']['woocommerce_terms_page_id'] = '10';
        $GLOBALS['__trusteed_pages'][10]['post_content'] = 'cuerpo secreto';
        $keys = array_keys(Trusteed_Heartbeat::policy_pages()[0]);
        sort($keys);
        $this->assertSame(['page_modified_at', 'title', 'type', 'url'], $keys);
    }

    public function test_heartbeat_payload_includes_policy_pages(): void
    {
        $GLOBALS['__amcp_options_store']['woocommerce_terms_page_id'] = '10';
        $payload = Trusteed_Heartbeat::build_payload();
        $this->assertArrayHasKey('taxConfig', $payload);
        $this->assertCount(1, $payload['policyPages']);
    }
}
