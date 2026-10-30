<?php
/**
 * Enforcement API client — calls POST /v1/rules/evaluate.
 *
 * Signs requests with HMAC-SHA256 in Stripe-style format:
 *   X-Trusteed-Signature: t=<unix>,s=<sha256-hex>
 *   where signature = HMAC-SHA256("${t}.${rawBody}", secret)
 *
 * Returns tri-state outcome: ALLOW | BLOCK | INDETERMINATE.
 * Caller decides BLOCK vs ALLOW on INDETERMINATE based on failure_mode
 * (spec-048 Gap 5/6 — fail-closed when ENFORCE, fail-open + telemetry when OBSERVE).
 *
 * @package Trusteed
 * @since   1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Outcome enum for evaluate() results.
 *
 * @since 1.5.0
 */
class Trusteed_Eval_Outcome {
	const ALLOW         = 'ALLOW';
	const BLOCK         = 'BLOCK';
	const INDETERMINATE = 'INDETERMINATE';
}

/**
 * Outcome of consume_nonce() — single-use replay protection (spec-048 P2.8).
 *
 *   ACCEPTED       — HTTP 200, nonce stored, token may be used.
 *   REPLAY         — HTTP 409, nonce already seen — caller must mark token INVALID.
 *   INDETERMINATE  — network / 5xx / 4xx / bad response — caller maps per failure_mode.
 *
 * @since 1.6.0
 */
class Trusteed_Nonce_Outcome {
	const ACCEPTED      = 'ACCEPTED';
	const REPLAY        = 'REPLAY';
	const INDETERMINATE = 'INDETERMINATE';
}

/**
 * Result of consume_nonce().
 *
 * @since 1.6.0
 */
class Trusteed_Nonce_Result {

	/** @var string Trusteed_Nonce_Outcome::* */
	public string $outcome;

	/** @var string Short reason code. */
	public string $reason;

	/** @var int|null HTTP status when available. */
	public ?int $http_status;

	public function __construct( string $outcome, string $reason, ?int $http_status = null ) {
		$this->outcome     = $outcome;
		$this->reason      = $reason;
		$this->http_status = $http_status;
	}
}

/**
 * Result of an evaluate call.
 *
 * @since 1.5.0
 */
class Trusteed_Eval_Result {

	/** @var string Trusteed_Eval_Outcome::* */
	public string $outcome;

	/** @var string Short reason code (network_error, http_5xx, bad_response, ok). */
	public string $reason;

	/** @var int|null HTTP status if response received, null otherwise. */
	public ?int $http_status;

	/**
	 * R043 HITL freeze payload when the BLOCK is a human-in-the-loop escalation
	 * (decision=BLOCK + ucp.state=requires_escalation + reason_code trusteed:R043).
	 * Shape: ['rule_code' => string, 'reason' => string, 'evaluation_id' => string].
	 * Null for plain blocks / non-HITL outcomes.
	 *
	 * @since 1.6.0
	 * @var array<string,string>|null
	 */
	public ?array $hitl_payload;

	/**
	 * 2.5.2: veredicto estructurado del servidor en un BLOCK (`agent_reason`), ya saneado:
	 * reason_category, reason, next_action y, si viene, rule_code. Null = servidor antiguo.
	 *
	 * @var array<string,string>|null
	 */
	public ?array $agent_reason;

	public function __construct( string $outcome, string $reason, ?int $http_status = null, ?array $hitl_payload = null, ?array $agent_reason = null ) {
		$this->outcome      = $outcome;
		$this->reason       = $reason;
		$this->http_status  = $http_status;
		$this->hitl_payload = $hitl_payload;
		$this->agent_reason = $agent_reason;
	}

