**English** | [Español](README.es.md) | [Français](README.fr.md) | [Deutsch](README.de.md)

# Trusteed Agentic Commerce for WooCommerce

AI agents are a new kind of online shopper. With Trusteed, the network that connects businesses and agents, they can buy from your store on your terms.

- Set your business rules: who can buy, up to what amount, which categories you don't offer to agents, price limits, stock levels that protect you from fraudulent agents, and more.
- Get signed receipts. Every transaction produces a cryptographically signed, tamper-evident receipt you can use as evidence of the purchase if there's a dispute. Aligned with eIDAS (EU) and with eSIGN (USA).
- See what agents do: how much they spend, what they buy and how often.
- Block agents that look dangerous or cause problems.
- Accept purchases in digital currencies through the X402 protocol.
- Let agents and merchants trade directly, peer to peer.

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

Every agent transaction produces a signed trust receipt, a tamper-evident record (aligned with eIDAS and with eSIGN) listed under **My Sales → AI Sales**. Click a row to see the details: agent ID, tool called, input and output hashes, JWS. You can also download the receipt as a ZIP file and keep it as backup in case of a dispute.

## Features

The plugin connects your product catalog to AI shopping agents through the **Model Context Protocol (MCP)**, an open standard created by Anthropic. It never processes payments or touches sensitive customer data. Checkout always happens in your native WooCommerce checkout.

- MCP tools for agents: `search_products`, `browse_categories`, `get_product_details` and `create_cart`, which sends the shopper to the native WooCommerce checkout.
- Automatic catalog sync. Products sync through WooCommerce hooks when you create, update or delete them, stock changes included, and you can run a full manual sync from the settings page. Only public catalog data leaves your store (titles, descriptions, prices, images, categories, stock), never customer PII, orders or payment info.
- Agent token verification: `create_cart` passes the agent's JWS token on to checkout, so signature and replay verification (R002) runs in the normal flow.
- Enforcement gate (HITL): configurable human-in-the-loop approval for high-value agent orders.
- SSRF hardening: store and API URLs are checked against an exact host allowlist and against RFC1918, IPv6 ULA and cloud IMDS blocklists.
- Fail-closed defaults: nothing is dispatched when the enforcement secret is empty, and reconnecting requires proof that you own the domain, which protects against cross-merchant takeover.

## Compatibility

| Component | Supported |
|-----------|-----------|
| WordPress | 6.0 – 6.9 |
| WooCommerce | 8.0 – 10.6 |
| PHP | 7.4+ (tested on 8.0–8.3) |

## Requirements

- WordPress 6.0+ with WooCommerce 8.0+
- PHP 7.4 or newer
- A Trusteed account ([sign up free at trusteed.xyz](https://trusteed.xyz))

## Installation

### Manual upload (recommended)

1. **Download the installable `.zip`** from the latest GitHub Release:
   [**⬇ trusteed-agentic-commerce-woocommerce-2.1.0.zip**](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases/latest/download/trusteed-agentic-commerce-woocommerce-2.1.0.zip)
   or browse all versions at the [Releases page](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases).
2. In your WordPress admin: **Plugins → Add New → Upload Plugin**.
3. Select the downloaded `trusteed-agentic-commerce-woocommerce-2.1.0.zip` and click **Install Now**.
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
3. Enter your **API Key** from [app.trusteed.xyz/settings](https://app.trusteed.xyz/settings).
4. Click **Save & Connect**. The plugin tests the connection, registers your store and syncs your catalog.

Once connected, any MCP-compatible agent (Claude, ChatGPT, or a custom one built with LangChain, CrewAI, Vercel AI SDK and similar tools) can search your products, browse categories, read product details and build carts. When the customer is ready to buy, the agent sends them to your native WooCommerce checkout, and your existing gateways (Stripe, PayPal, …) take the payment.

The full merchant walkthrough is in [`docs/MERCHANT_INSTALLATION_GUIDE.md`](docs/MERCHANT_INSTALLATION_GUIDE.md).

## FAQ

**What data is sent?** Only your public product catalog: titles, prices, descriptions, images, categories and stock status. No customer PII, payment data or order history. All communication uses HTTPS.

**Which agents are supported?** Any MCP-compatible agent: Claude (Anthropic), ChatGPT (OpenAI), and custom agents built with LangChain, CrewAI, Vercel AI SDK or any other framework that supports the Model Context Protocol.

**Does it slow down my store?** It adds one request at checkout. The plugin also talks to Trusteed when your catalog changes and when an agent acts on your store. When a customer places an order, whether an agent or a person, the plugin asks Trusteed to evaluate your rules. That request times out after 5 seconds. If Trusteed can't be reached, the plugin applies your rules from the last signed snapshot it pulled. If that isn't possible either, the result follows the failure mode you set in the plugin settings: block the order or allow it.

## Changelog

### 2.1.0

- Rebrand: internal classes, option keys and REST routes are renamed from `Amcp_`/`amcp_` to `Trusteed_`/`trusteed_`. Existing installs keep working. Legacy `amcp_{key}` options are still read as a fallback, legacy REST namespaces stay registered next to the new ones, and the legacy encrypted-value prefix still decrypts.
- Fix: the R043 HITL payload now passes through end to end, so a BLOCK can surface a human-in-the-loop freeze instead of a hard block that loses the buyer's intent.
- Fix (critical): the compiled admin SPA bundle (`assets/admin-spa/`) was missing from the distributed package. The Trusteed admin panel showed a "bundle not compiled" error on every install. The bundle now ships correctly.
- Hardening in billing webhooks, the checkout enforcer, catalog sync and cart signals.

### 2.0.2

Checkout enforcement fix. Merchant rules (max order amount, blocked countries, business hours) were skipped entirely for organic, non-agent checkouts. They now apply to every checkout. Added an offline safety-valve evaluator that enforces these rules locally when the remote rules API is unreachable.

### 2.0.1

Critical activation and security hotfix (Codex audit). Fixes a half-finished `AGENTICMCP_*` → `TRUSTEED_*` rename that prevented activation in 2.0.0. `create_cart` now forwards the agent JWS token so R002 verification runs. The REST client validates the API base host against an exact allowlist.

### 2.0.0

Security and reliability sprint. Disconnecting is now a two-phase flow with a confirmation token. Reconnecting requires proof of domain ownership (`/.well-known/amcp-verify.txt`). `create_cart` gets a real cart-bridge endpoint. The agent-event webhook retries with exponential backoff. Also SSRF hardening and fail-closed enforcement defaults.

## Support

- Support email: support@trusteed.xyz
- GitHub issues: [github.com/Trusteedxyz/agentic-commerce-woocommerce/issues](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/issues)

## License

GPL-2.0-or-later. See [LICENSE](LICENSE) for full text.
