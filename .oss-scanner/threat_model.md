# Threat model

## What this project does and where untrusted input enters
A WordPress plugin ("Trusteed Agentic Commerce for WooCommerce", PHP, v2.4.x) that connects a WooCommerce store to AI
shopping agents through the Trusteed service. It syncs the public product catalog to the service, lets an agent create a
cart that hands the shopper over to the native WooCommerce checkout, evaluates enforcement rules on checkout, sends signed
agent events to the service, and (opt-in, off by default) lets the Trusteed service create and confirm orders for agents
through signed REST calls. It does NOT process payments: the `trusteed_agent` payment gateway is only a label on orders
(`is_available()` is false, `process_payment()` always returns failure); money is collected by Trusteed outside
WooCommerce and the plugin only records the confirmed payment reference.

Untrusted input:
- REST routes (namespace `trusteed/v1`, legacy alias `agenticmcp/v1` for some):
  - `POST /cart` (header `X-AgenticMCP-Key`, `hash_equals` against the stored key) and `POST /billing-webhook`
    (`X-AgenticMCP-Signature`, HMAC-SHA256 of the body with the webhook secret). Both pre-date 2.4.
  - `/embed/token` (token broker): `manage_woocommerce` capability plus `X-WP-Nonce` (`wp_rest`).
  - NEW in 2.4 (`class-agent-orders-endpoint.php`, only registered when option `trusteed_agent_orders_enabled` is `yes`;
    otherwise no route exists, 404): `POST /quote`, `POST /orders`, `POST /orders/{id}/paid`. No WordPress user: the
    `permission_callback` is `Trusteed_Agent_Request_Auth` — header `X-Trusteed-Signature: t=<unix>,v1=<hex>`,
    `v1 = HMAC-SHA256(installation secret, "<t>.<raw body>")`, `hash_equals`, +-300 s clock window, then a transient
    anti-replay entry keyed by `v1` (600 s). Missing secret/header, bad format, stale `t` or reused signature ⇒ 401 with
    no reason. They change state: `/orders` creates a `pending` WC order (idempotent by `trusteed_order_id`, total must
    equal `expected_total_cents` or the order is deleted with 409, then local enforcement rules run and a non-ALLOW
    deletes it); `/orders/{id}/paid` marks the order paid and reduces stock only for orders carrying
    `_trusteed_order_id`, with matching amount/currency (mismatch ⇒ put on hold). `/quote` writes nothing persistent.
  - NEW in 2.4.1: `GET /.well-known/amcp-verify.txt`, served at `init` priority 0 by `class-domain-challenge.php`. It
    returns a 32-128 hex token held in a transient (<= 600 s, deleted right after the redemption call), only while a
    store reconnection is in progress; otherwise WordPress handles the URL normally. No file is written.
- The agent token (a JWS, EdDSA) that an agent passes into checkout, verified in `class-token-verifier.php`; the DID and
  origin marker query parameters (`trusteed_agent_id`, `trusteed_agent_token`, `trusteed_origin`) are declarations, not
  proof (`class-agent-identity.php`): only a valid token of the same DID becomes order attribution.
- Request data on checkout and cart (cart attributes, cart signals, multi-add payloads).
- `wp_ajax_*` handlers used by the settings screens (capability `manage_woocommerce` and nonce checks apply).
- Responses from the Trusteed API: catalog sync, JWKS, and the enforcement snapshot (a JWS; payloads over 50 KB are raw
  DEFLATE, `zip: DEF`). The plugin must treat them as data and never inflate or parse before the signature verifies.
- Merchant-supplied settings (API base URL, secrets) and the one-time disconnect confirmation token.

## Components that matter most / least
Most important:
- Authentication of every REST route: `class-agent-orders-endpoint.php` + `class-agent-request-auth.php` (new, state
  changing, no WP session), `class-cart-bridge.php`, `class-billing-webhooks.php`, `admin/class-token-broker.php`. A route
  reachable without the intended credential is high or critical.
- `class-agent-woo-adapter.php`: builds quotes/orders with WooCommerce; whatever it trusts from the request must stay
  bounded by the signed channel (`agent_did` is trusted only because the body is signed).
- Token verification and replay protection: `class-token-verifier.php` (signature, mandatory `exp`/`iat`, max age,
  `iss`/`kid`, audience, nonce), `class-agent-identity.php` (binding of declared DID to a verified token, REST meta guard
  that stops `meta_data` writes to the `_trusteed_agent_*` attribution keys).
