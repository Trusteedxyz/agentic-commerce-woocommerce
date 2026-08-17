[English](README.md) | **Español** | [Français](README.fr.md) | [Deutsch](README.de.md)

# Trusteed Agentic Commerce para WooCommerce

Permite que los nuevos compradores online, los agentes de IA, realicen compras en tu tienda de forma segura y fiable gracias a Trusteed: la red que fomenta la confianza entre negocios y agentes.

- **Define tus reglas de negocio**: a quién permites comprar, hasta qué importe, qué categorías no quieres ofrecer a agentes, límites de precio, mantener niveles de stock para protegerte de posibles agentes fraudulentos, y más.
- **Recibos con evidencia de manipulación**: generamos recibos firmados electrónicamente y criptográficamente resistentes a la manipulación que registran lo que el agente hizo realmente — una prueba verificable de la integridad del agente. Están diseñados para seguir el estándar europeo de firma electrónica (eIDAS) y eSIGN (EE. UU.), pero todavía no son una firma ni un sello de tiempo *cualificados*, así que por sí solos no son evidencia de disputa lista para un banco o un tribunal.
- **Analítica de agentes**: consulta estadísticas de las compras de agentes — cuánto gastan, qué productos compran y con qué frecuencia.
- **Bloqueo de agentes**: bloquea agentes potencialmente peligrosos o problemáticos.
- **Divisas digitales**: permite compras en divisas digitales gracias al protocolo X402.
- **Transacciones entre pares**: permite el comercio directo entre pares (peer-to-peer) entre agentes y comercios.

## Capturas de pantalla

Cada panel de abajo corresponde a un elemento del menú **Trusteed** dentro de WooCommerce.

| Inicio | Trust Center | Mis Ventas |
|------|--------------|----------|
| ![Home](assets/screenshots/home.png) | ![Trust Center](assets/screenshots/trust-center.png) | ![My Sales](assets/screenshots/my-sales.png) |

| Mis Reglas | Agentes | Merchant Center |
|----------|--------|-----------------|
| ![My Rules](assets/screenshots/my-rules.png) | ![Agents](assets/screenshots/agents.png) | ![Merchant Center](assets/screenshots/merchant-center.png) |

| Recibos de confianza (Mis Ventas → Ventas IA) |
|--------------------------------------|
| ![Trust Receipts](assets/screenshots/ai-receipts.png) |

Cada transacción de un agente genera un **recibo de confianza** firmado criptográficamente — un registro a prueba de manipulación (compatible con eIDAS / eSIGN) que aparece en **Mis Ventas → Ventas IA**. Haz clic en cualquier fila para ver el detalle completo (ID del agente, herramienta invocada, hashes de entrada/salida, JWS) y descargar el comprobante como ZIP. La exportación es una prueba verificable de la integridad del agente: es un respaldo útil si un comprador afirma que nunca hizo el pedido, pero por sí sola no sustituye la evidencia que un banco o un tribunal pueda exigir en una disputa.

## Características

Trusteed for WooCommerce es un **conector ligero** que conecta tu catálogo de productos con el creciente ecosistema de agentes de compra de IA usando el **Model Context Protocol (MCP)** — un estándar abierto creado por Anthropic. El plugin nunca procesa pagos ni accede a datos sensibles de clientes: el checkout siempre ocurre en tu **checkout nativo de WooCommerce**.

- **Herramientas MCP para agentes** — `search_products`, `browse_categories`, `get_product_details` y `create_cart` (con redirección al checkout nativo de WooCommerce)
- **Sincronización automática del catálogo** — los productos se sincronizan mediante hooks de WooCommerce al crear/actualizar/eliminar, incluyendo cambios de stock; también hay sincronización manual completa disponible desde la página de ajustes. Solo se envían datos públicos del catálogo (títulos, descripciones, precios, imágenes, categorías, stock) — nunca PII de clientes, pedidos ni información de pago
- **Verificación de token del agente** — `create_cart` reenvía el token JWS del agente al checkout, de modo que la verificación de firma/repetición (R002) se ejecuta en el flujo normal
- **Puerta de aplicación (HITL)** — aprobación humana en el bucle (human-in-the-loop) configurable para pedidos de agentes de alto valor
- **Endurecimiento SSRF** — la URL base de la API con credenciales debe ser HTTPS y estar en una lista blanca exacta de hosts; el IMDS de la nube (`169.254.0.0/16`, `100.100.100.200`, `metadata.google.internal`), el IPv6 unique-local (`fc00::/7`) y el link-local (`fe80::/10`) se bloquean en todos los entornos, y loopback / RFC1918 solo funcionan tras activar explícitamente `TRUSTEED_ALLOW_LOCAL_API_BASE` (desactivado por defecto)
- **Valores por defecto de fallo cerrado (fail-closed)** — no hay envío si el secreto de aplicación está vacío; se requiere prueba de propiedad del dominio al reconectar (protección contra apropiación entre comercios)

