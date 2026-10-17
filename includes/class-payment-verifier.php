<?php
/**
 * Verifica el cobro con el PSP antes de dar un pedido por pagado (C-A3).
 *
 * `POST trusteed/v1/orders/{id}/paid` lo firma Trusteed con el HMAC de la
 * instalación; si Trusteed se compromete, esa firma bastaba para marcar pagado
 * (descontar stock, pasar a «procesando») un pedido que nadie cobró. Aquí el
 * plugin pregunta al PSP con las credenciales del PROPIO comercio: Stripe
 * (`PaymentIntent`) o PayPal (`capture`). Lo que no se pueda verificar no se
 * da por pagado.
 *
 * `reason` es un código cerrado:
 *   verified · psp_unreachable · psp_rejected · not_succeeded · amount_mismatch
 *   · currency_mismatch · reference_reused · no_credentials
 *
 * Transporte inyectable: `$http_get( string $url, array $args ): array{status:int,body:string}`
 * (`$args['method']` = GET por defecto; POST para el OAuth de PayPal). `status` 0 = sin
 * respuesta. Sin redirecciones: la petición lleva el Bearer del comercio.
 *
 * @package Trusteed
 * @since   2.4.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Trusteed_Payment_Verifier {

	private const STRIPE_BASE      = 'https://api.stripe.com';
	private const PAYPAL_LIVE_BASE = 'https://api-m.paypal.com';
	private const PAYPAL_TEST_BASE = 'https://api-m.sandbox.paypal.com';

	/** @var callable */
	private $http_get;

	/** @var callable */
	private $stripe_key_provider;

	/** @var callable */
	private $paypal_creds_provider;

	/** @var callable */
	private $reference_in_use;

	/**
	 * @param callable|null $http_get              Transporte (ver cabecera); null ⇒ `wp_remote_request`.
	 * @param callable      $stripe_key_provider   Devuelve la clave secreta de Stripe o ''.
	 * @param callable      $paypal_creds_provider Devuelve {client_id, client_secret, mode} o null.
	 * @param callable      $reference_in_use      fn( string $reference, int $order_id ): bool — otro pedido ya la usa.
	 */
	public function __construct( ?callable $http_get, callable $stripe_key_provider, callable $paypal_creds_provider, callable $reference_in_use ) {
		$this->http_get              = $http_get ?? array( self::class, 'default_request' );
		$this->stripe_key_provider   = $stripe_key_provider;
		$this->paypal_creds_provider = $paypal_creds_provider;
		$this->reference_in_use      = $reference_in_use;
	}

	/**
	 * @param string $method       '' (Stripe) o 'paypal'.
	 * @param string $reference    PaymentIntent `pi_…` o `captureId` de PayPal.
	 * @param int    $amount_cents Importe del pedido en céntimos.
	 * @param string $currency     Moneda ISO del pedido.
	 * @param int    $order_id     Pedido que se quiere dar por pagado.
	 * @return array{ok:bool,reason:string}
	 */
	public function verify( string $method, string $reference, int $amount_cents, string $currency, int $order_id ): array {
		// Defensa en profundidad: la referencia entra en una URL del PSP.
		if ( 1 !== preg_match( '/^[A-Za-z0-9_]{1,200}$/', $reference ) || ! in_array( $method, array( '', 'paypal' ), true ) ) {
			return self::result( 'psp_rejected' );
		}
		try {
			if ( (bool) call_user_func( $this->reference_in_use, $reference, $order_id ) ) {
				return self::result( 'reference_reused' );
			}
			return 'paypal' === $method
				? $this->verify_paypal( $reference, $amount_cents, $currency )
				: $this->verify_stripe( $reference, $amount_cents, $currency );
		} catch ( \Throwable $e ) {
			// Una avería nuestra no es un permiso. Sólo la clase: el mensaje podría llevar datos del pago.
			if ( function_exists( 'wc_get_logger' ) ) {
				wc_get_logger()->log( 'error', 'payment_verifier_exception class=' . get_class( $e ), array( 'source' => 'trusteed-agent-orders' ) );
			}
			return self::result( 'psp_unreachable' );
		}
	}

	// ── Stripe ───────────────────────────────────────────────────────────────

	private function verify_stripe( string $reference, int $amount_cents, string $currency ): array {
		$key = (string) call_user_func( $this->stripe_key_provider );
		if ( '' === $key ) {
			return self::result( 'no_credentials' );
		}
		$res = $this->request(
			self::STRIPE_BASE . '/v1/payment_intents/' . $reference,
			array( 'headers' => array( 'Authorization' => 'Bearer ' . $key ) )
		);
		$bad = self::transport_failure( $res );
		if ( null !== $bad ) {
			return self::result( $bad );
		}
		$pi = json_decode( $res['body'], true );
		if ( ! is_array( $pi ) ) {
			return self::result( 'psp_unreachable' );
		}
		if ( 'succeeded' !== ( $pi['status'] ?? null ) ) {
			return self::result( 'not_succeeded' );
		}
		if ( ! is_int( $pi['amount_received'] ?? null ) || $pi['amount_received'] !== $amount_cents ) {
			return self::result( 'amount_mismatch' );
		}
		if ( ! is_string( $pi['currency'] ?? null ) || strtolower( $pi['currency'] ) !== strtolower( $currency ) ) {
			return self::result( 'currency_mismatch' );
		}
		return self::result( 'verified' );
	}

	// ── PayPal ───────────────────────────────────────────────────────────────

	private function verify_paypal( string $reference, int $amount_cents, string $currency ): array {
		$creds = call_user_func( $this->paypal_creds_provider );
		if ( ! is_array( $creds ) || empty( $creds['client_id'] ) || empty( $creds['client_secret'] ) ) {
			return self::result( 'no_credentials' );
		}
		$base  = 'sandbox' === ( $creds['mode'] ?? '' ) ? self::PAYPAL_TEST_BASE : self::PAYPAL_LIVE_BASE;
		$token = $this->request(
			$base . '/v1/oauth2/token',
			array(
				'method'  => 'POST',
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( $creds['client_id'] . ':' . $creds['client_secret'] ),
					'Content-Type'  => 'application/x-www-form-urlencoded',
				),
				'body'    => 'grant_type=client_credentials',
			)
		);
		$bad = self::transport_failure( $token );
		if ( null !== $bad ) {
			return self::result( $bad );
		}
		$decoded = json_decode( $token['body'], true );
		$bearer  = is_array( $decoded ) && is_string( $decoded['access_token'] ?? null ) ? $decoded['access_token'] : '';
		if ( '' === $bearer ) {
			return self::result( 'psp_unreachable' );
		}
		$res = $this->request(
			$base . '/v2/payments/captures/' . $reference,
			array( 'headers' => array( 'Authorization' => 'Bearer ' . $bearer ) )
		);
		$bad = self::transport_failure( $res );
		if ( null !== $bad ) {
			return self::result( $bad );
		}
		$cap = json_decode( $res['body'], true );
		if ( ! is_array( $cap ) ) {
			return self::result( 'psp_unreachable' );
		}
		if ( 'COMPLETED' !== ( $cap['status'] ?? null ) ) {
			return self::result( 'not_succeeded' );
		}
		$amount = $cap['amount'] ?? null;
		$paid   = is_array( $amount ) ? self::decimal_to_cents( $amount['value'] ?? null ) : null;
		if ( null === $paid || $paid !== $amount_cents ) {
			return self::result( 'amount_mismatch' );
		}
		if ( ! is_string( $amount['currency_code'] ?? null ) || strtoupper( $amount['currency_code'] ) !== strtoupper( $currency ) ) {
			return self::result( 'currency_mismatch' );
		}
		return self::result( 'verified' );
	}

	/**
	 * '49.99' ⇒ 4999 sin pasar por float. Más de dos decimales o formato raro ⇒ null.
	 *
	 * @param mixed $value Importe decimal de PayPal.
	 */
	private static function decimal_to_cents( $value ): ?int {
		if ( ! is_string( $value ) || 1 !== preg_match( '/^(\d{1,12})(?:\.(\d{1,2}))?$/', $value, $m ) ) {
			return null;
		}
		return ( (int) $m[1] * 100 ) + (int) str_pad( $m[2] ?? '', 2, '0' );
	}

	// ── transporte ───────────────────────────────────────────────────────────

	/**
	 * @param array{status:int,body:string} $res Respuesta.
	 * @return string|null Código si la respuesta no permite seguir; null si es 2xx.
	 */
	private static function transport_failure( array $res ): ?string {
		$status = (int) $res['status'];
		if ( $status >= 200 && $status < 300 ) {
			return null;
		}
		// 0 (sin respuesta), 429 y 5xx: el PSP no contestó; reintentable.
		if ( 0 === $status || 429 === $status || $status >= 500 ) {
			return 'psp_unreachable';
		}
		// 3xx (no se siguen) y 4xx: el PSP no reconoce la credencial o la referencia.
		return 'psp_rejected';
	}

	/**
	 * @param string              $url  URL del PSP.
	 * @param array<string,mixed> $args Argumentos de la petición.
	 * @return array{status:int,body:string}
	 */
	private function request( string $url, array $args ): array {
		$res = call_user_func( $this->http_get, $url, $args );
		return array(
			'status' => is_array( $res ) ? (int) ( $res['status'] ?? 0 ) : 0,
			'body'   => is_array( $res ) ? (string) ( $res['body'] ?? '' ) : '',
		);
	}

	/**
	 * @param string              $url  URL.
	 * @param array<string,mixed> $args Argumentos (method, headers, body).
	 * @return array{status:int,body:string}
	 */
	public static function default_request( string $url, array $args ): array {
		$args['timeout']     = 10;
		$args['redirection'] = 0;
		$r                   = wp_remote_request( $url, $args );
		if ( is_wp_error( $r ) ) {
			return array(
				'status' => 0,
				'body'   => '',
			);
		}
		return array(
			'status' => (int) wp_remote_retrieve_response_code( $r ),
			'body'   => (string) wp_remote_retrieve_body( $r ),
		);
	}

	/**
	 * @return array{ok:bool,reason:string}
	 */
	private static function result( string $reason ): array {
		return array(
			'ok'     => 'verified' === $reason,
			'reason' => $reason,
		);
	}
}
