**English** | [Español](README.es.md) | [Français](README.fr.md) | [Deutsch](README.de.md)

# Trusteed Agentic Commerce for WooCommerce

Enable new online shoppers, AI agents, to make purchases in your store securely and reliably thanks to Trusteed: the network that fosters trust between businesses and agents.

- **Set your business rules**: who you allow to buy, up to what amount, which categories you don't want to offer to agents, set price limits, maintain stock levels to protect yourself against potential fraudulent agents, and more.
- **Tamper-evident receipts**: we generate electronically signed, cryptographically tamper-evident receipts that record what the agent actually did — a verifiable proof of agent integrity. They are designed to follow the EU electronic-signature standard (eIDAS) and eSIGN (USA), but they are not yet a *qualified* signature or timestamp, so on their own they are not ready-made dispute evidence for a bank or court.
- **Agent analytics**: view statistics on agent purchases — how much they spend, what products they buy, and how often.
- **Agent blocking**: block potentially dangerous or problematic agents.
- **Digital currencies**: enables purchases in digital currencies thanks to the X402 protocol.
- **Peer-to-peer transactions**: enables direct peer-to-peer commerce between agents and merchants.

## Screenshots

Each panel below maps to an item in the **Trusteed** menu inside WooCommerce.

| Home | Trust Center | My Sales |
|------|--------------|----------|
| ![Home](assets/screenshots/home.png) | ![Trust Center](assets/screenshots/trust-center.png) | ![My Sales](assets/screenshots/my-sales.png) |

| My Rules | Agents | Merchant Center |
|----------|--------|-----------------|
| ![My Rules](assets/screenshots/my-rules.png) | ![Agents](assets/screenshots/agents.png) | ![Merchant Center](assets/screenshots/merchant-center.png) |

| Trust Receipts (My Sales → AI Sales) |
|--------------------------------------|
| ![Trust Receipts](assets/screenshots/ai-receipts.png) |

Every agent transaction produces a cryptographically signed **trust receipt** — a tamper-proof record (compatible with eIDAS / eSIGN) listed under **My Sales → AI Sales**. Click any row to see the full detail (agent ID, tool called, input/output hashes, JWS) and download the receipt as a ZIP file. The export is a verifiable proof of agent integrity: useful backup if a buyer claims they never placed the order, but on its own it does not replace the evidence a bank or court may require in a dispute.

## Features

Trusteed for WooCommerce is a **thin connector** that bridges your product catalog to the growing ecosystem of AI shopping agents using the **Model Context Protocol (MCP)** — an open standard created by Anthropic. The plugin never processes payments or touches sensitive customer data: checkout always happens in your **native WooCommerce checkout**.

- **MCP tools for agents** — `search_products`, `browse_categories`, `get_product_details`, and `create_cart` (with native WooCommerce checkout redirect)
- **Automatic catalog sync** — products sync via WooCommerce hooks on create/update/delete, including stock changes; full manual sync available from the settings page. Only public catalog data is sent (titles, descriptions, prices, images, categories, stock) — never customer PII, orders, or payment info
- **Agent token verification** — `create_cart` forwards the agent's JWS token through to checkout so signature/replay verification (R002) runs on the normal flow
- **Enforcement gate (HITL)** — configurable human-in-the-loop approval for high-value agent orders
- **SSRF hardening** — the credentialed API base must be HTTPS on an exact host allowlist; cloud IMDS (`169.254.0.0/16`, `100.100.100.200`, `metadata.google.internal`), IPv6 unique-local (`fc00::/7`) and link-local (`fe80::/10`) are blocked in every environment, and loopback / RFC1918 only work behind the explicit `TRUSTEED_ALLOW_LOCAL_API_BASE` dev opt-in (off by default)
- **Fail-closed defaults** — no dispatch when the enforcement secret is empty; domain-ownership proof required on reconnect (cross-merchant takeover protection)

## Compatibility

| Component | Supported |
|-----------|-----------|
| WordPress | 6.0 – 6.9 |
| WooCommerce | 8.0 – 10.6 |
| PHP | 7.4+ (tested on 8.0–8.3) |

## Requirements

