<?php
/**
 * WebMCP checkout endpoint — tools `get_checkout`, `update_checkout`,
 * `complete_checkout` registered in the browser on the checkout page.
 *
 * The browser module POSTs `{ tool, arguments }` here. This endpoint is what
 * marks the WC session as agentic SERVER-SIDE (the client can never clear it),
 * and it never creates an order or charges: `complete_checkout` only hands the
 * buyer the checkout URL.
 *
 * @package Trusteed
 * @since   2.6.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Trusteed_Webmcp_Checkout
 *
 * @since 2.6.0
 */
class Trusteed_Webmcp_Checkout {

	/**
	 * Maximum request body size in bytes (16 KiB).
	 *
	 * @var int
	 */
	const MAX_BODY_BYTES = 16384;

	/**
	 * Nonce action; starts with 'woocommerce' so WC binds it to the guest session.
	 *
	 * @var string
	 */
	const NONCE_ACTION = 'woocommerce-trusteed-webmcp-checkout';

	/**
	 * Header carrying that nonce.
	 *
	 * @var string
	 */
	const NONCE_HEADER = 'X-Trusteed-Nonce';

	/**
	 * Tools this endpoint dispatches.
	 *
	 * @var string[]
	 */
	const TOOLS = array( 'get_checkout', 'update_checkout', 'complete_checkout' );

