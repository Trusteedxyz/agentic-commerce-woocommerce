<?php
/**
 * Multi-add handler — consumes `?agenticmcp_multi_add=1&items[N][...]` URLs
 * emitted by Trusteed_Cart_Bridge::build_checkout_url() for multi-item
 * agent carts, populates WC()->cart, then redirects to the checkout page
 * preserving the agent identity params for the enforcer.
 *
 * WooCommerce native `?add-to-cart=<id>` handles a single product only.
 * Without this handler multi-item agent carts arrived empty (or with the
 * first item only), silently degrading the agentic checkout flow.
 *
 * Trust model:
 *  - URL is emitted by Trusteed_Cart_Bridge after server-side validation
 *    (auth, in_stock, purchasable, max-50 items).
 *  - We re-validate each product server-side here (in case the URL is
 *    reused/shared) and hard-cap items at MAX_LINE_ITEMS=50 as defense
 *    in depth.
 *  - NO nonce check: the URL is meant to be opened by the buyer in a
 *    fresh browser session; nonces would not be available.
 *
 * @package AgenticMCPStores
 * @since   1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Amcp_Multi_Add_Handler
 *
 * @since 1.6.0
 */
class Amcp_Multi_Add_Handler {

	/**
	 * Query flag that activates this handler.
	 *
	 * @var string
	 */
	const FLAG_PARAM = 'agenticmcp_multi_add';

	/**
	 * Hard cap mirroring Trusteed_Cart_Bridge::MAX_LINE_ITEMS.
	 *
	 * @var int
	 */
	const MAX_LINE_ITEMS = 50;

	/**
	 * Register the wp_loaded hook (runs after WC has booted its session
	 * + cart, before template_redirect — early enough to add to cart and
	 * still safely redirect via wp_safe_redirect()).
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'wp_loaded', array( $this, 'maybe_handle' ), 20 );
	}

	/**
	 * Inspect the current request and, if it matches the multi-add shape,
	 * populate the cart and redirect to checkout.
	 *
	 * @return void
	 */
	public function maybe_handle(): void {
		if ( ! isset( $_GET[ self::FLAG_PARAM ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		if ( '1' !== (string) $_GET[ self::FLAG_PARAM ] ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		if ( ! isset( $_GET['items'] ) || ! is_array( $_GET['items'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return;
		}

		$raw_items = wp_unslash( $_GET['items'] ); // phpcs:ignore WordPress.Security.NonceVerification

		if ( count( $raw_items ) > self::MAX_LINE_ITEMS ) {
			wp_die(
				esc_html(
					sprintf(
						/* translators: %d: maximum allowed line items */
						__( 'AgenticMCP multi-add: too many line items (max %d).', 'agenticmcpstores' ),
						self::MAX_LINE_ITEMS
					)
				),
				'',
				array( 'response' => 400 )
			);
		}

		$added_any = false;

		foreach ( $raw_items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$product_id   = isset( $item['product_id'] ) ? absint( $item['product_id'] ) : 0;
			$quantity     = isset( $item['quantity'] ) ? absint( $item['quantity'] ) : 0;
			$variation_id = isset( $item['variation_id'] ) ? absint( $item['variation_id'] ) : 0;

			if ( $product_id < 1 || $quantity < 1 ) {
				continue;
			}

			$target_id = $variation_id > 0 ? $variation_id : $product_id;
			$product   = function_exists( 'wc_get_product' ) ? wc_get_product( $target_id ) : null;

			if ( ! $product instanceof \WC_Product ) {
				continue;
			}
			if ( ! $product->is_purchasable() || ! $product->is_in_stock() ) {
				continue;
			}

			$result = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id );
			if ( false !== $result ) {
				$added_any = true;
			}
		}

		if ( ! $added_any ) {
			return;
		}

		// Preserve the agent identity query params so the checkout-enforcer
		// can capture them on the next page load. We strip the `items[N]` /
		// `agenticmcp_multi_add` payload from the URL to keep it tidy.
		$forwarded = array();
		if ( isset( $_GET[ Trusteed_Cart_Bridge::AGENT_ID_PARAM ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$forwarded[ Trusteed_Cart_Bridge::AGENT_ID_PARAM ] = rawurlencode(
				sanitize_text_field(
					rawurldecode( (string) wp_unslash( $_GET[ Trusteed_Cart_Bridge::AGENT_ID_PARAM ] ) ) // phpcs:ignore WordPress.Security.NonceVerification
				)
			);
		}
		if ( isset( $_GET[ Trusteed_Cart_Bridge::AGENT_TOKEN_PARAM ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$forwarded[ Trusteed_Cart_Bridge::AGENT_TOKEN_PARAM ] = rawurlencode(
				sanitize_text_field(
					rawurldecode( (string) wp_unslash( $_GET[ Trusteed_Cart_Bridge::AGENT_TOKEN_PARAM ] ) ) // phpcs:ignore WordPress.Security.NonceVerification
				)
			);
		}

		$checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' );
		$redirect_to  = empty( $forwarded ) ? $checkout_url : add_query_arg( $forwarded, $checkout_url );

		wp_safe_redirect( $redirect_to );
		exit;
	}
}
