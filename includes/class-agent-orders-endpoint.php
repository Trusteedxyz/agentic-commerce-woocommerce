<?php
/**
 * Endpoints REST `trusteed/v1` para que Trusteed cree y cobre pedidos de agente
 * en esta tienda (plan 2026-10-05, tareas 4-6).
 *
 *   POST /wp-json/trusteed/v1/quote                  presupuesto con el motor de Woo
 *   POST /wp-json/trusteed/v1/orders                 crea el pedido (idempotente)
 *   POST /wp-json/trusteed/v1/orders/{id}/paid       confirma el pago y descuenta stock
 *
 * Apagados por defecto: sin `trusteed_agent_orders_enabled = yes` no se registra
 * ninguna ruta (404). Autenticación: ver Trusteed_Agent_Request_Auth.
 *
 * Esta clase decide (validar, idempotencia, comparar totales, reglas, pagado);
 * todo lo que toca WooCommerce está detrás de un «puerto»
 * (Trusteed_Agent_Woo_Adapter en producción, un doble en las pruebas):
 *   quote(array $input): array|WP_Error
 *   find_order_by_trusteed_id(string): ?array
 *   get_order(int): ?array
 *   create_order(array $input): array|WP_Error
 *   evaluate_rules(int $order_id, ?string $agent_did): 'ALLOW'|'BLOCK'|'UNAVAILABLE'
 *   delete_order(int): void
 *   complete_payment(int $order_id, string $payment_reference): void
 *   hold_for_review(int $order_id, string $note): void
 * Un pedido viaja como array: id, number, status, total_cents, currency,
 * trusteed_order_id, is_paid, transaction_id.
 *
 * @package Trusteed
 * @since   2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Trusteed_Agent_Orders_Endpoint {

	public const REST_NAMESPACE = 'trusteed/v1';
	public const OPTION_ENABLED = 'trusteed_agent_orders_enabled';
	public const SIGNATURE_HEADER = 'x_trusteed_signature';

	/** @var object */
	private $port;

	/** @var callable */
	private $secret_provider;

	/** @var callable */
	private $clock;

	/**
	 * @param object        $port            Puerto Woo (ver cabecera).
	 * @param callable      $secret_provider Devuelve el secreto de instalación en claro ('' si no hay).
	 * @param callable|null $clock           Devuelve el unix actual (inyectable en pruebas).
	 */
	public function __construct( $port, callable $secret_provider, ?callable $clock = null ) {
		$this->port            = $port;
		$this->secret_provider = $secret_provider;
		$this->clock           = $clock ?? static function (): int {
			return time();
		};
	}

	/**
	 * Construye el endpoint de producción y engancha `rest_api_init`.
	 *
	 * @param Trusteed_Checkout_Enforcer|null $enforcer Evaluador de reglas locales (null ⇒ cierra con 503).
	 * @return void
	 */
	public static function boot( $enforcer ): void {
		require_once __DIR__ . '/class-agent-woo-adapter.php';
		$endpoint = new self(
			new Trusteed_Agent_Woo_Adapter( $enforcer ),
			static function (): string {
				$raw = (string) Trusteed_Options::get_option( 'enforcement_hmac_secret', '' );
				return Trusteed_Crypto_Helper::decrypt( $raw );
			}
		);
		add_action( 'rest_api_init', array( $endpoint, 'register_routes' ) );
	}

	/**
	 * @return bool
	 */
	public function is_enabled(): bool {
		return 'yes' === get_option( self::OPTION_ENABLED, 'no' );
	}

	/**
	 * Registra las rutas sólo si la opción está en `yes`.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}
		$common = array(
			'methods'             => 'POST',
			'permission_callback' => array( $this, 'authorize' ),
		);
		register_rest_route( self::REST_NAMESPACE, '/quote', $common + array( 'callback' => array( $this, 'handle_quote' ) ) );
		register_rest_route( self::REST_NAMESPACE, '/orders', $common + array( 'callback' => array( $this, 'handle_create_order' ) ) );
		register_rest_route( self::REST_NAMESPACE, '/orders/(?P<id>\d+)/paid', $common + array( 'callback' => array( $this, 'handle_paid' ) ) );
	}

	/**
	 * permission_callback: firma HMAC + ventana + anti-replay. 401 sin detalle.
	 *
	 * @param object $request Petición REST.
	 * @return true|WP_Error
	 */
	public function authorize( $request ) {
		if ( ! $this->is_enabled() ) {
			return new WP_Error( 'rest_no_route', 'No route was found matching the URL and request method.', array( 'status' => 404 ) );
		}
		$deny = new WP_Error( 'trusteed_unauthorized', 'Unauthorized', array( 'status' => 401 ) );
		try {
			$secret = (string) call_user_func( $this->secret_provider );
		} catch ( \Throwable $e ) {
			return $deny;
		}
		$header = (string) $request->get_header( self::SIGNATURE_HEADER );
		$now    = (int) call_user_func( $this->clock );
		if ( ! Trusteed_Agent_Request_Auth::verify( $header, (string) $request->get_body(), $secret, $now ) ) {
			return $deny;
		}
		// Sólo una firma ya verificada entra en el registro anti-replay.
		if ( ! Trusteed_Agent_Request_Auth::register_use( $header ) ) {
			return $deny;
		}
		return true;
	}

	// ── POST /quote ──────────────────────────────────────────────────────────

	/**
	 * @param object $request Petición REST.
	 * @return WP_REST_Response
	 */
	public function handle_quote( $request ) {
		if ( ! $this->is_enabled() ) {
			return self::not_found();
		}
		$body  = self::json_body( $request );
		$input = self::normalize_input( $body );
		if ( null === $input ) {
			return self::error( 400, 'invalid_request', 'Invalid quote request.' );
		}
		try {
			$quote = $this->port->quote( $input );
		} catch ( \Throwable $e ) {
			return $this->internal_error( 'quote', $e );
		}
		if ( $quote instanceof WP_Error ) {
			return self::from_wp_error( $quote );
		}
		$quote['quote_hash'] = self::quote_hash( $quote );
		return new WP_REST_Response( $quote, 200 );
	}

	// ── POST /orders ─────────────────────────────────────────────────────────

	/**
	 * @param object $request Petición REST.
	 * @return WP_REST_Response
	 */
	public function handle_create_order( $request ) {
		if ( ! $this->is_enabled() ) {
			return self::not_found();
		}
		$body  = self::json_body( $request );
		$input = self::normalize_input( $body );
		$tid   = isset( $body['trusteed_order_id'] ) && is_string( $body['trusteed_order_id'] ) ? $body['trusteed_order_id'] : '';
		$sid   = isset( $body['checkout_session_id'] ) && is_string( $body['checkout_session_id'] ) ? $body['checkout_session_id'] : '';
		$exp   = $body['expected_total_cents'] ?? null;
		if (
			null === $input
			|| 1 !== preg_match( '/^[A-Za-z0-9_\-]{1,64}$/', $tid )
			|| 1 !== preg_match( '/^[A-Za-z0-9_\-]{1,128}$/', $sid )
			|| ! is_int( $exp ) || $exp < 0
		) {
			return self::error( 400, 'invalid_request', 'Invalid order request.' );
		}
		$did = isset( $body['agent_did'] ) && is_string( $body['agent_did'] ) && 1 === preg_match( '/^[A-Za-z0-9:._%\-]{1,256}$/', $body['agent_did'] )
			? $body['agent_did']
			: null;

		$input['trusteed_order_id']   = $tid;
		$input['checkout_session_id'] = $sid;
		$input['agent_did']           = $did;

		// Idempotencia: el mismo trusteed_order_id nunca crea dos pedidos.
		$lock_key = 'trusteed_agent_order_lock_' . $tid;
		$locked   = false;
		try {
			$existing = $this->port->find_order_by_trusteed_id( $tid );
			if ( null !== $existing ) {
				return new WP_REST_Response( self::order_payload( $existing ), 200 );
			}
			if ( false !== get_transient( $lock_key ) ) {
				return self::error( 409, 'in_progress', 'This order is already being created.' );
			}
			set_transient( $lock_key, 1, 30 );
			$locked = true;
			return $this->create_and_validate( $input, $exp );
		} catch ( \Throwable $e ) {
			return $this->internal_error( 'create_order', $e );
		} finally {
			// Sólo suelta el cerrojo quien lo tomó: otra petición en curso lo conserva.
			if ( $locked ) {
				delete_transient( $lock_key );
			}
		}
	}

	/**
	 * @param array<string,mixed> $input Entrada normalizada.
	 * @param int                 $expected_total_cents Total que Trusteed aprobó.
	 * @return WP_REST_Response
	 */
	private function create_and_validate( array $input, int $expected_total_cents ) {
		$order = $this->port->create_order( $input );
		if ( $order instanceof WP_Error ) {
			return self::from_wp_error( $order );
		}
		$id = (int) $order['id'];

		// El total que Woo ha calculado manda: si no es el aprobado, el pedido no vale.
		if ( (int) $order['total_cents'] !== $expected_total_cents ) {
			$this->port->delete_order( $id );
			$this->log( 'warning', sprintf( 'total_mismatch trusteed_order_id=%s', $input['trusteed_order_id'] ) );
			return new WP_REST_Response(
				array(
					'error' => array(
						'code'                 => 'total_mismatch',
						'message'              => 'The order total differs from the approved total.',
						'expected_total_cents' => $expected_total_cents,
						'actual_total_cents'   => (int) $order['total_cents'],
					),
				),
				409
			);
		}

		// Reglas locales del comerciante, evaluadas a mano (aquí no hay checkout).
		$decision = (string) $this->port->evaluate_rules( $id, $input['agent_did'] );
		if ( 'ALLOW' !== $decision ) {
			$this->port->delete_order( $id );
			if ( 'BLOCK' === $decision ) {
				return self::error( 422, 'rule_blocked', 'The order did not pass the store\'s verification rules.' );
			}
			return self::error( 503, 'rules_unavailable', 'The order could not be verified right now.' );
		}
		return new WP_REST_Response( self::order_payload( $order ), 201 );
	}

	// ── POST /orders/{id}/paid ───────────────────────────────────────────────

	/**
	 * @param object $request Petición REST.
	 * @return WP_REST_Response
	 */
	public function handle_paid( $request ) {
		if ( ! $this->is_enabled() ) {
			return self::not_found();
		}
		$body = self::json_body( $request );
		$ref  = isset( $body['payment_reference'] ) && is_string( $body['payment_reference'] ) ? $body['payment_reference'] : '';
		$amt  = $body['amount_cents'] ?? null;
		$cur  = isset( $body['currency'] ) && is_string( $body['currency'] ) ? strtoupper( $body['currency'] ) : '';
		$id   = (int) $request->get_param( 'id' );
		if ( 1 !== preg_match( '/^pi_[A-Za-z0-9_]{1,200}$/', $ref ) || ! is_int( $amt ) || $amt < 0 || 1 !== preg_match( '/^[A-Z]{3}$/', $cur ) || $id <= 0 ) {
			return self::error( 400, 'invalid_request', 'Invalid payment confirmation.' );
		}
		try {
			$order = $this->port->get_order( $id );
			if ( null === $order || '' === (string) ( $order['trusteed_order_id'] ?? '' ) ) {
				return self::error( 404, 'order_not_found', 'Order not found.' );
			}
			if ( ! empty( $order['is_paid'] ) ) {
				if ( (string) $order['transaction_id'] === $ref ) {
					return new WP_REST_Response( self::order_payload( $order ) + array( 'already_paid' => true ), 200 );
				}
				return self::error( 409, 'already_paid', 'The order is already paid with a different reference.' );
			}
			if ( ! in_array( (string) $order['status'], array( 'pending', 'on-hold', 'failed' ), true ) ) {
				return self::error( 409, 'order_not_payable', 'The order is not in a payable state.' );
			}
			if ( (int) $order['total_cents'] !== $amt || strtoupper( (string) $order['currency'] ) !== $cur ) {
				$note = sprintf(
					'Trusteed: payment %s does not match the order (paid %d %s, order %d %s). Review before fulfilling.',
					$ref,
					$amt,
					$cur,
					(int) $order['total_cents'],
					(string) $order['currency']
				);
				$this->port->hold_for_review( $id, $note );
				$this->log( 'warning', sprintf( 'payment_mismatch order=%d', $id ) );
				return self::error( 409, 'payment_mismatch', 'The paid amount does not match the order; it was put on hold for review.' );
			}
			$this->port->complete_payment( $id, $ref );
			$fresh = $this->port->get_order( $id ) ?? $order;
			return new WP_REST_Response( self::order_payload( $fresh ), 200 );
		} catch ( \Throwable $e ) {
			return $this->internal_error( 'paid', $e );
		}
	}

	// ── helpers ──────────────────────────────────────────────────────────────

	/**
	 * @param array<string,mixed> $order Pedido del puerto.
	 * @return array<string,mixed>
	 */
	private static function order_payload( array $order ): array {
		return array(
			'order_id'     => (int) $order['id'],
			'order_number' => (string) $order['number'],
			'total_cents'  => (int) $order['total_cents'],
			'currency'     => (string) $order['currency'],
			'status'       => (string) $order['status'],
		);
	}

	/**
	 * @param object $request Petición REST.
	 * @return array<string,mixed>
	 */
	private static function json_body( $request ): array {
		$decoded = json_decode( (string) $request->get_body(), true );
		return is_array( $decoded ) ? $decoded : array();
	}

	/**
	 * Valida y normaliza la entrada común a /quote y /orders. null ⇒ inválida.
	 *
	 * @param array<string,mixed> $body Cuerpo decodificado.
	 * @return array<string,mixed>|null
	 */
	private static function normalize_input( array $body ): ?array {
		$lines = $body['lines'] ?? null;
		if ( ! is_array( $lines ) || array() === $lines || count( $lines ) > 100 ) {
			return null;
		}
		$out_lines = array();
		foreach ( $lines as $line ) {
			if ( ! is_array( $line ) ) {
				return null;
			}
			$pid = $line['product_id'] ?? null;
			$vid = $line['variation_id'] ?? 0;
			$qty = $line['qty'] ?? null;
			if ( ! is_int( $pid ) || $pid <= 0 || ! is_int( $vid ) || $vid < 0 || ! is_int( $qty ) || $qty < 1 || $qty > 999 ) {
				return null;
			}
			$out_lines[] = array(
				'product_id'   => $pid,
				'variation_id' => $vid,
				'qty'          => $qty,
			);
		}
		$coupons = array();
		foreach ( (array) ( $body['coupons'] ?? array() ) as $code ) {
			if ( ! is_string( $code ) || '' === $code || strlen( $code ) > 64 ) {
				return null;
			}
			$coupons[] = sanitize_text_field( $code );
		}
		$method = $body['shipping_method_id'] ?? null;
		if ( null !== $method && ( ! is_string( $method ) || 1 !== preg_match( '/^[A-Za-z0-9_:\-.]{1,128}$/', $method ) ) ) {
			return null;
		}
		return array(
			'lines'              => $out_lines,
			'coupons'            => $coupons,
			'shipping_method_id' => $method,
			'shipping_address'   => self::normalize_address( $body['shipping_address'] ?? array() ),
			'billing_address'    => self::normalize_address( $body['billing_address'] ?? ( $body['shipping_address'] ?? array() ) ),
			'email'              => isset( $body['email'] ) && is_string( $body['email'] ) ? sanitize_text_field( $body['email'] ) : '',
		);
	}

	/**
	 * @param mixed $address Dirección cruda.
	 * @return array<string,string>
	 */
	private static function normalize_address( $address ): array {
		$out = array();
		if ( ! is_array( $address ) ) {
			return $out;
		}
		foreach ( array( 'first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country', 'phone', 'email' ) as $key ) {
			if ( isset( $address[ $key ] ) && is_string( $address[ $key ] ) ) {
				$out[ $key ] = sanitize_text_field( $address[ $key ] );
			}
		}
		return $out;
	}

	/**
	 * sha256 de la forma canónica (claves ordenadas, sin escapes) de lo devuelto.
	 *
	 * @param array<string,mixed> $quote Presupuesto sin `quote_hash`.
	 * @return string
	 */
	public static function quote_hash( array $quote ): string {
		unset( $quote['quote_hash'] );
		return hash( 'sha256', (string) json_encode( self::canonical( $quote ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
	}

	/**
	 * @param mixed $value Valor.
	 * @return mixed
	 */
	private static function canonical( $value ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}
		$is_list = array_keys( $value ) === range( 0, count( $value ) - 1 );
		if ( ! $is_list ) {
			ksort( $value );
		}
		foreach ( $value as $k => $v ) {
			$value[ $k ] = self::canonical( $v );
		}
		return $value;
	}

	private static function not_found(): WP_REST_Response {
		return self::error( 404, 'rest_no_route', 'No route was found matching the URL and request method.' );
	}

	private static function error( int $status, string $code, string $message ): WP_REST_Response {
		return new WP_REST_Response( array( 'error' => array( 'code' => $code, 'message' => $message ) ), $status );
	}

	private static function from_wp_error( WP_Error $e ): WP_REST_Response {
		$data   = $e->get_error_data();
		$status = is_array( $data ) && isset( $data['status'] ) ? (int) $data['status'] : 422;
		return self::error( $status, $e->get_error_code(), $e->get_error_message() );
	}

	private function internal_error( string $where, \Throwable $e ): WP_REST_Response {
		// Sólo la clase del error: el mensaje podría llevar datos de la petición.
		$this->log( 'error', sprintf( 'internal_error where=%s class=%s', $where, get_class( $e ) ) );
		return self::error( 500, 'internal_error', 'Internal error.' );
	}

	private function log( string $level, string $message ): void {
		if ( function_exists( 'wc_get_logger' ) ) {
			wc_get_logger()->log( $level, $message, array( 'source' => 'trusteed-agent-orders' ) );
		}
	}
}
