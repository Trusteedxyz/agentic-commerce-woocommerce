<?php
/**
 * Checkout enforcer — intercepts WooCommerce checkout for agent-initiated orders.
 *
 * Supports both checkout paths:
 *
 * CLASSIC checkout (woocommerce_checkout_process):
 *  1. Cart bridge appends ?trusteed_agent_id=<did> to the checkout URL.
 *  2. `woocommerce_before_checkout_form` saves the DID to WC session.
 *  3. `woocommerce_checkout_process` fires on POST: calls /v1/rules/evaluate.
 *  4. On BLOCK → wc_add_notice() aborts.
 *
 * BLOCKS checkout (woocommerce_store_api_checkout_update_order_from_request):
 *  1. Same URL capture and WC session storage (step 2 above fires on page load).
 *  2. Store API fires the hook on the AJAX checkout POST.
 *  3. On BLOCK → RouteException with 403 aborts the order.
 *
 * Fail-open design: connectivity issues, timeouts, or misconfiguration
 * never prevent a legitimate checkout from completing.
 *
 * @package Trusteed
 * @since   1.3.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Trusteed_Checkout_Enforcer
 *
 * @since 1.3.0
 */
class Trusteed_Checkout_Enforcer {

	/** 2.3.6 (5b): our own failure closed an agent — not a rule; own message. */
	const DECISION_UNAVAILABLE = 'UNAVAILABLE';

	/** 2.5.2: mismas frases que AGENT_REASON_TEXT del servidor (merchant_policy / checks_unavailable). */
	const REASON_MERCHANT_POLICY    = 'Checkout declined: the merchant does not accept this purchase from automated agents. The order was not placed and nothing was charged. Escalate to a human buyer.';
	const REASON_CHECKS_UNAVAILABLE = 'Checkout not completed: the checks for this order could not run right now. This is not a merchant decision, the order was not placed and nothing was charged. Wait a few minutes and retry.';

	/** 2.3.6 (5b): DID proven by a VALID token in the last run_evaluation(); '' otherwise. */
	private string $verified_did = '';

	/**
	 * 2.4.0: sólo lo activa evaluate_order_for_agent_api() (pedido creado por la API de
	 * Trusteed, sin sesión de WooCommerce): el pedido es agéntico aunque no haya señal de sesión.
	 */
	private bool $force_agentic = false;

	/**
	 * 2.4.0: DID de agente ya autenticado por el canal firmado de Trusteed (HMAC de instalación).
	 * Sólo lo fija evaluate_order_for_agent_api(); null en el checkout normal.
	 */
	private ?string $trusted_agent_did = null;

	/**
	 * Niveles de identidad que el servidor entiende (AGENT_IDENTITY_LEVELS). Cualquier otro
	 * valor se descarta: nunca se reenvía una cadena desconocida.
	 */
	const IDENTITY_LEVELS = array( 'verified_signature', 'oauth_person', 'credential_only', 'anonymous' );

	/**
	 * 2.5.1: nivel de identidad que Trusteed resolvió para el agente (canal firmado por el HMAC
	 * de instalación). Sólo lo fija evaluate_order_for_agent_api(); null en el checkout normal,
	 * donde el payload no lo lleva.
	 */
	private ?string $trusted_identity_level = null;

	/** 2.5.2: motivo estructurado del último run_evaluation() (BLOCK/UNAVAILABLE); null en ALLOW. */
	private ?array $last_agent_reason = null;

	/** 2.5.2: verificador de la firma RFC 9421 de un agente de navegador (null = desactivado). */
	private ?Trusteed_Browser_Agent_Signature $browser_signature;

	/** 2.5.2: resultado de la verificación en el último run_evaluation() (null = sin firma o apagado). */
	private ?array $browser_result = null;

	/**
	 * Enforcement API client.
	 *
	 * @since 1.3.0
	 * @var Trusteed_Enforcement_Api_Client
	 */
	private Trusteed_Enforcement_Api_Client $api_client;

	/**
	 * Merchant UUID.
	 *
	 * @since 1.3.0
	 * @var string
	 */
	private string $merchant_id;

	/**
	 * Installation UUID passed in evaluate requests.
	 *
	 * @since 1.3.0
	 * @var string
	 */
	private string $installation_id;

	/**
	 * Snapshot client — fetches agentDidResolver for Ed25519 verification.
	 *
	 * @since 1.4.0
	 * @var Trusteed_Snapshot_Client_Woo|null
	 */
	private ?Trusteed_Snapshot_Client_Woo $snapshot_client;

	/**
	 * Failure mode policy (spec-048 Gap 5/6 tri-state fail-closed).
	 *
	 * Since 2.3.6 it only governs AGENTIC checkouts (DID, token or origin
	 * marker); a human checkout is never blocked by INDETERMINATE:
	 * 'enforce' → an agent's INDETERMINATE maps to BLOCK (fail-closed).
	 * 'observe' → an agent's INDETERMINATE maps to ALLOW + telemetry log.
	 *
	 * @since 1.5.0
	 * @var string
	 */
	private string $failure_mode;

	/**
	 * Optional agent-event webhook used to POST `enforcement_indeterminate`
	 * telemetry to backend so `enforcement_api_fail_total` Prometheus counter
	 * ticks per fail-open / fail-closed event.
	 *
	 * @since 1.5.0
	 * @var Trusteed_Agent_Event_Webhook|null
	 */
	private ?Trusteed_Agent_Event_Webhook $event_webhook;

	/**
	 * Constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param Trusteed_Enforcement_Api_Client   $api_client      Enforcement API client.
	 * @param string                        $merchant_id     Merchant UUID.
	 * @param string                        $installation_id Installation UUID.
	 * @param Trusteed_Snapshot_Client_Woo|null $snapshot_client Optional snapshot client for token verification.
	 * @param string                        $failure_mode    'enforce' (fail-closed) or 'observe' (fail-open).
	 */
	public function __construct(
		Trusteed_Enforcement_Api_Client $api_client,
		string $merchant_id,
		string $installation_id,
		?Trusteed_Snapshot_Client_Woo $snapshot_client = null,
		string $failure_mode = 'enforce',
		?Trusteed_Agent_Event_Webhook $event_webhook = null,
		?Trusteed_Browser_Agent_Signature $browser_signature = null
	) {
		$this->browser_signature = $browser_signature;
		$this->api_client      = $api_client;
		$this->merchant_id     = $merchant_id;
		$this->installation_id = $installation_id;
		$this->snapshot_client = $snapshot_client;
		// Trim + lowercase contra el vocabulario enforce|observe; un valor no
		// resuelto (p. ej. `permissive` de otra plataforma) cierra: 'enforce'.
		$this->failure_mode    = Trusteed_Enforcement_Failure_Policy::normalize_failure_mode( $failure_mode ) ?? 'enforce';
		$this->event_webhook   = $event_webhook;
	}

	/** 2.5.2: JSON {status, provider, keyId} de la firma verificada en esta petición; '' si no hubo. */
	public function browser_signature_meta(): string {
		if ( null === $this->browser_result ) {
			return '';
		}
		$r = $this->browser_result;
		return (string) wp_json_encode( array( 'status' => $r['status'], 'provider' => $r['provider'], 'keyId' => $r['keyId'] ) );
	}