- Checkout enforcement: `class-checkout-enforcer.php`, `class-offline-safety-valve-evaluator.php`,
  `class-r043-hitl-gate.php`, `class-enforcement-failure-policy.php`. Intended policy: an agentic checkout with an
  invalid signature, a missing snapshot with a token, an INDETERMINATE token in `enforce` mode, or an internal error fails
  CLOSED; a human checkout is never blocked by a Trusteed outage (only by the merchant's own offline rules). A way to
  bypass a block, or to make a human checkout fail because of an outage, is high.
- Snapshot trust chain: `class-snapshot-client-woo.php` (EdDSA verify against the JWKS, `crit` rejected, size cap, signed
  `validUntil` anti-rollback, negative cache) and `class-snapshot-payload-decoder.php` (inflate only after verification,
  2 MiB output cap, incremental `inflate_add`; unknown `zip`, corrupt stream, over-cap or non-object JSON ⇒ unusable).
- Outbound requests that carry credentials: `class-api-client.php` (HTTPS + exact host allowlist, link-local/metadata
  denied), `class-enforcement-api-client.php` (HMAC signing), `class-disconnect.php` and `uninstall.php` (two-phase
  disconnect and best-effort deregistration).
- Secret handling: `class-crypto-helper.php`, `class-trusteed-options.php`, `uninstall.php` (option cleanup list).
- Standard WordPress hygiene in every handler: capability checks, nonces, sanitization, escaping, prepared SQL.

Less important / out of scope:
- `assets/admin-spa/admin-spa.js` is a minified build artifact; report only flaws reachable through the plugin.
- `assets/screenshots/`, `languages/`, `docs/`, README files.
- `tests/` (including Playwright E2E) is test code.
- WordPress core and WooCommerce themselves, and the Trusteed service on the other side of the API.

## How to exercise it
- `vendor/bin/phpunit` runs `tests/unit/` against WordPress stubs (`tests/unit/bootstrap.php` defines `ABSPATH` and loads
  `tests/unit/wp-stubs.php`; each test `require_once`s the classes it needs). The image has PHP 8.3 and Composer but no
  WordPress, WooCommerce or database. Needs `ext-sodium` (EdDSA tests skip themselves without it) and `ext-zlib`
  (`inflate_init`, used by the snapshot decoder); both ship with `php:8.3-cli-bookworm`.
- Stubs give a fake clock (`$GLOBALS['__amcp_clock']`), in-memory transients and a scripted `wp_remote_get`; use them to
  drive signature windows, replay and expiry. Tests that document security properties:
  `AgentRequestAuthTest`, `AgentOrdersEndpointTest` (fake Woo port in `AgentOrdersFakes.php`), `AgentGatewayTest`,
  `DomainChallengeTest`, `DisconnectTwoPhaseTest`, `UninstallNotifiesBackendTest`, `SnapshotClientHardeningTest`,
  `CompressedSnapshotFlowTest`, `EnforcementFailurePolicyConformanceTest` (fixture-driven),
  `HumanCheckoutNotBlockedByOutageTest`, `AgentAttributionRequiresVerifiedTokenTest`, `OfflineSafetyValveConformanceTest`
  (`tests/fixtures/offline-safety-valve-conformance.json`).
- `class-agent-woo-adapter.php` is not unit-tested (needs real WooCommerce); reason from the code.
- The scanner has no Internet access, so calls to the Trusteed API cannot be completed. Use mocks.

## How you rate severity
- Critical: unauthenticated or low-privilege access to a state-changing route; forging or replaying a signed
  `X-Trusteed-Signature` request so that `/orders` creates an order, or `/orders/{id}/paid` marks one paid, without a valid
  fresh signature (including bypassing the +-300 s window or the replay check); marking an order paid with a different
  amount/currency/reference; touching an order that does not carry `_trusteed_order_id`; making the `trusteed_agent`
  gateway actually take or fake a payment at checkout; bypass of enforcement that lets an agent order that should be
  blocked; forgery or replay of an agent token that is accepted; accepting an unsigned or tampered rule snapshot;
  remote code execution.
- High: SSRF through the API base URL or any other URL the plugin fetches; leaking the enforcement secret, webhook
  secret or API key; the domain-challenge token served outside an active reconnection or after its TTL, or any
  disclosure of it that lets a third party redeem credentials; decompression bomb or unbounded memory/CPU from a snapshot
  or other API response; a human checkout blocked by an outage; SQL injection; stored or reflected XSS in an admin or
  storefront page; CSRF on a settings action.
- Medium: missing capability or nonce check with limited impact; denial of service by a small unauthenticated request;
  timing differences in a secret comparison; information disclosure of non-secret data; reason leakage in the 401 of the
  signed routes.
- Low: hardening gaps with no demonstrated path to impact.
- Post-authentication issues that need a WordPress administrator are medium at most, because an administrator
  already controls the site.

## Anything to leave alone
- The documented dev opt-in `TRUSTEED_ALLOW_LOCAL_API_BASE` (and `WP_ENVIRONMENT_TYPE=local`): loopback and RFC1918 API
  base. It is off by default and never re-opens link-local/cloud-metadata hosts.
- The agent-orders feature being off by default, and `agent_orders_enabled` having no UI toggle in the PHP code.
- The `trusteed_agent` gateway appearing in the WooCommerce gateway list; it is never available at checkout.
- The `/.well-known/amcp-verify.txt` token being readable by anyone during an active reconnection: that is how Trusteed
  fetches it. Report only exposure outside that window, or redemption that does not need the store's own credentials.
- INDETERMINATE token results in `observe` mode failing open with telemetry (documented), and `failure_mode` only
  governing agentic checkouts.
- Findings that only exist if WordPress or the server is already compromised.
- Legal-status wording about eIDAS or ESIGN in the README is not a security finding.
