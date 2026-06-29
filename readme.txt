=== Trusteed for WooCommerce ===
Contributors: trusteed
Tags: ai, mcp, agentic-commerce, ai-agents, product-search, chatgpt, claude, ai-shopping
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 2.0.1
WC requires at least: 8.0
WC tested up to: 10.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connect your WooCommerce store to AI agents via MCP. Let Claude, ChatGPT, and any AI agent search your catalog, browse categories, and create carts — checkout always happens in your native WooCommerce checkout.

== Description ==

Trusteed for WooCommerce is a thin connector plugin that bridges your product catalog to the growing ecosystem of AI shopping agents. It uses the **Model Context Protocol (MCP)** — an open standard created by Anthropic — to let AI agents interact with your store programmatically.

Once connected, any MCP-compatible agent (Claude by Anthropic, ChatGPT by OpenAI, or custom-built agents) can search your products, browse your category taxonomy, view detailed product information including variants and reviews, and build shopping carts on behalf of users. When a customer is ready to buy, the agent redirects them to your **native WooCommerce checkout page**, where your existing payment gateways (Stripe, PayPal, or any other) handle the transaction securely. The plugin never processes payments or touches sensitive customer data.

Your catalog syncs automatically via WooCommerce hooks whenever you create, update, or delete a product — including stock level changes. You can also trigger a full manual sync from the settings page at any time. The sync transmits only public catalog data: titles, descriptions, prices, images, categories, and stock status. No customer PII, order history, or payment information is ever sent.

The plugin is fully compatible with **WooCommerce High-Performance Order Storage (HPOS)** and **Cart/Checkout Blocks**, ensuring it works seamlessly with modern WooCommerce installations. It also integrates cleanly with popular extensions like WooCommerce Subscriptions, Bookings, and Bundles.

By connecting your store, you open a brand-new sales channel — AI-assisted shopping — without changing your existing tech stack, theme, or checkout flow. Customers discover your products through AI conversations and complete their purchase on your familiar checkout page.

