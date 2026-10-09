<?php

declare(strict_types=1);

/**
 * Contrato del payload de /api/v1/plugin/catalog/sync.
 *
 * El backend valida cada producto con `productSchema`
 * (apps/api/src/routes/plugin.routes.ts):
 *   - categories[].id      -> z.string().min(1)          (un entero da 400)
 *   - stock_quantity       -> z.number().int().optional() (null da 400; ausente vale)
 *
 * Regresion 2026-10-09: el plugin enviaba `term_id` como entero y
 * `stock_quantity: null` en productos sin gestion de stock, asi que el lote
 * entero respondia 400 en cualquier tienda con categorias. Estos tests fijan
 * el contrato en el lado del plugin.
 *
 * No se carga WordPress: se stubean las pocas funciones que usa el SUT.
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}

if (!class_exists('WP_Term')) {
    class WP_Term
    {
        /** @var int */
        public $term_id;
        /** @var string */
        public $name;

        public function __construct(int $term_id, string $name)
        {
            $this->term_id = $term_id;
            $this->name = $name;
        }
    }
}

if (!function_exists('wp_strip_all_tags')) {
    function wp_strip_all_tags($s)
    {
        return strip_tags((string) $s);
    }
}

if (!function_exists('get_term')) {
    function get_term($id, $taxonomy = '')
    {
        return $GLOBALS['__amcp_terms'][(int) $id] ?? null;
    }
}

if (!function_exists('get_the_terms')) {
    function get_the_terms($id, $taxonomy)
    {
        return [];
    }
}

if (!function_exists('wp_list_pluck')) {
    function wp_list_pluck($list, $field)
    {
        return array_map(static fn($o) => $o->$field, (array) $list);
    }
}

if (!function_exists('wp_get_attachment_url')) {
    function wp_get_attachment_url($id)
    {
        return false;
    }
}

if (!function_exists('get_post_meta')) {
    function get_post_meta($id, $key = '', $single = false)
    {
        return '';
    }
}

if (!function_exists('esc_url')) {
    function esc_url($u)
    {
        return (string) $u;
    }
}

if (!function_exists('absint')) {
    function absint($v): int
    {
        return abs((int) $v);
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($s)
    {
        return trim((string) $s);
    }
}

if (!function_exists('get_woocommerce_currency')) {
    function get_woocommerce_currency()
    {
        return 'EUR';
    }
}

require_once __DIR__ . '/../../includes/class-catalog-sync.php';

use PHPUnit\Framework\TestCase;

/** Producto minimo con la superficie que lee product_to_payload(). */
final class CatalogContractFakeProduct
{
    /** @var int|null */
    public $stock_quantity;
    /** @var array<int,int> */
    public $category_ids;

    /**
     * @param int|null        $stock_quantity null = el producto no gestiona stock.
     * @param array<int,int>  $category_ids
     */
    public function __construct(?int $stock_quantity, array $category_ids)
    {
        $this->stock_quantity = $stock_quantity;
        $this->category_ids = $category_ids;
    }

    public function get_id() { return 42; }
    public function get_name() { return 'Mel Multifloral'; }
    public function get_sku() { return 'XUR-MEL-MULTI-500'; }
    public function get_price() { return '12.9'; }
    public function get_stock_status() { return 'instock'; }
    public function get_stock_quantity() { return $this->stock_quantity; }
    public function get_permalink() { return 'https://tienda.example/producto/mel/'; }
    public function get_description() { return '<p>Desc</p>'; }
    public function get_short_description() { return 'Corta'; }
    public function get_regular_price() { return '12.9'; }
    public function get_sale_price() { return ''; }
    public function get_category_ids() { return $this->category_ids; }
    public function get_image_id() { return 0; }
    public function get_gallery_image_ids() { return []; }
    public function get_type() { return 'simple'; }
}

final class CatalogSyncPayloadContractTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['__amcp_terms'] = [
            7 => new WP_Term(7, 'Mel e Apicultura'),
            9 => new WP_Term(9, 'Caixas e Regalos'),
        ];
    }

    /** @return array<string,mixed> */
    private function payloadFor(CatalogContractFakeProduct $product): array
    {
        $ref = new ReflectionClass(Trusteed_Catalog_Sync::class);
        $sync = $ref->newInstanceWithoutConstructor();
        $method = $ref->getMethod('product_to_payload');
        $method->setAccessible(true);

        return $method->invoke($sync, $product);
    }

    public function test_category_ids_are_strings_not_integers(): void
    {
        $payload = $this->payloadFor(new CatalogContractFakeProduct(5, [7, 9]));

        $this->assertCount(2, $payload['categories']);
        foreach ($payload['categories'] as $category) {
            $this->assertIsString($category['id'], 'productSchema exige categories[].id como string');
            $this->assertNotSame('', $category['id']);
        }
        $this->assertSame('7', $payload['categories'][0]['id']);
    }

    public function test_category_ids_serialize_as_json_strings(): void
    {
        $payload = $this->payloadFor(new CatalogContractFakeProduct(5, [7]));

        $this->assertStringContainsString('"id":"7"', json_encode($payload['categories']));
    }

    public function test_stock_quantity_is_omitted_when_the_product_does_not_manage_stock(): void
    {
        $payload = $this->payloadFor(new CatalogContractFakeProduct(null, [7]));

        $this->assertArrayNotHasKey(
            'stock_quantity',
            $payload,
            'productSchema acepta stock_quantity ausente pero rechaza null'
        );
        $this->assertStringNotContainsString('"stock_quantity":null', json_encode($payload));
    }

    public function test_stock_quantity_is_kept_as_an_integer_when_managed(): void
    {
        $payload = $this->payloadFor(new CatalogContractFakeProduct(42, [7]));

        $this->assertArrayHasKey('stock_quantity', $payload);
        $this->assertSame(42, $payload['stock_quantity']);
    }

    public function test_zero_stock_is_not_confused_with_unmanaged_stock(): void
    {
        $payload = $this->payloadFor(new CatalogContractFakeProduct(0, [7]));

        $this->assertArrayHasKey('stock_quantity', $payload);
        $this->assertSame(0, $payload['stock_quantity']);
    }
}
