<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Consume el fixture compartido del flujo principal
 * (packages/shared/src/enforcement/__fixtures__/platform-flow-conformance.json),
 * el mismo que valida la referencia TS `platform-flow.ts`.
 *
 * Cada bloque cuenta los vectores que EJECUTA (no basta con «verde»: un
 * bloque que no corre nada también sale verde). `serverRouting` no aplica a
 * este plugin: WooCommerce principal no evalúa ninguna regla localmente y
 * pregunta siempre al servidor, así que no hay decisión de enrutado que portar.
 */
final class EnforcementFailurePolicyConformanceTest extends TestCase
{
    private static function fixture(): array
    {
        $path = __DIR__ . '/../../../../shared/src/enforcement/__fixtures__/platform-flow-conformance.json';
        $json = json_decode((string) file_get_contents($path), true);
        self::assertIsArray($json, 'el fixture tiene que ser alcanzable desde el monorepo');
        return $json;
    }

    public function test_agentic_detection_vectors(): void
    {
        $vectors = self::fixture()['agenticDetection'];
        $run     = 0;
        foreach ($vectors as $v) {
            $s = $v['signals'];
            self::assertSame(
                $v['expected'],
                Trusteed_Enforcement_Failure_Policy::is_agentic($s['agentId'], $s['tokenPresent'], $s['originMarker']),
                $v['id']
            );
            $run++;
        }
        self::assertSame(6, $run, 'vectores agenticDetection ejecutados');
    }

    public function test_offline_valve_scope_vectors(): void
    {
        $run = 0;
        foreach (self::fixture()['offlineValveScope'] as $v) {
            $codes = array_map(
                static fn (array $r): string => $r['ruleCode'],
                Trusteed_Enforcement_Failure_Policy::offline_rules_for($v['rules'], $v['isAgentic'])
            );
            self::assertSame($v['expectedCodes'], $codes, $v['id']);
            $run++;
        }
        self::assertSame(6, $run, 'vectores offlineValveScope ejecutados');
    }

    public function test_offline_rules_for_does_not_mutate_its_input(): void
    {
        $rules = [
            ['ruleCode' => 'R029', 'enabled' => true, 'appliesTo' => 'AGENT'],
            ['ruleCode' => 'R030', 'enabled' => true, 'appliesTo' => 'ALL'],
        ];
        $copy = $rules;
        $out  = Trusteed_Enforcement_Failure_Policy::offline_rules_for($rules, false);
        self::assertSame($copy, $rules);
        self::assertSame([['ruleCode' => 'R030', 'enabled' => true, 'appliesTo' => 'ALL']], $out, 'reindexado desde 0');
    }

    /**
     * Los 21 vectores de decisión se ejecutan contra el port puro, que compara
     * EXACTAMENTE como la referencia (sin normalizar).
     */
    public function test_unavailable_decision_vectors(): void
    {
        $run = 0;
        foreach (self::fixture()['unavailableDecision'] as $v) {
            self::assertSame(
                $v['expected'],
                Trusteed_Enforcement_Failure_Policy::decide_unavailable($v['isAgentic'], $v['failureMode'], $v['offlineBlock']),
                $v['id']
            );
            $run++;
        }
        self::assertSame(21, $run, 'vectores unavailableDecision ejecutados');
    }

    /**
     * CARRY de la Task 1: el port normaliza su modo contra SU vocabulario
     * (enforce|observe) antes de llamar a la política. Un valor de otro
     * vocabulario (`permissive`, `balanced`, `strict`) es «no resuelto» y
     * cierra a un agente; a un humano nunca lo toca.
     */
    public function test_failure_mode_normalization_against_this_vocabulary(): void
    {
        $fx      = self::fixture();
        $vocab   = $fx['_vocabulary']['failureModes']['woocommerce'];
        $opening = $fx['_vocabulary']['agentOpeningFailureModes']['woocommerce'];
        self::assertSame(Trusteed_Enforcement_Failure_Policy::FAILURE_MODES, $vocab, 'vocabulario del port = fixture');

        $run = 0;
        foreach ($vocab as $mode) {
            self::assertSame($mode, Trusteed_Enforcement_Failure_Policy::normalize_failure_mode($mode), $mode);
            self::assertSame($mode, Trusteed_Enforcement_Failure_Policy::normalize_failure_mode('  ' . strtoupper($mode) . ' '), $mode);
            $agent = Trusteed_Enforcement_Failure_Policy::decide_unavailable(
                true,
                Trusteed_Enforcement_Failure_Policy::normalize_failure_mode($mode),
                false
            );
            self::assertSame(in_array($mode, $opening, true) ? 'ALLOW' : 'BLOCK', $agent, 'agente con ' . $mode);
            $run++;
        }

        $foreign = [];
        foreach ($fx['_vocabulary']['failureModes'] as $modes) {
            foreach ($modes as $mode) {
                if (!in_array($mode, $vocab, true)) {
                    $foreign[$mode] = true;
                }
            }
        }
        self::assertSame(['strict', 'balanced', 'permissive'], array_keys($foreign));
        foreach (array_keys($foreign) as $mode) {
            $normalized = Trusteed_Enforcement_Failure_Policy::normalize_failure_mode($mode);
            self::assertNull($normalized, $mode . ' no es de este vocabulario');
            self::assertSame('BLOCK', Trusteed_Enforcement_Failure_Policy::decide_unavailable(true, $normalized, false), 'agente con ' . $mode);
            self::assertSame('ALLOW', Trusteed_Enforcement_Failure_Policy::decide_unavailable(false, $normalized, false), 'humano con ' . $mode);
            $run++;
        }

        foreach ($fx['_vocabulary']['unresolvedFailureModeSamples'] as $raw) {
            $normalized = Trusteed_Enforcement_Failure_Policy::normalize_failure_mode($raw);
            self::assertSame('BLOCK', Trusteed_Enforcement_Failure_Policy::decide_unavailable(true, $normalized, false), 'agente con ' . var_export($raw, true));
            self::assertSame('ALLOW', Trusteed_Enforcement_Failure_Policy::decide_unavailable(false, $normalized, false), 'humano con ' . var_export($raw, true));
            $run++;
        }
        self::assertSame(2 + 3 + 4, $run, 'modos ejecutados: vocabulario + ajenos + muestras no resueltas');
    }

