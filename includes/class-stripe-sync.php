<?php
/**
 * Sincroniza la cuenta Stripe del plugin de WooCommerce con la plataforma.
 *
 * Lee la clave secreta de `woocommerce_stripe_settings` y la entrega UNA vez al
 * endpoint embed de conexión (el mismo que usa el panel). La plataforma valida
 * la clave contra Stripe, aplica la guarda de modo (tienda demo => sólo test) y
 * la guarda en su vault; aquí no se conserva copia ni se devuelve a quien llama.
 *
 * @package Trusteed
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Trusteed_Stripe_Sync
 */
class Trusteed_Stripe_Sync {

	/** Opción que recuerda que el comerciante activó la sincronización. */
	public const OPTION_ENABLED = 'trusteed_stripe_sync_enabled';

	/** Opción del plugin Stripe de WooCommerce. */
	public const STRIPE_SETTINGS_OPTION = 'woocommerce_stripe_settings';

	private const CONNECT_PATH = '/v1/embed/merchant/payment-methods/stripe/connect';

	/**
	 * Mismo patrón que el conector de la plataforma. Acepta la clave restringida
	 * `rk_` (recomendada: sólo «PaymentIntents: Write», el resto en «None») además
	 * de la secreta completa `sk_`; el modo sigue saliendo del prefijo test/live.
	 */
	private const KEY_PATTERN = '/^(?:sk|rk)_(test|live)_[A-Za-z0-9]{24,200}$/';

	/** El Bearer del comerciante sólo puede viajar a hosts de Trusteed. */
	private const ALLOWED_API_HOSTS = array(
		'api.trusteed.xyz',
		'agenticmcpstores-production.up.railway.app',
		'staging-api.trusteed.xyz',
	);

	/** @var callable():(?string) */
	private $token_provider;

	/** @var string */
	private string $api_base;

	/** @var callable(string,array):array{status:int,body:string} */
	private $http_post;

	/**
	 * @param callable():(?string)                                   $token_provider Devuelve el token embed o null.
	 * @param string                                                 $api_base       Base de la API sin barra final.
	 * @param (callable(string,array):array{status:int,body:string})|null $http_post Inyectable para pruebas.
	 */
	public function __construct( callable $token_provider, string $api_base, ?callable $http_post = null ) {
		$this->token_provider = $token_provider;
		$this->api_base       = rtrim( $api_base, '/' );
		$this->http_post      = $http_post ?? array( self::class, 'default_post' );
	}

	/**
	 * Clave secreta y modo según el modo configurado en el plugin Stripe.
	 *
	 * @param array<string,mixed> $settings Contenido de `woocommerce_stripe_settings`.
	 * @return array{secret_key:string,mode:string}|null
	 */
	public static function read_credentials( array $settings ): ?array {
		$test = 'yes' === ( $settings['testmode'] ?? 'no' );
		$key  = $settings[ $test ? 'test_secret_key' : 'secret_key' ] ?? '';
		$mode = $test ? 'test' : 'live';
		if ( ! is_string( $key ) || 1 !== preg_match( self::KEY_PATTERN, $key, $m ) || $m[1] !== $mode ) {
			return null;
		}
		return array(
			'secret_key' => $key,
			'mode'       => $mode,
		);
	}

	/**
	 * Estado para el panel. Nunca incluye la clave.
	 *
	 * @param array<string,mixed> $settings Contenido de `woocommerce_stripe_settings`.
	 * @param bool                $enabled  Si el comerciante activó la sincronización.
	 * @return array{available:bool,enabled:bool,mode:?string,last4:?string}
	 */
	public static function describe( array $settings, bool $enabled ): array {
		$c = self::read_credentials( $settings );
		return array(
			'available' => null !== $c,
			'enabled'   => $enabled,
			'mode'      => $c['mode'] ?? null,
			'last4'     => null !== $c ? substr( $c['secret_key'], -4 ) : null,
		);
	}

	/**
	 * Entrega la clave a la plataforma.
	 *
	 * @param array<string,mixed> $settings Contenido de `woocommerce_stripe_settings`.
	 * @return array{ok:bool,code:string,mode?:string,reason?:string}
	 */
	public function sync( array $settings ): array {
		$creds = self::read_credentials( $settings );
		if ( null === $creds ) {
			return array(
				'ok'   => false,
				'code' => 'no_stripe_key',
			);
		}
		if ( ! $this->is_allowed_api_base() ) {
			return array(
				'ok'   => false,
				'code' => 'api_host_not_allowed',
			);
		}
		$token = call_user_func( $this->token_provider );
		if ( ! is_string( $token ) || '' === $token ) {
			return array(
				'ok'   => false,
				'code' => 'token_unavailable',
			);
		}
		$res = call_user_func(
			$this->http_post,
			$this->api_base . self::CONNECT_PATH,
			array(
				'timeout' => 30,
				'headers' => array(
					'Authorization'  => 'Bearer ' . $token,
					'X-Embed-Source' => 'wp-embed',
					'Content-Type'   => 'application/json',
				),
				'body'    => wp_json_encode(
					array(
						'flow'   => 'apiKey',
						'apiKey' => $creds['secret_key'],
						// Reemplaza la conexión existente: el comerciante pidió sincronizar.
						'force'  => true,
					)
				),
			)
		);
		if ( 200 === (int) $res['status'] ) {
			return array(
				'ok'   => true,
				'code' => 'connected',
				'mode' => $creds['mode'],
			);
		}
		$status = (int) $res['status'];
		$reason = 'http_' . $status;
		// Un 3xx no se interpreta: su cuerpo no es de la plataforma.
		if ( $status < 300 || $status >= 400 ) {
			$body = json_decode( (string) $res['body'], true );
			if ( is_array( $body ) && isset( $body['error'] ) && is_string( $body['error'] ) ) {
				$reason = $body['error'];
			}
		}
		return array(
			'ok'     => false,
			'code'   => 'platform_rejected',
			'reason' => $reason,
		);
	}

	/**
	 * @return bool
	 */
	private function is_allowed_api_base(): bool {
		$host   = (string) wp_parse_url( $this->api_base, PHP_URL_HOST );
		$scheme = (string) wp_parse_url( $this->api_base, PHP_URL_SCHEME );
		if ( 'https' === $scheme && in_array( $host, self::ALLOWED_API_HOSTS, true ) ) {
			return true;
		}
		// Sólo desarrollo local, como el token broker.
		return defined( 'WP_DEBUG' ) && WP_DEBUG && in_array( $host, array( 'localhost', '127.0.0.1', 'host.docker.internal' ), true );
	}

	/**
	 * @param string $url  URL.
	 * @param array  $args Argumentos de wp_remote_post.
	 * @return array{status:int,body:string}
	 */
	public static function default_post( string $url, array $args ): array {
		// Sin redirecciones: el POST lleva el Bearer del comerciante y un 3xx lo reenviaría.
		$args['redirection'] = 0;
		$r                   = wp_remote_post( $url, $args );
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
}
