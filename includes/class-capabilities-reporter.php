<?php
/**
 * Spec-048 4.9 — reporta a Trusteed qué señales de carrito proyecta ESTA
 * instalación (`POST /api/v1/enforcement/capabilities`).
 *
 * POR QUÉ EXISTE. Sin este reporte el servidor no sabe qué aporta la
 * instalación y las sondas C7/C8 salen «sin evaluar» para toda tienda con el
 * plugin vigente (el reporte sólo lo enviaba el plugin antiguo).
 *
 * LA LISTA ES VERDAD, NO COPIA. `signals_provided()` enumera exactamente las
 * claves que `Trusteed_Checkout_Enforcer` escribe en `cartAttributes`; el gate
 * `signals-provided-plugin-declarations.test.ts` (packages/shared) escanea ese
 * fichero y falla si la declaración y las escrituras divergen.
 *
 * CUÁNDO SE ENVÍA. En `init` si cambió (versión + huella de la lista), al
 * guardar credenciales/ajustes (evento diferido), y una vez al día (cron).
 * Nunca por checkout. Un fallo de red se registra y NO rompe el admin.
 *
 * FIRMA. HMAC-SHA256 hex sobre el JSON canónico del cuerpo sin `signature`,
 * con el secreto de la instalación (mismo esquema que la ruta del servidor).
 *
 * @package Trusteed
 * @since   2.4.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Trusteed_Capabilities_Reporter {

	public const ENDPOINT_PATH = '/api/v1/enforcement/capabilities';

	/** Opción con la huella (versión + señales) del último reporte aceptado. */
	public const OPTION_REPORTED_FINGERPRINT = 'trusteed_capabilities_reported_fingerprint';

	/** Evento diferido tras guardar credenciales/ajustes. */
	public const HOOK_DEFERRED = 'trusteed_capabilities_report_deferred';

	/** Evento diario (WP-Cron). */
	public const HOOK_DAILY = 'trusteed_capabilities_report_daily';

	/** Espera tras un reporte fallido antes de reintentarlo desde `init`. */
	public const TRANSIENT_BACKOFF = 'trusteed_capabilities_report_backoff';

	/** Una hora. Literal y no `HOUR_IN_SECONDS` para no depender del bootstrap. */
	private const BACKOFF_SECONDS = 3600;

	/**
	 * Claves de `cartAttributes` que `Trusteed_Checkout_Enforcer` escribe.
	 * El gate de packages/shared impone que esta lista y las escrituras
	 * coincidan; al añadir una señal al enforcer hay que añadirla aquí.
	 *
	 * @return string[]
	 */
	public static function signals_provided(): array {
		return array(
			'_agent_key_age_hours',
			'_agent_token_jti_malformed',
			'_agent_token_jti_missing',
			'_agent_token_nonce_unavailable',
			'_agent_token_present',
			'_agent_token_replay',
			'_agent_token_signature_invalid',
			'_b2b_order',
			'_lowest_stock',
			'_price_delta_bps',
			'_product_categories',
			'_product_platform',
			'_shipping_freight_forwarder',
			'_shipping_po_box',
			'_stored_value_cents',
			'_subscription',
		);
	}

	/**
	 * Vocabulario del servidor: prefijo `cartAttr.` + clave cruda.
	 *
	 * @return string[]
	 */
	public static function server_signals(): array {
		$out = array();
		foreach ( self::signals_provided() as $attr ) {
			if ( in_array( $attr, self::NOT_READ_BY_ANY_RULE, true ) ) {
				continue;
			}
			$out[] = 'cartAttr.' . $attr;
		}
		return $out;
	}

	/**
	 * Señales que el plugin escribe en el carrito pero que NINGUNA regla lee
	 * (documentadas como huérfanas en `signals-provided-plugin-declarations.test.ts`).
	 *
	 * Se declaran en `signals_provided()` porque es la verdad de lo que se
	 * escribe, pero NO se reportan: el servidor (`SignalsProvidedSchema`)
	 * rechaza con 400 el reporte entero si trae una señal fuera de su
	 * vocabulario, y entonces C7/C8 se quedaban sin veredicto.
	 */
	private const NOT_READ_BY_ANY_RULE = array(
		'_agent_token_replay',
		'_agent_token_jti_missing',
		'_agent_token_jti_malformed',
		'_agent_token_nonce_unavailable',
	);

	/**
	 * Huella estable de lo que se reportaría: cambia con la versión o la lista.
	 */
	public static function fingerprint( string $version ): string {
		$signals = self::server_signals();
		sort( $signals, SORT_STRING );
		return hash( 'sha256', $version . '|' . implode( ',', $signals ) );
	}

	/**
	 * Engancha el reporte. Idempotente.
	 */
	public static function register(): void {
		add_action( 'init', array( self::class, 'maybe_report' ), 20 );
		add_action( 'init', array( self::class, 'ensure_daily_scheduled' ), 20 );
		add_action( self::HOOK_DAILY, array( self::class, 'report_now' ) );
		add_action( self::HOOK_DEFERRED, array( self::class, 'report_now' ) );
		// Al guardar credenciales/ajustes: reenvío diferido (no bloquea la petición).
		foreach ( array( 'trusteed_enforcement_hmac_secret', 'trusteed_enforcement_installation_id', 'trusteed_api_base_url' ) as $opt ) {
			add_action( 'update_option_' . $opt, array( self::class, 'schedule_deferred' ) );
			add_action( 'add_option_' . $opt, array( self::class, 'schedule_deferred' ) );
		}
	}

	/** Programa el latido diario si falta. */
	public static function ensure_daily_scheduled(): void {
		if ( function_exists( 'wp_next_scheduled' ) && ! wp_next_scheduled( self::HOOK_DAILY ) ) {
			wp_schedule_event( time() + 300, 'daily', self::HOOK_DAILY );
		}
	}

	/** Desprograma los eventos (desactivación). */
	public static function unschedule(): void {
		if ( function_exists( 'wp_clear_scheduled_hook' ) ) {
			wp_clear_scheduled_hook( self::HOOK_DAILY );
			wp_clear_scheduled_hook( self::HOOK_DEFERRED );
		}
	}

	/** Reenvío diferido (5 s) tras un cambio de credenciales/ajustes. */
	public static function schedule_deferred(): void {
		if ( function_exists( 'wp_next_scheduled' ) && ! wp_next_scheduled( self::HOOK_DEFERRED ) ) {
			wp_schedule_single_event( time() + 5, self::HOOK_DEFERRED );
		}
	}

	/** En `init`: reporta sólo si la huella cambió desde el último envío aceptado. */
	public static function maybe_report(): void {
		$fingerprint = self::fingerprint( self::plugin_version() );
		if ( get_option( self::OPTION_REPORTED_FINGERPRINT, '' ) === $fingerprint ) {
			return;
		}
		// Tras un rechazo o fallo de red no se reintenta en cada `init`: con el
		// panel abierto serían una petición a la API por cada admin-ajax. El
		// reenvío diario y el diferido (al guardar ajustes) no pasan por aquí.
		if ( false !== get_transient( self::TRANSIENT_BACKOFF ) ) {
			return;
		}
		if ( ! self::report_now() ) {
			set_transient( self::TRANSIENT_BACKOFF, 1, self::BACKOFF_SECONDS );
		}
	}

	/**
	 * Envía el reporte ahora. Nunca propaga errores.
	 *
	 * @return bool true si el servidor lo aceptó (2xx; responde 202).
	 */
	public static function report_now(): bool {
		try {
			$version         = self::plugin_version();
			$merchant_id     = (string) Trusteed_Options::get_option( 'merchant_id', '' );
			$installation_id = (string) Trusteed_Options::get_option( 'enforcement_installation_id', '' );
			$api_base        = (string) Trusteed_Options::get_option( 'api_base_url', defined( 'TRUSTEED_API_BASE' ) ? TRUSTEED_API_BASE : '' );
			$secret          = Trusteed_Crypto_Helper::decrypt( (string) Trusteed_Options::get_option( 'enforcement_hmac_secret', '' ) );

			if ( '' === $merchant_id || '' === $installation_id || '' === $secret || '' === $api_base
				|| ( class_exists( 'Trusteed_Plugin' ) && Trusteed_Plugin::STUB_INSTALLATION_ID === $installation_id ) ) {
				return false;
			}

			$body              = array(
				'installationId'  => $installation_id,
				'merchantId'      => $merchant_id,
				'platform'        => 'WOOCOMMERCE',
				'pluginVersion'   => $version,
				'signalsProvided' => self::server_signals(),
			);
			$body['signature'] = self::sign( $body, $secret );

			$response = wp_remote_post(
				rtrim( $api_base, '/' ) . self::ENDPOINT_PATH,
				array(
					'timeout' => 5,
					'headers' => array( 'Content-Type' => 'application/json' ),
					'body'    => wp_json_encode( $body ),
				)
			);

			if ( is_wp_error( $response ) ) {
				error_log( '[spec-048 4.9] capabilities report failed: ' . $response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				return false;
			}
			$code = (int) wp_remote_retrieve_response_code( $response );
			if ( $code < 200 || $code >= 300 ) { // Server answers 202; any 2xx means accepted.
				error_log( '[spec-048 4.9] capabilities report rejected: HTTP ' . $code ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				return false;
			}
			update_option( self::OPTION_REPORTED_FINGERPRINT, self::fingerprint( $version ), false );
			delete_transient( self::TRANSIENT_BACKOFF );
			return true;
		} catch ( \Throwable $e ) {
			error_log( '[spec-048 4.9] capabilities reporter exception: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return false;
		}
	}

	/**
	 * HMAC-SHA256 hex sobre el JSON canónico (claves asociativas ordenadas
	 * recursivamente, sin escapar `/` ni unicode) del cuerpo sin `signature`.
	 *
	 * @param array  $body   Cuerpo sin `signature`.
	 * @param string $secret Secreto de la instalación.
	 */
	public static function sign( array $body, string $secret ): string {
		unset( $body['signature'] );
		$canonical = wp_json_encode( self::ksort_recursive( $body ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		return hash_hmac( 'sha256', (string) $canonical, $secret );
	}

	/**
	 * @param mixed $value Array o escalar.
	 * @return mixed
	 */
	private static function ksort_recursive( $value ) {
		if ( is_array( $value ) ) {
			$is_list = array() === $value || array_keys( $value ) === range( 0, count( $value ) - 1 );
			if ( ! $is_list ) {
				ksort( $value, SORT_STRING );
			}
			foreach ( $value as $k => $v ) {
				$value[ $k ] = self::ksort_recursive( $v );
			}
		}
		return $value;
	}

	private static function plugin_version(): string {
		return defined( 'TRUSTEED_VERSION' ) ? (string) TRUSTEED_VERSION : '0.0.0';
	}
}
