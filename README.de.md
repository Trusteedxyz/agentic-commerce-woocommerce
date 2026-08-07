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

Jede Agententransaktion erzeugt einen kryptografisch signierten **Vertrauensbeleg (Trust Receipt)** — einen manipulationssicheren Datensatz (kompatibel mit eIDAS / eSIGN), der unter **Meine Verkäufe → KI-Verkäufe** aufgeführt wird. Klicke auf eine Zeile, um alle Details zu sehen (Agenten-ID, aufgerufenes Tool, Input-/Output-Hashes, JWS) und den Beleg als ZIP herunterzuladen, um ihn im Streitfall als Nachweis vorzulegen.

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
   [**⬇ trusteed-agentic-commerce-woocommerce-2.2.0.zip**](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases/latest/download/trusteed-agentic-commerce-woocommerce-2.2.0.zip)
   — oder durchsuchen Sie alle Versionen auf der [Releases-Seite](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases).
2. Im WordPress-Admin: **Plugins → Installieren → Plugin hochladen**.
3. Wählen Sie die heruntergeladene Datei `trusteed-agentic-commerce-woocommerce-2.2.0.zip` aus und klicken Sie auf **Jetzt installieren**.
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
3. Geben Sie Ihren **API-Schlüssel** von [trusteed.xyz/dashboard/settings](https://trusteed.xyz/dashboard/settings) ein.
4. Klicken Sie auf **Speichern & Verbinden** — das Plugin testet die Konnektivität, registriert Ihren Shop und synchronisiert Ihren Katalog automatisch.

Nach der Verbindung kann jeder MCP-kompatible Agent (Claude, ChatGPT oder benutzerdefinierte Agenten, erstellt mit LangChain, CrewAI, Vercel AI SDK usw.) Ihre Produkte durchsuchen, Kategorien durchstöbern, Produktdetails ansehen und Warenkörbe erstellen. Wenn der Kunde zum Kauf bereit ist, leitet der Agent ihn zu Ihrem nativen WooCommerce-Checkout weiter, wo Ihre bestehenden Zahlungs-Gateways (Stripe, PayPal, …) die Zahlung abwickeln.

Eine ausführliche Anleitung für Händler finden Sie unter [`docs/MERCHANT_INSTALLATION_GUIDE.md`](docs/MERCHANT_INSTALLATION_GUIDE.md).

## FAQ

**Welche Daten werden übermittelt?** Nur Ihr öffentlicher Produktkatalog (Titel, Preise, Beschreibungen, Bilder, Kategorien, Lagerstatus). Keine personenbezogenen Kundendaten, Zahlungsdaten oder Bestellhistorie. Die gesamte Kommunikation erfolgt über HTTPS.

**Welche Agenten werden unterstützt?** Jeder MCP-kompatible Agent: Claude (Anthropic), ChatGPT (OpenAI) sowie benutzerdefinierte Agenten, erstellt mit LangChain, CrewAI, Vercel AI SDK oder jedem Framework, das das Model Context Protocol unterstützt.

**Verlangsamt es meinen Shop?** Nein. Das Plugin kommuniziert nur bei Katalogänderungen mit Trusteed — es fügt weder dem Laden der Storefront-Seiten noch dem Kunden-Checkout zusätzlichen Overhead hinzu.

## Änderungsprotokoll

### 2.2.0

- **Sicherheitsfix** — der Agent-Token-Verifizierer behandelte `exp` und `iat` als optional: beide Prüfungen hingen an `> 0`, sodass ein Token, das den Claim schlicht wegließ, die Prüfung vollständig umging. Ohne `exp` lief es nie ab; ohne `iat` hatte es kein Höchstalter. Beide sind jetzt verpflichtend, und ein nicht-numerischer Wert wird zurückgewiesen statt gecastet. Der Replay-Schutz war hier bereits fail-closed (ein fehlendes oder fehlerhaftes `jti` wird abgelehnt) — dies schließt die verbleibende Hälfte.
- **Sicherheitsfix** — ein `iat` in der Zukunft wird nun abgelehnt (30s Uhrenabweichung werden toleriert). Zusammen mit dem Höchstalter-Fenster ergab sich eine gleitende Lebensdauer: `jetzt - iat` bleibt klein, solange der Aussteller den Claim nach vorne schiebt — das Token alterte also nie.
- **Fix** — Regel R036 (maximaler Positionswert) las ihre Obergrenze aus einem Parameter namens `maxCents`, von R035 übernommen. Der kanonische Name lautet `maxCentsPerLine` und ist der einzige, den das strikte Schema des Händlerpanels akzeptiert — eine vom Händler konfigurierte Obergrenze hätte die Prüfung nie erreicht. Der kanonische Schlüssel wird jetzt zuerst gelesen; `maxCents` bleibt als Rückfall akzeptiert.
- **Fix** — der sprachübergreifende Konformitätstest löste sein Fixture über einen Pfad auf, den es nur im Entwicklungs-Monorepo gibt, und schlug daher in diesem Repository fehl. Er liest nun die in `tests/fixtures/` mitgelieferte Kopie.
- **Trust Receipts** — das Admin-SPA-Bundle wurde mit der Beleg-Download-Schaltfläche neu gebaut, die einen Beleg über denselben Endpunkt als ZIP exportiert, den auch das gehostete Dashboard nutzt. Die Schaltfläche benennt klar, was dieser Export ist: ein Nachweis der Agenten-Integrität, kein Streitfall-Beweis.

### 2.1.0

- **Rebranding** — interne Klassen, Options-Schlüssel und REST-Routen von `Amcp_`/`amcp_` zu `Trusteed_`/`trusteed_` umbenannt. Abwärtskompatibilität gewahrt: bestehende Installationen funktionieren weiter (Legacy-Optionen `amcp_{key}` werden weiterhin als Fallback gelesen, Legacy-REST-Namespaces bleiben neben den neuen registriert, das Legacy-Präfix verschlüsselter Werte wird weiterhin entschlüsselt).
- **Fix** — die R043-HITL-Payload wird nun durchgängig weitergereicht, sodass ein BLOCK eine Human-in-the-Loop-Pause anzeigen kann, statt eines harten Blocks, der die Kaufabsicht verliert.
- **Kritischer Fix** — das kompilierte Admin-SPA-Bundle (`assets/admin-spa/`) fehlte komplett im verteilten Paket; das Trusteed-Admin-Panel zeigte bei jeder Installation einen Fehler "Bundle nicht kompiliert". Das Bundle wird jetzt korrekt mitgeliefert.
- Härtung bei Abrechnungs-Webhooks, Checkout-Durchsetzung, Katalog-Synchronisation und Warenkorb-Signalen.

### 2.0.2

Fix für die Durchsetzung von Checkout-Regeln. Händlerregeln (Höchstbetrag, gesperrte Länder, Geschäftszeiten) wurden bei organischen Checkouts ohne Agenten komplett übersprungen — sie gelten jetzt universell. Ein Offline-Sicherheitsventil-Evaluator wurde hinzugefügt, der diese Regeln lokal durchsetzt, wenn die entfernte Regel-API nicht erreichbar ist.

### 2.0.1

Kritischer Aktivierungs- und Sicherheits-Hotfix (Codex-Audit). Behebt eine halbfertige `AGENTICMCP_*` → `TRUSTEED_*`-Umbenennung, die die Aktivierung in 2.0.0 verhinderte; `create_cart` leitet nun das JWS-Token des Agenten weiter, sodass die R002-Verifizierung ausgeführt wird; der REST-Client validiert den API-Basis-Host gegen eine exakte Allowlist.

### 2.0.0

Sicherheits- und Zuverlässigkeits-Sprint. 2-Phasen-Trennung mit Bestätigungstoken; erneute Verbindung erfordert einen Nachweis des Domainbesitzes (`/.well-known/amcp-verify.txt`); echter Cart-Bridge-Endpunkt für `create_cart`; Wiederholungsversuche des Agenten-Event-Webhooks mit exponentiellem Backoff; SSRF-Härtung; Fail-Closed-Standardeinstellungen für die Durchsetzung.

## Support

- Support-E-Mail: support@trusteed.xyz
- GitHub-Issues: [github.com/Trusteedxyz/agentic-commerce-woocommerce/issues](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/issues)

## Lizenz

GPL-2.0-or-later. Vollständiger Text siehe [LICENSE](LICENSE).
