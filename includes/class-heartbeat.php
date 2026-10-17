<?php
/**
 * C14 — latido horario hacia `POST /api/v1/plugin/heartbeat`.
 *
 * El plugin sólo empujaba catálogo por eventos de producto: una tienda estable
 * pasaba días sin tocar la API y C14 no sabía si el plugin seguía vivo. El
 * latido deja una fila `sync_logs` success por hora, sin tocar el catálogo.
 * Mismo credencial (`X-AgenticMCP-Key`) que el sync. Fallos: log, nunca fatal.
 *
 * @package Trusteed
 * @since   2.4.2 (taxConfig: 2.4.3, policyPages: 2.4.8)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Trusteed_Heartbeat {

	public const HOOK     = 'trusteed_heartbeat_hourly';
	public const ENDPOINT = '/api/v1/plugin/heartbeat';

	public static function register(): void {
		add_action( self::HOOK, array( self::class, 'send' ) );
		add_action( 'init', array( self::class, 'ensure_scheduled' ), 20 );
	}

	/** Programa el evento horario si falta (activación, y red de seguridad en init). */
	public static function ensure_scheduled(): void {
		if ( ! wp_next_scheduled( self::HOOK ) ) {
			wp_schedule_event( time() + 60, 'hourly', self::HOOK );
		}
	}

	public static function unschedule(): void {
		wp_clear_scheduled_hook( self::HOOK );
	}

	/**
	 * Ajustes fiscales de WooCommerce. La API los usa para saber si el precio
	 * sincronizado (`get_price()`, tal como se introdujo) y el que ve un agente
	 * (visualización de la tienda) están en la misma base fiscal.
	 *
	 * @return array{calcTaxes:bool,pricesIncludeTax:bool,taxDisplayShop:string}
	 */
	public static function tax_config(): array {
		return array(
			'calcTaxes'        => 'yes' === get_option( 'woocommerce_calc_taxes', 'no' ),
			'pricesIncludeTax' => 'yes' === get_option( 'woocommerce_prices_include_tax', 'no' ),
			'taxDisplayShop'   => 'incl' === get_option( 'woocommerce_tax_display_shop', 'excl' ) ? 'incl' : 'excl',
		);
	}

	/**
	 * Páginas de términos y de privacidad de la tienda (D6, 2026-10-15). Sólo la
	 * URL, el título y la fecha de modificación de las que están PUBLICADAS: ni
	 * borradores, ni privadas, ni el cuerpo. La API las guarda como importadas,
	 * aparte de lo que declara el comercio, y no cuentan para el trust score.
	 *
	 * @return array<int,array{type:string,url:string,title:string,page_modified_at:string}>
	 */
	public static function policy_pages(): array {
		$sources = array(
			'terms'   => 'woocommerce_terms_page_id',
			'privacy' => 'wp_page_for_privacy_policy',
		);
		$pages   = array();
		foreach ( $sources as $type => $option ) {
			$id = (int) get_option( $option, 0 );
			if ( $id <= 0 || 'publish' !== get_post_status( $id ) ) {
				continue;
			}
			$url      = get_permalink( $id );
			$modified = (string) get_post_field( 'post_modified_gmt', $id );
			$ts       = strtotime( $modified . ' UTC' );
			if ( ! is_string( $url ) || '' === $url || false === $ts ) {
				continue;
			}
			$pages[] = array(
				'type'             => $type,
				'url'              => $url,
				'title'            => (string) get_the_title( $id ),
				'page_modified_at' => gmdate( 'Y-m-d\TH:i:s\Z', $ts ),
			);
		}
		return $pages;
	}

	/**
	 * Cuerpo del latido.
	 *
	 * @return array{taxConfig:array,policyPages:array}
	 */
	public static function build_payload(): array {
		return array(
			'taxConfig'   => self::tax_config(),
			'policyPages' => self::policy_pages(),
		);
	}

	/**
	 * Envía el latido. Devuelve true si la API respondió 2xx.
	 */
	public static function send(): bool {
		try {
			if ( '' === Trusteed_Api_Client::get_stored_api_key() ) {
				return false; // Tienda no conectada: nada que latir.
			}
			$response = ( new Trusteed_Api_Client() )->post( self::ENDPOINT, self::build_payload() );
			if ( is_wp_error( $response ) ) {
				error_log( '[trusteed.heartbeat] failed: ' . $response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				return false;
			}
			return true;
		} catch ( \Throwable $e ) {
			error_log( '[trusteed.heartbeat] exception: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return false;
		}
	}
}
