<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * B6 — Pure-function cart signal detection for WooCommerce CEL.
 *
 * Extracted from class-checkout-enforcer.php so the audit-flagged gaps for
 * R015 (price delta), R016 (stock), R025 (PO box / freight forwarder),
 * R026 (subscription), R027 (stored value) and R028 (B2B) can be unit-tested
 * without bootstrapping WooCommerce.
 *
 * The enforcer calls these helpers after extracting primitive arrays from
 * WC_Product / WC_Order / WC_Cart objects.
 */
final class AgenticMCP_Cart_Signals
{
    public const PRICE_SNAP_VERSION = 'tps.v1';

    /** @var string[] Multi-language PO-box prefixes. `u` flag = UTF-8 aware (handles accents). */
    private const PO_BOX_PATTERNS = [
        '/\b(p\.?\s*o\.?\s*box|post\s+office\s+box|postbox|po\s*-?\s*box)\b/iu',
        '/\bapartado(\s+postal|\s+de\s+correos)?\b/iu',
        '/\bapdo\.?\s*(postal)?\b/iu',
        '/\bbo[iî]te\s+postale\b/iu',
        '/\bpostfach\b/iu',
        '/\bcasella\s+postale\b/iu',
        '/\bcaixa\s+postal\b/iu',
    ];

    /** @var string[] Known consumer freight forwarders (substring match on address / company). */
    private const FREIGHT_FORWARDER_BRANDS = [
        'shipito', 'myus', 'stackry', 'planet express', 'reship',
        'borderlinx', 'usgobuy', 'shipforward', 'comgateway',
    ];

    /** @var string[] Common B2B role slugs added by WooCommerce wholesale plugins. */
    private const B2B_ROLE_PATTERNS = [
        '/^b2bking_/i',
        '/wholesale/i',
        '/^b2b_/i',
    ];

    /** @var string[] Gift card / stored-value product types and tag/category slugs. */
    private const GIFT_CARD_TYPES = ['pw-gift-card', 'gift-card', 'gift_card', 'yith-gift-card'];
    private const GIFT_CARD_SLUGS = ['gift-card', 'gift-cards', 'giftcard', 'gift_card'];

    // ─── R025 sensitive delivery address ────────────────────────────────────

    public static function detect_po_box(string $address1, string $address2): bool
    {
        $haystack = trim($address1 . ' ' . $address2);
        if ($haystack === '') {
            return false;
        }
        foreach (self::PO_BOX_PATTERNS as $regex) {
            if (preg_match($regex, $haystack) === 1) {
                return true;
            }
        }
        return false;
    }

    public static function detect_freight_forwarder(string $address1, string $address2, string $company): bool
    {
        $haystack = strtolower(trim($address1 . ' ' . $address2 . ' ' . $company));
        if ($haystack === '') {
            return false;
        }
        foreach (self::FREIGHT_FORWARDER_BRANDS as $brand) {
            if (strpos($haystack, $brand) !== false) {
                return true;
            }
        }
        return false;
    }

    // ─── R016 stock confidence ───────────────────────────────────────────────

    /**
     * @param array<int, array{stock: int|null}> $items
     * @return int|null minimum managed stock; null when no item has managed stock
     */
    public static function lowest_stock(array $items): ?int
    {
        $min = null;
        foreach ($items as $item) {
            $stock = $item['stock'] ?? null;
            if (!is_int($stock)) {
                continue;
            }
            if ($min === null || $stock < $min) {
                $min = $stock;
            }
        }
        return $min;
    }

    // ─── R026 subscription detection ─────────────────────────────────────────

    /**
     * @param array<int, array{type?: string, is_subscription?: bool}> $items
     */
    public static function cart_has_subscription(array $items): bool
    {
        foreach ($items as $item) {
            $type = strtolower((string) ($item['type'] ?? ''));
            if ($type === 'subscription' || $type === 'variable-subscription' || $type === 'subscription_variation') {
                return true;
            }
            if (!empty($item['is_subscription'])) {
                return true;
            }
        }
        return false;
    }

