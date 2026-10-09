<?php
/**
 * Política del flujo principal — port PHP de
 * packages/shared/src/enforcement/platform-flow.ts, fijado por
 * packages/shared/src/enforcement/__fixtures__/platform-flow-conformance.json
 * (tests/unit/EnforcementFailurePolicyConformanceTest.php).
 *
 * Invariante (auditoría spec-048 2026-09-27, A8; decisión D1 del plan 04): un
 * comprador humano —checkout sin ninguna señal agéntica— nunca se bloquea por
 * una avería nuestra (red, 5xx, 4xx incluido 429, timeout, comerciante sin
 * configurar). Sólo puede bloquearlo una regla `appliesTo: ALL`/`HUMAN` del
 * propio comerciante que la válvula offline siga evaluando (el tope de R030,
 * países, horario…). El `failure_mode` del comerciante sólo gobierna a los
 * checkouts agénticos.
 *
 * Funciones puras: sin WordPress, sin WooCommerce, sin I/O. No mutan sus
 * parámetros (PHP pasa los arrays por valor y aquí sólo se filtran copias).
 *
 * @package Trusteed
 * @since   2.3.6
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Trusteed_Enforcement_Failure_Policy
 *
 * @since 2.3.6
 */
final class Trusteed_Enforcement_Failure_Policy {

	/** Vocabulario de `trusteed_failure_mode` en este plugin (igual que el fixture). */
	const FAILURE_MODES = array( 'enforce', 'observe' );

	/** Valor que toma un `failure_mode` AUSENTE (default histórico del plugin). */
	const DEFAULT_FAILURE_MODE = 'enforce';

	/**
	 * Modos que ABREN a un agente ante una avería, comparados exactamente. Es la
	 * lista GLOBAL de la referencia (`AGENT_OPENING_FAILURE_MODES`): por eso este
	 * port normaliza contra SU vocabulario antes (ver normalize_failure_mode),
	 * para que un `balanced`/`permissive` ajeno no abra aquí.
	 */
	const AGENT_OPENING_FAILURE_MODES = array( 'observe', 'balanced', 'permissive' );

	/**
	 * Cualquier señal que sólo pone nuestro lado o el agente ⇒ agéntico. Un
	 * token presentado cuenta aunque no verifique: nunca se trata como humano.
	 *
	 * @param string|null $agent_id      DID del agente; '' equivale a ausente.
	 * @param bool        $token_present Hay token de agente en la sesión.
	 * @param bool        $origin_marker Marca de origen de nuestro MCP.
	 * @return bool
	 */
	public static function is_agentic( ?string $agent_id, bool $token_present, bool $origin_marker ): bool {
		return ( null !== $agent_id && '' !== $agent_id ) || $token_present || $origin_marker;
	}

	/**
	 * Reglas que la válvula offline puede aplicar, en el orden recibido y
	 * reindexadas. `appliesTo` sin distinguir mayúsculas.
	 *
	 * - Agente: todas salvo las `HUMAN` (con `appliesTo` ausente incluido).
	 * - Humano: sólo `ALL` y `HUMAN`; ausente no cuenta (ante la duda, nunca
	 *   bloquear a una persona).
	 *
	 * @param array $rules      `rules[]` del snapshot firmado.
	 * @param bool  $is_agentic Resultado de is_agentic().
	 * @return array
	 */
	public static function offline_rules_for( array $rules, bool $is_agentic ): array {
		return array_values(
			array_filter(
				$rules,
				static function ( $rule ) use ( $is_agentic ): bool {
					if ( ! is_array( $rule ) ) {
						return false;
					}
					$applies_to = strtoupper( (string) ( $rule['appliesTo'] ?? '' ) );
					return $is_agentic
						? 'HUMAN' !== $applies_to
						: ( 'ALL' === $applies_to || 'HUMAN' === $applies_to );
				}
			)
		);
	}

	/**
	 * Resuelve el modo configurado contra el vocabulario de ESTE plugin:
	 * recorta y pasa a minúsculas; ausente o vacío ⇒ DEFAULT_FAILURE_MODE;
	 * fuera de FAILURE_MODES (p. ej. `permissive` de otra plataforma) ⇒ null,
	 * «no resuelto», que decide_unavailable() cierra para un agente.
	 *
	 * @param string|null $raw Valor configurado (no confiable).
	 * @return string|null
	 */
	public static function normalize_failure_mode( ?string $raw ): ?string {
		$normalized = strtolower( trim( (string) $raw ) );
		if ( '' === $normalized ) {
			return self::DEFAULT_FAILURE_MODE;
		}
		return in_array( $normalized, self::FAILURE_MODES, true ) ? $normalized : null;
	}

