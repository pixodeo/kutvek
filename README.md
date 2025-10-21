# Kutvek-KitGraphik Platform

> Plateforme e-commerce spécialisée dans les kits graphiques personnalisés pour véhicules, housses de selle et vêtements de sport.

## 📋 Vue d'ensemble

Kutvek-KitGraphik est une plateforme e-commerce B2C/B2B développée avec un framework PHP MVC personnalisé. La plateforme permet la vente de produits graphiques personnalisables pour différents types de véhicules, ainsi que des housses de selle et des vêtements de sport.

### Caractéristiques principales

- 🛒 **Système de panier avancé** avec gestion des variantes et options
- 🌍 **Multi-langue** (Français, Anglais, Italien)
- 💰 **Multi-devise** avec gestion automatique des taux de change
- 🎨 **Personnalisation produits** avec prévisualisation en temps réel
- 🚗 **Catalogue véhicules** avec recherche par marque/modèle/année
- 📦 **Gestion des commandes** avec suivi et export
- 🔐 **Authentification** et gestion des comptes clients
- 📊 **Tableaux de bord** analytics et reporting

## 🛠 Stack Technique

### Backend
- **PHP 8.1+** avec typage strict
- **Architecture ADR** (Action-Domain-Responder)
- **PSR-7/PSR-15** pour HTTP messages et middleware
- **MySQL 8.0+** avec support multi-bases
- **Custom MVC Framework** basé sur les standards modernes

### Frontend
- **HTML5/CSS3** avec layouts responsifs
- **JavaScript vanilla** pour les interactions
- **AJAX** pour les appels API asynchrones

### Librairies tierces
- **PHPMailer** - Envoi d'emails
- **Stripe** - Paiements en ligne
- **NumberFormatter** - Formatage des devises

## 📁 Structure du Projet

```
kutvek/
├── App/                    # Modules applicatifs (Cart, Product, Checkout, etc.)
├── Core/                   # Framework MVC core
│   ├── Action.php          # Classe de base pour les Actions
│   ├── Controller/         # Contrôleurs de base
│   ├── Model/              # Modèles de base
│   ├── Routing/            # Système de routing
│   ├── Http/               # PSR-7 HTTP messages
│   ├── Database/           # Couche d'abstraction base de données
│   ├── Library/            # Utilitaires et helpers
│   └── View/               # Moteur de templates
├── Domain/                 # Domain objects (Entities, Tables)
│   ├── Entity/             # Entités métier
│   └── Table/              # Couche d'accès données
├── Middleware/             # Middleware HTTP
├── View/                   # Templates et layouts
│   ├── Layout/             # Layouts de base
│   ├── Partials/           # Composants réutilisables
│   └── [Modules]/          # Vues par module
├── Component/              # Composants réutilisables
├── Library/                # Librairies custom
├── Config/                 # Configuration (DB, environnements)
├── webroot/                # Point d'entrée public
│   ├── index.php           # Bootstrap application
│   ├── css/                # Feuilles de style
│   ├── js/                 # Scripts JavaScript
│   ├── img/                # Images
│   └── files/              # Fichiers uploadés
├── Vendor/                 # Dépendances tierces
└── Psr/                    # Interfaces PSR
```

## 🚀 Installation

### Prérequis

- PHP >= 8.1
- MySQL >= 8.0
- Apache/Nginx avec mod_rewrite
- Composer (optionnel, pour les dépendances)
- Git

### Étapes d'installation

1. **Cloner le repository**
   ```bash
   git clone https://github.com/votre-org/kutvek.git
   cd kutvek
   ```

2. **Configurer la base de données**

   Copier le fichier de configuration :
   ```bash
   cp Config/DbConf.php.example Config/DbConf.php
   ```

   Éditer `Config/DbConf.php` avec vos paramètres :
   ```php
   return array(
       "db_user" => "votre_user",
       "db_pass" => "votre_password",
       "db_host" => "localhost",
       "db_port" => 3306,
       "db_name" => "kutvek_db"
   );
   ```

3. **Importer le schéma de base de données**
   ```bash
   mysql -u root -p kutvek_db < database/schema.sql
   ```

4. **Configurer les permissions**
   ```bash
   chmod -R 755 webroot/
   chmod -R 777 webroot/cache/
   chmod -R 777 webroot/files/
   chmod -R 777 webroot/orders/
   ```

