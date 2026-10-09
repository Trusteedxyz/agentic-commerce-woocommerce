<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Task 29b — endurecimiento del cliente de snapshot del WC principal, con el
 * `Trusteed_Snapshot_Client_Woo` REAL, JWS firmados de verdad (Ed25519) y JWKS
 * servido por el stub HTTP:
 *
 * - L3: la re-descarga forzada del JWKS que NO rescata la firma se recuerda 60 s
 *   (transient); dentro de la ventana no hay una segunda descarga y se clasifica
 *   con lo ya sabido. Un almacén que rechaza la escritura no rompe nada.
 * - L6a: el `validUntil` firmado se comprueba (anti-rollback): un snapshot
 *   caducado, servido o en caché, no está disponible.
 * - L6b: tope de tamaño del cuerpo HTTP antes de verificar.
 * - L6c: cabecera JWS `crit` (RFC 7515 §4.1.11) rechazada.
 *
 * El «reloj falso» es `$GLOBALS['__amcp_clock']` (wp-stubs.php): expira lo que
 * se escribió con TTL.
 */
final class SnapshotClientHardeningTest extends TestCase
{
    private const API      = 'https://api.trusteed.test';
    private const KID      = 'snap-kid-29b';
    private const MERCHANT = 'merchant-29b';
    private const INST     = 'inst-29b';
    private const SECRET   = 'example-hmac-secret-not-real-29b';

    private string $platformPriv = '';
    private string $platformPub  = '';
    private string $errorLog     = '';

    protected function setUp(): void
    {
        $this->errorLog = (string) tempnam(sys_get_temp_dir(), 'trusteed-wc-log');
        ini_set('error_log', $this->errorLog);
        $this->resetGlobals();

        $platform           = sodium_crypto_sign_keypair();
        $this->platformPriv = sodium_crypto_sign_secretkey($platform);
        $this->platformPub  = sodium_crypto_sign_publickey($platform);
        $this->publishJwks([self::KID => $this->platformPub]);
    }

    protected function tearDown(): void
    {
        $this->resetGlobals();
        @unlink($this->errorLog);
        ini_restore('error_log');
    }

    private function resetGlobals(): void
    {
        $GLOBALS['__amcp_transients_store']         = [];
        $GLOBALS['__amcp_transient_expiry']         = [];
        $GLOBALS['__amcp_transient_ttls']           = [];
        $GLOBALS['__amcp_transients_reject_writes'] = false;
        $GLOBALS['__amcp_remote_get_responses']     = [];
        $GLOBALS['__amcp_remote_get_calls']         = [];
        unset($GLOBALS['__amcp_clock']);
    }

    // ── L3: caché negativa de la re-descarga forzada del JWKS ───────────────

    public function test_a_forced_retry_that_does_not_rescue_the_signature_is_not_repeated_inside_the_window(): void
    {
        $this->seedJwksCache([self::KID => $this->platformPub]);
        $this->serveSnapshot($this->tamperedJws());

        $first = $this->client()->get_snapshot_result(self::MERCHANT);
        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_TAMPERED, $first['status']);
        self::assertCount(1, $this->jwksGets(), 'el primer checkout fuerza exactamente una re-descarga');

