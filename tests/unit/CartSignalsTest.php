<?php

declare(strict_types=1);

/**
 * B6 — WooCommerce cart-signal detection (R015/R016/R025/R026/R027/R028).
 *
 * Pure-function layer extracted from class-checkout-enforcer.php so the
 * detection logic can be tested without a WooCommerce kernel. Wrappers
 * in the enforcer call WC APIs (WC_Product, WC_Order) and pass primitive
 * dicts/arrays into these functions.
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/abspath-stub/');
}

// Minimal WC_Cart stub for unit testing R035/R036 helpers without a WooCommerce runtime.
if (!class_exists('WC_Cart')) {
    class WC_Cart
    {
        /** @var string */
        public $total = '0';
        /** @var array<int,array<string,mixed>> */
        public $items = [];

        public function get_total($context = 'view'): string
        {
            return $this->total;
        }

        /** @return array<int,array<string,mixed>> */
        public function get_cart(): array
        {
            return $this->items;
        }
    }
}

require_once __DIR__ . '/../../includes/class-cart-signals.php';

use PHPUnit\Framework\TestCase;

final class CartSignalsTest extends TestCase
{
    // ── R025 PO box ──────────────────────────────────────────────────────────

    public function test_detect_po_box_english(): void
    {
        $this->assertTrue(AgenticMCP_Cart_Signals::detect_po_box('P.O. Box 123', ''));
        $this->assertTrue(AgenticMCP_Cart_Signals::detect_po_box('PO BOX 7', 'Suite 4'));
        $this->assertTrue(AgenticMCP_Cart_Signals::detect_po_box('123 Main St', 'PO Box 9'));
        $this->assertTrue(AgenticMCP_Cart_Signals::detect_po_box('Post Office Box 17', ''));
    }

    public function test_detect_po_box_multi_language(): void
    {
        $this->assertTrue(AgenticMCP_Cart_Signals::detect_po_box('Apartado 123', ''));
        $this->assertTrue(AgenticMCP_Cart_Signals::detect_po_box('Apdo. Postal 47', ''));
        $this->assertTrue(AgenticMCP_Cart_Signals::detect_po_box('Boîte Postale 5', ''));
        $this->assertTrue(AgenticMCP_Cart_Signals::detect_po_box('Postfach 9', ''));
        $this->assertTrue(AgenticMCP_Cart_Signals::detect_po_box('Casella Postale 11', ''));
    }

    public function test_detect_po_box_negatives(): void
    {
        $this->assertFalse(AgenticMCP_Cart_Signals::detect_po_box('123 Box Hill Rd', ''));
        $this->assertFalse(AgenticMCP_Cart_Signals::detect_po_box('Posthumous Lane 4', ''));
        $this->assertFalse(AgenticMCP_Cart_Signals::detect_po_box('', ''));
        $this->assertFalse(AgenticMCP_Cart_Signals::detect_po_box('456 Oak Ave', 'Apt 2B'));
    }

    public function test_detect_freight_forwarder_known_brands(): void
    {
        $this->assertTrue(AgenticMCP_Cart_Signals::detect_freight_forwarder('123 Main', '', 'Shipito Inc'));
        $this->assertTrue(AgenticMCP_Cart_Signals::detect_freight_forwarder('456 Oak', 'MyUS Suite 9', ''));
        $this->assertTrue(AgenticMCP_Cart_Signals::detect_freight_forwarder('789 Elm', '', 'Stackry LLC'));
    }

    public function test_detect_freight_forwarder_negatives(): void
    {
        $this->assertFalse(AgenticMCP_Cart_Signals::detect_freight_forwarder('123 Main', '', 'Acme Corp'));
        $this->assertFalse(AgenticMCP_Cart_Signals::detect_freight_forwarder('', '', ''));
    }

    // ── R016 lowest stock ────────────────────────────────────────────────────

    public function test_lowest_stock_returns_min_finite(): void
    {
        $items = [
            ['stock' => 50],
            ['stock' => 3],
            ['stock' => 17],
        ];
        $this->assertSame(3, AgenticMCP_Cart_Signals::lowest_stock($items));
    }

