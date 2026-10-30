<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/class-agent-discovery.php';

final class AgentDiscoveryTest extends TestCase
{
    public function test_llms_txt_points_to_the_mcp_and_warns_about_the_web_channel(): void
    {
        $txt = Trusteed_Agent_Discovery::build_llms_txt('Xurés Demo', 'https://www.trusteed.xyz/xures/mcp');

        $this->assertStringStartsWith('# Xurés Demo', $txt);
        $this->assertStringContainsString('MCP endpoint: https://www.trusteed.xyz/xures/mcp', $txt);
        $this->assertStringContainsString('create_cart', $txt);
        $this->assertStringContainsString('no verifiable agent identity', $txt);
        $this->assertStringContainsString('may treat it as a human purchase', $txt);
    }

    public function test_llms_txt_falls_back_to_a_name_when_the_site_has_none(): void
    {
        $this->assertStringStartsWith('# Store', Trusteed_Agent_Discovery::build_llms_txt('  ', 'https://x/mcp'));
    }

    public function test_only_the_exact_own_path_is_served(): void
    {
        $this->assertTrue(Trusteed_Agent_Discovery::is_llms_request('/llms.txt', '/llms.txt'));
        $this->assertTrue(Trusteed_Agent_Discovery::is_llms_request('/tienda/llms.txt', '/tienda/llms.txt'));
        $this->assertFalse(Trusteed_Agent_Discovery::is_llms_request('/llms.txt/', '/llms.txt'));
        $this->assertFalse(Trusteed_Agent_Discovery::is_llms_request('/otra/llms.txt', '/llms.txt'));
        $this->assertFalse(Trusteed_Agent_Discovery::is_llms_request('/llms.txt', ''));
    }

    public function test_not_connected_store_publishes_nothing(): void
    {
        update_option('agenticmcp_store_slug', '');
        update_option('agenticmcp_mcp_endpoint', '');
        $this->assertSame('', (new Trusteed_Agent_Discovery())->mcp_endpoint());

        update_option('agenticmcp_store_slug', 'xures');
        update_option('agenticmcp_mcp_endpoint', 'https://api.trusteed.xyz/xures/mcp/discovery');
        $this->assertSame('https://api.trusteed.xyz/xures/mcp/discovery', (new Trusteed_Agent_Discovery())->mcp_endpoint());

        // Guardado por un alta anterior a 2.5.5: el /mcp unificado está retirado (410), se publica su sucesor.
        update_option('agenticmcp_mcp_endpoint', 'https://api.trusteed.xyz/xures/mcp');
        $this->assertSame('https://api.trusteed.xyz/xures/mcp/discovery', (new Trusteed_Agent_Discovery())->mcp_endpoint());

        update_option('agenticmcp_store_slug', '');
        update_option('agenticmcp_mcp_endpoint', '');
    }
}
