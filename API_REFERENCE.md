# API Reference - Kutvek Platform

## 📋 Vue d'ensemble

Documentation complète des endpoints API de la plateforme Kutvek. L'API suit les principes REST et retourne des réponses HTML ou JSON selon le contexte.

### Base URL

```
Production: https://demo.kutvek-kitgraphik.com
Development: http://localhost/kutvek
API: https://dev.kutvek.com
```

### Format des réponses

- **HTML**: Pour les pages web (Content-Type: text/html)
- **JSON**: Pour les API calls (Content-Type: application/json)

### Authentification

Certains endpoints requièrent une authentification (à implémenter) :

```http
Authorization: Bearer {token}
```

---

## 🏠 Pages & Content

### Homepage

```http
GET /
```

Affiche la page d'accueil.

**Middleware**: `PoweredBy`

**Response** (HTML):
```html
<!DOCTYPE html>
<html>
<!-- Homepage content -->
</html>
```

---

### Page statique

```http
GET /:slug
```

Affiche une page de contenu CMS.

**Paramètres**:
- `slug` (string): Slug de la page (ex: `about-us`, `contact`, `legal/terms`)

**Exemple**:
```http
GET /about-us
GET /legal/privacy-policy
```

**Middleware**: `PoweredBy`, `UrlStatus`, `Dispatch`

---

## 🛒 Cart (Panier)

### Ajouter un code promo

```http
PUT /carts/:id/add-voucher
```

**Paramètres URL**:
- `id` (integer): ID du panier

**Body** (JSON):
```json
{
  "voucher_code": "SUMMER2024"
}
```

**Response** (JSON):
```json
{
  "success": true,
  "cart": {
    "id": 123,
    "total_amount": 89.99,
    "discount_amount": 10.00,
    "voucher_code": "SUMMER2024"
  }
}
```

---

### Supprimer un code promo

```http
PUT /carts/:id/delete-voucher
```

**Paramètres URL**:
- `id` (integer): ID du panier

**Response** (JSON):
```json
{
  "success": true,
  "cart": {
    "id": 123,
    "total_amount": 99.99,
    "discount_amount": 0.00,
    "voucher_code": null
  }
}
```

---

### Mettre à jour quantité d'un item

```http
PUT /carts/:id/items/:item/qty
```

**Paramètres URL**:
- `id` (integer): ID du panier
- `item` (integer): ID de l'item

**Body** (JSON):
```json
{
  "quantity": 3
}
```

**Response** (JSON):
```json
{
  "success": true,
  "item": {
    "id": 456,
    "cart_id": 123,
    "product_id": 789,
    "quantity": 3,
    "unit_price": 29.99,
    "total_price": 89.97
  }
}
```

**Codes d'erreur**:
- `400`: Quantité invalide
- `404`: Item ou panier non trouvé
- `422`: Stock insuffisant

---

### Supprimer un item du panier

```http
DELETE /carts/:id/items/:item
```

**Paramètres URL**:
- `id` (integer): ID du panier
- `item` (integer): ID de l'item

**Response** (JSON):
```json
{
  "success": true,
  "cart": {
    "id": 123,
    "items_count": 2,
    "total_amount": 59.98
  }
}
```

---

### Aperçu du panier

```http
GET /checkout/cart-overview
```

Récupère le contenu complet du panier.

**Middleware**: `PoweredBy`

**Response** (JSON):
```json
{
  "cart": {
    "id": 123,
    "total_amount": 149.97,
    "currency": "EUR",
    "items": [
      {
        "id": 1,
        "product_id": 789,
        "product_title": "Kit graphique MX",
        "quantity": 2,
        "unit_price": 49.99,
        "total_price": 99.98,
        "options": {
          "color": "red",
          "number": "24"
        }
      },
      {
        "id": 2,
        "product_id": 790,
        "product_title": "Housse de selle",
        "quantity": 1,
        "unit_price": 49.99,
        "total_price": 49.99
      }
    ]
  }
}
```

---

## 🛍 Checkout (Commande)

### Page panier

```http
GET /checkout/cart
```

Affiche la page du panier.

**Middleware**: `PoweredBy`

