[English](README.md) | [Español](README.es.md) | **Français** | [Deutsch](README.de.md)

# Trusteed Agentic Commerce pour WooCommerce

Les agents IA sont un nouveau type d'acheteur en ligne. Avec Trusteed, le réseau qui met en relation les entreprises et les agents, ils peuvent acheter dans votre boutique selon vos conditions.

- Définissez vos règles métier : qui peut acheter, jusqu'à quel montant, quelles catégories vous ne proposez pas aux agents, des limites de prix, des niveaux de stock qui vous protègent des agents frauduleux, et plus encore.
- Recevez des reçus signés. Chaque transaction produit un reçu signé cryptographiquement, dont toute altération est détectable, et que vous pouvez utiliser comme preuve de l'achat en cas de litige. Aligné sur eIDAS (UE) et sur eSIGN (États-Unis).
- Voyez ce que font les agents : combien ils dépensent, ce qu'ils achètent et à quelle fréquence.
- Bloquez les agents qui semblent dangereux ou qui posent problème.
- Acceptez des achats en monnaies numériques grâce au protocole X402.
- Laissez agents et marchands échanger directement, de pair à pair.

## Captures d'écran

Chaque panneau ci-dessous correspond à un élément du menu **Trusteed** dans WooCommerce.

| Accueil | Trust Center | Mes Ventes |
|------|--------------|----------|
| ![Home](assets/screenshots/home.png) | ![Trust Center](assets/screenshots/trust-center.png) | ![My Sales](assets/screenshots/my-sales.png) |

| Mes Règles | Agents | Merchant Center |
|----------|--------|-----------------|
| ![My Rules](assets/screenshots/my-rules.png) | ![Agents](assets/screenshots/agents.png) | ![Merchant Center](assets/screenshots/merchant-center.png) |

| Reçus de confiance (Mes Ventes → Ventes IA) |
|--------------------------------------|
| ![Trust Receipts](assets/screenshots/ai-receipts.png) |

Chaque transaction d'un agent génère un reçu de confiance signé, un enregistrement dont toute altération est détectable (aligné sur eIDAS et sur eSIGN) répertorié sous **Mes Ventes → Ventes IA**. Cliquez sur une ligne pour voir le détail : ID de l'agent, outil appelé, hachages d'entrée et de sortie, JWS. Vous pouvez aussi télécharger le reçu au format ZIP et le conserver en cas de litige.

## Fonctionnalités

Le plugin relie votre catalogue de produits aux agents d'achat IA grâce au **Model Context Protocol (MCP)**, un standard ouvert créé par Anthropic. Il ne traite jamais les paiements et ne touche pas aux données sensibles des clients. Le paiement se fait toujours sur votre checkout natif WooCommerce.

- Outils MCP pour les agents : `search_products`, `browse_categories`, `get_product_details` et `create_cart`, qui envoie l'acheteur vers le checkout natif WooCommerce.
- Synchronisation automatique du catalogue. Les produits se synchronisent via les hooks WooCommerce lorsque vous les créez, les modifiez ou les supprimez, y compris les changements de stock, et vous pouvez lancer une synchronisation complète manuelle depuis la page des réglages. Seules les données publiques du catalogue quittent votre boutique (titres, descriptions, prix, images, catégories, stock), jamais les données personnelles des clients, les commandes ni les informations de paiement.
- Vérification du token de l'agent : `create_cart` transmet le token JWS de l'agent au checkout, de sorte que la vérification de la signature et du rejeu (R002) s'exécute dans le flux normal.
- Porte d'application (HITL) : approbation humaine (human-in-the-loop) configurable pour les commandes d'agents de forte valeur.
- Renforcement SSRF : les URLs de la boutique et de l'API sont vérifiées par rapport à une liste blanche exacte d'hôtes et aux listes de blocage RFC1918, IPv6 ULA et IMDS cloud.
- Comportements par défaut fail-closed : rien n'est envoyé si le secret d'application est vide, et pour vous reconnecter il faut prouver que vous êtes propriétaire du domaine, ce qui protège contre le détournement entre marchands.

## Compatibilité

| Composant | Compatible avec |
|-----------|-----------|
| WordPress | 6.0 – 6.9 |
| WooCommerce | 8.0 – 10.6 |
| PHP | 7.4+ (testé sur 8.0–8.3) |

## Prérequis

