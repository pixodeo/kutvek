# Architecture Technique - Kutvek Platform

## 📐 Vue d'ensemble

La plateforme Kutvek est construite sur une architecture **Action-Domain-Responder (ADR)**, une évolution du pattern MVC traditionnel mieux adaptée aux applications web modernes et aux APIs REST.

### Principes architecturaux

- **Séparation des préoccupations** - Chaque couche a une responsabilité unique
- **Standards PSR** - Conformité PSR-7 (HTTP Messages) et PSR-15 (Middleware)
- **Inversion de dépendances** - Utilisation d'interfaces et injection de dépendances
- **Domain-Driven Design** - Logique métier dans le Domain layer
- **SOLID principles** - Code maintenable et testable

## 🏗 Pattern ADR (Action-Domain-Responder)

### Différences avec MVC

| Aspect | MVC | ADR |
|--------|-----|-----|
| Point d'entrée | Controller avec plusieurs méthodes | Action unique par route |
| Logique métier | Parfois dans Controller | Toujours dans Domain |
| Réponse HTTP | Gérée par Controller/View | Dédiée au Responder |
| Testabilité | Moyenne | Excellente |
| Responsabilité unique | Souvent violée | Strictement respectée |

### Flux de requête HTTP

```
┌─────────────┐
│   Client    │
└──────┬──────┘
       │ HTTP Request
       ▼
┌─────────────────────────────────────────────────────────────┐
│                     webroot/index.php                       │
│  - Définition des constantes                                │
│  - Initialisation autoloaders                               │
│  - Création PSR-7 ServerRequest                             │
│  - Initialisation Router                                    │
└──────┬──────────────────────────────────────────────────────┘
       │
       ▼
┌─────────────────────────────────────────────────────────────┐
│                  Core/Routing/Router                        │
│  - Match URL → Route                                        │
│  - Résolution Action class                                  │
│  - Application des Middleware                               │
└──────┬──────────────────────────────────────────────────────┘
       │
       ▼
┌─────────────────────────────────────────────────────────────┐
│                    Middleware Stack                         │
│  PoweredBy → UrlStatus → IsPage → Dispatch → [Custom]      │
└──────┬──────────────────────────────────────────────────────┘
       │
       ▼
┌─────────────────────────────────────────────────────────────┐
│                   Action (App/*/Action)                     │
│  - Validation des paramètres                                │
│  - Appel au Domain layer                                    │
│  - Préparation des données                                  │
│  - Passage au Responder                                     │
└──────┬──────────────────────────────────────────────────────┘
       │
       ▼
┌─────────────────────────────────────────────────────────────┐
│              Domain Layer (Domain/Table/*)                  │
│  - Requêtes base de données                                 │
│  - Logique métier                                           │
│  - Transformation des données                               │
│  - Retour d'Entities                                        │
└──────┬──────────────────────────────────────────────────────┘
       │
       ▼
┌─────────────────────────────────────────────────────────────┐
│                    Responder/View                           │
│  - Chargement du template                                   │
│  - Injection des données                                    │
│  - Génération HTML                                          │
│  - Construction PSR-7 Response                              │
└──────┬──────────────────────────────────────────────────────┘
       │
       ▼
┌─────────────────────────────────────────────────────────────┐
│                   HTTP Response                             │
│  - Headers                                                  │
│  - Body (HTML/JSON)                                         │
│  - Status Code                                              │
└──────┬──────────────────────────────────────────────────────┘
       │
       ▼
┌──────────────┐
│    Client    │
└──────────────┘
```

## 🎯 Couches de l'application

### 1. App Layer - Actions et logique applicative

**Localisation**: `/App/`

Contient les **Actions** (équivalent des Controllers) organisées par module métier.

```
App/
├── Product/
│   ├── Read.php          # Action pour lire un produit
│   ├── AddToCart.php     # Action pour ajouter au panier
│   ├── Gallery.php       # Action pour la galerie
│   └── Types/            # Stratégies par type de produit
├── Cart/
│   ├── Overview.php
│   └── UpdateItemQty.php
└── Checkout/
    ├── Cart.php
    ├── Shipping.php
    └── Payment.php
```

**Caractéristiques** :
- Chaque Action est une classe avec une méthode `__invoke()`
- Une Action = Une route HTTP
- Hérite de `AppAction` qui hérite de `Core\Action`
- Implémente `MiddlewareInterface` et `RequestHandlerInterface`

**Exemple d'Action** :

