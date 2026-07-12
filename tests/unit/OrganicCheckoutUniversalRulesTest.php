<?php

declare(strict_types=1);

/**
 * App Store remediation (2026-07-11) — R030 (and every other merchant-wide
 * "appliesTo:ALL" policy rule: R014/R018/R019/R020/R025/R027) must apply to
 * a normal human (non-agentic) WooCommerce checkout, not just agentic carts.
 *
 * Root cause fixed in class-checkout-enforcer.php::run_evaluation(): the
 * method used to `return 'ALLOW'` immediately whenever no agent_id was in
 * the WC session, WITHOUT ever calling the enforcement evaluator — so a
 * merchant's "max order amount" cap (or any other universal rule) silently
 * never applied to a real customer. The shared Layer-2 evaluator
 * (rule-evaluator.service.ts + rule-row-mapper.ts resolveRuleAppliesTo) is
 * what now safely decides, server-side, which rules apply without an agent
 * — this test only asserts the WooCommerce plugin no longer short-circuits
 * before ever calling it.
 *
 * `Amcp_Checkout_Enforcer::run_evaluation()` is private and deeply coupled
 * to the live WC()/WC_Session globals (not stubbed anywhere in this test
 * suite — see FailureModeNormalizationTest.php for the same constraint).
 * Following that file's established pattern, this test asserts the control
 * flow structurally against the real source rather than instantiating the
 * class. Every substring below was verified byte-for-byte against the real
 * file (`sed -n '<line>p' | cat -A`) before being written here.
 */

use PHPUnit\Framework\TestCase;

final class OrganicCheckoutUniversalRulesTest extends TestCase
{
    private function enforcerSource(): string
    {
        $src = file_get_contents(__DIR__ . '/../../includes/class-checkout-enforcer.php');
        $this->assertIsString($src);
        return $src;
    }

    public function test_no_agent_early_return_before_calling_evaluate(): void
    {
        $src = $this->enforcerSource();

        // The old fail-safe-but-wrong short-circuit must be gone.
        $this->assertStringNotContainsString(
            'Not an agent-initiated cart',
            $src,
            'run_evaluation() must no longer skip enforcement entirely for non-agent carts'
        );
    }

    public function test_merchant_id_check_precedes_agent_id_resolution(): void
    {
        $src = $this->enforcerSource();
        $merchantCheckPos = strpos($src, 'if ( empty( $this->merchant_id ) ) {');
        $agentIdPos = strpos($src, '$agent_id = WC()->session');
        $this->assertNotFalse($merchantCheckPos);
        $this->assertNotFalse($agentIdPos);
        $this->assertLessThan(
            $agentIdPos,
            $merchantCheckPos,
            'merchant_id check must run before agent_id is even read (still fails fast when merchant unconfigured)'
        );
    }

    public function test_evaluate_call_is_unconditional_on_agent_presence(): void
    {
        $src = $this->enforcerSource();

        // Between "agent_id = null" normalization and the api_client->evaluate()
        // call there must be NO early `return 'ALLOW'` — i.e. evaluate() always
        // runs regardless of whether an agent was detected.
        $agentNullPos = strpos($src, '$agent_id = null;');
        $evaluatePos = strpos($src, '$this->api_client->evaluate( $payload );');
        $this->assertNotFalse($agentNullPos);
        $this->assertNotFalse($evaluatePos);
        $this->assertLessThan($evaluatePos, $agentNullPos);

        $between = substr($src, $agentNullPos, $evaluatePos - $agentNullPos);
        $this->assertStringNotContainsString(
            "return 'ALLOW';",
            $between,
            'no early ALLOW return may sit between agent detection and the evaluate() call'
        );
    }

    public function test_payload_agent_id_is_nullable_not_required(): void
    {
        $src = $this->enforcerSource();
        // The payload must forward the (possibly-null) $agent_id variable,
        // not require a non-empty string before it is even built.
        $this->assertStringContainsString("'agentId'        => \$agent_id,", $src);
    }

    public function test_session_set_calls_are_null_safe(): void
    {
        $src = $this->enforcerSource();
        $pos = strpos($src, 'Clear session regardless of decision');
        $this->assertNotFalse($pos);
        $snippet = substr($src, $pos, 200);
        $this->assertStringContainsString('if ( WC()->session ) {', $snippet);
    }
}
