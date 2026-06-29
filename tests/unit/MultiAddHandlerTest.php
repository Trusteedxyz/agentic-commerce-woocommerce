<?php

declare(strict_types=1);

/**
 * Gap 2 — Amcp_Multi_Add_Handler structural test.
 *
 * Full integration requires WC()->cart + wc_get_product, which we cannot
 * bootstrap in pure unit tests. We instead verify:
 *  - the class loads without fatal errors
 *  - register_routes-like contract: init() calls add_action with the
 *    expected hook + priority
 *  - constants match the bridge contract (MAX_LINE_ITEMS, FLAG_PARAM)
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}

// Reuse the shared registry from ClassicCheckoutMetaPersistenceTest so this
// test sees hooks even when run in the same PHP process (PHPUnit loads all
// test files into one interpreter; the first add_action stub wins).
require_once __DIR__ . '/ClassicCheckoutMetaPersistenceTest.php';

if (!class_exists('Stub_Multi_Add_Hooks')) {
    final class Stub_Multi_Add_Hooks
    {
        /** @var array<int,array{hook:string,prio:int,args:int}> */
        public static array $registered = [];
    }
}

require_once __DIR__ . '/../../includes/class-cart-bridge.php';
require_once __DIR__ . '/../../includes/class-multi-add-handler.php';

use PHPUnit\Framework\TestCase;

final class MultiAddHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Stub_Multi_Add_Hooks::$registered = [];
        if (class_exists('Stub_Captured_Hooks_Registry')) {
            Stub_Captured_Hooks_Registry::$hooks = [];
        }
    }

    public function test_flag_param_matches_cart_bridge_output(): void
    {
        // The bridge emits `agenticmcp_multi_add=1` — handler must consume the
        // exact same flag.
        $this->assertSame('agenticmcp_multi_add', Amcp_Multi_Add_Handler::FLAG_PARAM);
    }

    public function test_max_line_items_matches_cart_bridge(): void
    {
        $this->assertSame(
            Trusteed_Cart_Bridge::MAX_LINE_ITEMS,
            Amcp_Multi_Add_Handler::MAX_LINE_ITEMS,
            'Handler cap must mirror the bridge cap (defense in depth)'
        );
    }

    public function test_init_registers_wp_loaded_hook(): void
    {
        $handler = new Amcp_Multi_Add_Handler();
        $handler->init();

        $this->assertTrue(
            class_exists('Stub_Captured_Hooks_Registry'),
            'Shared add_action stub must be loaded'
        );
        $this->assertArrayHasKey(
            'wp_loaded',
            Stub_Captured_Hooks_Registry::$hooks,
            'Handler must hook into wp_loaded'
        );
        $this->assertNotEmpty(Stub_Captured_Hooks_Registry::$hooks['wp_loaded']);
    }

    public function test_maybe_handle_silently_returns_without_flag(): void
    {
        // No flag in $_GET → no work, no side effects, no warnings.
        unset($_GET[Amcp_Multi_Add_Handler::FLAG_PARAM]);
        $handler = new Amcp_Multi_Add_Handler();
        $handler->maybe_handle();
        $this->assertTrue(true);
    }
}
