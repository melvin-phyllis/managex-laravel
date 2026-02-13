# Déploiement de ManageX sur Namecheap (Shared Hosting)

Ce guide explique comment déployer le projet Laravel ManageX sur un hébergement mutualisé Namecheap avec cPanel.

## Prérequis

- Un compte Namecheap avec un plan d'hébergement (Stellar, Stellar Plus, ou Stellar Business)
- PHP 8.1+ activé sur votre hébergement
- Accès à cPanel
- Un client FTP (FileZilla recommandé) ou utilisation du File Manager de cPanel

---

## Étape 1 : Préparer le projet en local

### 1.1 Optimiser pour la production

```bash
# Dans le dossier du projet
composer install --optimize-autoloader --no-dev

# Compiler les assets
npm run build

# Nettoyer les caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 1.2 Créer l'archive du projet

Compresser tout le projet en ZIP (exclure `node_modules` et `.git`) :

**Windows (PowerShell) :**
```powershell
# Créer une archive sans node_modules et .git
Compress-Archive -Path * -DestinationPath ManageX.zip -Force
```

**Ou manuellement :** Sélectionnez tous les fichiers SAUF `node_modules/` et `.git/`, puis créez un ZIP.

---

## Étape 2 : Configurer la base de données sur cPanel

### 2.1 Créer la base de données

1. Connectez-vous à **cPanel** (https://votre-domaine.com:2083 ou via Namecheap Dashboard)
2. Allez dans **MySQL Databases**
3. Créez une nouvelle base de données :
   - Nom : `votrecompte_managex` (le préfixe est automatique)
4. Créez un utilisateur MySQL :
   - Nom d'utilisateur : `votrecompte_managex`
   - Mot de passe : *générez un mot de passe fort*
5. Associez l'utilisateur à la base de données avec **ALL PRIVILEGES**

### 2.2 Noter les informations

```
DB_DATABASE=votrecompte_managex
DB_USERNAME=votrecompte_managex
DB_PASSWORD=votre_mot_de_passe
DB_HOST=localhost
```

---

## Étape 3 : Uploader les fichiers

### Option A : Via File Manager (cPanel)

1. Dans cPanel, ouvrez **File Manager**
2. Naviguez vers le dossier racine (généralement `/home/votrecompte/`)
3. Créez un dossier `managex` (en dehors de `public_html`)
4. Uploadez le ZIP dans ce dossier
5. Extrayez le ZIP

### Option B : Via FTP (FileZilla)

1. Connectez-vous avec vos identifiants FTP :
   - Hôte : `ftp.votre-domaine.com`
   - Utilisateur : votre nom d'utilisateur cPanel
   - Mot de passe : votre mot de passe cPanel
   - Port : 21

2. Uploadez les fichiers dans `/home/votrecompte/managex/`

### Structure recommandée

```
/home/votrecompte/
├── managex/                 # ← Tout le projet Laravel (hors public)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── ...
│
└── public_html/             # ← Contenu du dossier public/
    ├── index.php (modifié)
    ├── build/
    ├── storage/ (lien symbolique)
    └── ...
```

---

## Étape 4 : Configurer le dossier public

### 4.1 Déplacer le contenu de `public/` vers `public_html/`

1. Copiez TOUT le contenu du dossier `managex/public/` vers `public_html/`
2. **Important :** Ne supprimez PAS le dossier `public/` original

### 4.2 Modifier `index.php`

Éditez `/home/votrecompte/public_html/index.php` :

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Vérification du mode maintenance
if (file_exists($maintenance = __DIR__.'/../managex/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader Composer - CHEMIN MODIFIÉ
require __DIR__.'/../managex/vendor/autoload.php';

// Bootstrap Laravel - CHEMIN MODIFIÉ
$app = require_once __DIR__.'/../managex/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

### 4.3 Créer le lien symbolique pour storage

Via **Terminal** dans cPanel (ou SSH si disponible) :

```bash
cd ~/public_html
ln -s ../managex/storage/app/public storage
```

**Si pas d'accès SSH :** Créez un fichier PHP temporaire pour créer le lien :

Créez `~/public_html/create-storage-link.php` :
```php
<?php
$target = $_SERVER['DOCUMENT_ROOT'] . '/../managex/storage/app/public';
$link = $_SERVER['DOCUMENT_ROOT'] . '/storage';

if (file_exists($link)) {
    echo "Le lien existe déjà.";
} else {
    if (symlink($target, $link)) {
        echo "Lien symbolique créé avec succès !";
    } else {
        echo "Erreur lors de la création du lien.";
    }
}
```

Visitez `https://votre-domaine.com/create-storage-link.php`, puis **supprimez ce fichier**.

---

## Étape 5 : Configurer le fichier .env

Éditez `/home/votrecompte/managex/.env` :

```env
APP_NAME=ManageX
APP_ENV=production
APP_KEY=base64:VOTRE_CLE_EXISTANTE
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Base de données
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=votrecompte_managex
DB_USERNAME=votrecompte_managex
DB_PASSWORD=votre_mot_de_passe

# Cache et Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Mail (optionnel - configurez selon votre fournisseur)
MAIL_MAILER=smtp
MAIL_HOST=mail.votre-domaine.com
MAIL_PORT=465
MAIL_USERNAME=noreply@votre-domaine.com
MAIL_PASSWORD=mot_de_passe_email
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="${APP_NAME}"

# Filesystem
FILESYSTEM_DISK=public
```