	/**
	 * El servidor va firmado, pero el motivo se trata como entrada: sólo las cuatro claves,
	 * sólo strings, sin caracteres de control y con longitud acotada.
	 *
	 * @param mixed $raw `agent_reason` tal como llega.
	 * @return array<string,mixed>|null Null si falta lo imprescindible.
	 */
	public static function sanitize_agent_reason( $raw ): ?array {
		if ( ! is_array( $raw ) ) {
			return null;
		}
		$limits = array(
			'reason_category' => 64,
			'rule_code'       => 64,
			'reason'          => 400,
			'next_action'     => 64,
		);
		$out    = array();
		// Se valida antes: un limit_exceeded válido es lo que permite cifras en `reason` (bloqueos order_limits).
		$limit_exceeded = self::sanitize_limit_exceeded( $raw['limit_exceeded'] ?? null );
		foreach ( $limits as $key => $max ) {
			if ( ! isset( $raw[ $key ] ) || ! is_string( $raw[ $key ] ) ) {
				continue;
			}
			// Controles, formato (bidi, ancho cero) y separadores U+2028/9: se descarta el valor entero.
			// UTF-8 inválido: preg devuelve false => también se descarta.
			$clean = $raw[ $key ];
			if ( 0 !== preg_match( '/[\p{Cc}\p{Cf}\x{2028}\x{2029}]/u', $clean ) ) {
				continue;
			}
			if ( 'rule_code' === $key ) {
				// Se descarta, no se trunca: un código cortado apunta a otra regla.
				if ( strlen( $clean ) > $max || 1 !== preg_match( '/^R\d{3}(\.[a-z0-9-]+)?$/', $clean ) ) {
					continue;
				}
				$out[ $key ] = $clean;
				continue;
			}
			// Se descarta, no se trunca: un texto cortado cambia de sentido.
			if ( '' === $clean || preg_match_all( '/./su', $clean ) > $max ) {
				continue;
			}
			// La frase del contrato no lleva digitos (umbrales) salvo con limit_exceeded válido; nunca '<' '>' ni entidades HTML.
			$digits = null === $limit_exceeded ? '\p{Nd}' : '';
			if ( 'reason' === $key && 1 === preg_match( '/[' . $digits . '<>]|&#?[A-Za-z0-9]+;/u', $clean ) ) {
				return null;
			}
			$out[ $key ] = $clean;
		}
		foreach ( array( 'reason_category', 'reason', 'next_action' ) as $required ) {
			if ( ! isset( $out[ $required ] ) ) {
				return null;
			}
		}
		// Claves opcionales nuevas: cada una se valida sola; una inválida se descarta sin tocar el resto.
		$extras = array(
			'human_checkout'  => self::sanitize_human_checkout( $raw['human_checkout'] ?? null ),
			'blocked_item'    => self::sanitize_blocked_item( $raw['blocked_item'] ?? null ),
			'published_limit' => self::sanitize_published_limit( $raw['published_limit'] ?? null ),
			'limit_exceeded'  => $limit_exceeded,
		);
		return array_merge( $out, array_filter( $extras, static fn( $v ): bool => null !== $v ) );
	}

	/** Texto libre acotado: sin controles/formato, sin '<' '>' ni entidades HTML. Las cifras sí valen. */
	private static function clean_text( $v, int $max ): ?string {
		if ( ! is_string( $v ) || '' === $v || 0 !== preg_match( '/[\p{Cc}\p{Cf}\x{2028}\x{2029}<>]|&#?[A-Za-z0-9]+;/u', $v ) ) {
			return null;
		}
		$len = preg_match_all( '/./su', $v );
		return ( false === $len || $len > $max ) ? null : $v;
	}

	/** @param mixed $raw */
	private static function sanitize_human_checkout( $raw ): ?array {
		if ( ! is_array( $raw ) || ! isset( $raw['available'] ) || ! is_bool( $raw['available'] ) ) {
			return null;
		}
		$message = self::clean_text( $raw['message'] ?? null, 400 );
		if ( null === $message ) {
			return null;
		}
		$out = array( 'available' => $raw['available'], 'message' => $message );
		if ( array_key_exists( 'url', $raw ) ) {
			$url = $raw['url'];
			// Un url presente pero inválido (p. ej. javascript:) descarta todo el bloque.
			if ( ! is_string( $url ) || strlen( $url ) > 2048 || 1 === preg_match( '/[\x00-\x20\x7f<>"\']/', $url )
				|| ! in_array( strtolower( (string) parse_url( $url, PHP_URL_SCHEME ) ), array( 'http', 'https' ), true )
				|| false === filter_var( $url, FILTER_VALIDATE_URL ) ) {
				return null;
			}
			$out['url'] = $url;
		}
		return $out;
	}

