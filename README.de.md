[English](README.md) | [Español](README.es.md) | [Français](README.fr.md) | **Deutsch**

# Trusteed Agentic Commerce für WooCommerce

KI-Agenten sind eine neue Art von Online-Käufern. Mit Trusteed, dem Netzwerk, das Unternehmen und Agenten verbindet, können sie zu Ihren Bedingungen in Ihrem Shop einkaufen.

- Legen Sie Ihre Geschäftsregeln fest: wer kaufen darf, bis zu welchem Betrag, welche Kategorien Sie Agenten nicht anbieten, Preisgrenzen, Lagerbestände, die Sie vor betrügerischen Agenten schützen, und mehr.
- Erhalten Sie signierte Belege. Jede Transaktion erzeugt einen kryptografisch signierten Beleg, an dem sich jede Manipulation erkennen lässt und der festhält, was der Agent tatsächlich getan hat, ein überprüfbarer Nachweis der Agentenintegrität. An eIDAS (EU) und an eSIGN (USA) ausgerichtet, aber noch keine *qualifizierte* Signatur und kein qualifizierter Zeitstempel, sodass er für sich allein kein fertiger Streitfallnachweis für eine Bank oder ein Gericht ist.
- Sehen Sie, was Agenten tun: wie viel sie ausgeben, was sie kaufen und wie oft.
- Sperren Sie Agenten, die gefährlich wirken oder Probleme verursachen.
- Nehmen Sie Käufe in digitalen Währungen über das X402-Protokoll an.
- Lassen Sie Agenten und Händler direkt miteinander handeln, Peer-to-Peer.
- Prüfen Sie, ob Agenten bei Ihnen einkaufen können. Das Agent-Readiness-Dashboard testet live, ob KI-Agenten heute wirklich in Ihrem Shop einkaufen können. Es bietet drei unabhängige Ansichten (was andere sagen, was Sie versprechen im Vergleich zu dem, was Sie tun, was wir beobachtet haben), und nichts wird bewertet, bevor es verifiziert ist.

## Screenshots

Jedes Panel unten entspricht einem Eintrag im **Trusteed**-Menü innerhalb von WooCommerce.

| Startseite | Trust Center | Meine Verkäufe |
|------|--------------|----------|
| ![Home](assets/screenshots/home.png) | ![Trust Center](assets/screenshots/trust-center.png) | ![My Sales](assets/screenshots/my-sales.png) |

| Meine Regeln | Agenten | Merchant Center |
|----------|--------|-----------------|
| ![My Rules](assets/screenshots/my-rules.png) | ![Agents](assets/screenshots/agents.png) | ![Merchant Center](assets/screenshots/merchant-center.png) |

| Vertrauensbelege (Meine Verkäufe → KI-Verkäufe) | Agent Readiness |
|--------------------------------------|------------------|
| ![Trust Receipts](assets/screenshots/ai-receipts.png) | ![Agent Readiness](assets/screenshots/agent-readiness.png) |

Jede Agententransaktion erzeugt einen signierten Vertrauensbeleg (Trust Receipt), einen Datensatz, an dem sich jede Manipulation erkennen lässt (an eIDAS und an eSIGN ausgerichtet), der unter **Meine Verkäufe → KI-Verkäufe** aufgeführt wird. Mit einem Klick auf eine Zeile sehen Sie die Details: Agenten-ID, aufgerufenes Tool, Input- und Output-Hashes, JWS. Den Beleg können Sie außerdem als ZIP-Datei herunterladen. Der Export ist ein überprüfbarer Nachweis der Agentenintegrität. Er dient als Absicherung, wenn ein Käufer behauptet, die Bestellung nie aufgegeben zu haben, ersetzt aber für sich allein nicht die Nachweise, die eine Bank oder ein Gericht im Streitfall verlangen kann.

## Funktionen

