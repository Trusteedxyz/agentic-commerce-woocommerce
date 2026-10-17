<?php

declare(strict_types=1);

/**
 * Sincronización de la cuenta PayPal de WooCommerce PayPal Payments (PPCP) con
 * la plataforma. El secreto se lee de las opciones de PPCP, se envía UNA vez al
 * endpoint embed de conexión y no se devuelve ni se registra.
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/class-paypal-sync.php';

final class PayPalSyncTest extends TestCase
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

    // Credenciales ficticias, construidas por partes para no dejar literales con forma de secreto.
    private const CLIENT_ID     = 'AaBb' . 'CcDdEeFfGgHhIiJj' . 'Kk01';
    private const CLIENT_SECRET = 'EJ-' . 'secreto_rest_' . '0123456789abcdef';
    private const LEGACY_ID     = 'ZzYy' . 'XxWwVvUuTtSsRrQq' . 'Pp99';
    private const LEGACY_SECRET = 'EK-' . 'heredado_rest_' . 'fedcba9876543210';

    /** @return array<string,mixed> */
    private function common(array $over = []): array
    {
        return array_merge([
            'client_id'          => self::CLIENT_ID,
            'client_secret'      => self::CLIENT_SECRET,
            'merchant_connected' => true,
            'sandbox_merchant'   => false,
            'use_sandbox'        => true, // estado de UI: no debe decidir el modo
        ], $over);
    }

    // ---- read_credentials -------------------------------------------------

    public function test_4x_conectado_en_produccion(): void
    {
        $c = Trusteed_PayPal_Sync::read_credentials($this->common(), []);
        $this->assertSame(
            ['client_id' => self::CLIENT_ID, 'client_secret' => self::CLIENT_SECRET, 'mode' => 'production'],
            $c
        );
    }

    public function test_4x_el_modo_sale_de_sandbox_merchant_y_no_de_use_sandbox(): void
    {
        $c = Trusteed_PayPal_Sync::read_credentials($this->common(['sandbox_merchant' => true, 'use_sandbox' => false]), []);
        $this->assertSame('sandbox', $c['mode']);
    }

    public function test_4x_desconectado_devuelve_null_aunque_queden_credenciales(): void
    {
        $this->assertNull(Trusteed_PayPal_Sync::read_credentials($this->common(['merchant_connected' => false]), []));
        // Con data-common poblado no se salta a la heredada: es la fuente vigente.
        $this->assertNull(Trusteed_PayPal_Sync::read_credentials(
            $this->common(['merchant_connected' => false]),
            ['client_id' => self::LEGACY_ID, 'client_secret' => self::LEGACY_SECRET, 'sandbox_on' => '']
        ));
    }

    public function test_3x_heredado_en_sandbox_y_en_live(): void
    {
        $sandbox = Trusteed_PayPal_Sync::read_credentials(null, [
            'client_id' => self::LEGACY_ID, 'client_secret' => self::LEGACY_SECRET, 'sandbox_on' => '1',
        ]);
        $this->assertSame(
            ['client_id' => self::LEGACY_ID, 'client_secret' => self::LEGACY_SECRET, 'mode' => 'sandbox'],
            $sandbox
        );
        $live = Trusteed_PayPal_Sync::read_credentials(null, [
            'client_id' => self::LEGACY_ID, 'client_secret' => self::LEGACY_SECRET, 'sandbox_on' => '',
        ]);
        $this->assertSame('production', $live['mode']);
        $liveBool = Trusteed_PayPal_Sync::read_credentials(null, [
            'client_id' => self::LEGACY_ID, 'client_secret' => self::LEGACY_SECRET, 'sandbox_on' => false,
        ]);
        $this->assertSame('production', $liveBool['mode']);
    }

    public function test_los_dos_vacios_devuelven_null(): void
    {
        $this->assertNull(Trusteed_PayPal_Sync::read_credentials(null, []));
        $this->assertNull(Trusteed_PayPal_Sync::read_credentials([], []));
    }

    public function test_secreto_ausente_o_con_formato_ajeno_devuelve_null(): void
    {
        $this->assertNull(Trusteed_PayPal_Sync::read_credentials($this->common(['client_secret' => '']), []));
        $this->assertNull(Trusteed_PayPal_Sync::read_credentials($this->common(['client_id' => '']), []));
        $this->assertNull(Trusteed_PayPal_Sync::read_credentials($this->common(['client_id' => 'corto']), []));
        $this->assertNull(Trusteed_PayPal_Sync::read_credentials($this->common(['client_secret' => str_repeat('a', 201)]), []));
        $this->assertNull(Trusteed_PayPal_Sync::read_credentials($this->common(['client_secret' => 'tiene espacios y ñ 0123456789']), []));
        $this->assertNull(Trusteed_PayPal_Sync::read_credentials($this->common(['client_id' => 12345]), []));
    }

    // ---- describe ---------------------------------------------------------

    public function test_describe_solo_expone_modo_y_ultimas_cuatro_cifras(): void
    {
        $d = Trusteed_PayPal_Sync::describe($this->common(['sandbox_merchant' => true]), [], true);
        $this->assertTrue($d['available']);
        $this->assertTrue($d['enabled']);
        $this->assertSame('sandbox', $d['mode']);
        $this->assertSame('Kk01', $d['last4']);
        $json = (string) json_encode($d);
        $this->assertStringNotContainsString(self::CLIENT_SECRET, $json);
        $this->assertStringNotContainsString(self::CLIENT_ID, $json);
    }

    public function test_describe_sin_credenciales_no_esta_disponible(): void
    {
        $d = Trusteed_PayPal_Sync::describe([], [], false);
        $this->assertFalse($d['available']);
        $this->assertFalse($d['enabled']);
        $this->assertNull($d['mode']);
        $this->assertNull($d['last4']);
    }

    // ---- sync -------------------------------------------------------------

    public function test_sync_envia_las_credenciales_una_vez_con_el_token_embed(): void
    {
        $sent = [];
        $sync = new Trusteed_PayPal_Sync(
            static fn() => 'tok-123',
            'https://api.trusteed.xyz',
            static function (string $url, array $args) use (&$sent): array {
                $sent = ['url' => $url, 'args' => $args];
                return ['status' => 200, 'body' => '{"ok":true}'];
            }
        );
        $r = $sync->sync($this->common(['sandbox_merchant' => true]), []);

        $this->assertTrue($r['ok']);
        $this->assertSame('connected', $r['code']);
        $this->assertSame('sandbox', $r['mode']);
        $this->assertSame('https://api.trusteed.xyz/v1/embed/merchant/payment-methods/paypal/connect', $sent['url']);
        $this->assertSame('Bearer tok-123', $sent['args']['headers']['Authorization']);
        $this->assertSame('wp-embed', $sent['args']['headers']['X-Embed-Source']);
        $this->assertSame(
            [
                'flow'      => 'apiKey',
                'accountId' => self::CLIENT_ID,
                'apiKey'    => self::CLIENT_SECRET,
                'mode'      => 'sandbox',
                'force'     => true,
            ],
            json_decode($sent['args']['body'], true)
        );
        $this->assertStringNotContainsString(self::CLIENT_SECRET, (string) json_encode($r));
    }

    public function test_sync_sin_credenciales_no_llama_a_la_api(): void
    {
        $calls = 0;
        $sync  = new Trusteed_PayPal_Sync(
            static fn() => 'tok',
            'https://api.trusteed.xyz',
            static function () use (&$calls): array {
                $calls++;
                return ['status' => 200, 'body' => '{}'];
            }
        );
        $r = $sync->sync([], []);
        $this->assertFalse($r['ok']);
        $this->assertSame('no_paypal_credentials', $r['code']);
        $this->assertSame(0, $calls);
    }

    public function test_sync_sin_token_no_envia_el_secreto(): void
    {
        $calls = 0;
        $sync  = new Trusteed_PayPal_Sync(
            static fn() => null,
            'https://api.trusteed.xyz',
            static function () use (&$calls): array {
                $calls++;
                return ['status' => 200, 'body' => '{}'];
            }
        );
        $r = $sync->sync($this->common(), []);
        $this->assertSame('token_unavailable', $r['code']);
        $this->assertSame(0, $calls);
    }

    public function test_sync_rechazo_de_la_plataforma_devuelve_su_codigo_sin_el_secreto(): void
    {
        foreach ([
            [422, '{"error":"method_coming_soon","message":"x ' . 'EJ-secreto_rest_0123456789abcdef"}', 'method_coming_soon'],
            [422, '{"error":"real_store_requires_live"}', 'real_store_requires_live'],
            [401, '{"error":"invalid_credentials"}', 'invalid_credentials'],
            [502, 'no es json', 'http_502'],
        ] as [$status, $body, $reason]) {
            $sync = new Trusteed_PayPal_Sync(
                static fn() => 'tok',
                'https://api.trusteed.xyz',
                static fn(): array => ['status' => $status, 'body' => $body]
            );
            $r = $sync->sync($this->common(), []);
            $this->assertFalse($r['ok']);
            $this->assertSame('platform_rejected', $r['code']);
            $this->assertSame($reason, $r['reason']);
            $this->assertStringNotContainsString(self::CLIENT_SECRET, (string) json_encode($r));
            $this->assertStringNotContainsString('secreto_rest', (string) json_encode($r));
        }
    }

    public function test_sync_rechaza_una_base_api_ajena(): void
    {
        $calls = 0;
        $sync  = new Trusteed_PayPal_Sync(
            static fn() => 'tok',
            'https://evil.example.com',
            static function () use (&$calls): array {
                $calls++;
                return ['status' => 200, 'body' => '{}'];
            }
        );
        $r = $sync->sync($this->common(), []);
        $this->assertSame('api_host_not_allowed', $r['code']);
        $this->assertSame(0, $calls);
    }

    // ---- fingerprint ------------------------------------------------------

    public function test_fingerprint_es_estable_cambia_con_cualquier_dato_y_no_contiene_el_secreto(): void
    {
        $a = Trusteed_PayPal_Sync::fingerprint(Trusteed_PayPal_Sync::read_credentials($this->common(), []));
        $b = Trusteed_PayPal_Sync::fingerprint(Trusteed_PayPal_Sync::read_credentials($this->common(), []));
        $this->assertSame($a, $b);
        $this->assertSame(64, strlen($a));
        $this->assertNotSame($a, Trusteed_PayPal_Sync::fingerprint(Trusteed_PayPal_Sync::read_credentials($this->common(['sandbox_merchant' => true]), [])));
        $this->assertNotSame($a, Trusteed_PayPal_Sync::fingerprint(Trusteed_PayPal_Sync::read_credentials($this->common(['client_secret' => self::LEGACY_SECRET]), [])));
        $this->assertStringNotContainsString(self::CLIENT_SECRET, $a);
    }

    // ---- H1: la heredada sólo si data-common no existe --------------------

    private function legacy(): array
    {
        return ['client_id' => self::LEGACY_ID, 'client_secret' => self::LEGACY_SECRET, 'sandbox_on' => ''];
    }

    public function test_h1_data_common_reseteado_por_la_desconexion_no_cae_a_la_heredada(): void
    {
        // PPCP 4.x al desconectar vacía data-common (reset_merchant_data) y NO borra la heredada.
        $this->assertNull(Trusteed_PayPal_Sync::read_credentials([], $this->legacy()));
        $this->assertNull(Trusteed_PayPal_Sync::read_credentials(
            ['client_id' => '', 'client_secret' => '', 'merchant_connected' => false],
            $this->legacy()
        ));
        $this->assertFalse(Trusteed_PayPal_Sync::describe([], $this->legacy(), true)['available']);
    }

    public function test_h1_data_common_inexistente_ppcp_menor_que_4_usa_la_heredada(): void
    {
        $c = Trusteed_PayPal_Sync::read_credentials(null, $this->legacy());
        $this->assertSame(self::LEGACY_ID, $c['client_id']);
        $this->assertTrue(Trusteed_PayPal_Sync::describe(null, $this->legacy(), false)['available']);
    }

    public function test_h1_sync_con_data_common_reseteado_no_envia_la_heredada(): void
    {
        $calls = 0;
        $sync  = new Trusteed_PayPal_Sync(static fn() => 't', 'https://api.trusteed.xyz', static function () use (&$calls): array {
            $calls++;
            return ['status' => 200, 'body' => '{}'];
        });
        $r = $sync->sync([], $this->legacy());
        $this->assertSame('no_paypal_credentials', $r['code']);
        $this->assertSame(0, $calls);
    }

    // ---- L1 / M3: redirecciones, 3xx y timeout ----------------------------

    public function test_l1_un_3xx_es_rechazo_sin_cuerpo(): void
    {
        $sync = new Trusteed_PayPal_Sync(static fn() => 't', 'https://api.trusteed.xyz', static fn(): array => ['status' => 302, 'body' => '{"error":"ignorado_por_ser_3xx"}']);
        $r = $sync->sync($this->common(), []);
        $this->assertSame('platform_rejected', $r['code']);
        $this->assertSame('http_302', $r['reason']);
        $d = $sync->disconnect();
        $this->assertSame('http_302', $d['reason']);
    }

    public function test_l4_reason_no_seguro_se_reduce_a_http_status(): void
    {
        foreach (['<script>x</script>', 'Tiene Espacios', str_repeat('a', 65), '', 'MAYUS'] as $bad) {
            $sync = new Trusteed_PayPal_Sync(static fn() => 't', 'https://api.trusteed.xyz', static fn(): array => ['status' => 422, 'body' => json_encode(['error' => $bad])]);
            $this->assertSame('http_422', $sync->sync($this->common(), [])['reason'], "reason={$bad}");
        }
    }

    public function test_m3_el_timeout_es_configurable_y_por_defecto_largo(): void
    {
        $seen = [];
        $mk   = static function (?int $t) use (&$seen): Trusteed_PayPal_Sync {
            $post = static function (string $u, array $a) use (&$seen): array {
                $seen[] = $a['timeout'];
                return ['status' => 200, 'body' => '{}'];
            };
            return null === $t
                ? new Trusteed_PayPal_Sync(static fn() => 't', 'https://api.trusteed.xyz', $post)
                : new Trusteed_PayPal_Sync(static fn() => 't', 'https://api.trusteed.xyz', $post, $t);
        };
        $mk(null)->disconnect();
        $mk(5)->disconnect();
        $this->assertSame([30, 5], $seen);
    }

    // ---- disconnect (D5) --------------------------------------------------

    public function test_disconnect_llama_a_la_ruta_embed_de_desconexion(): void
    {
        $sent = [];
        $sync = new Trusteed_PayPal_Sync(
            static fn() => 'tok-9',
            'https://api.trusteed.xyz',
            static function (string $url, array $args) use (&$sent): array {
                $sent = ['url' => $url, 'args' => $args];
                return ['status' => 200, 'body' => '{"ok":true}'];
            }
        );
        $r = $sync->disconnect();
        $this->assertTrue($r['ok']);
        $this->assertSame('disconnected', $r['code']);
        $this->assertSame('https://api.trusteed.xyz/v1/embed/merchant/payment-methods/paypal/disconnect', $sent['url']);
        $this->assertSame('Bearer tok-9', $sent['args']['headers']['Authorization']);
        $this->assertSame('wp-embed', $sent['args']['headers']['X-Embed-Source']);
    }

    public function test_disconnect_respeta_allowlist_y_token(): void
    {
        $calls = 0;
        $post  = static function () use (&$calls): array {
            $calls++;
            return ['status' => 200, 'body' => '{}'];
        };
        $this->assertSame('api_host_not_allowed', (new Trusteed_PayPal_Sync(static fn() => 't', 'https://evil.example.com', $post))->disconnect()['code']);
        $this->assertSame('token_unavailable', (new Trusteed_PayPal_Sync(static fn() => null, 'https://api.trusteed.xyz', $post))->disconnect()['code']);
        $this->assertSame(0, $calls);
        $r = (new Trusteed_PayPal_Sync(static fn() => 't', 'https://api.trusteed.xyz', static fn(): array => ['status' => 404, 'body' => '{"error":"not_found"}']))->disconnect();
        $this->assertFalse($r['ok']);
        $this->assertSame('platform_rejected', $r['code']);
        $this->assertSame('not_found', $r['reason']);
    }
}