	/**
	 * Register WooCommerce hooks.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function init(): void {
		// Capture agent_id from URL into WC session on any front-end page load.
		// template_redirect fires after WC is fully initialized (including session).
		add_action( 'template_redirect', array( $this, 'capture_agent_session_early' ), 1 );

		// Classic checkout template hook (shortcode path).
		add_action( 'woocommerce_before_checkout_form', array( $this, 'capture_agent_session' ), 1 );

		// Classic checkout: fires on POST form submit.
		add_action( 'woocommerce_checkout_process', array( $this, 'enforce_rules_classic' ) );

		// Blocks / Store API checkout: fires during AJAX order creation.
		add_action( 'woocommerce_store_api_checkout_update_order_from_request', array( $this, 'enforce_rules_blocks' ), 10, 2 );

		// 2.3.6 (5b): la REST de pedidos no puede escribir la atribución del agente.
		Trusteed_Rest_Order_Meta_Guard::register();
	}

	/**
	 * Evalúa las reglas locales sobre un pedido ya creado por la API de Trusteed
	 * (POST trusteed/v1/orders), donde no hay checkout ni sesión de WooCommerce.
	 *
	 * Mismo camino que el checkout (run_evaluation: API de enforcement, válvula
	 * offline y política de fallo), con dos diferencias declaradas: el pedido es
	 * agéntico por construcción, y el DID —autenticado por el HMAC de instalación,
	 * no por un token del comprador— viaja como agentId.
	 *
	 * @since 2.4.0
	 *
	 * @param \WC_Order  $order     Pedido creado (estado pending, con dirección y método).
	 * @param string|null $agent_did DID del agente, o null si no viene.
	 * @param string|null $identity_level Nivel de identidad (2.5.1); fuera del vocabulario se descarta.
	 * @return string 'ALLOW', 'BLOCK' o self::DECISION_UNAVAILABLE.
	 */
	public function evaluate_order_for_agent_api( $order, ?string $agent_did = null, ?string $identity_level = null ): string {
		$this->force_agentic          = true;
		$this->trusted_agent_did      = ( null !== $agent_did && '' !== $agent_did ) ? $agent_did : null;
		$this->trusted_identity_level = ( null !== $identity_level && in_array( $identity_level, self::IDENTITY_LEVELS, true ) ) ? $identity_level : null;
		try {
			return $this->run_evaluation(
				fn(): array => $this->build_order_context_from_order( $order, null )
			);
		} finally {
			$this->force_agentic          = false;
			$this->trusted_agent_did      = null;
			$this->trusted_identity_level = null;
		}
	}