```php
<?php
declare(strict_types=1);
namespace App\Product;

use App\AppAction;
use Domain\Table\Product;

final class Read extends AppAction {

    public function __invoke() {
        // 1. Récupération des paramètres
        $queries = $this->getRequest()->getQueryParams();
        $id = (int)$queries['id'];

        // 2. Appel au Domain Layer
        $productTable = new Product($this->_setDb());
        $product = $productTable->read($id);

        // 3. Application d'une stratégie (pattern Strategy)
        $strategy = match($product->behavior_type) {
            'Graphics' => new Types\Graphics($this->_route),
            'PlateStickers' => new Types\PlateStickers($this->_route),
            default => new Types\Basics($this->_route)
        };

        $strategy->setProduct($product);
        $this->_middleware = $strategy;

        // 4. Passage au middleware/responder
        return $this->handle($this->getRequest());
    }
}
```

### 2. Core Layer - Framework

**Localisation**: `/Core/`

Le cœur du framework avec les composants réutilisables.

```
Core/
├── Action.php              # Classe de base pour toutes les Actions
├── Responder.php           # Gestion des réponses HTTP
├── Controller/             # Legacy controllers (si besoin)
├── Routing/
│   ├── Router.php          # Routeur principal
│   ├── Route.php           # Représentation d'une route
│   └── RouterInterface.php
├── Database/
│   ├── MysqlDatabase.php   # Abstraction MySQL
│   └── QueryBuilder.php
├── Http/
│   └── Message/
│       ├── Factory/Psr17Factory.php
│       ├── ServerRequest.php
│       └── Response.php
├── Model/                  # Modèles de base
├── View/                   # Moteur de templates
├── Library/                # Helpers et utilitaires
│   ├── TraitCore.php
│   ├── TraitModel.php
│   ├── TraitView.php
│   ├── TraitL10n.php       # Localisation
│   └── TraitCookie.php     # Gestion cookies
└── Middleware/             # Middleware de base
```

**Responsabilités** :
- Gestion du routing
- Abstraction base de données
- Gestion des requêtes/réponses HTTP (PSR-7)
- Middleware pipeline (PSR-15)
- Rendu des vues
- Utilitaires globaux

### 3. Domain Layer - Logique métier

**Localisation**: `/Domain/`

Contient la **logique métier pure** indépendante de l'infrastructure.

```
Domain/
├── Entity/                 # Entités métier (objets de valeur)
│   ├── Product.php
│   ├── Card.php
│   ├── YearType.php
│   └── SaddleCoverExport.php
├── Table/                  # Couche d'accès données (Repository pattern)
│   ├── Product.php
│   ├── Catalog.php
│   ├── Order.php
│   ├── Checkout.php
│   └── Category.php
└── Decorator/              # Pattern Decorator pour enrichissement
    ├── Graphics.php
    └── Basics.php
```

#### Entities

Les **Entities** représentent les objets métier avec leur comportement.

```php
<?php
namespace Domain\Entity;

class Product extends Entity {
    private NumberFormatter $_numFormatter;
    public $locale = 'fr';

    public function __construct(RouteInterface $route) {
        $this->_numFormatter = new NumberFormatter(
            $this->locale,
            NumberFormatter::CURRENCY
        );
        $this->_route = $route;
    }

    // Lazy loading avec __get
    public function __get($key) {
        $method = "_{$key}";
        if (!method_exists($this, $method)) return '';
        $this->{$key} = $this->{$method}();
        return $this->{$key};
    }

    private function _cost() {
        $price = $this->_withVAT((float)$this->price->price);
        return $this->_setPrice($price);
    }

    private function _title() {
        if ($this->full_designation !== null) {
            return $this->full_designation;
        }
        // Sinon, délégation au Decorator
        return $this->getDecorator()->title();
    }

    protected function _withVAT(float $price): float {
        if ($this->country_vat > 0 && $this->currency_id !== 1) {
            return $price * (1 + ($this->tax_rate / 100));
        }
        return $price;
    }
}
```

#### Tables (Repository Pattern)

Les **Tables** gèrent l'accès aux données.

```php
<?php
namespace Domain\Table;

class Product extends Table {

    public function read(int $id): \Domain\Entity\Product {
        $query = "
            SELECT p.*, ...
            FROM products p
            WHERE p.id = :id
        ";

        $result = $this->db->query($query, ['id' => $id]);
        return $this->hydrate($result);
    }

    public function findByCategory(int $categoryId): array {
        // ...
    }

    private function hydrate($data): \Domain\Entity\Product {
        $entity = new \Domain\Entity\Product($this->route);
        foreach ($data as $key => $value) {
            $entity->$key = $value;
        }
        return $entity;
    }
}
```

### 4. View Layer - Présentation

**Localisation**: `/View/`

Templates PHP organisés par module.