    public function test_lowest_stock_ignores_null_unmanaged(): void
    {
        $items = [
            ['stock' => null], // not managing stock
            ['stock' => 5],
        ];
        $this->assertSame(5, AgenticMCP_Cart_Signals::lowest_stock($items));
    }

    public function test_lowest_stock_all_unmanaged_returns_null(): void
    {
        $this->assertNull(AgenticMCP_Cart_Signals::lowest_stock([['stock' => null], ['stock' => null]]));
        $this->assertNull(AgenticMCP_Cart_Signals::lowest_stock([]));
    }

    public function test_lowest_stock_zero_is_meaningful(): void
    {
        $items = [['stock' => 10], ['stock' => 0]];
        $this->assertSame(0, AgenticMCP_Cart_Signals::lowest_stock($items));
    }

    // ── R026 subscription ────────────────────────────────────────────────────

    public function test_detect_subscription_via_product_type(): void
    {
        $this->assertTrue(AgenticMCP_Cart_Signals::cart_has_subscription([
            ['type' => 'simple'],
            ['type' => 'subscription'],
        ]));
        $this->assertTrue(AgenticMCP_Cart_Signals::cart_has_subscription([
            ['type' => 'variable-subscription'],
        ]));
    }

    public function test_detect_subscription_via_recurring_flag(): void
    {
        $this->assertTrue(AgenticMCP_Cart_Signals::cart_has_subscription([
            ['type' => 'simple', 'is_subscription' => true],
        ]));
    }

    public function test_detect_subscription_negatives(): void
    {
        $this->assertFalse(AgenticMCP_Cart_Signals::cart_has_subscription([['type' => 'simple']]));
        $this->assertFalse(AgenticMCP_Cart_Signals::cart_has_subscription([]));
    }

    // ── R027 stored value (gift cards) ───────────────────────────────────────

    public function test_stored_value_total_sums_gift_card_lines(): void
    {
        $items = [
            ['type' => 'simple', 'price_cents' => 1500, 'qty' => 1],
            ['type' => 'pw-gift-card', 'price_cents' => 5000, 'qty' => 2], // 10000
            ['type' => 'gift-card', 'price_cents' => 2500, 'qty' => 1],
        ];
        $this->assertSame(12500, AgenticMCP_Cart_Signals::stored_value_cents($items));
    }

    public function test_stored_value_total_zero_when_no_gift_cards(): void
    {
        $items = [
            ['type' => 'simple', 'price_cents' => 1500, 'qty' => 2],
        ];
        $this->assertSame(0, AgenticMCP_Cart_Signals::stored_value_cents($items));
    }

    public function test_stored_value_detects_via_tag_or_category(): void
    {
        $items = [
            ['type' => 'simple', 'price_cents' => 1500, 'qty' => 1, 'tags' => ['gift-card']],
        ];
        $this->assertSame(1500, AgenticMCP_Cart_Signals::stored_value_cents($items));

        $items2 = [
            ['type' => 'simple', 'price_cents' => 2000, 'qty' => 1, 'categories' => ['gift-cards']],
        ];
        $this->assertSame(2000, AgenticMCP_Cart_Signals::stored_value_cents($items2));
    }

    // ── R028 B2B detection ───────────────────────────────────────────────────

    public function test_b2b_detected_when_company_present(): void
    {
        $this->assertTrue(AgenticMCP_Cart_Signals::is_b2b_order('Acme Corp', null, []));
        $this->assertTrue(AgenticMCP_Cart_Signals::is_b2b_order('', null, ['b2bking_b2b_customer']));
        $this->assertTrue(AgenticMCP_Cart_Signals::is_b2b_order('', null, ['wholesale_customer']));
    }

    public function test_b2b_negatives(): void
    {
        $this->assertFalse(AgenticMCP_Cart_Signals::is_b2b_order('', null, ['customer', 'subscriber']));
        $this->assertFalse(AgenticMCP_Cart_Signals::is_b2b_order('   ', null, []));
    }

