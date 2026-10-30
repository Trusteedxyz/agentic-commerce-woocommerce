<?php
/**
 * Señal para agentes en la web de la tienda: dónde se compra como agente.
 *
 * Un agente con navegador puede comprar pulsando los botones de la web
 * (`?add-to-cart=`) en vez de las tools de checkout del MCP. Esa compra no
 * lleva identidad verificable ni TrustReceipt de agente, y el comercio puede
 * tratarla como humana. Esta clase publica, en la propia web, un `/llms.txt`
 * que lo dice y apunta al MCP de la tienda, y un `<link rel="alternate">`
 * en la cabecera que lo anuncia.
 *
 * Sólo texto visible y público: nada oculto al visitante dirigido a agentes
 * (sería el patrón de la inyección de instrucciones). No cambia ningún
 * veredicto ni ninguna clasificación. Si ya existe un `/llms.txt` (fichero
 * físico u otro plugin), no responde.
 *
 * @package Trusteed
 * @since 2.5.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Trusteed_Agent_Discovery {

	const LLMS_PATH        = '/llms.txt';
	const CHECKOUT_GUIDE   = 'https://trusteed.xyz/.well-known/agent-checkout-guide.lite.json';
	const OPTION_SLUG      = 'agenticmcp_store_slug';
	const OPTION_ENDPOINT  = 'agenticmcp_mcp_endpoint';

	public function init(): void {
		add_action( 'wp_head', array( $this, 'print_head_link' ), 5 );
		add_action( 'template_redirect', array( $this, 'maybe_serve_llms_txt' ), 1 );
	}

	/** Endpoint MCP de la tienda, o '' si el plugin no está conectado. */
	public function mcp_endpoint(): string {
		$slug     = (string) get_option( self::OPTION_SLUG, '' );
		$endpoint = (string) get_option( self::OPTION_ENDPOINT, '' );
		if ( '' === $slug || '' === $endpoint ) {
			return '';
		}
		return self::normalize_endpoint( $endpoint );
	}

	/** Un alta anterior a 2.5.5 guardó el /mcp unificado, retirado (410): se publica su sucesor, el bucket de descubrimiento. */
	public static function normalize_endpoint( string $endpoint ): string {
		return preg_match( '#/mcp/?$#', $endpoint ) ? rtrim( $endpoint, '/' ) . '/discovery' : $endpoint;
	}

	public function print_head_link(): void {
		if ( '' === $this->mcp_endpoint() ) {
			return;
		}
		printf(
			'<link rel="alternate" type="text/plain" title="llms.txt" href="%s" />' . "\n",
			esc_url( home_url( self::LLMS_PATH ) )
		);
	}

	public function maybe_serve_llms_txt(): void {
		$endpoint = $this->mcp_endpoint();
		if ( '' === $endpoint || ! is_404() ) {
			return;
		}
		$request_path = (string) wp_parse_url( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ); // phpcs:ignore WordPress.Security
		$own_path     = (string) wp_parse_url( home_url( self::LLMS_PATH ), PHP_URL_PATH );
		if ( ! self::is_llms_request( $request_path, $own_path ) ) {
			return;
		}
		status_header( 200 );
		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'X-Robots-Tag: noindex' );
		echo self::build_llms_txt( (string) get_bloginfo( 'name' ), $endpoint ); // phpcs:ignore WordPress.Security.EscapeOutput
		exit;
	}

	/** Coincidencia exacta con la ruta propia (admite instalaciones en subdirectorio). */
	public static function is_llms_request( string $request_path, string $own_path ): bool {
		return '' !== $own_path && $request_path === $own_path;
	}

	public static function build_llms_txt( string $site_name, string $mcp_endpoint ): string {
		$name = '' !== trim( $site_name ) ? trim( $site_name ) : 'Store';
		return implode(
			"\n",
			array(
				"# {$name}",
				'',
				'> Store connected to Trusteed. AI agents should buy through the MCP checkout tools, not by pressing the buttons of this website.',
				'',
				'## Buy as an agent',
				'',
				"- MCP endpoint: {$mcp_endpoint}",
				'- Flow: explore_store, create_agent_credential, search_products, create_cart, preview_checkout, complete_checkout',
				'- Checkout guide: ' . self::CHECKOUT_GUIDE,
				'',
				'## Buying through the website',
				'',
				'A purchase made by driving this website (its add-to-cart buttons or checkout form) bypasses the agent flow: it carries no verifiable agent identity and no agent TrustReceipt, and the merchant may treat it as a human purchase.',
				'',
			)
		);
	}
}
