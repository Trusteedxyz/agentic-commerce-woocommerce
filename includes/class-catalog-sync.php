<?php
/**
 * Catalog synchronization between WooCommerce and AgenticMCPStores.
 *
 * @package AgenticMCPStores
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AgenticMCP_Catalog_Sync
 *
 * Handles product sync on create, update, stock change, and delete events.
 * Also provides a full catalog sync triggered manually from the settings page.
 *
 * @since 1.0.0
 */
class AgenticMCP_Catalog_Sync {

	/**
	 * Number of products per batch during full sync.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	const BATCH_SIZE = 100;

	/**
	 * API client instance.
	 *
	 * @since 1.0.0
	 * @var Trusteed_Api_Client
	 */
	private $api_client;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Trusteed_Api_Client $api_client API client instance.
	 */
	public function __construct( Trusteed_Api_Client $api_client ) {
		$this->api_client = $api_client;
	}

	/**
	 * Handle product save (create or update).
	 *
	 * Hooked to 'save_post_product'. Sends the product payload to the API
	 * so the remote catalog stays in sync.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an update (true) or new post (false).
	 *
	 * @return void
	 */
	public function on_product_save( $post_id, $post, $update ) {
		// Skip autosaves and revisions.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Only sync published products.
		if ( 'publish' !== $post->post_status ) {
			return;
		}

		$product = wc_get_product( $post_id );

		if ( ! $product instanceof \WC_Product ) {
			return;
		}

		$payload = $this->product_to_payload( $product );

		$response = $this->api_client->post(
			'/api/v1/plugin/catalog/sync',
			array(
				'products' => array( $payload ),
			)
		);

		$this->log_sync_outcome( 'on_product_save', $post_id, $response );
	}

	/**
	 * Handle stock quantity change.
	 *
	 * Hooked to 'woocommerce_product_set_stock'. Sends an incremental
	 * sync with only the stock-relevant fields.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Product $product The product whose stock changed.
	 *
	 * @return void
	 */
	public function on_stock_change( $product ) {
		if ( ! $product instanceof \WC_Product ) {
			return;
		}

		// Only sync published products.
		if ( 'publish' !== get_post_status( $product->get_id() ) ) {
			return;
		}

		$payload = $this->product_to_payload( $product );

		$response = $this->api_client->post(
			'/api/v1/plugin/catalog/sync',
			array(
				'products' => array( $payload ),
			)
		);

		$this->log_sync_outcome( 'on_stock_change', $product->get_id(), $response );
	}

	/**
	 * Handle product deletion or trashing.
	 *
	 * Hooked to 'woocommerce_delete_product' and 'woocommerce_trash_product'.
	 * Notifies the API to remove the product from the remote catalog.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID of the deleted product.
	 *
	 * @return void
	 */
	public function on_product_delete( $post_id ) {
		$post_id = absint( $post_id );

		if ( 0 === $post_id ) {
			return;
		}

		$response = $this->api_client->post(
			'/api/v1/plugin/catalog/delete',
			array(
				'external_ids' => array( strval( $post_id ) ),
			)
		);

		$this->log_sync_outcome( 'on_product_delete', $post_id, $response );
	}

