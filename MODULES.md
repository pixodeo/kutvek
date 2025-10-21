# Documentation des Modules - Kutvek Platform

## 📋 Vue d'ensemble

La plateforme Kutvek est organisée en modules fonctionnels indépendants, chacun gérant un domaine métier spécifique. Cette architecture modulaire facilite la maintenance et l'évolution du système.

### Modules disponibles

- **Product** - Gestion des produits génériques
- **Cart** - Panier d'achat
- **Checkout** - Processus de commande
- **SaddleCover** - Housses de selle pour véhicules
- **Sportswear** - Vêtements personnalisés
- **Vehicle** - Catalogue de véhicules
- **Page** - Système CMS pour pages de contenu
- **Category** - Catégories de produits
- **Catalog** - Navigation dans le catalogue
- **DealsCorner** - Promotions et bonnes affaires

---

## 📦 Module Product

**Namespace**: `App\Product`
**Table**: `Domain\Table\Product`
**Entity**: `Domain\Entity\Product`

### Description

Gestion des produits génériques de la plateforme. Support de différents types de produits via le pattern Strategy.

### Actions

#### Read
**Fichier**: `App/Product/Read.php`
**Route**: `GET /:slug-:id`

Affiche le détail d'un produit.

```php
final class Read extends AppAction {
    public function __invoke(): void {
        $queries = $this->getRequest()->getQueryParams();
        $id = (int)$queries['id'];

        $this->_table = new Product($this->_setDb());
        $product = $this->_table->read($id);

        // Application d'une stratégie selon le type
        $strategy = match($product->behavior_type) {
            'Graphics' => new Types\Graphics($this->_route),
            'PlateStickers' => new Types\PlateStickers($this->_route),
            'AccessoryStickers' => new Types\AccessorySticker($this->_route),
            'EngineGuard' => new Types\EngineGuard($this->_route),
            default => new Types\Basics($this->_route)
        };

        $strategy->setProduct($product);
        $this->_middleware = $strategy;
        $this->handle($this->getRequest());
    }
}
```

#### Gallery
**Fichier**: `App/Product/Gallery.php`
**Route**: `GET /products/:id/gallery`

Récupère la galerie d'images d'un produit.

#### Options
**Fichier**: `App/Product/Options.php`
**Route**: `GET /products/:id/options`

Récupère les options de personnalisation.

#### AddToCart
**Fichier**: `App/Product/AddToCart.php`
**Route**: `POST /product/add-to-cart`

Ajoute un produit au panier.

### Types de produits (Strategies)

#### Graphics
**Fichier**: `App/Product/Types/Graphics.php`

Gestion des kits graphiques pour véhicules.

**Spécificités**:
- Personnalisation couleurs
- Numéros de course
- Templates par véhicule

#### PlateStickers
**Fichier**: `App/Product/Types/PlateStickers.php`

Autocollants de plaques.

**Spécificités**:
- Personnalisation numéro
- Choix couleurs
- Format de plaque

#### Basics
**Fichier**: `App/Product/Types/Basics.php`

Produits standards sans personnalisation spéciale.

### Entity

`Domain/Entity/Product.php`

Propriétés calculées via lazy loading :
- `cost` - Prix formaté avec devise
- `amount` - Prix numérique avec TVA
- `title` - Titre avec fallback traduction
- `url` - URL du produit
- `description` - Description traduite

### Table

`Domain/Table/Product.php`

Méthodes principales :
- `findAll()` - Liste tous les produits
- `read($id)` - Lit un produit par ID
- `readBySlug($slug)` - Lit par slug
- `findByCategory($categoryId)` - Produits par catégorie
- `findRelated($productId, $limit)` - Produits liés
- `search($term)` - Recherche texte

---

## 🛒 Module Cart

**Namespace**: `App\Cart`
**Table**: `Domain\Table\Cart`
**Entity**: `Domain\Entity\Cart`

### Description

Gestion complète du panier d'achat avec items, quantités, codes promo et calcul de totaux.

### Actions

#### Overview
**Fichier**: `App/Cart/Overview.php`
**Route**: `GET /checkout/cart-overview`

Récupère le contenu complet du panier.