```
View/
├── Layout/                 # Layouts de base
│   ├── default.php
│   ├── checkout.php
│   └── admin.php
├── Partials/               # Composants réutilisables
│   ├── header.php
│   ├── footer.php
│   ├── nav.php
│   └── product-card.php
├── Products/               # Vues produits
│   ├── read.php
│   ├── list.php
│   └── gallery.php
├── Cart/
│   └── overview.php
└── Checkout/
    ├── cart.php
    ├── shipping.php
    └── payment.php
```

**Utilisation dans une Action** :

```php
// Dans l'Action
$this->render('Products/read', [
    'product' => $product,
    'related' => $relatedProducts
]);
```

### 5. Middleware Layer

**Localisation**: `/Middleware/`

Les middleware sont exécutés avant l'Action.

```
Middleware/
├── PoweredBy.php      # Ajoute headers X-Powered-By
├── Dispatch.php       # Dispatching conditionnel
├── IsPage.php         # Vérifie si c'est une page
└── UrlStatus.php      # Gère les redirections 301/302
```

**Exemple de Middleware** :

```php
<?php
namespace Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PoweredBy implements MiddlewareInterface {

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        // Traitement avant l'Action
        $response = $handler->handle($request);

        // Traitement après l'Action
        return $response->withHeader('X-Powered-By', 'Kutvek-Framework');
    }
}
```

## 🎨 Design Patterns Utilisés

### 1. Strategy Pattern

Permet de changer le comportement selon le type de produit.

```php
// Dans Product/Read.php
$strategy = match($product->behavior_type) {
    'Graphics' => new Types\Graphics($this->_route),
    'PlateStickers' => new Types\PlateStickers($this->_route),
    'EngineGuard' => new Types\EngineGuard($this->_route),
    default => new Types\Basics($this->_route)
};

$strategy->setProduct($product);
```

### 2. Decorator Pattern

Enrichit les entités avec des comportements additionnels.

```php
// Dans Domain/Entity/Product.php
public function getDecorator(): Decorator {
    if (!$this->_decorator) {
        $this->_decorator = match($this->behavior_type) {
            'Graphics' => new Graphics($this, $this->_route),
            default => new Basics($this, $this->_route)
        };
    }
    return $this->_decorator;
}
```

### 3. Repository Pattern

Abstraction de l'accès aux données via les Tables.

```php
// Utilisation
$productTable = new \Domain\Table\Product($db);
$products = $productTable->findByCategory(5);
```

### 4. Factory Pattern

Création d'objets PSR-7.

```php
$psr17Factory = new \Core\Http\Message\Factory\Psr17Factory();
$creator = new \Core\Http\Message\ServerRequestCreator(
    $psr17Factory,  // ServerRequestFactory
    $psr17Factory,  // UriFactory
    $psr17Factory,  // UploadedFileFactory
    $psr17Factory   // StreamFactory
);
$request = $creator->fromGlobals();
```

### 5. Singleton Pattern

Pour les instances uniques (Database, Config).

```php
class App {
    private static $_instance = null;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new App();
        }
        return self::$_instance;
    }
}
```

## 🔄 Système de Routing

### Définition des routes

Routes définies dans `webroot/index.php` :

```php
$router = new Router($request);

// Route simple
$router->get('/', "page.homepage")
    ->middleware('PoweredBy');

// Route avec paramètres
$router->get('/:slug-:id', 'product.read')
    ->with('slug', '([a-z0-9\-\/\.]+)')
    ->with('id', '([0-9]+)')
    ->middleware('PoweredBy')
    ->middleware('IsPage')
    ->middleware('Dispatch');

// Route RESTful
$router->put('/carts/:id/items/:item/qty', 'cart.updateItemQty')
    ->with('id', '([0-9]+)')
    ->with('item', '([0-9]+)');

// Route POST
$router->post('/checkout/:order/:psp', 'checkout.create')
    ->with('order', '([0-9]+)')
    ->with('psp', '([a-z\-]+)')
    ->middleware('PoweredBy');
```

### Format de résolution

Le format `'module.action'` est résolu en :
- Module: `App\ModuleName\ActionName`
- Exemple: `'product.read'` → `App\Product\Read`

### Méthodes HTTP supportées

- `GET` - Récupération de ressources
- `POST` - Création de ressources
- `PUT` - Mise à jour complète
- `DELETE` - Suppression
- `PATCH` - Mise à jour partielle (si implémenté)

## 🗄 Base de données

### Architecture multi-bases

Support de plusieurs bases de données simultanément :

```php
// Base par défaut
$db = $this->_setDb();

// Base spécifique
$dbAmerika = $this->_setDb('DbConfAmerika');
```

### Connexions définies

- **DbConf.php** - Base principale (app_kutvek)
- **DbConfKitGraphik.php** - Base KitGraphik
- **DbConfAmerika.php** - Base Amerika
- **DbConfAppKutvek.php** - Base App Kutvek

