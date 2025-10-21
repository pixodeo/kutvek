# Guide de Déploiement - Kutvek Platform

## 📋 Vue d'ensemble

Ce guide couvre le déploiement de la plateforme Kutvek en environnement de production.

---

## 🎯 Prérequis

### Système

- **OS**: Ubuntu 20.04+ / Debian 11+ / CentOS 8+
- **CPU**: 2+ cores
- **RAM**: 4GB minimum, 8GB recommandé
- **Storage**: 50GB minimum SSD

### Logiciels

- **PHP**: 8.1+
- **MySQL**: 8.0+
- **Apache**: 2.4+ (avec mod_rewrite) ou Nginx 1.18+
- **Git**: 2.30+
- **Composer**: 2.0+ (optionnel)

### Services tiers

- **Serveur SMTP** - Pour envoi d'emails
- **Stripe/PayPal** - Pour paiements
- **CDN** (optionnel) - Pour assets statiques
- **SSL Certificate** - Let's Encrypt ou commercial

---

## 🔧 Configuration serveur

### Apache

#### Installation

```bash
sudo apt update
sudo apt install apache2 php8.1 php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-json php8.1-intl
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl restart apache2
```

#### Virtual Host

`/etc/apache2/sites-available/kutvek.conf` :

```apache
<VirtualHost *:80>
    ServerName demo.kutvek-kitgraphik.com
    ServerAlias www.demo.kutvek-kitgraphik.com

    DocumentRoot /var/www/kutvek/webroot

    <Directory /var/www/kutvek/webroot>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted

        # Réécriture d'URL
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^ index.php [QSA,L]
    </Directory>

    # Logs
    ErrorLog ${APACHE_LOG_DIR}/kutvek_error.log
    CustomLog ${APACHE_LOG_DIR}/kutvek_access.log combined

    # Sécurité
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>

# Redirection HTTP → HTTPS
<VirtualHost *:443>
    ServerName demo.kutvek-kitgraphik.com
    ServerAlias www.demo.kutvek-kitgraphik.com

    DocumentRoot /var/www/kutvek/webroot

    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/demo.kutvek-kitgraphik.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/demo.kutvek-kitgraphik.com/privkey.pem

    <Directory /var/www/kutvek/webroot>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Compression
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
    </IfModule>

    # Cache statique
    <IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType image/jpg "access plus 1 year"
        ExpiresByType image/jpeg "access plus 1 year"
        ExpiresByType image/png "access plus 1 year"
        ExpiresByType image/webp "access plus 1 year"
        ExpiresByType text/css "access plus 1 month"
        ExpiresByType application/javascript "access plus 1 month"
    </IfModule>

    ErrorLog ${APACHE_LOG_DIR}/kutvek_ssl_error.log
    CustomLog ${APACHE_LOG_DIR}/kutvek_ssl_access.log combined
</VirtualHost>
```

#### Activation

```bash
sudo a2ensite kutvek.conf
sudo a2enmod ssl
sudo systemctl reload apache2
```

---

### Nginx (Alternative)

#### Installation

```bash
sudo apt update
sudo apt install nginx php8.1-fpm
sudo systemctl start nginx
sudo systemctl enable nginx
```

#### Configuration

`/etc/nginx/sites-available/kutvek` :

```nginx
server {
    listen 80;
    server_name demo.kutvek-kitgraphik.com www.demo.kutvek-kitgraphik.com;

    # Redirection HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name demo.kutvek-kitgraphik.com www.demo.kutvek-kitgraphik.com;

    root /var/www/kutvek/webroot;
    index index.php;

    # SSL
    ssl_certificate /etc/letsencrypt/live/demo.kutvek-kitgraphik.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/demo.kutvek-kitgraphik.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Logs
    access_log /var/log/nginx/kutvek_access.log;
    error_log /var/log/nginx/kutvek_error.log;

    # Gzip
    gzip on;
    gzip_vary on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    # Sécurité
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # PHP
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Cache statique
    location ~* \.(jpg|jpeg|png|gif|webp|css|js|woff2?|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Blocage fichiers sensibles
    location ~ /\. {
        deny all;
    }

    location ~ /Config/ {
        deny all;
    }
}
```

