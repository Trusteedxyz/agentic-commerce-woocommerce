<?php

declare(strict_types=1);

/**
 * Sincronización de la cuenta Stripe del plugin de WooCommerce con la
 * plataforma. La clave secreta se lee de `woocommerce_stripe_settings`, se
 * envía UNA vez al endpoint embed de conexión y no se devuelve a quien llama.
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/class-stripe-sync.php';

final class StripeSyncTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!function_exists('wp_parse_url')) {
            function wp_parse_url($url, $component = -1)
            {
                return parse_url($url, $component);
            }
        }
    }

    // Claves ficticias, construidas por partes para no dejar un literal con forma de clave real.
    private const TEST_KEY = 'sk_' . 'test_' . 'AAAAAAAAAAAAAAAAAAAAAAAAAAAA';
    private const LIVE_KEY = 'sk_' . 'live_' . 'BBBBBBBBBBBBBBBBBBBBBBBBBBBB';

    public function test_testmode_usa_la_clave_de_test(): void
    {
        $c = Trusteed_Stripe_Sync::read_credentials([
            'enabled' => 'yes', 'testmode' => 'yes',
            'test_secret_key' => self::TEST_KEY, 'secret_key' => self::LIVE_KEY,
        ]);
        $this->assertSame(['secret_key' => self::TEST_KEY, 'mode' => 'test'], $c);
    }

    public function test_modo_live_usa_la_clave_live(): void
    {
        $c = Trusteed_Stripe_Sync::read_credentials([
            'testmode' => 'no', 'test_secret_key' => self::TEST_KEY, 'secret_key' => self::LIVE_KEY,
        ]);
        $this->assertSame(['secret_key' => self::LIVE_KEY, 'mode' => 'live'], $c);
    }

    public function test_sin_clave_o_con_formato_ajeno_devuelve_null(): void
    {
        $this->assertNull(Trusteed_Stripe_Sync::read_credentials([]));
        $this->assertNull(Trusteed_Stripe_Sync::read_credentials(['testmode' => 'yes', 'test_secret_key' => '']));
        // La publicable no es una clave secreta.
        $this->assertNull(Trusteed_Stripe_Sync::read_credentials(['testmode' => 'yes', 'test_secret_key' => 'pk_' . 'test_' . 'AAAAAAAAAAAAAAAAAAAAAAAAAAAA']));
        // rk_ demasiado corta.
        $this->assertNull(Trusteed_Stripe_Sync::read_credentials(['testmode' => 'yes', 'test_secret_key' => 'rk_' . 'test_' . 'AAAA']));
        // La clave live no vale con testmode activo: no se adivina el modo.
        $this->assertNull(Trusteed_Stripe_Sync::read_credentials(['testmode' => 'yes', 'test_secret_key' => self::LIVE_KEY]));
    }

    public function test_clave_restringida_rk_se_acepta_en_ambos_modos(): void
    {
        $rk_test = 'rk_' . 'test_' . 'AAAAAAAAAAAAAAAAAAAAAAAAAAAA';
        $rk_live = 'rk_' . 'live_' . 'BBBBBBBBBBBBBBBBBBBBBBBBBBBB';
        $this->assertSame(
            ['secret_key' => $rk_test, 'mode' => 'test'],
            Trusteed_Stripe_Sync::read_credentials(['testmode' => 'yes', 'test_secret_key' => $rk_test])
        );
        $this->assertSame(
            ['secret_key' => $rk_live, 'mode' => 'live'],
            Trusteed_Stripe_Sync::read_credentials(['testmode' => 'no', 'secret_key' => $rk_live])
        );
        // El modo sigue saliendo del prefijo: rk_live con testmode activo no se adivina.
        $this->assertNull(Trusteed_Stripe_Sync::read_credentials(['testmode' => 'yes', 'test_secret_key' => $rk_live]));
    }

    public function test_describe_no_expone_la_clave(): void
    {
        $d = Trusteed_Stripe_Sync::describe(['testmode' => 'yes', 'test_secret_key' => self::TEST_KEY], true);
        $this->assertTrue($d['available']);
        $this->assertSame('test', $d['mode']);
        $this->assertSame('AAAA', $d['last4']);
        $this->assertTrue($d['enabled']);
        $this->assertStringNotContainsString(self::TEST_KEY, (string) json_encode($d));
    }

    public function test_describe_sin_clave_no_esta_disponible(): void
    {
        $d = Trusteed_Stripe_Sync::describe([], false);
        $this->assertFalse($d['available']);
        $this->assertFalse($d['enabled']);
    }

    public function test_sync_envia_la_clave_una_vez_con_el_token_embed(): void
    {
        $sent = [];
        $sync = new Trusteed_Stripe_Sync(
            static fn() => 'tok-123',
            'https://api.trusteed.xyz',
            static function (string $url, array $args) use (&$sent): array {
                $sent = ['url' => $url, 'args' => $args];
                return ['status' => 200, 'body' => '{"success":true}'];
            }
        );
        $r = $sync->sync(['testmode' => 'yes', 'test_secret_key' => self::TEST_KEY]);

        $this->assertTrue($r['ok']);
        $this->assertSame('test', $r['mode']);
        $this->assertSame('https://api.trusteed.xyz/v1/embed/merchant/payment-methods/stripe/connect', $sent['url']);
        $this->assertSame('Bearer tok-123', $sent['args']['headers']['Authorization']);
        $this->assertSame(['flow' => 'apiKey', 'apiKey' => self::TEST_KEY, 'force' => true], json_decode($sent['args']['body'], true));
        $this->assertStringNotContainsString(self::TEST_KEY, (string) json_encode($r));
    }

    public function test_sync_sin_clave_no_llama_a_la_api(): void
    {
        $calls = 0;
        $sync = new Trusteed_Stripe_Sync(
            static fn() => 'tok',
            'https://api.trusteed.xyz',
            static function () use (&$calls): array {
                $calls++;
                return ['status' => 200, 'body' => '{}'];
            }
        );
        $r = $sync->sync([]);
        $this->assertFalse($r['ok']);
        $this->assertSame('no_stripe_key', $r['code']);
        $this->assertSame(0, $calls);
    }

    public function test_sync_sin_token_no_envia_la_clave(): void
    {
        $calls = 0;
        $sync = new Trusteed_Stripe_Sync(
            static fn() => null,
            'https://api.trusteed.xyz',
            static function () use (&$calls): array {
                $calls++;
                return ['status' => 200, 'body' => '{}'];
            }
        );
        $r = $sync->sync(['testmode' => 'yes', 'test_secret_key' => self::TEST_KEY]);
        $this->assertSame('token_unavailable', $r['code']);
        $this->assertSame(0, $calls);
    }

    public function test_sync_rechazo_de_la_plataforma_devuelve_su_codigo_sin_la_clave(): void
    {
        $sync = new Trusteed_Stripe_Sync(
            static fn() => 'tok',
            'https://api.trusteed.xyz',
            static fn(): array => ['status' => 422, 'body' => '{"error":"demo_store_requires_test_key"}']
        );
        $r = $sync->sync(['testmode' => 'no', 'secret_key' => self::LIVE_KEY]);
        $this->assertFalse($r['ok']);
        $this->assertSame('platform_rejected', $r['code']);
        $this->assertSame('demo_store_requires_test_key', $r['reason']);
        $this->assertStringNotContainsString(self::LIVE_KEY, (string) json_encode($r));
    }

    public function test_sync_rechaza_una_base_api_ajena(): void
    {
        $calls = 0;
        $sync = new Trusteed_Stripe_Sync(
            static fn() => 'tok',
            'https://evil.example.com',
            static function () use (&$calls): array {
                $calls++;
                return ['status' => 200, 'body' => '{}'];
            }
        );
        $r = $sync->sync(['testmode' => 'yes', 'test_secret_key' => self::TEST_KEY]);
        $this->assertSame('api_host_not_allowed', $r['code']);
        $this->assertSame(0, $calls);
    }

    public function test_l1_un_3xx_es_rechazo_sin_cuerpo(): void
    {
        $sync = new Trusteed_Stripe_Sync(
            static fn() => 't',
            'https://api.trusteed.xyz',
            static fn(): array => ['status' => 302, 'body' => '{"error":"ignorado_por_ser_3xx"}']
        );
        $r = $sync->sync(['testmode' => 'yes', 'test_secret_key' => self::TEST_KEY]);
        $this->assertSame('platform_rejected', $r['code']);
        $this->assertSame('http_302', $r['reason']);
    }
}
