<?php
/**
 * Pasarela `trusteed_agent`: etiqueta de los pedidos que Trusteed crea y cobra
 * fuera de WooCommerce (Stripe Connect). No cobra nada en el front.
 *
 * Se carga sólo cuando WC_Payment_Gateway existe (ver Trusteed_Agent_Gateway_Registrar).
 *
 * @package Trusteed
 * @since   2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Payment_Gateway' ) && ! class_exists( 'Trusteed_Agent_Gateway', false ) ) {

	class Trusteed_Agent_Gateway extends WC_Payment_Gateway {

		public function __construct() {
			$this->id                 = 'trusteed_agent';
			$this->method_title       = 'Trusteed (agente)';
			$this->method_description = 'Pedidos pagados por un agente de IA a través de Trusteed. No aparece en el checkout.';
			$this->title              = 'Trusteed (agente)';
			$this->has_fields         = false;
			$this->enabled            = 'yes';
			$this->supports           = array( 'products' );
		}

		/**
		 * Nunca se ofrece al comprador: sólo etiqueta pedidos creados por la API.
		 *
		 * @return bool
		 */
		public function is_available() {
			return false;
		}

		/**
		 * Defensa en profundidad: si alguien fuerza esta pasarela en un checkout, no cobra.
		 *
		 * @param int $order_id Pedido.
		 * @return array{result:string}
		 */
		public function process_payment( $order_id ) {
			return array( 'result' => 'failure' );
		}
	}
}
