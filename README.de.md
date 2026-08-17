[English](README.md) | [Español](README.es.md) | [Français](README.fr.md) | **Deutsch**

# Trusteed Agentic Commerce für WooCommerce

Ermöglichen Sie es den neuen Online-Käufern, den KI-Agenten, sicher und zuverlässig in Ihrem Shop einzukaufen — dank Trusteed: dem Netzwerk, das Vertrauen zwischen Unternehmen und Agenten fördert.

- **Legen Sie Ihre Geschäftsregeln fest**: wem Sie den Kauf erlauben, bis zu welchem Betrag, welche Kategorien Sie Agenten nicht anbieten möchten, legen Sie Preisgrenzen fest, halten Sie Lagerbestände aufrecht, um sich vor potenziell betrügerischen Agenten zu schützen, und mehr.
- **Manipulationserkennende Belege**: wir erstellen elektronisch signierte, kryptografisch manipulationserkennende Belege, die festhalten, was der Agent tatsächlich getan hat — ein überprüfbarer Nachweis der Agentenintegrität. Sie sind darauf ausgelegt, dem EU-Standard für elektronische Signaturen (eIDAS) sowie eSIGN (USA) zu folgen, sind aber noch keine *qualifizierte* Signatur bzw. kein qualifizierter Zeitstempel — für sich allein sind sie daher kein fertiger Streitfallnachweis für Bank oder Gericht.
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

Jede Agententransaktion erzeugt einen kryptografisch signierten **Vertrauensbeleg (Trust Receipt)** — einen manipulationssicheren Datensatz (kompatibel mit eIDAS / eSIGN), der unter **Meine Verkäufe → KI-Verkäufe** aufgeführt wird. Klicke auf eine Zeile, um alle Details zu sehen (Agenten-ID, aufgerufenes Tool, Input-/Output-Hashes, JWS) und den Beleg als ZIP herunterzuladen. Der Export ist ein überprüfbarer Nachweis der Agentenintegrität: eine nützliche Absicherung, wenn ein Käufer behauptet, die Bestellung nie aufgegeben zu haben — er ersetzt jedoch für sich allein nicht die Nachweise, die eine Bank oder ein Gericht im Streitfall verlangen kann.

## Funktionen

Trusteed for WooCommerce ist ein **schlanker Connector**, der Ihren Produktkatalog mit dem wachsenden Ökosystem von KI-Einkaufsagenten verbindet — mithilfe des **Model Context Protocol (MCP)**, einem offenen Standard von Anthropic. Das Plugin verarbeitet niemals Zahlungen und greift nie auf sensible Kundendaten zu: der Checkout findet immer in Ihrem **nativen WooCommerce-Checkout** statt.

- **MCP-Tools für Agenten** — `search_products`, `browse_categories`, `get_product_details` und `create_cart` (mit Weiterleitung zum nativen WooCommerce-Checkout)
- **Automatische Katalogsynchronisierung** — Produkte werden über WooCommerce-Hooks bei Erstellung/Aktualisierung/Löschung synchronisiert, einschließlich Lagerbestandsänderungen; eine vollständige manuelle Synchronisierung ist über die Einstellungsseite verfügbar. Es werden ausschließlich öffentliche Katalogdaten übermittelt (Titel, Beschreibungen, Preise, Bilder, Kategorien, Lagerbestand) — niemals personenbezogene Kundendaten, Bestellungen oder Zahlungsinformationen
- **Verifizierung des Agenten-Tokens** — `create_cart` leitet das JWS-Token des Agenten an den Checkout weiter, sodass die Signatur-/Replay-Verifizierung (R002) im normalen Ablauf ausgeführt wird
- **Freigabeschranke (HITL)** — konfigurierbare Human-in-the-Loop-Freigabe für hochwertige Agentenbestellungen
- **SSRF-Härtung** — die API-Basis-URL mit Zugangsdaten muss HTTPS sein und auf einer exakten Host-Allowlist stehen; Cloud-IMDS (`169.254.0.0/16`, `100.100.100.200`, `metadata.google.internal`), IPv6-Unique-Local (`fc00::/7`) und Link-Local (`fe80::/10`) werden in jeder Umgebung gesperrt, und Loopback / RFC1918 funktionieren nur nach ausdrücklicher Aktivierung von `TRUSTEED_ALLOW_LOCAL_API_BASE` (standardmäßig aus)
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
   [**⬇ Neuestes Release — trusteed-agentic-commerce-woocommerce-&lt;Version&gt;.zip**](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases/latest)
   — oder durchsuchen Sie alle Versionen auf der [Releases-Seite](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases).
2. Im WordPress-Admin: **Plugins → Installieren → Plugin hochladen**.
3. Wählen Sie die heruntergeladene `.zip`-Datei aus und klicken Sie auf **Jetzt installieren**.
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

### 2.2.2

