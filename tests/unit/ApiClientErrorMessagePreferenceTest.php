<?php

declare(strict_types=1);

/**
 * El comerciante debe ver la frase, no el código — auditoría de onboarding
 * 2026-09-09, hueco A5.
 *
 * `parse_response()` prefería `body.error` sobre `body.message`. `error` es el
 * código estable por el que ramifica una máquina (`wrong_deployment`,
 * `invalid_store_url`…) y `message` es la frase escrita para una persona. Con
 * el orden invertido, un rechazo por despliegue equivocado —que ahora sí
 * explica que la tienda NO quedó registrada y a qué instalación ir— se le
 * enseñaba al comerciante como «API error 409: wrong_deployment».
 *
 * Por qué aislamiento de proceso: exactamente el mismo motivo que documenta
 * `ApiClientSsrfGuardTest`. `CatalogSyncErrorHandlingTest` declara un DOBLE de
 * `Trusteed_Api_Client` tras un `class_exists`, y PHPUnit carga todos los
 * ficheros de test en un solo proceso: requerir aquí la clase real a nivel de
 * fichero hace que el doble no llegue a declararse y tumba esa otra suite
 * entera (comprobado: 8 errores). La clase real se carga dentro de `setUp()`
 * de un proceso aislado.
 */

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class ApiClientErrorMessagePreferenceTest extends TestCase
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

        require_once __DIR__ . '/../../includes/class-api-client.php';
    }

    /**
     * Invoca el `parse_response` privado con una respuesta HTTP simulada.
     *
     * Sin constructor a propósito: el método sólo usa sus argumentos, y
     * construir el cliente de verdad arrastraría la cadena entera de opciones
     * de WordPress para no ejercitar nada de ella.
     *
     * @param int   $status Código HTTP.
     * @param array $body   Cuerpo que se serializa como JSON.
     * @return array|WP_Error
     */
    private function parse(int $status, array $body)
    {
        $client = (new ReflectionClass(Trusteed_Api_Client::class))->newInstanceWithoutConstructor();

        $method = new ReflectionMethod(Trusteed_Api_Client::class, 'parse_response');
        $method->setAccessible(true);

        $response = [
            'response' => ['code' => $status],
            'body'     => json_encode($body),
            'headers'  => [],
        ];

        return $method->invoke($client, $response, '/api/v1/plugin/onboard');
    }

    public function test_prefiere_el_mensaje_humano_cuando_vienen_los_dos(): void
    {
        $result = $this->parse(409, [
            'success'  => false,
            'error'    => 'wrong_deployment',
            'message'  => 'This Trusteed installation does not handle WooCommerce store registrations. '
                . 'Your store has NOT been registered. Point the plugin\'s API base URL at '
                . 'https://api.otro.example and try again.',
            'peer_url' => 'https://api.otro.example',
        ]);

        $this->assertInstanceOf(WP_Error::class, $result);
        $this->assertStringContainsString('NOT been registered', $result->get_error_message());
        $this->assertStringContainsString('https://api.otro.example', $result->get_error_message());
    }

    public function test_no_le_ensena_el_codigo_de_maquina_al_comerciante(): void
    {
        $result = $this->parse(409, [
            'error'   => 'wrong_deployment',
            'message' => 'Your store has NOT been registered. Contact Trusteed support.',
        ]);

        // `wrong_deployment` no significa nada para quien regenta una tienda.
        $this->assertStringNotContainsString('wrong_deployment', $result->get_error_message());
    }

    public function test_cae_al_codigo_cuando_no_hay_mensaje(): void
    {
        // Respuestas antiguas del backend traen sólo `error`. Enseñar eso sigue
        // siendo mejor que enseñar una cadena vacía.
        $result = $this->parse(400, ['error' => 'invalid_store_url']);

        $this->assertInstanceOf(WP_Error::class, $result);
        $this->assertStringContainsString('invalid_store_url', $result->get_error_message());
    }

    public function test_un_mensaje_vacio_no_gana_al_codigo(): void
    {
        // `isset` bastaría para elegir `message` y dejar al comerciante con
        // «API error 500: » — peor que el código.
        $result = $this->parse(500, ['error' => 'internal_error', 'message' => '']);

        $this->assertStringContainsString('internal_error', $result->get_error_message());
    }

    public function test_conserva_el_codigo_en_los_datos_para_que_el_plugin_pueda_ramificar(): void
    {
        // Que el humano vea la frase no puede costarle a la máquina el código:
        // el cuerpo entero sigue disponible en los datos del WP_Error.
        $result = $this->parse(409, [
            'error'   => 'wrong_deployment',
            'message' => 'Your store has NOT been registered.',
        ]);

        $data = $result->get_error_data();
        $this->assertSame(409, $data['status']);
        $this->assertSame('wrong_deployment', $data['body']['error']);
    }

    public function test_una_respuesta_correcta_no_se_convierte_en_error(): void
    {
        $result = $this->parse(200, ['success' => true, 'merchant_id' => 'mrc_1']);

        $this->assertIsArray($result);
        $this->assertSame(200, $result['status']);
    }
}
