import { defineConfig, devices } from "@playwright/test";

export default defineConfig({
  testDir: "./e2e",
  testMatch: /demo-video\.spec\.ts/,
  timeout: 90_000,
  retries: 0,
  use: {
    baseURL: "http://localhost:8890",
    headless: false,
    viewport: { width: 1280, height: 800 },
    video: {
      mode: "on",
      size: { width: 1280, height: 800 },
    },
    screenshot: "only-on-failure",
    slowMo: 400,
  },
  outputDir: "./test-results/demo",
  projects: [
    {
      name: "chromium-demo",
      use: { ...devices["Desktop Chrome"] },
    },
  ],
});