### Gestion des connexions

```php
// Dans App.php
public function setDb($dbConf = null) {
    if ($dbConf !== null) {
        $config = Config::getInstance(CONFIG.DS.$dbConf.'.php');
    } else {
        $config = Config::getInstance(CONFIG.DS.'DbConf.php');
    }

    if (!isset($this->db_instance[$config->get('db_name')])) {
        $this->db_instance[$config->get('db_name')] = new MysqlDatabase(
            $config->get('db_name'),
            $config->get('db_user'),
            $config->get('db_pass'),
            $config->get('db_host'),
            $config->get('db_port'),
            $config->get('charset')
        );
    }

    return $this->db_instance[$config->get('db_name')];
}
```

## 🌐 Internationalisation (i18n/l10n)

### Support multi-langue

Langues supportées :
- Français (fr) - Par défaut
- Anglais (en)
- Italien (it)

### Stockage des traductions

Traductions stockées en base de données avec suffixe `_l10n` :

```sql
products_l10ns
├── id
├── product_id
├── locale (fr/en/it)
├── title
├── description
├── meta_title
└── meta_description
```

### Utilisation dans le code

```php
// Dans une Entity
private function _title() {
    // La colonne l10n_title est automatiquement peuplée
    // selon la langue de la requête
    if ($this->l10n_title !== null) {
        return $this->l10n_title;
    }
}
```

## 💰 Gestion Multi-devise

### Devises supportées

- EUR - Euro (devise de base)
- USD - Dollar américain
- GBP - Livre sterling
- CHF - Franc suisse

### Calcul automatique

```php
// Dans Domain/Entity/Product.php
protected function _withVAT(float $price): float {
    if ($this->country_vat > 0 && $this->currency_id !== 1) {
        return $price * (1 + ($this->tax_rate / 100));
    }
    return $price;
}

protected function _setPrice(float $price = 0.00, int $digits = 2) {
    $this->_numFormatter->setAttribute(
        NumberFormatter::MAX_FRACTION_DIGITS,
        $digits
    );
    return $this->_numFormatter->formatCurrency(
        $price,
        $this->currency_code
    );
}
```

## 🔐 Sécurité

### CORS

Configuration CORS dans `App.php` :

```php
private static function cors() {
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }
        exit(0);
    }
}
```

### Protection XSS

Templates échappent automatiquement les variables :

```php
<!-- Dans les vues -->
<?= htmlspecialchars($product->title, ENT_QUOTES, 'UTF-8') ?>

<!-- Ou utiliser short tags (auto-escape) -->
<?= $product->title ?>
```

### Protection CSRF

À implémenter pour les formulaires.

## 📦 Autoloading

### Système d'autoloading personnalisé

Chaque namespace a son propre autoloader :

```php
// Dans webroot/index.php
App::load();

// Dans App.php
public static function load() {
    require APP.DS.'Autoloader.php';
    App\Autoloader::register();

    require CORE.DS.'Autoloader.php';
    Core\Autoloader::register();

    require LIBRARY.DS.'Autoloader.php';
    Library\Autoloader::register();

    require DOMAIN.DS.'Autoloader.php';
    Domain\Autoloader::register();

    // etc...
}
```

### Convention de nommage

- Namespace = Structure de dossiers
- `App\Product\Read` → `App/Product/Read.php`
- `Domain\Entity\Product` → `Domain/Entity/Product.php`

## 🎭 Environnements

### Configuration par environnement

Utilisation de constantes pour différencier :

```php
// Développement
define('URL_SITE', 'http://localhost/kutvek/');
define('DOMAIN', 'http://localhost');

// Production
define('URL_SITE', 'https://demo.kutvek-kitgraphik.com/');
define('DOMAIN', 'https://demo.kutvek-kitgraphik.com');
```

### Variables importantes

```php
define('WORKSPACE', 2);        // Espace de travail
define('WEBSITE_ID', 5);       // ID du site
define('HALLOWEEN', 0);        // Feature flags
```

## 📊 Performance

### Optimisations

1. **Lazy Loading** - Chargement à la demande des propriétés d'entités
2. **Connection Pooling** - Réutilisation des connexions DB
3. **Caching** - Cache de vues et données (à améliorer)
4. **Autoloading** - Chargement uniquement des classes utilisées

### Points d'amélioration

- Implémenter un système de cache (Redis/Memcached)
- Optimiser les requêtes SQL (N+1 queries)
- Ajouter un CDN pour les assets statiques
- Mettre en place un cache HTTP (Varnish)

---

**Maintenu par**: Équipe Kutvek
**Dernière mise à jour**: Octobre 2024
