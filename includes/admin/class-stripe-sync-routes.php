<?php
/**
 * Rutas REST del panel para sincronizar la cuenta Stripe de WooCommerce.
 *
 * GET  trusteed/v1/stripe-sync  → estado (sin clave).
 * POST trusteed/v1/stripe-sync  → activa la sincronización y la ejecuta.
 *
 * Mismo control de acceso que el token broker: `manage_woocommerce` + nonce.
 * Además, mientras la sincronización esté activa, un cambio en la configuración
 * del plugin Stripe vuelve a entregar la clave.
 *
 * @package Trusteed
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Trusteed_Stripe_Sync_Routes
 */
class Trusteed_Stripe_Sync_Routes {

	private const REST_NAMESPACE = 'trusteed/v1';
	private const REST_ROUTE     = '/stripe-sync';

	/** @var Trusteed_Token_Broker */
	private $broker;

	/** @var string */
	private string $api_base;

	/**
	 * @param Trusteed_Token_Broker $broker   Emisor de tokens embed.
	 * @param string                $api_base Base de la API.
	 */
	public function __construct( $broker, string $api_base ) {
		$this->broker   = $broker;
		$this->api_base = $api_base;
	}

	/**
	 * @return void
	 */
	public function init(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_action( 'update_option_' . Trusteed_Stripe_Sync::STRIPE_SETTINGS_OPTION, array( $this, 'on_stripe_settings_updated' ), 10, 2 );
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
			)
		);
	}

	/**
	 * @return array<string,mixed>
	 */
	private function stripe_settings(): array {
		$s = get_option( Trusteed_Stripe_Sync::STRIPE_SETTINGS_OPTION, array() );
		return is_array( $s ) ? $s : array();
	}

	/**
	 * @return Trusteed_Stripe_Sync
	 */
	private function sync_service(): Trusteed_Stripe_Sync {
		return new Trusteed_Stripe_Sync(
			function (): ?string {
				$r = $this->broker->request_token();
				if ( $r instanceof \WP_REST_Response ) {
					$d = $r->get_data();
					return is_array( $d ) && ! empty( $d['token'] ) ? (string) $d['token'] : null;
				}
				return null;
			},
			$this->api_base
		);
	}

	/**
	 * @param \WP_REST_Request $request Petición.
	 * @return \WP_REST_Response
	 */
	public function handle_status( \WP_REST_Request $request ) {
		return new \WP_REST_Response(
			Trusteed_Stripe_Sync::describe( $this->stripe_settings(), (bool) get_option( Trusteed_Stripe_Sync::OPTION_ENABLED, false ) ),
			200
		);
	}

	/**
	 * @param \WP_REST_Request $request Petición.
	 * @return \WP_REST_Response
	 */
	public function handle_sync( \WP_REST_Request $request ) {
		$result = $this->sync_service()->sync( $this->stripe_settings() );
		if ( $result['ok'] ) {
			update_option( Trusteed_Stripe_Sync::OPTION_ENABLED, true, false );
		}
		return new \WP_REST_Response( $result, $result['ok'] ? 200 : 422 );
	}

	/**
	 * Re-entrega la clave si el comerciante activó la sincronización y la
	 * clave cambió. Un fallo se registra sin la clave y no interrumpe el guardado.
	 *
	 * @param mixed $old_value Valor anterior.
	 * @param mixed $new_value Valor nuevo.
	 * @return void
	 */
	public function on_stripe_settings_updated( $old_value, $new_value ): void {
		if ( ! get_option( Trusteed_Stripe_Sync::OPTION_ENABLED, false ) || ! is_array( $new_value ) ) {
			return;
		}
		$old = is_array( $old_value ) ? Trusteed_Stripe_Sync::read_credentials( $old_value ) : null;
		$new = Trusteed_Stripe_Sync::read_credentials( $new_value );
		if ( null === $new || ( null !== $old && $old === $new ) ) {
			return;
		}
		$result = $this->sync_service()->sync( $new_value );
		if ( ! $result['ok'] ) {
			error_log( '[amcp] stripe_sync failed: ' . $result['code'] . ( isset( $result['reason'] ) ? ' (' . $result['reason'] . ')' : '' ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		}
	}
}