```php
final class Overview extends AppAction {
    public function __invoke(): void {
        $cartId = $this->getCartId();

        $cartTable = new Cart($this->_setDb());
        $cart = $cartTable->getWithItems($cartId);

        $this->json([
            'cart' => $cart->toArray(),
            'items' => array_map(fn($i) => $i->toArray(), $cart->items)
        ]);
    }
}
```

#### UpdateItemQty
**Fichier**: `App/Cart/UpdateItemQty.php`
**Route**: `PUT /carts/:id/items/:item/qty`

Met à jour la quantité d'un item.

#### DeleteItem
**Fichier**: `App/Cart/DeleteItem.php`
**Route**: `DELETE /carts/:id/items/:item`

Supprime un item du panier.

#### AddVoucher
**Fichier**: `App/Cart/AddVoucher.php`
**Route**: `PUT /carts/:id/add-voucher`

Applique un code promo.

#### DeleteVoucher
**Fichier**: `App/Cart/DeleteVoucher.php`
**Route**: `PUT /carts/:id/delete-voucher`

Retire un code promo.

### Entity

`Domain/Entity/Cart.php`

Propriétés :
- `id` - ID du panier
- `user_id` - ID utilisateur (null si guest)
- `session_id` - ID de session
- `total_amount` - Montant total
- `items` - Collection d'items
- `voucher_code` - Code promo appliqué
- `discount_amount` - Montant de réduction

Méthodes :
- `addItem($product, $quantity, $options)` - Ajoute un item
- `removeItem($itemId)` - Retire un item
- `updateQuantity($itemId, $quantity)` - Met à jour quantité
- `calculateTotal()` - Recalcule le total
- `applyVoucher($code)` - Applique un code promo
- `isEmpty()` - Vérifie si vide

### Table

`Domain/Table/Cart.php`

Méthodes :
- `getOrCreate($sessionId)` - Récupère ou crée un panier
- `getWithItems($cartId)` - Récupère avec items
- `addItem($cartId, $data)` - Ajoute un item
- `updateItemQty($itemId, $quantity)` - Met à jour quantité
- `deleteItem($itemId)` - Supprime un item
- `applyVoucher($cartId, $code)` - Applique code promo
- `clearVoucher($cartId)` - Retire code promo

---

## 💳 Module Checkout

**Namespace**: `App\Checkout`
**Table**: `Domain\Table\Checkout`

### Description

Processus complet de commande en plusieurs étapes : panier → expédition → paiement → confirmation.

### Actions

#### Cart
**Fichier**: `App/Checkout/Cart.php`
**Route**: `GET /checkout/cart`

Affiche la page panier (étape 1).

#### Shipping
**Fichier**: `App/Checkout/Shipping.php`
**Route**: `GET /checkout/shipping`

Affiche la page de sélection de livraison (étape 2).

#### Payment
**Fichier**: `App/Checkout/Payment.php`
**Route**: `GET /checkout/pay`

Affiche la page de paiement (étape 3).

#### Create
**Fichier**: `App/Checkout/Create.php`
**Route**: `POST /checkout/:order/:psp`

Crée la commande et initialise le paiement.

```php
final class Create extends AppAction {
    public function __invoke(int $order, string $psp): void {
        $body = $this->getRequest()->getParsedBody();

        // Validation
        $this->validate($body);

        // Création de la commande
        $checkoutTable = new Checkout($this->_setDb());
        $orderId = $checkoutTable->createOrder($body);

        // Initialisation du paiement
        $paymentService = $this->getPaymentService($psp);
        $payment = $paymentService->initialize($orderId, $body);

        $this->json([
            'success' => true,
            'order' => $checkoutTable->getOrder($orderId),
            'payment' => $payment
        ]);
    }
}
```

#### Capture
**Fichier**: `App/Checkout/Capture.php`
**Route**: `POST /checkout/:order/:psp/capture`

Capture/confirme le paiement.

#### MapboxPoints
**Fichier**: `App/Checkout/MapboxPoints.php`
**Route**: `GET /cart/stores`

Récupère les points de retrait (Click & Collect).

### Workflow de commande

```
1. Cart (Panier)
   ↓
2. Shipping (Adresse + mode livraison)
   ↓
3. Payment (Moyen de paiement)
   ↓
4. Create Order (Création commande)
   ↓
5. Payment Processing (Traitement paiement)
   ↓
6. Capture (Confirmation)
   ↓
7. Confirmation (Page de remerciement)
```