	/**
	 * Register the route in both namespaces (see Trusteed_Cart_Bridge::REST_NAMESPACE_LEGACY).
	 *
	 * Hooked to 'rest_api_init'.
	 *
	 * @return void
	 */
	public function register_routes() {
		$route_args = array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'handle' ),
			'permission_callback' => array( $this, 'check_nonce' ),
		);

		register_rest_route( Trusteed_Cart_Bridge::REST_NAMESPACE, '/webmcp/checkout', $route_args );
		register_rest_route( Trusteed_Cart_Bridge::REST_NAMESPACE_LEGACY, '/webmcp/checkout', $route_args );
	}

	/**
	 * Enqueue the browser module that registers the tools. Hooked to 'wp_enqueue_scripts'.
	 *
	 * Only on the checkout page (not order-received). The nonce is bound to the
	 * guest's WC session; X-WP-Nonce keeps logged-in users authenticated for WP core.
	 *
	 * @return void
	 */
	public function enqueue() {
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
			return;
		}
		if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
			return;
		}

		$handle = 'trusteed-checkout-tools';
		wp_enqueue_script( $handle, TRUSTEED_PLUGIN_URL . 'assets/js/checkout-tools.iife.js', array(), TRUSTEED_VERSION, true );
		wp_add_inline_script(
			$handle,
			'window.__trusteedCheckoutTools = ' . wp_json_encode(
				array(
					'endpoint'     => rest_url( Trusteed_Cart_Bridge::REST_NAMESPACE . '/webmcp/checkout' ),
					'nonce'        => wp_create_nonce( self::NONCE_ACTION ),
					'nonceHeader'  => self::NONCE_HEADER,
					'extraHeaders' => array( 'X-WP-Nonce' => wp_create_nonce( 'wp_rest' ) ),
					'platform'     => 'woocommerce',
				)
			) . ';',
			'before'
		);
	}

	/**
	 * Permission callback: own session-bound nonce (CSRF) and same-origin check.
	 *
	 * The core `wp_rest` nonce is identical for every guest, so it proves nothing
	 * here; WC binds nonces whose action starts with 'woocommerce' to the guest's
	 * session customer id. WordPress core still validates X-WP-Nonce itself.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return bool
	 */
	public function check_nonce( $request ): bool {
		// Guest nonces only verify once WC's session is up (it registers `nonce_user_logged_out`).
		$this->load_cart();

		if ( ! $this->origin_is_allowed( $request ) ) {
			return false;
		}

		$nonce = $request->get_header( self::NONCE_HEADER );
		return is_string( $nonce ) && '' !== $nonce && false !== wp_verify_nonce( $nonce, self::NONCE_ACTION );
	}

	/**
	 * An Origin header, when present, must match the site's scheme+host+port.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return bool
	 */
	private function origin_is_allowed( $request ): bool {
		$origin = $request->get_header( 'Origin' );
		if ( null === $origin || '' === $origin ) {
			return true;
		}
		return is_string( $origin ) && self::origin_of( $origin ) === self::origin_of( home_url() ) && null !== self::origin_of( $origin );
	}

	/**
	 * Normalise a URL to `scheme://host:port` (default ports filled in).
	 *
	 * @param string $url URL or Origin value.
	 * @return string|null
	 */
	private static function origin_of( string $url ) {
		$p = parse_url( $url );
		if ( ! is_array( $p ) || empty( $p['scheme'] ) || empty( $p['host'] ) ) {
			return null;
		}
		$scheme = strtolower( $p['scheme'] );
		$port   = $p['port'] ?? ( 'https' === $scheme ? 443 : 80 );
		return $scheme . '://' . strtolower( $p['host'] ) . ':' . $port;
	}

	/**
	 * Dispatch a tool call.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response
	 */
	public function handle( $request ) {
		$raw = (string) $request->get_body();
		if ( strlen( $raw ) > self::MAX_BODY_BYTES ) {
			return new WP_REST_Response( array( 'error' => 'payload_too_large' ), 413 );
		}

		$body = json_decode( $raw, true );
		$tool = is_array( $body ) && isset( $body['tool'] ) && is_string( $body['tool'] ) ? $body['tool'] : '';
		if ( ! in_array( $tool, self::TOOLS, true ) ) {
			return new WP_REST_Response( array( 'error' => 'unknown_tool' ), 400 );
		}
		$args = isset( $body['arguments'] ) && is_array( $body['arguments'] ) ? $body['arguments'] : array();

		$this->load_cart();
		$wc = WC();
		if ( empty( $wc->cart ) || empty( $wc->session ) || empty( $wc->customer ) || $wc->cart->is_empty() ) {
			// No cart (or expired session): report it and mark nothing.
			return new WP_REST_Response( array( 'status' => 'empty' ), 200 );
		}

		// A fresh REST request restores the cart but has no rates/totals yet.
		if ( method_exists( $wc->cart, 'calculate_totals' ) ) {
			$wc->cart->calculate_totals();
		}

		$checkout = isset( $args['checkout'] ) && is_array( $args['checkout'] ) ? $args['checkout'] : array();
		if ( 'update_checkout' === $tool && ! $this->shipping_method_is_valid( $wc, $checkout ) ) {
			return new WP_REST_Response( array( 'error' => 'invalid_shipping_method' ), 400 );
		}

		// Mark BEFORE dispatching; only ever adds restrictions, the client cannot remove it.
		$wc->session->set( Trusteed_Cart_Bridge::SESSION_AGENTIC_ORIGIN, '1' );
		$wc->session->set( Trusteed_Cart_Bridge::SESSION_AGENTIC_CHANNEL, 'webmcp' );

		if ( 'complete_checkout' === $tool ) {
			// Never creates an order: the human confirms in the checkout page.
			return new WP_REST_Response(
				array(
					'status'       => 'requires_buyer_confirmation',
					'checkout_url' => wc_get_checkout_url(),
				),
				200
			);
		}

		if ( 'update_checkout' === $tool ) {
			$this->apply_update( $wc, $checkout );
		}

		return new WP_REST_Response( $this->describe( $wc ), 200 );
	}

	/**
	 * REST requests do not load the cart by default; frontend-only in WooCommerce.
	 *
	 * @return void
	 */
	private function load_cart() {
		if ( function_exists( 'wc_load_cart' ) ) {
			wc_load_cart();
		}
	}

	/**
	 * A requested method_id must be one of the rates currently offered.
	 *
	 * @param object               $wc       WooCommerce instance.
	 * @param array<string,mixed> $checkout `checkout` argument.
	 * @return bool
	 */
	private function shipping_method_is_valid( $wc, array $checkout ): bool {
		$method = $checkout['fulfillment']['method_id'] ?? null;
		if ( null === $method || '' === $method ) {
			return true;
		}
		if ( ! is_string( $method ) ) {
			return false;
		}
		foreach ( (array) $wc->shipping()->get_packages() as $package ) {
			if ( isset( $package['rates'] ) && is_array( $package['rates'] ) && array_key_exists( $method, $package['rates'] ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Apply buyer email, shipping address and shipping method; everything else is ignored.
	 *
	 * @param object               $wc       WooCommerce instance.
	 * @param array<string,mixed> $checkout `checkout` argument.
	 * @return void
	 */
	private function apply_update( $wc, array $checkout ) {
		$customer = $wc->customer;

		$email = $checkout['buyer']['email'] ?? null;
		if ( is_string( $email ) && false !== filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$customer->set_billing_email( $email );
		}

		$fulfillment = isset( $checkout['fulfillment'] ) && is_array( $checkout['fulfillment'] ) ? $checkout['fulfillment'] : array();

		if ( isset( $fulfillment['address'] ) && is_array( $fulfillment['address'] ) ) {
			$map = array(
				'address1'      => 'set_shipping_address_1',
				'address2'      => 'set_shipping_address_2',
				'city'          => 'set_shipping_city',
				'postal_code'   => 'set_shipping_postcode',
				'country_code'  => 'set_shipping_country',
				'province_code' => 'set_shipping_state',
				'first_name'    => 'set_shipping_first_name',
				'last_name'     => 'set_shipping_last_name',
			);
			foreach ( $map as $field => $setter ) {
				if ( isset( $fulfillment['address'][ $field ] ) && is_string( $fulfillment['address'][ $field ] ) ) {
					$customer->$setter( sanitize_text_field( $fulfillment['address'][ $field ] ) );
				}
			}
		}

		if ( isset( $fulfillment['method_id'] ) && is_string( $fulfillment['method_id'] ) && '' !== $fulfillment['method_id'] ) {
			$wc->session->set( 'chosen_shipping_methods', array( sanitize_text_field( $fulfillment['method_id'] ) ) );
		}

		$wc->cart->calculate_totals();
	}

	/**
	 * Current checkout state.
	 *
	 * @param object $wc WooCommerce instance.
	 * @return array<string,mixed>
	 */
	private function describe( $wc ): array {
		$items = array();
		foreach ( $wc->cart->get_cart() as $line ) {
			$items[] = array(
				'name'       => (string) $line['data']->get_name(),
				'quantity'   => (int) $line['quantity'],
				'line_total' => (float) $line['line_total'],
			);
		}

		$chosen = $wc->session->get( 'chosen_shipping_methods' );

		return array(
			'status'      => 'open',
			'currency'    => get_woocommerce_currency(),
			'items'       => $items,
			'totals'      => array(
				'subtotal' => (float) $wc->cart->get_subtotal(),
				'shipping' => (float) $wc->cart->get_shipping_total(),
				'tax'      => (float) $wc->cart->get_total_tax(),
				'total'    => (float) $wc->cart->get_total( 'edit' ),
			),
			'buyer'       => array( 'email' => (string) $wc->customer->get_billing_email() ),
			'fulfillment' => array(
				'address'   => array(
					'address1'     => (string) $wc->customer->get_shipping_address_1(),
					'city'         => (string) $wc->customer->get_shipping_city(),
					'postal_code'  => (string) $wc->customer->get_shipping_postcode(),
					'country_code' => (string) $wc->customer->get_shipping_country(),
				),
				'method_id' => is_array( $chosen ) && isset( $chosen[0] ) ? (string) $chosen[0] : null,
			),
		);
	}
}