        $second = $this->client()->get_snapshot_result(self::MERCHANT);
        $third  = $this->client()->get_snapshot_result(self::MERCHANT);

        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_TAMPERED, $second['status'], 'se clasifica con lo ya sabido');
        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_TAMPERED, $third['status']);
        self::assertCount(1, $this->jwksGets(), 'dentro de la ventana no hay una segunda descarga del JWKS');
    }

    public function test_the_negative_entry_is_a_wp_transient_of_sixty_seconds(): void
    {
        $this->seedJwksCache([self::KID => $this->platformPub]);
        $this->serveSnapshot($this->tamperedJws());

        $this->client()->get_snapshot_result(self::MERCHANT);

        self::assertArrayHasKey($this->negKey(self::KID), $GLOBALS['__amcp_transients_store']);
        self::assertSame(60, $GLOBALS['__amcp_transient_ttls'][$this->negKey(self::KID)]);
    }

    public function test_the_retry_runs_again_when_the_window_has_expired(): void
    {
        $this->seedJwksCache([self::KID => $this->platformPub]);
        $this->serveSnapshot($this->tamperedJws());
        $this->client()->get_snapshot_result(self::MERCHANT);
        self::assertCount(1, $this->jwksGets());

        $GLOBALS['__amcp_clock'] = time() + 61;
        $again = $this->client()->get_snapshot_result(self::MERCHANT);

        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_TAMPERED, $again['status']);
        self::assertCount(2, $this->jwksGets(), 'pasados 60 s se vuelve a comprobar');
    }

    public function test_a_jwks_that_was_down_on_the_retry_stays_unavailable_inside_the_window_without_another_download(): void
    {
        $this->seedJwksCache([self::KID => $this->platformPub]);
        $this->serveSnapshot($this->tamperedJws());
        unset($GLOBALS['__amcp_remote_get_responses'][self::API . '/.well-known/jwks.json']); // caído

        $first  = $this->client()->get_snapshot_result(self::MERCHANT);
        $second = $this->client()->get_snapshot_result(self::MERCHANT);

        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_UNAVAILABLE, $first['status'], 'con el JWKS caído no se distingue rotación de manipulación');
        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_UNAVAILABLE, $second['status']);
        self::assertCount(1, $this->jwksGets());
    }

    public function test_a_store_that_rejects_the_write_retries_every_time_and_breaks_nothing(): void
    {
        $this->seedJwksCache([self::KID => $this->platformPub]);
        $this->serveSnapshot($this->tamperedJws());
        $GLOBALS['__amcp_transients_reject_writes'] = true;

        $first  = $this->client()->get_snapshot_result(self::MERCHANT);
        $second = $this->client()->get_snapshot_result(self::MERCHANT);

        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_TAMPERED, $first['status']);
        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_TAMPERED, $second['status']);
        self::assertCount(2, $this->jwksGets(), 'sin caché negativa se comporta como antes: una descarga por petición');
    }

    public function test_a_rotation_rescued_by_the_retry_leaves_no_negative_entry(): void
    {
        $rotated = sodium_crypto_sign_keypair();
        $this->seedJwksCache([self::KID => $this->platformPub]); // caché con la clave vieja
        $this->publishJwks(['kid-rotated' => sodium_crypto_sign_publickey($rotated)]);
        $this->serveSnapshot($this->signedSnapshot([], sodium_crypto_sign_secretkey($rotated), [], 'kid-rotated'));

        $result = $this->client()->get_snapshot_result(self::MERCHANT);

        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_OK, $result['status'], 'una rotación no es manipulación');
        self::assertArrayNotHasKey($this->negKey(self::KID), $GLOBALS['__amcp_transients_store']);
    }

    public function test_another_kid_does_not_inherit_the_negative_window_but_the_same_kid_does(): void
    {
        $this->seedJwksCache([self::KID => $this->platformPub]);
        $this->serveSnapshot($this->tamperedJws());
        $this->client()->get_snapshot_result(self::MERCHANT);
        self::assertCount(1, $this->jwksGets());
        self::assertArrayHasKey($this->negKey(self::KID), $GLOBALS['__amcp_transients_store']);

        $rotated = sodium_crypto_sign_keypair();
        $this->publishJwks([self::KID => $this->platformPub, 'kid-rotated' => sodium_crypto_sign_publickey($rotated)]);
        $this->serveSnapshot($this->signedSnapshot([], sodium_crypto_sign_secretkey($rotated), [], 'kid-rotated'));
        $rescued = $this->client()->get_snapshot_result(self::MERCHANT);
        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_OK, $rescued['status'], 'el kid nuevo no hereda la ventana del anterior');
        self::assertCount(2, $this->jwksGets(), 'el kid nuevo sí provoca su propia re-descarga');
        self::assertArrayNotHasKey($this->negKey('kid-rotated'), $GLOBALS['__amcp_transients_store'], 'un snapshot que verifica no deja entrada');

        // Se descarta la copia del snapshot bueno (solo queda el JWKS y las entradas negativas).
        foreach (array_keys($GLOBALS['__amcp_transients_store']) as $k) {
            if (false === strpos((string) $k, 'jwks')) {
                unset($GLOBALS['__amcp_transients_store'][$k]);
            }
        }
        $this->serveSnapshot($this->tamperedJws());
        $again = $this->client()->get_snapshot_result(self::MERCHANT);
        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_TAMPERED, $again['status']);
        self::assertCount(2, $this->jwksGets(), 'el mismo kid hereda la ventana');
    }

    public function test_a_jws_without_kid_uses_one_fixed_entry_and_a_long_kid_stays_within_the_transient_name_cap(): void
    {
        $this->seedJwksCache([self::KID => $this->platformPub]);
        $enc   = static fn (string $s): string => rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
        $noKid = $enc('{"alg":"EdDSA","typ":"cel-snapshot+jwt"}') . '.' . $enc('{}') . '.' . $enc(str_repeat('a', 64));
        $this->serveSnapshot($noKid);
        $this->client()->get_snapshot_result(self::MERCHANT);
        self::assertArrayHasKey('trusteed_jwks_forced_retry_failed_' . md5(self::API) . '_nokid', $GLOBALS['__amcp_transients_store']);

        $longKid = str_repeat('k', 600);
        $this->serveSnapshot($this->signedSnapshot([], null, [], $longKid));
        $this->client()->get_snapshot_result(self::MERCHANT);
        $key = $this->negKey($longKid);
        self::assertArrayHasKey($key, $GLOBALS['__amcp_transients_store']);
        self::assertLessThanOrEqual(172, strlen($key));
    }

    // ── L6a: validUntil firmado ─────────────────────────────────────────────

    public function test_a_served_snapshot_whose_valid_until_is_past_is_unavailable_and_not_cached(): void
    {
        $this->serveSnapshot($this->signedSnapshot(['validUntil' => gmdate('Y-m-d\TH:i:s.000\Z', time() - 5)]));

        $result = $this->client()->get_snapshot_result(self::MERCHANT);

        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_UNAVAILABLE, $result['status'], 'un snapshot antiguo bien firmado (rollback) no vale');
        self::assertNull($result['payload']);
        self::assertArrayNotHasKey('trusteed_snap_' . substr(md5(self::MERCHANT), 0, 12), $GLOBALS['__amcp_transients_store']);
    }

    public function test_a_cached_payload_that_outlived_its_own_valid_until_is_not_served(): void
    {
        $GLOBALS['__amcp_transients_store']['trusteed_snap_' . substr(md5(self::MERCHANT), 0, 12)] = [
            'merchantId' => self::MERCHANT,
            'rules'      => [],
            'validUntil' => gmdate('Y-m-d\TH:i:s.000\Z', time() - 5),
        ];
        // El servidor sirve uno vigente: se descarga en vez de usar la copia vencida.
        $this->serveSnapshot($this->signedSnapshot(['validUntil' => gmdate('Y-m-d\TH:i:s.000\Z', time() + 120), 'ruleSetVersion' => 'fresh']));

        $result = $this->client()->get_snapshot_result(self::MERCHANT);

        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_OK, $result['status']);
        self::assertSame('fresh', $result['payload']['ruleSetVersion']);
        self::assertCount(1, $this->snapshotGets(), 'la copia vencida no se sirvió');
    }

    public function test_a_cached_payload_that_outlived_its_valid_until_with_the_server_down_is_unavailable(): void
    {
        $GLOBALS['__amcp_transients_store']['trusteed_snap_' . substr(md5(self::MERCHANT), 0, 12)] = [
            'merchantId' => self::MERCHANT,
            'validUntil' => gmdate('Y-m-d\TH:i:s.000\Z', time() - 5),
        ];

        $result = $this->client()->get_snapshot_result(self::MERCHANT);

        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_UNAVAILABLE, $result['status']);
    }

    /**
     * A snapshot signed by our server ALWAYS carries `validUntil` (ISO-8601
     * with zone). Absent or unreadable is malformed: `unavailable`, same as the
     * expired one, never `tampered` (the signature verified) and never served.
     *
     * @return array<string,array{0:bool,1:mixed}> [absent?, value]
     */
    public static function unusableValidUntil(): array
    {
        return array(
            'absent'                       => array( true, null ),
            'null'                         => array( false, null ),
            'empty string'                 => array( false, '' ),
            'garbage string'               => array( false, 'not-a-date' ),
            'integer'                      => array( false, 1893456000 ),
            'integer out of range'         => array( false, PHP_INT_MAX ),
            'huge float'                   => array( false, 1.0e+20 ),
            'huge numeric string'          => array( false, '99999999999999999999' ),
            'relative date strtotime takes' => array( false, 'tomorrow' ),
            'impossible date'              => array( false, '2099-13-45T00:00:00Z' ),
            'ISO without zone'             => array( false, '2099-01-01T00:00:00' ),
            'list'                         => array( false, array( '2099-01-01T00:00:00Z' ) ),
            'boolean'                      => array( false, true ),
        );
    }

    /** @return array<string,mixed> */
    private function payloadWithValidUntil( bool $absent, $value ): array
    {
        $payload = $this->basePayload();
        if ( $absent ) {
            unset( $payload['validUntil'] );
        } else {
            $payload['validUntil'] = $value;
        }

        return $payload;
    }

    /** @dataProvider unusableValidUntil */
    public function test_a_served_snapshot_without_a_usable_valid_until_is_unavailable_not_tampered_and_not_cached( bool $absent, $value ): void
    {
        $this->serveSnapshot( $this->sign( $this->payloadWithValidUntil( $absent, $value ) ) );

        $result = $this->client()->get_snapshot_result( self::MERCHANT );

        self::assertSame( Trusteed_Snapshot_Client_Woo::STATUS_UNAVAILABLE, $result['status'] );
        self::assertNull( $result['payload'] );
        self::assertArrayNotHasKey( 'trusteed_snap_' . substr( md5( self::MERCHANT ), 0, 12 ), $GLOBALS['__amcp_transients_store'] );
    }

    /** @dataProvider unusableValidUntil */
    public function test_a_cached_payload_without_a_usable_valid_until_is_not_served( bool $absent, $value ): void
    {
        $GLOBALS['__amcp_transients_store']['trusteed_snap_' . substr( md5( self::MERCHANT ), 0, 12 )] = $this->payloadWithValidUntil( $absent, $value );

        $result = $this->client()->get_snapshot_result( self::MERCHANT );

        self::assertSame( Trusteed_Snapshot_Client_Woo::STATUS_UNAVAILABLE, $result['status'], 'servidor caído y copia sin validUntil legible: no hay snapshot' );
        self::assertNull( $result['payload'] );
    }

    public function test_a_readable_future_valid_until_is_served_and_cached(): void
    {
        // Control: with a good validUntil the same path serves and caches.
        $this->serveSnapshot( $this->sign( $this->basePayload() ) );

        $result = $this->client()->get_snapshot_result( self::MERCHANT );

        self::assertSame( Trusteed_Snapshot_Client_Woo::STATUS_OK, $result['status'] );
        self::assertArrayHasKey( 'trusteed_snap_' . substr( md5( self::MERCHANT ), 0, 12 ), $GLOBALS['__amcp_transients_store'] );
    }

    // ── L6b: tope de tamaño del cuerpo ──────────────────────────────────────

    public function test_the_snapshot_request_asks_for_one_byte_more_than_the_cap(): void
    {
        $this->serveSnapshot($this->signedSnapshot([]));

        $this->client()->get_snapshot_result(self::MERCHANT);

        $gets = $this->snapshotGets();
        self::assertCount(1, $gets);
        self::assertSame(4194304 + 1, $gets[0]['args']['limit_response_size']);
    }

    public function test_an_oversized_body_is_not_verified_and_is_unavailable(): void
    {
        $this->serveSnapshot(str_repeat('a', 4194304 + 1));

        $result = $this->client()->get_snapshot_result(self::MERCHANT);

        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_UNAVAILABLE, $result['status']);
        self::assertCount(0, $this->jwksGets(), 'ni siquiera se llegó a verificar (no se pidió el JWKS)');
    }

    public function test_a_body_exactly_at_the_cap_still_goes_through_verification(): void
    {
        $this->serveSnapshot(str_repeat('a', 4194304));

        $result = $this->client()->get_snapshot_result(self::MERCHANT);

        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_TAMPERED, $result['status'], 'llegó a verificarse y no tiene forma de JWS');
    }

    // ── L6c: cabecera crit ──────────────────────────────────────────────────

    public function test_a_crit_header_is_rejected_even_with_a_valid_signature(): void
    {
        $this->serveSnapshot($this->signedSnapshot([], null, ['crit' => ['exp']]));

        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_TAMPERED, $this->client()->get_snapshot_result(self::MERCHANT)['status']);
    }

    public function test_an_empty_crit_header_is_rejected_too(): void
    {
        $this->serveSnapshot($this->signedSnapshot([], null, ['crit' => []]));

        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_TAMPERED, $this->client()->get_snapshot_result(self::MERCHANT)['status']);
    }

    public function test_the_same_snapshot_without_crit_verifies(): void
    {
        $this->serveSnapshot($this->signedSnapshot([]));

        self::assertSame(Trusteed_Snapshot_Client_Woo::STATUS_OK, $this->client()->get_snapshot_result(self::MERCHANT)['status']);
    }

    // ── Arnés ────────────────────────────────────────────────────────────────

    private function client(): Trusteed_Snapshot_Client_Woo
    {
        return new Trusteed_Snapshot_Client_Woo(self::API, self::INST, self::SECRET);
    }

    /** @return array<string,mixed> */
    private function basePayload(): array
    {
        return [
            'merchantId'     => self::MERCHANT,
            'ruleSetVersion' => 'v1.0',
            'validUntil'     => gmdate('Y-m-d\TH:i:s.000\Z', time() + 120),
            'rules'          => [],
            'agentDidResolver' => [],
        ];
    }

    /**
     * @param array<string,mixed> $payloadOverrides
     * @param array<string,mixed> $headerExtra
     */
    private function signedSnapshot(array $payloadOverrides, ?string $priv = null, array $headerExtra = [], string $kid = self::KID): string
    {
        return $this->sign(array_merge($this->basePayload(), $payloadOverrides), $priv, $headerExtra, $kid);
    }

    /**
     * @param array<string,mixed> $payload
     * @param array<string,mixed> $headerExtra
     */
    private function sign(array $payload, ?string $priv = null, array $headerExtra = [], string $kid = self::KID): string
    {
        $h = self::b64((string) json_encode(array_merge(['alg' => 'EdDSA', 'kid' => $kid, 'typ' => 'cel-snapshot+jwt'], $headerExtra)));
        $b = self::b64((string) json_encode($payload, JSON_UNESCAPED_SLASHES));

        return $h . '.' . $b . '.' . self::b64(sodium_crypto_sign_detached($h . '.' . $b, $priv ?? $this->platformPriv));
    }

    private function negKey(string $kid): string
    {
        return 'trusteed_jwks_forced_retry_failed_' . md5(self::API) . '_' . md5($kid);
    }

    private function tamperedJws(): string
    {
        return $this->signedSnapshot([], sodium_crypto_sign_secretkey(sodium_crypto_sign_keypair()));
    }

    /** @param array<string,string> $keys kid => raw public key */
    private function publishJwks(array $keys): void
    {
        $GLOBALS['__amcp_remote_get_responses'][self::API . '/.well-known/jwks.json'] = [
            'response' => ['code' => 200],
            'body'     => (string) json_encode(['keys' => $this->jwkList($keys)]),
        ];
    }

    /** @param array<string,string> $keys kid => raw public key */
    private function seedJwksCache(array $keys): void
    {
        $GLOBALS['__amcp_transients_store']['trusteed_jwks'] = $this->jwkList($keys);
    }

    /**
     * @param array<string,string> $keys
     * @return array<int,array<string,string>>
     */
    private function jwkList(array $keys): array
    {
        $jwks = [];
        foreach ($keys as $kid => $raw) {
            $jwks[] = ['kty' => 'OKP', 'crv' => 'Ed25519', 'kid' => $kid, 'x' => self::b64($raw)];
        }

        return $jwks;
    }

    private function serveSnapshot(string $body): void
    {
        $GLOBALS['__amcp_remote_get_responses'][self::API . '/v1/rules/snapshot/' . rawurlencode(self::MERCHANT)] = ['response' => ['code' => 200], 'body' => $body];
    }

    /** @return array<int,array{url:string,args:array<string,mixed>}> */
    private function jwksGets(): array
    {
        return array_values(array_filter(
            $GLOBALS['__amcp_remote_get_calls'],
            static fn (array $c): bool => str_ends_with($c['url'], '/.well-known/jwks.json')
        ));
    }

    /** @return array<int,array{url:string,args:array<string,mixed>}> */
    private function snapshotGets(): array
    {
        return array_values(array_filter(
            $GLOBALS['__amcp_remote_get_calls'],
            static fn (array $c): bool => str_contains($c['url'], '/v1/rules/snapshot/')
        ));
    }

    private static function b64(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }
}
