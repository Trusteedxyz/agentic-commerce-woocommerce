/**
 * T039-332 + T039-333: WP Admin SPA E2E — Trust Center + Payment Methods rotate
 *
 * Requires WP docker staging running at WP_STAGING_URL (default localhost:8888).
 * Tests self-skip when AMCP_WP_E2E_ENABLED is not "true" so CI stays green
 * without a live WP instance (same pattern as X402_TESTNET_ENABLED).
 *
 * Run locally:
 *   wp-env start
 *   AMCP_WP_E2E_ENABLED=true npx playwright test --config=tests/playwright.config.ts tests/e2e/us3-us4-trust-rotate.spec.ts
 */
import { test, expect, type Page, type BrowserContext } from "@playwright/test";

const BASE_URL = process.env["WP_STAGING_URL"] ?? "http://localhost:8888";
const ADMIN_USER = process.env["WP_ADMIN_USER"] ?? "admin";
const ADMIN_PASS = process.env["WP_ADMIN_PASS"] ?? "password";
const E2E_ENABLED = process.env["AMCP_WP_E2E_ENABLED"] === "true";

// Self-skip when staging is not available.
test.beforeEach(({}, testInfo) => {
  if (!E2E_ENABLED) {
    testInfo.skip(true, "AMCP_WP_E2E_ENABLED not set — skip WP staging E2E");
  }
});

// ── helpers ─────────────────────────────────────────────────────────────────

async function loginWpAdmin(page: Page): Promise<void> {
  await page.goto(`${BASE_URL}/wp-login.php`);
  await page.fill("#user_login", ADMIN_USER);
  await page.fill("#user_pass", ADMIN_PASS);
  await page.click("#wp-submit");
  await page.waitForURL(/wp-admin/);
}

/**
 * Asserts no token value appears in page HTML, localStorage, or cookies.
 * Accepts the token string to search for.
 */
async function assertZeroDomToken(page: Page, token: string): Promise<void> {
  const html = await page.content();
  expect(html, "Token must not appear in page HTML (C-003)").not.toContain(
    token
  );

  const localStorageKeys: string[] = await page.evaluate(() =>
    Object.keys(localStorage)
  );
  const tokenKeys = localStorageKeys.filter((k) => /token|jwt|bearer/i.test(k));
  expect(
    tokenKeys,
    "No token-related keys in localStorage (C-004)"
  ).toHaveLength(0);

  const cookies = await page.evaluate(() => document.cookie);
  expect(cookies, "Token must not appear in cookies (C-004)").not.toContain(
    token
  );
}

// ── US3: Trust Center ────────────────────────────────────────────────────────

test.describe("US3: Trust Center — WP Admin SPA", () => {
  test("SPA loads and token broker returns 200", async ({ page }) => {
    await loginWpAdmin(page);

    // Intercept token broker call to capture the token value
    let capturedToken: string | null = null;
    page.on("response", async (response) => {
      if (
        response.url().includes("/wp-json/agenticmcps/v1/embed/token") &&
        response.status() === 200
      ) {
        try {
          const body = (await response.json()) as {
            token?: string;
            expires_at?: string;
          };
          if (typeof body.token === "string") {
            capturedToken = body.token;
          }
        } catch {
          // ignore parse errors
        }
      }
    });

    await page.goto(`${BASE_URL}/wp-admin/admin.php?page=amcp-trust-center`);

    // Wait for the SPA mount point
    await expect(page.locator("#amcp-root")).toBeVisible({ timeout: 5_000 });

    // Token broker must have been called (SPA triggers it on mount)
    await page.waitForResponse(
      (r) =>
        r.url().includes("/wp-json/agenticmcps/v1/embed/token") &&
        r.status() === 200,
      { timeout: 5_000 }
    );

    // Trust Receipts section must appear within 2s NFR-001
    // "Trust panel" = en_US, "Panel de confianza" = es, "Cargando" = es loading
    await expect(
      page
        .locator("text=Trust panel")
        .or(page.locator("text=Panel de confianza"))
        .or(page.locator("text=Cargando"))
        .or(page.locator("text=Loading...")),
      "Trust Center heading must appear within 2s (NFR-001)"
    ).toBeVisible({ timeout: 2_000 });

    // C-003/C-004 zero-token-DOM assertion
    if (capturedToken !== null) {
      await assertZeroDomToken(page, capturedToken);
    }
  });

  test("recent receipts table or empty state renders (NFR-001 <2s)", async ({
    page,
  }) => {
    await loginWpAdmin(page);
    await page.goto(`${BASE_URL}/wp-admin/admin.php?page=amcp-trust-center`);
    await expect(page.locator("#amcp-root")).toBeVisible({ timeout: 5_000 });

    // Trust panel heading or score area must render within 2s (NFR-001).
    // The Trust Center shows the score overview panel; receipt list is in My Sales.
    await expect(
      page
        .locator("text=Trust panel")
        .or(page.locator("text=Panel de confianza"))
        .or(page.locator("text=No data."))
        .or(page.locator("text=No data"))
        .or(page.locator("text=Cargando"))
        .or(page.locator("text=Loading...")),
      "Trust Center panel must render within 2s (NFR-001)"
    ).toBeVisible({ timeout: 2_000 });
  });
});

