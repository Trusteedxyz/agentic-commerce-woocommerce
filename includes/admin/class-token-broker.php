<?php
/**
 * Token broker — REST endpoint for the admin SPA to obtain an ephemeral embed token.
 *
 * @package AgenticMCPStores
 * @since   1.1.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Amcp_Token_Broker
 *
 * Registers a REST route that the Trusteed admin SPA calls to obtain a
 * short-lived token from the Trusteed API. The token is returned in the
 * response body; the SPA stores it only in JS memory (never in DOM or
 * localStorage). This complies with embed security constraint C-003.
 *
 * Authentication:
 *  (a) WordPress capability check (manage_woocommerce)
 *  (b) wp_rest nonce validation (X-WP-Nonce header)
 *
 * @since 1.1.0
 */
class Amcp_Token_Broker {

	/**
	 * REST namespace.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private const REST_NAMESPACE = 'agenticmcps/v1';

	/**
	 * REST route path.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private const REST_ROUTE = '/embed/token';

	/**
	 * WP option key that holds the S2S embed secret.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private const OPTION_EMBED_SECRET = 'amcp_embed_wp_secret';

	/**
	 * WP option key that holds the merchant identifier.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private const OPTION_MERCHANT_ID = 'amcp_merchant_id';

	/**
	 * Remote API endpoint path to issue an embed token.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private const API_ISSUE_PATH = '/v1/embed/wp/issue-token';

	/**
	 * wp_remote_post timeout in seconds.
	 *
	 * @since 1.1.0
	 * @var int
	 */
	private const REQUEST_TIMEOUT = 10;

	/**
	 * Trusteed API base URL (e.g. https://api.trusteed.xyz).
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private string $api_base_url;

	/**
	 * Plugin version string, used in User-Agent header.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private string $plugin_version;

	/**
	 * Allowed API hostnames — S039-SEC-006.
	 * The S2S secret is included in every token-issue request; only these hosts
	 * may receive it. Rejects http:// (no TLS) and arbitrary custom domains.
	 *
	 * @since 1.2.0
	 * @var string[]
	 */
	private const ALLOWED_API_HOSTS = array(
		'api.trusteed.xyz',
		'agenticmcpstores-production.up.railway.app',
		'staging-api.trusteed.xyz',
	);

	/**
	 * Constructor.
	 *
	 * @since 1.1.0
	 *
	 * @param string $api_base_url   Trusteed API base URL without trailing slash.
	 * @param string $plugin_version Plugin version string (e.g. '1.1.0').
	 */
	public function __construct( string $api_base_url, string $plugin_version ) {
		$this->api_base_url   = rtrim( $api_base_url, '/' );
		$this->plugin_version = $plugin_version;
	}

	/**
	 * S039-SEC-006: Validate the API base URL against the allowed-host list.
	 *
	 * Must use HTTPS and point to a Trusteed-owned hostname to prevent the
	 * embed S2S secret from being sent to an attacker-controlled server.
	 *
	 * Dev exception: when WP_DEBUG is true, private/loopback addresses over
	 * HTTP are also accepted (they cannot be reached by external attackers).
	 *
	 * @since 1.2.0
	 *
	 * @param string $url URL to validate.
	 * @return bool True if the host is in the allowlist and the scheme is https.
	 */
	private function is_allowed_api_url( string $url ): bool {
		$host   = (string) wp_parse_url( $url, PHP_URL_HOST );
		$scheme = (string) wp_parse_url( $url, PHP_URL_SCHEME );

		// Production: must be HTTPS + known Trusteed host.
		if ( 'https' === $scheme && is_string( $host ) && in_array( $host, self::ALLOWED_API_HOSTS, true ) ) {
			return true;
		}

		// Dev-only bypass: allow private/loopback addresses when WP_DEBUG is enabled.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			if ( $host === 'localhost' || $host === '127.0.0.1' ) {
				return true;
			}
			// Docker Desktop / Linux host-gateway hostname — common in local
			// dev when API runs on host and WP runs inside a container. The
			// container resolves `host.docker.internal` to the host gateway IP.
			if ( $host === 'host.docker.internal' ) {
				return true;
			}
			// Private IPv4 ranges: 10.x.x.x, 172.16–31.x.x, 192.168.x.x
			if ( preg_match( '/^(10\.|172\.(1[6-9]|2\d|3[01])\.|192\.168\.)/', $host ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Wire up WordPress hooks.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the REST route.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_ROUTE,
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'handle_token_request' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);
	}