- WordPress 6.0+ avec WooCommerce 8.0+
- PHP 7.4 ou plus récent
- Un compte Trusteed ([inscrivez-vous gratuitement sur trusteed.xyz](https://trusteed.xyz))

## Installation

### Téléversement manuel (recommandé)

1. **Téléchargez le `.zip` installable** depuis la dernière Release GitHub :
   [**⬇ trusteed-agentic-commerce-woocommerce-2.1.0.zip**](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases/latest/download/trusteed-agentic-commerce-woocommerce-2.1.0.zip)
   ou parcourez toutes les versions sur la [page des Releases](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/releases).
2. Dans votre administration WordPress : **Extensions → Ajouter → Téléverser une extension**.
3. Sélectionnez le fichier téléchargé `trusteed-agentic-commerce-woocommerce-2.1.0.zip` et cliquez sur **Installer maintenant**.
4. Cliquez sur **Activer**.

### Depuis les sources (compiler le zip vous-même)

```bash
git clone https://github.com/Trusteedxyz/agentic-commerce-woocommerce.git
cd agentic-commerce-woocommerce
bash build-zip.sh        # outputs dist/trusteed-agentic-commerce-woocommerce-<version>.zip
```

## Configuration

1. Connectez-vous à votre **administration** WordPress.
2. Allez dans **WooCommerce → Trusteed** (ou dans l'élément de menu **Trusteed**).
3. Saisissez votre **clé API** depuis [app.trusteed.xyz/settings](https://app.trusteed.xyz/settings).
4. Cliquez sur **Enregistrer et connecter**. Le plugin teste la connexion, enregistre votre boutique et synchronise votre catalogue.

Une fois connecté, tout agent compatible MCP (Claude, ChatGPT, ou un agent personnalisé créé avec LangChain, CrewAI, Vercel AI SDK et des outils similaires) peut rechercher vos produits, parcourir les catégories, lire les fiches produit et constituer des paniers. Lorsque le client est prêt à acheter, l'agent l'envoie vers votre checkout natif WooCommerce, et vos passerelles de paiement existantes (Stripe, PayPal, …) encaissent le paiement.

Le guide complet destiné aux marchands se trouve dans [`docs/MERCHANT_INSTALLATION_GUIDE.md`](docs/MERCHANT_INSTALLATION_GUIDE.md).

## FAQ

**Quelles données sont envoyées ?** Uniquement votre catalogue public de produits : titres, prix, descriptions, images, catégories et statut du stock. Aucune donnée personnelle de client, aucune information de paiement, aucun historique de commandes. Toutes les communications passent par HTTPS.

**Quels agents sont pris en charge ?** Tout agent compatible MCP : Claude (Anthropic), ChatGPT (OpenAI), et des agents personnalisés créés avec LangChain, CrewAI, Vercel AI SDK ou tout autre framework qui prend en charge le Model Context Protocol.

**Cela ralentit-il ma boutique ?** Il ajoute une requête au checkout. Le plugin communique aussi avec Trusteed quand votre catalogue change et quand un agent agit sur votre boutique. Quand un client passe une commande, agent ou personne, le plugin demande à Trusteed d'évaluer vos règles. Cette requête expire au bout de 5 secondes. Si Trusteed est injoignable, le plugin applique vos règles issues du dernier snapshot signé qu'il a récupéré. Si cela n'est pas possible non plus, le résultat suit le mode d'échec que vous avez choisi dans les réglages du plugin : bloquer la commande ou l'autoriser.

## Journal des modifications

### 2.1.0

- Rebranding : les classes internes, les clés d'options et les routes REST passent de `Amcp_`/`amcp_` à `Trusteed_`/`trusteed_`. Les installations existantes continuent de fonctionner. Les options héritées `amcp_{key}` sont toujours lues en repli, les espaces de noms REST hérités restent enregistrés à côté des nouveaux, et le préfixe hérité des valeurs chiffrées se déchiffre toujours.
- Correctif : le payload HITL R043 est désormais transmis de bout en bout, de sorte qu'un BLOCK peut déclencher une pause d'intervention humaine au lieu d'un blocage strict qui perd l'intention de l'acheteur.
- Correctif (critique) : le bundle SPA d'administration compilé (`assets/admin-spa/`) était absent du paquet distribué. Le panneau d'administration Trusteed affichait une erreur « bundle non compilé » à chaque installation. Le bundle est désormais correctement inclus.
- Renforcement des webhooks de facturation, de l'application des règles au checkout, de la synchronisation du catalogue et des signaux de panier.

### 2.0.2

Correctif d'application des règles au checkout. Les règles du marchand (montant maximum, pays bloqués, horaires d'ouverture) étaient entièrement ignorées pour les checkouts organiques, sans agent. Elles s'appliquent désormais à tous les checkouts. Ajout d'un évaluateur de soupape de sécurité hors ligne qui applique ces règles localement lorsque l'API distante des règles est inaccessible.

### 2.0.1

Correctif critique d'activation et de sécurité (audit Codex). Corrige un renommage `AGENTICMCP_*` → `TRUSTEED_*` resté à mi-chemin, qui empêchait l'activation en 2.0.0. `create_cart` transmet désormais le token JWS de l'agent afin que la vérification R002 s'exécute. Le client REST valide l'hôte de base de l'API par rapport à une liste blanche exacte.

### 2.0.0

Sprint sécurité et fiabilité. La déconnexion se fait désormais en deux phases, avec un jeton de confirmation. La reconnexion exige une preuve de propriété du domaine (`/.well-known/amcp-verify.txt`). `create_cart` dispose d'un véritable endpoint de pont de panier. Le webhook d'événements des agents réessaie avec un backoff exponentiel. S'y ajoutent le renforcement SSRF et des comportements par défaut fail-closed pour l'application des règles.

## Support

- E-mail support : support@trusteed.xyz
- Tickets GitHub : [github.com/Trusteedxyz/agentic-commerce-woocommerce/issues](https://github.com/Trusteedxyz/agentic-commerce-woocommerce/issues)

## Licence

GPL-2.0-or-later. Voir [LICENSE](LICENSE) pour le texte complet.