#### Activation

```bash
sudo ln -s /etc/nginx/sites-available/kutvek /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 🗄 MySQL

### Installation

```bash
sudo apt install mysql-server
sudo mysql_secure_installation
```

### Création de la base

```sql
CREATE DATABASE app_kutvek CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'kutvek_user'@'localhost' IDENTIFIED BY 'mot_de_passe_fort';
GRANT ALL PRIVILEGES ON app_kutvek.* TO 'kutvek_user'@'localhost';
FLUSH PRIVILEGES;
```

### Configuration optimisée

`/etc/mysql/mysql.conf.d/mysqld.cnf` :

```ini
[mysqld]
# Performance
innodb_buffer_pool_size = 2G
innodb_log_file_size = 512M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT

# Connexions
max_connections = 200
max_allowed_packet = 64M

# Cache
query_cache_type = 1
query_cache_size = 128M

# Logs
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow-query.log
long_query_time = 2

# Caractères
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
```

Redémarrer :

```bash
sudo systemctl restart mysql
```

---

## 📦 Déploiement de l'application

### 1. Cloner le repository

```bash
cd /var/www
sudo git clone https://github.com/votre-org/kutvek.git
cd kutvek
```

### 2. Configurer les permissions

```bash
# Propriétaire
sudo chown -R www-data:www-data /var/www/kutvek

# Permissions
sudo find /var/www/kutvek -type d -exec chmod 755 {} \;
sudo find /var/www/kutvek -type f -exec chmod 644 {} \;

# Dossiers en écriture
sudo chmod -R 777 /var/www/kutvek/webroot/cache
sudo chmod -R 777 /var/www/kutvek/webroot/files
sudo chmod -R 777 /var/www/kutvek/webroot/orders
```

### 3. Configuration base de données

```bash
cd Config
sudo cp DbConf.php.example DbConf.php
sudo nano DbConf.php
```

`Config/DbConf.php` :

```php
<?php
return array(
    "db_user" => "kutvek_user",
    "db_pass" => "mot_de_passe_fort",
    "db_host" => "localhost",
    "db_port" => 3306,
    "db_name" => "app_kutvek",
    "charset" => "utf8mb4"
);
```

### 4. Importer le schéma

```bash
mysql -u kutvek_user -p app_kutvek < database/schema.sql
```

### 5. Configuration de l'application

Éditer `webroot/index.php` :

```php
<?php
// Mode production
error_reporting(E_ERROR);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/kutvek/error.log');

// URLs
define('URL_SITE', 'https://demo.kutvek-kitgraphik.com/');
define('DOMAIN', 'https://demo.kutvek-kitgraphik.com');
define('FQDN', 'https://demo.kutvek-kitgraphik.com');

// Configuration
define('WORKSPACE', 2);
define('WEBSITE_ID', 5);
```

### 6. SSL avec Let's Encrypt

```bash
sudo apt install certbot python3-certbot-apache

# Pour Apache
sudo certbot --apache -d demo.kutvek-kitgraphik.com -d www.demo.kutvek-kitgraphik.com

# Pour Nginx
sudo certbot --nginx -d demo.kutvek-kitgraphik.com -d www.demo.kutvek-kitgraphik.com

# Renouvellement automatique
sudo certbot renew --dry-run
```

### 7. Créer les logs

```bash
sudo mkdir -p /var/log/kutvek
sudo chown www-data:www-data /var/log/kutvek
sudo touch /var/log/kutvek/error.log
sudo touch /var/log/kutvek/access.log
```

---

## 🔐 Sécurité

### Firewall

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### Fail2Ban

```bash
sudo apt install fail2ban
sudo systemctl enable fail2ban
```

`/etc/fail2ban/jail.local` :

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true

[apache]
enabled = true
port = http,https
logpath = /var/log/apache2/kutvek_error.log
```

