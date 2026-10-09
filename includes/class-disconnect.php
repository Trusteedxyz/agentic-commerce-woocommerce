<?php
/**
 * Baja del comercio contra el backend — flujo de DOS fases.
 *
 * ## El fallo que cierra
 *
 * `POST /api/v1/plugin/disconnect` no da de baja: responde `202 pending` con
 * un `confirmation_token` de un solo uso y 5 minutos de vida (spec-048 F6.BE
 * S5, anti-DoS por clave robada). Quien ejecuta la secuencia destructiva es
 * `POST /api/v1/plugin/disconnect/confirm`.
 *
 * El plugin llamaba sólo a la primera y descartaba la respuesta. Con el flag
 * del servidor en su valor por defecto (`DISCONNECT_CONFIRMATION_ENFORCED`
 * activo), eso significa que el botón «Desconectar» borraba las credenciales
 * locales y **no daba de baja nada**: el comercio desaparecía de su propio
 * WordPress y seguía publicado en `/.well-known/merchant-index.json`, en el
 * directorio y con su endpoint MCP anunciado, con la puntuación de confianza
 * recalculándose sobre un catálogo que ya nadie sincroniza.
 *
 * ## Por qué es una clase aparte y sin dependencias de WordPress
 *
 * La usan dos sitios con reglas distintas: el panel (que tiene el plugin
 * cargado) y `uninstall.php` (que NO puede arrancar clases del plugin — es la
 * regla de WordPress y su propia cabecera lo documenta). Recibe la función de
 * envío por parámetro para que se pueda probar sin WordPress y para que
 * `uninstall.php` pueda pasarle una versión cruda.
 *
 * @package Trusteed
 * @since   2.3.5
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ejecuta la baja completa contra el backend.
 */
class Trusteed_Disconnect {

	/** Fase 1: pide la baja. Puede contestar 200 (hecho) o 202 (falta confirmar). */
	const ENDPOINT_REQUEST = '/api/v1/plugin/disconnect';

	/** Fase 2: redime el token y ejecuta la baja de verdad. */
	const ENDPOINT_CONFIRM = '/api/v1/plugin/disconnect/confirm';

	/**
	 * Completa las dos fases.
	 *
	 * NUNCA lanza: quien llama sigue después con el borrado local, y una baja
	 * que se queda a medias porque el backend no contesta sería peor que la
	 * que ya teníamos. El resultado se devuelve para poder registrarlo.
	 *
	 * @param callable $post fn(string $endpoint, array $body): array{status:int, body:array}|null
	 *                       `null` (o cualquier cosa que no sea un array con
	 *                       `status`) se trata como fallo de transporte.
	 * @return array{ok:bool, phase:string, status:int}
	 */
	public static function run( $post ) {
		$first = self::call( $post, self::ENDPOINT_REQUEST, array() );
		if ( null === $first ) {
			return self::outcome( false, 'transport_error', 0 );
		}

		$status = (int) $first['status'];

		// 200: el servidor tiene la confirmación desactivada y ya ha dado de
		// baja. Es el camino legacy y se conserva a propósito: los dos flags
		// conviven durante el despliegue gradual del plugin.
		if ( 200 === $status ) {
			return self::outcome( true, 'direct', $status );
		}

		if ( 202 !== $status ) {
			return self::outcome( false, 'request_failed', $status );
		}

		$token = isset( $first['body']['confirmation_token'] )
			? (string) $first['body']['confirmation_token']
			: '';

		// Un 202 sin token no se puede completar. Es justo la respuesta que el
		// código anterior leía como éxito.
		if ( '' === $token ) {
			return self::outcome( false, 'pending_without_token', $status );
		}

		$second = self::call(
			$post,
			self::ENDPOINT_CONFIRM,
			array( 'confirmation_token' => $token )
		);
		if ( null === $second ) {
			return self::outcome( false, 'transport_error', 0 );
		}

		$confirmStatus = (int) $second['status'];
		if ( 200 !== $confirmStatus ) {
			return self::outcome( false, 'confirm_failed', $confirmStatus );
		}

		return self::outcome( true, 'confirmed', $confirmStatus );
	}

	/**
	 * Invoca al emisor y normaliza cualquier cosa que no sea una respuesta
	 * interpretable a `null`. Un `WP_Error` del cliente HTTP, una excepción o
	 * una forma inesperada acaban todos en el mismo sitio.
	 *
	 * @param callable $post     Emisor.
	 * @param string   $endpoint Ruta.
	 * @param array    $body     Cuerpo.
	 * @return array{status:int, body:array}|null
	 */
	private static function call( $post, $endpoint, $body ) {
		try {
			$response = call_user_func( $post, $endpoint, $body );
		} catch ( \Throwable $e ) {
			return null;
		}

		if ( ! is_array( $response ) || ! isset( $response['status'] ) ) {
			return null;
		}

		return array(
			'status' => (int) $response['status'],
			'body'   => isset( $response['body'] ) && is_array( $response['body'] )
				? $response['body']
				: array(),
		);
	}

	/**
	 * @param bool   $ok     Si la baja se completó.
	 * @param string $phase  Dónde se quedó.
	 * @param int    $status Último código HTTP visto.
	 * @return array{ok:bool, phase:string, status:int}
	 */
	private static function outcome( $ok, $phase, $status ) {
		return array(
			'ok'     => (bool) $ok,
			'phase'  => (string) $phase,
			'status' => (int) $status,
		);
	}
}
