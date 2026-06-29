# AgenticMCPStores for WooCommerce — Merchant Installation Guide

A step-by-step guide to connect your WooCommerce store to AI shopping agents.

---

## Table of Contents

1. [Prerequisites](#1-prerequisites)
2. [Installation](#2-installation)
3. [Get Your API Key](#3-get-your-api-key)
4. [Configure the Plugin](#4-configure-the-plugin)
5. [First Catalog Sync](#5-first-catalog-sync)
6. [Verify Everything Works](#6-verify-everything-works)
7. [Troubleshooting](#7-troubleshooting)
8. [Support](#8-support)

---

## 1. Prerequisites

Before installing, make sure your site meets these requirements:

| Requirement     | Minimum Version                  |
| --------------- | -------------------------------- |
| WordPress       | 6.8 or later                     |
| WooCommerce     | 8.0 or later                     |
| PHP             | 7.4 or later                     |
| SSL Certificate | Required (HTTPS must be enabled) |

You will also need:

- **Admin access** to your WordPress dashboard
- **At least one published product** in WooCommerce (so the sync has something to send)
- An active internet connection from your server to `api.trusteed.xyz`

### How to check your WooCommerce version

1. Go to **WooCommerce > Status** in your WordPress admin menu
2. Look for the "WC Version" field under "WordPress Environment"
3. If your version is below 8.0, update WooCommerce before proceeding

### How to check your PHP version

1. Go to **WooCommerce > Status** in your WordPress admin menu
2. Look for the "PHP Version" field under "Server Environment"
3. If your version is below 7.4, contact your hosting provider to upgrade

---

## 2. Installation

### Option A: Install from the Woo Marketplace (recommended)

1. Log in to your WordPress admin dashboard
2. Go to **Plugins > Add New**
3. Search for **"AgenticMCPStores for WooCommerce"**
4. Click **Install Now** on the plugin card
5. Once installed, click **Activate**

### Option B: Manual upload

1. Download the `agenticmcpstores-for-woocommerce.zip` file from [trusteed.xyz/downloads](https://trusteed.xyz/downloads)
2. In your WordPress admin, go to **Plugins > Add New > Upload Plugin**
3. Click **Choose File**, select the downloaded `.zip` file
4. Click **Install Now**
5. Once installed, click **Activate**

After activation, you will see a new menu item: **WooCommerce > AgenticMCPStores**.

---

## 3. Get Your API Key

You need a free API key to connect your store.

1. Go to [trusteed.xyz/developers](https://trusteed.xyz/developers)
2. Click **"Get Free API Key"**
3. Enter your email address and store URL
4. Check your inbox for the confirmation email and verify your account
5. Copy the API key from your dashboard (it starts with `amcs_`)

> **Keep your API key private.** Do not share it publicly or commit it to version control. If you suspect your key has been compromised, regenerate it immediately from your AgenticMCPStores dashboard.

---

## 4. Configure the Plugin

1. In your WordPress admin, go to **WooCommerce > AgenticMCPStores**
2. Paste your API key into the **"API Key"** field

   <!-- Screenshot reference: settings-api-key-field.png -->

3. Click **"Save & Connect"**
4. Wait a few seconds. You should see a **green "Connected" badge** appear next to the connection status

   <!-- Screenshot reference: settings-connected-badge.png -->

If you see a red "Connection Failed" message instead, check the [Troubleshooting](#7-troubleshooting) section below.

---

## 5. First Catalog Sync

After connecting, your product catalog syncs automatically. Here is what happens:

1. The plugin scans all your **published** WooCommerce products
2. It sends catalog data (titles, descriptions, prices, images, categories, stock status) to AgenticMCPStores via HTTPS
3. The settings page shows a progress indicator during the initial sync
4. Once complete, you will see the total number of synced products

**What gets synced:**

- Product titles, descriptions, and short descriptions
- Prices (regular and sale prices)
- Product images (URLs only, images are not uploaded)
- Categories and tags
- Stock status and quantity
- Product variations (sizes, colors, etc.)

**What does NOT get synced:**

- Customer data (names, emails, addresses)
- Order history
- Payment information
- Private or draft products

### Manual sync

If you need to force a full re-sync (for example, after a bulk product import):

1. Go to **WooCommerce > AgenticMCPStores**
2. Scroll down to the **"Catalog Sync"** section
3. Click **"Sync Now"**
4. Wait for the progress indicator to complete

---

## 6. Verify Everything Works

After the initial sync, verify that your store is properly connected:

### Check 1: Connection status

Go to **WooCommerce > AgenticMCPStores**. You should see:

- A **green "Connected" badge**
- Your store name and slug displayed
- The number of synced products matching your published product count

### Check 2: Product count matches

Compare the "Synced Products" count on the settings page with your actual published products:

1. Go to **Products > All Products**
2. Note the count of "Published" products
3. This number should match the synced count on the AgenticMCPStores settings page

### Check 3: Test with an AI agent

The quickest way to verify end-to-end functionality:

1. Go to [claude.ai](https://claude.ai) (or any MCP-compatible agent)
2. Ask the agent: _"Search for products on [your-store-slug] at AgenticMCPStores"_
3. The agent should return products from your catalog
4. If you have the demo store configured, you can test at `https://trusteed.xyz/demo-store`

### Check 4: Automatic sync verification

1. Go to **Products** in WooCommerce and edit any product
2. Change the price and click **Update**
3. Go back to **WooCommerce > AgenticMCPStores**
4. The "Last Sync" timestamp should show the current time, confirming the hook fired

---

## 7. Troubleshooting

### Problem 1: "Connection Failed" after entering API key

**Cause:** The API key is invalid or your server cannot reach the AgenticMCPStores API.

**Solution:**

- Double-check that you copied the full API key (it starts with `amcs_`)
- Make sure there are no extra spaces before or after the key
- Verify your server can make outbound HTTPS requests (some hosts block external API calls)
- Contact your hosting provider if outbound HTTPS is blocked

### Problem 2: Product count is zero after sync

**Cause:** No published products found, or the sync failed silently.

**Solution:**

- Confirm you have at least one product with status "Published" (not Draft or Private)
- Click "Sync Now" to trigger a manual full sync
- Check **WooCommerce > Status > Logs** for any error messages from the plugin
- If using a caching plugin, clear your cache and try again

### Problem 3: Products are outdated after editing

**Cause:** WooCommerce hooks did not fire, or a caching layer intercepted the update.

**Solution:**

- Click "Sync Now" on the settings page to force a full re-sync
- If you use an object caching plugin (Redis, Memcached), flush the cache
- If you use a staging/production workflow, ensure the plugin is active on the production site

### Problem 4: Plugin conflicts or white screen

**Cause:** Another plugin or theme is conflicting with AgenticMCPStores.

**Solution:**

- Deactivate all other plugins temporarily
- Activate them one by one to identify the conflict
- If the issue is with a specific plugin, contact support@trusteed.xyz with the plugin name and error details
- Check your PHP error log (`wp-content/debug.log`) for specific error messages

### Problem 5: Slow initial sync for large catalogs

**Cause:** Stores with thousands of products may take several minutes for the first sync.

**Solution:**

- The initial sync processes products in batches. Allow up to 5 minutes for stores with 1,000+ products
- Do not navigate away from the settings page during the initial sync
- If the sync times out, click "Sync Now" to resume where it left off
- For stores with 10,000+ products, consider running the sync during low-traffic hours

---

## 8. Support

If you need help or have questions:

- **Documentation:** [trusteed.xyz/docs](https://trusteed.xyz/docs)
- **Email:** support@trusteed.xyz
- **Response time:** Within 24 hours on business days
- **Free tier support:** Email only
- **Growth/Pro/Enterprise support:** Priority email with guaranteed SLA

When contacting support, please include:

- Your WordPress version (found in **Dashboard > Updates**)
- Your WooCommerce version (found in **WooCommerce > Status**)
- Your PHP version (found in **WooCommerce > Status > Server Environment**)
- The error message or screenshot of the issue
- Your store URL

---

_Last updated: 2026-03-28_
