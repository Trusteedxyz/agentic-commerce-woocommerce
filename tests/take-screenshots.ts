import { test, expect } from "@playwright/test";
import path from "path";

const SCREENSHOTS_DIR = path.resolve(__dirname, "../assets/screenshots");

const WP_ADMIN_URL = "http://localhost:8888/wp-admin";
const WP_USER = "admin";
const WP_PASS = "password";

test.describe("Plugin Screenshots", () => {
  test.use({
    viewport: { width: 1280, height: 800 },
  });

  test.beforeEach(async ({ page }) => {
    // Login to wp-admin
    await page.goto(`${WP_ADMIN_URL}/`);

    // Fill login form
    await page.fill("#user_login", WP_USER);
    await page.fill("#user_pass", WP_PASS);
    await page.click("#wp-submit");

    // Wait for dashboard to load
    await page.waitForSelector("#wpbody", { timeout: 15000 });
  });

  test("Screenshot 1: Settings page", async ({ page }) => {
    await page.goto(`${WP_ADMIN_URL}/admin.php?page=agenticmcp-settings`);

    // Wait for page content to fully render
    await page.waitForLoadState("networkidle");
    await page.waitForTimeout(1000);

    await page.screenshot({
      path: path.join(SCREENSHOTS_DIR, "settings-page.png"),
      fullPage: true,
    });
  });

  test("Screenshot 2: Connection status section", async ({ page }) => {
    await page.goto(`${WP_ADMIN_URL}/admin.php?page=agenticmcp-settings`);

    await page.waitForLoadState("networkidle");
    await page.waitForTimeout(1000);

    // Target the Connection Status section by its aria-label
    const connectionSection = page.locator(
      'div.agenticmcp-section[aria-label="Connection Status"]'
    );

    const sectionExists = await connectionSection
      .isVisible()
      .catch(() => false);

    if (sectionExists) {
      await connectionSection.screenshot({
        path: path.join(SCREENSHOTS_DIR, "connection-status.png"),
      });
    } else {
      // Fallback: screenshot the main content wrapper of the plugin page
      const mainContent = page.locator(".agenticmcp-settings").first();
      const mainExists = await mainContent.isVisible().catch(() => false);

      if (mainExists) {
        await mainContent.screenshot({
          path: path.join(SCREENSHOTS_DIR, "connection-status.png"),
        });
      } else {
        // Final fallback: full page screenshot
        await page.screenshot({
          path: path.join(SCREENSHOTS_DIR, "connection-status.png"),
          fullPage: true,
        });
      }
    }
  });

  test("Screenshot 3: WooCommerce products", async ({ page }) => {
    await page.goto(`${WP_ADMIN_URL}/edit.php?post_type=product`);

    // Wait for products table to load
    await page.waitForLoadState("networkidle");
    await page.waitForTimeout(1000);

    await page.screenshot({
      path: path.join(SCREENSHOTS_DIR, "woocommerce-products.png"),
      fullPage: true,
    });
  });
});
