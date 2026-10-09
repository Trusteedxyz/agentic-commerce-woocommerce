<?php

declare(strict_types=1);

/**
 * Reto de control de dominio al reconectar (`/.well-known/amcp-verify.txt`).
 *
 * Sin esto el plugin trataba el 202 de `/plugin/onboard` como un éxito con
 * campos vacíos: una tienda ya registrada no podía recuperar sus credenciales.
 */

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class DomainChallengeTest extends TestCase
{
    private const TOKEN = 'a3f9c0de11223344556677889900aabbccddeeff00112233445566778899aabb';

    protected function setUp(): void
    {
        if (!function_exists('wp_parse_url')) {
            function wp_parse_url($url, $component = -1)
            {
                return parse_url($url, $component);
            }
        }
        require_once __DIR__ . '/../../includes/class-domain-challenge.php';
    }

    public function test_extrae_el_token_del_202(): void
    {
        $body = [
            'success'   => false,
            'reason'    => 'domain_verification_required',
            'challenge' => ['token' => self::TOKEN, 'expected_path' => '/.well-known/amcp-verify.txt'],
        ];
        $this->assertSame(self::TOKEN, Trusteed_Domain_Challenge::token_from_body($body));
    }

    public function test_rechaza_cuerpos_que_no_son_un_reto(): void
    {
        $this->assertNull(Trusteed_Domain_Challenge::token_from_body(['reason' => 'otra_cosa', 'challenge' => ['token' => self::TOKEN]]));
        $this->assertNull(Trusteed_Domain_Challenge::token_from_body(['reason' => 'domain_verification_required']));
        $this->assertNull(Trusteed_Domain_Challenge::token_from_body(['reason' => 'domain_verification_required', 'challenge' => ['token' => '<script>']]));
        $this->assertNull(Trusteed_Domain_Challenge::token_from_body('texto'));
    }

    public function test_sirve_el_token_solo_en_su_ruta(): void
    {
        $this->assertSame(self::TOKEN, Trusteed_Domain_Challenge::response_for('/.well-known/amcp-verify.txt', self::TOKEN));
        $this->assertSame(self::TOKEN, Trusteed_Domain_Challenge::response_for('/.well-known/amcp-verify.txt?x=1', self::TOKEN));
        $this->assertNull(Trusteed_Domain_Challenge::response_for('/.well-known/otra.txt', self::TOKEN));
        $this->assertNull(Trusteed_Domain_Challenge::response_for('/', self::TOKEN));
    }

    public function test_sin_reto_pendiente_no_responde_nada(): void
    {
        $this->assertNull(Trusteed_Domain_Challenge::response_for('/.well-known/amcp-verify.txt', false));
        $this->assertNull(Trusteed_Domain_Challenge::response_for('/.well-known/amcp-verify.txt', 'no-hex'));
    }
}