- WordPress 6.0+ with WooCommerce 8.0+
- PHP 7.4 or newer
- A Trusteed account — [sign up free at trusteed.xyz](https://trusteed.xyz)

## Installation

### Manual upload (recommended)

1. **Download the installable `.zip`** from the latest GitHub Release:
   [**⬇ Latest release — trusteed-agentic-commerce-woocommerce-&lt;version&gt;.zip**](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases/latest)
   — or browse all versions at the [Releases page](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases).
2. In your WordPress admin: **Plugins → Add New → Upload Plugin**.
3. Select the downloaded `.zip` file and click **Install Now**.
4. Click **Activate**.

### From source (build the zip yourself)

```bash
git clone https://github.com/Trusteedxyz/agentic-commerce-woocommerce.git
cd agentic-commerce-woocommerce
bash build-zip.sh        # outputs dist/trusteed-agentic-commerce-woocommerce-<version>.zip
```

## Configuration

1. Log in to your WordPress **Admin**.
2. Go to **WooCommerce → Trusteed** (or the **Trusteed** menu item).
3. Enter your **API Key** from [trusteed.xyz/dashboard/settings](https://trusteed.xyz/dashboard/settings).
4. Click **Save & Connect** — the plugin tests connectivity, registers your store, and syncs your catalog automatically.

Once connected, any MCP-compatible agent (Claude, ChatGPT, or custom agents built with LangChain, CrewAI, Vercel AI SDK, etc.) can search your products, browse categories, view product details, and build carts. When the customer is ready to buy, the agent redirects them to your native WooCommerce checkout, where your existing gateways (Stripe, PayPal, …) handle payment.

A detailed merchant walkthrough lives in [`docs/MERCHANT_INSTALLATION_GUIDE.md`](docs/MERCHANT_INSTALLATION_GUIDE.md).

## FAQ

**What data is sent?** Only your public product catalog (titles, prices, descriptions, images, categories, stock status). No customer PII, payment data, or order history. All communication uses HTTPS.

**Which agents are supported?** Any MCP-compatible agent: Claude (Anthropic), ChatGPT (OpenAI), and custom agents built with LangChain, CrewAI, Vercel AI SDK, or any framework that supports the Model Context Protocol.

**Does it slow down my store?** No. The plugin only talks to Trusteed when catalog changes occur — it adds no overhead to storefront page loads or customer checkout.

## Changelog

### 2.2.2

- **Security fix** — the API client accepted a loopback or RFC1918 API base URL (`10.*`, `172.16–31.*`, `192.168.*`, `localhost`, `127.*`) in **every** environment, with plain HTTP tolerated. An install whose API URL had been redirected would send its `X-AgenticMCP-Key` credential to an internal address. The dev override is now opt-in and off by default, enabled only via `TRUSTEED_ALLOW_LOCAL_API_BASE` or a `local` WordPress environment type — the same gate `Trusteed_Token_Broker` already applied via `WP_DEBUG`, which this client had dropped.
- **Security fix** — cloud instance metadata and IPv6 internal ranges are now denied outright in every environment, *including* under the dev override, which previously re-opened all of them: `169.254.0.0/16` (IMDS), Alibaba `100.100.100.200`, `metadata.google.internal`, unique-local `fc00::/7`, link-local `fe80::/10`. They also get their own error code instead of the misleading "configure an HTTPS URL" message.
- **Fixed** — IPv6 API hosts never matched any check: `parse_url()` returns them bracketed (`[::1]`), so the `::1` loopback entry was dead code.
- **Privacy fix** — uninstalling left 21 option rows behind, including three encrypted secrets (`trusteed_embed_wp_secret`, `trusteed_enforcement_hmac_secret`, `trusteed_woo_webhook_secret`) and the legacy `amcp_*` aliases the option accessor still reads as a fallback, so a reinstall could resurrect a stale secret. `uninstall.php` now clears all three namespaces plus the cached snapshot and JWKS transients. A new test scans the source for every writable option key and fails if the uninstall list falls behind.
- **Docs fix** — trust receipts were described as "proof of the actual transaction in case of any dispute". The product itself says the opposite: a verifiable integrity proof, not ready-made dispute evidence for a bank or court. Corrected here to match.
- **Docs fix** — the deactivation FAQ claimed deactivating disconnects the store and that no residual data remains on our servers. Deactivation is inert, and disconnecting retains the store record and synced products. Corrected, with the deletion-request route documented.
- **Docs fix** — the catalog was described as syncing "variants and reviews"; neither is sent. The transmitted field list is now exact.
- **Docs fix** — `Tested up to` / `WC tested up to` disagreed between `readme.txt` (6.9 / 10.6) and the plugin header (6.7 / 9.5). Both now read 6.9 / 10.6. Automated tests run against WordPress 6.8 with latest-stable WooCommerce on PHP 8.1–8.2.
- **Docs fix** — repaired 404 links: `/developers`, `/privacy` and `/terms` need the `/en/` prefix, and `/support` does not exist (replaced with the contact form and GitHub issues).

### 2.2.1

- **Fixed** — `browse_categories` fed the same wrapped string to both the machine-readable and narrated channels. `guardMerchantField` wraps merchant text in `<<<MERCHANT_CONTENT_START>>> … <<<MERCHANT_CONTENT_END>>>` by default so an agent can tell "this is merchant data, not an instruction" — but the tool reused that already-wrapped string for `structuredContent` too, so a category named "Sneakers" surfaced as `<<<MERCHANT_CONTENT_START>>>Sneakers<<<MERCHANT_CONTENT_END>>>` in the machine channel. `structuredContent` now gets the unwrapped value; the delimiters stay only where they do their job, in the narration.
- **Fixed** — rule R047 (minimum contribution amount) had no form field in the admin panel; its parameters existed in the schema but could only be set via the API.
- **Fixed** — `MerchantCheckoutConfig` had translated copy for an empty state (`noRails`, present in both `en.ts` and `es.ts`) that the component never rendered, so a merchant with no configured payment rails saw an unexplained empty list.
- **Fixed** — the admin panel bundle (`assets/admin-spa/`) shipped unminified: 869 KB / 25,064 lines instead of the 490 KB / 41 lines the documented build command actually produces. Rebuilt from source with a stable output filename (`admin-spa.js`), matching the other three platform connectors.

### 2.2.0

- **Security fix** — the agent token verifier treated `exp` and `iat` as optional: both guards hung off `> 0`, so a token that simply omitted the claim skipped the check entirely. Without `exp` it never expired; without `iat` it had no maximum age. Both claims are now mandatory, and a non-numeric value is rejected rather than cast. Replay protection was already fail-closed here (a missing or malformed `jti` is rejected), so this closes the remaining half.
- **Security fix** — an `iat` in the future is now rejected (30s of clock skew tolerated). Combined with the max-age window it gave a sliding lifetime: `now - iat` stays small for as long as the issuer pushes the claim forward, so the token effectively never grew old.
- **Fix** — rule R036 (max line-item value) read its cap from a parameter named `maxCents`, copied from R035. The canonical name is `maxCentsPerLine`, and it is the only one the merchant panel's strict schema accepts, so a merchant-configured cap would never have reached the check. The canonical key is now read first; `maxCents` stays accepted as a fallback.
- **Fix** — the cross-language conformance test resolved its fixture through a path that only exists in the development monorepo, so it failed in this repository. It now reads the copy shipped in `tests/fixtures/`.
- **Trust receipts** — the admin SPA bundle is rebuilt with the receipt download button, which exports a receipt as a ZIP through the same endpoint the hosted dashboard uses. The button states plainly what the export is: proof of agent integrity, not dispute evidence.

### 2.1.0

- **Rebrand** — internal classes, option keys, and REST routes renamed `Amcp_`/`amcp_` → `Trusteed_`/`trusteed_`. Back-compat preserved: existing installs keep working (legacy `amcp_{key}` options are still read as a fallback, legacy REST namespaces stay registered alongside the new ones, legacy encrypted-value prefix still decrypts).
- **Fix** — R043 HITL payload is now plumbed through end-to-end, so a BLOCK can surface a human-in-the-loop freeze instead of a hard block that loses buyer intent.
- **Fix (critical)** — the compiled admin SPA bundle (`assets/admin-spa/`) was missing from the distributed package entirely; the Trusteed admin panel rendered a "bundle not compiled" error on every install. Bundle now ships correctly.
- Hardening in billing webhooks, checkout enforcer, catalog sync, and cart signals.

### 2.0.2

Checkout enforcement fix. Merchant rules (max order amount, blocked countries, business hours) were being skipped entirely for organic, non-agent checkouts — they now apply universally. Added an offline safety-valve evaluator that enforces these rules locally when the remote rules API is unreachable.

### 2.0.1

Critical activation + security hotfix (Codex audit). Fixes a half-finished `AGENTICMCP_*` → `TRUSTEED_*` rename that prevented activation in 2.0.0; `create_cart` now forwards the agent JWS token so R002 verification runs; REST client validates the API base host against an exact allowlist.

### 2.0.0

Security & reliability sprint. 2-phase disconnect with confirmation token; reconnect requires domain-ownership proof (`/.well-known/amcp-verify.txt`); real cart-bridge endpoint for `create_cart`; agent-event webhook retries with exponential backoff; SSRF hardening; fail-closed enforcement defaults.

## Support

- Support email: support@trusteed.xyz
- GitHub issues: [github.com/Trusteedxyz/agentic-commerce-woocommerce/issues](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/issues)

## License

GPL-2.0-or-later. See [LICENSE](LICENSE) for full text.
