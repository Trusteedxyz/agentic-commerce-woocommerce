**English** | [Español](README.es.md) | [Français](README.fr.md) | [Deutsch](README.de.md)

# Trusteed Agentic Commerce for WooCommerce

AI agents are a new kind of online shopper. With Trusteed, the network that connects businesses and agents, they can buy from your store on your terms.

- Set your business rules: who can buy, up to what amount, which categories you don't offer to agents, price limits, stock levels that protect you from fraudulent agents, and more.
- Get signed receipts. Every transaction produces a cryptographically signed, tamper-evident receipt that records what the agent actually did, a verifiable proof of agent integrity. Aligned with eIDAS (EU) and with eSIGN (USA), but not yet a *qualified* signature or timestamp, so on its own it isn't ready-made dispute evidence for a bank or court.
- See what agents do: how much they spend, what they buy and how often.
- Block agents that look dangerous or cause problems.
- Accept purchases in digital currencies through the X402 protocol.
- Let agents and merchants trade directly, peer to peer.
- Check whether agents can buy from you. The agent readiness dashboard tests live whether AI agents can actually buy in your store today. It gives three independent views (what others say, what you promise versus what you do, what we've observed), and nothing is scored until it's verified.

## Screenshots

Each panel below maps to an item in the **Trusteed** menu inside WooCommerce.

| Home | Trust Center | My Sales |
|------|--------------|----------|
| ![Home](assets/screenshots/home.png) | ![Trust Center](assets/screenshots/trust-center.png) | ![My Sales](assets/screenshots/my-sales.png) |

| My Rules | Agents | Merchant Center |
|----------|--------|-----------------|
| ![My Rules](assets/screenshots/my-rules.png) | ![Agents](assets/screenshots/agents.png) | ![Merchant Center](assets/screenshots/merchant-center.png) |

| Trust Receipts (My Sales → AI Sales) | Agent Readiness |
|--------------------------------------|------------------|
| ![Trust Receipts](assets/screenshots/ai-receipts.png) | ![Agent Readiness](assets/screenshots/agent-readiness.png) |

Every agent transaction produces a signed trust receipt, a tamper-evident record (aligned with eIDAS and with eSIGN) listed under **My Sales → AI Sales**. Click a row to see the details: agent ID, tool called, input and output hashes, JWS. You can also download the receipt as a ZIP file. The export is a verifiable proof of agent integrity. It's a useful backup if a buyer claims they never placed the order, but on its own it doesn't replace the evidence a bank or court may require in a dispute.

## Features

The plugin connects your product catalog to AI shopping agents through the **Model Context Protocol (MCP)**, an open standard created by Anthropic. It never processes payments or touches sensitive customer data. Checkout always happens in your native WooCommerce checkout.

- MCP tools for agents: `search_products`, `browse_categories`, `get_product_details` and `create_cart`, which sends the shopper to the native WooCommerce checkout.
- Automatic catalog sync. Products sync through WooCommerce hooks when you create, update or delete them, stock changes included, and you can run a full manual sync from the settings page. Only public catalog data leaves your store (titles, descriptions, prices, images, categories, stock), never customer PII, orders or payment info.
- Agent token verification: `create_cart` passes the agent's JWS token on to checkout, so signature and replay verification (R002) runs in the normal flow.
- Enforcement gate (HITL): configurable human-in-the-loop approval for high-value agent orders.
- SSRF hardening: the API base that carries your credentials must be HTTPS on an exact host allowlist. Cloud IMDS addresses (`169.254.0.0/16`, `100.100.100.200`, `metadata.google.internal`), IPv6 unique-local (`fc00::/7`) and link-local (`fe80::/10`) are blocked in every environment. Loopback and RFC1918 addresses only work behind the explicit `TRUSTEED_ALLOW_LOCAL_API_BASE` dev opt-in, which is off by default.
- Fail-closed defaults: nothing is dispatched when the enforcement secret is empty, and reconnecting requires proof that you own the domain, which protects against cross-merchant takeover.

## Compatibility

| Component | Supported |
|-----------|-----------|
| WordPress | 6.0 – 7.0 |
| WooCommerce | 8.0 – 11.0 |
| PHP | 7.4+ (tested on 8.0–8.3) |

## Requirements

- WordPress 6.0+ with WooCommerce 8.0+
- PHP 7.4 or newer
- A Trusteed account ([sign up free at trusteed.xyz](https://trusteed.xyz))

## Installation

### Manual upload (recommended)

1. **Download the installable `.zip`** from the latest GitHub Release:
   [**⬇ Latest release: trusteed-agentic-commerce-woocommerce-&lt;version&gt;.zip**](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases/latest)
   or browse all versions at the [Releases page](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases).
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
3. Enter your Trusteed email and password and click **Connect my store**. If you don't have an account yet, one is created for you. If you'd rather not use a password, click **Connect with API key** instead and paste your API key from [trusteed.xyz/dashboard/settings](https://trusteed.xyz/dashboard/settings).
4. The plugin registers your store and syncs your catalog. **Test connection** checks the link and **Sync catalog now** runs a full sync whenever you want one.

Once connected, any MCP-compatible agent (Claude, ChatGPT, or a custom one built with LangChain, CrewAI, Vercel AI SDK and similar tools) can search your products, browse categories, read product details and build carts. When the customer is ready to buy, the agent sends them to your native WooCommerce checkout, and your existing gateways (Stripe, PayPal, …) take the payment.

The full merchant walkthrough is in [`docs/MERCHANT_INSTALLATION_GUIDE.md`](docs/MERCHANT_INSTALLATION_GUIDE.md).

## FAQ

**What data is sent?** Only your public product catalog: titles, prices, descriptions, images, categories and stock status. No customer PII, payment data or order history. All communication uses HTTPS.

**Which agents are supported?** Any MCP-compatible agent: Claude (Anthropic), ChatGPT (OpenAI), and custom agents built with LangChain, CrewAI, Vercel AI SDK or any other framework that supports the Model Context Protocol.

**Does it slow down my store?** It adds one request at checkout. The plugin also talks to Trusteed when your catalog changes and when an agent acts on your store. When a customer places an order, whether an agent or a person, the plugin asks Trusteed to evaluate your rules. That request times out after 5 seconds. If Trusteed can't be reached, the plugin applies your rules from the last signed snapshot it pulled. If that isn't possible either, the result follows the `trusteed_failure_mode` option: `enforce` (the default) blocks the order and `observe` allows it.

## The agent readiness dashboard

**Can agents find me?** is a page in your admin panel that answers one
question: when an AI shopping agent visits your store, does it get what you
think it gets?

It never shows a single score. It shows three columns and doesn't average them,
because they answer different questions and can disagree:

| Column | What it is |
| --- | --- |
| **What a third party says** | The verdict of an external scanner, quoted verbatim. We never convert it to a scale of our own: rescaling someone else's grade would mean grading our own exam |
| **Does what you say match what you do?** | 16 checks that compare what your store *advertises* with what it *actually answers*. No external scanner can do this, because it needs your credentials |
| **What we have seen** | Real agent traffic in the selected window: which agents arrived, which tools they used, how far they got and where they failed |

A check that could not run is reported as **not checked**, with the reason. It
is never dropped silently and never counted as a pass. "We could not look" and
"we looked and it was fine" are different answers, and the page tells you which
one you're reading.

### What each check looks at

| Check | What it detects |
| --- | --- |
| C1 | You advertise tools your store does not serve |
| C2 | You advertise a checkout protocol whose endpoint does not answer |
| C3 | The catalogue price is not the price charged |
| C4 | Things are advertised as available when they are not |
| C5 | Your return policy says different things depending on where you look |
| C6 | You advertise as available something that is switched off |
| C7 | Rules switched on that cannot act for lack of data |
| C8 | Your rules observe but do not block |
| C9 | The identification method you advertise does not work |
| C10 | An agent can buy any amount without your confirmation |
| C11 | The point of sale is using expired rules |
| C12 | Operations with no signed receipt |
| C13 | Advertised addresses that do not work |
| C14 | Agents are seeing stale data from your store |
| C15 | Identity credentials about to expire |
| C16 | The delivery time you promise is not the one you meet |

Some checks need more than your settings to run, and the page says so instead of
leaving a gap:

- Needs your store connected (C3, C4, C5, C14): they compare against your real
  catalogue, and without credentials there is nothing to compare with.
- Needs delivered orders (C16): it compares what you promise with what you
  actually met, which can't be done without history.
- Nothing to compare this time: C12, for example, has nothing to check until an
  agent has completed a purchase. That is not a failing grade.

The checks run once a day and the page shows the result **with its date**, so a
verdict from yesterday looks like one from yesterday. A cached "all good"
passed off as current is exactly the kind of self-deception this page exists
to catch.

## Changelog

### 2.3.4

- New: a check that could not run now says why in one of four groups (nothing to do, needs configuration, waiting for data, or one of our own checks failed) instead of one flat list of unexplained grays.
- New: the panel now shows which of our servers answered your request, a short opaque label. Useful when comparing what you see here with what support sees; it never reveals a hostname or service name.

### 2.3.3

- New: Settings now lets you choose which tools your store serves to agents. If you never saved a list, the panel tells you that what you serve is the basic set the platform ships with, not a choice of yours.
- New: a button to re-run the readiness check without waiting for the daily sweep, and the panel remembers what changed since the previous run.
- Changed: our own outages no longer count as your store's mismatches. The panel keeps them separate, because there is nothing you can do about them.

### 2.3.2

- Fixed: the agent readiness page shipped without its stylesheet, so the panel rendered unstyled.
- Fixed: the panel could show its shell in one language and the diagnosis in another. The resolved language now travels with the texts instead of being detected twice.
- New: every finding carries a link to where it is fixed, and the merchant's own claims, the delivery promise and the rest, appear with the backing each one has.
- Changed: a store with no run yet reads as "checking" instead of "checked once a day": opening the panel already triggers the first run in the background.

### 2.3.1

- Critical fix: 2.3.0 shipped a PHP parse error in the admin router (`->render_spa_shell()` with no `$this`, and `array( , 'render_agent_readiness' )`). Activating it fataled the **entire WordPress admin**, not just the Trusteed pages. Anyone on 2.3.0 should upgrade immediately. Every PHP file in the plugin is now syntax-checked.
- Fixed: the Agent Readiness page rendered the Trust Center instead. The SPA mount validates the section against an allowlist and `agent-readiness` had never been added to it, so it fell back silently: the merchant clicked "Agent Readiness" and got a different panel.

### 2.3.0

- New, agent readiness dashboard: *Can agents find me?* now ships in the admin panel. It contrasts what your store advertises with what it actually answers, in **16 checks**, and shows all sixteen, not only the ones that fail. A check that could not run says **why** (store not connected, no delivered orders yet, nothing to compare this time) instead of leaving a gap that reads like a fault. See "The agent readiness dashboard" above.
- Fixed: the diagnosis was written in Spanish inside the API and shown verbatim, so a merchant with the panel in English read English headings above Spanish findings. The checks now emit language-neutral codes and the text is composed when served, in the language you are using.
- Fixed: check C1 ("you advertise tools your store does not serve") counted the full public catalogue as served when no tool list was configured, reporting 46 of 48 answering when the server actually serves 12. It failed in the flattering direction, which is the one this panel exists to catch.
- Fixed: check C6 ("you advertise as available something that is switched off") reported a capability as off whenever its flag was unset, even for flags that are on by default. It was a false alarm on every store.

### 2.2.2

- Security fix: the API client accepted a loopback or RFC1918 API base URL (`10.*`, `172.16–31.*`, `192.168.*`, `localhost`, `127.*`) in **every** environment, with plain HTTP tolerated. An install whose API URL had been redirected would send its `X-AgenticMCP-Key` credential to an internal address. The dev override is now opt-in and off by default, enabled only via `TRUSTEED_ALLOW_LOCAL_API_BASE` or a `local` WordPress environment type. It is the same gate `Trusteed_Token_Broker` already applied via `WP_DEBUG`, which this client had dropped.
- Security fix: cloud instance metadata and IPv6 internal ranges are now denied outright in every environment, *including* under the dev override, which previously re-opened all of them: `169.254.0.0/16` (IMDS), Alibaba `100.100.100.200`, `metadata.google.internal`, unique-local `fc00::/7`, link-local `fe80::/10`. They also get their own error code instead of the misleading "configure an HTTPS URL" message.
- Fixed: IPv6 API hosts never matched any check: `parse_url()` returns them bracketed (`[::1]`), so the `::1` loopback entry was dead code.
- Privacy fix: uninstalling left 21 option rows behind, including three encrypted secrets (`trusteed_embed_wp_secret`, `trusteed_enforcement_hmac_secret`, `trusteed_woo_webhook_secret`) and the legacy `amcp_*` aliases the option accessor still reads as a fallback, so a reinstall could resurrect a stale secret. `uninstall.php` now clears all three namespaces plus the cached snapshot and JWKS transients. A new test scans the source for every writable option key and fails if the uninstall list falls behind.
- Docs fix: trust receipts were described as "proof of the actual transaction in case of any dispute". The product itself says the opposite: a verifiable integrity proof, not ready-made dispute evidence for a bank or court. Corrected here to match.
- Docs fix: the deactivation FAQ claimed deactivating disconnects the store and that no residual data remains on our servers. Deactivation is inert, and disconnecting retains the store record and synced products. Corrected, with the deletion-request route documented.
- Docs fix: the catalog was described as syncing "variants and reviews"; neither is sent. The transmitted field list is now exact.
- Docs fix: `Tested up to` / `WC tested up to` disagreed between `readme.txt` (6.9 / 10.6) and the plugin header (6.7 / 9.5). Both now read 6.9 / 10.6. Automated tests run against WordPress 6.8 with latest-stable WooCommerce on PHP 8.1–8.2.
- Docs fix: repaired 404 links: `/developers`, `/privacy` and `/terms` need the `/en/` prefix, and `/support` does not exist (replaced with the contact form and GitHub issues).

### 2.2.1

- Fixed: `browse_categories` fed the same wrapped string to both the machine-readable and narrated channels. `guardMerchantField` wraps merchant text in `<<<MERCHANT_CONTENT_START>>> … <<<MERCHANT_CONTENT_END>>>` by default so an agent can tell "this is merchant data, not an instruction", but the tool reused that already-wrapped string for `structuredContent` too, so a category named "Sneakers" surfaced as `<<<MERCHANT_CONTENT_START>>>Sneakers<<<MERCHANT_CONTENT_END>>>` in the machine channel. `structuredContent` now gets the unwrapped value; the delimiters stay only where they do their job, in the narration.
- Fixed: rule R047 (minimum contribution amount) had no form field in the admin panel; its parameters existed in the schema but could only be set via the API.
- Fixed: `MerchantCheckoutConfig` had translated copy for an empty state (`noRails`, present in both `en.ts` and `es.ts`) that the component never rendered, so a merchant with no configured payment rails saw an unexplained empty list.
- Fixed: the admin panel bundle (`assets/admin-spa/`) shipped unminified: 869 KB / 25,064 lines instead of the 490 KB / 41 lines the documented build command actually produces. Rebuilt from source with a stable output filename (`admin-spa.js`), matching the other three platform connectors.

### 2.2.0

- Security fix: the agent token verifier treated `exp` and `iat` as optional: both guards hung off `> 0`, so a token that simply omitted the claim skipped the check entirely. Without `exp` it never expired; without `iat` it had no maximum age. Both claims are now mandatory, and a non-numeric value is rejected rather than cast. Replay protection was already fail-closed here (a missing or malformed `jti` is rejected), so this closes the remaining half.
- Security fix: an `iat` in the future is now rejected (30s of clock skew tolerated). Combined with the max-age window it gave a sliding lifetime: `now - iat` stays small for as long as the issuer pushes the claim forward, so the token effectively never grew old.
- Fix: rule R036 (max line-item value) read its cap from a parameter named `maxCents`, copied from R035. The canonical name is `maxCentsPerLine`, and it is the only one the merchant panel's strict schema accepts, so a merchant-configured cap would never have reached the check. The canonical key is now read first; `maxCents` stays accepted as a fallback.
- Fix: the cross-language conformance test resolved its fixture through a path that only exists in the development monorepo, so it failed in this repository. It now reads the copy shipped in `tests/fixtures/`.
- Trust receipts: the admin SPA bundle is rebuilt with the receipt download button, which exports a receipt as a ZIP through the same endpoint the hosted dashboard uses. The button states plainly what the export is: proof of agent integrity, not dispute evidence.

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
