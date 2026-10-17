<?php

declare(strict_types=1);

/**
 * L1: el POST de sincronización lleva el Bearer del comerciante y no puede
 * seguir redirecciones (reenviaría la cabecera a otro destino).
 */

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class SyncHttpRedirectionTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['__amcp_wp_remote_post_response'] = ['response' => ['code' => 302], 'body' => ''];
        require_once __DIR__ . '/../../includes/class-paypal-sync.php';
        require_once __DIR__ . '/../../includes/class-stripe-sync.php';
    }

    public function test_paypal_default_post_no_sigue_redirecciones(): void
    {
        $r = Trusteed_PayPal_Sync::default_post('https://api.trusteed.xyz/x', ['timeout' => 5]);
        $this->assertSame(0, $GLOBALS['__amcp_wp_remote_post_last_args']['redirection'] ?? null);
        $this->assertSame(302, $r['status']);
    }

    public function test_stripe_default_post_no_sigue_redirecciones(): void
    {
        $r = Trusteed_Stripe_Sync::default_post('https://api.trusteed.xyz/x', ['timeout' => 5]);
        $this->assertSame(0, $GLOBALS['__amcp_wp_remote_post_last_args']['redirection'] ?? null);
        $this->assertSame(302, $r['status']);
    }
}