	/**
	 * Permission callback: verifies capability + nonce.
	 *
	 * @since 1.1.0
	 *
	 * @param \WP_REST_Request $request Incoming REST request.
	 * @return true|\WP_Error True if allowed, WP_Error otherwise.
	 */
	public function check_permission( \WP_REST_Request $request ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'No tienes permisos suficientes.', 'agenticmcpstores' ),
				array( 'status' => 403 )
			);
		}

		$nonce = $request->get_header( 'X-WP-Nonce' );

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'Nonce inválido o ausente.', 'agenticmcpstores' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Handle the token issuance request.
	 *
	 * Reads merchant configuration from WP options, calls the Trusteed API,
	 * and returns the opaque token + expiry to the SPA.
	 *
	 * @since 1.1.0
	 *
	 * @param \WP_REST_Request $request Incoming REST request.
	 * @return \WP_REST_Response|\WP_Error REST response or error.
	 */
	public function handle_token_request( \WP_REST_Request $request ) {
		$merchant_id = get_option( self::OPTION_MERCHANT_ID, '' );

		if ( empty( $merchant_id ) ) {
			return new \WP_Error(
				'not_configured',
				__( 'Plugin no configurado: falta el merchant_id.', 'agenticmcpstores' ),
				array( 'status' => 503 )
			);
		}

		// S039-SEC-004: decrypt the stored secret before use.
		$stored_secret = (string) get_option( self::OPTION_EMBED_SECRET, '' );
		$embed_secret  = Amcp_Crypto_Helper::decrypt( $stored_secret );

		if ( empty( $embed_secret ) ) {
			return new \WP_Error(
				'not_configured',
				__( 'Plugin no configurado: falta el secret embed.', 'agenticmcpstores' ),
				array( 'status' => 503 )
			);
		}

		// S039-SEC-006: validate the API base URL against the allowlist before
		// sending the S2S secret. Misconfigured or tampered options could otherwise
		// exfiltrate the secret to an attacker-controlled host.
		if ( ! $this->is_allowed_api_url( $this->api_base_url ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions
			error_log( '[amcp] S039-SEC-006: rejected API base URL (not in allowlist): ' . $this->api_base_url );
			return new \WP_Error(
				'configuration_error',
				__( 'La URL de la API no está en la lista de hosts permitidos.', 'agenticmcpstores' ),
				array( 'status' => 503 )
			);
		}

		$wp_user_id = (string) get_current_user_id();
		$api_url    = $this->api_base_url . self::API_ISSUE_PATH;

		$response = wp_remote_post(
			$api_url,
			array(
				'timeout' => self::REQUEST_TIMEOUT,
				'headers' => array(
					'Content-Type'      => 'application/json',
					'X-Embed-Wp-Secret' => $embed_secret,
					'User-Agent'        => 'AgenticMCPStores-WP/' . $this->plugin_version,
				),
				'body'    => wp_json_encode(
					array(
						'merchant_id'            => $merchant_id,
						'wp_user_id'             => $wp_user_id,
						'capability_attestation' => 'manage_woocommerce',
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			error_log( '[amcp] Token broker API error: ' . $response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return new \WP_Error(
				'api_unavailable',
				__( 'No se pudo conectar con la API de Trusteed.', 'agenticmcpstores' ),
				array( 'status' => 503 )
			);
		}

		$status_code = (int) wp_remote_retrieve_response_code( $response );
		$raw_body    = wp_remote_retrieve_body( $response );
		$body        = json_decode( $raw_body, true );

		if ( 200 !== $status_code || ! is_array( $body ) || empty( $body['token'] ) ) {
			error_log( '[amcp] Token broker unexpected response: HTTP ' . $status_code ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return new \WP_Error(
				'token_issue_failed',
				__( 'Error al emitir el token de sesión.', 'agenticmcpstores' ),
				array( 'status' => 502 )
			);
		}

		// Return only the opaque token and its TTL — no sensitive data.
		// The SPA must store the token exclusively in JS memory (constraint C-003).
		// `expires_at` from the API is a Unix timestamp in seconds; convert to TTL (seconds)
		// so the SPA tokenManager can compute its in-memory expiry with Date.now() + expiresIn*1000.
		$expires_at_ts = isset( $body['expires_at'] ) ? (int) $body['expires_at'] : 0;
		$expires_in    = $expires_at_ts > 0 ? max( 1, $expires_at_ts - time() ) : 300;

		return new \WP_REST_Response(
			array(
				'token'     => $body['token'],
				'expiresIn' => $expires_in,
			),
			200
		);
	}
}
