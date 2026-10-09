<?php

declare(strict_types=1);

/**
 * `/plugin/onboard` devuelve las credenciales de enforcement y del webhook de
 * Woo, y el cliente tiene que entregarlas a `class-settings.php`, que es quien
 * las guarda. Hasta 2.4.0 la lista blanca del cliente las omitía: la tienda se
 * registraba sin secreto de instalación y todo lo firmado cerraba en silencio.
 *
 * Aislamiento de proceso por el mismo motivo que `ApiClientErrorMessagePreferenceTest`.
 */

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class ApiClientOnboardCredentialsTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('TRUSTEED_API_BASE')) {
            define('TRUSTEED_API_BASE', 'https://api.trusteed.xyz');
        }

        if (!function_exists('esc_url_raw')) {
            function esc_url_raw($url)
            {
                return is_string($url) ? trim($url) : '';
            }
        }
        if (!function_exists('untrailingslashit')) {
            function untrailingslashit($value)
            {
                return rtrim((string) $value, '/\\');
            }
        }
        if (!function_exists('__')) {
            function __($text, $domain = null)
            {
                return $text;
            }
        }
        if (!function_exists('wp_remote_retrieve_body')) {
            function wp_remote_retrieve_body($response)
            {
                return (string) ($response['body'] ?? '');
            }
        }
        if (!function_exists('wp_remote_retrieve_headers')) {
            function wp_remote_retrieve_headers($response)
            {
                return $response['headers'] ?? [];
            }
        }

        if (!function_exists('sanitize_text_field')) {
            function sanitize_text_field($v)
            {
                return trim((string) $v);
            }
        }

        require_once __DIR__ . '/../../includes/class-api-client.php';
    }

    public function test_entrega_las_credenciales_de_enforcement_y_del_webhook(): void
    {
        $r = Trusteed_Api_Client::onboard_result_from_data([
            'merchant_id'                 => 'st1',
            'api_key'                     => 'agnt_x',
            'enforcement_installation_id' => 'c034f4b5-6add-450b-97d6-6c5d64907dc2',
            'enforcement_hmac_secret'     => str_repeat('a', 64),
            'woo_webhook_secret'          => 'whsec',
            'already_connected'           => true,
        ]);

        $this->assertSame('c034f4b5-6add-450b-97d6-6c5d64907dc2', $r['enforcement_installation_id']);
        $this->assertSame(str_repeat('a', 64), $r['enforcement_hmac_secret']);
        $this->assertSame('whsec', $r['woo_webhook_secret']);
        $this->assertTrue($r['already_connected']);
    }

    public function test_campos_ausentes_o_no_textuales_salen_vacios(): void
    {
        $r = Trusteed_Api_Client::onboard_result_from_data([
            'enforcement_hmac_secret' => ['no', 'es', 'texto'],
        ]);

        $this->assertSame('', $r['enforcement_hmac_secret']);
        $this->assertSame('', $r['enforcement_installation_id']);
        $this->assertSame('FREE', $r['tier']);
        $this->assertFalse($r['already_connected']);
    }
}
