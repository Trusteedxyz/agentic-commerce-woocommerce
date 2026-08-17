# Privacy Policy — Trusteed for WooCommerce

**Last updated:** 2026-08-17
**Plugin version:** 2.2.2

## What Data Is Sent to Trusteed

When you install and configure this plugin, the following product catalog data is transmitted to the Trusteed SaaS platform:

- Product titles
- Product prices (regular and sale prices)
- Product descriptions (full and short)
- Product images (URLs and alt text)
- Product categories (names and IDs)
- Stock status and stock quantity
- SKU (Stock Keeping Unit) identifiers
- Product permalinks (URLs on your WooCommerce store)

This data is sent during:

- Initial catalog synchronization
- Automatic sync on product create, update, or delete
- Automatic sync on stock quantity changes
- Manual sync triggered from the plugin settings page

## What Data Is NOT Sent

The following data is **never** transmitted to Trusteed:

- **Customer data**: names, email addresses, physical addresses, phone numbers
- **Order data**: order history, order details, order statuses
- **Payment data**: credit card numbers, payment tokens, billing information
- **Session data**: buyer browsing sessions, cookies, login credentials
- **WordPress user data**: admin usernames, passwords, user roles

## Where Data Is Stored

Product catalog data is stored on Trusteed servers hosted on Railway (US-East region). Data is encrypted in transit (TLS 1.2+) and at rest.

## Data Retention

- Product catalog data is maintained on Trusteed servers for as long as the plugin is installed and active on your WooCommerce store.
- Uninstalling the plugin removes every option row it created from your WordPress database, including all stored secrets. It does not by itself erase the catalog data already held on Trusteed servers.
- Disconnecting your store from the plugin settings page expires your API key, marks the store inactive so agents can no longer reach it, and deletes the stored WooCommerce webhook secret. Your store record and previously synced products are **retained** (products are marked unavailable rather than deleted) so that reconnecting restores the store without a full re-sync.
- To have that data erased, request deletion by contacting the DPO at the address below. Data covered by a deletion request is permanently removed within **30 days**.

## Third-Party Access

Product catalog data stored on Trusteed may be queried by AI agents (such as Claude, ChatGPT, and other MCP-compatible agents) through the MCP (Model Context Protocol) interface when they search for products on behalf of buyers.

Your catalog data is **never sold** to third parties. Access is limited to:

- AI agents performing product search and cart creation via MCP
- Trusteed internal systems for catalog indexing and serving

## Legal Basis for Processing

The legal basis for processing your product catalog data is **consent** under GDPR Article 6(1)(a). By installing the plugin, entering your API key, and triggering the catalog synchronization, you explicitly consent to the transmission and processing of your product catalog data as described in this policy.

## Data Protection Officer (DPO)

For any privacy-related inquiries, contact our DPO:

**Email:** privacy@trusteed.xyz

## Your Rights

As a merchant using this plugin, you have the right to:

- **Access**: Request a copy of the catalog data stored about your store
- **Rectification**: Update your catalog data by syncing from WooCommerce
- **Erasure**: Request complete deletion of your data by disconnecting your store via the plugin settings page or by emailing the DPO
- **Restriction**: Deactivate the plugin to stop all data transmission while retaining your configuration
- **Portability**: Request your catalog data in a machine-readable format
- **Objection**: Object to the processing of your data by contacting the DPO

To stop agents from reaching your store, use the plugin settings page to disconnect it. To have stored data erased, send an email to privacy@trusteed.xyz — disconnecting alone does not delete it.

## Disclaimer

This privacy policy applies to the Trusteed for WooCommerce plugin. For the complete Trusteed privacy policy, visit https://trusteed.xyz/en/privacy
