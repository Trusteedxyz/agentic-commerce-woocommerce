<?php
/**
 * AgenticMCP Stores — R043 HITL Gate / Compuerta HITL R043 (Spec 048 Sprint E.2 T-E45).
 *
 * Cart-freeze hook for `R043.agent-checkout-approval-required`. When the
 * /v1/rules/evaluate response signals HITL (decision=BLOCK +
 * ucp.state=requires_escalation + reason_code prefix `trusteed:R043`), this
 * gate parks the order in `wc-pending` status with HITL metadata so the
 * merchant dashboard can later resolve approve/reject without losing the
 * customer's intent.
 *
 * Detection contract (matches buildHitlResponse in
 * packages/shared/src/enforcement/rule-evaluator.service.ts):
 *   - response.decision === 'BLOCK'
 *   - response.ucp.state === 'requires_escalation'
 *   - response.ucp.reason_code === 'trusteed:R043.agent-checkout-approval-required'
 *
 * Compuerta de carrito-congelado para R043. Cuando la respuesta `/v1/rules/evaluate`
 * indica HITL, aparca el pedido en estado `wc-pending` con metadata HITL para
 * que el comerciante pueda aprobar/rechazar después sin perder la intención.
 *
 * @package AgenticMCP_Stores
 * @since   1.6.0 (spec-048 Sprint E.2 T-E45)
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'AgenticMCP_R043_Hitl_Gate' ) ) {

	/**
	 * Detects R043 HITL outcomes from enforcement responses and freezes the cart.
	 */
	class AgenticMCP_R043_Hitl_Gate {

		public const R043_REASON_PREFIX  = 'trusteed:R043';
		public const SESSION_HITL_PENDING = 'amcp_hitl_pending_payload';
		public const ORDER_META_HITL     = '_amcp_hitl_pending';
		public const ORDER_META_RULE     = '_amcp_hitl_rule_code';
		public const ORDER_META_REASON   = '_amcp_hitl_reason';
		public const ORDER_META_EVAL_ID  = '_amcp_hitl_evaluation_id';

		/**
		 * Returns true if the evaluation response signals R043 HITL.
		 *
		 * @param array|object $response Raw decoded JSON from /v1/rules/evaluate.
		 * @return bool
		 */
		public static function is_hitl_response( $response ): bool {
			$arr = is_object( $response ) ? get_object_vars( $response ) : (array) $response;
			if ( ! isset( $arr['decision'] ) || 'BLOCK' !== $arr['decision'] ) {
				return false;
			}
			$ucp = $arr['ucp'] ?? array();
			if ( is_object( $ucp ) ) {
				$ucp = get_object_vars( $ucp );
			}
			if ( ! is_array( $ucp ) ) {
				return false;
			}
			$state = $ucp['state'] ?? '';
			$code  = $ucp['reason_code'] ?? '';
			if ( 'requires_escalation' !== $state ) {
				return false;
			}
			return is_string( $code ) && 0 === strncmp( $code, self::R043_REASON_PREFIX, strlen( self::R043_REASON_PREFIX ) );
		}

		/**
		 * Extracts the rule code from a HITL response (e.g. "R043.agent-checkout-approval-required").
		 *
		 * @param array|object $response
		 * @return string Empty string when not parseable.
		 */
		public static function rule_code_from( $response ): string {
			$arr = is_object( $response ) ? get_object_vars( $response ) : (array) $response;
			$ucp = $arr['ucp'] ?? array();
			if ( is_object( $ucp ) ) {
				$ucp = get_object_vars( $ucp );
			}
			$code = ( is_array( $ucp ) && isset( $ucp['reason_code'] ) ) ? (string) $ucp['reason_code'] : '';
			return '' !== $code && 0 === strncmp( $code, 'trusteed:', 9 ) ? substr( $code, 9 ) : '';
		}

		/**
		 * Persists a HITL pending payload to the WC session so the
		 * `woocommerce_checkout_order_processed` hook can stamp the order.
		 *
		 * @param array        $payload Sub-set of the evaluation response:
		 *                              ['rule_code','reason','evaluation_id'].
		 */
		public static function park_in_session( array $payload ): void {
			if ( function_exists( 'WC' ) && WC()->session ) {
				WC()->session->set( self::SESSION_HITL_PENDING, $payload );
			}
		}

		/**
		 * `woocommerce_checkout_order_processed` action handler. If a HITL payload
		 * was parked in session by the enforcer, stamp the order with metadata
		 * and force `wc-pending` so payment is not captured automatically.
		 *
		 * @param int $order_id WC order id (legacy positional arg).
		 */
		public static function on_order_processed( $order_id ): void {
			if ( ! function_exists( 'wc_get_order' ) || ! function_exists( 'WC' ) || ! WC()->session ) {
				return;
			}
			$payload = WC()->session->get( self::SESSION_HITL_PENDING );
			if ( empty( $payload ) || ! is_array( $payload ) ) {
				return;
			}
			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				return;
			}
			$order->update_meta_data( self::ORDER_META_HITL, '1' );
			$order->update_meta_data( self::ORDER_META_RULE, (string) ( $payload['rule_code'] ?? '' ) );
			$order->update_meta_data( self::ORDER_META_REASON, (string) ( $payload['reason'] ?? '' ) );
			$order->update_meta_data( self::ORDER_META_EVAL_ID, (string) ( $payload['evaluation_id'] ?? '' ) );
			$order->set_status( 'pending', __( 'Agent checkout requires merchant approval (R043 HITL).', 'agenticmcpstores' ) );
			$order->save();

			WC()->session->set( self::SESSION_HITL_PENDING, null );
		}

		/**
		 * Plugin bootstrap — registers the hook. Idempotent (multiple calls are safe).
		 */
		public static function register(): void {
			if ( ! function_exists( 'add_action' ) ) {
				return;
			}
			add_action(
				'woocommerce_checkout_order_processed',
				array( __CLASS__, 'on_order_processed' ),
				20,
				1
			);
		}
	}
}
