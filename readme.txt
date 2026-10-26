=== Trusteed for WooCommerce ===
Contributors: trusteed
Tags: ai, mcp, agentic-commerce, ai-agents, product-search, chatgpt, claude, ai-shopping
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 2.5.2
WC requires at least: 8.0
WC tested up to: 11.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connect your WooCommerce store to AI agents via MCP. Claude, ChatGPT and other agents can search your catalog, browse categories and create carts. Checkout stays in your native WooCommerce checkout.

== Description ==

Trusteed for WooCommerce connects your product catalog to AI shopping agents. It uses the **Model Context Protocol (MCP)**, an open standard created by Anthropic, so agents can work with your store programmatically.

Once connected, any MCP-compatible agent (Claude by Anthropic, ChatGPT by OpenAI or a custom-built one) can search your products, browse your category taxonomy, read product details (name, SKU, price and sale price, description, images, categories, tags, product type and stock status), and build shopping carts for users. When a customer is ready to buy, the agent sends them to your **native WooCommerce checkout page**, and your existing payment gateways (Stripe, PayPal or any other) handle the transaction. The plugin never processes payments or touches sensitive customer data.

Your catalog syncs through WooCommerce hooks whenever you create, update or delete a product, and when stock levels change. You can also run a full manual sync from the settings page at any time. The sync sends only public catalog data: titles, descriptions, prices, images, categories and stock status. No customer PII, order history or payment information is ever sent.

The plugin is fully compatible with **WooCommerce High-Performance Order Storage (HPOS)** and **Cart/Checkout Blocks**. It also works with popular extensions such as WooCommerce Subscriptions, Bookings and Bundles.

Connecting your store opens a new sales channel, AI-assisted shopping, without changing your tech stack, theme or checkout flow. Customers find your products in AI conversations and complete the purchase on your usual checkout page.

