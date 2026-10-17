<?php
/**
 * Rutas REST del panel y disparadores para sincronizar la cuenta PayPal de
 * WooCommerce PayPal Payments (PPCP).
 *
 * GET  trusteed/v1/paypal-sync  → estado (sin secreto).
 * POST trusteed/v1/paypal-sync  → activa la sincronización y la ejecuta.
 * DELETE trusteed/v1/paypal-sync → desactiva el opt-in (deja de sincronizar). NO
 *                                  desconecta PayPal en Trusteed: la cuenta ya
 *                                  conectada sigue conectada; se desconecta desde
 *                                  el panel de métodos de pago.
 *
 * Mismo control de acceso que el token broker: `manage_woocommerce` + nonce.
 * Con la sincronización activa, un cambio de credenciales o de modo en PPCP
 * vuelve a entregarlas (idempotente por huella) y desconectar PayPal en PPCP lo
 * desconecta también en Trusteed (D5).
 *
 * @package Trusteed
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Trusteed_PayPal_Sync_Routes
 */
class Trusteed_PayPal_Sync_Routes {

	private const REST_NAMESPACE = 'trusteed/v1';
	private const REST_ROUTE     = '/paypal-sync';

	/** Timeout (s) del POST lanzado desde hooks: no puede alargar el guardado del admin. */
	private const HOOK_TIMEOUT = 5;

	/** Hooks de PPCP (plan 2026-10-12 §0.1, leídos en su código fuente). */
	private const HOOK_AUTHENTICATED = 'woocommerce_paypal_payments_authenticated_merchant';
	private const HOOK_DISCONNECTED  = 'woocommerce_paypal_payments_merchant_disconnected';

	/** @var Trusteed_Token_Broker */
	private $broker;

	/** @var string */
	private string $api_base;

	/** @var (callable(string,array):array{status:int,body:string})|null */
	private $http_post;

	/**
	 * @param Trusteed_Token_Broker                                       $broker    Emisor de tokens embed.
	 * @param string                                                      $api_base  Base de la API.
	 * @param (callable(string,array):array{status:int,body:string})|null $http_post Inyectable para pruebas.
	 */
	public function __construct( $broker, string $api_base, ?callable $http_post = null ) {
		$this->broker    = $broker;
		$this->api_base  = $api_base;
		$this->http_post = $http_post;
	}

	/**
	 * @return void
	 */
	public function init(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_action( 'update_option_' . Trusteed_PayPal_Sync::PPCP_COMMON_OPTION, array( $this, 'on_common_updated' ), 10, 2 );
		add_action( 'update_option_' . Trusteed_PayPal_Sync::PPCP_LEGACY_OPTION, array( $this, 'on_legacy_updated' ), 10, 2 );
		add_action( self::HOOK_AUTHENTICATED, array( $this, 'on_authenticated' ) );
		add_action( self::HOOK_DISCONNECTED, array( $this, 'on_disconnected' ) );
	}

	/**
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_ROUTE,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'handle_status' ),
					'permission_callback' => array( $this->broker, 'check_permission' ),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'handle_sync' ),
					'permission_callback' => array( $this->broker, 'check_permission' ),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'handle_disable' ),
					'permission_callback' => array( $this->broker, 'check_permission' ),
				),
			)
		);
	}

	/**
	 * `woocommerce-ppcp-data-common`: null si la opción NO existe (PPCP < 4); un
	 * array vacío es una opción existente y reseteada (desconexión en PPCP 4.x).
	 *
	 * @return array<string,mixed>|null
	 */
	private function ppcp_common(): ?array {
		$s = get_option( Trusteed_PayPal_Sync::PPCP_COMMON_OPTION, false );
		return is_array( $s ) ? $s : null;
	}

	/**
	 * @param string $option Nombre de la opción de PPCP.
	 * @return array<string,mixed>
	 */
	private function ppcp_option( string $option ): array {
		$s = get_option( $option, array() );
		return is_array( $s ) ? $s : array();
	}

	/**
	 * @return Trusteed_PayPal_Sync
	 */
	private function sync_service( int $timeout = 30 ): Trusteed_PayPal_Sync {
		return new Trusteed_PayPal_Sync(
			function (): ?string {
				$r = $this->broker->request_token();
				if ( $r instanceof \WP_REST_Response ) {
					$d = $r->get_data();
					return is_array( $d ) && ! empty( $d['token'] ) ? (string) $d['token'] : null;
				}
				return null;
			},
			$this->api_base,
			$this->http_post,
			$timeout
		);
	}

	/**
	 * @param \WP_REST_Request $request Petición.
	 * @return \WP_REST_Response
	 */
	public function handle_status( \WP_REST_Request $request ) {
		return new \WP_REST_Response(
			Trusteed_PayPal_Sync::describe(
				$this->ppcp_common(),
				$this->ppcp_option( Trusteed_PayPal_Sync::PPCP_LEGACY_OPTION ),
				(bool) get_option( Trusteed_PayPal_Sync::OPTION_ENABLED, false )
			),
			200
		);
	}

