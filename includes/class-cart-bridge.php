<?php
/**
 * Cart bridge — REST API endpoints for agent-initiated carts.
 *
 * @package Trusteed
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Trusteed_Cart_Bridge
 *
 * Receives cart creation requests from the Trusteed backend
 * and builds native WooCommerce checkout URLs for the buyer.
 *
 * CRITICAL CONSTRAINT: checkout_url ALWAYS points to the merchant's own
 * WooCommerce instance. NEVER returns URLs to trusteed.xyz or
 * any external domain.
 *
 * @since 1.0.0
 */
class Trusteed_Cart_Bridge {

	/**
	 * REST API namespace.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const REST_NAMESPACE = 'agenticmcp/v1';

	/**
	 * Maximum allowed line items per cart request.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	const MAX_LINE_ITEMS = 50;

	/**
	 * Register REST API routes.
	 *
	 * Hooked to 'rest_api_init'.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			'/cart',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'handle_create_cart' ),
				'permission_callback' => array( $this, 'check_api_key_permission' ),
				'args'                => array(
					'line_items' => array(
						'required'          => true,
						'validate_callback' => array( $this, 'validate_line_items_param' ),
						'sanitize_callback' => array( $this, 'sanitize_line_items_param' ),
					),
				),
			)
		);
	}

	/**
	 * WC session key used to carry the agent DID from checkout page load to checkout process.
	 *
	 * @since 1.3.0
	 * @var string
	 */
	const SESSION_AGENT_ID = 'amcp_agent_id';

	/**
	 * URL query param that carries the agent DID to the checkout page.
	 *
	 * @since 1.3.0
	 * @var string
	 */
	const AGENT_ID_PARAM = 'amcp_agent_id';

	/**
	 * DID regex — matches did:web:... and did:key:... without fragment.
	 *
	 * @since 1.3.0
	 * @var string
	 */
	const AGENT_DID_RE = '/^did:(web|key):[A-Za-z0-9._:\-]+$/';

	/**
	 * URL query param that carries the agent JWS token to the checkout page.
	 *
	 * @since 1.4.0
	 * @var string
	 */
	const AGENT_TOKEN_PARAM = 'amcp_agent_token';

	/**
	 * WC session key used to carry the agent JWS token from checkout page load to checkout process.
	 *
	 * @since 1.4.0
	 * @var string
	 */
	const SESSION_AGENT_TOKEN = 'amcp_agent_token';

	/**
	 * Handle POST /wp-json/agenticmcp/v1/cart.
	 *
	 * Validates each line item, builds a native WooCommerce checkout URL,
	 * and returns it along with item count and estimated total.
	 *
	 * Accepts optional `agent_id` (DID string) that is appended to the checkout
	 * URL so the enforcement hook can identify agent-initiated orders.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request REST request object.
	 *
	 * @return WP_REST_Response|WP_Error JSON response with checkout_url, items_count, total.
	 */
	public function handle_create_cart( $request ) {
		$line_items  = $request->get_param( 'line_items' );
		$agent_id    = $this->sanitize_agent_id( $request->get_param( 'agent_id' ) );
		$agent_token = $this->sanitize_agent_token( $request->get_param( 'agent_token' ) );

		if ( empty( $line_items ) || ! is_array( $line_items ) ) {
			return new \WP_Error(
				'agenticmcp_invalid_line_items',
				__( 'line_items must be a non-empty array.', 'trusteed-for-woocommerce' ),
				array( 'status' => 400 )
			);
		}

		if ( count( $line_items ) > self::MAX_LINE_ITEMS ) {
			return new \WP_Error(
				'agenticmcp_too_many_items',
				sprintf(
					/* translators: %d: maximum number of line items */
					__( 'Maximum %d line items allowed per cart.', 'trusteed-for-woocommerce' ),
					self::MAX_LINE_ITEMS
				),
				array( 'status' => 400 )
			);
		}

		$validated_items = array();
		$items_count     = 0;

		foreach ( $line_items as $index => $item ) {
			$product_id   = isset( $item['product_id'] ) ? absint( $item['product_id'] ) : 0;
			$quantity     = isset( $item['quantity'] ) ? absint( $item['quantity'] ) : 0;
			$variation_id = isset( $item['variation_id'] ) ? absint( $item['variation_id'] ) : 0;

			if ( 0 === $product_id ) {
				return new \WP_Error(
					'agenticmcp_invalid_product_id',
					sprintf(
						/* translators: %d: line item index (0-based) */
						__( 'Invalid product_id at line item index %d.', 'trusteed-for-woocommerce' ),
						$index
					),
					array( 'status' => 400 )
				);
			}

			if ( 0 === $quantity ) {
				return new \WP_Error(
					'agenticmcp_invalid_quantity',
					sprintf(
						/* translators: %d: line item index (0-based) */
						__( 'Quantity must be greater than 0 at line item index %d.', 'trusteed-for-woocommerce' ),
						$index
					),
					array( 'status' => 400 )
				);
			}

			$target_id = $variation_id > 0 ? $variation_id : $product_id;
			$product   = wc_get_product( $target_id );

			if ( ! $product instanceof \WC_Product ) {
				return new \WP_Error(
					'agenticmcp_product_not_found',
					sprintf(
						/* translators: %d: product ID */
						__( 'Product with ID %d not found.', 'trusteed-for-woocommerce' ),
						$target_id
					),
					array( 'status' => 404 )
				);
			}

			if ( ! $product->is_purchasable() ) {
				return new \WP_Error(
					'agenticmcp_product_not_purchasable',
					sprintf(
						/* translators: %s: product name */
						__( 'Product "%s" is not available for purchase.', 'trusteed-for-woocommerce' ),
						$product->get_name()
					),
					array( 'status' => 400 )
				);
			}

			if ( ! $product->is_in_stock() ) {
				return new \WP_Error(
					'agenticmcp_product_out_of_stock',
					sprintf(
						/* translators: %s: product name */
						__( 'Product "%s" is out of stock.', 'trusteed-for-woocommerce' ),
						$product->get_name()
					),
					array( 'status' => 400 )
				);
			}

			$validated_items[] = array(
				'product_id'   => $product_id,
				'quantity'     => $quantity,
				'variation_id' => $variation_id,
				'product'      => $product,
			);

			$items_count += $quantity;
		}

		$checkout_url = $this->build_checkout_url( $validated_items, $agent_id, $agent_token );
		$total        = $this->calculate_total( $validated_items );

		return rest_ensure_response(
			array(
				'checkout_url' => esc_url( $checkout_url ),
				'items_count'  => $items_count,
				'total'        => $total,
			)
		);
	}

