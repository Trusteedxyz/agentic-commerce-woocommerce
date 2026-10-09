<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/class-agent-request-auth.php';

/**
 * Firma de las llamadas de Trusteed al plugin (plan 2026-10-05, tarea 4).
 *
 * Formato idéntico a `signWebhookPayload` de apps/api/src/services/webhook.service.ts:
 * cabecera `t=<unix>,v1=<hex>` con v1 = HMAC-SHA256(secreto, "<t>.<cuerpo crudo>").
 */
final class AgentRequestAuthTest extends TestCase
{
    private const SECRET = 'secreto-de-instalacion-de-prueba';
    private const NOW    = 1790000000;

    private static function header(string $body, int $t, string $secret = self::SECRET): string
    {
        return 't=' . $t . ',v1=' . hash_hmac('sha256', $t . '.' . $body, $secret);
    }

    protected function setUp(): void
    {
        $GLOBALS['__amcp_transients_store'] = [];
        $GLOBALS['__amcp_transient_expiry'] = [];
    }

    public function test_firma_valida_se_acepta(): void
    {
        $body = '{"a":1}';
        $this->assertTrue(Trusteed_Agent_Request_Auth::verify(self::header($body, self::NOW), $body, self::SECRET, self::NOW));
    }

    public function test_cuerpo_alterado_se_rechaza(): void
    {
        $h = self::header('{"a":1}', self::NOW);
        $this->assertFalse(Trusteed_Agent_Request_Auth::verify($h, '{"a":2}', self::SECRET, self::NOW));
    }

    public function test_secreto_distinto_se_rechaza(): void
    {
        $body = '{"a":1}';
        $this->assertFalse(Trusteed_Agent_Request_Auth::verify(self::header($body, self::NOW, 'otro'), $body, self::SECRET, self::NOW));
    }

    public function test_firma_caducada_se_rechaza(): void
    {
        $body = '{"a":1}';
        $this->assertFalse(Trusteed_Agent_Request_Auth::verify(self::header($body, self::NOW - 301), $body, self::SECRET, self::NOW));
    }

    public function test_firma_del_futuro_se_rechaza(): void
    {
        $body = '{"a":1}';
        $this->assertFalse(Trusteed_Agent_Request_Auth::verify(self::header($body, self::NOW + 301), $body, self::SECRET, self::NOW));
    }

    public function test_el_borde_de_la_ventana_se_acepta(): void
    {
        $body = '{"a":1}';
        $this->assertTrue(Trusteed_Agent_Request_Auth::verify(self::header($body, self::NOW - 300), $body, self::SECRET, self::NOW));
        $this->assertTrue(Trusteed_Agent_Request_Auth::verify(self::header($body, self::NOW + 300), $body, self::SECRET, self::NOW));
    }

    public function test_cabecera_ausente_o_malformada_o_sin_secreto_se_rechaza(): void
    {
        $body = '{"a":1}';
        $this->assertFalse(Trusteed_Agent_Request_Auth::verify('', $body, self::SECRET, self::NOW));
        $this->assertFalse(Trusteed_Agent_Request_Auth::verify('basura', $body, self::SECRET, self::NOW));
        $this->assertFalse(Trusteed_Agent_Request_Auth::verify('t=abc,v1=zz', $body, self::SECRET, self::NOW));
        $this->assertFalse(Trusteed_Agent_Request_Auth::verify(self::header($body, self::NOW, ''), $body, '', self::NOW));
    }

    public function test_replay_se_rechaza_en_la_segunda_vez(): void
    {
        $h = self::header('{"a":1}', self::NOW);
        $this->assertTrue(Trusteed_Agent_Request_Auth::register_use($h));
        $this->assertFalse(Trusteed_Agent_Request_Auth::register_use($h));
    }

    public function test_replay_registra_con_ttl_de_600_segundos(): void
    {
        $h = self::header('{"a":1}', self::NOW);
        Trusteed_Agent_Request_Auth::register_use($h);
        $this->assertContains(600, $GLOBALS['__amcp_transient_ttls']);
    }
}