    public function test_absent_failure_mode_resolves_to_the_default_enforce(): void
    {
        self::assertSame('enforce', Trusteed_Enforcement_Failure_Policy::normalize_failure_mode(null));
        self::assertSame('enforce', Trusteed_Enforcement_Failure_Policy::normalize_failure_mode('   '));
        self::assertSame('BLOCK', Trusteed_Enforcement_Failure_Policy::decide_unavailable(true, null, false), 'no resuelto cierra a un agente');
    }

    // ── Task 23a: avería degradada por causa y resolver ────────────────────

    /** Los 17 vectores `degradedEnforcement` del port `woocommerce` contra el port puro. */
    public function test_degraded_enforcement_vectors_for_woocommerce(): void
    {
        $run = 0;
        foreach (self::fixture()['degradedEnforcement']['vectors'] as $v) {
            if ('woocommerce' !== $v['port']) {
                continue;
            }
            self::assertSame(
                $v['expected'],
                Trusteed_Enforcement_Failure_Policy::decide_degraded($v['kind'], $v['isAgentic'], $v['tokenPresent'], $v['failureMode'], $v['offlineBlock']),
                $v['id']
            );
            $run++;
        }
        self::assertSame(17, $run, 'vectores degradedEnforcement de woocommerce ejecutados');
    }

    /**
     * Los mismos, con el modo normalizado como lo lee el enforcer: aquí `""` es
     * AUSENTE ⇒ `enforce` (default), y el vector `unresolved-empty` sigue en
     * BLOCK por ese camino.
     */
    public function test_degraded_enforcement_vectors_through_the_mode_normalizer(): void
    {
        $run = 0;
        foreach (self::fixture()['degradedEnforcement']['vectors'] as $v) {
            if ('woocommerce' !== $v['port']) {
                continue;
            }
            self::assertSame(
                $v['expected'],
                Trusteed_Enforcement_Failure_Policy::decide_degraded(
                    $v['kind'],
                    $v['isAgentic'],
                    $v['tokenPresent'],
                    Trusteed_Enforcement_Failure_Policy::normalize_failure_mode($v['failureMode']),
                    $v['offlineBlock']
                ),
                $v['id']
            );
            $run++;
        }
        self::assertSame(17, $run);
    }

    public function test_every_degraded_enforcement_vector_against_the_pure_port(): void
    {
        $run = 0;
        foreach (self::fixture()['degradedEnforcement']['vectors'] as $v) {
            self::assertSame(
                $v['expected'],
                Trusteed_Enforcement_Failure_Policy::decide_degraded($v['kind'], $v['isAgentic'], $v['tokenPresent'], $v['failureMode'], $v['offlineBlock']),
                $v['id']
            );
            $run++;
        }
        self::assertSame(100, $run);
        self::assertSame('BLOCK', Trusteed_Enforcement_Failure_Policy::decide_degraded('not-a-kind', true, false, 'observe', false), 'causa desconocida = firma inválida');
        self::assertSame('ALLOW', Trusteed_Enforcement_Failure_Policy::decide_degraded('not-a-kind', false, false, 'enforce', true));
    }

    /** Estado del cliente de snapshot ⇒ causa (el enforcer lo usa tal cual). */
    public function test_snapshot_status_maps_to_the_degraded_kind(): void
    {
        self::assertNull(Trusteed_Snapshot_Client_Woo::degraded_kind(Trusteed_Snapshot_Client_Woo::STATUS_OK));
        self::assertSame(Trusteed_Enforcement_Failure_Policy::KIND_INVALID_SIGNATURE, Trusteed_Snapshot_Client_Woo::degraded_kind(Trusteed_Snapshot_Client_Woo::STATUS_TAMPERED));
        self::assertSame(Trusteed_Enforcement_Failure_Policy::KIND_MISSING_SNAPSHOT, Trusteed_Snapshot_Client_Woo::degraded_kind(Trusteed_Snapshot_Client_Woo::STATUS_UNAVAILABLE));
    }

    /** Los 19 vectores `snapshotResolverShape` contra el resolver del verificador. */
    public function test_snapshot_resolver_shape_vectors(): void
    {
        $run = 0;
        foreach (self::fixture()['snapshotResolverShape']['vectors'] as $v) {
            self::assertEquals($v['expected'], Trusteed_Token_Verifier::resolve_agent_did_public_key($v['resolver'], $v['did']), $v['id']);
            $run++;
        }
        self::assertSame(19, $run, 'vectores snapshotResolverShape ejecutados');
    }

    /** El `snapshotPayload` real (lista) resuelve sus dos DIDs. */
    public function test_the_real_snapshot_payload_resolves_every_did(): void
    {
        $payload = self::fixture()['snapshotResolverShape']['snapshotPayload'];
        self::assertTrue(array_is_list($payload['agentDidResolver']));
        $run = 0;
        foreach ($payload['agentDidResolver'] as $entry) {
            $key = Trusteed_Token_Verifier::resolve_agent_did_public_key($payload['agentDidResolver'], $entry['did']);
            self::assertSame($entry['publicKeyJwk']['x'], $key['x'] ?? null, $entry['did']);
            $run++;
        }
        self::assertSame(2, $run);
    }
}
