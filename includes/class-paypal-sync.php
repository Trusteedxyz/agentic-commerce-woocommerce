<?php
/**
 * Sincroniza la cuenta PayPal de WooCommerce PayPal Payments (PPCP) con la plataforma.
 *
 * Lee `client_id`, `client_secret` y el modo de las opciones de PPCP y los entrega
 * UNA vez al endpoint embed de conexión (el mismo que usa el panel). La plataforma
 * mide el entorno real contra PayPal (el `mode` sólo ordena el sondeo), aplica la
 * guarda demo/real y guarda el secreto en su vault; aquí no se conserva copia ni
 * se devuelve a quien llama.
 *
 * Fuente de las opciones: plan 2026-10-12-paypal-cuenta-del-comercio.md §0.1.
 *
 * @package Trusteed
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Trusteed_PayPal_Sync
 */
class Trusteed_PayPal_Sync {

	/** Opción que recuerda que el comerciante activó la sincronización. */
	public const OPTION_ENABLED = 'trusteed_paypal_sync_enabled';

	/** Huella (sha256) de lo último entregado con éxito: evita reenvíos idénticos. */
	public const OPTION_HASH = 'trusteed_paypal_sync_hash';

	/** Instante (epoch) del último fallo automático: pausa los disparadores 300 s. */
	public const OPTION_RETRY_AT = 'trusteed_paypal_sync_retry_at';

	/** Segundos de pausa tras un fallo automático. */
	public const RETRY_COOLDOWN = 300;

	/** Opción vigente de PPCP (UI nueva, 3.0+; única desde 4.0.1). */
	public const PPCP_COMMON_OPTION = 'woocommerce-ppcp-data-common';

	/** Opción heredada de PPCP (<= 3.x). */
	public const PPCP_LEGACY_OPTION = 'woocommerce-ppcp-settings';

	private const CONNECT_PATH    = '/v1/embed/merchant/payment-methods/paypal/connect';
	private const DISCONNECT_PATH = '/v1/embed/merchant/payment-methods/paypal/disconnect';

	/**
	 * Cota local, no un formato: PayPal no publica uno y no se inventa.
	 * La plataforma es quien valida de verdad contra PayPal.
	 */
	private const CREDENTIAL_PATTERN = '/^[A-Za-z0-9_-]{20,200}$/';

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

	/** @var int */
	private int $timeout;

	/**
	 * @param callable():(?string)                                   $token_provider Devuelve el token embed o null.
	 * @param string                                                 $api_base       Base de la API sin barra final.
	 * @param (callable(string,array):array{status:int,body:string})|null $http_post Inyectable para pruebas.
	 * @param int                                                    $timeout        Timeout del POST en segundos (corto en disparadores automáticos).
	 */
	public function __construct( callable $token_provider, string $api_base, ?callable $http_post = null, int $timeout = 30 ) {
		$this->timeout        = max( 1, $timeout );
		$this->token_provider = $token_provider;
		$this->api_base       = rtrim( $api_base, '/' );
		$this->http_post      = $http_post ?? array( self::class, 'default_post' );
	}

	/**
	 * Credenciales y modo según PPCP.
	 *
	 * 1) Si `woocommerce-ppcp-data-common` EXISTE (PPCP >= 4, aunque esté vacía o
	 *    reseteada por una desconexión) es la fuente exclusiva: exige `client_id`
	 *    y `merchant_connected === true`; el modo sale de `sandbox_merchant`
	 *    (NUNCA de `use_sandbox`, que es estado de UI).
	 * 2) Sólo si NO existe (null, PPCP < 4) se usa la heredada
	 *    `woocommerce-ppcp-settings` con `sandbox_on`. PPCP 4.x nunca la borra y
	 *    su desconexión sólo vacía data-common: caer a ella reconectaría la cuenta antigua.
	 *
	 * @param array<string,mixed>|null $common Contenido de `woocommerce-ppcp-data-common`; null si la opción no existe.
	 * @param array<string,mixed> $legacy Contenido de `woocommerce-ppcp-settings`.
	 * @return array{client_id:string,client_secret:string,mode:string}|null
	 */
	public static function read_credentials( ?array $common, array $legacy ): ?array {
		if ( null !== $common ) {
			if ( true !== ( $common['merchant_connected'] ?? false ) ) {
				return null;
			}
			return self::build( $common['client_id'], $common['client_secret'] ?? '', self::truthy( $common['sandbox_merchant'] ?? false ) );
		}
		return self::build( $legacy['client_id'] ?? '', $legacy['client_secret'] ?? '', self::truthy( $legacy['sandbox_on'] ?? false ) );
	}

	/**
	 * @param mixed $client_id     Client ID.
	 * @param mixed $client_secret Secreto.
	 * @param bool  $sandbox       Si el entorno configurado es sandbox.
	 * @return array{client_id:string,client_secret:string,mode:string}|null
	 */
	private static function build( $client_id, $client_secret, bool $sandbox ): ?array {
		if (
			! is_string( $client_id ) || 1 !== preg_match( self::CREDENTIAL_PATTERN, $client_id )
			|| ! is_string( $client_secret ) || 1 !== preg_match( self::CREDENTIAL_PATTERN, $client_secret )
		) {
			return null;
		}
		return array(
			'client_id'     => $client_id,
			'client_secret' => $client_secret,
			'mode'          => $sandbox ? 'sandbox' : 'production',
		);
	}

