[English](README.md) | **Español** | [Français](README.fr.md) | [Deutsch](README.de.md)

# Trusteed Agentic Commerce para WooCommerce

Los agentes de IA son un tipo nuevo de comprador online. Con Trusteed, la red que conecta a negocios y agentes, pueden comprar en tu tienda con las condiciones que tú fijes.

- Define tus reglas de negocio: a quién dejas comprar, hasta qué importe, qué categorías no quieres ofrecer a los agentes, límites de precio, niveles de stock que te protejan de agentes fraudulentos, y más.
- Recibe recibos firmados. Cada transacción genera un recibo firmado criptográficamente, en el que cualquier alteración queda a la vista, y que te sirve como evidencia de la compra si hay una disputa. Alineado con eIDAS (UE) y con eSIGN (EE. UU.).
- Consulta lo que hacen los agentes: cuánto gastan, qué productos compran y con qué frecuencia.
- Bloquea a los agentes que parezcan peligrosos o den problemas.
- Acepta compras en divisas digitales mediante el protocolo X402.
- Deja que agentes y comercios comercien directamente entre pares (peer-to-peer).

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

Cada transacción de un agente genera un recibo de confianza firmado, un registro en el que cualquier alteración queda a la vista (alineado con eIDAS y con eSIGN) que aparece en **Mis Ventas → Ventas IA**. Haz clic en una fila para ver el detalle: ID del agente, herramienta invocada, hashes de entrada y salida, JWS. También puedes descargar el recibo como ZIP y guardarlo como respaldo por si hay una disputa.

## Características

El plugin conecta tu catálogo de productos con los agentes de compra de IA a través del **Model Context Protocol (MCP)**, un estándar abierto creado por Anthropic. Nunca procesa pagos ni accede a datos sensibles de clientes. El checkout siempre ocurre en tu checkout nativo de WooCommerce.

- Herramientas MCP para agentes: `search_products`, `browse_categories`, `get_product_details` y `create_cart`, que lleva al comprador al checkout nativo de WooCommerce.
- Sincronización automática del catálogo. Los productos se sincronizan mediante hooks de WooCommerce al crearlos, actualizarlos o eliminarlos, cambios de stock incluidos, y puedes lanzar una sincronización completa a mano desde la página de ajustes. Solo salen de tu tienda datos públicos del catálogo (títulos, descripciones, precios, imágenes, categorías, stock), nunca datos personales (PII) de clientes, pedidos ni información de pago.
- Verificación del token del agente: `create_cart` reenvía el token JWS del agente al checkout, de modo que la verificación de firma y de repetición (R002) se ejecuta en el flujo normal.
- Puerta de aplicación (HITL): aprobación humana (human-in-the-loop) configurable para pedidos de agentes de alto valor.
- Endurecimiento SSRF: las URLs de la tienda y de la API se comprueban contra una lista blanca exacta de hosts y contra listas de bloqueo RFC1918, IPv6 ULA e IMDS de la nube.
- Valores por defecto fail-closed: no se envía nada si el secreto de aplicación está vacío, y para reconectar hace falta demostrar que eres el dueño del dominio, lo que protege contra la apropiación entre comercios.

## Compatibilidad

| Componente | Compatible con |
|-----------|-----------|
| WordPress | 6.0 – 6.9 |
| WooCommerce | 8.0 – 10.6 |
| PHP | 7.4+ (probado en 8.0–8.3) |

## Requisitos

