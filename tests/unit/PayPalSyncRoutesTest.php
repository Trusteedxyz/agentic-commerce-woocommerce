<?php

declare(strict_types=1);

/**
 * Rutas y disparadores de la sincronización PayPal (PPCP): opt-in, re-sync solo
 * si cambian credenciales o modo (huella), y desconexión (D5).
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/AgentOrdersFakes.php'; // WP_REST_Response mínima (guardada con class_exists).
require_once __DIR__ . '/../../includes/class-paypal-sync.php';
require_once __DIR__ . '/../../includes/admin/class-paypal-sync-routes.php';

if (!class_exists('WP_REST_Request')) {
    class WP_REST_Request
    {
    }
}

if (!function_exists('wp_parse_url')) {
    function wp_parse_url($url, $component = -1)
    {
        return parse_url($url, $component);
    }
}

final class PayPalSyncRoutesTest extends TestCase
{
    private const CLIENT_ID     = 'AaBb' . 'CcDdEeFfGgHhIiJj' . 'Kk01';
    private const CLIENT_SECRET = 'EJ-' . 'secreto_rest_' . '0123456789abcdef';
    private const OTHER_SECRET  = 'EK-' . 'otro_secreto_' . 'fedcba9876543210';
    private const COMMON        = 'woocommerce-ppcp-data-common';
    private const LEGACY        = 'woocommerce-ppcp-settings';

    /** @var list<array{url:string,args:array}> */
    private array $posts = [];
    private int $status  = 200;
    private string $logFile;
    private string $prevLog;

    protected function setUp(): void
    {
        $GLOBALS['__amcp_options_store'] = [];
        $GLOBALS['__amcp_option_calls']  = [];
        Stub_Captured_Hooks_Registry::$hooks = [];
        $this->posts   = [];
        $this->status  = 200;
        $this->logFile = tempnam(sys_get_temp_dir(), 'ppsync');
        $this->prevLog = (string) ini_get('error_log');
        ini_set('error_log', $this->logFile);
    }

    protected function tearDown(): void
    {
        ini_set('error_log', $this->prevLog);
        @unlink($this->logFile);
    }

    private function routes(): Trusteed_PayPal_Sync_Routes
    {
        $broker = new class {
            public function request_token()
            {
                return new WP_REST_Response(['token' => 'tok-1'], 200);
            }

            public function check_permission($request)
            {
                return true;
            }
        };
        $r = new Trusteed_PayPal_Sync_Routes(
            $broker,
            'https://api.trusteed.xyz',
            function (string $url, array $args): array {
                $this->posts[] = ['url' => $url, 'args' => $args];
                return ['status' => $this->status, 'body' => 200 === $this->status ? '{"ok":true}' : '{"error":"method_coming_soon"}'];
            }
        );
        $r->init();
        return $r;
    }

    /** @return array<string,mixed> */
    private function common(array $over = []): array
    {
        return array_merge([
            'client_id' => self::CLIENT_ID, 'client_secret' => self::CLIENT_SECRET,
            'merchant_connected' => true, 'sandbox_merchant' => false,
        ], $over);
    }

    private function fire(string $hook, ...$args): void
    {
        foreach (Stub_Captured_Hooks_Registry::$hooks[$hook] ?? [] as $cb) {
            $cb(...$args);
        }
    }

    private function log(): string
    {
        return (string) file_get_contents($this->logFile);
    }

    public function test_init_registra_la_ruta_y_los_cuatro_disparadores(): void
    {
        $this->routes();
        foreach ([
            'rest_api_init',
            'update_option_' . self::COMMON,
            'update_option_' . self::LEGACY,
            'woocommerce_paypal_payments_authenticated_merchant',
            'woocommerce_paypal_payments_merchant_disconnected',
        ] as $hook) {
            $this->assertNotEmpty(Stub_Captured_Hooks_Registry::$hooks[$hook] ?? [], "falta el hook {$hook}");
        }
    }

    public function test_sin_opt_in_un_cambio_en_ppcp_no_envia_nada(): void
    {
        $this->routes();
        $this->fire('update_option_' . self::COMMON, [], $this->common());
        $this->fire('woocommerce_paypal_payments_authenticated_merchant');
        $this->assertCount(0, $this->posts);
    }

    public function test_con_opt_in_un_cambio_de_data_common_se_entrega_y_se_guarda_la_huella(): void
    {
        $this->routes();
        update_option(Trusteed_PayPal_Sync::OPTION_ENABLED, true);
        $new = $this->common();
        update_option(self::COMMON, $new);
        $this->fire('update_option_' . self::COMMON, [], $new);

        $this->assertCount(1, $this->posts);
        $this->assertStringEndsWith('/paypal/connect', $this->posts[0]['url']);
        $this->assertSame(
            Trusteed_PayPal_Sync::fingerprint(['client_id' => self::CLIENT_ID, 'client_secret' => self::CLIENT_SECRET, 'mode' => 'production']),
            get_option(Trusteed_PayPal_Sync::OPTION_HASH)
        );
    }

    public function test_la_huella_evita_reenviar_lo_mismo_y_un_cambio_real_si_reenvia(): void
    {
        $this->routes();
        update_option(Trusteed_PayPal_Sync::OPTION_ENABLED, true);
        $new = $this->common();
        update_option(self::COMMON, $new);
        $this->fire('update_option_' . self::COMMON, [], $new);
        $this->fire('update_option_' . self::COMMON, $new, $this->common(['otra_clave_de_ui' => 'x']));
        $this->fire('woocommerce_paypal_payments_authenticated_merchant');
        $this->assertCount(1, $this->posts, 'mismas credenciales y modo: un solo envío');

        $changed = $this->common(['client_secret' => self::OTHER_SECRET]);
        update_option(self::COMMON, $changed);
        $this->fire('update_option_' . self::COMMON, $new, $changed);
        $this->assertCount(2, $this->posts);

        $sandbox = $this->common(['client_secret' => self::OTHER_SECRET, 'sandbox_merchant' => true]);
        update_option(self::COMMON, $sandbox);
        $this->fire('update_option_' . self::COMMON, $changed, $sandbox);
        $this->assertCount(3, $this->posts, 'cambiar de modo también re-sincroniza');
    }

    public function test_un_cambio_en_la_opcion_heredada_se_entrega_cuando_data_common_esta_vacio(): void
    {
        $this->routes();
        update_option(Trusteed_PayPal_Sync::OPTION_ENABLED, true);
        $legacy = ['client_id' => self::CLIENT_ID, 'client_secret' => self::CLIENT_SECRET, 'sandbox_on' => '1'];
        update_option(self::LEGACY, $legacy);
        $this->fire('update_option_' . self::LEGACY, [], $legacy);

        $this->assertCount(1, $this->posts);
        $this->assertSame('sandbox', json_decode($this->posts[0]['args']['body'], true)['mode']);
    }

    public function test_el_hook_authenticated_merchant_lee_las_opciones_actuales(): void
    {
        $this->routes();
        update_option(Trusteed_PayPal_Sync::OPTION_ENABLED, true);
        update_option(self::COMMON, $this->common(['sandbox_merchant' => true]));
        $this->fire('woocommerce_paypal_payments_authenticated_merchant');
        $this->assertCount(1, $this->posts);
        $this->assertSame('sandbox', json_decode($this->posts[0]['args']['body'], true)['mode']);
    }

    public function test_credenciales_no_validas_o_desconectadas_no_envian_nada(): void
    {
        $this->routes();
        update_option(Trusteed_PayPal_Sync::OPTION_ENABLED, true);
        $off = $this->common(['merchant_connected' => false]);
        update_option(self::COMMON, $off);
        $this->fire('update_option_' . self::COMMON, $this->common(), $off);
        $this->fire('update_option_' . self::COMMON, [], 'no-es-un-array');
        $this->assertCount(0, $this->posts);
    }

    public function test_un_rechazo_no_guarda_la_huella_y_el_log_no_lleva_el_secreto(): void
    {
        $this->routes();
        $this->status = 422;
        update_option(Trusteed_PayPal_Sync::OPTION_ENABLED, true);
        $new = $this->common();
        update_option(self::COMMON, $new);
        $this->fire('update_option_' . self::COMMON, [], $new);

        $this->assertCount(1, $this->posts);
        $this->assertFalse(get_option(Trusteed_PayPal_Sync::OPTION_HASH, false), 'sin huella: el siguiente cambio reintenta');
        $log = $this->log();
        $this->assertStringContainsString('paypal_sync failed: platform_rejected (method_coming_soon)', $log);
        $this->assertStringNotContainsString(self::CLIENT_SECRET, $log);
        $this->assertStringNotContainsString(self::CLIENT_ID, $log);
    }

    public function test_handle_sync_activa_el_opt_in_y_guarda_la_huella_sin_devolver_el_secreto(): void
    {
        $r = $this->routes();
        update_option(self::COMMON, $this->common());
        $res = $r->handle_sync(new WP_REST_Request());

        $this->assertSame(200, $res->get_status());
        $this->assertTrue($res->get_data()['ok']);
        $this->assertTrue((bool) get_option(Trusteed_PayPal_Sync::OPTION_ENABLED, false));
        $this->assertNotFalse(get_option(Trusteed_PayPal_Sync::OPTION_HASH, false));
        $this->assertStringNotContainsString(self::CLIENT_SECRET, (string) json_encode($res->get_data()));
    }

    public function test_handle_sync_rechazado_devuelve_422_y_no_activa_el_opt_in(): void
    {
        $r = $this->routes();
        $this->status = 422;
        update_option(self::COMMON, $this->common());
        $res = $r->handle_sync(new WP_REST_Request());

        $this->assertSame(422, $res->get_status());
        $this->assertSame('method_coming_soon', $res->get_data()['reason']);
        $this->assertFalse(get_option(Trusteed_PayPal_Sync::OPTION_ENABLED, false));
        $this->assertFalse(get_option(Trusteed_PayPal_Sync::OPTION_HASH, false));
    }

    public function test_handle_status_describe_sin_secreto(): void
    {
        $r = $this->routes();
        update_option(self::COMMON, $this->common(['sandbox_merchant' => true]));
        $res = $r->handle_status(new WP_REST_Request());
        $this->assertSame(200, $res->get_status());
        $this->assertSame(['available' => true, 'enabled' => false, 'mode' => 'sandbox', 'last4' => 'Kk01'], $res->get_data());
    }

    public function test_d5_desconectar_en_ppcp_con_opt_in_desconecta_en_trusteed_y_limpia_la_huella(): void
    {
        $this->routes();
        update_option(Trusteed_PayPal_Sync::OPTION_ENABLED, true);
        update_option(Trusteed_PayPal_Sync::OPTION_HASH, 'abc');
        $this->fire('woocommerce_paypal_payments_merchant_disconnected');

        $this->assertCount(1, $this->posts);
        $this->assertStringEndsWith('/paypal/disconnect', $this->posts[0]['url']);
        $this->assertFalse(get_option(Trusteed_PayPal_Sync::OPTION_HASH, false));
        $this->assertTrue((bool) get_option(Trusteed_PayPal_Sync::OPTION_ENABLED, false), 'el opt-in se conserva: reconectar en PPCP re-sincroniza');
    }

    public function test_d5_sin_opt_in_no_toca_trusteed(): void
    {
        $this->routes();
        $this->fire('woocommerce_paypal_payments_merchant_disconnected');
        $this->assertCount(0, $this->posts);
    }

    public function test_d5_un_fallo_de_desconexion_se_registra_sin_romper_nada(): void
    {
        $this->routes();
        $this->status = 422;
        update_option(Trusteed_PayPal_Sync::OPTION_ENABLED, true);
        update_option(Trusteed_PayPal_Sync::OPTION_HASH, 'abc');
        $this->fire('woocommerce_paypal_payments_merchant_disconnected');
        $this->assertStringContainsString('paypal_sync disconnect failed: platform_rejected', $this->log());
        $this->assertSame('abc', get_option(Trusteed_PayPal_Sync::OPTION_HASH, false), 'si falla, la huella no se borra');
    }

    // ---- H1 flujo ---------------------------------------------------------

    public function test_h1_data_common_vaciado_por_la_desconexion_no_envia_connect_con_la_heredada(): void
    {
        $this->routes();
        update_option(Trusteed_PayPal_Sync::OPTION_ENABLED, true);
        update_option(self::LEGACY, ['client_id' => self::CLIENT_ID, 'client_secret' => self::CLIENT_SECRET, 'sandbox_on' => '']);
        $reset = ['client_id' => '', 'client_secret' => '', 'merchant_connected' => false];
        update_option(self::COMMON, $reset);
        $this->fire('update_option_' . self::COMMON, $this->common(), $reset);
        $this->fire('woocommerce_paypal_payments_authenticated_merchant');
        $this->assertCount(0, $this->posts, 'la heredada no se reconecta tras desconectar en PPCP 4.x');
    }

    // ---- M3: pausa tras fallo ---------------------------------------------

    public function test_m3_tras_un_fallo_los_disparadores_automaticos_no_reintentan_antes_de_300s(): void
    {
        $this->routes();
        $this->status = 422;
        update_option(Trusteed_PayPal_Sync::OPTION_ENABLED, true);
        $new = $this->common();
        update_option(self::COMMON, $new);
        $this->fire('update_option_' . self::COMMON, [], $new);
        $this->assertCount(1, $this->posts);
        $at = (int) get_option(Trusteed_PayPal_Sync::OPTION_RETRY_AT, 0);
        $this->assertGreaterThan(time() - 5, $at);

        $this->fire('update_option_' . self::COMMON, $new, $new);
        $this->fire('woocommerce_paypal_payments_authenticated_merchant');
        $this->fire('woocommerce_paypal_payments_merchant_disconnected');
        $this->assertCount(1, $this->posts, 'en pausa: ningún disparador automático envía');

        update_option(Trusteed_PayPal_Sync::OPTION_RETRY_AT, time() - 301);
        $this->fire('woocommerce_paypal_payments_authenticated_merchant');
        $this->assertCount(2, $this->posts, 'pasados 300 s se reintenta');
    }

    public function test_m3_un_exito_borra_la_pausa_y_los_hooks_usan_timeout_corto(): void
    {
        $this->routes();
        update_option(Trusteed_PayPal_Sync::OPTION_ENABLED, true);
        update_option(Trusteed_PayPal_Sync::OPTION_RETRY_AT, time() - 400);
        update_option(self::COMMON, $this->common());
        $this->fire('woocommerce_paypal_payments_authenticated_merchant');
        $this->assertCount(1, $this->posts);
        $this->assertLessThanOrEqual(5, $this->posts[0]['args']['timeout']);
        $this->assertFalse(get_option(Trusteed_PayPal_Sync::OPTION_RETRY_AT, false));
    }

    public function test_m3_el_envio_manual_no_espera_la_pausa(): void
    {
        $r = $this->routes();
        update_option(Trusteed_PayPal_Sync::OPTION_RETRY_AT, time());
        update_option(self::COMMON, $this->common());
        $res = $r->handle_sync(new WP_REST_Request());
        $this->assertSame(200, $res->get_status());
        $this->assertCount(1, $this->posts);
        $this->assertGreaterThan(5, $this->posts[0]['args']['timeout'], 'el manual conserva el timeout largo');
        $this->assertFalse(get_option(Trusteed_PayPal_Sync::OPTION_RETRY_AT, false));
    }

    // ---- L4 ---------------------------------------------------------------

    public function test_l4_desactivar_borra_opt_in_huella_y_pausa_sin_llamar_a_la_plataforma(): void
    {
        $r = $this->routes();
        update_option(Trusteed_PayPal_Sync::OPTION_ENABLED, true);
        update_option(Trusteed_PayPal_Sync::OPTION_HASH, 'abc');
        update_option(Trusteed_PayPal_Sync::OPTION_RETRY_AT, time());
        $res = $r->handle_disable(new WP_REST_Request());
        $this->assertSame(200, $res->get_status());
        $this->assertFalse(get_option(Trusteed_PayPal_Sync::OPTION_ENABLED, false));
        $this->assertFalse(get_option(Trusteed_PayPal_Sync::OPTION_HASH, false));
        $this->assertFalse(get_option(Trusteed_PayPal_Sync::OPTION_RETRY_AT, false));
        $this->assertSame(false, $res->get_data()['enabled']);
        $this->assertCount(0, $this->posts, 'desactivar no desconecta en Trusteed');

        update_option(self::COMMON, $this->common());
        $this->fire('update_option_' . self::COMMON, [], $this->common());
        $this->assertCount(0, $this->posts, 'desactivado: ya no sincroniza');
    }

    public function test_l4_el_422_nunca_devuelve_un_reason_no_seguro(): void
    {
        $r  = new Trusteed_PayPal_Sync_Routes(
            new class {
                public function request_token()
                {
                    return new WP_REST_Response(['token' => 't'], 200);
                }
                public function check_permission($r)
                {
                    return true;
                }
            },
            'https://api.trusteed.xyz',
            static fn(string $u, array $a): array => ['status' => 422, 'body' => '{"error":"<img src=x onerror=1>"}']
        );
        update_option(self::COMMON, $this->common());
        $res = $r->handle_sync(new WP_REST_Request());
        $this->assertSame(422, $res->get_status());
        $this->assertMatchesRegularExpression('/^[a-z0-9_]{1,64}$/', $res->get_data()['reason']);
    }
}