    // ── R015 price-delta computation ─────────────────────────────────────────

    public function test_price_delta_bps_zero_when_unchanged(): void
    {
        $current  = ['100' => 1500, '200' => 2500];
        $snapshot = ['100' => 1500, '200' => 2500];
        $this->assertSame(0, AgenticMCP_Cart_Signals::max_price_delta_bps($current, $snapshot));
    }

    public function test_price_delta_bps_picks_max(): void
    {
        $current  = ['100' => 1500, '200' => 2700]; // 200: +200/2500 = 800 bps
        $snapshot = ['100' => 1500, '200' => 2500];
        $this->assertSame(800, AgenticMCP_Cart_Signals::max_price_delta_bps($current, $snapshot));
    }

    public function test_price_delta_bps_handles_decrease(): void
    {
        $current  = ['100' => 1000];          // -500/1500 = 3333 bps abs
        $snapshot = ['100' => 1500];
        $this->assertSame(3333, AgenticMCP_Cart_Signals::max_price_delta_bps($current, $snapshot));
    }

    public function test_price_delta_bps_ignores_missing_snapshot_entries(): void
    {
        $current  = ['100' => 1500, '300' => 9999]; // 300 not in snapshot → ignored
        $snapshot = ['100' => 1500];
        $this->assertSame(0, AgenticMCP_Cart_Signals::max_price_delta_bps($current, $snapshot));
    }

    public function test_price_delta_bps_zero_snapshot_safe(): void
    {
        // div-by-zero guard: snapshot entry of 0 must not crash, just skip.
        $current  = ['100' => 100];
        $snapshot = ['100' => 0];
        $this->assertSame(0, AgenticMCP_Cart_Signals::max_price_delta_bps($current, $snapshot));
    }

    // ── R015 HMAC envelope (parity with PS PriceSnapVerifier) ────────────────

    public function test_verify_price_snap_round_trip(): void
    {
        $key   = bin2hex(random_bytes(32));
        $prices = ['100' => 1500, '200' => 2500];
        $cookie = AgenticMCP_Cart_Signals::build_price_snap_cookie($prices, $key);
        $this->assertSame($prices, AgenticMCP_Cart_Signals::verify_price_snap($cookie, $key));
    }

    public function test_verify_price_snap_rejects_tampered(): void
    {
        $key    = bin2hex(random_bytes(32));
        $cookie = AgenticMCP_Cart_Signals::build_price_snap_cookie(['100' => 1500], $key);
        $decoded = json_decode((string) base64_decode($cookie, true), true);
        $decoded['p']['100'] = 1;
        $tampered = base64_encode((string) json_encode($decoded));
        $this->assertSame([], AgenticMCP_Cart_Signals::verify_price_snap($tampered, $key));
    }

    public function test_verify_price_snap_rejects_legacy_unsigned(): void
    {
        $legacy = base64_encode((string) json_encode(['100' => 1500]));
        $key    = bin2hex(random_bytes(32));
        $this->assertSame([], AgenticMCP_Cart_Signals::verify_price_snap($legacy, $key));
    }

    public function test_verify_price_snap_rejects_wrong_key(): void
    {
        $cookie = AgenticMCP_Cart_Signals::build_price_snap_cookie(['100' => 1500], bin2hex(random_bytes(32)));
        $wrong  = bin2hex(random_bytes(32));
        $this->assertSame([], AgenticMCP_Cart_Signals::verify_price_snap($cookie, $wrong));
    }

    // ═════════════════════════════════════════════════════════════════════
    // Spec-048 Sprint E (T-E37, ADR-054) — Agentic Starter Kit signals.
    // ═════════════════════════════════════════════════════════════════════

    public function test_r032_has_blocked_category_hits(): void
    {
        $items = [
            ['categoryIds' => ['books', 'fiction']],
            ['categoryIds' => ['adult']],
        ];
        $this->assertTrue(AgenticMCP_Cart_Signals::r032_has_blocked_category($items, ['adult', 'weapons']));
    }

