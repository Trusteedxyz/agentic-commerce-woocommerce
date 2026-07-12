<?php
/**
 * Offline safety-valve evaluator — local PHP port of the "universal merchant
 * policy" rule subset from `packages/shared/src/enforcement/rule-catalog.ts`
 * (R014-country-only, R018, R019, R020, R025, R027, R028, R029, R030).
 *
 * Why this exists: WooCommerce currently has ZERO local per-rule evaluator —
 * every ALLOW/BLOCK verdict comes from the single remote
 * `/v1/rules/evaluate` call (`Amcp_Checkout_Enforcer::run_evaluation()`). If
 * that call fails (network error, timeout, 5xx), the merchant's own safety
 * rules (max order amount, blocked countries, business hours, PO-box block,
 * gift-card cap...) simply never get a chance to fire — the checkout falls
 * through to the blunt `failure_mode` policy (block everything / allow
 * everything) instead.
 *
 * This class is a FALLBACK, not a replacement: the remote evaluator stays
 * primary (it has richer history/DB signal for rules outside this subset).
 * This only runs when the remote call is unavailable, evaluating the last
 * pulled signed RuleSnapshot's `rules[]` params against the current cart —
 * pure functions, no network, no DB.
 *
 * CONFORMANCE: every method here MUST match the behavior of its TypeScript
 * counterpart in rule-catalog.ts EXACTLY. Both are tested against the same
 * fixture: `packages/shared/src/enforcement/__fixtures__/
 * offline-safety-valve-conformance.json`. If you change one, update the
 * fixture and re-run BOTH test suites (TS + this plugin's PHPUnit) before
 * shipping — cross-language drift here is exactly the class of bug the
 * 2026-07-11 App Store remediation spent a whole session finding and fixing.
 *
 * @since 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Amcp_Offline_Safety_Valve_Evaluator {

	private const DEFAULT_HIGH_RISK_COUNTRIES = array( 'KP', 'IR', 'SY', 'CU' );

	/**
	 * Evaluate the offline-capable rule subset against a pulled snapshot's
	 * rules array + the current cart. Returns the FIRST rule that blocks
	 * (first-match-wins, mirroring the shared TS evaluator's ordering
	 * semantics is not required here — a merchant should never have two
	 * safety-valve rules conflict), or null when everything passes.
	 *
	 * @param array $rules         Snapshot `rules[]` — each `{ruleCode, enabled, params}`.
	 * @param array $order_context `{cartTotalCents, itemCount, billingCountry?, shippingCountry?, lineItems?}`.
	 * @param array $cart_attributes String-keyed map (already-stringified values, matching the wire cart-attribute contract).
	 * @return array{ruleCode:string,reason:string}|null
	 */
	public static function evaluate( array $rules, array $order_context, array $cart_attributes ): ?array {
		$dispatch = array(
			'R014' => array( self::class, 'eval_r014' ),
			'R018' => array( self::class, 'eval_r018' ),
			'R019' => array( self::class, 'eval_r019' ),
			'R020' => array( self::class, 'eval_r020' ),
			'R025' => array( self::class, 'eval_r025' ),
			'R027' => array( self::class, 'eval_r027' ),
			'R028' => array( self::class, 'eval_r028' ),
			'R029' => array( self::class, 'eval_r029' ),
			'R030' => array( self::class, 'eval_r030' ),
		);

		foreach ( $rules as $rule ) {
			if ( ! is_array( $rule ) || empty( $rule['enabled'] ) ) {
				continue;
			}
			$rule_code = self::short_code( (string) ( $rule['ruleCode'] ?? '' ) );
			if ( ! isset( $dispatch[ $rule_code ] ) ) {
				continue;
			}
			$params = (array) ( $rule['params'] ?? array() );
			$reason = call_user_func( $dispatch[ $rule_code ], $params, $order_context, $cart_attributes );
			if ( null !== $reason ) {
				return array( 'ruleCode' => $rule_code, 'reason' => $reason );
			}
		}

		return null;
	}

	/** Strip a canonical dotted suffix ("R030.simple-controls" -> "R030"). */
	private static function short_code( string $rule_code ): string {
		$dot = strpos( $rule_code, '.' );
		return false === $dot ? $rule_code : substr( $rule_code, 0, $dot );
	}

	private static function order_country( array $order_context ): ?string {
		return $order_context['billingCountry'] ?? $order_context['shippingCountry'] ?? null;
	}

	private static function attr_bool( array $cart_attributes, string $key ): bool {
		$v = $cart_attributes[ $key ] ?? null;
		return 'true' === $v || '1' === $v;
	}

	private static function attr_number( array $cart_attributes, string $key ) {
		if ( ! array_key_exists( $key, $cart_attributes ) ) {
			return null;
		}
		if ( ! is_numeric( $cart_attributes[ $key ] ) ) {
			return null;
		}
		return (float) $cart_attributes[ $key ];
	}

	// ── R014 (country dimension only — cancellation-history dimension needs a DB lookup, not offline-capable) ──

	private static function eval_r014( array $params, array $order_context, array $cart_attributes ): ?string {
		$high_risk = $params['highRiskCountries'] ?? self::DEFAULT_HIGH_RISK_COUNTRIES;
		$country   = self::order_country( $order_context );
		if ( null !== $country && in_array( $country, $high_risk, true ) ) {
			return "delivery country {$country} is high-risk";
		}
		return null;
	}

	// ── R018 cart-composition-guard ──

	private static function eval_r018( array $params, array $order_context, array $cart_attributes ): ?string {
		$avg  = $params['merchantAvgOrderCents'] ?? null;
		$mult = $params['spikeMultiplier'] ?? 5.0;
		$cart_total_cents = (int) ( $order_context['cartTotalCents'] ?? 0 );

		if ( null !== $avg && $avg > 0 ) {
			$spike = $cart_total_cents / $avg;
			if ( $spike > $mult ) {
				return sprintf( 'cart %d is %.1fx avg %d', $cart_total_cents, $spike, $avg );
			}
		}

		$max_item_count = $params['maxItemCount'] ?? null;
		$item_count     = (int) ( $order_context['itemCount'] ?? 0 );
		if ( null !== $max_item_count && $item_count > $max_item_count ) {
			return "item count {$item_count} > {$max_item_count}";
		}

		$max_qty = $params['maxSingleSkuQty'] ?? null;
		if ( null !== $max_qty ) {
			foreach ( (array) ( $order_context['lineItems'] ?? array() ) as $line ) {
				if ( ( (int) ( $line['qty'] ?? 0 ) ) > $max_qty ) {
					return "line item quantity exceeds {$max_qty}";
				}
			}
		}

		return null;
	}

	// ── R019 country-jurisdiction ──

	private static function eval_r019( array $params, array $order_context, array $cart_attributes ): ?string {
		$country = self::order_country( $order_context );
		if ( null === $country ) {
			return null; // NO_SIGNAL — not a block.
		}
		$blocked = $params['blockedCountries'] ?? array();
		if ( in_array( $country, $blocked, true ) ) {
			return "country {$country} is blocked";
		}
		$allowed = $params['allowedCountries'] ?? array();
		if ( ! empty( $allowed ) && ! in_array( $country, $allowed, true ) ) {
			return "country {$country} is not allowed";
		}
		return null;
	}

	// ── R020 business-hours ──

	private static function eval_r020( array $params, array $order_context, array $cart_attributes ): ?string {
		$start = $params['startHour'] ?? null;
		$end   = $params['endHour'] ?? null;
		if ( null === $start || null === $end ) {
			return null; // NO_SIGNAL — not configured.
		}

		$hour = self::attr_number( $cart_attributes, '_merchant_local_hour' );
		if ( null === $hour ) {
			$tz = $params['timezone'] ?? 'UTC';
			try {
				$now  = new DateTime( 'now', new DateTimeZone( (string) $tz ) );
				$hour = (int) $now->format( 'G' );
			} catch ( Exception $e ) {
				return null; // invalid timezone — fail-open, matches TS behavior.
			}
		}

		$inside = $start <= $end
			? ( $hour >= $start && $hour < $end )
			: ( $hour >= $start || $hour < $end );

		return $inside ? null : "local hour {$hour} outside {$start}-{$end}";
	}

	// ── R025 sensitive-delivery-address ──

	private static function eval_r025( array $params, array $order_context, array $cart_attributes ): ?string {
		$block_po_box = $params['blockPoBox'] ?? true;
		if ( $block_po_box && self::attr_bool( $cart_attributes, '_shipping_po_box' ) ) {
			return 'sensitive PO box delivery address';
		}
		$block_freight = $params['blockFreightForwarder'] ?? true;
		if ( $block_freight && self::attr_bool( $cart_attributes, '_shipping_freight_forwarder' ) ) {
			return 'freight-forwarder delivery address';
		}
		return null;
	}

	// ── R027 gift-card-stored-value ──

	private static function eval_r027( array $params, array $order_context, array $cart_attributes ): ?string {
		$raw = self::attr_number( $cart_attributes, '_stored_value_cents' );
		if ( null === $raw ) {
			return null; // NO_SIGNAL.
		}
		$max = $params['maxStoredValueCents'] ?? 0;
		return $raw > $max ? "stored value {$raw} > {$max}" : null;
	}

	// ── R028 b2b-po-guard ──

	private static function eval_r028( array $params, array $order_context, array $cart_attributes ): ?string {
		if ( isset( $params['requirePurchaseOrder'] ) && false === $params['requirePurchaseOrder'] ) {
			return null;
		}
		$is_b2b     = self::attr_bool( $cart_attributes, '_b2b_order' );
		$has_po_hash = array_key_exists( '_purchase_order_hash', $cart_attributes );
		return ( $is_b2b && ! $has_po_hash ) ? 'B2B purchase order evidence missing' : null;
	}

	// ── R029 merchant-preset ──

	private static function eval_r029( array $params, array $order_context, array $cart_attributes ): ?string {
		$preset = $params['preset'] ?? 'equilibrado';
		if ( 'abierto' === $preset ) {
			return null;
		}
		if ( 'regulado' === $preset && ! self::attr_bool( $cart_attributes, '_regulated_evidence_present' ) ) {
			return 'regulated preset requires additional merchant evidence';
		}
		if ( 'estricto' === $preset ) {
			// Offline evaluator has no agent context (organic checkout by
			// definition here) — "estricto" REQUIRES a verified high-trust
			// agent by design, so this correctly HITs for every organic cart.
			return 'strict preset requires verified high-trust agent';
		}
		return null;
	}

	// ── R030 simple-controls ──

	private static function eval_r030( array $params, array $order_context, array $cart_attributes ): ?string {
		$max = $params['maxAmountCents'] ?? null;
		$cart_total_cents = (int) ( $order_context['cartTotalCents'] ?? 0 );
		if ( null !== $max && $cart_total_cents > $max ) {
			return "cart total {$cart_total_cents} > merchant max {$max}";
		}
		$country = self::order_country( $order_context );
		$allowed = $params['allowedCountries'] ?? array();
		if ( null !== $country && ! empty( $allowed ) && ! in_array( $country, $allowed, true ) ) {
			return "country {$country} is outside merchant controls";
		}
		return null;
	}
}
