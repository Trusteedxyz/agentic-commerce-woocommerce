<?php

declare(strict_types=1);

/**
 * R012 en el checkout nativo de WooCommerce ve las categorías Y las etiquetas.
 *
 * Xures 2026-10-25: el «Licor de Herbas» tenía la categoría `bebidas` y la
 * etiqueta `alcohol`. El plugin solo enviaba `product_cat`, así que la etiqueta
 * que el comerciante puso a propósito no llegaba al motor.
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}

if (!function_exists('wp_get_post_terms')) {
    /** @return array<int,string>|\WP_Error */
    function wp_get_post_terms($post_id, $taxonomy = 'post_tag', $args = [])
    {
        $fixture = $GLOBALS['trusteed_test_terms'][$post_id][$taxonomy] ?? [];
        return $fixture;
    }
}

require_once __DIR__ . '/../../includes/class-cart-signals.php';

use PHPUnit\Framework\TestCase;

final class ProductTermSlugsTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['trusteed_test_terms'] = [
            33 => [
                'product_cat' => ['bebidas'],
                'product_tag' => ['licor', 'alcohol', 'restringido'],
            ],
            34 => ['product_cat' => ['bebidas'], 'product_tag' => ['bebidas']],
        ];
    }

    public function test_incluye_categorias_y_etiquetas(): void
    {
        $this->assertSame(
            ['bebidas', 'licor', 'alcohol', 'restringido'],
            Trusteed_Cart_Signals::product_term_slugs(33)
        );
    }

    public function test_no_repite_un_slug_que_esta_en_las_dos_taxonomias(): void
    {
        $this->assertSame(['bebidas'], Trusteed_Cart_Signals::product_term_slugs(34));
    }

    public function test_un_producto_sin_terminos_devuelve_vacio(): void
    {
        $this->assertSame([], Trusteed_Cart_Signals::product_term_slugs(999));
    }
}
