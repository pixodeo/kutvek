# Guide de Contribution - Kutvek Platform

## 🎯 Bienvenue

Merci de votre intérêt pour contribuer à la plateforme Kutvek ! Ce guide vous aidera à contribuer efficacement au projet.

---

## 📋 Table des matières

1. [Code of Conduct](#code-of-conduct)
2. [Comment contribuer](#comment-contribuer)
3. [Workflow Git](#workflow-git)
4. [Standards de code](#standards-de-code)
5. [Tests](#tests)
6. [Documentation](#documentation)
7. [Pull Requests](#pull-requests)
8. [Code Review](#code-review)

---

## 📜 Code of Conduct

### Nos engagements

- Respecter tous les contributeurs
- Accepter les critiques constructives
- Être professionnel et courtois
- Focus sur ce qui est meilleur pour le projet

### Comportements inacceptables

- Langage offensant ou discriminatoire
- Harcèlement de toute forme
- Publication d'informations privées
- Comportement non professionnel

---

## 🚀 Comment contribuer

### Types de contributions

#### 🐛 Reporter un bug

1. Vérifier que le bug n'existe pas déjà dans les issues
2. Créer une nouvelle issue avec le template "Bug Report"
3. Fournir :
   - Description claire du problème
   - Étapes pour reproduire
   - Comportement attendu vs actuel
   - Captures d'écran si pertinent
   - Environnement (OS, PHP version, etc.)

#### ✨ Proposer une fonctionnalité

1. Créer une issue avec le template "Feature Request"
2. Décrire :
   - Le problème que cela résout
   - La solution proposée
   - Les alternatives considérées
   - Impact sur l'existant

#### 🔧 Corriger un bug

1. Trouver une issue "bug" non assignée
2. Commenter pour indiquer que vous travaillez dessus
3. Créer une branche
4. Implémenter le fix
5. Ajouter des tests
6. Soumettre une Pull Request

#### 📝 Améliorer la documentation

1. Fork le repository
2. Modifier la documentation
3. Vérifier l'orthographe et la grammaire
4. Soumettre une Pull Request

---

## 🌿 Workflow Git

### Configuration initiale

```bash
# Fork le repository sur GitHub
# Puis clone votre fork

git clone https://github.com/VOTRE_USERNAME/kutvek.git
cd kutvek

# Ajouter l'upstream
git remote add upstream https://github.com/kutvek/kutvek.git

# Vérifier
git remote -v
```

### Branches

#### Structure des branches

- `main` - Production, toujours stable
- `develop` - Développement, intégration continue
- `feature/*` - Nouvelles fonctionnalités
- `bugfix/*` - Corrections de bugs
- `hotfix/*` - Corrections urgentes pour production
- `release/*` - Préparation de releases

#### Créer une branche

```bash
# Mettre à jour main
git checkout main
git pull upstream main

# Créer une nouvelle branche
git checkout -b feature/nom-de-la-feature

# Ou pour un bugfix
git checkout -b bugfix/description-du-bug
```

#### Convention de nommage

```
type/description-courte

Types:
- feature/   Nouvelle fonctionnalité
- bugfix/    Correction de bug
- hotfix/    Correction urgente
- refactor/  Refactoring
- docs/      Documentation
- test/      Tests
- chore/     Maintenance

Exemples:
- feature/add-wishlist
- bugfix/cart-total-calculation
- hotfix/payment-stripe-error
- refactor/product-entity
- docs/api-endpoints
```

### Commits

#### Messages de commit

Format :

```
type(scope): description courte

Description détaillée (optionnel)

Fixes #123
```

Types :
- `feat` - Nouvelle fonctionnalité
- `fix` - Correction de bug
- `docs` - Documentation
- `style` - Formatage, indentation
- `refactor` - Refactoring
- `test` - Ajout/modification de tests
- `chore` - Maintenance, config

Exemples :

```bash
# Bonne pratique
git commit -m "feat(cart): add voucher code validation

- Validate voucher code format
- Check expiration date
- Verify minimum order amount

Fixes #142"

# Correction de bug
git commit -m "fix(checkout): resolve payment capture issue

Stripe payment intent was not captured correctly
when user returned from 3D Secure authentication.

Fixes #156"

# Documentation
git commit -m "docs(api): add cart endpoints documentation"
```

#### Règles des commits

- **Atomiques** - Un commit = Une modification logique
- **Descriptifs** - Message clair et concise
- **Testés** - Le code compile et fonctionne
- **Formatés** - Code respecte les standards

### Synchronisation

```bash
# Récupérer les dernières modifications
git fetch upstream

# Rebaser sur main
git rebase upstream/main

# En cas de conflits
git rebase --continue
# ou
git rebase --abort
```

---

## 📝 Standards de code

### PHP

#### Style de code : PSR-12

```php
<?php
declare(strict_types=1);

namespace App\Product;

use App\AppAction;
use Domain\Table\Product;

final class Read extends AppAction
{
    private Product $_table;

    public function __invoke(): void
    {
        // Code ici
    }

    private function validateId(int $id): bool
    {
        return $id > 0;
    }
}
```

#### Règles importantes

✅ **À FAIRE** :

```php
// Typage strict
declare(strict_types=1);

// Types de retour
public function getProduct(int $id): ?Product

// Final classes pour les Actions
final class Read extends AppAction

// Constantes en UPPER_SNAKE_CASE
private const MAX_ITEMS = 50;

// Propriétés privées avec underscore
private Product $_table;

// DocBlocks complets
/**
 * Récupère un produit par son ID
 *
 * @param int $id L'identifiant du produit
 * @return Product|null
 * @throws ProductNotFoundException
 */
```

❌ **À ÉVITER** :

```php
// Pas de typage
public function getProduct($id)

// Variables globales
global $db;

// Requêtes SQL non préparées
$query = "SELECT * FROM products WHERE id = {$id}";

// Magic numbers
if (count($items) > 50)

// Noms de variables peu clairs
$p = new Product();
$x = 123;
```

### JavaScript

```javascript
// Utiliser const/let, pas var
const productId = 123;
let quantity = 1;

// Arrow functions
const addToCart = (productId, qty) => {
    // Code
};

// Destructuring
const {id, title, price} = product;

// Template literals
const message = `Produit ${title} ajouté`;

// Async/await
const fetchProduct = async (id) => {
    const response = await fetch(`/products/${id}`);
    return await response.json();
};
```

### CSS

```css
/* BEM Naming */
.product-card { }
.product-card__title { }
.product-card__price { }
.product-card--featured { }

/* Mobile-first */
.container {
    width: 100%;
}

@media (min-width: 768px) {
    .container {
        width: 750px;
    }
}

/* Variables CSS */
:root {
    --primary-color: #e74c3c;
    --spacing-unit: 8px;
}

.button {
    background: var(--primary-color);
    padding: calc(var(--spacing-unit) * 2);
}
```

### SQL

```sql
-- Noms en snake_case
CREATE TABLE product_categories (
    id INT PRIMARY KEY,
    product_id INT NOT NULL,
    category_id INT NOT NULL
);

-- Indices explicites
CREATE INDEX idx_product_id ON product_categories(product_id);

-- Requêtes formatées
SELECT
    p.id,
    p.title,
    c.name AS category_name
FROM products p
INNER JOIN categories c ON p.category_id = c.id
WHERE p.active = 1
ORDER BY p.created_at DESC;
```

---

## 🧪 Tests

### Tests unitaires

Créer des tests pour :
- Logique métier
- Méthodes complexes
- Calculs critiques

```php
<?php
// tests/Unit/Entity/ProductTest.php

use PHPUnit\Framework\TestCase;
use Domain\Entity\Product;

class ProductTest extends TestCase
{
    public function testCalculateFinalPriceWithVAT(): void
    {
        $product = new Product();
        $product->base_price = 100.00;
        $product->tax_rate = 20.00;

        $finalPrice = $product->calculateFinalPrice();

        $this->assertEquals(120.00, $finalPrice);
    }

    public function testIsAvailableReturnsTrueWhenInStock(): void
    {
        $product = new Product();
        $product->stock = 10;
        $product->active = 1;

        $this->assertTrue($product->isAvailable());
    }
}
```

### Tests d'intégration

```php
<?php
// tests/Integration/CartTest.php

class CartTest extends TestCase
{
    public function testAddProductToCart(): void
    {
        $cart = new Cart();
        $product = $this->createTestProduct();

        $cart->addItem($product, 2);

        $this->assertCount(1, $cart->items);
        $this->assertEquals(2, $cart->items[0]->quantity);
    }
}
```

### Lancer les tests

```bash
# Tous les tests
./vendor/bin/phpunit

# Un fichier spécifique
./vendor/bin/phpunit tests/Unit/Entity/ProductTest.php

# Avec coverage
./vendor/bin/phpunit --coverage-html coverage/
```

### Couverture de tests

Objectif : **> 80% de couverture**

---

## 📚 Documentation

### Code

```php
/**
 * Récupère un produit par son ID
 *
 * Cette méthode charge un produit avec ses traductions
 * et ses relations (catégorie, images, etc.).
 *
 * @param int $id L'identifiant unique du produit
 * @return Product|null Le produit trouvé ou null
 * @throws ProductNotFoundException Si le produit n'existe pas
 * @throws DatabaseException En cas d'erreur base de données
 *
 * @example
 * $product = $productTable->read(123);
 * if ($product) {
 *     echo $product->title;
 * }
 */
public function read(int $id): ?Product
{
    // Implementation
}
```

### Markdown

```markdown
# Titre principal

## Section

Description claire et concise.

### Sous-section

- Point 1
- Point 2

**Gras** pour l'emphase
`code` pour les commandes

Exemples de code avec syntaxe highlighting
```

### README des modules

Chaque module devrait avoir un README :

```markdown
# Module Product

## Description
Gestion des produits de la plateforme.

## Actions
- `Read` - Affiche un produit
- `Gallery` - Galerie d'images
- `AddToCart` - Ajout au panier

## Utilisation
Voir [MODULES.md](../MODULES.md#module-product)
```

---

## 🔍 Pull Requests

### Avant de créer une PR

- [ ] Code compilé et testé
- [ ] Tests passent (unitaires + intégration)
- [ ] Pas de conflits avec `main`
- [ ] Code formaté selon standards
- [ ] Documentation mise à jour
- [ ] Changements committed et pushed

### Créer une PR

1. **Push votre branche**

```bash
git push origin feature/ma-feature
```

2. **Ouvrir la PR sur GitHub**

- Aller sur le repository
- Cliquer "New Pull Request"
- Sélectionner votre branche
- Remplir le template

3. **Template de PR**

```markdown
## Description
Brève description des changements

## Type de changement
- [ ] Bug fix
- [ ] Nouvelle fonctionnalité
- [ ] Breaking change
- [ ] Documentation

## Comment tester
1. Étape 1
2. Étape 2
3. Résultat attendu

## Checklist
- [ ] Mon code suit les standards du projet
- [ ] J'ai commenté le code complexe
- [ ] J'ai mis à jour la documentation
- [ ] Mes changements ne génèrent pas de warnings
- [ ] J'ai ajouté des tests
- [ ] Tous les tests passent
- [ ] Pas de conflits avec main

## Screenshots (si applicable)
![description](url)

## Issues liées
Fixes #123
Related to #456
```

### Taille des PRs

- **Petites PRs** = Review plus rapide
- **< 400 lignes** idéalement
- Si > 400 lignes, découper en plusieurs PRs

---

## 👀 Code Review

### En tant qu'auteur

1. **Faciliter la review**
   - Description claire
   - Contexte fourni
   - Tests inclus
   - Screenshots si UI

2. **Répondre aux commentaires**
   - Promptement
   - De manière constructive
   - Expliquer vos choix
   - Être ouvert aux suggestions

3. **Appliquer les changements**
   - Faire les modifications demandées
   - Commit et push
   - Répondre aux commentaires

### En tant que reviewer

1. **Vérifier**
   - [ ] Logique correcte
   - [ ] Standards respectés
   - [ ] Tests adéquats
   - [ ] Performance
   - [ ] Sécurité
   - [ ] Documentation

2. **Commenter**
   - Être constructif et poli
   - Expliquer le "pourquoi"
   - Proposer des solutions
   - Pointer le code spécifique

3. **Types de commentaires**

```markdown
❌ "Ce code est mauvais"

✅ "Cette approche pourrait causer des problèmes de performance
   avec de grandes collections. Considérez utiliser un generateur:

   ```php
   foreach ($this->getItemsGenerator() as $item) {
       // Process
   }
   ```"
```

4. **Approuver ou demander des changements**
   - **Approve** si tout est bon
   - **Request changes** si modifications nécessaires
   - **Comment** pour questions/suggestions

---

## 🏷 Labels GitHub

### Types

- `bug` - Quelque chose ne fonctionne pas
- `enhancement` - Nouvelle fonctionnalité
- `documentation` - Amélioration docs
- `performance` - Optimisation
- `security` - Problème de sécurité
- `refactor` - Refactoring code

### Priorités

- `priority: critical` - À traiter immédiatement
- `priority: high` - Important
- `priority: medium` - Moyen terme
- `priority: low` - Quand possible

### Statuts

- `status: in progress` - En cours
- `status: on hold` - En attente
- `status: needs review` - À reviewer
- `status: needs testing` - À tester

### Modules

- `module: cart`
- `module: checkout`
- `module: product`
- etc.

---

## 🎓 Ressources

### Documentation du projet

- [README.md](README.md) - Vue d'ensemble
- [ARCHITECTURE.md](ARCHITECTURE.md) - Architecture
- [DEVELOPER_GUIDE.md](DEVELOPER_GUIDE.md) - Guide développeur
- [API_REFERENCE.md](API_REFERENCE.md) - Référence API

### Standards

- [PSR-12](https://www.php-fig.org/psr/psr-12/) - Style de code PHP
- [PSR-7](https://www.php-fig.org/psr/psr-7/) - HTTP Messages
- [PSR-15](https://www.php-fig.org/psr/psr-15/) - HTTP Handlers

### Outils

- [PHPStan](https://phpstan.org/) - Analyse statique
- [PHP CS Fixer](https://cs.symfony.com/) - Formatage code
- [PHPUnit](https://phpunit.de/) - Tests

---

## 📞 Contact

### Questions ?

- **Discord** : [Lien serveur]
- **Email** : dev@kutvek.com
- **Issues** : [GitHub Issues](https://github.com/kutvek/kutvek/issues)

### Équipe

- **Lead Developer** : [@username]
- **Reviewers** : [@user1], [@user2]
- **Maintainers** : [@user3], [@user4]

---

## 🙏 Remerciements

Merci à tous les contributeurs qui rendent ce projet possible !

### Top Contributors

- [@contributor1] - 150 commits
- [@contributor2] - 120 commits
- [@contributor3] - 95 commits

---

## ⚖️ License

En contribuant à ce projet, vous acceptez que vos contributions soient sous la même licence que le projet.

---

**Dernière mise à jour**: Octobre 2024
**Version**: 1.0.0
