<?php
/**
 * Identidad del agente: qué señal es sólo una declaración y cuál la prueba
 * (Task 5b del plan 04 de reglas de checkout; mismo criterio que Magento 1.3.5).
 *
 * Modelo de amenaza. El DID de `?trusteed_agent_id=` lo puede escribir
 * cualquiera, igual que el cuerpo de la Store API o el `$_POST` del checkout
 * clásico. Por eso:
 *
 * - Cualquier señal (DID, token, token con forma rota, marca de origen) sólo
 *   ENDURECE el trato: el checkout es de agente. Nunca lo aligera.
 * - Un DID declarado NO es una identidad verificada. Sólo un token VÁLIDO
 *   (firma Ed25519 contra el resolver del snapshot) cuyo DID coincida lo
 *   convierte en la atribución que el pedido guarda en `_trusteed_agent_did`
 *   y que R023 usa para el historial del agente.
 * - Token válido de OTRO agente que el declarado ⇒ token inválido; no se
 *   consume su jti.
 * - Sin DID declarado + token válido ⇒ el DID del token.
 * - Task 16 (revisión de seguridad): al servidor sólo viaja como `agentId` el
 *   DID que prueba un token VÁLIDO. Un DID sólo declarado (sin token, con un
 *   token INDETERMINATE, inválido o de otro agente) viaja como agente ANÓNIMO
 *   (`agentId: null`, `endpointClass: agentic_endpoint`): el historial del
 *   servidor se indexa por `agentId`, y un DID sin prueba dejaba heredar el
 *   historial de otro agente o quemar el de una víctima.
 *
 * Funciones puras (sin WordPress): testeables sin WooCommerce.
 *
 * @package Trusteed
 * @since   2.3.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Trusteed_Agent_Identity
 *
 * @since 2.3.6
 */
final class Trusteed_Agent_Identity {

	/**
	 * Campo del token de agente en el cuerpo de la Store API, en el `$_POST`
	 * del checkout clásico y en la meta del pedido: el mismo nombre que lee
	 * WC-AgenticTools, así que un agente que sólo manda el token deja de ser
	 * una persona para este plugin.
	 */
	const REQUEST_TOKEN_FIELD = '_trusteed_agent_token';

	/**
	 * Marca de sesión: la URL trajo un token sin forma de JWS. Se trata como
	 * token de agente inválido; se borra con el resto de claves tras evaluar.
	 */
	const SESSION_TOKEN_REJECTED = 'trusteed_agent_token_rejected';

	/** Techo de longitud antes de aplicar la expresión regular. */
	const MAX_TOKEN_LENGTH = 8192;

	/** JWS Compact: tres segmentos base64url separados por puntos. */
	const JWS_RE = '/^[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]*$/';

	/**
	 * Clasifica un token recibido de fuera.
	 *
	 * @param mixed $raw Valor sin confiar (ya sin barras de WordPress).
	 * @return array{token:string, rejected:bool} `rejected` = se presentó algo
	 *         que no es un JWS (o es demasiado largo): token inválido de agente.
	 */
	public static function accept_token( $raw ): array {
		$value = is_string( $raw ) ? trim( $raw ) : '';
		if ( '' === $value ) {
			return array( 'token' => '', 'rejected' => false );
		}
		if ( strlen( $value ) > self::MAX_TOKEN_LENGTH || 1 !== preg_match( self::JWS_RE, $value ) ) {
			return array( 'token' => '', 'rejected' => true );
		}
		return array( 'token' => $value, 'rejected' => false );
	}

	/**
	 * El primer candidato NO vacío decide (en el orden recibido); los
	 * siguientes no se miran aunque el primero esté roto.
	 *
	 * @param array<int,mixed> $candidates Valores sin confiar, por prioridad.
	 * @return array{token:string, rejected:bool}
	 */
	public static function first_token( array $candidates ): array {
		foreach ( $candidates as $raw ) {
			if ( is_string( $raw ) && '' !== trim( $raw ) ) {
				return self::accept_token( $raw );
			}
		}
		return array( 'token' => '', 'rejected' => false );
	}

	/**
	 * Token de agente de este checkout: el de la URL guardado en sesión (ya
	 * filtrado al capturarlo); si la URL traía uno roto, «rechazado»; si no, el
	 * primero no vacío de la petición (cuerpo de la Store API, meta del pedido,
	 * `$_POST` del clásico).
	 *
	 * @param object|null      $session        WC()->session (get()).
	 * @param array<int,mixed> $request_tokens Candidatos de la petición, por prioridad.
	 * @return array{token:string, rejected:bool}
	 */
	public static function resolve_token( ?object $session, array $request_tokens ): array {
		$stored = $session ? $session->get( Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN ) : null;
		if ( is_string( $stored ) && '' !== $stored ) {
			return array( 'token' => $stored, 'rejected' => false );
		}
		if ( $session && '1' === $session->get( self::SESSION_TOKEN_REJECTED ) ) {
			return array( 'token' => '', 'rejected' => true );
		}
		return self::first_token( $request_tokens );
	}

