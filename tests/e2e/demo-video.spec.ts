/**
 * Demo video script for WooCommerce Marketplace submission.
 *
 * Flujo grabado (~2 min):
 *   1. WordPress admin login
 *   2. Plugin activo en Plugins screen
 *   3. Panel Trusteed (amcp-inicio) — dashboard del store
 *   4. Panel Settings del plugin
 *   5. Llamada al endpoint MCP /wp-json/agenticmcp/v1/cart
 *   6. Catálogo de productos disponible para agentes
 */

import { test, expect, Page } from "@playwright/test";

const BASE_URL = "http://localhost:8890";
const ADMIN_USER = "admin";
const ADMIN_PASS = "password";

async function loginAdmin(page: Page) {
  await page.goto(`${BASE_URL}/wp-login.php`);
  await page.fill("#user_login", ADMIN_USER);
  await page.fill("#user_pass", ADMIN_PASS);
  await page.click("#wp-submit");
  await page.waitForURL(/wp-admin/, { timeout: 15_000 });
}

test.describe("Trusteed for WooCommerce — Demo", () => {
  test("muestra el plugin instalado, panel de configuración y endpoint MCP en acción", async ({
    page,
  }) => {
    // ──────────────────────────────────────────────────────────────────────────
    // Paso 1: Login WordPress admin
    // ──────────────────────────────────────────────────────────────────────────
    await loginAdmin(page);
    await expect(page).toHaveTitle(/Dashboard/i);
    await page.waitForTimeout(1500);

    // ──────────────────────────────────────────────────────────────────────────
    // Paso 2: Plugins screen — verificar que Trusteed está activo
    // ──────────────────────────────────────────────────────────────────────────
    await page.goto(`${BASE_URL}/wp-admin/plugins.php`);
    await page.waitForLoadState("networkidle");

    const pluginRow = page.locator("tr").filter({ hasText: "Trusteed for WooCommerce" }).first();
    await pluginRow.scrollIntoViewIfNeeded();
    await page.waitForTimeout(2000);

    await expect(pluginRow.locator("a").filter({ hasText: /Deactivate/i })).toBeVisible();

    // ──────────────────────────────────────────────────────────────────────────
    // Paso 3: Panel Trusteed — Home
    // ──────────────────────────────────────────────────────────────────────────
    await page.goto(`${BASE_URL}/wp-admin/admin.php?page=amcp-inicio`);
    await page.waitForLoadState("networkidle");
    await page.waitForTimeout(2500);

    // ──────────────────────────────────────────────────────────────────────────
    // Paso 4: Settings del plugin
    // ──────────────────────────────────────────────────────────────────────────
    await page.goto(`${BASE_URL}/wp-admin/admin.php?page=amcp-settings-embed`);
    await page.waitForLoadState("networkidle");
    await page.waitForTimeout(2000);

    // ──────────────────────────────────────────────────────────────────────────
    // Paso 5: Llamar al endpoint MCP y mostrar el resultado visual
    // POST /wp-json/agenticmcp/v1/cart — como lo invocaría un agente IA
    // ──────────────────────────────────────────────────────────────────────────
    await page.goto(`${BASE_URL}/wp-admin/admin.php?page=amcp-inicio`);
    await page.waitForLoadState("networkidle");

    // Inyectar panel demo visual (solo para la grabación)
    await page.evaluate(() => {
      const banner = document.createElement("div");
      banner.id = "trusteed-demo-panel";
      banner.style.cssText = `
        position: fixed; top: 60px; right: 20px; width: 440px;
        background: #1e1e2e; color: #cdd6f4; border-radius: 12px;
        padding: 20px; z-index: 99999; font-family: monospace; font-size: 13px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.5); border: 1px solid #313244;
      `;
      banner.innerHTML = `
        <div style="color:#89b4fa; font-size:15px; font-weight:bold; margin-bottom:12px;">
          🤖 AI Agent → MCP Endpoint
        </div>
        <div style="color:#a6e3a1; margin-bottom:4px;">POST /wp-json/agenticmcp/v1/cart</div>
        <div style="color:#6c7086; margin-bottom:12px; font-size:11px;">Invocando: create_cart tool con 2 productos...</div>
        <pre id="trusteed-demo-result" style="background:#181825; padding:12px; border-radius:8px; overflow:auto; max-height:220px; color:#cdd6f4; font-size:11px;">⏳ Llamando al endpoint...</pre>
      `;
      document.body.appendChild(banner);
    });

    await page.waitForTimeout(1200);

    // Llamar al endpoint del plugin (sin autenticación — el plugin devuelve 401 o 200)
    const apiResponse = await page.evaluate(async (base) => {
      try {
        const res = await fetch(`${base}/wp-json/agenticmcp/v1/cart`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            line_items: [
              { product_id: 11, quantity: 1 },
              { product_id: 12, quantity: 2 },
            ],
          }),
        });
        const data = await res.json();
        return { status: res.status, data };
      } catch (err: unknown) {
        return { status: 0, data: { error: String(err) } };
      }
    }, BASE_URL);

    // Mostrar resultado en el panel
    await page.evaluate((result) => {
      const el = document.getElementById("trusteed-demo-result");
      if (!el) return;
      const pretty = JSON.stringify(result.data, null, 2);
      el.textContent = `HTTP ${result.status}\n\n${pretty}`;
      if (result.status >= 200 && result.status < 300) {
        el.style.color = "#a6e3a1";
      } else if (result.status === 401) {
        el.style.color = "#fab387";
        // 401 = endpoint existe pero requiere autenticación de agente
        const note = document.createElement("div");
        note.style.cssText = "color:#89b4fa; margin-top:8px; font-size:11px;";
        note.textContent = "→ El endpoint requiere API key de agente (esperado en demo)";
        el.parentElement?.appendChild(note);
      } else {
        el.style.color = "#f38ba8";
      }
    }, apiResponse);

    await page.waitForTimeout(3500);

    // ──────────────────────────────────────────────────────────────────────────
    // Paso 6: Catálogo de productos disponibles para agentes
    // ──────────────────────────────────────────────────────────────────────────
    await page.goto(`${BASE_URL}/wp-admin/edit.php?post_type=product`);
    await page.waitForLoadState("networkidle");
    await page.waitForTimeout(1500);

    const productsCount = await page.locator(".wp-list-table tbody tr").count();

    await page.evaluate((count) => {
      const badge = document.createElement("div");
      badge.style.cssText = `
        position: fixed; bottom: 30px; right: 30px;
        background: #1e66f5; color: white; border-radius: 8px;
        padding: 12px 20px; font-family: sans-serif; font-size: 14px; font-weight: 600;
        z-index: 99999; box-shadow: 0 4px 20px rgba(30,102,245,0.4);
      `;
      badge.textContent = `✅ ${count} productos disponibles para agentes MCP`;
      document.body.appendChild(badge);
    }, productsCount);

    await page.waitForTimeout(2500);

    // ──────────────────────────────────────────────────────────────────────────
    // Paso 7: Mostrar el storefront con los productos
    // ──────────────────────────────────────────────────────────────────────────
    await page.goto(`${BASE_URL}/shop/`);
    await page.waitForLoadState("networkidle");
    await page.waitForTimeout(2500);

    // ──────────────────────────────────────────────────────────────────────────
    // Verificaciones
    // ──────────────────────────────────────────────────────────────────────────
    expect([200, 201, 401, 400]).toContain(apiResponse.status);
    expect(productsCount).toBeGreaterThanOrEqual(1);
  });
});