	/** @param mixed $raw */
	private static function sanitize_blocked_item( $raw ): ?array {
		if ( ! is_array( $raw ) || ! in_array( $raw['kind'] ?? null, array( 'category', 'sku', 'digital_good_type' ), true ) ) {
			return null;
		}
		$value = $raw['value'] ?? null;
		if ( ! is_string( $value ) || 1 !== preg_match( '/^[\p{L}\p{N}_.:\/ -]{1,64}$/u', $value ) ) {
			return null;
		}
		return array( 'kind' => $raw['kind'], 'value' => $value );
	}

	/** @param mixed $raw */
	private static function sanitize_published_limit( $raw ): ?array {
		if ( ! is_array( $raw ) ) {
			return null;
		}
		$source = self::clean_text( $raw['source'] ?? null, 128 );
		if ( null === $source ) {
			return null;
		}
		$kind = $raw['kind'] ?? null;
		if ( 'max_cart_value' === $kind && is_int( $raw['amount_cents'] ?? null ) && $raw['amount_cents'] >= 0 && 'minor_units_of_store_currency' === ( $raw['unit'] ?? null ) ) {
			return array( 'kind' => $kind, 'amount_cents' => $raw['amount_cents'], 'unit' => $raw['unit'], 'source' => $source );
		}
		if ( 'max_units_per_line' === $kind && is_int( $raw['units'] ?? null ) && $raw['units'] >= 0 ) {
			return array( 'kind' => $kind, 'units' => $raw['units'], 'source' => $source );
		}
		return null;
	}

	/** @param mixed $raw */
	private static function sanitize_limit_exceeded( $raw ): ?array {
		if ( ! is_array( $raw ) ) {
			return null;
		}
		$kind = $raw['kind'] ?? null;
		// Un campo inválido descarta TODO el bloque. unit se valida contra el enum, sin cruzarlo con kind.
		if ( ! in_array( $kind, array( 'max_cart_value', 'max_line_value', 'max_items_per_order', 'max_units_per_line' ), true )
			|| ! is_string( $raw['rule'] ?? null ) || 1 !== preg_match( '/^R\d{3}$/D', $raw['rule'] )
			|| ! is_string( $raw['rule_code'] ?? null ) || 1 !== preg_match( '/^R\d{3}\.[a-z0-9-]{1,60}$/D', $raw['rule_code'] )
			|| ! is_int( $raw['limit'] ?? null ) || $raw['limit'] < 0
			|| ! is_int( $raw['observed'] ?? null ) || $raw['observed'] < 0
			|| ! in_array( $raw['unit'] ?? null, array( 'minor_units_of_store_currency', 'units' ), true ) ) {
			return null;
		}
		$name    = self::clean_text( $raw['rule_name'] ?? null, 80 );
		$message = self::clean_text( $raw['message'] ?? null, 400 );
		if ( null === $name || null === $message ) {
			return null;
		}
		return array(
			'rule'      => $raw['rule'],
			'rule_code' => $raw['rule_code'],
			'rule_name' => $name,
			'kind'      => $kind,
			'limit'     => $raw['limit'],
			'observed'  => $raw['observed'],
			'unit'      => $raw['unit'],
			'message'   => $message,
		);
	}

