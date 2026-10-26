<?php
/**
 * Puerto de producción de los endpoints de pedidos de agente: la parte que habla
 * con WooCommerce (ver Trusteed_Agent_Orders_Endpoint para el contrato).
 *
 * Decisión de diseño del presupuesto (/quote): se calcula con el CARRITO de Woo
 * (`wc_load_cart()` + `WC()->cart`), no con un `WC_Order` temporal. Es el único
 * camino por el que pasan, con el motor real y en el orden real, las tarifas de
 * envío (incluidos los métodos que consultan el carrito, como el envío gratis por
 * importe mínimo o por cupón), los cupones, los impuestos del envío y los plugins
 * de terceros que enganchan `woocommerce_cart_*`. Un `WC_Order` sin persistir no
 * tiene paquetes de envío y obligaría a reimplementar esa lógica.
 * El coste de ese camino es la sesión: en una petición REST sin cookie, el carrito
 * crea una sesión de cliente. Se vacía el carrito sin persistir y se destruye la
 * sesión al terminar (bloque `finally`), de modo que /quote no deja filas.
 *
 * El pedido (/orders) se crea con `wc_create_order` y las MISMAS entradas, y su
 * total lo recalcula `calculate_totals( true )`; el endpoint compara ese total con
 * el aprobado, de modo que una discrepancia de redondeo entre carrito y pedido
 * (plugins de impuestos de terceros) nunca se cobra: da 409.
 *
 * ESTE FICHERO NO SE EJECUTA EN LAS PRUEBAS UNITARIAS (no hay WordPress ni
 * WooCommerce): su verificación es el E2E de la Fase B del plan.
 *
 * @package Trusteed
 * @since   2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Trusteed_Agent_Woo_Adapter {

	public const META_ORDER_ID   = '_trusteed_order_id';
	public const META_SESSION_ID = '_trusteed_checkout_session_id';
	public const META_AGENT_DID  = '_trusteed_agent_did';
	public const GATEWAY_ID      = 'trusteed_agent';

	/** @var Trusteed_Checkout_Enforcer|null */
	private $enforcer;

	/**
	 * @param Trusteed_Checkout_Enforcer|null $enforcer Evaluador de reglas locales.
	 */
	public function __construct( $enforcer ) {
		$this->enforcer = $enforcer;
	}

	/**
	 * Presupuesto con el motor del carrito de Woo.
	 *
	 * @param array<string,mixed> $input Entrada normalizada por el endpoint.
	 * @return array<string,mixed>|WP_Error
	 */
	public function quote( array $input ) {
		if ( ! function_exists( 'wc_load_cart' ) || ! function_exists( 'WC' ) ) {
			return new WP_Error( 'woo_unavailable', 'WooCommerce is not available.', array( 'status' => 503 ) );
		}
		wc_load_cart();
		$cart = WC()->cart;
		if ( ! $cart || ! WC()->customer ) {
			return new WP_Error( 'woo_unavailable', 'WooCommerce cart is not available.', array( 'status' => 503 ) );
		}

		try {
			wc_clear_notices();
			$cart->empty_cart( false );
			$this->apply_customer_address( $input );

			foreach ( $input['lines'] as $line ) {
				$key = $cart->add_to_cart( $line['product_id'], $line['qty'], $line['variation_id'] );
				if ( false === $key ) {
					return new WP_Error( 'line_unavailable', $this->first_notice( 'Item unavailable or out of stock.' ), array( 'status' => 409 ) );
				}
			}
			foreach ( $input['coupons'] as $code ) {
				if ( ! $cart->apply_coupon( $code ) ) {
					return new WP_Error( 'coupon_invalid', $this->first_notice( 'Coupon not valid.' ), array( 'status' => 422 ) );
				}
			}
			// Comprobación final de stock/compra de todo el carrito (cantidades acumuladas).
			$cart->check_cart_items();
			$stock_notice = $this->take_notices();
			if ( '' !== $stock_notice ) {
				return new WP_Error( 'out_of_stock', $stock_notice, array( 'status' => 409 ) );
			}

			if ( null !== $input['shipping_method_id'] && WC()->session ) {
				WC()->session->set( 'chosen_shipping_methods', array( $input['shipping_method_id'] ) );
			}
			$cart->calculate_shipping();
			$cart->calculate_totals();

			return $this->read_quote( $cart, $input );
		} finally {
			$cart->empty_cart( false );
			wc_clear_notices();
			if ( WC()->session && method_exists( WC()->session, 'destroy_session' ) ) {
				WC()->session->destroy_session();
			}
		}
	}

	/**
	 * @param \WC_Cart            $cart  Carrito calculado.
	 * @param array<string,mixed> $input Entrada.
	 * @return array<string,mixed>|WP_Error
	 */
	private function read_quote( $cart, array $input ) {
		$rates  = array();
		$chosen = null;
		if ( $cart->needs_shipping() ) {
			$packages   = WC()->shipping()->get_packages();
			$package    = is_array( $packages ) ? reset( $packages ) : false;
			$raw_rates  = is_array( $package ) && isset( $package['rates'] ) ? $package['rates'] : array();
			$chosen_ids = WC()->session ? (array) WC()->session->get( 'chosen_shipping_methods', array() ) : array();
			$chosen_id  = isset( $chosen_ids[0] ) ? (string) $chosen_ids[0] : '';
			foreach ( $raw_rates as $rate ) {
				$entry   = array(
					'id'         => (string) $rate->get_id(),
					'label'      => (string) $rate->get_label(),
					'cost_cents' => self::cents( $rate->get_cost() ),
				);
				$rates[] = $entry;
				if ( $entry['id'] === $chosen_id ) {
					$chosen = $entry;
				}
			}
			if ( array() === $rates ) {
				return new WP_Error( 'no_shipping_rates', 'No shipping method is available for this address.', array( 'status' => 422 ) );
			}
			if ( null !== $input['shipping_method_id'] && null === $chosen ) {
				return new WP_Error( 'shipping_method_unavailable', 'The chosen shipping method is not available.', array( 'status' => 409 ) );
			}
		}

		$lines = array();
		foreach ( $cart->get_cart() as $item ) {
			$lines[] = array(
				'product_id'       => (int) $item['product_id'],
				'variation_id'     => (int) $item['variation_id'],
				'qty'              => (int) $item['quantity'],
				'line_total_cents' => self::cents( $item['line_subtotal'] ),
			);
		}

		return array(
			'currency'       => strtoupper( (string) get_woocommerce_currency() ),
			'subtotal_cents' => self::cents( $cart->get_subtotal() ),
			'discount_cents' => self::cents( $cart->get_discount_total() ),
			'shipping_cents' => self::cents( $cart->get_shipping_total() ),
			'tax_cents'      => self::cents( $cart->get_total_tax() ),
			'total_cents'    => self::cents( $cart->get_total( 'edit' ) ),
			'lines'          => $lines,
			'shipping'       => array(
				'chosen' => $chosen,
				'rates'  => $rates,
			),
		);
	}

	/**
	 * Crea el pedido `pending` con las líneas, cupones, envío y direcciones.
	 *
	 * @param array<string,mixed> $input Entrada normalizada + trusteed_order_id, checkout_session_id, agent_did.
	 * @return array<string,mixed>|WP_Error Pedido (snapshot) o error.
	 */
	public function create_order( array $input ) {
		$quote = $this->quote( $input );
		if ( $quote instanceof WP_Error ) {
			return $quote;
		}
		$order = wc_create_order(
			array(
				'status'     => 'pending',
				'created_via' => 'trusteed_agent',
			)
		);
		if ( is_wp_error( $order ) || ! $order ) {
			return new WP_Error( 'order_create_failed', 'The order could not be created.', array( 'status' => 500 ) );
		}

		try {
			foreach ( $input['lines'] as $line ) {
				$product = wc_get_product( $line['variation_id'] > 0 ? $line['variation_id'] : $line['product_id'] );
				if ( ! $product ) {
					throw new \RuntimeException( 'product_missing' );
				}
				$order->add_product( $product, $line['qty'] );
			}
			$order->set_address( $input['billing_address'], 'billing' );
			$order->set_address( $input['shipping_address'], 'shipping' );
			if ( '' !== $input['email'] ) {
				$order->set_billing_email( $input['email'] );
			}
			foreach ( $input['coupons'] as $code ) {
				$applied = $order->apply_coupon( $code );
				if ( is_wp_error( $applied ) ) {
					throw new \RuntimeException( 'coupon_invalid' );
				}
			}
			$chosen = $quote['shipping']['chosen'] ?? null;
			if ( is_array( $chosen ) ) {
				$parts = explode( ':', (string) $chosen['id'], 2 );
				$item  = new WC_Order_Item_Shipping();
				$item->set_method_title( (string) $chosen['label'] );
				$item->set_method_id( $parts[0] );
				$item->set_instance_id( isset( $parts[1] ) ? (int) $parts[1] : 0 );
				$item->set_total( (string) ( $chosen['cost_cents'] / 100 ) );
				$order->add_item( $item );
			}
			$order->set_payment_method( self::GATEWAY_ID );
			$order->set_payment_method_title( 'Trusteed (agente)' );
			$order->update_meta_data( self::META_ORDER_ID, (string) $input['trusteed_order_id'] );
			$order->update_meta_data( self::META_SESSION_ID, (string) $input['checkout_session_id'] );
			if ( ! empty( $input['agent_did'] ) ) {
				$order->update_meta_data( self::META_AGENT_DID, (string) $input['agent_did'] );
			}
			$order->calculate_totals( true );
			$order->save();
		} catch ( \Throwable $e ) {
			$order->delete( true );
			if ( 'coupon_invalid' === $e->getMessage() ) {
				return new WP_Error( 'coupon_invalid', 'Coupon not valid.', array( 'status' => 422 ) );
			}
			throw $e;
		}
		return $this->snapshot( $order );
	}

	/**
	 * @param string $trusteed_order_id Id de Trusteed.
	 * @return array<string,mixed>|null
	 */
	public function find_order_by_trusteed_id( string $trusteed_order_id ): ?array {
		$found = wc_get_orders(
			array(
				'limit'      => 1,
				'return'     => 'objects',
				'status'     => array_keys( wc_get_order_statuses() ),
				'meta_query' => array(
					array(
						'key'   => self::META_ORDER_ID,
						'value' => $trusteed_order_id,
					),
				),
			)
		);
		return ( is_array( $found ) && isset( $found[0] ) ) ? $this->snapshot( $found[0] ) : null;
	}

	/**
	 * @param int $id Pedido.
	 * @return array<string,mixed>|null
	 */
	public function get_order( int $id ): ?array {
		$order = wc_get_order( $id );
		return ( $order instanceof WC_Order ) ? $this->snapshot( $order ) : null;
	}

	/**
	 * @param int         $order_id  Pedido.
	 * @param string|null $agent_did DID declarado por Trusteed (canal firmado).
	 * @param string|null $identity_level Nivel de identidad declarado por Trusteed (canal firmado).
	 * @return string 'ALLOW' | 'BLOCK' | 'UNAVAILABLE'
	 */
	public function evaluate_rules( int $order_id, ?string $agent_did, ?string $identity_level = null ): string {
		$order = wc_get_order( $order_id );
		if ( ! ( $order instanceof WC_Order ) || null === $this->enforcer ) {
			return 'UNAVAILABLE';
		}
		$decision = $this->enforcer->evaluate_order_for_agent_api( $order, $agent_did, $identity_level );
		if ( 'ALLOW' === $decision || 'BLOCK' === $decision ) {
			return $decision;
		}
		return 'UNAVAILABLE';
	}

	/**
	 * 2.5.2: motivo estructurado de la última evaluate_rules() (null si no hay o fue ALLOW).
	 *
	 * @return array<string,string>|null
	 */
	public function last_agent_reason(): ?array {
		return null === $this->enforcer ? null : $this->enforcer->get_last_agent_reason();
	}

	/**
	 * @param int $order_id Pedido.
	 * @return void
	 */
	public function delete_order( int $order_id ): void {
		$order = wc_get_order( $order_id );
		if ( $order instanceof WC_Order ) {
			$order->delete( true );
		}
	}

	/**
	 * @param int    $order_id          Pedido.
	 * @param string $payment_reference PaymentIntent (pi_…) o, con `paypal`, captureId de PayPal.
	 * @param string $payment_method    '' (Stripe, como siempre) o 'paypal'.
	 * @return void
	 */
	public function complete_payment( int $order_id, string $payment_reference, string $payment_method = '' ): void {
		$order = wc_get_order( $order_id );
		if ( ! ( $order instanceof WC_Order ) ) {
			return;
		}
		if ( 'paypal' === $payment_method ) {
			$order->set_payment_method( 'paypal' );
			$order->set_payment_method_title( 'PayPal (Trusteed)' );
		} else {
			$order->set_payment_method( self::GATEWAY_ID );
			$order->set_payment_method_title( 'Trusteed (agente)' );
		}
		$order->set_transaction_id( $payment_reference );
		$order->save();
		// Descuenta stock (hook wc_maybe_reduce_stock_levels) y pasa a processing/completed.
		$order->payment_complete( $payment_reference );
	}

	/**
	 * @param int    $order_id Pedido.
	 * @param string $note     Nota de pedido visible para el comerciante.
	 * @return void
	 */
	public function hold_for_review( int $order_id, string $note ): void {
		$order = wc_get_order( $order_id );
		if ( $order instanceof WC_Order ) {
			$order->update_status( 'on-hold', $note );
		}
	}

	// ── helpers ──────────────────────────────────────────────────────────────

	/**
	 * @param \WC_Order $order Pedido.
	 * @return array<string,mixed>
	 */
	private function snapshot( $order ): array {
		return array(
			'id'                => (int) $order->get_id(),
			'number'            => (string) $order->get_order_number(),
			'status'            => (string) $order->get_status(),
			'total_cents'       => self::cents( $order->get_total() ),
			'currency'          => strtoupper( (string) $order->get_currency() ),
			'trusteed_order_id' => (string) $order->get_meta( self::META_ORDER_ID, true ),
			'is_paid'           => (bool) $order->is_paid(),
			'transaction_id'    => (string) $order->get_transaction_id(),
		);
	}

	/**
	 * @param array<string,mixed> $input Entrada.
	 * @return void
	 */
	private function apply_customer_address( array $input ): void {
		$customer = WC()->customer;
		$ship     = $input['shipping_address'];
		$bill     = $input['billing_address'];
		foreach ( array( 'country', 'state', 'postcode', 'city', 'address_1', 'address_2' ) as $field ) {
			$s = isset( $ship[ $field ] ) ? $ship[ $field ] : '';
			$b = isset( $bill[ $field ] ) ? $bill[ $field ] : $s;
			call_user_func( array( $customer, 'set_shipping_' . $field ), $s );
			call_user_func( array( $customer, 'set_billing_' . $field ), $b );
		}
	}

	/**
	 * Primer aviso de error de Woo (y los limpia); $fallback si no hay.
	 *
	 * @param string $fallback Texto por defecto.
	 * @return string
	 */
	private function first_notice( string $fallback ): string {
		$text = $this->take_notices();
		return '' !== $text ? $text : $fallback;
	}

	/**
	 * @return string Avisos de error concatenados, sin HTML.
	 */
	private function take_notices(): string {
		$out = array();
		foreach ( (array) wc_get_notices( 'error' ) as $notice ) {
			$text  = is_array( $notice ) ? ( $notice['notice'] ?? '' ) : $notice;
			$out[] = trim( wp_strip_all_tags( (string) $text ) );
		}
		wc_clear_notices();
		return trim( implode( ' ', array_filter( $out ) ) );
	}

	/**
	 * @param mixed $amount Importe decimal de Woo.
	 * @return int Céntimos.
	 */
	private static function cents( $amount ): int {
		return (int) round( (float) $amount * 100 );
	}
}
