[English](README.md) | **Español** | [Français](README.fr.md) | [Deutsch](README.de.md)

# Trusteed Agentic Commerce para WooCommerce

Los agentes de IA son un tipo nuevo de comprador online. Con Trusteed, la red que conecta a negocios y agentes, pueden comprar en tu tienda con las condiciones que tú fijes.

- Define tus reglas de negocio: a quién dejas comprar, hasta qué importe, qué categorías no quieres ofrecer a los agentes, límites de precio, niveles de stock que te protejan de agentes fraudulentos, y más.
- Recibe recibos firmados. Cada transacción genera un recibo firmado criptográficamente, en el que cualquier alteración queda a la vista, y que registra lo que el agente hizo realmente, una prueba verificable de la integridad del agente. Alineado con eIDAS (UE) y con eSIGN (EE. UU.), pero todavía no es una firma ni un sello de tiempo *cualificados*, así que por sí solo no sirve como evidencia de disputa lista para un banco o un tribunal.
- Consulta lo que hacen los agentes: cuánto gastan, qué productos compran y con qué frecuencia.
- Bloquea a los agentes que parezcan peligrosos o den problemas.
- Acepta compras en divisas digitales mediante el protocolo X402.
- Deja que agentes y comercios comercien directamente entre pares (peer-to-peer).
- Comprueba si los agentes pueden comprarte. El panel de preparación para agentes comprueba en vivo si los agentes de IA pueden comprar hoy en tu tienda. Ofrece tres vistas independientes (lo que dicen otros, lo que prometes frente a lo que haces, lo que hemos observado), y nada se puntúa hasta que se verifica.

## Capturas de pantalla

Cada panel de abajo corresponde a un elemento del menú **Trusteed** dentro de WooCommerce.

| Inicio | Trust Center | Mis Ventas |
|------|--------------|----------|
| ![Home](assets/screenshots/home.png) | ![Trust Center](assets/screenshots/trust-center.png) | ![My Sales](assets/screenshots/my-sales.png) |

| Mis Reglas | Agentes | Merchant Center |
|----------|--------|-----------------|
| ![My Rules](assets/screenshots/my-rules.png) | ![Agents](assets/screenshots/agents.png) | ![Merchant Center](assets/screenshots/merchant-center.png) |

| Recibos de confianza (Mis Ventas → Ventas IA) | Agent Readiness |
|--------------------------------------|------------------|
| ![Trust Receipts](assets/screenshots/ai-receipts.png) | ![Agent Readiness](assets/screenshots/agent-readiness.png) |

Cada transacción de un agente genera un recibo de confianza firmado, un registro en el que cualquier alteración queda a la vista (alineado con eIDAS y con eSIGN) que aparece en **Mis Ventas → Ventas IA**. Haz clic en una fila para ver el detalle: ID del agente, herramienta invocada, hashes de entrada y salida, JWS. También puedes descargar el recibo como ZIP. La exportación es una prueba verificable de la integridad del agente. Sirve de respaldo si un comprador afirma que nunca hizo el pedido, pero por sí sola no sustituye la evidencia que un banco o un tribunal pueda exigir en una disputa.

## Características

El plugin conecta tu catálogo de productos con los agentes de compra de IA a través del **Model Context Protocol (MCP)**, un estándar abierto creado por Anthropic. Nunca procesa pagos ni accede a datos sensibles de clientes. El checkout siempre ocurre en tu checkout nativo de WooCommerce.

- Herramientas MCP para agentes: `search_products`, `browse_categories`, `get_product_details` y `create_cart`, que lleva al comprador al checkout nativo de WooCommerce.
- Sincronización automática del catálogo. Los productos se sincronizan mediante hooks de WooCommerce al crearlos, actualizarlos o eliminarlos, cambios de stock incluidos, y puedes lanzar una sincronización completa a mano desde la página de ajustes. Solo salen de tu tienda datos públicos del catálogo (títulos, descripciones, precios, imágenes, categorías, stock), nunca datos personales (PII) de clientes, pedidos ni información de pago.
- Verificación del token del agente: `create_cart` reenvía el token JWS del agente al checkout, de modo que la verificación de firma y de repetición (R002) se ejecuta en el flujo normal.
- Puerta de aplicación (HITL): aprobación humana (human-in-the-loop) configurable para pedidos de agentes de alto valor.
- Endurecimiento SSRF: la URL base de la API que lleva tus credenciales debe ser HTTPS y estar en una lista blanca exacta de hosts. Las direcciones IMDS de la nube (`169.254.0.0/16`, `100.100.100.200`, `metadata.google.internal`), IPv6 unique-local (`fc00::/7`) y link-local (`fe80::/10`) se bloquean en todos los entornos. Loopback y RFC1918 solo funcionan si activas explícitamente `TRUSTEED_ALLOW_LOCAL_API_BASE`, desactivado por defecto.
- Valores por defecto fail-closed: no se envía nada si el secreto de aplicación está vacío, y para reconectar hace falta demostrar que eres el dueño del dominio, lo que protege contra la apropiación entre comercios.