### Permissions sensibles

```bash
# Protéger les fichiers de config
sudo chmod 600 /var/www/kutvek/Config/*.php
sudo chown root:www-data /var/www/kutvek/Config/*.php

# Désactiver listing directories
# Déjà fait dans la config Apache/Nginx
```

---

## 🚀 Optimisations

### PHP

`/etc/php/8.1/apache2/php.ini` (ou `/etc/php/8.1/fpm/php.ini` pour Nginx) :

```ini
; Performance
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1

; Uploads
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
memory_limit = 256M

; Production
display_errors = Off
display_startup_errors = Off
error_reporting = E_ERROR
log_errors = On
error_log = /var/log/kutvek/php_errors.log

; Sécurité
expose_php = Off
allow_url_fopen = Off
disable_functions = exec,passthru,shell_exec,system,proc_open,popen
```

Redémarrer PHP :

```bash
# Apache
sudo systemctl restart apache2

# Nginx
sudo systemctl restart php8.1-fpm
```

### Cache navigateur

Déjà configuré dans Apache/Nginx (voir sections ci-dessus).

### CDN (Optionnel)

Utiliser Cloudflare ou similaire pour :
- Cache global
- Protection DDoS
- SSL gratuit
- Optimisation images

Configuration Cloudflare :
1. Créer un compte
2. Ajouter le domaine
3. Changer les nameservers
4. Activer :
   - Auto Minify (CSS, JS, HTML)
   - Brotli compression
   - Rocket Loader
   - Polish (optimisation images)

---

## 📊 Monitoring

### Logs

```bash
# Logs Apache
tail -f /var/log/apache2/kutvek_access.log
tail -f /var/log/apache2/kutvek_error.log

# Logs Nginx
tail -f /var/log/nginx/kutvek_access.log
tail -f /var/log/nginx/kutvek_error.log

# Logs application
tail -f /var/log/kutvek/error.log

# Logs MySQL
tail -f /var/log/mysql/error.log
tail -f /var/log/mysql/slow-query.log
```

### Rotation des logs

`/etc/logrotate.d/kutvek` :

```
/var/log/kutvek/*.log {
    daily
    rotate 30
    compress
    delaycompress
    notifempty
    missingok
    sharedscripts
    postrotate
        systemctl reload apache2 > /dev/null 2>&1 || true
    endscript
}
```

### Monitoring avec Uptime Robot