    // ─── R027 gift card / stored value ───────────────────────────────────────

    /**
     * @param array<int, array{type?: string, price_cents?: int, qty?: int, tags?: string[], categories?: string[]}> $items
     */
    public static function stored_value_cents(array $items): int
    {
        $total = 0;
        foreach ($items as $item) {
            if (!self::is_stored_value_item($item)) {
                continue;
            }
            $price = (int) ($item['price_cents'] ?? 0);
            $qty   = max(1, (int) ($item['qty'] ?? 1));
            $total += $price * $qty;
        }
        return $total;
    }

    /**
     * @param array<string,mixed> $item
     */
    private static function is_stored_value_item(array $item): bool
    {
        $type = strtolower((string) ($item['type'] ?? ''));
        if (in_array($type, self::GIFT_CARD_TYPES, true)) {
            return true;
        }
        foreach (['tags', 'categories'] as $field) {
            $values = $item[$field] ?? [];
            if (!is_array($values)) {
                continue;
            }
            foreach ($values as $slug) {
                if (in_array(strtolower((string) $slug), self::GIFT_CARD_SLUGS, true)) {
                    return true;
                }
            }
        }
        return false;
    }

    // ─── R028 B2B detection ──────────────────────────────────────────────────

    /**
     * @param string             $company   billing / shipping company field
     * @param int|null           $user_id   buyer user ID (unused in pure layer; kept for symmetry)
     * @param array<int,string>  $roles     WP user roles
     */
    public static function is_b2b_order(string $company, ?int $user_id, array $roles): bool
    {
        unset($user_id);
        if (trim($company) !== '') {
            return true;
        }
        foreach ($roles as $role) {
            foreach (self::B2B_ROLE_PATTERNS as $pattern) {
                if (preg_match($pattern, (string) $role) === 1) {
                    return true;
                }
            }
        }
        return false;
    }

    // ─── R015 price snapshot (parity with PS PriceSnapVerifier) ──────────────

    /**
     * @param array<string,int> $current  pid => price_cents at checkout time
     * @param array<string,int> $snapshot pid => price_cents at cart-add time (HMAC-verified)
     */
    public static function max_price_delta_bps(array $current, array $snapshot): int
    {
        $max = 0;
        foreach ($current as $pid => $cents) {
            $pidStr = (string) $pid;
            if (!isset($snapshot[$pidStr])) {
                continue;
            }
            $orig = (int) $snapshot[$pidStr];
            if ($orig <= 0) {
                continue;
            }
            $delta = (int) round(abs(((int) $cents) - $orig) / $orig * 10000);
            if ($delta > $max) {
                $max = $delta;
            }
        }
        return $max;
    }

    /**
     * Encode + HMAC-sign a price snapshot for storage in WC session.
     * Same envelope as PS / Shopify so a single verifier serves all platforms.
     *
     * @param array<string,int> $prices pid => price_cents
     */
    public static function build_price_snap_cookie(array $prices, string $hmac_key_hex): string
    {
        ksort($prices, SORT_STRING);
        $msg = self::canonical_price_message($prices);
        $key = (string) @hex2bin($hmac_key_hex);
        $h   = hash_hmac('sha256', $msg, $key === '' ? $hmac_key_hex : $key);
        return base64_encode((string) json_encode(['v' => self::PRICE_SNAP_VERSION, 'p' => $prices, 'h' => $h]));
    }

