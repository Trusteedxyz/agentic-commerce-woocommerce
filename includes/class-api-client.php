<?php
/**
 * API client for communicating with the Trusteed backend.
 *
 * @package Trusteed
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Trusteed_Api_Client
 *
 * Handles all HTTP requests to the Trusteed API.
 * Uses the WordPress HTTP API (wp_remote_request) exclusively.
 * Implements automatic retry on 429 (Too Many Requests) and 503 (Service Unavailable).
 *
 * @since 1.0.0
 */
class Trusteed_Api_Client {

	/**
	 * API key for authentication.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $api_key;

	/**
	 * Base URL for the Trusteed API.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $api_base;

	/**
	 * HTTP request timeout in seconds.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $timeout;

	/**
	 * Maximum number of retry attempts on transient errors.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private const MAX_RETRIES = 3;

	/**
	 * Delay in seconds between retry attempts.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private const RETRY_DELAY_SECONDS = 2;

	/**
	 * HTTP status codes that trigger automatic retry.
	 *
	 * @since 1.0.0
	 * @var array<int>
	 */
	private const RETRYABLE_STATUS_CODES = array( 429, 503 );

	/**
	 * Plugin version sent as User-Agent.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const USER_AGENT_PREFIX = 'Trusteed-WP/';

	/**
	 * Allowlist of Trusteed-owned API hosts.
	 *
	 * Conceptual SSOT: Trusteed_Token_Broker::ALLOWED_API_HOSTS
	 * (includes/admin/class-token-broker.php). Kept in sync here so the
	 * credentialed request path (X-AgenticMCP-Key / account password) never
	 * targets an attacker-controlled host via a manipulated `amcp_api_base_url`.
	 *
	 * @since 2.0.0
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
	 * @since 1.0.0
	 *
	 * @param string $api_key API key for authentication. Falls back to stored option.
	 * @param string $api_base Base URL for the API. Falls back to TRUSTEED_API_BASE constant.
	 * @param int    $timeout HTTP request timeout in seconds. Default 15.
	 */
	public function __construct( $api_key = '', $api_base = '', $timeout = 15 ) {
		$this->api_key = ! empty( $api_key )
			? sanitize_text_field( $api_key )
			: self::get_stored_api_key();

		$this->api_base = ! empty( $api_base )
			? esc_url_raw( untrailingslashit( $api_base ) )
			: esc_url_raw( untrailingslashit( (string) get_option( 'amcp_api_base_url', TRUSTEED_API_BASE ) ) );

		$this->timeout = absint( $timeout );
	}

	/**
	 * Read and decrypt the stored Trusteed API key.
	 *
	 * Single choke-point for reading the `agenticmcp_api_key` option. Values
	 * written by store_api_key() are encrypted at rest via Amcp_Crypto_Helper;
	 * legacy plaintext values are returned unchanged (transparent decrypt).
	 *
	 * @since 2.0.0
	 *
	 * @return string Decrypted API key, or empty string when unset.
	 */
	public static function get_stored_api_key(): string {
		return Amcp_Crypto_Helper::decrypt( (string) get_option( 'agenticmcp_api_key', '' ) );
	}

	/**
	 * Encrypt and persist the Trusteed API key at rest.
	 *
	 * Single choke-point for writing the `agenticmcp_api_key` option. When the
	 * TRUSTEED_EMBED_SECRET_KEY constant is configured the value is stored as
	 * `amcp_enc:` ciphertext; otherwise it degrades to plaintext (identical to
	 * the embed secret) until the key is provisioned.
	 *
	 * @since 2.0.0
	 *
	 * @param string $plaintext Raw API key.
	 * @return void
	 */
	public static function store_api_key( string $plaintext ): void {
		update_option(
			'agenticmcp_api_key',
			Amcp_Crypto_Helper::encrypt( sanitize_text_field( $plaintext ) )
		);
	}