	/**
	 * Frase breve para la persona que compra: elemento bloqueado, límite y salida humana.
	 * Texto plano ya escapado (esc_html); vacío si el servidor no mandó nada de esto.
	 *
	 * @param array<string,mixed> $reason agent_reason saneado.
	 */
	public static function human_detail( array $reason ): string {
		$esc   = static fn( string $t ): string => function_exists( 'esc_html' ) ? esc_html( $t ) : htmlspecialchars( $t, ENT_QUOTES, 'UTF-8' );
		$parts = array();
		if ( isset( $reason['blocked_item']['value'] ) ) {
			$parts[] = 'Item: ' . $esc( (string) $reason['blocked_item']['value'] ) . '.';
		}
		$limit = $reason['published_limit'] ?? null;
		if ( isset( $reason['limit_exceeded']['message'] ) ) {
			// Texto del servidor (ya validado, con cifras): sustituye a la línea genérica de published_limit.
			$parts[] = $esc( (string) $reason['limit_exceeded']['message'] );
		} elseif ( is_array( $limit ) && 'max_cart_value' === $limit['kind'] ) {
			$cur     = function_exists( 'get_woocommerce_currency' ) ? (string) get_woocommerce_currency() : '';
			$parts[] = 'Limit: ' . $esc( trim( number_format( $limit['amount_cents'] / 100, 2, '.', '' ) . ' ' . $cur ) ) . '.';
		} elseif ( is_array( $limit ) && 'max_units_per_line' === $limit['kind'] ) {
			$parts[] = 'Limit: max. ' . (int) $limit['units'] . ' units per line.';
		}
		$hc = $reason['human_checkout'] ?? null;
		if ( is_array( $hc ) ) {
			$parts[] = ( $hc['available'] && isset( $hc['url'] ) ) ? 'Complete your purchase here: ' . $esc( $hc['url'] ) : $esc( $hc['message'] );
		}
		return implode( ' ', $parts );
	}
}

/**
 * Class Trusteed_Enforcement_Api_Client
 *
 * @since 1.3.0
 */
class Trusteed_Enforcement_Api_Client {

	private string $api_base;
	private string $installation_id;
	private string $hmac_secret;

	private const TIMEOUT_SECONDS = 5;

	/**
	 * Gap 5 — sliding window + threshold for the config-drift admin notice.
	 * Counts /v1/rules/evaluate HTTP-4xx responses; when the count exceeds
	 * the threshold within the window, we set a 24h notice transient.
	 */
	private const EVAL_4XX_RATE_TRANSIENT      = 'trusteed_eval_4xx_rate';
	private const EVAL_4XX_RATE_WINDOW_SECONDS = 900;   // 15 min
	private const EVAL_4XX_RATE_THRESHOLD      = 10;
	public const  EVAL_4XX_NOTICE_TRANSIENT    = 'trusteed_eval_config_drift_notice';
	private const EVAL_4XX_NOTICE_TTL_SECONDS  = 86400; // 24h

	public function __construct( string $api_base, string $installation_id, string $hmac_secret ) {
		$this->api_base        = rtrim( $api_base, '/' );
		$this->installation_id = $installation_id;
		$this->hmac_secret     = $hmac_secret;
	}