    public function test_r032_has_blocked_category_passes_when_no_match(): void
    {
        $items = [['categoryIds' => ['books']]];
        $this->assertFalse(AgenticMCP_Cart_Signals::r032_has_blocked_category($items, ['adult']));
    }

    public function test_r032_has_blocked_category_empty_blocklist_passes(): void
    {
        $items = [['categoryIds' => ['adult']]];
        $this->assertFalse(AgenticMCP_Cart_Signals::r032_has_blocked_category($items, []));
    }

    public function test_r032_handles_missing_category_ids(): void
    {
        $items = [['id' => 'sku-1']]; // no categoryIds key
        $this->assertFalse(AgenticMCP_Cart_Signals::r032_has_blocked_category($items, ['adult']));
    }

    public function test_r034_has_blocked_sku_hits(): void
    {
        $items = [
            ['id' => 'sku-allowed'],
            ['id' => 'sku-banned'],
        ];
        $this->assertTrue(AgenticMCP_Cart_Signals::r034_has_blocked_sku($items, ['sku-banned']));
    }

    public function test_r034_has_blocked_sku_passes_when_no_match(): void
    {
        $items = [['id' => 'sku-1']];
        $this->assertFalse(AgenticMCP_Cart_Signals::r034_has_blocked_sku($items, ['sku-banned']));
    }

    public function test_r034_empty_blocklist_passes(): void
    {
        $items = [['id' => 'sku-1']];
        $this->assertFalse(AgenticMCP_Cart_Signals::r034_has_blocked_sku($items, []));
    }

    public function test_r038_item_count_sums_quantities(): void
    {
        $items = [
            ['quantity' => 2],
            ['quantity' => 5],
            ['quantity' => 1],
        ];
        $this->assertSame(8, AgenticMCP_Cart_Signals::r038_item_count($items));
    }

    public function test_r038_item_count_handles_empty_cart(): void
    {
        $this->assertSame(0, AgenticMCP_Cart_Signals::r038_item_count([]));
    }

    public function test_r038_item_count_ignores_negative(): void
    {
        $items = [['quantity' => -3], ['quantity' => 4]];
        $this->assertSame(4, AgenticMCP_Cart_Signals::r038_item_count($items));
    }

    public function test_r039_max_line_quantity_returns_max(): void
    {
        $items = [['quantity' => 2], ['quantity' => 7], ['quantity' => 3]];
        $this->assertSame(7, AgenticMCP_Cart_Signals::r039_max_line_quantity($items));
    }

    public function test_r039_max_line_quantity_empty_cart_zero(): void
    {
        $this->assertSame(0, AgenticMCP_Cart_Signals::r039_max_line_quantity([]));
    }

    public function test_r048_digital_good_types_detects_gift_card(): void
    {
        $items = [['type' => 'pw-gift-card']];
        $this->assertSame('gift_card', AgenticMCP_Cart_Signals::r048_digital_good_types($items));
    }

    public function test_r048_digital_good_types_detects_virtual_as_downloadable(): void
    {
        $items = [['type' => 'simple', 'is_virtual' => true]];
        $this->assertSame('downloadable', AgenticMCP_Cart_Signals::r048_digital_good_types($items));
    }

    public function test_r048_digital_good_types_returns_empty_for_physical(): void
    {
        $items = [['type' => 'simple', 'is_virtual' => false]];
        $this->assertSame('', AgenticMCP_Cart_Signals::r048_digital_good_types($items));
    }

    public function test_r048_digital_good_types_combines_multiple(): void
    {
        $items = [
            ['type' => 'simple', 'is_virtual' => true],
            ['type' => 'yith-gift-card'],
        ];
        $csv = AgenticMCP_Cart_Signals::r048_digital_good_types($items);
        $parts = explode(',', $csv);
        sort($parts);
        $this->assertSame(['downloadable', 'gift_card'], $parts);
    }