	/**
	 * PPCP guarda booleanos; la heredada también cadenas `'1'`.
	 *
	 * @param mixed $v Valor.
	 * @return bool
	 */
	private static function truthy( $v ): bool {
		return true === $v || 1 === $v || '1' === $v;
	}

	/**
	 * Huella de lo que se entrega. Un hash no reversible: no contiene el secreto.
	 *
	 * @param array{client_id:string,client_secret:string,mode:string} $creds Credenciales.
	 * @return string
	 */
	public static function fingerprint( array $creds ): string {
		return hash( 'sha256', $creds['client_id'] . '|' . $creds['client_secret'] . '|' . $creds['mode'] );
	}

	/**
	 * Estado para el panel. Nunca incluye el secreto ni el client_id entero.
	 *
	 * @param array<string,mixed>|null $common  Contenido de `woocommerce-ppcp-data-common` (null si no existe).
	 * @param array<string,mixed> $legacy  Contenido de `woocommerce-ppcp-settings`.
	 * @param bool                $enabled Si el comerciante activó la sincronización.
	 * @return array{available:bool,enabled:bool,mode:?string,last4:?string}
	 */
	public static function describe( ?array $common, array $legacy, bool $enabled ): array {
		$c = self::read_credentials( $common, $legacy );
		return array(
			'available' => null !== $c,
			'enabled'   => $enabled,
			'mode'      => $c['mode'] ?? null,
			'last4'     => null !== $c ? substr( $c['client_id'], -4 ) : null,
		);
	}

	/**
	 * Entrega las credenciales a la plataforma.
	 *
	 * @param array<string,mixed>|null $common Contenido de `woocommerce-ppcp-data-common` (null si no existe).
	 * @param array<string,mixed> $legacy Contenido de `woocommerce-ppcp-settings`.
	 * @return array{ok:bool,code:string,mode?:string,reason?:string}
	 */
	public function sync( ?array $common, array $legacy ): array {
		$creds = self::read_credentials( $common, $legacy );
		if ( null === $creds ) {
			return array(
				'ok'   => false,
				'code' => 'no_paypal_credentials',
			);
		}
		$token = '';
		$gate  = $this->preflight( $token );
		if ( null !== $gate ) {
			unset( $creds );
			return $gate;
		}
		$mode = $creds['mode'];
		$res  = call_user_func(
			$this->http_post,
			$this->api_base . self::CONNECT_PATH,
			$this->request_args(
				$token,
				(string) wp_json_encode(
					array(
						'flow'      => 'apiKey',
						'accountId' => $creds['client_id'],
						'apiKey'    => $creds['client_secret'],
						'mode'      => $mode,
						// Reemplaza la conexión existente: el comerciante pidió sincronizar.
						'force'     => true,
					)
				)
			)
		);
		unset( $creds );
		if ( 200 === (int) $res['status'] ) {
			return array(
				'ok'   => true,
				'code' => 'connected',
				'mode' => $mode,
			);
		}
		return $this->rejected( $res );
	}

	/**
	 * Desconecta PayPal en la plataforma (D5: el comercio lo desconectó en PPCP).
	 *
	 * @return array{ok:bool,code:string,reason?:string}
	 */
	public function disconnect(): array {
		$token = '';
		$gate  = $this->preflight( $token );
		if ( null !== $gate ) {
			return $gate;
		}
		$res = call_user_func(
			$this->http_post,
			$this->api_base . self::DISCONNECT_PATH,
			$this->request_args( $token, '{}' )
		);
		if ( 200 === (int) $res['status'] ) {
			return array(
				'ok'   => true,
				'code' => 'disconnected',
			);
		}
		return $this->rejected( $res );
	}

	/**
	 * Comprobaciones previas a cualquier envío: host permitido y token disponible.
	 *
	 * @param string $token Salida: el token embed, una sola petición por envío.
	 * @return array{ok:false,code:string}|null Null si se puede enviar.
	 */
	private function preflight( string &$token ): ?array {
		if ( ! $this->is_allowed_api_base() ) {
			return array(
				'ok'   => false,
				'code' => 'api_host_not_allowed',
			);
		}
		$issued = call_user_func( $this->token_provider );
		if ( ! is_string( $issued ) || '' === $issued ) {
			return array(
				'ok'   => false,
				'code' => 'token_unavailable',
			);
		}
		$token = $issued;
		return null;
	}

	/**
	 * @param string $token Token embed.
	 * @param string $body  Cuerpo JSON.
	 * @return array<string,mixed>
	 */
	private function request_args( string $token, string $body ): array {
		return array(
			'timeout' => $this->timeout,
			'headers' => array(
				'Authorization'  => 'Bearer ' . $token,
				'X-Embed-Source' => 'wp-embed',
				'Content-Type'   => 'application/json',
			),
			'body'    => $body,
		);
	}

	/**
	 * Sólo el código `error` de la plataforma, nunca el cuerpo entero.
	 *
	 * @param array{status:int,body:string} $res Respuesta.
	 * @return array{ok:false,code:string,reason:string}
	 */
	private function rejected( array $res ): array {
		$status = (int) $res['status'];
		$reason = 'http_' . $status;
		// Un 3xx no se interpreta: su cuerpo no es de la plataforma. Para el resto,
		// sólo un código seguro (como PrestaShop) llega al log y al panel.
		if ( $status < 300 || $status >= 400 ) {
			$body = json_decode( (string) $res['body'], true );
			if ( is_array( $body ) && isset( $body['error'] ) && is_string( $body['error'] ) && 1 === preg_match( '/^[a-z0-9_]{1,64}$/', $body['error'] ) ) {
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
