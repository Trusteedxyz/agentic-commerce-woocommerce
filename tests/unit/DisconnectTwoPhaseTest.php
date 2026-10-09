<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * La baja del comercio tiene DOS fases y el plugin sólo hacía la primera.
 *
 * `POST /api/v1/plugin/disconnect` responde `202 pending` con un
 * `confirmation_token` de un solo uso y 5 minutos de vida
 * (`DISCONNECT_CONFIRMATION_ENFORCED`, por defecto activo desde spec-048
 * F6.BE S5); quien da de baja de verdad es
 * `POST /api/v1/plugin/disconnect/confirm`. El botón «Desconectar» del panel
 * llamaba sólo a la primera, ignoraba el 202 y borraba las opciones locales:
 * el comercio desaparecía de su propio WordPress y seguía publicado en el
 * registro público, en el directorio y con su endpoint MCP anunciado.
 *
 * @covers Trusteed_Disconnect
 */
final class DisconnectTwoPhaseTest extends TestCase
{
    /** @var list<array{0:string,1:array}> */
    private array $calls = [];

    /**
     * @param list<array{status:int,body:array}> $responses
     */
    private function poster(array $responses): callable
    {
        $this->calls = [];
        $i = 0;
        return function (string $endpoint, array $body) use (&$i, $responses) {
            $this->calls[] = [$endpoint, $body];
            $r = $responses[$i] ?? ['status' => 500, 'body' => []];
            $i++;
            return $r;
        };
    }

    public function testCompletesBothPhasesWhenTheApiAsksForConfirmation(): void
    {
        $post = $this->poster([
            ['status' => 202, 'body' => ['pending' => true, 'confirmation_token' => 'tok-abc']],
            ['status' => 200, 'body' => ['success' => true]],
        ]);

        $result = Trusteed_Disconnect::run($post);

        $this->assertTrue($result['ok']);
        $this->assertSame('confirmed', $result['phase']);
        $this->assertCount(2, $this->calls);
        $this->assertSame('/api/v1/plugin/disconnect', $this->calls[0][0]);
        $this->assertSame('/api/v1/plugin/disconnect/confirm', $this->calls[1][0]);
        $this->assertSame(['confirmation_token' => 'tok-abc'], $this->calls[1][1]);
    }

    public function testSingleCallStillWorksWhenTheApiDisconnectsDirectly(): void
    {
        // `DISCONNECT_CONFIRMATION_ENFORCED=false` en el servidor: 200 directo.
        $post = $this->poster([['status' => 200, 'body' => ['success' => true]]]);

        $result = Trusteed_Disconnect::run($post);

        $this->assertTrue($result['ok']);
        $this->assertSame('direct', $result['phase']);
        $this->assertCount(1, $this->calls);
    }

    public function testA202WithoutTokenIsAFailure_notASilentSuccess(): void
    {
        // Es exactamente lo que ocurría antes: un 202 se leía como «hecho».
        $post = $this->poster([['status' => 202, 'body' => ['pending' => true]]]);

        $result = Trusteed_Disconnect::run($post);

        $this->assertFalse($result['ok']);
        $this->assertSame('pending_without_token', $result['phase']);
        $this->assertCount(1, $this->calls);
    }

    public function testAFailedConfirmationIsReportedAsFailed(): void
    {
        $post = $this->poster([
            ['status' => 202, 'body' => ['confirmation_token' => 'tok-abc']],
            ['status' => 410, 'body' => ['error' => 'token_expired']],
        ]);

        $result = Trusteed_Disconnect::run($post);

        $this->assertFalse($result['ok']);
        $this->assertSame('confirm_failed', $result['phase']);
        $this->assertSame(410, $result['status']);
    }

    public function testATransportErrorDoesNotThrow(): void
    {
        // El llamador SIEMPRE sigue con el borrado local: la desinstalación no
        // puede quedarse a medias porque el backend no conteste.
        $post = function (string $endpoint, array $body) {
            return null; // lo que devuelve un WP_Error normalizado por el cliente
        };

        $result = Trusteed_Disconnect::run($post);

        $this->assertFalse($result['ok']);
        $this->assertSame('transport_error', $result['phase']);
    }

    public function testTheFirstCallCarriesAnEmptyBody_theKeyTravelsInTheHeader(): void
    {
        $post = $this->poster([['status' => 200, 'body' => []]]);

        Trusteed_Disconnect::run($post);

        $this->assertSame([], $this->calls[0][1]);
    }
}
