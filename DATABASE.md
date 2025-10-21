# Documentation Base de Données - Kutvek Platform

## 📋 Vue d'ensemble

La plateforme Kutvek utilise **MySQL 8.0+** avec support de connexions multiples à différentes bases de données. L'architecture suit le pattern **Repository** via les classes Table du Domain Layer.

---

## 🔌 Configuration des connexions

### Bases de données disponibles

Le système supporte plusieurs connexions simultanées configurées dans `/Config/` :

| Fichier | Base | Description |
|---------|------|-------------|
| `DbConf.php` | app_kutvek | Base principale (défaut) |
| `DbConfKitGraphik.php` | kitgraphik_db | Base KitGraphik |
| `DbConfAmerika.php` | amerika_db | Base Amerika |
| `DbConfAppKutvek.php` | app_kutvek | Base App Kutvek |

### Structure de configuration

`Config/DbConf.php` :

```php
<?php
return array(
    "db_user" => "votre_user",
    "db_pass" => "votre_password",
    "db_host" => "localhost",      // ou IP du serveur
    "db_port" => 3306,
    "db_name" => "app_kutvek",
    "charset" => "utf8mb4"
);
```

### Utilisation dans le code

```php
// Connexion par défaut
$db = $this->_setDb();

// Connexion spécifique
$dbKitGraphik = $this->_setDb('DbConfKitGraphik');
$dbAmerika = $this->_setDb('DbConfAmerika');

// Utiliser avec une Table
$productTable = new \Domain\Table\Product($dbKitGraphik);
```

### Pooling de connexions

Le système maintient un pool de connexions réutilisables :

```php
// Dans App.php
private $db_instance = [];

public function setDb($dbConf = null) {
    $config = Config::getInstance(CONFIG.DS.$dbConf.'.php');

    // Réutiliser la connexion existante
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

---

## 🗄 Structure des tables principales

### Tables de base

#### products
Catalogue des produits

```sql
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sku VARCHAR(100) UNIQUE,
    slug VARCHAR(255) UNIQUE,
    default_title VARCHAR(255),
    behavior_type VARCHAR(50),          -- 'Graphics', 'PlateStickers', 'Basics', etc.
    price JSON,                         -- {"price": 29.99, "currency": "EUR"}
    tax_rate DECIMAL(5,2) DEFAULT 20.00,
    stock INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    featured TINYINT(1) DEFAULT 0,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (slug),
    INDEX idx_category (category_id),
    INDEX idx_active (active),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### products_l10ns
Traductions des produits

```sql
CREATE TABLE products_l10ns (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    locale VARCHAR(5) NOT NULL,         -- 'fr', 'en', 'it'
    title VARCHAR(255),
    short_desc VARCHAR(500),
    description TEXT,
    features TEXT,
    composition_care TEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,

    UNIQUE KEY unique_product_locale (product_id, locale),
    INDEX idx_locale (locale),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### categories
Catégories de produits

```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    parent_id INT NULL,
    slug VARCHAR(255) UNIQUE,
    name VARCHAR(255),
    description TEXT,
    image VARCHAR(255),
    position INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_parent (parent_id),
    INDEX idx_slug (slug),
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### carts
Paniers clients

```sql
CREATE TABLE carts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    session_id VARCHAR(255),
    currency_id INT DEFAULT 1,
    country_id INT DEFAULT 1,
    voucher_code VARCHAR(50) NULL,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('active', 'abandoned', 'converted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,

    INDEX idx_session (session_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### cart_items
Items dans les paniers

```sql
CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2),
    options JSON,                       -- Options de personnalisation
    total_price DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_cart (cart_id),
    INDEX idx_product (product_id),
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### orders
Commandes