**Response**: HTML

---

### Page expédition

```http
GET /checkout/shipping
```

Affiche la page de sélection de l'expédition.

**Middleware**: `PoweredBy`

**Response**: HTML

---

### Page paiement

```http
GET /checkout/pay
```

Affiche la page de paiement.

**Middleware**: `PoweredBy`

**Response**: HTML

---

### Créer une commande

```http
POST /checkout/:order/:psp
```

Crée une commande et initialise le paiement.

**Paramètres URL**:
- `order` (integer): ID de la pré-commande
- `psp` (string): Processeur de paiement (`stripe`, `paypal`, `bank-transfer`)

**Body** (JSON):
```json
{
  "billing": {
    "firstname": "Jean",
    "lastname": "Dupont",
    "email": "jean.dupont@example.com",
    "phone": "+33612345678",
    "address": "123 Rue de la Paix",
    "city": "Paris",
    "postal_code": "75001",
    "country": "FR"
  },
  "shipping": {
    "same_as_billing": false,
    "firstname": "Jean",
    "lastname": "Dupont",
    "address": "456 Avenue des Champs",
    "city": "Lyon",
    "postal_code": "69001",
    "country": "FR"
  },
  "shipping_method": "standard",
  "payment_method": "stripe"
}
```

**Response** (JSON):
```json
{
  "success": true,
  "order": {
    "id": 1001,
    "order_number": "ORD-2024-1001",
    "status": "pending",
    "payment_status": "pending",
    "total_amount": 149.97,
    "currency": "EUR"
  },
  "payment": {
    "provider": "stripe",
    "client_secret": "pi_xxx_secret_xxx",
    "publishable_key": "pk_test_xxx"
  }
}
```

**Middleware**: `PoweredBy`

---

### Capturer un paiement

```http
POST /checkout/:order/:psp/capture
```

Capture/confirme un paiement.

**Paramètres URL**:
- `order` (integer): ID de la commande
- `psp` (string): Processeur de paiement

**Body** (JSON):
```json
{
  "payment_intent_id": "pi_xxx",
  "payment_method_id": "pm_xxx"
}
```

**Response** (JSON):
```json
{
  "success": true,
  "order": {
    "id": 1001,
    "order_number": "ORD-2024-1001",
    "status": "processing",
    "payment_status": "paid"
  }
}
```

**Middleware**: `PoweredBy`

---

### Page suivante (funnel)

```http
GET /checkout/next
```

Redirige vers la prochaine étape du checkout.

**Middleware**: `PoweredBy`

**Response**: Redirection 303

---

### Points de collecte (Mapbox)

```http
GET /cart/stores
```

Récupère les points de retrait disponibles.

**Query params**:
- `lat` (float, optional): Latitude
- `lng` (float, optional): Longitude
- `postal_code` (string, optional): Code postal

**Response** (JSON):
```json
{
  "stores": [
    {
      "id": 1,
      "name": "Kutvek Store Paris",
      "address": "123 Rue Example",
      "city": "Paris",
      "postal_code": "75001",
      "coordinates": {
        "lat": 48.8566,
        "lng": 2.3522
      },
      "hours": "9h-18h",
      "distance": 2.5
    }
  ]
}
```

**Middleware**: `PoweredBy`

---

## 📦 Products

### Détail d'un produit

```http
GET /:slug-:id
```

Affiche le détail d'un produit.

**Paramètres**:
- `slug` (string): Slug du produit
- `id` (integer): ID du produit

**Exemple**:
```http
GET /kit-graphique-yamaha-yz250f-2024-125
```

**Middleware**: `PoweredBy`, `IsPage`, `Dispatch`

**Response**: HTML

---

### Galerie produit

```http
GET /products/:id/gallery
```

Récupère la galerie d'images d'un produit.

**Paramètres URL**:
- `id` (integer): ID du produit

**Middleware**: `PoweredBy`

**Response** (JSON):
```json
{
  "product_id": 125,
  "images": [
    {
      "id": 1,
      "url": "https://demo.kutvek.com/img/products/kit-yz250f-1.jpg",
      "alt": "Kit graphique Yamaha YZ250F vue 1",
      "position": 1
    },
    {
      "id": 2,
      "url": "https://demo.kutvek.com/img/products/kit-yz250f-2.jpg",
      "alt": "Kit graphique Yamaha YZ250F vue 2",
      "position": 2
    }
  ]
}
```