Das Plugin verbindet Ihren Produktkatalog über das **Model Context Protocol (MCP)**, einen offenen Standard von Anthropic, mit KI-Einkaufsagenten. Es verarbeitet nie Zahlungen und greift nicht auf sensible Kundendaten zu. Der Checkout findet immer in Ihrem nativen WooCommerce-Checkout statt.

- MCP-Tools für Agenten: `search_products`, `browse_categories`, `get_product_details` und `create_cart`, das den Käufer zum nativen WooCommerce-Checkout weiterleitet.
- Automatische Katalogsynchronisierung. Produkte werden über WooCommerce-Hooks synchronisiert, wenn Sie sie erstellen, aktualisieren oder löschen, Lagerbestandsänderungen eingeschlossen, und auf der Einstellungsseite können Sie eine vollständige manuelle Synchronisierung starten. Ihren Shop verlassen nur öffentliche Katalogdaten (Titel, Beschreibungen, Preise, Bilder, Kategorien, Lagerbestand), nie personenbezogene Kundendaten, Bestellungen oder Zahlungsinformationen.
- Verifizierung des Agenten-Tokens: `create_cart` reicht das JWS-Token des Agenten an den Checkout weiter, sodass die Signatur- und Replay-Verifizierung (R002) im normalen Ablauf läuft.
- Freigabeschranke (HITL): konfigurierbare Human-in-the-Loop-Freigabe für hochwertige Agentenbestellungen.
- SSRF-Härtung: Die API-Basis-URL, die Ihre Zugangsdaten trägt, muss HTTPS sein und auf einer exakten Host-Allowlist stehen. Cloud-IMDS-Adressen (`169.254.0.0/16`, `100.100.100.200`, `metadata.google.internal`), IPv6-Unique-Local (`fc00::/7`) und Link-Local (`fe80::/10`) werden in jeder Umgebung gesperrt. Loopback und RFC1918 funktionieren nur nach ausdrücklicher Aktivierung von `TRUSTEED_ALLOW_LOCAL_API_BASE`, standardmäßig aus.
- Fail-Closed-Standardeinstellungen: Es wird nichts versendet, wenn das Enforcement-Secret leer ist, und für eine erneute Verbindung müssen Sie den Besitz der Domain nachweisen. Das schützt vor der Übernahme durch andere Händler.

## Kompatibilität

| Komponente | Unterstützt |
|-----------|-----------|
| WordPress | 6.0 – 7.0 |
| WooCommerce | 8.0 – 11.0 |
| PHP | 7.4+ (getestet mit 8.0–8.3) |

## Voraussetzungen

