# Threat model

## What this project does and where untrusted input enters
A WordPress plugin ("Trusteed Agentic Commerce for WooCommerce", PHP) that connects a WooCommerce store to AI shopping
agents through the Trusteed service. It syncs the public product catalog to the service, lets an agent create a cart
that hands the shopper over to the native WooCommerce checkout, evaluates enforcement rules on checkout, and sends
signed agent events to the service. It never processes payments.

Untrusted input:
- REST routes under `trusteed/v1` (and a legacy namespace): `/cart` (API-key protected), `/billing-webhook`
  (HMAC-signed by the service), `/embed/token` (admin only).
- The agent token (a JWS, EdDSA) that an agent passes into checkout, verified in `class-token-verifier.php`.
- Request data on checkout and cart (cart attributes, cart signals, multi-add payloads).
- `wp_ajax_*` handlers used by the settings screens (capability and nonce checks apply).
- Responses from the Trusteed API (catalog sync, enforcement snapshot): the plugin must treat them as data.
- Merchant-supplied settings (API base URL, secrets).

## Components that matter most / least
Most important:
- Authentication of the REST routes: `class-cart-bridge.php`, `class-billing-webhooks.php`,
  `admin/class-token-broker.php`. A route reachable without the intended credential is high or critical.
- Token verification and replay protection: `class-token-verifier.php` (signature, `jti`, expiry, key resolution).
- Checkout enforcement: `class-checkout-enforcer.php`, `class-offline-safety-valve-evaluator.php`,
  `class-r043-hitl-gate.php`. The fallback must fail closed where documented; a way to bypass a block is high.
- Outbound requests that carry credentials: `class-api-client.php` (SSRF guard, host allowlist, HTTPS only),
  `class-enforcement-api-client.php` (HMAC signing), `class-snapshot-client-woo.php`.
- Secret handling: `class-crypto-helper.php`, `class-trusteed-options.php`, `uninstall.php`.
- Standard WordPress hygiene in every handler: capability checks, nonces, input sanitization, output escaping,
  prepared SQL.

Less important / out of scope:
- `assets/admin-spa/admin-spa.js` is a minified build artifact of the admin app. Report only if a flaw is
  reachable through the plugin, not in bundled third-party libraries.
- `assets/screenshots/`, `languages/`, `docs/`, README files.
- `tests/` (including Playwright E2E) is test code.
- WordPress core and WooCommerce themselves, and the Trusteed service on the other side of the API.

## How to exercise it
- `vendor/bin/phpunit` runs the unit tests in `tests/unit/` against WordPress stubs (`tests/unit/wp-stubs.php`).
  The image has PHP 8.3 and Composer but no WordPress or database. To test behavior that needs real WordPress or
  WooCommerce, fetch them during the scan only if the environment allows it, and otherwise reason from the code.
- `OfflineSafetyValveConformanceTest` uses `tests/fixtures/offline-safety-valve-conformance.json`.
- The scanner has no Internet access, so calls to the Trusteed API cannot be completed. Use mocks.

## How you rate severity
- Critical: unauthenticated or low-privilege access to a state-changing route; bypass of enforcement that lets an
  agent order that should be blocked; forgery or replay of an agent token that is accepted; remote code execution.
- High: SSRF through the API base URL or any other URL the plugin fetches; leaking the enforcement secret or API key;
  SQL injection; stored or reflected XSS in an admin or storefront page; CSRF on a settings action.
- Medium: missing capability or nonce check with limited impact; denial of service by a small request; timing
  differences in a secret comparison; information disclosure of non-secret data.
- Low: hardening gaps with no demonstrated path to impact.
- Post-authentication issues that need a WordPress administrator are medium at most, because an administrator
  already controls the site.

## Anything to leave alone
- The documented dev opt-in `TRUSTEED_ALLOW_LOCAL_API_BASE` (loopback and RFC1918 API base). It is off by default.
- Findings that only exist if WordPress or the server is already compromised.
- Legal-status wording about eIDAS or ESIGN in the README is not a security finding.
