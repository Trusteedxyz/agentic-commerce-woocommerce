<?php
/**
 * Plugin settings page under WooCommerce admin menu.
 *
 * @package Trusteed
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Trusteed_Settings
 *
 * Registers the admin menu page, renders settings UI,
 * handles AJAX actions, and enqueues admin assets.
 *
 * @since 1.0.0
 */
class Trusteed_Settings {

	/**
	 * API client instance.
	 *
	 * @since 1.0.0
	 * @var Trusteed_Api_Client
	 */
	private $api_client;

	/**
	 * Settings page hook suffix.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $page_hook = '';

	/**
	 * Settings group name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const SETTINGS_GROUP = 'agenticmcp_settings';

	/**
	 * Settings page slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const PAGE_SLUG = 'agenticmcp-settings';

	/**
	 * Nonce action for settings form.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const NONCE_ACTION = 'agenticmcp_settings_nonce';

	/**
	 * Nonce action for test connection AJAX.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const TEST_NONCE_ACTION = 'agenticmcp_test_nonce';

	/**
	 * Nonce action for sync catalog AJAX.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const SYNC_NONCE_ACTION = 'agenticmcp_sync_nonce';

	/**
	 * Trusteed pricing page URL.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const PRICING_URL = 'https://trusteed.xyz/en/pricing';

	/**
	 * Nonce action for onboard AJAX.
	 *
	 * @since 1.2.0
	 * @var string
	 */
	private const ONBOARD_NONCE_ACTION = 'agenticmcp_onboard_nonce';

	/**
	 * Nonce action for disconnect AJAX.
	 *
	 * @since 1.2.0
	 * @var string
	 */
	private const DISCONNECT_NONCE_ACTION = 'agenticmcp_disconnect_nonce';

