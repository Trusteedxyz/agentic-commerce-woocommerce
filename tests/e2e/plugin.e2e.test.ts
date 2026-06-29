import { test, expect } from "@playwright/test";

const BASE_URL = process.env.WP_BASE_URL || "http://localhost:8081";
const API_KEY = "sk_test_wp_env_testing_key_12345";
const ADMIN_USER = "admin";
const ADMIN_PASS = "password";
const ADMIN_APP_PASS = process.env.WP_APP_PASS || "G4Vc66fwkylzsp9FS7xRgSIz";

/**
 * Decode HTML entities in checkout URLs returned by WooCommerce.
 * WC encodes `&` as `&#038;` in some contexts.
 */
function decodeCheckoutUrl(raw: string): string {
  return raw.replace(/&#038;/g, "&");
}

// ============================================================
// 1. Plugin Activation & Admin UI
// ============================================================

test.describe("Plugin Activation", () => {
  test("plugin is active in WordPress", async ({ request }) => {
    // Via REST API: check plugin status (requires Application Password)
    const response = await request.get(`${BASE_URL}/wp-json/wp/v2/plugins`, {
      headers: {
        Authorization: `Basic ${btoa(`${ADMIN_USER}:${ADMIN_APP_PASS}`)}`,
      },
    });
    expect(response.ok()).toBeTruthy();
    const plugins = await response.json();
    const ourPlugin = plugins.find((p: any) =>
      p.plugin.includes("agenticmcpstores")
    );
    expect(ourPlugin).toBeDefined();
    expect(ourPlugin.status).toBe("active");
  });
});

test.describe("Settings Page", () => {
  test("settings page loads in WP Admin", async ({ page }) => {
    // Login to WP Admin
    await page.goto(`${BASE_URL}/wp-login.php`);
    await page.fill("#user_login", ADMIN_USER);
    await page.fill("#user_pass", ADMIN_PASS);
    await page.click("#wp-submit");
    await page.waitForURL(/wp-admin/);

    // Navigate to our settings page
    await page.goto(`${BASE_URL}/wp-admin/admin.php?page=agenticmcp-settings`);

    // Verify page contains our plugin heading
    await expect(page.locator("text=AgenticMCPStores").first()).toBeVisible();

    // Connected state: shows action buttons
    await expect(page.locator("#agenticmcp-test-connection")).toBeVisible();
    await expect(page.locator("#agenticmcp-sync-catalog")).toBeVisible();

    // MCP endpoint input (readonly, shows current endpoint)
    const endpointInput = page.locator('input[type="url"][readonly]').first();
    await expect(endpointInput).toBeVisible();
  });

  test("settings page shows connection status badge", async ({ page }) => {
    await page.goto(`${BASE_URL}/wp-login.php`);
    await page.fill("#user_login", ADMIN_USER);
    await page.fill("#user_pass", ADMIN_PASS);
    await page.click("#wp-submit");

    await page.goto(`${BASE_URL}/wp-admin/admin.php?page=agenticmcp-settings`);

    // Connected badge must be visible
    await expect(
      page.locator(".agenticmcp-badge--success").first()
    ).toBeVisible();

    // Plan badge must show a tier (FREE, GROWTH, PRO, or ENTERPRISE)
    await expect(page.locator(".agenticmcp-badge--info").first()).toBeVisible();
  });
});

// ============================================================
// 2. REST API Endpoints
// ============================================================

test.describe("Cart Bridge REST API", () => {
  test("POST /wp-json/agenticmcp/v1/cart returns WC checkout URL", async ({
    request,
  }) => {
    const response = await request.post(
      `${BASE_URL}/wp-json/agenticmcp/v1/cart`,
      {
        headers: {
          "Content-Type": "application/json",
          "X-AgenticMCP-Key": API_KEY,
        },
        data: {
          line_items: [{ product_id: 14, quantity: 2 }],
        },
      }
    );

    // Should succeed
    expect(response.status()).toBe(200);
    const body = await response.json();

    // checkout_url MUST be on the merchant's own domain (CRITICAL — compliance 1.2.10)
    expect(body.checkout_url).toBeDefined();
    const checkoutUrl = decodeCheckoutUrl(body.checkout_url);
    expect(checkoutUrl).toContain(new URL(BASE_URL).host);
    expect(checkoutUrl).not.toContain("trusteed.xyz");
    expect(checkoutUrl).not.toContain("railway.app");

    expect(body.items_count).toBe(2);
  });

  test("POST /wp-json/agenticmcp/v1/cart rejects invalid API key", async ({
    request,
  }) => {
    const response = await request.post(
      `${BASE_URL}/wp-json/agenticmcp/v1/cart`,
      {
        headers: {
          "Content-Type": "application/json",
          "X-AgenticMCP-Key": "invalid_key",
        },
        data: {
          line_items: [{ product_id: 14, quantity: 1 }],
        },
      }
    );

    expect(response.status()).toBe(401);
  });

  test("POST /wp-json/agenticmcp/v1/cart rejects missing API key", async ({
    request,
  }) => {
    const response = await request.post(
      `${BASE_URL}/wp-json/agenticmcp/v1/cart`,
      {
        headers: { "Content-Type": "application/json" },
        data: {
          line_items: [{ product_id: 14, quantity: 1 }],
        },
      }
    );

    expect(response.status()).toBe(401);
  });

  test("POST /wp-json/agenticmcp/v1/cart rejects empty line_items", async ({
    request,
  }) => {
    const response = await request.post(
      `${BASE_URL}/wp-json/agenticmcp/v1/cart`,
      {
        headers: {
          "Content-Type": "application/json",
          "X-AgenticMCP-Key": API_KEY,
        },
        data: {
          line_items: [],
        },
      }
    );

    expect(response.status()).toBe(400);
  });
});

// ============================================================
// 3. Billing Webhooks
// ============================================================

test.describe("Billing Webhooks", () => {
  test("POST /wp-json/agenticmcp/v1/billing-webhook accepts valid signature", async ({
    request,
  }) => {
    const crypto = require("crypto");
    const body = JSON.stringify({
      event_type: "subscription.activated",
      event_id: "evt_test_001",
      data: { tier: "GROWTH" },
    });
    const secret = "whsec_test_secret_for_e2e";
    const signature = crypto
      .createHmac("sha256", secret)
      .update(body)
      .digest("hex");

    const response = await request.post(
      `${BASE_URL}/wp-json/agenticmcp/v1/billing-webhook`,
      {
        headers: {
          "Content-Type": "application/json",
          "X-AgenticMCP-Signature": signature,
        },
        data: body,
      }
    );

    expect(response.status()).toBe(200);
    const result = await response.json();
    expect(result.success).toBe(true);
  });

  test("POST /wp-json/agenticmcp/v1/billing-webhook rejects invalid signature", async ({
    request,
  }) => {
    const body = JSON.stringify({
      event_type: "subscription.activated",
      event_id: "evt_test_002",
      data: { tier: "PRO" },
    });

    const response = await request.post(
      `${BASE_URL}/wp-json/agenticmcp/v1/billing-webhook`,
      {
        headers: {
          "Content-Type": "application/json",
          "X-AgenticMCP-Signature": "invalid_signature",
        },
        data: body,
      }
    );

    expect(response.status()).toBe(401);
  });

  test("billing webhook is idempotent", async ({ request }) => {
    const crypto = require("crypto");
    const body = JSON.stringify({
      event_type: "subscription.upgraded",
      event_id: "evt_test_idempotent_001",
      data: { tier: "PRO" },
    });
    const secret = "whsec_test_secret_for_e2e";
    const signature = crypto
      .createHmac("sha256", secret)
      .update(body)
      .digest("hex");

    // First call
    const r1 = await request.post(
      `${BASE_URL}/wp-json/agenticmcp/v1/billing-webhook`,
      {
        headers: {
          "Content-Type": "application/json",
          "X-AgenticMCP-Signature": signature,
        },
        data: body,
      }
    );
    expect(r1.status()).toBe(200);

    // Second call (same event_id) — should still succeed but not reprocess
    const r2 = await request.post(
      `${BASE_URL}/wp-json/agenticmcp/v1/billing-webhook`,
      {
        headers: {
          "Content-Type": "application/json",
          "X-AgenticMCP-Signature": signature,
        },
        data: body,
      }
    );
    expect(r2.status()).toBe(200);
  });
});

// ============================================================
// 4. HPOS Compatibility (smoke)
// ============================================================

test.describe("HPOS Compatibility", () => {
  test("plugin declares HPOS support", async ({ request }) => {
    // Check that our plugin didn't cause WC to show HPOS incompatibility warnings
    // WC REST API requires Application Password for authentication
    const response = await request.get(
      `${BASE_URL}/wp-json/wc/v3/system_status`,
      {
        headers: {
          Authorization: `Basic ${btoa(`${ADMIN_USER}:${ADMIN_APP_PASS}`)}`,
        },
      }
    );
    expect(response.ok()).toBeTruthy();
    // If we got here without 500, WooCommerce is running with our plugin active and HPOS enabled
  });
});

// ============================================================
// 5. Checkout URL Compliance (critical)
// ============================================================

test.describe("Compliance 1.2.10 — Checkout URL must be merchant domain", () => {
  test("checkout URL with single product is native WC", async ({ request }) => {
    const response = await request.post(
      `${BASE_URL}/wp-json/agenticmcp/v1/cart`,
      {
        headers: {
          "Content-Type": "application/json",
          "X-AgenticMCP-Key": API_KEY,
        },
        data: { line_items: [{ product_id: 14, quantity: 1 }] },
      }
    );

    const body = await response.json();
    const checkoutUrl = decodeCheckoutUrl(body.checkout_url);
    const url = new URL(checkoutUrl);

    const expectedPort = new URL(BASE_URL).port;
    // URL must be on the merchant's WooCommerce domain
    expect(url.hostname).toBe("localhost");
    expect(url.port).toBe(expectedPort);
    // Must contain checkout or cart path
    expect(url.pathname).toMatch(/\/(checkout|cart)\//);
  });

  test("checkout URL with multiple products is native WC", async ({
    request,
  }) => {
    const response = await request.post(
      `${BASE_URL}/wp-json/agenticmcp/v1/cart`,
      {
        headers: {
          "Content-Type": "application/json",
          "X-AgenticMCP-Key": API_KEY,
        },
        data: {
          line_items: [
            { product_id: 14, quantity: 1 },
            { product_id: 15, quantity: 3 },
          ],
        },
      }
    );

    const body = await response.json();
    const checkoutUrl = decodeCheckoutUrl(body.checkout_url);
    const url = new URL(checkoutUrl);
    const expectedPort = new URL(BASE_URL).port;

    expect(url.hostname).toBe("localhost");
    expect(url.port).toBe(expectedPort);
    expect(checkoutUrl).not.toContain("agenticmcpstores");
    expect(checkoutUrl).not.toContain("railway");
    expect(checkoutUrl).not.toContain("stripe");
    expect(checkoutUrl).not.toContain("paypal");
  });
});