	/**
	 * Perform a full catalog sync.
	 *
	 * Paginates through all published WooCommerce products in batches
	 * and sends each batch to the API.
	 *
	 * @since 1.0.0
	 *
	 * @return array {
	 *     Sync results.
	 *
	 *     @type int    $total_synced  Total products sent to the API.
	 *     @type int    $total_created Products created on the remote side.
	 *     @type int    $total_updated Products updated on the remote side.
	 *     @type int    $total_errors  Number of failed batch requests.
	 *     @type string $last_sync     ISO 8601 timestamp of the sync.
	 * }
	 */
	public function sync_full_catalog() {
		$page          = 1;
		$total_synced  = 0;
		$total_created = 0;
		$total_updated = 0;
		$total_errors  = 0;

		while ( true ) {
			$products = wc_get_products(
				array(
					'limit'  => self::BATCH_SIZE,
					'page'   => $page,
					'status' => 'publish',
					'return' => 'objects',
				)
			);

			if ( empty( $products ) ) {
				break;
			}

			$payloads = array();
			foreach ( $products as $product ) {
				$payloads[] = $this->product_to_payload( $product );
			}

			$response = $this->api_client->post(
				'/api/v1/plugin/catalog/sync',
				array(
					'products' => $payloads,
				)
			);

			if ( is_wp_error( $response ) ) {
				$total_errors++;
				error_log( sprintf( '[amcp.catalog_sync] sync_full_catalog WP_Error page=%d: %s', $page, $response->get_error_message() ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			} else {
				$status_code = isset( $response['status'] ) ? absint( $response['status'] ) : 0;

				if ( $status_code >= 400 || $status_code < 200 ) {
					$total_errors++;
					error_log( sprintf( '[amcp.catalog_sync] sync_full_catalog HTTP %d page=%d batch_size=%d', $status_code, $page, count( $payloads ) ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				} else {
					$total_synced += count( $payloads );

					$body = isset( $response['body'] ) ? $response['body'] : $response;
					$data = is_array( $body ) && isset( $body['data'] ) ? $body['data'] : $body;

					if ( is_array( $data ) && isset( $data['created'] ) ) {
						$total_created += absint( $data['created'] );
					}
					if ( is_array( $data ) && isset( $data['updated'] ) ) {
						$total_updated += absint( $data['updated'] );
					}
				}
			}

			// If fewer products than batch size, we've reached the end.
			if ( count( $products ) < self::BATCH_SIZE ) {
				break;
			}

			$page++;
		}

		// F3.T6 — Gap #6 honest sync error reporting.
		// `agenticmcp_last_sync_attempt` always updates (audit trail).
		// `agenticmcp_last_sync_success` only updates when all batches succeeded.
		// `agenticmcp_last_sync` (legacy) is kept in sync with success for backward-compat UI.
		$last_sync_attempt  = gmdate( 'c' );
		$is_full_success    = ( 0 === $total_errors );
		$prior_last_success = get_option( 'agenticmcp_last_sync_success', null );
		$last_sync_success  = $is_full_success ? $last_sync_attempt : $prior_last_success;

		update_option( 'agenticmcp_last_sync_attempt', $last_sync_attempt );

		if ( $is_full_success ) {
			update_option( 'agenticmcp_last_sync_success', $last_sync_attempt );
			update_option( 'agenticmcp_last_sync', $last_sync_attempt );
		}

		return array(
			'total_synced'      => $total_synced,
			'total_created'     => $total_created,
			'total_updated'     => $total_updated,
			'total_errors'      => $total_errors,
			'last_sync_attempt' => $last_sync_attempt,
			'last_sync_success' => $last_sync_success,
			// Legacy field — equals last_sync_success when all batches passed,
			// otherwise null (preserves prior behaviour for already-green stores).
			'last_sync'         => $is_full_success ? $last_sync_attempt : null,
		);
	}

	/**
	 * AJAX handler for manual catalog sync from the settings page.
	 *
	 * Verifies nonce and capability before executing the sync.
	 * Returns JSON response with sync results.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function ajax_sync_catalog() {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['nonce'] ) ),
			'agenticmcp_sync_nonce'
		) ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid security token.', 'agenticmcpstores' ) ),
				403
			);
			return;
		}

		// Verify user capability.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Insufficient permissions.', 'agenticmcpstores' ) ),
				403
			);
			return;
		}

		$results = $this->sync_full_catalog();

		wp_send_json_success( $results );
	}

	/**
	 * Log the outcome of a fire-and-forget sync request.
	 *
	 * Replaces the previous silent fire-and-forget behaviour so merchants
	 * have a trail when product/stock/delete syncs fail at transport or
	 * HTTP level. Does not throw — sync events must never break the
	 * WooCommerce hook chain.
	 *
	 * @since 1.0.0
	 *
	 * @param string         $context  Caller name (e.g. 'on_product_save').
	 * @param int            $entity_id Product / post ID being synced.
	 * @param array|WP_Error $response  Result returned by AgenticMCP_Api_Client::post().
	 *
	 * @return void
	 */
	private function log_sync_outcome( $context, $entity_id, $response ) {
		if ( is_wp_error( $response ) ) {
			error_log( sprintf( '[amcp.catalog_sync] %s WP_Error entity=%d code=%s message=%s', $context, absint( $entity_id ), $response->get_error_code(), $response->get_error_message() ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return;
		}

		if ( ! is_array( $response ) ) {
			error_log( sprintf( '[amcp.catalog_sync] %s unexpected response type entity=%d', $context, absint( $entity_id ) ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return;
		}

		$status_code = isset( $response['status'] ) ? absint( $response['status'] ) : 0;

		if ( $status_code < 200 || $status_code >= 400 ) {
			error_log( sprintf( '[amcp.catalog_sync] %s HTTP %d entity=%d', $context, $status_code, absint( $entity_id ) ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		}
	}

	/**
	 * Convert a WooCommerce product to the API payload format.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Product $product WooCommerce product object.
	 *
	 * @return array Associative array suitable for the catalog sync API.
	 */
	private function product_to_payload( $product ) {
		$payload = array(
			'external_id'       => strval( $product->get_id() ),
			'name'              => $product->get_name(),
			'sku'               => $product->get_sku(),
			'price'             => $product->get_price(),
			'stock_status'      => $product->get_stock_status(),
			'stock_quantity'    => $product->get_stock_quantity(),
			'permalink'         => $product->get_permalink(),
			'description'       => wp_strip_all_tags( $product->get_description() ),
			'short_description' => wp_strip_all_tags( $product->get_short_description() ),
		);

		// Compare-at price: only include when regular price differs from sale price.
		$regular_price = $product->get_regular_price();
		$sale_price    = $product->get_sale_price();

		if ( '' !== $regular_price && '' !== $sale_price && $regular_price !== $sale_price ) {
			$payload['compare_at_price'] = $regular_price;
		}

		// Categories.
		$payload['categories'] = $this->get_product_categories( $product );

		// Images (featured + gallery).
		$payload['images'] = $this->get_product_images( $product );

		// F2.T5 — Gap #7: currency / tags / product_type ingest.
		// Required by enforcement rules (R027 giftcard discovery,
		// tag-based product matching) and multi-currency stores.
		if ( function_exists( 'get_woocommerce_currency' ) ) {
			$payload['currency'] = get_woocommerce_currency();
		}

		// Product tag term names (e.g. ["sale", "limited-edition"]).
		$tag_terms = get_the_terms( $product->get_id(), 'product_tag' );
		if ( is_array( $tag_terms ) ) {
			$payload['tags'] = wp_list_pluck( $tag_terms, 'name' );
		} else {
			$payload['tags'] = array();
		}

		// Product type: "simple" | "variable" | "subscription" |
		// "giftcard" (when WC Gift Cards plugin installed) | etc.
		$payload['product_type'] = $product->get_type();

		return $payload;
	}

	/**
	 * Get product categories as an array of {id, name} objects.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Product $product WooCommerce product object.
	 *
	 * @return array Array of associative arrays with 'id' and 'name' keys.
	 */
	private function get_product_categories( $product ) {
		$category_ids = $product->get_category_ids();
		$categories   = array();

		foreach ( $category_ids as $cat_id ) {
			$term = get_term( absint( $cat_id ), 'product_cat' );

			if ( $term instanceof \WP_Term ) {
				$categories[] = array(
					'id'   => $term->term_id,
					'name' => $term->name,
				);
			}
		}

		return $categories;
	}

	/**
	 * Get product images (featured image first, then gallery).
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Product $product WooCommerce product object.
	 *
	 * @return array Array of associative arrays with 'src' and 'alt' keys.
	 */
	private function get_product_images( $product ) {
		$images            = array();
		$featured_image_id = $product->get_image_id();

		if ( $featured_image_id ) {
			$image = $this->attachment_to_image( absint( $featured_image_id ) );

			if ( null !== $image ) {
				$images[] = $image;
			}
		}

		$gallery_ids = $product->get_gallery_image_ids();

		foreach ( $gallery_ids as $attachment_id ) {
			$image = $this->attachment_to_image( absint( $attachment_id ) );

			if ( null !== $image ) {
				$images[] = $image;
			}
		}

		return $images;
	}

	/**
	 * Convert a WordPress attachment ID to an image array.
	 *
	 * @since 1.0.0
	 *
	 * @param int $attachment_id WordPress attachment post ID.
	 *
	 * @return array|null Array with 'src' and 'alt' keys, or null if not found.
	 */
	private function attachment_to_image( $attachment_id ) {
		$src = wp_get_attachment_url( $attachment_id );

		if ( ! $src ) {
			return null;
		}

		$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

		return array(
			'src' => esc_url( $src ),
			'alt' => sanitize_text_field( $alt ),
		);
	}
}
