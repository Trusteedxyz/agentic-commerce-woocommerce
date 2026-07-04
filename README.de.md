[English](README.md) | [Español](README.es.md) | [Français](README.fr.md) | **Deutsch**

# Trusteed Agentic Commerce für WooCommerce

Ermöglichen Sie es den neuen Online-Käufern, den KI-Agenten, sicher und zuverlässig in Ihrem Shop einzukaufen — dank Trusteed: dem Netzwerk, das Vertrauen zwischen Unternehmen und Agenten fördert.

- **Legen Sie Ihre Geschäftsregeln fest**: wem Sie den Kauf erlauben, bis zu welchem Betrag, welche Kategorien Sie Agenten nicht anbieten möchten, legen Sie Preisgrenzen fest, halten Sie Lagerbestände aufrecht, um sich vor potenziell betrügerischen Agenten zu schützen, und mehr.
- **Manipulationssichere Belege**: wir erstellen elektronisch signierte und kryptografisch manipulationssichere Belege, die im Streitfall als Nachweis der tatsächlichen Transaktion dienen. Kompatibel mit eIDAS (EU, UK) und eSIGN (USA).
- **Agenten-Analysen**: Statistiken zu Agentenkäufen einsehen — wie viel sie ausgeben, welche Produkte sie kaufen und wie oft.
- **Agentensperrung**: potenziell gefährliche oder problematische Agenten blockieren.
- **Digitale Währungen**: ermöglicht Käufe in digitalen Währungen dank des X402-Protokolls.
- **Peer-to-Peer-Transaktionen**: ermöglicht direkten Peer-to-Peer-Handel zwischen Agenten und Händlern.

## Screenshots

Jedes Panel unten entspricht einem Eintrag im **Trusteed**-Menü innerhalb von WooCommerce.

| Startseite | Trust Center | Meine Verkäufe |
|------|--------------|----------|
| ![Home](assets/screenshots/home.png) | ![Trust Center](assets/screenshots/trust-center.png) | ![My Sales](assets/screenshots/my-sales.png) |

| Meine Regeln | Agenten | Merchant Center |
|----------|--------|-----------------|
| ![My Rules](assets/screenshots/my-rules.png) | ![Agents](assets/screenshots/agents.png) | ![Merchant Center](assets/screenshots/merchant-center.png) |

| Vertrauensbelege (Meine Verkäufe → KI-Verkäufe) |
|--------------------------------------|
| ![Trust Receipts](assets/screenshots/ai-receipts.png) |

Jede Agententransaktion erzeugt einen kryptografisch signierten **Vertrauensbeleg (Trust Receipt)** — einen manipulationssicheren Datensatz (kompatibel mit eIDAS / eSIGN), der unter **Meine Verkäufe → KI-Verkäufe** aufgeführt wird.

## Funktionen

Trusteed for WooCommerce ist ein **schlanker Connector**, der Ihren Produktkatalog mit dem wachsenden Ökosystem von KI-Einkaufsagenten verbindet — mithilfe des **Model Context Protocol (MCP)**, einem offenen Standard von Anthropic. Das Plugin verarbeitet niemals Zahlungen und greift nie auf sensible Kundendaten zu: der Checkout findet immer in Ihrem **nativen WooCommerce-Checkout** statt.

- **MCP-Tools für Agenten** — `search_products`, `browse_categories`, `get_product_details` und `create_cart` (mit Weiterleitung zum nativen WooCommerce-Checkout)
- **Automatische Katalogsynchronisierung** — Produkte werden über WooCommerce-Hooks bei Erstellung/Aktualisierung/Löschung synchronisiert, einschließlich Lagerbestandsänderungen; eine vollständige manuelle Synchronisierung ist über die Einstellungsseite verfügbar. Es werden ausschließlich öffentliche Katalogdaten übermittelt (Titel, Beschreibungen, Preise, Bilder, Kategorien, Lagerbestand) — niemals personenbezogene Kundendaten, Bestellungen oder Zahlungsinformationen
- **Verifizierung des Agenten-Tokens** — `create_cart` leitet das JWS-Token des Agenten an den Checkout weiter, sodass die Signatur-/Replay-Verifizierung (R002) im normalen Ablauf ausgeführt wird
- **Freigabeschranke (HITL)** — konfigurierbare Human-in-the-Loop-Freigabe für hochwertige Agentenbestellungen
- **SSRF-Härtung** — Shop-/API-URLs werden gegen eine exakte Host-Allowlist sowie RFC1918-/IPv6-ULA-/Cloud-IMDS-Sperrlisten validiert
- **Fail-Closed-Standardeinstellungen** — kein Versand, wenn das Enforcement-Secret leer ist; bei erneuter Verbindung ist ein Nachweis des Domainbesitzes erforderlich (Schutz vor Übernahme durch andere Händler)

## Kompatibilität

| Komponente | Unterstützt |
|-----------|-----------|
| WordPress | 6.0 – 6.9 |
| WooCommerce | 8.0 – 10.6 |
| PHP | 7.4+ (getestet mit 8.0–8.3) |