	/**
	 * Determine whether the parsed host is a local/dev host.
	 *
	 * Loopback and private addresses cannot be reached by external attackers,
	 * so the dev/staging override (`amcp_api_base_url`) keeps working against a
	 * local API even over plain HTTP.
	 *
	 * @since 2.0.0
	 *
	 * @param string $host Host extracted from the API base URL.
	 * @return bool True when the host is local/dev.
	 */
	private function is_local_host( string $host ): bool {
		$local_hosts = array( 'localhost', '127.0.0.1', '::1', 'host.docker.internal' );

		if ( in_array( $host, $local_hosts, true ) ) {
			return true;
		}

		// Private IPv4 ranges: 10.x.x.x, 172.16–31.x.x, 192.168.x.x.
		if ( preg_match( '/^(10\.|172\.(1[6-9]|2\d|3[01])\.|192\.168\.)/', $host ) ) {
			return true;
		}

		return ( function_exists( 'wp_get_environment_type' ) && 'local' === wp_get_environment_type() );
	}

	/**
	 * Guard against sending credentials to an untrusted or insecure endpoint.
	 *
	 * Production credentials (account password, API key) must only travel over
	 * HTTPS *and* to a Trusteed-owned host. A manipulated `amcp_api_base_url`
	 * pointing at an attacker host over HTTPS would otherwise leak the embed
	 * S2S secret / API key. Plain HTTP and arbitrary hosts are tolerated solely
	 * for local development hosts so the dev/staging override keeps working.
	 *
	 * Host matching is exact (no suffix matching), so `trusteed.xyz.evil.com`
	 * is rejected because it is not a member of ALLOWED_API_HOSTS.
	 *
	 * @since 2.0.0
	 *
	 * @return true|WP_Error True when the base is secure + allowlisted (or local),
	 *                       WP_Error otherwise.
	 */
	private function require_secure_base() {
		$host     = (string) wp_parse_url( $this->api_base, PHP_URL_HOST );
		$is_https = ( 0 === stripos( (string) $this->api_base, 'https://' ) );

		// Production path: HTTPS + exact-match Trusteed-owned host.
		if ( $is_https && '' !== $host && in_array( $host, self::ALLOWED_API_HOSTS, true ) ) {
			return true;
		}

		// Dev/local path: loopback or private host (HTTP tolerated).
		if ( '' !== $host && $this->is_local_host( $host ) ) {
			return true;
		}

		if ( ! $is_https ) {
			return new WP_Error(
				'agenticmcp_insecure_api_base',
				__( 'Refusing to send credentials over an insecure (non-HTTPS) connection. Configure an HTTPS API URL.', 'trusteed-for-woocommerce' )
			);
		}

		return new WP_Error(
			'agenticmcp_untrusted_api_base',
			__( 'Refusing to send credentials to an untrusted API host. Configure a Trusteed API URL.', 'trusteed-for-woocommerce' )
		);
	}

