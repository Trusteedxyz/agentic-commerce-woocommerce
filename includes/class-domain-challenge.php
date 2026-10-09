<?php
/**
 * Prueba de control del dominio al reconectar una tienda ya registrada.
 *
 * Al reconectar, `POST /api/v1/plugin/onboard` responde 202 con un token de un
 * solo uso (10 min). Trusteed lo descarga de
 * `<tienda>/.well-known/amcp-verify.txt` y sólo entonces rota y entrega las
 * credenciales (`POST /api/v1/plugin/onboard/verify-challenge`).
 *
 * El token se guarda en un transitorio y se sirve desde el propio WordPress:
 * no se escribe ningún fichero en el docroot.
 *
 * @package Trusteed
 * @since   2.4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Trusteed_Domain_Challenge {

	public const PATH          = '/.well-known/amcp-verify.txt';
	public const TRANSIENT     = 'trusteed_amcp_verify_token';
	public const TTL_SECONDS   = 600;
	private const TOKEN_FORMAT = '/^[a-f0-9]{32,128}$/';

	/**
	 * Engancha el servidor del token. Antes que cualquier plantilla.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'init', array( self::class, 'maybe_serve' ), 0 );
	}

	/**
	 * Token del cuerpo 202 de `/plugin/onboard`, o null si no es un reto válido.
	 *
	 * @param mixed $body Cuerpo decodificado.
	 * @return string|null
	 */
	public static function token_from_body( $body ): ?string {
		if ( ! is_array( $body ) || ( $body['reason'] ?? null ) !== 'domain_verification_required' ) {
			return null;
		}
		$token = $body['challenge']['token'] ?? null;
		return is_string( $token ) && 1 === preg_match( self::TOKEN_FORMAT, $token ) ? $token : null;
	}

	/**
	 * Lo que se responde a una petición: el token si la ruta es la del reto y
	 * hay uno pendiente; null en cualquier otro caso (WordPress sigue normal).
	 *
	 * @param string     $request_uri URI de la petición.
	 * @param mixed      $token       Token pendiente (transitorio).
	 * @return string|null
	 */
	public static function response_for( string $request_uri, $token ): ?string {
		$path = (string) wp_parse_url( $request_uri, PHP_URL_PATH );
		if ( self::PATH !== $path ) {
			return null;
		}
		return is_string( $token ) && 1 === preg_match( self::TOKEN_FORMAT, $token ) ? $token : null;
	}

	/**
	 * @param string $token Token del reto.
	 * @return void
	 */
	public static function publish( string $token ): void {
		set_transient( self::TRANSIENT, $token, self::TTL_SECONDS );
	}

	/**
	 * @return void
	 */
	public static function clear(): void {
		delete_transient( self::TRANSIENT );
	}

	/**
	 * Hook de `init`: sirve el token en texto plano y termina la petición.
	 *
	 * @return void
	 */
	public static function maybe_serve(): void {
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( '' === $uri ) {
			return;
		}
		$body = self::response_for( $uri, get_transient( self::TRANSIENT ) );
		if ( null === $body ) {
			return;
		}
		nocache_headers();
		header( 'Content-Type: text/plain; charset=utf-8' );
		echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- token hex validado.
		exit;
	}
}
