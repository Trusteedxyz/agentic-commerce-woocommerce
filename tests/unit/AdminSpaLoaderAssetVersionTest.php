<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * The SPA bundle keeps a STABLE file name (`admin-spa.js`, no content hash), so a
 * URL without `?ver=` lets the browser keep serving the old bundle after a plugin
 * update. Seen on Xures (2026-10-26): the merchant ran the pre-fix JS and got the
 * same bug twice. The script and every CSS chunk must carry a version that changes
 * with the file.
 */
final class AdminSpaLoaderAssetVersionTest extends TestCase
{
    private string $source;

    protected function setUp(): void
    {
        $this->source = (string) file_get_contents(
            dirname(__DIR__, 2) . '/includes/admin/class-admin-spa-loader.php'
        );
        $this->assertStringContainsString('wp_enqueue_script', $this->source);
    }

    public function test_script_and_styles_are_never_enqueued_with_a_null_version(): void
    {
        $this->assertDoesNotMatchRegularExpression(
            '/wp_enqueue_(script|style)\([^;]*array\(\),\s*null\b/',
            $this->source,
            'A null version leaves the stable-name bundle cacheable across updates.'
        );
    }

    public function test_the_version_comes_from_the_file_itself(): void
    {
        $this->assertStringContainsString('filemtime', $this->source);
    }
}