	/**
	 * Call /v1/rules/evaluate and return a tri-state result.
	 *
	 * Network errors, timeouts, HTTP 5xx, malformed responses → INDETERMINATE.
	 * Caller maps INDETERMINATE to BLOCK or ALLOW per failure_mode policy.
	 *
	 * @since 1.5.0
	 *
	 * @param array $payload Evaluate payload.
	 * @return Trusteed_Eval_Result
	 */
	public function evaluate( array $payload ): Trusteed_Eval_Result {
		$url      = $this->api_base . '/v1/rules/evaluate';
		$raw_body = wp_json_encode( $payload );

		if ( false === $raw_body ) {
			return new Trusteed_Eval_Result( Trusteed_Eval_Outcome::INDETERMINATE, 'json_encode_failed' );
		}

		$signature = $this->build_signature( $raw_body );
		if ( '' === $signature ) {
			// F5.S3 — fail-closed: never POST a placeholder/unsigned request.
			return new Trusteed_Eval_Result( Trusteed_Eval_Outcome::INDETERMINATE, 'hmac_secret_missing' );
		}

		$headers = array(
			'Content-Type'               => 'application/json',
			'X-Trusteed-Installation-Id' => $this->installation_id,
			'X-Trusteed-Signature'       => $signature,
		);

		$response = wp_remote_post(
			$url,
			array(
				'timeout' => self::TIMEOUT_SECONDS,
				'headers' => $headers,
				'body'    => $raw_body,
			)
		);

		if ( is_wp_error( $response ) ) {
			error_log( '[amcp] enforcement evaluate network error: ' . $response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return new Trusteed_Eval_Result( Trusteed_Eval_Outcome::INDETERMINATE, 'network_error' );
		}

		$status = (int) wp_remote_retrieve_response_code( $response );

		if ( $status >= 500 ) {
			error_log( '[amcp] enforcement evaluate HTTP ' . $status ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return new Trusteed_Eval_Result( Trusteed_Eval_Outcome::INDETERMINATE, 'http_5xx', $status );
		}

		if ( 200 !== $status ) {
			// 4xx — backend rejected payload. Treat as INDETERMINATE (config bug),
			// not as ALLOW, so ENFORCE mode surfaces it instead of silently passing.
			error_log( '[amcp] enforcement evaluate unexpected HTTP ' . $status ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			$this->record_4xx_rate();
			return new Trusteed_Eval_Result( Trusteed_Eval_Outcome::INDETERMINATE, 'http_4xx', $status );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) || empty( $body['decision'] ) ) {
			return new Trusteed_Eval_Result( Trusteed_Eval_Outcome::INDETERMINATE, 'bad_response', $status );
		}

		if ( 'BLOCK' === $body['decision'] ) {
			// R043 HITL — surface the freeze payload so the checkout enforcer can
			// park the order pending merchant approval (via Trusteed_R043_Hitl_Gate)
			// instead of a hard block that loses the customer's intent. Guarded by
			// class_exists so this file has no hard dependency on load order.
			$hitl = null;
			if (
				class_exists( 'Trusteed_R043_Hitl_Gate' )
				&& Trusteed_R043_Hitl_Gate::is_hitl_response( $body )
			) {
				$hitl = array(
					'rule_code'     => Trusteed_R043_Hitl_Gate::rule_code_from( $body ),
					'reason'        => (string) ( $body['reason'] ?? '' ),
					'evaluation_id' => (string) ( $body['evaluationId'] ?? '' ),
				);
			}
			return new Trusteed_Eval_Result( Trusteed_Eval_Outcome::BLOCK, 'ok', $status, $hitl, Trusteed_Eval_Result::sanitize_agent_reason( $body['agent_reason'] ?? null ) );
		}

		return new Trusteed_Eval_Result( Trusteed_Eval_Outcome::ALLOW, 'ok', $status );
	}

	/**
	 * Call POST /v1/agent-events/nonce-consume to record a single-use
	 * agent-token jti (spec-048 P2.8 replay protection).
	 *
	 * @since 1.6.0
	 *
	 * @param string $merchant_id Merchant UUID (must match installation merchant).
	 * @param string $agent_did   Agent DID from the verified JWS iss/kid claim.
	 * @param string $jti         Single-use nonce — base64url 16–128 chars.
	 * @param int    $exp         Token exp (unix seconds). Used as nonce TTL upper bound.
	 * @return Trusteed_Nonce_Result
	 */
	public function consume_nonce( string $merchant_id, string $agent_did, string $jti, int $exp ): Trusteed_Nonce_Result {
		$url = $this->api_base . '/v1/agent-events/nonce-consume';

		$payload = array(
			'merchantId'     => $merchant_id,
			'installationId' => $this->installation_id,
			'agentId'        => $agent_did,
			'nonce'          => $jti,
			'expiresAt'      => gmdate( 'Y-m-d\TH:i:s\Z', $exp > 0 ? $exp : ( time() + 300 ) ),
		);

		$raw_body = wp_json_encode( $payload );
		if ( false === $raw_body ) {
			return new Trusteed_Nonce_Result( Trusteed_Nonce_Outcome::INDETERMINATE, 'json_encode_failed' );
		}

		$signature = $this->build_signature( $raw_body );
		if ( '' === $signature ) {
			// F5.S3 — fail-closed.
			return new Trusteed_Nonce_Result( Trusteed_Nonce_Outcome::INDETERMINATE, 'hmac_secret_missing' );
		}

		$headers = array(
			'Content-Type'               => 'application/json',
			'X-Trusteed-Installation-Id' => $this->installation_id,
			'X-Trusteed-Signature'       => $signature,
		);

		$response = wp_remote_post(
			$url,
			array(
				'timeout' => self::TIMEOUT_SECONDS,
				'headers' => $headers,
				'body'    => $raw_body,
			)
		);

		if ( is_wp_error( $response ) ) {
			return new Trusteed_Nonce_Result( Trusteed_Nonce_Outcome::INDETERMINATE, 'network_error' );
		}

		$status = (int) wp_remote_retrieve_response_code( $response );

		if ( 200 === $status ) {
			return new Trusteed_Nonce_Result( Trusteed_Nonce_Outcome::ACCEPTED, 'ok', $status );
		}
		if ( 409 === $status ) {
			return new Trusteed_Nonce_Result( Trusteed_Nonce_Outcome::REPLAY, 'replay_detected', $status );
		}
		if ( $status >= 500 ) {
			return new Trusteed_Nonce_Result( Trusteed_Nonce_Outcome::INDETERMINATE, 'http_5xx', $status );
		}
		return new Trusteed_Nonce_Result( Trusteed_Nonce_Outcome::INDETERMINATE, 'http_4xx', $status );
	}

	/**
	 * Gap 5 — track HTTP-4xx evaluate responses in a 15-minute sliding window
	 * and, when they exceed the threshold, set a 24h admin-notice transient
	 * so the merchant sees that their plugin is misconfigured (bad
	 * installationId / merchantId / hmac_secret) instead of relying on ops
	 * spotting it in error_log.
	 *
	 * Best-effort: failures to read/write transients are swallowed so the
	 * evaluate hot path is never affected.
	 *
	 * @return void
	 */
	private function record_4xx_rate(): void {
		if ( ! function_exists( 'get_transient' ) || ! function_exists( 'set_transient' ) ) {
			return;
		}

		$now    = time();
		$record = get_transient( self::EVAL_4XX_RATE_TRANSIENT );
		if ( ! is_array( $record ) || ! isset( $record['start'] ) || ( $now - (int) $record['start'] ) > self::EVAL_4XX_RATE_WINDOW_SECONDS ) {
			$record = array(
				'start' => $now,
				'count' => 0,
			);
		}

		$record = array(
			'start' => (int) $record['start'],
			'count' => ( (int) $record['count'] ) + 1,
		);

		set_transient( self::EVAL_4XX_RATE_TRANSIENT, $record, self::EVAL_4XX_RATE_WINDOW_SECONDS );

		if ( $record['count'] >= self::EVAL_4XX_RATE_THRESHOLD ) {
			set_transient( self::EVAL_4XX_NOTICE_TRANSIENT, 1, self::EVAL_4XX_NOTICE_TTL_SECONDS );
		}
	}

	/**
	 * Build the `t=<ts>,s=<hex>` signature header.
	 *
	 * F5.S3 — Returns empty string when the HMAC secret is unset. Callers
	 * MUST treat empty as fail-closed (do NOT send the request). The legacy
	 * `'dev-bypass'` literal is removed so a misconfigured plugin can never
	 * emit a forged placeholder header that a buggy backend might accept.
	 *
	 * @param string $raw_body Request body.
	 * @return string Signature header value, or empty string on fail-closed.
	 */
	private function build_signature( string $raw_body ): string {
		if ( empty( $this->hmac_secret ) ) {
			error_log( '[amcp.enforcement_api] fail-closed: enforcement_hmac_secret missing' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			if ( function_exists( 'update_option' ) ) {
				update_option( Trusteed_Agent_Event_Webhook::NOTICE_OPTION_HMAC_MISSING, 1, false );
			}
			return '';
		}

		$ts        = time();
		$message   = $ts . '.' . $raw_body;
		$signature = hash_hmac( 'sha256', $message, $this->hmac_secret );

		return 't=' . $ts . ',s=' . $signature;
	}
}