	/**
	 * Permission callback: validate the X-AgenticMCP-Key header.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request REST request object.
	 *
	 * @return bool|WP_Error True if authorized, WP_Error otherwise.
	 */
	public function check_api_key_permission( $request ) {
		$provided_key = sanitize_text_field( $request->get_header( 'X-AgenticMCP-Key' ) );
		$stored_key   = Trusteed_Api_Client::get_stored_api_key();

		if ( empty( $stored_key ) ) {
			return new \WP_Error(
				'agenticmcp_not_configured',
				__( 'Plugin API key is not configured.', 'trusteed-for-woocommerce' ),
				array( 'status' => 500 )
			);
		}

		if ( empty( $provided_key ) || ! hash_equals( $stored_key, $provided_key ) ) {
			return new \WP_Error(
				'agenticmcp_unauthorized',
				__( 'Invalid or missing API key.', 'trusteed-for-woocommerce' ),
				array( 'status' => 401 )
			);
		}

		return true;
	}

	/**
	 * Validate the line_items parameter.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed           $value   Parameter value.
	 * @param WP_REST_Request $request REST request object.
	 * @param string          $param   Parameter name.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public function validate_line_items_param( $value, $request, $param ) {
		if ( ! is_array( $value ) ) {
			return false;
		}

		if ( empty( $value ) ) {
			return false;
		}

		foreach ( $value as $item ) {
			if ( ! is_array( $item ) ) {
				return false;
			}

			if ( ! isset( $item['product_id'] ) || absint( $item['product_id'] ) < 1 ) {
				return false;
			}

			if ( ! isset( $item['quantity'] ) || absint( $item['quantity'] ) < 1 ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Sanitize the line_items parameter.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed           $value   Parameter value.
	 * @param WP_REST_Request $request REST request object.
	 * @param string          $param   Parameter name.
	 *
	 * @return array Sanitized array of line items.
	 */
	public function sanitize_line_items_param( $value, $request, $param ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$sanitized = array();

		foreach ( $value as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$sanitized[] = array(
				'product_id'   => isset( $item['product_id'] ) ? absint( $item['product_id'] ) : 0,
				'quantity'     => isset( $item['quantity'] ) ? absint( $item['quantity'] ) : 0,
				'variation_id' => isset( $item['variation_id'] ) ? absint( $item['variation_id'] ) : 0,
			);
		}

		return $sanitized;
	}

	/**
	 * Sanitize and validate an agent_id (DID) from untrusted input.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed $raw Raw value from REST request.
	 * @return string|null Validated DID string or null if invalid/absent.
	 */
	private function sanitize_agent_id( $raw ): ?string {
		if ( empty( $raw ) || ! is_string( $raw ) ) {
			return null;
		}
		$did = sanitize_text_field( $raw );
		if ( ! preg_match( self::AGENT_DID_RE, $did ) ) {
			return null;
		}
		return $did;
	}