- WordPress 6.0+ con WooCommerce 8.0+
- PHP 7.4 o más reciente
- Una cuenta de Trusteed ([regístrate gratis en trusteed.xyz](https://trusteed.xyz))

## Instalación

### Subida manual (recomendado)

1. **Descarga el `.zip` instalable** desde el último Release de GitHub:
   [**⬇ trusteed-agentic-commerce-woocommerce-2.1.0.zip**](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases/latest/download/trusteed-agentic-commerce-woocommerce-2.1.0.zip)
   o consulta todas las versiones en la [página de Releases](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases).
2. En tu panel de administración de WordPress: **Plugins → Añadir nuevo → Subir plugin**.
3. Selecciona el archivo descargado `trusteed-agentic-commerce-woocommerce-2.1.0.zip` y haz clic en **Instalar ahora**.
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
3. Introduce tu **API Key** desde [app.trusteed.xyz/settings](https://app.trusteed.xyz/settings).
4. Haz clic en **Guardar y conectar**. El plugin comprueba la conexión, registra tu tienda y sincroniza tu catálogo.

Una vez conectado, cualquier agente compatible con MCP (Claude, ChatGPT o uno propio construido con LangChain, CrewAI, Vercel AI SDK y herramientas parecidas) puede buscar tus productos, explorar categorías, leer los detalles de un producto y construir carritos. Cuando el cliente quiere comprar, el agente lo lleva a tu checkout nativo de WooCommerce, y tus pasarelas de pago de siempre (Stripe, PayPal, …) cobran.

La guía completa para comercios está en [`docs/MERCHANT_INSTALLATION_GUIDE.md`](docs/MERCHANT_INSTALLATION_GUIDE.md).

## Preguntas frecuentes

**¿Qué datos se envían?** Solo el catálogo público de productos: títulos, precios, descripciones, imágenes, categorías y estado del stock. Ningún dato personal (PII) de clientes, información de pago ni historial de pedidos. Toda la comunicación usa HTTPS.

**¿Qué agentes son compatibles?** Cualquier agente compatible con MCP: Claude (Anthropic), ChatGPT (OpenAI) y agentes propios construidos con LangChain, CrewAI, Vercel AI SDK o cualquier otro framework que soporte el Model Context Protocol.

**¿Ralentiza mi tienda?** Añade una petición en el checkout. El plugin también se comunica con Trusteed cuando cambia tu catálogo y cuando un agente actúa en tu tienda. Cuando un cliente hace un pedido, sea un agente o una persona, el plugin pide a Trusteed que evalúe tus reglas. Esa petición caduca a los 5 segundos. Si no se puede contactar con Trusteed, el plugin aplica tus reglas del último snapshot firmado que descargó. Si tampoco es posible, el resultado depende del modo de fallo que hayas elegido en los ajustes del plugin: bloquear el pedido o permitirlo.

## Historial de cambios

### 2.1.0

- Rebrand: las clases internas, las claves de opciones y las rutas REST pasan de `Amcp_`/`amcp_` a `Trusteed_`/`trusteed_`. Las instalaciones existentes siguen funcionando. Las opciones legacy `amcp_{key}` se siguen leyendo como respaldo, las rutas REST legacy se mantienen registradas junto a las nuevas y el prefijo de valores cifrados legacy se sigue descifrando.
- Corrección: el payload HITL de R043 ahora se propaga de punta a punta, para que un BLOCK pueda mostrar una pausa de intervención humana en vez de un bloqueo duro que pierde la intención del comprador.
- Corrección crítica: el bundle compilado del SPA de administración (`assets/admin-spa/`) faltaba en el paquete distribuido. El panel de administración de Trusteed mostraba un error de «bundle no compilado» en todas las instalaciones. Ahora el bundle se incluye correctamente.
- Endurecimiento en webhooks de facturación, aplicación de checkout, sincronización de catálogo y señales de carrito.

### 2.0.2

Corrección de aplicación de reglas en checkout. Las reglas del comerciante (monto máximo, países bloqueados, horarios comerciales) se saltaban por completo en checkouts orgánicos sin agente. Ahora aplican a todos los checkouts. Se agregó un evaluador de válvula de seguridad offline que aplica estas reglas localmente cuando la API remota de reglas no está disponible.

### 2.0.1

Corrección crítica de activación y seguridad (auditoría de Codex). Corrige un cambio de nombre a medias `AGENTICMCP_*` → `TRUSTEED_*` que impedía la activación en 2.0.0. `create_cart` ahora reenvía el token JWS del agente para que se ejecute la verificación R002. El cliente REST valida el host base de la API contra una lista blanca exacta.

### 2.0.0

Sprint de seguridad y fiabilidad. Desconectar pasa a ser un proceso en dos fases con token de confirmación. Reconectar exige prueba de propiedad del dominio (`/.well-known/amcp-verify.txt`). `create_cart` tiene un endpoint real de puente de carrito. El webhook de eventos de agente reintenta con backoff exponencial. También hay endurecimiento SSRF y valores por defecto de aplicación fail-closed.

## Soporte

- Correo de soporte: support@trusteed.xyz
- Incidencias en GitHub: [github.com/Trusteedxyz/agentic-commerce-woocommerce/issues](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/issues)

## Licencia

GPL-2.0-or-later. Consulta [LICENSE](LICENSE) para el texto completo.
