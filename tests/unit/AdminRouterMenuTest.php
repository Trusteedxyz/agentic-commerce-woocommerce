<?php

declare(strict_types=1);

/**
 * Unified admin menu (design 2026-10-03 §4): the sidebar shows 7 entries in a fixed
 * order, and the three pages that left the menu (Agent Readiness, Security, Merchant
 * Center) stay REGISTERED so they keep resolving by URL and from the SPA's links.
 *
 * Why the hiding is asserted on admin_head and not on admin_menu: wp-admin/admin.php
 * runs user_can_access_admin_page() against $submenu before admin_head. Removing the
 * entries on admin_menu would turn admin.php?page=amcp-seguridad into "not allowed".
 */

namespace {
    if (!defined('ABSPATH')) {
        define('ABSPATH', '/tmp/abspath-stub/');
    }

    $GLOBALS['__amcp_router_menu_calls'] = ['top' => [], 'sub' => [], 'removed' => []];

    if (!function_exists('add_menu_page')) {
        function add_menu_page($title, $menu, $cap, $slug, $cb = null, $icon = '', $pos = null): string
        {
            $GLOBALS['__amcp_router_menu_calls']['top'][] = $slug;
            return $slug;
        }
    }
    if (!function_exists('add_submenu_page')) {
        function add_submenu_page($parent, $title, $menu, $cap, $slug, $cb = null): string
        {
            $GLOBALS['__amcp_router_menu_calls']['sub'][] = [
                'parent' => $parent, 'label' => $menu, 'cap' => $cap, 'slug' => $slug, 'cb' => $cb,
            ];
            return $slug;
        }
    }
    if (!function_exists('remove_submenu_page')) {
        function remove_submenu_page($parent, $slug): void
        {
            $GLOBALS['__amcp_router_menu_calls']['removed'][] = [$parent, $slug];
        }
    }
}

namespace Trusteed\Tests\Unit {

    use PHPUnit\Framework\TestCase;

    require_once __DIR__ . '/../../includes/admin/class-admin-router.php';

    final class AdminRouterMenuTest extends TestCase
    {
        private \Trusteed_Admin_Router $router;

        protected function setUp(): void
        {
            if (!defined('ABSPATH')) {
                define('ABSPATH', '/tmp/abspath-stub/');
            }
            $GLOBALS['__amcp_router_menu_calls'] = ['top' => [], 'sub' => [], 'removed' => []];
            $this->router = new \Trusteed_Admin_Router('http://x/', __DIR__ . '/../../');
            $this->router->register_menus();
        }

        public function testSidebarShowsTheSevenUnifiedEntriesInOrder(): void
        {
            // Visible = registered minus what hide_legacy_submenus() removes.
            $this->router->hide_legacy_submenus();
            $removed = array_column($GLOBALS['__amcp_router_menu_calls']['removed'], 1);
            $visible = array_values(array_filter(
                $GLOBALS['__amcp_router_menu_calls']['sub'],
                static fn (array $e): bool => !in_array($e['slug'], $removed, true)
            ));

            self::assertSame(
                ['Home', 'My sales', 'Agents', 'My rules', 'Payment methods', 'Trust', 'Settings'],
                array_column($visible, 'label')
            );
            self::assertSame(
                ['amcp-inicio', 'amcp-mis-ventas', 'amcp-agentes', 'amcp-mis-reglas', 'amcp-payment-methods', 'amcp-trust-center', 'amcp-settings-embed'],
                array_column($visible, 'slug')
            );
        }

        public function testLegacyPagesStayRegisteredAndAreOnlyHiddenAtAdminHead(): void
        {
            $registered = array_column($GLOBALS['__amcp_router_menu_calls']['sub'], 'slug');
            foreach (['amcp-agent-readiness', 'amcp-seguridad', 'amcp-merchant-center'] as $slug) {
                self::assertContains($slug, $registered, "$slug must stay registered (reachable by URL).");
            }

            // Nothing is removed while the menu is being registered...
            self::assertSame([], $GLOBALS['__amcp_router_menu_calls']['removed']);

            // ...only the hook run on admin_head removes the three of them.
            $this->router->hide_legacy_submenus();
            $removed = $GLOBALS['__amcp_router_menu_calls']['removed'];
            self::assertEqualsCanonicalizing(
                [
                    ['amcp-inicio', 'amcp-agent-readiness'],
                    ['amcp-inicio', 'amcp-seguridad'],
                    ['amcp-inicio', 'amcp-merchant-center'],
                ],
                $removed
            );
        }

        public function testEverySlugKeepsItsCapabilityAndParent(): void
        {
            self::assertCount(10, $GLOBALS['__amcp_router_menu_calls']['sub']);
            foreach ($GLOBALS['__amcp_router_menu_calls']['sub'] as $e) {
                self::assertSame('amcp-inicio', $e['parent']);
                self::assertSame('manage_woocommerce', $e['cap']);
                self::assertIsCallable($e['cb']);
            }
        }

        public function testHideHookIsWiredOnAdminHead(): void
        {
            \Stub_Captured_Hooks_Registry::$hooks = [];
            $this->router->init();
            self::assertArrayHasKey('admin_menu', \Stub_Captured_Hooks_Registry::$hooks);
            self::assertArrayHasKey('admin_head', \Stub_Captured_Hooks_Registry::$hooks);
        }

        public function testPaymentMethodsSectionIsInTheSpaMountAllowlist(): void
        {
            // Without this entry the page silently fell back to 'trust-center'.
            $src = (string) file_get_contents(__DIR__ . '/../../includes/admin/views/spa-mount.php');
            self::assertStringContainsString("'payment-methods'", $src);
        }
    }
}
