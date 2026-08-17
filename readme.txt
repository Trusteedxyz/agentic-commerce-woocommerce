=== Trusteed for WooCommerce ===
Contributors: trusteed
Tags: ai, mcp, agentic-commerce, ai-agents, product-search, chatgpt, claude, ai-shopping
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 2.2.2
WC requires at least: 8.0
WC tested up to: 11.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connect your WooCommerce store to AI agents via MCP. Let Claude, ChatGPT, and any AI agent search your catalog, browse categories, and create carts — checkout always happens in your native WooCommerce checkout.

== Description ==

Trusteed for WooCommerce is a thin connector plugin that bridges your product catalog to the growing ecosystem of AI shopping agents. It uses the **Model Context Protocol (MCP)** — an open standard created by Anthropic — to let AI agents interact with your store programmatically.

Once connected, any MCP-compatible agent (Claude by Anthropic, ChatGPT by OpenAI, or custom-built agents) can search your products, browse your category taxonomy, view detailed product information (name, SKU, price and sale price, description, images, categories, tags, product type, and stock status), and build shopping carts on behalf of users. When a customer is ready to buy, the agent redirects them to your **native WooCommerce checkout page**, where your existing payment gateways (Stripe, PayPal, or any other) handle the transaction securely. The plugin never processes payments or touches sensitive customer data.

Your catalog syncs automatically via WooCommerce hooks whenever you create, update, or delete a product — including stock level changes. You can also trigger a full manual sync from the settings page at any time. The sync transmits only public catalog data: titles, descriptions, prices, images, categories, and stock status. No customer PII, order history, or payment information is ever sent.

The plugin is fully compatible with **WooCommerce High-Performance Order Storage (HPOS)** and **Cart/Checkout Blocks**, ensuring it works seamlessly with modern WooCommerce installations. It also integrates cleanly with popular extensions like WooCommerce Subscriptions, Bookings, and Bundles.

By connecting your store, you open a brand-new sales channel — AI-assisted shopping — without changing your existing tech stack, theme, or checkout flow. Customers discover your products through AI conversations and complete their purchase on your familiar checkout page.