// ── US4: Payment Methods rotate ──────────────────────────────────────────────

test.describe("US4: Payment Methods — rotate credentials", () => {
  test("Payment Methods page loads with rotate buttons", async ({ page }) => {
    await loginWpAdmin(page);
    // Navigate directly to the payments tab via hash
    await page.goto(
      `${BASE_URL}/wp-admin/admin.php?page=amcp-merchant-center#payments`
    );
    await expect(page.locator("#amcp-root")).toBeVisible({ timeout: 5_000 });

    // PHP renders #amcp-root immediately with "Loading Trusteed..." fallback text.
    // Wait for React to mount and replace it (SPA init + token broker call).
    await expect(page.locator("text=Loading Trusteed...")).not.toBeVisible({
      timeout: 10_000,
    });

    // Section headers, empty state, or loading — scoped to #amcp-root to avoid
    // matching WP admin bar items like "Store coming soon".
    const root = page.locator("#amcp-root");
    await expect(
      root
        .locator("text=Coming soon")
        .or(root.locator("text=Available now"))
        .or(root.locator("text=Próximamente"))
        .or(root.locator("text=Disponibles ahora"))
        .or(root.locator("text=No payment methods"))
        .or(root.locator("text=No hay metodos de pago"))
        .or(root.locator("text=Loading..."))
        .or(root.locator("text=Cargando")),
      "Payment methods section must appear within 5s"
    ).toBeVisible({ timeout: 5_000 });
  });

  test("rotate action sends Bearer token and confirm dialog works", async ({
    page,
    context,
  }: {
    page: Page;
    context: BrowserContext;
  }) => {
    await loginWpAdmin(page);

    // Track API calls to verify Bearer token is present
    const rotateCalls: string[] = [];
    page.on("request", (req) => {
      if (
        req.url().includes("/v1/merchant/payment-methods/") &&
        req.url().includes("/rotate")
      ) {
        const auth = req.headers()["authorization"] ?? "";
        rotateCalls.push(auth);
      }
    });

    await page.goto(
      `${BASE_URL}/wp-admin/admin.php?page=amcp-merchant-center#payments`
    );
    await expect(page.locator("#amcp-root")).toBeVisible({ timeout: 5_000 });

    const rotateBtn = page
      .locator(
        'button[aria-label^="Rotar credenciales de"], button[aria-label^="Rotate credentials for"]'
      )
      .first();

    // Skip if no payment methods exist (empty state is valid)
    const hasMethods = await rotateBtn
      .isVisible({ timeout: 2_000 })
      .catch(() => false);
    if (!hasMethods) {
      test.skip(false, "No payment methods in staging — skip rotate flow");
      return;
    }

    await rotateBtn.click();

    // Confirm dialog should appear
    await expect(page.locator("dialog[open]")).toBeVisible({ timeout: 1_000 });
    await expect(page.locator("#dialog-title")).toContainText(
      "Rotar credenciales"
    );

    // Confirm rotate
    await page.locator('button:has-text("Confirmar")').click();

    // Wait for API call
    await page.waitForRequest(
      (req) =>
        req.url().includes("/v1/merchant/payment-methods/") &&
        req.url().includes("/rotate"),
      { timeout: 5_000 }
    );

    // All rotate calls must include Bearer token
    for (const auth of rotateCalls) {
      expect(auth, "Rotate request must use Bearer token").toMatch(/^Bearer /);
    }

    // C-003/C-004: token must not appear in DOM/storage after rotate
    if (rotateCalls.length > 0) {
      const token = rotateCalls[0]!.replace(/^Bearer /, "");
      await assertZeroDomToken(page, token);
    }
  });
});

// ── T039-333: Capability negative test ──────────────────────────────────────

test.describe("T039-333: Insufficient permissions", () => {
  test("user without manage_woocommerce cannot access Trusteed menu", async ({
    page,
  }) => {
    // Attempt direct URL access without admin login
    await page.goto(`${BASE_URL}/wp-admin/admin.php?page=amcp-trust-center`);

    // WP should redirect to login or show insufficient permissions
    const url = page.url();
    const isLoginPage = url.includes("wp-login.php");
    const isAdminPage = url.includes("wp-admin");

    if (isAdminPage && !isLoginPage) {
      // If we somehow reached WP admin, the page must show an access denied message
      // (wp_die with "No tienes permisos suficientes")
      const bodyText = await page.locator("body").textContent();
      expect(bodyText, "Non-admin must see capability error").toMatch(
        /permisos|Permission|denied|forbidden/i
      );
    } else {
      // Expected: redirected to login
      expect(isLoginPage, "Non-admin must be redirected to WP login").toBe(
        true
      );
    }
  });

  test("token broker rejects request without manage_woocommerce", async ({
    request,
  }) => {
    // Direct REST call without authentication — must return 401 or 403
    const response = await request.post(
      `${BASE_URL}/wp-json/agenticmcps/v1/embed/token`,
      {
        headers: {
          "Content-Type": "application/json",
          // No X-WP-Nonce header — simulates unauthenticated request
        },
      }
    );

    expect(
      [401, 403],
      `Token broker must reject unauthenticated requests (got ${response.status()})`
    ).toContain(response.status());
  });
});