---

## Étape 6 : Configurer les permissions

Via **Terminal** cPanel ou SSH :

```bash
cd ~/managex

# Permissions des dossiers
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Propriétaire (normalement automatique sur shared hosting)
# chown -R $USER:$USER storage bootstrap/cache
```

**Via File Manager :** Clic droit sur `storage/` et `bootstrap/cache/` → Change Permissions → 755

---

## Étape 7 : Exécuter les commandes Laravel

### Option A : Via Terminal cPanel

```bash
cd ~/managex

# Générer la clé (si pas déjà fait)
php artisan key:generate

# Exécuter les migrations
php artisan migrate --force

# Créer le cache de config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Seeder (optionnel, pour données de test)
php artisan db:seed --force
```

### Option B : Via fichier PHP (si pas de SSH)

Créez `~/public_html/setup.php` :

```php
<?php
// SUPPRIMER CE FICHIER APRÈS UTILISATION !

// Sécurité basique
$secret = 'CHANGEZ_CE_TOKEN_SECRET';
if (!isset($_GET['token']) || $_GET['token'] !== $secret) {
    die('Accès refusé');
}

chdir(__DIR__ . '/../managex');

echo "<pre>";

// Migrations
echo "=== Migrations ===\n";
echo shell_exec('php artisan migrate --force 2>&1');

// Cache
echo "\n=== Cache Config ===\n";
echo shell_exec('php artisan config:cache 2>&1');

echo "\n=== Cache Routes ===\n";
echo shell_exec('php artisan route:cache 2>&1');

echo "\n=== Cache Views ===\n";
echo shell_exec('php artisan view:cache 2>&1');

echo "\n=== Storage Link ===\n";
echo shell_exec('php artisan storage:link 2>&1');

echo "\n\nTerminé ! SUPPRIMEZ CE FICHIER IMMÉDIATEMENT.";
echo "</pre>";
```

Visitez : `https://votre-domaine.com/setup.php?token=CHANGEZ_CE_TOKEN_SECRET`

**⚠️ SUPPRIMEZ `setup.php` après utilisation !**

---

## Étape 8 : Configurer le Cron Job (optionnel)

Pour les tâches planifiées Laravel, ajoutez un cron dans cPanel :

1. Allez dans **Cron Jobs**
2. Ajoutez une nouvelle tâche :

```
* * * * * cd /home/votrecompte/managex && php artisan schedule:run >> /dev/null 2>&1
```

---

## Étape 9 : Configurer HTTPS (SSL)

1. Dans cPanel, allez dans **SSL/TLS Status**
2. Activez **AutoSSL** pour votre domaine
3. Forcez HTTPS dans `.htaccess` (dans `public_html/`) :

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Forcer HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Redirection vers index.php
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## Dépannage

### Erreur 500 (Internal Server Error)

1. Vérifiez les logs : `~/managex/storage/logs/laravel.log`
2. Vérifiez les permissions de `storage/` et `bootstrap/cache/`
3. Assurez-vous que `.env` est bien configuré
4. Exécutez `php artisan config:clear`

### Page blanche

1. Activez temporairement `APP_DEBUG=true` dans `.env`
2. Vérifiez la version PHP (8.1+ requis)
3. Vérifiez que toutes les extensions PHP sont activées :
   - OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo

### Erreur de base de données

1. Vérifiez les identifiants dans `.env`
2. Testez la connexion via phpMyAdmin
3. Assurez-vous que l'utilisateur a les droits sur la base

### Assets non chargés (CSS/JS)

1. Vérifiez que le dossier `build/` est dans `public_html/`
2. Vérifiez l'URL dans le navigateur (pas de mixed content HTTP/HTTPS)
3. Videz le cache du navigateur

### Avatars/Images non affichés

1. Vérifiez le lien symbolique `storage/` dans `public_html/`
2. Vérifiez les permissions de `storage/app/public/`

---

## Checklist finale

- [ ] Base de données créée et configurée
- [ ] Fichiers uploadés dans la bonne structure
- [ ] `index.php` modifié avec les bons chemins
- [ ] `.env` configuré pour la production
- [ ] Permissions correctes sur `storage/` et `bootstrap/cache/`
- [ ] Migrations exécutées
- [ ] Lien symbolique storage créé
- [ ] HTTPS activé
- [ ] Cron job configuré (si nécessaire)
- [ ] Fichiers temporaires supprimés (`setup.php`, `create-storage-link.php`)
- [ ] `APP_DEBUG=false` dans `.env`

---

## Support

Si vous rencontrez des problèmes :
1. Consultez les logs Laravel : `storage/logs/laravel.log`
2. Contactez le support Namecheap pour les problèmes d'hébergement
3. Vérifiez la documentation Laravel : https://laravel.com/docs

---

*Guide créé pour ManageX - Système de Gestion RH*
