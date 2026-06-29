import { defineConfig } from "@playwright/test";

export default defineConfig({
  testDir: "./e2e",
  timeout: 30000,
  retries: 1,
  use: {
    baseURL: process.env.WP_BASE_URL || "http://localhost:8081",
    extraHTTPHeaders: {
      Accept: "application/json",
    },
  },
  projects: [
    {
      name: "api",
      testMatch: /.*\.(e2e\.test|spec)\.ts/,
    },
  ],
});