	/**
	 * @param \WP_REST_Request $request Petición.
	 * @return \WP_REST_Response
	 */
	public function handle_sync( \WP_REST_Request $request ) {
		$common = $this->ppcp_common();
		$legacy = $this->ppcp_option( Trusteed_PayPal_Sync::PPCP_LEGACY_OPTION );
		$result = $this->sync_service()->sync( $common, $legacy );
		if ( $result['ok'] ) {
			update_option( Trusteed_PayPal_Sync::OPTION_ENABLED, true, false );
			delete_option( Trusteed_PayPal_Sync::OPTION_RETRY_AT );
			$creds = Trusteed_PayPal_Sync::read_credentials( $common, $legacy );
			if ( null !== $creds ) {
				update_option( Trusteed_PayPal_Sync::OPTION_HASH, Trusteed_PayPal_Sync::fingerprint( $creds ), false );
			}
			unset( $creds );
		}
		return new \WP_REST_Response( $result, $result['ok'] ? 200 : 422 );
	}

	/**
	 * Desactiva el opt-in: deja de sincronizar. NO desconecta PayPal en Trusteed.
	 *
	 * @param \WP_REST_Request $request Petición.
	 * @return \WP_REST_Response
	 */
	public function handle_disable( \WP_REST_Request $request ) {
		delete_option( Trusteed_PayPal_Sync::OPTION_ENABLED );
		delete_option( Trusteed_PayPal_Sync::OPTION_HASH );
		delete_option( Trusteed_PayPal_Sync::OPTION_RETRY_AT );
		return new \WP_REST_Response( array( 'enabled' => false ), 200 );
	}

	/**
	 * ¿Estamos en la pausa posterior a un fallo automático?
	 *
	 * @return bool
	 */
	private function in_cooldown(): bool {
		$at = (int) get_option( Trusteed_PayPal_Sync::OPTION_RETRY_AT, 0 );
		return $at > 0 && ( time() - $at ) < Trusteed_PayPal_Sync::RETRY_COOLDOWN;
	}

	/**
	 * @param mixed $old_value Valor anterior.
	 * @param mixed $new_value Valor nuevo de `woocommerce-ppcp-data-common`.
	 * @return void
	 */
	public function on_common_updated( $old_value, $new_value ): void {
		if ( ! is_array( $new_value ) ) {
			return;
		}
		$this->resync_if_changed( $new_value, $this->ppcp_option( Trusteed_PayPal_Sync::PPCP_LEGACY_OPTION ) );
	}

	/**
	 * @param mixed $old_value Valor anterior.
	 * @param mixed $new_value Valor nuevo de `woocommerce-ppcp-settings`.
	 * @return void
	 */
	public function on_legacy_updated( $old_value, $new_value ): void {
		if ( ! is_array( $new_value ) ) {
			return;
		}
		$this->resync_if_changed( $this->ppcp_common(), $new_value );
	}

	/**
	 * PPCP avisa (sin argumentos) tras guardar y quedar conectado.
	 *
	 * @return void
	 */
	public function on_authenticated(): void {
		$this->resync_if_changed(
			$this->ppcp_common(),
			$this->ppcp_option( Trusteed_PayPal_Sync::PPCP_LEGACY_OPTION )
		);
	}

	/**
	 * D5: el comercio desconectó PayPal en PPCP. Con el opt-in activo, también en
	 * Trusteed. La huella se borra para que reconectar las mismas credenciales
	 * vuelva a entregarlas. Un fallo se registra y deja la huella como estaba.
	 *
	 * @return void
	 */
	public function on_disconnected(): void {
		if ( ! get_option( Trusteed_PayPal_Sync::OPTION_ENABLED, false ) || $this->in_cooldown() ) {
			return;
		}
		$result = $this->sync_service( self::HOOK_TIMEOUT )->disconnect();
		if ( $result['ok'] ) {
			delete_option( Trusteed_PayPal_Sync::OPTION_HASH );
			delete_option( Trusteed_PayPal_Sync::OPTION_RETRY_AT );
			return;
		}
		update_option( Trusteed_PayPal_Sync::OPTION_RETRY_AT, time(), false );
		error_log( '[amcp] paypal_sync disconnect failed: ' . $result['code'] . ( isset( $result['reason'] ) ? ' (' . $result['reason'] . ')' : '' ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
	}

	/**
	 * Entrega las credenciales sólo si el opt-in está activo y la huella
	 * (credenciales + modo) cambió desde la última entrega con éxito.
	 *
	 * @param array<string,mixed>|null $common Contenido de `woocommerce-ppcp-data-common` (null si no existe).
	 * @param array<string,mixed> $legacy Contenido de `woocommerce-ppcp-settings`.
	 * @return void
	 */
	private function resync_if_changed( ?array $common, array $legacy ): void {
		if ( ! get_option( Trusteed_PayPal_Sync::OPTION_ENABLED, false ) || $this->in_cooldown() ) {
			return;
		}
		$creds = Trusteed_PayPal_Sync::read_credentials( $common, $legacy );
		if ( null === $creds ) {
			return;
		}
		$fingerprint = Trusteed_PayPal_Sync::fingerprint( $creds );
		unset( $creds );
		if ( hash_equals( (string) get_option( Trusteed_PayPal_Sync::OPTION_HASH, '' ), $fingerprint ) ) {
			return;
		}
		$result = $this->sync_service( self::HOOK_TIMEOUT )->sync( $common, $legacy );
		if ( $result['ok'] ) {
			update_option( Trusteed_PayPal_Sync::OPTION_HASH, $fingerprint, false );
			delete_option( Trusteed_PayPal_Sync::OPTION_RETRY_AT );
			return;
		}
		update_option( Trusteed_PayPal_Sync::OPTION_RETRY_AT, time(), false );
		error_log( '[amcp] paypal_sync failed: ' . $result['code'] . ( isset( $result['reason'] ) ? ' (' . $result['reason'] . ')' : '' ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
	}
}