## Compatibilidad

| Componente | Compatible con |
|-----------|-----------|
| WordPress | 6.0 – 6.9 |
| WooCommerce | 8.0 – 10.6 |
| PHP | 7.4+ (probado en 8.0–8.3) |

## Requisitos

- WordPress 6.0+ con WooCommerce 8.0+
- PHP 7.4 o más reciente
- Una cuenta de Trusteed — [regístrate gratis en trusteed.xyz](https://trusteed.xyz)

## Instalación

### Subida manual (recomendado)

1. **Descarga el `.zip` instalable** desde el último Release de GitHub:
   [**⬇ Último release — trusteed-agentic-commerce-woocommerce-&lt;versión&gt;.zip**](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases/latest)
   — o consulta todas las versiones en la [página de Releases](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases).
2. En tu panel de administración de WordPress: **Plugins → Añadir nuevo → Subir plugin**.
3. Selecciona el archivo `.zip` descargado y haz clic en **Instalar ahora**.
4. Haz clic en **Activar**.

### Desde el código fuente (compilar el zip tú mismo)

```bash
git clone https://github.com/Trusteedxyz/agentic-commerce-woocommerce.git
cd agentic-commerce-woocommerce
bash build-zip.sh        # outputs dist/trusteed-agentic-commerce-woocommerce-<version>.zip
```

## Configuración

1. Inicia sesión en tu **panel de administración** de WordPress.
2. Ve a **WooCommerce → Trusteed** (o al elemento de menú **Trusteed**).
3. Introduce tu **API Key** desde [trusteed.xyz/dashboard/settings](https://trusteed.xyz/dashboard/settings).
4. Haz clic en **Guardar y conectar** — el plugin comprueba la conectividad, registra tu tienda y sincroniza tu catálogo automáticamente.

Una vez conectado, cualquier agente compatible con MCP (Claude, ChatGPT o agentes personalizados construidos con LangChain, CrewAI, Vercel AI SDK, etc.) puede buscar tus productos, explorar categorías, ver detalles de producto y construir carritos. Cuando el cliente está listo para comprar, el agente lo redirige a tu checkout nativo de WooCommerce, donde tus pasarelas de pago existentes (Stripe, PayPal, …) gestionan el pago.

Encontrarás una guía detallada para comercios en [`docs/MERCHANT_INSTALLATION_GUIDE.md`](docs/MERCHANT_INSTALLATION_GUIDE.md).

## Preguntas frecuentes

**¿Qué datos se envían?** Solo el catálogo público de productos (títulos, precios, descripciones, imágenes, categorías, estado del stock). Ningún dato personal (PII) de clientes, información de pago ni historial de pedidos. Toda la comunicación usa HTTPS.

**¿Qué agentes son compatibles?** Cualquier agente compatible con MCP: Claude (Anthropic), ChatGPT (OpenAI) y agentes personalizados construidos con LangChain, CrewAI, Vercel AI SDK, o cualquier framework que soporte el Model Context Protocol.

**¿Ralentiza mi tienda?** No. El plugin solo se comunica con Trusteed cuando hay cambios en el catálogo — no añade sobrecarga a la carga de páginas de la tienda ni al checkout del cliente.

## Historial de cambios

### 2.2.2

- **Corrección de seguridad** — el cliente de la API aceptaba una URL base en loopback o en rango privado RFC1918 (`10.*`, `172.16–31.*`, `192.168.*`, `localhost`, `127.*`) en **todos** los entornos, incluso por HTTP plano. Una instalación cuya URL de API hubiese sido redirigida enviaría su credencial `X-AgenticMCP-Key` a una dirección interna. Ese modo de desarrollo ahora hay que activarlo a propósito y viene desactivado: sólo se habilita con `TRUSTEED_ALLOW_LOCAL_API_BASE` o con el tipo de entorno `local` de WordPress — la misma puerta que `Trusteed_Token_Broker` ya aplicaba con `WP_DEBUG` y que este cliente había perdido.
- **Corrección de seguridad** — los metadatos de instancia en la nube y los rangos internos de IPv6 quedan bloqueados en todos los entornos, *incluido* el modo de desarrollo, que antes los reabría todos: `169.254.0.0/16` (IMDS), Alibaba `100.100.100.200`, `metadata.google.internal`, unique-local `fc00::/7` y link-local `fe80::/10`. Además devuelven su propio código de error en lugar del engañoso «configura una URL HTTPS».
- **Corregido** — los hosts IPv6 no coincidían con ninguna comprobación: `parse_url()` los devuelve entre corchetes (`[::1]`), así que la entrada de loopback `::1` era código muerto.
- **Corrección de privacidad** — al desinstalar quedaban 21 filas de opciones, entre ellas tres secretos cifrados (`trusteed_embed_wp_secret`, `trusteed_enforcement_hmac_secret`, `trusteed_woo_webhook_secret`) y los alias heredados `amcp_*` que el accesor de opciones sigue leyendo como reserva, de modo que una reinstalación podía resucitar un secreto viejo. `uninstall.php` ahora limpia los tres espacios de nombres más los transitorios de snapshot y JWKS. Un test nuevo recorre el código buscando toda clave de opción escribible y falla si la lista de desinstalación se queda atrás.
- **Corrección de documentación** — los recibos de confianza se describían como «prueba de la transacción real en caso de disputa». El propio producto dice lo contrario: una prueba verificable de integridad, no evidencia de disputa lista para un banco o un tribunal. Corregido para que coincida.
- **Corrección de documentación** — la FAQ de desactivación afirmaba que desactivar desconecta la tienda y que no quedan datos residuales en nuestros servidores. Desactivar no hace nada, y desconectar conserva el registro de la tienda y los productos sincronizados. Corregido, documentando la vía para solicitar el borrado.
- **Corrección de documentación** — se decía que el catálogo sincronizaba «variantes y reseñas»; no se envía ninguna de las dos. La lista de campos transmitidos ahora es exacta.
- **Corrección de documentación** — `Tested up to` / `WC tested up to` no coincidían entre `readme.txt` (6.9 / 10.6) y la cabecera del plugin (6.7 / 9.5). Ambos dicen ya 6.9 / 10.6. Los tests automáticos se ejecutan contra WordPress 6.8 con la última WooCommerce estable en PHP 8.1–8.2.
- **Corrección de documentación** — arreglados enlaces que daban 404: `/developers`, `/privacy` y `/terms` necesitan el prefijo `/en/`, y `/support` no existe (sustituido por el formulario de contacto y las issues de GitHub).

### 2.2.1

- **Corregido** — `browse_categories` enviaba la misma cadena envuelta en delimitadores al canal legible por máquina y al narrado. `guardMerchantField` envuelve el texto del comercio por defecto en `<<<MERCHANT_CONTENT_START>>> … <<<MERCHANT_CONTENT_END>>>` para que un agente distinga "esto es dato del comercio, no una instrucción" — pero la tool reutilizaba esa misma cadena ya envuelta para `structuredContent`, así que una categoría llamada "Zapatillas" aparecía como `<<<MERCHANT_CONTENT_START>>>Zapatillas<<<MERCHANT_CONTENT_END>>>` en el canal de máquina. Ahora `structuredContent` recibe el valor sin envolver; los delimitadores se quedan solo donde cumplen su función, en la narración.
- **Corregido** — la regla R047 (importe mínimo de aportación) no tenía campo en el panel de administración: sus parámetros existían en el esquema pero solo se podían configurar por API.
- **Corregido** — `MerchantCheckoutConfig` tenía texto traducido para un estado vacío (`noRails`, presente en `en.ts` y `es.ts`) que el componente nunca pintaba, así que un comercio sin rieles de pago configurados veía una lista vacía sin explicación.
- **Corregido** — el bundle del panel de administración (`assets/admin-spa/`) se distribuía sin minificar: 869 KB / 25.064 líneas en vez de los 490 KB / 41 líneas que produce el comando de build documentado. Reconstruido desde la fuente con nombre de fichero estable (`admin-spa.js`), igual que los otros tres conectores de plataforma.

### 2.2.0

- **Corrección de seguridad** — el verificador de tokens de agente trataba `exp` e `iat` como opcionales: ambas comprobaciones colgaban de `> 0`, así que un token que simplemente OMITÍA el claim se saltaba la comprobación entera. Sin `exp` no caducaba nunca; sin `iat` no tenía antigüedad máxima. Ahora los dos son obligatorios, y un valor no numérico se rechaza en vez de convertirse. La protección anti-replay ya era fail-closed aquí (un `jti` ausente o mal formado se rechaza), así que esto cierra la mitad que faltaba.
- **Corrección de seguridad** — un `iat` en el futuro se rechaza (se toleran 30s de desfase de reloj). Combinado con la ventana de antigüedad máxima daba una vida deslizante: `ahora - iat` se mantiene pequeño mientras el emisor siga empujando el claim hacia adelante, así que el token no envejecía nunca.
- **Corrección** — la regla R036 (valor máximo por línea) leía su tope de un parámetro llamado `maxCents`, copiado de R035. El nombre canónico es `maxCentsPerLine`, y es el único que acepta el esquema estricto del panel del comerciante, así que un tope configurado por el comerciante nunca habría llegado a la comprobación. Ahora se lee primero la clave canónica; `maxCents` se sigue aceptando como reserva.
- **Corrección** — el test de conformidad entre lenguajes resolvía su fixture por una ruta que sólo existe en el monorepo de desarrollo, así que fallaba en este repositorio. Ahora lee la copia incluida en `tests/fixtures/`.
- **Recibos de confianza** — el bundle del panel se reconstruye con el botón de descarga del recibo, que lo exporta en ZIP por el mismo endpoint que usa el panel alojado. El botón dice sin rodeos qué es esa exportación: prueba de integridad del agente, no evidencia de disputa.

### 2.1.0

- **Rebrand** — clases internas, claves de opciones y rutas REST renombradas de `Amcp_`/`amcp_` a `Trusteed_`/`trusteed_`. Compatibilidad retroactiva preservada: las instalaciones existentes siguen funcionando (las opciones legacy `amcp_{key}` se siguen leyendo como respaldo, las rutas REST legacy se mantienen registradas junto a las nuevas, el prefijo de valores cifrados legacy se sigue descifrando).
- **Corrección** — el payload HITL de R043 ahora se propaga de punta a punta, para que un BLOCK pueda mostrar una pausa de intervención humana en vez de un bloqueo duro que pierde la intención del comprador.
- **Corrección crítica** — el bundle compilado del SPA de administración (`assets/admin-spa/`) faltaba por completo en el paquete distribuido; el panel de administración de Trusteed mostraba un error de "bundle no compilado" en toda instalación. El bundle ahora se incluye correctamente.
- Endurecimiento en webhooks de facturación, aplicación de checkout, sincronización de catálogo y señales de carrito.

### 2.0.2

Corrección de aplicación de reglas en checkout. Las reglas del comerciante (monto máximo, países bloqueados, horarios comerciales) se saltaban por completo en checkouts orgánicos sin agente — ahora aplican universalmente. Se agregó un evaluador de válvula de seguridad offline que aplica estas reglas localmente cuando la API remota de reglas no está disponible.

### 2.0.1

Corrección crítica de activación y seguridad (auditoría de Codex). Corrige un cambio de nombre a medias `AGENTICMCP_*` → `TRUSTEED_*` que impedía la activación en 2.0.0; `create_cart` ahora reenvía el token JWS del agente para que la verificación R002 se ejecute; el cliente REST valida el host base de la API contra una lista blanca exacta.

### 2.0.0

Sprint de seguridad y fiabilidad. Desconexión en 2 fases con token de confirmación; la reconexión requiere prueba de propiedad del dominio (`/.well-known/amcp-verify.txt`); endpoint real de puente de carrito para `create_cart`; reintentos con backoff exponencial en el webhook de eventos de agente; endurecimiento SSRF; valores por defecto de aplicación fail-closed.

## Soporte

- Correo de soporte: support@trusteed.xyz
- Incidencias en GitHub: [github.com/Trusteedxyz/agentic-commerce-woocommerce/issues](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/issues)

## Licencia

GPL-2.0-or-later. Consulta [LICENSE](LICENSE) para el texto completo.
