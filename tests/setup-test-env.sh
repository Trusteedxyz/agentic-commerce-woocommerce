#!/usr/bin/env bash
set -euo pipefail

echo "=== Setting up WooCommerce test environment ==="

# Wait for WordPress to be ready
sleep 5

# Activate WooCommerce and run setup wizard silently
npx wp-env run cli wp wc update

# Create test products via WC-CLI
npx wp-env run cli wp wc product create --name="Test Laptop" --regular_price=999.99 --sku="TEST-LAPTOP-001" --stock_quantity=50 --manage_stock=true --categories='[{"id":0,"name":"Electronics"}]' --user=1
npx wp-env run cli wp wc product create --name="Test Headphones" --regular_price=49.99 --sku="TEST-HP-001" --stock_quantity=100 --manage_stock=true --user=1
npx wp-env run cli wp wc product create --name="Test Book" --regular_price=19.99 --sku="TEST-BOOK-001" --stock_quantity=200 --manage_stock=true --user=1

# Set plugin options (simulate a connected store)
npx wp-env run cli wp option update agenticmcp_api_key "sk_test_wp_env_testing_key_12345"
npx wp-env run cli wp option update agenticmcp_store_slug "test-wp-store"
npx wp-env run cli wp option update agenticmcp_mcp_endpoint "https://agenticmcpstores-production.up.railway.app/test-wp-store/mcp"
npx wp-env run cli wp option update agenticmcp_tier "FREE"
npx wp-env run cli wp option update agenticmcp_webhook_secret "whsec_test_secret_for_e2e"

# Flush rewrite rules (needed for REST API endpoints)
npx wp-env run cli wp rewrite flush

# Verify plugin is active
npx wp-env run cli wp plugin list --status=active --format=table

echo "=== Test environment ready ==="
echo "WordPress: http://localhost:8888"
echo "Admin: http://localhost:8888/wp-admin (admin/password)"
