<?php

declare(strict_types=1);

/**
 * CAB-001: la sincronización de catálogo no hace HTTP en el camino del pago;
 * sólo encola (Action Scheduler) un trabajo deduplicado por producto.
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}
if (!function_exists('absint')) {
    function absint($v): int { return abs((int) $v); }
}
if (!function_exists('is_wp_error')) {
    function is_wp_error($t): bool { return false; }
}
if (!function_exists('as_enqueue_async_action')) {
    function as_enqueue_async_action($hook, $args = [], $group = '') { $GLOBALS['__cab001_enqueued'][] = [$hook, $args]; return 1; }
}
if (!function_exists('as_has_scheduled_action')) {
    function as_has_scheduled_action($hook, $args = null, $group = '') {
        foreach ($GLOBALS['__cab001_enqueued'] ?? [] as $e) { if ($e === [$hook, $args]) { return true; } }
        return false;
    }
}
if (!class_exists('WC_Product')) {
    class WC_Product
    {
        public function __construct(private int $id = 0, private int $parent = 0) {}
        public function get_id() { return $this->id; }
        public function get_parent_id() { return $this->parent; }
    }
}
if (!class_exists('Trusteed_Api_Client')) {
    class Trusteed_Api_Client
    {
        public int $calls = 0;
        public function post(string $endpoint, array $body) { $this->calls++; return ['status' => 200, 'body' => []]; }
    }
}

require_once __DIR__ . '/../../includes/class-catalog-sync.php';

use PHPUnit\Framework\TestCase;

final class CatalogSyncAsyncHooksTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['__cab001_enqueued'] = [];
    }

    private function sync(bool $connected): Trusteed_Catalog_Sync
    {
        return new class(new Trusteed_Api_Client(), $connected) extends Trusteed_Catalog_Sync {
            public function __construct($client, private bool $connected) { parent::__construct($client); }
            protected function is_connected() { return $this->connected; }
        };
    }

    public function test_product_change_only_enqueues_and_dedupes(): void
    {
        $s = $this->sync(true);
        $s->on_product_changed(5);
        $s->on_product_changed(5);
        $this->assertSame([[Trusteed_Catalog_Sync::SYNC_HOOK, [5]]], $GLOBALS['__cab001_enqueued']);
    }

    public function test_variation_stock_change_syncs_the_parent(): void
    {
        $this->sync(true)->on_variation_stock_change(new WC_Product(11, 5));
        $this->assertSame([[Trusteed_Catalog_Sync::SYNC_HOOK, [5]]], $GLOBALS['__cab001_enqueued']);
    }

    public function test_disconnected_store_enqueues_nothing(): void
    {
        $this->sync(false)->on_product_changed(5);
        $this->assertSame([], $GLOBALS['__cab001_enqueued']);
    }

    public function test_stock_change_in_checkout_does_no_http(): void
    {
        $s = $this->sync(true);
        $s->on_stock_change(new WC_Product(7));
        $client = (new ReflectionProperty(Trusteed_Catalog_Sync::class, 'api_client'));
        $client->setAccessible(true);
        $this->assertSame(0, $client->getValue($s)->calls);
    }
}