---

### Options produit

```http
GET /products/:id/options
```

Récupère les options de personnalisation d'un produit.

**Paramètres URL**:
- `id` (integer): ID du produit

**Middleware**: `PoweredBy`

**Response** (JSON):
```json
{
  "product_id": 125,
  "options": [
    {
      "id": 1,
      "name": "Couleur principale",
      "type": "color",
      "required": true,
      "values": [
        {"value": "red", "label": "Rouge", "price_modifier": 0.00},
        {"value": "blue", "label": "Bleu", "price_modifier": 0.00},
        {"value": "custom", "label": "Personnalisé", "price_modifier": 10.00}
      ]
    },
    {
      "id": 2,
      "name": "Numéro de course",
      "type": "text",
      "required": false,
      "max_length": 3,
      "price_modifier": 5.00
    }
  ]
}
```

---

### Ajouter au panier

```http
POST /product/add-to-cart
```

Ajoute un produit au panier.

**Body** (JSON):
```json
{
  "product_id": 125,
  "quantity": 2,
  "options": {
    "color": "red",
    "number": "24",
    "custom_text": "RIDER NAME"
  }
}
```

**Response** (JSON):
```json
{
  "success": true,
  "cart": {
    "id": 123,
    "items_count": 3,
    "total_amount": 199.97
  },
  "item": {
    "id": 789,
    "product_id": 125,
    "quantity": 2,
    "unit_price": 49.99,
    "total_price": 99.98
  }
}
```

**Middleware**: `PoweredBy`

---

## 🏍 Vehicles

### Années/Millésimes d'un véhicule

```http
GET /vehicle/:id/years
```

Récupère les années disponibles pour un véhicule.

**Paramètres URL**:
- `id` (integer): ID du véhicule

**Middleware**: `PoweredBy`

**Response** (JSON):
```json
{
  "vehicle_id": 42,
  "brand": "Yamaha",
  "model": "YZ250F",
  "years": [
    {"id": 1, "year": 2024, "designation": "2024"},
    {"id": 2, "year": 2023, "designation": "2023"},
    {"id": 3, "year": 2022, "designation": "2019-2022"}
  ]
}
```

---

### Types de kit par année

```http
GET /vehicle-years/:id/kit-types
```

Récupère les types de kits disponibles pour une année.

**Paramètres URL**:
- `id` (integer): ID de l'année du véhicule

**Middleware**: `PoweredBy`

**Response** (JSON):
```json
{
  "year_id": 1,
  "year": 2024,
  "kit_types": [
    {
      "id": 1,
      "name": "Kit Complet",
      "description": "Kit complet 16 pièces",
      "price": 149.99,
      "parts_count": 16
    },
    {
      "id": 2,
      "name": "Kit Basique",
      "description": "Kit basique 8 pièces",
      "price": 89.99,
      "parts_count": 8
    }
  ]
}
```

---

### Gabarits d'un véhicule/année

```http
GET /vehicles/:id/years/:year_id/year-types
```

Récupère les gabarits/types pour une combinaison véhicule/année.

**Paramètres URL**:
- `id` (integer): ID du véhicule
- `year_id` (integer): ID de l'année

**Middleware**: `PoweredBy`

**Response** (JSON):
```json
{
  "vehicle_id": 42,
  "year_id": 1,
  "types": [
    {
      "id": 1,
      "name": "Standard",
      "template": "yz250f-2024-std"
    },
    {
      "id": 2,
      "name": "Factory Edition",
      "template": "yz250f-2024-fe"
    }
  ]
}
```

---

## 👕 Sportswear

### Détail vêtement

```http
GET /sportswear/:slug-:id
```

Affiche le détail d'un vêtement.

**Paramètres**:
- `slug` (string): Slug du vêtement
- `id` (integer): ID du vêtement

**Exemple**:
```http
GET /sportswear/maillot-mx-custom-42
```