### Table

`Domain/Table/Checkout.php`

Méthodes :
- `createOrder($data)` - Crée une commande
- `getOrder($orderId)` - Récupère une commande
- `updateStatus($orderId, $status)` - Met à jour le statut
- `calculateShipping($cartId, $method)` - Calcule frais de port
- `validateAddress($address)` - Valide une adresse
- `getShippingMethods()` - Récupère modes de livraison
- `getPaymentMethods()` - Récupère moyens de paiement

---

## 🪑 Module SaddleCover

**Namespace**: `App\SaddleCover`
**Table**: `Domain\Table\SaddleCover`
**Entity**: `Domain\Entity\SaddleCover`

### Description

Gestion des housses de selle personnalisées pour motos, quads, jetskis et motoneiges.

### Actions

#### Index
**Fichier**: `App/SaddleCover/Index.php`
**Route**: `GET /housses-de-selle` (FR), `/seat-covers` (EN)

Liste les housses de selle avec filtres.

#### Read
**Fichier**: `App/SaddleCover/Read.php`
**Route**: `GET /:section/:slug-:id`

Détail d'une housse de selle.

#### Filter
**Fichier**: `App/SaddleCover/Filter.php`
**Route**: `GET /:slug/filters`

Récupère les filtres disponibles.

#### RefreshFilter
**Fichier**: `App/SaddleCover/RefreshFilter.php`
**Route**: `GET /:slug/refresh-filters`

Rafraîchit les filtres selon sélections.

#### AddToCart
**Fichier**: `App/SaddleCover/AddToCart.php`
**Route**: `POST /:slug`

Ajoute une housse au panier.

#### Export
**Fichier**: `App/SaddleCover/Export.php`
**Route**: `GET /:slug/export`

Exporte le catalogue (CSV/PDF).

### Filtrage

Système de filtrage dynamique :
- Marque de véhicule
- Modèle
- Année/Millésime
- Type (Moto, Quad, Jetski, Snowmobile)

### Entity

`Domain/Entity/SaddleCover.php`

Propriétés spécifiques :
- `vehicle_brand` - Marque du véhicule
- `vehicle_model` - Modèle
- `year` - Année
- `reference` - Référence produit
- `compatibility` - Compatibilité véhicules

---

## 👕 Module Sportswear

**Namespace**: `App\Sportswear`
**Table**: `Domain\Table\Sportswear`

### Description

Vêtements de sport personnalisables (maillots, pantalons, gants).

### Actions

#### Read
**Fichier**: `App/Sportswear/Read.php`
**Route**: `GET /sportswear/:slug-:id`

Détail d'un vêtement.

#### AddToCart
**Fichier**: `App/Sportswear/AddToCart.php`
**Route**: `POST /sportswear`

Ajoute un vêtement personnalisé au panier.

### Personnalisation

Options disponibles :
- Taille (XS, S, M, L, XL, XXL, XXXL)
- Couleurs primaire/secondaire
- Texte personnalisé
- Numéro
- Logo/Sponsors

---

## 🏍 Module Vehicle

**Namespace**: `App\Vehicle`
**Table**: `Domain\Table\Vehicle`

### Description

Catalogue de véhicules (motos, quads, jetskis, motoneiges) pour associer aux produits.

### Actions

#### GraphicYears
**Fichier**: `App/Vehicle/GraphicYears.php`
**Route**: `GET /vehicle/:id/years`

Récupère les années disponibles pour un véhicule.

#### YearKitTypes
**Fichier**: `App/Vehicle/YearKitTypes.php`
**Route**: `GET /vehicle-years/:id/kit-types`

Types de kits pour une année.

#### YearTypes
**Fichier**: `App/Vehicle/YearTypes.php`
**Route**: `GET /vehicles/:id/years/:year_id/year-types`

Gabarits/types pour véhicule + année.

### Hiérarchie

```
Vehicle (Yamaha YZ250F)
  ├── Year (2024)
  │   ├── YearType (Standard)
  │   │   └── KitType (Kit Complet 16 pièces)
  │   └── YearType (Factory Edition)
  │       └── KitType (Kit FE 16 pièces)
  └── Year (2023)
      └── YearType (Standard)
          └── KitType (Kit Complet 16 pièces)
```

