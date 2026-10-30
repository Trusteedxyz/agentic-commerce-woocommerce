<?php

declare(strict_types=1);

/**
 * «Probar conexión» daba error con la API en «degraded» (2026-10-30).
 *
 * `/api/v1/health` responde `degraded` cuando todas las comprobaciones
 * obligatorias pasan y falla alguna de las que se toleran: la API atiende.
 * El plugin sólo aceptaba `ok` y enseñaba «Error en la solicitud» con la
 * tienda conectada y funcionando. Ver
 * wiki/gotchas/probar-conexion-woo-fallaba-con-la-api-en-degraded-2026-10-30.md
 *
 * Aislamiento de proceso por el mismo motivo que ApiClientErrorMessagePreferenceTest.
 */

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class ApiClientHealthDegradedTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('TRUSTEED_API_BASE')) {
            define('TRUSTEED_API_BASE', 'https://api.trusteed.xyz');
        }
        require_once __DIR__ . '/../../includes/class-api-client.php';
    }

    public function test_ok_y_degraded_son_operativos(): void
    {
        $this->assertTrue(Trusteed_Api_Client::is_health_operational(200, ['status' => 'ok']));
        $this->assertTrue(Trusteed_Api_Client::is_health_operational(200, ['status' => 'degraded']));
    }

    public function test_caido_o_respuesta_rara_no_es_operativo(): void
    {
        $this->assertFalse(Trusteed_Api_Client::is_health_operational(503, ['status' => 'ok']));
        $this->assertFalse(Trusteed_Api_Client::is_health_operational(200, ['status' => 'unhealthy']));
        $this->assertFalse(Trusteed_Api_Client::is_health_operational(200, []));
        $this->assertFalse(Trusteed_Api_Client::is_health_operational(200, 'ok'));
    }
}