    public function test_r048_gift_card_via_tag(): void
    {
        $items = [['type' => 'simple', 'tags' => ['gift-card', 'sale']]];
        $this->assertSame('gift_card', AgenticMCP_Cart_Signals::r048_digital_good_types($items));
    }

    // ── R035 max-order-value ────────────────────────────────────────────────

    public function test_r035_blocks_when_total_exceeds_cap(): void
    {
        $cart = new WC_Cart();
        $cart->total = '150.00'; // 15000 cents
        $result = AgenticMCP_Cart_Signals::evaluateR035MaxOrderValue($cart, ['maxCents' => 10000]);
        $this->assertTrue($result['hit']);
        $this->assertSame('cart total 15000 exceeds cap 10000', $result['reason']);
    }

    public function test_r035_passes_when_total_within_cap(): void
    {
        $cart = new WC_Cart();
        $cart->total = '99.99'; // 9999 cents
        $result = AgenticMCP_Cart_Signals::evaluateR035MaxOrderValue($cart, ['maxCents' => 10000]);
        $this->assertFalse($result['hit']);
        $this->assertArrayNotHasKey('reason', $result);
    }

    public function test_r035_passes_when_total_equals_cap_boundary(): void
    {
        $cart = new WC_Cart();
        $cart->total = '100.00'; // 10000 cents
        $result = AgenticMCP_Cart_Signals::evaluateR035MaxOrderValue($cart, ['maxCents' => 10000]);
        $this->assertFalse($result['hit']);
    }

    public function test_r035_passes_when_max_cents_missing(): void
    {
        $cart = new WC_Cart();
        $cart->total = '999999.99';
        $result = AgenticMCP_Cart_Signals::evaluateR035MaxOrderValue($cart, []);
        $this->assertFalse($result['hit']);
    }

    // ── R036 max-line-item-value ────────────────────────────────────────────

    public function test_r036_blocks_when_line_exceeds_cap(): void
    {
        $cart = new WC_Cart();
        $cart->items = [
            ['product_id' => 42, 'line_total' => 250.0], // 25000 cents
            ['product_id' => 99, 'line_total' => 10.0],
        ];
        $result = AgenticMCP_Cart_Signals::evaluateR036MaxLineItemValue($cart, ['maxCents' => 20000]);
        $this->assertTrue($result['hit']);
        $this->assertSame('line item 42 value 25000 exceeds cap 20000', $result['reason']);
    }

    public function test_r036_passes_when_all_lines_within_cap(): void
    {
        $cart = new WC_Cart();
        $cart->items = [
            ['product_id' => 1, 'line_total' => 50.0],
            ['product_id' => 2, 'line_total' => 199.99],
        ];
        $result = AgenticMCP_Cart_Signals::evaluateR036MaxLineItemValue($cart, ['maxCents' => 20000]);
        $this->assertFalse($result['hit']);
    }

    public function test_r036_passes_on_empty_cart(): void
    {
        $cart = new WC_Cart();
        $cart->items = [];
        $result = AgenticMCP_Cart_Signals::evaluateR036MaxLineItemValue($cart, ['maxCents' => 100]);
        $this->assertFalse($result['hit']);
    }

    public function test_r036_passes_when_max_cents_missing(): void
    {
        $cart = new WC_Cart();
        $cart->items = [['product_id' => 1, 'line_total' => 9999.0]];
        $result = AgenticMCP_Cart_Signals::evaluateR036MaxLineItemValue($cart, []);
        $this->assertFalse($result['hit']);
    }

    public function test_r036_returns_first_hit(): void
    {
        $cart = new WC_Cart();
        $cart->items = [
            ['product_id' => 11, 'line_total' => 5.0],
            ['product_id' => 22, 'line_total' => 300.0], // first hit at 30000
            ['product_id' => 33, 'line_total' => 500.0],
        ];
        $result = AgenticMCP_Cart_Signals::evaluateR036MaxLineItemValue($cart, ['maxCents' => 10000]);
        $this->assertTrue($result['hit']);
        $this->assertSame('line item 22 value 30000 exceeds cap 10000', $result['reason']);
    }
}