## Voraussetzungen

- WordPress 6.0+ mit WooCommerce 8.0+
- PHP 7.4 oder neuer
- Ein Trusteed-Konto — [kostenlos registrieren auf trusteed.xyz](https://trusteed.xyz)

## Installation

### Manueller Upload (empfohlen)

1. **Laden Sie die installierbare `.zip`** aus dem neuesten GitHub-Release herunter:
   [**⬇ trusteed-agentic-commerce-woocommerce-2.0.1.zip**](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases/latest/download/trusteed-agentic-commerce-woocommerce-2.0.1.zip)
   — oder durchsuchen Sie alle Versionen auf der [Releases-Seite](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases).
2. Im WordPress-Admin: **Plugins → Installieren → Plugin hochladen**.
3. Wählen Sie die heruntergeladene Datei `trusteed-agentic-commerce-woocommerce-2.0.1.zip` aus und klicken Sie auf **Jetzt installieren**.
4. Klicken Sie auf **Aktivieren**.

### Aus dem Quellcode (die Zip selbst bauen)

```bash
git clone https://github.com/Trusteedxyz/agentic-commerce-woocommerce.git
cd agentic-commerce-woocommerce
bash build-zip.sh        # outputs dist/trusteed-agentic-commerce-woocommerce-<version>.zip
```

## Konfiguration

1. Melden Sie sich in Ihrem WordPress-**Admin** an.
2. Gehen Sie zu **WooCommerce → Trusteed** (oder dem Menüpunkt **Trusteed**).
3. Geben Sie Ihren **API-Schlüssel** von [app.trusteed.xyz/settings](https://app.trusteed.xyz/settings) ein.
4. Klicken Sie auf **Speichern & Verbinden** — das Plugin testet die Konnektivität, registriert Ihren Shop und synchronisiert Ihren Katalog automatisch.

Nach der Verbindung kann jeder MCP-kompatible Agent (Claude, ChatGPT oder benutzerdefinierte Agenten, erstellt mit LangChain, CrewAI, Vercel AI SDK usw.) Ihre Produkte durchsuchen, Kategorien durchstöbern, Produktdetails ansehen und Warenkörbe erstellen. Wenn der Kunde zum Kauf bereit ist, leitet der Agent ihn zu Ihrem nativen WooCommerce-Checkout weiter, wo Ihre bestehenden Zahlungs-Gateways (Stripe, PayPal, …) die Zahlung abwickeln.

Eine ausführliche Anleitung für Händler finden Sie unter [`docs/MERCHANT_INSTALLATION_GUIDE.md`](docs/MERCHANT_INSTALLATION_GUIDE.md).

## FAQ

**Welche Daten werden übermittelt?** Nur Ihr öffentlicher Produktkatalog (Titel, Preise, Beschreibungen, Bilder, Kategorien, Lagerstatus). Keine personenbezogenen Kundendaten, Zahlungsdaten oder Bestellhistorie. Die gesamte Kommunikation erfolgt über HTTPS.

**Welche Agenten werden unterstützt?** Jeder MCP-kompatible Agent: Claude (Anthropic), ChatGPT (OpenAI) sowie benutzerdefinierte Agenten, erstellt mit LangChain, CrewAI, Vercel AI SDK oder jedem Framework, das das Model Context Protocol unterstützt.

**Verlangsamt es meinen Shop?** Nein. Das Plugin kommuniziert nur bei Katalogänderungen mit Trusteed — es fügt weder dem Laden der Storefront-Seiten noch dem Kunden-Checkout zusätzlichen Overhead hinzu.

## Änderungsprotokoll

### 2.0.1

Kritischer Aktivierungs- und Sicherheits-Hotfix (Codex-Audit). Behebt eine halbfertige `AGENTICMCP_*` → `TRUSTEED_*`-Umbenennung, die die Aktivierung in 2.0.0 verhinderte; `create_cart` leitet nun das JWS-Token des Agenten weiter, sodass die R002-Verifizierung ausgeführt wird; der REST-Client validiert den API-Basis-Host gegen eine exakte Allowlist.

### 2.0.0

Sicherheits- und Zuverlässigkeits-Sprint. 2-Phasen-Trennung mit Bestätigungstoken; erneute Verbindung erfordert einen Nachweis des Domainbesitzes (`/.well-known/amcp-verify.txt`); echter Cart-Bridge-Endpunkt für `create_cart`; Wiederholungsversuche des Agenten-Event-Webhooks mit exponentiellem Backoff; SSRF-Härtung; Fail-Closed-Standardeinstellungen für die Durchsetzung.

## Support

- Support-E-Mail: support@trusteed.xyz
- GitHub-Issues: [github.com/Trusteedxyz/agentic-commerce-woocommerce/issues](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/issues)

## Lizenz

GPL-2.0-or-later. Vollständiger Text siehe [LICENSE](LICENSE).
