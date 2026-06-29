<?php

declare(strict_types=1);

/**
 * Spec-048 Sprint E.2 T-E45 — R043 HITL Gate (WooCommerce).
 *
 * Pure-function tests for the R043 HITL detection helpers. The
 * on_order_processed hook is integration territory (needs WC) so we
 * cover only the detection + payload-shape helpers here.
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}

require_once __DIR__ . '/../../includes/class-r043-hitl-gate.php';

use PHPUnit\Framework\TestCase;

final class R043HitlGateTest extends TestCase
{
    public function test_detects_canonical_hitl_response(): void
    {
        $resp = [
            'decision' => 'BLOCK',
            'ucp' => [
                'state' => 'requires_escalation',
                'reason_code' => 'trusteed:R043.agent-checkout-approval-required',
            ],
        ];
        $this->assertTrue(AgenticMCP_R043_Hitl_Gate::is_hitl_response($resp));
    }

    public function test_detects_with_object_response(): void
    {
        $resp = (object) [
            'decision' => 'BLOCK',
            'ucp' => (object) [
                'state' => 'requires_escalation',
                'reason_code' => 'trusteed:R043.something',
            ],
        ];
        $this->assertTrue(AgenticMCP_R043_Hitl_Gate::is_hitl_response($resp));
    }

    public function test_rejects_plain_block(): void
    {
        $resp = [
            'decision' => 'BLOCK',
            'ucp' => ['state' => 'failed', 'reason_code' => 'trusteed:R001'],
        ];
        $this->assertFalse(AgenticMCP_R043_Hitl_Gate::is_hitl_response($resp));
    }

    public function test_rejects_allow_with_escalation(): void
    {
        $resp = [
            'decision' => 'ALLOW',
            'ucp' => ['state' => 'requires_escalation', 'reason_code' => 'trusteed:R043'],
        ];
        $this->assertFalse(AgenticMCP_R043_Hitl_Gate::is_hitl_response($resp));
    }

    public function test_rejects_other_rule_codes(): void
    {
        $resp = [
            'decision' => 'BLOCK',
            'ucp' => ['state' => 'requires_escalation', 'reason_code' => 'trusteed:R031.kill-switch'],
        ];
        $this->assertFalse(AgenticMCP_R043_Hitl_Gate::is_hitl_response($resp));
    }

    public function test_rule_code_from_extracts_canonical(): void
    {
        $resp = [
            'decision' => 'BLOCK',
            'ucp' => ['state' => 'requires_escalation', 'reason_code' => 'trusteed:R043.agent-checkout-approval-required'],
        ];
        $this->assertSame(
            'R043.agent-checkout-approval-required',
            AgenticMCP_R043_Hitl_Gate::rule_code_from($resp)
        );
    }

    public function test_rule_code_from_returns_empty_on_missing_prefix(): void
    {
        $resp = ['ucp' => ['reason_code' => 'R043.no-prefix']];
        $this->assertSame('', AgenticMCP_R043_Hitl_Gate::rule_code_from($resp));
    }

    public function test_rejects_missing_ucp(): void
    {
        $resp = ['decision' => 'BLOCK'];
        $this->assertFalse(AgenticMCP_R043_Hitl_Gate::is_hitl_response($resp));
    }
}