- **Sicherheitsfix** — der API-Client akzeptierte in **jeder** Umgebung eine Basis-URL im Loopback- oder RFC1918-Bereich (`10.*`, `172.16–31.*`, `192.168.*`, `localhost`, `127.*`), auch über einfaches HTTP. Eine Installation, deren API-URL umgeleitet worden war, hätte ihre `X-AgenticMCP-Key`-Zugangsdaten an eine interne Adresse geschickt. Dieser Entwicklungsmodus muss nun ausdrücklich aktiviert werden und ist standardmäßig aus: nur über `TRUSTEED_ALLOW_LOCAL_API_BASE` oder den WordPress-Umgebungstyp `local` — dieselbe Absicherung, die `Trusteed_Token_Broker` über `WP_DEBUG` längst anwandte und die diesem Client fehlte.
- **Sicherheitsfix** — Cloud-Instanz-Metadaten und interne IPv6-Bereiche werden jetzt in jeder Umgebung gesperrt, *auch* im Entwicklungsmodus, der sie vorher alle wieder öffnete: `169.254.0.0/16` (IMDS), Alibaba `100.100.100.200`, `metadata.google.internal`, Unique-Local `fc00::/7`, Link-Local `fe80::/10`. Sie liefern zudem einen eigenen Fehlercode statt der irreführenden Meldung „HTTPS-URL konfigurieren".
- **Behoben** — IPv6-Hosts trafen auf keine einzige Prüfung zu: `parse_url()` gibt sie in Klammern zurück (`[::1]`), der Loopback-Eintrag `::1` war also toter Code.
- **Datenschutzfix** — beim Deinstallieren blieben 21 Options-Zeilen zurück, darunter drei verschlüsselte Secrets (`trusteed_embed_wp_secret`, `trusteed_enforcement_hmac_secret`, `trusteed_woo_webhook_secret`) und die veralteten `amcp_*`-Aliase, die der Options-Accessor weiterhin als Rückfall liest — eine Neuinstallation konnte so ein altes Secret wiederbeleben. `uninstall.php` räumt jetzt alle drei Namensräume sowie die Snapshot- und JWKS-Transients auf. Ein neuer Test durchsucht den Quellcode nach jedem schreibbaren Options-Schlüssel und schlägt fehl, wenn die Deinstallationsliste zurückfällt.
- **Doku-Fix** — Vertrauensbelege wurden als „Nachweis der tatsächlichen Transaktion im Streitfall" beschrieben. Das Produkt selbst sagt das Gegenteil: ein überprüfbarer Integritätsnachweis, kein fertiger Streitfallnachweis für Bank oder Gericht. Entsprechend korrigiert.
- **Doku-Fix** — die FAQ zur Deaktivierung behauptete, Deaktivieren trenne den Shop und es blieben keine Restdaten auf unseren Servern. Deaktivieren bewirkt nichts, und beim Trennen bleiben Shop-Datensatz und synchronisierte Produkte erhalten. Korrigiert, mit dokumentiertem Weg für Löschanfragen.
- **Doku-Fix** — der Katalog wurde als „Varianten und Bewertungen" synchronisierend beschrieben; beides wird nicht gesendet. Die Liste der übertragenen Felder ist jetzt exakt.
- **Doku-Fix** — `Tested up to` / `WC tested up to` widersprachen sich zwischen `readme.txt` (6.9 / 10.6) und dem Plugin-Header (6.7 / 9.5). Beide lauten nun 6.9 / 10.6. Die automatisierten Tests laufen gegen WordPress 6.8 mit der aktuellen stabilen WooCommerce auf PHP 8.1–8.2.
- **Doku-Fix** — 404-Links repariert: `/developers`, `/privacy` und `/terms` brauchen das `/en/`-Präfix, und `/support` existiert nicht (ersetzt durch Kontaktformular und GitHub-Issues).

### 2.2.1

- **Behoben** — `browse_categories` lieferte dieselbe mit Trennzeichen umschlossene Zeichenkette sowohl an den maschinenlesbaren als auch an den erzählenden Kanal. `guardMerchantField` umschließt Händlertext standardmäßig mit `<<<MERCHANT_CONTENT_START>>> … <<<MERCHANT_CONTENT_END>>>`, damit ein Agent erkennt "das sind Händlerdaten, keine Anweisung" — aber das Tool verwendete diese bereits umschlossene Zeichenkette auch für `structuredContent`, sodass eine Kategorie namens "Sneakers" im Maschinenkanal als `<<<MERCHANT_CONTENT_START>>>Sneakers<<<MERCHANT_CONTENT_END>>>` erschien. `structuredContent` erhält jetzt den unumschlossenen Wert; die Trennzeichen bleiben nur dort, wo sie ihre Funktion erfüllen — in der Erzählung.
- **Behoben** — die Regel R047 (Mindestbeitrag) hatte kein Formularfeld im Admin-Panel; ihre Parameter existierten im Schema, konnten aber nur über die API gesetzt werden.
- **Behoben** — `MerchantCheckoutConfig` hatte übersetzten Text für einen Leerzustand (`noRails`, vorhanden in `en.ts` und `es.ts`), den die Komponente nie rendert hat, sodass ein Händler ohne konfigurierte Zahlungswege eine unerklärte leere Liste sah.
- **Behoben** — das Admin-Panel-Bundle (`assets/admin-spa/`) wurde unminifiziert ausgeliefert: 869 KB / 25.064 Zeilen statt der 490 KB / 41 Zeilen, die der dokumentierte Build-Befehl tatsächlich erzeugt. Neu aus der Quelle gebaut mit stabilem Ausgabedateinamen (`admin-spa.js`), wie bei den anderen drei Plattform-Konnektoren.

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