## Compatibilidad

| Componente | Compatible con |
|-----------|-----------|
| WordPress | 6.0 – 7.0 |
| WooCommerce | 8.0 – 11.0 |
| PHP | 7.4+ (probado en 8.0–8.3) |

## Requisitos

- WordPress 6.0+ con WooCommerce 8.0+
- PHP 7.4 o más reciente
- Una cuenta de Trusteed ([regístrate gratis en trusteed.xyz](https://trusteed.xyz))

## Instalación

### Subida manual (recomendado)

1. **Descarga el `.zip` instalable** desde el último Release de GitHub:
   [**⬇ Último release: trusteed-agentic-commerce-woocommerce-&lt;versión&gt;.zip**](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases/latest)
   o consulta todas las versiones en la [página de Releases](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases).
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
3. Introduce tu correo y contraseña de Trusteed y pulsa **Conectar mi tienda** (*Connect my store*). Si aún no tienes cuenta, se crea una. Si prefieres no usar contraseña, pulsa **Conectar con API key** (*Connect with API key*) y pega tu API key desde [trusteed.xyz/dashboard/settings](https://trusteed.xyz/dashboard/settings).
4. El plugin registra tu tienda y sincroniza tu catálogo. **Probar conexión** (*Test connection*) comprueba el enlace y **Sincronizar catálogo ahora** (*Sync catalog now*) lanza una sincronización completa cuando quieras.

Una vez conectado, cualquier agente compatible con MCP (Claude, ChatGPT o uno propio construido con LangChain, CrewAI, Vercel AI SDK y herramientas parecidas) puede buscar tus productos, explorar categorías, leer los detalles de un producto y construir carritos. Cuando el cliente quiere comprar, el agente lo lleva a tu checkout nativo de WooCommerce, y tus pasarelas de pago de siempre (Stripe, PayPal, …) cobran.

La guía completa para comercios está en [`docs/MERCHANT_INSTALLATION_GUIDE.md`](docs/MERCHANT_INSTALLATION_GUIDE.md).

## Preguntas frecuentes

**¿Qué datos se envían?** Solo el catálogo público de productos: títulos, precios, descripciones, imágenes, categorías y estado del stock. Ningún dato personal (PII) de clientes, información de pago ni historial de pedidos. Toda la comunicación usa HTTPS.

**¿Qué agentes son compatibles?** Cualquier agente compatible con MCP: Claude (Anthropic), ChatGPT (OpenAI) y agentes propios construidos con LangChain, CrewAI, Vercel AI SDK o cualquier otro framework que soporte el Model Context Protocol.

**¿Ralentiza mi tienda?** Añade una petición en el checkout. El plugin también se comunica con Trusteed cuando cambia tu catálogo y cuando un agente actúa en tu tienda. Cuando un cliente hace un pedido, sea un agente o una persona, el plugin pide a Trusteed que evalúe tus reglas. Esa petición caduca a los 5 segundos. Si no se puede contactar con Trusteed, el plugin aplica tus reglas del último snapshot firmado que descargó. Si tampoco es posible, el resultado depende de la opción `trusteed_failure_mode`: `enforce` (el valor por defecto) bloquea el pedido y `observe` lo permite.

## El panel de preparación agéntica

**¿Me encuentran los agentes?** es una página dentro de tu panel de
administración que responde a una sola pregunta: cuando un agente de compra con
IA visita tu tienda, ¿se encuentra lo que tú crees que se encuentra?

Nunca enseña una nota única. Tres columnas, sin promediar, porque responden a
preguntas distintas y pueden contradecirse con toda legitimidad:

| Columna | Qué es |
| --- | --- |
| **Lo que dice un tercero** | El veredicto de un escáner externo, citado tal cual. Nunca reinterpretado a una escala nuestra: en cuanto reescalas la nota de otro, estás corrigiendo tu propio examen |
| **¿Coincide lo que dices con lo que haces?** | 16 comprobaciones que contrastan lo que tu tienda **anuncia** con lo que **responde de verdad**. Esta es la parte que ningún escáner externo puede hacer: necesita tus credenciales |
| **Lo que hemos visto pasar** | Tráfico agéntico real en la ventana elegida: qué agentes llegaron, qué herramientas usaron, hasta dónde llegaron y dónde fallaron |

Una comprobación que no se ha podido hacer se informa como **sin comprobar**,
con el motivo. Nunca se descarta en silencio ni se cuenta como aprobado. «No
hemos podido mirar» y «hemos mirado y está bien» son respuestas distintas, y la
página dice cuál de las dos es.

### Qué mira cada comprobación

| Comprobación | Qué detecta |
| --- | --- |
| C1 | Anuncias herramientas que tu tienda no sirve |
| C2 | Anuncias un protocolo de compra cuyo endpoint no responde |
| C3 | El precio del catálogo no es el que se cobra |
| C4 | Se anuncia disponible lo que no lo está |
| C5 | Tu política de devoluciones dice cosas distintas según dónde se mire |
| C6 | Anuncias como disponible algo que está apagado |
| C7 | Reglas activadas que no pueden actuar por falta de datos |
| C8 | Tus reglas observan pero no bloquean |
| C9 | La forma de identificarse que anuncias no funciona |
| C10 | Un agente puede comprar cualquier importe sin tu confirmación |
| C11 | El punto de venta usa reglas caducadas |
| C12 | Operaciones sin comprobante firmado |
| C13 | Direcciones anunciadas que no funcionan |
| C14 | Los agentes ven datos desfasados de tu tienda |
| C15 | Credenciales de identidad a punto de caducar |
| C16 | El plazo de entrega que prometes no es el que cumples |

Algunas comprobaciones necesitan algo más que tu configuración para ejecutarse, y
la página lo dice en vez de dejar un hueco:

- **Necesita tu tienda conectada** (C3, C4, C5, C14): comparan contra tu catálogo
  real, y sin credenciales no hay con qué comparar.
- **Necesita pedidos entregados** (C16): compara lo que prometes con lo que has
  cumplido de verdad, y eso no se puede sin historial.
- **Esta vez no había nada que comparar**: por ejemplo, C12 no tiene nada que
  mirar hasta que un agente haya completado una compra. Eso no es un suspenso.

Las comprobaciones se ejecutan una vez al día y la página enseña el resultado
**con su fecha**, para que un veredicto de ayer se vea como un veredicto de ayer.
Un «todo bien» guardado y presentado como actual sería justo el autoengaño que
esta página existe para cazar.

## Historial de cambios

### 2.3.4

- Nuevo: cuando una comprobación no se pudo ejecutar, el panel ahora explica qué la desbloquearía — nada que hacer, hay que configurar algo, falta esperar datos, o ha fallado una comprobación nuestra — en vez de una lista plana de grises sin explicar.
- Nuevo: el panel ahora muestra qué servidor nuestro respondió a tu petición, una etiqueta corta y opaca. Útil para comparar lo que ves aquí con lo que ve soporte; nunca revela un nombre de host o de servicio.

### 2.3.3

- Nuevo: en Ajustes puedes elegir qué herramientas sirve tu tienda a los agentes. Si nunca has guardado una lista, el panel te dice que lo que sirve es el conjunto básico que trae la plataforma, y no una elección tuya.
- Nuevo: un botón para volver a comprobar sin esperar al barrido diario, y el panel recuerda qué ha cambiado desde la comprobación anterior.
- Cambiado: nuestras propias averías dejan de contarse como incoherencias de tu tienda. El panel las separa, porque no puedes hacer nada con ellas.

### 2.3.2

- Corregido: la página de disponibilidad para agentes se publicaba sin su hoja de estilos, así que el panel salía sin formato.
- Corregido: el panel podía mostrar la carcasa en un idioma y el diagnóstico en otro. El idioma resuelto viaja ahora junto a los textos, en vez de detectarse dos veces por separado.
- Nuevo: cada hallazgo lleva un enlace a donde se corrige, y las afirmaciones del comercio —el plazo de entrega y las demás— aparecen con el respaldo que tiene cada una.
- Cambiado: una tienda sin ninguna comprobación todavía se lee como «comprobando» en lugar de «se comprueba una vez al día»: abrir el panel ya lanza la primera comprobación en segundo plano.

### 2.3.1

- Corrección crítica: la 2.3.0 salió con un error de sintaxis PHP en el enrutador del admin (`->render_spa_shell()` sin `$this`, y `array( , 'render_agent_readiness' )`). Activarla tumbaba **todo el escritorio de WordPress**, no sólo las páginas de Trusteed. Si estás en 2.3.0, actualiza ya. Ahora se comprueba la sintaxis de todos los ficheros PHP del plugin.
- Corregido: la página «Agent Readiness» enseñaba el Trust Center. El montaje de la SPA valida la sección contra una lista blanca y `agent-readiness` nunca se añadió, así que caía en silencio: el comerciante pulsaba «Agent Readiness» y veía otro panel.

### 2.3.0

- **Nuevo — panel de preparación agéntica.** *¿Me encuentran los agentes?* llega al panel de administración. Contrasta lo que tu tienda anuncia con lo que responde de verdad, en **16 comprobaciones**, y las enseña las dieciséis, no sólo las que fallan. Una comprobación que no se ha podido hacer dice **por qué** (tienda sin conectar, todavía sin pedidos entregados, nada que comparar esta vez) en vez de dejar un hueco que se lee como avería. Ver «El panel de preparación agéntica» más arriba.
- Corregido: el diagnóstico se escribía en castellano dentro de la API y se mostraba tal cual, así que un comerciante con el panel en inglés leía encabezados en inglés y hallazgos en castellano. Las comprobaciones emiten ahora códigos neutros de idioma y el texto se compone al servirlo, en el idioma que estés usando.
- Corregido: la comprobación C1 («anuncias herramientas que tu tienda no sirve») daba por servido el catálogo público entero cuando no había lista de herramientas configurada: informaba de 46 de 48 respondiendo cuando el servidor sirve 12. Fallaba en la dirección aduladora, que es justo la que este panel existe para cazar.
- Corregido: la comprobación C6 («anuncias como disponible algo que está apagado») daba una capacidad por apagada siempre que su bandera no estuviera puesta, incluso en banderas que están encendidas por defecto. Era una falsa alarma en todas las tiendas.

### 2.2.2

- Corrección de seguridad: el cliente de la API aceptaba una URL base en loopback o en rango privado RFC1918 (`10.*`, `172.16–31.*`, `192.168.*`, `localhost`, `127.*`) en **todos** los entornos, incluso por HTTP plano. Una instalación cuya URL de API hubiese sido redirigida enviaría su credencial `X-AgenticMCP-Key` a una dirección interna. Ese modo de desarrollo ahora hay que activarlo a propósito y viene desactivado: sólo se habilita con `TRUSTEED_ALLOW_LOCAL_API_BASE` o con el tipo de entorno `local` de WordPress — la misma puerta que `Trusteed_Token_Broker` ya aplicaba con `WP_DEBUG` y que este cliente había perdido.
- Corrección de seguridad: los metadatos de instancia en la nube y los rangos internos de IPv6 quedan bloqueados en todos los entornos, *incluido* el modo de desarrollo, que antes los reabría todos: `169.254.0.0/16` (IMDS), Alibaba `100.100.100.200`, `metadata.google.internal`, unique-local `fc00::/7` y link-local `fe80::/10`. Además devuelven su propio código de error en lugar del engañoso «configura una URL HTTPS».
- Corregido: los hosts IPv6 no coincidían con ninguna comprobación: `parse_url()` los devuelve entre corchetes (`[::1]`), así que la entrada de loopback `::1` era código muerto.
- Corrección de privacidad: al desinstalar quedaban 21 filas de opciones, entre ellas tres secretos cifrados (`trusteed_embed_wp_secret`, `trusteed_enforcement_hmac_secret`, `trusteed_woo_webhook_secret`) y los alias heredados `amcp_*` que el accesor de opciones sigue leyendo como reserva, de modo que una reinstalación podía resucitar un secreto viejo. `uninstall.php` ahora limpia los tres espacios de nombres más los transitorios de snapshot y JWKS. Un test nuevo recorre el código buscando toda clave de opción escribible y falla si la lista de desinstalación se queda atrás.
- Corrección de documentación: los recibos de confianza se describían como «prueba de la transacción real en caso de disputa». El propio producto dice lo contrario: una prueba verificable de integridad, no evidencia de disputa lista para un banco o un tribunal. Corregido para que coincida.
- Corrección de documentación: la FAQ de desactivación afirmaba que desactivar desconecta la tienda y que no quedan datos residuales en nuestros servidores. Desactivar no hace nada, y desconectar conserva el registro de la tienda y los productos sincronizados. Corregido, documentando la vía para solicitar el borrado.
- Corrección de documentación: se decía que el catálogo sincronizaba «variantes y reseñas»; no se envía ninguna de las dos. La lista de campos transmitidos ahora es exacta.
- Corrección de documentación: `Tested up to` / `WC tested up to` no coincidían entre `readme.txt` (6.9 / 10.6) y la cabecera del plugin (6.7 / 9.5). Ambos dicen ya 6.9 / 10.6. Los tests automáticos se ejecutan contra WordPress 6.8 con la última WooCommerce estable en PHP 8.1–8.2.
- Corrección de documentación: arreglados enlaces que daban 404: `/developers`, `/privacy` y `/terms` necesitan el prefijo `/en/`, y `/support` no existe (sustituido por el formulario de contacto y las issues de GitHub).

### 2.2.1

- Corregido: `browse_categories` enviaba la misma cadena envuelta en delimitadores al canal legible por máquina y al narrado. `guardMerchantField` envuelve el texto del comercio por defecto en `<<<MERCHANT_CONTENT_START>>> … <<<MERCHANT_CONTENT_END>>>` para que un agente distinga "esto es dato del comercio, no una instrucción" — pero la tool reutilizaba esa misma cadena ya envuelta para `structuredContent`, así que una categoría llamada "Zapatillas" aparecía como `<<<MERCHANT_CONTENT_START>>>Zapatillas<<<MERCHANT_CONTENT_END>>>` en el canal de máquina. Ahora `structuredContent` recibe el valor sin envolver; los delimitadores se quedan solo donde cumplen su función, en la narración.
- Corregido: la regla R047 (importe mínimo de aportación) no tenía campo en el panel de administración: sus parámetros existían en el esquema pero solo se podían configurar por API.
- Corregido: `MerchantCheckoutConfig` tenía texto traducido para un estado vacío (`noRails`, presente en `en.ts` y `es.ts`) que el componente nunca pintaba, así que un comercio sin rieles de pago configurados veía una lista vacía sin explicación.
- Corregido: el bundle del panel de administración (`assets/admin-spa/`) se distribuía sin minificar: 869 KB / 25.064 líneas en vez de los 490 KB / 41 líneas que produce el comando de build documentado. Reconstruido desde la fuente con nombre de fichero estable (`admin-spa.js`), igual que los otros tres conectores de plataforma.

### 2.2.0

- Corrección de seguridad: el verificador de tokens de agente trataba `exp` e `iat` como opcionales: ambas comprobaciones colgaban de `> 0`, así que un token que simplemente OMITÍA el claim se saltaba la comprobación entera. Sin `exp` no caducaba nunca; sin `iat` no tenía antigüedad máxima. Ahora los dos son obligatorios, y un valor no numérico se rechaza en vez de convertirse. La protección anti-replay ya era fail-closed aquí (un `jti` ausente o mal formado se rechaza), así que esto cierra la mitad que faltaba.
- Corrección de seguridad: un `iat` en el futuro se rechaza (se toleran 30s de desfase de reloj). Combinado con la ventana de antigüedad máxima daba una vida deslizante: `ahora - iat` se mantiene pequeño mientras el emisor siga empujando el claim hacia adelante, así que el token no envejecía nunca.
- Corrección: la regla R036 (valor máximo por línea) leía su tope de un parámetro llamado `maxCents`, copiado de R035. El nombre canónico es `maxCentsPerLine`, y es el único que acepta el esquema estricto del panel del comerciante, así que un tope configurado por el comerciante nunca habría llegado a la comprobación. Ahora se lee primero la clave canónica; `maxCents` se sigue aceptando como reserva.
- Corrección: el test de conformidad entre lenguajes resolvía su fixture por una ruta que sólo existe en el monorepo de desarrollo, así que fallaba en este repositorio. Ahora lee la copia incluida en `tests/fixtures/`.
- Recibos de confianza: el bundle del panel se reconstruye con el botón de descarga del recibo, que lo exporta en ZIP por el mismo endpoint que usa el panel alojado. El botón dice sin rodeos qué es esa exportación: prueba de integridad del agente, no evidencia de disputa.

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
