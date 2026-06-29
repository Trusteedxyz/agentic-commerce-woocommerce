<?php

declare(strict_types=1);

/**
 * Gap 4 — failure-mode option whitelisting + drift log.
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}

if (!function_exists('wp_json_encode')) {
    function wp_json_encode($v): string
    {
        return (string) json_encode($v);
    }
}

// AgenticMCP_Plugin pulls a lot of dependencies via require_once chains we
// can't satisfy in unit tests. We re-declare the helper method as a small
// surrogate class so the normalization logic can be exercised directly.
if (!class_exists('AgenticMCP_Plugin_FailureModeSurrogate')) {
    final class AgenticMCP_Plugin_FailureModeSurrogate
    {
        private const ALLOWED = ['enforce', 'observe'];

        public static function normalize(string $raw): string
        {
            $normalized = strtolower(trim($raw));
            if (in_array($normalized, self::ALLOWED, true)) {
                return $normalized;
            }
            error_log(sprintf(
                '[amcp.config_drift] amcp_failure_mode has invalid value %s — falling back to enforce',
                wp_json_encode($raw)
            ));
            return 'enforce';
        }
    }
}

use PHPUnit\Framework\TestCase;

final class FailureModeNormalizationTest extends TestCase
{
    public function test_accepts_enforce(): void
    {
        $this->assertSame('enforce', AgenticMCP_Plugin_FailureModeSurrogate::normalize('enforce'));
    }

    public function test_accepts_observe(): void
    {
        $this->assertSame('observe', AgenticMCP_Plugin_FailureModeSurrogate::normalize('observe'));
    }

    public function test_normalizes_case_and_whitespace(): void
    {
        $this->assertSame('observe', AgenticMCP_Plugin_FailureModeSurrogate::normalize('  OBSERVE '));
        $this->assertSame('enforce', AgenticMCP_Plugin_FailureModeSurrogate::normalize('Enforce'));
    }

    public function test_falls_back_to_enforce_on_drift(): void
    {
        $this->assertSame('enforce', AgenticMCP_Plugin_FailureModeSurrogate::normalize('panic'));
        $this->assertSame('enforce', AgenticMCP_Plugin_FailureModeSurrogate::normalize(''));
        $this->assertSame('enforce', AgenticMCP_Plugin_FailureModeSurrogate::normalize('block-everything'));
    }

    public function test_normalize_matches_real_plugin_logic(): void
    {
        // Sanity check: keep the surrogate aligned with the real class.
        // We grep the real class file for the allowed-modes constant body.
        $plugin_src = file_get_contents(__DIR__ . '/../../includes/class-plugin.php');
        $this->assertIsString($plugin_src);
        $this->assertStringContainsString("ALLOWED_FAILURE_MODES = array( 'enforce', 'observe' )", $plugin_src);
        $this->assertStringContainsString('normalize_failure_mode', $plugin_src);
    }
}
