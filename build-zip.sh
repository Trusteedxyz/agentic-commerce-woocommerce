#!/usr/bin/env bash
# Empaqueta el plugin instalable para WooCommerce / WordPress.
# La carpeta raíz del zip == slug del plugin (text domain) == trusteed-for-woocommerce.
# El nombre del archivo sigue el patrón de distribución Trusteed (magento/prestashop).
# Excluye node_modules/, vendor/, tests/, screenshots y artefactos de desarrollo.
# Uso: bash build-zip.sh
set -euo pipefail

PLUGIN_SLUG="trusteed-for-woocommerce"   # carpeta raíz dentro del zip (WordPress slug)
REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
VERSION=$(grep "Version:" "${REPO_DIR}/trusteed-for-woocommerce.php" | head -1 | sed 's/.*Version: *//' | tr -d ' \r')
OUTPUT_DIR="${REPO_DIR}/dist"
OUTPUT="${OUTPUT_DIR}/trusteed-agentic-commerce-woocommerce-${VERSION}.zip"

echo "==> Empaquetando ${PLUGIN_SLUG} v${VERSION}"

TEMP_DIR="$(mktemp -d)"
trap 'rm -rf "${TEMP_DIR}"' EXIT
STAGE="${TEMP_DIR}/${PLUGIN_SLUG}"
mkdir -p "${STAGE}"

# Archivos de runtime que necesita el plugin instalado.
cp "${REPO_DIR}/trusteed-for-woocommerce.php" "${STAGE}/"
cp "${REPO_DIR}/uninstall.php"                "${STAGE}/"
cp "${REPO_DIR}/readme.txt"                   "${STAGE}/"
cp "${REPO_DIR}/PRIVACY_POLICY.md"            "${STAGE}/"
cp -r "${REPO_DIR}/includes/"                 "${STAGE}/includes/"
cp -r "${REPO_DIR}/assets/"                   "${STAGE}/assets/"
cp -r "${REPO_DIR}/languages/"                "${STAGE}/languages/"

# Quitar de la copia lo que no debe distribuirse.
rm -rf "${STAGE}/assets/screenshots"
find "${STAGE}" -name "*.test.*" -delete
find "${STAGE}" -name ".gitignore" -delete
find "${STAGE}" -name ".DS_Store" -delete

mkdir -p "${OUTPUT_DIR}"
rm -f "${OUTPUT}"
( cd "${TEMP_DIR}" && zip -rq "${OUTPUT}" "${PLUGIN_SLUG}" )

echo ""
echo "==> Paquete generado:"
echo "    ${OUTPUT}"
echo "    $(du -h "${OUTPUT}" | cut -f1)"
echo ""
echo "==> Estructura (carpeta raíz == slug del plugin):"
unzip -l "${OUTPUT}" | awk '{print $4}' | grep -E "^${PLUGIN_SLUG}/[^/]*$" | head
echo ""
echo "==> Instalar: WP Admin > Plugins > Añadir nuevo > Subir plugin > seleccionar el zip"