- WordPress 6.0+ mit WooCommerce 8.0+
- PHP 7.4 oder neuer
- Ein Trusteed-Konto ([kostenlos registrieren auf trusteed.xyz](https://trusteed.xyz))

## Installation

### Manueller Upload (empfohlen)

1. **Laden Sie die installierbare `.zip`** aus dem neuesten GitHub-Release herunter:
   [**⬇ Neuestes Release: trusteed-agentic-commerce-woocommerce-&lt;Version&gt;.zip**](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases/latest)
   oder durchsuchen Sie alle Versionen auf der [Releases-Seite](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases).
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
2. Gehen Sie zu **WooCommerce → Trusteed** (oder zum Menüpunkt **Trusteed**).
3. Geben Sie Ihre Trusteed-E-Mail-Adresse und Ihr Passwort ein und klicken Sie auf **Connect my store**. Wenn Sie noch kein Konto haben, wird eines für Sie angelegt. Wenn Sie lieber kein Passwort verwenden, klicken Sie stattdessen auf **Connect with API key** und fügen Ihren API-Schlüssel von [trusteed.xyz/dashboard/settings](https://trusteed.xyz/dashboard/settings) ein. Die Oberfläche des Plugins ist auf Englisch, außer auf einer spanischsprachigen WordPress-Seite.
4. Das Plugin registriert Ihren Shop und synchronisiert Ihren Katalog. **Test connection** prüft die Verbindung, und **Sync catalog now** startet jederzeit eine vollständige Synchronisierung.

Nach der Verbindung kann jeder MCP-kompatible Agent (Claude, ChatGPT oder ein eigener Agent, gebaut mit LangChain, CrewAI, Vercel AI SDK und ähnlichen Tools) Ihre Produkte durchsuchen, Kategorien ansehen, Produktdetails lesen und Warenkörbe anlegen. Wenn der Kunde kaufen möchte, schickt ihn der Agent zu Ihrem nativen WooCommerce-Checkout, und Ihre bestehenden Zahlungs-Gateways (Stripe, PayPal, …) nehmen die Zahlung an.

Die vollständige Anleitung für Händler finden Sie in [`docs/MERCHANT_INSTALLATION_GUIDE.md`](docs/MERCHANT_INSTALLATION_GUIDE.md).

## FAQ

**Welche Daten werden übermittelt?** Nur Ihr öffentlicher Produktkatalog: Titel, Preise, Beschreibungen, Bilder, Kategorien und Lagerstatus. Keine personenbezogenen Kundendaten, Zahlungsdaten oder Bestellhistorie. Die gesamte Kommunikation läuft über HTTPS.

**Welche Agenten werden unterstützt?** Jeder MCP-kompatible Agent: Claude (Anthropic), ChatGPT (OpenAI) sowie eigene Agenten, gebaut mit LangChain, CrewAI, Vercel AI SDK oder jedem anderen Framework, das das Model Context Protocol unterstützt.

**Verlangsamt es meinen Shop?** Es fügt im Checkout eine Anfrage hinzu. Außerdem kommuniziert das Plugin mit Trusteed, wenn sich Ihr Katalog ändert und wenn ein Agent in Ihrem Shop handelt. Wenn ein Kunde eine Bestellung aufgibt, ob Agent oder Mensch, bittet das Plugin Trusteed, Ihre Regeln auszuwerten. Diese Anfrage läuft nach 5 Sekunden ab. Ist Trusteed nicht erreichbar, wendet das Plugin Ihre Regeln aus dem zuletzt geladenen signierten Snapshot an. Ist auch das nicht möglich, richtet sich das Ergebnis nach der Option `trusteed_failure_mode`: `enforce` (der Standard) blockiert die Bestellung, `observe` lässt sie zu.

## Das Dashboard zur Agenten-Bereitschaft

**Finden mich Agenten?** ist eine Seite in Ihrem Verwaltungsbereich, die eine
einzige Frage beantwortet: Wenn ein KI-Einkaufsagent Ihren Shop besucht, bekommt
er das, was Sie glauben, dass er bekommt?

Es wird nie eine einzelne Note angezeigt. Drei Spalten, die nicht gemittelt
werden, weil sie unterschiedliche Fragen beantworten und sich zu Recht
widersprechen können:

| Spalte | Was sie bedeutet |
| --- | --- |
| **Was ein Dritter sagt** | Das Urteil eines externen Scanners, wörtlich zitiert. Nie in eine eigene Skala übersetzt: Sobald man die Note eines anderen umrechnet, korrigiert man seine eigene Prüfung |
| **Stimmt überein, was Sie sagen, mit dem, was Sie tun?** | 16 Prüfungen, die das, was Ihr Shop **ankündigt**, mit dem vergleichen, was er **tatsächlich antwortet**. Genau das kann kein externer Scanner leisten: Es braucht Ihre Zugangsdaten |
| **Was wir gesehen haben** | Echter Agentenverkehr im gewählten Zeitraum: welche Agenten kamen, welche Werkzeuge sie nutzten, wie weit sie kamen und woran sie scheiterten |

Eine Prüfung, die nicht durchgeführt werden konnte, wird als **nicht geprüft**
ausgewiesen, mit Begründung. Sie wird nie stillschweigend verworfen und nie als
bestanden gewertet. «Wir konnten nicht nachsehen» und «wir haben nachgesehen und
es war in Ordnung» sind verschiedene Antworten, und die Seite sagt, welche gilt.

### Was jede Prüfung betrachtet

| Prüfung | Was sie erkennt |
| --- | --- |
| C1 | Sie kündigen Werkzeuge an, die Ihr Shop nicht bereitstellt |
| C2 | Sie kündigen ein Checkout-Protokoll an, dessen Endpunkt nicht antwortet |
| C3 | Der Katalogpreis ist nicht der berechnete Preis |
| C4 | Als verfügbar angekündigt, obwohl nicht verfügbar |
| C5 | Ihre Rückgaberichtlinie sagt je nach Quelle etwas anderes |
| C6 | Sie kündigen etwas als verfügbar an, das abgeschaltet ist |
| C7 | Aktivierte Regeln, die mangels Daten nicht greifen können |
| C8 | Ihre Regeln beobachten, blockieren aber nicht |
| C9 | Die angekündigte Authentifizierungsmethode funktioniert nicht |
| C10 | Ein Agent kann jeden Betrag ohne Ihre Bestätigung kaufen |
| C11 | Die Verkaufsstelle verwendet abgelaufene Regeln |
| C12 | Vorgänge ohne signierten Beleg |
| C13 | Angekündigte Adressen, die nicht funktionieren |
| C14 | Agenten sehen veraltete Daten Ihres Shops |
| C15 | Identitätsnachweise kurz vor Ablauf |
| C16 | Die zugesagte Lieferzeit ist nicht die eingehaltene |

Einige Prüfungen brauchen mehr als Ihre Einstellungen, und die Seite sagt es,
statt eine Lücke zu lassen:

- **Erfordert einen verbundenen Shop** (C3, C4, C5, C14): Sie vergleichen mit
  Ihrem echten Katalog, und ohne Zugangsdaten gibt es nichts zu vergleichen.
- **Erfordert ausgelieferte Bestellungen** (C16): Vergleicht Zusage und
  tatsächliche Einhaltung, was ohne Historie nicht möglich ist.
- **Diesmal gab es nichts zu vergleichen**: C12 etwa hat nichts zu prüfen, bevor
  ein Agent tatsächlich einen Kauf abgeschlossen hat. Das ist kein Durchfallen.

Die Prüfungen laufen einmal täglich, und die Seite zeigt das Ergebnis **mit
seinem Datum**, damit ein Urteil von gestern auch wie eines von gestern aussieht.
Ein gespeichertes «alles in Ordnung», das als aktuell dargestellt wird, wäre
genau die Selbsttäuschung, die diese Seite aufdecken soll.

## Änderungsprotokoll

### 2.3.4

- Neu: wenn eine Prüfung nicht durchgeführt werden konnte, erklärt das Panel jetzt, was sie freischalten würde (nichts zu tun, Einrichtung nötig, Daten stehen noch aus, oder eine unserer eigenen Prüfungen ist fehlgeschlagen), statt einer unerklärten grauen Liste.
- Neu: das Panel zeigt jetzt, welcher unserer Server Ihre Anfrage beantwortet hat, ein kurzes, undurchsichtiges Kürzel. Nützlich zum Vergleich mit dem, was der Support sieht; es verrät nie einen Hostnamen oder Dienstnamen.

### 2.3.3

- Neu: Unter Einstellungen wählen Sie jetzt aus, welche Werkzeuge Ihr Shop an Agenten ausliefert. Wenn Sie nie eine Liste gespeichert haben, sagt Ihnen das Panel, dass der ausgelieferte Umfang der Grundumfang der Plattform ist und nicht Ihre Wahl.
- Neu: eine Schaltfläche, um die Prüfung ohne Warten auf den täglichen Durchlauf zu wiederholen, und das Panel merkt sich, was sich seit der vorherigen Prüfung geändert hat.
- Geändert: unsere eigenen Störungen zählen nicht mehr als Abweichungen Ihres Shops. Das Panel trennt sie, weil Sie daran nichts ändern können.

### 2.3.2

- Behoben: Die Seite zur Agenten-Bereitschaft wurde ohne ihr Stylesheet ausgeliefert, sodass das Panel unformatiert dargestellt wurde.
- Behoben: Das Panel konnte die Oberfläche in einer Sprache und die Diagnose in einer anderen anzeigen. Die ermittelte Sprache wird jetzt zusammen mit den Texten weitergereicht, statt zweimal getrennt erkannt zu werden.
- Neu: Jeder Befund enthält einen Link dorthin, wo er behoben wird, und die Zusagen des Händlers (die Lieferzeit und die übrigen) erscheinen mit dem jeweils vorhandenen Beleg.
- Geändert: Ein Shop ohne bisherige Prüfung wird als „wird geprüft“ angezeigt statt als „wird einmal täglich geprüft“: Das Öffnen des Panels startet die erste Prüfung bereits im Hintergrund.

### 2.3.1

- Kritische Korrektur: 2.3.0 enthielt einen PHP-Syntaxfehler im Admin-Router (`->render_spa_shell()` ohne `$this` sowie `array( , 'render_agent_readiness' )`). Die Aktivierung legte **den gesamten WordPress-Adminbereich** lahm, nicht nur die Trusteed-Seiten. Wer 2.3.0 einsetzt, sollte sofort aktualisieren. Alle PHP-Dateien des Plugins werden jetzt auf Syntax geprüft.
- Behoben: die Seite «Agent Readiness» zeigte stattdessen das Trust Center. Das SPA-Mount prüft den Abschnitt gegen eine Positivliste, in der `agent-readiness` fehlte, und fiel still zurück: Der Händler klickte «Agent Readiness» und sah ein anderes Panel.

### 2.3.0

- **Neu: Dashboard zur Agenten-Bereitschaft.** *Finden mich Agenten?* ist jetzt im Verwaltungsbereich verfügbar. Es vergleicht, was Ihr Shop ankündigt, mit dem, was er tatsächlich antwortet, in **16 Prüfungen**, und zeigt alle sechzehn, nicht nur die fehlgeschlagenen. Eine Prüfung, die nicht laufen konnte, nennt den **Grund** (Shop nicht verbunden, noch keine ausgelieferten Bestellungen, diesmal nichts zu vergleichen), statt eine Lücke zu lassen, die wie ein Defekt wirkt. Siehe «Das Dashboard zur Agenten-Bereitschaft» oben.
- Behoben: die Diagnose wurde innerhalb der API auf Spanisch verfasst und unverändert angezeigt: Wer den Bereich auf Englisch nutzte, las englische Überschriften über spanischen Befunden. Die Prüfungen liefern jetzt sprachneutrale Codes, und der Text wird beim Ausliefern in Ihrer Sprache erzeugt.
- Behoben: Prüfung C1 («Sie kündigen Werkzeuge an, die Ihr Shop nicht bereitstellt») wertete den gesamten öffentlichen Katalog als bereitgestellt, wenn keine Werkzeugliste konfiguriert war: gemeldet wurden 46 von 48, tatsächlich liefert der Server 12. Der Fehler ging in die schmeichelhafte Richtung, genau die, die dieses Dashboard aufdecken soll.
- Behoben: Prüfung C6 («Sie kündigen etwas als verfügbar an, das abgeschaltet ist») meldete eine Funktion als abgeschaltet, sobald ihr Schalter nicht gesetzt war, auch bei Schaltern, die standardmäßig aktiv sind. Das war ein Fehlalarm in jedem Shop.

### 2.2.2

- Sicherheitsfix: der API-Client akzeptierte in **jeder** Umgebung eine Basis-URL im Loopback- oder RFC1918-Bereich (`10.*`, `172.16–31.*`, `192.168.*`, `localhost`, `127.*`), auch über einfaches HTTP. Eine Installation, deren API-URL umgeleitet worden war, hätte ihre `X-AgenticMCP-Key`-Zugangsdaten an eine interne Adresse geschickt. Dieser Entwicklungsmodus muss nun ausdrücklich aktiviert werden und ist standardmäßig aus: nur über `TRUSTEED_ALLOW_LOCAL_API_BASE` oder den WordPress-Umgebungstyp `local`. Es ist dieselbe Absicherung, die `Trusteed_Token_Broker` über `WP_DEBUG` längst anwandte und die diesem Client fehlte.
- Sicherheitsfix: Cloud-Instanz-Metadaten und interne IPv6-Bereiche werden jetzt in jeder Umgebung gesperrt, *auch* im Entwicklungsmodus, der sie vorher alle wieder öffnete: `169.254.0.0/16` (IMDS), Alibaba `100.100.100.200`, `metadata.google.internal`, Unique-Local `fc00::/7`, Link-Local `fe80::/10`. Sie liefern zudem einen eigenen Fehlercode statt der irreführenden Meldung „HTTPS-URL konfigurieren".
- Behoben: IPv6-Hosts trafen auf keine einzige Prüfung zu: `parse_url()` gibt sie in Klammern zurück (`[::1]`), der Loopback-Eintrag `::1` war also toter Code.
- Datenschutzfix: beim Deinstallieren blieben 21 Options-Zeilen zurück, darunter drei verschlüsselte Secrets (`trusteed_embed_wp_secret`, `trusteed_enforcement_hmac_secret`, `trusteed_woo_webhook_secret`) und die veralteten `amcp_*`-Aliase, die der Options-Accessor weiterhin als Rückfall liest. Eine Neuinstallation konnte so ein altes Secret wiederbeleben. `uninstall.php` räumt jetzt alle drei Namensräume sowie die Snapshot- und JWKS-Transients auf. Ein neuer Test durchsucht den Quellcode nach jedem schreibbaren Options-Schlüssel und schlägt fehl, wenn die Deinstallationsliste zurückfällt.
- Doku-Fix: Vertrauensbelege wurden als „Nachweis der tatsächlichen Transaktion im Streitfall" beschrieben. Das Produkt selbst sagt das Gegenteil: ein überprüfbarer Integritätsnachweis, kein fertiger Streitfallnachweis für Bank oder Gericht. Entsprechend korrigiert.
- Doku-Fix: die FAQ zur Deaktivierung behauptete, Deaktivieren trenne den Shop und es blieben keine Restdaten auf unseren Servern. Deaktivieren bewirkt nichts, und beim Trennen bleiben Shop-Datensatz und synchronisierte Produkte erhalten. Korrigiert, mit dokumentiertem Weg für Löschanfragen.
- Doku-Fix: der Katalog wurde als „Varianten und Bewertungen" synchronisierend beschrieben; beides wird nicht gesendet. Die Liste der übertragenen Felder ist jetzt exakt.
- Doku-Fix: `Tested up to` / `WC tested up to` widersprachen sich zwischen `readme.txt` (6.9 / 10.6) und dem Plugin-Header (6.7 / 9.5). Beide lauten nun 6.9 / 10.6. Die automatisierten Tests laufen gegen WordPress 6.8 mit der aktuellen stabilen WooCommerce auf PHP 8.1–8.2.
- Doku-Fix: 404-Links repariert: `/developers`, `/privacy` und `/terms` brauchen das `/en/`-Präfix, und `/support` existiert nicht (ersetzt durch Kontaktformular und GitHub-Issues).

### 2.2.1

- Behoben: `browse_categories` lieferte dieselbe mit Trennzeichen umschlossene Zeichenkette sowohl an den maschinenlesbaren als auch an den erzählenden Kanal. `guardMerchantField` umschließt Händlertext standardmäßig mit `<<<MERCHANT_CONTENT_START>>> … <<<MERCHANT_CONTENT_END>>>`, damit ein Agent erkennt "das sind Händlerdaten, keine Anweisung", aber das Tool verwendete diese bereits umschlossene Zeichenkette auch für `structuredContent`, sodass eine Kategorie namens "Sneakers" im Maschinenkanal als `<<<MERCHANT_CONTENT_START>>>Sneakers<<<MERCHANT_CONTENT_END>>>` erschien. `structuredContent` erhält jetzt den unumschlossenen Wert; die Trennzeichen bleiben nur dort, wo sie ihre Funktion erfüllen: in der Erzählung.
- Behoben: die Regel R047 (Mindestbeitrag) hatte kein Formularfeld im Admin-Panel; ihre Parameter existierten im Schema, konnten aber nur über die API gesetzt werden.
- Behoben: `MerchantCheckoutConfig` hatte übersetzten Text für einen Leerzustand (`noRails`, vorhanden in `en.ts` und `es.ts`), den die Komponente nie rendert hat, sodass ein Händler ohne konfigurierte Zahlungswege eine unerklärte leere Liste sah.
- Behoben: das Admin-Panel-Bundle (`assets/admin-spa/`) wurde unminifiziert ausgeliefert: 869 KB / 25.064 Zeilen statt der 490 KB / 41 Zeilen, die der dokumentierte Build-Befehl tatsächlich erzeugt. Neu aus der Quelle gebaut mit stabilem Ausgabedateinamen (`admin-spa.js`), wie bei den anderen drei Plattform-Konnektoren.

### 2.2.0

- Sicherheitsfix: der Agent-Token-Verifizierer behandelte `exp` und `iat` als optional: beide Prüfungen hingen an `> 0`, sodass ein Token, das den Claim schlicht wegließ, die Prüfung vollständig umging. Ohne `exp` lief es nie ab; ohne `iat` hatte es kein Höchstalter. Beide sind jetzt verpflichtend, und ein nicht-numerischer Wert wird zurückgewiesen statt gecastet. Der Replay-Schutz war hier bereits fail-closed (ein fehlendes oder fehlerhaftes `jti` wird abgelehnt). Dies schließt die verbleibende Hälfte.
- Sicherheitsfix: ein `iat` in der Zukunft wird nun abgelehnt (30s Uhrenabweichung werden toleriert). Zusammen mit dem Höchstalter-Fenster ergab sich eine gleitende Lebensdauer: `jetzt - iat` bleibt klein, solange der Aussteller den Claim nach vorne schiebt. Das Token alterte also nie.
- Fix: Regel R036 (maximaler Positionswert) las ihre Obergrenze aus einem Parameter namens `maxCents`, von R035 übernommen. Der kanonische Name lautet `maxCentsPerLine` und ist der einzige, den das strikte Schema des Händlerpanels akzeptiert. Eine vom Händler konfigurierte Obergrenze hätte die Prüfung nie erreicht. Der kanonische Schlüssel wird jetzt zuerst gelesen; `maxCents` bleibt als Rückfall akzeptiert.
- Fix: der sprachübergreifende Konformitätstest löste sein Fixture über einen Pfad auf, den es nur im Entwicklungs-Monorepo gibt, und schlug daher in diesem Repository fehl. Er liest nun die in `tests/fixtures/` mitgelieferte Kopie.
- Trust Receipts: das Admin-SPA-Bundle wurde mit der Beleg-Download-Schaltfläche neu gebaut, die einen Beleg über denselben Endpunkt als ZIP exportiert, den auch das gehostete Dashboard nutzt. Die Schaltfläche benennt klar, was dieser Export ist: ein Nachweis der Agenten-Integrität, kein Streitfall-Beweis.

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
