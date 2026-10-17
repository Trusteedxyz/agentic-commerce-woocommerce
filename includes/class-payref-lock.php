<?php
/**
 * Candado por referencia de pago para `/paid` (C-A3).
 *
 * El valor es un token único por toma (`<unix>:<aleatorio>`). Se libera sólo si el
 * valor sigue siendo el propio, y un candado caducado (>120 s) se toma con un
 * UPDATE condicionado al valor viejo leído: de dos procesos que lo vean caducado
 * a la vez, gana exactamente uno.
 *
 * @package Trusteed
 * @since   2.4.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Trusteed_Payref_Lock {

	public const TTL = 120;

	/**
	 * @param string $name Nombre de la opción-candado.
	 * @return string|null Token propio si se tomó; null si lo tiene otro.
	 */
	public static function acquire( string $name ): ?string {
		global $wpdb;
		$token = self::new_token();
		// INSERT IGNORE: exclusivo de verdad (add_option acaba en ON DUPLICATE KEY UPDATE).
		$affected = $wpdb->query(
			$wpdb->prepare(
				"INSERT IGNORE INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, %s, 'no')",
				$name,
				$token
			)
		);
		self::flush_cache( $name );
		if ( 1 === (int) $affected ) {
			return $token;
		}
		// Lectura directa a la BD, no a la caché de opciones.
		$current = $wpdb->get_var(
			$wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $name )
		);
		if ( ! is_string( $current ) || '' === $current ) {
			return null;
		}
		$since = (int) explode( ':', $current )[0];
		if ( $since > 0 && ( time() - $since ) > self::TTL ) {
			return self::take_over( $name, $current );
		}
		return null;
	}

	/**
	 * Toma atómica de un candado caducado: sólo gana quien aún ve el valor viejo.
	 *
	 * @param string $name      Opción-candado.
	 * @param string $old_value Valor caducado leído.
	 * @return string|null
	 */
	public static function take_over( string $name, string $old_value ): ?string {
		global $wpdb;
		$token    = self::new_token();
		$affected = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s AND option_value = %s",
				$token,
				$name,
				$old_value
			)
		);
		self::flush_cache( $name );
		return 1 === (int) $affected ? $token : null;
	}

	/**
	 * Libera sólo si el candado sigue siendo del que lo pide.
	 *
	 * @param string $name  Opción-candado.
	 * @param string $token Token devuelto por acquire().
	 */
	public static function release( string $name, string $token ): void {
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s",
				$name,
				$token
			)
		);
		self::flush_cache( $name );
	}

	private static function new_token(): string {
		return time() . ':' . bin2hex( random_bytes( 10 ) );
	}

	private static function flush_cache( string $name ): void {
		if ( function_exists( 'wp_cache_delete' ) ) {
			wp_cache_delete( $name, 'options' );
			wp_cache_delete( 'notoptions', 'options' );
			wp_cache_delete( 'alloptions', 'options' );
		}
	}
}
