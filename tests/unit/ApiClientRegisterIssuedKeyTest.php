<?php

declare(strict_types=1);

/**
 * /plugin/register con una clave del panel (`mcp_live_sk_`) devuelve una clave
 * nueva `amcp_wc_` en `data.api_key`; el plugin debe guardar ESA, no la pegada.
 * Una API antigua no la devuelve: se conserva la pegada.
 */

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class ApiClientRegisterIssuedKeyTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('TRUSTEED_API_BASE')) {
            define('TRUSTEED_API_BASE', 'https://api.trusteed.xyz');
        }
        if (!function_exists('esc_url_raw')) {
            function esc_url_raw($url) { return is_string($url) ? trim($url) : ''; }
        }
        if (!function_exists('untrailingslashit')) {
            function untrailingslashit($value) { return rtrim((string) $value, '/\\'); }
        }
        if (!function_exists('__')) {
            function __($text, $domain = null) { return $text; }
        }
        if (!function_exists('sanitize_text_field')) {
            function sanitize_text_field($v) { return trim((string) $v); }
        }
        require_once __DIR__ . '/../../includes/class-api-client.php';
    }

    private function persisted(array $data, string $pasted): string
    {
        $result = Trusteed_Api_Client::register_result_from_data($data);
        return Trusteed_Api_Client::key_to_persist($result, $pasted);
    }

    public function test_guarda_la_clave_emitida_en_lugar_de_la_pegada(): void
    {
        $this->assertSame(
            'amcp_wc_issued123',
            $this->persisted(['merchant_id' => 's1', 'api_key' => 'amcp_wc_issued123'], 'mcp_live_sk_pasted')
        );
    }

    public function test_api_antigua_sin_api_key_conserva_la_pegada(): void
    {
        $this->assertSame('mcp_live_sk_pasted', $this->persisted(['merchant_id' => 's1'], 'mcp_live_sk_pasted'));
    }

    public function test_ignora_una_api_key_sin_prefijo_de_plugin(): void
    {
        $this->assertSame('amcp_wc_mine', $this->persisted(['api_key' => 'otra_cosa'], 'amcp_wc_mine'));
    }
}
