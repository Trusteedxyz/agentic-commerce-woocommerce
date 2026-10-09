<?php
/**
 * Registro de la pasarela `trusteed_agent` en WooCommerce.
 *
 * La clase de la pasarela (`WC_Payment_Gateway`) sólo se puede declarar cuando
 * WooCommerce ya está cargado, así que vive en class-agent-gateway-class.php y
 * se carga de forma perezosa desde el filtro `woocommerce_payment_gateways`.
 *
 * @package Trusteed
 * @since   2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Trusteed_Agent_Gateway_Registrar {

	public const GATEWAY_CLASS = 'Trusteed_Agent_Gateway';

	/**
	 * Engancha el filtro. Se registra SIEMPRE (con o sin los endpoints activos):
	 * un pedido ya creado debe seguir mostrando «Trusteed (agente)» como método.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_filter( 'woocommerce_payment_gateways', array( self::class, 'filter_gateways' ) );
	}

	/**
	 * @param array<int,string> $gateways Clases de pasarela ya registradas.
	 * @return array<int,string>
	 */
	public static function filter_gateways( $gateways ): array {
		$gateways = is_array( $gateways ) ? $gateways : array();
		if ( ! class_exists( self::GATEWAY_CLASS, false ) ) {
			require_once __DIR__ . '/class-agent-gateway-class.php';
		}
		if ( class_exists( self::GATEWAY_CLASS, false ) && ! in_array( self::GATEWAY_CLASS, $gateways, true ) ) {
			$gateways[] = self::GATEWAY_CLASS;
		}
		return $gateways;
	}
}