For more information, visit [trusteed.xyz](https://trusteed.xyz).

== Installation ==

1. Upload the `trusteed-for-woocommerce` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce > Trusteed in the admin menu
4. Enter your API key (get one free at [trusteed.xyz/developers](https://trusteed.xyz/developers))
5. Click "Save & Connect" — your catalog will sync automatically

== Frequently Asked Questions ==

= Does this plugin process payments? =

No. Checkout always happens in your native WooCommerce checkout. AI agents create carts and then redirect users to your checkout page, where your existing payment gateways (Stripe, PayPal, etc.) handle the transaction. This plugin never handles payment data.

= What data is sent to Trusteed? =

Only your product catalog (titles, prices, descriptions, images, categories, stock status). No customer PII, payment data, or order history is transmitted. All communication uses HTTPS encryption.

= Is this compatible with WooCommerce HPOS? =

Yes. Full compatibility with High-Performance Order Storage is declared and tested. The plugin also supports Cart/Checkout Blocks.

= What MCP tools does this enable? =

Once connected, your store exposes these MCP tools to agents: `search_products`, `browse_categories`, `get_product_details`, and `create_cart` (with native WooCommerce checkout redirect).

= Do I need a paid plan? =

No. The free tier gives you full catalog sync and AI agent access for one store. Paid plans (Growth, Pro, Enterprise) add analytics, white-label features, higher rate limits, and multi-store support.

= Which AI agents work with this? =

Any MCP-compatible agent: Claude (Anthropic), ChatGPT (OpenAI with MCP support), custom agents built with LangChain, CrewAI, Vercel AI SDK, and any other framework that supports the Model Context Protocol.

= How often does the catalog sync? =

Products sync automatically when you create, update, or delete them via WooCommerce hooks. Stock level changes also trigger a sync. You can additionally trigger a full catalog sync manually from the settings page.

= Is my store data secure? =

Yes. All communication between your store and Trusteed uses HTTPS. Your API key is stored securely in the WordPress database. No customer data is ever transmitted — only public product catalog information.

= Can I use this with other WooCommerce extensions? =

Yes. The plugin is compatible with WooCommerce Subscriptions, Bookings, Bundles, and other popular extensions. It reads product data through standard WooCommerce APIs and does not interfere with other plugins.

= Where can I get support? =

Visit [trusteed.xyz/support](https://trusteed.xyz/support) or email support@trusteed.xyz. You can also open an issue on our GitHub repository.

= What happens if I deactivate the plugin? =

Your store is immediately disconnected from AI agents. No residual data remains on our servers after disconnection. You can reactivate at any time and your catalog will sync again automatically.

= Does this slow down my store? =

No. The plugin only communicates with Trusteed when catalog changes occur (product create, update, delete). It adds no overhead to your storefront page loads or customer checkout experience.

== External services ==

This plugin is a connector for the **Trusteed** service and requires a Trusteed
account to function. It communicates with the Trusteed API (default
`https://api.trusteed.xyz`, configurable per-install) so that AI shopping agents
can interact with your catalog. Below is every request the plugin makes, the data
it sends, and when it is triggered.

* **Connection test** — `GET /api/v1/health`. Sends no store data; measures
  round-trip latency. Triggered when you click "Test connection" on the settings
  page.
* **Onboarding** — `POST /api/v1/plugin/onboard`. Sends the email and password you
  enter to create or link your Trusteed account. Triggered only when you submit the
  onboarding form. Credentials are exchanged for an API key and are not stored in
  plaintext by the plugin.
* **Store registration** — `POST /api/v1/plugin/register`. Sends store metadata
  (site URL, store name/slug). Triggered when you connect the store.
* **Catalog sync** — `POST /api/v1/plugin/catalog/sync`. Sends public product data
  only (title, description, price, images, categories, SKU, stock status).
  Triggered on product create/update/stock-change/delete and on manual full sync.
  No customer PII, order history, or payment data is ever sent.
* **Rule snapshot** — `GET /v1/rules/snapshot/{merchantId}` and key discovery
  `GET /.well-known/jwks.json`. Fetches your signed enforcement ruleset and the
  public keys used to verify it. Sends your merchant identifier only.
* **Agent enforcement** — `POST /v1/rules/evaluate`, `POST /v1/agent-events` and
  `POST /v1/agent-events/nonce-consume`. Sends agent-action context (agent
  identifier/DID, cart context, single-use nonce) when an AI agent acts on your
  store, so the action can be authorized and audited.
* **Embed token** — `POST /v1/embed/wp/issue-token`. Issues a short-lived token for
  the in-admin Trust Center panel. Triggered when you open that panel.

All requests use HTTPS. The plugin never processes payments and never transmits
customer personal data or order history.

Service provider: Trusteed — [https://trusteed.xyz](https://trusteed.xyz)
Terms of Use: [https://trusteed.xyz/terms](https://trusteed.xyz/terms)
Privacy Policy: [https://trusteed.xyz/privacy](https://trusteed.xyz/privacy)

== Screenshots ==

1. Settings page — Configure your API key and monitor connection status
2. Connection status — Green badge when connected, sync statistics visible
3. AI agent searching products — Claude finding products in your catalog via MCP

== Changelog ==

= 2.0.1 =
**Critical activation + security hotfix (Codex audit, 2026-06-11)** — Closes 9 audit findings + 2 adjacent HIGH issues surfaced in code review.

**Critical (the plugin did not activate in 2.0.0):**
* Fixed a half-finished `AGENTICMCP_*` → `TRUSTEED_*` rename that left `class-plugin.php` referencing an undefined class (`AgenticMCP_Plugin`), undefined constants (`AGENTICMCP_PLUGIN_DIR/URL`, `AGENTICMCP_API_BASE`, `AGENTICMCP_VERSION`) and non-existent classes (`AgenticMCP_Api_Client/Cart_Bridge/Settings`) — a fatal "class/constant not found" on activation. The plugin now boots cleanly.
* Fixed stale `AgenticMCP_Cart_Bridge` references in the checkout enforcer and multi-add handler (the class was renamed to `Trusteed_Cart_Bridge`) — a fatal error fired on the checkout page.
* Fixed the admin SPA loader referencing the undefined `AGENTICMCP_API_BASE` constant — a fatal on PHP 8 on every admin page load.

**Security:**
* `create_cart` now forwards the agent's JWS token (`amcp_agent_token`) through to the checkout URL, so signature/replay verification (R002) actually runs on the normal flow (previously the token was silently dropped).
* The REST API client now validates the API base URL against an exact host allowlist before sending the bearer key — a tampered `amcp_api_base_url` can no longer exfiltrate credentials to an arbitrary HTTPS host.

**Hygiene:**
* `build-zip.sh` now references the real entrypoint (`trusteed-for-woocommerce.php`).
* Removed an orphaned, unmounted admin SPA component with an incompatible auth contract.
* Replaced decorative emoji icons in the admin SPA with plain text for a denser, B2B-consistent UI.

**Server-side companions (backend, no plugin change required):** WooCommerce order/inventory webhooks now capture the raw request body correctly (HMAC no longer 500s on valid signatures); inventory webhooks validate HMAC with a per-store secret (fail-closed) instead of a shared global; onboarding no longer reports success when credential/webhook persistence fails.

= 2.0.0 =
**Security & reliability sprint (Spec-WCGAPS, 2026-05-24)** — Closes 10 audit gaps + 6 HIGH + 11 MEDIUM findings from the upstream Codex + security-reviewer + code-reviewer review.

**Backward-incompatible (requires re-Connect after upgrade):**
* Onboarding now provisions and persists `enforcement_installation_id`, `enforcement_hmac_secret`, and `woo_webhook_secret` returned by the backend. Plugins upgraded from 1.x without re-Connect will show an admin notice prompting the merchant to reconnect.
* `/plugin/disconnect` is now a 2-phase flow with confirmation token + 5min TTL (closes DoS by stolen API key).
* Reconnect requires domain-ownership proof via `/.well-known/amcp-verify.txt` challenge (closes cross-merchant takeover).

**New (additive):**
* `create_cart` MCP tool now POSTs to the real WP cart bridge endpoint instead of returning a generic `/cart` URL — enables agent_id + agent_token forwarding for enforcement.
* New `POST /plugin/catalog/delete` handler — soft-deletes products marked for deletion in WP admin.
* Agent-event webhook now retries on 5xx with exponential backoff (60s / 120s / 240s, max 3) via WP-Cron — no more silent failures.
* Currency and tags are now ingested from real WC values (was hardcoded USD + empty tags).
* Bilingual admin notices (EN + ES) guide merchants through reconnect + missing-HMAC scenarios.
* SSRF hardening — store URL is validated against IPv4 RFC1918 + IPv6 ULA/link-local + cloud IMDS endpoints.

**Fail-closed defaults:**
* The legacy `s=dev-bypass` HMAC placeholder is no longer emitted — plugin now returns `null` and skips dispatch when the enforcement secret is empty, with admin notice surfaced.
* Catalog sync now records `last_sync_attempt` and `last_sync_success` separately — merchants can distinguish a green "tried" from a green "succeeded".

**Hygiene:**
* Default `AGENTICMCP_API_BASE` consolidated to `https://api.trusteed.xyz` across the plugin (removes Railway URL drift).
* Versioned encryption prefix (`amcp_enc:`) — legacy plaintext options decrypt transparently and re-encrypt on next save.
* WP-Cron retry payload validated against 7-step shape (merchantId, installationId, allowed event kinds, body size cap 64 KiB) — prevents event injection from DB-write attackers.

See full sprint detail in repository docs: `docs/CLAUDE/specifications.md` (Spec-WCGAPS section), `specs/048-checkout-enforcement-layer/spec.md` §32.

= 1.0.0 =
* Initial release
* Catalog sync via WooCommerce hooks (create, update, delete, stock changes)
* Cart bridge with native WooCommerce checkout URL generation
* Settings page with connection test and manual sync
* Billing webhooks for SaaS subscription lifecycle
* HPOS and Cart/Checkout Blocks compatibility
* Full i18n support (English + Spanish)

== Upgrade Notice ==

= 2.0.0 =
**SECURITY UPGRADE — re-Connect required.** Closes 17 findings (3 HIGH + 14 MEDIUM/LOW) including SSRF, cross-merchant takeover, disconnect DoS, and silent enforcement bypass. After upgrade, click "Reconnect" once to receive fresh enforcement credentials.

= 1.0.0 =
Initial release — connect your store to AI agents via MCP.