    /**
     * Verify + decode a price-snapshot envelope.
     * Returns the price map on success, [] otherwise (R015 degrades to PASS).
     *
     * @return array<string,int>
     */
    public static function verify_price_snap(string $cookie_raw, string $hmac_key_hex): array
    {
        if ($cookie_raw === '' || $hmac_key_hex === '') {
            return [];
        }
        $decoded = base64_decode($cookie_raw, true);
        if (!is_string($decoded) || $decoded === '') {
            return [];
        }
        $payload = json_decode($decoded, true);
        if (!is_array($payload)) {
            return [];
        }
        if (($payload['v'] ?? null) !== self::PRICE_SNAP_VERSION) {
            return [];
        }
        $prices = $payload['p'] ?? null;
        $hmac   = $payload['h'] ?? null;
        if (!is_array($prices) || !is_string($hmac) || $hmac === '') {
            return [];
        }
        $normalized = [];
        foreach ($prices as $pid => $cents) {
            $pidStr = (string) $pid;
            if ($pidStr === '' || (!is_int($cents) && !(is_string($cents) && ctype_digit($cents)))) {
                return [];
            }
            $value = (int) $cents;
            if ($value < 0) {
                return [];
            }
            $normalized[$pidStr] = $value;
        }
        $key = @hex2bin($hmac_key_hex);
        if ($key === false || $key === '') {
            return [];
        }
        $expected = hash_hmac('sha256', self::canonical_price_message($normalized), $key);
        if (!hash_equals($expected, $hmac)) {
            return [];
        }
        return $normalized;
    }

    /**
     * @param array<string,int> $prices
     */
    public static function canonical_price_message(array $prices): string
    {
        ksort($prices, SORT_STRING);
        $parts = [];
        foreach ($prices as $pid => $cents) {
            $parts[] = $pid . '=' . $cents;
        }
        return implode(',', $parts);
    }

    // ═════════════════════════════════════════════════════════════════════
    // Spec-048 Sprint E (T-E37, ADR-054) — Agentic Starter Kit signals.
    //   R032 (blocked-category) — server-known product category match.
    //   R034 (blocked-sku-list) — variant ID Set lookup.
    //   R038 (max-items-per-order) — Σ quantities.
    //   R039 (max-quantity-per-sku) — per-line quantity max.
    //   R048 (no-digital-goods-for-agents) — virtual product detection.
    // R031/R042/R043 handled outside this class (kill-switch + history + HITL).
    //
    // Rule code references for matrix conformance:
    //   R032 R034 R038 R039 R048
    // ═════════════════════════════════════════════════════════════════════