5. **Configurer le virtual host**

   Apache (`/etc/apache2/sites-available/kutvek.conf`) :
   ```apache
   <VirtualHost *:80>
       ServerName kutvek.local
       DocumentRoot /path/to/kutvek/webroot

       <Directory /path/to/kutvek/webroot>
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>

       ErrorLog ${APACHE_LOG_DIR}/kutvek_error.log
       CustomLog ${APACHE_LOG_DIR}/kutvek_access.log combined
   </VirtualHost>
   ```

6. **Ajouter au fichier hosts**
   ```bash
   echo "127.0.0.1 kutvek.local" | sudo tee -a /etc/hosts
   ```

7. **Redémarrer le serveur web**
   ```bash
   sudo service apache2 restart
   ```

8. **Accéder à l'application**

   Ouvrir dans le navigateur : `http://kutvek.local`

## 🎯 Démarrage Rapide

### Exemple : Créer un nouveau module

1. **Créer la structure de dossiers**
   ```bash
   mkdir -p App/MonModule
   mkdir -p App/MonModule/View
   mkdir -p View/MonModule
   ```

2. **Créer une Action** (`App/MonModule/Index.php`)
   ```php
   <?php
   declare(strict_types=1);
   namespace App\MonModule;

   use App\AppAction;

   final class Index extends AppAction {
       public function __invoke() {
           $data = ['message' => 'Hello from MonModule'];
           $this->render('MonModule/index', $data);
       }
   }
   ```

3. **Ajouter la route** (dans `webroot/index.php`)
   ```php
   $router->get('/mon-module', 'monModule.index')
       ->middleware('PoweredBy');
   ```

4. **Créer la vue** (`View/MonModule/index.php`)
   ```php
   <h1><?= $message ?></h1>
   ```

### Exemple : Requête en base de données

```php
// Dans votre Action
public function __invoke() {
    $productTable = new \Domain\Table\Product($this->_setDb());
    $products = $productTable->findAll();

    $this->render('Product/list', ['products' => $products]);
}
```

## 📚 Documentation

- [Architecture](ARCHITECTURE.md) - Architecture technique détaillée
- [Guide Développeur](DEVELOPER_GUIDE.md) - Guide de développement
- [Base de données](DATABASE.md) - Structure et utilisation de la BDD
- [API Reference](API_REFERENCE.md) - Documentation des endpoints
- [Modules](MODULES.md) - Documentation des modules
- [Déploiement](DEPLOYMENT.md) - Guide de déploiement
- [Contribution](CONTRIBUTING.md) - Guide de contribution

## 🔧 Configuration

### Variables d'environnement (webroot/index.php)

```php
define('URL_SITE', 'https://votre-domaine.com/');
define('DOMAIN', 'https://votre-domaine.com');
define('WORKSPACE', 2);        // ID de l'espace de travail
define('WEBSITE_ID', 5);       // ID du site web
```

### Configurations multiples bases de données

Le système supporte plusieurs connexions DB :
- `DbConf.php` - Base principale
- `DbConfAmerika.php` - Base Amerika
- `DbConfKitGraphik.php` - Base KitGraphik
- `DbConfAppKutvek.php` - Base App Kutvek

## 🧪 Tests

```bash
# Lancer les tests unitaires
php vendor/bin/phpunit tests/

# Lancer un test spécifique
php vendor/bin/phpunit tests/Unit/ProductTest.php
```

## 📊 Monitoring

- Logs d'erreurs : `webroot/logs/error.log`
- Logs d'accès : Apache logs
- Cache : `webroot/cache/`

## 🚀 Déploiement

Voir le [Guide de Déploiement](DEPLOYMENT.md) pour les instructions complètes.

## 🤝 Contribution

Les contributions sont les bienvenues ! Consultez [CONTRIBUTING.md](CONTRIBUTING.md) pour les guidelines.

## 📄 License

Propriétaire - Kutvek-KitGraphik © 2024

## 👥 Équipe

- **Development Team** - Kutvek
- **Contact** - dev@kutvek.com

## 🔗 Liens Utiles

- [Site Production](https://demo.kutvek-kitgraphik.com/)
- [API Dev](https://dev.kutvek.com)
- Documentation interne
- Support technique

---

**Version actuelle**: 2.0.0
**Dernière mise à jour**: Octobre 2024