	/**
	 * Capture agent_id early (before woocommerce_before_checkout_form fires).
	 *
	 * For blocks checkout the template hook may not fire, so we also hook on
	 * 'wp' which runs on every front-end page load including the checkout page.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function capture_agent_session_early(): void {
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
			return;
		}
		// Bail if neither the DID, the token nor the origin marker is in the URL.
		if ( ! isset( $_GET[ Trusteed_Cart_Bridge::AGENT_ID_PARAM ] ) && ! isset( $_GET[ Trusteed_Cart_Bridge::AGENT_TOKEN_PARAM ] ) && ! isset( $_GET[ Trusteed_Cart_Bridge::ORIGIN_PARAM ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		$this->capture_agent_session();
	}

	/**
	 * Save the agent_id and optional agent JWS token URL params into the WC session.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function capture_agent_session(): void {
		// Marca de origen de nuestro MCP (Trusteed_Cart_Bridge::ORIGIN_PARAM).
		// Añadirla a mano sólo endurece el trato (agente); ver la constante.
		if ( isset( $_GET[ Trusteed_Cart_Bridge::ORIGIN_PARAM ] ) && WC()->session ) { // phpcs:ignore WordPress.Security.NonceVerification
			WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN, '1' );
			WC()->session->save_data();
		}

		if ( ! WC()->session ) {
			return;
		}

		// DID declarado: sólo una declaración (Trusteed_Agent_Identity); marca el
		// checkout como agéntico y se ata al token, nunca viaja ni atribuye solo.
		// Task 16: `?param[]=x` llega como array; rawurldecode() lanzaba TypeError
		// (500). Un valor que no es cadena es una señal de agente rota (DID ⇒
		// marca de origen; token ⇒ token rechazado).
		if ( isset( $_GET[ Trusteed_Cart_Bridge::AGENT_ID_PARAM ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$raw = self::url_param( Trusteed_Cart_Bridge::AGENT_ID_PARAM );
			if ( null === $raw ) {
				WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN, '1' );
			} elseif ( preg_match( Trusteed_Cart_Bridge::AGENT_DID_RE, $raw ) ) {
				WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENT_ID, $raw );
			} elseif ( '' !== $raw ) {
				// Un DID con forma rota sigue siendo una señal de agente (sólo endurece).
				WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN, '1' );
			}
		}

		// Token (2.3.6: también sin DID; el bridge puede emitirlo solo). Uno sin
		// forma de JWS es un token inválido de agente, nunca «sin token».
		if ( isset( $_GET[ Trusteed_Cart_Bridge::AGENT_TOKEN_PARAM ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$raw_token = self::url_param( Trusteed_Cart_Bridge::AGENT_TOKEN_PARAM );
			$accepted  = null === $raw_token
				? array( 'token' => '', 'rejected' => true )
				: Trusteed_Agent_Identity::accept_token( $raw_token );
			if ( '' !== $accepted['token'] ) {
				WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, $accepted['token'] );
			} elseif ( $accepted['rejected'] ) {
				WC()->session->set( Trusteed_Agent_Identity::SESSION_TOKEN_REJECTED, '1' );
			}
		}

		WC()->session->save_data();
	}

	/**
	 * URL parameter as a decoded, sanitized string; null when it is present but
	 * not a string (`?name[]=x`). Task 16.
	 *
	 * @param string $name Query parameter name.
	 * @return string|null
	 */
	private static function url_param( string $name ): ?string {
		$value = wp_unslash( $_GET[ $name ] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification
		return is_string( $value ) ? sanitize_text_field( rawurldecode( $value ) ) : null;
	}

	/**
	 * Enforce rules for the classic checkout (woocommerce_checkout_process).
	 *
	 * Throws \Exception on BLOCK — WooCommerce catches it during
	 * checkout processing and aborts order creation, re-rendering the
	 * checkout page with the error message. This matches the hard-fail
	 * semantics of the Store API path (spec-048 enforcement parity).
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 * @throws \Exception On BLOCK decision.
	 */
	public function enforce_rules_classic(): void {
		// 2.3.6 (5b): el token del formulario es señal de agente (mismo campo que
		// lee WC-AgenticTools); la atribución sólo sale de un token verificado.
		$decision = $this->run_evaluation(
			fn(): array => $this->get_order_context_from_post(),
			array( $_POST[ Trusteed_Agent_Identity::REQUEST_TOKEN_FIELD ] ?? '' ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
		);

		if ( self::DECISION_UNAVAILABLE === $decision ) {
			throw new \Exception( esc_html__( 'Your order could not be verified right now. Please try again in a few minutes.', 'agenticmcpstores' ) );
		}

		if ( 'BLOCK' === $decision ) {
			throw new \Exception(
				esc_html__(
					'Your order could not be completed because it did not pass the store\'s agent verification rules. Please contact the store if you believe this is an error.',
					'agenticmcpstores'
				)
			);
		}

		Trusteed_Browser_Agent_Signature::stamp_next_classic_order( $this->browser_signature_meta() );

		// ALLOW path — delegate to Trusteed_Classic_Meta_Persister which registers
		// a one-shot `woocommerce_checkout_create_order` hook to stamp the
		// three metas the Blocks path writes inline. Only a DID proven by a
		// VALID token (2.3.6): a declared one never attributes the order.
		if ( '' !== $this->verified_did ) {
			Trusteed_Classic_Meta_Persister::register( $this->verified_did );
		}
	}

	/**
	 * Enforce rules for the Blocks / Store API checkout.
	 *
	 * Fires via woocommerce_store_api_checkout_update_order_from_request.
	 * Throws RouteException on BLOCK so the Store API returns a 422 with
	 * the block reason visible to the client.
	 *
	 * @since 1.3.0
	 *
	 * @param \WC_Order        $order   The order being created.
	 * @param \WP_REST_Request $request The Store API request.
	 *
	 * @return void
	 * @throws \Automattic\WooCommerce\StoreApi\Exceptions\RouteException On BLOCK.
	 */
	public function enforce_rules_blocks( $order, $request ): void {
		$body          = is_object( $request ) && method_exists( $request, 'get_json_params' ) ? $request->get_json_params() : array();
		// 2.3.6 (5b): token del cuerpo y, si no, de la meta del pedido (lo que lee WC-AgenticTools).
		$decision      = $this->run_evaluation(
			fn(): array => $this->build_order_context_from_order( $order, $request ),
			array(
				is_array( $body ) ? ( $body[ Trusteed_Agent_Identity::REQUEST_TOKEN_FIELD ] ?? '' ) : '',
				method_exists( $order, 'get_meta' ) ? $order->get_meta( Trusteed_Agent_Identity::REQUEST_TOKEN_FIELD, true ) : '',
			)
		);

		if ( self::DECISION_UNAVAILABLE === $decision && class_exists( '\Automattic\WooCommerce\StoreApi\Exceptions\RouteException' ) ) {
			throw new \Automattic\WooCommerce\StoreApi\Exceptions\RouteException(
				'trusteed_agent_unverifiable',
				__( 'Your order could not be verified right now. Please try again in a few minutes.', 'agenticmcpstores' ),
				503,
				$this->block_additional_data()
			);
		}

		if ( 'BLOCK' === $decision || self::DECISION_UNAVAILABLE === $decision ) {
			if ( class_exists( '\Automattic\WooCommerce\StoreApi\Exceptions\RouteException' ) ) {
				throw new \Automattic\WooCommerce\StoreApi\Exceptions\RouteException(
					'trusteed_agent_blocked',
					trim(
						__(
							'Your order could not be completed because it did not pass the store\'s agent verification rules. Please contact the store if you believe this is an error.',
							'agenticmcpstores'
						) . ' ' . ( is_array( $this->last_agent_reason ) ? Trusteed_Eval_Result::human_detail( $this->last_agent_reason ) : '' )
					),
					403,
					$this->block_additional_data()
				);
			}
			// Fallback: add notice (will surface on next page load).
			wc_add_notice(
				self::DECISION_UNAVAILABLE === $decision
					? __( 'Your order could not be verified right now. Please try again in a few minutes.', 'agenticmcpstores' )
					: __(
						'Your order could not be completed because it did not pass the store\'s agent verification rules.',
						'agenticmcpstores'
					),
				'error'
			);
			return;
		}

		// 2.5.2: la verificación de firma se anota (observe y enforce), sin cambiar nada más.
		if ( '' !== $this->browser_signature_meta() ) {
			$order->update_meta_data( Trusteed_Browser_Agent_Signature::META_KEY, $this->browser_signature_meta() );
		}

		// ALLOW path: persist agent DID + R022 cart-attr status so the order webhook
		// can propagate agentIdHash to PlatformOrder for R023 refund-abuse-guard
		// enforcement and downstream readers can verify agent provenance. Only a
		// DID proven by a VALID token of that same DID (2.3.6): a declared one
		// is evaluated but never attributed.
		if ( '' !== $this->verified_did ) {
			$order->update_meta_data( '_trusteed_agent_did', sanitize_text_field( $this->verified_did ) );
			$order->update_meta_data( '_trusteed_agent_status', 'ok' );
			$order->update_meta_data( '_trusteed_eval_at', gmdate( DATE_ATOM ) );
			$order->save_meta_data();
		}
	}

	/**
	 * Señales agénticas de la sesión, leídas ANTES de que run_evaluation() la
	 * borre (Trusteed_Enforcement_Failure_Policy::is_agentic).
	 *
	 * @since 2.3.6
	 *
	 * @param string|null                        $agent_id DID ya normalizado (null si no hay).
	 * @param array{token:string, rejected:bool} $token    De resolve_token(); uno rechazado también cuenta.
	 * @return bool
	 */
	private function resolve_is_agentic( ?string $agent_id, array $token ): bool {
		if ( $this->force_agentic ) {
			return true;
		}
		$session = WC()->session;
		$origin  = $session ? $session->get( Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN ) : null;
		return Trusteed_Enforcement_Failure_Policy::is_agentic(
			$agent_id,
			'' !== $token['token'] || $token['rejected'],
			'1' === $origin
		);
	}

	/**
	 * Read agent_id and token from session, verify signature, call evaluate, clear session.
	 *
	 * Tri-state outcome from API client (spec-048 Gap 5/6):
	 *   ALLOW         → return 'ALLOW'
	 *   BLOCK         → return 'BLOCK'
	 *   INDETERMINATE → offline safety valve (scoped by appliesTo), then
	 *                   Trusteed_Enforcement_Failure_Policy::decide_unavailable():
	 *                   a human → 'ALLOW'; an agent → 'BLOCK' in 'enforce',
	 *                   'ALLOW' in 'observe' (since 2.3.6, A8).
	 *
	 * App Store remediation (2026-07-11): previously this method returned
	 * 'ALLOW' immediately for any non-agent cart, WITHOUT ever calling the
	 * evaluator — which meant merchant-wide policy rules (R019 country
	 * restriction, R020 business hours, R030 max-order-amount, etc.) never
	 * applied to a normal human checkout, only to agentic ones. The shared
	 * evaluator (`rule-evaluator.service.ts`) already resolves each rule's
	 * `appliesTo` from ruleCode identity (not a DB column) and correctly
	 * excludes AGENT-only rules (R001, etc.) when `agentId` is null — so it
	 * is now safe to call `evaluate()` unconditionally with `agentId: null`
	 * for a non-agent cart, and let the server-side partition decide which
	 * rules apply. Only merchant-unset / WC-session-unavailable still fail
	 * fast (nothing to evaluate against).
	 *
	 * @since 1.3.0
	 *
	 * @param \Closure         $build_context  Builds the orderContext array. It runs INSIDE the
	 *                                         guard (Task 29b): an exception while reading the
	 *                                         cart/order is our failure, not a reason to abort
	 *                                         a person's order.
	 * @param array<int,mixed> $request_tokens Agent tokens carried by the request (2.3.6, 5b).
	 * @return string 'ALLOW', 'BLOCK' (a rule) or self::DECISION_UNAVAILABLE (our failure).
	 */
	private function run_evaluation( \Closure $build_context, array $request_tokens = array() ): string {
		$this->last_agent_reason = null;
		$decision                = $this->run_evaluation_guarded( $build_context, $request_tokens );
		// Sin motivo del servidor (válvula offline, avería, servidor antiguo) se deriva una categoría
		// segura: nunca se inventa una regla ni se calla.
		if ( null === $this->last_agent_reason && ( 'BLOCK' === $decision || self::DECISION_UNAVAILABLE === $decision ) ) {
			$this->last_agent_reason = self::DECISION_UNAVAILABLE === $decision
				? array( 'reason_category' => 'checks_unavailable', 'reason' => self::REASON_CHECKS_UNAVAILABLE, 'next_action' => 'wait_and_retry', 'source' => 'plugin_fallback' )
				: array( 'reason_category' => 'merchant_policy', 'reason' => self::REASON_MERCHANT_POLICY, 'next_action' => 'escalate_human', 'source' => 'plugin_fallback' );
		}
		return $decision;
	}

	/**
	 * 2.5.2: motivo del último veredicto BLOCK/UNAVAILABLE (reason_category, reason, next_action y,
	 * si el servidor la dio, rule_code). Null si la última evaluación fue ALLOW.
	 *
	 * @return array<string,string>|null
	 */
	public function get_last_agent_reason(): ?array {
		return $this->last_agent_reason;
	}

	/**
	 * Motivo estructurado del último bloqueo, como datos adicionales de la
	 * RouteException 403 de la Store API (un agente headless los lee; el texto
	 * humano no cambia). El motivo derivado por el propio plugin se declara con
	 * `reason_source`, no se hace pasar por veredicto del servidor.
	 *
	 * @return array<string,mixed>
	 */
	private function block_additional_data(): array {
		$reason = $this->last_agent_reason;
		if ( ! is_array( $reason ) ) {
			return array();
		}
		$data = array();
		foreach ( array( 'reason_category', 'rule_code', 'reason', 'next_action' ) as $key ) {
			if ( isset( $reason[ $key ] ) && is_string( $reason[ $key ] ) && '' !== $reason[ $key ] ) {
				$data[ $key ] = $reason[ $key ];
			}
		}
		foreach ( array( 'human_checkout', 'blocked_item', 'published_limit' ) as $key ) {
			if ( isset( $reason[ $key ] ) && is_array( $reason[ $key ] ) ) {
				$data[ $key ] = $reason[ $key ];
			}
		}
		if ( 'plugin_fallback' === ( $reason['source'] ?? '' ) ) {
			$data['reason_source'] = 'plugin_fallback';
		}
		return $data;
	}

	/** Cuerpo guardado de {@see run_evaluation()}: captura las averías propias. */
	private function run_evaluation_guarded( \Closure $build_context, array $request_tokens = array() ): string {
		// Task 27c (M6): la señal agéntica se lee ANTES (el cuerpo borra la sesión
		// a mitad) y, si ni eso se puede leer, se trata como agente (D1 sólo
		// ampara a la persona probada).
		try {
			$agent_id_probe = WC()->session ? WC()->session->get( Trusteed_Cart_Bridge::SESSION_AGENT_ID ) : null;
			$probe_agentic  = $this->resolve_is_agentic(
				is_string( $agent_id_probe ) && '' !== $agent_id_probe ? $agent_id_probe : null,
				Trusteed_Agent_Identity::resolve_token( WC()->session, $request_tokens )
			);
		} catch ( \Throwable $e ) {
			$probe_agentic = true;
		}

		try {
			return $this->run_evaluation_unguarded( $build_context, $request_tokens );
		} catch ( \Throwable $e ) {
			// Una avería nuestra no aborta el pedido de una persona ni es un permiso
			// para un agente. Sin detalle en el log (el mensaje podría llevar datos
			// de la petición) y sin código de regla: no es «una regla te bloqueó».
			$this->verified_did = '';
			$decision           = Trusteed_Enforcement_Failure_Policy::decide_on_internal_error( $probe_agentic );
			try {
				$this->clear_agent_session();
			} catch ( \Throwable $ignored ) {
				// La decisión no depende de limpiar la sesión.
			}
			error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				sprintf(
					'[amcp.enforcement_internal_error] platform=woocommerce merchant=%s class=%s agentic=%s decision=%s',
					$this->merchant_id,
					get_class( $e ),
					$probe_agentic ? 'true' : 'false',
					$decision
				)
			);
			return 'BLOCK' === $decision ? self::DECISION_UNAVAILABLE : 'ALLOW';
		}
	}

	/**
	 * Cuerpo de {@see run_evaluation()}, sin la guarda de excepciones.
	 *
	 * @param \Closure         $build_context  Builds the orderContext array.
	 * @param array<int,mixed> $request_tokens Agent tokens carried by the request.
	 * @return string 'ALLOW', 'BLOCK' or self::DECISION_UNAVAILABLE.
	 */
	private function run_evaluation_unguarded( \Closure $build_context, array $request_tokens = array() ): string {
		$this->verified_did   = '';
		$this->browser_result = null;
		$order_context        = $build_context();
		$token              = Trusteed_Agent_Identity::resolve_token( WC()->session, $request_tokens );

		// Merchant not connected — nothing to evaluate against. Since 2.3.6
		// (Task 23a) this is `layer_disabled` (configuration, not an outage):
		// nobody is held, an agent neither, like an unconfigured WC-AgenticTools,
		// PrestaShop or Odoo. Logged either way.
		if ( empty( $this->merchant_id ) ) {
			$agent = WC()->session ? WC()->session->get( Trusteed_Cart_Bridge::SESSION_AGENT_ID ) : null;
			$agent = is_string( $agent ) && '' !== $agent ? $agent : null;
			return $this->report_unavailable( 'merchant_id_unset', $agent, $this->resolve_is_agentic( $agent, $token ), false, Trusteed_Enforcement_Failure_Policy::KIND_LAYER_DISABLED, $this->token_present( $token ) );
		}

		$agent_id = WC()->session
			? WC()->session->get( Trusteed_Cart_Bridge::SESSION_AGENT_ID )
			: null;
		if ( empty( $agent_id ) || ! is_string( $agent_id ) ) {
			$agent_id = null;
		}

		// Read BEFORE the session is cleared below (and before the token check).
		$is_agentic = $this->resolve_is_agentic( $agent_id, $token );

		// 2.5.2: agente de navegador con firma RFC 9421. Sólo en `enforce` cambia la
		// clasificación, y sólo con verified/spoofed: unverified, error o timeout siguen
		// como humano (fail-open — una avería de la verificación no reclasifica a nadie).
		$this->browser_result = null !== $this->browser_signature ? $this->browser_signature->resolve( $_SERVER ) : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$browser_enforced     = null !== $this->browser_result && 'enforce' === $this->browser_signature->mode();
		$browser_key          = $browser_enforced && 'verified' === $this->browser_result['status'] ? $this->browser_result['agentId'] : '';
		if ( $browser_enforced && in_array( $this->browser_result['status'], array( 'verified', 'spoofed' ), true ) ) {
			$is_agentic = true;
		}

		// Verify agent token signature and enrich orderContext cartAttributes.
		// No-op when there is no token. 5b: the token is bound to the declared
		// DID; only a VALID token of that same DID yields $this->verified_did.
		$verified           = $this->enrich_with_token_verification( $order_context, $token, (string) $agent_id );
		$order_context      = $verified['context'];
		$declared_did       = $agent_id; // Only for the outage counter (report_unavailable).
		// Decision 2: with the nonce store down the identity is AUTHENTIC (the
		// signature verified) and travels as agentId below, but the order is not
		// attributed (R023 only keeps a DID whose nonce was accepted).
		$this->verified_did = $verified['nonce_unavailable'] ? '' : $verified['verified_did'];

		// Task 23a: the token could not be checked because there is no verified
		// snapshot (the DID resolver travels INSIDE it). Tampered or missing,
		// an agent with a token is held in every mode — before asking the
		// server, so a forged token is never a pass. A person never gets here
		// (no token ⇒ no snapshot lookup).
		if ( null !== $verified['degraded'] ) {
			$this->clear_agent_session();
			return $this->report_unavailable( 'snapshot_' . $verified['degraded'], $declared_did, $is_agentic, false, $verified['degraded'], true );
		}
		// Task 16: only a DID proven by a VALID token travels as agentId (the
		// server keys the agent's history on it); a declared-only DID is an
		// anonymous agent (endpointClass above still says agentic_endpoint).
		$agent_id = '' !== $verified['verified_did'] ? $verified['verified_did'] : null;
		// 2.4.0: pedido de la API de Trusteed — el DID viene del canal firmado, no de la URL.
		if ( null === $agent_id && null !== $this->trusted_agent_did ) {
			$agent_id = $this->trusted_agent_did;
		}
		// 2.5.2: `wba:<origen del Signature-Agent>#<keyId>` es la identidad (spoofed ⇒ agente anónimo).
		if ( null === $agent_id && '' !== $browser_key ) {
			$agent_id           = $browser_key;
			$this->verified_did = $browser_key;
		}

		// Decision 2: the signature verified and only the anti-replay check could
		// not run (our nonce store did not answer). The failure mode decides, as
		// with any outage: `enforce` holds the agent before asking the server
		// (our outage, not "a rule blocked you"); `observe` lets it through with
		// the marker. The identity is kept either way. The server ignores the
		// allowlist when it sees the marker.
		if ( $verified['nonce_unavailable'] && 'BLOCK' === Trusteed_Enforcement_Failure_Policy::decide_unavailable( true, $this->failure_mode, false ) ) {
			$this->clear_agent_session();
			return $this->report_unavailable( 'nonce_store_unavailable', $verified['verified_did'], true, false, Trusteed_Enforcement_Failure_Policy::KIND_EVALUATOR_UNAVAILABLE, true );
		}

		$payload = array(
			'merchantId'     => $this->merchant_id,
			'agentId'        => $agent_id,
			'orderContext'   => $order_context,
			'platform'       => 'WOOCOMMERCE',
			'installationId' => $this->installation_id,
			'timestamp'      => gmdate( DATE_ATOM ),
		);
		// Sólo en agénticos: el payload orgánico queda byte a byte como antes.
		$payload = $is_agentic ? array_merge( $payload, array( 'endpointClass' => 'agentic_endpoint' ) ) : $payload;

		// 2.5.1: sólo la vía de la API de pedidos de Trusteed lleva el nivel; el checkout normal no.
		if ( null !== $this->trusted_identity_level ) {
			$payload['agentIdentityLevel'] = $this->trusted_identity_level;
		} elseif ( '' !== $browser_key && $agent_id === $browser_key ) {
			$payload['agentIdentityLevel'] = 'verified_signature';
		}

		$result = $this->api_client->evaluate( $payload );

		// Clear session regardless of decision to avoid re-checking on retry.
		if ( WC()->session ) {
			WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENT_ID, null );
			WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, null );
			WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN, null );
			WC()->session->set( Trusteed_Agent_Identity::SESSION_TOKEN_REJECTED, null );
		}

		if ( Trusteed_Eval_Outcome::INDETERMINATE === $result->outcome ) {
			// App Store remediation follow-up (2026-07-11) — before falling
			// back to the blunt failure_mode policy (block everything /
			// allow everything), try the offline safety-valve evaluator
			// against the last pulled signed snapshot. This lets a merchant's
			// own universal policy rules (max order amount, blocked
			// countries, business hours, PO-box block, gift-card cap...)
			// still fire during a remote-API outage, instead of either
			// silently letting every cart through or blocking every
			// legitimate human checkout.
			// Task 23a: the valve only runs on a VERIFIED snapshot; without one
			// the cause (tampered / missing) decides and its rules are ignored.
			$kind          = $this->snapshot_degraded_kind() ?? Trusteed_Enforcement_Failure_Policy::KIND_EVALUATOR_UNAVAILABLE;
			$offline_block = Trusteed_Enforcement_Failure_Policy::KIND_EVALUATOR_UNAVAILABLE === $kind
				? $this->try_offline_safety_valve( $order_context, $is_agentic )
				: null;
			if ( null !== $offline_block ) {
				error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
					sprintf(
						'[amcp.offline_safety_valve] BLOCK ruleCode=%s reason=%s (remote evaluate() was INDETERMINATE: %s)',
						$offline_block['ruleCode'],
						$offline_block['reason'],
						$result->reason
					)
				);
				return $this->report_unavailable( $result->reason, $declared_did, $is_agentic, true, $kind, $this->token_present( $token ) );
			}
			return $this->report_unavailable( $result->reason, $declared_did, $is_agentic, false, $kind, $this->token_present( $token ) );
		}

		// R043 HITL — a human-in-the-loop approval is required before the order may
		// complete. Instead of a hard block (which loses the customer's intent) we
		// park the freeze payload so Trusteed_R043_Hitl_Gate::on_order_processed
		// stamps the created order as `wc-pending` (payment NOT captured) pending
		// merchant approval — parity with Magento's quote freeze (is_active=0).
		if (
			Trusteed_Eval_Outcome::BLOCK === $result->outcome
			&& null !== $result->hitl_payload
			&& class_exists( 'Trusteed_R043_Hitl_Gate' )
		) {
			Trusteed_R043_Hitl_Gate::park_in_session( $result->hitl_payload );
			return 'ALLOW'; // Order proceeds → gate freezes it to pending.
		}

		if ( Trusteed_Eval_Outcome::BLOCK !== $result->outcome ) {
			return 'ALLOW';
		}
		$this->last_agent_reason = $result->agent_reason;
		return 'BLOCK';
	}

	/**
	 * App Store remediation follow-up (2026-07-11) — offline safety-valve
	 * fallback. Evaluates the merchant-policy rule subset that needs no
	 * network/DB (R014-country/R018/R019/R020/R025/R027/R028/R029/R030)
	 * against the last pulled signed snapshot's `rules[]`, using ONLY the
	 * cart data already in $order_context. Returns null (no local block —
	 * caller falls through to failure_mode) when the snapshot is
	 * unavailable, has no applicable rules, or the cart passes.
	 *
	 * Since 2.3.6 the rules are first scoped by who is checking out
	 * (Trusteed_Enforcement_Failure_Policy::offline_rules_for): a human only
	 * gets the merchant's `appliesTo: ALL`/`HUMAN` rules (R030 cap, countries…),
	 * never an AGENT rule such as R028/R029.
	 *
	 * @param array $order_context Same shape sent to the remote evaluator.
	 * @param bool  $is_agentic    Checkout carries an agentic signal.
	 * @return array{ruleCode: string, reason: string}|null
	 */
	private function try_offline_safety_valve( array $order_context, bool $is_agentic ): ?array {
		if ( null === $this->snapshot_client || ! method_exists( $this->snapshot_client, 'get_rules' ) ) {
			return null;
		}
		try {
			$rules = $this->snapshot_client->get_rules( $this->merchant_id );
		} catch ( \Throwable $e ) {
			return null;
		}
		if ( empty( $rules ) || ! class_exists( 'Trusteed_Offline_Safety_Valve_Evaluator' ) ) {
			return null;
		}
		$rules = Trusteed_Enforcement_Failure_Policy::offline_rules_for( (array) $rules, $is_agentic );
		if ( empty( $rules ) ) {
			return null;
		}
		$cart_attributes = isset( $order_context['cartAttributes'] ) && is_array( $order_context['cartAttributes'] )
			? $order_context['cartAttributes']
			: array();
		// M1: actor signal for the valve's currency-not-comparable branch (agent => block).
		$order_context['agentCheckout'] = $is_agentic;
		return Trusteed_Offline_Safety_Valve_Evaluator::evaluate( $rules, $order_context, $cart_attributes );
	}

	/**
	 * The server did not answer (INDETERMINATE) or there is nobody to ask
	 * (merchant_id_unset). Decides with the shared policy — a human is never
	 * blocked by this; only an agent gets the failure_mode — and leaves a trace:
	 *   - Structured error_log line (grep-able fallback).
	 *   - POST /v1/agent-events kind=enforcement_indeterminate when an
	 *     event_webhook and a DID are available, so the backend counter
	 *     `enforcement_api_fail_total{platform,reason,mode}` ticks.
	 *
	 * The DID arrives as a parameter: the previous apply_failure_mode() read it
	 * from the session AFTER run_evaluation() had cleared it, so the
	 * `enforcement_indeterminate` telemetry was never emitted.
	 *
	 * @since 2.3.6 (replaces apply_failure_mode(), 1.5.0)
	 *
	 * Since 2.3.6 (Task 23a) the CAUSE decides too
	 * (Trusteed_Enforcement_Failure_Policy::decide_degraded()): a tampered or
	 * missing snapshot holds an agent with a token in every mode; an
	 * unconnected merchant (`layer_disabled`) holds nobody.
	 *
	 * @param string      $reason        Short reason code from API client.
	 * @param string|null $agent_id      DID read before the session was cleared.
	 * @param bool        $is_agentic    Checkout carries an agentic signal.
	 * @param bool        $offline_block The offline safety valve found a blocking rule.
	 * @param string      $kind          Trusteed_Enforcement_Failure_Policy::KIND_*.
	 * @param bool        $token_present The checkout carried an agent token (valid or not).
	 * @return string 'ALLOW', 'BLOCK' (the offline valve: a merchant rule) or self::DECISION_UNAVAILABLE.
	 */
	private function report_unavailable( string $reason, ?string $agent_id, bool $is_agentic, bool $offline_block, string $kind = Trusteed_Enforcement_Failure_Policy::KIND_EVALUATOR_UNAVAILABLE, bool $token_present = false ): string {
		$decision = Trusteed_Enforcement_Failure_Policy::decide_degraded( $kind, $is_agentic, $token_present, $this->failure_mode, $offline_block );
		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			sprintf(
				'[amcp.enforcement_indeterminate] platform=woocommerce merchant=%s mode=%s reason=%s kind=%s agentic=%s decision=%s',
				$this->merchant_id,
				$this->failure_mode,
				$reason,
				$kind,
				$is_agentic ? 'true' : 'false',
				$decision
			)
		);
		if ( null !== $this->event_webhook && null !== $agent_id && '' !== $agent_id ) {
			$this->event_webhook->emit_indeterminate( $agent_id, $reason, $this->failure_mode );
		}
		// 2.3.6 (5b): closing an agent because WE failed is not "a rule blocked
		// you"; only the offline valve (a merchant rule) returns BLOCK here.
		return 'BLOCK' === $decision && ! $offline_block ? self::DECISION_UNAVAILABLE : $decision;
	}

	/**
	 * Read the agent JWS token from session, verify its Ed25519 signature, and
	 * add _agent_token_present / _agent_token_signature_invalid to cartAttributes.
	 *
	 * No token ⇒ no attributes. Task 16: a token that is present but not VALID
	 * (INDETERMINATE, INVALID, another agent's, or no snapshot to check it
	 * against) is `_agent_token_signature_invalid` — an unverifiable token is
	 * an invalid one as far as the agent is concerned.
	 *
	 * 2.3.6 (5b): the token is bound to the declared DID
	 * (Trusteed_Agent_Identity::bind_token): a valid token of ANOTHER agent is
	 * an invalid token (its jti is not consumed); with no declared DID a valid
	 * token supplies its own. A token without JWS shape is an invalid token.
	 * `verified_did` is non-empty only for a VALID token that is not marked
	 * invalid afterwards (replay). Decision 2: with the nonce store down the
	 * signature still verified, so the DID is kept and only marked
	 * `_agent_token_nonce_unavailable`; the caller decides by failure mode.
	 *
	 * @since 1.4.0
	 *
	 * @param array                              $order_context Existing orderContext.
	 * @param array{token:string, rejected:bool} $token         From resolve_token().
	 * @param string                             $declared_did  Declared DID ('' if none).
	 * @return array{context: array, verified_did: string, nonce_unavailable: bool, degraded: ?string} `nonce_unavailable`:
	 *         the signature verified and only the anti-replay check could not run (our nonce store did not answer);
	 *         `degraded`: KIND_* when there was no verified snapshot to check the token (Task 23a).
	 */
	private function enrich_with_token_verification( array $order_context, array $token, string $declared_did ): array {
		$jws_token = $token['token'];
		$none      = array( 'context' => $order_context, 'verified_did' => '', 'nonce_unavailable' => false, 'degraded' => null );

		if ( '' === $jws_token && ! $token['rejected'] ) {
			// No token present — no attributes added. Fail open.
			return $none;
		}

		if ( ! isset( $order_context['cartAttributes'] ) || ! is_array( $order_context['cartAttributes'] ) ) {
			$order_context['cartAttributes'] = array();
		}

		$order_context['cartAttributes']['_agent_token_present'] = 'true';

		if ( $token['rejected'] ) {
			// Presented but not a JWS: an agent's invalid token, never "no token".
			$order_context['cartAttributes']['_agent_token_signature_invalid'] = 'true';
			return array( 'context' => $order_context, 'verified_did' => '', 'nonce_unavailable' => false, 'degraded' => null );
		}

		if ( null === $this->snapshot_client ) {
			// Snapshot client not configured — cannot verify: not VALID (Task 16).
			$order_context['cartAttributes']['_agent_token_signature_invalid'] = 'true';
			return array( 'context' => $order_context, 'verified_did' => '', 'nonce_unavailable' => false, 'degraded' => null );
		}

		// Task 23a: WHY there is no resolver matters. Tampered or missing
		// snapshot ⇒ the token cannot be checked ⇒ `degraded` (run_evaluation
		// holds the agent before asking the server; no jti is consumed).
		$snapshot = $this->snapshot_client->get_snapshot_result( $this->merchant_id );
		if ( Trusteed_Snapshot_Client_Woo::STATUS_OK !== $snapshot['status'] ) {
			$order_context['cartAttributes']['_agent_token_signature_invalid'] = 'true';
			return array(
				'context'           => $order_context,
				'verified_did'      => '',
				'nonce_unavailable' => false,
				'degraded'          => Trusteed_Snapshot_Client_Woo::degraded_kind( $snapshot['status'] ),
			);
		}
		$did_resolver = is_array( $snapshot['payload']['agentDidResolver'] ?? null ) ? $snapshot['payload']['agentDidResolver'] : array();
		$result       = Trusteed_Token_Verifier::verify( $jws_token, $did_resolver, $this->merchant_id );
		$bound        = Trusteed_Agent_Identity::bind_token( $declared_did, $result->state, $result->agent_did );

		if ( 'valid' !== $bound['state'] ) {
			// INVALID, INDETERMINATE (e.g. a DID outside the resolver) or another
			// agent's token: not a proof of anyone (Task 16).
			$order_context['cartAttributes']['_agent_token_signature_invalid'] = 'true';
		}

		if ( $result->is_invalid() ) {
			$order_context['cartAttributes']['_agent_token_signature_invalid'] = 'true';

			// Surface identifier-specific failures as explicit attributes so operators
			// can diagnose tokens minted without a single-use identifier (spec-048
			// P2.8). The names say `jti` for compatibility with the server; since B5
			// they cover the canonical `nonce` (or the legacy `jti` alias).
			if ( 'missing_jti' === $result->error ) {
				$order_context['cartAttributes']['_agent_token_jti_missing'] = 'true';
			} elseif ( 'bad_jti' === $result->error ) {
				$order_context['cartAttributes']['_agent_token_jti_malformed'] = 'true';
			}
		}

		// Spec-048 P2.8 — single-use replay protection. Only consume nonces for
		// VALID tokens (INDETERMINATE / INVALID are already handled above) that
		// belong to the declared agent (5b: never burn another agent's jti).
		$nonce_unavailable = false;
		if ( 'valid' === $bound['state'] && '' !== $result->jti ) {
			$nonce_outcome = $this->api_client->consume_nonce(
				$this->merchant_id,
				$result->agent_did,
				$result->jti,
				$result->exp
			);

			if ( Trusteed_Nonce_Outcome::REPLAY === $nonce_outcome->outcome ) {
				// Token reused — downgrade to INVALID and tag attributes so the rule
				// evaluator can reject (R002 / R005 etc.) and ops can alert.
				$order_context['cartAttributes']['_agent_token_signature_invalid'] = 'true';
				$order_context['cartAttributes']['_agent_token_replay']            = 'true';
			} elseif ( Trusteed_Nonce_Outcome::INDETERMINATE === $nonce_outcome->outcome ) {
				// Decision 2: the signature verified, so the token stays VALID and the
				// DID is authentic; only replay could not be checked. Mark it (the
				// server ignores the allowlist with this marker) and let the caller
				// decide by failure mode.
				$order_context['cartAttributes']['_agent_token_nonce_unavailable'] = 'true';
				$nonce_unavailable = true;
				error_log( '[amcp] nonce-consume indeterminate (' . $this->failure_mode . '): ' . $nonce_outcome->reason ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			}
			// ACCEPTED → nothing to do, token remains VALID.
		}

		// R004: first-seen key age tracking via WP transients (persistent across requests).
		// Only for a VALID token (Task 16): an unproven or another agent's key must not age this one.
		if ( '' !== $result->kid && 'valid' === $bound['state'] ) {
			$transient_key = 'trusteed_kid_fs_' . hash( 'sha256', $result->kid );
			$first_seen    = get_transient( $transient_key );
			if ( false === $first_seen ) {
				$first_seen = time();
				set_transient( $transient_key, $first_seen, YEAR_IN_SECONDS );
			}
			$order_context['cartAttributes']['_agent_key_age_hours'] = (string) round( ( time() - (int) $first_seen ) / 3600, 2 );
		}

		$verified = 'valid' === $bound['state'] && ! isset( $order_context['cartAttributes']['_agent_token_signature_invalid'] );
		return array(
			'context'           => $order_context,
			'verified_did'      => $verified ? $bound['did'] : '',
			'nonce_unavailable' => $nonce_unavailable,
			'degraded'          => null,
		);
	}

	/**
	 * Task 23a: kind of the snapshot failure, or null with a verified snapshot
	 * (or with no snapshot client, which only a test builds).
	 *
	 * @return string|null Trusteed_Enforcement_Failure_Policy::KIND_*.
	 */
	private function snapshot_degraded_kind(): ?string {
		try {
			return null === $this->snapshot_client ? null : Trusteed_Snapshot_Client_Woo::degraded_kind( $this->snapshot_client->get_snapshot_result( $this->merchant_id )['status'] );
		} catch ( \Throwable $e ) {
			return Trusteed_Enforcement_Failure_Policy::KIND_MISSING_SNAPSHOT;
		}
	}

	/**
	 * @param array{token:string, rejected:bool} $token From resolve_token().
	 * @return bool The checkout carried an agent token (valid or not).
	 */
	private function token_present( array $token ): bool {
		return '' !== $token['token'] || $token['rejected'];
	}

	/** Same session cleanup as after evaluate(), for the early return of Task 23a. */
	private function clear_agent_session(): void {
		if ( WC()->session ) {
			WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENT_ID, null );
			WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENT_TOKEN, null );
			WC()->session->set( Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN, null );
			WC()->session->set( Trusteed_Agent_Identity::SESSION_TOKEN_REJECTED, null );
		}
	}

	/**
	 * Build orderContext from classic checkout $_POST data.
	 *
	 * @since 1.3.0
	 *
	 * @return array orderContext payload.
	 */
	private function get_order_context_from_post(): array {
		$cart     = WC()->cart;
		$currency = get_woocommerce_currency();

		$total_cents = (int) round( (float) $cart->get_total( 'edit' ) * 100 );

		$line_items       = array();
		$product_cat_slugs = array();
		foreach ( $cart->get_cart() as $item ) {
			$product = $item['data'];
			if ( ! $product instanceof \WC_Product ) {
				continue;
			}
			$line_items[] = array(
				'id'         => (string) $item['product_id'],
				'qty'        => (int) $item['quantity'],
				'priceCents' => (int) round( (float) $product->get_price() * 100 ),
			);
			// Collect server-side product categories (R012 high-risk-category enforcement).
			foreach ( Trusteed_Cart_Signals::product_term_slugs( (int) $item['product_id'] ) as $slug ) {
				$product_cat_slugs[ $slug ] = true;
			}
		}

		$context = array(
			'cartTotalCents' => $total_cents,
			'currency'       => strtoupper( $currency ),
			'itemCount'      => (int) $cart->get_cart_contents_count(),
			'discountCodes'  => array_values( array_keys( WC()->cart->get_applied_coupons() ) ),
			'lineItems'      => $line_items,
		);

		if ( ! empty( $product_cat_slugs ) ) {
			$context['cartAttributes']['_product_categories'] = implode( ',', array_keys( $product_cat_slugs ) );
			// M1 (audit): declare the platform so the enforcement engine maps native
			// WooCommerce term slugs (wine/spirits) to canonical taxonomy (alcohol)
			// for R006/R012/R032. Without it those rules silently raw-match only.
			$context['cartAttributes']['_product_platform'] = 'woocommerce';
		}

		if ( ! empty( $_POST['shipping_country'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$sc = strtoupper( sanitize_text_field( wp_unslash( $_POST['shipping_country'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
			if ( 2 === strlen( $sc ) ) {
				$context['shippingCountry'] = $sc;
			}
		}

		if ( ! empty( $_POST['billing_country'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$bc = strtoupper( sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
			if ( 2 === strlen( $bc ) ) {
				$context['billingCountry'] = $bc;
			}
		}

		if ( ! empty( $_POST['payment_method'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$context['paymentMethod'] = sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		// B6 — R015/R016/R025/R026/R027/R028 signal injection.
		$context = $this->apply_cart_signals_from_cart( $cart, $context );

		// B6 — PO box / freight forwarder from POST address fields.
		$ship_a1 = isset( $_POST['shipping_address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['shipping_address_1'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$ship_a2 = isset( $_POST['shipping_address_2'] ) ? sanitize_text_field( wp_unslash( $_POST['shipping_address_2'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$ship_co = isset( $_POST['shipping_company'] )   ? sanitize_text_field( wp_unslash( $_POST['shipping_company'] ) )   : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$bill_co = isset( $_POST['billing_company'] )    ? sanitize_text_field( wp_unslash( $_POST['billing_company'] ) )    : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$context = $this->apply_address_signals( $context, $ship_a1, $ship_a2, $ship_co, $bill_co );

		return $context;
	}

	/**
	 * Build orderContext from a WC_Order and Store API request (blocks checkout).
	 *
	 * @since 1.3.0
	 *
	 * @param \WC_Order        $order   The WC order object.
	 * @param \WP_REST_Request $request The Store API request.
	 * @return array orderContext payload.
	 */
	private function build_order_context_from_order( $order, $request ): array {
		$currency    = $order->get_currency();
		$total_cents = (int) round( (float) $order->get_total() * 100 );

		$line_items        = array();
		$item_count        = 0;
		$product_cat_slugs = array();
		foreach ( $order->get_items() as $item ) {
			$product_id = (int) $item->get_product_id();
			$qty        = (int) $item->get_quantity();
			$subtotal   = (float) $item->get_subtotal();
			$unit_cents = $qty > 0 ? (int) round( ( $subtotal / $qty ) * 100 ) : 0;

			$line_items[] = array(
				'id'         => (string) $product_id,
				'qty'        => $qty,
				'priceCents' => $unit_cents,
			);
			$item_count  += $qty;

			// Collect server-side product categories (R012 high-risk-category enforcement).
			foreach ( Trusteed_Cart_Signals::product_term_slugs( $product_id ) as $slug ) {
				$product_cat_slugs[ $slug ] = true;
			}
		}

		$coupon_codes = array();
		foreach ( $order->get_coupon_codes() as $code ) {
			$coupon_codes[] = $code;
		}

		$context = array(
			'cartTotalCents' => $total_cents,
			'currency'       => strtoupper( $currency ),
			'itemCount'      => $item_count,
			'discountCodes'  => $coupon_codes,
			'lineItems'      => $line_items,
		);

		if ( ! empty( $product_cat_slugs ) ) {
			$context['cartAttributes']['_product_categories'] = implode( ',', array_keys( $product_cat_slugs ) );
			// M1 (audit): declare the platform so the enforcement engine maps native
			// WooCommerce term slugs (wine/spirits) to canonical taxonomy (alcohol)
			// for R006/R012/R032. Without it those rules silently raw-match only.
			$context['cartAttributes']['_product_platform'] = 'woocommerce';
		}

		$billing_country = $order->get_billing_country();
		if ( ! empty( $billing_country ) ) {
			$context['billingCountry'] = strtoupper( $billing_country );
		}

		$shipping_country = $order->get_shipping_country();
		if ( ! empty( $shipping_country ) ) {
			$context['shippingCountry'] = strtoupper( $shipping_country );
		}

		$payment_method = $order->get_payment_method();
		if ( ! empty( $payment_method ) ) {
			$context['paymentMethod'] = $payment_method;
		}

		// B6 — R015/R016/R025/R026/R027/R028 signal injection.
		$context = $this->apply_cart_signals_from_order( $order, $context );
		$context = $this->apply_address_signals(
			$context,
			(string) $order->get_shipping_address_1(),
			(string) $order->get_shipping_address_2(),
			(string) $order->get_shipping_company(),
			(string) $order->get_billing_company()
		);

		return $context;
	}

	/**
	 * B6 — inject R015/R016/R026/R027 cartAttributes from a WC_Cart.
	 *
	 * @param \WC_Cart $cart    Active cart.
	 * @param array    $context Existing orderContext.
	 * @return array
	 */
	private function apply_cart_signals_from_cart( $cart, array $context ): array {
		$items = array();
		foreach ( $cart->get_cart() as $cart_item ) {
			$product = $cart_item['data'] ?? null;
			if ( ! $product instanceof \WC_Product ) {
				continue;
			}
			$items[] = $this->wc_product_to_signal( $product, (int) ( $cart_item['quantity'] ?? 1 ) );
		}
		return $this->merge_signals( $context, $items, (int) ( $cart->id ?? 0 ) );
	}

	/**
	 * B6 — inject R015/R016/R026/R027 cartAttributes from a WC_Order.
	 *
	 * @param \WC_Order $order   Order being placed.
	 * @param array     $context Existing orderContext.
	 * @return array
	 */
	private function apply_cart_signals_from_order( $order, array $context ): array {
		$items = array();
		foreach ( $order->get_items() as $item ) {
			$product = method_exists( $item, 'get_product' ) ? $item->get_product() : null;
			if ( ! $product instanceof \WC_Product ) {
				continue;
			}
			$items[] = $this->wc_product_to_signal( $product, (int) $item->get_quantity() );
		}
		return $this->merge_signals( $context, $items, (int) $order->get_id() );
	}

	/**
	 * Convert a WC_Product + quantity into the primitive signal dict used by
	 * Trusteed_Cart_Signals helpers. Pure data — no further WC calls.
	 *
	 * @param \WC_Product $product Product object.
	 * @param int         $qty     Line quantity.
	 * @return array{id:string,type:string,price_cents:int,qty:int,stock:int|null,tags:string[],categories:string[]}
	 */
	private function wc_product_to_signal( $product, int $qty ): array {
		$pid    = (int) $product->get_id();
		$type   = (string) $product->get_type();
		$price  = (int) round( (float) $product->get_price() * 100 );
		$stock  = $product->managing_stock() ? (int) $product->get_stock_quantity() : null;
		$tags   = wp_get_post_terms( $pid, 'product_tag', array( 'fields' => 'slugs' ) );
		$cats   = wp_get_post_terms( $pid, 'product_cat', array( 'fields' => 'slugs' ) );
		return array(
			'id'          => (string) $pid,
			'type'        => $type,
			'price_cents' => $price,
			'qty'         => max( 1, $qty ),
			'stock'       => $stock,
			'tags'        => is_array( $tags ) ? array_values( $tags ) : array(),
			'categories'  => is_array( $cats ) ? array_values( $cats ) : array(),
		);
	}

	/**
	 * Merge per-item primitive signals into orderContext.cartAttributes via
	 * the pure-function helpers in Trusteed_Cart_Signals.
	 *
	 * R015 price-snap verification: reads the per-merchant HMAC key from the
	 * latest signed snapshot (R015.params.priceSnapHmacKeyHex). Cookie
	 * `amcp_price_snap_{cart_id}` carries the envelope written at add-to-cart
	 * time. Missing key / cookie / invalid HMAC → R015 simply has no evidence.
	 *
	 * @param array $context Existing orderContext.
	 * @param array $items   Primitive item signal dicts.
	 * @param int   $cart_id Numeric cart identifier (used for the price-snap cookie key).
	 * @return array
	 */
	private function merge_signals( array $context, array $items, int $cart_id ): array {
		if ( empty( $items ) ) {
			return $context;
		}
		if ( ! isset( $context['cartAttributes'] ) || ! is_array( $context['cartAttributes'] ) ) {
			$context['cartAttributes'] = array();
		}

		$lowest = Trusteed_Cart_Signals::lowest_stock( $items );
		if ( $lowest !== null ) {
			$context['cartAttributes']['_lowest_stock'] = (string) $lowest;
		}

		if ( Trusteed_Cart_Signals::cart_has_subscription( $items ) ) {
			$context['cartAttributes']['_subscription'] = 'true';
		}

		$stored = Trusteed_Cart_Signals::stored_value_cents( $items );
		if ( $stored > 0 ) {
			$context['cartAttributes']['_stored_value_cents'] = (string) $stored;
		}

		// R015 — verify HMAC-signed price snapshot cookie if present.
		// NOTE: the cookie name intentionally keeps the legacy `amcp_price_snap_`
		// prefix. Its setter lives in front-end/edge code outside this repo, so
		// renaming the read side here would silently break price-snap evidence
		// for existing storefronts with no way to verify. Left as-is by design.
		$hmac_key = $this->resolve_r015_hmac_key();
		$cookie   = isset( $_COOKIE[ 'amcp_price_snap_' . $cart_id ] ) ? (string) $_COOKIE[ 'amcp_price_snap_' . $cart_id ] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification
		if ( $cookie !== '' && $hmac_key !== '' ) {
			$snapshot = Trusteed_Cart_Signals::verify_price_snap( $cookie, $hmac_key );
			if ( ! empty( $snapshot ) ) {
				$current = array();
				foreach ( $items as $signal ) {
					$current[ (string) $signal['id'] ] = (int) $signal['price_cents'];
				}
				$delta = Trusteed_Cart_Signals::max_price_delta_bps( $current, $snapshot );
				if ( $delta > 0 ) {
					$context['cartAttributes']['_price_delta_bps'] = (string) $delta;
				}
			}
		}

		return $context;
	}

	/**
	 * B6 — inject R025 + R028 cartAttributes derived from address fields.
	 *
	 * @param array  $context Existing orderContext.
	 * @param string $ship_a1 Shipping address line 1.
	 * @param string $ship_a2 Shipping address line 2.
	 * @param string $ship_co Shipping company (used as freight-forwarder probe).
	 * @param string $bill_co Billing company (primary B2B signal).
	 * @return array
	 */
	private function apply_address_signals( array $context, string $ship_a1, string $ship_a2, string $ship_co, string $bill_co ): array {
		if ( ! isset( $context['cartAttributes'] ) || ! is_array( $context['cartAttributes'] ) ) {
			$context['cartAttributes'] = array();
		}

		if ( Trusteed_Cart_Signals::detect_po_box( $ship_a1, $ship_a2 ) ) {
			$context['cartAttributes']['_shipping_po_box'] = 'true';
		}
		if ( Trusteed_Cart_Signals::detect_freight_forwarder( $ship_a1, $ship_a2, $ship_co ) ) {
			$context['cartAttributes']['_shipping_freight_forwarder'] = 'true';
		}

		$user_id = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0;
		$roles   = array();
		if ( $user_id > 0 && function_exists( 'wp_get_current_user' ) ) {
			$user = wp_get_current_user();
			if ( $user && isset( $user->roles ) && is_array( $user->roles ) ) {
				$roles = $user->roles;
			}
		}
		if ( Trusteed_Cart_Signals::is_b2b_order( $bill_co, $user_id, $roles ) ) {
			$context['cartAttributes']['_b2b_order'] = 'true';
		}

		return $context;
	}

	/**
	 * B6 — read per-merchant R015 HMAC key from the latest signed snapshot.
	 * Returns '' when the snapshot client is absent or R015 is not configured.
	 */
	private function resolve_r015_hmac_key(): string {
		if ( null === $this->snapshot_client || ! method_exists( $this->snapshot_client, 'get_rules' ) ) {
			return '';
		}
		try {
			$rules = $this->snapshot_client->get_rules( $this->merchant_id );
		} catch ( \Throwable $e ) {
			return '';
		}
		if ( ! is_array( $rules ) ) {
			return '';
		}
		foreach ( $rules as $rule ) {
			if ( ! is_array( $rule ) ) {
				continue;
			}
			$code = (string) ( $rule['ruleCode'] ?? '' );
			if ( $code !== 'R015' && strpos( $code, 'R015.' ) !== 0 ) {
				continue;
			}
			$params = $rule['params'] ?? array();
			if ( is_array( $params ) && ! empty( $params['priceSnapHmacKeyHex'] ) ) {
				return (string) $params['priceSnapHmacKeyHex'];
			}
		}
		return '';
	}
}
