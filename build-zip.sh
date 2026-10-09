#!/usr/bin/env bash
set -euo pipefail

PLUGIN_DIR="agenticmcpstores-for-woocommerce"
VERSION=$(grep "Version:" trusteed-for-woocommerce.php | head -1 | sed 's/.*Version: *//' | tr -d ' ')
OUTPUT="${PLUGIN_DIR}-${VERSION}.zip"

echo "Building ${OUTPUT}..."

# Create temp directory with clean plugin files
TEMP_DIR=$(mktemp -d)
mkdir -p "${TEMP_DIR}/${PLUGIN_DIR}"

# Copy plugin files (excluding dev/test files)
cp trusteed-for-woocommerce.php "${TEMP_DIR}/${PLUGIN_DIR}/"
cp uninstall.php "${TEMP_DIR}/${PLUGIN_DIR}/"
cp readme.txt "${TEMP_DIR}/${PLUGIN_DIR}/"
cp PRIVACY_POLICY.md "${TEMP_DIR}/${PLUGIN_DIR}/"
cp -r includes/ "${TEMP_DIR}/${PLUGIN_DIR}/"
cp -r assets/ "${TEMP_DIR}/${PLUGIN_DIR}/"
cp -r languages/ "${TEMP_DIR}/${PLUGIN_DIR}/"

# Remove any dev artifacts from copied files
find "${TEMP_DIR}" -name "*.test.*" -delete
find "${TEMP_DIR}" -name ".gitignore" -delete

# Create ZIP
cd "${TEMP_DIR}"
zip -r "${OLDPWD}/${OUTPUT}" "${PLUGIN_DIR}/"
cd "${OLDPWD}"

# Cleanup
rm -rf "${TEMP_DIR}"

echo "Built: ${OUTPUT} ($(du -h "${OUTPUT}" | cut -f1))"
echo "Ready for Woo Marketplace submission."