**Middleware**: `PoweredBy`

**Response**: HTML

---

### Ajouter vêtement au panier

```http
POST /sportswear
```

Ajoute un vêtement personnalisé au panier.

**Body** (JSON):
```json
{
  "product_id": 200,
  "size": "L",
  "quantity": 1,
  "customization": {
    "color_primary": "#FF0000",
    "color_secondary": "#000000",
    "text": "TEAM KUTVEK",
    "number": "24"
  }
}
```

**Response** (JSON):
```json
{
  "success": true,
  "cart_item_id": 890
}
```

**Middleware**: `PoweredBy`

---

## 🪑 Saddle Covers (Housses de selle)

### Liste housses de selle

```http
GET /:slug
```

**Paramètres**:
- `slug`: `housses-de-selle` (FR), `seat-covers` (EN), `coprisedili` (IT)

**Middleware**: `PoweredBy`

**Response**: HTML

---

### Filtres housses

```http
GET /:slug/filters
```

Récupère les filtres disponibles (marques, modèles, années).

**Paramètres**:
- `slug`: `housses-de-selle`, `seat-covers`, `coprisedili`

**Middleware**: `PoweredBy`

**Response** (JSON):
```json
{
  "filters": {
    "brands": [
      {"id": 1, "name": "Yamaha", "product_count": 45},
      {"id": 2, "name": "Honda", "product_count": 38}
    ],
    "years": [2024, 2023, 2022, 2021],
    "types": [
      {"id": 1, "name": "Moto", "count": 120},
      {"id": 2, "name": "Quad", "count": 45}
    ]
  }
}
```

---

### Rafraîchir filtres

```http
GET /:slug/refresh-filters
```

Rafraîchit les filtres en fonction des sélections.

**Query params**:
- `brand` (integer, optional): ID de la marque
- `year` (integer, optional): Année
- `type` (integer, optional): Type de véhicule

**Exemple**:
```http
GET /housses-de-selle/refresh-filters?brand=1&year=2024
```

**Middleware**: `PoweredBy`

**Response** (JSON):
```json
{
  "filters": {
    "models": [
      {"id": 10, "name": "YZ250F"},
      {"id": 11, "name": "YZ450F"}
    ]
  },
  "products_count": 8
}
```

---

### Détail housse de selle

```http
GET /:section/:slug-:id
```

**Paramètres**:
- `section`: `housses-de-selle`, `seat-covers`, `coprisedili`
- `slug`: Slug de la housse
- `id`: ID de la housse

**Middleware**: `PoweredBy`

**Response**: HTML

---

### Ajouter housse au panier

```http
POST /:slug
```

**Paramètres**:
- `slug`: `housses-de-selle`, `seat-covers`, `coprisedili`

**Body** (JSON):
```json
{
  "saddle_cover_id": 567,
  "quantity": 1,
  "options": {
    "color": "black",
    "logo": "custom"
  }
}
```

**Response** (JSON):
```json
{
  "success": true,
  "cart_item_id": 901
}
```

**Middleware**: `PoweredBy`

---

### Export housses

```http
GET /:slug/export
```

Exporte le catalogue de housses (CSV/PDF).

**Paramètres**:
- `slug`: `housses-de-selle`, `seat-covers`, `coprisedili`

**Query params**:
- `format` (string): `csv` ou `pdf`

**Middleware**: `PoweredBy`

**Response**: Fichier téléchargeable

---

## 🎨 Assets

### Récupérer un asset

```http
GET /asset/:file
```

Récupère un fichier asset (CSS, JS, images optimisées).

**Paramètres**:
- `file` (string): Chemin du fichier

**Exemple**:
```http
GET /asset/css/main.css
GET /asset/js/cart.js
GET /asset/img/logo.png
```

---

## 📊 Codes de réponse HTTP

| Code | Description |
|------|-------------|
| 200 | OK - Requête réussie |
| 201 | Created - Ressource créée |
| 204 | No Content - Réussite sans contenu |
| 303 | See Other - Redirection |
| 301 | Moved Permanently - Redirection permanente |
| 302 | Found - Redirection temporaire |
| 400 | Bad Request - Requête invalide |
| 401 | Unauthorized - Authentification requise |
| 403 | Forbidden - Accès refusé |
| 404 | Not Found - Ressource non trouvée |
| 410 | Gone - Ressource supprimée définitivement |
| 422 | Unprocessable Entity - Validation échouée |
| 500 | Internal Server Error - Erreur serveur |

