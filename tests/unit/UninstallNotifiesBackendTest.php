<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * `uninstall.php` avisa a Trusteed, y lo hace ANTES de borrar las opciones.
 *
 * Hasta el 2026-09-05 no avisaba en absoluto: WooCommerce no tiene equivalente
 * al `app/uninstalled` de Shopify, así que un comercio que borraba el plugin
 * seguía publicado indefinidamente en el registro público y en el directorio.
 *
 * El orden importa y no es un detalle de estilo: el aviso necesita
 * `agenticmcp_api_key` y la URL base, que el bucle de limpieza borra. Ponerlo
 * después lo dejaría sin credenciales y fallaría en silencio — que es
 * indistinguible de no haberlo puesto.
 */
final class UninstallNotifiesBackendTest extends TestCase
{
    private string $source;

    protected function setUp(): void
    {
        $this->source = (string) file_get_contents(__DIR__ . '/../../uninstall.php');
    }

    public function testItCallsTheTwoPhaseDisconnect(): void
    {
        $this->assertStringContainsString(
            'Trusteed_Disconnect::run(',
            $this->source,
            'uninstall.php ya no avisa al backend: un comercio que borre el plugin '
            . 'seguirá publicado en el registro público sin que nada lo detecte.'
        );
    }

    public function testTheNoticeRunsBeforeAnyOptionIsDeleted(): void
    {
        $notice = strpos($this->source, 'Trusteed_Disconnect::run(');
        $firstDelete = strpos($this->source, 'delete_option(');

        $this->assertNotFalse($notice);
        $this->assertNotFalse($firstDelete, 'uninstall.php ya no borra opciones: revisa este gate.');
        $this->assertLessThan(
            $firstDelete,
            $notice,
            'El aviso va DESPUÉS del primer delete_option(): para entonces la clave '
            . 'de API ya no existe y la llamada fallaría en silencio.'
        );
    }

    public function testItReadsTheKeyThroughTheCryptoHelper_notRaw(): void
    {
        // La opción está cifrada en reposo. Leerla en crudo mandaría el
        // ciphertext como credencial y el backend respondería 401.
        $this->assertStringContainsString(
            'Trusteed_Crypto_Helper::decrypt(',
            $this->source
        );
    }

    public function testItDoesNotBootThePlugin(): void
    {
        // Regla de WordPress y de la propia cabecera del fichero: uninstall.php
        // no arranca el plugin. Se permiten sólo las clases autocontenidas.
        $this->assertStringNotContainsString('class-plugin.php', $this->source);
        $this->assertStringNotContainsString('Trusteed_Plugin::', $this->source);
    }
}
