[English](README.md) | [Español](README.es.md) | [Français](README.fr.md) | **Deutsch**

# Trusteed Agentic Commerce für WooCommerce

KI-Agenten sind eine neue Art von Online-Käufern. Mit Trusteed, dem Netzwerk, das Unternehmen und Agenten verbindet, können sie zu Ihren Bedingungen in Ihrem Shop einkaufen.

- Legen Sie Ihre Geschäftsregeln fest: wer kaufen darf, bis zu welchem Betrag, welche Kategorien Sie Agenten nicht anbieten, Preisgrenzen, Lagerbestände, die Sie vor betrügerischen Agenten schützen, und mehr.
- Erhalten Sie signierte Belege. Jede Transaktion erzeugt einen kryptografisch signierten Beleg, an dem sich jede Manipulation erkennen lässt und den Sie im Streitfall als Nachweis des Kaufs verwenden können. An eIDAS (EU) und an eSIGN (USA) ausgerichtet.
- Sehen Sie, was Agenten tun: wie viel sie ausgeben, was sie kaufen und wie oft.
- Sperren Sie Agenten, die gefährlich wirken oder Probleme verursachen.
- Nehmen Sie Käufe in digitalen Währungen über das X402-Protokoll an.
- Lassen Sie Agenten und Händler direkt miteinander handeln, Peer-to-Peer.

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

Jede Agententransaktion erzeugt einen signierten Vertrauensbeleg (Trust Receipt), einen Datensatz, an dem sich jede Manipulation erkennen lässt (an eIDAS und an eSIGN ausgerichtet), der unter **Meine Verkäufe → KI-Verkäufe** aufgeführt wird. Mit einem Klick auf eine Zeile sehen Sie die Details: Agenten-ID, aufgerufenes Tool, Input- und Output-Hashes, JWS. Den Beleg können Sie außerdem als ZIP-Datei herunterladen und für den Streitfall aufbewahren.

## Funktionen

Das Plugin verbindet Ihren Produktkatalog über das **Model Context Protocol (MCP)**, einen offenen Standard von Anthropic, mit KI-Einkaufsagenten. Es verarbeitet nie Zahlungen und greift nicht auf sensible Kundendaten zu. Der Checkout findet immer in Ihrem nativen WooCommerce-Checkout statt.

- MCP-Tools für Agenten: `search_products`, `browse_categories`, `get_product_details` und `create_cart`, das den Käufer zum nativen WooCommerce-Checkout weiterleitet.
- Automatische Katalogsynchronisierung. Produkte werden über WooCommerce-Hooks synchronisiert, wenn Sie sie erstellen, aktualisieren oder löschen, Lagerbestandsänderungen eingeschlossen, und auf der Einstellungsseite können Sie eine vollständige manuelle Synchronisierung starten. Ihren Shop verlassen nur öffentliche Katalogdaten (Titel, Beschreibungen, Preise, Bilder, Kategorien, Lagerbestand), nie personenbezogene Kundendaten, Bestellungen oder Zahlungsinformationen.
- Verifizierung des Agenten-Tokens: `create_cart` reicht das JWS-Token des Agenten an den Checkout weiter, sodass die Signatur- und Replay-Verifizierung (R002) im normalen Ablauf läuft.
- Freigabeschranke (HITL): konfigurierbare Human-in-the-Loop-Freigabe für hochwertige Agentenbestellungen.
- SSRF-Härtung: Shop- und API-URLs werden gegen eine exakte Host-Allowlist und gegen die Sperrlisten für RFC1918, IPv6 ULA und Cloud-IMDS geprüft.
- Fail-Closed-Standardeinstellungen: Es wird nichts versendet, wenn das Enforcement-Secret leer ist, und für eine erneute Verbindung müssen Sie den Besitz der Domain nachweisen. Das schützt vor der Übernahme durch andere Händler.

## Kompatibilität

| Komponente | Unterstützt |
|-----------|-----------|
| WordPress | 6.0 – 6.9 |
| WooCommerce | 8.0 – 10.6 |
| PHP | 7.4+ (getestet mit 8.0–8.3) |

## Voraussetzungen