	/**
	 * Sanitize and validate an agent JWS token (Compact Serialization) from untrusted input.
	 *
	 * A JWS Compact token is three base64url segments separated by dots
	 * (header.payload.signature). This performs a format-only gate matching the
	 * checkout enforcer's capture regex; cryptographic verification happens
	 * server-side after the token reaches the checkout page via the URL param.
	 *
	 * @since 1.4.0
	 *
	 * @param mixed $raw Raw value from REST request.
	 * @return string|null Validated JWS compact string or null if invalid/absent.
	 */
	private function sanitize_agent_token( $raw ): ?string {
		if ( empty( $raw ) || ! is_string( $raw ) ) {
			return null;
		}
		$token = sanitize_text_field( $raw );
		// Bound length to a sane maximum to avoid oversized URLs / abuse.
		if ( strlen( $token ) > 8192 ) {
			return null;
		}
		// JWS Compact: three base64url segments separated by dots.
		if ( ! preg_match( '/^[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]*$/', $token ) ) {
			return null;
		}
		return $token;
	}

	/**
	 * Build a native WooCommerce checkout URL for the given line items.
	 *
	 * Uses the WooCommerce add-to-cart URL parameter format to pre-populate
	 * the cart. For multiple items, chains them via the standard WooCommerce
	 * add-to-cart query format.
	 *
	 * When $agent_id is provided (a DID), it is appended to the URL so the
	 * enforcement hook can identify agent-initiated checkouts. When $agent_token
	 * (a JWS compact token) is provided, it is appended so the enforcer can run
	 * R002 signature/replay verification on the agent identity.
	 *
	 * @since 1.0.0
	 *
	 * @param array       $validated_items Array of validated line item arrays.
	 * @param string|null $agent_id        Optional agent DID.
	 * @param string|null $agent_token     Optional agent JWS compact token.
	 *
	 * @return string Native WooCommerce checkout URL.
	 */
	private function build_checkout_url( array $validated_items, ?string $agent_id = null, ?string $agent_token = null ) {
		if ( count( $validated_items ) === 1 ) {
			$item       = $validated_items[0];
			$product_id = $item['product_id'];
			$quantity   = $item['quantity'];

			$args = array(
				'add-to-cart' => $product_id,
				'quantity'    => $quantity,
			);

			if ( $item['variation_id'] > 0 ) {
				$args['variation_id'] = $item['variation_id'];
			}

			if ( null !== $agent_id ) {
				$args[ self::AGENT_ID_PARAM ] = rawurlencode( $agent_id );
			}

			if ( null !== $agent_token ) {
				$args[ self::AGENT_TOKEN_PARAM ] = rawurlencode( $agent_token );
			}

			return add_query_arg( $args, wc_get_checkout_url() );
		}

		// Multiple items: use WooCommerce's batch add-to-cart format.
		// Build a URL that adds items sequentially via a custom endpoint,
		// falling back to the first-item approach with a session-based strategy.
		$query_args = array();

		foreach ( $validated_items as $index => $item ) {
			$prefix = 'items[' . $index . ']';

			$query_args[ $prefix . '[product_id]' ] = $item['product_id'];
			$query_args[ $prefix . '[quantity]' ]    = $item['quantity'];

			if ( $item['variation_id'] > 0 ) {
				$query_args[ $prefix . '[variation_id]' ] = $item['variation_id'];
			}
		}

		$query_args['agenticmcp_multi_add'] = '1';

		if ( null !== $agent_id ) {
			$query_args[ self::AGENT_ID_PARAM ] = rawurlencode( $agent_id );
		}

		if ( null !== $agent_token ) {
			$query_args[ self::AGENT_TOKEN_PARAM ] = rawurlencode( $agent_token );
		}

		return add_query_arg( $query_args, wc_get_checkout_url() );
	}

	/**
	 * Calculate the estimated cart total.
	 *
	 * Sums the price of each line item multiplied by its quantity.
	 * Returns a formatted string using WooCommerce's currency settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $validated_items Array of validated line items with 'product' key.
	 *
	 * @return string Formatted price string (e.g. "59.98").
	 */
	private function calculate_total( array $validated_items ) {
		$total = 0.0;

		foreach ( $validated_items as $item ) {
			$product = $item['product'];
			$price   = (float) $product->get_price();

			$total += $price * $item['quantity'];
		}

		return wc_format_decimal( $total, wc_get_price_decimals() );
	}
}