	/**
	 * Nonce action for the API-key connect AJAX.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private const CONNECT_KEY_NONCE_ACTION = 'agenticmcp_connect_key_nonce';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Trusteed_Api_Client|null $api_client Optional API client instance.
	 */
	public function __construct( $api_client = null ) {
		$this->api_client = $api_client instanceof Trusteed_Api_Client
			? $api_client
			: new Trusteed_Api_Client();

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'wp_ajax_agenticmcp_test_connection', array( $this, 'ajax_test_connection' ) );
		add_action( 'wp_ajax_agenticmcp_sync_catalog', array( $this, 'ajax_sync_catalog' ) );
		add_action( 'wp_ajax_agenticmcp_onboard', array( $this, 'ajax_onboard' ) );
		add_action( 'wp_ajax_agenticmcp_connect_key', array( $this, 'ajax_connect_api_key' ) );
		add_action( 'wp_ajax_agenticmcp_disconnect', array( $this, 'ajax_disconnect' ) );
		// Gap 5 — surface the config-drift notice on every admin screen so the
		// merchant cannot miss repeated 4xx responses from /v1/rules/evaluate.
		add_action( 'admin_notices', array( $this, 'maybe_render_config_drift_notice' ) );
		// F5.S3 — surface fail-closed HMAC-missing condition so merchant can
		// reconnect the plugin instead of silently dropping enforcement events.
		add_action( 'admin_notices', array( $this, 'maybe_render_hmac_missing_notice' ) );
		// F6.PHP2 / M1 — surface the stub installation_id condition so a
		// post-upgrade store without re-onboard does not silently get
		// every enforcement call rejected.
		add_action( 'admin_notices', array( $this, 'maybe_render_enforcement_stub_notice' ) );
	}

	/**
	 * F6.PHP2 / M1 — render an admin notice when the plugin booted with
	 * the sentinel `STUB_INSTALLATION_ID`, which indicates the merchant
	 * upgraded the plugin without re-running onboarding. The notice is
	 * NOT user-dismissible: it is cleared only after a successful
	 * onboard (see ajax_onboard).
	 *
	 * @since 1.8.0
	 *
	 * @return void
	 */
	public function maybe_render_enforcement_stub_notice(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		if ( ! class_exists( 'Trusteed_Plugin' ) ) {
			return;
		}
		if ( ! get_option( Trusteed_Plugin::NOTICE_OPTION_INSTALLATION_STUB ) ) {
			return;
		}
		?>
		<div class="notice notice-error">
			<p>
				<strong><?php esc_html_e( 'Trusteed — enforcement is not configured. Click Reconnect to enable agent verification.', 'trusteed-for-woocommerce' ); ?></strong>
			</p>
			<p>
				<em><?php esc_html_e( 'Trusteed — el enforcement no está configurado. Haz clic en Reconectar para activar la verificación de agentes.', 'trusteed-for-woocommerce' ); ?></em>
			</p>
		</div>
		<?php
	}

	/**
	 * F5.S3 — render an admin notice when the agent-event webhook (or any
	 * other HMAC-signed outbound call) was aborted because the plugin's
	 * `enforcement_hmac_secret` is empty. Set by Amcp_Agent_Event_Webhook
	 * (and sibling clients) as `update_option( NOTICE_OPTION_HMAC_MISSING, 1 )`.
	 *
	 * Dismissible — the option is cleared the next time the merchant visits
	 * the settings page or successfully reconnects.
	 *
	 * @since 1.7.0
	 *
	 * @return void
	 */
	public function maybe_render_hmac_missing_notice(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		if ( ! class_exists( 'Amcp_Agent_Event_Webhook' ) ) {
			return;
		}
		if ( ! get_option( Amcp_Agent_Event_Webhook::NOTICE_OPTION_HMAC_MISSING ) ) {
			return;
		}
		?>
		<div class="notice notice-error is-dismissible">
			<p><strong><?php esc_html_e( 'Trusteed — integración incompleta', 'trusteed-for-woocommerce' ); ?></strong></p>
			<p>
				<?php
				esc_html_e(
					'No se ha podido firmar una llamada al backend porque falta el HMAC secret de la instalación. Tu integración Trusteed no está completamente configurada. Reconecta el plugin para restaurar la sincronización de eventos de agente.',
					'trusteed-for-woocommerce'
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Gap 5 — render an admin notice when the enforcement API has been
	 * returning HTTP 4xx repeatedly (set via Amcp_Enforcement_Api_Client).
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function maybe_render_config_drift_notice(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		if ( ! get_transient( Amcp_Enforcement_Api_Client::EVAL_4XX_NOTICE_TRANSIENT ) ) {
			return;
		}
		?>
		<div class="notice notice-warning is-dismissible">
			<p><strong><?php esc_html_e( 'Trusteed — posible configuración incorrecta', 'trusteed-for-woocommerce' ); ?></strong></p>
			<p>
				<?php
				esc_html_e(
					'El evaluador remoto está devolviendo errores 4xx con frecuencia. Esto suele indicar credenciales o identificadores incorrectos. Verifica los siguientes valores en la pestaña de Trusteed:',
					'trusteed-for-woocommerce'
				);
				?>
			</p>
			<ul style="list-style:disc;padding-left:20px;">
				<li><code>installationId</code></li>
				<li><code>merchantId</code></li>
				<li><code>hmac_secret</code></li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Register the submenu page under WooCommerce.
	 *
	 * Hooked to admin_menu via class-plugin.php.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_menu() {
		$this->page_hook = add_submenu_page(
			'woocommerce',
			__( 'Trusteed', 'trusteed-for-woocommerce' ),
			__( 'Trusteed', 'trusteed-for-woocommerce' ),
			'manage_woocommerce',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register settings with the WordPress Settings API.
	 * Only called to keep backward compatibility with options that may
	 * already be stored. Onboarding now uses AJAX (ajax_onboard).
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_settings() {
		// Keep registration so existing stored options are not orphaned.
		register_setting(
			self::SETTINGS_GROUP,
			'agenticmcp_api_key',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);
		register_setting(
			self::SETTINGS_GROUP,
			'amcp_embed_wp_secret',
			array(
				'type'    => 'string',
				'default' => '',
			)
		);
	}

	/**
	 * Sanitize the embed WP secret on save.
	 *
	 * @since 1.1.0
	 *
	 * @param mixed $value Raw submitted value.
	 * @return string Sanitized secret or existing stored value.
	 */
	public function sanitize_embed_secret( $value ) {
		$sanitized = sanitize_text_field( (string) $value );

		if ( '' === $sanitized ) {
			return (string) get_option( 'amcp_embed_wp_secret', '' );
		}

		return Amcp_Crypto_Helper::encrypt( $sanitized );
	}

	/**
	 * Render the full settings page.
	 *
	 * Not-connected: shows email + password onboarding form.
	 * Connected: shows store status, actions, and disconnect button.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$store_slug   = get_option( 'agenticmcp_store_slug', '' );
		$mcp_endpoint = get_option( 'agenticmcp_mcp_endpoint', '' );
		$tier         = get_option( 'agenticmcp_tier', 'FREE' );
		$last_sync    = get_option( 'agenticmcp_last_sync', '' );
		$is_connected = ! empty( $store_slug );
		?>
		<div class="wrap agenticmcp-settings">
			<h1><?php esc_html_e( 'Trusteed para WooCommerce', 'trusteed-for-woocommerce' ); ?></h1>

			<?php if ( ! $is_connected ) : ?>
			<!-- ── Formulario de conexión ──────────────────────────────────── -->
			<div class="agenticmcp-section" role="region" aria-label="<?php esc_attr_e( 'Conectar tienda', 'trusteed-for-woocommerce' ); ?>">
				<h2><?php esc_html_e( 'Conecta tu tienda a Trusteed', 'trusteed-for-woocommerce' ); ?></h2>
				<p><?php esc_html_e( 'Introduce tu correo y contraseña de Trusteed. Si no tienes cuenta la crearemos automáticamente.', 'trusteed-for-woocommerce' ); ?></p>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="amcp-onboard-email"><?php esc_html_e( 'Tu correo', 'trusteed-for-woocommerce' ); ?></label>
							</th>
							<td>
								<input
									type="email"
									id="amcp-onboard-email"
									class="regular-text"
									autocomplete="email"
									placeholder="paco@tusandias.es"
								/>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="amcp-onboard-password"><?php esc_html_e( 'Contraseña', 'trusteed-for-woocommerce' ); ?></label>
							</th>
							<td>
								<input
									type="password"
									id="amcp-onboard-password"
									class="regular-text"
									autocomplete="new-password"
									placeholder="<?php esc_attr_e( 'Mínimo 8 caracteres', 'trusteed-for-woocommerce' ); ?>"
								/>
								<p class="description">
									<?php esc_html_e( 'Si ya tienes cuenta en Trusteed usa la misma contraseña.', 'trusteed-for-woocommerce' ); ?>
								</p>
							</td>
						</tr>
					</tbody>
				</table>

				<div class="agenticmcp-actions">
					<button
						type="button"
						id="amcp-onboard-btn"
						class="button button-primary"
					>
						<?php esc_html_e( 'Conectar mi tienda', 'trusteed-for-woocommerce' ); ?>
					</button>
					<span id="amcp-onboard-result" class="agenticmcp-result" aria-live="polite"></span>
				</div>
			</div>

			<!-- ── Alternativa: conectar con API key ────────────────────────── -->
			<div class="agenticmcp-section" role="region" aria-label="<?php esc_attr_e( 'Conectar con API key', 'trusteed-for-woocommerce' ); ?>">
				<h2><?php esc_html_e( '¿Prefieres no usar contraseña?', 'trusteed-for-woocommerce' ); ?></h2>
				<p>
					<?php
					printf(
						/* translators: %s: link to the Trusteed dashboard. */
						esc_html__( 'Pega una API key de tu %s y conecta sin introducir tu contraseña en WordPress.', 'trusteed-for-woocommerce' ),
						'<a href="https://trusteed.xyz/developers" target="_blank" rel="noopener noreferrer">' . esc_html__( 'panel de Trusteed', 'trusteed-for-woocommerce' ) . '</a>'
					);
					?>
				</p>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="amcp-connect-key"><?php esc_html_e( 'API key', 'trusteed-for-woocommerce' ); ?></label>
							</th>
							<td>
								<input
									type="password"
									id="amcp-connect-key"
									class="regular-text"
									autocomplete="off"
									spellcheck="false"
									placeholder="sk_live_..."
								/>
							</td>
						</tr>
					</tbody>
				</table>

				<div class="agenticmcp-actions">
					<button
						type="button"
						id="amcp-connect-key-btn"
						class="button button-secondary"
					>
						<?php esc_html_e( 'Conectar con API key', 'trusteed-for-woocommerce' ); ?>
					</button>
					<span id="amcp-connect-key-result" class="agenticmcp-result" aria-live="polite"></span>
				</div>
			</div>

			<?php else : ?>
			<!-- ── Estado de conexión ──────────────────────────────────────── -->
			<div class="agenticmcp-section" role="region" aria-label="<?php esc_attr_e( 'Estado de conexión', 'trusteed-for-woocommerce' ); ?>">
				<h2><?php esc_html_e( 'Estado de la conexión', 'trusteed-for-woocommerce' ); ?></h2>
				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row"><?php esc_html_e( 'Estado', 'trusteed-for-woocommerce' ); ?></th>
							<td>
								<span class="agenticmcp-badge agenticmcp-badge--success">
									✓ <?php esc_html_e( 'Conectada', 'trusteed-for-woocommerce' ); ?>
								</span>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Plan activo', 'trusteed-for-woocommerce' ); ?></th>
							<td>
								<span class="agenticmcp-badge agenticmcp-badge--info">
									<?php echo esc_html( strtoupper( $tier ) ); ?>
								</span>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Endpoint MCP', 'trusteed-for-woocommerce' ); ?></th>
							<td>
								<input
									type="url"
									class="large-text"
									value="<?php echo esc_url( $mcp_endpoint ); ?>"
									readonly
									aria-readonly="true"
								/>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Última sincronización', 'trusteed-for-woocommerce' ); ?></th>
							<td>
								<?php if ( ! empty( $last_sync ) ) : ?>
									<time datetime="<?php echo esc_attr( $last_sync ); ?>">
										<?php echo esc_html( $last_sync ); ?>
									</time>
								<?php else : ?>
									<em><?php esc_html_e( 'Nunca', 'trusteed-for-woocommerce' ); ?></em>
								<?php endif; ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- ── Acciones ───────────────────────────────────────────────── -->
			<div class="agenticmcp-section" role="region" aria-label="<?php esc_attr_e( 'Acciones', 'trusteed-for-woocommerce' ); ?>">
				<h2><?php esc_html_e( 'Acciones', 'trusteed-for-woocommerce' ); ?></h2>
				<div class="agenticmcp-actions">
					<button type="button" id="agenticmcp-test-connection" class="button button-secondary">
						<?php esc_html_e( 'Probar conexión', 'trusteed-for-woocommerce' ); ?>
					</button>
					<span id="agenticmcp-connection-result" class="agenticmcp-result" aria-live="polite"></span>
				</div>
				<div class="agenticmcp-actions">
					<button type="button" id="agenticmcp-sync-catalog" class="button button-secondary">
						<?php esc_html_e( 'Sincronizar catálogo ahora', 'trusteed-for-woocommerce' ); ?>
					</button>
					<span id="agenticmcp-sync-result" class="agenticmcp-result" aria-live="polite"></span>
				</div>
				<div class="agenticmcp-actions" style="margin-top:16px;border-top:1px solid #ddd;padding-top:16px;">
					<button type="button" id="amcp-disconnect-btn" class="button button-link-delete">
						<?php esc_html_e( 'Desconectar tienda', 'trusteed-for-woocommerce' ); ?>
					</button>
					<span id="amcp-disconnect-result" class="agenticmcp-result" aria-live="polite"></span>
				</div>
			</div>

			<?php $this->render_enforcement_section(); ?>
			<?php $this->render_upgrade_cta( $tier ); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render the spec-048 enforcement controls.
	 *
	 * Exposes the `amcp_failure_mode` toggle: 'enforce' (fail-closed,
	 * INDETERMINATE → BLOCK) vs 'observe' (fail-open + telemetry only).
	 * Default is 'enforce'. Operators flip to 'observe' during canary
	 * burn-in, switch back once burn-in passes.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	private function render_enforcement_section(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// Persist on POST (nonce-guarded, capability-guarded).
		if (
			isset( $_POST['amcp_failure_mode_nonce'] ) // phpcs:ignore WordPress.Security.NonceVerification
			&& wp_verify_nonce(
				sanitize_text_field( wp_unslash( $_POST['amcp_failure_mode_nonce'] ) ), // phpcs:ignore WordPress.Security.NonceVerification
				'amcp_failure_mode_save'
			)
			&& isset( $_POST['amcp_failure_mode'] )
		) {
			$submitted = sanitize_text_field( wp_unslash( $_POST['amcp_failure_mode'] ) );
			$value     = 'observe' === $submitted ? 'observe' : 'enforce';
			update_option( 'amcp_failure_mode', $value );
			echo '<div class="notice notice-success is-dismissible"><p>'
				. esc_html__( 'Modo de fallo actualizado. Recarga el plugin (deactivate + activate) para aplicar.', 'trusteed-for-woocommerce' )
				. '</p></div>';
		}

		$current = (string) get_option( 'amcp_failure_mode', 'enforce' );
		$current = 'observe' === $current ? 'observe' : 'enforce';
		?>
		<div class="agenticmcp-section" role="region" aria-label="<?php esc_attr_e( 'Enforcement', 'trusteed-for-woocommerce' ); ?>" style="margin-top:24px;">
			<h2><?php esc_html_e( 'Enforcement de checkout (Spec-048)', 'trusteed-for-woocommerce' ); ?></h2>
			<p><?php esc_html_e( 'Cuando el evaluador remoto no responde (timeout, 5xx, payload inválido), el plugin debe decidir si bloquear el checkout o permitirlo y registrar telemetría.', 'trusteed-for-woocommerce' ); ?></p>
			<form method="post" action="">
				<?php wp_nonce_field( 'amcp_failure_mode_save', 'amcp_failure_mode_nonce' ); ?>
				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row"><?php esc_html_e( 'Modo de fallo', 'trusteed-for-woocommerce' ); ?></th>
							<td>
								<label style="display:block;margin-bottom:8px;">
									<input type="radio" name="amcp_failure_mode" value="enforce" <?php checked( $current, 'enforce' ); ?> />
									<strong><?php esc_html_e( 'Enforce (recomendado)', 'trusteed-for-woocommerce' ); ?></strong> —
									<?php esc_html_e( 'fallos del evaluador bloquean el checkout (fail-closed).', 'trusteed-for-woocommerce' ); ?>
								</label>
								<label style="display:block;">
									<input type="radio" name="amcp_failure_mode" value="observe" <?php checked( $current, 'observe' ); ?> />
									<strong><?php esc_html_e( 'Observe (solo canary)', 'trusteed-for-woocommerce' ); ?></strong> —
									<?php esc_html_e( 'permite el checkout y envía telemetría a Prometheus (enforcement_api_fail_total). Usar solo durante burn-in.', 'trusteed-for-woocommerce' ); ?>
								</label>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Guardar modo', 'trusteed-for-woocommerce' ); ?></button>
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the upgrade CTA banner when the store is on the FREE tier.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tier Current subscription tier.
	 * @return void
	 */
	private function render_upgrade_cta( $tier ) {
		if ( 'FREE' !== strtoupper( $tier ) ) {
			return;
		}
		?>
		<div class="agenticmcp-upgrade-cta" role="complementary" aria-label="<?php esc_attr_e( 'Upgrade your plan', 'trusteed-for-woocommerce' ); ?>">
			<div class="agenticmcp-upgrade-cta__content">
				<h3><?php esc_html_e( 'Unlock More Features', 'trusteed-for-woocommerce' ); ?></h3>
				<p>
					<?php esc_html_e( 'Upgrade to a paid plan to get higher rate limits, advanced analytics, and priority support for your AI agent integrations.', 'trusteed-for-woocommerce' ); ?>
				</p>
				<a
					href="<?php echo esc_url( self::PRICING_URL ); ?>"
					class="button button-primary"
					target="_blank"
					rel="noopener noreferrer"
				>
					<?php esc_html_e( 'View Plans &amp; Pricing', 'trusteed-for-woocommerce' ); ?>
					<span class="screen-reader-text"><?php esc_html_e( '(opens in new tab)', 'trusteed-for-woocommerce' ); ?></span>
				</a>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX handler: One-shot onboarding from WC admin panel.
	 *
	 * Calls POST /api/v1/plugin/onboard, saves all credentials automatically.
	 * Paco never leaves WooCommerce.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function ajax_onboard() {
		check_ajax_referer( self::ONBOARD_NONCE_ACTION );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Sin permisos.', 'trusteed-for-woocommerce' ) ), 403 );
		}

		$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$password = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : '';

		if ( empty( $email ) || empty( $password ) ) {
			wp_send_json_error( array( 'message' => __( 'Correo y contraseña son obligatorios.', 'trusteed-for-woocommerce' ) ) );
		}

		$site_url   = get_site_url();
		$wc_version = defined( 'WC_VERSION' ) ? WC_VERSION : '0.0.0';

		$client = new Trusteed_Api_Client();
		$result = $client->onboard( $email, $password, $site_url, $wc_version );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		// Guardar todo automáticamente — Paco no toca nada más.
		if ( ! empty( $result['api_key'] ) ) {
			Trusteed_Api_Client::store_api_key( $result['api_key'] );
		}
		if ( ! empty( $result['merchant_id'] ) ) {
			update_option( 'amcp_merchant_id', sanitize_text_field( $result['merchant_id'] ) );
		}
		if ( ! empty( $result['embed_wp_secret'] ) ) {
			update_option( 'amcp_embed_wp_secret', Amcp_Crypto_Helper::encrypt( sanitize_text_field( $result['embed_wp_secret'] ) ) );
		}
		if ( ! empty( $result['store_slug'] ) ) {
			update_option( 'agenticmcp_store_slug', sanitize_text_field( $result['store_slug'] ) );
		}
		if ( ! empty( $result['mcp_endpoint'] ) ) {
			update_option( 'agenticmcp_mcp_endpoint', esc_url_raw( $result['mcp_endpoint'] ) );
		}
		if ( ! empty( $result['tier'] ) ) {
			update_option( 'agenticmcp_tier', sanitize_text_field( $result['tier'] ) );
		}
		// F1.T1 — enforcement HMAC + Woo webhook secrets. Backward-compat: if a
		// legacy backend omits these fields the plugin keeps its previous
		// (broken) fallback (`amcp_enforcement_installation_id` UUID stub +
		// empty HMAC). New backends always populate them.
		if ( ! empty( $result['enforcement_installation_id'] ) ) {
			$new_install_id = sanitize_text_field( $result['enforcement_installation_id'] );
			update_option(
				'amcp_enforcement_installation_id',
				$new_install_id
			);
			// F6.PHP2 / M1 — clear stub-id admin notice once a real
			// installation id is provisioned by the backend.
			if (
				class_exists( 'Trusteed_Plugin' )
				&& Trusteed_Plugin::STUB_INSTALLATION_ID !== $new_install_id
				&& '' !== $new_install_id
			) {
				delete_option( Trusteed_Plugin::NOTICE_OPTION_INSTALLATION_STUB );
			}
		}
		if ( ! empty( $result['enforcement_hmac_secret'] ) ) {
			update_option(
				'amcp_enforcement_hmac_secret',
				Amcp_Crypto_Helper::encrypt(
					sanitize_text_field( $result['enforcement_hmac_secret'] )
				)
			);
		}
		if ( ! empty( $result['woo_webhook_secret'] ) ) {
			update_option(
				'amcp_woo_webhook_secret',
				Amcp_Crypto_Helper::encrypt(
					sanitize_text_field( $result['woo_webhook_secret'] )
				)
			);
		}

		wp_send_json_success(
			array(
				'message'           => $result['already_connected']
					? __( '¡Tienda reconectada con éxito!', 'trusteed-for-woocommerce' )
					: __( '¡Tienda conectada con éxito!', 'trusteed-for-woocommerce' ),
				'tier'              => strtoupper( sanitize_text_field( $result['tier'] ) ),
				'mcp_endpoint'      => esc_url_raw( $result['mcp_endpoint'] ),
				'already_connected' => $result['already_connected'],
			)
		);
	}

	/**
	 * AJAX handler: connect the store using a pasted Trusteed API key.
	 *
	 * Alternative to the email + password onboarding flow: the merchant copies
	 * an API key from the Trusteed dashboard and pastes it here, so no account
	 * password ever enters WordPress. The key is validated against
	 * POST /api/v1/plugin/register before it is persisted. This path provisions
	 * the core catalog + agent connection; the Trust Center embed secret and
	 * enforcement HMAC are only provisioned by the email onboarding flow.
	 *
	 * @since 2.0.0
	 *
	 * @return void Sends a JSON response and exits.
	 */
	public function ajax_connect_api_key() {
		check_ajax_referer( self::CONNECT_KEY_NONCE_ACTION );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Sin permisos.', 'trusteed-for-woocommerce' ) ), 403 );
		}

		$api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : '';

		if ( empty( $api_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Introduce tu API key.', 'trusteed-for-woocommerce' ) ) );
		}

		$site_url   = get_site_url();
		$wc_version = defined( 'WC_VERSION' ) ? WC_VERSION : '0.0.0';

		$client = new Trusteed_Api_Client( $api_key );
		$result = $client->register_store( $site_url, $wc_version );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		// Key validated by the backend — persist it (encrypted at rest) and metadata.
		Trusteed_Api_Client::store_api_key( $api_key );
		if ( ! empty( $result['merchant_id'] ) ) {
			update_option( 'amcp_merchant_id', sanitize_text_field( $result['merchant_id'] ) );
		}
		if ( ! empty( $result['store_slug'] ) ) {
			update_option( 'agenticmcp_store_slug', sanitize_text_field( $result['store_slug'] ) );
		}
		if ( ! empty( $result['mcp_endpoint'] ) ) {
			update_option( 'agenticmcp_mcp_endpoint', esc_url_raw( $result['mcp_endpoint'] ) );
		}
		if ( ! empty( $result['tier'] ) ) {
			update_option( 'agenticmcp_tier', sanitize_text_field( $result['tier'] ) );
		}

		wp_send_json_success(
			array(
				'message'      => __( '¡Tienda conectada con tu API key!', 'trusteed-for-woocommerce' ),
				'tier'         => strtoupper( sanitize_text_field( $result['tier'] ) ),
				'mcp_endpoint' => esc_url_raw( $result['mcp_endpoint'] ),
			)
		);
	}

	/**
	 * AJAX handler: Disconnect the store (clear all stored credentials).
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function ajax_disconnect() {
		check_ajax_referer( self::DISCONNECT_NONCE_ACTION );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Sin permisos.', 'trusteed-for-woocommerce' ) ), 403 );
		}

		// F3.T7 Gap #8 — Best-effort remote revoke BEFORE deleting local
		// credentials. If the backend is unreachable we still proceed with
		// the local wipe so the merchant can always recover the WP side.
		$api_key = Trusteed_Api_Client::get_stored_api_key();
		if ( ! empty( $api_key ) ) {
			$client         = new Trusteed_Api_Client();
			$remote_result  = $client->post( '/api/v1/plugin/disconnect', array() );
			if ( is_wp_error( $remote_result ) ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log(
					'[amcp.disconnect] remote revoke failed: ' . $remote_result->get_error_message()
				);
				// Continue — do not block local disconnect on backend failure.
			}
		}

		delete_option( 'agenticmcp_api_key' );
		delete_option( 'amcp_merchant_id' );
		delete_option( 'amcp_embed_wp_secret' );
		delete_option( 'agenticmcp_store_slug' );
		delete_option( 'agenticmcp_mcp_endpoint' );
		delete_option( 'agenticmcp_tier' );
		delete_option( 'agenticmcp_last_sync' );
		// F1.T1 — clear enforcement + webhook secrets on disconnect.
		delete_option( 'amcp_enforcement_installation_id' );
		delete_option( 'amcp_enforcement_hmac_secret' );
		delete_option( 'amcp_woo_webhook_secret' );

		wp_send_json_success( array( 'message' => __( 'Tienda desconectada.', 'trusteed-for-woocommerce' ) ) );
	}

	/**
	 * AJAX handler: Test connection to the Trusteed API.
	 *
	 * Verifies the nonce, calls test_connection() on the API client,
	 * and returns a JSON response.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function ajax_test_connection() {
		check_ajax_referer( self::TEST_NONCE_ACTION );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Permission denied.', 'trusteed-for-woocommerce' ) ),
				403
			);
		}

		$result = $this->api_client->test_connection();

		if ( is_wp_error( $result ) ) {
			$data       = $result->get_error_data();
			$latency_ms = is_array( $data ) && isset( $data['latency_ms'] )
				? (float) $data['latency_ms']
				: 0;

			wp_send_json_error(
				array(
					'message'    => $result->get_error_message(),
					'latency_ms' => $latency_ms,
				)
			);
		}

		wp_send_json_success(
			array(
				'connected'  => true,
				'latency_ms' => (float) $result['latency_ms'],
			)
		);
	}

	/**
	 * AJAX handler: Trigger a full catalog sync.
	 *
	 * Verifies the nonce and calls register_store() to trigger
	 * a sync on the backend side.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function ajax_sync_catalog() {
		check_ajax_referer( self::SYNC_NONCE_ACTION );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Permission denied.', 'trusteed-for-woocommerce' ) ),
				403
			);
		}

		$site_url   = get_site_url();
		$wc_version = defined( 'WC_VERSION' ) ? WC_VERSION : '0.0.0';

		$result = $this->api_client->register_store( $site_url, $wc_version );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error(
				array( 'message' => $result->get_error_message() )
			);
		}

		// Persist updated store data.
		if ( ! empty( $result['store_slug'] ) ) {
			update_option( 'agenticmcp_store_slug', sanitize_text_field( $result['store_slug'] ) );
		}
		if ( ! empty( $result['mcp_endpoint'] ) ) {
			update_option( 'agenticmcp_mcp_endpoint', esc_url_raw( $result['mcp_endpoint'] ) );
		}
		if ( ! empty( $result['tier'] ) ) {
			update_option( 'agenticmcp_tier', sanitize_text_field( $result['tier'] ) );
		}

		$sync_time = current_time( 'mysql' );
		update_option( 'agenticmcp_last_sync', $sync_time );

		wp_send_json_success(
			array(
				'message'   => __( 'Catalog sync completed successfully.', 'trusteed-for-woocommerce' ),
				'synced_at' => $sync_time,
			)
		);
	}

	/**
	 * Enqueue admin scripts and styles for the settings page.
	 *
	 * Only loads assets when on the Trusteed settings page.
	 * Hooked to admin_enqueue_scripts via class-plugin.php.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook The current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_assets( $hook ) {
		if ( 'woocommerce_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'agenticmcp-settings',
			TRUSTEED_PLUGIN_URL . 'assets/css/settings.css',
			array(),
			TRUSTEED_VERSION
		);

		wp_enqueue_script(
			'agenticmcp-settings',
			TRUSTEED_PLUGIN_URL . 'assets/js/settings.js',
			array( 'jquery' ),
			TRUSTEED_VERSION,
			true
		);

		wp_localize_script(
			'agenticmcp-settings',
			'agenticmcp_settings',
			array(
				'ajax_url'         => admin_url( 'admin-ajax.php' ),
				'test_nonce'       => wp_create_nonce( self::TEST_NONCE_ACTION ),
				'sync_nonce'       => wp_create_nonce( self::SYNC_NONCE_ACTION ),
				'onboard_nonce'    => wp_create_nonce( self::ONBOARD_NONCE_ACTION ),
				'connect_key_nonce' => wp_create_nonce( self::CONNECT_KEY_NONCE_ACTION ),
				'disconnect_nonce' => wp_create_nonce( self::DISCONNECT_NONCE_ACTION ),
				'i18n'             => array(
					'testing'            => __( 'Probando...', 'trusteed-for-woocommerce' ),
					'test_btn'           => __( 'Probar conexión', 'trusteed-for-woocommerce' ),
					'syncing'            => __( 'Sincronizando...', 'trusteed-for-woocommerce' ),
					'sync_btn'           => __( 'Sincronizar catálogo ahora', 'trusteed-for-woocommerce' ),
					'connecting'         => __( 'Conectando...', 'trusteed-for-woocommerce' ),
					'connect_btn'        => __( 'Conectar mi tienda', 'trusteed-for-woocommerce' ),
					'disconnecting'      => __( 'Desconectando...', 'trusteed-for-woocommerce' ),
					'disconnect_confirm' => __( '¿Seguro que quieres desconectar tu tienda?', 'trusteed-for-woocommerce' ),
					'request_failed'     => __( 'Error en la solicitud', 'trusteed-for-woocommerce' ),
					'connected'          => __( 'Conectada', 'trusteed-for-woocommerce' ),
					'failed'             => __( 'Error', 'trusteed-for-woocommerce' ),
				),
			)
		);
	}
}