	/**
	 * Ata el token verificado al DID declarado (port de
	 * AgentIdentityResolver::bindToken de Magento).
	 *
	 * `did` es SÓLO la identidad que prueba un token VÁLIDO ('' en cualquier
	 * otro caso, Task 16): nunca el DID declarado sin prueba.
	 *
	 * @param string $declared_did DID declarado ('' si no hay).
	 * @param string $token_state  Trusteed_Token_State::* del verificador.
	 * @param string $token_did    DID que prueba el token ('' si no hay).
	 * @return array{state:string, did:string, mismatch:bool}
	 */
	public static function bind_token( string $declared_did, string $token_state, string $token_did ): array {
		if ( 'valid' !== $token_state ) {
			return array( 'state' => $token_state, 'did' => '', 'mismatch' => false );
		}
		if ( '' === $token_did ) {
			return array( 'state' => 'invalid', 'did' => '', 'mismatch' => false );
		}
		if ( '' === $declared_did || $token_did === $declared_did ) {
			return array( 'state' => 'valid', 'did' => $token_did, 'mismatch' => false );
		}
		return array( 'state' => 'invalid', 'did' => '', 'mismatch' => true );
	}
}

/**
 * La REST de pedidos (`POST|PUT /wc/v3/orders`, lotes incluidos) acepta
 * `meta_data` con cualquier clave, también las de atribución que escribe este
 * plugin tras verificar el token. Quien tenga claves REST podía así colgarle a
 * un pedido el DID de otro agente (R023). Este filtro devuelve esas claves al
 * valor que el pedido tenía guardado (o las quita si no lo tenía). No evalúa
 * reglas: ese camino sigue siendo de WC-AgenticTools.
 *
 * @since 2.3.6
 */
final class Trusteed_Rest_Order_Meta_Guard {

	/**
	 * Claves que sólo escribe este plugin tras un ALLOW con token verificado,
	 * más `_trusteed_agent_id`, que nadie escribe en este árbol pero que el
	 * emisor de fallos de checkout de WC-AgenticTools lee como DID de reserva.
	 */
	const PROTECTED_KEYS = array( '_trusteed_agent_did', '_trusteed_agent_status', '_trusteed_eval_at', '_trusteed_agent_id' );

	/** Filtro de WooCommerce que corre después de aplicar `meta_data` al pedido. */
	const HOOK = 'woocommerce_rest_pre_insert_shop_order_object';

	/**
	 * @return void
	 */
	public static function register(): void {
		add_filter( self::HOOK, array( self::class, 'restore_attribution_meta' ), 10, 2 );
	}

	/**
	 * @param mixed $order   Pedido ya modificado por la petición (WC_Order o WP_Error).
	 * @param mixed $request WP_REST_Request.
	 * @return mixed El mismo objeto.
	 */
	public static function restore_attribution_meta( $order, $request ) {
		$keys = self::requested_protected_keys( $request );
		if ( empty( $keys ) || ! is_object( $order ) || ! method_exists( $order, 'delete_meta_data' ) ) {
			return $order;
		}
		$id     = method_exists( $order, 'get_id' ) ? (int) $order->get_id() : 0;
		$stored = $id > 0 && function_exists( 'wc_get_order' ) ? wc_get_order( $id ) : null;
		foreach ( $keys as $key ) {
			$value = is_object( $stored ) && method_exists( $stored, 'get_meta' ) ? (string) $stored->get_meta( $key, true ) : '';
			if ( '' === $value ) {
				$order->delete_meta_data( $key );
			} else {
				$order->update_meta_data( $key, $value );
			}
		}
		return $order;
	}

	/**
	 * Claves protegidas que la petición intenta escribir (sin distinguir
	 * mayúsculas ni espacios alrededor).
	 *
	 * @param mixed $request WP_REST_Request.
	 * @return string[]
	 */
	private static function requested_protected_keys( $request ): array {
		$meta = is_object( $request ) && method_exists( $request, 'get_param' ) ? $request->get_param( 'meta_data' ) : null;
		if ( ! is_array( $meta ) ) {
			return array();
		}
		$found = array();
		foreach ( $meta as $entry ) {
			$key = is_array( $entry ) && isset( $entry['key'] ) && is_string( $entry['key'] ) ? strtolower( trim( $entry['key'] ) ) : '';
			if ( in_array( $key, self::PROTECTED_KEYS, true ) ) {
				$found[ $key ] = true;
			}
		}
		return array_keys( $found );
	}
}