For more information, visit [trusteed.xyz](https://trusteed.xyz).

== Installation ==

1. Upload the `trusteed-for-woocommerce` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce > Trusteed in the admin menu
4. Enter your Trusteed email and password and click "Connect my store". Or click "Connect with API key" and paste a key (get one free at [trusteed.xyz/en/developers](https://trusteed.xyz/en/developers))
5. Your catalog syncs automatically

== Frequently Asked Questions ==

= Does this plugin process payments? =

No. Checkout always happens in your native WooCommerce checkout. AI agents create carts and then send users to your checkout page, where your existing payment gateways (Stripe, PayPal, etc.) handle the transaction. This plugin never handles payment data.

= What data is sent to Trusteed? =

Only your product catalog (titles, prices, descriptions, images, categories, stock status). No customer PII, payment data or order history is transmitted. All communication uses HTTPS encryption.

= Is this compatible with WooCommerce HPOS? =

Yes. Full compatibility with High-Performance Order Storage is declared and tested. The plugin also supports Cart/Checkout Blocks.

= What MCP tools does this enable? =

Once connected, your store exposes these MCP tools to agents: `search_products`, `browse_categories`, `get_product_details`, and `create_cart` (with native WooCommerce checkout redirect).

= Do I need a paid plan? =

No. The free tier gives you full catalog sync and AI agent access for one store. Paid plans (Growth, Pro, Enterprise) add analytics, white-label features, higher rate limits, and multi-store support.

= Which AI agents work with this? =

Any MCP-compatible agent: Claude (Anthropic), ChatGPT (OpenAI with MCP support), custom agents built with LangChain, CrewAI, Vercel AI SDK, and any other framework that supports the Model Context Protocol.

= How often does the catalog sync? =

Products sync automatically when you create, update or delete them via WooCommerce hooks. Stock level changes also trigger a sync. You can also run a full catalog sync manually from the settings page.

= Is my store data secure? =

Yes. All communication between your store and Trusteed uses HTTPS. Your API key is stored securely in the WordPress database. No customer data is ever transmitted, only public product catalog information.

= Can I use this with other WooCommerce extensions? =

Yes. The plugin is compatible with WooCommerce Subscriptions, Bookings, Bundles and other popular extensions. It reads product data through standard WooCommerce APIs and does not interfere with other plugins.

= Where can I get support? =

Email support@trusteed.xyz, use the [contact form](https://trusteed.xyz/en/contact), or open an issue on [our GitHub repository](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/issues).

= What happens if I deactivate the plugin? =

Deactivating stops the plugin from running, so no further catalog syncs or agent-enforcement calls are made from your site. It does **not** revoke your API key or disconnect the store on our side. To do that, click **Disconnect store** on the settings page before deactivating.

Disconnecting expires your API key, marks the store inactive so agents can no longer reach it, and deletes the stored WooCommerce webhook secret. It does not erase the catalog data already synced: your store record and previously synced product rows are retained (products are marked unavailable rather than deleted) so that reconnecting restores the store without a full re-sync. To have that data erased, request deletion as described in our [Privacy Policy](https://trusteed.xyz/en/privacy).

Uninstalling the plugin removes every option row it created from your WordPress database, including all stored secrets.

= Does this slow down my store? =

It adds one request at checkout. The plugin also talks to Trusteed when your catalog changes (product create, update, delete) and when an AI agent acts on your store. When a customer places an order, whether an agent or a person, the plugin asks Trusteed to evaluate your rules. That request times out after 5 seconds. If Trusteed can't be reached, the plugin applies your rules from the last signed snapshot it pulled. If that isn't possible either, the result follows the `trusteed_failure_mode` option: `enforce` (the default) blocks the order and `observe` allows it.

== External services ==

This plugin is a connector for the **Trusteed** service and requires a Trusteed
account to function. It communicates with the Trusteed API (default
`https://api.trusteed.xyz`, configurable per-install) so that AI shopping agents
can interact with your catalog. Below is every request the plugin makes, the data
it sends, and when it is triggered.

* **Connection test**: `GET /api/v1/health`. Sends no store data; measures
  round-trip latency. Triggered when you click "Test connection" on the settings
  page.
* **Onboarding**: `POST /api/v1/plugin/onboard`. Sends the email and password you
  enter to create or link your Trusteed account. Triggered only when you submit the
  onboarding form. Credentials are exchanged for an API key and are not stored in
  plaintext by the plugin.
* **Store registration**: `POST /api/v1/plugin/register`. Sends store metadata
  (site URL, store name/slug). Triggered when you connect the store.
* **Catalog sync**: `POST /api/v1/plugin/catalog/sync`. Sends public product data
  only: product id, name, SKU, price, sale/compare-at price, currency, stock status
  and quantity, permalink, description and short description (tags stripped),
  categories, images (URL + alt text), tags, and product type. Individual
  variations of a variable product are not sent, only the parent product with its
  own price and stock values. Product reviews and ratings are not sent.
  Triggered on product create/update/stock-change/delete and on manual full sync.
  No customer PII, order history, or payment data is ever sent.
* **Rule snapshot**: `GET /v1/rules/snapshot/{merchantId}` and key discovery
  `GET /.well-known/jwks.json`. Fetches your signed enforcement ruleset and the
  public keys used to verify it. Sends your merchant identifier only.
* **Agent enforcement**: `POST /v1/rules/evaluate`, `POST /v1/agent-events` and
  `POST /v1/agent-events/nonce-consume`. Sends agent-action context (agent
  identifier/DID, cart context, single-use nonce) when an AI agent acts on your
  store, so the action can be authorized and audited.
* **Embed token**: `POST /v1/embed/wp/issue-token`. Issues a short-lived token for
  the in-admin Trust Center panel. Triggered when you open that panel.

All requests use HTTPS. The plugin never processes payments and never transmits
customer personal data or order history.

Service provider: Trusteed ([https://trusteed.xyz](https://trusteed.xyz))
Terms of Use: [https://trusteed.xyz/en/terms](https://trusteed.xyz/en/terms)
Privacy Policy: [https://trusteed.xyz/en/privacy](https://trusteed.xyz/en/privacy)

== Screenshots ==

1. Settings page: configure your API key and monitor connection status
2. Connection status: green badge when connected, sync statistics visible
3. AI agent searching products: Claude finding products in your catalog via MCP

== Security Acknowledgements ==

We thank MD Rabbi Hossain (LinkedIn: https://www.linkedin.com/in/csrrabbi/; X: https://x.com/csrrabbi) for a responsible disclosure report against our identity provider, auth.trusteed.xyz.

The report's central claim is accurate: our OAuth dynamic client registration endpoint (/oidc/register, RFC 7591) accepts requests without a credential. That is deliberate: our MCP connectors, including Claude's, self-register through this endpoint before they can authorize at all, and requiring a credential there would break them. The access it enables is consent phishing, a property of any open dynamic-registration flow rather than unauthenticated access, and it does not reach checkout: our payment path requires a verified agent-identity claim that a token obtained this way does not carry.

Investigating the report surfaced something it did not flag: an OAuth scope (mcp:admin) was published across our discovery documents and offered on the consent screen, but no code enforced it: a token holding it carried the exact same privileges as mcp:read. That scope has been retired platform-wide. The fix lives in our private API and dashboard, not in this plugin, but we record the credit here as agreed with the reporter.

== Changelog ==

= 2.5.2 =
* New: browser agents (browser-use style) that sign their requests with RFC 9421 HTTP Message Signatures are now recognised at checkout, according to the `browser_agent_signature_mode` setting. This release also contains the change below, so it bundles two independent changes.
* Change: when the store's rules block an order that an agent placed through Trusteed, the plugin now tells Trusteed why (`reason_category`, `rule_code`, `reason`, `next_action` on the `422 rule_blocked` answer), so the agent knows what to do next instead of getting a bare refusal. With a Trusteed server that sends `agent_reason` the reason is the server's verdict. With an older server the plugin derives a generic reason itself (`merchant_policy` or `checks_unavailable`) and marks it with `reason_source: plugin_fallback` so it is never taken for the server's verdict; it does not stay silent. `rule_code` is kept only if it looks like a rule code (up to 64 characters); anything else is dropped. An error that is not a rule block no longer answers as `rule_blocked` (it uses `order_invalid`).
* Change: the same answer can also say what caused the block when Trusteed knows it: `blocked_item` (a category, a SKU or a digital product type), `published_limit` (the limit the store published, only when the rule that fired has one) and `human_checkout` (a link to your own human checkout, only if you provided one). Each key is checked on its own and dropped if it does not look right.

= 2.5.1 =
* Fix: when a store requires a verified agent identity, orders that Trusteed creates for a verified agent are no longer blocked by the plugin's own second check. The plugin now passes along the identity level Trusteed resolved (only the four known values are accepted; anything else is ignored). Update the plugin before turning that rule option on.

= 2.5.0 =
* Change: an agent order is no longer marked as paid unless the payment provider confirms the charge. The plugin now checks the payment (Stripe PaymentIntent or PayPal capture) with the credentials of your own account; if it cannot confirm it, the order stays On hold and the agent orders endpoint answers `409 payment_not_verified` (definitive) or `503 payment_verification_unavailable` (provider unreachable or no credentials; Trusteed retries). Review On hold orders from agents in WooCommerce > Orders. If the charge was made on a Trusteed demo account instead of your own, it will not be confirmed by design.

= 2.4.9 =
* Fix: variable products now send their variations (price, stock and attributes) with the catalog sync. Before, Trusteed listed a variable product without any variation, so an agent could add it to the cart without choosing one and the store refused to price the order. Variations without a price are skipped. Run "Sync catalog" once after updating so existing products pick them up.

= 2.4.8 =
* New: the hourly heartbeat now also sends the link, title and last-modified date of your Terms and Privacy Policy pages (only if they are published). Trusteed shows them to agents as imported from your store, separate from the policies you declare. The page content is never sent.

= 2.4.7 =
* New: orders paid through PayPal can now be marked as paid on your store. The agent orders endpoint accepts a PayPal capture reference (with `payment_method = paypal`) next to the Stripe PaymentIntent, and titles the payment method as PayPal. Stripe payments are unchanged.

= 2.4.6 =
* New: "Use my WooCommerce PayPal account" in the Payment methods panel. The credentials already set in WooCommerce PayPal Payments are sent to Trusteed by your site (never through the browser) and kept in sync when you change them; disconnecting PayPal there disconnects it in Trusteed too. Payments go to your own PayPal account. Opt-in.

= 2.4.5 =
* New: "Use my WooCommerce Stripe account" in the Payment methods panel. The Stripe key already set in the WooCommerce Stripe plugin is sent to Trusteed by your site (never through the browser) and kept in sync when you change it. Opt-in.
* New: purchases paused for your approval show up under My sales, and a notice on Home tells you when one is waiting.

= 2.4.4 =
* Fixed: the capabilities report no longer includes cart signals that no rule reads. The Trusteed API rejected the whole report (HTTP 400), so the rules checks in the dashboard stayed without a verdict.
* Fixed: a rejected capabilities report is retried after one hour instead of on every page load.
* Changed (My sales): an order placed by an AI agent now shows the agent and the client it declared (for example ChatGPT or Claude), the channel and payment method (for example "Agent · MCP · x402"), and the real total. It used to show "Other channel", "Person" and "0.00 USD". The order number opens the signed Trust Receipt of the purchase. Requires the matching Trusteed API update.
* Changed (Security log): an entry without an agent id is no longer labelled "Person"; tool calls are labelled as agent activity.
* Changed (Agent readiness): a check without a verdict now says why (needs a setting, waiting for data, does not apply, we will retry) instead of "Not checked"; checks that do not apply are counted apart. The three panels are stacked full width and wide tables no longer overflow their card.

= 2.4.3 =
* New: the hourly heartbeat also reports your tax display settings (prices entered with or without tax, and how the shop shows them), so Trusteed can tell whether the catalog price and the price an agent sees are comparable. No customer data.

= 2.4.2 =
* New: the plugin reports which cart signals it provides to Trusteed (`POST /api/v1/enforcement/capabilities`, signed with the installation secret) on update, after saving credentials and once a day, so the dashboard can tell which rules your store can actually feed. Network failures are logged and never break the admin.
* New: an hourly heartbeat (WP-Cron) tells Trusteed the plugin is alive, so a quiet store is no longer reported as out of date.
* New: when an order is marked Completed, the plugin sends an `order.completed` event (no customer data: id, number, totals, dates and the agent id when present) so Trusteed can compare your shipping promise with real fulfilment times. Requires a connection made with 2.4.1 or later.

= 2.4.1 =
* Fixed: connecting a store never saved its enforcement credentials (installation id, installation secret and WooCommerce webhook secret). Trusteed sent them, but the plugin dropped them before saving, so everything signed with the installation secret was refused: rule snapshots, agent events and the `trusteed/v1/*` endpoints. Stores connected before this fix must reconnect once.
* Fixed: reconnecting a store that was already registered did nothing. Trusteed asks for proof that you control the domain; the plugin now answers it automatically by serving a one-time token at `/.well-known/amcp-verify.txt` for up to ten minutes (no file is written).
* Fixed: the catalog sync was rejected by the Trusteed API (HTTP 400) for any store with product categories, because category ids were sent as numbers instead of strings. Products that do not manage stock also no longer send a null stock quantity, which the API rejects.
* Fixed (documentation): the connection steps in the readme and the merchant guide described pasting an API key from the dashboard. The plugin connects with your Trusteed email and password ("Connect my store"); an API key is only for reconnecting an existing store. The guide also states the real requirements (HTTPS, non-Plain permalinks, password rules) and no longer promises an automatic initial catalog sync or a working "Sync" button.
* Known issue: the "Sync" button on the settings page only refreshes the store registration; it does not push the full catalog.

= 2.4.0 =
**Trusteed can now create and confirm orders in your store on behalf of an AI agent (off by default).**

* New: a payment method called "Trusteed (agente)" (`trusteed_agent`). It never appears at checkout; it only labels orders that Trusteed creates and charges outside WooCommerce.
* New: three REST endpoints under `/wp-json/trusteed/v1/`: `POST /quote` (price, shipping, taxes and discounts calculated by WooCommerce itself), `POST /orders` (creates the order once per Trusteed order id, checks that the total matches the approved one, and runs your store rules) and `POST /orders/{id}/paid` (confirms the payment and reduces stock).
* Off by default: nothing is registered unless the option `trusteed_agent_orders_enabled` is set to `yes`.
* Security: every call must carry `X-Trusteed-Signature: t=<unix>,v1=<hmac>` signed with the installation secret, inside a +/-300 second window; a signature is accepted once only. A bad signature gets a plain 401 with no detail.
* A paid amount that does not match the order puts it on hold with a note for you to review; a total that does not match the approved one deletes the order before anything is charged.

= 2.3.6 =
**A shopper is no longer blocked at checkout when Trusteed cannot answer.**

* Fixed: a valid agent token minted by Trusteed's own contract was rejected as invalid (`_agent_token_jti_missing`). The plugin required a `jti` claim that the canonical token (the schema, the server verifier and the repository's token generator) does not carry: its single-use identifier is `nonce`. The plugin now reads `nonce` (16–64 characters, letters, digits, `_` and `-`) as the single-use identifier it sends to the call that stops a token from being used twice; `jti` is only accepted as a legacy alias when `nonce` is absent, and `nonce` wins if both are present and differ. A token with neither, or with a malformed one, is still an invalid token (the `_agent_token_jti_missing` / `_agent_token_jti_malformed` markers are kept and now cover the nonce) and nothing is spent on the server. Strictly tighter than before in one respect: a `jti` between 65 and 128 characters, or a `nonce` outside that alphabet, no longer passes.
* Fixed: if the Trusteed API was unreachable, slow, rate-limited or returned an error, the "enforce" failure mode blocked every checkout, people included. Now the failure mode only applies to checkouts started by an AI agent. A person's checkout goes through; the only thing that can still stop it during an outage is one of your own store-wide limits (for example your maximum order amount), checked against the last rules the plugin downloaded.
* Fixed: an unexpected error of the plugin's own while checking an order (token verification, the offline safety valve or the rules client) no longer aborts a person's checkout. An agent's checkout is held in every failure mode, worded as an outage and not as a rule; a person's goes through. The error class is logged, never its message.
* Fixed: a store whose merchant ID is not configured (the plugin is not connected to Trusteed) no longer blocks anyone at checkout: there are no rules to check against, so neither people nor agents are held.
* Fixed: during an outage, agent-only rules are no longer applied to people's checkouts.
* New: carts created by Trusteed's MCP carry an origin marker in the checkout link (`trusteed_origin`), so an agent without an identity is treated as an agent, not as a person. Removing the marker from the link only makes the checkout look like any other visitor's; adding it only makes the checks stricter.
* Fixed: the "enforcement indeterminate" signal sent to Trusteed when an agent checkout could not be evaluated was never sent. It is now.
* Security fix: an order was credited to whatever agent ID came in the checkout link, with nothing proving it: anyone could pin their orders, refunds and cancellations on another agent's history. Now an order is credited to an agent only when that agent's signed token checks out for that same ID. A link that names one agent and carries another agent's token is treated as carrying an invalid token. Through the WooCommerce REST API, the agent-credit fields of an order can no longer be written; the order keeps what it had.
* Fixed: an agent that only sent its signed token (in the checkout link, the checkout form or the block checkout request, in the `_trusteed_agent_token` field) was treated as a person. It is now treated as an agent, and a token that is not even well-formed counts as an invalid agent token, never as "no token".
* Changed: when an agent's checkout is stopped because Trusteed could not be reached, the message now says the order could not be verified right now (block checkout answers 503) instead of claiming it failed your rules.
* Security fix: an agent ID that was only stated (in the checkout link) and not proven by a valid signed token was still sent to Trusteed as the agent's identity. Trusteed keeps each agent's history (orders, refunds, disputes, failed checkouts) under that ID, so an agent could borrow another agent's record, and anyone could use up another agent's counters with a made-up token. Now only an ID proven by a valid token is sent. Any other agent checkout is checked as an agent without an identity: your agent rules still apply, and if you have the rule that requires a verified agent identity (R001) turned on, it blocks the checkout. A token that cannot be checked (for example, signed by a key Trusteed does not know) now counts as an invalid token.
* Changed: when the service that stops a signed token from being used twice cannot be reached, the agent is no longer treated as anonymous: its signature did check out, so its identity is kept and the checkout carries a `_agent_token_nonce_unavailable` marker (Trusteed ignores the plugin's allowlist for it). In "enforce" failure mode the agent is held before Trusteed is asked, worded as "could not be verified right now", not as a rule, even if your rules would have let it through; in "observe" it goes through. In both cases the order is not credited to that agent's history, because without the single-use check the token cannot be told from a replay. A token whose signature could not be checked at all (its ID is not among Trusteed's agent keys) is still an invalid token, as before.
* Fixed: a checkout link whose agent parameters were written as a list (for example `?trusteed_agent_token[]=x`) made the checkout page fail with a server error. The page now loads and the checkout is treated as an agent's with an invalid token.
* Security fix: the rule snapshot is only used once its signature checks out. Before, if Trusteed's signing keys could not be downloaded (or the PHP `sodium` extension was missing) the plugin read the snapshot without checking it and used its agent keys and its rules as if they were genuine.
* Changed: an agent checkout that carries a signed token is held (the agent is told the order could not be verified right now) in both failure modes, "observe" included, when there is no rule snapshot that checks out: Trusteed cannot be reached and there is no recent copy, or the snapshot Trusteed sent does not check out even after downloading its signing keys again. Without the snapshot the token cannot be checked, so letting it through would let a forged token pass. Trusteed is not asked in that case and the token is not used up. A person's checkout is never held by this.
* Changed: when Trusteed does not answer and the rule snapshot does not check out, your store-wide limits in that snapshot are not applied (they may not be yours) and an agent checkout is held in both failure modes; a person's checkout goes through. With a snapshot that checks out, nothing changes: your limits still apply to everyone and the failure mode decides for agents.
* Changed: the agent keys in the rule snapshot are read in both the current list format and the older per-agent format; a malformed entry for an agent no longer hides a good key listed before it.
* Fixed: large rule snapshots are now read. When your rules pass about 50 KB (for example, with many registered agents) Trusteed sends them compressed, and the plugin could not read them: with the change above, every agent checkout with a signed token would have been held. They are now decompressed only after their signature checks out, and never beyond 2 MB; one that still cannot be read counts as "Trusteed cannot be reached", not as a snapshot that does not check out.
* Changed: with the Trusteed AgenticTools plugin (2.0.3 or later) also active, this plugin is the one that evaluates the classic and the block checkout, with or without an agent token; AgenticTools stands aside on those two paths (it still evaluates orders created through the WooCommerce REST API, which this plugin does not evaluate). Before, an agent token was evaluated by both plugins, twice per checkout, and the two token formats could disagree.
* Fixed: an unexpected error while reading the cart or the order to build the checkout evaluation (before the check itself ran) could still abort a person's checkout. It is now handled like any other internal error of the plugin: a person's checkout goes through, an agent's is held as an outage and not as a rule, and only the error class is logged.
* Security fix: a rule snapshot whose signature does not check out is no longer retried against Trusteed's signing keys on every checkout: after one failed re-download the result is remembered for 60 seconds (WordPress transient), per server and per signing key (`kid`) of the snapshot, so a legitimate key rotation is not mistaken for tampering just because another attempt failed a moment earlier. If the transient cannot be written, the plugin retries as before.
* Security fix: the signed `validUntil` of the rule snapshot is now checked. A snapshot that is already expired (for example an old, validly signed one replayed by an attacker on the network) counts as "no snapshot that checks out"; a cached copy that outlived its own `validUntil` is not used. A snapshot without `validUntil`, or with one the plugin cannot read, is treated the same way (see the next entry).
* Security fix: a snapshot response larger than 4 MiB is refused before it is verified, and a signed token or snapshot whose header carries a `crit` field (RFC 7515) is rejected, because the plugin understands no critical extensions.
* Security fix: a rule snapshot signed by Trusteed always carries its expiry date (`validUntil`, ISO-8601 with time zone). One that arrives without it, or with one the plugin cannot read (a number, a relative date such as "tomorrow", an impossible date), now counts as "no snapshot that checks out", exactly like an expired one: it is never reported as tampered, because its signature did verify, and neither a freshly served copy nor a cached one is used. Before, a missing date meant the copy never expired. The agent-token check already rejected a `crit` header (RFC 7515).

= 2.3.5 =
**Error messages from Trusteed now reach you as sentences, not codes.**

* Fixed: when Trusteed rejected a request, the plugin showed you its machine code instead of the explanation that came with it. You saw "API error 409: wrong_deployment" where the server had actually sent a sentence telling you what happened and what to do about it. The sentence now wins; the code is still available to the plugin.

= 2.3.4 =
**When a readiness check can't run, the panel now says what would unlock it.**

* New: a check that could not run now says why in one of four groups (nothing to do, needs configuration, waiting for data, or one of our own checks failed) instead of one flat list of unexplained grays.
* New: the panel now shows which of our servers answered your request, a short opaque label. Useful when comparing what you see here with what support sees; it never reveals a hostname or service name.

= 2.3.3 =
**Settings now lets you choose which tools your store serves to agents.**

* New: Settings now lets you choose which tools your store serves to agents. If you never saved a list, the panel tells you that what you serve is the basic set the platform ships with, not a choice of yours.
* New: a button to re-run the readiness check without waiting for the daily sweep, and the panel remembers what changed since the previous run.
* Changed: our own outages no longer count as your store's mismatches. The panel keeps them separate, because there is nothing you can do about them.

= 2.3.2 =
**The agent readiness page had its markup but no stylesheet, and the diagnosis could come back in the wrong language.**

* Fixed: the agent readiness page shipped without its stylesheet, so the panel rendered unstyled.
* Fixed: the panel could show its shell in one language and the diagnosis in another. The resolved language now travels with the texts instead of being detected twice.
* New: findings now carry a link to where each one is fixed, and the merchant's own claims (delivery promise and the rest) appear with the backing each one has.
* Changed: a store with no run yet reads as "checking" instead of "checked once a day": opening the panel already triggers the first run in the background.

= 2.3.1 =
**Critical: 2.3.0 fataled the WordPress admin. Upgrade immediately.**

* Critical fix: 2.3.0 shipped a PHP parse error in the admin router, so activating it broke the entire WordPress admin, not only the Trusteed pages. Every PHP file in the plugin is now syntax-checked before release.
* Fixed: the Agent Readiness page rendered the Trust Center instead. The SPA mount validates the section against an allowlist, and `agent-readiness` had never been added to it, so it fell back silently.

= 2.3.0 =
**The agent readiness dashboard arrives in the admin panel, and the diagnosis stops erring in your favour.**

* New: the agent readiness dashboard ships in the admin panel. It contrasts what your store advertises against what it actually answers, in 16 checks, and shows all sixteen, not only the failures. A check that could not run says why (store not connected, no delivered orders yet, nothing to compare this time) instead of leaving a gap that reads like a fault.
* Fixed: the diagnosis was written in Spanish inside the API and shown verbatim, so a merchant using the panel in English read English headings above Spanish findings. The checks now emit language-neutral codes and the text is composed when served, in the language you are using.
* Fixed: check C1 ("you advertise tools your store does not serve") counted the full public catalogue as served when no tool list was configured, reporting 46 of 48 answering when the server actually serves 12. It failed in the flattering direction, which is the one this panel exists to catch.
* Fixed: check C6 ("you advertise as available something that is switched off") reported a capability as off whenever its flag was unset, including flags that are on by default. It was a false alarm on every store.

= 2.2.2 =
**Credential-egress hardening, complete uninstall cleanup, and documentation corrected to match the code.**

* Security fix: the API client tolerated a private-range or loopback API base URL (`10.*`, `172.16–31.*`, `192.168.*`, `localhost`, `127.*`) in **every** environment, so an install whose API URL had been redirected would send its `X-AgenticMCP-Key` credential to an internal address over plain HTTP. That dev override is now opt-in, off by default, and enabled only via `TRUSTEED_ALLOW_LOCAL_API_BASE` or a `local` WordPress environment type, matching the `WP_DEBUG` gate the token broker already applied.
* Security fix: cloud instance-metadata and IPv6 internal addresses are now blocked outright, in every environment including the dev override: `169.254.0.0/16` (IMDS), Alibaba `100.100.100.200`, `metadata.google.internal`, IPv6 unique-local `fc00::/7`, and link-local `fe80::/10`. Previously, enabling local development re-opened all of these.
* Fixed: IPv6 API hosts were never matched at all. `parse_url()` returns them bracketed (`[::1]`), so the loopback check silently failed.
* Privacy fix: uninstalling left 21 option rows behind, including three encrypted secrets (`trusteed_embed_wp_secret`, `trusteed_enforcement_hmac_secret`, `trusteed_woo_webhook_secret`) and the legacy `amcp_*` aliases that the option accessor still reads as a fallback, so a reinstall could resurrect a stale secret. `uninstall.php` now clears all three option namespaces (`agenticmcp_*`, `trusteed_*`, `amcp_*`) plus the cached snapshot and JWKS transients, as PRIVACY_POLICY.md promises. A test now scans the source for every writable option key and fails if the uninstall list falls behind.
* Docs fix: the deactivation FAQ claimed deactivating disconnects the store immediately and that "no residual data remains on our servers". Neither was true. Deactivation is inert; disconnecting expires the API key and marks the store inactive but retains the store record and previously synced products. The FAQ now describes what actually happens and points to the deletion-request route.
* Docs fix: the catalog was described as syncing "variants and reviews". Neither is sent, only the parent product of a variable product, and no reviews or ratings. The transmitted field list is now exact.
* Compatibility fix: `Tested up to` / `WC tested up to` disagreed between readme.txt (6.9 / 10.6) and the plugin header (6.7 / 9.5), and neither figure had actually been verified against those versions. Verified by installing WordPress 7.0.4 + WooCommerce 11.0.1 on PHP 8.2 and activating the plugin: clean activation, no fatal errors, `init` runs correctly. `Tested up to` / `WC tested up to` now read 7.0 / 11.0 in both files, matching what was actually run. `.wp-env.json`'s local dev pin updated to the same versions.
* Docs fix: corrected links that returned 404: `trusteed.xyz/developers`, `/privacy` and `/terms` need the `/en/` language prefix, and `/support` does not exist (replaced with the contact form and GitHub issues).

= 2.2.1 =
**Admin panel fixes: prompt-injection delimiter leak, R047 form, empty state, unminified bundle.**

* Fixed: `browse_categories` fed the same delimiter-wrapped string to both the machine-readable and narrated response channels, so a category name surfaced with `<<<MERCHANT_CONTENT_START>>>` / `<<<MERCHANT_CONTENT_END>>>` markers in the machine channel. Now unwrapped there; the delimiters stay only in the narration, where they signal "this is merchant data" to the agent.
* Fixed: rule R047 (minimum contribution amount) had no form field in the admin panel; its parameters existed in the schema but could only be set via the API.
* Fixed: `MerchantCheckoutConfig` had translated copy for an empty state that was never rendered, so a merchant with no configured payment rails saw an unexplained empty list.
* Fixed: the admin panel bundle shipped unminified (869 KB / 25,064 lines instead of the 490 KB / 41 lines the documented build command produces). Rebuilt from source.

= 2.2.0 =
**Agent token hardening + rule parameter fix.**

* Security fix: the agent token verifier treated `exp` and `iat` as optional: both guards hung off `> 0`, so a token that simply omitted the claim skipped the check entirely. Without `exp` it never expired; without `iat` it had no maximum age. Both are now mandatory, and a non-numeric value is rejected rather than cast.
* Security fix: an `iat` in the future is now rejected (30s clock skew tolerated). Combined with the max-age window it gave a sliding lifetime, so the token effectively never grew old.
* Fixed: rule R036 (max line-item value) read its cap from `maxCents`, copied from R035. The canonical parameter is `maxCentsPerLine`, the only key the merchant panel accepts, so a merchant-configured cap never reached the check.
* Fixed: the cross-language conformance test resolved its fixture through a development-only path and failed in the published repository.
* Trust receipts: admin SPA bundle rebuilt with the receipt ZIP download button, which states plainly that the export is proof of agent integrity, not dispute evidence.

= 2.1.0 =
**Rebrand + admin panel fix.**

* Internal classes, option keys, and REST routes renamed `Amcp_`/`amcp_` to `Trusteed_`/`trusteed_`. Back-compat preserved: existing installs keep working (legacy options are still read as a fallback, legacy REST namespaces stay registered, legacy encrypted values still decrypt).
* Fixed: R043 HITL payload is now plumbed through end-to-end, so a BLOCK can surface a human-in-the-loop freeze instead of a hard block that loses buyer intent.
* Fixed (critical): the compiled admin SPA bundle was missing from the distributed package entirely; the Trusteed admin panel rendered a "bundle not compiled" error on every install.
* Hardening in billing webhooks, checkout enforcer, catalog sync, and cart signals.

= 2.0.2 =
**Checkout enforcement fix (Shopify App Store review remediation, 2026-07-11/12).** Closes an agent-gating gap surfaced by cross-platform verification after an unrelated Shopify App Store suspension.

* Fixed: checkout enforcement was skipped entirely for organic (human, non-agent) checkouts. Merchant rules such as maximum order amount, blocked countries, and business-hours restrictions never ran unless an agent token was present. These rules now apply to every checkout regardless of agent presence.
* Added: an offline safety-valve evaluator that enforces the same universal merchant rules locally when the remote rules-evaluation API is unreachable, instead of only falling back to a blanket allow/block policy.

= 2.0.1 =
**Critical activation and security hotfix (Codex audit, 2026-06-11).** Closes 9 audit findings and 2 adjacent HIGH issues surfaced in code review.

**Critical (the plugin did not activate in 2.0.0):**
* Fixed a half-finished `AGENTICMCP_*` → `TRUSTEED_*` rename that left `class-plugin.php` referencing an undefined class (`AgenticMCP_Plugin`), undefined constants (`AGENTICMCP_PLUGIN_DIR/URL`, `AGENTICMCP_API_BASE`, `AGENTICMCP_VERSION`) and non-existent classes (`AgenticMCP_Api_Client/Cart_Bridge/Settings`). Activation failed with a fatal "class/constant not found". The plugin now boots cleanly.
* Fixed stale `AgenticMCP_Cart_Bridge` references in the checkout enforcer and multi-add handler (the class was renamed to `Trusteed_Cart_Bridge`). They caused a fatal error on the checkout page.
* Fixed the admin SPA loader referencing the undefined `AGENTICMCP_API_BASE` constant. On PHP 8 that was a fatal on every admin page load.

**Security:**
* `create_cart` now forwards the agent's JWS token (`amcp_agent_token`) through to the checkout URL, so signature/replay verification (R002) actually runs on the normal flow (previously the token was silently dropped).
* The REST API client now validates the API base URL against an exact host allowlist before sending the bearer key. A tampered `amcp_api_base_url` can no longer exfiltrate credentials to an arbitrary HTTPS host.

**Hygiene:**
* `build-zip.sh` now references the real entrypoint (`trusteed-for-woocommerce.php`).
* Removed an orphaned, unmounted admin SPA component with an incompatible auth contract.
* Replaced decorative emoji icons in the admin SPA with plain text for a denser, B2B-consistent UI.

**Server-side companions (backend, no plugin change required):** WooCommerce order/inventory webhooks now capture the raw request body correctly (HMAC no longer 500s on valid signatures). Inventory webhooks validate HMAC with a per-store secret (fail-closed) instead of a shared global. Onboarding no longer reports success when credential/webhook persistence fails.

= 2.0.0 =
**Security & reliability sprint (Spec-WCGAPS, 2026-05-24).** Closes 10 audit gaps, 6 HIGH and 11 MEDIUM findings from the upstream Codex, security-reviewer and code-reviewer review.

**Backward-incompatible (requires re-Connect after upgrade):**
* Onboarding now provisions and persists `enforcement_installation_id`, `enforcement_hmac_secret`, and `woo_webhook_secret` returned by the backend. Plugins upgraded from 1.x without re-Connect will show an admin notice prompting the merchant to reconnect.
* `/plugin/disconnect` is now a 2-phase flow with confirmation token + 5min TTL (closes DoS by stolen API key).
* Reconnect requires domain-ownership proof via `/.well-known/amcp-verify.txt` challenge (closes cross-merchant takeover).

**New (additive):**
* `create_cart` MCP tool now POSTs to the real WP cart bridge endpoint instead of returning a generic `/cart` URL, which enables agent_id + agent_token forwarding for enforcement.
* New `POST /plugin/catalog/delete` handler that soft-deletes products marked for deletion in WP admin.
* Agent-event webhook now retries on 5xx with exponential backoff (60s / 120s / 240s, max 3) via WP-Cron, so failures are no longer silent.
* Currency and tags are now ingested from real WC values (was hardcoded USD + empty tags).
* Bilingual admin notices (EN + ES) guide merchants through reconnect + missing-HMAC scenarios.
* SSRF hardening: store URL is validated against IPv4 RFC1918 + IPv6 ULA/link-local + cloud IMDS endpoints.

**Fail-closed defaults:**
* The legacy `s=dev-bypass` HMAC placeholder is no longer emitted. The plugin now returns `null` and skips dispatch when the enforcement secret is empty, with an admin notice.
* Catalog sync now records `last_sync_attempt` and `last_sync_success` separately, so merchants can tell a green "tried" from a green "succeeded".

**Hygiene:**
* Default `AGENTICMCP_API_BASE` consolidated to `https://api.trusteed.xyz` across the plugin (removes Railway URL drift).
* Versioned encryption prefix (`amcp_enc:`): legacy plaintext options decrypt transparently and re-encrypt on next save.
* WP-Cron retry payload validated against 7-step shape (merchantId, installationId, allowed event kinds, body size cap 64 KiB), which prevents event injection from DB-write attackers.

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

= 2.5.2 =
When the store's rules block an order placed by an agent, the plugin now tells Trusteed why, so the agent can react. Also recognises browser agents that sign their requests (RFC 9421). Update the server-side integration together with the plugin for the full reason.

= 2.5.0 =
Agent orders are only marked as paid once your own payment provider (Stripe or PayPal) confirms the charge. Unconfirmed ones stay On hold for review.

= 2.0.0 =
**SECURITY UPGRADE: re-Connect required.** Closes 17 findings (3 HIGH + 14 MEDIUM/LOW) including SSRF, cross-merchant takeover, disconnect DoS, and silent enforcement bypass. After upgrade, click "Reconnect" once to receive fresh enforcement credentials.

= 1.0.0 =
Initial release: connect your store to AI agents via MCP.