1. Créer un compte sur [UptimeRobot.com](https://uptimerobot.com)
2. Ajouter un monitor HTTP(s)
3. URL : `https://demo.kutvek-kitgraphik.com`
4. Intervalle : 5 minutes
5. Notifications : Email/SMS

---

## 🔄 Mises à jour

### Processus de mise à jour

```bash
#!/bin/bash
# /var/www/kutvek/deploy.sh

set -e

echo "🚀 Démarrage du déploiement..."

# 1. Backup
echo "📦 Backup de la base de données..."
mysqldump -u kutvek_user -p app_kutvek > /backups/kutvek_$(date +%Y%m%d_%H%M%S).sql

# 2. Git pull
echo "📥 Récupération du code..."
cd /var/www/kutvek
git fetch origin
git pull origin main

# 3. Permissions
echo "🔐 Ajustement des permissions..."
sudo chown -R www-data:www-data /var/www/kutvek
sudo find /var/www/kutvek -type d -exec chmod 755 {} \;
sudo find /var/www/kutvek -type f -exec chmod 644 {} \;
sudo chmod -R 777 /var/www/kutvek/webroot/cache
sudo chmod -R 777 /var/www/kutvek/webroot/files

# 4. Migrations (si nécessaire)
# mysql -u kutvek_user -p app_kutvek < database/migrations/latest.sql

# 5. Clear cache
echo "🧹 Nettoyage du cache..."
rm -rf /var/www/kutvek/webroot/cache/*

# 6. Restart services
echo "♻️ Redémarrage des services..."
sudo systemctl restart apache2
# ou pour Nginx:
# sudo systemctl restart php8.1-fpm
# sudo systemctl reload nginx

echo "✅ Déploiement terminé!"
```

Rendre exécutable :

```bash
chmod +x /var/www/kutvek/deploy.sh
```

---

## 💾 Backups

### Script de backup

```bash
#!/bin/bash
# /usr/local/bin/backup-kutvek.sh

BACKUP_DIR="/backups/kutvek"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=30

# Créer le dossier
mkdir -p $BACKUP_DIR

# Backup DB
mysqldump -u kutvek_user -p'mot_de_passe' app_kutvek | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup fichiers
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/kutvek/webroot/files /var/www/kutvek/webroot/orders

# Supprimer anciens backups
find $BACKUP_DIR -type f -mtime +$RETENTION_DAYS -delete

echo "Backup terminé : $DATE"
```

### Cron automatique

```bash
sudo crontab -e
```

Ajouter :

```cron
# Backup quotidien à 2h du matin
0 2 * * * /usr/local/bin/backup-kutvek.sh >> /var/log/kutvek/backup.log 2>&1
```

---

## 🌍 Multi-environnements

### Configuration par environnement

Créer des fichiers de config :

- `Config/DbConf.production.php`
- `Config/DbConf.staging.php`
- `Config/DbConf.development.php`

Utiliser une variable d'environnement :

```bash
export APP_ENV=production
```

Dans `webroot/index.php` :

```php
$env = getenv('APP_ENV') ?: 'production';
$dbConfig = "DbConf.{$env}.php";
```

---

## 📈 Performance en production

### Benchmarks attendus

- **Time To First Byte (TTFB)**: < 200ms
- **Page Load Time**: < 2s
- **Concurrent Users**: 100+
- **Requests/second**: 50+

### Outils de test

```bash
# Apache Bench
ab -n 1000 -c 100 https://demo.kutvek-kitgraphik.com/

# Siege
siege -c 50 -t 30s https://demo.kutvek-kitgraphik.com/
```

---

## ✅ Checklist pré-déploiement

- [ ] Serveur configuré (Apache/Nginx + PHP + MySQL)
- [ ] SSL installé et fonctionnel
- [ ] Base de données créée et importée
- [ ] Configuration DB renseignée
- [ ] Permissions fichiers correctes
- [ ] Logs configurés
- [ ] Firewall activé
- [ ] Fail2Ban configuré
- [ ] Backups automatiques programmés
- [ ] Monitoring activé
- [ ] DNS configurés
- [ ] Tests de charge effectués
- [ ] Documentation à jour

---

## 🆘 Troubleshooting

### Erreur 500

```bash
# Vérifier les logs
tail -f /var/log/apache2/kutvek_error.log
tail -f /var/log/kutvek/error.log

# Vérifier permissions
ls -la /var/www/kutvek/webroot
```

### Base de données inaccessible

```bash
# Tester connexion
mysql -u kutvek_user -p -h localhost app_kutvek

# Vérifier service MySQL
sudo systemctl status mysql
```

### Site lent

```bash
# Vérifier CPU/RAM
top
htop

# Requêtes lentes MySQL
mysql -u root -p -e "SELECT * FROM information_schema.processlist WHERE time > 2;"

# Logs slow queries
tail -f /var/log/mysql/slow-query.log
```

---

## 📞 Support

En cas de problème en production :

1. Consulter les logs
2. Vérifier le monitoring
3. Contacter l'équipe DevOps
4. Email : devops@kutvek.com

---

**Maintenu par**: Équipe DevOps Kutvek
**Dernière mise à jour**: Octobre 2024
