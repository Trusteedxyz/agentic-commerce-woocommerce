# Trusteed for WooCommerce: Merchant Installation Guide

This guide takes you from a fresh WordPress admin to a store that AI shopping agents can search. Follow the sections in order.

---

## Table of Contents

1. [Prerequisites](#1-prerequisites)
2. [Installation](#2-installation)
3. [Choose How to Connect](#3-choose-how-to-connect)
4. [Connect Your Store](#4-connect-your-store)
5. [First Catalog Sync](#5-first-catalog-sync)
6. [Verify Everything Works](#6-verify-everything-works)
7. [Troubleshooting](#7-troubleshooting)
8. [Support](#8-support)

---

## 1. Prerequisites

Check that your site meets these requirements before you install:

| Requirement | Minimum Version                   |
| ----------- | --------------------------------- |
| WordPress   | 6.0 or later                      |
| WooCommerce | 8.0 or later                      |
| PHP         | 7.4 or later                      |
| HTTPS       | Recommended (SSL certificate)     |

You also need:

- Admin access to your WordPress dashboard
- At least one published product in WooCommerce, so the sync has something to send
- A server that can reach `api.trusteed.xyz` over the internet

### How to check your WooCommerce version

1. Go to **WooCommerce > Status** in your WordPress admin menu
2. Find the "WC Version" field under "WordPress Environment"
3. If the version is below 8.0, update WooCommerce before you continue

### How to check your PHP version

1. Go to **WooCommerce > Status** in your WordPress admin menu
2. Find the "PHP Version" field under "Server Environment"
3. If the version is below 7.4, ask your hosting provider to upgrade it

---

## 2. Installation

1. Download [trusteed-agentic-commerce-woocommerce-2.1.0.zip](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases/latest/download/trusteed-agentic-commerce-woocommerce-2.1.0.zip) from the latest GitHub release
2. In your WordPress admin, go to **Plugins > Add New > Upload Plugin**
3. Click **Choose File** and select the `.zip` you downloaded
4. Click **Install Now**
5. When the install finishes, click **Activate**

Once the plugin is active, a new menu item appears at **WooCommerce > Trusteed**.

---

## 3. Choose How to Connect

The connection page offers two ways to link your store to Trusteed. Pick one:

- **Email and password.** Enter your Trusteed email and password. If you don't have an account yet, the plugin creates one for you.
- **API key.** If you'd rather not type your password into WordPress, paste an API key from the [Trusteed dashboard](https://trusteed.xyz/developers).

> **Keep your API key private.** Don't post it anywhere public and don't commit it to version control. If you think it has been exposed, regenerate it from your Trusteed dashboard right away.

---

## 4. Connect Your Store

In this version, the labels on the connection page are in Spanish. The English meaning is in parentheses below.

### Option A: Email and password

1. In your WordPress admin, go to **WooCommerce > Trusteed**
2. In the section **"Conecta tu tienda a Trusteed"** (Connect your store to Trusteed), enter your email in **"Tu correo"** (Your email)
3. Enter your password in **"Contraseña"** (Password). It needs at least 8 characters. If you already have a Trusteed account, use the same password
4. Click **"Conectar mi tienda"** (Connect my store)
5. Wait a few seconds. The plugin shows "¡Tienda conectada con éxito!" (Store connected successfully!)

### Option B: API key

1. Go to **WooCommerce > Trusteed**
2. In the section **"¿Prefieres no usar contraseña?"** (Prefer not to use a password?), paste your key into **"API key"**
3. Click **"Conectar con API key"** (Connect with API key)
4. Wait a few seconds. The plugin shows "¡Tienda conectada con tu API key!" (Store connected with your API key!)

<!-- Screenshot reference: settings-connected-badge.png -->

After either option, the section **"Estado de la conexión"** (Connection status) shows a **"Conectada"** (Connected) badge.

If an error message shows up instead, go to the [Troubleshooting](#7-troubleshooting) section.

---

## 5. First Catalog Sync

Your catalog syncs on its own once the store is connected. Here is what the plugin does:

1. It scans all your **published** WooCommerce products
2. It sends the catalog data to Trusteed over HTTPS, in batches of 100 products
3. When the sync ends, **"Última sincronización"** (Last sync) shows the time of the sync

**What gets synced:**

- Product name, SKU, description and short description
- Current price. When a product is on sale, the regular price also goes along as a compare-at price
- Stock status and quantity
- Product images (the image URL and alt text, the image files stay on your server)
- Categories and tags
- Currency and product type (simple, variable and so on)
- The product's link on your store

Variable products sync as a single product. Their variations are not sent separately.

**What does NOT get synced:**

- Customer data (names, emails, addresses)
- Order history
- Payment information
- Private or draft products

### Manual sync

Force a full re-sync when you need one, for example after a bulk product import:

1. Go to **WooCommerce > Trusteed**
2. In the **"Acciones"** (Actions) section, click **"Sincronizar catálogo ahora"** (Sync catalog now)
3. The button shows "Sincronizando..." (Syncing...) while the sync runs. Wait until it finishes

---

## 6. Verify Everything Works

Run these four checks after the first sync.

### Check 1: Connection status

Go to **WooCommerce > Trusteed**. In **"Estado de la conexión"** you should see:

- A **"Conectada"** badge
- Your active plan ("Plan activo")
- Your MCP endpoint ("Endpoint MCP")
- A timestamp in "Última sincronización" instead of "Nunca" (Never)

### Check 2: Test the connection

1. In the **"Acciones"** section, click **"Probar conexión"** (Test connection)
2. The plugin shows the result and the connection latency in milliseconds

### Check 3: Test with an AI agent

This is the quickest end-to-end test.

1. Open [claude.ai](https://claude.ai) or any other MCP-compatible agent
2. Ask it: _"Search for products on [your-store-slug] at Trusteed"_
3. The agent should list products from your catalog
4. If you have the demo store configured, you can also test at `https://trusteed.xyz/demo-store`

### Check 4: Automatic sync

1. Go to **Products** in WooCommerce and edit any product
2. Change the price and click **Update**
3. Go back to **WooCommerce > Trusteed**
4. The "Última sincronización" timestamp should show the current time. That confirms the product hook fired

---

## 7. Troubleshooting

### Problem 1: An error appears when you connect

**Cause:** The email or password is wrong, the API key is invalid, or your server can't reach the Trusteed API.

**Solution:**

- Retype your email and password, or copy the full API key again
- Remove any space before or after the key
- Confirm that your server can make outbound HTTPS requests, because some hosts block them
- If outbound HTTPS is blocked, ask your hosting provider to open it
- Click **"Probar conexión"** to see whether the plugin can reach Trusteed

### Problem 2: Your products don't show up in the agent after the sync

**Cause:** The plugin found no published products, or the sync failed without a visible error.

**Solution:**

- Confirm that at least one product has the status "Published" (Draft and Private products are skipped)
- Click **"Sincronizar catálogo ahora"** to run a full sync by hand
- Check the PHP error log (`wp-content/debug.log` when debug logging is on) for lines that start with `[amcp.catalog_sync]`
- If you use a caching plugin, clear its cache and try again

### Problem 3: Products are outdated after editing

**Cause:** The WooCommerce hooks didn't fire, or a caching layer intercepted the update.

**Solution:**

- Click **"Sincronizar catálogo ahora"** on the settings page to force a full re-sync
- If you use an object cache (Redis, Memcached), flush it
- If you work with a staging and a production site, confirm that the plugin is active on production

### Problem 4: Plugin conflict or white screen

**Cause:** Another plugin or your theme conflicts with Trusteed.

**Solution:**

- Deactivate all other plugins for now
- Reactivate them one at a time until the problem comes back. The last one you activated is the conflict
- Send that plugin's name and the error details to support@trusteed.xyz
- Read your PHP error log (`wp-content/debug.log`) for the exact error message

### Problem 5: Slow first sync on a large catalog

**Cause:** A store with thousands of products sends many batches, and the first sync can take a while.

**Solution:**

- The first sync sends products in batches of 100
- Stay on the settings page until the sync finishes
- If the sync times out, click **"Sincronizar catálogo ahora"** to run it again. Each run starts from the first product
- For stores with 10,000+ products, run the sync when traffic is low

---

## 8. Support

Need help? Reach us here:

- Documentation: [trusteed.xyz/docs](https://trusteed.xyz/docs)
- Email: support@trusteed.xyz
- GitHub issues: [github.com/Trusteedxyz/agentic-commerce-woocommerce/issues](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/issues)

Include these details in your message:

- Your WordPress version (found in **Dashboard > Updates**)
- Your WooCommerce version (found in **WooCommerce > Status**)
- Your PHP version (found in **WooCommerce > Status > Server Environment**)
- The error message, or a screenshot of the problem
- Your store URL

---

_Last updated: 2026-09-20_
