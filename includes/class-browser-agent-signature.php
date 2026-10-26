<?php
/**
 * Firma RFC 9421 / Web Bot Auth de un agente que compra NAVEGANDO la web.
 *
 * Un agente de navegador (browser-use) entra al checkout como un humano: no hay
 * DID ni marca del puente de carrito. Si la petición trae `Signature-Input` /
 * `Signature`, se reenvía a la API de Trusteed (verify-agent-signature, auth S2S
 * por tienda) y el modo `browser_agent_signature_mode` decide qué se hace:
 *
 *  - off      no hace nada (ni una llamada).
 *  - observe  verifica y anota el resultado; NO cambia la clasificación (defecto).
 *  - enforce  `verified` ⇒ agente con la clave como identidad; `spoofed` ⇒ agente
 *             sin identidad; cualquier otro estado, error o timeout ⇒ humano.
 *
 * Fail-open en la clasificación: una avería de la verificación nunca bloquea ni
 * reclasifica, porque eso castigaría a una persona por un fallo nuestro.
 *
 * @package Trusteed
 * @since 2.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Trusteed_Browser_Agent_Signature {

	const MODES        = array( 'off', 'observe', 'enforce' );
	const DEFAULT_MODE = 'observe';

	/** Meta del pedido: JSON {status, provider, keyId}. */
	const META_KEY = '_trusteed_browser_agent_signature';

	const TIMEOUT_SECONDS = 1.5;
	const ENDPOINT        = '/v1/embed/agentic-tools/verify-agent-signature';
	const KNOWN_STATUSES  = array( 'verified', 'spoofed', 'unverified', 'skipped' );

	/** Nunca se reenvían, aunque `Signature-Input` los cubra (credenciales del navegador). */
	const NEVER_FORWARD = array( 'cookie', 'authorization', 'proxy-authorization', 'x-wp-nonce' );

	private string $api_base;
	private string $merchant_id;
	/** @var string|callable():string Secreto S2S, o una función que lo descifra bajo demanda. */
	private $embed_secret;
	private string $mode;
	private string $site_origin;

	/** @var array{key:string, result:?array}|null Una verificación por petición. */
	private ?array $cache = null;

	/**
	 * @param string|callable $embed_secret Secreto S2S o función que lo descifra (solo se invoca si hay que llamar).
	 * @param string          $site_url     home_url(): de ahí salen esquema y host. Nunca de Host (replay cross-host).
	 */
	public function __construct( string $api_base, string $merchant_id, $embed_secret, string $mode, string $site_url ) {
		$this->api_base     = rtrim( $api_base, '/' );
		$this->merchant_id  = $merchant_id;
		$this->embed_secret = $embed_secret;
		$this->mode         = self::normalize_mode( $mode );
		$parts              = parse_url( $site_url );
		$this->site_origin  = ( is_array( $parts ) && ! empty( $parts['scheme'] ) && ! empty( $parts['host'] ) )
			? strtolower( $parts['scheme'] ) . '://' . strtolower( $parts['host'] ) . ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' )
			: '';
	}

	public function mode(): string {
		return $this->mode;
	}

	/** Ausente o desconocido ⇒ `observe` (el defecto: nunca cambia la clasificación). */
	public static function normalize_mode( ?string $raw ): string {
		$mode = strtolower( trim( (string) $raw ) );
		return in_array( $mode, self::MODES, true ) ? $mode : self::DEFAULT_MODE;
	}

	/**
	 * Solo llama con las tres cabeceras (Signature, Signature-Input, Signature-Agent) y una
	 * etiqueta común: sin eso la API no puede verificar nada y cada llamada gastaría cupo
	 * de la tienda (DoS del cupo S2S). `verified` solo se concede si la firma cubre
	 * `@authority` o `@target-uri` (si no, un replay contra otro host valdría) y hay un
	 * origen https en `Signature-Agent` al que atar la identidad.
	 *
	 * @param array $server `$_SERVER`.
	 * @return array{status:string, provider:string, keyId:string, agentId:string}|null null = nada que hacer.
	 */
	public function resolve( array $server ): ?array {
		if ( 'off' === $this->mode || '' === $this->merchant_id || '' === $this->site_origin ) {
			return null;
		}
		$input = (string) ( $server['HTTP_SIGNATURE_INPUT'] ?? '' );
		$sig   = (string) ( $server['HTTP_SIGNATURE'] ?? '' );
		$agent = (string) ( $server['HTTP_SIGNATURE_AGENT'] ?? '' );
		if ( '' === $input || '' === $sig || '' === $agent ) {
			return null;
		}
		$components = self::covered_components( $input );
		$labels     = array_intersect( array_keys( $components ), self::signature_labels( $sig ) );
		$uri        = (string) ( $server['REQUEST_URI'] ?? '' );
		if ( array() === $labels || '' === $uri ) {
			return null;
		}

		$key = sha1( $input . "\n" . $sig . "\n" . $agent );
		if ( null !== $this->cache && $this->cache['key'] === $key ) {
			return $this->cache['result'];
		}

		$secret = is_callable( $this->embed_secret ) ? (string) ( $this->embed_secret )() : (string) $this->embed_secret;
		if ( '' === $secret ) {
			return null;
		}
		$target = parse_url( $uri );
		$path   = is_array( $target ) && isset( $target['path'] ) && '' !== $target['path'] ? $target['path'] : '/';
		$url    = $this->site_origin . $path . ( isset( $target['query'] ) ? '?' . $target['query'] : '' );

		$result = $this->call_api(
			$secret,
			array(
				'merchant_id' => $this->merchant_id,
				'method'      => strtoupper( (string) ( $server['REQUEST_METHOD'] ?? 'POST' ) ),
				'url'         => $url,
				'headers'     => $this->collect_headers( $server, $input ),
			)
		);
		$result = $this->bind_identity( $result, $components, $labels, $agent );
		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			sprintf( '[amcp.browser_agent_signature] mode=%s status=%s provider=%s', $this->mode, $result['status'], $result['provider'] )
		);
		$this->cache = array( 'key' => $key, 'result' => $result );
		return $result;
	}

	/**
	 * `verified` → `unverified` si no cubre autoridad/URI o no hay origen https del agente;
	 * si se queda `verified`, `agentId` = `wba:<origen>#<keyId>`.
	 */
	private function bind_identity( array $result, array $components, array $labels, string $agent_header ): array {
		$result['agentId'] = '';
		if ( 'verified' !== $result['status'] ) {
			return $result;
		}
		$covers = false;
		foreach ( $labels as $label ) {
			if ( array_intersect( array( '@authority', '@target-uri' ), $components[ $label ] ) ) {
				$covers = true;
			}
		}
		$origin = self::agent_origin( $agent_header );
		if ( ! $covers || '' === $origin || '' === $result['keyId'] ) {
			$result['status'] = 'unverified';
			return $result;
		}
		$result['agentId'] = 'wba:' . $origin . '#' . $result['keyId'];
		return $result;
	}

	/** @return array<string,string[]> etiqueta ⇒ componentes cubiertos (minúsculas). */
	private static function covered_components( string $input ): array {
		$out = array();
		if ( preg_match_all( '/(?:^|,)\s*([A-Za-z*][A-Za-z0-9_.*\-]*)=\(([^)]*)\)/', $input, $m, PREG_SET_ORDER ) ) {
			foreach ( $m as $member ) {
				preg_match_all( '/"([^"]*)"/', $member[2], $names );
				$out[ $member[1] ] = array_map( 'strtolower', $names[1] );
			}
		}
		return $out;
	}

	/** @return string[] */
	private static function signature_labels( string $sig ): array {
		return preg_match_all( '/(?:^|,)\s*([A-Za-z*][A-Za-z0-9_.*\-]*)=:/', $sig, $m ) ? $m[1] : array();
	}

	/** Origen https normalizado del `Signature-Agent` (cadena o diccionario); '' si no hay. */
	private static function agent_origin( string $header ): string {
		if ( ! preg_match( '/https?:\/\/[^"\s,;]+/i', $header, $m ) ) {
			return '';
		}
		$parts = parse_url( $m[0] );
		if ( ! is_array( $parts ) || 'https' !== strtolower( (string) ( $parts['scheme'] ?? '' ) ) || empty( $parts['host'] ) ) {
			return '';
		}
		return 'https://' . strtolower( $parts['host'] ) . ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' );
	}

	/** Cabeceras de firma + las que cubre `Signature-Input` (`@authority` sale de Host). */
	private function collect_headers( array $server, string $input ): array {
		$names = array( 'signature', 'signature-input', 'signature-agent', 'content-digest', 'host' );
		if ( preg_match_all( '/\(([^)]*)\)/', $input, $lists ) ) {
			foreach ( $lists[1] as $list ) {
				if ( preg_match_all( '/"([^"]*)"/', $list, $covered ) ) {
					$plain = array_filter( $covered[1], static fn( $n ) => '' !== $n && '@' !== $n[0] );
					$names = array_merge( $names, array_map( 'strtolower', $plain ) );
				}
			}
		}
		$headers = array();
		foreach ( array_unique( array_diff( $names, self::NEVER_FORWARD ) ) as $name ) {
			$env   = strtoupper( str_replace( '-', '_', $name ) );
			$value = $server[ 'HTTP_' . $env ] ?? ( in_array( $env, array( 'CONTENT_TYPE', 'CONTENT_LENGTH' ), true ) ? ( $server[ $env ] ?? null ) : null );
			if ( is_string( $value ) && '' !== $value ) {
				$headers[ $name ] = substr( $value, 0, 4096 );
			}
		}
		// El Host que verifica la API es el del sitio, no el que manda el cliente.
		$headers['host'] = (string) parse_url( $this->site_origin, PHP_URL_HOST ) . ( ( $port = parse_url( $this->site_origin, PHP_URL_PORT ) ) ? ':' . $port : '' );
		return $headers;
	}

	/** @return array{status:string, provider:string, keyId:string, agentId:string} `error` = timeout/HTTP/JSON malo. */
	private function call_api( string $secret, array $payload ): array {
		$error    = array( 'status' => 'error', 'provider' => '', 'keyId' => '', 'agentId' => '' );
		$response = wp_remote_post(
			$this->api_base . self::ENDPOINT,
			array(
				'timeout' => self::TIMEOUT_SECONDS,
				'headers' => array(
					'Content-Type'          => 'application/json',
					'x-trusteed-s2s-secret' => $secret,
				),
				'body'    => wp_json_encode( $payload ),
			)
		);
		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return $error;
		}
		$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $body ) || ! in_array( $body['verificationStatus'] ?? null, self::KNOWN_STATUSES, true ) ) {
			return $error;
		}
		return array(
			'status'   => $body['verificationStatus'],
			'provider' => substr( (string) ( $body['provider'] ?? '' ), 0, 200 ),
			'keyId'    => substr( (string) ( $body['keyId'] ?? '' ), 0, 200 ),
			'agentId'  => '',
		);
	}

	/** Pedido clásico: se crea después del hook de checkout, así que la meta se estampa al crearlo. */
	public static function stamp_next_classic_order( string $json ): void {
		if ( '' === $json ) {
			return;
		}
		add_action(
			'woocommerce_checkout_create_order',
			static function ( $order ) use ( $json ): void {
				if ( $order instanceof \WC_Order ) {
					$order->update_meta_data( self::META_KEY, $json );
				}
			},
			10,
			1
		);
	}
}