---

## 🔐 Headers importants

### Request Headers

```http
Accept: application/json
Content-Type: application/json
Accept-Language: fr-FR,fr;q=0.9,en;q=0.8
Authorization: Bearer {token}
X-Requested-With: XMLHttpRequest
```

### Response Headers

```http
Content-Type: application/json; charset=utf-8
X-Powered-By: Kutvek-Framework
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Cache-Control: no-cache, private
```

---

## 🌐 Multi-langue

Les endpoints supportent la multi-langue via :

1. **Header Accept-Language**:
   ```http
   Accept-Language: fr-FR
   ```

2. **Cookie**:
   ```
   locale=fr
   ```

3. **Query parameter**:
   ```http
   GET /products?lang=en
   ```

Langues supportées : `fr`, `en`, `it`

---

## 💰 Multi-devise

Les prix sont retournés dans la devise de l'utilisateur :

**Cookie**:
```
country_currency={"country":"FR","currency":"EUR"}
```

**Response**:
```json
{
  "price": {
    "amount": 49.99,
    "currency": "EUR",
    "formatted": "49,99 €"
  }
}
```

Devises supportées : `EUR`, `USD`, `GBP`, `CHF`

---

## 🧪 Exemples de requêtes

### cURL

```bash
# GET request
curl -X GET "https://demo.kutvek.com/products/125/gallery" \
  -H "Accept: application/json"

# POST request
curl -X POST "https://demo.kutvek.com/product/add-to-cart" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "product_id": 125,
    "quantity": 1,
    "options": {"color": "red"}
  }'

# PUT request
curl -X PUT "https://demo.kutvek.com/carts/123/items/456/qty" \
  -H "Content-Type: application/json" \
  -d '{"quantity": 3}'

# DELETE request
curl -X DELETE "https://demo.kutvek.com/carts/123/items/456" \
  -H "Accept: application/json"
```

### JavaScript (Fetch)

```javascript
// GET
fetch('https://demo.kutvek.com/products/125/gallery', {
  method: 'GET',
  headers: {
    'Accept': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data));

// POST
fetch('https://demo.kutvek.com/product/add-to-cart', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    product_id: 125,
    quantity: 1,
    options: { color: 'red' }
  })
})
.then(response => response.json())
.then(data => console.log(data));
```

### PHP

```php
// GET
$response = file_get_contents('https://demo.kutvek.com/products/125/gallery');
$data = json_decode($response, true);

// POST
$options = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode([
            'product_id' => 125,
            'quantity' => 1,
            'options' => ['color' => 'red']
        ])
    ]
];
$context = stream_context_create($options);
$response = file_get_contents('https://demo.kutvek.com/product/add-to-cart', false, $context);
$data = json_decode($response, true);
```

---

## ⚠️ Gestion des erreurs

### Format d'erreur JSON

```json
{
  "error": true,
  "message": "Product not found",
  "code": "PRODUCT_NOT_FOUND",
  "status": 404,
  "details": {
    "product_id": 999
  }
}
```

### Codes d'erreur courants

| Code | Message | Status |
|------|---------|--------|
| `PRODUCT_NOT_FOUND` | Produit non trouvé | 404 |
| `CART_NOT_FOUND` | Panier non trouvé | 404 |
| `INVALID_QUANTITY` | Quantité invalide | 400 |
| `INSUFFICIENT_STOCK` | Stock insuffisant | 422 |
| `INVALID_VOUCHER` | Code promo invalide | 422 |
| `PAYMENT_FAILED` | Paiement échoué | 422 |
| `UNAUTHORIZED` | Non autorisé | 401 |

---

## 📈 Rate Limiting

À implémenter :

```
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1634567890
```

---

**Maintenu par**: Équipe Kutvek
**Dernière mise à jour**: Octobre 2024
**Version API**: 1.0