---

## 📄 Module Page

**Namespace**: `App\Page`
**Table**: `Domain\Table\Page`

### Description

Système CMS pour pages de contenu statiques et dynamiques.

### Actions

#### Homepage
**Fichier**: `App/Page/Homepage.php`
**Route**: `GET /`

Page d'accueil.

#### Read
**Fichier**: `App/Page/Read.php`
**Route**: `GET /:slug`

Affiche une page de contenu.

### Fonctionnalités

- Templates personnalisables
- Multi-langue
- SEO (meta title, description)
- Widgets intégrables
- Statut (draft/published)

---

## 🏷 Module Category

**Namespace**: `App\Category`
**Table**: `Domain\Table\Category`

### Description

Gestion des catégories de produits avec hiérarchie parent/enfant.

### Structure

Catégories hiérarchiques :

```
Graphics
  ├── Motos
  │   ├── Motocross
  │   └── Enduro
  ├── Quads
  └── Jetskis

Housses de selle
  ├── Motos
  └── Quads

Sportswear
  ├── Maillots
  ├── Pantalons
  └── Gants
```

---

## 📚 Module Catalog

**Namespace**: `App\Catalog`
**Table**: `Domain\Table\Catalog`

### Description

Navigation et recherche dans le catalogue de produits.

### Fonctionnalités

- Listing avec filtres
- Tri (prix, popularité, nouveautés)
- Pagination
- Recherche full-text
- Filtres multi-critères

---

## 💰 Module DealsCorner

**Namespace**: `App\DealsCorner`
**Table**: `Domain\Table\DealsCorner`

### Description

Gestion des promotions et bonnes affaires.

### Fonctionnalités

- Produits en promotion
- Ventes flash
- Codes promo
- Bundles/Packs
- Outlet

---

## 🧩 Création d'un nouveau module

### Template

```php
<?php
declare(strict_types=1);
namespace App\MonModule;

use App\AppAction;
use Domain\Table\MonModule as MonModuleTable;

final class Index extends AppAction {

    private MonModuleTable $_table;

    public function __invoke(): void {
        // 1. Initialisation
        $this->_table = new MonModuleTable($this->_setDb());

        // 2. Récupération données
        $items = $this->_table->findAll();

        // 3. Rendu
        $this->render('MonModule/index', [
            'items' => $items,
            'title' => 'Mon Module'
        ]);
    }
}
```

### Checklist

- [ ] Créer namespace `App/MonModule/`
- [ ] Créer les Actions (Index, Read, Create, etc.)
- [ ] Créer Entity `Domain/Entity/MonModule.php`
- [ ] Créer Table `Domain/Table/MonModule.php`
- [ ] Créer les vues `View/MonModule/`
- [ ] Ajouter les routes dans `webroot/index.php`
- [ ] Créer les tables SQL
- [ ] Ajouter les tests
- [ ] Documenter dans MODULES.md

---

## 📊 Statistiques des modules

| Module | Actions | Routes | Tables DB | Complexité |
|--------|---------|--------|-----------|------------|
| Product | 5 | 4 | 2 | Moyenne |
| Cart | 5 | 5 | 2 | Moyenne |
| Checkout | 6 | 6 | 3 | Élevée |
| SaddleCover | 6 | 6 | 3 | Élevée |
| Sportswear | 2 | 2 | 2 | Faible |
| Vehicle | 3 | 3 | 2 | Moyenne |
| Page | 2 | 2 | 2 | Faible |
| Category | 2 | 2 | 1 | Faible |
| Catalog | 3 | 3 | 1 | Moyenne |
| DealsCorner | 4 | 4 | 2 | Moyenne |

---

## 🔗 Dépendances entre modules

```
Product
  ├── Category (relation)
  └── Cart (ajout au panier)

Cart
  ├── Product (items)
  └── Checkout (conversion)

Checkout
  ├── Cart (source)
  └── Order (création)

SaddleCover
  ├── Vehicle (compatibilité)
  └── Product (héritage)

Sportswear
  └── Product (héritage)

Vehicle
  └── SaddleCover (relation)
  └── Product/Graphics (relation)
```

---

**Maintenu par**: Équipe Kutvek
**Dernière mise à jour**: Octobre 2024