	/**
	 * Perform an HTTP request to the Trusteed API.
	 *
	 * Automatically retries on 429 and 503 responses up to MAX_RETRIES times
	 * with RETRY_DELAY_SECONDS between each attempt.
	 *
	 * @since 1.0.0
	 *
	 * @param string     $method   HTTP method (GET, POST, PUT, DELETE).
	 * @param string     $endpoint API endpoint path (e.g. '/api/v1/health').
	 * @param array|null $body     Request body. Will be JSON-encoded for non-GET requests.
	 * @return array|WP_Error Array with 'status' (int), 'body' (mixed), 'headers' (array) on success.
	 *                        WP_Error on failure with descriptive error code.
	 */
	public function request( $method, $endpoint, $body = null ) {
		// Fail-closed: every request carries the X-AgenticMCP-Key credential,
		// so the base host must be HTTPS + Trusteed-owned (or a local dev host).
		$secure = $this->require_secure_base();
		if ( is_wp_error( $secure ) ) {
			return $secure;
		}

		$url = $this->api_base . '/' . ltrim( sanitize_text_field( $endpoint ), '/' );

		$args = array(
			'method'  => strtoupper( sanitize_text_field( $method ) ),
			'timeout' => $this->timeout,
			'headers' => array(
				'Content-Type'     => 'application/json',
				'Accept'           => 'application/json',
				'X-AgenticMCP-Key' => $this->api_key,
				'User-Agent'       => self::USER_AGENT_PREFIX . $this->get_plugin_version(),
			),
		);

		if ( null !== $body && 'GET' !== $args['method'] ) {
			$args['body'] = wp_json_encode( $body );
		}

		$last_response = null;

		for ( $attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++ ) {
			$response = wp_remote_request( $url, $args );

			// WordPress-level transport error (DNS, timeout, SSL, etc.).
			if ( is_wp_error( $response ) ) {
				$wp_code = $response->get_error_code();

				if ( 'http_request_failed' === $wp_code && strpos( $response->get_error_message(), 'timed out' ) !== false ) {
					return new WP_Error(
						'agenticmcp_api_timeout',
						sprintf(
							/* translators: 1: endpoint path, 2: timeout in seconds */
							__( 'Request to %1$s timed out after %2$d seconds.', 'trusteed-for-woocommerce' ),
							$endpoint,
							$this->timeout
						)
					);
				}

				return new WP_Error(
					'agenticmcp_api_transport_error',
					sprintf(
						/* translators: 1: endpoint path, 2: error message */
						__( 'Transport error requesting %1$s: %2$s', 'trusteed-for-woocommerce' ),
						$endpoint,
						$response->get_error_message()
					)
				);
			}

			$status_code = wp_remote_retrieve_response_code( $response );

			// Retry on transient server errors (429, 503).
			if ( in_array( $status_code, self::RETRYABLE_STATUS_CODES, true ) && $attempt < self::MAX_RETRIES ) {
				$last_response = $response;
				sleep( self::RETRY_DELAY_SECONDS );
				continue;
			}

			return $this->parse_response( $response, $endpoint );
		}

		// All retries exhausted — return the last failed response.
		if ( null !== $last_response ) {
			$status_code = wp_remote_retrieve_response_code( $last_response );

			return new WP_Error(
				'agenticmcp_api_retries_exhausted',
				sprintf(
					/* translators: 1: endpoint path, 2: HTTP status code, 3: max retries */
					__( 'Request to %1$s failed with status %2$d after %3$d attempts.', 'trusteed-for-woocommerce' ),
					$endpoint,
					$status_code,
					self::MAX_RETRIES
				)
			);
		}

		return new WP_Error(
			'agenticmcp_api_error',
			__( 'Unexpected error during API request.', 'trusteed-for-woocommerce' )
		);
	}

	/**
	 * Perform a GET request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $endpoint API endpoint path.
	 * @return array|WP_Error Parsed response or WP_Error.
	 */
	public function get( $endpoint ) {
		return $this->request( 'GET', $endpoint );
	}

	/**
	 * Perform a POST request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $endpoint API endpoint path.
	 * @param array  $body     Request body as associative array.
	 * @return array|WP_Error Parsed response or WP_Error.
	 */
	public function post( $endpoint, $body ) {
		return $this->request( 'POST', $endpoint, $body );
	}

