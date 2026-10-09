<?php
/**
 * Autenticación de las llamadas de Trusteed a los endpoints de pedidos de agente.
 *
 * Formato idéntico a `signWebhookPayload` (apps/api/src/services/webhook.service.ts):
 *   X-Trusteed-Signature: t=<unix>,v1=<hex>
 *   v1 = HMAC-SHA256( secreto, "<t>.<cuerpo crudo>" )
 *
 * El secreto es el de instalación de enforcement (`trusteed_enforcement_hmac_secret`,
 * cifrado en reposo con Trusteed_Crypto_Helper): el mismo que ya firma las llamadas
 * del plugin hacia Trusteed.
 *
 * Esta clase decide sólo sí/no. No devuelve el motivo del rechazo: el endpoint
 * responde 401 sin detalle para no darle a un atacante un oráculo.
 *
 * @package Trusteed
 * @since   2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Trusteed_Agent_Request_Auth {

	/** Ventana de tolerancia del reloj, en segundos (a cada lado). */
	public const TOLERANCE_SECONDS = 300;

	/** Cuánto se recuerda una firma ya vista (anti-replay), en segundos. */
	public const REPLAY_TTL_SECONDS = 600;

	/**
	 * Comprueba la firma de una petición. Sin efectos secundarios.
	 *
	 * @param string $header   Valor de `X-Trusteed-Signature`.
	 * @param string $raw_body Cuerpo CRUDO tal como llegó (nunca el re-serializado).
	 * @param string $secret   Secreto de instalación en claro.
	 * @param int    $now      Reloj (unix); inyectable para las pruebas.
	 * @return bool
	 */
	public static function verify( string $header, string $raw_body, string $secret, int $now ): bool {
		if ( '' === $secret || '' === $header ) {
			return false;
		}
		$parsed = self::parse( $header );
		if ( null === $parsed ) {
			return false;
		}
		if ( abs( $now - $parsed['t'] ) > self::TOLERANCE_SECONDS ) {
			return false;
		}
		$expected = hash_hmac( 'sha256', $parsed['t'] . '.' . $raw_body, $secret );
		return hash_equals( $expected, $parsed['v1'] );
	}

	/**
	 * Anti-replay: registra la firma como usada. Devuelve false si ya se había visto.
	 *
	 * Sólo se llama con una firma YA verificada (si no, un atacante llenaría la
	 * tabla de transients con basura). No es atómico —get + set—; la ventana de
	 * carrera es de milisegundos y la idempotencia de /orders cubre el resto.
	 *
	 * @param string $header Valor de `X-Trusteed-Signature` ya verificado.
	 * @return bool true si es la primera vez que se ve.
	 */
	public static function register_use( string $header ): bool {
		$parsed = self::parse( $header );
		if ( null === $parsed ) {
			return false;
		}
		$key = 'trusteed_agent_sig_' . $parsed['v1'];
		if ( false !== get_transient( $key ) ) {
			return false;
		}
		set_transient( $key, 1, self::REPLAY_TTL_SECONDS );
		return true;
	}

	/**
	 * @param string $header Cabecera cruda.
	 * @return array{t:int, v1:string}|null
	 */
	private static function parse( string $header ): ?array {
		$t  = null;
		$v1 = null;
		foreach ( explode( ',', $header ) as $part ) {
			$kv = explode( '=', trim( $part ), 2 );
			if ( 2 !== count( $kv ) ) {
				continue;
			}
			if ( 't' === $kv[0] && ctype_digit( $kv[1] ) ) {
				$t = (int) $kv[1];
			} elseif ( 'v1' === $kv[0] && 1 === preg_match( '/^[0-9a-f]{64}$/', $kv[1] ) ) {
				$v1 = $kv[1];
			}
		}
		return ( null === $t || null === $v1 ) ? null : array(
			't'  => $t,
			'v1' => $v1,
		);
	}
}
