<?php

declare(strict_types=1);

/**
 * 2026-10-16 — Variaciones de los productos variables en el payload de
 * /api/v1/plugin/catalog/sync.
 *
 * Xures sincroniza por empuje del plugin, y el payload no llevaba las
 * variaciones: el catálogo guardaba `variants: []`, `create_cart` no podía
 * exigir `variant_id` y la cotización de la tienda devolvía 409
 * line_unavailable (`variation_id: 0`). Estos tests fijan el contrato:
 *   - sólo los productos variables llevan `variations`;
 *   - una variación sin precio se omite (la API rechaza `price` vacío y
 *     tumbaría el lote entero);
 *   - `attributes` es un OBJETO JSON aunque esté vacío (un `[]` no casa con
 *     `z.record`);
 *   - los atributos de taxonomía salen con su etiqueta y el nombre del
 *     término, no con slugs.
 */

require_once __DIR__ . '/CatalogSyncPayloadContractTest.php';

if (!function_exists('wc_get_product')) {
    function wc_get_product($id)
    {
        return $GLOBALS['__amcp_variations'][(int) $id] ?? false;
    }
}
if (!function_exists('taxonomy_exists')) {
    function taxonomy_exists($t)
    {
        return $t === 'pa_tamano';
    }
}
if (!function_exists('wc_attribute_label')) {
    function wc_attribute_label($name, $product = null)
    {
        return $name === 'pa_tamano' ? 'Tamaño' : $name;
    }
}
if (!function_exists('get_term_by')) {
    function get_term_by($field, $value, $taxonomy)
    {
        $names = ['esencial' => 'Esencial', 'premium' => 'Premium'];
        return isset($names[$value]) ? new WP_Term(1, $names[$value]) : false;
    }
}

final class CatalogVariationFake
{
    /** @param array<string,string> $attrs */
    public function __construct(
        private int $id,
        private string $price,
        private array $attrs,
        private ?int $qty = 3,
        private string $stock = 'instock',
        private bool $purchasable = true
    ) {
    }
    public function get_id() { return $this->id; }
    public function get_name() { return 'Caixa - variación ' . $this->id; }
    public function get_price() { return $this->price; }
    public function get_sku() { return 'SKU-' . $this->id; }
    public function get_stock_status() { return $this->stock; }
    public function get_stock_quantity() { return $this->qty; }
    public function is_purchasable() { return $this->purchasable; }
    public function get_variation_attributes($with_prefix = true) { return $this->attrs; }
}

final class CatalogVariableFakeProduct
{
    /** @param array<int,int> $children */
    public function __construct(private array $children) {}
    public function get_id() { return 27; }
    public function get_name() { return 'Caixa Xurés Personalizada'; }
    public function get_sku() { return ''; }
    public function get_price() { return '35'; }
    public function get_stock_status() { return 'instock'; }
    public function get_stock_quantity() { return null; }
    public function get_permalink() { return 'https://tienda.example/producto/caixa/'; }
    public function get_description() { return ''; }
    public function get_short_description() { return ''; }
    public function get_regular_price() { return ''; }
    public function get_sale_price() { return ''; }
    public function get_category_ids() { return []; }
    public function get_image_id() { return 0; }
    public function get_gallery_image_ids() { return []; }
    public function get_type() { return 'variable'; }
    public function is_type($t) { return $t === 'variable'; }
    public function get_children() { return $this->children; }
}

final class CatalogSyncVariationsTest extends \PHPUnit\Framework\TestCase
{
    /** @return array<string,mixed> */
    private function payloadFor($product): array
    {
        $ref = new ReflectionClass(Trusteed_Catalog_Sync::class);
        $sync = $ref->newInstanceWithoutConstructor();
        $m = $ref->getMethod('product_to_payload');
        $m->setAccessible(true);
        return $m->invoke($sync, $product);
    }

    protected function setUp(): void
    {
        $GLOBALS['__amcp_terms'] = [];
        $GLOBALS['__amcp_variations'] = [
            28 => new CatalogVariationFake(28, '35', ['pa_tamano' => 'esencial']),
            29 => new CatalogVariationFake(29, '', ['pa_tamano' => 'sin-precio']),
            30 => new CatalogVariationFake(30, '70', ['pa_tamano' => 'premium'], null, 'outofstock', false),
            31 => new CatalogVariationFake(31, '40', ['grabado' => 'Sí']),
            32 => new CatalogVariationFake(32, '45', []),
        ];
    }

    public function test_variable_product_carries_its_priced_variations(): void
    {
        $p = $this->payloadFor(new CatalogVariableFakeProduct([28, 29, 30]));
        $this->assertArrayHasKey('variations', $p);
        $ids = array_column($p['variations'], 'external_id');
        $this->assertSame(['28', '30'], $ids, 'la variación 29 no tiene precio y se omite');

        $first = $p['variations'][0];
        $this->assertSame('35', $first['price']);
        $this->assertSame('Esencial', $first['title']);
        $this->assertSame('SKU-28', $first['sku']);
        $this->assertSame('instock', $first['stock_status']);
        $this->assertSame(3, $first['stock_quantity']);
        $this->assertTrue($first['purchasable']);
        $this->assertSame(['Tamaño' => 'Esencial'], (array) $first['attributes']);

        $premium = $p['variations'][1];
        $this->assertArrayNotHasKey('stock_quantity', $premium, 'null no se envía');
        $this->assertFalse($premium['purchasable']);
    }

    public function test_custom_attribute_keeps_its_name_and_value(): void
    {
        $p = $this->payloadFor(new CatalogVariableFakeProduct([31]));
        $this->assertSame(['grabado' => 'Sí'], (array) $p['variations'][0]['attributes']);
    }

    public function test_attributes_serialize_as_json_object_even_when_empty(): void
    {
        $p = $this->payloadFor(new CatalogVariableFakeProduct([32]));
        $json = json_encode($p['variations'][0]);
        $this->assertStringContainsString('"attributes":{}', $json);
        $this->assertSame('Caixa - variación 32', $p['variations'][0]['title']);
    }

    public function test_simple_product_has_no_variations_key(): void
    {
        $p = $this->payloadFor(new CatalogContractFakeProduct(5, []));
        $this->assertArrayNotHasKey('variations', $p);
    }
}