	/**
	 * Test the connection to the Trusteed API.
	 *
	 * Calls GET /api/v1/health and measures round-trip latency.
	 *
	 * @since 1.0.0
	 *
	 * @return array|WP_Error Array with 'connected' (bool) and 'latency_ms' (float) on success.
	 *                        WP_Error if the request fails at the transport level.
	 */
	public function test_connection() {
		$start = microtime( true );

		$response = $this->get( '/api/v1/health' );

		$latency_ms = round( ( microtime( true ) - $start ) * 1000, 2 );

		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				$response->get_error_code(),
				$response->get_error_message(),
				array( 'latency_ms' => $latency_ms )
			);
		}

		$is_connected = (
			200 === $response['status']
			&& isset( $response['body']['status'] )
			&& 'ok' === $response['body']['status']
		);

		if ( ! $is_connected ) {
			return new WP_Error(
				'agenticmcp_api_unhealthy',
				sprintf(
					/* translators: 1: HTTP status code */
					__( 'API health check returned status %1$d. Expected 200 with status "ok".', 'trusteed-for-woocommerce' ),
					$response['status']
				),
				array(
					'latency_ms' => $latency_ms,
					'status'     => $response['status'],
					'body'       => $response['body'],
				)
			);
		}

		return array(
			'connected'  => true,
			'latency_ms' => $latency_ms,
		);
	}

	/**
	 * One-shot onboarding from the WP admin panel.
	 *
	 * Calls POST /api/v1/plugin/onboard with email + password.
	 * No API key required — the backend creates/authenticates the account
	 * and returns all credentials in a single response.
	 *
	 * @since 1.2.0
	 *
	 * @param string $email      Merchant email address.
	 * @param string $password   Account password (min 8 chars).
	 * @param string $site_url   WordPress site URL.
	 * @param string $wc_version WooCommerce version string.
	 * @param string $store_name Optional display name for the store.
	 * @return array|WP_Error Array with 'merchant_id', 'api_key', 'embed_wp_secret',
	 *                        'store_slug', 'mcp_endpoint', 'tier', 'already_connected'.
	 *                        WP_Error on failure.
	 */
	public function onboard( $email, $password, $site_url, $wc_version, $store_name = '' ) {
		$secure = $this->require_secure_base();
		if ( is_wp_error( $secure ) ) {
			return $secure;
		}

		$body = array(
			'email'      => sanitize_email( $email ),
			'password'   => $password,
			'site_url'   => esc_url_raw( $site_url ),
			'wc_version' => sanitize_text_field( $wc_version ),
		);

		if ( ! empty( $store_name ) ) {
			$body['store_name'] = sanitize_text_field( $store_name );
		}

		// Onboard endpoint uses email+password auth — no X-AgenticMCP-Key needed.
		$url  = $this->api_base . '/api/v1/plugin/onboard';
		$args = array(
			'method'  => 'POST',
			'timeout' => 30,
			'headers' => array(
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
				'User-Agent'   => self::USER_AGENT_PREFIX . $this->get_plugin_version(),
			),
			'body'    => wp_json_encode( $body ),
		);

		$raw = wp_remote_request( $url, $args );

		if ( is_wp_error( $raw ) ) {
			return new WP_Error(
				'agenticmcp_onboard_transport',
				$raw->get_error_message()
			);
		}

		$parsed = $this->parse_response( $raw, '/api/v1/plugin/onboard' );

		if ( is_wp_error( $parsed ) ) {
			return $parsed;
		}

		if ( $parsed['status'] === 401 ) {
			return new WP_Error(
				'agenticmcp_onboard_invalid_credentials',
				__( 'Correo o contraseña incorrectos.', 'trusteed-for-woocommerce' )
			);
		}

		if ( $parsed['status'] < 200 || $parsed['status'] >= 300 ) {
			$msg = isset( $parsed['body']['error'] )
				? sanitize_text_field( $parsed['body']['error'] )
				: __( 'Error al conectar con Trusteed.', 'trusteed-for-woocommerce' );
			return new WP_Error( 'agenticmcp_onboard_failed', $msg );
		}

		$d = isset( $parsed['body']['data'] ) ? $parsed['body']['data'] : array();

		return array(
			'merchant_id'       => isset( $d['merchant_id'] ) ? sanitize_text_field( $d['merchant_id'] ) : '',
			'api_key'           => isset( $d['api_key'] ) ? sanitize_text_field( $d['api_key'] ) : '',
			'embed_wp_secret'   => isset( $d['embed_wp_secret'] ) ? sanitize_text_field( $d['embed_wp_secret'] ) : '',
			'store_slug'        => isset( $d['store_slug'] ) ? sanitize_text_field( $d['store_slug'] ) : '',
			'mcp_endpoint'      => isset( $d['mcp_endpoint'] ) ? esc_url_raw( $d['mcp_endpoint'] ) : '',
			'tier'              => isset( $d['tier'] ) ? sanitize_text_field( $d['tier'] ) : 'FREE',
			'already_connected' => ! empty( $d['already_connected'] ),
		);
	}

	/**
	 * Register the WooCommerce store with the Trusteed backend.
	 *
	 * Sends store metadata to POST /api/v1/plugin/register and returns
	 * the assigned store slug, MCP endpoint URL, and subscription tier.
	 *
	 * @since 1.0.0
	 *
	 * @param string $site_url   The WordPress site URL.
	 * @param string $wc_version The WooCommerce version string.
	 * @return array|WP_Error Array with 'merchant_id', 'store_slug', 'mcp_endpoint', 'tier' on success.
	 *                        WP_Error if the request fails or returns a non-2xx status.
	 */
	public function register_store( $site_url, $wc_version ) {
		$secure = $this->require_secure_base();
		if ( is_wp_error( $secure ) ) {
			return $secure;
		}

		$body = array(
			'api_key'    => $this->api_key,
			'site_url'   => esc_url_raw( $site_url ),
			'platform'   => 'WOOCOMMERCE',
			'wc_version' => sanitize_text_field( $wc_version ),
		);

		$response = $this->post( '/api/v1/plugin/register', $body );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( $response['status'] < 200 || $response['status'] >= 300 ) {
			$error_message = isset( $response['body']['error'] )
				? sanitize_text_field( $response['body']['error'] )
				: __( 'Unknown error during store registration.', 'trusteed-for-woocommerce' );

			return new WP_Error(
				'agenticmcp_registration_failed',
				$error_message,
				array(
					'status' => $response['status'],
					'body'   => $response['body'],
				)
			);
		}

		$data = $response['body'];

		if ( ! isset( $data['data'] ) ) {
			return new WP_Error(
				'agenticmcp_registration_invalid_response',
				__( 'Registration response missing expected data field.', 'trusteed-for-woocommerce' )
			);
		}

		$store_data = $data['data'];

		return array(
			'merchant_id'  => isset( $store_data['merchant_id'] ) ? sanitize_text_field( $store_data['merchant_id'] ) : '',
			'store_slug'   => isset( $store_data['store_slug'] ) ? sanitize_text_field( $store_data['store_slug'] ) : '',
			'mcp_endpoint' => isset( $store_data['mcp_endpoint'] ) ? esc_url_raw( $store_data['mcp_endpoint'] ) : '',
			'tier'         => isset( $store_data['tier'] ) ? sanitize_text_field( $store_data['tier'] ) : '',
		);
	}

	/**
	 * Parse a WordPress HTTP API response into a standardized array.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $response WP HTTP API response array.
	 * @param string $endpoint The requested endpoint (for error messages).
	 * @return array|WP_Error Array with 'status', 'body', 'headers' or WP_Error on JSON parse failure.
	 */
	private function parse_response( $response, $endpoint ) {
		$status_code   = wp_remote_retrieve_response_code( $response );
		$raw_body      = wp_remote_retrieve_body( $response );
		$headers       = wp_remote_retrieve_headers( $response );
		$headers_array = ( $headers instanceof \Requests_Utility_CaseInsensitiveDictionary || $headers instanceof \WpOrg\Requests\Utility\CaseInsensitiveDictionary )
			? $headers->getAll()
			: (array) $headers;

		$decoded_body = json_decode( $raw_body, true );

		if ( ! empty( $raw_body ) && null === $decoded_body && JSON_ERROR_NONE !== json_last_error() ) {
			return new WP_Error(
				'agenticmcp_api_json_error',
				sprintf(
					/* translators: 1: endpoint path, 2: JSON error message */
					__( 'Invalid JSON response from %1$s: %2$s', 'trusteed-for-woocommerce' ),
					$endpoint,
					json_last_error_msg()
				),
				array(
					'status'   => $status_code,
					'raw_body' => substr( $raw_body, 0, 500 ),
				)
			);
		}

		if ( $status_code >= 400 ) {
			$error_message = '';
			if ( is_array( $decoded_body ) && isset( $decoded_body['error'] ) ) {
				$error_message = sanitize_text_field( $decoded_body['error'] );
			} elseif ( is_array( $decoded_body ) && isset( $decoded_body['message'] ) ) {
				$error_message = sanitize_text_field( $decoded_body['message'] );
			}

			if ( ! empty( $error_message ) ) {
				return new WP_Error(
					'agenticmcp_api_error',
					sprintf(
						/* translators: 1: HTTP status code, 2: error message */
						__( 'API error %1$d: %2$s', 'trusteed-for-woocommerce' ),
						$status_code,
						$error_message
					),
					array(
						'status'  => $status_code,
						'body'    => $decoded_body,
						'headers' => $headers_array,
					)
				);
			}
		}

		return array(
			'status'  => $status_code,
			'body'    => $decoded_body,
			'headers' => $headers_array,
		);
	}

	/**
	 * Get the current plugin version.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin version string.
	 */
	private function get_plugin_version() {
		return defined( 'TRUSTEED_VERSION' ) ? TRUSTEED_VERSION : '1.0.0';
	}
}
