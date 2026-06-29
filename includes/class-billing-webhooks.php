<?php
/**
 * Billing webhook receiver for plan/subscription changes.
 *
 * @package AgenticMCPStores
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AgenticMCP_Billing_Webhooks
 *
 * Receives webhook events from the AgenticMCPStores backend
 * to keep the local tier/plan state in sync. Verifies HMAC-SHA256
 * signatures and provides idempotent event processing.
 *
 * @since 1.0.0
 */
class AgenticMCP_Billing_Webhooks {

	/**
	 * REST namespace for webhook routes.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const REST_NAMESPACE = 'agenticmcp/v1';

	/**
	 * REST route path for billing webhooks.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const REST_ROUTE = '/billing-webhook';

	/**
	 * Webhook signature header name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const SIGNATURE_HEADER = 'X-AgenticMCP-Signature';

	/**
	 * Option key for the webhook secret.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const SECRET_OPTION = 'agenticmcp_webhook_secret';

	/**
	 * Option key for the last processed event ID (idempotency).
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const LAST_EVENT_OPTION = 'agenticmcp_last_billing_event_id';

	/**
	 * Option key for the subscription tier.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const TIER_OPTION = 'agenticmcp_tier';

	/**
	 * Option key for trial end date.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const TRIAL_ENDS_OPTION = 'agenticmcp_trial_ends_at';

	/**
	 * Option key for the previous tier (before downgrade/deactivation).
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const PREVIOUS_TIER_OPTION = 'agenticmcp_previous_tier';

	/**
	 * Supported event types and their handler methods.
	 *
	 * @since 1.0.0
	 * @var array<string, string>
	 */
	private const EVENT_HANDLERS = array(
		'subscription.activated'   => 'handle_subscription_activated',
		'subscription.deactivated' => 'handle_subscription_deactivated',
		'subscription.upgraded'    => 'handle_subscription_upgraded',
		'subscription.downgraded'  => 'handle_subscription_downgraded',
		'subscription.trial_started' => 'handle_subscription_trial_started',
	);

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'maybe_show_expiration_notice' ) );
	}

	/**
	 * Register the REST API route for billing webhooks.
	 *
	 * Hooked to rest_api_init via class-plugin.php.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_ROUTE,
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'handle_webhook' ),
				'permission_callback' => array( $this, 'verify_webhook_signature' ),
			)
		);
	}

	/**
	 * Verify the webhook request signature using HMAC-SHA256.
	 *
	 * Reads the X-AgenticMCP-Signature header and compares it against
	 * a locally computed HMAC of the raw request body.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The incoming REST request.
	 * @return bool True if the signature is valid, false otherwise.
	 */
	public function verify_webhook_signature( $request ) {
		$signature = $request->get_header( 'x_agenticmcp_signature' );

		if ( empty( $signature ) ) {
			return false;
		}

		$secret = get_option( self::SECRET_OPTION, '' );

		if ( empty( $secret ) ) {
			return false;
		}

		$raw_body        = $request->get_body();
		$expected_signature = hash_hmac( 'sha256', $raw_body, $secret );

		return hash_equals( $expected_signature, $signature );
	}

	/**
	 * Handle the incoming billing webhook event.
	 *
	 * Parses the JSON body, checks for idempotency via event_id,
	 * and dispatches to the appropriate handler based on event_type.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_REST_Request $request The incoming REST request.
	 * @return WP_REST_Response The response to send back.
	 */
	public function handle_webhook( $request ) {
		$body = $request->get_json_params();

		if ( empty( $body ) || ! is_array( $body ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'error'   => 'Invalid request body.',
				),
				400
			);
		}

		$event_type = isset( $body['event_type'] ) ? sanitize_text_field( $body['event_type'] ) : '';
		$event_id   = isset( $body['event_id'] ) ? sanitize_text_field( $body['event_id'] ) : '';

		if ( empty( $event_type ) || empty( $event_id ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'error'   => 'Missing event_type or event_id.',
				),
				400
			);
		}

		// Idempotency check: skip already-processed events.
		$last_event_id = get_option( self::LAST_EVENT_OPTION, '' );

		if ( $event_id === $last_event_id ) {
			return new WP_REST_Response(
				array( 'success' => true, 'message' => 'Event already processed.' ),
				200
			);
		}

		// Check if we have a handler for this event type.
		if ( ! isset( self::EVENT_HANDLERS[ $event_type ] ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'error'   => 'Unsupported event type.',
				),
				422
			);
		}

		$handler = self::EVENT_HANDLERS[ $event_type ];
		$data    = isset( $body['data'] ) && is_array( $body['data'] ) ? $body['data'] : array();

		$this->$handler( $data );

		// Mark this event as processed.
		update_option( self::LAST_EVENT_OPTION, $event_id );

		/**
		 * Fires after a billing webhook event has been successfully processed.
		 *
		 * @since 1.0.0
		 *
		 * @param string $event_type The event type that was processed.
		 * @param string $event_id   The unique event identifier.
		 * @param array  $data       The event payload data.
		 */
		do_action( 'agenticmcp_billing_webhook_processed', $event_type, $event_id, $data );

		return new WP_REST_Response(
			array( 'success' => true ),
			200
		);
	}

	/**
	 * Handle subscription.activated event.
	 *
	 * Sets the subscription tier to the value provided in the event data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Event payload data containing 'tier'.
	 * @return void
	 */
	private function handle_subscription_activated( $data ) {
		$tier = isset( $data['tier'] ) ? sanitize_text_field( $data['tier'] ) : '';

		if ( ! empty( $tier ) ) {
			update_option( self::TIER_OPTION, $tier );
		}
	}

	/**
	 * Handle subscription.deactivated event.
	 *
	 * Stores the current tier as the previous tier (for admin notices),
	 * then resets the tier to FREE.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Event payload data (unused for deactivation).
	 * @return void
	 */
	private function handle_subscription_deactivated( $data ) {
		$current_tier = get_option( self::TIER_OPTION, 'FREE' );

		if ( 'FREE' !== strtoupper( $current_tier ) ) {
			update_option( self::PREVIOUS_TIER_OPTION, $current_tier );
		}

		update_option( self::TIER_OPTION, 'FREE' );
	}

	/**
	 * Handle subscription.upgraded event.
	 *
	 * Sets the subscription tier to the new upgraded value.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Event payload data containing 'tier'.
	 * @return void
	 */
	private function handle_subscription_upgraded( $data ) {
		$tier = isset( $data['tier'] ) ? sanitize_text_field( $data['tier'] ) : '';

		if ( ! empty( $tier ) ) {
			update_option( self::TIER_OPTION, $tier );
		}
	}

	/**
	 * Handle subscription.downgraded event.
	 *
	 * Sets the subscription tier to the new downgraded value.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Event payload data containing 'tier'.
	 * @return void
	 */
	private function handle_subscription_downgraded( $data ) {
		$tier = isset( $data['tier'] ) ? sanitize_text_field( $data['tier'] ) : '';

		if ( ! empty( $tier ) ) {
			update_option( self::TIER_OPTION, $tier );
		}
	}

	/**
	 * Handle subscription.trial_started event.
	 *
	 * Sets the subscription tier and records the trial end date.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Event payload data containing 'tier' and 'trial_ends_at'.
	 * @return void
	 */
	private function handle_subscription_trial_started( $data ) {
		$tier = isset( $data['tier'] ) ? sanitize_text_field( $data['tier'] ) : '';

		if ( ! empty( $tier ) ) {
			update_option( self::TIER_OPTION, $tier );
		}

		$trial_ends_at = isset( $data['trial_ends_at'] ) ? sanitize_text_field( $data['trial_ends_at'] ) : '';

		if ( ! empty( $trial_ends_at ) ) {
			update_option( self::TRIAL_ENDS_OPTION, $trial_ends_at );
		}
	}

	/**
	 * Display an admin notice when a subscription has been deactivated.
	 *
	 * Shows a warning if the current tier is FREE but there was a
	 * previous paid tier, indicating the subscription has expired.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function maybe_show_expiration_notice() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$current_tier  = get_option( self::TIER_OPTION, 'FREE' );
		$previous_tier = get_option( self::PREVIOUS_TIER_OPTION, '' );

		if ( 'FREE' !== strtoupper( $current_tier ) || empty( $previous_tier ) ) {
			return;
		}

		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<strong><?php esc_html_e( 'AgenticMCPStores:', 'agenticmcpstores' ); ?></strong>
				<?php
				printf(
					/* translators: 1: previous tier name, 2: link to pricing page */
					esc_html__( 'Your %1$s subscription has expired. Your store is now on the FREE tier with limited features. %2$s to restore full functionality.', 'agenticmcpstores' ),
					'<strong>' . esc_html( strtoupper( $previous_tier ) ) . '</strong>',
					'<a href="' . esc_url( 'https://agenticmcpstores.com/en/pricing' ) . '" target="_blank" rel="noopener noreferrer">'
					. esc_html__( 'Upgrade now', 'agenticmcpstores' )
					. '</a>'
				);
				?>
			</p>
		</div>
		<?php
	}
}