- WordPress 6.0+ mit WooCommerce 8.0+
- PHP 7.4 oder neuer
- Ein Trusteed-Konto ([kostenlos registrieren auf trusteed.xyz](https://trusteed.xyz))

## Installation

### Manueller Upload (empfohlen)

1. **Laden Sie die installierbare `.zip`** aus dem neuesten GitHub-Release herunter:
   [**⬇ trusteed-agentic-commerce-woocommerce-2.1.0.zip**](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases/latest/download/trusteed-agentic-commerce-woocommerce-2.1.0.zip)
   oder durchsuchen Sie alle Versionen auf der [Releases-Seite](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases).
2. Im WordPress-Admin: **Plugins → Installieren → Plugin hochladen**.
3. Wählen Sie die heruntergeladene Datei `trusteed-agentic-commerce-woocommerce-2.1.0.zip` aus und klicken Sie auf **Jetzt installieren**.
4. Klicken Sie auf **Aktivieren**.

### Aus dem Quellcode (die Zip selbst bauen)

```bash
git clone https://github.com/Trusteedxyz/agentic-commerce-woocommerce.git
cd agentic-commerce-woocommerce
bash build-zip.sh        # outputs dist/trusteed-agentic-commerce-woocommerce-<version>.zip
```

## Konfiguration

1. Melden Sie sich in Ihrem WordPress-**Admin** an.
2. Gehen Sie zu **WooCommerce → Trusteed** (oder zum Menüpunkt **Trusteed**).
3. Geben Sie Ihren **API-Schlüssel** von [app.trusteed.xyz/settings](https://app.trusteed.xyz/settings) ein.
4. Klicken Sie auf **Speichern & Verbinden**. Das Plugin testet die Verbindung, registriert Ihren Shop und synchronisiert Ihren Katalog.

Nach der Verbindung kann jeder MCP-kompatible Agent (Claude, ChatGPT oder ein eigener Agent, gebaut mit LangChain, CrewAI, Vercel AI SDK und ähnlichen Tools) Ihre Produkte durchsuchen, Kategorien ansehen, Produktdetails lesen und Warenkörbe anlegen. Wenn der Kunde kaufen möchte, schickt ihn der Agent zu Ihrem nativen WooCommerce-Checkout, und Ihre bestehenden Zahlungs-Gateways (Stripe, PayPal, …) nehmen die Zahlung an.

Die vollständige Anleitung für Händler finden Sie in [`docs/MERCHANT_INSTALLATION_GUIDE.md`](docs/MERCHANT_INSTALLATION_GUIDE.md).

## FAQ

**Welche Daten werden übermittelt?** Nur Ihr öffentlicher Produktkatalog: Titel, Preise, Beschreibungen, Bilder, Kategorien und Lagerstatus. Keine personenbezogenen Kundendaten, Zahlungsdaten oder Bestellhistorie. Die gesamte Kommunikation läuft über HTTPS.

**Welche Agenten werden unterstützt?** Jeder MCP-kompatible Agent: Claude (Anthropic), ChatGPT (OpenAI) sowie eigene Agenten, gebaut mit LangChain, CrewAI, Vercel AI SDK oder jedem anderen Framework, das das Model Context Protocol unterstützt.

**Verlangsamt es meinen Shop?** Es fügt im Checkout eine Anfrage hinzu. Außerdem kommuniziert das Plugin mit Trusteed, wenn sich Ihr Katalog ändert und wenn ein Agent in Ihrem Shop handelt. Wenn ein Kunde eine Bestellung aufgibt, ob Agent oder Mensch, bittet das Plugin Trusteed, Ihre Regeln auszuwerten. Diese Anfrage läuft nach 5 Sekunden ab. Ist Trusteed nicht erreichbar, wendet das Plugin Ihre Regeln aus dem zuletzt geladenen signierten Snapshot an. Ist auch das nicht möglich, richtet sich das Ergebnis nach dem Fehlermodus, den Sie in den Plugin-Einstellungen gewählt haben: die Bestellung blockieren oder zulassen.

## Änderungsprotokoll

### 2.1.0

- Rebranding: Interne Klassen, Options-Schlüssel und REST-Routen wurden von `Amcp_`/`amcp_` zu `Trusteed_`/`trusteed_` umbenannt. Bestehende Installationen funktionieren weiter. Legacy-Optionen `amcp_{key}` werden weiterhin als Fallback gelesen, Legacy-REST-Namespaces bleiben neben den neuen registriert, und das Legacy-Präfix verschlüsselter Werte wird weiterhin entschlüsselt.
- Fix: Die R043-HITL-Payload wird jetzt durchgängig weitergereicht, sodass ein BLOCK eine Human-in-the-Loop-Pause anzeigen kann statt eines harten Blocks, der die Kaufabsicht verliert.
- Fix (kritisch): Das kompilierte Admin-SPA-Bundle (`assets/admin-spa/`) fehlte im ausgelieferten Paket. Das Trusteed-Admin-Panel zeigte bei jeder Installation den Fehler „Bundle nicht kompiliert“. Das Bundle wird jetzt korrekt mitgeliefert.
- Härtung bei Abrechnungs-Webhooks, Checkout-Durchsetzung, Katalogsynchronisierung und Warenkorb-Signalen.

### 2.0.2

Fix für die Durchsetzung von Checkout-Regeln. Händlerregeln (Höchstbetrag, gesperrte Länder, Geschäftszeiten) wurden bei organischen Checkouts ohne Agenten komplett übersprungen. Jetzt gelten sie für jeden Checkout. Neu ist ein Offline-Sicherheitsventil-Evaluator, der diese Regeln lokal durchsetzt, wenn die entfernte Regel-API nicht erreichbar ist.

### 2.0.1

Kritischer Hotfix für Aktivierung und Sicherheit (Codex-Audit). Behebt eine halbfertige Umbenennung von `AGENTICMCP_*` zu `TRUSTEED_*`, die in 2.0.0 die Aktivierung verhinderte. `create_cart` leitet jetzt das JWS-Token des Agenten weiter, sodass die R002-Verifizierung läuft. Der REST-Client prüft den API-Basis-Host gegen eine exakte Allowlist.

### 2.0.0

Sprint für Sicherheit und Zuverlässigkeit. Das Trennen läuft jetzt in zwei Phasen mit einem Bestätigungstoken. Für die erneute Verbindung ist ein Nachweis des Domainbesitzes nötig (`/.well-known/amcp-verify.txt`). `create_cart` bekommt einen echten Cart-Bridge-Endpunkt. Der Webhook für Agenten-Events wiederholt fehlgeschlagene Zustellungen mit exponentiellem Backoff. Dazu kommen SSRF-Härtung und Fail-Closed-Standardeinstellungen für die Durchsetzung.

## Support

- Support-E-Mail: support@trusteed.xyz
- GitHub-Issues: [github.com/Trusteedxyz/agentic-commerce-woocommerce/issues](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/issues)

## Lizenz

GPL-2.0-or-later. Den vollständigen Text finden Sie in [LICENSE](LICENSE).
