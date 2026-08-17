<?php

declare(strict_types=1);

/**
 * uninstall.php must remove EVERY option row the plugin can write.
 *
 * PRIVACY_POLICY.md promises merchant data is "permanently removed within 30
 * days" on uninstall, and several of these rows are live secrets encrypted at
 * rest (`trusteed_embed_wp_secret`, `trusteed_enforcement_hmac_secret`,
 * `trusteed_woo_webhook_secret`). uninstall.php originally listed only the eight
 * `agenticmcp_*` rows, so the whole `trusteed_*` / legacy `amcp_*` namespace —
 * introduced by the 2.1.0 rename — survived uninstallation.
 *
 * The primary test is a DRIFT GATE, not a mirror of uninstall.php: it scans the
 * plugin sources for every option key that can be written, seeds each one, runs
 * uninstall.php, and asserts nothing is left. A future `update_option()` added
 * anywhere in `includes/` therefore fails this test until uninstall.php learns
 * about it — which is the failure mode that produced this bug.
 *
 * Process isolation: uninstall.php defines no class and can only be `require`d
 * once per process, and it needs WP_UNINSTALL_PLUGIN defined.
 */

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class UninstallOptionCleanupTest extends TestCase
{
    private const PLUGIN_DIR = __DIR__ . '/../..';

    /**
     * Option keys read via get_option() but owned by WordPress core, never by us.
     */
    private const FOREIGN_OPTIONS = ['active_plugins'];

    protected function setUp(): void
    {
        $GLOBALS['__amcp_options_store']    = [];
        $GLOBALS['__amcp_option_calls']     = [];
        $GLOBALS['__amcp_transients_store'] = [];
        $GLOBALS['__amcp_deleted_options']  = [];
    }

    /**
     * Every PHP source file that can touch an option row.
     *
     * @return string[]
     */
    private function sourceFiles(): array
    {
        $files = [self::PLUGIN_DIR . '/trusteed-for-woocommerce.php'];
        $it    = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(self::PLUGIN_DIR . '/includes', RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ('php' === $file->getExtension()) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Statically derive every option key the plugin can persist.
     *
     * Three shapes exist in this codebase:
     *   1. `update_option( 'agenticmcp_x' )`      — fully-qualified literal.
     *   2. `const SOMETHING_OPTION = 'amcp_x'`    — indirected through a class
     *      constant (class-billing-webhooks.php, class-plugin.php).
     *   3. `Trusteed_Options::get_option( 'x' )`  — BARE key; the accessor
     *      writes `trusteed_x` and read-falls-back to legacy `amcp_x`, so BOTH
     *      rows must die or the fallback resurrects a stale secret.
     *
     * @return string[] Sorted, de-duplicated, fully-qualified option names.
     */
    private function discoverOptionKeys(): array
    {
        $keys = [];

        foreach ($this->sourceFiles() as $file) {
            $src = (string) file_get_contents($file);

            // Shape 1 — direct literal writes/reads/deletes. The `(?<![:\w])`
            // guard matters: without it, `Trusteed_Options::delete_option('x')`
            // also matches here and the gate would demand deletion of a
            // phantom UNPREFIXED `x` row that nothing ever writes.
            preg_match_all(
                "/(?<![:\w])(?:update_option|add_option|delete_option|get_option)\(\s*'([a-z0-9_]+)'/",
                $src,
                $m
            );
            $keys = array_merge($keys, $m[1]);

            // Shape 2 — class constants holding an option name.
            preg_match_all("/const\s+[A-Z0-9_]*OPTION[A-Z0-9_]*\s*=\s*'([a-z0-9_]+)'/", $src, $m);
            $keys = array_merge($keys, $m[1]);

            // Shape 3 — bare keys behind the Trusteed_Options accessor.
            preg_match_all(
                "/Trusteed_Options::(?:get_option|delete_option)\(\s*'([a-z0-9_]+)'/",
                $src,
                $m
            );
            foreach ($m[1] as $bare) {
                $keys[] = 'trusteed_' . $bare;
                $keys[] = 'amcp_' . $bare;
            }
        }

        $keys = array_values(array_diff(array_unique($keys), self::FOREIGN_OPTIONS));
        sort($keys);

        return $keys;
    }

    private function runUninstall(): void
    {
        if (!defined('WP_UNINSTALL_PLUGIN')) {
            define('WP_UNINSTALL_PLUGIN', 'trusteed-for-woocommerce/trusteed-for-woocommerce.php');
        }
        require self::PLUGIN_DIR . '/uninstall.php';
    }

    public function testSourceScanFindsTheKnownOptionNamespaces(): void
    {
        $keys = $this->discoverOptionKeys();

        // Sanity-check the scanner itself: if these regexes silently stop
        // matching, the drift gate below would pass vacuously.
        $this->assertContains('agenticmcp_api_key', $keys);
        $this->assertContains('agenticmcp_previous_tier', $keys, 'shape 2 (class constant) not matched');
        $this->assertContains('trusteed_embed_wp_secret', $keys, 'shape 3 (bare accessor key) not matched');
        $this->assertContains('amcp_embed_wp_secret', $keys, 'legacy amcp_ alias not derived');
        $this->assertGreaterThan(20, count($keys), 'scanner found suspiciously few option keys');
    }

    public function testUninstallRemovesEveryOptionThePluginCanWrite(): void
    {
        $keys = $this->discoverOptionKeys();
        foreach ($keys as $key) {
            update_option($key, 'seeded-value');
        }

        $this->runUninstall();

        $survivors = array_keys($GLOBALS['__amcp_options_store']);
        sort($survivors);

        $this->assertSame(
            [],
            $survivors,
            "uninstall.php left plugin option rows behind:\n  " . implode("\n  ", $survivors)
        );
    }

    /**
     * The rows that made this a privacy/security bug rather than untidiness:
     * three encrypted secrets plus the identifiers that bind the install to a
     * merchant account.
     */
    public function testUninstallRemovesTheSecretBearingRows(): void
    {
        $secrets = [
            'trusteed_enforcement_hmac_secret',
            'trusteed_embed_wp_secret',
            'trusteed_woo_webhook_secret',
            'trusteed_merchant_id',
            'trusteed_api_base_url',
            'trusteed_failure_mode',
            'trusteed_enforcement_installation_id',
            'agenticmcp_api_key',
            'agenticmcp_webhook_secret',
        ];
        foreach ($secrets as $key) {
            update_option($key, 'seeded-secret');
        }

        $this->runUninstall();

        foreach ($secrets as $key) {
            $this->assertFalse(
                get_option($key, false),
                "{$key} survived uninstall — PRIVACY_POLICY.md promises removal"
            );
        }
    }

    /**
     * The legacy `amcp_*` alias must die too: Trusteed_Options::get_option()
     * read-falls-back to it, so deleting only the `trusteed_*` row would let a
     * reinstall silently resurrect the pre-rename secret.
     */
    public function testUninstallRemovesLegacyAmcpAliases(): void
    {
        $legacy = [
            'amcp_embed_wp_secret',
            'amcp_enforcement_hmac_secret',
            'amcp_woo_webhook_secret',
            'amcp_merchant_id',
        ];
        foreach ($legacy as $key) {
            update_option($key, 'seeded-legacy-secret');
        }

        $this->runUninstall();

        foreach ($legacy as $key) {
            $this->assertFalse(get_option($key, false), "legacy {$key} survived uninstall");
        }
    }

    /**
     * The direct-access guard must stay in front of the deletion loop: without
     * WP_UNINSTALL_PLUGIN, hitting uninstall.php over HTTP would wipe a live
     * install's credentials. Asserted on source shape because the guard is a
     * bare `exit;` that would end the PHPUnit process.
     */
    public function testUninstallKeepsTheDirectAccessGuardAheadOfDeletions(): void
    {
        $src = (string) file_get_contents(self::PLUGIN_DIR . '/uninstall.php');

        $this->assertMatchesRegularExpression(
            "/if\s*\(\s*!\s*defined\(\s*'WP_UNINSTALL_PLUGIN'\s*\)\s*\)\s*\{\s*exit;/",
            $src,
            'uninstall.php lost its WP_UNINSTALL_PLUGIN guard'
        );
        $this->assertLessThan(
            (int) strpos($src, 'delete_option'),
            (int) strpos($src, 'WP_UNINSTALL_PLUGIN'),
            'the guard must precede the first delete_option() call'
        );
    }
}
