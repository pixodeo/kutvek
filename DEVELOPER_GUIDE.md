# Guide du Développeur - Kutvek Platform

## 📚 Table des matières

1. [Structure du projet](#structure-du-projet)
2. [Conventions de code](#conventions-de-code)
3. [Créer un nouveau module](#créer-un-nouveau-module)
4. [Travailler avec les Actions](#travailler-avec-les-actions)
5. [Domain Layer](#domain-layer)
6. [Routing avancé](#routing-avancé)
7. [Middleware](#middleware)
8. [Vues et templates](#vues-et-templates)
9. [Base de données](#base-de-données)
10. [Internationalisation](#internationalisation)
11. [Debugging](#debugging)
12. [Best Practices](#best-practices)

---

## 📁 Structure du projet

### Organisation des répertoires

```
kutvek/
├── App/                      # Code application (Actions par module)
│   ├── App.php               # Bootstrap application
│   ├── AppAction.php         # Action de base pour l'app
│   ├── Autoloader.php
│   ├── Assets.php
│   ├── Cart/                 # Module Panier
│   │   ├── AddItem.php
│   │   ├── DeleteItem.php
│   │   └── UpdateItemQty.php
│   ├── Checkout/             # Module Commande
│   │   ├── Cart.php
│   │   ├── Shipping.php
│   │   ├── Payment.php
│   │   └── Create.php
│   ├── Product/              # Module Produit
│   │   ├── Read.php
│   │   ├── Gallery.php
│   │   ├── Options.php
│   │   ├── AddToCart.php
│   │   └── Types/            # Stratégies par type
│   │       ├── Graphics.php
│   │       ├── PlateStickers.php
│   │       └── Basics.php
│   ├── SaddleCover/          # Module Housses de selle
│   ├── Sportswear/           # Module Vêtements
│   ├── Vehicle/              # Module Véhicules
│   └── Page/                 # Module Pages CMS
│
├── Core/                     # Framework core
│   ├── Action.php            # Classe de base Actions
│   ├── Responder.php         # Gestion réponses HTTP
│   ├── Component.php
│   ├── Controller/           # (Legacy)
│   ├── Model/                # Modèles de base
│   ├── View/                 # Moteur de vues
│   ├── Routing/              # Système routing
│   │   ├── Router.php
│   │   ├── Route.php
│   │   └── RouterInterface.php
│   ├── Database/             # Abstraction DB
│   │   └── MysqlDatabase.php
│   ├── Http/                 # PSR-7 HTTP Messages
│   │   └── Message/
│   │       ├── ServerRequest.php
│   │       ├── Response.php
│   │       └── Factory/
│   ├── Library/              # Helpers & Traits
│   │   ├── TraitCore.php
│   │   ├── TraitModel.php
│   │   ├── TraitView.php
│   │   ├── TraitL10n.php     # Localisation
│   │   └── TraitCookie.php
│   └── Middleware/
│
├── Domain/                   # Domain Layer (DDD)
│   ├── Entity/               # Entités métier
│   │   ├── Product.php
│   │   ├── Card.php
│   │   ├── YearType.php
│   │   └── ...
│   ├── Table/                # Repositories
│   │   ├── Product.php
│   │   ├── Catalog.php
│   │   ├── Order.php
│   │   ├── Checkout.php
│   │   └── ...
│   └── Decorator/            # Décorateurs
│       ├── Graphics.php
│       └── Basics.php
│
├── Middleware/               # Middleware application
│   ├── PoweredBy.php
│   ├── Dispatch.php
│   ├── IsPage.php
│   └── UrlStatus.php
│
├── View/                     # Templates
│   ├── Layout/               # Layouts
│   │   ├── default.php
│   │   └── checkout.php
│   ├── Partials/             # Composants réutilisables
│   │   ├── header.php
│   │   ├── footer.php
│   │   └── product-card.php
│   ├── Products/
│   ├── Cart/
│   ├── Checkout/
│   └── Pages/
│
├── Component/                # Composants réutilisables
├── Library/                  # Librairies custom
├── Config/                   # Configuration
│   ├── DbConf.php            # DB principale
│   ├── DbConfAmerika.php
│   ├── DbConfKitGraphik.php
│   └── ...
│
├── webroot/                  # Document root
│   ├── index.php             # Point d'entrée
│   ├── .htaccess             # Rewrite rules
│   ├── css/
│   ├── js/
│   ├── img/
│   ├── files/                # Uploads
│   ├── orders/               # Fichiers commandes
│   └── cache/                # Cache
│
├── Vendor/                   # Dépendances tierces
│   └── PHPMailer-master/
└── Psr/                      # Interfaces PSR
```

---

## 📝 Conventions de code

### Normes PHP

- **PHP 8.1+** avec typage strict
- **PSR-12** pour le style de code
- **PSR-4** pour l'autoloading
- **Declare strict types** en début de fichier

### Fichiers PHP

```php
<?php
declare(strict_types=1);
namespace App\Product;

use App\AppAction;
use Domain\Table\Product;

final class Read extends AppAction {
    // Code ici
}
```

### Nommage

| Type | Convention | Exemple |
|------|-----------|---------|
| Classes | PascalCase | `ProductController`, `ReadAction` |
| Méthodes | camelCase | `getProduct()`, `updateCart()` |
| Variables | camelCase | `$productId`, `$cartItems` |
| Constantes | UPPER_SNAKE_CASE | `URL_SITE`, `MAX_ITEMS` |
| Namespaces | PascalCase | `App\Product`, `Domain\Entity` |
| Fichiers | PascalCase.php | `Read.php`, `Product.php` |

### Types de retour

Toujours spécifier les types :

```php
// ✅ BON
public function getProductId(): int {
    return (int)$this->id;
}

public function findAll(): array {
    return $this->db->query("SELECT * FROM products");
}

public function getProduct(): ?Product {
    return $this->product ?? null;
}

// ❌ MAUVAIS
public function getProductId() {
    return $this->id;
}
```

### Commentaires

```php
/**
 * Récupère un produit par son ID
 *
 * @param int $id L'identifiant du produit
 * @return Product|null Le produit ou null si non trouvé
 * @throws ProductNotFoundException Si le produit n'existe pas
 */
public function getProductById(int $id): ?Product {
    // Code ici
}
```

### Organisation du code

```php
<?php
declare(strict_types=1);
namespace App\Product;

// 1. Use statements (groupés et triés)
use App\AppAction;
use Domain\Entity\Product;
use Domain\Table\Product as ProductTable;

// 2. Classe
final class Read extends AppAction {

    // 3. Constantes
    private const MAX_RELATED = 4;

    // 4. Propriétés
    private ProductTable $_table;
    private ?Product $product = null;

    // 5. Méthode __invoke (pour les Actions)
    public function __invoke(): void {
        // Implementation
    }

    // 6. Méthodes publiques
    public function getProduct(): Product {
        return $this->product;
    }

    // 7. Méthodes protected
    protected function loadRelatedProducts(): array {
        // Implementation
    }

    // 8. Méthodes privées
    private function validateId(int $id): bool {
        return $id > 0;
    }
}
```

---

## 🆕 Créer un nouveau module

### Étape 1: Structure de dossiers

```bash
# Créer le module
mkdir -p App/MonModule
mkdir -p View/MonModule

# Créer les Actions de base
touch App/MonModule/Index.php
touch App/MonModule/Read.php
touch App/MonModule/Create.php
```

### Étape 2: Action principale

`App/MonModule/Index.php` :

```php
<?php
declare(strict_types=1);
namespace App\MonModule;

use App\AppAction;
use Domain\Table\MonModule as MonModuleTable;

final class Index extends AppAction {

    private MonModuleTable $_table;

    public function __invoke(): void {
        // 1. Initialiser la table
        $this->_table = new MonModuleTable($this->_setDb());

        // 2. Récupérer les données
        $items = $this->_table->findAll();

        // 3. Préparer les données pour la vue
        $data = [
            'items' => $items,
            'title' => 'Liste des items',
            'meta_title' => 'Mon Module - Liste'
        ];

        // 4. Rendre la vue
        $this->render('MonModule/index', $data);
    }
}
```

### Étape 3: Action de lecture

`App/MonModule/Read.php` :

```php
<?php
declare(strict_types=1);
namespace App\MonModule;

use App\AppAction;
use Domain\Table\MonModule as MonModuleTable;
use Core\Request\UrlQueryResult;
use stdClass;

final class Read extends AppAction implements UrlQueryResult {

    public stdClass $queryResult;
    private MonModuleTable $_table;

    public function __invoke(): void {
        // Récupérer les paramètres de l'URL
        $queries = $this->getRequest()->getQueryParams();
        $id = (int)$queries['id'];

        // Valider
        if ($id <= 0) {
            $this->notFound();
            return;
        }

        // Récupérer l'item
        $this->_table = new MonModuleTable($this->_setDb());
        $item = $this->_table->read($id);

        if (!$item) {
            $this->notFound();
            return;
        }

        // Rendre
        $this->render('MonModule/read', [
            'item' => $item,
            'title' => $item->title
        ]);
    }

    public function setQueryResult(stdClass $query): void {
        $this->queryResult = $query;
    }

    private function notFound(): void {
        $this->_response = $this->_response->withStatus(404);
        $this->render('Errors/404');
    }
}
```

### Étape 4: Créer l'Entity

`Domain/Entity/MonModule.php` :

```php
<?php
declare(strict_types=1);
namespace Domain\Entity;

use Core\Domain\Entity;
use Core\Routing\RouteInterface;

class MonModule extends Entity {

    public function __construct(RouteInterface $route) {
        $this->_route = $route;
    }

    public function getId(): int {
        return (int)$this->id;
    }

    // Lazy loading avec __get
    public function __get($key) {
        $method = "_{$key}";
        if (!method_exists($this, $method)) {
            return '';
        }
        $this->{$key} = $this->{$method}();
        return $this->{$key};
    }

    private function _url(): string {
        return "/mon-module/{$this->slug}-{$this->id}";
    }

    private function _title(): string {
        // Utiliser la version localisée si disponible
        return $this->l10n_title ?? $this->default_title ?? '';
    }
}
```

### Étape 5: Créer la Table (Repository)

`Domain/Table/MonModule.php` :

```php
<?php
declare(strict_types=1);
namespace Domain\Table;

use Domain\Entity\MonModule as MonModuleEntity;

class MonModule extends Table {

    protected string $table = 'mon_module';
    protected string $entityClass = MonModuleEntity::class;

    /**
     * Récupère tous les items
     */
    public function findAll(): array {
        $query = "
            SELECT *
            FROM {$this->table}
            WHERE active = 1
            ORDER BY created_at DESC
        ";

        $results = $this->db->query($query);
        return $this->hydrateCollection($results);
    }

    /**
     * Récupère un item par ID
     */
    public function read(int $id): ?MonModuleEntity {
        $query = "
            SELECT *
            FROM {$this->table}
            WHERE id = :id
        ";

        $result = $this->db->query($query, ['id' => $id]);

        if (empty($result)) {
            return null;
        }

        return $this->hydrate($result[0]);
    }

    /**
     * Hydrate une entité
     */
    private function hydrate(array $data): MonModuleEntity {
        $entity = new MonModuleEntity($this->route);
        foreach ($data as $key => $value) {
            $entity->$key = $value;
        }
        return $entity;
    }

    /**
     * Hydrate une collection
     */
    private function hydrateCollection(array $results): array {
        $entities = [];
        foreach ($results as $data) {
            $entities[] = $this->hydrate($data);
        }
        return $entities;
    }
}
```

### Étape 6: Créer les vues

`View/MonModule/index.php` :

```php
<?php $this->layout('Layout/default', ['title' => $title]) ?>

<div class="mon-module-index">
    <h1><?= $title ?></h1>

    <div class="items-grid">
        <?php foreach ($items as $item): ?>
            <div class="item-card">
                <h2>
                    <a href="<?= $item->url ?>">
                        <?= htmlspecialchars($item->title) ?>
                    </a>
                </h2>
                <p><?= htmlspecialchars($item->excerpt) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

`View/MonModule/read.php` :

```php
<?php $this->layout('Layout/default', ['title' => $title]) ?>

<article class="mon-module-detail">
    <h1><?= htmlspecialchars($item->title) ?></h1>

    <div class="content">
        <?= $item->content ?>
    </div>

    <?php if ($item->hasRelated()): ?>
        <section class="related">
            <h2>Articles liés</h2>
            <?php foreach ($item->related as $related): ?>
                <a href="<?= $related->url ?>">
                    <?= htmlspecialchars($related->title) ?>
                </a>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</article>
```

### Étape 7: Ajouter les routes

Dans `webroot/index.php` :

```php
// Liste des items
$router->get('/mon-module', 'monModule.index')
    ->middleware('PoweredBy');

// Détail d'un item
$router->get('/mon-module/:slug-:id', 'monModule.read')
    ->with('slug', '([a-z0-9\-]+)')
    ->with('id', '([0-9]+)')
    ->middleware('PoweredBy');

// API REST
$router->post('/mon-module', 'monModule.create')
    ->middleware('PoweredBy');

$router->put('/mon-module/:id', 'monModule.update')
    ->with('id', '([0-9]+)')
    ->middleware('PoweredBy');

$router->delete('/mon-module/:id', 'monModule.delete')
    ->with('id', '([0-9]+)')
    ->middleware('PoweredBy');
```

---

## 🎯 Travailler avec les Actions

### Anatomie d'une Action

```php
<?php
declare(strict_types=1);
namespace App\Product;

use App\AppAction;
use Psr\Http\Message\ResponseInterface;

final class Read extends AppAction {

    // 1. Propriétés
    private ProductTable $_table;

    // 2. Point d'entrée (__invoke)
    public function __invoke(): void {
        // L'Action est appelée automatiquement par le Router
        // Les paramètres de route sont injectés automatiquement

        // a. Récupérer les paramètres
        $queries = $this->getRequest()->getQueryParams();
        $id = (int)$queries['id'];

        // b. Valider les données
        if (!$this->validate($id)) {
            $this->badRequest('Invalid ID');
            return;
        }

        // c. Récupérer les données (Domain Layer)
        $this->_table = new ProductTable($this->_setDb());
        $product = $this->_table->read($id);

        // d. Vérifier l'existence
        if (!$product) {
            $this->notFound();
            return;
        }

        // e. Appliquer une stratégie si nécessaire
        $strategy = $this->getStrategy($product);
        if ($strategy) {
            $this->_middleware = $strategy;
            $this->handle($this->getRequest());
            return;
        }

        // f. Rendre la réponse
        $this->render('Product/read', [
            'product' => $product,
            'related' => $this->getRelated($product)
        ]);
    }

    // 3. Méthodes helpers
    private function validate(int $id): bool {
        return $id > 0;
    }

    private function getStrategy($product) {
        return match($product->behavior_type) {
            'Graphics' => new Types\Graphics($this->_route),
            default => null
        };
    }

    private function getRelated($product): array {
        return $this->_table->findRelated($product->getId(), 4);
    }

    private function notFound(): void {
        $this->_response = $this->_response->withStatus(404);
        $this->render('Errors/404');
    }

    private function badRequest(string $message): void {
        $this->_response = $this->_response->withStatus(400);
        $this->json(['error' => $message]);
    }
}
```

### Actions avec paramètres de route

```php
// Route: /products/:category/:slug-:id
$router->get('/products/:category/:slug-:id', 'product.read')
    ->with('category', '([a-z\-]+)')
    ->with('slug', '([a-z0-9\-]+)')
    ->with('id', '([0-9]+)');

// Action
final class Read extends AppAction {
    public function __invoke(string $category, string $slug, int $id): void {
        // Les paramètres sont injectés automatiquement !
        // $category, $slug, $id sont disponibles directement

        $this->_table = new ProductTable($this->_setDb());
        $product = $this->_table->readBySlugAndCategory($slug, $id, $category);

        $this->render('Product/read', ['product' => $product]);
    }
}
```

### Actions API (JSON)

```php
final class GetProducts extends AppAction {

    public function __invoke(): void {
        $queries = $this->getRequest()->getQueryParams();
        $page = (int)($queries['page'] ?? 1);
        $limit = (int)($queries['limit'] ?? 20);

        $table = new ProductTable($this->_setDb());
        $products = $table->paginate($page, $limit);
        $total = $table->count();

        // Retourner du JSON
        $this->json([
            'data' => array_map(fn($p) => $p->toArray(), $products),
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
}
```

### Actions avec POST/PUT data

```php
final class UpdateCart extends AppAction {

    public function __invoke(): void {
        // Récupérer le body de la requête
        $body = $this->getRequest()->getParsedBody();

        // Valider
        if (!isset($body['quantity']) || !is_numeric($body['quantity'])) {
            $this->json(['error' => 'Invalid quantity'], 400);
            return;
        }

        $cartId = (int)$this->getRequest()->getQueryParams()['id'];
        $quantity = (int)$body['quantity'];

        // Mettre à jour
        $cartTable = new CartTable($this->_setDb());
        $success = $cartTable->updateQuantity($cartId, $quantity);

        if ($success) {
            $this->json(['success' => true, 'quantity' => $quantity]);
        } else {
            $this->json(['error' => 'Update failed'], 500);
        }
    }
}
```

---

## 🏛 Domain Layer

### Principes

- **Entities** = Objets métier avec comportement
- **Tables** = Repositories pour l'accès données
- **Decorators** = Enrichissement d'entités

### Créer une Entity

```php
<?php
declare(strict_types=1);
namespace Domain\Entity;

use Core\Domain\Entity;
use Core\Routing\RouteInterface;

class Product extends Entity {

    private NumberFormatter $_formatter;

    public function __construct(RouteInterface $route) {
        $this->_route = $route;
        $this->_formatter = new NumberFormatter('fr', NumberFormatter::CURRENCY);
    }

    // Getters avec typage
    public function getId(): int {
        return (int)$this->id;
    }

    public function getSlug(): string {
        return (string)$this->slug;
    }

    // Lazy loading avec __get
    public function __get($key) {
        $method = "_{$key}";
        if (!method_exists($this, $method)) {
            return '';
        }
        // Cache le résultat
        $this->{$key} = $this->{$method}();
        return $this->{$key};
    }

    // Propriétés calculées (lazy)
    private function _price(): string {
        $amount = $this->_withVAT((float)$this->price_amount);
        return $this->_formatter->formatCurrency($amount, 'EUR');
    }

    private function _url(): string {
        return "/products/{$this->slug}-{$this->id}";
    }

    private function _title(): string {
        // Préférence pour la version localisée
        return $this->l10n_title ?? $this->default_title;
    }

    // Méthodes métier
    private function _withVAT(float $price): float {
        if ($this->country_vat > 0) {
            return $price * (1 + ($this->tax_rate / 100));
        }
        return $price;
    }

    public function isAvailable(): bool {
        return $this->stock > 0 && $this->active === 1;
    }

    public function hasDiscount(): bool {
        return isset($this->discount_amount) && $this->discount_amount > 0;
    }

    // Pour les APIs
    public function toArray(): array {
        return [
            'id' => $this->getId(),
            'title' => $this->title,
            'slug' => $this->slug,
            'price' => $this->price,
            'url' => $this->url,
            'available' => $this->isAvailable()
        ];
    }
}
```

### Créer une Table (Repository)

```php
<?php
declare(strict_types=1);
namespace Domain\Table;

use Domain\Entity\Product as ProductEntity;
use Core\Database\MysqlDatabase;
use Core\Routing\RouteInterface;

class Product {

    private MysqlDatabase $db;
    private RouteInterface $route;
    protected string $table = 'products';

    public function __construct(MysqlDatabase $db, RouteInterface $route = null) {
        $this->db = $db;
        $this->route = $route;
    }

    /**
     * Find all products
     */
    public function findAll(array $options = []): array {
        $where = $options['where'] ?? 'active = 1';
        $order = $options['order'] ?? 'created_at DESC';
        $limit = $options['limit'] ?? 100;

        $query = "
            SELECT p.*, pl.title as l10n_title, pl.description as l10n_description
            FROM {$this->table} p
            LEFT JOIN products_l10ns pl ON p.id = pl.product_id AND pl.locale = :locale
            WHERE {$where}
            ORDER BY {$order}
            LIMIT {$limit}
        ";

        $results = $this->db->query($query, [
            'locale' => $this->route->getLang() ?? 'fr'
        ]);

        return $this->hydrateCollection($results);
    }

    /**
     * Find one by ID
     */
    public function read(int $id): ?ProductEntity {
        $query = "
            SELECT p.*, pl.title as l10n_title, pl.description as l10n_description
            FROM {$this->table} p
            LEFT JOIN products_l10ns pl ON p.id = pl.product_id AND pl.locale = :locale
            WHERE p.id = :id
        ";

        $results = $this->db->query($query, [
            'id' => $id,
            'locale' => $this->route->getLang() ?? 'fr'
        ]);

        return !empty($results) ? $this->hydrate($results[0]) : null;
    }

    /**
     * Find by slug
     */
    public function readBySlug(string $slug): ?ProductEntity {
        $query = "
            SELECT p.*, pl.title as l10n_title
            FROM {$this->table} p
            LEFT JOIN products_l10ns pl ON p.id = pl.product_id AND pl.locale = :locale
            WHERE p.slug = :slug
        ";

        $results = $this->db->query($query, [
            'slug' => $slug,
            'locale' => $this->route->getLang() ?? 'fr'
        ]);

        return !empty($results) ? $this->hydrate($results[0]) : null;
    }

    /**
     * Pagination
     */
    public function paginate(int $page = 1, int $perPage = 20): array {
        $offset = ($page - 1) * $perPage;

        $query = "
            SELECT p.*, pl.title as l10n_title
            FROM {$this->table} p
            LEFT JOIN products_l10ns pl ON p.id = pl.product_id AND pl.locale = :locale
            WHERE p.active = 1
            ORDER BY p.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ";

        $results = $this->db->query($query, [
            'locale' => $this->route->getLang() ?? 'fr'
        ]);

        return $this->hydrateCollection($results);
    }

    /**
     * Count total
     */
    public function count(array $conditions = []): int {
        $where = !empty($conditions) ? $this->buildWhere($conditions) : '1=1';

        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$where}";
        $result = $this->db->query($query);

        return (int)($result[0]['total'] ?? 0);
    }

    /**
     * Create
     */
    public function create(array $data): int {
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $query = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";

        $this->db->query($query, $data);
        return $this->db->lastInsertId();
    }

    /**
     * Update
     */
    public function update(int $id, array $data): bool {
        $sets = [];
        foreach (array_keys($data) as $field) {
            $sets[] = "{$field} = :{$field}";
        }
        $set = implode(', ', $sets);

        $query = "UPDATE {$this->table} SET {$set} WHERE id = :id";
        $data['id'] = $id;

        return $this->db->query($query, $data) !== false;
    }

    /**
     * Delete
     */
    public function delete(int $id): bool {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->db->query($query, ['id' => $id]) !== false;
    }

    /**
     * Hydrate single entity
     */
    private function hydrate(array $data): ProductEntity {
        $entity = new ProductEntity($this->route);
        foreach ($data as $key => $value) {
            $entity->$key = $value;
        }
        return $entity;
    }

    /**
     * Hydrate collection
     */
    private function hydrateCollection(array $results): array {
        return array_map(fn($data) => $this->hydrate($data), $results);
    }

    /**
     * Build WHERE clause from conditions
     */
    private function buildWhere(array $conditions): string {
        $clauses = [];
        foreach ($conditions as $field => $value) {
            $clauses[] = "{$field} = :{$field}";
        }
        return implode(' AND ', $clauses);
    }
}
```

---

## 🛣 Routing avancé

### Définir des routes

```php
// Route simple GET
$router->get('/products', 'product.index');

// Route avec paramètres
$router->get('/products/:id', 'product.read')
    ->with('id', '([0-9]+)');

// Route avec slug et ID
$router->get('/:slug-:id', 'product.read')
    ->with('slug', '([a-z0-9\-]+)')
    ->with('id', '([0-9]+)');

// Route POST
$router->post('/products', 'product.create');

// Route PUT
$router->put('/products/:id', 'product.update')
    ->with('id', '([0-9]+)');

// Route DELETE
$router->delete('/products/:id', 'product.delete')
    ->with('id', '([0-9]+)');

// Route avec middleware
$router->get('/admin/products', 'admin.product.index')
    ->middleware('Auth')
    ->middleware('AdminOnly');

// Route avec plusieurs middleware chaînés
$router->get('/:slug', 'page.read')
    ->with('slug', '([a-z0-9\-\/\~\.]+)')
    ->middleware('PoweredBy')
    ->middleware('UrlStatus')
    ->middleware('Dispatch');
```

### Patterns de routes courantes

```php
// Produits par catégorie
$router->get('/category/:category/products', 'product.byCategory')
    ->with('category', '([a-z\-]+)');

// API versionnée
$router->get('/api/v1/products', 'api.v1.product.index');
$router->get('/api/v2/products', 'api.v2.product.index');

// Routes avec extension de fichier
$router->get('/products/export.:format', 'product.export')
    ->with('format', '(csv|json|xml)');

// Routes multilingues
$router->get('/:lang/products', 'product.index')
    ->with('lang', '(fr|en|it)');
```

### Groupes de routes (à implémenter)

```php
// Concept pour futures améliorations
$router->group('/api', function($router) {
    $router->get('/products', 'api.product.index');
    $router->get('/products/:id', 'api.product.read');
})->middleware('ApiAuth');
```

---

## 🔗 Middleware

### Créer un Middleware

`Middleware/MyMiddleware.php` :

```php
<?php
declare(strict_types=1);
namespace Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MyMiddleware implements MiddlewareInterface {

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        // Avant l'Action
        // Modifier la requête, valider, logger, etc.

        // Exemple: Ajouter un attribut
        $request = $request->withAttribute('processed_by', 'MyMiddleware');

        // Passer au handler suivant
        $response = $handler->handle($request);

        // Après l'Action
        // Modifier la réponse, ajouter des headers, etc.

        $response = $response->withHeader('X-Custom-Header', 'value');

        return $response;
    }
}
```

### Middleware d'authentification

```php
<?php
namespace Middleware;

class Auth implements MiddlewareInterface {

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        // Vérifier le token/session
        $token = $request->getHeaderLine('Authorization');

        if (empty($token) || !$this->validateToken($token)) {
            // Non autorisé
            $response = new Response();
            return $response
                ->withStatus(401)
                ->withHeader('WWW-Authenticate', 'Bearer realm="API"');
        }

        // Ajouter l'utilisateur à la requête
        $user = $this->getUserFromToken($token);
        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }

    private function validateToken(string $token): bool {
        // Validation du token
        return true; // Simplifié
    }

    private function getUserFromToken(string $token) {
        // Récupérer l'utilisateur
        return ['id' => 1, 'name' => 'John'];
    }
}
```

### Middleware de logging

```php
<?php
namespace Middleware;

class Logger implements MiddlewareInterface {

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        $start = microtime(true);
        $method = $request->getMethod();
        $uri = (string)$request->getUri();

        // Traiter la requête
        $response = $handler->handle($request);

        // Logger
        $duration = microtime(true) - $start;
        $status = $response->getStatusCode();

        error_log(sprintf(
            '[%s] %s %s - %d (%s ms)',
            date('Y-m-d H:i:s'),
            $method,
            $uri,
            $status,
            number_format($duration * 1000, 2)
        ));

        return $response;
    }
}
```

---

## 🎨 Vues et templates

### Structure d'une vue

```php
<?php
// View/Products/read.php

// 1. Définir le layout
$this->layout('Layout/default', [
    'title' => $product->meta_title,
    'description' => $product->meta_description
]);
?>

<!-- 2. Contenu de la page -->
<article class="product-detail">

    <!-- 3. Affichage des données -->
    <h1><?= htmlspecialchars($product->title) ?></h1>

    <div class="product-price">
        <?= $product->price ?>
    </div>

    <div class="product-description">
        <?= $product->description ?>
    </div>

    <!-- 4. Conditions -->
    <?php if ($product->hasGallery()): ?>
        <div class="product-gallery">
            <?php foreach ($product->gallery as $image): ?>
                <img src="<?= $image->url ?>" alt="<?= htmlspecialchars($image->alt) ?>">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- 5. Inclusion de partials -->
    <?php $this->partial('Partials/add-to-cart', ['product' => $product]) ?>

</article>
```

### Layout

```php
<?php
// View/Layout/default.php
?>
<!DOCTYPE html>
<html lang="<?= $this->lang ?? 'fr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Kutvek-KitGraphik' ?></title>

    <?php if (isset($description)): ?>
        <meta name="description" content="<?= htmlspecialchars($description) ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="/css/main.css">
</head>
<body>

    <?php $this->partial('Partials/header') ?>

    <main>
        <?= $this->section('content') ?>
    </main>

    <?php $this->partial('Partials/footer') ?>

    <script src="/js/main.js"></script>
</body>
</html>
```

### Partials

```php
<?php
// View/Partials/product-card.php
?>
<div class="product-card">
    <a href="<?= $product->url ?>">
        <?php if ($product->hasImage()): ?>
            <img src="<?= $product->image_url ?>" alt="<?= htmlspecialchars($product->title) ?>">
        <?php endif; ?>

        <h3><?= htmlspecialchars($product->title) ?></h3>

        <div class="price">
            <?php if ($product->hasDiscount()): ?>
                <span class="old-price"><?= $product->original_price ?></span>
                <span class="new-price"><?= $product->price ?></span>
            <?php else: ?>
                <span><?= $product->price ?></span>
            <?php endif; ?>
        </div>
    </a>
</div>
```

---

## 🗄 Base de données

### Connexions multiples

```php
// DB par défaut
$db = $this->_setDb();

// DB spécifique
$dbKitGraphik = $this->_setDb('DbConfKitGraphik');
$dbAmerika = $this->_setDb('DbConfAmerika');

// Utilisation
$productTable = new ProductTable($dbKitGraphik);
```

### Requêtes préparées

```php
// SELECT avec paramètres
$query = "SELECT * FROM products WHERE category_id = :category AND active = :active";
$results = $db->query($query, [
    'category' => $categoryId,
    'active' => 1
]);

// INSERT
$query = "INSERT INTO products (title, slug, price) VALUES (:title, :slug, :price)";
$db->query($query, [
    'title' => 'Mon Produit',
    'slug' => 'mon-produit',
    'price' => 29.99
]);
$newId = $db->lastInsertId();

// UPDATE
$query = "UPDATE products SET title = :title WHERE id = :id";
$db->query($query, [
    'title' => 'Nouveau titre',
    'id' => $productId
]);

// DELETE
$query = "DELETE FROM products WHERE id = :id";
$db->query($query, ['id' => $productId]);
```

### Transactions

```php
try {
    $db->beginTransaction();

    // Opération 1
    $db->query("INSERT INTO orders (...) VALUES (...)", $orderData);
    $orderId = $db->lastInsertId();

    // Opération 2
    $db->query("INSERT INTO order_items (...) VALUES (...)", $itemData);

    // Opération 3
    $db->query("UPDATE products SET stock = stock - :qty WHERE id = :id", [
        'qty' => $quantity,
        'id' => $productId
    ]);

    $db->commit();

} catch (\Exception $e) {
    $db->rollBack();
    throw $e;
}
```

---

## 🌍 Internationalisation

### Configuration

Langues disponibles : `fr`, `en`, `it`

### Stockage en base

Tables avec traductions suffixées `_l10ns` :

```sql
CREATE TABLE products_l10ns (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    locale VARCHAR(5) NOT NULL,
    title VARCHAR(255),
    description TEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,
    UNIQUE KEY (product_id, locale)
);
```

### Récupération des traductions

```php
// Dans une Table
$query = "
    SELECT p.*, pl.title as l10n_title, pl.description as l10n_description
    FROM products p
    LEFT JOIN products_l10ns pl ON p.id = pl.product_id AND pl.locale = :locale
    WHERE p.id = :id
";

$result = $this->db->query($query, [
    'id' => $productId,
    'locale' => $this->route->getLang() ?? 'fr'
]);
```

### Dans les Entities

```php
private function _title(): string {
    // Utiliser la traduction si disponible, sinon le titre par défaut
    return $this->l10n_title ?? $this->default_title ?? '';
}
```

---

## 🐛 Debugging

### Affichage des erreurs

```php
// En développement (webroot/index.php)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// En production
error_reporting(E_ERROR);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log');
```

### Debug dans les Actions

```php
// Dump and die
if (isset($_GET['debug'])) {
    die(print_r($product, true));
}

// Logging
error_log(sprintf('[DEBUG] Product ID: %d', $product->getId()));

// Var dump formaté
echo '<pre>';
var_dump($data);
echo '</pre>';
exit;
```

### Debug queries SQL

```php
// Dans MysqlDatabase, ajouter du logging
public function query(string $sql, array $params = []) {
    // Log la requête
    error_log("[SQL] {$sql}");
    error_log("[PARAMS] " . json_encode($params));

    // Exécuter
    // ...
}
```

---

## ✅ Best Practices

### 1. Toujours typer

```php
// ✅ BON
public function getProduct(int $id): ?Product {
    return $this->productTable->read($id);
}

// ❌ MAUVAIS
public function getProduct($id) {
    return $this->productTable->read($id);
}
```

### 2. Une Action = Une responsabilité

```php
// ✅ BON
class ReadProduct extends AppAction { }
class CreateProduct extends AppAction { }
class UpdateProduct extends AppAction { }

// ❌ MAUVAIS
class ProductController extends Controller {
    public function read() { }
    public function create() { }
    public function update() { }
}
```

### 3. Logique métier dans le Domain

```php
// ✅ BON
// Dans Domain/Entity/Product.php
public function calculateFinalPrice(): float {
    $price = $this->base_price;
    if ($this->hasDiscount()) {
        $price -= $this->discount_amount;
    }
    return $this->_withVAT($price);
}

// Dans Action
$finalPrice = $product->calculateFinalPrice();

// ❌ MAUVAIS
// Tout dans l'Action
$price = $product->base_price;
if ($product->discount) {
    $price -= $product->discount_amount;
}
if ($product->country_vat > 0) {
    $price *= (1 + $product->tax_rate / 100);
}
```

### 4. Sécuriser les vues

```php
// ✅ BON
<?= htmlspecialchars($product->title, ENT_QUOTES, 'UTF-8') ?>

// ❌ MAUVAIS
<?= $product->title ?>
```

### 5. Utiliser les repositories

```php
// ✅ BON
$productTable = new ProductTable($db);
$products = $productTable->findByCategory($categoryId);

// ❌ MAUVAIS
$query = "SELECT * FROM products WHERE category_id = {$categoryId}";
$products = $db->query($query);
```

### 6. Valider les entrées

```php
// ✅ BON
$id = (int)$queries['id'];
if ($id <= 0) {
    $this->badRequest('Invalid ID');
    return;
}

// ❌ MAUVAIS
$id = $queries['id'];
$product = $this->table->read($id);
```

### 7. Gérer les erreurs

```php
// ✅ BON
$product = $this->table->read($id);
if (!$product) {
    $this->notFound();
    return;
}

// ❌ MAUVAIS
$product = $this->table->read($id);
$this->render('Product/read', ['product' => $product]);
```

### 8. Utiliser les constantes

```php
// ✅ BON
define('MAX_CART_ITEMS', 50);

if (count($cartItems) >= MAX_CART_ITEMS) {
    // ...
}

// ❌ MAUVAIS
if (count($cartItems) >= 50) {
    // Magic number
}
```

---

**Maintenu par**: Équipe Kutvek
**Dernière mise à jour**: Octobre 2024