	/**
	 * El servidor no contestó (o no hay a quién preguntar). La válvula offline
	 * ya filtrada con offline_rules_for() bloquea a cualquiera; sin ella, un
	 * humano pasa SIEMPRE, y a un agente se le aplica el modo del comerciante:
	 * abre sólo con un modo de AGENT_OPENING_FAILURE_MODES y cierra con
	 * cualquier otro valor, null incluido. No normaliza (igual que la
	 * referencia): el llamador pasa el valor de normalize_failure_mode().
	 *
	 * @param bool        $is_agentic    Resultado de is_agentic().
	 * @param string|null $failure_mode  Modo resuelto, o null si no se resolvió.
	 * @param bool        $offline_block La válvula offline encontró una regla que bloquea.
	 * @return string 'ALLOW' | 'BLOCK'.
	 */
	public static function decide_unavailable( bool $is_agentic, ?string $failure_mode, bool $offline_block ): string {
		if ( $offline_block ) {
			return 'BLOCK';
		}
		if ( ! $is_agentic ) {
			return 'ALLOW';
		}
		return null !== $failure_mode && in_array( $failure_mode, self::AGENT_OPENING_FAILURE_MODES, true )
			? 'ALLOW'
			: 'BLOCK';
	}

	/**
	 * Una excepción NUESTRA durante la evaluación (constructor de contexto,
	 * válvula offline, cliente). Una persona nunca se bloquea por ella; a un
	 * agente no se le abre en ningún modo: una avería propia no es un permiso.
	 * No lee el modo del comerciante. Igual que `decide_on_internal_error` de
	 * Odoo y `InternalErrorGuard` de PrestaShop.
	 *
	 * @param bool $is_agentic Resultado de is_agentic().
	 * @return string 'ALLOW' | 'BLOCK'.
	 */
	public static function decide_on_internal_error( bool $is_agentic ): string {
		return $is_agentic ? 'BLOCK' : 'ALLOW';
	}

	/*
	 * Causas de una capa degradada (`PLATFORM_DEGRADED_KINDS` de la referencia,
	 * vectores `degradedEnforcement` del fixture, port `woocommerce`). En este
	 * plugin (2.3.6, Task 23a):
	 *
	 * - KIND_INVALID_SIGNATURE: el snapshot servido no verifica ni tras volver a
	 *   bajar el JWKS (Trusteed_Snapshot_Client_Woo::STATUS_TAMPERED).
	 * - KIND_MISSING_SNAPSHOT: no hay snapshot verificado (red, HTTP, JWKS
	 *   inalcanzable, sin sodium: STATUS_UNAVAILABLE).
	 * - KIND_EVALUATOR_UNAVAILABLE: `/v1/rules/evaluate` INDETERMINATE con el
	 *   snapshot verificado (la válvula offline sí corre).
	 * - KIND_LAYER_DISABLED: el comerciante sin conectar (`merchant_id` vacío).
	 */
	const KIND_INVALID_SIGNATURE     = 'invalid_signature';
	const KIND_MISSING_SNAPSHOT      = 'missing_snapshot';
	const KIND_EVALUATOR_UNAVAILABLE = 'evaluator_unavailable';
	const KIND_LAYER_DISABLED        = 'layer_disabled';

	/**
	 * Port literal de `decideOnDegradedEnforcement` (platform-flow.ts). Sobre un
	 * agente: firma inválida ⇒ BLOCK en todos los modos; snapshot ausente con
	 * token ⇒ BLOCK en todos los modos (el resolver de DIDs viaja DENTRO del
	 * snapshot: sin él el token no se puede verificar), sin token ⇒ el modo;
	 * capa 2 caída con snapshot verificado ⇒ el modo y la válvula; capa apagada
	 * ⇒ ALLOW. Un humano nunca se cierra por avería: sólo `$offline_block` de un
	 * snapshot verificado (capa 2 caída) lo bloquea; con firma inválida o sin
	 * snapshot se IGNORA. Una causa desconocida se trata como firma inválida.
	 * `$token_present` cuenta como señal agéntica. No normaliza el modo.
	 *
	 * @param string      $kind          Una de las KIND_*.
	 * @param bool        $is_agentic    Resultado de is_agentic().
	 * @param bool        $token_present Hay token de agente (válido o no).
	 * @param string|null $failure_mode  Modo resuelto (normalize_failure_mode()).
	 * @param bool        $offline_block La válvula offline encontró una regla que bloquea.
	 * @return string 'ALLOW' | 'BLOCK'.
	 */
	public static function decide_degraded( string $kind, bool $is_agentic, bool $token_present, ?string $failure_mode, bool $offline_block ): string {
		$is_agentic = $is_agentic || $token_present;
		switch ( $kind ) {
			case self::KIND_LAYER_DISABLED:
				return 'ALLOW';
			case self::KIND_EVALUATOR_UNAVAILABLE:
				return self::decide_unavailable( $is_agentic, $failure_mode, $offline_block );
			case self::KIND_MISSING_SNAPSHOT:
				if ( ! $is_agentic ) {
					return 'ALLOW';
				}
				return $token_present ? 'BLOCK' : self::decide_unavailable( true, $failure_mode, false );
			default:
				return $is_agentic ? 'BLOCK' : 'ALLOW';
		}
	}
}