    /**
     * R032 signal: returns true iff any cart item has a category id in $blocked.
     *
     * @param array<int,array<string,mixed>> $items each item: ['categoryIds' => string[]]
     * @param string[] $blocked
     */
    public static function r032_has_blocked_category(array $items, array $blocked): bool
    {
        if (empty($blocked)) {
            return false;
        }
        $blocked_set = array_fill_keys($blocked, true);
        foreach ($items as $item) {
            $cats = $item['categoryIds'] ?? [];
            if (!is_array($cats)) {
                continue;
            }
            foreach ($cats as $cat) {
                if (is_string($cat) && isset($blocked_set[$cat])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * R034 signal: returns true iff any cart item id (variant/SKU) is in $blocked.
     *
     * @param array<int,array<string,mixed>> $items each item: ['id' => string]
     * @param string[] $blocked
     */
    public static function r034_has_blocked_sku(array $items, array $blocked): bool
    {
        if (empty($blocked)) {
            return false;
        }
        $blocked_set = array_fill_keys($blocked, true);
        foreach ($items as $item) {
            $id = $item['id'] ?? '';
            if (is_string($id) && $id !== '' && isset($blocked_set[$id])) {
                return true;
            }
        }
        return false;
    }

    /**
     * R038 signal: returns total item count Σ qty.
     *
     * @param array<int,array<string,mixed>> $items each item: ['quantity' => int]
     */
    public static function r038_item_count(array $items): int
    {
        $count = 0;
        foreach ($items as $item) {
            $qty = (int) ($item['quantity'] ?? 0);
            if ($qty > 0) {
                $count += $qty;
            }
        }
        return $count;
    }

    /**
     * R039 signal: returns max per-line quantity (0 if cart empty).
     *
     * @param array<int,array<string,mixed>> $items
     */
    public static function r039_max_line_quantity(array $items): int
    {
        $max = 0;
        foreach ($items as $item) {
            $qty = (int) ($item['quantity'] ?? 0);
            if ($qty > $max) {
                $max = $qty;
            }
        }
        return $max;
    }

    /**
     * R048 signal: returns comma-separated digital good types present in cart.
     *
     * Source mapping for Woo:
     *   - WC_Product::is_virtual() === true  → "downloadable"
     *   - product type === "subscription"     → not digital (handled by R026)
     *   - gift-card types (PW/YITH/etc)       → "gift_card"
     *
     * @param array<int,array<string,mixed>> $items each item: ['type' => string, 'is_virtual' => bool, 'tags' => string[]]
     */
    /**
     * R035 (max-order-value): evaluates merchant cap on cart total.
     *
     * Reads $cart->get_total('edit') (string in store currency) and converts
     * to integer cents (×100 truncated). HIT iff $total_cents > $maxCents.
     * Missing/null $maxCents → PASS NO_PARAMS.
     *
     * Mirrors `evaluateR035` in packages/shared/src/enforcement/rule-catalog.ts.
     *
     * @param WC_Cart $cart
     * @param array{maxCents?:int|null} $params
     * @return array{hit:bool,reason?:string}
     */
    public static function evaluateR035MaxOrderValue(WC_Cart $cart, array $params): array
    {
        $cap = $params['maxCents'] ?? null;
        if ($cap === null) {
            return ['hit' => false];
        }
        $cap = (int) $cap;
        $total_raw = (string) $cart->get_total('edit');
        $total_cents = (int) ((float) $total_raw * 100);
        if ($total_cents > $cap) {
            return [
                'hit' => true,
                'reason' => "cart total {$total_cents} exceeds cap {$cap}",
            ];
        }
        return ['hit' => false];
    }

    /**
     * R036 (max-line-item-value): evaluates merchant cap on per-line subtotal.
     *
     * Iterates $cart->get_cart() items; each item['line_total'] is already
     * qty × unit price in store currency. Converts to integer cents (×100).
     * HIT first line where line_total_cents > $maxCents. Missing/null cap → PASS.
     *
     * Note: catalog source-of-truth uses `maxCentsPerLine`; this WC helper
     * accepts `maxCents` per task spec (Sprint E.3 gap closure).
     *
     * @param WC_Cart $cart
     * @param array{maxCents?:int|null} $params
     * @return array{hit:bool,reason?:string}
     */
    public static function evaluateR036MaxLineItemValue(WC_Cart $cart, array $params): array
    {
        $cap = $params['maxCents'] ?? null;
        if ($cap === null) {
            return ['hit' => false];
        }
        $cap = (int) $cap;
        $items = $cart->get_cart();
        if (!is_array($items)) {
            return ['hit' => false];
        }
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $product_id = (int) ($item['product_id'] ?? 0);
            $line_total = (float) ($item['line_total'] ?? 0);
            $line_cents = (int) ($line_total * 100);
            if ($line_cents > $cap) {
                return [
                    'hit' => true,
                    'reason' => "line item {$product_id} value {$line_cents} exceeds cap {$cap}",
                ];
            }
        }
        return ['hit' => false];
    }

    public static function r048_digital_good_types(array $items): string
    {
        $types = [];
        foreach ($items as $item) {
            $type = strtolower((string) ($item['type'] ?? ''));
            if (in_array($type, self::GIFT_CARD_TYPES, true)) {
                $types['gift_card'] = true;
                continue;
            }
            $tags = $item['tags'] ?? [];
            if (is_array($tags)) {
                foreach ($tags as $t) {
                    if (is_string($t) && in_array(strtolower($t), self::GIFT_CARD_SLUGS, true)) {
                        $types['gift_card'] = true;
                    }
                }
            }
            if (!empty($item['is_virtual'])) {
                // Virtual + non-gift-card → downloadable category.
                if (!isset($types['gift_card'])) {
                    $types['downloadable'] = true;
                }
            }
        }
        return implode(',', array_keys($types));
    }
}