```sql
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE,
    user_id INT NULL,
    cart_id INT,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',

    -- Billing
    billing_firstname VARCHAR(100),
    billing_lastname VARCHAR(100),
    billing_email VARCHAR(255),
    billing_phone VARCHAR(50),
    billing_address TEXT,
    billing_city VARCHAR(100),
    billing_postal_code VARCHAR(20),
    billing_country VARCHAR(50),

    -- Shipping
    shipping_firstname VARCHAR(100),
    shipping_lastname VARCHAR(100),
    shipping_address TEXT,
    shipping_city VARCHAR(100),
    shipping_postal_code VARCHAR(20),
    shipping_country VARCHAR(50),
    shipping_method VARCHAR(100),
    shipping_cost DECIMAL(10,2),

    -- Payment
    payment_method VARCHAR(50),        -- 'stripe', 'paypal', 'bank_transfer'
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_transaction_id VARCHAR(255),

    -- Amounts
    subtotal DECIMAL(10,2),
    tax_amount DECIMAL(10,2),
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'EUR',

    -- Metadata
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_order_number (order_number),
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### order_items
Items des commandes

```sql
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT,
    product_sku VARCHAR(100),
    product_title VARCHAR(255),
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2),
    tax_rate DECIMAL(5,2),
    total_price DECIMAL(10,2),
    options JSON,

    INDEX idx_order (order_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### users
Utilisateurs

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    firstname VARCHAR(100),
    lastname VARCHAR(100),
    phone VARCHAR(50),
    role ENUM('customer', 'admin', 'super_admin') DEFAULT 'customer',
    active TINYINT(1) DEFAULT 1,
    email_verified_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### vehicles
Véhicules (pour housses de selle et graphics)

```sql
CREATE TABLE vehicles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    brand VARCHAR(100),
    model VARCHAR(100),
    type ENUM('moto', 'quad', 'jetski', 'snowmobile'),
    year_start INT,
    year_end INT,
    image VARCHAR(255),
    active TINYINT(1) DEFAULT 1,

    INDEX idx_brand (brand),
    INDEX idx_type (type),
    INDEX idx_years (year_start, year_end)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### vehicle_years
Années / Millésimes de véhicules

```sql
CREATE TABLE vehicle_years (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vehicle_id INT NOT NULL,
    year INT NOT NULL,
    designation VARCHAR(255),

    INDEX idx_vehicle (vehicle_id),
    INDEX idx_year (year),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### saddle_covers
Housses de selle

```sql
CREATE TABLE saddle_covers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vehicle_id INT,
    year_id INT,
    product_id INT,
    reference VARCHAR(100),
    price DECIMAL(10,2),
    stock INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1,

    INDEX idx_vehicle (vehicle_id),
    INDEX idx_product (product_id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### pages
Pages CMS

```sql
CREATE TABLE pages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(255) UNIQUE,
    template VARCHAR(100),
    status ENUM('draft', 'published') DEFAULT 'draft',
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (slug),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### pages_l10ns
Traductions des pages

```sql
CREATE TABLE pages_l10ns (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_id INT NOT NULL,
    locale VARCHAR(5) NOT NULL,
    title VARCHAR(255),
    content LONGTEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,

    UNIQUE KEY unique_page_locale (page_id, locale),
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### urls
Gestion des URLs et redirections

```sql
CREATE TABLE urls (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(255) UNIQUE,
    type VARCHAR(50),                   -- 'product', 'page', 'category'
    target_id INT,
    locale VARCHAR(5) DEFAULT 'fr',
    status_code INT DEFAULT 200,        -- 200, 301, 302, 404, 410
    redirect_to VARCHAR(255) NULL,

    INDEX idx_slug (slug),
    INDEX idx_type_target (type, target_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🏗 Vues SQL (Views)

Le système utilise des vues SQL pour optimiser certaines requêtes courantes.

### vue_catalog
Vue complète du catalogue avec traductions

```sql
CREATE VIEW vue_catalog AS
SELECT
    p.id,
    p.sku,
    p.slug,
    p.default_title,
    p.behavior_type,
    p.price,
    p.stock,
    p.active,
    p.category_id,
    c.name as category_name,
    pl.locale,
    pl.title,
    pl.description,
    pl.meta_title,
    pl.meta_description
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN products_l10ns pl ON p.id = pl.product_id
WHERE p.active = 1;
```

### vue_menu_items
Vue pour la navigation

```sql
CREATE VIEW vue_menu_items AS
SELECT
    c.id,
    c.parent_id,
    c.slug,
    c.name,
    c.position,
    COUNT(p.id) as product_count
FROM categories c
LEFT JOIN products p ON c.id = p.category_id AND p.active = 1
WHERE c.active = 1
GROUP BY c.id
ORDER BY c.position;
```

---

## 🔍 Requêtes courantes

### Récupérer un produit avec traductions

```sql
SELECT
    p.*,
    pl.title as l10n_title,
    pl.description as l10n_description,
    pl.meta_title,
    pl.meta_description
FROM products p
LEFT JOIN products_l10ns pl ON p.id = pl.product_id AND pl.locale = :locale
WHERE p.id = :id AND p.active = 1;
```

### Recherche de produits par catégorie

```sql
SELECT
    p.id,
    p.slug,
    pl.title,
    p.price,
    p.stock
FROM products p
INNER JOIN products_l10ns pl ON p.id = pl.product_id AND pl.locale = :locale
WHERE p.category_id = :category_id
    AND p.active = 1
ORDER BY p.featured DESC, p.created_at DESC
LIMIT :limit OFFSET :offset;
```

### Panier avec items

```sql
SELECT
    c.id as cart_id,
    c.total_amount,
    c.currency_id,
    ci.id as item_id,
    ci.quantity,
    ci.unit_price,
    ci.options,
    p.slug as product_slug,
    pl.title as product_title
FROM carts c
LEFT JOIN cart_items ci ON c.id = ci.cart_id
LEFT JOIN products p ON ci.product_id = p.id
LEFT JOIN products_l10ns pl ON p.id = pl.product_id AND pl.locale = :locale
WHERE c.id = :cart_id;
```

### Commandes d'un utilisateur

```sql
SELECT
    o.id,
    o.order_number,
    o.status,
    o.payment_status,
    o.total_amount,
    o.currency,
    o.created_at,
    COUNT(oi.id) as items_count
FROM orders o
LEFT JOIN order_items oi ON o.id = oi.order_id
WHERE o.user_id = :user_id
GROUP BY o.id
ORDER BY o.created_at DESC;
```

### Produits liés (related)

```sql
SELECT
    p.id,
    p.slug,
    pl.title,
    p.price
FROM products p
INNER JOIN products_l10ns pl ON p.id = pl.product_id AND pl.locale = :locale
WHERE p.category_id = :category_id
    AND p.id != :current_product_id
    AND p.active = 1
ORDER BY RAND()
LIMIT :limit;
```

### Recherche full-text

```sql
SELECT
    p.id,
    p.slug,
    pl.title,
    p.price,
    MATCH(pl.title, pl.description) AGAINST(:search_term IN NATURAL LANGUAGE MODE) as relevance
FROM products p
INNER JOIN products_l10ns pl ON p.id = pl.product_id AND pl.locale = :locale
WHERE MATCH(pl.title, pl.description) AGAINST(:search_term IN NATURAL LANGUAGE MODE)
    AND p.active = 1
ORDER BY relevance DESC
LIMIT :limit;
```

---

## 🔧 Utilisation via les Tables (Repositories)

### Exemple complet

`Domain/Table/Product.php` :

```php
<?php
declare(strict_types=1);
namespace Domain\Table;

use Domain\Entity\Product as ProductEntity;
use Core\Database\MysqlDatabase;

class Product {

    private MysqlDatabase $db;
    private string $table = 'products';

    public function __construct(MysqlDatabase $db) {
        $this->db = $db;
    }

    /**
     * Find all products
     */
    public function findAll(int $limit = 100): array {
        $query = "
            SELECT p.*, pl.title as l10n_title
            FROM {$this->table} p
            LEFT JOIN products_l10ns pl ON p.id = pl.product_id AND pl.locale = :locale
            WHERE p.active = 1
            ORDER BY p.created_at DESC
            LIMIT {$limit}
        ";

        $results = $this->db->query($query, ['locale' => 'fr']);
        return $this->hydrateCollection($results);
    }

    /**
     * Find one by ID
     */
    public function read(int $id): ?ProductEntity {
        $query = "
            SELECT p.*, pl.*
            FROM {$this->table} p
            LEFT JOIN products_l10ns pl ON p.id = pl.product_id AND pl.locale = :locale
            WHERE p.id = :id
        ";

        $results = $this->db->query($query, [
            'id' => $id,
            'locale' => 'fr'
        ]);

        return !empty($results) ? $this->hydrate($results[0]) : null;
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
     * Delete (soft delete recommandé)
     */
    public function delete(int $id): bool {
        // Soft delete
        return $this->update($id, ['active' => 0]);

        // Hard delete (à éviter)
        // $query = "DELETE FROM {$this->table} WHERE id = :id";
        // return $this->db->query($query, ['id' => $id]) !== false;
    }

    /**
     * Hydrate entity
     */
    private function hydrate(array $data): ProductEntity {
        $entity = new ProductEntity();
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
}
```

---

## 🔐 Sécurité

### Requêtes préparées

**TOUJOURS** utiliser des requêtes préparées :

```php
// ✅ BON
$query = "SELECT * FROM products WHERE id = :id";
$results = $db->query($query, ['id' => $id]);

// ❌ MAUVAIS - SQL Injection !
$query = "SELECT * FROM products WHERE id = {$id}";
$results = $db->query($query);
```

### Validation des données

```php
// Avant insertion
$data = [
    'title' => filter_var($input['title'], FILTER_SANITIZE_STRING),
    'price' => (float)$input['price'],
    'stock' => (int)$input['stock']
];

$this->productTable->create($data);
```

### Transactions

Pour les opérations critiques :

```php
try {
    $db->beginTransaction();

    // Créer la commande
    $orderId = $orderTable->create($orderData);

    // Créer les items
    foreach ($items as $item) {
        $orderItemTable->create([
            'order_id' => $orderId,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity']
        ]);

        // Décrémenter le stock
        $productTable->decrementStock($item['product_id'], $item['quantity']);
    }

    $db->commit();

} catch (\Exception $e) {
    $db->rollBack();
    throw $e;
}
```

---

## 📊 Optimisation

### Index

Créer des index sur les colonnes fréquemment utilisées :

```sql
-- Index sur colonnes de recherche
CREATE INDEX idx_slug ON products(slug);
CREATE INDEX idx_active ON products(active);
CREATE INDEX idx_category ON products(category_id);

-- Index composite
CREATE INDEX idx_active_featured ON products(active, featured);

-- Index sur foreign keys
CREATE INDEX idx_cart_id ON cart_items(cart_id);
CREATE INDEX idx_product_id ON cart_items(product_id);

-- Index FULLTEXT pour recherche
CREATE FULLTEXT INDEX idx_fulltext_product
ON products_l10ns(title, description);
```

### Requêtes optimisées

```php
// ✅ BON - Une seule requête avec JOIN
$query = "
    SELECT p.*, pl.title, pl.description, c.name as category_name
    FROM products p
    LEFT JOIN products_l10ns pl ON p.id = pl.product_id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = :id
";

// ❌ MAUVAIS - N+1 queries
$product = $productTable->read($id);
$translation = $translationTable->findByProduct($id);
$category = $categoryTable->read($product->category_id);
```

### Cache

Mettre en cache les résultats coûteux :

```php
// À implémenter avec Redis/Memcached
$cacheKey = "product_{$id}_{$locale}";
$product = $cache->get($cacheKey);

if (!$product) {
    $product = $productTable->read($id);
    $cache->set($cacheKey, $product, 3600); // 1 heure
}
```

---

## 🔄 Migrations (à implémenter)

### Structure recommandée

```
database/
├── migrations/
│   ├── 001_create_products_table.sql
│   ├── 002_create_products_l10ns_table.sql
│   ├── 003_create_categories_table.sql
│   └── ...
├── seeds/
│   ├── categories_seed.sql
│   └── products_seed.sql
└── schema.sql (dump complet)
```

### Script de migration

```bash
#!/bin/bash
# database/migrate.sh

DB_NAME="app_kutvek"
DB_USER="root"
DB_PASS="password"

for file in database/migrations/*.sql; do
    echo "Running migration: $file"
    mysql -u $DB_USER -p$DB_PASS $DB_NAME < $file
done
```

---

## 📝 Maintenance

### Backup

```bash
# Backup complet
mysqldump -u root -p app_kutvek > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup structure seule
mysqldump -u root -p --no-data app_kutvek > schema.sql

# Backup données seules
mysqldump -u root -p --no-create-info app_kutvek > data.sql
```

### Restore

```bash
mysql -u root -p app_kutvek < backup_20241021.sql
```

### Nettoyage

```sql
-- Supprimer les paniers abandonnés de plus de 30 jours
DELETE FROM carts
WHERE status = 'abandoned'
AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Archiver les commandes anciennes
INSERT INTO orders_archive
SELECT * FROM orders
WHERE created_at < DATE_SUB(NOW(), INTERVAL 2 YEAR);

DELETE FROM orders
WHERE created_at < DATE_SUB(NOW(), INTERVAL 2 YEAR);
```

---

**Maintenu par**: Équipe Kutvek
**Dernière mise à jour**: Octobre 2024
