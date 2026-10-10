<?php

declare(strict_types=1);

/**
 * C7/C8 (capabilities reporter), C14 (heartbeat) y C16 (order.completed).
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}
if (!defined('TRUSTEED_VERSION')) {
    define('TRUSTEED_VERSION', '9.9.9-test');
}
if (!function_exists('get_site_url')) {
    function get_site_url(): string { return 'https://shop.example.com'; }
}
if (!function_exists('wp_next_scheduled')) {
    function wp_next_scheduled($hook) { return $GLOBALS['__sched'][$hook] ?? false; }
}
if (!function_exists('wp_schedule_event')) {
    function wp_schedule_event($ts, $rec, $hook) { $GLOBALS['__sched'][$hook] = $ts; $GLOBALS['__sched_rec'][$hook] = $rec; return true; }
}
if (!function_exists('wp_clear_scheduled_hook')) {
    function wp_clear_scheduled_hook($hook) { unset($GLOBALS['__sched'][$hook]); return 1; }
}

require_once __DIR__ . '/../../includes/class-trusteed-options.php';
require_once __DIR__ . '/../../includes/class-crypto-helper.php';
require_once __DIR__ . '/../../includes/class-capabilities-reporter.php';
require_once __DIR__ . '/../../includes/class-heartbeat.php';
require_once __DIR__ . '/../../includes/class-agent-event-webhook.php';

use PHPUnit\Framework\TestCase;

final class PluginCapabilitiesHeartbeatOrderCompletedTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['__amcp_options_store'] = [];
        $GLOBALS['__amcp_wp_remote_post_calls'] = 0;
        $GLOBALS['__amcp_wp_remote_post_last_args'] = null;
        $GLOBALS['__sched'] = [];
        $GLOBALS['__amcp_transients_store'] = [];
        $GLOBALS['__amcp_transient_expiry'] = [];
        unset($GLOBALS['__amcp_wp_remote_post_response']);
    }

    private function configure(): void
    {
        $GLOBALS['__amcp_options_store']['trusteed_merchant_id'] = '11111111-1111-4111-8111-111111111111';
        $GLOBALS['__amcp_options_store']['trusteed_enforcement_installation_id'] = '22222222-2222-4222-8222-222222222222';
        $GLOBALS['__amcp_options_store']['trusteed_enforcement_hmac_secret'] = 'plain-secret';
        $GLOBALS['__amcp_options_store']['trusteed_api_base_url'] = 'https://api.trusteed.xyz';
    }

    public function test_signals_provided_matches_what_the_enforcer_writes(): void
    {
        $src = (string) file_get_contents(__DIR__ . '/../../includes/class-checkout-enforcer.php');
        preg_match_all("/\['cartAttributes'\]\[\s*'(_[a-z0-9_]+)'\s*\]\s*=[^=]/", $src, $m);
        $written = array_values(array_unique($m[1]));
        sort($written);
        $declared = Trusteed_Capabilities_Reporter::signals_provided();
        sort($declared);
        $this->assertSame($written, $declared);
    }

    public function test_report_signs_body_with_installation_secret_and_posts(): void
    {
        $this->configure();
        $this->assertTrue(Trusteed_Capabilities_Reporter::report_now());
        $this->assertSame(1, $GLOBALS['__amcp_wp_remote_post_calls']);
        $this->assertSame(
            'https://api.trusteed.xyz/api/v1/enforcement/capabilities',
            $GLOBALS['__amcp_wp_remote_post_last_url']
        );
        $body = json_decode($GLOBALS['__amcp_wp_remote_post_last_args']['body'], true);
        $sig = $body['signature'];
        unset($body['signature']);
        $this->assertSame('WOOCOMMERCE', $body['platform']);
        $this->assertContains('cartAttr._lowest_stock', $body['signalsProvided']);
        $this->assertSame(Trusteed_Capabilities_Reporter::sign($body, 'plain-secret'), $sig);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $sig);
    }

    /**
     * El servidor rechaza con 400 el reporte ENTERO si trae una señal que
     * ninguna regla lee (`SignalsProvidedSchema`). Las cuatro huérfanas
     * documentadas se escriben en el carrito pero no se reportan: con ellas,
     * la instalación real de pruebas recibía «HTTP 400» y C7/C8 seguían sin
     * veredicto.
     */
    public function test_report_excludes_signals_no_rule_reads(): void
    {
        $this->configure();
        Trusteed_Capabilities_Reporter::report_now();
        $body = json_decode($GLOBALS['__amcp_wp_remote_post_last_args']['body'], true);
        foreach (
            array(
                'cartAttr._agent_token_replay',
                'cartAttr._agent_token_jti_missing',
                'cartAttr._agent_token_jti_malformed',
                'cartAttr._agent_token_nonce_unavailable',
            ) as $orphan
        ) {
            $this->assertNotContains($orphan, $body['signalsProvided']);
        }
        $this->assertContains('cartAttr._agent_token_signature_invalid', $body['signalsProvided']);
        $this->assertCount(12, $body['signalsProvided']);
    }

    /**
     * Un rechazo del servidor no puede reintentarse en cada `init`: con el
     * panel abierto eso es una petición a la API por cada admin-ajax (visto en
     * la tienda de pruebas: un 400 por minuto). Tras un fallo se espera 1 h.
     */
    public function test_rejected_report_backs_off_instead_of_retrying_every_init(): void
    {
        $this->configure();
        $GLOBALS['__amcp_wp_remote_post_response'] = ['response' => ['code' => 400], 'body' => '{}'];
        Trusteed_Capabilities_Reporter::maybe_report();
        Trusteed_Capabilities_Reporter::maybe_report();
        Trusteed_Capabilities_Reporter::maybe_report();
        $this->assertSame(1, $GLOBALS['__amcp_wp_remote_post_calls']);
    }

    public function test_accepted_report_clears_the_backoff(): void
    {
        $this->configure();
        $GLOBALS['__amcp_wp_remote_post_response'] = ['response' => ['code' => 400], 'body' => '{}'];
        Trusteed_Capabilities_Reporter::maybe_report();
        unset($GLOBALS['__amcp_wp_remote_post_response']);
        // El reenvío diario (o el diferido tras guardar ajustes) no espera.
        $this->assertTrue(Trusteed_Capabilities_Reporter::report_now());
        Trusteed_Capabilities_Reporter::maybe_report();
        $this->assertSame(2, $GLOBALS['__amcp_wp_remote_post_calls']);
    }

    public function test_report_skips_without_credentials_and_does_not_throw(): void
    {
        $this->assertFalse(Trusteed_Capabilities_Reporter::report_now());
        $this->assertSame(0, $GLOBALS['__amcp_wp_remote_post_calls']);
    }

    public function test_maybe_report_only_resends_when_fingerprint_changes(): void
    {
        $this->configure();
        Trusteed_Capabilities_Reporter::maybe_report();
        Trusteed_Capabilities_Reporter::maybe_report();
        $this->assertSame(1, $GLOBALS['__amcp_wp_remote_post_calls']);
        $GLOBALS['__amcp_options_store'][Trusteed_Capabilities_Reporter::OPTION_REPORTED_FINGERPRINT] = 'stale';
        Trusteed_Capabilities_Reporter::maybe_report();
        $this->assertSame(2, $GLOBALS['__amcp_wp_remote_post_calls']);
    }

    public function test_heartbeat_schedules_hourly_once_and_unschedules(): void
    {
        Trusteed_Heartbeat::ensure_scheduled();
        $this->assertSame('hourly', $GLOBALS['__sched_rec'][Trusteed_Heartbeat::HOOK]);
        $first = $GLOBALS['__sched'][Trusteed_Heartbeat::HOOK];
        Trusteed_Heartbeat::ensure_scheduled();
        $this->assertSame($first, $GLOBALS['__sched'][Trusteed_Heartbeat::HOOK]);
        Trusteed_Heartbeat::unschedule();
        $this->assertArrayNotHasKey(Trusteed_Heartbeat::HOOK, $GLOBALS['__sched']);
    }

    public function test_heartbeat_tax_config_reflects_woocommerce_tax_options(): void
    {
        $GLOBALS['__amcp_options_store']['woocommerce_calc_taxes'] = 'yes';
        $GLOBALS['__amcp_options_store']['woocommerce_prices_include_tax'] = 'no';
        $GLOBALS['__amcp_options_store']['woocommerce_tax_display_shop'] = 'incl';
        $this->assertSame(
            ['calcTaxes' => true, 'pricesIncludeTax' => false, 'taxDisplayShop' => 'incl'],
            Trusteed_Heartbeat::tax_config()
        );
    }

    public function test_heartbeat_tax_config_defaults_when_options_missing(): void
    {
        $this->assertSame(
            ['calcTaxes' => false, 'pricesIncludeTax' => false, 'taxDisplayShop' => 'excl'],
            Trusteed_Heartbeat::tax_config()
        );
        $GLOBALS['__amcp_options_store']['woocommerce_tax_display_shop'] = 'garbage';
        $this->assertSame('excl', Trusteed_Heartbeat::tax_config()['taxDisplayShop']);
    }

    public function test_order_completed_posts_wc_shaped_signed_webhook_without_pii(): void
    {
        $GLOBALS['__amcp_options_store']['trusteed_woo_webhook_secret'] = 'woo-secret';
        $order = new class {
            public function get_id() { return 42; }
            public function get_order_number() { return '42'; }
            public function get_currency() { return 'eur'; }
            public function get_total() { return '19.90'; }
            public function get_billing_email() { return 'x@y.z'; }
            public function get_date_created() { return new class { public function getTimestamp() { return 1700000000; } public function date($f) { return gmdate($f, 1700000000); } }; }
            public function get_date_completed() { return new class { public function getTimestamp() { return 1700003600; } public function date($f) { return gmdate($f, 1700003600); } }; }
            public function get_meta($k, $s = true) { return $k === '_trusteed_agent_did' ? 'did:web:agent' : ''; }
        };
        $hook = new Trusteed_Agent_Event_Webhook('https://api.trusteed.xyz', 'i', 's', 'm');
        $hook->on_order_completed(42, $order);

        $this->assertSame(1, $GLOBALS['__amcp_wp_remote_post_calls']);
        $this->assertSame('https://api.trusteed.xyz/api/v1/ucp/webhooks/woocommerce/orders', $GLOBALS['__amcp_wp_remote_post_last_url']);
        $args = $GLOBALS['__amcp_wp_remote_post_last_args'];
        $h = $args['headers'];
        $this->assertSame('order.completed', $h['x-wc-webhook-topic']);
        $this->assertSame('https://shop.example.com', $h['x-wc-webhook-source']);
        $this->assertSame('trusteed-plugin-42-1700003600', $h['x-wc-webhook-delivery-id']);
        $this->assertSame(base64_encode(hash_hmac('sha256', $args['body'], 'woo-secret', true)), $h['x-wc-webhook-signature']);
        $body = json_decode($args['body'], true);
        $this->assertSame(42, $body['id']);
        $this->assertSame('EUR', $body['currency']);
        $this->assertSame('2023-11-14T22:13:20', $body['date_created_gmt']);
        $this->assertSame('2023-11-14T23:13:20', $body['date_completed_gmt']);
        $this->assertSame([['key' => '_trusteed_agent_did', 'value' => 'did:web:agent']], $body['meta_data']);
        $this->assertStringNotContainsString('x@y.z', $args['body']);
        $this->assertArrayNotHasKey('_trusteed_delivery_id', $body);
    }

    public function test_order_completed_is_silent_without_webhook_secret(): void
    {
        $hook = new Trusteed_Agent_Event_Webhook('https://api.trusteed.xyz', 'i', 's', 'm');
        $hook->on_order_completed(1, null);
        $this->assertSame(0, $GLOBALS['__amcp_wp_remote_post_calls']);
    }
}
