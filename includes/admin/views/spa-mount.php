<?php
/**
 * SPA mount template — renders the root div where the React SPA attaches.
 *
 * SECURITY: Zero data-attributes with tokens or secrets (constraint C-003).
 * All sensitive data flows through the REST token-broker endpoint, not the DOM.
 *
 * Expected variables provided by the caller (Amcp_Admin_Router::render_spa_shell):
 *   @var string $section One of 'trust-center' | 'merchant-center' | 'settings' |
 *                        'inicio' | 'mis-ventas' | 'mis-reglas' | 'seguridad' | 'agentes'.
 *
 * @package AgenticMCPStores
 * @since   1.2.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Validate section against the allowlist — default to 'trust-center' if unknown.
$allowed_sections = array(
	'trust-center',
	'merchant-center',
	'settings',
	'inicio',
	'mis-ventas',
	'mis-reglas',
	'seguridad',
	'agentes',
);
$safe_section     = in_array( $section ?? '', $allowed_sections, true ) ? $section : 'trust-center';
?>
<div
	id="amcp-root"
	data-section="<?php echo esc_attr( $safe_section ); ?>"
>
	<?php /* Loading fallback — replaced by the React SPA on mount. */ ?>
	<div class="amcp-loading" style="padding:20px;color:#666;font-family:sans-serif">
		<?php esc_html_e( 'Loading Trusteed...', 'agenticmcpstores' ); ?>
	</div>
</div>