For more information, visit [trusteed.xyz](https://trusteed.xyz).

== Installation ==

1. Upload the `trusteed-for-woocommerce` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce > Trusteed in the admin menu
4. Enter your API key (get one free at [trusteed.xyz/en/developers](https://trusteed.xyz/en/developers))
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

Email support@trusteed.xyz, use the [contact form](https://trusteed.xyz/en/contact), or open an issue on [our GitHub repository](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/issues).

= What happens if I deactivate the plugin? =

Deactivating stops the plugin from running, so no further catalog syncs or agent-enforcement calls are made from your site. It does **not** revoke your API key or disconnect the store on our side — to do that, click **Disconnect store** on the settings page before deactivating.

Disconnecting expires your API key, marks the store inactive so agents can no longer reach it, and deletes the stored WooCommerce webhook secret. It does not erase the catalog data already synced: your store record and previously synced product rows are retained (products are marked unavailable rather than deleted) so that reconnecting restores the store without a full re-sync. To have that data erased, request deletion as described in our [Privacy Policy](https://trusteed.xyz/en/privacy).

Uninstalling the plugin removes every option row it created from your WordPress database, including all stored secrets.

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
  only: product id, name, SKU, price, sale/compare-at price, currency, stock status
  and quantity, permalink, description and short description (tags stripped),
  categories, images (URL + alt text), tags, and product type. Individual
  variations of a variable product are not sent — only the parent product, with its
  own price and stock values. Product reviews and ratings are not sent.
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
Terms of Use: [https://trusteed.xyz/en/terms](https://trusteed.xyz/en/terms)
Privacy Policy: [https://trusteed.xyz/en/privacy](https://trusteed.xyz/en/privacy)

== Screenshots ==

1. Settings page — Configure your API key and monitor connection status
2. Connection status — Green badge when connected, sync statistics visible
3. AI agent searching products — Claude finding products in your catalog via MCP

== Changelog ==

= 2.2.2 =
**Credential-egress hardening, complete uninstall cleanup, and documentation corrected to match the code.**

* Security fix: the API client tolerated a private-range or loopback API base URL (`10.*`, `172.16–31.*`, `192.168.*`, `localhost`, `127.*`) in **every** environment, so an install whose API URL had been redirected would send its `X-AgenticMCP-Key` credential to an internal address over plain HTTP. That dev override is now opt-in, off by default, and enabled only via `TRUSTEED_ALLOW_LOCAL_API_BASE` or a `local` WordPress environment type — matching the `WP_DEBUG` gate the token broker already applied.
* Security fix: cloud instance-metadata and IPv6 internal addresses are now blocked outright, in every environment including the dev override — `169.254.0.0/16` (IMDS), Alibaba `100.100.100.200`, `metadata.google.internal`, IPv6 unique-local `fc00::/7`, and link-local `fe80::/10`. Previously, enabling local development re-opened all of these.
* Fixed: IPv6 API hosts were never matched at all. `parse_url()` returns them bracketed (`[::1]`), so the loopback check silently failed.
* Privacy fix: uninstalling left 21 option rows behind, including three encrypted secrets (`trusteed_embed_wp_secret`, `trusteed_enforcement_hmac_secret`, `trusteed_woo_webhook_secret`) and the legacy `amcp_*` aliases that the option accessor still reads as a fallback — so a reinstall could resurrect a stale secret. `uninstall.php` now clears all three option namespaces (`agenticmcp_*`, `trusteed_*`, `amcp_*`) plus the cached snapshot and JWKS transients, as PRIVACY_POLICY.md promises. A test now scans the source for every writable option key and fails if the uninstall list falls behind.
* Docs fix: the deactivation FAQ claimed deactivating disconnects the store immediately and that "no residual data remains on our servers" — neither was true. Deactivation is inert; disconnecting expires the API key and marks the store inactive but retains the store record and previously synced products. The FAQ now describes what actually happens and points to the deletion-request route.
* Docs fix: the catalog was described as syncing "variants and reviews". Neither is sent — only the parent product of a variable product, and no reviews or ratings. The transmitted field list is now exact.
* Compatibility fix: `Tested up to` / `WC tested up to` disagreed between readme.txt (6.9 / 10.6) and the plugin header (6.7 / 9.5), and neither figure had actually been verified against those versions. Verified by installing WordPress 7.0.4 + WooCommerce 11.0.1 on PHP 8.2 and activating the plugin: clean activation, no fatal errors, `init` runs correctly. `Tested up to` / `WC tested up to` now read 7.0 / 11.0 in both files, matching what was actually run. `.wp-env.json`'s local dev pin updated to the same versions.
* Docs fix: corrected links that returned 404 — `trusteed.xyz/developers`, `/privacy` and `/terms` need the `/en/` language prefix, and `/support` does not exist (replaced with the contact form and GitHub issues).

= 2.2.1 =
**Admin panel fixes: prompt-injection delimiter leak, R047 form, empty state, unminified bundle.**

* Fixed: `browse_categories` fed the same delimiter-wrapped string to both the machine-readable and narrated response channels, so a category name surfaced with `<<<MERCHANT_CONTENT_START>>>` / `<<<MERCHANT_CONTENT_END>>>` markers in the machine channel. Now unwrapped there; the delimiters stay only in the narration, where they signal "this is merchant data" to the agent.
* Fixed: rule R047 (minimum contribution amount) had no form field in the admin panel — its parameters existed in the schema but could only be set via the API.
* Fixed: `MerchantCheckoutConfig` had translated copy for an empty state that was never rendered, so a merchant with no configured payment rails saw an unexplained empty list.
* Fixed: the admin panel bundle shipped unminified (869 KB / 25,064 lines instead of the 490 KB / 41 lines the documented build command produces). Rebuilt from source.

= 2.2.0 =
**Agent token hardening + rule parameter fix.**

* Security fix: the agent token verifier treated `exp` and `iat` as optional — both guards hung off `> 0`, so a token that simply omitted the claim skipped the check entirely. Without `exp` it never expired; without `iat` it had no maximum age. Both are now mandatory, and a non-numeric value is rejected rather than cast.
* Security fix: an `iat` in the future is now rejected (30s clock skew tolerated). Combined with the max-age window it gave a sliding lifetime, so the token effectively never grew old.
* Fixed: rule R036 (max line-item value) read its cap from `maxCents`, copied from R035. The canonical parameter is `maxCentsPerLine` — the only key the merchant panel accepts — so a merchant-configured cap never reached the check.
* Fixed: the cross-language conformance test resolved its fixture through a development-only path and failed in the published repository.
* Trust receipts: admin SPA bundle rebuilt with the receipt ZIP download button, which states plainly that the export is proof of agent integrity, not dispute evidence.

= 2.1.0 =
**Rebrand + admin panel fix.**

* Internal classes, option keys, and REST routes renamed `Amcp_`/`amcp_` to `Trusteed_`/`trusteed_`. Back-compat preserved: existing installs keep working (legacy options are still read as a fallback, legacy REST namespaces stay registered, legacy encrypted values still decrypt).
* Fixed: R043 HITL payload is now plumbed through end-to-end, so a BLOCK can surface a human-in-the-loop freeze instead of a hard block that loses buyer intent.
* Fixed (critical): the compiled admin SPA bundle was missing from the distributed package entirely; the Trusteed admin panel rendered a "bundle not compiled" error on every install.
* Hardening in billing webhooks, checkout enforcer, catalog sync, and cart signals.

= 2.0.2 =
**Checkout enforcement fix (Shopify App Store review remediation, 2026-07-11/12)** — closes an agent-gating gap surfaced by cross-platform verification after an unrelated Shopify App Store suspension.

* Fixed: checkout enforcement was skipped entirely for organic (human, non-agent) checkouts — merchant rules such as maximum order amount, blocked countries, and business-hours restrictions never ran unless an agent token was present. These rules now apply to every checkout regardless of agent presence.
* Added: an offline safety-valve evaluator that enforces the same universal merchant rules locally when the remote rules-evaluation API is unreachable, instead of only falling back to a blanket allow/block policy.

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
